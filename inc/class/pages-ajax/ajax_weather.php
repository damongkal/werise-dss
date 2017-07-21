<?php
class ajax_weather extends ajax_base 
{
    private $db;

    private $station;
    private $year;
    private $wvar;
    private $wtype;
    private $pct_enabled;
    private $chart_data;
    private $grid_data;
    private $advisory = false;
    private $acknowledge = '';

    protected function actionDefault() {        
        
        if (_ADM_ENV==='PROD' && dss_auth::getUsername()==='')
        {
            $this->json_ret = 'unauthorized access';
            return;
        }
        
        $this->db = Database_MySQL::getInstance();
        $this->init();
        $this->prepareData();
        $this->getAdvisory();
        return $this->getChartData();
    }

    private function init()
    {
        $country = 'ID';
        if (isset($_GET['country']) && in_array($_GET['country'],array('PH','ID','LA','TH')))
        {
            $country = $_GET['country'];
        }

        $station = 0;
        if (isset($_GET['station']))
        {
            $station = intval($_GET['station']);
        }

        $wstation = new weather_stations;
        $this->station = $wstation->getStation($country, $station);

        $this->year = 0;
        if (isset($_GET['year']))
        {
            $this->year = intval($_GET['year']);
        }

        $this->wvar = 0;
        if (isset($_GET['wvar']))
        {
            $this->wvar = intval($_GET['wvar']);
        }

        $this->wtype = 'r';
        if (isset($_GET['wtype']) && in_array($_GET['wtype'],array('r','f')))
        {
            $this->wtype = $_GET['wtype'];
        }

        $this->pct_enabled = true;
        if (isset($_GET['pct']) && intval($_GET['pct'])===0)
        {
            $this->pct_enabled = false;
        }
        // override geo latitude value to affect start date
        if (isset($_GET['override_geolat']))
        {
            $this->station->geo_lat = intval($_GET['override_geolat']);
        }        
        
        $this->addAccessLog();        
    }

    private function prepareData()
    {        
        date_default_timezone_set('UTC');

        // prepare date series
        $x_axis = dss_utils::getDecadalSeries($this->year, $this->station->geo_lat);
        $xcnt = count($x_axis);

        // wet season period
        list($ws_from, $ws_to) = dss_utils::getWetSeasonPeriod($this->station->geo_lat,$this->year);

        // decadal data
        $weather = new weather_data;
        $data = $weather->getDecadal($this->station, $this->year, $this->wtype, $this->wvar);
        // check if there is actual data value
        if (!$data)
        {
            $this->chart_data = false;
            return;
        }

        if ($this->wvar===0)
        {
            // get onset of rain
            $rain_buffer = 0;
            $rain_onset = false;
            foreach($data as $key => $val)
            {
                $rain_buffer += $val;
                if ($rain_buffer >= 30)
                {
                    $rdate = DateTime::createFromFormat('U', $x_axis[$key] / 1000);
                    $rain_onset = $rdate->format('M-d');
                    break;
                }
            }
            if($rain_onset)
            {
                $this->advisory['rain_onset'] = $rain_onset;
            }
        }

        // get percentiles
        $all_years = $this->getDecadalAll($this->station,$this->wvar);
        $percentile = $this->getStationPercentile($all_years, $this->wvar);

        // min-max
        list($new_min,$new_max) = $this->getMinMax($percentile[2],$this->wvar);

        // for temperature, we make min and max temperatures
        if ($this->wvar==1)
        {
            $temp_max_id = 2;
            // decadal data
            $data2 = $weather->getDecadal($this->station, $this->year, $this->wtype, $temp_max_id);

            // get percentiles
            $all_years2 = $this->getDecadalAll($this->station,$temp_max_id);
            $percentile2 = $this->getStationPercentile($all_years2, $temp_max_id);

            // min-max
            list($new_min2,$new_max2) = $this->getMinMax($percentile2[2], $temp_max_id);
            if ($new_max2>$new_max)
            {
                $new_max = $new_max2;
            }
        }

        // chart properties
        $graphtype = 'scatter';
        if ($this->wvar == 0)
        {
            $graphtype = 'column';
        }

        // put to chart data return
        $this->chart_data = array();
        $this->chart_data['series'][] = array(
            'data' => $this->addDates($data, $x_axis),
            'name' => _t($this->getVarName($this->wvar)),
            'type' => $graphtype
        );


        $new_percentile = $this->getPercentileThreshold($this->wvar, $data, $percentile);        
        if ($this->wvar === 0)
        {
            // $this->debug->addLog($data,true);            
            // piggyback advisory for flood/drought
            $this->getAdvisoryThreshold($x_axis, $new_percentile);
            $this->addPercentileChart($new_percentile, $x_axis, _t('Drought'), _t('Flood'));
        } else
        {
            $this->addPercentileChart($new_percentile, $x_axis);
        }

        if ($this->wvar==1)
        {
            $this->chart_data['series'][] = array(
                'data' => $this->addDates($data2, $x_axis),
                'name' => $this->getVarName($temp_max_id),
                'type' => $graphtype
            );
            $new_percentile2 = $this->getPercentileThreshold($this->wvar, $data2, $percentile2);        
            $this->addPercentileChart($new_percentile2,$x_axis);
        }

        $this->chart_data['wetseason'] = array(
            'from' => $ws_from->format('U') * 1000,
            'to' => $ws_to->format('U') * 1000 );
        $this->chart_data['chart_period'] = date('M Y',$x_axis[0] / 1000) . " - " . date('M Y',$x_axis[$xcnt-1] / 1000);
        $this->chart_data['min'] = $new_min;
        $this->chart_data['max'] = $new_max;

        // prepare tabular data
        $grid = false;
        if (_opt(sysoptions::_OPT_SHOW_DATAGRID))
        {
            foreach($x_axis as $key => $rec)
            {
                $grid[] = array(
                    date('M-d',$rec/1000),
                    ($key%3)+1,
                    isset($data[$key]) ? $data[$key] : 'No Data',
                    isset($percentile[0][$key]) ? $percentile[0][$key] : 'No Data',
                    isset($percentile[1][$key]) ? $percentile[1][$key] : 'No Data',
                    isset($percentile[2][$key]) ? $percentile[2][$key] : 'No Data'
                );
            }
        }
        $this->grid_data = $grid;

        // data acknowledgement
        $ack = new werise_weather_acknowledge;
        $this->acknowledge = $ack->getRecords($this->station, $this->year, $this->wtype);
    }
    
    private function getDecadalAll($station,$wvar)
    {
        $all_years_filter = array(
            'country'=>$station->country_code,
            'station'=>$station->station_id,
            'wtype'=> werise_weather_properties::_REALTIME);        
        $all_years = weather_data::getDatasetDecadal($all_years_filter, $wvar);
        if (count($all_years)===0)
        {
            $all_years_filter['wtype'] = werise_weather_properties::_FORECAST;
            $all_years = weather_data::getDatasetDecadal($all_years_filter, $wvar);
        }        
        return $all_years;
    }

    /**
     * check data against lower and upper limits
     * @param type $wvar
     * @param type $data
     * @param type $percentile
     * @return type
     */
    private function getPercentileThreshold($wvar, $data, $percentile) {
        $new_percentile = $percentile;
        foreach ($percentile[0] as $key => $lowlimit) {
            if (isset($data[$key]))
            {
                $mean = 99;
                if ($wvar==0)
                {
                    $mean = $percentile[1][$key];
                }
                // check lower limit
                $val = $data[$key];
                if ($mean < 15 || $val > $lowlimit) {
                    $new_percentile[0][$key] = null;
                }

                // check upper limit
                $hilimit = $percentile[2][$key];
                if ($val < 30 || $val < $hilimit) {
                    $new_percentile[2][$key] = null;
                }
            } else
            {
                $new_percentile[0][$key] = null;
                $new_percentile[2][$key] = null;
            }
        }

        return $new_percentile;
    }

    private function addPercentileChart($percentile, $x_axis, $title1 = '', $title2 = '', $title3 = '') {
        if ($title1==='')
        {
            $title1 = _t('Lower threshold'); // probability at 80%
        }
        $this->chart_data['series'][] = array(
            'data' => $this->addDates($percentile[0], $x_axis),
            'name' => $title1,
            'color' => '#ff0000',
            'type' => 'scatter'
        );

        // percentile data in chart is disabled
        if ($this->pct_enabled) {            
            if ($title3==='')
            {
                $title3 = _t('Mean'); // probability at 50%
            }
            $this->chart_data['series'][] = array(
                'data' => $this->addDates($percentile[1], $x_axis),
                'name' => $title3,
                'color' => '#00ff00'
            );
        }

        if ($title2==='')
        {
            $title2 = _t('Upper threshold'); // probability at 20%
        }
        $this->chart_data['series'][] = array(
            'data' => $this->addDates($percentile[2], $x_axis),
            'name' => $title2,
            'color' => '#0000A0',
            'type' => 'scatter'
        );
    }

    private function getAdvisory()
    {
        $is_get_advisory = 1;
        if (isset($_GET['advisory']))
        {
            $is_get_advisory = intval($_GET['advisory']);
        }
        if (!$is_get_advisory || $this->wvar!=0)
        {
            $this->advisory['adv_rain'] = array('advisory_code'=>0);
            return;
        }
        $adv = new advisory_rainfall($this->station, $this->wtype);
        $period = $adv->getWSPeriod($this->station,$this->year);
        $this->advisory['adv_rain'] = $adv->getAdvisory($period[0], $period[1]);
    }

    private function getAdvisoryThreshold($x_axis,$percentile)
    {
        $drought = array();
        foreach($percentile[0] as $key => $val)
        {
            if (!is_null($val))
            {
                $date = DateTime::createFromFormat('U', $x_axis[$key] / 1000);
                $drought[] = $date->format('U') * 1000;
            }
        }
        $this->advisory['dry_dates'] = $drought;

        $flood = array();
        foreach($percentile[2] as $key => $val)
        {
            if (!is_null($val))
            {
                $date = DateTime::createFromFormat('U', $x_axis[$key] / 1000);
                $flood[] = $date->format('U') * 1000;
            }
        }
        $this->advisory['wet_dates'] = $flood;
    }

    private function getChartData() {
        return array(
            'chart' => $this->chart_data,
            //'grid' => $this->grid_data,
            'grid' => false,
            'advisory' => $this->advisory,
            'acknowledge' => $this->acknowledge
        );
    }

    private function getStationPercentile($raw, $wvar)
    {
        $pctile = new werise_weather_percentile;
        $pctile->getStationPercentile2($raw, $this->station, $wvar);
        return array($pctile->pctile_20,$pctile->pctile_50,$pctile->pctile_80);
    }


    private function addDates($data, $x_axis)
    {
        $ret = array();
        foreach ($data as $key => $rec)
        {
            $ret[] = array($x_axis[$key],$rec);
        }
        return $ret;
    }

    private function getMinMax($percentile,$wvar)
    {
        // get min/max from database
        $weather = new weather_data;
        list($new_min,$new_max) = $weather->getDecadalMinMax($this->station, $this->wtype, $wvar);

        // get min/max from percentiles
        foreach($percentile as $p)
        {
            if ($p < $new_min)
            {
                $new_min = $p;
            }
            if ($p > $new_max)
            {
                $new_max = $p;
            }
        }
        // normalize
        switch ($wvar)
        {
            case 0;
                $interval = 25;
                break;
            case 1;
            case 2;
                $interval = 2;
                break;
            case 3;
                $interval = 5;
                break;
            case 4;
                $interval = 0.25;
                break;
            case 5;
                $interval = 1;
                break;
            case 6;
                $interval = 1;
                break;
            default:
                $interval = 50;
        }
        for($i=0; $i<=50; $i++)
        {
            if($new_min<($i*$interval))
            {
                $new_min = ($i*$interval)-$interval;
                break;
            }
        }
        for($i=0; $i<=50; $i++)
        {
            if($new_max<($i*$interval))
            {
                $new_max = $i*$interval;
                break;
            }
        }
        if ($new_min<0)
        {
            $new_min = 0;
        }
        return array($new_min,$new_max);
    }

    private function getVarName($wvar)
    {
        switch ($wvar)
        {
            case 0:
                return 'Rainfall';
            case 1:
                return 'Minimum Temperature';
            case 2:
                return 'Maximum Temperature';
            case 3:
                return 'Solar Radiation';
            case 4:
                return 'Early morning vapor pressure';
            case 5:
                return 'Wind Speed';
            case 6:
                return 'Sunshine Duration';
        }
    }
    
    private function addAccesslog()
    {
        $userid = dss_auth::getUserId();
        $sql = "
            INSERT INTO `weather_access_log` (
                `userid`, `country_code`, `station_id`,
                `year`, `wtype`, `create_date` )
            VALUES (
                %d, '%s', %d,
                %d, '%s', NOW()
            )";
        $sql2 = sprintf($sql,
            $userid,
            $this->station->country_code,
            $this->station->station_id,
            $this->year,
            $this->db->escape($this->wtype));
        $this->db->query($sql2);
    }
}
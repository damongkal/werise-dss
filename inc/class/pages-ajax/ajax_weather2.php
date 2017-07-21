<?php
class ajax_weather2 extends ajax_base 
{
    /**
     * external arguments
     * @var type 
     */
    private $station;
    private $year;
    private $wvar;
    private $wtype;
    private $pct_enabled;
    private $start_month;
    /**
     * chart data
     * @var type 
     */
    private $chart_start;
    private $chart_end;
    private $chart_data;
    /**
     * advisory
     * @var type 
     */
    private $advisory = false;

    protected function actionDefault() {        
        
        if (_ADM_ENV==='PROD' && dss_auth::getUsername()==='')
        {
            $this->json_ret = 'unauthorized access';
            return;
        }
        
        $this->db = Database_MySQL::getInstance();
        $this->init();
        $this->prepareData();
        return $this->getChartData();
    }

    private function init()
    {
        $country = $this->getArg('country','ID');
        if (!in_array($country,array('PH','ID','LA','TH')))
        {
            $country = 'ID';
        }
        $station = intval($this->getArg('station',0));
        $wstation = new weather_stations;
        $this->station = $wstation->getStation($country, $station);
        $this->wvar = intval($this->getArg('wvar',0));
        $this->wtype = $this->getArg('wtype', werise_weather_properties::_FORECAST);
        if (!in_array($this->wtype,array(werise_weather_properties::_REALTIME,werise_weather_properties::_FORECAST)))
        {
            $this->wtype = werise_weather_properties::_FORECAST;
        }
        // display percentiles?
        $this->pct_enabled = (bool)($this->getArg('pct',true));
        // chart period
        $tmpd1 = explode('-',$this->getArg('start',''));
        $this->year = $tmpd1[1];
        $this->start_month = $tmpd1[0];
        list($this->chart_start,$this->chart_end) = dss_utils::getCropSeason($this->start_month,$this->year);
    }

    private function prepareData()
    {        
        // prepare date series
        $x_axis = dss_utils::getDecadalSeries2($this->start_month,$this->year);
        $xcnt = count($x_axis);
        // decadal data
        list($decadal_template,$data) = $this->getDecadal($this->wvar);
        // check if there is actual data value
        if (!$data)
        {
            $this->chart_data = false;
            return;
        }
        // get percentiles
        $all_years = $this->getDecadalAll($this->station,$this->wvar);
        $percentile = $this->getStationPercentile($all_years, $this->wvar,$decadal_template);

        // min-max
        list($new_min,$new_max) = $this->getMinMax($percentile[2],$this->wvar);

        // for temperature, we make min and max temperatures
        if ($this->wvar==werise_weather_properties::_WVAR_MINTEMP)
        {
            $temp_max_id = werise_weather_properties::_WVAR_MAXTEMP;
            // decadal data
            list($decadal_template2,$data2) = $this->getDecadal($temp_max_id);

            // get percentiles
            $all_years2 = $this->getDecadalAll($this->station,$temp_max_id);
            $percentile2 = $this->getStationPercentile($all_years2, $temp_max_id, $decadal_template2);

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
            'name' => werise_weather_properties::getVarName($this->wvar),
            'type' => $graphtype
        );


        $new_percentile = $this->getPercentileThreshold($this->wvar, $data, $percentile);        
        if ($this->wvar === 0)
        {
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
        $this->chart_data['chart_period'] = date('M Y',$x_axis[0] / 1000) . " - " . date('M Y',$x_axis[$xcnt-1] / 1000);
        $this->chart_data['min'] = $new_min;
        $this->chart_data['max'] = $new_max;
    }
    
    private function getDecadal($wvar)
    {
        $weather = new weather_data;
        $opts = array('start_date'=>$this->chart_start,'end_date'=>$this->chart_end,'output_raw'=>true);
        $rs = $weather->getDecadal($this->station, $this->year, $this->wtype, $wvar, $opts);                
        // compile
        $decadals = false;
        $data = false;        
        if ($rs)
        {
            foreach ($rs as $rec)
            {
                $data[] = $weather->cleanVar($this->wvar,$rec->wvar);
                $decadals[] = $rec->decadal;
            }
        }        
        return array($decadals,$data);
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

    private function getChartData() {
        return array(
            'chart' => $this->chart_data,
            'advisory' => $this->advisory
        );
    }

    private function getStationPercentile($raw, $wvar, $decadal_template)
    {
        $pctile = new werise_weather_percentile;
        $opts = array('decadal_template'=>$decadal_template);
        $pctile->getStationPercentile2($raw, $this->station, $wvar, 'wvar', $opts);
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
}
<?php
include_once('bootstrap.php');

class weather_chart
{
    private $country;
    private $station;
    private $year;
    private $wvar;
    private $wtype;
    private $chart_data;
    private $grid_data;
    private $advisory = false;

    public function init()
    {
        $this->db = Database_MySQL::getInstance();

        $this->country = 'ID';
        if (isset($_GET['country']) && in_array($_GET['country'],array('PH','ID','LA','TH')))
        {
            $this->country = $_GET['country'];
        }

        $this->station = 0;
        if (isset($_GET['station']))
        {
            $this->station = intval($_GET['station']);
        }

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

        $this->prepareData();
        if ($this->wvar==0)
        {
            $this->getAdvisory();
        }
    }

    private function prepareData()
    {
        date_default_timezone_set('UTC');
        $wstation = new weather_stations;
        $geolat = $wstation->getStationGeoLat($this->country,$this->station);

        // prepare date series
        $x_axis = dss_utils::getDecadalSeries($this->year, $geolat);
        $xcnt = count($x_axis);

        // wet season period
        list($ws_from, $ws_to) = dss_utils::getWetSeasonPeriod($geolat,$this->year);

        // decadal data
        $weather = new weather_data;
        $data = $weather->getDecadal($this->country, $this->station, $this->year, $this->wtype, $this->wvar, false, $geolat);

        // get percentiles
        $all_years = $weather->getDecadalAll($this->country, $this->station, 'r', $this->wvar);
        $percentile = $this->getStationPercentile($all_years, $this->wvar, $geolat);
        
        // min-max
        list($new_min,$new_max) = $this->getMinMax($all_years, $percentile[2], $this->wvar);        

        // for temperature, we make min and max temperatures
        if ($this->wvar==1)
        {
            $temp_max_id = 2;
            // decadal data            
            $data2 = $weather->getDecadal($this->country, $this->station, $this->year, $this->wtype, $temp_max_id, false, $geolat);
            
            // get percentiles
            $all_years2 = $weather->getDecadalAll($this->country, $this->station, 'r', $temp_max_id);
            $percentile2 = $this->getStationPercentile($all_years2, $temp_max_id, $geolat);            
            
            // min-max
            list($new_min2,$new_max2) = $this->getMinMax($all_years2, $percentile2[2], 2);                    
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
            'name' => $this->getVarName($this->wvar),
            'type' => $graphtype
        );
        $this->chart_data['series'][] = array(
            'data' => $this->addDates($percentile[0], $x_axis),
            'name' => 'Probability at 80%',
            'color' => '#ff0000'
        );
        $this->chart_data['series'][] = array(
            'data' => $this->addDates($percentile[1], $x_axis),
            'name' => 'Probability at 50%',
            'color' => '#00ff00'
        );
        $this->chart_data['series'][] = array(
            'data' => $this->addDates($percentile[2], $x_axis),
            'name' => 'Probability at 20%',
            'color' => '#0000A0'
        );
        if ($this->wvar==1)
        {
            $this->chart_data['series'][] = array(
                'data' => $this->addDates($data2, $x_axis),
                'name' => $this->getVarName($temp_max_id),
                'type' => $graphtype
            );
            $this->chart_data['series'][] = array(
                'data' => $this->addDates($percentile2[0], $x_axis),
                'name' => 'Probability at 80%',
                'color' => '#ff0000'
            );
            $this->chart_data['series'][] = array(
                'data' => $this->addDates($percentile2[1], $x_axis),
                'name' => 'Probability at 50%',
                'color' => '#00ff00'
            );
            $this->chart_data['series'][] = array(
                'data' => $this->addDates($percentile2[2], $x_axis),
                'name' => 'Probability at 20%',
                'color' => '#0000A0'
            );            
        }
        
        $this->chart_data['wetseason'] = array(
            'from' => $ws_from->format('U') * 1000,
            'to' => $ws_to->format('U') * 1000 );
        $this->chart_data['chart_period'] = date('M Y',$x_axis[0] / 1000) . " - " . date('M Y',$x_axis[$xcnt-1] / 1000);
        $this->chart_data['min'] = $new_min;
        $this->chart_data['max'] = $new_max;

        // prepare tabular data
        $grid = false;
        if (_OPT_SHOW_DATAGRID)
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

        // piggyback fertilizer advisory
        $this->getAdvisoryFert($data, $x_axis);
    }

    private function getAdvisory()
    {
        $adv = new advisory_rainfall;
        $rainfall_adv = $adv->getAdvisory($this->country,$this->station,$this->year);
        $this->advisory['f_rain'] = $rainfall_adv['advisory'];
        $this->advisory['f_rain_code'] = $rainfall_adv['advisory_code'];
    }

    private function getAdvisoryFert($data,$series)
    {
        $this->advisory['fert_apply'] = false;

        $fapply = array();
        $last_key = -2;
        foreach($data as $key => $rec)
        {
            if($rec>=30 && $rec<=100)
            {
                $to = DateTime::createFromFormat('U', $series[$key]/1000);
                $to->modify('+9 day');
                if (($key-1) == $last_key)
                {
                    $cnt = count($fapply);
                    $fapply[$cnt-1]['to'] = array($to->format('U') * 1000,$rec);
                } else
                {
                    $fapply[] = array(
                        'from' => array($series[$key],$rec),
                        'to' => array( $to->format('U') * 1000 ,$rec));
                }
                $last_key = $key;
            }
        }
        //echo '<pre>';
        //print_r($fapply);
        //die();
        $this->advisory['fert_apply'] = $fapply;
    }


    public function getChartData()
    {
        return json_encode(
                array(
                    'chart' => $this->chart_data,
                    'grid' => $this->grid_data,
                    'advisory' => $this->advisory
                ));
    }

    private function getStationPercentile($raw, $wvar, $geolat)
    {
        // try to read from cache
        $cache = new cache('ptile-'.$this->country.'-'.$this->station.'-r-'.$wvar);
        $cache_data = $cache->read();
        if ($cache_data)
        {
            return $cache_data;
        }

        $percentiles = array(false,false,false);

        if ($raw)
        {
            $weather = new weather_data;
            $decadals = false;
            foreach ($raw as $rec)
            {
                $decadal = $rec->decadal;
                $decadals[$decadal][] = $weather->cleanVar($wvar, $rec->wvar);
            }

            if ($geolat>=0)
            {
                foreach ($decadals as $key => $rec)
                {
                    $percentiles[0][] = dss_utils::percentile($rec, 20);
                    $percentiles[1][] = dss_utils::percentile($rec, 50);
                    $percentiles[2][] = dss_utils::percentile($rec, 80);
                }
            } else
            {
                foreach ($decadals as $key => $rec)
                {
                    $decadal = $key+0;
                    if ($decadal>70)
                    {
                        $percentiles[0][] = dss_utils::percentile($rec, 20);
                        $percentiles[1][] = dss_utils::percentile($rec, 50);
                        $percentiles[2][] = dss_utils::percentile($rec, 80);
                    }
                }

                foreach ($decadals as $key => $rec)
                {
                    $decadal = $key+0;
                    if ($decadal<70)
                    {
                        $percentiles[0][] = dss_utils::percentile($rec, 20);
                        $percentiles[1][] = dss_utils::percentile($rec, 50);
                        $percentiles[2][] = dss_utils::percentile($rec, 80);
                    }
                }
            }
        }

        // save to cache
        $cache->write($percentiles);

        return $percentiles;
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

    private function getMinMax($all_years, $percentile, $wvar)
    {
        $weather = new weather_data;
        $new_min = 0;
        $new_max = 0;
        foreach($all_years as $p)
        {
            $val = $weather->cleanVar($wvar,$p->wvar);
            if ($val < $new_min)
            {
                $new_min = $val;
            }
            if ($val > $new_max)
            {
                $new_max = $val;
            }
        }
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

        switch ($wvar)
        {
            case 0;
                $interval = 25;
                $limit = 400;
                break;
            case 1;
            case 2;
                $interval = 2;
                $limit = 45;
                break;
            case 3;
                $interval = 5;
                $limit = 35;
                break;
            case 4;
                $interval = 1;
                $limit = 6;
                break;
            case 5;
                $interval = 1;
                $limit = 10;
                break;
        }
        for($i=0; $i<=50; $i++)
        {
            if($new_max<($i*$interval))
            {
                $new_max = $i*$interval;
                break;
            }
        }
        if ($new_max>$limit)
        {
            $new_max = $limit;
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
        }
    }
}

$cls = new weather_chart;
$cls->init();
echo $cls->getChartData();
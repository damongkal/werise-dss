<?php
include_once('bootstrap.php');

class oryza_chart
{
    private $country;
    private $station;
    private $compare;
    private $geolat;

    private $chart_data;
    private $grid_data;
    private $raw_data;
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

        $this->compare = array();

        // set  1
        if (isset($_GET['year']))
        {
            $year = intval($_GET['year']);
            if ($year > 0)
            {
                $wtype = '';
                if ( isset($_GET['wtype']) && in_array($_GET['wtype'],array('r','f')) )
                {
                    $wtype = $_GET['wtype'];
                }
                $variety = '';
                if (isset($_GET['variety']))
                {
                    $variety = $_GET['variety'];
                }
                $fertil = 0;
                if (isset($_GET['fert']))
                {
                    $fertil =intval($_GET['fert']);
                }
                $this->compare[] = array(
                    'year' => $year,
                    'wtype' => $wtype,
                    'variety' => $variety,
                    'fertil' => $fertil
                );
            }
        }

        // set 2
        if (isset($_GET['year2']))
        {
            $year = intval($_GET['year2']);
            if ($year > 0)
            {
                $wtype = '';
                if ( isset($_GET['wtype2']) && in_array($_GET['wtype2'],array('r','f')) )
                {
                    $wtype = $_GET['wtype2'];
                }
                $variety = '';
                if (isset($_GET['variety2']))
                {
                    $variety = $_GET['variety2'];
                }
                $fertil = 0;
                if (isset($_GET['fert2']))
                {
                    $fertil = intval($_GET['fert2']);
                }
                $this->compare[] = array(
                    'year' => $year,
                    'wtype' => $wtype,
                    'variety' => $variety,
                    'fertil' => $fertil
                );
            }
        }

        // set 3
        if (isset($_GET['year3']))
        {
            $year = intval($_GET['year3']);
            if ($year > 0)
            {
                $wtype = '';
                if ( isset($_GET['wtype3']) && in_array($_GET['wtype3'],array('r','f')) )
                {
                    $wtype = $_GET['wtype3'];
                }
                $variety = '';
                if (isset($_GET['variety3']))
                {
                    $variety = $_GET['variety3'];
                }
                $fertil = 0;
                if (isset($_GET['fert3']))
                {
                    $fertil = intval($_GET['fert3']);
                }
                $this->compare[] = array(
                    'year' => $year,
                    'wtype' => $wtype,
                    'variety' => $variety,
                    'fertil' => $fertil
                );
            }
        }

        // set 4
        if (isset($_GET['year4']))
        {
            $year = intval($_GET['year4']);
            if ($year > 0)
            {
                $wtype = '';
                if ( isset($_GET['wtype4']) && in_array($_GET['wtype4'],array('r','f')) )
                {
                    $wtype = $_GET['wtype4'];
                }
                $variety = '';
                if (isset($_GET['variety4']))
                {
                    $variety = $_GET['variety4'];
                }
                $fertil = 0;
                if (isset($_GET['fert4']))
                {
                    $fertil = intval($_GET['fert4']);
                }
                $this->compare[] = array(
                    'year' => $year,
                    'wtype' => $wtype,
                    'variety' => $variety,
                    'fertil' => $fertil
                );
            }
        }

        //echo '<pre>';
        //print_r($this->compare);

        $this->prepareData();
        $this->getAdvisory();
    }

    private function prepareData()
    {
        date_default_timezone_set('UTC');
        $wstation = new weather_stations;
        $this->geolat = $wstation->getStationGeoLat($this->country,$this->station);
        $fakeyear = 2001; // fake year so that we can compare

        // wet season period
        list($ws_from, $ws_to) = dss_utils::getWetSeasonPeriod($this->geolat,$fakeyear);

        // oryza2000 data
        $oryza = new oryza_data;
        $this->chart_data = false;

        $chart = false;
        $grid = false;
        foreach ($this->compare as $key => $dataset)
        {
            $data = $oryza->getYield(
                $this->country,
                $this->station,
                $dataset['year'],
                $dataset['wtype'],
                $dataset['fertil'],
                $dataset['variety'], $this->geolat );

            $fakeyear = 2001; // fake year so that we can compare            
            $tmp = false;
            $tmp2 = false;
            foreach($data as $key2 => $rec)
            {
                $setdate = explode('-',$rec->observe_date);
                $year = $setdate[0]; // real year
                if ($key2==0)
                {
                    $first_year = $year;
                }
                if ($first_year != $year)
                {
                    $fakeyear = 2002;
                }

                // chart
                $tmpdate = mktime(0,0,0,$setdate[1],$setdate[2],$fakeyear);
                $setdate_utc = $tmpdate * 1000;
                $tmp[] = array($setdate_utc, $rec->yield+0);
                // grid
                $setdate_grid = date('M-d',$tmpdate);
                $grid[$setdate_grid][] = $rec->yield+0;
                // raw data
                $tmp2[] = $rec;
            }

            // add dummy records if season is incomplete
            if (count($data)<24)
            {
                for($month=1; $month<=6;$month++)
                {
                    // chart
                    $tmpdate = mktime(0,0,0,$month,1,$fakeyear+1);
                    $setdate_utc = $tmpdate * 1000;
                    $tmp[] = array($setdate_utc, null);
                    // grid
                    $setdate_grid = date('M-d',$tmpdate);
                    $grid[$setdate_grid][] = null;

                    // chart
                    $tmpdate = mktime(0,0,0,$month,15,$fakeyear+1);
                    $setdate_utc = $tmpdate * 1000;
                    $tmp[] = array($setdate_utc, null);
                    // grid
                    $setdate_grid = date('M-d',$tmpdate);
                    $grid[$setdate_grid][] = null;
                }
                $data[] = array();
            }

            if ($tmp)
            {
                $chart[] = array(
                    'name' => $this->getDatasetName($dataset,$key),
                    // 'name' => "Set " . ($key+1),
                    'data' => $tmp
                );
                $this->raw_data[] = $tmp2;
            }
        }       
        
        $this->chart_data = array(
            'series' => $chart,
            'wetseason' => array(
                'from' => $ws_from->format('U') * 1000,
                'to' => $ws_to->format('U') * 1000 )
        );

        // tabular data
        $grid2 = false;
        if (_OPT_SHOW_DATAGRID)
        {
            foreach($grid as $key => $rec)
            {
                $grid2[] = array_merge(array($key),$rec);
            }
        }
        $this->grid_data = $grid2;

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

    private function getVarietyFile($variety)
    {
        switch ($variety)
        {
            case 'IR64':
                return 'IR64.J96';
                break;
            case 'IR72':
                return 'IR72.DAT';
                break;
        }
    }

    private function getDatasetName($dataset,$key)
    {
        $wtype_name = 'Real-Time';
        if ($dataset['wtype'] == 'f')
        {
            $wtype_name = 'Forecast';
        }

        $fert_name = 'No Fert.';
        if ($dataset['fertil'] == 1)
        {
            $fert_name = 'General Recommendation';
        }
        $variety = explode('.',$dataset['variety']);


        $name = "Set " . ($key+1) . ': ' .
                $dataset['year'] . ' ' .
                $wtype_name . ' , ' .
                $variety[0] . ' , ' .
                $fert_name;
        return $name;
    }

    private function getAdvisory()
    {
        $dataset_index = 0; 
        
        // rainfall
        $adv = new advisory_rainfall;
        $rainfall_adv = $adv->getAdvisory($this->country,$this->station,$this->compare[$dataset_index]['year']);
        $this->advisory['f_rain'] = $rainfall_adv['advisory'];
        $this->advisory['f_rain_code'] = $rainfall_adv['advisory_code'];

        $this->getAdvisoryFert($dataset_index);
        $this->getAdvisoryYld($dataset_index);
    }

    /**
     * @todo documentation
     */
    private function getAdvisoryYld($dataset_index)
    {
        // get 80th percentile of yield
        $yld = array();
        foreach($this->raw_data[$dataset_index] as $rec)
        {
            $yld[] = $rec->yield+0;
        }
        $p80 = dss_utils::percentile($yld,80);
        
        // get last day of emergence
        $oryza_class = new oryza_data;
        $hi = array();
        $hi_sorter = array();
        foreach($this->raw_data[$dataset_index] as $key => $rec)
        {
            $yld = $rec->yield + 0;
            $is_high = false;
            if($yld>$p80)
            {                
                $is_high = true;
            }
            $setdate = explode('-',$rec->observe_date);
            $tmpdate = mktime(0,0,0,$setdate[1],$setdate[2],$setdate[0]);

            $emergence_limit = $oryza_class->getEmergenceLimit($rec->dataset_id,$rec->runnum,$rec->observe_date);                 
                
            $tmp = array($tmpdate*1000,$yld,$is_high);
            $tmp['fert'] = $this->getAdvisoryFert2($tmpdate,$emergence_limit);
            $tmp['key'] = $key;
            $hi[] = $tmp;
            $hi_sorter[] = $yld;            
        }
        array_multisort($hi_sorter,SORT_DESC,$hi);
        $this->advisory['hi_yld'] = $hi;
    }

    /**
     * @todo: documentation
     * @param integer $dataset_index
     */
    private function getAdvisoryFert($dataset_index)
    {
        $dataset = $this->compare[$dataset_index];
        $this->advisory['fert_apply'] = false;

        if ($dataset['fertil']==0)
        {
            $this->advisory['fert_apply'] = false;
        }

        $weather = new weather_data;
        $data = $weather->getDecadal($this->country, $this->station, $dataset['year'], $dataset['wtype'], 0, false, $this->geolat);
        $series = dss_utils::getDecadalSeries($dataset['year'], $this->geolat);

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

        $this->advisory['fert_apply'] = $fapply;

    }

    /**
     *
     * @param type $date
     * @return type 
     */
    private function getAdvisoryFert2($date,$emergence_limit)
    {
        /**
        $tmp = DateTime::createFromFormat('U', $date);
        echo 'observe_date:'.$tmp->format('Y-m-d').'<br />';
        echo '<hr />';
            
        $tmp = DateTime::createFromFormat('U', $emergence_limit);
        echo 'emergence_date:'.$tmp->format('Y-m-d').'<br />';
        echo '<hr />';            
         * 
         */
            
        $ret = false;
        foreach ($this->advisory['fert_apply'] as $fert)
        {
            /*
            $tmp = DateTime::createFromFormat('U', $fert['from'][0]/1000);
            echo 'from_date:'.$tmp->format('Y-m-d').'<br />';
            echo '<hr />'; 
            
            $tmp = DateTime::createFromFormat('U', $fert['to'][0]/1000);
            echo 'to_date:'.$tmp->format('Y-m-d').'<br />';
            echo '<hr />';             
             * 
             */
            
            if ( $date <= ($fert['to'][0]/1000) && $emergence_limit >= ($fert['from'][0]/1000) )
            {
                // adjust from
                if ( $date >= ($fert['from'][0]/1000) )
                {
                    $fert['from'][0] = $date*1000;
                }
                
                // adjust to
                if ( $emergence_limit <= ($fert['to'][0]/1000) )
                {
                    $fert['to'][0] = $emergence_limit*1000;
                }                

                $ret[] = $fert;
            }
        }

        return $ret;
    }
}

$cls = new oryza_chart;
$cls->init();
echo $cls->getChartData();
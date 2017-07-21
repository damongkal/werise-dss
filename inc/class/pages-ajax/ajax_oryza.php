<?php

class ajax_oryza extends ajax_base
{
    private $station;
    private $compare;

    private $chart_data;
    private $grid_data;
    private $raw_data;
    private $advisory = false;

    protected function actionDefault() {        
        if (_ADM_ENV==='PROD' && dss_auth::getUsername()==='')
        {
            $this->json_ret = 'unauthorized access';
            return;
        }        
        $this->init();
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

        // compare datasets
        $this->compare = array();
        $this->addCompareSet(1);
        $this->addCompareSet(2);
        $this->addCompareSet(3);
        $this->addCompareSet(4);        

        $this->prepareData();
        $this->getAdvisory();
    }

    private function prepareData()
    {
        // prepare chart data
        $month_start = 1;
        if ($this->station->geo_lat < 0)
        {
            $month_start = 7;
        }
        list($season_start,$season_end) = dss_utils::getCropSeason($month_start,$this->compare[0]['year']);
        $chart_yield = new werise_chart_yield($this->station,$season_start,$season_end);
        foreach ($this->compare as $dataset)
        {
            $chart_yield->addDataset($dataset['variety'], $dataset['wtype'], $dataset['fertil']);
        }        
        $chart_yield->prepareChart();       
        
        foreach($chart_yield->getDatasets() as $dataset)
        {
            $this->raw_data[] = $dataset['data'];
        }

        // wet season period
        list($ws_from, $ws_to) = dss_utils::getWetSeasonPeriod($this->station->geo_lat,  werise_chart_yield::_FAKEYEAR);

        $this->chart_data = array(
            'series' => $chart_yield->getChartSeries(),
            'wetseason' => array(
                'from' => werise_core_date::toUTC($ws_from->format('U')),
                'to' => werise_core_date::toUTC($ws_to->format('U'))),
            'maxyld' => $chart_yield->getChartMaxYield()
        );

    }

    private function getChartData() {
        return array(
            'chart' => $this->chart_data,
            'grid' => $this->grid_data,
            'advisory' => $this->advisory
        );
    }

    private function getAdvisory()
    {        
        $dataset_index = 0;
        $dataset = $this->compare[$dataset_index];

        // get dates where there is rain
        $fertcls = new advisory_fertilizer;        
        $this->advisory['fert_apply'] = $fertcls->getAdvisoryFert($this->station, $dataset['year'], $dataset['wtype'], $dataset['fertil'] );
        
        $this->getAdvisoryYld($dataset_index);
        $this->getAdvisoryCompare();
    }

    /**
     * @todo documentation
     */
    private function getAdvisoryYld($dataset_index)
    {
        $dataset = $this->compare[$dataset_index];

        // get 80th percentile of yield
        $yld = array();
        foreach($this->raw_data[$dataset_index] as $rec)
        {
            $yld[] = $rec->yield+0;
        }
        $p80 = dss_utils::percentile($yld,80);
        
        // rainfall
        $advrain = new advisory_rainfall($this->station,$dataset['wtype']);

        // get last day of emergence
        $hi = array();
        $hi_sorter = array();
        foreach($this->raw_data[$dataset_index] as $key => $rec)
        {
            $yld = $rec->yield + 0;

            // is yld high?
            $is_high = false;
            if($yld>$p80)
            {
                $is_high = true;
            }

            // sowing date
            $sowdate = explode('-',$rec->observe_date);
            $sowdate_u = mktime(0,0,0,$sowdate[1],$sowdate[2],$sowdate[0]);
            // panicle initiation date
            $panicle_init_date = werise_oryza_cropcalendar::getDate($rec->observe_date, $rec->panicle_init);
            // flowering date, emergence limit
            $emergence_limit = werise_oryza_cropcalendar::getDate($rec->observe_date, $rec->flowering);
            // harvest date
            $harvest_date = werise_oryza_cropcalendar::getDate($rec->observe_date, $rec->harvest);

            // tmp array
            $tmp = array($sowdate_u*1000,$yld,$is_high);
            $tmp['key'] = $key;
            $tmp['dataset_id'] = $rec->dataset_id;
            $tmp['runnum'] = $rec->runnum;

            // crop calendar
            $tmp['cropdate_panicle_init'] = $panicle_init_date * 1000;
            $tmp['cropdate_flowering'] = $emergence_limit * 1000;
            $tmp['cropdate_harvest'] = $harvest_date * 1000;

            // fert advisory
            $tmp['fert'] = $this->getAdvisoryFert2($sowdate_u,$emergence_limit);
            $tmp['fertsched'] = $this->getAdvisoryFertSched($dataset,$rec);
            
            // rainfall advisory
            $fromdate = date_create($rec->observe_date);
            $todate = date_create(date('Y-m-d',$harvest_date));
            $raindata = $advrain->getAdvisory($fromdate,$todate);
            $tmp['rain_amt'] = $raindata['wsrain'];
            $tmp['rain_code'] = $raindata['advisory_cat'];

            // high yield array
            $hi[] = $tmp;
            $hi_sorter[] = $yld;
        }
        array_multisort($hi_sorter,SORT_DESC,$hi);
        $this->advisory['hi_yld'] = $hi;
    }

    /**
     *
     * @param type $date
     * @return type
     */
    private function getAdvisoryFert2($date,$emergence_limit)
    {
        $ret = false;
        if (!$this->advisory['fert_apply'])
        {
            return $ret;
        }
        foreach ($this->advisory['fert_apply'] as $key => $fert)
        {
            $fert['key'] = $key;
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

    private function getAdvisoryFertSched($dataset,$rec) {
        if ($dataset['fertil'] == 0) {
            return "0";
        }

        return $rec->fert;
    }

    private function getAdvisoryCompare()
    {
        $this->advisory['compare_seta'] = '';
        $this->advisory['compare_setb'] = '';
        $this->advisory['compare_yld'] = 0;
        $this->advisory['compare_date'] = '';

        if (isset($this->raw_data[1]))
        {
            if ($this->compare[0]['year']!==$this->compare[1]['year'])
            {
                return;
            }

            $hiset1 = 1;
            foreach($this->raw_data[0] as $key => $rec)
            {
                $diff = $rec->yield - $this->raw_data[1][$key]->yield;
                $diff_abs = abs($diff);
                if ($diff_abs > $this->advisory['compare_yld'])
                {
                    $this->advisory['compare_yld'] = $diff_abs;
                    $this->advisory['compare_date'] = date('F-d',strtotime($rec->observe_date));
                }
                $hiset1 = ($diff>0) ? 1 : 2;
            }
            $hiset2 = ($hiset1 === 1) ? 2 : 1;
            $this->advisory['compare_seta'] = 'SET '.$hiset1;
            $this->advisory['compare_setb'] = 'SET '.$hiset2;
        }
    }
    
    private function addCompareSet($set) {

        $s = $set;
        if ($set === 1) {
            $s = '';
        }
        if (isset($_GET['year' . $s])) {
            $year = intval($_GET['year' . $s]);
            if ($year > 0) {
                $wtype = '';
                if (isset($_GET['wtype' . $s]) && in_array($_GET['wtype' . $s], array('r', 'f'))) {
                    $wtype = $_GET['wtype' . $s];
                }
                $variety = '';
                if (isset($_GET['variety' . $s])) {
                    $variety = $_GET['variety' . $s];
                }
                $fertil = 0;
                if (isset($_GET['fert' . $s])) {
                    $fertil = intval($_GET['fert' . $s]);
                }
                
                $compare_set = array(
                    'year' => $year,
                    'wtype' => $wtype,
                    'variety' => $variety,
                    'fertil' => $fertil
                );
                $this->compare[] = $compare_set;
                $this->addAccesslog($compare_set);                
            }
        }
    }

    private function addAccesslog($compare_set)
    {
        $db = Database_MySQL::getInstance();
        $userid = dss_auth::getUserId();
        $sql = "
            INSERT INTO `oryza_access_log` (
                `userid`, `country_code`, `station_id`,
                `year`, `wtype`, `variety`,
                `fert`, `create_date` )
            VALUES (
                %d, '%s', %d,
                %d, '%s', '%s',
                '%s', NOW()
            )";
        $sql2 = sprintf($sql,
            $userid,
            $this->station->country_code,
            $this->station->station_id,
            $compare_set['year'],
            $compare_set['wtype'],
            $db->escape($compare_set['variety']),
            $compare_set['fertil']);    
        $db->query($sql2);
    }        

}
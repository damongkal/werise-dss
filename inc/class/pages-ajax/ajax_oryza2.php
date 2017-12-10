<?php

class ajax_oryza2 extends ajax_base {
    private $advisory;
    
    protected function actionCombilist() {
        $country = $this->getArg('country','');
        $station_id = $this->getArg('station',0);
        $wstation = new weather_stations;
        $station = $wstation->getStation($country, $station_id);
        if (!$station)
        {
            throw new Exception("Invalid Station: {$country}-{$station_id}");
        }
        $dataset = new werise_core_dataset;
        $dataset->setStation($station);
        $dataset->setYear($this->getArg('year',0));
        $dataset->setWType($this->getArg('wtype',''));
        
        // advisory type
        $adv_type = $this->getArg('cstype',werise_oryza_cropcalendar2::_TYPE_RECO);
        
        // get high yields
        $cal = new werise_oryza_cropcalendar2();
        // crop 1
        $crop1 = array(
            'variety'=>$this->getArg('c1variety',''),
            'fert'=>$this->getArg('c1fert',''));
        // crop 2
        $crop2 = array(
            'date'=>$this->getArg('c2date',''),
            'variety'=>$this->getArg('c2variety',''),
            'fert'=>$this->getArg('c2fert',''));        
        if ($adv_type === werise_oryza_cropcalendar2::_TYPE_RECO)
        {
            // get crop season
            list($season_start,$season_tmp1) = dss_utils::getCropSeason(1,$dataset->getYear());        
            list($season_tmp2,$season_end) = dss_utils::getCropSeason(12,$dataset->getYear());                           
            // get rain dates
            $raindates = $this->getRainDates($dataset,$season_start,$season_end);
            $high_yield = $cal->getRecommended($dataset,$raindates,$season_start,$season_end,$crop1,$crop2);
        } else
        {
            // get crop season
            $cs1date = $this->getArg('c1date',$dataset->getYear().'-01-01');
            $crop1['date'] = $cs1date;
            $cs1datetmp = explode('-',$cs1date);
            $month_start = intval($cs1datetmp[1]);
            list($season_start,$season_end) = dss_utils::getCropSeason($month_start,$dataset->getYear());
            // get rain dates
            $raindates = $this->getRainDates($dataset,$season_start,$season_end);
            $high_yield = $cal->getCustom($dataset,$raindates,$crop1,$crop2);
        }
        // get yield chart period
        list($chart_start,$chart_end) = $this->getYieldChartPeriod($high_yield);        
        // rain dates
        $this->advisory['fert_apply'] = $raindates;                            
        // high yields
        $this->advisory['hiyld'] = $high_yield;
        
        // yield chart
        $chart_yield = new werise_chart_yield($dataset->getStation(),$chart_start,$chart_end);
        foreach($this->getVarietyFertCombo($high_yield['runnums']) as $combo)
        {
            $chart_yield->addDataset($combo[0], $dataset->getWType(), $combo[1]);
        }
        $chart_yield->prepareChart();
        $this->advisory['chart_yield'] = array(
            'series' => $chart_yield->getChartSeries(),
            'maxyld' => $chart_yield->getChartMaxYield());        
        
        return $this->advisory;
    }
    
    /**
     * get dates where there is rain
     * @param werise_core_dataset $dataset
     * @return type
     */
    private function getRainDates(werise_core_dataset $dataset,$season_start,$season_end)
    {
        $fertcls = new advisory_fertilizer;        
        $raindate = array();        
        $years = array($dataset->getYear()-1,$dataset->getYear(),$dataset->getYear()+1);
        foreach ($years as $year)
        {
            $raindate1 = $fertcls->getAdvisoryFert($dataset->getStation(), $year, $dataset->getWType(), advisory_fertilizer::_FERT_GEN );            
            foreach ($raindate1 as $tmp)
            {
                if ($tmp['from'][2]>$season_start && $tmp['to'][2]<$season_end)
                {
                    $raindate[] = $tmp;
                }
            }
        }     
        return $raindate;
    }

    /**
     * get from runnums the unique combinations for variety/fert
     * to be used in grain yield chart
     * @param type $runnums
     * @return type
     */
    private function getVarietyFertCombo($runnums)
    {
        $combo = array();
        foreach($runnums as $runnum)
        {
            $idx = werise_oryza_terms::getVarietyLabel($runnum['variety']).intval($runnum['fertcode']);
            $combo[$idx] = array($runnum['variety'],$runnum['fertcode']);
        }
        return $combo;
    }
    
    private function getYieldChartPeriod($high_yield) {

        // get indexes of top records
        $idxs = array();
        foreach ($high_yield['toprecs'] as $yieldrec) {
            $idxs[] = $yieldrec['first']['dataset_id'].'_'.$yieldrec['first']['runnum'];
            foreach ($yieldrec['second'] as $second) {
                $idxs[] = $second['dataset_id'].'_'.$second['runnum'];
            }
        }
        
        // get min-max dates
        $chart_start = 0;
        $chart_end = 0;
        foreach ($idxs as $idx) {           
            $rec = $high_yield['runnums'][$idx];
            $newval = date('Y-m-', ($rec['sow_date'] / 1000)) . '01';
            if ($chart_start === 0 || $chart_start > $newval) {
                $chart_start = $newval;
            }
            if ($chart_start === 0 || $chart_end < $newval) {
                $chart_end = $newval;
            }                
        }
        return array($chart_start,$chart_end);
    }

}

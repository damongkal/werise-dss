<?php

class werise_chart_yield
{
    /**
     * we set a fake year so that we can compare
     */
    const _FAKEYEAR = 2000;
    /**
     * variables common to all dataset
     * @var type 
     */
    private $station;
    private $chart_start;
    private $chart_end;
    
    /**
     * datasets container
     * @var type 
     */
    private $datasets;
    /**
     * chart variables
     * @var type 
     */
    private $chart_series;
    private $chart_maxyield;
    
    public function __construct($station, $chart_start, $chart_end) {
        $this->station = $station;
        $this->chart_start = $chart_start;
        $this->chart_end = $chart_end;
    }

    public function addDataset($variety, $wtype, $fert) {
        $this->datasets[] = array(
            'wtype' => $wtype,
            'variety' => $variety,
            'fertil' => intval($fert)
        );
    }

    public function prepareChart()
    {
        $series = $this->makeSeriesTemplate();
                
        foreach ($this->datasets as $key => $dataset)
        {
            $data = $this->getYieldData($dataset);
            $first_year = $this->getSowYear($data[0]->observe_date);
            $period = $this->getChartPeriod($first_year);       
            $this->chart_series[] = array(
                'name' => $this->getDatasetName($dataset,$key,$period),
                'data' => $this->makeSeriesData($series, $data, $first_year)
            );
            $this->datasets[$key]['data'] = $data;
        }
        
        $oryza = new oryza_data;
        $this->chart_maxyield = $oryza->getMaxYield($this->station);        
    }
    
    public function getChartSeries()            
    {
        return $this->chart_series;
    }    
    
    public function getChartMaxYield()
    {
        return $this->chart_maxyield;
    }
    
    public function getDatasets()            
    {
        return $this->datasets;
    }        
    
    private function makeSeriesTemplate() {
        // months period
        $d1 = new DateTime($this->chart_start);
        $d2 = new DateTime($this->chart_end);
        $interval = $d1->diff($d2,true);
        $period = (intval($interval->format('%y'))*12)+intval($interval->format('%m'))+1;        
        
        $date1 = explode('-',$this->chart_start);
        $startd = DateTime::createFromFormat('Y-m-d H:i', self::_FAKEYEAR.'-'.$date1[1].'-01 0:00');        
        $day15 =  clone $startd;
        $day15->modify('+14 day');
        $series = array();
        for ($month = 0; $month < $period; $month++) {
            // day 1
            $series[] = array(werise_core_date::toUTC($startd->format('U')),null);
            $startd->modify('+1 month');
            // day 15
            $series[] = array(werise_core_date::toUTC($day15->format('U')),null);
            $day15->modify('+1 month');
        }
        return $series;
    }
    
    private function makeSeriesData($series, $data, $first_year) {
        foreach ($data as $rec) {
            $tmpdate = $this->getFakeSowDate($rec->observe_date, $first_year);
            $setdate_utc = werise_core_date::toUTC($tmpdate);
            foreach($series as $key => $sdata)
            {
                if ($sdata[0]===$setdate_utc)
                {
                    $series[$key][1] = $rec->yield + 0;
                }
            }
        }
        return $series;
    }

    private function getYieldData($dataset) {
        $oryza = new oryza_data;
        $filter = array();
        $filter['startdate'] = $this->chart_start;
        $filter['enddate'] = $this->chart_end;

        $data = $oryza->getYield(
                $this->station, 
                0, 
                $dataset['wtype'], 
                $dataset['fertil'], 
                $dataset['variety'], 
                $filter);
        return $data;
    }

    /**
     * 
     * @param array $dataset
     * @param int $key
     * @param string $period
     * @return type
     */
    private function getDatasetName($dataset,$key, $period)
    {
        $set = _t("Set")." ".($key+1);
        $wtype_name = werise_weather_properties::getTypeDesc($dataset['wtype']);
        $fert_name = werise_oryza_fertilizer::getTypeDesc($dataset['fertil']);
        $variety = werise_oryza_terms::getVarietyLabel($dataset['variety']);
        return "{$set}: {$period} , {$wtype_name} , {$variety} , {$fert_name}";
    }    
    
    /**
     * determine chart period
     * @param int $first_sowyear
     */
    private function getChartPeriod($first_sowyear) {        
        list($season_start,$season_end) = dss_utils::getCropSeason2($this->chart_start,$this->chart_end,'M-Y');
        return "{$season_start} to {$season_end}";
    }
    
    private function getFakeSowDate($sowdate, $first_year) {
        $setdate = explode('-', $sowdate);
        $fakeyear = self::_FAKEYEAR;
        if ( $setdate[0] != $first_year) {
            $fakeyear = $fakeyear + 1;
        }
        $date = DateTime::createFromFormat('Y-m-d H:i', $fakeyear.'-'.$setdate[1].'-'.$setdate[2].' 0:00');
        return $date->format('U');
    }
    
    private function getSowYear($sowdate)
    {
        $setdate = explode('-', $sowdate);
        return $setdate[0];
    }    

}
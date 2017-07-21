<?php
/**
 * two cropping calendar advisory
 */
class werise_oryza_cropcalendar2
{
    const _TYPE_RECO = 'recommend';
    const _TYPE_CUST = 'custom';
    private $rain_advisory;
    private $raindata;
    private $raindates;
    private $runnums = array();
    
    public function getRecommended(werise_core_dataset $dataset, $raindates, $season_start,$season_end )
    {
        $this->rain_advisory = new advisory_rainfall($dataset->getStation(),$dataset->getWType());
        $this->raindates = $raindates;
        return $this->getList($dataset,$season_start,$season_end);
    }
    
    public function getCustom(werise_core_dataset $dataset, $raindates, $crop1, $crop2)
    {
        $this->rain_advisory = new advisory_rainfall($dataset->getStation(),$dataset->getWType());
        $this->raindates = $raindates;
        return $this->getList2($dataset,$crop1, $crop2);
    }         
    
    private function getList(werise_core_dataset $dataset, $season_start, $season_end)
    {        
        $db = Database_MySQL::getInstance();
        
        // rest days after 1st harvest
        $rest = 5;
        // get max crop calendar period
        $maxharvest = $this->getMaxHarvest($dataset,$season_start,$season_end);
        // get 1st crop calendar cutoff
        $cutoff1 = $this->getCalendarCutoff($season_end,$maxharvest+$rest);
        // get 2nd crop calendar cutoff
        $cutoff2 = 365 - $maxharvest - $rest;          
        // main SQL
        $sql4 = "
            SELECT a.variety,a.fert AS fertcode,b.*
            FROM "._DB_DATA.".`oryza_dataset` AS a
            INNER JOIN "._DB_DATA.".`oryza_data` AS b
                ON a.`id` = b.`dataset_id`
            WHERE a.`country_code` = '%s'
                AND a.`station_id` = %u
                AND a.wtype = '%s'
                AND a.fert = 1";               
        $sql5 = sprintf($sql4,
            $dataset->getCountryCode(),
            $dataset->getStationId(),
            $dataset->getWType());
        // collector
        $combi = array();
        $combikeys = array();
        
        // 1st crop
        $sql6 = sprintf("{$sql5}
            AND b.`observe_date` >= '%s'
            AND b.observe_date < '%s'",
            $db->escape($season_start),
            $cutoff1);
        $rs3 = $db->getRowList($sql6);
        foreach($rs3 as $rec3) // 1st crop
        {
            // second crop
            $sql7 = sprintf("{$sql5}
                AND b.`observe_date` >= DATE_ADD('%s', INTERVAL %u DAY)
                AND b.observe_date < DATE_ADD('%s', INTERVAL %u DAY)",
                $rec3->observe_date, 
                intval($rec3->harvest+$rest),
                $rec3->observe_date,     
                $cutoff2    
                );
            $rs4 = $db->getRowList($sql7);
            $second = $this->getSecondCropList($rs4);
            $scount = count($second);
            // make combination
            if($scount>0)
            {
                $first = $rec3;
                $combi[] = array(
                    'first' => $first,
                    'maxyld' => $first->yield + $second[0]->yield,
                    'minyld' => $first->yield + $second[$scount-1]->yield,
                    'second' => $second);
                $combikeys[] = array($first->yield + $second[0]->yield);            
            }
        }
        array_multisort($combikeys, SORT_DESC, SORT_REGULAR, $combi  );
        $final = array_slice($combi, 0, 2);
        
        // breakdown
        $ref = array();
        foreach($final as $frec)
        {
            $tmp = array();                        
            $tmp['first'] = array(
                'dataset_id' => $frec['first']->dataset_id,
                'runnum' => $frec['first']->runnum);
            $this->buildCalendar($frec['first']);
            $tmp2 = array();                        
            foreach ($frec['second'] as $frec2)
            {
                $tmp2[] = array(
                    'dataset_id' => $frec2->dataset_id,
                    'runnum' => $frec2->runnum);                
            }
            $tmp['second'] = $tmp2;
            $tmp['maxyld'] = $frec['maxyld'];
            $tmp['minyld'] = $frec['minyld'];            
            $ref[] = $tmp;
        }
        foreach($final as $frec)
        {
            foreach ($frec['second'] as $frec2)
            {              
                $this->buildCalendar($frec2);
            }
        }        
        return array('runnums'=>$this->runnums,'toprecs'=>$ref);
    }
    
    private function getList2(werise_core_dataset $dataset, $crop1, $crop2)
    {        
        $db = Database_MySQL::getInstance();
            
        // main SQL
        $sql1 = "
            SELECT a.variety,a.fert AS fertcode,b.*
            FROM "._DB_DATA.".`oryza_dataset` AS a
            INNER JOIN "._DB_DATA.".`oryza_data` AS b
                ON a.`id` = b.`dataset_id`
            WHERE a.`country_code` = '%s'
                AND a.`station_id` = %u
                AND a.wtype = '%s'";               
        $sql2 = sprintf($sql1,
            $dataset->getCountryCode(),
            $dataset->getStationId(),
            $dataset->getWType());
        // collector
        $combi = array();
        $combikeys = array();
        // 1st crop
        $sql4 = "{$sql2}
            AND b.`observe_date` >= '%s'
            AND a.variety = '%s'
            AND a.fert = '%s'                
            ORDER BY b.`observe_date`
            LIMIT 2";
        $sql3 = sprintf($sql4,
            $db->escape($crop1['date']),
            $db->escape($crop1['variety']),
            $db->escape($crop1['fert']));
        $rs3 = $db->getRowList($sql3);
        foreach($rs3 as $rec3) // 1st crop
        {
            // second crop
            $sql7 = sprintf($sql4,
                $db->escape($crop2['date']),
                $db->escape($crop2['variety']),
                $db->escape($crop2['fert']));            
            $rs4 = $db->getRowList($sql7);
            $second = $this->getSecondCropList($rs4);
            $scount = count($second);
            // make combination
            if($scount>0)
            {
                $first = $rec3;
                $combi[] = array(
                    'first' => $first,
                    'maxyld' => $first->yield + $second[0]->yield,
                    'minyld' => $first->yield + $second[$scount-1]->yield,
                    'second' => $second);
                $combikeys[] = array($first->yield + $second[0]->yield);            
            }
        }
        array_multisort($combikeys, SORT_DESC, SORT_REGULAR, $combi  );        
        $final = array_slice($combi, 0, 5);            
        
        // breakdown
        $ref = array();
        foreach($final as $frec)
        {
            $tmp = array();                        
            $tmp['first'] = array(
                'dataset_id' => $frec['first']->dataset_id,
                'runnum' => $frec['first']->runnum);
            $this->buildCalendar($frec['first']);
            $tmp2 = array();                        
            foreach ($frec['second'] as $frec2)
            {
                $tmp2[] = array(
                    'dataset_id' => $frec2->dataset_id,
                    'runnum' => $frec2->runnum);                
            }
            $tmp['second'] = $tmp2;
            $tmp['maxyld'] = $frec['maxyld'];
            $tmp['minyld'] = $frec['minyld'];            
            $ref[] = $tmp;
        }
        foreach($final as $frec)
        {
            foreach ($frec['second'] as $frec2)
            {              
                $this->buildCalendar($frec2);
            }
        }        
        return array('runnums'=>$this->runnums,'toprecs'=>$ref);
    }    
    
    private function getSecondCropList($rs)
    {
            $second = array();
            $combikeys = array();
            foreach($rs as $rec)
            {
                $second[] = $rec;
                $combikeys[] = array($rec->yield);
            }
            array_multisort($combikeys, SORT_DESC, SORT_REGULAR, $second  );
            $tmp_second = array_slice($second, 0, 2);            
            return $tmp_second;
    }
    
    private function buildCalendar($rec,$format='U')
    {
        $idx = $rec->dataset_id.'_'.$rec->runnum;        
        if (isset($this->runnums[$idx]))
        {
            return;
        }
        // rain advisory
        $harvestdate = $this->getDateFromOffset($rec->observe_date, $rec->harvest, 'Y-m-d');
        $raindata = $this->getRainAdvisory($rec->observe_date, $harvestdate);
        // fertilizer schedule
        $sowdate_u = $this->getDateFromOffset($rec->observe_date, 0) / 1000;
        $flower_u = $this->getDateFromOffset($rec->observe_date, $rec->flowering) / 1000;                
        $fert_advisory = new advisory_fertilizer;
        $fert_sched = $fert_advisory->getFertSchedule($this->raindates,$sowdate_u,$flower_u);        
        // crop calendar
        $calendar = array();
        $calendar['variety'] = $rec->variety;
        $calendar['fertcode'] = $rec->fertcode;
        $calendar['fert'] = $rec->fert;
        $calendar['dataset_id'] = intval($rec->dataset_id);
        $calendar['runnum'] = intval($rec->runnum);
        $calendar['yield'] = floatval($rec->yield);
        $calendar['sow_date'] = $this->getDateFromOffset($rec->observe_date, 0, $format);
        $calendar['panicleinit_date'] = $this->getDateFromOffset($rec->observe_date, $rec->panicle_init, $format);
        $calendar['flower_date'] = $this->getDateFromOffset($rec->observe_date, $rec->flowering, $format);
        $calendar['harvest_date'] = $this->getDateFromOffset($rec->observe_date, $rec->harvest, $format);
        $calendar['fertsched'] = $fert_sched;                     
        $calendar['rain_amt'] = $raindata['wsrain'];                     
        $calendar['rain_code'] = $raindata['advisory_cat'];                       
        $this->runnums[$idx] = $calendar;
    }
    
    private function getRainAdvisory($sowdate,$harvestdate)
    {
        if (!isset($this->raindata[$sowdate]))
        {
            $fromdate = date_create($sowdate);
            $todate = date_create($harvestdate);
            $this->raindata[$sowdate] = $this->rain_advisory->getAdvisory($fromdate,$todate);
        }        
        return $this->raindata[$sowdate];
    }  
    
    private function getDateFromOffset($date,$offset,$format='U')
    {
        $datec = DateTime::createFromFormat('Y-m-d', $date);
        $datet = $datec->add(new DateInterval("P{$offset}D"));
        if ($format === 'U')
        {
            return $datet->format($format) * 1000;
        } else
        {
            return $datet->format($format);
        }
    }

    /**
     * get max crop calendar
     * @param werise_core_dataset $dataset
     * @return type
     */
    private function getMaxHarvest(werise_core_dataset $dataset,$season_start,$season_end)
    {
        $db = Database_MySQL::getInstance();
        $sql = "
            SELECT MAX(b.`harvest`) AS maxharvest
            FROM "._DB_DATA.".`oryza_dataset` AS a
            INNER JOIN "._DB_DATA.".`oryza_data` AS b
                ON a.`id` = b.`dataset_id`
            WHERE a.`country_code` = '%s'
                AND a.`station_id` = %u
                AND a.wtype = '%s'
                AND b.`observe_date` >= '%s'
                AND b.`observe_date` <= '%s'";        
        $sql2 = sprintf($sql,
            $db->escape($dataset->getCountryCode()),
            $dataset->getStationId(),
            $db->escape($dataset->getWType()),
            $db->escape($season_start),
            $db->escape($season_end));
        $tmp = $db->getRow($sql2);
        return $tmp->maxharvest;
    }
    
    /**
     * get crop calendar cutoff
     * @param type $interval
     */
    private function getCalendarCutoff($date,$days)
    {
        $db = Database_MySQL::getInstance();
        $sql = "SELECT DATE_SUB('%s',INTERVAL %u DAY) AS cutoff";
        $sql2 = sprintf($sql,$db->escape($date),intval($days));
        $tmp = $db->getRow($sql2);        
        return $tmp->cutoff;
    }

}
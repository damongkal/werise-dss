<?php

class oryza_data
{

    private $db;
    private $datafile;

    const _REALTIME = 'r';
    const _FORECAST = 'f';

    /**
     * @todo : documentation 
     */
    public function __construct()
    {
        $this->db = Database_MySQL::getInstance();
        $this->datafile = new datafiles;
    }

    /**
     * @todo : documentation
     * @param type $file
     * @param type $wtype
     * @return type 
     */
    public function load($file, $wtype)
    {
        $oryza = new oryza2000_api;
        
        if ($wtype == self::_REALTIME)
        {
            $subdir = _DATA_SUBDIR_WEATHER_REALTIME;
        }
        if ($wtype == self::_FORECAST)
        {
            $subdir = _DATA_SUBDIR_WEATHER_FORECAST;
        }

        $dataset = $this->datafile->getDatasetFromFilename($file);

        // run Oryza2000
        $datasets = $oryza->exec($dataset['country'], $dataset['station'], $dataset['year'], $wtype);

        if ($datasets)
        {
            $debug = array();
            
            // save dataset to DB
            foreach($datasets as $key => $dset)
            {
                $datasets[$key]['id'] = $this->initDataSet($dset);
                unset($dset['sttimes']);
                $debug['dataset'][] = $dset;                
            }

            // load op.dat
            $load_op = $this->loadOutput($datasets);
            if ($load_op)
            {
                $debug['db_op'] = $load_op[1];
            } else
            {
                return array(false,$load_op[1]);
            }
            
            // load res.dat
            $load_res = $this->loadRes($datasets);
            if ($load_res)
            {
                $debug['db_res'] = $load_res[1];
            } else
            {
                return array(false,$load_res[1]);
            }
            
            $this->cleanAfterLoad($datasets);        

            return array(true,$debug);
        } else
        {
            return array(false,$oryza->error_msg);
        }
    }

    /**
     * read Oryza2000 op.dat for data loading
     * @param type $file
     * @param type $type
     * @return type 
     */
    private function loadOutput($datasets)
    {
        $error = '';
        $debug = array();
        $handle = oryza2000_api::getOpFile();
        if ($handle)
        {
            while (($buffer = fgets($handle, 4096)) !== false)
            {
                $vars = $this->datafile->validate2($buffer);
                if ($vars)
                {
                    // determine dataset from runnum
                    $id = floor(($vars[0]-1) / 24);
                    $this->addData($datasets[$id], $vars);
                    $debug[] = $vars;
                }
            }
            if (!feof($handle))
            {
                $error = "unexpected fgets() fail\n";
            }

            fclose($handle);
        }
        else
        {
            $error = 'file does not exist.';
        }

        if ($error != '')
        {
            return array(false, $error);
        }
        else
        {
            return array(true, $debug);
        }
    }
    
    /**
     * read Oryza2000 res.dat for data loading
     * @param type $file
     * @param type $type
     * @return type 
     */
    private function loadRes($datasets)
    {
        $error = '';
        $debug = array();
        $handle = oryza2000_api::getResFile();
        if ($handle)
        {
            while (($buffer = fgets($handle, 4096)) !== false)
            {
                $vars = $this->datafile->validate3($buffer);
                if ($vars)
                {
                    if ($vars[0]=='RUNNUM')
                    {
                        // determine dataset from runnum
                        $runnum = $vars[1];
                        $id = floor( ($runnum-1) / 24);
                    } else
                    {
                        $this->addDataRes($datasets[$id], $runnum, $vars[1]);
                    }
                    $debug[] = $vars[1];
                }
            }
            if (!feof($handle))
            {
                $error = "unexpected fgets() fail\n";
            }

            fclose($handle);
        }
        else
        {
            $error = 'file does not exist.';
        }

        if ($error != '')
        {
            return array(false, $error);
        }
        else
        {
            return array(true, $debug);
        }
    }    

    /**
     * @todo : documentation
     * @param type $dataset
     * @param type $wtype
     * @param type $fert
     * @param type $variety
     * @return boolean 
     */
    public function isLoaded($dataset, $wtype ='r', $fert = 0, $variety = '')
    {
        $country = mysql_real_escape_string($dataset['country']);
        $station_id = intval($dataset['station']);
        $year = intval($dataset['year']);

        $specific_set = '';
        if ($variety!='')
        {
            $specific_set = "AND `wtype` = '{$wtype}' AND `fert` = {$fert} AND `variety` = '{$variety}'";
        }
        $sql = "
            SELECT `id`,`upload_date`
            FROM `oryza_dataset`
            WHERE `country_code` = '{$country}'
                AND `station_id` = {$station_id}
                AND `year` = '{$year}'
                {$specific_set}
            LIMIT 1";

        $ret = $this->db->getRow($sql);
        if ($ret)
        {
            return $ret;
        }
        else
        {
            return false;
        }
    }

    /**
     * @todo : documentation
     * @param type $dataset
     * @return type 
     */
    private function initDataSet($dataset)
    {
        $rs = $this->isLoaded($dataset, $dataset['wtype'], $dataset['fert'], $dataset['variety']);
        if ($rs)
        {
            $id = $rs->id;
            $sql = "DELETE FROM `oryza_data` WHERE `dataset_id`={$id}";
            $this->db->query($sql);
        }
        else
        {
            $sql = sprintf("
                INSERT INTO `oryza_dataset` (
                    `country_code`,`station_id`, `year`,
                    `wtype`, `variety`, `fert`, `upload_date`)
                VALUES (
                    '%s', %s, '%s', '%s', '%s', %s, CURDATE())", 
                    $dataset['country'], $dataset['station'], 
                    $dataset['year'], $dataset['wtype'], 
                    $dataset['variety'], $dataset['fert']);

            $this->db->query($sql);
            $id = $this->db->getInsertId();
        }

        return $id;
    }

    /**
     * @todo : documentation
     * @param type $dataset
     * @param type $data 
     */
    private function addData($dataset, $data)
    {
        //echo '<pre>';
        //print_r($dataset);
        //echo '</pre>';

        // op.dat columns 
        $runnum = $data[0]; // runnum
        $wrr14 = $data[1] / 1000; // yield (g/ha)
        
        // sttime
        $sttime = $dataset['sttimes'][$runnum];
        $date = DateTime::createFromFormat('Y-m-d', ($dataset['year']-1).'-12-31');
        $date->add(new DateInterval("P{$sttime}D"));
        
        $sql = sprintf("
            INSERT INTO `oryza_data` (
                `dataset_id`, `runnum`, `observe_date`, `yield`)
            VALUES (%s, %s, '%s', %s)",
            $dataset['id'],
            $runnum,
            date_format($date, 'Y-m-d'),
            $wrr14
        );

        $this->db->query($sql);
        
        return $data;
    }    
    
    /**
     * @todo : documentation
     * @param type $dataset
     * @param type $data 
     */
    private function addDataRes($dataset, $runnum, $data)
    {
        //echo '<pre>';
        //print_r($dataset);
        //echo '</pre>';

        // res.dat columns 
        $day = intval($data[0]);
        $dvs = $data[1];
        
        $sql = sprintf("
            INSERT INTO `oryza_datares` (
                `dataset_id`, `runnum`, `day`, `dvs`)
            VALUES (%s, %s, %s, %s)
            ON DUPLICATE KEY UPDATE
                `dvs` = %s",
            $dataset['id'],
            $runnum,
            $day,
            $dvs,
            $dvs
        );

        $this->db->query($sql);
        
        return $sql;
    }    
    
    /**
     * @todo : documentation
     * @param type $country
     * @param type $station
     * @param type $year
     * @param type $wtype
     * @param type $fert
     * @param type $variety
     * @param type $geolat
     * @return type 
     */
    public function getYield($country, $station, $year, $wtype, $fert, $variety, $geolat)
    {
        $start_date = $year.'-01-01';
        $end_date = $year.'-12-31';
        if($geolat<0)
        {
            $start_date = $year.'-07-01';
            $end_date = ($year+1).'-06-30';
        }
        
        $sql = sprintf("
            SELECT `dataset_id`, `runnum`, `observe_date`, `yield`
            FROM `oryza_dataset` AS a
            INNER JOIN `oryza_data` AS b
                ON a.`id` = b.`dataset_id`
            WHERE `country_code` = '%s'
                AND a.`station_id` = %s
                AND b.`observe_date` BETWEEN '%s' AND '%s'
                AND a.`wtype` = '%s'
                AND a.`fert` = %s 
                AND a.`variety` = '%s'
            ORDER BY `observe_date`",
            mysql_real_escape_string($country),
            intval($station),
            $start_date, $end_date,
            mysql_real_escape_string($wtype),
            intval($fert), mysql_real_escape_string($variety) );
// echo $sql;
        return $this->db->getRowList($sql);
    }
    
    /**
     * @todo : documentation
     * @param type $datasets 
     */
    private function cleanAfterLoad($datasets)
    {
        foreach($datasets as $dset)
        {
            $sql = sprintf("SELECT 1 as exist FROM `oryza_data` WHERE `dataset_id` = %d LIMIT 1",$dset['id']);
            if(!$this->db->getRow($sql))
            {
                $sql2 = sprintf("DELETE FROM `oryza_dataset` WHERE `id` = %d",$dset['id']);
                $this->db->query($sql2);
            }
        }
    }   
    
    /**
     * @todo : documentation
     * @param type $country
     * @param type $station
     * @param type $year
     * @return type 
     */
    public function getVarieties($country,$station,$year)
    {
        $sql = "
            SELECT DISTINCT `variety`
            FROM `oryza_dataset`
            WHERE country_code = '{$country}'
                AND station_id = {$station}
                AND year = {$year}";
        $rs = $this->db->getRowList($sql);
        $rs2 = array();
        foreach($rs as $rec)
        {
            $tmp = explode('.',$rec->variety);
            $rs2[] = array($rec->variety,$tmp[0]);
        }
        return $rs2;
    }
    
    public function getEmergenceLimit($dataset_id,$runnum,$observe_date)
    {
        $sql = "
            SELECT MIN(`day`) AS min_day, MAX(`day`) AS max_day
            FROM `oryza_datares`
            WHERE `dataset_id` = {$dataset_id}
                AND `runnum` = {$runnum}
                AND dvs < 1";
        $rs = $this->db->getRow($sql);
        if ($rs)
        {
            $date = DateTime::createFromFormat('Y-m-d', $observe_date);
            $date->modify('+'.($rs->max_day - $rs->min_day).' day');
            return $date->format('U');
        }
        return false;
    }
}
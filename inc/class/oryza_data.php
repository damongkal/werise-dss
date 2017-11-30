<?php

class oryza_data
{

    private $db;
    private $debug;

    /**
     * @todo : documentation
     */
    public function __construct()
    {
        $this->db = Database_MySQL::getInstance();
        $this->debug = debug::getInstance();
    }

    /**
     * @todo : documentation
     * @param type $file
     * @param type $wtype
     * @return type
     */
    public function load(oryza2000_api $oryza2000) {

        $debug = array();

        // save dataset to DB
        $datasets = $oryza2000->getDatasets();
        foreach ($datasets as $key => $dset) {
            $dataset_id = $this->initDataSet($dset);
            $datasets[$key]['id'] = $dataset_id;
            $dset['id'] = $dataset_id;
            unset($dset['sttimes']);
            unset($dset['ferts']);
            $debug['dataset'][] = $dset;
        }

        // load op.dat
        $debug['db_op'] = $this->loadOutput($oryza2000,$datasets);
        // load res.dat
        $debug['db_res'] = $this->loadRes($oryza2000,$datasets);
        // clean
        $this->cleanAfterLoad($datasets);

        return $debug;
    }

    /**
     * read Oryza2000 op.dat for data loading
     * @param type $file
     * @param type $type
     * @return type
     */
    private function loadOutput(oryza2000_api $oryza2000,$datasets) {
        $debug = array();

        $cols = array('RUNNUM','WRR14');        
        $columns = oryza2000_api::getVariableIndex('op.dat', $cols, null);        
        $this->debug->addLog($columns,true,'op.dat COLUMNS');
        
        $file = $oryza2000->getOpFileName();
        $debug[] = 'file = ' . $file;
        $handle = werise_core_files::getHandle($file);        
        while (($buffer = fgets($handle, 4096)) !== false) {
            $this->debug->addLog('LINE: '.$buffer,false,'op.dat');
            
            // parse column titles
            if (strpos($buffer,'WRR14')!=false)
            {
                $columns = oryza2000_api::getVariableIndex('op.dat', $cols, $this->getColumnIndex($buffer));
                $this->debug->addLog($columns,true,'op.dat COLUMNS');
            }
            
            $vars = datafiles::validate2($buffer);
            $this->debug->addLog($vars,true,'op.dat VARS');
            if ($vars) {
                $this->addData($datasets, $vars, $columns);
                if (_opt(sysoptions::_ADM_SHOW_LOAD_ORYZA_DETAIL)) {
                    $debug[] = $vars;
                }
            }
        }
        if (!feof($handle)) {
            throw new Exception('unexpected fgets() fail');
        }

        fclose($handle);

        return $debug;
    }
    
    private function getColumnIndex($buffer) {
        $columns = explode(' ', $buffer);
        if (count($columns)==1)
        {
            $columns = explode("\t", $buffer);
        }
        $columns2 = array();
        $idx = 0;
        foreach ($columns as $c) {
            $tmp = trim($c);
            if ($tmp!='')
            {
                $columns2[$tmp] = $idx++;
            }
        }
        $this->debug->addLog($columns2,true,'COLUMNS');
        return $columns2;
    }

    /**
     * read Oryza2000 res.dat for data loading
     * @param type $file
     * @param type $type
     * @return type
     */
    private function loadRes(oryza2000_api $oryza2000, $datasets) {
        $debug = array();        
        
        // get column index
        $cols = array('TIME','DVS','ZW','DOY');
        $outfile = 'res.dat fert0';
        if ($datasets[0]['fert']==1)
        {
            $outfile = 'res.dat fert1';
        }            
        $columns = oryza2000_api::getVariableIndex($outfile, $cols, null);
        $this->debug->addLog($columns,true,'res.dat COLUMNS');
        
        $file = $oryza2000->getResFileName();
        $debug[] = 'file = ' . $file;
        $handle = werise_core_files::getHandle($file);        
        while (($buffer = fgets($handle, 4096)) !== false) {
            $this->debug->addLog('LINE: '.$buffer,false,'res.dat');
            
            // parse column titles
            if (strpos($buffer,'TIME')!==false)
            {
                $columns = oryza2000_api::getVariableIndex($outfile, $cols, $this->getColumnIndex($buffer));
                $this->debug->addLog($columns,true,'res.dat COLUMNS');
            }            
            
            $vars = datafiles::validate3($buffer);
            $this->debug->addLog($vars,true,'res.dat VAR');
            
            if ($vars) {
                if ($vars[0] == 'RUNNUM') {
                    // determine dataset from runnum
                    $runnum = $vars[1];
                    $id = floor(($runnum - 1) / 24);
                } else {
                    $this->addDataRes($datasets[$id], $runnum, $vars, $columns);
                }
            }
        }
        if (!feof($handle)) {
            throw new Exception('unexpected fgets() fail\n');
        }

        fclose($handle);
        return $debug;
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
        $country = $this->db->escape($dataset['country']);
        $station_id = intval($dataset['station']);
        $year = intval($dataset['year']);

        $specific_set = '';
        if ($variety!='')
        {
            $specific_set = "AND `fert` = {$fert} AND `variety` = '{$variety}'";
        }
        $sql = "
            SELECT `id`,`upload_date`
            FROM "._DB_DATA.".`oryza_dataset`
            WHERE `country_code` = '{$country}'
                AND `station_id` = {$station_id}
                AND `year` = '{$year}'
                AND `wtype` = '{$wtype}'
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
            $this->deleteDataSet($id);
        }
        else
        {
            $sql = sprintf("
                INSERT INTO "._DB_DATA.".`oryza_dataset` (
                    `country_code`,`station_id`, `year`,
                    `wtype`, `variety`, `fert`, `oryza_ver`, `upload_date`)
                VALUES (
                    '%s', %u, %u, '%s', '%s', %u, %u, CURDATE())",
                    $dataset['country'], $dataset['station'],
                    $dataset['year'], $dataset['wtype'],
                    $dataset['variety'], $dataset['fert'],
                    _opt(sysoptions::_ORYZA_VERSION));

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
    private function addData($dset, $data, $colref)
    {
        // op.dat columns
        $runnum = $data[$colref['RUNNUM']]; // runnum        
        $wrr14 = $data[$colref['WRR14']] / 1000; // yield (g/ha) OLD ORYZA
        
        // determine dataset from runnum
        $id = floor(($runnum - 1) / 24);        
        $dataset = $dset[$id];

        // sttime
        $sttime = $dataset['sttimes'][$runnum];
        $fert = is_array($dataset['ferts'][$runnum]) ? implode(',',($dataset['ferts'][$runnum])) : '0';
        $date = DateTime::createFromFormat('Y-m-d', ($dataset['year']-1).'-12-31');
        $date->add(new DateInterval("P{$sttime}D"));

        $sql = sprintf("
            INSERT INTO "._DB_DATA.".`oryza_data` (
                `dataset_id`, `runnum`, `observe_date`, `yield`, `fert`)
            VALUES (%s, %s, '%s', %s, '%s')",
            $dataset['id'],
            $runnum,
            date_format($date, 'Y-m-d'),
            $wrr14,
            $fert
        );

        $this->db->query($sql);

        return $data;
    }

    /**
     * @todo : documentation
     * @param type $dataset
     * @param type $data
     */
    private function addDataRes($dataset, $runnum, $data, $colref)
    {
        // res.dat columns
        $day = $data[$colref['TIME']];
        $dvs = isset($data[$colref['DVS']]) ? $data[$colref['DVS']] : 'NULL';
        if ($dvs>3)
        {
            throw new Exception('DVS is out of range: '.$dvs);
        }
        $zw = isset($data[$colref['ZW']]) ? $data[$colref['ZW']] : 'NULL';
        if ($zw=='-')
        {
            $zw = 'NULL';
        }
        $doy = isset($data[$colref['DOY']]) ? $data[$colref['DOY']] : 'NULL';

        $sql = sprintf('
            INSERT INTO '._DB_DATA.'.`oryza_datares` (
                `dataset_id`, `runnum`, `day`, `dvs`, `zw`, `doy`)
            VALUES (%1$d, %2$d, %3$s, %4$s, %5$s, %6$d)
            ON DUPLICATE KEY UPDATE
                `dvs` = %4$s,
                `zw` = %5$s,
                `doy` = %6$d',
            $dataset['id'], $runnum, $day, $dvs, $zw, $doy
        );

        $this->db->query($sql);

        return $sql;
    }

    public function deleteDataSet($setid, $update_mode=true)
    {
        $sql = "DELETE FROM "._DB_DATA.".`oryza_data` WHERE `dataset_id`={$setid}";
        $this->db->query($sql);

        $sql2 = "DELETE FROM "._DB_DATA.".`oryza_datares` WHERE `dataset_id`={$setid}";
        $this->db->query($sql2);

        if ($update_mode)
        {
            $sql3 = sprintf("UPDATE "._DB_DATA.".`oryza_dataset` SET `oryza_ver`=%u, `upload_date` = CURDATE() WHERE `id`= %u",_opt(sysoptions::_ORYZA_VERSION),$setid);
        } else
        {
            $sql3 = "DELETE FROM "._DB_DATA.".`oryza_dataset` WHERE `id` = {$setid}";
        }
        $this->db->query($sql3);
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
    public function getYield($station, $year, $wtype, $fert, $variety, $filters = array())
    {
        $extra = '';
        // filter: date period
        if(isset($filters['startdate']))
        {
            $start_date = $filters['startdate'];
            $end_date = $filters['enddate'];
        } else
        {
            $start_date = $year.'-01-01';
            $end_date = $year.'-12-31';
            if($station->geo_lat<0)
            {
                $start_date = $year.'-07-01';
                $end_date = ($year+1).'-06-30';
            }
        }
        $extra .= "AND b.`observe_date` BETWEEN '{$start_date}' AND '{$end_date}'";
        // filter: runnum
        if(isset($filters['runnum']))
        {
            $extra = $extra." AND b.`runnum` = {$runnum}";
        }

        $sql = sprintf("
            SELECT b.*
            FROM "._DB_DATA.".`oryza_dataset` AS a
            INNER JOIN "._DB_DATA.".`oryza_data` AS b
                ON a.`id` = b.`dataset_id`
            WHERE `country_code` = '%s'
                AND a.`station_id` = %s
                AND a.`wtype` = '%s'
                AND a.`fert` = %s
                AND a.`variety` = '%s'
                %s
            ORDER BY `observe_date`",
            $station->country_code,
            $station->station_id,
            $this->db->escape($wtype),
            intval($fert), $this->db->escape($variety) , $extra);

        if(isset($filters['runnum']))    
        {
            return $this->db->getRow($sql);
        } else
        {
            return $this->db->getRowList($sql);
        }
    }

    /**
     * @todo : documentation
     * @param type $datasets
     */
    private function cleanAfterLoad($datasets)
    {
        foreach($datasets as $dset)
        {
            $sql = sprintf("SELECT 1 as exist FROM "._DB_DATA.".`oryza_data` WHERE `dataset_id` = %d LIMIT 1",$dset['id']);
            if(!$this->db->getRow($sql))
            {
                $sql2 = sprintf("DELETE FROM "._DB_DATA.".`oryza_dataset` WHERE `id` = %d",$dset['id']);
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
    public function getVarieties($country,$station,$year,$wtype='r')
    {
        $sql = "
            SELECT DISTINCT ds.`variety`, v.*
            FROM "._DB_DATA.".`oryza_dataset` AS ds
            INNER JOIN `varieties` AS v ON ds.`variety` = v.`variety_code`
            WHERE ds.`country_code` = '{$country}'
                AND ds.`station_id` = {$station}
                AND ds.`year` = {$year}
                AND ds.`wtype` = '{$wtype}'";
        $rs = $this->db->getRowList($sql);
        return $rs;
    }
    
    /**
     * 
     * @param type $dataset
     * @param type $wtype
     * @return boolean
     */
    public static function getDatasets($dataset)
    {
        $db = Database_MySQL::getInstance();
        $where = array();
        if (isset($dataset['wtype']))
        {
            $where[] = sprintf("a.`wtype` = '%s'",$db->escape($dataset['wtype']));
        }
        if (isset($dataset['id']))
        {
            $where[] = sprintf("`id` = %u",intval($dataset['id']));
        }        
        if (isset($dataset['country']))
        {
            $where[] = sprintf("`country_code` = '%s'",$db->escape($dataset['country']));
        }
        if (isset($dataset['station']))
        {
            $where[] = sprintf("`station_id` = %u",intval($dataset['station']));
        }
        if (isset($dataset['year']))
        {
            $where[] = sprintf("`year` = %u",intval($dataset['year']));
        }
        $where_clause = Database_MySQL::getWhere($where);
        
        $sql = "
            SELECT a.* , c.is_disabled
            FROM "._DB_DATA.".`oryza_dataset` AS a
            LEFT JOIN "._DB_DATA.".`oryza_dataset_display` AS c
                ON a.id = c.dataset_id
            {$where_clause}     
            ORDER BY `country_code`, `station_id`, `wtype`, `year`";
        return $db->getRowList($sql);
    }    
    
    /**
     * 
     * @param type $dataset
     * @param type $wtype
     * @return boolean
     */
    public static function getDatasetRecords($filter)
    {
        $db = Database_MySQL::getInstance();
        $where = array();
        if (isset($filter['id']))
        {
            $where[] = sprintf("`dataset_id` = %u",$db->escape($filter['id']));
        }
        $where_clause = Database_MySQL::getWhere($where);

        $sql = "SELECT * FROM "._DB_DATA.".`oryza_data` {$where_clause} ORDER BY `observe_date`";
        return $db->getRowList($sql);
    }      

    /**
     * @todo : documentation
     * @param type $wtype
     */
    public static function getAllDatasets($wtype=null)
    {
        $db = Database_MySQL::getInstance();
        if (is_null($wtype))
        {
            $sql = "
                SELECT DISTINCT a.`country_code`, a.`station_id`, b.`station_name`
                FROM "._DB_DATA.".`oryza_dataset` AS a
                INNER JOIN `weather_stations` AS b
                        ON a.`country_code` = b.`country_code`
                        AND a.`station_id` = b.`station_id`";
            return $db->getRowList($sql);
        } else
        {
            return self::getDatasets(array('wtype'=>$wtype));
            /*
            $sql = "
                SELECT a.* , c.is_disabled
                FROM "._DB_DATA.".`oryza_dataset` AS a
                LEFT JOIN "._DB_DATA.".`oryza_dataset_display` AS c
                        ON a.id = c.dataset_id
                WHERE a.wtype='{$wtype}'
                ORDER BY `country_code`, `station_id`, `wtype`, `year`";*/
        }
    }
    
    public function getMaxYield($station)
    {
        $sql = "
            SELECT MAX(`yield`) AS maxyld 
            FROM "._DB_DATA.".`oryza_data` AS a
            INNER JOIN "._DB_DATA.".`oryza_dataset` AS b
                ON a.dataset_id = b.id
            WHERE b.country_code = '{$station->country_code}'
            AND b.station_id = {$station->station_id}";
        $rs = $this->db->getRow($sql);
        return intval($rs->maxyld)+1;
    }
}
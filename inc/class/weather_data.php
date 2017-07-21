<?php
class weather_data
{
    private $db;
    private $debug;

    public function __construct()
    {
        $this->db = Database_MySQL::getInstance();
        $this->debug = debug::getInstance();
    }

    public function load($file, $type) {
        $debug = array();

        $f = werise_weather_file::getFileName($type, $file);
        $debug['file'] = $f;
        $handle = werise_core_files::getHandle($f);

        // create ver3 file if needed
        $v3file = werise_weather_filev3::createFile($file, $type, $handle);
        $debug['v3file'] = $v3file;

        rewind($handle);

        // inititalize dataset
        $datafile = new datafiles;
        $dataset = $datafile->getDatasetFromFilename($file);
        if ($dataset['error'] != '') {
            throw new Exception($dataset['error']);
        }
        $setid = $this->initDataSet($dataset, $type);
        $debug['dataset'] = $dataset;
        $debug['dataset']['id'] = $setid;

        // loop thru the weather records
        $comments = array();
        while (($buffer = fgets($handle, 4096)) !== false) {
            $this->debug->addLog($buffer,false,'LINE');
            // compile the comments
            if (strpos($buffer,'*')===0)
            {
                $comments[] = utf8_encode($buffer);
            }
            $vars = datafiles::validate($buffer);
            $this->debug->addLog($vars ,true,'VARS');
            if ($vars) {
                $rs = $this->addData($setid, $vars);
                if (_opt(sysoptions::_ADM_SHOW_LOAD_WEATHER_DETAIL)) {
                    $debug['db'][] = $rs;
                }
            }
        }
        
        // store the comments
        $this->storeComments($setid,$comments);

        // error! eof not reached.
        if (!feof($handle)) {
            $this->deleteDataSet($setid, $dataset, $type, false);
            throw new Exception("unexpected fgets() fail\n");
        }
        fclose($handle);

        // detect sunshine duration
        $this->moveSunshineDuration($setid);

        // check missing data
        $missing = $this->checkMissing($dataset, $type);
        if ($missing)
        {
            $this->addNotes($setid, $missing);
        }

        return $debug;
    }

    /**
     *
     * @param class $station db row instance of station
     * @param type $year
     * @param type $wtype
     * @param type $wvar
     * @param type $get_raw
     * @return type
     */
    public function getDecadal($station, $year, $wtype, $wvar, $opts = false)
    {
        // start date filter
        if (isset($opts['start_date']))
        {
            $start_date = $opts['start_date'];
        } else
        {
            $start_date = $year.'-01-01';
            if($station->geo_lat<0)
            {
                $start_date = $year.'-07-01';
            }
        }
        // end date filter
        if (isset($opts['end_date']))
        {
            $end_date = $opts['end_date'];
        } else
        {
            $end_date = $year.'-12-31';
            if($station->geo_lat<0)
            {
                $end_date = ($year+1).'-06-30';
            }
        }
        // get column name
        $col = werise_weather_properties::getColumnName($wvar);
        $opr = 'SUM';
        if ($wvar>0)
        {
            $opr = 'AVG';
        }
        // main SQL
        $sql = sprintf("
            SELECT a.`decadal`, FORMAT({$opr}(`%s`),1) AS wvar
            FROM "._DB_DATA.".`weather_data` AS a
            INNER JOIN "._DB_DATA.".`weather_dataset` AS b
                ON a.`dataset_id` = b.`id`
            WHERE b.`wtype` = '%s'
                AND b.`country_code` = '%s'
                AND b.`station_id` = %s
                AND a.`observe_date` BETWEEN '%s' AND '%s'
            GROUP BY a.`decadal`
            ORDER BY a.`observe_date`",
            $col,
            $this->db->escape($wtype),
            $this->db->escape($station->country_code),
            $station->station_id,
            $this->db->escape($start_date),
            $this->db->escape($end_date));

        $rs = $this->db->getRowList($sql);
        if (isset($opts['output_raw']))
        {
            return $rs;
        }

        // some data cleaning
        if ($rs)
        {
            $ret = array();
            $is_no_data = true;
            foreach ($rs as $rec)
            {
                $ret[] = $this->cleanVar($wvar,$rec->wvar);
                if (!is_null($rec))
                {
                    $is_no_data = false;
                }
            }

            if ($is_no_data)
            {
                return false;
            }

            return $ret;
        } else
        {
            return false;
        }
    }

    /**
     * get maximum value of variable
     * @param type $station
     * @param type $wtype
     * @param type $wvar
     * @return type
     */
    public function getDecadalMinMax($station, $wtype, $wvar)
    {
        $col = werise_weather_properties::getColumnName($wvar);
        $opr = 'SUM';
        if ($wvar>0)
        {
            $opr = 'AVG';
        }

        $sql = "
            SELECT a.`decadal` , b.`year`,
                {$opr}( `{$col}` ) AS wvar
            FROM "._DB_DATA.".`weather_data` AS a
            INNER JOIN "._DB_DATA.".`weather_dataset` AS b
                ON a.`dataset_id` = b.`id`
            WHERE b.`wtype` = '".$this->db->escape($wtype)."'
                AND b.`country_code` = '".$this->db->escape($station->country_code)."'
                AND b.`station_id` = {$station->station_id}
            GROUP BY a.`decadal` , b.`year`
            ORDER BY wvar DESC";

        $rs = $this->db->getRowList($sql);

        $min = 0;
        foreach($rs as $rec)
        {
            $raw[] = $rec->wvar;
            if (!is_null($rec->wvar))
            {
                $min = $rec->wvar;
            }
        }
        // compute max based on mean and standard deviation
        $n = count(array_filter($raw));
        if ($n==0)
        {
            $mean = 0;
            $std_dev = 0;
            $max = 0;
        } else {
            $mean = array_sum($raw) / $n;
            $std_dev = dss_utils::std_dev($raw);
            $max = $mean+$std_dev;
        }

        // special conversion for solar radiation
        if ($wvar==3)
        {
            $min = $min / 1000;
            $max = $max / 1000;
        }
        return array($min,$max);
    }

    private function addNotes($id,$notes)
    {
        $sql = sprintf("
            UPDATE "._DB_DATA.".`weather_dataset`
                SET `notes` = '%s'
            WHERE `id` = %d",
                $notes,
                $id);

        $this->db->query($sql);
    }

    /**
     *
     * @param type $dataset
     * @param type $wtype
     * @return boolean
     */
    public static function getDatasets($dataset,$wtype='r')
    {
        $db = Database_MySQL::getInstance();
        $where = array();
        if ($wtype!='')
        {
            $where[] = sprintf("`wtype` = '%s'",$db->escape($wtype));
        }
        if (isset($dataset['id']))
        {
            $where[] = sprintf("`id` = %u",$db->escape($dataset['id']));
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
            SELECT a.*, c.`is_disabled`
            FROM "._DB_DATA.".`weather_dataset` AS a
            LEFT JOIN "._DB_DATA.".`weather_dataset_display` AS c
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

        $sql = "SELECT * FROM "._DB_DATA.".`weather_data` {$where_clause} ORDER BY `observe_date`";
        return $db->getRowList($sql);
    }

    /**
     *
     * @param type $dataset
     * @param type $wtype
     * @return boolean
     */
    public static function getDatasetDecadal($filter,$wvar=null)
    {
        $db = Database_MySQL::getInstance();

        if (is_null($wvar))
        {
            $fields = "
                MIN(d.observe_date) AS observe_date,
                MAX(d.observe_date) AS observe_date2,
                SUM(d.rainfall) AS rainfall,
                AVG(d.min_temperature) AS min_temperature,
                AVG(d.max_temperature) AS max_temperature,
                AVG(d.irradiance) AS irradiance,
                AVG(d.sunshine_duration) AS sunshine_duration,
                AVG(d.vapor_pressure) AS vapor_pressure,
                AVG(d.mean_wind_speed) AS mean_wind_speed";
        } else
        {
            $col = werise_weather_properties::getColumnName($wvar);
            $opr = 'SUM';
            if ($wvar>0)
            {
                $opr = 'AVG';
            }
            $fields = "{$opr}( `{$col}` ) AS wvar";
        }

        $where = array();
        if (isset($filter['id']))
        {
            $where[] = sprintf("d.`dataset_id` = %u",$db->escape($filter['id']));
        }
        if (isset($filter['wtype']))
        {
            $where[] = sprintf("ds.`wtype` = '%s'",$db->escape($filter['wtype']));
        }
        if (isset($filter['country']))
        {
            $where[] = sprintf("ds.`country_code` = '%s'",$db->escape($filter['country']));
        }
        if (isset($filter['station']))
        {
            $where[] = sprintf("ds.`station_id` = %u",intval($filter['station']));
        }
        $where_clause = Database_MySQL::getWhere($where);

        $sql = "
            SELECT d.`decadal`,
                ds.`year`,
                {$fields}
            FROM "._DB_DATA.".`weather_data` AS  d
            INNER JOIN "._DB_DATA.".`weather_dataset` AS ds
                ON d.`dataset_id` = ds.`id`
                {$where_clause} GROUP BY d.`decadal`,ds.`year`";
        return $db->getRowList($sql);
    }

    private function initDataSet($dataset, $type)
    {
        $tmp = $this->getDatasets($dataset,$type);
        if ($tmp)
        {
            $setid = $tmp[0]->id;
            $this->deleteDataSet($setid, $dataset, $type);
        } else
        {
            $sql = sprintf("
                INSERT INTO "._DB_DATA.".`weather_dataset` (
                    `country_code`,`station_id`, `year`,
                    `wtype`, `oryza_ver`, `upload_date`)
                VALUES ('%s', %u, %u, '%s', %u, CURDATE())",
                    $dataset['country'],$dataset['station'],
                    $dataset['year'],$type,
                    _opt(sysoptions::_ORYZA_VERSION));

            $this->db->query($sql);
            $setid = $this->db->getInsertId();
        }

        return $setid;
    }

    private function addData($setid,$data)
    {
        $interval = date_interval_create_from_date_string($data[2].' days');
        $date = date_add( new DateTime(($data[1]-1).'-12-31') ,$interval);
        $d = date_format($date, 'd');
        $m = date_format($date, 'm');
        $decadal = 3;
        if ($d<=10)
        {
            $decadal = 1;
        }
        if ($d>10 && $d<=20 )
        {
            $decadal = 2;
        }

        // clean data
        $data[8] = datafiles::cleanVal($data[8]);
        $data[4] = datafiles::cleanVal($data[4]);
        $data[5] = datafiles::cleanVal($data[5]);
        $data[3] = datafiles::cleanVal($data[3]);
        $data[6] = datafiles::cleanVal($data[6]);
        $data[7] = datafiles::cleanVal($data[7]);

        $sql = sprintf("
            INSERT INTO "._DB_DATA.".`weather_data` (
                `dataset_id`, `observe_date`, `rainfall`,
                `min_temperature`, `max_temperature`, `irradiance`,
                `vapor_pressure`, `mean_wind_speed`, `decadal`)
            VALUES (%s, '%s', %s, %s, %s, %s, %s, %s, %s)",
                $setid,
                date_format($date, 'Y-m-d'),
                $data[8],
                $data[4],
                $data[5],
                $data[3],
                $data[6],
                $data[7],
                (($m * 10) + $decadal)
                );

        $this->db->query($sql);

        return $data;
    }

    /**
     *
     * @param type $setid
     * @param type $dataset
     * @param type $type
     * @param type $update_mode
     */
    public function deleteDataSet($setid, $dataset, $type, $update_mode=true)
    {
        $sql = "DELETE FROM "._DB_DATA.".`weather_data` WHERE `dataset_id`= {$setid}";
        $this->db->query($sql);
        $sql4 = "DELETE FROM "._DB_DATA.".`weather_headers` WHERE `dataset_id`= {$setid}";
        $this->db->query($sql4);        

        if ($update_mode)
        {
            $sql2 = sprintf("UPDATE "._DB_DATA.".`weather_dataset` SET `oryza_ver` = %u, `upload_date` = CURDATE() WHERE `id`= %u",_opt(sysoptions::_ORYZA_VERSION),$setid);
        } else
        {
            $sql2 = "DELETE FROM "._DB_DATA.".`weather_dataset` WHERE `id` = {$setid}";
        }
        $this->db->query($sql2);

        $cache_id = 'ptile-'.$dataset['country'].'-'.$dataset['station'].'-'.$type;
        $sql3 = "DELETE FROM "._DB_DATA.".`cache` WHERE `keyid` LIKE '{$cache_id}%'";
        $this->db->query($sql3);
    }

    private function moveSunshineDuration($id)
    {
        $sql = "
            SELECT MAX(`irradiance`) AS max
            FROM "._DB_DATA.".`weather_data` AS a
            WHERE `dataset_id` = {$id}";
        $rs = $this->db->getRow($sql);
        if ($rs->max < 100)
        {
            $sql2 = "
                UPDATE "._DB_DATA.".`weather_data`
                    SET `sunshine_duration` = `irradiance`
                WHERE `dataset_id` = {$id}";
            $this->db->query($sql2);

            $sql3 = "
                UPDATE "._DB_DATA.".`weather_data`
                    SET `irradiance` = NULL
                WHERE `dataset_id` = {$id}";
            $this->db->query($sql3);
        }
    }

    public static function cleanVar($var_id, $value)
    {
        if (is_null($value))
        {
            return null;
        }
        else
        {
            $val = floatval(str_replace(',', '', $value));
            // special conversion for solar radiation
            if ($var_id == 3)
            {
                $val = $val / 1000;
            }
            return number_format($val,1,'.','')+0;
        }
    }

    /**
     * check if there is missing rainfall or solar radiation
     * @param array $dataset
     * @param type $wtype
     * @return type
     */
    public function checkMissing($dataset, $wtype, $day_limit = 365)
    {
        $day_limit_filter = '';
        if ($day_limit<365)
        {
            $day_limit_filter = sprintf(" AND `observe_date` < DATE_ADD('%d-01-01', INTERVAL %d DAY)",$dataset['year'],$day_limit);
        }
        $sql = "
            SELECT `observe_date`
            FROM "._DB_DATA.".`weather_dataset` AS a
            INNER JOIN "._DB_DATA.".`weather_data` AS b ON a.`id` = b.`dataset_id`
            WHERE a.`country_code` = '%s'
                AND a.`station_id` = %d
                AND a.`year` = %d
                AND a.`wtype` = '%s'
                AND %s IS NULL
                {$day_limit_filter}
            LIMIT 1";

        // missing rainfall
        $sql2 = sprintf($sql,
            $dataset['country'],
            $dataset['station'],
            $dataset['year'],
            $wtype,
            'b.`rainfall`');
        $rs2 = $this->db->getRow($sql2);
        if ($rs2)
        {
            return 'Missing rainfall data on ' . $rs2->observe_date;
        }

        // missing solar radiation
        $sql3 = sprintf($sql,
            $dataset['country'],
            $dataset['station'],
            $dataset['year'],
            $wtype,
            'b.`irradiance`');
        $rs3 = $this->db->getRow($sql3);

        // missing sunshine duration
        $sql7 = sprintf($sql,
            $dataset['country'],
            $dataset['station'],
            $dataset['year'],
            $wtype,
            'b.`sunshine_duration`');
        $rs7 = $this->db->getRow($sql7);
        if ($rs3 && $rs7)
        {
            if ($rs3->observe_date > $rs7->observe_date)
            {
                return 'Missing solar radiation data on ' . $rs3->observe_date;
            } else {
                return 'Missing sunshine duration data on ' . $rs7->observe_date;
            }
        }

        $sql4 = "
            SELECT COUNT(observe_date) AS cnt
            FROM "._DB_DATA.".`weather_dataset` AS a
            INNER JOIN "._DB_DATA.".`weather_data` AS b ON a.`id` = b.`dataset_id`
            WHERE a.`country_code` = '%s'
                AND a.`station_id` = %d
                AND a.`year` = %d
                AND a.`wtype` = '%s'";
        $sql5 = sprintf($sql4,
            $dataset['country'],
            $dataset['station'],
            $dataset['year'],
            $wtype);
        $rs4 = $this->db->getRow($sql5);
        if ($rs4->cnt < 365)
        {
            return 'Incomplete observed days for '.$dataset['year'].'. Only ' . $rs4->cnt . ' are recorded.';
        }

        return false;
    }

    public static function getWvarList($country_code, $station_id, $year, $wtype)
    {
        $db = Database_MySQL::getInstance();

        $sql = "
            SELECT
                    SUM( IF( a.`rainfall` IS NOT NULL , 1, 0 ) ) AS cnt_rainfall,
                    SUM( IF( a.`min_temperature` IS NOT NULL , 1, 0 ) ) AS cnt_temperature,
                    SUM( IF( a.`irradiance` IS NOT NULL , 1, 0 ) ) AS cnt_irradiance,
                    SUM( IF( a.`sunshine_duration` IS NOT NULL , 1, 0 ) ) AS cnt_sunshine,
                    SUM( IF( a.`vapor_pressure` IS NOT NULL , 1, 0 ) ) AS cnt_vapor,
                    SUM( IF( a.`mean_wind_speed` IS NOT NULL , 1, 0 ) ) AS cnt_wind
            FROM "._DB_DATA.".`weather_data` AS a
            INNER JOIN "._DB_DATA.".`weather_dataset` AS b
                ON a.`dataset_id` = b.`id`
            WHERE b.`country_code` = '%s'
                AND b.`station_id` = %d
                AND b.`year` = %d
                AND b.`wtype` = '%s'";

        $sql2 = sprintf($sql,
            $db->escape($country_code),
            intval($station_id),
            intval($year),
            $db->escape($wtype));
        $rs = $db->getRow($sql2);

        if (is_null($rs->cnt_rainfall))
        {
            return array();
        }

        if ($rs->cnt_rainfall>0)
        {
            $wvar[] = array( 'wvar_id' => 0, 'wvar_name' => _t('Rainfall'));
        }
        if ($rs->cnt_temperature>0)
        {
            $wvar[] = array('wvar_id' => 1, 'wvar_name' => _t('Temperature'));
        }
        if ($rs->cnt_irradiance>0)
        {
            $wvar[] = array('wvar_id' => 3, 'wvar_name' => _t('Solar Radiation'));
        }
        if ($rs->cnt_sunshine>0)
        {
            $wvar[] = array('wvar_id' => 6, 'wvar_name' => _t('Sunshine Duration'));
        }
        if ($rs->cnt_vapor>0)
        {
            $wvar[] = array('wvar_id' => 4, 'wvar_name' => _t('Early morning vapor pressure'));
        }
        if ($rs->cnt_wind>0)
        {
            $wvar[] = array('wvar_id' => 5, 'wvar_name' => _t('Wind Speed'));
        }

        return $wvar;
    }
    
    private function storeComments($setid,$comments)
    {
        $headers = array('STATIONNAME','AUTHOR','AUTHORS','SOURCE','SOURCES','COMMENT','COMMENTS','YEAR');
        $last_header = '';
        $comments2 = array();
        foreach($comments as $comment)
        {
            $ctmp1 = trim(str_replace('*','',$comment));
            $ctmp2 = explode(':',$ctmp1);
            $tmp_header = trim($ctmp2[0]);
            $tmp_value = trim($ctmp1);
            $line_done = false;
            // "column" section
            if (strpos($tmp_header,'Column')===0)
            {
                $last_header = 'Column';
                $tmp_value = '';
                $line_done = true;
            }
            // geo headers
            if (!$line_done && strpos($ctmp1,'Longitude')!==false)
            {
                $geo_headers = array('Coordinates','Longitude','Latitude','Altitude');
                $geotmp1 = $tmp_value;
                foreach($geo_headers as $gh)
                {
                    $geotmp1 = str_ireplace($gh,'+++'.$gh,$geotmp1);
                }
                $geotmp4 = explode('+++',$geotmp1);
                foreach($geotmp4 as $geohead)
                {
                    $geotmp5 = explode(':',$geohead);
                    if (isset($geotmp5[1]) && trim($geotmp5[0])!=$geo_headers[0])
                    {
                        $tmp_header = strtoupper(trim($geotmp5[0]));
                        $comments2[$tmp_header][] = trim($geotmp5[1]);
                    }
                }
                $tmp_value = '';
                $line_done = true;
            }
            // regular header
            if (!$line_done && isset($ctmp2[1]))
            {
                $tmp_header1 = preg_replace("/[^a-zA-Z]+/", "", $tmp_header);
                $tmp_header2 = str_replace(' ','',strtoupper($tmp_header1));
                $tmp_header = $tmp_header2;
                if (in_array($tmp_header,$headers))
                {
                    $last_header = $tmp_header;
                    $tmp_value = trim($ctmp2[1]);
                }
            }

            if ($last_header!=='' && $tmp_value!=='')
            {
                $comments2[$last_header][] = $tmp_value;
            }
        }
        //dbg($comments2);
        //die();
        foreach($comments2 as $header => $headerval)
        {
            $this->addComments($setid, $header, $headerval);
        }
    }
    
    private function addComments($setid,$header,$val)
    {
        $sql = "
            INSERT INTO "._DB_DATA.".`weather_headers`
            (`dataset_id`, `header_field`, `header_value`)
            VALUES(%u,'%s','%s')";
        $sql2 = sprintf($sql,$setid,$this->db->escape($header),$this->db->escape(implode("\n",$val)));
        $this->db->query($sql2);    
    }
    
    public static function getComments($dataset,$wtype)
    {
        $db = Database_MySQL::getInstance();
        $sql = "SELECT * FROM "._DB_DATA.".`weather_headers`";       
        $rs = $db->getRowList($sql);        
        $rs2 = array();
        foreach($rs as $rec)
        {
            $setid = $rec->dataset_id;
            $field = $rec->header_field;
            $val = $rec->header_value;
            $rs2[$setid][$field] = $val;
        }
        return $rs2;
    }
}
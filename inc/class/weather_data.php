<?php
class weather_data
{
    private $db;
    private $datafile;
    const _REALTIME = 'r';
    const _FORECAST = 'f';
    
    public function __construct()
    {
        $this->db = Database_MySQL::getInstance();
        $this->datafile = new datafiles;
    }

    public function load($file,$type)
    {
        $error = '';
        $debug = array();
        
        if ($type==self::_REALTIME)
        {
            $subdir = _DATA_SUBDIR_WEATHER_REALTIME;
        }
        if ($type==self::_FORECAST)
        {
            $subdir = _DATA_SUBDIR_WEATHER_FORECAST;
        }           
        
        $handle = @fopen(_DATA_DIR.$subdir.$file, "r");
        if ($handle)
        {
            $dataset = $this->datafile->getDatasetFromFilename($file);
            $debug['dataset'] = $dataset;

            if ($dataset['error']!='')
            {
                $error = $dataset['error'];
            } else
            {
                $setid = $this->initDataSet($dataset,$type);

                while (($buffer = fgets($handle, 4096)) !== false)
                {
                    $vars = $this->datafile->validate($buffer);
                    if ($vars)
                    {
                        $rs = $this->addData($setid,$vars);
                        $debug['db'][] = $rs;
                    }
                }
                if (!feof($handle))
                {
                    $error = "unexpected fgets() fail\n";
                }
            }
            fclose($handle);
            
        } else
        {
            $error = 'file does not exist.';
        }
        
        if ($error!='')
        {
            return array(false,$error);
        } else
        {
            return array(true,$debug);
        }
    }
    
    public function getDecadal($country, $station_id, $year, $wtype, $wvar, $get_raw = false, $geolat = 0)
    {
        $start_date = $year.'-01-01';
        $end_date = $year.'-12-31';
        if($geolat<0)
        {
            $start_date = $year.'-07-01';
            $end_date = ($year+1).'-06-30';
        }
        
        $col = $this->getColumnName($wvar);
        $opr = 'SUM';
        if ($wvar>0)
        {
            $opr = 'AVG';
        }
        
        $sql = sprintf("
            SELECT a.`decadal`, FORMAT({$opr}(`%s`),1) AS wvar
            FROM `weather_data` AS a
            INNER JOIN `weather_dataset` AS b
                ON a.`dataset_id` = b.`id`
            WHERE b.`wtype` = '%s'
                AND b.`country_code` = '%s'
                AND b.`station_id` = %s
                AND a.`observe_date` BETWEEN '%s' AND '%s'
            GROUP BY a.`decadal`
            ORDER BY a.`observe_date`",
            $col, 
            mysql_real_escape_string($wtype),    
            mysql_real_escape_string($country), $station_id, $start_date, $end_date);
 //echo $sql;
        $rs = $this->db->getRowList($sql);
        if ($get_raw)
        {
            return $rs;
        }

        // some data cleaning
        $ret = false;
        if ($rs)
        {
            foreach ($rs as $rec)
            {
                $ret[] = $this->cleanVar($wvar,$rec->wvar);
            }
        }
        return $ret;
    }
    
    public function getDecadalAll($country, $station_id, $wtype, $wvar)
    {
        $col = $this->getColumnName($wvar);
        $opr = 'SUM';
        if ($wvar>0)
        {
            $opr = 'AVG';
        }        
        
        $sql = "
            SELECT a.`decadal` , 
                b.`year`, 
                {$opr}( `{$col}` ) AS wvar
            FROM `weather_data` AS a
            INNER JOIN `weather_dataset` AS b
                ON a.`dataset_id` = b.`id`
            WHERE b.`wtype` = '".mysql_real_escape_string($wtype)."'
                AND b.`country_code` = '".mysql_real_escape_string($country)."'
                AND b.`station_id` = {$station_id}
                AND b.wtype = 'r'
            GROUP BY a.`decadal` , b.`year`";
//echo $sql;
        return $this->db->getRowList($sql);
    }
    
    public function isLoaded($dataset,$wtype='r')
    {
        $country = $dataset['country'];
        $station_id = $dataset['station'];
        $year = $dataset['year'];
        
        $sql = "
            SELECT `id`,`upload_date`
            FROM `weather_dataset`
            WHERE `wtype` = '".mysql_real_escape_string($wtype)."'
                AND `country_code` = '".mysql_real_escape_string($country)."'
                AND `station_id` = {$station_id}
                AND `year` = {$year}
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
    
    private function initDataSet($dataset, $type)
    {
        $tmp = $this->isLoaded($dataset,$type);
        
        if ($tmp)
        {
            $setid = $tmp->id;            
            $sql = "UPDATE `weather_dataset` SET upload_date = CURDATE() WHERE `id`={$setid}";
            $this->db->query($sql);
            
            $sql = "DELETE FROM weather_data WHERE `dataset_id`={$setid}";
            $this->db->query($sql);

            $cache_id = 'ptile-'.$dataset['country'].'-'.$dataset['station'].'-'.$type;        

            $sql2 = "DELETE FROM `cache` WHERE `keyid` LIKE '{$cache_id}%'";
            $this->db->query($sql2);
        } else
        {   
            $sql = sprintf("
                INSERT INTO `weather_dataset` (
                    `country_code`,`station_id`, `year`, 
                    `wtype`, `upload_date`) 
                VALUES ('%s', '%s', '%s', '%s', CURDATE())",
                    $dataset['country'],
                    $dataset['station'],
                    $dataset['year'],
                    $type);

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
        $data[8] = $this->datafile->cleanVal($data[8]);
        $data[4] = $this->datafile->cleanVal($data[4]);
        $data[5] = $this->datafile->cleanVal($data[5]);
        $data[3] = $this->datafile->cleanVal($data[3]);
        $data[6] = $this->datafile->cleanVal($data[6]);
        $data[7] = $this->datafile->cleanVal($data[7]);        
        
        $sql = sprintf("
            INSERT INTO `weather_data` (
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
    
    public function getAvailableFiles($type)
    {
        if ($type==self::_REALTIME)
        {
            return $this->datafile->getAvailableFiles(_DATA_SUBDIR_WEATHER_REALTIME);
        }
        if ($type==self::_FORECAST)
        {
            return $this->datafile->getAvailableFiles(_DATA_SUBDIR_WEATHER_FORECAST);
        }        
        
    }
    
    private function getColumnName($wvar)
    {
        switch ($wvar)
        {
            case 1:
                return 'min_temperature';
                break;
            case 2:
                return 'max_temperature';
                break;            
            case 3:
                return 'irradiance';
                break;
            case 4:
                return 'vapor_pressure';
                break;
            case 5:
                return 'mean_wind_speed';
                break;
            default:
                return 'rainfall';
                break;
        }
    }
    
    public function cleanVar($var_id, $value)
    {
        if (is_null($value))
        {
            return null;
        }
        else
        {
            $val = floatval(str_replace(',', '', $value));
            if ($var_id == 3)
            {
                $val = $val / 1000;
            }
            return number_format($val,1,'.','')+0;
        }
    }

}
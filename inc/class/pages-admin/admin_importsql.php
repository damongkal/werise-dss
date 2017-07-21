<?php
echo 'under construction...';die();
include('bootstrap.php'); 
class admin_importsql
{
    private $db;
    public function exec() 
    {
        $this->db = Database_MySQL::getInstance();

        $sql = filter_input(INPUT_POST, 'sqlstr', FILTER_UNSAFE_RAW);
        
        if (!is_null($sql))
        {
            if ($sql==='INIT-IMPORT')
            {
                $this->initImport();
                return;
            }
            if ($sql==='FINISH-IMPORT')
            {
                $this->finishImport();
                return;
            }            
            $this->db->query($sql);
        }
    }    
    
    private function initImport()
    {
        $sql = 'DELETE FROM `export_weather_dataset`';
        $this->db->query($sql);
        $sql2 = 'DELETE FROM `export_weather_data`';
        $this->db->query($sql2);
        $sql3 = 'DELETE FROM `export_oryza_dataset`';
        $this->db->query($sql3);
        $sql4 = 'DELETE FROM `export_oryza_data`';
        $this->db->query($sql4);
        $sql5 = 'DELETE FROM `export_oryza_datares`';        
        $this->db->query($sql5);
    }
    
    private function finishImport()
    {
        $row = $this->db->getRow("SELECT * FROM `export_weather_dataset`");
        
        $sql = "DELETE a.* FROM `weather_data` AS a INNER JOIN `weather_dataset` AS b ON a.`dataset_id` = b.`id` WHERE b.`country_code` = '{$row->country_code}' AND b.`station_id` = {$row->station_id}";
        $this->db->query($sql);
        
        $sql = "DELETE a.* FROM `oryza_data` AS a INNER JOIN `oryza_dataset` AS b ON a.`dataset_id` = b.`id` WHERE b.`country_code` = '{$row->country_code}' AND b.`station_id` = {$row->station_id}";
        $this->db->query($sql);
        
        $sql = "DELETE a.* FROM `oryza_datares` AS a INNER JOIN `oryza_dataset` AS b ON a.`dataset_id` = b.`id` WHERE b.`country_code` = '{$row->country_code}' AND b.`station_id` = {$row->station_id}";
        $this->db->query($sql);
        
        $sql = "INSERT IGNORE INTO `weather_dataset` (`country_code`, `station_id`, `year`, `wtype`, `upload_date`)
SELECT `country_code`, `station_id`, `year`, `wtype`, `upload_date` FROM `export_weather_dataset`";
        $this->db->query($sql);
        
        $sql = "UPDATE `export_weather_dataset` AS a
INNER JOIN `weather_dataset` AS b
ON a.`country_code` = b.`country_code`
    AND a.`station_id` = b.`station_id`
    AND a.`year` = b.`year`
    AND a.`wtype` = b.`wtype`
    SET a.`newid` = b.`id`";
        $this->db->query($sql);
        
        $sql = "INSERT INTO `weather_data` (`dataset_id`, `observe_date`, `rainfall`, `min_temperature`, `max_temperature`, `irradiance`, `vapor_pressure`, `mean_wind_speed`, `decadal`)
SELECT `newid`, `observe_date`, `rainfall`, `min_temperature`, `max_temperature`, `irradiance`, `vapor_pressure`, `mean_wind_speed`, `decadal`
FROM `export_weather_data` AS a
INNER JOIN `export_weather_dataset` AS b ON a.dataset_id = b.id";
        $this->db->query($sql);
        
        $sql = "INSERT IGNORE INTO `oryza_dataset` (`country_code`, `station_id`, `year`, `wtype`, `variety`, `fert`, `upload_date`)
SELECT `country_code`, `station_id`, `year`, `wtype`, `variety`, `fert`, `upload_date` FROM `export_oryza_dataset`";
        $this->db->query($sql);
        
        $sql = "UPDATE `export_oryza_dataset` AS a
INNER JOIN `oryza_dataset` AS b
ON a.`country_code` = b.`country_code`
    AND a.`station_id` = b.`station_id`
    AND a.`year` = b.`year`
    AND a.`wtype` = b.`wtype`
    AND a.`variety` = b.`variety`
    AND a.`fert` = b.`fert`
SET a.`newid` = b.`id`";
        $this->db->query($sql);
        
        $sql = "INSERT INTO `oryza_data` (`dataset_id`, `runnum`, `observe_date`, `yield`, `fert`)
SELECT `newid`, `runnum`, `observe_date`, `yield`, a.`fert`
FROM `export_oryza_data` AS a
INNER JOIN `export_oryza_dataset` AS b ON a.dataset_id = b.id;";
        $this->db->query($sql);
        
        $sql = "INSERT INTO `oryza_datares` (`dataset_id`, `runnum`, `day`, `dvs`, `zw`, `doy`)
SELECT `newid`, `runnum`, `day`, `dvs`, `zw`, `doy`
FROM `export_oryza_datares` AS a
INNER JOIN `export_oryza_dataset` AS b ON a.dataset_id = b.id;";
        $this->db->query($sql);
    }
}
try {
    $cls = new admin_importsql;
    $cls->exec();
    echo 'ACK';    
} catch (Exception $ex) {
    echo $ex->getMessage();
    //echo 'NACK';    
}

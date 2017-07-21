<?php

class werise_cdfdm_historical {

    public function createFiles($country_code, $region_id, $station_id) {
        $rs = $this->getHistorical($country_code, $station_id);

        // create folder
        $dir = werise_cdfdm_folder::getFolder($country_code, $region_id, werise_cdfdm_folder::_SRC_OBS);
        @mkdir($dir);

        // create historical files
        $files = array();
        foreach (werise_cdfdm_file::getTypes() as $type) {
            $files[$type] = new werise_cdfdm_filewrite;
            $files[$type]->open($country_code, $region_id, werise_cdfdm_folder::_SRC_OBS, $type);
        }

        // put data
        foreach ($rs as $rec) {
            $files[werise_cdfdm_file::_TYPE_PR]->write($rec->observe_date, number_format($rec->rainfall, 3));
            $files[werise_cdfdm_file::_TYPE_TN]->write($rec->observe_date, number_format($rec->min_temperature, 3));
            $files[werise_cdfdm_file::_TYPE_TX]->write($rec->observe_date, number_format($rec->max_temperature, 3));
            $files[werise_cdfdm_file::_TYPE_WS]->write($rec->observe_date, number_format($rec->mean_wind_speed, 3));
        }

        // close files
        foreach (werise_cdfdm_file::getTypes() as $type) {
            $files[$type]->close();
        }
        
        $this->logEvent($country_code, $region_id, $station_id);
    }
    
    public static function getStations($region_id) {
        $db = Database_MySQL::getInstance();
        $sql = "
            SELECT DISTINCT c.`station_id`, c.`station_name`
            FROM `regions` AS a
            INNER JOIN `weather_stations` AS c
                ON a.`region_id` = c.`region_id`
            INNER JOIN "._DB_DATA.".`weather_dataset` AS d
                ON c.`country_code` = d.`country_code`                
                AND c.`station_id` = d.`station_id`                
                AND d.`wtype` = 'r'
            WHERE a.`region_id` = %u
            UNION
            SELECT DISTINCT c.`station_id`, c.`station_name`
            FROM `regions` AS a
            INNER JOIN `weather_stations` AS c
                ON a.`region_id` = c.`region_id`
            INNER JOIN "._DB_DATA.".`weather_dataset` AS d
                ON c.`country_code` = d.`country_code`                
                AND c.`station_id` = d.`station_id`                
                AND d.`wtype` = 'r'                
            WHERE a.`parent_region` = %u
            ORDER BY station_name";        
        return $db->getRowList(sprintf($sql,$region_id,$region_id));
    }    

    
    private function getHistorical($country_code, $station_id) {
        // get historical data
        $db = Database_MySQL::getInstance();
        $sql = "
            SELECT `observe_date`, `rainfall`, `min_temperature`, `max_temperature`, `mean_wind_speed` 
            FROM " . _DB_DATA . ".weather_dataset AS a
            INNER JOIN " . _DB_DATA . ".weather_data AS b
                ON a.`id` = b.`dataset_id`
            WHERE a.`station_id` = %u
                AND a.`country_code` = '%s'
                AND a.`wtype` = 'r'
            ORDER BY `observe_date`";
        return $db->getRowList(sprintf($sql,intval($station_id),$country_code));        
    }
    private function logEvent($country_code, $region_id, $station_id) {
        $db = Database_MySQL::getInstance();
        $sql = "
            INSERT INTO " . _DB_DATA . ".cdfdm_historical_log 
            (`country_code`, `region_id`, `station_id`, `date_log`)
            VALUES ('%s',%u,".'%3$u'.",NOW())
            ON DUPLICATE KEY UPDATE `station_id` = ".'%3$u'.", `date_log` = NOW()";
        $db->query(sprintf($sql,$country_code,intval($region_id),intval($station_id)));        
    }
    
    public static function getLastLog($country_code, $region_id) {
        $db = Database_MySQL::getInstance();
        $sql = "
            SELECT w.`station_name`, a.*
            FROM " . _DB_DATA . ".`cdfdm_historical_log` AS a
            INNER JOIN `weather_stations` AS w
                ON a.`station_id` = w.`station_id`
            WHERE a.`country_code` = '%s'
                AND a.`region_id` = %u";
        return $db->getRow(sprintf($sql,$country_code,intval($region_id)));
    }
}

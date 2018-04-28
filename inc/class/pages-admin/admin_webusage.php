<?php
define('_CURRENT_OPT', 'Administration &raquo; Website Usage');

class admin_webusage
{

    public function __construct()
    {
        
    }

    public function getWeatherAccessLog()
    {
        $db = Database_MySQL::getInstance();
        $sql = "
            SELECT a.*, u.`username`, w.`station_name`, 
                r1.`region_name` as sub_region,
                r2.`region_name` as top_region
            FROM `weather_access_log` AS a 
            LEFT JOIN `users` AS u 
                ON a.`userid` = u.`userid`
            LEFT JOIN `weather_stations` AS w 
                ON a.`country_code` = w.`country_code`
                AND a.`station_id` = w.`station_id`
            LEFT JOIN `regions` AS r1 
                ON w.`region_id` = r1.`region_id`    
            LEFT JOIN `regions` AS r2 
                ON r1.`parent_region` = r2.`region_id`        
            ORDER BY a.`create_date` DESC
            LIMIT 100";
        return $db->getRowList($sql);
    }

    public function getOryzaAccessLog()
    {
        $db = Database_MySQL::getInstance();
        $sql = "
            SELECT a.*, u.`username`, w.`station_name`, 
                r1.`region_name` as sub_region,
                r2.`region_name` as top_region
            FROM `oryza_access_log` AS a 
            LEFT JOIN `users` AS u 
                ON a.`userid` = u.`userid`
            LEFT JOIN `weather_stations` AS w 
                ON a.`country_code` = w.`country_code`
                AND a.`station_id` = w.`station_id`
            LEFT JOIN `regions` AS r1 
                ON w.`region_id` = r1.`region_id`    
            LEFT JOIN `regions` AS r2 
                ON r1.`parent_region` = r2.`region_id`        
            ORDER BY a.`create_date` DESC
            LIMIT 100";        
        return $db->getRowList($sql);
    }
}

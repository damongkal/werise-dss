<?php
class weather_stations
{
    private $db;
    
    public function __construct()
    {
        $this->db = Database_MySQL::getInstance();
    }
    
    public function getStation($country,$station)
    {
        $sql = "SELECT * FROM weather_stations WHERE country_code='{$country}' AND station_id=".$station;
        return $this->db->getRow($sql);
    }    
    
    public function getStations($country,$ctype)
    {
        if ($ctype=='w')
        {
            $sql = "
                SELECT DISTINCT w.* 
                FROM `weather_stations` AS w
                INNER JOIN `weather_dataset` AS d
                    ON w.`country_code` = d.`country_code`
                    AND w.`station_id` = d.`station_id`
                WHERE w.`country_code` ='{$country}' 
                    AND w.`is_enabled`=1 
                ORDER BY station_name";
        } else
        {
            $sql = "
                SELECT DISTINCT w.* 
                FROM `weather_stations` AS w
                INNER JOIN `oryza_dataset` AS d
                    ON w.`country_code` = d.`country_code`
                    AND w.`station_id` = d.`station_id`
                WHERE w.`country_code` ='{$country}' 
                    AND w.`is_enabled`=1 
                ORDER BY station_name";            
        }
        return $this->db->getRowList($sql);
    }
    
    public function getStationYears($country,$station,$dbsource)
    {
        $dbs = 'weather_dataset';
        if ($dbsource=='o')
        {
            $dbs = 'oryza_dataset';
        }
        $sql = "SELECT DISTINCT `wtype`, `year` FROM {$dbs} WHERE country_code='{$country}' AND station_id = {$station} ORDER BY `wtype`, `year`";
        return $this->db->getRowList($sql);
    }    
    
    public function getStationGeoLat($country, $station)
    {
        $rs = $this->getStation($country, $station);
        $geolat = null;
        if($rs)
        {
            $geolat = $rs->geo_lat;
        }
        
        // if not specified, use country defaults
        if (is_null($geolat))
        {
            $geolat = 0; // northern            
            if ($country=='ID')
            {
                $geolat = -1; // southern 
            }
        }
        return $geolat;
    }    

}
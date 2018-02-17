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
        $sql = sprintf("
            SELECT *
            FROM `weather_stations`
            WHERE country_code='%s'
                AND station_id=%d", $this->db->escape($country),intval($station));
        $rs = $this->db->getRow($sql);
        $rs->geo_lat = $this->getStationGeoLat($rs);
        return $rs;
    }
    
    public static function getRegion($region_id)
    {
        $db = Database_MySQL::getInstance();
        $sql = "
            SELECT a.`country_code`, 
                a.parent_region,
                a.`region_id` AS subregion_id,
                a.`region_name` AS subregion_name,
                b.`region_id` AS region_id,
                b.`region_name` AS region_name
            FROM `regions` AS a
            LEFT JOIN `regions` AS b
                ON a.`parent_region` = b.`region_id`
            WHERE a.`region_id` = '%d'";       
        $sql2 = sprintf($sql, intval($region_id));
        return $db->getRow($sql2);
    }    

    public function getStations($country,$ctype)
    {
        $username = dss_auth::getUsername();
        $filter_auth = '';

        /*if (_ADM_ENV==='PROD' && $username!='admin')
        {
            $filter_auth = "
                INNER JOIN `weather_access` AS a
                ON a.`username`='{$username}'
                AND a.`country_code` = d.`country_code`
                AND a.`station_id` = d.`station_id`";
        }*/

        if ($ctype=='w')
        {
            $sql = "
                SELECT DISTINCT w.*, 
                r1.region_id AS subregion_id,
                r1.region_name AS subregion_name, 
                r2.region_id AS region_id,
                r2.region_name
                FROM `weather_stations` AS w
                INNER JOIN "._DB_DATA.".`weather_dataset` AS d
                    ON w.`country_code` = d.`country_code`
                    AND w.`station_id` = d.`station_id`
                LEFT JOIN `regions` AS r1
                    ON w.`region_id` = r1.`region_id`
                LEFT JOIN `regions` AS r2
                    ON r1.`parent_region` = r2.`region_id`
                LEFT JOIN "._DB_DATA.".`weather_dataset_display` AS c
                    ON d.id = c.dataset_id
                {$filter_auth}
                WHERE w.`country_code` = '{$country}'
                    AND w.`is_enabled` = 1
                    AND c.`is_disabled` IS NULL
                ORDER BY r2.region_id,r1.region_id,station_name";
        } else
        {
            $sql = "
                SELECT DISTINCT w.*,
                r1.region_id AS subregion_id,
                r1.region_name AS subregion_name, 
                r2.region_id AS region_id,
                r2.region_name
                FROM `weather_stations` AS w
                INNER JOIN "._DB_DATA.".`oryza_dataset` AS d
                    ON w.`country_code` = d.`country_code`
                    AND w.`station_id` = d.`station_id`
                LEFT JOIN `regions` AS r1
                    ON w.`region_id` = r1.`region_id`
                LEFT JOIN `regions` AS r2
                    ON r1.`parent_region` = r2.`region_id`
                LEFT JOIN "._DB_DATA.".`oryza_dataset_display` AS c
                    ON d.id = c.dataset_id
                {$filter_auth}
                WHERE w.`country_code` = '{$country}'
                    AND w.`is_enabled` = 1
                    AND c.`is_disabled` IS NULL
                ORDER BY r1.region_name,r2.region_name,station_name";
        }
        return $this->db->getRowList($sql);
    }

    public function getStationYears($country,$station,$dbsource,$show_historical=null)
    {
        $dbs = 'weather_dataset';
        if ($dbsource=='o')
        {
            $dbs = 'oryza_dataset';
        }
        $wtype_clause = '';
        if ($show_historical===false)
        {
            $wtype_clause = "AND `wtype`='f'";
        }
        $sql = "
            SELECT DISTINCT a.`wtype`, a.`year`
            FROM "._DB_DATA.".{$dbs} AS a
            LEFT JOIN "._DB_DATA.".{$dbs}_display AS c
                ON a.id = c.dataset_id
            WHERE a.`country_code`='{$country}'
                AND a.`station_id` = {$station}
                AND c.`is_disabled` IS NULL
                {$wtype_clause}
            ORDER BY `wtype`, `year`";
        return $this->db->getRowList($sql);
    }

    private function getStationGeoLat($rs)
    {
        $geolat = null;
        if($rs)
        {
            $geolat = $rs->geo_lat;
        }

        // if not specified, use country defaults
        if (is_null($geolat))
        {
            $geolat = 0; // northern
            if ($rs->country_code=='ID')
            {
                $geolat = -1; // southern
            }
        }
        return $geolat;
    }

    public static function getAll($filter=null)
    {
        $db = Database_MySQL::getInstance();
        $where = array();
        $where[] = "a.`is_enabled` = 1";
        if (isset($filter['country']))
        {
            $where[] = sprintf("a.`country_code` = '%s'",$db->escape($filter['country']));
        }
        if (isset($filter['station']))
        {
            $where[] = sprintf("a.`station_id` = '%u'",intval($filter['station']));
        }
        $where_clause = Database_MySQL::getWhere($where);
        $sql = "
            SELECT 
            c.region_id AS topregion_id,            
            c.`region_name` AS topregion_name,
            b.region_id AS subregion_id,            
            b.`region_name` AS subregion_name,
            a.* 
            FROM `weather_stations` AS a
            INNER JOIN `regions` AS b
                ON a.`region_id` = b.`region_id`
            INNER JOIN `regions` AS c
                ON b.`parent_region` = c.`region_id`
            {$where_clause}
            ORDER BY a.`country_code`, c.`region_name`, b.`region_name`, a.`station_name`";
        return $db->getRowList($sql);
    }

}
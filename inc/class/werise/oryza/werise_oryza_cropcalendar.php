<?php
/**
 * one cropping calendar advisory
 */
class werise_oryza_cropcalendar
{    
    public static function updateCalendars()
    {
        $db = Database_MySQL::getInstance();
        $sql = '
            UPDATE '._DB_DATA.'.`oryza_data` AS a,
            (
                SELECT b.`dataset_id`, b.`runnum`, MAX(b.`day`) - MIN(b.`day`) AS resdate
                FROM '._DB_DATA.'.`oryza_datares` AS b
                WHERE `dvs` <= %f
                GROUP BY b.`dataset_id`, b.`runnum`
            ) AS c
            SET a.`%2$s` = c.`resdate`
            WHERE a.`%2$s` IS NULL
                AND a.`dataset_id` = c.`dataset_id`
                AND a.`runnum` = c.`runnum`';
        $db->query(sprintf($sql,0,'emergence'));
        $db->query(sprintf($sql,0.65,'panicle_init'));
        $db->query(sprintf($sql,1,'flowering'));
        $db->query(sprintf($sql,2,'harvest'));
    }
    
    /**
     * get crop calendar dates
     * @param type $dataset_id
     * @param type $runnum
     * @param type $sow_date
     * @param type $growth_stage
     * @return boolean
     */
    public static function getDate($sowdate,$day)
    {
        $date = DateTime::createFromFormat('Y-m-d', $sowdate);
        $date->modify('+'.$day.' day');
        return $date->format('U');
    }        
}
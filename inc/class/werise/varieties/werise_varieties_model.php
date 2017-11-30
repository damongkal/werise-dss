<?php

class werise_varieties_model
{

    public static function getRecords(werise_varieties_record $variety = null)
    {
        $db = Database_MySQL::getInstance();

        $where = array();
        if (!is_null($variety)) {
            if (!is_null($variety->id)) {
                $where[] = sprintf("`variety_id` = %d", intval($variety->id));
            }
        }
        $where_clause = '';
        if (count($where) > 0) {
            $where_clause = 'WHERE ' . implode(' AND ', $where);
        }

        $sql = "SELECT * FROM `varieties` {$where_clause} ORDER BY `variety_name`";
        $recs = false;
        foreach ($db->getRowList($sql) as $var_rec) {
            $rec_tmp = new werise_varieties_record;
            $rec_tmp->loadRecord($var_rec);
            $recs[] = $rec_tmp;
        }
        return $recs;
    }
}

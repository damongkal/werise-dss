<?php

class werise_cdfdm_gcm
{

    public function createFiles($country_code, $region_id)
    {
        // get sintex-f data
        $db = Database_MySQL::getInstance();
        $min = $db->getRow("SELECT MIN(forecast_date) AS mindate FROM " . _DB_DATA . ".sintexf_raw");
        $min_parts = explode('-', $min->mindate);
        $max = $db->getRow("SELECT MAX(forecast_date) AS maxdate FROM " . _DB_DATA . ".sintexf_raw");
        $max_parts = explode('-', $max->maxdate);
        $sql = "
            SELECT *
            FROM `calendar` AS c
            LEFT JOIN " . _DB_DATA . ".sintexf_raw AS r
                ON c.`cal_date` = r.`forecast_date`
                AND r.`region_id` = %u
            WHERE c.cal_date BETWEEN '%s' AND '%s'
            ORDER BY `cal_date`";
        $sql2 = sprintf($sql, intval($region_id), $min_parts[0] . '-01-01', $max_parts[0] . '-12-31');
        $rs = $db->getRowList($sql2);

        // wind speed 10m to 2m conversion factor
        $wsf = log(2 / 0.05) / log(10 / 0.05);

        // create folder
        $dir = werise_cdfdm_folder::getFolder($country_code, $region_id, werise_cdfdm_folder::_SRC_GCM);
        @mkdir($dir);

        // create GCM files
        $files = array();
        foreach (werise_cdfdm_file::getTypes() as $type) {
            $files[$type] = new werise_cdfdm_filewrite;
            $files[$type]->open($country_code, $region_id, werise_cdfdm_folder::_SRC_GCM, $type);
        }

        // put data
        foreach ($rs as $rec) {
            // convert null to -999
            if (is_null($rec->pr)) {
                $pr = -999;
            } else {
                $pr = number_format($rec->pr, 3);
            }
            $files[werise_cdfdm_file::_TYPE_PR]->write($rec->cal_date, $pr);
            // convert null to -999
            if (is_null($rec->tn)) {
                $tn = -999;
            } else {
                $tn = number_format($rec->tn, 3);
            }
            $files[werise_cdfdm_file::_TYPE_TN]->write($rec->cal_date, $tn);
            // convert null to -999
            if (is_null($rec->tx)) {
                $tx = -999;
            } else {
                $tx = number_format($rec->tx, 3);
            }
            $files[werise_cdfdm_file::_TYPE_TX]->write($rec->cal_date, $tx);
            // convert null to -999
            if (is_null($rec->ws)) {
                $ws = -999;
            } else {
                $ws = number_format($rec->ws * $wsf, 3);
            }
            $files[werise_cdfdm_file::_TYPE_WS]->write($rec->cal_date, $ws);
        }

        // close files
        foreach (werise_cdfdm_file::getTypes() as $type) {
            $files[$type]->close();
        }
    }
}

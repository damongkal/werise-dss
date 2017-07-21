<?php

class werise_cdfdm_gcm {

    public function createFiles($country_code, $region_id) {
        // get sintex-f data
        $db = Database_MySQL::getInstance();
        $sql = "SELECT * FROM " . _DB_DATA . ".sintexf_raw WHERE `region_id` = " . intval($region_id) . ' ORDER BY `forecast_date`';
        $rs = $db->getRowList($sql);

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
            $files[werise_cdfdm_file::_TYPE_PR]->write($rec->forecast_date, number_format($rec->pr, 3));
            $files[werise_cdfdm_file::_TYPE_TN]->write($rec->forecast_date, number_format($rec->tn, 3));
            $files[werise_cdfdm_file::_TYPE_TX]->write($rec->forecast_date, number_format($rec->tx, 3));
            $files[werise_cdfdm_file::_TYPE_WS]->write($rec->forecast_date, number_format($rec->ws * $wsf, 3));
        }

        // close files
        foreach (werise_cdfdm_file::getTypes() as $type) {
            $files[$type]->close();
        }
    }

}

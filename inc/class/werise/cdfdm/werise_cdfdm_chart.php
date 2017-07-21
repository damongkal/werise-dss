<?php

class werise_cdfdm_chart {
    public function getYears($region_id) {
        $db = Database_MySQL::getInstance();
        $sql = "SELECT DISTINCT DATE_FORMAT(`forecast_date`,'%Y') AS year FROM "._DB_DATA.".sintexf_raw WHERE `region_id` = ".intval($region_id).' ORDER BY `forecast_date`';
        return $db->getRowList($sql);        
    }
    public function getData($country_code,$region_id,$year,$cdfdm_source) {        
        $sintexf = new werise_sintexf_data;
        $rs = $sintexf->getRecords(array('region_id'=>$region_id,'year'=>$year));
        if ($rs) {
            $all = array();            
            $data = array();       
            foreach($rs as $rec) {
                $fdate = DateTime::createFromFormat('Y-m-d', $rec->forecast_date);
                $data[] = array($fdate->format('U') * 1000, floatval($rec->$cdfdm_source));
            }
            $all[] = array('data'=>$data,'name'=>'SINTEX-F');
            
            $datasources = array(werise_cdfdm_folder::_SRC_GCM,werise_cdfdm_folder::_SRC_OBS,werise_cdfdm_folder::_SRC_OUT);
            foreach ($datasources as $datasource) {
                $folder = werise_cdfdm_folder::getFolder($country_code, $region_id, $datasource);                        
                $cdfdm_file = new werise_cdfdm_fileread;
                try {
                    $cdfdm_file->open($country_code,$region_id, $datasource, $cdfdm_source);
                } catch(Exception $e) {
                    continue;
                }                    
                $data_series = $cdfdm_file->getSeriesData($year);
                $all[] = array('data'=>$data_series,'name'=> strtoupper($datasource));
                $cdfdm_file->close();                
            }
            return $all;
        }
        return false;
    }
    
    public static function getWvarField($cdfdm_col) {
        if ($cdfdm_col===werise_cdfdm_file::_TYPE_PR) {
            $idx = werise_weather_properties::_WVAR_RAINFALL;
        }
        if ($cdfdm_col===werise_cdfdm_file::_TYPE_TN) {
            $idx = werise_weather_properties::_WVAR_MINTEMP;
        }
        if ($cdfdm_col===werise_cdfdm_file::_TYPE_TX) {
            $idx = werise_weather_properties::_WVAR_MAXTEMP;
        }
        if ($cdfdm_col===werise_cdfdm_file::_TYPE_WS) {
            $idx = werise_weather_properties::_WVAR_WINDSPEED;
        }        
        return werise_weather_properties::getVarName($idx);
    }    

}

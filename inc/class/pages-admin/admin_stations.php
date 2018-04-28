<?php

define('_CURRENT_OPT', 'Administration &raquo; Database Overview');

class admin_stations {

    public $action;

    public $stations;
    public $regions;

    public function __construct() {

        // requested action
        $this->action = 'list';
        if (isset($_REQUEST['action']))
        {
            $this->action = $_REQUEST['action'];
        }
        switch($this->action)
        {
            case 'list':
                $this->actionList();
                break;
            case 'sandbox':
                $this->actionSandbox();
                break;
        }
    }

    private function actionList() {
        // physical PRN weather files
        $files_r = $this->getFileCounts(werise_weather_properties::_REALTIME);
        $files_f = $this->getFileCounts(werise_weather_properties::_FORECAST);
        // weather data in DB
        $weather_r = $this->getWeatherDatasetCounts(werise_weather_properties::_REALTIME);
        $weather_f = $this->getWeatherDatasetCounts(werise_weather_properties::_FORECAST);

        // oryza datasets in DB
        $oryza_dset = $this->getOryzaCounts();

        $this->stations = array();
        $this->regions = array();
        foreach (weather_stations::getAll(array('is_enabled'=>true)) as $rec) {
            $country_code = $rec->country_code;
            $region_id = intval($rec->topregion_id);
            if (is_null($region_id)) {
                $region_id = 0;
            }
            $subregion_id = intval($rec->subregion_id);
            if (is_null($subregion_id)) {
                $subregion_id = 0;
            }
            $station_id = $rec->station_id;
            // merge PRN historical data
            $rec->historical = false;
            if (isset($files_r[$country_code][$station_id])) {
                $rec->historical = $files_r[$country_code][$station_id];
            }
            // merge PRN forecast data
            $rec->forecast = false;
            if (isset($files_f[$country_code][$station_id])) {
                $rec->forecast = $files_f[$country_code][$station_id];
            }
            // merge DB historical data
            $rec->historicaldb = false;
            if (isset($weather_r[$country_code][$station_id])) {
                $rec->historicaldb = $weather_r[$country_code][$station_id];
            }
            // merge DB forecast data
            $rec->forecastdb = false;
            if (isset($weather_f[$country_code][$station_id])) {
                $rec->forecastdb = $weather_f[$country_code][$station_id];
            }
            // merge oryza data
            $rec->oryza = false;
            if (isset($oryza_dset[$country_code][$station_id])) {
                $rec->oryza = $oryza_dset[$country_code][$station_id];
            }
            // compile
            $this->stations[$country_code][$region_id][$subregion_id][] = $rec;
            $this->regions[$region_id] = $rec->topregion_name;
            $this->regions[$subregion_id] = $rec->subregion_name;
        }
    }
    
    private function actionSandbox() {
        return;
        $db = Database_MySQL::getInstance();
        // get max-date
        $sql = "SELECT MAX(`cal_date`) AS maxdate FROM `calendar`";
        $rs = $db->getRow($sql);
        $d = new DateTime($rs->maxdate);
        // create new dates
        $sql2 = "INSERT IGNORE INTO `calendar` VALUES ('%s')";
        for ($i=0;$i<10000;$i++) {
            $d->add(new DateInterval('P1D'));            
            $sql3 = sprintf($sql2,$d->format('Y-m-d'));
            $db->query($sql3);
        }
    }

    private function getFileCounts($wtype) {
        $cls3 = new datafiles;
        $files = werise_weather_file::getFileList($wtype);
        $files2 = array();
        if ($files) {
            foreach ($files as $key => $file) {
                $arr = $cls3->getDatasetFromFilename($file['file']);
                $country = $arr['country'];
                $station = $arr['station'];
                $year = $arr['year'];
                if (!isset($files2[$country][$station])) {
                    $files2[$country][$station] = array('min' => $year, 'max' => $year, 'cnt' => 0);
                }
                if ($year < $files2[$country][$station]['min']) {
                    $files2[$country][$station]['min'] = $year;
                }
                if ($year > $files2[$country][$station]['max']) {
                    $files2[$country][$station]['max'] = $year;
                }
                $files2[$country][$station]['cnt'] ++;
            }
        }
        return $files2;
    }

    private function getWeatherDatasetCounts($wtype) {
        $weather_dset = weather_data::getDatasets(null, $wtype);
        $files2 = array();
        if ($weather_dset) {
            foreach ($weather_dset as $rec) {
                $country = $rec->country_code;
                $station = $rec->station_id;
                $year = $rec->year;
                if (!isset($files2[$country][$station])) {
                    $files2[$country][$station] = array('min' => $year, 'max' => $year, 'cnt' => 0);
                }
                if ($year < $files2[$country][$station]['min']) {
                    $files2[$country][$station]['min'] = $year;
                }
                if ($year > $files2[$country][$station]['max']) {
                    $files2[$country][$station]['max'] = $year;
                }
                $files2[$country][$station]['cnt'] ++;
            }
        }
        return $files2;
    }

    private function getOryzaCounts() {
        $oryza_dset = oryza_data::getAllDatasets(werise_weather_properties::_FORECAST);
        $files2 = array();
        if ($oryza_dset) {
            foreach ($oryza_dset as $rec) {
                $country = $rec->country_code;
                $station = $rec->station_id;
                $year = $rec->year;
                if (!isset($files2[$country][$station])) {
                    $files2[$country][$station] = array('min' => $year, 'max' => $year, 'cnt' => 0);
                }
                if ($year < $files2[$country][$station]['min']) {
                    $files2[$country][$station]['min'] = $year;
                }
                if ($year > $files2[$country][$station]['max']) {
                    $files2[$country][$station]['max'] = $year;
                }
                $files2[$country][$station]['cnt'] ++;
            }
        }
        return $files2;
    }

    public function getRegionName($region_id) {
        if (isset($this->regions[$region_id])) {
            return $this->regions[$region_id];
        }
        return 'unknown region ' . $region_id;
    }

    public function fmtLatLon($station) {
        $map_ok = true;
        $gps_span = 'success';
        $gps_status = 'thumbs-up';
        if ($station->gps_confirmed == 0) {
            $gps_span = 'danger';
            $gps_status = 'thumbs-down';
        }        
        $lat = '<span class="badge badge-'.$gps_span.'">' . number_format($station->geo_lat, 3) . '</span>';
        if ($station->geo_lat == '') {
            $lat = '<span class="badge badge-danger">???</span>';
            $map_ok = false;
        }
        $lon = '<span class="badge badge-'.$gps_span.'">' . number_format($station->geo_lon, 3) . '</span>';
        if ($station->geo_lon == '') {
            $lon = '<span class="badge badge-danger">???</span>';
            $map_ok = false;
        }
        $alt = '<span class="badge badge-'.$gps_span.'">' . number_format($station->geo_alt, 1) . '</span>';
        if ($station->geo_alt == '') {
            $alt = '<span class="badge badge-danger">?</span>';
        }
        $map_btn = "";
        if ($map_ok) {
            $loc[] = $station->station_name;
            if ($station->subregion_name != '') {
                $loc[] = $station->subregion_name;
            }
            if ($station->topregion_name != '') {
                $loc[] = $station->topregion_name;
            }
            $location = implode(', ', $loc);
            $map_btn = '<button class="btn btn-sm mapbtn" type="button" data="' . $station->geo_lat . ';' . $station->geo_lon . ';' . $location . '"><i class="fas fa-map-marker"></i></button>';
        }
        return "{$lat} {$lon} {$alt} {$map_btn}";
    }

    public function fmtDataFiles($data,$data2 = false) {
        if ($data2 === false) {
            if ($data === false) {
                echo '<span class="label label-important">no data</span>';
            } else {
                echo $data['min'] . ' to ' . $data['max'];
            }
        } else {
            echo 'DB ' . $data2['min'] . ' to ' . $data2['max'];
        }
    }

}

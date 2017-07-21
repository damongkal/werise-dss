<?php

define('_CURRENT_OPT', 'Administration &raquo; Database Overview');

class admin_stations {

    public $stations;
    public $regions;

    public function __construct() {

        $files_r = $this->getFileCounts(werise_weather_properties::_REALTIME);
        $files_f = $this->getFileCounts(werise_weather_properties::_FORECAST);
        $oryza_dset = $this->getOryzaCounts();

        $this->stations = array();
        $this->regions = array();
        foreach (weather_stations::getAll() as $rec) {
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
            // merge historical data
            $rec->historical = false;
            if (isset($files_r[$country_code][$station_id])) {
                $rec->historical = $files_r[$country_code][$station_id];
            }
            // merge forecast data
            $rec->forecast = false;
            if (isset($files_f[$country_code][$station_id])) {
                $rec->forecast = $files_f[$country_code][$station_id];
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
        /*
          echo '<pre>';
          print_r($this->stations);
          echo '</pre>';
          die(); */
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
        $lat = '<span class="label">' . number_format($station->geo_lat, 3) . '</span>';
        if ($station->geo_lat == '') {
            $lat = '<span class="label label-important">???</span>';
            $map_ok = false;
        }
        $lon = '<span class="label">' . number_format($station->geo_lon, 3) . '</span>';
        if ($station->geo_lon == '') {
            $lon = '<span class="label label-important">???</span>';
            $map_ok = false;
        }
        $alt = '<span class="label">' . number_format($station->geo_alt, 1) . '</span>';
        if ($station->geo_alt == '') {
            $alt = '<span class="label label-important">?</span>';
        }
        $gps_span = 'success';
        $gps_status = 'thumbs-up';
        if ($station->gps_confirmed == 0) {
            $gps_span = 'important';
            $gps_status = 'thumbs-down';
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
            $map_btn = '<button class="btn btn-mini mapbtn" type="button" data="' . $station->geo_lat . ';' . $station->geo_lon . ';' . $location . '"><i class="icon-map-marker icon-fix"></i></button>';
        }
        return "$lat : $lon : $alt <span class=\"label label-{$gps_span}\"><i class=\"icon-{$gps_status} icon-white\"></i></span> $map_btn";
    }

    public function fmtDataFiles($data) {
        if ($data === false) {
            echo '<span class="label label-important">no data</span>';
        } else {
            echo $data['min'] . ' to ' . $data['max'];
        }
    }

}

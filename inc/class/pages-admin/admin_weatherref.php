<?php

define('_CURRENT_OPT', 'Administration &raquo; Weather Data');

class admin_weatherref {

    public $view = 'list';
    // list view
    private $stations;
    private $comments;
    // station view
    public $country_code = '';
    public $station_id = 0;
    public $station_data;
    public $weather_data;
    public $oryza_data;

    public function __construct() {
        if (isset($_GET['station_id'])) {
            $code = explode('-', $_GET['station_id']);
            $this->country_code = $code[0];
            $this->station_id = $code[1];
            $this->view = 'station';
            $dataset = array('country' => $this->country_code, 'station' => $this->station_id);
            $station_data = weather_stations::getAll($dataset);
            $this->station_data = $station_data[0];
            $this->setOryzaDatasets(werise_weather_properties::_FORECAST);
            $this->setOryzaDatasets(werise_weather_properties::_REALTIME);
            $this->setWeatherDatasets(werise_weather_properties::_FORECAST);
            $this->setWeatherDatasets(werise_weather_properties::_REALTIME);
        }
        $this->comments = weather_data::getComments(false, 'r');
    }

    public function setOryzaDatasets($wtype) {
        $this->oryza_data[$wtype] = oryza_data::getDatasets(array('country' => $this->country_code, 'station' => $this->station_id, 'wtype' => $wtype));
    }

    public function setWeatherDatasets($wtype) {
        $dataset = array('country' => $this->country_code, 'station' => $this->station_id);
        $this->weather_data[$wtype] = weather_data::getDatasets($dataset, $wtype);
    }

    public function getStationName($country_code, $station_id) {
        $idx = $country_code . $station_id;
        if (!isset($this->stations[$idx])) {
            $station = new weather_stations();
            $ret = $station->getStation($country_code, $station_id);
            $this->stations[$idx] = $ret->station_name;
        }
        return $this->stations[$idx];
    }

    public function getComment($setid, $field) {
        if ($field === 'GEO') {
            $lat = '';
            if (isset($this->comments[$setid]['LATITUDE'])) {
                $lat = $this->comments[$setid]['LATITUDE'];
            }
            $lon = '';
            if (isset($this->comments[$setid]['LONGITUDE'])) {
                $lon = $this->comments[$setid]['LONGITUDE'];
            }
            $alt = '';
            if (isset($this->comments[$setid]['ALTITUDE'])) {
                $alt = $this->comments[$setid]['ALTITUDE'];
            }
            return "{$lat} ; {$lon} ; {$alt}";
        }
        if (isset($this->comments[$setid][$field])) {
            return $this->comments[$setid][$field];
        }
        return '-';
    }

    public function fmtFert($fertcode) {
        switch ($fertcode) {
            case 0:
                return 'none';
            case 1:
                return 'general recommendation';
            case 2:
                return 'specific';
            default:
                return 'unknown';
        }
    }

}

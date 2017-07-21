<?php

class werise_sintex_oryza {

    // dataset
    private $country;
    private $station_id;
    // output
    public $files_created;
    // debugger
    private $debug;

    public function __construct() {
        $this->debug = debug::getInstance();
    }

    public function createFiles($country, $station_id, $sintex_raw, $overwrite_file, $wtype) {
        // to be used everywhere
        $this->country = $country;
        $this->station_id = $station_id;
        $year = 0;
        foreach ($sintex_raw as $raw) {
            // detect year
            if ($raw[werise_cdfdm_file::_COL_YR] != $year) {
                if ($year != 0)
                {
                    fclose($handle2);
                }
                $year = $raw[werise_cdfdm_file::_COL_YR];
                $template = $this->getTemplate();
                $handle2 = $this->getFileHandle($year,$overwrite_file, $wtype);
                fwrite($handle2, $template); // write template                
            }
            $data = $this->formatLine($raw,$year);
            fwrite($handle2, "{$data}\r\n"); // write data
        }
        fclose($handle2);
    }

    /**
     * get ORYZA2000 template file
     * @return type
     * @throws Exception
     */
    private function getTemplate() {
        $template = file_get_contents(str_replace('weather_ver1','weather_ver3',_DATA_DIR . _DATA_SUBDIR_WEATHER_FORECAST . 'template.txt'));

        // get station info
        $station_info = $this->getStationInfo();
        $geoinfo = $station_info->geo_lon . '  ' . $station_info->geo_lat . ' ' . $station_info->geo_alt . '   0.00   0.00';
        $name = $station_info->station_name;
        if (!is_null($station_info->full_name)) {
            $name = $station_info->full_name;
        }
        $name .= ' , ' . werise_stations_country::getName($this->country);

        // replace keys in template
        $replace = array(
            'station' => $name,
            'geoinfo' => $geoinfo,
            'geolat' => $station_info->geo_lat,
            'geolon' => $station_info->geo_lon,
            'geoalt' => $station_info->geo_alt);
        foreach ($replace as $rkey => $rval) {
            $template = str_replace("({$rkey})", $rval, $template);
        }

        return $template;
    }

    /**
     * get station info
     * @return type
     * @throws Exception
     */
    private function getStationInfo() {
        $station = new weather_stations;
        $station_info = $station->getStation($this->country, $this->station_id);
        if (is_null($station_info->geo_lon)) {
            throw new Exception('Check Database! Please provide Geo Info Longitude for station ' . $station_info->station_name);
        }
        if (is_null($station_info->geo_lat)) {
            throw new Exception('Check Database! Please provide Geo Info Latitude for station ' . $station_info->station_name);
        }
        if (is_null($station_info->geo_alt)) {
            throw new Exception('Check Database! Please provide Geo Info Altitude for station ' . $station_info->station_name);
        }
        return $station_info;
    }

    /**
     * initialize file creation
     * @return type
     * @throws Exception
     */
    private function getFileHandle($year,$overwrite_file, $wtype) {
        $filename = $this->getWeatherFileName($year,$wtype);
        // check if file exist
        if (file_exists($filename)) {
            if (!$overwrite_file) {
                throw new Exception('file already exist! : ' . $filename);
            }
            unlink($filename);
        }
        // make new file
        $handle2 = werise_core_files::getHandle($filename, "x");
        if (!$handle2) {
            throw new Exception('unable to create file : ' . $filename);
        }
        $this->files_created[$year] = $filename;
        return $handle2;
    }

    /**
     * determine the ORYZA2000 filename
     * @param type $year
     * @return string
     */
    private function getWeatherFileName($year,$wtype) {
        $country_folder = werise_stations_country::getDir($this->country);
        $country_file = werise_stations_country::getFile($this->country);
        if ($year >= 2000) {
            $year_base = 2000;
        } else {
            $year_base = 1900;
        }
        $year_ext = str_pad($year - $year_base, 3, 0, STR_PAD_LEFT);
        
        $filename = _DATA_DIR . werise_weather_file::getFolder($wtype) . $country_folder . $country_file . $this->station_id . '.' . $year_ext;

        return $filename;
    }

    /**
     * format 1 line of ORYZA data
     * @param array $raw
     * @return string
     */
    private function formatLine($raw,$year) {
        $data = $this->formatData($this->station_id, 0, 4);
        $data .= $this->formatData($year, 0, 5);
        $data .= $this->formatData($raw[werise_cdfdm_file::_COL_DOY], 0, 4);
        $rad = -990;
        if (isset($raw['rad'])) {
            $rad = $raw['rad'];
        }
        $data .= $this->formatData($rad, 0, 8, -99000);
        $data .= $this->formatData($raw['tn'], 1, 8);
        $data .= $this->formatData($raw['tx'], 1, 8);
        $tmin = -990;
        if (isset($raw['tmin'])) {
            $tmin = $raw['tmin'];
        }        
        $data .= $this->formatData($tmin, 2, 8);
        $data .= $this->formatData($raw['ws'], 1, 8);
        $data .= $this->formatData($raw['pr'], 1, 8);
        return $data;
    }

    /**
     * format 1 data value
     * @param type $value
     * @param type $decimal
     * @param type $padding
     * @param type $null_value
     * @return type
     */
    private function formatData($value, $decimal = 1, $padding = 8, $null_value = -99) {
        if (is_null($value)) {
            $value = $null_value;
        }
        return str_pad(number_format(round($value, $decimal), $decimal, '.', ''), $padding, ' ', STR_PAD_LEFT);
    }

}

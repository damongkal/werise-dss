<?php

define('_CURRENT_OPT', 'Administration &raquo; CDF-DM Conversion');

class admin_cdfdm_convert {

    // expected URL arguments
    public $action = '';
    public $arg_override = 0;
    public $arg_year = null;
    public $arg_region = null;
    public $region_info = null;
    public $arg_country = null;
    public $arg_station = null;
    public $arg_wtype = werise_weather_properties::_FORECAST;
    // layout variables
    public $action_ret;
    public $files_sintex;
    public $files_forecast;
    public $files_compute;
    public $specialvars;
    public $is_error = false;

    public function __construct() {

        $this->initArgs();
        switch ($this->action) {
            case 'export':
                $this->doActionExport();
                break;
        }
    }

    private function initArgs() {
        if (isset($_REQUEST['action'])) {
            $this->action = $_REQUEST['action'];
        }

        if (isset($_REQUEST['overwrite_file'])) {
            $this->arg_override = intval($_REQUEST['overwrite_file']);
        }
        $this->arg_override = 1;

        $this->arg_year = date('Y');
        if (isset($_REQUEST['year'])) {
            $this->arg_year = intval($_REQUEST['year']);
        }

        if (isset($_REQUEST['region_id'])) {
            $this->arg_region = intval($_REQUEST['region_id']);        
            $this->region_info = weather_stations::getRegion($this->arg_region);
            $this->arg_country = $this->region_info->country_code;
        }

        if (isset($_REQUEST['station_id'])) {
            $this->arg_station = intval($_REQUEST['station_id']);
        }
        
        if (isset($_REQUEST['output_wtype'])) {
            $this->arg_wtype = $_REQUEST['output_wtype'];
        }        
    }

    private function doActionExport() {
        
        try {
            $sintex = new werise_sintex_handler;
            $sintex->export($this->arg_country, $this->arg_region,$this->arg_station, $this->arg_year, $this->arg_override,$this->arg_wtype);
            $this->files_sintex = $sintex->cdfdm->outfiles;
            $this->specialvars = $sintex->sintex_compute->getSpecialValues();            
            $this->files_forecast = $sintex->sintex_oryza->files_created;

            $this->files_compute = $sintex->getFilesCompute();
            $this->action_ret = $sintex->getCsv();
        } catch (Exception $e) {
            $this->is_error = true;
            $this->action_ret = $e->getMessage();
        }
    }

    public function getSintexFiles() {
        $station = new weather_stations();
        $all = array();
        foreach (werise_stations_country::getAll() as $country => $ref) {
            $files = werise_core_files::getFiles(_DATA_SUBDIR_SINTEX . $ref['dir']);
            if (is_array($files)) {
                foreach ($files as $station_id) {
                    $station_rec = $station->getStation($country, $station_id);
                    $all[] = array('country' => $country, 'station_id' => $station_id, 'station_name' => $station_rec->station_name);
                }
            }
        }

        return $all;
    }

    /**
     * format number
     * @param float $val
     * @param integer $dec
     * @return string
     */
    public function fn($val, $dec = 3) {
        if (is_null($val)) {
            return '';
        }
        return number_format($val, $dec);
    }

}

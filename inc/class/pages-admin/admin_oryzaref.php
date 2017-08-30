<?php

define('_CURRENT_OPT', 'Administration &raquo; Oryza2000 Data');

class admin_oryzaref {

    public $action;
    private $stations;
    
    public $dataset_info;
    public $dataset_station;
    public $dataset_data;    

    public function __construct() {

        // requested action
        $this->action = 'list';
        if (isset($_GET['action']))
        {
            $this->action = $_GET['action'];
        }
        
        switch($this->action)
        {
            case 'detail':
                list($this->dataset_info, $this->dataset_station, $this->dataset_data) = $this->actionDetails();
        }
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
    
    public function actionDetails()
    {
        $id = 0;
        if (isset($_GET['id']))
        {
            $id = $_GET['id'];
        }        
        // dataset info
        $filter = array('id'=>$id);
        $dset = oryza_data::getDatasets($filter);
        $dataset_info = $dset[0];
        // station info
        $filter2 = array('country'=>$dset[0]->country_code,'station'=>$dset[0]->station_id);
        $station = weather_stations::getAll($filter2);
        $dataset_station = $station[0];
        // dataset records
        $dataset_data = oryza_data::getDatasetRecords($filter);        
        return array($dataset_info,$dataset_station,$dataset_data);
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

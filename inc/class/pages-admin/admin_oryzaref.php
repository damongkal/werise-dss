<?php

define('_CURRENT_OPT', 'Administration &raquo; Oryza2000 Data');

class admin_oryzaref {

    public $action;
    private $stations;
    
    public $station;
    public $datasets;   

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
                list($this->station, $this->datasets) = $this->actionDetails();
        }
    }
    
    private function getStation($country_code, $station_id)
    {
        $filter = array('country' => $country_code, 'station' => $station_id);
        $station = weather_stations::getAll($filter);
        return $station[0];
    }

    /**
     * to be replaced!!!
     * @param type $country_code
     * @param type $station_id
     * @return type
     */
    public function getStationName($country_code, $station_id) {
        $idx = $country_code . $station_id;
        if (!isset($this->stations[$idx])) {
            $station = new weather_stations();
            $ret = $station->getStation($country_code, $station_id);
            $this->stations[$idx] = $ret->station_name;
        }
        return $this->stations[$idx];
    }
    
    private function actionDetails()
    {
        $id = 0;
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
        }
        // get first dataset info
        $filter = array('id' => $id);
        $dset = oryza_data::getDatasets(array('id' => $id));
        // get station
        $filterw = array('country' => $dset[0]->country_code, 'station' => $dset[0]->station_id);
        $station = weather_stations::getAll($filterw);
        $station_data = $station[0];
        // get all datasets
        $filter_all = array('country' => $dset[0]->country_code, 'station' => $dset[0]->station_id, 'year' => $dset[0]->year);
        $dset_all = oryza_data::getDatasets($filter_all);
        foreach ($dset_all as $dset_rec) {
            $dataset_data = oryza_data::getDatasetRecords(array('id' => $dset_rec->id));
            $chart_data = $this->getSeriesData($dataset_data);
            $datasets[$dset_rec->id] = array('dataset_info' => $dset_rec, 'dataset_data' => $dataset_data, 'chart_data' => $chart_data);
        }
        return array($station_data, $datasets);
    }
    
    private function getSeriesData($dataset) {
        $series = array();
        foreach($dataset as $data) {
            $d = new DateTime($data->observe_date);
            $series[] = array($d->format('U') * 1000, (float)$data->yield);
        }
        return $series;
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

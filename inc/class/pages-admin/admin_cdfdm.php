<?php

define('_CURRENT_OPT', 'Administration &raquo; CDF-DM');

class admin_cdfdm {

    // expected URL arguments
    public $action = '';
    public $arg_region = 0;
    public $arg_override = 0;
    public $arg_chart_year = 0;
    public $arg_chart_cdfdm_sourcecol = werise_cdfdm_file::_TYPE_PR;
    public $arg_station_id = 0;
    // output variables
    public $raw = array();
    public $region_info = null;
    public $datafiles = array();
    public $scriptfiles = array();
    public $outmaxyear = 0;
    public $debug = array();
    // weather chart
    public $chart = false;
    public $chart_years = false;

    public function __construct() {
        $this->initArgs();
        if ($this->arg_region > 0) {
            // execute action
            $this->doAction();
            $c = new werise_sintexf_data;
            $this->raw = $c->getRawData($this->arg_region);            
            // datafiles
            foreach (werise_cdfdm_folder::getSources() as $source) {
                list($dir, $files) = werise_cdfdm_folder::getFolderInfo($this->region_info->country_code, $this->arg_region, $source);
                $this->datafiles[$source] = array($dir, $files);
            }
            // get OUT datafile max year            
            $this->outmaxyear = $this->getOutMaxYear();
            // scriptfiles
            $this->scriptfiles['list'] = werise_cdfdm_script::getListFile();
            $this->scriptfiles['real'] = werise_cdfdm_script::getScriptFile(werise_cdfdm_script::_TYPE_REAL);
            $this->scriptfiles['grasp'] = werise_cdfdm_script::getScriptFile(werise_cdfdm_script::_TYPE_GRASP);
        }
    }

    private function initArgs() {
        if (isset($_REQUEST['action'])) {
            $this->action = $_REQUEST['action'];
        }
        if (isset($_REQUEST['region_id'])) {
            $this->arg_region = intval($_REQUEST['region_id']);
            $this->region_info = weather_stations::getRegion($this->arg_region);
        }
        if (isset($_REQUEST['station_id'])) {
            $this->arg_station_id = intval($_REQUEST['station_id']);
        }
        if (isset($_REQUEST['chart_year'])) {
            $this->arg_chart_year = intval($_REQUEST['chart_year']);
        }
        if (isset($_REQUEST['chart_sourcecol'])) {
            $this->arg_chart_cdfdm_sourcecol = $_REQUEST['chart_sourcecol'];
        }
    }

    private function doAction() {
        $country_code = $this->region_info->country_code;
        switch ($this->action) {
            case 'uploadraw':
                $e = new werise_sintexf_savedb;
                $e->execute($this->arg_region);
                $this->debug[] = 'Data stored to database successfully!';
            case 'rawtogcm':
                $gcm = new werise_cdfdm_gcm;
                $gcm->createFiles($country_code, $this->arg_region);
                $this->debug[] = 'GCM files created successfully!';
                // refresh datafiles
                $source = werise_cdfdm_folder::_SRC_GCM;
                list($dir, $files) = werise_cdfdm_folder::getFolderInfo($country_code, $this->arg_region, $source);
                $this->datafiles[$source] = array($dir, $files);
                break;
            case 'updscript':
                if (isset($_REQUEST['ftype'])) {
                    $type = $_REQUEST['ftype'];
                    // update script
                    $script = new werise_cdfdm_script;
                    $script->updateScript($type, $country_code, $this->arg_region);
                    $this->debug[] = 'Fortran scripts updated successfully!';
                }
                break;
            case 'downscale':
                if (isset($_REQUEST['ftype'])) {
                    $type = $_REQUEST['ftype'];
                    // execute cdfdm
                    $script = new werise_cdfdm_downscale;
                    $cmdout = $script->downscale($type, $country_code, $this->arg_region);
                    $this->debug = $cmdout;
                }
                break;
            case 'chart':
                // prepare chart data
                $f = new werise_cdfdm_chart;
                $this->chart_years = $f->getYears($this->arg_region);
                if ($this->arg_chart_year === 0 && $this->chart_years) {
                    foreach($this->chart_years as $listyear) {
                        if ($listyear->year > $this->arg_chart_year) {
                            $this->arg_chart_year = $listyear->year;
                        }
                    }                    
                }
                $this->chart = $f->getData($this->region_info->country_code, $this->arg_region, $this->arg_chart_year, $this->arg_chart_cdfdm_sourcecol);
                break;
            case 'obsreal':
                if ($this->arg_station_id > 0) {
                    $history = new werise_cdfdm_historical;
                    $history->createFiles($country_code, $this->arg_region, $this->arg_station_id);
                    $this->debug[] = 'Historical weather files (PRN) imported!';
                }
                break;
            /*    
            case 'createprn':
                if ($this->arg_station_id > 0) {
                    $history = new werise_cdfdm_historical;
                    $history->createFiles($country_code,$this->arg_region,$this->arg_station_id);
                    $this->debug[] = 'Forecast weather files (PRN) exported!';
                } 
                break; */
        }
    }

    public function getRegions() {
        $db = Database_MySQL::getInstance();
        $sql = "
            SELECT a.*,
                b.`region_id` AS subregion_id,
                b.`region_name` as subregion_name,
                '' AS year_data
            FROM `regions` AS a
            INNER JOIN `regions` AS b
                ON a.`region_id` = b.`parent_region`
            WHERE a.`parent_region` IS NULL
            ORDER BY a.`country_code`, a.`region_name`, b.`region_name`";
        $rs = $db->getRowList($sql);

        // get sintex-f raw data
        $sql3 = "
            SELECT `region_id`, MIN(`forecast_date`) AS miny, MAX(`forecast_date`) AS maxy
            FROM " . _DB_DATA . ".`sintexf_raw`
            GROUP BY `region_id`";
        $rs3 = $db->getRowList($sql3);
        $year_data = array();
        foreach ($rs3 as $ydata) {
            $region_id = $ydata->region_id;
            $year_data[$region_id] = $ydata;
        }

        $rs2 = array();
        foreach ($rs as $regions) {
            $country_code = $regions->country_code;
            $region_id = $regions->region_id;
            $subregion_id = $regions->subregion_id;
            if (!isset($rs2[$country_code][$region_id]['year_data'])) {
                $rs2[$country_code][$region_id]['year_data'] = '';
            }
            if (isset($year_data[$region_id])) {
                $rs2[$country_code][$region_id]['year_data'] = $year_data[$region_id]->miny . ' to ' . $year_data[$region_id]->maxy;
            }
            if (isset($year_data[$subregion_id])) {
                $regions->year_data = $year_data[$subregion_id]->miny . ' to ' . $year_data[$subregion_id]->maxy;
            }
            $rs2[$country_code][$region_id]['region_name'] = $regions->region_name;
            $rs2[$country_code][$region_id]['sub'][$subregion_id] = $regions;
        }

        //echo '<pre>';
        //print_r($rs2);
        //echo '<pre>';
        return $rs2;
    }

    public function fmtMonth($monthyear) {
        $d = DateTime::createFromFormat('Ymd',$monthyear.'01');
        return strtoupper($d->format('M-Y'));
    }

    public function fmtStatus($status) {
        $icon = "icon-ok";
        if (intval($status) === 0) {            
            $icon = "icon-exclamation-sign";
        }
        echo '<i class="' . $icon . '"></i>';
    }

    public function fmtRegion() {
        if ($this->region_info->subregion_name != '') {
            $region[] = $this->region_info->subregion_name;
        }
        if ($this->region_info->parent_region > 0 && $this->region_info->region_name != '') {
            $region[] = $this->region_info->region_name;
        }
        $region[] = werise_stations_country::getName($this->region_info->country_code);
        return implode(', ', $region);
    }

    public function getStations() {
        return werise_cdfdm_historical::getStations($this->arg_region);
    }

    public function getLastHistoryLog() {
        $station = werise_cdfdm_historical::getLastLog($this->region_info->country_code, $this->arg_region);
        if ($station) {
            if ($this->arg_station_id === 0) {
                $this->arg_station_id = $station->station_id;
            }
            return $station->station_name . ' [' . $station->date_log . ']';
        } else {
            return 'none';
        }
    }

    /**
     * get OUT datafile max year            
     */
    private function getOutMaxYear() {
        $maxyear = 0;
        $outdir = werise_cdfdm_folder::_SRC_OUT;
        if ($this->datafiles[$outdir][1]) {
            foreach ($this->datafiles[$outdir][1] as $file) {
                $tmpdate = explode('-', $file[2]);
                if (isset($tmpdate[0])) {
                    $tmpyear = intval($tmpdate[0]);
                    if ($tmpyear > $this->outmaxyear) {
                        $maxyear = $tmpyear;
                    }
                }
            }
        }
        return $maxyear;
    }

}

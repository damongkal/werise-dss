<?php
define('_CURRENT_OPT','Administration &raquo; Weather Data Files');

class admin_weatherfile
{
    public $action;
    public $action_ret = false;

    // weatherfile list
    public $files;
    public $fileprops;
    private $station_names = null;

    // dataset details
    public $dataset_info = null;
    public $dataset_station = null;
    public $dataset_data = null;
    public $dataset_irradiance_ok = false;
    public $dataset_sunshine_ok = false;
    public $dataset_decadal = null;

    // percentile
    public $pctile_station = null;
    public $pctile_decadal = null;
    public $pctile_data20 = null;
    public $pctile_data50 = null;
    public $pctile_data80 = null;
    
    // upload files created
    public $grasp_files;

    private $ver;

    public function __construct() {
        // requested action
        $this->action = 'list';
        if (isset($_REQUEST['action']))
        {
            $this->action = $_REQUEST['action'];
        }

        // choose action loader
        $action_loader = new weather_action;
        switch($this->action)
        {
            case 'list':
                list($this->files['r'],$this->files['f'],$this->files['a']) = $action_loader->actionList();
                //dbg($this->files['a']);

                $this->fileprops['r'] = array('container-id'=>'realtime-container');
                $this->fileprops['f'] = array('container-id'=>'forecast-container');
                break;
            case 'load':
                try {
                    $fcn_ret = $action_loader->actionLoad();
                    $this->action_ret = '<p class="alert alert-success" style="display:block;">dataset was loaded to database successfully.</p>';
                    $this->action_ret .= '<pre style="width:680px">';
                    $this->action_ret .= print_r($fcn_ret, true);
                    $this->action_ret .= '</pre>';
                } catch (Exception $ex) {
                    $this->action_ret = '<p class="alert alert-error" style="display:block"><b>ERROR</b> : ' . $ex->getMessage() . '</p>';
                }
                break;
            case 'del':
                $fcn_ret = $action_loader->actionDelete();
                if ($fcn_ret)
                {
                    $this->action_ret = '<p class="alert alert-success" style="display:block">dataset was deleted successfully</p>';
                    $this->action_ret .= '<pre style="width:680px">';
                    $this->action_ret .= print_r($fcn_ret, true);
                    $this->action_ret .= '</pre>';
                }
                break;
            case 'detail':
                $this->getDataset();
                break;
            case 'pctile':
                $this->getPercentile();
                break;
            case 'grasp':
                $grasp = new weather_action_grasp;
                $grasp->upload();
                $this->dataset_station = $grasp->station_info;
                $this->grasp_files = $grasp->files_created;
                break;
        }

        $this->ver = _opt(sysoptions::_ORYZA_VERSION);
    }

    /**
     * station name reference for layout
     */
    private function setStationNames()
    {
        if (!is_null($this->station_names))
        {
            return;
        }
        foreach (weather_stations::getAll(array('is_enabled'=>true)) as $station)
        {
            $idx = $station->country_code.$station->station_id;
            $this->station_names[$idx] = $station->station_name;
        }
    }
    
    private function getStationRecord($country_code,$station_id) {
        $filter = array(
            'country' => $country_code,
            'station' => $station_id,
            'is_enabled' => true);
        $station = weather_stations::getAll($filter);        
        return $station[0];
    }

    /**
     * @todo : documentation
     * @param type $country
     * @param type $station
     * @return type
     */
    public function getStationName($country,$station_id)
    {
        $this->setStationNames();
        $idx = $country.$station_id;
        if (isset($this->station_names[$idx]))
        {
            return $this->station_names[$idx];
        } else
        {
            return "unknown station [{$country}-{$station_id}]";
        }
    }

    public function getBtnUrl($prnfile,$action,$wtype) {
        $url = "admin.php?pageaction=weatherfile&action={$action}&amp;dst=w&amp;type={$wtype}&amp;prnfile={$prnfile}";
        return $url;
    }

    public function showNotes($file) {
        $notes = '';
        if ($file['is_loaded']) {
            $notes = "<p>{$file['is_loaded']->notes}</p>";
            if ($file['is_loaded']->oryza_ver!=$this->ver) {
                $notes .= '<p>version incompatible</p>';
            }
        }
        return $notes;
    }

    public function nf($val,$dec = 1) {
        if (is_null($val) || $val=='') {
            return '&nbsp;';
        }
        return number_format($val,$dec);
    }

    public function fmtPctDate($date) {
        $tmp = explode('-',$date);
        return $tmp[1].'-'.$tmp[2];
    }

    /**
     * types for CDFDM
     * @return type
     */
    public function getWvars() {
        $wvars = array(
            werise_weather_properties::_WVAR_RAINFALL,
            werise_weather_properties::_WVAR_MINTEMP,
            werise_weather_properties::_WVAR_MAXTEMP );
        if ($this->dataset_irradiance_ok) {
            $wvars[] = werise_weather_properties::_WVAR_IRRADIANCE;
        }
        if ($this->dataset_sunshine_ok) {
            $wvars[] = werise_weather_properties::_WVAR_SUNSHINE;
        }
        $wvars[] = werise_weather_properties::_WVAR_VAPOR;
        $wvars[] = werise_weather_properties::_WVAR_WINDSPEED;
        return $wvars;
    }

    private function getDataset()
    {
        $id = 0;
        if (isset($_REQUEST['id']))
        {
            $id = $_REQUEST['id'];
        }
        // dataset info
        $filter = array('id'=>$id);
        $dset = weather_data::getDatasets($filter,'');
        $this->dataset_info = $dset[0];
        // station info
        $this->dataset_station = $this->getStationRecord($dset[0]->country_code, $dset[0]->station_id);
        // dataset records
        $this->dataset_data = weather_data::getDatasetRecords($filter);
        // is data available
        foreach($this->dataset_data as $rec) {
            if (floatval($rec->irradiance)>0) {
                $this->dataset_irradiance_ok = true;
            }
            if (floatval($rec->sunshine_duration)>0) {
                $this->dataset_sunshine_ok = true;
            }
        }
        $this->dataset_decadal = weather_data::getDatasetDecadal($filter);
    }

    private function getPercentile()
    {
        $prnfile = 0;
        if (isset($_REQUEST['prnfile']))
        {
            $prnfile = $_REQUEST['prnfile'];
        }
        $tmp = explode('-',$prnfile);
        $country = $tmp[0];
        $station_id = intval($tmp[1]);

        // station info
        $this->pctile_station = $this->getStationRecord($country, $station_id);

        // decadal data
        $filter = array(
            'country'=>$country,
            'station'=>$station_id,
            'wtype'=> werise_weather_properties::_REALTIME);
        $this->pctile_decadal = weather_data::getDatasetDecadal($filter);

        if (count($this->pctile_decadal)>0)
        {
            // initialize percentile
            $this->pctile_data20 = $this->initPercentileArray();
            $this->pctile_data50 = $this->pctile_data20;
            $this->pctile_data80 = $this->pctile_data20;

            // compute percentiles
            $this->doPercentile(werise_weather_properties::_WVAR_RAINFALL);
            $this->doPercentile(werise_weather_properties::_WVAR_MINTEMP);
            $this->doPercentile(werise_weather_properties::_WVAR_MAXTEMP);
            $this->doPercentile(werise_weather_properties::_WVAR_IRRADIANCE);
            $this->doPercentile(werise_weather_properties::_WVAR_VAPOR);
            $this->doPercentile(werise_weather_properties::_WVAR_WINDSPEED);
            $this->doPercentile(werise_weather_properties::_WVAR_SUNSHINE);
        }
    }

    private function initPercentileArray()
    {
        $pctile_data = array();
        $decadal_cmp = '';
        foreach($this->pctile_decadal as $d)
        {
            if ($d->decadal!=$decadal_cmp)
            {
                $pctile_data[] = array('observe_date'=>$d->observe_date,'observe_date2'=>$d->observe_date2);
            }
            $decadal_cmp = $d->decadal;
        }
        return $pctile_data;
    }

    private function doPercentile($field)
    {
        $fieldname = werise_weather_properties::getColumnName($field);
        $pctile = new werise_weather_percentile;
        $pctile->getStationPercentile2($this->pctile_decadal, $this->pctile_station, $field, $fieldname);
        foreach ($pctile->pctile_20 as $key => $val)
        {
            $this->pctile_data20[$key][$fieldname] = $val;
        }
        foreach ($pctile->pctile_50 as $key => $val)
        {
            $this->pctile_data50[$key][$fieldname] = $val;
        }
        foreach ($pctile->pctile_80 as $key => $val)
        {
            $this->pctile_data80[$key][$fieldname] = $val;
        }
    }
}

class admin_weather_action
{
    protected $db;
    protected $cls3;

    protected $arg_prnfile;
    protected $arg_wtype;
    protected $arg_setid;

    /**
     * @todo : documentation
     */
    public function __construct()
    {
        $this->db = Database_MySQL::getInstance();

        // url parameter: prnfile
        $this->arg_prnfile = '';
        if (isset($_REQUEST['prnfile']))
        {
            $this->arg_prnfile = $_REQUEST['prnfile'];
        }

        // url parameter: realtime or forecast
        $this->arg_wtype = '';
        if (isset($_REQUEST['type']))
        {
            $this->arg_wtype = $_REQUEST['type'];
        }

        // url parameter: set-id
        $this->arg_setid = 0;
        if (isset($_REQUEST['setid']))
        {
            $this->arg_setid = intval($_REQUEST['setid']);
        }
    }

}

class weather_action extends admin_weather_action
{
    /**
     * display the list of files available
     */
    public function actionList()
    {
        // realtime files
        $files_r = $this->getWeatherFiles(werise_weather_properties::_REALTIME);
        // forecast files
        $files_f = $this->getWeatherFiles(werise_weather_properties::_FORECAST);
        $files_a = array();
        // station / region names
        foreach (weather_stations::getAll(array('is_enabled'=>true)) as $station)
        {
            $country = $station->country_code;
            $station_id = $station->station_id;
            $idx = $station->country_code.$station->station_id;
            $station_names[$idx] = $station;

            $topregion = intval($station->topregion_id);
            $topregion_name = $station->topregion_name;
            $subregion = intval($station->subregion_id);
            $subregion_name = $station->subregion_name;
            $station_name = $station->station_name;

            $files_a[$country][$topregion]['name'] = $topregion_name;
            $files_a[$country][$topregion]['subregion'][$subregion]['name'] = $subregion_name;
            $files_a[$country][$topregion]['subregion'][$subregion]['station'][$station_id]['name'] = $station_name;
        }
        // merge
        foreach($files_r as $file) {
            $country = $file['country'];
            $region = 1;
            $station = $file['station'];
            // try to get names
            $idx = $country.$station;
            $topregion = 0;
            $topregion_name = 'unknown';
            $subregion = 0;
            $subregion_name = 'unknown';
            $station_name = 'unknown';
            if (isset($station_names[$idx])) {
                $station_info = $station_names[$idx];
                $topregion = intval($station_info->topregion_id);
                $topregion_name = $station_info->topregion_name;
                $subregion = intval($station_info->subregion_id);
                $subregion_name = $station_info->subregion_name;
                $station_name = $station_info->station_name;
            }
            $files_a[$country][$topregion]['name'] = $topregion_name;
            $files_a[$country][$topregion]['subregion'][$subregion]['name'] = $subregion_name;
            $files_a[$country][$topregion]['subregion'][$subregion]['station'][$station]['name'] = $station_name;
            $files_a[$country][$topregion]['subregion'][$subregion]['station'][$station]['r'][] = $file;
        }
        foreach($files_f as $file) {
            $country = $file['country'];
            $region = 1;
            $station = $file['station'];
            // try to get names
            $idx = $country.$station;
            $topregion = 0;
            $topregion_name = 'unknown';
            $subregion = 0;
            $subregion_name = 'unknown';
            $station_name = 'unknown';
            if (isset($station_names[$idx])) {
                $station_info = $station_names[$idx];
                $topregion = intval($station_info->topregion_id);
                $topregion_name = $station_info->topregion_name;
                $subregion = intval($station_info->subregion_id);
                $subregion_name = $station_info->subregion_name;
                $station_name = $station_info->station_name;
            }
            $files_a[$country][$topregion]['name'] = $topregion_name;
            $files_a[$country][$topregion]['subregion'][$subregion]['name'] = $subregion_name;
            $files_a[$country][$topregion]['subregion'][$subregion]['station'][$station]['name'] = $station_name;
            $files_a[$country][$topregion]['subregion'][$subregion]['station'][$station]['f'][] = $file;
        }
        return array($files_r,$files_f,$files_a);
    }

    /**
     * load a dataset
     */
    public function actionLoad()
    {
        set_time_limit(600);
        $cls = new weather_data;
        // load all?
        if (strpos($this->arg_prnfile,'-ALL'))
        {
            // extract parameters
            // $tmp = str_replace('-ALL','',$this->arg_prnfile);
            // $f = explode('-',$tmp);
            $tmp = explode('-',$this->arg_prnfile);
            $arg_country = $tmp[0];
            $arg_station = $tmp[1];

            $compile = array();
            foreach($this->getWeatherFiles($this->arg_wtype) as $file)
            {
                if ($file['country']==$arg_country && ($arg_station=='ALL' || $file['station']==$arg_station))
                {
                    $prnfile = $file['subdir'] . '/' . $file['file'];
                    $compile[] = $cls->load($prnfile,$this->arg_wtype);
                }
            }
            return $compile;
        } else
        {
            return $cls->load($this->arg_prnfile,$this->arg_wtype);
        }
    }

    /**
     * delete a dataset
     */
    public function actionDelete()
    {
        set_time_limit(600);
        $cls = new weather_data;
        // load all?
        if (strpos($this->arg_prnfile,'-ALL'))
        {
            // extract parameters
            $tmp = explode('-',$this->arg_prnfile);
            $arg_country = $tmp[0];
            $arg_station = $tmp[1];
            $dset = array('country'=>$arg_country,'station'=>$arg_station);
            $compile = array();
            $all = $cls->getDatasets($dset,$this->arg_wtype);
            foreach($all as $file)
            {
                $compile[] = $file;
                $cls->deleteDataSet($file->id, $dset, $this->arg_wtype, false);
            }
            return $compile;
        } else
        {
            // get dataset
            $cls3 = new datafiles;
            $dataset = $cls3->getDatasetFromFilename($this->arg_prnfile);
            // delete dataset
            $dset = $cls->getDatasets($dataset,$this->arg_wtype);
            $cls->deleteDataSet($dset[0]->id, $dataset, $this->arg_wtype, false);
            return $dset;
        }
    }

    /**
     * @todo : documentation
     * @param type $wtype
     * @return boolean
     */
    public function getWeatherFiles($wtype)
    {
        $cls3 = new datafiles;

        // weather datasets
        $w_sets = $this->getAllDatasets(weather_data::getDatasets(array(),$wtype));

        $files = werise_weather_file::getFileList($wtype);
        if ($files)
        {
            $sort = array();
            foreach ($files as $key => $file)
            {
                $arr = $cls3->getDatasetFromFilename($file['file']);
                $arr['subdir'] = $file['subdir'];

                // sort key
                $idx = $arr['country'].$arr['station'].'.'.$arr['year'];
                $sort[] = $idx;

                // is loaded?
                $arr['is_loaded'] = false;
                if (isset($w_sets[$idx]))
                {
                    $arr['is_loaded'] = $w_sets[$idx]['rec'];
                    $w_sets[$idx]['file_exist'] = true;
                }
                $files[$key] = $arr;
            }
            array_multisort($sort,SORT_ASC,$files);
        }
        return $files;
    }

    /**
     * @todo : documentation
     * @param type $rs
     * @return boolean
     */
    private function getAllDatasets($rs)
    {
        $rs2 = array();
        foreach ($rs as $rec)
        {
            $key = $rec->country_code.$rec->station_id.".".$rec->year;
            $rs2[$key] = array('rec'=>$rec,'file_exist'=>false);
        }
        return $rs2;
    }
}

class weather_action_grasp extends admin_weather_action {
    public $station_info;
    private $upload_dir;
    public $files_created;

    public function __construct() {
        parent::__construct();
        $this->upload_dir = _APP_DIR . '..'.DIRECTORY_SEPARATOR.'logs'.DIRECTORY_SEPARATOR.'grasp' . DIRECTORY_SEPARATOR;
    }
    public function upload() {
        $tmp = explode('-',$this->arg_prnfile);
        $arg_country = $tmp[0];
        $arg_station = $tmp[1];
        // station info
        $this->station_info = $this->getStationRecord($arg_country,$arg_station);
        if (count($_FILES)>0) {
            $this->deleteOldFiles();
            $this->uploadFiles();
            $this->processFiles($arg_country,$arg_station);
        }
    }

    private function deleteOldFiles() {
        foreach(werise_cdfdm_file::getTypes() as $file) {
            @unlink("{$this->upload_dir}{$file}.txt");
        }
    }

    private function uploadFiles() {
        foreach ($_FILES as $key => $file) {
            if (isset($file["tmp_name"])) {
                move_uploaded_file($file["tmp_name"], "{$this->upload_dir}{$key}.txt");
            }
        }
    }

    private function processFiles($country_code,$station_id) {
        // grab upload
        $grasp = new werise_cdfdm_fileload;
        foreach(werise_cdfdm_file::getTypes() as $ftype) {
            $grasp->loadFile("{$this->upload_dir}{$ftype}.txt",$ftype);
        }
        // convert to ORYZA2000 files
        $sintex_oryza = new werise_sintex_oryza;
        $sintex_oryza->createFiles($country_code, $station_id, $grasp->raw, false, werise_weather_properties::_REALTIME);
        $this->files_created = $sintex_oryza->files_created;
    }
}
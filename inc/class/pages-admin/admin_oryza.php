<?php
define('_CURRENT_OPT','Administration &raquo; Oryza2000 Interface');

class admin_oryza
{
    public $action;
    public $action_ret;
    public $files;
    public $fileprops;
    
    public $dataset_info;
    public $dataset_station;
    public $dataset_data;

    private $station_names;
    private $ver;

    public function __construct() {
        // requested action
        $this->action = 'list';
        if (isset($_GET['action']))
        {
            $this->action = $_GET['action'];
        }

        $action_loader = new oryza_action;

        $fcn_ret = false;
        $this->action_ret = false;
        switch($this->action)
        {
            case 'list':
                list($this->files['r'],$this->files['f'],$this->files['a']) = $this->actionList();
                $this->fileprops['r'] = array('container-id'=>'realtime-container','title'=>'Historical Data');
                $this->fileprops['f'] = array('container-id'=>'forecast-container','title'=>'Forecast Data');
                break;
            case 'load':
                $this->action_ret = $action_loader->actionLoad();
                break;
            case 'del':
                try {
                    $action_loader->actionDelete();
                    $this->action_ret = array(true,'');
                } catch (Exception $e)
                {
                    $this->action_ret = array(false,$e->getMessage());
                }                    
                break;
            case 'detail':
                list($this->dataset_info,$this->dataset_station,$this->dataset_data) = $action_loader->actionDetails();
                break;                    
            case 'oryza_err':
                $this->action_ret = $action_loader->actionOryzaError();
                break;

        }
        $this->ver = _opt(sysoptions::_ORYZA_VERSION);
    }
    /**
     * display the list of files available
     */
    public function actionList()
    {
       $db = Database_MySQL::getInstance();

        // station name reference for later
        $sql = "SELECT * FROM `weather_stations`";
        $this->station_names = $db->getRowList($sql);
        // realtime files
        $files_r = $this->getWeatherFiles(werise_weather_properties::_REALTIME);
        // forecast files
        $files_f = $this->getWeatherFiles(werise_weather_properties::_FORECAST);
        // station / region names
        foreach (weather_stations::getAll() as $station)
        {
            $idx = $station->country_code.$station->station_id;
            $station_names[$idx] = $station;
        }
        // merge
        $files_a = array();
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
     * @todo : documentation
     * @param type $wtype
     * @return boolean
     */
    public function getWeatherFiles($wtype)
    {
        $cls3 = new datafiles;

        // weather datasets
        $w_sets = $this->getAllDatasets(weather_data::getDatasets(array(),$wtype));

        // oryza datasets
        $o_sets = $this->getAllDatasets(oryza_data::getDatasets(array('wtype'=>$wtype)));

        $files = werise_weather_file::getFileList($wtype);        
        if ($files)
        {
            foreach ($files as $key => $file)
            {
                $arr = $cls3->getDatasetFromFilename($file['file']);
                $arr['subdir'] = $file['subdir'];

                // sort key
                $idx = $arr['country'].$arr['station'].'.'.$arr['year'];
                $sort[] = $idx;

                // is loaded
                $arr['is_loaded'] = false;
                if (isset($w_sets[$idx]))
                {
                    $arr['is_loaded'] = $w_sets[$idx]['rec'];
                    $w_sets[$idx]['file_exist'] = true;
                }

                // is oryza loaded
                $arr['is_oryza_loaded'] = false;
                if (isset($o_sets[$idx]))
                {
                    $arr['is_oryza_loaded'] = $o_sets[$idx]['rec'];
                    $o_sets[$idx]['file_exist'] = true;
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

    /**
     * @todo : documentation
     * @param type $country
     * @param type $station
     * @return type
     */
    public function getStationName($country,$station_id)
    {
        foreach($this->station_names as $station)
        {
            if ($country==$station->country_code && $station_id==$station->station_id)
            {
                return $station->station_name;
            }
        }
        return "unknown station [{$country}-{$station_id}]";
    }

    public function getLoadAllKey($file)
    {
        // extract station
        //$f = preg_replace('/[0-9]+/', '', $file['file']);
        $f = $file['file'];
        return $file['subdir'] . '/' . $f . '-ALL';
    }
    
    public function showActionElements($file,$wtype)
    {        
        $html = '';
        $tmp = '<a class="btn btn-small" href="admin.php?pageaction=oryza&action=load&amp;dst=o&amp;fert=8&amp;type='. $wtype . '&amp;prnfile=' . $file['subdir'] . '/' . $file['file'] . '"><i class="icon-download"></i> Load</a>&nbsp;';
        $html .= $tmp;
        if ($file['is_oryza_loaded'])
        {
            $tmp2 = '<a class="btn btn-small" href="admin.php?pageaction=oryza&action=del&amp;dst=o&amp;type='. $wtype . '&amp;prnfile=' . $file['subdir'] . '/' . $file['file'] . '"><i class="icon-remove"></i> Delete</a>';
            $html .= $tmp2;
        }
        return $html;
    }
    
    public function showNotes($file)
    {        
        $notes = array();
        if ($file['is_loaded']) {
            $notes[] = "{$file['is_loaded']->notes}";
            if ($file['is_loaded']->oryza_ver!=$this->ver) {
                $notes[] = 'weather version incompatible';
            }
        }
        if ($file['is_oryza_loaded']) {
            if ($file['is_oryza_loaded']->oryza_ver!=$this->ver) {
                $notes[] = 'oryza version incompatible';
            }
        }        
        return implode('<br />',$notes);        
    }
}

class admin_weather_action
{
    protected $db;

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
        if (isset($_GET['prnfile']))
        {
            $this->arg_prnfile = $_GET['prnfile'];
        }

        // url parameter: realtime or forecast
        $this->arg_wtype = '';
        if (isset($_GET['type']))
        {
            $this->arg_wtype = $_GET['type'];
        }

        // url parameter: set-id
        $this->arg_setid = 0;
        if (isset($_GET['setid']))
        {
            $this->arg_setid = intval($_GET['setid']);
        }
    }
}

class oryza_action extends admin_weather_action
{
    /**
     * load a dataset
     */
    public function actionLoad() {
        $ret = array();
        // determine dataset from file
        $datafile = new datafiles;
        $dataset = $datafile->getDatasetFromFilename($this->arg_prnfile);
        // loop thru all combinations
        foreach ($this->getVarieties($dataset['country']) as $variety) {
            foreach ($this->getFert() as $f) {
                $dset = $dataset;
                $dset['variety'] = $variety;
                $dset['fert'] = $f;
                try {
                    // execute oryza2000
                    $oryza2000 = new oryza2000_api;
                    $oryza_ok = $oryza2000->exec($dataset, $variety, $f, $this->arg_wtype);
                } catch (Exception $e)
                {
                    $ret[] = array(false,array(
                        'error' => $e->getMessage(),
                        'dataset' => $dset));
                    break 2;
                }                    
                try {
                    if ($oryza_ok)
                    {
                        // save to database
                        $cls = new oryza_data;
                        $load_ret = $cls->load($oryza2000);
                        $ret[] = array(true,$load_ret);
                    }
                } catch (Exception $e)
                {
                    $ret[] = array(false,array(
                        'error' => $e->getMessage(),
                        'dataset' => $dset));
                    break 2;
                }
            }
        }
        werise_oryza_cropcalendar::updateCalendars();
        return $ret;
    }

    /**
     * delete a dataset
     */
    public function actionDelete()
    {
        $cls3 = new datafiles;
        $dataset = $cls3->getDatasetFromFilename($this->arg_prnfile);
        $sql = "
            SELECT `id`
            FROM "._DB_DATA.".`oryza_dataset`
            WHERE country_code = '{$dataset['country']}'
                AND station_id = {$dataset['station']}
                AND year = {$dataset['year']}
                AND wtype = '{$this->arg_wtype}'";
        $rs = $this->db->getRowList($sql);
        $cls = new oryza_data;
        foreach($rs as $rec)
        {
            $cls->deleteDataSet($rec->id, false);
        }
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

    private function getFert()
    {
        // load test override
        if (_opt(sysoptions::_ADM_ORYZA_LOAD_TEST))
        {
            return array(advisory_fertilizer::_FERT_NONE);
        }
        // url parameter: fert
        $fert = 0;
        if (isset($_GET['fert']))
        {
            $valid_fert = array(0,1,2,8,9);
            $fert = intval($_GET['fert']);
            if (!in_array($fert,$valid_fert))
            {
                $fert = advisory_fertilizer::_FERT_NONE;
            }
        }
        $ferts = array($fert);
        if ($fert==8)
        {
            $ferts = array(advisory_fertilizer::_FERT_NONE);
            if (_opt(sysoptions::_ORYZACHART_SHOW_GENFERT))
            {
                $ferts[] = advisory_fertilizer::_FERT_GEN;
            }
        }
        if ($fert==9)
        {
            $ferts = array(advisory_fertilizer::_FERT_NONE);
            if (_opt(sysoptions::_ORYZACHART_SHOW_GENFERT))
            {
                $ferts[] = advisory_fertilizer::_FERT_GEN;
            }
            if (_opt(sysoptions::_ORYZACHART_SHOW_RCMFERT))
            {
                $ferts[] = advisory_fertilizer::_FERT_SPC;
            }
        }
        return $ferts;
    }

    /**
     * get varieties from sysopts
     * @param type $country
     * @return type
     */
    private function getVarieties($country)
    {
        $ret = array();

        foreach (explode(',',_opt(sysoptions::_ORYZA_VARIETIES)) as $rec)
        {
            $ret[] = trim($rec);
        }

        // local checks
        switch ($country)
        {
            case 'PH':
                $ret[] = _opt(sysoptions::_ORYZA_LCHK_PH);
                break;
            case 'ID':
                $ret[] = _opt(sysoptions::_ORYZA_LCHK_ID);
                break;
            case 'TH':
                $ret[] = _opt(sysoptions::_ORYZA_LCHK_TH);
                break;
            case 'LA':
                $ret[] = _opt(sysoptions::_ORYZA_LCHK_LA);
                break;
        }

        if (_opt(sysoptions::_ADM_ORYZA_LOAD_TEST))
        {
            return array($ret[0]);
        }

        // make sure there are no duplicates
        return array_unique($ret);
    }

    public function actionOryzaError()
    {

    }
}
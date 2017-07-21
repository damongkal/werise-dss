<?php
define('_CURRENT_OPT','Administration &raquo; Weather Files Acknowledgement');

class admin_acknowledge
{
    public $action;
    public function __construct()
    {
        if (isset($_GET['action']))
        {
            $this->action = $_GET['action'];
        }        
    }
    
    /**
     * display the list of files available
     */
    public function actionList()
    {
        // realtime files
        $files_r = $this->getWeatherFiles(werise_weather_properties::_REALTIME);
        // forecast files
        $files_f = $this->getWeatherFiles(werise_weather_properties::_FORECAST);
        return array(
            array('title' => 'Real-Time', 'wtype'=>'r', 'files' => $files_r),
            array('title' => 'Forecast', 'wtype'=>'f', 'files' => $files_f));
    }

    /**
     * @todo : documentation
     * @param type $wtype
     * @return boolean
     */
    public function getWeatherFiles($wtype)
    {
        $cls3 = new datafiles;

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
                $files[$key] = $arr;
            }
            array_multisort($sort,SORT_ASC,$files);
        }
        return $files;
    }

    public function loadPrn($file,$wtype)
    {
        $prn = $file['subdir'] . '/' . $file['file'];
        $cls = new werise_weather_acknowledge;
        $cls->load($prn,$wtype);
    }
}
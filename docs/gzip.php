<?php
class compress_files
{
    const _ENABLE_CACHE = false;
    
    public function execute()
    {
        $filelist = $this->getFileList();
        if ($filelist)
        {
            $this->output($filelist);
        }
    }
    
    private function getFileList()
    {
        // filegroup
        if (isset($_REQUEST['group']))
        {
            return $this->getFileGroup($_REQUEST['group']);
        }
        // filelist
        $filelist = '';
        if (isset($_REQUEST['file']))
        {
            $filelist = $_REQUEST['file'];
        }
        if ($filelist!=='')
        {
            $filelist2 = explode(',', $filelist);
            if (count($filelist2)<10)
            {
                return $filelist2;
            }
        }
        return false;
    }
    
    private function getFileGroup($group)
    {
        switch($group)
        {
            case 'common':
                $filelist = 'dss-common-imgloader,dss-common-date,dss-common-translate,dss-common-terms,dss-common-dropdown';
                break;
            case 'weather':    
                $filelist = 'dss-class-wadvisory,dss-weather-onload,dss-weather-chart,dss-weather-raw,dss-weather-map';
                break;
            case 'oryza':
                $filelist = 'dss-class-wadvisory,dss-class-cropcalendar,dss-oryza-chart,dss-oryza-wchart,dss-oryza-advisory,dss-oryza-raw,dss-weather-map,dss-oryza-combilist,dss-oryza-onload';
                break;
            case 'oryzaadmin':
                $filelist = 'dss-oryzaadm-chart';
                break;
            default: 
                return false;
        }
        return explode(',', $filelist);
    }
    
    private function output($filelist)
    {  
        $file_ext = array('javascript'=>'js','css'=>'css');  
        $file_type = $this->getFileType();
        ob_start();
        if (self::_ENABLE_CACHE)
        {
            header("Cache-Control: must-revalidate");
            $expires = "Expires: " . gmdate("D, d M Y H:i:s", time() + 3600) . " GMT";
            header($expires);        
        }
        
        header("Content-type: text/{$file_type}; charset: UTF-8");
        
        foreach($filelist as $filename) {
            $f = trim($filename);
            if ($f!=='')
            {
                require_once("js/{$f}.{$file_ext[$file_type]}");
                echo "\n";
            }
        }
        $gzip = ob_get_contents();
        ob_end_clean();
        echo $gzip;
    }
    
    private function getFileType()
    {
        $valid_type = array('javascript','css');
        if (isset($_REQUEST['type']) && in_array($_REQUEST['type'],$valid_type))
        {
            return $_REQUEST['type'];
        }    
        return $valid_type[0];
    }       
}
$gzip = new compress_files;
$gzip->execute();
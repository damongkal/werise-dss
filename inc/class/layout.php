<?php
class layout
{
    const _V1 = 'v1';
    const _V2 = 'v2-irri';

    private $layout;
    private $header;
    private $footer;

    public function __construct($file='') {
        if ($file==='')
        {
            $file = self::_V2;
        }
        // expose pageaction to layout
        $pageaction = 'index';
        if (isset($_REQUEST['pageaction'])) {
            $pageaction = $_REQUEST['pageaction'];
        }        
        // load layout
        ob_start();
        include_once(_CLASS_DIR.'layout'.DIRECTORY_SEPARATOR.'layout-template-'.$file.'.php');
        $this->layout = ob_get_contents();
        ob_end_clean();

        // special rules for IRRI template
        if ($file===self::_V2)
        {
            $this->processIRRILayout();
        }

        // separate header / footer
        $tmp = explode('-content-',$this->layout);
        $this->header = $tmp[0];
        $this->footer = $tmp[1];
    }

    private function processIRRILayout()
    {
        $tmp = 'href="/';
        $this->layout = str_replace($tmp,'href="http://irri.org/',$this->layout);
    }

    public function getHeader()
    {
        return $this->header;
    }

    public function getFooter()
    {
        ob_start();
        debug::getInstance()->showLog();
        $debug_log = ob_get_contents();
        ob_end_clean();
        return str_replace('<debug></debug>',$debug_log,$this->footer);
    }

}
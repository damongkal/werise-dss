<?php
class layout
{
    private $layout;
    private $header;
    private $footer;
    private $layout_dir;
    private $pageaction;

    public function __construct($pageaction) {
        $this->pageaction = $pageaction;
        
        // load layout
        ob_start();
        $layout2 = array(
            'admin_index', 'admin_phpinfo', 'admin_config',
            'admin_users', 'admin_webusage', 'admin_varieties',
            'admin_rcm', 'admin_stations'
            );
        if (strpos($pageaction,'form_')===0 || in_array($pageaction,$layout2)) {
            $this->layout_dir = 'layout-v2';
        } else {
            $this->layout_dir = 'layout';
            $this->processIRRILayout();            
        }
        include_once(_CLASS_DIR.$this->layout_dir.DIRECTORY_SEPARATOR.'layout-template.php');        
        $this->layout = ob_get_contents();
        ob_end_clean();

        // separate header / footer
        $tmp = explode('-content-',$this->layout);
        $this->header = $tmp[0];
        $this->footer = $tmp[1];
    }
    
    public function getLayoutContent() {
        return _CLASS_DIR.$this->layout_dir.DIRECTORY_SEPARATOR . $this->pageaction . '.php';
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
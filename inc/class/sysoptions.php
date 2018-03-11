<?php
class sysoptions
{
    protected $sysopts;
    protected $default;

    // database
    const _DB_DATA = '_DB_DATA';

    // admin
    const _ADM_SHOW_MENU = '_ADM_SHOW_MENU';
    const _ADM_SHOW_LOAD_WEATHER_DETAIL = '_ADM_SHOW_LOAD_WEATHER_DETAIL';
    const _ADM_ORYZA_LOAD_TEST = '_ADM_ORYZA_LOAD_TEST';

    // oryza2000
    const _ORYZA_VERSION = '_ORYZA_VERSION';
    const _ORYZA_VARIETIES = '_ORYZA_VARIETIES';
    const _ORYZA_LCHK_ID = '_ORYZA_LCHK_ID';
    const _ORYZA_LCHK_LA = '_ORYZA_LCHK_LA';
    const _ORYZA_LCHK_TH = '_ORYZA_LCHK_TH';
    const _ORYZA_LCHK_PH = '_ORYZA_LCHK_PH';
    const _ORYZA_NITROENV_NOFERT = '_ORYZA_NITROENV_NOFERT';
    const _ORYZA_NITROENV_GENFERT = '_ORYZA_NITROENV_GENFERT';
    const _ORYZA_NITROENV_SPCFERT = '_ORYZA_NITROENV_SPCFERT';
    const _ORYZA_DBG_RES = '_ORYZA_DBG_RES';
    const _ORYZA_INTERVAL = '_ORYZA_INTERVAL';

    // page options
    const _OPT_SHOW_DATAGRID = '_OPT_SHOW_DATAGRID';
    const _OPT_GOOGLE_ANALYTICS = '_OPT_GOOGLE_ANALYTICS';
    const _SHOW_MAP = '_SHOW_MAP';
    const _SHOW_HISTORICAL = '_SHOW_HISTORICAL';

    // oryza chart options
    const _ORYZACHART_SHOW_GENFERT = '_ORYZACHART_SHOW_GENFERT';
    const _ORYZACHART_SHOW_RCMFERT = '_ORYZACHART_SHOW_RCMFERT';
    const _ORYZACHART_SHOW_ALLDATES = '_ORYZACHART_SHOW_ALLDATES';
    const _ORYZACHART_SHOW_NPK = '_ORYZACHART_SHOW_NPK';
    const _ORYZACHART_NEWWINDOW = '_ORYZACHART_NEWWINDOW';

    protected static $instance = null;
    protected function __construct()
    {
        //Thou shalt not construct that which is unconstructable!
    }
    protected function __clone()
    {
        //Me not like clones! Me smash clones!
    }

    public static function getInstance()
    {
        if (!isset(self::$instance))
        {
            $className = __CLASS__;
            self::$instance = new $className;
        }
        return self::$instance;
    }

    public function init()
    {
        $this->getDefaults();
        $db = Database_MySQL::getInstance();
        $tmp = $db->getRowList('SELECT * FROM `system_options`');
        $this->sysopts = array();
        foreach ($tmp as $rec) {
            if (isset($this->default[$rec->id])) {
                $type = $this->default[$rec->id]['type'];
                $this->sysopts[$rec->id] = $this->convertToType($type, $rec->ovalue);
            }
        }
        $this->setConstants();
    }

    /**
     * set constants that is used
     * by whole application
     */
    private function setConstants()
    {
        // database name for weather and oryza data
        define('_DB_DATA', $this->get('_DB_DATA'));
        // oryza2000 subfolder
        switch ($this->get(sysoptions::_ORYZA_VERSION))
        {
            case '3':
                define('_DATA_SUBDIR_ORYZA', _ORYZA3_DIR);
                break;
            default:
                define('_DATA_SUBDIR_ORYZA', _ORYZA_DIR);
        }
        
        // weather date file config
        define('_DATA_SUBDIR_WEATHER_REALTIME', 'weather_ver1'.DIRECTORY_SEPARATOR.'realtime'.DIRECTORY_SEPARATOR);
        define('_DATA_SUBDIR_WEATHER_FORECAST', 'weather_ver1'.DIRECTORY_SEPARATOR.'forecast'.DIRECTORY_SEPARATOR);
        define('_DATA_SUBDIR_SINTEX', _DATA_DIR.'sintex'.DIRECTORY_SEPARATOR);        

        werise_stations_country::init();
    }

    private function getDefaults()
    {
        // db
        $this->default[] =  array('value' => 'category','desc' => 'Database');        
        $this->default[self::_DB_DATA] = array(
            'value' => _DB_NAME,
            'desc' => 'Database name of Weather and Oryza2000 data');

        // admin
        $this->default[] =  array('value' => 'category','desc' => 'Admin');
        $this->default[self::_ADM_SHOW_MENU] = array(
            'value' => false,
            'desc' => 'Show the Admin menu?');
        /*
        $this->default[self::_ADM_SHOW_LOAD_WEATHER_DETAIL] =  array(
            'value' => false,
            'desc' => 'Show processing details of "Weather Data Files" section?');*/
        $this->default[self::_ADM_ORYZA_LOAD_TEST] =  array(
            'value' => false,
            'desc' => 'Load only 1 set for Oryza2000? Set to TRUE to quickly test the result of "Oryza2000 Interface" section.');

        // oryza2000
        $this->default[] =  array('value' => 'category','desc' => 'Oryza2000');
        $this->default[self::_ORYZA_VERSION] =  array(
            'value' => '3',
            'desc' => 'Version of Oryza2000 to use. 1 or 3?');
        $this->default[self::_ORYZA_INTERVAL] =  array(
            'value' => 15,
            'desc' => 'RERUN STTIME interval to use. 1, 5 or 15?');        
        $this->default[self::_ORYZA_VARIETIES] =  array(
            'value' => 'IR64.J96,IR72.DAT,YTH183.D12',
            'desc' => 'List of varieties to use in Oryza2000 runs. Example: IR64.J96,IR72.DAT,YTH183.D12');
        $this->default[self::_ORYZA_LCHK_ID] =  array(
            'value' => 'IR64.J96',
            'desc' => 'Local Check variety for Indonesia.');
        $this->default[self::_ORYZA_LCHK_LA] =  array(
            'value' => 'KDML105.DAT',
            'desc' => 'Local Check variety for Lao PDR.');
        $this->default[self::_ORYZA_LCHK_TH] =  array(
            'value' => 'KDML105.DAT',
            'desc' => 'Local Check variety for Thailand.');
        $this->default[self::_ORYZA_LCHK_PH] =  array(
            'value' => 'IR72.DAT',
            'desc' => 'Local Check variety for Philippines.');
        $this->default[self::_ORYZA_NITROENV_NOFERT] =  array(
            'value' => 'POTENTIAL',
            'desc' => 'RERUN.DAT NITROENV value for no fertilizer. POTENTIAL or NITROGEN BALANCE?');
        $this->default[self::_ORYZA_NITROENV_GENFERT] =  array(
            'value' => 'POTENTIAL',
            'desc' => 'RERUN.DAT NITROENV value for general fertilizer recommendation. POTENTIAL or NITROGEN BALANCE?');
        $this->default[self::_ORYZA_NITROENV_SPCFERT] =  array(
            'value' => 'POTENTIAL',
            'desc' => 'RERUN.DAT NITROENV value for specific fertilizer recommendation. POTENTIAL or NITROGEN BALANCE?');
        $this->default[self::_ORYZA_DBG_RES] =  array(
            'value' => false,
            'desc' => 'display res.dat debug info?');        

        // page options
        $this->default[] =  array('value' => 'category','desc' => 'Page Display Options');
        $this->default[self::_OPT_SHOW_DATAGRID] =  array(
            'value' => false,
            'desc' => 'Show raw data used in generating the charts?');
        $this->default[self::_OPT_GOOGLE_ANALYTICS] =  array(
            'value' => false,
            'desc' => 'Enable Google Analytics?');
        $this->default[self::_SHOW_HISTORICAL] =  array(
            'value' => false,
            'desc' => 'Show Historical Data?');        

        // oryza chart options
        $this->default[] =  array('value' => 'category','desc' => 'Chart Options');
        $this->default[self::_ORYZACHART_SHOW_GENFERT] =  array(
            'value' => true,
            'desc' => 'Show "General Fertilizer" option in dropdown list?');
        $this->default[self::_ORYZACHART_SHOW_RCMFERT] =  array(
            'value' => false,
            'desc' => 'Show "Specific Fertilizer" option in dropdown list?');
        $this->default[self::_ORYZACHART_SHOW_ALLDATES] =  array(
            'value' => false,
            'desc' => 'Show all available date info in cropping schedule like Panicle Initiation?');
        $this->default[self::_ORYZACHART_SHOW_NPK] =  array(
            'value' => false,
            'desc' => 'Show NPK values used in fertilizer recommendation');
        $this->default[self::_ORYZACHART_NEWWINDOW] =  array(
            'value' => false,
            'desc' => 'Display results in new window');
        $this->default[self::_SHOW_MAP] =  array(
            'value' => false,
            'desc' => 'Use Google Maps to show the stations');

        // determine datatype
        foreach ($this->default as $key => $rec) {
            $this->default[$key]['type'] = $this->getType($rec['value']);
        }
    }

    private function getType($val) {
        if (is_bool($val)) {
            return 'boolean';
        }
        if (is_string($val)) {
            return 'string';
        }
        if (is_numeric($val)) {
            return 'number';
        }
        return 'string';
    }

    public function convertToType($type, $origval) {
        switch ($type) {
            case 'number':
                $val = intval($origval);
                break;
            case 'boolean':
                $val = (bool) $origval;
                break;
            case 'string':
            default:
                $val = $origval;
                break;
        }
        return $val;
    }

    public function get($var)
    {
        // get default
        $val = $this->getDefault($var);
        $is_bool = is_bool($val);
        // get db
        if (isset($this->sysopts[$var]))
        {
            $val = $this->sysopts[$var];
        }

        if (_ADM_ENV==='PROD')
        {
            // get hardcoded values for PROD
            if ($var===sysoptions::_ADM_SHOW_MENU)
            {
                $val = false;
            }
            if ($var===sysoptions::_OPT_SHOW_DATAGRID)
            {
                $val = false;
            }
        } else
        {
            // get hardcoded values for STAGE
            if ($var===sysoptions::_OPT_GOOGLE_ANALYTICS)
            {
                $val = false;
            }
        }
        if ($is_bool)
        {
            return (bool)$val;
        }
        return $val;
    }

    public function set()
    {

    }

    public function getAll()
    {
        return array($this->default,$this->sysopts);
    }

    private function getDefault($var)
    {
        if (isset($this->default[$var]))
        {
            return $this->default[$var]['value'];
        }
        return 'nodefault';
    }

    public static function update($key,$val)
    {
        $db = Database_MySQL::getInstance();
        $sql = 'INSERT INTO `system_options` (`id`,`ovalue`) VALUES (\'%1$s\',\'%2$s\') ON DUPLICATE KEY UPDATE `ovalue`=\'%2$s\'';
        $db->query(sprintf($sql,$db->escape($key),$db->escape($val)));
    }

}

function _opt($var)
{
    return sysoptions::getInstance()->get($var);
}
<?php
include_once('../inc/class/config.php');

// weather date file config
define('_DATA_SUBDIR_WEATHER_REALTIME', 'data/weather/realtime/');
define('_DATA_SUBDIR_WEATHER_FORECAST', 'data/weather/forecast/');
$GLOBALS['weather_files'] = array (
    'ID' => array (
        'dir' => 'Indo/',
        'file' => 'INDON',
        'country' => 'Indonesia'
    ),
    'LA' => array (
        'dir' => 'Laos/',
        'file' => 'LAOS',
        'country' => 'Laos'
    ),    
    'PH' => array (
        'dir' => 'Phil/',
        'file' => 'PHIL',
        'country' => 'Philippines'
    ),
    'TH' => array (
        'dir' => 'Thai/',
        'file' => 'THAI',
        'country' => 'Thailand'
    )
);

// oryza2000 config
define('_DATA_SUBDIR_ORYZA', 'oryza/');

function __autoload($class_name)
{
    include_once(_CLASS_DIR . $class_name . '.php');
}

function admin_auth()
{
    if (!isset($_SERVER['PHP_AUTH_USER'])) {
        header('WWW-Authenticate: Basic realm="DSS CCARA Admin"');
        header('HTTP/1.0 401 Unauthorized');
        echo 'You are not allowed to this section.';
        exit;
    } else {
        if ( $_SERVER['PHP_AUTH_USER']!=_ADM_USER || $_SERVER['PHP_AUTH_PW']!=_ADM_PWD)
        {
            echo ('Invalid Administator login');
            exit;
        }
    }    
}
<?php
function _makepath($path,$with_trailing_dirsep = true) {
    $newpath = realpath($path);
    if (!$newpath) {
        throw new Exception('folder not found: '.$path) ;
    }    
    if ($with_trailing_dirsep) {
        $newpath .= DIRECTORY_SEPARATOR;
    }
    return $newpath;
}
error_reporting(E_ALL);
ini_set('display_errors',1);
//error_reporting(0);
session_start();
date_default_timezone_set('UTC');
include_once('../inc/class/config.php');
// load debugger at once
include_once(_CLASS_DIR.'debug.php');
// remember root app directory
define('_APP_DIR', dirname(__FILE__).DIRECTORY_SEPARATOR);

spl_autoload_register(function ($class_name) {
    $dir = "";
    if (strpos($class_name, 'werise_')!==false)
    {
        $tmp = explode('_',$class_name);
        array_pop($tmp);
        $dir = implode(DIRECTORY_SEPARATOR,$tmp);
    }    
    if (strpos($class_name, 'admin_')!==false)
    {
        $dir = 'pages-admin';
    }
    if (strpos($class_name, 'form_')!==false)
    {
        $dir = 'pages-form';
    }
    if (strpos($class_name, 'ajax_')!==false)
    {
        $dir = 'pages-ajax';
    }
    $phpfile = _CLASS_DIR . $dir . DIRECTORY_SEPARATOR . $class_name . '.php';
    debug::getInstance()->addLog($phpfile,false,'LoadClass');
    include_once($phpfile);
});

// system options
sysoptions::getInstance()->init();

// internationalization
language::getInstance()->init();

// save latest dataset selection
dss_utils::saveLastSelection();

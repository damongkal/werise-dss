<?php
include('bootstrap.php');
$valid_admin = array(
    'config', 'acknowledge', 'export',
    'import', 'oryza', 'phpinfo',
    'rcm', 'stations', 'weatherfile',
    'cdfdm_convert','help','users',
    'weatherref','oryzaref','cdfdm',
    'varieties','webusage'
);
$pageaction = 'admin_index';
if (isset($_REQUEST['pageaction'])) {
    if (in_array($_REQUEST['pageaction'],$valid_admin))
    {
        $pageaction = 'admin_'.$_REQUEST['pageaction'];
    } else
    {
        $debug = debug::getInstance()->addLog('invalid admin: '.$_REQUEST['pageaction']);    
    }
}

if ($pageaction=='admin_help' || _ADM_ENV!='PROD' || (_ADM_ENV==='PROD' && dss_auth::getUsername()=='admin'))
{
    $allow = true;
} else
{
    define('_CURRENT_OPT','');
    $allow = false;    
}

// class action
if ($allow)
{
    $cls = new $pageaction; // $cls can be referenced in layout files
}
$layout = new layout($pageaction);
echo $layout->getHeader();
// class content
if ($allow)
{
    include $layout->getLayoutContent();
}
echo $layout->getFooter();
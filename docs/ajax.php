<?php
include('bootstrap.php');

$valid_admin = array(
    'lookup', 'weather', 'oryza', 'config', 'map', 'weather2', 'oryza2'
);
$pageaction = 'ajax_index';
if (isset($_REQUEST['pageaction']) && in_array($_REQUEST['pageaction'],$valid_admin))
{
    $pageaction = 'ajax_'.$_REQUEST['pageaction'];
}
$cls = new $pageaction;

if (!dss_utils::isAjax())
{
    debug::getInstance()->addLog($cls->json_ret,true,'RETURN');
    debug::getInstance()->showLog('inline');
} else
{
    echo json_encode($cls->json_ret);
}
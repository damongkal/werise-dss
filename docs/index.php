<?php
include('bootstrap.php');

try {
    werise_core_browser::isCompatible();
} catch (Exception $e) {
    echo $e->getMessage();
    exit;
}

$valid_admin = array(
    'about', 'terms', 'weather', 'oryza', 'lookup','register'
);
$pageaction = 'form_index';
if (isset($_REQUEST['pageaction']) && in_array($_REQUEST['pageaction'], $valid_admin)) {
    $pageaction = 'form_' . $_REQUEST['pageaction'];
}

$secured = array('form_weather','form_oryza');
if (_ADM_ENV==='PROD' && in_array($pageaction,$secured))
{
    dss_auth::checkAccess();
}
dss_auth::logAccess($pageaction);

// route action
$cls = new $pageaction;
// layout
$layout = new layout($pageaction);
echo $layout->getHeader();
include $layout->getLayoutContent();
echo $layout->getFooter();
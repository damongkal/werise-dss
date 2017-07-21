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

$cls = new $pageaction; // referenced in layout files

$layout = new layout();
echo $layout->getHeader();

// content
include(_CLASS_DIR . 'layout' . DIRECTORY_SEPARATOR . $pageaction . '.php');

echo $layout->getFooter();
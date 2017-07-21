<?php
define('_CURRENT_OPT',_t('Home'));
class form_index
{
    public function __construct() {
        if (isset($_GET['lang']))
        {
            $lang = language::getLang();
            if ($lang!='en')
            {
                $_GET['country'] = $lang;
                dss_utils::saveLastSelection(true);
            }
        }
    }
}
<?php

define('_CURRENT_OPT', _t('Weather Advisory'));

class form_weather
{

    public function __construct()
    {

    }

    public function getWvars()
    {
        return array(
            _t('Rainfall'),
            _t('Temperature'),
            _t('Solar Radiation'),
            _t('Early Morning Vapor Pressure'),
            _t('Wind Speed')
        );
    }

}
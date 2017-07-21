<?php
define('_CURRENT_OPT','Administration &raquo; Oryza2000 Data');

class admin_oryzaref
{
    private $stations;
    
    public function __construct() {

    }
    
    public function getStationName($country_code, $station_id)
    {
        $idx = $country_code.$station_id;
        if (!isset($this->stations[$idx]))
        {
            $station = new weather_stations();
            $ret = $station->getStation($country_code, $station_id);            
            $this->stations[$idx] = $ret->station_name;
        }
        return $this->stations[$idx];
    }    
}
<?php
define('_CURRENT_OPT','Administration &raquo; Weather Data');

class admin_weatherref
{
    private $stations;
    private $comments;
    
    public function __construct() {
        $this->comments = weather_data::getComments(false, 'r');
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
    
    public function getComment($setid,$field)
    {
        if ($field==='GEO')
        {
            $lat = '';
            if (isset($this->comments[$setid]['LATITUDE']))
            {
                $lat = $this->comments[$setid]['LATITUDE'];
            }
            $lon = '';
            if (isset($this->comments[$setid]['LONGITUDE']))
            {
                $lon = $this->comments[$setid]['LONGITUDE'];
            }
            $alt = '';
            if (isset($this->comments[$setid]['ALTITUDE']))
            {
                $alt = $this->comments[$setid]['ALTITUDE'];
            }           
            return "{$lat} ; {$lon} ; {$alt}";
        }
        if (isset($this->comments[$setid][$field]))
        {
            return $this->comments[$setid][$field];
        }
        return '-';
    }
}
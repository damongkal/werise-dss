<?php

class ajax_map {

    public $json_ret;
    private $debug;

    public function __construct() {
        $this->debug = debug::getInstance();        
        $this->json_ret = $this->exec();
        $this->debug->addLog($this->json_ret,true);
    }

    private function exec() {
        $vcountries = array('ID','LA','TH','PH');
        $countries = $_GET['country'];
        $source = 'w';
        if (isset($_GET['source']))
        {
            $source = $_GET['source'];
        }
        if (in_array($countries,$vcountries))
        {
            return $this->getMapPoints(array($countries),$source);
        }
    }
    
    private function getMapPoints($countries,$source)
    {
        $ws = new weather_stations;
        $cmp = array();        
        foreach($countries as $country)
        {
            $stations = $ws->getStations($country, $source);
            if ($stations)
            {
                foreach($stations as $station)
                {
                    if (!is_null($station->geo_lat))
                    {
                        $cmp[] = array($station->station_name,floatval($station->geo_lat),floatval($station->geo_lon));
                    }
                }       
            }
        }
        return $cmp;        
    }

}

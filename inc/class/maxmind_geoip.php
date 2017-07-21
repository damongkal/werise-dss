<?php
require_once(_CLASS_DIR.'maxmind/autoload.php');
use GeoIp2\Database\Reader as MaxMind_GeoIP_API;
class maxmind_geoip
{
    private $reader;
    private $record;
    public function __construct($ip=null) {
        if (is_null($ip))
        {
            $ip = 'none';
            if (isset($_SERVER['REMOTE_ADDR']))
            {
                $ip = $_SERVER['REMOTE_ADDR'];
            }
        }
        if ($ip === '127.0.0.1' || $ip === '::1' || $ip === 'none')
        {
            // $ip = '202.136.242.162'; // LAOS
            $ip = '66.218.72.112'; // US
        }
        $reader = new MaxMind_GeoIP_API(_CLASS_DIR.'maxmind/GeoLite2-Country.mmdb');
        $this->record = $reader->country($ip);
    }
    
    public function getIsoCode()
    {
        return $this->record->country->isoCode;
    }
    
    public function getLang()
    {
        $iso = $this->getIsoCode();
        if (in_array($iso,array('ID','LA','TH')))
        {
            return $iso;
        }
        return 'EN';        
    }
}
<?php

class werise_weather_percentile {

    public $pctile_20;
    public $pctile_50;
    public $pctile_80;

    /**
     * compute station percentiles
     * @param array $raw raw decadal data
     * @param string $wvar
     * @return type
     */
    public function getStationPercentile($raw, $station, $wvar, $fieldname='wvar') {
        // try to read from cache
        $cachefile = 'ptile-' . $station->country_code . '-' . $station->station_id . '-r-' . $wvar;
        $cache = new cache($cachefile);
        $cache_data = $cache->read();
        if ($cache_data) {
            $this->pctile_20 = $cache_data[0];
            $this->pctile_50 = $cache_data[1];
            $this->pctile_80 = $cache_data[2];
            return;
        }
        $this->getStationPercentile2($raw, $station, $wvar, $fieldname);
        $percentiles = array($this->pctile_20, $this->pctile_50, $this->pctile_80);
        $cache->write($percentiles);
    }
    
    public function getStationPercentile2($raw, $station, $wvar, $fieldname='wvar', $opts = false) {
        // initialize
        $this->pctile_20 = false;
        $this->pctile_50 = false;
        $this->pctile_80 = false;
        if ($raw) {
            $decadals = $this->cleanDecadals($raw,$wvar,$fieldname);
            if (isset($opts['decadal_template']))
            {
                $this->doTemplate($decadals,$opts['decadal_template']);
            } else
            {
                if ($station->geo_lat >= 0) {
                    $this->doNorthern($decadals);
                } else {
                    $this->doSouthern($decadals);
                }
            }
        }        
    }

    /**
     * clean the variables of decadal data
     * @param type $raw
     * @return type
     */
    private function cleanDecadals($raw,$wvar,$fieldname) {
        $decadals = false;
        foreach ($raw as $rec) {
            $decadal = intval($rec->decadal);
            $decadals[$decadal][] = weather_data::cleanVar($wvar, $rec->$fieldname);
        }
        return $decadals;
    }

    /**
     * process according to template
     * wet season starts at decadal 0
     * @param type $decadals
     */
    private function doTemplate($decadals,$template) {
        foreach ($template as $key) {
            $this->addOutput($decadals[$key]);
        }
    }
    
    /**
     * process northern region
     * wet season starts at decadal 0
     * @param type $decadals
     */
    private function doNorthern($decadals) {
        foreach ($decadals as $rec) {
            $this->addOutput($rec);
        }
    }

    /**
     * process northern region
     * wet season starts at decadal 70
     * @param type $decadals
     */
    private function doSouthern($decadals) {
        foreach ($decadals as $decadal => $rec) {
            if ($decadal > 70) {
                $this->addOutput($rec);
            }
        }
        foreach ($decadals as $decadal => $rec) {
            if ($decadal < 70) {
                $this->addOutput($rec);
            }
        }
    }

    /**
     * store output to class variable
     * @param type $rec
     */
    private function addOutput($rec) {
        $this->pctile_20[] = dss_utils::percentile($rec, 20);
        $this->pctile_50[] = dss_utils::percentile($rec, 50);
        $this->pctile_80[] = dss_utils::percentile($rec, 80);
    }

}

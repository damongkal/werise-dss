<?php

class advisory_fertilizer {

    const _FERT_NONE = 0;
    const _FERT_GEN = 1;
    const _FERT_SPC = 2;

    /**
     * get array of dates where there is rain
     * @param type $station
     * @param type $year
     * @param type $wtype
     * @param type $fert
     * @return boolean|array
     */
    public function getAdvisoryFert($station, $year, $wtype, $fert) {
        // if fert is not applied, no need to compute
        if ($fert === self::_FERT_NONE) {
            return false;
        }

        // check if there is weather data
        $weather = new weather_data;
        $data = $weather->getDecadal($station, $year, $wtype, 0);
        if (!$data) {
            return false;
        }

        // get the dates where rain is between 30 and 100
        $series = dss_utils::getDecadalSeries($year, $station->geo_lat);
        $fapply = array();
        $last_key = -2;
        foreach ($data as $key => $rain) {
            if ($rain >= 30 && $rain <= 100) {
                $to = DateTime::createFromFormat('U', $series[$key] / 1000);
                $fromdate_txt = $to->format('Y-m-d');
                $to->modify('+9 day');
                $todate_txt = $to->format('Y-m-d');
                if (($key - 1) == $last_key) {
                    $cnt = count($fapply);
                    $fapply[$cnt - 1]['to'] = array($to->format('U') * 1000, $rain, $todate_txt);
                } else {
                    $fapply[] = array(
                        'from' => array($series[$key], $rain, $fromdate_txt),
                        'to' => array($to->format('U') * 1000, $rain, $todate_txt));
                }
                $last_key = $key;
            }
        }

        return $fapply;
    }

    /**
     * 
     * @param type $station
     * @param type $year
     * @param type $wtype
     * @param type $fert
     * @return boolean|array
     */
    public function getRainDates($station, $year, $wtype, $fert) {
        if ($fert === self::_FERT_NONE) {
            return array();
        }
        $station->geo_lat = 0; // force northern;
        $fert_apply1 = $this->getAdvisoryFert($station, $year, $wtype, $fert);
        $fert_apply2 = $this->getAdvisoryFert($station, $year, $wtype, $fert);
        if ($fert_apply2) {
            return array_merge($fert_apply1, $fert_apply2);
        } else {
            return $fert_apply1;
        }
    }
    
    /**
     * determine fertilizer schedule 
     * @param type $raindays
     * @param type $fromdate
     * @param type $todate
     * @return type
     */
    public function getFertSchedule($raindates,$fromdate,$todate)
    {        
        if (!$raindates)
        {
            return false;
        }
        $fromdate_c = $fromdate * 1000;
        $todate_c = $todate * 1000;
        $ret = array();
        foreach ($raindates as $key => $fert)
        {
            $fert['key'] = $key;
            if ( $fromdate_c <= ($fert['to'][0]) && $todate_c >= ($fert['from'][0]) )
            {
                // adjust from
                if ( $fromdate_c >= ($fert['from'][0]) )
                {
                    $fert['from'][0] = $fromdate_c;
                }

                // adjust to
                if ( $todate_c <= ($fert['to'][0]) )
                {
                    $fert['to'][0] = $todate_c;
                }

                $ret[] = $fert;
            }
        }
        return $ret;
    }    

}

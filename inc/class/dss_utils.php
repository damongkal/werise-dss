<?php

class dss_utils {

    /**
     * formal percentile math function
     * @param type $data
     * @param type $percentile
     * @return string
     */
    public static function percentile($data, $percentile) {
        $count = count($data);
        $null_count = 0;
        foreach ($data as $datum) {
            if (is_null($datum)) {
                $null_count++;
            }
        }
        if ($count === $null_count) {
            return null;
        }

        if (0 < $percentile && $percentile < 1) {
            $p = $percentile;
        } else if (1 < $percentile && $percentile <= 100) {
            $p = $percentile * .01;
        } else {
            return "";
        }

        $allindex = ($count - 1) * $p;
        $intvalindex = intval($allindex);
        $floatval = $allindex - $intvalindex;
        sort($data);
        if (!is_float($floatval)) {
            $result = $data[$intvalindex];
        } else {
            if ($count > $intvalindex + 1)
                $result = $floatval * ($data[$intvalindex + 1] - $data[$intvalindex]) + $data[$intvalindex];
            else
                $result = $data[$intvalindex];
        }
        return number_format($result, 1, '.', '') + 0;
    }

    /**
     * create array of decadals in UTC format
     * @param int $geolat northern / southern hemisphere
     * @return array
     */
    public static function getDecadalSeries($year, $geolat) {
        $month = "01";
        if ($geolat < 0) {
            $month = "07";
        }
        return self::getDecadalSeries2($month,$year);
    }
    
    /**
     * create array of decadals in UTC format
     * @param string $start
     * @return array
     */
    public static function getDecadalSeries2($month,$year) {
        $ret = array();
        $refdate = DateTime::createFromFormat('Y-m-d H:i', "$year-{$month}-01 0:00");
        for ($month = 1; $month <= 12; $month++) {
            $day1 = clone $refdate;
            $ret[] = $day1->format('U') * 1000;

            $day1->modify('+10 day');
            $ret[] = $day1->format('U') * 1000;

            $day1->modify('+10 day');
            $ret[] = $day1->format('U') * 1000;
            
            $refdate->modify('+1 month');            
        }
        return $ret;
    }    

    public static function getWetSeasonPeriod($geolat, $year) {
        // wet season period
        if ($geolat < 0) {
            $ws_from = DateTime::createFromFormat('Y-m-d H:i', $year . '-10-01 0:00');
            $ws_to = DateTime::createFromFormat('Y-m-d H:i', ($year + 1) . '-03-01 0:00');
        } else {
            $ws_from = DateTime::createFromFormat('Y-m-d H:i', $year . '-06-01 0:00');
            $ws_to = DateTime::createFromFormat('Y-m-d H:i', $year . '-10-01 0:00');
        }
        return array($ws_from, $ws_to);
    }
    
    public static function getCropSeason($month,$year,$format='Y-m-d')
    {
        $season_start = mktime(0,0,0,$month,1,$year);
        $season_end = DateTime::createFromFormat('U', $season_start);
        $season_end->add(new DateInterval("P1Y"));
        return array(date($format,$season_start),$season_end->format($format));
    }  
    
    public static function getCropSeason2($start,$end,$format='Y-m-d')
    {
        $season_start = DateTime::createFromFormat('Y-m-d', $start);
        $season_end = DateTime::createFromFormat('Y-m-d', $end);
        return array($season_start->format($format),$season_end->format($format));
    }    

    /**
     * determine if current call is AJAX
     * @return boolean
     */
    public static function isAjax() {
        $is_ajax = false; // assume not ajax
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
            $is_ajax = true;
        }
        return $is_ajax;
    }

    /**
     * Calculates the standard deviation for all non-zero items in an array
     * @param type $arr
     * @return type
     */
    public static function std_dev($arr)
    {
        $n = count(array_filter($arr));   // Counts non-zero elements in the array.
        $mean = array_sum($arr) / $n;     // Calculates the arithmetic mean.
        $sum = 0;

        foreach( $arr as $key=>$a )
        {
            $sum = $sum + pow( $a - $mean , 2 );
        }

        $stdev = sqrt( $sum / $n ) ;

        return $stdev;
    }
    
    private static function getSelectionAttr()
    {
        $attr = array(
            'country' => 'ID',
            'station' => 0,
            'year' => 0,
            'wtype' => '',
            'wvar' => 0,
            'variety' => '',
            'fert' => 1,
            'year2' => 0,
            'wtype2' => '',            
            'variety2' => '',
            'fert2' => 0,
            'year3' => 0,
            'wtype3' => '',            
            'variety3' => '',
            'fert3' => 0,
            'year4' => 0,
            'wtype4' => '',            
            'variety4' => '',
            'fert4' => 0
        );        
        
        return $attr;
    }

    public static function saveLastSelection($erase = false)
    {
        foreach (self::getSelectionAttr() as $key => $val)
        {
            if (isset($_GET[$key]))
            {
                if (is_numeric($val))
                {
                    $val2 = intval($_GET[$key]);
                } else
                {
                    $val2 = $_GET[$key];
                }
                if ($key==='country')
                {
                    $val2 = strtoupper($val2);
                }
                if ($key==='wtype' && $val2 === 'x')
                {
                    continue;
                }
                $_SESSION['sel'][$key] = $val2;
            } else
            {
                if ($erase)
                {
                    unset($_SESSION['sel'][$key]);
                }
            }
        }
    }

    public static function getLastSelectValues($var = null) {
        $default = self::getSelectionAttr();
        if (is_null($var))
        {
            foreach ($default as $key => $val) {
                if (isset($_SESSION['sel'][$key])) {
                    $default[$key] = $_SESSION['sel'][$key];
                }
            }            
            return $default;
        } else
        {            
            if (isset($_SESSION['sel'][$var])) {
                return $_SESSION['sel'][$var];
            }
            if (isset($default[$var])) {
                return $default[$var];
            }
        }
        return '';
    }    
}

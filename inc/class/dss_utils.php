<?php

class dss_utils
{

    /**
     * formal percentile math function
     * @param type $data
     * @param type $percentile
     * @return string
     */
    public static function percentile($data, $percentile)
    {
        $count = count($data);
        $null_count = 0;
        foreach($data as $datum)
        {
            if (is_null($datum))
            {
                $null_count++;
            }
        }
        if ($count===$null_count)
        {
            return null;
        }
        
        if (0 < $percentile && $percentile < 1)
        {
            $p = $percentile;
        }
        else if (1 < $percentile && $percentile <= 100)
        {
            $p = $percentile * .01;
        }
        else
        {
            return "";
        }
        
        $allindex = ($count - 1) * $p;
        $intvalindex = intval($allindex);
        $floatval = $allindex - $intvalindex;
        sort($data);
        if (!is_float($floatval))
        {
            $result = $data[$intvalindex];
        }
        else
        {
            if ($count > $intvalindex + 1)
                $result = $floatval * ($data[$intvalindex + 1] - $data[$intvalindex]) + $data[$intvalindex];
            else
                $result = $data[$intvalindex];
        }
        return number_format($result,1,'.','')+0;
    }
    
    /**
     * create array of decadals in UTC format
     * @param int $geolat northern / southern hemisphere
     * @return array 
     */
    public static function getDecadalSeries($year,$geolat)
    {
        if($geolat<0)
        {
            $start = $year.'-06';
        } else
        {
            $start = ($year-1).'-12';
        }

        $ret = array();
        $refdate = DateTime::createFromFormat('Y-m-d H:i', $start.'-01 0:00');
        for($month=1; $month<=12;$month++)
        {
            $refdate->modify('+1 month');
            
            $day1 = clone $refdate;
            $ret[] = $day1->format('U') * 1000;

            $day1->modify('+10 day');
            $ret[] = $day1->format('U') * 1000;
            
            $day1->modify('+10 day');
            $ret[] = $day1->format('U') * 1000;
        }        
        return $ret;        
    }    
    
    public static function getWetSeasonPeriod($geolat,$year)
    {
        // wet season period
        if ($geolat<0)
        {
            $ws_from = DateTime::createFromFormat('Y-m-d H:i', $year.'-10-01 0:00');
            $ws_to = DateTime::createFromFormat('Y-m-d H:i', ($year+1).'-03-01 0:00');
        } else
        {
            $ws_from = DateTime::createFromFormat('Y-m-d H:i', $year.'-06-01 0:00');
            $ws_to = DateTime::createFromFormat('Y-m-d H:i', $year.'-10-01 0:00');
        }
        return array($ws_from,$ws_to);
    }

}
<?php
class advisory_rainfall
{
    private $db;
    private $debug;
    //private $monthly;

    private $station;
    private $wtype;

    const _RAIN_NORMAL = 1;
    const _RAIN_FLOOD = 2;
    const _RAIN_DROUGHT = 3;
    const _RAIN_UNKNOWN = 4;

    public function __construct($station, $wtype)
    {
        $this->db = Database_MySQL::getInstance();
        $this->debug = debug::getInstance();
        $this->station = $station;
        $this->wtype = $wtype;        
        //$this->monthly = $this->getAllMonthlyRainfall();
    }

    /**
     * 
     * @param type $station
     */
    public function getWSPeriod($station,$year)
    {
        if ($station->country_code=='ID' || $station->country_code=='TH')
        {
            $from = date_create($year.'-10-01');
            $to = date_create(($year+1).'-2-01');            
        } else
        {
            $from = date_create($year.'-06-01');
            $to = date_create($year.'-10-01');
        }        
        return array($from,$to);
    }

    /**
     * get advisory for the specific period
     * @param type $from
     * @param type $to
     * @return type
     */
    public function getAdvisory($from, $to)
    {
        $year = date_format($from,'Y');
        $rain_totals = $this->getRainfall($from,$to);
        $ws_year = $rain_totals[$year];
        $p20 = dss_utils::percentile($rain_totals, 20);
        $p80 = dss_utils::percentile($rain_totals, 80);

        $advisory_code = 1;
        $advisory_cat = _t('normal');
        
        if ($ws_year>$p80)
        {
            $advisory_code = 2;
            $advisory_cat = _t('above normal');
        }
        if ($ws_year<$p20)
        {
            $advisory_code = 3;
            $advisory_cat = _t('below normal');
        }

        $advisory = array(
            'rainfall' => $ws_year,
            'p20' => $p20,
            'p80' => $p80,
            'advisory_code' => $advisory_code,
            'advisory_cat' => $advisory_cat
        );
        $this->debug->addLog($advisory,true);
        return $advisory;
    }

    private function getAllMonthlyRainfall()
    {
        $sql = "
            SELECT DATE_FORMAT( b.`observe_date` , '%Y-%m' ) AS myear, SUM( b.`rainfall` ) AS month_rain
            FROM "._DB_DATA.".`weather_dataset` AS a
            INNER JOIN "._DB_DATA.".`weather_data` AS b ON a.`id` = b.`dataset_id`
            WHERE a.`country_code` = '{$this->station->country_code}'
            AND a.`station_id` = {$this->station->station_id}
            AND a.`wtype` = '{$this->wtype}'
            GROUP BY 1";
        return $this->db->getRowList($sql);
    }        

    /**
     * get the rainfall of date period
     */
    private function getRainfall($from,$to)
    {
        $from_date = date_format($from,'Y-m-d');
        $from_year = intval(date_format($from,'Y'));
        $to_date = date_format($to,'Y-m-d');
        $to_year = intval(date_format($to,'Y'));
        $this->debug->addLog('TOTAL RAINFALL');
        $this->debug->addLog("from : {$from_date}");
        $this->debug->addLog("to : {$to_date}");
        // get total rainfall
        $sql = "
            SELECT SUM( b.`rainfall` ) AS total_rain
            FROM "._DB_DATA.".`weather_dataset` AS a
            INNER JOIN "._DB_DATA.".`weather_data` AS b ON a.`id` = b.`dataset_id`
            WHERE a.`country_code` = '{$this->station->country_code}'
            AND a.`station_id` = {$this->station->station_id}
            AND a.`wtype` = '{$this->wtype}'
            AND b.observe_date >= '%s'    
            AND b.observe_date <= '%s'";
        $sql_rain = sprintf($sql,$from_date,$to_date);    
        $row = $this->db->getRow($sql_rain);
        $rain[$from_year] = $row->total_rain + 0;
        // get min year
        $sql_minyear = "
            SELECT MIN(a.`year`) AS min_year
            FROM "._DB_DATA.".`weather_dataset` AS a
            WHERE a.`country_code` = '{$this->station->country_code}'
            AND a.`station_id` = {$this->station->station_id}
            AND a.`wtype` = 'r'";
        $row2 = $this->db->getRow($sql_minyear);        
        // get yearly rainfall
        for($year_i = intval($row2->min_year); $year_i<$from_year; $year_i++) {
            $end_year = $year_i;
            if ($from_year!=$to_year) {
                $end_year++;
            }
            $sql_rain3 = sprintf($sql,$year_i.date_format($from,'-m-d'),$end_year.date_format($to,'-m-d'));    
            $row3 = $this->db->getRow($sql_rain3);
            if (!is_null($row3->total_rain)) {
                $rain[$year_i] = $row3->total_rain + 0;            
            }
        }
        $this->debug->addLog($rain,true);
        return $rain;
        /*
        // compose the month array
        $months = array();
        $tmp_monthfrom = date_format($from,'n');
        $tmp_monthto = date_format($to,'n')+1;
        $i = 0;
        while(date_format($from,'n')!=$tmp_monthto && $i++<10)
        {
            $months[] = date_format($from,'n');
            date_modify($from, '+1 month');
        }

        // compute rainfall total
        $ws_total = array();
        foreach($this->monthly as $rec)
        {
            $tmp = explode('-',$rec->myear);
            $year = $tmp[0];
            $month = $tmp[1];
            if (in_array($month,$months))
            {
                // if months overlaps 1 year, count rain to first year
                if ($month < $tmp_monthfrom)
                {
                    $year--;
                }

                if (!isset($ws_total[$year]))
                {
                    $ws_total[$year] = $rec->month_rain;
                } else
                {
                    if (!is_null($ws_total[$year]) || !is_null($rec->month_rain))
                    {
                        $ws_total[$year] += $rec->month_rain;
                    }
                }
            }
        }

        // remove null records
        $ws_total2 = array();
        foreach($ws_total as $year => $rain)
        {
            if (!is_null($rain))
            {
                $ws_total2[$year] = $rain;
            }
        }
        $this->debug->addLog($ws_total2,true);

        return $ws_total2;
         * 
         */
    }
}
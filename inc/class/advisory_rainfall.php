<?php
class advisory_rainfall
{
    private $db;
    private $debug;
    private $monthly;

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
        $this->monthly = $this->getAllMonthlyRainfall();
        //$this->debug->addLog($this->monthly,true);
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

    public function getAdvisory($from, $to)
    {
        $year = date_format($from,'Y');

        $ws = $this->getRainfall($from,$to);
        if (!isset($ws[$year]))
        {
            return array(
                'year' => $year,
                'wsrain' => 0,
                'p20' => null,
                'p80' => null,
                'advisory_code' => self::_RAIN_UNKNOWN,
                'advisory_cat' => 'unknown',
                'advisory_txt' => ''
            );
        }
        $ws_year = $ws[$year];
        $p20 = dss_utils::percentile($ws, 20);
        $p80 = dss_utils::percentile($ws, 80);

        $advisory_code = 1;
        $advisory_cat = _t('normal');
        $advisory_txt = '';
        if ($ws_year>$p80)
        {
            $advisory_code = 2;
            $advisory_cat = _t('above normal');
            // $advisory_txt = _t('There is high risk of flooding.');
        }
        if ($ws_year<$p20)
        {
            $advisory_code = 3;
            $advisory_cat = _t('below normal');
            // $advisory_txt = _t('There is high risk of drought.');
        }

        return array(
            'year' => $year,
            'wsrain' => $ws_year,
            'p20' => $p20,
            'p80' => $p80,
            'advisory_code' => $advisory_code,
            'advisory_cat' => $advisory_cat,
            'advisory_txt' => $advisory_txt
        );
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
     * get the yearly rainfall of date period
     */
    private function getRainfall($from,$to)
    {
        $this->debug->addLog('from : ' . date_format($from,'Y-m'));
        $this->debug->addLog('to : ' . date_format($to,'Y-m'));
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
        //$this->debug->addLog($ws_total,true);

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
    }
}
<?php
class advisory_rainfall
{
    private $db;
    private $country;
    private $station;
    private $year;
    
    public function __construct()
    {
        $this->db = Database_MySQL::getInstance();
    }
    
    public function getAdvisory($country, $station, $year)
    {
        $this->country = $country;
        $this->station = $station;
        $this->year = $year;
        
        $monthly = $this->getMonthlyRainfall();        
        $ws = $this->getWsRainfall($monthly);
        if (!isset($ws[$this->year]))
        {
            return array(
                'rainfall' => $ws,
                'p20' => null,
                'p80' => null,
                'advisory_code' => 4,
                'advisory' => "<strong>unknown</strong>"
            );
        }
        $ws_year = $ws[$this->year];
        $p20 = dss_utils::percentile($ws, 20);
        $p80 = dss_utils::percentile($ws, 80);
        
        
        $adv = '<span class="text-success">normal.</span>';        
        $advisory_code = 1;
        $extra = "<code>Probability at 80% : {$p20}</code> &lt; <code>Rainfall : {$ws_year}</code> &lt; <code>Probability at 20% : {$p80}</code>";
        if ($ws_year>$p80)
        {
            $advisory_code = 2;
            $adv = '<span class="text-error">above normal</span>. High risk of flooding.';
            $extra = "<code>Probability at 80% : {$p20}</code> &lt; <code>Probability at 20% : {$p80}</code> &lt; <code>Rainfall : {$ws_year}</code>";
        }
        if ($ws_year<$p20)
        {
            $advisory_code = 3;
            $adv = '<span class="text-error">below normal</span>. High risk of drought.';
            $extra = "<code>Rainfall: {$ws_year}</code> &lt; <code>Probability at 80% : {$p20}</code> &lt; <code>Probability at 20% : {$p80}</code>";
        }
/*
        echo 'country: ' . $this->country . '<br />';
        echo 'station: ' . $this->station . '<br />';
        echo 'year: ' . $this->year . '<br />';
        echo 'year total: ' . $ws_year . '<br />';
        echo 'p20: ' . $p20 . '<br />';
        echo 'p80: ' . $p80 . '<br />';
        echo 'advisory: ' . $adv . '<br />';
 * 
 */
        return array(
            'rainfall' => $ws,
            'p20' => $p20,
            'p80' => $p80,
            'advisory_code' => $advisory_code,
            'advisory' => "<strong>$adv</strong> <br />$extra"
        );
    }
    
    private function getMonthlyRainfall()
    {
        $sql = "
            SELECT DATE_FORMAT( b.`observe_date` , '%Y-%m' ) AS myear, SUM( b.`rainfall` ) AS month_rain
            FROM `weather_dataset` AS a
            INNER JOIN `weather_data` AS b ON a.`id` = b.`dataset_id`
            WHERE a.`country_code` = '{$this->country}'
            AND a.`station_id` = {$this->station}
            AND a.`wtype` = 'r'
            GROUP BY 1";
        return $this->db->getRowList($sql);
    }
    
    /**
     * get the yearly wet season rainfall  
     */
    private function getWsRainfall($monthly)
    {
        // wet season months
        if ($this->country=='ID' || $this->country=='TH')
        {
            $ws_months = array(10,11,12,1,2);
        } else
        {
            $ws_months = array(6,7,8,9,10);
        }
        
        // compute wet season rainfall total
        $ws_total = array();
        foreach($monthly as $rec)
        {
            $tmp = explode('-',$rec->myear);
            $year = $tmp[0];
            $month = $tmp[1];
            if (in_array($month,$ws_months))
            {
                // for wet season of ID / TH, the years overlap. we count over the past year
                if ($month==1 || $month==2)
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
        
        $ws_total2 = array();
        foreach($ws_total as $year => $rain)
        {
            if (!is_null($rain))
            {
                $ws_total2[$year] = $rain;
            }
        }
        
        return $ws_total2;
    }
}
<?php

class fertilizer_application {

    private $db;
    private $debug;

    public function __construct() {
        $this->db = Database_MySQL::getInstance();
        $this->debug = debug::getInstance();
    }

    public function getAll($ftype) {
        $sql = "
            SELECT a.* , b.`station_name`
            FROM `rcm_fertilizer` AS a
            LEFT JOIN `weather_stations` AS b
                ON a.`country_code` = b.`country_code`
                AND a.`station_id` = b.`station_id`
            WHERE a.`ftype` = '$ftype' 
            ORDER BY a.`country_code`, b.`station_name`, a.`variety`, a.`yld`";
        return $this->db->getRowList($sql);
    }

    /**
     * get fertilizer recommendation from RCM tables
     * @param type $variety
     * @param type $yld
     * @return boolean
     */
    public function getFertSched($ftype, $station, $variety, $yld) {
/*echo "fert={$ftype};variety={$variety};yld={$yld}<pre>";
print_r($station);
echo '</pre>';*/
        // general recommendation
        if ($ftype === advisory_fertilizer::_FERT_GEN) {
            // station
            $filter[] = array('country' => $station->country_code, 'station' => $station->station_id);
            $order[] = 'ASC';

            // country
            $filter[] = array('country' => $station->country_code);
            $order[] = 'ASC';
        }

        // specific recommendation
        if ($ftype === advisory_fertilizer::_FERT_SPC) {
            // high target by variety
            $filter[] = array('variety' => $variety, 'yld' => ">={$yld}");
            $order[] = 'ASC';

            // recommended by variety
            $filter[] = array('variety' => $variety, 'yld' => "<{$yld}");
            $order[] = 'DESC';

            // lowest value by variety
            $filter[] = array('variety' => $variety);
            $order[] = 'ASC';

            // high target by default
            $filter[] = array('variety' => '', 'yld' => ">={$yld}");
            $order[] = 'ASC';

            // recommended by default
            $filter[] = array('variety' => '', 'yld' => "<{$yld}");
            $order[] = 'DESC';

            // lowest value by default
            $filter[] = array('variety' => '');
            $order[] = 'ASC';
        }

        foreach ($filter as $key => $f) {
            $fert_s = $this->getFertSchedDB($ftype, $f, $order[$key]);
            if ($fert_s) {
                return $fert_s;
            }
        }

        throw new Exception('no fertilizer schedule found.');
    }

    private function getFertSchedDB($ftype, $filter, $sort_order = 'ASC') {
        $cond = '';
        if (isset($filter['country'])) {
            $cond .= " AND a.`country_code` = '{$filter['country']}'";
        }
        if (isset($filter['station'])) {
            $cond .= " AND a.`station_id` = {$filter['station']}";
        }
        if (isset($filter['variety'])) {
            $cond .= " AND a.`variety` = '{$filter['variety']}'";
        }

        if (isset($filter['yld'])) {
            $cond .= " AND a.`yld` = {$filter['yld']}";
        }

        $sql = "
            SELECT *
            FROM `rcm_fertilizer` AS a
            WHERE a.`ftype` = '{$ftype}' {$cond}
            ORDER BY a.`yld` {$sort_order}
            LIMIT 1";
        return $this->getSchedArr($this->db->getRow($sql));
    }

    /**
     * format schedule as array
     * @param object $fert_s
     * @return string|boolean
     */
    private function getSchedArr($fert_s) {
        if ($fert_s) {

            $arr = array(
                $fert_s->n1day, $fert_s->n1, $fert_s->n2day, $fert_s->n2, $fert_s->n3day, $fert_s->n3,
                $fert_s->p1, $fert_s->p2, $fert_s->p3,
                $fert_s->k1, $fert_s->k2, $fert_s->k3);
            foreach ($arr as $key => $val) {
                if ($val == '') {
                    $arr[$key] = '*';
                }
            }
            return $arr;
        }
        return false;
    }
    
    /**
     * adjust fert sched based on availability of rain
     * @param type $fertil
     * @param type $sowdate
     * @param type $fert_apply
     * @return int
     */
    public function adjustFertil($fertil, $sowdate, $fert_apply) {
        $this->debug->addLog('SOWDATE: ' . $sowdate->format('Y-m-d'),false,'FERTCALC');
        $this->debug->addLog('FERTIL: ' . implode(',', $fertil),false,'FERTCALC');
        foreach (array(2, 4) as $fertkey) {
            $das = clone $sowdate;
            $das->add(new DateInterval("P{$fertil[$fertkey]}D"));
            $this->debug->addLog('DAS' . $fertkey . ':' . $das->format('Y-m-d'),false,'FERTCALC');
            $das_unix = $das->format('U') * 1000;
            $newdasval = null;
            foreach ($fert_apply as $key => $rec) {
                $this->debug->addLog('KEY: ' . $key,false,'FERTCALC');
                $ref_from = DateTime::createFromFormat('U', $rec['from'][0] / 1000);
                $ref_to = DateTime::createFromFormat('U', $rec['to'][0] / 1000);
                $this->debug->addLog('REF: ' . $ref_from->format('Y-m-d') . ' to ' . $ref_to->format('Y-m-d'),false,'FERTCALC');
                if ($rec['from'][0] <= $das_unix && $rec['to'][0] >= $das_unix) {
                    $this->debug->addLog('RAIN FOUND',false,'FERTCALC');
                    break; // rain found. no need to adjust
                }

                if (($rec['from'][0] > $das_unix)) {
                    if ($key > 0) {
                        // new DAS date
                        $ref = $fert_apply[$key - 1]['to'][0];
                        $tmp = DateTime::createFromFormat('U', $ref / 1000);
                        $this->debug->addLog('NEWDAS :' . $tmp->format('Y-m-d'),false,'FERTCALC');

                        // date diff must be less than 6 days
                        if ($das->diff($tmp)->format('%a') > 6) {
                            $this->debug->addLog('TOO EARLY',false,'FERTCALC');
                        } else {
                            $newdasval = $sowdate->diff($tmp)->format('%a');
                        }
                    }

                    if (is_null($newdasval)) {
                        // new DAS date
                        $ref = $rec['from'][0];
                        $tmp = DateTime::createFromFormat('U', $ref / 1000);
                        $this->debug->addLog('NEWDAS :' . $tmp->format('Y-m-d'),false,'FERTCALC');

                        // date diff must be less than 6 days
                        if ($das->diff($tmp)->format('%a') > 6) {
                            $this->debug->addLog('TOO LATE',false,'FERTCALC');
                        } else {
                            $newdasval = $sowdate->diff($tmp)->format('%a');
                        }
                    }

                    if (!is_null($newdasval)) {
                        $this->debug->addLog('NEWDASVAL :' . $newdasval,false,'FERTCALC');
                        $fertil[$fertkey] = $newdasval;
                    } else {
                        $this->debug->addLog('NO RAIN FOUND',false,'FERTCALC');
                        $fertil[$fertkey + 1] = 0;
                        break; // no rain found. no need to adjust
                    }

                    break;
                }
            }
        }
        return $fertil;
    }    

}

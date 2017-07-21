<?php

class werise_sintexf_data {

    public function getRecords($filters = null) {
        $parts = array();
        if (isset($filters['region_id'])) {
            $parts[] = sprintf("`region_id` = %u", intval($filters['region_id']));
        }
        if (isset($filters['year'])) {
            $parts[] = sprintf("`forecast_date` BETWEEN '%u-01-01' AND '%u-12-31'", intval($filters['year']), intval($filters['year']));
        }
        $where = '';
        if (count($parts) > 0) {
            $where = "WHERE " . implode(' AND ', $parts);
        }
        $db = Database_MySQL::getInstance();
        $sql = "
            SELECT *
            FROM " . _DB_DATA . ".`sintexf_raw`
            $where
            ORDER BY `region_id`,`forecast_date`";
        return $db->getRowList($sql);
    }

    public function getRawData($region_id) {
        $db = Database_MySQL::getInstance();
        $sql = "
            SELECT YEAR(`forecast_date`) as fyear, MONTH(`forecast_date`) as fmonth,
                    COUNT(IF(`pr` IS NULL OR `pr`=-990,NULL,1)) AS pr_cnt,
                    COUNT(IF(`tn` IS NULL OR `tn`=-990,NULL,1)) AS tn_cnt,
                    COUNT(IF(`tx` IS NULL OR `tx`=-990,NULL,1)) AS tx_cnt,
                    COUNT(IF(`ws` IS NULL OR `ws`=-990,NULL,1)) AS ws_cnt
            FROM " . _DB_DATA . ".`sintexf_raw`
            WHERE `region_id` = %u
            GROUP BY fyear, fmonth";
        $rs = $db->getRowList(sprintf($sql, intval($region_id)));
        if (!$rs) {
            return array();
        }
        // initial all possible records
        $rs2 = array();
        foreach ($rs as $monthyear) {
            $year = intval($monthyear->fyear);
            $month = intval($monthyear->fmonth);
            $idx = $year. str_pad($month,2,'0',STR_PAD_LEFT);
            $rs2[$idx] = array('to' => $idx, 'details' => array());
        }

        // summarize
        $last_idx = $rs[0]->fyear. str_pad($rs[0]->fmonth,2,'0',STR_PAD_LEFT);
        $last_pass = 5;
        foreach ($rs as $monthyear) {
            $year = intval($monthyear->fyear);
            $month = intval($monthyear->fmonth);
            $idx = $year. str_pad($month,2,'0',STR_PAD_LEFT);
            // get month day count
            $d = new DateTime("{$monthyear->fyear}-{$monthyear->fmonth}-01");
            $lastday = $d->format('t');
            $data = array('my' => $idx, 'pr' => 0, 'tn' => 0, 'tx' => 0, 'ws' => 0, 'pass' => 0);
            // test PR
            $data['pr'] = $monthyear->pr_cnt;
            if ($monthyear->pr_cnt == $lastday) {
                $data['pass'] ++;
            }
            // test TN
            $data['tn'] = $monthyear->tn_cnt;
            if ($monthyear->tn_cnt == $lastday) {
                $data['pass'] ++;
            }
            // test TX
            $data['tx'] = $monthyear->tx_cnt;
            if ($monthyear->tx_cnt == $lastday) {
                $data['pass'] ++;
            }
            // test WS
            $data['ws'] = $monthyear->ws_cnt;
            if ($monthyear->ws_cnt == $lastday) {
                $data['pass'] ++;
            }
            /*
             * compare with previous record
             * compile same results
             */
            if ($data['pass'] === $last_pass) {
                $rs2[$last_idx]['to'] = $idx;
                unset($rs2[$idx]);
            } else {
                $last_idx = $idx;
            }
            $rs2[$last_idx]['details'][] = $data;
            $last_pass = $data['pass'];
        }
        return $rs2;
    }

}

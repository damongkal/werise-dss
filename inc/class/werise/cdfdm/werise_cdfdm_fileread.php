<?php

class werise_cdfdm_fileread extends werise_cdfdm_file {

    public function open($country_code, $region_id, $source, $filetype) {
        $this->getHandle($country_code, $region_id, $source, $filetype, 'r');
        $this->checkFile();
    }

    public function openGeneric($filename) {
        $this->current_file = $filename;
        $this->handle = werise_core_files::getHandle($filename, 'r');
        $this->checkFile();
    }

    private function checkFile() {
        $buffer = fgets($this->handle, 4096);
        if (trim($buffer) !== trim(werise_cdfdm_file::_HDR)) {
            throw new Exception('Invalid Header Line: ' . $buffer);
        }
    }

    /**
     * read a line of data
     * @return boolean
     */
    public function read() {
        // get 1 line
        $buffer = fgets($this->handle, 4096);
        if ($buffer === false) {
            return false;
        }
        $line_tmp = explode(' ', $buffer);
        // remove empty
        $line = array();
        foreach ($line_tmp as $line_tmp2) {
            if (trim($line_tmp2)!='') {
                $line[] = trim($line_tmp2);
            }
        }

        $colidx = werise_cdfdm_file::getColumnIndex();
        $ret = array();
        foreach($colidx as $idx => $col) {
            $ret[$col] = 0;
            if (isset($line[$idx])) {
                if ($col===werise_cdfdm_file::_COL_DATA) {
                    $ret[$col] = floatval(trim($line[$idx]));
                } else {
                    $ret[$col] = intval(trim($line[$idx]));
                }
            }
        }

        return $ret;
    }

    /**
     * get series data for chart
     * @param type $year
     * @return type
     */
    public function getSeriesData($year) {
        $data = array();
        while (($line = $this->read()) !== false) {
            if ($year == $line[werise_cdfdm_file::_COL_YR]) {
                $d = DateTime::createFromFormat('Y-m-d', "{$line[werise_cdfdm_file::_COL_YR]}-{$line[werise_cdfdm_file::_COL_MN]}-{$line[werise_cdfdm_file::_COL_DY]}");
                $data[] = array($d->format('U') * 1000, $line[werise_cdfdm_file::_COL_DATA]);
            }
        }
        return $data;
    }

    /**
     * get min max values of year
     * @return type
     */
    public function getMinMax() {
        $line = $this->read();

        $d = DateTime::createFromFormat('Y-m-d', "{$line[werise_cdfdm_file::_COL_YR]}-{$line[werise_cdfdm_file::_COL_MN]}-{$line[werise_cdfdm_file::_COL_DY]}");
        $min = '';
        $max = '';
        if ($d!==false) {
            $min = $d->format('Y-m-d');
            while (($line = $this->read()) !== false) {
                $d = DateTime::createFromFormat('Y-m-d', "{$line[werise_cdfdm_file::_COL_YR]}-{$line[werise_cdfdm_file::_COL_MN]}-{$line[werise_cdfdm_file::_COL_DY]}");
                if ($d !== false) {
                    $max = $d->format('Y-m-d');
                }
            }
        }
        return array($min, $max);
    }

}

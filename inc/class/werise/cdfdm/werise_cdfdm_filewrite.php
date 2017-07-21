<?php

class werise_cdfdm_filewrite extends werise_cdfdm_file {

    public function open($country_code, $region_id, $source, $filetype) {
        $this->getHandle($country_code, $region_id, $source, $filetype, 'w');
        fwrite($this->handle, werise_cdfdm_file::_HDR . "\n");
    }

    /**
     * write a line of data
     * @param type $date
     * @param type $data
     */
    public function write($date, $data) {
        // break date
        $d = DateTime::createFromFormat('Y-m-d', $date);
        $yr = str_pad($d->format('Y'), 4, ' ', STR_PAD_LEFT);
        $mn = str_pad($d->format('n'), 3, ' ', STR_PAD_LEFT);
        $dy = str_pad($d->format('j'), 3, ' ', STR_PAD_LEFT);
        $doy = str_pad($d->format('z') + 1, 4, ' ', STR_PAD_LEFT);
        // validate data
        if (intval($data)== -999) {
            $data = '';
        }
        // data
        if ($data === '') {
            $data2 = '  -999.000';
        } else {
            $data2 = str_pad($data, 10, ' ', STR_PAD_LEFT);
        }
        fwrite($this->handle, $yr . $mn . $dy . $doy . $data2 . "\n");
    }

}

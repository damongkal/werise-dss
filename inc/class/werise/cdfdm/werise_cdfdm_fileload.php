<?php

class werise_cdfdm_fileload {

    // output variables
    public $outfiles;
    public $raw;
    
    public function loadFile($filename,$ftype) {
        $out1 = new werise_cdfdm_fileread;
        $out1->openGeneric($filename);
        $this->outfiles[] = $out1->getCurrentFile();
        while (($line = $out1->read()) !== false) {
            $this->processVars($ftype, $line);
        }
        $out1->close();
    }

    /**
     * process 1 line of variables
     * @param type $data_col
     * @param type $vars
     * @return boolean
     */
    protected function processVars($data_col, $vars) {
        // prepare data
        $idx = "{$vars[werise_cdfdm_file::_COL_YR]}-{$vars[werise_cdfdm_file::_COL_MN]}-{$vars[werise_cdfdm_file::_COL_DY]}";
        if (in_array($vars[werise_cdfdm_file::_COL_DATA], array('-999', '-99'))) {
            $data_val = null;
        } else {
            $data_val = $vars[werise_cdfdm_file::_COL_DATA];
        }
        unset($vars[werise_cdfdm_file::_COL_DATA]);

        // save datapoint
        if (!isset($this->raw[$idx])) {
            $this->raw[$idx] = $vars;
        }
        $this->raw[$idx][$data_col] = $data_val;
    }

}

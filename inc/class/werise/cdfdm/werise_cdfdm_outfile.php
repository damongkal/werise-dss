<?php

class werise_cdfdm_outfile extends werise_cdfdm_fileload {

    public function loadFiles($country_code, $region_id, $year) {
        foreach(werise_cdfdm_file::getTypes() as $ftype) {
            $out1 = new werise_cdfdm_fileread;
            $out1->open($country_code, $region_id, werise_cdfdm_folder::_SRC_OUT, $ftype);
            $this->outfiles[] = $out1->getCurrentFile();            
            while (($line = $out1->read()) !== false) {
                // generate only the chosen year
                if ($year > 0 && $line[werise_cdfdm_file::_COL_YR] < ($year-2)) {
                    continue;
                }            
                $this->processVars($ftype, $line);
            }        
            $out1->close();
        }
        if (is_null($this->raw)) {
            throw new Exception('No CDFDM OUT files processed.');
        }
    }

}

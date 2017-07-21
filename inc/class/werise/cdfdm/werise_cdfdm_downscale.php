<?php

class werise_cdfdm_downscale {

    /**
     * update script file
     * @param type $type
     * @param type $country_code
     * @param type $region_id
     */
    public function downscale($type, $country_code, $region_id) {
        // get min max values
        list($gcmdir, $gcmfiles) = werise_cdfdm_folder::getFolderInfo($country_code, $region_id, werise_cdfdm_folder::_SRC_GCM);

        list($gcmmin, $gcmmax) = $this->getMinMaxYears($gcmfiles);
        list($obsdir, $obsfiles) = werise_cdfdm_folder::getFolderInfo($country_code, $region_id, werise_cdfdm_folder::_SRC_OBS);
        list($obsmin, $obsmax) = $this->getMinMaxYears($obsfiles);

        // make replacements
        $replace['iyrstt_obs'] = $obsmin;
        $replace['iyrend_obs'] = $obsmax;
        $replace['iyrstt_gcm'] = $gcmmin;
        $replace['iyrend_gcm'] = $gcmmax;
        $trmin = $gcmmin;
        if ($obsmin > $gcmmin) {
            $trmin = $obsmin;
        }
        $replace['iyrstt_tr'] = $trmin;
        $trmax = $gcmmax;
        if ($obsmax < $gcmmax) {
            $trmax = $obsmax;
        }
        $replace['iyrend_tr'] = $trmax;
        $country_dir = str_replace(DIRECTORY_SEPARATOR, '/', werise_stations_country::getDir($country_code));
        $subfolder = $country_dir . 'r' . $region_id;
        $replace['dir_obs'] = './data/obs/' . $type . '/' . $subfolder;
        $replace['dir_gcm'] = './data/gcm/' . $subfolder;
        $replace['dir_cdfdm'] = './out/' . $type . '_forecast/' . $subfolder;
        // replace target code
        $handle = werise_core_files::getHandle(werise_cdfdm_script::getControlFile(), 'w');


        fwrite($handle, "#-------------------\n");
        fwrite($handle, "# station folder\n");
        fwrite($handle, "#-------------------\n");
        fwrite($handle, "dir_stn={$subfolder}\n\n");

        fwrite($handle, "#-------------------\n");
        fwrite($handle, "# training start year. 1=auto compute\n");
        fwrite($handle, "#-------------------\n");
        fwrite($handle, "iyrstt_tr=1\n\n");

        fwrite($handle, "#-------------------\n");
        fwrite($handle, "# training end year. 1=auto compute\n");
        fwrite($handle, "#-------------------\n");
        fwrite($handle, "iyrend_tr=1\n");

        //foreach($replace as $src => $target) {
        //    fwrite($handle,"{$target}\n");
        //}
        fclose($handle);

        // copy files to process directory
        //$this->copyFiles($gcmdir,$gcmfiles,$obsdir,$obsfiles,$type);
        // prepare output folder
        $dir = werise_cdfdm_folder::getFolder($country_code, $region_id, werise_cdfdm_folder::_SRC_OUT);
        @mkdir($dir);

        // execute cdfdm
        set_time_limit(600);
        $debug = array();
        foreach (werise_cdfdm_file::getTypes() as $file) {
        //foreach (array('pr') as $file) { // tester
            $debug[] = "<h2>[" . strtoupper($file) . "] downscale result:</h2>";
            $handle2 = werise_core_files::getHandle(werise_cdfdm_script::getListFile(), 'w');
            fwrite($handle2, "r{$region_id} {$file}\n");
            fclose($handle2);
            unset($cmdout);
            $cmdret = exec(_CDFDM_DIR . DIRECTORY_SEPARATOR . 'run_cdfdm.bat', $cmdout);
            $debug[] = $this->fmtCmdOut($cmdout);
        }
        // delete debug files
        array_map('unlink', glob($dir2.DIRECTORY_SEPARATOR.'*debug*'));
        return $debug;
    }

    private function copyFiles($gcmdir, $gcmfiles, $obsdir, $obsfiles, $type) {
        $dest1 = werise_cdfdm_folder::getFolder('', 0, werise_cdfdm_folder::_SRC_GCM);
        foreach ($gcmfiles as $files1) {
            copy($gcmdir . DIRECTORY_SEPARATOR . $files1[0], $dest1 . DIRECTORY_SEPARATOR . $files1[0]);
        }
        $dest2 = werise_cdfdm_folder::getFolder('', 0, werise_cdfdm_folder::_SRC_OBS);
        foreach ($obsfiles as $files2) {
            copy($obsdir . DIRECTORY_SEPARATOR . $files2[0], $dest2 . DIRECTORY_SEPARATOR . $files2[0]);
        }
    }

    /**
     * update list.dat
     * @param type $files
     */
    private function updateListdat($region_id) {
        $handle2 = werise_core_files::getHandle(werise_cdfdm_script::getListFile(), 'w');
        //foreach(werise_cdfdm_file::getTypes() as $file) {
        //fwrite($handle2,"r{$region_id} {$file}\n");
        //}
        fwrite($handle2, "r{$region_id} pr\n");
        fclose($handle2);
    }

    /**
     * get min max years from source files
     * to be used as replacements
     * @param type $files
     * @return type
     */
    private function getMinMaxYears($files) {
        if (!isset($files[0][1])) {
            return array(0, 0);
        }
        $min = DateTime::createFromFormat('Y-m-d', $files[0][1]);
        $max = DateTime::createFromFormat('Y-m-d', $files[0][2]);
        return array(intval($min->format('Y')), intval($max->format('Y')));
    }

    /**
     * format the output returned by fortran
     * @param type $cmdout
     */
    private function fmtCmdOut($cmdout) {
        $clean = array();
        $exclude = array('cdfdm.exe', '::', 'debug file created', '>cd ');
        $h2 = array('Control File Variables', 'StationCode', 'Num. of calculations', 'Calculation report', 'successfully');
        foreach ($cmdout as $line) {
            foreach ($exclude as $ex) {
                if (trim($line) == '') {
                    continue(2);
                }
                if (strpos($line, $ex) !== false) {
                    continue(2);
                }
            }
            foreach ($h2 as $head) {
                if (strpos($line, $head) !== false) {
                    $clean[] = "<h3>{$line}</h3>";
                    continue(2);
                }
            }
            $clean[] = $line;
        }
        return implode("<br />\n", $clean);
    }

}

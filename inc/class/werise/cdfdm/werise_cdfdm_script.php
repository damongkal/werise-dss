<?php
class werise_cdfdm_script {
    /**
     * real-time code
     */
    const _TYPE_REAL = 'real-time';
    /**
     * grasp code
     */
    const _TYPE_GRASP = 'grasp';

    /**
     * list.dat location
     * @return type
     */
    public static function getListFile() {
        return _CDFDM_DIR.'list.dat';
    }

    /**
     * script file location
     * @param type $type
     * @return type
     * @throws Exception
     */
    public static function getScriptFile($type) {
        if (!in_array($type, self::getTypes())) {
            throw new Exception('Invalid Script Type: ' . $type);
        }
        return _CDFDM_DIR.'src'.DIRECTORY_SEPARATOR.'src_'.$type.DIRECTORY_SEPARATOR.'cdfdm_'.$type.'.f90';
    }
    
    /**
     * control file location
     * @param type $type
     * @return type
     * @throws Exception
     */
    public static function getControlFile() {
        return _CDFDM_DIR.'control.dat';
    }    

    /**
     * update script file
     * @param type $type
     * @param type $country_code
     * @param type $region_id
     */
    public function updateScript($type, $country_code, $region_id) {
        // get min max values
        list($gcmdir,$gcmfiles) = werise_cdfdm_folder::getFolderInfo($country_code, $region_id, werise_cdfdm_folder::_SRC_GCM);
        list($gcmmin,$gcmmax) = $this->getMinMaxYears($gcmfiles);
        list($obsdir,$obsfiles) = werise_cdfdm_folder::getFolderInfo($country_code, $region_id, werise_cdfdm_folder::_SRC_OBS);
        list($obsmin,$obsmax) = $this->getMinMaxYears($obsfiles);

        // get template code
        $tplfile = _CDFDM_DIR.'src'.DIRECTORY_SEPARATOR.'cdfdm_main.bak';
        $tpl = file_get_contents($tplfile);

        // make replacements
        $replace['iyrstt_obs'] = $obsmin;
        $replace['iyrend_obs'] = $obsmax;
        $replace['iyrstt_gcm'] = $gcmmin;
        $replace['iyrend_gcm'] = $gcmmax;
        $trmin = $gcmmin;
        if ($obsmin<$gcmmin) {
            $trmin = $obsmin;
        }
        $replace['iyrstt_tr'] = $trmin;
        $trmax = $gcmmax;
        if ($obsmax>$gcmmax) {
            $trmax = $obsmax;
        }
        $replace['iyrend_tr'] = $trmax;
        $country_dir = str_replace(DIRECTORY_SEPARATOR,'/',werise_stations_country::getDir($country_code));
        $subfolder = $country_dir. 'r' . $region_id;
        $replace['dir_obs'] = './data/obs/'.$type.'/'.$subfolder;
        $replace['dir_gcm'] = './data/gcm/'.$subfolder;
        $replace['dir_cdfdm'] = './out/'.$type.'_forecast/'.$subfolder;
        foreach($replace as $src => $target) {
            $idx = '{{'.$src.'}}';
            $tmp = str_replace($idx,$target,$tpl);
            $tpl = $tmp;
        }
        // replace target code
        $handle = werise_core_files::getHandle(self::getScriptFile($type),'w');
        fwrite($handle,$tpl);
        fclose($handle);

        // update list.dat
        $this->updateListdat($gcmfiles);

        // prepare output folder
        $dir1 = werise_cdfdm_folder::getFolder($country_code, $region_id, werise_cdfdm_folder::_SRC_OUT);
        @mkdir($dir1);
    }

    /**
     * sources for CDFDM
     * @return type
     */
    public static function getTypes() {
        return array(self::_TYPE_GRASP, self::_TYPE_REAL);
    }

    /**
     * get min max years from source files
     * to be used as replacements
     * @param type $files
     * @return type
     */
    private function getMinMaxYears($files){
        if (!isset($files[0][1])) {
            return array(0,0);
        }
        $min = DateTime::createFromFormat('Y-m-d',$files[0][1]);
        $max = DateTime::createFromFormat('Y-m-d',$files[0][2]);
        return array(intval($min->format('Y')),intval($max->format('Y')));
    }
}
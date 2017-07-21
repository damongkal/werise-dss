<?php

class werise_cdfdm_folder {

    /**
     * GCM code
     */
    const _SRC_GCM = 'gcm';

    /**
     * observation real-time code
     */
    const _SRC_OBS = 'obs';

    /**
     * output real-time code
     */
    const _SRC_OUT = 'out';

    /**
     * get CDFDM folder
     * @param type $country_code
     * @param type $region_id
     * @param type $source
     * @return type
     */
    public static function getFolder($country_code, $region_id, $source) {
        if (!in_array($source, self::getSources())) {
            throw new Exception('Invalid Source: ' . $source);
        }
        
        $region_folder = '';
        if ($country_code!='') {
            // determine country/region subfolder 
            $country_dir = werise_stations_country::getDir($country_code);
            $region_folder = $country_dir . 'r' . $region_id;
        }
        
        // determine scripttype
        $scripttype = '';
        if ($source === self::_SRC_OBS || $source === self::_SRC_OUT) {
            $scripttype = werise_cdfdm_script::_TYPE_REAL;
        }

        // get folder
        switch ($source) {
            case self::_SRC_GCM :
            case self::_SRC_OBS :    
                return _CDFDM_DIR . 'data' . DIRECTORY_SEPARATOR . $source . DIRECTORY_SEPARATOR . $region_folder;
            case self::_SRC_OUT :    
                return _CDFDM_DIR . 'out' . DIRECTORY_SEPARATOR . $region_folder;
        }
    }
    
    /**
     * 
     * @param type $country_code
     * @param type $region_id
     * @param type $source
     * @return type
     */
    public static function getFolderInfo($country_code, $region_id, $source) {
        $dir = werise_cdfdm_folder::getFolder($country_code, $region_id, $source);
        $files = werise_core_files::getFiles($dir);
        if ($files) {
            $files2 = array();
            foreach ($files as $file) {
                if (strpos ($file,'debug')!==false) {
                    continue;
                }
                $min = 0;
                $max = 0;
                // read file contents 
                $tmpf = explode('.',$file);
                $tmpf2 = explode('_',$tmpf[0]);
                $cdfdm_file = new werise_cdfdm_fileread;
                $cdfdm_file->open($country_code, $region_id, $source, $tmpf2[0]);
                list($min,$max) = $cdfdm_file->getMinMax();
                $cdfdm_file->close();
                // store result
                $files2[] = array($file, $min, $max);
            }
            return array($dir,$files2);
        }
        return array($dir,false);
    }    

    /**
     * sources for CDFDM
     * @return type
     */
    public static function getSources() {
        return array(self::_SRC_GCM, self::_SRC_OBS, self::_SRC_OUT);
    }

}

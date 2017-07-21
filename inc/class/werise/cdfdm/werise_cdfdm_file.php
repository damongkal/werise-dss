<?php

abstract class werise_cdfdm_file {

    /**
     * header line
     */
    const _HDR = "  Yr Mn Dy DOY      Data";
    
    const _COL_YR = "yr";
    const _COL_MN = "mn";
    const _COL_DY = "dy";
    const _COL_DOY = "doy";
    const _COL_DATA = "data";

    /**
     * Rainfall file code
     */
    const _TYPE_PR = "pr";

    /**
     * Min Temperature file code
     */
    const _TYPE_TN = "tn";

    /**
     * Max Temperature file code
     */
    const _TYPE_TX = "tx";

    /**
     * Wind Speed file code
     */
    const _TYPE_WS = "ws";

    /**
     * file handle
     * @var type
     */
    protected $handle;
    
    protected $current_file = '';

    /**
     *
     * @param type $country_code
     * @param type $region_id
     * @param type $source
     * @param type $filetype
     */
    abstract public function open($country_code, $region_id, $source, $filetype);

    /**
     *
     * @param type $country_code
     * @param type $region_id
     * @param type $source
     * @param type $filetype
     */
    public function getHandle($country_code, $region_id, $source, $filetype, $mode) {
        $this->current_file = $this->getFileName($country_code, $region_id, $source, $filetype);
        $this->handle = werise_core_files::getHandle($this->current_file, $mode);
    }

    /**
     * close the file properly
     */
    public function close() {
        fclose($this->handle);
    }

    /**
     *
     * @param type $country_code
     * @param type $region_id
     * @param type $source
     * @param type $filetype
     * @return type
     * @throws Exception
     */
    public static function getFileName($country_code, $region_id, $source, $filetype) {
        if (!in_array($filetype, self::getTypes())) {
            throw new Exception('Invalid Type: ' . $filetype);
        }
        $folder = werise_cdfdm_folder::getFolder($country_code, $region_id, $source);
        return $folder . DIRECTORY_SEPARATOR . $filetype . '_r' . $region_id . '.dat';
    }

    /**
     * types for CDFDM
     * @return type
     */
    public static function getTypes() {
        return array(self::_TYPE_PR, self::_TYPE_TN, self::_TYPE_TX, self::_TYPE_WS);
    }
    
    /**
     * column index for CDFDM
     * @return type
     */
    public static function getColumnIndex() {
        return array(
            werise_cdfdm_file::_COL_YR,
            werise_cdfdm_file::_COL_MN,
            werise_cdfdm_file::_COL_DY, 
            werise_cdfdm_file::_COL_DOY, 
            werise_cdfdm_file::_COL_DATA);
    }    
    
    public function getCurrentFile() {
        return $this->current_file;
    }

}

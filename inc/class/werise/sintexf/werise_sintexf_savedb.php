<?php

class werise_sintexf_savedb {

    private $upload_dir;
    // output variables
    // db
    private $db;
    // debugger
    private $debug;

    public function __construct() {
        $this->db = Database_MySQL::getInstance();
        $this->debug = debug::getInstance();
        $this->upload_dir = _APP_DIR . '..'.DIRECTORY_SEPARATOR.'logs'.DIRECTORY_SEPARATOR.'cdfdm' . DIRECTORY_SEPARATOR;
    }

    public function execute($region_id) {
        if (count($_FILES)>0) {        
            $this->deleteOldFiles();
            $this->uploadFiles();
            foreach(werise_cdfdm_file::getTypes() as $file) {
                $this->processFiles($region_id, $file);
            }
        }
    }

    private function deleteOldFiles() {
        foreach(werise_cdfdm_file::getTypes() as $file) {
            @unlink("{$this->upload_dir}{$file}.txt");
        }
    }

    private function uploadFiles() {
        foreach ($_FILES as $key => $file) {
            if (isset($file["tmp_name"])) {
                move_uploaded_file($file["tmp_name"], "{$this->upload_dir}{$key}.txt");
            }
        }
    }

    private function processFiles($region_id, $file) {
        $handle = werise_core_files::getHandle("{$this->upload_dir}{$file}.txt");
        while (($buffer = fgets($handle, 4096)) !== false) {
            $this->debug->addLog('LINE: '.$buffer,false,'line');
            $vars = explode(':', $buffer);
            if (count($vars) === 4) {
                $date = "{$vars[0]}-{$vars[1]}-{$vars[2]}";
                //$debug->addLog($date,true,'date');
                $tmp = explode(' ', $vars[3]);
                $value = trim($tmp[1]);
                if ($value<-1) {
                    $value = -990;
                }
                //$this->debug->addLog($value,true,'VALUE');
                $this->saveRecord($region_id, $file, $date, $value);
            }
        }
        if (!feof($handle)) {
            fclose($handle);
            throw new Exception('unexpected fgets() fail');
        }
        fclose($handle);
    }

    private function saveRecord($region_id, $fld, $date, $value) {
        $sql = "
            INSERT INTO "._DB_DATA.".sintexf_raw
            (`region_id`, `forecast_date`, `%s`)
            VALUES (%u, '%s', %f)
            ON DUPLICATE KEY UPDATE `%s` = %f";
        $sql2 = sprintf($sql, $fld, $region_id, $date, $value, $fld, $value);
        $this->db->query($sql2);
    }

}

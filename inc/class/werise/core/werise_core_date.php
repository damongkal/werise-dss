<?php

class werise_core_date {

    /**
     * convert mktime format to UTC javascript readable format
     * @param type $date
     */
    public static function toUTC($date) {
        return $date * 1000;
    }
    public static function UTCtoDate($utc) {
        return date('Y-m-d',$utc/1000);
    }    
}

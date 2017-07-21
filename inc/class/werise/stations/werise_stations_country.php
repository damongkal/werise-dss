<?php

class werise_stations_country {

    public static function init() {
        $GLOBALS['weather_files'] = array(
            'ID' => array(
                'dir' => 'Indo',
                'file' => 'INDON',
                'country' => 'Indonesia'
            ),
            'LA' => array(
                'dir' => 'Laos',
                'file' => 'LAOS',
                'country' => 'Lao PDR'
            ),
            'PH' => array(
                'dir' => 'Phil',
                'file' => 'PHIL',
                'country' => 'Philippines'
            ),
            'TH' => array(
                'dir' => 'Thai',
                'file' => 'THAI',
                'country' => 'Thailand'
            )
        );
    }

    public static function getAll() {
        return $GLOBALS['weather_files'];
    }    

    public static function getName($countrycode) {
        return self::getVal($countrycode, 'country');
    }

    public static function getDir($countrycode) {
        return self::getVal($countrycode, 'dir').DIRECTORY_SEPARATOR;
    }

    public static function getFile($countrycode) {
        return self::getVal($countrycode, 'file');
    }

    private static function getVal($countrycode, $field) {
        if (isset($GLOBALS['weather_files'][$countrycode][$field])) {
            return $GLOBALS['weather_files'][$countrycode][$field];
        }
        throw new Exception('Invalid countrycode: ' . $countrycode);
    }

}

<?php

class werise_weather_filev3
{
    public static function createFile($file,$type,$handle)
    {
        $ver = _opt(sysoptions::_ORYZA_VERSION);
        if ($ver=='3')
        {
            return '';
        }   
        // check existence of version 3 file
        $f = werise_weather_file::getFileName($type, $file);
        $f = str_replace('weather','weather_ver3',$f);
        if ( file_exists($f) ) {
            return "ver3 file already exist: {$f}";
            //throw new Exception("ver3 file already exist: {$f}");
        }
        // create new file
        $handle2 = fopen($f, "w");

        // loop thru the weather records
        while (($buffer = fgets($handle, 4096)) !== false) {
            $vars = datafiles::validate($buffer);
            if ($vars)
            {
                $tmp = implode(' , ',$vars);
                fwrite($handle2,"     {$tmp}\r\n");
            } else
            {
                fwrite($handle2,"{$buffer}");
            }
        }

        // error! eof not reached.
        if (!feof($handle)) {
            throw new Exception("unexpected fgets() fail\n");
        }
        
        fclose($handle2);
        
        return $f;
    }    
}
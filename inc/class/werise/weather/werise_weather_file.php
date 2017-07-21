<?php

class werise_weather_file
{
    public static function getFolder($wtype)
    {
        $data_subdir = _DATA_SUBDIR_WEATHER_FORECAST;
        if ($wtype==werise_weather_properties::_REALTIME)
        {
            $data_subdir = _DATA_SUBDIR_WEATHER_REALTIME;
        }
        $ver = _opt(sysoptions::_ORYZA_VERSION);
        if ($ver==='3')
        {
            $data_subdir = str_replace('weather_ver1','weather_ver3',$data_subdir);
        }
        return $data_subdir;
    }
    
    public static function getFileList($wtype)
    {
        $data_subdir = self::getFolder($wtype);
        $files = false;
        $subdirs = werise_core_files::getFiles(_DATA_DIR . $data_subdir);
        if ($subdirs)
        {
            foreach ($subdirs as $subdir)
            {
                if ($subdir==='template.txt')
                {
                    continue;
                }
                $dir = _DATA_DIR . $data_subdir . $subdir;
                $data_files = werise_core_files::getFiles($dir);
                if ($data_files)
                {
                    foreach ($data_files as $data_file)
                    {
                        $files[] = array(
                            'file' => $data_file,
                            'subdir' => $subdir
                        );
                    }
                }
            }
        }
        return $files;
    }
    
    public static function getFileName($wtype,$file)
    {
        return _DATA_DIR.self::getFolder($wtype).$file;
    }
}
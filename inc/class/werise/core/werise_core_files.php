<?php

class werise_core_files {

    public static function getHandle($file,$mode='r', $opts = null) {
        $debug = debug::getInstance();
        $debug->addLog('GET-HANDLE: ' . $file, false, 'FILE');
        // check if file exist        
        $exist = file_exists($file);
        if ( $mode ==='x' && $exist ) {
            if (isset($opts['overwrite']) && $opts['overwrite'] === false) {
                throw new Exception('file already exist! : ' . $file);
            }
            unlink($file);
        }        
        if ( $mode ==='r' && !$exist ) {
            throw new Exception('file does not exist : ' . $file);
        }
        $handle = fopen($file, $mode);
        if (!$handle)
        {
            throw new Exception('file does not exist : ' . $file);
        }
        return $handle;
    }
    
    /**
     * get directory listing
     * @param string $dir
     * @return array
     */
    public static function getFiles($dir)
    {
        $debug = debug::getInstance();
        $debug->addLog('READ-DIR: ' . $dir, false, 'FILE');        
        $files = false;
        if ($handle = @opendir($dir))
        {
            while (false !== ($entry = readdir($handle)))
            {
                if ($entry != '.' && $entry != '..')
                {
                    $files[] = $entry;
                }
            }
            closedir($handle);
        }

        return $files;
    }
    
    public static function getFilePreview($file,$lines = 5) {
        $handle = self::getHandle($file);
        $line = 0;
        $preview = '';
        while (($buffer = fgets($handle, 4096)) !== false && $lines > $line++) {
            $preview .= "$buffer\n";
        }
        fclose($handle);        
        return $preview;
    }
    
    

}

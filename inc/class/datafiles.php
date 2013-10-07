<?php

class datafiles
{

    public function getAvailableFiles($data_subdir)
    {
        $files = false;
        $subdirs = $this->getFiles(_DATA_DIR . $data_subdir);
        if ($subdirs)
        {
            foreach ($subdirs as $subdir)
            {
                $dir = _DATA_DIR . $data_subdir . $subdir;
                $data_files = $this->getFiles($dir);
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

    /**
     * get directory listing
     * @param string $dir
     * @return array
     */
    private function getFiles($dir)
    {
        $files = false;
        if ($handle = opendir($dir))
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

    public function cleanVal($val)
    {
        if ($val == -99 || $val == -99000)
        {
            return 'NULL';
        }
        return $val;
    }

    /**
     * for weather files
     * parse the filename to its component identifiers
     * @global array $weather_files
     * @param string $filename
     * @return array
     */
    public function getDatasetFromFilename($filename)
    {
        global $weather_files;

        $strip_filename = $filename;
        $tmp1 = strrpos($filename, '/');
        if ($tmp1 !== false)
        {
            $strip_filename = substr($filename, $tmp1 + 1);
        }

        $country = '';
        $station = 0;
        $year = 0;

        $tmp4 = '';
        foreach ($weather_files as $country_code => $wfile)
        {
            if (strpos($strip_filename, $wfile['file']) !== false)
            {
                $country = $country_code;
                $tmp4 = str_replace($wfile['file'], '', $strip_filename);
            }
        }

        if ($tmp4 != '')
        {
            $tmp5 = explode('.', $tmp4);
            if (isset($tmp5[0]))
            {
                $station = $tmp5[0] + 0;
            }
            if (isset($tmp5[1]))
            {
                $tmp_year = $tmp5[1] + 0;
                if ($tmp_year < 60)
                {
                    $year = $tmp_year + 2000;
                }
                else
                {
                    $year = $tmp_year + 1900;
                }

                if ($tmp_year >= 900 && $tmp_year < 1000)
                {
                    $year = $tmp_year + 1000;
                }
            }
        }

        $error = '';
        if ($country == '')
        {
            $error = 'Invalid Country';
        }

        if ($station == 0)
        {
            $error = 'Invalid Station';
        }

        if ($year == 0)
        {
            $error = 'Invalid year';
        }

        return array(
            'file' => $strip_filename, 
            'country' => $country, 
            'station' => $station, 
            'year' => $year,
            'error' => $error);
    }

    /**
     * for weather files
     * @param type $line
     * @return boolean 
     */
    public function validate($line)
    {
        $vars = $this->parseVars($line);
        if (count($vars) == 9)
        {
            return $vars;
        }
        else
        {
            return false;
        }
    }
    
    /**
     * for oryza2000 output files (op.dat)
     * @param string $line
     * @return array
     */
    public function validate2($line)
    {
        $ignore = array('RUNNUM');
        $vars = $this->parseVars($line, $ignore);
        if ($vars)
        {
            return array($vars[0],$vars[1]);
        } else
        {
            return false;
        }
    }    

    
    /**
     * for oryza2000 output files (res.dat)
     * @param string $line
     * @return array
     */
    public function validate3($line)
    {
        // search for runnum
        if (strpos($line, 'Output from rerun set') !== false)
        {
            $tmp = explode(":",$line);
            return array('RUNNUM',intval(trim($tmp[1])));
        }
        
        $ignore = array('TIME');
        $vars = $this->parseVars($line, $ignore);
        if ($vars)
        {
            return array('DATA',array($vars[0],$vars[1]));
        } else
        {
            return false;
        }
    }        
    
    private function parseVars($line,$ignore=false)
    {
        // ignore comments
        if (strpos($line, '*') !== false)
        {
            return false;
        }
        
        // ignore keywords
        if ($ignore)
        {
            foreach ($ignore as $ig)
            {
                if (strpos($line,$ig)!==false)
                {
                    return false;
                }            
            }
        }
        
        $tmp = explode("\t",$line);
        if (!isset($tmp[1]))
        {
            $tmp = explode(" ",$line);
        }
        $vars = false;
        foreach ($tmp as $tmp2)
        {
            $tmp3 = trim($tmp2);
            if ($tmp2!='')
            {
                $vars[] = $tmp3;
            }
        }

        // ignore no data
        if (!isset($vars[1]))
        {
            return false;
        }
        
        return $vars;
    }
}
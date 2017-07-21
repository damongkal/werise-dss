<?php

class datafiles
{
    public static function cleanVal($val)
    {
        if ($val == -99 || $val == -99000 || $val == 'nan')
        {
            return 'NULL';
        }
        return $val;
    }

    /**
     * for weather files
     * parse the filename to its component identifiers
     * @param string $filename
     * @return array
     */
    public function getDatasetFromFilename($filename)
    {
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
        foreach (werise_stations_country::getAll() as $country_code => $wfile)
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
    public static function validate($line)
    {
        $vars = self::parseVars($line);
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
    public static function validate2($line)
    {
        $ignore = array('RUNNUM');
        $vars = self::parseVars($line, $ignore);
        if ($vars)
        {
            return $vars;
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
    public static function validate3($line)
    {
        // detect runnum
        if (stripos($line, 'output from rerun set') !== false)
        {
            $tmp = explode(":",$line);
            return array('RUNNUM',intval(trim($tmp[1])));
        }
        
        $ignore = array('TIME','WARNING','Please');        
        $vars = self::parseVars($line, $ignore);
        if ($vars)
        {
            return $vars;
        } else
        {
            return false;
        }
    }

    public static function parseVars($line,$ignore=false)
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

        // determine delimiter
        $chr = ' ';
        if (strpos($line,','))
        {
            $chr = ',';
        }

        $tmp = explode("\t",$line);
        if (!isset($tmp[1]))
        {
            $tmp = explode($chr,$line);
        }
        $vars = false;
        foreach ($tmp as $tmp2)
        {
            $tmp3 = trim($tmp2);
            if ($tmp3!='')
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
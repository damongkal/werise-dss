<?php
class werise_weather_acknowledge
{
    public function load($file,$type)
    {
        $f = werise_weather_file::getFileName($type, $file);
        $handle = werise_core_files::getHandle($f);

        // detect start of acknowledgement
        $done = false;
        while (($buffer = fgets($handle, 4096)) !== false && !$done) {
            if (strpos($buffer, '*****') !== false)
            {
                $done = true;
            }
        }

        // get acknowledgement
        $ack = '';
        $done2 = false;
        while (($buffer = fgets($handle, 4096)) !== false && !$done2) {
            // detect end of acknowledgement
            if (strpos($buffer, '*****') !== false)
            {
                $done2 = true;
            } else
            {
                $ack.=$buffer;
            }
        }

        fclose($handle);

        $this->add($file, $type, str_replace('*','',$ack));
    }
    
    private function add($prnfile,$wtype,$ack)
    {
        $db = Database_MySQL::getInstance();
        
        $datafile = new datafiles;
        $dataset = $datafile->getDatasetFromFilename($prnfile);
        $sql = sprintf("
            INSERT IGNORE INTO "._DB_DATA.".`weather_acknowledge` (
                `filename`, `wtype`, `country_code`, `station_id`, `year`, `remarks`)
            VALUES ('%s', '%s', '%s', %u, %u, '%s')",
                $prnfile,
                $wtype,
                $dataset['country'],
                $dataset['station'],
                $dataset['year'],
                $db->escape($ack));
        $db->query($sql);
    }    
    
    public function getRecords($station, $year, $wtype)
    {
        $db = Database_MySQL::getInstance();
        
        $sql = "
            SELECT a.`remarks`
            FROM "._DB_DATA.".`weather_acknowledge` AS a
            WHERE a.`wtype` = '".$db->escape($wtype)."'
                AND a.`country_code` = '".$db->escape($station->country_code)."'
                AND a.`station_id` = {$station->station_id}
                AND a.`year` = {$year}";

        $rs = $db->getRow($sql);
        if (!$rs)
        {
            return '';
        }

        // parse remarks
        $remarks = explode("\n",$rs->remarks);
        $sections = array();
        if (is_array($remarks))
        {
            $section_name = 'null';
            $buffer = '';
            foreach($remarks as $line)
            {
                // detect section line
                if (strpos($line,':'))
                {
                    $sections[$section_name] = $buffer; // save the previous section
                    $sec = explode(':',$line);
                    $section_name = strtolower(trim($sec[0])); // extract section name
                    preg_replace("/[^A-Za-z ]/", '', $section_name);
                    $buffer = ''; // clean buffer
                }
                $buffer.=$line; // compile lines to current section
            }
        }
        $ack = '';
        $keys = array('authors','author','sources','source');
        foreach ($keys as $key)
        {
            if (isset($sections[$key]))
            {
                $ack .= $sections[$key];
            }
        }
        return $ack;
    }    
}
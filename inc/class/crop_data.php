<?php
class crop_data
{
    private $db;

    public function __construct()
    {
        $this->db = Database_MySQL::getInstance();
    }

    public function load($file)
    {

        $handle = @fopen($file, "r");
        if ($handle)
        {
            while (($buffer = fgets($handle, 4096)) !== false)
            {
                $vars = $this->validate($buffer);
                if ($vars)
                {
                    $this->addData($vars);
                }
            }
            if (!feof($handle))
            {
                echo "Error: unexpected fgets() fail\n";
            }
            fclose($handle);
        } else
        {
            echo 'file does not exist.';
        }
    }

    private function validate($line)
    {
        if (strpos($line,'RUNNUM')!==false)
        {
            return false;
        }

        $tmp = explode("\t",$line);
        $vars = false;
        foreach ( $tmp as $tmp2)
        {
            $tmp3 = trim($tmp2);
            if ($tmp2!='')
            {
                $vars[] = $tmp3;
            }
        }

        return $vars;
    }

    private function addData($data)
    {
        $date = DateTime::createFromFormat('j-M-y', $data[2]);

        if (!$date)
        {
            echo '<pre>';
            print_r($data);
            die();
        }

        $sql = sprintf("
            INSERT INTO `crop_data` (
                `variety`, `setdate`, `yield`)
                VALUES ('%s', '%s', %s)",
                $data[1],
                date_format($date, 'Y-m-d'),
                $data[3]
                );

        $this->db->query($sql);
    }

    public function getYield($year)
    {
        $sql = sprintf("
            SELECT `variety`, `setdate`, `yield`
            FROM `crop_data`
            WHERE `setdate` BETWEEN '%s-01-01' AND '%s-12-31'
            ORDER BY `variety`, `setdate`",
            $year,$year);

        return $this->db->getRowList($sql);
    }
    
    public function getCropYears()
    {
        $sql = "SELECT DISTINCT DATE_FORMAT(setdate,'%Y') AS crop_year FROM `crop_data`";
        return $this->db->getRowList($sql);
    }
}
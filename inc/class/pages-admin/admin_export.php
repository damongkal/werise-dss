<?php
echo 'under construction...';die();
define('_CURRENT_OPT','Administration &raquo; Export to Server');
set_time_limit(300);

class admin_export
{
    private $db;

    public $arg_country = '';
    public $arg_station = 0;

    public $where;

    public function __construct()
    {
        $this->db = Database_MySQL::getInstance();
        
        $this->getParams();
        if ($this->arg_country!='' && $this->arg_station>0)
        {
            $cls->export();
        }        
    }

    /**
     * prepare expected form parameters
     */
    private function getParams()
    {
        if (isset($_GET['country']))
        {
            $this->arg_country = $this->db->escape($_GET['country']);
        }
        if (isset($_GET['country']))
        {
            $this->arg_station = intval($_GET['station']);
        }
    }

    public function export()
    {
        $sql0 = 'INIT-IMPORT';
        $this->sendtoServer($sql0);

        $this->where = "b.`country_code` = '$this->arg_country' AND b.`station_id` = $this->arg_station";

        $sql1 = "SELECT * FROM `weather_dataset` AS b WHERE {$this->where}";
        $parts1 = $this->makeTableData($sql1);
        $table_def1 = "INSERT INTO `export_weather_dataset` (`id`, `country_code`, `station_id`, `year`, `wtype`, `upload_date`) VALUES";
        $this->exportParts($table_def1, $parts1);

        $sql2 = "SELECT a.* FROM `weather_data` AS a INNER JOIN `weather_dataset` AS b ON a.`dataset_id` = b.`id` WHERE {$this->where}";
        $parts2 = $this->makeTableData($sql2);
        $table_def2 = "INSERT INTO `export_weather_data` (`dataset_id`, `observe_date`, `rainfall`, `min_temperature`, `max_temperature`, `irradiance`, `vapor_pressure`, `mean_wind_speed`, `decadal`) VALUES";
        $this->exportParts($table_def2, $parts2);

        $sql3 = "SELECT * FROM `oryza_dataset` AS b WHERE {$this->where}";
        $parts3 = $this->makeTableData($sql3);
        $table_def3 = "INSERT INTO `export_oryza_dataset` (`id`, `country_code`, `station_id`, `year`, `wtype`, `variety`, `fert`, `upload_date`) VALUES";
        $this->exportParts($table_def3, $parts3);

        $sql4 = "SELECT a.* FROM `oryza_data` AS a INNER JOIN `oryza_dataset` AS b ON a.`dataset_id` = b.`id` WHERE {$this->where}";
        $parts4 = $this->makeTableData($sql4);
        $table_def4 = "INSERT INTO `export_oryza_data` (`dataset_id`, `runnum`, `observe_date`, `yield`, `fert`) VALUES";
        $this->exportParts($table_def4, $parts4);

        $sql5 = "SELECT a.* FROM `oryza_datares` AS a INNER JOIN `oryza_dataset` AS b ON a.`dataset_id` = b.`id` WHERE {$this->where}";
        $parts5 = $this->makeTableData($sql5);
        $table_def5 = "INSERT INTO `export_oryza_datares` (`dataset_id`, `runnum`, `day`, `dvs`, `zw`, `doy`) VALUES";
        $this->exportParts($table_def5, $parts5);
        
        $sql6 = 'FINISH-IMPORT';
        $this->sendtoServer($sql6);        
    }

    private function makeTableData($sql)
    {
        $parts = array();
        $rec = array();
        $ctr = 0;
        foreach($this->db->getRowList($sql,'array') as $row)
        {
            $rec[] = "('" . implode("','",$row) . "')";
            if ($ctr>1 && ($ctr%3000)===0)
            {
                $parts[] = implode(",\n",$rec);
                $rec = array();
            }
            $ctr++;
        }
        $parts[] = implode(",\n",$rec);
        return $parts;
    }

    private function exportParts($table_def,$parts)
    {
        foreach ($parts as $part)
        {
            $this->sendtoServer(trim($table_def.' '.$part).';');
        }
    }

    private function sendtoServer($sql)
    {
        $ch = curl_init('http://werise.no-ip.biz//admin_importsql.php');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch,CURLOPT_POST, 1);
        curl_setopt($ch,CURLOPT_POSTFIELDS, "sqlstr={$sql}");
        $result = curl_exec($ch);
        curl_close($ch);
        echo $result.'<br />';
        /*
        if ($result=='ACK')
        {
            echo "$sql<br />";
        } else
        {
            echo $result;
        }*/
    }
}
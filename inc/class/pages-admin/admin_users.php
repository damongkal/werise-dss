<?php
define('_CURRENT_OPT','Administration &raquo; Users');

class admin_users
{
    public $userid = null;
    
    public function __construct() {
        if (isset($_GET['userid']))
        {
            $this->userid = intval($_GET['userid']);
        }
    }
    
    public function getUser()
    {
        $users = new werise_users_model;
        $user = new werise_users_record;
        $user->userid = $this->userid;
        $rs = $users->getRecords($user);
        if ($rs)
        {
            return $rs[0];
        }
        return false;    
    }
    
    public function formatEnabled($rec)
    {
        if ($rec->is_enabled==1)
        {
            return '<span class="badge badge-success">'.$rec->username.'</span>';
        } else
        {
            return '<span class="badge badge-danger">NO</span>';
        }
    }
    
    public function getWeatherAccessLog()
    {
        $db = Database_MySQL::getInstance();        
        $sql = "SELECT * FROM weather_access_log WHERE userid={$this->userid}";
        return $db->getRowList($sql);
    }
    
    public function getOryzaAccessLog()
    {
        $db = Database_MySQL::getInstance();        
        $sql = "SELECT * FROM oryza_access_log WHERE userid={$this->userid}";
        return $db->getRowList($sql);
    }    
}
<?php
class rcm_api
{
    private $db;
    
    private $ret = null;
    private $ret_array = null;
    
    private $yld_conv = null;
    
    public function __construct() {
        $this->db = Database_MySQL::getInstance();
    }

    public function compute($args)
    {
        $variety_id = 29;
        if (isset($args['variety_id']))
        {
            $variety_id = intval($args['variety_id']);
        }

        $ha = 10;
        if (isset($args['ha']))
        {
            $ha = $args['ha'];
        }

        $water = 1;
        if (isset($args['water']))
        {
            $water = $args['water'];
        }
        
        $yld = 3;
        if (isset($args['yld']))
        {
            $yld = $args['yld'];
        }
        
        $mc = 907.18474 / 0.88;
        $kg = 30;
        $sacks = round( ($yld * $args['ha'] * $mc) / $kg,0,PHP_ROUND_HALF_UP);        
        $this->yld_conv = "$yld t/ha => {$sacks} sacks @ {$kg} kg each";

        // try database
        $sql = sprintf("
            SELECT `result`
            FROM `rcm_compute`
            WHERE `variety_id` = %d
                AND `ha` = %d
                AND `yld` = %01.2f
                AND `water` = %d",$variety_id,$ha,$yld,$water);
                
        $dbret = $this->db->getRow($sql);        
        
        if ($dbret)
        {
            $this->ret = $dbret->result;
        } else
        {
            $url = "http://webapps.irri.org/ph/rcmlib/compute.php?lang=1&session_id=H29apvwFn8BPtjZNdnAR1VdgCYB2N9Aw8iua9ZJB&fname_ext=&lname_ext=&reside=0&cellExtension=&emailExtension=&profList=0&profList_index=0&othersProfText=&have_fb=0&have_smphone=0&region=5&province=3&municipality=5&crop=1&season=1&pc=1&irrig=3&water={$water}&ce=1&straw=1&ins=2&low=2&org=2&rev=1&prev_season=1&region_id=5&province_id=25&municipality_id=488&variety_id={$variety_id}&refer=http%3A//www.google.com.ph/url%3Fsa%3Dt%26rct%3Dj%26q%3D%26esrc%3Ds%26source%3Dweb%26cd%3D1%26ved%3D0CCoQFjAA%26url%3Dhttp%253A%252F%252Fwebapps.irri.org%252Fph%252Frcm%252F%26ei%3DCmXbUv1ZkLiIB4WsgNAE%26usg%3DAFQjCNGcfB4A7IZRJWDTGM_SS5MLGUi-Aw%26bvm%3Dbv.59568121%2Cd.aGc&field=Archie&fsize=1&ha={$ha}&useNM=2&ref_id=11407&ext_id=2659&sowDate=12/15/2014&vars=1&variety=4&GW=2&sa=1&sacks={$sacks}&kg={$kg}&MC=25&MC2=22&sacks3=&npk=0&k=0&ref_id=11407&appname=ph/rcm&_=1390114694769";
            $this->ret = file_get_contents($url);

            $sql2 = sprintf("
                INSERT INTO `rcm_compute`
                (`variety_id`, `ha`, `yld`, `water`, `result`, `date_created`)	
                VALUES
                (%d, %d, %01.2f, %d, '%s', CURDATE())",
                $variety_id, $ha, $yld, $water, $this->ret);            
            $this->db->query($sql2);
        }
        
        $ret2 = explode(';',$this->ret);
        $this->ret_array = array();
        foreach($ret2 as $ret)
        {
            $tmp = explode('=',$ret);
            $key = '';
            $val = '';
            if (isset($tmp[1]))
            {
                $tmp1 = trim($tmp[0]);
                $tmp2 = str_replace('[','_',$tmp1);
                $key = str_replace(']','_',$tmp2);
                $val = trim($tmp[1]);
            }
            $this->ret_array[$key] = $val;
        }
    }

    /**
     * getter method for variables populated in compute()
     * @return type
     */
    public function getRaw()
    {
        return $this->ret_array;
    }
    
    /**
     * getter method for variables populated in compute()
     * @return type
     */    
    public function getYldConv()
    {
        return $this->yld_conv;
    }
    
    /**
     * getter method for variables populated in compute()
     * @return type
     */    
    public function getVal($key)
    {
        if (isset($this->ret_array[$key]))
        {
            return $this->ret_array[$key];
        } else
        {
            return false;
        }
    }
}
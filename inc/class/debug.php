<?php
class debug
{
    protected $log = array();

    protected static $instance = null;
    protected function __construct()
    {
        //Thou shalt not construct that which is unconstructable!
    }
    protected function __clone()
    {
        //Me not like clones! Me smash clones!
    }

    public static function getInstance()
    {
        if (!isset(self::$instance))
        {
            $className = __CLASS__;
            self::$instance = new $className;
        }
        return self::$instance;
    }
    
    public function is_debug_mode()
    {
        if (isset($_GET['debugmode']) && $_GET['debugmode']==1)
        {
            $_SESSION['debugmode'] = true;
        }
        return isset($_SESSION['debugmode']) ? $_SESSION['debugmode'] : false;
    }    
    
    public function addLog($str, $mixed = false, $badge = '')
    {
        if (!$this->is_debug_mode())
        {
            return;
        }
        $badge_tag = '';
        if ($badge!='')
        {
            $badge_tag = '<span class="badge badge-secondary">'.$badge.'</span> ';
        }
        if ($mixed)
        {
            $this->log[] = $badge_tag . '<pre>' . htmlentities(print_r($str,true)) . '</pre>';            
        } else
        {
            $this->log[] = $badge_tag . htmlentities($str);
        }
    }
    
    public function showLog($style='css')
    {
        if(_ADM_ENV==='PROD' && dss_auth::getUsername()!=='admin')
        {
            return;
        }        
        if (!$this->is_debug_mode())
        {
            return;
        }
        
        $style1 = 'id="sql-log"';
        $style2 = '';
        $style3 = '';
        if ($style==='inline')
        {
            $style1 = 'style="border:1px solid #000"';
            $style2 = 'style="border:1px solid #000"';
            $style3 = 'style="border:1px solid #000"';            
        }
        
        echo '<div '.$style1.'>';
        
        // debug trace
        echo '<div '.$style2.'><b>debug trace</b></div>';
        foreach($this->log as $log)
        {
            echo '<div '.$style3.'>'.$log.'</div>';
        }
        
        // session data
        echo '<div '.$style2.'><b>session data</b></div>';
        if (is_array($_SESSION))
        {
            foreach($_SESSION as $sesskey => $sessval)
            {
                if (is_array($sessval))
                {
                    $sessval2 = '<pre>'.print_r($sessval,true).'</pre>';
                }else
                {
                    $sessval2 = $sessval;
                }
                
                echo '<div '.$style3.'><span class="badge badge-secondary">'.$sesskey.'</span> '.$sessval2.'</div>';
            }
        }
        
        echo '</div>';        
    }
}
/**
 * quick debugger
 * @param type $var
 */
function dbg($var)
{
    echo '<b>debug:</b><br /><pre>';print_r($var);echo '</pre>';    
}
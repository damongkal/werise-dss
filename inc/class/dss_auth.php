<?php

class dss_auth {
    
    const _AUTH_USERID = 'auth_userid';
    const _AUTH_USERNAME = 'auth_username';    

    public static function checkAccess() {
        return; // disable http auth
        
        if (!isset($_SERVER['PHP_AUTH_USER'])) {
            header('WWW-Authenticate: Basic realm="WeRise Decision Support System"');
            header('HTTP/1.0 401 Unauthorized');
            echo 'You cancelled authentication. Redirecting you back to WeRise ...<script type="text/javascript">window.location = "index.php"</script>';
            exit;
        } else {
            // check session
            if (self::getUsername()!=='')
            {
                return;
            }            
            // check db
            $rs = self::checkAccessDb($_SERVER['PHP_AUTH_USER'],$_SERVER['PHP_AUTH_PW']);
            if (!$rs)
            {
                self::denyAccess();
            }
        }
    }
    
    public static function checkAccess2()
    {
        $pageaction = '';
        if (isset($_REQUEST['pageaction']))
        {
            $pageaction = 'form_' . $_REQUEST['pageaction'];
        }
        $secured = array('form_weather','form_oryza');
        if (_ADM_ENV==='PROD')
        {
            if (self::getUsername()==='')
            {
                if (isset($_SERVER['SCRIPT_NAME']) && strpos($_SERVER['SCRIPT_NAME'],'admin.php'))
                {
                    return false;
                }
                if (in_array($pageaction,$secured))
                {
                    return false;
                }
            }
            return true;
        } else
        {
            return true;
        }
    }
    
    public static function checkAccessDb($username,$password)
    {
        $users = new werise_users_model;
        $user = new werise_users_record;
        $user->username = $username;
        $user->password = $password;
        $user->is_enabled = true;
        $rs = $users->getRecords($user);        
        if ($rs)
        {
            self::setUser($rs[0]->userid,$rs[0]->username);
        }
        return $rs;
    }
    
    private static function denyAccess()
    {
        header('Location: index.php?err=noaccess');
        exit;
    }
    
    private static function setUser($userid,$username)
    {
       $_SESSION[self::_AUTH_USERID] = intval($userid);
       $_SESSION[self::_AUTH_USERNAME] = $username;
    }
    
    public static function getUserId()
    {
        if (isset($_SESSION[self::_AUTH_USERID])) {
            return $_SESSION[self::_AUTH_USERID];
        }
        return false;
    }    
    
    public static function getUsername()
    {
        if (isset($_SESSION[self::_AUTH_USERNAME])) {
            return $_SESSION[self::_AUTH_USERNAME];
        }
        return '';
    }    
    
    public static function logAccess($pageaction)
    {
        $userid = self::getUserId();
        $ip = 'none';
        if (isset($_SERVER['REMOTE_ADDR']))
        {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        $db = Database_MySQL::getInstance();
        $sql = "INSERT INTO `accesslog` (`userid`, `pageaction`, `ip`, `date_created`) VALUES ('%s','%s','%s',NOW())";
        $db->query(sprintf($sql,$userid,$db->escape($pageaction),$db->escape($ip)));
    }
}

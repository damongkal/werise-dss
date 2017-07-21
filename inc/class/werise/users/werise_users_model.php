<?php
class werise_users_model
{    
    public function create(werise_users_record $user)
    {        
        $db = Database_MySQL::getInstance();
        $sql = "
            INSERT INTO `users` (
                `username`, `password`, `fullname`, 
                `address`, `email`, `phone`, 
                `is_enabled`, `date_created` )
            VALUES (
                '%s', '%s', '%s', 
                '%s', '%s', '%s', 
                1, NOW() 
            )";
        $sql2 = sprintf(
            $sql,
            $db->escape($user->username),
            $db->escape(md5($user->password)),
            $db->escape($user->fullname),
            $db->escape($user->address),
            $db->escape($user->email),
            $db->escape($user->phone));
        $db->query($sql2);
        
        $userid = $db->getInsertId();                
        return $userid;
    }
    
    public function createReason($userid, werise_users_record $user)
    {
        $db = Database_MySQL::getInstance();
        $sql = "
            INSERT INTO `users_info` (
                `userid`, `reason`)
            VALUES (
                %u, '%s'
            )";
        $sql2 = sprintf(
            $sql,
            intval($userid),
            $db->escape($user->reason));
        $db->query($sql2);        
    }
    
    public static function getRecords(werise_users_record $user = null)
    {
        $db = Database_MySQL::getInstance();        
        
        $where = array();
        if (!is_null($user))
        {
            if (!is_null($user->userid))
            {
                $where[] = sprintf("a.`userid` = %d",intval($user->userid));
            }            
            if ($user->username!=='')
            {
                $where[] = sprintf("a.`username` = '%s'",$db->escape($user->username));
            }
            if ($user->email!=='')
            {
                $where[] = sprintf("a.`email` = '%s'",$db->escape($user->email));
            }
            if ($user->password!=='')
            {
                $where[] = sprintf("a.`password` = MD5('%s')",$db->escape($user->password));
            }            
            if (!is_null($user->is_enabled))
            {
                $where[] = sprintf("a.`is_enabled` = %d", intval($user->is_enabled));
            }                    
        }
        $where_clause = '';
        if (count($where)>0)
        {
            $where_clause = 'WHERE ' . implode(' AND ', $where);            
        }
        
        $sql = "SELECT a.*,b.reason FROM `users` AS a LEFT JOIN `users_info` AS b ON a.`userid` = b.`userid` {$where_clause} ORDER BY `username`";
        return $db->getRowList($sql);
    }

}
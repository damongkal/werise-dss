<?php

/**
 * MySQL database class
 * @author gabe@fijiwebdesign.com
 *
 * Invocation example
 * $config = array('host' => $host, 'user' => $user, 'password' => $password, 'database' => $database);
 * $DB = Database_MySQL::getInstance($config);
 * $rows = $DB->getRowList("select * from table1 where col1 = %d and col2 = '%s' LIMIT 1", 5, '6');
 *
 */
class Database_MySQL
{

    protected $result;
    protected $resource;
    protected $debug;

    protected static $instance = null;
    protected function __construct()
    {
        //Thou shalt not construct that which is unconstructable!
    }
    protected function __clone()
    {
        //Me not like clones! Me smash clones!
    }

    /**
     * 
     * @return Database_MySQL
     */
    public static function getInstance()
    {
        if (!isset(self::$instance))
        {
            $className = __CLASS__;
            self::$instance = new $className;
        }
        return self::$instance;
    }

    /**
     * Connect to the MySQL Database
     * @param $host String Host
     * @param $user String Username
     * @param $password Password
     * @param $database Database name
     * @return Bool
     */
    public function connect($config)
    {
        $this->debug = debug::getInstance();
        extract($config);
        if ($this->resource = mysqli_connect($host, $user, $password))
        {
            mysqli_set_charset($this->resource,'utf8');            
            return mysqli_select_db($this->resource,$database);
        }
        throw new Exception('db connect failed.');
    }

    protected function setQuery($args)
    {
        call_user_func_array(array($this, 'query'), $args);
    }

    /**
     * Execute a Query
     * @param $query SQL
     * @param Int|String optional parameters to interpolate with SQL
     * @return Bool
     */
    public function query($query)
    {
        /*$args = func_get_args();
        if (count($args) > 1)
        {
            array_shift($args);
            $args = array_map('mysqli_real_escape_string', $args);
            array_unshift($args, $query);
            $query = call_user_func_array('sprintf', $args);
        }*/
        $this->debug->addLog($query,false,'SQL');
        if (!$this->result = mysqli_query($this->resource,$query))
        {
            echo "QUERY ERROR: $query\n";
            throw new Exception('Query failed: ' . mysqli_error($this->resource));
        }
        return $this->result;
    }

    /**
     * Retrieve a an Array of Objects from query resultset
     * @param $query SQL
     * @param Int|String optional parameters to interpolate with SQL
     * @return Array
     */
    public function getRowList($query = null, $returntype = 'assoc')
    {
        if ($query)
        {
            $args = func_get_args();
            $this->setQuery($args);
        }
        if ($this->result)
        {
            $rows = array();
            while ($row = mysqli_fetch_assoc($this->result))
            {
                if ($returntype === 'array')
                {
                    $rows[] = $row;
                } else {
                    $rows[] = (object) $row;
                }
            }
            return $rows;
        } else
        {
            return false;
        }
    }

    /**
     * Retrieve a single row in query resultset as Object
     * @param $query SQL
     * @param Int|String optional parameters to interpolate with SQL
     * @return Object
     */
    public function getRow($query = null)
    {
        if ($query)
        {
            $args = func_get_args();
            $this->setQuery($args);
        }
        if ($this->result)
        {
            if ($row = mysqli_fetch_assoc($this->result))
            {
                return (object) $row;
            }
        }
        return false;
    }

    /**
     * Retrieve a single result
     * @param $query String
     * @param Int|String optional parameters to interpolate with SQL
     * @return String
     */
    public function getResult($query = null)
    {
        if ($query)
        {
            $args = func_get_args();
            $this->setQuery($args);
        }
        if ($this->result)
        {
            if ($row = mysqli_fetch_row($this->result))
            {
                return $row[0];
            }
        }
        return false;
    }

    /**
     * Retrieve number of rows affected in last insert|update query
     * @return Int
     */
    public function getAffectedRows()
    {
        return mysqli_affected_rows($this->resource);
    }

    /**
     * Get the auto-increment column of the last insert
     * @return Int|false
     */
    public function getInsertId()
    {
        return mysqli_insert_id($this->resource);
    }
    
    public function escape($str)
    {
        return mysqli_real_escape_string($this->resource, $str);
    }
    
    /**
     * concatenate filters into a valid where clause
     * @param type $where
     */
    public static function getWhere($where)
    {
        if (count($where)>=1)
        {
            return ' WHERE '.implode(' AND ',$where);
        }        
        return '';
    }
}

$config = array('host' => _DB_HOST, 'user' => _DB_USER, 'password' => _DB_PWD, 'database' => _DB_NAME);
$db = Database_MySQL::getInstance();
$db->connect($config);
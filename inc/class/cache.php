<?php

class cache
{
    private $db;
    private $keyid;

    /**
     * instantiate cache
     * @param string $keyid identifier of cache data
     */
    public function __construct($keyid)
    {
        $this->db = Database_MySQL::getInstance();
        $this->keyid = $keyid;
    }

    /**
     * read cached data
     * @return boolean 
     */
    public function read()
    {
        $sql = "SELECT cache_data FROM "._DB_DATA.".`cache` WHERE keyid='{$this->keyid}'";
        $rs = $this->db->getRow($sql);
        if ($rs)
        {
            return unserialize($rs->cache_data);
        }
        return false;
    }

    /**
     * write data to cache
     * @param array $data 
     */
    public function write($data)
    {
        $sql = sprintf("
            INSERT INTO "._DB_DATA.".`cache` (
                keyid, cache_data, cache_date)
            VALUES ('%s', '%s', NOW())", $this->keyid, serialize($data));
        $this->db->query($sql);
    }
}
<?php

class Models_SQLCache
{

    private static $instance;
    private $cache = array();
    private $useCache = true;


    private function __construct()
    {

    }

    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            $c = __CLASS__;
            self::$instance = new $c;
        }
        return self::$instance;
    }

    public function getAllCache()
    {
        return $this->cache;
    }

    public function getCache($_ckey, $sqlFetchMode = DB_FETCHMODE_OBJECT)
    {

        if ($_ckey != '') {
            $ckey = md5($_ckey . $sqlFetchMode);
            if ($this->cacheDataAvailable($ckey)) {
                return unserialize($this->cache[$ckey]);
            }
        }
    }

    public function emptyCache()
    {
        unset($this->cache);
        $this->cache = array();
    }

    public function ignoreCache()
    {
        $this->useCache = false;
    }

    public function setCache($object, $_ckey, $sqlFetchMode = DB_FETCHMODE_OBJECT)
    {
        $ckey = md5($_ckey . $sqlFetchMode);
        $this->cache[$ckey] = serialize($object);
    }

    private function cacheDataAvailable($ckey)
    {
        if (array_key_exists($ckey, $this->cache)) {
            return true;
        } else {
            return false;
        }
    }




    public function getData($sql, $sqlFetchMode = DB_FETCHMODE_OBJECT, $cached = true)
    {
        global $db;

        $ckey = md5($sql . $sqlFetchMode);
        if (($this->useCache) && ($cached && $this->cacheDataAvailable($ckey))) {
            // echo "from cache\n";
            $object = unserialize($this->cache[$ckey]);
        } else {
            //  echo "from db\n";
            $object = $db->getAll($sql, null, $sqlFetchMode);

            $this->setCache($object, $sql, $sqlFetchMode);
        }
        $this->useCache = true; //by default turn on caching after this db call
        return $object;
    }

}

?>
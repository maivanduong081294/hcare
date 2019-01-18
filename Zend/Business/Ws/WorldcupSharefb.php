<?php

class Business_Ws_WorldcupSharefb extends Business_Abstract {

    private $_tablename = 'sharefb';
    private static $_instance = null;

    function __construct() {
        
    }

    //public static function
    /**
     * get instance of Business_Ws_WorldcupSharefb
     *
     * @return Business_Ws_WorldcupSharefb
     */
    public static function getInstance() {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Get DB Connection
     *
     * @return Zend_Db_Adapter_Abstract
     */
    private function getDbConnection() {
        $db = Globals::getDbConnection('worldcup');
        return $db;
    }

    private function getCacheInstance() {
        $cache = GlobalCache::getCacheInstance('ws');
        return $cache;
    }

    public function getDetail($userid) {
        $cache = $this->getCacheInstance();
        $key = 'wc.sharefb.'.$userid;
        $result = $cache->getCache($key);
        if ($result === false){
        $db = $this->getDbConnection();
            $query = "SELECT * FROM " . $this->_tablename . " WHERE userid='" . parent::adaptSQL($userid) . "'";
            $result = $db->fetchAll($query);
            if ($result != null) {
                $cache->setCache($key, $result[0]);
                $result = $result[0];
            }
        }
        return $result;
    }
    
    public function insert($data) {
        $db = $this->getDbConnection();
        $db->insert($this->_tablename, $data);
            
        return $db->lastInsertId();
    }   
    
    
}

?>
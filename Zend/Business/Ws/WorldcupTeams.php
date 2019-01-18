<?php

class Business_Ws_WorldcupTeams extends Business_Abstract {

    private $_tablename = 'teams';
    private static $_instance = null;

    function __construct() {
        
    }

    //public static function
    /**
     * get instance of Business_Ws_WorldcupTeams
     *
     * @return Business_Ws_WorldcupTeams
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

    public function getDetail($id) {
        $cache = $this->getCacheInstance();
        $key = 'team.'.$id;
        $result = $cache->getCache($key);
        if ($result === false) {
            $db = $this->getDbConnection();
            $query = "SELECT * FROM " . $this->_tablename . " WHERE id='" . parent::adaptSQL($id) . "'";
            $ret = $db->fetchAll($query);
            $result = $ret[0];
            $cache->setCache($key, $result);
        }
        return $result;
    }
    
    public function getListByGroup() {
        $cache = $this->getCacheInstance();
        $key = 'group';
        $result = $cache->getCache($key);
        if ($result === false) {
            $db = $this->getDbConnection();
            $query = "SELECT * FROM " . $this->_tablename . " WHERE 1=1";
            $ret = $db->fetchAll($query);
            $result = $ret;
            $cache->setCache($key, $result);
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
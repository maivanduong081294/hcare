<?php

class Business_Ws_Worldcupvotes extends Business_Abstract {

    private $_tablename = 'votes';
    private static $_instance = null;

    function __construct() {
        
    }

    //public static function
    /**
     * get instance of Business_Ws_Worldcupvotes
     *
     * @return Business_Ws_Worldcupvotes
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
    
    
    public function getListByUID($userid) {
        $cache = $this->getCacheInstance();
        $key = 'wc.vote.listuid.'.$userid;
        $result = $cache->getCache($key);
        if ($result === false) {
            $db = $this->getDbConnection();
            $query = "SELECT * FROM " . $this->_tablename . " WHERE userid = " . $userid;
            $result = $db->fetchAll($query);
            if ($result != null) {
                $cache->setCache($key, $result);
            }
        }
        return $result;
    }
    
    /*
     * Get user by matcheid and userid
     */
    public function getDetail2($matcheid, $userid) {
        $cache = $this->getCacheInstance();
        $key = 'wc.vote.detail2.'.$matcheid.$userid;
        $result = $cache->getCache($key);
        if ($result === false) {
            $db = $this->getDbConnection();
            $query = "SELECT * FROM " . $this->_tablename . " WHERE userid = " . (int) $userid  . " AND matcheid=" . (int) $matcheid . "";
            $result = $db->fetchAll($query);
            if ($result != null) {
                $cache->setCache($key, $result);
            }
        }
        return $result[0];
    }
    
    public function getDetailByMUID($uid, $matcheid) {
        $cache = $this->getCacheInstance();
        $key = 'wc.vote.detail.'.$uid.$matcheid;
        $result = $cache->getCache($key);
        if ($result === false){
            $db = $this->getDbConnection();
            $query = "SELECT * FROM " . $this->_tablename . " WHERE matcheid='" . parent::adaptSQL($matcheid) . "' AND userid = " . $uid;
            $ret = $db->fetchAll($query);
            $result = $ret[0];
            if (count($result)>0)
                $cache->setCache($key, $result);                       
        }
        return $result;
    }
    
    public function insert($data) {
        $cache = $this->getCacheInstance();
        
        $db = $this->getDbConnection();
        $db->insert($this->_tablename, $data);
            
        //clear matches cache
        $_key = 'team.'.$data["team1id"];
        $cache->deleteCache($_key);
        $_key = 'team.'.$data["team2id"];
        $cache->deleteCache($_key);
            
        return $db->lastInsertId();
    }   
}

?>
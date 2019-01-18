<?php

class Business_Ws_Worldcupmatches extends Business_Abstract {

    private $_tablename = 'matches';
    private static $_instance = null;

    function __construct() {
        
    }

    //public static function
    /**
     * get instance of Business_Ws_Worldcupmatches
     *
     * @return Business_Ws_Worldcupmatches
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

    public function getDetail($id, $_cache=true) {
        $cache = $this->getCacheInstance();
        $key = 'wc.matche.detail'.$id;
        $result = $cache->getCache($key);
            
        if ($_cache == false) {
            $result=false;
        }            
        
        if ($result === false) {
            $db = $this->getDbConnection();
            $query = "SELECT * FROM " . $this->_tablename . " WHERE id='" . parent::adaptSQL($id) . "'";
            $ret = $db->fetchAll($query);
            $result = $ret[0];
            $cache->setCache($key, $result);
        }   
        return $result;
    }
    
    public function getListAll($_cache=true) {
        $cache = $this->getCacheInstance();
        $key = 'wc.matches.list';
        $result = $cache->getCache($key);
        if ($_cache==false) {
            $result = false;
        }
        if ($result === false) {
            $db = $this->getDbConnection();
            $query = "SELECT * FROM " . $this->_tablename . " order by datetime asc";
            $result = $db->fetchAll($query);
            $cache->setCache($key, $result);
        }
        return $result;
    }
    
    public function getListNotExpired($round) {
        $min_15 = 15 * 60;
        $time = time();
        $ts = $time - $min_15;
//        $ts = $time + (600 * 24 * 60 * 60);
        $date = date("Y-m-d H:i:s", $ts);
                
        $cache = $this->getCacheInstance();
//        $key = 'wc.matches.list.noexp.'.$round;
        $key = 'wc.matches.list.noexp';
        $result = $cache->getCache($key);
        $result = false;
        if ($result === false) {
            $db = $this->getDbConnection();
//            $query = "SELECT * FROM " . $this->_tablename . " where round = '$round' AND datetime <= '$date' group by datetime order by datetime asc";
            $query = "SELECT * FROM " . $this->_tablename . " where datetime >= '$date' order by datetime asc";
                
            $result = $db->fetchAll($query);
            $cache->setCache($key, $result);
        }
        return $result;
    }
    
    public function insert($data) {
        $db = $this->getDbConnection();
        $db->insert($this->_tablename, $data);
        return $db->lastInsertId();
    }

    public function update($id, $data) {
        $db = $this->getDbConnection();
        $where = array();
        $where[] = "id='" . parent::adaptSQL($id) . "'";
        $db = $this->getDbConnection();
        $result = $db->update($this->_tablename, $data,$where);
        
        //remove cache
        $cache = $this->getCacheInstance();
        $key = 'wc.matche.detail'.$id;
        $cache->deleteCache($key);
        $key = 'wc.matches.list';
        $cache->deleteCache($key);
        
        return $result;
    }

}

?>
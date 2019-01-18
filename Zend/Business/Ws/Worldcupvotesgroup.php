<?php

class Business_Ws_Worldcupvotesgroup extends Business_Abstract {

    private $_tablename = 'votes_group';
    private static $_instance = null;

    function __construct() {
        
    }

    //public static function
    /**
     * get instance of Business_Ws_Worldcupvotesgroup
     *
     * @return Business_Ws_Worldcupvotesgroup
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
    
    /*
     * Get user by userid
     */
    public function getDetail($userid) {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " WHERE userid = " . $userid;
        $result = $db->fetchAll($query);
        return $result[0];
    }
    
    public function insert($data) {
        $db = $this->getDbConnection();
        $db->insert($this->_tablename, $data);
        return $db->lastInsertId();
    }   
}

?>
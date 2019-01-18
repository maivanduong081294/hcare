<?php

class Business_Ws_Worldcupvotesexcplayer extends Business_Abstract {

    private $_tablename = 'votes_win_excplayer';
    private static $_instance = null;

    function __construct() {
        
    }

    //public static function
    /**
     * get instance of Business_Ws_Worldcupvotesexcplayer
     *
     * @return Business_Ws_Worldcupvotesexcplayer
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
    public function getDetail($id) {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " WHERE userid = " . $id;
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
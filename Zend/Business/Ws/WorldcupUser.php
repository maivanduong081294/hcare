<?php

class Business_Ws_WorldcupUser extends Business_Abstract {

    private $_tablename = 'users';
    private static $_instance = null;

    function __construct() {
        
    }

    //public static function
    /**
     * get instance of Business_Ws_WorldcupUser
     *
     * @return Business_Ws_WorldcupUser
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
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " WHERE id='" . parent::adaptSQL($id) . "'";
        $result = $db->fetchAll($query);
        return $result[0];
    }
    
    public function getDetailByEmail($email) {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " WHERE email='" . parent::adaptSQL($email) . "'";
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
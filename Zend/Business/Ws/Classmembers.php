<?php

class Business_Ws_Classmembers extends Business_Abstract {

    private $_tablename = 'class_members';
    private static $_instance = null;

    function __construct() {
        
    }

    //public static function
    /**
     * get instance of Business_Ws_Classmembers
     *
     * @return Business_Ws_Classmembers
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
        $db = Globals::getDbConnection('course');
        return $db;
    }

    private function getCacheInstance() {
        $cache = GlobalCache::getCacheInstance('ws');
        return $cache;
    }

    public function getListByCourse($classid) {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " WHERE classid=? ORDER BY datetime desc";
        $data = array($classid);
        $result = $db->fetchAll($query, $data);
        return $result;
    }

    public function getDetail($memberid) {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " WHERE id='" . parent::adaptSQL($memberid) . "'";
        $result = $db->fetchAll($query);
        return $result[0];
    }
    
    public function getDetailByClassAndEmail($id, $email) {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " WHERE id='" . parent::adaptSQL($id) . "' AND email = '".parent::adaptSQL($email)."'";
        $result = $db->fetchAll($query);
        return $result[0];
    }

    public function insert($data) {
        $db = $this->getDbConnection();
        $result = $db->insert($this->_tablename, $data);
        return $db->lastInsertId();
    }

}

?>
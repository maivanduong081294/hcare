<?php

class Business_Addon_Samsung extends Business_Abstract {

    private $_tablename = 'addon_samsung';

    const KEY_LIST = 'up.ss.%s';   //key of list.questionid
    const KEY_DETAIL = 'up.ss.%s';  //key of detail.id

    private static $_instance = null;

    function __construct() {
        
    }

    //public static function
    /**
     * get instance of Business_Addon_Samsung
     *
     * @return Business_Addon_Samsung
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
        $db = Globals::getDbConnection('maindb');
        return $db;
    }

    /**
     * Enter description here...
     *
     * @return Maro_Cache
     */
    private function getCacheInstance() {
        $cache = GlobalCache::getCacheInstance('default');
        return $cache;
    }

    public function getTotalByItemid($itemid) {
        $db = $this->getDbConnection();
        $query = "SELECT count(*) as total FROM " . $this->_tablename . " WHERE itemid=?";
        $data = array($itemid);
        $result = $db->fetchAll($query, $data);
        return $result[0]["total"];
    }
    
    public function getTotal() {
        $db = $this->getDbConnection();
        $query = "SELECT count(*) as total FROM " . $this->_tablename;
        $data = array();
        $result = $db->fetchAll($query, $data);
        return $result[0]["total"];
    }

    public function insert($data) {
        $db = $this->getDbConnection();
        $result = $db->insert($this->_tablename, $data);
        return $result;
    }
}

?>

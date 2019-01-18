<?php

class Business_Addon_Usedphonehistorysell extends Business_Abstract {

    private $_tablename = 'addon_usedphone_history_sell';

    const KEY_LIST = 'uphs.list.%s';   //key of list.questionid
    const KEY_DETAIL = 'uphs.detail.%s';  //key of detail.id

    private static $_instance = null;

    function __construct() {
        
    }

    //public static function
    /**
     * get instance of Business_Addon_Usedphonehistorysell
     *
     * @return Business_Addon_Usedphonehistorysell
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
        $cache = GlobalCache::getCacheInstance('ws');
        return $cache;
    }

    public function getListByItemid($itemid) {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " WHERE itemid=? ORDER datetime DESC";
        $data = array($itemid);
        $result = $db->fetchAll($query, $data);
        return $result;
    }

    public function insert($data) {
        $db = $this->getDbConnection();
        $result = $db->insert($this->_tablename, $data);
        if ($result > 0) {
            return $db->lastInsertId();
        }
        return 0;
    }


}

?>

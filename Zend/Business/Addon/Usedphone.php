<?php

class Business_Addon_Usedphone extends Business_Abstract {

    private $_tablename = 'addon_usedphone';

    const KEY_LIST = 'up.list.%s';   //key of list.questionid
    const KEY_DETAIL = 'up.detail.%s';  //key of detail.id

    private static $_instance = null;

    function __construct() {
        
    }

    //public static function
    /**
     * get instance of Business_Addon_Usedphone
     *
     * @return Business_Addon_Usedphone
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

    public function getListByIMEI($imei) {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " WHERE imei=?";
        $data = array($imei);
        $query .=" order by id desc";
        $result = $db->fetchAll($query, $data);
        return $result;
    }
    public function get_list_test($userid,$month,$year) {
        $db = $this->getDbConnection();
        $query = "SELECT userid_check,count(*) as total FROM " . $this->_tablename . " WHERE userid_check=$userid and MONTH(datetime)=$month and YEAR(datetime)=$year and active_target=1";
        $query .=" order by id desc";
        $result = $db->fetchAll($query, $data);
        return $result;
    }
    public function get_list_by_auid() {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " WHERE auid >0";
        $query .=" order by id desc";
        $result = $db->fetchAll($query);
        return $result;
    }
    
    public function getDetail($id) {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " WHERE id=?";
        $data = array($id);
        $result = $db->fetchAll($query, $data);
        return $result[0];
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

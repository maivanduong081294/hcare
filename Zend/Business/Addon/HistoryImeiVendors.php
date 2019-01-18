<?php

class Business_Addon_HistoryImeiVendors extends Business_Abstract {

    private $_tablename = 'history_imei_vendors';
    private static $_instance = null;

    function __construct() {
        
    }

    //public static function
    /**
     * get instance of Business_Addon_HistoryImeiVendors
     *
     * @return Business_Addon_HistoryImeiVendors
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
        $cache = GlobalCache::getCacheInstance('event');
        return $cache;
    }

    public function insert($data) {
        $db = $this->getDbConnection();
        $result = $db->insert($this->_tablename, $data);
        return $result; 
    }
     
    public function getDetail($id){
        $db= $this->getDbConnection();
        $query = "select * from $this->_tablename where id = $id";
        $result = $db->fetchAll($query);
        return $result[0];
    }
    public function get_detail_by_imei($imei){
        $db= $this->getDbConnection();
        $query = "select * from $this->_tablename where imei_old = '$imei'";
        $result = $db->fetchAll($query);
        
        return $result[0];
    }
    public function get_list_by_imei($imei){
        $db= $this->getDbConnection();
        $query = "select * from $this->_tablename where imei_new like '%$imei%' or imei_old like '%$imei%' and enabled = 1";
        $result = $db->fetchAll($query);
        return $result;
    }

    public function update($id,$data) {
        $db= $this->getDbConnection();
        $query = "id = ".$id;
        $result = $db->update($this->_tablename, $data,$query);
        return $result;
    }

}

?>

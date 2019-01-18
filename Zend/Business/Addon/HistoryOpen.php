<?php

class Business_Addon_HistoryOpen extends Business_Abstract {

    private $_tablename = 'history_chargecard_installment';
    private static $_instance = null;

    function __construct() {
        
    }

    //public static function
    /**
     * get instance of Business_Addon_HistoryOpen
     *
     * @return Business_Addon_HistoryOpen
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
    
    
    public function getDetail($id) {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " WHERE id=?";
        $data = array($id);
        $result = $db->fetchAll($query, $data);
        return $result;
    }
    public function get_detail_by_date($storeid,$type,$date,$id_addon_user=0) {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " WHERE storeid= $storeid and type = $type and DATE(datetime) = '$date' and enabled = 1";
        if((int)$id_addon_user > 0){
            $query .=" and id_addon_user = $id_addon_user ";
        }
        $result = $db->fetchAll($query);
        return $result[0];
    }
    
    public function insert($data) {
        $db = $this->getDbConnection();
        $result = $db->insert($this->_tablename, $data);
        return $result;
    }
    public function update($id,$data) {
        $db= $this->getDbConnection();
        $query = "id = ".$id;
        $result = $db->update($this->_tablename, $data,$query);
        return $result;
    }
    
    private function _deleteAllCache()
    {
            $cache = $this->getCacheInstance('ws');
            $cache->flushAll();
    }
    private function _deleteCacheByKey($key)
    {
        
        $key_name = "getlist".$key;
            $cache = $this->getCacheInstance('ws');
            $data = $cache->getCache($key_list);
//            $cache->flushAll();
            $cache->deleteCache($key_name);
    }

}

?>

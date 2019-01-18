<?php

class Business_Addon_Baohanh extends Business_Abstract {

    private $_tablename = 'ws_baohanh';
    private static $_instance = null;

    function __construct() {
        
    }

    //public static function
    /**
     * get instance of Business_Addon_Baohanh
     *
     * @return Business_Addon_Baohanh
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
        if ($result > 0) {
            $lastid= $db->lastInsertId($this->_tablename); // tra ve id khi them vao
        }
        return $lastid;
    }
    
    public function excute($query){
        $db     =  $this->getDbConnection();
        $result = $db->query($query);
        return $result;
    }
    public function update($id,$data) {
        $db= $this->getDbConnection();
        $query = "id = ".$id;
        $result = $db->update($this->_tablename, $data,$query);
        return $result;
    }
    public function get_key_by_itemid($itemid){
        $str = md5("$itemid");
        $key ="get_list_by_itemid.".  $this->_tablename.$str;
        return $key;
    }
    public function delete_key_by_itemid($itemid){
        $cache = $this->getCacheInstance();
        $key = $this->get_key_by_itemid($itemid);
        $cache->deleteCache($key);
    }

    public function get_list_by_itemid($strId){
        $db = $this->getDbConnection();
        $cache = $this->getCacheInstance();
        $key = $this->get_key_by_itemid($strId);
        $result = $cache->getCache($key);
        if($result ===FALSE){
            $query = "select * from $this->_tablename where itemid IN ($strId) "; 
            $result = $db->fetchAll($query);
            if(!is_null($result) && is_array($result)){
                $cache->setCache($key, $result,600);
            }
        }
        return $result;
    }
}

?>

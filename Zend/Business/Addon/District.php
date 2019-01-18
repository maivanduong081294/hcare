<?php

class Business_Addon_District extends Business_Abstract {

    private $_tablename = 'district_viettel';
    private static $_instance = null;

    function __construct() {
        
    }

    //public static function
    /**
     * get instance of Business_Addon_District
     *
     * @return Business_Addon_District
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
    public function get_list($id=""){
        $db = $this->getDbConnection();
        $cache = $this->getCacheInstance();
        $key ="get_list.".  $this->_tablename.$id;
        $result = $cache->getCache($key);
        if($result ===FALSE){
            $query = "select * from $this->_tablename where 1 = 1 "; 
            if($id != NULL){
                $query.=" and id = '$id'";
            }
            $result = $db->fetchAll($query);
        }
        return $result;
    }
    public function get_list_by_provinceid($province_id=""){
        $db = $this->getDbConnection();
        $cache = $this->getCacheInstance();
        $key ="get_list_by_provinceid.".  $this->_tablename.$province_id;
        $result = $cache->getCache($key);
        if($result ===FALSE){
            $query = "select * from $this->_tablename where province_id = '$province_id' "; 
            $result = $db->fetchAll($query);
        }
        return $result;
    }
}

?>

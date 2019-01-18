<?php

class Business_Addon_Document extends Business_Abstract {

    private $_tablename = 'document';

    const KEY_LIST = 'document.list.%s';   //key of list.questionid
    const KEY_DETAIL = 'document.detail.%s';  //key of detail.id
    const KEY_DETAIL_KM = 'km.%s';  //key of detail.id
    const KEY_DETAIL_LIST_KM = 'getlist_document.%s';  //key of list.id

    private static $_instance = null;

    function __construct() {
        
    }

    //public static function
    /**
     * get instance of Business_Addon_Document
     *
     * @return Business_Addon_Document
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
    
    private function getKeyKM($itemid){
        return sprintf(self::KEY_DETAIL_KM, $itemid);
    }
    private function getListKeyKM($keywork){
        return sprintf(self::KEY_DETAIL_LIST_KM,$keywork);
    }
    public function getList($id=0){
        $cache = $this->getCacheInstance();
        $key = "getlist".  $this->_tablename.$id;
        $result = $cache->getCache($key);
        if($result === FALSE){
            $db = $this->getDbConnection();
            $query = "SELECT * FROM " . $this->_tablename . " WHERE enabled=1 ";
            if((int)$id >0){
                $query .=" and id = $id";
            }
            $query .=" order by datetime Desc";
//        var_dump($query);exit();
            $result = $db->fetchAll($query); 
            if(!is_null($query) && is_array($result)){
                $cache->setCache($key, $result);
            }
        }
        return $result;
    }

    public function getListByProductsId($products_id){
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " WHERE status = 1 and products_id = $products_id and DATEDIFF(NOW(),startdate) >= 0 and DATEDIFF(NOW(),enddate) <= 0";
        $result = $db->fetchAll($query);
        return $result;
    }
    public function getListActived(){
        $cache = $this->getCacheInstance();
        $key = "getListActived".  $this->_tablename;
        $result = $cache->getCache($key);
        if($result === FALSE){
            $db = $this->getDbConnection();
            $query = "SELECT * FROM " . $this->_tablename . " WHERE status = 1 and enabled=1 and  DATEDIFF(NOW(),startdate) >= 0 and DATEDIFF(NOW(),enddate) <= 0";
            $query .=" order by datetime desc";
            $result = $db->fetchAll($query); 
                if(!is_null($query) && is_array($result)){
                    $cache->setCache($key, $result);
                }
        }
        
        return $result;
    }
    public function getListActived2($regency = ''){
        $cache = $this->getCacheInstance();
        $key = "getListActived3".  $this->_tablename;
        $result = $cache->getCache($key);
        $result = false;
        if($result === FALSE){
            $db = $this->getDbConnection();
            if($regency) {
                $where = " AND FIND_IN_SET($regency,regency)";
            }
            $query = "SELECT id,des_sort FROM " . $this->_tablename . " WHERE status = 1 $where and enabled=1 and  DATEDIFF(NOW(),startdate) >= 0 and DATEDIFF(NOW(),enddate) <= 0";
            $query .=" order by datetime desc limit 3";
            $result = $db->fetchAll($query); 
                if(!is_null($query) && is_array($result)){
                    $cache->setCache($key, $result);
                }
        }
        
        return $result;
    }
    public function getDetailById($id){
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " WHERE enabled = 1 and id = $id";
        $result = $db->fetchAll($query);
        return $result[0];
    }
    public function getDetailById2($id){
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " WHERE status = 1 and id = $id ";
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
        $this->deleteKeyKM($id);
        $this->deleteListKeyKM();
//            var_dump($result);exit();
        return $result;
    }
    public function excute($query){
            $db     =  $this->getDbConnection();
            $result = $db->query($query);
            return $result;
        }
        
    private function deleteKeyKM($itemid) {
        $cache = $this->getCacheInstance();
        $key = $this->getKeyKM($itemid);
        $cache->deleteCache($key);
    }
    private function deleteListKeyKM($keyword="") {
        $cache = $this->getCacheInstance();
        $key = $this->getListKeyKM($keyword);
        $cache->deleteCache($key);
    }
}

?>

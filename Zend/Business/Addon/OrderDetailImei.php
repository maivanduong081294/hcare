<?php

class Business_Addon_OrderDetailImei extends Business_Abstract {

    private $_tablename = 'hnam_order_detail_imei';
    private static $_instance = null;

    function __construct() {
        
    }

    //public static function
    /**
     * get instance of Business_Addon_OrderDetailImei
     *
     * @return Business_Addon_OrderDetailImei
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
    public function get_list_by_imei($imei){
        $cache = $this->getCacheInstance();
        $key = "get_list_by_imei".  $this->_tablename.$imei;
        $result = $cache->getCache($key);
        if($result ===FALSE){
            $db= $this->getDbConnection();
            $query = "select * from $this->_tablename where imei = '$imei' and enabled = 1";
            $result = $db->fetchAll($query);
            if(!is_null($result) && is_array($result)){
                $cache->setCache($key, $result,600);
            }
        }
        
        return $result;
    }

    public function getListByDetailOrderId($detail_orderid){
        $cache = $this->getCacheInstance();
        $key = "getListByDetailOrderId".  $this->_tablename.$detail_orderid;
        $result = $cache->getCache($key);
        $result = FALSE;
        if($result ===FALSE){
            $db= $this->getDbConnection();
            $query = "select * from $this->_tablename where detail_orderid = $detail_orderid and enabled = 1";
            $result = $db->fetchAll($query);
            if(!is_null($result) && is_array($result)){
                $cache->setCache($key, $result,600);
            }
        }
        
        return $result;
    }

    public function getDetail($id){
        $db= $this->getDbConnection();
        $query = "select * from $this->_tablename where id = $id";
        $result = $db->fetchAll($query);
        return $result[0];
    }
    public function getList($userid=0,$enabled=""){
        $db= $this->getDbConnection();
        $query = "select * from $this->_tablename where 1=1 ";
        if($userid >0){
            $query .=" and userid = $userid";
        }
        if($enabled != null){
            $query .=" and enabled = $enabled";
        }
        
        $result = $db->fetchAll($query);
        return $result;
    }
    public function excute($query){
            $db     =  $this->getDbConnection();
            $result = $db->query($query);
            return $result;
        }
    public function delete($itemid) {
        $current = $this->getDetail($itemid);
        $db = $this->getDbConnection();
        $where = array();
        $where[] = "item_id='" . parent::adaptSQL($itemid) . "'";
        $result = $db->delete($this->_tablename, $where);
        if ($result > 0) {
            $this->_deleteAllCache($itemid, $current['poll_id']);
        }
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

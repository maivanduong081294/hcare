<?php

class Business_Addon_Order extends Business_Abstract {

    private $_tablename = 'hnam_order';
    private static $_instance = null;

    function __construct() {
        
    }

    //public static function
    /**
     * get instance of Business_Addon_Order
     *
     * @return Business_Addon_Order
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
    public function getListByDay($startday,$endday,$supplier_id,$enable=""){
        $db= $this->getDbConnection();
        $query = "SELECT *  FROM $this->_tablename WHERE datetime >= '$startday' and datetime <= '$endday'";
        if(intval($supplier_id)>0){
            $query .=" and supplier_id = $supplier_id";
        }
        if($enable != null){
            $query.=" and enabled = ".  intval($enable);
        }
        $result = $db->fetchAll($query);
        return $result;
    }
    public function getListByDayUserId($userid=0,$startday,$endday,$supplier_id,$enable=""){
        $db= $this->getDbConnection();
        $query = "SELECT *  FROM $this->_tablename WHERE datetime >= '$startday' and datetime <= '$endday'";
        if(intval($userid)>0){
            $query .=" and userid = $userid";
        }
        if(intval($supplier_id)>0){
            $query .=" and supplier_id = $supplier_id";
        }
        if($enable != null){
            $query.=" and enabled = ".  intval($enable);
        }
        $query .=" order by id desc";
        $result = $db->fetchAll($query);
        return $result;
    }

    public function getDetail($id){
        $db= $this->getDbConnection();
        $query = "select * from $this->_tablename where id = $id";
        
        $result = $db->fetchAll($query);
        return $result[0];
    }
    
    public function get_list_by_id($strid){
        $db= $this->getDbConnection();
        $query = "select * from $this->_tablename where id IN ($strid)";
        
        $result = $db->fetchAll($query);
        return $result;
    }
    public function getList($userid=0,$enabled="",$idvendor=0){
        $db= $this->getDbConnection();
        $query = "select * from $this->_tablename where 1=1 ";
        if($userid >0){
            $query .=" and userid = $userid";
        }
        if($enabled != null){
            $query .=" and enabled = $enabled";
        }
        if(intval($idvendor)>0){
            $query .=" and supplier_id = $idvendor";
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

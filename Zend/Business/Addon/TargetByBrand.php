<?php

class Business_Addon_TargetByBrand extends Business_Abstract {

    private $_tablename = 'hnam_target_bybrand';
    private static $_instance = null;

    function __construct() {
        
    }

    //public static function
    /**
     * get instance of Business_Addon_TargetByBrand
     *
     * @return Business_Addon_TargetByBrand
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
     public function get_detail($id){
        $db= $this->getDbConnection();
        $query = "select * from $this->_tablename where id = $id";
        $result = $db->fetchAll($query);
        return $result[0];
    }
     public function get_detail_by_target($storeid,$month,$year,$cated_id){
        $db= $this->getDbConnection();
        $query = "select * from $this->_tablename where enabled =1 and storeid = $storeid and month = $month and year = $year and cated_id = $cated_id";
        $result = $db->fetchAll($query);
        return $result[0];
    }
     public function get_list_by_target($storeid,$month,$year,$cated_id){
        $db= $this->getDbConnection();
        $query = "select * from $this->_tablename where enabled =1";
        if((int)$storeid >0){
            $query .=" and storeid = $storeid";
        }
        if((int)$month >0){
            $query .=" and month = $month";
        }
        if((int)$year >0){
            $query .=" and year = $year";
        }
        if((int)$cated_id >0){
            $query .=" and cated_id = $cated_id";
        }
        $result = $db->fetchAll($query);
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

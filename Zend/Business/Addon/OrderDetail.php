<?php

class Business_Addon_OrderDetail extends Business_Abstract {

    private $_tablename = 'hnam_order_detail';
    private static $_instance = null;

    function __construct() {
        
    }

    //public static function
    /**
     * get instance of Business_Addon_OrderDetail
     *
     * @return Business_Addon_OrderDetail
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
    public function get_list_by_strid($strid){
        $db= $this->getDbConnection();
        $query = "select * from $this->_tablename where id IN ($strid)";
        $result = $db->fetchAll($query);
        return $result;
    }
    public function getDetail($id){
        $db= $this->getDbConnection();
        $query = "select * from $this->_tablename where id = $id";
        $result = $db->fetchAll($query);
        return $result[0];
    }
    public function getListByOrderId($orderid){
        $db= $this->getDbConnection();
        $query = "select * from $this->_tablename where enabled = 1 and orderid IN ($orderid)";
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

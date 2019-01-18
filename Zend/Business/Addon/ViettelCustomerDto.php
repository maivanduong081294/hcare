<?php

class Business_Addon_ViettelCustomerDto extends Business_Abstract {

    private $_tablename = 'viettel_customer_dto';
    private static $_instance = null;

    function __construct() {
        
    }

    //public static function
    /**
     * get instance of Business_Addon_ViettelCustomerDto
     *
     * @return Business_Addon_ViettelCustomerDto
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
    
    public function get_detail_by_id_addon_user($id_addon_user){
        $db = $this->getDbConnection();
        $query = "select * from " . $this->_tablename . " where id_addon_user = $id_addon_user and  enabled = 1";
        $result = $db->fetchAll($query); 
        $result = $result[0];
        return $result;
    }
    public function getDetail($id){
        $id = (int)$id;
        $db = $this->getDbConnection();
        $query = "select * from $this->_tablename where enabled = 1 and ids = $id";
        $result = $db->fetchAll($query);
        return $result[0];
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
        $query = "ids = ".$id;
        $result = $db->update($this->_tablename, $data,$query);
        return $result;
    }

}

?>

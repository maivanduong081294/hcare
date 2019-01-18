<?php

class Business_Addon_Totaltarget extends Business_Abstract {

    private $_tablename = 'total_target';
    private static $_instance = null;

    function __construct() {
        
    }

    //public static function
    /**
     * get instance of Business_Addon_Totaltarget
     *
     * @return Business_Addon_Totaltarget
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
    public function update($id,$data){
            $db=  $this->getDbConnection();
            $query = "id = ".$id;
            $result = $db->update($this->_tablename, $data, $query);
            return $result;
        }
    
    public function get_detail($id){
        $db = $this->getDbConnection();
        $query = "select * from $this->_tablename where enabled = 1 and id = $id "; 
        $result = $db->fetchAll($query);
        $result = $result[0];
        return $result;
    }
    public function get_detail_by_userid_month_year($userid,$month,$year){
        $db = $this->getDbConnection();
        $query = "select * from $this->_tablename where enabled = 1 and userid = $userid and month = $month and year = $year "; 
        $result = $db->fetchAll($query);
        $result = $result[0];
        return $result;
    }
    public function get_list_by_userid_month_year($storeid,$month,$year){
        $db = $this->getDbConnection();
        $query = "select * from $this->_tablename where enabled = 1 and month = $month and year = $year "; 
        if($storeid >0){
            $query .="  and storeid = $storeid";
        }
        $result = $db->fetchAll($query);
        return $result;
    }
    
}

?>

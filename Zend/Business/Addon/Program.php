<?php

class Business_Addon_Program extends Business_Abstract {

    private $_tablename = 'hnam_program';
    private static $_instance = null;

    function __construct() {
        
    }

    //public static function
    /**
     * get instance of Business_Addon_Program
     *
     * @return Business_Addon_Program
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
     public function getDetailByPC($number_pc,$month,$year){
        $db= $this->getDbConnection();
        $query = "select * from $this->_tablename where enabled =1 and number_pc = $number_pc and MONTH(bill_datetime) = $month and YEAR(bill_datetime) = $year";
        $result = $db->fetchAll($query);
        return $result[0];
    }
    public function getDetail($id){
        $db= $this->getDbConnection();
        $query = "select * from $this->_tablename where id = $id";
        $result = $db->fetchAll($query);
        return $result[0];
    }
    public function getKMPK(){
        $db= $this->getDbConnection();
        $query = "select * from $this->_tablename where kmpk = 1";
        $result = $db->fetchAll($query);
        return $result;
    }
    public function get_list_by_date($date){
        $db= $this->getDbConnection();
        $query = "select * from $this->_tablename where enabled = 1 ";
        if($date){
            $query .=" and start <='$date' and end >='$date'";
        }
        $result = $db->fetchAll($query);
        return $result;
    }
    public function get_list_by_date2($start,$end){
        $db= $this->getDbConnection();
        $query = "select * from $this->_tablename where enabled = 1 and kmpk = 0";
        $query .=" and start <='$start' and end >='$end'";
        $result = $db->fetchAll($query);
        return $result;
    }
    public function get_kmpk_by_date2($start,$end){
        $db= $this->getDbConnection();
        $query = "select * from $this->_tablename where enabled = 1 and kmpk = 1";
        $query .=" and start <='$start' and end >='$end'";
        $result = $db->fetchAll($query);
        if($result) {
            return $result[0];
        }
        return $result;
    }
    public function get_list_by_id($str_id){
        $cache = $this->getCacheInstance();
        $key = $this->_tablename."get_list_by_id".$str_id;
        $result = $cache->getCache($key);
//        $result=false;
        if ($result === FALSE) {
            $db= $this->getDbConnection();
            $query = "select * from $this->_tablename where enabled = 1 ";
            if($str_id){
                $query .=" and id IN ($str_id)";
            }
            $result = $db->fetchAll($query);
            if(!is_null($result) && is_array($result)){
                $cache->setCache($key, $result,800);
            }
        }
        
        return $result;
    }
    public function getList(){
        $db= $this->getDbConnection();
        $query = "select * from $this->_tablename where enabled = 1";
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

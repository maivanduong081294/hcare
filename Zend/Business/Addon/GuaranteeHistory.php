<?php

class Business_Addon_GuaranteeHistory extends Business_Abstract {

    private $_tablename = 'hnam_guarantee_history2';
    private static $_instance = null;

    function __construct() {
        
    }

    //public static function
    /**
     * get instance of Business_Addon_GuaranteeHistory
     *
     * @return Business_Addon_GuaranteeHistory
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
    public function get_list_by_idguaranree($id_reguarantee){
        $db = $this->getDbConnection();
        $query ="select * from $this->_tablename where enabled = 1 and  id_guarantee = $id_reguarantee";
        $result = $db->fetchAll($query);
        return $result;
    }
    public function get_list_by_type($start="",$end="",$type=0){
        $db = $this->getDbConnection();
        $query ="select * from $this->_tablename where enabled = 1 ";
        if($start != NULL){
            $query.=" and datetime >= '$start' and datetime <= '$end'";
        }
        if((int)$type >0){
            $query.=" and type = $type";
        }
        $result = $db->fetchAll($query);
        return $result;
    }
    public function get_list_by_date_out($start,$end,$storeid=0){
        $db = $this->getDbConnection();
        $query = "select * from $this->_tablename where enabled = 1 and  date_out > '$start' and date_out <='$end' ";
        if((int)$storeid >0){
            $query .=" and storeid IN ($storeid) ";
        }
        $result = $db->fetchAll($query);
        return $result;
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

}

?>

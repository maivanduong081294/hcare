<?php

class Business_Addon_TransferGuarantee extends Business_Abstract {

    private $_tablename = 'hnam_guarantee_transfer';
    private static $_instance = null;

    function __construct() {
        
    }

    //public static function
    /**
     * get instance of Business_Addon_TransferGuarantee
     *
     * @return Business_Addon_TransferGuarantee
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
    public function get_detail_by_idguarantee($idguarantee){
        $db = $this->getDbConnection();
        $query ="select * from $this->_tablename where enabled = 1 and idguarantee = $idguarantee";
        
        $result = $db->fetchAll($query);
        return $result[0];
    }
    public function get_list_by_idguarantee($idguarantee){
        $db = $this->getDbConnection();
        $query ="select * from $this->_tablename where enabled = 1 and idguarantee IN ($idguarantee)";
        $result = $db->fetchAll($query);
        return $result;
    }
    public function get_list_by_idguarantee2($idguarantee){
        $db = $this->getDbConnection();
        $query ="select * from $this->_tablename where enabled = 1 and idguarantee IN ($idguarantee) and status=5 ";
        $result = $db->fetchAll($query);
        return $result;
    }
    public function get_list_by_storeid_receiverid($storeid=0,$receiverid=0){
        $db = $this->getDbConnection();
        $query ="select * from $this->_tablename where enabled = 1 ";
        if((int)$storeid >0){
            $query .=" and storeid = $storeid";
        }
        if((int)$receiverid >0){
            $query .=" and receiverid = $receiverid";
        }
        $result = $db->fetchAll($query);
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

<?php

class Business_Addon_GuaranteeWarrantyPk extends Business_Abstract {

    private $_tablename = 'hnam_guarantee_warranty_pk';
    private static $_instance = null;

    function __construct() {
        
    }

    //public static function
    /**
     * get instance of Business_Addon_GuaranteeWarrantyPk
     *
     * @return Business_Addon_GuaranteeWarrantyPk
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
    
    public function getListByUserId($receiver_id=0,$start,$end,$storeid=0){
        $db = $this->getDbConnection();
        $query = "select * from $this->_tablename where 1=1";
        if((int)$receiver_id >0){
            $query .= " and receiver_id = $receiver_id";
        }
        if((int)$storeid >0){
            $query .= " and storeid = $storeid";
        }
        $query.=" and datetime >= '$start' and datetime <= '$end'";
        $query .= " order by id desc";
        $result = $db->fetchAll($query);
        return $result;
    }
    public function getDetail($id){
        $db = $this->getDbConnection();
        $query = "select * from $this->_tablename where id = $id";
        $result = $db->fetchAll($query);
        return $result[0];
    }
    public function getListByGuarantee($id_guarantee){
        $cache = $this->getCacheInstance();
        $str = md5($id_guarantee);
        $key = "getListByGuarantee".$this->_tablename.$str;
        $result = $cache->getCache($key);
        if($result ===FALSE){
            $db = $this->getDbConnection();
            $query = "select * from $this->_tablename where id_guarantee IN ($id_guarantee)";
            $result = $db->fetchAll($query);
            if(!is_null($result) && is_array($result)){
                $cache->setCache($key, $result,900);
            }
        }
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

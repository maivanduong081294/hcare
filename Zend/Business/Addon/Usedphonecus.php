<?php

class Business_Addon_Usedphonecus extends Business_Abstract {

    private $_tablename = 'addon_usedphone_customer';

    const KEY_LIST = 'upc.list.%s';   //key of list.questionid
    const KEY_DETAIL = 'upc.detail.%s';  //key of detail.id

    private static $_instance = null;

    function __construct() {
        
    }

    //public static function
    /**
     * get instance of Business_Addon_Usedphonecus
     *
     * @return Business_Addon_Usedphonecus
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
        $cache = GlobalCache::getCacheInstance('default');
        return $cache;
    }
    public function get_list_by_imei($imei){
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " WHERE imei like '%$imei%'";
        $result = $db->fetchAll($query);
        return $result;
    }
    public function get_list_by_usedphone_id($strid){
        $db = $this->getDbConnection();
        $query = "SELECT * FROM addon_usedphone_info WHERE addon_usedphone_id  IN ($strid)";
        $result = $db->fetchAll($query);
        return $result;
    }

    public function getDetailByOption($id="",$addon_usedphone_id="",$active="",$datetime_expired=""){
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " WHERE 1=1";
        if($id != null){
            $query .=" and id = '$id' ";
        }
        if($addon_usedphone_id != null){
            $query .=" and addon_usedphone_id = '$addon_usedphone_id' ";
        }
        if($active != null){
            $query .=" and active = $active ";
        }
        if($datetime_expired != null){
            $query .=" and datetime_expired < $datetime_expired ";
        }
        $result = $db->fetchAll($query);
        return $result[0];
    }

    public function getDetailByImei($imei) {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " WHERE imei=?";
        $data = array($imei);
        $result = $db->fetchAll($query, $data);
        return $result[0];
    }
    
    public function getDetail($id) {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " WHERE id=?";
        $data = array($id);
        $result = $db->fetchAll($query, $data);
        return $result[0];
    }
    
    public function getList($storeid,$keyword,$vote_name) {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " WHERE 1 = 1 ";
        if($storeid != 'vote_all'){
            $query .= " and storeid = '$storeid' ";
        }
        if($keyword != null){
            $query .= " and id = '$keyword' or phone like '%$keyword%'";
        }
        if($vote_name != null){
            $query .= " and storeid = '$vote_name' ";
        }
        $query .= " order by id desc";
//        var_dump($query);exit();
        $result = $db->fetchAll($query);
        return $result;
    }

    public function insert($data) {
        $db = $this->getDbConnection();
        $result = $db->insert($this->_tablename, $data);
        if ($result > 0) {
            return $db->lastInsertId();
        }
        return 0;
    }
     public function delete($id,$active){
            $db=  $this->getDbConnection();
            $query = "id = $id";
            if($active == 1){
                $data =array(
                "active" => 0
                );
            }else{
                $data =array(
                "active" => 1
                );
            }
            
            $result = $db->update($this->_tablename, $data, $query);
//            var_dump($result);exit();
            return $result;
        }
        public function restore($id){
            $db=  $this->getDbConnection();
            $query = "id = ".$id;
            $data =array(
                "active" => 1
                );
            $result = $db->update($this->_tablename, $data, $query);
            return $result;
        }
        
        public function update($id,$data){
            $db=  $this->getDbConnection();
            $query = "id = ".$id;
            $result = $db->update($this->_tablename, $data, $query);
            return $result;
        }
}

?>

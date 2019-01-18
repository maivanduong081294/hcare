<?php

class Business_Addon_Customer extends Business_Abstract {

    private $_tablename = 'hnam_customer';
    private static $_instance = null;

    function __construct() {
        
    }

    //public static function
    /**
     * get instance of Business_Addon_Customer
     *
     * @return Business_Addon_Customer
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
     public function getDetailByPhone($phone){
        $db = $this->getDbConnection();
        $query = "select * from $this->_tablename where enabled = 1 and phone like '%$phone%' or fullname like '%$phone%' limit 200";
        $result = $db->fetchAll($query);
        return $result[0];
    }
    public function get_detail_by_phone($phone){
//            $cache = $this->getCacheInstance();
//            $strs = "get_detail_by_phone.$phone";
//            $key = $this->get_key_detail($strs);
//            $result=$cache->getCache($key);
//            if($result === FALSE)
//            {
                $db = $this->getDbConnection();
                $query = "select * from " . $this->_tablename . " where phone = '$phone'";
                $result = $db->fetchAll($query);
                $result = $result[0];
//                if(!is_null($result) && is_array($result)){
//                    $cache->setCache($key, $result);
//                }
//            }
            return $result;
        }
    public function get_detail_by_phone_actived($phone){
                $db = $this->getDbConnection();
                $query = "select * from " . $this->_tablename . " where phone = '$phone' and enabled = 1";
                $result = $db->fetchAll($query);
                $result = $result[0];
            return $result;
        }
     public function get_list_by_search($phone){
        $db = $this->getDbConnection();
        $query = "select * from $this->_tablename where enabled = 1 and phone = '$phone' or fullname like '%$phone%' limit 200";
        $result = $db->fetchAll($query);
        return $result;
    }
     public function get_list_by_search2($phone){
        $db = $this->getDbConnection();
        $query = "select * from $this->_tablename where enabled = 1 and phone = '$phone' limit 200";
        $result = $db->fetchAll($query);
        return $result;
    }
     public function search($kw){
        $db = $this->getDbConnection();
        $query = "select * from $this->_tablename where enabled = 1 and phone like '%$kw%' limit 200";
        $result = $db->fetchAll($query);
        return $result;
    }
    public function get_list_by_id($id){
        $db = $this->getDbConnection();
        $query = "select * from $this->_tablename where enabled = 1 and id IN ($id)";
        $result = $db->fetchAll($query);
        return $result;
    }
    public function getDetail($id){
        $id = (int)$id;
        $db = $this->getDbConnection();
        $query = "select * from $this->_tablename where enabled = 1 and id = $id";
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
    public function delete_cache($default='event'){
            $cache = $this->getCacheInstance($default);
            $cache->flushAll();
        }
    public function excute($query){
        $db     =  $this->getDbConnection();
        $result = $db->query($query);
        $this->delete_cache();
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

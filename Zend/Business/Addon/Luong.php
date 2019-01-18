<?php

class Business_Addon_Luong extends Business_Abstract {

    private $_tablename = 'hnam_luong';
    private static $_instance = null;

    function __construct() {
        
    }

    //public static function
    /**
     * get instance of Business_Addon_Luong
     *
     * @return Business_Addon_Luong
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
        $this->delete_key_list("", date('m'), 'Y');
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
        $this->delete_key_by_id($id);
        $detail = $this->get_detail($id);
        $month = date('m',  strtotime($detail["datetime"]));
        $year = date('Y',  strtotime($detail["datetime"]));
        $this->delete_key_list($detail["username"], $month, $year);
        $this->delete_key_list("", $month, $year);
        return $result;
    }
    public function get_list($username="",$month=0,$year=0){
        $db = $this->getDbConnection();
        $cache = $this->getCacheInstance();
        $key = $this->get_key_list($username, $month, $year);
        $result = $cache->getCache($key);
        if($result ===FALSE){
            $query = "select * from $this->_tablename where enabled = 1 "; 
            if($username != NULL){
                $query.=" and username = '$username'";
            }
            if((int)$month>0){
                $query.=" and MONTH(datetime)=$month";
            }
            if((int)$year>0){
                $query.=" and YEAR(datetime)=$year";
            }
           
            $query.=" order by id desc";
            $result = $db->fetchAll($query);
            if (!is_null($result) && is_array($result)) {
                $cache->setCache($key, $result);
            }
        }
        
        return $result;
    }
    public function get_key_detail($id){
        $key = "get_detail".$this->_tablename.$id;
        return $key;
    }
    public function delete_key_by_id($id){
        $cache = $this->getCacheInstance();
        $key = $this->get_key_detail($id);
        $cache->deleteCache($key);
    }
    public function get_key_list($username,$month,$year){
        $key ="get_list.".  $this->_tablename.$username.$month.$year;
        return $key;
    }
    public function delete_key_list($username,$month,$year){
        $cache = $this->getCacheInstance();
        $key = $this->get_key_list($username,$month,$year);
        $cache->deleteCache($key);
    }
    public function get_detail($id){
        $db = $this->getDbConnection();
        $cache = $this->getCacheInstance();
        $key = $this->get_key_detail($id);
        $result = $cache->getCache($key);
        if($result ===FALSE){
            $query = "select * from $this->_tablename where enabled = 1 and id = $id"; 
            $result = $db->fetchAll($query);
            $result = $result[0];
            if (!is_null($result) && is_array($result)){
                $cache->setCache($key, $result);
            }
        }
        return $result;
    }
}

?>

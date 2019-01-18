<?php

class Business_Addon_Bno extends Business_Abstract {

    private $_tablename = 'hnam_bno';
    private static $_instance = null;

    function __construct() {
        
    }

    //public static function
    /**
     * get instance of Business_Addon_Bno
     *
     * @return Business_Addon_Bno
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
    public function update($id,$data) {
        $db= $this->getDbConnection();
        $query = "id = ".$id;
        $result = $db->update($this->_tablename, $data,$query);
        return $result;
    }
    public function get_detail_by_id_addon_user($id_addon_user){
        $db = $this->getDbConnection();
        $cache = $this->getCacheInstance();
        $key ="get_detail_by_id_addon_user.".  $this->_tablename.$id_addon_user;
        $result = $cache->getCache($key);
        $result = FALSE;
        if($result ===FALSE){
            $query = "select * from $this->_tablename where enabled = 1 and id_addon_user = $id_addon_user "; 
            $query.=" order by id desc";
            $result = $db->fetchAll($query);
            $result = $result[0];
            if(!is_null($result) && is_array($result)){
                $cache->setCache($key, $result);
            }
        }
        
        return $result;
    }
    public function get_detail($id){
        $db = $this->getDbConnection();
        $cache = $this->getCacheInstance();
        $key ="get_detail.".  $this->_tablename.$id;
        $result = $cache->getCache($key);
        $result = FALSE;
        if($result ===FALSE){
            $query = "select * from $this->_tablename where enabled = 1 and id = $id "; 
            $result = $db->fetchAll($query);
            $result = $result[0];
            if(!is_null($result) && is_array($result)){
                $cache->setCache($key, $result);
            }
        }
        
        return $result;
    }
    public function get_list_by_id_addon_user($id_addon_user){
        $db = $this->getDbConnection();
        $cache = $this->getCacheInstance();
        $key ="get_list_by_id_addon_user.".  $this->_tablename.$id_addon_user;
        $result = $cache->getCache($key);
        $result = FALSE;
        if($result ===FALSE){
            $query = "select * from $this->_tablename where enabled = 1 and id_addon_user = $id_addon_user "; 
            $query.=" order by id desc";
            $result = $db->fetchAll($query);
            if(!is_null($result) && is_array($result)){
                $cache->setCache($key, $result);
            }
        }
        
        return $result;
    }
    public function get_count_by_id_addon_user($id_addon_user){
        $db = $this->getDbConnection();
        $cache = $this->getCacheInstance();
        $key ="get_count_by_id_addon_user.".  $this->_tablename.$id_addon_user;
        $result = $cache->getCache($key);
        $result = FALSE;
        if($result ===FALSE){
            $query = "select id_addon_user,sum(money) as sum,sum(money_confirm) as sum_money_confirm,note from $this->_tablename where enabled = 1 and id_addon_user IN ($id_addon_user) "; 
            $query.=" group by id_addon_user ";
            $result = $db->fetchAll($query);
            if(!is_null($result) && is_array($result)){
                $cache->setCache($key, $result);
            }
        }
        
        return $result;
    }
    public function get_list($username=""){
        $db = $this->getDbConnection();
        $cache = $this->getCacheInstance();
        $key ="get_list.".  $this->_tablename.$username;
        $result = $cache->getCache($key);
        $result = FALSE;
        if($result ===FALSE){
            $query = "select * from $this->_tablename where enabled = 1 "; 
            if($username != NULL){
                $query.=" and username = '$username'";
            }
            $query.=" order by id desc";
            $result = $db->fetchAll($query);
            if(!is_null($result) && is_array($result)){
                $cache->setCache($key, $result);
            }
        }
        
        return $result;
    }
}

?>

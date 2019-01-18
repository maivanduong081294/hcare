<?php

class Business_Addon_Chart extends Business_Abstract {

    private $_tablename = 'ws_chart_media';
    private static $_instance = null;

    function __construct() {
        
    }

    //public static function
    /**
     * get instance of Business_Addon_Chart
     *
     * @return Business_Addon_Chart
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
    public function excute($query){
            $db     =  $this->getDbConnection();
            $result = $db->query($query);
            return $result;
        }
    public function getDetail($id){
        $db= $this->getDbConnection();
        $query = "select * from $this->_tablename where id = $id";
        $result = $db->fetchAll($query);
        return $result[0];
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
    public function get_detail_by_date($date,$type=0){
        $cache = $this->getCacheInstance();
        $key = $this->_tablename."get_detail_by_dates".$date.$type;
        $result = $cache->getCache($key);
//        $result=false;
        if ($result === FALSE) {
            $db= $this->getDbConnection();
            $query = "select * from $this->_tablename where enabled = 1 ";
            if($date){
                $query .=" and DATE(created_date)='$date'";
            }
            if((int)$type>0){
                $query .=" and type=$type";
            }
            $result = $db->fetchAll($query);
            $result = $result[0];
            if(!is_null($result) && is_array($result)){
                $cache->setCache($key, $result,3600*2);
            }
        }
        return $result;
    }
    public function get_list_by_date_type($date,$type=0){
        $cache = $this->getCacheInstance();
        $key = $this->_tablename."get_list_by_date_type".$date.$type;
        $result = $cache->getCache($key);
        $result=false;
        if ($result === FALSE) {
            $db= $this->getDbConnection();
            $query = "select * from $this->_tablename where enabled = 1 ";
            if($date){
                $query .=" and DATE(created_date)='$date'";
            }
            if((int)$type>0){
                $query .=" and type IN ($type)";
            }
//            echo "<pre>";
//            var_dump($query);
//            die();
            $result = $db->fetchAll($query);
            if(!is_null($result) && is_array($result)){
                $cache->setCache($key, $result,3600);
            }
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
    public function get_list_by_userid($userid=0,$status=0){
        $db= $this->getDbConnection();
        $query = "select * from $this->_tablename where enabled = 1";
        if((int)$userid >0){
            $query .=" and (userid = $userid or receiver = $userid)";
        }
        if((int)$status >0){
            $query .=" and status = $status";
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

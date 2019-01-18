<?php

class Business_Addon_Ticket extends Business_Abstract {

    private $_tablename = 'hnam_ticket';
    private static $_instance = null;

    function __construct() {
        
    }

    //public static function
    /**
     * get instance of Business_Addon_Ticket
     *
     * @return Business_Addon_Ticket
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
    public function get_list_by_userid($userid=0,$status=0,$regencyid=0,$str_id,$start,$end){
        $db= $this->getDbConnection();
        $query = "select * from $this->_tablename where enabled = 1";
        if((int)$userid >0){
            $query .=" and (userid = $userid or receiver = $userid)";
        }
        if((int)$status >0){
            $query .=" and status = $status";
        }
        if((int)$regencyid >0){
            $query .=" and regencyid = $regencyid";
        }
        if($start != NULL){
            $query .=" and datetime >= '$start'";
        }
        if($end != NULL){
            $query .=" and datetime < '$end'";
        }
        
        $query .=" order by id desc";
        
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

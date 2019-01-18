<?php

class Business_Addon_ClosingSales extends Business_Abstract {

    private $_tablename = 'closing_sales';


    private static $_instance = null;

    function __construct() {
        
    }

    //public static function
    /**
     * get instance of Business_Addon_ClosingSales
     *
     * @return Business_Addon_ClosingSales
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
    public function getDetailByClose($userid="",$day_closing=""){
        $cache = $this->getCacheInstance();
        $key = "getDetailByClose".  $this->_tablename.$userid.$day_closing;
        $result = $cache->getCache($key);
        
        if($result ===FALSE){
            $db = $this->getDbConnection();
            $query = "select * from $this->_tablename where 1=1 ";
            if($userid != null){
                $query .=" and user_id = $userid ";
            }
            if($day_closing != null){
                $query .=" and DATE(day_closing) = '$day_closing'";
            }
            var_dump($query);exit();
            $result = $result[0];
            $result = $db->fetchAll($query);
            if(!is_null($result) && is_array($result)){
                $cache->setCache($key, $result,200);
            }
        }
        return $result;
    }
    public function getListByVoteDay3($userid,$day_closing){
        $db = $this->getDbConnection();
        $query = "select * from $this->_tablename where user_id = $userid and day_closing = '$day_closing' and enabled = 1";
        $result = $db->fetchAll($query);
        return $result;
    }
    public function getListByVoteDay($userid,$day_closing){
        $cache = $this->getCacheInstance();
        $key = "getListByVoteDay".  $this->_tablename.$userid.$day_closing;
        $result = $cache->getCache($key);
//        $cache->deleteCache($key);exit();
        $result = FALSE;
        if($result ===FALSE){
            $db = $this->getDbConnection();
            $query = "select * from $this->_tablename where user_id = $userid and day_closing = '$day_closing'";
            $result = $db->fetchAll($query);
            if(!is_null($result) && is_array($result)){
                $cache->setCache($key, $result);
            }
        }
        
        return $result;
    }
    public function get_key_detail($userid,$day_closing){
        $key = "getDetailByVoteDayss".  $this->_tablename.$userid.$day_closing;
        return $key;
    }
    public function delete_key_detail($id){
        if($id>0){
            $detail = $this->getDetail($id);
            if($detail){
                $key = $this->get_key_detail($detail["user_id"], $detail["day_closing"]);
                $cache = $this->getCacheInstance();
                $cache->deleteCache($key);
            }
        }
        
    }

    public function getDetailByVoteDay($userid="",$day_closing="",$enable=""){
        $cache = $this->getCacheInstance();
        $key = $this->get_key_detail($userid, $day_closing);
        $result = $cache->getCache($key);
        if($result ===FALSE){
            $db = $this->getDbConnection();
            $query = "select * from $this->_tablename where user_id = $userid ";
            if($day_closing != null){
                $query .=" and day_closing = '$day_closing'";
            }
            if($enable != null){
                $query .=" and enabled = $enable ";
            }
//            var_dump($query);exit();
            $result = $db->fetchAll($query);
            $result = $result[0];
            if(!is_null($result) && is_array($result)){
                $cache->setCache($key, $result);
            }
        }
        return $result;
    }
    public function getListByVoteDay2($userid,$day_closing,$mid_day){
        $db = $this->getDbConnection();
        $query = "select * from $this->_tablename where user_id = $userid and day_closing = '$day_closing' and mid_day = '$mid_day'";
//        var_dump($query);exit();
        $result = $db->fetchAll($query);
        return $result;
    }
    public function getDetailByUidByday($userid,$day_closing){
        $cache = $this->getCacheInstance();
        $key = "getDetailByUidByday".  $this->_tablename.$userid.$day_closing;
        $result = $cache->getCache($key);
//        $cache->deleteCache($key);exit();
        if($result === FALSE){
            $db = $this->getDbConnection();
            $query = "select * from $this->_tablename where user_id = $userid and day_closing = '$day_closing'";
//            var_dump($query);exit();
            $result = $db->fetchAll($query);
            $result = $result[0];
            $cache->setCache($key, $result);
        }
        return $result;
    }

    public function getLastClosingSale($userid){
        $db = $this->getDbConnection();
        $query = "select * from $this->_tablename where user_id = $userid";
        $result = $db->fetchRow($query);
        return $result;
    }

    public function getDetail($id) {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " WHERE id=?";
        $data = array($id);
        $result = $db->fetchAll($query, $data);
        return $result[0];
    }
    public function getListByProductIds($itemid)
	{
//            exit('231');
            $db = $this->getDbConnection();
            $query = "SELECT * FROM  $this->_tablename  where product_ids = $itemid";
//    			var_dump($query);exit();
            $result = $db->fetchAll($query);
            
//            var_dump(count($result));exit();
            return $result;		
	}
    public function insert($data) {
        $db = $this->getDbConnection();
        $result = $db->insert($this->_tablename,$data);
        if ($result > 0) {
            $lastid= $db->lastInsertId($this->_tablename); // tra ve id khi them vao
        }
        $this->delete_key_detail($lastid);
        return $lastid;
    }
    public function updates($id,$data) {
        $db = $this->getDbConnection();
        $where = array();
        $where[] = "id='" . parent::adaptSQL($id) . "'";
        try {
            $result = $db->update($this->_tablename, $data, $where);
            $this->delete_key_detail($id);
            return $result;
        } catch (Exception $e) {
            return false;
        }
    }
    public function update($id,$end_date) {
        $db = $this->getDbConnection();
        $query = "id = ".$id;
        $data = array(
            "time_closing" => $end_date,
            "enabled" => "1"
        );
        $result = $db->update($this->_tablename, $data,$query);
        $this->delete_key_detail($id);
        
        return $result;
    }
    public function _update($id,$end_date) {
        $db = $this->getDbConnection();
        $query = "id = ".$id;
        $data = array(
            "time_closing" => $end_date,
            "enabled" => "1"
        );
        $result = $db->update($this->_tablename, $data,$query);
        $this->delete_key_detail($id);
        return $result;
    }

    public function delete($id, $qid) {
        //get current menu
        $db = $this->getDbConnection();
        $where = array();
        $where[] = "itemid='" . parent::adaptSQL($id) . "'";
        $result = $db->delete($this->_tablename, $where);
    }

}

?>

<?php

class Business_Addon_Expenditure extends Business_Abstract {

    private $_tablename = 'ws_expenditure';
    private static $_instance = null;

    function __construct() {
        
    }

    //public static function
    /**
     * get instance of Business_Addon_Expenditure
     *
     * @return Business_Addon_Expenditure
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
        $cache = GlobalCache::getCacheInstance('app');
        return $cache;
    }
    
    public function getList($keywork="",$vote_id=0,$created_date,$end_date,$type=1){
        $cache = $this->getCacheInstance();
        $key = 'getList'.$this->_tablename.$keywork.$vote_id.$created_date.$end_date;
        $result = $cache->getCache($key);
//        var_dump($key,$result);exit();
//        $cache->deleteCache($key);
        $result = FALSE;
        if ($result === FALSE) {
            $db = $this->getDbConnection();
			if ($type>0) {
				$query = "SELECT * FROM  $this->_tablename where type=$type and enabled = 1 and created_date >= '$created_date' and created_date <= '$end_date' ";
			} else {
				$query = "SELECT * FROM  $this->_tablename where enabled = 1 and created_date >= '$created_date' and created_date <= '$end_date' ";
			}
            
            if($vote_id != 0){
                $query .= " and vote_id = $vote_id ";
            }
            if($keywork !=null){
                $query .=" and (title like '%$keywork%' or fullcontent = '$keywork')";
            }
            $query.=" ORDER BY id DESC";
            $query.= " LIMIT 100";
//            var_dump($query);exit();
            $result = $db->fetchAll($query);
            $cache->setCache($key, $result);
            
        }
        return $result;
    }
    

    public function getDetail($id) {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " WHERE enabled = 1 and  id=?";
        $data = array($id);
        $result = $db->fetchAll($query, $data);
        return $result[0];
    }
    
    public function getDetailByVoteId($vote_id,$day,$type=1) {
        $db = $this->getDbConnection();
		if ($type>0) {
			$query = "SELECT * FROM " . $this->_tablename . " WHERE type=$type AND vote_id= $vote_id and date(created_date) = '$day' and enabled = 1";
		} else {
			$query = "SELECT * FROM " . $this->_tablename . " WHERE vote_id= $vote_id and date(created_date) = '$day' and enabled = 1";
		}
        $result = $db->fetchAll($query);
        return $result[0];
    }
    public function getListByVoteId($vote_id,$day,$type=1) {
        $db = $this->getDbConnection();
		if ($type>0) {
			$query = "SELECT * FROM " . $this->_tablename . " WHERE type=$type AND vote_id= $vote_id and date(created_date) = '$day' and enabled = 1";
		} else {
			$query = "SELECT * FROM " . $this->_tablename . " WHERE vote_id= $vote_id and date(created_date) = '$day' and enabled = 1";
		}
        $result = $db->fetchAll($query);
        return $result;
    }
    public function getListByVoteId2($vote_id,$cday,$eday, $type=1) {
        $cache = GlobalCache::getCacheInstance('event');
        $key = "getListByVoteId2".  $this->_tablename.$vote_id.$cday.$eday;
        $result = $cache->getCache($key);
        $result = FALSE;
        if($result === FALSE){
            $db = $this->getDbConnection();
			if ($type>0) {
				$query = "SELECT * FROM " . $this->_tablename . " WHERE type=$type AND vote_id= $vote_id and created_date >= '$cday' and created_date <= '$eday' and enabled = 1";
			} else {
				$query = "SELECT * FROM " . $this->_tablename . " WHERE vote_id= $vote_id and created_date >= '$cday' and created_date <= '$eday' and enabled = 1";
			}
            $result = $db->fetchAll($query);
            if(!is_null($result) && is_array($result)){
                $cache->setCache($key, $result,60*5);
            }
        }
        
        return $result;
    }
    
    public function insert($data) {
        $db = $this->getDbConnection();
        $result = $db->insert($this->_tablename, $data);
        $this->_deleteAllCache();
        return $result;
    }
    public function update($id,$data) {
        $db= $this->getDbConnection();
        $query = "id = ".$id;
        $result = $db->update($this->_tablename, $data,$query);
        $this->_deleteAllCache();
//            var_dump($result);exit();
        return $result;
    }

    public function delete($id)
	{
		$db = $this->getDbConnection();
		$where = array();
		$where[] = "id='" . parent::adaptSQL($id) . "'";
		$result = $db->delete($this->_tablename,$where);
                if($result > 0)
		{
			$this->_deleteAllCache($id);
		}
                return $result;
	}
    public function _update($id) { // update trạng thái
            $db= $this->getDbConnection();
            $query = "id = ".$id;
            $data = array(
                "enabled" => "0"
            );
            $result = $db->update($this->_tablename, $data,$query);
            $this->_deleteAllCache();
            return $result;
    }
    public function restore($id) {
        //get current menu
            $db= $this->getDbConnection();
            $query = "itemid = ".$id;
            $data = array(
                "enabled" => "1"
            );
            $result = $db->update($this->_tablename, $data,$query);
//            var_dump($result);exit();
            $this->_deleteAllCache();
            return $result;
    }
    
    private function _deleteAllCache()
    {
            $cache = $this->getCacheInstance('ws');
            $cache->flushAll();
    }
}

?>

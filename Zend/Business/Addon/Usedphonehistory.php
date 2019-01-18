<?php

class Business_Addon_Usedphonehistory extends Business_Abstract {

    private $_tablename = 'addon_usedphone_history';
    private $_tablename_upload = 'addon_usedphone_history_upload';

    const KEY_LIST = 'uph.list.%s';   //key of list.questionid
    const KEY_DETAIL = 'uph.detail.%s';  //key of detail.id

    private static $_instance = null;

    function __construct() {
        
    }

    //public static function
    /**
     * get instance of Business_Addon_Usedphonehistory
     *
     * @return Business_Addon_Usedphonehistory
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
        $cache = GlobalCache::getCacheInstance('ws');
        return $cache;
    }

    public function getDetail($id) {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " WHERE id=?";
        $data = array($id);
        $result = $db->fetchAll($query, $data);
        return $result[0];
    }
    public function get_list_by_date($from,$to) {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " WHERE datetime >= '$from' and datetime <= '$to'";
        $result = $db->fetchAll($query);
        return $result[0];
    }
    
    public function getListPast5Days() {
        $time = time() - (5 * 24 * 60 * 60);
        $date = date("Y-m-d H:i:s", $time);        
            
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " WHERE datetime <= '$date' GROUP BY itemid ORDER BY datetime DESC";
        $data = array();
        $result = $db->fetchAll($query, $data);
        return $result;
    }
    
    public function getListByItemID($itemid) {
        $cache = $this->getCacheInstance();
        $key = "used.list.itemid." . md5($itemid);
        $result = $cache->getCache($key);
        if ($result === false) {
            $db = $this->getDbConnection();
            $query = "SELECT * FROM " . $this->_tablename . " WHERE itemid IN ($itemid)";
            $data = array();
            $result = $db->fetchAll($query, $data);
            $cache->setCache($key, $result, 60);
        }
        
        return $result;
    }
    
    public function getListPast5DaysByID($itemid) {
        $timestamp5Days = 5 * 24 * 60 * 60;
        $timePast5Days = time() - $timestamp5Days;
        $datetime = date("Y-m-d H:i:s", $timePast5Days);
        $cache = $this->getCacheInstance();
        $key = "used.list.itemid.day." . md5($itemid);
        $result = $cache->getCache($key);
$result=false;  
        if ($result === false) {
            $db = $this->getDbConnection();
            $query = "SELECT * FROM " . $this->_tablename . " WHERE itemid IN ($itemid) AND DATETIME <= '$datetime'";
			
            $data = array();
            $result = $db->fetchAll($query, $data);
			//var_dump($result);die();
            //$cache->setCache($key, $result, 60);
        }
        
        return $result;
    }
    
    public function getListByUsedID($usedid) {
            
        $cache = $this->getCacheInstance();
        $key = "used.list.usedid." . $usedid;
        $result = $cache->getCache($key);
        
        if ($result === false) {
            $db = $this->getDbConnection();
            $query = "SELECT * FROM " . $this->_tablename . " WHERE addon_usedphone_info_id=?";
            $data = array($usedid);
            $result = $db->fetchAll($query, $data);
            $cache->setCache($key, $result);
        }
        
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

    public function delete($id, $active, $sid) {
        $db = $this->getDbConnection();
        $query = "id = $id";
        $result = $db->update($this->_tablename, $data, $query);
        return $result;
    }

    public function restore($id) {
        $db = $this->getDbConnection();
        $query = "id = " . $id;
        $data = array(
            "active" => 1
        );
        $result = $db->update($this->_tablename, $data, $query);
        return $result;
    }

    public function update($id, $data) {
        $db = $this->getDbConnection();
        $where = array();
        $where[] = "id = '" . parent::adaptSQL($id) . "'";
        try {
            $result = $db->update($this->_tablename, $data, $where);
        } catch (Exception $e) {
            return 0;
        }
    }
    
    //upload
    public function getListUpload($key) {
            
		$db = $this->getDbConnection();
		$query = "SELECT * FROM " . $this->_tablename_upload . " WHERE id_upload='$key'";
		$data = array();
		$result = $db->fetchAll($query, $data);
        
        return $result;
    }
	
	public function insertUpload($data) {
        $db = $this->getDbConnection();
        $result = $db->insert($this->_tablename_upload, $data);
        if ($result > 0) {
            return $db->lastInsertId();
        }
        return 0;
    }

}

?>

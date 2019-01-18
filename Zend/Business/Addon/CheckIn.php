<?php

class Business_Addon_CheckIn extends Business_Abstract {

    private $_tablename = 'check_in';
    private static $_instance = null;

    function __construct() {
        
    }

    //public static function
    /**
     * get instance of Business_Addon_CheckIn
     *
     * @return Business_Addon_CheckIn
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
    public function getListByMonthYear($userid,$month,$year){
        $cache = $this->getCacheInstance();
        $key = "getListByMonthYear".$month.$year.$userid;
        $result = $cache->getCache($key);
        if($result === FALSE){
            $db = $this->getDbConnection();
            $query = "SELECT * FROM `check_in` WHERE userid = $userid and month(datetime) = $month and year(datetime)= $year";
//            var_dump($query);exit();
            $result = $db->fetchAll($query);
            if(!is_null($result) && is_array($result)){
                $cache->setCache($key, $result,300);
            }
        }
        
        return $result;
    }

    public function getDetailUserIdByDay($userid,$day,$location,$type){
        $db = $this->getDbConnection();
        $query = " select * from $this->_tablename where type = $type and location = $location and userid = $userid and DATE(datetime) = '$day'";
        $result = $db->fetchAll($query);
        return $result[0];
    }

    public function delete($itemid) {
        $current = $this->getDetail($itemid);
        $db = $this->getDbConnection();
        $where = array();
        $where[] = "item_id='" . parent::adaptSQL($itemid) . "'";
        $result = $db->delete($this->_tablename, $where);
        if ($result > 0) {
            $this->_deleteAllCache($itemid, $current['poll_id']);
        }
        return $result;
    }

    public function update($itemid, $data) {
        $db = $this->getDbConnection();
        $where = array();
        $where[] = "item_id = '" . parent::adaptSQL($itemid) . "'";
        try {
            $result = $db->update($this->_tablename, $data, $where);

            if ($result > 0) {
                $this->_deleteAllCache($itemid, $data['poll_id']);
            }

            return $result;
        } catch (Exception $e) {
            return 0;
        }
    }

}

?>

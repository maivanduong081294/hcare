<?php

class Business_Ws_Class extends Business_Abstract {

    private $_tablename = 'class';
    private static $_instance = null;

    const KEY_LIST = 'class.list';   //key of list by language
    const KEY_DETAIL = 'class.detail.%s';

    function __construct() {
        
    }

    //public static function
    /**
     * get instance of Business_Ws_Class
     *
     * @return Business_Ws_Class
     */
    public static function getInstance() {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    private function getKeyList() {
        return sprintf(self::KEY_LIST);
    }

    private function getKeyDetail($id) {
        return sprintf(self::KEY_DETAIL, $id);
    }

    /**
     * Get DB Connection
     *
     * @return Zend_Db_Adapter_Abstract
     */
    private function getDbConnection() {
        $db = Globals::getDbConnection('course');
        return $db;
    }

    private function getCacheInstance() {
        $cache = GlobalCache::getCacheInstance('ws');
        return $cache;
    }

    public function getList($all=false) {
        $curdate = date("Y-m-d H:i:s");
        $cache = $this->getCacheInstance();
        $key = $this->getKeyList($lang);
        $result = $cache->getCache($key);

		if ($all==true){
			$db = $this->getDbConnection();
			$query = "SELECT * FROM " . $this->_tablename . " WHERE 1=1 ORDER BY id ASC";
			$result = $db->fetchAll($query);
			return $result;
		}
		
        $result = false;
        if ($result === FALSE) {
            $db = $this->getDbConnection();
            $query = "SELECT * FROM " . $this->_tablename . " WHERE endtime >= '$curdate' ORDER BY starttime ASC";
            $data = array($lang);
            $result = $db->fetchAll($query, $data);
            if (!is_null($result) && is_array($result)) {
                $cache->setCache($key, $result, 60);
            }
        }
        return $result;
    }

    public function getDetail($id) {
        $cache = $this->getCacheInstance();
        $key = $this->getKeyDetail($id);
        $result = $cache->getCache($key);

        if ($result === FALSE) {
            $db = $this->getDbConnection();
            $query = "SELECT * FROM " . $this->_tablename . " WHERE id='" . parent::adaptSQL($id) . "'";
            $result = $db->fetchAll($query);
            if ($result != null && is_array($result)) {
                $result = $result[0];
                $cache->setCache($key, $result, 60);
            }
        }
        return $result;
    }

    public function update($id, $data) {
        $where = array();
        $where[] = "id='" . parent::adaptSQL($id) . "'";
        $db = $this->getDbConnection();
        $result = $db->update($this->_tablename, $data, $where);
        return $result;
    }

}

?>
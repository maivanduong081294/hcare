<?php

class Business_Addon_Menuitem2 extends Business_Abstract {

    private $_tablename = 'ws_menuitem';
    private static $_instance = null;

    function __construct() {
        
    }

    //public static function
    /**
     * get instance of Business_Addon_Menuitem2
     *
     * @return Business_Addon_Menuitem2
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
    public function getListByItemid($itemid) {
        $cache = $this->getCacheInstance();
        $key = "getListByItemid".  $this->_tablename.$itemid;
        $result = $cache->getCache($key);
//        $result = FALSE;
        if($result ===FALSE){
            $db = $this->getDbConnection();
            $query = "SELECT itemid,title FROM " . $this->_tablename . " WHERE itemid IN ($itemid)";
            $result = $db->fetchAll($query);
            if(!is_null($result) && is_array($result)){
                $cache->setCache($key, $result);
            }
        }
        
        return $result;
    }
    public function getDetail($id){
        $db= $this->getDbConnection();
        $query = "select * from $this->_tablename where id = $id";
        $result = $db->fetchAll($query);
        return $result[0];
    }
    public function getList($id){
        $db= $this->getDbConnection();
        $query = "select * from $this->_tablename where enabled = 1";
//         order by datetime DESC
        $result = $db->fetchAll($query);
        return $result;
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

    public function update($id,$data) {
        $db= $this->getDbConnection();
        $query = "id = ".$id;
        $result = $db->update($this->_tablename, $data,$query);
        return $result;
    }

}

?>

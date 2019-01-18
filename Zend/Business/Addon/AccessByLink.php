<?php

class Business_Addon_AccessByLink extends Business_Abstract {

    private $_tablename = 'access_by_link';

    const KEY_LIST = 'access_by_link.list.%s';   //key of list.questionid
    const KEY_DETAIL = 'access_by_link.detail.%s';  //key of detail.id
    const KEY_DETAIL_KM = 'km.%s';  //key of detail.id
    const KEY_DETAIL_LIST_KM = 'getlist_access_by_link.%s';  //key of list.id

    private static $_instance = null;

    function __construct() {
        
    }

    //public static function
    /**
     * get instance of Business_Addon_AccessByLink
     *
     * @return Business_Addon_AccessByLink
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
    
    private function getKeyKM($itemid){
        return sprintf(self::KEY_DETAIL_KM, $itemid);
    }
    private function getListKeyKM($keywork){
        return sprintf(self::KEY_DETAIL_LIST_KM,$keywork);
    }
    public function getList($kw=0){
        $cache = $this->getCacheInstance();
        $key = "getList".  $this->_tablename.$kw;
        $result  = $cache->getCache($key);
        if($result === false){
            $db = $this->getDbConnection();
            $query = "SELECT * FROM " . $this->_tablename . " WHERE status=1";
            if((int)$kw > 0){
                $query .=" and people like '%$kw%'";
            }
            $query .=" order by postion ASC";
            $result = $db->fetchAll($query);
            if(!is_null($result) && is_array($result)){
                $cache->setCache($key, $result);
            }
        }
        return $result;
    }

    public function getDetail($moudels,$controller,$action){
        $cache = $this->getCacheInstance();
        $key = "getDetails2".  $this->_tablename.$moudels.$controller.$action;
        $result  = $cache->getCache($key);
//        $result = FALSE;
        if($result === false){
            $db = $this->getDbConnection();
            $query = "SELECT * FROM " . $this->_tablename . " WHERE status = 1 and modules = '$moudels' and controller = '$controller' and action = '$action'";
//            var_dump($query);
            $result = $db->fetchAll($query);
            $result = $result[0];
            if(!is_null($result) && is_array($result)){
                $cache->setCache($key, $result);
            }
        }
        
        return $result;
    }
    public function getDetailById($id){
        $cache = $this->getCacheInstance();
        $key = "getDetailById".  $this->_tablename.$id;
        $result  = $cache->getCache($key);
        $result = FALSE;
        if($result === false){
            $db = $this->getDbConnection();
            $query = "SELECT * FROM " . $this->_tablename . " WHERE status = 1 and id = $id";
            $result = $db->fetchAll($query);
            $result = $result[0];
            if(!is_null($result) && is_array($result)){
                $cache->setCache($key, $result);
            }
        }
        
        return $result;
    }
    

    public function insert($data) {
        $db = $this->getDbConnection();
        $result = $db->insert($this->_tablename, $data);
        return $result;
    }
    public function update($id,$data) {
        $db= $this->getDbConnection();
        $query = "id = ".$id;
        $result = $db->update($this->_tablename, $data,$query);
        $this->deleteKeyKM($id);
        $this->deleteListKeyKM();
//            var_dump($result);exit();
        return $result;
    }
    public function excute($query){
            $db     =  $this->getDbConnection();
            $result = $db->query($query);
            return $result;
        }
        
    private function deleteKeyKM($itemid) {
        $cache = $this->getCacheInstance();
        $key = $this->getKeyKM($itemid);
        $cache->deleteCache($key);
    }
    private function deleteListKeyKM($keyword="") {
        $cache = $this->getCacheInstance();
        $key = $this->getListKeyKM($keyword);
        $cache->deleteCache($key);
    }
}

?>

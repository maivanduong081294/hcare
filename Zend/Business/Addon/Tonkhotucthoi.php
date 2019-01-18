<?php

class Business_Addon_Tonkhotucthoi extends Business_Abstract {

    private $_tablename = 'fast_tonkhoimei';

    const KEY_LIST = 'fast_tonkhoimei.list.%s';   //key of list.questionid
    const KEY_DETAIL = 'fast_tonkhoimei.detail.%s';  //key of detail.id
    const KEY_DETAIL_KM = 'km.%s';  //key of detail.id
    const KEY_DETAIL_LIST_KM = 'get_fast_tonkhoimei.%s';  //key of list.id

    private static $_instance = null;

    function __construct() {
        
    }

    //public static function
    /**
     * get instance of Business_Addon_Tonkhotucthoi
     *
     * @return Business_Addon_Tonkhotucthoi
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
        $db = Globals::getDbConnection('hnam_app');
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
   
    public function get_list_by_itemid($itemid,$ma_bp){
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " WHERE sl_ton >0 and itemid IN ($itemid)";
        if($ma_bp){
            $query .=" and ma_bp ='$ma_bp'";
        }
        $result = $db->fetchAll($query);
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
        return $result;
    }
    public function excute($query){
        $db     =  $this->getDbConnection();
        $result = $db->query($query);
        return $result;
    }
    
}

?>

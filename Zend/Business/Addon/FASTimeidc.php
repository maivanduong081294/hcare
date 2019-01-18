<?php

class Business_Addon_FASTimeidc extends Business_Abstract {

    private $_tablename = '[FAST_HNAM_DBTG].[dbo].[imeidc]';
    private static $_instance = null;
        
    function __construct() {
        
    }

    //public static function
    /**
     * get instance of Business_Addon_FASTimeidc
     *
     * @return Business_Addon_FASTimeidc
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
     * @return Maro_Db_Interface
     */
    private function getDbConnection() {
        $db = Globals::getDbInstance('windows');
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
    public function get_list_by_mavt($ma_vt,$ma_bp=""){
        $cache = $this->getCacheInstance();
        $key ="get_list_by_mavt".$this->_tablename.$ma_vt.$ma_bp;
        $result = $cache->getCache($key);
        $result = FALSE;
        if($result === FALSE){
            $db = $this->getDbConnection();
            $query = "select * from $this->_tablename where ma_vt IN ($ma_vt) and sl_ton >0";
            if($ma_bp != NULL){
                $query .=" and ma_bp = '$ma_bp'";
            }
            
            $result = $db->select2($query);
            if(!is_null($result) && is_array($result)){
                $cache->setCache($key, $result,120);
            }
        }
        return $result;
    }
    public function get_list_by_mavt_web($ma_vt,$ma_bp=""){
        $cache = $this->getCacheInstance();
        $key ="get_list_by_mavt_web".$this->_tablename.$ma_vt.$ma_bp;
        $result = $cache->getCache($key);
        if($result === FALSE){
            $db = $this->getDbConnection();
            $query = "select * from $this->_tablename where ma_vt IN ($ma_vt) and sl_ton >0";
            if($ma_bp != NULL){
                $query .=" and ma_bp = '$ma_bp'";
            }
            
            $result = $db->select2($query);
            if(!is_null($result) && is_array($result)){
                $cache->setCache($key, $result,1800);
            }
        }
        return $result;
    }
    
    public function get_list_all() {
		$db = $this->getDbConnection();
		$query = "select * from $this->_tablename where sl_ton >0";		
		$result = $db->select2($query);
		return $result;
	}
    
}

?>

<?php

class Business_Addon_MappingStore extends Business_Abstract
{
	private $_tablename = 'app_mapping_store';
	
	const KEY_LIST              = 'app_mapping_store.list.%s';
	const KEY_LIST2             = 'app_mapping_store.list2.%s';
	const KEY_DETAIL = 'app_mapping_store.uid.%s';
    
	protected static $_current_rights = null;
	
	protected static $_instance = null; 

	function __construct()
	{
		
	}
	
	/**
	 * get instance of Business_Addon_MappingStore
	 *
	 * @return Business_Addon_MappingStore
	 */
	public static function getInstance()
	{
		if(self::$_instance == null)
		{
			self::$_instance = new self();
		}
		return self::$_instance;
	}
        private function getKeyList($itemid){
        return sprintf(self::KEY_LIST, $itemid);
        }
        
        private function getKeyDetail($keywork){
            return sprintf(self::KEY_DETAIL,$keywork);
        }
        private function deleteKeyDetail($itemid) {
        $cache = $this->getCacheInstance();
        $key = $this->getKeyDetail($itemid);
        $cache->deleteCache($key);
        }
        private function deleteKeyList($keyword="") {
            $cache = $this->getCacheInstance();
            $key = $this->getKeyList($keyword);
            $cache->deleteCache($key);
        }
        
        private function getKeyList2($keys){
        return sprintf(self::KEY_LIST2, $keys);
        }
        private function deleteKeyList2($keys=0) {
            $cache = $this->getCacheInstance();
//            $key = $this->getKeyList2($keys);
            $cache->deleteCache($keys);
        }
        
        
        
	/**
	 * get Zend_Db connection
	 *
	 * @return Zend_Db_Adapter_Abstract
	 */
	function getDbConnection()
	{		
		$db    	= Globals::getDbConnection('maindb', false);
		return $db;	
	}
	
	/**
	 * Enter description here...
	 *
	 * @return Maro_Cache
	 */
	function getCacheInstance()
	{
		$cache = GlobalCache::getCacheInstance('event');		
		return $cache;
	}
       
        public function get_list_by_id_store()
	{
            $cache = $this->getCacheInstance();
            $key = "get_list_by_id_stores".  $this->_tablename;
            $result = $cache->getCache($key);
            if($result ===FALSE){
                $db = $this->getDbConnection();
                $query = "SELECT * FROM $this->_tablename where id_store >0 group by id_store order by id_fast_bp asc";
                $result = $db->fetchAll($query);
                if(!is_null($result) && is_array($result)){
                    $cache->setCache($key, $result);
                }
            }
                
		return $result;		
	}
        public function get_list($storeid)
	{
           $cache = $this->getCacheInstance();
            $key = $this->get_key_list($storeid);
            $result=$cache->getCache($key);
            if($result === FALSE)
            {
            $db = $this->getDbConnection();
            $query = " SELECT * FROM " . $this->_tablename;
            $result = $db->fetchAll($query);
            if(!is_null($result) && is_array($result)){
                    $cache->setCache($key, $result);
                }
            }
            return $result;
	}
        public function get_list_by_id($id)
	{
                $db = $this->getDbConnection();
                $query = "SELECT * FROM $this->_tablename where itemid = $id";
                $result = $db->fetchAll($query);
		return $result;		
	}
        public function get_detail_by_storeid($id_store)
	{
                $db = $this->getDbConnection();
                $query = "SELECT * FROM $this->_tablename where id_store = $id_store";
                $result = $db->fetchAll($query);
		return $result[0];		
	}
        public function get_detail_by_id($id)
	{
                $db = $this->getDbConnection();
                $query = "SELECT * FROM $this->_tablename where itemid = $id";
                $result = $db->fetchAll($query);
		return $result[0];		
	}
        public function get_detail_by_id_fast_warehouse($id_fast_warehouse)
	{
                $db = $this->getDbConnection();
                $query = "SELECT * FROM $this->_tablename where id_fast_warehouse = '$id_fast_warehouse'";
                $result = $db->fetchAll($query);
		return $result[0];		
	}
	public function getListById($id)
	{
		$cache = $this->getCacheInstance();
		$key = 'getListById'.$this->_tablename.$id;
		$result = $cache->getCache($key);
		$result = FALSE;				
		if($result === FALSE)
		{
			$db = $this->getDbConnection();
			$query = "SELECT * FROM $this->_tablename where itemid IN ($id)";
//                        echo "<pre>";
//                        var_dump($query);
//                        die();
			$result = $db->fetchAll($query);
			if(!is_null($result) && is_array($result))
			{
				$cache->setCache($key, $result);
			}
		}
		
		return $result;		
	}
		
}
?>
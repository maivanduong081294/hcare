<?php

class Business_Addon_AddonProductTitleKT extends Business_Abstract
{
	private $_tablename = 'addon_product_title_kt';
	
	const KEY_LIST              = 'addon_product_title_kt.list.%s';
	const KEY_LIST2             = 'addon_product_title_kt.list2.%s';
	const KEY_DETAIL = 'addon_product_title_kt.uid.%s';
    
	protected static $_current_rights = null;
	
	protected static $_instance = null; 

	function __construct()
	{
		
	}
	
	/**
	 * get instance of Business_Addon_AddonProductTitleKT
	 *
	 * @return Business_Addon_AddonProductTitleKT
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
		$db    	= Globals::getDbConnection('hnam_wh', false);
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
       private function _deleteAllCache()
        {
                $cache = $this->getCacheInstance('ws');
                $cache->flushAll();
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
			$result = $db->fetchAll($query);
			if(!is_null($result) && is_array($result))
			{
				$cache->setCache($key, $result);
			}
		}
		
		return $result;		
	}
        public function getListByPidByColorid($product_id,$colorid)
	{
		$cache = $this->getCacheInstance();
		$key = 'getListByPidByColorid'.$this->_tablename.$product_id.$colorid;
		$result = $cache->getCache($key);
		$result = FALSE;				
		if($result === FALSE)
		{
			$db = $this->getDbConnection();
			$query = "SELECT * FROM $this->_tablename where itemid = $product_id and colorid = $colorid";
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
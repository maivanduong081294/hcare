<?php

class Business_Import_Category extends Business_Abstract
{
	private $_tablename = 'ws_menuitem';
	private static $_instance = null; 
	const EXPIRED = 3000; //secs
	const KEY_DETAIL = 'number.detail.%s'; //key of detail.id
	function __construct()
	{
		
	} 
	
	/**
	 * get instance of Business_Addon_Number
	 *
	 * @return Business_Addon_Number
	 */
	public static function getInstance()
	{
		if(self::$_instance == null)
		{
			self::$_instance = new Business_Import_Product();
		}
		return self::$_instance;
	}
	
	/**
	 * get Zend_Db connection
	 *
	 * @return Zend_Db_Adapter_Abstract
	 */
	function getDbConnection()
	{		
		$db = Globals::getDbConnection('maindb', false);
		return $db;	
	}
	private function getCacheInstance() {
        $cache = GlobalCache::getCacheInstance('ws');
        return $cache;
    }
	public function getNameByItemid($itemid) {
        $cache = $this->getCacheInstance();
        $key = 'getNameByItemid' . $id;
        $result = $cache->getCache($key);
        if ($result === false) {
            $db = $this->getDbConnection();
            $query = "select * from $this->_tablename where itemid = $itemid";
            $result = $db->fetchAll($query);
            if (!is_null($result) && is_array($result)) {
                $cache->setCache($key, $result);
            }
        }

        return $result;
    }
}
?>
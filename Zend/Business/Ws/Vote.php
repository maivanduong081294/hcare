<?php

class Business_Ws_AddonVote extends Business_Abstract
 {
	private $_tablename = 'addon_vote';
	private static $_instance = null; 
	
	function __construct()
	{			
	}
	
	//public static function
	/**
	 * get instance of Business_Ws_City
	 *
	 * @return Business_Ws_City
	 */
	public static function getInstance()
	{
		if(self::$_instance == null)
		{
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	/**
	 * Get DB Connection
	 *
	 * @return Zend_Db_Adapter_Abstract
	 */
	private function getDbConnection()
	{		
		$db    	= Globals::getDbConnection('maindb');		
		return $db;	
	}
	
	/**
	 * Enter description here...
	 *
	 * @return Maro_Cache
	 */
	private function getCacheInstance()
	{
		$cache = GlobalCache::getCacheInstance('ws');
		return $cache;
	}
	
	
			
	public function getDetail($cityid)
	{
            $db = $this->getDbConnection();
            $query = "SELECT * FROM " .$this->_tablename . " WHERE id='" . parent::adaptSQL($cityid) . "'";
            $result = $db->fetchAll($query);
            if($result != null && is_array($result))
            {
                    $result = $result[0];
                    return $result;
            }
            return array();
	}

        public function getList($countryid)
	{
            $db = $this->getDbConnection();
            $query = "SELECT * FROM " .$this->_tablename . " WHERE countryid='" . parent::adaptSQL($countryid) . "'" . " ORDER BY ordering asc";
            $result = $db->fetchAll($query);
            if($result != null && is_array($result))
            {                    
                    return $result;
            }
            return array();
	}
	
}
?>
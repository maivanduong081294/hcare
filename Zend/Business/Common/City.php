<?php

class Business_Common_City extends Business_Abstract
{
	private $_tablename = 'data_city';
	
	const KEY_LIST = 'city-all';		//key list all
	const KEY_LIST_CID = 'ca-%s';		//key list all
		
	private static $_instance = null; 
	
	function __construct()
	{			
	}	
	
	function getKeyList()
	{
		return sprintf(Business_Common_City::KEY_LIST);
	}

	function getKeyListByCid($cid)
	{
		return sprintf(Business_Common_City::KEY_LIST_CID, $cid);
	}
	
	/**
	 * get instance of Business_Common_City
	 *
	 * @return Business_Common_City
	 */
	public static function getInstance()
	{
		if(self::$_instance == null)
		{
			self::$_instance = new Business_Common_City();
		}
		return self::$_instance;
	}	
	
	/**
	 * Get DB Connection
	 *
	 * @return Zend_Db_Adapter_Abstract
	 */
	function getDbConnection()
	{		
		$db    	= Globals::getDbConnection('maindb');		
		return $db;	
	}
	
	/**
	 * Enter description here...
	 *
	 * @return Maro_Cache
	 */
	function getCacheInstance()
	{
		$cache = GlobalCache::getCacheInstance();
		return $cache;
	}

	public function getListCity()
	{
		$key = $this->getKeyList();
		$cache = $this->getCacheInstance();		
		$result = $cache->getCache($key);		
		if($result === FALSE)
		{
			$db = $this->getDbConnection();
			$query = "SELECT * FROM " . $this->_tablename . " where countryid = 1 ORDER BY ordering";
			$result = $db->fetchAll($query);
			if(!is_null($result) && is_array($result))
			{
				$cache->setCache($key, $result);
			}
		}		
		return $result;		
	}

	public function getCityOfCountry($cid)
	{
		$key = $this->getKeyListByCid($cid);
		$cache = $this->getCacheInstance();
		$result = $cache->getCache($key);
		if($result === FALSE)
		{
			$db = $this->getDbConnection();
			$query = "SELECT * FROM " . $this->_tablename . " where countryid = $cid ORDER BY ordering";
			$result = $db->fetchAll($query);
			if(!is_null($result) && is_array($result))
			{
				$cache->setCache($key, $result);
			}
		}
		return $result;	
	}
	
	public function addCity($name)
	{
		$db = $this->getDbConnection();
		$data = array("name" => $catename);
		
		$result = $db->insert($this->_tablename, $data);
		$cache = $this->getCacheInstance();
		$key = $this->getKeyList();
		$cache->deleteCache($key);
		return $result;		
	}
	
	public function deleteCity($cityid)
	{				
		$db = $this->getDbConnection();
		$where = array();
		$where[] = "cityid='" . parent::adaptSQL($cityid) . "'";
		$db->delete($this->_tablename, $where);
		$cache = $this->getCacheInstance();
		$key = $this->getKeyList();
		$cache->deleteCache($key);
	}
		
	public function updateCity($cityid, $data)
	{
		$db = $this->getDbConnection();
		$where = "cityid='" . parent::adaptSQL($cityid) . "'";
		$result = $db->update($this->_tablename, $data, $where);
		$cache = $this->getCacheInstance();
		$key = $this->getKeyList();
		$cache->deleteCache($key);
		return $result;
	}

        public function getCityDetail($cityid){
            $db = $this->getDbConnection();
            $query = "SELECT * FROM " . $this->_tablename . " where id = $cityid";
            $result = $db->fetchAll($query);
            return $result[0];
        }
}

?>
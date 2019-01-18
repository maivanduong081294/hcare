<?php

class Business_BlockManagement_Boxes extends Business_Abstract
{
	private $_tablename = 'zfw_boxes';
	
	const KEY = 'boxes.boxid.%s'; //boxes.boxid.[id]
	const KEY_LIST = 'boxes.list';
		 	
	private static $_instance = null; 
	
	function __construct()
	{
		
	}
	
	/**
	 * get instance of Business_BlockManagement_Boxes
	 *
	 * @return Business_BlockManagement_Boxes
	 */
	public static function getInstance()
	{
		if(self::$_instance == null)
		{
			self::$_instance = new Business_BlockManagement_Boxes();
		}
		return self::$_instance;
	}
	
	
	
	public function getKey($boxid)
	{
		return sprintf(Business_BlockManagement_Boxes::KEY,$boxid);
	}	
	
	public function getKeyList()
	{
		return Business_BlockManagement_Boxes::KEY_LIST;
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
		$cache = GlobalCache::getCacheInstance();		
		return $cache;
	}

	public function getList()
	{
		$key = $this->getKeyList();
		$cache = $this->getCacheInstance();
		$result = $cache->getCache($key);		
		if($result === FALSE)
		{			
			$db = $this->getDbConnection();
			$query = "select * from " . $this->_tablename . " ORDER BY boxname";			
			$result = $db->fetchAll($query);
			if(!is_null($result) && is_array($result))
			{
				$cache->setCache($key,$result);
			}			
		}
		return $result;
	}
		
	public function getBox($boxid)
	{		
		$key = $this->getKey($boxid);
		$cache = $this->getCacheInstance();
		$result = $cache->getCache($key);
		if($result === FALSE)
		{			
			$db = $this->getDbConnection();
			$query = " SELECT * FROM " . $this->_tablename
				." WHERE boxid = ?";
			$data = array($boxid);					
			$result = $db->fetchAll($query,$data);
			if(!is_null($result) && is_array($result))
			{
				$cache->setCache($key,$result);
			}
		}
		
		if($result != null && is_array($result) && count($result) > 0)
		{
			return $result[0];				
		}
		else return null;
	}
	
	public function updateBox($boxid, $data)
	{		
		$db = $this->getDbConnection();
		$where = array();	
		$where[] = "boxid='" . parent::adaptSQL($boxid) . "'";
		try
		{			
			$result = $db->update($this->_tablename, $data, $where);
			//xoa cache
			$cache = $this->getCacheInstance();
			$key = $this->getKey($boxid);
			$cache->deleteCache($key);
			$key = $this->getKeyList();
			$cache->deleteCache($key);
			return $result; 
		}
		catch(Exception $e)
		{
			return 0;
		}
	}
	
	public function insertBox($data)
	{
		$db = $this->getDbConnection();
		$result = $db->insert($this->_tablename,$data);		
		//xoa cache list
		$cache = $this->getCacheInstance();
		$key = $this->getKeyList();
		$cache->deleteCache($key);
		return $result;		
	}
	
	public function deleteBox($boxid)
	{
		$block = $this->getBox($boxid);
		if($block == null) return;	
		
		$db = $this->getDbConnection();
		$where = array();
		$where[] = "boxid='" . parent::adaptSQL($boxid) . "'";
		$result = $db->delete($this->_tablename,$where);
		$cache = $this->getCacheInstance();
		$key = $this->getKey($boxid);
		$cache->deleteCache($key);
		$key = $this->getKeyList();
		$cache->deleteCache($key);
	}	
	
}

?>
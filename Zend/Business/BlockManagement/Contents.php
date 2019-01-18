<?php

class Business_BlockManagement_Contents extends Business_Abstract
{
	private $_tablename = 'zfw_contents';
	
	const KEY = 'contents.contentid.%s'; //contents.contentid.[id]
	const KEY_LIST = 'contents.list';
		 	
	private static $_instance = null; 
	
	function __construct()
	{
		
	}
	
	/**
	 * get instance of Business_BlockManagement_Contents
	 *
	 * @return Business_BlockManagement_Contents
	 */
	public static function getInstance()
	{
		if(self::$_instance == null)
		{
			self::$_instance = new Business_BlockManagement_Contents();
		}
		return self::$_instance;
	}	
	
	public function getKey($contentid)
	{
		return sprintf(Business_BlockManagement_Contents::KEY,$contentid);
	}	
	
	public function getKeyList()
	{
		return Business_BlockManagement_Contents::KEY_LIST;
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
			$query = "select * from " . $this->_tablename . " ORDER BY contentname";			
			$result = $db->fetchAll($query);
			if(!is_null($result) && is_array($result))
			{
				$cache->setCache($key,$result);
			}			
		}
		return $result;
	}
		
	public function getContent($contentid)
	{		
		$key = $this->getKey($contentid);
		$cache = $this->getCacheInstance();
		$result = $cache->getCache($key);
		if($result === FALSE)
		{			
			$db = $this->getDbConnection();
			$query = " SELECT * FROM " . $this->_tablename
				." WHERE contentid = ?";
			$data = array($contentid);		
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
	
	public function updateContent($contentid, $data)
	{		
		$db = $this->getDbConnection();
		$where = array();	
		$where[] = "contentid='" . parent::adaptSQL($contentid) . "'";
		try
		{			
			$result = $db->update($this->_tablename, $data, $where);
			//xoa cache
			$cache = $this->getCacheInstance();
			$key = $this->getKey($contentid);
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
	
	public function insertContent($data)
	{
		$db = $this->getDbConnection();
		$result = $db->insert($this->_tablename,$data);		
		//xoa cache list
		$cache = $this->getCacheInstance();
		$key = $this->getKeyList();
		$cache->deleteCache($key);
		return $result;		
	}
	
	public function deleteContent($contentid)
	{
		$block = $this->getContent($contentid);
		if($block == null) return;	
		
		$db = $this->getDbConnection();
		$where = array();
		$where[] = "contentid='" . parent::adaptSQL($contentid) . "'";
		$result = $db->delete($this->_tablename,$where);
		$cache = $this->getCacheInstance();
		$key = $this->getKey($viewid);
		$cache->deleteCache($key);
		$key = $this->getKeyList();
		$cache->deleteCache($key);
	}	
	
}

?>

<?php

class Business_BlockManagement_Views extends Business_Abstract
{
	private $_tablename = 'zfw_views';
	
	const KEY = 'views.viewid.%s'; //views.viewid.[id]
	const KEY_LIST = 'views.list';
		 	
	private static $_instance = null; 
	
	function __construct()
	{
		
	}
	
	/**
	 * get instance of Business_BlockManagement_Views
	 *
	 * @return Business_BlockManagement_Views
	 */
	public static function getInstance()
	{
		if(self::$_instance == null)
		{
			self::$_instance = new Business_BlockManagement_Views();
		}
		return self::$_instance;
	}
	
	
	
	public function getKey($viewid)
	{
		return sprintf(Business_BlockManagement_Views::KEY,$viewid);
	}	
	
	public function getKeyList()
	{
		return Business_BlockManagement_Views::KEY_LIST;
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
		$cache = GlobalCache::getCacheInstance('qs');		
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
			$query = "select * from " . $this->_tablename . " ORDER BY module, controller, action, viewname";			
			$result = $db->fetchAll($query);
			if(!is_null($result) && is_array($result))
			{
				$cache->setCache($key,$result);
			}			
		}
		return $result;
	}
		
	public function getView($viewid)
	{		
		$key = $this->getKey($viewid);
		$cache = $this->getCacheInstance();
		$result = $cache->getCache($key);
		if($result === FALSE)
		{			
			$db = $this->getDbConnection();
			$query = " SELECT * FROM " . $this->_tablename
				." WHERE viewid = ?";
			$data = array($viewid);		
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
	
	public function updateView($viewid, $data)
	{		
		$db = $this->getDbConnection();
		$where = array();	
		$where[] = "viewid='" . parent::adaptSQL($viewid) . "'";
		try
		{			
			$result = $db->update($this->_tablename, $data, $where);
			//xoa cache
			$cache = $this->getCacheInstance();
			$key = $this->getKey($viewid);
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
	
	public function insertView($data)
	{
		$db = $this->getDbConnection();
		$result = $db->insert($this->_tablename,$data);
		$layoutname = $data["layout_name"];
		//xoa cache list
		$cache = $this->getCacheInstance();
		$key = $this->getKeyList();
		$cache->deleteCache($key);
		return $result;		
	}
	
	public function deleteView($viewid)
	{
		$block = $this->getView($viewid);
		if($block == null) return;	
		
		$db = $this->getDbConnection();
		$where = array();
		$where[] = "viewid='" . parent::adaptSQL($viewid) . "'";
		$result = $db->delete($this->_tablename,$where);
		$cache = $this->getCacheInstance();
		$key = $this->getKey($viewid);
		$cache->deleteCache($key);
		$key = $this->getKeyList();
		$cache->deleteCache($key);
	}	
	
}

?>
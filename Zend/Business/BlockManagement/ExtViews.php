<?php

class Business_BlockManagement_ExtViews extends Business_Abstract
{
	private $_tablename = 'zfw_extviews';
	
	const KEY = 'extviews.extviewid.%s'; //views.viewid.[id]
	const KEY_LIST = 'extviews.list';
		 	
	private static $_instance = null; 
	
	function __construct()
	{
		
	}
	
	/**
	 * get instance of Zing_Me_Business_BlockManagement_ExtViews
	 *
	 * @return Zing_Me_Business_BlockManagement_ExtViews
	 */
	public static function getInstance()
	{
		if(self::$_instance == null)
		{
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	
	
	public function getKey($id)
	{
		return sprintf(self::KEY,$id);
	}	
	
	public function getKeyList()
	{
		return self::KEY_LIST;
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
			$query = "select * from " . $this->_tablename . " ORDER BY callback";			
			$result = $db->fetchAll($query);
			if(!is_null($result) && is_array($result))
			{
				$cache->setCache($key,$result);
			}			
		}		
		return $result;
	}
		
	public function getView($extviewid)
	{				
		$key = $this->getKey($extviewid);
		$cache = $this->getCacheInstance();
		$result = $cache->getCache($key);
		if($result === FALSE)
		{			
			$db = $this->getDbConnection();
			$query = " SELECT * FROM " . $this->_tablename
				." WHERE extviewid = ?";
			$data = array($extviewid);		
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
	
	public function updateView($extviewid, $data)
	{		
		$db = $this->getDbConnection();
		$where = array();	
		$where[] = "extviewid='" . parent::adaptSQL($extviewid) . "'";
		try
		{			
			$result = $db->update($this->_tablename, $data, $where);
			//xoa cache
			$cache = $this->getCacheInstance();
			$key = $this->getKey($extviewid);
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
		//xoa cache list
		$cache = $this->getCacheInstance();
		$key = $this->getKeyList();
		$cache->deleteCache($key);
		return $result;		
	}
	
	public function deleteView($extviewid)
	{
		$block = $this->getView($extviewid);
		if($block == null) return;	
		
		$db = $this->getDbConnection();
		$where = array();
		$where[] = "extviewid='" . parent::adaptSQL($extviewid) . "'";
		$result = $db->delete($this->_tablename,$where);
		$cache = $this->getCacheInstance();
		$key = $this->getKey($extviewid);
		$cache->deleteCache($key);
		$key = $this->getKeyList();
		$cache->deleteCache($key);
	}	
	
}

?>
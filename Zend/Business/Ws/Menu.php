<?php

class Business_Ws_Menu extends Business_Abstract
{
	private $_tablename = 'ws_menu';
	
	const KEY_LIST = 'menu.list';			//key of list 
	const KEY_COUNT = 'menu.totalcount'; 	//key of total count
	const KEY_DETAIL = 'menu.detail.%s';	//key of detail.id
	const KEY_DETAIL_MENU_NAME = 'menu.detail.name.%s';	//key of detail by menu name
	
	private static $_instance = null; 
	
	function __construct()
	{			
	}
	
	/**
	 * get instance of Business_Ws_Menu
	 *
	 * @return Business_Ws_Menu
	 */
	public static function getInstance()
	{
		if(self::$_instance == null)
		{
			self::$_instance = new self();
		}
		return self::$_instance;
	}	

	private function getKeyCount()
	{
		return self::KEY_COUNT;
	}
	
	private function getKeyList()
	{
		return sprintf(self::KEY_LIST);
	}
	
	private function getKeyDetail($id)
	{
		return sprintf(self::KEY_DETAIL,$id);
	}
	
	private function getKeyByName($menuname)
	{
		return sprintf(self::KEY_DETAIL_MENU_NAME, $menuname);
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
	
	public function getList()
	{
		$cache = $this->getCacheInstance();
		$key = $this->getKeyList();
		$result = $cache->getCache($key);
		if($result === FALSE)
		{
			$db = $this->getDbConnection();
			$query = "SELECT * FROM " . $this->_tablename . " ORDER BY menuname";
			$result = $db->fetchAll($query);
			if(!is_null($result) && is_array($result))
			{
				$cache->setCache($key, $result);
			}			
		}
		return $result;
	}
	
	public function getTotalCount()
	{
		$cache = $this->getCacheInstance();
		$key = $this->getKeyCount();
		$result = $cache->getCache($key);
		if($result === FALSE)
		{
			$db = $this->getDbConnection();
			$query = "SELECT COUNT(*) AS sum FROM " . $this->_tablename;
			$result = $db->fetchAll($query);
			if(!is_null($result) && is_array($result) && !empty($result))
			{
				$result = intval($result[0]['sum']);
			}
			else $result = 0;
			$cache->setCache($key,$result);
		}
		return $result;
	}
	
	public function getDetail($id)
	{
		$cache = $this->getCacheInstance();
		$key = $this->getKeyDetail($id);
		$result = $cache->getCache($key);
		
		if($result === FALSE)
		{
			$db = $this->getDbConnection();
			$query = "SELECT * FROM " .$this->_tablename . " WHERE menuid='" . parent::adaptSQL($id) . "'";
			$result = $db->fetchAll($query);
			if($result != null && is_array($result))
			{
				$result = $result[0];
				$cache->setCache($key, $result);				
			}
		}
		return $result;
	}
	
	public function getDetailByMenuName($menuname)
	{
		$cache = $this->getCacheInstance();
		$key = $this->getKeyByName($menuname);
		$result = $cache->getCache($key);		
		if($result === FALSE)
		{
			$db = $this->getDbConnection();
			$query = "SELECT * FROM " .$this->_tablename . " WHERE menuname='" . parent::adaptSQL($menuname) . "'";
			$result = $db->fetchAll($query);
			if($result != null && is_array($result))
			{
				$result = $result[0];
				$cache->setCache($key, $result);				
			}
		}
		return $result;
	}
	
	public function insert($data)
	{
		$db = $this->getDbConnection();
		$result = $db->insert($this->_tablename, $data);
		if($result > 0)
		{
			$this->_deleteAllCache();			
		}
	}
	
	public function update($id, $data)
	{
		$where = array();
		$where[] = "menuid='" . parent::adaptSQL($id) . "'";
		$db = $this->getDbConnection();
		$result = $db->update($this->_tablename, $data,$where);
		if($result > 0)
		{
			$this->_deleteAllCache($id);
			if(array_key_exists('menuname', $data))
			{
				$this->_deleteCacheByName($data['menuname']);
			}
		}
	}
	
	public function delete($id)
	{
		//get current menu
		$current = $this->getDetail($id);
				
		$where = array();
		$where[] = "menuid='" . parent::adaptSQL($id) . "'";
		$db = $this->getDbConnection();
		$result = $db->delete($this->_tablename, $where);
		if($result > 0)
		{
			$this->_deleteAllCache($id);
			if($current != null)
			{
				$menuname = $current['menuname'];
				if($menuname != null && !empty($menuname))
				{
					$this->_deleteCacheByName($menuname);
				}
			}
		}
	}
	
	///private functions /////

	private function _deleteCacheByName($menuname)
	{
		$cache = $this->getCacheInstance();
		$key = $this->getKeyByName($menuname);
		$cache->deleteCache($key);
	}
	
	private function _deleteAllCache($id = null)
	{
		$cache = $this->getCacheInstance();
		$key = $this->getKeyList();
		$cache->deleteCache($key);
		$key = $this->getKeyCount();
		$cache->deleteCache($key);
		if($id != null)
		{
			$key = $this->getKeyDetail($id);
			$cache->deleteCache($key);
		}		
	}
	
}
?>
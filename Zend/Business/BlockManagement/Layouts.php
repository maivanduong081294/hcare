<?php

class Business_BlockManagement_Layouts extends Business_Abstract
{
	private $_tablename = 'zfw_layouts';
	
	const KEY_BY_LAYOUT = 'layouts.layoutname.%s'; //blocks.layout.layout-name	
	const KEY_LIST = 'layouts.layout';
		 	
	private static $_instance = null; 
	
	function __construct()
	{
		
	}
	
	/**
	 * get instance of Business_BlockManagement_Layouts
	 *
	 * @return Business_BlockManagement_Layouts
	 */
	public static function getInstance()
	{
		if(self::$_instance == null)
		{
			self::$_instance = new Business_BlockManagement_Layouts();
		}
		return self::$_instance;
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
	
	public function getKeyList()
	{
		return Business_BlockManagement_Layouts::KEY_LIST;
	}
	
	public function getKeyByLayout($layout)
	{
		return sprintf(Business_BlockManagement_Layouts::KEY_BY_LAYOUT,$layout);
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

	public function getList()
	{		
		$key = $this->getKeyList();
		$cache = $this->getCacheInstance();
		$result = $cache->getCache($key);		
		if($result === FALSE)
		{
			$db = $this->getDbConnection();
			$query = "select * from " . $this->_tablename . " ORDER BY layout_name ASC";			
			$result = $db->fetchAll($query);			
			if(!is_null($result) && is_array($result))
			{
				$cache->setCache($key,$result);
			}			
		}
		return $result;
	}		
		
	public function getLayout($layoutname)
	{
		if($layoutname == '') return null;
		$key = $this->getKeyByLayout($layoutname);
		$cache = $this->getCacheInstance();
		$result = $cache->getCache($key);
		if($result === FALSE)
		{			
			$db = $this->getDbConnection();
			$query = " SELECT * FROM " . $this->_tablename
				." WHERE layout_name = ?";
			$data = array($layoutname);		
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
	
	public function updateLayout($layoutname, $data)
	{		
		$db = $this->getDbConnection();
		$where = array();	
		$where[] = "layout_name='" . parent::adaptSQL($layoutname) . "'";
		try
		{			
			$result = $db->update($this->_tablename, $data, $where);
			//xoa cache
			$cache = $this->getCacheInstance();
			$key = $this->getKeyList();
			$cache->deleteCache($key);
			$key = $this->getKeyByLayout($layoutname);
			$cache->deleteCache($key);			
			return $result; 
		}
		catch(Exception $e)
		{
			return 0;
		}
	}
	
	public function insertLayout($data)
	{
		$db = $this->getDbConnection();
		$result = $db->insert($this->_tablename,$data);
		$layoutname = $data["layout_name"];
		//xoa cache list
		$cache = $this->getCacheInstance();
		$key = $this->getKeyList();
		$cache->deleteCache($key);
		$key = $this->getKeyByLayout($layoutname);
		$cache->deleteCache($key);		
		return $result;		
	}
	
	public function deleteLayout($layoutname)
	{
		$block = $this->getLayout($layoutname);
		if($block == null) return;	
		
		$db = $this->getDbConnection();
		$where = array();
		$where[] = "layout_name='" . parent::adaptSQL($layoutname) . "'";
		$result = $db->delete($this->_tablename,$where);
		$cache = $this->getCacheInstance();
		$key = $this->getKeyList();
		$cache->deleteCache($key);
		$key = $this->getKeyByLayout($layoutname);
		$cache->deleteCache($key);		
	}
}

?>
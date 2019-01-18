<?php

class Business_BlockManagement_Blocks extends Business_Abstract
{
	private $_tablename = 'zfw_blocks';
	
	const KEY_LIST_BY_LAYOUT = 'blocks.layout.%s'; //blocks.layout.layout-name
	const KEY_LIST_BY_LAYOUT_ACTIVE = 'blocks.layout.%s.active';	//blocks.layout.layout-name.active
	const KEY_LIST = 'blocks.layout';
		 	
	private static $_instance = null; 
	
	function __construct()
	{
		
	}
	
	/**
	 * get instance of Business_BlockManagement_Blocks
	 *
	 * @return Business_BlockManagement_Blocks
	 */
	public static function getInstance()
	{
		if(self::$_instance == null)
		{
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	public function getKeyList()
	{
		return self::KEY_LIST;
	}
	
	public function getKeyListByLayout($layout)
	{
		return sprintf(self::KEY_LIST_BY_LAYOUT,$layout);
	}
	
	public function getKeyListByLayoutActive($layout)
	{
		return sprintf(self::KEY_LIST_BY_LAYOUT_ACTIVE,$layout);
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

	public function getListByLayout($layout)
	{
		$key = $this->getKeyListByLayoutActive($layout);
		$cache = $this->getCacheInstance();
		$result = $cache->getCache($key);
				
		if($result === FALSE)
		{
			$db = $this->getDbConnection();
			$query = "select * from " . $this->_tablename . " where layout = ? and status=1 order by section,weight asc";
			$data = array($layout);
			$result = $db->fetchAll($query,$data);
			if(!is_null($result) && is_array($result))
			{
				$cache->setCache($key,$result);
			}			
		}
		return $result;
	}
		
	public function getList($layout = '')
	{		
		$result = null;
		$key = $this->getKeyListByLayout($layout);
		$cache = $this->getCacheInstance();
		$result = $cache->getCache($key);		
		if($result === FALSE)
		{
			$db = $this->getDbConnection();			
			if($layout == '')
			{
				$query = "select * from " . $this->_tablename . " order by blockid asc";
				$data = array();	
			}
			else
			{			
				$query = "select * from " . $this->_tablename . " where layout = ? order by blockid asc";
				$data = array($layout);
			}						
			$result = $db->fetchAll($query,$data);
			if(!is_null($result) && is_array($result))
			{
				$cache->setCache($key, $result);
			}					
		}
		return $result;
	}
	
	public function getBlock($blockid)
	{
		$db = $this->getDbConnection();
		$query = " SELECT * FROM zfw_blocks"
				." WHERE blockid = ?";
		$data = array($blockid);		
		$result = $db->fetchAll($query,$data);
		if($result != null && is_array($result))
		{
			return $result[0];				
		}
		else return null;
	}
	
	public function updateBlock($blockid, $data)
	{
		$db = $this->getDbConnection();
		$where = array();	
		$where[] = "blockid='" . parent::adaptSQL($blockid) . "'";
		try
		{			
			$result = $db->update($this->_tablename, $data, $where);
			//xoa cache
			$layout = $data['layout'];
			
			$cache = $this->getCacheInstance();
			$key = $this->getKeyList();
			$cache->deleteCache($key);
			$key = $this->getKeyListByLayout($layout);
			$cache->deleteCache($key);			
			$key = $this->getKeyListByLayoutActive($layout);
			$cache->deleteCache($key);
			
			$key = $this->getKeyListByLayout('');
			$cache->deleteCache($key);
			$key = $this->getKeyListByLayoutActive('');
			$cache->deleteCache($key);
			return $result; 
		}
		catch(Exception $e)
		{
			return 0;
		}
	}
	
	public function insertBlock($data)
	{
		$db = $this->getDbConnection();
		$result = $db->insert($this->_tablename,$data);
		$layout = $data["layout"];
		//xoa cache list
		$cache = $this->getCacheInstance();
		
		$key = $this->getKeyList();		
		$cache->deleteCache($key);
		$key = $this->getKeyListByLayout($layout);
		$cache->deleteCache($key);
		$key = $this->getKeyListByLayoutActive($layout);
		$cache->deleteCache($key);
		
		$key = $this->getKeyListByLayout('');
		$cache->deleteCache($key);
		$key = $this->getKeyListByLayoutActive('');
		$cache->deleteCache($key);
		return $result;		
	}
	
	public function deleteBlock($blockid)
	{
		$block = $this->getBlock($blockid);
		if($block == null) return;
		
		$layout = $block["layout"];
		
		$db = $this->getDbConnection();
		$where = array();
		$where[] = "blockid='" . parent::adaptSQL($blockid) . "'";
		$result = $db->delete($this->_tablename,$where);
		$key = $this->getKeyList();
		GlobalCache::deleteCache($key);
		$key = $this->getKeyListByLayout($layout);
		GlobalCache::deleteCache($key);
		$key = $this->getKeyListByLayoutActive($layout);
		GlobalCache::deleteCache($key);
		
		$key = $this->getKeyListByLayout('');
		GlobalCache::deleteCache($key);
		$key = $this->getKeyListByLayoutActive('');
		GlobalCache::deleteCache($key);
	}
}

?>
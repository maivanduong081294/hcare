<?php

class Business_Addon_DefaultValue extends Business_Abstract
{
	private $_tablename = 'addon_defaultvalue';

	const KEY_LIST_ALL = 'dv.list';			//key of list
	const KEY_DETAIL = 'dv.detail.%s';		//key of detail.id


	private static $_instance = null;

	function __construct()
	{
	}

	//public static function
	/**
	 * get instance of Business_Addon_Question
	 *
	 * @return Business_Addon_Question
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
		return sprintf(self::KEY_LIST_ALL);
	}

	public function getKeyDetail($id)
	{
		return sprintf(self::KEY_DETAIL,$id);
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
		$cache = GlobalCache::getCacheInstance('nt');
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
			$query = "SELECT * FROM " . $this->_tablename . " ";
			$result = $db->fetchAll($query);
			if(!is_null($result) && is_array($result))
			{
				$cache->setCache($key, $result);
			}
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
			$query = "SELECT * FROM " . $this->_tablename . " WHERE qid=?";
			$data = array($id);
			$result = $db->fetchAll($query,$data);
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
		$where[] = "qid='" . parent::adaptSQL($id) . "'";
		$db = $this->getDbConnection();
		$result = $db->update($this->_tablename, $data,$where);
		if($result > 0)
		{
			$this->_deleteAllCache($id);
		}
	}

	public function delete($id)
	{
		//get current menu
		$current = $this->getDetail($id);

		$where = array();
		$where[] = "qid='" . parent::adaptSQL($id) . "'";
		$db = $this->getDbConnection();
		$result = $db->delete($this->_tablename, $where);
		if($result > 0)
		{
			$this->_deleteAllCache($id);
		}
	}

	///private functions /////

	private function _deleteAllCache($id = null)
	{
		$cache = $this->getCacheInstance();
		$key = $this->getKeyList();
		$cache->deleteCache($key);
		if($id != null)
		{
			$key = $this->getKeyDetail($id);
			$cache->deleteCache($key);
		}
	}

}

?>
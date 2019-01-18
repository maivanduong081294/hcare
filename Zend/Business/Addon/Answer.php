<?php

class Business_Addon_Answer extends Business_Abstract
{
	private $_tablename = 'addon_answer';

	const KEY_LIST = 'anws.list.%s';			//key of list.questionid
	const KEY_DETAIL = 'anws.detail.%s';		//key of detail.id


	private static $_instance = null;

	function __construct()
	{
	}

	//public static function
	/**
	 * get instance of Business_Addon_Answer
	 *
	 * @return Business_Addon_Answer
	 */
	public static function getInstance()
	{
		if(self::$_instance == null)
		{
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	private function getKeyListByQuest($qid)
	{
		return sprintf(self::KEY_LIST,$qid);
	}

	private function getKeyDetail($id)
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

	

	public function getListByQuest($qid)
	{
		$cache = $this->getCacheInstance();
		$key = $this->getKeyListByQuest($qid);
		$result = $cache->getCache($key);
		if($result === FALSE)
		{
			$db = $this->getDbConnection();
			$query = "SELECT * FROM " . $this->_tablename . " WHERE qid=?";
			$data = array($qid);
			$result = $db->fetchAll($query,$data);
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
			$query = "SELECT * FROM " . $this->_tablename . " WHERE aid=?";
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

	public function insert($data,$qid)
	{
		$db = $this->getDbConnection();
		$result = $db->insert($this->_tablename, $data);
		if($result > 0)
		{
			$this->_deleteAllCache($qid);
			$last_id = $db->lastInsertId();
			return $last_id;
		}
		else return 0;
	}

	public function deleteExcept($qid,$except_ids)
	{
		$answers = $this->getListByQuest($qid);
		if($answers != null) {
			for($i=0;$i<count($answers);$i++) {
				if(!in_array($answers[$i], $except_ids)) {
					$this->delete($answers[$i], $qid);
				}
			}
		}
	}

	public function update($id,$qid, $data)
	{
		$where = array();
		$where[] = "aid='" . parent::adaptSQL($id) . "'";
		$db = $this->getDbConnection();
		$result = $db->update($this->_tablename, $data,$where);
		if($result > 0)
		{
			$this->_deleteAllCache($qid,$id);
		}
	}

	public function delete($id,$qid)
	{
		//get current menu
		$current = $this->getDetail($id);

		$where = array();
		$where[] = "aid='" . parent::adaptSQL($id) . "'";
		$wherep[] =
		$db = $this->getDbConnection();
		$result = $db->delete($this->_tablename, $where);
		if($result > 0)
		{
			$this->_deleteAllCache($qid,$id);
		}
	}

	///private functions /////

	private function _deleteAllCache($qid, $id = null)
	{
		$cache = $this->getCacheInstance();
		$key = $this->getKeyListByQuest($qid);
		$cache->deleteCache($key);
		if($id != null)
		{
			$key = $this->getKeyDetail($id);
			$cache->deleteCache($key);
		}
	}

}

?>

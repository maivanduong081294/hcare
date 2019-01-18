<?php

class Business_Addon_Soap extends Business_Abstract
{
	private $_tablename = 'addon_soap';

	const KEY_LIST = 'soap.list.%s';			//key of list.questionid
	const KEY_DETAIL = 'soap.detail.%s';		//key of detail.id


	private static $_instance = null;

	function __construct()
	{
	}

	//public static function
	/**
	 * get instance of Business_Addon_Soap
	 *
	 * @return Business_Addon_Soap
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
	

	public function insert($data)
	{
		$db = $this->getDbConnection();
		$result = $db->insert($this->_tablename, $data);
		if($result > 0)
		{
			$last_id = $db->lastInsertId();
			return $last_id;
		}
		else return 0;
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
        
        public function getDetail($userid, $info) {
            $info = strtolower($info);
            $db = $this->getDbConnection();
            $query = "SELECT * FROM " . $this->_tablename . " WHERE userid='$userid' AND LOWER(info)='$info'";
            $data = array($classid);
            $result = $db->fetchAll($query, $data);
            
            return $result[0];
        }
        
        public function getWinner($num) {
            $db = $this->getDbConnection();
            $query = "SELECT * FROM " . $this->_tablename . " LIMIT $num, 1";
                
            $data = array();
            $result = $db->fetchAll($query, $data);
                
            return $result[0];
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

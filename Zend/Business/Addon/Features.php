<?php

class Business_Addon_Features extends Business_Abstract
{
	private $_tablename = 'hn_features';

	const KEY_LIST = 'hn_features.list.%s';			//key of list.questionid
	const KEY_DETAIL = 'hn_features.detail.%s';		//key of detail.id


	private static $_instance = null;

	function __construct()
	{
	}

	//public static function
	/**
	 * get instance of Business_Addon_Features
	 *
	 * @return Business_Addon_Features
	 */
	public static function getInstance()
	{
		if(self::$_instance == null)
		{
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	private function getKeyList($pid)
	{
		return sprintf(self::KEY_LIST, $pid);
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

	public function getList(){
            $db = $this->getDbConnection();
            $query = "SELECT f.* FROM " . $this->_tablename . " f ";
            $result = $db->fetchAll($query);
            return $result;
        }

	public function getListByPid($pid, $show='')
	{
		$cache = $this->getCacheInstance();
		$key = $this->getKeyList($pid."+".$show);
//                $result=$cache->deleteCache($key);//exit();
		$result = $cache->getCache($key);
                $result=false;
		if($result === FALSE)
		{
//                    exit('dsa');
			$db = $this->getDbConnection();
//			$query = "SELECT f.* FROM " . $this->_tablename . " f ";
			$query = "SELECT f.*, fd.value, fd.pid FROM " . $this->_tablename . " f ";                       
                        if ($show != ''){
                            $query .= "JOIN hn_features_data fd ON f.fid = fd.fid AND fd.parentid = f.parentid  AND fd.pid = ? ";
                            $query .= "AND f.view >= 1 ";
                            $query .= "ORDER BY view ASC";
//                             pre($query);
                        }else{
                            $query .= "LEFT JOIN hn_features_data fd ON f.fid = fd.fid AND  fd.parentid = f.parentid   AND fd.pid = ? ";
                            $query .= "ORDER BY ordering ASC";
                        }
//                        var_dump($query);die(); 
                       
			$data = array($pid);
			$result = $db->fetchAll($query,$data);
//			if(!is_null($result) && is_array($result))
//			{
				$cache->setCache($key, $result);
//			}
		}
//                var_dump($result);exit();
		return $result;
	}

        
        public function update($fid, $item) {           
            $where = array();
            $where[] = "fid='" . parent::adaptSQL($fid) . "'";
            $db = $this->getDbConnection();
            $result = $db->update($this->_tablename, $item,$where);
            if($result > 0)
            {
                $this->_deleteAllCache($lang,$id);			
            }
        }
        
	///private functions /////
	private function _deleteAllCache()
	{
		$cache = $this->getCacheInstance();
		$key = $this->getKeyList();
		$cache->deleteCache($key);
	}

}

?>

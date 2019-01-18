<?php

class Business_Ws_RatingModule extends Business_Abstract
{
	private $_tablename = 'ws_rating';
	
	const KEY_LIST = 'rating.list.%';			//key of list by moduleid
	const KEY_LIST_ALL = 'rating.list.all.%s';		//key of list all by moduleid
	const KEY_COUNT = 'rating.count.%s';			//key of count by moduleid
        const KEY_COUNT_DETAIL = 'rating.count.%s.%s.%s.%s';			//key of count by moduleid
	const KEY_DETAIL = 'rating.detail.%s';	//key of detail.id
	
	//for paging
	CONST PAGING_NUM_RECORDS = 100;
	CONST PAGING_MAX_PAGE = 10;
	private $_paging = null;
        private $_paging_cache = null;
	const KEY_LIST_PAGING = 'rating.list.paging.%s';	//key of list by moduleid
        const KEY_LIST_PAGING_CATE = 'rating.list.paging.cate.%s.%s.%s.%s';	//key of list by moduleid, moduledetail, cateid
		
	private static $_instance = null; 
	
	function __construct()
	{			
	}
	
	//public static function
	/**
	 * get instance of Business_Ws_RatingModule
	 *
	 * @return Business_Ws_RatingModule
	 */
	public static function getInstance()
	{
		if(self::$_instance == null)
		{
			self::$_instance = new self();
		}
		return self::$_instance;
	}	
		
	private function getKeyList($moduleid)
	{
		return sprintf(self::KEY_LIST,$moduleid);
	}
		
	private function getKeyCount($moduleid)
	{
		return sprintf(self::KEY_COUNT,$moduleid);
	}

        private function getKeyCountDetail($moduleid, $moduledetailid, $cateid, $itemsid)
	{
		return sprintf(self::KEY_COUNT_DETAIL,$moduleid, $moduledetailid, $cateid, $itemsid);
	}
	
	private function getKeyListAll()
	{
		return sprintf(self::KEY_LIST_ALL);
	}	
		
	private function getKeyDetail($id)
	{
		return sprintf(self::KEY_DETAIL,$id);
	}
	
	private function getKeyListPaging($moduleid)
	{
		return sprintf(self::KEY_LIST_PAGING,$moduleid);
	}

        private function getKeyListPagingByCate($moduleid, $moduledetailid, $cateid, $itemsid)
	{
		return sprintf(self::KEY_LIST_PAGING_CATE,$moduleid, $moduledetailid, $cateid, $itemsid);
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
	
        public function getCateList()
        {
            $name = 'rating';
            $config = Globals::getConfig($name);
            return explode(",", $config->module->list);
        }
        public function getCateDetail($cate)
        {
            $list = $this->getCateList();
            return $list[$cate];
        }

        public function getTopRating($moduleid, $moduledetailid, $offset, $records)
        {
            $db = $this->getDbConnection();
            $query = "SELECT * FROM " . $this->_tablename . " WHERE moduleid=? AND moduledetailid=? ORDER BY ratingid asc LIMIT ";
            $query .= $offset . " , " . $records;
            $data = array($moduleid, $moduledetailid);
            $result = $db->fetchAll($query,$data);
            if(!is_null($result) && is_array($result))
            {
                return $result;
            }
            return array();
        }

        public function getTotalRatingByUid($moduleid, $moduledetailid, $cateid, $uid)
        {
            $db = $this->getDbConnection();
            $query = "SELECT count(*) as total FROM " . $this->_tablename . " WHERE moduleid = ? AND moduledetailid=? AND cateid=? AND uid=? ";
            $data = array($moduleid, $moduledetailid, $cateid, $uid);
            $result = $db->fetchAll($query,$data);
            if(!is_null($result) && is_array($result))
            {
                return $result[0]['total'];
            }
            return 0;
        }

        public function getListByUid($uid)
        {
            $db = $this->getDbConnection();
            $query = "SELECT * FROM " . $this->_tablename . " WHERE uid=? ";
            $data = array($uid);
            $result = $db->fetchAll($query,$data);
            if(!is_null($result) && is_array($result))
            {
                return $result;
            }
            return array();
        }

	public function getList($moduleid = 0)
	{
		$cache = $this->getCacheInstance();
		$key = $this->getKeyList($moduleid);
		$result = $cache->getCache($key);
		if($result === FALSE)
		{
			$db = $this->getDbConnection();
			$query = "SELECT * FROM " . $this->_tablename . " WHERE moduleid=?";
			$data = array($moduleid);
			$result = $db->fetchAll($query,$data);			
			if(!is_null($result) && is_array($result))
			{
				$cache->setCache($key, $result);
			}
		}
		return $result;
	}
	
	public function getListWithPaging($moduleid = 0, $offset, $records = 20)
	{
		$paging = $this->getPagingBusiness();
		$keyprefix = $this->getKeyListPaging($moduleid);
		$params = array(
			'moduleid' => $moduleid,
		);
		$result = $paging->getData($offset,$records,$keyprefix,$params);
		return $result;
	}
	
	public function getListAll()
	{
		$cache = $this->getCacheInstance();
		$key = $this->getKeyListAll();
		$result = $cache->getCache($key);
		if($result === FALSE)
		{
			$db = $this->getDbConnection();
			$query = "SELECT * FROM " . $this->_tablename ;
			$result = $db->fetchAll($query);			
			if(!is_null($result) && is_array($result))
			{
				$cache->setCache($key, $result);
			}
		}
		return $result;
	}
	
	public function _getRealData($moduleid, $page)
	{
		$offset = 0;
		$records = self::PAGING_NUM_RECORDS;

		if ($page == 0)
			$offset = 0;
		else
			$offset = $page * $records;
			
		$db = $this->getDbConnection();
		
		$query = "SELECT * FROM " . $this->_tablename . " WHERE moduleid=? LIMIT ";
		$query .= " $offset , $records ";
		$data = array($moduleid);
		$result = $db->fetchAll($query,$data);
		return $result;		
	}
	
	public function getCountByModule($moduleid=0)
	{
		$cache = $this->getCacheInstance();
		$key = $this->getKeyCount($moduleid);
		$result = $cache->getCache($key);
		if($result === FALSE)
		{
			$db = $this->getDbConnection();
			$query = "SELECT count(*) AS mysum FROM " . $this->_tablename . " WHERE moduleid=?";
			$data = array($moduleid);
			$result = $db->fetchAll($query,$data);
			if(!is_null($result) && is_array($result) && count($result) == 1)
			{
				$result = $result[0]['mysum'];				
			}
			else
			{
				$result = 0;				
			}
			$cache->setCache($key,$result);			
		}
		return $result;
	}

        public function getCountByModuleDetail($moduleid, $moduledetailid, $cateid, $itemsid)
	{
		$cache = $this->getCacheInstance();
		$key = $this->getKeyCountDetail($moduleid, $moduledetailid, $cateid, $itemsid);
		$result = $cache->getCache($key);
		if($result === FALSE)
		{
			$db = $this->getDbConnection();
			$query = "SELECT count(*) AS mysum FROM " . $this->_tablename . " WHERE moduleid=? AND moduledetailid=? AND cateid=? AND itemsid=? ";
			$data = array($moduleid, $moduledetailid, $cateid, $itemsid);
			$result = $db->fetchAll($query,$data);
			if(!is_null($result) && is_array($result) && count($result) == 1)
			{
				$result = $result[0]['mysum'];
			}
			else
			{
				$result = 0;
			}
			$cache->setCache($key,$result);
		}
		return $result;
	}

        public function getCountByModuleDetailPart2($moduleid, $moduledetailid, $cateid, $itemsid)
	{
		$cache = $this->getCacheInstance();
		$key = $this->getKeyCountDetail($moduleid, $moduledetailid, $cateid, $itemsid);
		$result = $cache->getCache($key);
		if($result === FALSE)
		{
			$db = $this->getDbConnection();
			$query = "SELECT count(*) AS mysum FROM " . $this->_tablename . " WHERE moduleid=? AND moduledetailid=? AND cateid=? AND itemsid=? AND last_rating_time >= '2010-09-14' ";
			$data = array($moduleid, $moduledetailid, $cateid, $itemsid);
			$result = $db->fetchAll($query,$data);
			if(!is_null($result) && is_array($result) && count($result) == 1)
			{
				$result = $result[0]['mysum'];
			}
			else
			{
				$result = 0;
			}
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
			$query = "SELECT * FROM " .$this->_tablename . " WHERE ratingid='" . parent::adaptSQL($id) . "'";
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
			$this->_deleteAllCache($data);
		}
	}
	
	public function update($ratingid, $data)
	{
		if(array_key_exists('ratingid',$data)) unset($data['ratingid']);
		$where = array();
		$where[] = "ratingid='" . parent::adaptSQL($ratingid) . "'";
		$db = $this->getDbConnection();
		$result = $db->update($this->_tablename, $data,$where);
		if($result > 0)
		{
			$this->_deleteAllCache($data, $ratingid);
		}
	}
	
	public function delete($ratingid)
	{
		//get current menu
		$current = $this->getDetail($rating);
				
		$where = array();
		$where[] = "ratingid='" . parent::adaptSQL($ratingid) . "'";
		$db = $this->getDbConnection();
		$result = $db->delete($this->_tablename, $where);
		if($result > 0)
		{
			$this->_deleteAllCache($current, $ratingid);
		}
	}
	
	///private functions /////

	private function _update($id, $data)
	{
	
		$where = array();
		$where[] = "ratingid='" . parent::adaptSQL($id) . "'";
		$db = $this->getDbConnection();
		$result = $db->update($this->_tablename, $data,$where);
		return $result;
	}
	
		
	private function _deleteAllCache($data, $ratingid = null)
	{
		$cache = $this->getCacheInstance();
		$key = $this->getKeyList($data['moduleid']);
		$cache->deleteCache($key);
		$key = $this->getKeyCount($data['moduleid']);
		$cache->deleteCache($key);
		$key = $this->getKeyListAll();
		$cache->deleteCache($key);
                $key = $this->getKeyCountDetail($data['moduleid'], $data['moduledetailid'], $data['cateid'], $data['itemsid']);
                $cache->deleteCache($key);
                
		if($ratingid != null)
		{
			$key = $this->getKeyDetail($ratingid);
			$cache->deleteCache($key);
		}
		// clean paging cache
		$key = $this->getKeyListPaging($data['moduleid']);
		$paging = $this->getPagingBusiness();
		$paging->clearCachePaging($key);
                $key = $this->getKeyListPagingByCate($data['moduleid'], $data['moduledetailid'], $data['cateid'], $data['itemsid']);
                $paging->clearCachePaging($key);
	}
	
	/**
	 * Enter description here...
	 *
	 * @return Maro_Paging_Interface
	 */
	private function getPagingBusiness()
	{
		if($this->_paging != null) return $this->_paging;
			
		$cache = $this->getCacheInstance();

		$this->_paging = new Maro_Paging_Common($cache, array($this, "_getRealData"), self::PAGING_MAX_PAGE, self::PAGING_NUM_RECORDS);
		
		return $this->_paging;
	}

        	/**
	 * Enter description here...
	 *
	 * @return Maro_Paging_Interface
	 */
	private function getPagingBusinessByCate()
	{
		if($this->_paging_cache != null) return $this->_paging_cache;

		$cache = $this->getCacheInstance();

		$this->_paging_cache = new Maro_Paging_Common($cache, array($this, "_getRealDataByCate"), self::PAGING_MAX_PAGE, self::PAGING_NUM_RECORDS);

		return $this->_paging_cache;
	}

        public function _getRealDataByCate($moduleid, $moduledetailid, $cateid, $itemsid, $page)
	{
		$offset = 0;
		$records = self::PAGING_NUM_RECORDS;

		if ($page == 0)
			$offset = 0;
		else
			$offset = $page * $records;

		$db = $this->getDbConnection();

		$query = "SELECT * FROM " . $this->_tablename . " WHERE moduleid=? AND moduledetailid=? AND cateid=? AND itemsid=? LIMIT ";
		$query .= " $offset , $records ";
		$data = array($moduleid, $moduledetailid, $cateid, $itemsid);
		$result = $db->fetchAll($query,$data);
		return $result;
	}

        public function getListWithPagingByCate($moduleid = 0, $moduledetailid = 0, $cateid = 0, $itemsid=0, $offset, $records = 20)
	{
            $paging = $this->getPagingBusinessByCate();
            $keyprefix = $this->getKeyListPagingByCate($moduleid, $moduledetailid, $cateid, $itemsid);
            $params = array(
                'moduleid' => $moduleid,
                'moduledetailid' => $moduledetailid,
                'cateid' => $cateid,
                'itemsid' => $itemsid
            );
            $result = $paging->getData($offset,$records,$keyprefix,$params);
            return $result;
	}
}
?>
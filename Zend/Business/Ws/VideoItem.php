<?php

class Business_Ws_VideoItem extends Business_Abstract
{
	private $_tablename = 'ws_videoitem';
	
	const KEY_LIST = 'videoitem.list.%s.%s';			//key of list by videoid,cateid
	const KEY_LIST_HOT = 'videoitem.listhot.%s.%s';	//key of list hot by videoid,cateid
	const KEY_LIST_ALL = 'videoitem.list.all.%s.%s';		//key of list all by videoid,cateid
	const KEY_LIST_ALL_HOT = 'videoitem.listhost.%s';		//key of list all by videoid
	const KEY_COUNT = 'videoitem.count.%s.%s';			//key of count by videoid,cateid
	const KEY_DETAIL = 'videoitem.detail.%s';	//key of detail.id
	
	//for paging
	CONST PAGING_NUM_RECORDS = 100;
	CONST PAGING_MAX_PAGE = 10;
	private $_paging = null;
	const KEY_LIST_PAGING = 'videoitem.list.paging.%s.%s';	//key of list by videoid, cateid with paging
		
	private static $_instance = null; 
	
	function __construct()
	{			
	}
	
	//public static function
	/**
	 * get instance of Business_Ws_VideoItem
	 *
	 * @return Business_Ws_VideoItem
	 */
	public static function getInstance()
	{
		if(self::$_instance == null)
		{
			self::$_instance = new self();
		}
		return self::$_instance;
	}	
		
	private function getKeyList($videoid,$cateid)
	{
		return sprintf(self::KEY_LIST,$videoid,$cateid);
	}
	
	private function getKeyListHot($videoid,$cateid)
	{
		return sprintf(self::KEY_LIST_HOT,$videoid,$cateid);
	}
	
	private function getKeyCount($videoid,$cateid)
	{
		return sprintf(self::KEY_COUNT,$videoid,$cateid);
	}
	
	private function getKeyListAll($videoid)
	{
		return sprintf(self::KEY_LIST_ALL_HOT,$videoid);
	}	
	
	private function getKeyListHotAll($videoid, $cateid)
	{
		return sprintf(self::KEY_LIST_ALL,$videoid, $cateid);
	}
		
	private function getKeyDetail($id)
	{
		return sprintf(self::KEY_DETAIL,$id);
	}
	
	private function getKeyListPaging($videoid,$cateid)
	{
		return sprintf(self::KEY_LIST_PAGING,$videoid,$cateid);
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

        public function getListByViewWithPaging($videoid, $cateid, $offset, $records, $part = 1)
        {
            $db = $this->getDbConnection();
            if ($part == 2)
                $query = "SELECT * FROM " . $this->_tablename . " WHERE  ishot <> 1 and videoid=? AND cateid=? AND itemid in (129,112,94,88,87,86,81,79,77,75,71,62,55,54,53) ORDER BY hits desc LIMIT ";
            else
                $query = "SELECT * FROM " . $this->_tablename . " WHERE  ishot <> 1 and videoid=? AND cateid=? AND itemid not in (129,112,94,88,87,86,81,79,77,75,71,62,55,54,53) ORDER BY hits desc LIMIT ";
            $query .= " $offset , $records ";
            $data = array($videoid,$cateid);
            $result = $db->fetchAll($query,$data);
            if (count($result)>0)
                return $result;
            else
                return array();
        }

        public function getListByRatingWithPaging($videoid, $cateid, $offset, $records, $part = 1)
        {
            
            $db = $this->getDbConnection();
            if ($part == 2)
            {
                $query = "select count(ws_rating.like) as total, ws_videoitem.*
                FROM ws_videoitem LEFT JOIN ws_rating ON ws_rating.last_rating_time >= '2010-09-14' AND ws_videoitem.itemid = ws_rating.itemsid AND ws_videoitem.videoid = ? AND ws_videoitem.cateid=? AND ws_rating.like = 1 AND ws_videoitem.itemid in (129,112,94,88,87,86,81,79,77,75,71,62,55,54,53)
                GROUP BY ws_rating.itemsid
                ORDER BY total desc LIMIT ";             
            }
            else
                $query = "select count(ws_rating.like) as total, ws_videoitem.*
                FROM ws_videoitem LEFT JOIN ws_rating ON ws_videoitem.itemid = ws_rating.itemsid AND ws_videoitem.videoid = ? AND ws_videoitem.cateid=? AND ws_rating.like = 1 AND ws_videoitem.itemid not in (129,112,94,88,87,86,81,79,77,75,71,62,55,54,53) 

                GROUP BY ws_rating.itemsid
                ORDER BY total desc LIMIT ";
            $query .= " $offset , $records ";
            $data = array($videoid,$cateid);
            $result = $db->fetchAll($query,$data);
            if (count($result)>0)
                return $result;
            else
                return array();
        }


        public function getListByTitle($videoid, $cateid, $title)
        {
            $db = $this->getDbConnection();

            $query = "SELECT * FROM " . $this->_tablename . " WHERE  ishot <> 1 and videoid=? AND cateid=? AND title like '%".$title."%' ORDER BY myorder,itemid desc";
            $data = array($videoid,$cateid);
            $result = $db->fetchAll($query,$data);
            return $result;
        }

        

	public function getList($videoid = 0, $cateid = 0)
	{
		$cache = $this->getCacheInstance();
		$key = $this->getKeyList($videoid,$cateid);
		$result = $cache->getCache($key);
		if($result === FALSE)
		{
			$db = $this->getDbConnection();
			$query = "SELECT * FROM " . $this->_tablename . " WHERE videoid=? AND cateid=? ORDER BY myorder,title desc";
			$data = array($videoid,$cateid);
			$result = $db->fetchAll($query,$data);			
			if(!is_null($result) && is_array($result))
			{
				$cache->setCache($key, $result);
			}
		}
		return $result;
	}
	
	public function getListWithPaging($videoid, $cateid = 0, $offset, $records = 20, $part = 1)
	{
                if ($part == 1)
                {
                    $paging = $this->getPagingBusiness();
                    $keyprefix = $this->getKeyListPaging($videoid,$cateid);
                    $params = array(
                            'videoid' => $videoid,
                            'cateid' => $cateid
                    );
                    $result = $paging->getData($offset,$records,$keyprefix,$params);
                    return $result;
                }
                elseif($part == 2)
                {
                    return $this->_getRealData2($videoid, $cateid, $offset, $records = 20);
                }

	}
	
	public function getListAll($videoid = 0)
	{
		$cache = $this->getCacheInstance();
		$key = $this->getKeyListAll($videoid);
		$result = $cache->getCache($key);
		if($result === FALSE)
		{
			$db = $this->getDbConnection();
			$query = "SELECT * FROM " . $this->_tablename . " WHERE videoid=? ORDER BY myorder,itemid desc";
			$data = array($videoid);
			$result = $db->fetchAll($query,$data);			
			if(!is_null($result) && is_array($result))
			{
				$cache->setCache($key, $result);
			}
		}
		return $result;
	}
	
	public function _getRealData($videoid, $cateid, $page)
	{
		$offset = 0;
		$records = self::PAGING_NUM_RECORDS;

		if ($page == 0)
			$offset = 0;
		else
			$offset = $page * $records;
			
		$db = $this->getDbConnection();
		
		$query = "SELECT * FROM " . $this->_tablename . " WHERE  ishot <> 1 and videoid=? AND cateid=? AND itemid not in (129,112,94,88,87,86,81,79,77,75,71,62,55,54,53) ORDER BY myorder,itemid desc LIMIT ";
		$query .= " $offset , $records ";
		$data = array($videoid,$cateid);
		$result = $db->fetchAll($query,$data);
		return $result;		
	}

        public function _getRealData2($videoid, $cateid, $offset, $records)
	{
		$db = $this->getDbConnection();

		$query = "SELECT * FROM " . $this->_tablename . " WHERE  ishot <> 1 and videoid=? AND cateid=? AND itemid in (129,112,94,88,87,86,81,79,77,75,71,62,55,54,53) ORDER BY myorder,itemid desc LIMIT ";
		$query .= " $offset , $records ";
		$data = array($videoid,$cateid);
		$result = $db->fetchAll($query,$data);
		return $result;
	}
	
	public function getHotList($videoid, $cateid)
	{
		$cache = $this->getCacheInstance();
		$key = $this->getKeyListHotAll($videoid,$cateid);
		$result = $cache->getCache($key);
		if($result === FALSE)
		{
			$db = $this->getDbConnection();
			$query = "SELECT * FROM " . $this->_tablename . " WHERE videoid=? and cateid=? and ishot=1 ORDER BY myorder,itemid desc";
			$data = array($videoid,$cateid);
			$result = $db->fetchAll($query,$data);			
			if(!is_null($result) && is_array($result))
			{
				$cache->setCache($key, $result);
			}
		}
		return $result;
	}
		
	public function getCountByCate($videoid=0,$cateid=0)
	{
		$cache = $this->getCacheInstance();
		$key = $this->getKeyCount($videoid,$cateid);
		$result = $cache->getCache($key);
		if($result === FALSE)
		{
			$db = $this->getDbConnection();
			$query = "SELECT count(*) AS mysum FROM " . $this->_tablename . " WHERE videoid=? AND cateid=?";
			$data = array($videoid,$cateid);
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
			$query = "SELECT * FROM " .$this->_tablename . " WHERE itemid='" . parent::adaptSQL($id) . "'";
			$result = $db->fetchAll($query);
			if($result != null && is_array($result))
			{
				$result = $result[0];
				$cache->setCache($key, $result);				
			}
		}
		return $result;
	}	
	
	public function insert($videoid, $cateid, $data)
	{
		$data['videoid'] = $videoid;
		$data['cateid'] = $cateid;
		$db = $this->getDbConnection();
		$result = $db->insert($this->_tablename, $data);
		if($result > 0)
		{
			$this->_deleteAllCache($videoid,$cateid);
		}
                return $db->lastInsertId();
	}
	
	public function moveCate($id,$videoid,$cateid,$cateid_new)
	{
		$data = array(
			'cateid' => $cateid_new
		);
		$result = $this->_update($id,$data);
		if($result > 0)
		{
			$this->_deleteAllCache($videoid,$cateid);
			$this->_deleteAllCache($videoid,$cateid_new,$id);
		}
	}

        public function hits($id, $videoid, $cateid)
	{
            
                $db = $this->getDbConnection();

                $sql = 'update '.$this->_tablename.' '.'set hits = hits + 1 where itemid = '.$id.' and videoid = '.$videoid.' and cateid = '.$cateid;

                $db->query($sql);
                
                $this->_deleteAllCache($videoid,$cateid,$id);
	}
	
	public function update($id, $videoid, $cateid, $data)
	{
		if(array_key_exists('videoid',$data)) unset($data['videoid']);
		if(array_key_exists('cateid',$data)) unset($data['cateid']);
		$where = array();
		$where[] = "itemid='" . parent::adaptSQL($id) . "'";
		$db = $this->getDbConnection();
		$result = $db->update($this->_tablename, $data,$where);
		if($result > 0)
		{
			$this->_deleteAllCache($videoid,$cateid,$id);
		}
	}
	
	public function delete($id,$videoid,$cateid)
	{
		//get current menu
		$current = $this->getDetail($id);
				
		$where = array();
		$where[] = "itemid='" . parent::adaptSQL($id) . "'";
		$db = $this->getDbConnection();
		$result = $db->delete($this->_tablename, $where);
		if($result > 0)
		{
			$this->_deleteAllCache($videoid,$cateid,$id);
		}
	}
	
	///private functions /////

	private function _update($id, $data)
	{
	
		$where = array();
		$where[] = "itemid='" . parent::adaptSQL($id) . "'";
		$db = $this->getDbConnection();
		$result = $db->update($this->_tablename, $data,$where);
		return $result;
	}
	
		
	private function _deleteAllCache($videoid,$cateid,$id = null)
	{
		$cache = $this->getCacheInstance();
		$key = $this->getKeyList($videoid,$cateid);
		$cache->deleteCache($key);
		$key = $this->getKeyCount($videoid,$cateid);
		$cache->deleteCache($key);
		$key = $this->getKeyListAll($videoid);
		$cache->deleteCache($key);
		$key = $this->getKeyListHotAll($videoid,$cateid);
		$cache->deleteCache($key);
		if($id != null)
		{
			$key = $this->getKeyDetail($id);
			$cache->deleteCache($key);
		}
		
		// clean paging cache
		$key = $this->getKeyListPaging($videoid,$cateid);
		$paging = $this->getPagingBusiness();
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
	
	
}
?>
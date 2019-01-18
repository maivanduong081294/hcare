<?php

class Business_Ws_CommentsModule extends Business_Abstract
{
	private $_tablename = 'ws_comments';
	
	const KEY_LIST = 'comments.list.%s';			//key of list by moduleid
        const KEY_LIST_PARENTS = 'comments.parent.list.%s.%s';			//key of list by moduleid
	const KEY_LIST_ALL = 'comments.list.all';		//key of list all by moduleid
	const KEY_COUNT = 'comments.count.%s.%s';			//key of count by moduleid
        const KEY_COUNT_IDP = 'comments.count.idp.%s.%s.%s.%s.%s';			//key of count by moduleid
	const KEY_DETAIL = 'comments.detail.%s';	//key of detail.id
	
	//for paging
	CONST PAGING_NUM_RECORDS = 100;
	CONST PAGING_MAX_PAGE = 10;
	private $_paging = null;
        private $_paging_idp = null;
	const KEY_LIST_PAGING = 'comments.list.paging.%s.%s';	//key of list by moduleid
        const KEY_LIST_PAGING_BY_ITEM_DEPTH_PUBLISHED = 'comments.list.paging.idp.%s.%s.%s.%s.%s'; // key of list by moduleid, itemsid, depth, published
        
	private static $_instance = null; 
	
	function __construct()
	{			
	}
	
	//public static function
	/**
	 * get instance of Business_Ws_CommentsModule
	 *
	 * @return Business_Ws_CommentsModule
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

        private function getKeyListByParentid($moduleid, $parentid)
	{
		return sprintf(self::KEY_LIST_PARENTS,$moduleid,$commentsid);
	}

        private function getKeyListWithPagingByItemDepthPublished($moduleid, $moduledetailid, $cateid, $itemsid, $depth)
	{
		return sprintf(self::KEY_LIST_PAGING_BY_ITEM_DEPTH_PUBLISHED,$moduleid, $moduledetailid, $cateid, $itemsid, $depth);
	}
        
	private function getKeyCount($moduleid, $depth = 1)
	{
		return sprintf(self::KEY_COUNT,$moduleid, $depth);
	}

        private function getKeyCountIDP($moduleid, $moduledetailid, $cateid, $itemsid, $depth = 1)
	{
		return sprintf(self::KEY_COUNT_IDP,$moduleid, $moduledetailid, $cateid, $itemsid, $depth);
	}
	
	private function getKeyListAll()
	{
		return sprintf(self::KEY_LIST_ALL);
	}	
		
	private function getKeyDetail($id)
	{
		return sprintf(self::KEY_DETAIL,$id);
	}
	
	private function getKeyListPaging($moduleid, $depth = 1)
	{
		return sprintf(self::KEY_LIST_PAGING,$moduleid, $depth);
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

        public function getTotalListByItemid($itemid){
            $db = $this->getDbConnection();
            $query = "SELECT count(*) as total FROM " . $this->_tablename . " WHERE itemsid=? AND published = 1 ";
            $data = array($itemid);
            $result = $db->fetchAll($query,$data);
            $result = (int)$result[0]['total'];
            return $result;
        }

        public function getListByItemid($itemid, $offset='', $records='', $published = ''){
            $db = $this->getDbConnection();
            if ($published === '')
                $query = "SELECT * FROM " . $this->_tablename . " WHERE itemsid=? ORDER BY datetime desc";
            else
                $query = "SELECT * FROM " . $this->_tablename . " WHERE itemsid=? AND published = 1 ORDER BY datetime desc";
            if ($offset !== '' && $records != '')
                $query .= " LIMIT $offset, $records";
            $data = array($itemid);
            $result = $db->fetchAll($query,$data);
            return $result;
        }
        
        public function getDetailByItemsid($itemid)
        {
            $db = $this->getDbConnection();
            $query = "SELECT * FROM " . $this->_tablename . " WHERE itemsid=? ";
            $data = array($itemid);
            $result = $db->fetchAll($query,$data);
            return $result[0];
        }

        public function getTotalCommentsByUid($moduleid, $moduledetailid, $cateid, $uid)
        {
            $db = $this->getDbConnection();
            $query = "SELECT count(*) as total FROM " . $this->_tablename . " WHERE moduleid=? AND moduledetailid=? AND cateid=? AND uid=? AND published = 1 ";
            $data = array($moduleid, $moduledetailid, $cateid, $uid);
            $result = $db->fetchAll($query,$data);
            if(!is_null($result) && is_array($result) && count($result) == 1)
            {
                    $result = $result[0]['total'];
            }
            return $result;
        }

        public function getListByUid($uid){
            $db = $this->getDbConnection();
            $query = "SELECT * FROM " . $this->_tablename . " WHERE uid=? ";
            $data = array($uid);
            $result = $db->fetchAll($query,$data);
            if(!is_null($result) && is_array($result) && count($result) == 1)
            {
                    $result = $result;
            }
            return array();
        }


        public function getCountByModule($moduleid=0, $depth = 1)
	{
		$cache = $this->getCacheInstance();
		$key = $this->getKeyCount($moduleid, $depth);
		$result = $cache->getCache($key);
		if($result === FALSE)
		{
			$db = $this->getDbConnection();
			$query = "SELECT count(*) AS mysum FROM " . $this->_tablename . " WHERE moduleid=? AND depth=?";
			$data = array($moduleid, $depth);
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

        public function getCateList()
        {
            $name = 'comments';
            $config = Globals::getConfig($name);
            return explode(",", $config->module->list);
        }
        public function getCateDetail($cate)
        {
            $list = $this->getCateList();
            return $list[$cate];
        }

	public function getList($moduleid = 0)
	{
            $cache = $this->getCacheInstance();
            $key = $this->getKeyList($moduleid);
            $result = $cache->getCache($key);
            if($result === FALSE)
            {
                $db = $this->getDbConnection();
                $query = "SELECT * FROM " . $this->_tablename . " WHERE moduleid=? order by datetime asc ";
                $data = array($moduleid);
                $result = $db->fetchAll($query,$data);
                if(!is_null($result) && is_array($result))
                {
                    $cache->setCache($key, $result);
                }
            }
            return $result;
	}



	public function getListWithPaging($moduleid = 0, $depth = 1, $offset, $records = 20)
	{

		$paging = $this->getPagingBusiness();
		$keyprefix = $this->getKeyListPaging($moduleid, $depth);
		$params = array(
                    'moduleid' => $moduleid,
                    'depth' => $depth
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

	public function _getRealData($moduleid, $depth = 1, $page)
	{
		$offset = 0;
		$records = self::PAGING_NUM_RECORDS;

		if ($page == 0)
			$offset = 0;
		else
			$offset = $page * $records;
			
		$db = $this->getDbConnection();
		
		$query = "SELECT * FROM " . $this->_tablename . " WHERE moduleid=? AND depth=? ORDER BY datetime asc LIMIT ";
		$query .= " $offset , $records ";
		$data = array($moduleid, $depth);
		$result = $db->fetchAll($query,$data);
                
		return $result;
	}
	

        public function getCountByModuleIDP($moduleid, $moduledetailid, $cateid, $itemsid, $depth = 1)
	{

		$cache = $this->getCacheInstance();
		$key = $this->getKeyCountIDP($moduleid, $moduledetailid, $cateid, $itemsid, $depth);
		$result = $cache->getCache($key);
		if($result === FALSE)
		{
			$db = $this->getDbConnection();
			$query = "SELECT count(*) AS mysum FROM " . $this->_tablename . " WHERE moduleid=? AND moduledetailid=? AND cateid=? AND itemsid=? AND depth=? ORDER BY datetime asc";
			$data = array($moduleid, $moduledetailid, $cateid, $itemsid, $depth);
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
			$query = "SELECT * FROM " .$this->_tablename . " WHERE commentsid='" . parent::adaptSQL($id) . "'";
			$result = $db->fetchAll($query);
			if($result != null && is_array($result))
			{
				$result = $result[0];
				$cache->setCache($key, $result);				
			}
		}
		return $result;
	}	
	
	public function insert($commentsid = null, $moduleid, $data)
	{
//            $data['commentsid'] = $commentsid;
            $db = $this->getDbConnection();
            $result = $db->insert($this->_tablename, $data);
            $this->_deleteAllCache($moduleid, $commentsid, $data);
	}
	
	public function update($id, $moduleid, $data)
	{
            
            if(array_key_exists('commentsid',$data)) unset($data['commentsid']);
            $where = array();
            $where[] = "commentsid='" . parent::adaptSQL($id) . "'";
            $db = $this->getDbConnection();
            $result = $db->update($this->_tablename, $data,$where);
            if($result > 0)
            {
                    $this->_deleteAllCache($moduleid,$id, $data);
            }
	}
	
	public function delete($id,$moduleid)
	{
            //get current menu
            $current = $this->getDetail($id);

            $where = array();
            $where[] = "commentsid='" . parent::adaptSQL($id) . "'";
            $db = $this->getDbConnection();
            $result = $db->delete($this->_tablename, $where);
            if($result > 0)
            {
                    $this->_deleteAllCache($moduleid,$id,$current);
            }
	}
	
	///private functions /////

	private function _update($id, $data)
	{
	
            $where = array();
            $where[] = "commentsid='" . parent::adaptSQL($id) . "'";
            $db = $this->getDbConnection();
            $result = $db->update($this->_tablename, $data,$where);
            return $result;
	}
	
		
	private function _deleteAllCache($moduleid,$id = null,$current = array())
	{
            $cache = $this->getCacheInstance();
            $key = $this->getKeyList($moduleid);
            $cache->deleteCache($key);
            $key = $this->getKeyCount($moduleid);
            $cache->deleteCache($key);
            $key = $this->getKeyListAll();
            $cache->deleteCache($key);
            $key = $this->getKeyCountIDP($moduleid, $current['moduledetailid'], $current['cateid'], $current['itemsid'], $current['depth']);
            $cache->deleteCache($key);
            $paging = $this->getPagingBusinessByIDP();
//                private function getKeyListWithPagingByItemDepthPublished($moduleid, $moduledetailid, $cateid, $itemsid, $depth, $published)
            $key = $this->getKeyListWithPagingByItemDepthPublished($moduleid, $current['moduledetailid'], $current['cateid'], $current['itemsid'], $current['depth'], 1);
            $paging->clearCachePaging($key);

            $key = $this->getKeyListWithPagingByItemDepthPublished($moduleid, $current['moduledetailid'], $current['cateid'], $current['itemsid'], $current['depth'], 0);
            $paging->clearCachePaging($key);

            if($id != null)
            {
                $paging = $this->getPagingBusinessByIDP();
//                private function getKeyListWithPagingByItemDepthPublished($moduleid, $moduledetailid, $cateid, $itemsid, $depth, $published)
                $key = $this->getKeyListWithPagingByItemDepthPublished($moduleid, $current['moduledetailid'], $current['cateid'], $current['itemsid'], $current['depth'], $current['published']);
                $paging->clearCachePaging($key);

                $key = $this->getKeyDetail($id);
                $cache->deleteCache($key);

                // clean paging cache
                $_paging = $this->getPagingBusiness();
                $key = $this->getKeyListPaging($moduleid, $current['depth']);
                $_paging->clearCachePaging($key);


                $key = $this->getKeyListByParentid($moduleid, $current['parentid']);
                $cache->deleteCache($key);
                
            }
            
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
	private function getPagingBusinessByIDP()
	{
            if($this->_paging_idp != null) return $this->_paging_idp;

            $cache = $this->getCacheInstance();

            $this->_paging_idp = new Maro_Paging_Common($cache, array($this, "_getRealDataByIDP"), self::PAGING_MAX_PAGE, self::PAGING_NUM_RECORDS);

            return $this->_paging_idp;
	}

        public function _getRealDataByIDP($moduleid, $moduledetailid, $cateid, $itemsid, $depth, $page)
	{
         
            $offset = 0;
            $records = self::PAGING_NUM_RECORDS;

            if ($page == 0)
                    $offset = 0;
            else
                    $offset = $page * $records;

            $db = $this->getDbConnection();

            $query = "SELECT * FROM " . $this->_tablename . " WHERE moduleid=? AND moduledetailid=? AND cateid=? AND itemsid=? AND depth=? AND published = 1 ORDER BY datetime asc LIMIT ";
            
            $query .= " $offset , $records ";

            $data = array($moduleid, $moduledetailid, $cateid, $itemsid, $depth);
            
            $result = $db->fetchAll($query,$data);
            
            return $result;
	}

        public function getListWithPagingByIDP($moduleid=0, $moduledetailid = 0, $cateid = 0, $itemsid, $depth = 1, $offset, $records = 20, $published = 0)
	{           
            $paging = $this->getPagingBusinessByIDP();
            $keyprefix = $this->getKeyListWithPagingByItemDepthPublished($moduleid, $moduledetailid, $cateid, $itemsid, $depth, $published);
            
            $params = array(
                'moduleid' => $moduleid,
                'moduledetailid' => $moduledetailid,
                'cateid' => $cateid,
                'itemsid' => $itemsid,
                'depth' => $depth
            );
            $result = $paging->getData($offset,$records,$keyprefix,$params);
            return $result;
	}

        public function getListWithPagingByIDP2($moduleid=0, $moduledetailid = 0, $cateid = 0, $itemsid, $depth = 1, $offset, $records = 20, $published = 0)
	{
            $db = $this->getDbConnection();

            $query = "SELECT * FROM " . $this->_tablename . " WHERE moduleid=? AND moduledetailid=? AND cateid=? AND itemsid=? AND depth=? ORDER BY datetime asc LIMIT ";

            $query .= " $offset , $records ";

            $data = array($moduleid, $moduledetailid, $cateid, $itemsid, $depth);

            $result = $db->fetchAll($query,$data);

            return $result;
	}



        public function getListByParentid($moduleid =0, $parentid = 0)
	{
            $cache = $this->getCacheInstance();
            $key = $this->getKeyListByParentid($moduleid,$parentid);
            $result = $cache->getCache($key);

            if($result === FALSE)
            {
                    $db = $this->getDbConnection();
                    $query = "SELECT * FROM " . $this->_tablename . " WHERE moduleid=? AND parentid=? ORDER BY datetime asc ";
                    $data = array($moduleid, $parentid);
                    $result = $db->fetchAll($query, $data);
                    if($result != null && is_array($result))
                    {
                            $cache->setCache($key, $result);
                    }
            }
            return $result;
	}

}
?>
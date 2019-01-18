<?php
class Business_Ws_NewsItem extends Business_Abstract
{
	private $_tablename = 'ws_newsitem';
        
        private $_filters = array('infront','ishot','highlight');
        
        const EXPIRED = 3000; //secs
        
	const KEY_LIST = 'newsitem.list.%s.%s';			//key of list by newsid,cateid
	const KEY_LIST_HOT = 'newsitem.listhot.%s.%s';	//key of list hot by newsid,cateid
	const KEY_LIST_ALL = 'newsitem.list.all.%s.%s';		//key of list all by newsid,cateid
	const KEY_LIST_ALL_HOT = 'newsitem.listhost.%s';		//key of list all by newsid
	const KEY_COUNT = 'newsitem.count.%s.%s';			//key of count by newsid,cateid	
	const KEY_DETAIL = 'newsitem.detail.%s';	//key of detail.id
	const KEY_DETAIL_TITLE = 'newsitem.detail.title.%s';	//key of detail.title
	
	//for paging
	CONST PAGING_NUM_RECORDS = 100;
	CONST PAGING_MAX_PAGE = 10;
	private $_paging = null;
	const KEY_LIST_PAGING = 'newsitem.list.paging.%s.%s';	//key of list by newsid, cateid with paging
		
	private static $_instance = null; 
	
	function __construct()
	{			
	}
	
	//public static function
	/**
	 * get instance of Business_Ws_NewsItem
	 *
	 * @return Business_Ws_NewsItem
	 */
	public static function getInstance()
	{
		if(self::$_instance == null)
		{
			self::$_instance = new self();
		}
		return self::$_instance;
	}	
		
	private function getKeyList($newsid,$cateid)
	{
		return sprintf(self::KEY_LIST,$newsid,$cateid);
	}
	
	private function getKeyListHot($newsid,$cateid)
	{
		return sprintf(self::KEY_LIST_HOT,$newsid,$cateid);
	}
	
	private function getKeyCount($newsid,$cateid)
	{
		return sprintf(self::KEY_COUNT,$newsid,$cateid);
	}
	
	private function getKeyListAll($newsid)
	{
		return sprintf(self::KEY_LIST_ALL_HOT,$newsid);
	}	
	
	private function getKeyListHotAll($newsid, $cateid)
	{
		return sprintf(self::KEY_LIST_ALL,$newsid, $cateid);
	}
		
	private function getKeyDetail($id)
	{
		return sprintf(self::KEY_DETAIL,$id);
	}

	private function getKeyDetailTitle($title)
	{
		return sprintf(self::KEY_DETAIL_TITLE,$title);
	}
	
	private function getKeyListPaging($newsid,$cateid)
	{
		return sprintf(self::KEY_LIST_PAGING,$newsid,$cateid);
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
		$cache = GlobalCache::getCacheInstance('event');                
		return $cache;
	}
        public function getListByItemid($itemid){
            $cache = $this->getCacheInstance();
            $key = "getListByItemid".$this->_tablename.$itemid;
            $result = $cache->getCache($key);
            if($result ===FALSE){
                $db= $this->getDbConnection();
                $query= "SELECT itemid,title FROM " . $this->_tablename . " WHERE itemid In ($itemid)";
                $result = $db->fetchAll($query);
                if(!is_null($result) && is_array($result)){
                    $cache->setCache($key, $result);
                }
            }
            return $result;
        }

        public function getListByCateId($cateid){
            $cache = $this->getCacheInstance();
		$key = 'getListByCateId'.$this->_tablename.$cateid;
		$result = $cache->getCache($key);
		if($result === FALSE)
		{
                    $db = $this->getDbConnection();
                    $query = "SELECT itemid,title FROM " . $this->_tablename . " WHERE cateid = $cateid";
                    $result = $db->fetchAll($query);
                    if(!is_null($result) && is_array($result))
                    {
                        $cache->setCache($key, $result);
                            
                    }
                }
                return $result;
        }
        


        /*--Nghidv added 1:36am 04-06-2011*/
        public function getLastestItem($newsid, $cateid){
            $db = $this->getDbConnection();
            $query = "SELECT * FROM " . $this->_tablename . " WHERE newsid = ? AND cateid = ? ORDER BY myorder asc LIMIT 0, 1 ";
            $data = array($newsid, $cateid);
            $result = $db->fetchAll($query, $data);

            if(!is_null($result) && is_array($result))
            {
                    return $result[0];
            }
            return null;
        }

        
        /*--Nghidv added 1:36am 04-06-2011*/
        public function getNewsetItem(){
            $db = $this->getDbConnection();
            $query = "SELECT * FROM " . $this->_tablename . " WHERE 1 ORDER BY myorder asc LIMIT 0, 1 ";
            $result = $db->fetchAll($query);

            if(!is_null($result) && is_array($result))
            {
                    return $result[0];
            }
            return null;
        }

        /*--Nghidv added 3:28pm 15-05-2011*/
        public function getMaxMyOrder(){
            $db = $this->getDbConnection();
            $query = "SELECT max(myorder) as max FROM " . $this->_tablename . " WHERE 1 ";
            $result = $db->fetchAll($query);

            if(!is_null($result) && is_array($result))
            {
                    return $result[0]['max'];
            }
            return 0;
        }


         public function getMaxMyOrderDiscount($newsid, $cateid){
            $db = $this->getDbConnection();
            $query = "SELECT max(myorder) as max FROM " . $this->_tablename . " WHERE newsid=? AND cateid=?";
            
            $data = array($newsid, $cateid);
            $result = $db->fetchAll($query, $data);

            $maxOrder = (int)$result[0]['max'];
//            if(!is_null($result) && is_array($result) && $result[0]['max']>0)
//            {
//                    return $result[0]['max'];
//            }

            $query = "SELECT count(*) as max FROM " . $this->_tablename . " WHERE newsid=$newsid AND cateid=$cateid ";
            $data = array($newsid, $cateid);
            $result = $db->fetchAll($query, $data);
            $maxCount= (int)$result[0]['max'];
            
            ($maxOrder > $maxCount ? $max = $maxOrder : $max = $maxCount);
//            if(!is_null($result) && is_array($result))
//            {
//                    return $result[0]['max'];
//            }

            return $max;
        }

        public function getDetailByMyOrder($myorder){
            $db = $this->getDbConnection();
            $query = "SELECT * FROM " . $this->_tablename . " WHERE myorder = ? ";
            $data = array($myorder);
            $result = $db->fetchAll($query,$data);

            if(!is_null($result) && is_array($result))
            {
                    return $result[0];
            }
            return null;
        }


        public function getUniqueDate($datestart = '', $dateend = ''){
            $db = $this->getDbConnection();
            if ($datestart != '' && $dateend != ''){
                $query = "SELECT DISTINCT DATE_FORMAT(posteddate,'%Y-%m-%d') as date FROM " . $this->_tablename . " WHERE uid > 0 AND posteddate >= ? AND posteddate <= ? ORDER BY posteddate asc";
                $data = array($datestart, $dateend);
            }else{
                $query = "SELECT DISTINCT DATE_FORMAT(posteddate,'%Y-%m-%d') as date FROM " . $this->_tablename . " WHERE uid > 0 ORDER BY posteddate asc";
                $data = array();
            }

            $result = $db->fetchAll($query,$data);
            if(!is_null($result) && is_array($result))
            {
                    return $result;
            }
            return null;
        }

        public function getCountByUID(){
            $db = $this->getDbConnection();
            $query = "SELECT count(distinct uid) as total FROM " . $this->_tablename . " WHERE uid > 0 ORDER BY myorder asc,posteddate desc";
            $result = $db->fetchAll($query,$data);
            if(!is_null($result) && is_array($result))
            {
                    return $result[0]['total'];
            }
            return 0;
        }

        public function getListByUIDByDateWithPaging($datestart = '', $dateend = '', $offset = 0, $records = 20)
	{
            $db = $this->getDbConnection();
            if ($datestart == '' || $dateend == ''){
                $query = "SELECT * FROM " . $this->_tablename . " WHERE uid > 0 ORDER BY myorder asc,posteddate desc LIMIT ";
                $data = array();
            }else{
                $query = "SELECT * FROM " . $this->_tablename . " WHERE uid > 0 AND posteddate >= ? AND posteddate <= ? ORDER BY myorder asc,posteddate desc LIMIT ";
                $data = array($datestart, $dateend);
            }
            $query .= " $offset, $records ";
            $result = $db->fetchAll($query,$data);
            if(!is_null($result) && is_array($result))
            {
                    return $result;
            }
            return null;
	}

        public function getListByUID()
	{
            $db = $this->getDbConnection();
            $query = "SELECT * FROM " . $this->_tablename . " WHERE uid > 0 ORDER BY myorder asc,posteddate desc";
            $result = $db->fetchAll($query,$data);
            if(!is_null($result) && is_array($result))
            {
                    return $result;
            }
	}

        public function getListByUIDWithPaging($offset=0, $records=500) {
            $db = $this->getDbConnection();
            $query = "SELECT * FROM " . $this->_tablename . " WHERE uid > 0 ORDER BY myorder asc,posteddate desc LIMIT ";
            $query .= " $offset, $records ";
            $data = array($uid,$newsid,$cateid);
            $result = $db->fetchAll($query,$data);
            if(!is_null($result) && is_array($result))
            {
                    return $result;
            }
	}

        /*--End nghidv added 3:28pm 15-05-2011*/
        public function getListOlder($newsid = 0, $cateid = 0, $itemid = 0, $limit=20)
	{
            $cache = $this->getCacheInstance();
            $key = $this->getKeyDetail('news.list.all.older'.$newsid.$cateid.$itemid.$limit);
            $result = $cache->getCache($key);

            if($result === FALSE)
            {
                if ($itemid == 0)
                    $query = "SELECT * FROM " . $this->_tablename . " WHERE newsid=? AND cateid=? AND enabled=1 ORDER BY posteddate desc";
                else
                    $query = "SELECT * FROM " . $this->_tablename . " WHERE newsid=? AND cateid=? AND enabled=1 AND itemid < $itemid ORDER BY posteddate desc LIMIT 0,$limit";

                $db = $this->getDbConnection();

                $data = array($newsid,$cateid);
                $result = $db->fetchAll($query,$data);
                $cache->setCache($key, $result, self::EXPIRED);
            }
            return $result;
	}

        public function getListNewer($newsid = 0, $cateid = 0, $itemid = 0)
	{
            if ($itemid == 0)
                $query = "SELECT * FROM " . $this->_tablename . " WHERE newsid=? AND cateid=? ORDER BY myorder asc,title desc";
            else
                $query = "SELECT * FROM " . $this->_tablename . " WHERE newsid=? AND cateid=? AND itemid > $itemid ORDER BY myorder asc,title desc LIMIT 0,3";

            $db = $this->getDbConnection();

            $data = array($newsid,$cateid);
            $result = $db->fetchAll($query,$data);
            return $result;
	}


	public function getList($newsid = 0, $cateid = 0,$offset=0, $records=20, $_key='', $filter='', $cols='', $enabled='', $random=false)
	{
                if (is_array($cateid)){
                    $cateids = implode(",", $cateid);
                }else{
                    $cateids = $cateid;
                }

		$cache = $this->getCacheInstance();
                
                $_key = addslashes($_key);
                
                if ($enabled !== ''){
                    $where[] = " enabled = $enabled";
                }
                
                if ($_key!='') $where[] = " title like '%$_key%'";
                if ($cols!='') $where[] = " $cols=1";
                
                if ($filter!='') $filter = " $filter , "; else $filter='';
                
                if ($cateids != '') $where[] = "cateid IN($cateids)";
                if ($newsid > 0) $where[] = "newsid = $newsid";
                $where = implode(" AND ", $where);
                if ($where != "" && $where != null) $where = " WHERE $where";
                
                if ($filter == '' || $_key == ''){
                    $key = $this->getKeyList($newsid,$cateids.$_key.$filter.$offset.$records.$cols.$random);
                    $result = $cache->getCache($key);
                }
                if ($random == true) {
                    $rand = ', rand() as rand';
                    $randOrder = 'rand ASC,';
//                    $result = false;
                }
               //$result = FALSE;                      
		if($result === FALSE)
		{
			$db = $this->getDbConnection();
                        if($records>0){
                            $query = "SELECT * $rand FROM " . $this->_tablename . " $where ORDER BY $randOrder $filter myorder asc, posteddate desc";
                            $query.= " LIMIT $offset,$records";
                        }else{
                            $query = "SELECT * $rand FROM " . $this->_tablename . " $where ORDER BY $randOrder $filter myorder asc, posteddate desc";
                        }
                        
			$result = $db->fetchAll($query);
                        
			if(!is_null($result) && is_array($result))
			{
                            $cache->setCache($key, $result, 300);
			}
		}
		return $result;
	}
        public function getListNewsDiscount($newsid = 0, $cateid = 0,$offset=0, $records=20)
        {
            $db = $this->getDbConnection();
            $query = "SELECT * FROM " . $this->_tablename . " WHERE newsid=$newsid AND cateid=$cateid ORDER BY myorder asc LIMIT ";
            $query .= " $offset, $records";
            $result = $db->fetchAll($query);
            if(!is_null($result) && is_array($result))
            {
                return $result;
            }
        }


        public function countGetListNewsDiscount($newsid = 0, $cateid = 0)
        {
            $db = $this->getDbConnection();
            $query = "SELECT * FROM " . $this->_tablename . " WHERE newsid=? AND cateid=? ORDER BY myorder asc,title desc";
            $data = array($newsid,$cateid);
            $result = $db->fetchAll($query,$data);
            return count($result);
        }
        public function getListTop($newsid = 0, $cateid = 0)
	{
            $db = $this->getDbConnection();
            $query = "SELECT * FROM " . $this->_tablename . " WHERE newsid=? AND cateid=? ORDER BY posteddate desc,title desc";
            $data = array($newsid,$cateid);
            $result = $db->fetchAll($query,$data);
            return $result[0];
	}
	
	public function getListWithPaging($newsid, $cateid = 0, $offset, $records = 20)
	{
		$paging = $this->getPagingBusiness();
		$keyprefix = $this->getKeyListPaging($newsid,$cateid);
		$params = array(
			'newsid' => $newsid,
			'cateid' => $cateid
		);
		$result = $paging->getData($offset,$records,$keyprefix,$params);
		return $result;
	}
	
	public function getListAll($newsid = 0, $offset=0, $records=20)
	{
		$cache = $this->getCacheInstance();
		$key = $this->getKeyListAll($newsid.$offset.$records);
		$result = $cache->getCache($key);
		if($result === FALSE)
		{
                    $db = $this->getDbConnection();
                    if ($newsid >0) {
                        $query = "SELECT * FROM " . $this->_tablename . " WHERE enabled = 1 AND newsid=? ORDER BY myorder,itemid desc LIMIT $offset, $records";
                        $data = array($newsid);
                    } else {
                        $query = "SELECT * FROM " . $this->_tablename . " WHERE enabled = 1 ORDER BY itemid desc LIMIT $offset, $records";
                        $data = array($newsid);                            
                    }
                    $result = $db->fetchAll($query,$data);			
                    if(!is_null($result) && is_array($result))
                    {
                            $cache->setCache($key, $result);
                    }
		}
		return $result;
	}
	
	public function _getRealData($newsid, $cateid, $page)
	{
		$offset = 0;
		$records = self::PAGING_NUM_RECORDS;

		if ($page == 0)
			$offset = 0;
		else
			$offset = $page * $records;
			
		$db = $this->getDbConnection();
		
		$query = "SELECT * FROM " . $this->_tablename . " WHERE  ishot <> 1 and newsid=? AND cateid=? ORDER BY myorder,itemid desc LIMIT ";			
		$query .= " $offset , $records ";
		$data = array($newsid,$cateid);
		$result = $db->fetchAll($query,$data);
		return $result;		
	}
	
	public function getHotList($newsid=0, $cateid=0, $limit=3)
	{
            
            $cache = $this->getCacheInstance();
            $key = $this->getKeyListHotAll($newsid, $cateid);
            $result = $cache->getCache($key);
            
            if ($result === FALSE){
                $db = $this->getDbConnection();
                $query = "SELECT * FROM " . $this->_tablename . " WHERE newsid=? and cateid=? and ishot=1 ORDER BY myorder asc,itemid desc LIMIT 0, $limit";
                $data = array($newsid,$cateid);
                $result = $db->fetchAll($query,$data);
                if(!is_null($result) && is_array($result))
                    $cache->setCache ($key, $result);
            }
            return $result;
	}
		
	public function getCountByCate($newsid=0,$cateid=0, $_key='')
	{
            if (is_array($cateid)){
                $cateids = implode(",", $cateid);
            }else{
                $cateids = $cateid;
            }
            
            if ($_key== ''){
		$cache = $this->getCacheInstance();
		$key = $this->getKeyCount($newsid,$cateids);
		$result = $cache->getCache($key);
            }else{
                //$result=false;
                $_key = 'AND title like "%'.$_key.'%"';
            }
            
		if($result === FALSE)
		{
			$db = $this->getDbConnection();
			$query = "SELECT count(*) AS mysum FROM " . $this->_tablename . " WHERE newsid=? AND cateid IN ($cateids) $_key";
			$data = array($newsid);
                        
			$result = $db->fetchAll($query,$data);
			if(!is_null($result) && is_array($result) && count($result) == 1)
			{
				$result = $result[0]['mysum'];				
			}
			else
			{
				$result = 0;				
			}
                        if($_key == '')
                            $cache->setCache($key,$result, 600);			
		}
		return $result;
	}
			
	public function getDetail($id)
	{
		$cache = $this->getCacheInstance();
		$key = $this->getKeyDetail($id);
		$result = $cache->getCache($key);
//                $result = false;
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


        public function getDetailByTitle($title)
	{
                $title = Business_Common_Utils::adaptTitleLinkURLSEO($title);               
		$cache = $this->getCacheInstance();
		$key = $this->getKeyDetailTitle($title);
		$result = $cache->getCache($key);

		if($result === FALSE)
		{
			$db = $this->getDbConnection();
			$query = "SELECT * FROM " .$this->_tablename . " WHERE title_seo='" . parent::adaptSQL($title) . "'";
			$result = $db->fetchAll($query);
			if($result != null && is_array($result))
			{
				$result = $result[0];
				$cache->setCache($key, $result, 600);
			}
		}
		return $result;
	}
	
	public function insert($newsid, $cateid, $data)
	{
		$data['newsid'] = $newsid;
		$data['cateid'] = $cateid;
                $data['title_seo'] = Business_Common_Utils::adaptTitleLinkURLSEO($data['title']);
		$db = $this->getDbConnection();
		$result = $db->insert($this->_tablename, $data);
                
		if($result > 0)
		{
			$this->_deleteAllCache($newsid,$cateid,null,$data['title']);
		}
                return $db->lastInsertId();
	}
	
	public function moveCate($id,$newsid,$cateid,$cateid_new)
	{
		$data = array(
			'cateid' => $cateid_new
		);
		$result = $this->_update($id,$data);
		if($result > 0)
		{
			$this->_deleteAllCache($newsid,$cateid);
			$this->_deleteAllCache($newsid,$cateid_new,$id);		
		}
	}
	
	public function update($id, $newsid, $cateid, $data)
	{
		//if(array_key_exists('newsid',$data)) unset($data['newsid']);
		//if(array_key_exists('cateid',$data)) unset($data['cateid']);
		$where = array();
		$where[] = "itemid='" . parent::adaptSQL($id) . "'";
		$db = $this->getDbConnection();
		$result = $db->update($this->_tablename, $data,$where);
		if($result > 0)
		{
                        $cache = $this->getCacheInstance();
                        $cache->flushAll();
			$this->_deleteAllCache($newsid,$cateid,$id);
		}
		return $result;
	}
	
	public function delete($id)
	{
		//get current menu
		$current = $this->getDetail($id);

		$where = array();
		$where[] = "itemid='" . parent::adaptSQL($id) . "'";
		$db = $this->getDbConnection();
		$result = $db->delete($this->_tablename, $where);
		if($result > 0)
		{
			$this->_deleteAllCache($current['newsid'],$current['cateid'],$id,$current['title']);
		}
	}

	///private functions /////

	private function _update($id, $data)
	{
	
		$where = array();
		$where[] = "itemid='" . parent::adaptSQL($id) . "'";
		$db = $this->getDbConnection();
		$result = $db->update($this->_tablename, $data,$where);
                $this->_deleteAllCache($data['newsid'], $data['cateid'], $id);
		return $result;
	}
	
		
	private function _deleteAllCache($newsid,$cateid,$id = null, $title='')
	{
		$cache = $this->getCacheInstance();
                
		$key = $this->getKeyList($newsid,$cateid);
		$cache->deleteCache($key);
                
		$key = $this->getKeyCount($newsid,$cateid);
		$cache->deleteCache($key);

		$key = $this->getKeyListAll($newsid);
		$cache->deleteCache($key);

		$key = $this->getKeyListHotAll($newsid,$cateid);
		$cache->deleteCache($key);
                
		if($id != null)
		{
			$key = $this->getKeyDetail($id);
			$cache->deleteCache($key);
		}
		
		// clean paging cache
		$key = $this->getKeyListPaging($newsid,$cateid);
		$paging = $this->getPagingBusiness();
		$paging->clearCachePaging($key);		
		
//                $cache->flushAll();f
                
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
        public function getListByName($name, $limit=0)
	{
            
            $cache = $this->getCacheInstance();
            $key = $this->getKeyDetailTitle(md5($name).$limit);
            $name = str_replace("'","",$name);
            $result = $cache->getCache($key);
            if ($result === FALSE){
                $db = $this->getDbConnection();
                if ($limit == 0)
                    $query = "SELECT * FROM " . $this->_tablename ." WHERE LOWER(title) LIKE '%$name%' ORDER BY itemid DESC";
                else
                    $query = "SELECT * FROM " . $this->_tablename ." WHERE LOWER(title) LIKE '%$name%' ORDER BY itemid DESC LIMIT 0, $limit";
		//$data = array($name);
                //$result = $db->fetchAll($query, $data);
		$result = $db->fetchAll($query);
                $cache->setCache($key, $result, self::EXPIRED);
            }
            return $result;
	}
        
	public function getListByName2($name, $limit=0)
	{
            
            $cache = $this->getCacheInstance();
            $key = $this->getKeyDetailTitle(md5($name).$limit."2");
            
            $result = $cache->getCache($key);
            if ($result === FALSE){
                $db = $this->getDbConnection();
                if ($limit == 0)
                    $query = "SELECT * FROM " . $this->_tablename ." WHERE LOWER(title) LIKE '%$name%' ORDER BY itemid DESC";
                else
                    $query = "SELECT * FROM " . $this->_tablename ." WHERE LOWER(title) LIKE '%$name%' ORDER BY itemid DESC LIMIT 0, $limit";		
                $result = $db->fetchAll($query);
                $cache->setCache($key, $result, self::EXPIRED);
            }
            return $result;
	}

        public function getListByTitle($name, $limit=0)
	{
//            SELECT * FROM quotes_table WHERE MATCH (quote) AGAINST ('"mangé")
            
            $cache = $this->getCacheInstance();
            $key = $this->getKeyDetailTitle('ws.get.list.by.title.new'.$title.$name.$limit);
            $result = $cache->getCache($key);
            if ($result === FALSE){
                $db = $this->getDbConnection();
                if ($limit == 0)
                    $query = "SELECT *,MATCH (title) AGAINST ('+$name') as t FROM ".$this->_tablename." WHERE MATCH (title) AGAINST ('+$name') > 1 ORDER BY t desc";
                else
                    $query = "SELECT *,MATCH (title) AGAINST ('+$name') as t FROM ".$this->_tablename." WHERE MATCH (title) AGAINST ('+$name') > 1 ORDER BY t desc LIMIT 0, $limit";
                $result = $db->fetchAll($query);
                $cache->setCache($key, $result, self::EXPIRED);
            }
            return $result;
	}
        
        public function getCountByName($name)
        {
            $cache = $this->getCacheInstance();
            $key = $this->getKeyDetail($id.$name);
            $result = $cache->getCache($key);
            
            if ($result === FALSE){
                $db = $this->getDbConnection();
                $query = "SELECT count(*) AS mysum FROM " . $this->_tablename ." WHERE title LIKE '%$name%' ORDER BY itemid DESC";
                $result = $db->fetchAll($query);
                $cache->setCache($key, $result, self::EXPIRED);
            }
            return $result;
        }
        public function showData($data)
        {
            $data=str_replace("\n","<br />", $data);
            return $data;
        }
        public function getListNewsHost()
        {
            $db = $this->getDbConnection();
            $query = "SELECT * FROM " . $this->_tablename ." WHERE ishot='1' ORDER BY itemid DESC LIMIT 0,4";
            $result = $db->fetchAll($query);
            return $result;
        }
        public function getListAllRequest($newsid, $cateid)
	{
            $db = $this->getDbConnection();
            $query = "SELECT * FROM " . $this->_tablename." WHERE newsid = $newsid AND cateid = $cateid ORDER BY itemid DESC";
            $result = $db->fetchAll($query);
            return $result;
	}
        public function getListNews()
        {
            $db = $this->getDbConnection();
            $query = "SELECT * FROM " . $this->_tablename." WHERE newsid=0 AND cateid=0 ORDER BY myorder asc";
            $result = $db->fetchAll($query);
            return $result;
        }
        
        
        
        /*=========================*/
        public function getListAllByType($type='', $offset=0, $records=0){
            $cache = $this->getCacheInstance();
            $key = $this->getKeyDetail($type.$offset.$records.'list.all.by.type');
            $result = $cache->getCache($key);
            $result=false;
            if ($result === FALSE){
                $db = $this->getDbConnection();
                $query = "SELECT * FROM " . $this->_tablename." WHERE enabled=1 AND $type > 0 ORDER BY posteddate desc";
                if($records>0){
                    $query .= " LIMIT $offset, $records";
                }
                $result = $db->fetchAll($query);
                $cache->setCache($key, $result, self::EXPIRED);
            }
            return $result;
        }
        
}
?> 
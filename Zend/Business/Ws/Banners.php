<?php

class Business_Ws_Banners extends Business_Abstract
{
	private $_tablename = 'ws_banners';
	
	const KEY_LIST_ALL = 'banners.list.all.%s';			//key of list by language
	const KEY_DETAIL = 'banners.detail.%s';
	
	private static $_instance = null; 
	
	function __construct()
	{
		
	}
	
	/**
	 * get instance of Business_Ws_Banners
	 *
	 * @return Business_Ws_Banners
	 */
	public static function getInstance()
	{
		if(self::$_instance == null)
		{
			self::$_instance = new Business_Ws_Banners();
		}
		return self::$_instance;
	}

        public static function mi_buildUrl($lang,$delta,$itemid)
	{

		$_banners = Business_Ws_Banners::getInstance();
		$item = $_banners->getDetail($delta,$lang);
		$url = $item['link'];

		if(strpos($url,'http://') === FALSE)
		{
			$url = Globals::getConfig('root_url') . $url;
		}
//		$title = $item['name'];
//
//		$special = array(" ","/","\\","?","&");
//		$title = str_replace($special,"_",$title);
//
//		$title = $_links->removeTiengViet($title);
//
//		$title = strtolower($title);
//
//		$url = 'item/' . $itemid . '/lang/' . $lang . '/links/' . $delta . '/' . $title;
		return $url;
	}

	public static function mi_getDetail($lang,$delta)
	{
		$_banners = Business_Ws_Banners::getInstance();
		$item = $_banners->getDetail($delta,$lang);
		return $item;
	}

	public static function mi_getAltTitle($lang,$delta)
	{
		$_banners = Business_Ws_Banners::getInstance();
		$item = $_banners->getDetail($delta,$lang);
		if($item == null) return "";
		return $item['name'] . ' (' . $item['link'] . ')';
	}

	public static function mi_getList($lang,$itemid = null)
	{
		$_banners = Business_Ws_Banners::getInstance();
		$list = $_banners->getList($lang);
		$return = array();

		if($list != null && is_array($list) && count($list) > 0)
		{
			for($i=0;$i<count($list);$i++)
			{
				$return[$list[$i]['banners_id']] = $list[$i]['name'] . ' (' . $list[$i]['link'] . ')';
			}
		}
		return $return;
	}

	private function getKeyList($filter)
	{
		return sprintf(self::KEY_LIST_ALL,$filter);
	}

	private function getKeyDetail($id)
	{
		return sprintf(self::KEY_DETAIL,$id);
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
	
        private function getCacheInstance()
	{
		$cache = GlobalCache::getCacheInstance('ws');
		return $cache;
	}

	public function getList($enabled='')
	{
            $cache = $this->getCacheInstance();
            //$result = $cache->getCache($key);
//            if($result === FALSE)
//            {
            $db = $this->getDbConnection();
            $query = "select * from " . $this->_tablename . " where 1 order by ordering asc";
            $result = $db->fetchAll($query);
//            }
            return $result;
	}
	public function getListByKey($key='')
	{
            $cache = $this->getCacheInstance();
            $db = $this->getDbConnection();
            $query = "select * from " . $this->_tablename . " where LOWER(name) LIKE '%".$key."%' OR link LIKE '%".$key."%' order by ordering asc";
            $result = $db->fetchAll($query);
            return $result;
	}
        
        public function getListOfFifterByKey($fitter=-1, $key_search=null, $enabled='', $rnd=false)
	{            
            $cache = $this->getCacheInstance();
            if ($rnd != false)
                $key = $this->getKeyList($fitter.$enabled.$key_search);
            else
                $key = $this->getKeyList($fitter.$enabled.$key_search.$rnd.$limit);
            
            $result = $cache->getCache($key);
            
            if ($enabled === '')
                $where = '';
            else
                $where = ' AND enabled=' . $enabled;
            
            if ($key_search !== null) {
                $key_search = strtolower($key_search);
                $where = ' AND ( LOWER(name) LIKE "%' . $key_search . '%" OR LOWER(link) LIKE "%'.$key_search.'%")';
            }
            $result = false;
            if ($result === FALSE){
                $db = $this->getDbConnection();
                if($fitter==-1)
                {
                    $query = "select * from " . $this->_tablename . " where 1 order by ordering asc";
                }
                else
                {
                    if ($rnd == false)
                        $query = "select * from " . $this->_tablename . " where pageshow=$fitter $where order by ordering asc";
                    else {
                        $query = "select *,rand() as random from " . $this->_tablename . " where pageshow=$fitter $where order by random asc LIMIT 0, 5";
                    }
                }
                        
                $result = $db->fetchAll($query);
                
                $cache->setCache($key, $result);
                
            }
            
            return $result;
	}
        
        public function getListOfFifter($fitter=-1, $enabled='', $rnd=false, $limit)
	{            
            $cache = $this->getCacheInstance();
            if ($rnd != false)
                $key = $this->getKeyList($fitter.$enabled);
            else
                $key = $this->getKeyList($fitter.$enabled.$rnd.$limit);
            
            $result = $cache->getCache($key);
            
            if ($enabled === '')
                $where = '';
            else
                $where = ' AND enabled=' . $enabled;
            if ($result === FALSE){
                $db = $this->getDbConnection();
                if($fitter==-1)
                {
                    $query = "select * from " . $this->_tablename . " where 1 order by ordering asc";
                }
                else
                {
                    if ($rnd == false)
                        $query = "select * from " . $this->_tablename . " where pageshow=$fitter $where order by ordering asc";
                    else {
                        $query = "select *,rand() as random from " . $this->_tablename . " where pageshow=$fitter $where order by random asc LIMIT 0, $limit";
                    }
                }
                $result = $db->fetchAll($query);
                
                $cache->setCache($key, $result);
                
            }
            
            return $result;
	}
        
        public function getListByName($fitter=-1, $enabled=0, $name='', $rnd=false)
	{            
            $cache = $this->getCacheInstance();
            $md5name = md5($name);
            if ($rnd != false)
                $key = $this->getKeyList($fitter.$enabled.$md5name);
            else
                $key = $this->getKeyList($fitter.$enabled.$rnd.$limit.$md5name);
            
            $result = $cache->getCache($key);
            
            if ($enabled === '')
                $where = '';
            else {
                $where = ' AND enabled=' . $enabled;
            }
            if ($name != "") {
                $where .= ' AND name like"%' . $name . '%"';
            }
            
            if ($result === FALSE){
                $db = $this->getDbConnection();
                if($fitter==-1)
                {
                    $query = "select * from " . $this->_tablename . " where 1 order by ordering asc";
                }
                else
                {
                    if ($rnd == false)
                        $query = "select * from " . $this->_tablename . " where pageshow=$fitter $where order by ordering asc";
                    else {
                        $query = "select *,rand() as random from " . $this->_tablename . " where pageshow=$fitter $where order by random asc LIMIT 0, 5";
                    }
                }
                $result = $db->fetchAll($query);
                
                $cache->setCache($key, $result);
                
            }
            
            return $result;
	}
        
	public function getDetail($banners_id)
	{
            $cache = $this->getCacheInstance();
            $key = $this->getKeyDetail($banners_id);
            $result = $cache->getCache($key);
            if($result === FALSE)
            {
                $banners_id = intval($banners_id);
                $db = $this->getDbConnection();
                $query = " SELECT * FROM " . $this->_tablename ." WHERE banners_id = ?";
                $data = array($banners_id);
                $result = $db->fetchAll($query,$data);
                if($result != null && is_array($result))
                {
                        $cache->setCache($key, $result[0]);
			return $result[0];
                }
            }
            else return $result;
	}
	public function update($banners_id, $data)
	{
		$db = $this->getDbConnection();
		$where = array();
		$where[] = "banners_id = '" . parent::adaptSQL($banners_id) . "'";                
		try
		{			
			$result = $db->update($this->_tablename, $data, $where);

                        if($result > 0)
                        {
			$this->_deleteAllCache($banners_id);
                        }
			
			return $result; 
		}
		catch(Exception $e)
		{
			return 0;
		}
	}
        
        public function updateHits($banners_id, $data)
	{
		$db = $this->getDbConnection();
		$where = array();
		$where[] = "banners_id = '" . parent::adaptSQL($banners_id) . "'";                
		try
		{			
			$result = $db->update($this->_tablename, $data, $where);

                        if($result > 0)
                        {
                            $this->_deleteAllCacheHits($banners_id);
                        }
			
			return $result; 
		}
		catch(Exception $e)
		{
			return 0;
		}
	}
        
	public function insert($data)
	{
		$db = $this->getDbConnection();
		$result = $db->insert($this->_tablename,$data);
                $this->_deleteAllCache($banners_id);
                return $result;
	}
	
	public function delete($banners_id)
	{
                $current = $this->getDetail($id);
		$db = $this->getDbConnection();
		$where = array();
		$where[] = "banners_id='" . parent::adaptSQL($banners_id) . "'";
		$result = $db->delete($this->_tablename,$where);
                if($result > 0)
		{
			$this->_deleteAllCache($banners_id);
		}
                return $result;
	}

        private function _deleteAllCache($banners_id = null)
	{
		$cache = $this->getCacheInstance();
                for($i=-1; $i<100;$i++){
                    $key = $this->getKeyList($i);
                    $cache->deleteCache($key);
                    
                }
		if($banners_id != null)
		{
			$key = $this->getKeyDetail($banners_id);
			$cache->deleteCache($key);
		}
	}
        
        private function _deleteAllCacheHits($banners_id = null)
	{
		$cache = $this->getCacheInstance();
		if($banners_id != null)
		{
			$key = $this->getKeyDetail($banners_id);
			$cache->deleteCache($key);
		}
	}
        
        public function getListBannerOfPageshow($page)
        {
            $this->getListOfFifter($page);
        }
        public function getMaxOrdering(){
            $db = $this->getDbConnection();
            $query = "SELECT max(ordering) as max FROM " . $this->_tablename . " WHERE 1 ";
            $result = $db->fetchAll($query);
            if(!is_null($result) && is_array($result))
            {
                    return $result[0]['max'];
            }
            return 0;
        }
        
        public function getTotal($pos=null){
            $db = $this->getDbConnection();
            if ($pos != null)
                $query = "SELECT count(*) as max FROM " . $this->_tablename . " WHERE pageshow =  " . $pos;
            else
                $query = "SELECT count(*) as max FROM " . $this->_tablename . " WHERE 1";
            $result = $db->fetchAll($query);
            if(!is_null($result) && is_array($result))
            {
                    return $result[0]['max'];
            }
            return 0;
        }
        public function getDetailByMyOrdering($ordering)
        {
            $db = $this->getDbConnection();
            $query = "SELECT * FROM " . $this->_tablename . " WHERE ordering = ? ";
            $data = array($ordering);
            $result = $db->fetchAll($query,$data);

            if(!is_null($result) && is_array($result))
            {
                    return $result[0];
            }
            return null;
        }
}

?>
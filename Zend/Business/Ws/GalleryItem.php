<?php
class Business_Ws_GalleryItem extends Business_Abstract
{
	private $_tablename = 'ws_galleryitem';
	
	const KEY_LIST = 'gallery.list.%s';			//key of list by language
        const KEY_LIST_BY_CATE = 'gallery.list.cate.%s';			//key of list by language
	const KEY_DETAIL = 'gallery.detail.%s';	//key of detail.id
	
	private static $_instance = null; 
	
	function __construct()
	{			
	}
	//public static function
	/**
	 * get instance of Business_Ws_GalleryItem
	 *
	 * @return Business_Ws_GalleryItem
	 */
	public static function getInstance()
	{
		if(self::$_instance == null)
		{
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	public static function mi_buildUrl($lang,$delta,$itemid)
	{		
		try 
		{
			$_contents = Business_Ws_Contents::getInstance();
			$item = $_contents->getDetail($delta,$lang);
			
			$title = $item['title'];
			
			$title = Business_Common_Utils::adaptTitleLinkURL($title);
			
			$_lang = Business_Ws_Languages::getInstance();
			$mylang = $_lang->getDetail($lang);
			$lang = $mylang['lang'];
			//$url = 'item/' . $itemid . '/lang/' . $lang . '/content/' . $delta . '/' . $title;
			//format /:lang/:item/content/:delta/*
			$url = Globals::getBaseUrl() . $lang . '/' . $itemid . '/content/' . $delta . '/' . $title;
			
		}
		catch(Exception $e)
		{			
			$url = "";
		}		
		return $url;
	}
	
	public static function mi_getDetail($lang,$delta)
	{
		$_contents = Business_Ws_Contents::getInstance();
		$item = $_contents->getDetail($delta,$lang);
		return $item;
	}
	
	public static function mi_getAltTitle($lang,$delta)
	{
		$_contents = Business_Ws_Contents::getInstance();
		$item = $_contents->getDetail($delta,$lang);
		
		if($item == null) return "";
		return $item['title'];
	}
	
	public static function mi_getList($lang,$itemid = null)
	{		
		$_contents = Business_Ws_Contents::getInstance();
		$list = $_contents->getList($lang);
		$return = array();
		
		if($list != null && is_array($list) && count($list) > 0)
		{
			for($i=0;$i<count($list);$i++)
			{
				$return[$list[$i]['contentid']] = $list[$i]['title'];
			}
		}
		return $return;
	}
	
	private function getKeyList($lang)
	{
		return sprintf(self::KEY_LIST,$lang);
	}
	
	private function getKeyDetail($id)
	{
		return sprintf(self::KEY_DETAIL,$id);
	}

        private function getKeyListByCate($id)
	{
		return sprintf(self::KEY_LIST_BY_CATE,$id);
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

        public function getTotal(){
            $db = $this->getDbConnection();
            $query = "SELECT count(*) as total FROM " . $this->_tablename . " WHERE 1";
            $result = $db->fetchAll($query);
            if(!is_null($result) && is_array($result))
            {
                return $result[0]['total'];
            }
            return null;
        }

        public function getListWithPaging($offset=0, $records=0){
            $db = $this->getDbConnection();
            $query = "SELECT * FROM " . $this->_tablename . " WHERE 1 ORDER BY posteddate DESC";            
            $result = $db->fetchAll($query);
            if(!is_null($result) && is_array($result))
            {
                return $result;
            }
            return null;
        }

	public function getList($lang = '')
	{
            $db = $this->getDbConnection();
            $query = "SELECT * FROM " . $this->_tablename . " WHERE 1 ORDER BY posteddate DESC";            
            $result = $db->fetchAll($query,$data);
            if(!is_null($result) && is_array($result))
            {
                    return $result;
            }
            return null;
	}

        public function getListByCate($galleryid, $lang = '')
	{
		$cache = $this->getCacheInstance();
		$key = $this->getKeyListByCate($galleryid);
		$result = $cache->getCache($key);
		if($result === FALSE)
		{
			$db = $this->getDbConnection();
			$query = "SELECT * FROM " . $this->_tablename . " WHERE galleryid = ? AND lang=? ORDER BY posteddate";
			$data = array($galleryid, $lang);
			$result = $db->fetchAll($query,$data);
                        
			if(!is_null($result) && is_array($result))
			{
				$cache->setCache($key, $result);
			}
		}
		return $result;
	}
			
	public function getDetail($id,$lang = '')
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
	
	public function insert($lang, $data)
	{
		$data['lang'] = $lang;
		$db = $this->getDbConnection();
		$result = $db->insert($this->_tablename, $data);
		if($result > 0)
		{
			$this->_deleteAllCache($lang);			
		}
	}
	
	public function update($id, $lang, $data)
	{
		if(array_key_exists('lang',$data)) unset($data['lang']);
		$where = array();
		$where[] = "itemid='" . parent::adaptSQL($id) . "'";
		$db = $this->getDbConnection();
		$result = $db->update($this->_tablename, $data,$where);
		if($result > 0)
		{
			$this->_deleteAllCache($lang,$id);			
		}
	}
	
	public function delete($id,$lang)
	{
		//get current menu
		$current = $this->getDetail($id);
				
		$where = array();
		$where[] = "itemid='" . parent::adaptSQL($id) . "'";
		$db = $this->getDbConnection();
		$result = $db->delete($this->_tablename, $where);
		if($result > 0)
		{
			$this->_deleteAllCache($lang,$id);			
		}
	}
	
	///private functions /////

		
	private function _deleteAllCache($lang,$id = null)
	{
		$cache = $this->getCacheInstance();
		$key = $this->getKeyList($lang);
		$cache->deleteCache($key);		
		if($id != null)
		{
			$key = $this->getKeyDetail($id);
			$cache->deleteCache($key);
		}		
	}

        public function getMaxOrdering(){
            $db = $this->getDbConnection();
            $query = "SELECT max(myorder) as max FROM " . $this->_tablename . " WHERE 1 ";
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
            $query = "SELECT * FROM " . $this->_tablename . " WHERE myorder = ? ";
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
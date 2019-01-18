<?php

class Business_Ws_Contents extends Business_Abstract
 {
	private $_tablename = 'ws_contents';
	
	const KEY_LIST = 'ws_contents.list.%s';			//key of list by language	
	const KEY_DETAIL = 'ws_contents.detail.%s';	//key of detail.id
	const KEY_DETAIL_TITLE = 'ws_contents.detail.title.%s';	//key of detail.id
		
	private static $_instance = null; 
	
	function __construct()
	{			
	}
	
	//public static function
	/**
	 * get instance of Business_Ws_Contents
	 *
	 * @return Business_Ws_Contents
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

        private function getKeyDetailByTitle($title)
	{
            return sprintf(self::KEY_DETAIL_TITLE,$title);
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
	
	public function getList($lang = '')
	{
		$cache = $this->getCacheInstance();
		$key = $this->getKeyList($lang);
		$result = $cache->getCache($key);
		if($result === FALSE)
		{
			$db = $this->getDbConnection();
			$query = "SELECT * FROM " . $this->_tablename . " WHERE lang=? ORDER BY title";
			$data = array($lang);
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
			$query = "SELECT * FROM " .$this->_tablename . " WHERE contentid='" . parent::adaptSQL($id) . "'";
			$result = $db->fetchAll($query);
			if($result != null && is_array($result))
			{
				$result = $result[0];
				$cache->setCache($key, $result);				
			}
		}
		return $result;
	}

        public function getDetailByName($name,$lang = '')
	{		
            $db = $this->getDbConnection();
            $query = "SELECT * FROM " .$this->_tablename . " WHERE title='" . parent::adaptSQL($name) . "'";
            $result = $db->fetchAll($query);
            if($result != null && is_array($result))
            {
                    $result = $result[0];                    
            }
            return $result;
	}	
	
	public function insert($lang, $data)
	{
		$data['lang'] = $lang;
                $data['title_seo'] = Business_Common_Utils::adaptTitleLinkURLSEO($data['title']);                
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
                $title_seo = Business_Common_Utils::adaptTitleLinkURLSEO($data['title']);
                $data['title_seo'] = $title_seo;
		$where = array();
		$where[] = "contentid='" . parent::adaptSQL($id) . "'";
		$db = $this->getDbConnection();
		$result = $db->update($this->_tablename, $data,$where);
		if($result > 0)
		{
			$this->_deleteAllCache($lang,$id, $title_seo);
		}
	}
	
	public function delete($id,$lang)
	{
		//get current menu
		$current = $this->getDetail($id);
		$title_seo = Business_Common_Utils::adaptTitleLinkURLSEO($current['title']);
		$where = array();
		$where[] = "contentid='" . parent::adaptSQL($id) . "'";
		$db = $this->getDbConnection();
		$result = $db->delete($this->_tablename, $where);
		if($result > 0)
		{
			$this->_deleteAllCache($lang,$id, $title_seo);
		}
	}
	
	///private functions /////

		
	private function _deleteAllCache($lang,$id = null, $title_seo=null)
	{
		$cache = $this->getCacheInstance();
		$key = $this->getKeyList($lang);
		$cache->deleteCache($key);		
		$key = $this->getKeyDetailByTitle($title_seo);
		$cache->deleteCache($key);
		if($id != null)
		{
			$key = $this->getKeyDetail($id);
			$cache->deleteCache($key);
		}		
	}
	
        /*===============MODIFY====================*/

        public function getDetailByTitle($title){
            $cache = $this->getCacheInstance();
            $key = $this->getKeyDetailByTitle($title);
            $result = $cache->getCache($key);

            if($result === FALSE)
            {
                    $db = $this->getDbConnection();
                    $query = "SELECT * FROM " .$this->_tablename . " WHERE title_seo='" . parent::adaptSQL($title) . "'";
                    $result = $db->fetchAll($query);
                    if($result != null && is_array($result))
                    {
                            $result = $result[0];
                            $cache->setCache($key, $result);
                    }
            }
            return $result;
        }

        /*===============END MODIFY====================*/
}
?>
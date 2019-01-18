<?php

class Business_Ws_FaqModule extends Business_Abstract
{
	private $_tablename = 'ws_faqmodule';
	
	const KEY_LIST = 'ws_faqmodule.list';			//key of list
	const KEY_DETAIL = 'ws_faqmodule.detail.%s';		//key of detail.id
			
	private static $_instance = null; 
	
	function __construct()
	{			
	}
	
	//public static function
	/**
	 * get instance of Business_Ws_FaqModule
	 *
	 * @return Business_Ws_FaqModule
	 */
	public static function getInstance()
	{
		if(self::$_instance == null)
		{
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	public function getKeyList()
	{
		return sprintf(self::KEY_LIST);
	}
	
	public function getKeyDetail($id)
	{
		return sprintf(self::KEY_DETAIL,$id);
	}
	
	public static function mi_buildUrl($lang,$delta,$itemid)
	{	
		$_menuitem = Business_Ws_MenuItem::getInstance();
		$item = $_menuitem->getDetailById($itemid);
		$url = '';
		$langid = $lang;
		
		if($item != null)
		{
			$title = $item['title'];
			$title = Business_Common_Utils::adaptTitleLinkURL($title);
			$_faqmodule = Business_Ws_FaqModule::getInstance();
			$faq = $_faqmodule->getDetail($delta);
			
			$menuitemstart = $faq['menuitemstart'];
			if($menuitemstart == "" || $menuitemstart == null) $menuitemstart = 0;
			else
			{
				$menuitemstart = json_decode($menuitemstart,true);
				if(array_key_exists($langid,$menuitemstart)) $menuitemstart = $menuitemstart[$langid];
				else $menuitemstart = 0;
			}			
			
			$_lang = Business_Ws_Languages::getInstance();
			$mylang = $_lang->getDetail($lang);
			$lang = $mylang['lang'];			

			//format : /:lang/:menuitem/news/cate/:newsid/:cateid/title		//for show cate
			//format : /:lang/:menuitem/news/detail/:newsid/:newsitemid/title
			$url = Globals::getBaseUrl() . $lang . '/' . $itemid . '/faq/' . $delta . '/' . $menuitemstart . '/' . $title;			 
		}
		return $url;
	}
	
	public static function mi_getDetail($lang,$delta)
	{
		$_faqmodule = Business_Ws_FaqModule::getInstance();
		$faqmodule = $_faqmodule->getDetail($delta);
		return $faqmodule;
	}
	
	public static function mi_getAltTitle($lang,$delta)
	{		
		$_faqmodule = Business_Ws_FaqModule::getInstance();
		$faqmodule = $_faqmodule->getDetail($delta);
		return $faqmodule['desc'];
	}
	
	public static function mi_getList($lang,$itemid = null)
	{		
		$return = array();
		
		$_faqmodule = Business_Ws_FaqModule::getInstance();
		
		$list = $_faqmodule->getList();
		
		if($list != null && count($list) > 0)
		{
			for($i=0;$i<count($list);$i++) $return[$list[$i]['faqid']] = $list[$i]['name'];
		}
		
		return $return;
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
	
	public function buildDetailUrl($lang,$menuitem,$item)
	{
		//:lang/:menuitem/news/detail/:newsid/:newsitemid/title
		$_lang = Business_Ws_Languages::getInstance();
		$mylang = $_lang->getDetail($lang);
		$lang = $mylang['lang'];		
			
		$delta = $item['cateid'];
		$title = $item['title'];
		$special = array(" ","/","\\","?","&");
		$title = str_replace($special,"_",$title);
		$title = Business_Common_Utils::removeTiengViet($title);
		$title = strtolower($title) . '.html';
		$url = Globals::getBaseUrl() . $lang . '/' . $menuitem . '/faq/detail/' . $delta . '/' . $item['itemid'] . '/' . $title;
		return $url;
	}
	
	public function getList()
	{
		$cache = $this->getCacheInstance();
		$key = $this->getKeyList();
		$result = $cache->getCache($key);
		if($result === FALSE)
		{
			$db = $this->getDbConnection();
			$query = "SELECT * FROM " . $this->_tablename . " ORDER BY name";			
			$result = $db->fetchAll($query);
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
			$query = "SELECT * FROM " . $this->_tablename . " WHERE faqid=?";
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
	
	public function insert($data)
	{		
		$db = $this->getDbConnection();
		$result = $db->insert($this->_tablename, $data);
		if($result > 0)
		{
			$this->_deleteAllCache();			
		}
	}
	
	public function update($id, $data)
	{		
		$where = array();
		$where[] = "faqid='" . parent::adaptSQL($id) . "'";
		$db = $this->getDbConnection();
		$result = $db->update($this->_tablename, $data,$where);
		if($result > 0)
		{
			$this->_deleteAllCache($id);			
		}
	}
	
	public function delete($id)
	{
		//get current menu
		$current = $this->getDetail($id);
				
		$where = array();
		$where[] = "faqid='" . parent::adaptSQL($id) . "'";
		$db = $this->getDbConnection();
		$result = $db->delete($this->_tablename, $where);
		if($result > 0)
		{
			$this->_deleteAllCache($id);			
		}
	}
		
	///private functions /////

	private function _deleteAllCache($id = null)
	{
		$cache = $this->getCacheInstance();
		$key = $this->getKeyList();
		$cache->deleteCache($key);		
		if($id != null)
		{
			$key = $this->getKeyDetail($id);
			$cache->deleteCache($key);
		}		
	}
	
	
	
	
}
?>
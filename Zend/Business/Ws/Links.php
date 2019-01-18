<?php

class Business_Ws_Links extends Business_Abstract
{
	private $_tablename = 'ws_links';
	
	const KEY_LIST = 'links.list.%s';//key of list by language	
	const KEY_DETAIL = 'links.detail.%s';//key of detail.id
		
	private static $_instance = null; 
	
	function __construct()
	{			
	}
	
	//public static function
	/**
	 * get instance of Business_Ws_Links
	 *
	 * @return Business_Ws_Links
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
		
		$_links = Business_Ws_Links::getInstance();
		$item = $_links->getDetail($delta,$lang);
		$url = $item['linkpath'];
		
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
		$_links = Business_Ws_Links::getInstance();
		$item = $_links->getDetail($delta,$lang);
		return $item;
	}
	
	public static function mi_getAltTitle($lang,$delta)
	{
		$_links = Business_Ws_Links::getInstance();
		$item = $_links->getDetail($delta,$lang);
		if($item == null) return "";
		return $item['name'] . ' (' . $item['linkpath'] . ')';
	}
	
	public static function mi_getList($lang,$itemid = null)
	{
		$_links = Business_Ws_Links::getInstance();
		$list = $_links->getList($lang);
		$return = array();
		
		if($list != null && is_array($list) && count($list) > 0)
		{
			for($i=0;$i<count($list);$i++)
			{
				$return[$list[$i]['linkid']] = $list[$i]['name'] . ' (' . $list[$i]['linkpath'] . ')';
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
			$query = "SELECT * FROM " . $this->_tablename . " WHERE lang=? ORDER BY name";
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
			$query = "SELECT * FROM " .$this->_tablename . " WHERE linkid='" . parent::adaptSQL($id) . "'";
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
		$where[] = "linkid='" . parent::adaptSQL($id) . "'";
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
		$where[] = "linkid='" . parent::adaptSQL($id) . "'";
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
	
	private function removeTiengViet($content)
	{
		 $trans = array ('à' => 'a', 'á' => 'a', 'ả' => 'a', 'ã' => 'a', 'ạ' => 'a', 'â' => 'a', 'ấ' => 'a', 'ầ' => 'a', 'ẫ' => 'a', 'ẩ' => 'a', 'ậ' => 'a', 'ú' => 'a', 'ù' => 'u', 'ủ' => 'u', 'ũ' => 'u', 'ụ' => 'u', 'à' => 'a', 'á' => 'a', 'ô' => 'o', 'ố' => 'o', 'ồ' => 'o', 'ổ' => 'o', 'ỗ' => 'o', 'ộ' => 'o', 'ó' => 'o', 'ò' => 'o', 'ỏ' => 'o', 'õ' => 'o', 'ọ' => 'o', 'ê' => 'e', 'ế' => 'e', 'ề' => 'e', 'ể' => 'e', 'ễ' => 'e', 'ệ' => 'e', 'í' => 'i', 'ì' => 'i', 'ỉ' => 'i', 'ĩ' => 'i', 'ị' => 'i', 'ơ' => 'o', 'ớ' => 'o', 'ý' => 'y', 'ỳ' => 'y', 'ỷ' => 'y', 'ỹ' => 'y', 'ỵ' => 'y', 'ờ' => 'o', 'ở' => 'o', 'ỡ' => 'o', 'ợ' => 'o', 'ư' => 'u', 'ừ' => 'u', 'ứ' => 'u', 'ử' => 'u', 'ữ' => 'u', 'ự' => 'u', 'đ' => 'd', 'À' => 'A', 'Á' => 'A', 'Ả' => 'A', 'Ã' => 'A', 'Ạ' => 'A', 'Â' => 'A', 'Ấ' => 'A', 'À' => 'A', 'Ẫ' => 'A', 'Ẩ' => 'A', 'Ậ' => 'A', 'Ú' => 'U', 'Ù' => 'U', 'Ủ' => 'U', 'Ũ' => 'U', 'Ụ' => 'U', 'Ô' => 'O', 'Ố' => 'O', 'Ồ' => 'O', 'Ổ' => 'O', 'Ỗ' => 'O', 'Ộ' => 'O',
        'Ê' => 'E', 'Ế' => 'E', 'Ề' => 'E', 'Ể' => 'E', 'Ễ' => 'E', 'Ệ' => 'E', 'Í' => 'I', 'Ì' => 'I', 'Ỉ' => 'I', 'Ĩ' => 'I', 'Ị' => 'I', 'Ơ' => 'O', 'Ớ' => 'O', 'Ờ' => 'O', 'Ở' => 'O', 'Ỡ' => 'O', 'Ợ' => 'O', 'Ư' => 'U', 'Ừ' => 'U', 'Ứ' => 'U', 'Ử' => 'U', 'Ữ' => 'U', 'Ự' => 'U', 'Đ' => 'D', 'Ý' => 'Y', 'Ỳ' => 'Y', 'Ỷ' => 'Y', 'Ỹ' => 'Y', 'Ỵ' => 'Y', 
        'á' => 'a', 'à' => 'a', 'ả' => 'a', 'ã' => 'a', 'ạ' => 'a', 'ă' => 'a', 'ắ' => 'a', 'ằ' => 'a', 'ẳ' => 'a', 'ẵ' => 'a', 'ặ' => 'a', 'â' => 'a', 'ấ' => 'a', 'ầ' => 'a', 'ẩ' => 'a', 'ẫ' => 'a', 'ậ' => 'a', 'ú' => 'u', 'ù' => 'u', 'ủ' => 'u', 'ũ' => 'u', 'ụ' => 'u', 'ư' => 'u', 'ứ' => 'u', 'ừ' => 'u', 'ử' => 'u', 'ữ' => 'u', 'ự' => 'u', 'í' => 'i', 'ì' => 'i', 'ỉ' => 'i', 'ĩ' => 'i', 'ị' => 'i', 'ó' => 'o', 'ò' => 'o', 'ỏ' => 'o', 'õ' => 'o', 'ọ' => 'o', 'ô' => 'o', 'ố' => 'o', 'ồ' => 'ô', 'ổ' => 'o', 'ỗ' => 'o', 'ộ' => 'o', 'ơ' => 'o', 'ớ' => 'o', 'ờ' => 'o', 'ở' => 'o', 'ỡ' => 'o', 'ợ' => 'o', 'đ' => 'd', 'Đ' => 'D', 'ý' => 'y', 'ỳ' => 'y', 'ỷ' => 'y', 'ỹ' => 'y', 'ỵ' => 'y', 'Á' => 'A', 'À' => 'A', 'Ả' => 'A', 'Ã' => 'A', 'Ạ' => 'A', 'Ă' => 'A', 'Ắ' => 'A', 'Ẳ' => 'A', 'Ẵ' => 'A', 'Ặ' => 'A', 'Â' => 'A', 'Ấ' => 'A', 'Ẩ' => 'A', 'Ẫ' => 'A', 'Ậ' => 'A', 'É' => 'E', 'È' => 'E', 'Ẻ' => 'E', 'Ẽ' => 'E', 'Ẹ' => 'E', 'Ế' => 'E', 'Ề' => 'E', 'Ể' => 'E', 'Ễ' => 'E', 'Ệ' => 'E', 'Ú' => 'U', 'Ù' => 'U', 'Ủ' => 'U', 'Ũ' => 'U', 'Ụ' => 'U', 'Ư' => 'U', 'Ứ' => 'U', 'Ừ' => 'U', 'Ử' => 'U', 'Ữ' => 'U', 'Ự' => 'U', 'Í' => 'I', 'Ì' => 'I', 'Ỉ' => 'I', 'Ĩ' => 'I', 'Ị' => 'I', 'Ó' => 'O', 'Ò' => 'O', 'Ỏ' => 'O', 'Õ' => 'O', 'Ọ' => 'O', 'Ô' => 'O', 'Ố' => 'O', 'Ổ' => 'O', 'Ỗ' => 'O', 'Ộ' => 'O', 'Ơ' => 'O', 'Ớ' => 'O', 'Ờ' => 'O', 'Ở' => 'O', 'Ỡ' => 'O', 'Ợ' => 'O', 'Ý' => 'Y', 'Ỳ' => 'Y', 'Ỷ' => 'Y', 'Ỹ' => 'Y', 'Ỵ' => 'Y', ' ' => '-' )
        ;        
        $content = strtr ( $content, $trans ); // chuoi da duoc bo dau
        return $content;
	}
	
	
}
?>
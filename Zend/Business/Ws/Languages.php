<?php
class Business_Ws_Languages extends Business_Abstract
{
	private $_tablename = 'ws_language';
	const KEY_LIST = 'lang.list';           //key of list
	const KEY_COUNT = 'lang.totalcount'; 	//key of total count
	const KEY_LIST_PAGING = 'lang.list.';	//key of prefix with paging
	const KEY_DETAIL = 'lang.detail.%s';	//key of detail
	const KEY_DETAIL_NAME = 'lang.detailname.%s';	//key of detail by name
	CONST PAGING_NUM_RECORDS = 100;
	CONST PAGING_MAX_PAGE = 10;
	private $_paging = null;
	private $_expire = -1; // default no cached
	
	private static $_instance = null; 
	
	function __construct()
	{			
        }
	private function getKeyListPaging()
	{
		return self::KEY_LIST_PAGING;
	}
	
	private function getKeyCount()
	{
		return self::KEY_COUNT;
	}
	
	private function getKeyList()
	{
		return sprintf(self::KEY_LIST);
	}
	
	private function getKeyDetail($id)
	{
		return sprintf(self::KEY_DETAIL,$id);
	}
	
	private function getKeyDetailByName($lang)
	{
		return sprintf(self::KEY_DETAIL_NAME,$lang);
	}
	
	/**
	 * get instance of Business_Ws_Languages
	 *
	 * @return Business_Ws_Languages
	 */
	public static function getInstance()
	{
		if(self::$_instance == null)
		{
			self::$_instance = new self();
		}
		return self::$_instance;
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
	
	public function getList()
	{
		$cache = $this->getCacheInstance();
		$key = $this->getKeyList();
		$result = $cache->getCache($key);
		if($result === FALSE)
		{
			$db = $this->getDbConnection();
			$query = "SELECT * FROM " . $this->_tablename . " ORDER BY enable desc, lang";
			$result = $db->fetchAll($query);
			if(!is_null($result) && is_array($result))
			{
				$cache->setCache($key, $result);
			}			
		}
		return $result;
	}
	
	public function getTotalCount()
	{
		$cache = $this->getCacheInstance();
		$key = $this->getKeyCount();
		$result = $cache->getCache($key);
		if($result === FALSE)
		{
			$db = $this->getDbConnection();
			$query = "SELECT COUNT(*) AS sum FROM " . $this->_tablename;
			$result = $db->fetchAll($query);
			if(!is_null($result) && is_array($result) && !empty($result))
			{
				$result = intval($result[0]['sum']);
			}
			else $result = 0;
			$cache->setCache($key,$result);
		}
		return $result;
	}
	
	public function getDefaultLangItem()
	{
		$list = $this->getList();
		if($list == null || !is_array($list) || empty($list)) return null;
		
		for($i=0;$i<count($list);$i++)
		{
			if($list[$i]['default'] == '1') break;
		}
		if($i<count($list)) return $list[$i];
		else return null;
	}
	
	public function getDefaultLangName()
	{
		$item = $this->getDefaultLangItem();
		if($item != null) return $item['lang'];
		else return '';
	}
	
	public function getDefaultLang()
	{
		$item = $this->getDefaultLangItem();
		if($item != null) return $item['langid'];
		else return '';
	}
	
	public function getLangId($lang)
	{
		$item = $this->getDetailByName($lang);		
		if($item != null) return intval($item['langid']);
		else return 0;
	}
	
	public function getDetailByName($lang)
	{
		$cache = $this->getCacheInstance();
		$key = $this->getKeyDetailByName($lang);
		$result = $cache->getCache($key);
		
		if($result === FALSE)
		{
			$db = $this->getDbConnection();
			$query = "SELECT * FROM " . $this->_tablename . " WHERE lang= ? ";
			$data = array($lang);
			$result = $db->fetchAll($query,$data);
			if(!is_null($result) && is_array($result) && count($result)>0)
			{
				$result = $result[0];
				$cache->setCache($key,$result);
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
			$query = "SELECT * FROM " .$this->_tablename . " WHERE langid='" . parent::adaptSQL($id) . "'";
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
			$this->_deleteAllCache();	
		}
	}
	
	public function update($id, $data)
	{
		$where = array();
		$where[] = "langid='" . parent::adaptSQL($id) . "'";
		$db = $this->getDbConnection();
		$result = $db->update($this->_tablename, $data,$where);
		if($result > 0)
		{
			$this->_deleteAllCache($id);
		}
	}
	
	public function delete($id)
	{
		$where = array();
		$where[] = "langid='" . parent::adaptSQL($id) . "'";
		$db = $this->getDbConnection();
		$result = $db->delete($this->_tablename, $where);
		if($result > 0)
		{
			$this->_deleteAllCache($id);
		}
	}
	
	public function getListWithPaging($offset, $record)
	{
		$paging = $this->_getPagingBusiness();
		$keyprefix = $this->getKeyListPaging();
		$params = array();
		$result = $paging->getData($offset, $record, $keyprefix, $params);
		return $result;
	}
	
	public function getRealData($page)
	{
		$offset = 0;
		$records = self::PAGING_NUM_RECORDS;
		if($page > 0)
		{
			$offset = $page * $records;
		}
		
		$db = $this->getDbConnection();
		$query = "SELECT * FROM " . $this->_tablename . " ORDER BY enable desc, lang LIMIT " . $offset . "," . $records;
		$result = $db->fetchAll($query);
		if(!is_null($result) && is_array($result))
		{
			return $result;
		}
		return array();
	}
	
	
	///private functions /////

	private function _deleteAllCache($id = null)
	{
		$cache = $this->getCacheInstance();
		$key = $this->getKeyList();
		$cache->deleteCache($key);
		$key = $this->getKeyCount();
		$cache->deleteCache($key);
		if($id != null)
		{
			$item = $this->getDetail($id);
			if($item != null)
			{
				$lang = $item['lang'];
				$key = $this->getKeyDetailByName($lang);
				$cache->deleteCache($key);
			}
			$key = $this->getKeyDetail($id);
			$cache->deleteCache($key);
		}
		$this->_deletepagingCache();
	}
	
	private function _deletepagingCache()
	{
		$paging = $this->_getPagingBusiness();
		$keyprefix = $this->getKeyListPaging();
		$paging->clearCachePaging($keyprefix);
	}
	
	/**
	 * Enter description here...
	 *
	 * @return Maro_Paging_Interface
	 */
	private function _getPagingBusiness()
	{
		if($this->_paging != null) return $this->_paging;
		
		$cache = $this->getCacheInstance();
		$this->_paging = new Maro_Paging_Common($cache, array($this, "getRealData"), self::PAGING_MAX_PAGE, self::PAGING_NUM_RECORDS);
		return $this->_paging;
	}
}

?>
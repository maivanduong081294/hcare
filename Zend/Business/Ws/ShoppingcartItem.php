<?php

class Business_Ws_ShoppingcartItem extends Business_Abstract
{
	private $_tablename = 'ws_shoppingcartitem';

	const KEY_LIST = 'shoppingcart.list.%s';			//key of list by language
	const KEY_LIST_PID = 'shoppingcart.list.pid.%s';			//key of list by language
	const KEY_DETAIL = 'shoppingcart.detail.%s';	//key of detail.id

	private static $_instance = null;

	function __construct()
	{
	}

	//public static function
	/**
	 * get instance of Business_Ws_ShoppingcartItem
	 *
	 * @return Business_Ws_ShoppingcartItem
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

	private function getKeyList($cartid)
	{
		return sprintf(self::KEY_LIST,$cartid);
	}
        
	private function getKeyListByProductid($pid)
	{
		return sprintf(self::KEY_LIST_PID,$pid);
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

        public function getLastOrderInfoByPID($pid) {
            $db = $this->getDbConnection();
            $query = "SELECT * FROM " . $this->_tablename . " WHERE productsid=? ORDER BY itemid DESC LIMIT 0,1";
            $data = array($pid);
            $result = $db->fetchAll($query,$data);
            return $result[0];
        }

        public function getTotalOrderByPID($pid) {
            $db = $this->getDbConnection();
            $query = "SELECT count(itemid) as total FROM " . $this->_tablename . " WHERE productsid=?";
            $data = array($pid);
            $result = $db->fetchAll($query,$data);
            return (int)$result[0]["total"];
        }

        
        public function getListByProductid($product_id)
	{
		$cache = $this->getCacheInstance();
		$key = $this->getKeyListByProductid($product_id);
		$result = $cache->getCache($key);
		if($result === FALSE)
		{
			$db = $this->getDbConnection();
			$query = "SELECT * FROM " . $this->_tablename . " WHERE productsid=?";
			$data = array($product_id);
			$result = $db->fetchAll($query,$data);
			if(!is_null($result) && is_array($result))
			{
				$cache->setCache($key, $result);
			}
		}
		return $result;
	}
        
        public function getTotalBuyedProducts($product_id, $datetime)
	{
            $db = $this->getDbConnection();
//            $query = "SELECT sum(numbers) as total FROM " . $this->_tablename . " WHERE productsid=?";
            $query = "SELECT sum(t2.numbers) total FROM `ws_shoppingcartmodule` t1, ws_shoppingcartitem t2  WHERE t1.`shoppingcartid` = t2.`shoppingcartid` AND t1.`created` >= '".$datetime."'  AND t2.productsid = ?";
            
            $data = array($product_id);
            $result = $db->fetchAll($query,$data);
            return (int)$result[0]['total'];
	}
        
	public function getList($shoppingcartid)
	{
		$cache = $this->getCacheInstance();
		$key = $this->getKeyList($shoppingcartid);
		$result = $cache->getCache($key);
		if($result === FALSE)
		{
			$db = $this->getDbConnection();
			$query = "SELECT * FROM " . $this->_tablename . " WHERE shoppingcartid=?";
			$data = array($shoppingcartid);
			$result = $db->fetchAll($query,$data);
			if(!is_null($result) && is_array($result))
			{
				$cache->setCache($key, $result);
			}
		}
		return $result;
	}

        public function getTotalByCartID($cartid){
            $db = $this->getDbConnection();
            $query = "SELECT count(*) as total FROM " . $this->_tablename . " WHERE shoppingcartid=?";
            $data = array($shoppingcartid);
            $result = $db->fetchAll($query,$data);
            if(!is_null($result) && is_array($result))
            {
                return (int)$result[0]['total'];
            }
            return 0;
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

	public function insert($shoppingcartid,$lang, $data)
	{            
            
		$data['lang'] = $lang;
                $data['shoppingcartid']= $shoppingcartid;
		$db = $this->getDbConnection();
                
		$result = $db->insert($this->_tablename, $data);
		if($result > 0)
		{
			$this->_deleteAllCache($shoppingcartid,$lang);
                        return $db->lastInsertId();
		}

	}

	public function update($id,$shoppingcartid, $lang, $data)
	{
		if(array_key_exists('lang',$data)) unset($data['lang']);
		$where = array();
		$where[] = "itemid='" . parent::adaptSQL($id) . "'";
		$db = $this->getDbConnection();
		$result = $db->update($this->_tablename, $data,$where);
		if($result > 0)
		{
			$this->_deleteAllCache($lang,$shoppingcartid,$id);
		}
	}

	public function delete($id,$lang)
	{
		//get current menu
		$current = $this->getDetail($id);

		$where = array();
		$where[] = "shoppingcartid='" . parent::adaptSQL($id) . "'";
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
                $cache->flushAll();
	}
        
}
?>
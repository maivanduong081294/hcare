<?php

class Business_Common_Products extends Business_Abstract
{
	private $_tablename = 'products';
	
	const KEY_LIST = 'product.list.%s';					//key list : product.list.module
	const KEY_LIST_BY_CATE = 'product.list.%s.%s'; 		//key list : product.list.module.cateid
	const KEY_DETAIL = 'product.detail.%s';				//key detail : product.productid
		
	private static $_instance = null; 
	
	function __construct()
	{			
	}	
	
	private function getKeyList($module)
	{
		return sprintf(Business_Common_Products::KEY_LIST,$module);
	}
	
	private function getKeyListByCate($module, $cateid)
	{
		return sprintf(Business_Common_Products::KEY_LIST_BY_CATE,$module, $cateid);
	}
	
	private function getKeyDetail($id)
	{
		return sprintf(Business_Common_Products::KEY_DETAIL,$id);
	}
	
	/**
	 * get instance of Business_Common_Products
	 *
	 * @return Business_Common_Products
	 */
	public static function getInstance()
	{
		if(self::$_instance == null)
		{
			self::$_instance = new Business_Common_Products();
		}
		return self::$_instance;
	}	
	
	/**
	 * Get DB Connection
	 *
	 * @return Zend_Db_Adapter_Abstract
	 */
	function getDbConnection()
	{		
		$db    	= Globals::getDbConnection('maindb');		
		return $db;	
	}
	
	/**
	 * Enter description here...
	 *
	 * @return Maro_Cache
	 */
	function getCacheInstance()
	{
		$cache = GlobalCache::getCacheInstance();
		return $cache;
	}
	
	public function getProductList($module = '')
	{
		$cache = $this->getCacheInstance();
		$key = $this->getKeyList($module);
		$result = $cache->getCache($key);
		if($result === FALSE)
		{
			$db = $this->getDbConnection();
			$query = "SELECT productid,module,cateid,title,shortdes,thumb_small,thumb_large,ordering,posteddate,status,ishot,metadata FROM " 
					. $this->_tablename . " WHERE module = ? ORDER BY productid";
			$data = array(
				$module
			);
			$result = $db->fetchAll($query, $data);
			if(!is_null($result) && is_array($result))
			{
				$cache->setCache($key, $result);
			}
		}
		return $result;
	}
	
	public function getProductListByCate($module = '', $cateid = null)
	{
		$cache = $this->getCacheInstance();
		$key = $this->getKeyListByCate($module, $cateid);
		$result = $cache->getCache($key);
		if($result === FALSE)
		{
			$db = $this->getDbConnection();
			$query = "SELECT productid,module,cateid,title,thumb,posteddate,status,ishot,metadata FROM "
					. $this->_tablename . " WHERE module = ? AND cateid = ? ORDER BY productid";
			$data = array(
				$module,
				$cateid
			);
			$result = $db->fetchAll($query, $data);
			if(!is_null($result) && is_array($result))
			{
				$cache->setCache($key, $result);					
			}
		}
	}
	
	public function getProductDetail($module, $productid)
	{
		$cache = $this->getCacheInstance();
		$key = $this->getKeyDetail($productid);
		$result = $cache->getCache($key);
		if($result === FALSE)
		{
			$db = $this->getDbConnection();
			$query = "SELECT * FROM " . $this->_tablename . " WHERE productid = ?";
			$data = array(
				$productid
			);
			
			$result = $db->fetchAll($query,$data);			
			if(!is_null($result) && is_array($result) && count($result) == 1)
			{
				$result = $result[0];
				$cache->setCache($key, $result);
			}
		}
		return $result;
	}
	
	public function insertProduct($module, $data)
	{
		$cateid = -1;
		if(array_key_exists('cateid',$data)) $cateid = $data['cateid'];
		$data['module'] = $module;
				
		$db = $this->getDbConnection();
		$db->insert($this->_tablename, $data);
		//clear cache
		$cache = $this->getCacheInstance();
		if($cateid != -1)
		{
			$key = $this->getKeyListByCate($module, $cateid);
			$cache->deleteCache($key);
		}
		$key = $this->getKeyList($module);
		$cache->deleteCache($key);
	}
	
	public function updateProduct($module, $productid, $data)
	{
		$cateid = -1;
		if(array_key_exists('module',$data)) unset($data['module']);
		//get current product
		$product = $this->getProductDetail($module, $productid);
		if($product != null)
		{
			$cateid = $product['cateid'];
			$db = $this->getDbConnection();
			$where = array();
			$where[] = "productid='" . parent::adaptSQL($productid) . "'";			
			$db->update($this->_tablename, $data, $where);
			//clear cache
			$cache = $this->getCacheInstance();
			$key = $this->getKeyDetail($productid);
			$cache->deleteCache($key);
			$key = $this->getKeyList($module);
			$cache->deleteCache($key);
			$key = $this->getKeyListByCate($module, $cateid);
			$cache->deleteCache($key);
		}		
	}
	
	public function deleteProduct($module, $productid)
	{
		$cateid = -1;		
		//get current product
		$product = $this->getProductDetail($module, $productid);
		if($product != null)
		{
			$cateid = $product['cateid'];
			$db = $this->getDbConnection();
			$where = array();
			$where[] = "productid='" . parent::adaptSQL($productid) . "'";
			$db->delete($this->_tablename, $where);
			//clear cache
			$cache = $this->getCacheInstance();
			$key = $this->getKeyDetail($productid);
			$cache->deleteCache($key);
			$key = $this->getKeyList($module);
			$cache->deleteCache($key);
			$key = $this->getKeyListByCate($module, $cateid);
			$cache->deleteCache($key);
		}
	}
	
}

?>
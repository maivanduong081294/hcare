<?php

class Business_Addon_ProductsDetail extends Business_Abstract
{
	private $_tablename = 'addon_products_detail';
        
        const KEY_LIST = 'addon.products.detail.list.%s';			
        const KEY_LIST_ALL = 'addon.products.list.all.%s';			
        const KEY_LIST_SIZE = 'addon.products.list.sizeid.%s.%s';
        const KEY_LIST_COLOR = 'addon.products.list.colorid.%s.%s';
        const KEY_LIST_DISTINCT_COLOR = 'addon.products.detail.distinct.color.%s';			//key of list by language
	
	private static $_instance = null; 
	
	function __construct()
	{
		
	}
	
	/**
	 * get instance of Business_Addon_ProductsDetail
	 *
	 * @return Business_Addon_ProductsDetail
	 */
	public static function getInstance()
	{
		if(self::$_instance == null)
		{
			self::$_instance = new Business_Addon_ProductsDetail();
		}
		return self::$_instance;
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

        private function getKeyListDistinctColor($itemid)
	{
		return sprintf(self::KEY_LIST_DISTINCT_COLOR, $itemid);
	}

        private function getKeyListAll($itemid)
	{
		return sprintf(self::KEY_LIST_ALL, $itemid);
	}

        private function getKeyListBySizeid($itemid, $sizeid)
	{
		return sprintf(self::KEY_LIST_SIZE, $itemid, $sizeid);
	}

        private function getKeyListByColorid($itemid, $colorid)
	{
		return sprintf(self::KEY_LIST_COLOR, $itemid, $colorid);
	}

        private function getCacheInstance()

	{
		$cache = GlobalCache::getCacheInstance('ws');
		return $cache;
	}
        
        public function getColorBySizeIDAndPrice($itemid, $sizeid, $price){
            $db = $this->getDbConnection();
            $query = "select * from " . $this->_tablename . " where itemid = $itemid AND sizeid = $sizeid AND price <= $price";
            $result = $db->fetchAll($query);
            return $result;
        }

        public function getDistinctSizeByPrice($itemid, $price)
	{
            $db = $this->getDbConnection();
            $query = "select distinct(sizeid) from " . $this->_tablename . " where itemid = $itemid AND price <= $price";
            $result = $db->fetchAll($query);
            return $result;
	}
	

        public function getDetailByItemidSizeAndColor($itemid, $sid, $cid)
	{
            $banners_id = intval($banners_id);
            $db = $this->getDbConnection();
            $query = " SELECT * FROM " . $this->_tablename ." WHERE itemid = $itemid AND colorid = $cid AND sizeid = $sid";
            $result = $db->fetchAll($query);
            if($result != null && is_array($result))
            {
                    return $result[0];
            }
            else return null;
	}
        
        public function getListColorBySizeAndItemid($itemid, $sizeid){
            $cache = $this->getCacheInstance();
            $key = $this->getKeyListByColorid($itemid, $colorid);
            $result = $cache->getCache($key);

            if ($result === FALSE){
                $db = $this->getDbConnection();
                $query = "select * from " . $this->_tablename . " where itemid = $itemid AND sizeid = $sizeid";
                $result = $db->fetchAll($query);
                if (is_array($result)){
                    $cache->setCache($key, $result);
                    return $result;
                }
            }
            return $result;
        }

        public function getListByItemidAndColorid($itemid, $colorid){
            $cache = $this->getCacheInstance();
            $key = $this->getKeyListByColorid($itemid, $colorid);
            $result = $cache->getCache($key);

            if ($result === FALSE){
                $db = $this->getDbConnection();
                $query = "select * from " . $this->_tablename . " where itemid = $itemid AND colorid = $colorid";
                $result = $db->fetchAll($query);
                if (is_array($result)){
                    $cache->setCache($key, $result);
                    return $result;
                }
                
            }
            return $result;
        }

        public function getSizeByColor($colorid){
            $db = $this->getDbConnection();
            $query = "select * from " . $this->_tablename . " where colorid = $colorid ";
            $result = $db->fetchAll($query);
            return $result;
        }

        public function getListColorByItemid($itemid){
            $cache = $this->getCacheInstance();
            $key = $this->getKeyListDistinctColor($itemid);
            $result = $cache->getCache($key);
            if ($result === FALSE){
                $db = $this->getDbConnection();
                $query = "select distinct(colorid) as colorid from " . $this->_tablename . " where itemid = $itemid ";
                $result = $db->fetchAll($query);
                if(is_array($result)){
                    $cache->setCache($key, $result);
                    return $result;
                }
            }
            return $result;
        }

	public function getListByItemid($itemid)
	{
            $cache = $this->getCacheInstance();
            $key = $this->getKeyListAll($itemid);
            $result = $cache->getCache($key);
            if ($result === FALSE){
                $db = $this->getDbConnection();
                $query = "select * from " . $this->_tablename . " where itemid = $itemid ";
                $result = $db->fetchAll($query);
                if (is_array($result))
                {
                    $cache->setCache($key, $result);
                    return $result;
                }                
            }
            return $result;
	}
	
	public function getDetail($pid, $sid)
	{
            $banners_id = intval($banners_id);
            $db = $this->getDbConnection();
            $query = " SELECT * FROM " . $this->_tablename ." WHERE itemid = $pid AND sizeid = $sid";
            $result = $db->fetchAll($query);
            if($result != null && is_array($result))
            {
                    return $result[0];
            }
            else return null;
	}
	
	public function update($pid, $sid, $cid, $data)
	{
            $db = $this->getDbConnection();
            $where = array();
            $where[] = "itemid='" . parent::adaptSQL($pid) . "'";
            $where[] = "sizeid='" . parent::adaptSQL($sid) . "'";
            $where[] = "colorid='" . parent::adaptSQL($cid) . "'";
            try
            {
                    $result = $db->update($this->_tablename, $data, $where);
                    $this->_deleteAllCached($pid, $sid, $cid);
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
                $this->_deleteAllCached($data['itemid'], $data['sizeid'], $data['colorid']);
                return $result;
	}
	
	public function delete($pid, $sid, $cid)
	{
            $detail = $this->getDetail($pid, $sid);
            $db = $this->getDbConnection();
            $where = array();
            $where[] = "itemid='" . parent::adaptSQL($pid) . "'";
            $where[] = "sizeid='" . parent::adaptSQL($sid) . "'";
            $result = $db->delete($this->_tablename,$where);
            $this->_deleteAllCached($pid, $detail['sizeid'], $detail['colorid']);
                
            return $result;
	}

        public function deletebyPid($pid)
	{
            $list = $this->getListByItemid($pid);
            foreach($list as $item){
                $this->_deleteAllCached($pid, $item['sizeid'], $item['colorid']);
            }
            $db = $this->getDbConnection();
            $where = array();
            $where[] = "itemid='" . parent::adaptSQL($pid) . "'";
            $result = $db->delete($this->_tablename,$where);            
            return $result;
	}

        private function _deleteAllCached($itemid, $sizeid, $colorid){
            $cache = $this->getCacheInstance();
            $key = $this->getKeyListDistinctColor($itemid);
            $cache->deleteCache($key);
            $key = $this->getKeyListAll($itemid);
            $cache->deleteCache($key);
            $key = $this->getKeyListByColorid($itemid, $colorid);
            $cache->deleteCache($key);
            $key = $this->getKeyListBySizeid($itemid, $sizeid);
            $cache->deleteCache($key);            

        }
}

?>
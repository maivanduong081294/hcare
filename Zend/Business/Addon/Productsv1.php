<?php

class Business_Addon_Productsv1 extends Business_Abstract
{
	private $_tablename = 'hn_products';


	private static $_instance = null;

	function __construct()
	{
	}

        
        private function getKeyList($cateid) {
            return 'addon.productsv1.list.cateid.'.$cateid;
        }
        
	//public static function
	/**
	 * get instance of Business_Addon_Productsv1
	 *
	 * @return Business_Addon_Productsv1
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
		$db    	= Globals::getDbConnection('hnamv1');
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

	public function getList($cateid=''){
            
            $db = $this->getDbConnection();
            $cache = $this->getCacheInstance();
            $key = $this->getKeyList($cateid);
            if ($cateid != '')
                $query = "SELECT * FROM " . $this->_tablename . " WHERE category_id = $cateid";            
            else
                $query = "SELECT * FROM " . $this->_tablename . " WHERE 1";            
            $result = $db->fetchAll($query);
            $cache->setCache($key, $result);
            return $result;
        }

	public function getListByPid($pid)
	{
		$cache = $this->getCacheInstance();
		$key = $this->getKeyList($pid);
		$result = $cache->getCache($key);
                
		if($result == FALSE)
		{
			$db = $this->getDbConnection();
			$query = "SELECT f.* FROM " . $this->_tablename . " f ";
			$query = "SELECT f.*, fd.value, fd.pid FROM " . $this->_tablename . " f ";
                        $query .= "LEFT JOIN hn_features_data fd ON f.fid = fd.fid AND fd.parentid = f.parentid AND fd.pid = ? ";                        
                        $query .= " ORDER BY ordering ASC";
//                        $query .= " GROUP BY f.parentid";
			$data = array($pid);
			$result = $db->fetchAll($query,$data);
//			if(!is_null($result) && is_array($result))
//			{
				$cache->setCache($key, $result, 180);
//			}
		}
                
		return $result;
	}

        
        public function update($fid, $item) {           
            $where = array();
            $where[] = "fid='" . parent::adaptSQL($fid) . "'";
            $db = $this->getDbConnection();
            $result = $db->update($this->_tablename, $item,$where);
            if($result > 0)
            {
                $this->_deleteAllCache($lang,$id);			
            }
        }
        
	///private functions /////
	private function _deleteAllCache()
	{
		$cache = $this->getCacheInstance();
		$key = $this->getKeyList();
		$cache->deleteCache($key);
	}

}

?>

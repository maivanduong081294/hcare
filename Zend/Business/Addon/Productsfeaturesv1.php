<?php

class Business_Addon_Productsfeaturesv1 extends Business_Abstract
{
	private $_tablename = 'hn_features_data';


	private static $_instance = null;

	function __construct()
	{
	}

        
        private function getKeyList($parentid) {
            return 'addon.productsFeaturesv1.list.parentid.'.$parentid;
        }
        
	//public static function
	/**
	 * get instance of Business_Addon_Productsfeaturesv1
	 *
	 * @return Business_Addon_Productsfeaturesv1
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

	public function getList($parentid){
            $db = $this->getDbConnection();
            $cache = $this->getCacheInstance();
            $key = $this->getKeyList($parentid);
            $query = "SELECT * FROM " . $this->_tablename . " WHERE parentid = $parentid";            
            $result = $db->fetchAll($query);
            $cache->setCache($key, $result);
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

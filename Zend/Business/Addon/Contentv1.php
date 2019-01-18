<?php

class Business_Addon_Contentv1 extends Business_Abstract
{
	private $_tablename = 'hn_content';


	private static $_instance = null;

	function __construct()
	{
	}

        
        private function getKeyList() {
            return 'addon.accesories.list';
        }
        
	//public static function
	/**
	 * get instance of Business_Addon_Contentv1
	 *
	 * @return Business_Addon_Contentv1
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

	public function getListByCateid($cateid){
            $db = $this->getDbConnection();
            $query = "SELECT * FROM " . $this->_tablename . " WHERE catid = $cateid";            
            $result = $db->fetchAll($query);
            return $result;
        }

        public function update($id, $item) {           
            $where = array();
            $where[] = "id='" . parent::adaptSQL($id) . "'";
            $db = $this->getDbConnection();
            $result = $db->update($this->_tablename, $item,$where);
        }
        
        public function insert($item) {           
            $where = array();
            $db = $this->getDbConnection();
            $result = $db->insert($this->_tablename, $item);
        }
}

?>

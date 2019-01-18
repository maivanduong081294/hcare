<?php

class Business_Addon_Poll extends Business_Abstract
{
	private $_tablename = 'addon_poll';
	
	const KEY_LIST_ALL = 'poll.list.all.%s';			//key of list by language
	const KEY_DETAIL = 'poll.detail.%s';
	
	private static $_instance = null; 
	
	function __construct()
	{
		
	}
	
	/**
	 * get instance of Business_Addon_Poll
	 *
	 * @return Business_Addon_Poll
	 */
	public static function getInstance()
	{
		if(self::$_instance == null)
		{
			self::$_instance = new Business_Addon_Poll();
		}
		return self::$_instance;
	}

	private function getKeyDetail($id)
	{
		return sprintf(self::KEY_DETAIL,$id);
	}

        private function getKeyList($enabled){
            return sprintf(self::KEY_LIST_ALL,$enabled); 
        }
        
	/**
	 * get Zend_Db connection
	 *
	 * @return Zend_Db_Adapter_Abstract
	 */
	function getDbConnection()
	{		
		$db = Globals::getDbConnection('maindb', false);
		return $db;	
	}
	
        private function getCacheInstance()
	{
		$cache = GlobalCache::getCacheInstance('ws');
		return $cache;
	}
        public function getList($enabled='', $limit='')
	{            
            $cache = $this->getCacheInstance();
            $key = $this->getKeyList($enabled.$limit);
            $result = $cache->getCache($key);
            if ($enabled === '')
                $where = ' 1=1';
            else
                $where = ' enabled=' . (int)$enabled;
            
            ($limit === '' ? true : $limit = ' LIMIT 0, ' . $limit);
            if ($result === FALSE){
                $db = $this->getDbConnection();
                
                $query = "select * from " . $this->_tablename . " where $where order by ordering asc" . $limit;
                
                $result = $db->fetchAll($query);
                
                $cache->setCache($key, $result);
                
            }
            return $result;
	}
	public function getDetail($itemid)
	{
            $cache = $this->getCacheInstance();
            $key = $this->getKeyDetail($itemid);            
            $result = $cache->getCache($key);
            if($result === FALSE)
            {
                $itemid = intval($itemid);
                $db = $this->getDbConnection();
                $query = " SELECT * FROM " . $this->_tablename ." WHERE item_id = ?";
                $data = array($itemid);
                $result = $db->fetchAll($query,$data);
                if($result != null && is_array($result))
                {
                        $cache->setCache($key, $result[0]);
			return $result[0];
                }
            }
            return $result;
	}
	public function update($itemid, $data)
	{
		$db = $this->getDbConnection();
		$where = array();
		$where[] = "item_id = '" . parent::adaptSQL($itemid) . "'";                
		try
		{			
			$result = $db->update($this->_tablename, $data, $where);

                        if($result > 0)
                        {
			$this->_deleteAllCache($banners_id);
                        }
			
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
                $this->_deleteAllCache();
                return $db->lastInsertId();
	}
	
	public function delete($itemid)
	{
                $current = $this->getDetail($itemid);
		$db = $this->getDbConnection();
		$where = array();
		$where[] = "item_id='" . parent::adaptSQL($itemid) . "'";
		$result = $db->delete($this->_tablename,$where);
                if($result > 0)
		{
			$this->_deleteAllCache($itemid);
		}
                return $result;
	}

        private function _deleteAllCache($itemid = null)
	{
		$cache = $this->getCacheInstance();
                $key = $this->getKeyDetail((int)$itemid);
                $cache->deleteCache($key);
                $key = $this->getKeyList('');
                $cache->deleteCache($key);
                $key = $this->getKeyList(1);
                $cache->deleteCache($key);
                $key = $this->getKeyList('11');
                $cache->deleteCache($key);
                
	}
        
        
        public function getMaxOrdering(){
            $db = $this->getDbConnection();
            $query = "SELECT max(ordering) as max FROM " . $this->_tablename . " WHERE 1 ";
            $result = $db->fetchAll($query);
            if(!is_null($result) && is_array($result))
            {
                    return $result[0]['max'];
            }
            return 0;
        }
        
        public function getDetailByMyOrdering($ordering)
        {
            $db = $this->getDbConnection();
            $query = "SELECT * FROM " . $this->_tablename . " WHERE ordering = ? ";
            $data = array($ordering);
            $result = $db->fetchAll($query,$data);

            if(!is_null($result) && is_array($result))
            {
                    return $result[0];
            }
            return null;
        }
}

?>
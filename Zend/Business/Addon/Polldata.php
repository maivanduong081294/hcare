<?php

class Business_Addon_Polldata extends Business_Abstract
{
	private $_tablename = 'addon_poll_data';
	
	const KEY_DETAIL = 'addon_poll_data.detail.%s';			//key of list by language
	const KEY_LIST_ALL = 'addon_poll_data.list.all.%s';			//key of list by language
	
	private static $_instance = null; 
	
	function __construct()
	{
		
	}
	
	/**
	 * get instance of Business_Addon_Polldata
	 *
	 * @return Business_Addon_Polldata
	 */
	public static function getInstance()
	{
		if(self::$_instance == null)
		{
			self::$_instance = new Business_Addon_Polldata();
		}
		return self::$_instance;
	}

	private function getKeyDetail($id)
	{
		return sprintf(self::KEY_DETAIL,$id);
	}

        private function getKeyList($poll_id){
            return sprintf(self::KEY_LIST_ALL,$poll_id); 
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
	
        private function getCacheInstance()
	{
		$cache = GlobalCache::getCacheInstance('ws');
		return $cache;
	}
        public function getList($poll_id)
	{            
            $cache = $this->getCacheInstance();
            $key = $this->getKeyList($poll_id.$limit.$enabled);
            $result = $cache->getCache($key);
            if ($result === FALSE){
                
                $db = $this->getDbConnection();
                
                $query = "select * from " . $this->_tablename . " where poll_id = " . (int)$poll_id . " order by item_id asc";
                
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
			$this->_deleteAllCache($itemid, $data['poll_id']);
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
                $this->_deleteAllCache($itemid, $data['poll_id']);
                return $result;
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
			$this->_deleteAllCache($itemid, $current['poll_id']);
		}
                return $result;
	}
        
        public function deleteByPollId($pollid)
	{
		$db = $this->getDbConnection();
		$where = array();
		$where[] = "poll_id='" . parent::adaptSQL($pollid) . "'";
		$result = $db->delete($this->_tablename,$where);
                if($result > 0)
		{
			$this->_deleteAllCache(null, $current['poll_id']);
		}
                return $result;
	}

        private function _deleteAllCache($itemid = null, $poll_id=null)
	{
		$cache = $this->getCacheInstance();
                $key = $this->getKeyDetail((int)$itemid);
                $cache->deleteCache($key);
                $key = $this->getKeyList($poll_id);
                $cache->deleteCache($key);
	}
        
}

?>
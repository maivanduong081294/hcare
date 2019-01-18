<?php

class Business_Ws_Online extends Business_Abstract
{
	private $_tablename = 'ws_online';
	
	private static $_instance = null; 
	
        private $_expired = 180; // seconds (60*2)

	function __construct()
	{			
	}
	
	//public static function
	/**
	 * get instance of Business_Ws_Online
	 *
	 * @return Business_Ws_Online
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
	
        public function getTotal() {
            $curdate = strtotime(date('Y-m-d H:i:s'));

            $exec_date = $curdate - $this->_expired;

            $date = date('Y-m-d H:i:s',$exec_date);

            $db = $this->getDbConnection();
            $cache = $this->getCacheInstance();
            $key = 'ws.online.list.all';
            $result = $cache->getCache($key);
            if ($result === FALSE){
                $query = "SELECT count(*) as total FROM " . $this->_tablename . " WHERE datetime >= ?";
                $data = array($date);
                $result = $db->fetchAll($query,$data);
                if(!is_null($result) && is_array($result) && count($result) == 1)
                {
                    $result = $result[0]['total'];
                }

                $sql = "DELETE FROM $this->_tablename where datetime < '$date'";

                $db->query($sql);
                $cache->setCache($key, $result, 300);
//                return $result;
            }
            return $result;
        }

        public function getDetail($id) {
            $db = $this->getDbConnection();
            $query = "SELECT count(*) as total FROM " . $this->_tablename . " WHERE id = ?";
            $data = array($id);
            $result = $db->fetchAll($query,$data);
            if(!is_null($result) && is_array($result) && count($result) == 1)
            {
                $result = $result[0]['total'];
            }

            return intval($result);
        }

        public function insert($id)
	{
            if ($this->getDetail($id) > 0)
                    return;
            $data['id'] = $id;
            $data['datetime'] = date('Y-m-d H:i:s');
            $db = $this->getDbConnection();
            $result = $db->insert($this->_tablename, $data);
	}
}
?>
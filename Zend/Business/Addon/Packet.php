<?php

class Business_Addon_Packet extends Business_Abstract
{
	private $_tablename = 'addon_packet';
        
        const KEY_DETAIL = 'addon.packet.detail.%s';
	
	private static $_instance = null; 
	
	function __construct()
	{
		
	}
	
	/**
	 * get instance of Business_Addon_Packet
	 *
	 * @return Business_Addon_Packet
	 */
	public static function getInstance()
	{
		if(self::$_instance == null)
		{
			self::$_instance = new Business_Addon_Packet();
		}
		return self::$_instance;
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


        private function getKeyDetail($pitemid)
	{
		return sprintf(self::KEY_DETAIL, $pitemid);
	}

        public function getDetailByPItemid($pitemid)
	{
            $cache = $this->getCacheInstance();
            $key = $this->getKeyDetail($pitemid);
            $result = $cache->getCache($key);
            if ($result === FALSE) {
                $db = $this->getDbConnection();
                $query = " SELECT * FROM " . $this->_tablename ." WHERE products_itemid = $pitemid ";
                $result = $db->fetchAll($query);
                if($result != null && is_array($result))
                {
                    $cache->setCache($key, $result[0]);
                    $result = $result[0];
                }
            }
            return $result;
            
	}
        

	public function update($pitemid, $data)
	{
            $db = $this->getDbConnection();
            $where = array();
            $where[] = "products_itemid='" . parent::adaptSQL($pitemid) . "'";
            try
            {
                    $result = $db->update($this->_tablename, $data, $where);
                    $this->_deleteAllCached($pitemid);
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
                $this->_deleteAllCached($data['products_itemid']);
                return $result;
	}
	
	public function delete($pitemid)
	{
            $detail = $this->getDetail($pitemid);
            $db = $this->getDbConnection();
            $where = array();
            $where[] = "products_itemid='" . parent::adaptSQL($pitemid) . "'";
            $result = $db->delete($this->_tablename,$where);
            $this->_deleteAllCached($pitemid);
                
            return $result;
	}

        private function _deleteAllCached($pitemid){
            $cache = $this->getCacheInstance();
            $key = $this->getKeyDetail($pitemid);
            $cache->deleteCache($key);
        }
}

?>
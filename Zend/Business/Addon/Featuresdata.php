<?php

class Business_Addon_Featuresdata extends Business_Abstract
{
	private $_tablename = 'hn_features_data';

	const KEY_LIST = 'hn_features.data.list.%';			//key of list.questionid
	const KEY_DETAIL = 'hn_features.data.detail.%s.%s.%s';		//key of detail.id


	private static $_instance = null;

	function __construct()
	{
	}

	//public static function
	/**
	 * get instance of Business_Addon_Featuresdata
	 *
	 * @return Business_Addon_Featuresdata
	 */
	public static function getInstance()
	{
		if(self::$_instance == null)
		{
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	private function getKeyList($pid)
	{
		return sprintf(self::KEY_LIST, $pid);
	}
        
	private function getKeyDetail($phone_id, $parent_id, $f_id)
	{
		return sprintf(self::KEY_DETAIL, $phone_id, $parent_id, $f_id);
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

        public function deleteByPid($pid)  {
            $where = array();
            $where[] = "pid='" . parent::adaptSQL($pid) . "'";
            $db = $this->getDbConnection();
            $result = $db->delete($this->_tablename, $where);
        }
        
        public function getList(){
            $db = $this->getDbConnection();
            $query = "SELECT f.* FROM " . $this->_tablename . " f ";
            $result = $db->fetchAll($query);
            return $result;
        }
        
        public function getListByPid($pid){
            $db = $this->getDbConnection();
            $query = "SELECT f.* FROM " . $this->_tablename . " f WHERE f.pid = " . $pid;
            $result = $db->fetchAll($query);
            return $result;
        }
        
        public function getUniqueOpertor(){
            return array(                                
                'iOS'=>'iOS',
                'Android'=>'Android',
                'MeeGo'=>'MeeGo',
                'Symbian'=>'Symbian',
                'BlackBerry'=>'BlackBerry',
                'Microsoft Windows Mobile'=>'Microsoft Windows Mobile'
            );
        }        
        
        //not user
        public function _getUniqueOpertor(){
            $cache = $this->getCacheInstance();
            $key = 'hnamv2.addon.fd.operator';
            $result = $cache->getCache($key);
            
            if ($result === FALSE){
                $db = $this->getDbConnection();
                $query = "SELECT DISTINCT f.value FROM " . $this->_tablename . " f ";
                $query .= 'WHERE f.fid = 44';            
                $result = $db->fetchAll($query);
                $cache->setCache($key, $result);
            }
            
            return $result;
        }
        
        public function getDetail($phone_id, $parent_id, $f_id) {
            
		$cache = $this->getCacheInstance();
		$key = $this->getKeyDetail($phone_id, $parent_id, $f_id);                
//		$result = $cache->getCache($key);
                $result = FALSE;
		if($result === FALSE)
		{
                    $where[] = "pid = $phone_id";
                    $where[] = "parentid = $parent_id";
                    $where[] = "fid = $f_id";
                    
                    $where = implode(" AND ", $where);

                    $db = $this->getDbConnection();
                    $query = "SELECT * FROM " . $this->_tablename;
                    $query .= " WHERE $where";                    
                    
                    $result = $db->fetchAll($query);
                    
                    $result = $result[0];
		}
                
		return $result;            
        }
	
        public function update($fid, $pid, $parentid, $item) {           
            $where = array();
            $where[] = "fid='" . parent::adaptSQL($fid) . "'";
            $where[] = "pid='" . parent::adaptSQL($pid) . "'";
            $where[] = "parentid='" . parent::adaptSQL($parentid) . "'";
            $db = $this->getDbConnection();
            $result = $db->update($this->_tablename, $item,$where);
            if($result > 0)
            {
                $this->_deleteAllCache($lang,$id,'');			
            }
            return $result;
        }
        
        public function insert($fid, $pid, $parentid, $item) {           
            $where = array();
            $db = $this->getDbConnection();
            $result = $db->insert($this->_tablename, $item);
        }
	///private functions /////
	private function _deleteAllCache($phone_id, $parent_id, $f_id)
	{
		$cache = $this->getCacheInstance();
		$key = $this->getKeyDetail($phone_id, $parent_id, $f_id);  
		$cache->deleteCache($key);
                $key = 'hnamv2.addon.fd.operator';
                $cache->deleteCache($key);
	}

}

?>

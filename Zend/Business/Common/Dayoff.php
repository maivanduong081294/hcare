<?php

class Business_Common_Dayoff extends Business_Abstract
{
	private $_tablename = 'dayoff';
	
	const KEY_LIST = 'dayoff.list';
	const KEY_DETAIL = 'dayoff.uid.%s';
	
	protected static $_current_rights = null;
	
	protected static $_instance = null; 

	function __construct()
	{
		
	}
	/**
	 * get instance of Business_Common_Dayoff
	 *
	 * @return Business_Common_Dayoff
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
	 * get Zend_Db connection
	 *
	 * @return Zend_Db_Adapter_Abstract
	 */
	function getDbConnection()
	{		
		$db    	= Globals::getDbConnection('maindb', false);
		return $db;	
	}
	
	/**
	 * Enter description here...
	 *
	 * @return Maro_Cache
	 */
	function getCacheInstance()
	{
		$cache = GlobalCache::getCacheInstance('event');		
		return $cache;
	}
	
        public function excute($query){
            $db     =  $this->getDbConnection();
            $result = $db->query($query);
            return $result;
        }
	public function updatebyday($date,$data){
            $db=  $this->getDbConnection();
            $query = "date = '$date' ";
            $result = $db->update($this->_tablename, $data, $query);
            return $result;
        }
        public function getListByDay($userid,$month,$year){
            $cache = $this->getCacheInstance();
            $key = "getListByDay".  $this->_tablename.$month.$year.$userid;
            $result = $cache->getCache($key);
//            $result = FALSE;
            if($result ===FALSE){
                $db = $this->getDbConnection();
                $query = "select * from $this->_tablename where userid = $userid and MONTH(date) = $month and YEAR(date) = $year  and enabled != 0";
                $result = $db->fetchAll($query);
                if(!is_null($result) && is_array($result)){
                    $cache->setCache($key, $result);
                }
            }
            return $result;
        }
        public function getDetailByDay($userid,$date){
            $cache = $this->getCacheInstance();
            $key = "getDetailByDay".  $this->_tablename.$date.$userid;
            $result = $cache->getCache($key);
//            $result = FALSE;
            if($result ===FALSE){
                $db = $this->getDbConnection();
                $query = "select * from $this->_tablename where userid = $userid and date = '$date'";
                $result = $db->fetchAll($query);
                $result = $result[0];
                if(!is_null($result) && is_array($result)){
                    $cache->setCache($key, $result);
                }
            }
            return $result;
        }
        public function insert($data)
	{
            $db = $this->getDbConnection();
            $result = $db->insert($this->_tablename,$data);
            return $result;
	}
	public function update($id,$data){
            $db=  $this->getDbConnection();
            $query = "id = ".$id;
            $result = $db->update($this->_tablename, $data, $query);
            return $result;
        }
		
}
?>
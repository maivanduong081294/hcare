<?php

class Business_Common_AcceptByCated extends Business_Abstract
{
	private $_tablename = 'accept_by_cated';
	
	const KEY_LIST = 'accept_by_cated.list';
	const KEY_DETAIL = 'accept_by_cated.uid.%s';
	
	protected static $_current_rights = null;
	
	protected static $_instance = null; 

	function __construct()
	{
		
	}
	
	/**
	 * get instance of Business_Common_AcceptByCated
	 *
	 * @return Business_Common_AcceptByCated
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
        public function getDetailByNow($userid){
            $cache = $this->getCacheInstance();
            $key = "getDetailByNow".  $this->_tablename.$userid;
            $result = $cache->getCache($key);
            $result = FALSE;
            if($result ===FALSE){
                $db = $this->getDbConnection();
                $query = "select * from $this->_tablename where now = 1 and userid = $userid and enabled = 1";
                $result = $db->fetchAll($query);
                $result = $result[0];
                if(!is_null($result) && is_array($result)){
                    $cache->setCache($key, $result,600);
                }
            }
            return $result;
        }
        public function getDetailById($id){
            $cache = $this->getCacheInstance();
            $key = "getDetailById".  $this->_tablename.$id;
            $result = $cache->getCache($key);
//            $result = FALSE;
            if($result ===FALSE){
                $db = $this->getDbConnection();
                $query = "select * from $this->_tablename where id = $id  and enabled = 1";
                $result = $db->fetchAll($query);
                $result = $result[0];
                if(!is_null($result) && is_array($result)){
                    $cache->setCache($key, $result);
                }
            }
            return $result;
        }
        
        public function getDetailByUserId($userid){
            $cache = $this->getCacheInstance();
            $key = "getDetailByUserId".  $this->_tablename.$userid;
            $result = $cache->getCache($key);
//            $result = FALSE;
            if($result ===FALSE){
                $db = $this->getDbConnection();
                $query = "select * from $this->_tablename where userid = $userid  and enabled = 1";
                $result = $db->fetchAll($query);
                $result = $result[0];
                if(!is_null($result) && is_array($result)){
                    $cache->setCache($key, $result,600);
                }
            }
            return $result;
        }
        public function getDetailByUserIdProductsId($userid,$productsid){
            $cache = $this->getCacheInstance();
            $key = "getDetailByUserIdProductsId".  $this->_tablename.$userid.$productsid;
            $result = $cache->getCache($key);
            $result = FALSE;
            if($result ===FALSE){
                $db = $this->getDbConnection();
                $query = "select * from $this->_tablename where userid = $userid and productsid = $productsid  and enabled = 1";
                $result = $db->fetchAll($query);
                $result = $result[0];
                if(!is_null($result) && is_array($result)){
                    $cache->setCache($key, $result,600);
                }
            }
            return $result;
        }
        
        
        
        public function checkList($month,$year,$storeid=0,$idmb=0,$flag=0,$type=0)
	{
            $cache = $this->getCacheInstance();
            $key = "checkList".  $this->_tablename.$month.$year.$flag.$storeid.$type;
            $result = $cache->getCache($key);
            $result =FALSE;
            if($result ===FALSE){
                $db = $this->getDbConnection();
                $query = "SELECT * FROM  $this->_tablename  where  actived=1 and  month = $month and year = $year";
                if($flag != 0){
                    $query .= " and flag = $flag";
                }
                if($storeid != 0){
                    $query .= " and storeid = $storeid";
                }
                if($idmb != 0){
                    $query .= " and idmb = $idmb";
                }
                if($type != 0){
                    $query .= " and type = $type";
                }
//                var_dump($query);exit();
                $result = $db->fetchAll($query);
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
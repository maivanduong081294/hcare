<?php

class Business_Common_Target extends Business_Abstract
{
	private $_tablename = 'target';
	
	const KEY_LIST = 'target.list';
	const KEY_DETAIL = 'target.uid.%s';
	
	protected static $_current_rights = null;
	
	protected static $_instance = null; 

	function __construct()
	{
		
	}
	
	/**
	 * get instance of Business_Common_Target
	 *
	 * @return Business_Common_Target
	 */
	public static function getInstance()
	{
		if(self::$_instance == null)
		{
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
//	
	
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
	
        public function getListByIdByMonthYear($id,$user_id,$month_created,$year_created){
            
        }
        public function getDetailById($id){
            $cache = $this->getCacheInstance();
            $key = "getDetailById".  $this->_tablename.$id;
            $result = $cache->getCache($key);
//            $result = FALSE;
            if($result ===FALSE){
                $db = $this->getDbConnection();
                $query = "select * from $this->_tablename where id = $id";
                $result = $db->fetchAll($query);
                $result = $result[0];
                if(!is_null($result) && is_array($result)){
                    $cache->setCache($key, $result);
                }
            }
            
            return $result;
        }
        
        
        public function getList($month,$year,$storeid=0,$mbid=0,$flag=0,$type=0)
	{
            $cache = $this->getCacheInstance();
            $key = "getList".  $this->_tablename.$month.$year.$flag.$storeid.$type.$mbid;
            $result = $cache->getCache($key);
//            $result = FALSE;
            if($result ===FALSE){
                $db = $this->getDbConnection();
                $query = "SELECT * FROM  $this->_tablename  where  actived=1 and  month = $month and year = $year";
                if($flag != 0){
                    $query .= " and flag = $flag";
                }
                if($storeid != 0){
                    $query .= " and storeid IN ($storeid)";
                }
                $query .= " and idmb = " . intval($mbid);
                
                if($type != 0){
                    $query .= " and type = $type";
                }
//                var_dump($query);//exit();
                $result = $db->fetchAll($query);
                if(!is_null($result) && is_array($result)){
                    $cache->setCache($key, $result);
                }
            }
            return $result;		
	}
        public function checkList($month,$year,$storeid=0,$idmb=0,$flag=0,$type=0)
	{
            $cache = $this->getCacheInstance();
            $key = "checkList".  $this->_tablename.$month.$year.$flag.$storeid.$type.$idmb;
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
                $query .= " and idmb = $idmb";
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
	
	public function getRolesForUser($userid)
	{
		$_roles = Business_Common_Roles::getInstance();
		
		$user_roles = $_roles->getRolesByUser($userid);		
				
		$user_perm = array();
		
		$_permission = Business_Common_Permissions::getInstance();
		
		if($user_roles != null && is_array($user_roles) && count($user_roles) > 0)
		{
			for($i=0;$i<count($user_roles);$i++)
			{
				$pid = $user_roles[$i]['pid'];				
				$perm = $_permission->getPermision($pid);				
				$perm = explode(',',$perm['permission']);												
				$user_perm = array_merge($user_perm,$perm);				
			}
		}		
		return $user_perm;
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
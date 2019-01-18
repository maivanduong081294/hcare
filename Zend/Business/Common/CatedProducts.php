<?php

class Business_Common_CatedProducts extends Business_Abstract
{
	private $_tablename = 'cated_products';
	
	const KEY_LIST = 'cated_products.list';
	const KEY_DETAIL = 'cated_products.uid.%s';
	
	protected static $_current_rights = null;
	
	protected static $_instance = null; 

	function __construct()
	{
		
	}
	
	/**
	 * get instance of Business_Common_CatedProducts
	 *
	 * @return Business_Common_CatedProducts
	 */
	public static function getInstance()
	{
		if(self::$_instance == null)
		{
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
//	public static function checkRight($right = '')
//	{
//		$auth = Zend_Auth::getInstance();
//		$identity = $auth->getIdentity();
//		
//		if(!$auth->hasIdentity()) return false;
//		
//		if(is_null(self::$_current_rights))
//		{			
//			if($auth->hasIdentity())
//			{
//				$_user = self::getInstance();			
//				$userid = $identity->userid;
//				self::$_current_rights = $_user->getRolesForUser($userid);	  
//			}
//			else
//			{
//				self::$_current_rights = array();
//			}			
//		}
//
//		$username = $identity->username;
//		if($username == "admin") return true;
//		
//		if(in_array($right, self::$_current_rights)) return true;
//		else return false;
//	}
//	
//	private function getKeyList()
//	{
//		return sprintf(Business_Common_Users::KEY_LIST);
//	}
//	
//	private function getKeyDetail($uid)
//	{
//		return sprintf(Business_Common_Users::KEY_DETAIL, $uid);
//	}
	
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
		$cache = GlobalCache::getCacheInstance();		
		return $cache;
	}
	
    public function getList()
    {
        $cache = $this->getCacheInstance();
        $key = "getListv2".$this->_tablename;
        $result = $cache->getCache($key);
        if($result === FALSE)
        {
                $db = $this->getDbConnection();
                $query = "SELECT * FROM  $this->_tablename ";
                $result = $db->fetchAll($query);
                if(!is_null($result) && is_array($result))
                {
                        $cache->setCache($key, $result);
                }
        }

        return $result;		
    }
	public function getListById($id)
	{
                $db = $this->getDbConnection();
                $query = "SELECT * FROM  $this->_tablename where id = $id";
                $result = $db->fetchAll($query);
		return $result;		
	}
	
	
        public function getListByUname(){
            $db = $this->getDbConnection();
            $ch = 'vote_';
            $query = "select * from $this->_tablename  where username like '%$ch%'";
            $result = $db->fetchAll($query);
//            var_dump($result);exit();
            return $result;
        }
	public function updateUser($userid, $data)
	{
		$db = $this->getDbConnection();
		$where = array();
		$where[] = "userid='" . parent::adaptSQL($userid) . "'";
		$result = $db->update($this->_tablename, $data, $where);
		if($result)
		{
			$cache = $this->getCacheInstance();
			$key = $this->getKeyList();
			$cache->deleteCache($key);
			
			$key = $this->getKeyDetail($userid);
			$cache->deleteCache($key);
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
	
	
		
}
?>
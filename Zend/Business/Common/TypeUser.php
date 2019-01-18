<?php

class Business_Common_TypeUser extends Business_Abstract
{
	private $_tablename = 'zfw_type';
	
	const KEY_LIST = 'zfw_type.list';
	const KEY_CHECKROLE = 'zfw_type.checkrole';
	const KEY_DETAIL = 'zfw_type.uid.%s';
	
	protected static $_current_rights = null;
	
	protected static $_instance = null; 

	function __construct()
	{
		
	}
	
	/**
	 * get instance of Business_Common_TypeUser
	 *
	 * @return Business_Common_TypeUser
	 */
	public static function getInstance()
	{
		if(self::$_instance == null)
		{
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	public static function checkRight($right = '')
	{
		$auth = Zend_Auth::getInstance();
		$identity = $auth->getIdentity();
		
		if(!$auth->hasIdentity()) return false;
		
		if(is_null(self::$_current_rights))
		{			
			if($auth->hasIdentity())
			{
				$_user = self::getInstance();			
				$userid = $identity->userid;
				self::$_current_rights = $_user->getRolesForUser($userid);	  
			}
			else
			{
				self::$_current_rights = array();
			}			
		}

		$username = $identity->username;
		if($username == "admin") return true;
		
		if(in_array($right, self::$_current_rights)) return true;
		else return false;
	}
	
	private function getKeyList()
	{
		return sprintf(Business_Common_TypeUser::KEY_LIST);
	}
	private function getKeyCheckRole()
	{
		return sprintf(Business_Common_TypeUser::KEY_CHECKROLE);
	}
	
	private function getKeyDetail($uid)
	{
		return sprintf(Business_Common_TypeUser::KEY_DETAIL, $uid);
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
		$cache = GlobalCache::getCacheInstance();		
		return $cache;
	}
	public function getDetailById($itemid){
            $db = $this->getDbConnection();
            $query = "select * from $this->_tablename where id = $itemid";
            $result = $db->fetchAll($query);
            return $result[0];
        }

        public function getList()
	{
            $db = $this->getDbConnection();
            $query = "SELECT * FROM " . $this->_tablename;
            $result = $db->fetchAll($query);
            return $result;		
	}
	
	
	
		
}
?>
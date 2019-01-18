<?php

class Business_Import_User extends Business_Abstract
{
	private $_tablename = 'import_user';
	private static $_instance = null; 
	const EXPIRED = 3000; //secs
	const KEY_DETAIL = 'number.detail.%s'; //key of detail.id
	function __construct()
	{
		
	} 
	
	/**
	 * get instance of Business_Addon_Number
	 *
	 * @return Business_Addon_Number
	 */
	public static function getInstance()
	{
		if(self::$_instance == null)
		{
			self::$_instance = new Business_Import_User();
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
		$db = Globals::getDbConnection('hnam_app', false);
		return $db;	
	}
	private function getCacheInstance() {
        $cache = GlobalCache::getCacheInstance('ws');
        return $cache;
    }
	public function getByItemid($itemid, $field = '')
	{
        $itemid = intval($itemid);
        $db = $this->getDbConnection();
        $query = " SELECT ".$field." FROM " . $this->_tablename ." WHERE itemid = ?";
        $data = array($itemid);
        $result = $db->fetchAll($query,$data);
        if($result != null && is_array($result))
        {
                return $result;
        }
        else return null;
	}
	public function getByPhone($phone, $field = '*')
	{
        $db = $this->getDbConnection();
        $query = " SELECT ".$field." FROM " . $this->_tablename ." WHERE phone = ?";
        $data = array($phone);
        $result = $db->fetchAll($query,$data);
        if($result != null && is_array($result))
        {
                return $result[0];
        }
        else return null;
	}
	public function getByFullname($fullname, $field = '*')
	{
        $db = $this->getDbConnection();
        $query = " SELECT ".$field." FROM " . $this->_tablename ." WHERE fullname = ?";
        $data = array($fullname);
        $result = $db->fetchAll($query,$data);
        if($result != null && is_array($result))
        {
                return $result[0];
        }
        else return null;
	}
	public function insert($data)
	{
		$db = $this->getDbConnection();
		$result = $db->insert($this->_tablename,$data);
        $id = $db->lastInsertId();
        return $id;
	}
}

?>
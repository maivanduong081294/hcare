<?php

class Business_Import_Newitem extends Business_Abstract
{
	private $_tablename = 'ws_newsitem';
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
			self::$_instance = new Business_Import_Newitem();
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
		$db = Globals::getDbConnection('maindb', false);
		return $db;	
	}
	private function getCacheInstance() {
        $cache = GlobalCache::getCacheInstance('ws');
        return $cache;
    }
	public function getByColorid($colorid,$field = '')
	{
        $colorid = intval($colorid);
        $db = $this->getDbConnection();
        $query = " SELECT ".$field." FROM " . $this->_tablename ." WHERE itemid = ?";
        $data = array($colorid);
        $result = $db->fetchAll($query,$data);
        if($result != null && is_array($result))
        {
                return $result;
        }
        else return null;
	}
}

?>
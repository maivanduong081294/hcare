<?php

class Business_Import_Ketoan extends Business_Abstract
{
	private $_tablename = 'addon_product_title_kt';
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
			self::$_instance = new Business_Import_Ketoan();
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
		$db = Globals::getDbConnection('hnam_wh', false);
		return $db;	
	}
	private function getCacheInstance() {
        $cache = GlobalCache::getCacheInstance('ws');
        return $cache;
    }
    public function getList()
	{
        $db = $this->getDbConnection();
        $query = "select * from " . $this->_tablename . " where 1 order by id desc";
        $result = $db->fetchAll($query);
        return $result;
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
	public function getByColorid($colorid, $field = '')
	{
        $colorid = intval($colorid);
        $db = $this->getDbConnection();
        $query = " SELECT ".$field." FROM " . $this->_tablename ." WHERE colorid = ?";
        $data = array($colorid);
        $result = $db->fetchAll($query,$data);
        if($result != null && is_array($result))
        {
                return $result;
        }
        else return null;
	}
	public function getWhere2($where_field1,$where_value1,$where_field2,$where_value2, $field = '')
	{
        $colorid = intval($colorid);
        $db = $this->getDbConnection();
        $query = " SELECT ".$field." FROM " . $this->_tablename ." WHERE ".$where_field1." = ".$where_value1." and ".$where_field2." = ".$where_value2."";
        $result = $db->fetchAll($query);
        if($result != null && is_array($result))
        {
                return $result;
        }
        else return null;
	}
}

?>
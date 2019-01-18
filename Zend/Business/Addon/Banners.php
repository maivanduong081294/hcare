<?php

class Business_Addon_Banners extends Business_Abstract
{
	private $_tablename = 'addon_banners';
	private static $_instance = null; 
	
	function __construct()
	{
		
	}
	
	/**
	 * get instance of Business_Addon_Banners
	 *
	 * @return Business_Addon_Banners
	 */
	public static function getInstance()
	{
		if(self::$_instance == null)
		{
			self::$_instance = new Business_Addon_Banners();
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
		
	public function getList()
	{
            $db = $this->getDbConnection();
            $query = "select * from " . $this->_tablename . " where 1 order by ordering desc";
            $result = $db->fetchAll($query);
            return $result;
	}
	
	public function getDetail($banners_id)
	{
            $banners_id = intval($banners_id);
            $db = $this->getDbConnection();
            $query = " SELECT * FROM " . $this->_tablename ." WHERE banners_id = ?";
            $data = array($banners_id);
            $result = $db->fetchAll($query,$data);
            if($result != null && is_array($result))
            {
                    return $result[0];
            }
            else return null;
	}
	
	public function update($banners_id, $data)
	{
		
		$db = $this->getDbConnection();
		$where = array();	
		$where[] = "banners_id = '" . parent::adaptSQL($banners_id) . "'";
		try
		{			
			$result = $db->update($this->_tablename, $data, $where);
			
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
                return $result;
	}
	
	public function delete($banners_id)
	{	
		$db = $this->getDbConnection();
		$where = array();
		$where[] = "banners_id='" . parent::adaptSQL($banners_id) . "'";
		$result = $db->delete($this->_tablename,$where);
                return $result;
	}
}

?>
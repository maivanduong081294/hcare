<?php

class Business_Addon_VipUsed extends Business_Abstract
{
	private $_tablename = 'addon_vip_used';
	private static $_instance = null; 
	
	function __construct()
	{
		
	}
	
	/**
	 * get instance of Business_Addon_VipUsed
	 *
	 * @return Business_Addon_VipUsed
	 */
	public static function getInstance()
	{
		if(self::$_instance == null)
		{
			self::$_instance = new Business_Addon_VipUsed();
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
		
        public function getScreenGuardDetail($vipid) {
            $month = date("m");
            $year = date("Y");
            //id mieng dan man hinh = 1
            $bonussid = 1;
            $db = $this->getDbConnection();
            $query = "SELECT * FROM $this->_tablename WHERE vip_id = '$vipid' AND bonus_id= '$bonussid' AND MONTH(datetime) = $month AND YEAR(datetime)=$year";
            $result = $db->fetchAll($query);
            if ($result != null)
                return $result[0];
            return null;
        }
        
        public function insert($data)
	{
            $db = $this->getDbConnection();
            $result = $db->insert($this->_tablename,$data);
            return $result;
	}
}

?>
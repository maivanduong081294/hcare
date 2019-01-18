<?php

class Business_Addon_Lenovo extends Business_Abstract
{
	private $_tablename = 'addon_lenovo_points';
	private static $_instance = null; 
	
	function __construct()
	{
		
	}
	
	/**
	 * get instance of Business_Addon_Lenovo
	 *
	 * @return Business_Addon_Lenovo
	 */
	public static function getInstance()
	{
		if(self::$_instance == null)
		{
			self::$_instance = new Business_Addon_Lenovo();
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

        public function getDetailByUserID($userid) {
            $db = $this->getDbConnection();
            $query = "select * FROM " . $this->_tablename . " where userid='$userid'";
            $result = $db->fetchAll($query);
            return $result[0];
        }
        
        public function update($userid, $data)
	{
            $db = $this->getDbConnection();
            $where = array();
            $where[] = "userid = '" . parent::adaptSQL($userid) . "'";                
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
	}
}

?>
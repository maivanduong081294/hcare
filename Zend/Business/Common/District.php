<?php
class Business_Common_District extends Business_Abstract
{
	private $_tablename = 'data_district';
	private static $_instance = null;
	function __construct()
	{
	}
	//public static function
	/**
	 * get instance of Business_Common_District
	 *
	 * @return Business_Common_District
	 */
	public static function getInstance()
	{
		if(self::$_instance == null)
		{
			self::$_instance = new Business_Common_District();
		}
		return self::$_instance;
        }
	/**
	 * Get DB Connection
	 *
	 * @return Zend_Db_Adapter_Abstract
	 */
	private function getDbConnection()
	{
		$db    	= Globals::getDbConnection('maindb');
		return $db;
	}

	/**
	 * Enter description here...
	 *
	 * @return Maro_Cache
	 */


	
        public function getListDistrict($idcity)
        {
            $db = $this->getDbConnection();
            $query = "SELECT * FROM ".$this->_tablename." WHERE cityid=1";
            $result = $db->fetchAll($query);
            return $result;
        }
        public function getListDistrict2($idcity="")
        {
            $db = $this->getDbConnection();
            $query = "SELECT * FROM ".$this->_tablename." where 1=1 ";
            if($idcity != null){
                $query .=" and cityid = $idcity";
            }
            $result = $db->fetchAll($query);
            return $result;
        }

        public function getDistrictDetail($districtid)
        {
            $db = $this->getDbConnection();
            $query = "SELECT * FROM ".$this->_tablename." WHERE id = $districtid";
            $result = $db->fetchAll($query);
            return $result[0];
        }
}
?>
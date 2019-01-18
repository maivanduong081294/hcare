<?php

class Business_Addon_Vote extends Business_Abstract
{
	private $_tablename = 'addon_vote';
	private static $_instance = null; 
	
	function __construct()
	{
		
	}
	
	/**
	 * get instance of Business_Addon_Vote
	 *
	 * @return Business_Addon_Vote
	 */
	public static function getInstance()
	{
		if(self::$_instance == null)
		{
			self::$_instance = new Business_Addon_Vote();
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
		
	public function getListSummary($storeid)
	{
            $db = $this->getDbConnection();
$query = "SELECT datetime, ";
$query .= "SUM(if(lev1 = 1, 1, 0)) as LEV1,";
$query .= "SUM(if(lev2 = 1, 1, 0)) as LEV2,";
$query .= "SUM(if(lev3 = 1, 1, 0)) as LEV31,";
$query .= "SUM(if(lev3 = 2, 1, 0)) as LEV32,";
$query .= "SUM(if(lev3 = 3, 1, 0)) as LEV33,";
$query .= "SUM(if(lev4 != '0', 1, 0)) as LEV4 ";
$query .= "FROM `addon_vote` ";
$query .= "WHERE storeid=? ";
$query .= "GROUP BY DATE(datetime) ";
$query .= "ORDER BY datetime desc";
            $arr = array($storeid);
            $result = $db->fetchAll($query, $arr);
            return $result;
	}	
        
	public function getList($storeid)
	{
            $db = $this->getDbConnection();
            $query = "select * from " . $this->_tablename . " where storeId = ? ORDER BY datetime DESC";
            $arr = array($storeid);
            $result = $db->fetchAll($query, $arr);
            return $result;
	}				
		
        public function getListByDate($storeid, $date)
	{
            $db = $this->getDbConnection();
            $query = "select * from " . $this->_tablename . " where storeId = ? AND DATE(datetime) = ? ORDER BY datetime DESC";
            
            $arr = array($storeid, $date);
            
            $result = $db->fetchAll($query, $arr);
            return $result;
	}
        
        public function getListSummaryByDate($storeid, $date)
	{
            $db = $this->getDbConnection();
$query = "SELECT datetime, ";
$query .= "SUM(if(lev1 = 1, 1, 0)) as LEV1,";
$query .= "SUM(if(lev2 = 1, 1, 0)) as LEV2,";
$query .= "SUM(if(lev3 = 1, 1, 0)) as LEV31,";
$query .= "SUM(if(lev3 = 2, 1, 0)) as LEV32,";
$query .= "SUM(if(lev3 = 3, 1, 0)) as LEV33,";

$query .= "SUM(if(lev4 != '0', 1, 0)) as LEV4 ";
$query .= "FROM `addon_vote` ";
$query .= "WHERE storeid=? AND DATE(datetime) = ? ";
$query .= "GROUP BY DATE(datetime) ";
$query .= "ORDER BY datetime desc";

            $arr = array($storeid, $date);
            
            $result = $db->fetchAll($query, $arr);
            return $result;
	}				
        
        public function getListByDate2($storeid, $date_from, $date_to)
	{
            $db = $this->getDbConnection();
            $query = "select * from " . $this->_tablename . " where storeId = ? AND DATE(datetime) >= ? AND DATE(datetime) <= ?  ORDER BY datetime DESC";
            $arr = array($storeid, $date_from, $date_to);
            $result = $db->fetchAll($query, $arr);
            return $result;
	}	
        
        public function getListSummaryByDate2($storeid, $date_from, $date_to)
	{
            $db = $this->getDbConnection();
$query = "SELECT datetime, ";
$query .= "SUM(if(lev1 = 1, 1, 0)) as LEV1,";
$query .= "SUM(if(lev2 = 1, 1, 0)) as LEV2,";
$query .= "SUM(if(lev3 = 1, 1, 0)) as LEV31,";
$query .= "SUM(if(lev3 = 2, 1, 0)) as LEV32,";
$query .= "SUM(if(lev3 = 3, 1, 0)) as LEV33,";
$query .= "SUM(if(lev4 != '0', 1, 0)) as LEV4 ";
$query .= "FROM `addon_vote` ";
$query .= "WHERE storeid=? AND DATE(datetime) >= ? AND DATE(datetime) <= ?  ";
$query .= "GROUP BY DATE(datetime) ";
$query .= "ORDER BY datetime desc";

            $arr = array($storeid, $date_from, $date_to);
            $result = $db->fetchAll($query, $arr);
            return $result;
	}				

	public function insert($data)
	{
		$db = $this->getDbConnection();
		$result = $db->insert($this->_tablename,$data);
	}
}

?>
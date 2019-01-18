<?php

class Business_Addon_Vip extends Business_Abstract
{
	private $_tablename = 'addon_vip';
	private static $_instance = null; 
	
	function __construct()
	{
		
	}
	
	/**
	 * get instance of Business_Addon_Vip
	 *
	 * @return Business_Addon_Vip
	 */
	public static function getInstance()
	{
		if(self::$_instance == null)
		{
			self::$_instance = new Business_Addon_Vip();
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

        public function getDetailByID($id) {
            $db = $this->getDbConnection();
            $query = "select * FROM " . $this->_tablename . " where id='$id'";
            $result = $db->fetchAll($query);
            return $result[0];
        }
        
        public function getDetailByPhone($phone) {
            $db = $this->getDbConnection();
            $query = "select id FROM " . $this->_tablename . " where phone='$phone'";
            $result = $db->fetchAll($query);
            return $result[0];
        }
        public function getDetailByEmail($email) {
            $db = $this->getDbConnection();
            $query = "select id FROM " . $this->_tablename . " where email='$email'";
            $result = $db->fetchAll($query);
            return $result[0];
        }
        
        public function getReport($date) {
            $db = $this->getDbConnection();
            $query = "select count(id) as total, storename FROM " . $this->_tablename . " where DATE(createdate)= '$date' AND storename != '' GROUP BY storename";
            $result = $db->fetchAll($query);
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
	
        public function getListByDate($date)
	{
            $db = $this->getDbConnection();
            $query = "select * from " . $this->_tablename . " where createdate = ? ORDER BY createdate ASC";
            $arr = array($date);
            $result = $db->fetchAll($query, $arr);
            return $result;
	}
        
	public function insert($data)
	{
		$db = $this->getDbConnection();
		$result = $db->insert($this->_tablename,$data);
	}
        
        public function getListByKey($keys){
            if ($keys == null) return null;
            $db = $this->getDbConnection();
            $keys = strtolower(trim(Globals::adaptData($keys)));
            $query = "select * from " . $this->_tablename . " where id= '$keys' OR email LIKE '%$keys%' OR CODE LIKE '%$keys%' OR PHONE LIKE '%$keys%' OR LOWER(fullname) LIKE '%$keys%'";
//            $arr = array($keys, $keys);            
//            $result = $db->fetchAll($query, $arr);
            $result = $db->fetchAll($query);
            return $result;
        }
        public function getListByStore($storename){
            $db = $this->getDbConnection();
            $keys = strtolower(trim(Globals::adaptData($keys)));
            $query = "select * from " . $this->_tablename . " where storename LIKE '%$storename%' ORDER BY id DESC LIMIT 0,50";
//            $arr = array($keys, $keys);            
//            $result = $db->fetchAll($query, $arr);
            $result = $db->fetchAll($query);
            return $result;
        }
        
        public function updateCodeById($id, $code){
            $db = $this->getDbConnection();
            $where = array();
            $where[] = "id='" . parent::adaptSQL($id) . "'";
            $data = array();
            $data["code"] = $code;
            try
            {                
                $result = $db->update($this->_tablename, $data, $where);
                return $result;
            }
            catch(Exception $e)
            {                
                    return false;
            }

        }
        
        public function update($itemid, $data)
	{
            $db = $this->getDbConnection();
            $where = array();
            $where[] = "id = '" . parent::adaptSQL($itemid) . "'";                
            try
            {			
                $result = $db->update($this->_tablename, $data, $where);
            }
            catch(Exception $e)
            {
                    return 0;
            }
	}
}

?>
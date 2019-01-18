<?php

class Business_Addon_Users extends Business_Abstract
{
	private $_tablename = 'addon_users';
	private static $_instance = null; 
	
	function __construct()
	{
		
	}
	
	/**
	 * get instance of Business_Addon_Users
	 *
	 * @return Business_Addon_Users
	 */
	public static function getInstance()
	{
		if(self::$_instance == null)
		{
			self::$_instance = new Business_Addon_Users();
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
        function getCacheInstance()
	{
		$cache = GlobalCache::getCacheInstance('event');		
		return $cache;
	}
        public function getListByPhone($phone)
	{
            $db = $this->getDbConnection();
            $query = "select * from " . $this->_tablename . " where phone like '%$phone%' group by phone,fullname ORDER BY createdate DESC";
            $result = $db->fetchAll($query);
            return $result;
	}
    public function getAllTagsByPhone2() {
        $cache = $this->getCacheInstance();
        $key = "getAllTagsByPhone" . $this->_tablename;
        $result = $cache->getCache($key);
        $result =  false;
        if ($result === FALSE) {
            $db = $this->getDbConnection();
            $query = "SELECT id, fullname,phone,address FROM addon_users WHERE phone != '' ORDER BY phone ASC";
            $result = $db->fetchAll($query);
            if (!is_null($result) && is_array($result) && count($result) > 0) {
                foreach ($result as $item) {
                    $itemid = "";
                    $itemid = "--" . $item["id"];
                    $fullname = "--" . $item["fullname"];
                    $address = "--" . $item["address"];
                    $ret[] = "\"" . $item["phone"] . $itemid . $fullname . $address."\"";
                }
                $ret = implode(",", $ret);
                $result = $ret;
                $cache->setCache($key, $result, 60 * 5);
            }
        }
        return $result;
    }
    
       public function sumPrePaidChangeCard($storename,$created_date, $end_date,$cated_card){
//           $cache = $this->getCacheInstance();
//            $key = "sumPrePaidChangeCard.$this->_tablename.$storename.$created_date.$end_date.$cated_card";
//            $result = $cache->getCache($key);
//            $result =FALSE;
//            if($result === FALSE)
//            {
                $db = $this->getDbConnection();
                $query =  "SELECT sum(prepaid) FROM $this->_tablename  WHERE storename = '$storename' and cated_card = $cated_card ";
                $query .= " and createdate > '$created_date' and createdate < '$end_date' ";
                $query .= " ORDER BY createdate DESC";
//                var_dump($query);exit();
                $result = $db->fetchAll($query);
//                $cache->setCache($key, $result);
//            }
            return $result;
        }
       public function getListTragop($storename,$created_date, $end_date,$cated_prepaid_installment){
//           $cache = $this->getCacheInstance();
//            $key = "sumPrePaidChangeCard.$this->_tablename.$storename.$created_date.$end_date.$cated_card";
//            $result = $cache->getCache($key);
//            $result =FALSE;
//            if($result === FALSE)
//            {
                $db = $this->getDbConnection();
                $query =  "SELECT * FROM $this->_tablename  WHERE storename = '$storename' and cated_prepaid_installment = $cated_prepaid_installment";
                $query .= " and createdate > '$created_date' and createdate < '$end_date' ";
                $query .= " ORDER BY createdate DESC";
//                var_dump($query);exit();
                $result = $db->fetchAll($query);
//                $cache->setCache($key, $result);
//            }
            return $result;
        }
        
        public function getDetailByID($id) {
            $cache = $this->getCacheInstance();
		$key = "getDetailByID221".$this->_tablename.$id;
		$result = $cache->getCache($key);
		$result = FALSE;
		if($result === FALSE)
		{
                    $db = $this->getDbConnection();
                    $query = "select * FROM " . $this->_tablename . " where id='$id'";
        //            var_dump($query);exit();
                    $_result = $db->fetchAll($query);
                    $result = $_result[0];
                    if($result != null && is_array($result))
			{
				$cache->setCache($key, $result,600);				
			}
                }
            
            return $result;
        }
        
        public function getDetailByPhone($phone) {
            $db = $this->getDbConnection();
            $query = "select id FROM " . $this->_tablename . " where phone='$phone'";
            $result = $db->fetchAll($query);
            return $result[0];
        }
        public function getDetailByTopPhone($phone) {
            $db = $this->getDbConnection();
            $query = "select * FROM " . $this->_tablename . " where phone  = '$phone' order by createdate DESC";
//            var_dump($query);exit();
            $result = $db->fetchAll($query);
            return $result[0];
        }
        public function getDetailByEmail($email) {
            $db = $this->getDbConnection();
            $query = "select id FROM " . $this->_tablename . " where email='$email'";
            $result = $db->fetchAll($query);
            return $result[0];
        }
        public function get_detail_by_username($username) {
            $db = $this->getDbConnection();
            $query = "select id FROM " . $this->_tablename . " where username='$username'";
            $result = $db->fetchAll($query);
            return $result[0];
        }
        public function getDetailByPersonalID($pid) {
            $db = $this->getDbConnection();
            $query = "select id FROM " . $this->_tablename . " where personalid='$pid'";
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
                if ($result > 0) {
                    $lastid= $db->lastInsertId($this->_tablename); // tra ve id khi them vao
                }
                return $lastid;
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
        
        public function getReportBV($storename, $secType, $from, $to){
            $db = $this->getDbConnection();
            $query = "SELECT storename,security_name, count(`security_point`) as total FROM `addon_users` WHERE `security_point`=$secType AND createdate >= '$from' AND createdate <= '$to' AND storename = '$storename'";
            $result = $db->fetchAll($query);
            return $result[0];
        }
        
        public function getReportKnow($storename, $know, $from, $to){
            $db = $this->getDbConnection();
            $query = "SELECT storename,know, count(`know`) as total FROM `addon_users` WHERE `know`=$know AND createdate >= '$from' AND createdate <= '$to' AND storename = '$storename'";
            $result = $db->fetchAll($query);
            return $result[0];
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
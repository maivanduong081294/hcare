<?php
// ws_user = .vc_provider_accout : ten bang
// Business_Ws_ = .Business_EVoucher_ : duong dan den lop business
// User = .ProviderAccount : ten goi nho cua table 
// userid = .pid : khoa chinh
// Userid = .Pid : khoa chinh viet in dunug trong ten ham
// email = .username : lay noi dung theo ten voi key username
// maindb = .maindb


	class Business_Ws_User_Storage_MySQL extends Business_Abstract implements Business_Ws_User_Storage_Interface 
	{
		private $_tablename = 'ws_user';
		
		protected static $_instance = null;
		
		/**
		 * get instance of Business_Ws_User_Storage_MySQL
		 *
		 * @return Business_Ws_User_Storage_Interface
		 */
		public static function getInstance()
		{
			if(self::$_instance == null)
			{
				self::$_instance = new self();
			}
			return self::$_instance;
		}
			
		function __construct()
		{
			
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
		
		public function getUserList()
		{
			$db = $this->getDbConnection();
//1			$query = "SELECT * FROM " . $this->_tablename;			
			$query = "SELECT * FROM " . $this->_tablename . " ORDER BY email ASC";
			$result = $db->fetchAll($query);		
			return $result;
		}
		
		public function getUserByUserid($userid)
		{
			$db = $this->getDbConnection();
			$query = "SELECT * FROM " . $this->_tablename . " WHERE userid = ? ";
			$data = array($userid);
			$result = $db->fetchAll($query, $data);
			if (!empty($result) && is_array($result) && count($result)>0 )		
				return $result[0];
			return null;
		}

                public function getUserByDate($day, $month, $year)
                {
                    $db = $this->getDbConnection();
                    $query = "SELECT * FROM " . $this->_tablename . " WHERE DAY(created)=? AND MONTH(created)=? AND YEAR(created)=? ";
                    $data = array($day, $month, $year);
                    $result = $db->fetchAll($query, $data);
                    if (!empty($result) && is_array($result) && count($result)>0 )
                            return $result;
                    return array();
                }
		
		public function getUserByEmail($email)
		{
			$db = $this->getDbConnection();
			$query = "SELECT * FROM " . $this->_tablename . " WHERE email = ?";
			$data = array($email);
			$result = $db->fetchAll($query, $data);
			if (!empty($result) && is_array($result) && count($result)>0 )		
				return $result[0];
			return null;
		}

                public function getUserByUsername($username)
		{
			$db = $this->getDbConnection();
			$query = "SELECT * FROM " . $this->_tablename . " WHERE username = ?";
			$data = array($username);
			$result = $db->fetchAll($query, $data);
			if (!empty($result) && is_array($result) && count($result)>0 )
				return $result[0];
			return null;
		}
		
		public function insertUser($data) //ham tra ve mid vua duoc insert, neu insert failed return 0
		{
			$db = $this->getDbConnection();
			$result = $db->insert($this->_tablename,$data);
			if($result)
			{
				$lastid = $db->lastInsertId($this->_tablename);
				return $lastid;		
			}
			else return 0;
		}
		
		public function deleteUser($userid)
		{
			$db = $this->getDbConnection();
			$where = array();
			$where[] = "userid='" . parent::adaptSQL($userid) . "'";
			$result = $db->delete($this->_tablename,$where);
			return $result;
		}
		
		public function updateUser($userid, $data)
		{
			$db = $this->getDbConnection();
			$where = array();
			$where[] = "userid='" . parent::adaptSQL($userid) . "'";
			$result = $db->update($this->_tablename,$data,$where);
			return $result;
		}
		
		public function getUserListWithPaging($offset = 0, $records = 20)
		{
			$db = $this->getDbConnection();
//1			$query = "SELECT * FROM " . $this->_tablename . " LIMIT " ;
			$query = "SELECT * FROM " . $this->_tablename . " ORDER BY created DESC LIMIT ";
			$query .= " $offset , $records ";
			$result = $db->fetchAll($query);	
			return $result;
		}

                
		
	}
?>
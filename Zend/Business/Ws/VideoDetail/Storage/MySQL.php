<?php
// ws_videodetail = .vc_provider_accout : ten bang
// Business_Ws_ = .Business_EVoucher_ : duong dan den lop business
// VideoDetail = .ProviderAccount : ten goi nho cua table 
// videoid = .pid : khoa chinh
// Videoid = .Pid : khoa chinh viet in dunug trong ten ham
// userid = .username : lay noi dung theo ten voi key username
// maindb = .maindb


	class Business_Ws_VideoDetail_Storage_MySQL extends Business_Abstract implements Business_Ws_VideoDetail_Storage_Interface 
	{
		private $_tablename = 'ws_videodetail';
		
		protected static $_instance = null;
		
		/**
		 * get instance of Business_Ws_VideoDetail_Storage_MySQL
		 *
		 * @return Business_Ws_VideoDetail_Storage_Interface
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
		
		public function getVideoDetailList()
		{
			$db = $this->getDbConnection();
//1			$query = "SELECT * FROM " . $this->_tablename;			
			$query = "SELECT * FROM " . $this->_tablename . " ORDER BY userid ASC";
			$result = $db->fetchAll($query);		
			return $result;
		}
		
		public function getVideoDetailByVideoid($videoid)
		{
			$db = $this->getDbConnection();
			$query = "SELECT * FROM " . $this->_tablename . " WHERE videoid = ? ";
			$data = array($videoid);
			$result = $db->fetchAll($query, $data);
			if (!empty($result) && is_array($result) && count($result)>0 )		
				return $result[0];
			return null;
		}
		
		public function getVideoDetailByUserid($userid)
		{
			$db = $this->getDbConnection();
			$query = "SELECT * FROM " . $this->_tablename . " WHERE userid = ? order by videoid desc";
			$data = array($userid);
			$result = $db->fetchAll($query, $data);
			if (!empty($result) && is_array($result) && count($result)>0 )		
				return $result;
			return null;
		}

		public function insertVideoDetail($data) //ham tra ve mid vua duoc insert, neu insert failed return 0
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
		
		public function deleteVideoDetail($videoid)
		{
			$db = $this->getDbConnection();
			$where = array();
			$where[] = "videoid='" . parent::adaptSQL($videoid) . "'";
			$result = $db->delete($this->_tablename,$where);
			return $result;
		}
		
		public function updateVideoDetail($videoid, $data)
		{
			$db = $this->getDbConnection();
			$where = array();
			$where[] = "videoid='" . parent::adaptSQL($videoid) . "'";
			$result = $db->update($this->_tablename,$data,$where);
			return $result;
		}
		
		public function getVideoDetailListWithPaging($offset = 0, $records = 20)
		{
			$db = $this->getDbConnection();
//1			$query = "SELECT * FROM " . $this->_tablename . " LIMIT " ;
			$query = "SELECT * FROM " . $this->_tablename . " ORDER BY userid ASC LIMIT ";			
			$query .= " $offset , $records ";
			$result = $db->fetchAll($query);	
			return $result;
		}

                public function getVideoDetailListWithPagingByuserid($userid, $offset = 0, $records = 20)
		{
			$db = $this->getDbConnection();
			$query = "SELECT * FROM " . $this->_tablename . " WHERE userid = ? LIMIT ";
                        $data = array($userid);
			$query .= " $offset , $records ";
			$result = $db->fetchAll($query, $data);
			return $result;
		}
		
	}
?>
<?php
// ws_user = .vc_provider_accout : ten bang
// Business_Ws_ = .Business_EVoucher_ : duong dan den lop business
// User = .ProviderAccount : ten goi nho cua table 
// userid = .userid : khoa chinh
// Userid = .Pid : khoa chinh viet in dunug trong ten ham
// USERID = .PID : khoa chinh viet in toan bo
// email = .username : lay noi dung theo ten voi key username
// Email = .Username : lay noi dung theo ten voi key username
// EMAIL = .USERNAME : key name "username" viet in toan bo
// maindb = .maindb
// ws = vc;

class Business_Ws_UserModule extends Business_Abstract
{
		
	protected static $_instance = null;
	
        CONST WS_TOTAL_USER = 'WS_TOTAL_USER';

		//for paging
	CONST PAGING_NUM_RECORDS = 100;
	CONST PAGING_MAX_PAGE = 100;
	private $_paging = null;	
		
	/**
	 * get instance of Business_Ws_UserModule
	 *
	 * @return Business_Ws_UserModule
	 */
	public static function getInstance()
	{
		if(self::$_instance == null)
		{
			self::$_instance = new self();
		}
		return self::$_instance;
	}
		
        private function getCacheInstance() {
            $cache = GlobalCache::getCacheInstance('ws');
            return $cache;
        }

   
	function __construct()
	{
		
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
        
        
        public function getTotalUser(){
            $variable = Business_Common_Variables::getInstance();
            $value = $variable->getVariable(self::WS_TOTAL_USER, 0);
            return intval($value);
        }

        public function getListByDate($day, $month, $year)
        {            
            $storage = $this->getStorageAdapter();
            $result = $storage->getUserByDate($day, $month, $year);
            return $result;
        }

	public function getUserList()
	{
		//read cache first
		$cache = $this->getCacheAdapter();		
		$result = $cache->getUserList();
		if($result === FALSE) //miss cache
		{
			$storage = $this->getStorageAdapter();
			$result = $storage->getUserList();
			if(!is_null($result) && is_array($result))
			{
				$cache->setUserList($result);
			}
		}
		
		return $result;
	}

        public function getUserByActivecode($acode) {
            if ($acode == '') return null;
            
            $db = $this->getDbConnection();
            $query = "SELECT * FROM ws_user WHERE activecode = ? ";
            $data = array($acode);
            $result = $db->fetchAll($query, $data);
            if($result != null && is_array($result) && count($result) == 1) {
                    return $result[0];
            }
            else return null;

	}
        
        public function getTotalWCMembers($_cache = true) {
            $cache = $this->getCacheInstance();
            
            $db = $this->getDbConnection();
            $query = "SELECT count(username) as total FROM ws_user WHERE activecode != ''";
            $data = array($acode);
            $result = $db->fetchAll($query, $data);
            if ($cache===false) {
                $result = null;
            }
            if($result != null && is_array($result)) {
                    $cache->setCache("user.wc.total", $result[0]["total"], (15 * 60));
                    return $result[0]["total"];
            }
            else return 0;
        }
        
        public function getListWC() {
            
            $db = $this->getDbConnection();
            $query = "SELECT * FROM ws_user WHERE activecode != ''";
            $data = array($acode);
            $result = $db->fetchAll($query, $data);
            if($result != null && is_array($result)) {
                    return $result;
            }
            else return null;
	}


	public function getUserByUserid($userid = 0)
	{
		if(intval($userid) == 0) return null;
		//read cache first
		$cache = $this->getCacheAdapter();		
		$result = $cache->getUserByUserid($userid);
		if($result === FALSE) //miss cache
		{
			$storage = $this->getStorageAdapter();
			$result = $storage->getUserByUserid($userid);
			if(!is_null($result))
			{
				$cache->setUserByUserid($userid, $result);
			}
		}
		return $result;
	}


        public function getUserByUsername($username = '')
	{
		if( $username == '') return null;
		//read cache first
		$cache = $this->getCacheAdapter();
		$result = $cache->getUserByUsername($username);
		if($result === FALSE) //miss cache
		{
			$storage = $this->getStorageAdapter();
			$result = $storage->getUserByUsername($username);
			if(!is_null($result))
			{
				$cache->setUserByUsername($username, $result);
			}
		}
		return $result;
	}


	public function getUserByEmail($email)
	{
		if(empty($email)) return null;
		//read cache first
		$cache = $this->getCacheAdapter();		
		$result = $cache->getUserByEmail($email);
		if($result === FALSE) //miss cache
		{
			$storage = $this->getStorageAdapter();
			$result = $storage->getUserByEmail($email);
			if(!is_null($result))
			{
				$cache->setUserByEmail($email, $result);
			}
		}
		return $result;
	}
	
	//	lay info nhieu provider 1 luc
	public function getMultiUserByUserid($userids = array())
	{
		if(is_array($userids) && count($userids) > 0)
		{
			//read cache first
			$cache = $this->getCacheAdapter();
			
			$result = $cache->getMultiUserByUserid($userids);
			
			if($result == null)
			{
				$result = array();
			}
			
			for($i=0;$i<count($userids);$i++)
			{				
				if(!array_key_exists($userids[$i],$result))
				{
					$item_miss = $this->getUserByUserid($userids[$i]);
					if($item_miss != null)
						$result[$userids[$i]] = $item_miss;					
				}
			}
			return $result;
		}
		return null;
	}
	
	
	public function insertUser($data)
	{
		$storage = $this->getStorageAdapter();
		$userid = $storage->insertUser($data);
		if(intval($userid) > 0)
		{
			//insert thanh cong -> xoa cache
			$this->deleteAllCache($userid);
                        $variable = Business_Common_Variables::getInstance();
                        $cur_value = $variable->getVariable(self::WS_TOTAL_USER, 0);
                        $cur_value++;
                        $variable->setVariable(self::WS_TOTAL_USER, $cur_value);
		}
		return $userid;
	}	
	
	public function updateUser($userid,$data)
	{
		$storage = $this->getStorageAdapter();
		$result = $storage->updateUser($userid, $data);
		if($result)
		{
			//update thanh cong -> xoa cache
			$this->deleteAllCache($userid);
                        $variable = Business_Common_Variables::getInstance();
                        
		}
		return $result;
	}
	
	public function deleteUser($userid)
	{
		$storage = $this->getStorageAdapter();
		$result = $storage->deleteUser($userid);
		if($result)
		{
                    //delete thanh cong -> xoa cache
                    $variable = Business_Common_Variables::getInstance();
                    $cur_value = $variable->getVariable(self::WS_TOTAL_USER, 0);
                    if ($cur_value > 0)
                    {
                        $cur_value--;
                        $variable->setVariable(self::WS_TOTAL_USER, $cur_value);
                    }
                    $this->deleteAllCache($userid);
		}
	}
	

	public function getUserListWithPaging($offset = 0, $records = 20)
	{
		$paging = $this->getPagingBusiness();
		
		$cache = $this->getCacheAdapter();

		$key = $cache->getKeyListPaging();

		//$paging->clearCachePaging($key);
		$params = array(
		);		
		
		$result = $paging->getData($offset,$records,$key,$params);
		return $result;
	}
	
	public function getRealData($page)
	{
		$offset = 0;
		$records = self::PAGING_NUM_RECORDS;

		if ($page == 0)
			$offset = 0;
		else
			$offset = $page * $records;
		$self = self::getInstance();
		
		$storage = $this->getStorageAdapter();
		$result = $storage->getUserListWithPaging($offset, $records);
		
		return $result;	
	}

	/**
	 * Enter description here...
	 *
	 * @return Maro_Paging_Interface
	 */
	private function getPagingBusiness()
	{
		if($this->_paging != null) return $this->_paging;
		$_cache = $this->getCacheAdapter();
		
		$cache = $_cache->getCacheInstance();

		$this->_paging = new Maro_Paging_Common($cache, array($this, "getRealData"), self::PAGING_MAX_PAGE, self::PAGING_NUM_RECORDS);
		
		return $this->_paging;
	}
	
	//private function
	private function deleteAllCache($userid = 0)
	{
		$email = '';
		
		$cache = $this->getCacheAdapter();		
				
		if($userid != 0)
		{
			//lay username tuong ung voi mid => xoa cache key username
			$item = $this->getUserByUserid($userid);			
			if($item != null)
			{
				$username = $item['email'];				
			}			
		}		
//1		$cache->deleteAllCache($userid);		
		$cache->deleteAllCache($userid,$item['email'], $item['username']);
		
		// clean paging cache
		$key = $cache->getKeyListPaging();
		
		if ($this->_paging == null)
			$this->_paging = $this->getPagingBusiness();
			
		$this->_paging->clearCachePaging($key);
	}	
	
	//get cache instance and storage adapter
	
	/**
	 * get cache instance
	 *
	 * @return Business_Ws_User_Cache_Interface
	 */
	private function getCacheAdapter()
	{
		return Business_Ws_User_Cache_Factory::factory('memcache');
	}
	
	/**
	 * get storage instance
	 * @return Business_Ws_User_Storage_Interface
	 */
	private function getStorageAdapter()
	{
		return Business_Ws_User_Storage_Factory::factory('mysql');
	}
	
}

?>
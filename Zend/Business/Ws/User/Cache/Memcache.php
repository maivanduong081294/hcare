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

	class Business_Ws_User_Cache_Memcache implements Business_Ws_User_Cache_Interface  
	{
		const KEY_LIST = 'User.list';
		const KEY_DETAIL_USERID = 'User.list.userid.%s';
		const KEY_DETAIL_EMAIL = 'User.list.email.%s';
                const KEY_DETAIL_USERNAME = 'User.list.username.%s';
		const KEY_LIST_PAGING = 'User.list.paging';
		
		private $_cache_instance = 'ws';
		
		protected static $_instance;
		
		/**
		 * get instance of Business_WsUser_Cache_Memcache
		 *
		 * @return Business_Ws_User_Cache_Memcache
		 */
		public static function getInstance()
		{
			if (self::$_instance == NULL)
				self::$_instance = new self();
			return self::$_instance;
		}
		
		function __construct()
		{
			
		}
		
		

		private function getKeyList()
		{
			return sprintf(self::KEY_LIST);
		}
		
		private function getKeyDetailByUserid($userid)
		{
			return sprintf(self::KEY_DETAIL_USERID, $userid);	 
		}
		
		private function getKeyDetailByEmail($email)
		{
			return sprintf(self::KEY_DETAIL_EMAIL, $email);
		}

                private function getKeyDetailByUsername($username)
		{
			return sprintf(self::KEY_DETAIL_USERNAME, $username);
		}
		
		public function getKeyListPaging()
		{
			return sprintf(self::KEY_LIST_PAGING);
		}
		
		/**
		 * get cache instance
		 *
		 * @return Maro_Cache
		 */
		public function getCacheInstance()
		{
			return GlobalCache::getCacheInstance($this->_cache_instance);		
		}
		
		public function getUserList()
		{
			$cache = $this->getCacheInstance();
			$key = $this->getKeyList();
			$result = $cache->getCache($key);
			return $result;
		}
		
		public function setUserList($result)
		{
			$cache = $this->getCacheInstance();
			$key = $this->getKeyList();
			$cache->setCache($key, $result);
		}
		
		public function getUserByUserid($userid)
		{
			$cache = $this->getCacheInstance();
			$key = $this->getKeyDetailByUserid($userid);
			$result = $cache->getCache($key);
			return $result;
		}
		
		public function setUserByUserid($userid, $result)
		{
			$cache = $this->getCacheInstance();
			$key = $this->getKeyDetailByUserid($userid);
			$cache->setCache($key, $result);
		}
		
		public function getUserByEmail($email)
		{
			$cache = $this->getCacheInstance();
			$key = $this->getKeyDetailByEmail($email);
			$result = $cache->getCache($key);
			return $result;
		}
		
		public function setUserByEmail($email, $result)
		{
			$cache = $this->getCacheInstance();
			$key = $this->getKeyDetailByEmail($email);
			$cache->setCache($key, $result);
		}

                public function getUserByUsername($username)
		{
			$cache = $this->getCacheInstance();
			$key = $this->getKeyDetailByUsername($username);
			$result = $cache->getCache($key);
			return $result;
		}

		public function setUserByUsername($username, $result)
		{
			$cache = $this->getCacheInstance();
			$key = $this->getKeyDetailByUsername($username);
			$cache->setCache($key, $result);
		}


		public function getMultiUserByUserid($userids = array())
		{
			if(!is_array($userids) || count($userids) == 0) return array();
			$cache = $this->getCacheInstance();
			$keys = array();
			
			//chuan bi cac mang keys		
			for($i=0;$i<count($userids);$i++)
			{
				
				$keys[] = $this->getKeyDetailByUserid($userids[$i]);
			}
			
			$result = $cache->getMultiCache($keys);
			if($result === FALSE || $result == null || !is_array($result) || count($result) == 0) return array();
			
			$_return = array();
			
			for($i=0;$i<count($userids);$i++)
			{
				$key = $this->getKeyDetailByUserid($userids[$i]);
				if(array_key_exists($key,$result))
				{
					$_return[$userids[$i]] = $result[$key];
				}
			}
			return $_return;
		}
		
//1		public function deleteAllCache($userid = 0)
		public function deleteAllCache($userid = 0, $email = '', $username = '')
		{
			$cache = $this->getCacheInstance();
			
			$key = $this->getKeyList();
			$cache->deleteCache($key);
			
			if ($userid != 0)
			{
				$key = $this->getKeyDetailByUserid($userid);
				$cache->deleteCache($key);
			}
			
			if (!empty($email))
			{
				$key = $this->getKeyDetailByEmail($email);
				$cache->deleteCache($key);
			}
			if (!empty($username))
			{
				$key = $this->getKeyDetailByUsername($username);
				$cache->deleteCache($key);
			}
		}
		
	};
?>
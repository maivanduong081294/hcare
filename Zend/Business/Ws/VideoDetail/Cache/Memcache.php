<?php
// ws_videodetail = .vc_provider_accout : ten bang
// Business_Ws_ = .Business_EVoucher_ : duong dan den lop business
// VideoDetail = .ProviderAccount : ten goi nho cua table 
// videoid = .videoid : khoa chinh
// Videoid = .Pid : khoa chinh viet in dunug trong ten ham
// VIDEOID = .PID : khoa chinh viet in toan bo
// userid = .username : lay noi dung theo ten voi key username
// Userid = .Username : lay noi dung theo ten voi key username
// USERID = .USERNAME : key name "username" viet in toan bo
// maindb = .maindb
// ws = vc;

	class Business_Ws_VideoDetail_Cache_Memcache implements Business_Ws_VideoDetail_Cache_Interface  
	{
		const KEY_LIST = 'VideoDetail.list';
		const KEY_DETAIL_VIDEOID = 'VideoDetail.list.videoid.%s';
		const KEY_DETAIL_USERID = 'VideoDetail.list.userid.%s';
		const KEY_LIST_PAGING = 'VideoDetail.list.paging';
                const KEY_LIST_PAGING_userid = 'VideoDetail.list.paging.userid.%s';
		
		private $_cache_instance = 'ws';
		
		protected static $_instance;
		
		/**
		 * get instance of Business_WsVideoDetail_Cache_Memcache
		 *
		 * @return Business_Ws_VideoDetail_Cache_Memcache
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
		
		private function getKeyDetailByVideoid($videoid)
		{
			return sprintf(self::KEY_DETAIL_VIDEOID, $videoid);	 
		}
		
		private function getKeyDetailByUserid($userid)
		{
			return sprintf(self::KEY_DETAIL_USERID, $userid);
		}
		
		public function getKeyListPaging()
		{
			return sprintf(self::KEY_LIST_PAGING);
		}

             public function getKeyListPagingByuserid($userid)
		{
			return sprintf(self::KEY_LIST_PAGING_userid,$userid);
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
		
		public function getVideoDetailList()
		{
			$cache = $this->getCacheInstance();
			$key = $this->getKeyList();
			$result = $cache->getCache($key);
			return $result;
		}
		
		public function setVideoDetailList($result)
		{
			$cache = $this->getCacheInstance();
			$key = $this->getKeyList();
			$cache->setCache($key, $result);
		}
		
		public function getVideoDetailByVideoid($videoid)
		{
			$cache = $this->getCacheInstance();
			$key = $this->getKeyDetailByVideoid($videoid);
			$result = $cache->getCache($key);
			return $result;
		}
		
		public function setVideoDetailByVideoid($videoid, $result)
		{
			$cache = $this->getCacheInstance();
			$key = $this->getKeyDetailByVideoid($videoid);
			$cache->setCache($key, $result);
		}
		
		public function getVideoDetailByUserid($userid)
		{
			$cache = $this->getCacheInstance();
			$key = $this->getKeyDetailByUserid($userid);
			$result = $cache->getCache($key);
			return $result;
		}
		
		public function setVideoDetailByUserid($userid, $result)
		{
			$cache = $this->getCacheInstance();
			$key = $this->getKeyDetailByUserid($userid);
			$cache->setCache($key, $result);
		}
		
		public function getMultiVideoDetailByVideoid($videoids = array())
		{
			if(!is_array($videoids) || count($videoids) == 0) return array();
			$cache = $this->getCacheInstance();
			$keys = array();
			
			//chuan bi cac mang keys		
			for($i=0;$i<count($videoids);$i++)
			{
				
				$keys[] = $this->getKeyDetailByVideoid($videoids[$i]);
			}
			
			$result = $cache->getMultiCache($keys);
			if($result === FALSE || $result == null || !is_array($result) || count($result) == 0) return array();
			
			$_return = array();
			
			for($i=0;$i<count($videoids);$i++)
			{
				$key = $this->getKeyDetailByVideoid($videoids[$i]);
				if(array_key_exists($key,$result))
				{
					$_return[$videoids[$i]] = $result[$key];
				}
			}
			return $_return;
		}
		
//1		public function deleteAllCache($videoid = 0)
		public function deleteAllCache($videoid = 0, $userid = '')
		{
			$cache = $this->getCacheInstance();
			
			$key = $this->getKeyList();
			$cache->deleteCache($key);
			
			if ($videoid != 0)
			{
				$key = $this->getKeyDetailByVideoid($videoid);
				$cache->deleteCache($key);
			}
			
			if (!empty($userid))
			{
				$key = $this->getKeyDetailByUserid($userid);
				$cache->deleteCache($key);
			}
		}
		
	};
?>
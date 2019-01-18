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

class Business_Ws_VideoDetail
{

	protected static $_instance = null;

		//for paging
	CONST PAGING_NUM_RECORDS = 100;
	CONST PAGING_MAX_PAGE = 100;
	private $_paging = null;

	/**
	 * get instance of Business_Ws_VideoDetail
	 *
	 * @return Business_Ws_VideoDetail
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

	public function getVideoDetailList()
	{
		//read cache first
		$cache = $this->getCacheAdapter();
		$result = $cache->getVideoDetailList();
		if($result === FALSE) //miss cache
		{
			$storage = $this->getStorageAdapter();
			$result = $storage->getVideoDetailList();
			if(!is_null($result) && is_array($result))
			{
				$cache->setVideoDetailList($result);
			}
		}

		return $result;
	}

	public function getVideoDetailByVideoid($videoid = 0)
	{
		if(intval($videoid) == 0) return null;
		//read cache first
		$cache = $this->getCacheAdapter();
		$result = $cache->getVideoDetailByVideoid($videoid);
		if($result === FALSE) //miss cache
		{
			$storage = $this->getStorageAdapter();
			$result = $storage->getVideoDetailByVideoid($videoid);
			if(!is_null($result))
			{
				$cache->setVideoDetailByVideoid($videoid, $result);
			}
		}
		return $result;
	}

	public function getVideoDetailByUserid($userid)
	{
		if(empty($userid)) return null;
		//read cache first
		$cache = $this->getCacheAdapter();
		$result = $cache->getVideoDetailByUserid($userid);
		if($result === FALSE) //miss cache
		{
			$storage = $this->getStorageAdapter();
			$result = $storage->getVideoDetailByUserid($userid);
			if(!is_null($result))
			{
				$cache->setVideoDetailByUserid($userid, $result);
			}
		}
		return $result;
	}

	//	lay info nhieu provider 1 luc
	public function getMultiVideoDetailByVideoid($videoids = array())
	{
		if(is_array($videoids) && count($videoids) > 0)
		{
			//read cache first
			$cache = $this->getCacheAdapter();

			$result = $cache->getMultiVideoDetailByVideoid($videoids);

			if($result == null)
			{
				$result = array();
			}

			for($i=0;$i<count($videoids);$i++)
			{
				if(!array_key_exists($videoids[$i],$result))
				{
					$item_miss = $this->getVideoDetailByVideoid($videoids[$i]);
					if($item_miss != null)
						$result[$videoids[$i]] = $item_miss;
				}
			}
			return $result;
		}
		return null;
	}


	public function insertVideoDetail($data)
	{
		$storage = $this->getStorageAdapter();
		$videoid = $storage->insertVideoDetail($data);
		if(intval($videoid) > 0)
		{
			//insert thanh cong -> xoa cache
			$this->deleteAllCache($videoid);
		}
		return $videoid;
	}

	public function updateVideoDetail($videoid,$data)
	{
		$storage = $this->getStorageAdapter();
		$result = $storage->updateVideoDetail($videoid, $data);
		if($result)
		{
			//update thanh cong -> xoa cache
			$this->deleteAllCache($videoid);
		}
		return $result;
	}

	public function deleteVideoDetail($videoid)
	{
		$storage = $this->getStorageAdapter();
		$result = $storage->deleteVideoDetail($videoid);
		if($result)
		{
			//delete thanh cong -> xoa cache
			$this->deleteAllCache($videoid);
		}
	}


	public function getVideoDetailListWithPaging($offset = 0, $records = 20)
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
		$result = $storage->getVideoDetailListWithPaging($offset, $records);

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



     public function getVideoDetailListWithPagingByuserid($userid, $offset = 0, $records = 20)
	{
		$paging = $this->getPagingBusinessByuserid();

		$cache = $this->getCacheAdapter();

		$key = $cache->getKeyListPaging();

		//$paging->clearCachePaging($key);
		$params = array(
                 'userid' => $userid
		);

		$result = $paging->getData($offset,$records,$key,$params);
		return $result;
	}


        /**
	 * Enter description here...
	 *
	 * @return Maro_Paging_Interface
	 */
	private function getPagingBusinessByuserid()
	{
		if($this->_paging_userid != null) return $this->_paging_userid;
		$_cache = $this->getCacheAdapter();

		$cache = $_cache->getCacheInstance();

		$this->_paging_userid = new Maro_Paging_Common($cache, array($this, "getRealData_userid"), self::PAGING_MAX_PAGE, self::PAGING_NUM_RECORDS);

		return $this->_paging_userid;
	}


     public function getRealDatauserid($userid, $page)
	{
		$offset = 0;
		$records = self::PAGING_NUM_RECORDS;

		if ($page == 0)
			$offset = 0;
		else
			$offset = $page * $records;
		$self = self::getInstance();

		$storage = $this->getStorageAdapter();
		$result = $storage->getVideoDetailListWithPagingByuserid($userid, $offset, $records);

		return $result;
	}

	//private function
	private function deleteAllCache($videoid = 0)
	{
		$userid = '';

		$cache = $this->getCacheAdapter();

		if($videoid != 0)
		{
			//lay username tuong ung voi mid => xoa cache key username
			$item = $this->getVideoDetailByVideoid($videoid);
			if($item != null)
			{
				$username = $item['userid'];
			}
		}
//1		$cache->deleteAllCache($videoid);
		$cache->deleteAllCache($videoid,$item['userid']);

		// clean paging cache
		$key = $cache->getKeyListPaging();

		if ($this->_paging == null)
			$this->_paging = $this->getPagingBusiness();

		$this->_paging->clearCachePaging($key);

              $key = $cache->getKeyListPagingByuserid($item['userid']);

		if ($this->_paging_userid == null)
			$this->_paging_userid = $this->getPagingBusinessByuserid();

		$this->_paging_userid->clearCachePaging($key);

	}

	//get cache instance and storage adapter

	/**
	 * get cache instance
	 *
	 * @return Business_Ws_VideoDetail_Cache_Interface
	 */
	private function getCacheAdapter()
	{
		return Business_Ws_VideoDetail_Cache_Factory::factory('memcache');
	}

	/**
	 * get storage instance
	 * @return Business_Ws_VideoDetail_Storage_Interface
	 */
	private function getStorageAdapter()
	{
		return Business_Ws_VideoDetail_Storage_Factory::factory('mysql');
	}

}

?>
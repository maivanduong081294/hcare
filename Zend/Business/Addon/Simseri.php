<?php

class Business_Addon_Simseri extends Business_Abstract
{
	private $_tablename = 'addon_sim_seri';

	const KEY_LIST = 'asim.list.%s';			//key of list.questionid
	const KEY_DETAIL = 'asim.detail.%s';		//key of detail.id


	private static $_instance = null;

	function __construct()
	{
	}

	//public static function
	/**
	 * get instance of Business_Addon_Simseri
	 *
	 * @return Business_Addon_Simseri
	 */
	public static function getInstance()
	{
		if(self::$_instance == null)
		{
			self::$_instance = new self();
		}
		return self::$_instance;
	}
        
	private function getKeyListByStore($storeid)
	{
		return sprintf(self::KEY_LIST,$storeid);
	}

	private function getKeyDetail($id)
	{
		return sprintf(self::KEY_DETAIL,$id);
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
	private function getCacheInstance()
	{
		$cache = GlobalCache::getCacheInstance('ws');
		return $cache;
	}
        public function excute($query){
            $db     =  $this->getDbConnection();
            $result = $db->query($query);
            return $result;
        }
        public function getList($vote_id,$keywork,$check_sim="",$offset=0, $records=20){
        $cache = $this->getCacheInstance();
        $key    = $this->_tablename."getList".$vote_id.$keywork.$check_sim.$offset.$records;
        $result = $cache->getCache($key);
//        $result = FALSE;
        if ($result === FALSE) {
            $db = $this->getDbConnection();
            $query = "SELECT * FROM  $this->_tablename where 1= 1 and storeid = $vote_id ";
            if($keywork != null){
                $query .= " and seri like '%$keywork%' ";
            }
            if($check_sim != null){
                $query .=" and status = $check_sim ";
            }
//            $query .= " ORDER BY datetime DESC ";
            $query .= " LIMIT $records OFFSET $offset ";
//            var_dump($query);//exit();
            $result = $db->fetchAll($query);
            $cache->setCache($key, $result,60*5);
            
        }
        return $result;
    }
        
        
        public function getSeriNotUsed($vote_id){
        $cache = $this->getCacheInstance();
        $key = 'getSeriNotUsed'.$this->_tablename;
//        $cache->deleteCache($key);exit();
        $result = $cache->getCache($key);
        $result = FALSE;
        if ($result === FALSE) {
            $db = $this->getDbConnection();
            $query = "SELECT *, rand() as random FROM  $this->_tablename where status = 0 and storeid = $vote_id";
            $query.=" ORDER BY random DESC ";
            $query .= " LIMIT 0, 1 ";
            $result = $db->fetchAll($query);
            $result =$result[0];
            $cache->setCache($key, $result);
            
        }
        return $result;
    }
	

	public function getListByStore($storeid, $offset=0, $records=100)
	{
		$cache = $this->getCacheInstance();
		$key = $this->getKeyListByStore($storeid.$offset.$records);
		$result = $cache->getCache($key);
		if($result === FALSE)
		{
			$db = $this->getDbConnection();
			$query = "SELECT * FROM " . $this->_tablename . " WHERE storeid=? LIMIT $offset, $records";
			$data = array($storeid);
			$result = $db->fetchAll($query,$data);
			if(!is_null($result) && is_array($result))
			{
				$cache->setCache($key, $result);
			}
		}
		return $result;
	}

	public function getDetailBySeri($seri)
	{
            $cache = $this->getCacheInstance();
            $key = $this->getKeyDetail($seri);
            $result = $cache->getCache($key);
            if($result === FALSE)
            {
                    $db = $this->getDbConnection();
                    $query = "SELECT * FROM " . $this->_tablename . " WHERE seri=?";
                    $data = array($seri);
                    $result = $db->fetchAll($query,$data);
                    if($result != null && is_array($result))
                    {
                            $result = $result[0];
                            $cache->setCache($key, $result);
                    }
            }
            return $result;
	}
	public function getDetailBySeriByVote($seri,$storied)
	{
            $cache = $this->getCacheInstance();
            $key = $this->getKeyDetail($seri.$storied);
            $result = $cache->getCache($key);
            $result =FALSE;
            if($result === FALSE)
            {
                    $db = $this->getDbConnection();
                    $query = "SELECT * FROM " . $this->_tablename . " WHERE seri=$seri and storeid = $storied and status = 0 ";
                    $result = $db->fetchAll($query);
                    if($result != null && is_array($result))
                    {
                            $result = $result[0];
                            $cache->setCache($key, $result);
                    }
            }
            return $result;
	}
	public function get_detail_by_storeid($seri,$storied)
	{
            $cache = $this->getCacheInstance();
            $key = "get_detail_by_storeid".$this->_tablename.$seri.$storied;
            $result = $cache->getCache($key);
            $result =FALSE;
            if($result === FALSE)
            {
                    $db = $this->getDbConnection();
                    $query = "SELECT * FROM " . $this->_tablename . " WHERE seri=$seri and storeid = $storied";
                    $result = $db->fetchAll($query);
                    if($result != null && is_array($result))
                    {
                            $result = $result[0];
                            $cache->setCache($key, $result);
                    }
            }
            return $result;
	}
        
        public function getTop1NotUsed() {
            $db = $this->getDbConnection();
            $query = "SELECT * FROM " . $this->_tablename . " WHERE status=0 ORDER BY datetime ACS LIMIT 0, 1";
            $result = $db->fetchAll($query);
            return $result[0];
        }
        
        public function executeQueryInsert($query) {
            $db = $this->getDbConnection();
            if ($query !=null) {
                return $db->query($query);
            }
        }

	public function insert($data)
	{
		$db = $this->getDbConnection();
		$result = $db->insert($this->_tablename, $data);
		if($result > 0)
		{
			$this->_deleteAllCache($qid);
			$last_id = $db->lastInsertId();
			return $last_id;
		}
		else return 0;
	}

	public function update($seri, $data)
	{
            $where = array();
            $where[] = "seri='" . parent::adaptSQL($seri) . "'";
            $db = $this->getDbConnection();
            $result = $db->update($this->_tablename, $data,$where);
            return $result;
	}
        public function updateAdonSeriSim($seri) {
        $db= $this->getDbConnection();
        $query = "seri = ".$seri;
        $data = array(
            "status" => 1
        );
        $result = $db->update($this->_tablename, $data,$query);
        return $result;
    }
        public function restore($seri) {
        $db= $this->getDbConnection();
        $query = "seri = ".$seri;
        $data = array(
            "status" => 0
        );
        $result = $db->update($this->_tablename, $data,$query);
        return $result;
    }
	public function delete($seri)
	{
            //get current menu
            $where = array();
            $where[] = "seri='" . parent::adaptSQL($seri) . "'";
            $db = $this->getDbConnection();
            $result = $db->delete($this->_tablename, $where);
            return $result;
	}



}

?>

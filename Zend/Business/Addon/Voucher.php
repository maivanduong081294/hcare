<?php

class Business_Addon_Voucher extends Business_Abstract
{
	private $_tablename = 'ws_vouchers';

	const KEY_LIST = 'ws_vouchers.list.%s';			//key of list.questionid
	const KEY_DETAIL = 'ws_vouchers.detail.%s';		//key of detail.id


	private static $_instance = null;

	function __construct()
	{
	}

	//public static function
	/**
	 * get instance of Business_Addon_Voucher
	 *
	 * @return Business_Addon_Voucher
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
		$db    	= Globals::getDbConnection('codedb');
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
        public function getListByVoucher($voucher){
            $cache = $this->getCacheInstance();
            $key    = $this->_tablename."getListByVoucher".$voucher;
            $result = $cache->getCache($key);
            if ($result === FALSE) {
                $db = $this->getDbConnection();
                $query = "SELECT * FROM  $this->_tablename where code_name IN ($voucher) ";
//                var_dump($query);exit();
                $result = $db->fetchAll($query);
                $cache->setCache($key, $result,3600);
            }
            return $result;
        }
        public function get_group_sectionid(){
            $cache = $this->getCacheInstance();
            $key    = $this->_tablename."get_group_sectionid";
            $result = $cache->getCache($key);
            if ($result === FALSE) {
                $db = $this->getDbConnection();
                $query = "SELECT sectionid,note FROM $this->_tablename WHERE `used` = 0 group by sectionid order by sectionid desc";
    //            var_dump($query);exit();
                $result = $db->fetchAll($query);
                $cache->setCache($key, $result,60*5);
            }
            return $result;
        }
        public function getListByPhone($phone,$m=0,$sectionid=0){
            $cache = $this->getCacheInstance();
            $key    = $this->_tablename."getListByPhone".$phone.$m.$sectionid;
            $result = $cache->getCache($key);
            $result = FALSE;
            if ($result === FALSE) {
                $db = $this->getDbConnection();
                $query = "SELECT * FROM  $this->_tablename where phone = '$phone' ";
                if((int)$m > 0){
                   $query .=" and MONTH(code_created) = $m"; 
                }
                if((int)$sectionid > 0){
                   $query .=" and sectionid = $sectionid"; 
                }
    //            var_dump($query);exit();
                $result = $db->fetchAll($query);
                $cache->setCache($key, $result,60*5);
            }
            return $result;
        }
        public function get_list_by_phone($phone,$sectionid=0,$id_addon_user=0){
            $cache = $this->getCacheInstance();
            $key    = $this->_tablename."get_list_by_phone".$phone.$sectionid;
            $result = $cache->getCache($key);
            $result = FALSE;
            if ($result === FALSE) {
                $db = $this->getDbConnection();
                $query = "SELECT * FROM  $this->_tablename where phone = '$phone' ";
                if((int)$sectionid > 0){
                   $query .=" and sectionid = $sectionid"; 
                }
                if((int)$id_addon_user > 0){
                   $query .=" and id_addon_user = $id_addon_user"; 
                }
    //            var_dump($query);exit();
                $result = $db->fetchAll($query);
                $cache->setCache($key, $result,60*5);
            }
            return $result;
        }
        public function get_random_voucher($sectionid=0,$limit=1){
            $cache = $this->getCacheInstance();
            $key    = $this->_tablename."get_random_voucher".$limit.$sectionid;
            $result = $cache->getCache($key);
            $result = FALSE;
            if ($result === FALSE) {
                $db = $this->getDbConnection();
                $query = "SELECT * FROM  $this->_tablename where used = 0 and code_publish =0 and sectionid = $sectionid ";
                $query .="ORDER BY RAND() limit $limit ";
    //            var_dump($query);exit();
                $result = $db->fetchAll($query);
                $cache->setCache($key, $result,60*5);
            }
            return $result;
        }
        public function getListByBill($total_bill){
            $total_bill = (int)$total_bill;
            $cache = $this->getCacheInstance();
            $key    = $this->_tablename."getListByBill".$total_bill;
            $result = $cache->getCache($key);
            $result = FALSE;
            if ($result === FALSE) {
                $db = $this->getDbConnection();
                $query = "SELECT * FROM  $this->_tablename where id_addon_user = $total_bill ";
//                var_dump($query);exit();
                $result = $db->fetchAll($query);
                $result =  $result[0];
                $cache->setCache($key, $result,60*5);
            }
            return $result;
        }
        public function get_list_by_id_addon_user($id_addon_user,$id=0){
            $total_bill = (int)$total_bill;
            $cache = $this->getCacheInstance();
            $key    = $this->_tablename."get_list_by_id_addon_user".$id_addon_user.$id;
            $result = $cache->getCache($key);
            $result = FALSE;
            if ($result === FALSE) {
                $db = $this->getDbConnection();
                $query = "SELECT * FROM  $this->_tablename where id_addon_user = $id_addon_user ";
                if((int)$id >0){
                    $query .=" and sectionid = $id ";
                }
                $result = $db->fetchAll($query);
                $cache->setCache($key, $result,60*5);
            }
            return $result;
        }

        public function getList($vote_id,$keywork,$check_sim="",$offset=0, $records=20){
        $cache = $this->getCacheInstance();
        $key    = $this->_tablename."getList".$vote_id.$keywork.$check_sim;
        $result = $cache->getCache($key);
        $result = FALSE;
        if ($result === FALSE) {
            $db = $this->getDbConnection();
            $query = "SELECT * FROM  $this->_tablename where 1= 1 and storeid = $vote_id ";
            if($keywork != null){
                $query .= " and seri like '%$keywork%' ";
            }
            if($check_sim != null){
                $query .=" and status = $check_sim ";
            }
            $query .= " ORDER BY datetime DESC ";
            $query .= " LIMIT $records OFFSET $offset ";
//            var_dump($query);exit();
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

	public function getDetailByCode($code)
	{
            $db = $this->getDbConnection();
            $query = "SELECT * FROM " . $this->_tablename . " WHERE code_name='$code'";
            $result = $db->fetchAll($query);
            return $result[0];
	}
	public function getDetailBySeriByVote($seri,$storied)
	{
            $cache = $this->getCacheInstance();
            $key = $this->getKeyDetail($seri.$storied);
            $result = $cache->getCache($key);
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

	public function insert($data) {
            $db = $this->getDbConnection();
            $result = $db->insert($this->_tablename, $data);
            return $result;
        }

	public function update($id, $data)
	{
            $where = array();
            $where[] = "code_name='" . parent::adaptSQL($id) . "'";
            $db = $this->getDbConnection();
            $result = $db->update($this->_tablename, $data,$where);
            return $result;
	}
//        public function update($id,$data){
//            $db=  $this->getDbConnection();
//            $query = "id = ".$id;
//            $result = $db->update($this->_tablename, $data, $query);
//            return $result;
//        }
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

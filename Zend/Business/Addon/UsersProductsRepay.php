<?php

class Business_Addon_UsersProductsRepay extends Business_Abstract
{
	private $_tablename = 'users_products_repay';
	
	const KEY_LIST              = 'users_products_repay.list.%s';
	const KEY_LIST2             = 'users_products.list_repay.%s';
	const KEY_DETAIL = 'users_products_repay.uid.%s';
    
	protected static $_current_rights = null;
	
	protected static $_instance = null; 

	function __construct()
	{
		
	}
	
	/**
	 * get instance of Business_Addon_UsersProductsRepay
	 *
	 * @return Business_Addon_UsersProductsRepay
	 */
	public static function getInstance()
	{
		if(self::$_instance == null)
		{
			self::$_instance = new self();
		}
		return self::$_instance;
	}
        private function getKeyList($itemid){
        return sprintf(self::KEY_LIST, $itemid);
        }
        
        private function getKeyDetail($keywork){
            return sprintf(self::KEY_DETAIL,$keywork);
        }
        private function deleteKeyDetail($itemid) {
        $cache = $this->getCacheInstance();
        $key = $this->getKeyDetail($itemid);
        $cache->deleteCache($key);
        }
        private function deleteKeyList($keyword="") {
            $cache = $this->getCacheInstance();
            $key = $this->getKeyList($keyword);
            $cache->deleteCache($key);
        }
        
        private function getKeyList2($keys){
        return sprintf(self::KEY_LIST2, $keys);
        }
        private function deleteKeyList2($keys=0) {
            $cache = $this->getCacheInstance();
//            $key = $this->getKeyList2($keys);
            $cache->deleteCache($keys);
        }
        public function get_list_by_idaddonuser_products_id($id_addon_user,$products_id){
            $db = $this->getDbConnection();
            $query = "select * from $this->_tablename where status = 1 and id_addon_user IN ($id_addon_user)";
            $result = $db->fetchAll($query);
            return $result;
        }
        public function get_list_by_idaddonuser($id_addon_user){
            $db = $this->getDbConnection();
            $query = "select * from $this->_tablename where status = 1 and id_addon_user IN ($id_addon_user)";
            $result = $db->fetchAll($query);
            return $result;
        }

        public function getListByKeywork($storeid=0,$keywork="",$status=1,$created_date="",$end_date=""){
            $cache = GlobalCache::getCacheInstance('event');
            $key = "getListByKeywork".  $this->_tablename.$storeid.$status.$created_date.$end_date.$keywork;
            $result = $cache->getCache($key);
//            $result = FALSE;
            if($result === FALSE){
                $db = $this->getDbConnection();
                $query =  "SELECT * FROM $this->_tablename  WHERE  1 = 1 ";
                if($storeid != 0){
                    $query .=" and storeid = $storeid";
                }
                if($keywork != null){
                    $query .=" and (id_addon_user = '$keywork' or phone like '%$keywork%' or fullname like '%$keywork%') ";
                }
                if($created_date != null){
                    $query .=" and datetime >= '$created_date' ";
                }
                if($end_date != null){
                    $query .=" and datetime <= '$end_date' ";
                }
                $query .= " and status = $status";
                $result = $db->fetchAll($query);
                if(!is_null($result) && is_array($result)){
                    $cache->setCache($key, $result,300);
                }
            }
            
            return $result;
        }

        public function getListBillId($userid,$created_date, $end_date,$member_id="",$keywork,$status="-1"){
            
            $c_date = date('Y-m-d',  strtotime($created_date));
            $e_date = date('Y-m-d',strtotime($end_date));
            $cache = $this->getCacheInstance('app');
            $key = "getListBillId.$this->_tablename.$userid.$c_date.$e_date.$keywork";
            $result = $cache->getCache($key);
            if($result === FALSE)
            {
                $db = $this->getDbConnection();
                $query =  "SELECT * FROM $this->_tablename  WHERE  is_actived = 1 ";
                if($userid != "" || $userid != null){
                    $query.="  and vote_id = '$userid' ";
                }
                if($member_id != 0 || $member_id != null){
                    $query.=" and id_users = $member_id ";
                }
                if($keywork !=null){
                    if($userid ==167){
                        $query.=" and (id_addon_user = '$keywork' or imes = '$keywork' or fullname_addon like '%$keywork%' or phone_addon like '%$keywork%' or order_code like '%$keywork%') ";
                    }else{
                        $query.=" and (id_addon_user = '$keywork' or imes = '$keywork' or fullname_addon like '%$keywork%' or phone_addon like '%$keywork%') ";
                    }
                }
                if($userid ==167 && $status !="-1"){
                    $query .= " and status_ghn = $status";
                }
                $query .= " and create_date > '$created_date' and create_date < '$end_date' group by id_addon_user";
                $query .= " ORDER BY create_date DESC";
                $result = $db->fetchAll($query);

                if(!is_null($result) && is_array($result))
                {
                    $cache->setCache($key, $result,60*5);
                }
            }
        return $result;
    }
        
        
        
        
        
        public function getCountOption($cated_id="",$flag="",$is_actived=""){
            $db = $this->getDbConnection();
            $query  = " SELECT  sum(products_price) as sum, count(products_price) as total FROM $this->_tablename where 1=1";
            if($cated_id != null){
                $query  .= " and cated_id IN ($cated_id) ";
            }
            if($flag != null){
                $query  .= " and flag = $flag ";
            }
            if($is_actived != null){
                $query  .= " and is_actived = $is_actived ";
            }
//            var_dump($query);exit();
            $result = $db->fetchAll($query);
            return $result;
        }
        public function getListSacombank($created_day="", $end_day="",$is_actived="",$vote_online="",$sacombank="")
	{
            $c_day = date('Y-m-d',  strtotime($created_day));
            $e_day = date('Y-m-d',  strtotime($end_day));
            $cache = $this->getCacheInstance();
		$key = "getListSacombank.$this->_tablename.$c_day.$e_day.$is_actived.$vote_online.$sacombank";
		$result = $cache->getCache($key);
//                $cache->deleteCache($key);exit();
//                var_dump($result);exit();
//                $result = FALSE;
		if($result === FALSE)
                {
                    $db = $this->getDbConnection();
                    $query  = "SELECT count(*) as total, sum(products_price) as sum,sum(reduction_money) as giamtien,sum(money_voucher) as tienvoucher FROM $this->_tablename where 1=1";
                    
                    if($created_day != null){
                        $query .=" and create_date >=  '$created_day'";
                    }
                    if($end_day != null){
                        $query .="  and create_date <= '$end_day'";
                    }
                    if($is_actived != null){
                        $query .="  and is_actived = $is_actived";
                    }
                    if($sacombank != null){
                        $query .="  and sacombank = $sacombank";
                    }
                    $query .=" and vote_online = $vote_online";
                    
//                    var_dump($query);exit();
                    $result = $db->fetchAll($query);
                    if(!is_null($result) && is_array($result))
                    {
                            $cache->setCache($key, $result,60*10);
                    }
                }
            return $result;		
	}

        public function getListByOption($created_day="", $end_day="",$is_actived="",$flag="",$vote_online="",$cated_id=0,$products_id="")
	{
            $c_day = date('Y-m-d',  strtotime($created_day));
            $e_day = date('Y-m-d',  strtotime($end_day));
            $cache = $this->getCacheInstance();
		$key = "getListByOption.$this->_tablename.$c_day.$e_day.$is_actived.$flag.$vote_online.$cated_id.$products_id";
		$result = $cache->getCache($key);
//                $cache->deleteCache($key);exit();
//                var_dump($result);exit();
//                $result = FALSE;
		if($result === FALSE)
                {
                    $db = $this->getDbConnection();
                    if($products_id != null){
                        $query  = "SELECT vote_id,products_id, sum(products_price) as sum, count(products_price) as countp,sum(reduction_money) as reduction_money,sum(money_voucher) as money_voucher FROM $this->_tablename where products_id = $products_id ";
                    }else{
                        $query   = "SELECT vote_id, cated_id, sum(products_price) as sum, count(products_price) as countp,sum(reduction_money) as reduction_money,sum(money_voucher) as money_voucher FROM $this->_tablename where 1=1 ";
                    }
                    
                    if($created_day != null){
                        $query .=" and create_date >=  '$created_day'";
                    }
                    if($end_day != null){
                        $query .="  and create_date <= '$end_day'";
                    }
                    if($is_actived != null){
                        $query .="  and is_actived = $is_actived";
                    }
                    if($flag != null){
                        $query .="  and flag = $flag ";
                    }
                    $query .=" and vote_online = $vote_online";
                    if($cated_id != 0){
                        $query .=" and cated_id = $cated_id";
                    }
                    if($products_id != null){
                        $query .= " group by vote_id";
                    }else{
                        $query .= " group by vote_id,cated_id";
                    }
                    
//                    var_dump($query);exit();
                    $result = $db->fetchAll($query);
                    if(!is_null($result) && is_array($result))
                    {
                            $cache->setCache($key, $result,60*10);
                    }
                }
            return $result;		
	}
        public function getDetailByOption($id_addon_user="",$is_actived="")
	{
            $db = $this->getDbConnection();
            $query = "select * from $this->_tablename where 1=1 ";
            if($id_addon_user != null){
                $query .=" and id_addon_user = $id_addon_user";
            }
            if($is_actived != null){
                $query .=" and is_actived = $is_actived";
            }
            $result = $db->fetchAll($query);
            return $result[0];
	}
        public function getListByCateId($is_actived="",$created_date="",$end_date="",$cated_id="",$vote_id="",$mb_id=""){
            $cache = $this->getCacheInstance();
		$key = "getListByCateId.$this->_tablename.$cated_id";
		$result = $cache->getCache($key);
//                $cache->deleteCache($key);exit();
//                var_dump($result);exit();
                $result = FALSE;
		if($result === FALSE)
                {
                    $db = $this->getDbConnection();
                    $query   = "SELECT product_color,products_price,products_price_cost,reduction_money,money_voucher,products_name,imes ,create_date ,vote_id ,fullname_addon ,phone_addon  FROM $this->_tablename where 1=1 ";
                    if($created_date != null){
                        $query .=" and create_date >= '$created_date' ";
                    }
                    if($end_date != null){
                        $query .=" and create_date <= '$end_date' ";
                    }
                    if($is_actived != null){
                        $query .=" and is_actived = '$is_actived' ";
                    }
                    if($cated_id != null){
                        $query .=" and cated_id = '$cated_id' ";
                    }
                    if($vote_id != null){
                        $query .=" and vote_id = '$vote_id' ";
                    }
                    if($mb_id != null){
                        $query .=" and id_users = '$mb_id' ";
                    }
//                    var_dump($query);exit();
                    $result = $db->fetchAll($query);
                    if(!is_null($result) && is_array($result))
                    {
                            $cache->setCache($key, $result,60*5);
                    }
                }
            return $result;
        }

        public function getListDisabled($products_id ='',$created_date,$end_date,$is_actived=1){
            $db = $this->getDbConnection();
            $query =  "SELECT vote_id,count(*) as tong  FROM `users_products` WHERE 1=1 ";
            if($products_id != null){
                $query .="  and products_id = 6020 ";
            }
            if($is_actived != 1){
                $query .=" and `is_actived` = 0  and addon_info != ''";
            }
            $query .= " and create_date > '$created_date' and create_date < '$end_date' ";
            $query .="group by vote_id ";
//            var_dump($query);exit();
            $result = $db->fetchAll($query);
            return $result;
            
        }

        public function getListByXiaomi($userid,$created_date, $end_date,$member_id="",$keywork,$products_id="",$all=""){
                $day = md5($created_date.$end_date);
                $cache = $this->getCacheInstance('app');
		$key = "getListByXiaomi.$this->_tablename.$userid.$day.$keywork";
		$result = $cache->getCache($key);
                $result = FALSE;
		if($result === FALSE)
		{
                    $db = $this->getDbConnection();
//                    $query =  "SELECT * FROM `users_products` up WHERE up.is_actived = 1 ";
                    $query =  "SELECT up.vote_id, sum(up.products_price) as tong,count(products_name) as soluong FROM `users_products` up WHERE up.is_actived = 1 ";
                    if($userid != "" || $userid != null){
                        $query.="  AND up.vote_id = '$userid' ";
                    }
                    if($member_id != 0 || $member_id != null){
                        $query.=" and up.id_users = $member_id ";
                    }
                    if($keywork !=null){
                        $query.=" and (up.id_addon_user = '$keywork' or up.imes = '$keywork' or fullname_addon like '%$keywork%' or phone_addon like '%$keywork%') ";
                    }
                    if($products_id != null){
                        if($all !=null){
                            $query.="  AND (up.products_id = '$products_id' or addon_info !='')";
                        }else{
                            $query.="  AND (up.products_id = '$products_id' )";
                        }
                        
                    }
                    $query .= " and up.create_date > '$created_date' and up.create_date < '$end_date' ";
                    $query .= " group by vote_id ORDER BY up.create_date DESC";
//                    var_dump($query);exit();
                    $result = $db->fetchAll($query);
                    
                    if(!is_null($result) && is_array($result))
                    {
                        $cache->setCache($key, $result,60*5);
                    }
                }
            return $result;
        }
        
        
        
        public function getSumPrice($id_users,$created_date,$end_date){
            $db = $this->getDbConnection();
            $created_date = $this->getDayCreate($created_date);
            $end_date  = $this->getDayEnd($end_date);
            $query = "select sum(products_price) as sump from $this->_tablename  where  id_users = $id_users and is_actived =1 and create_date >= '$created_date' and create_date <= '$end_date'";
//            var_dump($query);exit();
            $result = $db->fetchAll($query);
            return $result;
        }
        public function getListByPhone($phone){
            $db = $this->getDbConnection();
            $query  = "select * from $this->_tablename where 1=1 ";
            if($phone != null){
                $query .= " and phone_addon = $phone ";
            }
            $result = $db->fetchAll($query);
            return $result;
        }

        public function getList()
	{
		$cache = $this->getCacheInstance();
		$key = 'getListUsersProducts';
		$result = $cache->getCache($key);
						
		if($result === FALSE)
		{
			$db = $this->getDbConnection();
			$query = "SELECT * FROM $this->_tablename";
			$result = $db->fetchAll($query);
			if(!is_null($result) && is_array($result))
			{
				$cache->setCache($key, $result);
			}
		}
		
		return $result;		
	}
        public function getListOnlineByKeyword($created_date, $end_date,$keywork){
                $day = md5($created_date.$end_date);
                $cache = $this->getCacheInstance('app');
		$key = "getListOnlineByKeyword.$this->_tablename.$day.$keywork";
		$result = $cache->getCache($key);
                $result = FALSE;
		if($result === FALSE)
		{
                    $db = $this->getDbConnection();
                    $query =  "SELECT * FROM `users_products` WHERE is_actived = 1 ";
                    
                    if($keywork !=null){
                        $query.=" and (id_addon_user = '$keywork' or imes = '$keywork' or fullname_addon like '%$keywork%' or phone_addon like '%$keywork%') ";
                    }
                    $query .= " and create_date > '$created_date' and create_date < '$end_date' group by id_addon_user";
                    
                    $query .= " ORDER BY create_date DESC";
//                    var_dump($query);exit();
                    $result = $db->fetchAll($query);
                    
                    if(!is_null($result) && is_array($result))
                    {
                        $cache->setCache($key, $result);
                    }
                }
            return $result;
        }
        public function checkImes($imes)
	{
            $db = $this->getDbConnection();
            $query = "select * from $this->_tablename where imes = '$imes'";
//            var_dump($query);exit();
            $result = $db->fetchAll($query);
            return $result;
	}
        public function getDetailByImes($imes)
	{
            $db = $this->getDbConnection();
            $query = "select * from $this->_tablename where imes = '$imes'";
//            var_dump($query);exit();
            $result = $db->fetchAll($query);
            return $result[0];
	}
        public function getDetailByBill($id_addon_user)
	{
            $db = $this->getDbConnection();
            $query = "select * from $this->_tablename where id_addon_user = '$id_addon_user' and status = 1";
//            var_dump($query);exit(); 
            $result = $db->fetchAll($query);
            return $result[0];
	}
        public function getDetailByProductsId($products_id)
	{
            $db = $this->getDbConnection();
            $query = "select * from $this->_tablename where products_id = '$products_id'";
//            var_dump($query);exit();
            $result = $db->fetchAll($query);
            return $result[0];
	}
        public function getListByBillIdActived($id_addon_user,$is_actived="")
	{
            $db = $this->getDbConnection();
            $query = "select * from $this->_tablename where id_addon_user = '$id_addon_user' ";
            if($is_actived != null){
                $query .=" and is_actived = $is_actived";
            }
            $result = $db->fetchAll($query);
            return $result;
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
	
	/**
	 * Enter description here...
	 *
	 * @return Maro_Cache
	 */
	function getCacheInstance()
	{
		$cache = GlobalCache::getCacheInstance();		
		return $cache;
	}
       private function _deleteAllCache()
        {
                $cache = $this->getCacheInstance('ws');
                $cache->flushAll();
        }
	public function insert($data)
	{
//                $days       = date('Y-m-d',  strtotime($data["create_date"]));
//                $userid     = $data["vote_id"];
		$db = $this->getDbConnection();
		$result = $db->insert($this->_tablename,$data);
//                var_dump($result);exit();
//                $this->_deleteAllCache();
//                for($i=0;$i<4;$i++){
//                    $keys = $userid."_".$days."_".$i;
//                    $this->deleteKeyList2($keys);
//                }
                
                return $result;
//                var_dump($result);exit();
//                if ($result > 0) {
//                    $lastid= $db->lastInsertId($this->_tablename);
//                }
////                var_dump($lastid);exit('123');
//                return $lastid;
	}
        public function updateBill($data){
            $db=  $this->getDbConnection();
            $query = "id_addon_user = ".$data["id_addon_user"];
            $result = $db->update($this->_tablename, $data, $query);
            $this->_deleteAllCache();
            return $result;
        }

        public function _update($id){
            $db=  $this->getDbConnection();
            $query = "id = ".$id;
            $data =array(
                "isonline" => "1",
                "is_actived" => "1"
                );
            $result = $db->update($this->_tablename, $data, $query);
            $this->_deleteAllCache();
            return $result;
        }
        public function _update2($id){
            $db=  $this->getDbConnection();
            $query = "id_addon_user = ".$id;
            $data =array(
                "isonline" => "1",
                "is_actived" => "1"
                );
//                var_dump($id,$data);exit();
            $result = $db->update($this->_tablename, $data, $query);
            return $result;
        }
        public function _updateChagreCard($id,$data,$products_id,$createdate,$userid){
            $db=  $this->getDbConnection();
            $query = "id_addon_user = ".$id;
            $query .=" and products_id = ".$products_id;
            $query .= " and autoid =". $data["autoid"];
            $result = $db->update($this->_tablename, $data, $query);
//            $days       = date('Y-m-d',  strtotime($createdate));
//            $previousDay = Business_Addon_Options::getInstance()->getPrevDay($days);
//            $tmp1    = $userid."_".$previousDay."_0";
//            $keys = $this->getKeyList2($tmp1);
//            $this->deleteKeyList2($keys);
//            for($i=0;$i<4;$i++){
//                $tmp    = $userid."_".$days."_".$i;
//                $keys = $this->getKeyList2($tmp);
//                $this->deleteKeyList2($keys);
//            }
            
            return $result;
        }
        public function pupdate($query){
            $db     =  $this->getDbConnection();
            $result = $db->query($query);
            return $result;
        }
        public function updatePack($id,$pack_next="",$pack_pre=""){
            $db=  $this->getDbConnection();
            $query  = " update $this->_tablename set pack_next = '$pack_next',pack_pre = '$pack_pre' where id_addon_user = $id ";
//            var_dump($query);exit();
            $result = $db->fetchAll($query);
            return $result;
        }

        public function update($id,$data){
            $db=  $this->getDbConnection();
            $query = "id_addon_user = ".$id;
            $result = $db->update($this->_tablename, $data, $query);
            return $result;
        }

        public function update2($id,$data){
            $db=  $this->getDbConnection();
            $query = "autoid = ".$id;
            $result = $db->update($this->_tablename, $data, $query);
            return $result;
        }
        public function updateDelete($id,$data){
            $db=  $this->getDbConnection();
            $query = "id_addon_user = ".$id;
            $result = $db->update($this->_tablename, $data, $query);
            return $result;
        }
        public function _updateTraGop($id,$data,$products_id,$createdate,$userid){
            $db=  $this->getDbConnection();
            $query = "id_addon_user = ".$id;
            if($data["money_installment"] != 0){// charge the
                $query .=" and products_id = ".$products_id;
            }
            $result = $db->update($this->_tablename, $data, $query);
//            $days       = date('Y-m-d',  strtotime($createdate));
//            $previousDay = Business_Addon_Options::getInstance()->getPrevDay($days);
//            $tmp1    = $userid."_".$previousDay."_0";
//            $keys = $this->getKeyList2($tmp1);
//            $this->deleteKeyList2($keys);
//            for($i=0;$i<4;$i++){
//                $tmp    = $userid."_".$days."_".$i;
//                $keys = $this->getKeyList2($tmp);
//                $this->deleteKeyList2($keys);
//            }
            
            return $result;
        }
	public function getListByMember($uid,$months)
	{
//		$cache = $this->getCacheInstance();
//		$key = "getListByMember.$uid.$months";
//		$result = $cache->getCache($key);
//		if($result === FALSE)
//		{
			$db = $this->getDbConnection();
			$query   = "SELECT upd.*,aud.fullname,aud.phone,aud.createdate FROM  $this->_tablename upd, addon_users aud ";
                        $query  .= "where aud.id = upd.id_addon_user and id_users =$uid and upd.is_actived=1 and months =  $months";
                        $query  .=  " ORDER BY upd.id_addon_user DESC";
//                        var_dump($query);exit();
			$result = $db->fetchAll($query);
//			if(!is_null($result) && is_array($result))
//			{
//				$cache->setCache($key, $result);
//			}
//		}
		
		return $result;		
	}
	public function getListByMemberByMonths($uid,$month)
	{
		$cache = $this->getCacheInstance('ws');
		$key = "getlistbymemberbymonths".$this->_tablename.$uid.$month;
		$result = $cache->getCache($key);
		if($result === FALSE)
		{
			$db = $this->getDbConnection();
			$query   = "SELECT upd.*,aud.fullname,aud.phone,aud.createdate FROM  $this->_tablename upd, addon_users aud ";
                        $query  .= "where aud.id = upd.id_addon_user and id_users =$uid  and months =  $month";
                        $query  .=  " ORDER BY upd.id_addon_user DESC";
//                        var_dump($query);exit();
			$result = $db->fetchAll($query);
			if(!is_null($result) && is_array($result))
			{
				$cache->setCache($key, $result);
			}
		}
		
		return $result;		
	}
        public function getListByBillId($id_addon_user){
            $db = $this->getDbConnection();
            $query   = "SELECT * FROM  $this->_tablename ";
            $query  .= "where id_addon_user = $id_addon_user ";
            $query  .=  " ORDER BY id_addon_user DESC";
//            var_dump($query);exit();
            $result = $db->fetchAll($query);
            return $result;
        }

        public function getListById($id,$month)
	{
//		$cache = $this->getCacheInstance();
//		$key = "getListById.$id.$month";
//		$result = $cache->getCache($key);
//		if($result === FALSE)
//		{
			$db = $this->getDbConnection();
			$query   = "SELECT * FROM  $this->_tablename upd, addon_users aud ";
                        $query  .= "where aud.id = upd.id_addon_user and upd.id_addon_user =$id  and months =  $month";
                        $query  .=  " ORDER BY upd.id_addon_user DESC";
//                        var_dump($query);exit();
			$result = $db->fetchAll($query);
//			if(!is_null($result) && is_array($result))
//			{
//				$cache->setCache($key, $result);
//			}
//		}
		
		return $result;		
	}
	public function getListById2($id,$month)
	{
//		$cache = $this->getCacheInstance();
//		$key = "getListById.$id.$month";
//		$result = $cache->getCache($key);
//		if($result === FALSE)
//		{
			$db = $this->getDbConnection();
			$query   = "SELECT * FROM  $this->_tablename upd, addon_users aud ";
                        $query  .= "where aud.id = upd.id_addon_user and upd.id_addon_user =$id and is_actived = 1 and MONTH(create_date) =  $month";
                        $query  .=  " ORDER BY upd.id_addon_user DESC";
			$result = $db->fetchAll($query);
//			if(!is_null($result) && is_array($result))
//			{
//				$cache->setCache($key, $result);
//			}
//		}
		
		return $result;		
	}
	public function getListByUserid3($id, $create_day,$end_day,$cated_id,$cate_bonus,$quantity)
	{
//		$cache = $this->getCacheInstance();
//		$key = "getListByUserid3.$this->_tablename.$id.$month";
//		$result = $cache->getCache($key);
//		if($result === FALSE)
//		{
			$db = $this->getDbConnection();
			$query   = "SELECT * FROM  $this->_tablename ";
                        $query  .= "where vote_id = $id and is_actived = 1 and create_date >=  '$create_day' and create_date <= '$end_day' and cated_id = $cated_id";
                        $query  .=  " ORDER BY id_addon_user ASC";
                        if($cate_bonus == 2){
                            $query  .=  " LIMIT $quantity";
                        }
//                        var_dump($query);exit();
			$result = $db->fetchAll($query);
//			if(!is_null($result) && is_array($result))
//			{
//				$cache->setCache($key, $result);
//			}
//		}
                        
		return $result;		
	}
	public function getListByUserid4($id, $create_day,$end_day,$cated_id,$quantity)
	{
//		$cache = $this->getCacheInstance();
//		$key = "getListByUserid3.$this->_tablename.$id.$month";
//		$result = $cache->getCache($key);
//		if($result === FALSE)
//		{
			$db = $this->getDbConnection();
			$query   = "SELECT * FROM  $this->_tablename ";
                        $query  .= "where id_users = $id and is_actived = 1 and create_date >=  '$create_day' and create_date <= '$end_day' and cated_id = $cated_id";
                        $query  .=  " ORDER BY id_addon_user DESC";
//                        var_dump($query);exit();
			$result = $db->fetchAll($query);
//			if(!is_null($result) && is_array($result))
//			{
//				$cache->setCache($key, $result);
//			}
//		}
                        if(count($result) < $quantity){
                            $result = "";
                        }
		return $result;		
	}
	public function getListByUseridByCated($id, $create_day,$end_day,$cated_id)
	{
//		$cache = $this->getCacheInstance();
//		$key = "getListByUserid3.$this->_tablename.$id.$month";
//		$result = $cache->getCache($key);
//		if($result === FALSE)
//		{
			$db = $this->getDbConnection();
			$query   = "SELECT * FROM  $this->_tablename ";
                        $query  .= "where id_users =$id and is_actived = 1 and create_date >=  '$create_day' and create_date <= '$end_day' and cated_id = $cated_id";
                        $query  .=  " ORDER BY id_addon_user DESC";
//                        var_dump($query);exit();
			$result = $db->fetchAll($query);
//			if(!is_null($result) && is_array($result))
//			{
//				$cache->setCache($key, $result);
//			}
//		}
		return $result;		
	}
        public function getListByUserid5($id, $create_day,$end_day,$products_id)
	{
//		$cache = $this->getCacheInstance();
//		$key = "getListByUserid3.$this->_tablename.$id.$month";
//		$result = $cache->getCache($key);
//		if($result === FALSE)
//		{
			$db = $this->getDbConnection();
			$query   = "SELECT * FROM  $this->_tablename  ";
                        $query  .= "where id_users =$id and is_actived = 1 and create_date >=  '$create_day' and create_date <= '$end_day' and products_id IN ($products_id)";
                        $query  .=  " ORDER BY id_addon_user DESC";
//                        var_dump($query);exit();
			$result = $db->fetchAll($query);
//			if(!is_null($result) && is_array($result))
//			{
//				$cache->setCache($key, $result);
//			}
//		}
                        
		return $result;		
	}
        public function getListByUserid6($id, $create_day,$end_day,$products_id,$cate_bonus,$quantity)
	{
//		$cache = $this->getCacheInstance();
//		$key = "getListByUserid3.$this->_tablename.$id.$month";
//		$result = $cache->getCache($key);
//		if($result === FALSE)
//		{
			$db = $this->getDbConnection();
			$query   = "SELECT * FROM  $this->_tablename  ";
                        $query  .= "where vote_id =$id and is_actived = 1 and create_date >=  '$create_day' and create_date <= '$end_day' ";
                        if($products_id != null){
                            $query  .=  " and products_id IN ($products_id) ";
                        }
                        $query  .=  " ORDER BY id_addon_user ASC";
                        if($cate_bonus == 2){
                            $query  .=  " LIMIT $quantity";
                        }
			$result = $db->fetchAll($query);
//			if(!is_null($result) && is_array($result))
//			{
//				$cache->setCache($key, $result);
//			}
//		}
		return $result;		
	}
        
        public function getListGroupByUseridByCated($id, $create_day,$end_day,$cated_id,$quantity)
	{
//		$cache = $this->getCacheInstance();
//		$key = "getListByUserid3.$this->_tablename.$id.$month";
//		$result = $cache->getCache($key);
//		if($result === FALSE)
//		{
			$db = $this->getDbConnection();
			$query   = "SELECT * FROM  $this->_tablename ";
                        $query  .= "where vote_id IN ($id) and is_actived = 1 and create_date >=  '$create_day' and create_date <= '$end_day' and cated_id = $cated_id";
                        $query  .=  " ORDER BY id_addon_user DESC";
//                        var_dump($query);//exit();
			$result = $db->fetchAll($query);
//			if(!is_null($result) && is_array($result))
//			{
//				$cache->setCache($key, $result);
//			}
//		}
                        if(count($result) < $quantity){
                            $result = "";
                        }
		return $result;		
	}
        public function getListGroupByUserid($id, $create_day,$end_day,$products_id,$quantity)
	{
//		$cache = $this->getCacheInstance();
//		$key = "getListByUserid3.$this->_tablename.$id.$month";
//		$result = $cache->getCache($key);
//		if($result === FALSE)
//		{
			$db = $this->getDbConnection();
			$query   = "SELECT * FROM  $this->_tablename ";
                        $query  .= "where vote_id IN ($id) and is_actived = 1 and create_date >=  '$create_day' and create_date <= '$end_day' and products_id IN ($products_id)";
                        $query  .=  " ORDER BY id_addon_user DESC";
//                        var_dump($query);exit();
			$result = $db->fetchAll($query);
//			if(!is_null($result) && is_array($result))
//			{
//				$cache->setCache($key, $result);
//			}
//		}
                        if(count($result) < $quantity){
                            $result = "";
                        }
		return $result;		
	}
        public function getCountGroupByUserid($id, $create_day,$end_day,$products_id)
	{
            $db = $this->getDbConnection();
            $query   = "SELECT count(*) FROM  $this->_tablename ";
            $query  .= "where vote_id IN ($id) and is_actived = 1 and create_date >=  '$create_day' and create_date <= '$end_day' and products_id IN ($products_id) ";
            $query  .=  " ORDER BY id_addon_user DESC";
            $result = $db->fetchAll($query);
            return $result;		
	}
        public function getCountGroupByCated($id, $create_day,$end_day,$cated_id)
	{
            $db = $this->getDbConnection();
            $query   = "SELECT count(*) FROM  $this->_tablename ";
            $query  .= "where vote_id IN ($id) and is_actived = 1 and create_date >=  '$create_day' and create_date <= '$end_day' and cated_id = $cated_id";
            $query  .=  " ORDER BY id_addon_user DESC";
//            var_dump($query);exit();
            $result = $db->fetchAll($query);
            return $result;		
	}
        
        public function sumGroupByCatedByVote($create_day,$end_day,$cated_id,$vote_id)
	{
            $db = $this->getDbConnection();
            $query   = "SELECT sum(products_price) as sump FROM  $this->_tablename ";
            $query  .= "where is_actived = 1 and create_date >=  '$create_day' and create_date <= '$end_day' and cated_id = $cated_id and vote_id = $vote_id ";
            $query  .=  " ORDER BY id_addon_user DESC";
//            var_dump($query);exit();
            $result = $db->fetchAll($query);
            return $result;		
	}
        public function getListAllByCatedByVote($created_day, $end_day)
	{
            $cache = $this->getCacheInstance();
		$key = "getListAllByCatedByVote.$this->_tablename.$created_day.$end_day";
		$result = $cache->getCache($key);
//                $cache->deleteCache($key);exit();
//                var_dump($result);exit();
                $result = FALSE;
		if($result === FALSE)
                {
                    $db = $this->getDbConnection();
                    $query   = "SELECT vote_id, cated_id, sum(products_price) as sum, count(products_price) as countp FROM `users_products` where is_actived = 1  and flag != 3 and create_date >=  '$created_day' and create_date <= '$end_day' and vote_online=0 group by vote_id,cated_id";
//                    var_dump($query);exit();
                    $result = $db->fetchAll($query);
                    if(!is_null($result) && is_array($result))
                    {
                            $cache->setCache($key, $result,60*5);
                    }
                }
            return $result;		
	}
        public function getListCateIdByVote($created_day="", $end_day="",$cated_id="")
	{
            $cache = $this->getCacheInstance();
		$key = "getListCateIdByVote.$this->_tablename.$created_day.$end_day";
		$result = $cache->getCache($key);
//                $cache->deleteCache($key);exit();
                $result = FALSE;
		if($result === FALSE)
                {
                    $db = $this->getDbConnection();
                    $query   = "SELECT vote_id, sum(products_price) as sum, count(products_price) as countp FROM `users_products` where is_actived = 1  and flag != 3";
                    if($cated_id != null){
                        $query .= " and cated_id =  '$cated_id'";
                    }
                    if($created_day != null){
                        $query .= " and create_date >=  '$created_day'";
                    }
                    if($end_day != null){
                        $query .= " and create_date <= '$end_day'";
                    }
                    $query .="   group by vote_id";
                    $result = $db->fetchAll($query);
                    if(!is_null($result) && is_array($result))
                    {
                            $cache->setCache($key, $result,60*5);
                    }
                }
            return $result;		
	}
        public function getListGroupByFlag($created_day="", $end_day="",$cated_id="")
	{
            $cache = $this->getCacheInstance();
		$key = "getListGroupByFlag.$this->_tablename.$created_day.$end_day";
		$result = $cache->getCache($key);
//                $cache->deleteCache($key);exit();
                $result = FALSE;
		if($result === FALSE)
                {
                    $db = $this->getDbConnection();
                    $query   = "SELECT vote_id,flag, sum(products_price) as sum, count(products_price) as countp FROM `users_products` where is_actived = 1";
                    if($cated_id != null){
                        $query .= " and cated_id =  '$cated_id'";
                    }
                    if($created_day != null){
                        $query .= " and create_date >=  '$created_day'";
                    }
                    if($end_day != null){
                        $query .= " and create_date <= '$end_day'";
                    }
                    $query .="   group by vote_id,flag";
//                    echo "<pre>";
//                    var_dump($query);
//                    exit();
                    $result = $db->fetchAll($query);
                    if(!is_null($result) && is_array($result))
                    {
                            $cache->setCache($key, $result,60*5);
                    }
                }
            return $result;		
	}
        public function getListByVote($created_day, $end_day)
	{
            $c_day = date('Y-m-d',  strtotime($created_day));
            $e_day = date('Y-m-d',  strtotime($end_day));
            $cache = $this->getCacheInstance();
		$key = "getListByStore.$this->_tablename.$c_day.$e_day";
		$result = $cache->getCache($key);
//                $cache->deleteCache($key);exit();
//                var_dump($result);exit();
//                $result = FALSE;
		if($result === FALSE)
                {
                    $db = $this->getDbConnection();
                    $query   = "SELECT vote_id, sum(products_price) as sum, count(products_price) as countp, sum(reduction_money) as reduction_money, sum(money_voucher) as money_voucher FROM `users_products` where is_actived = 1  and flag != 3 and create_date >=  '$created_day' and create_date <= '$end_day' group by vote_id";
//                    var_dump($query);exit();
                    $result = $db->fetchAll($query);
                    if(!is_null($result) && is_array($result))
                    {
                            $cache->setCache($key, $result,60*10);
                    }
                }
            return $result;		
	}
        public function getListAllByCatedByVote2($created_day, $end_day,$cated_id)
	{
            $cache = $this->getCacheInstance();
		$key = "getListAllByCatedByVote2.$this->_tablename.$created_day.$end_day";
		$result = $cache->getCache($key);
//                $cache->deleteCache($key);//exit();
                $result = FALSE;
		if($result === FALSE)
                {
                    $db = $this->getDbConnection();
                    $query   = "SELECT vote_id, products_id, sum(products_price) as sum,  count(products_price) as countp FROM `users_products` where cated_id = $cated_id and is_actived = 1  and flag != 3 and create_date >=  '$created_day' and create_date <= '$end_day' ";
                   $query ." group by vote_id,products_id ";
                    $result = $db->fetchAll($query);
                    if(!is_null($result) && is_array($result))
                    {
                            $cache->setCache($key, $result,60*5);
                    }
                }
            return $result;		
	}
        public function getListAllByProductsIdByVote($created_day, $end_day,$products_id)
	{
            $cache = $this->getCacheInstance();
		$key = "getListAllByProductsIdByVote.$this->_tablename.$created_day.$end_day";
		$result = $cache->getCache($key);
//                $cache->deleteCache($key);//exit();
                $result = FALSE;
		if($result === FALSE)
                {
                    $db = $this->getDbConnection();
                    $query   = "SELECT vote_id, products_id, sum(products_price) as sum,  count(products_price) as countp FROM `users_products` where products_id = $products_id and is_actived = 1  and flag != 3 and create_date >=  '$created_day' and create_date <= '$end_day' ";
                   $query .=" group by vote_id ";
//                   echo "<pre>";
//                   var_dump($query);
//                   exit();
                    $result = $db->fetchAll($query);
                    if(!is_null($result) && is_array($result))
                    {
                            $cache->setCache($key, $result,60*5);
                    }
                }
            return $result;		
	}
        public function sumGroupByCated2($create_day,$end_day,$cated_id)
	{
            $db = $this->getDbConnection();
            $query   = "SELECT sum(products_price) FROM  $this->_tablename ";
            $query  .= "where is_actived = 1 and create_date >=  '$create_day' and create_date <= '$end_day' and cated_id = $cated_id and flag = '1'";
            $query  .=  " ORDER BY id_addon_user DESC";
//            var_dump($query);exit();
            $result = $db->fetchAll($query);
            return $result;		
	}
        public function sumBonusByCated($create_day,$end_day,$cated_id)
	{
            $db = $this->getDbConnection();
            $query   = "SELECT sum(bonus) FROM  $this->_tablename ";
            $query  .= "where is_actived = 1 and create_date >=  '$create_day' and create_date <= '$end_day' and cated_id = $cated_id and flag = '1'";
            $query  .=  " ORDER BY id_addon_user DESC";
//            var_dump($query);exit();
            $result = $db->fetchAll($query);
            return $result;		
	}
        public function sumGroupByCated($create_day,$end_day)
	{
            $db = $this->getDbConnection();
            $query   = "SELECT sum(products_price) FROM  $this->_tablename ";
            $query  .= "where is_actived = 1 and create_date >=  '$create_day' and create_date <= '$end_day' and flag = '1'";
            $query  .=  " ORDER BY id_addon_user DESC";
//            var_dump($query);exit();
            $result = $db->fetchAll($query);
            return $result;		
	}
        
	public function getListByMemberByDay($uid,$day_create,$day_end)
	{
                $day = md5($day_create.$day_end);
                $cache = $this->getCacheInstance('ws');
		$key = "getListByMemberByDay.$this->_tablename.$uid.$day";
		$result = $cache->getCache($key);
//                $cache->deleteCache($key);exit();
//                var_dump($result);exit();
		if($result === FALSE)
		{
			$db = $this->getDbConnection();
                        $day_create = $this->getDayCreate($day_create);
                        $day_end = $this->getDayEnd($day_end);
			$query   = "SELECT upd.*,aud.fullname,aud.phone,upd.create_date FROM  $this->_tablename upd, addon_users aud ";
                        $query  .= "where aud.id = upd.id_addon_user and id_users =$uid  and upd.create_date >= '$day_create' and upd.create_date <= '$day_end'";
                        $query  .=  " ORDER BY upd.id_addon_user DESC";
//                        var_dump($query);exit();
			$result = $db->fetchAll($query);
                        if(!is_null($result) && is_array($result))
			{
				$cache->setCache($key, $result);
			}
		}
		return $result;		
	}
        public function getAllByCreate_Date($userid,$created_date, $end_date,$member_id="",$keywork,$status="-1"){
                $day = md5($created_date.$end_date);
                $cache = $this->getCacheInstance('app');
//                $key2 = "getallbydate_keys";
		$key = "getAllByCreate_Date.$this->_tablename.$userid.$day.$keywork";
//                $array[] = $key;
//                $cache->setCache($key2, $array);
//                var_dump($key);exit();
		$result = $cache->getCache($key);
//                $cache->deleteCache($key);
//                var_dump($result);exit();
                $result = FALSE;
		if($result === FALSE)
		{
//                    exit('dsad');
                    $db = $this->getDbConnection();
                    if($created_date == ' 00:00:00'){
                        $created_date = date('Y-m-d').' 00:00:00';
                    }
                    if($end_date == ' 23:59:59'){
                        $end_date = date('Y-m-d').' 23:59:59';
                    }
                    $query =  "SELECT * FROM `users_products` up, zfw_users zu WHERE  up.is_actived = 1 ";
                    if($userid != "" || $userid != null){
                        $query.="  AND vote_id = '$userid' ";
                    }
                    if($member_id != 0 || $member_id != null){
                        $query.=" and up.id_users = $member_id ";
                    }
                    if($keywork !=null){
                        if($userid ==167){
                            $query.=" and (up.id_addon_user = '$keywork' or up.imes = '$keywork' or fullname_addon like '%$keywork%' or phone_addon like '%$keywork%' or up.order_code like '%$keywork%') ";
                        }else{
                            $query.=" and (up.id_addon_user = '$keywork' or up.imes = '$keywork' or fullname_addon like '%$keywork%' or phone_addon like '%$keywork%') ";
                        }
                        
                    }
                    if($userid ==167 && $status !="-1"){
                        $query .= " and status_ghn = $status";
                    }
                    $query .= " and up.create_date > '$created_date' and up.create_date < '$end_date' group by up.id_addon_user";
                    
                    $query .= " ORDER BY up.create_date DESC";
//                    echo "<pre>";
//                    var_dump($query);
//                    exit();
                    $result = $db->fetchAll($query);
                    
                    if(!is_null($result) && is_array($result))
                    {
                        $cache->setCache($key, $result);
                    }
                }
            return $result;
        }
        public function getListByDays($userid,$created_date, $end_date,$member_id="",$keywork,$cated_products=""){
                $day = md5($created_date.$end_date);
                $cache = $this->getCacheInstance('app');
		$key = "getListByDays.$this->_tablename.$userid.$day.$keywork.$member_id.$cated_products";
		$result = $cache->getCache($key);
                $result = FALSE;
		if($result === FALSE)
		{
                    $db = $this->getDbConnection();
                    if($created_date == ' 00:00:00'){
                        $created_date = date('Y-m-d').' 00:00:00';
                    }
                    if($end_date == ' 23:59:59'){
                        $end_date = date('Y-m-d').' 23:59:59';
                    }
                    $query =  "SELECT * FROM $this->_tablename WHERE is_actived = 1 ";
                    if($userid != "" || $userid != null){
                        $query.="  AND vote_id = '$userid' ";
                    }
                    if($member_id != 0 || $member_id != null){
                        $query.=" and id_users = $member_id ";
                    }
                    if($keywork !=null){
                        $query.=" and (id_addon_user = '$keywork' or imes = '$keywork' or fullname_addon like '%$keywork%' or phone_addon like '%$keywork%') ";
                    }
                    if($cated_products !=""){
                        $query .= " and flag = $cated_products ";
                    }
                    $query .= " and create_date > '$created_date' and create_date < '$end_date' ";
                    $query .= " ORDER BY create_date DESC";
//                    echo "<pre>";
//                    var_dump($query);
//                    exit();
                    $result = $db->fetchAll($query);
                    if(!is_null($result) && is_array($result))
                    {
                        $cache->setCache($key, $result);
                    }
                }
            return $result;
        }
        public function getListByDay($userid,$created_date, $end_date,$member_id="",$keywork,$cated_products=""){
                $day = md5($created_date.$end_date);
                $cache = $this->getCacheInstance('app');
		$key = "getListByDay.$this->_tablename.$userid.$day.$keywork.$member_id.$cated_products";
		$result = $cache->getCache($key);
                $result = FALSE;
		if($result === FALSE)
		{
                    $db = $this->getDbConnection();
                    if($created_date == ' 00:00:00'){
                        $created_date = date('Y-m-d').' 00:00:00';
                    }
                    if($end_date == ' 23:59:59'){
                        $end_date = date('Y-m-d').' 23:59:59';
                    }
                    $query =  "SELECT * FROM $this->_tablename WHERE is_actived = 1 ";
                    if($userid != "" || $userid != null){
                        $query.="  AND vote_id = '$userid' ";
                    }
                    if($member_id != 0 || $member_id != null){
                        $query.=" and id_users = $member_id ";
                    }
                    if($keywork !=null){
                        $query.=" and (id_addon_user = '$keywork' or imes = '$keywork' or fullname_addon like '%$keywork%' or phone_addon like '%$keywork%') ";
                    }
                    if($cated_products !=""){
                        $query .= " and flag = $cated_products ";
                    }
                    $query .= " and create_date > '$created_date' and create_date < '$end_date' group by id_addon_user";
                    $query .= " ORDER BY create_date DESC";
                    $result = $db->fetchAll($query);
                    if(!is_null($result) && is_array($result))
                    {
                        $cache->setCache($key, $result);
                    }
                }
            return $result;
        }
        
        public function getListOnlineVote($created_date, $end_date,$keywork,$vote_id){
            $cache = $this->getCacheInstance('ws');
            $key = "getListOnlineVote.$this->_tablename.$created_date.$end_date.$keywork.$vote_id";
            $result = $cache->getCache($key);
//            $result= FALSE;
            if($result=== FALSE){
                $db = $this->getDbConnection();
                if($created_date == ' 00:00:00'){
                    $created_date = date('Y-m-d').' 00:00:00';
                }
                if($end_date == ' 23:59:59'){
                    $end_date = date('Y-m-d').' 23:59:59';
                }
    //            $query =  "SELECT * FROM `users_products` up WHERE  isonline = 1 and vote_id !=169 ";//saleonline localhost
                $query =  "SELECT * FROM `users_products` up WHERE  isonline = 1 and vote_id !=167 ";//saleonline line
                if($member_id != 0 || $member_id != null){
                    $query.=" and up.id_users = $member_id ";
                }
                if($keywork !=null){
                    $query.=" and (up.id_addon_user = '$keywork' or up.imes = '$keywork' or fullname_addon like '%$keywork%' or phone_addon like '%$keywork%') ";
                }
                if($vote_id !=0){
                    $query.=" and vote_id = $vote_id ";
                }
                $query .= " and up.create_date > '$created_date' and up.create_date < '$end_date' group by up.id_addon_user";
                $query .= " ORDER BY up.create_date DESC";
                $query .= " LIMIT 100";
//                        var_dump($query);exit();
                $result = $db->fetchAll($query);
                if(!is_null($result) && is_array($result))
                    {
                        $cache->setCache($key, $result, 60 * 5);
                    }
            }
            
            return $result;
        }
        public function countDonHangOnline($pack){
            $db = $this->getDbConnection();
            $query =  "SELECT * FROM `users_products` up WHERE  isonline = 2";
            if($pack == 1){
                $query .=" and pack != 2 ";
            }
            if($pack == 2){
                $query .= " and pack = 2 ";
            }
            $query .=" GROUP BY id_addon_user";
//            var_dump($query);exit();
            $result = $db->fetchAll($query);
            return $result;
        }

        public function getAllByCreate_DateIsOnline($created_date, $end_date,$keywork){
            $db = $this->getDbConnection();
            if($created_date == ' 00:00:00'){
                $created_date = date('Y-m-d').' 00:00:00';
            }
            if($end_date == ' 23:59:59'){
                $end_date = date('Y-m-d').' 23:59:59';
            }
            $query =  "SELECT * FROM `users_products` up WHERE  isonline = 2";
            if($member_id != 0 || $member_id != null){
                $query.=" and up.id_users = $member_id ";
            }
            if($keywork !=null){
                $query.=" and (up.id_addon_user = '$keywork' or up.imes = '$keywork' or fullname_addon like '%$keywork%' or phone_addon like '%$keywork%') ";
            }
            $query .= " and up.create_date > '$created_date' and up.create_date < '$end_date' group by up.id_addon_user";

            $query .= " ORDER BY up.create_date DESC";
//                    var_dump($query);exit();
            $result = $db->fetchAll($query);
            return $result;
        }
        public function getListIsOnline($created_date, $end_date,$keywork,$vote_id,$isonline,$pack){
            $db = $this->getDbConnection();
            if($created_date == ' 00:00:00'){
                $created_date = date('Y-m-d').' 00:00:00';
            }
            if($end_date == ' 23:59:59'){
                $end_date = date('Y-m-d').' 23:59:59';
            }
            $query =  "SELECT * FROM `users_products` up WHERE 1=1 ";
            if($isonline != null){
                $query.=" and up.isonline = $isonline ";
            }
            if($keywork == null && $vote_id != 167){
                $query.=" and up.vote_online = $vote_id ";
            }
            if($keywork !=null || $keywork != ""){
                $query.=" and (up.id_addon_user = '$keywork' or up.imes = '$keywork' or fullname_addon like '%$keywork%' or phone_addon like '%$keywork%') ";
            }
            if($pack == 1){
                    $query .=" and pack != 2 ";
                }
                if($pack == 2){
                    $query .= " and pack = 2 ";
                }
            $query .= " and up.create_date > '$created_date' and up.create_date < '$end_date' group by up.id_addon_user";

            $query .= " ORDER BY up.create_date DESC";
//                    var_dump($query);exit();
            $result = $db->fetchAll($query);
            return $result;
        }

        public function getAllByCreate_Date2($userid,$created_date, $end_date,$mid_day){
            $cache = $this->getCacheInstance();
		$key = "getAllByCreate_Date2.$this->_tablename.$userid.$created_date.$end_date.$mid_day";
		$result = $cache->getCache($key);
		$result =FALSE;
		if($result === FALSE)
		{
                    $db = $this->getDbConnection();
                    $query =  "SELECT * FROM $this->_tablename WHERE vote_id = '$userid' and is_actived = 1 ";
                    if($mid_day !=null){
                        $query .= " and create_date > '$mid_day' and create_date < '$end_date'";
                    }else{
                        $query .= " and create_date > '$created_date' and create_date < '$end_date' ";
                    }
                    $query .= " ORDER BY create_date DESC";
//                    var_dump($query);exit();
                    $result = $db->fetchAll($query);
                    if(!is_null($result) && is_array($result))
			{
                        $cache->setCache($key, $result,60*5);
                    }
                    
                }
            return $result;
        }
        public function getAllByDay($userid,$created_date, $end_date,$task =0){
            $days = date('Y-m-d',  strtotime($created_date));
            $tmp    = $userid."_".$days."_".$task;
            $cache = $this->getCacheInstance();
            
            $key = $this->getKeyList2($tmp);
            echo "--->>".$key."<br />";
//            $cache->deleteCache($key);exit();
            $result = $cache->getCache($key);
//            $result = false;
		if($result === FALSE)
		{
                    $db = $this->getDbConnection();
                    $query  = "SELECT * FROM `users_products` up, zfw_users zu WHERE up.id_users = zu.userid AND zu.parentid = '$userid' and up.is_actived = 1 ";
                    $query .= " and up.create_date > '$created_date' and up.create_date < '$end_date' ";
                    $query .= " ORDER BY up.create_date DESC";
//                    var_dump($query);exit();
                    $result = $db->fetchAll($query);
                    $cache->setCache($key, $result);
                }
            return $result;
        }
        public function countChangeCard($userid,$created_date, $end_date,$prepaid){
            $cache = $this->getCacheInstance();
            $key = "countChangeCard.$this->_tablename.$userid.$created_date.$end_date.$prepaid";
            $result = $cache->getCache($key);
            $result =FALSE;
            if($result === FALSE)
            {
                $db = $this->getDbConnection();
                $query =  "SELECT count(*) FROM `users_products`  WHERE vote_id = '$userid' and is_actived = 1 and cated_card = $prepaid ";
                $query .= " and create_date > '$created_date' and create_date < '$end_date' ";
                $query .= " ORDER BY create_date DESC";
                $result = $db->fetchAll($query);
                $cache->setCache($key, $result);
            }
            return $result;
        }
        public function sumChangeCard($userid,$created_date, $end_date,$cated_card){
            $cache = $this->getCacheInstance();
            $key = "sumChangeCard.$this->_tablename.$userid.$created_date.$end_date.$cated_card";
            $result = $cache->getCache($key);
//            $result =FALSE;
            if($result === FALSE)
            {
                $db = $this->getDbConnection();
                $query =  "SELECT sum(products_price) FROM `users_products`  WHERE vote_id = '$userid' and is_actived = 1 and cated_card = $cated_card ";
                $query .= " and create_date > '$created_date' and create_date < '$end_date' ";
                $query .= " ORDER BY create_date DESC";
                $result = $db->fetchAll($query);
                $cache->setCache($key, $result);
            }
            return $result;
        }
        public function getListChangeCard($userid,$created_date, $end_date,$cated_card){
            $cache = $this->getCacheInstance();
            $key = "getListChangeCard.$this->_tablename.$userid.$created_date.$end_date.$cated_card";
            $result = $cache->getCache($key);
//            $result =FALSE;
            if($result === FALSE)
            {
                $db = $this->getDbConnection();
                $query =  "SELECT * FROM `users_products`  WHERE vote_id = '$userid' and is_actived = 1 ";
                if($cated_card !=""){
                    $query .= " and cated_card = $cated_card ";
                }
                $query .= " and create_date > '$created_date' and create_date < '$end_date' ";
                $query .= " ORDER BY create_date DESC";
                $result = $db->fetchAll($query);
                $cache->setCache($key, $result);
            }
            return $result;
        }
       
        
        public function getAllByBill($userid,$created_date, $end_date,$mid_day){
            $cache = $this->getCacheInstance();
		$key = "getAllByBill.$this->_tablename.$userid.$created_date.$end_date.$mid_day";
		$result = $cache->getCache($key);
		$result =FALSE;
		if($result === FALSE)
		{
                    $db = $this->getDbConnection();
                    $query =  "SELECT * FROM `users_products` up, zfw_users zu WHERE up.id_users = zu.userid AND zu.parentid = '$userid' and out_bill = 1";
                    if($mid_day !=null){
                        $query .= " and up.create_date > '$mid_day' and up.create_date < '$end_date' and up.is_actived = 1";
                    }else{
                        $query .= " and up.create_date > '$created_date' and up.create_date < '$end_date' and up.is_actived = 1";
                    }
                    
                    
                    $query .= " ORDER BY up.create_date DESC";
//                    var_dump($query);exit();
                    $result = $db->fetchAll($query);
                    $cache->setCache($key, $result);
                }
            return $result;
        }
        public function getAllByChangeCard($userid,$created_date, $end_date,$mid_day){
            $cache = $this->getCacheInstance();
		$key = "getAllByBill.$this->_tablename.$userid.$created_date.$end_date.$mid_day";
		$result = $cache->getCache($key);
		$result =FALSE;
		if($result === FALSE)
		{
                    $db = $this->getDbConnection();
                    $query =  "SELECT * FROM `users_products` up, zfw_users zu WHERE up.id_users = zu.userid AND zu.parentid = '$userid' and up.prepaid != 0";
                    if($mid_day !=null){
                        $query .= " and up.create_date > '$mid_day' and up.create_date < '$end_date' and up.is_actived = 1";
                    }else{
                        $query .= " and up.create_date > '$created_date' and up.create_date < '$end_date' and up.is_actived = 1";
                    }
                    
                    
                    $query .= " ORDER BY up.create_date DESC";
//                    var_dump($query);exit();
                    $result = $db->fetchAll($query);
                    $cache->setCache($key, $result);
                }
            return $result;
        }
        public function getAllByCty($userid,$created_date, $end_date,$mid_day){
            $cache = $this->getCacheInstance();
		$key = "getAllByBill.$this->_tablename.$userid.$created_date.$end_date.$mid_day";
		$result = $cache->getCache($key);
		$result =FALSE;
		if($result === FALSE)
		{
                    $db = $this->getDbConnection();
                    $query =  "SELECT * FROM `users_products` up, zfw_users zu WHERE up.id_users = zu.userid AND zu.parentid = '$userid' and up.flag = 1";
                    if($mid_day !=null){
                        $query .= " and up.create_date > '$mid_day' and up.create_date < '$end_date' and up.is_actived = 1";
                    }else{
                        $query .= " and up.create_date > '$created_date' and up.create_date < '$end_date' and up.is_actived = 1";
                    }
                    
                    
                    $query .= " ORDER BY up.create_date DESC";
//                    var_dump($query);exit();
                    $result = $db->fetchAll($query);
                    $cache->setCache($key, $result);
                }
            return $result;
        }
        public function getAllByXtay($userid,$created_date, $end_date,$mid_day){
            $cache = $this->getCacheInstance();
		$key = "getAllByBill.$this->_tablename.$userid.$created_date.$end_date.$mid_day";
		$result = $cache->getCache($key);
		$result =FALSE;
		if($result === FALSE)
		{
                    $db = $this->getDbConnection();
                    $query =  "SELECT * FROM `users_products` up, zfw_users zu WHERE up.id_users = zu.userid AND zu.parentid = '$userid' and up.flag = 2";
                    if($mid_day !=null){
                        $query .= " and up.create_date > '$mid_day' and up.create_date < '$end_date' and up.is_actived = 1";
                    }else{
                        $query .= " and up.create_date > '$created_date' and up.create_date < '$end_date' and up.is_actived = 1";
                    }
                    
                    
                    $query .= " ORDER BY up.create_date DESC";
//                    var_dump($query);exit();
                    $result = $db->fetchAll($query);
                    $cache->setCache($key, $result);
                }
            return $result;
        }
        public function getAllByIdAddonUser($id_addon_user,$created_date="", $end_date=""){
            $c_day = date('Y-m-d',  strtotime($created_date));
            $e_day = date('Y-m-d',  strtotime($end_date));
                $cache = $this->getCacheInstance('app');
		$key = "getAllByIdAddonUser.$this->_tablename.$id_addon_user.$c_day.$e_day";
		$result = $cache->getCache($key);
		if($result === FALSE)
		{
                    $db = $this->getDbConnection();
                    if($created_date == ' 00:00:00'){
                        $created_date = date('Y-m-d').' 00:00:00';
                    }
                    if($end_date == ' 23:59:59'){
                        $end_date = date('Y-m-d').' 23:59:59';
                    }
                    $query      = "select * from users_products where id_addon_user = $id_addon_user ";
                    if($created_date != null){
                        $query .= " and create_date > '$created_date' ";
                    }
                    if($end_date != null){
                        $query     .= " and create_date < '$end_date' ";
                    }
                    
                    $query .= " ORDER BY create_date DESC";
//                    var_dump($query);exit();
                    $result = $db->fetchAll($query);
                    if(!is_null($result) && is_array($result))
			{
				$cache->setCache($key, $result);
			}
                }
                return $result;
        }
        public function countApple($vote_id,$months){
            $db = $this->getDbConnection();
            $query = "select count(*) as countApple from $this->_tablename  where  vote_id = $vote_id and is_actived =1 and cated_id IN (585,264,455,688,466,586,587,622) and months = $months";
//            var_dump($query);exit();
            $result = $db->fetchAll($query);
            return $result;
        }
        public function countAppleByDay($vote_id,$created_day,$end_day){
            $db = $this->getDbConnection();
            $query = "select count(*) as countApple from $this->_tablename  where  vote_id = $vote_id and is_actived =1 and cated_id IN (585,264,455,688,466,586,587,622) and create_date > '$created_day' and create_date < '$end_day'";
//            var_dump($query);exit();
            $result = $db->fetchAll($query);
            return $result;
        }
        public function deleteSalesById($id,$description,$userid){
//            exit('dsada');
            $db=  $this->getDbConnection();
//            var_dump($db);exit();
            $query = "id_addon_user = $id";
//            var_dump($query);exit();
            $data =array(
                "is_actived" => "0",
                "description"=> $description,
                "userid_delete_bill"=>$userid
                );
            $result = $db->update($this->_tablename, $data, $query);
            $this->_deleteAllCache();
//            var_dump($result);exit();
            return $result;
        }
        public function deleteSalesByIdSaleonline($id,$description,$userid){
//            exit('dsada');
            $db=  $this->getDbConnection();
            $query = "id_addon_user = $id";
            $data =array(
                "isonline" => "1",
                "description"=> $description,
                "userid_delete_bill"=>$userid
                );
            $result = $db->update($this->_tablename, $data, $query);
            $this->_deleteAllCache();
//            var_dump($result);exit();
            return $result;
        }
	public function restoreSalesById($id){
            $db=  $this->getDbConnection();
            $query = "id_addon_user = ".$id;
            $data =array(
                "is_actived" => "1"
                );
            $result = $db->update($this->_tablename, $data, $query);
            return $result;
        }
        public function sumPriceSalesByIdCTY($id_users,$months){
            $db = $this->getDbConnection();
            $query = "select sum(products_price) from $this->_tablename where  id_users = $id_users and is_actived = 1 and flag = '1' and months = $months";
//            var_dump($query);exit();
            $result = $db->fetchAll($query);
            return $result;
        }
        public function sumPriceSalesByIdXTAY($id_users,$months){
            $db = $this->getDbConnection();
            $query = "select sum(products_price) from $this->_tablename  where id_users = $id_users and is_actived =1 and flag = '2' and months = $months";
//            var_dump($query);exit();
            $result = $db->fetchAll($query);
            return $result;
        }
        public function sumPriceSalesByIdLKIEN($id_users,$months){
            $db = $this->getDbConnection();
            $query = "select sum(products_price) from $this->_tablename  where  id_users = $id_users and is_actived =1 and flag = '3' and months = $months";
//            var_dump($query);exit();
            $result = $db->fetchAll($query);
            return $result;
        }
        public function sumBonusSalesById($id_users,$months){
            $db = $this->getDbConnection();
            $query = "select sum(bonus) from $this->_tablename  where id_users = $id_users and is_actived =1 and months = $months";
//            var_dump($query);exit();
            $result = $db->fetchAll($query);
            return $result;
        }
        public function countProductsSalesByIdCTY($id_users,$months){
            $db = $this->getDbConnection();
            $query = "select count(*) from $this->_tablename  where  id_users = $id_users and is_actived =1 and flag = '1' and months = $months";
//            var_dump($query);exit();
            $result = $db->fetchAll($query);
            return $result;
        }
        
        public function countProductsSalesByIdXTAY($id_users,$months){
            $db = $this->getDbConnection();
            $query = "select count(*) from $this->_tablename  where  id_users = $id_users and is_actived =1 and flag = '2' and months = $months";
//            var_dump($query);exit();
            $result = $db->fetchAll($query);
            return $result;
        }
        public function countProductsSalesByIdLKIEN($id_users,$months){
            $db = $this->getDbConnection();
            $query = "select count(*) from $this->_tablename  where  id_users = $id_users and is_actived =1 and flag = '3' and months = $months";
//            var_dump($query);exit();
            $result = $db->fetchAll($query);
            return $result;
        }
        public function getDayByMonth($thang){
        switch ($thang){
            case 1:
            case 3:
            case 5:
            case 7:
            case 8:
            case 10:
            case 12:
            {
                $thang = '31';
                break;
             }
            case 4:
            case 6:
            case 9:
            case 11:
            {
                $thang = '30';
                break;
            }
            case 2: 
            {
                if(date('Y') % 400 == 0 || (date('Y') % 4 == 0 && date('Y') % 4 !=0))
                    $thang = '29';
                else {
                    $thang ='28';
                }
                    break;
            }
         default :
             break;
        }
        return $thang;
    }
        
    public function getDayCreate($created_date){
        $months = date('m');
            $count_days = $this->getDayByMonth($months);
            if($created_date == ' 00:00:00'){
                    $created_date = date('Y/m/01').' 00:00:00';
                }
        return $created_date;
    }
    public function getDayEnd($end_date){
        $months = date('m');
            $count_days = $this->getDayByMonth($months);
            if($end_date == ' 23:59:59'){
                $end_date = date('Y/m/'.$count_days).' 23:59:59';
            }
        return $end_date;
    }
    public function getDayCreateView($created_date){
        $months = date('m');
            $count_days = $this->getDayByMonth($months);
            if($created_date == ''){
                    $created_date = date('Y/m/01');
                }
        return $created_date;
    }
    public function getDayEndView($end_date){
        $months = date('m');
            $count_days = $this->getDayByMonth($months);
            if($end_date == ''){
                $end_date = date('Y/m/'.$count_days);
            }
        return $end_date;
    }
    public function getDayCreated($months,$years){
        if($months < 10){
            $months = '0'.$months;
        }
        return date($years.'/'.$months.'/01');
    }
    public function getDayEndd($months,$years){
        if($months < 10){
            $months = '0'.$months;
        }
        $count_days = $this->getDayByMonth($months);
        return date($years.'/'.$months.'/'.$count_days);
    }
    public function getDayCreated2($months,$years){
        return date($years.'/'.$months.'/01');
    }
    public function getDayEndd2($months,$years){
        $count_days = $this->getDayByMonth($months);
        return date($years.'/'.$months.'/'.$count_days);
    }
    public function sumPriceSalesDay($id_users,$created_date,$end_date){
            $db = $this->getDbConnection();
            $query = "select sum(products_price) from $this->_tablename  where  id_users = $id_users and is_actived =1 and flag = '1' and create_date >= '$created_date' and create_date <= '$end_date'";
//            var_dump($query);exit();
            $result = $db->fetchAll($query);
            return $result;
        }

        public function sumPriceSalesDayByIdCTY($id_users,$created_date,$end_date){
            $db = $this->getDbConnection();
            $created_date = $this->getDayCreate($created_date);
            $end_date  = $this->getDayEnd($end_date);
            
            $query = "select sum(products_price) from $this->_tablename  where  id_users = $id_users and is_actived =1 and flag = '1' and create_date >= '$created_date' and create_date <= '$end_date'";
//            var_dump($query);exit();
            $result = $db->fetchAll($query);
            return $result;
        }
        public function sumPriceSalesDayByIdXTAY($id_users,$created_date,$end_date){
            $db = $this->getDbConnection();
            $created_date = $this->getDayCreate($created_date);
            $end_date  = $this->getDayEnd($end_date);
            $query = "select sum(products_price) from $this->_tablename where  id_users = $id_users and is_actived =1 and flag = '2' and create_date >= '$created_date' and create_date <= '$end_date'";
//            var_dump($query);exit();
            $result = $db->fetchAll($query);
            return $result;
        }
        public function sumPriceSalesDayByIdLKIEN($id_users,$created_date,$end_date){
            $db = $this->getDbConnection();
            $created_date = $this->getDayCreate($created_date);
            $end_date  = $this->getDayEnd($end_date);
            $query = "select sum(products_price) from $this->_tablename  where id_users = $id_users and is_actived =1 and flag = '3' and create_date >= '$created_date' and create_date <= '$end_date'";
//            var_dump($query);exit();
            $result = $db->fetchAll($query);
            return $result;
        }
        public function sumBonusSalesDayById($id_users,$created_date,$end_date){
            $db = $this->getDbConnection();
            $created_date = $this->getDayCreate($created_date);
            $end_date  = $this->getDayEnd($end_date);
            $query = "select sum(bonus) from $this->_tablename  where id_users = $id_users and is_actived =1 and create_date >= '$created_date' and create_date <= '$end_date'";
//            var_dump($query);exit();
            $result = $db->fetchAll($query);
            return $result;
        }
        public function countProductsSalesDayByIdCTY($id_users,$created_date,$end_date){
            $db = $this->getDbConnection();
            $created_date = $this->getDayCreate($created_date);
            $end_date  = $this->getDayEnd($end_date);
            $query = "select count(*) from $this->_tablename  where  id_users = $id_users and is_actived = 1 and flag = '1' and create_date >= '$created_date' and create_date <= '$end_date'";
//            var_dump($query);exit();
            $result = $db->fetchAll($query);
            return $result;
        }
        public function countProductsSalesDayByIdXTAY($id_users,$created_date,$end_date){
            $db = $this->getDbConnection();
            $created_date = $this->getDayCreate($created_date);
            $end_date  = $this->getDayEnd($end_date);
            $query = "select count(*) from $this->_tablename  where id_users = $id_users and is_actived =1 and flag = '2' and create_date >= '$created_date' and create_date <= '$end_date'";
//            var_dump($query);exit();
            $result = $db->fetchAll($query);
            return $result;
        }
        public function countProductsSalesDayByIdLKIEN($id_users,$created_date,$end_date){
            $db = $this->getDbConnection();
            $created_date = $this->getDayCreate($created_date);
            $end_date  = $this->getDayEnd($end_date);
            $query = "select count(*) from $this->_tablename  where  id_users = $id_users and is_actived =1 and flag = '3' and create_date >= '$created_date' and create_date <= '$end_date'";
//            var_dump($query);exit();
            $result = $db->fetchAll($query);
            return $result;
        }
//	
        function getSaleByMonth($date_from="",$date_to="", $actived="") {
            $day_from =  date("Y-m-d", strtotime($date_from));
            $day_to =  date("Y-m-d", strtotime($date_to));
//            $month = (int) date("m", strtotime($date));
            $cache = $this->getCacheInstance();
            $key = "getSaleByMonth.$this->_tablename.$day_from.$day_to.$actived";
            $result = $cache->getCache($key);
            if($result === FALSE){
                $db = $this->getDbConnection();
                $query = "SELECT flag,cated_id,  date(create_date) as date, sum(products_price) as total,count(products_name) as sl FROM `users_products` WHERE create_date>='$date_from' AND create_date <='$date_to' and vote_online =0";
                if($actived != null){
                    $query .="  AND is_actived='$actived' ";
                }
                $query .=" GROUP BY  date(create_date),cated_id,flag";
//                var_dump($query);
                $data = array();
                $result = $db->fetchAll($query, $data);
                if(!is_null($result) && is_array($result))
                {
                        $cache->setCache($key, $result,60*10);
                }
            }
            return $result;
        }
        function getListDetailSale($date_from="",$date_to="", $actived="") {
            $day_from =  date("Y-m-d", strtotime($date_from));
            $day_to =  date("Y-m-d", strtotime($date_to));
            $cache = $this->getCacheInstance();
            $key = "getListDetailSale.$this->_tablename.$day_from.$day_to.$actived";
            $result = $cache->getCache($key);
//            $result=FALSE;
            if($result === FALSE){
                $db = $this->getDbConnection();
                $query = "SELECT flag,cated_id, vote_id, sum(products_price) as total,count(products_name) as sl FROM `users_products` WHERE create_date>='$date_from' AND create_date <='$date_to' and vote_online =0";
                if($actived != null){
                    $query .="  AND is_actived='$actived' ";
                }
                $query .=" GROUP BY  vote_id,cated_id,flag";
//                var_dump($query);
                $data = array();
                $result = $db->fetchAll($query, $data);
                if(!is_null($result) && is_array($result))
                {
                        $cache->setCache($key, $result,60*10);
                }
            }
            return $result;
        }
        
        //dsb
        function getSaleByHour($date, $voteids=null, $actived=1) {
            $day = (int) date("d", strtotime($date));
            $month = (int) date("m", strtotime($date));
            $year = (int) date("Y", strtotime($date));

            $db = $this->getDbConnection();
            if ($voteids == null) {
                $query = "SELECT vote_id, hour(create_date) as hour, date(create_date) as date, count(*) as total FROM `users_products` WHERE month(create_date)='$month' AND day(create_date)='$day' AND year(create_date)='$year' AND is_actived='$actived' GROUP BY vote_id, hour(create_date), date(create_date)";
            } else {
                $query = "SELECT vote_id, hour(create_date) as hour, date(create_date) as date, count(*) as total FROM `users_products` WHERE month(create_date)='$month' AND day(create_date)='$day' AND year(create_date)='$year' AND is_actived='$actived' AND vote_id IN($voteids) GROUP BY vote_id, hour(create_date), date(create_date)";    
            }
            
            $data = array();
            $result = $db->fetchAll($query, $data);
            return $result;
        }
		
}
?>
<?php

class Business_Addon_UsersProductsbk extends Business_Abstract
{
	private $_tablename = 'users_products';
	
	const KEY_LIST              = 'users_products.list.%s';
	const KEY_LIST2             = 'users_products.list2.%s';
	const KEY_DETAIL = 'users_products.uid.%s';
    
	protected static $_current_rights = null;
	
	protected static $_instance = null; 

	function __construct()
	{
		
	}
	
	/**
	 * get instance of Business_Addon_UsersProducts
	 *
	 * @return Business_Addon_UsersProducts
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
        public function excute($query){
            $db     =  $this->getDbConnection();
            $result = $db->query($query);
            return $result;
        }
				
		/*NghiDV*/
		public function getBillIDForForceSyncLastHour() {
			$oneHour = 60*60; //seconds
			$timeForSync = time() - $oneHour;
			$datetimeForSync = date("Y-m-d H:i:s", $timeForSync);	
			$db = $this->getDbConnection();
			$query = "select id_addon_user,create_date from $this->_tablename where is_actived=1 AND vote_id != 603 AND status2=0 AND is_actived=1 AND isync = 1 AND create_date <= '$datetimeForSync' ORDER BY id_addon_user ASC LIMIT 0,1";			
			$result = $db->fetchAll($query);
			var_dump($datetimeForSync."\t".$result[0]["create_date"]."\t".$result[0]["id_addon_user"]);
			return $result[0]["id_addon_user"];
		
		}
		
        public function get_detail_by_id_addon_user_by_imei_by_products_id($id_addon_user,$imei,$product_ids){
        $db = $this->getDbConnection();
        $query = "select * from $this->_tablename where id_addon_user = $id_addon_user and imes = '$imei' and products_id = $product_ids";
        $result = $db->fetchAll($query);
        return $result[0];
    }
        public function get_list_installment($cated_prepaid_installment=0){
        $db = $this->getDbConnection();
        $query = "select * from $this->_tablename where cated_prepaid_installment >0";
        if((int)$cated_prepaid_installment>0){
            $query.=" and cated_prepaid_installment = $cated_prepaid_installment ";
        }
        $result = $db->fetchAll($query); 
        return $result;
    }
    public function get_thong_ke($storeid,$productsid=0,$from, $to){
                $cache = $this->getCacheInstance('app');
		$key = "get_thong_ke.$this->_tablename.$productsid.$from.$to";
		$result = $cache->getCache($key);
                $result ==FALSE;
		if($result === FALSE)
		{
                    $db = $this->getDbConnection();
                    $query =  "SELECT vote_id,id_addon_user,count(*) as total,(sum(products_price)-sum(reduction_money)-sum(money_voucher)-sum(bonus_tech)) as sum,id_users  FROM `users_products` WHERE is_actived=1 and status2=0 and vote_id>0 ";
                    if((int)$storeid > 0){
                        $query.="  AND vote_id = $storeid ";
                    }
                    if((int)$productsid > 0){
                        $query.="  AND productsid = '$productsid' ";
                    }
                    $query .= " and create_date > '$from' and create_date <= '$to' ";
                    $query .= " group by id_addon_user order by vote_id";
                    $result = $db->fetchAll($query);
                    if(!is_null($result) && is_array($result))
                    {
                        $cache->setCache($key, $result,60);
                    }
                }
            return $result;
        }
        public function get_list_by_products_storeid_date($products_id=0,$storeid=0,$from="",$to=""){
            $db = $this->getDbConnection();
            $query = "select colorid,products_price_cost,products_name,imes,create_date,vote_id,status2,id_addon_user,description,is_actived,products_id,products_price,reduction_money,money_voucher,bonus_tech,(products_price-reduction_money-money_voucher-bonus_tech) as sum from $this->_tablename where is_actived =1 and status2=0";
            if((int)$products_id >0){
                $query .=" and products_id = $products_id";
            }
            if((int)$storeid >0){
                $query .=" and vote_id = $storeid";
            }
            if($from != NULL){
                $query .=" and create_date >= '$from'";
            }
            if($to != NULL){
                $query .=" and create_date <= '$to'";
            }
            $result = $db->fetchAll($query);
            return $result;
        }
        public function search($keywork=""){
            if($keywork != NULL){
                $db = $this->getDbConnection();
                $query = "select cated_id,products_id,productsid,imes,fullname_addon,phone_addon,warranty,products_name,create_date,vote_id,status2,id_addon_user,(products_price-reduction_money-money_voucher-bonus_tech) as sum from $this->_tablename where is_actived =1";
                $query.=" and (id_addon_user = '$keywork' or imes = '$keywork' or  phone_addon = '$keywork') order by autoid desc LIMIT  2";
                $result = $db->fetchAll($query);
            }
            return $result;
        }
        public function search2($keywork=""){
            if($keywork != NULL){
                $db = $this->getDbConnection();
                $query = "select products_id,productsid,imes,fullname_addon,phone_addon,warranty,products_name,create_date,vote_id,status2,id_addon_user,(products_price-reduction_money-money_voucher-bonus_tech) as sum from $this->_tablename where is_actived =1";
                $query.=" and imes = '$keywork' order by autoid desc LIMIT  2";
                $result = $db->fetchAll($query);
            }
            return $result;
        }
        
        public function get_top1($_date30=""){
            $db = $this->getDbConnection();
            $query = "select * from $this->_tablename where isync=1 and is_actived = 1 and status2=0 and block=1   ";
            if($_date30 != NULL){
                $query .=" and create_date >= '$_date30'";
            }
            $query .=" order by id asc limit 0,1";
            $result = $db->fetchAll($query);
            return $result[0];
        }
        public function get_top2($_date30="",$____storeid=0){
            $db = $this->getDbConnection();
            $query = "select * from $this->_tablename where isync=1 and block=1 and is_actived = 1 and status2=0 ";
            if($_date30 != NULL){
                $query .=" and create_date >= '$_date30'";
            }
            if((int)$____storeid >0){
                $query .=" and vote_id=$____storeid";
            }
            $query .=" order by id asc limit 0,1";
            $result = $db->fetchAll($query);
            return $result[0];
        }
        public function get_top1_ck($_date30=""){
            $db = $this->getDbConnection();
            $query = "select * from $this->_tablename where isync_ck=1 and is_actived = 1 and status2=0 and money_voucher >0 and block=1   ";
            if($_date30 != NULL){
                $query .=" and create_date >= '$_date30'";
            }
            $query .=" order by id asc limit 0,1";
            $result = $db->fetchAll($query);
            return $result[0];
        }
        
        

        public function get_list_by_active_vendor($_productsid=0,$strID,$start,$end,$storeid=0,$active_vendor=0){
            $db = $this->getDbConnection();
            $query = "select products_name,imes,create_date,vote_id,status2,id_addon_user,parentid_outbill,description,is_actived,(sum(products_price)-sum(reduction_money)-sum(money_voucher)-sum(bonus_tech)) as sum from $this->_tablename where is_actived =1 and cated_id IN ($strID) and active_vendor =$active_vendor ";
            if((int)$_productsid >0){
                $query .= " and productsid = $_productsid ";
            }
            
            if((int)$storeid >0){
                $query .= " and vote_id = $storeid ";
            }
            $query .= " and create_date >= '$start' and create_date <= '$end' ";
            
            $result = $db->fetchAll($query);
            
            return $result;
        }
        public function get_list_products_color($products_id="",$start,$end)
	{
            $cache = $this->getCacheInstance();
            $key = "get_list_products_color.$this->_tablename.$products_id";
            $result = $cache->getCache($key);
                $result = FALSE;
            if($result === FALSE)
            {
                $db = $this->getDbConnection();
                $query   = "SELECT vote_id,products_id,colorid, (sum(products_price)-sum(reduction_money)-sum(money_voucher)-sum(bonus_tech)) as sum, count(products_price) as total FROM $this->_tablename where is_actived = 1";
                if($products_id !=NULL){
                    $query .=" and products_id IN ($products_id) ";
                }
                $query .="  and status2 = 0 and create_date >='$start' and create_date <='$end'";
                $query .= " group by vote_id,colorid";
//                    var_dump($query);exit();
                $result = $db->fetchAll($query);
                if(!is_null($result) && is_array($result))
                {
                        $cache->setCache($key, $result,600);
                }
            }
            return $result;		
	}
        
        
        public function get_list_by_id_addon_user($id_addon_user=""){
            $db = $this->getDbConnection();
            if($id_addon_user ==NULL){
                $id_addon_user =  (int)$id_addon_user;
            }
            $query = "select products_name,imes,create_date,vote_id,status2,id_addon_user,parentid_outbill,description,is_actived,products_id,phone_addon,block,cated_id from $this->_tablename where id_addon_user IN ($id_addon_user)";
            $result = $db->fetchAll($query);
            return $result;
        }
        public function get_list_by_id_addon_user_not_sync($strID,$start="",$end=""){
            $db = $this->getDbConnection();
            $query = "select products_name,imes,create_date,vote_id,status2,id_addon_user,parentid_outbill,description,is_actived,products_id,phone_addon,block,vote_id from $this->_tablename where id_addon_user NOT IN ($strID) and is_actived=1 and vote_id !=167 ";
            if($start != NULL){
                $query .=" and create_date > '$start'";
            }
            if($end != NULL){
                $query .=" and create_date <= '$end'";
            }
            $result = $db->fetchAll($query);
            return $result;
        }
        public function get_list_by_id_addon_user_sync_not_delete($strID="",$start="",$end=""){
            $db = $this->getDbConnection();
            $query = "select products_name,imes,create_date,vote_id,status2,id_addon_user,parentid_outbill,description,is_actived,products_id,phone_addon,block,vote_id from $this->_tablename where  is_actived=0 and isync=2 and vote_id !=167 ";
            if($strID != NULL){
                $query .=" and id_addon_user IN ($strID)";
            }
            if($start != NULL){
                $query .=" and create_date > '$start'";
            }
            if($end != NULL){
                $query .=" and create_date <= '$end'";
            }
            $result = $db->fetchAll($query);
            return $result;
        }
        public function get_list_by_req($isdel,$storeid){
            $db = $this->getDbConnection();
            $query = "select * from $this->_tablename where isdel > 0";
            if((int)$isdel >0){
                $query .=" and isdel = $isdel ";
                if($isdel ==1 || $isdel==3){
                    $query .=" and is_actived = 1 ";
                }
            }
            if((int)$storeid >0){
                $query .=" and vote_id IN ($storeid) ";
            }
            $query .=" group by id_addon_user";
            $query .=" order by time_delete desc";
            $result = $db->fetchAll($query);
            return $result;
        }
        
        public function get_list_by_imei($imei){
            $db = $this->getDbConnection();
            $query = "select products_name,imes,create_date,vote_id,status2,id_addon_user,parentid_outbill,description,is_actived,products_id from $this->_tablename where imes Like '%$imei%' limit 200";
            $result = $db->fetchAll($query);
            return $result;
        }
        public function get_list_by_imei3($imei){
            $db = $this->getDbConnection();
            $query = "select products_name,imes,create_date,vote_id,status2,id_addon_user,parentid_outbill,description,is_actived,products_id from $this->_tablename where imes ='$imei'";
            $result = $db->fetchAll($query);
            return $result;
        }
        public function get_list_by_imei2($imei){
            $db = $this->getDbConnection();
            $query = "select products_name,imes,create_date,vote_id,status2,id_addon_user,parentid_outbill,description,is_actived,products_id from $this->_tablename where imes LIKE '%$imei%' limit 200";
            $result = $db->fetchAll($query);
            return $result;
        }
        public function get_list_by_imei4($strimei){
            $db = $this->getDbConnection();
            $query = "select DISTINCT imes, products_name,create_date,vote_id,status2,id_addon_user,description,is_actived,products_id from $this->_tablename where imes IN ($strimei) and is_actived =1 and status2=0 order by autoid desc";
            $result = $db->fetchAll($query);
            return $result;
        }
        

        public function report_voucher($cated_voucher,$sectionid_voucher=0,$start,$end,$storeid=0){
            $cache = $this->getCacheInstance('event');
            $key = "report_voucher.$this->_tablename.$cated_voucher.$sectionid_voucher.$start.$end.$storeid";
            $result = $cache->getCache($key);
            $result = FALSE;
            if($result === FALSE)
            {
                $db = $this->getDbConnection();
                $query =  "SELECT sectionid_voucher,productsid,id_voucher,count(*) as total, (sum(products_price)-sum(reduction_money)-sum(bonus_tech)) as sum,sum(money_voucher) as money_vouchers FROM $this->_tablename "
                        . "WHERE `cated_voucher` = $cated_voucher and is_actived=1 and status2=0 and create_date >='$start' and create_date <='$end'  ";
                
                if((int)$sectionid_voucher >0){
                    $query .="and sectionid_voucher = $sectionid_voucher";
                }
                if((int)$storeid >0){
                    $query .="and vote_id = $storeid";
                }
                $query .=" group by productsid";
//                echo "<pre>";
//                var_dump($query);
//                die();
                $result = $db->fetchAll($query);
                if(!is_null($result) && is_array($result))
                {
                    $cache->setCache($key, $result,60*5);
                }
            }
            return $result;    
        }

        public function getListSales($userid=0,$created_date, $end_date,$member_id=0,$keywork="",$status="-1"){
                $day = md5($created_date.$end_date);
                $cache = $this->getCacheInstance('app');
		$key = "getListSales.$this->_tablename.$userid.$day.$keywork.$member_id.$status";
		$result = $cache->getCache($key);
                $result ==FALSE;
		if($result === FALSE)
		{
                    $db = $this->getDbConnection();
                    $query =  "SELECT *,(sum(products_price)-sum(reduction_money)-sum(money_voucher)-sum(bonus_tech)) as sum,free_prepaid FROM `users_products` up WHERE  up.is_actived = 1 ";
                    if((int)$userid > 0){
                        $query.="  AND vote_id = '$userid' ";
                    }
                    if((int)$member_id >0){
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
                    $query .= " and up.create_date > '$created_date' and up.create_date <= '$end_date' ";
                    
                    $query .= " group by autoid ORDER BY up.create_date DESC";
                    
                    $result = $db->fetchAll($query);
                    if(!is_null($result) && is_array($result))
                    {
                        $cache->setCache($key, $result,60);
                    }
                }
            return $result;
        }
        public function get_list_by_cateid($str_cated_id,$from,$to){
            $db = $this->getDbConnection();
            $query = "select flag,cated_id,colorid,product_color,products_name,imes,create_date,vote_id,status2,id_addon_user,parentid_outbill,description,is_actived,products_id,(sum(products_price)-sum(reduction_money)-sum(money_voucher)-sum(bonus_tech)) as sum,count(*) as total from $this->_tablename where is_actived=1 ";
            if($str_cated_id != NULL){
                $query .=" and cated_id IN ($str_cated_id)";
            }
            $query .= " and create_date > '$from' and create_date <= '$to' ";
            $query .=" group by products_id,colorid order by cated_id desc";
            $result = $db->fetchAll($query);
            return $result;
        }
        public function get_list_by_sales($storeid=0,$from, $to){
                $cache = $this->getCacheInstance('app');
		$key = "get_list_by_sales.$this->_tablename.$storeid.$from.$to";
		$result = $cache->getCache($key);
                $result ==FALSE;
		if($result === FALSE)
		{
                    $db = $this->getDbConnection();
                    $query =  "SELECT *,(sum(products_price)-sum(reduction_money)-sum(money_voucher)-sum(bonus_tech)) as sum FROM `users_products` up WHERE  up.is_actived = 1 ";
                    if((int)$storeid > 0){
                        $query.="  AND vote_id = '$storeid' ";
                    }
                    $query .= " and up.create_date > '$from' and up.create_date <= '$to' ";
                    $query .= " group by autoid ORDER BY up.create_date DESC";
                    
                    $result = $db->fetchAll($query);
                    if(!is_null($result) && is_array($result))
                    {
                        $cache->setCache($key, $result,60);
                    }
                }
            return $result;
        }
        public function getOnline($storeid,$idregency=0,$mbid=0,$dh,$keywork=""){
            $cache = $this->getCacheInstance('event');
            $key = "getOnline.$this->_tablename.$storeid.$idregency.$mbid.$keywork";
            $result = $cache->getCache($key);
//            $result= FALSE;
            if($result=== FALSE){
                $db = $this->getDbConnection();
                $query =  "SELECT *,(sum(products_price)-sum(reduction_money)-sum(money_voucher)-sum(bonus_tech)) as sum FROM $this->_tablename  WHERE  1=1 ";
                if($storeid != 167){
                    $query .=" and vote_online = $storeid";
                }else{
                    if($idregency ==18){
                        $query .=" and id_users = $mbid";
                    }
                }
                if($dh ==3){
                   $query .=" and pre_order = 1"; 
                }else{
                    $query .=" and isonline = 2"; 
                }
//                $query .=" and is_actived = 0";
                if($keywork != null){
                    $query .=" and (id_addon_user = '$keywork' or imes = '$keywork' or fullname_addon like '%$keywork%' or phone_addon like '%$keywork%') ";
                }
                $query .= " group by autoid order by create_date desc limit 200";
//                echo "<pre>";
//                var_dump($query);
//                die();
                $result = $db->fetchAll($query);
                if(!is_null($result) && is_array($result))
                {
                    $cache->setCache($key, $result, 120);
                }
            }
            return $result;
            
        }
       
        
        
        public function countGoalByPhone($phone="",$id_addon_user=0,$start=""){
            $cache = GlobalCache::getCacheInstance('event');
            $key = "countGoalByPhone.$this->_tablename.$phone.$id_addon_user.$start";
            $result = $cache->getCache($key);
//            $result = FALSE;
            $now = date('Y-m-d');
            if($result=== FALSE){
                
               $db = $this->getDbConnection();
                $query = "select (sum(products_price)-sum(reduction_money)-sum(money_voucher)) as sum from $this->_tablename where is_actived = 1 and status2 = 0";
                if($phone != NULL){
                    $query .=" and phone_addon = '$phone'";
                }
                if((int)$id_addon_user >0){
                    $query .=" and id_addon_user = $id_addon_user";
                }
                if($start != NULL){
                    $query .=" and create_date >= '$start'";
                }
                $query .=" and create_date <= '$now'";
                
                $_result = $db->fetchAll($query);
                $result = $_result[0];
                if($result != null && is_array($result))
                {
                    $cache->setCache($key, $result,10*60);				
                }
            }
            
            return $result;
        }
        public function countGoalByPhone2($phone="",$id_addon_user=0,$start="",$__itemid=""){
            $cache = GlobalCache::getCacheInstance('event');
            $key = "countGoalByPhone2.$this->_tablename.$phone.$id_addon_user.$start.$__itemid";
            $result = $cache->getCache($key);
//            $result = FALSE;
            $now = date('Y-m-d');
            if($result=== FALSE){
                
               $db = $this->getDbConnection();
                $query = "select (sum(products_price)-sum(reduction_money)-sum(money_voucher)) as sum from $this->_tablename where is_actived = 1 and status2 = 0";
                if($phone != NULL){
                    $query .=" and phone_addon = '$phone'";
                }
                if((int)$id_addon_user >0){
                    $query .=" and id_addon_user = $id_addon_user";
                }
                if($start != NULL){
                    $query .=" and create_date >= '$start'";
                }
                if($__itemid != NULL){
                    $query .=" and products_id NOT IN ($__itemid)";
                }
                $_result = $db->fetchAll($query);
                $result = $_result[0];
                if($result != null && is_array($result))
                {
                    $cache->setCache($key, $result,10*60);				
                }
            }
            
            return $result;
        }

        public function getDetail($autoid)
	{
            $cache = $this->getCacheInstance('ws');
            $key = "getDetail.$this->_tablename.$autoid";
            $result = $cache->getCache($key);
            $result= FALSE;
            if($result=== FALSE){
                
               $db = $this->getDbConnection();
                $query = "select * from $this->_tablename where autoid = $autoid ";
                $_result = $db->fetchAll($query);
                $result = $_result[0];
                if($result != null && is_array($result))
                {
                    $cache->setCache($key, $result);				
                }
            }
            
            return $result;
	}
        public function get_detail_by_sync_cathe($id_addon_user)
	{
            $cache = $this->getCacheInstance('ws');
            $key = "get_detail_by_sync_cathe.$this->_tablename.$id_addon_user";
            $result = $cache->getCache($key);
            $result = FALSE;
            if($result=== FALSE){
                
               $db = $this->getDbConnection();
                $query = "select * from $this->_tablename where is_actived = 1 and id_addon_user = $id_addon_user and isync_cathe=1 and free_prepaid > 0 and block=1  ";
                $_result = $db->fetchAll($query);
                $result = $_result[0];
                if($result != null && is_array($result))
                {
                    $cache->setCache($key, $result);				
                }
            }
            
            return $result;
	}
        public function get_list_by_sync_cathe($id_addon_user,$foresync=0)
	{
            $cache = $this->getCacheInstance('ws');
            $key = "get_list_by_sync_cathe.$this->_tablename.$id_addon_user";
            $result = $cache->getCache($key);
            $result = FALSE;
            if($result=== FALSE){
                
               $db = $this->getDbConnection();
               if($foresync==1){
                   $query = "select * from $this->_tablename where is_actived = 1 and id_addon_user = $id_addon_user and free_prepaid > 0   and vote_id !=167";
               }else{
                   $query = "select * from $this->_tablename where is_actived = 1 and id_addon_user = $id_addon_user and isync_cathe=1 and free_prepaid > 0 and block=1   and vote_id !=167";
               }
                
                $result = $db->fetchAll($query);
                if($result != null && is_array($result))
                {
                    $cache->setCache($key, $result);				
                }
            }
            
            return $result;
	}
        public function get_detail_by_sync($id_addon_user)
	{
            $cache = $this->getCacheInstance('ws');
            $key = "get_detail_by_sync.$this->_tablename.$id_addon_user";
            $result = $cache->getCache($key);
            $result = FALSE;
            if($result=== FALSE){
                
               $db = $this->getDbConnection();
                $query = "select * from $this->_tablename where is_actived = 1 and id_addon_user = $id_addon_user and isync=1 and block=1  ";
                $_result = $db->fetchAll($query);
                $result = $_result[0];
                if($result != null && is_array($result))
                {
                    $cache->setCache($key, $result);				
                }
            }
            
            return $result;
	}
        public function get_list_by_sync($id_addon_user,$foresync=0)
	{
            $cache = $this->getCacheInstance('ws');
            $key = "get_list_by_sync.$this->_tablename.$id_addon_user.$foresync";
            $result = $cache->getCache($key);
            $result = FALSE;
            if($result=== FALSE){
                
               $db = $this->getDbConnection();
               if($foresync ==1){
                   $query = "select * from $this->_tablename where is_actived = 1 and  id_addon_user = $id_addon_user and vote_id !=167";
               }else{
                   $query = "select * from $this->_tablename where is_actived = 1 and  id_addon_user = $id_addon_user and isync=1 and block=1  and vote_id !=167 ";
               }
                $result = $db->fetchAll($query);
                if($result != null && is_array($result))
                {
                    $cache->setCache($key, $result);				
                }
            }
            
            return $result;
	}
        public function get_detail_by_sync_ck($id_addon_user)
	{
            $cache = $this->getCacheInstance('ws');
            $key = "get_detail_by_sync_ck.$this->_tablename.$id_addon_user";
            $result = $cache->getCache($key);
            $result = FALSE;
            if($result=== FALSE){
                
               $db = $this->getDbConnection();
                $query = "select * from $this->_tablename where is_actived = 1 and  id_addon_user = $id_addon_user and isync_ck=1 and money_voucher >0 and block=1  ";
                $_result = $db->fetchAll($query);
                $result = $_result[0];
                if($result != null && is_array($result))
                {
                    $cache->setCache($key, $result);				
                }
            }
            
            return $result;
	}
        public function get_list_by_sync_ck($id_addon_user,$foresync=0)
	{
            $cache = $this->getCacheInstance('ws');
            $key = "get_list_by_sync_ck.$this->_tablename.$id_addon_user";
            $result = $cache->getCache($key);
            $result = FALSE;
            if($result=== FALSE){
                
               $db = $this->getDbConnection();
               if($foresync ==1){
                   $query = "select * from $this->_tablename where is_actived = 1 and  id_addon_user = $id_addon_user and money_voucher >0  and vote_id !=167";
               }else{
                   $query = "select * from $this->_tablename where is_actived = 1 and  id_addon_user = $id_addon_user and isync_ck=1 and money_voucher >0 and block=1   and vote_id !=167";
               }
                $result = $db->fetchAll($query);
                if($result != null && is_array($result))
                {
                    $cache->setCache($key, $result);				
                }
            }
            
            return $result;
	}
        public function get_list_by_autoid($autoid)
	{
            $cache = $this->getCacheInstance('ws');
            $key = "get_list_by_autoid.$this->_tablename.$autoid";
            $result = $cache->getCache($key);
            $result= FALSE;
            if($result=== FALSE){
                
               $db = $this->getDbConnection();
                $query = "select * from $this->_tablename where autoid = $autoid ";
                $_result = $db->fetchAll($query);
                $result = $_result[0];
                if($result != null && is_array($result))
                {
                    $cache->setCache($key, $result);				
                }
            }
            
            return $result;
	}
        public function getListByVoucher($voucher)
	{
		$cache = $this->getCacheInstance();
		$key = 'getListByVoucher'.$this->_tablename.$voucher;
		$result = $cache->getCache($key);
		if($result === FALSE)
		{
			$db = $this->getDbConnection();
			$query = "SELECT * FROM $this->_tablename where id_voucher = '$voucher' or phone_addon = '$voucher'";
			$result = $db->fetchAll($query);
			if(!is_null($result) && is_array($result))
			{
				$cache->setCache($key, $result);
			}
		}
		
		return $result;		
	}
        
        public function getListSalesAll($created_day="", $end_day="",$productsid="",$storeid="")
	{
            $cache = $this->getCacheInstance();
            $key = "getListSalesAll.$this->_tablename.$created_day.$end_day.$productsid";
            $result = $cache->getCache($key);
                $result = FALSE;
            if($result === FALSE)
            {
                $db = $this->getDbConnection();
                $query   = "SELECT productsid,is_apple,vote_id, flag, (sum(products_price)-sum(reduction_money)-sum(money_voucher)-sum(bonus_tech)) as sum, count(products_price) as total FROM $this->_tablename where is_actived = 1";
                if($productsid !=NULL){
                    $query .=" and productsid IN ($productsid) ";
                }
                if($created_day != null){
                    $query .=" and create_date >=  '$created_day'";
                }
                if($end_day != null){
                    $query .="  and create_date <= '$end_day'";
                }
                if($storeid !=null){
                    $query .=" and vote_id IN ($storeid)";
                }
                $query .="  and status2 = 0";
                $query .= " group by vote_id,flag,productsid,is_apple";
//                    var_dump($query);//exit();
                $result = $db->fetchAll($query);
                if(!is_null($result) && is_array($result))
                {
                        $cache->setCache($key, $result,600);
                }
            }
            return $result;		
	}
        
        
        public function getListByInstallment($created_date, $end_date){
            $cache = $this->getCacheInstance();
		$key = "getListByInstallment.$this->_tablename.$created_date.$end_date";
		$result = $cache->getCache($key);
		$result =FALSE;
		if($result === FALSE)
		{
                    $db = $this->getDbConnection();
                    $query =  "SELECT flag,vote_id,create_date,products_name,products_price,id_addon_user,status_chargecard,product_color,cated_prepaid_installment,contract,money_installment,products_price,(products_price- reduction_money - money_voucher) as total_price FROM $this->_tablename WHERE is_actived = 1 and cated_prepaid_installment >0";
                    $query .= " and create_date >= '$created_date' and create_date <= '$end_date' ";
                    $query .= " ORDER BY vote_id,cated_prepaid_installment ";
//                    var_dump($query);exit();
                    $result = $db->fetchAll($query);
                    if(!is_null($result) && is_array($result))
			{
                        $cache->setCache($key, $result,600);
                    }
                    
                }
            return $result;
        }
        public function getListByTotalInstallment($created_date, $end_date){
            $cache = $this->getCacheInstance();
		$key = "getListByTotalInstallment.$this->_tablename.$created_date.$end_date";
		$result = $cache->getCache($key);
		$result =FALSE;
		if($result === FALSE)
		{
                    $db = $this->getDbConnection();
                    $query =  "SELECT flag,vote_id,create_date,products_name,id_addon_user,status_chargecard,product_color,cated_prepaid_installment,contract,money_installment,,products_price,(sum(products_price)- sum(reduction_money) - sum(money_voucher)) as total_price FROM $this->_tablename WHERE is_actived = 1 and cated_prepaid_installment >0";
                    $query .= " and create_date >= '$created_date' and create_date <= '$end_date' ";
                    $query .= " Group by id_addon_user,cated_card";
                    $query .= " order by vote_id,create_date";
//                    var_dump($query);exit();
                    $result = $db->fetchAll($query);
                    if(!is_null($result) && is_array($result))
			{
                        $cache->setCache($key, $result,600);
                    }
                    
                }
            return $result;
        }
        public function getListByChargecard($created_date, $end_date){
            $cache = $this->getCacheInstance();
		$key = "getListByChargecard.$this->_tablename.$created_date.$end_date";
		$result = $cache->getCache($key);
		$result =FALSE;
		if($result === FALSE)
		{
                    $db = $this->getDbConnection();
                    $query =  "SELECT flag,vote_id,create_date,products_name,products_price,id_addon_user,status_chargecard,product_color,prepaid,free_prepaid,bill_txt,cated_card,free_prepaid,bill_txt,cated_card,products_price,(products_price- reduction_money - money_voucher) as total_price FROM $this->_tablename WHERE is_actived = 1 and cated_card >0";
                    $query .= " and create_date >= '$created_date' and create_date <= '$end_date' ";
                    $query .= " ORDER BY vote_id,id_addon_user ";
//                    var_dump($query);exit();
                    $result = $db->fetchAll($query);
                    if(!is_null($result) && is_array($result))
			{
                        $cache->setCache($key, $result,600);
                    }
                    
                }
            return $result;
        }
        public function getListByTotalChargecard($created_date, $end_date){
            $cache = $this->getCacheInstance();
		$key = "getListByTotalChargecard.$this->_tablename.$created_date.$end_date";
		$result = $cache->getCache($key);
		$result =FALSE;
		if($result === FALSE)
		{
                    $db = $this->getDbConnection();
                    $query =  "SELECT flag,vote_id,create_date,products_name,id_addon_user,status_chargecard,product_color,sum(prepaid) as prepaids,sum(free_prepaid) as free_prepaids,bill_txt,cated_card,products_price,(sum(products_price)- sum(reduction_money) - sum(money_voucher)) as total_price FROM $this->_tablename WHERE is_actived = 1 and cated_card >0";
                    $query .= " and create_date >= '$created_date' and create_date <= '$end_date' ";
                    $query .= " Group by id_addon_user,cated_card";
                    $query .= " order by vote_id,create_date";
//                    var_dump($query);exit();
                    $result = $db->fetchAll($query);
                    if(!is_null($result) && is_array($result))
			{
                        $cache->setCache($key, $result,600);
                    }
                    
                }
            return $result;
        }
        
        public function getListByMenuProducts($productsid=3,$tiento="", $phuDinh=0){
        $menuname='';
        if($productsid ==3){
            $menuname = 'menu_products';
        }
        if($productsid ==4){
            $menuname = 'menu_acc';
        }
        if($productsid ==5){
            $menuname = 'menu_tablet';
        }
        if($productsid ==6){
            $menuname = 'menu_laptop';
        }
        $db = $this->getDbConnection();
        if ($phuDinh===1) {
            $query = "SELECT itemid,title FROM `ws_menuitem` WHERE title NOT like '%$tiento%' and menuname ='$menuname'";
        } else {
            $query = "SELECT itemid,title FROM `ws_menuitem` WHERE title like '%$tiento%' and menuname ='$menuname'";
        }
        $result = $db->fetchAll($query);
        return $result;
    }
        
        
        public function getListSalesCatedId($created_day="", $end_day="",$flag="",$storeid=0,$productsid=0,$is_apple="",$is_type="")
	{
            $cache = $this->getCacheInstance();
            $key = "getListSalesCatedId.$this->_tablename.$created_day.$end_day.$flag.$storeid.$productsid.$is_apple.$is_type";
            $result = $cache->getCache($key);
            $result = FALSE;
            if($result === FALSE)
            {
                $db = $this->getDbConnection();
                $query   = "SELECT productsid,is_apple,vote_id, cated_id, (sum(products_price)-sum(reduction_money)-sum(money_voucher)-sum(bonus_tech)) as sum, count(*) as total FROM $this->_tablename where is_actived = 1";
                if($productsid !=0){
                    $query .=" and productsid IN ($productsid) ";
                }
                if($is_apple != null){
                    $query .=" and is_apple = '$is_apple'";
                }
                if($is_type != null){
                    $query .=" and type = '$is_type'";
                }else{
                    $query .="  and cated_id !=53";
                }
                if($created_day != null){
                    $query .=" and create_date >=  '$created_day'";
                }
                if($end_day != null){
                    $query .="  and create_date <= '$end_day'";
                }
                if($flag != null){
                    $query .="  and flag = $flag ";
                }
                if($storeid > 0){
                    $query .="  and vote_id = $storeid ";
                }
                
                $query .="  and status2 = 0";
                $query .= " group by vote_id,cated_id";
//                    var_dump($query);//exit();
                $result = $db->fetchAll($query);
                if(!is_null($result) && is_array($result))
                {
                        $cache->setCache($key, $result,600);
                }
            }
            return $result;		
	}
        
        public function getListSalesProductId($created_day="", $end_day="",$flag="",$vote_online="",$productsid=0,$cated_id,$is_apple="",$is_type="")
	{
            $cache = $this->getCacheInstance();
            $key = "getListSalesProductId.$this->_tablename.$created_day.$end_day.$flag.$vote_online.$productsid.$cated_id.$is_apple.$is_type";
            $result = $cache->getCache($key);
                $result = FALSE;
            if($result === FALSE)
            {
                $db = $this->getDbConnection();
                $query  = "SELECT vote_id,products_id, (sum(products_price)-sum(reduction_money)-sum(money_voucher)-sum(bonus_tech)) as sum, count(*) as total FROM $this->_tablename where  is_actived = 1 and cated_id IN ($cated_id) ";
                if($productsid !=0){
                    $query .=" and productsid =  $productsid ";
                }
                if($is_apple != null){
                    $query .=" and is_apple = '$is_apple'";
                }
                if($is_type != null){
                    $query .=" and type = '$is_type'";
                }
                if($created_day != null){
                    $query .=" and create_date >=  '$created_day'";
                }
                if($end_day != null){
                    $query .="  and create_date <= '$end_day'";
                }
                if($flag != null){
                    $query .="  and flag = $flag ";
                }
                
                $query .="  and status2 = 0";
                $query .= " group by vote_id,products_id";
//                    var_dump($query);//exit();
                $result = $db->fetchAll($query);
                if(!is_null($result) && is_array($result))
                {
                        $cache->setCache($key, $result,300);
                }
            }
            return $result;		
	}
        
        
        
        
        
        
        
        
        
        
        function getListTargetByMb($date_from="",$date_to="",$storeid=0,$mbid=0, $actived="") {
            $cache = $this->getCacheInstance();
            $key = "getListTargetByMb.$this->_tablename.$date_from.$date_to.$actived.$storeid.$mbid";
            $result = $cache->getCache($key);
            $result=FALSE;
            if($result === FALSE){
                $db = $this->getDbConnection();
                $query = "SELECT id_users as mb,flag,productsid,type,is_apple, vote_id, (sum(products_price)-sum(reduction_money)-sum(money_voucher)-sum(bonus_tech)) as sum,count(products_name) as total "
                        . "FROM $this->_tablename "
                        . "WHERE create_date>='$date_from' AND create_date <='$date_to'  and is_actived = 1 and status2=0 and status_thuhoi=0 ";
                if($storeid !=0){
                    $query .=" and vote_id = $storeid";
                }
                if($mbid !=0){
                    $query .=" and id_users = $mbid";
                }
                $query .=" GROUP BY  id_users,vote_id,productsid,flag,type,is_apple";
//                var_dump($query);//exit();
                $data = array();
                $result = $db->fetchAll($query, $data);
                if(!is_null($result) && is_array($result))
                {
                        $cache->setCache($key, $result,300);
                }
            }
            return $result;
        }
        function getListTargetByMonthYear($month,$year,$storeid=0,$mbid=0) {
            $cache = $this->getCacheInstance();
            $key = "getListTargetByMonthYear.$this->_tablename.$month.$year.$storeid.$mbid";
            $result = $cache->getCache($key);
            $result=FALSE;
            if($result === FALSE){
                $db = $this->getDbConnection();
                $query = "SELECT id_users as mb,flag,productsid,type,is_apple, vote_id, (sum(products_price)-sum(reduction_money)-sum(money_voucher)-sum(bonus_tech)) as sum,count(products_name) as total "
                        . "FROM $this->_tablename "
                        . "WHERE MONTH(create_date) = $month AND YEAR(create_date) = $year  and is_actived = 1 and status2=0 and status_thuhoi=0";
                if($storeid !=0){
                    $query .=" and vote_id = $storeid";
                }
                if($mbid !=0){
                    $query .=" and id_users = $mbid";
                }
                $query .=" GROUP BY  id_users,vote_id,productsid,flag,type,is_apple";
//                var_dump($query);//exit();
                $data = array();
                $result = $db->fetchAll($query, $data);
                if(!is_null($result) && is_array($result))
                {
                        $cache->setCache($key, $result,300);
                }
            }
            return $result;
        }
        function getListTargetByStoreid($date_from="",$date_to="",$storeid=0,$status2=0) {
            $status2 = (int)$status2;
            $cache = $this->getCacheInstance();
            $key = "getListTargetByStoreid.$this->_tablename.$date_from.$date_to.$storeid.$status2";
            $result = $cache->getCache($key);
            $result=FALSE;
            if($result === FALSE){
                $db = $this->getDbConnection();
                $query = "SELECT flag,productsid,type,is_apple, vote_id, (sum(products_price)-sum(reduction_money)-sum(money_voucher)-sum(bonus_tech)) as sum,count(products_name) as total "
                        . "FROM $this->_tablename "
                        . "WHERE  create_date>='$date_from' AND create_date <='$date_to'  and is_actived = 1  ";
                if($status2 >-1){
                    $query .="and status2 = $status2 ";
                }
                
                if($storeid !=0){
                    $query .=" and vote_id IN ($storeid)";
                }
                $query .=" GROUP BY  vote_id,productsid,flag,type,is_apple";
//                var_dump($query);//exit();
                $data = array();
                $result = $db->fetchAll($query, $data);
                if(!is_null($result) && is_array($result))
                {
                        $cache->setCache($key, $result,300);
                }
            }
            return $result;
        }
        function get_target($date_from="",$date_to="",$storeid=0,$status2=0) {
            $status2 = (int)$status2;
            $cache = $this->getCacheInstance();
            $key = "get_target.$this->_tablename.$date_from.$date_to.$storeid.$status2";
            $result = $cache->getCache($key);
            $result=FALSE;
            if($result === FALSE){
                $db = $this->getDbConnection();
                $query = "SELECT flag,productsid,type,is_apple, vote_id, (sum(products_price)-sum(reduction_money)-sum(money_voucher)-sum(bonus_tech)) as sum,count(products_name) as total "
                        . "FROM $this->_tablename "
                        . "WHERE  create_date>='$date_from' AND create_date <='$date_to'  and is_actived = 1 and status_thuhoi=0";
                if($status2 >-1){
                    $query .=" and status2 = $status2 ";
                }
                
                if($storeid !=0){
                    $query .=" and vote_id IN ($storeid)";
                }
                $query .=" GROUP BY  vote_id,productsid,flag,type,is_apple";
//                var_dump($query);//exit();
                $data = array();
                $result = $db->fetchAll($query, $data);
                if(!is_null($result) && is_array($result))
                {
                        $cache->setCache($key, $result,300);
                }
            }
            return $result;
        }
        
        
        
        
        public function getInstallment($id_installment=0,$c_months_installment=0,$startdate="",$enddate=""){
            $cache = $this->getCacheInstance();
            $key = "getInstallment.$this->_tablename.$id_installment.$c_months_installment.$startdate.$enddate";
            $result = $cache->getCache($key);
//            $result=FALSE;
            if($result === FALSE){
                $db = $this->getDbConnection();
                $query  = " SELECT  vote_id,id_installment,c_months_installment,(sum(products_price)-sum(reduction_money)-sum(money_voucher)-sum(bonus_tech)) as sum, count(products_price) as total,SUM(money_installment) as prepay FROM $this->_tablename where is_actived =1 ";
                if($id_installment != 0){
                    $query  .= " and id_installment = $id_installment ";
                }
                if($startdate !=null){
                    $query .=" and create_date >= '$startdate'";
                }
                if($enddate !=null){
                    $query .=" and create_date <= '$enddate'";
                }
                if($c_months_installment > 0){
                    $query  .= " and c_months_installment = $c_months_installment ";
                }
                $query.=" group by vote_id,id_installment,c_months_installment";
//                var_dump($query);exit();
                $result = $db->fetchAll($query);
                if(!is_null($result) && is_array($result))
                {
                        $cache->setCache($key, $result,600);
                }
            }
            
            return $result;
        }
        
        
        public function checkPhoneOnline($phone){
            $db = $this->getDbConnection();
            $query =  "SELECT * FROM $this->_tablename  WHERE  pre_order = 1 and phone_addon = '$phone' and is_actived = 0";
            $result = $db->fetchAll($query);
            return $result;
        }

        public function getPreOrder($storeid,$is_actived="",$uid="",$keywork=""){
            $db = $this->getDbConnection();
            $query =  "SELECT * FROM $this->_tablename  WHERE  pre_order = 1 ";
            if($storeid != 167){
                $query .=" and vote_online = $storeid";
            }else{
                if($uid !=167){
                    $query .=" and id_users = $uid";
                }
            }
            $query .=" and is_actived = $is_actived";
            if($keywork != null){
                $query .=" and (id_addon_user = '$keywork' or imes = '$keywork' or fullname_addon like '%$keywork%' or phone_addon like '%$keywork%') ";
            }
            $query .= " group by id_addon_user  limit 200";
            $result = $db->fetchAll($query);
            return $result;
            
        }

        

        public function getListByKeywork($storeid=0,$keywork="",$is_actived){
            $db = $this->getDbConnection();
            $query =  "SELECT * FROM $this->_tablename  WHERE  1 = 1 ";
            if($storeid != 0){
                $query .=" and vote_id = $storeid";
            }
            if($is_actived != null){
                $query .=" and is_actived = $is_actived";
            }
            if($keywork != null){
                $query .=" and (id_addon_user = '$keywork' or imes = '$keywork' or fullname_addon like '%$keywork%' or phone_addon like '%$keywork%') ";
            }
            $query .= " group by id_addon_user  order by autoid desc limit 200  ";
            $result = $db->fetchAll($query);
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
                $query .= " and create_date >= '$created_date' and create_date <= '$end_date' group by id_addon_user";
                $query .= " ORDER BY create_date DESC";
                $result = $db->fetchAll($query);

                if(!is_null($result) && is_array($result))
                {
                    $cache->setCache($key, $result,600);
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
                            $cache->setCache($key, $result,600);
                    }
                }
            return $result;		
	}

        public function getListByOption($created_day="", $end_day="",$is_actived="",$flag="",$vote_online="",$cated_id=0,$products_id="")
	{
            $cache = $this->getCacheInstance();
		$key = "getListByOption.$this->_tablename.$created_day.$end_day.$is_actived.$flag.$vote_online.$cated_id.$products_id";
		$result = $cache->getCache($key);
//                $cache->deleteCache($key);exit();
//                var_dump($result);exit();
//                $result = FALSE;
		if($result === FALSE)
                {
                    $db = $this->getDbConnection();
                    if($products_id != null){
                        $query  = "SELECT vote_id,products_id, sum(products_price) as sum, count(products_price) as countp,sum(reduction_money) as reduction_money,sum(money_voucher) as money_voucher FROM $this->_tablename where cated_id IN ($products_id) ";
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
//                    $query .=" and vote_online = $vote_online";
                    if($cated_id != 0){
                        $query .=" and cated_id IN($cated_id)";
                    }
                    $query .="  and status2 = 0";
                    if($products_id != null){
                        $query .= " group by vote_id,products_id";
                    }else{
                        $query .= " group by vote_id,cated_id";
                    }
//                    var_dump($query);//exit();
                    $result = $db->fetchAll($query);
                    if(!is_null($result) && is_array($result))
                    {
                            $cache->setCache($key, $result,600);
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
        public function getListByCateId($is_actived="",$created_date="",$end_date="",$cated_id="",$vote_id="",$mb_id="",$flag=""){
            $cache = $this->getCacheInstance();
		$key = "getListByCateId.$this->_tablename.$cated_id.$created_date.$end_date.$is_actived.$vote_id.$mb_id.$flag";
		$result = $cache->getCache($key);
                $result = FALSE; 
		if($result === FALSE)
                {
                    $db = $this->getDbConnection();
                    $query   = "SELECT products_id,id_users,id_addon_user,product_color,products_price,products_price_cost,reduction_money,money_voucher,products_name,imes ,create_date ,vote_id ,fullname_addon ,phone_addon  FROM $this->_tablename where 1=1 ";
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
                    if($flag != null){
                        $query .=" and flag = '$flag' ";
                    }
                    $query.=" and status2=0 ";
                    $query .=" order by vote_id ";
//                    var_dump($query);exit();
                    $result = $db->fetchAll($query);
                    if(!is_null($result) && is_array($result))
                    {
                            $cache->setCache($key, $result,600);
                    }
                }
            return $result;
        }
        public function getListByCateIdLite($is_actived="",$created_date="",$end_date="",$cated_id="",$vote_id="",$mb_id="",$flag=""){
            $cache = $this->getCacheInstance();
		$key = "getListByCateIdLite.$this->_tablename.$cated_id.$created_date.$end_date.$vote_id.$mb_id.$flag";
		$result = $cache->getCache($key);
//                $cache->deleteCache($key);exit();
//                var_dump($result);exit();
//                $result = FALSE;
		if($result === FALSE)
                {
                    $db = $this->getDbConnection();
                    $query   = "SELECT product_color,products_price,(sum(products_price_cost)- sum(reduction_money) - sum(money_voucher)) as total_price, count(products_price_cost) as total_items,reduction_money,money_voucher,products_name ,create_date ,vote_id  FROM $this->_tablename where 1=1 ";
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
                    if($flag != null){
                        $query .=" and flag = '$flag' ";
                    }
                    $query.=" and status2=0 ";
                    $query .=" GROUP BY products_name,product_color,vote_id";
//                    var_dump($query);exit();
                    $result = $db->fetchAll($query);
                    if(!is_null($result) && is_array($result))
                    {
                            $cache->setCache($key, $result,600);
                    }
                }
            return $result;
        }

        public function getListDisabled($products_id ='',$created_date,$end_date,$is_actived=1){
            $db = $this->getDbConnection();
            if($products_id == null){
                $products_id =6020;
            }
            $query =  "SELECT vote_id,count(*) as tong  FROM `users_products` WHERE products_id =  $products_id";
            
            if($is_actived != 1){
                $query .=" and `is_actived` = 0  and addon_info != ''";
            }
            $query .= " and create_date > '$created_date' and create_date < '$end_date' ";
            $query .="group by vote_id ";
//            var_dump($query);exit();
            $result = $db->fetchAll($query);
            return $result;
            
        }
        public function getSaleByStoreid($products_id ='',$created_date,$end_date,$is_actived=1){
            $db = $this->getDbConnection();
            if($products_id == null){
                $products_id =6020;
            }
            $query =  "SELECT vote_id,count(*) as tong  FROM `users_products` WHERE products_id =  $products_id";
            
            $query .=" and `is_actived` = 1  and addon_info is NULL";
            $query .= " and create_date >= '$created_date' and create_date <= '$end_date' ";
            $query .="group by vote_id ";
//            var_dump($query);exit();
            $result = $db->fetchAll($query);
            return $result;
            
        }
        public function getSaleByStoreid2($products_id ='',$created_date,$end_date,$is_actived=1){
            $db = $this->getDbConnection();
            $query =  "SELECT vote_id,count(*) as tong  FROM `users_products` WHERE products_id =  $products_id";
            
            $query .=" and `is_actived` = 1  and addon_info is not NULL";
            $query .= " and create_date >= '$created_date' and create_date <= '$end_date' ";
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
                    $query .= " and up.create_date >= '$created_date' and up.create_date <= '$end_date' ";
                    $query .= " group by vote_id ORDER BY up.create_date DESC";
//                    var_dump($query);exit();
                    $result = $db->fetchAll($query);
                    
                    if(!is_null($result) && is_array($result))
                    {
                        $cache->setCache($key, $result,600);
                    }
                }
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
            $cache = GlobalCache::getCacheInstance('event');
            $key = "checkImes".  $this->_tablename.$imes;
            $result = $cache->getCache($key);
            if($result === FALSE){
                $db = $this->getDbConnection();
                $query = "select * from $this->_tablename where imes = '$imes'";
    //            var_dump($query);exit();
                $result = $db->fetchAll($query);
                if(!is_null($result) && is_array($result))
                    {
                        $cache->setCache($key, $result);
                    }
            }
            
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
        public function get_detail_by_id($autoid)
	{
            $db = $this->getDbConnection();
            $query = "select * from $this->_tablename where autoid = $autoid";
//            var_dump($query);exit();
            $result = $db->fetchAll($query);
            return $result[0];
	}
        public function get_list_by_type($type,$storeid=0,$from="",$to="")
	{
            $db = $this->getDbConnection();
            $query = "select autoid,products_id,products_price,type,id_users,vote_id from $this->_tablename where 1=1";
            if($type != NULL){
                $query .=" and type IN ($type)";
            }
            if((int)$storeid > 0){
                $query .=" and vote_id IN ($storeid)";
            }
            if($from != NULL){
                $query .=" and create_date  >= '$from'";
            }
            if($to != NULL){
                $query .=" and create_date  <= '$to'";
            }
//            var_dump($query);exit();
            $result = $db->fetchAll($query);
            return $result;
	}
        public function get_detail_by_imei($imei)
	{
            $db = $this->getDbConnection();
            $query = "select * from $this->_tablename where imes = '$imei' order by autoid desc";
//            var_dump($query);exit();
            $result = $db->fetchAll($query);
            return $result[0];
	}
        public function get_detail_by_imei_by_date($imei,$date,$productsid="")
	{
            $db = $this->getDbConnection();
            $query = "select * from $this->_tablename where imes = '$imei' and is_actived=1 and DATE(create_date)='$date' and status2=0";
//            var_dump($query);exit();
            if($productsid != NULL){
                $query .=" and productsid IN ($productsid)";
            }
            $result = $db->fetchAll($query);
            return $result[0];
	}
        public function get_list_by_imei_by_date($imei,$date,$productsid="")
	{
            $db = $this->getDbConnection();
            $query = "select * from $this->_tablename where imes IN ($imei) and is_actived=1 and DATE(create_date)='$date'  and status2=0 ";
//            var_dump($query);exit();
            if($productsid != NULL){
                $query .=" and productsid IN ($productsid)";
            }
            $result = $db->fetchAll($query);
            return $result;
	}
        
        public function get_detail_by_imei_cateid($imei,$cateid)
	{
            $db = $this->getDbConnection();
            $query = "select * from $this->_tablename where imes = '$imei' and cated_id IN ($cateid) order by autoid desc";
//            var_dump($query);exit();
            $result = $db->fetchAll($query);
            return $result[0];
	}
        public function get_detail_by_imei2($imei)
	{
            $db = $this->getDbConnection();
            $query = "select autoid,id_addon_user,flag from $this->_tablename where imes = '$imei' order by autoid desc";
//            var_dump($query);exit();
            $result = $db->fetchAll($query);
            return $result[0];
	}
        public function get_detail_by_idaddonuser($id_addon_user)
	{
            $db = $this->getDbConnection();
            $query = "select * from $this->_tablename where id_addon_user = $id_addon_user";
//            var_dump($query);exit();
            $result = $db->fetchAll($query);
            return $result[0];
	}
        public function getDetailByBill($id_addon_user,$products_id="",$autoid="")
	{
            $cache = $this->getCacheInstance('ws');
            $key = "getDetailByBill.$this->_tablename.$id_addon_user.$products_id.$autoid";
            $result = $cache->getCache($key);
            $result= FALSE;
            if($result=== FALSE){
                
               $db = $this->getDbConnection();
                $query = "select * from $this->_tablename where id_addon_user = '$id_addon_user'";
                if($products_id != null){
                    $query .=" and products_id = $products_id";
                }
                if($autoid != null){
                    $query .=" and autoid = $autoid";
                }
//                var_dump($query);exit(); 
                $_result = $db->fetchAll($query);
                $result = $_result[0];
                if($result != null && is_array($result))
                {
                    $cache->setCache($key, $result);				
                }
            }
            
            return $result;
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
             $cache = $this->getCacheInstance('ws');
            $key = "getListByBillIdActived.$this->_tablename.$id_addon_user.$is_actived";
            $result = $cache->getCache($key);
            $result= FALSE;
            if($result=== FALSE){
                $db = $this->getDbConnection();
                $query = "select * from $this->_tablename where id_addon_user = '$id_addon_user' ";
                if($is_actived != null){
                    $query .=" and is_actived = $is_actived";
                }
                $result = $db->fetchAll($query);
                if(!is_null($result) && is_array($result))
                    {
                        $cache->setCache($key, $result, 600);
                    }
            }
            
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
		$db = $this->getDbConnection();
		$result = $db->insert($this->_tablename,$data);
                $this->_deleteAllCache();
                return $result;
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
//            var_dump($query);
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
            $query  .= "where  id_addon_user IN ($id_addon_user) ";
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
                            $cache->setCache($key, $result,600);
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
                            $cache->setCache($key, $result,600);
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
                            $cache->setCache($key, $result,600);
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
                            $cache->setCache($key, $result,600);
                    }
                }
            return $result;		
	}
        public function getListByVote2($productsid,$created_day, $end_day,$cated_id=0,$flag="")
	{
            $cache = $this->getCacheInstance();
		$key = "getListByStore2.$this->_tablename.$created_day.$end_day.$productsid.$cated_id.$flag";
		$result = $cache->getCache($key);
//                $cache->deleteCache($key);exit();
//                var_dump($result);exit();
//                $result = FALSE;
		if($result === FALSE)
                {
                    $db = $this->getDbConnection();
                    $query   = "SELECT vote_id, sum(products_price) as sum, count(products_price) as countp, sum(reduction_money) as reduction_money, sum(money_voucher) as money_voucher FROM `users_products` where is_actived = 1   and create_date >=  '$created_day' and create_date <= '$end_day' ";
                    if($cated_id != 0){
                        $query .=" and cated_id IN ($cated_id)";
                    }
                    if($flag != null){
                        $query .=" and flag = $flag";
                    }
                    $query .=" and productsid = $productsid";
                    $query .= " group by vote_id";
//                    var_dump($query);exit();
                    $result = $db->fetchAll($query);
                    if(!is_null($result) && is_array($result))
                    {
                            $cache->setCache($key, $result,600);
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
                            $cache->setCache($key, $result,600);
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
                            $cache->setCache($key, $result,600);
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
        public function getAllByCreate_Date($userid,$created_date, $end_date,$member_id=0,$keywork,$status="-1"){
                $day = md5($created_date.$end_date);
                $cache = $this->getCacheInstance('app');
		$key = "getAllByCreate_Date.$this->_tablename.$userid.$day.$keywork.$member_id.$status";
		$result = $cache->getCache($key);
                $result ==FALSE;
		if($result === FALSE)
		{
                    $db = $this->getDbConnection();
                    $query =  "SELECT *,(sum(products_price)-sum(reduction_money)-sum(money_voucher)-sum(bonus_tech)) as sum FROM `users_products` up WHERE  up.is_actived = 1 ";
                    if($userid != "" || $userid != null){
                        $query.="  AND vote_id = '$userid' ";
                    }
                    if((int)$member_id >0){
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
                    $query .= " and up.create_date >= '$created_date' and up.create_date <= '$end_date' group by up.id_addon_user";
                    
                    $query .= " ORDER BY up.create_date DESC";
                    $result = $db->fetchAll($query);
                    if(!is_null($result) && is_array($result))
                    {
                        $cache->setCache($key, $result,6*10);
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
                $query .= " and up.create_date >= '$created_date' and up.create_date <= '$end_date' group by up.id_addon_user";
                $query .= " ORDER BY up.create_date DESC";
                $query .= " LIMIT 100";
//                        var_dump($query);exit();
                $result = $db->fetchAll($query);
                if(!is_null($result) && is_array($result))
                    {
                        $cache->setCache($key, $result, 600);
                    }
            }
            
            return $result;
        }
        public function countDonHangOnline($pack){
            $cache = GlobalCache::getCacheInstance('event');
            $key = "countDonHangOnline.$this->_tablename.$pack";
            $result = $cache->getCache($key);
//            $result= FALSE;
            if($result=== FALSE){
                
                $db = $this->getDbConnection();
                $query =  "SELECT count(*) as total FROM `users_products` up WHERE  isonline = 2";
                if($pack == 1){
                    $query .=" and pack != 2 ";
                }
                if($pack == 2){
                    $query .= " and pack = 2 ";
                }
                $query .=" GROUP BY id_addon_user";
//                var_dump($query);exit();
                $result = $db->fetchAll($query);
                if(!is_null($result) && is_array($result))
                    {
                        $cache->setCache($key, $result, 1200);
                    }
            }
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
            
            $c_day = date('Y-m-d',  strtotime($created_date));
            $e_day = date('Y-m-d',  strtotime($end_date));
            $cache = $this->getCacheInstance('ws');
            $key = "getListIsOnline.$this->_tablename.$keywork.$vote_id.$isonline.$pack.$c_day.$e_day";
            $result = $cache->getCache($key);
            $result= FALSE;
            if($result=== FALSE){
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
                $query .= " and up.create_date >= '$created_date' and up.create_date <= '$end_date' group by up.id_addon_user";

                $query .= " ORDER BY up.create_date DESC";
//                        var_dump($query);//exit();
                $result = $db->fetchAll($query);
                if(!is_null($result) && is_array($result))
                    {
                        $cache->setCache($key, $result, 300);
                    }
            }
            return $result;
        }

        public function getAllByCreate_Date2($userid,$created_date, $end_date,$mid_day){
            $cache = $this->getCacheInstance();
		$key = "getAllByCreate_Date2.$this->_tablename.$userid.$created_date.$end_date.$mid_day";
		$result = $cache->getCache($key);
//		$result =FALSE;
		if($result === FALSE)
		{
                    $db = $this->getDbConnection();
                    $query =  "SELECT * FROM $this->_tablename WHERE vote_id = '$userid' and is_actived = 1 ";
                    if($mid_day !=null){
                        $query .= " and create_date >= '$mid_day' and create_date <= '$end_date'";
                    }else{
                        $query .= " and create_date >= '$created_date' and create_date <= '$end_date' ";
                    }
                    $query .= " ORDER BY create_date DESC";
//                    var_dump($query);exit();
                    $result = $db->fetchAll($query);
                    if(!is_null($result) && is_array($result))
			{
                        $cache->setCache($key, $result,600);
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
                    $query      = "select * from users_products where id_addon_user IN ($id_addon_user) ";
                    if($created_date != null){
                        $query .= " and create_date >= '$created_date' ";
                    }
                    if($end_date != null){
                        $query     .= " and create_date <= '$end_date' ";
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
        public function countByCateId($cated_id="",$created_date="", $end_date="",$is_actived=""){
            $c_day = date('Y-m-d',  strtotime($created_date));
            $e_day = date('Y-m-d',  strtotime($end_date));
                $cache = GlobalCache::getCacheInstance('ws');
		$key = "countByCateId.$this->_tablename.$cated_id.$c_day.$e_day.$is_actived";
		$result = $cache->getCache($key);
//                $result = FALSE;
		if($result === FALSE)
		{
                    $db = $this->getDbConnection();
                    $query = "select count(*) as total,vote_id from $this->_tablename  where  create_date >= '$created_date' and create_date <= '$end_date' and status2=0 ";
                    if($is_actived!=null){
                        $query .=" and is_actived = $is_actived";
                    }
                    if($cated_id!=null){
                        $query .=" and cated_id IN ($cated_id)";
                    }
                    $query .=" group by vote_id";
//                    var_dump($query);//exit();
                    $result = $db->fetchAll($query);
                        if(!is_null($result) && is_array($result))
                            {
                                    $cache->setCache($key, $result);
                            }
                    }
                return $result;
        }
        public function count_apple($created_date="", $end_date="",$is_actived=""){
            $c_day = date('Y-m-d',  strtotime($created_date));
            $e_day = date('Y-m-d',  strtotime($end_date));
                $cache = GlobalCache::getCacheInstance('ws');
		$key = "count_apple.$this->_tablename.$c_day.$e_day.$is_actived";
		$result = $cache->getCache($key);
//                $result = FALSE;
		if($result === FALSE)
		{
                    $db = $this->getDbConnection();
                    $query = "select distinct imes,count(*) as total,vote_id from $this->_tablename  where  create_date >= '$created_date' and create_date <= '$end_date' and is_apple = 1 and productsid IN (3,5) ";
                    if($is_actived!=null){
                        $query .=" and is_actived = $is_actived";
                    }
                    $query .=" group by vote_id";
//                    var_dump($query);//exit();
                    $result = $db->fetchAll($query);
                        if(!is_null($result) && is_array($result))
                            {
                                    $cache->setCache($key, $result,300);
                            }
                    }
                return $result;
        }
        public function count_bbmh_by_month($month="", $year="",$strID){
                $cache = GlobalCache::getCacheInstance('ws');
		$key = "count_bbmh_by_month.$this->_tablename.$month.$year.$strID";
		$result = $cache->getCache($key);
                $result = FALSE;
		if($result === FALSE)
		{
                    $db = $this->getDbConnection();
                    $query = "select distinct imes,vote_id,count(*) as total,sum(products_price) as sum from $this->_tablename  where  MONTH(create_date) IN ($month)  and Year(create_date)=$year  and is_actived=1 and productsid =3 and products_id IN ($strID) ";
                    $query .=" group by vote_id";
//                    var_dump($query);exit();
                    $result = $db->fetchAll($query);
                        if(!is_null($result) && is_array($result))
                            {
                                    $cache->setCache($key, $result,300);
                            }
                    }
                return $result;
        }
        public function count_bbmh_by_cateid_month($month="", $year="",$cated_id){
                $cache = GlobalCache::getCacheInstance('ws');
		$key = "count_bbmh_by_cateid_month.$this->_tablename.$month.$year.$cated_id";
		$result = $cache->getCache($key);
                $result = FALSE;
		if($result === FALSE)
		{
                    $db = $this->getDbConnection();
                    $query = "select distinct imes,vote_id,count(*) as total,sum(products_price) as sum from $this->_tablename  where  MONTH(create_date) IN ($month)  and Year(create_date)=$year  and is_actived=1 and cated_id IN ($cated_id) ";
                    $query .=" group by vote_id";
//                    var_dump($query);exit();
                    $result = $db->fetchAll($query);
                        if(!is_null($result) && is_array($result))
                            {
                                    $cache->setCache($key, $result,300);
                            }
                    }
                return $result;
        }
        public function count_bbmh($created_date="", $end_date="",$strID){
            $c_day = date('Y-m-d',  strtotime($created_date));
            $e_day = date('Y-m-d',  strtotime($end_date));
                $cache = GlobalCache::getCacheInstance('ws');
		$key = "count_bbmh.$this->_tablename.$c_day.$e_day.$strID";
		$result = $cache->getCache($key);
                $result = FALSE;
		if($result === FALSE)
		{
                    $db = $this->getDbConnection();
                    $query = "select distinct imes,vote_id,count(*) as total,sum(products_price) as sum from $this->_tablename  where  create_date >= '$created_date' and create_date <= '$end_date'  and is_actived=1 and productsid =3 and products_id IN ($strID)  and status2=0 ";
                    $query .=" group by vote_id";
//                    var_dump($query);exit();
                    $result = $db->fetchAll($query);
                        if(!is_null($result) && is_array($result))
                            {
                                    $cache->setCache($key, $result,300);
                            }
                    }
                return $result;
        }
        public function count_bbmh_by_cateid($created_date="", $end_date="",$cated_id){
            $c_day = date('Y-m-d',  strtotime($created_date));
            $e_day = date('Y-m-d',  strtotime($end_date));
                $cache = GlobalCache::getCacheInstance('ws');
		$key = "count_bbmh_by_cateid.$this->_tablename.$c_day.$e_day.$cated_id";
		$result = $cache->getCache($key);
                $result = FALSE;
		if($result === FALSE)
		{
                    $db = $this->getDbConnection();
                    $query = "select distinct imes,vote_id,count(*) as total,sum(products_price) as sum from $this->_tablename  where  create_date >= '$created_date' and create_date <= '$end_date' and is_actived=1  and cated_id IN ($cated_id) ";
                    $query .=" group by vote_id";
//                    var_dump($query);//exit();
                    $result = $db->fetchAll($query);
                        if(!is_null($result) && is_array($result))
                            {
                                    $cache->setCache($key, $result,300);
                            }
                    }
                return $result;
        }
        public function count_bbmh_by_productsid($created_date="", $end_date="",$productsid,$type=0){
            $c_day = date('Y-m-d',  strtotime($created_date));
            $e_day = date('Y-m-d',  strtotime($end_date));
                $cache = GlobalCache::getCacheInstance('ws');
		$key = "count_bbmh_by_productsid.$this->_tablename.$c_day.$e_day.$productsid.$type";
		$result = $cache->getCache($key);
                $result = FALSE; 
		if($result === FALSE)
		{
                    $db = $this->getDbConnection();
                    $query = "select distinct imes,vote_id,count(*) as total,sum(products_price) as sum from $this->_tablename  where  create_date >= '$created_date' and create_date <= '$end_date' and is_actived=1  and productsid IN ($productsid) ";
                    if($type >0){
                        $query .=" and type = $type ";
                    }
                    $query .=" group by vote_id";
//                    var_dump($query);exit();
                    $result = $db->fetchAll($query);
                        if(!is_null($result) && is_array($result))
                            {
                                    $cache->setCache($key, $result,300);
                            }
                    }
                return $result;
        }
        public function count_by_productsid($created_date="", $end_date="",$productsid,$type=0,$is_apple=""){
            $c_day = date('Y-m-d',  strtotime($created_date));
            $e_day = date('Y-m-d',  strtotime($end_date));
                $cache = GlobalCache::getCacheInstance('ws');
		$key = "count_bbmh_by_productsid.$this->_tablename.$c_day.$e_day.$productsid.$type";
		$result = $cache->getCache($key);
                $result = FALSE; 
		if($result === FALSE)
		{
                    $db = $this->getDbConnection();
                    $query = "select distinct imes,vote_id,count(*) as total,sum(products_price) as sum from $this->_tablename  where  create_date >= '$created_date' and create_date <= '$end_date' and is_actived=1  and productsid IN ($productsid) ";
                    if($type >0){
                        $query .=" and type = $type ";
                    }
                    if($is_apple != NULL){
                        $query .=" and is_apple = $is_apple ";
                    }
                    $query .=" group by vote_id";
//                    var_dump($query);exit();
                    $result = $db->fetchAll($query);
                        if(!is_null($result) && is_array($result))
                            {
                                    $cache->setCache($key, $result,300);
                            }
                    }
                return $result;
        }
        public function count_by_cateid($created_date="", $end_date="",$cated_id=0){
            $c_day = date('Y-m-d',  strtotime($created_date));
            $e_day = date('Y-m-d',  strtotime($end_date));
                $cache = GlobalCache::getCacheInstance('ws');
		$key = "count_by_cateid.$this->_tablename.$c_day.$e_day.$cated_id";
		$result = $cache->getCache($key);
//                $result = FALSE;
		if($result === FALSE)
		{
                    $db = $this->getDbConnection();
                    $query = "select distinct imes,count(*) as total,vote_id,sum(products_price) as sum from $this->_tablename  where  create_date >= '$created_date' and create_date <= '$end_date' and is_actived=1 ";
                    if((int)$cated_id >0){
                        $query .=" and cated_id = $cated_id";
                    }
                    $query .=" group by vote_id";
//                    var_dump($query);exit();
                    $result = $db->fetchAll($query);
                        if(!is_null($result) && is_array($result))
                            {
                                    $cache->setCache($key, $result,300);
                            }
                    }
                return $result;
        }
        public function count_apple2($month="", $year=""){
                $cache = GlobalCache::getCacheInstance('ws');
		$key = "count_apple2.$this->_tablename.$month.$year";
		$result = $cache->getCache($key);
//                $result = FALSE;
		if($result === FALSE)
		{
                    $db = $this->getDbConnection();
                    $query = "select distinct imes,count(*) as total,vote_id from $this->_tablename  where  MONTH(create_date) IN ($month)  and Year(create_date)=$year and is_apple = 1 and vote_id >0 and productsid IN (3,5) ";
                    $query .=" group by vote_id";
//                    var_dump($query);//exit();
                    $result = $db->fetchAll($query);
                        if(!is_null($result) && is_array($result))
                            {
                                    $cache->setCache($key, $result,300);
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
                $query = "SELECT productsid,flag,cated_id,  date(create_date) as date, sum(products_price) as total,count(products_name) as sl FROM `users_products` WHERE create_date>='$date_from' AND create_date <='$date_to' and status2=0 ";
                if($actived != null){
                    $query .="  AND is_actived='$actived' ";
                }
                $query .=" GROUP BY  date(create_date),cated_id,flag";
//                var_dump($query);
                $data = array();
                $result = $db->fetchAll($query, $data);
                if(!is_null($result) && is_array($result))
                {
                        $cache->setCache($key, $result,600);
                }
            }
            return $result;
        }
        public function getTotalByCateId($cateid="",$date_from="",$date_to="", $actived=""){
            $day_from =  date("Y-m-d", strtotime($date_from));
            $day_to =  date("Y-m-d", strtotime($date_to));
            $cache = $this->getCacheInstance();
            $key = "getTotalByCateId.$this->_tablename.$cateid.$day_from.$day_to.$actived";
            $result = $cache->getCache($key);
            $result=FALSE;
            if($result === FALSE){
                $db = $this->getDbConnection();
                $query = "SELECT flag,cated_id, vote_id, sum(products_price) as total,count(products_name) as sl FROM `users_products` WHERE create_date>='$date_from' AND create_date <='$date_to' ";
                if($cateid != null){
                    $query .="  AND cated_id= $cateid ";
                }
                if($actived != null){
                    $query .="  AND is_actived= $actived ";
                }
                $query .=" GROUP BY  flag,cated_id";
//                var_dump($query);
                $data = array();
                $result = $db->fetchAll($query, $data);
                if(!is_null($result) && is_array($result))
                {
                        $cache->setCache($key, $result,300);
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
                $query = "SELECT flag,cated_id, vote_id, sum(products_price) as total,count(products_name) as sl FROM `users_products` WHERE create_date>='$date_from' AND create_date <='$date_to'   and status2=0";
                if($actived != null){
                    $query .="  AND is_actived='$actived' ";
                }
                $query .=" GROUP BY  vote_id,cated_id,flag";
//                var_dump($query);
                $data = array();
                $result = $db->fetchAll($query, $data);
                if(!is_null($result) && is_array($result))
                {
                        $cache->setCache($key, $result,600);
                }
            }
            return $result;
        }
        function getListDetailSale2($date_from="",$date_to="", $actived="") {
            $day_from =  date("Y-m-d", strtotime($date_from));
            $day_to =  date("Y-m-d", strtotime($date_to));
            $cache = $this->getCacheInstance();
            $key = "getListDetailSale2.$this->_tablename.$day_from.$day_to.$actived";
            $result = $cache->getCache($key);
            $result=FALSE;
            if($result === FALSE){
                $db = $this->getDbConnection();
                $query = "SELECT flag,productsid,type,is_apple, vote_id, sum(products_price) as total,count(products_name) as sl FROM $this->_tablename WHERE create_date>='$date_from' AND create_date <='$date_to'   and status2=0";
                if($actived != null){
                    $query .="  AND is_actived='$actived' ";
                }
                $query .=" GROUP BY  vote_id,productsid,flag,type,is_apple";
//                var_dump($query);//exit();
                $data = array();
                $result = $db->fetchAll($query, $data);
                if(!is_null($result) && is_array($result))
                {
                        $cache->setCache($key, $result,600);
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
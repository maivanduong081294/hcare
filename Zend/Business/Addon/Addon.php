<?php

class Business_Addon_Addon extends Business_Abstract {

    private $_tablename = 'users_products';
    private static $_instance = null;

    function __construct() {
        
    }

    //public static function
    /**
     * get instance of Business_Addon_Addon
     *
     * @return Business_Addon_Addon
     */
    public static function getInstance() {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Get DB Connection
     *
     * @return Zend_Db_Adapter_Abstract
     */
    private function getDbConnection() {
        $db = Globals::getDbConnection('maindb');
        return $db;
    }

    /**
     * Enter description here...
     *
     * @return Maro_Cache
     */
    private function getCacheInstance() {
        $cache = GlobalCache::getCacheInstance('ws');
        return $cache;
    }
    
    public function get_makho_by_imei_vt($imei, $idr=8) {
		if ($imei=="") {   
			return null;
		}
		$db = $this->getDbConnection();
        $query = "SELECT t1.imei,t2.ma_kho_map as ma_kho FROM `hnam_vt`.`quynhn_order_detail` as t1, `hnam_vt`.`quynhn_productsitem` as t2 where t1.imei='$imei' and t1.itemid = t2.itemid AND t2.idregency=$idr";        
        $result = $db->fetchAll($query);
        return $result[0];
	}
	
	public function isSMCProduct($imei) {
		
		if ($imei=="") {   
			return false;
		}
		$db = $this->getDbConnection();
        $query = "SELECT *  FROM `hnam_vt`.`quynhn_order_detail` as t1, `hnam_vt`.`quynhn_productsitem` as t2, `hnam_vt`.`quynhn_cateid` as t3 WHERE t1.imei ='$imei' and t1.itemid = t2.itemid and t2.cateid = t3.id and t3.idregency=8";        
        $result = $db->fetchAll($query);
        if (count($result)>0) {
			return true;
		}
		return false;
	}
	
	public function isPKProduct($imei) {
		
		if ($imei=="") {   
			return false;
		}
		$db = $this->getDbConnection();
        $query = "SELECT *  FROM `hnam_vt`.`quynhn_order_detail` as t1, `hnam_vt`.`quynhn_productsitem` as t2, `hnam_vt`.`quynhn_cateid` as t3 WHERE t1.imei ='$imei' and t1.itemid = t2.itemid and t2.cateid = t3.id and t3.idregency=10";        
        $result = $db->fetchAll($query);
        if (count($result)>0) {
			return true;
		}
		return false;
	}
	
	public function isPKAzuloProduct($imei) {
		
		if ($imei=="") {   
			return false;
		}
		$db = $this->getDbConnection();
        $query = "SELECT *  FROM `hnam_vt`.`quynhn_order_detail` as t1, `hnam_vt`.`quynhn_productsitem` as t2, `hnam_vt`.`quynhn_cateid` as t3 WHERE t1.imei ='$imei' and t1.itemid = t2.itemid and t2.cateid = t3.id and t3.idregency=12";        
        $result = $db->fetchAll($query);
        if (count($result)>0) {
			return true;
		}
		return false;
	}
	
	public function isPKGenshaiProduct($imei) {
		
		if ($imei=="") {   
			return false;
		}
		$db = $this->getDbConnection();
        $query = "SELECT *  FROM `hnam_vt`.`quynhn_order_detail` as t1, `hnam_vt`.`quynhn_productsitem` as t2, `hnam_vt`.`quynhn_cateid` as t3 WHERE t1.imei ='$imei' and t1.itemid = t2.itemid and t2.cateid = t3.id and t3.idregency=13";        
        $result = $db->fetchAll($query);
        if (count($result)>0) {
			return true;
		}
		return false;
	}
    
    public function getListByID($id) {
        $db = $this->getDbConnection();
        $query = "select * from " . $this->_tablename . " where id_addon_user IN($id)";        
        $result = $db->fetchAll($query);
        return $result;
    }
    
    public function getBillResale($from, $to, $offset=0, $records=100) {
        $db = $this->getDbConnection();
        $query = "select * from " . $this->_tablename . " where create_date >= '$from' and create_date <= '$to' and parentid_outbill > 0 LIMIT $offset, $records";        
        $result = $db->fetchAll($query);
        return $result;
    }
    
    public function getSaleTotal($created_day="", $end_day="",$is_actived=1)
    {
        $cache = $this->getCacheInstance();
        $key = md5("salebystoret.$created_day.$end_day.$is_actived");
        $result = $cache->getCache($key);
//        $result=false;
        if($result === FALSE)
        {
            $db = $this->getDbConnection();
                $query  = "SELECT is_apple,type,productsid,flag, (sum(products_price)-sum(reduction_money)-sum(money_voucher)-sum(bonus_tech)) as sum, count(products_id) as countp,sum(reduction_money) as reduction_money,sum(money_voucher) as money_voucher FROM $this->_tablename WHERE 1=1 ";                                                
            if($created_day != null){
                $query .=" and create_date >=  '$created_day'";
            }
            if($end_day != null){
                $query .="  and create_date <= '$end_day'";
            }
            if($is_actived != null){
                $query .="  and is_actived = $is_actived";
            }
            $query .="  and status2 = 0";
            $query .= " group by productsid,flag,type,is_apple";  
//            var_dump($query);exit();
            $result = $db->fetchAll($query);
			
            if(!is_null($result) && is_array($result))
            {
                    $cache->setCache($key, $result,60*10);
            }
        }
        return $result;		
    }
    
    public function getSaleTotalByStore($created_day="", $end_day="",$is_actived=1, $flag=0, $ids="", $offset=0, $records=40, $pids=0)
    {
        $cache = $this->getCacheInstance();
        $key = md5("salebystoreu.$created_day.$end_day.$is_actived.$offset.$records.$flag.$ids.$pids");
        $result = $cache->getCache($key);
//        $result=false;
        if($result === FALSE)
        {
            $db = $this->getDbConnection();
            if ($ids != "") {
                $query  = "SELECT products_id as itemid, count(products_id) as countp FROM $this->_tablename WHERE 1=1 AND cated_id IN ($ids)";                                                
            } else {
                $query  = "SELECT products_id as itemid, count(products_id) as countp FROM $this->_tablename WHERE 1=1 ";                                                                    
            }
            if ($flag > 0) {
                $query .= " AND flag= $flag";
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
            $query .="  and status2 = 0";
            
            if ($vote_id>0) {
                $query .="  and vote_id IN('$vote_id')";
            }
			
			if ($pids > 0) {
				$query .="  and productsid = $pids";
			}
			
            $query .= " group by products_id";                
            $query .= " order by countp DESC";                
            $query .= " LIMIT $offset, $records"; 

            
            $result = $db->fetchAll($query);
            if(!is_null($result) && is_array($result))
            {
                    $cache->setCache($key, $result,60*10);
            }
        }
        return $result;		
    }
    
    public function getSaleTotalByStore2($created_day="", $end_day="",$is_actived=1, $flag=0, $pids="", $offset=0, $records=40)
    {
        $cache = $this->getCacheInstance();
        $key = md5("salebystoreu2.$created_day.$end_day.$is_actived.$offset.$records.$flag.$pids");
        $result = $cache->getCache($key);
//        $result=false;
        if($result === FALSE)
        {
            $db = $this->getDbConnection();
            if ($pids != "") {
                $query  = "SELECT products_id as itemid, count(products_id) as countp FROM $this->_tablename WHERE 1=1 AND products_id = $pids";                                                
            } else {
                $query  = "SELECT products_id as itemid, count(products_id) as countp FROM $this->_tablename WHERE 1=1 ";                                                                    
            }
            if ($flag > 0) {
                $query .= " AND flag= $flag";
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
            $query .=" and status2 = 0";
			
            $query .= " group by products_id";                
            $query .= " order by countp DESC";                
            $query .= " LIMIT $offset, $records"; 

            
            $result = $db->fetchAll($query);
            if(!is_null($result) && is_array($result))
            {
                    $cache->setCache($key, $result,60*10);
            }
        }
        return $result;		
    }

    public function getSaleTotalByStoreSum($is_actived=1, $flag=0, $ids="", $list_itemid)
    {
        $cache = $this->getCacheInstance();
        $key = md5("salebystoresum.$is_actived.$flag.$ids.$list_itemid");
        $result = $cache->getCache($key);
//        $result=false;
        if($result === FALSE)
        {
            $db = $this->getDbConnection();
            if ($ids != "") {
                $query  = "SELECT products_id as itemid, count(products_id) as countp FROM $this->_tablename WHERE 1=1 AND cated_id IN ($ids) AND products_id in (".$list_itemid.")";                                                
            } else {
                $query  = "SELECT products_id as itemid, count(products_id) as countp FROM $this->_tablename WHERE 1=1 AND products_id in (".$list_itemid.")";                                                                    
            }
            if ($flag > 0) {
                $query .= " AND flag= $flag";
            }
            if($is_actived != null){
                $query .="  and is_actived = $is_actived";
            }
            $query .="  and status2 = 0";                      
            $query .= " group by products_id";                
            $query .= " order by countp DESC";                
 
            $result = $db->fetchAll($query);
            if(!is_null($result) && is_array($result))
            {
                    $cache->setCache($key, $result,60*10);
            }
        }
        return $result;     
    }
    
    public function getSaleTotalByStoreSum2($is_actived=1, $flag=0, $list_itemid)
    {
        $cache = $this->getCacheInstance();
        $key = md5("salebystoresum.$is_actived.$flag.$list_itemid");
        $result = $cache->getCache($key);
//        $result=false;
        if($result === FALSE)
        {
            $db = $this->getDbConnection();
            $query  = "SELECT products_id as itemid, count(products_id) as countp FROM $this->_tablename WHERE 1=1 AND products_id in (".$list_itemid.")";                                                                    
            if ($flag > 0) {
                $query .= " AND flag= $flag";
            }
            if($is_actived != null){
                $query .="  and is_actived = $is_actived";
            }
            $query .= " and status2 = 0";     						
            $query .= " group by products_id";                
            $query .= " order by countp DESC";                
 
            $result = $db->fetchAll($query);
            if(!is_null($result) && is_array($result))
            {
                    $cache->setCache($key, $result,60*10);
            }
        }
        return $result;     
    }
    
    public function getSaleByStore($created_day="", $end_day="",$is_actived=1, $vote_id=0)
    {
        $cache = $this->getCacheInstance();
        $key = md5("salebystore.$created_day.$end_day.$is_actived.$vote_id");
        $result = $cache->getCache($key);
        $result=false;
        if($result === FALSE)
        {
            $db = $this->getDbConnection();
            if ($vote_id>0) {
                $query  = "SELECT is_apple, type,productsid, flag, (sum(products_price)-sum(reduction_money)-sum(money_voucher)-sum(bonus_tech)) as sum, count(products_id) as countp FROM $this->_tablename WHERE 1=1 ";                                
//                $query  = "SELECT is_apple, type,productsid, flag, (sum(products_price)-sum(bonus_tech)) as sum, count(products_id) as countp,sum(reduction_money) as reduction_money,sum(money_voucher) as money_voucher FROM $this->_tablename WHERE 1=1 ";                                
            } else {
                $query  = "SELECT is_apple, type,productsid, flag, (sum(products_price)-sum(reduction_money)-sum(money_voucher)-sum(bonus_tech)) as sum, count(products_id) as countp FROM $this->_tablename WHERE 1=1 ";                                                
//                $query  = "SELECT is_apple, type,productsid, flag, (sum(products_price)-sum(bonus_tech)) as sum, count(products_id) as countp,sum(reduction_money) as reduction_money,sum(money_voucher) as money_voucher FROM $this->_tablename WHERE 1=1 ";                                                
            }
//            $query  = "SELECT vote_id, flag,cated_id as cate_id, sum(products_price) as sum, count(products_price) as countp,sum(reduction_money) as reduction_money,sum(money_voucher) as money_voucher FROM $this->_tablename WHERE 1=1 ";                
            if($created_day != null){
                $query .=" and create_date >=  '$created_day'";
            }
            if($end_day != null){
                $query .="  and create_date <= '$end_day'";
            }
            if($is_actived != null){
                $query .="  and is_actived = $is_actived";
            }
            $query .="  and status2 = 0";
            if ($vote_id>0) {
                $query .="  and vote_id IN('$vote_id')";
                $query .= " group by productsid,flag,type,is_apple";
            } else {
                $query .= " group by productsid,flag,type,is_apple";                
            }
			//var_dump($query);die();
            $result = $db->fetchAll($query);
            if(!is_null($result) && is_array($result))
            {
                    $cache->setCache($key, $result,60*10);
            }
        }
        return $result;		
    }
    
    public function getSaleByStoreAll($created_day="", $end_day="",$is_actived=1)
    {
        $cache = $this->getCacheInstance();
        $key = md5("salebystore2.$created_day.$end_day.$is_actived.$vote_id");
        $result = $cache->getCache($key);
//        $result=false;
        if($result === FALSE)
        {
            $db = $this->getDbConnection();
            $query  = "SELECT flag,cated_id as cate_id, sum(products_price) as sum, count(products_id) as countp,sum(reduction_money) as reduction_money,sum(money_voucher) as money_voucher FROM $this->_tablename WHERE 1=1 ";                                                

            if($created_day != null){
                $query .=" and create_date >=  '$created_day'";
            }
            if($end_day != null){
                $query .="  and create_date <= '$end_day'";
            }
            if($is_actived != null){
                $query .="  and is_actived = $is_actived";
            }
            $query .="  and status2 = 0";
            $query .= " group by flag, cated_id";                
            $result = $db->fetchAll($query);
            if(!is_null($result) && is_array($result))
            {
                    $cache->setCache($key, $result,60*10);
            }
        }
        return $result;		
    }
    
    public function getSaleByStore2($created_day="", $end_day="",$is_actived=1, $vote_id=0)
    {
        $cache = $this->getCacheInstance();
        $key = md5("salebystore2.$created_day.$end_day.$is_actived.$vote_id");
        $result = $cache->getCache($key);
//        $result=false;
        if($result === FALSE)
        {
            $db = $this->getDbConnection();
            if ($vote_id>0) {
                $query  = "SELECT flag,cated_id as cate_id, sum(products_price) as sum, count(products_id) as countp,sum(reduction_money) as reduction_money,sum(money_voucher) as money_voucher FROM $this->_tablename WHERE 1=1 ";                                
            } else {
                $query  = "SELECT vote_id, flag,cated_id as cate_id, sum(products_price) as sum, count(products_id) as countp,sum(reduction_money) as reduction_money,sum(money_voucher) as money_voucher FROM $this->_tablename WHERE 1=1 ";                                                
            }
//            $query  = "SELECT vote_id, flag,cated_id as cate_id, sum(products_price) as sum, count(products_price) as countp,sum(reduction_money) as reduction_money,sum(money_voucher) as money_voucher FROM $this->_tablename WHERE 1=1 ";                
            if($created_day != null){
                $query .=" and create_date >=  '$created_day'";
            }
            if($end_day != null){
                $query .="  and create_date <= '$end_day'";
            }
            if($is_actived != null){
                $query .="  and is_actived = $is_actived";
            }
            $query .="  and status2 = 0";
            if ($vote_id>0) {
                $query .="  and vote_id IN('$vote_id')";
                $query .= " group by vote_id, flag, cated_id";
            } else {
                $query .= " group by flag, cated_id";                
            }
             $result = $db->fetchAll($query);
            if(!is_null($result) && is_array($result))
            {
                    $cache->setCache($key, $result,60*10);
            }
        }
        return $result;		
    }

    function getSaleByHour($date, $voteids = null, $actived = 1) {
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
    
    function getSaleByHour2($dateFrom, $dateTo, $voteids = null, $actived = 1) {
        $dateTo .= " 23:59:59"; 

        $db = $this->getDbConnection();
        if ($voteids != null) {
            $query = "SELECT vote_id, hour(create_date) as hour, date(create_date) as date, count(*) as total FROM `users_products` WHERE create_date > '$dateFrom' AND create_date <= '$dateTo' and vote_id IN($voteids) GROUP BY vote_id, hour(create_date), date(create_date)";
        } else {
            $query = "SELECT vote_id, hour(create_date) as hour, date(create_date) as date, count(*) as total FROM `users_products` WHERE create_date > '$dateFrom' AND create_date <= '$dateTo' GROUP BY vote_id, hour(create_date), date(create_date)";
        }
        $data = array();
        $result = $db->fetchAll($query, $data);
        return $result;
    }
    
    function getSaleByDate($fromDate, $toDate, $voteids = null, $actived = 1) {

        $db = $this->getDbConnection();
        if ($voteids == null) {
            $query = "SELECT vote_id, hour(create_date) as hour, date(create_date) as date, count(*) as total FROM `users_products` WHERE create_date >= $fromDate AND create_date <= $toDate AND is_actived='$actived' GROUP BY vote_id, hour(create_date), date(create_date)";
        } else {
            $query = "SELECT vote_id, hour(create_date) as hour, date(create_date) as date, count(*) as total FROM `users_products` WHERE create_date >= $fromDate AND create_date <= $toDate AND is_actived='$actived' AND vote_id IN($voteids) GROUP BY vote_id, hour(create_date), date(create_date)";
        }
        $data = array();
        $result = $db->fetchAll($query, $data);
        return $result;
    }

    function getSaleByPrice($dateFrom, $dateTo, $voteids = null, $actived = 1, $flag = 1) {
        $db = $this->getDbConnection();
        if ($voteids == null) {
            $query = "SELECT vote_id, products_price_cost as cost FROM `users_products` WHERE flag= '$flag' AND is_actived='$actived' AND create_date >= '$dateFrom' AND create_date < '$dateTo'";
        } else {
            $query = "SELECT vote_id, products_price_cost as cost FROM `users_products` WHERE flag= '$flag' AND  is_actived='$actived' AND vote_id IN($voteids) AND create_date >= '$dateFrom' AND create_date < '$dateTo'";
        }

        $data = array();
        $result = $db->fetchAll($query, $data);
        return $result;
    }
    
    function getSaleByItem($dateFrom, $dateTo, $itemid, $voteids="", $actived=1) {
            $db = $this->getDbConnection();
            if ($voteids!="") {
                $where = "vote_id IN ($voteids) AND";
            } else {
                $where = "";
            }
            if ($itemid >0) {
                $query = "SELECT vote_id, count(products_id) as total FROM `users_products` WHERE $where "
                        . "is_actived='$actived'  AND create_date >= '$dateFrom' AND create_date < '$dateTo' "
                        . "AND products_id=$itemid GROUP BY vote_id";
//                echo "<pre>";
//                var_dump($query);
//                die();
                $data = array();
                $result = $db->fetchAll($query, $data);
                return $result;
            }
            return array();
        }
        
        function getSaleByItemAndDate($dateFrom, $dateTo, $itemid, $voteids="", $actived=1) {
            $db = $this->getDbConnection();
            if ($voteids!="") {
                $where = "vote_id IN ($voteids) AND";
            } else {
                $where = "";
            }
            if ($itemid >0) {
                $query = "SELECT count(products_id) as total, date(create_date) as dt FROM `users_products` WHERE $where "
                        . "is_actived='$actived' "
                        . "AND create_date >= '$dateFrom' AND create_date < '$dateTo' AND "
                        . "products_id=$itemid GROUP BY date(create_date) ORDER BY create_date ASC";
                $data = array();
                $result = $db->fetchAll($query, $data);
                return $result;
            }
            return array();
        }
		
		public function execute($sql) {
			$db = $this->getDbConnection();    
			if ($sql=="") {
				return;
			}
			try {
				$db->query($sql);
			} catch (Exception $e) {
				return $e->getMessage();
			}				
		}

		public function getListTonKhoDuKien($strVoteId, $strItemId) {
		//	var_dump($strVoteId, $strItemId);die();
			$db = $this->getDbConnection();
            if ($strVoteId != "") {  
				$wheres[] = "voteid IN ($strVoteId)";
			}  
			 
            if ($strItemId != "") {
				$wheres[] = "itemid IN ($strItemId)";
				$wheres[] = "enabled = 1";
				$where= implode(" AND ", $wheres);
				
                $query = "SELECT * FROM `addon_tonkhodukien` WHERE $where ";
                $data = array();
				
                $result = $db->fetchAll($query, $data);
                return $result;
            }
            return array();
		}
		 
		public function getActivedStore() {
		//	var_dump($strVoteId, $strItemId);die();
			$db = $this->getDbConnection();
            			 
			$wheres[] = "enabled = 1";
			$where= implode(" AND ", $wheres);
			
			$query = "SELECT distinct(voteid) FROM `addon_tonkhodukien` WHERE $where ";
			$data = array();
			
			$result = $db->fetchAll($query, $data);
			return $result;
		
		}
		
}

?>

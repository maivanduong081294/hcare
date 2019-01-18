<?php

class Business_Addon_Report extends Business_Abstract {

    private $_tablename = 'users_products';
    private static $_instance = null;

    function __construct() {
        
    }

    //public static function
    /**
     * get instance of Business_Addon_Report
     *
     * @return Business_Addon_Report
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
    private function getDbConnection($name) {
		if ($name=="") {
			$name="hnam_app";
		}
		
        $db = Globals::getDbConnection($name);
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
    
    public function getLechMoney($month, $year) {
		$db = $this->getDbConnection();
        $query = "	SELECT p.id,p.imei,p.price,p.enabled,p.isync,p.datetime,p.billid,pos.price*1000 as price2 FROM `hnam_live`.`hnam_purchase` as p, `hnam_vt`.quynhn_pos as pos , `hnam_vt`.`quynhn_cateid` as cat
					WHERE p.enabled=1 and p.isync=2 and p.billid>0
					and p.billid = pos.billid
					and p.imei = pos.imei
					and pos.cateid = cat.id
					and p.price <> pos.price*1000 
					and p.datetime >= '$year-$month-01'
					and p.datetime < '$year-$month-31 23:59:59'
				";        		

        $result = $db->fetchAll($query);				
        return $result;
	}
	
    public function getList() {
		
		$db = $this->getDbConnection();
        $query = "SELECT t1.*, t2.itemid,count(t2.ma_lo) as tongton FROM `fast_price` as t1, fast_tonkhoimei as t2 where t1.ma_vt = t2.ma_vt AND t2.ma_kho NOT LIKE '%.DEMO' AND t2.ma_kho NOT LIKE '%.REPR' AND t2.ma_kho NOT LIKE '%.GIFT' AND t2.ma_vt LIKE 'PK%' group by t2.ma_vt,t2.itemid";        		
		//SELECT ma_vt,products_id,products_price,date(create_date) as ngaythang,count(autoid) FROM `users_products` where create_date > '2018-01-01' and is_actived=1 GROUP BY ngaythang,trim(ma_vt)
        $result = $db->fetchAll($query);				
        return $result;
    }
	
	public function getList2($itemids) {
		
		$db = $this->getDbConnection();
        $query = "SELECT DISTINCT (ma_vt),itemid FROM `hnam_app`.`fast_tonkhoimei` where itemid NOT IN ($itemids)";        		
        $result = $db->fetchAll($query);				
        return $result;
    }
	
	public function getIDFromFast() {
		
		$db = $this->getDbConnection();
        $query = "SELECT distinct(itemid) FROM `hnam_app`.`fast_tonkhoimei`";        		
        $result = $db->fetchAll($query);				 
        return $result;
    }
	
	public function getMavtMapping() {
		
		$db = $this->getDbConnection();
        $query = "SELECT distinct(id_material),id_product FROM `hnam_live`.`app_mapping_product`";        				
		//var_dump($query);die();
        $result = $db->fetchAll($query);				
        return $result;
    }
	
	public function getSalePre() {
		
		$db = $this->getDbConnection("maindb");		
		$query = "SELECT ma_vt,products_id,products_price,DATE_FORMAT(create_date,'%m/%Y') as ngaythang,count(autoid) as total FROM users_products where create_date > DATE_FORMAT(LAST_DAY(NOW() - INTERVAL 4 MONTH), '%Y-%m-%d 23:59:59') AND create_date < '".date("Y-m-01")."' and is_actived=1 GROUP BY ngaythang,trim(ma_vt),products_id";		
        $result = $db->fetchAll($query);				
        return $result;
	}
	
	public function getAllIMEI() {
		$db = $this->getDbConnection("hnam_app");		
		$query = "SELECT * FROM `fast_tonkhoimei`";		
        $result = $db->fetchAll($query);				
        return $result;
	}
	
	public function getSaleLastSevenDay($day=7) {
		$day = intval($day);
		$db = $this->getDbConnection("maindb");		
		$query = "SELECT products_name,fullname_addon,phone_addon,ma_bp,create_date FROM users_products WHERE create_date > CURDATE() - INTERVAL $day DAY AND is_actived=1 AND status2=0 AND productsid IN (3,5) ORDER BY create_date desc";		
        $result = $db->fetchAll($query);				
        return $result;
	}
	
    
}

?>

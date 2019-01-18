<?php

class Business_Addon_ProductProperties extends Business_Abstract {

    private $_tablename =  array( 
        'addon_properties'=> 'addon_product_properties',
        'ws_properties'=> 'ws_properties',
        'ws_properties_sub'=> 'ws_properties_sub',
        'ws_faq_properties'=> 'ws_faq_properties',
        'ws_faq_properties_sub'=> 'ws_faq_properties_sub',
        'ws_faq_item'=> 'ws_faq_item',
        'ws_faq_item_properties'=> 'ws_faq_item_properties',
    );
    private static $_instance = null;
    const EXPIRED = 3000; //secs
    function __construct() {
        
    }

    /**
     * get instance of Business_Addon_ProductProperties
     *
     * @return Business_Addon_ProductProperties
     */
    public static function getInstance() {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * get Zend_Db connection
     *
     * @return Zend_Db_Adapter_Abstract
     */
    function getDbConnection() {
        $db = Globals::getDbConnection('maindb', false);
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
    

    public function updateP($id, $data,$prefix) {
    
        $db = $this->getDbConnection();
        $where = array();
        $where[] = "id = '" . parent::adaptSQL($id)."'";

        $result = $db->update($this->_tablename[$prefix], $data, $where);
        if($result)
        {
            return $result;
        }
        else
            return 0;
        
    }


    public function update($itemid,$pid, $data,$prefix) {

        $db = $this->getDbConnection();
        $where = array();
        $where[] = "itemid = '" . parent::adaptSQL($itemid) . "' AND pid=$pid";
        try {
            $result = $db->update($this->_tablename[$prefix], $data, $where);
            $cache = $this->getCacheInstance();
            $cache->flushAll();
            return $result;
        } catch (Exception $e) {
            return 0;
        }
    }

    public function getProductsProperties($subid,$start,$end) {
        $db = $this->getDbConnection();
        if($subid > 0) {
            $andProperties = " p.products_id in (select itemid from addon_product_properties where subid like '%$subid%' ) and create_date >= '$start' and create_date <= '$end'";
            $query = "SELECT products_name,products_id,vote_id,(sum(products_price)-sum(reduction_money)-sum(money_voucher)-sum(bonus_tech)) as sum, count(*) as total FROM users_products as p  WHERE $andProperties and is_actived = 1";
        }
        else {
            $andProperties = " p.products_id in (select itemid from ws_productsitem where productsid = 4) and create_date >= '$start' and create_date <= '$end'";
            $query = "SELECT products_name,products_id,vote_id,(sum(products_price)-sum(reduction_money)-sum(money_voucher)-sum(bonus_tech)) as sum, count(*) as total FROM users_products as p  WHERE $andProperties and is_actived = 1";
        }
        $query .= " group by vote_id,products_id";
        //var_dump($query);die();
        $result = $db->fetchAll($query);
        return $result;
    }

    public function getNuocHoaProperties($subid,$start,$end) {
        $db = $this->getDbConnection();
        if($subid > 0) {
            $andProperties = " p.products_id in (select itemid from addon_product_properties where subid like '%$subid%' ) and create_date >= '$start' and create_date <= '$end'";
            $query = "SELECT products_name,products_id,vote_id,(sum(products_price)-sum(reduction_money)-sum(money_voucher)-sum(bonus_tech)) as sum, count(*) as total FROM users_products as p  WHERE $andProperties and is_actived = 1";
        }
        else {
            $andProperties = " p.products_id in (select itemid from ws_productsitem where productsid = 9) and create_date >= '$start' and create_date <= '$end'";
            $query = "SELECT products_name,products_id,vote_id,(sum(products_price)-sum(reduction_money)-sum(money_voucher)-sum(bonus_tech)) as sum, count(*) as total FROM users_products as p  WHERE $andProperties and is_actived = 1";
        }
        $query .= " group by vote_id,products_id";
        //var_dump($query);die();
        $result = $db->fetchAll($query);
        return $result;
    }

    public function insert($data,$prefix) {
        $db = $this->getDbConnection();
        $result = $db->insert($this->_tablename[$prefix], $data);
        if($result)
        return $db->lastInsertId($this->_tablename[$prefix]);
        else
           return $result;
    }
    
    public function deleteSub($pid) {
        $db = $this->getDbConnection();
        $where = array();
        $where[] = " pid = " . parent::adaptSQL($pid) . "";
        $result = $db->delete('addon_product_properties', $where);
        return $result;
    }
    
    
    
    public function selectAddonProperties($prefix,$id=0,$w="pid") {
    
        
        $cache = $this->getCacheInstance();
        $key = "selectAddonProperties".$prefix.$id.$w;
        $result  = $cache->getCache($key);
        if($result === false){
        if($id!=0)
           $where ="  WHERE $w=$id " ;
    
        $sql = "SELECT* FROM ". $this->_tablename[$prefix].$where;
        $db = $this->getDbConnection();
    
        $result = $db->fetchAll($sql);
        $cache->setCache($key, $result);
        }      
        return $result;
    }
    
    public function selectChudeOplung($id , $pid) {
    
        
        $cache = $this->getCacheInstance();
        $key = "selectChudeOplung".$id.$pid;
        $result  = $cache->getCache($key);
        if($result === false){
        if($id > 0)
           $where ="  WHERE itemid = $id and pid = $pid " ;
    
        $sql = "SELECT* FROM addon_product_properties".$where;
        $db = $this->getDbConnection();
    
        $result = $db->fetchRow($sql);
        $cache->setCache($key, $result);
        }      
        return $result;
    }
    
    public function selectChudeOplungMore($supid,$supid1) {
    
        
        $cache = $this->getCacheInstance();
        $key = "selectChudeOplungMore".md5($supid,$supid1);
        $result  = $cache->getCache($key);
        
        if($result === false){
        $where ="  WHERE  ( pid = 168 " ;
        foreach ($supid as $key => $value) {
            $andProperties[]=" subid like '%$value%'";
        }  
    
        $sql = "SELECT * FROM addon_product_properties".$where." and ".implode(' or ',$andProperties).")";

        if(count($supid1)>0)
        {
            $where ="  WHERE   pid = 169 " ;
            foreach ($supid1 as $key => $value) {
                $andProperties1[]=" subid like '%$value%'";
            }  
            $sql .= " and itemid in ( SELECT itemid FROM addon_product_properties".$where." and ".implode(' or ',$andProperties1).")";

        }

    
        $db = $this->getDbConnection();
    
        $result = $db->fetchAll($sql);
        $cache->setCache($key, $result);
        }      
        return $result;
    }
    


    public function listOldProduct($pid) {
        
        $cache = $this->getCacheInstance();
        $key = "listOldProduct".$pid;
        $result  = $cache->getCache($key);
        if($result === false){ 
        $sql = "SELECT addon_product_properties.subid , addon_product_properties.itemid , addon_product_properties.pid FROM addon_product_properties INNER JOIN ws_productsitem ON addon_product_properties.itemid=ws_productsitem.itemid WHERE pid=$pid AND onstock != 0 AND enabled = 1 AND cateid IN (53) "; 
        $db = $this->getDbConnection();
    
        $result = $db->fetchAll($sql);
        $cache->setCache($key, $result);
        }
        
        return $result;
    }
    
    
        public function checkProperties($itemid,$pid) {
        $sql = "SELECT * FROM addon_product_properties WHERE pid =$pid and itemid = $itemid"; 
         $db = $this->getDbConnection();        
         $result = $db->fetchRow($sql);
         return $result;
    }

     public function checkPropertiesList($itemid,$pid) {
        $sql = "SELECT * FROM addon_product_properties WHERE pid =$pid and itemid = $itemid and subid != '0' "; 
         $db = $this->getDbConnection();        
         $result = $db->fetchAll($sql);
         return $result;
    }
    
        public function selectFAQDetail($id) {
        $cache = $this->getCacheInstance();
        $key = "FAQ-detail-$id";
        $result = $cache->getCache($key);  
        $result=false;
        if ($result === FALSE) {
        $sql = "SELECT * FROM ws_faq_item where id=$id ";         
        $db = $this->getDbConnection();        
        $result = $db->fetchRow($sql);
            if (!is_null($result) && is_array($result)) {
            $cache->setCache($key, $result,self::EXPIRED);
           }
        }
        return $result;
        
    }
    
    
    public function selectALLFAQ($limit,$to,$active,$filter,$featured) {
        if($active!=-1 )
        {
            $andActive=" and p.enabled=$active ";
        }
        if($featured!=-1)
        {
            $andFeatured=" and p.featured=$featured ";
        }
        
        $cache = $this->getCacheInstance();
        $key = "FAQ-$limit-$to-$active-$filter-$featured";
        $result = $cache->getCache($key);  
        $result=false;
        if ($result === FALSE) {
        $limit = " limit $limit OFFSET $to";
        if($filter>0)
        $sql = "SELECT p.* FROM ws_faq_item as p inner join ws_faq_item_properties as c on p.id=c.itemid where 1=1 $andActive $andFeatured  and c.subid like  '%\"$filter\"%' order by p.id DESC  $limit";      
        else
        $sql = "SELECT p.* FROM ws_faq_item as p where 1=1 $andActive $andFeatured  order by p.id DESC $limit";  
        
        $db = $this->getDbConnection();        
        $result = $db->fetchAll($sql);
            if (!is_null($result) && is_array($result)) {
            $cache->setCache($key, $result,self::EXPIRED);
           }
        }
        return $result;
        
    }
        public function totalALLFAQ($active,$filter,$featured) {
        if($active!=-1)
        {
            $andActive=" and p.enabled=$active ";
        }
        if($featured!=-1)
        {
            $andFeatured=" and p.featured=$featured ";
        }        
        
        $cache = $this->getCacheInstance();
        $key = "FAQ-total-$active-$filter-$featured";
        $result = $cache->getCache($key);  
        $result=false;
        if ($result === FALSE) {
       if($filter>0)
        $sql = "SELECT count(*)  as count   FROM ws_faq_item as p inner join ws_faq_item_properties as c on p.id=c.itemid where 1=1 $andActive $andFeatured and c.subid like  '%\"$filter\"%' ";      
        else
        $sql = "SELECT count(*) as count FROM ws_faq_item as p  where 1=1 $andActive $andFeatured ";         
        $db = $this->getDbConnection();       
        $result = $db->fetchRow($sql);
            if (!is_null($result) && is_array($result)) {
            $cache->setCache($key, $result['count'],self::EXPIRED);
           }
        }
        return $result['count'];
        
    }
    
    
    
    public function selectProperties($prefix,$id=0,$w="pid",$oder="") {
        if($id!=0)
            $where =" WHERE $w=$id order by stt" ;
        if(is_array($oder))
            $oder=" order by ".key($oder)." ". $oder[key($oder)]."  ";
           $sql = "SELECT * FROM ". $this->_tablename[$prefix].$where.$oder; 
        $db = $this->getDbConnection();
        
        $result = $db->fetchAll($sql);
        return $result;
    }
    




    
      public function countBaiviet($pid,$subid) {
        
        $sql = "SELECT count(*) as count FROM ws_faq_item_properties where pid=$pid and  subid like  '%\"$subid\"%'"; 
        $db = $this->getDbConnection();
        $result = $db->fetchRow($sql);
        return $result['count'];
    }
    
     
        public function getPropertiesByGroup($pid,$group=0) {

        $sql = "SELECT subid FROM `addon_product_properties` as a inner join `ws_productsitem` as p on a.itemid=p.itemid  where p.enabled=1  and a.pid=$pid and a.itemid in ( SELECT itemid FROM `addon_product_properties` where subid like '%\"$group\"%') group by a.subid";
        $db = $this->getDbConnection();
    
        $cache = $this->getCacheInstance();
        $key = "properties.group.list.$pid.$group";
        $result = $cache->getCache($key);
        if( $_GET['d'] == 10) {
            $result = FALSE;
        }
        if( $result === FALSE)
        {
        $result = $db->fetchAll($sql);
        if(!is_null($result) && is_array($result))
        {
            $cache->setCache($key, $result);
        }
        }
        return $result;
    }   
    
    
        public function getPropertiesByPid($pid=0,$list='') {

        if($list !='')
            $andList ="  and id in ($list) " ; 

        if($pid!=0)
            $where =" WHERE pid=$pid and stt<1000  $andList order by stt ASC" ;


        $sql = "SELECT * FROM ". $this->_tablename['ws_properties_sub'].$where;
        $db = $this->getDbConnection();
    
        $cache = $this->getCacheInstance();
        $key = "properties.list.$pid".md5($list);
        $result = $cache->getCache($key);
        if( $_GET['d'] == 10) {
            $result = FALSE;
        }
        if( $result === FALSE)
        {
        $result = $db->fetchAll($sql);
        if(!is_null($result) && is_array($result))
        {
            $cache->setCache($key, $result);
        }
        }
        return $result;
    }
    
    public function getPropertiesBySubid($subid=0) {
        $cache = $this->getCacheInstance();
        $key = md5("properties.subid.$subid");
		$result = $cache->getCache($key);
		if( $result === FALSE){
			$where =" WHERE subid like '%\"$subid\"%'" ;
			$sql = "SELECT title FROM addon_product_properties as p inner join ws_productsitem as c on p.itemid = c.itemid ".$where;
			$db = $this->getDbConnection();
			$result = $db->fetchAll($sql);
			$cache->setCache($key, $result);
			
		}
        return $result;
    }
    

    public function delete($id,$prefix,$w="id") {
        $db = $this->getDbConnection();
        $where = array();
        $where[] = "$w in (" . parent::adaptSQL($id) . ")";
        $result = $db->delete($this->_tablename[$prefix], $where);
        return $result;
    }
    
    public function execute($itemid,$colorid) {
	$where = "itemid = $itemid and colorid = $colorid";
	$sql = "DELETE FROM ".$this->_tablename." WHERE ".$where;
	$db = $this->getDbConnection();
	$ret = $db->query($sql);
	return $ret;
    }
    
    public function execute1($sql) {
	$db = $this->getDbConnection();
	$ret = $db->query($sql);
	return $ret;
    }

}

?>
<?php

class Business_Addon_PromotionFastProduct extends Business_Abstract {

    private $_tablename = 'addon_promotion_fast_product';

    const KEY_LIST = 'addon_promotion_fast_product.list.%s';   //key of list.questionid
    const KEY_DETAIL = 'addon_promotion_fast_product.detail.%s';  //key of detail.id

    private static $_instance = null;

    function __construct() {
        
    }

    //public static function
    /**
     * get instance of Business_Addon_PromotionFastProduct
     *
     * @return Business_Addon_PromotionFastProduct
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
        $cache = GlobalCache::getCacheInstance('event');
        return $cache;
    }
    public function get_list_by_3g($strID,$itemid_title){
        $db = $this->getDbConnection();
        $query = "SELECT b.itemid,a.itemid as idfast,b.product_ids FROM addon_promotion_fast_product b,`addon_promotion_fast` a WHERE a.enabled =1 and b.enabled=1 and a.itemid = b.idfast and itemid_title IN ($itemid_title) and b.product_ids IN ($strID);";
        $result = $db->fetchAll($query);
        return $result;
    }
    public function get_list_san($product_ids,$ictkm){
        $db = $this->getDbConnection();
        $query = "SELECT *, sum(return_price) as sum FROM `addon_promotion_fast_product` t1,addon_promotion_fast t2 where t1.idfast=t2.itemid and t1.enabled=1 and t2.enabled=1 and t1.ictkm>=265 and t1.ictkm<=344 and t2.type=6";
        if((int)$product_ids>0){
           $query .=" and t1.product_ids = $product_ids"; 
        }
        if((int)$ictkm>0){
           $query .=" and t1.ictkm = $ictkm"; 
        }
        $query .=" group by product_ids,ictkm";
        $result = $db->fetchAll($query);
        return $result;
    }
    public function get_detail_by_itemid_ctkm($product_ids,$ictkm){
        $db = $this->getDbConnection();
        $query = "SELECT * FROM `addon_promotion_fast_product` where  enabled=1 and ictkm>=265 and ictkm<=344";
        if((int)$product_ids>0){
           $query .=" and product_ids = $product_ids"; 
        }
        if((int)$ictkm>0){
           $query .=" and t1.ictkm = $ictkm"; 
        }
        $result = $db->fetchAll($query);
        return $result;
    }
     public function getAllList($keywork="",$enabled){
        $cache = $this->getCacheInstance();
        $key = "getAllList".$this->_tablename.$keywork.$enabled;
//        var_dump($result);exit();
        $result = FALSE;
        if ($result === FALSE) {
            $db = $this->getDbConnection();
            $query = "SELECT * FROM  $this->_tablename where enabled = $enabled ";
            if($keywork !=null){
                $query .=" and (product_ids = '$keywork') ";
            }
            $query.=" ORDER BY itemid DESC";
            $query.= " LIMIT 500";
//            var_dump($query);exit();
            $result = $db->fetchAll($query);
            if($keywork == ""){
                $cache->setCache($key, $result);
                }
        }
        return $result;
    }
    public function getListProductFast($productid,$idfast){
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " where enabled =1 and product_ids IN ($productid) and idfast IN ($idfast)";
        $result = $db->fetchAll($query);
        return $result;
    }
    public function get_list_by_idfast($idfast){
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " where enabled =1 and idfast IN ($idfast)  ORDER BY ictkm ASC";
        $result = $db->fetchAll($query);
        return $result;
    }

    public function getList($keywork="",$offset=1, $records=30,$enabled){
        $cache = $this->getCacheInstance();
        $key = "getList".  $this->_tablename.$keywork.$offset.$records.$enabled;
        $result = $cache->getCache($key);
        $result = FALSE;
        if ($result === FALSE) {
            $db = $this->getDbConnection();
            $query = "SELECT * FROM  $this->_tablename where enabled = $enabled "; 
            if($keywork !=null){
                $query .=" and (product_ids = '$keywork')";
            }
            $query.=" ORDER BY ictkm ASC";
            $query .= " LIMIT $records OFFSET $offset "; 
//            var_dump($query);exit();
            $result = $db->fetchAll($query);
            $cache->setCache($key, $result);
            
        }
        return $result;
    }
    public function get_list_by_itemid($keywork="",$offset=1, $records=30,$enabled){
        $cache = $this->getCacheInstance();
        $key = "get_list_by_itemid".  $this->_tablename.$keywork.$offset.$records.$enabled;
        $result = $cache->getCache($key);
        $result = FALSE;
        if ($result === FALSE) {
            $db = $this->getDbConnection();
            $query = "SELECT * FROM  $this->_tablename where enabled = $enabled ";
            if($keywork !=null){
                $query .=" and (itemid_title = '$keywork')";
            }
            $query.=" ORDER BY itemid DESC";
            $query .= " LIMIT $records OFFSET $offset ";
//            var_dump($query);exit();
            $result = $db->fetchAll($query);
            $cache->setCache($key, $result);
            
        }
        return $result;
    }
     public function excute($query){
            $db     =  $this->getDbConnection();
            $result = $db->query($query);
            return $result;
        }
    public function getDetail($id) {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " WHERE itemid=?";
        $data = array($id);
        $result = $db->fetchAll($query, $data);
        return $result[0];
    }
    
    public function insert($data)
	{
		$db = $this->getDbConnection();
		$result = $db->insert($this->_tablename,$data);
                if ($result > 0) {
                    $lastid= $db->lastInsertId($this->_tablename); // tra ve id khi them vao
                }
                return $lastid;
	}
    public function update($id,$data) {
        $where = array();
        $where[] = "itemid='" . parent::adaptSQL($id) . "'";
        $db = $this->getDbConnection();
        $result = $db->update($this->_tablename, $data, $where);
    }

    public function delete($id, $qid) {
        //get current menu
        $db = $this->getDbConnection();
        $where = array();
        $where[] = "itemid='" . parent::adaptSQL($id) . "'";
        $result = $db->delete($this->_tablename, $where);
        if ($result > 0) {
            $this->_deleteAllCache($qid, $id);
        }
    }

}

?>

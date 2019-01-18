<?php

class Business_Addon_AddonPromotion extends Business_Abstract {

    private $_tablename = 'addon_user_promotion';

    const KEY_LIST = 'promotion.list.%s';   //key of list.questionid
    const KEY_DETAIL = 'promotion.detail.%s';  //key of detail.id

    private static $_instance = null;

    function __construct() {
        
    }

    //public static function
    /**
     * get instance of Business_Addon_AddonPromotion
     *
     * @return Business_Addon_AddonPromotion
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
    public function get_list_by_product_ids_itemid_title_enabled($id_addon_user,$product_ids,$itemid_title,$enabled){
        $db = $this->getDbConnection();
        $query = "SELECT * FROM $this->_tablename WHERE id_addon_user IN ($id_addon_user) and  product_ids = $product_ids and  itemid_title = $itemid_title and enabled = $enabled";
        $result = $db->fetchAll($query);
        return $result;
    }
    public function get_list_by_trano($start,$end){
        $db = $this->getDbConnection();
        $query = "SELECT * FROM $this->_tablename WHERE ispay=2 and update_ispay >= '$start' and update_ispay < '$end' and bill_no >0 ";
        $result = $db->fetchAll($query);
        return $result;
    }
    public function get_list_by_3g($itemid_title,$id_addon_user){
        $db = $this->getDbConnection();
        $query = "SELECT * FROM $this->_tablename WHERE enabled=2 and type=0 and itemid_title IN ($itemid_title) and  id_addon_user = $id_addon_user";
        $result = $db->fetchAll($query);
        return $result;
    }
    public function get_list_by_sync($id_addon_user,$foresync=0)
	{
            $cache = $this->getCacheInstance('ws');
            $key = "get_list_by_sync.$this->_tablename.$id_addon_user";
            $result = $cache->getCache($key);
            $result= FALSE;
            if($result=== FALSE){
                
               $db = $this->getDbConnection();
               if($foresync ==1){
                   $query = "select * from $this->_tablename where id_addon_user = $id_addon_user and ispay=2 and type=0 and enabled=2 and kmno =1 ";
               }else{
                   $query = "select * from $this->_tablename where id_addon_user = $id_addon_user and ispay=2 and type=0 and enabled=2 and kmno =1 and isync =1";
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
            $result= FALSE;
            if($result=== FALSE){
                
               $db = $this->getDbConnection();
                $query = "select * from $this->_tablename where id_addon_user = $id_addon_user and ispay=2 and type=0 and enabled=2 and kmno =1 and isync =1";
                $_result = $db->fetchAll($query);
                $result = $_result[0];
                if($result != null && is_array($result))
                {
                    $cache->setCache($key, $result);				
                }
            }
            
            return $result;
	}
    public function get_detail_by_imei_synced($imei)
	{
            $cache = $this->getCacheInstance('ws');
            $key = "get_detail_by_imei_synced.$this->_tablename.$imei";
            $result = $cache->getCache($key);
            $result= FALSE;
            if($result=== FALSE){
                
               $db = $this->getDbConnection();
                $query = "select * from $this->_tablename where imei = '$imei' and ispay=2 and type=0 and enabled=2 and kmno =1 and isync =2 order by id desc";
                $_result = $db->fetchAll($query);
                $result = $_result[0];
                if($result != null && is_array($result))
                {
                    $cache->setCache($key, $result);				
                }
            }
            
            return $result;
	}
        public function get_top1($_date30=""){
            $db = $this->getDbConnection();
            $query = "select * from $this->_tablename where  ispay=2 and type=0 and enabled=2 and kmno =1 and isync =1";
            if($_date30 != NULL){
                $query .=" and datetime >= '$_date30'";
            }
            $query .=" order by id asc limit 0,1";
            $result = $db->fetchAll($query);
            return $result[0];
        }
        public function get_list_top1($_date30=""){
            $db = $this->getDbConnection();
            $query = "select * from $this->_tablename where  ispay=2 and type=0 and enabled=2 and kmno =1 and isync =1";
            if($_date30 != NULL){
                $query .=" and datetime >= '$_date30'";
            }
            $query .=" order by id asc limit 0,1";
            $result = $db->fetchAll($query);
            return $result;
        }
        public function get_detail_by_sync_chietkhau($_date30=""){
            $db = $this->getDbConnection();
            $query = "select * from $this->_tablename WHERE (type =0 and ispay =2) or type = 6 and isync_ck=1 and enabled !=1";
            if($_date30 != NULL){
                $query .=" and datetime >= '$_date30'";
            }
            $query .=" order by id asc limit 0,1";
            $result = $db->fetchAll($query);
            return $result[0];
        }
        public function get_detail_by_sync_chietkhau_by_idaddonuser($id_addon_user){
            $db = $this->getDbConnection();
            $query = "select * from $this->_tablename WHERE id_addon_user = $id_addon_user AND ( (type =0 and ispay =2) or type = 6 and isync_ck=1 and enabled !=1)";
            $query .=" order by id asc limit 0,1";
            $result = $db->fetchAll($query);
            return $result[0];
        }
        public function get_list_by_sync_chietkhau_by_idaddonuser($id_addon_user,$foresync=0){
            $db = $this->getDbConnection();
            if($foresync==1){
                $query = "select * from $this->_tablename WHERE id_addon_user = $id_addon_user AND ( (type =0 and ispay =2) or type = 6  and enabled !=1)";
            }else{
                $query = "select * from $this->_tablename WHERE id_addon_user = $id_addon_user AND ( (type =0 and ispay =2) or type = 6 and isync_ck=1 and enabled !=1)";
            }
            $result = $db->fetchAll($query);
            return $result;
        }
    public function get_list_tra_no_by_imei($imei) {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " WHERE imei = '$imei'";
        $result = $db->fetchAll($query);
        return $result;
    }
    public function getListByIdAddonUser($id_addon_user) {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " WHERE id_addon_user = $id_addon_user";
        $result = $db->fetchAll($query);
        return $result;
    }
    public function get_list_owe($ispay=0,$start="",$end="",$storeid=0){
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " WHERE kmno = 1";
        if((int)$ispay >0){
            $query .=" and ispay = $ispay";
        }
        if($start != NULL){
            $query .=" and datetime >= '$start'";
        }
        if($end != NULL){
            $query .=" and datetime < '$end'";
        }
        if((int)$storeid >0){
            $query .=" and storeid = $storeid";
        }
        $query .=" order by autoid desc  limit 500";
//        echo "<pre>";
//        var_dump($query);
//        die();
        $result = $db->fetchAll($query);
        return $result;
    }

    public function getListById($strID){
        $cache = $this->getCacheInstance();
        $key = "getListById.$this->_tablename.$strID";
        $result = $cache->getCache($key);
//        $result = FALSE;
        if($result=== FALSE){
            $db = $this->getDbConnection();
            $query = "SELECT * FROM " . $this->_tablename . " WHERE id_km IN ($strID)";
            $result = $db->fetchAll($query);
                if(!is_null($result) && is_array($result))
                    {
                        $cache->setCache($key, $result, 60 * 10);
                    }
        }
        
        return $result;
    }
    public function getGroupListById($strID){
        $cache = $this->getCacheInstance();
        $key = "getGroupListById.$this->_tablename.$strID";
        $result = $cache->getCache($key);
        if($result=== FALSE){
            $db = $this->getDbConnection();
            $query = "SELECT title, count(autoid) as total FROM " . $this->_tablename . " WHERE id_addon_user IN ($strID) and enabled =2 and type =0 group by title";
            $result = $db->fetchAll($query);
            if(!is_null($result) && is_array($result))
            {
                $cache->setCache($key, $result, 3600);
            }
        }
        
        return $result;
    }

    public function getListByBillIdActive($id_addon_user,$enabled=""){
        $cache = $this->getCacheInstance();
        $key = "getListByBillIdActive.$this->_tablename.$id_addon_user.$enabled";
        $result = $cache->getCache($key);
//        $result = FALSE;
        if($result=== FALSE){
            $db = $this->getDbConnection();
            $query = "SELECT * FROM " . $this->_tablename . " WHERE id_addon_user IN ($id_addon_user) and enabled !=1";
            $result = $db->fetchAll($query);
                if(!is_null($result) && is_array($result))
                    {
                        $cache->setCache($key, $result, 60 * 10);
                    }
        }
        
        return $result;
    }
    public function get_list_by_billid_trano($id_addon_user){
        $cache = $this->getCacheInstance();
        $key = "get_list_by_billid_trano.$this->_tablename.$id_addon_user";
        $result = $cache->getCache($key);
        $result = FALSE;
        if($result=== FALSE){
            $db = $this->getDbConnection();
            $query = "SELECT * FROM " . $this->_tablename . " WHERE id_addon_user IN ($id_addon_user) and bill_no >0";
            $result = $db->fetchAll($query);
            if(!is_null($result) && is_array($result))
                {
                    $cache->setCache($key, $result, 60 * 10);
                }
        }
        
        return $result;
    }

    public function getListByType($id_addon_user=0,$type="",$enabled="") {
        $cache = $this->getCacheInstance();
        $key = "getListByType.$this->_tablename.$id_addon_user.$type.$enabled";
        $result = $cache->getCache($key);
//        $result = FALSE;
        if($result=== FALSE){
            $db = $this->getDbConnection();
            $query = "SELECT id_addon_user,sum(return_price) as total FROM " . $this->_tablename . " WHERE 1=1";
            if($id_addon_user != 0){
                $query .=" and id_addon_user IN ($id_addon_user)";
            }
            $query .=" and type = $type";
            $query .=" and enabled = $enabled";
            $query .=" group by id_addon_user";
//            var_dump($query);exit();
            $result = $db->fetchAll($query);
                if(!is_null($result) && is_array($result))
                    {
                        $cache->setCache($key, $result, 60 * 10);
                    }
        }
        
        return $result;
    }
    public function get_list_ctkm_by_id_addon_user_by_products_id($id_addon_user,$product_ids,$stt=0){
        $db = $this->getDbConnection();
            $query = "SELECT * FROM " . $this->_tablename . " WHERE 1=1";
            if($id_addon_user != NULL){
                $query .=" and id_addon_user IN ($id_addon_user)";
            }
            if($product_ids != NULL){
                $query .=" and product_ids IN ($product_ids)";
            }
            if((int)$stt >0){
                $query .="  and stt= $stt";
            }
            //echo "<pre>";
            //var_dump($query);
            $result = $db->fetchAll($query);
            return $result;
    }

    public function getDetail($id) {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " WHERE autoid=?";
        $data = array($id);
        $result = $db->fetchAll($query, $data);
        return $result[0];
    }
    public function getListByOption($id_addon_user,$product_ids="",$enabled=""){
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " WHERE 1=1";
        if($id_addon_user != null){
            $query .= " and id_addon_user = $id_addon_user";
        }
        if($product_ids != null){
            $query .= " and product_ids = $product_ids";
        }
        if($enabled != null){
            $query .= " and enabled != $enabled";
        }
        $result = $db->fetchAll($query);
        return $result;
    }

    public function getDetail2($id_addon_user) {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " WHERE id_addon_user = $id_addon_user";
        $result = $db->fetchAll($query);
        return $result;
    }
    public function getListByProductIds_Users($product_ids,$id_addon_user){
        $db = $this->getDbConnection();
        $query = " select * from $this->_tablename where product_ids = $product_ids and enabled != 1 and id_addon_user = $id_addon_user";
        $result = $db->fetchAll($query);
        return $result;
    }
    public function getListByProductIds($itemid)
	{
//            exit('231');
            $db = $this->getDbConnection();
            $query = "SELECT * FROM  $this->_tablename  where product_ids = $itemid";
//    			var_dump($query);exit();
            $result = $db->fetchAll($query);
            
//            var_dump(count($result));exit();
            return $result;		
	}
    public function getListByProductItemid($products_id)
	{
//            exit('231');
            $db = $this->getDbConnection();
            $query = "SELECT * FROM  $this->_tablename  where product_ids = $products_id ";
//    			var_dump($query);exit();
            $result = $db->fetchAll($query);
            
//            var_dump(count($result));exit();
            return $result;		
	}
    public function getListByProductIdsActive($products_id,$id_addon_user)
	{
        
            $cache = $this->getCacheInstance();
        $key = "getListByProductIdsActive".  $this->_tablename.$products_id.$id_addon_user;
        $result = $cache->getCache($key);
        if($result ===FALSE){
            $db = $this->getDbConnection();
            $query = "SELECT * FROM  $this->_tablename  where product_ids = $products_id and id_addon_user= $id_addon_user and (enabled !=1 or type = 5)";
//    			var_dump($query);exit();
            $result = $db->fetchAll($query);
            if(!is_null($result) && is_array($result)){
                $cache->setCache($key, $result);
            }
        }
//            var_dump(count($result));exit();
            return $result;		
	}
    public function getListByProductIdsActive2($products_id,$id_addon_user)
	{
        $cache = $this->getCacheInstance();
        $key = "getListByProductIdsActive2s".  $this->_tablename.$products_id.$id_addon_user;
        $result = $cache->getCache($key);
        if($result ===FALSE){
            $db = $this->getDbConnection();
            $query = "SELECT * FROM  $this->_tablename  where product_ids IN ($products_id) and id_addon_user IN ($id_addon_user) and (enabled !=1)";
//            $query .= "  group by id_km ";
            $result = $db->fetchAll($query);
            if(!is_null($result) && is_array($result)){
                $cache->setCache($key, $result,300);
            }
        }
            
            return $result;		
	}
    public function insert($data) {
        $db = $this->getDbConnection();
        $result = $db->insert($this->_tablename, $data);
        return $result;
    }
    public function update($id,$data) {
        $where = array();
        $where[] = "autoid='" . parent::adaptSQL($id) . "'";
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

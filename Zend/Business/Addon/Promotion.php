<?php

class Business_Addon_Promotion extends Business_Abstract {

    private $_tablename = 'addon_promotion';
    private $_tablename2 = 'addon_promotion_fast';
    private $_tablename3 = 'addon_promotion_fast_product';

    const KEY_LIST = 'promotion.list.%s';   //key of list.questionid
    const KEY_DETAIL = 'promotion.detail.%s';  //key of detail.id
    const KEY_DETAIL_KM = 'km.%s';  //key of detail.id
    const KEY_DETAIL_LIST_KM = 'getlistaddon_promotion.%s';  //key of list.id

    private static $_instance = null;

    function __construct() {
        
    }

    //public static function
    /**
     * get instance of Business_Addon_Promotion
     *
     * @return Business_Addon_Promotion
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
    
    private function getKeyKM($itemid){
        return sprintf(self::KEY_DETAIL_KM, $itemid);
    }
    private function getListKeyKM($keywork){
        return sprintf(self::KEY_DETAIL_LIST_KM,$keywork);
    }
    public function countList($keywork=""){
        
    }
    public function getListByType($product_ids,$type=0,$itemid_title=0,$title="") {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM $this->_tablename WHERE product_ids IN ($product_ids)  and enabled =1 ";
        if($type >0){
            $query .= " and type = $type ";
        }
        if($itemid_title > 0){
            $query .= " and itemid_title = $itemid_title ";
        }
        if($title !=NULL){
            $query .=" and title ='$title'";
        }
//        var_dump($query);exit();
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
            $query = "SELECT * FROM " . $this->_tablename . " WHERE itemid IN ($strID)";
            $result = $db->fetchAll($query);
                if(!is_null($result) && is_array($result))
                    {
                        $cache->setCache($key, $result, 60 * 10);
                    }
        }
        
        return $result;
    }
    public function countCTKM($_products_id,$colorid,$date=""){
        $db = $this->getDbConnection();
        
        //SELECT *,fp.itemid as id_km FROM  $this->_tablename3 fp, $this->_tablename2 f where fp.idfast = f.itemid and  fp.product_ids IN ($itemid) and fp.enabled =1 and  fp.colorid = $colorid
        $query = "SELECT *,fp.itemid as id_km,ictkm,fp.colorid as colorid_p FROM  $this->_tablename3 fp, $this->_tablename2 f where fp.idfast = f.itemid and  fp.product_ids IN ($_products_id) and fp.enabled =1 and f.enabled =1 and  ictkm>0 and  fp.colorid = 0";
//        if($date != NULL){
//                $query .=" and  f.date_to >= '$date'";
//            }
        $query .=" group by ictkm";
        
        $result1 = $db->fetchAll($query);
        if((int)$colorid >0){
            $query2 = "SELECT *,fp.itemid as id_km,ictkm,fp.colorid as colorid_p FROM  $this->_tablename3 fp, $this->_tablename2 f where fp.idfast = f.itemid and  fp.product_ids IN ($_products_id) and fp.enabled =1 and f.enabled =1 and  fp.colorid = $colorid and ictkm>0 ";
//            if($date != NULL){
//                $query2 .=" and  f.date_to >= '$date'";
//            }
            $query2 .=" group by ictkm";
            $result2 = $db->fetchAll($query2);
        }
        $result = array();
        if($result1!=null){
            $result = array_merge($result1,$result);
        }
        if($result2!=null){
            $result = array_merge($result2,$result);
        }
        return $result;
    }

    public function getListByPid($_products_id,$colorid=0,$ctkm=0){
        $db = $this->getDbConnection();
        $query = "SELECT * FROM $this->_tablename WHERE product_ids = $_products_id and enabled = 1";
        $query .=" and ctkm = $ctkm";
        $result = $db->fetchAll($query);
        
        if($colorid !=0){
            $query2 = "SELECT * FROM $this->_tablename WHERE product_ids = $_products_id and enabled = 1 and colorid = $colorid";
            $query2 .=" and ctkm = $ctkm";
            $result2 = $db->fetchAll($query2);
        }
        if($result2!=null){
            $result = array_merge($result,$result2);
        }
        return $result;
    }

    public function checkType4($products_id,$itemid_title){
        $db = $this->getDbConnection();
        $query = "SELECT * FROM $this->_tablename WHERE product_ids = $products_id and itemid_title = $itemid_title and enabled = 1";
        $result = $db->fetchAll($query);
        return $result[0];
    }
    public function checkType($products_id,$type){
        $db = $this->getDbConnection();
        $query = "SELECT * FROM $this->_tablename WHERE product_ids = $products_id and type = $type and enabled = 1";
        $result = $db->fetchAll($query);
        return $result[0];
    }
    public function getListByType2($products_id,$type){
        $db = $this->getDbConnection();
        $query = "SELECT * FROM $this->_tablename2 f,$this->_tablename3 fp WHERE fp.idfast = f.itemid and fp.product_ids IN ($products_id) and f.type IN ($type) and f.enabled = 1 and fp.enabled =1";
        
        $result = $db->fetchAll($query);
        return $result;
    }

    public function getAllTagsPromotion() {
        $cache = $this->getCacheInstance();
        $key = "getAllTagsPromotion" . $this->_tablename;
        $result = $cache->getCache($key);
//        $cache->deleteCache($key);exit();
//        $result = false;
//        var_dump($key);exit();
        if ($result === FALSE) {
            $db = $this->getDbConnection();
            $query = "SELECT title, itemid FROM $this->_tablename WHERE title != '' GROUP BY title  ORDER BY title ASC";
//            var_dump($query);exit();
            $result = $db->fetchAll($query);

            if (!is_null($result) && is_array($result) && count($result) > 0) {
                foreach ($result as $item) {
                    $ret[] = "\"" . $item["title"]."\"";
                }
                $ret = implode(",", $ret);
                $result = $ret;
                $cache->setCache($key, $result, 60 * 5);
            }
        }
        return $result;
    }
    
    
    public function getAllList($keywork="",$enabled){
        $cache = $this->getCacheInstance();
        $key = $this->getListKeyKM($keywork);
        if($keywork ==NULL){
            $result = $cache->getCache($key);
        }else{
            $result = FALSE;
        }
//        var_dump($result);exit();
        $result = FALSE;
        if ($result === FALSE) {
            $db = $this->getDbConnection();
            $query = "SELECT * FROM  $this->_tablename where 1= 1 and type != '5'  and enabled = $enabled ";
            if($keywork !=null){
                $query .=" and (title like '%$keywork%' or product_ids = '$keywork') ";
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
    public function getList($keywork="",$offset=1, $records=30,$enabled){
        $cache = $this->getCacheInstance();
        $key = $keywork.$offset.$records;
        $result = $cache->getCache($key);
        $result = FALSE;
        if ($result === FALSE) {
            $db = $this->getDbConnection();
            $query = "SELECT * FROM  $this->_tablename where 1= 1 and type != '5' and enabled = $enabled ";
            if($keywork !=null){
                $query .=" and (title like '%$keywork%' or product_ids = '$keywork')";
            }
            $query.=" ORDER BY itemid DESC";
            $query .= " LIMIT $records OFFSET $offset ";
//            var_dump($query);exit();
            $result = $db->fetchAll($query);
            $cache->setCache($key, $result);
            
        }
        return $result;
    }
    

    public function getDetail($id) {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " WHERE itemid=?";
        $data = array($id);
        $result = $db->fetchAll($query, $data);
        return $result;
    }
    public function getDetail2($id) {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " WHERE itemid=?";
        $data = array($id);
        $result = $db->fetchAll($query, $data);
        return $result[0];
    }
    public function getListByProductIdsType($product_ids,$type) {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM $this->_tablename WHERE product_ids = $product_ids and type = $type and enabled =1";
//        var_dump($query);exit();
        $result = $db->fetchAll($query);
        return $result;
    }
    public function getListByProductIdsType2($product_ids,$type,$itemid_title) {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM $this->_tablename WHERE product_ids = $product_ids and type = $type and enabled =1 and itemid_title = $itemid_title";
//        var_dump($query);exit();
        $result = $db->fetchAll($query);
        return $result;
    }
    public function checkListType4($product_ids,$type,$itemid_title) {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM $this->_tablename WHERE product_ids = $product_ids  and enabled =1 ";
        if($type != null){
            $query .= " and type = $type ";
        }
        if($itemid_title != null){
            $query .= " and itemid_title = $itemid_title ";
        }
//        var_dump($query);exit();
        $result = $db->fetchAll($query);
        return $result;
    }
    public function getListByProductIds($itemid,$ctkm=0)
    {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM  $this->_tablename  where product_ids IN ($itemid) and enabled =1";
        $query .=" and ctkm = $ctkm";
        $result = $db->fetchAll($query);
        return $result;		
    }
    public function getListByProductIdsColorCTKM($itemid,$colorid=0,$ctkm=0)
    {
        $db = $this->getDbConnection();
        $query = "SELECT *,fp.itemid as id_km,fp.colorid as colorids,fp.ictkm FROM  $this->_tablename3 fp, $this->_tablename2 f where fp.idfast = f.itemid and  fp.product_ids IN ($itemid) and fp.enabled =1 and f.enabled =1 and fp.ictkm = $ctkm and fp.colorid = 0";
        $result = $db->fetchAll($query);
        if((int)$colorid >0){
            $query2 = "SELECT *,fp.itemid as id_km,fp.colorid as colorids,fp.ictkm FROM  $this->_tablename3 fp, $this->_tablename2 f where fp.idfast = f.itemid and  fp.product_ids IN ($itemid) and fp.enabled =1 and f.enabled =1 and fp.ictkm = $ctkm and fp.colorid = $colorid";
            $result2 = $db->fetchAll($query2);
        }
        if($result2!=null){
            $result = array_merge($result,$result2);
        }
        
        return $result;		
    }
    public function get_key_promtion_web($itemid=0,$colorid=0){
        $key = "getListByProductIdsColorCTKM_web".$this->_tablename.$itemid.$colorid;
        return $key;
    }
    public function delete_key_promtion_web($itemid=0,$colorid=0){
        $cache = $this->getCacheInstance();
        $key = $this->get_key_promtion_web($itemid,$colorid);
        $cache->deleteCache($key);
    }

    public function getListByProductIdsColorCTKM_web($itemid,$colorid=0)
    {
        $cache = $this->getCacheInstance();
        $key = $this->get_key_promtion_web($itemid,$colorid);
        $result = $cache->getCache($key);
        $ctkm=0;
        if ($result === FALSE) {
        $db = $this->getDbConnection();
            $query = "SELECT *,fp.itemid as id_km,fp.colorid as colorids,fp.ictkm FROM  $this->_tablename3 fp, $this->_tablename2 f where fp.idfast = f.itemid and  fp.product_ids IN ($itemid) and fp.enabled =1 and f.enabled =1 and fp.ictkm = $ctkm and fp.colorid = 0";
            $result = $db->fetchAll($query);
            if((int)$colorid >0){
                $query2 = "SELECT *,fp.itemid as id_km,fp.colorid as colorids,fp.ictkm FROM  $this->_tablename3 fp, $this->_tablename2 f where fp.idfast = f.itemid and  fp.product_ids IN ($itemid) and fp.enabled =1 and f.enabled =1 and fp.ictkm = $ctkm and fp.colorid = $colorid";
                $result2 = $db->fetchAll($query2);
            }
            if($result2!=null){
                $result = array_merge($result,$result2);
            }
            if(!is_null($result) && is_array($result))
            {
                $cache->setCache($key, $result);
            }
        }
        
        return $result;		
    }
    public function getListByProductIds2($itemid)
    {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM  $this->_tablename  where product_ids IN ($itemid) and enabled =1";
        $result = $db->fetchAll($query);
        return $result;		
    }
    public function excute($query){
            $db     =  $this->getDbConnection();
            $result = $db->query($query);
            $this->deleteListKeyKM();
            return $result;
        }
    public function insert($data) {
        $db = $this->getDbConnection();
        $result = $db->insert($this->_tablename, $data);
        $this->deleteListKeyKM();
        return $result;
    }
    public function update($id,$data) {
        $db= $this->getDbConnection();
        $query = "itemid = ".$id;
        $result = $db->update($this->_tablename, $data,$query);
        $this->deleteKeyKM($id);
        $this->deleteListKeyKM();
//            var_dump($result);exit();
        return $result;
    }

    public function update_price($id,$price,$flag) {
        //get current menu
            $db= $this->getDbConnection();
            $query = "itemid = ".$id;
            $data = array(
                "enabled" => "0"
            );
            $result = $db->update($this->_tablename, $data,$query);
            $this->deleteKeyKM($id);
            $this->deleteListKeyKM();
//            var_dump($result);exit();
            return $result;
    }
    public function delete2($id) {
        //get current menu
            $db= $this->getDbConnection();
            $query = "itemid = ".$id;
            $data = array(
                "enabled" => "0",
                "time_delete" => date('Y-m-d H:i:s')
            );
            $result = $db->update($this->_tablename, $data,$query);
            $this->deleteKeyKM($id);
            $this->deleteListKeyKM();
//            var_dump($result);exit();
            return $result;
    }
    public function delete($id)
	{
		$db = $this->getDbConnection();
		$where = array();
		$where[] = "itemid='" . parent::adaptSQL($id) . "'";
		$result = $db->delete($this->_tablename,$where);
                if($result > 0)
		{
			$this->deleteKeyKM($id);
                        $this->deleteListKeyKM();
		}
                return $result;
	}
    public function restore($id) {
        //get current menu
            $db= $this->getDbConnection();
            $query = "itemid = ".$id;
            $data = array(
                "enabled" => "1",
                "time_restore" => date('Y-m-d H:i:s')
            );
            $result = $db->update($this->_tablename, $data,$query);
//            var_dump($result);exit();
            $this->deleteKeyKM($id);
            $this->deleteListKeyKM();
            return $result;
    }
    
    private function _deleteAllCache()
    {
            $cache = $this->getCacheInstance();
            $cache->flushAll();
    }
    private function _deleteCacheByKey($key)
    {
        
        $key_name = "getlist".$key;
            $cache = $this->getCacheInstance('ws');
            $data = $cache->getCache($key_list);
//            $cache->flushAll();
            $cache->deleteCache($key_name);
    }
    private function deleteKeyKM($itemid) {
        $cache = $this->getCacheInstance();
        $key = $this->getKeyKM($itemid);
        $cache->deleteCache($key);
    }
    private function deleteListKeyKM($keyword="") {
        $cache = $this->getCacheInstance();
        $key = $this->getListKeyKM($keyword);
        $cache->deleteCache($key);
    }
}

?>

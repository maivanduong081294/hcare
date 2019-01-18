<?php

class Business_Addon_PromotionOption extends Business_Abstract {

    private $_tablename = 'promotion_option';

    const KEY_LIST = 'promotion_option.list.%s';   //key of list.questionid
    const KEY_DETAIL = 'promotion_option.detail.%s';  //key of detail.id
    const KEY_DETAIL_KM = 'km.%s';  //key of detail.id
    const KEY_DETAIL_LIST_KM = 'getlistpromotion_option.%s';  //key of list.id

    private static $_instance = null;

    function __construct() {
        
    }

    //public static function
    /**
     * get instance of Business_Addon_PromotionOption
     *
     * @return Business_Addon_PromotionOption
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
//        $result = FALSE;
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
    public function getDetailByProductIds($product_ids,$type="",$enabled="",$date_from_to="") {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM $this->_tablename WHERE 1 =1 ";
        if($product_ids!=null){
            $query .=" and product_ids = $product_ids ";
        }
        if($type!=null){
            $query .=" and type = $type ";
        }
        if($enabled!=null){
            $query .=" and enabled = $enabled ";
        }
        $query .= " order by datetime DESC";
        $result = $db->fetchAll($query);
//        echo "<pre>";
//        var_dump($result[0]["date_from"],$result[0]["date_to"]);
//        exit();
        if($date_from_to > $result[0]["date_from"] &&  $date_from_to < $result[0]["date_to"]){
            return $result[0];
        }else{
            return;
        }
        
    }
    public function getDetailByProductIdsVoteId($product_ids,$type="",$enabled="",$date_from_to="",$vote_id) {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM $this->_tablename WHERE 1 =1 ";
        if($product_ids!=null){
            $query .=" and product_ids = $product_ids ";
        }
        if($type!=null){
            $query .=" and type = $type ";
        }
        if($enabled!=null){
            $query .=" and enabled = $enabled ";
        }
//        if($vote_id == null){
//            $query .= " and vote_id NOT IN ()";
//        }
        $query .= " order by datetime DESC";
        $result = $db->fetchAll($query);
//        echo "<pre>";
//        var_dump($result[0]["date_from"],$result[0]["date_to"]);
//        exit();
        if($date_from_to > $result[0]["date_from"] &&  $date_from_to < $result[0]["date_to"]){
            return $result[0];
        }else{
            return;
        }
        
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
                "time_delete"=>date('Y-m-d H:i:s')
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

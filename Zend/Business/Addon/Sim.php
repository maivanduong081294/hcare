<?php

class Business_Addon_Sim extends Business_Abstract {

    private $_tablename = 'ws_sim';
    private static $_instance = null;
    const KEY_DETAIL_SIM = 'detail_sim.%s';  //key of detail.id
    const KEY_LIST_SIM = 'list_sim.$s';  //key of list.id
    function __construct() {
        
    }

    //public static function
    /**
     * get instance of Business_Addon_Sim
     *
     * @return Business_Addon_Sim
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
    private function getKeyDetailSim($itemid){
        return sprintf(self::KEY_DETAIL_SIM, $itemid);
    }
    private function getListSim($keywork){
        return sprintf(self::KEY_LIST_SIM,$keywork);
    }
     private function getCacheInstance() {
        $cache = GlobalCache::getCacheInstance('ws');
        return $cache;
    }
    

    public function getAllSim() {
//    exit('dsads');
        $cache = $this->getCacheInstance('ws');
        $key = "getAllSim" . $this->_tablename;
        $result = $cache->getCache($key);
//        $cache->deleteCache($key);//exit();
//        $result = false;
//        var_dump($key);exit();
        if ($result === FALSE) {
            $db = $this->getDbConnection();
            $query = "SELECT title, itemid,price,original_price,productsid,productscode,cateid FROM ws_sim WHERE title != '' and enabled = 1 and onstock = 1 ORDER BY title ASC";
//            var_dump($query);exit();
            $result = $db->fetchAll($query);

            if (!is_null($result) && is_array($result) && count($result) > 0) {
                foreach ($result as $item) {

//                    $item = trim(str_replace("", "%", $item));
                    $subfix = "";
                    if ($item["productsid"] == 4) {
                        $subfix = "--(" . $item["productscode"] . ")";
                    } else {
                        $subfix = "--(cty)";
                        if ($item["price"] > 0) {
                            $subfix = "--(hnam)";
                        }
                    }
                    $itemid = "";
                    $itemid = "--" . $item["itemid"];
                    $productsid = "--" . $item["productsid"];
                    $cate_id = "--" . $item["cateid"];

                    $ret[] = "\"" . $item["title"] . $subfix . $itemid . $productsid . $cate_id."\"";
                }
                $ret = implode(",", $ret);
                $result = $ret;
                $cache->setCache($key, $result, 60 * 5);
            }
        }
        return $result;
    }
    public function getAllSimByCateid() {
//    exit('dsads');
        $cache = $this->getCacheInstance('ws');
        $key = "getAllSimByCateid" . $this->_tablename;
        $result = $cache->getCache($key);
//        $cache->deleteCache($key);exit();
//        $result = false;
//        var_dump($result);
        if ($result === FALSE) {
//            var_dump($result);exit('76');
            $db = $this->getDbConnection();
            $query = "SELECT title, itemid,price,original_price,productsid,productscode,cateid FROM ws_sim WHERE title != '' and title > 999999999 and enabled = 1 and onstock = 1 and cateid = 719 ORDER BY title ASC";
//            var_dump($query);exit();
            $result = $db->fetchAll($query);

            if (!is_null($result) && is_array($result) && count($result) > 0) {
                foreach ($result as $item) {

//                    $item = trim(str_replace("", "%", $item));
                    $subfix = "";
                    if ($item["productsid"] == 4) {
                        $subfix = "--(" . $item["productscode"] . ")";
                    } 
//                    else {
//                        $subfix = "--(cty)";
//                        if ($item["price"] > 0) {
//                            $subfix = "--(hnam)";
//                        }
//                    }
//                    $itemid = "";
//                    $itemid = "--" . $item["itemid"];
//                    $productsid = "--" . $item["productsid"];
//                    $cate_id = "--" . $item["cateid"];

                    $ret[] = "\"" . $item["title"] . $subfix ."\"";
//                    $ret[] = "\"" . $item["title"] . $subfix . $itemid . $productsid . $cate_id."\"";
                }
                $ret = implode(",", $ret);
                $result = $ret;
                $cache->setCache($key, $result, 60 * 5);
            }
        }
        return $result;
    }
    public function getList($cateid,$offset, $records,$check_sim="",$title){
        $cache = $this->getCacheInstance();
        $key = $this->getListSim($cateid);
        $result = $cache->getCache($key);
//        $cache->deleteCache($key);exit();
//        var_dump($result);exit();
        $result = FALSE;
        if ($result === FALSE) {
            $db = $this->getDbConnection();
            $query ="SELECT *,t1.sim,t2.enabled,t2.itemid FROM `ws_sim_6` as t1 JOIN `ws_sim` as t2 where t1.sim = t2.title and productsid = 7 ";
            if($title != null){
                $query .=" and title like '%$title%' ";
            }
            if($cateid != 0){
                $query .=" and cateid = $cateid ";
            }
            if($check_sim == ""){
                $query .=" and enabled = 1 ";
            }
            if($check_sim == 1){
                $query .=" and enabled = 1 ";
            }
            if($check_sim == 2){
                $query .=" and enabled = 0 ";
            }
            
            $query .= " LIMIT $records OFFSET $offset ";
//            $query.=" ORDER BY itemid DESC";
//            $query.=" ORDER BY itemid DESC LIMIT 30";
//            var_dump($query);exit();
            $result = $db->fetchAll($query);
            $cache->setCache($key, $result);
            
        }
        return $result;
    }
    public function countList($cateid,$check_sim = "",$title){
        $db = $this->getDbConnection();
        $query = "SELECT count(*) FROM  $this->_tablename where 1= 1 and enabled = 1 and productsid = 7";
        if($title != null){
                $query .=" and title like '%$title%' ";
            }
        if($cateid != 0){
            $query .=" and cateid = $cateid ";
        }
        if($check_sim == 1){
            $query .=" and enabled = 1 ";
        }
        if($check_sim == 2){
            $query .=" and enabled = 0 ";
        }
//        var_dump($query);exit();
        $result = $db->fetchAll($query);
        return $result[0];
    }
    public function countListBySimPriceType($sim_text,$price_from,$price_to,$type,$cateid){
        $cache = $this->getCacheInstance();
        $key = 'daa1';
        $result = $cache->getCache($key);
        $result = FALSE;
        if ($result === FALSE) {
            $db = $this->getDbConnection();
            $query = "SELECT * FROM  $this->_tablename where 1= 1 and enabled = 1 and productsid = 7 ";
            if($sim_text != ""){
                $query .= " and title like '%$sim_text%'";
            }
            if($price_from != ""){
                $query .= " and price >= $price_from";
            }
            if($price_to != ""){
                $query .= " and price <= $price_to";
            }
            if($cateid !=0){
                $query .= " and cateid = $cateid ";
            }
            if($type == 10){
                $query .= " and  title < 999999999";
            }
            if($type == 11){
                $query .= " and  title > 999999999";
            }
//            if($type ==0){
                $query .= " LIMIT 200";
//            }
//            var_dump($query);exit();
            $result = $db->fetchAll($query);
            $cache->setCache($key, $result);
            
        }
        return $result;
    }
    public function getListBySimPriceType($sim_text,$price_from,$price_to,$type,$offset=0, $records=20,$cateid){
        $cache = $this->getCacheInstance();
        $key = 'daa2';
//        $cache->deleteCache($key);exit();
        $result = $cache->getCache($key);
        $result = FALSE;
        if ($result === FALSE) {
            $db = $this->getDbConnection();
            $query = "SELECT * FROM  $this->_tablename where 1= 1 and enabled = 1 and productsid = 7 ";
            if($sim_text != ""){
                $query .= " and title like '%$sim_text%'";
            }
            if($price_from != ""){
                $query .= " and price >= $price_from";
            }
            if($price_to != ""){
                $query .= " and price <= $price_to";
            }
            if($type == 10){
                $query .= " and  title < 999999999";
            }
            if($type == 11){
                $query .= " and  title > 999999999";
            }
            if($cateid !=0){
                $query .= " and cateid = $cateid ";
            }
            if($type ==0){
                $query .= " LIMIT 100 OFFSET $offset ";
            }else{
                $query.=" ORDER BY itemid DESC ";
                $query .= " LIMIT $records OFFSET $offset ";
            }
//            var_dump($query);exit();
            $result = $db->fetchAll($query);
            $cache->setCache($key, $result);
            
        }
        return $result;
    }
    
    public function getSimNotUsed($cateid=0){
        $cache = $this->getCacheInstance();
        $key = 'getSimNotUsed'.$cateid;
//        $cache->deleteCache($key);exit();
        $result = $cache->getCache($key);
        $result = FALSE;
        if ($result === FALSE) {
            $db = $this->getDbConnection();
            $query = "SELECT *, rand() as random   FROM `ws_sim_6` as t1 JOIN `ws_sim` as t2 where t1.sim = t2.title and t2.enabled = 1";
            $query .= " and  t2.title > 999999999";
//            $query .= " and t2.cateid =  $cateid";
            $query.=" ORDER BY random DESC ";
            $query .= " LIMIT 0, 1 ";
            $result = $db->fetchAll($query);
            $result =$result[0];
            $cache->setCache($key, $result);
            
        }
        return $result;
    }
    

    public function getDetail($id) {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " WHERE itemid=?";
        $data = array($id);
        $result = $db->fetchAll($query, $data);
        return $result[0];
    }
    public function get_list_by_title($title) {
        $db = $this->getDbConnection();
        $query = "SELECT *,t1.sim,t2.enabled,t2.itemid FROM `ws_sim_6` as t1 JOIN `ws_sim` as t2 where t1.sim = t2.title  and t2.title IN ($title)";
//        $query = "SELECT * FROM " . $this->_tablename . " WHERE title IN ($title)";
        $result = $db->fetchAll($query);
        return $result;
    }
    public function get_detail_by_title($title) {
        $db = $this->getDbConnection();
        $query = "SELECT *,t1.sim,t2.enabled,t2.itemid FROM `ws_sim_6` as t1 JOIN `ws_sim` as t2 where t1.sim = t2.title  and t2.title ='$title'";
//        $query = "SELECT * FROM " . $this->_tablename . " WHERE title IN ($title)";
        $result = $db->fetchAll($query);
        return $result[0];
    }

    public function get_detail_by_titlev2($title,$onstock = '') {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM `ws_sim` where title ='$title' and agency not in ('viettel') and agency != '' and enabled = 1";
        if($onstock!='') {
            $query.= " and onstock = $onstock";
        }
//        $query = "SELECT * FROM " . $this->_tablename . " WHERE title IN ($title)";
        $result = $db->fetchAll($query);
        return $result[0];
    }

    public function get_detail_by_titlev3($title,$onstock = '') {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM `ws_sim` where title ='$title' and agency in ('viettel') and enabled = 1";
        if($onstock!='') {
            $query.= " and onstock = $onstock";
        }
//        $query = "SELECT * FROM " . $this->_tablename . " WHERE title IN ($title)";
        $result = $db->fetchAll($query);
        return $result[0];
    }

    public function get_detail_by_titlev4($title) {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM `ws_sim` where title ='$title' and agency != ''";
//        $query = "SELECT * FROM " . $this->_tablename . " WHERE title IN ($title)";
        $result = $db->fetchAll($query);
        return $result[0];
    }
    
    public function getDetailByCate($title,$cateid) {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " WHERE title = '$title'  and productsid = 7 and enabled = 1 and title > 999999999";
        $result = $db->fetchAll($query);
        return $result[0];
    }
    
    public function insert($data) {
        $db = $this->getDbConnection();
        $result = $db->insert($this->_tablename, $data);
//        var_dump($result);exit();
        $this->deleteListSim();
        return $result;
    }
    public function update($id,$data) {
        $db= $this->getDbConnection();
        $query = "itemid = ".$id;
        $result = $db->update($this->_tablename, $data,$query);
        $this->_deleteAllCache();
//        $this->deleteDetailSim($id);
//        $this->deleteListSim();
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
			$this->deleteDetailSim($id);
                        $this->deleteListSim();
		}
                return $result;
	}
    public function restore($title) {
        //get current menu
            $db= $this->getDbConnection();
            $query = "title = ".$title;
            $data = array(
                "enabled" => "1"
            );
            $result = $db->update($this->_tablename, $data,$query);
//            var_dump($result);exit();
            $this->deleteListSim();
            return $result;
    }
    public function _update($title) {
        //get current menu
            $db= $this->getDbConnection();
            $query = "title = ".$title;
            $data = array(
                "enabled" => "0"
            );
            $result = $db->update($this->_tablename, $data,$query);
//            $this->deleteDetailSim($id);
            $this->deleteListSim();
            return $result;
    }
    public function _delete($id) {
        //get current menu
            $db= $this->getDbConnection();
            $query = "itemid = ".$id;
            $data = array(
                "enabled" => "0"
            );
            $result = $db->update($this->_tablename, $data,$query);
//            var_dump($result);exit();
//            var_dump($result);exit();
            $this->_deleteAllCache();
//            $this->deleteDetailSim($id);
//            $this->deleteListSim();
            return $result;
    }
    
    private function _deleteAllCache()
    {
            $cache = $this->getCacheInstance('ws');
            $cache->flushAll();
    }
    private function deleteDetailSim($itemid) {
        $cache = $this->getCacheInstance();
        $key = $this->getKeyDetailSim($itemid);
        $cache->deleteCache($key);
    }
    private function deleteListSim($keyword="") {
        $cache = $this->getCacheInstance();
        $key = $this->getListSim($keyword);
        $cache->deleteCache($key);
    }
}

?>

<?php

class Business_Addon_AddonSim extends Business_Abstract {

    private $_tablename = 'addon_sim';
    private static $_instance = null;
    const KEY_DETAIL_SIM = 'detail_addon_sim.%s';  //key of detail.id
    const KEY_LIST_SIM = 'list_addon_sim.%s';  //key of list.id
    function __construct() {
        
    }

    //public static function
    /**
     * get instance of Business_Addon_AddonSim
     *
     * @return Business_Addon_AddonSim
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
    public function check_sim_block($sim_number,$id_addon_user){
        $db = $this->getDbConnection();
        $query = "SELECT * FROM $this->_tablename WHERE enabled =1 and block=1 and sim_number IN($sim_number) and id_addon_user = $id_addon_user order by id desc";
        $result = $db->fetchAll($query);
        return $result[0];
    }
    public function groupSimByVote(){
        $db = $this->getDbConnection();
        $query = "SELECT vote_id,count(*) as total FROM $this->_tablename WHERE enabled =1 GROUP BY vote_id;";
        $result = $db->fetchAll($query);
        return $result;
    }
    
    public function get_detail_by_id_addon_user($id_addon_user) {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " WHERE  enabled IN (0,1) and id_addon_user = $id_addon_user order by id desc ";
        $result = $db->fetchAll($query);
        return $result[0];
    }
    public function get_detail_by_sim($sim_number) {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " WHERE  enabled IN (0,1) and sim_number = '$sim_number' ";
        $result = $db->fetchAll($query);
        return $result[0];
    }
    public function get_detail_by_top($network=0) {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " WHERE block=1 and network = $network and enabled=1 and isync =1 and sync_img=2 and hopdong is NOT NULL  and cmnd_next is NOT NULL  and cmnd_pre is NOT NULL  and hakh is NOT NULL order by id asc";
        $result = $db->fetchAll($query);
        return $result[0];
    }
    public function get_detail_by_top_img($network=0) {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " WHERE block=1 and sync_img=1 and network = $network and enabled=1  and hopdong is NOT NULL  and cmnd_next is NOT NULL  and cmnd_pre is NOT NULL  and hakh is NOT NULL order by id asc";
        $result = $db->fetchAll($query);
        return $result[0];
    }
    public function getListByOption($id_addon_user="") {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " WHERE  enabled IN (0,1) ";
        if($id_addon_user != null){
            $query .=" and id_addon_user = $id_addon_user ";
        }
        $result = $db->fetchAll($query);
        return $result;
    }
    public function countSimActived($vote_id){
        $db = $this->getDbConnection();
        $query = "select count(*) from $this->_tablename where enabled = 1 and vote_id = $vote_id ";
        $result = $db->fetchAll($query);
        return $result;
    }
    public function countSim($created_day,$end_date){
        $db = $this->getDbConnection();
        $query  = "SELECT vote_id,enabled, count(*) as dem FROM `addon_sim` where  enabled IN (0,1) and createdate > '$created_day' and createdate < '$end_date' GROUP BY vote_id,enabled";
        $result = $db->fetchAll($query);
        return $result;
    }
    public function countSimByOption($created_day,$end_date,$option=""){
        $db = $this->getDbConnection();
        if($option == 'seriasim'){
            $query  = "SELECT vote_id,seria_sim, count(*) as dem FROM `addon_sim` where createdate > '$created_day' and createdate < '$end_date' group by vote_id,seria_sim = ''";
        }
        if($option == 'hopdong'){
            $query  = "SELECT vote_id,hopdong, count(*) as dem FROM `addon_sim` where createdate > '$created_day' and createdate < '$end_date' group by vote_id,hopdong = ''";
        }
        if($option == 'cmnd_next'){
            $query  = "SELECT vote_id,cmnd_next, count(*) as dem FROM `addon_sim` where createdate > '$created_day' and createdate < '$end_date' group by vote_id,cmnd_next = ''";
        }
        if($option == 'cmnd_pre'){
            $query  = "SELECT vote_id,cmnd_pre, count(*) as dem FROM `addon_sim` where createdate > '$created_day' and createdate < '$end_date' group by vote_id,cmnd_pre = ''";
        }
        if($option == 'complete'){
            $query  = "SELECT vote_id,complete, count(*) as dem FROM `addon_sim` where createdate > '$created_day' and createdate < '$end_date' group by vote_id,complete = ''";
        }
        if($option == 'active'){
            $query  = "SELECT vote_id,enabled, count(*) as dem FROM `addon_sim` where createdate > '$created_day' and createdate < '$end_date' GROUP BY vote_id,enabled";
        }
//        echo "<pre>";
//        var_dump($query);//exit();
        $result = $db->fetchAll($query);
        return $result;
    }
    public function count_sim_actived($created_day,$end_date){
        $db = $this->getDbConnection();
        $query  = "SELECT vote_id,count(*) as dem FROM `addon_sim` where updatedate > '$created_day' and updatedate < '$end_date' group by vote_id";
        
        $result = $db->fetchAll($query);
        return $result;
    }
    public function getListByDay($created_day,$end_date){
        $db = $this->getDbConnection();
        $query  = "SELECT * FROM `addon_sim` where createdate > '$created_day' and createdate < '$end_date'";
//        var_dump($query);exit();
        $result = $db->fetchAll($query);
        return $result;
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
            $query = "SELECT title, itemid,price,original_price,productsid,productscode,cateid FROM ws_sim WHERE title != '' and enabled = 1  ORDER BY title ASC";
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
            $query = "SELECT title, itemid,price,original_price,productsid,productscode,cateid FROM ws_sim WHERE title != '' and title > 999999999 and enabled = 1  and cateid = 719 ORDER BY title ASC";
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
    public function getList($vote_id,$keywork,$check_sim="",$offset=0, $records=20,$mb_id="",$start="",$end=""){
        $cache = $this->getCacheInstance();
        $key    = $this->_tablename."getList".$vote_id.$keywork.$check_sim;
        $result = $cache->getCache($key);
        $result = FALSE;
        if ($result === FALSE) {
            $db = $this->getDbConnection();
            $query = "SELECT * FROM  $this->_tablename where  enabled IN (0,1) and vote_id = $vote_id ";
            if($keywork != null){
                $query .= " and (id_addon_user like '%$keywork%' or sim_number like '%$keywork%' or seria_sim like '%$keywork%' or fullname like '%$keywork%') ";
            }
            if($check_sim != null){
                $query .=" and enabled = $check_sim ";
            }
            if($mb_id != null){
                $query .=" and users_id = $mb_id ";
            }
            if($start != NULL){
                $query .=" and updatedate >='$start' ";
            }
            if($end != NULL){
                $query .=" and updatedate <='$end' ";
            }
            $query .= " ORDER BY createdate DESC ";
            $query .= " LIMIT $records OFFSET $offset ";
//            var_dump($query);exit();
            $result = $db->fetchAll($query);
            $cache->setCache($key, $result,60*5);
            
        }
        return $result;
    }
    public function get_list($vote_id,$keywork,$check_sim="",$mb_id="",$start="",$end=""){
        $cache = $this->getCacheInstance();
        $key    = $this->_tablename."get_list".$vote_id.$keywork.$check_sim.$start.$end.$mb_id;
        $result = $cache->getCache($key);
        if ($result === FALSE) {
            $db = $this->getDbConnection();
            $query = "SELECT * FROM  $this->_tablename where  enabled IN (0,1) and vote_id = $vote_id ";
            if($keywork != null){
                $query .= " and (id_addon_user like '%$keywork%' or sim_number like '%$keywork%' or seria_sim like '%$keywork%' or fullname like '%$keywork%') ";
            }
            if($check_sim != null){
                $query .=" and enabled = $check_sim ";
            }
            if($mb_id != null){
                $query .=" and users_id = $mb_id ";
            }
            if((int)$check_sim==0){
                if($start != NULL){
                    $query .=" and createdate >='$start' ";
                }
                if($end != NULL){
                    $query .=" and createdate <='$end' ";
                }
            }else{
                if($start != NULL){
                    $query .=" and updatedate >='$start' ";
                }
                if($end != NULL){
                    $query .=" and updatedate <='$end' ";
                }
            }
            
            $query .= " ORDER BY id DESC LIMIT 500";
            $result = $db->fetchAll($query);
            $cache->setCache($key, $result,60);
            
        }
        return $result;
    }
    public function get_list_by_network($vote_id,$network,$isync=0,$start="",$end=""){
        $cache = $this->getCacheInstance();
        $key    = $this->_tablename."get_list_by_network".$vote_id.$network.$isync.$start.$end;
        $result = $cache->getCache($key);
        $result = FALSE;
        if ($result === FALSE) {
            $db = $this->getDbConnection();
//            $query = "SELECT * FROM  $this->_tablename where hopdong is NOT NULL and cmnd_next is NOT NULL and cmnd_pre is NOT NULL and hakh is NOT NULL";
            $query = "SELECT * FROM  $this->_tablename where enabled IN (0,1) ";
            if((int)$vote_id >0){
                $query .= " and vote_id = $vote_id ";
            }
            if((int)$isync >0){
                $query .= " and isync = $isync ";
            }
            if((int)$network >0){
                $query .=" and network = $network ";
            }
            if($start != NULL){
                $query .=" and createdate >'$start' ";
            }
            if($end != NULL){
                $query .=" and createdate <='$end' ";
            }
            
            $query .= " ORDER BY id DESC LIMIT 500";
           
            $result = $db->fetchAll($query);
            $cache->setCache($key, $result,60);
            
        }
        return $result;
    }
    public function getDetail($id) {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " WHERE  enabled IN (0,1) and id=?";
        $data = array($id);
        $result = $db->fetchAll($query, $data);
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
        $query = "id = ".$id;
        $result = $db->update($this->_tablename, $data,$query);
        $this->_deleteAllCache();
//        $this->deleteDetailSim($id);
//        $this->deleteListSim();
//            var_dump($result);exit();
        return $result;
    }
    public function excute($query){
            $db     =  $this->getDbConnection();
            $result = $db->query($query);
            return $result;
        }
    public function updateSeriSim($sim_number,$seri) {
        $db = $this->getDbConnection();
        $query = "sim_number = '$sim_number'";
        $data = array(
            "seria_sim" => $seri
        );
        $result = $db->update($this->_tablename, $data,$query);
        $this->_deleteAllCache();
        return $result;
    }

    public function delete($id)
	{
		$db = $this->getDbConnection();
		$where = array();
		$where[] = "id='" . parent::adaptSQL($id) . "'";
		$result = $db->delete($this->_tablename,$where);
                if($result > 0)
		{
			$this->deleteDetailSim($id);
                        $this->deleteListSim();
		}
                return $result;
	}
    public function restore($id) {
        //get current menu
            $db= $this->getDbConnection();
            $query = "itemid = ".$id;
            $data = array(
                "enabled" => "1"
            );
            $result = $db->update($this->_tablename, $data,$query);
//            var_dump($result);exit();
            $this->deleteDetailSim($id);
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
    public function getListByBillId($id_addon_user) {
         $cache = $this->getCacheInstance('ws');
            $key = "getListByBillId.$this->_tablename.$id_addon_user";
            $result = $cache->getCache($key);
//            $result= FALSE;
            if($result=== FALSE){
                $db = $this->getDbConnection();
                $query = "SELECT * FROM " . $this->_tablename . " WHERE  enabled IN (0,1) and id_addon_user=?";
                $data = array($id_addon_user);
                $result = $db->fetchAll($query, $data);
                if(!is_null($result) && is_array($result))
                    {
                        $cache->setCache($key, $result, 60);
                    }
            }
        
        
        return $result;
    }
}

?>
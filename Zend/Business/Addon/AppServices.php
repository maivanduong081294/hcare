<?php

class Business_Addon_AppServices extends Business_Abstract {

    private $_tablename = 'app_services';

    const KEY_LIST = 'appservices.list.%s';   //key of list.questionid
    const KEY_DETAIL = 'appservices.detail.%s';  //key of detail.id
    private static $_instance = null;

    function __construct() {
        
    }

    //public static function
    /**
     * get instance of Business_Addon_AppServices
     *
     * @return Business_Addon_AppServices
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
        return sprintf(self::KEY_DETAIL, $itemid);
    }
    private function getListKeyKM($keywork){
        return sprintf(self::KEY_LIST,$keywork);
    }
    
    public function getList($userid,$keywork="",$created_day,$end_day,$check_kh=0, $offset=0, $records=50){
//        var_dump($created_day);exit();
        $cache = $this->getCacheInstance();
//        $temp = md5($keywork.$userid);
        $key = $this->getListKeyKM($keywork);
//        $this->deleteListKeyKM();
//        var_dump($keywork);exit();
        if($keywork ==""){
            $result = $cache->getCache($key);
        }else{
            $result = FALSE;
        }
//        var_dump($key,$result);exit();
//        $cache->deleteCache($key);
//        $result = $cache->getCache($key);
//        var_dump($result);exit();
        $result = FALSE;
        if ($result === FALSE) {
            $db = $this->getDbConnection();
            $query = "SELECT * FROM  $this->_tablename where  userid = $userid and date_time >= '$created_day' and date_time < '$end_day'";
            
            if($check_kh !=0){
                $query .=" and check_kh ='$check_kh'";
            }
            if($keywork !=null){
                $query .=" and (bill_id ='$keywork' or fullname like '%$keywork%' or phone like '%$keywork%')";
            }
            $query.=" ORDER BY id DESC";
            $query .= " LIMIT $records OFFSET $offset ";
//            var_dump($query);exit();
            $result = $db->fetchAll($query);
            if($keywork == ""){
                $cache->setCache($key, $result);
                }
            
        }
        return $result;
    }
    public function getList2($userid,$months){
        $db = $this->getDbConnection();
        $query = "SELECT * FROM  $this->_tablename where 1= 1 and userid = $userid and MONTH(date_time) = '$months'";
        $query.=" ORDER BY id DESC";
        $query.= " LIMIT 200";
//            var_dump($query);exit();
        $result = $db->fetchAll($query);
        return $result;
    }
    public function getCountAppByVote($section="",$created_day,$end_day,$check_kh=""){
//        var_dump($created_day);exit();
        $c_day = date('Y-m-d',  strtotime($created_day));
            $e_day = date('Y-m-d',  strtotime($end_day));
        $cache = GlobalCache::getCacheInstance('event');
//        $temp = md5($keywork.$userid);
        $key = "getCountAppByVote.$this->_tablename.$c_day.$e_day.$check_kh.$section";
        $result = $cache->getCache($key);
        if ($result === FALSE) {
            $db = $this->getDbConnection();
            $query = "SELECT check_kh,count(*) as total,parentid as vote_id FROM  $this->_tablename where date_time >= '$created_day' and date_time < '$end_day'";
            if($section != null){
                $query .=" and section = $section";
            }
            if($check_kh != null){
                $query .=" and check_kh = $check_kh";
            }
            $query.=" group by parentid,check_kh";
//            var_dump($query);exit();
            $result = $db->fetchAll($query);
            if(!is_null($result) && is_array($result))
            {
                $cache->setCache($key, $result,5*60);
            }
        }
        return $result;
    }
    public function get_count_app_by_userid($section="",$created_day,$end_day){
        $cache = GlobalCache::getCacheInstance('event');
        $created_day2 = date('YmdHis',  strtotime($created_day));
        $end_day2 = date('YmdHis',  strtotime($end_day));
        $key = "get_count_app_by_userid.$this->_tablename.$created_day2.$end_day2.$section";
        $result = $cache->getCache($key);
        if ($result === FALSE) {
            $db = $this->getDbConnection();
            $query = "SELECT userid,count(*) as total,parentid  FROM  $this->_tablename where date_time >= '$created_day' and date_time < '$end_day'";
            if($section != null){
                $query .=" and section = $section";
            }
            $query.=" group by parentid,userid";
            $result = $db->fetchAll($query);
            if(!is_null($result) && is_array($result))
            {
                $cache->setCache($key, $result,30*60);
            }
        }
        return $result;
    }
    public function get_count_by_billid($str_billid,$check_kh=""){
            $db = $this->getDbConnection();
            $query = "SELECT type,userid,count(*) as total,parentid as vote_id,sum(total) as sum FROM  $this->_tablename where enabled=1 ";
            if($str_billid != null){
                $query .=" and bill_id IN ($str_billid)";
            }
            if($check_kh != null){
                $query .=" and check_kh = $check_kh";
            }
            $query.=" group by userid,type";
            $result = $db->fetchAll($query);
        return $result;
    }
    public function getAppGroupByMb($vote_id,$section="",$created_day,$end_day,$check_kh=""){
        $cache = GlobalCache::getCacheInstance('event');
//        $temp = md5($keywork.$userid);
        $key = "getAppGroupByMb.$this->_tablename.$created_day.$end_day.$check_kh.$vote_id.$section";
        $result = $cache->getCache($key);
//        $result = FALSE;
        if ($result === FALSE) {
            $db = $this->getDbConnection();
            $query = "SELECT check_kh,count(*) as total,userid  FROM  $this->_tablename where parentid = $vote_id and date_time >= '$created_day' and date_time <= '$end_day'";
            if($section != null){
                $query .=" and section = $section";
            }
            if($check_kh != null){
                $query .=" and check_kh = $check_kh";
            }
            $query.=" group by userid,check_kh";
//            var_dump($query);//exit();
            $result = $db->fetchAll($query);
            if(!is_null($result) && is_array($result))
            {
                $cache->setCache($key, $result,5*60);
            }
            
            
        }
        return $result;
    }
     public function getCountAppByDayVote($vote_id=0,$created_day,$end_day){
//        var_dump($created_day);exit();
        $cache = $this->getCacheInstance();
//        $temp = md5($keywork.$userid);
        $key = $this->getListKeyKM($vote_id);
        $result = FALSE;
        if ($result === FALSE) {
            $db = $this->getDbConnection();
            $query = "SELECT count(*) as countApp FROM  $this->_tablename where 1= 1 and date_time > '$created_day' and date_time < '$end_day' and parentid ='$vote_id'";
            $query.=" ORDER BY id DESC";
//            var_dump($query);exit();
            $result = $db->fetchAll($query);
            if($vote_id == 0){
                $cache->setCache($key, $result);
                }
            
        }
        return $result;
    }
    public function getCountAppByMBK($userid=0,$months){
        
        $db = $this->getDbConnection();
        $query = "SELECT count(*) as countApp FROM  $this->_tablename where 1= 1 and MONTH(date_time) = '$months' and userid ='$userid'";
        $query.=" ORDER BY id DESC";
//            var_dump($query);exit();
        $result = $db->fetchAll($query);
        return $result;
    }
    public function getCountAppByMBKByDay($userid=0,$created_day,$end_day){
        
        $db = $this->getDbConnection();
        $query = "SELECT count(*) as countApp FROM  $this->_tablename where 1= 1 and date_time > '$created_day' and date_time < '$end_day' and userid ='$userid'";
        $query.=" ORDER BY id DESC";
//            var_dump($query);exit();
        $result = $db->fetchAll($query);
        return $result;
    }
    

    public function getDetail($id) {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " WHERE id=?";
        $data = array($id);
        $result = $db->fetchAll($query, $data);
        return $result[0];
    }
    public function getListByImei($imei,$type) {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " WHERE enabled =1 and imei = '$imei' and type = $type";
        $result = $db->fetchAll($query);
        return $result;
    }
    public function getDetail2($id) {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " WHERE bill_id=?";
        $data = array($id);
        $result = $db->fetchAll($query, $data);
        return $result[0];
    }
    public function getListByBillId($id) {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " WHERE bill_id=?";
        $data = array($id);
        $result = $db->fetchAll($query, $data);
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

    public function delete2($id) {
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
                "enabled" => "1"
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

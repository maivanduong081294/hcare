<?php

class Business_Addon_Services extends Business_Abstract {

    private $_tablename = 'services';

    const KEY_LIST = 'services.list.%s';   //key of list.questionid
    const KEY_DETAIL = 'services.detail.%s';  //key of detail.id
    private static $_instance = null;

    function __construct() {
        
    }

    //public static function
    /**
     * get instance of Business_Addon_Services
     *
     * @return Business_Addon_Services
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
   private function getDbConnection()
    {  
     $db     = Globals::getDbConnection('maindb');  
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
    
    private function getKeyKM($itemid){
        return sprintf(self::KEY_DETAIL, $itemid);
    }
    private function getListKeyKM($keywork){
        return sprintf(self::KEY_LIST,$keywork);
    }
     public function getTkListByDay($c_datetime,$e_datetime,$type,$enabled,$p=0){
        $cache = $this->getCacheInstance();
        $key = "getTkListByDay".$this->_tablename.$c_datetime.$e_datetime.$type.$enabled.$p;
        $result = $cache->getCache($key);
//        $result = FALSE;
        if($result ==FALSE){
            $db = $this->getDbConnection();
            $query = "select count(*) as total,sum(price) as total_price,vote_id from $this->_tablename where datetime >= '$c_datetime' and datetime < '$e_datetime' and type  = $type and enabled = $enabled";
            if($p!=0){
                $query .=" and price = $p";
            }
            $query .=" group by vote_id";
//            var_dump($query);//exit();
            $result = $db->fetchAll($query);
            if($result != null && is_array($result))
            {
                $cache->setCache($key, $result,5*60);
            }
        }
        return $result;
        
    }
    
    public function getDetailBillId($id_addon_user="",$type="") {
        $cache = $this->getCacheInstance();
        $key = "getDetailBillId".  $this->_tablename.$id_addon_user.$type;
        $result = $cache->getCache($key);
        if($result ===FALSE){
            $db = $this->getDbConnection();
            $query = "SELECT * FROM " . $this->_tablename . " WHERE 1=1  ";
            if($id_addon_user !=NULL){
                $query .=" and id_addon_user='$id_addon_user'";
            }
            if($type !=NULL){
                $query .=" and type='$type'";
            }
            $query .=" order by datetime DESC";
            $result = $db->fetchAll($query);
            $result = $result[0];
            if($result != null && is_array($result))
            {
                $cache->setCache($key, $result,20*60);
            }
        }
        return $result;
    }
    public function getDetailByImei($imei="",$type="") {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " WHERE 1=1  ";
        if($imei !=NULL){
            $query .=" and imei='$imei' ";
        }
        if($type !=NULL){
            $query .=" and type='$type'";
        }
        $query .=" order by datetime DESC";
//        var_dump($query);exit();
        $result = $db->fetchAll($query);
        return $result[0];
    }
    public function getListByDay($storeid,$c_datetime,$e_datetime,$type="",$enabled){
        $cache = $this->getCacheInstance();
        $key = "getListByDay".$this->_tablename.$c_datetime.$e_datetime.$storeid.$type.$enabled;
        $result = $cache->getCache($key);
        if($result ===FALSE){
            $db = $this->getDbConnection();
            $query = "select * from $this->_tablename where vote_id = $storeid and datetime >= '$c_datetime' and datetime <= '$e_datetime'  and enabled = $enabled";
            if($type!= null){
                $query .=" and type  = $type";
            }
            $result = $db->fetchAll($query);
            if(!is_null($result) && is_array($result))
            {
                $cache->setCache($key, $result,60*10);
            }
        }
        return $result;
        
    }
    public function getListByDay2($userid=0,$storeid=0,$keyword="",$c_datetime,$e_datetime,$type,$enabled="",$price_ios=0){
        $cache = $this->getCacheInstance();
        $key = "getListByDay2".$this->_tablename.$c_datetime.$e_datetime.$userid.$storeid.$type.$enabled.$keyword.$price_ios;
//        var_dump($key);exit();
        $result = $cache->getCache($key);
        $result = FALSE;
        if($result ==FALSE){
            $db = $this->getDbConnection();
            $query = "select * from $this->_tablename where  datetime >= '$c_datetime' and datetime <= '$e_datetime' and type  = $type and enabled != 0 ";
            if($userid !=0){
                $query .=" and userid = $userid";
            }
            $query .=" and vote_id = $storeid ";
            if($enabled !=null){
                $query .=" and enabled = $enabled";
            }
            if($price_ios !=0){
                $query .=" and price = $price_ios";
            }
            if($keyword != null){
                $query .=" and (imei like '%$keyword%' or id_addon_user = '$keyword')";
            }
            $query .=" order by datetime desc";
//            echo "<pre>";
//            var_dump($query);
//            die();
            $result = $db->fetchAll($query);
            if($result != null && is_array($result))
            {
                $cache->setCache($key, $result,60);
            }
        }
        return $result;
        
    }
    public function getListByDay3($userid=0,$storeid=0,$keyword="",$c_datetime,$e_datetime,$type,$enabled="",$price_ios=0){
            $db = $this->getDbConnection();
            $query = "select *,t1.id as ids from $this->_tablename t1,users_products t2 where  t1.datetime >= '$c_datetime' and t1.datetime < '$e_datetime' and t1.type  = $type and t1.enabled != 0 ";
            if($userid !=0){
                $query .=" and t1.userid = $userid";
            }
            if((int)$storeid >0){
                $query .=" and t1.vote_id = $storeid ";
            }
            if($enabled !=null){
                $query .=" and t1.enabled = $enabled";
            }
            if($price_ios !=0){
                $query .=" and t1.price = $price_ios";
            }
            if($keyword != null){
                $query .=" and (t1.imei like '%$keyword%' or t1.id_addon_user = '$keyword')";
            }
            $query .="  and t2.is_actived=1 and t1.id_addon_user = t2.id_addon_user and t2.status2=0 group by t2.id_addon_user";
//            echo "<pre>";
//            var_dump($query);
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
    
    public function insert($data) {
        $db = $this->getDbConnection();
        $result = $db->insert($this->_tablename, $data);
        $this->deleteListKeyKM();
        return $result;
    }
    public function update($id,$data) {
        $db= $this->getDbConnection();
        $query = "id = ".$id;
        $result = $db->update($this->_tablename, $data,$query);
//        $this->deleteKeyKM($id);
//        $this->deleteListKeyKM();
//            var_dump($result);exit();
        return $result;
    }
    public function updateByBillId($id_addon_user,$data) {
        $db= $this->getDbConnection();
        $query = "id_addon_user = ".$id_addon_user;
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

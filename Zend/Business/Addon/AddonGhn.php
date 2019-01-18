<?php

class Business_Addon_AddonGhn extends Business_Abstract {

    private $_tablename = 'addon_ghn';

    const KEY_LIST = 'addon_ghn.list.%s';   //key of list.questionid
    const KEY_DETAIL = 'addon_ghn.detail.%s';  //key of detail.id
    const KEY_DETAIL_KM = 'km.%s';  //key of detail.id
    const KEY_DETAIL_LIST_KM = 'getlistaddon_ghn.%s';  //key of list.id

    private static $_instance = null;

    function __construct() {
        
    }

    //public static function
    /**
     * get instance of Business_Addon_AddonGhn
     *
     * @return Business_Addon_AddonGhn
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
    public function getList($keywork="",$status="",$day_created="",$day_end="",$offset=1, $records=50){
        $db = $this->getDbConnection();
        $query = "SELECT * FROM  $this->_tablename where 1= 1 ";
        if($keywork != null){
            $query .="  and ( client_order_code like '%$keywork%' or order_code like '%$keywork%' ) ";
        }
        if($day_created != null){
            $query .="  and datetime > '$day_created' ";
        }
        if($day_end != null){
            $query .="  and datetime < '$day_end' ";
        }
        if($status != null){
            $query .="  and status = $status ";
        }
        $query.=" ORDER BY id DESC";
        $query .= " LIMIT $records OFFSET $offset ";
//        echo "<pre>";
//        var_dump($query);
//        exit();
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
        $query = "itemid = ".$id;
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

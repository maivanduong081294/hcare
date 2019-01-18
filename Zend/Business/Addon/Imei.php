<?php

class Business_Addon_Imei extends Business_Abstract {

    private $_tablename = 'ws_imei';

    const KEY_LIST = 'ws_imei.list.%s';   //key of list.questionid
    const KEY_DETAIL = 'ws_imei.detail.%s';  //key of detail.id
    const KEY_DETAIL_KM = 'km.%s';  //key of detail.id
    const KEY_DETAIL_LIST_KM = 'getlistws_imei.%s';  //key of list.id

    private static $_instance = null;

    function __construct() {
        
    }

    //public static function
    /**
     * get instance of Business_Addon_Imei
     *
     * @return Business_Addon_Imei
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
    public function get_list_by_imei_new($imei){
        $db = $this->getDbConnection();
        $query = "select imei_new,createdate,id_addon_user,storeid from $this->_tablename where imei_new like '%$imei%'";
        $result = $db->fetchAll($query);
        return $result;
    }
    public function get_detail_by_imei_new($imei){
        $db = $this->getDbConnection();
        $query = "select * from $this->_tablename where imei_new = '$imei' order by id desc";
        $result = $db->fetchAll($query);
        return $result[0];
    }
    public function get_detail_by_imei_new2($imei,$id_addon_user){
        $db = $this->getDbConnection();
        $query = "select * from $this->_tablename where imei_new = '$imei' and id_addon_user = $id_addon_user order by id desc";
        $result = $db->fetchAll($query);
        return $result[0];
    }
    public function get_list_by_date_out($storeid=0,$start="",$end=""){
        $key = "get_list_by_date_out".$storeid.$start.$end;
        $cache = $this->getCacheInstance();
        $result = $cache->getCache($key);
        if($result ===FALSE){
            $db = $this->getDbConnection();
            $query = "select * from $this->_tablename where enabled = 1 ";
            if((int)$storeid >0){
                $query .=" and storeid = $storeid ";
            }
            if($start != NULL){
                $query .=" and createdate > '$start' ";
            }
            if($end != NULL){
                $query .=" and createdate <= '$end' ";
            }
            $result = $db->fetchAll($query);
            if(!is_null($result) && is_array($result)){
                $cache->setCache($key, $result,600);
            }
        }
        
        return $result;
    }
    public function get_list_by_active_vendor($_productsid=0,$strID,$start,$end,$storeid=0,$active_vendor=0){
            $db = $this->getDbConnection();
            $query = "select * from $this->_tablename where enabled =1 and cated_id IN ($strID) and active_vendor =$active_vendor ";
            if((int)$_productsid >0){
                $query .= " and productsid = $_productsid ";
            }
            
            if((int)$storeid >0){
                $query .= " and storeid = $storeid ";
            }
            $query .= " and createdate >= '$start' and createdate <= '$end' ";
            
            $result = $db->fetchAll($query);
            
            return $result;
        }
    public function get_list_by_imei_old($imei){
            $db = $this->getDbConnection();
            $query = "select * from $this->_tablename where imei_old like '%$imei%' or imei_new like '%$imei%'";
            $result = $db->fetchAll($query);
            return $result;
        }
    public function get_list_by_productsid($str_productsid){
            $db = $this->getDbConnection();
            $query = "select * from $this->_tablename where products_id IN ($str_productsid)";
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
    public function get_detail_by_idaddonuser($id_addon_user) {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " WHERE id_addon_user= $id_addon_user and enabled = 1 order by id desc";
        $result = $db->fetchAll($query);
        return $result[0];
    }
   
    public function insert($data) {
        $db = $this->getDbConnection();
        $result = $db->insert($this->_tablename, $data);
        return $result;
    }
    public function update($id,$data) {
        $db= $this->getDbConnection();
        $query = "id = ".$id;
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

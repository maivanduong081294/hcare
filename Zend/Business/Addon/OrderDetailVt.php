<?php

class Business_Addon_OrderDetailVt extends Business_Abstract
{
    private $_identity;
	private $_tablename = 'quynhn_order_detail';
	private $_tablename_history = 'quynhn_order_detail_history';
	private static $_instance = null; 
        private $_timecache = 120;
	function __construct()
	{
		$auth = Zend_Auth::getInstance(); 
                $identity = (array)$auth->getIdentity();
                $this->_identity = $identity;
	}
	
	/**
	 * get instance of Business_Addon_OrderDetailVt
	 *
	 * @return Business_Addon_OrderDetailVt
	 */
	public static function getInstance() 
	{
		if(self::$_instance == null)
		{
			self::$_instance = new Business_Addon_OrderDetailVt();
		}
		return self::$_instance;
	}
	
	/**
	 * get Zend_Db connection
	 *
	 * @return Zend_Db_Adapter_Abstract
	 */
        function get_base_url(){
            return alias_domain;  
        }
	function getDbConnection()
        {		
                $db    	= Globals::getDbConnection('hnamvt', false);
                return $db;	
        }
	public function getCacheInstance() {
            $cache = GlobalCache::getCacheInstance('pos');
            return $cache;
        }
        private function get_key_list($str)
	{
            $KEY_LIST = $this->get_base_url().'list.'.$this->_tablename.'.%s';
            return sprintf($KEY_LIST,$str);
	}
        private function get_key_detail($id)
	{
            $KEY_DETAIL = $this->get_base_url().'detail.'.$this->_tablename.'.%s';
            return sprintf($KEY_DETAIL,$id);
	}
        public function delete_all_cache(){
            $cache = $this->getCacheInstance();
            $cache->flushAll();
            Business_Addon_Options::getInstance()->flush_cache(5);
        }
        public function delete_key_list($str=""){
            $cache = $this->getCacheInstance();
            $key = $this->get_key_list($str);
            $cache->deleteCache($key);
            Business_Addon_Options::getInstance()->flush_cache(5);
        }
        public function delete_key_detail($id){
            $cache = $this->getCacheInstance();
            $key = $this->get_key_detail($id);
            $cache->deleteCache($key);
            Business_Addon_Options::getInstance()->flush_cache(5);
        }
        
	public function get_list_by_orderid($orderid)
	{
            $cache = $this->getCacheInstance();
            $strs = $this->get_base_url()."get_list_by_orderid.$orderid";
            $key = $this->get_key_list($strs);
            $result=$cache->getCache($key);
            if($result === FALSE)
            {
                $db = $this->getDbConnection();
                $query = "select * from " . $this->_tablename . " where enabled >0 and orderid IN ($orderid)";
                $result = $db->fetchAll($query);
                if(!is_null($result) && is_array($result)){
                    $cache->setCache($key, $result);
                }
            }
            return $result;
	}
	public function get_list_avg($orderid,$itemid)
	{
            $db = $this->getDbConnection();
            $query = "select * from " . $this->_tablename . " where orderid != $orderid and ( (isale=0 and avg>0) or (isale=1 and avg=0) )";
            $result = $db->fetchAll($query);
            return $result;
	}
	
	public function get_list_by_id($strid)
	{
            $cache = $this->getCacheInstance();
            $strs = $this->get_base_url()."get_list_by_id.$strid";
            $key = $this->get_key_list($strs);
            $result=$cache->getCache($key);
            if($result === FALSE)
            {
                $db = $this->getDbConnection();
                $query = "select * from " . $this->_tablename . " where enabled >0 and id IN ($strid)";
                $result = $db->fetchAll($query);
                if(!is_null($result) && is_array($result)){
                    $cache->setCache($key, $result);
                }
            }
            return $result;
	}
	public function get_list_by_orderid_import($q,$userid=0,$storeid=0,$itype)
	{
            $cache = $this->getCacheInstance();
            $strs = $this->get_base_url()."get_list_by_orderid_import.$q.$userid.$storeid";
            $key = $this->get_key_list($strs);
            $result=$cache->getCache($key);
            if($result === FALSE)
            {
                $db = $this->getDbConnection();
                $query = "select * from " . $this->_tablename . " where enabled >0  and status_import=1 and isale=0";
                if((int)$itype==0){
                    $query .=" and orderid IN ($q)";
                }else{
                    $query .=" and imei like '%$q%'";
                }
                if((int)$userid >0){
                    $query .=" and userid = $userid";
                }
                if((int)$storeid >0){
                    $query .=" and storeid = $storeid";
                }
//                echo "<pre>";
//                var_dump($query);
//                die();
                $result = $db->fetchAll($query);
                if(!is_null($result) && is_array($result)){
                    $cache->setCache($key, $result, $this->_timecache);
                }
            }
            return $result;
	}
	public function get_list_warehouse($storeid=0)
	{
            $cache = $this->getCacheInstance();
            $strs = $this->get_base_url()."get_list_warehouse.$storeid";
            $key = $this->get_key_list($strs);
            $result=$cache->getCache($key);
            if($result === FALSE)
            {
                $db = $this->getDbConnection();
                $query = "select * from " . $this->_tablename . " where enabled >0  and status_import=1 and isale=0 and status_move=0";
                if((int)$storeid >0){
                    $query .=" and storeid = $storeid";
                }
                $result = $db->fetchAll($query);
                if(!is_null($result) && is_array($result)){
                    $cache->setCache($key, $result, $this->_timecache);
                }
            }
            return $result;
	}
	public function get_list_by_orderid2($orderid)
	{
            $cache = $this->getCacheInstance();
            $strs = $this->get_base_url()."get_list_by_orderid2.$orderid";
            $key = $this->get_key_list($strs);
            $result=$cache->getCache($key);
            if($result === FALSE)
            {
                $db = $this->getDbConnection();
                $query = "select * from " . $this->_tablename . " where  orderid IN ($orderid)";
                $result = $db->fetchAll($query);
                if(!is_null($result) && is_array($result)){
                    $cache->setCache($key, $result);
                }
            }
            return $result;
	}
	
	public function get_detail($id)
	{
            $cache = $this->getCacheInstance();
            $key = $this->get_key_detail($id);
            $result=$cache->getCache($key);
            if($result === FALSE)
            {
                $id = intval($id);
                $db = $this->getDbConnection();
                $query = " SELECT * FROM " . $this->_tablename ." WHERE id = $id";
                $result = $db->fetchAll($query);
                $result = $result[0];
                if(!is_null($result) && is_array($result)){
                    $cache->setCache($key, $result);
                }
            }
            return $result;
	}
	public function get_detail_by_imei($imei)
	{
            $db = $this->getDbConnection();
            $query = " SELECT * FROM " . $this->_tablename ." WHERE enabled=1 and imei = '$imei' and isale=0  order by id desc";
            $result = $db->fetchAll($query);
            return $result[0];
	}
	public function get_list_by_imei($imei)
	{
            $db = $this->getDbConnection();
            $query = " SELECT * FROM " . $this->_tablename ." WHERE enabled=1 and imei IN ($imei) and isale=0 order by id desc";
            $result = $db->fetchAll($query);
            return $result;
	}
	public function get_list_by_warehouse($itemid=0,$storeid=0) 
	{
            $db = $this->getDbConnection();
            $query = " SELECT * FROM " . $this->_tablename ." WHERE enabled=1 and isale=0 and status_import = 1";
            if((int)$itemid >0){
                $query .=" and itemid = $itemid";
            }
            if((int)$storeid >0){
                $query .=" and storeid = $storeid";
            }
            $query .="  order by id desc";
            $result = $db->fetchAll($query);
            return $result;
	}
	public function insert($data)
	{
            $db = $this->getDbConnection();
            $result = $db->insert($this->_tablename,$data);
            if ($result > 0) {
                $lastid= $db->lastInsertId($this->_tablename); // tra ve id khi them vao
            }
            $this->delete_all_cache();
            return $lastid;
	}
	public function insert_history($data)
	{
            $db = $this->getDbConnection();
            $result = $db->insert($this->_tablename_history,$data);
            if ($result > 0) {
                $lastid= $db->lastInsertId($this->_tablename_history); // tra ve id khi them vao
            }
            return $lastid;
	}
        public function excute($query){
            $db     =  $this->getDbConnection();
            $result = $db->query($query);
            return $result;
        }
        public function update($id,$data) {
            $db= $this->getDbConnection();
            $query = "id = ".$id;
            $result = $db->update($this->_tablename, $data,$query);
//            $this->delete_key_detail($id);
            $this->delete_all_cache();
            return $result;
        }
        public function update_history($id,$data) {
            $db= $this->getDbConnection();
            $query = "id = ".$id;
            $result = $db->update($this->_tablename_history, $data,$query);
            $this->delete_all_cache();
            return $result;
        }

        public function getList(){
            $db= $this->getDbConnection();
            $query = "select itemid,imei,storeid from hnam_vt.quynhn_order_detail where enabled=1 and isale=0 and imei != '' and itemid IN ( SELECT distinct(h.itemid_vt) FROM hnam_live.`addon_product_color` as h where h.itemid_vt>0 )";
            $result = $db->fetchAll($query);
            return $result;
        }
        public function getListMakhoTam($listItemid){
            $db= $this->getDbConnection();
            $query = "select * from `quynhn_productsitem`  where itemid in ($listItemid) ";
            $result = $db->fetchAll($query);
            return $result;
        }


}

?>
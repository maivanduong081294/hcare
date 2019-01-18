<?php

class Business_Addon_Bill extends Business_Abstract
{
	private $_tablename = 'quynhn_bill';
        private $_timecache = 120;
	private static $_instance = null; 
	
	function __construct()
	{
		
	}
	
	/**
	 * get instance of Business_Addon_Bill
	 *
	 * @return Business_Addon_Bill
	 */
	public static function getInstance()
	{
		if(self::$_instance == null)
		{
			self::$_instance = new Business_Addon_Bill();
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
//            Business_Addon_Options::getInstance()->flush_cache(5);
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
       public function get_list_by_id($strid)
	{
            $db = $this->getDbConnection();
            $query = " SELECT * FROM " . $this->_tablename ." WHERE enabled=1";
            if($strid != NULL){
                $query .=" and id IN ($strid)";
            }
            $result = $db->fetchAll($query);
            return $result;
	}
	public function get_list($q,$start="" ,$end="",$userid=0 )
	{
            $cache = $this->getCacheInstance();
            $key = $this->get_key_list($q.$start.$end.$userid);
            $result=$cache->getCache($key);
            if($result === FALSE)
            {
                $db = $this->getDbConnection();
                $query = "select *,DATE(datetime) as dates from " . $this->_tablename . " where enabled =1 ";
                if($q != NULL){
                    $query .=" and (fullname like '%$q%' or id = '$q')";
                }
                if((int)$userid >0){
                    $query .=" and userid  = $userid";
                }
                if($start){
                    $query .=" and datetime > '$start' and datetime <= '$end'";
                }
                $query .=" order by id desc";
                $result = $db->fetchAll($query);
                if(!is_null($result) && is_array($result)){
                    $cache->setCache($key, $result,  $this->_timecache);
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
                    $cache->setCache($key, $result,  $this->_timecache);
                }
            }
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
        public function excute($query){
            $db     =  $this->getDbConnection();
            $result = $db->query($query);
            $this->delete_all_cache();
            return $result;
        }
        public function update($id,$data) {
            $db= $this->getDbConnection();
            $query = "id = ".$id;
            $result = $db->update($this->_tablename, $data,$query);
            $this->delete_all_cache();
            return $result;
        }
}

?>
<?php

class Business_Addon_Comment extends Business_Abstract
{
	private $_tablename = 'hnam_comment';
        private $_timecache = 120;
	private static $_instance = null; 
	
	function __construct()
	{
		
	}
	
	/**
	 * get instance of Business_Addon_Comment
	 *
	 * @return Business_Addon_Comment
	 */
	public static function getInstance()
	{
		if(self::$_instance == null)
		{
			self::$_instance = new Business_Addon_Comment();
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
		$db    	= Globals::getDbConnection('maindb', false);
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
        }
        public function delete_key_detail($id){
            $cache = $this->getCacheInstance();
            $key = $this->get_key_detail($id);
            $cache->deleteCache($key);
            Business_Addon_Options::getInstance()->flush_cache(5);
        }
        public function count_iview($iview=1){
            $cache = $this->getCacheInstance();
            $strs = $this->get_base_url().'count_iview'.$iview;
            $key = $this->get_key_list($strs);
            $result = $cache->getCache($key);
            if($result === FALSE){
                $db = $this->getDbConnection();
                $query = "select count(*) as total from " . $this->_tablename . " where iview =$iview and enabled =1";
                $query .=" order by id desc";
                $result = $db->fetchAll($query);
                $result = $result[0];
                if(!is_null($result) && is_array($result))
                {
                    $cache->setCache($key, $result,  $this->_timecache);
                }
            }
            return $result;
        }
        public function get_detail_send_email(){
            $cache = $this->getCacheInstance();
            $key = $this->_tablename."get_detail_send_email";
            $result=$cache->getCache($key);
            $result = FALSE;
            if($result === FALSE)
            {
                $db = $this->getDbConnection();
                $query = " SELECT * FROM " . $this->_tablename ." WHERE send = 0 order by id asc";
                $result = $db->fetchAll($query);
                $result = $result[0];
                if(!is_null($result) && is_array($result)){
                    $cache->setCache($key, $result,  $this->_timecache);
                }
            }
            return $result;
        }
        public function get_list_view_by_phone($phone){
            $cache = $this->getCacheInstance();
            $key = $this->_tablename."get_list_by_phone".$phone;
            $result=$cache->getCache($key);
            $result = FALSE;
            if($result === FALSE)
            {
                $db = $this->getDbConnection();
                $query = " SELECT * FROM " . $this->_tablename ." WHERE phone ='$phone' and pid =0 and iview=0 order by id asc";
                $result = $db->fetchAll($query);
                if(!is_null($result) && is_array($result)){
                    $cache->setCache($key, $result,  $this->_timecache);
                }
            }
            return $result;
        }
        public function get_list_view_by_pid($pid){
            $cache = $this->getCacheInstance();
            $key = $this->_tablename."get_list_view_by_pid".$pid;
            $result=$cache->getCache($key);
            $result = FALSE;
            if($result === FALSE)
            {
                $db = $this->getDbConnection();
                $query = " SELECT * FROM " . $this->_tablename ." WHERE pid IN ($pid) and iview=0  order by id asc";
                $result = $db->fetchAll($query);
                if(!is_null($result) && is_array($result)){
                    $cache->setCache($key, $result,  $this->_timecache);
                }
            }
            return $result;
        }
	public function get_list_by_itemid($itemid)
	{
            $cache = $this->getCacheInstance();
            $key = $this->get_key_list($itemid);
            $result=$cache->getCache($key);
            if($result === FALSE)
            {
                $db = $this->getDbConnection();
                $query = "select * from " . $this->_tablename . " where enabled =1 ";
                if((int)$itemid >0){
                    $query.= " and itemid = $itemid";
                }
                $query .=" order by id desc";
//                if((int)$limit >0){
//                    $query.= " LIMIT 0,$limit";
//                }
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
            $this->delete_key_list($data["itemid"]);
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
            return $result;
        }
}

?>
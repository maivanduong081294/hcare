<?php

class Business_Addon_ProductsColor extends Business_Abstract
{
	private $_tablename = 'addon_product_color';
	
	const KEY_LIST              = 'addon_product_color.list.%s';
	const KEY_LIST2             = 'addon_product_color.list2.%s';
	const KEY_DETAIL = 'addon_product_color.uid.%s';
    
	protected static $_current_rights = null;
	
	protected static $_instance = null; 

	function __construct()
	{
		
	}
	
	/**
	 * get instance of Business_Addon_ProductsColor
	 *
	 * @return Business_Addon_ProductsColor
	 */
	public static function getInstance()
	{
		if(self::$_instance == null)
		{
			self::$_instance = new self();
		}
		return self::$_instance;
	}
        private function getKeyList($itemid){
        return sprintf(self::KEY_LIST, $itemid);
        }
        
        private function getKeyDetail($keywork){
            return sprintf(self::KEY_DETAIL,$keywork);
        }
        private function deleteKeyDetail($itemid) {
        $cache = $this->getCacheInstance();
        $key = $this->getKeyDetail($itemid);
        $cache->deleteCache($key);
        }
        private function deleteKeyList($keyword="") {
            $cache = $this->getCacheInstance();
            $key = $this->getKeyList($keyword);
            $cache->deleteCache($key);
        }
        
        private function getKeyList2($keys){
        return sprintf(self::KEY_LIST2, $keys);
        }
        private function deleteKeyList2($keys=0) {
            $cache = $this->getCacheInstance();
//            $key = $this->getKeyList2($keys);
            $cache->deleteCache($keys);
        }
        
        
        
	/**
	 * get Zend_Db connection
	 *
	 * @return Zend_Db_Adapter_Abstract
	 */
	function getDbConnection()
	{		
		$db    	= Globals::getDbConnection('maindb', false);
		return $db;	
	}
	
	/**
	 * Enter description here...
	 *
	 * @return Maro_Cache
	 */
	function getCacheInstance()
	{
		$cache = GlobalCache::getCacheInstance('event');		
		return $cache;
	}
       
   public function getListByIMEI() {
		$db = $this->getDbConnection();
		$query = "SELECT itemid,code FROM $this->_tablename";
		$result = $db->fetchAll($query);
		return $result;
	}
	
	public function getListByIMEI2() {
		$db = $this->getDbConnection();
		//$query = 'SELECT distinct(id_product) as itemid, id_material as code, id_color FROM `app_mapping_product` where follow_imei = 1 and id_material != ""';
		$query = 'SELECT distinct(id_product) as itemid, id_material as code, id_color FROM `app_mapping_product` where id_material != ""';
		$result = $db->fetchAll($query);
		return $result;
	}
	
	public function getListImeiByIdAndColor($itemid, $colorid) {
		$db = $this->getDbConnection();
		$query = "SELECT imei FROM `app_mapping_product` WHERE id_product=$itemid AND id_color = $colorid";
		$result = $db->fetchAll($query);
		return $result;
	}
	
        public function get_list_by_id2($id)
	{
                $db = $this->getDbConnection();
                $query = "SELECT * FROM $this->_tablename where itemid IN ($id) and state = 1 and code IS NOT NULL and code !='' group by code ";
//                echo "<pre>";
//                var_dump($query);
                $result = $db->fetchAll($query);
		return $result;		
	}
        public function get_list_by_id3($id)
	{
                $db = $this->getDbConnection();
                $query = "SELECT * FROM $this->_tablename where itemid IN ($id) and state = 1 and code IS NOT NULL and code !='' group by itemid ";
                $result = $db->fetchAll($query);
		return $result;		
	}
        public function get_list_by_id($id)
	{
            $cache = $this->getCacheInstance();
            $key= $this->get_key_list_by_id($id);
            $result = $cache->getCache($key);
            if($result === FALSE)
            {
                $db = $this->getDbConnection();
                $query = "SELECT * FROM $this->_tablename where itemid IN ($id) and state = 1 ";
                $result = $db->fetchAll($query);
                if(!is_null($result) && is_array($result))
                {
                    $cache->setCache($key, $result,120);
                }
            }

            return $result;		
	}
        public function get_list_by_idv2($id,$type='')
	{
            $cache = $this->getCacheInstance();
            $key= $this->get_key_list_by_id($id);
            $key .= ''.$type;
            $result = $cache->getCache($key);
            //$result = false;
            if($result === FALSE)
            {
                if($type == 1) {
                    $where = ' and itemid_vt != 0';
                }
                elseif($type == 2) {
                    $where = ' and itemid_vt = 0';
                }
                else {
                    $where = '';
                }
                $db = $this->getDbConnection();
                $query = "SELECT * FROM $this->_tablename where itemid IN ($id) and state = 1 $where";
                $result = $db->fetchAll($query);
                if(!is_null($result) && is_array($result))
                {
                    $cache->setCache($key, $result,120);
                }
            }

            return $result;
	}
        public function get_detail_by_id($id)
	{
            $cache = $this->getCacheInstance();
            $key = "get_detail_by_id".$this->_tablename.$id;
            $result = $cache->getCache($key);
            if($result === FALSE)
            {
                $db = $this->getDbConnection();
                $query = "SELECT * FROM $this->_tablename where itemid = $id and state = 1 ";
                $result = $db->fetchAll($query); 
                $result = $result[0];
                if(!is_null($result) && is_array($result))
                {
                    $cache->setCache($key, $result,60);
                }
            }
		return $result[0];		
	}
        public function get_detail_by_id_vt($id)
	{
            $cache = $this->getCacheInstance();
            $key = "get_detail_by_id_vt".$this->_tablename.$id;
            $result = $cache->getCache($key);
            if($result === FALSE)
            { 
                $db = $this->getDbConnection();
                $query = "SELECT * FROM $this->_tablename where itemid_vt = $id and state = 1 ";
                $result = $db->fetchAll($query); 
                $result = $result[0];
                if(!is_null($result) && is_array($result))
                {
                    $cache->setCache($key, $result,60);
                }
            }
		return $result;		
	}
   public function get_list_by_id_vt($id)
	{
            $cache = $this->getCacheInstance();
            $key = "get_list_by_id_vt".$this->_tablename.$id;
            $result = $cache->getCache($key);
            if($result === FALSE)
            {
                $db = $this->getDbConnection();
                $query = "SELECT * FROM $this->_tablename where itemid_vt IN ($id) and state = 1 ";
                $result = $db->fetchAll($query); 
                if(!is_null($result) && is_array($result))
                {
                    $cache->setCache($key, $result,60);
                }
            }
		return $result;		
    }
    
    public function get_list_by_id_vt_console($id)
	{
        $db = $this->getDbConnection();
        $query = "SELECT * FROM $this->_tablename where itemid_vt IN ($id)  ";
        $result = $db->fetchAll($query); 
		return $result;		
    }



        public function get_key_detail_by_id_color($id=0,$colorid=0){
            $key = "get_detail_by_id_colors".$this->_tablename.$id.$colorid;
            return $key;
        }
        public function delete_key_detail_by_id_color($itemid=0,$colorid=0){
            $cache = $this->getCacheInstance();
            $key = $this->get_key_detail_by_id_color($itemid,$colorid);
            $cache->deleteCache($key);
        }
        public function get_key_list_by_id($id=0){
            $key = "get_list_by_ids".$this->_tablename.$id;
            return $key;
        }
        public function delete_key_list_by_id($itemid=0){
            $cache = $this->getCacheInstance();
            $key = $this->get_key_list_by_id($itemid);
            $cache->deleteCache($key);
        }
        public function get_detail_by_id_color($id,$colorid)
	{
            $cache = $this->getCacheInstance();
            $key = $this->get_key_detail_by_id_color($id, $colorid);
            $result = $cache->getCache($key);
            if($result === FALSE)
            {
                $db = $this->getDbConnection();
                $query = "SELECT * FROM $this->_tablename where itemid = $id and colorid = $colorid and state = 1 ";
                $result = $db->fetchAll($query);
                $result = $result[0];
                if(!is_null($result) && is_array($result))
                {
                    $cache->setCache($key, $result);
                }
            }
            return $result;		
	}
        public function get_detail_by_id_color2($id,$colorid)
	{
            $cache = $this->getCacheInstance();
            $key = "get_detail_by_id_color2s".$this->_tablename.$id.$colorid;
            $result = $cache->getCache($key);
            if($result === FALSE)
            {
                $db = $this->getDbConnection();
                $query = "SELECT * FROM $this->_tablename where itemid = $id and colorid = $colorid  ";
                $result = $db->fetchAll($query);
		$result = $result[0];
                if(!is_null($result) && is_array($result))
                {
                    $cache->setCache($key, $result,60);
                }
            }
            return $result;		
	}
	public function getListById($id)
	{
		$cache = $this->getCacheInstance();
		$key= $this->get_key_list_by_id($id);
		$result = $cache->getCache($key);
		if($result === FALSE)
		{
                    $db = $this->getDbConnection();
                    $query = "SELECT * FROM $this->_tablename where itemid IN ($id) and state = 1 ";
                    $result = $db->fetchAll($query);
                    $cache->setCache($key, $result,3600);
		}
		
		return $result;		
	}
	public function getListById2($id)
	{
		$cache = $this->getCacheInstance();
		$key = 'getListById2'.$this->_tablename.$id;
		$result = $cache->getCache($key);
		$result = FALSE;				
		if($result === FALSE)
		{
                    $db = $this->getDbConnection();
                    $query = "SELECT * FROM $this->_tablename where itemid IN ($id)";
                    $result = $db->fetchAll($query);
                    $cache->setCache($key, $result,600);
		}
		
		return $result;		
	}
		
}
?>
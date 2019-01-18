<?php

class Business_Addon_MappingProduct extends Business_Abstract
{
	private $_tablename = 'app_mapping_product';
	
	const KEY_LIST              = 'app_mapping_product.list.%s';
	const KEY_LIST2             = 'app_mapping_product.list2.%s';
	const KEY_DETAIL = 'app_mapping_product.uid.%s';
    
	protected static $_current_rights = null;
	
	protected static $_instance = null; 

	function __construct()
	{
		
	}
	
	/**
	 * get instance of Business_Addon_MappingProduct
	 *
	 * @return Business_Addon_MappingProduct
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
		$cache = GlobalCache::getCacheInstance();		
		return $cache;
	}
       
        public function get_list_by_id_product_groupimei($id_product)
	{
                $db = $this->getDbConnection();
                $query = "SELECT * FROM $this->_tablename where `id_product` = $id_product group by imei order by id desc ";
                $result = $db->fetchAll($query);
		return $result;		
	}
        public function get_list_by_id_product($id_product)
	{
                $db = $this->getDbConnection();
                $query = "SELECT * FROM $this->_tablename where `id_product` = $id_product group by id_color order by id desc ";
                $result = $db->fetchAll($query);
		return $result;		
	}
        public function get_list_by_itemid_imei($id,$strimei)
	{
                $db = $this->getDbConnection();
                $query = "SELECT * FROM $this->_tablename where id_product = $id and imei IN ($strimei) ";
                
                $result = $db->fetchAll($query);
		return $result;		
	}
        public function get_list_by_id($id)
	{
                $db = $this->getDbConnection();
                $query = "SELECT * FROM $this->_tablename where id_product = $id";
                $result = $db->fetchAll($query);
		return $result;		
	}
        public function get_list_by_follow_imei($follow_imei,$str_type)
	{
                $db = $this->getDbConnection();
                $query = "SELECT * FROM $this->_tablename where id_material like '$str_type%' and follow_imei = $follow_imei and imei !='' and imei NOT LIKE '%.%'";
                $result = $db->fetchAll($query);
		return $result;		
	}
        public function get_detail_by_product_color($id_product,$id_color)
	{
                $db = $this->getDbConnection();
                $query = "SELECT * FROM $this->_tablename where id_color = $id_color and id_product = $id_product";
                $result = $db->fetchAll($query);
		return $result[0];		
	}
        public function get_detail_by_id_material($id_material,$itemid=0)
	{
            $db = $this->getDbConnection();
            $query = "SELECT * FROM $this->_tablename where id_material = '$id_material' ";
            if((int) $itemid >0){
                $query .=" and id_product = $itemid ";
            }
            $query .="  order by id desc";
            $result = $db->fetchAll($query);
            return $result[0];		
	}
        public function get_list_by_id_material($id_material)
	{
            $db = $this->getDbConnection();
            $query = "SELECT * FROM $this->_tablename where id_material IN ($id_material) ";
            $query .="  order by id desc";
            $result = $db->fetchAll($query);
            return $result;		
	}
        
        public function get_detail_by_imei($imei,$id_fast_bp)
	{
                $db = $this->getDbConnection();
                $query = "SELECT * FROM $this->_tablename where imei = '$imei'";
                if($id_fast_bp !=NULL){
                    $query .=" and id_warehouse like '%$id_fast_bp%'";
                }
                $query .="  order by id desc";
                $result = $db->fetchAll($query);
		return $result[0];		
	}
        public function get_detail_by_imei_followimei($imei)
	{
                $db = $this->getDbConnection();
                $query = "SELECT * FROM $this->_tablename where imei = '$imei' and follow_imei=2 order by id desc";
                $result = $db->fetchAll($query);
		return $result[0];		
	}
	public function get_list_by_imei($imei)
	{
                $db = $this->getDbConnection();
                $query = "SELECT * FROM $this->_tablename where follow_imei = 1  and  UPPER(imei) IN ($imei) ";
                $result = $db->fetchAll($query);
		return $result;		
	}
        public function get_list_by_imei_kygui($imei)
	{
                $db = $this->getDbConnection();
                $query = "SELECT * FROM $this->_tablename where imei IN ($imei) and id_warehouse like '%.K.OLDX%' order by id desc";
                $result = $db->fetchAll($query);
		return $result;		
	}
        public function get_list_by_imei_by_makho($imei,$ma_kho="")
	{
                $db = $this->getDbConnection();
                $query = "SELECT * FROM $this->_tablename where imei IN ($imei)  ";
                if($ma_kho != NULL){
                    $query .=" and id_warehouse like '%$ma_kho%'";
                }
                $query .=" order by id desc";
                $result = $db->fetchAll($query);
		return $result;		
	}
	public function getListById($id)
	{
		$cache = $this->getCacheInstance();
		$key = 'getListById'.$this->_tablename.$id;
		$result = $cache->getCache($key);
		$result = FALSE;				
		if($result === FALSE)
		{
			$db = $this->getDbConnection();
			$query = "SELECT * FROM $this->_tablename where itemid IN ($id)";
//                        echo "<pre>";
//                        var_dump($query);
//                        die();
			$result = $db->fetchAll($query);
			if(!is_null($result) && is_array($result))
			{
				$cache->setCache($key, $result);
			}
		}
		
		return $result;		
	}
	
	public function deleteByImei($imei) {
        $db = $this->getDbConnection();
        $where = array();
        $where[] = "imei='" . parent::adaptSQL($imei) . "'";
        $result = $db->delete($this->_tablename, $where);
        return $result;
    }
		
}
?>
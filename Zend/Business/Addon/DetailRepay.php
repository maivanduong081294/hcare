<?php

class Business_Addon_DetailRepay extends Business_Abstract
{
	private $_tablename = 'detail_repay';
	
	const KEY_LIST              = 'detail_repay.list.%s';
	const KEY_LIST2             = 'detail_repay.list_repay.%s';
	const KEY_DETAIL = 'detail_repay.uid.%s';
    
	protected static $_current_rights = null;
	
	protected static $_instance = null; 

	function __construct()
	{
	}
	
	/**
	 * get instance of Business_Addon_DetailRepay
	 *
	 * @return Business_Addon_DetailRepay
	 */
	public static function getInstance()
	{
		if(self::$_instance == null)
		{
			self::$_instance = new self();
		}
		return self::$_instance;
	}
        function getCacheInstance()
	{
		$cache = GlobalCache::getCacheInstance('event');		
		return $cache;
	}
        private function getDbConnection() {
            $db = Globals::getDbConnection('maindb');
            return $db;
        }
        public function update($id,$data) {
            $db= $this->getDbConnection();
            $query = "id = ".$id;
            $result = $db->update($this->_tablename, $data,$query);
            return $result;
        }
        public function get_list_by_sync($id_addon_user)
	{
            $cache = $this->getCacheInstance('ws');
            $key = "get_list_by_sync.$this->_tablename.$id_addon_user";
            $result = $cache->getCache($key);
            $result= FALSE;
            if($result=== FALSE){
                
               $db = $this->getDbConnection();
                $query = "select * from $this->_tablename where id_addon_user = $id_addon_user and price >100 and isync =1 and status=1 and autoid>0";
                $result = $db->fetchAll($query);
                if($result != null && is_array($result))
                {
                    $cache->setCache($key, $result);				
                }
            }
            
            return $result;
	}
        public function get_detail_by_sync($id_addon_user)
	{
            $cache = $this->getCacheInstance('ws');
            $key = "get_detail_by_sync.$this->_tablename.$id_addon_user";
            $result = $cache->getCache($key);
            $result= FALSE;
            if($result=== FALSE){
                
               $db = $this->getDbConnection();
                $query = "select * from $this->_tablename where id_addon_user = $id_addon_user and price >100 and isync =1 and status=1 and autoid>0";
                $_result = $db->fetchAll($query);
                $result = $_result[0];
                if($result != null && is_array($result))
                {
                    $cache->setCache($key, $result);				
                }
            }
            
            return $result;
	}
        public function get_top1($_date30=""){
            $db = $this->getDbConnection();
            $query = "select * from $this->_tablename where  price >100 and  isync =1 and status=1 and autoid>0";
            if($_date30 != NULL){
                $query .=" and datetime >= '$_date30'";
            }
            $query .=" order by id asc limit 0,1";
            $result = $db->fetchAll($query);
            return $result[0];
        }
        
        public function getListBillId($bill){
            $cache = $this->getCacheInstance();
            $key = "getListBillId.$this->_tablename.$bill";
            $result = $cache->getCache($key);
            $result = FALSE;
            if($result === FALSE)
            {
                $db = $this->getDbConnection();
                $query =  "SELECT * FROM $this->_tablename  WHERE  id_addon_user in ($bill) and status=1";
                $result = $db->fetchAll($query);

                if(!is_null($result) && is_array($result))
                {
                    $cache->setCache($key, $result,60*5);
                }
            }
        return $result;
    }
    public function get_list_by_idaddonuser_products_id($id_addon_user,$products_id){
            $db = $this->getDbConnection();
            $query = "select * from $this->_tablename where status = 1 and id_addon_user IN ($id_addon_user)";
            $result = $db->fetchAll($query);
            return $result;
        }
}
?>
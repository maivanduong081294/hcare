<?php
class Business_Addon_HistoryPrintOrder extends Business_Abstract
{
	private $_tablename = 'history_print_order';
	private static $_instance = null; 
	const KEY_LIST = 'history_print_order.list.%s.%s';	
        const KEY_DETAIL = 'history_print_order.detail.%s';	//key of detail.id
	function __construct()
	{			
	}
	
	//public static function
	/**
	 * get instance of Business_Addon_HistoryPrintOrder
	 *
	 * @return Business_Addon_HistoryPrintOrder
	 */
	public static function getInstance()
	{
		if(self::$_instance == null)
		{
			self::$_instance = new self();
		}
		return self::$_instance;
	}	
	
	/**
	 * Get DB Connection
	 *
	 * @return Zend_Db_Adapter_Abstract
	 */
        function get_base_url(){
            return alias_domain;
        }
	private function getDbConnection()
	{		
		$db    	= Globals::getDbConnection('maindb');		
		return $db;	
	}
	
	/**
	 * Enter description here...
	 *
	 * @return Maro_Cache
	 */
	private function getCacheInstance()
	{
		$cache = GlobalCache::getCacheInstance();                
		return $cache;
	}
        private function getKeyList($newsid,$cateid)
	{
		return sprintf(self::KEY_LIST,$newsid,$cateid);
	}
        private function getKeyDetail($id)
	{
		return sprintf(self::KEY_DETAIL,$id);
	}
       
        public function update($id,$data) {
            $db= $this->getDbConnection();
            $query = "itemid = ".$id;
            $result = $db->update($this->_tablename, $data,$query);
            return $result;
        }
        public function insert($data)
        {
                $db = $this->getDbConnection();
                $result = $db->insert($this->_tablename,$data);
                if ($result > 0) {
                    $lastid= $db->lastInsertId($this->_tablename); // tra ve id khi them vao
                }
                return $lastid;
        }
        public function getTotalByID($id,$type=0) {
			$db = $this->getDbConnection();
			if ($id=="") {
				return 0;
			}
			if ($type >0) {
				$andType = " and type = $type ";
			}		
			$query = "SELECT count(*) as total FROM " . $this->_tablename . " WHERE id_addon_user = $id $andType ";
			$result = $db->fetchAll($query);
			return (int)$result[0]["total"];
		}
}
?> 
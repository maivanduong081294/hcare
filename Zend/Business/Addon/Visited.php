<?php
class Business_Addon_Visited extends Business_Abstract
{
	private $_tablename = 'hnam_visited';
	private static $_instance = null; 
	const KEY_LIST = 'hnam_visited.list.%s.%s';	
        const KEY_DETAIL = 'hnam_visited.detail.%s';	//key of detail.id
	function __construct()
	{			
	}
	
	//public static function
	/**
	 * get instance of Business_Addon_Visited
	 *
	 * @return Business_Addon_Visited
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
        public function get_detail($moudels,$controller,$action){
        $cache = $this->getCacheInstance();
        $key = $this->get_base_url()."get_detail2".  $this->_tablename.$moudels.$controller.$action;
        $result  = $cache->getCache($key);
        if($result === false){
            $db = $this->getDbConnection();
            $query = "SELECT * FROM " . $this->_tablename . " WHERE enabled = 1 and module = '$moudels' and controller = '$controller' and action = '$action' order by id desc";
            $result = $db->fetchAll($query);
            $result = $result[0];
            if(!is_null($result) && is_array($result)){
                $cache->setCache($key, $result,50);
            }
        }
        
        return $result;
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
}
?> 
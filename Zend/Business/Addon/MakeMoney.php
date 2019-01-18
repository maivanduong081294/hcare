<?php

class Business_Addon_MakeMoney extends Business_Abstract {

    private $_tablename = 'make_money';
    private static $_instance = null;

    function __construct() {
        
    }

    //public static function
    /**
     * get instance of Business_Addon_MakeMoney
     *
     * @return Business_Addon_MakeMoney
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
    public function getMakeMoney($storeid,$startday,$endday){
        $cache = $this->getCacheInstance();
        $key = "getMakeMoney".$this->_tablename.$storeid.$startday.$endday;
        $result = $cache->getCache($key);
        $result = FALSE;
        if($result === FALSE)
        {
                $db = $this->getDbConnection();
                $query = "SELECT * FROM " . $this->_tablename . " WHERE enabled = 1 and vote_id like '%$storeid%' and create_day >= '$startday' and end_day <= '$endday'";
                $result = $db->fetchAll($query);
                if($result != null && is_array($result))
                {
                        $cache->setCache($key, $result);				
                }
//						
        }
        return $result;
    }

        public function getDetailByUserByMonths($vote_id,$months) {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " WHERE vote_id  like '%$vote_id%' and MONTH(end_day) = $months";
//        var_dump($query);exit();
        $result = $db->fetchAll($query);
        return $result;
    }
    public function getGroupByName() {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " GROUP BY name";
        $result = $db->fetchAll($query);
        return $result;
    }
    public function getList($vote_ids,$months,$g_name){
        $cache = $this->getCacheInstance();
        $key = 'getList'.$this->_tablename.$keywork.$months;
        $result = $cache->getCache($key);
//        var_dump($key,$result);exit();
//        $cache->deleteCache($key);
        $result =FALSE;
        if ($result === FALSE) {
            $db = $this->getDbConnection();
            $query = "SELECT * FROM  $this->_tablename where 1= 1 and MONTH(end_day) = '$months' and enabled = 1";
            if($vote_ids !=""){
                $query .=" and vote_id = $vote_ids ";
            }
            if($g_name != null && $g_name != "all" ){
                $query .=" and name = '$g_name'";
            }
            $query.=" ORDER BY id DESC";
            $query.= " LIMIT 100";
//            var_dump($query);exit();
            $result = $db->fetchAll($query);
            $cache->setCache($key, $result);
            
        }
        return $result;
    }
    public function getListByProducts($vote_ids,$months,$products_id){
        $cache = $this->getCacheInstance();
        $key = 'getList'.$this->_tablename.$keywork.$months;
        $result = $cache->getCache($key);
//        var_dump($key,$result);exit();
//        $cache->deleteCache($key);
        $result =FALSE;
        if ($result === FALSE) {
            $db = $this->getDbConnection();
            $query = "SELECT * FROM  $this->_tablename where 1= 1 and MONTH(end_day) = '$months' ";
            if($vote_ids !=""){
                $query .=" and vote_id = $vote_ids ";
            }
            if($products_id != null && $products_id != "all" ){
                $query .=" and products_id IN ($products_id)";
            }
            $query.=" ORDER BY id DESC";
            $query.= " LIMIT 100";
//            var_dump($query);exit();
            $result = $db->fetchAll($query);
            $cache->setCache($key, $result);
            
        }
        return $result;
    }
    

    public function getDetail($id) {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " WHERE id=?";
        $data = array($id);
        $result = $db->fetchAll($query, $data);
        return $result[0];
    }
    public function getDetailByProductId($id,$months) {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " WHERE products_id= $id and MONTH(bonus_date) = $months";
        $result = $db->fetchAll($query);
        return $result[0];
    }
    public function insert($data) {
//        var_dump($data);exit();
        $db = $this->getDbConnection();
        $result = $db->insert($this->_tablename, $data);
//        var_dump($result);exit();
        $this->_deleteAllCache();
        return $result;
    }
    public function update($id,$data) {
        $db= $this->getDbConnection();
        $query = "id = ".$id;
        $result = $db->update($this->_tablename, $data,$query);
        $this->_deleteAllCache();
//            var_dump($result);exit();
        return $result;
    }

    public function delete($id)
	{
		$db = $this->getDbConnection();
		$where = array();
		$where[] = "id='" . parent::adaptSQL($id) . "'";
		$result = $db->delete($this->_tablename,$where);
                if($result > 0)
		{
			$this->_deleteAllCache($id);
		}
                return $result;
	}
    public function _delete($id) {
        //get current menu
            $db= $this->getDbConnection();
            $query = "id = ".$id;
            $data = array(
                "enabled" => "0"
            );
            $result = $db->update($this->_tablename, $data,$query);
//            var_dump($result);exit();
            $this->_deleteAllCache();
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
            $this->_deleteAllCache();
            return $result;
    }
    
    private function _deleteAllCache()
    {
            $cache = $this->getCacheInstance('ws');
            $cache->flushAll();
    }
}

?>

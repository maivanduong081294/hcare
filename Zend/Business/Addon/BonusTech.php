<?php

class Business_Addon_BonusTech extends Business_Abstract {

    private $_tablename = 'bonus_tech';
    private static $_instance = null;

    function __construct() {
        
    }

    //public static function
    /**
     * get instance of Business_Addon_BonusTech
     *
     * @return Business_Addon_BonusTech
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
    
    public function getList($keywork){
        $cache = $this->getCacheInstance();
        $key = 'getList'.$this->_tablename.$keywork;
        $result = $cache->getCache($key);
//        var_dump($key,$result);exit();
//        $cache->deleteCache($key);
        if ($result === FALSE) {
            $db = $this->getDbConnection();
            $query = "SELECT * FROM  $this->_tablename where  enabled=1 ";
            if($keywork !=null){
                $query .=" and bonus_name like '%$keywork%' or products_id = '$keywork'";
            }
            $query.=" ORDER BY id DESC";
//            $query.= " LIMIT 100";
//            var_dump($query);exit();
            $result = $db->fetchAll($query);
            $cache->setCache($key, $result,600);
            
        }
        return $result;
    }
    

    public function getDetail($id) {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " WHERE  enabled=1 and id=?";
        $data = array($id);
        $result = $db->fetchAll($query, $data);
        return $result[0];
    }
    
    public function getDetailByProductId($id,$months) {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " WHERE enabled=1 and products_id= $id and MONTH(bonus_date) = $months";
        $result = $db->fetchAll($query);
        return $result[0];
    }
    public function getDetailByProduct_Id($products_id) {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " WHERE enabled=1 and products_id= $products_id ";
        $result = $db->fetchAll($query);
        return $result[0];
    }
    public function getListByPId($products_id) {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " WHERE enabled=1 and products_id IN ($products_id) ";
        $result = $db->fetchAll($query);
        return $result;
    }
    public function insert($data) {
        $db = $this->getDbConnection();
        $result = $db->insert($this->_tablename, $data);
        $this->_deleteAllCache();
        return $result;
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

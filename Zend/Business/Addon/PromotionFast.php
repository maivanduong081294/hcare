<?php

class Business_Addon_PromotionFast extends Business_Abstract {

    private $_tablename = 'addon_promotion_fast';

    const KEY_LIST = 'addon_promotion_fast.list.%s';   //key of list.questionid
    const KEY_DETAIL = 'addon_promotion_fast.detail.%s';  //key of detail.id

    private static $_instance = null;

    function __construct() {
        
    }

    //public static function
    /**
     * get instance of Business_Addon_PromotionFast
     *
     * @return Business_Addon_PromotionFast
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
        $cache = GlobalCache::getCacheInstance('event');
        return $cache;
    }
    public function checkDetailByTitle($title="",$price=""){
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " WHERE title like '%$title%' and return_price = $price";
        $result = $db->fetchAll($query);
        return $result[0];
    }

    public function getList(){
        $cache = $this->getCacheInstance();
        $key ="getList".  $this->_tablename;
        $result = $cache->getCache($key);
        $result = FALSE;
        if($result ===FALSE){
            $db = $this->getDbConnection();
            $query = "SELECT * FROM " . $this->_tablename . " where enabled =1 order by itemid DESC";
            $result = $db->fetchAll($query);
            if(!is_null($result) && is_array($result)){
                $cache->setCache($key, $result,600);
            }
        }
        
        return $result;
    }
    public function get_list_by_itemid_title($itemid_title){
        $cache = $this->getCacheInstance();
        $key ="get_list_by_itemid_title".  $this->_tablename.$itemid_title;
        $result = $cache->getCache($key);
        $result = FALSE;
        if($result ===FALSE){
            $db = $this->getDbConnection();
            $query = "SELECT * FROM " . $this->_tablename . " where enabled=1 and itemid_title= $itemid_title order by itemid DESC";
            $result = $db->fetchAll($query);
            if(!is_null($result) && is_array($result)){
                $cache->setCache($key, $result,600);
            }
        }
        
        return $result;
    }
    public function get_list($str_type=NULL){
        $cache = $this->getCacheInstance();
        $key ="get_list".  $this->_tablename.$str_type;
        $result = $cache->getCache($key);
        if($result ===FALSE){
            $db = $this->getDbConnection();
            $query = "SELECT * FROM " . $this->_tablename . " where enabled=1 ";
            if($str_type != NULL){
                $query .=" and type IN ($str_type)";
            }
            $query .=" order by itemid DESC";
            $result = $db->fetchAll($query);
            if(!is_null($result) && is_array($result)){
                $cache->setCache($key, $result,600);
            }
        }
        
        return $result;
    }
    public function get_list_by_id($strID){
        
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " where enabled=1  and itemid IN ($strID)";
        $result = $db->fetchAll($query);
        return $result;
    }
    public function get_list_by_ctkm($ctkm=-1){
        if($ctkm ==NULL){
            $ctkm =-1;
        }
        $cache = $this->getCacheInstance();
        $key ="get_list_by_ctkmss".  $this->_tablename.$ctkm;
        $result = $cache->getCache($key);
        $result = FALSE;
        if($result ===FALSE){
            $db = $this->getDbConnection();
            $query = "SELECT * FROM " . $this->_tablename . " where enabled =1";
            if((int)$ctkm >-1){
                $query .=" and ctkm = $ctkm";
            }
            $query .=" order by itemid DESC";
            
            $result = $db->fetchAll($query);
            if(!is_null($result) && is_array($result)){
                $cache->setCache($key, $result,600);
            }
        }
        
        return $result;
    }
     public function excute($query){
            $db     =  $this->getDbConnection();
            $result = $db->query($query);
            return $result;
        }
    public function getDetail($id) {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " WHERE itemid=?";
        $data = array($id);
        $result = $db->fetchAll($query, $data);
        return $result[0];
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
    public function update($id,$data) {
        $where = array();
        $where[] = "itemid='" . parent::adaptSQL($id) . "'";
        $db = $this->getDbConnection();
        $result = $db->update($this->_tablename, $data, $where);
    }

    public function delete($id, $qid) {
        //get current menu
        $db = $this->getDbConnection();
        $where = array();
        $where[] = "itemid='" . parent::adaptSQL($id) . "'";
        $result = $db->delete($this->_tablename, $where);
        if ($result > 0) {
            $this->_deleteAllCache($qid, $id);
        }
    }

}

?>

<?php

class Business_Addon_PromotionFree extends Business_Abstract {

    private $_tablename = 'promotion_free_price';

    const KEY_LIST = 'promotion_free_price.list.%s';   //key of list.questionid
    const KEY_DETAIL = 'promotion_free_price.detail.%s';  //key of detail.id

    private static $_instance = null;

    function __construct() {
        
    }

    //public static function
    /**
     * get instance of Business_Addon_PromotionFree
     *
     * @return Business_Addon_PromotionFree
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
        $cache = GlobalCache::getCacheInstance('nt');
        return $cache;
    }

    public function getDetail($id) {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " WHERE aid=?";
        $data = array($id);
        $result = $db->fetchAll($query, $data);
        return $result[0];
    }
    public function getDetail2($id_addon_user) {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " WHERE id_addon_user = $id_addon_user";
        $result = $db->fetchAll($query);
        return $result;
    }
    public function getListByProductItemid($products_id)
	{
            $db = $this->getDbConnection();
            $query = "SELECT * FROM  $this->_tablename  where product_ids = $products_id ";
//    			var_dump($query);exit();
            $result = $db->fetchAll($query);
            return $result[0];		
	}
    public function getListByProductIdsActive($products_id,$id_addon_user)
	{
//            exit('231');
            $db = $this->getDbConnection();
            $query = "SELECT * FROM  $this->_tablename  where product_ids = $products_id and id_addon_user= $id_addon_user and (enabled !=1 or type = 5)";
//    			var_dump($query);exit();
            $result = $db->fetchAll($query);
            
//            var_dump(count($result));exit();
            return $result;		
	}
    public function getListByProductIdsActive2($products_id,$id_addon_user)
	{
//            exit('231');
            $db = $this->getDbConnection();
            $query = "SELECT * FROM  $this->_tablename  where product_ids = $products_id and id_addon_user= $id_addon_user and (enabled !=1)";
//    			var_dump($query);exit();
            $result = $db->fetchAll($query);
            
//            var_dump(count($result));exit();
            return $result;		
	}
    public function insert($data) {
        $db = $this->getDbConnection();
        $result = $db->insert($this->_tablename, $data);
        return $result;
    }
    public function update($id, $qid, $data) {
        $where = array();
        $where[] = "aid='" . parent::adaptSQL($id) . "'";
        $db = $this->getDbConnection();
        $result = $db->update($this->_tablename, $data, $where);
        if ($result > 0) {
            $this->_deleteAllCache($qid, $id);
        }
    }

    public function delete($id, $qid) {
        //get current menu
        $where = array();
        $where[] = "itemid='" . parent::adaptSQL($id) . "'";
        $result = $db->delete($this->_tablename, $where);
        if ($result > 0) {
            $this->_deleteAllCache($qid, $id);
        }
    }

}

?>

<?php

class Business_Addon_Installment extends Business_Abstract {

    private $_tablename = 'installment';

    const KEY_LIST = 'installment.list.%s';   //key of list.questionid
    const KEY_DETAIL = 'installment.detail.%s';  //key of detail.id
    const KEY_DETAIL_KM = 'km.%s';  //key of detail.id
    const KEY_DETAIL_LIST_KM = 'getlist_installment.%s';  //key of list.id

    private static $_instance = null;

    function __construct() {
        
    }

    //public static function
    /**
     * get instance of Business_Addon_Installment
     *
     * @return Business_Addon_Installment
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
    
    private function getKeyKM($itemid){
        return sprintf(self::KEY_DETAIL_KM, $itemid);
    }
    private function getListKeyKM($keywork){
        return sprintf(self::KEY_DETAIL_LIST_KM,$keywork);
    }
    public function getList($kw="",$cateid=0,$startdate,$enddate){
        $cache = $this->getCacheInstance();
        $key = "getList.$this->_tablename.$kw.$cateid.$startdate.$enddate";
        $result = $cache->getCache($key);
//            $result=FALSE;
        if($result === FALSE){
            $db = $this->getDbConnection();
            $query = "SELECT * FROM " . $this->_tablename . " WHERE 1=1";
            $pid = (int) $kw;
            if(!empty($kw)){
                $query.=" and (products_id = $pid or name like '%$kw%')";
            }
            if($cateid !=0){
                $query .=" and cated_prepaid = $cateid";
            }

            $query .=" and (startdate >= '$startdate' and startdate <= '$enddate') order by datetime Desc";
    //        var_dump($query);//exit();
            $result = $db->fetchAll($query);
            if(!is_null($result) && is_array($result))
            {
                    $cache->setCache($key, $result,600);
            }
        }
        
        return $result;
    }
    
    public function getList2($kw="",$cateid=0){
        $cache = $this->getCacheInstance();
        $key = "getList2.$this->_tablename.$kw.$cateid";
        $result = $cache->getCache($key);
//            $result=FALSE;
        if($result === FALSE){
            $db = $this->getDbConnection();
            $query = "SELECT * FROM " . $this->_tablename . " WHERE 1=1";
            $pid = (int) $kw;
            if(!empty($kw)){
                $query.=" and (products_id = $pid or name like '%$kw%')";
            }
            if($cateid !=0){
                $query .=" and cated_prepaid = $cateid";
            }

            $query .=" order by datetime Desc";
    //        var_dump($query);//exit();
            $result = $db->fetchAll($query);
            if(!is_null($result) && is_array($result))
            {
                    $cache->setCache($key, $result,600);
            }
        }
        
        return $result;
    }

    public function getListByProductsId($products_id){
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " WHERE status = 1 and products_id = $products_id and DATEDIFF(NOW(),startdate) >= 0 and DATEDIFF(NOW(),enddate) <= 0";
        $result = $db->fetchAll($query);
        return $result;
    }
    public function getListByApplyAllId(){
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " WHERE status = 1 and apply = 0 and DATEDIFF(NOW(),startdate) >= 0 and DATEDIFF(NOW(),enddate) <= 0";
        $result = $db->fetchAll($query);
        return $result;
    }
    public function getDetailById($id){
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " WHERE status = 1 and id = $id and DATEDIFF(NOW(),startdate) >= 0 and DATEDIFF(NOW(),enddate) <= 0";
        $result = $db->fetchAll($query);
        return $result[0];
    }
    public function getDetailById2($id){
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " WHERE id = $id ";
        $result = $db->fetchAll($query);
        return $result[0];
    }

    public function insert($data) {
        $db = $this->getDbConnection();
        $result = $db->insert($this->_tablename, $data);
        return $result;
    }
    public function update($id,$data) {
        $db= $this->getDbConnection();
        $query = "id = ".$id;
        $result = $db->update($this->_tablename, $data,$query);
        $this->deleteKeyKM($id);
        $this->deleteListKeyKM();
//            var_dump($result);exit();
        return $result;
    }
    public function excute($query){
            $db     =  $this->getDbConnection();
            $result = $db->query($query);
            return $result;
        }
        
    private function deleteKeyKM($itemid) {
        $cache = $this->getCacheInstance();
        $key = $this->getKeyKM($itemid);
        $cache->deleteCache($key);
    }
    private function deleteListKeyKM($keyword="") {
        $cache = $this->getCacheInstance();
        $key = $this->getListKeyKM($keyword);
        $cache->deleteCache($key);
    }
}

?>

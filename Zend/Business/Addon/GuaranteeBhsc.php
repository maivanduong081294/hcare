<?php

class Business_Addon_GuaranteeBhsc extends Business_Abstract {

    private $_tablename = 'hnam_guarantee_bhsc';
    private $_tablename2 = 'hnam_guarantee';
    private static $_instance = null;

    function __construct() {
        
    }

    //public static function
    /**
     * get instance of Business_Addon_GuaranteeBhsc
     *
     * @return Business_Addon_GuaranteeBhsc
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
    public function get_list_check_view(){
        $cache = $this->getCacheInstance();
            $key = "get_list_check_views".$this->_tablename;
            $result = $cache->getCache($key);
            if($result === FALSE){
                $db = $this->getDbConnection();
                $query = "select tb1.id,tb1.id_guaranteeid,tb1.money_dvsc,tb1.money,tb1.money_hnam,tb2.ok,tb1.is_view from $this->_tablename tb1,$this->_tablename2 tb2 where tb1.id_guaranteeid = tb2.id and tb2.ok>0 and tb1.enabled = 1 and tb1.is_view=0 ";
                $query.=" LIMIT 0,50";
                $result = $db->fetchAll($query);
                if(!is_null($result) && is_array($result)){
                    $cache->setCache($key, $result,900);
                }
            }
        return $result;
    }
    
    public function get_list($start="",$end="",$status=0,$ncc_ok=0,$userid=0){
        $db = $this->getDbConnection();
        $query = "select * from $this->_tablename where enabled = 1";
        if($start != NULL){
            $query.=" and datetime >= '$start' ";
        }
        if($end != NULL){
            $query.=" and datetime <= '$end' ";
        }
        if((int)$status >0){
            $query.=" and status =$status ";
        }
        if((int)$ncc_ok >0){
            $query.=" and ncc_ok =$ncc_ok ";
        }
        if((int)$userid >0){
            $query.=" and userid =$userid ";
        }
        $query .=" order by id desc";
        
        $result = $db->fetchAll($query);
        return $result;
    }
    public function get_list_by_guaranteeid($strid){
        $db = $this->getDbConnection();
        $query = "select * from $this->_tablename where enabled = 1 and  id_guaranteeid IN ($strid)  order by id desc";
        $result = $db->fetchAll($query);
        return $result;
    }
   public function get_list_searchid_phone($kq){
        $db = $this->getDbConnection();
        $query = "select * from $this->_tablename where enabled = 1 and (phone like '%$kq%' or id=$kq) ";
        $result = $db->fetchAll($query);
        return $result;
    }
    public function getDetail($id){
        $db = $this->getDbConnection();
        $query = "select * from $this->_tablename where enabled = 1 and id = $id";
        $result = $db->fetchAll($query);
        return $result[0];
    }
    public function get_detail_by_id_guaranteeid($id_guaranteeid){
        $db = $this->getDbConnection();
        $query = "select * from $this->_tablename where id_guaranteeid = $id_guaranteeid and enabled = 1 order by id desc";
        $result = $db->fetchAll($query);
        return $result[0];
    }

    public function insert($data) {
        $db = $this->getDbConnection();
        $result = $db->insert($this->_tablename, $data);
        if ($result > 0) {
            $lastid= $db->lastInsertId($this->_tablename); // tra ve id khi them vao
        }
        return $lastid;
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
        return $result;
    }
    public function update2($id,$data) {
        $db= $this->getDbConnection();
        $query = "id_guaranteeid = ".$id;
        $result = $db->update($this->_tablename, $data,$query);
        return $result;
    }

}

?>

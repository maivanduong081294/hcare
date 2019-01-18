<?php

class Business_Addon_GuaranteePk extends Business_Abstract {

    private $_tablename = 'hnam_guarantee_pk';
    private $_tablename_history = 'hnam_guarantee_history_pk';
    private $_tablename_warranty = 'hnam_guarantee_warranty_pk';
    private static $_instance = null;

    function __construct() {
        
    }

    //public static function
    /**
     * get instance of Business_Addon_GuaranteePk
     *
     * @return Business_Addon_GuaranteePk
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
    public function get_list_by_date($q="",$from="",$to=""){
        $db = $this->getDbConnection();
        $query = "select id,datetime,money,money_dvsc,money_hnam,ncc_ok,status_guarantee from $this->_tablename where enabled = 1 and (money>0 or money_dvsc >0 or money_hnam >0)";
        if($q!= NULL){
            $query.=" and (id='$q' or imei='$q' )";
        }
        if($from!= NULL){
            $query.=" and datetime >='$from'";
        }
        if($to!= NULL){
            $query.=" and datetime <='$to'";
        }
        
        $result = $db->fetchAll($query);
        return $result;
    }

    public function getDetailByWarranty($id_guarantee){
        $db = $this->getDbConnection();
        $query = "select * from $this->_tablename_warranty where id_guarantee = $id_guarantee order by datetime desc";
        $result = $db->fetchAll($query);
        return $result[0];
    }
    public function get_list_searchid_phone($kq){
        $db = $this->getDbConnection();
        $query = "select * from $this->_tablename where enabled = 1 and (phone ='$kq' or id='$kq' or imei like '%$kq%') ";
        $result = $db->fetchAll($query);
        return $result;
    }
    public function get_list_searchid_phone2($imei,$phone){
        $db = $this->getDbConnection();
        $query = "SELECT bhsc.item_name,bhsc.datetime,bhsc.imei,bhsc.phone,bhsc.id,bhsc.complete FROM `hnam_guarantee` bhsc, hnam_customer cus WHERE bhsc.idcustomer = cus.id and cus.phone ='$phone' and bhsc.imei LIKE '%$imei'";
        
        $result = $db->fetchAll($query);
        return $result;
    }
    public function get_list_searchid($kq){
        $db = $this->getDbConnection();
        $query = "select * from $this->_tablename where enabled = 1 and id='$kq' ";
        $result = $db->fetchAll($query);
        return $result;
    }
    public function get_list_search_id_imei($kq){
        $db = $this->getDbConnection();
        $query = "select * from $this->_tablename where enabled = 1 and (id='$kq' or imei like '%$kq') ";
        $result = $db->fetchAll($query);
        return $result;
    }
    public function get_list_by_id($strid){
        $db = $this->getDbConnection();
        $query = "select * from $this->_tablename where enabled = 1 and  id IN ($strid) ";
        $result = $db->fetchAll($query);
        return $result;
    }
    public function del_list_by_date_out($start,$end,$storeid){
        $day = md5("$start.$end");
        $key = "get_list_by_date_out".$this->_tablename.$day.$storeid;
        $cache = $this->getCacheInstance();
        $cache->deleteCache($key);
    }

    public function get_list_by_date_out($start,$end,$storeid=0,$sync=0){
        $cache = $this->getCacheInstance();
        $day = md5("$start.$end");
        $key = "get_list_by_date_out".$this->_tablename.$day.$storeid;
         if((int)$sync >0){
            $this->del_list_by_date_out($start,$end,$storeid);
            $result = FALSE;
        }else{
            $result = $cache->getCache($key);
        }
        
        if($result ===FALSE){
            $db = $this->getDbConnection();
            $query = "select * from $this->_tablename where enabled = 1 and  date_out > '$start' and date_out <='$end' ";
            if((int)$storeid >0){
                $query .=" and storeid IN ($storeid) ";
            }
            $result = $db->fetchAll($query);
            if(!is_null($result) && is_array($result)){
                $cache->setCache($key, $result,900);
            }
        }
        return $result;
    }
    public function get_list_detail_imei($imei){
        $db = $this->getDbConnection();
        $query = "select * from $this->_tablename where  imei = '$imei' order by id desc";
        $result = $db->fetchAll($query);
        return $result[0];
    }
    public function del_getListByUserId($receiver_id=0,$start,$end,$storeid=0,$complete=-1,$status_guarantee=0,$transfers=0,$enabled ="",$storeid_in=0,$ok=-1,$bhang=0){
        $day = md5("$start.$end");
        $cache = $this->getCacheInstance();
        $key = "getListByUserId".$this->_tablename.$day.$receiver_id.$storeid.$complete.$status_guarantee.$transfers.$enabled.$storeid_in.$ok.$bhang;
        $cache->deleteCache($key);
    }

    public function getListByUserId($receiver_id=0,$start,$end,$storeid=0,$complete=-1,$status_guarantee=0,$transfers=0,$enabled ="",$storeid_in=0,$ok=-1,$bhang=0,$sync=0){
        
        $cache = $this->getCacheInstance();
        $day = md5("$start.$end");
        $key = "getListByUserId".$this->_tablename.$day.$receiver_id.$storeid.$complete.$status_guarantee.$transfers.$enabled.$storeid_in.$ok.$bhang;
        if((int)$sync>0){
            $this->del_getListByUserId($receiver_id, $start, $end, $storeid, $complete, $status_guarantee, $transfers, $enabled, $storeid_in,$ok,$bhang);
            $result = FALSE;
        }else{
            $result = $cache->getCache($key);
        }
        
        if($result ===FALSE){
            $db = $this->getDbConnection();
            $query = "select * from $this->_tablename where 1=1";
            if((int)$receiver_id >0){
                $query .= " and (receiver_id = $receiver_id or receiver_id2 = $receiver_id )";
            }
            if((int)$storeid >0){
                $query .= " and storeid IN ($storeid)";
            }
            if((int)$complete >-1){
                $query .= " and complete = $complete";
            }
            if((int)$status_guarantee >0){
                $query .= " and status_guarantee = $status_guarantee";
            }
            if((int)$transfers >0){
                $query .= " and transfers = $transfers";
            }
            if((int)$storeid_in >0){
                $query .= " and storeid_in = $storeid_in";
            }
            if((int)$bhang >0){
                $query .= " and bhang = $bhang";
            }
            $query.=" and datetime >= '$start' and datetime <= '$end'";

            if($enabled != NULL){
                $query .= " and enabled = $enabled";
            }
            $query .= "  and enabled > 0";
            if($ok >-1){
                $query .= "  and ok IN ($ok)";
            }
            $query .= " order by id desc";
            $result = $db->fetchAll($query);
            if(!is_null($result) && is_array($result)){
                $cache->setCache($key, $result,900);
            }
        }
        return $result;
    }
    public function get_list_statistics($start,$end,$storeid=0,$flag=0,$price_tmp=-1,$transfers=0,$enabled ="",$storeid_in=0,$ok=-1,$bhang=0){
        $db = $this->getDbConnection();
        $query = "select * from $this->_tablename where 1=1";
        
        if((int)$storeid >0){
            $query .= " and storeid IN ($storeid)";
        }
        if((int)$flag >0){
            $query .= " and flag = $flag";
        }
        if($price_tmp >-1){
            if($price_tmp >0){
                $query .= " and price_tmp >0 ";
            }
            if($price_tmp ==0){
                $query .= " and price_tmp =0 ";
            }
            
        }
        if((int)$transfers >0){
            $query .= " and transfers = $transfers";
        }
        if((int)$storeid_in >0){
            $query .= " and storeid_in = $storeid_in";
        }
        if((int)$bhang >0){
            $query .= " and bhang = $bhang";
        }
        $query.=" and datetime >= '$start' and datetime <= '$end'";
        
        if($enabled != NULL){
            $query .= " and enabled = $enabled";
        }
        $query .= "  and enabled > 0";
        if($ok >-1){
            $query .= "  and ok IN ($ok)";
        }
        $query .= " order by id desc";
//        echo "<pre>";
//        var_dump($query);
//        die();
        $result = $db->fetchAll($query);
        return $result;
    }
    public function getDetail($id){
        $db = $this->getDbConnection();
        $query = "select * from $this->_tablename where id = $id";
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
    public function insert_history($data) {
        $db = $this->getDbConnection();
        $result = $db->insert($this->_tablename_history, $data);
        if ($result > 0) {
            $lastid= $db->lastInsertId($this->_tablename_history); // tra ve id khi them vao
        }
        return $lastid;
    }
    public function insert_warranty($data) {
        $db = $this->getDbConnection();
        $result = $db->insert($this->_tablename_warranty, $data);
        if ($result > 0) {
            $lastid= $db->lastInsertId($this->_tablename_warranty); // tra ve id khi them vao
        }
        return $lastid;
    }
    public function update_warranty($id,$data) {
        $db= $this->getDbConnection();
        $query = "id = ".$id;
        $result = $db->update($this->_tablename_warranty, $data,$query);
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
        return $result;
    }

}

?>

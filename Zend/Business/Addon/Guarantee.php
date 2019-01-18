<?php

class Business_Addon_Guarantee extends Business_Abstract {

    private $_tablename = 'hnam_guarantee';
    private $_tablename_history = 'hnam_guarantee_history';
    private $_tablename_warranty = 'hnam_guarantee_warranty';
    private static $_instance = null;

    function __construct() {
        
    }

    //public static function
    /**
     * get instance of Business_Addon_Guarantee
     *
     * @return Business_Addon_Guarantee
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
    public function get_list_by_ok($ok="",$from="",$to="",$strid=""){
        $db = $this->getDbConnection();
        $query = "select * from $this->_tablename where enabled = 1";
        if($ok!= NULL){
            $query.=" and ok=$ok";
        }
        if($from!= NULL){
            $query.=" and datetime >='$from'";
        }
        if($to!= NULL){
            $query.=" and datetime <='$to'";
        }
        if($strid!= NULL){
            $query.=" and id IN ($strid) ";
        }
//        echo "<pre>";
//        var_dump($query);
//        die();
        $result = $db->fetchAll($query);
        return $result;
    }
    public function get_list_creator($creator){
        $db = $this->getDbConnection();
        $query = "select * from $this->_tablename where enabled = 1 and creator  ='$creator'";
        $result = $db->fetchAll($query);
        return $result;
    }
    public function get_list_creator_month_year($creator,$month,$year){
        $db = $this->getDbConnection();
        $query = "select * from $this->_tablename where enabled = 1 and creator  ='$creator' and MONTH(date_out) = $month and YEAR(date_out) = $year";
        $result = $db->fetchAll($query);
        return $result;
    }
    public function get_list_check_show_web($storeid){
        $cache = $this->getCacheInstance();
        $key = $this->_tablename."get_list_check_show_web".$storeid;
        $result = $cache->getCache($key);
//        $result=false;
        if ($result === FALSE) {
            $db = $this->getDbConnection();
            $query = "select id from $this->_tablename where enabled = 1 and is_view=0 and money_dvsc >0 and ok=0";
            if($storeid!= NULL){
                $query.=" and storeid IN ($storeid)";
            }
            
            $query.=" LIMIT 0,50";
            
            $result = $db->fetchAll($query);
            
            if(!is_null($result) && is_array($result)){
                $cache->setCache($key, $result,800);
            }
        }
        
        return $result;
    }
    public function get_list_check_view($storeid){
        $cache = $this->getCacheInstance();
        $key = $this->_tablename."get_list_check_view".$storeid;
        $result = $cache->getCache($key);
//        $result=false;
        if ($result === FALSE) {
            $db = $this->getDbConnection();
            $query = "select id from $this->_tablename where enabled = 1 and is_view=0 and money_dvsc >0 and ok>0";
            if($storeid!= NULL){
                $query.=" and storeid IN ($storeid)";
            }
            $query.=" LIMIT 0, 50";
            $result = $db->fetchAll($query);
            if(!is_null($result) && is_array($result)){
                $cache->setCache($key, $result,800);
            }
        }
        return $result;
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
    public function get_list_by_date_out($storeid="",$from="",$to=""){
        $cache = $this->getCacheInstance();
        $day = strtotime($from)+strtotime($to);
        $key = "get_list_by_date_out".$this->_tablename.$day.$storeid;
        $result = $cache->getCache($key);
        if($result ===FALSE){
            $db = $this->getDbConnection();
            $query = "select ok,id,datetime,money,money_dvsc,money_hnam,ncc_ok,status_guarantee,date_out from $this->_tablename where enabled = 1 and money>0 ";
            if($storeid!= NULL){
                $query.=" and storeid IN ($storeid)";
            }
            if($from!= NULL){
                $query.=" and date_out >='$from'";
            }
            if($to!= NULL){
                $query.=" and date_out <='$to'";
            }

            $result = $db->fetchAll($query);
            if(!is_null($result) && is_array($result)){
                $cache->setCache($key, $result,1200);
            }
        }
        return $result;
    }
    public function get_list_by_date_out2($storeid="",$from="",$to=""){
        $cache = $this->getCacheInstance();
        $day = strtotime($from)+strtotime($to);
        $key = "get_list_by_date_out2".$this->_tablename.$day.$storeid;
        $result = $cache->getCache($key);
        if($result ===FALSE){
            $db = $this->getDbConnection();
            $query = "select ok,id,datetime,money,money_dvsc,money_hnam,ncc_ok,status_guarantee,date_out from $this->_tablename where enabled = 1 and money>0 and ok =1";
            if($storeid!= NULL){
                $query.=" and storeid IN ($storeid)";
            }
            if($from!= NULL){
                $query.=" and date_out >='$from'";
            }
            if($to!= NULL){
                $query.=" and date_out <='$to'";
            }

            $result = $db->fetchAll($query);
            if(!is_null($result) && is_array($result)){
                $cache->setCache($key, $result,1200);
            }
        }
        return $result;
    }
    public function get_list_by_date_out3($storeid="",$from="",$to=""){
        $cache = $this->getCacheInstance();
        $day = strtotime($from)+strtotime($to);
        $key = "get_list_by_date_out3".$this->_tablename.$day.$storeid;
        $result = $cache->getCache($key);
        if($result ===FALSE){
            $db = $this->getDbConnection();
            $query = "select ok,id,datetime,money,money_dvsc,money_hnam,ncc_ok,status_guarantee,date_out from $this->_tablename where enabled = 1 and money>0 ";
            if($storeid!= NULL){
                $query.=" and storeid IN ($storeid)";
            }
            if($from!= NULL){
                $query.=" and date_out >='$from'";
            }
            if($to!= NULL){
                $query.=" and date_out <'$to'";
            }
            $result = $db->fetchAll($query);
            if(!is_null($result) && is_array($result)){
                $cache->setCache($key, $result,1200);
            }
        }
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
        $query = "select * from $this->_tablename where enabled = 1 and (id='$kq' or imei like '%$kq' or seri like '%$kq') ";
        $result = $db->fetchAll($query);
        return $result;
    }
    public function get_list_by_id($strid,$quoc_te=""){
        $db = $this->getDbConnection();
        $query = "select * from $this->_tablename where enabled = 1 and  id IN ($strid) ";
        if($quoc_te != NULL){
            $query .= " and quoc_te = $quoc_te";
        }
        $result = $db->fetchAll($query);
        return $result;
    }
    public function get_list_by_id2($strid,$ok=""){
        $db = $this->getDbConnection();
        $query = "select * from $this->_tablename where enabled = 1 and  id IN ($strid) ";
        if($ok != NULL){
            $query .= " and ok = $ok";
        }
        $result = $db->fetchAll($query);
        return $result;
    }
    public function get_list_detail_imei($imei){
        $db = $this->getDbConnection();
        $query = "select * from $this->_tablename where  imei = '$imei' order by id desc";
        $result = $db->fetchAll($query);
        return $result[0];
    }

    public function getListByUserId($receiver_id=0,$start,$end,$storeid=-2,$complete=-1,$status_guarantee=0,$transfers=0,$enabled ="",$storeid_in=0,$ok=-1,$bhang=0,$quoc_te="",$loai_phieu=0){
        $db = $this->getDbConnection();
        $query = "select * from $this->_tablename where 1=1";
        if((int)$receiver_id >0){
            $query .= " and receiver_id = $receiver_id";
        }
        if($storeid >-2){
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
        if($quoc_te != NULL){
            $query .= " and quoc_te = $quoc_te";
        }
        $query.=" and datetime >= '$start' and datetime <= '$end'";
        
        if($enabled != NULL){
            $query .= " and enabled = $enabled";
        }
        if($loai_phieu>0){
            $query .= " and loai_phieu = $loai_phieu";
        }
        $query .= "  and enabled > 0";
        if($ok >-1){
            $query .= "  and ok IN ($ok)";
        }
        $query .= " and iddepartment = 0 ";
        $query .= " order by id desc";
        $query .= " LIMIT 0, 200 ";
//        echo "<pre>";
//        var_dump($query);
        $result = $db->fetchAll($query);
        return $result;
    }
    public function get_list_out($start,$end,$storeid=0,$flag=0){
        $db = $this->getDbConnection();
        $query = "select * from $this->_tablename where 1=1";
        if($storeid>-2){
            $query .= " and storeid IN ($storeid)";
        }
        
        if((int)$flag >0){
            $query .= " and flag = $flag";
        }
        $query.=" and date_out > '$start' and date_out <= '$end' and ok=1 and money_dvsc >0 ";
        $query .= "  and enabled =1 and iddepartment >0 and complete >1 and complete !=3";
        $query .= " order by id desc";
        
        $result = $db->fetchAll($query);
        return $result;
    }
    public function get_list_out2($start,$end,$storeid=0,$flag=0,$money_dvsc=0){
        $db = $this->getDbConnection();
        $query = "select * from $this->_tablename where 1=1";
        
        if($storeid>-2){
            $query .= " and storeid IN ($storeid)";
        }
        
        if((int)$flag >0){
            $query .= " and flag = $flag";
        }
        $query.=" and date_out > '$start' and date_out <= '$end' and ok=1  ";
        if((int)$money_dvsc==0){
            $query .=" and money_dvsc >0 ";
        }
        $query .= "  and enabled =1 and iddepartment >0 and complete >1 ";
        $query .= " order by id desc";
        $result = $db->fetchAll($query);
        return $result;
    }
    public function get_list_in($start,$end,$storeid=0,$flag=0){
        $db = $this->getDbConnection();
        $query = "select * from $this->_tablename where 1=1";
        
        if((int)$storeid >-2){
            $query .= " and storeid IN ($storeid)";
        }
        if((int)$flag >0){
            $query .= " and flag = $flag";
        }
        $query.=" and datetime > '$start' and datetime <= '$end' ";
        $query .= "  and enabled =1 and iddepartment = 0 ";
        $query .= " order by id desc";
        $result = $db->fetchAll($query);
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

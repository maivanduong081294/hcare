<?php

class Business_Addon_Cart extends Business_Abstract {

    private $_tablename = 'addon_quick_cart';

    const KEY_LIST_ALL = 'qcart.list.all.%s';   //key of list by language
    const KEY_DETAIL = 'qcart.detail.%s';

    private static $_instance = null;

    function __construct() {
        
    }

    /**
     * get instance of Business_Addon_Cart
     *
     * @return Business_Addon_Cart
     */
    public static function getInstance() {
        if (self::$_instance == null) {
            self::$_instance = new Business_Addon_Cart();
        }
        return self::$_instance;
    }

    private function getKeyDetail($id) {
        return sprintf(self::KEY_DETAIL, $id);
    }

    private function getKeyList($enabled) {
        return sprintf(self::KEY_LIST_ALL, $enabled);
    }

    /**
     * get Zend_Db connection
     *
     * @return Zend_Db_Adapter_Abstract
     */
    function getDbConnection() {
        $db = Globals::getDbConnection('maindb', false);
        return $db;
    }

    private function getCacheInstance() {
        $cache = GlobalCache::getCacheInstance('ws');
        return $cache;
    }
    public function getListByOption($tong="",$created_date,$end_date,$check_call="",$enabled ="",$sell="",$productsid){
        $db = $this->getDbConnection();
        if($productsid==null){
            $productsid=6020;
        }
        if($tong != ''){
            $query = " SELECT vote_id,check_call,count(check_call) as tong FROM `addon_quick_cart` where productsid = $productsid and datetime > '$created_date' and datetime < '$end_date' GROUP BY vote_id,productsid,check_call";
        }
        if($check_call != null){
            $query = " SELECT vote_id,check_call,count(check_call) as tong FROM `addon_quick_cart` where check_call=0 and enabled=1 and productsid = $productsid and datetime > '$created_date' and datetime < '$end_date' GROUP BY vote_id,productsid,check_call";
        }
        if($enabled != null){
            $query = " SELECT vote_id,enabled,count(enabled) as tong FROM `addon_quick_cart` where  description is not null and enabled=0 and productsid = $productsid and sell=0 and datetime > '$created_date' and datetime < '$end_date' GROUP BY vote_id,productsid,enabled";
        }
        if($sell != null){
            $query = " SELECT vote_id,sell,count(sell) as tong FROM `addon_quick_cart` where sell=1 and check_call =1 and productsid = $productsid and datetime > '$created_date' and datetime < '$end_date' GROUP BY vote_id,productsid,sell";
//            var_dump($query);exit();
        }
//        var_dump($query);exit();
        $result = $db->fetchAll($query);
        return $result;
        
    }
    public function checkCall($productsid,$created_date,$end_date){
        $db = $this->getDbConnection();
        $query = " SELECT vote_id,check_call,count(check_call) as total FROM `addon_quick_cart` where  enabled=1 and sell=0 and productsid = $productsid and datetime > '$created_date' and datetime < '$end_date' GROUP BY vote_id,productsid,check_call";
        $result = $db->fetchAll($query);
        return $result;
    }
    public function tong($productsid,$created_date,$end_date){
        $db = $this->getDbConnection();
        $query = " SELECT vote_id,count(productsid) as total FROM `addon_quick_cart` where productsid = $productsid and datetime > '$created_date' and datetime < '$end_date' GROUP BY vote_id,productsid";
        $result = $db->fetchAll($query);
        return $result;
    }
    
    public function get_count_by_date($date)
	{
            $cache = $this->getCacheInstance();
            $key = "get_count_by_dates.$this->_tablename.$date";
            $result = $cache->getCache($key);
//            $result = FALSE;
            if($result === FALSE)
            {
                $db = $this->getDbConnection();
                $query   = "SELECT count(*) as total FROM  $this->_tablename ";
                $query  .= "where enabled = 1 and DATE(datetime) =  '$date'";
                $result = $db->fetchAll($query);
                $result = $result[0];
                if(!is_null($result) && is_array($result))
                {
                    $cache->setCache($key, $result,600);
                }
            }
            return $result;		
	}
    
    public function get_list_by_itemid_phone($products_id =0,$phone="",$start,$end){
        $db = $this->getDbConnection();
        $query = "select * from $this->_tablename where 1 = 1 ";
        
        if((int)$products_id >0){
            $query .= " and productsid = $products_id ";
        }
        if($phone != NULL){
            $query .= " and phone = '$phone' ";
        }
        $query .= " and datetime > '$start' and datetime <= '$end' ";
        $query .= " order by datetime DESC ";
//        var_dump($query);//exit();
        $result = $db->fetchAll($query);
        return $result;
    }
    public function get_list_by_preorder($preorder,$start,$end){
        $db = $this->getDbConnection();
        $query = "select * from $this->_tablename where preorder = $preorder and oks = 0 and enabled=1";
        $query .= " and datetime > '$start' and datetime <= '$end' ";
        $query .= " order by datetime DESC ";
        $result = $db->fetchAll($query);
        return $result;
    }








    public function getListByProductsId($keywork="",$products_id ="",$vote_id=0,$enabled=1,$mb_id=""){
        $db = $this->getDbConnection();
        $query = "select * from $this->_tablename where enabled = $enabled ";
        if($keywork != null){
            $query .=" and (phone like '%$keywork%' or name  like '%$keywork%' or product_name like '%$keywork%' or id= '$keywork') ";
        }else{
            if($vote_id != 0){
                $query .= " and vote_id = $vote_id ";
            }
        }
        if($products_id != ""){
            $query .= " and productsid = $products_id ";
        }
        $query .= " order by datetime DESC ";
//        var_dump($query);exit();
        $result = $db->fetchAll($query);
        return $result;
    }
        public function getListByProductsId2($keywork="",$products_id ="",$vote_id=0,$enabled="",$mb_id=0, $offset=0, $records=50){
        $db = $this->getDbConnection();
        $query = "select * from $this->_tablename where 1=1 ";
        if($enabled ==0){//chưa bán
            $query .=" and sell = 0 and enabled = 1";
        }
        if($enabled ==1){//đã bán
            $query .=" and sell = 1 ";
        }
        if($enabled ==2){//đã hủy
            $query .=" and enabled = 0 and sell=0 ";
        }
        if($keywork != null){
            $query .=" and (phone like '%$keywork%' or name  like '%$keywork%' or product_name like '%$keywork%' or id= '$keywork') ";
        }
        if ((int)$vote_id >0) {
            $query .= " and vote_id = $vote_id ";			
        }
        if($products_id != ""){
            $query .= " and productsid = $products_id ";
        }
        // fix 304 user hnam_hoangtpk
        if((int)$mb_id >0 && (int)$mb_id != 392 && (int)$mb_id != 304){
            $query .= " and id_users = $mb_id ";
        }
        $query .= " order by datetime DESC ";
        $query .= " LIMIT $records OFFSET $offset ";
        $result = $db->fetchAll($query);
        return $result;
    }
        public function getListByProductsId3($keywork="",$products_id ="",$vote_id=0,$enabled="",$mb_id="", $offset=0, $records=50){
        $db = $this->getDbConnection();
        $query = "select * from $this->_tablename where 1=1 ";
        if($enabled ==0){//chưa bán
            $query .=" and sell = 0 and enabled = 1";
        }
        if($enabled ==1){//đã bán
            $query .=" and sell = 1 ";
        }
        if($enabled ==2){//đã hủy
            $query .=" and enabled = 0 and sell=0 ";
        }
        if($keywork != null){
            $query .=" and (phone like '%$keywork%' or name  like '%$keywork%' or product_name like '%$keywork%' or id= '$keywork') ";
        }
        if($vote_id != 0){
            $query .= " and vote_id = $vote_id ";
        }
        if($products_id != ""){
            $query .= " and productsid = $products_id ";
        }
        if($vote_id != $mb_id){
            $query .= " and id_users = $mb_id ";
        }
        $query .= " order by datetime DESC ";
        $query .= " LIMIT $records OFFSET $offset ";
//        var_dump($query);exit();
        $result = $db->fetchAll($query);
        return $result;
    }
    public function countXiaomi($products_id="",$vote_id=0,$check_call){
        
        $cache = $this->getCacheInstance('ws');
            $key = "countXiaomi.$this->_tablename.$products_id.$vote_id.$vote_id.$check_call";
            $result = $cache->getCache($key);
//            $result= FALSE;
            if($result=== FALSE){
                $db = $this->getDbConnection();
                $query = "select count(*) as tong from $this->_tablename where 1=1 ";
                $query .= " and check_call = $check_call and enabled=1 ";
                if($products_id != ""){
                    $query .= " and productsid = $products_id ";
                }
                if($vote_id != 0){
                    $query .= " and vote_id = $vote_id ";
                }
        //        var_dump($query);exit();
                $result = $db->fetchAll($query);
                if(!is_null($result) && is_array($result))
                    {
                        $cache->setCache($key, $result, 60 * 5);
                    }
            }
        return $result;
    }

    public function getList($limit = '') {
        $cache = $this->getCacheInstance();
        $key = $this->getKeyList($limit);
//        $result = $cache->getCache($key);
        ($limit === '' ? true : $limit = ' LIMIT 0, ' . $limit);
//        if ($result === FALSE) {
            $db = $this->getDbConnection();

            $query = "select * from " . $this->_tablename . " order by datetime DESC " . $limit;

            $result = $db->fetchAll($query);

//            $cache->setCache($key, $result);
//        }
        return $result;
    }

    public function getDetail($rid) {
        $db = $this->getDbConnection();

        $query = "select * from " . $this->_tablename . " WHERE id = " . $rid;

        $result = $db->fetchAll($query);
        return $result[0];
    }

    public function insert($data) {
        $db = $this->getDbConnection();
        $result = $db->insert($this->_tablename, $data);
        return $db->lastInsertId();
    }

    public function update($data) {
        $id = $data["id"];
        unset($data["id"]);
        $db = $this->getDbConnection();
        $where = array();
        $where[] = "id='" . parent::adaptSQL($id) . "'";
        try {
            $result = $db->update($this->_tablename, $data, $where);
            return $result;
        } catch (Exception $e) {
            return false;
        }
    }
    public function update_parrent_id($id,$data) {
        $db = $this->getDbConnection();
        $where = array();
        $where[] = "parrent_id='" . parent::adaptSQL($id) . "'";
        try {
            $result = $db->update($this->_tablename, $data, $where);
            return $result;
        } catch (Exception $e) {
            return false;
        }
    }
    public function updates($id,$data) {
        $db = $this->getDbConnection();
        $where = array();
        $where[] = "id='" . parent::adaptSQL($id) . "'";
        try {
            $result = $db->update($this->_tablename, $data, $where);
            return $result;
        } catch (Exception $e) {
            return false;
        }
    }
    public function updates2($id,$data) {
        $db = $this->getDbConnection();
        $where = array();
        $where[] = "parrent_id='" . parent::adaptSQL($id) . "'";
        try {
            $result = $db->update($this->_tablename, $data, $where);
            return $result;
        } catch (Exception $e) {
            return false;
        }
    }
    public function getDanhSachMuaHang($ids='',$start,$end,$store = '',$select='',$type=0) {
        $db = $this->getDbConnection();
        $where = '(1)';
        if($store) {
            $where .= " and vote_id = $store";
        }
        if($type){
            if($ids) {
                $where .= " and products_id in ($ids)";
            }
            $select = $select?$select:'fullname_addon,phone_addon,create_date,products_name,products_id,vote_id';
            $query = "SELECT $select FROM  `users_products` where $where  and create_date >= '$start' and create_date <= '$end' order by autoid DESC";
            //var_dump($query);die();
        }
        else {
            if($ids) {
                $where .= " and productsid in ($ids)";
            }
            $select = $select?$select:'name,phone,email,datetime,product_name,productsid,vote_id';
            $query = "SELECT $select FROM  `addon_quick_cart` where $where  and datetime >= '$start' and datetime <= '$end' order by id DESC";
        }
        $result = $db->fetchAll($query);
        return $result;
    }

    public function getDanhSachLayHang($ids='',$start,$end,$store = '') {
        $db = $this->getDbConnection();
        $where = '(1)';
        if($store) {
            $where .= " and vote_id = $store";
        }
        if($ids) {
            $where .= " and products_id in ($ids)";
        }
        $select = 'fullname_addon,phone_addon,create_date,products_name,products_id,vote_id,products_price';
        $query = "SELECT $select FROM  `users_products` where $where and is_actived = 1 and create_date >= '$start' and create_date <= '$end' order by create_date ASC";
        $result = $db->fetchAll($query);
        return $result;
    }

    public function getDanhSachLayHangByYear($year,$quarter='',$store = '') {
        $db = $this->getDbConnection();
        $where = '(1)';

        if($quarter) {
            $quarter = ($quarter > 4) ? 4 : (($quarter <= 0) ? 1 : $quarter);
            switch ($quarter) {
                case 1:
                    $month = array(1, 3);
                    break;
                case 2:
                    $month = array(4, 6);
                    break;
                case 3:
                    $month = array(7, 9);
                    break;
                default:
                    $month = array(10, 12);
            }
            $where .= " and month(create_date) >= {$month[0]} and month(create_date) <= {$month[1]}";
        }
        if($store) {
            $where .= " and vote_id = $store";
        }
        $select = 'fullname_addon,phone_addon,create_date,products_name,products_id,vote_id,products_price';
        $query = "SELECT $select FROM  `users_products` where $where and is_actived = 1 and year(create_date) = $year order by create_date ASC";
        $result = $db->fetchAll($query);
        return $result;
    }

    public function getOrderOnline($ids='',$data,$start,$end,$store = '') {
        if(!$data['start'] || !$data['end']) {
            return false;
        }
        $start = $data['start'];
        $end = $data['end'];
        $db = $this->getDbConnection();
        $where = '';
        if($data['store']) {
            $where .= " and vote_online = {$data['store']}";
        }
        if($data['ids']) {
            $where .= " and products_id in ({$data['ids']})";
        }
        $query = "SELECT * FROM  `users_products` where vote_id = 167 $where  and create_date >= '$start' and create_date <= '$end' and vote_online != 0 order by id DESC";
        //var_dump($query);die();
        $result = $db->fetchAll($query);
        return $result;
    }

}

?>
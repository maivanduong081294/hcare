<?php

class Business_Addon_General extends Business_Abstract {

    private static $_instance = null;

    function __construct() {
        
    }

    //public static function
    /**
     * get instance of Business_Addon_Addon
     *
     * @return Business_Addon_Addon
     */
    public static function getInstance() {
        if(self::$_instance == null) {
            self::$_instance = new Business_Addon_General();
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

    public function getTelegramHistory() {
        $db = $this->getDbConnection();
        $time = strtotime('now')-3600;
        $date = date('Y-m-d H:i:s',$time);
        $query = "select * from addon_telegram_history order by id desc limit 100";
        $_result = $db->fetchAll($query);
        if($_result) {
            return $_result;
        }
        return 0;
    }

    public function getOpenBillHistory($limit = 100,$where='') {
        $db = $this->getDbConnection();
        $time = strtotime('now')-3600;
        $date = date('Y-m-d H:i:s',$time);
        $query = "select * from addon_open_bill_history where datetime <= '$date' $where order by id asc limit $limit";
        $_result = $db->fetchAll($query);
        if($_result) {
            return $_result;
        }
        return 0;
    }

    public function getProductsGuaranteePK() {
        $db = $this->getDbConnection();
        $query = "select pbh.*,pi.title from addon_baohiem_products as pbh inner join ws_productsitem as pi on pi.itemid = pbh.itemid where pbh.status = 1 order by pbh.id asc";
        $result = $db->fetchAll($query);
        return $result;
    }

    public function getProductsByVT($mavt) {
        $db = $this->getDbConnection();
        $query = "select * from  addon_product_color where code in ($mavt) order by id asc";
        $result = $db->fetchAll($query);
        return $result;
    }

    public function excuteCode($query){
        $db     =  $this->getDbConnection();
        $result = $db->query(''.$query.'');
    }

    public function getDataDB($table,$select='',$where='',$orderby='',$limit='') {
        $db = $this->getDbConnection();
        $select = $select?$select:'*';
        $orderby = $orderby?$orderby:'id asc';
        $query = "select $select from $table";
        if($where) {
            $query .= " where (1) and $where";
        }
        $query.= " order by $orderby";
        if($limit) {
            $query .= " limit $limit";
        }
        $_result = $db->fetchAll($query);
        if($_result) {
            return $_result;
        }
        return 0;
    }

    public function insertDB($table,$data) {
        $db = $this->getDbConnection();
        $result = $db->insert($table,$data);
        if ($result > 0) {
            $lastid= $db->lastInsertId($table);
        }
        return $lastid;
    }

    public function updateDB($table,$data,$query) {
        $db= $this->getDbConnection();
        $result = $db->update($table, $data, $query);
        return $result;
    }

    public function deleteDB($table,$where) {
        $db= $this->getDbConnection();
        $result = $db->delete($table,$where);
        return $result;
    }
}
?>

<?php

class Business_Addon_Target extends Business_Abstract {

    private $_tablename = 'addon_target';
    private static $_instance = null;

    function __construct() {
        
    }

    //public static function
    /**
     * get instance of Business_Addon_Target
     *
     * @return Business_Addon_Target
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

    public function getSaleTotal($created_day = "", $end_day = "", $is_actived = 1) {
        $cache = $this->getCacheInstance();
        $key = md5("salebystoret.$created_day.$end_day.$is_actived");
        $result = $cache->getCache($key);
        $result = false;
        if ($result === FALSE) {
            $db = $this->getDbConnection();
            $query = "SELECT flag,cated_id as cate_id, sum(products_price) as sum, count(products_id) as countp,sum(reduction_money) as reduction_money,sum(money_voucher) as money_voucher FROM $this->_tablename WHERE 1=1 ";
            if ($created_day != null) {
                $query .=" and create_date >=  '$created_day'";
            }
            if ($end_day != null) {
                $query .="  and create_date <= '$end_day 23:59:59'";
            }
            if ($is_actived != null) {
                $query .="  and is_actived = $is_actived";
            }
            $query .="  and status2 = 0";
            $query .= " group by flag, cated_id";
            $result = $db->fetchAll($query);
            if (!is_null($result) && is_array($result)) {
                $cache->setCache($key, $result, 60 * 10);
            }
        }
        return $result;
    }

    public function insert($data) {
        $db = $this->getDbConnection();
        $result = $db->insert($this->_tablename, $data);
        $this->_deleteAllCache($itemid, $data['poll_id']);
        return $result;
    }

    public function delete($itemid) {
        $current = $this->getDetail($itemid);
        $db = $this->getDbConnection();
        $where = array();
        $where[] = "item_id='" . parent::adaptSQL($itemid) . "'";
        $result = $db->delete($this->_tablename, $where);
        if ($result > 0) {
            $this->_deleteAllCache($itemid, $current['poll_id']);
        }
        return $result;
    }

    public function update($itemid, $data) {
        $db = $this->getDbConnection();
        $where = array();
        $where[] = "item_id = '" . parent::adaptSQL($itemid) . "'";
        try {
            $result = $db->update($this->_tablename, $data, $where);

            if ($result > 0) {
                $this->_deleteAllCache($itemid, $data['poll_id']);
            }

            return $result;
        } catch (Exception $e) {
            return 0;
        }
    }

}

?>

<?php

class Business_Addon_Repayment extends Business_Abstract {

    private $_tablename = 'addon_repayment';

    const KEY_LIST_ALL = 'rp.list.all.%s';   //key of list by language
    const KEY_DETAIL = 'rp.detail.%s';

    private static $_instance = null;

    function __construct() {
        
    }

    /**
     * get instance of Business_Addon_Repayment
     *
     * @return Business_Addon_Repayment
     */
    public static function getInstance() {
        if (self::$_instance == null) {
            self::$_instance = new Business_Addon_Repayment();
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

    public function getList($limit = '') {
        $cache = $this->getCacheInstance();
        $key = $this->getKeyList($limit);
        $result = $cache->getCache($key);
//        $result=false;
        ($limit === '' ? true : $limit = ' LIMIT 0, ' . $limit);
        if ($result === FALSE) {
            $db = $this->getDbConnection();

            $query = "select * from " . $this->_tablename . " WHERE type = 0 order by datetime DESC " . $limit;

            $result = $db->fetchAll($query);

            $cache->setCache($key, $result);
        }
        return $result;
    }
    public function get_list_by_status($store_id) {
        $cache = $this->getCacheInstance();
        $key = $this->_tablename."get_list_by_status2".$store_id;
        $result = $cache->getCache($key);
//        $result=false;
        if ($result === FALSE) {
            $db = $this->getDbConnection();
            $query = "SELECT count(*) as total FROM addon_repayment  where type !=2 and status = 0 and MONTH(datetime) >= 5 and YEAR(datetime) >= 2017";
            if((int)$store_id >0){
                $query .=" and  store_id = $store_id";
            }
            $query .="  limit 50";
            
            $result = $db->fetchAll($query);
            $result = $result[0];
            
            $cache->setCache($key, $result,1000);
        }
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

}

?>
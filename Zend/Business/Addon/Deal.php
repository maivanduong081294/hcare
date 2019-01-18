<?php

class Business_Addon_Deal extends Business_Abstract {

    private $_tablename = 'addon_deal';

    const KEY_LIST_ALL = 'deal.list.all.%s.%s';   //key of list by language
    const KEY_DETAIL = 'deal.detail.%s';

    private static $_instance = null;

    function __construct() {
        
    }

    /**
     * get instance of Business_Addon_Deal
     *
     * @return Business_Addon_Deal
     */
    public static function getInstance() {
        if (self::$_instance == null) {
            self::$_instance = new Business_Addon_Deal();
        }
        return self::$_instance;
    }

    private function getKeyDetail($id) {
        return sprintf(self::KEY_DETAIL, $id);
    }

    private function getKeyList($enabled, $page) {
        return sprintf(self::KEY_LIST_ALL, $enabled, $page);
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

    public function getLastestProductsTime($pid) {
        $db = $this->getDbConnection();

        $query = "select * from " . $this->_tablename . " where product_id = ".(int)$pid." order by datetime desc";

        $result = $db->fetchAll($query);
        
        return $result[0]['datetime'];
    }
    
    public function getList($enabled='', $page=1, $params = array()) {
        $records = 40;

        $cache = $this->getCacheInstance();
        $key = $this->getKeyList($enabled, $page . md5(implode("", $params)));
        $result = $cache->getCache($key);
        if ($enabled === '')
            $where[] = ' 1=1 ';
        else
            $where[] = ' enabled=' . (int) $enabled;
        if ($page > 0)
            $offset = ($page - 1) * $records;
        else {
            $offset = 0;
        }   
        
        if (is_array($params) && count($params)>0)
            $where = array_merge($where, $params);
        
        $where = implode(" AND ", $where);
        
        $limit = " LIMIT $offset, $records";
        
        if ($result === FALSE) {
            $db = $this->getDbConnection();

            $query = "select * from " . $this->_tablename . " where $where order by myorder asc, datetime desc" . $limit;

            $result = $db->fetchAll($query);

            $cache->setCache($key, $result);
        }        
        return $result;
    }
    
    public function getTotal($enabled='') {
        $records = 20;

        $cache = $this->getCacheInstance();
        $key = $this->getKeyList($enabled, 'total');
        $result = $cache->getCache($key);
        if ($enabled === '')
            $where[] = ' 1=1 ';
        else
            $where[] = ' enabled=' . (int) $enabled;
        if ($page > 0)
            $offset = ($page - 1) * $records;
        else {
            $offset = 0;
        }   
        
        $where = implode(" AND ", $where);
        
        if ($result === FALSE) {
            $db = $this->getDbConnection();

            $query = "select count(product_id) total from " . $this->_tablename . " where $where ";

            $result = $db->fetchAll($query);

            if(count($result>0)) {
                
                $result = $result[0]['total'];
                $cache->setCache($key, $result);
            }
            
        }
        return $result;
    }

    public function getDetailByProduct_id($product_id) {
        $cache = $this->getCacheInstance();
        $product_id = intval($product_id);
        $db = $this->getDbConnection();
        $query = " SELECT * FROM " . $this->_tablename . " WHERE product_id = ?";
        $data = array($product_id);
        $result = $db->fetchAll($query, $data);
        if ($result != null && is_array($result)) {
            $cache->setCache($key, $result[0]);
            return $result[0];
        }
        return null;
    }

    public function getDetail($deal_id) {
        $cache = $this->getCacheInstance();
        $key = $this->getKeyDetail($deal_id);
        $result = $cache->getCache($key);
        if ($result === FALSE) {
            $itemid = intval($itemid);
            $db = $this->getDbConnection();
            $query = " SELECT * FROM " . $this->_tablename . " WHERE deal_id = ?";
            $data = array($deal_id);
            $result = $db->fetchAll($query, $data);
            if ($result != null && is_array($result)) {
                $cache->setCache($key, $result[0]);
                return $result[0];
            }
        }
        return $result;
    }
    
    
    public function update($itemid, $data) {
        
        $cache = $this->getCacheInstance();
        $cache->flushAll();
        
        $db = $this->getDbConnection();
        $where = array();
        $where[] = "product_id = '" . parent::adaptSQL($itemid) . "'";
        try {
            $result = $db->update($this->_tablename, $data, $where);

            if ($result > 0) {
                $this->_deleteAllCache();
            }

            return $result;
        } catch (Exception $e) {
            return 0;
        }
    }

    public function updateOrdering($product_id, $myorder) {
        $product_id = (int)$product_id;
        $myorder = (int)$myorder;
        
        if ((int)$product_id == 0 || (int)$myorder < 0) return;
        
        $detail = $this->getDetailByProduct_id($product_id);
        
        $myorder_from = $detail['myorder'];
        
//        pr($myorder_from);
//        pre($myorder);
        if ((int)$myorder_from < (int)$myorder) {
            
            $db = $this->getDbConnection();
            $query = "UPDATE " . $this->_tablename . " SET myorder = myorder - 1 WHERE  myorder > ? AND myorder <= ? ";            
            $data = array($myorder_from, $myorder);

        } else {
            
            $db = $this->getDbConnection();
            $query = "UPDATE " . $this->_tablename . " SET myorder = myorder + 1 WHERE  myorder >= ? AND myorder <= ? ";
            $data = array($myorder, $myorder_from);
            
        }
        
        $db->query($query, $data);
        
        $detail['myorder'] = $myorder;
        $this->update($product_id, $detail);
        
        
        
//        pr($myorder);
//        pre($product_id);
//        $db = $this->getDbConnection();
//        $query = "UPDATE " . $this->_tablename . " SET myorder = myorder + 1 WHERE  myorder >= ?";
//        $data = array($myorder);
//        
//        $query = "UPDATE " . $this->_tablename . " SET myorder = ? WHERE  product_id = ?";
//        $data = array($myorder, $product_id);
//        $db->query($query, $data);
    }

    public function insert($data) {
        $db = $this->getDbConnection();
        $this->updateOrdering($data['product_id'], 0);
        $result = $db->insert($this->_tablename, $data);
        $this->_deleteAllCache();
        
        return $db->lastInsertId();
    }

    public function delete($itemid) {
        $current = $this->getDetail($itemid);
        $db = $this->getDbConnection();
        $where = array();
        $where[] = "product_id='" . parent::adaptSQL($itemid) . "'";
        $result = $db->delete($this->_tablename, $where);
        if ($result > 0) {
            $this->_deleteAllCache();
        }
        return $result;
    }

    private function _deleteAllCache() {
        $cache = $this->getCacheInstance();
        $cache->flushAll();
    }

    public function getMaxOrdering() {
        $db = $this->getDbConnection();
        $query = "SELECT max(myorder) as max FROM " . $this->_tablename . " WHERE 1 ";
        $result = $db->fetchAll($query);
        if (!is_null($result) && is_array($result)) {
            return $result[0]['max'];
        }
        return 0;
    }

}

?>
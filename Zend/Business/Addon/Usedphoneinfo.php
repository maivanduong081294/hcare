<?php

class Business_Addon_Usedphoneinfo extends Business_Abstract {

    private $_tablename = 'addon_usedphone_info';

    const KEY_LIST = 'upi.list.%s';   //key of list.questionid
    const KEY_DETAIL = 'upi.detail.%s';  //key of detail.id

    private static $_instance = null;

    function __construct() {
        
    }

    //public static function
    /**
     * get instance of Business_Addon_Usedphoneinfo
     *
     * @return Business_Addon_Usedphoneinfo
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

    public function getDetail($id) {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " WHERE id=?";
        $data = array($id);
        $result = $db->fetchAll($query, $data);
        return $result[0];
    }
    
    public function getDetailByItemID($itemid) {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " WHERE itemid=? ORDER datetime DESC";
        $data = array($itemid);
        $result = $db->fetchAll($query, $data);
        return $result[0];
    }

    public function getDetailByUsedID($used_id) {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " WHERE addon_usedphone_id=?";
        $data = array($used_id);
        $result = $db->fetchAll($query, $data);
        return $result[0];
    }
    
    public function insert($data) {
        $db = $this->getDbConnection();
        $result = $db->insert($this->_tablename, $data);
        if ($result > 0) {
            return $db->lastInsertId();
        }
        return 0;
    }

    public function delete($id, $active, $sid) {
        $db = $this->getDbConnection();
        $query = "id = $id";
        if ($active == 1) {
            $data = array(
                "active" => 0,
                "datetime_update" => date("Y-m-d H:i:s"),
                "storeid_update" => $sid,
            );
        } else {
            $data = array(
                "active" => 1,
                "datetime_update" => date("Y-m-d H:i:s"),
                "storeid_update" => $sid,
            );
        }

        $result = $db->update($this->_tablename, $data, $query);
//            var_dump($result);exit();
        return $result;
    }

    public function restore($id) {
        $db = $this->getDbConnection();
        $query = "id = " . $id;
        $data = array(
            "active" => 1
        );
        $result = $db->update($this->_tablename, $data, $query);
        return $result;
    }

    public function update($id, $data) {
        $db = $this->getDbConnection();
        $where = array();
        $where[] = "id = '" . parent::adaptSQL($id) . "'";
        try {
            $result = $db->update($this->_tablename, $data, $where);
        } catch (Exception $e) {
            return 0;
        }
    }
    public function updateByUsedID($usedid, $data) {
        $db = $this->getDbConnection();
        $where = array();
        $where[] = "addon_usedphone_id = '" . parent::adaptSQL($usedid) . "'";
        try {
            $result = $db->update($this->_tablename, $data, $where);
            return $result;
        } catch (Exception $e) {
            return 0;
        }
    }
    public function updateByItemID($itemid, $data) {
        $db = $this->getDbConnection();
        $where = array();
        $where[] = "itemid = '" . parent::adaptSQL($itemid) . "'";
        try {
            $result = $db->update($this->_tablename, $data, $where);
            return $result;
        } catch (Exception $e) {
            return 0;
        }
    }

}

?>

<?php

class Business_Addon_Ghn extends Business_Abstract {

    private $_tablename = 'addon_ghn';

    const KEY_LIST = 'ghn.list.%s';   //key of list.questionid
    const KEY_DETAIL = 'ghn.detail.%s';  //key of detail.id

    private static $_instance = null;

    function __construct() {
        
    }

    //public static function
    /**
     * get instance of Business_Addon_Ghn
     *
     * @return Business_Addon_Ghn
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

    public function getDetail($order_code) {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " WHERE order_code=?";
        $data = array($order_code);
        $result = $db->fetchAll($query, $data);
        return $result[0];
    }
    
    public function getList() {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " ORDER BY datetime DESC LIMIT 0, 100";
        $data = array();
        $result = $db->fetchAll($query, $data);
        return $result;
    }
    public function getListByDate($date_from="",$date_to="") {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " where 1=1 ";
        if($date_from != null){
            $query .=" and datetime >= '$date_from'";
        }
        if($date_to != null){
            $query .=" and datetime <= '$date_to'";
        }
        $query .=" ORDER BY datetime ASC LIMIT 0, 100";
//        echo "<pre>";
//        var_dump($query);
//        exit();
        $data = array();
        $result = $db->fetchAll($query, $data);
        return $result;
    }

    public function insert($data) {
        $db = $this->getDbConnection();
        $result = $db->insert($this->_tablename, $data);
        if ($result > 0) {
            $last_id = $db->lastInsertId();
            return $last_id;
        }
        else
            return 0;
    }
    public function update($id, $data) {
        $where = array();
        $where[] = "id='" . parent::adaptSQL($id) . "'";
        $db = $this->getDbConnection();
        $result = $db->update($this->_tablename, $data, $where);
    }

    public function delete($id) {
        //get current menu
        $where = array();
        $where[] = "id='" . parent::adaptSQL($id) . "'";
        $result = $db->delete($this->_tablename, $where);
    }

}

?>

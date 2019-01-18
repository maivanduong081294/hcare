<?php

class Business_Addon_ProgramSub extends Business_Abstract {

    private $_tablename = 'hnam_program_sub';
    private static $_instance = null;

    function __construct() {
        
    }

    //public static function
    /**
     * get instance of Business_Addon_ProgramSub
     *
     * @return Business_Addon_ProgramSub
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

    public function insert($data) {
        $db = $this->getDbConnection();
        $result = $db->insert($this->_tablename, $data);
        return $result; 
    }

    public function getDetail($id){
        $db= $this->getDbConnection();
        $query = "select * from $this->_tablename where id = $id";
        $result = $db->fetchAll($query);
        return $result[0];
    }
    public function get_list_by_id($str_id){
        $db= $this->getDbConnection();
        $query = "select * from $this->_tablename where enabled = 1 and id_program IN ($str_id) order by type asc";
        $result = $db->fetchAll($query);
        return $result;
    }
    public function get_list_by_date($date){
        $cache = $this->getCacheInstance();
        $key = $this->_tablename."get_list_by_date".$date;
        $result = $cache->getCache($key);
//        $result=false;
        if ($result === FALSE) {
            $db= $this->getDbConnection();
            $query = "select * from $this->_tablename where enabled = 1 ";
            $query .=" and start <='$date' and  end >='$date'";
            $query .="  order by type asc";
            $result = $db->fetchAll($query);
            if(!is_null($result) && is_array($result)){
                $cache->setCache($key, $result,800);
            }
        }
        
        return $result;
    }
    public function get_list_by_date2($start,$end){
        $db= $this->getDbConnection();
        $query = "select * from $this->_tablename where enabled = 1 ";
        $query .=" and start <='$start' and  end >='$end'";
        $query .="  order by type asc";
        $result = $db->fetchAll($query);
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

    public function update($id,$data) {
        $db= $this->getDbConnection();
        $query = "id = ".$id;
        $result = $db->update($this->_tablename, $data,$query);
        return $result;
    }

}

?>

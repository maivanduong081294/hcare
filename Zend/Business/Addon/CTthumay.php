<?php

class Business_Addon_CTthumay extends Business_Abstract {

    private $_tablename = 'hnam_ct_thu_may';
    private static $_instance = null;

    function __construct() {
        
    }

    //public static function
    /**
     * get instance of Business_Addon_CTthumay
     *
     * @return Business_Addon_CTthumay
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

    public function insert($data)
	{
		$db = $this->getDbConnection();
		$result = $db->insert($this->_tablename,$data);
                if ($result > 0) {
                    $lastid= $db->lastInsertId($this->_tablename); // tra ve id khi them vao
                }
                return $lastid;
	}
    public function getDetail($id){
        $db= $this->getDbConnection();
        $query = "select * from $this->_tablename where id = $id";
        $result = $db->fetchAll($query);
        return $result[0];
    }
    public function getList($id=null){
        $db= $this->getDbConnection();
        $query = "select * from $this->_tablename where enabled = 1";
        $query .= " order by id DESC";
        $result = $db->fetchAll($query);
        return $result;
    }
    public function get_list_by_id($strID){
        $db= $this->getDbConnection();
        $query = "select * from $this->_tablename where enabled = 1";
        if($strID != NULL){
            $query .=" and id IN ($strID)";
        }
        $query .= " order by id DESC";
        $result = $db->fetchAll($query);
        return $result;
    }
    public function getListAll(){
        $db= $this->getDbConnection();
        $query = "select * from $this->_tablename order by id desc";
//         order by datetime DESC
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

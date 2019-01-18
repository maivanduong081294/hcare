<?php

class Business_Addon_ApiSamsung extends Business_Abstract {

    private $_tablename = 'hnam_samsung';
    private static $_instance = null;

    function __construct() {
        
    }

    //public static function
    /**
     * get instance of Business_Addon_ApiSamsung
     *
     * @return Business_Addon_ApiSamsung
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
     

    public function update($id,$data) {
        $db= $this->getDbConnection();
        $query = "id = ".$id;
        $result = $db->update($this->_tablename, $data,$query);
        return $result;
    }
	
	public function getList($offset=0, $records=500) {		
		$db = $this->getDbConnection();
		$query = "SELECT * FROM " . $this->_tablename . " ORDER BY datetime DESC LIMIT $offset, $records ";
		$data = array();
		$result = $db->fetchAll($query,$data);
		return $result;
	}
	
	public function getListByImei($imei) {
		if ($imei=="") {
			return null;
		} else {
			$db = $this->getDbConnection();
			$query = "SELECT * FROM " . $this->_tablename . " WHERE imei='$imei'"; 
			$data = array();
			$result = $db->fetchAll($query,$data);
			return $result;
		}
	}
	
	public function deleteByImei($imei) 
	{	
		$where = array();
		$where[] = "imei='" . parent::adaptSQL($imei) . "'";
		$db = $this->getDbConnection();
		$result = $db->delete($this->_tablename, $where);
	}
	
	public function delete($id)
	{	
		$where = array();
		$where[] = "ID='" . parent::adaptSQL($id) . "'";
		$wherep[] =
		$db = $this->getDbConnection();
		$result = $db->delete($this->_tablename, $where);
	}

}

?>

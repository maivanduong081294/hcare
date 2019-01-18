<?php

class Business_Addon_Bills extends Business_Abstract {

    private $_tablename = 'bills';
    private static $_instance = null;

    function __construct() {
        
    }

    //public static function
    /**
     * get instance of Business_Addon_Bills
     *
     * @return Business_Addon_Bills
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
     public function getDetailByPC($number_pc,$month,$year){
        $db= $this->getDbConnection();
        $query = "select * from $this->_tablename where enabled =1 and number_pc = $number_pc and MONTH(bill_datetime) = $month and YEAR(bill_datetime) = $year";
        $result = $db->fetchAll($query);
        return $result[0];
    }
    public function getDetail($id){
        $db= $this->getDbConnection();
        $query = "select * from $this->_tablename where id = $id";
        $result = $db->fetchAll($query);
        return $result[0];
    }
    public function getList($departmentid=0,$storeid=0,$month=0,$year,$orderby=0){
        $db= $this->getDbConnection();
        $query = "select * from $this->_tablename where enabled = 1 and YEAR(bill_datetime) = $year ";
        if((int)$month>0){
            $query .=" and MONTH(bill_datetime) = $month ";
        }
        if(intval($departmentid) >0){
            $query .=" and departmentid = $departmentid";
        }
        if(intval($storeid) >0){
            $query .=" and storeid = $storeid";
        }
        if($orderby ==0){
           $query .= " order by number_pc DESC" ;
        }else{
             $query .= " order by number_pc ASC" ;
        }
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

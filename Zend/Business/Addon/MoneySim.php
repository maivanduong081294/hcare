
<?php

class Business_Addon_MoneySim extends Business_Abstract {

    private $_tablename = 'money_sim';
    private static $_instance = null;
    const KEY_DETAIL_SIM = 'detail_money_sim.%s';  //key of detail.id
    const KEY_LIST_SIM = 'list_money_sim.$s';  //key of list.id
    function __construct() {
        
    }

    //public static function
    /**
     * get instance of Business_Addon_MoneySim
     *
     * @return Business_Addon_MoneySim
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
    public function getList($vote_id){
        $db = $this->getDbConnection();
        $query  = "select * from $this->_tablename where enabled =1 ";
        if($vote_id != null){
            $query .= " and vote_id = $vote_id ";
        }
        $query .= " ORDER BY id DESC";
//        var_dump($query);exit();
        $result = $db->fetchAll($query);
//        var_dump($result);exit();
        return $result;
    }
    public function insert($data) {
        $db = $this->getDbConnection();
        $result = $db->insert($this->_tablename, $data);
//        var_dump($result);exit();
        return $result;
    }
    public function update($id,$data) {
        $db= $this->getDbConnection();
        $query = "id = ".$id;
        $result = $db->update($this->_tablename, $data,$query);
//        $this->_deleteAllCache();
        return $result;
    }
 
}

?>

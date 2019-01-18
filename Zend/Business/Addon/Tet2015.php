<?php

class Business_Addon_Tet2015 extends Business_Abstract {

    private $_tablename = 'addon_tet2015';

    const KEY_LIST = 'up.tet.%s';   //key of list.questionid
    const KEY_DETAIL = 'up.tet.%s';  //key of detail.id

    private static $_instance = null;

    function __construct() {
        
    }

    //public static function
    /**
     * get instance of Business_Addon_Tet2015
     *
     * @return Business_Addon_Tet2015
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
        $cache = GlobalCache::getCacheInstance('default');
        return $cache;
    }
    
    public function getRandomCode($ip) {        
        $db = $this->getDbConnection();
        $query = "SELECT *, rand() as RND FROM " . $this->_tablename . " WHERE used=0 ORDER BY RND ASC LIMIT 0,1 ";
        $data = array($code);
        $result = $db->fetchAll($query, $data);
        $result = $result[0];
            
        //update to used
        $result["ip"] = $ip;
        $result["used"]=1;
        $result["datetime_used"] = date("Y-m-d H:i:s");
        unset($result["RND"]);
        $this->update($result["code"], $result);
        return $result;
    }

    public function getDetail($code) {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " WHERE code=?";
        $data = array($code);
        $result = $db->fetchAll($query, $data);
        return $result[0];
    }
    
//    20.000 code trúng 20.000đ. 
//    1.000 code trúng 50.000đ, 
//    100 code trúng 100.000đ và 
//    3 code trúng 1.000.000đ
    public function renderCode($name, $total) {
        $ret = "";
        for($i=0; $i<$total; $i++) {
            $codes = Business_Common_Utils::generateRandomWord(7);
            $ret .= "INSERT INTO `hnamv3`.`addon_tet2015` (`id`, `code`, `name`, `used`, `datetime`) VALUES (NULL, '".$codes."', '".$name."', '0', '2015-01-29 00:00:00');";
        }
        return $ret;
    }
    
    public function update($code, $item) {           
        $where = array();
        $where[] = "code='" . parent::adaptSQL($code) . "'";
        $db = $this->getDbConnection();
        $result = $db->update($this->_tablename, $item,$where);
        return $result;
    }
}

?>

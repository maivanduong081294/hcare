<?php

class Business_Addon_Sql extends Business_Abstract {

    private $_tablename = 'hnam_';
    private static $_instance = null;

    function __construct() {
        
    }

    //public static function
    /**
     * get instance of Business_Addon_Sql
     *
     * @return Business_Addon_Sql
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
     * @return Maro_Db_Interface
     */
    private function getDbConnection() {
        $db = Globals::getDbInstance('windows');
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

    public function select($query) {
        $db = $this->getDbConnection();
        try {
            $result = $db->select($query);
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
        }
        return $result;
    }
    public function excute($query) {
        
        $db = $this->getDbConnection();
        try {
            $result = $db->excute($query);
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
        }
        return $result;
    }

}

?>

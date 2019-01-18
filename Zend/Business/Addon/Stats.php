<?php

class Business_Addon_Stats extends Business_Abstract {

    private $_tablename = 'addon_stats';

    const KEY_LIST = 'stats.list.%s';   //key of list.questionid
    const KEY_DETAIL = 'stats.detail.%s';  //key of detail.id

    private static $_instance = null;

    function __construct() {
        
    }

    //public static function
    /**
     * get instance of Business_Addon_Stats
     *
     * @return Business_Addon_Stats
     */
    public static function getInstance() {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    private function getKeyListByQuest($qid) {
        return sprintf(self::KEY_LIST, $qid);
    }

    private function getKeyDetail($id) {
        return sprintf(self::KEY_DETAIL, $id);
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
        $cache = GlobalCache::getCacheInstance('nt');
        return $cache;
    }

    public function insert($data) {
        $db = $this->getDbConnection();
        $last_id = $db->insert($this->_tablename, $data);
        return $last_id;
    }

}

?>

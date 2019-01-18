<?php

class Business_WsNew_MenuItem extends Business_Abstract {

    private $_tablename = 'ws_menuitem';

    const KEY_LIST = 'menuitem.list.%s.%s.%s';     //key of list by menuname and language
    const KEY_DETAIL = 'menuitem.detail.%s';
    const KEY_DETAIL_LIST_SUB_ITEMS = 'menuitem.list.sub.items.%s';
    const KEY_DETAIL_DELTA = 'menuitem.detail.delta.%s';
    const EXPIRED = 60; //seconds

    private static $id_filter = 0;
    private static $depth_filter = 1;
    private static $_instance = null;

    function __construct() {
        
    }

    /**
     * get instance of Business_WsNew_MenuItem
     *
     * @return Business_WsNew_MenuItem
     */
    public static function getInstance() {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    private function getKeyList($menuname, $remove,$in) {
        return sprintf(self::KEY_LIST, $menuname, $remove, $in);
    }
    private function getKeyLustSubItems($itemid) {
        return sprintf(self::KEY_DETAIL_LIST_SUB_ITEMS, $itemid);
    }
    private function getKeyDetail($itemid) {
        return sprintf(self::KEY_DETAIL, $itemid);
    }
    private function getKeyDetailByDelta($delta) {
        return sprintf(self::KEY_DETAIL_DELTA, $delta);
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

    public function getDetailByMenuname($menuname = '') {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " WHERE menuname=?";
        $data = array($menuname);
        $result = $db->fetchAll($query, $data);
        return $result[0];
    }

    public function getDetailByTitle($title = '') {
        $cache = $this->getCacheInstance();

        $key = md5($title);
        $result = $cache->getCache($key);
        if ($result === FALSE) {
            $db = $this->getDbConnection();
            $query = "SELECT * FROM " . $this->_tablename . "   WHERE title=?";
            $data = array($title);
            $result = $db->fetchAll($query, $data);
            if (!is_null($result) && is_array($result)) {
                $cache->setCache($key, $result[0],self::EXPIRED);
                $result = $result[0];
            }
        }

        return $result;
    }

   public function getListSubDanhMuc($arrayIn) {
        $cache = $this->getCacheInstance();
        $key = 'list-sub-danh-muc-tin-tuc'.md5($arrayIn);
        $result = $cache->getCache($key);
        if ($result === FALSE) {
            $db = $this->getDbConnection();     
            $query = "SELECT itemid FROM " . $this->_tablename . " WHERE hide=0  AND lang=1 and  pid in ($arrayIn)  ORDER BY depth asc, myorder asc, itemid";
            $result = $db->fetchAll($query);
            if (!is_null($result) && is_array($result))
                $cache->setCache($key, $result,self::EXPIRED);
        }
        return $result;
    }
   
    public function getListCateidByName($menuname,$arrayRemove='',$arrayIn='') {
        $cache = $this->getCacheInstance();
        $key = $this->getKeyList($menuname,md5($arrayRemove),md5($arrayIn));
        $result = $cache->getCache($key);
        if ($result === FALSE) {
            $db = $this->getDbConnection();     
            if($arrayRemove!='')          
                $andRemove= " and itemid NOT IN  ($arrayRemove) ";
            if($arrayIn!='')          
                $andIn= " and itemid  IN  ($arrayIn) ";
            $query = "SELECT itemid,delta,title FROM " . $this->_tablename . " WHERE hide=0 and menuname=? AND lang=1  $andRemove $andIn  ORDER BY depth asc, myorder asc, itemid";
     
            $data = array($menuname);
            $result = $db->fetchAll($query, $data);
            if (!is_null($result) && is_array($result))
                $cache->setCache($key, $result,self::EXPIRED);
        }
        return $result;
    }
    
    
    public function getListSubItems($itemid) {
        if (intval($itemid) == 0)
            return null;
        $cache = $this->getCacheInstance();
        $key = $this->getKeyLustSubItems($itemid);
        $result = $cache->getCache($key);    
        if ($result === FALSE) {
            $db = $this->getDbConnection();
            $query = "SELECT * FROM " . $this->_tablename . " WHERE pid=?";
            $data = array();
            $data[] = $itemid;    
            $result = $db->fetchAll($query, $data);
            $cache->setCache($key, $result,self::EXPIRED);
        }
        if ($result == null)
            return null;
        return $result;
    }
    
    public function getDetail($itemid) {
        if (intval($itemid) == 0)
            return null;
        $cache = $this->getCacheInstance();
        $key = $this->getKeyDetail($itemid);
        $result = $cache->getCache($key);
       // $result=FALSE;
        if ($result === FALSE) {
            $db = $this->getDbConnection();
            $query = "SELECT * FROM " . $this->_tablename . " WHERE itemid=?";
            $data = array();
            $data[] = $itemid;
    
            $result = $db->fetchAll($query, $data);
            if (!is_null($result) && is_array($result) && count($result) == 1) {
                $result = $result[0];
            }
            else
                $result = false;
            $cache->setCache($key, $result,self::EXPIRED);
        }
        if ($result == null)
            return null;
        return $result;
    }
   
    
    public function getSubMenu($itemid) {
        if (intval($itemid) == 0)
            return null;
        $cache = $this->getCacheInstance();
        $key = $this->getKeyDetail('sub-'.$itemid);
        $result = $cache->getCache($key);
       // $result=FALSE;
        if ($result === FALSE) {
            $db = $this->getDbConnection();
           // $query = "SELECT * FROM " . $this->_tablename . " WHERE pid=? and hide!=1"; 
            $query = "SELECT p.*,(Select (price*1000+original_price*1000) as max FROM ws_productsitem where cateid=p.itemid and enabled=1  order by max desc limit 1) as price FROM ws_menuitem as p WHERE p.pid=? and p.hide!=1 order by  price desc";
            $data = array();
            $data[] = $itemid;
    
            $result = $db->fetchAll($query, $data);
            $cache->setCache($key, $result,self::EXPIRED);
        }
        if ($result == null)
            return null;
        return $result;
    }
    
    
    public function getDetailByDelta($delta) {
        if (intval($delta) == 0)
            return null;
        $cache = $this->getCacheInstance();
        $key = $this->getKeyDetailByDelta($delta);
        $result = $cache->getCache($key);
    
        if ($result === FALSE) {
            $db = $this->getDbConnection();
            $query = "SELECT * FROM " . $this->_tablename . " WHERE delta=? and module='products'";
            $data = array();
            $data[] = $delta;    
            $result = $db->fetchRow($query, $data);
            $cache->setCache($key, $result,self::EXPIRED);
        }
        if ($result == null)
            return null;
        return $result;
    }
    

}

?>
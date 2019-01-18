<?php

class Business_Addon_CommentItems extends Business_Abstract {

    private $_tablename = 'addon_comment_items';
    private static $_instance = null;

    function __construct() {
        
    }

    //public static function
    /**
     * get instance of Business_Addon_CommentItems
     *
     * @return Business_Addon_CommentItems
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

    private function getCacheInstance() {
        $cache = GlobalCache::getCacheInstance('ws');
        return $cache;
    }
    
    public function getListPagingByItemid($commentid=2, $itemid = '', $offset='', $records='', $published = '', $parentid = ''){
        
        $db = $this->getDbConnection();
        if ($itemid !== '') {
            $_itemid = " AND itemid = ". (int) $itemid;
        }
        if ($parentid !== '') {
            $_parentid = " AND parentid = ". (int) $parentid;
        }
        if ($published === '')
            $query = "SELECT * FROM " . $this->_tablename . " WHERE commentid=$commentid $_itemid $_parentid ORDER BY datetime desc";
        else
            $query = "SELECT * FROM " . $this->_tablename . " WHERE commentid=$commentid $_itemid $_parentid AND display = $published ORDER BY datetime desc";
            
        if ($offset !== '' && $records != '') {
            $query .= " LIMIT $offset, $records";
        }
        
        $data = array($itemid);
        $result = $db->fetchAll($query,$data);
        return $result;
    }
    
    public function getTotalByItemid($commentid=2, $itemid = '', $offset='', $records='', $published = '', $parentid = 0){
        
        $cache = $this->getCacheInstance();
        $__key = md5($commentid.$itemid.$offset.$records.$published.$parentid);
        
        $result = $cache->getCache($__key);
        if ($result===false){        
            $db = $this->getDbConnection();
            if ($itemid !== '') {
                $_itemid = " AND itemid = ". (int) $itemid;
            }
            $_parentid = " AND parentid = ". (int) $parentid;

            if ($published === '')
                $query = "SELECT count(*) as total FROM " . $this->_tablename . " WHERE commentid=$commentid $_itemid $_parentid ORDER BY datetime desc";
            else
                $query = "SELECT count(*) as total FROM " . $this->_tablename . " WHERE commentid=$commentid $_itemid $_parentid AND display = $published ORDER BY datetime desc";

            if ($offset !== '' && $records != '') {
                $query .= " LIMIT $offset, $records";
            }

            $data = array($itemid);
            $result = $db->fetchAll($query,$data);
            $cache->setCache($__key, $result[0]["total"], 60); 
            return $result[0]["total"];
        }
        return $result;
    }
        
    public function getTotalListByComment($commentid=2, $published = ''){
        
        $db = $this->getDbConnection();
        if ($published === '')
            $query = "SELECT count(*) as total FROM " . $this->_tablename . " WHERE commentid=$commentid ORDER BY datetime desc";
        else
            $query = "SELECT count(*) as total FROM " . $this->_tablename . " WHERE commentid=$commentid AND published = $published ORDER BY datetime desc";
        $data = array($itemid);
        $result = $db->fetchAll($query,$data);
        return $result[0]["total"];
    }
    
    public function getDetail($id){
        
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " WHERE id=? ";
        $data = array($id);
        $result = $db->fetchAll($query,$data);
        return $result[0];
    }
        
    public function getListByCate($cateid) {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " WHERE cateid = '" . parent::adaptSQL($cateid) ."'";
        $result = $db->fetchAll($query);
        return $result;
    }
    
    public function getLastestComments() {
        $cache = $this->getCacheInstance();
        $key = "comment.top";
        $result = $cache->getCache($key);
        if($result===false) {
            $db = $this->getDbConnection();
            $query = "SELECT * FROM " . $this->_tablename . " ORDER BY datetime DESC LIMIT 0, 10";
            $result = $db->fetchAll($query);
            $cache->setCache($key, $result, 5*60);
        }
        return $result;
    }
    
    public function getLastestCommentsByCate($cateid, $limit=2) {
        $cache = $this->getCacheInstance();
        $key = "comment.cate.".$cateid;
        $result = $cache->getCache($key);
        if($result===false) {
            $db = $this->getDbConnection();
            $query = "SELECT * FROM " . $this->_tablename . " WHERE cateid = $cateid ORDER BY datetime DESC LIMIT 0, " . $limit;
            $result = $db->fetchAll($query);
            $cache->setCache($key, $result, 5*60);
        }
        return $result;
    }
    
    public function insert($data) {
        $cache = $this->getCacheInstance();
        $db = $this->getDbConnection();
        $db->insert($this->_tablename, $data);
        $cache->deleteCache("news.top");
        $cache->deleteCache("comment.cate.".$data["cateid"]);
        return $db->lastInsertId();
    }   
    
    public function update($data) {
        $id = $data["id"];
        $where = array();
        $where[] = "id='" . parent::adaptSQL($id) . "'";
        $db = $this->getDbConnection();
        $result = $db->update($this->_tablename, $data, $where);
        return $result;
    }
}
?>
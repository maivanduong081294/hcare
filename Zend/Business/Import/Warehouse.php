<?php

class Business_Import_Warehouse extends Business_Abstract {

    private $_tablename = 'import_ie_inventory';
    private static $_instance = null;
    const KEY_LIST = 'wh.la.%s.%s.%s';
    const KEY_LIST_PIDS = 'wh.la.%s.%s.%s';
    
    private function getKeyList($wid, $pid, $cid) {
        return sprintf(self::KEY_LIST, $wid, $pid, $cid);
    }
    
    function __construct() {
        
    } 

    /**
     * get instance of Business_Import_Warehouse
     *
     * @return Business_Import_Warehouse
     */
    public static function getInstance() {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * get Zend_Db connection
     *
     * @return Zend_Db_Adapter_Abstract
     */
    function getDbConnection() {
        $db = Globals::getDbConnection('hnam_app', false);
        return $db;
    }
    
    /**
    * Enter description here...
    *
    * @return Maro_Cache
    */
    private function getCacheInstance()
    {
            $cache = GlobalCache::getCacheInstance('warehouse');
            return $cache;
    }
    
    public function getListByPID($pid) {
        if ($pid == 0) return array();
        $cache = $this->getCacheInstance();
        
        $key = $this->getKeyList($wid=0, $pid, $cid=0);
        $result = $cache->getCache($key);
        $result=false;
            
        if ($result === false) {            
            $db = $this->getDbConnection();            
            $query = "SELECT * FROM " . $this->_tablename . " where pid = $pid";
            
            $data = array();
            $result = $db->fetchAll($query, $data);            
            $cache->setCache($key, $result);            
        }
        
        return $result;
    }
    public function getListById($id, $field = '')
    {
            $id = intval($id);
            $db = $this->getDbConnection();
            $query = "SELECT ".$field." FROM " . $this->_tablename ." WHERE id_item = ?";
            $data = array($id);
            $result = $db->fetchAll($query,$data);
            if($result != null && is_array($result))
            {
                return $result;
            }
            else return null;
    }
    public function getListByImei($imei, $field = '*')
    {
            $id = intval($id);
            $db = $this->getDbConnection();
            $query = "SELECT ".$field." FROM " . $this->_tablename ." WHERE imei = ?";
            $data = array($imei);
            $result = $db->fetchAll($query,$data);
            if($result != null && is_array($result))
            {
                return $result;
            }
            else return null;
    }
    public function getListByImeiQ($imei, $field = '*')
    {
            $db = $this->getDbConnection();
            $query = "SELECT ".$field." FROM " . $this->_tablename ." WHERE imei IN ('$imei')";
            $result = $db->fetchAll($query);
            if($result != null && is_array($result))
            {
                return $result;
            }
            else return null;
    }
    
    public function getListByIdDicBran($id, $field = '')
    {
            $id = intval($id);
            $db = $this->getDbConnection();
            $query = "SELECT DISTINCT id_branch FROM " . $this->_tablename ." WHERE id_item = ?";
            $data = array($id);
            $result = $db->fetchAll($query,$data);
            if($result != null && is_array($result))
            {
                return $result;
            }
            else return null;
    }
    public function getListByIdDicColor($id, $field = '')
    {
            $id = intval($id);
            $db = $this->getDbConnection();
            $query = "SELECT DISTINCT id_color FROM " . $this->_tablename ." WHERE id_item = ?";
            $data = array($id);
            $result = $db->fetchAll($query,$data);
            if($result != null && is_array($result))
            {
                return $result;
            }
            else return null;
    }
    public function getListByIdBranch($id_inventory, $id_item, $field = '')
    {
            $id = intval($id);
            $db = $this->getDbConnection();
            $query = "SELECT ".$field." FROM " . $this->_tablename ." WHERE id_item = ? and id_inventory = ?";
            $data = array($id_item, $id_inventory);
            $result = $db->fetchAll($query,$data);
            if($result != null && is_array($result))
            {
                return $result;
            }
            else return null;
    } 
    public function getListByIdProduct($id_product, $field = '')
    {
            $db = $this->getDbConnection();
            $query = "SELECT ".$field." FROM " . $this->_tablename ." WHERE id_product = ?";
            $data = array($id_product);
            $result = $db->fetchAll($query,$data);
            if($result != null && is_array($result))
            {
                return $result;
            }
            else return null;
    } 
    public function getListWhere($listWhere = NULL, $field = '*')
    {

            $db = $this->getDbConnection();
            $query = "SELECT ".$field." FROM " . $this->_tablename ." WHERE id_product = ? and id_cate = ? and id_item = ? and id_color = ? ";
            $data = $listWhere;
            $result = $db->fetchAll($query,$data);
            if($result != null && is_array($result))
            {
                return $result;
            }
            else return null;
    } 
    public function getListWheres($listWhere = NULL, $field = '*')
    {

            $db = $this->getDbConnection();
            $query = "SELECT ".$field." FROM " . $this->_tablename ." WHERE id_product = ? and id_cate = ? and id_item = ? and id_color = ? and id_inventory = ? ";
            $data = $listWhere;
            $result = $db->fetchAll($query,$data);
            if($result != null && is_array($result))
            {
                return $result;
            }
            else return null;
    } 
    public function getListByIdCate($id_product,$id_cate, $field = '')
    {
            $db = $this->getDbConnection();
            $query = "SELECT ".$field." FROM " . $this->_tablename ." WHERE id_product = ? and id_cate = ?";
            $data = array($id_product,$id_cate);
            $result = $db->fetchAll($query,$data);
            if($result != null && is_array($result))
            {
                return $result;
            }
            else return null;
    } 
    public function getListByIdItem($id_product,$id_cate,$id_item, $field = '')
    {
            $db = $this->getDbConnection();
            $query = "SELECT ".$field." FROM " . $this->_tablename ." WHERE id_product = ? and id_cate = ? and id_item = ?";
            $data = array($id_product,$id_cate,$id_item);
            $result = $db->fetchAll($query,$data);
            if($result != null && is_array($result))
            {
                return $result;
            }
            else return null;
    } 
    public function getListByIdColor($id_product,$id_cate,$id_item,$id_color, $field = '')
    {
            $db = $this->getDbConnection();
            $query = "SELECT ".$field." FROM " . $this->_tablename ." WHERE id_product = ? and id_cate = ? and id_item = ? and id_color = ?";
            $data = array($id_product,$id_cate,$id_item,$id_color);
            $result = $db->fetchAll($query,$data);
            if($result != null && is_array($result))
            {
                return $result;
            }
            else return null;
    } 
    public function getCount($id_color, $id_inventory, $field = '')
    {
            $id = intval($id);
            $db = $this->getDbConnection();
            $query = "SELECT ".$field." FROM " . $this->_tablename ." WHERE id_color = ? and id_inventory = ?";
            $data = array($id_color, $id_inventory);
            $result = $db->fetchAll($query,$data);
            if($result != null && is_array($result))
            {
                return $result;
            }
            else return null;
    } 
    public function getSummaryByPID($pids=array()) {
        if (count($pids) == 0) return array();
        $_pids = implode(",", $pids);
        $kpid = md5($_pids);
        $cache = $this->getCacheInstance();
        
        $key = $this->getKeyList($wid=0, $kpid, $cid=0);
        $result = $cache->getCache($key);
        $result=false;
            
        if ($result === false) {            
            $db = $this->getDbConnection();            
            $query = "SELECT *, sum(quanlity) as total FROM " . $this->_tablename . " where pid IN ($_pids) GROUP BY storeid, pid";
            
            $data = array();
            $result = $db->fetchAll($query, $data);            
            $cache->setCache($key, $result);            
        }
        
        return $result;
    }
    
    public function getSummary($wid, $pids=array(), $cid) {
        
        if ($wid==0 || count($pids) == 0 || $cid==0) return array();
        
        $kpid = md5($pids);
        $_pids = impolode(",", $pids);
        
        $cache = $this->getCacheInstance();
        $key = $this->getKeyList($wid, $kpid, $cid);
        $result = $cache->getCache($key);
        
        if ($result === false) {
            
            $db = $this->getDbConnection();
            
            $query = "SELECT * FROM " . $this->_tablename . " where wid=? AND pid IN ($_pids) AND colorid=?";
            $data = array($wid, $cid);
            $result = $db->fetchAll($query, $data);
            
            $cache->setCache($key, $result);
            
        }
        
        return $result;
    }
    
    public function getSummaryByPIDCID($pids=array(), $cid) {
        
        if (count($pids) == 0 || $cid==0) return array();
        
        $kpid = md5($pids);
        $_pids = impolode(",", $pids);
        
        $cache = $this->getCacheInstance();
        $key = $this->getKeyList($wid, $kpid, $cid);
        $result = $cache->getCache($key);
        
        if ($result === false) {
            
            $db = $this->getDbConnection();
            
            $query = "SELECT * FROM " . $this->_tablename . " where pid IN ($_pids) AND colorid=?";
            $data = array($cid);
            $result = $db->fetchAll($query, $data);
            
            $cache->setCache($key, $result);
            
        }
        
        return $result;
    }

    public function insert($data) {
        $db = $this->getDbConnection();
        $result = $db->insert($this->_tablename, $data);
        if ($result) {
            return (int) $db->lastInsertId();
        }
        return 0;
    }
    
    public function executeQuery($sqls) {
        $db = $this->getDbConnection();
        $result = $db->query("".$sqls."");
        if(isset($result) && $result!=NULL){
            return 1;
        }else{
            return 0;
        }
        //$sql = implode("", $sqls);
        // try {
        //     $result = $db->query("".$sql."");
        // } catch (Exception $e) {
        //     return 0;
        // }
        // if ($result)
        //     return 1;
        //return 0;
    }
    

    public function update($itemid, $data) {
        $db = $this->getDbConnection();
        $where = array();
        $where[] = "id = '" . parent::adaptSQL($itemid) . "'";
        try {
            $result = $db->update($this->_tablename, $data, $where);
        } catch (Exception $e) {
            return 0;
        }
    }

}

?>
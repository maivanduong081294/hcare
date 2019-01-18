<?php

class Business_Addon_ProductsItem extends Business_Abstract
{
    private $_tablename = 'quynhn_productsitem'; 
    private static $_instance = null; 
    private  $_filed_search = '*'; 
    function __construct()
    {

    }

    /**
     * get instance of Business_Addon_ProductsItem
     *
     * @return Business_Addon_ProductsItem
     */
    public static function getInstance()
    {
            if(self::$_instance == null)
            {
                    self::$_instance = new Business_Addon_ProductsItem();
            }
            return self::$_instance;
    }

    /**
     * get Zend_Db connection
     *
     * @return Zend_Db_Adapter_Abstract
     */
    function get_base_url(){
        return alias_domain;
    }
    function getDbConnection()
    {		
            $db    	= Globals::getDbConnection('hnamvt', false);
            return $db;	
    }
    public function getCacheInstance() {
        $cache = GlobalCache::getCacheInstance('products');
        return $cache;
    }
    private function get_key_list_productsid($str)
    {
        $KEY_LIST = $this->get_base_url().'list-productsid.'.$this->_tablename.'.%s';
        return sprintf($KEY_LIST,$str);
    }
    private function get_key_list($str)
    {
        $KEY_LIST = $this->get_base_url().'list.'.$this->_tablename.'.%s';
        return sprintf($KEY_LIST,$str);
    }
    private function get_key_detail($id)
    {
        $KEY_DETAIL = $this->get_base_url().'detail.'.$this->_tablename.'.%s';
        return sprintf($KEY_DETAIL,$id);
    }
    
    public function delete_all_cache($currentUrl){
        $cache = $this->getCacheInstance();
        $cache->flushAll();
//        Business_Addon_Options::getInstance()->flush_cache(5,$currentUrl);
    }
    
    public function delete_key_detail($id){
        $cache = $this->getCacheInstance();
        $key = $this->get_key_detail($id);
        $cache->deleteCache($key);
        $url_detail = Business_Helpers_Products::get_link_by_itemid($id);
        Business_Addon_Options::getInstance()->flush_cache(5,$url_detail);
    }
    
    public function delete_key_list_productsid($productsid){
        $cache = $this->getCacheInstance();
        $key = $this->get_key_list_productsid($productsid);
        $cache->deleteCache($key);
    }
    
    public function getDetail($id){
            $id = (int)$id;
            $cache = $this->getCacheInstance();
            $key = $this->get_key_detail($id);
            $result=$cache->getCache($key);
            if($result === FALSE)
            {
                $db= $this->getDbConnection();
                $query = "select * from $this->_tablename where itemid = $id";
                $result = $db->fetchAll($query);
                $result = $result[0];
                if(!is_null($result) && is_array($result)){
                    $cache->setCache($key, $result);
                }
            }
            return $result;
        }
    
    //start admin
    public function insert($data)
    {
        $db = $this->getDbConnection();
        $result = $db->insert($this->_tablename,$data);
        if ($result > 0) {
            $lastid= $db->lastInsertId($this->_tablename); // tra ve id khi them vao
        }
        $this->delete_all_cache();
        return $lastid;
    }
    public function insert_history($data)
    {
        $db = $this->getDbConnection();
        $result = $db->insert($this->_tablename_history,$data);
        if ($result > 0) {
            $lastid= $db->lastInsertId($this->_tablename_history); // tra ve id khi them vao
        }
        return $lastid;
    }
    public function excute($query){
        $db     =  $this->getDbConnection();
        $result = $db->query($query);
        $this->delete_all_cache();
        return $result;
    }
    public function update($id,$data) {
        $db= $this->getDbConnection();
        $query = "itemid = ".$id;
        $result = $db->update($this->_tablename, $data,$query);
        $this->delete_key_detail($id);
        $this->delete_all_cache();
        return $result;
    }
    
    
    public function get_list_by_id($strid){
        $cache = $this->getCacheInstance();
        $key = $this->get_key_list($strid);
        $result = $cache->getCache($key);
        if ($result === FALSE) {
            $db = $this->getDbConnection();
            $query = "SELECT $this->_filed_search FROM " . $this->_tablename . " WHERE enabled=1 and itemid IN ($strid)";
            $result = $db->fetchAll($query);
            if (!is_null($result) && is_array($result)) {
                $cache->setCache($key, $result);
            }
        }
        return $result;
    }
        public function get_list($cateid ="",$q="",$offset=0, $records=0) {
            $cache = $this->getCacheInstance();
            $key = $this->get_base_url()."get_list".$this->_tablename.$cateid.$offset.$records;
            $result = $cache->getCache($key);
            if ($result === FALSE) {
                $db = $this->getDbConnection();
                $query = "SELECT * FROM " . $this->_tablename . " WHERE enabled=1";
                if((int)$cateid >0){
                    $query .=" and cateid IN ($cateid)";
                }
                if($q != NULL){
                    $query .=" and (title like '%$q%' or itemid = '$$q') ";
                }
                if($records>0){
                    $query.= " LIMIT $offset,$records";
                }
                $result = $db->fetchAll($query);
                if (!is_null($result) && is_array($result)) {
                    $cache->setCache($key, $result,300);
                }
            }
            return $result;
        }
    
    public function count_list($productsid ="",$userid=0) {
            $cache = $this->getCacheInstance();
            $key = $this->get_base_url()."count_list".$this->_tablename.$productsid;
            $result = $cache->getCache($key);
            if ($result === FALSE) {
                $db = $this->getDbConnection();
                $query = "SELECT count(*) as total FROM " . $this->_tablename . " WHERE enabled=1";
                
                if((int)$productsid >0){
                    $query .=" and productsid IN ($productsid)";
                }
                if($q != NULL){
                    $query .=" and (title like '%$q%' or itemid = '$q' ) ";
                }
                if((int)$onstock >0){
                    $query .=" and onstock = $onstock";
                }
                if((int)$userid >0){
                    $query .=" and userid = $userid";
                }
                $query .= "  ORDER BY myorder asc";
                
                $result = $db->fetchAll($query);
                $result = $result[0];
                if (!is_null($result) && is_array($result)) {
                    $cache->setCache($key, $result,300);
                }
            }
            return $result;
        }
        
        

}

?>
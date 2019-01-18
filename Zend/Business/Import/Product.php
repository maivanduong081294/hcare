<?php

class Business_Import_Product extends Business_Abstract
{
	private $_tablename = 'ws_productsitem';
	private static $_instance = null; 
	const EXPIRED = 3000; //secs
	const KEY_DETAIL = 'number.detail.%s'; //key of detail.id
	function __construct()
	{
		
	} 
	
	/**
	 * get instance of Business_Import_Product
	 *
	 * @return Business_Import_Product
	 */
	public static function getInstance()
	{
		if(self::$_instance == null)
		{
			self::$_instance = new Business_Import_Product();
		}
		return self::$_instance;
	}
	
	/**
	 * get Zend_Db connection
	 *
	 * @return Zend_Db_Adapter_Abstract
	 */
	function getDbConnection()
	{		
		$db = Globals::getDbConnection('hnam_wh', false);
		return $db;	
	}
	private function getCacheInstance() {
        $cache = GlobalCache::getCacheInstance('ws');
        return $cache;
    }
		
	public function getList()
	{
        $db = $this->getDbConnection();
        $query = "select * from " . $this->_tablename . " where 1 order by id desc";
        $result = $db->fetchAll($query);
        return $result;
	}
	private function getKeyDetail($id) {
        return sprintf(self::KEY_DETAIL, $id);
    }
    public function getListByItemId($strItem){
        $db = $this->getDbConnection();
        $query = " SELECT itemid,title FROM " . $this->_tablename ." WHERE itemid IN ($strItem)";
        $result = $db->fetchAll($query);
        if($result != null && is_array($result))
        {
                return $result;
        }
        else return null;
    }

    public function getDetail($id)
	{
            $id = intval($id);
            $db = $this->getDbConnection();
            $query = " SELECT * FROM " . $this->_tablename ." WHERE itemid = ?";
            $data = array($id);
            $result = $db->fetchAll($query,$data);
            if($result != null && is_array($result))
            {
                    return $result[0];
            }
            else return null;
	}
	public function getDetailByItemid($id, $field = "*")
	{
            $id = intval($id);
            $db = $this->getDbConnection();
            $query = " SELECT ".$field." FROM " . $this->_tablename ." WHERE itemid = ?";
            $data = array($id);
            $result = $db->fetchAll($query,$data);
            if($result != null && is_array($result))
            {
                    return $result[0];
            }
            else return null;
	}
	public function getListByTypeid($cateid)
	{
            $cateid = intval($cateid);
            $db = $this->getDbConnection();
            $query = " SELECT * FROM " . $this->_tablename ." WHERE productsid = ?";
            $data = array($cateid);
            $result = $db->fetchAll($query,$data);
            if($result != null && is_array($result))
            {
                    return $result;
            }
            else return null;
	}
	public function getListByCateid($cateid)
	{
            $cateid = intval($cateid);
            $db = $this->getDbConnection();
            $query = " SELECT * FROM " . $this->_tablename ." WHERE cateid = ?";
            $data = array($cateid);
            $result = $db->fetchAll($query,$data);
            if($result != null && is_array($result))
            {
                    return $result;
            }
            else return null;
	}
	public function getListByKey($key, $field = '')
	{
            $key = $key;
            $db = $this->getDbConnection();
            $query = " SELECT ".$field." FROM " . $this->_tablename ." WHERE title like '%".$key."%'";
            $result = $db->fetchAll($query);
            if($result != null && is_array($result))
            {
                    return $result;
            }
            else return null;
	}
}

?>
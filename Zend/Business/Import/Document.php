<?php

class Business_Import_Document extends Business_Abstract
{
	private $_tablename = 'import_document';
	private static $_instance = null; 
	const EXPIRED = 3000; //secs
	const KEY_DETAIL = 'number.detail.%s'; //key of detail.id
	function __construct()
	{
		
	} 
	
	/**
	 * get instance of Business_Addon_Number
	 *
	 * @return Business_Addon_Number
	 */
	public static function getInstance()
	{
		if(self::$_instance == null)
		{
			self::$_instance = new Business_Import_Document();
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
		$db = Globals::getDbConnection('hnam_app', false);
		return $db;	
	}
	private function getCacheInstance() {
        $cache = GlobalCache::getCacheInstance('ws');
        return $cache;
    }
		
	public function getListCount()
	{
        $db = $this->getDbConnection();
        $query = "select * from " . $this->_tablename . " where 1 order by id desc";
        $result = $db->fetchAll($query);
        return $result;
	}
	public function getList($offset = 0, $records = 10, $field = '*')
	{
        $db = $this->getDbConnection();
        $query = "select ".$field." from " . $this->_tablename . " where 1 ";
        $query .= " order by id desc";
        if(isset($offset) && isset($records)){
        	$query .= " limit $offset, $records";
        }
        $result = $db->fetchAll($query);
        return $result;
	}
	private function getKeyDetail($id) {
        return sprintf(self::KEY_DETAIL, $id);
    }
	public function getDetail($id, $field = "*")
	{
            $id = intval($id);
            $db = $this->getDbConnection();
            $query = " SELECT ".$field." FROM " . $this->_tablename ." WHERE id = ?";
            $data = array($id);
            $result = $db->fetchAll($query,$data);
            if($result != null && is_array($result))
            {
                    return $result[0];
            }
            else return null;
	}
    public function getByIdType($id,$id_inventory_detail, $field = "*")
    {
            $id = intval($id);
            $id_inventory_detail = intval($id_inventory_detail);
            $db = $this->getDbConnection();
            $query = " SELECT ".$field." FROM " . $this->_tablename ." WHERE id_type = ? and id_inventory_detail = ?";
            $data = array($id,$id_inventory_detail);
            $result = $db->fetchAll($query,$data);
            if($result != null && is_array($result))
            {
                return $result;
            }
            else return null;
    }
	public function getWheress($field_where,$value_where,$field_where1,$value_where1, $field = "*")
	{
            $db = $this->getDbConnection();
            $query = " SELECT ".$field." FROM " . $this->_tablename ." WHERE ".$field_where." = ?  and ".$field_where1." = ?";
            $data = array($value_where,$value_where1);
            $result = $db->fetchAll($query,$data);
            if($result != null && is_array($result))
            {
                return $result;
            }
            else return null;
	}
	public function getWhere($field_where,$value_where, $field = "*")
	{
            $db = $this->getDbConnection();
            $query = " SELECT ".$field." FROM " . $this->_tablename ." WHERE ".$field_where." = ?";
            $data = array($value_where);
            $result = $db->fetchAll($query,$data);
            if($result != null && is_array($result))
            {
                return $result;
            }
            else return null;
	}
	public function getWheres($value_where, $field = "*")
	{
            $db = $this->getDbConnection();
            $query = " SELECT ".$field." FROM " . $this->_tablename ." WHERE ".$value_where."";
            $result = $db->fetchAll($query,$data);
            if($result != null && is_array($result))
            {
                return $result;
            }
            else return null;
	}
	public function getWhereArray($arrayWhere = null, $field = "*")
	{
            $db = $this->getDbConnection();
            if($arrayWhere['id_inventorydetail'] == ''){
            	$query = " SELECT ".$field." FROM " . $this->_tablename ." WHERE id_type = ? ";
            	$data = array($arrayWhere['id_type']);
            }else if($arrayWhere['id_type'] == ''){
            	$query = " SELECT ".$field." FROM " . $this->_tablename ." WHERE id_inventory_detail = ? ";
            	$data = array($arrayWhere['id_inventorydetail']);
            }else{
            	$query = " SELECT ".$field." FROM " . $this->_tablename ." WHERE id_inventory_detail = ? and id_type = ? ";
            	$data = array($arrayWhere['id_inventorydetail'],$arrayWhere['id_type']);
            }
            $query .= " order by id desc";
            if(isset($offset) && isset($records)){
	        	$query .= " limit $offset, $records";
	        }
            
            $result = $db->fetchAll($query,$data);

            if($result != null && is_array($result))
            {
                return $result;
            }
            else return null;
	}
	public function update($id, $data)
	{
		
		$db = $this->getDbConnection();
		$where = array();	
		$where[] = "id = '" . parent::adaptSQL($id) . "'";
		try
		{			
			$result = $db->update($this->_tablename, $data, $where);
			
			return $result; 
		}
		catch(Exception $e)
		{
			return 0;
		}
	}
	
	public function insert($data)
	{
		$db = $this->getDbConnection();
		$result = $db->insert($this->_tablename,$data);
        return $result;
	}
	
	public function delete($id)
	{	
		$db = $this->getDbConnection();
		$where = array();
		$where[] = "id='" . parent::adaptSQL($id) . "'";
		$result = $db->delete($this->_tablename,$where);
                return $result;
	}
	public function getListOlder($id_num = 0, $limit=8)
	{
        $cache = $this->getCacheInstance();
        $key = $this->getKeyDetail('number.list.all'.$this->_tablename.$id_num.$limit);
        $cache->deleteCache($key);
        $result = $cache->getCache($key);
        if($result === FALSE)
        {
        	echo 'nnn';
            if ($id_num == 0)
                $query = "SELECT * FROM " . $this->_tablename . " WHERE id=? ORDER BY id desc";
            else
                $query = "SELECT * FROM " . $this->_tablename . " WHERE id=? ORDER BY id desc LIMIT 0,$limit";
            //echo $query;die;
            $db = $this->getDbConnection();
            $data = array($id_num);
            $result = $db->fetchAll($query,$data);
            $result = $result[0];
            $ret = $cache->setCache($key, $result, self::EXPIRED);
            //var_dump($result);die;

        }
        return $result;
	}
}

?>
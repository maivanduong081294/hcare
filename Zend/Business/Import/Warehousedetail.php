<?php

class Business_Import_Warehousedetail extends Business_Abstract {

    private $_tablename = 'import_ie_inventory_detail';
    private static $_instance = null;
    const KEY_LIST = 'wh.la.%s.%s.%s';
    const KEY_LIST_PIDS = 'wh.la.%s.%s.%s';
    
    private function getKeyList($wid, $pid, $cid) {
        return sprintf(self::KEY_LIST, $wid, $pid, $cid);
    }
    
    function __construct() {
        
    } 

    /**
     * get instance of Business_Import_Warehousedetail
     *
     * @return Business_Import_Warehousedetail
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
    // Quỳnh
    public function getListGByCateItemidColorId($id_type,$id_cate,$id_item,$id_color,$id_inventory,$id_xuat)
    {

            $db = $this->getDbConnection();
            $query = "SELECT id_inventory,id_xuat,count(*) as total FROM " . $this->_tablename ." WHERE id_type = $id_type and id_cate = $id_cate and id_item = $id_item and id_color = $id_color ";
            if($id_xuat!= null){
                $query .=" and id_xuat = $id_xuat";
            }
            if((int)$id_inventory >0){
                $query .="and id_inventory = $id_inventory ";
            }
            $query .="group by id_xuat";
            if((int)$id_inventory >0){
                $query .=",id_inventory";
            }
            $result = $db->fetchAll($query);
            if($result != null && is_array($result))
            {
                return $result;
            }
            else return null;
    }
    public function getListGByVendorCateItemidColorId($id_type,$id_cate,$id_item,$id_color,$id_inventory,$id_xuat)
    {

            $db = $this->getDbConnection();
            $query = "SELECT id_xuat,count(*) as total FROM " . $this->_tablename ." WHERE id_type = $id_type and id_cate = $id_cate and id_item = $id_item and id_color = $id_color";
            if($id_xuat!= null){
                $query .=" and id_xuat = $id_xuat";
            }
            $query .="group by id_xuat,id_inventory";
            $result = $db->fetchAll($query);
            if($result != null && is_array($result))
            {
                return $result;
            }
            else return null;
    }
    
    
    public function getIMEI($id_cate,$id_item,$id_color,$id_inventory,$id_xuat,$is_import)
    {

            $db = $this->getDbConnection();
            $query = "SELECT id_color,imei,created FROM import_ie_inventory_detail WHERE id_cate = $id_cate  and id_inventory = $id_inventory and id_xuat = $id_xuat and is_import = $is_import ";
            if(intval($id_item) >0){
                $query .=" and id_item = $id_item ";
            }
            if(intval($id_color) >0){
                $query .=" and id_color = $id_color";
            }
            $result = $db->fetchAll($query);
            if($result != null && is_array($result))
            {
                return $result;
            }
            else return null;
    } 
    
    public function getListGByCate($id_type,$id_cate="",$id_inventory,$id_xuat)
    {

            $db = $this->getDbConnection();
            $query = "SELECT id_type,id_cate,id_item,id_color,created,id_inventory,count(*) as number FROM import_ie_inventory_detail WHERE  id_inventory = $id_inventory and id_xuat = $id_xuat";
            if(intval($id_type) > 0){
                $query .=" and id_type = $id_type";
            }
            if($id_cate != null){
                $query .=" and id_cate IN ($id_cate)";
            }
            $query .=" group by id_cate";
            $result = $db->fetchAll($query);
            if($result != null && is_array($result))
            {
                return $result;
            }
            else return null;
    } 
    public function getListWhereQ($id_type,$id_inventory)
    {
            $db = $this->getDbConnection();
            $query = "SELECT id_type ,id_inventory as storeid,id_xuat,count(*) as total FROM " . $this->_tablename ." WHERE id_type IN ($id_type) and id_inventory IN ($id_inventory) group by storeid,id_xuat,id_type";
            $result = $db->fetchAll($query);
            if($result != null && is_array($result))
            {
                return $result;
            }
            else return null;
    }
    public function getListGByProductsID($id_type,$id_cate="",$id_inventory)
    {

            $db = $this->getDbConnection();
            $query = "SELECT id_type ,id_xuat,id_cate,id_item,id_color,created,id_inventory,count(*) as number FROM import_ie_inventory_detail WHERE id_inventory = $id_inventory ";
            
            if(intval($id_type) >0){
                $query .=" and id_type = $id_type";
            }
            if(intval($id_cate) >0){
                $query .=" and id_cate IN ($id_cate)";
            }
            $query .=" group by id_cate,id_item,id_color,id_xuat";
            $result = $db->fetchAll($query);
            if($result != null && is_array($result))
            {
                return $result;
            }
            else return null;
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    // End Quỳnh
    public function getList()
    {
        $db = $this->getDbConnection();
        $query = "select * from " . $this->_tablename . " where 1 order by id desc";
        $result = $db->fetchAll($query);
        return $result;
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
            $query = "SELECT ".$field." FROM " . $this->_tablename ." WHERE imei = ? order by id ASC";
			
            $data = array($imei);
            $result = $db->fetchAll($query,$data);
            if($result != null && is_array($result))
            {
                return $result;
            }
            else return null;
    }
    public function getImeiInfoNewest($imei, $field = '*')
    {
            $id = intval($id);
            $db = $this->getDbConnection();
            $query = "SELECT ".$field." FROM " . $this->_tablename ." WHERE imei = ? order by id DESC";
            
            $data = array($imei);
            $result = $db->fetchAll($query,$data);
            if($result != null && is_array($result))
            {
                return $result[0];
            }
            else return null;
    }
    public function getListByImeiRow($imei, $field = '*')
    {
            $db = $this->getDbConnection();
            $query = "SELECT ".$field." FROM " . $this->_tablename ." WHERE imei = ? ORDER BY id DESC";
            $data = array($imei);
            $result = $db->fetchAll($query,$data);
            if($result != null && is_array($result))
            {
                return $result[0];
            }
            else return null;
    }
    public function getListByImeiRowAsc($imei, $field = '*')
    {
            $db = $this->getDbConnection();
            $query = "SELECT ".$field." FROM " . $this->_tablename ." WHERE imei = ?  order by id desc";
            $data = array($imei);
            $result = $db->fetchAll($query,$data);
            if($result != null && is_array($result))
            {
                return $result[0];
            }
            else return null;
    }
    public function getListByImeiRowDesc($imei, $field = '*')
    {
            $db = $this->getDbConnection();
            $query = "SELECT ".$field." FROM " . $this->_tablename ." WHERE imei = ?  order by id asc";
            $data = array($imei);
            $result = $db->fetchAll($query,$data);
            if($result != null && is_array($result))
            {
                return $result[0];
            }
            else return null;
    }
    public function getListByIdDicBran($id, $field = '')
    {
            $id = intval($id);
            $db = $this->getDbConnection();
            $query = "SELECT DISTINCT id_branch FROM " . $this->_tablename ." WHERE id_item = ? ";
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
            $query = "SELECT ".$field." FROM " . $this->_tablename ." WHERE imei = ? and id_xuat = ? ";
            $data = $listWhere;
            $result = $db->fetchAll($query,$data);
            if($result != null && is_array($result))
            {
                return $result;
            }
            else return null;
    }
    public function getListWhereByBillRow($id_bill, $field = '*')
    {

            $db = $this->getDbConnection();
            $query = "SELECT ".$field." FROM " . $this->_tablename ." WHERE id_bill = ".$id_bill."";
            $result = $db->fetchAll($query);
            if($result != null && is_array($result))
            {
                return $result[0];
            }
            else return null;
    }
    
    public function getListWhere3($field_where1,$value_where1,$field_where2,$value_where2,$field_where3,$value_where3, $field = '*')
    {

            $db = $this->getDbConnection();
            $query = "SELECT ".$field." FROM " . $this->_tablename ." WHERE ".$field_where1." = ".$value_where1." and ".$field_where2." = ".$value_where2." and ".$field_where3." = ".$value_where3."";
            $result = $db->fetchAll($query);
            if($result != null && is_array($result))
            {
                return $result;
            }
            else return null;
    }
    public function getListWhere3Desc($field_where1,$value_where1,$field_where2,$value_where2,$field_where3,$value_where3, $field = '*')
    {

            $db = $this->getDbConnection();
            $query = "SELECT ".$field." FROM " . $this->_tablename ." WHERE ".$field_where1." = ".$value_where1." and ".$field_where2." = ".$value_where2." and ".$field_where3." = ".$value_where3." order by id desc";
            $result = $db->fetchAll($query);
            if($result != null && is_array($result))
            {
                return $result;
            }
            else return null;
    }
    public function getListWhere2Desc($field_where1,$value_where1,$field_where2,$value_where2, $field = '*')
    {

            $db = $this->getDbConnection();
            $query = "SELECT ".$field." FROM " . $this->_tablename ." WHERE ".$field_where1." = ".$value_where1." and ".$field_where2." = ".$value_where2." group by imei order by id desc";
            $result = $db->fetchAll($query);
            if($result != null && is_array($result))
            {
                return $result;
            }
            else return null;
    }
    public function getListWhere4($field_where1,$value_where1,$field_where2,$value_where2,$field_where3,$value_where3,$field_where4,$value_where4, $field = '*')
    {

            $db = $this->getDbConnection();
            $query = "SELECT ".$field." FROM " . $this->_tablename ." WHERE ".$field_where1." = ".$value_where1." and ".$field_where2." = ".$value_where2." and ".$field_where3." = ".$value_where3." and ".$field_where4." = ".$value_where4." ";
            $result = $db->fetchAll($query);
            if($result != null && is_array($result))
            {
                return $result;
            }
            else return null;
    } 
    public function getListWhere5($field_where1,$value_where1,$field_where2,$value_where2,$field_where3,$value_where3,$field_where4,$value_where4,$field_where5,$value_where5, $field = '*')
    {

            $db = $this->getDbConnection();
            $query = "SELECT ".$field." FROM " . $this->_tablename ." WHERE ".$field_where1." = ".$value_where1." and ".$field_where2." = ".$value_where2." and ".$field_where3." = ".$value_where3." and ".$field_where4." = ".$value_where4." and ".$field_where5." = ".$value_where5." ";
            $result = $db->fetchAll($query);
            if($result != null && is_array($result))
            {
                return $result;
            }
            else return null;
    } 
	public function getListWhere6($field_where1,$value_where1,$field_where2,$value_where2,$field_where3,$value_where3,$field_where4,$value_where4,$field_where5,$value_where5,$field_where6,$value_where6, $field = '*')
    {

            $db = $this->getDbConnection();
            $query = "SELECT ".$field." FROM " . $this->_tablename ." WHERE ".$field_where1." = ".$value_where1." and ".$field_where2." = ".$value_where2." and ".$field_where3." = ".$value_where3." and ".$field_where4." = ".$value_where4." and ".$field_where5." = ".$value_where5." and ".$field_where6." = ".$value_where6." ";
            var_dump($query);die;
            $result = $db->fetchAll($query);
            if($result != null && is_array($result))
            {
                return $result;
            }
            else return null;
    } 
    public function getListWhere3Groupby($field_where1,$value_where1,$field_where2,$value_where2,$field_where3,$value_where3, $field = '*',$groupby = '')
    {

            $db = $this->getDbConnection();
            $query = "SELECT ".$field.",count(*) as number FROM " . $this->_tablename ." WHERE ".$field_where1." = ".$value_where1." and ".$field_where2." = ".$value_where2." and ".$field_where3." = ".$value_where3." group by ".$groupby." ";
            $result = $db->fetchAll($query);
            if($result != null && is_array($result))
            {
                return $result;
            }
            else return null;
    } 
    public function getListWhere4Groupby($field_where1,$value_where1,$field_where2,$value_where2,$field_where3,$value_where3,$field_where4,$value_where4, $field = '*',$groupby = '')
    {

            $db = $this->getDbConnection();
            $query = "SELECT ".$field.",count(*) as number FROM " . $this->_tablename ." WHERE ".$field_where1." = ".$value_where1." and ".$field_where2." = ".$value_where2." and ".$field_where3." = ".$value_where3." and ".$field_where4." = ".$value_where4." group by ".$groupby." ";
            
            $result = $db->fetchAll($query);
            if($result != null && is_array($result))
            {
                return $result;
            }
            else return null;
    } 
    
    public function getListWhereArray($listWhere = NULL, $field = '*')
    {

            $db = $this->getDbConnection();
            $query = "SELECT ".$field." FROM " . $this->_tablename ." WHERE id_type = ? and id_cate = ? and id_item = ? and id_color = ? and id_inventory = ? and id_xuat = ? ";
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

}

?>
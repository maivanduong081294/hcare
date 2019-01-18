<?php

class Business_Common_DetailsSales extends Business_Abstract
{
	private $_tablename = 'details_sales';
	
	const KEY_LIST = 'zfw_users.list';
	const KEY_DETAIL = 'zfw_users.uid.%s';
	
	protected static $_current_rights = null;
	
	protected static $_instance = null; 

	function __construct()
	{
		
	}
	
	/**
	 * get instance of Business_Common_DetailsSales
	 *
	 * @return Business_Common_DetailsSales
	 */
	public static function getInstance()
	{
		if(self::$_instance == null)
		{
			self::$_instance = new self();
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
		$db    	= Globals::getDbConnection('maindb', false);
		return $db;	
	}
	
	/**
	 * Enter description here...
	 *
	 * @return Maro_Cache
	 */
	function getCacheInstance()
	{
		$cache = GlobalCache::getCacheInstance('ws');		
		return $cache;
	}
        public function _deleteAllCache(){
            $cache = $this->getCacheInstance('ws');
            $cache->flushAll();
        }

        public function insert($data)
	{
		$db = $this->getDbConnection();
		$result = $db->insert($this->_tablename,$data);
                if ($result > 0) {
                    $lastid= $db->lastInsertId($this->_tablename);
                }
                $this->_deleteAllCache();
//                var_dump($lastid);exit('123');
                return $lastid;
	}
        
        public function getCateid($cated_id,$vote_id,$months_created,$years_created)
	{
		$list = $this->getListByCatedId($cated_id,$vote_id,$months_created,$years_created);
		if($list != null && is_array($list) && count($list) > 0)
		{
			for($i=0;$i<count($list);$i++)
			{
				if($list[$i]['cated_id'] == $cated_id)
				{
					return $list[$i];
				}
			}
		}
		return null;
	}
	public function getListByCatedId($cateid,$vote_id,$months_created,$years_created)
	{

            $db = $this->getDbConnection();
            $query = "SELECT * FROM  $this->_tablename  where cated_id = $cateid and user_id = $vote_id and months_created = $months_created and years_created = $years_created";
//    			var_dump($query);exit();
            $result = $db->fetchAll($query);

            return $result;		
	}
	public function getListById($id)
	{

            $db = $this->getDbConnection();
            $query = "SELECT * FROM  $this->_tablename  where id = $id";
    //			var_dump($query);exit();
            $result = $db->fetchAll($query);

            return $result;		
	}
	public function getList(){
            $db = $this->getDbConnection();
            $query = "SELECT * FROM  $this->_tablename ";
//			var_dump($query);exit();
            $result = $db->fetchAll($query);
            return $result;
        }
	public function getListByVoteIdDate($vote_id,$months_created,$years_created,$productsid){
            $cache = $this->getCacheInstance();
            $key = 'getListByVoteIdDate'.$vote_id.$months_created.$years_created.$productsid;
            $result = $cache->getCache($key);
//            $cache->deleteCache($key);//exit();
            if($result === false){
                $db = $this->getDbConnection();
                $query = "SELECT * FROM  $this->_tablename where 1=1";
                if($vote_id !=null){
                    $query .=" and user_id = $vote_id  ";
                }
                if($months_created != 0){
                    $query .= " and months_created = $months_created ";
                }
                if($years_created != 0){
                    $query .= " and years_created = $years_created ";
                }
                if($productsid != 0){
                    $query.=" and productsid= $productsid ";
                }
                $query.="  order by id desc limit 100";
                $result = $db->fetchAll($query);
                $cache->setCache($key, $result);
            }
            return $result;
        }

        public function sumPriceSalesById($id_users){
            $db = $this->getDbConnection();
            $query = "select sum(sum_prices) from $this->_tablename where user_id = $id_users and is_actived =1";
//            var_dump($query);exit();
            $result = $db->fetchAll($query);
            return $result;
        }
        public function countProductsSalesById($id_users){
            $db = $this->getDbConnection();
            $query = "select sum(sum_numbers) from $this->_tablename where user_id = $id_users and is_actived =1";
//            var_dump($query);exit();
            $result = $db->fetchAll($query);
            return $result;
        }
        public function deleteProportionById($id){
            $db = $this->getDbConnection();
            $result = $db->delete($this->_tablename, "id = $id");
            $this->_deleteAllCache();
            return $result;
        }
        public function deleteAllProportionById($vote_id,$month_create,$year_create,$productsid){
            $db = $this->getDbConnection();
            $where = "1 = 1 ";
            if($vote_id != null){
                $where .= " and user_id = $vote_id ";
            }
            if($month_create !=0){
                $where .= " and months_created = $month_create ";
            }
            if($year_create !=0){
                $where .= " and years_created = $year_create ";
            }
            if($productsid !=0){
                $where .= " and productsid = $productsid ";
            }
            $result = $db->delete($this->_tablename, $where);
            $this->_deleteAllCache();
            return $result;
        }
        

        





        public function deleteSalesById($id){
//            exit('dsada');
            $db=  $this->getDbConnection();
//            var_dump($db);exit();
            $query = "id = $id";
//            var_dump($query);exit();
            $data =array(
                "is_actived" => "0"
                );
//                var_dump($data);exit();
            $result = $db->update($this->_tablename, $data, $query);
//            var_dump($result);exit();
            return $result;
        }
	public function restoreSalesById($id){
            $db=  $this->getDbConnection();
            $query = "id = ".$id;
            $data =array(
                "is_actived" => "1"
                );
            $result = $db->update($this->_tablename, $data, $query);
            return $result;
        }
        
        public function addSales($data){
            $db=  $this->getDbConnection();
            $result = $db->insert($this->_tablename, $data);
            return $result;
        }

        
        public function update($itemid, $data)
	{
            $db = $this->getDbConnection();
            $where = array();
            $where[] = "id = '" . parent::adaptSQL($itemid) . "'";                
            try
            {			
                $result = $db->update($this->_tablename, $data, $where);
            }
            catch(Exception $e)
            {
                    return 0;
            }
            $this->_deleteAllCache();
	}



	public function updateUser($userid, $data)
	{
		$db = $this->getDbConnection();
		$where = array();
		$where[] = "userid='" . parent::adaptSQL($userid) . "'";
		$result = $db->update($this->_tablename, $data, $where);
		if($result)
		{
			$cache = $this->getCacheInstance();
			$key = $this->getKeyList();
			$cache->deleteCache($key);
			
			$key = $this->getKeyDetail($userid);
			$cache->deleteCache($key);
		}
		return $result;
	}
	
	public function getRolesForUser($userid)
	{
		$_roles = Business_Common_Roles::getInstance();
		
		$user_roles = $_roles->getRolesByUser($userid);		
				
		$user_perm = array();
		
		$_permission = Business_Common_Permissions::getInstance();
		
		if($user_roles != null && is_array($user_roles) && count($user_roles) > 0)
		{
			for($i=0;$i<count($user_roles);$i++)
			{
				$pid = $user_roles[$i]['pid'];				
				$perm = $_permission->getPermision($pid);				
				$perm = explode(',',$perm['permission']);												
				$user_perm = array_merge($user_perm,$perm);				
			}
		}		
		return $user_perm;
	}
	
	
		
}
?>
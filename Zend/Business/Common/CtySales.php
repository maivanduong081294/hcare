<?php

class Business_Common_CtySales extends Business_Abstract
{
	private $_tablename = 'cty_sales';
	
	const KEY_LIST = 'zfw_users.list';
	const KEY_DETAIL = 'zfw_users.uid.%s';
	
	protected static $_current_rights = null;
	
	protected static $_instance = null; 

	function __construct()
	{
		
	}
	
	/**
	 * get instance of Business_Common_CtySales
	 *
	 * @return Business_Common_CtySales
	 */
	public static function getInstance()
	{
		if(self::$_instance == null)
		{
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
//	public static function checkRight($right = '')
//	{
//		$auth = Zend_Auth::getInstance();
//		$identity = $auth->getIdentity();
//		
//		if(!$auth->hasIdentity()) return false;
//		
//		if(is_null(self::$_current_rights))
//		{			
//			if($auth->hasIdentity())
//			{
//				$_user = self::getInstance();			
//				$userid = $identity->userid;
//				self::$_current_rights = $_user->getRolesForUser($userid);	  
//			}
//			else
//			{
//				self::$_current_rights = array();
//			}			
//		}
//
//		$username = $identity->username;
//		if($username == "admin") return true;
//		
//		if(in_array($right, self::$_current_rights)) return true;
//		else return false;
//	}
//	
//	private function getKeyList()
//	{
//		return sprintf(Business_Common_Users::KEY_LIST);
//	}
//	
//	private function getKeyDetail($uid)
//	{
//		return sprintf(Business_Common_Users::KEY_DETAIL, $uid);
//	}
	
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
		$cache = GlobalCache::getCacheInstance();		
		return $cache;
	}
	
	public function getList()
	{
//		$cache = $this->getCacheInstance();
//		$key = $this->getKeyList();
//		$result = $cache->getCache($key);
//						
//		if($result === FALSE)
//		{
			$db = $this->getDbConnection();
			$query = "SELECT * FROM  $this->_tablename cty , cated_products cp where cty.cateid_products = cp.id";
//			var_dump($query);exit();
                        $result = $db->fetchAll($query);
//			if(!is_null($result) && is_array($result))
//			{
//				$cache->setCache($key, $result);
//			}
//		}
		
		return $result;		
	}
        public function getListByIdByMonthYear($id,$user_id,$month_created,$year_created){
            
        }
        public function getListById($id){
            $db = $this->getDbConnection();
            $query = "select * from $this->_tablename where id = $id";
            $result = $db->fetchAll($query);
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
            return $result;
	}
        public function getCateid($cated_id,$vote_id,$months_created,$years_created,$type)
	{
		$list = $this->getListByCatedId($cated_id,$vote_id,$months_created,$years_created,$type);
		if($list != null && is_array($list) && count($list) > 0)
		{
			for($i=0;$i<count($list);$i++)
			{
				if($list[$i]['cateid_products'] == $cated_id)
				{
					return $list[$i];
				}
			}
		}
		return null;
	}
        public function getListByCatedId($cateid,$vote_id,$months_created,$years_created,$type="")
	{

            $db = $this->getDbConnection();
            $query = "SELECT * FROM  $this->_tablename  where cateid_products = $cateid and user_id = $vote_id and months_created = $months_created and years_created = $years_created";
            if($type != null){
                $query .= " and type = $type";
            }
//    			var_dump($query);exit();
            $result = $db->fetchAll($query);
//            var_dump($result);exit();
            return $result;		
	}
	public function getListGroupByVoteId($userid,$months,$years,$check_vote="")
	{
            $cache = $this->getCacheInstance();
		$key = "getListGroupByVoteId.$this->_tablename.$userid.$months.$years";
		$result = $cache->getCache($key);
//                $cache->deleteCache($key);exit();
                $result = FALSE;
		if($result === FALSE)
                {
                    $db = $this->getDbConnection();
                    $query = "SELECT user_id,cateid_products,sum_prices,sum_numbers FROM  $this->_tablename  where is_actived = 1";
                    if($check_vote != null){
                        $query .=" and check_vote = $check_vote ";
                    }
                    if($userid != null){
                        $query .=" and user_id = $userid ";
                    }
                    if($months != null){
                        $query .="  and months_created = $months";
                    }
                    if($years != null){
                        $query .="  and years_created = $years ";
                    }
//			var_dump($query);exit();
                    $result = $db->fetchAll($query);
                    if(!is_null($result) && is_array($result))
                    {
                            $cache->setCache($key, $result,60*5);
                    }
                }
			
		return $result;		
	}
	public function getListByDayVote($userid,$months,$years)
	{

			$db = $this->getDbConnection();
			$query = "SELECT cty.*,cp.cate_product_name FROM  $this->_tablename cty , cated_products cp where cty.cateid_products = cp.id and cty.user_id = $userid and months_created = $months and years_created = $years order by cty.id asc";
//			var_dump($query);exit();
                        $result = $db->fetchAll($query);

		return $result;		
	}
	public function getListByVote($userid,$months)
	{

			$db = $this->getDbConnection();
			$query = "SELECT cty.*,cp.cate_product_name FROM  $this->_tablename cty , cated_products cp where cty.cateid_products = cp.id and cty.user_id = $userid and months_created = $months order by cty.id asc";
//			var_dump($query);exit();
                        $result = $db->fetchAll($query);

		return $result;		
	}
	
	public function sumPriceSalesById($id_users,$months,$years){
            $db = $this->getDbConnection();
            $query = "select sum(sum_prices) from $this->_tablename where user_id = $id_users and is_actived =1 and months_created = $months and years_created = $years";
//            var_dump($query);exit();
            $result = $db->fetchAll($query);
            return $result;
        }
        public function countProductsSalesById($id_users,$months,$years){
            $db = $this->getDbConnection();
            $query = "select sum(sum_numbers) from $this->_tablename where user_id = $id_users and is_actived =1 and months_created = $months and years_created = $years";
//            var_dump($query);exit();
            $result = $db->fetchAll($query);
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
        
        public function updateSalesById($id,$data){
//            var_dump($id);exit();
            $db= $this->getDbConnection();
            $query = "id = ".$id;
//            var_dump($data);exit();
            $result = $db->update($this->_tablename, $data,$query);
//            $this->_deleteAllCache();
//            var_dump($result);exit();
            return $result;
        }

        public function deleteSalesById2($id){
            $db = $this->getDbConnection();
            $result = $db->delete($this->_tablename, "id = $id");
//            var_dump($result);exit();
            return $result;
        }
        public function getListByUname(){
            $db = $this->getDbConnection();
            $ch = 'vote_';
            $query = "select * from $this->_tablename  where username like '%$ch%'";
            $result = $db->fetchAll($query);
//            var_dump($result);exit();
            return $result;
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
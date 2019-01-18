<?php

class Business_Import_Users extends Business_Abstract
{
	private $_tablename = 'zfw_users';
	
	const KEY_LIST = 'zfw_users.list';
	const KEY_CHECKROLE = 'zfw_users.checkrole';
	const KEY_DETAIL = 'zfw_users.uid.%s';
	
	protected static $_current_rights = null;
	
	protected static $_instance = null; 

	function __construct()
	{
		
	} 
	
	/**
	 * get instance of Business_Import_Users
	 *
	 * @return Business_Import_Users
	 */
	public static function getInstance()
	{
		if(self::$_instance == null)
		{
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	public static function checkRight($right = '')
	{
		$auth = Zend_Auth::getInstance();
		$identity = $auth->getIdentity();
		
		if(!$auth->hasIdentity()) return 2; //chua login
                
                
                
		if(is_null(self::$_current_rights))
		{			
			if($auth->hasIdentity())
			{
				$_user = self::getInstance();			
				$userid = $identity->userid;
				self::$_current_rights = $_user->getRolesForUser($userid);	  
			}
			else
			{
				self::$_current_rights = array();
			}			
		}
		$username = $identity->username;
		if($username == "admin" || $username == "hnmobile") return 1;
		
                $arr = self::$_current_rights;
//                 var_dump($right,$arr);exit();
		if(in_array($right, $arr))
                {
                    return 1; //ok
                }else{
                    return 0; //khong dung quyen
                }
	}
	
	private function getKeyList()
	{
		return sprintf(Business_Import_Users::KEY_LIST);
	}
	private function getKeyCheckRole()
	{
		return sprintf(Business_Import_Users::KEY_CHECKROLE);
	}
	
	private function getKeyDetail($uid)
	{
		return sprintf(Business_Import_Users::KEY_DETAIL, $uid);
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
//		$cache = GlobalCache::getCacheInstance('noflushcache');		
		$cache = GlobalCache::getCacheInstance();		
		return $cache;
	}
	public function getDetailById($itemid){
            $cache = $this->getCacheInstance();
            $key    = 'getDetailById'.$this->_tablename.$itemid;
            $result = $cache->getCache($key);
            $result=FALSE;
            if($result === FALSE)
            {
                $db = $this->getDbConnection();
                $query = "select * from $this->_tablename where userid = $itemid";
                $result = $db->fetchAll($query);
                if(!is_null($result) && is_array($result))
                {
                        $cache->setCache($key, $result);
                }
            }
            
            return $result[0];
        }

        public function getListByGroup($group)
	{
		$cache = $this->getCacheInstance();
		$key = 'getListByGroup'.$group;
		$result = $cache->getCache($key);
		if($result === FALSE)
		{
			$db = $this->getDbConnection();
			$query = "SELECT * FROM " . $this->_tablename . " where 1=1 ";
                        if($group == 4){
                            $group = 'mb_';
                        }
                        if($group == 5){
                            $group = 'qlkt_';
                        }
                        if($group == 6){
                            $group = 'vote_';
                        }
                        if($group == 7){
                            $group = 'mbk_';
                        }
                        if($group == 8){
                            $group = 'kinhdoanh_';
                        }
                        if($group == 9){
                            $group = 'services_';
                        }
                        $query.=" and username like '%$group%'";
			$result = $db->fetchAll($query);
			if(!is_null($result) && is_array($result))
			{
				$cache->setCache($key, $result);
			}
		}
		
		return $result;		
	}
        public function getList()
	{
		$cache = $this->getCacheInstance();
		$key = $this->getKeyList();
		$result = $cache->getCache($key);
		$result=FALSE;				
		if($result === FALSE)
		{
			$db = $this->getDbConnection();
			$query = "SELECT * FROM " . $this->_tablename . " ORDER BY username";
			$result = $db->fetchAll($query);
			if(!is_null($result) && is_array($result))
			{
				$cache->setCache($key, $result);
			}
		}
		
		return $result;		
	}
	
	public function getUserByUid($uid)
	{
		$cache = $this->getCacheInstance();
		$key = $this->getKeyDetail($uid);
		$result = $cache->getCache($key);
		if($result === FALSE)
		{
			$db = $this->getDbConnection();
			$query = "SELECT * FROM " . $this->_tablename . " WHERE userid = ?";
			$data = array($uid);
			$result = $db->fetchAll($query, $data);
			if(!is_null($result) && is_array($result) && count($result) > 0)
			{
				$result = $result[0];
				$result = $cache->setCache($key, $result);
			}			
		}
		return $result;
	}
	public function getUserByUid2($uid)
	{
//		$cache = $this->getCacheInstance();
//		$key = $this->getKeyDetail($uid);
//		$result = $cache->getCache($key);
//		if($result === FALSE)
//		{
			$db = $this->getDbConnection();
			$query = "SELECT * FROM " . $this->_tablename . " WHERE userid = ?";
//                        var_dump($uid);exit();
			$data = array($uid);
			$result = $db->fetchAll($query, $data);
//			if(!is_null($result) && is_array($result) && count($result) > 0)
//			{
//				$result = $result[0];
//				$result = $cache->setCache($key, $result);
//			}			
//		}
		return $result;
	}
        public function getListByParentid($parentid=0,$is_actived="",$type=""){
            $db = $this->getDbConnection();
            $query = " SELECT * FROM $this->_tablename where 1=1 ";
            if($parentid != 0){
                $query .= " and parentid = $parentid ";
            }
            if($is_actived != ""){
                $query .= " and is_actived = $is_actived ";
            }
            if($type != ""){
                $query .= " and username like '%$type%' ";
            }
//            var_dump($query);exit();
            $result = $db->fetchAll($query);
            return $result;
        }

        public function getMemberByUid($uid)
	{
		$cache = $this->getCacheInstance();
		$key = "getMemberByStoreid".$uid.$this->_tablename;
		$result = $cache->getCache($key);
                $cache->deleteCache($key);
		if($result === FALSE)
		{
			$db = $this->getDbConnection();
			$query = "SELECT * FROM " . $this->_tablename . " WHERE  parentid = $uid and is_actived = 1 and type = 5";
//                        var_dump($query);exit();
			$result = $db->fetchAll($query);
                        if(is_null($result) && is_array($result)){
                            $cache->setCache($key, $result);
                        }
//						
		}
		return $result;
	}
	public function getMBKByUid($uid)
	{
                $db = $this->getDbConnection();
                $query = "SELECT * FROM " . $this->_tablename . " WHERE  parentid = $uid and type = 6 and is_actived = 1";
//                        var_dump($query);exit();
                $result = $db->fetchAll($query);
		return $result;
	}
	public function getListByPid($pid,$keyword)
	{
                $db = $this->getDbConnection();
                $query = "SELECT * FROM " . $this->_tablename . " WHERE  parentid = $pid and is_actived = 1 and type = 5 ";
                if($keyword != null){
                    $query .= " and (fullname like '%$keyword%' or phone like '%$keyword%')";
                }
//              var_dump($query);exit();
                $result = $db->fetchAll($query);
		return $result;
	}
	public function getListByUser($pid="",$keyword,$is_active="",$type="")
	{
                $db = $this->getDbConnection();
                $query = "SELECT * FROM " . $this->_tablename . " WHERE  1=1 ";
                if($is_active != null){
                    $query .=" and is_actived = $is_active";
                }
                if($pid != null){
                    $query .=" and parentid = $pid";
                }
                if($type != null){
                    $query .=" and type = 5 ";
                }
                if($keyword != null){
                    $query .= " and (fullname like '%$keyword%' or phone like '%$keyword%')";
                }
//              var_dump($query);exit();
                $result = $db->fetchAll($query);
		return $result;
	}
        
	public function countMemberById($uid)
	{
//            exit('dsadsads');
//		$cache = $this->getCacheInstance();
//		$key = "getMemberByUid".$uid;
//		$result = $cache->getCache($key);
//                $cache->deleteCache($key);
//		if($result === FALSE)
//		{
//                    exit('dsa');
			$db = $this->getDbConnection();
			$query = "SELECT count(*) FROM " . $this->_tablename . " WHERE  parentid = $uid and is_actived = 1 and type = 5";
//                        var_dump($query);exit();
//			$data = array($uid);
			$result = $db->fetchAll($query);
//                        var_dump($result);exit();
//			$result = $cache->setCache($key, $result);
//						
//		}
		return $result;
	}
	public function getMemberByUidByMonths($uid)
	{

			$db = $this->getDbConnection();
			$query = "SELECT * FROM " . $this->_tablename . " WHERE  parentid = $uid and is_actived = 1 and type = 5";
//                        var_dump($query);exit();
			$result = $db->fetchAll($query);
//                        var_dump($result);exit();			
//		}
		return $result;
	}
	
        public function deleteSalesById($id){
//            exit('dsada');
            $db=  $this->getDbConnection();
//            var_dump($db);exit();
            $query = "userid = $id";
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
            $query = "userid = ".$id;
            $data =array(
                "is_actived" => "1"
                );
            $result = $db->update($this->_tablename, $data, $query);
            return $result;
        }
        
        
        public function getMemberByPid($pid)
	{
//            exit('dsadsads');
		$cache = $this->getCacheInstance();
		$key = "getMemberByPid".$pid;
		$result = $cache->getCache($key);
//                $cache->deleteCache($key);
		if($result === FALSE)
		{
//                    exit('dsadsads');
			$db = $this->getDbConnection();
//                        var_dump($db);exit();
			$query = "SELECT * FROM " . $this->_tablename . " WHERE  userid = '$pid'";
//                        var_dump($query);exit();
//			$data = array($uid);
			$result = $db->fetchAll($query);
//                        var_dump($result);exit();
			$result = $cache->setCache($key, $result);
						
		}
		return $result;
	}
	public function getUser($username)
	{
		$list = $this->getList();
		if($list != null && is_array($list) && count($list) > 0)
		{
			for($i=0;$i<count($list);$i++)
			{
				if($list[$i]['username'] == $username)
				{
					return $list[$i];
				}
			}
		}
		return null;
	}
	public function getPassword($username,$password)
	{
		$list = $this->getList();
		if($list != null && is_array($list) && count($list) > 0)
		{
			for($i=0;$i<count($list);$i++)
			{
				if($list[$i]['username'] == $username && $list[$i]['password'] == $password)
				{
					return $list[$i];
				}
			}
		}
		return null;
	}
	public function checkRole($username,$password)
	{
		$cache = $this->getCacheInstance();
		$key = $this->getKeyCheckRole();
		$result = $cache->getCache($key);
		$result=FALSE;				
		if($result === FALSE)
		{
			$db = $this->getDbConnection();
			$query = "SELECT * FROM $this->_tablename where username = '$username' and password = '$password'";
//			var_dump($query);exit();
                        $result = $db->fetchAll($query);
			if(!is_null($result) && is_array($result))
			{
				$cache->setCache($key, $result);
			}
		}
		
		return $result[0];
	}
	
	//return userid last inserted
	public function addUser($data)
	{
		$db = $this->getDbConnection();
		$result = $db->insert($this->_tablename, $data);
		
		$lastid = 0;
		if($result)
		{
			$lastid = $db->lastInsertId();
			$cache = $this->getCacheInstance();
			$key = $this->getKeyList();
			$cache->deleteCache($key);
		}
		return $lastid;
	}
        public function addMember($data){
            $db=  $this->getDbConnection();
            $result = $db->insert($this->_tablename, $data);
            if ($result > 0) {
                    $lastid= $db->lastInsertId($this->_tablename); // tra ve id khi them vao
                }
            $this->_deleteAllCache();
//            var_dump($result);exit();
            return $lastid;
        }

        public function resetAllPass(){
            $db = $this->getDbConnection();
            $new_password = MD5('123456');
            $ch = 'vote';
            //$query = "UPDATE  $this->_tablename SET password = '$new_password' WHERE username LIKE '%$ch%'";
            $query = "username LIKE '%$ch%'";
            //$query = 'update'.$this->_tablename.'set password =' .$new_password.'where username LIKE '%vote%'';
            //var_dump($query);exit();
            $data = array(
                'password' => $new_password
            );
            $result = $db->update($this->_tablename, $data, $query);
            //var_dump($result);exit();
            return $result;
            
        }
        public function resetPassById($userid){
            $db = $this->getDbConnection();
            $new_password = md5('hnam@123@abc');
            $query = "userid = $userid";
            $data = array(
                'password' => $new_password
            );
            
            $result = $db->update($this->_tablename, $data, $query);
//            var_dump($query);exit();
            return $result;
        }
        public function update($userid,$data){
            $db = $this->getDbConnection();
            $query = "userid = $userid";
            $result = $db->update($this->_tablename, $data,$query);
            $this->_deleteAllCache();
            return $result;
        }

        public function getListByUname($vote_all=true){
            $cache = $this->getCacheInstance();
            if($vote_all == true){
                $key = 'getListByUname.'.$this->_tablename;
            }else{
                $key = 'getListByUname1.'.$this->_tablename;
            }
            $result = $cache->getCache($key);
            if($result===FALSE){
               $db = $this->getDbConnection();
                $ch = 'vote_';
                if ($vote_all==true) {
                    $query = "select * from $this->_tablename where username like '%$ch%'";
                } else {
                    $query = "select * from $this->_tablename where username like '%$ch%' AND username != 'vote_all' and username !='vote_hnamvt' ";
                }
                $result = $db->fetchAll($query); 
                if(is_null($result) && is_array($result)){
                    $cache->setCache($key, $result);
                }
            }    
//            var_dump($result);exit();
            return $result;
        }
        public function getListUser($keyword="",$vote_id="",$is_actived=""){
            $cache = $this->getCacheInstance();
		$key = $this->getKeyList().$keyword.$vote_id.$is_actived;
		$result = $cache->getCache($key);
//                $cache->deleteCache($key);
		if($result === FALSE)
		{
			$db = $this->getDbConnection();
			$query = " select * from $this->_tablename where  parentid !=0 ";
                        if($is_actived !=null){
                            $query .=" and is_actived = $is_actived";
                        }
                        if($keyword != null){
                            $query .=" and (fullname like '%$keyword%' or phone like '%$keyword%') ";
                        }
                        if($vote_id != null){
                            $query .=" and parentid = $vote_id ";
                        }
                        $query .= " order by userid desc";
//                        var_dump($query);exit();
			$result = $db->fetchAll($query);
                        if(is_null($result) && is_array($result)){
                            $cache->setCache($key, $result,60*5);
                        }
                        
		}
            return $result;
        }
        public function getMember($keyword,$vote_id){
            $cache = $this->getCacheInstance();
		$key = $this->getKeyList().$keyword.$vote_id;
		$result = $cache->getCache($key);
//                $cache->deleteCache($key);
		if($result === FALSE)
		{
			$db = $this->getDbConnection();
			$query = " select * from $this->_tablename where parentid !=0 ";
                        if($keyword != null){
                            $query .=" and fullname like '%$keyword%' or phone like '%$keyword%' ";
                        }
                        if($vote_id != null){
                            $query .=" and parentid = $vote_id ";
                        }
                        $query .= " order by userid desc";
//                        var_dump($query);exit();
			$result = $db->fetchAll($query);
                        $cache->setCache($key, $result);
		}
            return $result;
        }
        private function _deleteAllCache()
        {
                $cache = $this->getCacheInstance('default');
                $cache->flushAll();
        }
        public function deleteMember($id){
            $db=  $this->getDbConnection();
            $query = "userid = $id";
            $data =array(
                "is_actived" => "0"
                );
            $result = $db->update($this->_tablename, $data, $query);
            $this->_deleteAllCache();
//            var_dump($result);exit();
            return $result;
        }
	public function restoreMember($id){
            $db=  $this->getDbConnection();
            $query = "userid = ".$id;
            $data =array(
                "is_actived" => "1"
                );
            $result = $db->update($this->_tablename, $data, $query);
            $this->_deleteAllCache();
            return $result;
        }
        public function getListById($id){
            $db = $this->getDbConnection();
            $query = "select * from $this->_tablename where userid = $id";
//            var_dump($query);exit();
            $result = $db->fetchAll($query);
            
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
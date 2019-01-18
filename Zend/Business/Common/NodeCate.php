<?php

class Business_Common_NodeCate extends Business_Abstract
{
	private $_tablename = 'nodecate';
	
	const KEY_DETAIL_NODECATE = 'node.%s'; 		// node.id
	const KEY_LIST_MODULE_NODECATENAME = 'node.module-%s.nodecatename-%s';
	const KEY_LIST_PARENT_MODULE_NODECATENAME = 'node.parent.module-%s.nodecatename-%s';	
					 	
	private static $_instance = null;

	private static $id_filter = 0;
	private static $depth_filter = 1;
	
	function __construct()
	{
		
	}
	
	function getKeyNodeCate($id)
	{
		return sprintf(Business_Common_NodeCate::KEY_DETAIL_NODECATE, $id);
	}
	
	function getKeyModuleCateName($module, $nodecatename)
	{
		return sprintf(Business_Common_NodeCate::KEY_LIST_MODULE_NODECATENAME, $module, $nodecatename);
	}
	
	function getKeyParentModuleCateName($module, $nodecatename)
	{
		return sprintf(Business_Common_NodeCate::KEY_LIST_PARENT_MODULE_NODECATENAME, $module, $nodecatename);
	}
	
	/**
	 * get instance of class Business_Common_NodeCate
	 *
	 * @return Business_Common_NodeCate
	 */
	public static function getInstance()
	{
		if(self::$_instance == null)
		{
			self::$_instance = new Business_Common_NodeCate();
		}
		return self::$_instance;
	}
	
	/**
	 * Get DB Connection
	 *
	 * @return Zend_Db_Adapter_Abstract
	 */
	function getDbConnection()
	{		
		$db    	= Globals::getDbConnection('maindb');		
		return $db;	
	}
	
	public function getParentCate($nodecatename, $module)
	{
		$key = $this->getKeyParentModuleCateName($module, $nodecatename);
		$result = GlobalCache::getCache($key);
		if($result === FALSE)
		{
			$db = $this->getDbConnection();
			$query = "SELECT * FROM " . $this->_tablename . " WHERE pid=0 AND module=? AND nodecatename=? ORDER BY myorder desc,id";
			$data = array($module, $nodecatename);
			$result = $db->fetchAll($query, $data);
			if(!is_null($result) && is_array($result))
			{
				GlobalCache::setCache($key, $result);
			}
		}
		return $result;
	}
	
	public function getListFilter($nodecatename, $module, $id, $depth = null)
	{
		$list = $this->getList($nodecatename, $module);
		self::$id_filter = $id;
		if($depth == null) $list = array_filter($list, "Business_Common_NodeCate::filterDaisyStringCate");
		else
		{
			self::$depth_filter = $depth;
			$list = array_filter($list, "Business_Common_NodeCate::filterDaisyStringAndDepth");
		}
		$return = array();
		foreach($list as $key => $value)
		{
			$return[] = $value;
		}
		return $return;
	}	
	
	public function getList($nodecatename, $module)	
	{
		$key = $this->getKeyModuleCateName($module, $nodecatename);
		$result = GlobalCache::getCache($key);
		if($result === FALSE)
		{
			$db = $this->getDbConnection();
			$query = "SELECT * FROM " . $this->_tablename . " WHERE module=? AND nodecatename=? ORDER BY depth asc,myorder desc, id";
			$data = array($module, $nodecatename);
			$result = $db->fetchAll($query, $data);
			if(!is_null($result) && is_array($result))
			{
				GlobalCache::setCache($key, $result);
			}
		}
		return $result;
	}
	
	public function deleteByCateName($nodecatename, $module)
	{
		$nodecatelist = $this->getList($nodecatename, $module);
		if($nodecatelist != null && is_array($nodecatelist) && count($nodecatelist) > 0)
		{
			$db = $this->getDbConnection();
			$where = array();
			$where[] = "nodecatename='" . parent::adaptSQL($nodecatename) . "'";
			$where[] = "module='" . parent::adaptSQL($module) . "'";
			$db->delete($this->_tablename, $where);
			//clear cache each node cate first
			for($i=0;$i<count($nodecatelist);$i++)
			{
				$id = $nodecatelist[$i]["id"];
				$this->clearCacheDetail($id);
			}
			$this->clearCache($module, $nodecatename);				
		}		
	}
	
	public function deleteNodeCate($id,$nodecatename, $module)
	{
		
		//lay pid cua nodecate hien tai
		$currentnode = $this->getNodeCate($id, $nodecatename, $module);
		if($currentnode == null) return false;
		
		$pid = $currentnode["pid"];
		$_depth = 0;
		$_daisystring = "";
		
		if($pid != 0)
		{
			//lay depth va daisystring cua parent nodecate
			$parentnode = $this->getNodeCate($pid, $nodecatename, $module);
			if($parentnode == null) return false;
			$_depth = $parentnode["depth"];
			$_daisystring = $parentnode["daisystring"]; 
		}
		
		//update depth, daisystring for all sub node cate of current nodecate
		$subnodecate = $this->getListFilter($nodecatename, $module, $id);		
		if($subnodecate != null && is_array($subnodecate) && count($subnodecate) > 0)
		{			
			for($i=0; $i < count($subnodecate); $i++)
			{				
				$myid = $subnodecate[$i]["id"];				
				if($myid == $id) continue;			//skip current node cate				
				$mypid = $subnodecate[$i]["pid"];
				if($mypid == $id)					// lien ke voi nodecate current
				{
					$depth = $_depth++;
					if($_daisystring == '')
					{
						$daisystring = " " . $myid . " ";
					}
					else
					{
						$daisystring  = ", " . $myid . " ";
					}
					$data = array(
						"pid" => $pid,
						"depth" => $depth,
						"daisystring" => $daisystring
					);
					$this->updateData($myid, $nodecatename, $module, $data);
				}
				else
				{
					$parentcate = $this->getNodeCate($mypid, $nodecatename, $module);
					if($parentcate != null)
					{
						$depth = intval($parentcate["depth"]);
						$daisystring = $parentcate["daisystring"];
						$depth++;
						$daisystring .= ", " . $myid . " ";				
					}
					else
					{
						$depth = 1;
						$daisystring = " " . $myid . " ";
					}
					
					$data = array(					
						"depth" => $depth,
						"daisystring" => $daisystring
					);					
					$this->updateData($myid, $nodecatename, $module, $data);
				}				
			}
		}
		
				
		$this->_deleteNodeCate($id, $nodecatename, $module);
		
		return true;		
	}
	
	public function updateNodeCate($id, $nodecatename, $module, $data)
	{
		if(array_key_exists('nodecatename',$data)) unset($data['nodecatename']);
		if(array_key_exists('module',$data)) unset($data['module']);
		if(array_key_exists('id',$data)) unset($data['id']);
		if(array_key_exists('pid', $data)) unset($data['pid']);
		if(array_key_exists('endcate', $data)) unset($data['endcate']);
		if(array_key_exists('depth', $data)) unset($data['depth']);
		if(array_key_exists('daisystring', $data)) unset($data['daisystring']);
		
		$result = $this->updateData($id, $nodecatename, $module, $data);
		return $result;		
	}
	
	public function moveParentCate($id, $nodecatename, $module, $pid, $pid_new)
	{						
		$depth = 0;
		$daisystring = "";
		$parentcate = null;
		if(intval($pid_new) != 0)
		{
			//load new parent catenode
			$parentcate = $this->getNodeCate($pid_new, $nodecatename, $module);
			if($parentcate != null)
			{
				//check truong hop pid_new lai la con cua id
				$find = " " . $id . " ";
				$pos = strpos($parentcate["daisystring"],$find);		
				if($pos !== false)
				{					
					return false;				
				}				
				$depth = intval($parentcate["depth"]);
				$daisystring = $parentcate["daisystring"];				
			}
		}
				
		//update depth and daisystring to current nodecate
		$depth++;
		if($pid_new != 0) $daisystring .= ", " . $id . " ";
		else $daisystring = " " . $id . " ";
		
		$data = array(
			"pid" => $pid_new,
			"depth" => $depth,
			"daisystring" => $daisystring
		);
				
		$this->updateData($id, $nodecatename, $module, $data);		
		
		//update endcate to new parent nodecate
		if($parentcate != null)
		{			
			$data = array(
				"endcate" => "0"
			);
			$this->updateData($pid_new, $nodecatename, $module, $data);				
		}
			
		
		//update depth, daisystring for all sub node cate of current nodecate
		$subnodecate = $this->getListFilter($nodecatename, $module, $id);		
		if($subnodecate != null && is_array($subnodecate) && count($subnodecate) > 0)
		{			
			for($i=0; $i < count($subnodecate); $i++)
			{				
				$myid = $subnodecate[$i]["id"];				
				if($myid == $id) continue;			//skip current node cate
				$mypid = $subnodecate[$i]["pid"];
				$parentcate = $this->getNodeCate($mypid, $nodecatename, $module);
				if($parentcate != null)
				{
					$depth = intval($parentcate["depth"]);
					$daisystring = $parentcate["daisystring"];
					$depth++;
					$daisystring .= ", " . $myid . " ";				
				}
				else
				{
					$depth = 1;
					$daisystring = " " . $myid . " ";
				}
				
				$data = array(					
					"depth" => $depth,
					"daisystring" => $daisystring
				);
				//echo "update subcate id=$myid";
				//var_dump($data);
				//echo "<br><br>";
				$this->updateData($myid, $nodecatename, $module, $data);
			}
		}
		
		//update endcate to old parent nodecate
		$parentcate_old = $this->getNodeCate($pid, $nodecatename, $module);		
		if($parentcate_old != null)
		{
			$result = $this->getListFilter($nodecatename, $module, $pid);
			if($result == null || (is_array($result) && count($result) > 0))
			{
				$data = array(
					"endcate" => "1"
				);
				$this->updateData($pid, $nodecatename, $module, $data);
			}				
		}
		
		//clear all cache with nodecatename and module
		$this->clearCache($module, $nodecatename); 
		return true;
	}
	
	public function getNodeCate($id, $nodecatename, $module)
	{
		$key = $this->getKeyNodeCate($id);
		$result = GlobalCache::getCache($key);
		
		if($result === FALSE)
		{
			$db = $this->getDbConnection();		
				
			$query = "SELECT * FROM " . $this->_tablename . " WHERE id=? AND module=? AND nodecatename=?";
			$data = array();			
			$data[] = $id;
			$data[] = $module;
			$data[] = $nodecatename;
			
			$result = $db->fetchAll($query, $data);
			if(!is_null($result) && is_array($result) && count($result) == 1)
			{
				$result = $result[0];				
				GlobalCache::setCache($key, $result);
			}	
		}
		if($result == null) return null;
		return $result;		
	}

	/**
	 * Add new nodecate
	 *
	 * @param string $nodecatename
	 * @param string $module
	 * @param int $pid
	 * @param string $title
	 * @param int $myorder
	 * @param string $linkpath
	 * @param int $status
	 * @param int $expanded
	 * @return id of nodecate inserted
	 */
	public function addNodeCate($nodecatename, $module, $pid=0,$title='', $metadata='', $myorder=0, $linkpath='', $status=1, $expanded=1)
	{
		if($pid == 0)
		{
			//add new node cate parent			
			$data = array(
				"nodecatename" => $nodecatename,
				"module" => $module,
				"pid" => $pid,
				"linkpath" => $linkpath,
				"title" => $title,
				"status" => $status,
				"endcate" => 1,
				"expanded" => $expanded,
				"myorder" => $myorder,
				"depth" => 1,
				"daisystring" => "",
				"metadata" => $metadata
			);			
			$db = $this->getDbConnection();
			$result = $db->insert($this->_tablename, $data);
			$lastid = $db->lastInsertId();
			$daisystring = " " . $lastid . " ";
			$data = array(
				"daisystring" => $daisystring
			);
			$where = array();
			$where[] = "id='" . parent::adaptSQL($lastid) . "'";
			$where[] = "module='" . parent::adaptSQL($module) . "'";
			$where[] = "nodecatename='" . parent::adaptSQL($nodecatename) . "'";
			$result = $db->update($this->_tablename, $data, $where);
			
			$this->clearCache($module, $nodecatename);
			return $lastid;
		}
		else
		{			
			$db = $this->getDbConnection();
			
			//load parent node cate
			$result = $this->getNodeCate($pid, $nodecatename, $module);			
			
			if($result != null)
			{
				$depth = intval($result['depth']);
				$depth++;
				$daisystring = $result['daisystring'];
				
				$data = array(
					"nodecatename" => $nodecatename,
					"module" => $module,
					"pid" => $pid,
					"linkpath" => $linkpath,
					"title" => $title,
					"status" => $status,
					"endcate" => 1,
					"expanded" => $expanded,
					"myorder" => $myorder,
					"depth" => $depth,
					"daisystring" => ""
				);
				
				$result = $db->insert($this->_tablename, $data);
				$lastid = $db->lastInsertId();
				$daisystring .= ", " . $lastid . " ";
				$data = array(
					"daisystring" => $daisystring
				);
				
				$this->updateData($lastid, $nodecatename, $module, $data);				
				
				//update endcate of pid
				$data = array(
					"endcate" => "0"
				);
				$this->updateData($pid, $nodecatename, $module, $data);
								
				$this->clearCache($module, $nodecatename);
				return $lastid;
			}			
		}
		return 0;		
	}	
	
	// private function	
	static function filterDaisyStringCate($var)
	{
		$find = " " . self::$id_filter . " ";
		$pos = strpos($var["daisystring"],$find);		
		if($pos !== false) return true;
		else return false;
	}
	
	static function filterDaisyStringAndDepth($var)
	{
		$find = " " . self::$id_filter . " ";
		$pos = strpos($var["daisystring"],$find);		
		if($pos !== false)
		{
			if(intval($var["depth"]) == self::$depth_filter) return true;
			else return false;			
		}
		else return false;
	}
	
	private function _deleteNodeCate($id, $nodecatename, $module)
	{
		$db = $this->getDbConnection();
		$where = array();
		$where[] = "id='" . parent::adaptSQL($id) . "'";
		$where[] = "module='" . parent::adaptSQL($module) . "'";
		$where[] = "nodecatename='" . parent::adaptSQL($nodecatename) . "'";
		$db->delete($this->_tablename,$where);
		
		$this->clearCacheDetail($id);
		$this->clearCache($module, $nodecatename);		
	}
	
	private function clearCacheDetail($id)
	{
		$key = $this->getKeyNodeCate($id);
		GlobalCache::deleteCache($key);
	}
	
	private function clearCache($module, $nodecatename)
	{
		$key = $this->getKeyModuleCateName($module, $nodecatename);
		GlobalCache::deleteCache($key);
		$key = $this->getKeyParentModuleCateName($module, $nodecatename);
		GlobalCache::deleteCache($key);	
	}
	
	private function updateData($id, $nodecatename, $module, $data)
	{
		if(array_key_exists('nodecatename',$data)) unset($data['nodecatename']);
		if(array_key_exists('module',$data)) unset($data['module']);
		if(array_key_exists('id',$data)) unset($data['id']);
		
		$db = $this->getDbConnection();
		
		$where = array();
		$where[] = "id='" . parent::adaptSQL($id) . "'";
		$where[] = "nodecatename='" . parent::adaptSQL($nodecatename) . "'";
		$where[] = "module='" . parent::adaptSQL($module) . "'";
						
		$result = $db->update($this->_tablename, $data, $where);
		$this->clearCacheDetail($id);
		$this->clearCache($module, $nodecatename);
		return $result;	
	}
	
}

?>
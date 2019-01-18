<?php

class Business_Common_Category extends Business_Abstract
{
	private $_tablename = 'category';
	private $module = 'category';
	
	const KEY_LIST = 'category-all';		//key list all
		
	private static $_instance = null; 
	
	function __construct()
	{			
	}	
	
	function getKeyList()
	{
		return sprintf(Business_Common_Category::KEY_LIST);
	}
	
	/**
	 * get instance of Business_Common_Category
	 *
	 * @return Business_Common_Category
	 */
	public static function getInstance()
	{
		if(self::$_instance == null)
		{
			self::$_instance = new Business_Common_Category();
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

	/**
	 * get NodeCate model
	 *
	 * @return Business_Common_NodeCate
	 */
	private function getNodeCateModel()
	{
		return Business_Common_NodeCate::getInstance();
	}
	
	public function getListCategory()
	{
		$key = $this->getKeyList();
		$cache = $this->getCacheInstance();		
		$result = $cache->getCache($key);
		
		if($result === FALSE)
		{
			$db = $this->getDbConnection();
			$query = "SELECT * FROM " . $this->_tablename . " ORDER BY categoryid";
			$result = $db->fetchAll($query);
			if(!is_null($result) && is_array($result))
			{
				$cache->setCache($key, $result);
			}
		}		
		return $result;		
	}
	
	public function addCategoryName($catename, $description='')
	{
		$db = $this->getDbConnection();
		$data = array(
			"catename" => $catename,
			"created" => date("Y-m-d H:i:s"),
			"description" => $description
		);
		
		$result = $db->insert($this->_tablename, $data);
		$cache = $this->getCacheInstance();
		$key = $this->getKeyList();
		$cache->deleteCache($key);		
		return $result;		
	}
	
	public function deleteCategoryName($categoryid)
	{
		
		
		$db = $this->getDbConnection();
		$where = array();
		$where[] = "categoryid='" . parent::adaptSQL($categoryid) . "'";
		$db->delete($this->_tablename, $where);
		$cache = $this->getCacheInstance();
		$key = $this->getKeyList();
		$cache->deleteCache($key);
	}
	
	public function getCategoryName($categoryid)
	{
		$db = $this->getDbConnection();
		$query = "SELECT * FROM " . $this->_tablename . " WHERE categoryid=?";
		$data = array($categoryid);
		$result = $db->fetchAll($query, $data);
		if(result != null) return $result[0];		
		return $result;
	}
	
	public function updateCategoryName($categoryid, $data)
	{
		$db = $this->getDbConnection();
		$where = "categoryid='" . parent::adaptSQL($categoryid) . "'";
		$result = $db->update($this->_tablename, $data, $where);
		$cache = $this->getCacheInstance();
		$key = $this->getKeyList();
		$cache->deleteCache($key);
		return $result;
	}
	
	//for interact with nodecate
	public function getParentCate($catename)
	{
		$_nodecate = $this->getNodeCateModel();
		$result = $_nodecate->getParentCate($catename, $this->module);
		return $result;
	}
	
	public function moveCateToAnotherParent($catename, $id, $pid, $pid_new)
	{
		$_nodecate = $this->getNodeCateModel();
		$result = $_nodecate->moveParentCate($id, $catename, $this->module, $pid, $pid_new);
		return $result;		
	}
	
	public function getCate($catename, $id)
	{
		$_nodecate = $this->getNodeCateModel();
		$result = $_nodecate->getNodeCate($id, $catename, $this->module);
		return $result;
	}
	
	public function updateCate($catename, $id, $data)
	{
		$_nodecate = $this->getNodeCateModel();
		$result = $_nodecate->updateNodeCate($id, $catename, $this->module, $data);
		return $result;		
	}
	
	public function deleteCate($catename, $id=0)
	{
		$_nodecate = $this->getNodeCateModel();		
		$result = $_nodecate->deleteNodeCate($id, $catename, $this->module);
		return $result;
	}
	
	public function addCate($catename, $title='', $linkpath = '', $pid=0, $metadata = '', $myorder=0, $status=1, $expanded=1)
	{
		$_nodecate = $this->getNodeCateModel();		
		$result = $_nodecate->addNodeCate($catename, $this->module, $pid, $title, $metadata, $myorder, $linkpath, $status, $expanded);
		return $result;
	}
	
	public function getAllCate($catename)
	{
		if($catename == '') return array();
		
		$_nodecate = $this->getNodeCateModel();
		$list = $_nodecate->getList($catename, $this->module);

		return $list;		
	}

	public function getListFilter($catename, $id, $depth = null)
	{
		$_nodecate = $this->getNodeCateModel();
		return $_nodecate->getListFilter($catename, $this->module, $id, $depth);
	}
}

?>
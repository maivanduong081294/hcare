<?php

class Business_Common_NodePage extends Business_Abstract
{
	private $_tablename = 'nodepage';
	
	const KEY_LIST = 'nodepage_list';		//key list
	const KEY_PAGEPATH = 'nodepage-%s';		//key = nodepage-pagepath
	
	private $module = 'nodepage';
	
						 	
	private static $_instance = null; 
	
	function __construct()
	{		
	}
	
	private function getKeyList()
	{
		return sprintf(Business_Common_NodePage::KEY_LIST);
	}
	
	private function getKeyByPagePath($pagepath)
	{
		return sprintf(Business_Common_NodePage::KEY_PAGEPATH, $pagepath);
	}
	
	/**
	 * get instance of Business_Common_NodePage
	 *
	 * @return Business_Common_NodePage
	 */
	public static function getInstance()
	{
		if(self::$_instance == null)
		{
			self::$_instance = new Business_Common_NodePage();
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
	
	public function getNodePageContent($pageid, $lang = '')
	{
		$node = Business_Common_Node::getInstance();
		$result = $node->getNode($this->module, $pageid, $lang);
		return $result;		
	}
	
	public function insertNodePageContent($pageid, $content, $lang = '')
	{
		$node = Business_Common_Node::getInstance();

		$data = array(
			'title' => '',
			'shortcontent' => '',
			'content' => $content,
			'status' => '1',
			'created' => date("Y-m-d H:i:s"),
			'changed' => date("Y-m-d H:i:s")
		);
		
		$result = $node->insertNode($this->module, $pageid, $data, $lang);
		return $result;		
	}
	
	public function updateNodePageContent($pageid, $content, $lang = '')
	{
		$node = Business_Common_Node::getInstance();
		
		$nodepage = $node->getNode($this->module, $pageid, $lang);
		
		if($nodepage == null)
		{
			$data = array(				
				'title' => '',
				'shortcontent' => '',
				'content' => $content,
				'created' => date("Y-m-d H:i:s"),	
				'changed' => date("Y-m-d H:i:s")
			);
			$result = $node->insertNode($this->module, $pageid, $data, $lang);
		}
		else
		{
			$nodeid = $nodepage['nodeid'];
			$data = array(
				'title' => '',
				'shortcontent' => '',
				'content' => $content,						
				'changed' => date("Y-m-d H:i:s")
			);
			$result = $node->updateNode($nodeid, $this->module, $pageid, $data, $lang);
		}		
		return $result;
	}
	
	public function deleteNodePageContent($nodeid, $pageid, $lang = '')
	{
		$node = Business_Common_Node::getInstance();
		$result = $node->deleteNode($nodeid, $this->module, $pageid, $lang);
		return $result;		
	}
			
	public function getNodePageByPath($pagepath)
	{
		$cache = $this->getCacheInstance();
		$key = 	$this->getKeyByPagePath($pagepath);
		$result = $cache->getCache($key);
		if($result === FALSE)
		{
			$db = $this->getDbConnection();
			$sql = "SELECT * FROM " . $this->_tablename . " WHERE pagepath=?";
			$data = array($pagepath);
			$result = $db->fetchAll($sql, $data);			
			if(!is_null($result) && is_array($result) && count($result) > 0)
			{
				$result = $result[0];
				$cache->setCache($key, $result);
			}
		}
		return $result;
	}
	
	public function getListNodePage()
	{
		$cache = $this->getCacheInstance();
		$key = $this->getKeyList();
		$result = $cache->getCache($key);
		if($result === FALSE)
		{
			$db = $this->getDbConnection();
			$query = "SELECT * FROM " . $this->_tablename . " ORDER BY pagepath asc";
			$result = $db->fetchAll($query);
			if(!is_null($result) && is_array($result) && count($result) > 0)
			{
				$cache->setCache($key, $result);
			}
		}
		return $result;
	}
	
	public function addNodePage($pagepath, $layout = '', $status = 1)
	{
		$db = $this->getDbConnection();
		$data = array(
			'layout' => $layout,
			'pagepath' => $pagepath,
			'status' => $status
		);
		
		$result = $db->insert($this->_tablename, $data);
		return $result;
	}
	
	public function updateNodePage($pagepath, $data)
	{
		var_dump($page, $data);exit();
		$db = $this->getDbConnection();
		$where = array();
		$where[] = "pagepath='" . parent::adaptSQL($pagepath) . "'";
		
		$result = $db->update($this->_tablename, $data, $where);
		$cache = $this->getCacheInstance();
		$key = $this->getKeyList();
		$cache->deleteCache($key);
		$key = $this->getKeyByPagePath($pagepath);
		$cache->deleteCache($key);
		return $result;		
	}
	
	public function deleteNodePage($pagepath)
	{
		$db = $this->getDbConnection();
		$where = array();
		$where[] = "pagepath='" . parent::adaptSQL($pagepath) . "'";
		$result = $db->delete($this->_tablename, $where);
		if($result)
		{
			$cache = $this->getCacheInstance();
			$key = $this->getKeyByPagePath($pagepath);
			$cache->deleteCache($key);
			$key = $this->getKeyList();
			$cache->deleteCache($key);
		}
		return $result;
	}
		
}

?>
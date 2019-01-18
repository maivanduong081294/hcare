<?php

class Business_Common_Node extends Business_Abstract
{
	private $_tablename = 'node';
	
	const KEY_NODE = 'node.module-%s.delta-%s';
	const KEY_NODE_LANGUAGE = 'node.module-%s.delta-%s.lang-%s';
					 	
	private static $_instance = null; 
	
	function __construct()
	{
		
	}
	
	private function getKeyNode($module, $delta)
	{
		return sprintf(Business_Common_Node::KEY_NODE, $module, $delta);
	}
	
	private function getKeyNodeLang($module, $delta, $lang)
	{
		return sprintf(Business_Common_Node::KEY_NODE_LANGUAGE, $module, $delta, $lang);
	}
	
	/**
	 * get instance of Business_Common_Node
	 *
	 * @return Business_Common_Node
	 */
	public static function getInstance()
	{
		if(self::$_instance == null)
		{
			self::$_instance = new Business_Common_Node();
		}
		return self::$_instance;
	}	
	
	/**
	 * Get DB Connection
	 *
	 * @return Zend_db
	 */
	function getDbConnection()
	{		
		$db    	= Globals::getDbConnection('maindb');
		return $db;	
	}	
	
	public function getNode($module, $delta, $lang = '')
	{
				
		$key = $this->getKeyNode($module, $delta);
		$result = GlobalCache::getCache($key);
		if($result === FALSE)
		{
			$db = $this->getDbConnection();
			$query = "SELECT * FROM " . $this->_tablename . " WHERE module=? AND delta=?";
			$data = array($module,$delta);
			$result = $db->fetchAll($query, $data);
			if(!is_null($result) && is_array($result) && count($result) > 0)
			{
				GlobalCache::setCache($key,$result);			
			}
		}
		if($result == null || count($result) == 0 ) return null;
		
		for($i=0;$i<count($result);$i++)
		{
			if($result[$i]['language'] == $lang) return $result[$i];
		}
		return null;		
	}
	
	public function insertNode($module, $delta, $data, $lang = '')
	{
		$db = $this->getDbConnection();
		if(!array_key_exists('module', $data)) $data['module'] = $module;
		if(!array_key_exists('delta', $data)) $data['delta'] = $delta;
		
		if(array_key_exists('language', $data)) $lang = $data['language'];
		else $data['language'] = $lang;
		
		$result = $db->insert($this->_tablename, $data);
		
		if($result == true)
		{
			//delete cache
			$key = $this->getKeyNode($module, $delta);
			GlobalCache::deleteCache($key);			
		}
		return $result;
	}
	
	public function updateNode($nodeid, $module, $delta, $data, $lang = '')
	{
		$db = $this->getDbConnection();				
		if(array_key_exists('language', $data)) $lang = $data['language'];
		
		$where = array();		
		$where[] = "module='" . parent::adaptSQL($module) . "'";
		$where[] = "delta='" . parent::adaptSQL($delta) . "'";
		$result = $db->update($this->_tablename, $data, $where);
		if($result)
		{
			//delete cache
			$key = $this->getKeyNode($module, $delta);
			GlobalCache::deleteCache($key);			
		}
		return $result;
	}
	
	public function deleteNode($nodeid, $module, $delta, $lang = '')
	{
		$db = $this->getDbConnection();
						
		$where = array();
		$where[] = "nodeid='" . parent::adaptSQL($nodeid). "'";
		$where[] = "module='" . parent::adaptSQL($module) . "'";
		$where[] = "delta='" . parent::adaptSQL($delta) . "'";
		$where[] = "language='" . parent::adaptSQL($lang) . "'";
		
		$result = $db->delete($this->_tablename, $where);
		if($result)
		{
			//delete cache
			$key = $this->getKeyNode($module, $delta);
			GlobalCache::deleteCache($key);			
		}
		return $result;		
	}
	
	private function getNodeWithLang($module, $delta, $language)
	{
		$key = $this->getKeyNodeLang($module, $delta, $language);
		$result = GlobalCache::getCache($key);
		if($result === FALSE)
		{
			$db = $this->getDbConnection();
			$query = "SELECT * FROM " . $this->_tablename . " WHERE module=? AND delta=? AND language=?";
			$data = array($module, $delta, $language);
			$result = $db->fetchAll($query, $data);
			if(!is_null($result) && is_array($result) && count($result) > 0)
			{
				GlobalCache::setCache($key,$result);			
			}
		}
		if($result == null) return null;
		else
		{
			if(is_array($result) && count($result) > 0)
			{
				return $result[0];
			}
			else return null;	
		}
	}
	
}

?>
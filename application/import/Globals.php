<?php

class ServerLog
{
	static $log_path = null;
	static $logger = null;

	public static function initLog($path,$enable,$level)
	{
		if($enable)
		{
			self::$logger = new Zend_Log();
			self::$logger->addWriter(new Zend_Log_Writer_Stream($path));
			if(isset($level))
			{
				$filter = new Zend_Log_Filter_Priority($level);
				$logger->addFilter($filter);
			}

		}
	}

	public static function shutdown()
	{
		self::$logger = null;
	}

	public static function writeLog($content)
	{
		if(self::$logger != null)
		{
			$content .= "\n";
			self::$logger->info($content);
		}
	}

	public static function writeERR($error)
	{
		if(self::$logger != null)
		{
			$error .= "\n";
			self::$logger->err($error);
		}
	}

	public static function writeCRIT($error)
	{
		if(self::$logger != null)
		{
			$error .= "\n";
			self::$logger->crit($error);
		}
	}
}


class Log
{
	static $log_path = null;
	static $logger = null;
	
	public static function initLog($path,$enable,$level)
	{
		if($enable)
		{
			self::$logger = new Zend_Log();
			self::$logger->addWriter(new Zend_Log_Writer_Stream($path));
			if(isset($level))
			{
				$filter = new Zend_Log_Filter_Priority($level);
				$logger->addFilter($filter);				
			}
			
		}		
	}
	
	public static function shutdown()
	{
		self::$logger = null;
	}
	
	public static function writeLog($content)
	{
		if(self::$logger != null)
		{
			$content .= "\n";
			self::$logger->info($content);			
		}
	}
	
	public static function writeERR($error)
	{
		if(self::$logger != null)
		{
			$error .= "\n";
			self::$logger->err($error);
		}
	}
	
	public static function writeCRIT($error)
	{
		if(self::$logger != null)
		{
			$error .= "\n";
			self::$logger->crit($error);
		}
	}
}

class GlobalsDB
{
	private static $_db = null;
	public static $arrDB = array();
	
	public static function closeAllDbConnection()
	{
		if(is_array(self::$arrDB) && count(self::$arrDB) > 0)
       	{
       		foreach(self::$arrDB as $key => $value)
       		{
       			if($value != null) $value->closeConnection();
       			//unset(self::$arrDB[$key]);	       				       			
       		}
       	}
	}
	
	public static function getDbConnection($dbName, $state = false)
	{
		if(self::$_db != null && $state)
		{
			return self::$_db;
		}
		
		if(isset(self::$arrDB[$dbName]) && self::$arrDB[$dbName] != null)
		{				
			self::$_db = self::$arrDB[$dbName];			
			return self::$arrDB[$dbName];
		}		
		
		$config = Zend_Registry::get('configuration');
				
		$_db = Zend_Db::factory($config->$dbName);
		
		
		$debug = Globals::isDebug();
		
		if($debug == true)
		{
			$_db->getProfiler()->setEnabled(true);
		}
		
		
		$_db->query('SET NAMES UTF8');		
		self::$arrDB[$dbName] = $_db;		
		self::$_db = $_db;		
		return $_db;
	}		
}
 
 class Globals
 {
	/**
     * cache object
     * db object
     * @var object
     */
	private static $_cache 	= null;
	private static $_dbName = null;
	private static $_db 	= null;
	
	static public function getConfiguration()
	{
		return Zend_Registry::get('configuration');
	}	
	
	static public function getConfig($name = '')
	{
		if($name == '') return null;		
		$globalConfig 	= Zend_Registry::get('configuration');
		if(isset($globalConfig->$name))
		{
			return $globalConfig->$name;
		}
		else return null;
	}
	
	/*
	 * method for get db name
	 */
	static public function getDbName()
	{		
		return self::$_dbName;
	}	
		
	/*
	 * method for get db cache
	 * var $state : for check if db name is changed 
	 * $state = false = changed
	 */
	static public function getDbConnection($dbName, $state = false)
	{
		return GlobalsDB::getDbConnection($dbName, $state);
	}	
	
	static public function getDomainName()
	{
		$globalConfig 	= Zend_Registry::get('configuration');
		$domain_name = $globalConfig->domain->name;
		if ($domain_name != '')
			return $domain_name;
		else
			return 'http::/dev.emailing.test/';
	}
	
	
	static public function filterAlphabet($var)
	{  
		if(substr(ucfirst($var['displayname']), 0, 1) == 'C')
		{
			return $var;
		}
	}
	
	static public function getBaseUrl()
	{
		$configuration = Zend_Registry::get('configuration');
		if(isset($configuration->baseurl))
		{
			return $configuration->baseurl;
		}
		else return "/";
	}
	
	static public function variable_get($name, $default_value = null)
	{
		return Business_Common_Variables::variable_get($name, $default_value);
	}
	
	static public function variable_set($name, $value)
	{
		
	}
	
	static public function adaptData($value)
	{
		return str_replace('"', '&quot;',$value);
	}
	
	static public function readaptData($value)
	{
		return str_replace('&quot;', '"', $value);
	}	
		
	/**
	 * convert array result resource to array 
	 */
	static function resultToArray($result)
	{
		$arrTmp = array();
		foreach($result as $key => $value)
		{
			$arrTmp[$value['id']] = $value['name'];
		}
		return $arrTmp;
	}	
	
	static function isDebug()
	{
		return false;			
	}
}
 
/**
 * GlobalCache class
 * Description: cache object global for caching db, deleleting cache with key, subkey
 */ 
 
class GlobalCache
{
	private static $_main_array_name = "_main_array_";	
	private static $_local_cache = array();
	private static $_enable = null;
		
	private static function checkEnableCache()
	{
		if(is_null(self::$_enable))
		{
			$configuration = Zend_Registry::get("configuration");
			if(isset($configuration->caching->enable))
			{
				self::$_enable = $configuration->caching->enable;
			}
			else
			{
				self::$_enable = false;
			}
		}
		return self::$_enable;
	}
	
	/**
	 * get globalcache
	 *
	 * @param string $instance
	 * @return Maro_Cache_Interface
	 */
	public static function getCacheInstance($instance = 'default')
	{
		$cache = Maro_Cache_MemGlobalCache::getGlobalCache($instance);
		return $cache;
	}	
	
	public static function flushLocalCache($instance = 'default')
	{
		if(!self::checkEnableCache()) return;
		Maro_Cache_MemGlobalCache::flushLocalCache($instance);
	}
	
	public static function getMultiCache($keys, $instance = 'default')
	{
		if(!self::checkEnableCache()) return array();
		return Maro_Cache_MemGlobalCache::getMultiCache($keys, $instance);
	}
	
	public static function getCache($key, $instance = 'default')
	{
		if(!self::checkEnableCache()) return FALSE;
		return Maro_Cache_MemGlobalCache::getCache($key, $instance);
	}
	
	public static function deleteCache($key, $instance = 'default')
	{
		if(!self::checkEnableCache()) return array();
		Maro_Cache_MemGlobalCache::deleteCache($key, $instance);
	}	
	
	public static function setCache($key, $value, $instance = 'default', $expireTime = 0, $compress=0)
	{
		if(!self::checkEnableCache()) return;  		
		if(is_int($instance))
		{
			$expireTime = $instance;
			$_instance = '';			
			
			Maro_Cache_MemGlobalCache::setCache($key,$value,$_instance,$expireTime,$compress);
		}
		else
		{
			Maro_Cache_MemGlobalCache::setCache($key,$value,$instance,$expireTime,$compress);			
		}		
	}
}

class ProfileObj
{
	private $_id = '';
	private $_time = 0;
	private $_start = 0;
	private $_end = 0;
	
	function __construct($id,$start)
	{
		$this->_id = $id;
		$this->_start = $start;
	}
	
	function setEndTime($endtime)
	{
		$this->_end = $endtime;
		$diff = ($this->_end - $this->_start);
		$this->_time = $diff;
	}
	
	function getTime()
	{
		return $this->_time;				
	}
	
	function getID()
	{
		return $this->_id;
	}
	
	function getStartTime()
	{
		return $this->_start;
	}
	
	function getEndTime()
	{
		return $this->_end;		
	}	
	
}

class ProfilerLog
{
	private static $_array = array();
	private static $_totaltime = 0;
	
	public static function startLog($id)
	{
		if(Globals::isDebug() == false) return;
		if($id == '') return;
		$starttime = gettimeofday(true);		
		$obj_profile = new ProfileObj($id,$starttime);
		self::$_array[$id] = $obj_profile; 		
	}
	
	public static function endLog($id)
	{
		if(Globals::isDebug() == false) return;
		if($id == '') return;
		if(isset(self::$_array[$id]))
		{
			$obj_profile = self::$_array[$id];
			$endtime = gettimeofday(true); 		 	
			$obj_profile->setEndTime($endtime);
			self::$_totaltime += $obj_profile->getTime();
		} 		 
	}
	
	public static function dumpLog()
	{
		if(Globals::isDebug() == false) return;
		$_return = "";
		$count = 1;
		if(is_array(self::$_array) && count(self::$_array) > 0)
		{ 
			$_return = '<table style="border-collapse: collapse;" border="1" cellpadding="5" cellspacing="5"><tbody>'
				. '<tr><th colspan="3" bgcolor="#dddddd">Module Log Profiler</th></tr>'
				. '<tr><th width="50">No.</th><th>ID</th><th>Time elapsed in secs</th></tr>';
			
			$_return .= '<tr><td colspan="3" align="left"><b>Total time elapsed : ' . number_format(self::$_totaltime,9) . ' secs</td></tr>';
			
			foreach(self::$_array as $key => $value)
			{
				$obj_profiler = $value;			       
				$_return .= '<tr><td align="center">' . ($count++) . '</td><td align="left">' . $obj_profiler->getID() . '</td>'
					. '<td align="left">' . number_format($obj_profiler->getTime(),9) . "</td></tr>";
				
			}
			
			$_return .= "</table>";
		}
		return $_return;
	}	
}
?>
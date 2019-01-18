<?php
class Maro_Cache_MemGlobalCache
{
	static $_cache_array = array();
	static $_no_of_instance = null;
	private static $_default_instance = 'default';
	
	/**
	 * Enter description here...
	 *
	 * @param string $instance
	 * @return Maro_Cache
	 */
	
	public static function getGlobalCache($instance = 'default')
	{
		if(self::checkEnable())
		{			
			if(array_key_exists($instance, self::$_cache_array))
			{
				return self::$_cache_array[$instance];
			}
			else if(count(self::$_cache_array) > 0)
			{				
				return self::$_cache_array[self::$_default_instance];
			}
			else
			{								
				return null;
			}
		}
		else
		{			
			return null;
		}
	}
	
	public static function getAllProfilerData()
	{
		$output = "";
		if(self::checkEnable())
		{			
			if(count(self::$_cache_array) > 0)
			{				
				foreach(self::$_cache_array as $key => $value)
				{
					$output .= $value->getProfilerData($key);
					$output .= "<br><br>";
				}
			}
		}
		return $output;
	}
	
	private static function checkEnable()
	{
		self::initConfig(); 
		if(self::$_no_of_instance != null)
		{
			return true;
		}
		else return false;
	}
	
	private static function initConfig()
	{
		if(self::$_no_of_instance == null)
		{
			$config = Zend_Registry::get('configuration');
			
			$_cache_type = $config->caching->enable;
			//if($_cache_type == 'true') $_cache_type = 'memcache';//default;
									
			if(isset($config->cachingfarm))
			{
				if(isset($config->default_cache))
				{
					self::$_default_instance = $config->default_cache;
				}
				
				$list = $config->cachingfarm->list;				
				
				$arr = explode(',', $list);
								
				if(count($arr) > 0)
				{
					self::$_no_of_instance = count($arr);
					
					for($i=0;$i<self::$_no_of_instance;$i++)
					{
						$cache = $arr[$i];
						if(isset($config->cachingfarm->$cache))
						{
							$con =  $config->cachingfarm->$cache;
							
							if(isset($con->type)) $_cache_type = $con->type;
							else $_cache_type = 'memcache';//default
																																									
							$debug = Globals::isDebug();							
							if(empty($debug)) $debug = false;							
							$options = $con->toArray();
							//$memcache = new Maro_Cache_Adapter_MemCache($options, $debug,$cache);
							$cachename = '';
							$_cache = Maro_Cache_Cache::factory($_cache_type, $options, $debug, $cachename);								
							self::$_cache_array[$cache] = $_cache;
														
						}
					}
				}				
			}
		}
		
		
	}
	
	public static function flushLocalCache($instance = '')
	{
		$cache = Maro_Cache_MemGlobalCache::getGlobalCache($instance);
		if($cache != null) $cache->flushLocalCache();
	}
	
	 public static function getMultiCache($keys, $instance = '')
	 {
		$cache = Maro_Cache_MemGlobalCache::getGlobalCache($instance);
	 	$result = FALSE;
	 	if($cache != null)
	 	{
	 		$result = $cache->getMultiCache($keys);
	 	}
	 	return $result;
	 }
	
	public static function getCache($key, $instance = '')
	{		
		$cache = Maro_Cache_MemGlobalCache::getGlobalCache($instance);		
		$result = FALSE;
		if($cache != null)
		{	
			$result = $cache->getCache($key);			
		}
		return $result;
	}
	
	public static function deleteCache($key, $instance = '')
	{
		$cache = Maro_Cache_MemGlobalCache::getGlobalCache($instance);
		if($cache != null)
		{
			$cache->deleteCache($key);
		}
	}
	
	public static function setCache($key, $value, $instance = '', $expireTime = 0,  $compress=0)
	{
		$cache = Maro_Cache_MemGlobalCache::getGlobalCache($instance);
		if($cache != null)
		{
			$cache->setCache($key,$value,$expireTime,$compress);
		}
	}
	
	
	//public static function setCache($key, )
	
	
	
	
}
?>
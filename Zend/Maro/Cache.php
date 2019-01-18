<?php
abstract class Maro_Cache
{
	public function flushLocalCache()
	{		
	}
	
	public function getMultiCache($keys)
	{
		return null;
	}
	
	public function getCache($key)
	{
		return null;
	}
	
	public function deleteCache($key)
	{		
	}
	
	public function setCache($key, $value, $expireTime = 0, $compress=0)
	{
		
	}
	
	public function getProfilerData($cache_name = '')
	{
		return null;
	}
}
?>
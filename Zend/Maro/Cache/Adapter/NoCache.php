<?php
class Maro_Cache_Adapter_NoCache implements Maro_Cache_Interface
{	
	function __construct($options, $profiler_enable = false,$instance_name)
    {    	
    }
	public function flushLocalCache()
	{
		return;
	}
	public function getMultiCache($keys)
	{
		return FALSE;
	}
	public function getCache($key)
	{
		return FALSE;
	}
	public function deleteCache($key)
	{
		
	}
	public function setCache($key, $value, $expireTime = 0, $compress=0)
	{
		return;
	}
	public function getProfilerData($cache_name = '')
	{
		return '';
	}
        public function flushAll(){
            return;
        }
}
?>
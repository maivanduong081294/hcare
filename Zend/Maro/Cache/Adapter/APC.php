<?php

class Maro_Cache_Adapter_APC implements Maro_Cache_Interface
{
	private $_instancename = '';
	private $_profiler = null;
    private $_profiler_enable = false;
	
	function __construct($profiler_enable = false,$instance_name)
	{
		$this->_instancename = $instance_name;
		if($profiler_enable)
    	{
    		$this->_profiler_enable = true;
    		$this->_profiler = new Zing_Cache_ZingCacheProfiler();
    	}
	}
	
	
	public function flushLocalCache()
	{
		return;
	}
	public function getMultiCache($keys)
	{
	 	$return = array();
	 	
	 	if(is_array($keys) && count($keys) > 0)
	 	{
	 		for($i=0;$i<count($keys);$i++)
	 		{
	 			$key = $keys[$i];
	 			$result = $this->getCache($key);
	 			$return[$key] = $result; 
	 		}
	 	}
		return $return;
	}
	
	public function getCache($key)
	{
	 	$starttime = gettimeofday(true);
	 	$result = apc_fetch($key);
	 	$endtime = gettimeofday(true);
	 	if($this->_profiler_enable) $this->_profiler->pushToCacheProfiler($key,$result,$starttime,$endtime);
	 	return $result;
	}	
	
	public function deleteCache($key)
	{
	 	apc_delete($key);
	}
	
	public function setCache($key, $value, $expireTime = 0, $compress=0)
	{	
		apc_store($key,$value,$expireTime);	
	}
	
	public function getProfilerData($cache_name = '')
	{
		$output = "";
    	if($this->_profiler_enable)
    	{
    		$cache_name .= " " . $this->_server . ":" . $this->_port;
    		$output = $this->_profiler->getProfilerData($cache_name . " (APC)");
    	}
    	return $output; 	
	}
        
        
        public function flushAll(){
            return ;
        }
}




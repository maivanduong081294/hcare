<?php

class Maro_Cache_Adapter_MemCache implements Maro_Cache_Interface
{		
    private $_local_cache = array();
    private $_key_misses = array();
    
    private $_server = '';
    private $_port = '';
    private $_keyprefix = '';
    private $_memcache = null;    
    private $_profiler = null;
    private $_profiler_enable = false;
    private $_instancename = '';
    private $_enable_local_cache = true;
       
    
    function __construct($options, $profiler_enable = false,$instance_name)
    {    	
    	$server= $options['host'];
    	$port= $options['port'];

    	if(isset($options['enable_local']))
    	{
    		$this->_enable_local_cache = $options['enable_local'];
    	}
    	
    	$this->_keyprefix = $options['keyprefix'];
    	
    	$this->_memcache = new ZingMemcache($server, $port);
    	$this->_server = $server;
    	$this->_port = $port;    	
    	$this->_instancename = $instance_name;
    	
    	if($profiler_enable)
    	{
    		$this->_profiler_enable = true;
    		$this->_profiler = new ZingCacheProfiler();
    	}
    }
    
    public function flushLocalCache()
    {
    	foreach ($this->_local_cache as $key => &$value)
		{
		   	unset($this->_local_cache[$key]);	
		}   	
    }
       
    public function getMultiCache($keys)
    {
    	if($this->_keyprefix != '')
    	{
    		if(is_array($keys) && count($keys) > 0)
    		{
    			for($i=0;$i<count($keys);$i++)
    			{
    				$keys[$i] = $this->_buidKey($keys[$i]);
    			}
    		}
    	}
    	
	   	$cache = $this->_memcache;
	   	$starttime = gettimeofday(true);
	   	$result = $cache->loadMulti($keys);
	   	$endtime = gettimeofday(true);
	   	
	   	$return = array();
	       
	   	if($result != null && is_array($result))
	   	{
	   		$time_average = $endtime - $starttime;
	   		$time_average = $time_average / count($result);
	   		foreach ($result as $key => $value)
		    {
		    	if($this->_profiler_enable) $this->_profiler->pushToCacheProfiler($key . " (multikey)",$value,0,$time_average);
			 	$value = json_decode($value, true);
			 	$return[$this->_stripKey($key)] = $value;
			}								       
	   	}
	    
	   	unset($result);
	   	return $return;
    }
       
    public function getCache($key)
    {    	
    	$cache = $this->_memcache;
	    if($cache == null) return FALSE;
    	
    	$key = $this->_buidKey($key);
	  	if($this->_checkKeyMisses($key)) return FALSE;
	  	
	    if($this->_enable_local_cache)
	    {
		  	if(is_array($this->_local_cache) && isset($this->_local_cache[$key]))
		    {		       
				$result = $this->_local_cache[$key];
			    return $result;
		    }
	    }
	    	     		
	    $origin_key = $key;		

	    $starttime = gettimeofday(true);
	    
	    $result = $cache->load($key);
	    
	    $endtime = gettimeofday(true);
	       
	    if($result === FALSE)
	    {	       		
			$this->_addKeyMisses($origin_key);
	    } 		
	    if($this->_profiler_enable) $this->_profiler->pushToCacheProfiler($origin_key,$result,$starttime,$endtime);
	    if($result != null)
	    {	    	
		    $result = json_decode($result, true); 			 			
	    }
		   
	    if($this->_enable_local_cache) $this->_local_cache[$key] = $result; 	
	       
	    return $result;
    }
       
    public function deleteCache($key)
    {    	
		$cache = $this->_memcache;
	    if($cache == null) return null;
	    $key = $this->_buidKey($key);
	    $cache->remove($key);

	    if($this->_enable_local_cache)
	    {
		    if(is_array($this->_local_cache) && isset($this->_local_cache[$key]))
		    {
				unset($this->_local_cache[$key]);			
		    }
	    }
    }   
       
    public function setCache($key, $value, $expireTime = 0, $compress=0)
    {       		
	   	//if(is_null($value)) return;	    
	   	$cache = $this->_memcache;
	   	$key = $this->_buidKey($key);
	   	$this->_removeKeyMisses($key);
	   	$value = json_encode($value);
	   	$cache->save($value, $key, $compress, $expireTime);
	   	
	   	if($this->_enable_local_cache)
	   	{
	       	if(is_array($this->_local_cache) && isset($this->_local_cache[$key]))
		   	{
		   		unset($this->_local_cache[$key]);			
		    }
	   	}	    	
    }
    
    public function getProfilerData($cache_name = '')
    {
    	$output = "";
    	if($this->_profiler_enable)
    	{
    		$cache_name .= " " . $this->_server . ":" . $this->_port;
    		$output = $this->_profiler->getProfilerData($cache_name);
    	}
    	return $output;
    }
    
    public function flushAll(){
        $this->_memcache->clean();
    }
	
    
    //private function ////////
    
    private function _stripKey($key)
    {
    	if($this->_keyprefix == '') return $key;
    	return str_replace($this->_keyprefix, '', $key);
    }
    
    private function _buidKey($key)
    {
    	if($this->_keyprefix == '') return $key;
    	return $this->_keyprefix . $key; 
    }
    
	private function _checkKeyMisses($key)
    {    	
		if(isset($this->_key_misses[$key])) return true;
	    else return false;		
    }
       
    private function _addKeyMisses($key)
    {		
	    if(!$this->_checkKeyMisses($key))
	    $this->_key_misses[$key] = "1";		
    }
       
    private function _removeKeyMisses($key)
    {
	    if($this->_checkKeyMisses($key))
	    {
	    	unset($this->_key_misses[$key]);
	    }
    }    
    
}

class ZingMemcache
{
       static $_mymemcache = null;
       private $_memcache_obj = null;
             
              
       static function factory($server='127.0.0.1',$port='11211')
       {
	       if(self::$_mymemcache == null)
	       {
		       self::$_mymemcache = new ZingMemcache($server,$port);							
	       }
	       return self::$_mymemcache;
       }
       
       function __construct($server='127.0.0.1',$port='11211')
       {
	       $this->_memcache_obj = new Memcache();
	       $this->_memcache_obj->addServer($server, $port, true);
	       memcache_set_compress_threshold($this->_memcache_obj, 1024);	       
       }
       
       public function load($key)
       {       		
	       if(!$this->checkKeyValid($key)) return null;	       
	       $result = $this->_memcache_obj->get($key);	       
	       return $result;
       }
       
       public function loadMulti($keys)
       {			
	       if(!is_array($keys)) return null;		
	       $return = $this->_memcache_obj->get($keys);		
	       return $return;		
       }
       
       public function remove($key)
       {
	       if(!$this->checkKeyValid($key)) return;
	       $kq = $this->_memcache_obj->delete($key);
	       return $kq;
       }	
       
       public function save($value, $key, $compress=0, $expire=0)
       {
	       if(!$this->checkKeyValid($key)) return;		
	       return $this->_memcache_obj->set($key,$value, $compress, $expire);	
       }
       
       public function clean()
       {		
	       return $this->_memcache_obj->flush();
       }
       
       private function checkKeyValid($key)
       {
	       if(empty($key)) return false;
	       else return true;	       
       }       
}

class ZingCacheProfiler
{       
       private $_key_profiler_hits = array();
       private $_key_profiler_misses = array();
       public $_total_misses = 0;
       public $_total_hits = 0;
       public $_total_time = 0;
       
       function __construct()
       {
       		
       }	   

       public function getCacheProfiles()
       {
	       return array_merge($this->_key_profiler_hits,$this->_key_profiler_misses);
       }
       
       public function getTotalMissesCache()
       {
	       return $this->_total_misses;	
       }
       
       public function getTotalHitsCache()
       {
	       return $this->_total_hits;
       }
       
       public function getTotalEllapsedTime()
       {
	       return $this->_total_time;
       }
       
       public function getPercentMissesCache()
       {	       
		    $total = $this->_total_hits + $this->_total_misses;
			if($total == 0) return 0;
			$percent = $this->_total_misses / $total;
			return $percent * 100; 	
       }
       
       public function getPercentHitsCache()
       {	       
			$total = $this->_total_hits + $this->_total_misses;
			if($total == 0) return 0;
			$percent = $this->_total_hits / $total;
			return $percent * 100; 	
       }       
       
       public function pushToCacheProfiler($key, $result, $starttime, $endtime)
       {
       	   $debug = Globals::isDebug();
       	   if($debug == true)
       	   {
       	   	   $_cache_result = new ResultCache();
		       $_cache_result->key = $key;
		       $diff = ($endtime - $starttime);
		       $this->_total_time += $diff;
		       $_cache_result->ellapsedtime = $diff;		
		       if($result == null)
		       {			
			       $this->_total_misses++;
			       $_cache_result->result = 0;
			       $this->_key_profiler_misses[] = $_cache_result;
		       }
		       else 
		       {
			       $this->_total_hits++;
			       $_cache_result->result = 1;
			       $this->_key_profiler_hits[] = $_cache_result;
		       }
       	   }	       
       }	
       
       public function getProfilerData($cache_name = '')
       {
			$print = '';
			$print .= '<br><br><br><table border=1 cellspacing="5" cellpadding="5" style="border-collapse:collapse">'
				. "<tr><th colspan=4 bgcolor='#dddddd'>Caching Profiler for instance " . $cache_name . "</th></tr>"
					. "<tr><th width=50>No.</th><th>Key</th><th>Result</th><th>Time elapsed in secs</th>";
					
			
			$print .= "<tr><td colspan='4' align='left'><b>total hits : " . $this->getTotalHitsCache() . "</b></td></tr>";
			$print .= "<tr><td colspan='4' align='left'><b>total percent hits : " . $this->getPercentHitsCache(). " %</b></td></tr>";
			$print .= "<tr><td colspan='4' align='left'><b>total misses : " . $this->getTotalMissesCache() . "</tr>";
			$print .= "<tr><td colspan='4' align='left'><b>total percent misses : " . $this->getPercentMissesCache() . " %</b></td></tr>";
			$print .= "<tr><td colspan='4' align='left'><b>total ellapsed time by cache : " . number_format($this->getTotalEllapsedTime(),9) . " secs</b></td></tr>";
			
			$cache_profile = $this->getCacheProfiles();
			for($i=0;$i<count($cache_profile);$i++)
			{
				$profiler = $cache_profile[$i];
				$print .= "<tr><td>" . ($i+1) . "</td><td>" . $profiler->key . "</td><td>" 
						. ($profiler->result == "1" ? "<font color='green'>hit cache</font>" : "<font color='red'>miss cache</font>") 
						. "</td><td>" . number_format($profiler->ellapsedtime,9) . "</td></tr>";				 
			}
			$print .= "</table>";
			return $print;
       }       
}

class ResultCache
{
	public $key = 0;
	public $result = 0;
	public $ellapsedtime = 0; 	
}

?>
<?php

class Maro_Cache_Cache
{
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $type
	 * @return Maro_Cache_Interface
	 */
	public static function factory($type = 'memcache',$options, $debug, $cachename)
	{		
		if($type == 'memcache')
		{
			return new Maro_Cache_Adapter_MemCache($options, $debug, $cachename);
		}
		else if($type == 'nocache')
		{			
			return new Maro_Cache_Adapter_NoCache($options, $debug, $cachename);
		}
		else if($type == 'apc')
		{
			return new Maro_Cache_Adapter_APC($debug, $cachename);
		}
		else
		{
			return new Maro_Cache_Adapter_MemCache($options, $debug, $cachename);
		}
		
	}
}

?>
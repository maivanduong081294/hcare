<?php
class Business_Ws_NoModule extends Business_Abstract
{
private static $_instance = null; 
	
	function __construct()
	{			
	}
	
	//public static function
	/**
	 * get instance of Business_Ws_DownloadModule
	 *
	 * @return Business_Ws_DownloadModule
	 */
	public static function getInstance()
	{
		if(self::$_instance == null)
		{
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	public static function mi_buildUrl($lang,$delta,$itemid)
	{		
		return "#";
	}
	
	public static function mi_getDetail($lang,$delta)
	{
		return array();
	}
	
	public static function mi_getAltTitle($lang,$delta)
	{		
		return "";
	}
	
	public static function mi_getList($lang,$itemid = null)
	{		
		$return = array();
		
		$return[''] = "N/A";
		
		return $return;
	}
}
?>
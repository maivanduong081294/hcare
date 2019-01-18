<?php
class Business_Auth_GlobalAuth
{
	protected static $_store_key = "user_auth";
	
	public static function isLogged()
	{
		$storage = Auth_Storage_Session::getInstance(self::$_store_key);		
		$adapter = Business_Auth_Adapter::getInstance();
		$auth = Business_Auth_Auth::getInstance($adapter,$storage);
		return $auth->isLogged();		
	}
	
	public static function doLogin($username, $password)
	{            
		$storage = Auth_Storage_Session::getInstance(self::$_store_key);		
		$adapter = Business_Auth_Adapter::getInstance();
		$auth = Business_Auth_Auth::getInstance($adapter,$storage);
		return $auth->doLogin($username,$password);
	}
	
	public static function doLogout()
	{
		$storage = Auth_Storage_Session::getInstance(self::$_store_key);
		$adapter = Business_Auth_Adapter::getInstance();
		$auth = Business_Auth_Auth::getInstance($adapter,$storage);
		return $auth->doLogout();
	}
	
	public static function getIdentity()
	{
		$storage = Auth_Storage_Session::getInstance(self::$_store_key);
		$adapter = Business_Auth_Adapter::getInstance();
		$auth = Business_Auth_Auth::getInstance($adapter,$storage);
                
		return $auth->getIdentity();
	}
}
?>
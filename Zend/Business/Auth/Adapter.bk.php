<?php
class Business_Auth_Adapter implements Auth_Adapter_Interface
{
	
	protected static $_instance = null;
	
	/**
	 * 
	 *
	 * @return Business_LavieAuth_Adapter
	 */
	public static function getInstance()
	{
		if(null == self::$_instance)
		{
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	//return 0 : user not exits
	//return 1 : login sucessful
	//return -1 : user is banned
	public function doLogin($username, $password)
	{
		$password = md5($password);
		$userModel = Business_Ws_UserModule::getInstance();
		$result = $userModel->getUserByUsername($username);
                
		if($result == null || !is_array($result)) return 0; //login failed
		
		if(!isset($result['pass']) || $result['pass'] != $password) return 0; //login failed
		
//		if($result['isban'] == 1) return -1; //user is banned
		//if ((int)$result['status'] == 0) return -1; // locked
                
		return 1; //login successful
	}	
}
?>
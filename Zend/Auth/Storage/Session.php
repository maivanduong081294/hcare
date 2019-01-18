<?php
class Auth_Storage_Session implements Auth_Storage_Interface 
{
	private $_key = "AUTH"; 
	
	protected static $_instances = array();
			
	/**
	 * Enter description here...
	 *
	 * @return Auth_Storage_Interface
	 */
	static public function getInstance($key = '')
	{		
		if(!isset(self::$_instances[$key]))
		{
			self::$_instances[$key] = new Auth_Storage_Session($key);
		}
		return self::$_instances[$key];
	}
	
	public function __construct($key = '')
	{
		session_start();
		if($key != '') $this->_key = $key;
	}
	
	public function read()
	{
		if(isset($_SESSION[$this->_key])) return $_SESSION[$this->_key];
		else return null;
	}
	
	public function write($data)
	{
		$_SESSION[$this->_key] = $data;
	}
	
	public function flush()
	{
		if(isset($_SESSION[$this->_key])) unset($_SESSION[$this->_key]);
	}
}
?>
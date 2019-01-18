<?php
class Language_Language implements Language_Interface 
{
	private $_cookie_name = 'lang';
	
	static $_instance = null;
	
	public function __construct()
	{		
	}
	
	/**
	 * Enter description here...
	 *
	 * @return Language_Language
	 */
	public static function getInstance()
	{
		if(self::$_instance == null)
		{
			self::$_instance = new self();			
		}
		return self::$_instance;
	}
	
	public function getLang($default = 'en')
	{
		//read from cookie
		if(isset($_COOKIE[$this->_cookie_name])) return $_COOKIE[$this->_cookie_name];
		else return $default;
	}
	
	public function setLang($lang)
	{
		$_COOKIE[$this->_cookie_name] = $lang;
		//setcookie($this->_cookie_name, $lang, null, '/');
	}
}
?>
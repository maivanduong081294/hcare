<?php

class Business_Session_PHPSession implements Business_Session_Interface {

	protected static $_instance = null;

	function  __construct() {
		
	}

	/**
	 *
	 * @return Business_Session_Interface
	 */
	public static function getInstance() {
		session_start();
		if(self::$_instance == null) self::$_instance = new self();
		return self::$_instance;
	}

    public function get($name, $default_value = null) {
		if(!isset($_SESSION[$name])) return $default_value;
		else return $_SESSION[$name];
	}

	public function set($name, $value) {
		$_SESSION[$name] = $value;
	}

	public function delete($name) {
		if(isset($_SESSION[$name])) unset($_SESSION[$name]);
	}

	public static function getSession($name, $default_value) {
		$_session = self::getInstance();
		return $_session->get($name, $default_value);
	}

	public static function setSession($name, $value) {
		$_session = self::getInstance();
		$_session->set($name, $value);
	}

	public static function deleteSession($name) {
		$_session = self::getInstance();
		$_session->delete($name);
	}
}
?>

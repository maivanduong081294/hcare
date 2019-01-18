<?php

interface Business_Session_Interface {
	public function get($name, $default_value = null);
	public function set($name, $value);
	public function delete($name);
}
?>
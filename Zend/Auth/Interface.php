<?php

interface Auth_Interface
{
	
	public function doLogin($username, $password);
	public function doLogout();
	public function isLogged();
	public function getIdentity();
}

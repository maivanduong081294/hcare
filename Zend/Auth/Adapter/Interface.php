<?php

interface  Auth_Adapter_Interface
{
	//return 0 : user not exits
	//return 1 : login sucessful
	//return -1 : user is banned
	public function doLogin($username, $password);	
}
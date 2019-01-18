<?php

interface Auth_Storage_Interface
{
	public function read();
	public function write($data);
	public function flush();
}
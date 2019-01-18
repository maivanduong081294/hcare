<?php

interface Maro_Cache_Interface
{
	public function flushLocalCache();
	public function getMultiCache($keys);
	public function getCache($key);
	public function deleteCache($key);
	public function setCache($key, $value, $expireTime = 0, $compress=0);
	public function getProfilerData($cache_name = '');
	public function flushAll();
	 
}
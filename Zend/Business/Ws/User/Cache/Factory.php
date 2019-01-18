<?php
// ws_user = .vc_provider_accout : ten bang
// Business_Ws_ = .Business_EVoucher_ : duong dan den lop business
// User = .ProviderAccount : ten goi nho cua table 
// userid = .pid : khoa chinh
// Userid = .Pid : khoa chinh viet in dunug trong ten ham
// email = .username : lay noi dung theo ten voi key username
// maindb = .maindb

	class Business_Ws_User_Cache_Factory
	{
		/**
		 * @return  Business_Ws_User_Cache_Interface
		 */
		public static function factory($type = 'memcache')
		{
			switch($type)
			{
				case 'memcache': // get memcached instance
					
					return Business_Ws_User_Cache_Memcache::getInstance();
					break;
				default: // get memcached instance
					return Business_Ws_User_Cache_Memcache::getInstance();
					break;
			}
		}
	}
?>
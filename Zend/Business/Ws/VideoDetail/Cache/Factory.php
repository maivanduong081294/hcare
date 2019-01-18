<?php
// ws_videodetail = .vc_provider_accout : ten bang
// Business_Ws_ = .Business_EVoucher_ : duong dan den lop business
// VideoDetail = .ProviderAccount : ten goi nho cua table 
// videoid = .pid : khoa chinh
// Videoid = .Pid : khoa chinh viet in dunug trong ten ham
// userid = .username : lay noi dung theo ten voi key username
// maindb = .maindb

	class Business_Ws_VideoDetail_Cache_Factory
	{
		/**
		 * @return  Business_Ws_VideoDetail_Cache_Interface
		 */
		public static function factory($type = 'memcache')
		{
			switch($type)
			{
				case 'memcache': // get memcached instance
					
					return Business_Ws_VideoDetail_Cache_Memcache::getInstance();
					break;
				default: // get memcached instance
					return Business_Ws_VideoDetail_Cache_Memcache::getInstance();
					break;
			}
		}
	}
?>
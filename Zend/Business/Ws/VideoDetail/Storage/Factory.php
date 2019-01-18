<?php
// ws_videodetail = vc_provider_accout : ten bang
// Business_Ws_ = Business_EVoucher_ : duong dan den lop business
// VideoDetail = ProviderAccount : ten goi nho cua table 
// videoid = .pid : khoa chinh
// Videoid = .Pid : khoa chinh viet in dunug trong ten ham
// userid = username : lay noi dung theo ten voi key username

	class Business_Ws_VideoDetail_Storage_Factory 
	{
		/**
		 * Enter description here...
		 *
		 * 
		 * @return Business_Ws_VideoDetail_Storage_Interface
		 */
		public static function factory($type = 'mysql')
		{
			switch($type)
			{
				case 'mysql':
					return Business_Ws_VideoDetail_Storage_MySQL::getInstance();
					break;
				default:
					return Business_Ws_VideoDetail_Storage_MySQL::getInstance();
					break;
			}
		}
	}
?>
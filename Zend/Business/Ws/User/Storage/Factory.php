<?php
// ws_user = vc_provider_accout : ten bang
// Business_Ws_ = Business_EVoucher_ : duong dan den lop business
// User = ProviderAccount : ten goi nho cua table 
// userid = .pid : khoa chinh
// Userid = .Pid : khoa chinh viet in dunug trong ten ham
// email = username : lay noi dung theo ten voi key username

	class Business_Ws_User_Storage_Factory 
	{
		/**
		 * Enter description here...
		 *
		 * 
		 * @return Business_Ws_User_Storage_Interface
		 */
		public static function factory($type = 'mysql')
		{
			switch($type)
			{
				case 'mysql':
					return Business_Ws_User_Storage_MySQL::getInstance();
					break;
				default:
					return Business_Ws_User_Storage_MySQL::getInstance();
					break;
			}
		}
	}
?>
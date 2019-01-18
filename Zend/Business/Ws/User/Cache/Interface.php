<?php
// ws_user = .vc_provider_accout : ten bang
// Business_Ws_ = .Business_EVoucher_ : duong dan den lop business
// User = .ProviderAccount : ten goi nho cua table 
// userid = .pid : khoa chinh
// Userid = .Pid : khoa chinh viet in dunug trong ten ham
// email = .username : lay noi dung theo ten voi key username
// maindb = .maindb


	interface Business_Ws_User_Cache_Interface
	{
		public function getUserList();
		public function setUserList($result);
		
		public function getUserByUserid($userid);
		public function setUserByUserid($userid,$result);
		
		public function getUserByEmail($email);
		public function setUserByEmail($email,$result);

                public function getUserByUsername($username);
		public function setUserByUsername($username,$result);
		
		public function getMultiUserByUserid($userids = array());
		
//1		public function deleteAllCache($userid = 0);
		public function deleteAllCache($userid = 0, $email = '');
		
		public function getKeyListPaging();
		
		public function getCacheInstance();
	}
?>
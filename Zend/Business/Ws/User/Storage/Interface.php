<?php
// ws_user = vc_provider_accout : ten bang
// Business_Ws_ = Business_EVoucher_ : duong dan den lop business
// User = ProviderAccount : ten goi nho cua table 
// userid = .pid : khoa chinh
// Userid = .Pid : khoa chinh viet in dunug trong ten ham
// email = username : lay noi dung theo ten voi key username


	interface Business_Ws_User_Storage_Interface
	{
		public function getUserList();
		public function getUserByUserid($userid);
		
		public function getUserByEmail($email);
                public function getUserByUsername($username);
                public function getUserByDate($day, $month, $year);
                
		public function insertUser($data);
		public function deleteUser($userid);
		public function updateUser($userid, $data);
		
		public function getUserListWithPaging($offset = 0, $records = 20);                

	}
?>

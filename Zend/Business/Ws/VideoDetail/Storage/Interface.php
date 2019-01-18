<?php
// ws_videodetail = vc_provider_accout : ten bang
// Business_Ws_ = Business_EVoucher_ : duong dan den lop business
// VideoDetail = ProviderAccount : ten goi nho cua table 
// videoid = .pid : khoa chinh
// Videoid = .Pid : khoa chinh viet in dunug trong ten ham
// userid = username : lay noi dung theo ten voi key username


	interface Business_Ws_VideoDetail_Storage_Interface
	{
		public function getVideoDetailList();
		public function getVideoDetailByVideoid($videoid);
		
		public function getVideoDetailByUserid($userid);
		
		public function insertVideoDetail($data);
		public function deleteVideoDetail($videoid);
		public function updateVideoDetail($videoid, $data);
		
		public function getVideoDetailListWithPaging($offset = 0, $records = 20);
                public function getVideoDetailListWithPagingByuserid($userid, $offset = 0, $records = 20);
	}
?>
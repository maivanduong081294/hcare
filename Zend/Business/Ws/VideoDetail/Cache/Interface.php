<?php
// ws_videodetail = .vc_provider_accout : ten bang
// Business_Ws_ = .Business_EVoucher_ : duong dan den lop business
// VideoDetail = .ProviderAccount : ten goi nho cua table 
// videoid = .pid : khoa chinh
// Videoid = .Pid : khoa chinh viet in dunug trong ten ham
// userid = .username : lay noi dung theo ten voi key username
// maindb = .maindb


	interface Business_Ws_VideoDetail_Cache_Interface
	{
		public function getVideoDetailList();
		public function setVideoDetailList($result);
		
		public function getVideoDetailByVideoid($videoid);
		public function setVideoDetailByVideoid($videoid,$result);
		
		public function getVideoDetailByUserid($userid);
		public function setVideoDetailByUserid($userid,$result);
		
		public function getMultiVideoDetailByVideoid($videoids = array());
		
//1		public function deleteAllCache($videoid = 0);
		public function deleteAllCache($videoid = 0, $userid = '');
		
		public function getKeyListPaging();
                public function getKeyListPagingByuserid($userid);
		
		public function getCacheInstance();
	}
?>
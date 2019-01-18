<?php

class Business_HelpersNew_GetBanner {

    private static $_instance = null;

    // module news to store

    function __construct() {
        
    }

    /**
     * get instance of Business_HelpersNew_GetBanner
     *
     * @return Business_HelpersNew_GetBanner
     */
    public static function getInstance() {
        if (self::$_instance == null) {
            self::$_instance = new Business_HelpersNew_GetBanner();
        }
        return self::$_instance;
    }
    
    public static function getBannerPcTrangChuTextBox() {
        //======================get banner Trang Chủ Middle mới
        $_banners = Business_WsNew_Banners::getInstance();
         
        $bannerPos = 13;
        $list = $_banners->getListBanner($bannerPos);
        return $list;
    }
    
    
        public static function getBannerPcTrangChuRightDoc() {
        //======================get banner Trang Chủ Middle mới
        $_banners = Business_WsNew_Banners::getInstance();
         
        $bannerPos = 25;
        $list = $_banners->getListBanner($bannerPos);
        $list = Business_Helpers_Banner::updateBannerList($list);
        return $list;
    }
    
    
    public static function getBannerPcTrangChuMiddle() {
        //======================get banner Trang Chủ Middle mới
        $_banners = Business_WsNew_Banners::getInstance();
         
        $bannerPos = 23;
        $list = $_banners->getListBanner($bannerPos);
        $list = Business_Helpers_Banner::updateBannerList($list);
        return $list;
    }

    public static function getBannerBHSC() {
        //======================get banner Trang Chủ Middle mới
        $_banners = Business_WsNew_Banners::getInstance();
         
        $bannerPos = 42;
        $list = $_banners->getListBanner($bannerPos);
        $list = Business_Helpers_Banner::updateBannerList($list);
        return $list;
    }
    
	public static function getBannerPcTrangChuRight($bannerPos = 22) {
	    //======================get banner Trang Chủ Top Right mới
	    $_banners = Business_WsNew_Banners::getInstance();
	    
	  
	    $list = $_banners->getListBanner($bannerPos);
	    $list = Business_Helpers_Banner::updateBannerList($list);	        
        return $list;
    }
    
    public static function getBannerPopupMobile() {
        //======================get  Banner Trang Chủ Popup mới
        $_banners = Business_WsNew_Banners::getInstance();
         
        $bannerPos = 28;
        $list = $_banners->getListBanner($bannerPos);
        $list = Business_Helpers_Banner::updateBannerList($list);
        return $list;
    }
    
        public static function getBannerPopupPC() {
        //======================get  Banner Trang Chủ Popup mới
        $_banners = Business_WsNew_Banners::getInstance();
         
        $bannerPos = 27;
        $list = $_banners->getListBanner($bannerPos);
        $list = Business_Helpers_Banner::updateBannerList($list);
        return $list;
    }

    public static function getBanner($bannerPos) {
        //======================get banner Trang Chủ Top mới
        $_banners = Business_WsNew_Banners::getInstance();         
        $list = $_banners->getListBanner($bannerPos);
        $list = Business_Helpers_Banner::updateBannerList($list);
        return $list;
    }
    
    public static function getBannerPcTrangChu($bannerPos=21) {
        //======================get banner Trang Chủ Top mới
        $_banners = Business_WsNew_Banners::getInstance();         
        $list = $_banners->getListBanner($bannerPos);
        $list = Business_Helpers_Banner::updateBannerList($list);
        return $list;
    }
    
    public static function getBannerDienthoai() {
        //====================== Banner Danh mục sản phẩm Top mới
        $_banners = Business_WsNew_Banners::getInstance();
         
        $bannerPos = 36;     
        $list = $_banners->getListBanner($bannerPos);
        $list = Business_Helpers_Banner::updateBannerList($list);
        return $list;
    }


    public static function getBannerPhukien() {
        //====================== Banner Danh mục sản phẩm Top mới
        $_banners = Business_WsNew_Banners::getInstance();
         
        $bannerPos = 24;     
        $list = $_banners->getListBanner($bannerPos);
        $list = Business_Helpers_Banner::updateBannerList($list);
        return $list;
    }
    public static function getBannerSim() {
        //====================== Banner Danh mục sản phẩm Top mới
        $_banners = Business_WsNew_Banners::getInstance();
         
        $bannerPos = 41;     
        $list = $_banners->getListBanner($bannerPos);
        $list = Business_Helpers_Banner::updateBannerList($list);
        return $list;
    }



        public static function getBannerCateid($cateid) {
        //====================== Banner Danh mục cateid
        $_banners = Business_WsNew_Banners::getInstance();   
        $list = $_banners->getListBannerCateid($cateid);
        $list = Business_Helpers_Banner::updateBannerList($list);
        return $list;
    }

}

?>

<?php

class Business_Helpers_Banner {

    private static $_instance = null;
    public $_session = null;
    public $_userinfo = null;

    // module news to store

    function __construct() {
        $this->_session = new Zend_Session_Namespace('session_app');
        $this->_userinfo = $this->_session->userinfo;
    }

    public static function updateBannerList($banner) {
        foreach($banner as &$b){
            $b["img"] = Globals::getStaticUrl() . "uploads/banners/" . $b["img"];
        }
        return $banner;
    }
}

?>

<?php

class Business_Helpers_Sim {

    private static $_instance = null;
    public $_session = null;
    public $_userinfo = null;

    // module news to store

    function __construct() {
        $this->_session = new Zend_Session_Namespace('session_app');
        $this->_userinfo = $this->_session->userinfo;
    }
    public static function getPathSaleonline2($createdate) {
        $basePath = "/uploads/pack";
        $ts = strtotime($createdate);
        $year = date("Y", $ts);
        $month = date("m",$ts);
        $day = date("d",$ts);
       
        return sprintf("%s/%s/%s/%s", $basePath, $year, $month, $day);
    }
    public static function getPathSaleonline($createdate) {
        $lastChar = strrchr(STC_PATH, "/");     
        if ($lastChar !== "/") {
            $basePath = STC_PATH . "/";
        }
        $basePath = $basePath . "uploads/pack";
        $ts = strtotime($createdate);
        $year = date("Y", $ts);
        $month = date("m",$ts);
        $day = date("d",$ts);
       
        return sprintf("%s/%s/%s/%s", $basePath, $year, $month, $day);
    }
    public static function getPath($createdate) {
        $lastChar = strrchr(STC_PATH, "/");     
        if ($lastChar !== "/") {
            $basePath = STC_PATH . "/"; 
        }
        $basePath = $basePath . "uploads/sim";
        $ts = strtotime($createdate);
        $year = date("Y", $ts);
        $month = date("m",$ts);
        $day = date("d",$ts);
       
        return sprintf("%s/%s/%s/%s", $basePath, $year, $month, $day);
    }
    public static function getPath2($createdate) {
//        $lastChar = strrchr(STC_PATH, "/");     
//        if ($lastChar !== "/") {
//            $basePath = STC_PATH . "/";
//        }
        $basePath = "/uploads/sim";
        $ts = strtotime($createdate);
        $year = date("Y", $ts);
        $month = date("m",$ts);
        $day = date("d",$ts);
       
        return sprintf("%s/%s/%s/%s", $basePath, $year, $month, $day);
    }
}

?>

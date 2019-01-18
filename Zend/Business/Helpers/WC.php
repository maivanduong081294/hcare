<?php

class Business_Helpers_WC {

    private static $_instance = null;
    public $_session = null;
    public $_userinfo = null;

    // module news to store

    function __construct() {
        $this->_session = new Zend_Session_Namespace('session_app');
        $this->_userinfo = $this->_session->userinfo;
    }

    /**
     * get instance of Business_Helpers_WC
     *
     * @return Business_Helpers_WC
     */
    public static function getInstance() {

        if (self::$_instance == null) {
            self::$_instance = new Business_Helpers_WC();
        }
        return self::$_instance;
    }
    
    public static function getScoreByMatche($matcheid, $userid) {
        $ret["score"] = -1;
        $ret["check1"] = "<img src='/hnamv3/worldcup/images/icon_fail.png' />";
        $ret["check2"] = "<img src='/hnamv3/worldcup/images/icon_fail.png' />";
        $ret["check3"] = "<img src='/hnamv3/worldcup/images/icon_fail.png' />";
        
        $ketqua1 = 3;
        $ketqua2 = 1;
        $ketqua3 = 1;
        
        //get all matche
        $matches = Business_Ws_Worldcupmatches::getInstance()->getListAll();
        //getmatche detail
        $votedetail = Business_Ws_Worldcupvotes::getInstance()->getDetail2($matcheid, $userid);
        
        //tran dau chua co ket qua hoac chua duoc du doan
        foreach($matches as $m) {
            
            if ($m["id"]!=$matcheid || $m["teamid_keepball"]==0) continue;
            $ret["score"] = 0;
            
            $mid = $m["id"];
            $vote_mid = $votedetail["matcheid"];
        
            //tinh diem doi thang
            if ($mid==$vote_mid) {                
                
                if ($m["score1"]>$m["score2"]) $c1=1;
                if ($m["score1"]==$m["score2"]) $c1=2;
                if ($m["score1"]<$m["score2"]) $c1=3;   
                if ($c1 == $votedetail["c1"]) {
                    $ret["score"] = $ret["score"] + $ketqua1;
                    $ret["check1"] = "<img src='/hnamv3/worldcup/images/icon_ok.png' />";
                }
            }
            
            //tinh diem ty so tran dau
            $vote_score1 = $votedetail["team1"];
            $vote_score2 = $votedetail["team2"];
            $score1 = $m["score1"];
            $score2 = $m["score2"];
            
            if ($vote_score1 == $score1 && $vote_score2 == $score2) {
                $ret["score"] = $ret["score"] + $ketqua2;
                $ret["check2"] = "<img src='/hnamv3/worldcup/images/icon_ok.png' />";
            }
            
            //tinh diem giao bong truoc
            $tmp = $votedetail["c3"];
            if ($m["teamid_keepball"] == $votedetail["team".$tmp."id"]) {
                $ret["score"] = $ret["score"] + $ketqua3;
                $ret["check3"] = "<img src='/hnamv3/worldcup/images/icon_ok.png' />";
            }
            
            return $ret;
        }
        
    }
    
    
}
?>
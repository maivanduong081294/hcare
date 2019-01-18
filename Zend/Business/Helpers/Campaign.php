<?php

class Business_Helpers_Campaign {

    private static $_instance = null;
    public $_session = null;
    public $_userinfo = null;

    // module news to store

    function __construct() {
        $this->_session = new Zend_Session_Namespace('session_app');
        $this->_userinfo = $this->_session->userinfo;
    }

    /**
     * get instance of Business_Helpers_Campaign
     *
     * @return Business_Helpers_Campaign
     */
    public static function getInstance() {

        if (self::$_instance == null) {
            self::$_instance = new Business_Helpers_Campaign();
        }
        return self::$_instance;
    }        

}

?>

<?php

/**
 * User Admin Home Controller
 * @author: nghidv
 */
class User_Admin_ChartController extends Zend_Controller_Action {
    private $_identity;
    public function init() {
        // do something
        BlockManager::setLayout('appbh');
        $auth = Zend_Auth::getInstance(); 
        $identity = $auth->getIdentity();
        $this->view->full_name = $identity->fullname;
        $this->view->user_name = $identity->username;
        if ($identity->username == null) {
            header('Location: /admin');
        }
        if ($identity != null) {
            $username = $identity->username;
            $this->view->username = $username;
        }
        $_identity = (array)$auth->getIdentity();
        $this->_identity = $_identity;
        
    }
    public function editAction(){
        $userid = $this->_identity["userid"];
        $date = date('dmY');
        $token = md5($userid.'chart-media'.$date);
        $this->view->token = $token;
        $this->view->userid = $userid;
    }
    public function listAction(){
        $userid = $this->_identity["userid"];
        $date = date('dmY');
        $token = md5($userid.'chart-media'.$date);
        $this->view->token = $token;
        $this->view->userid = $userid;
    }
    public function userAction(){
        $userid = $this->_identity["userid"];
        $date = date('dmY');
        $token = md5($userid.'chart-media'.$date);
        $this->view->token = $token;
        $this->view->userid = $userid;
    }
    public function kinhDoanhAction(){
        $userid = $this->_identity["userid"];
        $date = date('dmY');
        $token = md5($userid.'chart-media'.$date);
        $this->view->token = $token;
        $this->view->userid = $userid;
    }
    public function saleAction(){
        $userid = $this->_identity["userid"];
        $date = date('dmY');
        $token = md5($userid.'chart-media'.$date);
        $this->view->token = $token;
        $this->view->userid = $userid;
    }
    public function marketingAction(){
        $userid = $this->_identity["userid"];
        $date = date('dmY');
        $token = md5($userid.'chart-media'.$date);
        $this->view->token = $token;
        $this->view->userid = $userid;
    }
    public function listMarketingAction(){
        $userid = $this->_identity["userid"];
        $date = date('dmY');
        $token = md5($userid.'chart-media'.$date);
        $this->view->token = $token;
        $this->view->userid = $userid;
    }
    
}
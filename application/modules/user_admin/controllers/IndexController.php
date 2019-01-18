<?php

/**
 * User Admin Home Controller
 * @author: nghidv
 */
class User_Admin_IndexController extends Zend_Controller_Action {

    public function init() {
        // do something
        BlockManager::setLayout('appbh');
    }

    public function indexAction() {
        $auth = Zend_Auth::getInstance(); 
        $identity = $auth->getIdentity();
        if ($identity != null) {
            //set cookie
            $username = $identity->username;
            $this->view->user_name = $username;
            $this->view->username = $username;
            $this->view->full_name = $identity->fullname;
            if($this->view->full_name == null){
                $this->view->full_name = $this->view->username;
            }
        }
    }

}
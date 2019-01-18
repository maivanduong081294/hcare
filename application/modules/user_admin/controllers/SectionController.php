<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class User_Admin_SectionController extends Zend_Controller_Action
{
    private $_identity;
    public function init() {
        $auth = Zend_Auth::getInstance();
        $this->_identity    = (array) $auth->getIdentity();
        $username           = $this->_identity["username"];
        $fullname           = $this->_identity["fullname"];
        
    }

    public function indexAction()
	{
		$this->_helper->Layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);

		$server->handle();
	}
        public function headerAction(){
            
            $auth = Zend_Auth::getInstance();
            $identity = $auth->getIdentity();
            if(is_null($identity) || count($identity) == 0)
                $this->_redirect('/admin/');           
            $username = $identity->username;
            $this->view->username = $username;
        }
        public function leftAction(){
            $_users_products            = Business_Addon_UsersProducts::getInstance();
            $countOnline                = $_users_products->countDonHangOnline();
            $this->view->dhonline       = $countOnline[0]["count_online"];
        }
        public function footerAction(){
            
        }
}
?>

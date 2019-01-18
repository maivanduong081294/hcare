<?php
/**  
* Admin_HomeController
* @author: tunm
*/ 
class Admin_HomeController extends Zend_Controller_Action 
{
	public function init()
	{
		// do something
	}
	
	public function indexAction()
	{
            $auth = Zend_Auth::getInstance();
            $identity = $auth->getIdentity();
            
            if ($identity->username == 'saleonline'){
                $this->_redirect('/admin/user/shopping-bag/list/');
            }
            
            if ($identity->username != 'admin')
                $this->_redirect ('/admin/user/');
	}
	
	/**
	 *
	 */
}
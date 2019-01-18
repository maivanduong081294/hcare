<?php
/**
 * Description Admin_SectionController
 * @author tunm
 */
class Admin_SectionController extends Zend_Controller_Action
{
	private $_lang;
	public function init()
	{
		// do something
	}
	
	public function indexAction()
	{
		$this->_helper->Layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$server = new Maro_Rest_Server();
		$server->setClass('MyAPI');
		
		$server->handle();
	}
	
	
	
	public function headerAction()
	{		
		$auth = Zend_Auth::getInstance();  
		$identity = $auth->getIdentity();		
		if($auth->hasIdentity())
		{ 			
			$this->view->msgLogin = "Hello {$identity->username} <a href=\"/admin/auth/logout\">[Logout]</a>";			
		}
		else
		{ 		
			$this->view->msgLogin = "&nbsp;";		
		}		
	}
	
	public function loginAction()
	{
		//show login form from view->login.phtml
	}	
	
	public function menuAction()
	{            
		//show menu for admin
	}
	
}

class MyAPI
{
	public function hello($firstname, $lastname)
	{
		return array($firstname, $lastname);
	}
}
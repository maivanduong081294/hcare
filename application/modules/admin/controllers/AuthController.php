<?php

class Admin_AuthController extends Zend_Controller_Action 
{
    
  
    
    public function init()
	{
		// do something
	}
	
	public function indexAction()  
	{  
	    
	    // call back url
	    $_callbackUrl = new Zend_Session_Namespace('callbackUrl');
	    $url = htmlspecialchars($_SERVER['HTTP_REFERER']);
	 
	    // so sanh call back url with domain current
	    if(strpos($url,$_SERVER['HTTP_HOST'] ) && !strpos($url,'auth'))
	    $url= str_replace('amp;', '', $url)   ; 
	    $_callbackUrl->url =$url;
	    
	    $auth = Zend_Auth::getInstance();  
        if($auth->hasIdentity())
		{ 
          $this->_redirect("/admin/home"); 
        }
	}
	
    public function loginAction()
	{      

        $request 	= $this->getRequest();        
        $registry 	= Zend_Registry::getInstance();
    	$auth		= Zend_Auth::getInstance(); 
    	
    	$db = Globals::getDbConnection('maindb');    		
    	$authAdapter = new Zend_Auth_Adapter_DbTable($db);
        $authAdapter->setTableName('zfw_users')
                    ->setIdentityColumn('username')
                    ->setCredentialColumn('password');    
    	
    	// Set the input credential values
    	$uname = $request->getParam('username');
    	$paswd = $request->getParam('password');
        $authAdapter->setIdentity($uname);
        $authAdapter->setCredential(md5($paswd));
    
        // Perform the authentication query, saving the result
        $result = $auth->authenticate($authAdapter);

        
        if($result->isValid())
        {

          $data = $authAdapter->getResultRowObject(null, 'password');
          $auth->getStorage()->write($data);
          $identity = $auth->getIdentity();
      
          //set timeout login 
          $authns = new Zend_Session_Namespace('Zend_Auth');
          $seconds=60 * 60 * 8 ;
          Zend_Session::rememberMe($seconds);
          Zend_Session::setOptions(array(
          'use_only_cookies' => 'on',
          'remember_me_seconds' => $seconds
          ));          
          $authns->setExpirationSeconds($seconds);  //set 8 tieng login
      
			setcookie('uname', urlencode($identity->username), time() + 1 * 60 * 60, '/', '.hnammobile.com');
			setcookie('token', urlencode(md5($identity->username."ASDQWEZXC!@#")), time() + 1 * 60 * 60, '/', '.hnammobile.com');
			

			// redirect call back url
			$_callbackUrl = new Zend_Session_Namespace('callbackUrl');
		   if(strpos($_callbackUrl->url,$_SERVER['HTTP_HOST'] ) && !strpos($_callbackUrl->url,'auth'))
			    $this->_redirect($_callbackUrl->url);
			
            if ($identity->username == 'saleonline'){
                $this->_redirect('/admin/user/shopping-bag/list/');
            }
          
            if ($identity->username == 'barcode'){
                $this->_redirect('/vote/cpanel/');
            }
            
          if ($identity->username != 'admin'){
              if (strpos($identity->username, "vote_") !== false) 
                $this->_redirect('/vote/cpanel/');
              else
                $this->_redirect('/admin/user/');
          }
          $this->_redirect('/admin/home');
        }
        else
        {
    	  $this->_redirect('/admin/auth');
    	}
    }
     
    public function logoutAction()
    {
        $auth = Zend_Auth::getInstance();
        $auth->clearIdentity();
	    $this->_redirect('/admin/auth');
    }
}
?>
<?php
class Business_Auth_Auth implements Auth_Interface
{
	protected $_storage = null;			//dung de luu session cua dang nhap : set class implement interface Storage
	protected $_adapter = null;			//dung de check dang nhap : set class implement interface Adapter
	
	protected static $_instance = null;
	protected $_identity = null;
	
	protected function __construct($adapter = null,$storage = null)
	{	
		if($adapter != null) $this->_adapter = $adapter;
		if($storage != null) $this->_storage = $storage;		
	}	
	
	/**
	 * 
	 *
	 * @return Business_Auth_Auth
	 */
	public static function getInstance($adapter = null,$storage = null)
	{
		if(null == self::$_instance)
		{
			self::$_instance = new self($adapter,$storage);
		}
		return self::$_instance;
	}
	
	public function setAdapter($adapter)
	{
		if($this->_adapter == null) $this->_adapter = $adapter;		
	}
	
	public function setStorage($storage)
	{
		if($this->_storage == null) $this->_storage = $storage;
	}	
	
	public function doLogin($username, $password)
	{
		//check login
		$result = $this->_adapter->doLogin($username, $password);                
		if($result != 1)
		{
                    return $result;	//user not exists
		}
		
		//set identity
		$this->setIdentity($username);
		
		//store identity
		$this->_storage->write($this->_identity);
		
		return 1;
	}
	public function doLogout()
	{
		$this->clearIdentity();
		
	}
	public function isLogged()
	{            
		if($this->_identity != null) return true;	
		
		//get identity from storage		
		$this->_identity = $this->_storage->read();		
		
		if($this->_identity != null) return true;
		
		return false;
	}
	public function getIdentity()
	{            
		if(!$this->isLogged()) return null;
		return $this->_identity;
	}
	
	private function clearIdentity()
	{
		$this->_identity = null;
		$this->_storage->flush();
	}
	
	private function setIdentity($username)
	{
            $userModel = Business_Ws_UserModule::getInstance();

            $user = $userModel->getUserByUsername($username);
		
            $this->_identity = array();
            $this->_identity['username'] = $user['username'];
            $this->_identity['uid'] = $user['userid'];
		
	}
}
?>
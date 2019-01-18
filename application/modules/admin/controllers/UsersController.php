<?php

class Admin_UsersController extends Zend_Controller_Action 
{
	private $_user_business = null;

	public function init()
	{
		// do something
		BlockManager::setLayout('admin_layout');
	}
	
	public function indexAction()
	{
		
	}
	
	public function changePassSaveAction()
	{
		$old_password = $this->_request->getParam('old_password');
		$new_password = $this->_request->getParam('new_password');
		$cfm_new_password = $this->_request->getParam('cfm_new_password');
		$userid = $this->_request->getParam('id');
		
		$auth = Zend_Auth::getInstance();  
		$identity = $auth->getIdentity();
		$username = $identity->username;

				
		$_user = Business_Common_Users::getInstance();
		if($username == "admin" && $userid != null)
		{			
			if($new_password != $cfm_new_password)
			{
				$this->view->message = "New password and Confirm New password not the same";
				$this->changePassAction();
				$this->_helper->viewRenderer->setRender('change-pass');
				return;
			}			
			$data = array();
			$data['password'] = md5($new_password);
			$_user->updateUser($userid,$data);
			$this->view->ok_message = "Password changed.";
			$this->_helper->viewRenderer->setRender('change-pass-msg');
			return;
		}
		else
		{
			$old_password = md5($old_password);
			
			$user = $_user->getUser($username);
						
			if($user != null && $user['password'] == $old_password)
			{
				$id = $user['userid'];
				if($new_password != $cfm_new_password)
				{
					$this->view->message = "New password and Confirm New password not the same";
					$this->changePassAction();
					$this->_helper->viewRenderer->setRender('change-pass');
					return;
				}			
				$data = array();
				$data['password'] = md5($new_password);
				$_user->updateUser($id,$data);
				$this->view->ok_message = "Password changed.";
				$this->_helper->viewRenderer->setRender('change-pass-msg');
				return;
			}
			else
			{
				$this->view->message = "Old password not correct";
				$this->changePassAction();
				$this->_helper->viewRenderer->setRender('change-pass');
				return;
			}
		}
		
		
	}
	
	public function changePassAction()
	{
		
		$auth = Zend_Auth::getInstance();  
		$identity = $auth->getIdentity();
		$username = $identity->username;
		$userid = $this->_request->getParam('id');
		$_user = Business_Common_Users::getInstance();
		
		if($username == "admin" && $userid != null)
		{
			$user = $_user->getUserByUid($userid);			
			$this->view->is_admin = true;	
			$this->view->data = $user;
		}
		else
		{
			$user = $_user->getUser($username);
			
			$this->view->is_admin = false;			
			$this->view->data = $user;
		}
		
		$this->view->form_action = Globals::getBaseUrl() . "admin/users/change-pass-save?id=" . $userid;
	}
	
	public function addNewAction()
	{
		
		$username = $this->_request->getParam('username');
		$password = $this->_request->getParam('password');
		$status = $this->_request->getParam('status');
		$roles = $this->_request->getParam('roles');
		
		$_user = Business_Common_Users::getInstance();
		
		//check username first
		$user = $_user->getUser($username);
		$data = array();
		$data['username'] = $username;
		$data['password'] = md5($password);
		$data['status'] = $status;
		
		if($user != null)
		{
			$this->view->message = "This username '$username' existed. Choose another one.";
			$this->view->data = $data;
			return;
		}	
		
		$userid = $_user->addUser($data);
		
		if($userid > 0)
		{
			$_roles = Business_Common_Roles::getInstance();		
			$_roles->deleteAllRoleForUser($userid);
			
			$roles = $this->_request->getParam('roles');
			
			if(is_array($roles) && count($roles) > 0)
			{
				for($i=0;$i<count($roles);$i++)
				{
					$pid = $roles[$i];
					$_roles->addRole($userid, $pid);
				}
			}
			else
			{
				$pid = $roles;
				$_roles->addRole($userid, $pid);
			}			
		}
		
		$this->_redirect(Globals::getBaseUrl() . "admin/users/list");
		
	}
	
	public function addAction()
	{
		$user_roles = array();
		
		//get permission
		$_permission = Business_Common_Permissions::getInstance();
		$permission = $_permission->getList();
		
		$roles_checklist = '';		
				
		if($permission != null && is_array($permission) && count($permission) > 0)
		{
			for($i=0;$i<count($permission);$i++)
			{
				$pid = $permission[$i]['pid'];
				if(in_array($pid, $user_roles))
				{
					$roles_checklist .= "<input type='checkbox' name='roles[]' id='roles_" . $pid . "' value='" . $pid . "' checked='checked' />" . $permission[$i]['name'] . "<br/>";
				}
				else
				{
					$roles_checklist .= "<input type='checkbox' name='roles[]' id='roles_" . $pid . "' value='" . $pid . "' />" . $permission[$i]['name'] . "<br/>";
				}
			}
		}
		
		$this->view->roles_checklist = $roles_checklist;
		$this->view->data = $user;
		$this->view->form_action = Globals::getBaseUrl() . "admin/users/add-new";
	}
	
	public function editAction()
	{
		$id = $this->_request->getParam('id');
		
		$_user = Business_Common_Users::getInstance();
		
		$user = $_user->getUserByUid($id);
		
		//get role for user
		$_roles = Business_Common_Roles::getInstance();		
		$roles = $_roles->getRolesByUser($id);
		
		$user_roles = array();
		if($roles != null && is_array($roles) && count($roles) > 0)
		{
			for($i=0;$i<count($roles);$i++)
			{
				$user_roles[] = $roles[$i]['pid'];
			}
		}

		//get permission
		$_permission = Business_Common_Permissions::getInstance();
		$permission = $_permission->getList();
		
		$roles_checklist = '';
						
		if($permission != null && is_array($permission) && count($permission) > 0)
		{
			for($i=0;$i<count($permission);$i++)
			{
				$pid = $permission[$i]['pid'];
				if(in_array($pid, $user_roles))
				{
					$roles_checklist .= "<input type='checkbox' name='roles[]' id='roles_" . $pid . "' value='" . $pid . "' checked='checked' />" . $permission[$i]['name'] . "<br/>";
				}
				else
				{
					$roles_checklist .= "<input type='checkbox' name='roles[]' id='roles_" . $pid . "' value='" . $pid . "' />" . $permission[$i]['name'] . "<br/>";
				}
			}
		}	
				
		
		$this->view->roles_checklist = $roles_checklist;
		$this->view->data = $user;
		$this->view->form_action = Globals::getBaseUrl() . "admin/users/save";
	}
	
	public function saveAction()
	{
		$this->_helper->viewRenderer->setNoRender();
		$id = $this->_request->getParam('id');
		
		$password = $this->_request->getParam('password');
		$status = $this->_request->getParam('status');
		
		$data = array();
		$data['status'] = $status;
		
		if($password != null && $password != '')
		{					
			$data['password'] = md5($password);		
		}
		
		$_user = Business_Common_Users::getInstance();
		$_user->updateUser($id, $data);
		
		//get roles
		
		$userid = $id;
		$_roles = Business_Common_Roles::getInstance();		
		$_roles->deleteAllRoleForUser($userid);
		
		$roles = $this->_request->getParam('roles');
		
		if(is_array($roles) && count($roles) > 0)
		{
			for($i=0;$i<count($roles);$i++)
			{
				$pid = $roles[$i];
				$_roles->addRole($userid, $pid);
			}
		}
		else
		{
			$pid = $roles;
			$_roles->addRole($userid, $pid);
		}			
		
		$this->_redirect(Globals::getBaseUrl() . "admin/users/list");
		
	}
	
	public function listAction()
	{
				
		$_users = $this->getUserBusiness();		
		
		$list = $_users->getList();		
				
		$title = array("Username", "Status", "Action");
		$fields = array(	
				array("type" => "title", "data" => "username"),
				array("type" => "title", "data" => "status"),							
				array("type" => "link", "data" => array(
															array(	"title" => "Edit", 
																	"field" => "userid", 
																	"link" => Globals::getBaseUrl() . "admin/users/edit/id/%s"
																),
															array(	"title" => "Change password", 
																	"field" => "userid", 
																	"link" => Globals::getBaseUrl() . "admin/users/change-pass/id/%s"
																),
															array(
																	"title" => "Delete", 
																	"field" => "userid", 
																	"link" => Globals::getBaseUrl() . "admin/users/delete-cfm/id/%s"
																)															
														)
					)
		);
		
		$listing = new Maro_Layout_Listing($title, $fields, $list,true);	
		
		$content = $listing->renderList();
		
		$this->view->content = $content;
		$this->view->user = $user;
		$this->view->create_url = Globals::getBaseUrl() . "admin/users/add";
			
		
	}
	
	
	
	////////// private functions ////////////
	
	/**
	 * Get bussiness instance of Business_Common_Users
	 *
	 * @return Business_Common_Users
	 */
	private function getUserBusiness()
	{
		if($this->_user_business == null)
		{
			$this->_user_business = new Business_Common_Users();			
		}
		return $this->_user_business;		
	}
}
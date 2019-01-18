<?php

class Admin_RolesController extends Zend_Controller_Action 
{
	private $_permission_business = null;

	public function init()
	{
		// do something
		BlockManager::setLayout('admin_layout');
	}
	
	public function indexAction()
	{
		
	}
	
	public function deleteCfmAction()
	{
		$this->_helper->viewRenderer->setRender('confirm');
		$id = $this->_request->getParam("id");
		
		$_permission = $this->getPermissionBusiness();
		$role = $_permission->getPermision($id);
		
		if($role != null)
		{
		
			$this->view->message = "Are you want to delete role name '" . $role["name"] . "'";
			$this->view->yes_link = Globals::getBaseUrl() . "admin/roles/delete/id/" . $id;
			$this->view->no_link = Globals::getBaseUrl() . "admin/roles/list";
		}
	}
	
	public function editAction()
	{
		
		$id = $this->_request->getParam('id');		
		
		if($id != null && $id != "")
		{
			$_permission = $this->getPermissionBusiness();
			$role = $_permission->getPermision($id);			
			$this->view->data = $role;
		}
		
		$this->view->form_action = Globals::getBaseUrl() . "admin/roles/save/";
		$this->view->cancel_btn = Globals::getBaseUrl() . "admin/roles/list";
		
		$this->view->edited = true;
		
	}
	
	public  function saveAction()
	{				
		$this->_helper->viewRenderer->setNoRender();
		
		$id = $this->_request->getParam('id');
		$rolename = $this->_request->getParam('rolename');
		
		//check role name
		
		$_permission = $this->getPermissionBusiness();
		
		$data = array();
		$data['name'] = $rolename;		
		
		if($id == null || $id == '')
		{
			$data['permission'] = '';
			$_permission->addPermision($data);
		}
		else
		{
			$_permission->updatePermission($id, $data);
		}
		
		$url = Globals::getBaseUrl() . "admin/roles/list";
		$this->_redirect($url);
	}
	
	public function listAction()
	{
		$_permission = $this->getPermissionBusiness();
		
		$list = $_permission->getList();
		
		$title = array("Name", "Operation", "Edit Permission");
		$fields = array(	
				array("type" => "title", "data" => "name"),
				array("type" => "link", "data" => array(
															array(	"title" => "edit role", 
																	"field" => "pid", 
																	"link" => Globals::getBaseUrl() . "admin/roles/edit/id/%s"
																),
															array(	"title" => "delete role", 
																	"field" => "pid", 
																	"link" => Globals::getBaseUrl() . "admin/roles/delete-cfm/id/%s"
																)
														),
					),							
				array("type" => "link", "data" => array(
															array(	"title" => "edit permission", 
																	"field" => "pid", 
																	"link" => Globals::getBaseUrl() . "admin/permissions/list/id/%s"
																)															
														),
					)
				
		);
		
		$listing = new Maro_Layout_Listing($title, $fields, $list,true);	
		
		$content = $listing->renderList();
		
		$this->view->content = $content;
		$this->view->user = $user;
		$this->view->action_url = Globals::getBaseUrl() . "admin/roles/save";
		
		
	}
	
	////////// private functions ////////////
	
	/**
	 * Get bussiness instance of Business_Common_Permissions
	 *
	 * @return Business_Common_Permissions
	 */
	private function getPermissionBusiness()
	{
		if($this->_permission_business == null)
		{
			$this->_permission_business = new Business_Common_Permissions();			
		}
		return $this->_permission_business;		
	}
}
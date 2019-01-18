<?php

/**  
* Admin_VariablesController
* @author: tunm
*/ 
class Admin_VariablesController extends Zend_Controller_Action 
{
			
	public function init()
	{
		// do something
		BlockManager::setLayout('admin_layout');
	}
	
	public function indexAction()
	{
		
	}
	
	public function saveAction()
	{
		$this->_helper->viewRenderer->setRender('edit');
		$id = $this->_request->getParam('id');
		
		$name = $this->_request->getParam('name');
		$oldname = $this->_request->getParam('oldname');
		$value = $this->_request->getParam('var_value');
		
		$data = array();
		$data['name'] = $name;
		$data['value'] = $value;
		
				
		$_variables = Business_Common_Variables::getInstance();
		
		//check name exist or not first		
		if($name == '')
		{		
			$this->view->message = "name can not be blank";
			$this->view->data = $data;
			$this->view->form_action = Globals::getBaseUrl() . "admin/variables/save/";
			$this->view->cancel_btn = Globals::getBaseUrl() . "admin/variables/list";
			return;
		}
		
		//check name exist or not first		
		if($name != $oldname)
		{			
			$variable = $_variables->getVariable($name, NULL);
			
			if(!is_null($variable))
			{
				$this->view->message = "This name '$id' is exists, please choose another";
				$this->view->data = $data;
				$this->view->form_action = Globals::getBaseUrl() . "admin/variables/save/";
				$this->view->cancel_btn = Globals::getBaseUrl() . "admin/variables/list";
				return;
			}
		}
		
		$_variables->setVariable($name, $value);
		$this->_redirect(Globals::getBaseUrl() . "admin/variables/list");		
	}
	
	public function editAction()
	{
		$id = $this->_request->getParam('id');		
		
		if($id != null && $id != "")
		{
			$_variables = Business_Common_Variables::getInstance();
			$variable = $_variables->getVariable($id, '');			
			$data = array();
			$data['name'] = $id;
			$data['value'] = $variable;
			$this->view->data = $data;
		}
		
		$this->view->form_action = Globals::getBaseUrl() . "admin/variables/save/";
		$this->view->cancel_btn = Globals::getBaseUrl() . "admin/variables/list";
	}
	
	public function listAction()
	{
		$_variables = Business_Common_Variables::getInstance();
		$list = $_variables->getList();
		
		$title = array("Name", "Serialized", "Action");
		$fields = array(	
				array("type" => "title", "data" => "name"),
				array("type" => "title", "data" => "serilized"),							
				array("type" => "link", "data" => array(
															array(	"title" => "Edit", 
																	"field" => "name", 
																	"link" => Globals::getBaseUrl() . "admin/variables/edit/id/%s"
																),
															array(
																	"title" => "Delete", 
																	"field" => "name", 
																	"link" => Globals::getBaseUrl() . "admin/variables/delete-cfm/id/%s"
																)															
														)
					)
		);
		
		$listing = new Maro_Layout_Listing($title, $fields, $list,true);	
		
		$content = $listing->renderList();
		
		$this->view->content = $content;
		$this->view->user = $user;
		$this->view->create_url = Globals::getBaseUrl() . "admin/variables/edit";
	}
	
	public function deleteCfmAction()
	{
		$id = $this->_request->getParam('id','');
		$this->_helper->viewRenderer->setNoRender(true);
		$variableModel = Business_Common_Variables::getInstance();
		$variableModel->deleteVariable($id);
		$this->goBack();	
	}
	
	private function goBack($lev = '-1')
	{
		echo "<script>history.go($lev);</script>";
	}
	
}
?>
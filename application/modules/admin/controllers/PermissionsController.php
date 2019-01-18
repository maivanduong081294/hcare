<?php

class Admin_PermissionsController extends Zend_Controller_Action 
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
	
	public function saveAction()
	{
		$this->_helper->viewRenderer->setNoRender();
		
		$id = $this->_request->getParam('id');
		$roles = array();
		$_permision = $this->getPermissionBusiness();
		
		if($id != null && $id != '')
		{
			$role = $_permision->getPermision($id);
			if($role != null) $roles[] = $role;	
		}
		else
		{
			$roles = $_permision->getList();
		}
		
		for($i=0;$i<count($roles);$i++)
		{
			$pid = $roles[$i]['pid'];			
			$perm = $this->_request->getParam($pid);
			
			if($perm != null)
			{
				$perm = implode(',',$perm);
				
				$data = array();
				$data['permission'] = $perm;				
				$_permision->updatePermission($pid, $data);
			}
		}
		
		if($id != null && $id != "") $this->_redirect(Globals::getBaseUrl() . "admin/permissions/list/id/" . $id);
		else $this->_redirect(Globals::getBaseUrl() . "admin/permissions/list");
		
	}
	
	public function listAction()
	{
		
		$_permision = $this->getPermissionBusiness();
		
		
		$id = $this->_request->getParam('id');
		
		$roles = array();
		
		if($id != null && $id != '')
		{
			$role = $_permision->getPermision($id);
			if($role != null) $roles[] = $role;	
		}
		else
		{
			$roles = $_permision->getList();
		}
		
		if(count($roles) == 0) return;
		
		$content = "";

		$this->openTable($roles, $content);
		
		$modules = GlobalPermision::getPermModules();
		
		$colspan = count($roles);
		

		
		//duyet qua bang modules
		for($i=0;$i<count($modules);$i++)
		{
			
			$list = array();
			
			$module_perm = GlobalPermision::getPermList($modules[$i]);
			
			$this->addTrModule($module_perm['title'],$content, $colspan + 1);
			
			$list_perm = explode(',', $module_perm['perm_list']);
			$list['permission_name'] = array();
			for($j=0;$j<count($list_perm);$j++)
			{
				$list['permission_name'][] = $list_perm[$j];				
			}
			$list['roles'] = $roles;
			
			$this->addTd($list,$content);
			
		}
		
		$this->closeTable($content);
		
		$this->view->content = $content;
		
		if($id == null || $id == "")
			$this->view->url_action = Globals::getBaseUrl() . "admin/permissions/save";
		else $this->view->url_action = Globals::getBaseUrl() . "admin/permissions/save/id/" . $id;
		
		
		
		
	}
	
	private function openTable($roles, &$content)
	{
		$content .= "<table class=\"mytable\"><thead><tr><th valign=\"middle\">Permission</th>";
		
		for($i=0;$i<count($roles);$i++)
		{
			$content .= "<th>" . $roles[$i]['name'] . "</th>";
		}
		
		$content .= "</tr></thead>";
	}
	
	private function closeTable(&$content)
	{
		$content .= "</table>";
	}
	
	private function addTrModule($modulename, &$content, $colspan = 2)
	{
		$content .= "<tr>" . "<td class=\"module\" colspan=\"$colspan\">" .$modulename . "</td></tr>";		
	}
	
	private function addTd($list, &$content)
	{
		if($list != null && is_array($list) && count($list) > 0)
		{
			for($i=0;$i<count($list['permission_name']);$i++)
			{
				$permission_name = $list['permission_name'][$i];
				$content .= "<tr class='" . ($i % 2 == 0 ? "odd" : "even") . "'>" . "<td>" . $permission_name . "</td>";
				for($j=0;$j<count($list['roles']);$j++)
				{
					$role = $list['roles'][$j];
					$permissions = explode(',', $role['permission']);
					if(in_array($permission_name,$permissions))
					{
						$content .= "<td><input type='checkbox' name='" . $role['pid'] . "[]' id='" .$role['name'] . "-" 
								. $permission_name . "' checked=checked value='" . $permission_name . "'></td>";
					}
					else
					{
						$content .= "<td><input type='checkbox' name='" . $role['pid'] . "[]' id='" .$role['name'] . "-" 
								. $permission_name . "' value='" . $permission_name . "'></td>";
					}
				}
				$content .= "</tr>";
			}
		}
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
<?php

/**  
* Admin_CategoryController
* Description: define categorycontroller class for list, insert, update, delete category
* @author: tunm
*/ 
class Admin_CategoryController extends Zend_Controller_Action
{
	public function init()
	{
		// do something
		BlockManager::setLayout('admin_layout');
	}
	
	public function indexAction()
	{
		// do something				
	}
	
	
	public function catenameDeleteCfmAction()
	{
		$this->_helper->viewRenderer->setRender('confirm');
		$id = $this->_request->getParam("id");
		
		$_catename = Business_Common_Category::getInstance();
		$catename = $_catename->getCategoryName($id);
		
		if($catename != null)
		{
		
			$this->view->message = "Are you want to delete category name '" . $catename["catename"] . "'";
			$this->view->yes_link = Globals::getBaseUrl() . "admin/category/catename-delete/id/" . $id;
			$this->view->no_link = Globals::getBaseUrl() . "admin/category/catename-list";
		}
		
	}
	
	public function catenameDeleteAction()
	{
		$this->_helper->viewRenderer->setRender('index');
		
		$id = $this->_request->getParam("id");
		
		if($id != null && $id != "")
		{
			$_catename = Business_Common_Category::getInstance();
			$_catename->deleteCategoryName($id);
			$this->_redirect(Globals::getBaseUrl() . "admin/category/catename-list");
		}		
	}
	
	public function catenameSaveAction()
	{
		$this->_helper->viewRenderer->setRender('index');
		
		if($this->_request->isPost())
		{			
			$data = array();
			$catename = $this->_request->getPost("catename");
			$description = $this->_request->getPost("description");
									
			$id = $this->_request->getPost("id");
			$_catename = Business_Common_Category::getInstance();
			
			
			if($id == '')
			{			
				$_catename->addCategoryName($catename, $description);
			}
			else
			{
				$data["catename"] = $catename;
				$data["description"] = $description;
				$_catename->updateCategoryName($id, $data);
			}			
			
			$this->_redirect(Globals::getBaseUrl() . "admin/category/catename-list");
		}
		else
		{
			$this->_redirect(Globals::getBaseUrl() . "admin/category/catename-list");
		}
	}
	
	public function catenameEditAction()
	{		
						
		$id = $this->_request->getParam("id");
		
		$data = null;
		
		if($id != "")
		{
			$this->view->edited = true;
			$_catename = Business_Common_Category::getInstance();
			$data = $_catename->getCategoryName($id);			
		}
		
		if(is_null($data))
		{
			$this->view->edited = false;
			
			$data["categoryid"] = "";
			$data["catename"] = "";
			$data["description"] = "";					
		}
		
		
		$config = Zend_Registry::get('configuration');
				
		$this->view->data = $data;				
		$this->view->form_action = Globals::getBaseUrl() . "admin/category/catename-save";
		$this->view->cancel_btn = Globals::getBaseUrl() . "admin/category/catename-list";
		
		
		
	}
	
	public function catenameListAction()
	{
		$_cate = Business_Common_Category::getInstance();
		$list = $_cate->getListCategory();	
		
				
		$title = array("CateName", "Description", "Created Date", "Action");
		$fields = array(	
				array("type" => "title", "data" => "catename"),
				array("type" => "title", "data" => "description"),
				array("type" => "title", "data" => "created"),				
				array("type" => "link", "data" => array(
															array(	"title" => "Edit", 
																	"field" => "categoryid", 
																	"link" => Globals::getBaseUrl() . "admin/category/catename-edit/id/%s"
																),
															array(
																	"title" => "Delete", 
																	"field" => "categoryid", 
																	"link" => Globals::getBaseUrl() . "admin/category/catename-delete-cfm/id/%s"
																),
															array( "title" => "Manage",
																	"field" => "catename",
																	"link" => Globals::getBaseUrl() . "admin/category/catenode-list/catename/%s"
																)
														)
					)
		);
		
		$listing = new Maro_Layout_Listing($title, $fields, $list,true);	
		
		$content = $listing->renderList();
		
		$this->view->content = $content;
		$this->view->user = $user;
		$this->view->create_url = Globals::getBaseUrl() . "admin/category/catename-edit";
		
	}
	
		
	public function catenodeDeleteCfmAction()
	{
		$this->_helper->viewRenderer->setRender('confirm');
		$catename = $this->_request->getParam("catename");
		$id = $this->_request->getParam("id");
		
		$_cate = Business_Common_Category::getInstance();
		$catenode = $_cate->getCate($catename,$id);
		
		if($catenode != null)
		{		
			$this->view->message = "Are you want to delete cate node '" . $catenode["title"] . "'";
			$this->view->yes_link = Globals::getBaseUrl() . "admin/category/catenode-delete/catename/" . $catename . "/id/" . $id;
			$this->view->no_link = Globals::getBaseUrl() . "admin/category/catenode-list/catename/" . $catename;
		}		
	}
	
	public function catenodeDeleteAction()
	{
		$catename = $this->_request->getParam("catename");
		$id = $this->_request->getParam('id');
						
		$_cate = Business_Common_Category::getInstance();
		
		$_cate->deleteCate($catename, $id);
		
		$this->_redirect(Globals::getBaseUrl() . "admin/category/catenode-list/catename/" . $catename);
	}
	
	public function catenodeAddSaveAction()
	{
				
		$this->_helper->viewRenderer->setNoRender();
		$catename = $this->_request->getParam("catename");
		$title = $this->_request->getParam('title');
		$linkpath = $this->_request->getParam('linkpath');
		$pid = $this->_request->getParam('pid');
							
		$metadata = array();
				
		if($_FILES['picfile'] != null && $_FILES['picfile']['size'] > 0)
		{
			$uploads_dir = BASE_PATH . '/uploads';
			$name = $_FILES['picfile']['name'];
			$tmp_name = $_FILES['picfile']['tmp_name'];
			move_uploaded_file($tmp_name, "$uploads_dir/$name");			
			$metadata['thumb'] = $name;
		}
		else
		{
			$metadata['thumb'] = '';
		}
		
		$shortdes = $this->_request->getParam('shortdes');
		if($shortdes != null)
		{
			$metadata['shortdes'] = $shortdes;
		}
		else
		{
			$metadata['shortdes'] = '';
		}
				
		$metadata = json_encode($metadata);		
		
		$_cate = Business_Common_Category::getInstance();		
		
		$_cate->addCate($catename, $title, $linkpath, $pid, $metadata);		
		
		$this->_redirect(Globals::getBaseUrl() . "admin/category/catenode-list/catename/" . $catename);
	}
	
	public function catenodeAddAction()
	{
		
		$catename = $this->_request->getParam("catename");
		$pid = $this->_request->getParam('pid');
		
		$_cate = Business_Common_Category::getInstance();
		
		$list = $_cate->getAllCate($catename);
		
		$parentlink = array("0" => "<Parent>");
		
		$parentlist = $_cate->getParentCate($catename);
		
		if($parentlist != null && count($parentlist) > 0)
		{
			for($i=0;$i<count($parentlist);$i++)
			{
				$temp = "";
				$depth = intval($parentlist[$i]["depth"]);
				for($j=0;$j<$depth;$j++) $temp .= "--";
				$parentlink[$parentlist[$i]["id"]] = $temp . " " . $parentlist[$i]["title"];
				if($parentlist[$i]["endcate"] != 1)
				{					
					$_depth = intval($parentlist[$i]["depth"]);
					$_depth++;
					$this->catelistRecursiveForSelect($catename, $parentlist[$i]["id"], $_depth, $parentlink); 
				}
			}
		}
		
		$this->view->pid = $pid;
		$this->view->catename = $catename;
		$this->view->parentlink = $parentlink;
		$this->view->form_action = Globals::getBaseUrl() . "admin/category/catenode-add-save/catename/" . $catename;		
	}
	
	public function catenodeEditSaveAction()
	{
		$catename = $this->_request->getParam("catename");
		$id = $this->_request->getParam('id');
		
		$title = $this->_request->getParam('title');
		$linkpath = $this->_request->getParam('linkpath');
		
		$data = array();
		$data['title'] = $title;
		$data['linkpath'] = $linkpath;
		
		$metadata = array();
		
		$shortdes = $this->_request->getParam('shortdes');
		if($shortdes != null)
		{
			$metadata['shortdes'] = $shortdes;
		}
		else
		{
			$metadata['shortdes'] = '';
		}
		
		$ck_delete_pic = $this->_request->getParam('ck_delete_pic');
		$oldpicfile = $this->_request->getParam('oldpicfile');
						
		if($ck_delete_pic != null && $ck_delete_pic == "del")
		{
			//delete old file
			$uploads_dir = BASE_PATH . '/uploads';
			$oldfile = $uploads_dir . '/' . $oldpicfile;
			if(is_file($oldfile))
			{
				unlink($oldfile);
			}
			$metadata['thumb'] = '';
		}
		else
		{			
			if($_FILES['picfile'] != null && $_FILES['picfile']['size'] > 0)
			{
				$uploads_dir = BASE_PATH . '/uploads';
				$name = $_FILES['picfile']['name'];
				$tmp_name = $_FILES['picfile']['tmp_name'];
				move_uploaded_file($tmp_name, "$uploads_dir/$name");			
				$metadata['thumb'] = $name;
				
				//delete old file
				$uploads_dir = BASE_PATH . '/uploads';
				$oldfile = $uploads_dir . '/' . $oldpicfile;
				if(is_file($oldfile))
				{
					unlink($oldfile);
				}
			}
			else
			{				
				$metadata['thumb'] = $oldpicfile;
			}			
		}
		$metadata = json_encode($metadata);
		
		$data['metadata'] = $metadata;
		
		//var_dump($data);exit();
		
		$_cate = Business_Common_Category::getInstance();
		$_cate->updateCate($catename, $id, $data);
		
		$this->_redirect(Globals::getBaseUrl() . "admin/category/catenode-list/catename/" . $catename);
	}
	
	public function catenodeEditAction()
	{
		$catename = $this->_request->getParam("catename");
		$id = $this->_request->getParam('id');
		
		$_cate = Business_Common_Category::getInstance();
		
		$nodecate = $_cate->getCate($catename, $id);
		
		if($nodecate != null)
		{
			if($nodecate['metadata'] != null)
			{
				$metadata = json_decode($nodecate['metadata'], true);
				$nodecate['metadata'] = $metadata;
			}
			$this->view->data = $nodecate;
			$img_path = Globals::getConfig('img_url');			
			$img = $img_path . $metadata['thumb'];
			$img = "<img src='" . $img . "'>";
			$this->view->img = $img;
			$this->view->oldpicfile = $metadata['thumb'];
			
		}		
		
		$this->view->catename = $catename;		
		$this->view->form_action = Globals::getBaseUrl() . "admin/category/catenode-edit-save/catename/" . $catename;
		$this->view->cancel_btn = Globals::getBaseUrl() . "admin/category/catenode-list/catename/" . $catename;
	}
	
	public function catenodeListAction()
	{
		$catename = $this->_request->getParam("catename");
		$_cate = Business_Common_Category::getInstance();
				
		$content = '';
		
		$parentlist = $_cate->getParentCate($catename);
		
		if($parentlist != null && count($parentlist) > 0)
		{
			$content = "<ul>";
			for($i=0;$i<count($parentlist);$i++)
			{
				$_id = $parentlist[$i]["id"];				
				$content .= "<li>" . $parentlist[$i]["title"] . " " . $this->buildNodeCateLink($_id, $catename);
				if($parentlist[$i]["endcate"] != 1)
				{					
					$_depth = intval($parentlist[$i]["depth"]);
					$_depth++;					
					$content .= $this->catelistRecursive($catename, $_id, $_depth); 
				}
				$content .= "</li>";
			}
			$content .= "</ul>";
		}
		
		$this->view->content = $content;
		$this->view->catename = $catename;
		$this->view->create_url = Globals::getBaseUrl() . "admin/category/catenode-add/catename/" . $catename;
		
	}
	
	//private functions
	
	private function catelistRecursiveForSelect($catename, $id, $depth, &$arr)
	{
		$_cate = Business_Common_Category::getInstance();		
		$list = $_cate->getListFilter($catename, $id, $depth);
		
		if($list == null || count($list) == 0)
		{			
			return;
		}
		else
		{		
			for($i=0;$i<count($list);$i++)
			{
				$temp = "";
				$_id = $list[$i]["id"];
				for($j=0;$j<$depth;$j++) $temp .= "--";
				$temp .= " " . $list[$i]["title"];
				$arr[$_id] = $temp;
				if($list[$i]["endcate"] != 1)
				{					
					$_depth = intval($list[$i]["depth"]);
					$_depth++;
					$this->catelistRecursiveForSelect($catename, $_id, $_depth, $arr); 
				}				
			}			
		}		
	}
	
	private function catelistRecursive($catename, $id, $depth)
	{
		$_cate = Business_Common_Category::getInstance();		
		$list = $_cate->getListFilter($catename, $id, $depth);
		
		if($list == null || count($list) == 0)
		{			
			return "";
		}
		else
		{
			
			$temp = "<ul>";
			for($i=0;$i<count($list);$i++)
			{
				$_id = $list[$i]["id"];
				$temp .= "<li>" . $list[$i]["title"] . " " . $this->buildNodeCateLink($_id, $catename);
				if($list[$i]["endcate"] != 1)
				{					
					$_depth = intval($list[$i]["depth"]);
					$_depth++;
					$temp .= $this->catelistRecursive($catename, $_id, $_depth); 
				}
				$temp .= "</li>";
			}
			$temp .= "</ul>";			
			return $temp;
		}		
	}
	
	private function buildNodeCateLink($id, $catename)
	{		
		$temp = '';		
		$temp .= "[ <a href='" . Globals::getBaseUrl() . "admin/category/catenode-add/catename/" . $catename . "/pid/" . $id . "'>Add</a> | ";		
		$temp .= "<a href='" . Globals::getBaseUrl() . "admin/category/catenode-edit/catename/" . $catename . "/id/" . $id . "'>Edit</a> | ";
		$temp .= "<a href='" . Globals::getBaseUrl() . "admin/category/catenode-delete-cfm/catename/" . $catename . "/id/" . $id . "'>Delete</a> | ";
		$temp .= "<a href='" . Globals::getBaseUrl() . "admin/category/catenode-move/catename/" . $catename . "/id/" . $id . "'>Move</a> ]";
		return $temp;
	}
	
	
}
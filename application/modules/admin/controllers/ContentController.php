<?php
/**  
* Admin_ViewController
* Description: define viewcontroller class for list, insert, update, delete view for the layout
* @author: tunm
*/ 
class Admin_ContentController extends Zend_Controller_Action 
{
	protected $_contentModel;
	
	public function init()
	{
		// do something
	}
	
	public function indexAction()
	{
		
	}
	
	/**
	 * listAction
	 * Description: show content list of layout
	 */
	 public function listAction()
	 {
		$model = $this->_getModel();
		$result = $model->getContents();	
		$page = (int)($this->_request->getParam('page'));
		Globals::doPaging($result, $page, $this->view);   
						
		$this->view->page = $page;
	 }
	
	/**
	 * method: new/edit content
	 * Description: create new or edit a content
	 */
	 public function editAction()
	 {
		$model = $this->_getModel();
		$id = $this->_request->getParam('id');   
		$this->view->data = $model->getContent($id);
	 }
	 
	 /**
	 * method: update view information
	 * Description: update view
	 */
	 public function saveAction()
	 {
		$model = $this->_getModel();
		// check if form is posted
		if($this->_request->isPost())
		{
			$arrData = array();
			$arrData['contentname'] 		= $this->_request->getPost('contentname');
			$arrData['content'] 			= $this->_request->getPost('fullcontent');			
			$id = $this->_request->getPost('id');
			$model->updateContent($arrData, $id);
			$this->_redirect('/admin/content/list');
		}
		else
			$this->_redirect('/admin/content/list');
	 }
	 
	 /**
	 * method: deleteAction
	 * Description: delete a content
	 */
	 public function deleteAction()
	 {
		$model = $this->_getModel();
		$model->deleteContent($this->_request->getParam('id'));
		$this->_redirect('/admin/content/list');
	 }
	
	 /**
	  * get model of Content
	  *
	  * @return Content
	  */
	protected function _getModel()
    {
        require APPLICATION_PATH . '/modules/admin/models/Content.php';
		if ($this->_contentModel == null) 
		{                        
            $this->_contentModel = new Content();
        }
        return $this->_contentModel;                
    }
}
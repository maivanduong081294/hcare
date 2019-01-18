<?php
/**  
* Admin_BoxController
* Description: define boxcontroller class for list, insert, update, delete box for the layout
* @author: tunm
*/ 
class Admin_BoxController extends Zend_Controller_Action 
{
	protected $_boxModel;
	
	public function init()
	{
		// do something
	}
	
	public function indexAction()
	{
		
	}
	
	/**
	 * listAction
	 * Description: show boxies list of box
	 */
	 public function listAction()
	 {
		$model = $this->_getModel();
		$result = $model->getBoxes();	
		$page = (int)($this->_request->getParam('page'));   
		if(count($result) > 0)
		{ 
			Globals::doPaging($result, $page, $this->view);
		}
				
		$this->view->page = $page;
	 }
	
	/**
	 * method: new/edit layout
	 * Description: create new or edit a box
	 */
	 public function editAction()
	 {
		$model = $this->_getModel();
		$boxId = $this->_request->getParam('boxid');   
		$this->view->objBox	= $model->getBox($boxId);
	 }
	 
	 /**
	 * method: update layout information
	 * Description: update layout
	 */
	 public function saveAction()
	 {
		$model = $this->_getModel();
		// check if form is posted
		if($this->_request->isPost())
		{
			$arrData = array();			
			$arrData['boxname'] 	= $this->_request->getPost('boxname');
			$arrData['content'] 	= $this->_request->getPost('content');			
			$id = $this->_request->getPost('id');
			$model->updateBox($arrData, $id);
			$this->_redirect('/admin/box/list');
		}
		else
			$this->_redirect('/admin/box/list');
	 }
	 
	 /**
	 * method: deleteAction
	 * Description: delete a box
	 */
	 public function deleteAction()
	 {
		$model = $this->_getModel();
		$model->deleteBox($this->_request->getParam('boxid'));
		$this->_redirect('/admin/box/list');
	 }
	 /**
	 * Get model box object	
	 */
	protected function _getModel()
    {
        require APPLICATION_PATH . '/modules/admin/models/Box.php';
		if ($this->_boxModel == null) 
		{                        
            $this->_boxModel = new Box();
        }
        return $this->_boxModel;                
    }
}
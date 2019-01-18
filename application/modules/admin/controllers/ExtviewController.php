<?php
/**  
* Admin_ViewController
* Description: define viewcontroller class for list, insert, update, delete view for the layout
* @author: tunm
*/ 
class Admin_ExtviewController extends Zend_Controller_Action 
{
	protected $_viewModel;
	
	public function init()
	{
		// do something
	}
	
	public function indexAction()
	{
		
	}
	
	/**
	 * listAction
	 * Description: show views list of layout
	 */
	 public function listAction()
	 {
		$model = $this->_getModel();
		$result = $model->getViews();	
		$page = (int)($this->_request->getParam('page'));   
		if(count($result) > 0)
		{ 
			Globals::doPaging($result, $page, $this->view);
		}
				
		$this->view->page = $page;
	 }
	
	/**
	 * method: new/edit view
	 * Description: create new or edit a view
	 */
	 public function editAction()
	 {
		$model = $this->_getModel();
		$viewId = $this->_request->getParam('viewid');   
		$this->view->objView = $model->getView($viewId);
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
			$arrData['extviewname'] 		= $this->_request->getPost('viewname');			
			$arrData['callback'] 		= $this->_request->getPost('callback');
			$arrData['require_option'] 		= $this->_request->getPost('require_option');
			$arrData['key']	 			= $this->_request->getPost('key');
			$arrData['value']	 		= $this->_request->getPost('value');
				
			// get array params
			$arrTmp = array();
			$n = count($arrData['key']);
			for($i = 0; $i < $n; $i++){
				$arrTmp[$arrData['key'][$i]] = $arrData['value'][$i];
			}
			unset($arrData['key']);
			unset($arrData['value']);
			$arrData['params'] = serialize($arrTmp);
			$id = $this->_request->getPost('id');			
			$model->updateView($arrData, $id);
			$this->_redirect('/admin/extview/list');
		}
		else
			$this->_redirect('/admin/extview/list');
	 }
	 
	 /**
	 * method: deleteAction
	 * Description: delete a view
	 */
	 public function deleteAction()
	 {
		$model = $this->_getModel();
		$model->deleteView($this->_request->getParam('viewid'));
		$this->_redirect('/admin/extview/list');
	 }
	 /**
	 * Get model layout object	
	 */
	protected function _getModel()
    {
        require APPLICATION_PATH . '/modules/admin/models/ExtView.php';
		if ($this->_viewModel == null) 
		{                        
            $this->_viewModel = new ExtView();
        }
        return $this->_viewModel;                
    }
}
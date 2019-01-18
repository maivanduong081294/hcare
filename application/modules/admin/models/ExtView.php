<?php
/**
 *  view model class
 *  @author Huytq2
 *
 */
class ExtView
{
	/**
	 * get all view from db
	 */
	public function getViews()
	{
		$_view = Business_BlockManagement_ExtViews::getInstance();
		$result = $_view->getList();
		return $result;
	}
	
	/**
	 * get view by viewid
	 * @return view object
	 */
	public function getView($viewId = '')
	{		
		$_view = Business_BlockManagement_ExtViews::getInstance();
		$result = $_view->getView($viewId);	 
		
		if(isset($result))
		{			
			$result = $this->parseArrayToObject($result);
			return $result;
		}
		else
		{
			$objView = new stdClass();
			$objView->extviewid 		= '';
			$objView->extviewname 		= '';
			$objView->callback  		= '';
			$objView->require_option	= '';
			$objView->params 		= '';
			return $objView;
		}		
	}
	
	/**
	 * update view information
	 */
	public function updateView($arrData, $id)
	{
		$_view = Business_BlockManagement_ExtViews::getInstance();
		if($id == '')
		{
			$_view->insertView($arrData);		
		}
		else
		{
			$_view->updateView($id, $arrData);				
		}
	}
	
	/**
	 * delete a view
	 */
	public function deleteView($viewid)
	{ 
		$_view = Business_BlockManagement_ExtViews::getInstance();
		$_view->deleteView($viewid);		
	}
	
//////////////////////// private functions //////////////////////////
			
	private function parseArrayToObject($array) {
		$object = new stdClass();
		if (is_array($array) && count($array) > 0) {
			foreach ($array as $name=>$value) {
				$name = strtolower(trim($name));
				if (!empty($name)) {
					$object->$name = $value;
				}
			}
		}
		return $object;
	}
}
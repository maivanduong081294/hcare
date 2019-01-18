<?php
/**
 *  view model class
 *  @author Huytq2
 *
 */
class Content
{
	/**
	 * get all view from db
	 */
	public function getContents()
	{
		$_content = Business_BlockManagement_Contents::getInstance();
		$result = $_content->getList();
		return $result;
	}
	
	/**
	 * get view by viewid
	 * @return view object
	 */
	public function getContent($contentId = '')
	{		
		$_content = Business_BlockManagement_Contents::getInstance();
		$result = $_content->getContent($contentId);	 
		if(isset($result))
		{			
			$result = $this->parseArrayToObject($result);
			return $result;
		}
		else
		{
			$objView = new stdClass();
			$objView->contentid 		= '';
			$objView->contentname 		= '';
			$objView->content  		= '';			
			return $objView;
		}		
	}
	
	/**
	 * update view information
	 */
	public function updateContent($arrData, $id)
	{
		$_content = Business_BlockManagement_Contents::getInstance();
		if($id == '')
		{
			$_content->insertContent($arrData);		
		}
		else
		{
			$_content->updateContent($id, $arrData);				
		}
	}
	
	/**
	 * delete a view
	 */
	public function deleteContent($contentid)
	{ 
		$_content = Business_BlockManagement_Contents::getInstance();
		$_content->deleteContent($contentid);		
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
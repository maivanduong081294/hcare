<?php
/**
 *  box model class
 *  @author Huytq2
 *
 */
class Box
{
	/**
	 * get all box from db
	 */
	public function getBoxes()
	{
		$_box = Business_BlockManagement_Boxes::getInstance();
		return $_box->getList();		
	}
	
	/**
	 * get box by boxid
	 * @return box object
	 */
	public function getBox($boxId = '')
	{
		$_box = Business_BlockManagement_Boxes::getInstance();
		$result = $_box->getBox($boxId);
		
		if(isset($result))
		{
			$result = $this->parseArrayToObject($result);
			return $result;
		}
		else
		{
			$objBox 		= new stdClass();
			$objBox->boxid 	= '';
			$objBox->boxname = '';
			$objBox->content = '';			
			return $objBox;
		}		
	}
	
	/**
	 * update box information
	 */
	public function updateBox($arrData, $id)
	{
		$_box = Business_BlockManagement_Boxes::getInstance();
		// get db object
		$db = Globals::getDbConnection('maindb');
		if($id == '')
		{
			$_box->insertBox($arrData);
		}
		else
		{
			$_box->updateBox($id,$arrData);											  
		}
	}
	
	/**
	 * delete a box
	 */
	public function deleteBox($boxid)
	{
		$_box = Business_BlockManagement_Boxes::getInstance();
		$_box->deleteBox($boxid);	
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
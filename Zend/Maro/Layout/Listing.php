<?php

class Maro_Layout_Listing
{
	private $_title = array();
	private $_fields = array();
	private $_data = array();
	private $_autoid = false;
		
	public function __construct($title = array(), $fields = array(), $data = array(), $autoid = false)
	{
		$this->_title = $title;
		$this->_fields = $fields;
		$this->_data = $data;
		$this->_autoid = $autoid;
		
	}
	
	public function renderList()
	{
		$view = new Zend_View();		
		$view->title = $this->_title;
		$view->fields = $this->_fields;
		$view->data = $this->_data;
		$view->autoid = $this->_autoid;
		$view->setBasePath(APPLICATION_PATH . "/modules/admin/views", "phtml");		
		$content = $view->render('commonlayout/listing.phtml');				
		return $content;
	}
}

?>
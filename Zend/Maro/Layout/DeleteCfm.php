<?php

class Maro_Layout_DeleteCfm
{
	private $_message = '';
	private $_yes_link = '';
	private $_no_link = '';

	
	public function __construct($message, $yes_link, $no_link)
	{
		$this->_message = $message;
		$this->_yes_link = $yes_link;
		$this->_no_link = $no_link;		
	}
	
	public function render()
	{
		$view = new Zend_View();		
		$view->message = $this->_message;
		$view->yes_link = $this->_yes_link;
		$view->no_link = $this->_no_link;		
		$view->setBasePath(APPLICATION_PATH . "/modules/admin/views", "phtml");		
		$content = $view->render('commonlayout/confirm.phtml');				
		return $content;
	}
	
	
}

?>
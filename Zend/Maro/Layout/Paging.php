<?php

class Maro_Layout_Paging
{
	private $_page_range = 3;
	private $_total_page = 0;
	private $_current_page = 0;
	private $_url = "";
	private $_template = 'paging.phtml';
	
	public function __construct($url,$total_page,$current_page,$page_range = 3,$template = 'paging.phtml')
	{
		$this->_url = $url;
		$this->_total_page = $total_page;
		$this->_current_page = $current_page;
		$this->_page_range = $page_range;
		$this->_template = $template;
	}
	
	public function render()
	{
		$view = new Zend_View();		
		$view->current_page = $this->_current_page;
		$view->total_page = $this->_total_page;
		$view->page_range = $this->_page_range;
		$view->url = $this->_url;
		$view->setBasePath(APPLICATION_PATH . "/modules/admin/views", "phtml");		
		$content = $view->render('commonlayout/' . $this->_template);				
		return $content;
	}
}

?>
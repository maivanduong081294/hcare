<?php

require_once 'Zend/Controller/Plugin/Abstract.php';
require_once APPLICATION_PATH.'/plugins/BlockManagement/BlockManagement.php';
require_once APPLICATION_PATH.'/etc/Globals.php';

class BlockManagementPlugin extends Zend_Controller_Plugin_Abstract
{
	protected $_modules_exclude = array();	
	
	public function __construct($module_exclude = array())
	{
		$this->_modules_exclude = $module_exclude;
		
	}
	
	public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
	{
		
		$module = strtoupper($request->getParam('module'));
		$controller = strtoupper($request->getParam('controller'));

		Zend_Layout::startMvc(APPLICATION_PATH . '/layouts/');
		if($module == 'ADMIN')
		{			
			$auth = Zend_Auth::getInstance(); 
			if($auth->hasIdentity())
			{ 
                BlockManager::setLayout('admin_layout');
			}
			else
			{ 
	            BlockManager::setLayout('admin_login');				
			}		
		}		
	}
	
	public function postDispatch(Zend_Controller_Request_Abstract $request)
	{	
		$module = $request->getParam('module');
		if(in_array($module,$this->_modules_exclude))
		{						
			return;
		}
		$_layout = Zend_Layout::getMvcInstance();
		if($_layout->isEnabled() === FALSE) return;		
		$this->executeBlocks();		
	}
	
	public function executeBlocks()
	{		
		$layout = BlockManager::getLayout();		
		if($layout == '') return;
		$sections = $this->getSections($layout);
		
		$folder_name = $this->getFoldername($layout);
		
		if($folder_name != '')
		{
			BlockManager::setLayout($folder_name . '/' . $layout);
		}

		
		//var_dump($sections);
		if(is_array($sections) && count($sections) > 0)
		{
			for($i=0;$i<count($sections);$i++)
			{
				BlockManager::setPostfix($sections[$i],'');
			}
		}
		$blocks = $this->getBlocks($layout);
		$this->processBlocks($blocks,$sections);		
	}	
	
	public function processBlocks($blocks,$sections)
	{
		if($blocks == null || !is_array($blocks) || count($blocks) == 0) return;
		$content = '';
		$module = '';	
		
		for($i=0;$i<count($blocks);$i++)
		{
			if(!in_array($blocks[$i]['section'],$sections)) continue;
			$module = $blocks[$i]['module'];

			if($module == 'extview')
			{
				$content = $this->processExtView($blocks[$i]);
			}
			else if($module == 'view')
			{
				$content = $this->processView($blocks[$i]);				
			}
			else if($module == 'box')
			{
				$content = $this->processBox($blocks[$i]);
			}
			
			if($content != null && $content != '')
			{
				BlockManager::setPostfix($blocks[$i]['section'],$content);
			}
		}		
	}
	
	public function processBox($block)
	{
		$content = '';
		if($block == null || !is_array($block))
		{
			return $content;
		}
		$delta = $block["delta"];
		
		$_box = Business_BlockManagement_Boxes::getInstance();
		$result = $_box->getBox($delta);
						
		if($result != NULL && is_array($result))
		{
			$content = $result["content"];
		}		
		return $content;
	}
	
	public function processExtView($block)
	{
		$content = '';
		
		if($block == null || !is_array($block))
		{
			return $content;
		}
		
		$delta = $block["delta"];
		
		$view_business = Business_BlockManagement_ExtViews::getInstance();
		$result = $view_business->getView($delta);
		
		if($result != null)
		{			
			$callback = $result['callback'];
			$require = $result['require_option'];
			$params_list = $result['params'];
			
			if($params_list != null && $params_list != '')
			{
				$params = unserialize($params_list);				
			}
			else
			{
				$params = array();
			}
			ProfilerLog::startLog("process extview callback='$callback'");
			$content = $this->renderExtView($callback, $params, $require);
			ProfilerLog::endLog("process extview callback='$callback'");
		}		
		return $content;
	}
	
	public function processView($block)
	{
		$content = '';
		
		if($block == null || !is_array($block))
		{
			return $content;
		}
		
		$delta = $block["delta"];
		
		$view_business = Business_BlockManagement_Views::getInstance();
		$result = $view_business->getView($delta);
				
		if($result != null)
		{
			$action = $result['action'];
			$controller = $result['controller'];
			$module = $result['module'];
			$params_list = $result['params'];
			
			if($params_list != null && $params_list != '')
			{
				$params = unserialize($params_list);				
			}
			else
			{
				$params = array();
			}
			ProfilerLog::startLog("process view module='$module' - controller='$controller' - action='$action'");
			$content = $this->renderView($action,$controller,$module,$params);
			ProfilerLog::endLog("process view module='$module' - controller='$controller' - action='$action'");
		}		
		return $content;		
	}
	
	public function getBlocks($layout)
	{
		$block_business = Business_BlockManagement_Blocks::getInstance();
		$result = $block_business->getListByLayout($layout);
		return $result;
	}	
	
	public function getFoldername($layout)
	{
		$layouts_business = Business_BlockManagement_Layouts::getInstance();
		$result = $layouts_business->getLayout($layout);
						
		if($result == null) return '';
		else
		{
			return $result['folder_name'];
		}
	}
	
	public function getSections($layout)
	{
		$layouts_business = Business_BlockManagement_Layouts::getInstance();
		$result = $layouts_business->getLayout($layout);
		
		$_return = array();
		
		if($result != null && is_array($result))
		{
			$_return = explode(',',$result['sections']);
		}		
		return $_return;		
	}
	
	public function renderExtView($callback, $params, $require = '')
	{
		$content = '';
		try
		{	
			if($require != '')
			{
				require_once $require;
			}
			
			$content = call_user_func_array($callback, $params);
			 
		}
		catch(Exception $e)
		{
			$content = '';
		}
		return $content;
	}
	
	public function renderView($action,$controller,$module,$params)
	{
		$content = "";
		try
		{
			$view = new Zend_View();
			//action, controller, module, params
			$content = $view->action($action,$controller,$module,$params);
		}
		catch(Exception $e)
		{
			$content = "<font color='red'><b>Error in renderView : $module, $controller, $action </b></font>";
			if(Globals::isDebug())
			{
				$content .= "<br>Exception : " . $e->getMessage();
			}
		}
		return $content;
	}
}

?>
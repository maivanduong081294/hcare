<?php
class Business_Ws_MenuItemHelper
{
	
	//const LINK_PATH = 'item/%s/%s/%s';		//item/vn/91/[postfix cua module]
		
	private static $_instance = null;

	private $_modules_list = array();
	private $_modules = array();
			
	function __construct()
	{
		$this->init();
	}
	
	/**
	 * get instance of Business_Ws_MenuItemHelper
	 *
	 * @return Business_Ws_MenuItemHelper
	 */
	public static function getInstance()
	{
		if(self::$_instance == null)
		{
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	private function init()
	{
		$configuration = Zend_Registry::get('configuration');
		
		if(isset($configuration->menuitem->modules))
		{
			$modules = $configuration->menuitem->modules;
			$modules = explode(',',$modules);
			
			if(is_array($modules) && count($modules) > 0)
			{
				for($i=0;$i<count($modules);$i++)
				{					
					$module_name = $modules[$i];
					if(isset($configuration->menuitem->$module_name))
					{
						$this->_modules_list[] = $module_name;
						$item = $configuration->menuitem->$module_name->toArray();
						$this->_modules[$module_name] = $item;						
					}
					
					
				}
			}
			
		}		
	}
	
	public function getListOfModule()
	{
		return $this->_modules_list;
	}
	
	public function getModule($modulename)
	{
//		if(array_key_exists($modulename, $this->_modules)) return $this->_modules[$modulename];
//		else return null;
		$configuration = Zend_Registry::get('configuration');
		if(isset($configuration->menuitem->$modulename))
		{
			$item = $configuration->menuitem->$modulename->toArray();
			return $item;
		}
		else return null;
	}
	
	public function callfunction($modulename,$function,$params = array())
	{
		$module = $this->getModule($modulename);
		
		$class = $module['class'];
		$content = '';
		try
		{
			$content = call_user_func_array($class . '::' . $function, $params);
		}
		catch(Exception $e)
		{
			$content = '';			
		}
		return $content;		
	}
	
	public function checkActive($itemid, $request)
	{
		
	}
	
	public function mi_getList($modulename, $lang,$itemid = null)
	{
		$params = array($lang,$itemid);
		return $this->callfunction($modulename,"mi_getList",$params);
	}
	
	public function mi_getAltTitle($modulename,$lang,$delta)
	{
		$params = array($lang,$delta);
		return $this->callfunction($modulename,"mi_getAltTitle",$params);
	}
	
	public function mi_getDetail($modulename,$lang,$delta)
	{
		$params = array($lang,$delta);
		return $this->callfunction($modulename,"mi_getDetail",$params);
	}
	
	public function mi_buildUrl($modulename,$lang,$delta,$itemid)
	{		
		$params = array($lang,$delta,$itemid);
		if($modulename == null) return '';	
		$url = $this->callfunction($modulename,"mi_buildUrl",$params);
				
		//$url = sprintf(self::LINK_PATH,$lang,$itemid,$post_fix_url);
		return $url;
	}
	
	public function renderMenuByCallback($menuname, $lang, $callback, $id = 0, $depth_stop = null, $depth = null, $start_for = '<ul>',$end_for = '</ul>')	
	{
		if($depth_stop != null && $depth > $depth_stop) return "";
		
		$_menuitem = Business_Ws_MenuItem::getInstance();
		$list = $_menuitem->getListFilter($menuname,$id,$lang,$depth);		
				
		$result = "";
		if($list != null && count($list) > 0)
		{
			$result .= $start_for;
			for($i=0;$i<count($list);$i++)
			{
				$params = array();
				$menuitem = $list[$i];
				$params['menuitem'] = $menuitem;
				$params['menuname'] = $menuname;
				$params['lang'] = $lang;
				$params['depth_stop'] = $depth_stop;
				
				$result .= call_user_func_array($callback,$params);
												
			}
			$result .= $end_for;
		}
		return $result;		
	}
}
?>
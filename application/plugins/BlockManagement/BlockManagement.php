<?php
class BlockManager
{
	var $_layout;
			
	public function __construct()
	{
		$this->_layout = Zend_Layout::getMvcInstance();		
	}	

	public static function set($section, $content)
	{
		$_blockmanager = new BlockManager();
		$_blockmanager->_set($section, $content);				
	}	
	public static function setPrefix($section, $content)
	{
		$_blockmanager = new BlockManager();
		$_blockmanager->_setPrefix($section, $content);				
	}
	public static function setPostfix($section, $content)
	{
		$_blockmanager = new BlockManager();
		$_blockmanager->_setPostfix($section, $content);				
	}
	
	public static function setLayoutPath($layoutpath)
	{
		$_blockmanager = new BlockManager();
		$_blockmanager->setLayoutPath($layoutpath);
	}
	
	public static function setLayout($layout)
	{
		$_blockmanager = new BlockManager();
		$_blockmanager->_setLayout($layout);
	}
	
	public static function getLayout()
	{
		$_blockmanager = new BlockManager();
		return $_blockmanager->_getLayout();
	}
	
	public function _getLayout()
	{
		return $this->_layout->getLayout();
	}
	
	public function _setLayoutPath($layoutpath)
	{
		$this->_layout->setLayoutPath($layoutpath);
	}
	
	public function _setLayout($layout)
	{
		$this->_layout->setLayout($layout);
	}
	
	public function _set($section,$content)
	{
		if(!isset($this->_layout->_section) && !is_array($this->_layout->_section))
		{
			$arr = array();
			$arr[$section] = $content;
			$this->_layout->_section = $arr;
		}
		else
		{
			$arr = $this->_layout->_section;
			$arr[$section] = $content;
			$this->_layout->_section = $arr;		
		}
	}
	
	public function _setPrefix($section, $content)
	{
		if(!isset($this->_layout->_section) && !is_array($this->_layout->_section))
		{
			$arr = array();
			$arr[$section] = $content;
			$this->_layout->_section = $arr;
		}
		else
		{
			$arr = $this->_layout->_section;
			$arr[$section] = $content.chr(10).$arr[$section];
			$this->_layout->_section = $arr;		
		}
	}
	
	public function _setPostfix($section, $content)
	{
		if(!isset($this->_layout->_section) && !is_array($this->_layout->_section))
		{
			$arr = array();
			$arr[$section] = $content;
			$this->_layout->_section = $arr;
		}
		else
		{
			$arr = $this->_layout->_section;
			if(isset($arr[$section]))
			{
				$arr[$section] .= chr(10).$content;						
			}
			else
			{
				$arr[$section] = $content;			
			}
			
			$this->_layout->_section = $arr;		
		}
	}
}

?>
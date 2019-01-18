<?php

class Maro_Paging_Common implements Maro_Paging_Interface
{
	//define private variables
	protected $_paging_max_page = 10;
	protected $_paging_num_records = 100;
	protected $_paging_cache_key = ".paging.%s";
	protected $_paging_time_exire = 0;//defaut no expire
	
	//define adapter
	protected $_cache;
	protected $_callback = null;	//function call prototype : function getData($offset, $records, ..... )
	
	
	function __construct($cache, $callback = null, $paging_max_page = null, $paging_num_records = null, $time_expire = null)
	{		
		if($paging_max_page != null)		
			$this->_paging_max_page = $paging_max_page;
				
		if($paging_num_records != null)		
			$this->_paging_num_records = $paging_num_records;		
		
		if($callback != null)		
			$this->_callback = $callback;
				
		if($cache != null)
			$this->_cache = $cache;
		
		if($time_expire != null)
			$this->_paging_time_exire = $time_expire;
			
	}
		
	public function getData($offset, $records, $keyprefix, $params = array())
	{
		$params['keyprefix'] = $keyprefix;
		
		$page_array = $this->_getWhichPage($offset, $records);
		
		$result = $this->_getResultMultiPage($page_array, $params);

		$_return = array();
		if($result != null && is_array($result))
		{
			$_return = $this->_getResult($result, $offset, $records);
		}
		
		return $_return;
	}
	
	public function clearCachePaging($keyprefix)
	{
		for($i=0;$i<=$this->_paging_max_page;$i++)
		{
			$params	= array(
				'keyprefix' => $keyprefix
			);
			$key = $this->_getPagingKey($i, $params);
			$this->_cache->deleteCache($key);
		}
	}
	
	//private functions
	
	private function _getResult($result, $offset, $records)
	{	
		
		//echo "<pre>";
		//print_r($result);
		//echo "</pre>";
		
		$page_array = $this->_getWhichPage($offset,$records);
		
		$_return = array();
		
		$page_start = (int)($offset / $this->_paging_num_records);
		$page_end = (int)(($offset + $records) / $this->_paging_num_records);

		//echo "page_start=$page_start - page_end=$page_end<br>";
		//var_dump($result);
				
		if($page_start < $page_end)
		{											
			for($i=0;$i<count($page_array);$i++)
			{				
				$page = $page_array[$i];
				//echo "page=$page<br>";
				$_local = $result[$page];
								
				if($i==0)		//first page
				{				
					$start = $offset - ($page_array[$i] * $this->_paging_num_records);					
					$end  = count($_local);												
				}						
				else if($i == count($page_array) - 1)
				{				
					$start = 0;
					$end = $offset + $records  - ($page_array[$i] * $this->_paging_num_records);
				}
				else
				{
					$start = 0;
					$end = count($_local); 				
				}
				
				for($j=$start;$j<$end;$j++)
				{
					if($j < count($_local)) $_return[] = $_local[$j];
				}
				
			}
		}
		else
		{			
			$page = $page_array[0];
			//echo "page=$page<br>"; 
			//echo "offset=$offset<br>";			
			$start = $offset - ($page * $this->_paging_num_records);
			$end = $start + $records;
			
			//echo "start=$start<br>";
			//echo "end=$end<br>";
						
			$_local = $result[$page];
						
			if($_local != null)
			{
				for($i=$start;$i<$end;$i++)
				{
					if($i < count($_local)) $_return[] = $_local[$i];
				}
			}
		}
				
		return $_return;
	}
	
	private function _getResultMultiPage($pages = array(), $params = array())
	{
		$_return = array();
		//duyet qua cac trang se lay
		if(is_array($pages) && count($pages) > 0)
		{
			//chuan bi mang cac key
			for($i=0;$i<count($pages);$i++)
			{
				$page = $pages[$i];
				$key_array[] = $this->_getPagingKey($page,$params);
			}
			
			//get multicache
			$result = $this->_cache->getMultiCache($key_array);			
			//check result
			if($result != null)
			{
				//duyet tung trang
				for($i=0;$i<count($pages);$i++)
				{
					$page = $pages[$i];
					$key = $this->_getPagingKey($page, $params);
					if(array_key_exists($key, $result))
					{
						$_return[$page] = $result[$key];
					}
					else
					{
						$result_miss_cache = $this->_callbackParentFunction($key, $page, $params);
						
						if(!is_null($result_miss_cache) && is_array($result_miss_cache))
						{
							$_return[$page] = $result_miss_cache;
						}
						else
							$_return[$page] = null;
					}
				}
			}
			else//miss cache all
			{
				for($i=0;$i<count($pages);$i++)
				{					
					$page = $pages[$i];
					$key = $this->_getPagingKey($page, $params);
					$result_miss_cache = $this->_callbackParentFunction($key, $page, $params);
					
					if(!is_null($result_miss_cache) && is_array($result_miss_cache))
					{						
						$_return[$page] = $result_miss_cache;
					}
					else
						$_return[$page] = null;
				}
			}
		}		
		return $_return;
	}
	
	//ham xem xet voi offset va record nhu vay thi lay nhung page nao
	private function _getWhichPage($offset, $records)
	{
		$page_start = 0;
		$page_end = 0;
		
		$page_start =  (int)($offset / $this->_paging_num_records) ;
		$page_end = (int)(($offset + $records - 1) / $this->_paging_num_records);
		$return = array();		
		for($i=$page_start;$i<=$page_end;$i++)
		{
			$return[] = $i;		
		}		
		return $return;
	}
	
	private function _callbackParentFunction($key, $page, $params = array())
	{
		unset($params['keyprefix']);
		//if($page > 0) $page = $page - 1;		
		$params['page'] = $page;	
		$result = call_user_func_array($this->_callback, $params);		
		if($page <= $this->_paging_max_page && !is_null($result) && is_array($result))
		{
			//setcache
			if($this->_paging_time_exire > 0)$this->_cache->setCache($key, $result,$this->_paging_time_exire);
			elseif($this->_paging_time_exire == 0) $this->_cache->setCache($key, $result);
		}		
		return $result;
	}
	
	private function _getPagingKey($page, $params)
	{
		$keyprefix = $params['keyprefix'];
		$key = $keyprefix . $this->_paging_cache_key;
		return sprintf($key,$page);
	}
}

?>
<?php
class Maro_Rest_Server
{
	private $_class='';
	private $_method='';
	protected static $magicMethods = array(
        '__construct',
        '__destruct',
        '__get',
        '__set',
        '__call',
        '__sleep',
        '__wakeup',
        '__isset',
        '__unset',
        '__tostring',
        '__clone',
        '__set_state',
    );
	public static function lowerCase(&$value, &$key)
    {
        return $value = strtolower($value);
    }
	public function setClass($className)
	{
		$this->_class=$className;
	}
	public function handle()
	{
		try
		{
			$funcArg=array();
			$result=array();
			$errorCode=0;
			$errorMessage="Successful.";
			$request = $_REQUEST;
			//check xem co request co bien method ko
			 if (isset($_REQUEST['method'])) 
			 {
				$_method=$_REQUEST['method'];
				$class = new ReflectionClass($this->_class);
				if($class->hasMethod($_method)&&!in_array($_method, self::$magicMethods))
				{
					$request_keys = array_keys($request);
					array_walk($request_keys, array(__CLASS__, "lowerCase"));
					$request = array_combine($request_keys, $request);

					$method=$class->getMethod($_method);
					$params=$method->getParameters();
					for($i=0;$i<count($params);$i++)
					{
						$paramName=strtolower($params[$i]->getName());
						$paramIndex=$params[$i]->getPosition();
						if (!isset($request[$paramName])) 
						{
							if ($params[$i]->isDefaultValueAvailable()) 
							{
								$paramValue = $params[$i]->getDefaultValue();
							} 
							else 
							{
								throw new Exception('Required parameter "'.$paramName.'" is not specified.');
							}
						} 
						else 
						{
							$paramValue = $request[$paramName];
						}
						$funcArg[$paramIndex]=$paramValue;
					}
					if ($method->isStatic())
					{
						$result=$method->invokeArgs(NULL, $funcArg);
					} 
					elseif($method->isPublic())
					{
						$instance=$class->newInstance();
						$result=$method->invokeArgs($instance, $funcArg);
					}
				}
				else 
				{
					throw new Exception('Request method not found');
				}

			 }
			 else
			 {
				throw new Exception('No method given.');
			 }
		}
		catch(Exception $e)
		{
			$errorMessage=$e->getMessage();
			$errorCode=1;
			$result="";
		}
		$data=json_encode($result);
		$errorCode=json_encode($errorCode);
		$errorMessage=json_encode($errorMessage);
		if(isset($_REQUEST['callback'])) 
		{
			$return		= '%s({"error_code":%s,"error_message":%s,"data":%s})';
			$return=sprintf($return,$_REQUEST['callback'],$errorCode,$errorMessage,$data);
		}
		else
		{
			$return		= '{"error_code":%s,"error_message":%s,"data":%s}';
			$return=sprintf($return,$errorCode,$errorMessage,$data);	
		}
		echo $return;
	}
}
?>
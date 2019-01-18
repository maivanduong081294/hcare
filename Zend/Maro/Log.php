<?php
class Maro_Log
{
	static $log_path = null;
	static $logger = null;

	public static function initLog($path,$enable,$level)
	{
		if($enable)
		{
			self::$logger = new Zend_Log();
			self::$logger->addWriter(new Zend_Log_Writer_Stream($path));
			if(isset($level))
			{
				$filter = new Zend_Log_Filter_Priority($level);
				$logger->addFilter($filter);
			}

		}
	}

	public static function shutdown()
	{
		self::$logger = null;
	}

	public static function writeLog($content)
	{
		if(self::$logger != null)
		{
			$content .= "\n";
			self::$logger->info($content);
		}
	}

	public static function writeERR($error)
	{
		if(self::$logger != null)
		{
			$content .= "\n";
			self::$logger->err($error);
		}
	}

	public static function writeCRIT($error)
	{
		if(self::$logger != null)
		{
			$content .= "\n";
			self::$logger->crit($error);
		}
	}
}

?>

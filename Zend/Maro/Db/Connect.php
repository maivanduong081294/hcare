<?php

class Maro_Db_Connect
{
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $type
	 * @return Maro_Cache_Interface
	 */
	public static function factory($type = 'windows',$options)
	{	
            if(APP_ENV=="development"){
                return new Maro_Db_Adapter_Windows();
            }else{
                return new Maro_Db_Adapter_Linux($options);
            }
	}
}

?>
<?php
class Maro_Rest_Jsonp extends Zend_Rest_Server
{
	protected function _handleStruct($struct)
    {
		$this->_headers=array();
		return Zend_Json::encode($struct);
	}
}
?>
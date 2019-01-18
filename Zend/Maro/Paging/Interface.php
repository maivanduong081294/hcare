<?php

interface Maro_Paging_Interface
{
	public function getData($offset, $records, $keyprefix, $params = array());
	public function clearCachePaging($keyprefix);
}

?>
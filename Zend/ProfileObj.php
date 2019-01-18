<?php
class ProfileObj
{
       private $_id = '';
       private $_time = 0;
       private $_start = 0;
       private $_end = 0;
	   private $_log_scribe = false;
	   private $_log_scribe_id = -1;

       function __construct($id,$start,$log_scribe = false,$log_scribe_id = -1)
       {
	       $this->_id = $id;
	       $this->_start = $start;
		   $this->_log_scribe = $log_scribe;
		   $this->_log_scribe_id = $log_scribe_id;
       }

       function setEndTime($endtime)
       {
	       $this->_end = $endtime;
	       $diff = ($this->_end - $this->_start);
	       $this->_time = $diff;
       }

		function getLogScribeId()
		{
			return $this->_log_scribe_id;
		}

	   function getIsLogScribe()
	   {
		   return $this->_log_scribe;
	   }

       function getTime()
       {
	       return $this->_time;
       }

       function getID()
       {
	       return $this->_id;
       }

       function getStartTime()
       {
	       return $this->_start;
       }

       function getEndTime()
       {
	       return $this->_end;
       }

}
?>

<?php
class ProfilerLog
{
       public static $_array = array();
       private static $_totaltime = 0;
	   public static $_debug = true;

       public static function startLog($id,$log_scribe = false, $log_scribe_id = 0)
       {
	       if(self::$_debug == false) return;
	       if($id == '') return;
	       $starttime = gettimeofday(true);
	       $obj_profile = new ProfileObj($id,$starttime,$log_scribe,$log_scribe_id);
	       self::$_array[$id] = $obj_profile;
       }

       public static function endLog($id)
       {
	       if(self::$_debug == false) return;
	       if($id == '') return;
	       if(isset(self::$_array[$id]))
	       {
		       $obj_profile = self::$_array[$id];
		       $endtime = gettimeofday(true);
		       $obj_profile->setEndTime($endtime);
		       self::$_totaltime += $obj_profile->getTime();
	       }
       }

	   public static function sort()
	   {
		   function _pf_cmp_name($a,$b)
			{
				$x = $a->getID();
				$y = $b->getID();

				if($x == $y) return 0;
				return ($x > $y) ? 1 : -1;
			}

			function _pf_cmp_time($a,$b)
			{
				$x = $a->getTime();
				$y = $b->getTime();
				if($x == $y) return 0;
				return ($x > $y) ? 1 : -1;
			}

		   if(isset($_REQUEST['debug_p_sort'])) {
				$sort = $_REQUEST['debug_p_sort'];

				if($sort == 'name') {
					usort(self::$_array,_pf_cmp_name);
				}
				elseif($sort == 'time') {
					usort(self::$_array,_pf_cmp_time);
				}				
		   }
		   else {
			   usort(self::$_array,_pf_cmp_name);
		   }
	   }
	   
       public static function dumpLog()
       {
	       if(self::$_debug == false) return;

		   self::sort();

	       $_return = "";
	       $count = 1;
			if(is_array(self::$_array) && count(self::$_array) > 0)
			{
				   $_return = '<table style="border-collapse: collapse;" border="1" cellpadding="5" cellspacing="5"><tbody>'
							   . '<tr><th colspan="3" bgcolor="#dddddd">Module Log Profiler</th></tr>'
							   . '<tr><th width="50">No.</th><th>ID</th><th>Time elapsed in secs</th></tr>';

				   $_return .= '<tr><td colspan="3" align="left"><b>Total time elapsed : ' . number_format(self::$_totaltime,9) . ' secs</td></tr>';

				   foreach(self::$_array as $key => $value)
				   {
					   $obj_profiler = $value;
					   $_return .= '<tr><td align="center">' . ($count++) . '</td><td align="left">' . $obj_profiler->getID();
					   if($obj_profiler->getIsLogScribe()) $_return .= "(log to scribe - " .$obj_profiler->getLogScribeId() ." )";
					   $_return .= '</td>'
								   . '<td align="left">' . number_format($obj_profiler->getTime(),9) . "</td></tr>";

				   }

				   $_return .= "</table>";
			}
			return $_return;
       }

 }

?>

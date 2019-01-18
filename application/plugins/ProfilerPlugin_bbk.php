<?php

require_once APPLICATION_PATH . "/etc/Globals.php";	

class ProfilerPlugin extends Zend_Controller_Plugin_Abstract
{
    private $_identity;
	private static $_time_start_render = 0;
	private static $_time_end_render = 0;
        
        public static function getImportStatic() {
            $url = Business_Common_Utils::getCurrentURL();            
            if (strpos($url, "/import/")!==false) {
                $css = "<link rel=\"stylesheet\" href=\"/import/css/style.css\"><link rel=\"stylesheet\" href=\"/import/css/animate.css\"><link href=\"//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/css/select2.min.css\" rel=\"stylesheet\" />";
                $js = "<script type=\"text/javascript\" src=\"/import/js/jquery.slimscroll.js\"></script><script type=\"text/javascript\" src=\"/import/js/script.js\"></script><script src=\"//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/js/select2.min.js\"></script>";
                return $css.$js;
            }
        }
        
	public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
	{ 	
                $auth = Zend_Auth::getInstance();
                $identity = (array) $auth->getIdentity();
                $this->_identity = $identity;
                $access = $this->_identity["idregency"];
		$module      = $this->_request->getModuleName();
                $controller = $this->_request->getControllerName();
                $action =$this->_request->getActionName();
                $_accesslink = Business_Addon_AccessByLink::getInstance();
                $detail = $_accesslink->getDetail($module,$controller,$action);
                if (APP_ENV == "development") {
                    return true;
                }
                if($module =="admin"){
                    return true;
                }
                if($detail == null){
                    return true;
                }
                if($module =="hnam" && $controller =="profile"){
                    return true;
                }
                if($access == 39 || $access == 40 || $access == 41){
                    return true;
                }
                if($detail != null){
                    $ok=0;
                    $_arr_access = $detail["people"];
                    $arr_access = explode(",", $_arr_access);
                    
                    $userid = $this->_identity["userid"];    
                    $_arr_userid = $detail["userid"];
                    $arr_userid = explode(",", $_arr_userid);
                    if(in_array($access, $arr_access) !== FALSE){
                        $ok=1;
                    }
                    if($_arr_userid !=0){
                        if(in_array($userid, $arr_userid) !== FALSE){
                            $ok=1;
                        }
                    }
                    if($controller != "auth"){
                        Business_Addon_Options::getInstance()->islogin();
                        if($this->_request->isPost()){
                            die('Bạn chưa đăng nhập.Vui lòng đăng nhập.');
                        }
                        header('Location: /admin');
                    }
                    
                    if($ok ==0){
                        header('Location: /admin');
                    }
                    
                }
		if(Globals::isDebug())
		{
			self::$_time_start_render = gettimeofday(true);
		}
		
	}

	public function dispatchLoopShutdown()
	{  			
		if(Globals::isDebug())
		{
			
			$print = "<div style='float:left;'>";					
			self::$_time_end_render = gettimeofday(true);			
			
			$print .= $this->dumpPageRenderProfiler(self::$_time_start_render, self::$_time_end_render);	
			
			$print .= $this->dumpMemoryUsageProfler();
			
			$print .= $this->dumpProfilerLog();
			
			$print .= $this->dumpCacheProfiler();
			
			$print .= $this->dumpDbProfiler();
			
			$print .= "</div>";
			
			$this->getResponse()->appendBody($print);			
		}		
	}
	
	
	/////////////////////// private functions ///////////////////////////////
	
	private function dumpPageRenderProfiler($start_time, $end_time)
	{
		$print = "";
		
		$diff = ($end_time - $start_time);
		
		$print .= '<br><table border=1 cellspacing="5" cellpadding="5" style="border-collapse:collapse">';
		$print .= '<tr><td><b>page render time : ' . $diff . ' secs</b></td></tr>';
		$print .= "</table><br>";
		return $print;
	}
	
	private function dumpMemoryUsageProfler()
	{
		$print = '';
		$print .= '<br><table border=1 cellspacing="5" cellpadding="5" style="border-collapse:collapse">';
		$print .= '<tr><td><b>Php Memory usage: ' . $this->adaptMB(@memory_get_usage()) . ' (' . $this->adaptMB(@memory_get_usage(true)). ' ) Mbytes</b></td></tr>';
		$print .= "</table>";
		return $print;
	}
	
	private function dumpProfilerLog()
	{
		$print = '<br>';
		$print .= ProfilerLog::dumpLog();		
		return $print;
	}
	
	private function adaptMB($value)
	{
		$size = $value / (1024 * 1024);
		return number_format($size,2);
	}
	
	private function dumpCacheProfiler()
	{
		$print = "";
		
		$print .= Maro_Cache_MemGlobalCache::getAllProfilerData();
		
		return $print;
	}
	
	private function dumpDbProfiler()
	{
		$print = "";
		$arr = GlobalsDB::$arrDB;
		if(is_array($arr) && count($arr) > 0)
		{
			$print .= '<br><br><table border=1 cellspacing="5" cellpadding="5" style="border-collapse:collapse">'
					. "<tr><th colspan=3 bgcolor='#dddddd'>Database Profiler</th></tr>"
					. "<tr><th width=50>No.</th><th>Query</th><th>Time elapsed in secs</th>";
			if(is_array($arr) && count($arr) > 0)
			{
				foreach($arr as $key => $value)
				{
					$count = 1;
					$profiler = $value->getProfiler();
					$print .= "<tr><td colspan='3' align='left'><b>debug profiler for db " . $key . "</b> --- ";
					$print .= "Total query : " . $profiler->getTotalNumQueries() . " ---- Total time elapsed : " 
							. number_format($profiler->getTotalElapsedSecs(),9) . " seconds"; 
					$print .= "</td></tr>";
					$profiler_arr = $profiler->getQueryProfiles();
					if(is_array($profiler_arr) && count($profiler_arr) > 0)
					{ 
						foreach ($profiler_arr as $query)
						{
							$print .= "<tr><td>" . $count++ ."</td><td align='left'>".$query->getQuery() ."</td><td align='left'>" . number_format($query->getElapsedSecs(),9) . "</td></tr>";				 	    
				    	}
					}			
				}
			}
			$print .= '</table><br><br><br>';			
		}
		else
		{
			$print .= "no database instance created.<br><br>";
		}
		return $print;
	}
	

}
?>

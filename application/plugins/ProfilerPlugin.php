<?php
require_once APPLICATION_PATH . "/etc/Globals.php";

class ProfilerPlugin extends Zend_Controller_Plugin_Abstract
{

    private static $_time_start_render = 0;

    private static $_time_end_render = 0;

    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
        $cache = GlobalCache::getCacheInstance('ws');    
        if(isset($_REQUEST['d']) and $_REQUEST['d']==10) {
            $cache->flushAll();
        }
    }

    public function dispatchLoopShutdown()
    {

        
            
       if (APP_ENV != 'production' and $_REQUEST['r']==1) {
           
        $Global=array();
        $Global['jquery-ui.min.js']=ROOT_PATH . "/www/v5/js/jquery-ui.min.js";
        $Global['services.js']=ROOT_PATH . "/www/v5/js/services.js";
        $Global['search.js']=ROOT_PATH . "/www/v5/js/search.js";
        $Global['cookie.js']=ROOT_PATH . "/www/v5/js/cookie.js";
        $Global['modernizr.custom.js']=ROOT_PATH . "/www/v5/lib/modernizr_menu/js/modernizr.custom.js";
        $Global['jquery.validate.min.js']=ROOT_PATH . "/www/v5/lib/jquery_validation/dist/jquery.validate.min.js";
        $Global['jquery.dlmenu.js']=ROOT_PATH . "/www/v5/lib/modernizr_menu/js/jquery.dlmenu.js";
        $Global['bootstrap.min.js']=ROOT_PATH . "/www/v5/js/bootstrap.min.js";
        $Global['home.js']=ROOT_PATH . "/www/v5/js/home.js";
        $Global['global.js']=ROOT_PATH . "/www/v5/js/global.js";
        $Global['swiper.min.js']=ROOT_PATH . "/www/v5/js/swiper.min.js";
        $data=array();
        $data=$Global;  
        foreach ($data as $keyJs=>$val){
                $buffer=SEOPlugin::file_get_curl($val);
                // Remove comments
                $buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
                // Remove space after colons
                $buffer = str_replace(': ', ':', $buffer);
                // Remove whitespace
                $buffer = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $buffer);
                $temp[$keyJs]=$buffer;
               
        }  

        $data=  implode('', $temp);
        
       $file=ROOT_PATH . "/www/v5/js/jquery1.js"; 
       $myfile = fopen($file, "w+") or die("Unable to open file!");
       $txt = $data;
       fwrite($myfile, $txt);
       fclose($myfile);      
           
           
       $fileCss[]=Globals::getBaseUrl()."/v5/css/swiper.min.css";         
       $data=  SEOPlugin::minCSS($fileCss,1);  
       $file=ROOT_PATH . "/www/v5/css/min1.css"; 
       $myfile = fopen($file, "w+") or die("Unable to open file!");
       $txt = $data;
       fwrite($myfile, $txt);
       fclose($myfile);    
       
       
       
         }
        
        
        // check acl
//         $controller = Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
//         $moudels = Zend_Controller_Front::getInstance()->getRequest()->getModuleName();
//         $action = Zend_Controller_Front::getInstance()->getRequest()->getActionName();
//         $accbylink = Business_Addon_AccessByLinkWeb::getInstance();
//         // var_dump($controller."-".$moudels."-".$action);
//         $acl = $accbylink->getDetail($moudels, $controller, $action);
//         // $acl['userid'] ,$acl['people']
//         $auth = Zend_Auth::getInstance();
//         $identity = $auth->getIdentity();
//         $nhom = explode(',', $acl['people']);
//         // var_dump($nhom); exit;
        
//         if ($acl['userid'] != 0) { // id dc phep vao
            
//             $detail = Business_Common_Users::getInstance()->getUserByUid($acl['userid']); // get nhom cho id duoc phep vao
//             $nhom = array_diff($nhom, array(
//                 $detail['idregency']
//             )); // bo nhom da co 1 id dc phep vao
//             if ($acl['userid'] != $identity->userid) { // list nhom dc phep vao
//                 if ($acl['people'] != null) { // list nhom dc phep vao
//                     if (! in_array($identity->idregency, $nhom) && isset($identity) && $identity->idregency != 1)
//                         header('Location: /admin/user');
//                 }
//             }
//         } else // id = 0 check nhom
// {
//             if ($acl['people'] != null) { // list nhom dc phep vao
//                 if (! in_array($identity->idregency, $nhom) && isset($identity) && $identity->idregency != 1)
//                     header('Location: /admin/user');
//             }
//         }
        
        
        
        
        if (Globals::isDebug()) {
            
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
    
    // ///////////////////// private functions ///////////////////////////////
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
        $print .= '<tr><td><b>Php Memory usage: ' . $this->adaptMB(@memory_get_usage()) . ' (' . $this->adaptMB(@memory_get_usage(true)) . ' ) Mbytes</b></td></tr>';
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
        return number_format($size, 2);
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
        if (is_array($arr) && count($arr) > 0) {
            $print .= '<br><br><table border=1 cellspacing="5" cellpadding="5" style="border-collapse:collapse">' . "<tr><th colspan=3 bgcolor='#dddddd'>Database Profiler</th></tr>" . "<tr><th width=50>No.</th><th>Query</th><th>Time elapsed in secs</th>";
            if (is_array($arr) && count($arr) > 0) {
                foreach ($arr as $key => $value) {
                    $count = 1;
                    $profiler = $value->getProfiler();
                    $print .= "<tr><td colspan='3' align='left'><b>debug profiler for db " . $key . "</b> --- ";
                    $print .= "Total query : " . $profiler->getTotalNumQueries() . " ---- Total time elapsed : " . number_format($profiler->getTotalElapsedSecs(), 9) . " seconds";
                    $print .= "</td></tr>";
                    $profiler_arr = $profiler->getQueryProfiles();
                    if (is_array($profiler_arr) && count($profiler_arr) > 0) {
                        foreach ($profiler_arr as $query) {
                            $print .= "<tr><td>" . $count ++ . "</td><td align='left'>" . $query->getQuery() . "</td><td align='left'>" . number_format($query->getElapsedSecs(), 9) . "</td></tr>";
                        }
                    }
                }
            }
            $print .= '</table><br><br><br>';
        } else {
            $print .= "no database instance created.<br><br>";
        }
        return $print;
    }
}
?>

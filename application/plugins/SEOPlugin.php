<?php

require_once APPLICATION_PATH . "/etc/Globals.php";

class SEOPlugin extends Zend_Controller_Plugin_Abstract {

    static $_title = "__seo_title";
    static $_des = "__seo_description";
    static $_key = "__seo_keywords";
    static $_social_img = 'https://www.hnammobile.com//fileext//loi-doi-ngay-lap-tuc-hnammobile-2017.png';
    static $_social_url = '';


   public static function  file_get_curl( $url ) {
    
              $arrContextOptions=array(
                  "ssl"=>array(
                      "verify_peer"=>false,
                      "verify_peer_name"=>false,
                  ),
              );  

              $response = file_get_contents( $url , false, stream_context_create($arrContextOptions));
          return $response;

        }



    public static function isDevide(){
        $tablet_browser = 0;
        $mobile_browser = 0;
         
        if (preg_match('/(tablet|ipad|playbook)|(android(?!.*(mobi|opera mini)))/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
            $tablet_browser++;
        }
         
        if (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|android|iemobile)/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
            $mobile_browser++;
        }
         
        if ((strpos(strtolower($_SERVER['HTTP_ACCEPT']),'application/vnd.wap.xhtml+xml') > 0) or ((isset($_SERVER['HTTP_X_WAP_PROFILE']) or isset($_SERVER['HTTP_PROFILE'])))) {
            $mobile_browser++;
        }
         
        $mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'], 0, 4));
        $mobile_agents = array(
            'w3c ','acs-','alav','alca','amoi','audi','avan','benq','bird','blac',
            'blaz','brew','cell','cldc','cmd-','dang','doco','eric','hipt','inno',
            'ipaq','java','jigs','kddi','keji','leno','lg-c','lg-d','lg-g','lge-',
            'maui','maxo','midp','mits','mmef','mobi','mot-','moto','mwbp','nec-',
            'newt','noki','palm','pana','pant','phil','play','port','prox',
            'qwap','sage','sams','sany','sch-','sec-','send','seri','sgh-','shar',
            'sie-','siem','smal','smar','sony','sph-','symb','t-mo','teli','tim-',
            'tosh','tsm-','upg1','upsi','vk-v','voda','wap-','wapa','wapi','wapp',
            'wapr','webc','winw','winw','xda ','xda-');
         
        if (in_array($mobile_ua,$mobile_agents)) {
            $mobile_browser++;
        }
         
        if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']),'opera mini') > 0) {
            $mobile_browser++;
            //Check for tablets on opera mini alternative headers
            $stock_ua = strtolower(isset($_SERVER['HTTP_X_OPERAMINI_PHONE_UA'])?$_SERVER['HTTP_X_OPERAMINI_PHONE_UA']:(isset($_SERVER['HTTP_DEVICE_STOCK_UA'])?$_SERVER['HTTP_DEVICE_STOCK_UA']:''));
            if (preg_match('/(tablet|ipad|playbook)|(android(?!.*mobile))/i', $stock_ua)) {
              $tablet_browser++;
            }
        }
         
        if ($tablet_browser > 0) {
           // do something for tablet devices
           // print 'is tablet';
              return 2;
        }
        else if ($mobile_browser > 0) {
           // do something for mobile devices
           //print 'is mobile';
              return 1;
        }
        else {
           // do something for everything else
          // print 'is desktop';
            return 0;
        }   


    } 



   public static  function isMobile() {
     if(self::isDevide()==0)
        return 0;
     else
       return 1;

}

   public static  function isMobile1() {
       // tru table ra 
    return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|palm|phone|pie|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
}
   public static  function checkCachedHtml($currentUrl) {
        $cache = GlobalCache::getCacheInstance('ws');
        $key=  "html-".SEOPlugin::isMobile1().'-'.SEOPlugin::isMobile().'-'.md5($currentUrl);
        $result = $cache->getCache($key);
        // KHÔNG TÌM THẤY TRANG 404 NOT FOUND
        if(strpos(strip_tags($result),'TRANG404 NOT FOUND'))
        {
             $cache = GlobalCache::getCacheInstance('ws');
             $cache->flushAll();
              return false;
        }
        if($result===false)
              return false;
        else
            return true;
       
   }

   public static  function minify_output($content){
        //remove redundant (white-space) characters
        $replace = array(
            //remove tabs before and after HTML tags
            '/\>[^\S ]+/s'   => '>',
            '/[^\S ]+\</s'   => '<',
            //shorten multiple whitespace sequences; keep new-line characters because they matter in JS!!!
            '/([\t ])+/s'  => ' ',
            //remove leading and trailing spaces
            '/^([\t ])+/m' => '',
            '/([\t ])+$/m' => '',
            // remove JS line comments (simple only); do NOT remove lines containing URL (e.g. 'src="https://server.com/"')!!!
            '~//[a-zA-Z0-9 ]+$~m' => '',
            //remove empty lines (sequence of line-end and white-space characters)
            '/[\r\n]+([\t ]?[\r\n]+)+/s'  => "\n",
            //remove empty lines (between HTML tags); cannot remove just any line-end characters because in inline JS they can matter!
            '/\>[\r\n\t ]+\</s'    => '><',
            //remove "empty" lines containing only JS's block end character; join with next line (e.g. "}\n}\n</script>" --> "}}</script>"
            '/}[\r\n\t ]+/s'  => '}',
            '/}[\r\n\t ]+,[\r\n\t ]+/s'  => '},',
            //remove new-line after JS's function or condition start; join with next line
            '/\)[\r\n\t ]?{[\r\n\t ]+/s'  => '){',
            '/,[\r\n\t ]?{[\r\n\t ]+/s'  => ',{',
            //remove new-line after JS's line end (only most obvious and safe cases)
            '/\),[\r\n\t ]+/s'  => '),',
            //remove quotes from HTML attributes that does not contain spaces; keep quotes around URLs!
            '~([\r\n\t ])?([a-zA-Z0-9]+)="([a-zA-Z0-9_/\\-]+)"([\r\n\t ])?~s' => '$1$2=$3$4', //$1 and $4 insert first white-space character found before/after attribute
        );
        $content=  preg_replace(array_keys($replace), array_values($replace), $content);
    
        $remove = array(
            '</option>', '</li>', '</dt>', '</dd>', '</tr>', '</th>', '</td>'
        );
        $content = str_ireplace($remove, '', $content);
        return $content;
    }
    
    
    public static  function sortUrl($url) {
        // brand -> filter -> p
        $arraySort=array();
        $arraySort[]='utype=';
        $arraySort[]='category=';
        $arraySort[]='search=';
        $arraySort[]='brand=';
        $arraySort[]='screen=';        
        $arraySort[]='system=';
        $arraySort[]='group=';
        $arraySort[]='price=';
        $arraySort[]='loaidienthoai=';      
        $arraySort[]='chude=';
        $arraySort[]='filter=';
        $arraySort[]='dungtich=';
        $arraySort[]='type=';
        $arraySort[]='tinhnang=';
        $arraySort[]='service=';
        $arraySort[]='product=';
        $arraySort[]='used=';
        $arraySort[]='page=';

        /////////////////////////
        $temp = explode('?', $url);
        $prefix=$temp[0];

        $tempSub= explode('&', $temp[1]);
        
        foreach ($arraySort as $val)
        {
            foreach ($tempSub as $valSub)
            {            
                if(strpos($valSub, $val)!==FALSE and $valSub!='page=1')
                {
                    $data[]=$valSub;        
                    
                }      
            }            
        }
        
        $urlSub=implode('&', $data);
        if($urlSub=='')
        { 
          
            return $prefix;
        
        }
            else {

                if(strpos($prefix, 'https://')===false)
                    {
                    $temp = explode('/', $prefix);
                    foreach ($temp as $val)
                    {
                        if($val!='')
                            $dataPrefix[]=$val;
                    }
                    $prefixOk= implode('/', $dataPrefix);
                    $prefixOk=str_replace(':/', '://', $prefixOk);
                    
                    if(strpos($prefix, '.html')!==false)
                    {
                        return "/".$prefixOk.'?'.$urlSub;
                    }
                    else
                    return "/".$prefixOk.'/?'.$urlSub;
                    }
                    else{
                        return $prefix.'?'.$urlSub;
                 
                    }
                
            }
        
    }
     


      public static  function sortUrlFAQ($url) {
        // brand -> filter -> p
        $arraySort=array();
        $arraySort[]='key=';
        $arraySort[]='l=';
        $arraySort[]='page=';

        /////////////////////////
        $temp = explode('?', $url);
        $prefix=$temp[0];

        $tempSub= explode('&', $temp[1]);
        
        foreach ($arraySort as $val)
        {
            foreach ($tempSub as $valSub)
            {            
                if(strpos($valSub, $val)!==FALSE and $valSub!='page=1')
                {
                    $data[]=$valSub;        
                    
                }      
            }            
        }
        
        $urlSub=implode('&', $data);
        if($urlSub=='')
        { 
          
            return $prefix;
        
        }
            else {

                if(strpos($prefix, 'https://')===false)
                    {
                    $temp = explode('/', $prefix);
                    foreach ($temp as $val)
                    {
                        if($val!='')
                            $dataPrefix[]=$val;
                    }
                    $prefixOk= implode('/', $dataPrefix);
                    $prefixOk=str_replace(':/', '://', $prefixOk);
                    
                    if(strpos($prefix, '.html')!==false)
                    {
                        return "/".$prefixOk.'?'.$urlSub;
                    }
                    else
                    return "/".$prefixOk.'/?'.$urlSub;
                    }
                    else{
                        return $prefix.'?'.$urlSub;
                 
                    }
                
            }
        
    }
    public static function getSimLink($cateid, $title) {
        return Globals::getBaseUrl() . "danh-sach-sim/$title.$cateid.html";
    }
    
    public static function getRemoveUrl($param,$url) {
    
        if(strpos($url, "?")!==false)  // có dấu ?
        {
            if(strpos($url, "?$param")!==false)  // có dấu ?param
            {
                if(strpos($url, "&")!==false)  // có dấu ?param
                {     
                    $url= preg_replace("/(\?$param=[a-zA-Z0-9-]+&)/", "?", $url);
                }else      
                $url= preg_replace("/(\?$param=[a-zA-Z0-9-]+)/", "", $url);
              
            }else   // có dấu ? mà ko kế bên param
            {
                $url= preg_replace("/(&$param=[a-zA-Z0-9-]+)/", "", $url);
            }
    
        }
        else
        {    
            $url= preg_replace("/(&$param=[a-zA-Z0-9-]+)/", "", $url);          
             
        }
    
    
        return self::sortUrl($url);
    
    }
    
    static function removeTiengViet($content) {
        $trans = array('à' => 'a', 'á' => 'a', 'ả' => 'a', 'ã' => 'a', 'ạ' => 'a', 'â' => 'a', 'ấ' => 'a', 'ầ' => 'a', 'ẫ' => 'a', 'ẩ' => 'a', 'ậ' => 'a', 'ú' => 'u', 'ù' => 'u', 'ủ' => 'u', 'ũ' => 'u', 'ụ' => 'u', 'à' => 'a', 'á' => 'a', 'ô' => 'o', 'ố' => 'o', 'ồ' => 'o', 'ổ' => 'o', 'ỗ' => 'o', 'ộ' => 'o', 'ó' => 'o', 'ò' => 'o', 'ỏ' => 'o', 'õ' => 'o', 'ọ' => 'o', 'ê' => 'e', 'ế' => 'e', 'ề' => 'e', 'ể' => 'e', 'ễ' => 'e', 'ệ' => 'e', 'í' => 'i', 'ì' => 'i', 'ỉ' => 'i', 'ĩ' => 'i', 'ị' => 'i', 'ơ' => 'o', 'ớ' => 'o', 'ý' => 'y', 'ỳ' => 'y', 'ỷ' => 'y', 'ỹ' => 'y', 'ỵ' => 'y', 'ờ' => 'o', 'ở' => 'o', 'ỡ' => 'o', 'ợ' => 'o', 'ư' => 'u', 'ừ' => 'u', 'ứ' => 'u', 'ử' => 'u', 'ữ' => 'u', 'ự' => 'u', 'đ' => 'd', 'À' => 'A', 'Á' => 'A', 'Ả' => 'A', 'Ã' => 'A', 'Ạ' => 'A', 'Â' => 'A', 'Ấ' => 'A', 'À' => 'A', 'Ẫ' => 'A', 'Ẩ' => 'A', 'Ậ' => 'A', 'Ú' => 'U', 'Ù' => 'U', 'Ủ' => 'U', 'Ũ' => 'U', 'Ụ' => 'U', 'Ô' => 'O', 'Ố' => 'O', 'Ồ' => 'O', 'Ổ' => 'O', 'Ỗ' => 'O', 'Ộ' => 'O',
            'Ê' => 'E', 'Ế' => 'E', 'Ề' => 'E', 'Ể' => 'E', 'Ễ' => 'E', 'Ệ' => 'E', 'Í' => 'I', 'Ì' => 'I', 'Ỉ' => 'I', 'Ĩ' => 'I', 'Ị' => 'I', 'Ơ' => 'O', 'Ớ' => 'O', 'Ờ' => 'O', 'Ở' => 'O', 'Ỡ' => 'O', 'Ợ' => 'O', 'Ư' => 'U', 'Ừ' => 'U', 'Ứ' => 'U', 'Ử' => 'U', 'Ữ' => 'U', 'Ự' => 'U', 'Đ' => 'D', 'Ý' => 'Y', 'Ỳ' => 'Y', 'Ỷ' => 'Y', 'Ỹ' => 'Y', 'Ỵ' => 'Y', 'ẻ' => 'e', 'ẽ' => 'e', 'ẹ' => 'e', 'ẵ' => 'a', 'ẳ' => 'a',
            'á' => 'a', 'à' => 'a', 'ả' => 'a', 'ã' => 'a', 'ạ' => 'a', 'ă' => 'a', 'ắ' => 'a', 'ằ' => 'a', 'ẳ' => 'a', 'ẵ' => 'a', 'ặ' => 'a', 'â' => 'a', 'ấ' => 'a', 'ầ' => 'a', 'ẩ' => 'a', 'ẫ' => 'a', 'ậ' => 'a', 'ú' => 'u', 'ù' => 'u', 'ủ' => 'u', 'ũ' => 'u', 'ụ' => 'u', 'ư' => 'u', 'ứ' => 'u', 'ừ' => 'u', 'ử' => 'u', 'ữ' => 'u', 'ự' => 'u', 'í' => 'i', 'ì' => 'i', 'ỉ' => 'i', 'ĩ' => 'i', 'ị' => 'i', 'ó' => 'o', 'ò' => 'o', 'ỏ' => 'o', 'õ' => 'o', 'ọ' => 'o', 'ô' => 'o', 'ố' => 'o', 'ồ' => 'ô', 'ổ' => 'o', 'ỗ' => 'o', 'ộ' => 'o', 'ơ' => 'o', 'ớ' => 'o', 'ờ' => 'o', 'ở' => 'o', 'ỡ' => 'o', 'ợ' => 'o', 'đ' => 'd', 'Đ' => 'D', 'ý' => 'y', 'ỳ' => 'y', 'ỷ' => 'y', 'ỹ' => 'y', 'ỵ' => 'y', 'Á' => 'A', 'À' => 'A', 'Ả' => 'A', 'Ã' => 'A', 'Ạ' => 'A', 'Ă' => 'A', 'Ắ' => 'A', 'Ẳ' => 'A', 'Ẵ' => 'A', 'Ặ' => 'A', 'Â' => 'A', 'Ấ' => 'A', 'Ẩ' => 'A', 'Ẫ' => 'A', 'Ậ' => 'A', 'É' => 'E', 'È' => 'E', 'Ẻ' => 'E', 'Ẽ' => 'E', 'Ẹ' => 'E', 'Ế' => 'E', 'Ề' => 'E', 'Ể' => 'E', 'Ễ' => 'E', 'Ệ' => 'E', 'Ú' => 'U', 'Ù' => 'U', 'Ủ' => 'U', 'Ũ' => 'U', 'Ụ' => 'U', 'Ư' => 'U', 'Ứ' => 'U', 'Ừ' => 'U', 'Ử' => 'U', 'Ữ' => 'U', 'Ự' => 'U', 'Í' => 'I', 'Ì' => 'I', 'Ỉ' => 'I', 'Ĩ' => 'I', 'Ị' => 'I', 'Ó' => 'O', 'Ò' => 'O', 'Ỏ' => 'O', 'Õ' => 'O', 'Ọ' => 'O', 'Ô' => 'O', 'Ố' => 'O', 'Ổ' => 'O', 'Ỗ' => 'O', 'Ộ' => 'O', 'Ơ' => 'O', 'Ớ' => 'O', 'Ờ' => 'O', 'Ở' => 'O', 'Ỡ' => 'O', 'Ợ' => 'O', 'Ý' => 'Y', 'Ỳ' => 'Y', 'Ỷ' => 'Y', 'Ỹ' => 'Y', 'Ỵ' => 'Y', 'ặ' => 'a', 'é' => 'e', 'ắ' => 'a', 'ế' => 'e', 'è' => 'e', 'ằ' => 'a', 'É' => 'E', ',' => ''
            , '(' => '', ')' => '', '>' => '', '"' => ''
        )
            ;
            $content = strtr($content, $trans); // chuoi da duoc bo dau
            return str_replace(' ', '-', strtolower(trim($content)));
    }
    
    

  public static function getUrl1($param,$value,$url) {

     if(strpos($url, "?")!==false)  // có dấu ?
     {
         if(strpos($url, "?$param")!==false)  // có dấu ?param
         {
             $url= preg_replace("/(\?$param=[a-zA-Z0-9-]+)/", "?", $url);
             $url=$url."&$param=";
         }else   // có dấu ? mà ko kế bên param
         {
             $url= preg_replace("/(&$param=[a-zA-Z0-9-]+)/", "", $url);
             $url=$url."&$param=";
         }
  
     }
     else
     {

         $url= preg_replace("/(&$param=[a-zA-Z0-9-]+)/", "", $url);       
         $url=$url."?$param=";
     
     }

    
     return self::sortUrl($url.$value);
    
    }


    public static function getUrl($param,$value,$url) {

     if(strpos($url,$value)===false and $param=='group') 
        return '?group='.$value;

     if(strpos($url, "?")!==false)  // có dấu ?
     {
         if(strpos($url, "?$param")!==false)  // có dấu ?param
         {
             $url= preg_replace("/(\?$param=[a-zA-Z0-9-]+)/", "?", $url);
             $url=$url."&$param=";
         }else   // có dấu ? mà ko kế bên param
         {
             $url= preg_replace("/(&$param=[a-zA-Z0-9-]+)/", "", $url);
             $url=$url."&$param=";
         }
  
     }
     else
     {

         $url= preg_replace("/(&$param=[a-zA-Z0-9-]+)/", "", $url);       
         $url=$url."?$param=";
     
     }

    
     return self::sortUrl($url.$value);
    
    }
      
    
        
    public static function getUrlFAQ($param,$value,$url) {
 
     if(strpos($url, "?")!==false)  // có dấu ?
     {
         if(strpos($url, "?$param")!==false)  // có dấu ?param
         {
             $url= preg_replace("/(\?$param=[a-zA-Z0-9-]+)/", "?", $url);
             $url=$url."&$param=";
         }else   // có dấu ? mà ko kế bên param
         {
             $url= preg_replace("/(&$param=[a-zA-Z0-9-]+)/", "", $url);
             $url=$url."&$param=";
         }
  
     }
     else
     {

         $url= preg_replace("/(&$param=[a-zA-Z0-9-]+)/", "", $url);       
         $url=$url."?$param=";
     
     }

    
     return self::sortUrlFAQ($url.$value);
    
    }
    
    
    
    
    private function minJS()
    {
        
        /*
        ghi nhớ min js
        
        <script type="text/javascript" src="<?= Globals::getStaticUrl() ?>v4/js/jquery-1.7.1.min.js"></script>
        <script type="text/javascript" src="<?= Globals::getStaticUrl();?>hnamv2/js/print.js"></script>
        <script type="text/javascript" src="<?= Globals::getStaticUrl() ?>v4/js/default-1.08.js"></script>
        <script type="text/javascript" src="<?= Globals::getStaticUrl() ?>v4/js/frmValid.js"></script>
        <script type="text/javascript" src="<?= Globals::getStaticUrl() ?>v4/autocomplete/jquery.auto.min.js?v=1.1"></script>
        <script type="text/javascript" src="<?= Globals::getStaticUrl() ?>v4/js/shoppingBag-1.01.js"></script>
        <script type="text/javascript" src="<?= Globals::getStaticUrl() ?>v4/thickbox/thickbox.js"></script>
        <script type="text/javascript" src="<?= Globals::getStaticUrl();?>v4/easyslider/easySlider1.7.min.js"></script>
        <script type="text/javascript" src="<?= Globals::getStaticUrl() ?>v4/js/cookie.js"></script>
        
        */
    }
    
    
    private function getCacheInstance()
    {
        $cache = GlobalCache::getCacheInstance('ws');
        return $cache;
    }
    
    private function  minCssCompress($cssFiles,$check){


            foreach ($cssFiles as $cssFile) {
                $buffer .= SEOPlugin::file_get_curl($cssFile);
            }
            // Remove comments
            $buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
            // Remove space after colons
            $buffer = str_replace(': ', ':', $buffer);
            // Remove whitespace
            $buffer = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $buffer);
            // set 1 day
            
            if ($check==1) {
               $BASE_URL = 'https://hnamnew.com';
               $buffer=str_replace(Globals::getStaticUrl(), $BASE_URL, $buffer);
            }

        
        return $buffer;
    }
    
    

    
    public  function minCSS($cssLink,$check) {
             // MIN CSS

        $fileCss[] = Globals::getBaseUrl()."v5/css/bootstrap.min.css";
        $fileCss[]= Globals::getBaseUrl()."v5/css/main.css";
        $fileCss[] = Globals::getBaseUrl()."v5/lib/font_awesome/css/font-awesome.min.css";
        $fileCss[] = Globals::getBaseUrl()."v5/lib/modernizr_menu/css/component.css";
        $fileCss[] = Globals::getBaseUrl()."v5/css/jquery-ui.css";
        
        
        
         
         foreach ($cssLink as $val)
         {
            $fileCss[]=$val;
         }
         $fileCss[]= Globals::getBaseUrl()."v5/css/custom.css";   
        return self::minCssCompress($fileCss,$check);
    
    }
    
    public static function setSocialUrl($social_url) {
        Zend_Registry::set(self::$_social_url, $social_url);
    }

    public static function getSocialUrl() {
        if (Zend_Registry::isRegistered(self::$_social_url)) {
            return Zend_Registry::get(self::$_social_url);
        } else
            return Business_Common_Utils::curPageURL();
    }

    public static function setSocialImg($content) {
        self::$_social_img = $content;
    }

    public static function getSocialImg() {
        return self::$_social_img;
    }

    public static function getAllTags() {
        $pname = Business_Ws_ProductsItem::getInstance()->getProductsName();
        return $pname;
    }

    public static function getProductHeader($flashvar) {
        $url = Globals::getBaseUrl();
        $source = $url . "v4/flash/run2.swf?xmlPath=" . (Globals::getBaseUrl() . "products/xml360?itemid=" . $flashvar);
        $w = 540;
        $h = 390;
        $install = $url . "v4/swfobject/expressInstall.swf";
        $divID = "box360s";
        return 'swfobject.embedSWF("' . $source . '", "' . $divID . '", "' . $w . '", "' . $h . '", "9.0.0", "' . $install . '");';
    }

    public static function getHnamWarranty() {
        return Globals::getBaseUrl() . "quy-dinh-bao-hanh";
    }

    public static function getWarrantyStoreLink($storeid, $brand) {
        $brand = Business_Common_Utils::adaptTitleLinkURLSEO($brand);
        $str = Globals::getBaseUrl() . "trung-tam-bao-hanh-%s.%s-2.html";
        $brand = str_replace("&nbsp;", "", $brand);
        return sprintf($str, strtolower($brand), $storeid);
    }

    public static function getNewsDetailLinkV2($title, $itemid) {
        $title = Business_Common_Utils::adaptTitleLinkURLSEO($title);
        return Globals::getBaseUrl() . "$title-$itemid-2.html";
    }

    public static function redirectLink($moduleName = '', $itemid = '', $redirect = '', $moduleid = 0) {
        if ($redirect != '')
            $redirect = urlencode($redirect);
        return Globals::getBaseUrl() . "api/index?moduleName=$moduleName&itemid=$itemid&redirect=$redirect&moduleid=$moduleid";
    }

    public static function getCurrentURL() {
        return Business_Common_Utils::getCurrentURL();
    }

    public static function setKeywords($key) {
        Zend_Registry::set(self::$_key, $key);
    }

    public static function getKeywords() {
        if (Zend_Registry::isRegistered(self::$_key)) {
            return Zend_Registry::get(self::$_key);
        } else
            return "";
    }

    public static function setDescriptions($des) {
        Zend_Registry::set(self::$_des, $des);
    }

    public static function getDescriptions() {
        if (Zend_Registry::isRegistered(self::$_des)) {
            return Zend_Registry::get(self::$_des);
        } else
            return "";
    }

    public static function setTitle($title) {
        Zend_Registry::set(self::$_title, $title);
    }

    public static function getTitle() {
        if (Zend_Registry::isRegistered(self::$_title)) {
            return Zend_Registry::get(self::$_title);
        } else
            return "";
    }

    public static function appendTitle($title) {
        $tmp = self::getTitle();
        $tmp .= $title;
        self::setTitle($tmp);
    }

    public static function getStaticLink($title) {
        return '/thong-tin/' . $title . '/';
    }

    public static function getParentMenuName($itemid) {
        $_menuitem = Business_Ws_MenuItem::getInstance();
        $menu = $_menuitem->getDetailById($itemid);

        if (is_array($menu) && count($menu) > 0) {
            $pid = $menu['pid'];
            if ($pid > 0) {
                $_parent = $_menuitem->getDetailById($pid);
                return $_parent['title'];
            } else {
                return '';
            }
        }
    }

    public static function getMenuName($itemid) {
        $_menuitem = Business_Ws_MenuItem::getInstance();
        $menu = $_menuitem->getDetailById($itemid);

        if (is_array($menu) && count($menu) > 0) {
            return $menu['title'];
        }
        return '';
    }

    public static function getAboutLink() {
        return Globals::getBaseUrl() . "thong-tin/gioi-thieu";
    }

    public static function getAccessoryListByProduct($name) {
        $name = Business_Common_Utils::adaptTitleLinkURLSEO($name);
        $name = trim($name, "-");
        $name .= ".html";
        return Globals::getBaseUrl() . "phu-kien-" . $name;
    }

    public static function getJobLink() {
        return Globals::getBaseUrl() . "tin-tuc/thong-bao-tuyen-dung-.6216.html";
    }

    public static function getNewsLink($alias, $itemid) {;
        $name = Business_Common_Utils::adaptTitleLinkURLSEO($alias);
        return Globals::getBaseUrl() . "$name-$itemid/";
    }

    public static function getNewsLinkAll() {
        return Globals::getBaseUrl() . "tin-tuc/";
    }

    public static function getDienthoaiLink() {
        return Globals::getBaseUrl() . "dien-thoai/";
    }

    public static function getMaytinhbangLink() {
        return Globals::getBaseUrl() . "may-tinh-bang/";
    }

    public static function getDonghothongminhLink() {
        return Globals::getBaseUrl() . "dong-ho-thong-minh/";
    }

    public static function getAccessoriesLink() {
        return Globals::getBaseUrl() . "phu-kien/";
    }

    public static function getDiscountLink() {
        return Globals::getBaseUrl() . "tin-khuyen-mai--67/";
    }

    public static function getCompareLink() {
        return Globals::getBaseUrl() . "so-sanh-san-pham";
    }

    public static function getContactLink() {
        return Globals::getBaseUrl() . "thong-tin/lien-he";
    }

    /* =============PRODUCT LINK */

    public static function getServicesLink($itemid, $title) {
        return Globals::getBaseUrl() . "tin-tuc/$title.$itemid.html";
    }

    public static function getProductLink($cateid, $title) {
        return Globals::getBaseUrl() . "loai-dien-thoai/$title.$cateid.html";
    }

        public static function getProductOldLink($cateid, $title) {
        return Globals::getBaseUrl() . "kho-may-cu/$title.$cateid.html";
    }


    public static function getProductLinkByStock($cateid, $title, $stock, $filter) {
        $arr = array();
        if ($stock != -1) {
            $arr[] = "st=" . $stock;
        }
        $arr[] = "ft=" . $filter;
        $params = implode("&", $arr);
        $params = htmlspecialchars($params);
        $url = Globals::getBaseUrl() . "loai-dien-thoai/$title.$cateid.html?" . $params;
        return $url;
    }

    public static function getTabletLink($cateid, $title) {
        return Globals::getBaseUrl() . "loai-may-tinh-bang/$title.$cateid.html";
    }

    public static function getLaptopAppleLink() {
        return Globals::getBaseUrl() . "laptop/apple.html";
    }

    public static function getTabletLinkByStock($cateid, $title, $stock, $filter) {
        $arr = array();
        if ($stock != -1) {
            $arr[] = "st=" . $stock;
        }
        $arr[] = "ft=" . $filter;
        $params = implode("&", $arr);
        $params = htmlspecialchars($params);
        return Globals::getBaseUrl() . "loai-may-tinh-bang/$title.$cateid.html?" . $params;
    }

    public static function getLaptopAppleLinkByStock($stock, $filter,$search=null) {
        $arr = array();
        if ($stock != -1) {
            $arr[] = "st=" . $stock;
        }
        $arr[] = "ft=" . $filter;
        $arr[] = "search=" . $search;
        $params = implode("&", $arr);
        $params = htmlspecialchars($params);
        return Globals::getBaseUrl() . "laptop/apple.html?" . $params;
    }

    public static function getProductDetailLink($pid, $title) {
        return Globals::getBaseUrl() . "dien-thoai/$title.$pid.html";
    }

    public static function getLaptopDetailLink($pid, $title) {
        return Globals::getBaseUrl() . "laptop/$title.$pid.html";
    }

    public static function getSmartWatchLink($acc_cateid, $title) {
        return Globals::getBaseUrl() . "dong-ho-thong-minh/$title.$acc_cateid.cate.html";
    }
    public static function getRepairDetailLink($pid, $title) {
        return Globals::getBaseUrl() . "dich-vu-sua-chua/$title.$pid.html";
    }
    public static function getBHSCDetailLink($pid, $title) {
        return Globals::getBaseUrl() . "bao-hanh-sua-chua/$title.$pid.html";
    }
    public static function getSmartWatchDetailLink($pid, $title) {
        return Globals::getBaseUrl() . "dong-ho-thong-minh/$title.$pid.html";
    }

    public static function getTabletDetailLink($pid, $title) {
        return Globals::getBaseUrl() . "may-tinh-bang/$title.$pid.html";
    }

    public static function getAccesoriesLink($acc_cateid, $title) {
        return Globals::getBaseUrl() . "loai-phu-kien/$title.$acc_cateid.html";
    }

    public static function getAccesoriesDetailLink($pid, $title) {
        return Globals::getBaseUrl() . "phu-kien/$title.$pid.html";
    }

    /* =============TIN TUC */
    public static function getNewsDetailChannelLink($itemid, $title) {
         //$title = Business_Common_Utils::adaptTitleLinkURLSEO($title);
        $title = Business_Common_Utils::adaptTitleLinkURLSEO($title);
        return Globals::getBaseUrl() . "channel/detail/$title.$itemid.html";
    }

    public static function getNewsDetailLinkCNC($itemid, $title) {
        $title = Business_Common_Utils::adaptTitleLinkURLSEO($title);
        return  "https://www.congnghecam.com/tin-tuc/$title.$itemid.html";

    }    
    
    public static function getNewsDetailLink($itemid, $title) {
        $title = Business_Common_Utils::adaptTitleLinkURLSEO($title);
        return Globals::getBaseUrl() . "tin-tuc/$title.$itemid.html";

//            switch($cateid){
//                case 35:
//                    return Globals::getBaseUrl()."tin-tuc/$title.$itemid.html"; 
//                    break;
//                case 66:
//                    return Globals::getBaseUrl()."tin-tuc-hnam/$title.$itemid.html"; 
//                    break;
//                case 67:
//                    return Globals::getBaseUrl()."khuyen-mai/$title.$itemid.html"; 
//                    break;
//                case 68:
//                    return Globals::getBaseUrl()."thu-thuat/$title.$itemid.html"; 
//                    break;
//            }
    }

//        public static function getNewsPromotionLink($itemid, $title){
//            return Globals::getBaseUrl()."khuyen-mai/$title.$itemid.html";
//        }
//        
    public static function getNewsPromotionListLink() {
        return Globals::getBaseUrl() . "khuyen-mai/";
    }

//        
//        public static function getNewsHnamNewsLink($itemid, $title){
//            return Globals::getBaseUrl()."tin-tuc-hnam/$title.$itemid.html";
//        }
//        
    public static function getNewsHnamNewsListLink() {
        return Globals::getBaseUrl() . "tin-tuc-hnam/";
    }

//        
    public static function getNewsListLink() {
        return Globals::getBaseUrl() . "tin-tuc/";
    }

//        
//        public static function getNewsTipLink($itemid, $title){
//            return Globals::getBaseUrl()."thu-thuat/$title.$itemid.html";
//        }
//        
    public static function getNewsTipListLink() {
        return Globals::getBaseUrl() . "thu-thuat/";
    }

    public static function getNewsListLinkAll() {
        return Globals::getBaseUrl() . "tin-tuc/";
    }

    /*
     * menuname="menu_product_knid"
     * menuname="menu_product_klinen"
     */

    public static function getMenuLev2($menuname = 'menu_product_knid') {

        $_menu_nkid = Business_Ws_MenuItem::getInstance();
        $pid = 0;
        $lang = 1;
        $depth = 1;
        $list_menu_nkid = $_menu_nkid->getListFilter($menuname, $pid, $lang, $depth);

        $result = array();
        foreach ($list_menu_nkid as &$item) {
            if ($item['haschild'] == 1) {
                $list_menu_nkid_c2 = $_menu_nkid->getListFilter($menuname, $item['itemid'], $lang, "2");
                foreach ($list_menu_nkid_c2 as &$item2) {
                    $item['str_itemid'].=$item2['itemid'] . ",";
                    $keywords[] = $item2['title'];
                }

                $item['list_menu_c2'] = $list_menu_nkid_c2;
            }
        }
        $keywords = array_unique($keywords);
        $keywords = implode(" , ", $keywords);
        return $keywords;
    }

    public function preDispatch(Zend_Controller_Request_Abstract $request) {
        $title = self::getTitle();
        if (empty($title)) {
            $config = Zend_Registry::get('configuration');
            if (isset($config->seo->title->default)) {
                self::setTitle($config->seo->title->default);
            }
        }
        $des = self::getDescriptions();
        if (empty($des)) {
            $config = Zend_Registry::get('configuration');
            if (isset($config->seo->des->default)) {
                self::setDescriptions($config->seo->des->default);
            }
        }
        $keys = self::getKeywords();
        if (empty($keys)) {
            $config = Zend_Registry::get('configuration');
            if (isset($config->seo->key->default)) {
                self::setKeywords($config->seo->key->default);
            }
        }
    }

}

?>

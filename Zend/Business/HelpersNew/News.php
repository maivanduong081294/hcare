<?php

class Business_HelpersNew_News {

    private static $_instance = null;

    // module news to store

    function __construct() {
        
    }

    /**
     * get instance of Business_HelpersNew_News
     *
     * @return Business_HelpersNew_News
     */
    public static function getInstance() {
        if (self::$_instance == null) {
            self::$_instance = new Business_HelpersNew_News();
        }
        return self::$_instance;
    }


    public static function updateLinkMenuItem($listMenuItem) {
        $url = Globals::getStaticUrl();
        foreach ($listMenuItem as  &$item) {
            if(!is_array($item))
            {   $listMenuItem["link"] = SEOPlugin::getNewsLink( $listMenuItem["title"],$listMenuItem["itemid"]);
                break;
            }
            $item["link"] = SEOPlugin::getNewsLink( $item["title"],$item["itemid"]);
        }
        return $listMenuItem;
    }
    
    function getYoutubeImage($vidID){
        return   $thumb = "https://img.youtube.com/vi/$vidID/mqdefault.jpg";
    }
    public function getTextBetweenTags($string, $tagname) {
        $pattern = "/<$tagname ?.*>(.*)<\/$tagname>/";
        preg_match($pattern, $string, $matches);
        preg_match ( '/embed\/([^"]*)"/', $matches[0], $matches1 );
        return $matches1[1];
    }
    
    public static function updateNewsList($listNews,$limit=10,$type=null,$channel=0) {
           if($type)
            $type="$type/";
        foreach ($listNews as &$news) {
            $url =Globals::getCDNUrl(4);
            $thumb = json_decode($news["thumb"]); 
            $news["img_small_search"] = $url . "uploads/news/" . $thumb->thumb1;     
            $news["img_small"] = $url . "uploads/news/$type" . $thumb->thumb1;
            $news["img_large"] = $url . "uploads/news/$type" . $thumb->thumb2;
            $news["img_v5"] = $url . "uploads/news/" . $thumb->thumbv5;  
            $news["idyoutube"]=self::getTextBetweenTags($news['fullcontent'],"iframe");
            $news["linkyoutube"] =self::getYoutubeImage(self::getTextBetweenTags($news['fullcontent'],"iframe"));
            $news["youtube"] =$url ."rsuploads/news/video/" .$news["idyoutube"].".jpg";
            //============ hinh resiza trang chu
            $pathUpload= BASE_PATH."/rsuploads/news/home/";  
            $file=$pathUpload.$value['newimage_path']; 
            if(file_exists($file) and $news["newimage_path"]!='')
            {
            $news["img_home"] = $url ."/rsuploads/news/home/" .$news["newimage_path"];
            $news["img_home_type"] = $url . "/rsuploads/news/$type/" .$news["newimage_path"];
            $news["img_gallery"] = $url . "/rsuploads/news/gallery/" .$news["newimage_path"];                
            }
            //============ hinh sao trang chu
            $pathUpload= BASE_PATH."/rsuploads/news/";  
            $file=$pathUpload.$thumb->thumb2; 
            if(file_exists($file))
            $news["img_sao"] = $url ."/rsuploads/news/" .$thumb->thumb2;
            else
             $news["img_sao"] =  $url . "uploads/news/$type" . $thumb->thumb2;
            //==============
            $news["newimage_path"] = $url . "/uploads/news/$type" .$news["newimage_path"];

            if($news["social_img"] !='' and  $news["social_img"] !='-social.jpg' )
               $news["rs_social_img"] = $url ."/rsuploads/news/images/" .$news["social_img"]."?v=".Globals::getVersion();
            else
               $news["rs_social_img"] =  Globals::getBaseUrl() ."/v5/images/noimage.jpg"; 
            
             if($news["social_img"] !='')
            $news["social_img"] = $url ."/uploads/news/large/" .$news["social_img"];

         
            //============================== null hinh  .jpg.jpg lỗi hình  .hnamv3/www/www
             if($thumb->thumb1=='' or strpos($thumb->thumb1,".jpg.jpg")!==false or strpos($thumb->thumb1,".hnamv3/www/www")!==false)
            {    
                $url = Globals::getBaseUrl();
                 $news["img_small"] =  Globals::getBaseUrl() ."/v5/images/noimage.jpg";
            }
            if($thumb->thumb2==''  or strpos($thumb->thumb2,".jpg.jpg")!==false or strpos($thumb->thumb1,".hnamv3/www/www")!==false)
            {   
                $url = Globals::getBaseUrl();
                 $news["img_large"] = Globals::getBaseUrl() ."/v5/images/noimage.jpg";
            }
            if($news["social_img"]==''  or $news["social_img"] =='-social.jpg')
            {
                $url = Globals::getBaseUrl();
                $news["social_img"] = Globals::getBaseUrl() . "/v5/images/noimage.jpg";
            }
            if($thumb->thumbv5!='' and $news["newimage_path"]=='')
                $news["newimage_path"]=  $news["img_v5"];
            if( $news["newimage_path"]==''  ) 
            {
                $url = Globals::getBaseUrl();
                $news["newimage_path"] = Globals::getBaseUrl() . "/v5/images/noimage.jpg";
            }
            //===============================================
            $news["display_date"] = date("d/m/Y H:i:s", strtotime($news["posteddate"]));  
            if($channel==1)     
            $news["link"] = SEOPlugin::getNewsDetailChannelLink($news["itemid"], $news["title_seo"]);
                else      
            $news["link"] = SEOPlugin::getNewsDetailLink($news["itemid"], $news["title_seo"]);
            $news["shortcontent"]= strip_tags($news['shortcontent']);
            if($news["shortcontent"] =='')
            {
                $shortContent = Business_Helpers_Common::shortText(strip_tags($news['fullcontent']),$limit)."...";  
                $news["shortcontent"] = $shortContent;
            }

            //update auto play iframe
        }            
        return $listNews;
    }
    public static function getStarAndHnam($List) {
        $starList = array();
        $starList = self::updateNewsList($List);
        return $starList;
    }
    

}

?>

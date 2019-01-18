<?php

class Business_Helpers_News {

    private static $_instance = null;

    // module news to store

    function __construct() {
        
    }

    /**
     * get instance of Business_Helpers_News
     *
     * @return Business_Helpers_News
     */
    public static function getInstance() {
        if (self::$_instance == null) {
            self::$_instance = new Business_Helpers_News();
        }
        return self::$_instance;
    }

    public static function fixLinkNoFollow($content) {
        $regexp = "<a\s[^>]*href=(\"??)([^\" >]*?)\\1[^>]*>(.*)<\/a>";
        $trust = "hnammobile.com";
        if(preg_match_all("/$regexp/siU", $content, $matches)) {            
            $url = $matches[0];
            foreach($url as $_url) {
                if (strpos($_url, $trust)===false) {
                    $new_url = str_replace("href", " rel=\"nofollow\" href", $_url);
                    $content = str_replace($_url, $new_url, $content);
                }
            }
        }
        return $content;
    }
    
    public static function getRelatedNews($pDetail, $limit = 12) {
        $_news = Business_Ws_NewsItem::getInstance();
        $title = strtolower(Business_Common_Utils::removeTiengViet($pDetail['title']));
        $title = str_replace(array("gb", "GB"), "", $title);
        if (strpos($title, 'sony') !== false)
            $length = 3; else
            $length = 2;
        if (strpos($title, 'apple') !== false)
            $title = str_replace("apple", "", $title);

        $title = Business_Helpers_Common::shortText($title, $length);

        $list = $_news->getListByName($title, 12);
        $list = Business_Helpers_News::updateNewsList($list);
        return $list;
    }

    public static function getRelatedNewsDH($pDetail, $limit = 12) {
        $_news = Business_Ws_NewsItem::getInstance();

        $title = strtolower(Business_Common_Utils::removeTiengViet($pDetail['title']));

//        $title = trim(preg_filter($pattern, "", $title));        
        $title = str_replace(array("gb", "GB"), "", $title);

        if (strpos($title, 'sony') !== false)
            $length = 3; else
            $length = 2;
        $title = str_replace(array("apple", "'"), "", $title);

        $title = "%đập hộp%" . Business_Helpers_Common::shortText($title, $length);
        //$title = "%đập hộp%";
        $list = $_news->getListByName2($title, 12);
        return $list;
    }

    public static function updateNewsItem($item) {
        $itemid = $item["itemid"];
        $title = $item["title"];
        $title = Business_Common_Utils::adaptTitleLinkURLSEO($title);
        
        $item["link"] = SEOPlugin::getNewsDetailLink($itemid, $title);
        $shortContent = Business_Helpers_Common::shortText(strip_tags($item['fullcontent']), 30)."...";            
        $item["shortcontent"] = $shortContent;
        $thumb = json_decode($item["thumb"]);
        $img = Globals::getStaticUrl()."uploads/news/".$thumb->thumb2;
        $_img = BASE_PATH_V3. "/uploads/news/".$thumb->thumb2;
        if (!is_file($_img)){
            $img_small = Globals::getStaticUrl()."v4/images/news-no-image.jpg";
        } else {
            $img_small = $img;
        }
        //resize new image
        /*if ($item["newimage"]==0) {
            $getImage = "http://www.hnammobile.com/import/do-import-news-imagev2?itemid=" . $item["itemid"];
            Business_Common_Utils::getContentByCurl($getImage);                
        }*/
		if ($item["newimage_path"]!=null){
        $_img = BASE_PATH_V3."/uploads/news/large/".$item["newimage_path"];
			if (!is_file($_img)){
				$img = Globals::getBaseUrl()."v4/images/news-no-image.jpg";
				$item["img"] = $img;
			} else {
				$item["img"] = Globals::getStaticUrl() . "uploads/news/large/".$item["newimage_path"];
			}
		} else {
				$img = Globals::getBaseUrl()."v4/images/news-no-image.jpg";
				$item["img"] = $img;			
		}
        $item["datetime"] = date("d/m/Y H:i:s", strtotime($item["posteddate"]));
        $item["img_small"]= $img_small;
        return $item;
    }

    public static function updateNewsList($listNews) {
        $url = Globals::getStaticUrl();
        foreach ($listNews as &$news) {
            $thumb = json_decode($news["thumb"]);
            $news["img_small"] = $url . "uploads/news/" . $thumb->thumb1;
            $news["img_large"] = $url . "uploads/news/" . $thumb->thumb2;
            $news["display_date"] = date("d/m/Y H:i:s", strtotime($news["posteddate"]));            
            $news["link"] = SEOPlugin::getNewsDetailLink($news["itemid"], $news["title"]);
            
            //update auto play iframe
            $news["fullcontent2"] = str_replace("\" f", "?autoplay=1\" f", $news["fullcontent"]);
        }            
        return $listNews;
    }
    
    public static function getStarAndHnam() {
        $_menu = Business_Ws_MenuItem::getInstance();
        $_news = Business_Ws_NewsItem::getInstance();
        $list = $_menu->getListByName("menu_static");
        $starList = array();
//        foreach($list as $newitem) {
//            $cateid = $newitem["itemid"];
//            $newsid = $newitem["delta"];
            
//            $title = strtolower($newitem["title"]);
//            if (strpos($title, "sao")!==false) {
//                $cateid = $newitem["itemid"];
//                $newsid = $newitem["delta"];
//                $starList1 = $_news->getList($newsid, $cateid, $offset=0, $records=100, $_key='', $filter='', $cols='', $enabled='', $random=true);
//                $starList1 = self::updateNewsList($starList1);
//            }
//            if (strpos(strtolower($title), "tại hnam")!==false) {
//                $cateid = $newitem["itemid"];
//                $newsid = $newitem["delta"];
//                $starList2 = $_news->getList($newsid, $cateid);
//                $starList2 = self::updateNewsList($starList2, $offset=0, $records=100, $_key='', $filter='', $cols='', $enabled='', $random=true);
//            }
//            if (strpos(strtolower($title), "ca sĩ")!==false) {
//                $cateid = $newitem["itemid"];
//                $newsid = $newitem["delta"];
//                $starList3 = $_news->getList($newsid, $cateid);
//                $starList3 = self::updateNewsList($starList3, $offset=0, $records=100, $_key='', $filter='', $cols='', $enabled='', $random=true);
//            }
//            if (strpos(strtolower($title), "diễn viên")!==false) {
//                $cateid = $newitem["itemid"];
//                $newsid = $newitem["delta"];
//                $starList4 = $_news->getList($newsid, $cateid, $offset=0, $records=100, $_key='', $filter='', $cols='', $enabled='', $random=true);
//                $starList4 = self::updateNewsList($starList4);
//            }
//        }
//        $starList=array();
//        if ($starList1!=null) {
//            $starList = array_merge($starList, $starList1);
//        }
//        if ($starList2!=null) {
//            $starList = array_merge($starList, $starList2);
//        }
//        if ($starList3!=null) {
//            $starList = array_merge($starList, $starList3);
//        }
//        if ($starList4!=null) {
//            $starList = array_merge($starList, $starList4);
//        }
        $cateid=605;
        $newsid=8;
        $starList = $_news->getList($newsid, $cateid, $offset=0, $records=100, $_key='', $filter='', $cols='', $enabled='', $random=false);
        $starList = self::updateNewsList($starList);
        return $starList;
    }

}

?>

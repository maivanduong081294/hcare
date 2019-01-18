<?php

class Business_HelpersNew_Common {

    private static $_instance = null;

    // module news to store

    function __construct() {
        
    }

    /**
     * get instance of Business_HelpersNew_Common
     *
     * @return Business_HelpersNew_Common
     */
    public static function getInstance() {
        if (self::$_instance == null) {
            self::$_instance = new Business_Helpers_Common();
        }
        return self::$_instance;
    }
    

	public static function fixItemPropTags($data) {
        if (is_array($data)) {
            if ($data["fullcontent"]!=null) {
                $data["fullcontent"] = self::removeAllItempropTags($data["fullcontent"]);
            }
            if ($data["shortcontent"]!=null) {
                $data["shortcontent"] = self::removeAllItempropTags($data["shortcontent"]);
            }
            if ($data["video"]!=null) {
                $data["video"] = self::removeAllItempropTags($data["video"]);
            }
            if ($data["unbox"]!=null) {
                $data["unbox"] = self::removeAllItempropTags($data["unbox"]);
            }
        } else {
            $data = self::removeAllItempropTags($data);
        }
        
        return $data;
    }
    
    private static function removeAllItempropTags($content) {
        if ($content == null) return null;
        $regex='/itemprop=[\"\'](.*)[\"\']/';
        preg_match_all($regex, $content, $matches);
        if (count($matches)>0) {
            foreach($matches as $prop) {
                $content = str_replace($prop, "", $content);
            }
        }
        return $content;
    }
    


}

?>

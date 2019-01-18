<?php

class Business_Import_Options extends Business_Abstract {

    private static $_instance = null;
    private $_secKey   = "Hnammobile@2015";
            function __construct() {
        
    }
 
    //public static function
    /**
     * get instance of Business_Import_Promotion
     *
     * @return Business_Import_Options
     */
    public static function getInstance() {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Get DB Connection
     *
     * @return Zend_Db_Adapter_Abstract
     */
    private function getDbConnection() {
        $db = Globals::getDbConnection('maindb');
        return $db;
    }

    /**
     * Enter description here...
     *
     * @return Maro_Cache
     */
    private function getCacheInstance() {
        $cache = GlobalCache::getCacheInstance('nt');
        return $cache;
    }

    public function getTabletMenu() {
        /* get menu tablet */
        $depth = 1;
        $parentid = 0;
        $menuname = 'menu_tablet';
        $leftMenuTablet = Business_Helpers_Common::getMenuLev($depth, $parentid, $menuname);
        if (count($leftMenuTablet) > 0) {
            $i = 0;

            foreach ($leftMenuTablet as &$item) {
                $item["title_encode"] = htmlspecialchars($item["title"]);
                $cateid = $item['itemid'];
                if ($cateid == 191)
                    $pos = $_pos;
                $_pos++;


                $title = Business_Common_Utils::adaptTitleLinkURLSEO($item['title']);
                if ($_cateid == $cateid)
                    $active = 'active'; else
                    $active = '';
                switch ($cateid) {
                    default:
                        $item['link'] = SEOPlugin::getTabletLink($cateid, $title);
                        break;
                    case 190:
                        $item['link'] = Globals::getBaseUrl() . "loai-may-tinh-bang/apple-ipad-chinh-hang.html";
                        break;
                }

                if ($i++ == count($leftMenuTablet) - 1)
                    $item['class'] = 'class="' . $active . ' last"';
                else
                    $item['class'] = 'class=' . $active;
            }
        }
        return $leftMenuTablet;
    }
}

?>

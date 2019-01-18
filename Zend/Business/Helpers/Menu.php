<?php

class Business_Helpers_Menu {

    private static $_instance = null;

    // module news to store

    function __construct() {
        
    }

    /**
     * get instance of Business_Helpers_Menu
     *
     * @return Business_Helpers_Menu
     */
    public static function getInstance() {
        if (self::$_instance == null) {
            self::$_instance = new Business_Helpers_Menu();
        }
        return self::$_instance;
    }

    public static function getProductMenuList() {
        $depth = 1;
        $parentid = 0;
        $menuname = "menu_menu";
        $menu = Business_Helpers_Common::getMenuLev($depth, $parentid, $menuname);
        Business_Helpers_Common::getInstance();
    }

    public static function getWarrantyStoreList() {
        $menuname = "menu_tip";
        $list = Business_Ws_MenuItem::getInstance()->getListByName($menuname);

        $newsid = $list[0]["delta"];
        $cateid = $list[0]["itemid"];
        $warranty = Business_Ws_NewsItem::getInstance()->getList($newsid, $cateid, 0, 100);
        
        return $warranty;
    }
}
?>
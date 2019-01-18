<?php

class Business_Helpers_CommonV2 {

    private static $_instance = null;

    // module news to store

    function __construct() {
        
    }

    public static function getWarrantyList() {
        $menuname = "menu_tip";
        $menu = Business_Helpers_Common::getMenuLev($depth = 1, $parentid = 0, $menuname);
        $cateid = $menu[0]["itemid"];
        
    }

}

?>

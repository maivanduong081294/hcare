<?php

class Business_Import_Common {

    private static $_instance = null;

    // module news to store

    function __construct() {
        
    }

    /**
     * get instance of Business_Helpers_Common
     *
     * @return Business_Helpers_Common
     */
    public static function getInstance() {
        if (self::$_instance == null) {
            self::$_instance = new Business_Helpers_Common();
        }
        return self::$_instance;
    }
    
    

    public static function geticonbarHTML() {
$msg = <<<HTMLCONTENT
<div class="middle-box">    
    <div class="inner">
            <ul id="icon-bar" style="background:url(/v4/images/icon-bar-7.png) 0 0 no-repeat">
                <li class="li1">
                    <div id="li1" class="icon-bar-sub-content hide">
                        <span>&nbsp;</span>
                        <h3 class="orange">Đại lý điện thoại chính hãng</h3>
                        <p>
                            Hnammobile là thương hiệu lâu năm bán lẻ sản phẩm công nghệ Chính hãng uy tín tại TP.HCM.<br />
Hnammobile cam kết sản phẩm luôn chính gốc nhà sản xuất đi kèm với chế độ bảo hành chính hãng.<br />
Hnammobile có sẵn máy trưng bày trải nghiệm tại các cửa hàng.</p>
                </div>
            </li>
            <li class="li2">
                <div id="li2" class="icon-bar-sub-content hide">
                    <span>&nbsp;</span>
                    <h3 class="orange">Giao hàng miễn phí 2 giờ tại TP.HCM</h3>
                    <p>
                        Giao hàng miễn phí tại các quận nội thành TP.HCM trong vòng 2 giờ<br />
- Quận 1, quận 3, quận 4, quận 5, quận 10, quận 11, quận Phú Nhuận.<br />
- Q.Gò Vấp, Quận 6, Quận Tân Bình, Quận Bình Thạnh 
                    </p>
                </div>
            </li>
            <li class="li3">
                <div id="li3" class="icon-bar-sub-content hide" style="width:450px;">
                    <span>&nbsp;</span>
                    <h3 class="orange">Áp dụng đối với các sản phẩm là điện thoại, máy tính bảng chính hãng (hàng cty), phụ kiện</h3>
                    <p>Khách hàng được đổi trả <b>100%</b> giá trị máy lỗi trong 20 ngày</p>
                    <p>Khách hàng dùng thử, không thích có thể đổi trả đến <b>90%</b> giá máy</p>
                    <p>Khách hàng được đổi trả <b>miễn phí 10 ngày</b> (đối với phụ kiện)</p>
                </div>
            </li>
            <li class="li4">
                <div id="li4" class="icon-bar-sub-content hide">
                    <span>&nbsp;</span>
                    <h3 class="orange">Cài đặt miễn phí 100%</h3>
                    <p>Dịch vụ cài đặt miễn phí 100% trong suốt quá trình sử dụng</p>
                </div>
            </li>
            <li class="li5">
                <div id="li5" class="icon-bar-sub-content hide">
                    <span>&nbsp;</span>
                    <h3 class="orange">Luôn dẫn đầu về giá</h3>
                    <p>Giá luôn luôn rẻ nhất so với thị trường, cập nhật liên tục hàng giờ</p>  
                </div>
            </li>
        </ul>
    </div>
</div>
HTMLCONTENT;
        return $msg;
    }
    
    public static function getVistors() {

        $visitors = (int) Business_Common_Variables::variable_get('hitcounts', 1);
        Business_Common_Variables::variable_set('hitcounts', ++$visitors);

//            $visitors .= "";
//
//            $max = 6 - strlen($visitors);
//
//            if ($max > 0) {
//                for($i=0; $i<$max; $i++) {
//                    $visitors = "0" . $visitors;
//                }
//            }

        return number_format($visitors);
    }

    public static function fixNumber($str) {
        $max = 4 - strlen($str);

        if ($max > 0) {
            for ($i = 0; $i < $max; $i++) {
                $str = "0" . $str;
            }
        }

        return $str;
    }

    public static function getCurVisit() {
        $_online = Business_Ws_Online::getInstance();
        $ssid = new Zend_Session_Namespace('SSID');

        $total = $_online->getTotal();

        if ($total == 0) {
            $ssid->ssid = Business_Common_Utils::generateRandomWord(10);
            $_online->insert($ssid->ssid);
        } else {
            if (!isset($ssid->ssid)) {
                $ssid->ssid = Business_Common_Utils::generateRandomWord(10);
                //                        pre($_SESSION);
                $_online->insert($ssid->ssid);
            }
        }
        $total = $_online->getTotal();
//                Zend_Registry::set('online',$total);
        return $total;
    }

    public static function getMenuDetail($menuname, &$delta, &$cateid) {
        $menu_size = self::getMenuLev($depth = 1, 0, $menuname);
        $_menu = Business_Ws_MenuItem::getInstance();
        $detail = $_menu->getListByName($menuname);
        if (count($menu_size) > 0)
            foreach ($menu_size as $item) {
                $delta = $item['delta'];
                $cateid = $item['itemid'];
                if (count($detail) > 0)
                    return $detail;
                break;
            }
    }

    public static function getMenuName($cateid, $delta) {
        $_menuitem = Business_Ws_MenuItem::getInstance();

        $menu = $_menuitem->getDetailByDeltaAndCateID($cateid, $delta);
        return $menu;
    }

    public static function getMenuLev($depth = 1, $parentid = 0, $menuname = '', $ordering=null) {
        $_menuitem = Business_Ws_MenuItem::getInstance();
        if ($menuname == '')
            return null;
        $id = $parentid;
        $menu = $_menuitem->getListFilter($menuname, $id, $lang = 1, $depth, $ordering);
//        var_dump($menu);exit();
        return $menu;
    }

    public static function getMenuParentName($itemid) {
        $_menu = Business_Ws_MenuItem::getInstance();
        $detail = $_menu->getDetail($itemid);
        if (count($detail) > 0) {
            $parent = $_menu->getDetail($detail['pid']);
            if (count($parent) > 0)
                return $parent['title'];
        }
        return '';
    }

    public static function getMenuParentID($itemid) {
        $_menu = Business_Ws_MenuItem::getInstance();
        $detail = $_menu->getDetail($itemid);
        if (count($detail) > 0) {
            $parent = $_menu->getDetail($detail['pid']);
            if (count($parent) > 0)
                return $parent['itemid'];
        }
        return '';
    }

    public static function shortText($string = '', $numOfWords = 0) {
        $string = explode(" ", $string);
        $string = array_slice($string, 0, $numOfWords);
        $string = implode(" ", $string);
        return $string;
    }

    public static function getRelateCateid($cateid, $menuname) {

        $_menu = Business_Ws_MenuItem::getInstance();

        $detail = $_menu->getDetail($cateid);
        $pid = $detail['pid'];

        if ($pid == 0) {
            $pid = $detail['itemid'];
            $lev2 = self::getMenuLev($depth = 2, $pid, $menuname);

            if (count($lev2) > 0) {
                foreach ($lev2 as $item) {
                    $result[] = $item['itemid'];
                }
                $result = implode(",", $result);
                return $result;
            } else {
                return $cateid;
            }
        }
        return $cateid;
    }

    public static function circle($type = '') {
        $ret .= '<span style="position:absolute;left:-1px; top:-1px; width:6px; height:6px; z-index:10; overflow:hidden; display:block"><img src="/hnamv2/images/circle' . $type . '.png" style="position:absolute; left:0; top:0;" /></span>';
        $ret .= '<span style="position:absolute;right:-1px; top:-1px; width:6px; height:6px; z-index:10; overflow:hidden; display:block"><img src="/hnamv2/images/circle' . $type . '.png" style="position:absolute; right:0; top:0;" /></span>';
        $ret .= '<span style="position:absolute;left:-1px; bottom:-1px; width:6px; height:6px; z-index:10; overflow:hidden; display:block"><img src="/hnamv2/images/circle' . $type . '.png" style="position:absolute; left:0; bottom:0;" /></span>';
        $ret .= '<span style="position:absolute;right:-1px; bottom:-1px; width:6px; height:6px; z-index:10; overflow:hidden; display:block"><img src="/hnamv2/images/circle' . $type . '.png" style="position:absolute; right:0; bottom:0;" /></span>';
        return $ret;
    }

}

?>

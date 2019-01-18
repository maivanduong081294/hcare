<?php

class Business_Import_Products {

    private static $_instance = null;
    // module news to store
//    private $_mixPrice = array(34,40,190,249,254,264);
    private $_mixPrice = array(39, 34, 254);
    private $_mixPPrice = array(3630, 3631, 4445);
    private static $_installment_point = 5000000;
    private static $_low_installment_percent = 30;
    private static $_high_installment_percent = 30;
    private static $_appleID = array(264, 455, 40);

    function __construct() {
        
    }

    /**
     * get instance of Business_Helpers_Products
     *
     * @return Business_Helpers_Products
     */
    public static function getInstance() {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public static function getFullboxImgPath($itemid) {
        $path = BASE_PATH . "/v4/fullbox/" . $itemid .  ".jpg";
            
        if (is_file($path)) {
            return $path;
        }
        return null;
    }
    
    public static function getProductsListData($list, $prefix=null, $title, $negative=false) {
        $ret = '';
        $_helper = Business_Helpers_Products::getInstance();
    
        if ($prefix==null) {
            foreach ($list as $product) {
                $product = Business_Helpers_Products::getInstance()->updateProductDetail($product);
                $__item = $_helper->getItemHTML($product);
                $ret .= $__item;
            }
        } else {
            $ret = "<p class='pline orange'>$title</p>";
            foreach ($list as $product) {
                if (strpos($product["title"], $prefix)!==false ) {
                    if ($negative==false) {
                        $product = Business_Helpers_Products::getInstance()->updateProductDetail($product);
                        $__item = $_helper->getItemHTML($product);
                        $ret .= $__item;   
                    }
                } else {
                    if ($negative==true){
                        $product = Business_Helpers_Products::getInstance()->updateProductDetail($product);
                        $__item = $_helper->getItemHTML($product);
                        $ret .= $__item;                                            
                    }
                }
            }
        }
        return $ret;
    }
    
    public static function getProductsListData2($list, $prefix=null, $title, $negative=array()) {
        $ret = '';
        $_helper = Business_Helpers_Products::getInstance();
    
        
        if ($prefix==null) {
            foreach ($list as $product) {
                $product = Business_Helpers_Products::getInstance()->updateProductDetail($product);
                $__item = $_helper->getItemHTML($product);
                $ret .= $__item;
            }
        } else {
            $ret = "<p class='pline orange'>$title</p>";            
            foreach ($list as $product) {
                $has = false;
                    
                foreach($negative as $_p){
                    if (strpos($product["title"], $_p)!==false){
                        $has = true;
                        break;
                    }
                }
                if ($has == false){
                    if (strpos($product["title"], $prefix)!==false) {
                        if ($has==false) {
                            $product = Business_Helpers_Products::getInstance()->updateProductDetail($product);
                            $__item = $_helper->getItemHTML($product);
                            $ret .= $__item;   
                        }
                    }
                }
                
            }
        }
        return $ret;
    }
            
    
    public static function get360Path($itemid) {
        $path = BASE_PATH . "/v4/360/" . $itemid .  "/1.jpg";
                
        if (is_file($path)) {
            return BASE_PATH . "/v4/360/" . $itemid;
        }
        return null;
    }
    
    public static function adaptSearchKey($key) {
        if ($key==null) return "";
        $keys = explode("-", $key);
        return implode(" ", $keys);
    }
    
    public static function calculateInstallmentPrice($price, $percent) {
        if ($price <= 0)
            return 0;
//        if ($price < self::$_installment_point) {
//            return (self::$_low_installment_percent * $price) / 100;
//        } else {
//            return (self::$_high_installment_percent * $price) / 100;
//        }
            return ($percent * $price) / 100;
    }

    public static function getProductMenu() {
        $depth = 1;
        $parentid = 0;
        $menuname = 'menu_products';
        $leftMenuProducts = Business_Import_Common::getMenuLev($depth, $parentid, $menuname);
//        var_dump($leftMenuProducts);exit();
        if (count($leftMenuProducts) > 0) {
            $i = 0;
            foreach ($leftMenuProducts as &$item) {
                if ($item["itemid"] == 53)
                    continue; //skip kho may cu
                $item["title_encode"] = htmlspecialchars($item["title"]);
                $cateid = $item['itemid'];
                $title = Business_Common_Utils::adaptTitleLinkURLSEO($item['title']);
                if ($_cateid == $cateid)
                    $active = 'active'; else
                    $active = '';
                switch ($cateid) {
                    default:
                        $item['link'] = SEOPlugin::getProductLink($cateid, $title);
                        break;
                    case 40:
                        $item['link'] = Globals::getBaseUrl() . "dien-thoai/apple-iphone-chinh-hang.html";
                        break;
                    case 190:
                        $item['link'] = Globals::getBaseUrl() . "may-tinh-bang/apple-ipad-chinh-hang.html";
                        break;
                    case 45:
                        $item['link'] = Globals::getBaseUrl() . "dien-thoai/gia-blackberry-chinh-hang.html";
                        break;
                }

                if ($i++ == count($leftMenuProducts) - 1)
                    $item['class'] = 'class="' . $active . ' last"';
                else
                    $item['class'] = 'class=' . $active;
            }
        }

        return $leftMenuProducts;
    }

    public static function getAccMenu($ordering=null) {
        //get left accesories
        $depth = 1;
        $parentid = 0;
        $menuname = 'menu_acc';
        $leftMenuAccesories = Business_Import_Common::getMenuLev($depth, $parentid, $menuname, $ordering);
//        var_dump($leftMenuAccesories);exit();   
        $pos = 0;
        $_pos = 0;
        if (count($leftMenuAccesories) > 0) {
            $i = 0;
            foreach ($leftMenuAccesories as &$item) {
                $item["title_encode"] = htmlspecialchars($item["title"]);
                //fix viva		    
                $cateid = $item['itemid'];
                if ($cateid == 183)
                    $pos = $_pos;
                $_pos++;

                $title = Business_Common_Utils::adaptTitleLinkURLSEO($item['title']);
                if ($_cateid == $cateid)
                    $active = 'active'; else
                    $active = '';
                $item['link'] = SEOPlugin::getAccesoriesLink($cateid, $title);
                $item['link_encode'] = urlencode($item['link']);

                if ($i++ == count($leftMenuAccesories) - 1)
                    $item['class'] = 'class="' . $active . ' last"';
                else
                    $item['class'] = 'class="' . $active . '"';
                $item['link'] = SEOPlugin::getAccesoriesLink($cateid, $title);
                $lev2 = Business_Import_Common::getMenuLev($depth = 2, $parentid = $item['itemid'], $menuname, $ordering);
                if (count($lev2) > 0) {
//                        $item['title'] = $item['title'] . '<span class="red"> +</span>';
                    $item['title'] = $item['title'];
                    foreach ($lev2 as &$item2) {
                        $cateid2 = $item2['itemid'];
                        $item2["title_encode"] = urlencode($item2["title"]);

                        if ($cateid2 == $_cateid) {

                            if (strpos($item['class'], 'last') !== FALSE) {
                                $item['class'] = 'class="active last"';
                            } else {
                                $item['class'] = 'class="active"';
                            }
                            $item2['class'] = 'class="blue"';
                        }
                        $title2 = Business_Common_Utils::adaptTitleLinkURLSEO($item2['title']);
                        if ($_cateid == $cateid)
                            $active = 'active'; else
                            $active = '';
                        $item2['link'] = SEOPlugin::getAccesoriesLink($cateid2, $title2);
                        $item2['link_encode'] = urlencode($item2['link']);
                    }
                    $item['sub'] = $lev2;
//                        pre($item['sub']);
                }else {
                    $item['sub'] = null;
                }
            }
        }
        return $leftMenuAccesories;
    }

    public static function updateProductDetail($product) {
        $_mobile = Business_Helpers_Mobile::getInstance();
        $product["price_text"] = "";
        $thumb2 = json_decode(json_decode($product['thumb'])->thumb2);
        $product["installment"] = 0;

        switch ($product['onstock']) {
            case 0:
                $product["status"] = 'Hết hàng';
                $product["buy_online"] = false;
                break;
            case 1:
                $product["status"] = 'Đang có hàng';
                $product["buy_online"] = true;
                break;
            case 2:
                $product["status"] = 'Sắp có hàng';
                $product["buy_online"] = true;
                break;
        }

        foreach ($thumb2 as $t) {
            if ($t != null) {
                if ($count++ > 0) {
                    $product["thumb_detail"][] = Globals::getStaticUrl() . "uploads/products/details/" . $t;
                } else {
                    $product["thumb_home"][] = Globals::getStaticUrl() . "uploads/products/thumbnails/" . $t;
                }
            }
        }

        if ((int)$product["price"]>0) {
            $product["prefix_price"] = "Hàng Hnam";
            if ($product["price"] > 0) {
                $product["price_text"] = number_format($product["price"]);
                //tinh phi tra gop
                $product["installment"] = self::calculateInstallmentPrice($product["price"], 30);
                $product["installment4"] = self::calculateInstallmentPrice($product["price"], 40);
                $product["installment5"] = self::calculateInstallmentPrice($product["price"], 50);
                $product["installment6"] = self::calculateInstallmentPrice($product["price"], 60);
                $product["installment7"] = self::calculateInstallmentPrice($product["price"], 70);
                $product["ptype"] = 0;
                $product["bag_type"] = "H.Nam";
                
            }
                
            $product["ishnam"] = true;
            $product["price_color"] = "orange";
        } else {
            $product["prefix_price"] = "Hàng Công ty";
            
            if ($product["original_price"] > 0) {
                $product["price_text"] = number_format($product["original_price"]);
                //tinh phi tra gop
                $product["installment"] = self::calculateInstallmentPrice($product["original_price"], 30);
                $product["installment4"] = self::calculateInstallmentPrice($product["original_price"], 40);
                $product["installment5"] = self::calculateInstallmentPrice($product["original_price"], 50);
                $product["installment6"] = self::calculateInstallmentPrice($product["original_price"], 60);
                $product["installment7"] = self::calculateInstallmentPrice($product["original_price"], 70);
                $product["price"] = $product["original_price"];
                $product["ptype"] = 1;
                $product["bag_type"] = "Hàng Công ty";
            }
            
//            $product["vat"] = "Quý khách sẽ nhận hóa đơn V.A.T ngay sau khi mua hàng tại hnammobile";
//            $product["vat_lite"] = "Hàng chính hãng, giá đã bao gồm 10% VAT<br />";
            $product["prefix_type"] = "Máy tính bảng";
            $product["ishnam"] = false;
        }
            
        //update length of fullcontent
        if (strlen($product["fullcontent"]) > 1000) {
            $product["longcontent"] = true;
        } else {
            $product["longcontent"] = false;
        }

        if (strip_tags($product["fullcontent"]) == "" || strip_tags($product["fullcontent"]) == "&nbsp;") {
            $product["hascontent"] = false;
        } else {
            $product["hascontent"] = true;
        }

        $product["installment_text"] = number_format($product["installment"])."đ";
        $product["installment_text4"] = number_format($product["installment4"])."đ";
        $product["installment_text5"] = number_format($product["installment5"])."đ";
        $product["installment_text6"] = number_format($product["installment6"])."đ";
        $product["installment_text7"] = number_format($product["installment7"])."đ";

        //update khuyen mai        
//        if ($product["cateid"] == 455 || $product["cateid"] == 467) {
//            $product['bonus_company_full'] .= '<p>- Giảm giá 20-25% khi mua một số phụ kiện từ 01/09 - 30/09/2014 (<a style="color:red" target="_blank"	 href="http://www.hnammobile.com/tin-tuc/khuyen-mai-he--giam-gia-phu-kien-tu-20----50-.9809.html">Thông tin chi tiết</a>)</p>';
//        }
//        if ($product["cateid"] == 490) {
//            $product['bonus_company_full'] .= '<p>- Ưu đã mua <a style="color:red" target="_blank" href="http://www.hnammobile.com/phu-kien/tai-nghe-samsung-gh59-with-mic.5337.html">tai nghe Samsung GH59</a> with mic giá 90.000đ (giá niêm yết 160.000đ)</p>';
//        }

        //==========================trung tam bao hanh chinh hang
        $product["wlink"] = self::getWarrantyLinkV2($product["cateid"]);

        if ($_mobile->isPhone($product)) {
            $product["prefix_type"] = "Điện thoại";
        } else if ($_mobile->isTablet($product)) {
            $product["prefix_type"] = "Máy tính bảng";
        } else {
            $product["prefix_type"] = "Phụ kiện";
        }
        $product["title_encode"] = htmlspecialchars($product["title"]);
        $product["fullcontent_encode"] = htmlspecialchars($product["fullcontent"]);

        //-------------------update link fullbox
        $hasFullbox = Business_Helpers_Products::getFullboxImgPath($product["itemid"]);
        if ($hasFullbox!=null) {
            $product["fullboxpic_img"] = Globals::getBaseUrl() . "v4/fullbox/" . $product["itemid"] . ".jpg";
        }
        
        //----------------------get 360 view
        $has360 = Business_Helpers_Products::get360Path($product["itemid"]);
        if ($has360 != null) {
            $product["has360"] = true;
            $product["path_360"] = Globals::getBaseUrl() . "products/xml360?itemid=" . $product["itemid"];
        } else {
            $product["has360"] = false;            
        }   
        //----------------------end get 360 view
                
                
        $product["flash_path"] = "/v4/flash/run.swf";
        $product["flash_exp_path"] = "/v4/swfobject/expressInstall.swf";
        $__title = Business_Common_Utils::adaptTitleLinkURLSEO($product["title"]);
        $product["link"] = SEOPlugin::getProductDetailLink($product["itemid"], $__title);
        $product["payment_link"] = SEOPlugin::getProductDetailLink($product["itemid"], $__title) . "/mua-tra-gop";
        
        //check is apple products
        $lowername = strtolower($product["title"]);
        preg_match("/(iphone|ipad|apple|iwatch)/", $lowername, $matches);
        if (count($matches)>0) {
            $product["isApple"] = true;
        } else {
            $product["isApple"] = false;
        }
        preg_match("/(iphone)/", $lowername, $matches2);
        if (count($matches2)>0) {
            $product["isiPhone"] = true;
        } else {
            $product["isiPhone"] = false;
        }
        
        preg_match("/(iphone)/", $lowername, $matches);
        if (count($matches)>0 && ($product["original_price"]>0 || strpos($lowername, "cty"))) {
            $product["isiPhoneCty"] = true;            
        } else {
            $product["isiPhoneCty"] = false;
        }
        
        //check if has video
        if (strlen($product["video"])>100) {
            $product["has_video"] = true;
        } else {
            $product["has_video"] = false;
        }
        
        //check product type for compare
        if ($_mobile->isPhone($product)) {
            $product["comparetype"] = "dien-thoai";
        } else {
            $product["comparetype"] = "may-tinh-bang";
        }
        return $product;
    }

    public static function updateAccessoryDetail($product) {
        $product["price_text"] = "";
        $thumb1 = json_decode($product['thumb'])->thumb1;
        $thumb2 = json_decode(json_decode($product['thumb'])->thumb2);

        switch ($product['onstock']) {
            case 0:
                $product["status"] = 'Hết hàng';
                $product["buy_online"] = false;
                break;
            case 1:
                $product["status"] = 'Đang có hàng';
                $product["buy_online"] = true;
                break;
            case 2:
                $product["status"] = 'Sắp có hàng';
                $product["buy_online"] = true;
                break;
        }
        $product["thumb_home"] = Globals::getStaticUrl() . "uploads/accesories/homes/" . $thumb1;
        foreach ($thumb2 as $t) {
            if ($t != null) {
                $product["thumb_detail"][] = Globals::getStaticUrl() . "uploads/accesories/details/" . $t;
            }
        }
        $product["prefix_price"] = "Hàng HNAM: ";
        $product["prefix_type"] = "Phụ kiện";
        $product["title_encode"] = htmlspecialchars($product["title"]);
        $product["fullcontent"] = str_replace("&nbsp;&nbsp;&nbsp;", "", $product["fullcontent"]);
        $product["fullcontent"] = str_replace("&nbsp;", "", $product["fullcontent"]);
        $product["fullcontent"] = str_replace("  ", "", $product["fullcontent"]);
        if ($product["shortcontent"] == null) {
            $product["shortcontent"] = $product["fullcontent"];
        }
        $product["hascontent"] = true;

        //update product price text
        if ($product["price"] > 0) {
            $price = $product["price"];
            $price_text = number_format($price);
            $product["price_text"] = $price_text;
        }

        $product["hits"] = number_format($product["hits"]);
        
        //----------------------get 360 view
        $has360 = Business_Helpers_Products::get360Path($product["itemid"]);
        if ($has360 != null) {
            $product["has360"] = true;
            $product["path_360"] = Globals::getBaseUrl() . "products/xml360?itemid=" . $product["itemid"];
        } else {
            $product["has360"] = false;            
        }   
        //----------------------end get 360 view
        
        
        return $product;
    }
    
    public static function hasTrainghiemTNTechnica($product, $storeid) {
        //----------------------------------update san pham trai nghiem truc tiep------------------------------
        $arr_trainghiem_id = array(6,9,10,8453,7265);
        $arr_cate_id = array(583);
            
        //----------------------------------end update san pham trai nghiem truc tiep------------------------------
        if (in_array($storeid, $arr_trainghiem_id)) {
            if (in_array($product["cateid"], $arr_cate_id)) {
                return true;
            }
        }
        return false;
    }

    public static function updatePhoneDetail($product) {
        $product["price_text"] = "";
        $thumb1 = json_decode($product['thumb'])->thumb1;
        $thumb2 = json_decode(json_decode($product['thumb'])->thumb2);

        switch ($product['onstock']) {
            case 0:
                $product["status"] = 'Hết hàng';
                $product["buy_online"] = false;
                break;
            case 1:
                $product["status"] = 'Đang có hàng';
                $product["buy_online"] = true;
                break;
            case 2:
                $product["status"] = 'Sắp có hàng';
                $product["buy_online"] = true;
                break;
        }
        $product["thumb_home"] = Globals::getStaticUrl() . "uploads/products/homes/" . $thumb1;
        $product["title_encode"] = htmlspecialchars($product["title"]);
        $product["fullcontent_encode"] = htmlspecialchars($product["fullcontent"]);
        return $product;
    }

    public static function getWarrantyLinkV2($cateid) {
        if ($cateid == 0) {
            return "";
        }        
        $menu = Business_Ws_MenuItem::getInstance()->getDetail($cateid);
        $wid = $menu["warrantyid"];
        $wdetail = Business_Ws_NewsItem::getInstance()->getDetail($wid);
        if ($wdetail != null) {
            $brand = trim(strtolower(strip_tags($wdetail["shortcontent"])));
            $brand = str_replace("&nbsp;", "", $brand);
            $link = SEOPlugin::getWarrantyStoreLink($wid, $brand);
        } else {
            $link = "";
        }
        return $link;
    }

    public static function updateProductFeaturesDetail($features, $pDetail) {
        $isPhone = self::isPhone($pDetail);
        
        $isCallable = self::isCallable($pDetail);
        $dataCallable = null;

        if ($isCallable) {
            //update font callable
            $dataCallable[0] = $features[1];
            $dataCallable[0]["name"] = "Điện thoại,SMS";
            $dataCallable[0]["value"] = "Có";
            //push callable info to features
            array_splice($features, 1, 0, $dataCallable);
        } else if (!$isPhone) {
            $dataCallable[0] = $features[1];
            $dataCallable[0]["name"] = "Điện thoại,SMS";
            $dataCallable[0]["value"] = "Không";
            //push callable info to features
            array_splice($features, 1, 0, $dataCallable);
        }
        $isApple = Business_Helpers_Products::isPhone($pDetail);
        foreach ($features as &$fItem) {
            if (trim($fItem['value']) != '' && trim($fItem['value']) != '_' && strlen($fItem['value']) > 3) {
                $fItem["length"] = strlen($fItem["value"]);
                if ($fItem["length"] > 50) {
                    //has 2 line
                    $fItem["class"] = " dline";
                } else {
                    $fItem["class"] = "";
                }
                $_lv = strtolower($fItem['value']);
                if ($_lv == 'không' && strtolower($fItem['name']) == 'khe cắm thẻ nhớ' && !$isApple) {
                    $value = "Không (nhấn <a style='float:none;line-height:1em; padding:0; margin:0;font-size:12px; color:red' href='http://www.hnammobile.com/loai-phu-kien/usb-otg-flash-drive.526.html'>vào đây</a> để nâng cấp dung lượng)";
                    $fItem['value'] = $value;
                }
//            } else {
//                $fItem['value'] = "";
            }
        }
        //add color
        $color = explode(',', $fListShort[1]['value']);
        return $features;
    }

    public static function getFilterText($filter) {
        switch ($filter) {
            case 'pa-z':
                return "Giá tăng";
            case 'pz-a':
                return "Giá giảm";
            case 'a-z':
                return "Theo tên A-Z";
            case 'z-a':
                return "Theo tên Z-A";
            default:
                return "Mặc định";
        }
    }

    public static function getItemHTML($product, $target = "_parent") {
        $_helper = Business_Helpers_Mobile::getInstance();

        $titleSEO = Business_Common_Utils::adaptTitleLinkURLSEO($product['title']);
        $title = $product['title'];
        $thumb = json_decode($product['thumb']);
        $thumb2 = json_decode($thumb->thumb2);
        $image = Globals::getStaticUrl() . "uploads/products/thumbnails/" . $thumb2[0];
    
        //get bonus
        $bonus = $_helper->getBonus($product);
//        var_dump($bonus);exit();
        if ($bonus != "") {
            $bonus_text = '<span class="pitem-overlay"><img class="pitem-overlay-img" src="/v4/images/bg-km.png" alt="khuyen mai icon"/><span class="pitem-overlay-2">' . $bonus . '</span></span>';
            $bonus_tag = '<span class="km">KM</span>';
        }
        $dualsim =$_helper->getDualsim($product);
//        var_dump($dualsim);//exit();
        if($dualsim > 0){
            $dualsim_tag ='<span class="dualsim"> 2 Sim </span>';
        }
        //is new product 3 month
        $isNew = $_helper->isNew($product);
        if ($isNew) {
            $new_text = '<span class="new">NEW</span>';
        }
//        var_dump($new_text);//exit();
        //is comming soon
//        $isCommingSoon = $_helper->isCommingSoon2($product);
//        if ($isCommingSoon) {
//            $isCommingSoon_text = '<span class="cs">Sắp có hàng</span>';
//        }
        $isCommingSoon_text = "";
        $image_text = '<img src="' . $image . '" alt="' . $title . '" title="' . $title . '" />';

        //get price
        $link = $_helper->getLink($product, $titleSEO);
        if ($product["ishnam"]) {
            $price = $product["price"];
            if ($price > 0){
                $price_text = number_format($price);
                $price = '<h4 class="pitem-price-xt">HNAM: ' . $price_text . 'đ</h4>';
            } else {
                $price = "";
            }
        } else {
            $price = $product["original_price"];
            if ($price > 0) {
                $price_text = number_format($price);
                $price = '<h4 class="pitem-price-original">CTY: ' . $price_text . 'đ</h4>';
            } else {
                $price = "";
            }
//            else {
//                $price = "";
//                $out_text = '<span class="out">Hết hàng</span>';
//            }
        }
        
        //check out of stock
        if ($product["onstock"]==0) {
            $out_text = '<span class="out">Hết hàng</span>';
        } else if ($product["onstock"]==2) {
            $isCommingSoon_text = '<span class="cs">Sắp có hàng</span>';
        }

        if ($product["oldprice"] > 0) {
            $tt_price = number_format($product["oldprice"]);
            $tt_price_text = '<span class="pitem-price-tt">Thị trường: ' . $tt_price . 'đ</span>';
        }
        //get discount circle
        if ($product["itemid"]==5832) {
            $discount_circle = '<span style="height:30px; padding: 3px; border-radius: 100px; background-color: #f15723; position: absolute; right:3px; top:3px;color:#ffffff;font-size:12px;">Giảm<br />45%</span>';
        } else {
            $discount_circle = '';
        }

//<li class="pitem" onclick="window.open('$link','$target');">			
        $item = <<<HTMLCONTENT
        <li class="pitem">			
            <a target="$target" href="$link" title="$title" class="product-item">
                    $bonus_tag
                    $dualsim_tag
                    $new_text
                    $isCommingSoon_text					
                    $out_text	
                    $discount_circle
                    $image_text										
            </a>			
            <a target="$target" href="$link"><h3 class="pitem-name">$title_prefix $title</h3>
            $price
            $tt_price_text
            $bonus_text
            </a>
        </li>
HTMLCONTENT;

        return $item;
    }

    public static function getAccItemHTML($product, $target = "_parent") {
        $_helper = Business_Helpers_Mobile::getInstance();

        $titleSEO = Business_Common_Utils::adaptTitleLinkURLSEO($product['title']);
        $title = $product['title'];
        $thumb = json_decode($product['thumb']);
        $thumb2 = json_decode($thumb->thumb2);
        $image = Globals::getStaticUrl() . "uploads/accesories/details/" . $thumb2[0];
        //get bonus
        
        $bonus = $_helper->getBonus($product);
        $note = $product["note"];
        $note = strip_tags($note);
        
        if ($bonus != "") {
            $bonus_text = '<span class="pitem-overlay"><img class="pitem-overlay-img" src="/v4/images/bg-km.png" alt="khuyen mai icon"/><span class="pitem-overlay-2">' . $bonus . '</span></span>';
            $bonus_tag = '<span class="km">KM</span>';
        } else if($note != null) {
            $bonus_text = '<span class="pitem-overlay"><img class="pitem-overlay-img" src="/v4/images/bg-km.png" alt="khuyen mai icon"/><span class="pitem-overlay-2">' . $note . '</span></span>';            
            $bonus_tag = '<span class="km">KM</span>';            
        }

        //is new product 3 month
        $isNew = $_helper->isNew($product);
        if ($isNew) {
            $new_text = '<span class="new">NEW</span>';
        }

        //is comming soon
//        $isCommingSoon = $_helper->isCommingSoon($product);
//        if ($isCommingSoon) {
//            $isCommingSoon_text = '<span class="cs">Coming soon</span>';
//        }
        //is comming soon
        $isCommingSoon = $_helper->isCommingSoon2($product);
        if ($isCommingSoon) {
            $isCommingSoon_text = '<span class="cs">Sắp có hàng</span>';
        }
        $image_text = '<img src="' . $image . '" alt="' . $title . '" title="' . $title . '" />';

        //get price
        $link = $_helper->getLink($product, $titleSEO);
        if ($product["price"] > 0) {
            $price = $product["price"];
            $price_text = number_format($price);
            $price = '<h4 class="pitem-price-xt">HNAM: ' . $price_text . 'đ</h4>';
        } else {
            $price = $product["original_price"];
            if ($price > 0) {
                $price_text = number_format($price);
                $price = '<h4 class="pitem-price-original">CTY: ' . $price_text . 'đ</h4>';
            } else {
                $price = "";
                $out_text = '<span class="out">Hết hàng</span>';
            }
        }
        //check out of stock
        if ($product["onstock"]==0) {
            $out_text = '<span class="out">Hết hàng</span>';
        }
        
        
        if ($product["oldprice"] > 0) {
            $tt_price = number_format($product["oldprice"]);
            $tt_price_text = '<span class="pitem-price-tt">Thị trường: ' . $tt_price . 'đ</span>';
        }

//        <li class="pitem" onclick="window.open('$link','$target');">			
        $item = <<<HTMLCONTENT
        <li class="pitem">			
            <a target="$target" href="$link" title="$title" class="product-item">
                    $bonus_tag
                    $new_text
                    $isCommingSoon_text					
                    $out_text					
                    $image_text										
            </a>			
            <a target="$target" href="$link"><h3 class="pitem-name">$title</h3>
                $price
                $tt_price_text
                $bonus_text
            </a>
        </li>
HTMLCONTENT;

        return $item;
    }

    public function getMixPrice() {
        return $this->__mixPrice;
    }

    public function getMixPPrice() {
        return $this->__mixPPrice;
    }

    public static function getAccessoryColor($detail) {
        $_color = null;
        if (isset($detail['fullcontent'])) {
            $content = $detail['fullcontent'];

            preg_match("/(M&agrave;u :|M&agrave;u:)(.+)/", $content, $matches);

            if (count($matches) > 0) {
                $color = $matches[count($matches) - 1];
                $_color = explode(',', $color);
            }
        }
        return $_color;
    }

    public static function isPhone($product) {
        if (isset($product['productsid'])) {
            if ($product['productsid'] == 3)
                return true;
        }
        return false;
    }

    public static function isApple($product) {
        if (in_array($product["cateid"], self::$_appleID))
            return true;
        return false;
    }

    public static function isCallable($product) {
        if (isset($product['callable'])) {
            if ($product['callable'] == 1)
                return true;
        }
        return false;
    }

    public static function isTablet($product) {
        if (isset($product['productsid'])) {
            if ($product['productsid'] == 5)
                return true;
        }
        return false;
    }

    public static function isAccessory($product) {
        if (isset($product['productsid'])) {
            if ($product['productsid'] == 4)
                return true;
        }
        return false;
    }

    public static function checkProductIsStock($pid) {
        $_products = Business_Ws_ProductsItem::getInstance();
        $_deal = Business_Addon_Deal::getInstance();

        $pdetail = $_products->getDetail($pid);
        $datetime = $_deal->getInstance()->getLastestProductsTime($pid);

        $total_buyed = Business_Helpers_Products::getInstance()->getBuyedProduct($pid, $datetime);
        $dDetail = $_deal->getDetailByProduct_id($pid);
        if (count($dDetail) > 0) {
            if ($total_buyed >= $dDetail['quanlity']) {
                //set to het hang
                $pdetail['onstock'] = 0;
            } else {
                $pdetail['onstock'] = 1;
            }
            $_products->update($pdetail['itemid'], $pdetail['productsid'], $pdetail['cateid'], $pdetail);
        }
    }

    public static function isShipLocal($product_id) {
        $_deal = Business_Addon_Deal::getInstance();
        $detail = $_deal->getDetailByProduct_id($product_id);
        if ((int) $detail['is_ship_local'])
            return true;
        return false;
    }

    public static function productTypeShip($pid) {
        $_product = Business_Ws_ProductsItem::getInstance();
        $detail = $_product->getDetail($pid);
        $productsid = $detail['productsid'];
        $_deal = Business_Addon_Deal::getInstance();
        $dealDetail = $_deal->getDetailByProduct_id($productsid);
        if (count($dealDetail) > 0)
            $discountPrice = $dealDetail['discount'];
        else
            $discountPrice = 0;

        switch ($productsid) {

            case 4:
                return 50000;
                break;

            case 5:
                return 250000;
                break;
            default:
                // gia san pham duoi 1tr giam 100K, con lai giam 150K
                $orange = $detail['original_price'] - $discountPrice;
                $range = $detail['price'] - $discountPrice;

                if (($orange > 0 && $orange <= 1000000) || ($range > 0 && $range <= 1000000))
                    return 100000;
                return 150000;
                break;
        }
    }

    public function getBuyedProduct($product_id, $datetime) {
        if ((int) $product_id == 0 || $datetime == "")
            return 0;
        $_cart = Business_Ws_ShoppingcartItem::getInstance();

        $total = $_cart->getTotalBuyedProducts($product_id, $datetime);
        return $total;
    }

    public function getPercentDiscount($price, $discount_price) {
        if ($discount_price == 0)
            return array($price, $price);
        if ($price == 0)
            return 0;
        $percent = (int) ($discount_price * 100 / $price);
        return array($percent, $price - $discount_price);
    }

    public function getRemainTime($detail) {
        if (count($detail) > 0 && is_array($detail)) {

            $end_time = strtotime($detail['end_date']);
            $cur_time = time();

            $result = $end_time - $cur_time;

            return $result < 0 ? 0 : $result;
        }
        return 0;
    }

    public function getImagePath($productDetail, $size = "homes") {
//        pre($productDetail);
        if (isset($productDetail['thumb']) && $size == "thumbnails") {
            $thumb = (array) json_decode(json_decode($productDetail['thumb'])->thumb2);
            if ($productDetail['productsid'] != 4) {
                return Globals::getStaticUrl() . 'uploads/products/' . $size . '/' . $thumb[0];
            } else {

                return Globals::getStaticUrl() . 'uploads/accesories/' . $size . '/' . $thumb[0];
            }
        } else {
            if ($productDetail['productsid'] != 4)
                return Globals::getStaticUrl() . 'uploads/products/' . $size . '/' . json_decode($productDetail['thumb'])->thumb1;
            else {
                if ($size != "homes") {
                    $thumb = (array) json_decode(json_decode($productDetail['thumb'])->thumb2);
                    return Globals::getStaticUrl() . 'uploads/accesories/' . $size . '/' . $thumb[0];
                } else {
                    $thumb = json_decode($productDetail['thumb'])->thumb1;
                    return Globals::getStaticUrl() . 'uploads/accesories/' . $size . '/' . $thumb;
                }
            }
        }
    }

    public function getWhere($features) {
//                    pre($features);
        $_fdetail = Business_Addon_Featuresdata::getInstance();
        $aOperator = $_fdetail->getUniqueOpertor();

        //xu ly loai dien thoai

        $where[] = 'fd.pid = itemid';

        //man hinh cam ung FID=8; co chuoi :cam ung
        if ($features['touch'] == 1) {
            $where[] = 'fd.fid = 8 AND fd.value like "%cảm ứng%"';
        }

        //3G FID=38; !=''
        if ($features['3G'] == 1) {
            $where[] = 'fd.fid = 38 AND fd.value != ""';
        }

        //Wifi FID=39; !=''
        if ($features['wifi'] == 1) {
            $where[] = 'fd.fid = 39 AND fd.value != ""';
        }

        //kieu dang dien thoai dang: 1:thanh; 2:gap; 3:truot
        if ($features['type_allow'] == 1) {
            $where[] = 'fd.fid = 2 AND type = ' . (int) $features['type'];
        }

        //ban phim dien thoai dang: qwerty
        if ($features['qwerty'] == 1) {
            $where[] = 'fd.fid= 10 AND LOWER(fd.value) like "%qwerty%"';
        }

        //dien thoai 2sim tro len
        if ($features['sim'] == 1) {
            $where[] = 'fd.fid = 2 AND LOWER(title) like "%2 sim%"';
        }

        if ($features['camera_allow'] == 1) {
            if ($features['camera'] == '5down')
                $where[] = 'fd.fid = 60 AND (fd.value like "%1.%" OR fd.value like "%2.%" OR fd.value like "%3.%" OR fd.value like "%4.%" OR fd.value like "%5.%")';
            else
                $where[] = 'fd.fid = 60 AND fd.value != ""';
        }

        if ($features['operator_allow'] == 1) {
            $where[] = 'fd.fid = 44 AND LOWER(fd.value) like "%' . strtolower($aOperator[$features['operator']]) . '%"';
        }

        return $where;
    }

    public function getFeaturesDetailView2($pid) {
        $_products = Business_Ws_ProductsItem::getInstance();
        $_features = Business_Addon_Features::getInstance();
        $_featuresData = Business_Addon_Featuresdata::getInstance();

        $_fList = $_features->getListByPid($pid);
        $display = array(
            9,//kich co
            45, //cpu
            18, //bộ nhớ trong
            44, //hđh
            60, //camera chính
            63, //camera phụ
            20, //khe cắm thẻ nhớ
            56, //pin chuẩn
            50, //mau sắc
            64 //chipset
        );
        $ret = array();
        foreach($_fList as $item){
            if (in_array($item["fid"], $display)) {
                $ret[] = $item;
            }
                
        }
        return $ret;
    }
    
    public function getFeaturesDetail($pid) {
        $_products = Business_Ws_ProductsItem::getInstance();
        $_features = Business_Addon_Features::getInstance();
        $_featuresData = Business_Addon_Featuresdata::getInstance();

        $_fList = $_features->getListByPid($pid);
            
        $ret = array();
        $display = array(
            9,//kich co
            45, //cpu
            18, //bộ nhớ trong
            44, //hđh
            60, //camera chính
            63, //camera phụ
            20, //khe cắm thẻ nhớ
            56 //pin chuẩn
        );
        return $_fList;
    }

    public function getFeaturesDetailView($pid, $show = 1) {
        $_products = Business_Ws_ProductsItem::getInstance();
        $_features = Business_Addon_Features::getInstance();
        $_featuresData = Business_Addon_Featuresdata::getInstance();

        $_fList = $_features->getListByPid($pid, $show);        
        return $_fList;
    }
    

    /*
     * @param array: newest, super, cheap, highend, smartphone, normal, newcomes, tablet
     */

    public function getProductsByTypeV2($typename, $text, $limit = 15) {
        $_products = Business_Ws_ProductsItem::getInstance();
        $menuname = 'menu_products';
        Business_Import_Common::getMenuDetail($menuname, $delta, $cateid);
        $list = $_products->getListByType($typename, $limit, $delta);
        $menuname = 'menu_tablet';
        Business_Import_Common::getMenuDetail($menuname, $delta, $cateid);
        $list2 = $_products->getListByType($typename, $limit, $delta);
        $list = array_merge($list, $list2);

        $_month_between = 2592000 * 3; //1 months
//<div class="homeLine">
//        <div class="title utm">Mới xuất hiện</div>
//</div>        
        if (count($list) > 0) {
            $ret = '<div class="homeLine">
                    <div class="title utm">' . $text . '</div>
                    </div>';
            $ret .= '<div class="homeBlock">';
            $ret .= '<div class="mtoggle"><a href="#"><b>[Xem toàn bộ]</b></a></div>';
            foreach ($list as $product) {
                $title = Business_Common_Utils::adaptTitleLinkURLSEO($product['title']);
                $thumb = json_decode($product['thumb']);
                $image = Globals::getBaseUrl() . "/uploads/products/homes/" . $thumb->thumb1;
                if ($product['price'] > 0) {
                    if ($product['cateid'] != 40 && $product['cateid'] != 39 && !in_array($product['cateid'], $this->_mixPrice)) {
                        $_color = 'orange';
                        $_name = 'HNam';
                    } else {
                        $_name = 'Đen';
                        $_color = '';
                    }
                    $price = number_format($product['price']) . "đ";
//                    $price = '<p class="l12 bold orange tLeft ">&nbsp;&nbsp;&nbsp;&nbsp;'.$_name.'&nbsp;&nbsp;&nbsp;: '.$price.'</p>';
                    $price = '<p class="' . $_color . '"><b>' . $_name . ': ' . $price . '</b></p>';
                } else {
                    $price = '';
                }
                if ($product['original_price'] > 0) {
                    if ($product['cateid'] != 40 && $product['cateid'] != 39 && !in_array($product['cateid'], $this->_mixPrice)) {
                        $_oname = 'Công ty';
                        $_color = '';
                    } else {
                        $_oname = 'Trắng';
                        $_color = 'orange';
                    }
                    if (in_array($product['itemid'], $this->_mixPPrice)) {
                        $_oname = 'Công ty';
                        $_color = '';
                    }

                    $original_price = number_format($product['original_price']) . "đ";
//                    $original_price = '<p class="l12 bold dblue tLeft ">&nbsp;&nbsp;&nbsp;&nbsp;'.$_oname.': '.$original_price.'</p>';
                    $original_price = '<p class="' . $_color . '"><b>' . $_oname . ': ' . $original_price . '</b></p>';
                } else {
                    $original_price = '';
                }
                $bonus_full = trim(preg_replace("/<.*?>/", "", $product['bonus_company_full']));

                $bonus = ($bonus_full == '' || $bonus_full == "&nbsp;" ? $product['bonus_company'] : $product['bonus_company_full']);

                $bonus = ($bonus == '' ? $product['bonus_hnam'] : $bonus);

                if ($bonus == '')
                    $display_bonus = 'hide'; else
                    $display_bonus = '';
                if ((int) $product['discount_online'] == 0)
                    $display_bonus = 'hide'; else
                    $display_bonus = "";

                $bonusBlock = '';
                $bonusInfo = '';
                if ($bonus != '' & $bonus != null) {
                    $bonusBlock = '<p class="gift"></p>';
                    if ($product['discount_online'] > 0) {
                        $bonusBlock = '<p class="gift_bonus">Giảm ' . Business_Common_Utils::shortPrice($product['discount_online']) . '</p>';
                    }
                    $bonusInfo = '<div class="giftinfo">' . $bonus . '</div>';
                } else {
                    if ($product['discount_online'] > 0) {
                        $bonusBlock = '<p class="bonus">Giảm ' . Business_Common_Utils::shortPrice($product['discount_online']) . '</p>';
                    }
                }
                $curdate = strtotime(date('Y-m-d'));
                $created = strtotime(date($product['posteddate']));
                if (($curdate - $created) <= $_month_between)
                    $status = '<p class="new"></p>';
                else
                    $status = '';
                //status: con hang, het hang, sap co hang, hang moi
                if ($status == '') {
                    switch ($product['onstock']) {
                        case 0://hethang
                            $status = '<p class="out"></p>';
                            break;
                        case 1: // con hang
                            $status = '';
                            break;
                        case 2://sap co hang
                            $status = '<p class="come"></p>';
                            break;
                    }
                }
                if ($text != 'mayTinhBang' && $text != 'Máy tính bảng') {
                    $__link = SEOPlugin::getProductDetailLink($product['itemid'], $title);
                    $__title = $product['title'];
                    $ret .= '     
                    <div class="round pitem">                            
                            ' . $status . '
                            <p class="img"><a href="' . $__link . '"><img src="' . $image . '" title="' . $product['title'] . '" /></a></p>
                            <p class="pname"><a href="' . $__link . '">' . $__title . '</a></p>                            
                            ' . $original_price . '
                            ' . $price . '                            
                            ' . $bonusBlock . '                            
                            ' . $bonusInfo . '                            
                    </div>';
                } else {
                    //may tinh bang
                    $__link = SEOPlugin::getTabletDetailLink($product['itemid'], $title);
                    $__title = $product['title'];
                    $ret .= '     
                        <div class="round pitem">                            
                                ' . $status . '
                                <p class="img"><a href="' . $__link . '"><img src="' . $image . '" title="' . $product['title'] . '" /></a></p>
                                <p class="pname"><a href="' . $__link . '">' . $__title . '</a></p>                            
                                ' . $original_price . '
                                ' . $price . '                            
                                ' . $bonusBlock . '                            
                                ' . $bonusInfo . '                            
                        </div>';
                }
            }
            $ret .= '</div>';
            return $ret;
        }
    }

    /*
     * @param array: newest, super, cheap, highend, smartphone, normal, newcomes, tablet
     */

    public function getProductsByType($typename, $text, $limit = 12) {
        $_products = Business_Ws_ProductsItem::getInstance();
        $_helper = Business_Helpers_Products::getInstance();
        $menuname = 'menu_products';
        Business_Import_Common::getMenuDetail($menuname, $delta, $cateid);
        $list = $_products->getListByType($typename, $limit, $delta);
        $menuname = 'menu_tablet';
        Business_Import_Common::getMenuDetail($menuname, $delta, $cateid);
        $list2 = $_products->getListByType($typename, $limit, $delta);
        $list = array_merge($list, $list2);
        $anchor = Business_Common_Utils::adaptTextSEO($text);
        if (count($list) > 0) {
            $ret = '<h2 class="home-header">';
            $ret .= '<a href="' . Business_Common_Utils::getCurrentURL() . '#!' . $anchor . '" class="hide"></a>';
            $ret .= '<span>' . $text . '</span><b class="home-view-block home-plus" id="' . $anchor . '"><i>Xem tất cả</i> <i class="plus">&nbsp;</i></b>';
            $ret .= '</h2>';
            $ret .= '<ul class="pitem-block pitem-plus">';
            foreach ($list as $product) {
                $item = $_helper->getItemHTML($product);
                $ret .= $item;
            }
            $ret .= '</ul>';
            return $ret;
        }
    }

    public function getNewComeProducts($limit = 3) {
        $typename = "smartphone";
        $_products = Business_Ws_ProductsItem::getInstance();
        $_helper = Business_Helpers_Products::getInstance();
        $menuname = 'menu_products';
        Business_Import_Common::getMenuDetail($menuname, $delta, $cateid);
        $list = $_products->getListByType($typename, $limit, $delta);
        $menuname = 'menu_tablet';
        Business_Import_Common::getMenuDetail($menuname, $delta, $cateid);
        $list2 = $_products->getListByType($typename, $limit, $delta);
        $list = array_merge($list, $list2);
        return $list;
    }

    public function updateProducts($product) {
        $title = Business_Common_Utils::adaptTitleLinkURLSEO($product['title']);
        $product = Business_Helpers_Products::updateProductDetail($product);
        $thumb = json_decode($product['thumb']);
        $image = Globals::getBaseUrl() . "/uploads/products/homes/" . $thumb->thumb1;
        if ($product['price'] > 0) {
            $price = number_format($product['price']) . "đ";
            $price = '<p class="l12 bold orange tLeft ">&nbsp;&nbsp;&nbsp;&nbsp;Hnam&nbsp;&nbsp;&nbsp;: ' . $price . '</p>';
        } else {
            $price = '';
        }
        if ($product['original_price'] > 0) {
            $original_price = number_format($product['original_price']) . "đ";
            $original_price = '<p class="l12 bold dblue tLeft ">&nbsp;&nbsp;&nbsp;&nbsp;Công ty: ' . $original_price . '</p>';
        } else {
            $original_price = '';
        }
        $bonus = ($product['bonus_company'] == '' ? $product['bonus_hnam'] : $product['bonus_company']);
        if ($bonus != '' & $bonus != null)
            $bonus = '<div class="bonus">+ ' . $bonus . '</div>' . $newItem;
        else
            $bonus = '';
        $curdate = strtotime(date('Y-m-d'));
        $created = strtotime(date($product['posteddate']));
        if (($curdate - $created) <= $_month_between)
//                $newItem = '<div class="new"><img src="'.Globals::getBaseUrl().'hnamv2/images/icon-new.png" alt="san pham moi" /></div>';
            $newItem = '<div class="new"><img src="' . Globals::getBaseUrl() . 'hnamv2/images/icon-new.png" alt="san pham moi" /></div>';
        else
            $newItem = '';

        $ret .= '                        
                <div class="itemp">
                        <div class="pic"><a href="' . SEOPlugin::getProductDetailLink($product['itemid'], $title) . '"><img class="product" src="' . $image . '" alt="' . $product['title'] . '"/></a></div>
                        <p class="ptitle"><a href="" class="bold black small l12">' . $product['title'] . '</a></p>
                        ' . $original_price . '
                        ' . $price . '
                        ' . $bonus . '
                </div>';
        $ret .= "</div>";
        return $ret;
    }

    public function getAccesories($limit = 15, $__productsid = null, $__cateid = null) {

        $_products = Business_Ws_ProductsItem::getInstance();
        $menuname = 'menu_acc';

        Business_Import_Common::getMenuDetail($menuname, $delta, $cateid);
        if ($__productsid == null && $__cateid == null)
            $list = $_products->getListByTypeForAcc("newest", $limit, $delta);
        else {

            $list = $_products->getListByProIdPaging($__productsid, $__cateid, $offset = 0, $limit, $_filter = '', $enable = 1, $_onstock, $key = '', $filter);
        }
        $_month_between = 2592000; //1 months
        if (count($list) > 0) {
            if ($__productsid == null && $__cateid == null) {
                $ret = '<div class="homeLine">
                    <div class="title utm">Phụ kiện mới</div>
                    </div>';
                $ret .= '<div class="homeBlock">';
                $ret .= '<div class="mtoggle"><a href="#"><b>[Xem toàn bộ]</b></a></div>';
            }
            foreach ($list as $product) {
                $title = Business_Common_Utils::adaptTitleLinkURLSEO($product['title']);
                $thumb = json_decode($product['thumb']);
                $image = Globals::getBaseUrl() . "uploads/accesories/homes/" . $thumb->thumb1;
                $price = "<p class='orange bold'>Hnam: " . number_format($product['price']) . "đ</p>";

                $curdate = strtotime(date('Y-m-d'));
                $created = strtotime(date($product['posteddate']));
                if (($curdate - $created) <= $_month_between)
//                    $newItem = '<div class="new"><img src="'.Globals::getBaseUrl().'hnamv2/images/icon-new.png" alt="san pham moi" /></div>';
                    $newItem = '<p class="new"></new>';
                else
                    $newItem = '';
                $__title = $product['title'];
                $__link = SEOPlugin::getAccesoriesDetailLink($product['itemid'], $title);
//                $ret .= '                        
//                    <div class="itemp">
//                            <a href="'.SEOPlugin::getAccesoriesDetailLink($product['itemid'], $title).'"><img style="margin-top:15px;" class="product" src="'.$image.'" alt="'.$product['title'].'"/></a>
//                            <p class="ptitle" ><a href="" class="bold black small l12">'.$product['title'].'</a></p>
//                            <p class="l12 bold orange tLeft ">&nbsp;&nbsp;&nbsp;&nbsp;Hnam&nbsp;&nbsp;&nbsp;: '.$price.'</p>						                            
//                    </div>';

                $bonusBlock = '';
                $bonus = ($product['note'] == '' ? '' : $product['note']);
                $bonusInfo = '';
                if ($bonus != '' & $bonus != null) {
                    $bonusBlock = '<p class="gift"></p>';
                    //$bonusBlock = $product['note'];                        					
                    $bonusInfo = '<div class="giftinfo">' . $bonus . '</div>';
                } else {
                    $bonusBlock = '';
                }
                $ret .= '     
                        <div class="round pitem">                            
                                <p class="img"><a href="' . $__link . '"><img src="' . $image . '" title="' . $product['title'] . '" /></a></p>
                                <p class="pname"><a href="' . $__link . '">' . $__title . '</a></p>                            
                                ' . $price . '                            
                                ' . $newItem . ' 
                                ' . $bonusInfo . ' 								
                                ' . $bonusBlock . ' 								
                        </div>';
            }
            return $ret;
        }
    }

    public function getAccesoriesV2($limit = 15, $__productsid = null, $__cateid = null) {

        $_products = Business_Ws_ProductsItem::getInstance();
        $menuname = 'menu_acc';

        Business_Import_Common::getMenuDetail($menuname, $delta, $cateid);
        if ($__productsid == null && $__cateid == null)
            $list = $_products->getListByTypeForAcc("newest", $limit, $delta);
        else {
            $list = $_products->getListByProIdPaging($__productsid, $__cateid, $offset = 0, $limit, $_filter = '', $enable = 1, $_onstock, $key = '', $filter);
        }
        $anchor = "phu-kien-dien-thoai-moi";
        $_helper = Business_Helpers_Products::getInstance();
        $text = "Phụ kiện điện thoại mới";
        if (count($list) > 0) {
            $ret = '<h2 class="home-header">';
            $ret .= '<a href="' . Business_Common_Utils::getCurrentURL() . '#!' . $anchor . '" class="hide"></a>';
            $ret .= '<span>' . $text . '</span><b class="home-view-block home-plus" id="' . $anchor . '"><i>Xem tất cả</i> <i class="plus">&nbsp;</i></b>';
            $ret .= '</h2>';
            $ret .= '<ul class="pitem-block pitem-plus">';
            foreach ($list as $product) {
                $item = $_helper->getAccItemHTML($product);
                $ret .= $item;
            }
            $ret .= '</ul>';
            return $ret;
        }
    }

    public function getAccesoriesLite($limit = 15, $__productsid = null, $__cateid = null) {

        $_products = Business_Ws_ProductsItem::getInstance();
        $menuname = 'menu_acc';

        Business_Import_Common::getMenuDetail($menuname, $delta, $cateid);
        if ($__productsid == null && $__cateid == null)
            $list = $_products->getListByType("newest", $limit, $delta);
        else {

            $list = $_products->getListByProIdPaging($__productsid, $__cateid, $offset = 0, $limit, $_filter = '', $enable = 1, $_onstock, $key = '', $filter);
        }
        return $list;
    }

    /*
     * @get list for mobile app
     * @param array: newest, super, cheap, highend, smartphone, normal, newcomes, tablet
     */

    public function getProductsByTypeLite($typename, $text, $limit = 15) {
        $_products = Business_Ws_ProductsItem::getInstance();
        $menuname = 'menu_products';
        Business_Import_Common::getMenuDetail($menuname, $delta, $cateid);
        $list = $_products->getListByType($typename, $limit, $delta);
        $menuname = 'menu_tablet';
        Business_Import_Common::getMenuDetail($menuname, $delta, $cateid);
        $list2 = $_products->getListByType($typename, $limit, $delta);
        $list = array_merge($list, $list2);
        return $list;
    }

    public function isAllowDeliveryFast($pDetail) {
        if ($pDetail["original_price"] >= 1500000) {
            //hang chinh hang
            return true;
        }
        return false;
    }

    public function getMatchAccesories($pDetail, $limit=1000) {
        $menuname = 'menu_acc';

        Business_Import_Common::getMenuDetail($menuname, $productsid, $cateid);

        $_products = Business_Ws_ProductsItem::getInstance();
        $accList = $_products->getListAll((int)$productsid, $limit);
        
        $accList = array_reverse($accList);

        $accids = array();
        if ($pDetail["accid"]!="") {
            $accids = trim($pDetail["accid"], ",");    
            $accList = Business_Ws_ProductsItem::getInstance()->getListByProductsID($accids);           
            foreach ($accList as $a){
                $result[] = $a;
            }
        }
        
        foreach ($accList as $item) {

            if ($item['search_text'] != '') {
                $text = "," . $item['search_text'] . ",";
                $text = str_replace(",,", ",", $text);
                $parentid = Business_Import_Common::getMenuParentID($pDetail['cateid']);
                if (strpos($text, "," . $pDetail['itemid'] . ",") !== FALSE
                        || strpos($text, "," . $pDetail['cateid'] . ",") !== FALSE
                        || strpos($text, "," . $parentid . ",") !== FALSE) {
                    $result[] = $item;
                }
            }
        }
        
        $arrItems = array(3906, 3905, 3907, 3909, 3908);
        $arrDefaultCateid = array(396, 320, 290, 377, 425, 180, 187, 447);

        foreach ($accList as $item) {
//	    }
            if (in_array($item['cateid'], $arrDefaultCateid)) {
                if ($item["enabled"] == 0)
                    continue;
                $result[] = $item;
            }
        }

        foreach ($accList as $item) {
            if ($item["enabled"] == 0)
                continue;
            if ($item['cateid'] == 33)
                $result[] = $item;
            if (strpos(strtolower($item['search_text']), "all") !== FALSE) {
                $result[] = $item;
            }
        }
        return $result;
    }

    public function getMatchAccesoriesV2($pDetail) {
        $accids = $pDetail["accid"];
        if ($accids != null) {
            $accList = Business_Ws_ProductsItem::getInstance()->getListByProductsID($accids);
            foreach ($accList as &$acc) {
                $acc = Business_Helpers_Products::getInstance()->updateAccessoryDetail($acc);
            }
            return $accList;
        } else {
            if (Business_Helpers_Products::isAccessory($pDetail)) {
                //get same price
                $itemid = $pDetail["itemid"];
                $productsid = $pDetail["productsid"];
                $cateid = $pDetail["cateid"];
                $price = $pDetail["price"];
                $oprice = 0;
                $accList = Business_Ws_ProductsItem::getInstance()->getProductByPrice($itemid, $productsid, $cateid, $price, $oprice, 0, 8, 200000);
                foreach ($accList as &$acc) {
                    $acc = Business_Helpers_Products::getInstance()->updateAccessoryDetail($acc);
                }
                return $accList;
            }
        }
        return null;
    }

    public function getMatchProductsV2($pDetail) {
        $accids = $pDetail["phoneid"];
        $accids = trim($accids, ",");

        if ($accids != null) {
            $pList = Business_Ws_ProductsItem::getInstance()->getListByProductsID($accids);
            foreach ($pList as &$p) {
                $p = Business_Helpers_Products::getInstance()->updateProductDetail($p);
            }
            return $pList;
        } else {
            //get same price
            $itemid = $pDetail["itemid"];
            $productsid = $pDetail["productsid"];
            $cateid = $pDetail["cateid"];
            $price = $pDetail["price"];
            $oprice = 0;
            $pList = Business_Ws_ProductsItem::getInstance()->getProductByPrice($itemid, $productsid, $cateid, $price, $oprice, 0, 4);
                
            foreach ($pList as &$product) {
                $product = Business_Helpers_Products::getInstance()->updateProductDetail($product);
            }
            return $pList;
        }
        return null;
    }

    public static function getStatusText($product) {
        switch ($product['onstock']) {
            case 0://hethang
                $status = 'Hết hàng';
                break;
            case 1: // con hang
                $status = 'Đang có hàng';
                break;
            case 2://sap co hang
                $status = 'Sắp có hàng';
                break;
        }
        return $status;
    }

}

?>

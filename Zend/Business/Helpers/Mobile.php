<?php

class Business_Helpers_Mobile {

    private $_productsid;
    private $_newsid;
    private $_cateid;
    private $_menu = 'menu_menu';
    private $_session;
    private $_other_acc_list = array(99, 251, 104, 183, 272, 134, 314);
    private $_notCompanyListCateId = array(34, 40, 249, 254, 264);
    private $_companyListItemsId = array(3630, 3631, 4445);
    private $_hidden_menu = array(183,387);
    private static $_instance = null;

    /**
     * get instance of Business_Helpers_Mobile
     *
     * @return Business_Helpers_Mobile
     */
    public static function getInstance() {
        if (self::$_instance == null) {
            self::$_instance = new Business_Helpers_Mobile();
        }
        return self::$_instance;
    }

    /* $menuid[] = array(
      1 => "Điện thoại",
      2 => "Máy tính bảng",
      3 => "Phụ kiện",
      4 => "Sản phẩm khác",
      5 => "Kho máy cũ"
      ); */

    public function getMenu() {

        $obj["id"] = 1;
        $obj["name"] = "Điện thoại";
        $obj["link"] = "/phone";
        $menuid[] = $obj;
        $obj["id"] = 2;
        $obj["name"] = "Máy tính bảng";
        $obj["link"] = "/tablet";
        $menuid[] = $obj;
        $obj["id"] = 3;
        $obj["name"] = "Phụ kiện";
        $obj["link"] = "/acc";
        $menuid[] = $obj;
        $obj["id"] = 4;
        $obj["name"] = "Sản phẩm khác";
        $obj["link"] = "/other";
        $menuid[] = $obj;
        $obj["id"] = 5;
        $obj["name"] = "Kho máy cũ";
        $obj["link"] = "/usedphone";
        $menuid[] = $obj;
        return $menuid;
    }

    /*
     * get category list of phone, tablet, accessories and other products
     * input
     * int cateid: 
     *  1: PHONE
     *  2: TABLET
     *  3: ACCESSORIES
     *  4: OTHER PRODUCTS
     *  5: USED PHONE
     *  
     */

    public function getCateList($cateid) {
        $result = array();

        switch ($cateid) {
            case 1:
                //phone
                $depth = 1;
                $parentid = 0;
                $menuname = 'menu_products';
                $lists = Business_Helpers_Common::getMenuLev($depth, $parentid, $menuname);

                foreach ($lists as $menu) {
                    if (strpos(strtolower($menu['title']), "kho máy cũ") === FALSE) {
                        $data['cateid'] = $menu['itemid'];
                        $data['title'] = $menu['title'];
                        $data['image'] = "http://www.hnammobile.com/uploads/data/" . json_decode($menu['metadata'])->picture;
                        $data['link'] = "/product/list?id=" . $menu['itemid'];
                        $result[] = $data;
                    }
                }
                break;
            case 2:
                //tablet
                $depth = 1;
                $parentid = 0;
                $menuname = 'menu_tablet';
                $lists = Business_Helpers_Common::getMenuLev($depth, $parentid, $menuname);

                foreach ($lists as $menu) {
                    $data['cateid'] = $menu['itemid'];
                    $data['title'] = $menu['title'];
                    $data['image'] = "http://www.hnammobile.com/uploads/data/" . json_decode($menu['metadata'])->picture;
                    $data['link'] = "/tablet/list?id=" . $menu['itemid'];
                    $result[] = $data;
                }
                break;
            case 3:
                //accessories
                $depth = 1;
                $parentid = 0;
                $menuname = 'menu_acc';
                $lists = Business_Helpers_Common::getMenuLev($depth, $parentid, $menuname);

                foreach ($lists as $menu) {
		     if (in_array($menu["itemid"], $this->_hidden_menu)) continue;
                    if (!in_array($menu['itemid'], $this->_other_acc_list)) {
                        $data['cateid'] = $menu['itemid'];
                        $data['title'] = $menu['title'];
                        $data['image'] = "http://www.hnammobile.com/uploads/data/" . json_decode($menu['metadata'])->picture;
                        $data['link'] = "/acc/list?id=" . $menu['itemid'];
                        $result[] = $data;
                    }
                }
                break;
            case 4:
                //other products
                $depth = 1;
                $parentid = 0;
                $menuname = 'menu_acc';
                $lists = Business_Helpers_Common::getMenuLev($depth, $parentid, $menuname);

                foreach ($lists as $menu) {
                    if (in_array($menu["itemid"], $this->_hidden_menu)) continue;
                    if (in_array($menu['itemid'], $this->_other_acc_list)) {
                        $data['cateid'] = $menu['itemid'];
                        $data['title'] = $menu['title'];
                        $data['image'] = "http://www.hnammobile.com/uploads/data/" . json_decode($menu['metadata'])->picture;
                        $data['link'] = "/other/list?id=" . $menu['itemid'];
                        $result[] = $data;
                    }
                }
                break;
            case 5:
                //usedphone
                $depth = 1;
                $parentid = 0;
                $menuname = 'menu_products';
                $lists = Business_Helpers_Common::getMenuLev($depth, $parentid, $menuname);

                foreach ($lists as $menu) {
                    if (strpos(strtolower($menu['title']), "kho máy cũ") !== FALSE) {
                        $data['cateid'] = $menu['itemid'];
                        $data['title'] = $menu['title'];
                        $data['image'] = "http://www.hnammobile.com/uploads/data/" . json_decode($menu['metadata'])->picture;
                        $data['link'] = "/usedphone?id=" . $menu['itemid'];
                        $result[] = $data;
                    }
                }
                break;
        }
        return $result;
    }

    /*
     * get list items of cate
     * input:
     * int CATEID
     * int STATUS
     *  1: Có hàng
     *  0: Hết hàng
     *  2: Sắp có hàng
     *  3: Tất cả
     */

    public function getItemsAction($cateid, $status, $enable = 1, $limit = 0) {

        if ($status == 3)
            $status = "";

        if ($limit > 0) {
            $offset = 0;
            $records = $limit;
        }

        $result = array();

        $_products = Business_Ws_ProductsItem::getInstance();

        $offset = 0;
        $records = 1000;


        $list = $_products->getListByProIdPaging(
                null, $cateid, $offset, $records, $_filter = '', $enable, $status, $key = '', $filter
        );
        foreach ($list as $item) {

            $thumb = json_decode($item['thumb']);
            if (Business_Helpers_Products::isAccessory($item))
                $image = "http://www.hnammobile.com/uploads/accesories/homes/" . $thumb->thumb1;
            else
                $image = "http://www.hnammobile.com/uploads/products/homes/" . $thumb->thumb1;

            $data = array();
            $data['itemid'] = $item['itemid'];
            $data['cateid'] = $item['cateid'];
            $data['title'] = $item['title'];
//                $data['shortdes'] = $item['shortcontent'];                
//                $data['fulldes'] = $item['fullcontent'];                
            $data['packet'] = (string) $item['packet'];
            $data['ishnam'] = $this->isHnam($item);
            $data['bonus'] = (string) $this->getBonus($item);
            if ($this->isHnam($item)) {
//                $data['price_color_black'] = (int) $item['price'];
//                $data['price_color_white'] = (int) $item['original_price'];
                $data['price1'] = (int) $item['price'];
                $data['price2'] = (int) $item['original_price'];
            } else {
//                $data['price_type_hnam'] = (int) $item['price'];
//                $data['price_type_company'] = (int) $item['original_price'];
                $data['price1'] = (int) $item['price'];
                $data['price2'] = (int) $item['original_price'];
            }
            $data['discount'] = (int) $item['discount'];
            $data['discount_online'] = (int) $item['discount_online'];
            $data['status'] = (int) $item['onstock'];
            $data["isnew"] = $this->isNewProduct($item);
            $data["isacc"] = $this->isAcc($item);
            $data["image"] = $image;
            $data["note"] = $item["note"];
            $data["bonus_mobile"] = $item["bonus_mobile"];
            $data["callable"] = $item["callable"];
            $data["discountid"] = $item["discountid"];
            $result[] = $data;
//            echo "<pre>";
//            var_dump($data);
//            die();
                
        }

        return $result;
    }

    public function getCateListHomeAction() {

        $_ph = Business_Helpers_Products::getInstance();

        $result = array();

        $listName = array(
            "newest" => "Mới xuất hiện",
            "super" => "Hàng siêu cấp",
            "tablet" => "Máy tính bảng HOT",
            "cheap" => "Hàng siêu rẻ",
            "highend" => "Hàng cao cấp",
            "discount" => "Vừa giảm giá",
            "smartphone" => "SmartPhone",
            "normal" => "Hàng phổ thông"
        );
        $i = 0;
        foreach ($listName as $k => $v) {
            $newest = $_ph->getProductsByTypeLite($typename = $k, $v);
            $newest = $this->updateCateListHome($newest);
            $result[$i]["name"] = $v;
            $result[$i++]["data"] = $newest;
        }

        $acc = $_ph->getAccesoriesLite(15);
        $acc = $this->updateProductList($acc);
        $result[$i]["name"] = "Phụ kiện mới";
        $result[$i]["data"] = $acc;

        return $result;
    }

    public function updateProductList($list) {
        $result = array();
        foreach ($list as $item) {

            $thumb = json_decode($item['thumb']);
            if (Business_Helpers_Products::isAccessory($item))
                $image = Globals::getBaseUrl() . "uploads/accesories/homes/" . $thumb->thumb1;
            else
                $image = Globals::getBaseUrl() . "uploads/products/homes/" . $thumb->thumb1;

            $data = array();
            $data['itemid'] = $item['itemid'];
            $data['cateid'] = $item['cateid'];
            $data['productsid'] = $item['productsid'];
            $data['title'] = $item['title'];
//                $data['shortdes'] = $item['shortcontent'];                
//                $data['fulldes'] = $item['fullcontent'];                
            $data['packet'] = (string) $item['packet'];
            $data['ishnam'] = $this->isHnam($item);
            $data['bonus'] = (string) $this->getBonus($item);
            if ($this->isHnam($item)) {
//                $data['price_color_black'] = (int) $item['price'];
//                $data['price_color_white'] = (int) $item['original_price'];
                $data['price1'] = (int) $item['price'];
                $data['price2'] = (int) $item['original_price'];
            } else {
//                $data['price_type_hnam'] = (int) $item['price'];
//                $data['price_type_company'] = (int) $item['original_price'];
                $data['price1'] = (int) $item['price'];
                $data['price2'] = (int) $item['original_price'];
            }
            $data['discount'] = (int) $item['discount'];
            $data['discount_online'] = (int) $item['discount_online'];
            $data['status'] = (int) $item['onstock'];
            $data['image'] = $image;
            $data['note'] = $item['note'];
            $data["isnew"] = $this->isNewProduct($item);
            $data["link"] = $this->buildLinkProduct($item);
            $result[] = $data;
        }
        return $result;
    }

    private function isNewProduct($item) {
        $_month_between = 2592000; //1 months
        $curdate = strtotime(date('Y-m-d'));
        $created = strtotime(date($item['posteddate']));
        if (($curdate - $created) <= $_month_between)
            return true;
        else
            return false;
    }

    public function isUsedPhone($item) {
        if ($item['cateid'] != 53) {
            return false;
        }        
        return true;
    }
    
    public function isHnam($item) {
//        if (in_array($item['itemid'], $this->_companyListItemsId)) {
//            return false;
//        }
//        if (in_array($item['cateid'], $this->_notCompanyListCateId)) {
//            return true;
//        }
//        return false;
        //new update
        if ($item["price"]>0) return true;
        return false;
    }
    public function getDualsim($item){
        $dualsim = $item['dualsim'];
//        var_dump($dualsim);
        return $dualsim;
        
    }

    public function getBonus($item) {
        $bonus = "";
//        if ($this->isHnam($item))
//            $bonus = $item["bonus_hnam"];
//        else {
            if ($item["bonus_company_full"] != "") {
                $bonus = str_replace("<br />", "__br__", $item['bonus_company_full']);
                $bonus = trim(preg_replace("/<.*?>/", "", $bonus));
//                $bonus = $item["bonus_company_full"];
            } else {
                $bonus = $item["bonus_hnam"];
            }
//        }
        $bonus = strip_tags($bonus);
        $bonus = str_replace("__br__", "<br />", $bonus);
        return $bonus;
    }

    private function updateCateListHome($list) {
        $result = array();
        foreach ($list as $item) {

            $thumb = json_decode($item['thumb']);
            $image = "http://www.hnammobile.com/uploads/products/homes/" . $thumb->thumb1;
            $data = array();
            $data['itemid'] = $item['itemid'];
            $data['cateid'] = $item['cateid'];
            $data['title'] = $this->wraptext($item['title']);
//                $data['shortdes'] = $item['shortcontent'];                
//                $data['fulldes'] = $item['fullcontent'];                
            $data['packet'] = (string) $item['packet'];
            $data['ishnam'] = $this->isHnam($item);
            $data['bonus'] = (string) $this->getBonus($item);
            if ($this->isHnam($item)) {
//                $data['price_color_black'] = (int) $item['price'];
//                $data['price_color_white'] = (int) $item['original_price'];
                $data['price1'] = (int) $item['price'];
                $data['price2'] = (int) $item['original_price'];
            } else {
//                $data['price_type_hnam'] = (int) $item['price'];
//                $data['price_type_company'] = (int) $item['original_price'];
                $data['price1'] = (int) $item['price'];
                $data['price2'] = (int) $item['original_price'];
            }
            $data['discount'] = (int) $item['discount'];
            $data['discount_online'] = (int) $item['discount_online'];
            $data['status'] = (int) $item['onstock'];
            $data['image'] = $image;
            $data["isnew"] = $this->isNewProduct($item);
            $data["link"] = $this->buildLinkProduct($item);
            $result[] = $data;
        }
        return $result;
    }

    public function getCateInfo($cateid) {
        return Business_Ws_MenuItem::getInstance()->getDetail($cateid);
    }

    private function wraptext($str) {
        $ret = $str;
//        $limit = 4;
//        if (strlen($str)>30) {
//            $str = explode(" ", $str);
//            $part1 = array_slice($str, 0, $limit);
//            $part2 = array_slice($str, $limit, strlen($str));
//            $ret = implode(" ", $part1) . "<br />" . implode(" ", $part2);
//        }
        return $ret;
    }

    public function getItemDetail($id) {
        $_news = Business_Ws_NewsItem::getInstance();
        $itemid = $id;
        if ($itemid > 0) {
            $item = Business_Ws_ProductsItem::getInstance()->getDetail($itemid);

            $data = array();
            $data['itemid'] = $item['itemid'];
            $data['cateid'] = $item['cateid'];
            $data['title'] = $item['title'];
            $data['productscode'] = $item['productscode'];
            $data['warranty'] = $item['warranty'];
            $data['packet'] = (string) $item['packet'];
            $data['ishnam'] = $this->isHnam($item);
            $data['onstock'] = $item['onstock'];
            $data['bonus'] = (string) $this->getBonus($item);
            if ($this->isHnam($item)) {
//                $data['price_color_black'] = (int) $item['price'];
//                $data['price_color_white'] = (int) $item['original_price'];
                $data['price1'] = (int) $item['price'];
                $data['price2'] = (int) $item['original_price'];
            } else {
//                $data['price_type_hnam'] = (int) $item['price'];
//                $data['price_type_company'] = (int) $item['original_price'];
                $data['price1'] = (int) $item['price'];
                $data['price2'] = (int) $item['original_price'];
            }
            $data['discount'] = (int) $item['discount'];
            $data['discount_online'] = (int) $item['discount_online'];
            $data['status'] = (int) $item['onstock'];
            $data['image'] = $image;
            $data['note'] = $item['note'];
            $data['isusedphone'] = $this->isUsedPhone($item);
            $data["isnew"] = $this->isNewProduct($item);
            $data["discountid"] = $item["discountid"];
            $thumb = json_decode(json_decode($item['thumb'])->thumb2);
            $i = 0;
            if ($this->isAcc($item)) {
                $prefix = "accesories";
                $prefix2 = "details";
            } else {
                $prefix = "products";
                $prefix2 = "thumbnails";
            }
            foreach ($thumb as &$img) {
                if ($img == '')
                    continue;
                if ($i++ == 0) {
                    $img = "http://www.hnammobile.com/uploads/$prefix/$prefix2/" . $img;
                } else {
                    $img = "http://www.hnammobile.com/uploads/$prefix/details/" . $img;
                }
            }
            $data["thumb"] = $thumb;

            //get color of content

            $content = $item['fullcontent'];
            preg_match("/(M&agrave;u :|M&agrave;u:)(.+)/", $content, $matches);

            if (count($matches) > 0) {
                $color = $matches[count($matches) - 1];
                $colors = explode("<", $color);
                $color = $colors[0];
//                $this->view->colors = explode(',', $color);
                $color = explode("<", $color);
                $color = $color[0];

                $data["color"] = $color;
            } else {
                $data["color"] = '';
            }

            //get trung tam bao hanh
            $link = $this->getWarrantyStore($item);
            if ($link != "#") {
                $id = explode(".", $link);
                $id = $id[1];
                $news = $_news->getDetail($id);

                $data["warranty_store"] = $news["fullcontent"];
            } else {
                $data["warranty_store"] = "#";
            }
            //get tong quan
            $data["info"] = $item["fullcontent"];

            //get thong tin dap hop
            $data["unbox"] = $item["unbox"];

            //get thong tin goi dien cho MTB
            $data["callable"] = $item["callable"];
            
            //get thong so ky thuat
            $_ph = Business_Helpers_Products::getInstance();
            $features = $_ph->getFeaturesDetail($data['itemid']);
            $ret = "";
            $pid = $data["itemid"];
            $ret = '<table width="100%" cellpadding="5" cellspacing="1" style="background-color:#CCCCCC;">';
            foreach ($features as $_item) {

                $_name = array();
                $_name[] = (int) $_item['fid'];
                $_name[] = (int) $pid;
                $_name[] = (int) $_item['parentid'];
                $name = "val[f_" . implode("_", $_name) . "]";
                $value = $_item['value'];
                if (strlen($value) > 200) {
                    $height = 'height:150px';
                } else {
                    $height = 'height:25px';
                }
                if ($_item['parentid'] == 0) {
                    $ret .= '<tr style="background-color:#CCCCCC;"><td colspan="2"><b style="font-size:small;padding:3px;">' . $_item['name'] . '</b></td></tr>';
                } else { //has fielf
                    if ($k++==0) {
                        //them thong tin goi dien, sms
                        if ($item["callable"] && Business_Helpers_Products::isTablet($item)) {
                            $ret .= '<tr style="background-color:#f0f0f0">';
                            $ret .= '<td width="90" style="font-family:Arial,Tahoma;font-size:small;"><span style="padding:3px;">Điện thoại, SMS</span></td>';
                            $ret .= '<td class="l12 bgWhite" style="font-family:Arial,Tahoma;font-size:small;"><span style="padding:3px;">Có</span></td>';
                            $ret .= '</tr>';
                        } else if( Business_Helpers_Products::isTablet($item)) {
                            $ret .= '<tr style="background-color:#f0f0f0">';
                            $ret .= '<td width="90" style="font-family:Arial,Tahoma;font-size:small;"><span style="padding:3px;">Điện thoại, SMS</span></td>';
                            $ret .= '<td class="l12 bgWhite" style="font-family:Arial,Tahoma;font-size:small;"><span style="padding:3px;">Không</span></td>';
                            $ret .= '</tr>';
                        }
                        continue;
                    }
                    $ret .= '<tr style="background-color:#f0f0f0">';
                    $ret .= '<td width="100" style="font-family:Arial,Tahoma;font-size:small;"><span style="padding:3px;">' . $_item['name'] . '</span></td>';
                    $ret .= '<td class="l12 bgWhite" style="font-family:Arial,Tahoma;font-size:small;"><span style="padding:3px;">' . $value . '</span></td>';
                    $ret .= '</tr>';
                }
            }
            $ret .= '</table>';

            $data["features"] = $ret;

            return $data;
        }
    }

    public function buildCateListLink($cateid, $type) {
        return "/$type/list?id=$cateid";
    }

    public function buildLinkProduct($item) {
        if ($this->isPhone($item)) {
            return "/product/detail?id=" . $item["itemid"] . "&cateid=" . $item["cateid"] . "&type=product";
        }
        if ($this->isTablet($item)) {
            return "/product/detail?id=" . $item["itemid"] . "&cateid=" . $item["cateid"] . "&type=tablet";
        }
        if ($this->isAcc($item)) {
            return "/product/detail?id=" . $item["itemid"] . "&cateid=" . $item["cateid"] . "&type=acc";
        }
        return "/product/detail?id=" . $item["itemid"] . "&cateid=" . $item["cateid"] . "&type=other";
    }
    
    public function buildLinkProductSearch($item) {
        if ($this->isPhone($item)) {
            return "/product/detail?id=" . $item["itemid"] . "&cateid=" . $item["cateid"] . "&type=product&b=s";
        }
        if ($this->isTablet($item)) {
            return "/product/detail?id=" . $item["itemid"] . "&cateid=" . $item["cateid"] . "&type=tablet&b=s";
        }
        if ($this->isAcc($item)) {
            return "/product/detail?id=" . $item["itemid"] . "&cateid=" . $item["cateid"] . "&type=acc&b=s";
        }
        return "/product/detail?id=" . $item["itemid"] . "&cateid=" . $item["cateid"] . "&type=other&b=s";
    }

    public function buildLinkProductDetail($itemid, $cateid, $type) {
        return "/product/detail?id=" . $itemid . "&cateid=" . $cateid . "&type=$type";
    }

    public function isTablet($item) {
        if ($item["productsid"] == 5)
            return true;
        return false;
    }

    public function isPhone($item) {
        if ($item["productsid"] == 3)
            return true;
        return false;
    }

    public function isAcc($item) {
        if ($item["productsid"] != 5 && $item["productsid"] != 3)
            return true;
        return false;
    }

    public function getWarrantyStore($pDetail) {
        switch ($pDetail['cateid']) {
            case 32: //htc
                $wlink = "/tin-tuc/trung-tam-bao-hanh-chinh-hang-htc.5317.html";
                break;
            case 186: //htc tablet
                $wlink = "/tin-tuc/trung-tam-bao-hanh-chinh-hang-htc.5317.html";
                break;
            case 39: //apple iphone
                $wlink = "";
                break;
            case 137: //ipad 3
            case 284: //ipad 4
            case 285: //ipad mini
                $wlink = "/tin-tuc/trung-tam-bao-hanh-chinh-hang-apple-ipad.6226.html";
                break;
            case 138: //archos
            case 173: //archos
                $wlink = "/tin-tuc/trung-tam-bao-hanh-chinh-hang-archos---ornova.4419.html";
                break;
            case 54: //archos tablet
                $wlink = "/tin-tuc/trung-tam-bao-hanh-chinh-hang-archos.4419.html";
                break;
            case 41: //nokia
                $wlink = "/tin-tuc/trung-tam-bao-hanh-chinh-hang-nokia.4280.html";
                break;
            case 42: //samsung
                $wlink = "/tin-tuc/trung-tam-bao-hanh-chinh-hang-samsung.4281.html";
                break;
            case 174: //samsung tablet
                $wlink = "/tin-tuc/trung-tam-bao-hanh-chinh-hang-samsung.4281.html";
                break;
            case 185: //sony ericsson
            case 413: //sony ericssontablet
                //$wlink = "/tin-tuc/trung-tam-bao-hanh-chinh-hang-sony-ericsson.4283.html";
                $wlink = "/tin-tuc/trung-tam-bao-hanh-chinh-hang-sony.8223.html";

                break;
            case 43: //sony ericsson
                $wlink = "/tin-tuc/trung-tam-bao-hanh-chinh-hang-sony-ericsson.4283.html";
                break;
            case 44: //lg
                $wlink = "/tin-tuc/trung-tam-bao-hanh-chinh-hang-lg.4282.html";
                break;
            case 45: //Bb
                $wlink = "/tin-tuc/trung-tam-bao-hanh-chinh-hang-blackberry.5750.html";
                break;
            case 193: //Bb tablet
                $wlink = "/tin-tuc/trung-tam-bao-hanh-chinh-hang-blackberry.5750.html";
                break;
            case 46: //moto
                $wlink = "/tin-tuc/trung-tam-bao-hanh-chinh-hang-motorola.4284.html";
                break;
            case 40: //4s
            case 264: //iphone 5
                $wlink = "/tin-tuc/trung-tam-bao-hanh-chinh-hang-apple-iphone-5-.8031.html";
                break;
            case 265: //Dell
                $wlink = "/tin-tuc/trung-tam-bao-hanh-chinh-hang-dell.4285.html";
                break;
            case 48: //qmobile
                $wlink = "/tin-tuc/trung-tam-bao-hanh-chinh-hang-q-mobile.4286.html";
                break;
            case 50: //alcatel
                $wlink = "/tin-tuc/trung-tam-bao-hanh-chinh-hang-alcatel.4287.html";
                break;
            case 22: //mobell
                $wlink = "";
                break;
            case 24: //cayon
                $wlink = "";
                break;
            case 25: //k-touch
                $wlink = "";
                break;
            case 51: //lenovo
                $wlink = "/tin-tuc/trung-tam-bao-hanh-chinh-hang-lenovo.6182.html";
                break;
            case 52: //inomobile
                $wlink = "/tin-tuc/trung-tam-bao-hanh-chinh-hang-inomobile.6181.html";
                break;
            case 227: //huwaai
                $wlink = "/tin-tuc/trung-tam-bao-hanh-chinh-hang-huawei.6880.html";
                break;
            case 266: //apple 3gs 
                $wlink = "/tin-tuc/trung-tam-bao-hanh-chinh-hang-apple-iphone.7004.html";
                break;

            case 7: //used phone
                $wlink = "";
                break;
            case 49: //viettel
                $wlink = "/tin-tuc/trung-tam-bao-hanh-chinh-hang-viettel.5751.html";
                break;
            case 263: //viettel
                $wlink = "/tin-tuc/trung-tam-bao-hanh-chinh-hang-asus.7365.html";
                break;
            case 354: //oppo
                $wlink = "/tin-tuc/trung-tam-bao-hanh-chinh-hang-oppo.7708.html";
                break;
            case 335: //philips
                $wlink = "/tin-tuc/trung-tam-bao-hanh-chinh-hang-philips.7709.html";
                break;
            case 361: //sharp
                $wlink = "/tin-tuc/trung-tam-bao-hanh-chinh-hang-sharp.7897.html";
                break;
            case 362: //acer
                $wlink = "/tin-tuc/trung-tam-bao-hanh-chinh-hang-acer.7898.html";
                break;
            case 437: //alcatel
                $wlink = "/tin-tuc/trung-tam-bao-hanh-chinh-hang-alcatel.8222.html";
                break;
            case 439: //lenovo
                $wlink = "/tin-tuc/trung-tam-bao-hanh-chinh-hang-lenovo-tablet.8241.html";
                break;
            case 490:
                $wlink = "/tin-tuc/trung-tam-bao-hanh-chinh-hang-asus.7365.html";
                break;
            case 484:
                $wlink = "/tin-tuc/trung-tam-bao-hanh-chinh-hang-lg.4282.html";
                break;
            case 459:
                $wlink = "/tin-tuc/trung-tam-bao-hanh-chinh-hang-may-tinh-bang-hp.8853.html";
                break;
            case 494:
                $wlink = "/tin-tuc/trung-tam-bao-hanh-chinh-hang-huawei.6880.html";
            case 513:
                $wlink = "/tin-tuc/trung-tam-bao-hanh-chinh-hang-hp.8853.html";
            case 529:
                $wlink = "/tin-tuc/trung-tam-bao-hanh-chinh-hang-nextbook.9751.html";
                break;
            default:
                $wlink = "";
                break;
        
        }
        return $wlink;
    }

    public function search($key, $type) {
        if ($type=="pt") {
            $delta = '3,5';
        } else {
            $delta = '4';
        }
        $_products = Business_Ws_ProductsItem::getInstance();
        
        $cateid = 0;
        $offset = 0;
        $records = 1000;
        $_filter = "";
        $_onstock = "";
        $where = "";
            
        $list = $_products->getListByProIdPaging($delta, $cateid, $offset, $records, $_filter = '', $enable = 1, $_onstock=1, $key, $filter = 'pa-z', $price_start = 0, $price_end = 1000000000, $where);
        $list = $this->updateProductList($list);
        return $list;
    }

    public function isNew($product) {
        $_month_between =  2592000 * 3;//1 tháng = 2592000
        $curdate = strtotime(date('Y-m-d'));
        $created = strtotime(date($product['posteddate']));
        if (($curdate - $created) <= $_month_between)
           return true;
        return false;
    }
    
    public function isCommingSoon($product) {
        if ($product["smartphone"]==1) {
            return true;
        }
        return false;
    }
    
    public function isCommingSoon2($product) {
        if ($product["onstock"]==2) {
            return true;
        }
        return false;
    }
    
    public function getLink($product, $titleSEO) {
        if ($this->isPhone($product)) {
            $link = SEOPlugin::getProductDetailLink($product['itemid'], $titleSEO); 
        } elseif($this->isTablet($product)) {
            $link = SEOPlugin::getTabletDetailLink($product['itemid'], $titleSEO);
        } else {
            $link = SEOPlugin::getAccesoriesDetailLink($product['itemid'], $titleSEO);
        }
        return $link;
    }
}

?>
<?php

class Business_Helpers_Used {

    private static $_instance = null;

    // module news to store

    function __construct() {
        
    }

    /**
     * get instance of Business_Helpers_Used
     *
     * @return Business_Helpers_Used
     */
    public static function getInstance() {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    public static function getUsedPhone() {
        $cateid = 53;
        $productsid=0;
        $offset=0;
        $records=200;
        $price_start=0;
        $enable=1;
        $list = Business_Ws_ProductsItem::getInstance()->getListByProIdPaging($productsid, $cateid, $offset, $records, $filter, $enable, $onstock, $key, $sort, $price_start);
        foreach($list as $item) {
            $ids[] = $item["itemid"];
        }
        $itemid = implode(",", $ids);
        $listUsed = Business_Addon_Usedphonehistory::getInstance()->getListPast5DaysByID($itemid);
        return $listUsed;
            
    }

    public static function resendEmail($usedinfoid, $itemid) {
        $usedinfoDetail = Business_Addon_Usedphoneinfo::getInstance()->getDetail($usedinfoid);
        $usedid = $usedinfoDetail["addon_usedphone_id"];
        $used = Business_Addon_Usedphone::getInstance()->getDetail($usedid);
        $used_id = (int) $usedid;
        if ($used_id ==0 || $used["itemid_tmp"]==0 ) return $used["datetime"] . " - $usedid - ID Null";
        
        if ($used["vouchers6"]>0) {
            return $used["datetime"] . " - $usedid - Chuong trinh s6";
        }
        
        $_used_history = Business_Addon_Usedphonehistory::getInstance();
        $listHistory = $_used_history->getListByItemID($itemid);
        
        $prices = "";
        $hasData = 0;
        
        $pdetail = Business_Ws_ProductsItem::getInstance()->getDetail($itemid);
        if ($pdetail["onstock"]==0 || $pdetail["enabled"]==0 || ($pdetail["price"]==0 && $pdetail["original_price"]==0)){
            $hasData = 0;
            return $used["datetime"] .  " - $usedid - Da ban";
        } else {
            foreach ($listHistory as $h) {
                $prices .= "<p style='padding:0 0 0 10px;line-height:120%;color:blue'><b>Lần " . ++$i . ":</b> " . number_format($h["price"]) . " - " . date("d/m/Y H:i:s", strtotime($h["datetime"])). "</p>";
                $hasData = 1;
            }
        }
        
        if ($hasData > 0) {
            $used_info = Business_Addon_Usedphoneinfo::getInstance()->getDetailByUsedID($used_id);
                
            $phone_name = $used["name"];
            $IMEI = $used["imei"];
            $product_cate_name = self::getProductCateName($used_info["product_cate"]);
            $color = $used_info["color"];
            $store_name = self::getFullStorename($used["storeid"]);
            $price = number_format($used["price"]) . "đ";
            $voucher = number_format($used_cus["price"]) . "đ";
            $info = $used_info["info"];
            $warranty = $used_info["warranty"];
            $accessory = $used_info["accessory"];
            $datetime = date("d/m/Y H:i:s", strtotime($used["datetime"]));

            $url = Globals::getBaseUrl() . "admin/user/usedphone/update-info2?id=" . $usedinfoid;

            $msg = <<<HTMLCONTENT
<p style="line-height:200%;color:#ff6600; font-size:14px; font-weight:bold">
        Hnammobile - Hệ thống bán lẻ điện thoại di động chính hãng
        </p>
<p style="line-height:120%">
<b>Thông tin cập nhật máy cũ</b>
</p>
<p style="line-height:120%"><b>Ngày thu máy:</b> $datetime</p>
<p style="line-height:120%"><b>Tên máy:</b> $phone_name</p>
<p style="line-height:120%"><b>IMEI:</b> $IMEI</p>
<p style="line-height:120%"><b>Loại:</b> $product_cate_name</p>
<p style="line-height:120%"><b>Màu:</b> $color</p>
<p style="line-height:120%"><b>Giá thu vào:</b> $price</p>
<p style="line-height:120%"><b>Voucher phát hành:</b> $voucher</p>
<p style="line-height:120%"><b>Chi nhánh:</b> $store_name</p>
<p style="line-height:120%"><b>Tình trạng:</b> $info</p>
<p style="line-height:120%"><b>Bảo hành:</b> $warranty</p>
<p style="line-height:120%"><b>Phụ kiện:</b> $accessory</p>
<p style="line-height:120%"><b style="color:red">Lịch sử giá</b></p>
$prices            
<p style="line-height:120%">&nbsp;</p>
<p style="line-height:200%"><b>Link cập nhật giá sản phẩm:</b> $url</p>
    
HTMLCONTENT;
	    echo $msg;
            $from = "khomaycu@hnammobile.com";
	    $displayname = "Hnammobile";
	    $replyto = $from;
            
            $auth = Zend_Auth::getInstance();
            $identity = (array) $auth->getIdentity();
            $sid = $identity["username"];
        
            $_sid = $used["storeid"];        
            $storeemail = Business_Helpers_Used::getStoreEmail($_sid);

            if (APP_ENV == "development") {
                $to = "nghi.dang@hnammobile.com";
                $cc = array("dangvannghi37@gmail.com");
            } else {
                $to = "duyhuy@hnammobile.com";			
                $cc = array("trongnhan@hnammobile.com","kinhdoanh@hnammobile.com","nghi.dang@hnammobile.com","hapt.hnam@gmail.com","$storeemail");			
            }
            $subject = "Thông tin cập nhật máy cũ - " . $phone_name . " - " . $store_name;
            $body_html = $msg;  
			if ($_REQUEST["se"]==1){
				$result = Business_Common_Utils::sendEmailKMC($from, $displayname, $replyto, $to, $subject, $body_html, $file_attached,"used", $cc);
			}
			if ($_REQUEST["se"]==2){
				$to = "dangvannghi37@gmail.com";
				$cc = array("hiepsikhokhao@gmail.com");
				$result = Business_Common_Utils::sendEmailKMC($from, $displayname, $replyto, $to, $subject, $body_html, $file_attached,"used", $cc);
			}
            return $result;
        } else {
            return "";
        }
    }

    public static function  getStoreEmail($username) {
        $array = array(	
            "vote_148" => "ngocdiep@hnammobile.com",
            "vote_191" => "vanvangs@hnammobile.com",
            "vote_206" => "bichphuong@hnammobile.com",
            "vote_294" => "trinhnk.hnam@gmail.com",
            "vote_253" => "thuytrang@hnammobile.com",
            "vote_301" => "kienphu@hnammobile.com",
            "vote_370" => "ngocdiep@hnammobile.com",
            "vote_654" => "vanvang@hnammobile.com",
            "vote_67" => "vangot@hnammobile.com",
            "vote_774" => "thuyntn.hnam@gmail.com",
            "vote_89" => "thanhhuyen@hnammobile.com",
            "vote_43" => "duyanh@hnammobile.com",
            "vote_776" => "duyanh@hnammobile.com",
            "vote_778" => "duyanh@hnammobile.com",
			"vote_1047" => "phuongthao@hnammobile.com", //ngochung@hnammobile.com
            "vote_all" => "nghi.dang@hnammobile.com",
            "vote_112" => "minhthinh@hnammobile.com",
            "vote_492" => "thuthao@hnammobile.com"
        );
        $ret = $array[$username];
        if ($ret == null) {
            $ret = "nghi.dang@hnammobile.com";
        }
        return $ret;
    }

    public static function getFullStorename($username) {

        $listStore = Business_Helpers_Store::getInstance()->getList();
        if ($username == "vote_778") {
            $username = "vote_776";
        }
        foreach ($listStore as $store) {
            $title = $store["title"];
            $usernames = explode("_", $username);
            if (strpos($title, $usernames[1]) !== false) {
                return $title;
            }
        }
        return "admin";
    }

    public static function getProductCateName($id) {
        if ($id == 1) {
            return "Công ty";
        }
        return "Xách tay";
    }

}

?>

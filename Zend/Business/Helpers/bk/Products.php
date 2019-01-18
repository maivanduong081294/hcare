<?php
class Business_Helpers_Products
{
    private static $_instance = null;
    // module news to store
//    private $_mixPrice = array(34,40,190,249,254,264);
    private $_mixPrice = array(39,34,254);
    private $_mixPPrice = array(3630,3631,4445);
    function __construct()
    {
    }

    /**
     * get instance of Business_Helpers_Products
     *
     * @return Business_Helpers_Products
     */
    public static function getInstance()
    {
            if(self::$_instance == null)
            {
                    self::$_instance = new Business_Helpers_Products();
            }
            return self::$_instance;
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

            if (count($matches)>0){
                $color = $matches[count($matches)-1];                             
                $_color = explode(',', $color);
            }
        }
        return $_color;
    }
    
    public static function isPhone($product) {
        if (isset($product['productsid'])) {
            if ($product['productsid'] == 3) return true;
        }
        return false;
    }
    
    public static function isTablet($product) {
        if (isset($product['productsid'])) {
            if ($product['productsid'] == 5) return true;
        }
        return false;
    }
    
    public static function isAccessory($product) {
        if (isset($product['productsid'])) {
            if ($product['productsid'] == 4) return true;
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
        if (count($dDetail)>0) {
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
        if ((int)$detail['is_ship_local']) return true;
        return false;
    }
    
    public static function productTypeShip($pid) {
        $_product = Business_Ws_ProductsItem::getInstance();
        $detail = $_product->getDetail($pid);
        $productsid = $detail['productsid'];                
        $_deal = Business_Addon_Deal::getInstance();
        $dealDetail = $_deal->getDetailByProduct_id($productsid);
        if (count($dealDetail)>0)
            $discountPrice = $dealDetail['discount'];
        else
            $discountPrice = 0;
        
        switch($productsid) {
            
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

                if ( ($orange >0 && $orange<=1000000) || ($range >0 && $range<=1000000) )
                    return 100000;
                return 150000;
                break;
        }
                
    }
    
    public function getBuyedProduct($product_id, $datetime) {
        if ((int) $product_id == 0 || $datetime == "") return 0;
        $_cart = Business_Ws_ShoppingcartItem::getInstance();        
        
        $total = $_cart->getTotalBuyedProducts($product_id, $datetime);
        return $total;
    }
    
    public function getPercentDiscount($price, $discount_price) {
        if ($discount_price == 0) return array($price, $price);
        if ($price == 0) return 0;
        $percent = (int) ($discount_price * 100 / $price);        
        return array($percent, $price - $discount_price);
    }
    
    public function getRemainTime($detail) {
        if (count($detail)>0 && is_array($detail)) {
            
            $end_time = strtotime($detail['end_date']);
            $cur_time = time();
            
            $result = $end_time - $cur_time;
            
            return $result < 0 ? 0 : $result;
        }
        return 0;
    }
    
    public function getImagePath($productDetail, $size="homes"){
//        pre($productDetail);
        if (isset($productDetail['thumb']) && $size == "thumbnails"){
            $thumb = (array)json_decode(json_decode($productDetail['thumb'])->thumb2);
            if ($productDetail['productsid']!=4) {
                
                
                return Globals::getBaseUrl().'uploads/products/'.$size.'/'.$thumb[0];
            } else {
                
                return Globals::getBaseUrl().'uploads/accesories/'.$size.'/'.$thumb[0];
            }
        }else{
            if ($productDetail['productsid']!=4)
                return Globals::getBaseUrl().'uploads/products/'.$size.'/'.json_decode($productDetail['thumb'])->thumb1;
            else {
                if ($size != "homes") {
                    $thumb = (array)json_decode(json_decode($productDetail['thumb'])->thumb2);
		    return Globals::getBaseUrl().'uploads/accesories/'.$size.'/'.$thumb[0];
                } else {
                    $thumb = json_decode($productDetail['thumb'])->thumb1;  
                    return Globals::getBaseUrl().'uploads/accesories/'.$size.'/'.$thumb;                    
                }
                
            }
        }
    }
    
    public function getWhere($features){
//                    pre($features);
        $_fdetail = Business_Addon_Featuresdata::getInstance();
        $aOperator = $_fdetail->getUniqueOpertor();                        

        //xu ly loai dien thoai
        
        $where[] = 'fd.pid = itemid';
        
        //man hinh cam ung FID=8; co chuoi :cam ung
        if ($features['touch'] == 1){
            $where[] = 'fd.fid = 8 AND fd.value like "%cảm ứng%"';
        }
        
        //3G FID=38; !=''
        if ($features['3G'] == 1){
            $where[] = 'fd.fid = 38 AND fd.value != ""';
        }
            
        //Wifi FID=39; !=''
        if ($features['wifi'] == 1){
            $where[] = 'fd.fid = 39 AND fd.value != ""';
        }
        
        //kieu dang dien thoai dang: 1:thanh; 2:gap; 3:truot
        if ($features['type_allow']==1){
            $where[] = 'fd.fid = 2 AND type = ' . (int)$features['type'];
        }
        
        //ban phim dien thoai dang: qwerty
        if ($features['qwerty']==1){
            $where[] = 'fd.fid= 10 AND LOWER(fd.value) like "%qwerty%"';
        }
        
        //dien thoai 2sim tro len
        if ($features['sim']==1){
            $where[] = 'fd.fid = 2 AND LOWER(title) like "%2 sim%"';
        }
        
        if ($features['camera_allow'] == 1){
            if ($features['camera'] == '5down')
                $where[] = 'fd.fid = 60 AND (fd.value like "%1.%" OR fd.value like "%2.%" OR fd.value like "%3.%" OR fd.value like "%4.%" OR fd.value like "%5.%")';            
            else
                $where[] = 'fd.fid = 60 AND fd.value != ""';
        }
        
        if ($features['operator_allow']==1){
            $where[] = 'fd.fid = 44 AND LOWER(fd.value) like "%'.strtolower($aOperator[$features['operator']]).'%"';
        }
        
        return $where;
        
    }
    
    public function getFeaturesDetail($pid) {
       $_products = Business_Ws_ProductsItem::getInstance();
       $_features = Business_Addon_Features::getInstance();
       $_featuresData = Business_Addon_Featuresdata::getInstance();
       
       $_fList = $_features->getListByPid($pid);
       return $_fList;       
    }
    
    public function getFeaturesDetailView($pid, $show=1) {
       $_products = Business_Ws_ProductsItem::getInstance();
       $_features = Business_Addon_Features::getInstance();
       $_featuresData = Business_Addon_Featuresdata::getInstance();
       
       $_fList = $_features->getListByPid($pid, $show);
       return $_fList;       
    }
    
    /*
     * @param array: newest, super, cheap, highend, smartphone, normal, newcomes, tablet
     */
    public function getProductsByType($typename, $text, $limit=15){
        $_products = Business_Ws_ProductsItem::getInstance();
        $menuname='menu_products';
        Business_Helpers_Common::getMenuDetail($menuname, $delta, $cateid);
        $list = $_products->getListByType($typename, $limit, $delta);
        $menuname='menu_tablet';
        Business_Helpers_Common::getMenuDetail($menuname, $delta, $cateid);
        $list2 = $_products->getListByType($typename, $limit, $delta);
        $list = array_merge($list,$list2);
        
        $_month_between = 2592000*3; //1 months
//<div class="homeLine">
//        <div class="title utm">Mới xuất hiện</div>
//</div>        
        if (count($list)>0){            
            $ret = '<div class="homeLine">
                    <div class="title utm">'.$text.'</div>
                    </div>';               
            $ret .= '<div class="homeBlock">';
            $ret .= '<div class="mtoggle"><a href="#"><b>[Xem toàn bộ]</b></a></div>';
            foreach($list as $product){
                $title = Business_Common_Utils::adaptTitleLinkURLSEO($product['title']);
                $thumb = json_decode($product['thumb']);
                $image = Globals::getBaseUrl()."/uploads/products/homes/" . $thumb->thumb1;
                if ($product['price']>0){
                    if ($product['cateid'] != 40 && $product['cateid'] != 39 && !in_array($product['cateid'], $this->_mixPrice)){
                        $_color = 'orange';
                        $_name = 'HNam';
                    } else {
                        $_name = 'Đen';
                        $_color = '';
                    }
                    $price = number_format($product['price'])."đ";
//                    $price = '<p class="l12 bold orange tLeft ">&nbsp;&nbsp;&nbsp;&nbsp;'.$_name.'&nbsp;&nbsp;&nbsp;: '.$price.'</p>';
                    $price = '<p class="'.$_color.'"><b>'.$_name.': '.$price.'</b></p>';
                }else{
                    $price = '';
                }
                if ($product['original_price']>0){
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

                    $original_price = number_format($product['original_price'])."đ";
//                    $original_price = '<p class="l12 bold dblue tLeft ">&nbsp;&nbsp;&nbsp;&nbsp;'.$_oname.': '.$original_price.'</p>';
                    $original_price = '<p class="'.$_color.'"><b>'.$_oname.': '.$original_price.'</b></p>';
                }else{
                    $original_price = '';
                }
                $bonus_full = trim(preg_replace("/<.*?>/", "", $product['bonus_company_full']));
                
                $bonus = ($bonus_full == '' || $bonus_full == "&nbsp;" ? $product['bonus_company'] : $product['bonus_company_full']);
                
                $bonus = ($bonus == '' ? $product['bonus_hnam'] : $bonus);
                
                if ($bonus == '') $display_bonus = 'hide'; else $display_bonus = '';
                if ((int)$product['discount_online'] == 0) $display_bonus = 'hide'; else $display_bonus="";

                $bonusBlock = '';
                $bonusInfo = '';
                if ($bonus != '' & $bonus != null) {
                    $bonusBlock = '<p class="gift"></p>';
                    if ($product['discount_online'] > 0) {
                        $bonusBlock = '<p class="gift_bonus">Giảm '.Business_Common_Utils::shortPrice($product['discount_online']).'</p>';                        
                    }
                    $bonusInfo = '<div class="giftinfo">'.$bonus.'</div>';
                } else {
                    if ($product['discount_online'] > 0) {
                        $bonusBlock = '<p class="bonus">Giảm '.Business_Common_Utils::shortPrice($product['discount_online']).'</p>';                        
                    }
                }
                $curdate = strtotime(date('Y-m-d'));
                $created = strtotime(date($product['posteddate']));
                if ( ($curdate - $created) <= $_month_between)
                    $status = '<p class="new"></p>';                
                else $status = '';
                //status: con hang, het hang, sap co hang, hang moi
                if ($status == '') {
                    switch($product['onstock']) {
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
                if ($text != 'mayTinhBang' && $text != 'Máy tính bảng'){
                $__link = SEOPlugin::getProductDetailLink($product['itemid'], $title);
                $__title = $product['title'];
                $ret .= '     
                    <div class="round pitem">                            
                            '.$status.'
                            <p class="img"><a href="'.$__link.'"><img src="'.$image.'" title="'.$product['title'].'" /></a></p>
                            <p class="pname"><a href="'.$__link.'">'.$__title.'</a></p>                            
                            '.$original_price.'
                            '.$price.'                            
                            '.$bonusBlock.'                            
                            '.$bonusInfo.'                            
                    </div>';
                }else{
                    //may tinh bang
                    $__link = SEOPlugin::getTabletDetailLink($product['itemid'], $title);
                    $__title = $product['title'];
                    $ret .= '     
                        <div class="round pitem">                            
                                '.$status.'
                                <p class="img"><a href="'.$__link.'"><img src="'.$image.'" title="'.$product['title'].'" /></a></p>
                                <p class="pname"><a href="'.$__link.'">'.$__title.'</a></p>                            
                                '.$original_price.'
                                '.$price.'                            
                                '.$bonusBlock.'                            
                                '.$bonusInfo.'                            
                        </div>';                    
                    }                
            }
            $ret .= '</div>';
            return $ret;
        }
    }
    
    public function updateProducts($product){
            $title = Business_Common_Utils::adaptTitleLinkURLSEO($product['title']);
            $thumb = json_decode($product['thumb']);
            $image = Globals::getBaseUrl()."/uploads/products/homes/" . $thumb->thumb1;
            if ($product['price']>0){
                $price = number_format($product['price'])."đ";
                $price = '<p class="l12 bold orange tLeft ">&nbsp;&nbsp;&nbsp;&nbsp;Hnam&nbsp;&nbsp;&nbsp;: '.$price.'</p>';
            }else{
                $price = '';
            }
            if ($product['original_price']>0){
                $original_price = number_format($product['original_price'])."đ";
                $original_price = '<p class="l12 bold dblue tLeft ">&nbsp;&nbsp;&nbsp;&nbsp;Công ty: '.$original_price.'</p>';
            }else{
                $original_price = '';
            }
            $bonus = ($product['bonus_company'] == '' ? $product['bonus_hnam'] : $product['bonus_company']);
            if ($bonus != '' & $bonus != null)
                $bonus = '<div class="bonus">+ '.$bonus.'</div>'.$newItem;
            else
                $bonus = '';
            $curdate = strtotime(date('Y-m-d'));
            $created = strtotime(date($product['posteddate']));
            if ( ($curdate - $created) <= $_month_between)
//                $newItem = '<div class="new"><img src="'.Globals::getBaseUrl().'hnamv2/images/icon-new.png" alt="san pham moi" /></div>';
                $newItem = '<div class="new"><img src="'.Globals::getBaseUrl().'hnamv2/images/icon-new.png" alt="san pham moi" /></div>';
            else
                $newItem = '';
            
            $ret .= '                        
                <div class="itemp">
                        <div class="pic"><a href="'.SEOPlugin::getProductDetailLink($product['itemid'], $title).'"><img class="product" src="'.$image.'" alt="'.$product['title'].'"/></a></div>
                        <p class="ptitle"><a href="" class="bold black small l12">'.$product['title'].'</a></p>
                        '.$original_price.'
                        '.$price.'
                        '.$bonus.'
                </div>';        
        $ret .= "</div>";
        return $ret;
    }
    
    public function getAccesories($limit=15, $__productsid=null, $__cateid = null){
        
        $_products = Business_Ws_ProductsItem::getInstance();
        $menuname='menu_acc';
        
        Business_Helpers_Common::getMenuDetail($menuname, $delta, $cateid);
        if ($__productsid == null && $__cateid == null)
	  $list = $_products->getListByType("newest", $limit, $delta);
        else {

	  $list = $_products->getListByProIdPaging($__productsid, $__cateid, $offset=0, $limit, $_filter = '', $enable = 1, $_onstock, $key = '', $filter);	  
	}
        $_month_between = 2592000; //1 months
        if (count($list)>0){
            if ($__productsid == null && $__cateid == null){
            $ret = '<div class="homeLine">
                    <div class="title utm">Phụ kiện mới</div>
                    </div>';               
            $ret .= '<div class="homeBlock">';
            $ret .= '<div class="mtoggle"><a href="#"><b>[Xem toàn bộ]</b></a></div>';
            }
            foreach($list as $product){
                $title = Business_Common_Utils::adaptTitleLinkURLSEO($product['title']);
                $thumb = json_decode($product['thumb']);
                $image = Globals::getBaseUrl()."uploads/accesories/homes/" . $thumb->thumb1;
                $price = "<p class='orange bold'>Hnam: " . number_format($product['price'])."đ</p>";
                
                $curdate = strtotime(date('Y-m-d'));
                $created = strtotime(date($product['posteddate']));
                if ( ($curdate - $created) <= $_month_between)
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
					$bonusInfo = '<div class="giftinfo">'.$bonus.'</div>';
				} else {
					$bonusBlock = '';                        
				}
                $ret .= '     
                        <div class="round pitem">                            
                                <p class="img"><a href="'.$__link.'"><img src="'.$image.'" title="'.$product['title'].'" /></a></p>
                                <p class="pname"><a href="'.$__link.'">'.$__title.'</a></p>                            
                                '.$price.'                            
                                '.$newItem.' 
                                '.$bonusInfo.' 								
                                '.$bonusBlock.' 								
                        </div>'; 
            }
            return $ret;
        }
    }

    public function getAccesoriesLite($limit=15, $__productsid=null, $__cateid = null){
        
        $_products = Business_Ws_ProductsItem::getInstance();
        $menuname='menu_acc';
        
        Business_Helpers_Common::getMenuDetail($menuname, $delta, $cateid);
        if ($__productsid == null && $__cateid == null)
	  $list = $_products->getListByType("newest", $limit, $delta);
        else {

	  $list = $_products->getListByProIdPaging($__productsid, $__cateid, $offset=0, $limit, $_filter = '', $enable = 1, $_onstock, $key = '', $filter);	  
	}
        return $list;
    }
    
    /*
     * @get list for mobile app
     * @param array: newest, super, cheap, highend, smartphone, normal, newcomes, tablet
     */
    public function getProductsByTypeLite($typename, $text, $limit=15){
        $_products = Business_Ws_ProductsItem::getInstance();
        $menuname='menu_products';
        Business_Helpers_Common::getMenuDetail($menuname, $delta, $cateid);
        $list = $_products->getListByType($typename, $limit, $delta);
        $menuname='menu_tablet';
        Business_Helpers_Common::getMenuDetail($menuname, $delta, $cateid);
        $list2 = $_products->getListByType($typename, $limit, $delta);
        $list = array_merge($list,$list2);
        return $list;
        
    }
    
    public function isAllowDeliveryFast($pDetail) {
        if ($pDetail["original_price"]>=1500000) {
            //hang chinh hang
            return true;
        }
        return false;
    }
    
    public function getMatchAccesories($pDetail){
        $_products = Business_Ws_ProductsItem::getInstance();
        
        $title = strtolower($pDetail['title']);
        
        $menuname='menu_acc';
        Business_Helpers_Common::getMenuDetail($menuname, $productsid, $cateid);
        $accList = $_products->getListAll($productsid);
	//newest product first
	$accList = array_reverse($accList);

        foreach($accList as $item){
        //    if ($item['cateid'] == 33)
                //$result[] = $item;       
			
            if ($item['search_text'] != ''){
                //if (in_array($item['search_text'], array($item['itemid'],$title))){		
		$text = ",".$item['search_text'].",";
		$text = str_replace(",,",",",$text);
		//$text = $text . implode(",", $arrItems) . ",";
		//hardcode 
		
                $parentid = Business_Helpers_Common::getMenuParentID($pDetail['cateid']);
//var_dump($parentid,$pDetail['cateid']);
//if ($item['search_text']=='3256'){var_dump($text,$pDetail,strpos($text, ",".$pDetail['itemid'].","), $item);die();}
  		if (    strpos($text, ",".$pDetail['itemid'].",") !== FALSE 
                        || strpos($text, ",".$pDetail['cateid'].",") !== FALSE
                        || strpos($text, ",".$parentid.",") !== FALSE){  
                    $result[] = $item;
                }
//elseif($item['search_text'] == 'all'){
                    //$result[] = $item;
                //}
            }
        }
        
        $arrItems = array(3906,3905,3907,3909,3908);
        $arrDefaultCateid = array(396,320,290,377,425,180,187,447);

        foreach($accList as $item){
//	    if (in_array($item['itemid'], $arrItems)){
//		  $result[] = $item;
//	    }
	    if (in_array($item['cateid'], $arrDefaultCateid)){
			if ($item["enabled"]==0) continue;
			$result[] = $item;
	    }
	}
        
	foreach($accList as $item){
			if ($item["enabled"]==0) continue;
            if ($item['cateid'] == 33)
                $result[] = $item;               
if (strpos(strtolower($item['search_text']), "all") !== FALSE) {
$result[] = $item;
}
        }
//var_dump($result);
        return $result;
    }
 }
?>

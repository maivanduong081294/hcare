<?php

/**
 * User Admin Home Controller
 * @author: nghidv
 */
class User_Admin_MakeMoneyController extends Zend_Controller_Action {
    private $_identity;
    private $_username;
    public function init() {
        // do something
        BlockManager::setLayout('appbh');
        $auth = Zend_Auth::getInstance(); 
        $identity = $auth->getIdentity();
        $_identity = (array)$auth->getIdentity();
        $this->view->full_name = $identity->fullname;
        $this->view->user_name = $identity->username;
        if ($identity->username == null) {
            header('Location: /admin');
        }
        if ($identity != null) {
            $username = $identity->username;
            $this->view->username = $username;
        }
        $this->_username = $username;
        $this->_identity = $_identity;
    }
    private $_default_menu = "3";
    private $_default_menu2 = "0";
    private $_plist = array(
                    "0" => "Lựa chọn loại chương trình",
                    "1" => "Chọn mã sản phẩm",
                    "3" => "Điện thoại",
                    "5" => "Máy tính bảng",
                    "4" => "Phụ kiện"
                );
     private function getStoreName($slist, $voteid) {
        if (count($slist)>0){
            foreach($slist as $store) {
                if ($store["userid"]==$voteid) return $store["abbreviation"];
            }
        }
    }
    
    private function getActiveVoteID($slist, $request) {
        $ret = null;
        foreach($slist as $item) {
            $voteid = $item["userid"];
            if($request["voteid_$voteid"]==1) {
                $ret[$voteid] = $voteid;
            }
        }
        
        return $ret;
    }
    public function chart2Action(){
        $this->_redirect('admin/home');
        $_option                = Business_Addon_Options::getInstance();
        $days_created_end = $this->_request->getParam("day_created_end");
        $__endday              = $_option->getDayEndByMonths();
        if ($days_created_end == null) {
			$days_created_end = date("Y/m/d") . " - " . date("Y/m/d 23:59:59");
        }
        $this->view->days_created_end = $days_created_end;
        $date_from              = $_option->getDayC($days_created_end);
        $date_to                = $_option->getDayE($days_created_end);
        $menu_products          = Business_Helpers_Products::getProductMenu2();
        $menu_acc               = Business_Helpers_Products::getAccMenu();
        $menu_tablet            = $_option->getTabletMenu();
        $menu_laptop            = $_option->getLaptopMenu();
        $_users_products        = Business_Addon_UsersProducts::getInstance();
        
        $slist = Business_Common_Users::getInstance()->getListByUname(false);
        $count  = count($slist);
        $_votename              = array();
        foreach ($slist as $items){
            $_votename[$items["abbreviation"]] = '"'.$items["abbreviation"].'"'; 
        }
        $this->view->vote_name = implode(",",$_votename);
        $list                   = $_users_products->getListDetailSale($date_from,$date_to,1);
        
//        echo "<pre>";
//        var_dump($list);
//        exit();
        //dien thoai
        $vid_name = array();
        $ret_phone = array();
        $ret_phone_hnam = array();
        $ret_phone_count = array();
        
        $phoneCate = array();
        foreach($menu_products as $m) {
            $phoneCate[] = $m["itemid"];
        }
        //mtb
        $ret_tablet = array();
        $ret_tablet_hnam = array();
        
        $tabletCate = array();
        foreach($menu_tablet as $m) {
            $tabletCate[] = $m["itemid"];
        }
        // phu kien
        $ret_acc=  array();
        $accCate = array();
        foreach($menu_acc as $m) {
            $accCate[] = $m["itemid"];
        }
        // laptop
        $ret_laptop=  array();
        $laptopCate = array();
        foreach($menu_laptop as $m) {
            $laptopCate[] = $m["itemid"];
        }
        foreach($list as $item) {
            $v_id   = $item["vote_id"];
            if (in_array($item["cated_id"], $phoneCate) && $item["flag"]==1){
                $ret_phone[$v_id] += $item["total"];
                $vid_name[$v_id] = '"'.$v_id.'"';
                $ret_phone_count[$v_id] += $item["sl"];
            }
            if (in_array($item["cated_id"], $phoneCate) && $item["flag"]==2){
                $ret_phone_hnam[$v_id] += $item["total"];
                $vid_name[$v_id] = '"'.$v_id.'"';
                $ret_phone_count_hnam[$v_id] += $item["sl"];
            }
            if (in_array($item["cated_id"], $tabletCate) && $item["flag"]==1){
                $ret_tablet[$v_id] += $item["total"];
                $ret_tablet_count[$v_id] += $item["sl"];
            }
            if (in_array($item["cated_id"], $tabletCate) && $item["flag"]==2){
                $ret_tablet_hnam[$v_id] += $item["total"];
                $ret_tablet_count_hnam[$v_id] += $item["sl"];
            }
            if (in_array($item["cated_id"], $accCate)){
                $ret_acc[$v_id] += $item["total"];
                $ret_acc_count[$v_id] += $item["sl"];
            }
            if (in_array($item["cated_id"], $laptopCate) && $item["flag"]==1){
                $ret_laptop_count[$v_id] += $item["sl"];
                $ret_laptop[$v_id] += $item["total"];
            }
            if (in_array($item["cated_id"], $laptopCate) && $item["flag"]==2){
                $ret_laptop_hnam[$v_id] += $item["total"];
                $ret_laptop_count_hnam[$v_id] += $item["sl"];
            }
        }
        $sum_phone_hnam =0;
        $sum_phone =0;
        $sum_tablet_hnam =0;
        $sum_tablet =0;
        $sum_acc =0;
        $sum_laptop =0;
        $sum_laptop_hnam =0;
        foreach($ret_phone as  $total) {
            $sum_phone +=$total;
            $seri[] = $total/1000;
        }
        foreach($ret_phone_hnam as  $total) {
            $sum_phone_hnam +=$total;
            $seri_hnam[] = $total/1000;
        }
        $this->view->sum_phone_cty = number_format($sum_phone);
        $this->view->sum_phone_hnam = number_format($sum_phone_hnam);
        $this->view->sum_phone = number_format($sum_phone_hnam+$sum_phone);
        $this->view->seri = json_encode($seri);
        $this->view->seri_hnam = json_encode($seri_hnam);
        
        $this->view->sum_phone_cty_lite = number_format($sum_phone/1000);
        $this->view->sum_phone_hnam_lite = number_format($sum_phone_hnam/1000);
        $this->view->sum_phone_lite = number_format(($sum_phone_hnam+$sum_phone)/1000);
        //so luong
        $count_phone=0;
        $count_phone_hnam=0;
        foreach($ret_phone_count as  $total) {
            $count_phone +=$total;
            $seri_phone_count[] = $total;
        }
        foreach($ret_phone_count_hnam as  $total) {
            $count_phone_hnam +=$total;
            $seri_phone_count_hnam[] = $total;
        }
        
        $this->view->count_phone_cty =  number_format($count_phone);
        $this->view->count_phone_hnam =  number_format($count_phone_hnam);
        $this->view->count_phone =  number_format($count_phone+$count_phone_hnam);
        
        $this->view->seri_count =  json_encode($seri_phone_count);
        $this->view->seri_hnam_count = json_encode($seri_phone_count_hnam);
        
//        $this->view->phone_avg = number_format(round($sum_phone/$count,2));
        
        //may tinh ban
        foreach($ret_tablet as  $total) {
            $sum_tablet +=$total;
            $seri_tablet[] = $total/1000;
        }
        foreach($ret_tablet_hnam as  $total) {
            $sum_tablet_hnam +=$total;
            $seri_tablet_hnam[] = $total/1000;
        }
        $this->view->sum_tablet_cty = number_format($sum_tablet);
        $this->view->sum_tablet_hnam = number_format($sum_tablet_hnam);
        $this->view->sum_tablet = number_format($sum_tablet_hnam+$sum_tablet);
        
        $this->view->sum_tablet_cty_lite = number_format($sum_tablet/1000);
        $this->view->sum_tablet_hnam_lite = number_format($sum_tablet_hnam/1000);
        $this->view->sum_tablet_lite = number_format(($sum_tablet_hnam+$sum_tablet)/1000);
        
//        $this->view->tablet_avg = number_format(round($sum_tablet/$count,2));
        $this->view->seri_tablet = json_encode($seri_tablet);
        $this->view->seri_tablet_hnam = json_encode($seri_tablet_hnam);
        // so luong
        $count_tablet=0;
        $count_tablet_hnam=0;
        foreach($ret_tablet_count as  $total) {
            $count_tablet +=$total;
            $seri_tablet_count[] = $total;
        }
        foreach($ret_tablet_count_hnam as  $total) {
            $count_tablet_hnam +=$total;
            $seri_tablet_count_hnam[] = $total;
        }
        $this->view->count_tablet_cty = number_format($count_tablet);
        $this->view->count_tablet_hnam = number_format($count_tablet_hnam);
        $this->view->count_tablet = number_format($count_tablet+$count_tablet_hnam);
        
        $this->view->seri_tablet_count = json_encode($seri_tablet_count);
        $this->view->seri_tablet_hnam_count = json_encode($seri_tablet_count_hnam);
        
        //phu kien
        foreach($ret_acc as  $total) {
            $sum_acc +=$total;
            $seri_acc[] = $total/1000;
        }
        $this->view->sum_acc_cty = number_format($sum_acc);
        $this->view->sum_acc_hnam = 0;
        $this->view->sum_acc = number_format($sum_acc+$this->view->sum_acc_hnam);
        
        $this->view->sum_acc_cty_lite = number_format($sum_acc/1000);
        $this->view->sum_acc_hnam_lite = 0;
        $this->view->sum_acc_lite = number_format(($sum_acc+$this->view->sum_acc_hnam)/1000);
//        $this->view->acc_avg = number_format(round($sum_acc/$count,2));
        $this->view->seri_acc = json_encode($seri_acc);
        
        // so luong 
        $count_acc=0;
        $count_acc_hnam=0;
        foreach($ret_acc_count as  $total) {
            $count_acc +=$total;
            $seri_acc_count[] = $total;
        }
        $this->view->count_acc_cty = number_format($count_acc);
        $this->view->count_acc_hnam = 0;
        $this->view->count_acc = number_format($count_acc+ $this->view->count_acc_hnam);
        $this->view->seri_acc_count = json_encode($seri_acc_count);
        
        //laptop
        foreach($ret_laptop as  $total) {
            $sum_laptop +=$total;
            $seri_laptop[] = $total/1000;
        }
        foreach($ret_laptop_hnam as  $total) {
            $sum_laptop_hnam +=$total;
            $seri_laptop_hnam[] = $total/1000;
        }
        $this->view->sum_laptop_cty = number_format($sum_laptop);
        $this->view->sum_laptop_hnam = number_format($sum_laptop_hnam);
        $this->view->sum_laptop = number_format($sum_laptop_hnam+$sum_laptop);
        
        $this->view->sum_laptop_cty_lite = number_format($sum_laptop/1000);
        $this->view->sum_laptop_hnam_lite = number_format($sum_laptop_hnam/1000);
        $this->view->sum_laptop_lite = number_format(($sum_laptop_hnam+$sum_laptop)/1000);
        
//        $this->view->laptop_avg = number_format(round($sum_laptop/$count,2));
        $this->view->seri_laptop = json_encode($seri_laptop);
        $this->view->seri_laptop_hnam = json_encode($seri_laptop_hnam);
        // số lượng
        $count_laptop=0;
        $count_laptop_hnam=0;
        foreach($ret_laptop_count as  $total) {
            $count_laptop +=$total;
            $seri_laptop_count[] = $total;
        }
        foreach($ret_laptop_count_hnam as  $total) {
            $count_laptop_hnam +=$total;
            $seri_laptop_hnam_count[] = $total;
        }
        $this->view->count_laptop_cty = number_format($count_laptop);
        $this->view->count_laptop_hnam = number_format($count_laptop_hnam);
        $this->view->count_laptop = number_format($count_laptop_hnam+$count_laptop);
        
        
        $this->view->seri_laptop_count = json_encode($seri_laptop_count);
        $this->view->seri_laptop_hnam_count = json_encode($seri_laptop_hnam_count);
        
        
        $this->view->sum = number_format($sum_phone+$sum_phone_hnam+$sum_tablet+$sum_tablet_hnam+$sum_acc+$sum_laptop+$sum_laptop_hnam);
        $this->view->count = number_format($count_phone+$count_phone_hnam+$count_tablet+$count_tablet_hnam+$count_acc+$count_laptop+$count_laptop_hnam);
    }
    public function tongQuanAction(){
		$this->_redirect("/admin/user/report/overview-chart");
        $phone_cty = array();
        $phone_hnam = array();
        $phone_old  = array();
        // máy tính bảng
        $tablet_cty = array();
        $tablet_hnam = array();
        $tablet_old = array();
        // phụ kiện
        $acc_cty = array();
        $acc_hnam = array();
        $acc_old = array();
        //laptop
        $laptop_cty = array();
        $laptop_hnam = array();
        $laptop_old = array();
        //watch
        $watch_cty = array();
        $watch_hnam = array();
        $watch_old = array();
        //so luong
        $cphone_cty = array();
        $cphone_hnam = array();
        $cphone_old  = array();
        // máy tính bảng
        $ctablet_cty = array();
        $ctablet_hnam = array();
        $ctablet_old = array();
        // phụ kiện
        $cacc_cty = array();
        $cacc_hnam = array();
        $cacc_old = array();
        //laptop
        $claptop_cty = array();
        $claptop_hnam = array();
        $claptop_old = array();
        //watch
        $cwatch_cty = array();
        $cwatch_hnam = array();
        $cwatch_old = array();
        //all
        $total = array();
        $count = array();
        
        $_option                = Business_Addon_Options::getInstance();
        $idregency = $this->_identity["idregency"];
        if(!$_option->isBGD($idregency)){
            $this->_helper->viewRenderer('tong-quan2');
        }
        
        $start_end= $this->_request->getParam("start_end");
        if($start_end ==null){
           $start_end = date("F j, Y")." - ".date("F j, Y"); 
        }
        $this->view->start_end = $start_end;
        $start  = $_option->getStartDate($start_end);
        $end  = $_option->getEndDate($start_end);
        $this->view->start = $start;
        $this->view->end = $end;
        
        
        
        
        $_users_products        = Business_Addon_UsersProducts::getInstance();
        
        $slist = Business_Common_Users::getInstance()->getListByUname(false);
        $_votename              = array();
        foreach ($slist as $items){
            $vid = $items["userid"];
            $_votename[$items["abbreviation"]] = '"'.$items["abbreviation"].'"';
            $phone_cty[$vid] =0;
            $cphone_cty[$vid] =0;
            $phone_hnam[$vid]=0;
            $cphone_hnam[$vid]=0;
            $phone_old[$vid] = 0;
            $cphone_old[$vid] = 0;
            
            $tablet_cty[$vid] = 0;
            $ctablet_cty[$vid] = 0;
            $tablet_hnam[$vid] =0;
            $ctablet_hnam[$vid] = 0;
            $tablet_old[$vid] = 0;
            $ctablet_old[$vid] = 0;
            
            $acc_cty[$vid] = 0;
            $cacc_cty[$vid] = 0;
            $acc_hnam[$vid] = 0;
            $cacc_hnam[$vid] = 0;
            
            $laptop_cty[$vid] = 0;
            $claptop_cty[$vid] = 0;
            $laptop_hnam[$vid] = 0;
            $claptop_hnam[$vid] = 0;
            
            $watch_cty[$vid] = 0;
            $cwatch_cty[$vid] = 0;
            $watch_hnam[$vid] = 0;
            $cwatch_hnam[$vid] = 0;
            
        }
        $this->view->vote_name = implode(",",$_votename);
        $list                   = $_users_products->getListDetailSale2($start,$end,1);
        $_khomaycu              = $_users_products->getTotalByCateId(53, $start, $end, 1);
        $kmcu = array();
        $ckmcu = array();
        foreach ($_khomaycu as $_item){
            $fl = $_item["flag"];
            $fl = $_item["flag"];
           $kmcu[$fl] = $_item["total"]/1000; 
           $ckmcu[$fl] = $_item["sl"]; 
        }
        $this->view->kmcu =$kmcu;
        $this->view->ckmcu =$ckmcu;
//        echo '<pre>';var_dump($list);
        foreach($list as $item) {
            $v_id           = $item["vote_id"];
            $flag           = $item["flag"];
            $productsid     = $item["productsid"];
            $type           = $item["type"];
            $is_apple       = $item["is_apple"];
            //phone
            if($flag ==1 && $productsid ==3 && $type == 3){
                $phone_cty[$v_id] += $item["total"];
                $cphone_cty[$v_id] += $item["sl"];
            }
            if($flag ==2 && $productsid ==3 && $type == 3){
                $phone_hnam[$v_id] += $item["total"];
                $cphone_hnam[$v_id] += $item["sl"];
            }
            if($productsid ==3 && $type == 4){
                $phone_old[$v_id] += $item["total"];
                $cphone_old[$v_id] += $item["sl"];
            }
            //tablet
            if($flag ==1 && $productsid ==5 && $type == 3){
                $tablet_cty[$v_id] += $item["total"];
                $ctablet_cty[$v_id] += $item["sl"];
            }
            if($flag ==2 && $productsid ==5 && $type == 3){
                $tablet_hnam[$v_id] += $item["total"];
                $ctablet_hnam[$v_id] += $item["sl"];
            }
            if($productsid ==5 && $type == 4){
                $tablet_old[$v_id] += $item["total"];
                $ctablet_old[$v_id] += $item["sl"];
            }
            // phụ kiện
            if($flag ==1 && $productsid ==4 && $type == 3){
                $acc_cty[$v_id] += $item["total"];
                $cacc_cty[$v_id] += $item["sl"];
            }
            if($flag ==2 && $productsid ==4 && $type == 3){
                $acc_hnam[$v_id] += $item["total"];
                $cacc_hnam[$v_id] += $item["sl"];
            }
            // Laptop
            if($flag ==1 && $productsid ==6 && $type == 3){
                $laptop_cty[$v_id] += $item["total"];
                $claptop_cty[$v_id] += $item["sl"];
            }
            if($flag ==2 && $productsid ==6 && $type == 3){
                $laptop_hnam[$v_id] += $item["total"];
                $claptop_hnam[$v_id] += $item["sl"];
            }
            // Đồng hồ thông minh
            if($flag ==1 && $productsid ==8 && $type == 3){
                $watch_cty[$v_id] += $item["total"];
                $cwatch_cty[$v_id] += $item["sl"];
            }
            if($flag ==2 && $productsid ==8 && $type == 3){
                $watch_hnam[$v_id] += $item["total"];
                $cwatch_hnam[$v_id] += $item["sl"];
            }
            // all
            $total[$flag][$productsid][$type][$is_apple] += $item["total"]/1000;
            $count[$flag][$productsid][$type][$is_apple] += $item["sl"];
        }
        //phone
        foreach ($phone_old as $items){
            $p_old[] = $items/1000;
        }
        $this->view->phone_old  = json_encode($p_old);
        foreach ($phone_cty as $items){
            $p_cty[] = $items/1000;
        }
        $this->view->phone_cty  = json_encode($p_cty);
        foreach ($phone_hnam as $items){
            $p_hnam[] = $items/1000;
        }
        $this->view->phone_hnam  = json_encode($p_hnam);
        //tablet
        foreach ($tablet_old as $items){
            $tab_old[] = $items/1000;
        }
        $this->view->tablet_old  = json_encode($tab_old);
        
        foreach ($tablet_cty as $items){
            $tab_cty[] = $items/1000;
        }
        $this->view->tablet_cty  = json_encode($tab_cty);
        
        foreach ($tablet_hnam as $items){
            $tab_hnam[] = $items/1000;
        }
        $this->view->tablet_hnam  = json_encode($tab_hnam);
        // phụ kiện
        foreach ($acc_old as $items){
            $ac_old[] = $items/1000;
        }
        $this->view->acc_old  = json_encode($ac_old);
        
        foreach ($acc_cty as $items){
            $ac_cty[] = $items/1000;
        }
        $this->view->acc_cty  = json_encode($ac_cty);
        
        foreach ($acc_hnam as $items){
            $ac_hnam[] = $items/1000;
        }
        $this->view->acc_hnam  = json_encode($ac_hnam);
        //laptop
        foreach ($laptop_old as $items){
            $lt_old[] = $items/1000;
        }
        $this->view->laptop_old  = json_encode($lt_old);
        
        foreach ($laptop_cty as $items){
            $lt_cty[] = $items/1000;
        }
        $this->view->laptop_cty  = json_encode($lt_cty);
        
        foreach ($laptop_hnam as $items){
            $lt_hnam[] = $items/1000;
        }
        $this->view->laptop_hnam  = json_encode($lt_hnam);
        //đồng hồ thông minh
        foreach ($watch_old as $items){
            $wt_old[] = $items/1000;
        }
        $this->view->watch_old  = json_encode($wt_old);
        
         foreach ($watch_cty as $items){
            $wt_cty[] = $items/1000;
        }
        $this->view->watch_cty  = json_encode($wt_cty);
        
         foreach ($watch_hnam as $items){
            $wt_hnam[] = $items/1000;
        }
        $this->view->watch_hnam  = json_encode($wt_hnam);
        
        
        // số lượng
        //phone
        foreach ($cphone_old as $items){
            $cp_old[] = $items;
        }
        $this->view->cphone_old  = json_encode($cp_old);
        
        foreach ($cphone_cty as $items){
            $cp_cty[] = $items;
        }
        $this->view->cphone_cty  = json_encode($cp_cty);
        foreach ($cphone_hnam as $items){
            $cp_hnam[] = $items;
        }
        $this->view->cphone_hnam  = json_encode($cp_hnam);
        //tablet
        foreach ($ctablet_old as $items){
            $ctab_old[] = $items;
        }
        $this->view->ctablet_old  = json_encode($ctab_old);
        
        foreach ($ctablet_cty as $items){
            $tab_cty[] = $items;
        }
        $this->view->ctablet_cty  = json_encode($ctab_cty);
        
        foreach ($ctablet_hnam as $items){
            $ctab_hnam[] = $items;
        }
        $this->view->ctablet_hnam  = json_encode($ctab_hnam);
        // phụ kiện
        foreach ($cacc_old as $items){
            $cac_old[] = $items;
        }
        $this->view->cacc_old  = json_encode($cac_old);
        
        foreach ($cacc_cty as $items){
            $cac_cty[] = $items;
        }
        $this->view->cacc_cty  = json_encode($cac_cty);
        
        foreach ($cacc_hnam as $items){
            $cac_hnam[] = $items;
        }
        $this->view->cacc_hnam  = json_encode($cac_hnam);
        //laptop
        foreach ($claptop_old as $items){
            $clt_old[] = $items;
        }
        $this->view->claptop_old  = json_encode($clt_old);
        
        foreach ($claptop_cty as $items){
            $clt_cty[] = $items;
        }
        $this->view->laptop_cty  = json_encode($clt_cty);
        
        foreach ($claptop_hnam as $items){
            $clt_hnam[] = $items;
        }
        $this->view->claptop_hnam  = json_encode($clt_hnam);
        //đồng hồ thông minh
//        $cwt_old = array(); 
//        foreach ($cwatch_old as $items){
//            $cwt_old[] = $items;
//        }
//        $this->view->cwatch_old  = json_encode($cwt_old);
        $cwt_cty = array();
        $cwt_hnam = array();
         foreach ($cwatch_cty as $items){
            $cwt_cty[] = $items;
        }
        $this->view->cwatch_cty  = json_encode($cwt_cty);
        
         foreach ($cwatch_hnam as $items){
            $cwt_hnam[] = $items;
        }
        $this->view->cwatch_hnam  = json_encode($cwt_hnam);
        // tổng
        $this->view->sum_all    = $total;
        $this->view->count_all  = $count;
        
    }

    

    public function chartAction() {
        die('no access');
//        $this->_helper->Layout()->disableLayout();
//        $date = $this->_request->getParam("date",date("Y-m-d"));
//        $this->view->date = $date;
//        $this->view->rdate = implode("/",  array_reverse(explode("-", $date)));
        $request = $_REQUEST;
//        $date_from              = $this->_request->getParam("date_from",date("Y-m-d"));
//        $date_to                = $this->_request->getParam("date_to",date("Y-m-d"));
        $_option                = Business_Addon_Options::getInstance();
        $days_created_end = $this->_request->getParam("day_created_end");
        $__endday              = $_option->getDayEndByMonths();
        if ($days_created_end == null) {
            $days_created_end   = date('Y/m/01') . ' - ' .$__endday;
        }
        $this->view->days_created_end = $days_created_end;
        $date_from              = $_option->getDayC($days_created_end);
        $date_to                = $_option->getDayE($days_created_end);
        
        $menu_products          = Business_Helpers_Products::getProductMenu2();
        $menu_acc               = Business_Helpers_Products::getAccMenu();
        $menu_tablet            = $_option->getTabletMenu();
        $menu_laptop            = $_option->getLaptopMenu();
        $_users_products        = Business_Addon_UsersProducts::getInstance();
        $list                   = $_users_products->getSaleByMonth($date_from,$date_to,1);
        //lay id cate dien thoai
        $phoneCate = array();
        foreach($menu_products as $m) {
            $phoneCate[] = $m["itemid"];
        }
        //phu kien
        $accCate = array();
        foreach($menu_acc as $acc) {
            $accCate[] = $acc["itemid"];
        }
        //may tinh bang
        $tabletCate = array();
        foreach($menu_tablet as $tablet) {
            $tabletCate[] = $tablet["itemid"];
        }
        // laptop
        $laptopCate = array();
        foreach($menu_laptop as $laptop) {
            $laptopCate[] = $laptop["itemid"];
        }
        //loc tong tien theo ngay theo dien thoai
        $_date_phone = array();
        $ret_phone = array();
        $ret_phone_count = array();
        //hnam
        $ret_phone2 = array();
        $ret_phone_count2 = array();
        
        // may tinh bang
        $_date_tablet = array();
        $ret_tablet = array();
        $ret_tablet_count = array();
        //hnam
        $ret_tablet2 = array();
        $ret_tablet_count2 = array();
        
        // phu kien
        $_date_acc = array();
        $ret_acc = array();
        $ret_acc_count = array();
        
        // laptop
        $_date_laptop = array();
        $ret_latop = array();
        $ret_latop_count = array();
        // laptop hnam
        $_date_laptop = array();
        $ret_latop2 = array();
        $ret_latop_count2 = array();
        
        
        
        $countday=0;
        foreach($list as $item) {
            $d= date('d/m',  strtotime($item["date"]));
            //dien thoai
            if (in_array($item["cated_id"], $phoneCate) && $item["flag"]==1){
                $ret_phone[$item["date"]] += $item["total"];
                $ret_phone_count[$item["date"]] += $item["sl"];
                $_date_phone[$item["date"]] = '"'.$d.'"'; 
            }
            if (in_array($item["cated_id"], $phoneCate) && $item["flag"]==2){
                $ret_phone2[$item["date"]] += $item["total"];
                $ret_phone_count2[$item["date"]] += $item["sl"];
                $_date_phone[$item["date"]] = '"'.$d.'"'; 
            }
            //may tinh bang cty
            if (in_array($item["cated_id"], $tabletCate)  && $item["flag"]==1){
                $ret_tablet[$item["date"]] += $item["total"];
                $ret_tablet_count[$item["date"]] += $item["sl"];
                $_date_tablet[$item["date"]] = '"'.$d.'"'; 
            }
            //may tinh bang hnam
            if (in_array($item["cated_id"], $tabletCate) && $item["flag"]==2){
                $ret_tablet2[$item["date"]] += $item["total"];
                $ret_tablet_count2[$item["date"]] += $item["sl"];
                $_date_tablet[$item["date"]] = '"'.$d.'"'; 
            }
            //phu kien
            if (in_array($item["cated_id"], $accCate)){
                $ret_acc[$item["date"]] += $item["total"];
                $ret_acc_count[$item["date"]] += $item["sl"];
                $_date_acc[$item["date"]] = '"'.$d.'"'; 
            }
            
            //laptop
            if (in_array($item["cated_id"], $laptopCate)&& $item["flag"]==1){
                $ret_latop[$item["date"]] += $item["total"];
                $ret_latop_count[$item["date"]] += $item["sl"];
                $_date_laptop[$item["date"]] = '"'.$d.'"'; 
            }
            //laptop hnam
            if (in_array($item["cated_id"], $laptopCate)&& $item["flag"]==2){
                $ret_latop2[$item["date"]] += $item["total"];
                $ret_latop_count2[$item["date"]] += $item["sl"];
                $_date_laptop[$item["date"]] = '"'.$d.'"'; 
            }
            
        }
        // điện thoại
        $sum_phone_cty  =0;
        $sum_phone_hnam      =0;
        $this->view->dates = implode(",",$_date_phone);
        $countday = count($_date_phone);
        $obj["name"] = "Cty";
        foreach($ret_phone as  $total) {
            $sum_phone_cty +=$total;
            $seri[] = $total;
        }
        $obj["data"] = $seri;
        
        $obj2["name"] = "Hnam";
        foreach($ret_phone2 as  $total) {
            $sum_phone_hnam += $total;
            $seri2[] = $total;
        }
        $obj2["data"] = $seri2;
        
        $datas[] = $obj;
        $datas[] = $obj2;
        $this->view->seri = json_encode($datas);
        
        $this->view->sum_phone_cty = number_format(round($sum_phone_cty/$countday));
        $this->view->sum_phone_hnam = number_format(round($sum_phone_hnam/$countday));
        $sum_phone = $sum_phone_cty+$sum_phone_hnam;
        $this->view->sum_phone = number_format(round($sum_phone/$countday));
        //số lượng
        $this->view->dates_sl = implode(",",$_date_phone);
        $obj_count["name"] = "Cty";
        foreach($ret_phone_count as  $total) {            
            $seri_count[] = $total;
        }
        $obj_count["data"] = $seri_count;
        $datas_count[] = $obj_count;
        //hnam
        $obj_count2["name"] = "Hnam";
        foreach($ret_phone_count2 as  $total) {            
            $seri_count2[] = $total;
        }
        $obj_count2["data"] = $seri_count2;
        $datas_count[] = $obj_count2;
        $this->view->seri_sl = json_encode($datas_count);
//        echo "<pre>";
//        var_dump($this->view->seri_sl);
//        exit();
        $countday_tablet =0;
        // may tinh bang
        $this->view->date_tablet = implode(",",$_date_tablet);
        $countday_tablet = count($_date_tablet);
        
        $sum_tablet_hnam =0;
        $sum_tablet_cty =0;
        //cty
        $obj_tablet["name"] = "Cty";
        foreach($ret_tablet as  $total) {
            $sum_tablet_cty += $total;
            $seri_tablet[] = $total;
        }
        $obj_tablet["data"] = $seri_tablet;
        $data_tablet[] = $obj_tablet;
        //hnam
        $obj_tablet2["name"] = "Hnam";
        foreach($ret_tablet2 as  $total) {
            $sum_tablet_hnam +=$total;
            $seri_tablet2[] = $total;
        }
        $obj_tablet2["data"] = $seri_tablet2;
        $data_tablet[] = $obj_tablet2;
        $this->view->seri_tablet = json_encode($data_tablet);
        // so luong mtb
        $this->view->date_tablet_sl = implode(",",$_date_tablet);
        //cty
        $obj_tablet_sl["name"] = "cty";
        foreach($ret_tablet_count as  $total) {            
            $seri_tablet_sl[] = $total;
        }
        $obj_tablet_sl["data"] = $seri_tablet_sl;
        $data_tablet_count[] = $obj_tablet_sl;
        //hnam
        $obj_tablet_sl2["name"] = "hnam";
        foreach($ret_tablet_count2 as  $total) {            
            $seri_tablet_sl2[] = $total;
        }
        $obj_tablet_sl2["data"] = $seri_tablet_sl2;
        $data_tablet_count[] = $obj_tablet_sl2;
        $this->view->seri_tablet_sl = json_encode($data_tablet_count);
        $this->view->sum_tablet_cty = number_format(round($sum_tablet_cty/$countday_tablet));
        $this->view->sum_tablet_hnam = number_format(round($sum_tablet_hnam/$countday_tablet));
        $sum_tablet = $sum_tablet_cty+$sum_tablet_hnam;
        $this->view->sum_tablet = number_format(round($sum_tablet/$countday_tablet));
        // phu kien
        $sum_acc=0;
        $sum_acc_cty=0;
        $sum_acc_hnam=0;
        $this->view->date_acc = implode(",",$_date_acc);
        $countday_acc = count($_date_acc);
        $obj_acc["name"] = "PK";
        foreach($ret_acc as  $total) {
            $sum_acc_cty +=$total;
            $seri_acc[] = $total;
        }
        $obj_acc["data"] = $seri_acc;
        $data_acc[] = $obj_acc;
        $this->view->seri_acc = json_encode($data_acc);
        //so luong
        $this->view->date_acc_sl = implode(",",$_date_acc);
        $obj_acc_sl["name"] = "PK";
        foreach($ret_acc_count as  $total) {            
            $seri_acc_sl[] = $total;
        }
        $obj_acc_sl["data"] = $seri_acc_sl;
        $data_acc_sl[] = $obj_acc_sl;
        $this->view->seri_acc_sl = json_encode($data_acc_sl);
        $this->view->sum_acc_cty = number_format(round($sum_acc_cty/$countday_acc));
        $this->view->sum_acc_hnam = number_format(round($sum_acc_hnam/$countday_acc));
        $sum_acc = $sum_acc_cty+$sum_acc_hnam;
        $this->view->sum_acc = number_format(round($sum_acc/$countday_acc));
        // laptop
        $sum_laptop =0;
        $sum_laptop_cty =0;
        $sum_laptop_hnam =0;
        $countday_laptop = count($_date_laptop);
        $this->view->date_laptop = implode(",",$_date_laptop);
        $obj_laptop["name"] = "Cty";
        foreach($ret_latop as  $total) {
            $sum_laptop += $total;
            $seri_laptop[] = $total;
        }
        $obj_laptop["data"] = $seri_laptop;
        $data_laptop[] = $obj_laptop;
        // laptop hnam
        $obj_laptop2["name"] = "Hnam";
        foreach($ret_latop2 as  $total) {
            $sum_laptop_hnam +=$total;
            $seri_laptop2[] = $total;
        }
        $obj_laptop2["data"] = $seri_laptop2;
        $data_laptop[] = $obj_laptop2;
        $this->view->seri_laptop = json_encode($data_laptop);
        
        $this->view->sum_laptop_cty = number_format(round($sum_laptop_cty/$countday_laptop));
        $this->view->sum_laptop_hnam = number_format(round($sum_laptop_hnam/$countday_laptop));
        $sum_laptop = $sum_laptop_cty+$sum_laptop_hnam;
        $this->view->sum_laptop = number_format(round($sum_laptop/$countday_laptop));
        //so luong
        $this->view->date_laptop_sl = implode(",",$_date_laptop);
        $obj_laptop_sl["name"] = "Cty";
        foreach($ret_latop_count as  $total) {            
            $seri_laptop_sl[] = $total;
        }
        $obj_laptop_sl["data"] = $seri_laptop_sl;
        $data_laptop_sl[] = $obj_laptop_sl;
        //hnam
        $obj_laptop_sl2["name"] = "Hnam";
        foreach($ret_latop_count2 as  $total) {            
            $seri_laptop_sl2[] = $total;
        }
        $obj_laptop_sl2["data"] = $seri_laptop_sl2;
        $data_laptop_sl[] = $obj_laptop_sl2;
        $this->view->seri_laptop_sl = json_encode($data_laptop_sl);
        
    }
    
    
    
    
    public function doanhSoBan2Action(){
        $_option                    = Business_Addon_Options::getInstance();
        $idregency = $this->_identity["idregency"];
        if($_option->isBGD($idregency)){
            $this->_redirect('/admin/user/report/sales');
        }else{
            $this->_redirect('/admin/user/report/sales-orther');
        }
        if ($_REQUEST["d"]==1){die(".");}
        $u_product                  = Business_Addon_UsersProducts::getInstance();
        $_zwf_user                  = Business_Common_Users::getInstance();
        
        $_accept_by_cated           = Business_Common_AcceptByCated::getInstance();
        $_products                  = Business_Helpers_Products::getInstance();
        $productsid                 = $this->_request->getParam("cated_hnam");
        if($productsid == NULL){
            $productsid =3;
        }
        $this->view->cated_hnam     = $productsid;
        $cated_dt                   = $this->_request->getParam("cated_dt");
        $cated_mtb                  = $this->_request->getParam("cated_mtb");
        $cated_pk                   = $this->_request->getParam("cated_pk");
        $cated_lt                   = $this->_request->getParam("cated_lt");
        $cated_id                   = $this->_request->getParam("cated_id");
        $flag                       = $this->_request->getParam("flag");
        $menu_product               = $_products->getProductMenu();
        $_tmp = array();
        $_tmp[0]["itemid"] = 999997;
        $_tmp[0]["title"] = "Apple";
        $menu_product = array_merge($_tmp, $menu_product);
        $_menu                      = $menu_product;
        if($productsid == 5){
//            $this->_helper->viewRenderer('doanh-so-ban-mtb');
            $menu_tablet            = $_option->getTabletMenu();
            $_tmp = array();
            $_tmp[0]["itemid"] = 999998;
            $_tmp[0]["title"] = "Apple";
            $menu_tablet = array_merge($_tmp, $menu_tablet);
            $_menu                  = $menu_tablet;
        }
        if($productsid == 4){
//            $this->_helper->viewRenderer('doanh-so-ban-pk');
            $menu_acc               = $_products->getAccMenu2();
//            echo '<pre>';var_dump($menu_acc);
            $_menu                  = $menu_acc;
        }
        
        if($productsid == 6){
            $menu_laptop            = $_option->getLaptopMenu();
            $_menu                  = $menu_laptop;
        }
//        var_dump($_menu);exit();
        $this->view->itemid         = $cated_id;
        $list_vote                  = $_zwf_user->getListByUname(false);
        $days_created_end           = $this->_request->getParam("day_created_end");
        
        
        if($_option->isBGD($idregency) ==true){
            $this->_helper->viewRenderer('admin-doanh-so-ban');
        }else{
            $userid = $this->_identity["userid"];
            $detail_cateid = $_accept_by_cated->getDetailByUserIdProductsId($userid,$productsid);
            if($productsid == 4){
                $flag = "";
            }else{
                $flag = 1;
            }
            if($idregency !=26){
                if(empty($detail_cateid)){
                    $this->_redirect('/admin/home');
                }
                if($cated_id ==null || $cated_id ==0){
                    $cated_id = $detail_cateid["cated_id"];
                    $_menu = Business_Ws_MenuItem::getInstance()->getListByItemid($cated_id);
                }
            }else{
                if($cated_id ==null || $cated_id ==0){
                    $_menu = $_products->getAccMenu2();
                    foreach ($_menu as $items){
                        $cid[] = $items["itemid"];
                    }
                    $cated_id = implode($cid, ",");
                }
                $flag = "";
            }
            
        }
        $this->view->flag           = $flag;
        $__endday                   = $_option->getDayEndByMonths();
        if ($days_created_end == null) {
            $days_created_end       = date('Y/m/01') . ' - ' .$__endday;
        }
        $this->view->days_created_end = $days_created_end;
        $created_day                = $_option->getDayC($days_created_end);
        $created_date               = date('d/m/Y',strtotime($created_day));
        $end_day                    = $_option->getDayE($days_created_end);
        $end_date                   = date('d/m/Y',strtotime($end_day));
        
        
        $this->view->menu_products  = $_menu;
        $listAll                    = $u_product->getListByOption($created_day, $end_day,1,$flag,0,$cated_id);
        $ret = array();
        $count = array();
        foreach($listAll as $item) {
            $vote_id                    = $item["vote_id"];
            $_cateid                   = $item["cated_id"];
            $ret[$vote_id][$_cateid]   = $item["sum"]-$item["reduction_money"]-$item["money_voucher"];
            $count[$vote_id][$_cateid] = $item["countp"];
        }
//        echo "<pre>";
//        var_dump($cated_id,$_menu,$listAll);
//        exit();
        $this->view->list_vote          = $list_vote;
        
        $this->view->sums               = $ret;
        $this->view->count              = $count;
        $this->view->created_date       = $created_date;
        $this->view->end_date           = $end_date;
        
        $list = $u_product->getListByVote2($productsid,$created_day, $end_day,$cated_id,$flag);
        
        $ret2 = array();
        $count2 = array();
        foreach($list as $item) {
            $vote_id  = $item["vote_id"];
            $ret2[$vote_id] = $item["sum"];
            $count2[$vote_id] = $item["countp"];
        }
        $this->view->sums2 = $ret2;
        $this->view->count2 = $count2;
        
        
        $list_hnammobile        = $_option->getCateHnamNew();
//        "3" => "Điện thoại", 
//            "4" => "Phụ kiện", 
//            "5" => "Máy tính bảng", 
//            "6" => "Laptop",
        if($idregency ==33){// nhân viên kinh doanh
            $list_hnammobile    = array("3" => "Điện thoại","5" => "Máy tính bảng");
        }
        if($idregency ==26){// nhân viên phụ kiện
            $list_hnammobile    = array("4" => "Phụ kiện");
        }
        
        $this->view->list_hnammobile = $list_hnammobile;
        $list_catedhnammobile        = $_option->getCatedHnammobile();
        $this->view->list_flag  = $list_catedhnammobile;
    }
    
    
    
    
    public function listAction(){
        $list = Business_Common_Users::getInstance()->getListByUname();
        $this->view->items = $list;
        // danh sach cac cua hang
    }
    public function exportXlsAction()
    {
        $this->_helper->Layout()->disableLayout();
        $_users_products = Business_Addon_UsersProducts::getInstance();
        $_option = Business_Addon_Options::getInstance();
        $cated_id        = $this->_request->getParam("cated_id");
        $cated_name        = $this->_request->getParam("cated_name");
        $this->view->cated_name = $cated_name;
        $days_created_end = $this->_request->getParam("day_created_end");
        $__endday              = $_option->getDayEndByMonths();
        if ($days_created_end == null) {
            $days_created_end   = date('Y/m/01') . ' - ' .$__endday;
        }
        $this->view->days_created_end = $days_created_end;
        
        $created_date = substr($days_created_end, 0, 10);
        $created_day = str_replace("/", "-", $created_date) . ' 00:00:00';
        
        $end_date = substr($days_created_end, 13, 10);
        $end_day = str_replace("/", "-", $end_date) . ' 23:59:59';
        $list = $_users_products->getListByCateId(1,$created_day, $end_day, $cated_id);
        foreach ($list as &$items){
            $vote_id    = $items["vote_id"];
            $items["vote_name"] = $_option->getStoreName($vote_id);
            $items["create_date"] = date('H:i:s d/m/Y',  strtotime($items["create_date"]));
        }
        $this->view->data = $list;
       
    }
    public function exportCateAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $zwfuser = Business_Common_Users::getInstance();
        $lstorename = $zwfuser->getListByUname(FALSE);
        $storename = array();
        foreach ($lstorename as $items){
            $storename[$items["userid"]] = $items["storename"];
        }
        
        $idregency = $this->_identity["idregency"];
        $_users_products = Business_Addon_UsersProducts::getInstance();
        $_option = Business_Addon_Options::getInstance();
        
        $_addon_promotion = Business_Addon_AddonPromotion::getInstance();
        $cated_id        = $this->_request->getParam("cated_id");
        $cated_name        = $this->_request->getParam("cated_name");
        if($_option->isBGD($idregency) == true){
            $flag = $this->_request->getParam("flag");
        }else{
            if($idregency ==26){
                $flag = "";
            }else{
                //$flag = 1; 
				$flag = ""; //hardcode: fix lỗi không export được phụ kiện bằng tài khoản hnam_tuanpha
            }
        }
        $days_created_end = $this->_request->getParam("day_created_end");
        $__endday              = $_option->getDayEndByMonths();
        if ($days_created_end == null) {
            $days_created_end   = date('Y/m/01') . ' - ' .$__endday;
        }
        $this->view->days_created_end = $days_created_end;
        
        $created_day = $_option->getDayC($days_created_end);
        
        $mb_name = array();
        $list_mb = Business_Common_Users::getInstance()->getMb();
        foreach ($list_mb as $_items){
            $mb_name[$_items["userid"]] = $_items["fullname"];
        }
        $end_day = $_option->getDayE($days_created_end);
        
        $list = $_users_products->getListByCateId(1,$created_day, $end_day, $cated_id,"","",$flag);
        $_colorid = array();
//        $list_color = Business_Ws_NewsItem::getInstance()->getListByCateId(722);
        $list_color = Business_Ws_ProductsItem::getInstance()->getListByCateId2(722);
        foreach ($list_color as $items){
            $_colorid[$items["title"]] = $items["itemid"];
        }
        if($list !=null){
                foreach ($list as &$items){
                    $id_addon_user[]    = $items["id_addon_user"];
                    $items["colorid"] = $_colorid[$items["product_color"]];
                    $_products_id[] = $items["products_id"];
                 }
                 $__pids = array_unique($_products_id);
                 $__pid = implode($__pids, ",");
                 $_scolor = Business_Addon_AddonProductTitleKT::getInstance()->getListById($__pid);
                 $name_kt = array();
                 foreach ($_scolor as $items){
                     $name_kt[$items["itemid"]][$items["colorid"]] = $items["name"];
                 }
                 foreach ($list as &$items){
                     $items["name_kt"] = $name_kt[$items["products_id"]][$items["colorid"]];
                 }
        }
        
        
                 $day = "_".date('Ymd');
                 header("Content-Disposition: attachment; filename='$cated_name$day.csv'");
                 header("Content-Type: text/csv; charset=utf-8");
                 header('Content-Type: charset=utf-8');
                 $finalData= array();
                 if($_option->isBGD($idregency) ==true){
                     $strItem ="Tên sản phẩm\tMàu\tTên kế toán\tIMEI\tGiá gốc\tGiá hoàn tiền KM\tGiá bán\tNgày bán\tChi Nhánh\tNhân viên\tTên khách hàng\tSố điện thoại"."\r\n";
                 } else {
                     $strItem ="Tên sản phẩm\tMàu\tTên kế toán\tIMEI\tGiá gốc\tGiá hoàn tiền KM\tGiá bán\tNgày bán\tChi Nhánh\tNhân viên"."\r\n";            
                 }
                 $_arr_id = implode($id_addon_user,",");
                 $price_km = array();
                 $list_km = $_addon_promotion->getListByType($_arr_id,0,0);
                 if($list_km != null){
                         foreach ($list_km as $_item){
                             $price_km[$_item["id_addon_user"]] = $_item["total"];
                         }
                     }
                     
                if($list !=null){     
                    foreach ($list as &$items){
                        $_mbname = $mb_name[$items["id_users"]];
                        $_price_km = 0;
                        $vote_id    = $items["vote_id"];
                        $id_addon_user    = $items["id_addon_user"];
                        $list_km = $_addon_promotion->getListByType($id_addon_user,0,0);
                        if($price_km != null){
                            $p = number_format($price_km[$id_addon_user]);
                            $_price_km = $p;

                        }
                        $products_price = $items["products_price"] - $items["reduction_money"]-$items["money_voucher"];
                        $_price = number_format($products_price, 0, ',', '.');
                        $_price_cost = number_format($items["products_price_cost"], 0, ',', '.');
                        $vote_name = $storename[$vote_id];
                        $items["create_date"] = date('d/m/Y',  strtotime($items["create_date"]));
                        if ($idregency ==39 || $idregency ==40 || $idregency ==41) {
                            $finalData = array(
                                $items["products_name"], // For chars with accents.
                                $items["product_color"], // For chars with accents.
                                $items["name_kt"],
                                $items["imes"],
                                $_price_cost,
                                $_price_km,
                                $_price,
                                $items["create_date"],
                                $vote_name,
                                $_mbname,
                                $items["fullname_addon"],
                                "=0&".$items["phone_addon"],                        
                            );
                        } else {
                            $finalData = array(
                                $items["products_name"], // For chars with accents.
                                $items["product_color"], // For chars with accents.
                                $items["name_kt"],
                                $items["imes"],
                                $_price_cost,
                                $_price_km,
                                $_price,
                                $items["create_date"],
                                $vote_name,
                                $_mbname
                            );
                        }
                        $strItem .= implode("\t", $finalData)."\r\n";
                 }
            }
                 ob_start();
                 echo chr(255) . chr(254) . mb_convert_encoding($strItem, 'UTF-16LE', 'UTF-8');
                 $content = ob_get_contents();
                 ob_end_clean();
                 echo $content;
        
        
    }
    
    public function exportCateLiteAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $zwfuser = Business_Common_Users::getInstance();
        $lstorename = $zwfuser->getListByUname(FALSE);
        $storename = array();
        foreach ($lstorename as $items){
            $storename[$items["userid"]] = $items["storename"];
        }
        $_users_products = Business_Addon_UsersProducts::getInstance();
        $_option = Business_Addon_Options::getInstance();
        $cated_id        = $this->_request->getParam("cated_id");
        $cated_name        = $this->_request->getParam("cated_name");
        $idregency = $this->_identity["idregency"];
        if($_option->isBGD($idregency) == true){
            $flag = $this->_request->getParam("flag");
        }else{
            if($idregency ==26){
                $flag = "";
            }else{
                $flag = 1;
            }
        }
        $days_created_end = $this->_request->getParam("day_created_end");
        $__endday              = $_option->getDayEndByMonths();
        if ($days_created_end == null) {
            $days_created_end   = date('Y/m/01') . ' - ' .$__endday;
        }
        $this->view->days_created_end = $days_created_end;
        
        $created_date = substr($days_created_end, 0, 10);
        $created_day = str_replace("/", "-", $created_date) . ' 00:00:00';
        
        $end_date = substr($days_created_end, 13, 10);
        $end_day = str_replace("/", "-", $end_date) . ' 23:59:59';
        $list = $_users_products->getListByCateIdLite(1,$created_day, $end_day, $cated_id,"","",$flag);
        $day = "_".date('Ymd');
        header("Content-Disposition: attachment; filename='$cated_name$day.csv'");
        header("Content-Type: text/csv; charset=utf-8");
        header('Content-Type: charset=utf-8');
        $finalData= array();
        $strItem ="Tên sản phẩm\tMàu\tTổng tiền\tTổng số lượng\tNgày bán\tChi Nhánh"."\r\n";            
        foreach ($list as &$items){
            $vote_id    = $items["vote_id"];
            $vote_name = $storename[$vote_id];
            $items["create_date"] = date('d/m/Y',  strtotime($items["create_date"]));
            $finalData = array(
                $items["products_name"], // For chars with accents.
                $items["product_color"], // For chars with accents.
                $items["total_price"], 
                $items["total_items"], 
                $items["create_date"],
                $vote_name,
            );
            $strItem .= implode("\t", $finalData)."\r\n";
        }
        ob_start();
        echo chr(255) . chr(254) . mb_convert_encoding($strItem, 'UTF-16LE', 'UTF-8');
        $content = ob_get_contents();
        ob_end_clean();
        echo $content;
    }

    public function detailAction(){
        $users_products = Business_Addon_UsersProducts::getInstance();
        $make_money = Business_Addon_MakeMoney::getInstance();
        $vote_ids = $this->_request->getParam("vote_id");
        $products_id = $this->_request->getParam("products_name");
        $this->view->pid = $products_id;
        $months = $this->_request->getParam("month");
        if($months == 0){
            $months = date('m');
        }
        $this->view->months = $months;
        
        $id = $this->_request->getParam("id");
        $detail = $make_money->getDetail($id);
        $this->view->detail = $detail;
        $create_day = $detail["create_day"];
        $end_day = $detail["end_day"];
        $quantity = $detail["quantity"];
        $v_id = $detail["vote_id"];
        if($vote_ids == null){
            $vote_ids = $v_id;
        }
        $this->view->v_ids =  $vote_ids;
        if($products_id == null){
            $products_id = $detail["products_id"];
        }
        $p_ids = $detail["products_id"];
        $cated_id = $detail["cated_id"];
        $p_id = explode(",", $p_ids);
        $products_name = $detail["products_name"];
        $p_name = explode(",", $products_name);
        
        $this->view->products_id = $p_id;
        $this->view->products_name = $p_name;
        $storename = array();
        $_arrstorename = Business_Common_Users::getInstance()->getListByUname(false);
        foreach ($_arrstorename as $item){
            $storename[$item["userid"]] = $item["storename"];
        }
        
        
        $list_p = $users_products->getListByUserid6($vote_ids, $create_day,$end_day,$products_id,$cate_bonus,$quantity);
        foreach ($list_p as &$items){
            
            if($items["products_id"] != 0){
                $check_makemoney = $users_products->getCountGroupByUserid($vote_ids, $create_day,$end_day,$p_ids);
            }else{
                $check_makemoney = $users_products->getCountGroupByCated($vote_ids, $create_day,$end_day,$items["cated_id"]);
            }
            
            $items["countp"] = $check_makemoney[0]["count(*)"];
            $items["storename"] = $storename[$items["vote_id"]];
        }
        
        $this->view->items = $list_p;
    }

    public function indexAction(){
        $_user = Business_Common_Users::getInstance();
        $list_vote = $_user->getListByUname();
        $this->view->list_vote = $list_vote;
        $vote_ids = $this->_request->getParam("vote_id");
        $this->view->v_ids =  $vote_ids;
        for ($i = 1; $i <= 12; $i++) {
            $months_view[$i] = $i;
        }
        $this->view->months_view = $months_view;
        $months = $this->_request->getParam("month");
        if($months == 0){
            $months = date('m');
        }
        $this->view->months = $months;
    $keywork = $this->_request->getParam("keywork");
    $this->view->keywork = $keywork;
    
    $make_money = Business_Addon_MakeMoney::getInstance();
    $group_name = $make_money->getGroupByName();
    $this->view->group_name = $group_name;
    $g_name = $this->_request->getParam("group_name");
    $this->view->g_name = $g_name;
    $users_products = Business_Addon_UsersProducts::getInstance();
    $list = $make_money->getList($vote_ids,$months,$g_name);
    $storename = array();
    $_arrstorename = $_user->getListByUname(false);
    foreach ($_arrstorename as $item){
        $storename[$item["userid"]] = $item["storename"];
    }
    foreach ($list as &$items){
        $v_id = $items["vote_id"];
        $create_day = $items["create_day"];
        $end_day = $items["end_day"];
        $products_id = $items["products_id"];
        $cated_id = $items["cated_id"];
//        var_dump($create_day,$end_day);exit();
        if($products_id != 0){
            $check_makemoney = $users_products->getCountGroupByUserid($v_id, $create_day,$end_day,$products_id);
        }else{
            $check_makemoney = $users_products->getCountGroupByCated($v_id, $create_day,$end_day,$cated_id);
        }
        $arr =array();
        $items["countp"] = $check_makemoney[0]["count(*)"];
        $vote_id = explode(",",$items["vote_id"]);
        foreach ($vote_id as $vid){
            $arr[]= $storename[$vid]; 
        }
        $items["storename"] = $arr;
        $products_name = $items["products_name"];
    }
//     echo '<pre>';var_dump($list);exit();
    $pname = explode(",", $products_name);
    $this->view->products_name = $pname;
    $this->view->items = $list;
}
public function addAction(){
    $list = Business_Common_Users::getInstance()->getListByUname();
    $this->view->items = $list;
    $menuname = $this->_request->getParam('menuname','');
//            var_dump($menuname);exit();
    if ($menuname == '' || is_null($menuname)){
        $menuname = $this->_default_menu2;
    }
    $this->view->menuname = $menuname;
    $this->view->productstype = $this->_plist;
//        var_dump($this->view->plist);exit();
    if($menuname == 3){
        $pmenuDT = Business_Helpers_Products::getProductMenu();
    }
    if($menuname == 4){
        $pmenuDT = Business_Helpers_Products::getAccMenu();
    }
    if($menuname == 5){
        $pmenuDT = Business_Addon_Options::getInstance()->getTabletMenu();
    }
    $this->view->menu_items = $pmenuDT;    
}

public function editAction(){
        $id = (int)$this->_request->getParam("id");
        $this->view->id = $id ;
        if($id > 0){
            $detail = Business_Addon_MakeMoney::getInstance()->getDetail($id);
            $this->view->items = $detail;
            $vote_id = $detail["vote_id"];
            $v_id = explode(",", $vote_id);
            $this->view->v_id = $v_id;
            $arr='';
            for($i=0;$i<count($v_id);$i++){
                $pdetail = Business_Common_Users::getInstance()->getDetailById($v_id[$i]);
                $arr[] = $pdetail["storename"];
            }
            $this->view->vote_id = $arr;
//            var_dump($this->view->vote_id);exit();
            $this->view->cated_id = $detail["cated_id"];
            $this->view->cate_bonus = $detail["cate_bonus"];
            $this->view->cated_name = $detail["cated_name"];
            $this->view->quantity = $detail["quantity"];
            $this->view->bonus_money = $detail["bonus_money"];
            $this->view->make_money_name = $detail["name"];
            $create_day = $detail["create_day"];
            $create_day = substr($create_day, 0,11);
            $create_day = str_replace("-", "/", $create_day);
            $end_day = $detail["end_day"];
            $end_day = substr($end_day, 0,11);

            $end_day = str_replace("-", "/", $end_day);
            $this->view->days_created_end = $create_day." - ".$end_day;
            $this->view->products_id = $detail["products_id"];
            $this->view->products_name = $detail["products_name"];
            }
        $menuname = $this->_request->getParam('menuname','');
//            var_dump($menuname);exit();
            if ($menuname == '' || is_null($menuname)){
                $menuname = $this->_default_menu2;
            }
            $this->view->menuname = $menuname;
        $this->view->productstype = $this->_plist;
//        var_dump($this->view->plist);exit();
        if($menuname == 3){
            $pmenuDT = Business_Helpers_Products::getProductMenu();
        }
        if($menuname == 4){
            $pmenuDT = Business_Helpers_Products::getAccMenu();
        }
        if($menuname == 5){
            $pmenuDT = Business_Addon_Options::getInstance()->getTabletMenu();
        }
        $this->view->menu_items = $pmenuDT;
    }
    public function reportAction(){
        $u_product = Business_Addon_UsersProducts::getInstance();
        $menu_product = Business_Helpers_Products::getProductMenu();
        
        $days_created_end = $this->_request->getParam("day_created_end");
        if ($days_created_end == null) {
            $days_created_end = date('Y/m/d') . ' - ' . date('Y/m/d');
        }
        $this->view->days_created_end = $days_created_end;
        
        $created_date = substr($days_created_end, 0, 10);
        $created_day = str_replace("/", "-", $created_date) . ' 00:00:00';
        
        $end_date = substr($days_created_end, 13, 10);
        $months = substr($days_created_end, 18, 2);
        $end_day = str_replace("/", "-", $end_date) . ' 23:59:59';
        $sumtong = $u_product->sumGroupByCated($created_day, $end_day);
        $this->view->sumtong = $sumtong[0]["sum(products_price)"];
        foreach ($menu_product as &$items){
            $cateid = $items["itemid"];
            $temp = $u_product->sumGroupByCated2($created_day, $end_day, $cateid);
            $items["sum"] = $temp[0]["sum(products_price)"];
            $bonus = $u_product->sumBonusByCated($created_day, $end_day, $cateid);
            $items["bonus"] = $bonus[0]["sum(bonus)"];
//            var_dump($sump);
        }
//        exit();
        $this->view->menu_products = $menu_product;
        $menu_tablet = Business_Addon_Options::getInstance()->getTabletMenu();
        foreach ($menu_tablet as &$items){
            $cateid = $items["itemid"];
            $temp = $u_product->sumGroupByCated2($created_day, $end_day, $cateid);
            $items["sum"] = $temp[0]["sum(products_price)"];
            $bonus = $u_product->sumBonusByCated($created_day, $end_day, $cateid);
            $items["bonus"] = $bonus[0]["sum(bonus)"];
        }
        $this->view->menu_tablet = $menu_tablet;
    }
    public function reportDetailAction(){
        $u_product                      = Business_Addon_UsersProducts::getInstance();
        $_products_items                = Business_Ws_ProductsItem::getInstance();
        $_zwf_user                      = Business_Common_Users::getInstance();
        $_option                        = Business_Addon_Options::getInstance();
        $cated_id                       = $this->_request->getParam("cated_id");
        $this->view->itemid             = $cated_id;
        $menu_product                   = $_products_items->getListByCatedId($cated_id);
//        echo "<pre>";
//        var_dump($menu_product);
//        exit();
        $list_vote = $_zwf_user->getListByUname(false);
        $days_created_end = $this->_request->getParam("day_created_end");
        $__endday              = $_option->getDayEndByMonths();
        if ($days_created_end == null) {
            $days_created_end   = date('Y/m/01') . ' - ' .$__endday;
        }
        $this->view->days_created_end = $days_created_end;
        
        $created_date = substr($days_created_end, 0, 10);
        $created_day = str_replace("/", "-", $created_date) . ' 00:00:00';
        
        $end_date = substr($days_created_end, 13, 10);
        $months = substr($days_created_end, 18, 2);
        $end_day = str_replace("/", "-", $end_date) . ' 23:59:59';
        $this->view->menu_products = $menu_product;
        $this->view->menu_products2 = Business_Helpers_Products::getProductMenu();;
        $listAll = $u_product->getListAllByCatedByVote2($created_day, $end_day,$cated_id);
//        echo '<pre>';var_dump($listAll);exit();
        $ret = array();
        $count = array();
        foreach($listAll as $item) {
            $vote_id  = $item["vote_id"];
            $products_id  = $item["products_id"];
            $ret[$vote_id][$products_id] = $item["sum"];
            $count[$vote_id][$products_id] = $item["countp"];
        }
        $this->view->list_vote = $list_vote;
        $this->view->sums = $ret;
        $this->view->count = $count;
    }
    
    public function getProductsidAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $_products                  = Business_Helpers_Products::getInstance();
        $_option                    = Business_Addon_Options::getInstance();
        $cated_hnam                 = $this->_request->getParam("cated_hnam");
        $_tmp = array();
        if($cated_hnam == null){
            $cated_hnam             = 3;
        }
        if($cated_hnam ==3){
            $menu_product2          = $_products->getProductMenu();
            $_tmp[0]["itemid"] = 999997;
            $_tmp[0]["title"] = "Apple";
        }
        if($cated_hnam ==5){
            $menu_product2          = $_option->getTabletMenu();
            $_tmp[0]["itemid"] = 999998;
            $_tmp[0]["title"] = "Apple";
        }
        if($cated_hnam ==4){
            $menu_product2          = $_products->getAccMenu2();
        }
        if($cated_hnam ==6){
            $menu_product2          = $_option->getLaptopMenu();
        }
        $ret                        = array();
        $arr                        = array();
        foreach ($menu_product2 as $items){
            $arr["itemid"]          = $items["itemid"];
            $arr["title"]           = $items["title"];
            $ret[]                  = $arr;
        }
        $ret = array_merge($_tmp, $ret);
        echo json_encode($ret);
    }
    public function getProductsid2Action(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $_products                  = Business_Helpers_Products::getInstance();
        $_option                    = Business_Addon_Options::getInstance();
        $_accept_by_cated           = Business_Common_AcceptByCated::getInstance();
        $_menuitem = Business_Ws_MenuItem::getInstance();
        $cated_hnam                 = $this->_request->getParam("cated_hnam");
        $userid = $this->_identity["userid"];
        $detail = $_accept_by_cated->getDetailByUserIdProductsId($userid,$cated_hnam);
        $cateid = $detail["cated_id"];
        $menu = $_menuitem->getDetailById2($cateid);
        
        $ret                        = array();
        $arr                        = array();
        foreach ($menu as $items){
            $arr["itemid"]          = $items["itemid"];
            $arr["title"]           = $items["title"];
            $ret[]                  = $arr;
        }
        echo json_encode($ret);
    }

    public function report2Action(){
        $u_product = Business_Addon_UsersProducts::getInstance();
        $_zwf_user = Business_Common_Users::getInstance();
        $_option    = Business_Addon_Options::getInstance();
        $cated_id  = $this->_request->getParam("cated_id");
        $this->view->itemid             = $cated_id;
        $_menuitem = Business_Ws_MenuItem::getInstance();
        $menu_product = Business_Helpers_Products::getProductMenu();
        $list_vote = $_zwf_user->getListByUname(false);
        $days_created_end = $this->_request->getParam("day_created_end");
        $__endday              = $_option->getDayEndByMonths();
        if ($days_created_end == null) {
            $days_created_end   = date('Y/m/01') . ' - ' .$__endday;
        }
        $this->view->days_created_end = $days_created_end;
        
        $created_date = substr($days_created_end, 0, 10);
        $created_day = str_replace("/", "-", $created_date) . ' 00:00:00';
        
        $end_date = substr($days_created_end, 13, 10);
        $months = substr($days_created_end, 18, 2);
        $end_day = str_replace("/", "-", $end_date) . ' 23:59:59';
        $this->view->menu_products = $menu_product;
//        $menu_tablet = Business_Addon_Options::getInstance()->getTabletMenu();
//        $this->view->menu_tablet = $menu_tablet;
        $listAll = $u_product->getListAllByCatedByVote($created_day, $end_day);
//        var_dump($listAll);exit();
        $ret = array();
        $count = array();
        foreach($listAll as $item) {
            $vote_id  = $item["vote_id"];
            $cated_id  = $item["cated_id"];
            $ret[$vote_id][$cated_id] = $item["sum"];
            $count[$vote_id][$cated_id] = $item["countp"];
        }
        $this->view->list_vote = $list_vote;
        $this->view->sums = $ret;
        $this->view->count = $count;
        
    }
//    public function doanhSoBanAction(){
//        $u_product = Business_Addon_UsersProducts::getInstance();
//        $_zwf_user = Business_Common_Users::getInstance();
//        $_option    = Business_Addon_Options::getInstance();
//        $cated_id  = $this->_request->getParam("cated_id");
//        $this->view->itemid             = $cated_id;
//        $_menuitem = Business_Ws_MenuItem::getInstance();
//        $menu_product = Business_Helpers_Products::getProductMenu();
//        $list_vote = $_zwf_user->getListByUname(false);
//        $days_created_end = $this->_request->getParam("day_created_end");
//        $__endday              = $_option->getDayEndByMonths();
//        if ($days_created_end == null) {
//            $days_created_end   = date('Y/m/01') . ' - ' .$__endday;
//        }
//        $this->view->days_created_end = $days_created_end;
//        
//        $created_date = substr($days_created_end, 0, 10);
//        $created_day = str_replace("/", "-", $created_date) . ' 00:00:00';
//        
//        $end_date = substr($days_created_end, 13, 10);
//        $months = substr($days_created_end, 18, 2);
//        $end_day = str_replace("/", "-", $end_date) . ' 23:59:59';
//        $this->view->menu_products = $menu_product;
////        $menu_tablet = Business_Addon_Options::getInstance()->getTabletMenu();
////        $this->view->menu_tablet = $menu_tablet;
//        $listAll = $u_product->getListAllByCatedByVote($created_day, $end_day);
////        var_dump($listAll);exit();
//        $ret = array();
//        $count = array();
//        foreach($listAll as $item) {
//            $vote_id  = $item["vote_id"];
//            $cated_id  = $item["cated_id"];
//            $ret[$vote_id][$cated_id] = $item["sum"];
//            $count[$vote_id][$cated_id] = $item["countp"];
//        }
//        $this->view->list_vote = $list_vote;
//        $this->view->sums = $ret;
//        $this->view->count = $count;
//        $this->view->created_date = $created_date;
//        $this->view->end_date = $end_date;
//        
//        $list = $u_product->getListByVote($created_day, $end_day);
//        $ret2 = array();
//        $count2 = array();
//        foreach($list as $item) {
//            $vote_id  = $item["vote_id"];
//            $ret2[$vote_id] = $item["sum"];
//            $count2[$vote_id] = $item["countp"];
//        }
//        $this->view->sums2 = $ret2;
//        $this->view->count2 = $count2;
//    }
    public function apiDoanhsobanAction(){
        $u_product = Business_Addon_UsersProducts::getInstance();
        $_zwf_user = Business_Common_Users::getInstance();
        $_option    = Business_Addon_Options::getInstance();
        $cated_id  = $this->_request->getParam("cated_id");
        $this->view->itemid             = $cated_id;
        $_menuitem = Business_Ws_MenuItem::getInstance();
        $menu_product = Business_Helpers_Products::getProductMenu();
        $list_vote = $_zwf_user->getListByUname(false);
        $days_created_end = $this->_request->getParam("day_created_end");
        $__endday              = $_option->getDayEndByMonths();
        if ($days_created_end == null) {
            $days_created_end   = date('Y/m/01') . ' - ' .$__endday;
        }
        $this->view->days_created_end = $days_created_end;
        
        $created_date = substr($days_created_end, 0, 10);
        $created_day = str_replace("/", "-", $created_date) . ' 00:00:00';
        
        $end_date = substr($days_created_end, 13, 10);
        $months = substr($days_created_end, 18, 2);
        $end_day = str_replace("/", "-", $end_date) . ' 23:59:59';
        $this->view->menu_products = $menu_product;
//        $listAll = $u_product->getListAllByCatedByVote($created_day, $end_day);
        $ret = array();
        $count = array();
        $result    = array();
//        foreach($listAll as $item) {
//            $vote_id  = $item["vote_id"];
//            $cated_id  = $item["cated_id"];
//            $ret[$vote_id][$cated_id] = $item["sum"];
//            $count[$vote_id][$cated_id] = $item["countp"];
//        }
//        $this->view->list_vote = $list_vote;
//        $this->view->sums = $ret;
        $this->view->count = $count;
        $arr    = array();
        
//        foreach ($menu_product as $items){
//             $arr["title"]   = $items["title"];
////             $arr["cated_id"]   = $items["itemid"];
//            foreach ($list_vote as $_vote){
//               $arr2=array();
////                $arr2["vote_id"] = $_vote["userid"];
////                $arr2["vote_name"] = $_vote["abbreviation"];
////                $arr["tong"] = $ret;
////                $arr["sl"] = $count;
////                $arr[] = $arr2;
////                $arr["name"] = $arr;
//            }
//            $result[] = $arr;
//        }
        
//        echo "<pre>";
//        var_dump($result);
//        exit();
//        echo $result;exit();
        echo json_encode($result);
    }

    public function deleteAction(){
            $this->_helper->Layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            $id = $this->_request->getParam("id");
//            var_dump($id);exit();
        Business_Addon_MakeMoney::getInstance()->_delete($id);
        
    }
    public function restoreAction(){
            $this->_helper->Layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            $id = $this->_request->getParam("id");
            Business_Addon_MakeMoney::getInstance()->restore($id);
    }
    
    
    public function isValid($ret,$menuname,$vote_id, $products_id,$cated_id, $quantity,$day_created_end,$bonus_money,$make_money_name,$p_name,$vote_all){
        if($menuname == 0){
            $err['id'] = "menuname";
            $err['msg'] = "Lỗi!\nVui lòng chọn loại chương trình make-money .";
            $ret[] = $err;
        }
        if($menuname ==1 && $products_id == null){
            $err['id'] = "products_id";
            $err['msg'] = "Lỗi!\nVui lòng nhập sản phẩm";
            $ret[] = $err;
        }
        if($menuname != 1 && $menuname != 0 & $cated_id == ""){
            $err['id'] = "cated_id";
            $err['msg'] = "Lỗi!\nVui lòng chọn loại sản phẩm .";
            $ret[] = $err;
        }
        if ($make_money_name == null) {
            $err['id'] = "make_money_name";
            $err['msg'] = "Lỗi!\nVui lòng nhập tên chương trình make-money .";
            $ret[] = $err;
        }
        if (($vote_id == null || $vote_id == 0) && $vote_all ==0) {
            $err['id'] = "vote_id";
            $err['msg'] = "Vui lòng check cửa hàng";
            $ret[] = $err;
        }
        
        if ($quantity == null) {
            $err['id'] = "quantity";
            $err['msg'] = "Lỗi!\nVui lòng nhập số lượng hoặc tổng doanh thu.";
            $ret[] = $err;
        }
        if ($day_created_end == null) {
            $err['id'] = "day_created_end";
            $err['msg'] = "Lỗi!\nVui lòng nhập thời gian diển ra chương trình make-money.";
            $ret[] = $err;
        }
        if ($bonus_money == null) {
            $err['id'] = "bonus_money";
            $err['msg'] = "Lỗi!\nVui lòng nhập số tiền thưởng trên 1 sản phẩm.";
            $ret[] = $err;
        }
        
        return $ret;
    }
    public function saveAction(){
        if ($this->_request->isPost()) {
            $this->_helper->Layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            $_user = Business_Common_Users::getInstance();
            $cate_bonus = $this->_request->getParam("cate_bonus");
            $vote_all = (int)$this->_request->getParam("vote_all",0);
            $list_vote = $_user->getListByUname(FALSE);
//            var_dump($_REQUEST);exit();
            $menuname = $this->_request->getParam("menuname");
            $list = $_user->getListByUname(FALSE);
            $arr='';
            foreach ($list as $plist){
                $userid = $plist["userid"];
                $vote_name = $this->_request->getParam("vote_check_$userid");
                if($vote_name != null){
                    $arr[] = "$vote_name";
                }
            }
            $vote_id = implode(",", $arr);
            if(count($arr) > 1){
                $multi = count($arr);
            }else{
                $multi = 1;
            }
            // param san pham
            $id = (int)($this->_request->getParam("id",0));
            
            $pid = $this->_request->getParam("products_id");
            foreach ($pid as $lpid){
                $p_id = explode("--", $lpid);
                $p_ids  = $p_id[2];
                $tmp_id[]=$p_ids;
                
                $p_name  = $p_id[0];
                $tmp_name[]=$p_name;
            }
            $products_id = implode($tmp_id, ",");
            $products_name = implode($tmp_name, ",");
            $cated_id = $this->_request->getParam("cated_id",0);
            $cated_name = $this->_request->getParam("cated_pname");
            $make_money_name = $this->_request->getParam("make_money_name");
//            $cated_id = str_replace(",", "", $cated_id);
            $quantity = $this->_request->getParam("quantity");
            $quantity = str_replace(",","",$quantity);
            $day_created_end = $this->_request->getParam("day_created_end");
            $created_day = substr($day_created_end,0,10)." 00:00:00";
            $created_day = str_replace("/", "-", $created_day);
            $end_day = substr($day_created_end,13,23)." 23:59:59";
            $end_day = str_replace("/", "-", $end_day)." ";
            $bonus_money = $this->_request->getParam("bonus_money");
            $bonus_money = str_replace(",","",$bonus_money);
//            var_dump($products_id);exit();
            $ret = array();
            if($id == 0){
                 $ret = $this->isValid($ret,$menuname,$vote_id, $products_id,$cated_id, $quantity,$day_created_end,$bonus_money,$make_money_name,$cated_name,$vote_all);
            }
            if (count($ret) > 0){
                echo json_encode($ret);
                
            }else{
                if($id > 0){
                }
                else{
                    $data= array();
                } 
                
                $data["products_id"] = $products_id;
                $data["cated_id"] = $cated_id;
                $data["quantity"] = $quantity;
                $data["create_day"] = $created_day;
                $data["end_day"] = $end_day;
                $data["bonus_money"] = $bonus_money;
                $data["name"] = $make_money_name;
                $data["cated_name"] = $cated_name;
                $data["multi"] = $multi;
                $data["enabled"] = 1;
                $data["products_name"] = $products_name;
                $data["cate_bonus"] = $cate_bonus;
//                var_dump($data);exit();
                if($id == 0){
                    if($vote_all == 1){// tich tat ca cac chi nhanh
                        foreach ($list_vote as $pvote){
                            if($pvote["username"] != "vote_all"){
                                $data["vote_id"] = $pvote["userid"];
                                Business_Addon_MakeMoney::getInstance()->insert($data);
                            }
                        }
                    }
                    if($vote_id != NULL){
                        $data["vote_id"] = $vote_id;
                        Business_Addon_MakeMoney::getInstance()->insert($data);
                    }
                    
                }
                
                else{
                    Business_Addon_MakeMoney::getInstance()->update($id,$data);
                }
                $err['id'] = "ok";
                $err['msg'] = "ok";
                $ret[] = $err;
                echo json_encode($ret);
            }
            
            
        }
    }
    public function rendererView($uname="",$view){
        if($uname != null){
            if(strpos($this->_identity["username"], $uname) !== false){
                $this->_helper->viewRenderer($view);
            }
        }
        return true;
    }
    

}
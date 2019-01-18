<?php

/**
 * User Admin Home Controller
 * @author: nghidv
 */
class User_Admin_TargetController extends Zend_Controller_Action {

    private $_identity;
    public function init() {
        // do something
        BlockManager::setLayout('appbh');
        $auth = Zend_Auth::getInstance(); 
        $this->_identity = (array) $auth->getIdentity();
        $this->view->full_name = $this->_identity["fullname"];
        if ($this->_identity != null) {
            $username = $this->_identity["username"];
            $this->view->username = $username;
        }
    }
private $_plist = array(
                    "3" => "Điện thoại",
                    "5" => "Máy tính bảng",
                    "4" => "Phụ kiện",

                );
private $_default_menu = "3";
public function getHistoryAction(){
    $this->_helper->Layout()->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);
    $_history = Business_Common_HistoryTarget::getInstance();
    $_option = Business_Addon_Options::getInstance();
    $id = (int)$this->_request->getParam("id");
    $token = $this->_request->getParam("token");
    $seckey ="TARGET2016HNAMMOBILE";
    $ztoken = md5($seckey.$id);
    if($ztoken != $token){
        die();
    }
    $list = array();
    if($id > 0){
        $list = $_history->getListById($id);
        if(!empty($list)){
            foreach ($list as &$items){
                if($items["flag"]==1){
                    $items["name_flag"] = "Công ty";
                }else{
                    $items["name_flag"] = "Hnam";
                }
                $items["name_type"] = $_option->getCateTargetHnamNew($items["type"]);
                $items["total"] = number_format($items["total"]);
                $items["money"] = number_format($items["money"]);
            }
            
        }
    }
    echo json_encode($list);
}
public function get_total_mcu($storeid,$from,$to){
    $total = 0;
    $__bs = Business_Addon_Usedphonehistory::getInstance();
    $_users_products = Business_Addon_UsersProducts::getInstance();
    $type = "4,7"; // cũ,demo,99,vt99
    $data = $_users_products->get_list_by_type($type,$storeid,$from,$to);
    foreach ($data as $d){
        $__price2[$d["products_id"]] = $d["products_price"];
        if((int)$d["products_id"] >0){
            $__strid[] = $d["products_id"];
        }
    }
    if($__strid != NULL){
        $strid = implode(",", $__strid);
        $list = $__bs->getListByItemID($strid);
    }
    $__price = array();
    foreach ($list as $val){
        $__price[$val["itemid"]] = $val["price"];
    }
    foreach ($data as $item){
        if((int)$__price[$item["products_id"]] >0 &&  (int)$__price2[$item["products_id"]] < (int)$__price[$item["products_id"]]){
            $__autoid[]=$item["autoid"];
        }
    }
    if($__autoid != NULL){
        $autoid = implode(",", $__autoid);
        $query = "update users_products set status_thuhoi=1 where autoid IN ($autoid)";
        $_users_products->excute($query);
    }
}


public function moneyMonthlyAction(){
    $_option = Business_Addon_Options::getInstance();
    $_zwfuser = Business_Common_Users::getInstance();
    $p = $this->_request->getParam("p");
    $idregency = $this->_identity["idregency"];
//    if($idregency ==11 || $idregency ==14 ){
//        $this->_helper->viewRenderer('money-monthly');
//    }else{
//        $this->_helper->viewRenderer('money-monthly2');
//    }
    
    
    $storeid = $this->_identity["parentid"];
    $bgd =0;
    if($_option->isBGD($idregency) || $idregency ==35 || $idregency ==36 || $idregency ==33){
        $storeid = (int)$this->_request->getParam("storeid");
        if($storeid ==0){
            $storeid =12;
        }
        $bgd =1;
        $this->_helper->viewRenderer('money-monthly2');
    }
    $this->view->bgd = $bgd;
    for ($i = 1; $i <= 12; $i++) {
            $months[] = $i;
        }
        $this->view->months = $months;
        
        for ($i = 2014; $i <= date('Y') + 5; $i++) {
            $years[] = $i;
        }
        $this->view->years = $years;
        
        $month = (int)$this->_request->getParam("month");
        if($month ==0){
            $month = date('m');
        }
        $this->view->month = $month;
        $year = (int)$this->_request->getParam("year");
        if($year ==0){
            $year = date('Y');
        }
        $this->view->year = $year;
        
        $_recipeid = Business_Addon_Recipe::getInstance();
        
        $detail_recipe = $_recipeid->getDetail($storeid, $month, $year);
        if($detail_recipe !=null){
            $recipeid = $detail_recipe["recipeid"];
            $slist_reciped = $_option->getCTTarget($recipeid);
        }
        $date_from              = $year."-".$month."-01";
        $date_to                = $year."-".$month."-".date('t', mktime(0, 0, 0, $month, 1, $year))." 23:59:59";
        
        $pp = (int)$this->_request->getParam("pp");
        if($pp==1){
            $total_thuhoitarget = $this->get_total_mcu($storeid,$date_from,$date_to);
            $this->view->total_thuhoitarget = $total_thuhoitarget;
//            echo "<pre>";
//            var_dump($total_thuhoitarget);
//            die();
        }
        $this->view->total_money = $detail_recipe["total_money"];
        $this->view->detail_recipe = $detail_recipe;
        $this->view->slist_reciped = $slist_reciped;
        
        $arr_mb = array();
        $groupuser = Business_Common_Users::getInstance()->countUserByStoreid($storeid);
        
        foreach ($groupuser as &$items){
            $arr_mb[$items["idregency"]] = $items["total"];
            $arr_mb[0] = 1;
            $arr_mb[14] = $arr_mb["13"] + $arr_mb[14];
        }
        $this->view->arr_mb= $arr_mb;
        $this->view->storeid = $storeid;
        $list_store = $_zwfuser->getListByUname(FALSE);
        $this->view->list_store = $list_store;
        $storename = array();
        foreach ($list_store as $items){
            $storename[$items["userid"]] = $items["storename"];
        }
        $this->view->storename = $storename;
        
        
        
        $_target = Business_Common_Target::getInstance();
        $list = $_target->getList($month, $year,$storeid);
        
        foreach ($list as &$items){
            if($items["flag"] ==1){
                $money_cty[$items["type"]] = $items["money"];
                $total_cty[$items["type"]] = $items["total"];
                $__id_cty[$items["type"]] = $items["id"];
            }
            if($items["flag"] ==2){
                $money_hnam[$items["type"]] = $items["money"];
                $total_hnam[$items["type"]] = $items["total"];
                $__id_hnam[$items["type"]] = $items["id"];
            }
        }
        
        $this->view->money_cty = $money_cty;
        $this->view->total_cty = $total_cty;
        
        $this->view->money_hnam = $money_hnam;
        $this->view->total_hnam = $total_hnam;
        
        $this->view->id_cty = $__id_cty;
        $this->view->id_hnam = $__id_hnam;
        
        
        $_users_products = Business_Addon_UsersProducts::getInstance();
        $datas = $_users_products->getListTargetByMonthYear($month, $year, $storeid);
        
        foreach ($datas as &$items){
            $_listmb[] = $items["mb"];
             if($items["flag"] ==1){
                if($items["type"] ==3){
                    // công ty
                    if($items["productsid"] ==3 && $items["is_apple"] ==0){ // điện thoại khác
                        $sum_mb[$items["mb"]][1] += $items["sum"];
                        $totals_cty[$items["mb"]][1] += $items["total"];
                        
                        $sum_cty[1] += $items["sum"];
                        $tt_cty[1] += $items["total"];
                        
                    }
                    if($items["productsid"] ==3 && $items["is_apple"] ==1){ // điện thoại apple
                        $sum_mb[$items["mb"]][2] += $items["sum"];
                        $totals_cty[$items["mb"]][2] += $items["total"];
                        
                        $sum_cty[2] += $items["sum"];
                        $tt_cty[2] += $items["total"];
                    }
                    if($items["productsid"] ==4){ // Phụ kiện
                        $sum_mb[$items["mb"]][3] += $items["sum"];
                        $totals_cty[$items["mb"]][3] += $items["total"];
                        
                        $sum_cty[3] += $items["sum"];
                        $tt_cty[3] += $items["total"];
                    }
                    if($items["productsid"] ==5 && $items["is_apple"] ==0){ // MTB khác
                        $sum_mb[$items["mb"]][4] += $items["sum"];
                        $totals_cty[$items["mb"]][4] += $items["total"];
                        
                        $sum_cty[4] += $items["sum"];
                        $tt_cty[4] += $items["total"];
                    }
                    if($items["productsid"] ==5 && $items["is_apple"] ==1){ // MTB Apple
                        $sum_mb[$items["mb"]][5] += $items["sum"];
                        $totals_cty[$items["mb"]][5] += $items["total"];
                        
                        $sum_cty[5] += $items["sum"];
                        $tt_cty[5] += $items["total"];
                    }
                    if($items["productsid"] ==6){ // Laptop
                        $sum_mb[$items["mb"]][7] += $items["sum"];
                        $totals_cty[$items["mb"]][7] += $items["total"];
                        
                        $sum_cty[7] += $items["sum"];
                        $tt_cty[7] += $items["total"];
                    }
                    if($items["productsid"] ==8){ // Đồng hồ thông minh
                        $sum_mb[$items["mb"]][8] += $items["sum"];
                        $totals_cty[$items["mb"]][8] += $items["total"];
                        
                        $sum_cty[8] += $items["sum"];
                        $tt_cty[8] += $items["total"];
                    }
                }
                if($items["type"] ==4){ // Cũ
                    $sum_mb[$items["mb"]][6] += $items["sum"];
                    $totals_cty[$items["mb"]][6] += $items["total"];
                    
                    $sum_cty[6] += $items["sum"];
                    $tt_cty[6] += $items["total"];
                }
                if($items["type"] ==5){ // Demo
                    $sum_mb[$items["mb"]][9] += $items["sum"];
                    $totals_cty[$items["mb"]][9] += $items["total"];
                    
                    $sum_cty[9] += $items["sum"];
                    $tt_cty[9] += $items["total"];
                }
                if($items["type"] ==6){ // 99%
                    $sum_mb[10] += $items["sum"];
                    $totals_cty[$items["mb"]][10] += $items["total"];
                    
                    $sum_cty[10] += $items["sum"];
                    $tt_cty[10] += $items["total"];
                }
                if($items["type"] ==7){ // VT
                    $sum_mb[$items["mb"]][11] += $items["sum"];
                    $totals_cty[$items["mb"]][11] += $items["total"];
                    
                    $sum_cty[11] += $items["sum"];
                    $tt_cty[11] += $items["total"];
                }

            }
            if($items["flag"] ==2){
                if($items["type"] ==3){
                    if($items["productsid"] ==3 && $items["is_apple"] ==0){ // điện thoại khác
                        $sum_hnam[1] += $items["sum"];
                        $totals_hnam[1] += $items["total"];
                        
                        $sum_mb_hnam[$items["mb"]][1] += $items["sum"];
                        $total_mb_hnam[$items["mb"]][1] += $items["total"];
                        
                    }
                    if($items["productsid"] ==3 && $items["is_apple"] ==1){ // điện thoại apple
                        $sum_hnam[2] += $items["sum"];
                        $totals_hnam[2] += $items["total"];
                        
                        $sum_mb_hnam[$items["mb"]][2] += $items["sum"];
                        $total_mb_hnam[$items["mb"]][2] += $items["total"];
                    }
                    if($items["productsid"] ==4){ // Phụ kiện
                        $sum_hnam[3] += $items["sum"];
                        $totals_hnam[3] += $items["total"];
                        
                        $sum_mb_hnam[$items["mb"]][3] += $items["sum"];
                        $total_mb_hnam[$items["mb"]][3] += $items["total"];
                    }
                    if($items["productsid"] ==5 && $items["is_apple"] ==0){ // MTB khác
                        $sum_hnam[4] += $items["sum"];
                        $totals_hnam[4] += $items["total"];
                        
                        $sum_mb_hnam[$items["mb"]][4] += $items["sum"];
                        $total_mb_hnam[$items["mb"]][4] += $items["total"];
                    }
                    if($items["productsid"] ==5 && $items["is_apple"] ==1){ // MTB Apple
                        $sum_hnam[5] += $items["sum"];
                        $totals_hnam[5] += $items["total"];
                        
                        $sum_mb_hnam[$items["mb"]][5] += $items["sum"];
                        $total_mb_hnam[$items["mb"]][5] += $items["total"];
                    }
                    if($items["productsid"] ==6){ // Laptop
                        $sum_hnam[7] += $items["sum"];
                        $totals_hnam[7] += $items["total"];
                        
                        $sum_mb_hnam[$items["mb"]][7] += $items["sum"];
                        $total_mb_hnam[$items["mb"]][7] += $items["total"];
                    }
                    if($items["productsid"] ==8){ // Đồng hồ thông minh
                        $sum_hnam[8] += $items["sum"];
                        $totals_hnam[8] += $items["total"];
                        
                        $sum_mb_hnam[$items["mb"]][8] += $items["sum"];
                        $total_mb_hnam[$items["mb"]][8] += $items["total"];
                    }
                }
                if($items["type"] ==4){ // Cũ
                    $sum_hnam[6] += $items["sum"];
                    $totals_hnam[6] += $items["total"];
                    
                    $sum_mb_hnam[$items["mb"]][6] += $items["sum"];
                    $total_mb_hnam[$items["mb"]][6] += $items["total"];
                }
                if($items["type"] ==5){ // Demo
                    $sum_hnam[9] += $items["sum"];
                    $totals_hnam[9] += $items["total"];
                    
                    $sum_mb_hnam[$items["mb"]][9] += $items["sum"];
                    $total_mb_hnam[$items["mb"]][9] += $items["total"];
                }
                if($items["type"] ==6){ // 99%
                    $sum_hnam[10] += $items["sum"];
                    $totals_hnam[10] += $items["total"];
                    
                    $sum_mb_hnam[$items["mb"]][10] += $items["sum"];
                    $total_mb_hnam[$items["mb"]][10] += $items["total"];
                }
                if($items["type"] ==7){ // VT
                    $sum_hnam[11] += $items["sum"];
                    $totals_hnam[11] += $items["total"];
                    
                    $sum_mb_hnam[$items["mb"]][11] += $items["sum"];
                    $total_mb_hnam[$items["mb"]][11] += $items["total"];
                }
                
            }
            
        }
        $this->view->sum_cty = $sum_cty;
        $this->view->scty = $sum_mb;
        
        $this->view->shnam = $sum_mb_hnam;
        
        $this->view->sum_hnam = $sum_hnam;
        $this->view->totals_hnam = $totals_hnam;
        $this->view->tt_hnam = $total_mb_hnam;
        
        $this->view->tt_cty = $totals_cty;
        $this->view->totals_cty = $tt_cty;
        
        if($_listmb !=null){
            $listmbs = array_unique($_listmb);
            $listmb = implode($listmbs, ",");
            $__listmb = explode(",", $listmb);

            $this->view->number_mb = count($__listmb);
            $_member = $_zwfuser->getListById($listmb);
            $this->view->member = $_member;
        }
        
        
        $_ptype      = $_option->getCateTargetHnamNew();
        $this->view->ptype = $_ptype;
        
        
        
        
        
}

public function listmbAction(){
    $_target = Business_Common_Target::getInstance();
    $_option = Business_Addon_Options::getInstance();
    $_users_products = Business_Addon_UsersProducts::getInstance();
    $_zwfuser = Business_Common_Users::getInstance();
    $fullname = array();
    $list_user = $_zwfuser->getMbAndStore();
    foreach ($list_user as $items){
        $fullname[$items["userid"]] = $items["fullname"];
    }
    $this->view->fullnames = $fullname;
    
    
    $idregency = (int)$this->_identity["idregency"];
    $storeid = (int)$this->_identity["parentid"];
    $access = 0;
    if($_option->isBGD($idregency) || $idregency ==35 || $idregency ==36){
        $storeid = (int)  $this->_request->getParam("storeid");
        if($storeid ==0){
            $storeid = 12;
        }
        $access= 1;
    }
    $money_cty = array();
        $total_cty = array();
        $money_hnam = array();
        $total_hnam = array();
        $__id_cty = array();
        $__id_hnam = array();
        for ($i = 1; $i <= 12; $i++) {
            $months[] = $i;
        }
        $this->view->months = $months;
        
        for ($i = 2014; $i <= 2020; $i++) {
            $years[] = $i;
        }
        $this->view->years = $years;
        
        $month = (int)$this->_request->getParam("month");
        if($month ==0){
            $month = date('m');
        }
        $this->view->month = $month;
        $year = (int)$this->_request->getParam("year");
        if($year ==0){
            $year = date('Y');
        }
        $this->view->year = $year;
        $_recipeid = Business_Addon_Recipe::getInstance();
        $detail_recipe = $_recipeid->getDetail($storeid, $month, $year);
        $tytrong_bh = 100;
        if($detail_recipe !=null){
            $recipeid = $detail_recipe["recipeid"];
            $slist_reciped = $_option->getCTTarget($recipeid);
            $tytrong_bh = $slist_reciped[4]["tytrong"];
        }
        $this->view->tytrong_bh = $tytrong_bh/100;
        
        $date_from              = $year."-".$month."-01";
        $number_end = date('t', mktime(0, 0, 0, $month, 1, $year));
        $date_to                = $year."-".$month."-$number_end";
        
        // lấy danh sách các nhân viên đã bán của chi nhánh trong tháng
        
        $data1 = $_users_products->getListTargetByMb($date_from, $date_to, $storeid);
        foreach ($data1 as &$items){
            $_listmb[] = $items["mb"];
        }
        $listmbs = array_unique($_listmb);
        $listmb = implode($listmbs, ",");
        $__listmb = explode(",", $listmb);
        $this->view->listmbs = $__listmb;
        // end lấy danh sách nhân viên
        
        if($idregency ==11 || $idregency ==14 || $idregency ==20 || $idregency ==35 || $idregency ==36 || $_option->isBGD($idregency)){
            
            $this->_helper->viewRenderer('store-listmb');
            $idmb = (int)$this->_request->getParam("idmb");
            if($idmb ==0){
                $idmb = $__listmb[0];
            }
        }else{
            $idmb = $this->_identity["userid"];
        }
        $this->view->idmb = $idmb;
        
        $often_cty = array();
        $often_hnam = array();
        $plist = $_target->getList($month, $year,$storeid);
        foreach ($plist as $items){
            if($items["flag"] ==1){
                $often_cty[$items["type"]] = $items["often"];
            }
            if($items["flag"] ==2){
                $often_hnam[$items["type"]] = $items["often"];
            }
        }
        
        $list = $_target->getList($month, $year,$storeid,$idmb);
        foreach ($list as &$items){
            if($items["flag"] ==1){
                $money_cty[$items["type"]] = $items["money"];
                $total_cty[$items["type"]] = $items["total"];
                $__id_cty[$items["type"]] = $items["id"];
            }
            if($items["flag"] ==2){
                $money_hnam[$items["type"]] = $items["money"];
                $total_hnam[$items["type"]] = $items["total"];
                $__id_hnam[$items["type"]] = $items["id"];
            }
        }
        $this->view->money_cty = $money_cty;
        $this->view->total_cty = $total_cty;
        
        $this->view->money_hnam = $money_hnam;
        $this->view->total_hnam = $total_hnam;
        
        $this->view->id_cty = $__id_cty;
        $this->view->id_hnam = $__id_hnam;
        
        $this->view->often_cty = $often_cty;
        $this->view->often_hnam = $often_hnam;
        $this->view->access = $access;
        $_ptype      = $_option->getCateTargetHnamNew();
        $this->view->ptype = $_ptype;
        
        
        
        
        // nhân viên chi nhánh bán
        $sum_cty= array();
        $totals_cty = array();
        $sum_hnam= array();
        $totals_hnam = array();
        
        
        $data = $_users_products->getListTargetByMb($date_from, $date_to, $storeid,$idmb);
        foreach ($data as $items){
            if($items["flag"] ==1){
                if($items["type"] ==3){
                    // công ty
                    if($items["productsid"] ==3 && $items["is_apple"] ==0){ // điện thoại khác
                        $sum_cty[1] += $items["sum"];
                        $totals_cty[1] += $items["total"];
                    }
                    if($items["productsid"] ==3 && $items["is_apple"] ==1){ // điện thoại apple
                        $sum_cty[2] += $items["sum"];
                        $totals_cty[2] += $items["total"];
                    }
                    if($items["productsid"] ==4){ // Phụ kiện
                        $sum_cty[3] += $items["sum"];
                        $totals_cty[3] += $items["total"];
                    }
                    if($items["productsid"] ==5 && $items["is_apple"] ==0){ // MTB khác
                        $sum_cty[4] += $items["sum"];
                        $totals_cty[4] += $items["total"];
                    }
                    if($items["productsid"] ==5 && $items["is_apple"] ==1){ // MTB Apple
                        $sum_cty[5] += $items["sum"];
                        $totals_cty[5] += $items["total"];
                    }
                    if($items["productsid"] ==6){ // Laptop
                        $sum_cty[7] += $items["sum"];
                        $totals_cty[7] += $items["total"];
                    }
                    if($items["productsid"] ==8){ // Đồng hồ thông minh
                        $sum_cty[8] += $items["sum"];
                        $totals_cty[8] += $items["total"];
                    }
                }
                if($items["type"] ==4){ // Cũ
                    $sum_cty[6] += $items["sum"];
                    $totals_cty[6] += $items["total"];
                }
                if($items["type"] ==5){ // Demo
                    $sum_cty[9] += $items["sum"];
                    $totals_cty[9] += $items["total"];
                }
                if($items["type"] ==6){ // 99%
                    $sum_cty[10] += $items["sum"];
                    $totals_cty[10] += $items["total"];
                }
                if($items["type"] ==7){ // VT
                    $sum_cty[11] += $items["sum"];
                    $totals_cty[11] += $items["total"];
                }

            }
            if($items["flag"] ==2){
                if($items["type"] ==3){
                    if($items["productsid"] ==3 && $items["is_apple"] ==0){ // điện thoại khác
                        $sum_hnam[1] += $items["sum"];
                        $totals_hnam[1] += $items["total"];
                    }
                    if($items["productsid"] ==3 && $items["is_apple"] ==1){ // điện thoại apple
                        $sum_hnam[2] += $items["sum"];
                        $totals_hnam[2] += $items["total"];
                    }
                    if($items["productsid"] ==4){ // Phụ kiện
                        $sum_hnam[3] += $items["sum"];
                        $totals_hnam[3] += $items["total"];
                    }
                    if($items["productsid"] ==5 && $items["is_apple"] ==0){ // MTB khác
                        $sum_hnam[4] += $items["sum"];
                        $totals_hnam[4] += $items["total"];
                    }
                    if($items["productsid"] ==5 && $items["is_apple"] ==1){ // MTB Apple
                        $sum_hnam[5] += $items["sum"];
                        $totals_hnam[5] += $items["total"];
                    }
                    
                    if($items["productsid"] ==6){ // Laptop
                        $sum_hnam[7] += $items["sum"];
                        $totals_hnam[7] += $items["total"];
                    }
                    if($items["productsid"] ==8){ // Đồng hồ thông minh
                        $sum_hnam[8] += $items["sum"];
                        $totals_hnam[8] += $items["total"];
                    }
                }
                if($items["type"] ==4){ // Cũ
                    $sum_hnam[6] += $items["sum"];
                    $totals_hnam[6] += $items["total"];
                }
                if($items["type"] ==5){ // Demo
                    $sum_hnam[9] += $items["sum"];
                    $totals_hnam[9] += $items["total"];
                }
                if($items["type"] ==6){ // 99%
                    $sum_hnam[10] += $items["sum"];
                    $totals_hnam[10] += $items["total"];
                }
                if($items["type"] ==7){ // VT
                    $sum_hnam[11] += $items["sum"];
                    $totals_hnam[11] += $items["total"];
                }
            }
        }
        
        $this->view->sum_cty = $sum_cty;
        $this->view->totals_cty = $totals_cty;
        
        $this->view->sum_hnam = $sum_hnam;
        $this->view->totals_hnam = $totals_hnam;
        $this->view->idregency = $idregency;
        $list_store = $_zwfuser->getListByUname(FALSE);
        $this->view->list_store = $list_store;
        $this->view->storeid = $storeid;
}
public function thuhoiAction(){
    $this->_helper->Layout()->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);
    $_option = Business_Addon_Options::getInstance();
    $idregency = (int)$this->_identity["idregency"];
    $storeid = (int)$this->_identity["parentid"];
    $bgd=0;
    if($_option->isBGD($idregency) || $idregency ==35 || $idregency ==36 || $idregency ==33){
        $storeid = $this->_request->getParam("storeid");
        if($storeid ==0){
            $storeid =12;
        }
        $this->view->storeid = $storeid;
        $bgd = 1;
    }
    $month = (int)$this->_request->getParam("month");
    if($month ==0){
        $month = date('m');
    }
    $this->view->month = $month;
    $year = (int)$this->_request->getParam("year");
    if($year ==0){
        $year = date('Y');
    }
    $this->view->year = $year;
    if($month <10){
        $month = "0".$month;
    }
    $date_from              = $year."-".$month."-01";
    $date_to                = $year."-".$month."-".date('t', mktime(0, 0, 0, $month, 1, $year))." 23:59:59";
    $this->get_total_mcu($storeid,$date_from,$date_to);
    header("location:".$_SERVER['HTTP_REFERER']);
    exit();
}

public function listAction(){
        $idregency = (int)$this->_identity["idregency"];
        $storeid = (int)$this->_identity["parentid"];
        $_target = Business_Common_Target::getInstance();
        $_option = Business_Addon_Options::getInstance();
        $_zwfuser = Business_Common_Users::getInstance();
        $_users_products = Business_Addon_UsersProducts::getInstance();
        $bgd=0;
        
        if($_option->isBGD($idregency) || $idregency ==35 || $idregency ==36 || $idregency ==33){
            $storeid = $this->_request->getParam("storeid");
            
            if($storeid ==0){
                $storeid =12;
            }
            $this->view->storeid = $storeid;
            $bgd = 1;
        }
        if($storeid ==443){
            $this->_helper->viewRenderer('list-vivo');
        }
        $this->view->bgd = $bgd;
        $list_store = $_zwfuser->getListByUname(FALSE);
        $this->view->list_store = $list_store;
        
        $money_cty = array();
        $total_cty = array();
        $money_hnam = array();
        $total_hnam = array();
        $__id_cty = array();
        $__id_hnam = array();
        $often_cty = array();
        $often_hnam = array();
        for ($i = 1; $i <= 12; $i++) {
            $months[] = $i;
        }
        $this->view->months = $months;
        
        for ($i = 2014; $i <= 2020; $i++) {
            $years[] = $i;
        }
        $this->view->years = $years;
        
        $month = (int)$this->_request->getParam("month");
        if($month ==0){
            $month = date('m');
        }
        $this->view->month = $month;
        $year = (int)$this->_request->getParam("year");
        if($year ==0){
            $year = date('Y');
        }
        $this->view->year = $year;
        
        $list = $_target->getList($month, $year,$storeid);
        foreach ($list as &$items){
            if($items["flag"] ==1){
                $money_cty[$items["type"]] = $items["money"];
                $total_cty[$items["type"]] = $items["total"];
                $__id_cty[$items["type"]] = $items["id"];
                $often_cty[$items["type"]] = $items["often"];
            }
            if($items["flag"] ==2){
                $money_hnam[$items["type"]] = $items["money"];
                $total_hnam[$items["type"]] = $items["total"];
                $__id_hnam[$items["type"]] = $items["id"];
                $often_hnam[$items["type"]] = $items["often"];
            }
        }
        $this->view->money_cty = $money_cty;
        $this->view->total_cty = $total_cty;
        
        $this->view->money_hnam = $money_hnam;
        $this->view->total_hnam = $total_hnam;
        
        $this->view->id_cty = $__id_cty;
        $this->view->id_hnam = $__id_hnam;
        
        $this->view->often_cty = $often_cty;
        $this->view->often_hnam = $often_hnam;
        $_ptype      = $_option->getCateTargetHnamNew();
        $this->view->ptype = $_ptype;
        
        // chi nhánh bán
        $sum_cty= array();
        $totals_cty = array();
        $sum_hnam= array();
        $totals_hnam = array();
        if($month <10){
            $month = "0".$month;
        }
        $date_from              = $year."-".$month."-01";
        $date_to                = $year."-".$month."-".date('t', mktime(0, 0, 0, $month, 1, $year))." 23:59:59";
        $data = $_users_products->get_target($date_from, $date_to, $storeid);
        foreach ($data as $items){
            if($items["flag"] ==1){ // công ty
                if($items["type"] ==3){
                    if($items["productsid"] ==3 && $items["is_apple"] ==0){ // điện thoại khác
                        $sum_cty[1] += $items["sum"];
                        $totals_cty[1] += $items["total"];
                    }
                    if($items["productsid"] ==3 && $items["is_apple"] ==1){ // điện thoại apple
                        $sum_cty[2] += $items["sum"];
                        $totals_cty[2] += $items["total"];
                    }
                    if($items["productsid"] ==4){ // Phụ kiện
                        $sum_cty[3] += $items["sum"];
                        $totals_cty[3] += $items["total"];
                    }
                    if($items["productsid"] ==5 && $items["is_apple"] ==0){ // MTB khác
                        $sum_cty[4] += $items["sum"];
                        $totals_cty[4] += $items["total"];
                    }
                    if($items["productsid"] ==5 && $items["is_apple"] ==1){ // MTB Apple
                        $sum_cty[5] += $items["sum"];
                        $totals_cty[5] += $items["total"];
                    }
                    if($items["productsid"] ==6){ // Laptop
                        $sum_cty[7] += $items["sum"];
                        $totals_cty[7] += $items["total"];
                    }
                    if($items["productsid"] ==8){ // Đồng hồ thông minh
                        $sum_cty[8] += $items["sum"];
                        $totals_cty[8] += $items["total"];
                    }
                }
                if($items["type"] ==4){ // Cũ
                    $sum_cty[6] += $items["sum"];
                    $totals_cty[6] += $items["total"];
                }
                if($items["type"] ==5){ // Demo
                    $sum_cty[9] += $items["sum"];
                    $totals_cty[9] += $items["total"];
                }
                if($items["type"] ==6){ // 99%
                    $sum_cty[10] += $items["sum"];
                    $totals_cty[10] += $items["total"];
                }
                if($items["type"] ==7){ // VT
                    $sum_cty[11] += $items["sum"];
                    $totals_cty[11] += $items["total"];
                }
                
            }
            if($items["flag"] ==2){
                if($items["type"] ==3){
                    if($items["productsid"] ==3 && $items["is_apple"] ==0){ // điện thoại khác
                        $sum_hnam[1] += $items["sum"];
                        $totals_hnam[1] += $items["total"];
                    }
                    if($items["productsid"] ==3 && $items["is_apple"] ==1){ // điện thoại apple
                        $sum_hnam[2] += $items["sum"];
                        $totals_hnam[2] += $items["total"];
                    }
                    if($items["productsid"] ==4){ // Phụ kiện
                        $sum_hnam[3] += $items["sum"];
                        $totals_hnam[3] += $items["total"];
                    }
                    if($items["productsid"] ==5 && $items["is_apple"] ==0){ // MTB khác
                        $sum_hnam[4] += $items["sum"];
                        $totals_hnam[4] += $items["total"];
                    }
                    if($items["productsid"] ==5 && $items["is_apple"] ==1){ // MTB Apple
                        $sum_hnam[5] += $items["sum"];
                        $totals_hnam[5] += $items["total"];
                    }
                    if($items["productsid"] ==6){ // Laptop
                        $sum_hnam[7] += $items["sum"];
                        $totals_hnam[7] += $items["total"];
                    }
                    if($items["productsid"] ==8){ // Đồng hồ thông minh
                        $sum_hnam[8] += $items["sum"];
                        $totals_hnam[8] += $items["total"];
                    }
                }
                if($items["type"] ==4){ // Cũ
                    $sum_hnam[6] += $items["sum"];
                    $totals_hnam[6] += $items["total"];
                }
                if($items["type"] ==5){ // Demo
                    $sum_hnam[9] += $items["sum"];
                    $totals_hnam[9] += $items["total"];
                }
                if($items["type"] ==6){ // 99%
                    $sum_hnam[10] += $items["sum"];
                    $totals_hnam[10] += $items["total"];
                }
                if($items["type"] ==7){ // VT
                    $sum_hnam[11] += $items["sum"];
                    $totals_hnam[11] += $items["total"];
                }
                
            }
        }
        
        $this->view->sum_cty = $sum_cty;
        $this->view->totals_cty = $totals_cty;
        
        $this->view->sum_hnam = $sum_hnam;
        $this->view->totals_hnam = $totals_hnam;
        
        
        //áp công thức.
        $lct = $_option->getCTTarget();
        foreach ($lct as $key=>$val){
            $cttg[$key]=$val;
        }
        $this->view->lct = $lct;
        $this->view->cttg = $cttg;
        
        
        
        
        $lnct = $_option->getNameCTTarget();
        $this->view->lnct = $lnct;
        
        $detail_recipe = Business_Addon_Recipe::getInstance()->getDetail($storeid, $month, $year);
        $this->view->detail_recipe = $detail_recipe;
//        echo "<pre>";
//        var_dump($lct);
//        die();
        $pp = (int)$this->_request->getParam("pp");
        if($pp==1){
            $total_thuhoitarget = $this->get_total_mcu($storeid,$date_from,$date_to);
            $this->view->total_thuhoitarget = $total_thuhoitarget;
//            echo "<pre>";
//            var_dump($total_thuhoitarget);
//            die();
        }
        
    }
    public function saveRecipeAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $month = $this->_request->getParam("month");
        $year = $this->_request->getParam("year");
        $storeid = $this->_request->getParam("storeid");
        $recipeid = $this->_request->getParam("recipeid");
        $_total_money = $this->_request->getParam("total_money");
        $_often_store = $this->_request->getParam("often_store");
        $total_money = str_replace(",", "", $_total_money);
        $_recipeid = Business_Addon_Recipe::getInstance();
        $ret = array();
        $arr = array();
        if($recipeid ==0){
            $arr["id"] = "recipeid";
            $arr["msg"] = "Thông báo!\nVui lòng chọn công thức áp dụng";
            $ret[] = $arr;
        }
        if(count($ret) >0){
            echo json_encode($ret);
        }else{
            $detail_recipe = $_recipeid->getDetail($storeid, $month, $year);
            $data["recipeid"] = $recipeid;
            $data["total_money"] = $total_money;
            $data["often_store"] = $_often_store;
            if($detail_recipe == null){
                $data["storeid"] = $storeid;
                $data["month"] = $month;
                $data["year"] = $year;
                $data["datetime"] = date('Y-m-d H:i:s');
                $data["creator"] = $this->_identity["username"];
                $_recipeid->insert($data);
            }else{
                $data["end_datetime"] = date('Y-m-d H:i:s');
                $data["end_creator"] = $this->_identity["username"];
                $_recipeid->update($detail_recipe["id"], $data);
            }
            
            $arr["id"] = "ok";
            $arr["msg"]="ok";
            $ret[] = $arr;
            echo json_encode($ret);
        }
    }
    public function saveRecipeRetrieveAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $id = $this->_request->getParam("id_recipe");
        $des_retrieve = $this->_request->getParam("des_retrieve");
        $_money_retrieve = $this->_request->getParam("money_retrieve");
        $money_retrieve = str_replace(",", "", $_money_retrieve);
        $_recipeid = Business_Addon_Recipe::getInstance();
        $detail = $_recipeid->getDetailById($id);
        $ret = array();
        $arr = array();
        if($detail == null){
            die('Not have access');
        }else{
            if($detail["status"] ==1){
                $arr["id"] = "id_recipe";
                $arr["msg"]="Bạn không thể thực hiện thao tác này, vì đã xác nhận rồi";
                $ret[] = $arr;
            }
        }
        if(count($ret) >0){
            echo json_encode($ret);
        }else{
            if($id >0){
               $data["money_retrieve"] = $money_retrieve;
                $data["des_retrieve"] = $des_retrieve;
                $_recipeid->update($id, $data); 
            }
            $arr["id"] = "ok";
            $arr["msg"]="ok";
            $ret[] = $arr;
            echo json_encode($ret);
        }
    }
    public function completeRetrieveAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $_recipeid = Business_Addon_Recipe::getInstance();
        $id = $this->_request->getParam("id");
        $data["status"] = 1;
        $_recipeid->update($id, $data); 
    }

    public function editAction(){
        $seckey ="TARGET2016HNAMMOBILE";
        $idregency = $this->_identity["idregency"];
        $_option = Business_Addon_Options::getInstance();
        $_zwfuser = Business_Common_Users::getInstance();
        $_cateproducts = Business_Common_CatedProducts::getInstance();
        $_target = Business_Common_Target::getInstance();
        
        if($_option->isBGD($idregency) || $idregency ==35 || $idregency ==36){
            $this->_helper->viewRenderer('admin-edit');
            $list_store = $_zwfuser->getListByUname(FALSE);
            $this->view->list_store =  $list_store;
        }else{
            $storeid = $this->_identity["parentid"];
            $listmb = $_zwfuser->getListByStoreid($storeid);
            $this->view->listmb = $listmb;
        }
        for ($i = 1; $i <= 12; $i++) {
            $months[] = $i;
        }
        $this->view->months = $months;
        $this->view->cur_month = date("m");
        
        $flag = $_cateproducts->getList();
        $this->view->flag = $flag;
        
        $list_type      = $_option->getCateTargetHnamNew();
        $this->view->list_type = $list_type;
        $id = (int)  $this->_request->getParam("id");
        $this->view->uid = $id;
//        echo "<pre>";
//        var_dump($id,$listmb);
//        die();
        $token = $this->_request->getParam("token");
        if($id > 0){
            $ztoken = md5($seckey.$id);
            if($token !=$ztoken){
                $this->_redirect('/admin/home');
            }
            $detail = $_target->getDetailById($id);
            $disabled            = 'readonly';
            $this->view->disabled = $disabled;
            $this->view->detail = $detail;
        }
        
        
    }
    
    public function delAction(){
            $this->_helper->Layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            $id = $this->_request->getParam("id");
            Business_Common_CtySales::getInstance()->deleteSalesById2($id);
        
    }

    public function saveAction(){
        if ($this->_request->isPost()) {
            $this->_helper->Layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            $_target = Business_Common_Target::getInstance();
            $_option = Business_Addon_Options::getInstance();
            $id = (int)($this->_request->getParam("id"));
            $isadmin = (int)($this->_request->getParam("isadmin"));
            $_often = $this->_request->getParam("often");
            $often = str_replace(",", ".", $_often);
            $storeid = $this->_request->getParam("storeid");
            $flag = $this->_request->getParam("flag");
            $this->view->idflag = $flag;
            $type = $this->_request->getParam("type");
            $month = $this->_request->getParam("month");
            $year = $this->_request->getParam("year");
            
            $_money = $this->_request->getParam("money");
            $money = str_replace(",", "", $_money);
            
            $_total = $this->_request->getParam("total");
            $total = str_replace(",", "", $_total);
            
            
            $ret = array();
           /*if ($money == 0) {
               $err['id'] = "money";
               $err['msg'] = "Số tiền không được để trống";
               $ret[] = $err;
           }
            if ($total ==0) {
               $err['id'] = "total";
               $err['msg'] = "Số lượng không được để trống";
               $ret[] = $err;
           }*/
            if($isadmin ==0){
                die('Không có quyền truy cập');
            }
            if ($storeid == 0) {
                $err['id'] = "storeid";
                $err['msg'] = "Chọn chi nhánh";
                $ret[] = $err;
            }
             if ($flag == 0) {
                $err['id'] = "flag";
                $err['msg'] = "Chọn nhóm sản phẩm";
                $ret[] = $err;
            }
             if ($type == 0) {
                $err['id'] = "type";
                $err['msg'] = "Lỗi.\nVui lòng chọn loại sản phẩm";
                $ret[] = $err;
            }
            if($id == 0){
                $list = $_target->checkList($month, $year,$storeid,$idmb=0,$flag,$type);
                if($list !=null){
                    $err['id'] = "storeid";
                    $err['msg'] = "Target của chi nhánh này đã được thêm.\nVui lòng chỉnh sửa ";
                    $ret[] = $err;
                }
                 
            }
            if (count($ret) > 0){
                echo json_encode($ret);
            }else{
                $data["money"] = $money;
                $data["total"] = $total;
                $data["actived"] = 1;
                $data["check"]=1;
                $data["idmb"]=0;
                $data["often"] =$often;
                if($id == 0){
                    $data["month"]=$month;
                    $data["year"]=$year;
                    $data["storeid"] = $storeid;
                    $data["type"] = $type;
                    $data["flag"] = $flag;
                    $data["creator"]= $this->_identity["username"];
                    $data["datetime"]= date('Y-m-d H:i:s');
                    $_target->insert($data);
                }
                else{
                    $detail = $_target->getDetailById($id);
                    $data["creator_end"]= $this->_identity["username"];
                    $data["datetime_end"]=date('Y-m-d H:i:s');
                    $_target->update($id, $data);
                    if($data["total"] != $detail["total"] || $data["money"] != $detail["money"] || $data["often"] !=$detail["often"]){
                        Business_Common_HistoryTarget::getInstance()->insert($detail);// lich su
                    }
                }
                Business_Addon_Options::getInstance()->syncAll('event');
                $err['id'] = "ok";
                $err['msg'] = "ok";
                $ret[] = $err;
                echo json_encode($ret);
            }
            
            
        }
    }
    public function save2Action(){
        if ($this->_request->isPost()) {
            $this->_helper->Layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            
            $_target = Business_Common_Target::getInstance();
            $storeid = $this->_identity["parentid"];
            $id = (int)($this->_request->getParam("id"));
            $idmb = (int)$this->_request->getParam("idmb");
            $flag = $this->_request->getParam("flag");
            $this->view->idflag = $flag;
            $type = $this->_request->getParam("type");
            $month = $this->_request->getParam("month");
            $year = $this->_request->getParam("year");
            
            $_money = $this->_request->getParam("money");
            $money = str_replace(",", "", $_money);
            
            $_total = $this->_request->getParam("total");
            $total = str_replace(",", "", $_total);
            
            $ret = array();
            
           if ($money == 0) {
               $err['id'] = "money";
               $err['msg'] = "Số tiền không được để trống";
               $ret[] = $err;
           }
            if ($total ==0) {
               $err['id'] = "total";
               $err['msg'] = "Số lượng không được để trống";
               $ret[] = $err;
           }
            
                if ($idmb == 0) {
                    $err['id'] = "idmb";
                    $err['msg'] = "Chọn tên nhân viên set target";
                    $ret[] = $err;
                }
                 if ($flag == 0) {
                    $err['id'] = "flag";
                    $err['msg'] = "Chọn nhóm sản phẩm";
                    $ret[] = $err;
                }
                 if ($type == 0) {
                    $err['id'] = "type";
                    $err['msg'] = "Lỗi.\nVui lòng chọn loại sản phẩm";
                    $ret[] = $err;
                }
            if($id == 0){
                $list = $_target->checkList($month, $year,$storeid,$idmb,$flag,$type);
                if($list !=null){
                    $err['id'] = "idmb";
                    $err['msg'] = "Target của nhân viên này đã được thêm.\nVui lòng chỉnh sửa ";
                    $ret[] = $err;
                }
                 
            }
            if (count($ret) > 0){
                echo json_encode($ret);
                return;
            }else{
                $data["money"] = $money;
                $data["total"] = $total;
                $data["actived"] = 1;
                $data["check"]=1;
                if($id == 0){
                    $data["storeid"] = $storeid;
                    $data["idmb"]=$idmb;
                    $data["month"]=$month;
                    $data["year"]=$year;
                    $data["type"] = $type;
                    $data["flag"] = $flag;
                    $data["creator"]= $this->_identity["username"];
                    $data["datetime"]= date('Y-m-d H:i:s');
                    $data["often"] = 0;
                    $_target->insert($data);
                }
                else{
                    $detail = $_target->getDetailById($id);
                    $data["creator_end"]= $this->_identity["username"];
                    $data["datetime_end"]=date('Y-m-d H:i:s');
                    $_target->update($id, $data);
                    if($data["total"] != $detail["total"] || $data["money"] != $detail["money"] || $data["often"] !=$detail["often"]){
                        Business_Common_HistoryTarget::getInstance()->insert($detail);// lich su
                    }
                }
                Business_Addon_Options::getInstance()->syncAll('event');
                $err['id'] = "ok";
                $err['msg'] = "ok";
                $ret[] = $err;
                echo json_encode($ret);
            }
            
            
        }
    }

}
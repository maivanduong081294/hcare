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
        
        
        
        
        $date_from              = $year."-".$month."-01";
        $date_to                = date("Y-m-t", strtotime($date_from));
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
        
        if($idregency ==11 || $idregency ==14){
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
        
        $_ptype      = $_option->getCateTargetHnamNew();
        $this->view->ptype = $_ptype;
        
        
        
        
        // nhân viên chi nhánh bán
        $sum_cty= array();
        $totals_cty = array();
        $sum_hnam= array();
        $totals_hnam = array();
        
        
        $data = $_users_products->getListTargetByMb($date_from, $date_to, $storeid,$idmb);
        foreach ($data as $items){
            if($items["flag"] ==1){ // công ty
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
                if($items["type"] ==4){ // Cũ
                    $sum_cty[6] += $items["sum"];
                    $totals_cty[6] += $items["total"];
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
            if($items["flag"] ==2){
                if($items["productsid"] ==3 && $items["is_apple"] ==0){ // điện thoại khác
                    $sum_hnam[1] += $items["sum"];
                    $totals_hnam[1] += $items["total"];
                }
                if($items["productsid"] ==3 && $items["is_apple"] ==1){ // điện thoại apple
                    $sum_hnam[2] += $items["sum"];
                    $totals_hnam[2] += $items["total"];
                }
                if($items["productsid"] ==4){ // Phụ kiện
                    $sum_cty[3] += $items["sum"];
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
                if($items["type"] ==4){ // Cũ
                    $sum_hnam[6] += $items["sum"];
                    $totals_hnam[6] += $items["total"];
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
        }
        
        $this->view->sum_cty = $sum_cty;
        $this->view->totals_cty = $totals_cty;
        
        $this->view->sum_hnam = $sum_hnam;
        $this->view->totals_hnam = $totals_hnam;
}

    public function listAction(){
        $idregency = (int)$this->_identity["idregency"];
        $storeid = (int)$this->_identity["parentid"];
        $_target = Business_Common_Target::getInstance();
        $_option = Business_Addon_Options::getInstance();
        $_zwfuser = Business_Common_Users::getInstance();
        $_users_products = Business_Addon_UsersProducts::getInstance();
        if($_option->isBGD($idregency)){
            $storeid = $this->_request->getParam("storeid");
            $this->_helper->viewRenderer('admin-list');
            if($storeid ==0){
                $storeid =12;
            }
            $this->view->storeid = $storeid;
        }
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
        
        $date_from              = $year."-".$month."-01";
        $date_to                = date("Y-m-t", strtotime($date_from));
        $data = $_users_products->getListTargetByStoreid($date_from, $date_to, $storeid);
        foreach ($data as $items){
            if($items["flag"] ==1){ // công ty
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
                if($items["type"] ==4){ // Cũ
                    $sum_cty[6] += $items["sum"];
                    $totals_cty[6] += $items["total"];
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
            if($items["flag"] ==2){
                if($items["productsid"] ==3 && $items["is_apple"] ==0){ // điện thoại khác
                    $sum_hnam[1] += $items["sum"];
                    $totals_hnam[1] += $items["total"];
                }
                if($items["productsid"] ==3 && $items["is_apple"] ==1){ // điện thoại apple
                    $sum_hnam[2] += $items["sum"];
                    $totals_hnam[2] += $items["total"];
                }
                if($items["productsid"] ==4){ // Phụ kiện
                    $sum_cty[3] += $items["sum"];
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
                if($items["type"] ==4){ // Cũ
                    $sum_hnam[6] += $items["sum"];
                    $totals_hnam[6] += $items["total"];
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
        }
        
        $this->view->sum_cty = $sum_cty;
        $this->view->totals_cty = $totals_cty;
        
        $this->view->sum_hnam = $sum_hnam;
        $this->view->totals_hnam = $totals_hnam;
    }

public function editAction(){
        $seckey ="TARGET2016HNAMMOBILE";
        $idregency = $this->_identity["idregency"];
        $_option = Business_Addon_Options::getInstance();
        $_zwfuser = Business_Common_Users::getInstance();
        $_cateproducts = Business_Common_CatedProducts::getInstance();
        $_target = Business_Common_Target::getInstance();
        
        if($_option->isBGD($idregency)){
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
        $token = $this->_request->getParam("token");
        if($id > 0){
            $ztoken = md5($seckey.$id);
            if($token !=$ztoken){
                $this->_redirect('/admin/home');
            }
            $detail = $_target->getDetailById($id);
            $disabled            = 'disabled="true"';
            $this->view->disabled = $disabled;
        }
        
        $this->view->detail = $detail;
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
            $id = (int)($this->_request->getParam("id"));
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
            if($id == 0){
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
            if($id == 0){
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
                $list = $_target->checkList($month, $year,$storeid,$idmb,$flag,$type);
                if($list !=null){
                    $err['id'] = "idmb";
                    $err['msg'] = "Target của nhân viên này đã được thêm.\nVui lòng chỉnh sửa ";
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
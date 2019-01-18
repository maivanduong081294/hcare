<?php

class User_Admin_UsersController extends Zend_Controller_Action {

    private $_user_business = null;
    private $_default_active = "1";
    private $_identity;
    private $_plist = array(
        "1" => "Đã kích hoạt",
        "0" => "Hết hiệu lực",
    );
    public function init() {
        // do something
        BlockManager::setLayout('appbh');
        $auth = Zend_Auth::getInstance();
        $identity = (array) $auth->getIdentity();
        $this->_identity = $identity;
        $username = $identity["username"];
        $this->view->username = $username;
        $this->view->full_name = $identity["fullname"];
    }
    public function countAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true); 
        $data1 = array();
    }
    public function delAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true); 
        $id = (int)$this->_request->getParam("id");
        $token = $this->_request->getParam("token");
        $date = date('Ymd');
        $ztoken = md5("eluong2018".$id.$date);
        if($token != $ztoken){
            die('Not Access');
        }
        $data["enabled"]=0;
        $__bs = Business_Addon_Luong::getInstance();
        $__bs->update($id, $data);
        header("location:".$_SERVER['HTTP_REFERER']);
        exit();
    }
    public function editLuongAction(){
        $__option = Business_Addon_Options::getInstance();
        $id = (int)$this->_request->getParam("id");
        $token = $this->_request->getParam("token");
        $this->view->token = $token;
        $this->view->id = $id;
        $date = date('Ymd');
        $ztoken = md5("eluong2018".$id.$date);
        $idregency = $this->_identity["idregency"];
        $bgd = 0;
        if($__option->isBGD($idregency)){
            $bgd=1;
        }
        if($bgd==0){
            if($token != $ztoken){
                die('Not Access');
            }
        }
        if($id>0){
            $__bs = Business_Addon_Luong::getInstance();
            $detail = $__bs->get_detail($id);
        }
        $this->view->detail = $detail;
        if ($this->_request->isPost()) {
            $data_frm = $this->_request->getParams("data_frm");
            $ret = array();
            if($detail==NULL){
                $arr["id"] = "fullname";
                $arr["msg"] = "Nhân viên này không có thực. Vui lòng kiểm tra lại";
                $ret[] = $arr;
            }
            if($data_frm["fullname"]==NULL){
                $arr["id"] = "fullname";
                $arr["msg"] = "Vui lòng nhập tiêu đề";
                $ret[] = $arr;
            }else{
               if(strlen($data_frm["fullname"]) <6){
                    $arr["id"] = "fullname";
                    $arr["msg"] = "Vui lòng nhập họ tên";
                    $ret[] = $arr;
                } 
            }
            
            $from = str_replace("/", "-", $data_frm["from"]);
            $__from       =   date('Y/m/d',  strtotime($from));
            if(strtotime($__from) ===FALSE){
                $err['id'] = "from";
                $err['msg'] = "Vui lòng nhập ngày hợp lệ";
                $ret[] = $err;
            }
            $to = str_replace("/", "-", $data_frm["to"]);
            $__to       =   date('Y/m/d',  strtotime($to));
            if(strtotime($__to) ===FALSE){
                $err['id'] = "from";
                $err['msg'] = "Vui lòng nhập ngày hợp lệ";
                $ret[] = $err;
            }
            $create_date = str_replace("/", "-", $data_frm["create_date"]);
            $__create_date       =   date('Y/m/d',  strtotime($create_date));
            if(strtotime($__create_date) ===FALSE){
                $err['id'] = "create_date";
                $err['msg'] = "Vui lòng nhập ngày hợp lệ";
                $ret[] = $err;
            }

            
            if(count($ret) >0){
                for($i=0;$i<count($ret);$i++){
                    $msg = $ret[$i]['msg'];
                    $ids = $ret[$i]['id'];
                    echo "<script>window.parent.show_msg('$msg','$ids');</script>";
                    die();
                }
            }else{
                $data["gio_tangca"] = trim($data_frm["gio_tangca"]);
                $data["note"] = trim($data_frm["note"]);
                $data["ngay_cong"] = trim($data_frm["ngay_cong"]);
                $data["fullname"] = trim($data_frm["fullname"]);
                $data["stk"] = stripslashes($data_frm["stk"]);
                $data["regency"] = $data_frm["regency"];
                $data["storename"] = $data_frm["storename"];
                $data["from"] = date('Y-m-d',  strtotime($__from));
                $data["to"] = date('Y-m-d',  strtotime($__to));
                $data["create_date"] = date('Y-m-d',  strtotime($__create_date));
                $data["tham_nien"] = $data_frm["tham_nien"];
                $data["luong"] = str_replace(",", "", $data_frm["luong"]);
                $data["trach_nhiem"] = str_replace(",", "", $data_frm["trach_nhiem"]);
                $data["co_ban"] = str_replace(",", "", $data_frm["co_ban"]);
                $data["thanh_tien"] = str_replace(",", "", $data_frm["thanh_tien"]);
                $data["luong_bhxh"] = str_replace(",", "", $data_frm["luong_bhxh"]);
                $data["tien_tangca"] = str_replace(",", "", $data_frm["tien_tangca"]);
                $data["tam_ung"] = str_replace(",", "", $data_frm["tam_ung"]);
                $data["giam_tru_bhxh"] = str_replace(",", "", $data_frm["giam_tru_bhxh"]);
                $data["thuc_lanh"] = str_replace(",", "", $data_frm["thuc_lanh"]);
                $__bs->update($id,$data);
                $__msg ='Lưu thành công';
                $url ='/admin/user/users/listluong';
                echo "<script>window.parent.show_success('$__msg','','$url');</script>";
                die();
            }
        }
    }
    public function listluongAction(){
        $__bs = Business_Addon_Luong::getInstance();
        $username = $this->_identity["username"];
        $idregency = (int)  $this->_identity["idregency"];
        $month = (int)  $this->_request->getParam("month");
        if($month==0){
            $month=date('m');
        }
        $year = (int)  $this->_request->getParam("year");
        if($year==0){
            $year=date('Y');
        }
        $this->view->month = $month;
        $this->view->year = $year;
        $bgd=0;
        $__option = Business_Addon_Options::getInstance();
        if($__option->isBGD($idregency) || $idregency ==36){
            $username = $this->_request->getParam("uname");
            $bgd=1;
        }
        $list = $__bs->get_list($username,$month,$year);
        foreach ($list as $val){
            if($val["username"]=="hnam_quynhn" && $idregency !=36){
                continue;
            }
        }
        if($username=="hnam_quynhn" && $idregency !=36){
            $list = array();
        }
        $this->view->bgd = $bgd;
        $this->view->list = $list;
        $this->view->idregency = $idregency;
        $this->view->username = $username;
    }
    
    public function uploadAction(){
        if ($this->_request->isPost()){
            $_option = Business_Addon_Options::getInstance();
            $path = $_option->getPath();
            $expensions = array("xls","xlsx");
            $check_upload = $_option->upload($path, $expensions);
            $ret = array();
            if($check_upload[0]["msg"] !="ok"){
                foreach ($check_upload as $item){
                  $ret[] = $item;  
                }
            }else{
                $files_path = $check_upload[0]["files_path"];
                $file_ext = $check_upload[0]["file_ext"];
            }
            if(count($ret) >0){
                for($i=0;$i<count($ret);$i++){
                    $msg = $ret[$i]['msg'];
                    $ids = $ret[$i]['id'];
                    echo "<script>window.parent.notification('$msg','$ids');</script>";
                    return;
                }
            }else{ 
                if($_column ==0){
                    $_column =1;
                }
                $col = $_column -1;
                if($file_ext == "xls"){
                   $col =  $_column;
                }
                $postion_username = $col;
                $postion_from =$col+1;
                $postion_to = $col+2;
                $postion_fullname = $col+3;
                $postion_stk = $col+4;
                $postion_regency = $col+5;
                $postion_storename = $col+6;
                $postion_create_date = $col+7;
                $postion_tham_nien = $col+8;
                $postion_luongbxh = $col+9;
                $postion_luong = $col+10;
                $postion_trach_nhiem = $col+11;
                $postion_co_ban = $col+12;
                $postion_ngay_cong = $col+13;
                $postion_thanh_tien = $col+14;
                $postion_gio_tangca = $col+15;
                $postion_tien_tangca = $col+16;
                $postion_tam_ung = $col+17;
                $postion_giam_tru_bhxh = $col+18;
                $postion_thuc_lanh = $col+19;
                $postion_note = $col+20;
                ini_set('display_errors', '1');
                if($file_ext == "xlsx"){
                    include BASE_PATH.'/simplexlsx.class.php';
                    $xlsx = new SimpleXLSX($files_path);
                    $data = $xlsx->rows();
                    foreach ($data as $key=> $items){
                        if($key<4){
                            continue;
                        }
                        if($items[$postion_username] != NULL){
                            $slist["username"] = $items[$postion_username];
                            $slist["from"] = $items[$postion_from];
                            $slist["to"] = $items[$postion_to];
                            $slist["fullname"] = $items[$postion_fullname];
                            $slist["stk"] = $items[$postion_stk];
                            $slist["regency"] = $items[$postion_regency];
                            $slist["storename"] = $items[$postion_storename];
                            $slist["create_date"] = $items[$postion_create_date];
                            $slist["tham_nien"] = $items[$postion_tham_nien];
                            $slist["luong"] = $items[$postion_luong];
                            $slist["luong_bhxh"] = $items[$postion_luongbxh];
                            $slist["trach_nhiem"] = $items[$postion_trach_nhiem];
                            $slist["co_ban"] = $items[$postion_co_ban];
                            $slist["ngay_cong"] = $items[$postion_ngay_cong];
                            $slist["thanh_tien"] = $items[$postion_thanh_tien];
                            $slist["note"] = $items[$postion_note];
                            $slist["gio_tangca"] = $items[$postion_gio_tangca];
                            $slist["tien_tangca"] = $items[$postion_tien_tangca];
                            $slist["tam_ung"] = $items[$postion_tam_ung];
                            $slist["giam_tru_bhxh"] = $items[$postion_giam_tru_bhxh];
                            $slist["thuc_lanh"] = $items[$postion_thuc_lanh];
                            $sdata[] = $slist;
                        }
                    }
                }else{
                    if($file_ext == "xls"){
                        include BASE_PATH.'/excel_reader2.php';
                        $data = new Spreadsheet_Excel_Reader($files_path);
                       for($i=0;$i<500;$i++){
                           if($i<5){
                               continue;
                           }
                           if($data->val($i,$postion_username) != NULL){
                                $slist["username"] = $data->val($i,$postion_username);
                                $slist["from"] = $data->val($i,$postion_from);
                                $slist["to"] = $data->val($i,$postion_to);
                                $slist["fullname"] = $data->val($i,$postion_fullname);
                                $slist["stk"] = $data->val($i,$postion_stk);
                                $slist["regency"] = $data->val($i,$postion_regency);
                                $slist["storename"] = $data->val($i,$postion_storename);
                                $slist["create_date"] = $data->val($i,$postion_create_date);
                                $slist["tham_nien"] = $data->val($i,$postion_tham_nien);
                                $slist["luong"] = $data->val($i,$postion_luong);
                                $slist["luong_bhxh"] = $data->val($i,$postion_luongbxh);
                                $slist["trach_nhiem"] = $data->val($i,$postion_trach_nhiem);
                                $slist["co_ban"] = $data->val($i,$postion_co_ban);
                                $slist["ngay_cong"] = $data->val($i,$postion_ngay_cong);
                                $slist["thanh_tien"] = $data->val($i,$postion_thanh_tien);
                                $slist["note"] = $data->val($i,$postion_note);
                                $slist["gio_tangca"] = $data->val($i,$postion_gio_tangca);
                                $slist["tien_tangca"] = $data->val($i,$postion_tien_tangca);
                                $slist["tam_ung"] = $data->val($i,$postion_tam_ung);
                                $slist["giam_tru_bhxh"] = $data->val($i,$postion_giam_tru_bhxh);
                                $slist["thuc_lanh"] = $data->val($i,$postion_thuc_lanh);
                                $sdata[] = $slist; 
                           }
                       }
                    }
                }
                
                $datetime = date("Y-m-d H:i:s");
                foreach($sdata as $val){
                    $val["enabled"] = 1;
                    $from = str_replace("/", "-", $val["from"]);
                    $__from = date('Y-m-d',  strtotime($from));
                    $val["from"] = $__from;
                    
                    $to = str_replace("/", "-", $val["to"]);
                    $__to = date('Y-m-d',  strtotime($to));
                    $val["to"] = $__to;
                    
                    $create_date = str_replace("/", "-", $val["create_date"]);
                    $__create_date = date('Y-m-d',  strtotime($create_date));
                    $val["create_date"] = $__create_date;
                    $luong = $val["luong"];
                    $luong = str_replace(",", "", $luong);
                    $luong = str_replace(".", "", $luong);
                    $val["luong"] = $luong;
                    
                    $trach_nhiem = $val["trach_nhiem"];
                    $trach_nhiem = str_replace(",", "", $trach_nhiem);
                    $trach_nhiem = str_replace(".", "", $trach_nhiem);
                    $val["trach_nhiem"] = $trach_nhiem;
                    
                    $co_ban = $val["co_ban"];
                    $co_ban = str_replace(",", "", $co_ban);
                    $co_ban = str_replace(".", "", $co_ban);
                    $val["co_ban"] = $co_ban;
                    
                    $thanh_tien = $val["thanh_tien"];
                    $thanh_tien = str_replace(",", "", $thanh_tien);
                    $thanh_tien = str_replace(".", "", $thanh_tien);
                    $val["thanh_tien"] = $thanh_tien;
                    
                    $val["creator"] = $this->_identity["username"];
                    $val["creator_id"] = $this->_identity["userid"];
                    $val["datetime"] = $__to." ".date("H:i:s");
                    Business_Addon_Luong::getInstance()->insert($val);
                }
                
                echo "<script>window.parent.completes('Upload thành công');</script>";
            }
        }
    }

    public function getListByCostidAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $_cost_detail = Business_Addon_CostDetail::getInstance();
        $costid = $this->_request->getParam("costid");
        $list = $_cost_detail->getListByCostId($costid);
        echo json_encode($list);
    }
    public function printBillsAction(){
        $id = (int)$this->_request->getParam("id");
        $_bills = Business_Addon_Bills::getInstance();
        $_option = Business_Addon_Options::getInstance();
        $detail = $_bills->getDetail($id);
        if($detail ==Null){
            $this->_redirect('/admin/user/users/add-bills');
        }
        $this->view->detail = $detail;
        $_department = Business_Addon_Department::getInstance();
        $ldepartment = $_department->getDetailById($detail["departmentid"]);
        $this->view->name_department = $ldepartment["name"];
        
        $_zwfuser = Business_Common_Users::getInstance();
        if($detail["departmentid"] == 10){
           $detail_user = $_zwfuser->getDetail($detail["storeid"]);
           $p = $this->_request->getParam("p");
           if($p==1){
               echo "<pre>";
               var_dump($detail["storeid"],$detail_user);
               die();
           }
            $name_regency = $detail_user["storename"];
        }else{
            if($detail["storeid"] ==0){
               $name_regency = "Tất cả"; 
            }else{
                $_regency = Business_Addon_Regency::getInstance();
                $lregency = $_regency->getDetailById($detail["storeid"]);
                $name_regency = $lregency["name"];
            }
        }
        
        $this->view->name_regency = $name_regency;
        $this->view->vnd = $_option->VndText($detail["money"]);
        
    }
    public function printReceiptsAction(){
        $id = (int)$this->_request->getParam("id");
        $_bills = Business_Addon_Receipts::getInstance();
        $_option = Business_Addon_Options::getInstance();
        $detail = $_bills->getDetail($id);
        if($detail ==Null){
            $this->_redirect('/admin/user/users/add-bills');
        }
        $this->view->detail = $detail;
        $_department = Business_Addon_Department::getInstance();
        $ldepartment = $_department->getDetailById($detail["departmentid"]);
        $this->view->name_department = $ldepartment["name"];
        
        $_zwfuser = Business_Common_Users::getInstance();
        if($detail["departmentid"] == 10){
           $detail_user = $_zwfuser->getDetail($detail["storeid"]); 
            $name_regency = $detail_user["storename"];
        }else{
            if($detail["storeid"] ==0){
               $name_regency = "Tất cả"; 
            }else{
                $_regency = Business_Addon_Regency::getInstance();
                $lregency = $_regency->getDetailById($detail["storeid"]);
                $name_regency = $lregency["name"];
            }
        }
        
        $this->view->name_regency = $name_regency;
        $this->view->vnd = $_option->VndText($detail["money"]);
        
    }
    public function printDelegateAction(){
        $id = (int)$this->_request->getParam("id");
        $_delegate = Business_Addon_Delegate::getInstance();
        $_option = Business_Addon_Options::getInstance();
        $detail = $_delegate->getDetail($id);
        if($detail ==Null){
            $this->_redirect('/admin/user/users/add-delegate');
        }
        $this->view->detail = $detail;
        $_department = Business_Addon_Department::getInstance();
        $ldepartment = $_department->getDetailById($detail["departmentid"]);
        $this->view->name_department = $ldepartment["name"];
        
        $_zwfuser = Business_Common_Users::getInstance();
        if($detail["departmentid"] == 10){
           $detail_user = $_zwfuser->getDetail($detail["storeid"]); 
            $name_regency = $detail_user["storename"];
        }else{
            if($detail["storeid"] ==0){
               $name_regency = "Tất cả"; 
            }else{
                $_regency = Business_Addon_Regency::getInstance();
                $lregency = $_regency->getDetailById($detail["storeid"]);
                $name_regency = $lregency["name"];
            }
        }
        
        $this->view->name_regency = $name_regency;
        $this->view->vnd = $_option->VndText($detail["money"]);
        
    }

    public function addBillsAction(){
        $_option = Business_Addon_Options::getInstance();
        $_zwfuser = Business_Common_Users::getInstance();
        $_bills = Business_Addon_Bills::getInstance();
        $_cost = Business_Addon_Cost::getInstance();
        $_cost_detail = Business_Addon_CostDetail::getInstance();
        $_department = Business_Addon_Department::getInstance();
        $__regency = Business_Addon_Regency::getInstance();
        $lregency = $__regency->getList();
        $name_regency = array();
        foreach ($lregency as $items){
            $name_regency[$items["id"]] = $items["name"];
        }
        $this->view->lregency = $lregency;
        $this->view->name_regency = $name_regency;
        $idregency = $this->_identity["idregency"];
        if($idregency ==37){
            if($this->_identity["username"] != "hnam_thaottt" ){
                $this->_redirect('/admin');
            }
        }
        
        $lformality = $_option->getformality();
        $this->view->lformality = $lformality;
        $lstore = $_zwfuser->getListByUname(FALSE);
        
        
        $id = $this->_request->getParam("id");
        $month = $this->_request->getParam("month");
        if($month ==NULL){
            $month =date('m');
        }
        $year = (int)$this->_request->getParam("year");
        if($year ==0){
            $year =date('Y');
        }
        $this->view->month = $month;
        $this->view->year = $year;
        for ($i = 1; $i <= 12; $i++) {
            $months[] = $i;
        }
        $this->view->months = $months;
        
        for ($i = 2014; $i <= date('Y') + 5; $i++) {
            $years[] = $i;
        }
        $this->view->years = $years;
        $id_departments = (int)$this->_request->getParam("departments");
        $this->view->id_departments = $id_departments;
        $id_storeid = (int)$this->_request->getParam("storeids");
        $this->view->id_storeid = $id_storeid;
        $list = $_bills->getList($id_departments,$id_storeid,$month,$year);
//        foreach ($list as $items){
//            if(date('Y',  strtotime($items["bill_datetime"])) == date('Y') &&  date('m',  strtotime($items["bill_datetime"])) == date('m')){
//                $s[] = $items["id"];
//            }
//        }
        $this->view->list = $list;
        $number_pc = count($list) + 1;
        if($id >0){
            $detail = $_bills->getDetail($id);
            if($detail ==Null){
                $this->_redirect('/admin/user/users/add-bills');
            }
            $this->view->detail = $detail;
            $number_pc = $detail["number_pc"];
            
            $lcost_detail = $_cost_detail->getListByCostId($detail["costid"]);
            $this->view->lcost_detail = $lcost_detail;
            
        }
        $this->view->number_pc = $number_pc;
        $storename = array();
        foreach ($lstore as $items){
            $storename[$items["userid"]] = $items["storename"];
        }
        $this->view->storename = $storename;
        $this->view->lstore = $lstore;
        $lcost = $_cost->getList();
        $this->view->lcost = $lcost;
        $name_cost = array();
        foreach ($lcost as $items){
            $name_cost[$items["id"]] = $items["name"];
        }
        $this->view->name_cost = $name_cost;
        
        
        $__lcost_detail = $_cost_detail->getList();
        $this->view->list_cost_details = $__lcost_detail;
        $name_cost_detail = array();
        foreach ($__lcost_detail as $items){
            $name_cost_detail[$items["id"]] = $items["name"];
        }
        $this->view->name_cost_detail = $name_cost_detail;
        
        
        $lbank = $_option->getChargeCardBank();
        $this->view->lbank = $lbank;
        
        //list phòng ban
        $ldepartment = $_department->getList();
        $this->view->ldepartment = $ldepartment;
        $name_department = array();
        foreach ($ldepartment as $items){
            $name_department[$items["id"]] = $items["name"];
        }
        $this->view->name_department = $name_department;
        
        $title ="PC".date('mY');
        SEOPlugin::setTitle($title);
    }
    public function addReceiptsAction(){
        $_option = Business_Addon_Options::getInstance();
        $_zwfuser = Business_Common_Users::getInstance();
        $_bills = Business_Addon_Receipts::getInstance();
        $_cost = Business_Addon_Cost::getInstance();
        $_cost_detail = Business_Addon_CostDetail::getInstance();
        $_department = Business_Addon_Department::getInstance();
        $__regency = Business_Addon_Regency::getInstance();
        $lregency = $__regency->getList();
        $name_regency = array();
        foreach ($lregency as $items){
            $name_regency[$items["id"]] = $items["name"];
        }
        $this->view->lregency = $lregency;
        $this->view->name_regency = $name_regency;
        $idregency = $this->_identity["idregency"];
        if($idregency ==37){
            if($this->_identity["username"] != "hnam_thaottt" ){
                $this->_redirect('/admin');
            }
        }
        $lformality = $_option->getformality();
        $this->view->lformality = $lformality;
        $lstore = $_zwfuser->getListByUname(FALSE);
        
        
        $id = $this->_request->getParam("id");
        $month = $this->_request->getParam("month");
        if($month ==NULL){
            $month =date('m');
        }
        $year = (int)$this->_request->getParam("year");
        if($year ==0){
            $year =date('Y');
        }
        $this->view->month = $month;
        $this->view->year = $year;
        for ($i = 1; $i <= 12; $i++) {
            $months[] = $i;
        }
        $this->view->months = $months;
        
        for ($i = 2014; $i <= date('Y') + 5; $i++) {
            $years[] = $i;
        }
        $this->view->years = $years;
        
        $id_departments = (int)$this->_request->getParam("departments");
        $this->view->id_departments = $id_departments;
        $id_storeid = (int)$this->_request->getParam("storeids");
        $this->view->id_storeid = $id_storeid;
        $list = $_bills->getList($id_departments,$id_storeid,$month,$year);
//        foreach ($list as $items){
//            if(date('Y',  strtotime($items["bill_datetime"])) == date('Y') &&  date('m',  strtotime($items["bill_datetime"])) == date('m')){
//                $s[] = $items["id"];
//            }
//        }
        $this->view->list = $list;
        $number_pc = count($list) + 1;
        if($id >0){
            $detail = $_bills->getDetail($id);
            if($detail ==Null){
                $this->_redirect('/admin/user/users/add-bills');
            }
            $this->view->detail = $detail;
            $number_pc = $detail["number_pc"];
            
            $lcost_detail = $_cost_detail->getListByCostId($detail["costid"]);
            $this->view->lcost_detail = $lcost_detail;
            
        }
        $this->view->number_pc = $number_pc;
        $storename = array();
        foreach ($lstore as $items){
            $storename[$items["userid"]] = $items["storename"];
        }
        $this->view->storename = $storename;
        $this->view->lstore = $lstore;
        $lcost = $_cost->getList();
        $this->view->lcost = $lcost;
        $name_cost = array();
        foreach ($lcost as $items){
            $name_cost[$items["id"]] = $items["name"];
        }
        $this->view->name_cost = $name_cost;
        
        $__lcost_detail = $_cost_detail->getList();
        $this->view->list_cost_details = $__lcost_detail;
        $name_cost_detail = array();
        foreach ($__lcost_detail as $items){
            $name_cost_detail[$items["id"]] = $items["name"];
        }
        $this->view->name_cost_detail = $name_cost_detail;
        
        $lbank = $_option->getChargeCardBank();
        $this->view->lbank = $lbank;
        
        //list phòng ban
        $ldepartment = $_department->getList();
        $this->view->ldepartment = $ldepartment;
        $name_department = array();
        foreach ($ldepartment as $items){
            $name_department[$items["id"]] = $items["name"];
        }
        $this->view->name_department = $name_department;
        
        $title ="PT".date('mY');
        SEOPlugin::setTitle($title);
    }
    public function addDelegateAction(){
        $_option = Business_Addon_Options::getInstance();
        $_zwfuser = Business_Common_Users::getInstance();
        $_bills = Business_Addon_Delegate::getInstance();
        $_cost = Business_Addon_Cost::getInstance();
        $_cost_detail = Business_Addon_CostDetail::getInstance();
        $_department = Business_Addon_Department::getInstance();
        $__regency = Business_Addon_Regency::getInstance();
        $lregency = $__regency->getList();
        $name_regency = array();
        foreach ($lregency as $items){
            $name_regency[$items["id"]] = $items["name"];
        }
        $this->view->lregency = $lregency;
        $this->view->name_regency = $name_regency;
        $idregency = $this->_identity["idregency"];
        if($idregency ==37){
            if($this->_identity["username"] != "hnam_thaottt" ){
                $this->_redirect('/admin');
            }
        }
        $lformality = $_option->getformality();
        $this->view->lformality = $lformality;
        $lstore = $_zwfuser->getListByUname(FALSE);
        
        
        $id = $this->_request->getParam("id");
        $month = $this->_request->getParam("month");
        if($month ==NULL){
            $month =date('m');
        }
        $year = (int)$this->_request->getParam("year");
        if($year ==0){
            $year =date('Y');
        }
        $this->view->month = $month;
        $this->view->year = $year;
        for ($i = 1; $i <= 12; $i++) {
            $months[] = $i;
        }
        $this->view->months = $months;
        
        for ($i = 2014; $i <= date('Y') + 5; $i++) {
            $years[] = $i;
        }
        $this->view->years = $years;
        
        $id_departments = (int)$this->_request->getParam("departments");
        $this->view->id_departments = $id_departments;
        $id_storeid = (int)$this->_request->getParam("storeids");
        $this->view->id_storeid = $id_storeid;
        $list = $_bills->getList($id_departments,$id_storeid,$month,$year);
//        foreach ($list as $items){
//            if(date('Y',  strtotime($items["bill_datetime"])) == date('Y') &&  date('m',  strtotime($items["bill_datetime"])) == date('m')){
//                $s[] = $items["id"];
//            }
//        }
        $this->view->list = $list;
        $number_pc = count($list) + 1;
        if($id >0){
            $detail = $_bills->getDetail($id);
            if($detail ==Null){
                $this->_redirect('/admin/user/users/add-bills');
            }
            $this->view->detail = $detail;
            $number_pc = $detail["number_pc"];
            
            $lcost_detail = $_cost_detail->getListByCostId($detail["costid"]);
            $this->view->lcost_detail = $lcost_detail;
            
        }
        $this->view->number_pc = $number_pc;
        $storename = array();
        foreach ($lstore as $items){
            $storename[$items["userid"]] = $items["storename"];
        }
        $this->view->storename = $storename;
        $this->view->lstore = $lstore;
        $lcost = $_cost->getList();
        $this->view->lcost = $lcost;
        $name_cost = array();
        foreach ($lcost as $items){
            $name_cost[$items["id"]] = $items["name"];
        }
        $this->view->name_cost = $name_cost;
        $lbank = $_option->getChargeCardBank();
        $this->view->lbank = $lbank;
        
        //list phòng ban
        $ldepartment = $_department->getList();
        $this->view->ldepartment = $ldepartment;
        $name_department = array();
        foreach ($ldepartment as $items){
            $name_department[$items["id"]] = $items["name"];
        }
        $this->view->name_department = $name_department;
        
        $title ="UNC".date('mY');
        SEOPlugin::setTitle($title);
    }
    public function delBillsAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $_bills = Business_Addon_Bills::getInstance();
        $id = (int)$this->_request->getParam("id");
        $data["enabled"] = 0;
        if($id > 0){
            $_bills->update($id,$data);
        }
    }
    public function delReceiptsAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $_bills = Business_Addon_Receipts::getInstance();
        $id = (int)$this->_request->getParam("id");
        $data["enabled"] = 0;
        if($id > 0){
            $_bills->update($id,$data);
        }
    }
    public function delDelegateAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $_bills = Business_Addon_Delegate::getInstance();
        $id = (int)$this->_request->getParam("id");
        $data["enabled"] = 0;
        if($id > 0){
            $_bills->update($id,$data);
        }
    }
    
    public function saveDelegateAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $_bills = Business_Addon_Delegate::getInstance();
        $id = (int)$this->_request->getParam("id");
        $name = $this->_request->getParam("name");
        $departmentid = $this->_request->getParam("department");
        $content = $this->_request->getParam("content");
        $bill_datetime = $this->_request->getParam("bill_datetime");
        $_money = $this->_request->getParam("money");
        $money = str_replace(",", "", $_money);
        $formality = (int)$this->_request->getParam("formality"); // hình thức
        $bank = (int) $this->_request->getParam("bank");
        $costid = (int)$this->_request->getParam("costid"); // phân loại chi phí
        $cost_detail = (int)$this->_request->getParam("cost_detail"); // chi tiết chi phí
        $storeid = (int)$this->_request->getParam("storeid");
        $bills_id = $this->_request->getParam("bills_id"); // mã hóa đơn
        $_dayout = $this->_request->getParam("dayout"); // ngày xuất hóa đơn
        $dayout = str_replace("/","-", $_dayout);
        $username = $this->_identity["username"];
        $datetime = date('Y-m-d H:i:s');
        $b_datetime = str_replace("/","-", $bill_datetime);
        
        $arr = array();
        $ret = array();
        
//        if($name ==NULL){
//            $arr["id"] ="name";
//            $arr["msg"] = "Vui lòng nhập tiêu đề";
//            $ret[] = $arr;
//        }
        if($bill_datetime ==NULL){
            $arr["id"] ="bill_datetime";
            $arr["msg"] = "Vui lòng nhập ngày chi tiền.";
            $ret[] = $arr;
        }else{
            $ism = (int) strpos($bill_datetime,"m");
            $isy = (int) strpos($bill_datetime,"y");
            if($ism >0){
                $arr["id"] ="bill_datetime";
                $arr["msg"] = "Vui lòng nhập ngày chi tiền (tháng).";
                $ret[] = $arr;
            }
            if($isy >0){
                $arr["id"] ="bill_datetime";
                $arr["msg"] = "Vui lòng nhập ngày chi tiền (năm).";
                $ret[] = $arr;
            }
        }
        if($_dayout !=NULL){
            $ism2 = (int) strpos($_dayout,"m");
            $isy2 = (int) strpos($_dayout,"y");
            if($ism2 >0){
                $arr["id"] ="dayout";
                $arr["msg"] = "Vui lòng nhập ngày xuất hóa đơn tiền (tháng).";
                $ret[] = $arr;
            }
            if($isy2 >0){
                $arr["id"] ="dayout";
                $arr["msg"] = "Vui lòng nhập ngày xuất hóa đơn tiền (năm).";
                $ret[] = $arr;
            }
        }

        if((int)$money ==0){
            $arr["id"] ="money";
            $arr["msg"] = "Vui lòng nhập số tiền";
            $ret[] = $arr;
        }
        if($formality ==0){
            $arr["id"] ="formality";
            $arr["msg"] = "Vui lòng chọn hình thức";
            $ret[] = $arr;
        }
        if($formality ==2){
            if($bank ==0){
                $arr["id"] ="bank";
                $arr["msg"] = "Vui lòng chọn ngân hàng";
                $ret[] = $arr;
            }
        }
//        if($costid ==0){
//            $arr["id"] ="costid";
//            $arr["msg"] = "Vui lòng chọn phân loại";
//            $ret[] = $arr;
//        }
//        if($cost_detail ==0){
//            $arr["id"] ="cost_detail";
//            $arr["msg"] = "Vui lòng chọn phân loại chi phí";
//            $ret[] = $arr;
//        }
        if($departmentid ==0){
            $arr["id"] ="department";
            $arr["msg"] = "Vui lòng chọn phòng ban..";
            $ret[] = $arr;
        }
        if($departmentid ==10){
            if($storeid ==0){
                $arr["id"] ="storeid";
                $arr["msg"] = "Vui lòng chọn chi nhánh..";
                $ret[] = $arr;
            }
        }
        if(count($ret)>0){
            echo json_encode($ret);
            return;
        }else{
            $data["name"] = $name;
            $data["departmentid"] = $departmentid;
            $data["bill_datetime"] = date('Y-m-d',  strtotime($b_datetime));
            $data["content"] = $content;
            $data["money"] = $money;
            $data["formality"] = $formality;
            if($formality ==1){
                $data["bank"] = 0;
            }else{
                $data["bank"] = $bank;
            }
            $data["costid"] = $costid;
            $data["cost_detail"] = $cost_detail;
            $data["storeid"] = $storeid;
            $data["bills_id"] = $bills_id;
            if($dayout !=null){
                if($dayout != "0000-00-00"){
                    $data["dayout"] = date('Y-m-d',  strtotime($dayout));
                }
                
            }else{
                $data["dayout"] ="0000-00-00";
            }
            
            
            if($id==0){
                $data["enabled"] = 1;
                $data["creator"] = $username;
                $data["datetime"] = $datetime;
                $_bills->insert($data);
            }else{
                $data["end_creator"] = $username;
                $data["end_datetime"] = $datetime;
                $_bills->update($id, $data);
            }
            $arr["id"] = "ok";
            $arr["msg"] = "ok";
            $ret[] = $arr;
            echo json_encode($ret);
        }
    }
    public function saveBillsAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $_bills = Business_Addon_Bills::getInstance();
        $id = (int)$this->_request->getParam("id");
        $number_pc = (int)$this->_request->getParam("number_pc");
        $name = $this->_request->getParam("name");
        $departmentid = $this->_request->getParam("department");
        $content = $this->_request->getParam("content");
        $bill_datetime = $this->_request->getParam("bill_datetime");
        $_money = $this->_request->getParam("money");
        $money = str_replace(",", "", $_money);
        $formality = (int)$this->_request->getParam("formality"); // hình thức
        $bank = (int) $this->_request->getParam("bank");
        $costid = (int)$this->_request->getParam("costid"); // phân loại chi phí
        $cost_detail = (int)$this->_request->getParam("cost_detail"); // chi tiết chi phí
        $storeid = (int)$this->_request->getParam("storeid");
        $bills_id = $this->_request->getParam("bills_id"); // mã hóa đơn
        $_dayout = $this->_request->getParam("dayout"); // ngày xuất hóa đơn
        $dayout = str_replace("/","-", $_dayout);
        $username = $this->_identity["username"];
        $datetime = date('Y-m-d H:i:s');
        $b_datetime = str_replace("/","-", $bill_datetime);
        
        $arr = array();
        $ret = array();
        
//        if($name ==NULL){
//            $arr["id"] ="name";
//            $arr["msg"] = "Vui lòng nhập tiêu đề";
//            $ret[] = $arr;
//        }
        if($bill_datetime ==NULL){
            $arr["id"] ="bill_datetime";
            $arr["msg"] = "Vui lòng nhập ngày chi tiền.";
            $ret[] = $arr;
        }else{
            $ism = (int) strpos($bill_datetime,"m");
            $isy = (int) strpos($bill_datetime,"y");
            if($ism >0){
                $arr["id"] ="bill_datetime";
                $arr["msg"] = "Vui lòng nhập ngày chi tiền (tháng).";
                $ret[] = $arr;
            }
            if($isy >0){
                $arr["id"] ="bill_datetime";
                $arr["msg"] = "Vui lòng nhập ngày chi tiền (năm).";
                $ret[] = $arr;
            }
        }
        if($_dayout !=NULL){
            $ism2 = (int) strpos($_dayout,"m");
            $isy2 = (int) strpos($_dayout,"y");
            if($ism2 >0){
                $arr["id"] ="dayout";
                $arr["msg"] = "Vui lòng nhập ngày xuất hóa đơn tiền (tháng).";
                $ret[] = $arr;
            }
            if($isy2 >0){
                $arr["id"] ="dayout";
                $arr["msg"] = "Vui lòng nhập ngày xuất hóa đơn tiền (năm).";
                $ret[] = $arr;
            }
        }

        if((int)$money ==0){
            $arr["id"] ="money";
            $arr["msg"] = "Vui lòng nhập số tiền";
            $ret[] = $arr;
        }
        if($formality ==0){
            $arr["id"] ="formality";
            $arr["msg"] = "Vui lòng chọn hình thức";
            $ret[] = $arr;
        }
        if($formality ==2){
            if($bank ==0){
                $arr["id"] ="bank";
                $arr["msg"] = "Vui lòng chọn ngân hàng";
                $ret[] = $arr;
            }
        }
//        if($costid ==0){
//            $arr["id"] ="costid";
//            $arr["msg"] = "Vui lòng chọn phân loại";
//            $ret[] = $arr;
//        }
//        if($cost_detail ==0){
//            $arr["id"] ="cost_detail";
//            $arr["msg"] = "Vui lòng chọn phân loại chi phí";
//            $ret[] = $arr;
//        }
        if($departmentid ==0){
            $arr["id"] ="department";
            $arr["msg"] = "Vui lòng chọn phòng ban..";
            $ret[] = $arr;
        }
        if($departmentid ==10){
            if($storeid ==0){
                $arr["id"] ="storeid";
                $arr["msg"] = "Vui lòng chọn chi nhánh..";
                $ret[] = $arr;
            }
        }
        if($id==0){
            $m = date('m',  strtotime($b_datetime));
            $y = date('Y',  strtotime($b_datetime));
            $check = $_bills->getDetailByPC($number_pc,$m,$y);
            if($check !=NULL){
                $arr["id"] ="number_pc";
                $arr["msg"] = "Số phiếu chi này đã trùng.Vui lòng kiểm tra lại";
                $ret[] = $arr;
            }
        }
        
        if(count($ret)>0){
            echo json_encode($ret);
            return;
        }else{
            $data["name"] = $name;
            $data["number_pc"] = $number_pc;
            $data["departmentid"] = $departmentid;
            $data["bill_datetime"] = date('Y-m-d',  strtotime($b_datetime));
            $data["content"] = $content;
            $data["money"] = $money;
            $data["formality"] = $formality;
            if($formality ==1){
                $data["bank"] = 0;
            }else{
                $data["bank"] = $bank;
            }
            $data["costid"] = $costid;
            $data["cost_detail"] = $cost_detail;
            $data["storeid"] = $storeid;
            $data["bills_id"] = $bills_id;
            if($dayout !=null){
                if($dayout != "0000-00-00"){
                    $data["dayout"] = date('Y-m-d',  strtotime($dayout));
                }
                
            }else{
                $data["dayout"] ="0000-00-00";
            }
            
            
            if($id==0){
                $data["enabled"] = 1;
                $data["creator"] = $username;
                $data["datetime"] = $datetime;
                $_bills->insert($data);
            }else{
                $data["end_creator"] = $username;
                $data["end_datetime"] = $datetime;
                $_bills->update($id, $data);
            }
            $arr["id"] = "ok";
            $arr["msg"] = "ok";
            $ret[] = $arr;
            echo json_encode($ret);
        }
    }
    public function saveReceiptsAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $_bills = Business_Addon_Receipts::getInstance();
        $id = (int)$this->_request->getParam("id");
        $number_pc = (int)$this->_request->getParam("number_pc");
        $name = $this->_request->getParam("name");
        $departmentid = $this->_request->getParam("department");
        $content = $this->_request->getParam("content");
        $bill_datetime = $this->_request->getParam("bill_datetime");
        $_money = $this->_request->getParam("money");
        $money = str_replace(",", "", $_money);
        $formality = (int)$this->_request->getParam("formality"); // hình thức
        $bank = (int) $this->_request->getParam("bank");
        $costid = (int)$this->_request->getParam("costid"); // phân loại chi phí
        $cost_detail = (int)$this->_request->getParam("cost_detail"); // chi tiết chi phí
        $storeid = (int)$this->_request->getParam("storeid");
        $bills_id = $this->_request->getParam("bills_id"); // mã hóa đơn
        $_dayout = $this->_request->getParam("dayout"); // ngày xuất hóa đơn
        $dayout = str_replace("/","-", $_dayout);
        $username = $this->_identity["username"];
        $datetime = date('Y-m-d H:i:s');
        $b_datetime = str_replace("/","-", $bill_datetime);
        
        $arr = array();
        $ret = array();
        
//        if($name ==NULL){
//            $arr["id"] ="name";
//            $arr["msg"] = "Vui lòng nhập tiêu đề";
//            $ret[] = $arr;
//        }
        if($bill_datetime ==NULL){
            $arr["id"] ="bill_datetime";
            $arr["msg"] = "Vui lòng nhập ngày chi tiền.";
            $ret[] = $arr;
        }else{
            $ism = (int) strpos($bill_datetime,"m");
            $isy = (int) strpos($bill_datetime,"y");
            if($ism >0){
                $arr["id"] ="bill_datetime";
                $arr["msg"] = "Vui lòng nhập ngày chi tiền (tháng).";
                $ret[] = $arr;
            }
            if($isy >0){
                $arr["id"] ="bill_datetime";
                $arr["msg"] = "Vui lòng nhập ngày chi tiền (năm).";
                $ret[] = $arr;
            }
        }
        if($_dayout !=NULL){
            $ism2 = (int) strpos($_dayout,"m");
            $isy2 = (int) strpos($_dayout,"y");
            if($ism2 >0){
                $arr["id"] ="dayout";
                $arr["msg"] = "Vui lòng nhập ngày xuất hóa đơn tiền (tháng).";
                $ret[] = $arr;
            }
            if($isy2 >0){
                $arr["id"] ="dayout";
                $arr["msg"] = "Vui lòng nhập ngày xuất hóa đơn tiền (năm).";
                $ret[] = $arr;
            }
        }

        if((int)$money ==0){
            $arr["id"] ="money";
            $arr["msg"] = "Vui lòng nhập số tiền";
            $ret[] = $arr;
        }
        if($formality ==0){
            $arr["id"] ="formality";
            $arr["msg"] = "Vui lòng chọn hình thức";
            $ret[] = $arr;
        }
        if($formality ==2){
            if($bank ==0){
                $arr["id"] ="bank";
                $arr["msg"] = "Vui lòng chọn ngân hàng";
                $ret[] = $arr;
            }
        }
//        if($costid ==0){
//            $arr["id"] ="costid";
//            $arr["msg"] = "Vui lòng chọn phân loại";
//            $ret[] = $arr;
//        }
//        if($cost_detail ==0){
//            $arr["id"] ="cost_detail";
//            $arr["msg"] = "Vui lòng chọn phân loại chi phí";
//            $ret[] = $arr;
//        }
        if($departmentid ==0){
            $arr["id"] ="department";
            $arr["msg"] = "Vui lòng chọn phòng ban..";
            $ret[] = $arr;
        }
        if($departmentid ==10){
            if($storeid ==0){
                $arr["id"] ="storeid";
                $arr["msg"] = "Vui lòng chọn chi nhánh..";
                $ret[] = $arr;
            }
        }
        if($id==0){
            $m = date('m',  strtotime($b_datetime));
            $y = date('Y',  strtotime($b_datetime));
            $check = $_bills->getDetailByPC($number_pc,$m,$y);
            if($check !=NULL){
                $arr["id"] ="number_pc";
                $arr["msg"] = "Số phiếu thu  này đã trùng.Vui lòng kiểm tra lại";
                $ret[] = $arr;
            }
        }
        if(count($ret)>0){
            echo json_encode($ret);
            return;
        }else{
            $data["name"] = $name;
            $data["number_pc"] = $number_pc;
            $data["departmentid"] = $departmentid;
            $data["bill_datetime"] = date('Y-m-d',  strtotime($b_datetime));
            $data["content"] = $content;
            $data["money"] = $money;
            $data["formality"] = $formality;
            if($formality ==1){
                $data["bank"] = 0;
            }else{
                $data["bank"] = $bank;
            }
            $data["costid"] = $costid;
            $data["cost_detail"] = $cost_detail;
            $data["storeid"] = $storeid;
            $data["bills_id"] = $bills_id;
            if($dayout !=null){
                if($dayout != "0000-00-00"){
                    $data["dayout"] = date('Y-m-d',  strtotime($dayout));
                }
                
            }else{
                $data["dayout"] ="0000-00-00";
            }
            
            
            if($id==0){
                $data["enabled"] = 1;
                $data["creator"] = $username;
                $data["datetime"] = $datetime;
//                echo "<pre>";
//                var_dump($id,$data);
//                die();
                $_bills->insert($data);
            }else{
                $data["end_creator"] = $username;
                $data["end_datetime"] = $datetime;
                $_bills->update($id, $data);
            }
            $arr["id"] = "ok";
            $arr["msg"] = "ok";
            $ret[] = $arr;
            echo json_encode($ret);
        }
    }

    

    public function detailCheckInAction(){
        $_option = Business_Addon_Options::getInstance();
        $_checkin = Business_Addon_CheckIn::getInstance();
        $zwf_user = Business_Common_Users::getInstance();
        $userid = $this->_identity["userid"];
        $idregency = $this->_identity["idregency"];
        $bgd = 0;
        if($_option->isBGD($idregency) || $idregency ==35 || $idregency ==36){
            $bgd =1;
            $userid = (int)$this->_request->getParam("userid");
            if($userid ==0){
                $userid = $this->_identity["userid"];
            }
        }
        $this->view->bgd = $bgd;
        for($i=1;$i<=12;$i++){
            $months[] =$i;
        }
        $this->view->months = $months;
        for($i=2015;$i<=date('Y')+5;$i++){
            $years[] = $i;
        }
        $this->view->years = $years;
        
        $month = (int)$this->_request->getParam("month");
        if($month ==0){
            $month = date('m');
        }
//        if($month >0 && $month <10){
//            $month = "0".$month;
//        }
        $year = (int)$this->_request->getParam("year");
        if($year == 0){
            $year = date('Y');
        }
        $number = date('t', mktime(0, 0, 0, $month, 1, $year));
        $this->view->number = $number;
        $list = $_checkin->getListByMonthYear($userid,$month, $year);
        $this->view->month = $month;
        $this->view->year = $year;
        $this->view->list = $list;
        $address_checkin = array();
        $address_checkout = array();
        $location = array();
        $username = $list[0]["username"];
        $time_checkIn = array();
        $time_checkOut = array();
        foreach ($list as $items){
            $day = (int)date('d',  strtotime($items["datetime"]));
            if($items["type"]==1){
                $address_checkin[$day] = $items["address"];
                $time_checkIn[$day] = date('H:i:s',  strtotime($items["datetime"]));
            }
            if($items["type"]==2){
                $address_checkout[$day] = $items["address"];
                $time_checkOut[$day] = date('H:i:s',  strtotime($items["datetime"]));
            }
            if($items["location"] ==1){
                $location[$day] = "Công ty";
            }
            if($items["location"] ==2){
                $location[$day] = "Ngoài Công ty";
            }
            
            
        }
        $this->view->time_checkin = $time_checkIn;
        $this->view->time_checkout = $time_checkOut;
        $this->view->userid = $userid;
        $this->view->location = $location;
        $this->view->address_checkin = $address_checkin;
        $this->view->address_checkout = $address_checkout;
        $this->view->uname = $username;
        $luser = $zwf_user->getListUser("",0,1,0);;
        $this->view->luser = $luser;
        
    }

    public  function getPathCheckIn($createdate) {
        $lastChar = strrchr(BASE_PATH, "/");     
        if ($lastChar !== "/") {
            $basePath = BASE_PATH . "/";
        }
        $basePath = $basePath . "uploads/checkin";
        $ts = strtotime($createdate);
        $year = date("Y", $ts);
        $month = date("m",$ts);
        $day = date("d",$ts);
       
        return sprintf("%s/%s/%s/%s", $basePath, $year, $month, $day);
    }
    public function captureCheckInAction() {
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        if ($this->_request->isPost()) {
            $userid = $this->_identity["userid"];
            $createdate = date('Y-m-d');
            $imageSrc = $this->_request->getParam("imageSrc","");
            $retName = $this->_request->getParam("type",0);
            $uname = $this->_identity["username"];
            //write to jpg
            $res = array();
            if($uname == null){
                $err['id']  = "snap";
                $err['msg'] = "Lỗi !\nVui lòng đăng nhập hệ thống.";
                $res[]      = $err;
                return;
            }
            if (count($res) > 0) {
                echo json_encode($res);
                return;
            }else{
                if (strlen($imageSrc) > 5000) {

                    $_file = Business_Helpers_File::getInstance();
                    $ext = "png";

                    $datas = explode(",", $imageSrc);

                    $imageSrc= base64_decode($datas[1]);

                    $fileUploaded = $_file->data2File2($imageSrc, $ext);                

                    $path = $this->getPathCheckIn($createdate);

                    if (!is_dir($path)) {
                        mkdir($path, 0777, true);
                    }

                    $newFile = $path . "/$userid-" . $retName . "." . $ext;
    //                var_dump($newFile);exit();
                    $ret = Business_Helpers_Image::resizeImage($fileUploaded, $newFile, 1000, 1000);
//                    if ($ret==true) {
//                        echo $itemid."-".$retName . "." . $ext;
//                    } else {
//                        echo "";
//                    }
                    $err['id'] = "ok";
                    $err['msg'] = "ok";
                    $res[] = $err;
                    echo json_encode($res);
                }
            }
        }
    }
    public function checkInAction(){
        $userid = (int)$this->_identity["userid"];
        if($userid ==0){
            $this->_redirect('/admin');
        }
        $keys = (int)  $this->_request->getParam("keys");
        if($keys !=1){
            $url = 'https://app.hnammobile.com/admin/user/users/check-in?&keys=1';
            $this->_redirect($url);
        }
        $this->view->f_name = $this->_identity["fullname"];
        $_option = Business_Addon_Options::getInstance();
        $_location =$_option->getLocation();
        $this->view->location = $_location;
        
        $this->view->userid = $userid;
        $createdate = date('Y-m-d');
        $path = $this->getPathCheckIn($createdate);
        $this->view->link   = $path."/".$userid."png";
        
    }

    public function saveCheckInAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true); 
        $_checkIn = Business_Addon_CheckIn::getInstance();
        $userid = $this->_identity["userid"];
        $location = $this->_request->getParam("location");
        $des = $this->_request->getParam("des");
        $address = $this->_request->getParam("adr");
        $type = (int)$this->_request->getParam("type");
        $curIP = Business_Common_Utils::getCurrentIP();
        $ret = array();
        $now = date('Y-m-d');
        if($type ==0){
            $err['msg'] = "Thông báo !\nVui lòng lựa chọn loại.";
            $err['id'] = "type";
            $ret[] = $err;
        }
        if($location ==0){
            $err['msg'] = "Thông báo !\nVui lòng chọn địa điểm check in.";
            $err['id'] = "location";
            $ret[] = $err;
        }
        if($location ==2){
            if($des ==NULL){
                $err['msg'] = "Thông báo !\nVui lòng nhập mô tả sơ lược địa điểm check in.";
                $err['id'] = "des";
                $ret[] = $err; 
            }
                
        }
        if($address ==null){
                $err['msg'] = "Thông báo !\nVui lòng chụp hình nơi bạn check in.";
                $err['id'] = "snap";
                $ret[] = $err;
            }
            $detail = $_checkIn->getDetailUserIdByDay($userid, $now,$location,$type);
            if($detail != null){
                $err['msg'] = "Thông báo !\nHôm nay Bạn đã check in rồi.";
                $err['id'] = "snap";
                $ret[] = $err; 
            }
        if(count($ret)>0){
            echo json_encode($ret);
            return;
        }else{
            $data["location"] = $location;
            $data["userid"] = $userid;
            $data["username"] = $this->_identity["username"];
            $data["des"] = $des;
            $data["datetime"] = date('Y-m-d H:i:s');
            $data["enabled"] = 1;
            $data["address"] = $address;
            $data["img"] = $userid."-$type".".png";
            $data["ips"] = $curIP;
            $data["type"] = $type;
            $_checkIn->insert($data);
            $err['id'] = "ok";
            $err['msg'] = "ok";
            $ret[] = $err;
            echo json_encode($ret);
        }
    }

    public function editIpsAction(){
        Business_Addon_Options::getInstance()->syncAll('event');
        $userid = $this->_request->getParam("userid");
        $ips = $this->_request->getParam("ips");
        $_ip = explode(",",$ips);
        echo "<pre>";
        var_dump($userid,$_ip);
        die();
    }
    public function luongAction(){
        $userid = $this->_identity["userid"];
        $this->view->userid = $userid;
        $id = $this->_request->getParam("id");
        $_department = Business_Addon_Department::getInstance();
        $_regency_department = Business_Addon_RegencyDepartment::getInstance();
        $_option = Business_Addon_Options::getInstance();
        $_regency = Business_Addon_Regency::getInstance();
        $_payroll = Business_Common_Payroll::getInstance();
        $_zwfuser = Business_Common_Users::getInstance();
        $idregency = $this->_identity["idregency"];
        $uid = $this->_request->getParam("userid");
        $list_us = $_zwfuser->getList();
        foreach ($list_us as $items){
            $fullname[$items["userid"]] = $items["fullname"];
            $zidregency[$items["userid"]] = $items["idregency"];
        }
        if($_option->isBGD($idregency) || $idregency==36){
            $userid = $this->_request->getParam("userid");
        }
        if($idregency==36){ // nhân sự không coi được lương của các trưởng phòng
            if($_option->isBGD($zidregency[$userid]) || $zidregency[$userid] ==28 || $zidregency[$userid]==31 || $zidregency[$userid] ==32 || $zidregency[$userid]==34 || $zidregency[$userid]==38 || $zidregency[$userid] ==43 || $uid==4040){
                $userid = $this->_identity["userid"]; 
//                $this->_redirect('/admin/user/users/list-user');
            }
        }
        
        
        
        $list_department = $_department->getList();
        $this->view->list_department = $list_department;
        $list_regency = $_regency->getList();
        $regency = array();
        foreach ($list_regency as $items) {
            $regency[$items["id"]] = $items["name"];
            $items["id_regency"] = $items["id"];
        }
        $this->view->list_regency = $list_regency;
        
        $list = $_payroll->getListByUserId($userid);
        if (!empty($list)) {
                foreach ($list as &$items) {
                    $items["name_regency"] ='';
                    $items["fullname"] = $fullname[$items["userid"]];
                    $items["startdate2"] = date('d/m/Y' ,  strtotime($items["startdate"]));
                    $items["enddate2"] = date('d/m/Y' ,  strtotime($items["enddate"]));
                    if($items["now"] ==1){
                        $items["now2"] = "Có";
                        $items["enddate2"] = "Hiện tại";
                        $items["enddate"] = date('d/m/Y');
                    }else{
                        if($items["now"] ==0){
                            $items["now2"] = "Không";
                        }
                    }
                    $items["idregency"] = (int)$items["idregency"];
                    if($items["idregency"] >0){
                        $items["name_regency"] = $regency[$items["idregency"]];
                    }
                }
            }
            
            for($i=0;$i<count($list);$i++){
                $start = $list[$i]["startdate"];
                $end = $list[$i+1]["startdate"];
                if ($end == null || $end == "") {
                    $list[$i]["end"] = "Hiện tại";
                }else {
                    $list[$i]["end"] = date('d/m/Y' ,  strtotime($end));
                }
                
            }
            $this->view->list = $list;
    }

    public function getDayOffAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $_dayoff = Business_Common_Dayoff::getInstance();
        $month = $this->_request->getParam("month");
        $year = $this->_request->getParam("year");
        $day = $this->_request->getParam("day");
        $userid = $this->_request->getParam("userid");
        if($day <10){
            $day = "0".$day;
        }
        if($month <10){
            $month = "0".$month;
        }
        $date = $year."-".$month."-".$day;
        $detail = $_dayoff->getDetailByDay($userid,$date);
        if($detail == null){
            $err['msg'] = "insert";
            $ret[] = $err;
            echo json_encode($ret);
        }else{
            if($detail["enabled"] ==0){
                $err['msg'] = "insert";
                $ret[] = $err;
            }else{
                if($detail["enabled"] ==2){
                    $err['msg'] = "update2"; // chưa duyệt
                    $ret[] = $err;
                }else{
                    $err['msg'] = "update"; // duyệt
                    $ret[] = $err;
                }
                
                
            }
           echo json_encode($ret); 
        }
    }

    public function saveDayOffAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $_dayoff = Business_Common_Dayoff::getInstance();
        $day = (int)$this->_request->getParam("day");
        $month = (int)$this->_request->getParam("month");
        $year = (int)$this->_request->getParam("year");
        $half = (int)$this->_request->getParam("half");
        $userid = (int) $this->_request->getParam("userid");
        $storeid = (int)  $this->_identity["parentid"];
        
        if($storeid ==0){
            $storeid = $this->_identity["idregency"];
        }
        if($day <10){
            $day = "0".$day;
        }
        if($month <10){
            $month = "0".$month;
        }
        $date = $year."-".$month."-".$day;
        $detail = $_dayoff->getDetailByDay($userid,$date);
        $ret = array();
        if (count($ret) > 0){
            echo json_encode($ret);
            return;
        }else{
            if($detail == null){
                $data["userid"] = $userid;
                $data["date"] = $date;
                $data["datetime"] = date('Y-m-d H:i:s');
                $data["enabled"] = 2;
                $data["creator"] = $this->_identity["username"];
                $data["half"] = $half;
                $data["storeid"] = $storeid;
                $_dayoff->insert($data);
            }
            
            else{
                if($detail["enabled"] !=1){
                    if($detail["enabled"] ==2){
                        $data["enabled"]    = 0;

                    }else{
                        $data["enabled"]    = 2;
                        $data["half"]       = $half;
                    }
                    $data["datetime_end"] = date('Y-m-d H:i:s');
                    $data["creator_end"] = $this->_identity["username"];
                    $_dayoff->updatebyday($date, $data);
                }
            }
            Business_Addon_Options::getInstance()->syncAll('event');
            $err['id'] = "ok";
            $err['msg'] = "ok";
            $ret[] = $err;
            echo json_encode($ret);
        }
    }
    public function saveDayOff2Action(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $_dayoff = Business_Common_Dayoff::getInstance();
        $day = (int)$this->_request->getParam("day");
        $month = (int)$this->_request->getParam("month");
        $year = (int)$this->_request->getParam("year");
        $half = (int)$this->_request->getParam("half");
        $userid = (int) $this->_request->getParam("userid");
        $storeid = (int)  $this->_identity["parentid"];
        
        if($storeid ==0){
            $storeid = $this->_identity["idregency"];
        }
        if($day <10){
            $day = "0".$day;
        }
        if($month <10){
            $month = "0".$month;
        }
        $date = $year."-".$month."-".$day;
        $detail = $_dayoff->getDetailByDay($userid,$date);
        $ret = array();
        if (count($ret) > 0){
            echo json_encode($ret);
            return;
        }else{
            if($detail == null){
                $data["userid"] = $userid;
                $data["date"] = $date;
                $data["datetime"] = date('Y-m-d H:i:s');
                $data["enabled"] = 1;
                $data["creator"] = $this->_identity["username"];
                $data["half"] = $half;
                $data["storeid"] = $storeid;
                $_dayoff->insert($data);
            }else{
                if($detail["enabled"] ==1){
                    $data["enabled"]    = 0;
                }else{
                    $data["enabled"]    = 1;
                    $data["half"]       = $half;
                }
                $data["datetime_end"] = date('Y-m-d H:i:s');
                $data["creator_end"] = $this->_identity["username"];
                $_dayoff->updatebyday($date, $data);
            }
            Business_Addon_Options::getInstance()->syncAll('event');
            $err['id'] = "ok";
            $err['msg'] = "ok";
            $ret[] = $err;
            echo json_encode($ret);
        }
    }

    public function testdayAction(){
        
    }

        public function dayOffAction(){
        $uid = $this->_identity["userid"];
        $idregency = $this->_identity["idregency"];
        $seckey ="DAYOFFHNAM2016";
        $_option = Business_Addon_Options::getInstance();
        if($_option->isBGD($idregency) || $idregency ==11 || $idregency ==20 || $idregency ==31 || $idregency ==32|| $idregency ==35 || $idregency ==36){
            if($_option->isBGD($idregency)){
                $this->_helper->viewRenderer('admin-day-off');
            }
            $userid = (int)$this->_request->getParam("userid");
            if($userid ==0){
                $userid = $this->_identity["userid"];
            }else{
                if($idregency ==11 || $idregency ==20 || $idregency ==31 || $idregency ==32|| $idregency ==35 || $idregency ==36){
                    if($uid != $userid){
                        $this->_helper->viewRenderer('admin-day-off');
                    }
                }else{
                    $token = $this->_request->getParam("token");
                    $this->view->token = $token;
                    $ztoken = md5($seckey.$userid);
                    if($token != $ztoken){
                        $this->_redirect('/admin/home');
                    }
                }
                
            }
            
        }else{
            $userid = $this->_identity["userid"];
        }
        
        $_dayoff = Business_Common_Dayoff::getInstance();
        
        for($i=1;$i<=12;$i++){
            $months[] = $i;
        }
        $this->view->months = $months;
        
        for($i=2015;$i<=2020;$i++){
            $years[] = $i;
        }
        $this->view->years = $years;
        $month = (int)$this->_request->getParam("month");
        $year = (int)$this->_request->getParam("year");
        if($month ==0){
            $month = date('m');
        }
        if($year ==0){
            $year = date('Y');
        }
        
//        $number = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        
        $number = date('t', mktime(0, 0, 0, $month, 1, $year));
//        $number = $_option->getDayByMonthYear($month,$year);
//        var_dump($number);exit();
        $mondays = $_option->getAllDaysInAMonth($year, $month);
        foreach ($mondays as $d) {
            $_monday[] = $d->format('d');
        }
        $tuesdays = $_option->getAllDaysInAMonth($year, $month,'Tuesday');
        foreach ($tuesdays as $d) {
            $_tuesday[] = $d->format('d');
        }
        $wednesdays = $_option->getAllDaysInAMonth($year, $month,'Wednesday');
        foreach ($wednesdays as $d) {
            $_wednesday[] = $d->format('d');
        }
        $thursdays = $_option->getAllDaysInAMonth($year, $month,'Thursday');
        foreach ($thursdays as $d) {
            $_thursday[] = $d->format('d');
        }
        $fridays = $_option->getAllDaysInAMonth($year, $month,'Friday');
        foreach ($fridays as $d) {
            $_friday[] = $d->format('d');
        }
        $saturdays = $_option->getAllDaysInAMonth($year, $month,'Saturday');
        foreach ($saturdays as $d) {
            $_saturday[] = $d->format('d');
        }
        $sundays = $_option->getAllDaysInAMonth($year, $month,'Sunday');
        foreach ($sundays as $d) {
            $_sunday[] = $d->format('d');
        }
        
        $this->view->monday = $_monday;
        $this->view->tuesday = $_tuesday;
        $this->view->wednesday = $_wednesday;
        $this->view->thursday = $_thursday;
        $this->view->friday = $_friday;
        $this->view->saturday = $_saturday;
        $this->view->sunday = $_sunday;
        $this->view->name_month = date('F', mktime(0,0,0,$month));
        $this->view->year = $year;
        $this->view->month = $month;
        $this->view->userid = $userid;
        $this->view->number = $number;
        
        $list = $_dayoff->getListByDay($userid,$month,$year);
        foreach ($list as &$items){
            if($items["enabled"] ==1){
                if($items["half"] ==0){
                    $_dayfull[] = date('d',  strtotime($items["date"]));
                    ++$countfull;
                }else{
                    $_dayhalf[] = date('d',  strtotime($items["date"]));
                    ++$counthalf;
                }
            }else{
                if($items["half"] ==0){
                    $_daynofull[] = date('d',  strtotime($items["date"]));
                    ++$countnofull;
                }else{
                    $_daynohalf[] = date('d',  strtotime($items["date"]));
                    ++$countnohalf;
                }
                
            }
            
        }
        $this->view->dayfull = $_dayfull;
        $this->view->dayhalf = $_dayhalf;
        $this->view->dayoff = $countfull + $counthalf/2;
        
        $this->view->daynofull = $_daynofull;
        $this->view->daynohalf = $_daynohalf;
        $this->view->daynooff = $countnofull + $countnohalf/2;
        
        $zwfuser = Business_Common_Users::getInstance();
        $detail_user = $zwfuser->getDetailById($userid);
        $this->view->detail_user = $detail_user;
        
        $storename = array();
        $list_store = $zwfuser->getListByUname(FALSE);
        foreach ($list_store as $items){
            $storename[$items["userid"]] = $items["storename"];
        }
        $this->view->storename = $storename;
        
        $_payroll = Business_Common_Payroll::getInstance();
        $detail_payroll = $_payroll->getDetailByStart($userid);
        $detail_payroll_now = $_payroll->getDetailByStart($userid,$desc=1);
        $this->view->detail_payroll = $detail_payroll;
        $this->view->detail_payroll_now = $detail_payroll_now;
        
        $list_payroll = $_payroll->getListByUserId($userid);
        $startdate ='';
        $__idregency =0;
        foreach ($list_payroll as $__items){
            if($__idregency != $__items["idregency"]){
                $__idregency = $__items["idregency"];
                $startdate = $__items["startdate"];
            }
        }
        $this->view->startdate = $startdate;
        $this->view->idregency_now = $__idregency;
        
        $_regency = Business_Addon_Regency::getInstance();
        $list_regency = $_regency->getList();
        $name_regency = array();
        foreach ($list_regency as $items){
            $name_regency[$items["id"]] = $items["name"];
        }
        $this->view->name_regency = $name_regency;
        $time = strtotime('now') - strtotime($startdate);
        
        $stime = date('m',  $time);
        $this->view->stime = $stime;
        if($stime >=6){
            $this->view->color = 'red;color:#FFFFFF';
        }
        
        $tn = strtotime('now')-strtotime($detail_payroll["startdate"]);
        $mtn = date('m',$tn);
        $ytn = date('Y',$tn)-1970;
        $nbtn = (int)$ytn*12 + $mtn;
        $this->view->nbtn = $nbtn;
        $this->view->mtn = $mtn;
        $this->view->ytn = $ytn;
        
    }

    public function delPayrollAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $id = $this->_request->getParam("id");
        $_payroll = Business_Common_Payroll::getInstance();
        $data["enabled"] = 0;
        $_payroll->update($id, $data);
    }

    public function savePayrollAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $_payroll = Business_Common_Payroll::getInstance();
        $_option = Business_Addon_Options::getInstance();
        $_zwfuser = Business_Common_Users::getInstance();
        $id = (int)$this->_request->getParam("id");
        $userid = $this->_request->getParam("userid");
        $now = (int) $this->_request->getParam("now");
        $startdate = $this->_request->getParam("startdate");
        $_money = $this->_request->getParam("money");
        $money = str_replace(",", "", $_money);
        $des = $this->_request->getParam("des");
        $_bhxh = $this->_request->getParam("bhxh");
        $bhxh = str_replace(",", "", $_bhxh);
        $_money_eat = $this->_request->getParam("money_eat");
        $money_eat = str_replace(",", "", $_money_eat);
        $_subsidize = $this->_request->getParam("subsidize");
        $subsidize = str_replace(",", "", $_subsidize);
        $date_now = date('Y-m-d H:i:s');
        $idregency = $this->_request->getParam("idregency");
        $ret = array();
        if ($idregency == 0) {
            $err['id'] = "idregency";
            $err['msg'] = "Thông báo. \nVui lòng chọn chức vụ.";
            $ret[] = $err;
        }
        if (empty($startdate)) {
            $err['id'] = "reservation";
            $err['msg'] = "Thông báo.\nVui lòng chọn thời gian";
            $ret[] = $err;
        }
        if ($money == 0) {
            $err['id'] = "money";
            $err['msg'] = "Số tiền không được để trống";
            $ret[] = $err;
        }
        if (count($ret) > 0){
                echo json_encode($ret);
        }else{
            $data["money"] = $money;
            $data["now"] = $now;
            $data["des"] = $des;
            $data["startdate"] = $startdate;
            $data["idregency"] = $idregency;
            $data["subsidize"] = $subsidize;
            $data["money_eat"] = $money_eat;
            $data["bhxh"] = $bhxh;
            if($id == 0){
                $data["userid"] = $userid;
                $data["datetime"] = $date_now;
                $data["enabled"] = 1;
                $data["creator"] = $this->_identity["username"];
                if($now ==1){
                    $data3["payroll"] = $money;
                    $_zwfuser->update($userid, $data3);
                    
                    $detail = $_payroll->getDetailByNow($userid);
                    if(!empty($detail)){
                        $data2["enddate"] = date('Y-m-d');
                        $_payroll->update($detail["id"], $data2);
                    }
                    $sql = "Update payroll set now =0 where userid = $userid";
                    $_payroll->excute($sql);
                }
                $_payroll->insert($data);
                
            }else{
               if($now ==1){
                    $sql = "Update payroll set now =0 where userid = $userid";
                    $_payroll->excute($sql);
               }
               $_payroll->update($id, $data); 
            }
            $_option->syncAll('event');
            $err['id'] = "ok";
            $err['msg'] = "ok";
            $ret[] = $err;
            echo json_encode($ret);
        }
    }
    public function saveIpsAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $_zwfuser = Business_Common_Users::getInstance();
        $id = (int)$this->_request->getParam("id");
        $userid = $this->_request->getParam("uid");
        $ips = $this->_request->getParam("ips");
        $ret = array();
        if ($ips == NULL) {
            $err['id'] = "ips";
            $err['msg'] = "Thông báo. \nVui lòng nhập ips.";
            $ret[] = $err;
        }
        
        if (count($ret) > 0){
                echo json_encode($ret);
        }else{
            $data["ips"] = $ips;
            $data["userid"] = $userid;
            if($id == 0){
                $_zwfuser->update($userid,$data);
                
            }else{
//                
            }
            Business_Addon_Options::getInstance()->syncAll('event');
            $err['id'] = "ok";
            $err['msg'] = "ok";
            $ret[] = $err;
            echo json_encode($ret);
        }
    }

    public function getPayrollAction() {
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $_payroll = Business_Common_Payroll::getInstance();
        $_zwfuser = Business_Common_Users::getInstance();
        $_regency = Business_Addon_Regency::getInstance();
        $list_regency = $_regency->getList();
        $name_regency = array();
        foreach ($list_regency as $items){
            $name_regency[$items["id"]] = $items["name"];
        }
        $fullname = array();
        $list_us = $_zwfuser->getList();
        foreach ($list_us as $items){
            $fullname[$items["userid"]] = $items["fullname"];
        }
        $id = (int) $this->_request->getParam("id");
//        var_dump($id);exit();
        $list = array();
        if ($id > 0) {
            $list = $_payroll->getListByUserId($id);
            if (!empty($list)) {
                foreach ($list as &$items) {
                    $items["name_regency"] ='';
                    $items["fullname"] = $fullname[$items["userid"]];
                    $items["money"] = number_format($items["money"]);
                    $items["startdate2"] = date('d/m/Y' ,  strtotime($items["startdate"]));
                    $items["enddate2"] = date('d/m/Y' ,  strtotime($items["enddate"]));
                    if($items["now"] ==1){
                        $items["now2"] = "Có";
                        $items["enddate2"] = "Hiện tại";
                        $items["enddate"] = date('d/m/Y');
                    }else{
                        if($items["now"] ==0){
                            $items["now2"] = "Không";
                        }
                    }
                    $items["idregency"] = (int)$items["idregency"];
                    if($items["idregency"] >0){
                        $items["name_regency"] = $name_regency[$items["idregency"]];
                    }
                    
                    
                }
            }
        }
        echo json_encode($list);
    }

    public function saveMoveUserAction() {
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $vote_rotatory = Business_Addon_VoteRotatory::getInstance();
        $zwf_users = Business_Common_Users::getInstance();
        $_option = Business_Addon_Options::getInstance();
        $userid = $this->_request->getParam("userid");
        $vote_id = $this->_request->getParam("vote_new");
        $vote_name = $this->_request->getParam("vote_name");
        $abbreviation = $this->_request->getParam("abbreviation");
        $storename = trim($vote_name);
        $ip = $_option->checkIp($vote_id);
        $detail = $vote_rotatory->getDetailByVoteId($userid);
        $id = (int) $detail["id"];
        $vote_old = $detail["vote_id"];

        if ($vote_id == $vote_old) {
            $err['id'] = "vote_new";
            $err['msg'] = "Lỗi!\nVui lòng lựa chọn chi nhánh mới";
            $ret[] = $err;
        }
        if ($vote_id == null) {
            $err['id'] = "vote_new";
            $err['msg'] = "Lỗi!\nVui lòng lựa chọn chi nhánh mới";
            $ret[] = $err;
        }
        if (count($ret) > 0) {
            echo json_encode($ret);
        } else {
            $data["userid"] = $userid;
            $data["vote_id"] = $vote_id;
            $data["created_date"] = date('Y-m-d H:i:s');
            $vote_rotatory->insert($data);
            $pdata["parentid"] = $vote_id;
            $pdata["storename"] = $storename;
            $pdata["abbreviation"] = $abbreviation;
            $pdata["ips"] = $ip;
            $zwf_users->updateUser($userid, $pdata);
            $err['id'] = "ok";
            $err['msg'] = "ok";
            $ret[] = $err;
            echo json_encode($ret);
        }
    }

    public function listUserAction() {
        $_department = Business_Addon_Department::getInstance();
        $_regency_department = Business_Addon_RegencyDepartment::getInstance();
        $list_department = $_department->getList();
        $zfw_users = Business_Common_Users::getInstance();
        $_option = Business_Addon_Options::getInstance();
        $_regency = Business_Addon_Regency::getInstance();
        
        $list_regency = $_regency->getList();
        
        $list_vote = $zfw_users->getListByUname2(false);
        $this->view->list_vote = $list_vote;
        $keyword = $this->_request->getParam("keyword");
        $this->view->keyword = $keyword;
        $vote_id = $this->_request->getParam("vote_id");
//            var_dump($vote_id);exit();
        $active = $this->_request->getParam('active', '');
        $this->view->vote_id = $vote_id;
        if ($active == '' || is_null($active)) {
            $active = $this->_default_active;
        }
        $department = (int) $this->_request->getParam("department");
        $this->view->department = $department;
        $idregency = $this->_request->getParam("regency");
        $this->view->regency = $idregency;


        $storename = array();
        foreach ($list_vote as $items) {
            $storename[$items["userid"]] = $items["storename"];
        }
        $regency = array();
        foreach ($list_regency as $items) {
            $regency[$items["id"]] = $items["name"];
            $items["id_regency"] = $items["id"];
        }
        $this->view->list_regency = $list_regency;
        if ($department > 0) {
            $list_regency2 = $_regency_department->getListByDepartment($department);
            foreach ($list_regency2 as &$items) {
                $items["name"] = $regency[$items["id_regency"]];
            }
            $this->view->list_regency = $list_regency2;
        }
        $this->view->active = $active;
        $this->view->plist = $this->_plist;
        $this->view->storeid = $vote_id;
        $list = $zfw_users->getListUser($keyword, $vote_id, $active, $idregency);
        foreach ($list as &$items) {
            $vote_id = $items["parentid"];
            if ($vote_id != 0) {
                $items["vote_name"] = $storename[$vote_id];
            }
            $items["regency"] = $regency[$items["idregency"]];
        }
        $this->view->list_department = $list_department;
        $this->view->items = $list;
        $display ='none';
        if($vote_id >0){
            $display = '';
        }
        $this->view->display = $display;
    }

    public function indexAction() {
        $_department = Business_Addon_Department::getInstance();
        $_regency_department = Business_Addon_RegencyDepartment::getInstance();
        $list_department = $_department->getList();
        $zfw_users = Business_Common_Users::getInstance();
        $_option = Business_Addon_Options::getInstance();
        $_regency = Business_Addon_Regency::getInstance();
        
        $list_regency = $_regency->getList();
        
        $list_vote = $zfw_users->getListByUname(false);
        $this->view->list_vote = $list_vote;
        $keyword = $this->_request->getParam("keyword");
        $this->view->keyword = $keyword;
        $vote_id = $this->_request->getParam("vote_id");
//            var_dump($vote_id);exit();
        $active = $this->_request->getParam('active', '');
        $this->view->vote_id = $vote_id;
        if ($active == '' || is_null($active)) {
            $active = $this->_default_active;
        }
        $department = (int) $this->_request->getParam("department");
        $this->view->department = $department;
        $idregency = (int)$this->_request->getParam("regency");
        $this->view->regency = $idregency;


        $storename = array();
        foreach ($list_vote as $items) {
            $storename[$items["userid"]] = $items["storename"];
        }
        $regency = array();
        foreach ($list_regency as $items) {
            $regency[$items["id"]] = $items["name"];
            $items["id_regency"] = $items["id"];
        }
        $this->view->list_regency = $list_regency;
        if ($department > 0) {
            $list_regency2 = $_regency_department->getListByDepartment($department);
            foreach ($list_regency2 as &$items) {
                $items["name"] = $regency[$items["id_regency"]];
            }
            $this->view->list_regency = $list_regency2;
        }
        $this->view->active = $active;
        $this->view->plist = $this->_plist;
        $this->view->storeid = $vote_id;
        $list = $zfw_users->getListUser($keyword, $vote_id, $active, $idregency);
        foreach ($list as &$items) {
            $vote_id = $items["parentid"];
            if ($vote_id != 0) {
                $items["vote_name"] = $storename[$vote_id];
            }
            $items["regency"] = $regency[$items["idregency"]];
        }
        $this->view->list_department = $list_department;
        $this->view->items = $list;
        $display ='none';
        if($vote_id >0){
            $display = '';
        }
        $this->view->display = $display;
    }

    public function editAction() {
        $auth = Zend_Auth::getInstance();
        $identity = $auth->getIdentity();

        $id = $identity->userid;

        $_user = Business_Common_Users::getInstance();

        $user = $_user->getUserByUid($id);

        //get role for user
        $_roles = Business_Common_Roles::getInstance();
        $roles = $_roles->getRolesByUser($id);

        $this->view->data = $user;
    }

    public function saveAction() {
        $this->_helper->viewRenderer->setNoRender();

        $auth = Zend_Auth::getInstance();
        $identity = $auth->getIdentity();

        $id = $identity->userid;

        $password = $this->_request->getParam('password');

        $data = array();

        if ($password != null && $password != '') {
            $data['password'] = md5($password);
        }

        $_user = Business_Common_Users::getInstance();
        $_user->updateUser($id, $data);
        echo "<script>
                        alert('Completed');
                        window.location = '/admin/user/users/edit/';
                    </script>";
    }

    ////////// private functions ////////////

    /**
     * Get bussiness instance of Business_Common_Users
     *
     * @return Business_Common_Users
     */
    private function getUserBusiness() {
        if ($this->_user_business == null) {
            $this->_user_business = new Business_Common_Users();
        }
        return $this->_user_business;
    }

    public function listshopAction() {
        $_ress = Business_Common_Users::getInstance();
        $this->view->list = $_ress->getListByUname();
    }

    public function resetallAction() {
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $_ress = Business_Common_Users::getInstance();
        $this->view->list = $_ress->resetAllPass();
        $this->_redirect('/admin/user/users/listshop'); //
    }
    public function getPassAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $userid = $this->_request->getParam("userid");
        $zwf_user = Business_Common_Users::getInstance();
        $detail = $zwf_user->getDetailnoCache($userid);
        echo json_encode($detail);
    }

    public function resetAction() {
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $res = Business_Common_Users::getInstance();
        $userid = $this->_request->getParam('userid');
        $chars = "abcdefghjkmnopqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ23456789";
        $size = strlen( $chars );
            for( $i = 0; $i < $size; $i++ ) {
              $str .= $chars[ rand( 0, $size - 1 ) ];
            }
        $pass = substr( str_shuffle( $chars ), 0, 10 );
        
        $this->view->item = $res->resetPassById($userid,$pass);
    }

    public function getMemberAction() {
        $this->_redirect('/admin/user/users/list-user');
        $list_vote = Business_Common_Users::getInstance()->getListByUname();
        foreach ($list_vote as &$items1) {
            $items1["vote_name1"] = Business_Addon_Options::getInstance()->getStoreName($items1["userid"]);
        }
        $this->view->items1 = $list_vote;
//            var_dump($list_vote);exit();
        $this->view->keyword = $keyword = $this->_request->getParam("keyword");
        $this->view->vote_id = $vote_id = $this->_request->getParam("vote_id");
        $list = Business_Common_Users::getInstance()->getMember($keyword, $vote_id);
//            var_dump($list);exit();
        foreach ($list as &$items) {
            $vote_id = $items["parentid"];
            $list_vote = Business_Common_Users::getInstance()->getUserByUid2($vote_id);
            foreach ($list_vote as $items2) {
                $items["vote_name"] = Business_Addon_Options::getInstance()->getStoreName($items2["userid"]);
            }
        }
        $this->view->items = $list;
    }

    public function editMemberAction() {
        $username = $this->_identity["username"];
//            if($username =="hnmobile"){
//                $this->_helper->viewRenderer('edit-member2');
//            }


        $_zwf_users = Business_Common_Users::getInstance();
        $_option = Business_Addon_Options::getInstance();
        $_type_user = Business_Common_TypeUser::getInstance();
        $list_vote = $_zwf_users->getListByUname2(FALSE);

        $_regency = Business_Addon_Regency::getInstance();

        $list_regency = $_regency->getList();
        $this->view->list_regency = $list_regency;

        foreach ($list_vote as &$items) {
            $userid = $items["userid"];
            $items["vote_name"] = $items["storename"];
        }
        $this->view->items1 = $list_vote;
        $list = $_type_user->getList();
        $this->view->list = $list;
        $id = $this->_request->getParam("id", 0);
        $userid_member = $this->_request->getParam("userid_member");
        $this->view->userid_member = $userid_member;
        $disabled = "";
        if ($id > 0) {
            $detail = $_zwf_users->getDetailById($id);
            $disabled = 'readonly="true"';
        }
        $this->view->disabled = $disabled;
        $this->view->detail = $detail;

        $_city = Business_Common_City::getInstance();
        $list_city = $_city->getListCity();
        $this->view->list_city = $list_city;
        $_district = Business_Common_District::getInstance();

        $id_city = (int) $detail["city"];
        if ($id_city == 0) {
            $id_city = 1;
        }
        $list_district = $_district->getListDistrict2($id_city);
//        var_dump($list_district);exit();
        $this->view->list_district = $list_district;
    }

    public function deleteMemberAction() {
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $userid = $this->_request->getParam("id");
        Business_Common_Users::getInstance()->deleteMember($userid);
    }

    public function restoreMemberAction() {
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $userid = $this->_request->getParam("id");
        Business_Common_Users::getInstance()->restoreMember($userid);
    }

    public function InputValiDate($ret, $data) {
        $_users = Business_Common_Users::getInstance();
        $_option = Business_Addon_Options::getInstance();
        $uname = $this->_request->getParam("username");
        $username = "hnam_" . $uname;
        $password = $this->_request->getParam("password");
        $r_password = $this->_request->getParam("r-password");
        $ret1 = $_option->isValid($data["idregency"], "idregency", "Thông báo!.\nVui lòng chọn chức vụ nhân viên.\nXin cảm ơn!.", 0);
        $ret = array_merge($ret, $ret1);
        $ret2 = $_option->isValid($data["fullname"], "fullname", "Thông báo!.\nVui lòng nhập họ tên đầy của nhân viên.\nXin cảm ơn!.");
        $ret = array_merge($ret, $ret2);
        $ret3 = $_option->isValid($data["phone"], "phone", "Thông báo!.\nVui lòng nhập số điện thoại nhân viên.\nXin cảm ơn!.");
        $ret = array_merge($ret, $ret3);
        $ret4 = $_option->isValid($username, "username", "Thông báo!.\nVui lòng nhập tên đăng nhập", "hnam_");
        $ret = array_merge($ret, $ret4);
        if ($username != "hnam_") {
            $id = (int) $this->_request->getParam("id");
            if ($id == 0) {
                $role = $_users->getUser($username);
                if ($role != null) {
                    $ret5 = $_option->isValid("", "username", "Thông báo!.\nTên đăng nhập này đã tồn tại.Vui lòng nhập tên khác.\nXin cảm ơn!.");
                    $ret = array_merge($ret, $ret5);
                }
            }
        }
        $ret6 = $_option->isValid($password, "password", "Thông báo!.\nVui lòng nhập password.\nXin cảm ơn!.");
        $ret = array_merge($ret, $ret6);
        if (strlen($password) < 6) {
            $ret7 = $_option->isValid(6, "password", "Thông báo!.\nPassword phải lớn hơn 6 ký tự.\nXin cảm ơn!.", 6);
            $ret = array_merge($ret, $ret7);
        }
        if ($password != $r_password) {
            $ret8 = $_option->isValid("", "r-password", "Thông báo!.\nPassword nhập lại không đúng.\nXin cảm ơn!.");
            $ret = array_merge($ret, $ret8);
        }
        if ($data["type"] == 1 || $data["type"] == 3) {
            $storeid = (int) $this->_request->getParam("storeid");
            if ($storeid == 0) {
                $ret9 = $_option->isValid("", "storeid", "Thông báo!.\nVui lòng nhập tên cửa hàng.\nXin cảm ơn!.");
                $ret = array_merge($ret, $ret9);
            }
        }
        return $ret;
    }

    public function saveMemberAction() {
        if ($this->_request->isPost()) {
            $this->_helper->Layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            $_users = Business_Common_Users::getInstance();
            $vote_rotatory = Business_Addon_VoteRotatory::getInstance();
            $_option = Business_Addon_Options::getInstance();
            $_role = Business_Common_Roles::getInstance();
            $data = array();
            $id = (int) ($this->_request->getParam("id"));


            $_birthday = str_replace("/", "-", $this->_request->getParam("birthday"));
            $birthday = date('Y-m-d', strtotime($_birthday));
            $cmnd = $this->_request->getParam("cmnd");
            $resident_address = $this->_request->getParam("resident_address");
            $staying_address = $this->_request->getParam("staying_address");
            $_startdate = str_replace("/", "-", $this->_request->getParam("startdate"));
            $startdate = date('Y-m-d', strtotime($_startdate));
            $type = $this->_request->getParam("type");
            $storeid = (int) $this->_request->getParam("storeid");
            $uname = $this->_request->getParam("username");
            $password = $this->_request->getParam("password");
            $fullname = $this->_request->getParam("fullname");
            $phone = $this->_request->getParam("phone");
            $address = $this->_request->getParam("address");
            $email = $this->_request->getParam("email");
            $sc_name = $this->_request->getParam("security_name");
            $vote_name = $this->_request->getParam("vote_name");
            $bank = $this->_request->getParam("bank");
            $city = (int) $this->_request->getParam("city");
            $district = (int) $this->_request->getParam("district");
            $idregency = (int) $this->_request->getParam("idregency"); // id chức vụ
            $storename = trim($vote_name);


            $_uname = "hnam_";
            $username = $_uname . $uname;

            if ($storeid != 0) {
                $list_store = Business_Common_Users::getInstance()->getListByUname(FALSE);
                foreach ($list_store as $st){
                    $___ip[$st["userid"]] = $st["ips"];
                    $___security_name[$st["userid"]] = $st["security_name"];
                    $___sort_name[$st["userid"]] = $st["abbreviation"];
                }
            }

            $data['parentid'] = $storeid;
            $data['fullname'] = $fullname;
            $data['phone'] = $phone;
            $data['address'] = $address;
            $data['email'] = $email;
            $data["is_actived"] = 1;
            $data["status"] = 1;
            $data["createdate"] = date('Y-m-d H:i:s');
            $data["type"] = $type;
            $data["storename"] = $storename;
            $data["ips"] = $___ip[$storeid];
            $data["security_name"] = $___security_name[$storeid];
            $data["abbreviation"] = $___sort_name[$storeid];
            $data["birthday"] = $birthday;
            $data["cmnd"] = $cmnd;
            $data["resident_address"] = $resident_address;
            $data["staying_address"] = $staying_address;
            $data["startdate"] = $startdate;
            $data["bank"] = $bank;
            $data["city"] = $city;
            $data["district"] = $district;
            $data["idregency"] = $idregency;
            $ret = array();
            $ret = $this->InputValiDate($ret, $data);
            if (count($ret) > 0) {
                echo json_encode($ret);
            } else {
                if ($id == 0) {
                    $data['username'] = $username;
                    $data['password'] = md5($password);
                    $data['pass_show'] = $password;
                    $_users->addMember($data);
                } else {
                    $_users->update($id, $data);
                }
                $_option->syncAll('event');
                $err['id'] = "ok";
                $err['msg'] = "ok";
                $ret[] = $err;
                echo json_encode($ret);
            }
        }
    }

    public function saveMember2Action() {
        if ($this->_request->isPost()) {
            $this->_helper->Layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            $_users = Business_Common_Users::getInstance();
            $vote_rotatory = Business_Addon_VoteRotatory::getInstance();
            $_option = Business_Addon_Options::getInstance();
            $_role = Business_Common_Roles::getInstance();
            $data = array();
            $id = (int) ($this->_request->getParam("id"));


            $_birthday = str_replace("/", "-", $this->_request->getParam("birthday"));
            $birthday = date('Y-m-d', strtotime($_birthday));
            $cmnd = $this->_request->getParam("cmnd");
            $resident_address = $this->_request->getParam("resident_address");
            $staying_address = $this->_request->getParam("staying_address");
            $_startdate = str_replace("/", "-", $this->_request->getParam("startdate"));
            $startdate = date('Y-m-d', strtotime($_startdate));
            $type = $this->_request->getParam("type");
            $storeid = (int) $this->_request->getParam("storeid");
            $uname = $this->_request->getParam("username");
            $password = $this->_request->getParam("password");
            $fullname = $this->_request->getParam("fullname");
            $phone = $this->_request->getParam("phone");
            $address = $this->_request->getParam("address");
            $email = $this->_request->getParam("email");
            $sc_name = $this->_request->getParam("security_name");
            $vote_name = $this->_request->getParam("vote_name");
            $bank = $this->_request->getParam("bank");
            $city = (int) $this->_request->getParam("city");
            $district = (int) $this->_request->getParam("district");
            $idregency = (int) $this->_request->getParam("idregency"); // id chức vụ
            $storename = trim($vote_name);


            $_uname = "hnam_";
            if ($id == 0) {
                $username = $_uname . $uname;
            } else {
                $username = $uname;
            }

            if ($storeid != 0) {
                $ips = $_option->checkIp($storeid);
                $security_name = $_option->getSecurityName($storeid);
                $sort_name = $_option->getSortStorename($storeid);
            }
//                $data['username']       = $username;
//                $data['password']       = md5($password);
//                $data['pass_show']      = $password;
//                $data['parentid']       = $storeid;
            $data['fullname'] = $fullname;
            $data['phone'] = $phone;
            $data['address'] = $address;
            $data['email'] = $email;
            $data["is_actived"] = 1;
            $data["status"] = 1;
            $data["createdate"] = date('Y-m-d H:i:s');
//                $data["type"]           = $type;
//                $data["storename"]      = $storename;
//                $data["ips"]            = $ips;
//                $data["security_name"]  = $security_name;
//                $data["abbreviation"]   = $sort_name;
            $data["birthday"] = $birthday;
            $data["cmnd"] = $cmnd;
            $data["resident_address"] = $resident_address;
            $data["staying_address"] = $staying_address;
            $data["startdate"] = $startdate;
            $data["bank"] = $bank;
            $data["city"] = $city;
            $data["district"] = $district;
            $data["idregency"] = $idregency;

            $ret = array();
//                $ret                    = $this->InputValiDate($ret,$data);
            if (count($ret) > 0) {
                echo json_encode($ret);
            } else {
//                     echo '<pre>';var_dump($data);exit();
                if ($id == 0) {
                    $userid = $_users->addMember($data);
                    $check_role = $_role->getRolesByUserPid($userid, $type);
                    if ($check_role == null) {
                        $_role->addRole($userid, $type);
                    }
                    $data2["userid"] = $userid;
                    $data2["vote_id"] = $storeid;
                    $data2["created_date"] = date('Y-m-d H:i:s');
                    $vote_rotatory->insert($data2);
                } else {
                    $detail = $vote_rotatory->getDetailByVoteId($id);
                    $data2["userid"] = $id;
                    $data2["vote_id"] = $storeid;
                    if ($detail != null) {
                        $data2["time_update"] = date('Y-m-d H:i:s');
                        $vote_rotatory->update($detail["id"], $data2);
                    } else {
                        $data2["created_date"] = date('Y-m-d H:i:s');
                        $vote_rotatory->insert($data2);
                    }
                    $_users->update($id, $data);
                }
                $err['id'] = "ok";
                $err['msg'] = "ok";
                $ret[] = $err;
                echo json_encode($ret);
            }
        }
    }

    public function logoutAction() {
        $auth = Zend_Auth::getInstance();
        $auth->clearIdentity();
        header("Location: /admin");
    }

    public function setUrlAction() {
        
    }

}

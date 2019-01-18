<?php

/**
 * Hnam Controller
 * @author: nghidv
 */
class User_Admin_AppServicesController extends Zend_Controller_Action {

    private $_identity;

    public function init() {
        // do something
        BlockManager::setLayout('appbh');
        $auth = Zend_Auth::getInstance();
        $identity = $auth->getIdentity();
        $this->_identity = (array) $auth->getIdentity();
        $this->view->full_name = $identity->fullname;
        if ($identity != null) {
            $username = $identity->username;
            $this->view->username = $username;
            $this->view->user_name = $username;
        }
    }
    public function thongKeInstallmentAction(){
        $_users_products = Business_Addon_UsersProducts::getInstance();
        $_installment = Business_Addon_Installment::getInstance();
        $_user = Business_Common_Users::getInstance();
        $list_storeid = $_user->getListByUname(FALSE);
        $_option = Business_Addon_Options::getInstance();
        $cmonths = (int)$this->_request->getParam("cmonths");
        $this->view->cmonths = $cmonths;
        $id = $this->_request->getParam("id");
        $this->view->id = $id;
        
        $day_ce = $this->_request->getParam("day_ce");
        $startmonth = date('Y/m/01');
        if($day_ce == null){
            $endmonth = date('t', mktime(0, 0, 0, date('m'), 1, date('Y')));
            $day_ce = $startmonth . ' - ' . date('Y')."/". date('m')."/".$endmonth;
        }
        $startdate = $_option->getDayC($day_ce);
        $enddate = $_option->getDayE($day_ce);
        $this->view->day_ce = $day_ce;
        
        $detail = $_installment->getDetailById2($id);
        $list_months = explode(",", $detail["c_months"]);
        $this->view->list_months = $list_months;
        $fee = $detail["fee"];
        $list = $_users_products->getInstallment($id,$cmonths,$startdate,$enddate);
        $sum = array();
        $total = array();
        $prepay = array();
        foreach ($list as &$items){
            $sum[$items["vote_id"]] +=  $items["sum"];
            $total[$items["vote_id"]] +=  $items["total"];
            $prepay[$items["vote_id"]] +=  $items["prepay"];
        }
        $this->view->sum = $sum;
        $this->view->total = $total;
        $this->view->prepay = $prepay;
        $this->view->fee = $fee;
        $this->view->list_vote = $list_storeid;
        $this->view->detail = $detail;
        $list_cated = $_option->getTraGop2();
        $this->view->list_cated = $list_cated;
//        echo '<pre>';;var_dump($list);exit();
    }

    public function listInstallmentAction(){
        $kw = $this->_request->getParam("kw");
        $this->view->kw = $kw;
        $_option  = Business_Addon_Options::getInstance();
        $_installment = Business_Addon_Installment::getInstance();
        $list_cated = $_option->getTraGop2();
        $this->view->list_cated = $list_cated;
        $cateid = $this->_request->getParam("cateid");
        $this->view->cateid = $cateid;
        
        $day_ce = $this->_request->getParam("day_ce");
        $startmonth = date('Y/m/01');
        if($day_ce == null){
            $endmonth = date('Y/m/t',  strtotime($startmonth));
            $day_ce = $startmonth . ' - ' . $endmonth;
        }
        $startdate = $_option->getDayC($day_ce);
        $enddate = $_option->getDayE($day_ce);
        
        $this->view->day_ce = $day_ce;
        
        $list = $_installment->getList2($kw,$cateid);
        foreach ($list as &$items){
            $items["name_cateid"] = $_option->getTraGop2($items["cated_prepaid"]);
            $items["startdate"] = date('d/m/Y',  strtotime($items["startdate"]));
            $items["enddate"] = date('d/m/Y',  strtotime($items["enddate"]));
            if($items["status"] ==1){
                $items["status2"] =" Kích hoạt";
            }else{
                $items["status2"] =" Không kích hoạt";
            }
            
        }
        $this->view->list = $list;
        
        //add
        $list_apply = $_option->getApply();
        $this->view->list_apply = $list_apply;
        $list_percent = array();
        for($i=0;$i<=100;){
            $list_percent[] = $i;
            $i+=5;
        }
        $this->view->list_percent = $list_percent;
        $cmonths = array();
        for($i=1;$i<=24;$i++){
            $cmonths[] = $i;
        }
        $this->view->mid = count($cmonths)/2;
        $this->view->list_cmonths = $cmonths;
    }
    public function deleteInstallmentAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $id = $this->_request->getParam("id");
        $_installment = Business_Addon_Installment::getInstance();
        $data["status"] = 0;
        $_installment->update($id, $data);
    }

    public function addInstallmentAction(){
        
    }
    public function saveInstallmentAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $_option = Business_Addon_Options::getInstance();
        $_installment = Business_Addon_Installment::getInstance();
        $id = (int)$this->_request->getParam("id");
        $_cmonths = array();
        
        for($i=1;$i<=24;$i++){
            $_monthsid = $this->_request->getParam("cmonths_$i");
            if($_monthsid != null){
                $_cmonths[] = "$_monthsid";
            }
        }
        
        $days_created_end = $this->_request->getParam("day_created_end");
        $startdate = $_option->getDayC($days_created_end);
        $enddate = $_option->getDayE($days_created_end);
        
        $name = $this->_request->getParam("name");
        $cated_prepaid = $this->_request->getParam("cated_prepaid");
        $apply = $this->_request->getParam("apply");
        $percent = $this->_request->getParam("percent");
        $m = implode(",", $_cmonths);
        $username = $this->_identity["username"];
        $now =date('Y-m-d H:i:s');
        $_fee =$this->_request->getParam("fee");
        $fee = (float)str_replace(",", ".", $_fee);
        $data = array();
        
        $data["name"] = $name;
        $data["cated_prepaid"] = $cated_prepaid;
        $data["apply"] = $apply;
        $data["percent"] = $percent;
        $data["c_months"] = $m;
        $data["startdate"]  = $startdate;
        $data["enddate"]    = $enddate;
        $data["datetime"] = $now;
        $data["creator"] = $username;
        $data["fee"] = $_fee;
        $data["status"] = 1;
        $products_id = $this->_request->getParam("products_id");
        
        $arr = implode(",", $products_id);
        if(!empty($arr)){
            foreach ($products_id as $items){
                $pid = explode( "--",$items);
                $_sql[] = "INSERT INTO installment (id   , name  , cated_prepaid, apply, products_id,products_name, percent, c_months,startdate,enddate,datetime,status,creator)
                                           VALUES (NULL, '$name', '$cated_prepaid', '1', '$pid[2]','$pid[0]', '$percent', '$m','$startdate', '$enddate','$now', 1,'$username');";
            }
        }
        $sql = implode("", $_sql);
        $ret = array();
        if ($id == 0) {
            $ret = $this->isValidIstallment($ret, $data);
        }
        if (count($ret) > 0) {
            echo json_encode($ret);
        } else {
            if($apply ==0){
                $data["products_id"] = 0;
                $data["products_name"] = 'Tất cả';
                $_installment->insert($data);
            } else {
                $_installment->excute($sql);
            }
            $err['id'] = "ok";
            $err['msg'] = "ok";
            $ret[] = $err;
            echo json_encode($ret);
        }
        
    }
    public function isValidIstallment($ret, $data) {
        $products_id = $this->_request->getParam("products_id");
        $arr = implode(",", $products_id);
        $_option = Business_Addon_Options::getInstance();
        $days_created_end = $this->_request->getParam("day_created_end");
        $ret = $_option->isValid($data["name"], "name", "Thông báo.!\nVui lòng nhập tên chương trình","");
        $ret2 = $_option->isValid($data["cated_prepaid"], "cated_prepaid", "Thông báo.!\nVui lòng chọn loại trả góp",0);
        $ret3 = $_option->isValid($data["apply"], "apply", "Thông báo.!\nVui lòng lựa chọn loại áp dụng ",-1);
        
//        $ret4 = $_option->isValid($data["percent"], "percent", "Thông báo.!\nVui lòng lựa chọn phần trăm trả trước ",0);
        $ret5 = $_option->isValid($data["c_months"], "cmonths_1", "Thông báo.!\nVui lòng lựa chọn thời gian trả góp ","");
        $ret6 = $_option->isValid($days_created_end, "reservation", "Thông báo.!\nVui lòng lựa chọn thời gian hiệu lực ","");
        $ret = array_merge($ret,$ret2);
        $ret = array_merge($ret,$ret3);
        if($data["apply"]==1){
            $ret7 = $_option->isValid($arr, "products_id", "Thông báo.!\nVui lòng lựa chọn sản phẩm áp dụng ","");
            $ret = array_merge($ret,$ret7);
        }
//        $ret = array_merge($ret,$ret4);
        $ret = array_merge($ret,$ret5);
        $ret = array_merge($ret,$ret6);
        return $ret;
    }
    
    public function thongKeBanDichVuAction(){
        $_option = Business_Addon_Options::getInstance();
        $_sev = Business_Addon_Services::getInstance();
        $p = (int)$this->_request->getParam("p");
        $type = (int)$this->_request->getParam("type");
        if($type ==0){
            $type = 4;
        }
        $this->view->type = $type;
        $this->view->plist = $_option->getPriceDV($type);
        
//        $days_created_end = $this->_request->getParam("day_created_end");
//        if ($days_created_end == null) {
//            $days_created_end = date('Y/m/d') . ' - ' . date('Y/m/d');
//        }
//        $this->view->days_created_end = $days_created_end;
//        $created_date = substr($days_created_end, 0, 10);
//        $created_day = str_replace("/", "-", $created_date) . ' 00:00:00';
//        
//        $end_date = substr($days_created_end, 13, 10);
//        $end_day = str_replace("/", "-", $end_date) . ' 23:59:59';
        
        $d=date('Y-m-d');
        $k=$_option->pre_date($d, 3); 
        $nows = date('F j, Y',  strtotime($k));
        $start_end           = $this->_request->getParam("start_end");
        if($start_end ==null){
           $start_end = $nows." - ".date("F j, Y"); 
        }
        $this->view->start_end = $start_end;
        $start  = $_option->getStartDate($start_end);
        $end  = $_option->getEndDate($start_end);
        $this->view->start = $start;
        $this->view->end = $end;
        
        
        
        $this->view->p = $p;
        $list_dv = $_option->getNameDV();
        $this->view->list_dv = $list_dv;
        
        $tk = $_sev->getTkListByDay($start,$end,$type,1,$p);
        $user = Business_Common_Users::getInstance();
        $list_vote = $user->getListByUname(FALSE);
        $total_sum = array();
        $total_count = array();
        $name_dv = '';
        if($type ==4){
            $name_dv = 'Gói cài đặt IOS';
        }
        if($type ==11){
            $name_dv = 'Gói cài đặt Android';
        }
        foreach ($tk as $items){
            $total_sum[$items["vote_id"]] = $items["total_price"];
            $total_count[$items["vote_id"]] = $items["total"];
        }
        $this->view->name_dv = $name_dv;
        $this->view->total_sum = $total_sum;
        $this->view->total_count = $total_count;
        $this->view->list_vote = $list_vote;
        $this->view->items = $tk;
        
        $list_sev2 = Business_Addon_AppServices::getInstance()->get_count_app_by_userid($type,$start,$end);
        foreach ($list_sev2 as $sv2){
            $array_sv[$sv2["parentid"]][] = $sv2;
            $sum_store[$sv2["parentid"]] += $sv2["total"];
            $str_userid[] = $sv2["userid"];
        }
        $this->view->sum_store = $sum_store;
        
        if($str_userid){
            $__str_userid = implode(",", $str_userid);
            $list_user = Business_Common_Users::getInstance()->getListById($__str_userid);
            foreach ($list_user as $user){
                $l_user[$user["parentid"]][] = $user;
                $lfullname[$user["userid"]] = $user["fullname"];
            }
        }
        
        $this->view->lfullname = $lfullname;
        $this->view->array_sv = $array_sv;
        
        
        $cProduct = Business_Addon_UsersProducts::getInstance()->count_apple($start,$end,1);
        foreach ($cProduct as $__items){
            $_cProduct[$__items["vote_id"]] += $__items["total"];
        }
        $this->view->cProducts = $_cProduct;
//        if($this->_identity["userid"]==8){
//            echo "<pre>";
//            var_dump($_cProduct);
//            die();
//        }
    }

        
    public function listSacombankAction(){
        $_option                = Business_Addon_Options::getInstance();
        $users_products         = Business_Addon_UsersProducts::getInstance();
        $days_created_end = $this->_request->getParam("day_created_end");
        $__endday              = $_option->getDayEndByMonths();
        if ($days_created_end == null) {
            $days_created_end   = date('Y/m/01') . ' - ' .$__endday;
        }
        $this->view->days_created_end = $days_created_end;
        $date_from              = $_option->getDayC($days_created_end);
        $date_to                = $_option->getDayE($days_created_end);
        $list                   = $users_products->getListSacombank($date_from,$date_to,1,0,1);
        $this->view->list       = $list;
    }

    public function indexAction() {
        $p = (int)$this->_request->getParam("pp");
        $_option = Business_Addon_Options::getInstance();
        
        $list_type_dv = $_option->get_type_dv();
        $this->view->list_type_dv = $list_type_dv;
        $_sev = Business_Addon_Services::getInstance();
        $type = (int)  $this->_request->getParam("type");
        $idregency = (int)$this->_identity["idregency"];
        $this->view->idregency = $idregency;
        
        $this->_helper->viewRenderer('index2');
        
        $d=date('Y-m-d');
        $k=$_option->pre_date($d, 1); 
        $nows = date('F j, Y',  strtotime($k));
        $start_end           = $this->_request->getParam("start_end");
        if($start_end ==null){
           $start_end = $nows." - ".date("F j, Y"); 
        }
        $this->view->start_end = $start_end;
        $created_day  = $_option->getStartDate($start_end);
        $end_day  = $_option->getEndDate($start_end);
        $this->view->start = $created_day;
        $this->view->end = $end_day;
        
        $this->view->storeid = $this->_identity["parentid"];
        $user = Business_Common_Users::getInstance();
        $vote_id = $this->_request->getParam("vote_id");
        $this->view->vote_id = $vote_id;
        $list_vote = $user->get_list_store();
        $_cProduct = array();
        $_cSetup_hnam = array();
        $_cSetup_other = array();
        $cProduct = array();
        if($type==1){
            $this->_helper->viewRenderer('index3');
            $cated_id =890; // bao bể màn hình
            $cProduct3 = Business_Addon_UsersProducts::getInstance()->get_list_by_cateid3($cated_id, $created_day, $end_day);
            foreach ($cProduct3 as $v2){
                $total_bbmh[$v2["vote_id"]] += $v2["total"];
                $sum_bbmh[$v2["vote_id"]] += $v2["total_price"];
                
                $total_bbmh_user[$v2["vote_id"]][$v2["id_users"]] = $v2["total"];
                $sum_bbmh_user[$v2["vote_id"]][$v2["id_users"]] = $v2["total_price"];
                
                $str_userid[] = $v2["id_users"];
                $list_user_by_store[$v2["vote_id"]][] = $v2["id_users"];
            }
            
//            $list_products2 = Business_Ws_ProductsItem::getInstance()->get_list_by_cheap($created_day, $end_day);
//            foreach ($list_products2 as $pd){
//                $__strID[] = $pd["itemid"];
//            }
//            if($__strID != NULL){
//                $strID = implode(",", $__strID);
//                $data1 = Business_Addon_UsersProducts::getInstance()->count_bbmh($created_day,$end_day); // điện thoại có màn hình cảm ứng
//            }
            $data1 = Business_Addon_UsersProducts::getInstance()->count_bbmh_by_cheap_bbmh($created_day,$end_day,0); // điện thoại có màn hình cảm ứng
//            $str_cated_ID ="466,586,587,622"; // máy tính bảng trừ ipab pro,trừ ipad mini 4
//            $not_cated_ID ="174"; // máy tính bảng trừ ipab pro,trừ ipad mini 4
//            $data2 = Business_Addon_UsersProducts::getInstance()->count_bbmh_by_cheap_bbmh2($created_day,$end_day,$not_cated_ID);  // máy tính bảng
            if($data1 != NULL){
                $cProduct = array_merge($cProduct,$data1);
            }
//            if($data2 != NULL){
//                $cProduct = array_merge($cProduct,$data2);
//            }
            
        }
        
        if($type==2){ // BHMR 24 Tháng
            $this->_helper->viewRenderer('index4');
            $cated_id4 =905; // bao bể màn hình
            $cProduct4 = Business_Addon_UsersProducts::getInstance()->get_list_by_cateid3($cated_id4, $created_day, $end_day);
            foreach ($cProduct4 as $v2){
                $total_bbmh[$v2["vote_id"]] += $v2["total"];
                $sum_bbmh[$v2["vote_id"]] += $v2["total_price"];
                
                $total_bbmh_user[$v2["vote_id"]][$v2["id_users"]] = $v2["total"];
                $sum_bbmh_user[$v2["vote_id"]][$v2["id_users"]] = $v2["total_price"];
                $str_userid[] = $v2["id_users"];
                $list_user_by_store[$v2["vote_id"]][] = $v2["id_users"];
            }
            $___pid="3,5";
            $cProduct = Business_Addon_UsersProducts::getInstance()->count_bbmh_by_productsid($created_day, $end_day,$___pid,$__type=3);
            
        }
        if($type==4){ // Bảo hành máy cũ
            $this->_helper->viewRenderer('index6');
            $cated_id4 = "901"; // bao bể màn hình
            $cProduct4 = Business_Addon_UsersProducts::getInstance()->get_list_by_cateid3($cated_id4, $created_day, $end_day);
            foreach ($cProduct4 as $v2){
                $total_bbmh[$v2["vote_id"]] += $v2["total"];
                $sum_bbmh[$v2["vote_id"]] += $v2["total_price"];
                
                $total_bbmh_user[$v2["vote_id"]][$v2["id_users"]] = $v2["total"];
                $sum_bbmh_user[$v2["vote_id"]][$v2["id_users"]] = $v2["total_price"];
                $str_userid[] = $v2["id_users"];
                $list_user_by_store[$v2["vote_id"]][] = $v2["id_users"];
            }
            
            $cProduct = Business_Addon_UsersProducts::getInstance()->get_list_by_cateid3(53,$created_day, $end_day);
        }
        if($type ==3){ // android
           $this->_helper->viewRenderer('index5');
           $tk = Business_Addon_UsersProducts::getInstance()->get_list_by_cateid3(929, $created_day, $end_day);
            
            $total_sum = array();
            $total_count = array();
            foreach ($tk as $item){
                $total_sum[$item["vote_id"]] += $item["total_price"];
                $total_count[$item["vote_id"]] += $item["total"];
                $sum_store_user[$item["vote_id"]][$item["id_users"]] = $item["total_price"];
                $total_store_user[$item["vote_id"]][$item["id_users"]] = $item["total"];
                $str_userid[] = $item["id_users"];
                $list_user_by_store[$item["vote_id"]][] = $item["id_users"];
            }
            
            $this->view->total_sum = $total_sum;
            $this->view->total_count = $total_count;
            $this->view->items = $tk;
            
            $this->view->sum_store_user = $sum_store_user;
            $this->view->total_store_user = $total_store_user;
           
            $ccSetup = Business_Addon_AppServices::getInstance()->getCountAppByVote(6, $created_day,$end_day);
            foreach ($ccSetup as $items){
                if($items["check_kh"] ==1){
                    $_cSetup_hnam[$items["vote_id"]] +=$items["total"];
                }
                if($items["check_kh"] ==2){
                    $_cSetup_other[$items["vote_id"]] +=$items["total"];
                }
            }
//            $list_products2 = Business_Ws_ProductsItem::getInstance()->get_list_by_cheap_android();
//            foreach ($list_products2 as $pd){
//                $__strID[] = $pd["itemid"];
//            }
//            if($__strID != NULL){
//                $strID = implode(",", $__strID);
//                $data1 = Business_Addon_UsersProducts::getInstance()->count_bbmh($created_day,$end_day,$strID); // điện thoại có màn hình cảm ứng
//            }
            $data1 = Business_Addon_UsersProducts::getInstance()->count_bbmh_by_cheap($created_day,$end_day,0); // điện thoại có màn hình cảm ứng
            $str_cated_ID ="174,263,439"; // máy tính bảng trừ ipab pro,trừ ipad mini 4
            $data2 = Business_Addon_UsersProducts::getInstance()->count_bbmh_by_cateid($created_day,$end_day,$str_cated_ID);  // máy tính bảng
            if($data1 != NULL){
                $cProduct = array_merge($cProduct,$data1);
            }
            if($data2 != NULL){
                $cProduct = array_merge($cProduct,$data2);
            }
       }
       
       if($type==5){ // BHVIP
            $this->_helper->viewRenderer('index7');
            
            $cated_id4 =1012; // bao bể màn hình
            $cProduct4 = Business_Addon_UsersProducts::getInstance()->get_list_by_cateid3($cated_id4, $created_day, $end_day);
            foreach ($cProduct4 as $v2){
                $total_bbmh[$v2["vote_id"]] += $v2["total"];
                $sum_bbmh[$v2["vote_id"]] += $v2["total_price"];
                
                $total_bbmh_user[$v2["vote_id"]][$v2["id_users"]] = $v2["total"];
                $sum_bbmh_user[$v2["vote_id"]][$v2["id_users"]] = $v2["total_price"];
                $str_userid[] = $v2["id_users"];
                $list_user_by_store[$v2["vote_id"]][] = $v2["id_users"];
            }
            
            $list_may_bhvip = Business_Ws_ProductsItem::getInstance()->get_list_bhvip(1);
            foreach ($list_may_bhvip as $bvip){ 
                $array_itemid_bhvip[] = $bvip["itemid"];
            }
            if($array_itemid_bhvip){
                $str_itemid_bhvip = implode(",", $array_itemid_bhvip);
                $cProduct = Business_Addon_UsersProducts::getInstance()->count_bbmh_by_products_id($created_day, $end_day,$str_itemid_bhvip);
            }
        }
       
        $this->view->type = $type;
        $this->view->total_bbmh = $total_bbmh;
        $this->view->sum_bbmh = $sum_bbmh;
        $this->view->total_bbmh_user = $total_bbmh_user;
        $this->view->sum_bbmh_user = $sum_bbmh_user;
        if($type==0){
            $cProduct = Business_Addon_UsersProducts::getInstance()->count_apple($created_day,$end_day,1);
            
            $ccSetup = Business_Addon_AppServices::getInstance()->getCountAppByVote(4, $created_day,$end_day);
            foreach ($ccSetup as $items){
                if($items["check_kh"] ==1){
                    $_cSetup_hnam[$items["vote_id"]] +=$items["total"];
                }
                if($items["check_kh"] ==2){
                    $_cSetup_other[$items["vote_id"]] +=$items["total"];
                }
            }
        }
        
        foreach ($cProduct as $__items){
            $_cProduct[$__items["vote_id"]] += $__items["total"];
        }
        $this->view->cProducts = $_cProduct;
        
        
        $this->view->cSetup_hnam = $_cSetup_hnam;
        $this->view->cSetup_other = $_cSetup_other;
        
        
        
        
        $this->view->list_vote = $list_vote;
        $__month1 = date('m',  strtotime($end_day));
        $__year = date('Y',  strtotime($end_day));
        $__month2 = $__month1-1;
        $__month3 = $__month1-2;
        $__month4 = $__month1-3;
        $__month = $__month2.",".$__month3.",".$__month4;
        $cProduct2 = array();
        if($type==0){
            $tk = Business_Addon_UsersProducts::getInstance()->get_list_by_cateid3(865, $created_day, $end_day);
            
//            $tk = $_sev->getTkListByDay($created_day,$end_day,4,1);
            $total_sum = array();
            $total_count = array();
            foreach ($tk as $item){
                $total_sum[$item["vote_id"]] += $item["total_price"];
                $total_count[$item["vote_id"]] += $item["total"];
                $sum_store_user[$item["vote_id"]][$item["id_users"]] = $item["total_price"];
                $total_store_user[$item["vote_id"]][$item["id_users"]] = $item["total"];
                $str_userid[] = $item["id_users"];
                
                $list_user_by_store[$item["vote_id"]][] = $item["id_users"];
            }
            
            $this->view->total_sum = $total_sum;
            $this->view->total_count = $total_count;
            $this->view->items = $tk;
            
            $this->view->sum_store_user = $sum_store_user;
            $this->view->total_store_user = $total_store_user;
            
            $cProduct2 = Business_Addon_UsersProducts::getInstance()->count_apple2($__month,$__year);
            
       }
       
       if($p==2){
           echo "<pre>";
           var_dump($sum_store_user,$list_user_by_store);
           die();
       }
       if($str_userid){
            $__str_userid = implode(",", $str_userid);
            $list_user = Business_Common_Users::getInstance()->getListById($__str_userid);
            foreach ($list_user as $user){
                $l_user[$user["parentid"]][] = $user;
                $lfullname[$user["userid"]] = $user["fullname"];
            }
        }
        $this->view->list_user = $l_user;
        $this->view->lfullname = $lfullname;
        $this->view->list_user_by_store = $list_user_by_store;
       if($type==1){
           $str_cated_ID ="466,586,587,622"; // máy tính bảng trừ ipab pro,trừ ipad mini 4
           if($__strID != NULL){
                $strID = implode(",", $__strID);
                $data3 = Business_Addon_UsersProducts::getInstance()->count_bbmh_by_month($__month,$__year,$strID); // điện thoại có màn hình cảm ứng
            }
            $data4 = Business_Addon_UsersProducts::getInstance()->count_bbmh_by_cateid_month($__month,$__year,$str_cated_ID);  // máy tính bảng
            if($data3 != NULL){
                $cProduct2 = array_merge($cProduct2,$data3);
            }
            if($data4 != NULL){
                $cProduct2 = array_merge($cProduct2,$data4);
            }
       }
       
       
       foreach ($cProduct2 as $v){
            $total_thamkhao[$v["vote_id"]] += $v["total"];
        }
       $this->view->total_thamkhao = $total_thamkhao;
       if($p==1){
           echo "<pre>";
           var_dump($data3,$data4,$total_thamkhao);
           die();
       }
       if($p==1){
           echo "<pre>";
           var_dump($cProduct3,$total_bbmh,$sum_bbmh);
           die();
       }
       $access = 0;
       if($this->get_user_db()){
           $access = 1;
       }
       $this->view->access = $access;
       
       
    }
    public function get_user_db(){
        $username =$this->_identity["username"];
        if($username =="hnam_thinhtm" || $username =="hnam_hanhtd"){
            return true;
        }
        return FALSE;
    }

    public function bbmhAction() {
        $_option = Business_Addon_Options::getInstance();
        $_sev = Business_Addon_Services::getInstance();
        $idregency = (int)$this->_identity["idregency"];
        $this->view->idregency = $idregency;
        
        $this->_helper->viewRenderer('index2');
        $d=date('Y-m-d');
        $k=$_option->pre_date($d, 1); 
        $nows = date('F j, Y',  strtotime($k));
        $start_end           = $this->_request->getParam("start_end");
        if($start_end ==null){
           $start_end = $nows." - ".date("F j, Y"); 
        }
        $this->view->start_end = $start_end;
        $created_day  = $_option->getStartDate($start_end);
        $end_day  = $_option->getEndDate($start_end);
        $this->view->start = $created_day;
        $this->view->end = $end_day;
        
        $this->view->storeid = $this->_identity["parentid"];
        $user = Business_Common_Users::getInstance();
        $vote_id = $this->_request->getParam("vote_id");
        $this->view->vote_id = $vote_id;
        $list_vote = $user->getListByUname(FALSE);
        $_cProduct = array();
        $_cSetup_hnam = array();
        $_cSetup_other = array();
        
        $cProduct = Business_Addon_UsersProducts::getInstance()->count_apple($created_day,$end_day,1);
        foreach ($cProduct as $__items){
            $_cProduct[$__items["vote_id"]] = $__items["total"];
        }
        $this->view->cProducts = $_cProduct;
        
        $ccSetup = Business_Addon_AppServices::getInstance()->getCountAppByVote($section=4, $created_day,$end_day);
        foreach ($ccSetup as $items){
            if($items["check_kh"] ==1){
                $_cSetup_hnam[$items["vote_id"]] +=$items["total"];
            }
            if($items["check_kh"] ==2){
                $_cSetup_other[$items["vote_id"]] +=$items["total"];
            }
        }
        $this->view->cSetup_hnam = $_cSetup_hnam;
        $this->view->cSetup_other = $_cSetup_other;
        
        
        
        
        $this->view->list_vote = $list_vote;
        
        
        $tk = $_sev->getTkListByDay($created_day,$end_day,4,1);
        $total_sum = array();
        $total_count = array();
        foreach ($tk as $item){
            $total_sum[$item["vote_id"]] = $item["total_price"];
            $total_count[$item["vote_id"]] = $item["total"];
        }
        $this->view->total_sum = $total_sum;
        $this->view->total_count = $total_count;
        $this->view->items = $tk;
       $__month1 = date('m',  strtotime($end_day));
       $__year = date('Y',  strtotime($end_day));
       $__month2 = $__month1-1;
       $__month3 = $__month1-2;
       $__month4 = $__month1-3;
       $__month = $__month2.",".$__month3.",".$__month4;
       $cProduct2 = Business_Addon_UsersProducts::getInstance()->count_apple2($__month,$__year);
       foreach ($cProduct2 as $v){
           $total_thamkhao[$v["vote_id"]] += $v["total"];
       }
       $this->view->total_thamkhao = $total_thamkhao;
       $p = (int)$this->_request->getParam("pp");
       if($p==1){
           echo "<pre>";
           var_dump($cProduct2,$total_thamkhao);
           die();
       }
    }
}
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
    public function updateimeinote8Action(){
        $product_ids = 12281;
        $itemid_title = 10566;
        $enabled = 2;
        $__bs = Business_Addon_UsersProducts::getInstance();
        $list = $__bs->get_list_by_productsid2(12281);
        foreach ($list as $val2){
            $imei[$val2["id_addon_user"]] = $val2["imes"];
            $array_idaddonuser[] = $val2["id_addon_user"];
        }
        if($array_idaddonuser){
            $str_idaddonuser = implode(",", $array_idaddonuser);
            $list2 = Business_Addon_AddonPromotion::getInstance()->get_list_by_product_ids_itemid_title_enabled($str_idaddonuser,$product_ids, $itemid_title, $enabled);
            foreach ($list2 as $val){
                $autoid = $val["autoid"];
                $id_addon_user = $val["id_addon_user"];
                $query = "update addon_user_promotion set imei = '$imei[$id_addon_user]' where autoid = $autoid";
                Business_Addon_UsersProducts::getInstance()->excute($query);
                
                echo "<pre>";
                var_dump($autoid);
            }
        }
        die();
    }
    public function add_note8($detail){
        $__option = Business_Addon_Options::getInstance();
        if((int)$detail["bill_no"] ==0){
            $data_user = Business_Addon_Users::getInstance()->getDetailByID($detail["id_addon_user"]);
            $id_addon_user = $__option->copy_addon_user($data_user); 
        }else{
            $id_addon_user = $detail["bill_no"];
        }

    if($id_addon_user >0){
            $sdata["id_addon_user"] = $id_addon_user;
            $sdata["products_id"] = $detail["itemid_title"];
            $detail_product = Business_Ws_ProductsItem::getInstance()->get_detail($detail["itemid_title"]);
            $sdata["products_name"] = $detail_product["title"];
            $sdata["products_price"] = 0;
            $____price = $detail_product["price"];
            $flag=2;
            if($____price ==0){
                $____price = $detail_product["original_price"];
                $flag=1;
            }
            $sdata["products_price_cost"] = $____price;
            $sdata["create_date"] = date('Y-m-d H:i:s');
            $sdata["bno"] = 3;
            $sdata["bill_no"] = $detail["id_addon_user"];
            $sdata["productsid"] = $detail_product["productsid"];
            $sdata["cated_id"] = $detail_product["cateid"];
            $sdata["vote_id"] = $detail["storeid"];
            $sdata["fullname_addon"] = $data_user["fullname"];
            $sdata["phone_addon"] = $data_user["phone"];
            $sdata["is_actived"] = 1;
            $sdata["id_users"] = $this->_identity["userid"];
            $sdata["isync"] = 2;
            $sdata["flag"] = $flag;
            $sdata["reduction_phone"] = 'Bán nợ từ bill '.$detail["id_addon_user"];
            $sdata["block"] = 1;
            $sdata["imes"] = $detail["imei"];
            
            $_detail_bp = Business_Addon_MappingStore::getInstance()->get_detail_by_storeid($detail["storeid"]);
            
            $sdata["ma_vt"] = 'DV.BHMR.MTB.2025';
            $sdata["ma_bp"] = $_detail_bp["id_fast_bp"];
            $sdata["ma_kho"] = $_detail_bp["id_fast_bp"].".C.NEWX";

//            $lastid = Business_Addon_UsersProducts::getInstance()->insert($sdata);
        echo "<pre>";
        var_dump($sdata);
            $data["bill_no"] = (int)$id_addon_user;
            $data["ispay"] = 2;
            $data["isync"] = 2;
//            Business_Addon_AddonPromotion::getInstance()->update($detail["autoid"], $data);

        }
//        return $lastid;
    }

    public function updatebhnote8Action(){
        $product_ids = 12281;
        $itemid_title = 10566;
        $enabled = 2;
        $__bs = Business_Addon_UsersProducts::getInstance();
        $list = $__bs->get_list_by_productsid2(12281);
        foreach ($list as $val2){
            $array_idaddonuser[] = $val2["id_addon_user"];
        }
        if($array_idaddonuser){
            $str_idaddonuser = implode(",", $array_idaddonuser);
            $list2 = Business_Addon_AddonPromotion::getInstance()->get_list_by_product_ids_itemid_title_enabled($str_idaddonuser,$product_ids, $itemid_title, $enabled);
            foreach ($list2 as $val){
                $d= $this->add_note8($val);
                echo "<pre>";
                var_dump($d);
            }
        }
        die();
    }
public function updateactivetargetthumaycuAction(){
    $__bs = Business_Addon_Usedphone::getInstance();
    $list = $__bs->get_list_by_auid();
    foreach ($list as $val){
        $array_auid[] = $val["auid"];
        $price1[$val["auid"]] = $val["price"];
    }
    if($array_auid){
        $str_auid = implode(",", $array_auid);
        $list2 = Business_Addon_UsersProducts::getInstance()->get_list_by_autoid2($str_auid);
        foreach ($list2 as $val2){
            $price2[$val2["autoid"]] = $val2["products_price"] - $val2["money_voucher"];
        }
    }
    
    foreach ($list as $v){
        $autoid = $v["auid"];
        if($price1[$autoid] == $price2[$autoid]){
            $query = "update addon_usedphone set active_target =0 where auid = $autoid";
            Business_Addon_UsersProducts::getInstance()->excute($query);
            echo "<pre>";
            var_dump($autoid);
        }
    }
    echo "<pre>";
    var_dump($price1,$price2,$str_auid);
    die();
}
public function listtotalAction(){
    $__bs = Business_Addon_Totaltarget::getInstance();
    $storeid           = $this->_request->getParam("storeid");
    $is_active           = $this->_request->getParam("is_active");
    if($is_active==NULL){
        $is_active=-1;
    }
    $month           = $this->_request->getParam("month");
    if($month==NULL){
        $month = (int)date('m');
    }
    $year           = $this->_request->getParam("year");
    if($year==NULL){
        $year = (int)date('Y');
    }
    $this->view->month = $month;
    $this->view->year = $year;
    $list = $__bs->get_list_by_userid_month_year($storeid, $month, $year);
    foreach ($list as $val){
        $array_userid[] = $val["userid"];
    }
    $array_storename = array();
    $array_fullname = array();
    $active = array();
    $list_store = Business_Common_Users::getInstance()->getListByUname(FALSE);
    foreach ($list_store as $store){
        $storename2[$store["userid"]] = $store["storename"];
    }
    $this->view->storename2 = $storename2;
    $this->view->list_store = $list_store;
    
    if($array_userid){
        $str_userid = implode(",", $array_userid);
        $list_user = Business_Common_Users::getInstance()->getListById($str_userid);
        foreach ($list_user as $val2){
            $__uid = $val2["userid"];
            $array_fullname[$__uid] = $val2["fullname"];
            $active[$__uid] = $val2["is_actived"];
        }
    }
    $qq = $_REQUEST["qq"];
    if($qq==1){
        echo "<pre>";
        var_dump($array_userid,$array_fullname);
        die();
    }
    $this->view->storeid = $storeid;
    $this->view->array_fullname2 = $array_fullname;
    $this->view->active = $active;
    $this->view->is_active = $is_active;
    
    foreach ($list as $val){
        $__uid2 = $val["userid"];
        if($active[$__uid2]==1){
            $list_active[] = $val;
        }else{
            $list_not_active[] = $val;
        }
    }
    if($is_active==1){
        $list = $list_active;
    }
    if($is_active==0){
        $list = $list_not_active;
    }
    $this->view->list = $list;
}

public function tyle_android($total_count_dv,$count_android,$idregency){
    $pesent_charge_ios = ($total_count_dv / $count_android) * 100;
    $nv = 0;
    $nvkt = 0;
    $ql = 0;
    if (round($pesent_charge_ios, 2) >= 12) { // đạt
        $nv = 5 / 100;
        $nvkt = 35 / 100;
        $ql = 15 / 100;
    }
    if($idregency==10){
        return $nv;
    }
    if($idregency==11 || $idregency==14){
        return $ql;
    }
    if($idregency==12){
        return $nvkt;
    }
}
public function tyle_bbmh($total_count_dv,$count_android,$idregency){
    $pesent_charge_ios = ($total_count_dv / $count_android) * 100;
    $nv = 0;
    $ql = 0;
    if(round($pesent_charge_ios, 2) >10){
        if (round($pesent_charge_ios, 2) <= 15) {
            $nv = 3.5 / 100;
            $ql = 1.5 / 100;
        } else {
            if (round($pesent_charge_ios, 2) < 20) {
                $nv = 7 / 100;
                $ql = 3 / 100;
            } else {
                $nv = 10.5 / 100;
                $ql = 4.5 / 100;
            }
        }
    }
    if($idregency==10){
        return $nv;
    }
    if($idregency==11 || $idregency==14){
        return $ql;
    }
}
public function tyle_bbmr24($total_count_dv,$count_android,$idregency){
    $pesent_charge_ios = ($total_count_dv / $count_android) * 100;
    $nv = 0;
    $ql = 0;
    if (round($pesent_charge_ios, 2) >= 10) {
        $nv = 8.4 / 100;
        $ql = 3.6 / 100;
    }
    if($idregency==10){
        return $nv;
    }
    if($idregency==11 || $idregency==14){
        return $ql;
    }
}
public function tyle_bbmc($total_count_dv,$count_android,$idregency){
    $pesent_charge_ios = ($total_count_dv / $count_android) * 100;
    $nv = 0;
    $ql = 0;
    if (round($pesent_charge_ios, 2) >= 13) {
        $nv = 7 / 100;
        $ql = 3 / 100;
    }
    if($idregency==10){
        return $nv;
    }
    if($idregency==11 || $idregency==14){
        return $ql;
    }
}
public function tyle_ios($total_count_dv,$count_apples,$idregency){
    $pesent_charge_ios = ($total_count_dv / $count_apples) * 100;
    $nv_ios = 0;
    $nvkt_ios = 0;
    $ql_ios = 0;
    if (round($pesent_charge_ios, 2) >= 30) { // đạt
        if (round($pesent_charge_ios, 2) <= 45) {
            $nv_ios = 5 / 100;
            $nvkt_ios = 35 / 100;
            $ql_ios = 15 / 100;
        } else {
            $nv_ios = 7 / 100;
            $nvkt_ios = 40 / 100;
            $ql_ios = 18 / 100;
        }
    }
    if($idregency==10){
        return $nv_ios;
    }
    if($idregency==11 || $idregency==14){
        return $ql_ios;
    }
    if($idregency==12){
        return $nvkt_ios;
    }
}
public function money_ios_mb($total_count_dv,$count_apples,$list_store_by_user,$sum_dv_user,$userid){
    $ios = 865;
    foreach ($list_store_by_user[$ios][$userid] as $storeid){
        $pesent_charge_ios = ($total_count_dv[$ios][$storeid] / $count_apples[$storeid]) * 100;
        $nv_ios = 0;
        $nvkt_ios = 0;
        $ql_ios = 0;
        if (round($pesent_charge_ios, 2) >= 30) { // đạt
            if (round($pesent_charge_ios, 2) <= 45) {
                $nv_ios = 5 / 100;
                $nvkt_ios = 35 / 100;
                $ql_ios = 15 / 100;
            } else {
                $nv_ios = 7 / 100;
                $nvkt_ios = 40 / 100;
                $ql_ios = 18 / 100;
            }
        }
        $money_ios += $sum_dv_user[$ios][$storeid][$userid] * $nv_ios;
    }
    return $money_ios;
}
public function money_android_mb($total_count_dv,$count_apples,$list_store_by_user,$sum_dv_user,$userid){
    $ios = 929;
    foreach ($list_store_by_user[$ios][$userid] as $storeid){
        $pesent_charge_ios = ($total_count_dv[$ios][$storeid] / $count_apples[$storeid]) * 100;
        $nv = 0;
        $nvkt = 0;
        $ql = 0;
        if (round($pesent_charge_ios, 2) >= 12) { // đạt
            $nv = 5 / 100;
            $nvkt = 35 / 100;
            $ql = 15 / 100;
        }
        $money_ios += $sum_dv_user[$ios][$storeid][$userid] * $nv;
    }
    return $money_ios;
}
public function money_bbmh_mb($total_count_dv,$count_apples,$list_store_by_user,$sum_dv_user,$userid){
    $ios = 890;
    foreach ($list_store_by_user[$ios][$userid] as $storeid){
        $pesent_charge_ios = ($total_count_dv[$ios][$storeid] / $count_apples[$storeid]) * 100;
        $nv = 0;
        if (round($pesent_charge_ios, 2) <= 15) {
            $nv = 3.5 / 100;
        } else {
            if (round($pesent_charge_ios, 2) < 20) {
                $nv = 7 / 100;
            } else {
                $nv = 10.5 / 100;
            }
        }
        $money_ios += $sum_dv_user[$ios][$storeid][$userid] * $nv;
    }
    return $money_ios;
}
public function money_bhmc_mb($total_count_dv,$count_apples,$list_store_by_user,$sum_dv_user,$userid){
    $ios = 901;
    foreach ($list_store_by_user[$ios][$userid] as $storeid){
        $pesent_charge_ios = ($total_count_dv[$ios][$storeid] / $count_apples[$storeid]) * 100;
        $nv = 0;
        if (round($pesent_charge_ios, 2) >= 13) {
            $nv = 7 / 100;
        }
        $money_ios += $sum_dv_user[$ios][$storeid][$userid] * $nv;
    }
    return $money_ios;
}
public function money_bhmr24_mb($total_count_dv,$count_apples,$list_store_by_user,$sum_dv_user,$userid){
    $ios = 905;
    foreach ($list_store_by_user[$ios][$userid] as $storeid){
        $pesent_charge_ios = ($total_count_dv[$ios][$storeid] / $count_apples[$storeid]) * 100;
        $nv = 0;
        if (round($pesent_charge_ios, 2) >= 10) {
            $nv = 8.4 / 100;
        }
        $money_ios += $sum_dv_user[$ios][$storeid][$userid] * $nv;
    }
    return $money_ios;
}
public function listuserAction(){
    $list = Business_Common_Users::getInstance()->get_list_all();
    foreach ($list as $val){
        $userid[] = $val["userid"];
    }
    $str_user = implode(",", $userid);
    echo $str_user;
    die();
}

public function indexAction(){
    $_option = Business_Addon_Options::getInstance();
    $__bs = Business_Addon_UsersProducts::getInstance();
    $ios =865; // ios    
    $bbmh =890; // bao bể màn hình    
    $bhmr24 =905; // BHMR 24 Tháng
    $bhmc = 901; // Bảo hành máy cũ
    $android = 929; // android
    $idregency = (int)$this->_identity["idregency"];
    $userid = $this->_identity["userid"];
    $storeid = $this->_identity["parentid"];
    $username = $this->_identity["username"];
    $__userid = (int)  $this->_request->getParam("userid");
    $this->view->suserid = $__userid;
    $sync = (int)  $this->_request->getParam("sync");
    $____date = date('Ymd');
    if($__userid>0){
        $userid = $__userid;
        $detail_user = Business_Common_Users::getInstance()->getDetail($userid);
        $idregency = $detail_user["idregency"];
        $storeid = $detail_user["parentid"];
        $username = $detail_user["username"];
        $token = $this->_request->getParam("token");
        $this->view->token = $token;
        
        $ztoken = md5("target_by_userid".$____date.$userid);
        if($ztoken != $token){
            die('no access');
        }
    }
    
        
    if($idregency==10){
        $this->_helper->viewRenderer('index-mb');
    }
    if($idregency==11 || $idregency==14){
        $this->_helper->viewRenderer('index-store');
    }
    if($idregency==12){
        $this->_helper->viewRenderer('index-kythuat');
        
    }
    $this->view->detail_user = $detail_user;
    $this->view->idregency = $idregency;
    $d=date('Y-m-d');
    $k=$_option->pre_date($d, 1); 
//    $nows = date('F j, Y',  strtotime($k));
//    $start_end           = $this->_request->getParam("start_end");
    $month           = $this->_request->getParam("month");
    if($month==NULL){
        $month = (int)date('m');
    }
    $year           = $this->_request->getParam("year");
    if($year==NULL){
        $year = (int)date('Y');
    }
    $this->view->month = $month;
    $this->view->year = $year;
    $startsss = $_option->getDayCreated($month, $year);
    $endss = $_option->getDayEndd($month, $year);
    $created_day = date('Y-m-d',  strtotime($startsss));
    $end_day = date('Y-m-d',  strtotime($endss))." 23:59:59";
//    echo "<pre>";
//    var_dump($created_day,$end_day);
//    die();
//    if($start_end ==null){
//       $start_end = $nows." - ".date("F j, Y"); 
//    }
//    $this->view->start_end = $start_end;
//    $created_day  = $_option->getStartDate($start_end);
//    $end_day  = $_option->getEndDate($start_end);
//    $this->view->start = $created_day;
//    $this->view->end = $end_day;
    
    $this->view->storeid = $storeid;
    
    $this->view->userid = $userid;
    $__ws_user = Business_Common_Users::getInstance();
        
//    $cated_id =865; // ios    
//    $cated_id =890; // bao bể màn hình    
//    $cated_id4 =905; // BHMR 24 Tháng
//    $cated_id4 = "901"; // Bảo hành máy cũ
//    $cated_id4 = "929"; // android
    $str_cated_id ="865,890,905,901,929";
    $cated_id_ios_android = "865,929";
    //android
    $count_android1 = Business_Addon_UsersProducts::getInstance()->count_bbmh_by_cheap($created_day,$end_day,0); // điện thoại có màn hình cảm ứng
    $str_cated_ID ="174,263,439"; // máy tính bảng trừ ipab pro,trừ ipad mini 4
    $count_android2 = Business_Addon_UsersProducts::getInstance()->count_bbmh_by_cateid($created_day,$end_day,$str_cated_ID);  // máy tính bảng
    
    $count_android = array();
    if($count_android1 != NULL){
        $count_android = array_merge($count_android,$count_android1);
    }
    if($count_android2 != NULL){
        $count_android = array_merge($count_android,$count_android2);
    }
    foreach ($count_android as $__items){
        $count_androids[$__items["vote_id"]] += $__items["total"];
    }
    $this->view->count_androids = $count_androids;
    // end android
    
    //ios
    $count_apple = $__bs->count_apple($created_day,$end_day,1); // số lượng apple bán ra
    foreach ($count_apple as $__items){
        $count_apples[$__items["vote_id"]] += $__items["total"];
    }
    
    
    $this->view->count_apples = $count_apples;
    //end ios
    if($idregency==10 || $idregency==11 || $idregency==14){
        // bao bể màn hình
        $count_bbmh1 = Business_Addon_UsersProducts::getInstance()->count_bbmh_by_cheap_bbmh($created_day,$end_day,0); // điện thoại có màn hình cảm ứng
//        $not_cated_ID ="174"; // máy tính bảng trừ ipab pro,trừ ipad mini 4
//        $count_bbmh2 = Business_Addon_UsersProducts::getInstance()->count_bbmh_by_cheap_bbmh2($created_day,$end_day,$not_cated_ID);  // máy tính bảng
        $count_bbmh = array();
        if($count_bbmh1 != NULL){
            $count_bbmh = array_merge($count_bbmh,$count_bbmh1);
        }
//        if($count_bbmh2 != NULL){
//            $count_bbmh = array_merge($count_bbmh,$count_bbmh2);
//        }
        foreach ($count_bbmh as $__items){
            $count_bbmhs[$__items["vote_id"]] += $__items["total"];
        }
        $this->view->count_bbmhs = $count_bbmhs;
        // end bao bể màn  hình
        // bảo hành mở rộng 24
        $___pid="3,5";
        $count_bhmr24 = Business_Addon_UsersProducts::getInstance()->count_bbmh_by_productsid($created_day, $end_day,$___pid,$__type=3);
        foreach ($count_bhmr24 as $__items){
            $count_bhmr24s[$__items["vote_id"]] += $__items["total"];
        }
        
        $this->view->count_bbmr24s = $count_bhmr24;
        // end bảo hành mở rộng
        //bảo hành máy cũ 901
        $count_bhmc = Business_Addon_UsersProducts::getInstance()->get_list_by_cateid3(53,$created_day, $end_day);
        foreach ($count_bhmc as $__items){
            $count_bhmcs[$__items["vote_id"]] += $__items["total"];
        }
        $this->view->count_bhmcs = $count_bhmcs;
        
    }
    
    $count_dv = $__bs->get_list_by_cateid_new($str_cated_id, $created_day, $end_day); // số lượng gói dich bán ra
    $total_sum_dv = array();
    $total_count_dv = array();
    foreach ($count_dv as $item){
        
        $total_sum_dv[$item["cated_id"]][$item["vote_id"]] += $item["total_price"];
        $total_count_dv[$item["cated_id"]][$item["vote_id"]] += $item["total"];
        
        $sum_dv_user[$item["cated_id"]][$item["vote_id"]][$item["id_users"]] = $item["total_price"];
        $total_dv_user[$item["cated_id"]][$item["vote_id"]][$item["id_users"]] = $item["total"];
        
        $str_userid[$item["cated_id"]][] = $item["id_users"];
        $list_store_by_user[$item["cated_id"]][$item["id_users"]][] = $item["vote_id"];
    }
    $this->view->total_sum_dv = $total_sum_dv;
    $this->view->total_count_dv = $total_count_dv;
    
    
    $this->view->sum_dv_user = $sum_dv_user;
    $this->view->total_dv_user = $total_dv_user;
    $this->view->list_store_by_user = $list_store_by_user;
    
    $str_idregency ="11,14";// cửa hàng trưởng 11, cửa hàng phó 14
    $list_quanly = $__ws_user->get_list_by_kh($storeid, $str_idregency);
    $total_quanly = count($list_quanly);
    $this->view->total_quanly = $total_quanly;
//    echo "<pre>";
//    var_dump($sum_dv_user[865][$storeid][$userid]);
//    die();
//    
    
    //android
    $tyle_android = $this->tyle_android($total_count_dv[$android][$storeid], $count_androids[$storeid],$idregency);
    //ios
    $tyle_ios = $this->tyle_ios($total_count_dv[$ios][$storeid], $count_apples[$storeid],$idregency);
    //bbmh
    $tyle_bbmh = $this->tyle_bbmh($total_count_dv[$bbmh][$storeid], $count_bbmhs[$storeid],$idregency);
    $tyle_bhmr24 = $this->tyle_bbmr24($total_count_dv[$bhmr24][$storeid], $count_bhmr24s[$storeid],$idregency);
    
    $tyle_bhmc = $this->tyle_bbmc($total_count_dv[$bhmc][$storeid], $count_bhmcs[$storeid],$idregency);
    
    if($idregency==10){
        $money_ios = $this->money_ios_mb($total_count_dv, $count_apples,$list_store_by_user,$sum_dv_user,$userid);
        $money_android = $this->money_android_mb($total_count_dv, $count_androids,$list_store_by_user,$sum_dv_user,$userid);
        $money_bbmh = $this->money_bbmh_mb($total_count_dv, $count_bbmhs,$list_store_by_user,$sum_dv_user,$userid);
        $money_bhmr24 = $this->money_bhmr24_mb($total_count_dv, $count_bhmr24s,$list_store_by_user,$sum_dv_user,$userid);
        $money_bhmc = $this->money_bhmc_mb($total_count_dv, $count_bhmcs,$list_store_by_user,$sum_dv_user,$userid);
        
    }
    if($idregency==11 || $idregency==14){
        $money_ios = ($total_sum_dv[$ios][$storeid]* $tyle_ios)/$total_quanly;
        $money_android = ($total_sum_dv[$android][$storeid]* $tyle_android)/$total_quanly;
        $money_bbmh = ($total_sum_dv[$bbmh][$storeid]* $tyle_bbmh)/$total_quanly;
        $money_bhmr24 = ($total_sum_dv[$bhmr24][$storeid]* $tyle_bhmr24)/$total_quanly;
        $money_bhmc = ($total_sum_dv[$bhmc][$storeid]* $tyle_bhmc)/$total_quanly;
        
    }
    
    if($idregency==12){
        $list_ios_android = $__bs->get_list_id_by_cateid($cated_id_ios_android, $created_day, $end_day);
        foreach ($list_ios_android as $ios_and){
            $arr_id_addon_user[] = $ios_and["id_addon_user"];
            $total_dv_ios_android[$ios_and["cated_id"]][] = $ios_and;
        }
        if($arr_id_addon_user){
            $str_id_addon_user = implode(",", $arr_id_addon_user);
            $list_service = Business_Addon_AppServices::getInstance()->get_count_by_billid($str_id_addon_user, 3);
            foreach ($list_service as $sv){
                $sum_sv_user[$sv["type"]][$sv["userid"]] += $sv["sum"];
            }
        }
        $this->view->sum_sv_user = $sum_sv_user;
        $this->view->total_dv_ios_android = $total_dv_ios_android;
        
        $money_ioss = $total_sum_dv[$ios][$storeid]* $tyle_ios;
        $money_ios = ($sum_sv_user[4][$userid]/$total_sum_dv[$ios][$storeid])*$money_ioss; //4 là ios
        
        $money_androidss = $total_sum_dv[$android][$storeid]* $tyle_android;
        $money_android = ($sum_sv_user[11][$userid]/$total_sum_dv[$android][$storeid])*$money_androidss; //11 là android
        
        // phí nhân công
        $list_phinhancong = Business_Addon_UsersProducts::getInstance()->get_count_by_cheap($storeid,"3", $month, $year,0);
        foreach ($list_phinhancong as $vp){
            $arr_total_phinhancong[$vp["vote_id"]] = $vp["total"];
        }
        $this->view->arr_total_phinhancong = $arr_total_phinhancong;
        $this->view->list_phinhancong = $list_phinhancong;
        
        
    }
    $test_may = Business_Addon_Usedphone::getInstance()->get_list_test($userid,$month,$year);
    $total_test = $test_may["total"]*10000;
    $this->view->total_test = $total_test;


    $phinhancongtestmaythu = Business_Addon_Guarantee::getInstance()->get_list_creator_month_year($username,$month,$year);
    if($phinhancongtestmaythu){
        $total_baohanh=0;
        $count_money_dvsc1=0;
        $count_money_dvsc2=0;
        foreach ($phinhancongtestmaythu as $phi){
            if($phi["money_dvsc"]>0 && $phi["money_hnam"]==0 && $phi["ok"]==1){ //x30.000
                $count_money_dvsc1++;
            }else{ // x5k
                $count_money_dvsc2++;
            }
        }
        $total_baohanh = $count_money_dvsc1*30000 + $count_money_dvsc2*5000;
    }
    $this->view->total_baohanh = $total_baohanh;
//    if($userid==534){
//       echo "<pre>";
//        var_dump($money_ios,$userid,$tyle_ios);
//        die(); 
//    }
    
    $this->view->money_ios = $money_ios;
    $this->view->money_android = $money_android;
    $this->view->money_bbmh = $money_bbmh;
    $this->view->money_bhmr24 = $money_bhmr24;
    $this->view->money_bhmc = $money_bhmc;
    
    
    // doanh số
    
    $_recipeid = Business_Addon_Recipe::getInstance();
        $detail_recipe = $_recipeid->getDetail($storeid, $month, $year);
        
        $total_money = $detail_recipe["total_money"];
        if($detail_recipe !=null){
            $recipeid = $detail_recipe["recipeid"];
            $slist_reciped = $_option->getCTTarget($recipeid);
            $slist_reciped2 = $_option->getCTTargetPk($recipeid);
            
            $total_money2 = $detail_recipe["total_money_pk"];
        }
        $this->view->detail_recipe = $detail_recipe;
        $this->view->slist_reciped = $slist_reciped;
        $this->view->slist_reciped2 = $slist_reciped2;
        $this->view->total_money = $total_money;
        $this->view->total_money2 = $total_money2;
    
    $_target = Business_Common_Target::getInstance();
        $list = $_target->getList($month, $year,$storeid);
        foreach ($list as &$items2){
            if($items2["flag"] ==1){
                $money_cty[$items2["type"]] = $items2["money"];
                $total_cty[$items2["type"]] = $items2["total"];
                $__id_cty[$items2["type"]] = $items2["id"];
            }
            if($items2["flag"] ==2){
                $money_hnam[$items2["type"]] = $items2["money"];
                $total_hnam[$items2["type"]] = $items2["total"];
                $__id_hnam[$items2["type"]] = $items2["id"];
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
            
//            $_member = $__ws_user->getListById($listmb);
            $_member = $__ws_user->getListById($userid);
            $this->view->member = $_member;
        }
        
        
        $_ptype      = $_option->getCateTargetHnamNew();
        $this->view->ptype = $_ptype;
        
        $arr_mb = array();
        $groupuser = Business_Common_Users::getInstance()->countUserByStoreid($storeid);
        
        foreach ($groupuser as &$items3){
            $arr_mb[$items3["idregency"]] = $items3["total"];
            $arr_mb[0] = 1;
        }
        $this->view->arr_mb= $arr_mb;
        
            //phuj kien
        $list_pk = Business_Addon_UsersProducts::getInstance()->get_thong_ke2($storeid,4, $month, $year);
        
        foreach ($list_pk as $val){
            $vote_id = $val["vote_id"];
            $stotal_pk = $val["total"];
            $sid_users = $val["id_users"];
            $tyle = $this->get_phan_tram($stotal_pk,$vote_id );
            
            $list_thuong_pk[$vote_id][$sid_users][$stotal_pk] += $val["sum"]*$tyle/100; // đã thưởng theo từng món + chi nhánh
            $list_sum_pk[$vote_id][$sid_users][$stotal_pk] += $val["sum"]; // tổng theo từng món + chi nhánh
            
            $list_tyle[$vote_id][$stotal_pk] = $tyle;
            $__sum_vote_pk[$vote_id][$sid_users] += $val["sum"]*$tyle/100; // tổng theo chi nhánh
            $total_pk[$vote_id][$stotal_pk] += $val["total"];
            
        }
        if($pp==1){
            echo "<pre>";
            var_dump($list_thuong_pk,$list_sum_pk,$list_tyle);
            die();
        }
        
        $this->view->list_sum_pk = $list_sum_pk;
        $this->view->list_thuong_pk = $list_thuong_pk;
        $this->view->sum_vote_pk = $__sum_vote_pk;
        $this->view->list_tyle = $list_tyle;
    
}












public function settargetproductsbybrandAction(){
    $_option = Business_Addon_Options::getInstance();
    $list_hnammobile        = $_option->getCatedHnam();
    $this->view->list_hnammobile = $list_hnammobile;
    $productsid = (int)  $this->_request->getParam("productsid");
    $cated_id = (int)  $this->_request->getParam("cated_id");
    $storeid = $this->_identity["parentid"];
    if($productsid==0){
        $productsid =3;
    }
    $menus =  $_option->getMenuById($productsid);
    foreach ($menus as $val){
        $name_cated[$val["itemid"]] = $val["title"];
    }
    $this->view->name_cated = $name_cated;
    $this->view->menu = $menus;
    $this->view->cated_id = $cated_id;
    $month = (int)  $this->_request->getParam("month");
    if($month==0){
        $month = date('m');
    }

    $year = (int)  $this->_request->getParam("year");
    if($year==0){
        $year = date('Y');
    }
    if($cated_id >0){
        $list = Business_Ws_ProductsItem::getInstance()->getListByCate($cated_id);
        $slist = Business_Addon_TargetByProducts::getInstance()->get_list_by_cated_id($cated_id,$month,$year);
        foreach ($slist as $sl){
            $array_p[$sl["itemid"]] = $sl["money"];
            $array_month[$sl["itemid"]] = $sl["month"];
            $array_year[$sl["itemid"]] = $sl["year"];
            $array_datetime[$sl["itemid"]] = $sl["datetime"];
        }
    }
    $this->view->array_datetime = $array_datetime;
    $this->view->array_month = $array_month;
    $this->view->array_year = $array_year;
    $this->view->array_p = $array_p;
    $this->view->list = $list;
    $this->view->month = $month;
    $this->view->year = $year;
    
}
public function savesettargetproductsbybrandAction(){
    $this->_helper->Layout()->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);
    
    $_option = Business_Addon_Options::getInstance();
    $list_hnammobile        = $_option->getCatedHnam();
    $this->view->list_hnammobile = $list_hnammobile;
    $productsid = (int)  $this->_request->getParam("productsid");
    $cated_id = (int)  $this->_request->getParam("cated_id");
    $storeid = $this->_identity["parentid"];
    if($productsid==0){
        $productsid =3;
    }
    $menus =  $_option->getMenuById($productsid);
    foreach ($menus as $val){
        $name_cated[$val["itemid"]] = $val["title"];
    }
    $this->view->name_cated = $name_cated;
    $this->view->menu = $menus;
    $this->view->cated_id = $cated_id;
    if($cated_id >0){
        $list = Business_Ws_ProductsItem::getInstance()->getListByCate($cated_id);
        
    }
    $this->view->array_p = $array_p;
    $this->view->list = $list;
    $month = (int)  $this->_request->getParam("month");
    if($month==0){
        $month = date('m');
    }

    $year = (int)  $this->_request->getParam("year");
    if($year==0){
        $year = date('Y');
    }
    $array_itemid = $this->_request->getParam('array_itemid');
    foreach ($array_itemid as $val){
        $array_item[] = $val;
    }
    if($array_item){
        $strID = implode(",", $array_item);
        $list2 = Business_Addon_TargetByProducts::getInstance()->get_list_by_itemid($strID,$month,$year);
        foreach ($list2 as $item){
            $array_check[$item["itemid"]] = $item["money"];
        }
    }
    foreach ($array_itemid as $val){
        $price = str_replace(",", "", $_REQUEST[$val]);
        $data["money"] = (int)$price;
        $data["itemid"] = $val;
        $data["productsid"] = $productsid;
        $data["cated_id"] = $cated_id;
        $data["month"] = $month;
        $data["year"] = $year;
        
        if($array_check[$val] >0){ //update
            $data["datetime_update"]  = date('Y-m-d H:i:s');
            $data["userid_update"] = $this->_identity["userid"];
            Business_Addon_TargetByProducts::getInstance()->update_by_itemid($val,$data);
        }else{ // insert
           $data["datetime"]  = date('Y-m-d H:i:s');
            $data["userid"] = $this->_identity["userid"];
            Business_Addon_TargetByProducts::getInstance()->insert($data); 
        }
    }
    $err['id']      = "ok";
    $err['msg']     = "ok";
    $err['url']     = $_SERVER['HTTP_REFERER'];
    $ret[]          = $err;
    echo json_encode($ret);
    
    
}

public function bybrandAction(){
    $_option                    = Business_Addon_Options::getInstance();
    $list_hnammobile        = $_option->getCatedHnam();
    $this->view->list_hnammobile = $list_hnammobile;
    $ids = (int)  $this->_request->getParam("ids");
    if($ids >0){
        $detail = Business_Addon_TargetByBrand::getInstance()->get_detail($ids);
    }
    if($detail){
        $month = $detail["month"];
        $year = $detail["year"];
        $productsid = $detail["productsid"];
        $cated_id = $detail["cated_id"];
        $sl = $detail["sl"];
        $storeid = $detail["storeid"];
    }else{
        $productsid = (int)  $this->_request->getParam("productsid");
        $cated_id = (int)  $this->_request->getParam("cated_id");
        $storeid = $this->_identity["parentid"];
        if($productsid==0){
            $productsid =3;
        }
        $month = (int)  $this->_request->getParam("month");
        if($month==0){
            $month = date('m');
        }
        
        $year = (int)  $this->_request->getParam("year");
        if($year==0){
            $year = date('Y');
        }
        $bgd=0;
        $idregency = $this->_identity["idregency"];
        if($_option->isBGD($idregency)){
            $bgd =1;
            $storeid = $this->_request->getParam("storeid");
        }
    }
    
    $menus =  $_option->getMenuById($productsid);
    foreach ($menus as $val){
        $name_cated[$val["itemid"]] = $val["title"];
        $array_itemid[] = $val["itemid"];
    }
    if($array_itemid){
        $strID = implode(",", $array_itemid);
        $list3 = Business_Addon_UsersProducts::getInstance()->get_list_by_cated($month,$year,$strID,1);
        foreach ($list3 as $l3){
            $total_sale_by_itemid[$l3["vote_id"]][$l3["products_id"]] = $l3["total"];
            $total_sale[$l3["vote_id"]][$l3["cated_id"]] += $l3["total"];
            $sum_sale[$l3["vote_id"]][$l3["cated_id"]] += $l3["sum"];
            $array_cated[$l3["vote_id"]][$l3["cated_id"]][] = $l3;
        }
        $list2 = Business_Addon_TargetByProducts::getInstance()->get_list_by_cated_id($strID,$month,$year);
        foreach ($list2 as $l2){
            $price_by_itemid[$l2["itemid"]] = $l2["money"];
            
        }
    }
    $p = $this->_request->getParam("pp");
    if($p==1){
        echo "<pre>";
        var_dump($list3);
        die();
    }
    if($p==2){
        echo "<pre>";
        var_dump($list2);
        die();
    }
    if($p==3){
        echo "<pre>";
        var_dump($total_sale[203][42]);
        die();
    }
    if($p==4){
        echo "<pre>";
        var_dump($price_by_itemid);
        die();
    }
    if($p==5){
        echo "<pre>";
        var_dump($array_cated[203][42]);
        die();
    }
    if($p==6){
        echo "<pre>";
        var_dump($total_sale_by_itemid[203]);
        die();
    }
    
    $this->view->array_cated = $array_cated;
    $this->view->total_sale_by_itemid = $total_sale_by_itemid;
    $this->view->price_by_itemid = $price_by_itemid;
    $this->view->total_sale = $total_sale;
    $this->view->sum_sale = $sum_sale;
    $this->view->list3 = $list3;
    $this->view->sl = $sl;
    $this->view->menu = $menus;
    $this->view->month = $month;
    $this->view->year = $year;
    $list_store = Business_Common_Users::getInstance()->getListByUname(FALSE);
    foreach ($list_store as $store){
        $storename[$store["userid"]] = $store["storename"];
    }
    $this->view->list_store = $list_store;
    
    $list = Business_Addon_TargetByBrand::getInstance()->get_list_by_target($storeid, $month, $year, $cated_id);
    
    foreach ($list as $item){
        $total_storeid_cated[$item["storeid"]][$item["cated_id"]] = $item["sl"];
        $ids_storeid_cated[$item["storeid"]][$item["cated_id"]] = $item["id"];
        $array_menu[$item["cated_id"]] = $item["cated_id"];
    }
    $this->view->productsid = $productsid;
    $this->view->itemid = $cated_id;
    $this->view->array_menu = $array_menu;
    $this->view->total_storeid_cated = $total_storeid_cated;
    $this->view->ids_storeid_cated = $ids_storeid_cated;
    $this->view->list = $list;
    $this->view->name_cated = $name_cated;
    $this->view->storename = $storename;
    $this->view->storeid = $storeid;
    $this->view->detail = $detail;
    
    
//    echo "<pre>";
//    var_dump($menus);
//    die();
        
}
public function savebybrandAction(){
    $this->_helper->Layout()->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);
    $_option                    = Business_Addon_Options::getInstance();
    $list_hnammobile        = $_option->getCatedHnam();
    $this->view->list_hnammobile = $list_hnammobile;
    $ids = (int)  $this->_request->getParam("ids");
    $productsid = (int)  $this->_request->getParam("productsid");
    $cated_id = (int)  $this->_request->getParam("cated_id");
    if($productsid==0){
        $productsid =3;
    }
    $menus =  $_option->getMenuById($productsid);
    $this->view->menu = $menus;
    $month = (int)  $this->_request->getParam("month");
    
    if($month==0){
        $month = date('m');
    }
    $this->view->month = $month;
    $year = (int)  $this->_request->getParam("year");
    if($year==0){
        $year = date('Y');
    }
    $this->view->year = $year;
    $storeid = (int)  $this->_request->getParam("storeid");
    if($storeid ==0){
      $err["id"] ="storeid";
      $err["msg"] ="Vui lòng chọn chi nhánh";
      $ret[] = $err;
    }
    if($cated_id ==0){
      $err["id"] ="cated_id";
      $err["msg"] ="Vui lòng chọn brand";
      $ret[] = $err;
    }
    $sl = (int) $this->_request->getParam("sl");
    if($sl==0){
        $err["id"] ="sl";
        $err["msg"] ="Vui lòng nhập số lượng";
        $ret[] = $err;
    }
    if($ids==0){
        $detail = Business_Addon_TargetByBrand::getInstance()->get_detail_by_target($storeid, $month, $year, $cated_id);
        if($detail){
            $err["id"] ="sl";
            $err["msg"] ="Target này đã được tạo. Vui lòng kiểm tra lại";
            $ret[] = $err;
        }
    }
    if(count($ret) >0){
        echo json_encode($ret);
        return;
    }else{
        $data["productsid"] = $productsid;
        $data["cated_id"] = $cated_id;
        $data["storeid"] = $storeid;
        $data["month"] = $month;
        $data["year"] = $year;
        $data["sl"] = $sl;
        if($ids ==0){
            $data["datetime"] = date('Y-m-d H:i:s');
            $data["userid"] = $this->_identity["userid"];
            Business_Addon_TargetByBrand::getInstance()->insert($data);
        }else{
            $data["datetime_update"] = date('Y-m-d H:i:s');
            $data["userid_update"] = $this->_identity["userid"];
            Business_Addon_TargetByBrand::getInstance()->update($ids,$data);
        }
        $err['id']      = "ok";
        $err['msg']     = "ok";
        $err['url']     = $_SERVER['HTTP_REFERER'];
        $ret[]          = $err;
        echo json_encode($ret);
    }
}


public function thongkedanhmucAction(){
        $_option = Business_Addon_Options::getInstance();
        $__zwf_user = Business_Common_Users::getInstance();
        $start_end= $this->_request->getParam("start_end");
        if($start_end ==null){
           $start_end = date("F j, Y")." - ".date("F j, Y"); 
        }
        $this->view->start_end = $start_end;
        $start  = $_option->getStartDate($start_end);
        $end  = $_option->getEndDate($start_end);
        $this->view->start = $start;
        $this->view->end = $end;
        $productsid = (int)  $this->_request->getParam("productsid");
        if($productsid ==0){
            $productsid =4;
        }
        $idregency = $this->_identity["idregency"];
        if($idregency ==11 || $idregency ==14){
            $storeid = $this->_identity["parentid"];
        }else{
            $storeid = (int)  $this->_request->getParam("storeid");
            if($storeid ==0){
                $storeid = 12;
            }
        }
        $sum = array();
        $total = array();
        $list = Business_Addon_UsersProducts::getInstance()->get_thong_ke($storeid=0,$productsid, $start, $end);
        
        foreach ($list as $val){
            $id_users = $val["id_users"];
            $vote_id = $val["vote_id"];
            $stotal = $val["total"];
            
            if($id_users >0 && $vote_id == $storeid){
                $__str_user[] = $val["id_users"];
            }
            $tyle = $this->get_phan_tram($stotal,$vote_id );
            $sum[$vote_id][$stotal] += $val["sum"]*$tyle/100; // tổng theo từng món + chi nhánh
            
            $__sum_vote[$vote_id] += $val["sum"]*$tyle/100; // tổng theo chi nhánh
            $total[$vote_id][$stotal] += $val["total"];
            
        }
        
        echo "<pre>";
        var_dump($__sum_vote,$sum);
        die();
        
        $this->view->list = $list; 
        $this->view->sum = $sum; 
        $this->view->total = $total; 
        $this->view->storeid = $storeid; 
        
        if($__str_user != NULL){
            $__str_user1 = array_unique($__str_user);
            $struser = implode(",", $__str_user1);
            $list_user = $__zwf_user->getListById($struser);
            foreach ($list_user as $u){
                $uname[$u["userid"]] = $u["fullname"];
            }
        }
        $list_store = $__zwf_user ->getListByUname(FALSE);
        foreach ($list_store as $store){
            $storename[$store["userid"]] = $store["storename"];
        }
        $this->view->storename = $storename;
        $this->view->uname = $uname;
        $this->view->list_user = $list_user;
        $this->view->list_store = $list_store;
        $iexport = (int)  $this->_request->getParam("iexport");
        if($iexport==1){
            $this->_helper->Layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true); 
            $this->exporttkdmAction($list_user, $total, $sum, $storeid, $uname);
        }
    }
    public function get_phan_tram($stotal,$vote_id){
        
        $__option = Business_Addon_Options::getInstance();
        if($__option->storeid_vivo($vote_id)){
            $tyle = 0;
            if($stotal ==1){
                $tyle = 0.5;
            }
           if($stotal ==2){
               $tyle = 2;
           } 
           if($stotal >2){
               $tyle = 3;
           }
        }
        if($__option->storeid_orther($vote_id)){
            $tyle = 0;
            if($stotal ==1){
                $tyle = 1;
            }
           if($stotal ==2){
               $tyle = 4;
           } 
           if($stotal >2){
               $tyle = 6;
           }
        }
        return $tyle;
    }
    
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
    $pp = (int) $this->_request->getParam("pppp");
//    if($idregency ==10){
//        if($pp==0){
//            die('no  access');
//        }
//    }
//    if($idregency ==11 || $idregency ==14 ){
//        $this->_helper->viewRenderer('money-monthly');
//    }else{
//        $this->_helper->viewRenderer('money-monthly2');
//    }
    
    $stype = (int)  $this->_request->getParam("stype");
    $storeid = $this->_identity["parentid"];
    $___userid = $this->_identity["userid"];
    $bgd =0;
    if($_option->isBGD($idregency) || $idregency ==35 || $idregency ==36 || $idregency ==33){
        $storeid = (int)$this->_request->getParam("storeid");
        if($storeid ==0){
            $storeid =12;
        }
        $bgd =1;
        $this->_helper->viewRenderer('money-monthly2');
        
    }
    if($idregency ==10){
        $this->_helper->viewRenderer('money-monthly-member');
        $____fname = $this->_identity["fullname"];
        $this->view->fname = $____fname;
    }
    if($stype==1){
            $this->_helper->viewRenderer('money-monthly-admin-pk');
            if($idregency ==10){
                $this->_helper->viewRenderer('money-monthly-admin-pk-member');
            }
        }
    $this->view->bgd = $bgd;
    $this->view->suserid = $___userid;
    $this->view->stype = $stype;
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
        $total_money = $detail_recipe["total_money"];
        $often_store = $detail_recipe["often_store"];
        if($detail_recipe !=null){
            $recipeid = $detail_recipe["recipeid"];
            $slist_reciped = $_option->getCTTarget($recipeid);
            if($stype ==1){
                $slist_reciped = $_option->getCTTargetPk($recipeid);
                $total_money = $detail_recipe["total_money_pk"];
            }
        }
        
        $date_from              = $year."-".$month."-01";
        $date_to                = $year."-".$month."-".date('t', mktime(0, 0, 0, $month, 1, $year))." 23:59:59";
        
        $pp = (int)$this->_request->getParam("pp");
        if($pp==1){
            $total_thuhoitarget = $this->get_total_mcu($storeid,$date_from,$date_to);
            $this->view->total_thuhoitarget = $total_thuhoitarget;
        }
        
        $this->view->total_money = $total_money;
        
        $this->view->detail_recipe = $detail_recipe;
        $this->view->slist_reciped = $slist_reciped;
        
        $arr_mb = array();
        $groupuser = Business_Common_Users::getInstance()->countUserByStoreid($storeid);
        
        foreach ($groupuser as &$items3){
            $arr_mb[$items3["idregency"]] = $items3["total"];
            $arr_mb[0] = 1;
        }
        $this->view->arr_mb= $arr_mb;
        $this->view->storeid = $storeid;
        $list_store = $_zwfuser->getListByUname(FALSE);
        $this->view->list_store = $list_store;
        $storename = array();
        foreach ($list_store as $items1){
            $storename[$items1["userid"]] = $items1["storename"];
        }
        $this->view->storename = $storename;
        
        
        
        $_target = Business_Common_Target::getInstance();
        $list = $_target->getList($month, $year,$storeid);
        foreach ($list as &$items2){
            if($items2["flag"] ==1){
                $money_cty[$items2["type"]] = $items2["money"];
                $total_cty[$items2["type"]] = $items2["total"];
                $__id_cty[$items2["type"]] = $items2["id"];
            }
            if($items2["flag"] ==2){
                $money_hnam[$items2["type"]] = $items2["money"];
                $total_hnam[$items2["type"]] = $items2["total"];
                $__id_hnam[$items2["type"]] = $items2["id"];
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
        
        
        
        if($stype ==1){
            //phuj kien
        $list_pk = Business_Addon_UsersProducts::getInstance()->get_thong_ke2($storeid,4, $month, $year);
        
        foreach ($list_pk as $val){
            $vote_id = $val["vote_id"];
            $stotal_pk = $val["total"];
            $sid_users = $val["id_users"];
            $tyle = $this->get_phan_tram($stotal_pk,$vote_id );
            
            $list_thuong_pk[$vote_id][$sid_users][$stotal_pk] += $val["sum"]*$tyle/100; // đã thưởng theo từng món + chi nhánh
            $list_sum_pk[$vote_id][$sid_users][$stotal_pk] += $val["sum"]; // tổng theo từng món + chi nhánh
            
            $list_tyle[$vote_id][$stotal_pk] = $tyle;
            $__sum_vote_pk[$vote_id][$sid_users] += $val["sum"]*$tyle/100; // tổng theo chi nhánh
            $total_pk[$vote_id][$stotal_pk] += $val["total"];
            
        }
        if($pp==1){
            echo "<pre>";
            var_dump($list_thuong_pk,$list_sum_pk,$list_tyle);
            die();
        }
        
        $this->view->list_sum_pk = $list_sum_pk;
        $this->view->list_thuong_pk = $list_thuong_pk;
        $this->view->sum_vote_pk = $__sum_vote_pk;
        $this->view->list_tyle = $list_tyle;
        }
        
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
        
        
        $list_pk = Business_Addon_UsersProducts::getInstance()->get_thong_ke2($storeid,4, $month, $year);
        
        foreach ($list_pk as $val){
            $vote_id = $val["vote_id"];
            $stotal_pk = $val["total"];
            $sid_users = $val["id_users"];
            $tyle = $this->get_phan_tram($stotal_pk,$vote_id );
            
            $list_thuong_pk[$vote_id][$sid_users][$stotal_pk] += $val["sum"]*$tyle/100; // đã thưởng theo từng món + chi nhánh
            $list_sum_pk[$vote_id][$sid_users][$stotal_pk] += $val["sum"]; // tổng theo từng món + chi nhánh
            
            $list_tyle[$vote_id][$stotal_pk] = $tyle;
            $__sum_vote_pk[$vote_id][$sid_users] += $val["sum"]*$tyle/100; // tổng theo chi nhánh
            $total_pk[$vote_id][$stotal_pk] += $val["total"];
            
        }
        if($pp==1){
            echo "<pre>";
            var_dump($list_thuong_pk,$list_sum_pk,$list_tyle);
            die();
        }
        
        $this->view->list_sum_pk = $list_sum_pk;
        $this->view->list_thuong_pk = $list_thuong_pk;
        $this->view->sum_vote_pk = $__sum_vote_pk;
        $this->view->list_tyle = $list_tyle;
        
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
        if($storeid ==167){
            $this->_helper->viewRenderer('list-saleonline');
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
                    if($items["productsid"] ==9){ // Nuoc hoa
                        $sum_hnam[12] += $items["sum"];
                        $totals_hnam[12] += $items["total"];
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
//            $total_thuhoitarget = $this->get_total_mcu($storeid,$date_from,$date_to);
//            $this->view->total_thuhoitarget = $total_thuhoitarget;
//            echo "<pre>";
//            var_dump($total_thuhoitarget);
//            die();
        }
        
        
        //phuj kien
        $list_pk = Business_Addon_UsersProducts::getInstance()->get_thong_ke2($storeid,"4,9", $month, $year);
        
        
        //phu kien binh thuong
		$list_pk0 = Business_Addon_UsersProducts::getInstance()->get_thong_ke2($storeid,"4,9", $month, $year, $group=0);       
		//phu kien dac biet, combo 3 mon
        $list_pk1 = Business_Addon_UsersProducts::getInstance()->get_thong_ke2($storeid,"4,9", $month, $year, $group=1);
        //phu kien khong giam
        $list_pk2 = Business_Addon_UsersProducts::getInstance()->get_thong_ke2($storeid,"4,9", $month, $year, $group=2);
        
        $total_pk_bt=0;
        foreach ($list_pk0 as $val){
			$total_pk_bt += $val["sum"];
		}
		
		$total_pk_dacbiet=0;
        foreach ($list_pk1 as $val){
			$total_pk_dacbiet += $val["sum"];
		}
		
		$total_pk_khonggiam=0;
        foreach ($list_pk2 as $val){
			$total_pk_khonggiam += $val["sum"];
		}		
		
        //new function
		foreach ($list_pk0 as $val){
            $vote_id = $val["vote_id"];
            $stotal_pk = $val["total"];
            $tyle = $this->get_phan_tram($stotal_pk,$vote_id );
            
            if($stotal_pk >2){
                $list_thuong_pk[$vote_id][3]+= $val["sum"]*$tyle/100;
            }else{
                $list_thuong_pk[$vote_id][$stotal_pk] += $val["sum"]*$tyle/100; // đã thưởng theo từng món + chi nhánh
            }
            $list_sum_pk[$vote_id][$stotal_pk] += $val["sum"]; // tổng theo từng món + chi nhánh
            
            $list_tyle[$vote_id][$stotal_pk] = $tyle;
            $__sum_vote_pk[$vote_id] += $val["sum"]*$tyle/100; // tổng theo chi nhánh
            $total_pk[$vote_id][$stotal_pk] += $val["total"];            
        }
		$this->view->list_sum_pk0 = $list_sum_pk;
        $this->view->list_thuong_pk0 = $list_thuong_pk;
        $this->view->sum_vote_pk0 = $__sum_vote_pk;
        $this->view->list_tyle0 = $list_tyle;		
		unset($list_sum_pk,$list_thuong_pk,$__sum_vote_pk,$list_tyle);
		
		
        
        foreach ($list_pk as $val){
            $vote_id = $val["vote_id"];
            $stotal_pk = $val["total"];
            $tyle = $this->get_phan_tram($stotal_pk,$vote_id );
            
            if($stotal_pk >2){
                $list_thuong_pk[$vote_id][3]+= $val["sum"]*$tyle/100;
            }else{
                $list_thuong_pk[$vote_id][$stotal_pk] += $val["sum"]*$tyle/100; // đã thưởng theo từng món + chi nhánh
            }
            $list_sum_pk[$vote_id][$stotal_pk] += $val["sum"]; // tổng theo từng món + chi nhánh
            
            $list_tyle[$vote_id][$stotal_pk] = $tyle;
            $__sum_vote_pk[$vote_id] += $val["sum"]*$tyle/100; // tổng theo chi nhánh
            $total_pk[$vote_id][$stotal_pk] += $val["total"];
            $total_pk_all += $val["sum"];
            
        }
        
        $total_pk_3mon = $total_pk_all - ($total_pk_bt + $total_pk_dacbiet + $total_pk_khonggiam);
		$this->view->total_pk_3mon = $total_pk_3mon;
		$this->view->total_pk_dacbiet = $total_pk_dacbiet;
		$this->view->total_pk_khonggiam = $total_pk_khonggiam;
		
        if($pp==1){
            echo "<pre>";
            var_dump($list_thuong_pk,$list_sum_pk,$list_tyle);
            die();
        }
        
        $this->view->list_sum_pk = $list_sum_pk;
        $this->view->list_thuong_pk = $list_thuong_pk;
        $this->view->sum_vote_pk = $__sum_vote_pk;
        $this->view->list_tyle = $list_tyle;
        
        
        // phí nhân công
        $list_phinhancong = Business_Addon_UsersProducts::getInstance()->get_count_by_cheap($storeid,"3", $month, $year,0);
        foreach ($list_phinhancong as $vp){
            $arr_total_phinhancong[$vp["vote_id"]] = $vp["total"];
        }
        $this->view->arr_total_phinhancong = $arr_total_phinhancong;
        $this->view->list_phinhancong = $list_phinhancong;
        
    }
    public function saveRecipeAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $month = $this->_request->getParam("month");
        $year = $this->_request->getParam("year");
        $storeid = $this->_request->getParam("storeid");
        $recipeid = $this->_request->getParam("recipeid");
        $_total_money = $this->_request->getParam("total_money");
        $_total_money_pk = $this->_request->getParam("total_money_pk");
        $_often_store = $this->_request->getParam("often_store");
        $_often_store_pk = $this->_request->getParam("often_store_pk");
        $total_money = str_replace(",", "", $_total_money);
        $total_money_pk = str_replace(",", "", $_total_money_pk);
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
            $data["total_money_pk"] = $total_money_pk;
            $data["often_store_pk"] = $_often_store_pk;
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
    public function settylemay99Action(){
        $_zwfuser = Business_Common_Users::getInstance();
        $list_store = $_zwfuser->getListByUname(FALSE);
        $this->view->list_store =  $list_store;
        $id = (int)  $this->_request->getParam("id");
        if($id>0){
            $detail = Business_Addon_Recipe::getInstance()->getDetailById($id);
        }
        $this->view->detail = $detail;
    }

    public function saveRecipe2Action(){
        $month = $this->_request->getParam("month");
        $year = $this->_request->getParam("year");
        $storeid = (int)$this->_request->getParam("storeid");
        $tyle_may99 = $this->_request->getParam("tyle_may99");
        
        $_recipeid = Business_Addon_Recipe::getInstance();
        $ret = array();
        $arr = array();
        if($storeid ==0){
            $arr["id"] = "storeid";
            $arr["msg"] = "Vui lòng chọn chi nhánh";
            $ret[] = $arr;
        }
        $tyle_may99 = str_replace(',', '.', $tyle_may99);
        if($tyle_may99 ==NULL){
            $arr["id"] = "tyle_may99";
            $arr["msg"] = "Vui lòng nhập tỷ lệ";
            $ret[] = $arr;
        }
        if(count($ret) >0){
            for($i=0;$i<count($ret);$i++){
                $msg = $ret[$i]['msg'];
                $ids = $ret[$i]['id'];
                echo "<script>window.parent.show_msg('$msg','$ids');</script>";
                die();
            }
        }else{
            $detail_recipe = $_recipeid->getDetail($storeid, $month, $year);
            $data["tyle_may99"] = $tyle_may99;
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
            $__msg ='Lưu thành công';
            $url_rd = $_SERVER['HTTP_REFERER'];
            echo "<script>window.parent.show_success('$__msg','','$url_rd');</script>";
            die();
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
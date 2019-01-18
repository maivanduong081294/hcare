<?php

/**
 * User Admin Home Controller
 * @author: nghidv
 */
class User_Admin_ExportController extends Zend_Controller_Action {
    private $_identity;
    public function init() {
        // do something
        $auth = Zend_Auth::getInstance(); 
        $identity = $auth->getIdentity();
        $this->view->full_name = $identity->fullname;
        $this->view->user_name = $identity->username;
        if ($identity->username == null) {
            header('Location: /admin');
        }
        if ($identity != null) {
            $username = $identity->username;
            $this->view->username = $username;
        }
        $_identity = (array)$auth->getIdentity();
        $this->_identity = $_identity;
        BlockManager::setLayout('appbh');
        
    }
    
    public function productsnosaleAction(){
        $_users_products = Business_Addon_UsersProducts::getInstance();
        $_option = Business_Addon_Options::getInstance();
        $list_hnammobile        = $_option->getCatedHnam();
        $this->view->list_hnammobile = $list_hnammobile;
        $list_catedhnammobile        = $_option->getCatedHnammobile();
        $this->view->list_flag  = $list_catedhnammobile;
        $productsid = (int)  $this->_request->getParam("productsid");
        $this->view->productsid= $productsid;
        $storeid = (int)  $this->_request->getParam("storeid");
        $this->view->storeid = $storeid;
        $cated_id = (int)  $this->_request->getParam("cated_id");
        
        $flag = (int)  $this->_request->getParam("flag");
        $this->view->flag = $flag;
        if($productsid ==0){
            $productsid=3;
        }
        $menus = $_option->getMenuById($productsid);
        if($productsid ==53){
            $cated_id = 53;
            $menus = $_option->get_khomaycu();
            $productsid =3;
        }
        
        
        $this->view->menu  = $menus;
        $this->view->cated_id = $cated_id;
        $storename = array();
        $list_vote                  = Business_Common_Users::getInstance()->getListByUname(false);
        foreach ($list_vote as $__item){
            $storename[$__item["userid"]] = $__item["storename"];
        }
        $this->view->storename = $storename;
        $date_now = date('Y-m-d');
        $start_end           = $this->_request->getParam("start_end");
        if($start_end ==null){
            $pre_date = $_option->get_pre_by_date($date_now, 7);
            $cur_date = date('F j, Y',  strtotime($pre_date));
            $start_end = $cur_date." - ".date("F j, Y"); 
        }
        
        $this->view->start_end = $start_end;
        $start  = $_option->getStartDate($start_end);
        $end  = $_option->getEndDate($start_end);
        $this->view->start = $start;
        $this->view->end = $end;
        $this->view->list_vote          = $list_vote;
        
        $list_item = $_users_products->get_list_sale_date($productsid,$cated_id, $start, $end);
		
		//nghidv - get report stock
		
		
        if($list_item){
            foreach ($list_item as $val){
                $__array_itemid[]= $val["products_id"];
            }
            if($__array_itemid){
                $strID = implode(",", $__array_itemid);
                $list = Business_Ws_ProductsItem::getInstance()->get_list_notsale($strID,$productsid,$cated_id);
                foreach ($list as $val){
                    $__array_itemid2[] = $val["itemid"];
                }
                $strID2 = implode(",", $__array_itemid2);
                $reportStock = Business_Addon_Tonkhoimei2::getInstance()->getReportByItemid2($strID2);
                foreach ($reportStock as $rp){
                    $sl_ton_itemid[$rp["itemid"]] += $rp["sl_ton"];
                    $array_itemid[$rp["itemid"]][] = $rp;
                    $array_imei[] = $rp["ma_lo"];
                }
                if($array_imei){
                    foreach ($array_imei as &$im){
                        $im = "'$im'";
                    }
                    $str_imei = implode(",", $array_imei);
                    $list_mapping_by_imei = Business_Addon_MappingProduct::getInstance()->get_list_by_imei_by_makho($str_imei);
                    foreach ($list_mapping_by_imei as $ims){
                        $array_check_imei[$ims["imei"]] = $ims["id"];
                    }
                }
                foreach ($list as &$vl){
                    $vl["title"] = $vl["title"]." ".$vl["bonus_mobile"];
                    $vl["sl_ton"] = $sl_ton_itemid[$vl["itemid"]];
//					foreach($reportStock as $report) {
//						if ($report["itemid"]==$vl["itemid"]) {
//							$vl["sl_ton"] = $report["sl_ton"];
//							break;
//						}
//					}
                }
            }
        }else{
            $list = Business_Ws_ProductsItem::getInstance()->get_list_notsale2($productsid,$cated_id);
            foreach ($list as $val){
                $__array_itemid[] = $val["itemid"];
            }
            if($__array_itemid){
                $strID = implode(",", $__array_itemid);
                $reportStock = Business_Addon_Tonkhoimei2::getInstance()->getReportByItemid2($strID);
            }
            foreach ($reportStock as $rp){
                $sl_ton_itemid[$rp["itemid"]] += $rp["sl_ton"];
                $array_itemid[$rp["itemid"]][] = $rp;
                $array_imei[] = $rp["ma_lo"];
            }
            if($array_imei){
                foreach ($array_imei as &$im){
                    $im = "'$im'";
                }
                $str_imei = implode(",", $array_imei);
                $list_mapping_by_imei = Business_Addon_MappingProduct::getInstance()->get_list_by_imei_by_makho($str_imei);
                foreach ($list_mapping_by_imei as $ims){
                    $array_check_imei[$ims["imei"]] = $ims["id"];
                }
            }
            
            foreach ($list as &$vl){
                $vl["title"] = $vl["title"]." ".$vl["bonus_mobile"];
                $vl["sl_ton"] = $sl_ton_itemid[$vl["itemid"]];
//				foreach($reportStock as $report) {
//					if ($report["itemid"]==$vl["itemid"]) {
//						$vl["sl_ton"] = $report["sl_ton"];
//						break;
//					}
//				}
            }
        }
        $pd = $this->_request->getParam("pd");
        if($pd==1){
            echo "<pre>";
            var_dump($list_mapping_by_imei);
            die();
        }
        
        $this->view->array_check_imei = $array_check_imei;
        $this->view->array_itemid = $array_itemid;
        $this->view->list = $list;
        $day = date('YmdHis');
        $title_ex = "Export$cated_id"."_$day";
        SEOPlugin::setTitle($title_ex);
        
    }
    public function exportbycatedAction(){
        $_users_products = Business_Addon_UsersProducts::getInstance();
        $_option = Business_Addon_Options::getInstance();
        $group_hnam = $_option->getGroupHnam();
        $this->view->group_hnam = $group_hnam;
        $list_hnammobile        = $_option->getCatedHnam();
        $this->view->list_hnammobile = $list_hnammobile;
        $list_catedhnammobile        = $_option->getCatedHnammobile();
        $this->view->list_flag  = $list_catedhnammobile;
        
        $type_ghn           = (int)$this->_request->getParam("type_ghn");
        $this->view->type_ghn = $type_ghn;
        $list_type_ghn = $_option->getTypeGHN();
        $this->view->list_type_ghn = $list_type_ghn;
        
        $idregency = $this->_identity["idregency"];
        $bgd=0;
        if($_option->isBGD($idregency)){
            $bgd=1;
        }
        if($this->_identity["username"]=="hnam_trungtq"){
            $bgd=1;
        }
        $this->view->bgd = $bgd;
        $productsid = (int)  $this->_request->getParam("productsid");
        $this->view->productsid= $productsid;
        $storeid = (int)  $this->_request->getParam("storeid");
        $this->view->storeid = $storeid;
        $cated_id = (int)  $this->_request->getParam("cated_id");
        $this->view->cated_id = $cated_id;
        $flag = (int)  $this->_request->getParam("flag");
        $this->view->flag = $flag;
        if($productsid ==0){
            $productsid=3;
        }
        $menus = $_option->getMenuById($productsid);
        $this->view->menu  = $menus;
        $type_old = $_option->getDemo99VTLN();
        $this->view->type_old = $type_old;
        
        $is_apple = $this->_request->getParam("is_apple");
        $this->view->is_apple = $is_apple;
        $is_type = $this->_request->getParam("is_type");
        $this->view->is_type = $is_type;
        $storename = array();
        $list_vote                  = Business_Common_Users::getInstance()->getListByUname(false);
        foreach ($list_vote as $__item){
            $storename[$__item["userid"]] = $__item["storename"];
        }
        $this->view->storename = $storename;
        $count_col = count($list_vote);
        $start_end           = $this->_request->getParam("start_end");
        if($start_end ==null){
           $start_end = date("F j, Y")." - ".date("F j, Y"); 
        }
        $this->view->start_end = $start_end;
        $start  = $_option->getStartDate($start_end);
        $end  = $_option->getEndDate($start_end);
        $this->view->start = $start;
        $this->view->end = $end;
        $col = 100/($count_col + 2);
        $this->view->col = $col;
        $this->view->list_vote          = $list_vote;
        $_addon_promotion = Business_Addon_AddonPromotion::getInstance();
        if($cated_id >0){
            $list = $_users_products->get_list_by_cateid4($cated_id,$start, $end,$storeid, $flag,$is_apple,$is_type,$type_ghn);
            foreach ($list as &$val){
                $array_id_customer[] = $val["id_customer"]; 
                $_str_id[] = $val["products_id"];
                $_array_idaddonuser[] = $val["id_addon_user"];
                $array_userid[] = $val["id_users"];
                $str_code_voucher[] = $val["id_voucher"];
                //cập nhật thêm đơn vị trả góp
                if (intval($val["cated_prepaid_installment"])>0) { 
					$val["installment_name"] = $_option->getTraGop2($val["cated_prepaid_installment"]);
				} else {
					$val["installment_name"] ="";
				}
				
            }
            if($array_id_customer){
                $str_id_customer = implode(",", $array_id_customer);
                $list_customer = Business_Addon_Customer::getInstance()->get_list_by_id($str_id_customer);
                foreach ($list_customer as $cs){
                    $array_address[$cs["id"]] = $cs["address"];
                }
            }
            if($_array_idaddonuser){
                $_arr_id = implode($_array_idaddonuser,",");
                $price_km = array();
                $list_km = $_addon_promotion->getListByType($_arr_id,0,0);
                if($list_km != null){
                    foreach ($list_km as $_item){
                        $price_km[$_item["id_addon_user"]] = $_item["total"];
                    }
                }
            }
            if($array_userid){
                $str_userid = implode(",", $array_userid);
                $list_user = Business_Common_Users::getInstance()->getListById($str_userid);
                foreach ($list_user as $user){
                    $usernames[$user["userid"]] = $user["fullname"];
                }
            }
            if($_str_id){
               $strid = implode(",", $_str_id) ;
               $_scolor = Business_Addon_AddonProductTitleKT::getInstance()->getListById($strid);
                 $name_kt = array();
                 foreach ($_scolor as $items){
                     $name_kt[$items["itemid"]][$items["colorid"]] = $items["name"];
                 }
            }
            if($_array_idaddonuser != NULL && $_str_id != NULL){
                $list_ctkm = $_addon_promotion->get_list_ctkm_by_id_addon_user_by_products_id($_arr_id, $strid);
                foreach ($list_ctkm as $v){
                    $ctkm[$v["id_addon_user"]][$v["product_ids"]] = $v["ctkm"];
                }
            }
            if($str_code_voucher){
                foreach ($str_code_voucher as &$vcs){
                    $vcs ="'$vcs'";
                }
                $str_vouchers = implode(",", $str_code_voucher);
                $list_vouchers = Business_Addon_Voucher::getInstance()->getListByVoucher($str_vouchers);
                $arr_voucher = array();
                foreach ($list_vouchers as $vc){
                    $arr_voucher[$vc["code_name"]] = $vc["note"]."-".$vc["note2"];
                }
            }
        }
        $this->view->arr_voucher = $arr_voucher;
        $this->view->ctkm = $ctkm;
        
        
        $list_ctkm2 = Business_Addon_Ctkm::getInstance()->getList();
        foreach ($list_ctkm2 as $items){
            $name_ctkm[$items["id"]] = $items["name"];
        }
        $this->view->name_ctkm = $name_ctkm;
        $this->view->list = $list;
        $this->view->name_kt = $name_kt;
        $this->view->price_km = $price_km;
        $this->view->usernames = $usernames;
        $this->view->array_address = $array_address;
        
        $day = date('YmdHis');
        $title_ex = "Export$cated_id"."_$day";
        SEOPlugin::setTitle($title_ex);
        
    }

    











    public function exChargecardAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
//        header('Content-Type: text/csv; charset=utf-8');
//        header('Content-Type: charset=utf-8');
        
        header('Content-Encoding: UTF-8');
        header('Content-type: text/csv; charset=UTF-8');
        
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        
        $output = fopen('php://output', 'w');
        
        $zwfuser = Business_Common_Users::getInstance();
        $lstorename = $zwfuser->getListByUname(FALSE);
        $storename = array();
        foreach ($lstorename as $items){
            $storename[$items["userid"]] = $items["abbreviation"];
        }
        $_users_products = Business_Addon_UsersProducts::getInstance();
        $_option = Business_Addon_Options::getInstance();
        $idregency = $this->_identity["idregency"];
        
        $start_end           = $this->_request->getParam("start_end");
        if($start_end ==null){
           $start_end = date("F j, Y")." - ".date("F j, Y"); 
        }
        $this->view->start_end = $start_end;
        $start  = $_option->getStartDate($start_end);
        $end  = $_option->getEndDate($start_end);
        
        
        $list = $_users_products->getListByTotalChargecard($start, $end);
        
        $day = date('YmdHis');
        header("Content-Disposition: attachment; filename='Chargecard-$day.csv");
        fputcsv($output, array('Ngay', 'Chi nhanh', 'Ma bill','Tong tien','Tong tien charge tren bill','Tien con lai','Loai may pos','Loai phi(Tien mat/The)','Phi'));
        foreach ($list as $items){
            $type_card='';
            $status_card='';
            $phitienmat0 =0;
            if ($items["status_chargecard"] == 1) {
                $status_card = 'VAO THE';
            }
            if ($items["status_chargecard"] == 2) {
                $status_card = 'TIEN MAT';
                $phitienmat0 = $items["free_prepaid"];
            }
            if ($items["cated_card"] == 1) {
                $type_card = 'Cty(Agribank)';
            }
            if ($items["cated_card"] == 2) {
                $type_card = 'Hnam(Agribank)';
            }
            if ($items["cated_card"] == 3) {
                $type_card = 'Cty(Sacombank)';
            }
            if ($items["cated_card"] == 4) {
                $type_card = 'Hnam(Sacombank)';
            }
            $vote_id    = $items["vote_id"];
            $vote_name = $storename[$vote_id];
            $items["create_date"] = date('d/m/Y',  strtotime($items["create_date"]));
            $finalData = array(
                 // For chars with accents.
                $items["create_date"],
                $vote_name,
                $items["id_addon_user"],
                $items["total_price"],
                ($items["prepaids"]+$phitienmat0),
                ($items["total_price"] - $items["prepaids"]),
                $type_card,
                $status_card, // For chars with accents.
                ($items["free_prepaids"]), // For chars with accents.
            );
            $strItem .= implode("\t", $finalData)."\r\n";
            $total4 +=$items["total_price"];
            $total5 +=$items["prepaids"]+$phitienmat0;
            $total6 +=$items["total_price"] - $items["prepaids"];
            
            fputcsv($output,$finalData);
        }
//        fclose($output);
        
        fputcsv($output, array('Ngay', 'Chi nhanh', 'Ma bill','Ten san pham','Cty/Hnam','Mau','Tong tien','Tong tien charge tren bill','Tien con lai','Loai may pos','Loai phi(Tien mat/The)','Phi'));
        $day = "_".date('Ymd');
        
        $strItem2="--------------------------Chi tiết------------------------\r\n";
        $finalData2= array();
        $list2 = $_users_products->getListByChargecard($start, $end);
        foreach ($list2 as $items){
            $loai ='';
            if($items["flag"] ==1){
               $loai ='Cong ty'; 
            }
            if($items["flag"] ==2){
               $loai ='Hnam'; 
            }
            $type_card='';
            $status_card='';
            $phitienmat =0;
            if ($items["status_chargecard"] == 1) {
                $status_card = 'VAO THE';
            }
            if ($items["status_chargecard"] == 2) {
                $status_card = 'TIEN MAT';
                $phitienmat = $items["free_prepaid"];
            }
            if ($items["cated_card"] == 1) {
                $type_card = 'Cty(Agribank)';
            }
            if ($items["cated_card"] == 2) {
                $type_card = 'Hnam(Agribank)';
            }
            if ($items["cated_card"] == 3) {
                $type_card = 'Cty(Sacombank)';
            }
            if ($items["cated_card"] == 4) {
                $type_card = 'Hnam(Sacombank)';
            }
            $vote_id    = $items["vote_id"];
            $vote_name = $storename[$vote_id];
            $items["create_date"] = date('d/m/Y',  strtotime($items["create_date"]));
            $productname = Business_Common_Utils::removeTiengViet($items["products_name"]);
            $product_color = Business_Common_Utils::removeTiengViet($items["product_color"]);
            $finalData2 = array(
                 // For chars with accents.
                $items["create_date"],
                $vote_name,
                $items["id_addon_user"],
                $productname."( $loai )",
                $loai,
                $items["product_color"],
                $items["total_price"],
                ($items["prepaid"]+$phitienmat),
                ($items["total_price"] - $items["prepaid"]),
                $type_card,
                $status_card, // For chars with accents.
                ($items["free_prepaid"]), // For chars with accents.
            );
            $strItem2 .= implode("\t", $finalData2)."\r\n";
            
            $total1 +=$items["total_price"];
            $total2 +=$items["prepaid"]+$phitienmat;
            $total3 +=$items["total_price"] - $items["prepaid"];
            fputcsv($output,$finalData2);
        }
        fclose($output);
    }
        public function exDetailChargeCardAction(){
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
        $idregency = $this->_identity["idregency"];
        
        $start_end           = $this->_request->getParam("start_end");
        if($start_end ==null){
           $start_end = date("F j, Y")." - ".date("F j, Y"); 
        }
        $this->view->start_end = $start_end;
        $start  = $_option->getStartDate($start_end);
        $end  = $_option->getEndDate($start_end);
        
        
        $list = $_users_products->getListByChargecard($start, $end);
//        echo "<pre>";
//        var_dump($list);
//        die();
        $day = "_".date('Ymd');
        header("Content-Disposition: attachment; filename=DetailChargecard_$day.csv");
        header("Content-Type: text/csv; charset=utf-8");
        header('Content-Type: charset=utf-8');
        
        $finalData= array();
        $strItem ="Ngày\tChi nhánh\tMã Bill\tTên sản phẩm\tCtyHnam\tMàu\tTổng tiền\tTổng tiền charge trên bill\tTiền còn lại\tLoại máy pos\tLoại phí(Tiền mặt/Thẻ)\tPhí"."\r\n";            
        foreach ($list as &$items){
            $loai ='';
            if($items["flag"] ==1){
               $loai ='Công ty'; 
            }
            if($items["flag"] ==2){
               $loai ='Hnam'; 
            }
            $type_card='';
            $status_card='';
            if ($items["status_chargecard"] == 1) {
                $status_card = 'Vào thẻ';
            }
            if ($items["status_chargecard"] == 2) {
                $phitienmat = $items["free_prepaid"];
                $status_card = 'Tiền mặt';
            }
            if ($items["cated_card"] == 1) {
                $type_card = 'Cty(Agribank)';
            }
            if ($items["cated_card"] == 2) {
                $type_card = 'Hnam(Agribank)';
            }
            if ($items["cated_card"] == 3) {
                $type_card = 'Cty(Sacombank)';
            }
            if ($items["cated_card"] == 4) {
                $type_card = 'Hnam(Sacombank)';
            }
            $vote_id    = $items["vote_id"];
            $vote_name = $storename[$vote_id];
            $items["create_date"] = date('d/m/Y',  strtotime($items["create_date"]));
            $finalData = array(
                 // For chars with accents.
                $items["create_date"],
                $vote_name,
                $items["id_addon_user"],
                $items["products_name"]."( $loai )",
                $loai,
                $items["product_color"],
                ($items["total_price"]),
                ($items["prepaid"]+$items["free_prepaid"]),
                ($items["total_price"] - $items["prepaid"]),
                $type_card,
                $status_card, // For chars with accents.
                ($items["free_prepaid"]), // For chars with accents.
            );
            $strItem .= implode("\t", $finalData)."\r\n";
            $total1 +=$items["total_price"];
            $total2 +=$items["prepaid"]+$phitienmat;
            $total3 +=$items["total_price"] - $items["prepaid"];
        }
        $strItem .="\t\t\t\t\tTổng\t$total1\t$total2\t$total3"."\r\n"; 
        ob_start();
        echo chr(255) . chr(254) . mb_convert_encoding($strItem, 'UTF-16LE', 'UTF-8');
        $content = ob_get_contents();
        ob_end_clean();
        echo $content;
    }
    
    
}
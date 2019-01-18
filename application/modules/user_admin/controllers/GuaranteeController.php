<?php

/**
 * User Admin Home Controller
 * @author: nghidv
 */
class User_Admin_GuaranteeController extends Zend_Controller_Action {

    private $_identity;
    private $skey="BHVT2016HNAM";
    private $skey2="BHVT2016";
    public function init() {
        // do something
        BlockManager::setLayout('appbh');
        $auth = Zend_Auth::getInstance(); 
        $this->_identity = (array) $auth->getIdentity();
    }
    public function reportAction(){
        $__bs = Business_Addon_Guarantee::getInstance();
        $_option = Business_Addon_Options::getInstance();
        $d=date('Y-m-d');
        $k=$_option->pre_date($d, 1);
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
        $q= $this->_request->getParam("q");
        $this->view->q= $q;
        $list = $__bs->get_list_by_date($q,$start,$end);
        foreach ($list as $val){
            $__strid[] = $val["id"];
        }
        if($__strid != NULL){
            $strid = implode(",", $__strid);
            $slist = Business_Addon_TransferGuarantee::getInstance()->get_list_by_idguarantee2($strid);
            foreach ($slist as $val2){
                $money_dathu[$val2["idguarantee"]] = $val2["money"];
            }
        }
        $this->view->list = $list;
        $this->view->slist = $slist;
        $this->view->money_dathu = $money_dathu;
        $list_status_guarantee = $_option->getBH();
        $this->view->list_status_guarantee = $list_status_guarantee;
        $list_ok = $_option->get_ok();
        $this->view->list_ok = $list_ok;
    }
    public function historyimeiAction(){
        $imei = $this->_request->getParam("imei");
        if($imei != NULL){
            $list = Business_Addon_Guarantee::getInstance()->get_list_search_id_imei($imei);
            foreach ($list as &$val){
                $val["token"] = md5($this->skey.$val["id"]);
            }
        }
        $this->view->list = $list;
    }

    public function addbhscAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $__bs = Business_Addon_GuaranteeBhsc::getInstance();
        $id_guaranteeid = (int)  $this->_request->getParam("id_guaranteeid");
        $__money_dvsc = $this->_request->getParam("money_dvsc");
        $money_dvsc = str_replace(",", "", $__money_dvsc);
        
        $__money_hnam = $this->_request->getParam("money_hnam");
        $money_hnam = str_replace(",", "", $__money_hnam);
        $ncc_ok = $this->_request->getParam("ncc_ok");
        $ids = (int)  $this->_request->getParam("ids");
        $note = $this->_request->getParam("note");
        if($id_guaranteeid ==0){
            die('no');
        }else{
            $detail = $__bs->getDetail($ids);
            $data["money_dvsc"] = $money_dvsc;
            if($ncc_ok==2){
                $money_hnam=0;
            }
            $data["money_hnam"] = $money_hnam;
//            $chagre = $money_dvsc*15/100;
//            if($chagre>0 && $chagre<100000){
//               $chagre=100000; 
//            }
            $status_guarantee = $this->_request->getParam("status_guarantee");
            
            $flag = $this->_request->getParam("flag");
            $money = $money_dvsc-$money_hnam;
            if($money_dvsc==$money_hnam){
                $money=0;
            }
//            if($status_guarantee ==1){
//                if($ncc_ok==2 && $flag==2){
//                    $money = $money_dvsc + $chagre;
//                }
//            }else{
//                $money = $money_dvsc + $chagre;
//            }
            
            $data["status_guarantee"] = $status_guarantee;
            $data["money"] = $money;
            $data["id_guaranteeid"] = $id_guaranteeid;
            $data["enabled"] = 1;
            $data["ncc_ok"] = $ncc_ok;
            $data["note"] = $note;
//            echo "<pre>";
//            var_dump($data,$ids);
//            die();
            if($detail["id"] ==0){
                $details2 = $__bs->get_detail_by_id_guaranteeid($id_guaranteeid);
                if($details2 != NULL){
                    die('Phieu nay da duoc them. Vui long chinh sua');
                }
                //insert
                $data["enabled"] = 1;
                $data["userid"] = $this->_identity["userid"];
                $data["datetime"] = date('Y-m-d H:i:s');
                $__bs->insert($data);
            }else{
                if($detail["status"]!=2){
                    $data["userid_update"] = $this->_identity["userid"];
                    $data["datetime_update"] = date('Y-m-d H:i:s');
                    $__bs->update($detail["id"],$data);
                }
                
                //update
            }
        }
        
    }
    public function savebillAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $id_guaranteeid2 = (int)$this->_request->getParam("id_guaranteeid2");
        $bill_of_bhsc = $this->_request->getParam("bill_of_bhsc");
        if($id_guaranteeid2>0 && $bill_of_bhsc != NULL){
            $query1 = "update hnam_guarantee_bhsc set bill_of_bhsc = '$bill_of_bhsc' where id_guaranteeid = $id_guaranteeid2";
            $query2 = "update hnam_guarantee set bill_of_bhsc = '$bill_of_bhsc' where id = $id_guaranteeid2";
            Business_Addon_Guarantee::getInstance()->excute($query1);
            Business_Addon_Guarantee::getInstance()->excute($query2);
        }
        header("location:".$_SERVER['HTTP_REFERER']);
        exit();
    }
    public function statusbhscAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $status = (int)  $this->_request->getParam("status");
        $id = (int)  $this->_request->getParam("id");
        $__bhsc = Business_Addon_GuaranteeBhsc::getInstance();
        $detail = $__bhsc->getDetail($id);
        if($detail != NULL){
            if($detail["status"] !=2){
                $data["time_status"] = date('Y-m-d H:i:s');
                $data["status"] =$status;
                $__bhsc->update($id, $data);
                $data2["money_dvsc"] = $detail["money_dvsc"];
                $data2["money"] = $detail["money"];
                $data2["money_hnam"] = $detail["money_hnam"];
                $data2["note2"] = $detail["note"];
                Business_Addon_Guarantee::getInstance()->update($detail["id_guaranteeid"], $data2);
            }
        }
        header("location:".$_SERVER['HTTP_REFERER']);
        exit();
    }
    public function isviewAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $id = (int)  $this->_request->getParam("id");
        $__bhsc = Business_Addon_GuaranteeBhsc::getInstance();
        $detail = $__bhsc->getDetail($id);
        if($detail != NULL){
            $data["is_view"] =1;
            $__bhsc->update($id, $data);
        }
        header("location:".$_SERVER['HTTP_REFERER']);
        exit();
    }

    public function editAction(){
        $idregency = (int)$this->_identity["idregency"];
        $kq = $this->_request->getParam("kq");
        $this->view->kq = $kq;
        $token = $this->_request->getParam("token");
        
        $ids = (int)  $this->_request->getParam("ids");
        $ztoken = md5($this->skey2.$ids);
        
        $this->view->ids = $ids;
        $this->view->skey2 = $this->skey2;
        $__bs = Business_Addon_Guarantee::getInstance();
        $_option = Business_Addon_Options::getInstance();
        $list_ok = $_option->get_ok();
        $bgd=0;
        if($_option->isBGD($idregency) || $this->isBHVT($idregency)){
           $bgd=1; 
        }
        $this->view->bgd = $bgd;
        $list_bh = $_option->getBH();
        $list_status_bhsc = Business_Addon_Options::getInstance()->get_status_bhsc();
//        if($this->_request->isPost()){
            if($kq != NULL){
                $list = $__bs->get_list_search_id_imei($kq);
                
//                if($__strId != NULL){
//                    $strid = implode(",", $__strId);
//                    $list_bhsc = Business_Addon_GuaranteeBhsc::getInstance()->get_list_by_guaranteeid($strid);
//                    foreach ($list_bhsc as $bh){
//                        $datetime[$bh["id_guaranteeid"]] = $bh["datetime"];
//                        $money_dvsc[$bh["id_guaranteeid"]] = $bh["money_dvsc"];
//                        $money[$bh["id_guaranteeid"]] = $bh["money"];
//                        $status[$bh["id_guaranteeid"]] = $bh["status"];
//                    }
//                }
            }
//        }
            
         if($token != NULL){
            if($token != $ztoken){
               die('no access'); 
            }
            $detail2 = Business_Addon_GuaranteeBhsc::getInstance()->getDetail($ids);
            $list = $__bs->get_list_by_id($detail2["id_guaranteeid"]);
            $this->view->detail2 = $detail2;
        }
        $__strId = array();
        foreach ($list as $val){
            $sstatus_guarantee2[$val["id"]] = $list_bh[$val["status_guarantee"]];
            $array_ids[] = $val["id"];
        }
        
        if($kq != NULL){
            if($array_ids != NULL){
                $str_ids = implode(",", $array_ids);
                $list_bhsc22 = Business_Addon_GuaranteeBhsc::getInstance()->get_list_by_guaranteeid($str_ids);
                foreach ($list_bhsc22 as $val2){
                    $check_guaranteeid[$val2["id_guaranteeid"]] = $val2["id"];
                }
                $__strId = $array_ids;
            }
        }
        $this->view->check_guaranteeid = $check_guaranteeid;
        $d=date('Y-m-d');
        //$k=$_option->pre_date($d, 1);
        $k=$_option->getPrevDay($d);
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
        $__status =  $this->_request->getParam("status");
        $this->view->sstatus2 = $__status;
        $ncc_ok =  $this->_request->getParam("ncc_ok");
        $this->view->ncc_ok = $ncc_ok;
        $__userid = $this->_identity["userid"];
        if($bgd ==1){
            $__userid = (int)$this->_request->getParam("uid");
        }
        $this->view->uid = $__userid;
        $slist = Business_Addon_GuaranteeBhsc::getInstance()->get_list($start,$end,$__status,$ncc_ok,$__userid); 
        foreach ($slist as $bh){
            $datetime[$bh["id"]] = $bh["datetime"];
            $money_dvsc[$bh["id"]] = $bh["money_dvsc"];
            $money_hnam[$bh["id"]] = $bh["money_hnam"];
            $money[$bh["id"]] = $bh["money"];
            $status[$bh["id"]] = $bh["status"];
            $sstatus[$bh["id"]] = $list_status_bhsc[$bh["status"]];
            $sncc_ok[$bh["id"]] = $bh["ncc_ok"];
            $snote[$bh["id"]] = $bh["note"];
            $__strId[] = $bh["id_guaranteeid"];
        }
        if($__strId != NULL){
            $strid = implode(",", $__strId);
            $list2 = $__bs->get_list_by_id($strid);
            foreach ($list2 as $val){
                $sitem_name[$val["id"]] = $val["item_name"];
                $simei[$val["id"]] = $val["imei"];
                $sstatus_guarantee[$val["id"]] = $list_bh[$val["status_guarantee"]];
                $sflag[$val["id"]] = $val["flag"];
                $oks[$val["id"]] = (int)$val["ok"];
            }
            $slist5 = Business_Addon_GuaranteeWarranty::getInstance()->getListByGuarantee($strid);
            $s_warranty = array();
            foreach ($slist5 as $_item){
                $s_warranty[$_item["id_guarantee"]][] = $_item;
                $s_warranty_time[$_item["id_guarantee"]] = $_item["datetime"];
                $s_warranty_dealine[$_item["id_guarantee"]] = $_item["dealine"];
            }
            $this->view->s_warranty_time = $$s_warranty_time;
            $this->view->s_warranty = $s_warranty;
            $this->view->s_warranty_dealine = $s_warranty_dealine;

            $_warranty_unit = Business_Addon_WarrantyUnit::getInstance();
            $sproducers = $_warranty_unit->getList();
            $name_producers = array();

            foreach ($sproducers as $items){
                $name_producers[$items["id"]] = $items["name"];
            }
            $this->view->name_producers = $name_producers;
        }
        $iexport = (int)  $this->_request->getParam("iexport");
        if($iexport==1){
            foreach ($slist as $vl){
                if($oks[$vl["id_guaranteeid"]]==1){
                    $list_export[] = $vl;
                }
            }
            $this->exportbhscAction($list_export,$sitem_name, $simei, $sstatus_guarantee,$s_warranty,$name_producers);
        }
        
        
        
        
        $this->view->list = $list;
        $this->view->slist = $slist;
        $this->view->sitem_name = $sitem_name;
        $this->view->simei = $simei;
        $this->view->datetime = $datetime;
        $this->view->money_dvsc = $money_dvsc;
        $this->view->money_hnam = $money_hnam;
        $this->view->money = $money;
        $this->view->status = $status;
        $this->view->sstatus = $sstatus;
        $this->view->sncc_ok = $sncc_ok;
        $this->view->snote = $snote;
        $this->view->list_status_bhsc= $list_status_bhsc;
        $this->view->list_ok = $list_ok;
        $this->view->oks = $oks;
        $this->view->sstatus_guarantee = $sstatus_guarantee;
        $this->view->sstatus_guarantee2 = $sstatus_guarantee2;
        $this->view->list_bh = $list_bh;
        $this->view->sflag = $sflag;
        $this->view->get_list_bhsc_orther = $_option->get_list_bhsc_orther();
    }
    public function exportbhscAction($list,$sitem_name, $simei, $sstatus_guarantee){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true); 
        $day = date('YmdHis'); 
        $__option = Business_Addon_Options::getInstance();
        $list_ok = $__option->get_ok();
        $list_status_bhsc = $__option->get_status_bhsc();
        header("Content-Disposition: attachment; filename=$day-bhsc.txt");
        header("Content-Type: text/csv; charset=utf-8");
        header("Content-Type: charset=utf-8");
        $finalData= array();
        $strItem .="MaPhieu\tTensanpham\tImei\tNgaybaophi\tTinhtrangbaohanh\tDongyhotro\tTTBHSCbao\tHnamhotro\tGhichu\tTrangthai\tBillBHSC"."\r\n";  
        
        foreach ($list as $items){
            $id = $items["id_guaranteeid"];
            $total_money_hnam += $items["money_hnam"];
            $total_money_dvsc += $items["money_dvsc"];
            
            $tensanpham = ucwords(strtolower($sitem_name[$id]));
            $imei = $simei[$id];
            
            $imei = trim($imei);
            $tensanpham = trim($tensanpham);
            $datetime = date('d/m/Y',  strtotime($items["datetime"]));
            $tinhtrangbaohanh = $sstatus_guarantee[$id];
            $dongyhotro = $list_ok[$id];
            
            $TTBHSCbao = $items["money_dvsc"];
            $Hnamhotro = $items["money_hnam"];
            $ghichu = $items["note"];
            $trangthai = $list_status_bhsc[$items["status"]];
            $BillBHSC = $items["bill_of_bhsc"];
            $finalData = array(
                 // For chars with accents.
                $items["id_guaranteeid"],
                $tensanpham,
                $imei,
                $datetime,
                $tinhtrangbaohanh,
                $dongyhotro,
                $TTBHSCbao,
                $Hnamhotro,
                $ghichu,
                $trangthai,
                $BillBHSC,
            );
            $strItem .= implode("\t", $finalData)."\r\n";
        }
        $strItem .="\t\t\t\t\t\t$total_money_dvsc\t$total_money_hnam\t\t\t"."\r\n";
        ob_start();
        echo $strItem;
        //echo chr(255) . chr(254) . mb_convert_encoding($strItem, 'UTF-16LE', 'UTF-8');
        $content = ob_get_contents();
        ob_end_clean();
        echo $content;
    }

    public function searchWarrantyAction(){
        $kq = $this->_request->getParam("kq");
        $this->view->kq = $kq;
        $__bs = Business_Addon_UsersProducts::getInstance();
        $__option = Business_Addon_Options::getInstance();
        $_productsitem = Business_Ws_ProductsItem::getInstance();
        $ngaybaohanh  =0;
        $kq = trim($kq);
        $kq = $__option->replace_space($kq);
        if($kq != NULL){
            $sdetail = Business_Addon_Guarantee::getInstance()->get_list_detail_imei($kq);
            if($sdetail != NULL){
                $ngaybaohanh = strtotime($sdetail["datetime"]);
            }
            $list = $__bs->search($kq);
            
            foreach ($list as $_items) {
                $_products_id[] = $_items["products_id"];
                if($_items["productsid"]==10){// dịch vụ
                    $slist1[] = $_items;
                    $array_itemid[] = $_items["products_id"];
                }else{
                    $slist2[] = $_items;
                }
            }
            if($array_itemid){
                $str_itemid = implode(",", $array_itemid);
                $list_product_service = Business_Ws_ProductsItem::getInstance()->getListByProductsID2($str_itemid);
                foreach ($list_product_service as $_sv) {
                    $_pmonths_sv[$_sv["itemid"]] = (int) $_sv["warranty"];
                }
            }
            $bhvip=0;
            $bhmc=0;
            $bh24=0;
            foreach ($slist1 as $v1){
                if($v1["cated_id"]==1012){
                    $bhvip=1;
                    $bh_dv[$v1["imes"]] = $_pmonths_sv[$v1["products_id"]];
                }
                if($v1["cated_id"]==901){ // bảo hành máy cũ
                    $bh_dv[$v1["imes"]] = $_pmonths_sv[$v1["products_id"]];
                    $bhmc =1;
                }
                if($v1["cated_id"] ==905){
                    $bh_dv[$v1["imes"]] = 12;
                    $bh24=1;
                }
            }
            if($_products_id != NULL){
                $___pid = implode(",", $_products_id);
                $list_baohanh = Business_Addon_Baohanh::getInstance()->get_list_by_itemid($___pid);
                foreach ($list_baohanh as $_item) {
                    $_pmonths[$_item["itemid"]] = (int) $_item["than_may"];
                }
            }

        
//            cated_id
            foreach ($list as &$val){
                $__id = $val["products_id"];
                if(strtotime($val["create_date"]) <$ngaybaohanh){
                    continue;
                }
                
                $imei = $val["imes"];
                $__products_id = $val["products_id"];
                $__month2 = $_pmonths[$__products_id];
                if($bhvip==1){
                    $__month2 = $bh_dv[$imei];
                }
                if($bhmc==1){
                    $__month2 = $__month2+$bh_dv[$imei];
                }
                if($bh24==1){
                    $__month2 = $__month2+$bh_dv[$imei];
                }

                if((int)$val["warranty"] >0){
                    $__month2 = $val["warranty"];
                }
                $sdate = strtotime($val["create_date"]);
                $date1 = $__option->add($val["create_date"], $__month2);
                $second_date = strtotime('now');
                
                $val["day_end"] =0;
                $first_date = strtotime($date1);
                $datediff = $first_date - $second_date;
                if($datediff >0){
                    $datediff2 = $first_date-$sdate;
                    $date_used = floor($datediff2 / (60*60*24)) - floor($datediff / (60*60*24));
//                    if($kq =="990002286608581"){
//                        echo "<pre>";
//                        var_dump($datediff,$datediff2,$date_used);
//                        die();
//                    }
                    $val["day_end"] = floor($datediff2 / (60*60*24)) - $date_used;
                }
                $arr_imei = $__option->list_imei_bao_hanh_0day();
                if(in_array($val["imes"], $arr_imei)){
                    $val["day_end"] = 0;
                }
            }
            
            $this->view->list = $list;
            $dealine = 14;
            $this->view->dealine = $dealine;
        }
    }

    public function required(){
        $ret = array();
        $arr = array();
        $data_frm = $this->_request->getParams('data_frm');
        if($data_frm["imei"] ==null){
            $arr["id"] = "imei";
            $arr["msg"] = "IMEI không được để trống";
            $ret[] = $arr;
        }
//        if($data_frm["seri"] ==null){
//            $arr["id"] = "seri";
//            $arr["msg"] = "SERI không được để trống";
//            $ret[] = $arr;
//        }
        if((int)$data_frm["productsid"] ==0){
            $arr["id"] = "productsid";
            $arr["msg"] = "Vui lòng chọn loại điện thoại, máy tính bảng,laptop, đồng hồ thông minh";
            $ret[] = $arr;
        }
        if((int)$data_frm["cated_id"] ==0){
            $arr["id"] = "cated_id";
            $arr["msg"] = "Vui lòng chọn danh mục sản phẩm";
            $ret[] = $arr;
        }
        if((int)$data_frm["itemid"] ==0){
            $arr["id"] = "itemid";
            $arr["msg"] = "Sản phẩm không được để trống";
            $ret[] = $arr;
        }
        
        if((int)$data_frm["flag"] ==0){
            $arr["id"] = "flag";
            $arr["msg"] = "Vui lòng chọn loại công ty hoặc hnam";
            $ret[] = $arr;
        }
        if((int)$data_frm["pid_color"] ==0){
            $arr["id"] = "pid_color";
            $arr["msg"] = "Màu không được để trống";
            $ret[] = $arr;
        }
        if((int)$data_frm["status_guarantee"] ==0){
            $arr["id"] = "status_guarantee";
            $arr["msg"] = "Tình trạng bảo hành của máy không được để trống";
            $ret[] = $arr;
        }
//        if($data_frm["producers"] ==null){
//            $arr["id"] = "producers";
//            $arr["msg"] = "Hãng bảo hành không được để trống";
//            $ret[] = $arr;
//        }
        $purchase_date = str_replace("/", "-", $data_frm["purchase_date"]) ;
        $__purchase_date       =   date('Y/m/d',  strtotime($purchase_date));
        
        $dealine = str_replace("/", "-", $data_frm["dealine"]) ;
        $__dealine       =   date('Y/m/d',  strtotime($dealine));
        $_idregency = $this->_identity["idregency"];
        $__option = Business_Addon_Options::getInstance();
        if($_idregency !=48){
            if($__purchase_date =='1970/01/01'){
                $arr["id"] = "purchase_date";
                $arr["msg"] = "Vui lòng nhập ngày mua máy";
                $ret[] = $arr;
            }
            if($__dealine =='1970/01/01'){
                $arr["id"] = "dealine";
                $arr["msg"] = "Vui lòng nhập ngày hẹn";
                $ret[] = $arr;
            }
        }
        $voucher = trim($data_frm["voucher"]);
        $ret2 = $this->check_voucher($voucher);
        if($ret2 != NULL){
           $ret = array_merge($ret,$ret2); 
        }
        return $ret;
    }
    public function checkVoucherAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $voucher = $this->_request->getParam("voucher");
        $voucher = trim($voucher);
        $ret = $this->check_voucher($voucher);
        if($voucher ==NULL){
            echo "";
            die();
        }
        if(count($ret) >0){
            for($i=0;$i<count($ret);$i++){
                $msg = $ret[$i]['msg'];
                $ids = $ret[$i]['id'];
                $msg .="<input name='code_value' id='code_value' type='hidden' value='0' />";
                echo $msg;
                die();
            }
        }else{
            $_wsvoucher = Business_Addon_Voucher::getInstance();
            $detail_voucher = $_wsvoucher->getDetailByCode($voucher);
            $code_value = 0;
            if($detail_voucher["type_ctkm"]==1){ //Hvip
                $code_value =10;
            }
            if($detail_voucher["type_ctkm"]==2){ //Hmember
               $code_value =5; 
            }
            $html ='Giảm '.number_format($code_value).'%';
            $html .=" <br/><input name='code_value' id='code_value' type='hidden' value='$code_value' />";
            echo  $html;
        }
    }

    public function check_voucher($voucher){
        $ret = array();
        $err = array();
        if($voucher != NULL){
            $_wsvoucher = Business_Addon_Voucher::getInstance();
            $detail_voucher = $_wsvoucher->getDetailByCode($voucher);
            $type_ctkm=0;
            if($detail_voucher["type_ctkm"]==1 || $detail_voucher["type_ctkm"]==2 ){
                $type_ctkm=1;
            }
            if($type_ctkm==0){ 
                $err['id'] = "voucher";
                $err['msg'] = "Mã voucher này không áp dụng cho bảo hành sửa chữa";
                $ret[] = $err;
            }
            if ($detail_voucher == null){
                $err['id'] = "voucher";
                $err['msg'] = "Mã voucher này không có thực";
                $ret[] = $err;
            }else{
                if ($detail_voucher["used"] == 1) {
                    $err['id'] = "voucher";
                    $err['msg'] = "Voucher này đã được sử dụng";
                    $ret[] = $err;
                } else {
                    if ((int) strtotime($detail_voucher["code_expired"]) > 0) {
                        if (strtotime($detail_voucher["code_expired"]) < $day_now) {
                            $err['id'] = "voucher";
                            $err['msg'] = "Voucher này đã hết hạn sử dụng";
                            $ret[] = $err;
                        }
                    }
                    if ($detail_voucher["number_used"] == 0) {
                        $err['id'] = "voucher";
                        $err['msg'] = "Voucher này đã hết số lần sử dụng";
                        $ret[] = $err;
                    }
                }
            }
        }
        return $ret;
    }
    public function update_voucher($voucher){
        // cập nhật sử dụng voucher =0
        $voucher = trim($voucher);
        $_wsvoucher = Business_Addon_Voucher::getInstance();
        $detail_voucher = $_wsvoucher->getDetailByCode($voucher);
        $number_used = $detail_voucher["number_used"] - 1;
        
        if ($number_used < 0) {
            $number_used = 0;
        }
                        
        if ($number_used == 0) {
            $data_voucher["used"] = 1;
        } else {
            $data_voucher["used"] = 0;
        }
        $data_voucher["number_used"] = $number_used;

        $data_voucher["code_store"] = $this->_identity["userid"];
        $data_voucher["code_updated"] = date('Y-m-d H:i:s');
        $_wsvoucher->update($voucher, $data_voucher);
    }

    public function iframeAdd1Action(){
        $this->_helper->Layout()->disableLayout();
    }
    public function getImeiBbmhAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $__option = Business_Addon_Options::getInstance();
        $imei = $this->_request->getParam("imei");
        $imei = $__option->replace_space($imei);
        $seri = $this->_request->getParam("seri");
        $seri = $__option->replace_space($seri);
        $cateid ="890,867,901";
        $detail = Business_Addon_UsersProducts::getInstance()->get_detail_by_imei_cateid($imei,$cateid);
        if($seri != NULL){
            $detail2 = Business_Addon_UsersProducts::getInstance()->get_detail_by_imei_cateid($seri,$cateid);
            if($detail2){
                $detail = $detail2;
            }
        }
        if($detail!= NULL ){
            $products_id = $detail["products_id"];
            echo $detail["products_name"]."[".number_format($detail["products_price"])."]";
            echo "<input type='hidden' name='idbbmh' value='$products_id' >";
        }else{
            echo "Không có";
        }
    }
    public function getimeibyflagAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $imei = $this->_request->getParam("imei");
        $detail = Business_Addon_UsersProducts::getInstance()->get_detail_by_imei2($imei);
        echo json_encode($detail);
    }
    public function okCustomerAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $id = (int)  $this->_request->getParam("id");
        $ok = (int)  $this->_request->getParam("ok");
        $data["ok"] = $ok;
        
        $_guarantee = Business_Addon_Guarantee::getInstance();
        $detail = $_guarantee->getDetail($id);
        if((int)$detail["ok"] !=0){
            die('no access');
        }
        $_guarantee->update($id, $data);
        $this->history_date($__type=6, $id); // Khách đồng ý hay không
    }
    public function bhangAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $id = (int)  $this->_request->getParam("id");
        $data["bhang"] = 1;
        $_guarantee = Business_Addon_Guarantee::getInstance();
        $_guarantee->update($id, $data);
    }
    public function delEnabledAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $id = (int)  $this->_request->getParam("id");
        $data["enabled"] =0;
        $token = $this->_request->getParam("token");
        $ztoken = md5($this->skey.$id);
        if($token != $ztoken){
            die("No access");
        }
        $_guarantee = Business_Addon_Guarantee::getInstance();
        $_guarantee->update($id, $data);
    }
    public function delbystoreAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $id = (int)  $this->_request->getParam("id");
        $data["enabled"] =0;
        $data["note_del"] ='Chi nhánh tự hủy '.date('d/m/Y H:i:s');
        $token = $this->_request->getParam("token");
        $ztoken = md5($this->skey.$id);
        if($token != $ztoken){
            die("No access");
        }
        $_guarantee = Business_Addon_Guarantee::getInstance();
        if($id >0){
            $detail = $_guarantee->getDetail($id);
            if($detail["isdel"]==0){
                $_guarantee->update($id, $data);
            }
        }
        
    }
    public function xnAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $id = (int)  $this->_request->getParam("id");
        $data["status"] =3;
        $data["creator_end"] = $this->_identity["username"];
        $token = $this->_request->getParam("token");
        $ztoken = md5($this->skey.$id);
        
        if($token != $ztoken){
            die("No access");
        }
        $_guarantee = Business_Addon_TransferGuarantee::getInstance();
        $_guarantee->update($id, $data);
    }
    public function chuyenAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $id = (int)  $this->_request->getParam("id");
        $data["status"] =4;
        $data["creator_end"] = $this->_identity["username"];
        $token = $this->_request->getParam("token");
        $ztoken = md5($this->skey.$id."4");
        
        if($token != $ztoken){
            die("No access");
        }
        $_guarantee = Business_Addon_TransferGuarantee::getInstance();
        $_guarantee->update($id, $data);
    }
    public function xn2Action(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $id = (int)  $this->_request->getParam("id");
        $data["status"] =5;
        $data["creator_end"] = $this->_identity["username"];
        $token = $this->_request->getParam("token");
        $ztoken = md5($this->skey.$id);
        
        if($token != $ztoken){
            die("No access");
        }
        $_guarantee = Business_Addon_TransferGuarantee::getInstance();
        $_guarantee->update($id, $data);
    }

    public function searchAction(){
        $this->_helper->Layout()->disableLayout();
        $this->view->loai = (int) $this->_request->getParam("loai");
  
    }
    public function searchsAction(){
        $this->_helper->Layout()->disableLayout();
        $_city                          = Business_Common_City::getInstance();
        $list_city                      = $_city->getListCity();
        $this->view->list_city          = $list_city;
        $_district                      = Business_Common_District::getInstance();
        
        $id_city                        = 1;
        if($id_city !=0){
            $list_district              = $_district->getListDistrict2($id_city);
        }
        $this->view->list_district      = $list_district;
        $phone = $this->_request->getParam("phone");
        $this->view->phone = $phone;
        $list = Business_Addon_Customer::getInstance()->search($phone);
        if($list == NULL){
            $this->_helper->viewRenderer('iframe-add1');
        }
        $this->view->list = $list;
    }
    public function imeiVendorsAction(){
        $imei = $this->_request->getParam("imei");
        $detail = Business_Addon_HistoryImeiVendors::getInstance()->get_detail_by_imei($imei);
        $this->view->detail = $detail;
        $this->view->imei_old = $imei;
        $_warranty_unit = Business_Addon_WarrantyUnit::getInstance();
        $sproducers = $_warranty_unit->getList();
        $this->view->sproducers = $sproducers;
        
        if($this->_request->isPost()){
            $data_frm = $this->_request->getParams("data_frm");
            $id = (int) $this->_request->getParam("id");
            $ret = array();
            $err = array();
            if((int)$data_frm["idvendors"] == 0){
                $err["id"] ="idvendors";
                $err["msg"] ="Vui lòng nhập nhà cung cấp";
                $ret[] = $err;
            }
            if($data_frm["imei_old"] == NULL){
                $err["id"] ="imei_old";
                $err["msg"] ="Vui lòng nhập imei cũ";
                $ret[] = $err;
            }
            if($data_frm["imei_new"] == NULL){
                $err["id"] ="imei_new";
                $err["msg"] ="Vui lòng nhập imei mới";
                $ret[] = $err;
            }
            if($data_frm["note"] == NULL){
                $err["id"] ="note";
                $err["msg"] ="Vui lòng nhập ghi chú vào đây";
                $ret[] = $err;
            }
            if(strlen($data_frm["note"]) < 10){
                $err["id"] ="note";
                $err["msg"] ="Ghi chú quá ngắn. Vui lòng nhập trên 10 ký tự";
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
                $data["imei_new"] = $data_frm["imei_new"];
                $data["imei_old"] = $data_frm["imei_old"];
                $data["idvendors"] = $data_frm["idvendors"];
                $data["note"] = $data_frm["note"];
                $data["enabled"] = 1;
                $data["datetime"] = date('Y-m-d H:i:s');
                $data["creator"] = $this->_identity["username"];
                if((int)$this->_identity["userid"]>0){
                    if($id ==0){
                        Business_Addon_HistoryImeiVendors::getInstance()->insert($data);
                    }else{
                        Business_Addon_HistoryImeiVendors::getInstance()->update($id,$data);
                    }
                }
                $url = '/admin/user/guarantee/list';
                echo "<script>window.parent.show_success('LƯU THÀNH CÔNG','','$url');</script>";
                die();
            }
        }
        
    }
    
    public function saveCustomerAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $_option = Business_Addon_Options::getInstance();
        $_customer = Business_Addon_Customer::getInstance();
        $data_frm = $this->_request->getParams("data_frm");
        $ret = array();
        $arr = array();
        
        $ret = $_option->checkPhone($data_frm["phone"],"phone");
        if($data_frm["fullname"] ==  NULL){
            $arr["id"] = "fullname";
            $arr["msg"] = "Vui lòng nhập họ tên";
            $ret[] = $arr;
        }
        if(strlen($data_frm["phone"])>9){
            $detail = $_customer->getDetailByPhone($data_frm["phone"]);
        }
        if(count($ret) >0){
            for($i=0;$i<count($ret);$i++){
                $msg = $ret[$i]['msg'];
                $ids = $ret[$i]['id'];
                echo "<script>window.parent.show_msg('$msg','$ids');</script>";
                return;
            }
        }else{
            $sdata["fullname"] = $data_frm["fullname"];
            $sdata["phone"] = $data_frm["phone"];
            $sdata["email"] = $data_frm["email"];
            $sdata["address"] = $data_frm["address"];
            if(strtotime($data_frm["birthday"]) > 0){
               $sdata["birthday"] = $data_frm["birthday"]; 
            }
            $sdata["district"] = $data_frm["district"];
            $sdata["city"] = $data_frm["city"];
            $sdata["datetime"] = date('Y-m-d H:i:s');
            $sdata["enabled"] = 1;
            if($detail != NULL){
                $idcustomer = $detail["id"];
            }else{
                $idcustomer = $_customer->insert($sdata);
            }
            echo "<script>window.parent.completes('LƯU THÀNH CÔNG',$idcustomer);</script>";
            return;
        }
        
    }

    public function iframeAddAction(){
//        die('dang bao tri');
        $this->_helper->Layout()->disableLayout();
        $_option = Business_Addon_Options::getInstance();
        $_zwf_user = Business_Common_Users::getInstance();
        $__customer = Business_Addon_Customer::getInstance();
        
        $__storeid = (int)  $this->_identity["parentid"];
        
        $listmb = $_zwf_user->get_list_by_kh($__storeid,12);
        
        foreach ($listmb as &$vl){
            $vl["fullname"] = "Kỹ thuật ".$vl["fullname"];
        }
        
        $receiver = array();
        $sreceiver = Business_Common_Users::getInstance()->getListUser("",0,1,26);
        foreach ($sreceiver as &$_items){
            $_items["fullname"] ="Luân chuyển ".$_items["fullname"];
            $receiver[$_items["userid"]] = $_items["fullname"];
        }
        
        if($listmb != NULL){
            $sreceiver = array_merge($sreceiver,$listmb);
        }
        
        $this->view->sreceiver = $sreceiver;
//        $dt="3,5,6,8";
//        $slist = Business_Ws_ProductsItem::getInstance()->getProducts2($dt);
//        $this->view->slist = $slist;
        
        $_warranty_unit = Business_Addon_WarrantyUnit::getInstance();
        $sproducers = $_warranty_unit->getList();
        $this->view->sproducers = $sproducers;
        
        $_idregency = $this->_identity["idregency"];
        if($this->isStore($_idregency)){
            $this->_helper->viewRenderer('iframe-add2');
        }
        if($_idregency==12){
            $this->_helper->viewRenderer('iframe-add-kythuat');
        }
        $bgd =0;
        if($_option->isBGD($_idregency) || $_idregency ==48){
            $bgd = 1;
        }
        $this->view->bgd = $bgd;
        $list_store = $_zwf_user->getListByUname(FALSE);
        $this->view->list_store = $list_store;
        $idcustomer = $this->_request->getParam("idcustomer");
        $this->view->idcustomer = $idcustomer;
        $detail_customer = $__customer->getDetail($idcustomer);
        $this->view->detail_customer = $detail_customer;
        $loai_phieu = (int) $this->_request->getParam("loai_phieu");
        $this->view->loai_phieu = $loai_phieu;
        


        
//        $_products = Business_Helpers_Products::getInstance();
//        $menus =  $_option->getMenuById2(3); 
//        $this->view->mennus = $menus; 
        
        $list_hnammobile        = $_option->getCatedBaohanhHnam();
        $this->view->list_hnammobile = $list_hnammobile;
        if($loai_phieu==2)
        $list_cateid = $_option->getMenuById2(4);
        else
        $list_cateid = $_option->getMenuById2(3);
       
        $ret                        = array();
        $arr                        = array();
        $_tmp = array();
        $_tmp[0]["itemid"] = 999997;
        $_tmp[0]["title"] = "Apple";
        if($_tmp !=null){
            $list_cateid = array_merge($_tmp, $list_cateid);
        }
        foreach ($list_cateid as $items){
            if(strpos(strtolower($items["title"]), "iphone")){
                continue;
            }
            $arr["itemid"]          = $items["itemid"];
            $arr["title"]           = $items["title"];
            $ret[]                  = $arr;
        }
       $this->view->list_cateid = $ret;
    }
    public function getWarrantyAction(){
        $_warranty_unit = Business_Addon_WarrantyUnit::getInstance();
        $sproducers = $_warranty_unit->getList();
        $this->view->sproducers = $sproducers;
    }
    public function addWarrantyAction(){
        
    }

    public function saveWarrantyAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $_warranty_unit = Business_Addon_WarrantyUnit::getInstance();
        $data_frm = $this->_request->getParams('data_frm');
        $name_warranty = $this->_request->getParam("name_warranty");
        $ret = array();
        $arr = array();
        if($name_warranty == null){
            $arr["id"] ="name_warranty";
            $arr["msg"] ="Vui lòng nhập tên đơn vị bảo hành mới";
            $ret[] = $arr;
        }
        if(count($ret) >0){
            echo json_encode($ret);
            return;
        }else{
            $data["name"] = $name_warranty;
            $data["address"] = $data_frm["address"];
            $data["phone"] = $data_frm["phone"];
            $data["datetime"] = date('Y-m-d H:i:s');
            $data["creator"] = $this->_identity["username"];
            $data["enabled"] = 1;
  
            if((int)$this->_identity["userid"]>0){
                $_warranty_unit->insert($data);
            }
            $sproducers = $_warranty_unit->getList();
            $arr["id"]="ok";
            $arr["msg"]="ok";
            $arr["data"]=$sproducers;
            $ret[]=$arr;
            echo json_encode($ret);
        }
    }
    public function data_guarantee($data_frm){
        $__option = Business_Addon_Options::getInstance();
        $_name  = $data_frm["itemid"];
        $name = explode("--", $_name);
        $data['id_box']       =   $data_frm["id_box"];
        $data['imei']       =   $__option->replace_space($data_frm["imei"]);
        $data['seri']       =   $__option->replace_space($data_frm["seri"]);
            
            if((int)$data_frm["itemid"]>0){
                $details = Business_Ws_ProductsItem::getInstance()->getDetail($data_frm["itemid"]);
                $data['item_name']  =   $details["title"]; 
            }
            
             
        $data['flag']  =   (int)$data_frm["flag"];
        $purchase_date = str_replace("/", "-", $data_frm["purchase_date"]) ;
        $data['purchase_date']       =   date('Y/m/d',  strtotime($purchase_date));
        
        $data['phone']       =   $data_frm["phone"];
        $data['note2']       =   $data_frm["note2"];
        $data["note"]       =   $data_frm["note"];
        $data["pid_color"]       =   (int)$data_frm["pid_color"];
        $data["damages"]       =   $data_frm["damages"];
        $data["status_receive"]       =   $data_frm["status_receive"];
        $data["acc_attach"]       =   $data_frm["acc_attach"];
        $data["receiver_id"]=   $data_frm["receiver_id"];
        if((int) $data_frm["receiver_id"] >0){
            $data["isdel"] = 1;
        }
        $data["receiver"]   =   $data_frm["receiver"];
        $data["money"]     = str_replace(",", "", $data_frm["money"]);
        $data["money_dvsc"]     = str_replace(",", "", $data_frm["money_dvsc"]);
        $data["money_hnam"]     = str_replace(",", "", $data_frm["money_hnam"]);
        return $data;
    }
    public function history_date($type,$id_guarantee){
        
        $date = date('Y-m-d H:i:s');
        $username = $this->_identity["username"];
        $data["id_guarantee"] = $id_guarantee;
        $data["datetime"] = $date;
        $data["creator"] = $username;
        $data["type"] = $type;
        Business_Addon_GuaranteeHistory::getInstance()->insert($data);
    }

    public function  addAction(){
        if((int)  $this->_identity["userid"] ==0){
            echo "<script>$( document ).ready(function() {
            alert('Thời gian đăng nhập hết hiệu lực, vui lòng đăng nhập lại');
        });</script>";
            die('Thoi gian dang nhap het hieu luc.Vui long dang nhap lai');
        }
        $_option = Business_Addon_Options::getInstance();
        $sreceiver = Business_Common_Users::getInstance()->getListUser("",0,1);
        $this->view->sreceiver = $sreceiver;
        $slist = Business_Ws_ProductsItem::getInstance()->getProducts();
        $this->view->slist = $slist;
        $idregency = $this->_identity["idregency"];
        if($this->_request->isPost()){
            $data_frm = $this->_request->getParams('data_frm');
            $_name  = $data_frm["itemid"];
            $name = explode("--", $_name);
            $ret = $this->required();
            $data= $this->data_guarantee($data_frm);
            $data['itemid']     =  (int)$data_frm["itemid"];
            $price_tmp = str_replace(",", "", $data_frm["price_tmp"]);
            $data["price_tmp"] = (int)$price_tmp;
            
            $money_voucher = str_replace(",", "", $data_frm["money_voucher"]);
            $data["money_voucher"] = (int)$money_voucher;
            
            $advance_payment = str_replace(",", "", $data_frm["advance_payment"]);
            $data["advance_payment"] = (int)$advance_payment;
            if((int)$advance_payment >0){
                $data["advance_payment_date"] = date('Y-m-d H:i:s');
            }
            $voucher = trim($data_frm["voucher"]);
            $data["voucher"] = trim($data_frm["voucher"]);
            
            
            $data["productsid"] = (int)$data_frm["productsid"];
            $data["cated_id"] = (int)$data_frm["cated_id"];
            $data["ncc_ok"] = (int)$data_frm["ncc_ok"];
            $data['idcustomer'] =   $data_frm["idcustomer"];
            $data['status_guarantee']       =   $data_frm["status_guarantee"];
            $dealine = str_replace("/", "-", $data_frm["dealine"]) ;
            $data['dealine']       =   date('Y/m/d',  strtotime($dealine));
            $data["confirm"]    = 1;
            $data["status_where"]     = 1; // chi nhánh
            if($idregency ==48 || $_option->isBGD($idregency)){
                $data["status_where"]     = 3; // Kho bảo hành
            }
            if($data["receiver_id"] >0){
                $data["confirm"]    = 0;
                $data["status_where"]     = 2; // nhân viên
                $data["isdel"] = 1;
                
            }
            $data["confirm_name"]    = $this->_identity["username"];
            $data["status"]     = 1; 
            
            $data["complete"]     = 0; // chưa sửa
            $data["storeid"]     = $this->_identity["parentid"];
            $data["datetime"]   = date('Y-m-d H:i:s');
            $data["creator"]    = $this->_identity["username"];
            
            $data["storeid_in"]     = (int)$data_frm["storeid_in"];
            $data["quoc_te"]     = (int)$data_frm["quoc_te"];
            
            $data["enabled"]    = 1; 
            $data["loai_phieu"] = (int)$data_frm["loai_phieu"];
            
            if(count($ret) >0){
                for($i=0;$i<count($ret);$i++){
                    $msg = $ret[$i]['msg'];
                    $ids = $ret[$i]['id'];
                    echo "<script>window.parent.show_msg('$msg','$ids');</script>";
                    return;
                }
            }else{
                if((int)$this->_identity["userid"]>0){
                    $last_id = Business_Addon_Guarantee::getInstance()->insert($data);
                    if($data["receiver_id"] >0){
                        $this->history_date($type=1,$last_id);// type = 1 ngày giao cho nhân viên
                    }
                }
                if($voucher != NULL){
                    $this->update_voucher($voucher);
                }
                $__token = md5($this->skey . $last_id);
                $__msg ='Lưu thành công';
                echo "<script>window.parent.completes('$__msg',$last_id,'$__token');</script>";
                return;
            }
        }
    }
    public function printAction(){
        $this->_helper->Layout()->disableLayout();
        $_option = Business_Addon_Options::getInstance();
        $id = $this->_request->getParam("id");
        $token = $this->_request->getParam("token");
        $ztoken = md5($this->skey . $id);
        if ($token != $ztoken) {
            die('No access');
        }
        $_guarantee = Business_Addon_Guarantee::getInstance();
        $detail = $_guarantee->getDetail($id);
        
        $_zwf_user = Business_Common_Users::getInstance();
        $list_store = $_zwf_user->getListByUname(FALSE);
        $storename = array();
        $address = array();
        foreach ($list_store as $items){
            $storename[$items["userid"]] = $items["storename"];
            $storename3[$items["userid"]] = $items["abbreviation"];
            $address[$items["userid"]] = $items["address"];
            $phone_store[$items["userid"]] = $items["phone"];
            
        }
        $this->view->address = $address[$this->_identity["parentid"]];
        $detail_mb = $_zwf_user->getDetail((int)$detail["receiver_id"]);
        $mb_name = $detail_mb["fullname"];
        $this->view->mb_name = $mb_name;
        $this->view->storename = $storename;
        $this->view->storename3 = $storename3;
        $this->view->storename2 = $storename[$this->_identity["parentid"]];
        $this->view->phone_store = $phone_store[$this->_identity["parentid"]];
        if((int)$this->_identity["parentid"] ==0){
            $this->view->storename2 = '';
            $this->view->address = '148 Nguyễn Cư Trinh,<br/>P. Nguyễn Cư Trinh, Q.1, TP.HCM';
        }
        $__customer = Business_Addon_Customer::getInstance();
        if($detail["idcustomer"] != NULL){
            $detail_customer = $__customer->getDetail((int)$detail["idcustomer"]);
        }
        $this->view->detail_customer = $detail_customer;
        
        $list_color = Business_Ws_NewsItem::getInstance()->getListByCateId(722);
        foreach ($list_color as $items) {
            $color_name[$items["itemid"]] = $items["title"];
        }
        $this->view->color_name = $color_name;
        
        $detail_user = Business_Common_Users::getInstance()->getDetailByUserName($detail["creator"]);
        $detail["fullname"] = $detail_user["fullname"];
        $this->view->detail = $detail;
        $token_id = md5("PRINTBHSC2017".$detail["imei"].$detail["id"]);
        $this->view->token_id = $token_id;
    }
    public function detailAction(){
        $_option = Business_Addon_Options::getInstance();
        $list_type_bh = $_option->get_type_bh(); 
        $this->view->list_type_bh = $list_type_bh;
        $list_ok = $_option->get_ok();
        $this->view->list_ok = $list_ok;
        
        $__customer = Business_Addon_Customer::getInstance();
        $_zwf_user = Business_Common_Users::getInstance();
        $id = $this->_request->getParam("id");
        $token = $this->_request->getParam("token");
        $this->view->token =$token;
        $ztoken = md5($this->skey . $id);
        if ($token != $ztoken) {
            $this->_redirect('/admin/home');
        }
        $_guarantee = Business_Addon_Guarantee::getInstance();
        $detail = $_guarantee->getDetail($id);
        $this->view->detail = $detail;
        
        
        $idcustomer = $detail["idcustomer"];
        $detail_customer = $__customer->getDetail($idcustomer);
        $this->view->detail_customer = $detail_customer;
        
        $idregency = $this->_identity["idregency"];
        $___userid = $this->_identity["userid"];
        $this->view->userids = $___userid;
        $this->view->idregency = $idregency;
        if($this->isStore($idregency)){
            $this->_helper->viewRenderer('detail-store');
            $__storeid = (int)  $this->_identity["parentid"];
            $listmb = $_zwf_user->get_list_by_kh($__storeid,12);
            foreach ($listmb as &$vl){
                $vl["fullname"] = "Kỹ thuật ".$vl["fullname"];
            }
        }
        if($_option->isBGD($idregency) || $this->isBHVT($idregency)){
            $this->_helper->viewRenderer('detail-admin');
        }
        if($idregency ==26){
//            if($detail["complete"] ==0 && $detail["confirm"]==0){
//                $this->_redirect('/admin/user/guarantee/list');
//            }
        }
//        if($idregency ==12){
//            $this->_helper->viewRenderer('detail-kythuat');
//        }
        if($idregency ==49){
            $this->_helper->viewRenderer('detail-view');
        }
        $list_store = $_zwf_user->getListByUname(FALSE);
        $this->view->list_store = $list_store;
        $storename = array();
        foreach ($list_store as $items){
            $storename[$items["userid"]] = $items["storename"];
        }
        $detail_mb = $_zwf_user->getDetail($detail["receiver_id"]);
        $mb_name = $detail_mb["fullname"];
        $this->view->mb_name = $mb_name;
        $this->view->storename = $storename;
        $list_status_guarantee = $_option->getBH();
        $this->view->status_guarantee = $list_status_guarantee[$detail["status_guarantee"]];
        
        $slist = Business_Addon_GuaranteeWarranty::getInstance()->getListByGuarantee($id);
        $s_warranty1 = array();
        $s_warranty2 = array();
        foreach ($slist as $_item){
            if($_item["status"]==0){
                $s_warranty1[$_item["id_guarantee"]][] = $_item;
            }else{
               $s_warranty2[$_item["id_guarantee"]][] = $_item; 
            }
            $s_warranty[$_item["id_guarantee"]][] = $_item; 
        }
        $this->view->slist = $slist;
        $this->view->warranty1 = $s_warranty1[$id];
        $this->view->warranty1 = $s_warranty1[$id];
        $this->view->warranty2 = $s_warranty2[$id][0];
        
        $detail_warranty = $s_warranty2[$id][0];
        $this->view->detail_warranty = $detail_warranty;
        
        
        $_warranty_unit = Business_Addon_WarrantyUnit::getInstance();
        $sproducers = $_warranty_unit->getList();
        $name_producers = array();
        foreach ($sproducers as $items){
            $name_producers[$items["id"]] = $items["name"]."-".$items["address"]."-".$items["phone"];
        }
        $this->view->sproducers = $sproducers;
        $this->view->name_producers = $name_producers;
        
        
        
        $sreceiver1 = Business_Common_Users::getInstance()->getListUser("",0,1,26);
        if($this->isBHVT($idregency) || $_option->isBGD($idregency)){
            $sreceiver2 =  Business_Common_Users::getInstance()->get_list_by_kh(0,12);
        }
        
        $sreceiver = array();
        if($sreceiver1 != NULL){ 
            $sreceiver = array_merge($sreceiver,$sreceiver1);
        }
        if($sreceiver2 != NULL){
            $sreceiver = array_merge($sreceiver,$sreceiver2);
        }
        
        
        $receiver = array();
        foreach ($sreceiver as &$_items){
            if($_items["idregency"]==12){
                $tt ="Kỹ thuật";
            }
            if($_items["idregency"]==26){
                $tt ="Luân chuyển";
            }
            $_items["fullname"] ="$tt ".$_items["fullname"];
        }
        if($listmb != NULL){
            $sreceiver = array_merge($sreceiver,$listmb);
        }
        foreach ($sreceiver as &$_items2){
            $receiver[$_items2["userid"]] = $_items2["fullname"];
        }
        $this->view->sreceiver = $sreceiver;
        $this->view->receiver = $receiver;
        
        $lcomplete = $_option->getCompleteKBH();
        $this->view->lcomplete = $lcomplete;
        $complete_color = array();
        $status_complete = array();
        foreach ($lcomplete as $val){
            $complete_color[$val["id"]] = $val["color"];
            $status_complete[$val["id"]] = $val["name"];
        }
        $this->view->complete_color = $complete_color;
        $this->view->status_complete = $status_complete;
        
        $show =0;
        if($detail_warranty ==null){
           $show =1; 
        }else{
            if($detail_warranty["status"] == 0){
                $show =1;
            }
        }
        $this->view->show = $show;
        $dt="3,5";
        $slist_products = Business_Ws_ProductsItem::getInstance()->getProducts2($dt);
        $this->view->slist_products = $slist_products;
        
        $list_regency = Business_Addon_Regency::getInstance()->getList();
        $this->view->list_regency = $list_regency;
        
        $list_department = Business_Addon_Department::getInstance()->getList();
        $department = array();
        foreach ($list_department as $item){
            $department[$item["id"]] = $item["name"];
        }
        $this->view->department = $department;
        $this->view->list_department = $list_department;
        
        
        $___color = Business_Addon_ProductsColor::getInstance();
        $color_name = array();
        $list_color = $___color->getListById($detail["itemid"]);
        $this->view->list_color = $list_color;
//        echo "<pre>";
//        var_dump($list_color,$detail["pid_color"]);
//        die();
        $scolor = Business_Ws_NewsItem::getInstance()->getListByCateId(722);
        foreach ($scolor as $items) {
            $color_name[$items["itemid"]] = $items["title"];
        }
        $this->view->color_name = $color_name;
        
    }
    public function listmoneyAction(){
        $this->view->skey = $this->skey;
        $list_collectingmoney = Business_Addon_Options::getInstance()->get_status_collectingmoney();
        $this->view->list_collectingmoney = $list_collectingmoney;
        
        $sreceiver = Business_Common_Users::getInstance()->getListUser("",0,1,26);
        $this->view->sreceiver = $sreceiver;
        $receiver = array();
        foreach ($sreceiver as $_items){
            $receiver[$_items["userid"]] = $_items["fullname"];
        }
        $this->view->receiver = $receiver;
        
        $_option = Business_Addon_Options::getInstance();
        $__bs = Business_Addon_TransferGuarantee::getInstance();
        $idregency = $this->_identity["idregency"];
        $storeid = (int)$this->_request->getParam("storeid");
        $receiverid = (int)$this->_request->getParam("receiverid");
        if($idregency ==26   || $idregency==12){ // nhân viên bảo hành
            $this->_helper->viewRenderer('listmoney2');
            $receiverid = $this->_identity["userid"];
            $list = $__bs->get_list_by_storeid_receiverid($storeid,$receiverid);
        }
        if($this->isStore($idregency)){ // cửa hàng
            $storeid = $this->_identity["parentid"];
            $list = $__bs->get_list_by_storeid_receiverid($storeid);
        }
        if($_option->isBGD($idregency) || $idregency ==48){ // KBH
            $this->_helper->viewRenderer('listmoney3');
            $list = $__bs->get_list_by_storeid_receiverid($storeid,$receiverid);
        }
        $this->view->list = $list;
        $list_status_money = $_option->get_status_money();
        $this->view->list_status_money = $list_status_money;
    }

    public function collectingmoneyAction(){
        $__bs = Business_Addon_Guarantee::getInstance();
        $list_collectingmoney = Business_Addon_Options::getInstance()->get_status_collectingmoney();
        $this->view->list_collectingmoney = $list_collectingmoney;
        
        $sreceiver = Business_Common_Users::getInstance()->getListUser("",0,1,26);
        $this->view->sreceiver = $sreceiver;
        $receiver = array();
        foreach ($sreceiver as $_items){
            $receiver[$_items["userid"]] = $_items["fullname"];
        }
        $this->view->receiver = $receiver;
        
        $idguarantee = (int)$this->_request->getParam("id");
        $this->view->idguarantee = $idguarantee;
        $token = $this->_request->getParam("token");
        $ztoken = md5($this->skey.$idguarantee);
        if($token != $ztoken){
            die('No access');
        }
        
        if($idguarantee >0){
            
            $detail = $__bs->getDetail($idguarantee);
            
            if($detail ==NULL){
                $this->_redirect('/admin');
            }
            $this->view->detail= $detail;
           
            $check_layout = Business_Addon_TransferGuarantee::getInstance()->get_detail_by_idguarantee($idguarantee);
            
            if($check_layout != NULL){
                $this->_helper->viewRenderer('collectingmoney2');
            }
            $this->view->check_layout = $check_layout;
            if($this->_request->isPost()){
                $data_frm = $this->_request->getParams('data_frm');
                $ret = array();
                $err = array();
                if((int)$data_frm["receiverid"]==0){
                    $err["id"] = "receiverid";
                    $err["msg"] = "Vui lòng chọn nhân viên";
                    $ret[] = $err;
                }
                if((int)$data_frm["money"]==0){
                    $err["id"] = "money";
                    $err["msg"] = "Vui lòng nhập số tiền";
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
                    
                    $data["money"] = str_replace(",", "", $data_frm["money"]);
                    $data["imei"] = $detail["imei"];
                    $data["receiverid"] = $data_frm["receiverid"];
                    $data["storeid"] = $this->_identity["parentid"];
                    $data["enabled"]= 1;
                    $data["status"]= 2;
                    $data["creator"] = $this->_identity["username"];
                    $data["datetime"] = date('Y-m-d H:i:s');
                    $data["idguarantee"] = $idguarantee;
                    if((int)$this->_identity["userid"]>0){
                        $last_id = Business_Addon_TransferGuarantee::getInstance()->insert($data);
                    }
                    $__msg ='Lưu thành công';
                    $url ='/admin/user/guarantee/list';
                    echo "<script>window.parent.show_success('$__msg',$last_id,'');</script>";
                    die();
                }
            }
        }
    }

    public function listAction(){

        // username   parentid  hnmobile
        $listChan = array();
        $listdt = array('hnam_thinhtm');
        $listpk = array('hnam_vinhpt');
        $loai =   $this->view->loai = (int)$this->_request->getParam("loai");

        foreach ($listChan as $key => $value) {
            if(strpos(  $this->_identity["username"],$value) !== false ){
                die('no access');

            }
        }

        foreach ($listpk as $key => $value) {
            if(strpos(  $this->_identity["username"],$value) !== false and  $loai != 2 ){
                $link = "/admin/user/guarantee/list?".rand().'='.rand()."&loai=2";
                header('Location: '.$link);
                die();
            }
        }

        foreach ($listdt as $key => $value) {
            if(strpos(  $this->_identity["username"],$value) !== false and  $loai !=  1 ){
                $link = "/admin/user/guarantee/list?".rand().'='.rand()."&loai=1";
                header('Location: '.$link);
                die();
            }
        }



       


        $_zwf_user = Business_Common_Users::getInstance();
        $list_store = $_zwf_user->getListByUname(FALSE);
        $__uid = (int)  $this->_identity["userid"];
        $detail_user = $_zwf_user->getDetail($__uid);
        $this->view->detail_user = $detail_user;
        $storename = array();
        $storename[0] ="KBH";
        foreach ($list_store as $__st){
            $storename[$__st["userid"]] = $__st["abbreviation"];
            
            $__str_storeid[] = $__st["userid"];
        }
        $str_storeid = implode(",", $__str_storeid);
        
        $this->view->storename = $storename;
        $this->view->list_store = $list_store;
        $_option = Business_Addon_Options::getInstance();
        $list_status_guarantee = $_option->getBH();
        
        $this->view->list_ok = $_option->get_ok();
        $ok = $this->_request->getParam("ok");
        if($ok ==NULL){
            $ok =-1;
        }
        $this->view->ok = $ok;
        $bhang = (int)$this->_request->getParam("bhang");
        $this->view->bhang = $bhang;
        $quoc_te = (int)$this->_request->getParam("quoc_te");
        $this->view->quoc_te = $quoc_te;
        
        $list_transfers = $_option->get_transfers();
        $this->view->list_transfers = $list_transfers;
        $transfers = $this->_request->getParam("transfers");
        $this->view->transfers = $transfers;
        $status_guarantee = $this->_request->getParam("status_guarantee");
        $this->view->status_guarantee = $status_guarantee;
        
        $complete = $this->_request->getParam("complete");
        if($complete == NULL){
            $complete = -1;
        }
        $this->view->complete = $complete;
        
        $this->view->list_status_guarantee = $list_status_guarantee;
        $lcomplete = $_option->getCompleteKBH();
        $this->view->lcomplete = $lcomplete;
        $complete_color = array();
        foreach ($lcomplete as $val){
            $complete_color[$val["id"]] = $val["color"];
        }
        $this->view->complete_color = $complete_color;
        
        $list_confirm = $_option->getConfirm();
        $name_confirm = array();
        $icon = array();
        foreach ($list_confirm as $cf){
            $name_confirm[$cf["id"]] = $cf["name"];
            $icon[$cf["id"]] = $cf["icon"];
            $icon2[$cf["id"]] = $cf["icon2"];
        }
        $this->view->icon = $icon;
        $this->view->icon2 = $icon2;
        $this->view->name_confirm = $name_confirm;
        $this->view->list_confirm = $list_confirm;
        
        $_guarantee = Business_Addon_Guarantee::getInstance();
        
        $d=date('Y-m-d');
        $k=$_option->pre_date($d, 1);
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
        
        $userid = $this->_identity["userid"];
        $this->view->uid = $userid;
        $creator = $this->_identity["username"];
        $this->view->creator = $creator;
        $storeid = $this->_identity["parentid"];
        $idregency = $this->_identity["idregency"];
        $status_where = 2;
        $bgd =0;
        if($_option->isBGD($idregency) || $this->isBHVT($idregency) || $loai>0){
            $status_where = 3;
            $userid = (int) $this->_request->getParam("receiver_id");
            $storeid = $this->_request->getParam("storeid");
            $this->view->storeid = $storeid;
            if($storeid ==NULL){
                $storeid =0;
            }
            if($storeid ==-1){
                $storeid =$str_storeid;
            }
            $bgd =1;
            $this->_helper->viewRenderer('list-admin');
        }
        
        
        $this->view->bgd = $bgd;
        
        if($this->isStore($idregency)){
            $status_where = 1;
            $userid = (int) $this->_request->getParam("receiver_id");
            $this->_helper->viewRenderer('list-store');
        }
        if($idregency ==12){
            $userid = (int) $this->_request->getParam("receiver_id");
        }
        if($idregency ==26){
            $storeid =-2;
        }
        $this->view->receiver_id = $userid;
        $storeid_in = (int)$this->_request->getParam('storeid_in');
        $flag = $this->_request->getParam('flag');
        $this->view->storeid_in = $storeid_in;
        $this->view->status_where = $status_where;
        $enabled =1;
//        if($_option->isBGD($idregency) || $this->isBHVT($idregency)){
//           $enabled =''; 
//        }
        $out = (int)$this->_request->getParam("out");
        $isxuat = (int)$this->_request->getParam("isxuat");
        if($isxuat ==1){
           $out =1; 
        }
        $this->view->isxuat = $isxuat;
//        if($out ==1){
//            $pdata = Business_Addon_GuaranteeHistory::getInstance()->get_list_by_type($start,$end,$__istype=4);
//            $__date_out = array();
//            foreach ($pdata as $__item){
//                $__id_guarantee[] = $__item["id_guarantee"];
//                $__date_out[$__item["id_guarantee"]] = $__item["datetime"];
//            }
//            if($__id_guarantee != NULL){
//                $str_id_guarantee = implode(",", $__id_guarantee);
//                $sdata = $_guarantee->get_list_by_id($str_id_guarantee,$quoc_te);
//            }
//        }else{
//            if($idregency ==12){
//                $storeid =0;
//            }
//            $sdata = $_guarantee->getListByUserId($userid,$start,$end,$storeid,$complete,$status_guarantee,$transfers,$enabled,$storeid_in,$ok,$bhang,$quoc_te);
//        }
        

        $loai_phieu = (int) $this->_request->getParam("loai_phieu");
        if($loai>0)
        {
            $loai_phieu= $loai;
        }
        if($out ==1){
            $list = $_guarantee->get_list_out2($start, $end, $storeid, $flag,1);
        }else{
            $list = $_guarantee->getListByUserId($userid,$start,$end,$storeid,$complete,$status_guarantee,$transfers,$enabled,$storeid_in,$ok,$bhang,$quoc_te,$loai_phieu);
        }
        
        
        
        foreach ($list as $item){
            if($item["creator"] ==NULL){
                continue;
            }
            if($bgd==1){
                if($storeid ==0 && $item["storeid"] !=0){
                    continue;
                }
                
            }
            
            $id_guarantee[] = $item["id"];
            if($item["confirm"] ==1){
                foreach ($lcomplete as $val){
                    if($item["complete"] == $val["id"]){
                        $total[$item["complete"]]++;
                        $_totals++;
                    }
                }
            }else{
                $total_cxn[0]++;
            }
            $array_customer[] = $item["idcustomer"];
        }
       if($array_customer){
           $str_idcustomer = implode(",", $array_customer);
           $list_customer = Business_Addon_Customer::getInstance()->get_list_by_id($str_idcustomer);
           foreach ($list_customer as $cs){
               $array_phone[$cs["id"]] = $cs["phone"];
               $array_name[$cs["id"]] = $cs["fullname"];
           }
       }
        
        $this->view->array_name = $array_name;
        $this->view->array_phone = $array_phone;
        $this->view->total = $total;
        $this->view->total_cxn = $total_cxn;
        
        
        
        $this->view->totalss = count($list);
        
        $str_guaranteeID = implode(",", $id_guarantee);
        if($str_guaranteeID != NULL){
            $slist = Business_Addon_GuaranteeWarranty::getInstance()->getListByGuarantee($str_guaranteeID);
        }
        
        $s_warranty = array();
        foreach ($slist as $_item){
            $s_warranty[$_item["id_guarantee"]][] = $_item;
            $s_warranty_time[$_item["id_guarantee"]] = $_item["datetime"];
            $s_warranty_dealine[$_item["id_guarantee"]] = $_item["dealine"];
        }
        
        
        
        
        foreach ($list as &$item){
            $item["s_warranty"] = $s_warranty[$item["id"]];
        }
        $this->view->list = $list;
        $this->view->skey = $this->skey;
        $this->view->s_warranty_time = $s_warranty_time;
        $this->view->s_warranty_dealine = $s_warranty_dealine;
        
        $_warranty_unit = Business_Addon_WarrantyUnit::getInstance();
        $sproducers = $_warranty_unit->getList();
        $name_producers = array();
        
        foreach ($sproducers as $items){
            $name_producers[$items["id"]] = $items["name"];
            $receipt[$items["id"]] = $items["receipt"];
        }
        $this->view->name_producers = $name_producers;
        
        $lstatus = $_option->getStatusKBH();
        $status_color = array();
        foreach ($lstatus as $val){
            $status_color[$val["id"]] = $val["color"];
        }
        $this->view->lstatus = $lstatus;
        $this->view->status_color = $status_color;
        if($storeid >0){
            $listmb = Business_Common_Users::getInstance()->get_list_by_kh($storeid,12);
            foreach ($listmb as &$vl){
                $vl["fullname"] = "Kt ".$vl["fullname"];
            }
        }
        
        $receiver = array();
        $sreceiver = Business_Common_Users::getInstance()->getListUser("",0,1,26);
        
        if($this->isBHVT($idregency) || $_option->isBGD($idregency)){
            $sreceiver =  Business_Common_Users::getInstance()->get_list_by_kh(0,12);
        }
        
        
        
        
        foreach ($sreceiver as &$_items){
            $_items["fullname"] ="lc ".$_items["fullname"];
        }
        
        if($listmb != NULL){
            $sreceiver = array_merge($sreceiver,$listmb);
        }
        
//        $sreceiver = Business_Common_Users::getInstance()->getListUser("",0,1,26);
        $this->view->sreceiver = $sreceiver;
//        $receiver = array();
        foreach ($sreceiver as $_items2){
            $receiver[$_items2["userid"]] = $_items2["fullname"];
        }
        $this->view->receiver = $receiver;
        
        
        $list_bhang = $_option->get_list_bhang();
        $this->view->list_bhang = $list_bhang;
        
        $this->view->idregency = $idregency;
        $this->view->username = $this->_identity["username"];
        $this->view->out = $out;
        
        $tstatus = (int)  $this->_request->getParam("tstatus");
        if($tstatus ==1){
            $this->_helper->viewRenderer('list-admin-1');
        }
        if($tstatus ==2){
            
            $this->_helper->viewRenderer('list-admin-2');
        }
        if($tstatus ==3){
            $this->_helper->viewRenderer('list-admin-3');
        }
        
        $this->view->tstatus = $tstatus;
    }
    public function statisticsAction(){
        $x = $_SERVER['REQUEST_URI'];
        $parsed = parse_url($x);
        $query = $parsed['query'];

        parse_str($query, $params);

        unset($params['st']);
        $url = http_build_query($params);
        $this->view->urls = $url;
        
        $dbs = (int)  $this->_request->getParam("dbs");
        $_zwf_user = Business_Common_Users::getInstance();
        $list_store = $_zwf_user->getListByUname(FALSE);
        $storename = array();
        $storename[0] ="KBH";
        foreach ($list_store as $__st){
            $storename[$__st["userid"]] = $__st["abbreviation"];
            
            $__str_storeid[] = $__st["userid"];
        }
        $str_storeid = implode(",", $__str_storeid);
        
        $this->view->storename = $storename;
        $this->view->list_store = $list_store;
        $_option = Business_Addon_Options::getInstance();
        $list_type_bh = $_option->get_type_bh(); 
        $this->view->list_type_bh = $list_type_bh;
        
        $list_status_guarantee = $_option->getBH();
        
        $this->view->list_ok = $_option->get_ok();
        
        $bhang = (int)$this->_request->getParam("bhang");
        $this->view->bhang = $bhang;
        
        $list_transfers = $_option->get_transfers();
        $this->view->list_transfers = $list_transfers;
        $transfers = $this->_request->getParam("transfers");
        $this->view->transfers = $transfers;
        $status_guarantee = $this->_request->getParam("status_guarantee");
        $this->view->status_guarantee = $status_guarantee;
        
        $flag = (int)$this->_request->getParam("flag");
        $this->view->flag = $flag;
        $price_tmp = $this->_request->getParam("price_tmp");
        if($price_tmp==NULL){
            $price_tmp = -1;
        }
        $price_thu = $this->_request->getParam("price_thu");
        if($price_thu==NULL){
            $price_thu = -1;
        }
        $this->view->price_tmp = $price_tmp;
        $this->view->price_thu = $price_thu;
        $complete = $this->_request->getParam("complete");
        if($complete == NULL){
            $complete = -1;
        }
        $this->view->complete = $complete;
        
        $this->view->list_status_guarantee = $list_status_guarantee;
        $lcomplete = $_option->getCompleteKBH();
        $this->view->lcomplete = $lcomplete;
        $complete_color = array();
        foreach ($lcomplete as $val){
            $complete_color[$val["id"]] = $val["color"];
        }
        $this->view->complete_color = $complete_color;
        
        $list_confirm = $_option->getConfirm();
        $name_confirm = array();
        $icon = array();
        foreach ($list_confirm as $cf){
            $name_confirm[$cf["id"]] = $cf["name"];
            $icon[$cf["id"]] = $cf["icon"];
            $icon2[$cf["id"]] = $cf["icon2"];
        }
        $this->view->icon = $icon;
        $this->view->icon2 = $icon2;
        $this->view->name_confirm = $name_confirm;
        $this->view->list_confirm = $list_confirm;
        
        $_guarantee = Business_Addon_Guarantee::getInstance();
        
        $d=date('Y-m-d');
        $k=$_option->pre_date($d, 1);
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
        
        $userid = $this->_identity["userid"];
        $storeid = $this->_identity["parentid"];
        $idregency = $this->_identity["idregency"];
        $bgd =0;
        if($_option->isBGD($idregency) || $this->isBHVT($idregency)){
            $userid = (int) $this->_request->getParam("receiver_id");
            $storeid = $this->_request->getParam("storeid");
            
            $bgd =1;
        }
        $this->view->storeid = $storeid;
        if($storeid ==NULL){ // kho bảo hành
            $storeid =0;
        }
        if($storeid ==-1){ // tất cả chi nhánh
            $storeid =$str_storeid;
        }
//        if($storeid ==-2){ // tất cả gồm kho bảo hành
//        }
        $this->view->bgd = $bgd;
        
        $this->view->receiver_id = $userid;
        $storeid_in = (int)$this->_request->getParam('storeid_in');
        $this->view->storeid_in = $storeid_in;
        $isxuat = $this->_request->getParam("isxuat");
        if($isxuat ==NULL){
            $isxuat =1;//tất cả
        }
        $this->view->isxuat = $isxuat;
        if($isxuat ==1){
            $list = $_guarantee->get_list_out($start, $end, $storeid, $flag);
        }else{
            $list = $_guarantee->get_list_in($start, $end, $storeid, $flag);
        }
        $qq = (int)  $this->_request->getParam("qq");
        foreach ($list as $item){
            
            if($item["imei"] != NULL){
                $imei_bbmh[] = $item["imei"];
            }
            if($item["seri"] != NULL){
                $seri_bbmh[] = $item["seri"];
            }
            $id_guarantee[] = $item["id"];
        }
        
        $cateid_bbmh ="890,867,901,905";
       if($imei_bbmh != NULL){
           foreach ($imei_bbmh as &$bbmh){
               $bbmh = "'$bbmh'";
           }
           $str_imei_bbmh = implode(",", $imei_bbmh);
           
            $list_bbmh = Business_Addon_UsersProducts::getInstance()->get_list_by_imei_cateid2($str_imei_bbmh,$cateid_bbmh);
            foreach ($list_bbmh as $vbbmh){
                $iimes1 = strtoupper(trim($vbbmh["imes"]));
                if($vbbmh["cated_id"]==905){ // bảo hành mở rộng
                    $check_bhmr[$iimes1] = 1;
                }else{ // bao bể màn hình
                    $check_bbmh[$iimes1] = 1;
                }
                
            }
       }
       if($seri_bbmh != NULL){
           foreach ($seri_bbmh as &$sbbmh){
               $sbbmh = "'$sbbmh'";
           }
           $str_seri_bbmh = implode(",", $seri_bbmh);
           
            $list_bbmh2 = Business_Addon_UsersProducts::getInstance()->get_list_by_imei_cateid2($str_seri_bbmh,$cateid_bbmh);
            foreach ($list_bbmh2 as $vbbmh2){
                $iseri1 = strtoupper(trim($vbbmh2["imes"]));
                if($vbbmh2["cated_id"]==905){ // bảo hành mở rộng
                    $check_bhmr2[$iseri1] = 1;
                }else{ // bao bể màn hình
                    $check_bbmh2[$iseri1] = 1;
                }
            }
       }
        
        $str_guaranteeID = implode(",", $id_guarantee);
        $money_thu = array();
        if($str_guaranteeID != NULL){
            $slist = Business_Addon_GuaranteeWarranty::getInstance()->getListByGuarantee($str_guaranteeID);
            
            $__guarantee_transfer = Business_Addon_TransferGuarantee::getInstance();
            $list_transfer = $__guarantee_transfer->get_list_by_idguarantee($str_guaranteeID);
            foreach ($list_transfer as $vl){
                $money_thu[$vl["idguarantee"]] = $vl["money"];
            }
        }
        $this->view->money_thu = $money_thu;
        
        $s_warranty = array();
        foreach ($slist as $_item){
            $s_warranty[$_item["id_guarantee"]][] = $_item;
        }
        
        foreach ($list as &$kitem){
            if($storeid==-2){
                if($kitem["storeid"]==764){
                    continue;
                }
            }
            
            $kitem["check_bbmh"]=0;
            $kitem["check_bhmr"]=0;
            
            $imeii = strtoupper(trim($kitem["imei"]));
            $iseri = strtoupper(trim($kitem["seri"]));
            if($qq==1 && $kitem["id"] ==11674){
                echo "<pre>";
                var_dump($check_bbmh[$imeii],$check_bbmh2[$iseri],$check_bhmr[$imeii],$check_bhmr2[$iseri]);
                die();
            }
            if( ($imeii != NULL && (int)$check_bbmh[$imeii]==1) || ($iseri != NULL && (int)$check_bbmh2[$iseri]==1) ){
                $kitem["check_bbmh"]=1;
            }
            if( ($imeii != NULL && (int)$check_bhmr[$imeii]==1) || ($iseri != NULL && (int)$check_bhmr2[$iseri]==1) ){
                $kitem["check_bhmr"]=1;
            }
            $kitem["s_warranty"] = $s_warranty[$kitem["id"]];
        }
        if($qq==1){
                echo "<pre>";
                var_dump($list_bbmh,$list_bbmh2);
                die();
            }
        $this->view->list = $list;
        $this->view->skey = $this->skey;
        
        $_warranty_unit = Business_Addon_WarrantyUnit::getInstance();
        $sproducers = $_warranty_unit->getList();
        $name_producers = array();
        
        foreach ($sproducers as $items){
            $name_producers[$items["id"]] = $items["name"];
        }
        $this->view->name_producers = $name_producers;
        
        $this->view->slist_warranty = $sproducers;
        $this->view->s_warranty = $s_warranty;
        
        
        $lstatus = $_option->getStatusKBH();
        $status_color = array();
        foreach ($lstatus as $val){
            $status_color[$val["id"]] = $val["color"];
        }
        $this->view->lstatus = $lstatus;
        $this->view->status_color = $status_color;
        
        
        $sreceiver = Business_Common_Users::getInstance()->getListUser("",0,1,26);
        $this->view->sreceiver = $sreceiver;
        $receiver = array();
        foreach ($sreceiver as $_items){
            $receiver[$_items["userid"]] = $_items["fullname"];
        }
        $this->view->receiver = $receiver;
        
        
        $list_bhang = $_option->get_list_bhang();
        $this->view->list_bhang = $list_bhang;
        
        $this->view->idregency = $idregency;
        $this->view->username = $this->_identity["username"];
        $day = "_".date('YmdHis');
        $title_ex = "BAOHANHSUACHUA_$day";
        SEOPlugin::setTitle($title_ex);
//        
    }
    
    public function exports($list,$storename){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true); 
        $day = "_".date('YmdHis');
        header("Content-Disposition: attachment; filename=BAOHANHSUACHUA_$day.csv");
        header("Content-Type: text/csv; charset=utf-8");
        header('Content-Type: charset=utf-8');
        $finalData= array();
//        $strItem ="MAPHIEU\tNGAYBH\tNGAYXUAT\tLOAI\tMAY\tIMEI\tCHINHANH\tTENDVSC\tTONGCHI\tTONGHNAMBAOHANH\tKHUYENMAI\tTHUTIENKHACHHANG\tTAMUNG\tTIENDATHU\tTHUCTE\tDUKIENCHI"."\r\n";            
        $strItem ="MAPHIEU\tNGAYBH\tNGAYXUAT\tLOAI\tMAY\tIMEI\tCHINHANH\tTENDVSC\tTONGCHI\tTONGHNAMBAOHANH\tKHUYENMAI\tTHUTIENKHACHHANG\tBAOBE\tQUOCTE\tCHENHLECH\tBHKHAC"."\r\n";            
        foreach ($list as $ims){
            $id_guarantee[] = $ims["id"];
        }
        $str_guaranteeID = implode(",", $id_guarantee);
        if($str_guaranteeID != NULL){
            $slist = Business_Addon_GuaranteeWarranty::getInstance()->getListByGuarantee($str_guaranteeID);
        }
        
        $s_warranty = array();
        foreach ($slist as $_item){
            $s_warranty[$_item["id_guarantee"]][] = $_item;
        }
        $_warranty_unit = Business_Addon_WarrantyUnit::getInstance();
        $sproducers = $_warranty_unit->getList();
        $name_producers = array();
        
        foreach ($sproducers as $items){
            $name_producers[$items["id"]] = $items["name"];
        }
        $this->view->name_producers = $name_producers;
        
        $this->view->slist_warranty = $sproducers;
        $this->view->s_warranty = $s_warranty;
        
        foreach ($list as $items){
            if((int)$items["money_dvsc"]==0 && (int)$items["money_hnam"]==0 && (int)$items["money"]==0){
                continue; 
            }
            $loai ='';
            if($items["flag"] ==1){
               $loai ='Công ty'; 
            }
            if($items["flag"] ==2){
               $loai ='Hnam'; 
            }
            $__id = $items["id"];
            foreach ($s_warranty[$__id] as $item){
                $name_warranty = $name_producers[$item["id_warranty"]];
            }
                
                $__datetime = date('d/m/Y',  strtotime($items["datetime"]));
                $__date_outs = date('d/m/Y',  strtotime($items["date_out"]));
                
                $__item_name = $items["item_name"];
                $__imei = trim($items["imei"]);
                $__note = trim($items["note"]);
                $vote_name = $storename[$items["storeid"]];
                if($vote_name ==NULL){
                    $vote_name ='KBH';
                }
                $__money_dvsc = $items["money_dvsc"];
                $__money_hnam = $items["money_hnam"];
                $__money_voucher = $items["money_voucher"];
                $__money = $items["money"];
                $__advance_payment = $items["advance_payment"];
                $tiendathu = $items["money_thu"];
                $__thucte = $items["money"] -$items["money_dvsc"]-$items["money_voucher"];
                $dukienchi = $items["money_dvsc"]-$items["money_hnam"]-$items["money"]-$items["money_voucher"];
                $chenhlech = $items["money"] + $items["money_hnam"] -$items["money_dvsc"];
                $bb=0;
                if($items["check_bbmh"]==1){
                   $bb=$items["money_hnam"]; 
                }
                if($items["check_bbmh_seri"]==1){
                   $bb=$items["money_hnam"]; 
                }
                $__quote=0;
                if($items["quoc_te"]==1){
                    $__quote=$items["money_hnam"];
                }
                $__bhkhac=0;
                if($items["bhkhac"]==1){
                    $__bhkhac=$items["money_hnam"];
                }
                $finalData = array(
                     // For chars with accents.
                    $__id,
                    $__datetime,
                    $__date_outs,
                    $loai,
                    $__item_name,
                    $__imei,
//                    $__note,
                    $vote_name,
                    $name_warranty,
                    $__money_dvsc,
                    $__money_hnam,
                    $__money_voucher,
                    $__money,
//                    $__advance_payment,
                    $bb,
                    $__quote,
                    $chenhlech,
                    $__bhkhac
                );
            $strItem .= implode("\t", $finalData)."\r\n";
            
            
            $tongtienbaokhach +=$items["money"];
            $tongtienhnambaohanh +=$items["money_hnam"];
            $tongtienbaobe +=$bb;
            
            $tongchi +=$__money_dvsc;
            $tongchenhlech +=$chenhlech;
            $tongtienkhac +=$__bhkhac;
            $tongtienquocte +=$__quote;
        }
        
        $total = $tongtienhnambaohanh-$tongchenhlech;
        
        $strItem .="-\t-\t-\t-\t-\t-\t-\t-\t$tongchi\t$tongtienhnambaohanh\t-\t$tongtienbaokhach\t$tongtienbaobe\t$tongtienquocte\t$tongchenhlech\t$tongtienkhac"."\r\n";
        $strItem .="Tien nop ve cho anh quan: $tongtienbaokhach"."\r\n";
        $strItem .="Tien hnam bao hanh: $tongtienhnambaohanh"."\r\n";
        $strItem .="Tien bao be: $tongtienbaobe"."\r\n";
        $strItem .="Tien quoc te: $tongtienquocte"."\r\n";
        $strItem .="Tien khac: $tongtienkhac"."\r\n";
        $strItem .="Tong tien hnam bao hanh trong thang : $total"."\r\n";
        ob_start();
        echo chr(255) . chr(254) . mb_convert_encoding($strItem, 'UTF-16LE', 'UTF-8');
        $content = ob_get_contents();
        ob_end_clean();
        echo $content;
        }
    public function statistics2Action(){
        $_option = Business_Addon_Options::getInstance();
        $d=date('Y-m-d');
        $k=$_option->pre_date($d, 1);
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
        
        $ok = 1; //khách đồng ý
        $___bs = Business_Addon_Guarantee::getInstance();
        $ncc = (int)  $this->_request->getParam("ncc");
        if($ncc==0){
            $list = $___bs->get_list_by_ok($ok,$start,$end);
        }else{
            $l_warranty = Business_Addon_Warranty::getInstance()->get_list_by_id($ncc);
            foreach ($l_warranty as $v1){
                $___strid[] = $v1["id_guarantee"];
            }
            if($___strid != NULL){
                $strid = implode(",", $___strid);
                $list = $___bs->get_list_by_ok($ok,$start,$end,$strid);
            }
        }
        
        foreach ($list as $val){
            $id_guarantee[] = $val["id"];
        }
        $str_guaranteeID = implode(",", $id_guarantee);
        if($str_guaranteeID != NULL){
            $slist = Business_Addon_GuaranteeWarranty::getInstance()->getListByGuarantee($str_guaranteeID);
        }
        
        $s_warranty = array();
        foreach ($slist as $_item){
            $s_warranty[$_item["id_guarantee"]][] = $_item;
            $s_warranty_time[$_item["id_guarantee"]] = $_item["datetime"];
            $s_warranty_dealine[$_item["id_guarantee"]] = $_item["dealine"];
        }
        
        $this->view->list = $list;
        $this->view->s_warranty = $s_warranty;
        
        $_warranty_unit = Business_Addon_WarrantyUnit::getInstance();
        $sproducers = $_warranty_unit->getList();
        $name_producers = array();
        
        foreach ($sproducers as $items){
            $name_producers[$items["id"]] = $items["name"];
            $receipt[$items["id"]] = $items["receipt"];
        }
        $this->view->name_producers = $name_producers;
        $this->view->slist_warranty = $sproducers;
        $list_store = Business_Common_Users::getInstance()->getListByUname(FALSE);
        foreach ($list_store as $store){
            $storename[$store["userid"]] = $store["storename"];
        }
        $this->view->storename = $storename;
        
        $idregency = $this->_identity["idregency"];
        $bgd = 0;
        $storeid = $this->_identity["parentid"];
        if($_option->isBGD($idregency)){
            $bgd = 1;
            $storeid = $this->_request->getParam("storeid");
        }
        $this->view->bgd = $bgd;
        $this->view->idregency = $idregency;
        $this->view->storeid = $storeid;
        
        $store=0;
        if($idregency ==11 || $idregency==14){
            $store = 1;
        }
        $this->view->store = $store;
        if($store ==1){
            $this->_helper->viewRenderer('statistics2-2');
        }
        
        $this->view->list_store = $list_store;
    }
    
    
    public function delAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        $id = (int)  $this->_request->getParam("id");
        $data["status"] =0;
        $_guarantee = Business_Addon_Guarantee::getInstance();
        $_guarantee->update_warranty($id, $data);
    }
    public function savechiAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        $id = (int)  $this->_request->getParam("id");
        $token = $this->_request->getParam("token");
        $ztoken = md5("CHI".$id);
        if($token != $ztoken){
            die('No access');
        }
        $__price = $this->_request->getParam("price");
        $price = str_replace(",", "", $__price);
        $data["advance_payment"] = $price;
        $data["advance_payment_date"] =date('Y-m-d H:i:s');
        $data["ngay_chi"] = date('Y-m-d H:i:s');
        $_guarantee = Business_Addon_Guarantee::getInstance();
        $_guarantee->update($id, $data);
    }
    public function savethuAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        $id = (int)  $this->_request->getParam("id");
        $token = $this->_request->getParam("token");
        $ztoken = md5("THU".$id);
        if($token != $ztoken){
            die('No access');
        }
        $__price = $this->_request->getParam("price");
        $price = str_replace(",", "", $__price);
        $data["money_thu"] = $price;
        $data["ngay_nhan"] = date('Y-m-d H:i:s');
        $_guarantee = Business_Addon_Guarantee::getInstance();
        $_guarantee->update($id, $data);
    }
    public function del2Action(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $id = (int)  $this->_request->getParam("id");
        $_guarantee = Business_Addon_Guarantee::getInstance();
        $idregency = $this->_identity["idregency"];
        if($id >0){
            $data["isdel"] = 1;
            $_guarantee->update($id, $data);
        }
        
    }
    public function activeAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $id = (int)  $this->_request->getParam("id");
        $_guarantee = Business_Addon_Guarantee::getInstance();
        $idregency = $this->_identity["idregency"];
        $detail = $_guarantee->getDetail($id);
        $token = $this->_request->getParam("token");
        $status_where = (int)  $this->_request->getParam("status_where");
        $ztoken = md5($this->skey.$id.$status_where);
        if($token != $ztoken){
            die("No access");
        }
        if($idregency==12){
            die();
        }
        switch ($status_where){
            case 1: // chi nhánh
            {
                if((int)$detail["complete"] >1){
                    $data["confirm"] =1;
                }
                break;
            }
            case 2: // luân chuyển
            {
                if((int)$detail["complete"] ==0){
                    $data["complete"] =1;
                }
                if((int)$detail["complete"] < 2){
                    $data["confirm"] =1;
                }
                break;
            }
            case 3: // KBH
            {
                $data["confirm"] =1;
                break;
            }
        }
        // Gửi mail
        $__zwf_user = Business_Common_Users::getInstance();
        $_option = Business_Addon_Options::getInstance();
        $detail_user = $__zwf_user->getDetailByUserName($detail["creator"]);
        $to = $detail_user["email"];
        if($to == NULL){
            $to ="quynh.nguyen@hnammobile.com";
        }
        $___fullname = $this->_identity["fullname"];
        $subject = "BHSC".$detail["id"];
        $ids = $detail["id"];
        $body_html ="$___fullname Bạn đã xác nhận. Vui lòng xem lại link <a href='http://app.hnammobile.com/admin/user/redirect/billbh?billid=$ids'>Tại đây</a>";
        $displayname = $this->_identity["fullname"];
        $cc="";
        $bcc=array("nghi.dang@hnammobile.com");
//                $bcc = array("nghi.dang@hnammobile.com","quynh.nguyen@hnammobile.com","duyhuy@hnammobile.com");
        $_option->sendMail($to, $subject, $displayname, $body_html, $cc, $bcc);
            
        
        $data["status_where"]     = $status_where;
        $_guarantee->update($id, $data);
    }
    public function transfersAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $_guarantee = Business_Addon_Guarantee::getInstance();
        $token = $this->_request->getParam("token");
        $id = (int)  $this->_request->getParam("id");
        $transfers = (int)  $this->_request->getParam("transfers");
        $ztoken = md5($this->skey.$transfers);
        if($token != $ztoken){
            die("No access");
        }
        $idregency = $this->_identity["idregency"];
        if($this->isStore($idregency)){
           die("No access"); 
        }
        $data["transfers"]     = $transfers;
        
        $_guarantee->update($id, $data);
    }
    public function historyAction(){
        $this->_helper->Layout()->disableLayout();
        $id_guarantee = (int) $this->_request->getParam("id_guarantee");
        $list = Business_Addon_GuaranteeHistory::getInstance()->get_list_by_idguaranree($id_guarantee);
        $this->view->list = $list;
        $istype = Business_Addon_Options::getInstance()->status_history_guarantee();
        $this->view->istype = $istype;
    }

    public function updatesAction(){


        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $uids = (int)  $this->_identity["userid"];
        if($uids ==0){
            die('Ban da bi logout. Vui long login lai he thong');
        }
        $_guarantee = Business_Addon_Guarantee::getInstance();
        $data_frm = $this->_request->getParams('data_frm');
        $_option = Business_Addon_Options::getInstance();
        $idregency = $this->_identity["idregency"];
        $ztoken = md5($this->skey . $data_frm["id"]);
        $detail = $_guarantee->getDetail($data_frm["id"]);
        
        $ret = array();
        $arr = array();
        if ($data_frm["token"] != $ztoken) {
            $err["id"] ="data_frm";
            $err["msg"] = "Bạn không có quyền truy cập";
            $ret[] = $err;
        }
        if($idregency ==26 || $idregency==12){
            if($detail["complete"] >1){
                $err["id"] ="data_frm";
                $err["msg"] = "Bạn đã hoàn tất nên không thể chỉnh sửa";
                $ret[] = $err;
            }
            if($uids !=$detail["receiver_id"]){
                $err["id"] ="note_warranty";
                $err["msg"] = "Bạn không phải là người nhận phiếu nên không thể chỉnh sửa";
                $ret[] = $err;
            }
        }
        
        if(count($ret) >0){
            for($i=0;$i<count($ret);$i++){
                $msg = $ret[$i]['msg'];
                $ids = $ret[$i]['id'];
                echo "<script>window.parent.show_msg('$msg','$ids');</script>";
                return;
            }
        }else{
            $check_history =0;
            if($this->isStore($idregency)){ // quản lý
                $_data = $this->get_data_store($data_frm,$detail);
                $data = $_data["data"][0];
                $check_history = (int)$_data["check_history"][0];
            }
            if($idregency ==26 || $idregency==12 || $idregency==33){ // nhân viên
                
                $_data = $this->get_data($data_frm,$detail);
                $data = $_data["data"][0];
                $check_history = (int)$_data["check_history"][0];
            }
            if($_option->isBGD($idregency) || $this->isBHVT($idregency)){ // trưởng KBH
                $_data = $this->get_data_admin($data_frm,$detail);
                $data = $_data["data"][0];
                $check_history = (int)$_data["check_history"][0];
            }
 
            $_guarantee->update($data_frm["id"], $data); 
            
            if($check_history ==1){
                $_guarantee->insert_history($detail); 
            }
            if($_option->isBGD($idregency) || $this->isBHVT($idregency)){
                $_sdata = $this->data_guarantee($data_frm);
                Business_Addon_Guarantee::getInstance()->update($data_frm["id"],$_sdata);
                
                $money_dvsc = str_replace(",", "", $data_frm["money_dvsc"]);
                $money_hnam = str_replace(",", "", $data_frm["money_hnam"]);
                $money = $money_dvsc;
                if($money_dvsc==$money_hnam){
                    $money=0;
                }
                if((int)$detail["complete"]>0){
                    $datass["money_dvsc"] = $money_dvsc;
                    $datass["money_hnam"] = $money_hnam;
                    $datass["money"] = $money;
                }
                
                $datass["datetime_update"] = date('Y-m-d H:i:s');
                $datass["userid_update"] = $this->_identity["userid"];
                Business_Addon_GuaranteeBhsc::getInstance()->update2($data_frm["id"], $datass);
            }
            echo "<script>window.parent.completes('LƯU THÀNH CÔNG');</script>";
            return;
        }
    }
    public function get_data_admin($data_frm,$detail){
        $idregency = $this->_identity["idregency"];
        $_option = Business_Addon_Options::getInstance();
        $__zwf_user = Business_Common_Users::getInstance();
        $data["status_guarantee"] = (int)$data_frm["status_guarantee"];
        $data["bhkhac"] = $data_frm["bhkhac"];
        $ids =$detail["id"];
        $___fullname = $this->_identity["fullname"];
        if((int)$detail["receiver_id"]==0){
            if((int)$data_frm["receiver_id"] >0){
                $data["receiver_id"]=   $data_frm["receiver_id"]; 
                $data["receiver"]   =   $data_frm["receiver"];
                $data["confirm"] = 0;
                $data["status_where"] = 2;
                $data["isdel"] = 1;
                $this->history_date($__type=1, $id_guarantee=$detail["id"]); // Ngày giao cho nhân viên
                
                $detail_user = $__zwf_user->getDetail($data_frm["receiver_id"]);
                $to = $detail_user["email"];
                if($to == NULL){
                    $to ="quynh.nguyen@hnammobile.com";
                }
                $subject = "BHSC".$detail["id"];
                
                $body_html ="$___fullname đồng ý xác nhận là đã nhận sản phẩm. Vui lòng nhấn xác nhận vào link <a href='http://app.hnammobile.com/admin/user/redirect/billbh?billid=$ids'>Tại đây</a>";
                $displayname = $this->_identity["fullname"];
                $cc="";
                $bcc=array("nghi.dang@hnammobile.com");
//                $bcc = array("nghi.dang@hnammobile.com","quynh.nguyen@hnammobile.com","duyhuy@hnammobile.com");
                $_option->sendMail($to, $subject, $displayname, $body_html, $cc, $bcc);
            }
        }else{
            if((int)$data_frm["receiver_id"] != 0 && (int)$detail["receiver_id"] != (int)$data_frm["receiver_id"]){
                $data["receiver_id"]=   $data_frm["receiver_id"];
                $data["receiver"]   =   $data_frm["receiver"];
                $data["confirm"] = 0;
                $data["status_where"] = 2;
                $data["complete"] = 0;
                
                
                $detail_user = $__zwf_user->getDetail($data_frm["receiver_id"]);
                $to = $detail_user["email"];
                if($to == NULL){
                    $to ="quynh.nguyen@hnammobile.com";
                }
                $subject = "BHSC".$detail["id"];
                
                $body_html ="Đồng ý xác nhận là đã nhận sản phẩm. Vui lòng nhấn xác nhận vào link <a href='http://app.hnammobile.com/admin/user/redirect/billbh?billid=$ids'>Tại đây</a>";
                $displayname = $this->_identity["fullname"];
                $cc="";
                $bcc=array("nghi.dang@hnammobile.com");;
//                $bcc = array("nghi.dang@hnammobile.com","quynh.nguyen@hnammobile.com","duyhuy@hnammobile.com");
                $_option->sendMail($to, $subject, $displayname, $body_html, $cc, $bcc);
            }
        }
        
        $data["is_view"] = (int)$data_frm["is_view"];
        $data["quoc_te"] = (int)$data_frm["quoc_te"];
        $data["ncc_ok"] = (int)$data_frm["ncc_ok"];
        $data["storeid_in"] = $data_frm["storeid_in"];
        $ck=0;
        
        $money = str_replace(",", "", $data_frm["money"]);
        $money_dvsc = str_replace(",", "", $data_frm["money_dvsc"]);
        $money_hnam = str_replace(",", "", $data_frm["money_hnam"]);
        if((int)$detail["complete"]>0){
            $data["money"] = $money;
            $data["money_dvsc"] = $money_dvsc;
            $data["money_hnam"] = $money_hnam;
        }
        
        if($money_dvsc >0 && $money_dvsc != $detail["money_dvsc"]){
            $this->history_date($__type=5, $id_guarantee=$detail["id"]); // KBH nhập phí
        }
        $price_tmp = str_replace(",", "", $data_frm["price_tmp"]);
        $data["price_tmp"] = (int)$price_tmp;
        $advance_payment = str_replace(",", "", $data_frm["advance_payment"]);
        $data["advance_payment"] = (int)$advance_payment;
        if((int)$detail["advance_payment"] ==0 && (int)$advance_payment >0){
            $data["advance_payment_date"] = date('Y-m-d H:i:s');
        }
        
        
        
        $check_history = 0;
        if($detail["money"] != (int)$money){
            $check_history =1;
        }
        if($detail["money_dvsc"] != (int)$money_dvsc){
            $check_history =1;
        }
        if($detail["money_hnam"] != (int)$money_hnam){
            $check_history =1;
        }
        if($detail["note"] != $data_frm["note"]){
            $check_history =1;
        }
        $data["note"] = $data_frm["note"];
        $data["editor"] = $this->_identity["username"];
        $data["datetime_editor"] = date('Y-m-d H:i:s');
        
        $money_voucher = str_replace(",", "", $data_frm["money_voucher"]);
        $data["money_voucher"] = (int)$money_voucher;
        $voucher = trim($data_frm["data_voucher"]);
        $data["voucher"] = $data_frm["data_voucher"];
        if($voucher != NULL){
            $this->update_voucher($voucher);
        }
        
        if($detail["confirm"] ==1 && $detail["complete"] >1){
            
            if($detail["iddepartment"]==0){
                if((int)$data_frm["iddepartment"]>0){
                    $data["iddepartment"] = $data_frm["iddepartment"];
                }
                if((int)$data_frm["storeid_out"]>0){
                    if((int)$data_frm["iddepartment"] ==10){ // phòng ban chi nhánh
                      $data["storeid_out"] = $data_frm["storeid_out"];  
                    }
                }
                if((int)$data_frm["iddepartment"]>0 || (int)$data_frm["storeid_out"]>0){
                    $this->history_date($__type=4, $id_guarantee=$detail["id"]); // Xuất
                    $data["date_out"] = date('Y-m-d H:i:s');
                }
            }
            
        }
       
        $sdata["data"][] = $data;
        $sdata["check_history"][] = $check_history;
        return $sdata;
    }
    public function get_data_store($data_frm,$detail){
        $idregency = $this->_identity["idregency"];
        $_option = Business_Addon_Options::getInstance();
        $__zwf_user = Business_Common_Users::getInstance();
        if((int)$detail["receiver_id"]==0){
            if((int)$data_frm["receiver_id"] >0){
                $data["receiver_id"]=   $data_frm["receiver_id"];
                $data["receiver"]   =   $data_frm["receiver"];
                $data["confirm"] = 0;
                $data["status_where"] = 2;
                $data["isdel"] =1;
                $this->history_date($__type=1, $id_guarantee=$detail["id"]); // Ngày giao cho nhân viên
                
                $detail_user = $__zwf_user->getDetail($data_frm["receiver_id"]);
                $to = $detail_user["email"];
                if($to == NULL){
                    $to ="quynh.nguyen@hnammobile.com";
                }
                $___fullname = $this->_identity["fullname"];
                $ids=$detail["id"];
                $subject = "BHSC".$detail["id"];
                $body_html ="$___fullname Đồng ý xác nhận là đã nhận sản phẩm. Vui lòng nhấn xác nhận vào link <a href='http://app.hnammobile.com/admin/user/redirect/billbh?billid=$ids'>Tại đây</a>";
                $displayname = $this->_identity["fullname"];
                $cc="";
                $bcc=array("nghi.dang@hnammobile.com");;
//                $bcc = array("nghi.dang@hnammobile.com","quynh.nguyen@hnammobile.com","duyhuy@hnammobile.com");
                $_option->sendMail($to, $subject, $displayname, $body_html, $cc, $bcc);
            }
        }
        $ck=0;
        $check_history = 0;
        $data["note"] = $data_frm["note"];
        $data["editor"] = $this->_identity["username"];
        $data["datetime_editor"] = date('Y-m-d H:i:s');
        
        
        if($detail["confirm"] ==1 && $detail["complete"] >1){
            if($detail["iddepartment"]==0){
                if((int)$data_frm["iddepartment"]>0){
                    $data["iddepartment"] = $data_frm["iddepartment"];
                }
                if((int)$data_frm["storeid_out"]>0){
                    if((int)$data_frm["iddepartment"] ==10){ // phòng ban chi nhánh
                      $data["storeid_out"] = $data_frm["storeid_out"];  
                    }
                }
                if((int)$data_frm["iddepartment"]>0 || (int)$data_frm["storeid_out"]>0){
                    $this->history_date($__type=4, $id_guarantee=$detail["id"]); // Xuất
                    $data["date_out"] = date('Y-m-d H:i:s');
                }
            }
        }
        $sdata["data"][] = $data;
        $sdata["check_history"][] = $check_history;
        return $sdata;
    }
    public function get_data($data_frm,$detail){
        $check_history = 0;
        $__zwf_user = Business_Common_Users::getInstance();
        $_option = Business_Addon_Options::getInstance();
        $_guarantee = Business_Addon_Guarantee::getInstance();
        $detail_warranty = $_guarantee->getDetailByWarranty($data_frm["id"]);
        $data_warranty["note"] = $data_frm["note_warranty"];
        $data_warranty["receipt"] = $data_frm["receipt"];
//        $data_warranty["dealine"] = date('Y-m-d',  strtotime($data_frm["dealine"]));
        
        $dealine = str_replace("/", "-", $data_frm["dealine"]) ;
        $__dealine       =   date('Y-m-d',  strtotime($dealine));
        
        $data_warranty["dealine"] = $__dealine;
        if($data_frm["producers"] >0){
            $data_warranty["id_guarantee"] = $data_frm["id"];
            $data_warranty["id_warranty"] = $data_frm["producers"];
            $data_warranty["creator"] = $this->_identity["username"];
            $data_warranty["status"] = 1;
            $data_warranty["enabled"] = 1;
            $data_warranty["complete"] = $data_frm["complete"];
            $data_warranty["datetime"] = date('Y-m-d H:i:s');
            $this->history_date($__type=2, $id_guarantee=$detail["id"]); // Nhân viên lên hãng
            if((int)$this->_identity["userid"]>0){
                $_guarantee->insert_warranty($data_warranty);
            }
        }else{
            if(strtotime($__dealine)>0){
                $data_warranty["complete"] = $data_frm["complete"];
                $data_warranty["end_creator"] = $this->_identity["username"];
                $data_warranty["end_datetime"] = date('Y-m-d H:i:s');
                $this->history_date($__type=2, $id_guarantee=$detail["id"]); // Nhân viên lên hãng
                Business_Addon_GuaranteeWarranty::getInstance()->update($detail_warranty["id"], $data_warranty);
            }
        }

    if($detail["complete"] != $data_frm["complete"]){
           $check_history =1;
        }
        $data["complete"] = $data_frm["complete"];
        $data["confirm_name"]    = $this->_identity["username"];
        $data["confirm"]    = 1;
        if($data_frm["complete"] > 1){
           if((int)$detail["complete"] <2){
                $data["confirm"]    = 0; 
                if((int)$detail["storeid"] > 0){
                    $data["status_where"] = 1; // trả về chi nhánh xác nhận
                }else{
                    $data["status_where"] = 3; // trả về kho bảo hành xác nhận
                }
                $this->history_date($__type=3, $id_guarantee=$detail["id"]);
                // Gửi mail
                 $detail_user = $__zwf_user->getDetailByUserName($detail["creator"]);
                 $to = $detail_user["email"];
                 if($to == NULL){
                     $to ="quynh.nguyen@hnammobile.com";
                 }
                 $___fullname = $this->_identity["fullname"];
                $ids = $detail["id"];
                 $subject ="BHSC".$detail["id"];
                 $body_html ="$___fullname Đồng ý xác nhận hoàn tất quá trình sửa chửa và giao lại máy. Vui lòng nhấn xác nhận vào link <a href='http://app.hnammobile.com/admin/user/redirect/billbh?billid=$ids'>Tại đây</a>";
                 $displayname = $this->_identity["fullname"];
                 $cc="";
                 $bcc=array("nghi.dang@hnammobile.com");;
     //                $bcc = array("nghi.dang@hnammobile.com","quynh.nguyen@hnammobile.com","duyhuy@hnammobile.com");

                 $_option->sendMail($to, $subject, $displayname, $body_html, $cc, $bcc);
            }
        }
        if($detail["note"] != $data_frm["note"]){
            $check_history =1;
        }
        if($data_frm["note"] != NULL){
            $data["note"] = $data_frm["note"];
        }
        
        $data["editor"] = $this->_identity["username"];
        $data["datetime_editor"] = date('Y-m-d H:i:s');
        $sdata["data"][] = $data;
        $sdata["check_history"][] = $check_history;
        return $sdata;
    }
    public function isBHVT($idregency){
        if($idregency ==48){
            return true;
        }
        return FALSE;
    }
    public function isStore($idregency){
        if($idregency ==11 || $idregency ==14 || $idregency ==20){
            return true;
        }
        return FALSE;
    }

}
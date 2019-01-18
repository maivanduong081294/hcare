<?php

/**
 * User Admin Home Controller
 * @author: nghidv
 */
class User_Admin_ReportfController extends Zend_Controller_Action {
    private $_identity;
    public function init() {
        // do something
        BlockManager::setLayout('appbh');
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
    
                

    public function getBillByProductAction(){
        $_option                    = Business_Addon_Options::getInstance();
        $__bs = Business_Addon_UsersProducts::getInstance();
        $start_end           = $this->_request->getParam("start_end");
        if($start_end ==null){
           $start_end = date("F j, Y")." - ".date("F j, Y"); 
        }
        $this->view->start_end = $start_end;
        $start  = $_option->getStartDate($start_end);
        $end  = $_option->getEndDate($start_end);
        $this->view->start = $start;
        $this->view->end = $end;
        $products_id = (int)  $this->_request->getParam("products_id");
        $storeid = (int)  $this->_request->getParam("storeid");
        $this->view->storeid = $storeid;
        $this->view->products_id = $products_id;
        if($products_id>0 && $storeid >0){
            $list = $__bs->get_list_by_products_storeid_date($products_id,$storeid,$start,$end);
        }
        $this->view->list = $list;
        $list_store = Business_Common_Users::getInstance()->getListByUname(FALSE);
        foreach ($list_store as $store){
            $storename[$store["userid"]] = $store["storename"];
        }
        $this->view->storename = $storename;
        $this->view->list_store = $list_store;
        
        $name_color = array();
        $list_color = Business_Ws_NewsItem::getInstance()->getListByCateId(722);
        foreach ($list_color as $val){
            $name_color[$val["itemid"]] = $val["title"];
        }
        $this->view->name_color = $name_color;
    }

    public function salesByProductsByColorAction(){
        $_option                    = Business_Addon_Options::getInstance();
        $start_end           = $this->_request->getParam("start_end");
        if($start_end ==null){
           $start_end = date("F j, Y")." - ".date("F j, Y"); 
        }
        $this->view->start_end = $start_end;
        $start  = $_option->getStartDate($start_end);
        $end  = $_option->getEndDate($start_end);
        $this->view->start = $start;
        $this->view->end = $end;
        $_users_products  = Business_Addon_UsersProducts::getInstance();
        $products_id = (int)  $this->_request->getParam("products_id");
        $detail = Business_Ws_ProductsItem::getInstance()->get_detail_by_itemid($products_id);
        $this->view->detail = $detail;
        $this->view->products_id = $products_id;
        $list = $_users_products->get_list_products_color($products_id,$start,$end);
        $total_color = array();
        $count_color = array();
        foreach ($list as $val){
            $color[] = $val["colorid"];
            $total_color[$val["vote_id"]][$val["colorid"]] = $val["sum"];
            $count_color[$val["vote_id"]][$val["colorid"]] = $val["total"];
        }
        if($color != NULL){
            $color = array_unique($color);
            $this->view->color = $color;
        }
        
        $this->view->total_color = $total_color;
        $this->view->count_color = $count_color;
//        $_product_color             = Business_Addon_ProductsColor::getInstance();
//        $list_color = $_product_color->get_list_by_id($products_id);
        
        $name_color = array();
        $list_color = Business_Ws_NewsItem::getInstance()->getListByCateId(722);
        foreach ($list_color as $val){
            $name_color[$val["itemid"]] = $val["title"];
        }
        $this->view->name_color = $name_color;
        
        $this->view->list = $list;
        
       
        $storename = array();
        $_zwf_user = Business_Common_Users::getInstance();
        $list_vote                  = $_zwf_user->getListByUname(false);
        foreach ($list_vote as $__item){
            $storename[$__item["userid"]] = $__item["abbreviation"];
        }
        $this->view->list_vote = $list_vote;
        $this->view->storename = $storename;
    }

    public function voucherAction(){
        $_option                    = Business_Addon_Options::getInstance();
        $start_end           = $this->_request->getParam("start_end");
        if($start_end ==null){
           $start_end = date("F j, Y")." - ".date("F j, Y"); 
        }
        $this->view->start_end = $start_end;
        $start  = $_option->getStartDate($start_end);
        $end  = $_option->getEndDate($start_end);
        $this->view->start = $start;
        $this->view->end = $end;
        $_users_products  = Business_Addon_UsersProducts::getInstance();
        
        $sectionid_voucher = (int)$this->_request->getParam("sectionid_voucher");
        $this->view->sectionid_voucher = $sectionid_voucher;
        $cated_voucher = (int)$this->_request->getParam("cated_voucher");
        if($cated_voucher ==0){
            $cated_voucher =2;
        }
        $this->view->cated_voucher = $cated_voucher;
        $storeid = (int)$this->_request->getParam("storeid");
        $list = $_users_products->report_voucher($cated_voucher, $sectionid_voucher, $start, $end,$storeid);
        $this->view->list = $list;
        
        $__list_cate_voucher = $_option->getCateVoucher();
        $this->view->list_cated_voucher = $__list_cate_voucher;
        
        $_voucher = Business_Ws_VoucherAdd::getInstance();
        $list_sectionid_voucher = $_voucher->getList();
        
        $name_sectionid = array();
        foreach ($list_sectionid_voucher as $items){
            $name_sectionid[$items["id"]] = $items["note"];
        }
        $this->view->name_sectionid = $name_sectionid;
        $this->view->list_sectionid_voucher = $list_sectionid_voucher;
        
        $list_vote = Business_Common_Users::getInstance()->getListByUname(FALSE);
        $this->view->list_vote = $list_vote;
        
        $this->view->cate_hnam = $_option->getCatedHnam();
    }

    public function sales2Action(){
        $u_product                  = Business_Addon_UsersProducts::getInstance();
        $_zwf_user                  = Business_Common_Users::getInstance();
        $list_store = $_zwf_user->getListByUname(FALSE);
        $this->view->list_store = $list_store;
        $_option                    = Business_Addon_Options::getInstance();
        $start_end           = $this->_request->getParam("start_end");
        if($start_end ==null){
           $start_end = date("F j, Y")." - ".date("F j, Y"); 
        }
        $this->view->start_end = $start_end;
        $start  = $_option->getStartDate($start_end);
        $end  = $_option->getEndDate($start_end);
        $this->view->start = $start;
        $this->view->end = $end;
        $productsid = "3,5,6,8";
        $storeid = $this->_request->getParam("storeid");
        $this->view->storeid = $storeid;
        $list                    = $u_product->getListSalesAll($start, $end,$productsid,$storeid);
        $sum = array();
        $total = array();
        $sum_hnam = array();
        $total_hnam = array();
        
        foreach ($list as $items){
            $is_apple =  $items["is_apple"];
            $flag =  $items["flag"];
            $___productsid =  $items["productsid"];
            if($flag==1){// công ty
                if($is_apple==0){
                    switch ($___productsid) {
                        case 3: // điện thoại
                        {
                            $sum[$is_apple][$___productsid] +=  $items["sum"];
                            $total[$is_apple][$___productsid] +=  $items["total"];
                            break;
                        }
                            
                        case 5: //máy tính bảng
                        {
                            $sum[$is_apple][$___productsid] +=  $items["sum"];
                            $total[$is_apple][$___productsid] +=  $items["total"];
                            break;
                        }
                        case 6: // laptop
                        {
                            $sum[$is_apple][$___productsid] +=  $items["sum"];
                            $total[$is_apple][$___productsid] +=  $items["total"];
                            break;
                        }
                        case 8: // đồng hồ thông minh
                        {
                            $sum[$is_apple][$___productsid] +=  $items["sum"];
                            $total[$is_apple][$___productsid] +=  $items["total"];
                            break;
                        }
                    }
                }else{
                    switch ($___productsid) {
                        case 3: // điện thoại
                        {
                            $sum[$is_apple][$___productsid] +=  $items["sum"];
                            $total[$is_apple][$___productsid] +=  $items["total"];
                            break;
                        }
                            
                        case 5: //máy tính bảng
                        {
                            $sum[$is_apple][$___productsid] +=  $items["sum"];
                            $total[$is_apple][$___productsid] +=  $items["total"];
                            break;
                        }
                        case 6: // laptop
                        {
                            $sum[$is_apple][$___productsid] +=  $items["sum"];
                            $total[$is_apple][$___productsid] +=  $items["total"];
                            break;
                        }
                        case 8: // đồng hồ thông minh
                        {
                            $sum[$is_apple][$___productsid] +=  $items["sum"];
                            $total[$is_apple][$___productsid] +=  $items["total"];
                            break;
                        }
                    }
                }
            }else{ // hnam
                if($is_apple==0){
                    switch ($___productsid) {
                        case 3: // điện thoại
                        {
                            $sum_hnam[$is_apple][$___productsid] +=  $items["sum"];
                            $total_hnam[$is_apple][$___productsid] +=  $items["total"];
                            break;
                        }
                            
                        case 5: //máy tính bảng
                        {
                            $sum_hnam[$is_apple][$___productsid] +=  $items["sum"];
                            $total_hnam[$is_apple][$___productsid] +=  $items["total"];
                            break;
                        }
                        case 6: // laptop
                        {
                            $sum_hnam[$is_apple][$___productsid] +=  $items["sum"];
                            $total_hnam[$is_apple][$___productsid] +=  $items["total"];
                            break;
                        }
                        case 8: // đồng hồ thông minh
                        {
                            $sum_hnam[$is_apple][$___productsid] +=  $items["sum"];
                            $total_hnam[$is_apple][$___productsid] +=  $items["total"];
                            break;
                        }
                    }
                }else{
                    switch ($___productsid) {
                        case 3: // điện thoại
                        {
                            $sum_hnam[$is_apple][$___productsid] +=  $items["sum"];
                            $total_hnam[$is_apple][$___productsid] +=  $items["total"];
                            break;
                        }
                            
                        case 5: //máy tính bảng
                        {
                            $sum_hnam[$is_apple][$___productsid] +=  $items["sum"];
                            $total_hnam[$is_apple][$___productsid] +=  $items["total"];
                            break;
                        }
                        case 6: // laptop
                        {
                            $sum_hnam[$is_apple][$___productsid] +=  $items["sum"];
                            $total_hnam[$is_apple][$___productsid] +=  $items["total"];
                            break;
                        }
                        case 8: // đồng hồ thông minh
                        {
                            $sum_hnam[$is_apple][$___productsid] +=  $items["sum"];
                            $total_hnam[$is_apple][$___productsid] +=  $items["total"];
                            break;
                        }
                    }
                }
            }
        }
        // công ty
        $this->view->sum = $sum;
        $this->view->total = $total;
        // hnam
        $this->view->sum_hnam = $sum_hnam;
        $this->view->total_hnam = $total_hnam;
    }

    public function exportBillDelegateAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $_bills = Business_Addon_Bills::getInstance();
        $_delegates = Business_Addon_Delegate::getInstance();
        $_receipts = Business_Addon_Receipts::getInstance();
        $month = $this->_request->getParam("month");
        $year = $this->_request->getParam("year");
        $ac = (int) $this->_request->getParam("ac");
        $id_departments = (int)$this->_request->getParam("departments");
        $id_storeid = (int)$this->_request->getParam("storeids");
        
        if($ac ==1){
          $list = $_bills->getList($id_departments,$id_storeid,$month, $year,1);  
        }
        if($ac ==2){
          $list = $_receipts->getList($id_departments,$id_storeid,$month, $year,1);  
        }
        if($ac ==3){
           $list = $_delegates->getList($id_departments,$id_storeid,$month, $year);  
        }
        Business_Common_Utils::getExcelBIllDelegate($list, $ac, $month, $year);
        
    }

    public function overviewChartAction(){
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
            $this->_helper->viewRenderer('overview-chart2');
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
        $list                   = $_users_products->getListTargetByStoreid($start,$end);
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
                $phone_cty[$v_id] += $item["sum"];
                $cphone_cty[$v_id] += $item["total"];
            }
            if($flag ==2 && $productsid ==3 && $type == 3){
                $phone_hnam[$v_id] += $item["sum"];
                $cphone_hnam[$v_id] += $item["total"];
            }
            if($productsid ==3 && $type == 4){
                $phone_old[$v_id] += $item["sum"];
                $cphone_old[$v_id] += $item["total"];
            }
            //tablet
            if($flag ==1 && $productsid ==5 && $type == 3){
                $tablet_cty[$v_id] += $item["sum"];
                $ctablet_cty[$v_id] += $item["total"];
            }
            if($flag ==2 && $productsid ==5 && $type == 3){
                $tablet_hnam[$v_id] += $item["sum"];
                $ctablet_hnam[$v_id] += $item["total"];
            }
            if($productsid ==5 && $type == 4){
                $tablet_old[$v_id] += $item["sum"];
                $ctablet_old[$v_id] += $item["total"];
            }
            // phụ kiện
            if($flag ==1 && $productsid ==4 && $type == 3){
                $acc_cty[$v_id] += $item["sum"];
                $cacc_cty[$v_id] += $item["total"];
            }
            if($flag ==2 && $productsid ==4 && $type == 3){
                $acc_hnam[$v_id] += $item["sum"];
                $cacc_hnam[$v_id] += $item["total"];
            }
            // Laptop
            if($flag ==1 && $productsid ==6 && $type == 3){
                $laptop_cty[$v_id] += $item["sum"];
                $claptop_cty[$v_id] += $item["total"];
            }
            if($flag ==2 && $productsid ==6 && $type == 3){
                $laptop_hnam[$v_id] += $item["sum"];
                $claptop_hnam[$v_id] += $item["total"];
            }
            // Đồng hồ thông minh
            if($flag ==1 && $productsid ==8 && $type == 3){
                $watch_cty[$v_id] += $item["sum"];
                $cwatch_cty[$v_id] += $item["total"];
            }
            if($flag ==2 && $productsid ==8 && $type == 3){
                $watch_hnam[$v_id] += $item["sum"];
                $cwatch_hnam[$v_id] += $item["total"];
            }
            // all
            $total[$flag][$productsid][$type][$is_apple] += $item["sum"]/1000;
            $count[$flag][$productsid][$type][$is_apple] += $item["total"];
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
        $_ptype      = $_option->getCateTargetHnamNew();
        $this->view->ptype = $_ptype;
        foreach ($list as $items){
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
        
        
    }

    public function exportAction(){
        $_option = Business_Addon_Options::getInstance();
        $start_end           = $this->_request->getParam("start_end");
        if($start_end ==null){
           $start_end = date("F j, Y")." - ".date("F j, Y"); 
        }
        $start  = $_option->getStartDate($start_end);
        $end  = $_option->getEndDate($start_end);
        $this->view->start_end = $start_end;
        $this->view->end = $end;
        $this->view->start = $start;
        $idregency = $this->_identity["idregency"];
        $dplay1 =0;
        $dplay2 =0;
        $dplay3 =0;
        if($_option->isBGD($idregency) || $idregency == 47){
            $dplay1 =1;
            $dplay2 =1;
            $dplay3 =1;  
        }else{
            if($this->_identity["username"] == "hnam_mainhp"){
                $dplay3 =1;  
            }
        }
        $this->view->dplay1 = $dplay1;
        $this->view->dplay2 = $dplay2;
        $this->view->dplay3 = $dplay3;
    }
        public function exportInstallmentAction(){
            $_option = Business_Addon_Options::getInstance();
            $_users_products = Business_Addon_UsersProducts::getInstance();
            $this->_helper->Layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true); 
            $zwfuser = Business_Common_Users::getInstance();
            $lstorename = $zwfuser->getListByUname(FALSE);
            $ltg = $_option->getTraGop2();
            $storename = array();
            foreach ($lstorename as $items){
                $storename[$items["userid"]] = $items["storename"];
            }
            $start_end           = $this->_request->getParam("start_end");
            if($start_end ==null){
               $start_end = date("F j, Y")." - ".date("F j, Y"); 
            }
            $this->view->start_end = $start_end;
            $start  = $_option->getStartDate($start_end);
            $end  = $_option->getEndDate($start_end);
            $list = $_users_products->getListByInstallment($start, $end);
        
        $day = date('Ymd');
        header("Content-Disposition: attachment; filename='Tragop$day.csv'");
        header("Content-Type: text/csv; charset=utf-8");
        header('Content-Type: charset=utf-8');
        
        $finalData= array();
        $strItem ="Ngày\tLoại\tMáy\tCtyHnam\tMàu\tGiá\tSố hợp đồng\tCN\tTrả trước\tCòn lại-Nợ đối tác\tMã bill"."\r\n";            
        foreach ($list as $items){
            $loai ='';
            if($items["flag"] ==1){
               $loai ='Công ty'; 
            }
            if($items["flag"] ==2){
               $loai ='Hnam'; 
            }
//            if ($items["productsid"] != 4) {
                $vote_id    = $items["vote_id"];
                $vote_name = $storename[$vote_id];
                $items["create_date"] = date('d/m/Y',  strtotime($items["create_date"]));
            
                $finalData = array(
                     // For chars with accents.
                    $items["create_date"],
                    $ltg[$items["cated_prepaid_installment"]],
                    $items["products_name"]."( $loai )",
                    $loai,
                    $items["product_color"],
                    $items["total_price"],
                    $items["contract"],
                    $vote_name,
                    $items["money_installment"],
                    ($items["total_price"]-$items["money_installment"]), // For chars with accents.
                    $items["id_addon_user"],
                );
            
            $strItem .= implode("\t", $finalData)."\r\n";
//            }
            $total4 +=$items["total_price"];
            $total5 +=$items["money_installment"];
            $total6 +=$items["total_price"]-$items["money_installment"];
        }
        $total4 = $total4;
        $total5 = $total5;
        $total6 = $total6;
        $strItem .="\t\t\t\tTổng\t$total4\t\t\t$total5\t$total6"."\r\n"; 
        
        ob_start();
        echo chr(255) . chr(254) . mb_convert_encoding($strItem, 'UTF-16LE', 'UTF-8');
        $content = ob_get_contents();
        ob_end_clean();
        echo $content;
        }

        public function exportChargeCardAction(){
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
        
        
        $list = $_users_products->getListByTotalChargecard($start, $end);
        
        $day = "_".date('Ymd');
        header("Content-Disposition: attachment; filename=Chargecard_$day.csv");
        header("Content-Type: text/csv; charset=utf-8");
        header('Content-Type: charset=utf-8');
        
        $finalData= array();
        $strItem ="Ngày\tChi nhánh\tMã Bill\tTổng tiền\tTổng tiền charge trên bill\tTiền còn lại\tLoại máy pos\tLoại phí(Tiền mặt/Thẻ)\tPhí"."\r\n";            
        foreach ($list as &$items){
            $type_card='';
            $status_card='';
            $phitienmat0 =0;
            if ($items["status_chargecard"] == 1) {
                $status_card = 'Vào thẻ';
            }
            if ($items["status_chargecard"] == 2) {
                $status_card = 'Tiền mặt';
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
        }
        $strItem .="\t\tTổng\t$total4\t$total5\t$total6"."\r\n"; 
        
        ob_start();
        echo chr(255) . chr(254) . mb_convert_encoding($strItem, 'UTF-16LE', 'UTF-8');
        $content = ob_get_contents();
        ob_end_clean();
        echo $content;
        $day = "_".date('Ymd');
        header("Content-Disposition: attachment; filename=Chargecard_$day.csv");
        header("Content-Type: text/csv; charset=utf-8");
        header('Content-Type: charset=utf-8');
        $strItem2="--------------------------Chi tiết------------------------\r\n";
        $finalData2= array();
        $strItem2 .="Ngày\tChi nhánh\tMã Bill\tTên sản phẩm\tCtyHnam\tMàu\tTổng tiền\tTổng tiền charge trên bill\tTiền còn lại\tLoại máy pos\tLoại phí(Tiền mặt/Thẻ)\tPhí"."\r\n"; 
        $list2 = $_users_products->getListByChargecard($start, $end);
        foreach ($list2 as $items){
            $loai ='';
            if($items["flag"] ==1){
               $loai ='Công ty'; 
            }
            if($items["flag"] ==2){
               $loai ='Hnam'; 
            }
            $type_card='';
            $status_card='';
            $phitienmat =0;
            if ($items["status_chargecard"] == 1) {
                $status_card = 'Vào thẻ';
            }
            if ($items["status_chargecard"] == 2) {
                $status_card = 'Tiền mặt';
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
            $finalData2 = array(
                 // For chars with accents.
                $items["create_date"],
                $vote_name,
                $items["id_addon_user"],
                $items["products_name"]."( $loai )",
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
        }
        $strItem2 .="\t\t\t\t\tTổng\t$total1\t$total2\t$total3"."\r\n"; 
        ob_start();
        echo chr(255) . chr(254) . mb_convert_encoding($strItem2, 'UTF-16LE', 'UTF-8');
        $content2 = ob_get_contents();
        ob_end_clean();
        echo $content2;
    }
        public function exportDetailChargeCardAction(){
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
    public function exportSalesAction(){
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
        $storeid        = $this->_request->getParam("storeid");
        $cated_name        = $this->_request->getParam("cated_name");
        $flag = (int)$this->_request->getParam("flag");
        if($idregency ==26 || $idregency ==34){
            $flag ="";
        }
        if($idregency ==33){
           $flag =1; 
        }
        $mb_name = array();
        $list_mb = Business_Common_Users::getInstance()->getMb();
        foreach ($list_mb as $_items){
            $mb_name[$_items["userid"]] = $_items["fullname"];
        }
        
        $start_end           = $this->_request->getParam("start_end");
        if($start_end ==null){
           $start_end = date("F j, Y")." - ".date("F j, Y"); 
        }
        $this->view->start_end = $start_end;
        $start  = $_option->getStartDate($start_end);
        $end  = $_option->getEndDate($start_end);
        
        
        
        
        
        
        
        
        $list = $_users_products->get_list_by_cateid4($cated_id,$start, $end,$storeid, $flag);
        $_colorid = array();
//        $list_color = Business_Ws_NewsItem::getInstance()->getListByCateId(722);
        $list_color = Business_Ws_ProductsItem::getInstance()->getListByCateId2(722);
        foreach ($list_color as $items){
            $_colorid[$items["title"]] = $items["itemid"];
        }
        $ctkm = array();
        if($list !=null){
                foreach ($list as &$items1){
                    $id_addon_user[]    = $items1["id_addon_user"];
                    $items1["colorid"] = $_colorid[$items1["product_color"]];
                    $_products_id[] = $items1["products_id"];
                    
                    $_str_id[] = $items1["products_id"];
                    $_array_idaddonuser[] = $items1["id_addon_user"];
                 }
                 $__pids = array_unique($_products_id);
                 $__pid = implode($__pids, ",");
                 $_scolor = Business_Addon_AddonProductTitleKT::getInstance()->getListById($__pid);
                 $name_kt = array();
                 foreach ($_scolor as $items){
                     $name_kt[$items["itemid"]][$items["colorid"]] = $items["name"];
                 }
                 foreach ($list as &$items2){
                     $items2["name_kt"] = $name_kt[$items2["products_id"]][$items2["colorid"]];
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
        $list_ctkm = Business_Addon_Ctkm::getInstance()->getList();
        foreach ($list_ctkm as $items){
            $name_ctkm[$items["id"]] = $items["name"];
        }
        
                 $day = "_".date('Ymd');
                 header("Content-Disposition: attachment; filename='$cated_name$day.txt'");
                 header("Content-Type: text/plain; charset=utf-8");
                 header('Content-Type: charset=utf-8');
                 $finalData= array();
                $strItem ="Hóa đơn bán hàng\tTên sản phẩm\tMàu\tTên kế toán\tIMEI\tGiá gốc\tGiá hoàn tiền KM\tGiá bán\tNgày bán\tChi Nhánh\tNhân viên\tTên khách hàng\tSố điện thoại\tVoucher\tTên chương trình voucher\tChương trình khuyến mãi\tSHĐ trả góp\tĐơn vị trả góp"."\r\n";
                 
                 $_arr_id = implode($id_addon_user,",");
                 $price_km = array();
                 $list_km = $_addon_promotion->getListByType($_arr_id,0,0);
                 if($list_km != null){
                         foreach ($list_km as $_item){
                             $price_km[$_item["id_addon_user"]] = $_item["total"];
                         }
                     }
                     
                if($list !=null){     
                    foreach ($list as $items){
                        
                        if (intval($items["cated_prepaid_installment"])>0) { 
							$items["installment_name"] = $_option->getTraGop2($items["cated_prepaid_installment"]);
						} else {
							$items["installment_name"] ="";
						}
						
                        $id_ctkm = $ctkm[$items["id_addon_user"]][$items["products_id"]];
                        
                        $_mbname = $mb_name[$items["id_users"]];
                        $_price_km = 0;
                        $vote_id    = $items["vote_id"];
                        $id_addon_user    = $items["id_addon_user"];
                        if($price_km != null){
                            $p = number_format($price_km[$id_addon_user]);
                            $_price_km = $p;

                        }
                        $id_voucher = strtoupper($items["id_voucher"]);
                        $id_vouchersss='';
                        if($items["id_voucher"]){
                            $id_vouchersss = $id_voucher."-". number_format($items["money_voucher"]);
                        }
                        
                        $products_price = $items["products_price"] - $items["reduction_money"]-$items["money_voucher"];
                        $_price = number_format($products_price, 0, ',', '.');
                        $_price_cost = number_format($items["products_price_cost"], 0, ',', '.');
                        $vote_name = $storename[$vote_id];
                        $imei = trim($items["imes"]);
                        $products_name = trim($items["products_name"]);
                        $items["create_date"] = date('d/m/Y',  strtotime($items["create_date"]));
                        $finalData = array(
                            $items["id_addon_user"], // For chars with accents.
                            $products_name, // For chars with accents.
                            $items["product_color"], // For chars with accents.
                            $items["name_kt"],
                            $imei,
                            $_price_cost,
                            $_price_km,
                            $_price,
                            $items["create_date"],
                            $vote_name,
                            $_mbname,
                            $items["fullname_addon"],
                            "=0&".$items["phone_addon"],                        
                            $id_vouchersss,                        
                            $id_vouchersss,                        
                            $name_ctkm[$id_ctkm],  
                            $items["contract"],
                            $items["installment_name"]
                        );
                        $strItem .= implode("\t", $finalData)."\r\n";
                 }
            }
                 ob_start(); 
                 echo $strItem;
//                 echo chr(255) . chr(254) . mb_convert_encoding($strItem, 'UTF-16LE', 'UTF-8');
                 $content = ob_get_contents();
                 ob_end_clean();
                 echo $content;
        
        
    }
    
     public function exportSalesLiteAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
//        header('Content-Type: text/csv; charset=utf-8');


// create a file pointer connected to the output stream
//$output = fopen('php://output', 'w');

// output the column headings
        
        $zwfuser = Business_Common_Users::getInstance();
        $lstorename = $zwfuser->getListByUname(FALSE);
        $storename = array();
        foreach ($lstorename as $items){
            $storename[$items["userid"]] = $items["abbreviation"];
        }
        $_users_products = Business_Addon_UsersProducts::getInstance();
        $_option = Business_Addon_Options::getInstance();
        $cated_id        = $this->_request->getParam("cated_id");
        $cated_name        = $this->_request->getParam("cated_name");
        $idregency = $this->_identity["idregency"];
        $flag = $this->_request->getParam("flag");
        $storeid = $this->_request->getParam("storeid");
        if($idregency ==26 || $idregency ==34){
            $flag ="";
        }
        if($idregency ==33){
           $flag =1; 
        }
        $start_end           = $this->_request->getParam("start_end");
        if($start_end ==null){
           $start_end = date("F j, Y")." - ".date("F j, Y"); 
        }
        $this->view->start_end = $start_end;
        $start  = $_option->getStartDate($start_end);
        $end  = $_option->getEndDate($start_end);
        
        
        
        $list = $_users_products->getListByCateIdLite(1,$start, $end, $cated_id,$storeid,"",$flag);
        $day = date('YmdHis');
//        header("Content-Disposition: attachment; filename='$cated_name$day.csv");
////        fputcsv($output, array('Ten san pham', 'Mau', 'Tong tien','So luong','Ngay ban','Chi nhanh'));
//        foreach ($list as &$items){
//            $vote_id    = $items["vote_id"];
//            $vote_name = Business_Common_Utils::removeTiengViet($storename[$vote_id]);
//            $items["create_date"] = date('d/m/Y',  strtotime($items["create_date"]));
//            $finalData = array(
//                $items["products_name"], // For chars with accents.
//                $items["product_color"], // For chars with accents.
//                $items["total_price"], 
//                $items["total_items"], 
//                $items["create_date"],
//                $vote_name,
//            );
//            fputcsv($output,$finalData);
//            
//        }
        header("Content-Disposition: attachment; filename='$cated_name$day.txt'");
        header("Content-Type: text/plain; charset=utf-8");
        header('Content-Type: charset=utf-8');
        $finalData= array();
        $strItem ="Tên sản phẩm\tMàu\tGng tiền\tGng số lượng\tNgày bán\tChi Nhánh"."\r\n";    
        $strItem = str_replace ( "ÿþ", "" ,  $strItem);
        chr(255) . chr(254) . mb_convert_encoding($strItem, 'UTF-16LE', 'UTF-8');
        foreach ($list as $items){
            $vote_id    = $items["vote_id"];
            $products_name = trim($items["products_name"]);
            $vote_name = Business_Common_Utils::removeTiengViet($storename[$vote_id]);
            $items["create_date"] = date('d/m/Y',  strtotime($items["create_date"]));
            $finalData = array(
                $products_name, // For chars with accents.
                $items["product_color"], // For chars with accents.
                $items["total_price"], 
                $items["total_items"], 
                $items["create_date"],
                $vote_name,
            );
            $strItem .= implode("\t", $finalData)."\r\n";
            
        }
        
        ob_start();
        echo $strItem;
//        echo chr(255) . chr(254) . mb_convert_encoding($strItem, 'UTF-16LE', 'UTF-8');
        $content = ob_get_contents();
        ob_end_clean();
        echo $content;
    }
    
    public function salesAction(){
        if ($_REQUEST["d"]==1){die(".");}
        $u_product                  = Business_Addon_UsersProducts::getInstance();
        $_zwf_user                  = Business_Common_Users::getInstance();
        $_option                    = Business_Addon_Options::getInstance();
        $_products                  = Business_Helpers_Products::getInstance();
        $_productsItem              = Business_Ws_ProductsItem::getInstance();
        $group_hnam = $_option->getGroupHnam();
        $this->view->group_hnam = $group_hnam;
        $vid = $this->_identity["parentid"];
        $idregency = $this->_identity["idregency"];
        
        $type_old = $_option->getDemo99VTLN();
        $this->view->type_old = $type_old;
        
        $is_apple = $this->_request->getParam("is_apple");
        $this->view->is_apple = $is_apple;
        $is_type = $this->_request->getParam("is_type");
        $this->view->is_type = $is_type;
        $productsid                 = (int)$this->_request->getParam("productsid");
        $cated_name                 = $this->_request->getParam("cated_name");
        $this->view->cated_name     = $cated_name;
        if($productsid==0){
            $productsid =3;
        }
        $this->view->productsid     = $productsid;
        $cated_id                   = (int)$this->_request->getParam("cated_id");
        $flag                       = $this->_request->getParam("flag");
        $menus =  $_products->getProductMenu();
        
        $this->view->itemid         = $cated_id;
        $storename = array();
        $list_vote                  = $_zwf_user->getListByUname(false);
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
        $this->view->flag           = $flag;
        $iexport = (int)  $this->_request->getParam("iexport");
        $bgd=0;
        if($_option->isBGD($idregency)){
            $bgd=1;
            $storeid = (int) $this->_request->getParam("storeid");
        }else{
            $storeid = $this->_identity["parentid"];
        }
        $this->view->bgd = $bgd;
        $this->view->storeid = $storeid;
        $ret = array();
        $count = array();
        //export by cateid
        
        if($iexport==1){
            $list3                    = $u_product->getListSalesCatedId($start, $end,"",0,$productsid,"","");
            foreach ($list3 as $items){
                $__cid[] = $items["cated_id"];
            }
            $str_catedid = array_unique($__cid);
            $str = implode(",", $str_catedid);
            
            $this->_helper->Layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true); 
            $list_export_by_cateid = $u_product->get_list_by_cateid($str,$start, $end);
            foreach ($menus as $m){
                $name_cateid[$m["itemid"]] = $m["title"];
            }
            $this->exportbycateidAction($list_export_by_cateid,$name_cateid, $start, $end);
        }
        if($iexport==2){
            $list3                    = $u_product->getListSalesCatedId($start, $end,"",0,$productsid,"","");
            foreach ($list3 as $items){
                $__cid[] = $items["cated_id"];
            }
            $str_catedid = array_unique($__cid);
            $str = implode(",", $str_catedid);
            
            $this->_helper->Layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true); 
            $list_export_by_cateid = $u_product->get_list_by_cateid2($str,$start, $end);
            foreach ($menus as $m){
                $name_cateid[$m["itemid"]] = $m["title"];
            }
            $this->exportbycateidAction($list_export_by_cateid,$name_cateid, $start, $end);
        }
                
                
        if($cated_id ==0){// tất cả
            
            if($storeid >0){
                $this->_helper->viewRenderer('sales-storeid');
            }
            
            $list                    = $u_product->getListSalesCatedId($start, $end,$flag,$storeid = 0,$productsid,$is_apple,$is_type);
            foreach ($list as $items){
                $__cid[] = $items["cated_id"];
            }
            $str_catedid = array_unique($__cid);
            $str = implode(",", $str_catedid);
            
            
            
            
            
            $_menuitem = Business_Ws_MenuItem::getInstance();
            if($str != null){
                $menus = $_menuitem->getListByItemid($str);
            }
            
            
            
            foreach($list as $item) {
                $vote_id                    = $item["vote_id"];
                $_cateid                   = $item["cated_id"];
                $ret[$vote_id][$_cateid]   = $item["sum"];
                $count[$vote_id][$_cateid] = $item["total"];
            }
            $col = 100/($count_col + 2);
        }else{//chi tiết
            if($storeid >0){
                $this->_helper->viewRenderer('detail-sales-storeid');
            }else{
               $this->_helper->viewRenderer('detail-sales'); 
            }
            
           $slist                    = $u_product->getListSalesProductId($start, $end,$flag,$vonline = 0,$productsid,$cated_id,$is_apple,$is_type); 
           foreach ($slist as $items){
               $__pid[] = $items["products_id"];
           }
           $__pid = array_unique($__pid);
           $__strpid = implode(",", $__pid);
           if($__strpid !=null){
               $lmenu = $_productsItem->getListByProductsID2($__strpid);
           }
           foreach($slist as $item) {
                $storeid                    = $item["vote_id"];
                $_itemid                   = $item["products_id"];
                $ret[$storeid][$_itemid]   = $item["sum"];
                $count[$storeid][$_itemid] = $item["total"];
            }
           $this->view->lmenu = $lmenu;
           $menus = $_option->getMenuById($productsid);
           $col = 100/($count_col + 3);
        }
        
        $this->view->sums               = $ret;
        $this->view->count              = $count;
        $this->view->menu  = $menus;
        $this->view->list_vote          = $list_vote;
        
        $list_hnammobile        = $_option->getCatedHnam();
        $this->view->list_hnammobile = $list_hnammobile;
        $list_catedhnammobile        = $_option->getCatedHnammobile();
        $this->view->list_flag  = $list_catedhnammobile;
        
        $this->view->col = $col;
        
        $this->view->vid = $vid;
        $this->view->idregency = $idregency;
        
    }
    public function exportdanhmucxuathoadonAction(){
        $_option = Business_Addon_Options::getInstance();
        $list_hnammobile        = $_option->getCatedHnam();
        $this->view->list_hnammobile = $list_hnammobile;
        $u_product = Business_Addon_UsersProducts::getInstance();
        $productsid           = (int)$this->_request->getParam("productsid");
        $menus =  $_option->getMenuById($productsid);
        
        $start_end           = $this->_request->getParam("start_end");
        if($start_end ==null){
           $start_end = date("F j, Y")." - ".date("F j, Y"); 
        }
        $this->view->start_end = $start_end;
        $start  = $_option->getStartDate($start_end);
        $end  = $_option->getEndDate($start_end);
        $this->view->start = $start;
        $this->view->end = $end;
        if($productsid >0){
            $list3                    = $u_product->getListSalesCatedId($start, $end,"",0,$productsid,"","");
            foreach ($list3 as $items){
                $__cid[] = $items["cated_id"];
            }
            $str_catedid = array_unique($__cid);
            $str = implode(",", $str_catedid);
            $list_export_by_cateid = $u_product->get_list_by_cateid2($str,$start, $end);
            foreach ($menus as $m){
                $name_cateid[$m["itemid"]] = $m["title"];
            }
            $this->exportbycateidAction($list_export_by_cateid,$name_cateid, $start, $end);
        }
            
    }
    public function exportbycateidAction($list,$name_cateid, $start, $end){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true); 
        $day = date('YmdHis'); 
        
        header("Content-Disposition: attachment; filename=thongkedm_$day.txt");
        header("Content-Type: text/csv; charset=utf-8");
        header("Content-Type: charset=utf-8");
        $finalData= array();
        $strItem ="Từ $start đến $end"."\r\n";  
        $strItem .="Hang\tTensanpham\tMau\tTongtien\tSoluong"."\r\n";  
        $list_color = Business_Ws_NewsItem::getInstance()->getListByCateId(722);
        foreach ($list_color as $items2){
            $name_color[$items2["itemid"]] = $items2["title"];
        }
        
        foreach ($list as $items){
            if($items["flag"]==1){
//                $__cateid = $name_cateid[$items["cated_id"]];
                $__cateid = ucwords(strtolower($name_cateid[$items["cated_id"]]));
                $__name = ucwords(strtolower($items["products_name"]));
                $__color = ucwords(strtolower($name_color[$items["colorid"]]));
                $__total = $items["total"];
                $__sum = $items["sum"];

                    $finalData = array(
                         // For chars with accents.
                        $__cateid,
                        $__name,
                        $__color,
                        $__sum,
                        $__total,
                    );
                $strItem .= implode("\t", $finalData)."\r\n";
            }
        }
        ob_start();
		echo $strItem;
        //echo chr(255) . chr(254) . mb_convert_encoding($strItem, 'UTF-16LE', 'UTF-8');
        $content = ob_get_contents();
        ob_end_clean();
        echo $content;
    }

    public function salesServiceAction(){
        if ($_REQUEST["d"]==1){die(".");}
        $u_product                  = Business_Addon_UsersProducts::getInstance();
        $_zwf_user                  = Business_Common_Users::getInstance();
        $_option                    = Business_Addon_Options::getInstance();
        $_products                  = Business_Helpers_Products::getInstance();
        $_productsItem              = Business_Ws_ProductsItem::getInstance();
        $group_hnam = $_option->getGroupHnam();
        $this->view->group_hnam = $group_hnam;
        
        $type_old = $_option->getDemo99VTLN();
        $this->view->type_old = $type_old;
        
        $is_apple = $this->_request->getParam("is_apple");
        $this->view->is_apple = $is_apple;
        $is_type = $this->_request->getParam("is_type");
        $this->view->is_type = $is_type;
        $productsid                 = (int)$this->_request->getParam("productsid");
        $cated_name                 = $this->_request->getParam("cated_name");
        $this->view->cated_name     = $cated_name;
        if($productsid==0){
            $productsid =3;
        }
        $productsid = 10;
        $this->view->productsid     = $productsid;
        $cated_id                   = (int)$this->_request->getParam("cated_id");
        $flag                       = $this->_request->getParam("flag");
        $menus =  $_option->getMenuById($productsid);
        
        $this->view->itemid         = $cated_id;
        $storename = array();
        $list_vote                  = $_zwf_user->getListByUname(false);
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
        $this->view->flag           = $flag;
        
        $storeid = (int) $this->_request->getParam("storeid");
        $this->view->storeid = $storeid;
        $ret = array();
        $count = array();
        if($cated_id ==0){// tất cả
            
            if($storeid >0){
                $this->_helper->viewRenderer('sales-service-storeid');
            }
            
            $list                    = $u_product->getListSalesCatedId($start, $end,$flag,$storeid = 0,$productsid,$is_apple,$is_type);
            foreach ($list as $items){
                $__cid[] = $items["cated_id"];
            }
            $str_catedid = array_unique($__cid);
            $str = implode(",", $str_catedid);

            $_menuitem = Business_Ws_MenuItem::getInstance();
            if($str != null){
                $menus = $_menuitem->getListByItemid($str);
            }
            foreach($list as $item) {
                $vote_id                    = $item["vote_id"];
                $_cateid                   = $item["cated_id"];
                $ret[$vote_id][$_cateid]   = $item["sum"];
                $count[$vote_id][$_cateid] = $item["total"];
            }
            $col = 100/($count_col + 2);
        }else{//chi tiết
            if($storeid >0){
                $this->_helper->viewRenderer('detail-sales-service-storeid');
            }else{
               $this->_helper->viewRenderer('detail-sales-service'); 
            }
            
           $slist                    = $u_product->getListSalesProductId($start, $end,$flag,$vonline = 0,$productsid,$cated_id,$is_apple,$is_type); 
           foreach ($slist as $items){
               $__pid[] = $items["products_id"];
           }
           $__pid = array_unique($__pid);
           $__strpid = implode(",", $__pid);
           if($__strpid !=null){
               $lmenu = $_productsItem->getListByProductsID2($__strpid);
           }
           foreach($slist as $item) {
                $storeid                    = $item["vote_id"];
                $_itemid                   = $item["products_id"];
                $ret[$storeid][$_itemid]   = $item["sum"];
                $count[$storeid][$_itemid] = $item["total"];
            }
           $this->view->lmenu = $lmenu;
           $menus = $_option->getMenuById($productsid);
           $col = 100/($count_col + 3);
        }
        
        $this->view->sums               = $ret;
        $this->view->count              = $count;
        $this->view->menu  = $menus;
        $this->view->list_vote          = $list_vote;
        
        $list_hnammobile        = $_option->getCatedHnam();
        $this->view->list_hnammobile = $list_hnammobile;
        $list_catedhnammobile        = $_option->getCatedHnammobile();
        $this->view->list_flag  = $list_catedhnammobile;
        
        $this->view->col = $col;
        
        
    }
    
    
    public function salesOrtherAction(){
       
        if ($_REQUEST["d"]==1){die(".");}
        $u_product                  = Business_Addon_UsersProducts::getInstance();
        $_zwf_user                  = Business_Common_Users::getInstance();
        $_option                    = Business_Addon_Options::getInstance();
        $_accept_by_cated           = Business_Common_AcceptByCated::getInstance();
        $_products                  = Business_Helpers_Products::getInstance();
        $_productsItem              = Business_Ws_ProductsItem::getInstance();
        $_menuitem = Business_Ws_MenuItem::getInstance();
        $productsid                 = (int)$this->_request->getParam("productsid");
        $cated_name                 = $this->_request->getParam("cated_name");
        $this->view->cated_name     = $cated_name;
        
        $type_old = $_option->getDemo99VTLN();
        $this->view->type_old = $type_old;
        $group_hnam = $_option->getGroupHnam();
        $this->view->group_hnam = $group_hnam;
        $is_type = $this->_request->getParam("is_type");
        $this->view->is_type = $is_type;
        $is_apple = $this->_request->getParam("is_apple");
        $this->view->is_apple = $is_apple;
        
        
        if($productsid==0){
            $productsid =3;
        }
        $this->view->productsid     = $productsid;
        $cated_id                   = (int)$this->_request->getParam("cated_id");
        $flag                       = 1;
        if($productsid == 4){
            $flag = "";
        }
        $idregency = $this->_identity["idregency"];
        $userid = $this->_identity["userid"];
        $detail_accept = $_accept_by_cated->getDetailByUserIdProductsId($userid,$productsid);
        
        if($detail_accept == NULL){
            $this->_redirect('/admin/home');
        }
        if($detail_accept["alls"]==1){
            $menus = $_option->getMenuById($productsid);
        }else{
            $menus = $_menuitem->getListByItemid($detail_accept["cated_id"]);
            $___cate = $detail_accept["cated_id"];
            $___catedid = explode(",", $___cate);
            if($cated_id>0){
               if(!in_array($cated_id, $___catedid)){
                    $this->_redirect('/admin/home');
                } 
            }
            
        }
        
//        $menus =  $_products->getProductMenu();
        
         
        $this->view->itemid         = $cated_id;
        $list_vote                  = $_zwf_user->getListByUname(false);
        $this->view->flag           = $flag;
        $start_end           = $this->_request->getParam("start_end");
        if($start_end ==null){
           $start_end = date("F j, Y")." - ".date("F j, Y"); 
        }
        $this->view->start_end = $start_end;
        $start  = $_option->getStartDate($start_end);
        $end  = $_option->getEndDate($start_end);
        $this->view->start = $start;
        $this->view->end = $end;
        $storeid = (int) $this->_request->getParam("storeid");
        $this->view->storeid = $storeid;
        $ret = array();
        $count = array();
        if($cated_id ==0){// tất cả
            if($storeid >0){
                $this->_helper->viewRenderer('sales-orther-storeid');
            }
            $list                    = $u_product->getListSalesCatedId($start, $end,$flag,$storeid,$productsid,$is_apple,$is_type);
            foreach ($list as $items){
                $__cid[] = $items["cated_id"];
            }
            $str_catedid = array_unique($__cid);
            $str = implode(",", $str_catedid);

            if($str != null){
                if($detail_accept["alls"]==1){
                    $menus = $_menuitem->getListByItemid($str);
                }
                
            }
            foreach($list as $item) {
                $vote_id                    = $item["vote_id"];
                $_cateid                   = $item["cated_id"];
                $ret[$vote_id][$_cateid]   = $item["sum"];
                $count[$vote_id][$_cateid] = $item["total"];
            }
            $count_col = count($list_vote);
            $this->view->col = 100/($count_col + 2);
            
        }else{//chi tiết
            
            if($storeid >0){
                $this->_helper->viewRenderer('detail-sales-orther-storeid');
            }else{
               $this->_helper->viewRenderer('detail-sales-orther'); 
            }
           $slist                    = $u_product->getListSalesProductId($start, $end,$flag,$vonline = 0,$productsid,$cated_id,"",$is_type); 
           foreach ($slist as $items){
               $__pid[] = $items["products_id"];
           }
           $__pid = array_unique($__pid);
           $__strpid = implode(",", $__pid);
           if($__strpid !=null){
               $lmenu = $_productsItem->getListByProductsID2($__strpid);
           }
           foreach($slist as $item) {
                $storeid                    = $item["vote_id"];
                $_itemid                   = $item["products_id"];
                $ret[$storeid][$_itemid]   = $item["sum"];
                $count[$storeid][$_itemid] = $item["total"];
            }
           $this->view->lmenu = $lmenu;
           $menus = $_option->getMenuById($productsid);
           $count_col = count($list_vote);
            $this->view->col = 100/($count_col + 3);
        }
        
        $this->view->sums               = $ret;
        $this->view->count              = $count;
        $this->view->menu  = $menus;
        $this->view->list_vote          = $list_vote;
        
        $list_hnammobile        = $_option->getCateHnamNew();
        $this->view->list_hnammobile = $list_hnammobile;
        $list_catedhnammobile        = $_option->getCatedHnammobile();
        $this->view->list_flag  = $list_catedhnammobile;
        
        
        
    }
    
    public function getProductsidAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $_products                  = Business_Helpers_Products::getInstance();
        $_products = Business_Helpers_Products::getInstance();
        $_option                    = Business_Addon_Options::getInstance();
        $_accept_by_cated           = Business_Common_AcceptByCated::getInstance();
        $_menuitem = Business_Ws_MenuItem::getInstance();
        $productsid                 = $this->_request->getParam("productsid");
        $userid = $this->_identity["userid"];
        $detail = $_accept_by_cated->getDetailByUserIdProductsId($userid,$productsid);
        $cateid = $detail["cated_id"];
        if($detail["alls"] ==1){
            $menu = $_option->getMenuById($productsid);
        }else{
            $menu = $_menuitem->getDetailById2($cateid);
        }
        
        $ret                        = array();
        $arr                        = array();
        foreach ($menu as $items){
            $arr["itemid"]          = $items["itemid"];
            $arr["title"]           = $items["title"];
            $ret[]                  = $arr;
        }
        echo json_encode($ret);
    }

        public function apiDoanhsobanAction(){
        $u_product = Business_Addon_UsersProducts::getInstance();
        $_zwf_user = Business_Common_Users::getInstance();
        $_option    = Business_Addon_Options::getInstance();
        $cated_id  = $this->_request->getParam("cated_id");
        $cated_name                 = $this->_request->getParam("cated_name");
        $this->view->cated_name     = $cated_name;
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
        echo json_encode($result);
    }
    
    
    public function chartAction() {
        $request = $_REQUEST;
        $_option                = Business_Addon_Options::getInstance();
        $idregency = $this->_identity["idregency"];
        if($idregency ==34){
            $this->_helper->viewRenderer('chart2');
        }
        $bgd =0;
        if($_option->isBGD($idregency)){
            $bgd=1;
        }
        
        $start_end           = $this->_request->getParam("start_end");
        if($start_end ==null){
           $start_end = date("F j, Y")." - ".date("F j, Y"); 
        }
        $this->view->start_end = $start_end;
        $start  = $_option->getStartDate($start_end);
        $end  = $_option->getEndDate($start_end);
        $this->view->start = $start;
        $this->view->end = $end;
        $date_from              = $start;
        $date_to                = $end;
        
        $_users_products        = Business_Addon_UsersProducts::getInstance();
        $list                   = $_users_products->getSaleByMonth($date_from,$date_to,1);
        
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
            if($item["productsid"]==3){
                if ($item["flag"]==1){
                    $ret_phone[$item["date"]] += $item["total"];
                    $ret_phone_count[$item["date"]] += $item["sl"];
                    $_date_phone[$item["date"]] = '"'.$d.'"'; 
                }else{
                    if($bgd==1){
                        $ret_phone2[$item["date"]] += $item["total"];
                        $ret_phone_count2[$item["date"]] += $item["sl"];
                        $_date_phone[$item["date"]] = '"'.$d.'"';
                    }else{
                        if($item["cated_id"] ==53){
                            $ret_phone2[$item["date"]] += $item["total"];
                            $ret_phone_count2[$item["date"]] += $item["sl"];
                            $_date_phone[$item["date"]] = '"'.$d.'"';
                        }
                    }
                }
            }
            //may tinh bang 
            if($item["productsid"]==5){
                if($item["flag"]==1){
                    $ret_tablet[$item["date"]] += $item["total"];
                    $ret_tablet_count[$item["date"]] += $item["sl"];
                    $_date_tablet[$item["date"]] = '"'.$d.'"';
                }else{
                    if($bgd ==1){
                        $ret_tablet2[$item["date"]] += $item["total"];
                        $ret_tablet_count2[$item["date"]] += $item["sl"];
                        $_date_tablet[$item["date"]] = '"'.$d.'"';
                    }else{
                        if($item["cated_id"] ==53){
                            $ret_tablet2[$item["date"]] += $item["total"];
                            $ret_tablet_count2[$item["date"]] += $item["sl"];
                            $_date_tablet[$item["date"]] = '"'.$d.'"';
                        }
                    }
                }
            }
            //phu kien hnam
                if($item["productsid"]==4){
                    if($item["flag"] ==1){
                        $ret_acc[$item["date"]] += $item["total"];
                        $ret_acc_count[$item["date"]] += $item["sl"];
                        $_date_acc[$item["date"]] = '"'.$d.'"'; 
                    }else{
                        if($bgd ==1 || $idregency ==34){
                            $ret_acc2[$item["date"]] += $item["total"];
                            $ret_acc_count2[$item["date"]] += $item["sl"];
                            $_date_acc[$item["date"]] = '"'.$d.'"';
                        }
                         
                    }
                }
            //laptop
                if($item["productsid"]==6){
                    if($item["flag"] ==1){
                        $ret_latop[$item["date"]] += $item["total"];
                        $ret_latop_count[$item["date"]] += $item["sl"];
                        $_date_laptop[$item["date"]] = '"'.$d.'"';
                    }else{
                        if($bgd ==1){
                            $ret_latop2[$item["date"]] += $item["total"];
                            $ret_latop_count2[$item["date"]] += $item["sl"];
                            $_date_laptop[$item["date"]] = '"'.$d.'"';
                        }
                    }
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
        $datas[] = $obj;
//        if($bgd ==1){
            $obj2["name"] = "Hnam";
            foreach($ret_phone2 as  $total) {
                $sum_phone_hnam += $total;
                $seri2[] = $total;
            }
            $obj2["data"] = $seri2;
            $datas[] = $obj2;
//        }
        
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
//        if($bgd ==1){
            //hnam
            $obj_count2["name"] = "Hnam";
            foreach($ret_phone_count2 as  $total) {            
                $seri_count2[] = $total;
            }
            $obj_count2["data"] = $seri_count2;
            $datas_count[] = $obj_count2;
//        }
        
        $this->view->seri_sl = json_encode($datas_count);
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
//        if($bgd ==1){
            //hnam
            $obj_tablet2["name"] = "Hnam";
            foreach($ret_tablet2 as  $total) {
                $sum_tablet_hnam +=$total;
                $seri_tablet2[] = $total;
            }
            $obj_tablet2["data"] = $seri_tablet2;
            $data_tablet[] = $obj_tablet2;
//        }
        
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
        if($bgd ==1){
            //hnam
            $obj_tablet_sl2["name"] = "hnam";
            foreach($ret_tablet_count2 as  $total) {            
                $seri_tablet_sl2[] = $total;
            }
            $obj_tablet_sl2["data"] = $seri_tablet_sl2;
            $data_tablet_count[] = $obj_tablet_sl2;
        }
        
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
        
        //cty
        $obj_acc["name"] = "Cty";
        foreach($ret_acc as  $total) {
            $sum_acc_cty += $total;
            $seri_acc[] = $total;
        }
        $obj_acc["data"] = $seri_acc;
        $data_acc[] = $obj_acc;
//        if($bgd ==1){
            //hnam
            $obj_acc2["name"] = "Hnam";
            foreach($ret_acc2 as  $total) {
                $sum_acc_hnam +=$total;
                $seri_acc2[] = $total;
            }
            $obj_acc2["data"] = $seri_acc2;
            $data_acc[] = $obj_acc2;
//        }
        $this->view->seri_acc = json_encode($data_acc);
        
        //so luong
        $this->view->date_acc_sl = implode(",",$_date_acc);
        $obj_acc_sl["name"] = "cty";
        foreach($ret_acc_count as  $total) {            
            $seri_acc_sl[] = $total;
        }
        $obj_acc_sl["data"] = $seri_acc_sl;
        $data_acc_sl[] = $obj_acc_sl;
        
        
        $this->view->date_acc_sl = implode(",",$_date_acc);
//        if($bgd ==1){
            $obj_acc_sl["name"] = "hnam";
            foreach($ret_acc_count2 as  $total) {            
                $seri_acc_sl2[] = $total;
            }
            $obj_acc_sl["data"] = $seri_acc_sl2;
            $data_acc_sl[] = $obj_acc_sl;
//        }
        
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
        if($bgd ==1){
            // laptop hnam
            $obj_laptop2["name"] = "Hnam";
            foreach($ret_latop2 as  $total) {
                $sum_laptop_hnam +=$total;
                $seri_laptop2[] = $total;
            }
            $obj_laptop2["data"] = $seri_laptop2;
            $data_laptop[] = $obj_laptop2;
        }
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
        if($bgd ==1){
            //hnam
            $obj_laptop_sl2["name"] = "Hnam";
            foreach($ret_latop_count2 as  $total) {            
                $seri_laptop_sl2[] = $total;
            }
            $obj_laptop_sl2["data"] = $seri_laptop_sl2;
            $data_laptop_sl[] = $obj_laptop_sl2;
        }
        $this->view->seri_laptop_sl = json_encode($data_laptop_sl);
        
    }
    
    
}
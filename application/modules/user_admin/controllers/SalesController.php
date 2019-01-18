<?php

/**
 * User Admin Home Controller
 * @author: nghidv
 */
class User_Admin_SalesController extends Zend_Controller_Action {

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
public function thongKeXiaomiAction(){
    $_users_products        = Business_Addon_UsersProducts::getInstance();
    $_zfw_users             = Business_Common_Users::getInstance();
    $_option                = Business_Addon_Options::getInstance();
     $days_created_end = $this->_request->getParam("day_created_end");
     $__endday              = $_option->getDayEndByMonths();
    if ($days_created_end == null) {
        $days_created_end   = date('Y/m/01') . ' - ' .$__endday;
    }
    $this->view->days_created_end       = $days_created_end ;
    $created_date           = substr($days_created_end, 0, 10);
    $created_date           = str_replace("/", "-", $created_date).' 00:00:00';
    $end_date               = substr($days_created_end, 13, 10);
    $end_date               = str_replace("/", "-", $end_date).' 23:59:59';

    $list_vote              = $_zfw_users->getListByUname(false);
    $this->view->list_vote  = $list_vote;
    $ret                    = array();
    $ret2                    = array();
    $list                   = $_users_products->getListByXiaomi("", $created_date, $end_date,"","",6020);
    foreach ($list as $items){
        $vote_id            = $items["vote_id"];
        $ret[$vote_id]      = $items["tong"];
        $ret2[$vote_id]     = $items["soluong"];
    }
    $this->view->sums       = $ret;
    $this->view->tongsoluong= $ret2;
    
    $ret_tong               = array();
    $ret2_tong              = array();
    $list_tong              = $_users_products->getListByXiaomi("", $created_date, $end_date,"","",6020,$all='tong');
    foreach ($list_tong as $items){
        $vote_id            = $items["vote_id"];
        $ret_tong[$vote_id] = $items["tong"];
        $ret2_tong[$vote_id]= $items["soluong"];
    }
    $this->view->sums_tong  = $ret_tong;
    $this->view->tongsoluong_tong   = $ret2_tong;
//    echo "<pre>";
//    var_dump($list_tong);
//    exit();
//    
    
}

private function getStoreName($username) {
        $array = array(
            "vote_89" => "89 Trần Quang Khải",
            "vote_148" => "148 Nguyễn Cư Trinh",
            "vote_654" => "654 Lê Hồng Phong",
            "vote_774" => "774 Nguyễn Trãi",
            "vote_370" => "370A Lê Văn Sỹ",
            "vote_43" => "67 Trần Quan Khải",
            "vote_67" => "67 Trần Quan Khải",
            "vote_778" => "778 CMT8",
            "vote_191" => "191 Khánh Hội",
            "vote_301" => "301 Võ Văn Tần",
            "vote_294" => "294A Bạch Đằng",
            "vote_249" => "249 Phan Đăng Lưu",
            "vote_206" => "206 Hoàng Văn Thụ",
            "vote_253" => "253 Quang Trung",
            "vote_all" => "Quản lý các cửa hàng"
        );
        if ($username==null) return $array;
        return $array[$username];
    }
    public function dsttAction(){
        $u_product = Business_Addon_UsersProducts::getInstance();
        $_zwf_user = Business_Common_Users::getInstance();
        $_option    = Business_Addon_Options::getInstance();
        $_ctysales = Business_Common_CtySales::getInstance();
        $cated_id  = $this->_request->getParam("cated_id");
        $this->view->itemid             = $cated_id;
        
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
        $end_day = str_replace("/", "-", $end_date) . ' 23:59:59';
        
        $months = date('m',  strtotime($end_day));
        $years = date('Y',  strtotime($end_day));
        $list = $u_product->getListGroupByFlag($created_day, $end_day);
//        echo "<pre>";
//        var_dump($list);
//        exit();
        $ret = array();
        $count = array();
        foreach($list as $item) {
            $flag       = $item["flag"];
            $vote_id  = $item["vote_id"];
            $ret[$vote_id][$flag] = $item["sum"];
            $count[$vote_id][$flag] = $item["countp"];
        }
        $this->view->list_vote = $list_vote;
        $this->view->sums = $ret;
        $this->view->count = $count;
        $this->view->created_date = $created_date;
        $this->view->end_date = $end_date;
        
        $list_sales = $_ctysales->getListGroupByVoteId($userid="", $months=8, $years,1);
        
        $ret2 = array();
        $count2 = array();
        foreach($list_sales as $item) {
            $cateid_products       = $item["cateid_products"];
            $vote_id  = $item["vote_id"];
            $ret2[$vote_id][$cateid_products] = $item["sum_prices"];
            $count2[$vote_id][$cateid_products] = $item["sum_numbers"];
            echo "<pre>";
        var_dump($ret2);
        }
        
        exit();
        $this->view->sums2 = $ret2;
        $this->view->count2 = $count2;
    }
    

    
    public function viewSalesMonthsAction(){
        for ($i = 1; $i <= 31; $i++) {
            $days[$i] = $i;
        }
        $this->view->days = $days;
        $this->view->cur_day = date("d");

        for ($i = 1; $i <= 12; $i++) {
            $months[$i] = $i;
        }
        $this->view->months = $months;
        $this->view->cur_month = date("m");

        for ($i = 2013; $i <= date('Y'); $i++) {
            $years[$i] = $i;
        }
        $this->view->years = $years;
        $this->view->cur_year = date("Y"); 
    }
    public function viewSalesAction(){
        $vote = Business_Common_Users::getInstance()->getListByUname();
        $this->view->vote =  $vote;
    }

    public function addSalesAction(){
        $_option = Business_Addon_Options::getInstance();
//        exit('767');
        for ($i = 1; $i <= 31; $i++) {
            $days[$i] = $i;
        }
        $this->view->days = $days;
        $this->view->cur_day = date("d");

        for ($i = 1; $i <= 12; $i++) {
            $months[$i] = $i;
        }
        $this->view->months = $months;
        $this->view->cur_month = date("m");

        for ($i = 2013; $i <= date('Y'); $i++) {
            $years[$i] = $i;
        }
        
//        var_dump(date('m'));exit();
        $this->view->years = $years;
        $this->view->cur_year = date("Y");  
        $vote = Business_Common_Users::getInstance()->getListByUname(FALSE);
        $this->view->vote =  $vote;
        $cated_products = Business_Common_CatedProducts::getInstance()->getList();
        $this->view->cated_products = $cated_products;
        
        $list_type      = $_option->getCateTargetHnamNew();
        $this->view->list_type = $list_type;
//          var_dump($vote);exit();
    }
    public function editSalesAction(){
        $_option = Business_Addon_Options::getInstance();
        $this->view->id=$id = $this->_request->getParam("id");
        $this->view->month = $month = $this->_request->getParam("month");
        $list = Business_Common_CtySales::getInstance()->getListById($id);
//         var_dump($list);exit();
        foreach ($list as &$items){
            $user_id = $items["user_id"];
//            var_dump($user_id);exit();
            $vote_name = Business_Common_Users::getInstance()->getListById($user_id);
//            var_dump($this->view->vote_id);exit();
            foreach ($vote_name as $vt_name){
                $this->view->vote_id= $vt_name["userid"];
                $this->view->vote_name = Business_Addon_Options::getInstance()->getStoreName($vt_name["userid"]);
            }
//            var_dump($this->view->vote_id);exit();
            $this->view->cated_products_id= $cated_products_id = $items["cateid_products"];
//            var_dump($this->view->cated_products_id);exit();
            $cated_products_name = Business_Common_CatedProducts::getInstance()->getListById($cated_products_id);
            foreach ($cated_products_name as $cp_name){
                $this->view->cated_product_name = $cp_name["cate_product_name"];
            }
            $this->view->sum_prices = $items["sum_prices"];
//            var_dump($this->view->sum_prices);exit();
            $this->view->sum_numbers = $items["sum_numbers"];
            $this->view->months = $items["months_created"];
            $this->view->years = $items["years_created"];
        }
        $this->view->items = $list;
        $list_type      = $_option->getCateTargetHnamNew();
        $this->view->list_type = $list_type;
//        var_dump($list);exit();
    }
    public function deleteSalesAction(){
            $this->_helper->Layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            $id = $this->_request->getParam("id");
//            var_dump($id);exit();
            Business_Common_CtySales::getInstance()->deleteSalesById2($id);
        
    }

    public function saveSalesAction(){
        if ($this->_request->isPost()) {
            $this->_helper->Layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            $id = (int)($this->_request->getParam("id"));
//            var_dump($id);exit();
            $vote_id = $this->_request->getParam("vote-name");
            $cated_id = $this->_request->getParam("cateid_name");
            $cated_name = Business_Common_CatedProducts::getInstance()->getListById($cated_id);
            foreach ($cated_name as $c_name){
                $cated_names = $c_name["cate_product_name"];
            }
//            var_dump($cated_id);exit();
            $sum_prices = $this->_request->getParam("sum_prices", 0);
            $sum_prices = str_replace(",", "", $sum_prices);
            $sum_numbers = $this->_request->getParam("sum-numbers",0);
            $sum_numbers = str_replace(",", "", $sum_numbers);
//            var_dump($sum_numbers);exit();
            $months_created = $this->_request->getParam("months_createdVN");
//            var_dump($months_created);exit();
            $years_created = $this->_request->getParam("years_createdVN");
            $type = $this->_request->getParam("type");
//            var_dump($sum_prices);exit();
//            var_dump($years_created);exit();
            $cateid_role = Business_Common_CtySales::getInstance()->getCateid($cated_id,$vote_id,$months_created,$years_created,$type);
//            var_dump($cateid_role);exit();
            $ret = array();
            if($id == 0){
                 if ($vote_id == '0') {
                    $err['id'] = "vote_id";
                    $err['msg'] = "Chọn chi nhánh";
                    $ret[] = $err;
                }
                 if ($cated_id == '0') {
                    $err['id'] = "cated_id";
                    $err['msg'] = "Chọn nhóm sản phẩm";
                    $ret[] = $err;
                }
                 if ($type == 0) {
                    $err['id'] = "type";
                    $err['msg'] = "Lỗi.\nVui lòng chọn loại sản phẩm";
                    $ret[] = $err;
                }
                if($cateid_role !=null && $cated_id !=null){
                    $err['id'] = "catedid_role";
                    $err['msg'] = "Hàng $cated_names đã được thêm vào tháng $months_created / $years_created ";
                    $ret[] = $err;
                }
                 if ($sum_prices == null || empty($sum_prices)) {
                    $err['id'] = "sum_prices";
                    $err['msg'] = "Số tiền không được để trống";
                    $ret[] = $err;
                }
                 if ($sum_numbers == null || empty($sum_numbers)) {
                    $err['id'] = "sum_numbers";
                    $err['msg'] = "Số lượng không được để trống";
                    $ret[] = $err;
                }
            }
            if (count($ret) > 0){
                echo json_encode($ret);
                
            }else{
                if($id > '0'){
//                    $data = Business_Common_CtySales::getInstance()->getListById($id);
//                    var_dump($data);exit();
                }
                else{
                    $data= array();
                } 
                $data["type"] = $type;
                $data["cateid_products"] = $cated_id;
                $data["sum_prices"] = $sum_prices;
                $data["sum_numbers"] = $sum_numbers;
                $data["user_id"] = $vote_id;
                $data["is_actived"] = "1";
                $data["months_created"]=$months_created;
                $data["years_created"]=$years_created;
                $data["datetime"]=date('Y-m-d H:i:s');
                $data["uid_created"]=  $this->_identity["userid"];
                $data["check_vote"]=1;
//                var_dump($data["years_created"]);exit();
                if($id == '0'){
                    Business_Common_CtySales::getInstance()->addSales($data);
                }
                else{
//                    exit('update');
//                    var_dump($id);exit();
                    
                    Business_Common_CtySales::getInstance()->updateSalesById($id, $data);
                }
                $err['id'] = "ok";
                $err['msg'] = "ok";
//                var_dump($err['id']);exit('ds21');
                $ret[] = $err;
                echo json_encode($ret);
            }
            
            
        }
    }
 public function addProportionAction(){//add tỷ lệ
     for ($i = 1; $i <= 12; $i++) {
            $months[$i] = $i;
        }
        $this->view->months = $months;
        $this->view->cur_month = date("m");

        for ($i = 2013; $i <= date('Y'); $i++) {
            $years[$i] = $i;
        }
        
//        var_dump(date('m'));exit();
        $this->view->years = $years;
        $this->view->cur_year = date("Y"); 
        $vote = Business_Common_Users::getInstance()->getListByUname();
        foreach ($vote as &$items){
            $items["vote_name"] = Business_Addon_Options::getInstance()->getStoreName($items["userid"]);
        }
        $this->view->vote =  $vote;
        
      $menuname = $this->_request->getParam('menuname','');
//            var_dump($menuname);exit();
            if ($menuname == '' || is_null($menuname)){
                $menuname = $this->_default_menu;
            }
            $this->view->menuname = $menuname;
        $this->view->productstype = $this->_plist;
//        var_dump($this->view->plist);exit();
        if($menuname == '3'){
            $pmenuDT = Business_Helpers_Products::getProductMenu();
        }
        if($menuname == '4'){
            $pmenuDT = Business_Helpers_Products::getAccMenu();
        }
        if($menuname == '5'){
            $pmenuDT = Business_Addon_Options::getInstance()->getTabletMenu();
        }
        $this->view->items = $pmenuDT;
    }
 public function addProportionHnamAction(){//add tỷ lệ
     for ($i = 1; $i <= 12; $i++) {
            $months[$i] = $i;
        }
        $this->view->months = $months;
        $this->view->cur_month = date("m");

        for ($i = 2013; $i <= date('Y'); $i++) {
            $years[$i] = $i;
        }
        
//        var_dump(date('m'));exit();
        $this->view->years = $years;
        $this->view->cur_year = date("Y"); 
        $vote = Business_Common_Users::getInstance()->getListByUname();
        foreach ($vote as &$items){
            $items["vote_name"] = Business_Addon_Options::getInstance()->getStoreName($items["userid"]);
        }
        $this->view->vote =  $vote;
        
      $menuname = $this->_request->getParam('menuname','');
//            var_dump($menuname);exit();
            if ($menuname == '' || is_null($menuname)){
                $menuname = $this->_default_menu;
            }
            $this->view->menuname = $menuname;
        $this->view->productstype = $this->_plist;
//        var_dump($this->view->plist);exit();
        if($menuname == '3'){
            $pmenuDT = Business_Helpers_Products::getProductMenu();
        }
        if($menuname == '4'){
            $pmenuDT = Business_Helpers_Products::getAccMenu();
        }
        if($menuname == '5'){
            $pmenuDT = Business_Addon_Options::getInstance()->getTabletMenu();
        }
        $this->view->items = $pmenuDT;
    }
    public function listProportionHnamAction(){
        for ($i = 1; $i <= 12; $i++) {
            $months[$i] = $i;
        }
        $this->view->months = $months;
        $this->view->cur_month = date("m");

        for ($i = 2013; $i <= date('Y'); $i++) {
            $years[$i] = $i;
        }
        
//        var_dump(date('m'));exit();
        $this->view->years = $years;
        $this->view->cur_year = date("Y"); 
        $vote = Business_Common_Users::getInstance()->getListByUname();
        foreach ($vote as &$items){
            $items["vote_name"] = Business_Addon_Options::getInstance()->getStoreName($items["userid"]);
        }
        $this->view->vote =  $vote;
        
        $vote_id  = $this->_request->getParam("vote-name");
        $this->view->v_id = $vote_id;
        $month_create = $this->_request->getParam("month_create");
        $this->view->months_created= $month_create;
        $year_create = $this->_request->getParam("year_create");
         $list = Business_Addon_BounsHnam::getInstance()->getListByVoteIdDate($vote_id,$month_create,$year_create);
//         var_dump($list);exit();
            foreach ($list as &$items){
                $uid = $items["userid"];
                $list_vote_name = Business_Common_Users::getInstance()->getListById($uid);
                foreach ($list_vote_name as $items2){
                    $items["vote_name"]= Business_Addon_Options::getInstance()->getStoreName($items2["userid"]);
                }
    //            var_dump($uid);exit();
            }
            $this->view->items = $list;
    }

    



    public function saveProportionAction(){ // save tỷ lệ
//        exit('dsad');
        if($this->_request->isPost()){
//            exit('dsadd111a');
            $id = $this->_request->getParam("id");
//            var_dump($id);exit();
             $this->_helper->Layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            
            $cateid = $this->_request->getParam("cated-name");
//            var_dump($cateid);exit();
            $cated_name = $this->_request->getParam("cated_pname");
            $tyle = $this->_request->getParam("tyle");
            $months_created = $this->_request->getParam("month_create");
            $years_created = $this->_request->getParam("year_create");
            $vote_id = $this->_request->getParam("vote-name");
            $productsid = $this->_request->getParam("menuname");
            $list_vote_name = Business_Common_Users::getInstance()->getListById($vote_id);
            foreach ($list_vote_name as $items3){
                $vote_name = Business_Addon_Options::getInstance()->getStoreName($items3["userid"]);
            }
//            var_dump($years_created);exit();
            $cateid_role = Business_Common_DetailsSales::getInstance()->getCateid($cateid,$vote_id,$months_created,$years_created);
//            var_dump($cateid_role);exit();
            $ret = array();
            if ($id == 0) {
                if ($vote_id == '0' ) {
                        $err['id'] = "vote_id";
                        $err['msg'] = "Chọn chi nhánh";
                        $ret[] = $err;
                    }
                if ($cateid == '0' || empty($cateid)) {
                        $err['id'] = "cateid";
                        $err['msg'] = "Chọn sản phẩm";
                        $ret[] = $err;
                    }
                    if($cateid_role != null  && $cateid != null){
                        $err['id'] = "cateid-role";
                        $err['msg'] = "Sản phẩm $cated_name đã được thêm  tháng $months_created/$years_created  \nChi nhánh $vote_name";
                        $ret[] = $err;
                    }
                if ($tyle == null || empty($tyle)) {
                        $err['id'] = "tyle";
                        $err['msg'] = "Tỷ lệ không được để trống";
                        $ret[] = $err;
                    }
            }else{
                if ($tyle == null || empty($tyle)) {
                        $err['id'] = "tyle";
                        $err['msg'] = "Tỷ lệ không được để trống";
                        $ret[] = $err;
                    }
            }
                
           if (count($ret) > 0){
                echo json_encode($ret);
                
            }
            else{
                if($id > 0){
//                    exit('dsad');
//                    $data = Business_Common_CatedProducts::getInstance()->getListById($id);
                }
                else{
                   $data = array(); 
                }
                
                
                $data["value"] = $tyle;
                
                $data["months_created"] = $months_created;
                $data["years_created"] = $years_created;
                $data["user_id"] = $vote_id;
//                var_dump($cated_name);exit();
//                var_dump($data["value"]);exit();
                if($id == '0' || $id == null){
//                    exit('sd123');
                    if($cateid == -1){
                        $menu_id = $this->_request->getParam("menuname");
                        if($menu_id == '3'){
                            $list_menu = Business_Helpers_Products::getProductMenu();
                        }
                        if($menu_id == '4'){
                            $list_menu = Business_Helpers_Products::getAccMenu();
                        }
                        if($menu_id == '5'){
                            $list_menu = Business_Addon_Options::getInstance()->getTabletMenu();
                        }
                        
                        foreach ($list_menu as $items){
                            $title = $items["title"];
                            $itemid = $items["itemid"];
//                            var_dump($itemid);
                            // kiem tra duoc them vao chua
                            $cateid_role_all = Business_Common_DetailsSales::getInstance()->getCateid($itemid,$vote_id,$months_created,$years_created);
                            if($cateid_role_all != null  && $itemid != null){
//                                $err['id'] = "cateid-role_all";
//                                $err['msg'] = "Sản phẩm $title đã được thêm  tháng $months_created/$years_created  \nChi nhánh $vote_name";
//                                $ret[] = $err;
//                                return $ret;
                                return false;
                            }
                            //end kiem tra
                            $data["productsid"] = $productsid;
                            $data["cated_id"] = $itemid;
                            $data["key"] = $title;
                            Business_Common_DetailsSales::getInstance()->insert($data);
                            
                        }
//                        exit();
                    }else{
                        $data["cated_id"] = $cateid;
                        $data["key"] = $cated_name;
                        Business_Common_DetailsSales::getInstance()->insert($data);
                    }
                     
                }
                else{
//                    exit('update');
                    Business_Common_DetailsSales::getInstance()->update($id, $data);
                }
                $err['id'] = "ok";
                $err['msg'] = "ok";
//                var_dump($err['id']);exit('ds21');
                $ret[] = $err;
                echo json_encode($ret);
            }
                
                
            }
    }
    public function saveProportionHnamAction(){ // save tỷ lệ
        if($this->_request->isPost()){
            $id = $this->_request->getParam("id");
             $this->_helper->Layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            $bouns = $this->_request->getParam("bouns");
            $months_created = $this->_request->getParam("month_create");
            $years_created = $this->_request->getParam("year_create");
            $vote_id = $this->_request->getParam("vote-name");
            $list_vote_name = Business_Common_Users::getInstance()->getListById($vote_id);
            foreach ($list_vote_name as $items3){
                $vote_name = Business_Addon_Options::getInstance()->getStoreName($items3["userid"]);
            }
            $bouns_role = Business_Addon_BounsHnam::getInstance()->getBounsVote($vote_id,$months_created,$years_created);
//            var_dump($bouns_role);exit();
            $ret = array();
//            var_dump($id);exit();
            if ($id == 0) {
                if ($vote_id == '0' ) {
                        $err['id'] = "vote_id";
                        $err['msg'] = "Chọn chi nhánh";
                        $ret[] = $err;
                    }
                    if($bouns_role != null){
                        $err['id'] = "bouns_role";
                        $err['msg'] = "Doanh số Hnam này đã được thêm  tháng $months_created/$years_created  \nChi nhánh $vote_name";
                        $ret[] = $err;
                    }
                if ($bouns == null || empty($bouns)) {
                        $err['id'] = "tyle";
                        $err['msg'] = "Tỷ lệ không được để trống";
                        $ret[] = $err;
                    }
            }else{
                if ($bouns == null || empty($bouns)) {
                        $err['id'] = "tyle";
                        $err['msg'] = "Tỷ lệ không được để trống";
                        $ret[] = $err;
                    }
            }
                
           if (count($ret) > 0){
                echo json_encode($ret);
                
            }
            else{
                if($id > 0){
//                    $data = Business_Common_CatedProducts::getInstance()->getListById($id);
                }
                else{
                   $data = array(); 
                }
                $data["userid"] = $vote_id;
                $data["months"] = $months_created;
                $data["years"] = $years_created;
                $data["bouns_hnam"] = $bouns;
//                var_dump($data);exit();
                if($id == 0 || $id == null){
                        Business_Addon_BounsHnam::getInstance()->insert($data);
                }
                else{
                    Business_Addon_BounsHnam::getInstance()->update($id, $data);
                }
                $err['id'] = "ok";
                $err['msg'] = "ok";
                $ret[] = $err;
                echo json_encode($ret);
            }
                
                
            }
    }
    public function listProportionAction(){
         for ($i = 1; $i <= 12; $i++) {
            $months[$i] = $i;
        }
        $this->view->months = $months;
        $this->view->cur_month = date("m");

        for ($i = 2013; $i <= date('Y'); $i++) {
            $years[$i] = $i;
        }
        
//        var_dump(date('m'));exit();
        $this->view->years = $years;
        $this->view->cur_year = date("Y"); 
        $vote = Business_Common_Users::getInstance()->getListByUname();
        foreach ($vote as &$items){
            $items["vote_name"] = Business_Addon_Options::getInstance()->getStoreName($items["userid"]);
        }
        $this->view->vote =  $vote;
        
        $this->view->v_id = $vote_id  = $this->_request->getParam("vote-name");
        $this->view->months_created= $month_create = $this->_request->getParam("month_create");
        $year_create = $this->_request->getParam("year_create");
        $productsid = $this->_request->getParam("menuname");
         $list = Business_Common_DetailsSales::getInstance()->getListByVoteIdDate($vote_id,$month_create,$year_create,$productsid);
//         var_dump($list);exit();
            foreach ($list as &$items){
                $uid = $items["user_id"];
                $list_vote_name = Business_Common_Users::getInstance()->getListById($uid);
                foreach ($list_vote_name as $items2){
                    $items["vote_name"]= Business_Addon_Options::getInstance()->getStoreName($items2["userid"]);
                }
    //            var_dump($uid);exit();
            }
            $this->view->items = $list;
    }
    public function editProportionAction(){
        $id = $this->_request->getParam("id");
        $list = Business_Common_DetailsSales::getInstance()->getListById($id);
        $this->view->items = $list;
        foreach ($list as $items){
            $this->view->key_name = $items["key"];
            $this->view->id = $items["id"];
            $this->view->value = $items["value"];
            $this->view->cate_id = $items["cated_id"];
            $this->view->months_created = $items["months_created"];
            $this->view->years_created = $items["years_created"];
            $this->view->vote_id=$vote_id = $items["user_id"];
            $list_vote_name = Business_Common_Users::getInstance()->getListById($vote_id);
            
            foreach ($list_vote_name as $items2){
                $this->view->vote_name = Business_Addon_Options::getInstance()->getStoreName($items2["userid"]);
            }
        }
    }
    public function editProportionHnamAction(){
        $id = $this->_request->getParam("id");
        $list = Business_Addon_BounsHnam::getInstance()->getDetail($id);
//        var_dump($list);exit();
        $this->view->items = $list;
        foreach ($list as $items){
            $this->view->bouns_hnam = $items["bouns_hnam"];
            $this->view->id = $items["id"];
            $this->view->months = $items["months"];
            $this->view->years = $items["years"];
            $vote_id = $items["userid"];
            $this->view->vote_id=$vote_id;
            $list_vote_name = Business_Common_Users::getInstance()->getListById($vote_id);
            
            foreach ($list_vote_name as $items2){
                $this->view->vote_name = Business_Addon_Options::getInstance()->getStoreName($items2["userid"]);
            }
        }
    }
    public function deleteProportionAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $id = $this->_request->getParam("id");
//        var_dump($id);exit('123');
        Business_Common_DetailsSales::getInstance()->deleteProportionById($id);
    }
    public function deleteProportionHnamAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $id = $this->_request->getParam("id");
//        var_dump($id);exit('123');
        Business_Addon_BounsHnam::getInstance()->deleteProportionById($id);
    }
    public function deleteAllProportionAction($vote_id,$month_create,$year_create,$productsid){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $id = $this->_request->getParam("id");
        $vote_id = $this->_request->getParam("vote-name");
        $productsid = $this->_request->getParam("menuname");
        $month_create = $this->_request->getParam("month_create");
        $year_create = $this->_request->getParam("year_create");
        
//        var_dump($vote_id);exit('123');
        Business_Common_DetailsSales::getInstance()->deleteAllProportionById($vote_id,$month_create,$year_create,$productsid);
        $this->_redirect('/admin/user/sales/list-proportion');
    }

    public function deleteVoteAction($id){
//        exit('dsadsa');
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $id = $this->_request->getParam("id");
//        var_dump($id);exit();
        $item = Business_Common_Users::getInstance()->deleteSalesById($id);
    }
    public function restoreVoteAction($id){
//        exit('dsadsa');
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $id = $this->_request->getParam("id");
        $item = Business_Common_Users::getInstance()->restoreSalesById($id);
    }
}
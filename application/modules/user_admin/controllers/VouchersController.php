<?php

class User_Admin_VouchersController extends Zend_Controller_Action
{
    private $_identity;
    private $_user_business = null;
    private $skey = "ABCHNAM2016D";
    public function init()
    {
        // do something
        BlockManager::setLayout('appbh');
        $auth = Zend_Auth::getInstance();
        $this->_identity = (array) $auth->getIdentity();
    }
    public function required($data){
        $ret = array();
        $arr = array();
        if ($data['note'] == null){
            $arr["id"] = "note";
            $arr["msg"] = "Tên voucher không được để trống !";
            $ret[] = $arr;
        }
        if ($data['code_value'] < 0){
            $arr["id"] = "code_value";
            $arr["msg"] = "Giá phải lớn hơn 0 !";
            $ret[] = $arr;
        }
        if ($data['limit_up'] < 0){
            $arr["id"] = "limit_up";
            $arr["msg"] = "Giá phải lớn hơn 0 !";
            $ret[] = $arr;
        }
        if ($data['limit_down'] < 0){
            $arr["id"] = "limit_down";
            $arr["msg"] = "Giá phải lớn hơn 0 !";
            $ret[] = $arr;
        }
        if((int)$data["total_bill"] >0 && $data["sectionid_by_bill"]==0){
                $arr['id']  = "sectionid_by_bill";
                $arr['msg'] = "Vui lòng chọn chương trình voucher.";
                $ret[] = $arr;
            }
        return $ret;
    }
    public function listAction(){
        $_voucher = Business_Ws_VoucherAdd::getInstance();
        $__option = Business_Addon_Options::getInstance();
        $this->view->skey = $this->skey;
        $this->view->parentid = $this->_identity["parentid"];
        $list_type_ctkm = Business_Addon_Options::getInstance()->get_type_ctkm_voucher();
        $this->view->list_type_ctkm = $list_type_ctkm;
        $i_type = $this->_request->getParam("i_type");
        if($i_type==NULL){
            $i_type =-1;
        }
        $this->view->i_type = $i_type;
        $enabled = 1;
        $idregency = $this->_identity["idregency"];
        if($__option->isBGD($idregency)){
            $enabled=2;
        }
        $list = $_voucher->get_list($i_type,$enabled);
        $this->view->list = $list;
    }
    public function list2Action(){
        $_voucher = Business_Ws_VoucherAdd::getInstance();
        $sectionid = 1;
        $list = $_voucher->getList(1,0,$sectionid);
        $this->view->list = $list;
        $this->view->skey = $this->skey;
        $this->view->parentid = $this->_identity["parentid"];
    }
    public function iframeShowAction(){
        $this->_helper->Layout()->disableLayout();
        $_voucher = Business_Ws_VoucherAdd::getInstance();
        $sectionid = (int)$this->_request->getParam("st");
        $list = $_voucher->getList2(1,0,$sectionid);
        $this->view->list = $list;
        $this->view->skey = $this->skey;
        $this->view->parentid = $this->_identity["parentid"];
        $phone = $this->_request->getParam("phone");
        $this->view->phone = $phone;
    }
    public function checkPhoneHvip2Action(){
        $phone = $this->_request->getParam("sphone");
        $this->view->sphone = $phone;
        if ($this->_request->isPost()) {
            $ret = array();
            $err = array();
            $goal = Business_Addon_UsersProducts::getInstance()->countGoalByPhone($phone);
            $total = number_format($goal["sum"]);
            if((int)$goal["sum"] < 5000000){ // H-VIP
                $err["id"] = "phone";
                $err["msg"] = "Bạn chưa đủ điểm tích lũy để sử dụng H-VIP và H-MEMBER. H-VIP/HVIP có tích lũy từ 5.000.000 đến 40.000.000. Tích lũy của bạn là ".number_format($goal["sum"]);
                $ret[] = $err;
            }
            
            if(count($ret) >0){
                for($i=0;$i<count($ret);$i++){
                    $msg = $ret[$i]['msg'];
                    $ids = $ret[$i]['id'];
                    echo "<script>window.parent.show_msg('$msg','$ids');</script>";
                    return;
                }
            }else{
                if((int)$goal["sum"] >5000000 && (int)$goal["sum"] < 40000000){ // HMEMBER
                    $st = 2;
                    $type_name ='H-MEMBER';
                }else{ // HVIP
                   $st = 1;
                   $type_name ='H-VIP';
                }
                echo "<script>window.parent.show_voucher_hvip($st,'$total','$type_name');</script>";
                return;
            }
        }
    }
    public function checkPhoneHvipAction(){
        $phone = $this->_request->getParam("sphone");
        $this->view->sphone = $phone;
        if ($this->_request->isPost()) {
            $ret = array();
            $err = array();
            $goal = Business_Addon_UsersProducts::getInstance()->countGoalByPhone($phone);
            $total = number_format($goal["sum"]);
            if((int)$goal["sum"] < 5000000){ // H-VIP
                $err["id"] = "phone";
                $err["msg"] = "Bạn chưa đủ điểm tích lũy để sử dụng H-VIP và H-MEMBER. H-VIP/HVIP có tích lũy từ 5.000.000 đến 40.000.000. Tích lũy của bạn là ".number_format($goal["sum"]);
                $ret[] = $err;
            }
            
            if(count($ret) >0){
                for($i=0;$i<count($ret);$i++){
                    $msg = $ret[$i]['msg'];
                    $ids = $ret[$i]['id'];
                    echo "<script>window.parent.show_msg('$msg','$ids');</script>";
                    return;
                }
            }else{
                if((int)$goal["sum"] >5000000 && (int)$goal["sum"] < 40000000){ // HMEMBER
                    $st = 2;
                    $type_name ='H-MEMBER';
                }else{ // HVIP
                   $st = 1;
                   $type_name ='H-VIP';
                }
                echo "<script>window.parent.show_voucher_hvip($st,'$total','$type_name');</script>";
                return;
            }
        }
    }
    

    public function addAction(){
        
        $_option = Business_Addon_Options::getInstance();
        $list_cate = $_option->getCatedType();
        $this->view->list_cate = $list_cate;
        $_voucher = Business_Ws_VoucherAdd::getInstance();
        $list = $_voucher->getList();
        $this->view->list = $list;
        $km = $_option->getPromotionName();
        $this->view->km = $km;
        $group_sectionid = Business_Addon_Voucher::getInstance()->get_group_sectionid();
        $this->view->group_sectionid = $group_sectionid;
        $list_productsid = $_option->getCatedHnam();
        $this->view->list_productsid = $list_productsid;
        $this->view->cty_hnam = $_option->getCatedHnammobile();
        $this->view->lis_istype = $_option->getIsType();
        $__ctkm = Business_Addon_Ctkm::getInstance();
        $list_ctkm = $__ctkm->getList();
        $this->view->list_ctkm = $list_ctkm;
        $__option = Business_Addon_Options::getInstance();
        $list_type_ctkm = Business_Addon_Options::getInstance()->get_type_ctkm_voucher();
        $this->view->list_type_ctkm = $list_type_ctkm;
        $idregency = $this->_identity["idregency"];
        $bgd = 0;
        if($__option->isBGD($idregency)){
            $bgd=1;
        }else{
            $this->_helper->viewRenderer('add2');
        }
        $this->view->bgd = $bgd;
        if ($this->_request->isPost()) {
            $data_frm = $this->_request->getParams('data_frm');
            $data = array();
            $data_frm["itemid"];
            $data['itemid'] = $data_frm["itemid"];
            $data['itemid_tmp'] = $data_frm["itemid_tmp"];
            $data['code_type'] = (int)$data_frm["code_type"];
            if((int)$data_frm["code_type"] ==1){ // giảm tiền
                $code_value = str_replace(",", "", $data_frm["code_value"]);
                $code_value = str_replace(".", "", $data_frm["code_value"]);
            }
            if((int)$data_frm["code_type"] ==2){ // giảm %
                $code_value = str_replace(",", ".", $data_frm["code_value"]);
            }
            $data['code_value'] = $code_value;
            $data['code_value_tmp'] = $code_value;
            
            $data['code_expired'] = $data_frm["code_expired"];
            $data['tiento'] = $data_frm["tiento"];
            $data['note'] = $data_frm["note"];
            $data['type_ctkm'] = $data_frm["type_ctkm"];
            $data['cateid'] = $data_frm["cateid"];
            $data['sectionid'] = 0;
            $data['limit_up'] = (int)$data_frm["limit_up"];
            $data['limit_down'] = (int)$data_frm["limit_down"];
            $data['sms'] = $data_frm["sms"];
            $data['enabled'] = (int)$data_frm["enabled"];
            $data["creator"] = $this->_identity["username"];
            $data["datetime"] = date('Y-m-d H:i:s');
            $id_ctkm = implode(",", $data_frm["id_ctkm"]);
            if($id_ctkm == NULL){
                $id_ctkm =0;
            }
            $data["id_ctkm"] = $id_ctkm;
            
            $productsid = implode(",", $data_frm["productsid"]);
            if($productsid == NULL){
                $productsid =0;
            }
            $data["productsid"] = $productsid;
            $istype = implode(",", $data_frm["istype"]);
            if($istype ==NULL){
                $istype =0;
            }
            $data["istype"] = $istype;
            $data["flag"] = (int)$data_frm["flag"];
            $nokm = implode(",", $data_frm["nokm"]);
            if($nokm ==NULL){
               $nokm =-1; 
            }
            $data["nokm"] = $nokm;
            $data["is_special"] = (int)$data_frm["is_special"];
            
            if((int)$data_frm["nb_used"] ==0){
                $data_frm["nb_used"] = 1;
            }
            $data["number_used"] = (int)$data_frm["number_used"];
            $data["nb_used"] = (int)$data_frm["nb_used"];
            $data["total_bill"] = (int)$data_frm["total_bill"];
            $data["sectionid_by_bill"] = (int)$data_frm["sectionid_by_bill"];
            $ret = $this->required($data);
            
            if(count($ret) >0){
                for($i=0;$i<count($ret);$i++){
                    $msg = $ret[$i]['msg'];
                    $ids = $ret[$i]['id'];
                    echo "<script>window.parent.notifications('$msg','$ids');</script>";
                    return;
                }
            }else{
                $_voucher->insert($data, 'ws_vouchers_add');
                echo "<script>window.parent.completes('LƯU THÀNH CÔNG');</script>";
                return;
            }
        }
    }

    public function editAction() {
        $id = $this->_request->getParam('id');
        $_vouchers = Business_Ws_VoucherAdd::getInstance();
        $this->view->item = $_vouchers->get_detail_by_id($id);

        $list_type_ctkm = Business_Addon_Options::getInstance()->get_type_ctkm_voucher();
        $this->view->list_type_ctkm = $list_type_ctkm;

        $_option = Business_Addon_Options::getInstance();
        $list_cate = $_option->getCatedType();
        $this->view->list_cate = $list_cate;
        $list_productsid = $_option->getCatedHnam();
        $this->view->list_productsid = $list_productsid;
        $this->view->lis_istype = $_option->getIsType();

        $__ctkm = Business_Addon_Ctkm::getInstance();
        $list_ctkm = $__ctkm->getList();
        $this->view->list_ctkm = $list_ctkm;

        $km = $_option->getPromotionName();
        $this->view->km = $km;
        if ($this->_request->isPost()) {
            $data_frm = $this->_request->getParams('data_frm');
            unset($data_frm['controller']);
            unset($data_frm['action']);
            unset($data_frm['module']);
            unset($data_frm['id']);
            $data_frm['id_ctkm'] = implode(',',$data_frm['id_ctkm']);
            $data_frm['nokm'] = implode(',',$data_frm['nokm']);
            $result = $_vouchers->update($id,$data_frm);
            //var_dump($result);
        }
    }

    public function delAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $id = $this->_request->getParam("id");
        $data["enabled"] = $this->_request->getParam("enabled");
        $data["end_creator"] = $this->_identity["username"];
        $data["end_datetime"] = date('Y-m-d H:i:s');
        $_voucher = Business_Ws_VoucherAdd::getInstance();
        $_voucher->update($id, $data);
    }

    public function getvoucherbybillAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $_option = Business_Addon_Options::getInstance();
        $_vouchers = Business_Ws_VoucherAdd::getInstance();
        
        $___voucher = Business_Addon_Voucher::getInstance();
        $id = $this->_request->getParam('id', 0);
        $id_addon_user = (int)$this->_request->getParam('id_addon_user', 0);
        $detal = $_vouchers->getVouchers($id, 'ws_vouchers_add');
        $ztoken = md5($this->skey . $id.$this->_identity["parentid"]);
        $this->view->vouchers = $detal['note'];
        $token = $this->_request->getParam("token");
        $ret = array();
        $err = array();
        if ($token != $ztoken) {
            $err['id']  = "id_addon_user";
            $err['msg'] = "Bạn không có quyền truy cập";
            $ret[] = $err;
        }
        if($detal["enabled"] ==0){
            $err['id']  = "id_addon_user";
            $err['msg'] = "Chương trình này đã hết hạn.";
            $ret[] = $err;
        }
        if($detal["total_bill"] ==0){
            $err['id']  = "id_addon_user";
            $err['msg'] = "Bạn không có quyền truy cập.";
            $ret[] = $err;
        }
        if($id_addon_user ==0){
            $err['id']  = "id_addon_user";
            $err['msg'] = "Vui lòng nhập mã đơn hàng..";
            $ret[] = $err; 
        }
        $start ='2016-06-11 00:00:00';
        $goal = Business_Addon_UsersProducts::getInstance()->countGoalByPhone("",$id_addon_user,$start);
        $detail_voucher_by_bill = $___voucher->getListByBill($id_addon_user);
        
        if($detail_voucher_by_bill != NULL){
            $err['id']  = "id_addon_user";
            $err['msg'] = "Đơn hàng này đã được cấp mã voucher.Xin kiểm tra lại.";
            $ret[] = $err; 
        }
        if($goal['sum'] ==0){
            $err['id']  = "id_addon_user";
            $err['msg'] = "Đơn hàng này không có thực hoặc trước ngày 11/06/2016. vui lòng thử lại";
            $ret[] = $err;
        }
        if(strtotime($detal["code_expired"]) >0){
                if(strtotime('now')>strtotime($detal["code_expired"])){
                    $err['id']  = "phone";
                    $err['msg'] = "Chương trình này đã hết hạn.";
                    $ret[] = $err;
                }
            }
        if($goal['sum'] < $detal["total_bill"]){
            $err['id']  = "id_addon_user";
            $err['msg'] = "Chương trình này chỉ áp dụng cho tổng đơn hàng trên ".  number_format($detal["total_bill"]). ". Tổng giá trị đơn hàng của bạn là ".number_format($goal['sum']);
            $ret[] = $err;
        }
        if(count($ret) >0){
            for($i=0;$i<count($ret);$i++){
                $msg = $ret[$i]['msg'];
                $ids = $ret[$i]['id'];
                echo "<script>window.parent.show_msg_alert('$msg','$ids');</script>";
                return;
            }
        }else{
            $total = (int)($goal['sum']/$detal["total_bill"]);
            
            $asus =0;
            $list_products = Business_Addon_UsersProducts::getInstance()->getListByBillId($id_addon_user);
            foreach ($list_products as $__item){
                if($__item["cated_id"] ==490){ // asus
                    $asus = 1;
                }
                $phone = $__item["phone_addon"];
            }
            if($asus ==1){
                $total = $total*2;
            }
            $sectionid =$detal["sectionid_by_bill"];
            $list_voucher = $___voucher->get_random_voucher($sectionid,$sstotal=1);
            foreach ($list_voucher as $items){
                $code_id[] = $items["code_id"];
                $code_name[]= $items["code_name"];
            }
            if($code_id != NULL){
                $str_codeid = implode(",", $code_id);
                $code = implode(",", $code_name);
                $username = $this->_identity["username"];
                $query = "update ws_vouchers set id_addon_user = $id_addon_user,code_publish =1,phone='$phone',creator='$username' where code_id IN ($str_codeid)";
                $___voucher->excute($query);
                
                $this->send_voucher($detal, $phone, $code);
            }
        }
    }
    public function getvoucherbybill2Action(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $_option = Business_Addon_Options::getInstance();
        $_vouchers = Business_Ws_VoucherAdd::getInstance();
        
        $___voucher = Business_Addon_Voucher::getInstance();
        $id = $this->_request->getParam('id', 0);
        $id_addon_user = (int)$this->_request->getParam('id_addon_user', 0);
//        $detal = $_vouchers->getVouchers($id, 'ws_vouchers_add');
        $detal = $_vouchers->get_detail_by_id($id);
        $__itemid = $detal["itemid"];
        $ztoken = md5($this->skey . $id.$this->_identity["parentid"]);
        $this->view->vouchers = $detal['note'];
        $token = $this->_request->getParam("token");
        $ret = array();
        $err = array();
        if ($token != $ztoken) {
            $err['id']  = "id_addon_user";
            $err['msg'] = "Bạn không có quyền truy cập";
            $ret[] = $err;
        }
        if($detal["enabled"] ==0){
            $err['id']  = "id_addon_user";
            $err['msg'] = "Chương trình này đã hết hạn.";
            $ret[] = $err;
        }
        
        if($id_addon_user ==0){
            $err['id']  = "id_addon_user";
            $err['msg'] = "Vui lòng nhập mã đơn hàng..";
            $ret[] = $err; 
        }
        $start ='2016-08-27 00:00:00';
        $goal = Business_Addon_UsersProducts::getInstance()->countGoalByPhone2("",$id_addon_user,$start,$__itemid);
        $detail_voucher_by_bill = $___voucher->getListByBill($id_addon_user);
        
        if($detail_voucher_by_bill != NULL){
            $err['id']  = "id_addon_user";
            $err['msg'] = "Đơn hàng này đã được cấp mã voucher.Xin kiểm tra lại.";
            $ret[] = $err; 
        }
        if($goal['sum'] ==0){
            $err['id']  = "id_addon_user";
            $err['msg'] = "Đơn hàng này không có thực hoặc trước ngày 27/08/2016. vui lòng thử lại";
            $ret[] = $err;
        }
        if(strtotime($detal["code_expired"]) >0){
                if(strtotime('now')>strtotime($detal["code_expired"])){
                    $err['id']  = "phone";
                    $err['msg'] = "Chương trình này đã hết hạn.";
                    $ret[] = $err;
                }
            }
        if($goal['sum'] < $detal["limit_up"]){
            $err['id']  = "id_addon_user";
            $err['msg'] = "Chương trình này chỉ áp dụng cho tổng đơn hàng trên ".  number_format($detal["limit_up"]). ". Tổng giá trị đơn hàng của bạn là ".number_format($goal['sum']);
            $ret[] = $err;
        }
        if(count($ret) >0){
            for($i=0;$i<count($ret);$i++){
                $msg = $ret[$i]['msg'];
                $ids = $ret[$i]['id'];
                echo "<script>window.parent.show_msg_alert('$msg','$ids');</script>";
                return;
            }
        }else{
            $tiento = $detal["tiento"];
            if($tiento ==NULL){
                $tiento = "CT-";
            }
            
            
            $code = "$tiento".$_vouchers->randomCode("0123456789", 6, 1);
            $data['code_name'] = $this->check_code_name($tiento,$code);
            $data['itemid'] = $detal['itemid'];
            if($detal["itemid_tmp"] != NULL){
                $data['itemid'] = $detal['itemid_tmp'];
            }
            $data['code_publish'] = 0;
            $data['code_value'] = $detal['code_value'];
            $data['code_value_tmp'] = $detal['code_value'];
            $data['code_type'] = $detal['code_type'];
            $data['type_ctkm'] = (int)$detal['type_ctkm'];
            $data['code_get'] = 0;
            $data['code_store'] = 0;
            $data['code_created'] = date('Y-m-d H:i:s');
            $data['code_updated'] = date('Y-m-d H:i:s');
            $data['code_expired'] = date('Y-m-d 23:59:59');
            
            $data['customer_id'] = 0;
            $data['note'] = $detal['note'];
            $data['used'] = 0;
            $data['sectionid'] = $detal['id'];
            $data['limit_up'] = 0;
            $data['limit_down'] = 0;
            $data['phone'] = 0;
            $data["email"] = '';

            $data["productsid"] = $detal["productsid"];
            $data["istype"] = $detal["istype"];
            $data["nokm"] = $detal["nokm"];
            $data["number_used"] = $detal["nb_used"];
            $data["creator"] = $this->_identity["username"];

            $data["flag"] = $detal["flag"];
            $data["id_ctkm"] = $detal["id_ctkm"];
            $data["is_special"] = $detal["is_special"];
            $data["id_addon_user"] = $id_addon_user;

            $data["typevoucher"] = 0;
            Business_Addon_Voucher::getInstance()->insert($data);
            echo "<script>window.parent.show_voucher('Mã voucher  là : $code');</script>";
            return;
        }
    }
    public function getvoucherbybillsssAction(){
        $this->_helper->Layout()->disableLayout();
        $_vouchers = Business_Ws_VoucherAdd::getInstance();
        $___voucher = Business_Addon_Voucher::getInstance();
        $id = $this->_request->getParam('id', 0);
        $id_addon_user = (int)$this->_request->getParam('id_addon_user', 0);
        $detal = $_vouchers->get_detail_by_id($id);
        $__cateid = $detal["cateid"];
        $__itemid = $detal["itemid"];
        $__arr_id = array();
        if($__itemid != NULL){
            $__arr_id = explode(",", $__itemid);
        }
        $__arr_cateid = array();
        if($__cateid != NULL){
            $__arr_cateid = explode(",", $__cateid);
        }
        
        $ztoken = md5($this->skey . $id.$this->_identity["parentid"]);
        $this->view->vouchers = $detal['note'];
        $token = $this->_request->getParam("token");
        $ret = array();
        $err = array();
        if ($token != $ztoken) {
            $err['id']  = "id_addon_user";
            $err['msg'] = "Bạn không có quyền truy cập";
            $ret[] = $err;
        }
        if((int)$detal["enabled"] ==0){
            $err['id']  = "id_addon_user";
            $err['msg'] = "Chương trình này đã hết hạn.";
            $ret[] = $err;
        }
        
        if($id_addon_user ==0){
            $err['id']  = "id_addon_user";
            $err['msg'] = "Vui lòng nhập mã đơn hàng..";
            $ret[] = $err; 
        }
        if($detal["id"]==197){
            $note_school = $this->_request->getParam("note_school");
            $note_mssv = $this->_request->getParam("note_mssv");
            if($note_school==NULL){
                $err['id']  = "note_school";
                $err['msg'] = "Vui lòng nhập tên trường đại học..";
                $ret[] = $err; 
            }else{
                if(strlen($note_school) <6){
                    $err['id']  = "note_school";
                    $err['msg'] = "Nhập tên trường đại học đầy đủ..";
                    $ret[] = $err; 
                }
            }
            if($note_mssv==NULL){
                $err['id']  = "note_mssv";
                $err['msg'] = "Vui lòng nhập mã số sinh viên..";
                $ret[] = $err; 
            }else{
                if(strlen($note_school) <4){
                    $err['id']  = "note_mssv";
                    $err['msg'] = "Nhập mssv đầy đủ..";
                    $ret[] = $err; 
                }
            }
            $__option = Business_Addon_Options::getInstance();
            $path = $__option->getPath('voucher');
            $expensions = $expensions= array("jpeg","jpg","png","gif");
            $__file = $_FILES['file_upload'];
            $check_upload = $__option->upload($path, $expensions, $__file);
            if($check_upload[0]["msg"] != "ok"){
                foreach ($check_upload as $ch){
                    $ret[] = $ch;
                }
            }else{
                $file_upload = $check_upload[0]["name_files"];
            }
            if($file_upload == NULL){
                $err["id"] ="file_upload";
                $err["msg"] ="Vui lòng upload file hình.";
                $ret[] = $err;
            }
            
        }
        $data["note_school"] = $note_school;
        $data["note_mssv"] = $note_mssv;
        $data["file_upload"] = $file_upload;
        $nid = array();
        $n__cateid = array();
        $sim3g=0;
        $is_apple=0;
        $code_value = $detal['code_value'];
        $check_maycu=0;
        $check_dt=0;
        $storeids = $this->_identity["parentid"];
        if($id_addon_user >0){
            $list = Business_Addon_UsersProducts::getInstance()->get_list_by_id_addon_user($id_addon_user);
            foreach ($list as $val){
                $__date = $val["create_date"];
                if(in_array($val["products_id"], $__arr_id)){
                    $nid[] = $val["products_id"];
                }
                if(in_array($val["cated_id"], $__arr_cateid)){
                    $n__cateid[] = $val["cated_id"];
                }
                $phone = $val["phone_addon"];
                if($val["products_id"] == 6138){
                    $sim3g =1;
                }
                
                if($val["is_apple"]==1){
                    $is_apple=1;
                }
                if($val["cated_id"]==53){
                    $check_maycu=1;
                }
                if($val["productsid"]==3){
                    $check_dt=1;
                }
            }
            $arr_sim3g = array();
            if($detal["id"]==197){
                if($is_apple==0){
                    $err['id']  = "id_addon_user";
                    $err['msg'] = "Voucher này chỉ áp dụng cho đơn hàng có iphone 99%. Vui lòng kiểm tra lại nhé.";
                    $ret[] = $err;
                }
            }
            if($detal["id"]==277){ // mua điện thoại online và mua máy cũ
                if($storeids==167){
                    if($check_dt==0){
                        $err['id']  = "id_addon_user";
                        $err['msg'] = "Voucher này chỉ áp dụng cho mua điện thoại online. Vui lòng kiểm tra lại nhé.";
                        $ret[] = $err;
                    }
                }else{
                   if($check_maycu==0){
                        $err['id']  = "id_addon_user";
                        $err['msg'] = "Voucher này chỉ áp dụng cho máy cũ. Vui lòng kiểm tra lại nhé.";
                        $ret[] = $err;
                    } 
                }
                
            }
            if($detal["type_ctkm"]==7){ //sim
                $list_sim3g = Business_Addon_AddonPromotion::getInstance()->get_list_by_3g($detal["itemid"],$id_addon_user);
                foreach ($list_sim3g as $sim){
                    $arr_sim3g[] = $sim["autoid"];
                }
                if($sim3g==0){
                    $err['id']  = "id_addon_user";
                    $err['msg'] = "Đơn hàng này không có bán sim 3g. Vui lòng kiểm tra lại nhé.";
                    $ret[] = $err;
                }else{
                    if($arr_sim3g == NULL){
                        $err['id']  = "id_addon_user";
                        $err['msg'] = "Đơn hàng này không có tặng sim 3g. Vui lòng kiểm tra lại nhé.";
                        $ret[] = $err;
                    }
                }
            }
            if($list ==NULL){
                $err['id']  = "id_addon_user";
                $err['msg'] = "Mã đơn hàng này không có thực. Vui lòng kiểm tra lại.";
                $ret[] = $err;
            }
//            if($detal["datetime"] != NULL && $detal["datetime"] != "0000-00-00 00:00:00" && strtotime($__date) < strtotime($detal["datetime"])){
//                $err['id']  = "id_addon_user";
//                $err['msg'] = "Voucher này chưa đến thời gian áp dụng.";
//                $ret[] = $err;
//            }
            if($detal["type_ctkm"] !=277){
                if($nid ==NULL && $n__cateid ==NULL){ 
                    $err['id']  = "id_addon_user";
                    $err['msg'] = "Đơn hàng này không có sản phẩm như chương trình khuyến mãi1. Vui lòng kiểm tra lại.";
                    $ret[] = $err;
                }else{
                    $nids = array_unique($nid);
                    $___itemid = $detal['itemid'];
                    
                    $arr_itemid = explode(",", $___itemid);
                    $__count1 = count($arr_itemid);
                    
                    foreach ($arr_itemid as $vals){
                        if(in_array($vals, $nids)){
                            $__count2++;
                        }
                    }
                    
                    
                    $nids3 = array_unique($n__cateid);
                    foreach ($__arr_cateid as $vals){
                        if(in_array($vals, $nids3)){
                            $__count4++;
                        }
                    }
                    $er1=0;
                    $er2=0;
                    if($detal["type_ctkm"]==6){
                        if($__count4 >0){
                            $er1=1;
                        }
                        if($__count2 >0){
                            $er2=1;
                        }
                        if($er1 ==0 && $er2==0){
                            $err['id']  = "id_addon_user";
                            $err['msg'] = "Đơn hàng này không có sản phẩm như chương trình khuyến mãi2. Vui lòng kiểm tra lại.";
                            $ret[] = $err;
                        }
                        
                    }else{
                        if($__count1 != $__count2){ // so sánh các phần tử trong đơn hàng và voucher
                            $err['id']  = "id_addon_user";
                            $err['msg'] = "Đơn hàng này không áp dụng chương trình này. Vui lòng kiểm tra lại.";
                            $ret[] = $err;
                        }
                    }
                    $__number = count($nids);
                    $___itemid_tmp = $detal['itemid_tmp'];
                    if($___itemid_tmp != NULL && $___itemid_tmp!=0){
                        $___itemid_tmp2 = explode(",", $___itemid_tmp);
                        foreach ($___itemid_tmp2 as $tmp2){
                            if($tmp2>0){
                                $array_itemid_tmp2[] = $tmp2;
                            }
                        }
                        $__number = count($array_itemid_tmp2);
                    }
                    if($detal["type_ctkm"]==5){
                        $code_value = $detal['code_value']/$__number;
                    }
                }
            }
            
        }
        
        if($detal["code_expired"]!=NULL && $detal["code_expired"]!="0000-00-00 00:00:00"){
            if(strtotime('now')>strtotime($detal["code_expired"])){
                $err['id']  = "phone";
                $err['msg'] = "Chương trình này đã hết hạn.";
                $ret[] = $err;
            }
        }
        
        if(count($ret) >0){
            for($i=0;$i<count($ret);$i++){
                $msg = $ret[$i]['msg'];
                $ids = $ret[$i]['id'];
                echo "<script>window.parent.show_msg_alert('$msg','$ids');</script>";
                return;
            }
        }else{
            $slist = $___voucher->get_list_by_id_addon_user($id_addon_user,$id);
            if($slist ==NULL){
//                if($nid != NULL){
                    $data = $this->get_data_voucher($detal, $id_addon_user, $code_value);
                    $data["itemid"] = $detal["itemid"];
                    if($detal["itemid_tmp"] != NULL && $detal["itemid_tmp"]!=0){
                        $data["itemid"] = $detal["itemid_tmp"];
                    }
                    if($detal["type_ctkm"]==4 || $detal["type_ctkm"]==7){
                        $code_name = $data["code_name"];
                        $___voucher->insert($data);
                    }
                    if($detal["type_ctkm"]==5 || $detal["type_ctkm"]==6){ 
                        $number_used = count($nid);
                        if($number_used==0){
                            $number_used=1;
                        }
                        $code_name = $data["code_name"];
                        if($detal["type_ctkm"]==6){ // chuyển itemid sử dụng thành itemid
                            $data["number_used"] =$detal["number_used"];
                        }else{
                            $data["number_used"] = $number_used;
                        }
                        $___voucher->insert($data);    
                    }
//                    echo "<pre>";
//                        var_dump($data,$phone,$code_name);
//                        die();
                    if($detal["sms"] != NULL){
                        $this->send_voucher($detal,$phone,$code_name);
                    }else{
                        $slist = $___voucher->get_list_by_id_addon_user($id_addon_user,$id);
                    }
                    
//                }
            }
            if($slist[0]["sms"]== NULL){
                $this->view->slist = $slist;
            }else{
                $tb ="Mã voucher $code_name đã gửi đến số điện thoại $phone. Vui lòng kiểm tra tin nhắn.";
                echo $tb;
                die();
            }
            
        }
    }
    
    public function get_data_voucher($detal,$id_addon_user,$code_value){
        $_vouchers = Business_Ws_VoucherAdd::getInstance();
        $__option = Business_Addon_Options::getInstance();
        $tiento = $detal["tiento"];
        if($tiento ==NULL){
            $tiento ="CT";
            if((int)$detal['type_ctkm']==4){
                $tiento ="UUDAI";
            }
            if((int)$detal['type_ctkm']==5){
                $tiento ="COMBO";
            }
            if((int)$detal['type_ctkm']==7){
                $tiento ="SIM3G";
            }
            if((int)$detal['id']==133){
                $tiento ="VCGGPK";
            }
            
        }
        $code = $tiento."-".$_vouchers->randomCode("0123456789", 6, 1);
        $data['code_name'] = $this->check_code_name($tiento,$code);
        $data['itemid'] = $detal['itemid'];
        $data['sms'] = $detal['sms'];
        $data['code_publish'] = 0;
        $data['code_value'] = $code_value;
        $data['code_value_tmp'] = $code_value;
        $data['code_type'] = $detal['code_type'];
        $data['type_ctkm'] = (int)$detal['type_ctkm'];
        $data['code_get'] = 0;
        $data['code_store'] = 0;
        $data['code_created'] = date('Y-m-d H:i:s');
        $data['code_updated'] = date('Y-m-d H:i:s');
        if($detal["code_expired"] !=null && $detal["code_expired"] !="0000-00-00 00:00:00" ){
            $data['code_expired'] = $detal["code_expired"];
        }else{
            $data['code_expired'] = date('Y-m-d 23:59:59');
        }
        
        $data['customer_id'] = 0;
        $data['note'] = $detal['note'];
        $data['used'] = 0;
        $data['sectionid'] = $detal['id'];
        $data['limit_up'] = 0;
        $data['limit_down'] = 0;
        
        $detail_users_products = Business_Addon_UsersProducts::getInstance()->get_detail_by_idaddonuser($id_addon_user);
        $data['phone'] = $detail_users_products["phone_addon"];
        $data["email"] = '';

        $data["productsid"] = $detal["productsid"];
        $data["istype"] = $detal["istype"];
        $data["nokm"] = $detal["nokm"];
        $data["number_used"] = $detal["nb_used"];
        $data["creator"] = $this->_identity["username"];

        $data["flag"] = $detal["flag"];
        $data["id_ctkm"] = $detal["id_ctkm"];
        $data["is_special"] = $detal["is_special"];
        $data["id_addon_user"] = $id_addon_user;

        $data["typevoucher"] = 0;
        $data["access_phone"] = (int)$detal["access_phone"];
        if((int)$detal['type_ctkm']==4){
            $data["access_phone"] = 1;
        }
        if($detal["itemid_tmp"] != NULL){
            $data["itemid"] = $detal["itemid_tmp"];
        }
        return $data;
    }

        public function send_voucher($detal,$phone,$code){
        try {
            $_option = Business_Addon_Options::getInstance();
            $ct = $detal["note"];
            if($detal['sms'] ==NULL){
                echo "<script>window.parent.show_voucher('Mã voucher $ct là : $code');</script>";
                return;
            }else{

                // replace VOUCHERHNAM2016
                $content = str_replace('VOUCHERHNAM2016', $code, $detal['sms']);
                $c = $_option->vn_str_filter($content);
                $token_smk = md5($phone."HNAMSACO2016");
                $c = urlencode($c);
                $url_mobile = "http://www.hnammobile.com/sms/iphone-sacom?token=$token_smk&phone=$phone&content=$c";
                
                Business_Common_Utils::getContentByCurl($url_mobile);
                $_msg ="Mã voucher $ct đã gửi đến số điện thoại. Vui lòng kiểm tra tin nhắn.";
//                echo $_msg;
//                die();
                echo "<script>window.parent.show_success('$_msg','','/admin/user/vouchers/list');</script>";
                return;
            }
        } catch (Exception $exc) {
            echo 'send_voucher';
            echo "<pre>";
            var_dump($detal,$phone,$code);
            die();
            echo $exc->getTraceAsString();
        }
    }

    public function getVouchersAction()
    {
        $__option = Business_Addon_Options::getInstance();
        $this->_helper->getHelper('layout')->disableLayout();
        // get vouchers
        $_vouchers = Business_Ws_VoucherAdd::getInstance();
        $id = $this->_request->getParam('id', 0);
        
        $detal = $_vouchers->getVouchers($id, 'ws_vouchers_add');
        $token = $this->_request->getParam("token");
        $ztoken = md5($this->skey . $id.$this->_identity["parentid"]);
        $this->view->vouchers = $detal['note'];
        $this->view->token =$token;
        $this->view->id =$id;
        $phone = $this->_request->getParam("phone",0);
        $this->view->phone = $phone;
        if($detal["total_bill"] >0){
            $this->_helper->viewRenderer('get-vouchers-by-bill');
        }
        if($detal["type_ctkm"] ==3){
            $this->_helper->viewRenderer('get-vouchers-by-bill2');
        }
        if($detal["type_ctkm"] ==4 || $detal["type_ctkm"] ==5 || $detal["type_ctkm"] ==6  || $detal["type_ctkm"] ==7){ // mã bill + combo
            $this->_helper->viewRenderer('get-vouchers-by-bill3');
        }
        if($detal["id"]==133 || $detal["id"]==134){ // voucher giảm 6 12% phụ kiện
            $this->_helper->viewRenderer('get-vouchers-by-6-12-phukien');
        }
        if($detal["id"]==135){ // voucher preorder s8 phụ kiện
            $this->_helper->viewRenderer('get-vouchers-by-phone');
        }
        if($detal["id"]==141){ // voucher 500k thu may cũ
            $this->_helper->viewRenderer('get-vouchers-thu-may-cu');
        }
        if($detal["id"]==197){ // voucher 500k thu may cũ
            $this->_helper->viewRenderer('get-vouchers-sv');
        }
        if ($token != $ztoken) {
            die('No access');
        }
        $idregency = $this->_identity["idregency"];
        if(!$__option->isBGD($idregency)){
            if($detal["enabled"] ==0){
                die('No access');
            }
        }
        
    }
    
    public function voucherthumaycuAction(){
        $this->_helper->Layout()->disableLayout();
        $_vouchers = Business_Ws_VoucherAdd::getInstance();
        $___voucher = Business_Addon_Voucher::getInstance();
        $__productItem = Business_Ws_ProductsItem::getInstance();
        $storeid = $this->_identity["parentid"];
        $id = (int)$this->_request->getParam('id');
        $id_addon_user = (int)$this->_request->getParam('id_addon_user');
        $detal = $_vouchers->get_detail_by_id($id);
        $token = $this->_request->getParam("token");
        $ztoken = md5($this->skey . $id.$this->_identity["parentid"]);
        $ret = array();
        $err = array();
        if ($token != $ztoken) {
            $err['id']  = "id_addon_user";
            $err['msg'] = "Bạn không có quyền truy cập";
            $ret[] = $err;
        }
        if((int)$detal["enabled"] ==0){
            $err['id']  = "id_addon_user";
            $err['msg'] = "Chương trình này đã hết hạn.";
            $ret[] = $err;
        }
        
        if($id_addon_user ==0){
            $err['id']  = "id_addon_user";
            $err['msg'] = "Vui lòng nhập mã đơn hàng..";
            $ret[] = $err; 
        }
        
        
        $start ='2017-02-06';
        $end ='2017-02-11';
        if($id_addon_user >0){
            $detail_hdmh = Business_Addon_Purchase::getInstance()->get_detail_by_productsid($id_addon_user,$storeid,$type=3,$start,$end);
        }
        if($detail_hdmh ==NULL){
            $err['id']  = "id_addon_user";
            $err['msg'] = "Mã đơn hàng này không có thực. Vui lòng kiểm tra lại.";
            $ret[] = $err;
        }else{
            if($detail_hdmh["ivoucher"]==1){
                $err['id']  = "id_addon_user";
                $err['msg'] = "Mã đơn hàng này đã được lấy voucher. Vui lòng kiểm tra lại.";
                $ret[] = $err;
            }else{
                if($detail_hdmh["productsid"] !=3){
                    $err['id']  = "id_addon_user";
                    $err['msg'] = "Sản phẩm thu này không được lấy voucher. Chi có điện thoại. Vui lòng kiểm tra lại.";
                    $ret[] = $err;
                }
            }
        }
        if(count($ret) >0){
            for($i=0;$i<count($ret);$i++){
                $msg = $ret[$i]['msg'];
                $ids = $ret[$i]['id'];
                echo "<script>window.parent.show_msg_alert('$msg','$ids');</script>";
                return;
            }
        }else{
            $slist = $___voucher->get_list_by_id_addon_user($id_addon_user,$id);
            if($slist==NULL){
               $code_value =500000;
                $data = $this->get_data_voucher($detal, $id_addon_user, $code_value);
                $___voucher->insert($data);
                $query = "update hnam_purchase set ivoucher = 1 where id = $id_addon_user";
                Business_Addon_UsersProducts::getInstance()->excute($query);
                $slist = $___voucher->get_list_by_id_addon_user($id_addon_user,$id); 
            }
        }
        $this->view->slist = $slist;
    }

        public function pass_promotion($products_id, $type) {
        $sdata = Business_Addon_Promotion::getInstance()->getListByType2($products_id, $type);
        return $sdata;
    }
    public function radcodevoucher612pkAction(){
         $this->_helper->Layout()->disableLayout();
        $_vouchers = Business_Ws_VoucherAdd::getInstance();
        $___voucher = Business_Addon_Voucher::getInstance();
        $__productItem = Business_Ws_ProductsItem::getInstance();
        $id = (int)$this->_request->getParam('id');
        $id_addon_user = (int)$this->_request->getParam('id_addon_user');
        $detal = $_vouchers->get_detail_by_id($id);
        $token = $this->_request->getParam("token");
        $ztoken = md5($this->skey . $id.$this->_identity["parentid"]);
        $ret = array();
        $err = array();
        if ($token != $ztoken) {
            $err['id']  = "id_addon_user";
            $err['msg'] = "Bạn không có quyền truy cập";
            $ret[] = $err;
        }
        if((int)$detal["enabled"] ==0){
            $err['id']  = "id_addon_user";
            $err['msg'] = "Chương trình này đã hết hạn.";
            $ret[] = $err;
        }
        
        if($id_addon_user ==0){
            $err['id']  = "id_addon_user";
            $err['msg'] = "Vui lòng nhập mã đơn hàng..";
            $ret[] = $err; 
        }
        
        
        
        if($id_addon_user >0){
            $list_products = Business_Addon_UsersProducts::getInstance()->getListByBillIdActived($id_addon_user);
        }
        if($list_products ==NULL){
            $err['id']  = "id_addon_user";
            $err['msg'] = "Mã đơn hàng này không có thực. Vui lòng kiểm tra lại.";
            $ret[] = $err;
        }
        if(count($ret) >0){
            for($i=0;$i<count($ret);$i++){
                $msg = $ret[$i]['msg'];
                $ids = $ret[$i]['id'];
                echo "<script>window.parent.show_msg_alert('$msg','$ids');</script>";
                return;
            }
        }else{
            $slist = $___voucher->get_list_by_id_addon_user($id_addon_user,$id);
            if($slist ==NULL){
                $count_pk = 0;
                $count_phone = 0;
                foreach ($list_products as $sdata){
                    $uid = $sdata["id_customer"];
                    if ($sdata["productsid"] ==3 || $sdata["productsid"] ==5 || $sdata["productsid"] ==6 ) {// đếm máy
                        $count_phone = 1;
                    }
                    if ($sdata["productsid"] == 4) { // đếm phụ kiện
                        if($sdata["products_id"] !=10042){
                            $count_pk++;
                        }
                    }
                    $__strID[] = $sdata["products_id"];
                }

                $strID = implode(",", $__strID);
                $_list_products = $__productItem->getListByProductsID2($strID);
                $old_price = array();
                foreach ($_list_products as $item) {
                    $old_price[$item["itemid"]] = $item["oldprice"];
                }
                $t = "4";
                $s_promotion = $this->pass_promotion($strID, $t);

                $continue_promotion = array();
                foreach ($s_promotion as $item) {
                    $continue_promotion[$item["itemid_title"]][$item["type"]] = (int) $item["itemid"];
                }

                $detail_customer = Business_Addon_Customer::getInstance()->getDetail($uid);
                foreach ($list_products as &$_data){
                    $products_id = $_data["products_id"];
                    if($detail_customer["kle"]==0){
                        $no_voucher = array(10439,10990,10989,11497,11556,11496,10983,10986,11495,10984,10988,10979,10982,10985,10976,10987,10981,10978,10975,10977,10980,11031,8471,7621);
                        if($_data["productsid"] == 4 && !in_array($products_id, $no_voucher)){
                            
                            $_data["code_value"] = 15;
                        }
//                        if($_data["productsid"] == 4 && $_data["is_special"]==0 && $old_price[$products_id] ==0 && (int)$continue_promotion[$products_id][4] ==0){
//                            if($count_phone >0){
//                                if($count_pk >1){
//                                    $_data["code_value"] = 12/100*$_data["products_price_cost"];
//                                }else{
//                                    $_data["code_value"] = 6/100*$_data["products_price_cost"];
//                                } 
//                            }else{
//                                if($count_pk >2){
//                                    $_data["code_value"] = 12/100*$_data["products_price_cost"];
//                                }
//                                if($count_pk ==2){
//                                    $_data["code_value"] = 6/100*$_data["products_price_cost"];
//                                }
//                            }
//                        }
                        
                        
                        
                    }
                }

                foreach ($list_products as $vl){
                    if($detail_customer["kle"]==0){
                        $code_value = (int)$vl["code_value"];
                        if($code_value >0){
                            $data = $this->get_data_voucher($detal, $id_addon_user, $code_value);
                            $___voucher->insert($data);
                        }
                    }
                }
                $slist = $___voucher->get_list_by_id_addon_user($id_addon_user,$id);
            }
            $this->view->slist = $slist;
        }
        
        
    }

    public function getvoucherbgdAction(){
        $type = intval($this->_request->getParam("type",0));
		$this->view->type = $type;
		
        if($this->_request->isPost()){
            $price = (int)  $this->_request->getParam("price");
            $_price_orther = $this->_request->getParam("price_orther");
            $price_orther = str_replace(",", "", $_price_orther);
            if($price==-1){
                $price = (int)$price_orther;
            }
            $dly_bbe = (int)  $this->_request->getParam("dly_bbe");
            $ret = array();
            $err = array();
            if($price ==0){
                $err['id']  = "price";
                $err['msg'] = "Vui lòng chọn số tiền";
                $ret[] = $err;
            }
            if($price <1){
                $err['id']  = "price";
                $err['msg'] = "Số tiền là số nguyên dương. Vui lòng kiểm tra lại";
                $ret[] = $err;
            }
            if($dly_bbe ==0){
                $err['id']  = "price";
                $err['msg'] = "Vui lòng chọn lý do cấp mã voucher";
                $ret[] = $err;
            }
            if(count($ret) >0){
                for($i=0;$i<count($ret);$i++){
                    $msg = $ret[$i]['msg'];
                    $ids = $ret[$i]['id'];
                    echo "<script>window.parent.show_msg('$msg','$ids');</script>";
                    return;
                }

            }else{
                $tiento = "BGD-";
                $_vouchers = Business_Ws_VoucherAdd::getInstance();
                $detal = $_vouchers->get_detail_by_id($__id=29);
                $code = "$tiento".$_vouchers->randomCode("0123456789", 6, 1);
                $data['code_name'] = $this->check_code_name($tiento,$code);
                $data['itemid'] = $detal['itemid'];
                $data['code_publish'] = 0;
                $data['code_value'] = $price;
                $data['code_value_tmp'] = $price;
                $data['code_type'] = $detal['code_type'];
                $data['type_ctkm'] = (int)$detal['type_ctkm'];
                $data['code_get'] = 0;
                $data['code_store'] = 0;
                $data['code_created'] = date('Y-m-d H:i:s');
                $data['code_updated'] = date('Y-m-d H:i:s');
                $data['code_expired'] = $detal['code_expired'];
                if($detal['code_expired'] == '0000-00-00 00:00:00' || $detal['code_expired'] == NULL){
                    $data['code_expired'] = date('Y-m-d 23:59:59');  
                }
                $data['customer_id'] = 0;
                $note = $this->_request->getParam("note");
                $data['note'] = $detal['note']."-".$note;
                $data['used'] = 0;
                $data['sectionid'] = $detal['id'];
                $data['limit_up'] = $detal['limit_up'];
                $data['limit_down'] = $detal['limit_down'];
                $data['phone'] = 0;
                $data["email"] = '';

                $data["productsid"] = $detal["productsid"];
                $data["istype"] = $detal["istype"];
                $data["nokm"] = $detal["nokm"];
                $data["number_used"] = $detal["nb_used"];
                $data["creator"] = $this->_identity["username"];

                $data["flag"] = $detal["flag"];
                $data["id_ctkm"] = $detal["id_ctkm"];
                $data["is_special"] = $detal["is_special"];
                
                $data["typevoucher"] = $dly_bbe;
                Business_Addon_Voucher::getInstance()->insert($data);
                
                $___pr = number_format($price);
                echo "<script>window.parent.show_voucher('Voucher mệnh giá $___pr có mã  là : $code');</script>";
                return;
            }
            
            
        }
    }
    public function saveitemidAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $id = (int)  $this->_request->getParam("id");
        $itemid = $this->_request->getParam("itemid");
        if($id >0){
            $data["itemid"] = $itemid;
            Business_Ws_VoucherAdd::getInstance()->update($id, $data);
        }
    }

    public function getvoucherAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $_option = Business_Addon_Options::getInstance();
        $_vouchers = Business_Ws_VoucherAdd::getInstance();
        $id = $this->_request->getParam('id', 0);
        $detal = $_vouchers->getVouchers($id, 'ws_vouchers_add');
        $ztoken = md5($this->skey . $id.$this->_identity["parentid"]);
        $this->view->vouchers = $detal['note'];
        $token = $this->_request->getParam("token");
        
        $ret = array();
        $err = array();
        if ($token != $ztoken) {
            $err['id']  = "phone";
            $err['msg'] = "Bạn không có quyền truy cập";
            $ret1[] = $err;
        }
        if($detal["enabled"] ==0){
            $err['id']  = "phone";
            $err['msg'] = "Chương trình này đã hết hạn.";
            $ret1[] = $err;
        }
        if($ret1 != NULL){
            $ret = array_merge($ret,$ret1);
        }
        
        
        $phone = $this->_request->getParam("phone",0);
        $email = $this->_request->getParam("email");
        $note2 = $this->_request->getParam("note2");
        $data = array();
        $tt = "HNAM-";
        if($detal["tiento"] != NULL){
            $tt = $detal["tiento"]."-";
        }
        
        $data['note2'] = $note2;
        $data['itemid'] = $detal['itemid'];
        $data['access_phone'] = (int)$detal['access_phone'];
        $data['code_publish'] = 0;
        $data['code_value'] = $detal['code_value'];
        $data['code_value_tmp'] = $detal['code_value'];
        $data['code_type'] = $detal['code_type'];
        $data['type_ctkm'] = (int)$detal['type_ctkm'];
        $data['code_get'] = 0;
        $data['code_store'] = 0;
        $data['code_created'] = date('Y-m-d H:i:s');
        $data['code_updated'] = date('Y-m-d H:i:s');
        $data['code_expired'] = $detal['code_expired'];
        if($detal['code_expired'] == '0000-00-00 00:00:00' || $detal['code_expired'] == NULL){
            $data['code_expired'] = date('Y-m-d 23:59:59');  
        }
        $data['customer_id'] = 0;
        $data['note'] = $detal['note'];
        $data['used'] = 0;
        $data['sectionid'] = $detal['id'];
        $data['limit_up'] = $detal['limit_up'];
        $data['limit_down'] = $detal['limit_down'];
        $data['phone'] = $phone;
        $data["email"] = $email;
       
        $data["productsid"] = $detal["productsid"];
        $data["istype"] = $detal["istype"];
        $data["nokm"] = $detal["nokm"];
        $data["number_used"] = $detal["nb_used"];
        $data["creator"] = $this->_identity["username"];
        
        $data["flag"] = $detal["flag"];
        $data["id_ctkm"] = $detal["id_ctkm"];
        $data["is_special"] = $detal["is_special"];
        
        $ret = $_option->checkPhone($phone, "phone");
        
        if($phone != NULL){
            $detail_custumer = Business_Addon_Customer::getInstance()->get_detail_by_phone_actived($phone);
        }
        if($detail_custumer["kle"]==1){
            $err["id"] = "phone";
            $err["msg"] = "Số điện thoại này của khách lẻ nên không thẻ áp dụng";
            $ret[] = $err;
        }
//        Điểm H-VIP
        $goal = Business_Addon_UsersProducts::getInstance()->countGoalByPhone($phone);
        if($detal["id"] ==42){
            if($note2 ==NULL){
                $err["id"] = "note2";
                $err["msg"] = "Vui lòng nhập mã voucher của hãng vào";
                $ret[] = $err;
            }
        }
        if($detal['type_ctkm'] ==1){ // H-VIP
            if((int)$goal["sum"] < 40000000){
                $err["id"] = "phone";
                $err["msg"] = "Bạn không phải là H-VIP. H-VIP có tích lũy trên 15.000.000. Tích lũy của bạn là ".number_format($goal["sum"]);
                $ret[] = $err;
            }
            $tt="HVIP";
        }
        if($detal['type_ctkm'] ==2){ // H-MEMBER
            if((int)$goal["sum"] < 5000000){
                $err["id"] = "phone";
                $err["msg"] = "Bạn không phải là H-MEMBER. H-MEMBER có tích lũy trên 1.000.000 đến 15.000.000. Tích lũy của bạn là ".number_format($goal["sum"]);
                $ret[] = $err;
            }
            $tt="HMEMBER";
        }
        if ($email != null && !$_option->isValidEmail($email)) {
                $err['id']  = "email";
                $err['msg'] = "Vui lòng nhập địa chỉ email hợp lệ";
                $ret[] = $err;
            }
            if(strtotime($detal["code_expired"]) >0){
                if(strtotime('now')>strtotime($detal["code_expired"])){
                    $err['id']  = "phone";
                    $err['msg'] = "Chương trình này đã hết hạn.";
                    $ret[] = $err;
                }
            }
        if((int)$detal["number_used"] >0){
            $m = (int)  $this->_request->getParam("month");
            if($m ==0){
                $m = (int)date('m');
            }
            $lcount_number_used = Business_Addon_Voucher::getInstance()->getListByPhone($phone,$m,$detal['id']);
            foreach ($lcount_number_used as $item){
                if($item["used"] ==1){
                    $count_number_used[] = $item;
                }
            }
            
            $number_used = count($count_number_used);
            if($number_used > $detal["number_used"]){
                $err["id"] = "phone";
                $err["msg"] = "Giới hạn số lần sử dụng ".$detal["number_used"].". Số lần sử dụng của bạn là ".  number_format($number_used);
                $ret[] = $err;
            }
        }
        
        if(count($ret) >0){
            for($i=0;$i<count($ret);$i++){
                $msg = $ret[$i]['msg'];
                $ids = $ret[$i]['id'];
                echo "<script>window.parent.show_msg_alert('$msg','$ids');</script>";
                return;
            }
            
        }else{
            $code = "$tt".$_vouchers->randomCode("ABCDEFGHJKMNPQRSTUVWXYZ23456789", 6, 1);
            $data['code_name'] = $this->check_code_name2($tt,$code);
            Business_Addon_Voucher::getInstance()->insert($data);
            $date = '2017-11-25';
            if(strtotime('now') < strtotime($date)){
                if($detal['type_ctkm'] ==1 || $detal['type_ctkm'] ==2){ // H-VIP
                    echo "<script>window.parent.show_voucher('Voucher có mã  là : $code');</script>";
                    return;
                }
            }else{
                $this->send_voucher($detal, $phone, $code);
            }
        }
            
    }
    public function getvoucherbyphoneAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $_option = Business_Addon_Options::getInstance();
        $_vouchers = Business_Ws_VoucherAdd::getInstance();
        $id = $this->_request->getParam('id', 0);
        $detal = $_vouchers->getVouchers($id, 'ws_vouchers_add');
        $ztoken = md5($this->skey . $id.$this->_identity["parentid"]);
        $this->view->vouchers = $detal['note'];
        $token = $this->_request->getParam("token");
        
        $ret = array();
        $err = array();
        if ($token != $ztoken) {
            $err['id']  = "phone";
            $err['msg'] = "Bạn không có quyền truy cập";
            $ret1[] = $err;
        }
        if($detal["enabled"] ==0){
            $err['id']  = "phone";
            $err['msg'] = "Chương trình này đã hết hạn.";
            $ret1[] = $err;
        }
        if($ret1 != NULL){
            $ret = array_merge($ret,$ret1);
        }
        
        
        $phone = $this->_request->getParam("phone",0);
        $email = $this->_request->getParam("email");
        $note2 = $this->_request->getParam("note2");
        $data = array();
        $tt = "HNAM-";
        if($detal["tiento"] != NULL){
            $tt = $detal["tiento"]."-";
        }
        
        $data['note2'] = $note2;
        $data['itemid'] = $detal['itemid'];
        $data['access_phone'] = (int)$detal['access_phone'];
        $data['code_publish'] = 0;
        $data['code_value'] = $detal['code_value'];
        $data['code_value_tmp'] = $detal['code_value'];
        $data['code_type'] = $detal['code_type'];
        $data['type_ctkm'] = (int)$detal['type_ctkm'];
        $data['code_get'] = 0;
        $data['code_store'] = 0;
        $data['code_created'] = date('Y-m-d H:i:s');
        $data['code_updated'] = date('Y-m-d H:i:s');
        $data['code_expired'] = $detal['code_expired'];
        if($detal['code_expired'] == '0000-00-00 00:00:00' || $detal['code_expired'] == NULL){
            $data['code_expired'] = date('Y-m-d 23:59:59');  
        }
        $data['customer_id'] = 0;
        $data['note'] = $detal['note'];
        $data['used'] = 0;
        $data['sectionid'] = $detal['id'];
        $data['limit_up'] = $detal['limit_up'];
        $data['limit_down'] = $detal['limit_down'];
        $data['phone'] = $phone;
        $data["email"] = $email;
       
        $data["productsid"] = $detal["productsid"];
        $data["istype"] = $detal["istype"];
        $data["nokm"] = $detal["nokm"];
        $data["number_used"] = $detal["nb_used"];
        $data["creator"] = $this->_identity["username"];
        
        $data["flag"] = $detal["flag"];
        $data["id_ctkm"] = $detal["id_ctkm"];
        $data["is_special"] = $detal["is_special"];
        
        $ret = $_option->checkPhone($phone, "phone");
        
        if($phone != NULL){
            $detail_custumer = Business_Addon_Customer::getInstance()->get_detail_by_phone_actived($phone);
        }
        
        $arr_phone = array("0934133551","0902675550","0922357721","0933986686","0914443330","0934088881","0903686746","0903686746","0903686746","0933289098","0933289098","0906300809","0976195431","0915767527","0989168198","0978762779","0909710979","0937277977","0904872782","0937339330","0919011958","0902347923","0933968281","01284232580","0904307054","0908108056","0902101026","0903362575","01236969572","0905555525","0933508599","0933508599","0932757567","0901230200","0935809288","0933986686","0918888160","0942808081","0907260288","0915754475","0909562568","0989920022","0903362575","01207846956","096406449","01259668866","0913190396","0988742247","0902429192","0868932683","0988963168","0907910579","0908403057","0903922082","0903922082","0984446165","0938569005","01236761000","0904307054","0966616784","01203002873","0906928889","0908474347","0972704100","0908651105","0944778977","0902551311","091550794","0901359074","0935141212","01223101155","0982985898","0989920022","01223182747","0935252601");
        if(!in_array($phone, $arr_phone)){
            $err["id"] = "phone";
            $err["msg"] = "Số điện thoại này không được áp dụng chương trình này";
            $ret[] = $err;
        }
        if($detail_custumer["kle"]==1){
            $err["id"] = "phone";
            $err["msg"] = "Số điện thoại này của khách lẻ nên không thẻ áp dụng";
            $ret[] = $err;
        }
        if($detal['id'] !=135){
            $err["id"] = "phone";
            $err["msg"] = "Chỉ áp dụng chương trình preorder ";
            $ret[] = $err;
        }
        
        if ($email != null && !$_option->isValidEmail($email)) {
            $err['id']  = "email";
            $err['msg'] = "Vui lòng nhập địa chỉ email hợp lệ";
            $ret[] = $err;
        }
        if(strtotime($detal["code_expired"]) >0){
            if(strtotime('now')>strtotime($detal["code_expired"])){
                $err['id']  = "phone";
                $err['msg'] = "Chương trình này đã hết hạn.";
                $ret[] = $err;
            }
        }
        $detail_vouchers = Business_Addon_Voucher::getInstance()->getListByPhone($phone);
        if($detail_vouchers != NULL){
            $err['id']  = "phone";
            $err['msg'] = "Voucher này đã được gửi tới số điện thoại $phone .Vui lòng kiểm tra lại";
            $ret[] = $err;
        }
        if((int)$detal["number_used"] >0){
            $m = (int)  $this->_request->getParam("month");
            if($m ==0){
                $m = (int)date('m');
            }
            $lcount_number_used = Business_Addon_Voucher::getInstance()->getListByPhone($phone,$m,$detal['id']);
            foreach ($lcount_number_used as $item){
                if($item["used"] ==1){
                    $count_number_used[] = $item;
                }
            }
            
            $number_used = count($count_number_used);
            if($number_used > $detal["number_used"]){
                $err["id"] = "phone";
                $err["msg"] = "Giới hạn số lần sử dụng ".$detal["number_used"].". Số lần sử dụng của bạn là ".  number_format($number_used);
                $ret[] = $err;
            }
        }
        
        if(count($ret) >0){
            for($i=0;$i<count($ret);$i++){
                $msg = $ret[$i]['msg'];
                $ids = $ret[$i]['id'];
                echo "<script>window.parent.show_msg_alert('$msg','$ids');</script>";
                return;
            }
            
        }else{
            $code = "$tt".$_vouchers->randomCode("ABCDEFGHJKMNPQRSTUVWXYZ23456789", 6, 1);
            $data['code_name'] = $this->check_code_name2($tt,$code);
            Business_Addon_Voucher::getInstance()->insert($data);
            $this->send_voucher($detal, $phone, $code);
        }
            
    }
    public function getvoucherbhscAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $_option = Business_Addon_Options::getInstance();
        $_vouchers = Business_Ws_VoucherAdd::getInstance();
        $id = $this->_request->getParam('id', 0);
        $detal = $_vouchers->getVouchers($id, 'ws_vouchers_add');
        $ztoken = md5($this->skey . $id.$this->_identity["parentid"]);
        $this->view->vouchers = $detal['note'];
        $token = $this->_request->getParam("token");
        
        $ret = array();
        $err = array();
        if ($token != $ztoken) {
            $err['id']  = "phone";
            $err['msg'] = "Bạn không có quyền truy cập";
            $ret[] = $err;
        }
        if($detal["enabled"] ==0){
            $err['id']  = "phone";
            $err['msg'] = "Chương trình này đã hết hạn.";
            $ret[] = $err;
        }
        
        
        
        $phone = $this->_request->getParam("phone",0);
        $email = $this->_request->getParam("email");
        $data = array();
        $tiento = "KBH-";
        $code = "$tiento".$_vouchers->randomCode("ABCDEFGHJKMNPQRSTUVWXYZ23456789", 6, 1);
        $data['code_name'] = $this->check_code_name2($tiento,$code);
        
        $data['itemid'] = $detal['itemid'];
        $data['code_publish'] = 0;
        
        $data['code_type'] = $detal['code_type'];
        $data['type_ctkm'] = (int)$detal['type_ctkm'];
        $data['code_get'] = 0;
        $data['code_store'] = 0;
        $data['code_created'] = date('Y-m-d H:i:s');
        $data['code_updated'] = date('Y-m-d H:i:s');
        $data['code_expired'] = $detal['code_expired'];
        if($detal['code_expired'] == '0000-00-00 00:00:00' || $detal['code_expired'] == NULL){
            $data['code_expired'] = date('Y-m-d 23:59:59');  
        }
        $data['customer_id'] = 0;
        $data['note'] = $detal['note'];
        $data['used'] = 0;
        $data['sectionid'] = $detal['id'];
        $data['limit_up'] = $detal['limit_up'];
        $data['limit_down'] = $detal['limit_down'];
        $data['phone'] = $phone;
        $data["email"] = $email;
       
        $data["productsid"] = $detal["productsid"];
        $data["istype"] = $detal["istype"];
        $data["nokm"] = $detal["nokm"];
        $data["number_used"] = $detal["nb_used"];
        $data["creator"] = $this->_identity["username"];
        
        $data["flag"] = $detal["flag"];
        $data["id_ctkm"] = $detal["id_ctkm"];
        $data["is_special"] = $detal["is_special"];
        
        $ret = $_option->checkPhone($phone, "phone");
//        Điểm H-VIP
        $goal = Business_Addon_UsersProducts::getInstance()->countGoalByPhone($phone);
        if((int)$goal["sum"] < 5000000){
            $err["id"] = "phone";
            $err["msg"] = "Bạn không phải là H-MEMBER/H-VIP. H-MEMBER/H-VIP có tích lũy trên 5.000.000. Tích lũy của bạn là ".number_format($goal["sum"]);
            $ret[] = $err;
        }
        if((int)$goal["sum"] >  5000000 && (int)$goal["sum"] < 40000000){
            $data['code_value'] = 5; // hmember
        }
        if((int)$goal["sum"] >  40000000){
            $data['code_value'] = 10; // hvip
        }
        
        if ($email != null && !$_option->isValidEmail($email)) {
                $err['id']  = "email";
                $err['msg'] = "Vui lòng nhập địa chỉ email hợp lệ";
                $ret[] = $err;
            }
            if(strtotime($detal["code_expired"]) >0){
                if(strtotime('now')>strtotime($detal["code_expired"])){
                    $err['id']  = "phone";
                    $err['msg'] = "Chương trình này đã hết hạn.";
                    $ret[] = $err;
                }
            }
        if((int)$detal["number_used"] >0){
            $m = (int)  $this->_request->getParam("month");
            if($m ==0){
                $m = (int)date('m');
            }
            $lcount_number_used = Business_Addon_Voucher::getInstance()->getListByPhone($phone,$m,$detal['id']);
            foreach ($lcount_number_used as $item){
                if($item["used"] ==1){
                    $count_number_used[] = $item;
                }
            }
            
            $number_used = count($count_number_used);
            if($number_used > $detal["number_used"]){
                $err["id"] = "phone";
                $err["msg"] = "Giới hạn số lần sử dụng ".$detal["number_used"].". Số lần sử dụng của bạn là ".  number_format($number_used);
                $ret[] = $err;
            }
        }
        
        if(count($ret) >0){
            for($i=0;$i<count($ret);$i++){
                $msg = $ret[$i]['msg'];
                $ids = $ret[$i]['id'];
                echo "<script>window.parent.show_msg_alert('$msg','$ids');</script>";
                return;
            }
            
        }else{
            Business_Addon_Voucher::getInstance()->insert($data);
            $this->send_voucher($detal, $phone, $code);
        }
            
    }
    function checkCodename($code)
    {
        $_vouchers = Business_Ws_VoucherAdd::getInstance();
        if ($_vouchers->validateVoucher($code, 'ws_vouchers')) {
            $code = $_vouchers->randomCode("abcdefghjkmnopqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ23456789", 8, 1);
            return $this->checkCodename($code);
        } else
            return $code;
    }
    function check_code_name($tiento,$code)
    {
        $_vouchers = Business_Ws_VoucherAdd::getInstance();
        if ($_vouchers->validateVoucher($code, 'ws_vouchers')) {
            $code = $tiento.$_vouchers->randomCode("0123456789", 6, 1);
            return $this->check_code_name($tiento,$code);
        } else
            return $code;
    }
    function check_code_name2($tiento,$code)
    {
        $_vouchers = Business_Ws_VoucherAdd::getInstance();
        if ($_vouchers->validateVoucher($code, 'ws_vouchers')) {
            $code = $tiento.$_vouchers->randomCode("ABCDEFGHJKMNPQRSTUVWXYZ23456789", 6, 1);
            return $this->check_code_name($tiento,$code);
        } else
            return $code;
    }
    public function getbillAction(){
        if($this->_request->isPost()){
                $id_addon_user = $this->_request->getParam("id_addon_user");
                $this->view->id_addon_user = $id_addon_user;
                $list = Business_Addon_UsersProducts::getInstance()->getListByBillIdActived($id_addon_user, $is_actived=1);
                $this->view->list = $list;
            }
    }
    public function getvoucherkdAction(){
        if($this->_request->isPost()){
            $id_addon_user = (int)$this->_request->getParam("id_addon_user");
            $products_id = (int)$this->_request->getParam("products_id");
            $_price = $this->_request->getParam("price");
            $price = str_replace(",", "", $_price);
           
            $detail_products = Business_Addon_UsersProducts::getInstance()->getDetailByBill($id_addon_user,$products_id);
            $phone_dly = $detail_products["phone_addon"];
            $products_name = $detail_products["products_name"];
            $check_dl = Business_Addon_Agency::getInstance()->get_detail_by_phone($phone_dly);
            
            $ret = array();
            $err = array();
            if($check_dl ==NULL){
                $err['id']  = "id_addon_user";
                $err['msg'] = "Số điện thoại này không phải của đại lý. nên không thể giảm giá. Vui lòng xem hoặc liên hệ IT";
                $ret[] = $err;
            }
            if((int)$price ==0){
                $err['id']  = "price";
                $err['msg'] = "Vui lòng chọn số tiền";
                $ret[] = $err;
            }
            if((int)$price <1){
                $err['id']  = "price";
                $err['msg'] = "Số tiền là số nguyên dương. Vui lòng kiểm tra lại";
                $ret[] = $err;
            }
            
            if(count($ret) >0){
                for($i=0;$i<count($ret);$i++){
                    $msg = $ret[$i]['msg'];
                    $ids = $ret[$i]['id'];
                    echo  $msg;
                    die();
                }

            }else{
                $_vouchers = Business_Ws_VoucherAdd::getInstance();
                $detal = $_vouchers->get_detail_by_id($__id=30);// Kinh doanh
                $tiento ="KD-";
                $code = $tiento.$_vouchers->randomCode("0123456789", 6, 1);
                
                $data['code_name'] = $this->check_code_name($tiento,$code);
                $data['itemid'] = $products_id;
                $data['code_publish'] = 0;
                $data['code_value'] = $price;
                $data['code_value_tmp'] = $price;
                $data['code_type'] = $detal['code_type'];
                $data['type_ctkm'] = (int)$detal['type_ctkm'];
                $data['code_get'] = 0;
                $data['code_store'] = 0;
                $data['code_created'] = date('Y-m-d H:i:s');
                $data['code_updated'] = date('Y-m-d H:i:s');
                $data['code_expired'] = $detal['code_expired'];
                if($detal['code_expired'] == '0000-00-00 00:00:00' || $detal['code_expired'] == NULL){
                    $data['code_expired'] = date('Y-m-d 23:59:59');  
                }
                $data['customer_id'] = 0;
                $data['note'] = $detal['note'];
                $data['used'] = 0;
                $data['sectionid'] = $detal['id'];
                $data['limit_up'] = $detal['limit_up'];
                $data['limit_down'] = $detal['limit_down'];
                $data['phone'] = $detail_products["phone_addon"];
                $data["email"] = '';

                $data["productsid"] = $detal["productsid"];
                $data["istype"] = $detal["istype"];
                $data["nokm"] = $detal["nokm"];
                $data["number_used"] = $detal["nb_used"];
                $data["creator"] = $this->_identity["username"];

                $data["flag"] = $detal["flag"];
                $data["id_ctkm"] = $detal["id_ctkm"];
                $data["is_special"] = $detal["is_special"];
                $data["id_addon_user"] = $id_addon_user;
                
                Business_Addon_Voucher::getInstance()->insert($data);
                
                $pname = Business_Common_Utils::removeTiengViet($products_name);
                
                $_content = "ma giam tien $price.d - $code - $pname";
                $content = substr($_content, 0, 150);
                
                $token_smk = md5($phone_dly."HNAMSACO2016");
                $c = urlencode($content);
                $url_mobile = "http://www.hnammobile.com/sms/iphone-sacom?token=$token_smk&phone=$phone_dly&content=$c";
                Business_Common_Utils::getContentByCurl($url_mobile);
                
                echo "Mã voucher đã được gửi tới sdt của khách hàng.";
                die();
            }
            
            
        }
    }
    
    public function eventnote7Action(){
        $userid = $this->_identity["userid"];
        $date = date('dmYH');
        $token = md5($userid.'note7-2016'.$date);
        $this->view->token = $token;
        $this->view->userid = $userid;
    }
    public function rvoucherAction(){
        $this->_helper->Layout()->disableLayout();
//        $this->_helper->viewRenderer->setNoRender(true);
        $_vouchers = Business_Ws_VoucherAdd::getInstance();
        $tiento = $this->_request->getParam("tiento");
        $sl = (int)$this->_request->getParam("sl");
//        $tiento ="LENOVO15-";
        for($i=0;$i<$sl;$i++){
            $code = $tiento.$_vouchers->randomCode("0123456789", 6, 1);
            $list[] = $code;
        }
        $this->view->list = $list;
    }
    
}
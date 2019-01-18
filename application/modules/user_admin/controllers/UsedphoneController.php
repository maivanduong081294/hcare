<?php

/**
 * User Admin Banner Controller
 * @author: nghidv
 */
class User_Admin_UsedphoneController extends Zend_Controller_Action {
    private $_identity;
    
    private $_type = array(
        0 => array(//apple sieu HOT
            1 => 40, //thoi gian bao hanh: 0thang
            2 => 35, //thoi gian bao hanh: 3-6th
            3 => 30, //thoi gian bao hanh: 6-9th
            4 => 30, //thoi gian bao hanh: 9-12th
            5 => 25, //Su dung duoi 16-30 thang
            6 => 20 //Su dug duoi 1-15 ngay
        ),
        1 => array(//apple
            1 => 40, //thoi gian bao hanh: 0thang
            2 => 35, //thoi gian bao hanh: 3-6th
            3 => 30, //thoi gian bao hanh: 6-9th
            4 => 30, //thoi gian bao hanh: 9-12th
            5 => 25, //Su dung duoi 16-30 thang
            6 => 20 //Su dug duoi 1-15 ngay
        ),
        2 => array(//nhanh
            1 => 45, //thoi gian bao hanh: 0thang
            2 => 40, //thoi gian bao hanh: 3-6th
            3 => 35, //thoi gian bao hanh: 6-9th
            4 => 30, //thoi gian bao hanh: 9-12th
            5 => 25, //Su dung duoi 16-30 thang
            6 => 20 //Su dug duoi 1-15 ngay
        ),
        3 => array(//binh thuong
            1 => 60, //thoi gian bao hanh: 0thang
            2 => 50, //thoi gian bao hanh: 3-6th
            3 => 45, //thoi gian bao hanh: 6-9th
            4 => 40, //thoi gian bao hanh: 9-12th
            5 => 35, //Su dung duoi 16-30 thang
            6 => 30 //Su dung duoi 1-15 ngay
        ),
        4 => array(//cham
            1 => 75, //thoi gian bao hanh: 0thang
            2 => 65, //thoi gian bao hanh: 3-6th
            3 => 60, //thoi gian bao hanh: 6-9th
            4 => 55, //thoi gian bao hanh: 9-12th
            5 => 50, //Su dung duoi 16-30 thang
            6 => 45 //Su dug duoi 1-15 ngay
        ),
        5 => array(//apple sieu HOT
            1 => 40, //thoi gian bao hanh: 0thang
            2 => 35, //thoi gian bao hanh: 3-6th
            3 => 30, //thoi gian bao hanh: 6-9th
            4 => 30, //thoi gian bao hanh: 9-12th
            5 => 25, //Su dung duoi 16-30 thang
            6 => 20 //Su dug duoi 1-15 ngay
        ),
    );
    //may bi can mop thi khong tinh tray suoc
    private $_canmop = array(
        1 => 0,
        2 => 15,
        3 => 25,
        4 => 35
    );
    //may bi tray suoc
    private $_tray = array(
        1 => 0,
        2 => 5, //tray nhe
        3 => 10, //tray nang
        4 => 15 //tray qua nang
    );
    private $_repair = array(
        1 => 0, //máy còn bảo hành, chưa sửa chữa
        2 => 5, //máy đã qua sửa chữa bởi bảo hành
        3 => 30 //máy đã sữa chưa dịch vụ bên ngoài
    );

    public function init() {
        BlockManager::setLayout('appbh');
        $auth = Zend_Auth::getInstance();
        $this->_identity = (array) $auth->getIdentity();
		$this->_redirect('http://app.hnammobile.com/admin/user/usedphone2');
    }
    
    public function testAction() {
        
        $usedid = 35;
        $used = Business_Addon_Usedphone::getInstance()->getDetail($usedid);
        $usedinfo = Business_Addon_Usedphoneinfo::getInstance()->getDetailByUsedID($usedid);
        
        echo $this->renderUsedDescription($used, $usedinfo);
    }
    
    public function updateInfo2Action() {
        //valid user
        $auth = Zend_Auth::getInstance();
        $identity = (array) $auth->getIdentity();
        $uname = $identity["username"];
            
        if (!($uname == 'tech' || $uname == 'tech2' || $uname == 'vote_all' || $uname == 'hnmobile')) {
            die("invalid access!!");
        }
        
        if ($this->_request->isPost()) {
            $this->_helper->viewRenderer->setNoRender();
            $this->view->layout()->disableLayout();
        
            $itemid = (int)$this->_request->getParam("itemid");
            $usedinfo = (int)$this->_request->getParam("usedid");
            $price = (int)$this->_request->getParam("newprice");
            
            //insert to history sell out
            $data=array();
            $data["addon_usedphone_info_id"] = $usedinfo;
            $data["itemid"] = $itemid;
            $data["price"] = $price;
            $data["datetime"] = date("Y-m-d H:i:s");
            
            Business_Addon_Usedphonehistory::getInstance()->insert($data);
            
            
            //update price
            $pdetail = Business_Ws_ProductsItem::getInstance()->getDetail($itemid);
            if ($pdetail["price"]>0) {
                $pdetail["price"] = $price;
            }
            if ($pdetail["original_price"]>0) {
                $pdetail["original_price"] = $price;
            }
            
            $productsid = $pdetail["productsid"];
            $cateid = $pdetail["cateid"];
                
            Business_Ws_ProductsItem::getInstance()->update($itemid, $productsid, $cateid, $pdetail);
            
            
            //result
            $data = array();
            $data["err"] = 0;
            $data["msg"] = "ok";
            echo json_encode($data);
            
        } else {
        
            $usedinfo = (int)$this->_request->getParam("id");
            $usedHistory = Business_Addon_Usedphonehistory::getInstance()->getListByUsedID($usedinfo);        

            if (count($usedHistory)>0) {
                $_item = $usedHistory[0];
                $itemid = $_item["itemid"];
                $pdetail = Business_Ws_ProductsItem::getInstance()->getDetail($itemid);

                $this->view->data = $pdetail;
                $this->view->usedid = $usedinfo;
                $this->view->history = $usedHistory;
                $this->view->hasItem = 1;
            } else {
                $this->view->hasItem = 0;
            }
        }
    }
    
    public function updateInfoAction() {
        
        //valid user
        $auth = Zend_Auth::getInstance();
        $identity = (array) $auth->getIdentity();
        $uname = $identity["username"];
            
        if (!($uname == 'tech' || $uname == 'tech2' || $uname == 'vote_all'  || $uname == 'hnmobile')) {
            die("invalid access!!");
        }
            
        if ($this->_request->isPost()){
            $this->_helper->viewRenderer->setNoRender();
            $this->view->layout()->disableLayout();
        
            $itemid = (int)$this->_request->getParam("itemid");
            $usedid = (int)$this->_request->getParam("usedid");

            //copy new product
            $lastid = $this->copyUsedAction($itemid);
                
            //update info
            $title = $this->_request->getParam("title");
            $price = $this->_request->getParam("price");
            $original_price = $this->_request->getParam("original_price");
            $warranty = $this->_request->getParam("warranty");
            $fullcontent = $this->_request->getParam("fullcontent");
            
            $pdetail = Business_Ws_ProductsItem::getInstance()->getDetail($lastid);            
            $pdetail["onstock"] = 1;
            $pdetail["quanlity"] = 1;
            $pdetail["title"] = $title;
            $pdetail["price"] = $price;
            $pdetail["newest"] = 0;
            $pdetail["super"] = 0;
            $pdetail["bestseller"] = 0;
            $pdetail["ishot"] = 0;
            $pdetail["highlight"] = 0;
            $pdetail["cheap"] = 0;
            $pdetail["highend"] = 0;
            $pdetail["smartphone"] = 0;
            $pdetail["newcome"] = 0;
            $pdetail["original_price"] = $original_price;
            $pdetail["warranty"] = $warranty;
            $pdetail["fullcontent"] = $fullcontent;
            $pdetail["bonus_company_full"] = '';
            $pdetail["bonus_mobile"] = '';
            
            $productsid = $pdetail["productsid"];
            $cateid = $pdetail["cateid"];
                
            Business_Ws_ProductsItem::getInstance()->update($lastid, $productsid, $cateid, $pdetail);
            
            //update to checked
            $usedinfo = Business_Addon_Usedphoneinfo::getInstance()->getDetailByUsedID($usedid);
            $usedinfo["checked"] = 1;
            $usedinfo["itemid"] = $lastid;
            Business_Addon_Usedphoneinfo::getInstance()->update($usedinfo["id"], $usedinfo);
            
            //update price to history list
            $hdata = array();
            $hdata["addon_usedphone_info_id"] = $usedinfo["id"];
            $hdata["itemid"] = $lastid;
            $hdata["price"] = ($original_price > 0 ? $original_price : $price);
            $hdata["datetime"] = date("Y-m-d H:i:s");
            
            $_history = Business_Addon_Usedphonehistory::getInstance();
            $_history->insert($hdata);
            
//            $this->addhdmh($usedid, $lastid); // thêm vào phần mua hàng
            
            //result
            $data = array();
            $data["err"] = 0;
            $data["msg"] = "ok";
            echo json_encode($data);

        } else {
            $usedid = (int)$this->_request->getParam("id");
            $used = Business_Addon_Usedphone::getInstance()->getDetail($usedid);
            $usedinfo = Business_Addon_Usedphoneinfo::getInstance()->getDetailByUsedID($usedid);
            $des =  $this->renderUsedDescription($used, $usedinfo);
            $this->view->des = $des;
            $this->view->usedid = $usedid;
            $this->view->checked = $usedinfo["checked"];
            
            $itemid = $used["itemid_tmp"];
            if ($itemid > 0){
                $this->view->hasItemid = $itemid;
                $pdetail = Business_Ws_ProductsItem::getInstance()->getDetail($itemid);
                
                if ($pdetail["original_price"]>0) {
                    $pdetail["original_price"] = $usedinfo["sellout_price"];
                } else if ($pdetail["price"]>0) {
                    $pdetail["price"] = $usedinfo["sellout_price"];
                }
                $this->view->data = $pdetail;
            } else {
                $this->view->hasItemid = 0;
            }
        }
    }
    
    public function customerAction() {
        
        $auth = Zend_Auth::getInstance();
        $identity = (array) $auth->getIdentity();
        $storename = $this->getStoreName($identity["username"]);
        $this->view->storename = $storename;
        $this->view->sid = $identity["username"];
        
        $this->view->usedphone_id = (int)$this->_request->getParam("id");
        $udetail = Business_Addon_Usedphone::getInstance()->getDetail($this->view->usedphone_id);
        $this->view->imei = $udetail["imei"];
        $this->view->price = $udetail["price"];
        $this->view->price_voucher = self::getVoucherPrice($this->view->price, $udetail["type"], $udetail["type_voucher"]);
    }
    

    public function saveCustomerAction() {
        $this->_helper->viewRenderer->setNoRender();
        $this->view->layout()->disableLayout();
        
        $auth = Zend_Auth::getInstance();        
        $identity = (array) $auth->getIdentity();
            
        $fullname = $this->_request->getParam("fullname","");
        $email = $this->_request->getParam("email","");
        $address = $this->_request->getParam("address","");
        $phone = $this->_request->getParam("phone","");
        
            
        $usedphone_id = (int)$this->_request->getParam("usedphone_id");
        $udetail = Business_Addon_Usedphone::getInstance()->getDetail($usedphone_id);
        $price = $udetail["price"];
        $imei = $udetail["imei"];
        $storeid = $identity["username"];
        $cur_datetime = date("Y-m-d H:i:s");

        $data = array();
        $data["storeid"] = $storeid;
        $data["addon_usedphone_id"] = $usedphone_id;
        $data["imei"] = $imei;
        $data["fullname"] = $fullname;
        $data["address"] = $address;
        $data["phone"] = $phone;
        $data["email"] = $email;
        $data["price"] = (int) self::getVoucherPrice($price, $udetail["type"], $udetail["type_voucher"]);
        $data["datetime"] = $cur_datetime;
        $data["datetime_expired"] = self::getExpiredTime($cur_datetime);
        $data["active"] = 1;       
            
        $ret = Business_Addon_Usedphonecus::getInstance()->insert($data);
        echo $ret;
    }
    
    public function listAction() {
        $keyword = "";
        $this->view->isAdmin = 0;
        $auth = Zend_Auth::getInstance();
        $identity = (array) $auth->getIdentity();
        $sid = $identity["username"];
        
        $full = (int) $this->_request->getParam("full",0);
        $this->view->keyword = $keyword = $this->_request->getParam("keyword");
        $from = $this->_request->getParam("from");
        $to = $this->_request->getParam("to");
        $cur_sid = $this->_request->getParam("vote_id");
        if ($cur_sid != null) {
            $sid = $cur_sid;
        } else {
            $sid = $identity["username"];
        }
//        echo "<pre>";
//        var_dump($cur_sid);
//        die();
            
        if ($from != null) {
            
            $froms = explode("/", $from);            
            $froms = array_reverse($froms);
            $from = implode("-",$froms);            
        }
        if ($to != null) {
            
            $tos = explode("/", $to);            
            $tos = array_reverse($tos);
            $to = implode("-",$tos);            
        }
        
        if ($sid == "vote_all" && $full==0) {
            $this->view->isAdmin = 1;
            $this->_helper->viewRenderer('list-admin');
            //update from & to date
		if ($to == "") {
			$to = date("Y-m-d");		
		}
            
            $toTime = strtotime($to);
		if ($from == ""){
			$fromTime = $toTime - (7 * 24 * 60 * 60);
            		$from = date("Y-m-d", $fromTime);		
		}	
            
        }
        $this->view->from = $from;
        $this->view->to = $to;
            
        if ($keyword != null) {
            $list = Business_Addon_Usedphonecus::getInstance()->getList("vote_all",$keyword, "", $from, $to);
        } else {
            $list = Business_Addon_Usedphonecus::getInstance()->getList($sid,$keyword, "", $from, $to);
        }
            
        foreach($list as &$item) {
            
            $used_id = $item["addon_usedphone_id"];
            $_detail = Business_Addon_Usedphone::getInstance()->getDetail($used_id);
            $_detail["storename"] = Business_Addon_Options::getInstance()->getStoreName($item["storeid"]);
            $item["detail"] = $_detail;
                
            $infos = Business_Addon_Usedphoneinfo::getInstance()->getDetailByUsedID($_detail["id"]);
                
            $_user = Business_Common_Users::getInstance()->getUserByUid($infos["storeid"]);
            $infos["storename"] = $this->getStoreName($_user["username"]);
            if ($infos["datetime_selling"]!=null) {
                $infos["datetime_selling_text"] = date("d/m/Y H:i:s", strtotime($infos["datetime_selling"]));
            }
            $item["hasinfo"] = true;
            //check info
            if ($infos["info"]==null || $infos["warranty"]==null) {
                $item["hasinfo"] = false;
            }
            
            $sellout_text = $this->getSelloutText($infos["sellout"]);
            $_infos = "<p><b>Tình trạng</b>: ".$infos["info"]."</p>";
            $_infos .= "<p><b>Bảo hành</b>: ".$infos["warranty"]."</p>";
            $_infos .= "<p><b>Phụ kiện</b>: ".$infos["accessory"]."</p>";
            $_infos .= "<p><b>Bán ra</b>: ".$sellout_text."</p>";
            
            $item["infos"] = $_infos;
            $item["detail2"] = $infos;
             
            $item["vouchers6"] = $_detail["vouchers6"];
            
            if (APP_ENV != "development") {
                $item["link_sendmail"] = "http://www.hnammobile.com/admin/user/usedphone/sendmail2?id=".$used_cus_id;
            } else {
                $item["link_sendmail"] = "http://dev.hnamv4.test/admin/user/usedphone/sendmail-dev?id=".$used_cus_id;
            }
        }
        $this->view->list = $list;
        
        //get store list
        $slist = Business_Addon_Options::getInstance()->getStoreName2($sid);
        $this->view->slist = $slist;
        
        $this->view->sid = $sid;
            
    }
    
    
    
    public function reportAction() {
        $keyword = "";
        $this->view->isAdmin = 0;
        $auth = Zend_Auth::getInstance();
        $identity = (array) $auth->getIdentity();
        $sid = $identity["username"];
        
        if ($sid == "vote_all") {
            $this->view->isAdmin = 1;
        } else {
            Business_Common_Utils::redirect("/admin/user/usedphone/list");
        }
        $this->view->keyword = $keyword = $this->_request->getParam("keyword");
        $from = $this->_request->getParam("from");
        $to = $this->_request->getParam("to");
        $cur_sid = $this->_request->getParam("vote_id");
        if ($cur_sid != null) {
            $sid = $cur_sid;
        } else {
            $sid = $identity["username"];
        }
//        echo "<pre>";
//        var_dump($cur_sid);
//        die();
            
        if ($from != null) {
            $this->view->from = $from;
            $froms = explode("/", $from);            
            $froms = array_reverse($froms);
            $from = implode("-",$froms);            
        }
        if ($to != null) {
            $this->view->to = $to;
            $tos = explode("/", $to);            
            $tos = array_reverse($tos);
            $to = implode("-",$tos);            
        }
        if ($keyword != null) {
            $list = Business_Addon_Usedphonecus::getInstance()->getReport("vote_all",$keyword, "", $from, $to, "id desc");
            $list2 = Business_Addon_Usedphonecus::getInstance()->getReportUsed("vote_all",$keyword, "", $from, $to, "id desc");
        } else {
            $list = Business_Addon_Usedphonecus::getInstance()->getReport($sid,$keyword, "", $from, $to, "id desc");
            $list2 = Business_Addon_Usedphonecus::getInstance()->getReportUsed($sid,$keyword, "", $from, $to, "id desc");
        }
        
        echo "<pre>";
        var_dump($list, $list2);
        die();
            
        
        foreach($list as &$item) {
            $used_id = $item["addon_usedphone_id"];
            $_detail = Business_Addon_Usedphone::getInstance()->getDetail($used_id);
            $_detail["storename"] = Business_Addon_Options::getInstance()->getStoreName($item["storeid"]);
            $item["detail"] = $_detail;
        }
        $this->view->list = $list;
        //get store list
        $slist = Business_Addon_Options::getInstance()->getStoreName2($sid);
        $this->view->slist = $slist;
        
        
        $this->view->sid = $sid;
            
    }
    
    public function list2Action() {
        $keyword = "";
        $this->view->isAdmin = 0;
        $auth = Zend_Auth::getInstance();
        $identity = (array) $auth->getIdentity();
        $sid = $identity["username"];
        
        if ($sid == "vote_all") {
            $this->view->isAdmin = 1;
        } else {
            Business_Common_Utils::redirect("/admin/user/usedphone/list");
        }
        $this->view->keyword = $keyword = $this->_request->getParam("keyword");
        $from = $this->_request->getParam("from");
        $to = $this->_request->getParam("to");
        $cur_sid = $this->_request->getParam("vote_id");
        if ($cur_sid != null) {
            $sid = $cur_sid;
        } else {
            $sid = $identity["username"];
        }
//        echo "<pre>";
//        var_dump($cur_sid);
//        die();
            
        if ($from != null) {
            $this->view->from = $from;
            $froms = explode("/", $from);            
            $froms = array_reverse($froms);
            $from = implode("-",$froms);            
        }
        if ($to != null) {
            $this->view->to = $to;
            $tos = explode("/", $to);            
            $tos = array_reverse($tos);
            $to = implode("-",$tos);            
        }
            
        if ($keyword != null) {
            $list = Business_Addon_Usedphonecus::getInstance()->getList("vote_all",$keyword, "", $from, $to, "id desc");
        } else {
            $list = Business_Addon_Usedphonecus::getInstance()->getList($sid,$keyword, "", $from, $to, "id desc");
        }
        
        
        foreach($list as &$item) {
            $used_id = $item["addon_usedphone_id"];
            $_detail = Business_Addon_Usedphone::getInstance()->getDetail($used_id);
            $_detail["storename"] = Business_Addon_Options::getInstance()->getStoreName($item["storeid"]);
            $item["detail"] = $_detail;
                
            
            $infos = Business_Addon_Usedphoneinfo::getInstance()->getDetailByUsedID($used_id);
                
            $sellout_text = $this->getSelloutText($infos["sellout"]);
//            $sellout_text = ".";
            $_infos = "<p><b>Tình trạng</b>: ".$infos["info"]."</p>";
            $_infos .= "<p><b>Bảo hành</b>: ".$infos["warranty"]."</p>";
            $_infos .= "<p><b>Phụ kiện</b>: ".$infos["accessory"]."</p>";
            $_infos .= "<p><b>Bán ra</b>: ".$sellout_text."</p>";
            
            $item["detail2"] = $infos;
            $item["infos"] = $_infos;
            $item["info"] = $infos;
        }
        $this->view->list = $list;
        //get store list
        $slist = Business_Addon_Options::getInstance()->getStoreName2($sid);
        $this->view->slist = $slist;
        
        $this->view->sid = $sid;
            
    }
    
    private function getSelloutText($selloutid=2) {
        $arr = array(
            0 => "Like new",
            1 => "Mới 100%",
            2 => "SR",
        );
        return $arr[$selloutid];
    }
    public function deleteAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $id = $this->_request->getParam("id");
        $active = $this->_request->getParam("active");
        
        $auth = Zend_Auth::getInstance();
        $identity = (array) $auth->getIdentity();
        $sid = $identity["username"];
        
        Business_Addon_Usedphonecus::getInstance()->delete($id,$active,$sid);
    }
    public function updateItemidAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $id = $this->_request->getParam("id");
        $itemid = $this->_request->getParam("itemid");
        $data = array(
            "itemid" => $itemid,
            "datetime_update" => date("Y-m-d H:i:s")
        );
            
        Business_Addon_Usedphoneinfo::getInstance()->updateByUsedID($id, $data);
    }
    
    public function updateAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $id = $this->_request->getParam("id");
        $price = $this->_request->getParam("price");
        $price = str_replace(",", "", $price);
        $data = array(
            "sellout_price" => $price,
            "datetime_update" => date("Y-m-d H:i:s")
        );
            
        Business_Addon_Usedphoneinfo::getInstance()->updateByUsedID($id, $data);
        
        //get phone info
        $pdetail = Business_Addon_Usedphone::getInstance()->getDetail($id);
        $phone_name = $pdetail["name"];
        $store_name = $this->getStoreName($pdetail["storeid"]);
        
        //get detail phone info
        $pdetail2 = Business_Addon_Usedphoneinfo::getInstance()->getDetailByUsedID($id);
        $info = $pdetail2["info"];
        $warranty = $pdetail2["warranty"];
        $accessory = $pdetail2["accessory"];
        //send email to updater
        
       
    }
    
    public function restoreAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $id = $this->_request->getParam("id");
        Business_Addon_Usedphonecus::getInstance()->restore($id);
    }
    public function insertExpenditure($addon_usedphone_id){
        $_expenditure   = Business_Addon_Expenditure::getInstance();
        $detail = Business_Addon_Usedphone::getInstance()->getDetail($addon_usedphone_id);
        $__option = Business_Addon_Options::getInstance();
        $data["hnamvt"]         = $detail["hnamvt"];
        $data["title"]          = $detail["name"];
        $data["money"]          = $detail["price"];
        $data["fullcontent"]    = $detail["note"];
        $data["vote_id"]        = $detail["storeid"];
        $data["enabled"]        = 1;
        $data["created_date"]   = date('Y-m-d H:i:s');
        $data["userphoneid"]   = $addon_usedphone_id;
        $_expenditure->insert($data);
        
        $auid =  (int)$detail["auid"];
        if($auid >0){
            $date1 = $__option->get1Months($detail["datetime"]);
            if(strtotime($date1) > strtotime('now')){
                $sdata["status2"]=1;
                Business_Addon_UsersProducts::getInstance()->update2($auid, $sdata);
            }
        }
    }

    public function printAction() {
        $used_cus_id = (int) $this->_request->getParam("id");
        $sendmail = (int) $this->_request->getParam("sendmail",0);
        $this->view->sendmail = $sendmail;
        
        $detail = Business_Addon_Usedphonecus::getInstance()->getDetail($used_cus_id);
        $usedid = $detail["addon_usedphone_id"];
        $used_info = Business_Addon_Usedphoneinfo::getInstance()->getDetailByUsedID($usedid);
        if ($used_info["info"] == null || $used_info["warranty"]==null) {
            echo "<script>alert('Vui lòng cập nhật thông máy để in voucher!!!');window.location='/admin/user/usedphone/list'</script>";
            return;
        }
//        echo "<pre>";
//        var_dump($detail);
//        die();
//        Business_Addon_Usedphoneinfo::getInstance()->getDetail($id);
        $detail["datetime_expired"] = date("d/m/Y", strtotime($detail["datetime_expired"]));
        
        $this->view->detail = $detail;
       
            
//        if ($sendmail == 1) {  
            if (APP_ENV != "development") {
                $this->view->sendmail_url = "http://app.hnammobile.com/admin/user/usedphone/sendmail?id=".$used_cus_id;
                
                    
            } else {
                $this->view->sendmail_url = "http://dev.app.hnammobile.com/admin/user/usedphone/sendmail-dev?id=".$used_cus_id;
            }
//        }
//        $this->insertExpenditure($usedid);// phát sinh chi phí
//        $this->addhdmh($usedid); // thêm vào phần mua hàng
        
    }
    
    public function addhdmhAction(){
        $id = (int)  $this->_request->getParam("usesid");
        $itemid = (int)  $this->_request->getParam("lastid");
        
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        if($id >0){
            $this->addhdmh($id,$itemid);
            echo "susscess";
            die();
        }
    }
    public function addhdmh($id,$itemid){
        $__option = Business_Addon_Options::getInstance();
        if((int)$id >0){
            $detail_product = Business_Ws_ProductsItem::getInstance()->get_detail($itemid);
            $flag=0;
            if($detail_product["original_price"] >0){
               $flag=1; 
            }
            if($detail_product["productsid"] ==4){
               $flag=1; 
            }
            $data["isync"] = 1;
            $service =0;
            $data["service"] = 0;
            if($detail_product["productsid"]==10){
                $data["service"] = 1;
                $service =1;
                $data["isync"] = 2;
            }
            $data["productsid"] = $detail_product["productsid"];
            
            $detail = Business_Addon_Usedphone::getInstance()->getDetail($id);
            
            $auid =  (int)$detail["auid"];
            if($auid >0){
                $date1 = $__option->get1Months($detail["datetime"]);
                if(strtotime($date1) > strtotime('now')){
                    $sdata["status2"]=1;
                    Business_Addon_UsersProducts::getInstance()->update2($auid, $sdata);
                }
            }
            
            $data["itemid"] = $itemid;
            $data["ct"] = $detail["ct"];
            $data["colorid"] = $detail["colorid"];
            $data["imei"] = trim($detail["imei"]);
            $data["storeid"] = $detail["storeid2"];
            $data["userid"] = $detail["userid"];
            $data["price"] = (int)$detail["price"];
            $data["datetime"] = date('Y-m-d H:i:s');
            $data["enabled"] = 1;
            
            $data["type"] = 3;
            $data["block"] = 1;
            $data["flag"] = $flag;
            $data["note"] = $detail["note"];
            if($service ==1){
               $data["isync"] = 2; 
            }
            
            $imei = trim($detail["imei"]);
            $colorid =$detail["colorid"];
            $__color = Business_Addon_ProductsColor::getInstance();
            $__option = Business_Addon_Options::getInstance();
            
            $array_pass_thu = $__option->array_pass_thu();
            if(in_array($itemid, $array_pass_thu)){
                $sdetail = $__color::getInstance()->get_detail_by_id_color2($itemid,$colorid);
            }else{
                $sdetail = $__color::getInstance()->get_detail_by_id_color($itemid,$colorid);
            }
            
            $ma_vt = $sdetail["code"];
            $data["ma_vt"] = $ma_vt;
            
            Business_Addon_Purchase::getInstance()->insert($data);
            $__storeid = (int)  $this->_identity["parentid"];
            $detail_store = Business_Addon_MappingStore::getInstance()->get_detail_by_storeid($__storeid);
            if($flag ==0){
                $k = ".K.OLDX";
            }else{
                $k = ".C.OLDX";
            }
            $__ma_kho = $detail_store["id_fast_bp"].$k;
            $ma_kho = trim($__ma_kho);
            $_query2= "INSERT INTO `hnam_live`.`app_mapping_product` (`id`, `id_product`, `id_color`, `id_material`,`id_warehouse`, `imei`, `type`) VALUES (NULL, '$itemid', '$colorid', '$ma_vt', '$ma_kho', '$imei',0)";
            if($service ==0 ){
                if($_query2 != null){
                    Business_Addon_UsersProducts::getInstance()->excute($_query2);
                }
            }
        }
    }

        public function prints6Action() {
        $used_cus_id = (int) $this->_request->getParam("id");
        
        $detail = Business_Addon_Usedphonecus::getInstance()->getDetail($used_cus_id);
        $detail["datetime_expired"] = date("d/m/Y", strtotime($detail["datetime_expired"]));
        
        $cusid = $detail["addon_usedphone_id"];
        $detail2 = Business_Addon_Usedphone::getInstance()->getDetail($cusid);
        if ($detail2["vouchers6"]==1) {
            $change_name = "Samsung Galaxy S6";
			$detail2["vprice"] = 1500000;
        }
        if ($detail2["vouchers6"]==2) {
            $change_name = "Samsung Galaxy S6 Edge";
			$detail2["vprice"] = 1700000;
        }
        $this->view->change_name = $change_name;
        $this->view->detail2 = $detail2;
        $this->view->detail = $detail;
    }
    
    
    
    public function sendmailAction() {
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        $used_cus_id = (int) $this->_request->getParam("id");
        $used_cus = Business_Addon_Usedphonecus::getInstance()->getDetail($used_cus_id);
        
        if ($used_cus != null) {
            $used_id = $used_cus["addon_usedphone_id"];
            $used = Business_Addon_Usedphone::getInstance()->getDetail($used_id);
            
            
            
            //fix send mail
            if ($used["type_voucher"]==1) {
                return;
            }
            
            $used_info = Business_Addon_Usedphoneinfo::getInstance()->getDetailByUsedID($used_id);

            $phone_name = $used["name"];
            $IMEI = $used["imei"];
            $product_cate_name = $this->getProductCateName($used_info["product_cate"]);
            $color = $used_info["color"];
            $list_store = Business_Common_Users::getInstance()->getListByUname(FALSE);
            foreach ($list_store as $___store){
                $___storename[$___store["userid"]] = $___store["storename"];
                $___address[$___store["userid"]] = $___store["address"];
                $___phone[$___store["userid"]] = $___store["phone"];
            }
            $store_name = $___storename[$used["storeid2"]] ."  ".$___address[$used["storeid2"]]."  ".$___phone[$used["storeid2"]];
            $price = number_format($used["price"])."đ";
            $voucher = number_format($used_cus["price"])."đ";
            $info = $used_info["info"];
            $warranty = $used_info["warranty"];
            $accessory = $used_info["accessory"];
            $sellout_text = $this->getSelloutText($used_info["sellout"]);
            if ((int) $used_info["sellout_price"] > 0) {
                $sellout_price_text = number_format($used_info["sellout_price"])."đ";
            }
            $datetime = date("d/m/Y H:i:s",strtotime($used["datetime"]));
            $url = Globals::getBaseUrl() . "admin/user/usedphone/update-info?id=" . $used_id;
            
$msg = <<<HTMLCONTENT
<p style="line-height:200%;color:#ff6600; font-size:14px; font-weight:bold">
        Hnammobile - Hệ thống bán lẻ điện thoại di động chính hãng
        </p>
<p style="line-height:120%">
<b>Thông tin cập nhật máy cũ</b>
</p>
<p style="line-height:120%"><b>Ngày thu máy:</b> $datetime</p>
<p style="line-height:120%"><b>Tên máy:</b> $phone_name</p>
<p style="line-height:120%"><b>IMEI:</b> $IMEI</p>
<p style="line-height:120%"><b>Loại:</b> $product_cate_name</p>
<p style="line-height:120%"><b>Màu:</b> $color</p>
<p style="line-height:120%"><b>Giá thu vào:</b> $price</p>
<p style="line-height:120%"><b>Voucher phát hành:</b> $voucher</p>
<p style="line-height:120%"><b>Chi nhánh:</b> $store_name</p>
<p style="line-height:120%"><b>Tình trạng:</b> $info</p>
<p style="line-height:120%"><b>Bảo hành:</b> $warranty</p>
<p style="line-height:120%"><b>Phụ kiện:</b> $accessory</p>
<p style="line-height:120%"><b>Đề xuất bán ra:</b> $sellout_text</p>
<p style="line-height:120%"><b>Giá đề xuất bán ra:</b> $sellout_price_text</p>
<p style="line-height:200%"><b>Link đăng sản phẩm:</b> $url</p>
    
HTMLCONTENT;
    
	    $from = "khomaycu@hnammobile.com";
	    $displayname = "Hnammobile";
	    $replyto = $from;
	    
//	    $to = "nghi.dang@hnammobile.com";
//            $cc = array("dangvannghi37@gmail.com");
            $auth = Zend_Auth::getInstance();
            $identity = (array) $auth->getIdentity();
            $sid = $identity["username"];
        
//            $storeemail = Business_Helpers_Used::getStoreEmail($sid);
            if((int)$used["userid"] >0){
                $detail_user = Business_Common_Users::getInstance()->getDetail($used["userid"]);
            }
            $storeemail = trim($detail_user["email"]);
            
//            $storeemail = $___email[$used["storeid2"]];	
            $to = "duyhuy@hnammobile.com";		
            if($storeemail != NULL){
                $cc = array("kinhdoanh@hnammobile.com","nghi.dang@hnammobile.com","trongnhan@hnammobile.com","$storeemail","minhthinh@hnammobile.com");
            }else{
                $cc = array("kinhdoanh@hnammobile.com","nghi.dang@hnammobile.com","trongnhan@hnammobile.com","minhthinh@hnammobile.com");
            }
            $bcc="quynh.nguyen@hnammobile.com";			
            $subject = "Thông tin cập nhật máy cũ - " . $phone_name . " - " . $store_name;
            $body_html = $msg;  
            
            if ($used_info["sendmail"]!=1) {
                                
                $result = Business_Common_Utils::sendEmailV4($from, $displayname, $replyto, $to, $subject, $body_html, $file_attached,"used", $cc,$bcc);
                if ($result=="") {
                    //copy product
//                    $newid = $this->copyUsedAction($pids);
//                    $used_info["itemid"] = $newid;
                    $used_info["sendmail"] = 1;
                    Business_Addon_Usedphoneinfo::getInstance()->update($used_info["id"], $used_info);
                    echo "done";
                }
                $this->addhdmh($used_id, $used["itemid_tmp"]);
            }
            echo $result;
        }        
    }
    
    public function copyProductAction() {
        $itemid = $this->_request->getParam("itemid");
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        $this->copyUsedAction($itemid);
    }
    
    public function sendmail2Action() {
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        $used_cus_id = (int) $this->_request->getParam("id");
        $used_cus = Business_Addon_Usedphonecus::getInstance()->getDetail($used_cus_id);
        
        if ($used_cus != null) {
            $used_id = $used_cus["addon_usedphone_id"];
            $used = Business_Addon_Usedphone::getInstance()->getDetail($used_id);
            
            $used_info = Business_Addon_Usedphoneinfo::getInstance()->getDetailByUsedID($used_id);
//            echo "<pre>";
//            var_dump($used, $used_cus, $used_info);
//            die();
//                
            $phone_name = $used["name"];
            $IMEI = $used["imei"];
            $product_cate_name = $this->getProductCateName($used_info["product_cate"]);
            $color = $used_info["color"];
//            $store_name = $this->getFullStorename($used["storeid"]);
            $list_store = Business_Common_Users::getInstance()->getListByUname(FALSE);
            foreach ($list_store as $store){
                $___storename[$store["userid"]] = $store["storename"]; 
                $___email[$store["userid"]] = $store["email"]; 
            }
            $store_name = $___storename[$used["storeid2"]];
            
            
            $price = number_format($used["price"])."đ";
            $voucher = number_format($used_cus["price"])."đ";
            $info = $used_info["info"];
            $warranty = $used_info["warranty"];
            $accessory = $used_info["accessory"];
            $sellout_text = $this->getSelloutText($used_info["sellout"]);
            if ((int) $used_info["sellout_price"] > 0) {
                $sellout_price_text = number_format($used_info["sellout_price"])."đ";
            }
            $datetime = date("d/m/Y H:i:s",strtotime($used["datetime"]));
            $income_text = $this->getSelloutText($used["type"]);
            $url = Globals::getBaseUrl() . "admin/user/usedphone/update-info?id=" . $used_id;
$msg = <<<HTMLCONTENT
<p style="line-height:200%;color:#ff6600; font-size:14px; font-weight:bold">
        Hnammobile - Hệ thống bán lẻ điện thoại di động chính hãng
        </p>
<p style="line-height:120%">
<b>Thông tin cập nhật máy cũ</b>
</p>
<p style="line-height:120%"><b>Ngày thu máy:</b> $datetime</p>
<p style="line-height:120%"><b>Tên máy:</b> $phone_name</p>
<p style="line-height:120%"><b>IMEI:</b> $IMEI</p>
<p style="line-height:120%"><b>Loại:</b> $product_cate_name</p>
<p style="line-height:120%"><b>Màu:</b> $color</p>
<p style="line-height:120%"><b>Giá thu vào:</b> $price</p>
<p style="line-height:120%"><b>Voucher phát hành:</b> $voucher</p>
<p style="line-height:120%"><b>Chi nhánh:</b> $store_name</p>
<p style="line-height:120%"><b>Tình trạng:</b> $info</p>
<p style="line-height:120%"><b>Bảo hành:</b> $warranty</p>
<p style="line-height:120%"><b>Phụ kiện:</b> $accessory</p>
<p style="line-height:120%"><b>Đề xuất bán ra:</b> $sellout_text</p>
<p style="line-height:120%"><b>Giá đề xuất bán ra:</b> $sellout_price_text</p>
<p style="line-height:200%"><b>Link đăng sản phẩm:</b> $url</p>
HTMLCONTENT;
	    $from = "khomaycu@hnammobile.com";
	    $displayname = "Hnammobile";
	    $replyto = $from;
	    
            $_sid = $used["storeid"];
        
//            $storeemail = Business_Helpers_Used::getStoreEmail($_sid);
            if((int)$used["userid"] >0){
                $detail_user = Business_Common_Users::getInstance()->getDetail($used["userid"]);
            }
            $storeemail = $detail_user["email"];
			//var_dump($storeemail, $msg);die();
			$to = "duyhuy@hnammobile.com";			
            $cc = array("kinhdoanh@hnammobile.com","nghi.dang@hnammobile.com","trongnhan@hnammobile.com","$storeemail","minhthinh@hnammobile.com");			
            //$to = "nghi.dang@hnammobile.com";
            //$cc = array("dangvannghi37@gmail.com");
            $subject = "Thông tin cập nhật máy cũ - " . $phone_name . " - " . $store_name;
            $body_html = $msg;

            $result = Business_Common_Utils::sendEmailV4($from, $displayname, $replyto, $to, $subject, $body_html, $file_attached,"used", $cc);
            if ($result=="") {
                $used_info2 = Business_Addon_Usedphoneinfo::getInstance()->getDetailByUsedID($used_id);
                $used_info2["sendmail"] = 1;
                Business_Addon_Usedphoneinfo::getInstance()->update($used_info2["id"], $used_info2);
                echo "done";
            }
            echo $result;
        }
        
    }
    
    private function renderUsedDescription($used, $usedinfo) {
        if ($used == null || $usedinfo == null) return "";
//        $store_name = $this->getFullStorename($used["storeid"]);
        
        $list_store = Business_Common_Users::getInstance()->getListByUname(FALSE);
        foreach ($list_store as $store){
            $___storename[$store["userid"]] = $store["storename"]; 
            $___address[$store["userid"]] = $store["address"];
            $___phone[$store["userid"]] = $store["phone"];
        }
        $store_name = $___address[$used["storeid2"]]." <br/> Số điện thoại:".$___phone[$used["storeid2"]];
        $info = $usedinfo["info"];
        $warranty = $usedinfo["warranty"];
        $accessory = $usedinfo["accessory"];
        $color = $usedinfo["color"];
        
        $content = '
            <p><font size="4" color="#000099"><em><strong>Li&ecirc;n hệ 
            __STORE__
                để xem sản phẩm. </strong></em></font></p>
            <p>
                <font size="2"><strong>Màu:</strong> </font>
                __COLOR__
            </p>
            <p>
                <font size="2"><strong>Tình trạng:</strong> </font>
                __INFO__
            </p>
            <p>
                <font size="2"><strong>Bảo hành:</strong> </font>
                __WAR__
            </p>
            <p>
                <font size="2"><strong>Phụ kiện kèm theo:</strong> </font>
                __ACC__
            </p>
        ';
        
            
        $content = str_replace("__COLOR__", $color, $content);
        $content = str_replace("__STORE__", $store_name, $content);
        $content = str_replace("__INFO__", $info, $content);
        $content = str_replace("__WAR__", $warranty, $content);
        $content = str_replace("__ACC__", $accessory, $content);
            
        return $content;
        
    }
    
    public function sendmailDevAction() {
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        $used_cus_id = (int) $this->_request->getParam("id");
        $used_cus = Business_Addon_Usedphonecus::getInstance()->getDetail($used_cus_id);
        
        if ($used_cus != null) {
            $used_id = $used_cus["addon_usedphone_id"];
            $used = Business_Addon_Usedphone::getInstance()->getDetail($used_id);
            
            $used_info = Business_Addon_Usedphoneinfo::getInstance()->getDetailByUsedID($used_id);
//            echo "<pre>";
//            var_dump($used, $used_cus, $used_info);
//            die();
//                
            $phone_name = $used["name"];
            $IMEI = $used["imei"];
            $product_cate_name = $this->getProductCateName($used_info["product_cate"]);
            $color = $used_info["color"];
//            $store_name = $this->getFullStorename($used["storeid"]);
            $list_store = Business_Common_Users::getInstance()->getListByUname(FALSE);
            foreach ($list_store as $store){
                $___storename[$store["userid"]] = $store["storename"]; 
            }
            $store_name = $___storename[$used["storeid2"]];
            
            
            
            $price = number_format($used["price"])."đ";
            $voucher = number_format($used_cus["price"])."đ";
            $info = $used_info["info"];
            $warranty = $used_info["warranty"];
            $accessory = $used_info["accessory"];
            $sellout_text = $this->getSelloutText($used_info["sellout"]);
            if ((int) $used_info["sellout_price"] > 0) {
                $sellout_price_text = number_format($used_info["sellout_price"])."đ";
            }
            $datetime = date("d/m/Y H:i:s",strtotime($used["datetime"]));
//            $income_text = $this->getSelloutText($used["type"]);
            $url = Globals::getBaseUrl() . "admin/user/usedphone/update-info?id=" . $used_id;
$msg = <<<HTMLCONTENT
<p style="line-height:200%;color:#ff6600; font-size:14px; font-weight:bold">
        Hnammobile - Hệ thống bán lẻ điện thoại di động chính hãng
        </p>
<p style="line-height:120%">
<b>Thông tin cập nhật máy cũ</b>
</p>
<p style="line-height:120%"><b>Ngày thu máy:</b> $datetime</p>
<p style="line-height:120%"><b>Tên máy:</b> $phone_name</p>
<p style="line-height:120%"><b>IMEI:</b> $IMEI</p>
<p style="line-height:120%"><b>Loại:</b> $product_cate_name</p>
<p style="line-height:120%"><b>Màu:</b> $color</p>
<p style="line-height:120%"><b>Giá thu vào:</b> $price</p>
<p style="line-height:120%"><b>Voucher phát hành:</b> $voucher</p>
<p style="line-height:120%"><b>Chi nhánh:</b> $store_name</p>
<p style="line-height:120%"><b>Tình trạng:</b> $info</p>
<p style="line-height:120%"><b>Bảo hành:</b> $warranty</p>
<p style="line-height:120%"><b>Phụ kiện:</b> $accessory</p>
<p style="line-height:120%"><b>Đề xuất bán ra:</b> $sellout_text</p>
<p style="line-height:120%"><b>Giá đề xuất bán ra:</b> $sellout_price_text</p>
<p style="line-height:200%"><b>Link đăng sản phẩm:</b> $url</p>
HTMLCONTENT;
	    $from = "khomaycu@hnammobile.com";
	    $displayname = "Hnammobile";
	    $replyto = $from;
	    
            $_sid = $used["storeid"];
            
            $storeemail = Business_Helpers_Used::getStoreEmail($_sid);
                
			//var_dump($storeemail, $msg);die();
//            $to = "duyhuy@hnammobile.com";			
//            $cc = array("xuanan@hnammobile.com","kinhdoanh@hnammobile.com","nghi.dang@hnammobile.com","","$storeemail");			
            $to = "nghi.dang@hnammobile.com";
            $cc = array("dangvannghi37@gmail.com");
            $subject = "Thông tin cập nhật máy cũ - " . $phone_name . " - " . $store_name;
            $body_html = $msg;

            if ($used_info["sendmail"]!=1) {
                $result = Business_Common_Utils::sendEmailV4($from, $displayname, $replyto, $to, $subject, $body_html, $file_attached,"used", $cc);
                if ($result=="") {
                    $used_info2 = Business_Addon_Usedphoneinfo::getInstance()->getDetailByUsedID($used_id);
                    $used_info2["sendmail"] = 1;
                    Business_Addon_Usedphoneinfo::getInstance()->update($used_info2["id"], $used_info2);
                    echo "done";
                }                
            }
            echo $result;
        }
        
    }
    
    public function infoAction() {
        $used_cus_id = (int) $this->_request->getParam("id");
        $this->view->used_cus_id = $used_cus_id;
        $detail = Business_Addon_Usedphonecus::getInstance()->getDetail($used_cus_id);
        $detail2 = Business_Addon_Usedphone::getInstance()->getDetail($detail["addon_usedphone_id"]);
        
        $this->view->detail2 = $detail2;
        $this->view->usedid = $detail["addon_usedphone_id"];
        
        if ($this->_request->isPost()) {
		//ini_set('display_errors', '1');
            $buy = $this->_request->getParam("buy", -1);
            $used_cus_id = $this->_request->getParam("used_cus_id");
            $product_cate = $this->_request->getParam("product_cate");
            $color = $this->_request->getParam("color");
            $info = $this->_request->getParam("info");
            $warranty = $this->_request->getParam("warranty");
            $accessory = $this->_request->getParam("accessory");
            $sellout = $this->_request->getParam("sellout");
            $sellout_price = $this->_request->getParam("sellout_price","");
            if ($sellout_price != "") {
                $sellout_price = str_replace(",", "", $sellout_price);
            }
            $uids = $this->_request->getParam("usedid");
            
            $data = array();
            $data["addon_usedphone_id"] = $uids;
            $data["product_cate"] = $product_cate;
            $data["color"] = $color;
            $data["info"] = $info;
            $data["warranty"] = $warranty;
            $data["accessory"] = $accessory;
            $data["sellout"] = $sellout;
            $data["sellout_price"] = $sellout_price;
            $data["buy"] = $buy;
            $data["datetime"] = date("Y-m-d H:i:s");
                
            $_detail = Business_Addon_Usedphoneinfo::getInstance()->getDetailByUsedID($uids);
            if ($_detail == null) {
                Business_Addon_Usedphoneinfo::getInstance()->insert($data);
            } else {
                unset($data["datetime"]);
                Business_Addon_Usedphoneinfo::getInstance()->update($_detail["id"], $data);
            }
            $this->view->ok = "1";
            $this->view->usedid2 = $used_cus_id;
        }
    }
    
    private static function getProductCateName($id) {
        if ($id == 1) {
            return "Công ty";
        } 
        return "Xách tay";
    }
    private static function getExpiredTime($datetime) {
        $range = 30 * 24 * 60 * 60;
        $time = strtotime($datetime) + $range;
        
        return date("Y-m-d 00:00:00", $time);
    }
    
    private static function getVoucherPrice($price, $type, $type_voucher=0) {
        
        
        if ($type==5 || $type_voucher==1 || $type_voucher==10) {
            //5 ==> like new khong phat hanh voucher
            //1==>thong tin tam cho chuong trinh samsung 6 khong phat hanh voucher
            //10 ==> khong phat hanh voucher
            return 0;
        }
        $price2 = $price * 10 / 100;
        if ($price2 > 1000000) {
            $price2 = 1000000;
            return $price2/2;
        }        
        $remain = $price2 % 100000;
        $price2 = ($price2 - $remain)/2;
        return $price2;
    }

    public function historyAction(){
//        $this->_helper->viewRenderer->setNoRender();
        $this->view->layout()->disableLayout();
        $__option = Business_Addon_Options::getInstance();
        $imei = $this->_request->getParam("imei", 0);
        $list = Business_Addon_Usedphone::getInstance()->getListByIMEI($imei);
        foreach($list as  &$item){
            $item["price"] = number_format($item["price"]);
            $item["storename"] = $this->getStoreName($item["storeid"]);
        }
        if($imei != NULL){
            $detail = Business_Addon_UsersProducts::getInstance()->getDetailByImes($imei);
        }
        if($detail != NULL){
            $date1 = $__option->getNextDay2(30, $detail["create_date"]);
            $detail["date1"] = $date1;
        }
        $this->view->detail = $detail;
        $this->view->listh = $list;
        $auid = (int)$this->_request->getParam("auid");
        $this->view->auid = $auid;
    }
    
    public function saveAction() {
        $this->_helper->viewRenderer->setNoRender();
        $this->view->layout()->disableLayout();

        $name = $this->_request->getParam("name", "");
        
        $type = $this->_request->getParam("type", 0);
        $vouchers6 = $this->_request->getParam("vouchers6", 0);
        $type_voucher = $this->_request->getParam("type_voucher", 10);
        $hasvoucher = $this->_request->getParam($hasvoucher, 1);
        $storeid = $this->_request->getParam("storeid", 0);
        $pricekh = $this->_request->getParam("pricekh", 0);
        $colorid = $this->_request->getParam("colorid", 0);
        $imei = $this->_request->getParam("imei", 0);
        $itemid = $this->_request->getParam("itemid", 0);
        $note = $this->_request->getParam("note");
        $hnamvt = (int)$this->_request->getParam("hnamvt");
        $auid = (int)  $this->_request->getParam("auid");
        $ct = (int)  $this->_request->getParam("ct");
        $__option = Business_Addon_Options::getInstance();
        $ret = array();
        if($imei != NULL){
            $detail = Business_Addon_UsersProducts::getInstance()->getDetailByImes($imei);
            $____slist2 = Business_Addon_FASTTONKHOIMEI::getInstance()->get_list_by_malo3($imei);
            if($____slist2 != NULL){
                $err["id"] = "imei";
                $err["msg"] = "IMEI này đã có trong kho của hệ thống HNAMOBILE. Vui lòng liên hệ IT kiểm tra.";
                $ret[] = $err;
            }
        }
        if($detail != NULL){
            $date1 = $__option->getNextDay2(30, $detail["create_date"]);
            $__date1 = strtotime($date1);
        }
        $this->view->detail = $detail;
        $now = strtotime('now');
        if((int)$colorid ==0){
                $err["id"] = "colorid";
                $err["msg"] = "Vui lòng chọn màu của sản phẩm.";
                $ret[] = $err;
            }
        
        if($auid==0){
            if($__date1 >$now){
                $err['id']      = "imei";
                $err['msg']     = "IMEI này trong 30 ngày đổi trả. vui lòng vào phần mềm bán hàng để thu.";
                $ret[]          = $err;
            }
        }else{
            $ssdetail = Business_Addon_UsersProducts::getInstance()->getDetail($auid);
            if($ssdetail==NULL){
                $err['id']      = "imei";
                $err['msg']     = "BILL đã bị thu nên không thể thu thêm lần nửa. Vui lòng kiểm tra lại.";
                $ret[]          = $err;
            }
        }
        $__pname = explode("--", $name);
        $itemid = $__pname[2];
        if((int) $itemid >0 && (int)$colorid >0){
//            $detail_product = Business_Addon_MappingProduct::getInstance()->get_detail_by_product_color($itemid,$colorid);
            $array_pass_thu = $__option->array_pass_thu();
            if(in_array($itemid, $array_pass_thu)){
                $detail_product = Business_Addon_ProductsColor::getInstance()->get_detail_by_id_color2($itemid,$colorid);
            }else{
                $detail_product = Business_Addon_ProductsColor::getInstance()->get_detail_by_id_color($itemid,$colorid);
            }
            
            
            if($detail_product["code"] ==NULL || trim($detail_product["code"]) ==""){
                $err['id']      = "name";
                $err['msg']     = "Sản phẩm này chưa có mã vật tư trong hệ thống hnammobile, vui lòng liên hệ IT để tạo mã vật tư.";
                $ret[]          = $err;
            }
            if(strpos( $ssdetail["ma_vt"],"DV.") === FALSE){
                $__tonkhoimei = Business_Addon_FASTTONKHOIMEI::getInstance();
                $detail_tonkho = $__tonkhoimei->get_detail_by_malo($imei,0); 
                if($detail_tonkho !=NULL){
                    $err['id']      = "name";
                    $err['msg']     = "Sản phẩm này đang tồn kho nên không thể mua sản phẩm này vào tiếp.";
                    $ret[]          = $err;
                }
            }
        }
        
        if ($storeid == null || $imei == null || $pricekh == 0) {
            $err['id']      = "storeid";
            $err['msg']     = "Vui lòng kiểm tra lại giá, số IMEI";
            $ret[]          = $err;
        }
        
        if(count($ret) >0){
            echo json_encode($ret);
        }else{
            $pricekh = str_replace(",", "", $pricekh);
            $data = array();
            $data["storeid"] = $storeid;
            $data["ct"] = $ct;
            $data["storeid2"] = $this->_identity["parentid"];
            $data["userid"] = $this->_identity["userid"];
            $data["name"] = $__pname[0];
            $data["price"] = (int) $pricekh;
            $data["colorid"] = (int) $colorid;
            $data["type"] = $type;
            $data["imei"] = $imei;
            $data["vouchers6"] = $vouchers6;
            $data["type_voucher"] = $type_voucher;
            $data["datetime"] = date("Y-m-d H:i:s");
            $data["itemid_tmp"] = $itemid;
            $data["note"] = $note;
            $data["hnamvt"] = $hnamvt;
            $data["auid"] = $auid;
            $lastid = Business_Addon_Usedphone::getInstance()->insert($data);
            $err['id']      = "ok";
            $err['lastid']      = $lastid;
            $err['msg']     = "ok";
            $ret[]          = $err;
//            $url = 'http://app.hnammobile.com/admin/user/usedphone/customer?id='.$lastid;
//            header("Location: $url");
            echo json_encode($ret);
        }
    }

    public function getPriceAction() {
        $this->_helper->viewRenderer->setNoRender();
        $this->view->layout()->disableLayout();
        
        $name = $this->_request->getParam("name");
                
        $names = explode("--", $name);
        $itemid = (int) $names[1];
        $pdetail = Business_Ws_ProductsItem::getInstance()->getDetail($itemid); 
        $oprice = $pdetail["original_price"];
        $price = $pdetail["price"];
        $arr = array();
        $arr["price"]= $oprice>0? $oprice : $price;        
        $arr["itemid"]= $itemid;
        $arr["name"]= $names[0];
        
        echo json_encode($arr);
    }
    public function getPrice2Action() {
        $this->_helper->viewRenderer->setNoRender();
        $this->view->layout()->disableLayout();
        
        $name = $this->_request->getParam("name");
                
        $names = explode("--", $name);
        $itemid = (int) $names[2];
        $pdetail = Business_Ws_ProductsItem::getInstance()->getDetail($itemid); 
        $oprice = $pdetail["original_price"];
        $price = $pdetail["price"];
        $arr = array();
        $arr["price"]= $oprice>0? $oprice : $price;        
        $arr["itemid"]= $itemid;
        $arr["name"]= $names[0];
        
        echo json_encode($arr);
    }
    
    public function indexAction() {
        
        $auth = Zend_Auth::getInstance();
        $identity = (array) $auth->getIdentity();
        $storename = $this->getStoreName($identity["username"]);
        $auid = (int)$this->_request->getParam("auid");
        $pid = (int)$this->_request->getParam("pid");
        $__d = date('Ymd');
        if($auid >0){
            $token = $this->_request->getParam("token");
            $skeys ="ABCKMC";
            $ztoken = md5($skeys.$__d.$auid);
            if($token != $ztoken){
                die('No access');
            }
            $ssdetail = Business_Addon_UsersProducts::getInstance()->getDetail($auid);
            if($ssdetail==NULL){
                die('Bill da thu. nen khong the thu them lan nua. Vui long kiem tra lai');
            }
            
            $detail_price_by_color = Business_Addon_ProductsColor::getInstance()->get_detail_by_id_color($ssdetail["products_id"], $ssdetail["colorid"]);
            $this->view->price_new = $detail_price_by_color["price"];
        }
        $this->view->ssdetail = $ssdetail;
        
        $name_color = array();
        $list_color = Business_Ws_NewsItem::getInstance()->getListByCateId(722);
        foreach ($list_color as $val){
            $name_color[$val["itemid"]] = $val["title"];
        }
        
        $this->view->name_color = $name_color;
        $this->view->token = $token;
        $this->view->auid = $auid;
        $this->view->storename = $storename;
        $this->view->sid = $identity["username"];
        $_option = Business_Addon_Options::getInstance();
        
        $list_ct_thumaycu = $_option->ct_thumaycu();
        $this->view->list_ct_thumaycu= $list_ct_thumaycu;
        
        $this->view->list_hnamVt= $_option->getHnamVT();
        if ($this->_request->isPost()) {
            $this->view->layout()->disableLayout();
            $this->view->ispost = true;
            
            $name = $this->_request->getParam("name", "");
            //process product
            $names = explode("--", $name);
            $itemid = (int)$names[1];
            if ($itemid > 0) {
                //copy id
//                $pdetail = business
            }
            $imei = $this->_request->getParam("imei", "");
            $price = $this->_request->getParam("price", "");
            $price = str_replace(",", "", $price);
            $type = $this->_request->getParam("type", 0);
            $warranty = $this->_request->getParam("warranty", 0);
            $can = $this->_request->getParam("can", 0);
            $rot = $this->_request->getParam("rot", 0);
            $repair = $this->_request->getParam("repair", 0);
            
            $cable = $this->_request->getParam("cable", 0);
            $cable = str_replace(",", "", $cable);
            $charge = $this->_request->getParam("charge", 0);
            $charge = str_replace(",", "", $charge);
            $headphone = $this->_request->getParam("headphone", 0);
            $headphone = str_replace(",", "", $headphone);
            $note = $this->_request->getParam("note");
            $vouchers6 = $this->_request->getParam("vouchers6", 0);            

            $this->view->name = $name;
            $this->view->imei = $imei;
            $this->view->note = $note;
            $this->view->price = $price;
            $this->view->type = $type;
            $this->view->warranty = $warranty;
            $this->view->can = $can;
            $this->view->rot = $rot;
            $this->view->repair = $repair;
            
            
            $price_type = $this->_type[$type][$warranty];
            $price_can = $this->_canmop[$can];
            $price_rot = $this->_tray[$rot];
            $price_repair = $this->_repair[$repair];

            //fix for apple sieu hot
            if ($type==0) {
                //apple sieu hot, can rot -5%
                $price_can -= 5;
                if ($price_can <0) {    
                    $price_can=0;
                }
            }
            
            $price = $price - ($price_type * $price / 100);
            $price = $price - ($price_can * $price / 100);
            $price = $price - ($price_rot * $price / 100);
            $price = $price - ($price_repair * $price / 100);

            //tinh gia phu kien
            $price = $price - $cable;
            $price = $price - $charge;
            $price = $price - $headphone;
            
            $min_price = $price - ($price * 10 / 100);
            $max_price = $price + ($price * 5 / 100);
            $cur_price = $price;
            
            //fix for apple sieu hot
            if ($type==0) {
                $min_price = $min_price + (int)($min_price*10/100);
                $max_price = $max_price + (int)($max_price*10/100);
                $cur_price = $cur_price + (int)($cur_price*10/100);
            }
            //end fix for apple sieu hot            
            
            //update price for voucher s6
            if ($vouchers6!=0) {
                $_p_range = $this->getVoucherS6Price($min_price);
                $min_price = $min_price - $_p_range;
                
                $_p_range = $this->getVoucherS6Price($max_price);
                $max_price = $max_price - $_p_range;
                
                $_p_range = $this->getVoucherS6Price($cur_price);
                $cur_price = $cur_price - $_p_range;
            }
            //end update price for voucher s6
            
            $this->view->cur_price = number_format($cur_price);
            $this->view->min_price = number_format($min_price);
            $this->view->max_price = number_format($max_price);
            $this->view->finalprice = number_format($price);
            
            //update gia ban du kiến
            if ($price <=3000000) {
                //muc ban ra 15-20%
                $min_price_sell = $min_price + (int)($min_price*20/100);
                $max_price_sell = $max_price + (int)($max_price*20/100);
                $cur_price_sell = $cur_price + (int)($cur_price*20/100);                
            } else if ($price <=7000000) {
                $min_price_sell = $min_price + (int)($min_price*15/100);
                $max_price_sell = $max_price + (int)($max_price*15/100);
                $cur_price_sell = $cur_price + (int)($cur_price*15/100); 
            } else {
                $min_price_sell = $min_price + (int)($min_price*7/100);
                $max_price_sell = $max_price + (int)($max_price*7/100);
                $cur_price_sell = $cur_price + (int)($cur_price*7/100); 
            }
            $this->view->cur_price_sell = number_format($cur_price_sell);
            $this->view->min_price_sell = number_format($min_price_sell);
            $this->view->max_price_sell = number_format($max_price_sell);
            
        } else {
            //get all products name
//            $pname = Business_Ws_ProductsItem::getInstance()->getProductsNameWithID();
//            $this->view->pnames = $pname;
            $slist = Business_Ws_ProductsItem::getInstance()->getProducts2();
            if($auid>0 || $pid >0){
                foreach ($slist as $val){
                    $__products_id = explode("--", $val); 
                    if($__products_id[2] == $pid){
                        $slist2[]=$val;
                    }
                }
                $slist = $slist2;
            }
            
            $this->view->slist = $slist;
        }
    }
    
    private function getVoucherS6Price($price) {
        
        if ($price < 1000000) {
            return (int) $price*25/100;
        }
        
        if ($price < 2000000) {
            return (int) $price*20/100;
        }
        
        if ($price < 3000000) {
            (int) $price*15/100;
        }
        
        return 500000;
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
            "vote_all" => "ADMIN"
        );
        return $array[$username];
    }
    
    private function getFullStorename($username) {
	
        $listStore = Business_Helpers_Store::getInstance()->getList();        
		if ($username == "vote_778") {
			$username = "vote_776";
		}
        foreach($listStore as $store) {
            $title = $store["title"];
            $usernames = explode("_", $username);
            if (strpos($title, $usernames[1])!==false) {
                return $title;
            }
        }
        return "admin";
    }
    
    
    private function isAdmin() {
        $identity = (array) $auth->getIdentity();
        $sid = $identity["username"];
        
        if ($sid == "vote_all") {
            return 1;
        }
        return 0;
    }
    
    private function copyUsedAction($pids) {

        $cache = GlobalCache::getCacheInstance('ws');
        $cache->flushAll();

        $error = 0;
        $msg = "";
        $data = null;
        if ($pids == null || $pids == "") {
            $msg = "Vui lòng chọn sản phẩm để copy!";
            $error = 1;
            
            return 0;
        } else {
            $_products = Business_Ws_ProductsItem::getInstance();
            $pids = explode(",", $pids);

            foreach ($pids as $pid) {
                $detail = $_products->getDetail($pid);
                if (isset($detail['itemid']))
                    unset($detail['itemid']);

                //copy product
                $detail['title'] = $detail['title'] . " cũ";
                $detail['cateid']  = 53; //hardcode to cateid 53: kho may cũ
                $detail['quanlity']  = 1; 
                $lastid = $_products->insert($detail['productsid'], $detail['cateid'], $detail);

                //copyimage
                $thumb = json_decode($detail['thumb']);
                $home = $thumb->thumb1;
                $large = json_decode($thumb->thumb2);

                $path_home = BASE_PATH_V3 . Globals::getConfig("product_path_home") . "/" . $home;
                $path_thumb = BASE_PATH_V3 . Globals::getConfig("product_path_thumbnails") . "/" . $large[0];
                $path_detail = BASE_PATH_V3 . Globals::getConfig("product_path_details");

                $title = Business_Common_Utils::adaptTitleLinkURLSEO($detail['title']);
                $ext = Business_Common_Images::get_image_extension($home);

                $new_home = "";
                if (is_file($path_home)) {
                    $new_home = BASE_PATH_V3 . Globals::getConfig("product_path_home") . "/" . $title . rand(0, 100) . "." . $ext;
                    copy($path_home, $new_home);
                }
                $new_thumb = "";
                if (is_file($path_thumb)) {
                    $ext = Business_Common_Images::get_image_extension($home);
                    $new_thumb = BASE_PATH_V3 . Globals::getConfig("product_path_thumbnails") . "/" . $title . rand(0, 100) . "." . $ext;
                    copy($path_thumb, $new_thumb);
                }

                $new_details[0] = $new_thumb;
                if (count($large) > 1) {
                    for ($i = 1; $i < count($large); $i++) {
                        if (isset($large[$i]) && $large[$i] != "") {
                            $_path_detail = $path_detail . "/" . $large[$i];
                            if (is_file($_path_detail)) {
                                $ext = Business_Common_Images::get_image_extension($large[$i]);
                                $new_details[$i] = BASE_PATH_V3 . Globals::getConfig("product_path_details") . "/" . $title . rand(0, 100) . "." . $ext;
                                copy($_path_detail, $new_details[$i]);
                            }
                        } else {
                            $new_details[$i] = "";
                        }
                    }
                }
                
                //copy 360 & fullbox
                $path_360 = BASE_PATH_V3 . "/v4/360/";
                $path_360_src = $path_360 . $pid;
                $path_360_des = $path_360 . $lastid;
                if (is_dir($path_360_src)) {
                    mkdir($path_360_des);
                    for($k=1; $k<=36; $k++) {
                        $_s = $path_360_src . "/" . $k . ".jpg";
                        $_d = $path_360_des . "/" . $k . ".jpg";
                        copy($_s, $_d);
                    }
                }
                
                //copy fullbox
                $path_fullbox = BASE_PATH_V3 . "/v4/fullbox/";
                $path_fullbox_src = $path_fullbox . $pid . ".jpg";
                $path_fullbox_des = $path_fullbox . $lastid . ".jpg";
                if (is_file($path_fullbox_src)) {
                    copy($path_fullbox_src, $path_fullbox_des);
                }

                //copy spec
                $this->dupSpec($pid, $lastid);

                //copy flash
                $flash_path = BASE_PATH_V3 . "/uploads/flash/" . $pid . ".swf";
                $flash_path_des = BASE_PATH_V3 . "/uploads/flash/" . $lastid . ".swf";
                if (is_file($flash_path)) {
                    copy($flash_path, $flash_path_des);
                }
            }
            $error = 0;
            $msg = "Thành công";
            return $lastid;
        }
    }

    public function dupspecAction() {
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $from_id = $this->_request->getParam('from_id');
        $to_id = $this->_request->getParam('to_id');

        $this->dupSpec($from_id, $to_id);
        echo "Done.";
    }

    private function dupSpec($from_pid, $to_pid) {
        //copy spec detail
        $_addon = Business_Addon_Featuresdata::getInstance();
        $alist = $_addon->getListByPid($from_pid);
        $_addon->deleteByPid($to_pid);

        if (count($alist) > 0) {
            foreach ($alist as $item) {
                $item['pid'] = $to_pid;
                $_addon->insert($item['fid'], $to_pid, $item['parentid'], $item);
            }
        }
    }
}
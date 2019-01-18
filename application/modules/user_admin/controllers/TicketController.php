<?php

/**
 * User Admin Home Controller
 * @author: nghidv
 */
class User_Admin_TicketController extends Zend_Controller_Action {
    private $_identity;
    private $_sky='ticket2018';
    private $url_list;
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
        
        $this->url_list ="/admin/user/ticket/list";
        $this->view->url_list = $this->url_list;
        $this->view->url_change_regencyid =  "/admin/user/ticket/change-regencyid";
        $this->view->url_change_regencyid2 =  "/admin/user/ticket/change-regencyid2";
    }
    public function registerAction(){
        $idregency = $this->_identity["idregency"];
        $userid = $this->_identity["userid"];
        $date = date('dmY');
        $token = md5($userid.'dkticket'.$date);
        $this->view->token = $token;
        $this->view->userid = $userid;
        $checks =0;
        if($idregency==11 || $idregency==14){
            $checks=1;
        }
        $this->view->checks=$checks;
    }
    public function register2Action(){
        $userid = $this->_identity["userid"];
        $idregency = $this->_identity["idregency"];
        $date = date('dmY');
        $token = md5($userid.'dkticket'.$date);
        $this->view->token = $token;
        $this->view->userid = $userid;
        $checks =0;
        if($idregency || $idregency==14){
            $checks=1;
        }
        $this->view->checks=$checks;
    }
    public function assignAction(){
        $__option = Business_Addon_Options::getInstance();
        $data_frm = $this->_request->getParams("data_frm");
        header('Content-Type: text/html; charset=utf-8');
        $id = (int)  $this->_request->getParam("id");
        $token =  $this->_request->getParam("token");
        $userid = $this->_identity["userid"];
        $idregency = $this->_identity["idregency"];
        $bgd=0;
        if($__option->isBGD($idregency)){
            $bgd=1;
        }
        $date = date('dmY');
        $ztoken = md5($id.$this->_sky.$date);
        if($token != $ztoken){
            die('Not access');
        }
        $assign=0;
        if((int)$data_frm["regencyid"]>0){
            $assign=1;
        }
        $receiver = (int)$data_frm["receiver"];
        if($receiver==404){
            $receiver = 438;
        }
        if($id>0){
            $detail = Business_Addon_Ticket::getInstance()->getDetail($id);
            $ret = array();
            if($assign==1){
                if($detail==NULL){
                    $arr["id"] = "id";
                    $arr["msg"] = "Ticket không có thực. Vui lòng kiểm tra lại";
                    $ret[] = $arr;
                }else{
                    if($detail["status"]>2){
                        $arr["id"] = "id";
                        $arr["msg"] = "Ticket đã xử lý xong nên không thể assign cho người khác";
                        $ret[] = $arr;
                    }else{
                       if($bgd==0 && $detail["receiver"]!= $userid){
                            $arr["id"] = "id";
                            $arr["msg"] = "Ticket của đối tượng khác nên bạn không có quyền assign cho đối tượng khác";
                            $ret[] = $arr;
                       }else{
                           if((int)$data_frm["regencyid"]==0){
                                $arr["id"] = "regencyid";
                                $arr["msg"] = "Vui lòng chọn chức vụ";
                                $ret[] = $arr;
                            }
                            if($receiver==0){
                                $arr["id"] = "userid";
                                $arr["msg"] = "Vui lòng chọn nhân viên";
                                $ret[] = $arr;
                            }
                       } 
                    }
                }
            }
            $forward = $data_frm["forward"];
            if(count($ret) >0){
                for($i=0;$i<count($ret);$i++){
                    $msg = $ret[$i]['msg'];
                    $ids = $ret[$i]['id'];
                    echo "<script>window.parent.show_msg('$msg','$ids');</script>";
                    die();
                }
            }else{
                if($assign==1){
                    $data = $detail;
                    $data["assign"]=1;
                    $data["datetime_receiver"] = date('Y-m-d H:i:s');
                    Business_Addon_TicketHistory::getInstance()->insert($data);
                    $data2["regencyid"] = (int)$data_frm["regencyid"];
                    $data2["receiver"] = $receiver;
                    $data2["status"] = 1;
                    $data2["datetime"] = date('Y-m-d H:i:s');
                    Business_Addon_Ticket::getInstance()->update($detail["id"], $data2);
                    $this->send_mail($id, $receiver);
                }
                
                
                if($forward != NULL){
                    $data_history = $detail;
                    foreach ($forward as $fw){
                        if($fw>0){
                            $data_history["receiver"] = $fw;
                            $data_history["assign"] = 2;
                            Business_Addon_TicketHistory::getInstance()->insert($data_history);
                        }
                    }
                }
                $__msg ='Lưu thành công';
                $url = $_SERVER['HTTP_REFERER'];
                echo "<script>window.parent.show_success('$__msg','','$url');</script>";
                die();
            }
           
        }
        
    }
    public function savecommentAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        if ($this->_request->isPost()) {
            $__bs = Business_Addon_Comment::getInstance();
            $data_frm = $this->_request->getParams("data_comment");
            $itemid = (int)$this->_request->getParam('itemid');
            $ret = array();
            if($data_frm["content"]==NULL){
                $arr["id"] = "content";
                $arr["msg"] = "Vui lòng nhập nội dung";
                $ret[] = $arr;
            }else{
               if(strlen($data_frm["content"]) <6){
                    $arr["id"] = "content";
                    $arr["msg"] = "Vui lòng nhập nội dung, lớn hơn 10 ký tự";
                    $ret[] = $arr;
                } 
            }
           $__option = Business_Addon_Options::getInstance();
            $__file = $_FILES['files'];
            $expensions =array("jpg","gif","jpeg","png","doc","docx","xls","xlsx","pdf");
            $path = $this->get_path_ticket();
            $check_upload = $__option->upload($path,$expensions,$__file);
                
                
                
            if($check_upload[0]["msg"] !="ok"){
                foreach ($check_upload as $item){
                  $ret[] = $item;  
                }
            }else{
                $data["files"] = $check_upload[0]["name_files"];
            }
            $forward = $data_frm["forward"];
            if(count($ret) >0){
                for($i=0;$i<count($ret);$i++){
                    $msg = $ret[$i]['msg'];
                    $ids = $ret[$i]['id'];
                    echo "<script>window.parent.show_msg('$msg','$ids');</script>";
                    die();
                }
            }else{
                $data["content"] = trim($data_frm["content"]);
                $data["userid"] = $this->_identity["userid"];
                $data["enabled"] = 1;
                $data["status"] = 1;
                $data["type"] = 1;
                $data["itemid"] = $itemid;
                $data["pid"] = (int)$data_frm["pid"];
                if(strpos($data_frm["content"], '@') ===FALSE){
                    $data["pid"] = 0;
                }
                $data["datetime"] = date('Y-m-d H:i:s');
                $data["userid"] = $this->_identity["userid"];
                $lastid = $__bs->insert($data);
                $detail_ticket = Business_Addon_Ticket::getInstance()->getDetail($itemid);
                if($detail_ticket){
                    if($this->_identity["userid"] == $detail_ticket["userid"]){
                        $userid_to = $detail_ticket["receiver"];
                    }else{
                        $userid_to = $detail_ticket["userid"];
                    }
                    $this->send_mail_comment($lastid, $userid_to);
                }
                
                $__msg ='Lưu thành công';
                $url = $_SERVER['HTTP_REFERER'];
                echo "<script>window.parent.show_success('$__msg','','$url');</script>";
                die();
            }
        }
    }
    public function editdealineAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        if ($this->_request->isPost()) {
            
            $__option = Business_Addon_Options::getInstance();
            $data_frm = $this->_request->getParams("data_dealine");
            $id = (int)$this->_request->getParam('ids');
            $ret = array();
            $dealine = str_replace("/", "-", $data_frm["dealine_edit"]);
            if($dealine != NULL){
                $__dealine       =   date('Y-m-d',  strtotime($dealine));
            }
             $idregency = $this->_identity["idregency"];
             $userid = $this->_identity["userid"];
             $bgd=0;
             if($__option->isBGD($idregency)){
                 $bgd=1;
             }
            if(strtotime($__dealine)==FALSE){
                $arr["id"] = "dealine_edit";
                $arr["msg"] = "Vui lòng nhập ngày dealine của bạn";
                $ret[] = $arr;
            }else{
               if(strtotime($__dealine) <  strtotime('now')){
                    $arr["id"] = "dealine_edit";
                    $arr["msg"] = "Ngày dealine phải lớn hơn hoặc bằng ngày hiện tại";
                    $ret[] = $arr;
                } 
            }
            if($id>0){
                $detail = Business_Addon_Ticket::getInstance()->getDetail($id);
            }
            if($detail ==NULL){
                $arr["id"] = "dealine_edit";
                $arr["msg"] = "Ticket này không có thực";
                $ret[] = $arr;
            }else{
                if($bgd==0 && $detail["receiver"] != $userid){
                    $arr["id"] = "dealine_edit";
                    $arr["msg"] = "Bạn không có quyền để chỉnh sửa ticket này";
                    $ret[] = $arr;
                }
                if(strtotime($detail["dealine_edit"]) != FALSE){
                    $arr["id"] = "dealine_edit";
                    $arr["msg"] = "Ngày hoàn thành chỉ cho chép chỉnh sửa 1 lần duy nhất";
                    $ret[] = $arr;
                }
                if($detail["status"]>3){
                    $arr["id"] = "dealine_edit";
                    $arr["msg"] = "Ticket này đã xử lý nên không thể thao tác";
                    $ret[] = $arr;
                }
                
            }
            
            if(count($ret) >0){
                for($i=0;$i<count($ret);$i++){
                    $msg = $ret[$i]['msg'];
                    $ids = $ret[$i]['id'];
                    echo "<script>window.parent.show_msg('$msg','$ids');</script>";
                    die();
                }
            }else{
                
                $data["dealine_edit"] = $__dealine;
                Business_Addon_Ticket::getInstance()->update($id, $data);
                
                $__msg ='Lưu thành công';
                $url = $_SERVER['HTTP_REFERER'];
                echo "<script>window.parent.show_success('$__msg','','$url');</script>";
                die();
            }
        }
    }

    public function editAction(){
        $userid = $this->_identity["userid"];
        
        $date = date('dmY');
        $token = md5($userid.'chart-media'.$date);
        $this->view->token = $token;
        $this->view->userid = $userid;
        
        $list_regency = Business_Addon_Regency::getInstance()->getList();
        foreach ($list_regency as $r){
            if($r["id"]==42){
                continue;
            }
            $list_regency2[] = $r;
            $name_regency[$r["id"]] = $r["name"];
        }
        $this->view->name_regency = $name_regency; 
        $this->view->list_regency = $list_regency2; 
        
        header('Content-Type: text/html; charset=utf-8');
        $id = (int)  $this->_request->getParam("id");
        $__option = Business_Addon_Options::getInstance();
        $idregency = (int)  $this->_identity["idregency"];
        if($idregency == 10 || $idregency == 11 || $idregency == 12  || $idregency == 14 || $idregency == 18 || $idregency == 20    ){
            $z_dealine = date('d/m/Y');
        }else{
            
        }
        $this->view->z_dealine = $z_dealine;
        if ($this->_request->isPost()) {
            $__bs = Business_Addon_Ticket::getInstance();
            $__bs_history = Business_Addon_TicketHistory::getInstance();
            $data_frm = $this->_request->getParams("data_frm");
            $id = (int)$this->_request->getParam('id');
            $ret = array();
            if($data_frm["name"]==NULL){
                $arr["id"] = "name";
                $arr["msg"] = "Vui lòng nhập tiêu đề";
                $ret[] = $arr;
            }else{
               if(strlen($data_frm["name"]) <10){
                    $arr["id"] = "name";
                    $arr["msg"] = "Vui lòng nhập tiêu đề tường minh, lớn hơn 10 ký tự";
                    $ret[] = $arr;
                } 
            }
            if($data_frm["fullcontent"]==NULL){
                $arr["id"] = "fullcontent";
                $arr["msg"] = "Vui lòng nhập nội dung";
                $ret[] = $arr;
            }
            if((int)$data_frm["regencyid"]==0){
                $arr["id"] = "regencyid";
                $arr["msg"] = "Vui lòng chọn chức vụ";
                $ret[] = $arr;
            }
            $receiver = (int)$data_frm["receiver"];
            if($receiver==404){
                $receiver = 438;
            }
            if($receiver==0){
                $arr["id"] = "userid";
                $arr["msg"] = "Vui lòng chọn nhân viên";
                $ret[] = $arr;
            }
            $dealine = str_replace("/", "-", $data_frm["dealine"]);
            $__dealine       =   date('Y/m/d',  strtotime($dealine));
            if($__dealine =='1970/01/01'){
                $err['id'] = "dealine";
                $err['msg'] = "Vui lòng nhập ngày hợp lệ";
                $ret[] = $err;
            }else{
                $dates = date('Y-m-d');
//                $d = $__option->getNextDay2(3,$dates);
                if(strtotime($__dealine) < strtotime($dates)){
                    $err['id'] = "dealine";
                    $err['msg'] = "Ngày mong muốn hoàn thành phải lớn hơn ngày hiện tại";
                    $ret[] = $err;
                }
            }
            
            $__file = $_FILES['files'];
            $expensions =array("jpg","gif","jpeg","png","doc","docx","xls","xlsx","pdf");
            $path = $this->get_path_ticket();
            $check_upload = $__option->upload($path,$expensions,$__file);
                
                
                
            if($check_upload[0]["msg"] !="ok"){
                foreach ($check_upload as $item){
                  $ret[] = $item;  
                }
            }else{
                $data["files"] = $check_upload[0]["name_files"];
            }
            $forward = $data_frm["forward"];
            if(count($ret) >0){
                for($i=0;$i<count($ret);$i++){
                    $msg = $ret[$i]['msg'];
                    $ids = $ret[$i]['id'];
                    echo "<script>window.parent.show_msg('$msg','$ids');</script>";
                    die();
                }
            }else{
                $po = $__option->randomCode("0123456789", 6, 1);
                $data["name"] = trim($data_frm["name"]);
                $data["fullcontent"] = stripslashes($data_frm["fullcontent"]);
                $data["regencyid"] = (int)$data_frm["regencyid"];
                $data["receiver"] = $receiver;
                $data["dealine"] = date('Y-m-d',  strtotime($__dealine));
                $data["dealine_edit"] = date('Y-m-d',  strtotime($__dealine));
                $data["enabled"] = 1;
                $data["status"] = 1;
                $data["datetime"] = date('Y-m-d H:i:s');
                $data["userid"] = $this->_identity["userid"];
                $__po = "TK".$po;
                $data["po"] = $__po;
                $lastid = $__bs->insert($data);
                
                if($forward != NULL){
                    $data_history = $data;
                    $data_history["id"] = $lastid;
                    foreach ($forward as $fw){
                        if($fw>0){
                            $data_history["receiver"] = $fw;
                            $data_history["assign"] = 2;
                            $__bs_history->insert($data_history);
                        }
                    }
                }
                $this->send_mail($lastid, $receiver);
                $__msg ='Lưu thành công';
                echo "<script>window.parent.show_success('$__msg','','$this->url_list');</script>";
                die();
            }
        }
        $this->view->detail = $detail;
        $list_all_user = Business_Common_Users::getInstance()->get_list_ticket();
        $this->view->list_all_user = $list_all_user;
        
    }
    public function send_mail($id,$userid_to){
         // Gửi mail
        $__zwf_user = Business_Common_Users::getInstance();
        $_option = Business_Addon_Options::getInstance();
        $detail = Business_Addon_Ticket::getInstance()->getDetail($id);
        $detail_user = $__zwf_user->getDetail($userid_to);
        
        $to = $detail_user["email"];
        if($to == NULL){
            $to ="quynh.nguyen@hnammobile.com";
        }
        $subject =$detail["po"]."-".trim($detail["name"]);
        $po = $detail["po"];
        $date = date('dmY');
        $token = md5($id.$this->_sky.$date);
        
        
        
        $tieudess = $detail["po"]."-".trim($detail["name"]);
        
        $linkss = "http://app.hnammobile.com/admin/user/ticket/view?id=$id&token=$token";
        $linkss = urlencode($linkss);
        $bcc1 = array();
        switch ($detail["status"]) {
            case 1:
                $body_html ="Bạn có một ticket đang chờ xử lý.";
                $list_fw = Business_Addon_TicketHistory::getInstance()->get_list_by_assign($id);
                foreach ($list_fw as $fw){
                    if($fw["assign"]==2){
                        $array_receiver_fw[] = $fw["receiver"];
                    }
                }
                if($array_receiver_fw){
                    $str_receiver_fw = implode(",", $array_receiver_fw);
                    $list_user_fw = Business_Common_Users::getInstance()->getListById($str_receiver_fw);
                    foreach ($list_user_fw as $u_fw){
                        if($u_fw["email"] != NULL){
                            $bcc1[]= $u_fw["email"];
                        }
                    }
                }
                break;
            case 2:
                $body_html ="$po chuyển qua giai đoạn đang xử lý.";
                break;
            case 3:
                $body_html ="$po đã xử lý xong.";
                break;
            case 4:
                $body_html ="$po Từ chối xử lý vì chọn sai đối tượng.";
                break;
            default:
                $body_html ="Bạn có một ticket đang chờ xử lý.";
                break;
        }
        $body_html2 = $body_html;
        $xem_them ="Vui lòng xem lại link <a href='http://app.hnammobile.com/admin/user/ticket/view?id=$id&token=$token'>Tại đây</a>";
        $body_html .=$xem_them;
        
        $displayname = $po;
        $cc="";
        $bcc2 = array("quynh.nguyen@hnammobile.com");
        $bcc = array_merge($bcc1,$bcc2);
        $_option->sendMail($to, $subject, $displayname, $body_html, $cc, $bcc);
        if(APP_ENV != 'development'){
            $contentss = urlencode(stripslashes($body_html2));
            $url_mobile = "https://www.hnammobile.com/push/push-data?userid=$userid_to&title=$tieudess&content=$contentss&link=$linkss";
            Business_Common_Utils::getContentByCurl($url_mobile);
            
            if($detail["status"]==1){
                if($array_receiver_fw){
                    foreach ($array_receiver_fw as $fws){
                        $url_mobile2 = "https://www.hnammobile.com/push/push-data?userid=$fws&title=$tieudess&content=$contentss&link=$linkss";
                        Business_Common_Utils::getContentByCurl($url_mobile2);
                    }
                }
            }
            
        }
    }
    public function send_mail_comment($ids,$userid_to){
         // Gửi mail
        $__zwf_user = Business_Common_Users::getInstance();
        $_option = Business_Addon_Options::getInstance();
        $sdetail = Business_Addon_Comment::getInstance()->get_detail($ids);
        if($sdetail){
            $detail = Business_Addon_Ticket::getInstance()->getDetail($sdetail["itemid"]);
        }
        $detail_user = $__zwf_user->getDetail($userid_to);
        $id = $sdetail["itemid"];
        $to = $detail_user["email"];
        if($to == NULL){
            $to ="quynh.nguyen@hnammobile.com";
        }
        $subject =$detail["po"]."-".trim($detail["name"]);
        $po = $detail["po"];
        $date = date('dmY');
        $token = md5($id.$this->_sky.$date);
        
        
        
        $tieudess = $detail["po"]."-".trim($detail["name"]);
        
        $linkss = "http://app.hnammobile.com/admin/user/ticket/view?id=$id&token=$token";
        $linkss = urlencode($linkss);
        $bcc1 = array();
        $body_html = "Nội dung comment: ".$sdetail["content"];
        $body_html2 = $body_html;
        $xem_them =" . Vui lòng xem lại link <a href='http://app.hnammobile.com/admin/user/ticket/view?id=$id&token=$token'>Tại đây</a>";
        $body_html .=$xem_them;
        
        $displayname = $po;
        $cc="";
        $bcc2 = array("quynh.nguyen@hnammobile.com");
        $bcc = array_merge($bcc1,$bcc2);
        $_option->sendMail($to, $subject, $displayname, $body_html, $cc, $bcc);
        if(APP_ENV != 'development'){
            $contentss = urlencode(stripslashes($body_html2));
            $url_mobile = "https://www.hnammobile.com/push/push-data?userid=$userid_to&title=$tieudess&content=$contentss&link=$linkss";
            Business_Common_Utils::getContentByCurl($url_mobile);
            
//            if($detail["status"]==1){
//                if($array_receiver_fw){
//                    foreach ($array_receiver_fw as $fws){
//                        $url_mobile2 = "https://www.hnammobile.com/push/push-data?userid=$fws&title=$tieudess&content=$contentss&link=$linkss";
//                        Business_Common_Utils::getContentByCurl($url_mobile2);
//                    }
//                }
//            }
            
        }
    }

    public  function get_path_ticket() {
        $lastChar = strrchr(BASE_PATH, "/");     
        if ($lastChar !== "/") {
            $basePath = BASE_PATH . "/";
        }
        $basePath = $basePath . "uploads/ticket";
        return sprintf("%s", $basePath);
    }
    public function listAction(){
        $__bs = Business_Addon_Ticket::getInstance();
        $userid = $this->_identity["userid"];
        $userids = $this->_identity["userid"];
        $this->view->userid = $userid;
        $idregency = (int)  $this->_identity["idregency"];
        $dealine = (int)  $this->_request->getParam("dealine");
        $this->view->dealine = $dealine;
        $__option = Business_Addon_Options::getInstance();
        $dates = date('Y-m-d');
        $d = $__option->getPrevDay2(7,$dates);
        $start_end           = $this->_request->getParam("start_end");
        if($start_end ==null){
           $start_end = date("F j, Y",  strtotime($d))." - ".date("F j, Y"); 
        }
        $this->view->start_end = $start_end;
        $start  = $__option->getStartDate($start_end);
        $end  = $__option->getEndDate($start_end);
        $this->view->start = $start;
        $this->view->end = $end;
        
        $status = $this->_request->getParam("status");
        
        if($status==NULL){
            $status=1;
        }
        $this->view->status = $status;
        $bgd = 0;
        if($__option->isBGD($idregency)){
            $userid=0;
        }
        if($idregency==41){
            $bgd=1;
            $regencyid = (int)$this->_request->getParam("regencyid");
            $userid = $this->_identity["userid"];
            if($regencyid>0){
                $userid=0;
            }
        }
        $list_assign = Business_Addon_TicketHistory::getInstance()->get_list_assign_by_receiver($userids,$start,$end); 
        $this->view->list_assign = $list_assign;
        $this->view->regencyid = $regencyid;
        $this->view->bgd = $bgd;
        $list = $__bs->get_list_by_userid($userid,$status,$regencyid,"",$start,$end);
        $date = date('dmY');
        foreach ($list as &$val){
            $val["token"] = md5($val["id"].$this->_sky.$date);
            $array_userid[] = $val["userid"];
            $array_userid[] = $val["receiver"];
        }
        
//        $list_assign = Business_Addon_TicketHistory::getInstance()->get_list_by_assign_by_receiver($userids,$start,$end);
//        $this->view->list_assign = $list_assign;
        foreach ($list_assign as $val2){
            $array_userid[] = $val2["userid"];
            $array_userid[] = $val2["receiver"];
        }
        
        if($array_userid){
            $array_userid = array_unique($array_userid);
            $str_userid = implode(",", $array_userid);
            $list_user = Business_Common_Users::getInstance()->getListById($str_userid);
            foreach ($list_user as $user){
                $array_fullname[$user["userid"]] = $user["fullname"];
            }
        }
        $this->view->array_fullname = $array_fullname;
        
        if($dealine>0){
            foreach ($list as $vals){
                $time_dealine = $vals["dealine"]." 23:59:59";
                
//                $time_dealine = $vals["dealine"];
                if($vals["status"]==1 || $vals["status"]==2){ // chưa xử lý và đang xử lý
                    if(strtotime($time_dealine) < strtotime('now')){
                        $list_dealine[] = $vals;
                    }
                }
                if($vals["status"]==3){ // Đang xử lý
                    if(strtotime($time_dealine) < strtotime($vals["datetime_receiver"])){
                        $list_dealine[] = $vals;
                    }
                }
            }
            $this->view->list = $list_dealine;
        }else{
           $this->view->list = $list; 
        }
        
        $list_status = $__option->get_status();
        $this->view->list_status = $list_status;
        $list_regency = Business_Addon_Regency::getInstance()->getList();
        foreach ($list_regency as $r){
            if($r["id"]==42){
                continue;
            }
            $list_regency2[] = $r;
        }
        $this->view->list_regency = $list_regency2;
        
        
        
    }
    public function changestatusAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $status = (int)  $this->_request->getParam("status");
        $id = (int)  $this->_request->getParam("id");
        $__bhsc = Business_Addon_Ticket::getInstance();
        $detail = $__bhsc->getDetail($id);
        if($detail != NULL && $status>0){
            if($detail["status"] <3){
                $data["datetime_receiver"] = date('Y-m-d H:i:s');
                $data["status"] =$status;
                $__bhsc->update($id, $data);
                Business_Addon_TicketHistory::getInstance()->insert($detail);
                $this->send_mail($id, $detail["userid"]);
                
            }
        }
        header("location:".$_SERVER['HTTP_REFERER']);
        exit();
    }
    public function delAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $id = (int)  $this->_request->getParam("id");
        $__bhsc = Business_Addon_Ticket::getInstance();
        $detail = $__bhsc->getDetail($id);
        if($detail != NULL){
            if($detail["status"] ==1){
                $data["enabled"] =0;
                $__bhsc->update($id, $data);
            }
        }
        header("location:".$_SERVER['HTTP_REFERER']);
        exit();
    }
    public function changeRegencyidAction(){
        $this->_helper->Layout()->disableLayout();
        $regencyid = (int)$this->_request->getParam("regencyid"); 
        if($regencyid >0){
            $list = Business_Common_Users::getInstance()->get_list_by_kh(0, $regencyid);
            foreach ($list as $val){
                if($val["userid"] == $this->_identity["userid"]){
                    continue;
                }
                $list2[] = $val;
            }
        }
        if($list2 == NULL){
            die();
        }
        $this->view->list = $list2;
        $this->view->userid = $this->_identity["userid"];
    }
    public function changeRegencyid2Action(){
        $this->_helper->Layout()->disableLayout();
        $regencyid = (int)$this->_request->getParam("regencyid"); 
        if($regencyid >0){
            $list = Business_Common_Users::getInstance()->get_list_by_kh(0, $regencyid);
            foreach ($list as $val){
                if($val["userid"] == $this->_identity["userid"]){
                    continue;
                }
                $list2[] = $val;
            }
        }
        if($list2 == NULL){
            die();
        }
        $this->view->list = $list2;
    }
    public function viewAction(){
        $__option = Business_Addon_Options::getInstance();
        header('Content-Type: text/html; charset=utf-8');
        $id = (int)  $this->_request->getParam("id");
        $token =  $this->_request->getParam("token");
        $userid = $this->_identity["userid"];
        $idregency = $this->_identity["idregency"];
        $bgd=0;
        $date = date('dmY');
        if($id>0){
            $detail = Business_Addon_Ticket::getInstance()->getDetail($id);
        }else{
            die('Not found');
        }
        if($__option->isBGD($idregency)){
            $bgd=1;
        }else{
            $ztoken = md5($id.$this->_sky.$date);
            if($detail["userid"] != $userid){
                if($token != $ztoken){
                    die('Not access');
                }
            }
        }
        $this->view->bgd= $bgd;
        
        
        $this->view->token = $token;
        $this->view->userid = $userid;
        if($id>0){
            $detail = Business_Addon_Ticket::getInstance()->getDetail($id);
            if($detail){
                $detail_regency = Business_Addon_Regency::getInstance()->getDetailById($detail["regencyid"]);
                $receiver=$detail["receiver"];
                $us=$detail["userid"];
                $u_r ="$receiver,$us";
                $list_assign = Business_Addon_TicketHistory::getInstance()->get_list_by_assign($id);
                foreach ($list_assign as $as){
                    $receiver_assign[] = $as["receiver"];
                }
                if($receiver_assign){
                    $receiver_assign2 = implode(",", $receiver_assign);
                    $u_r ="$u_r,$receiver_assign2";
                }
                
                $list_comment = Business_Addon_Comment::getInstance()->get_list_by_itemid($id);
                foreach ($list_comment as $cm){
                    $array_userid[] = $cm["userid"];
                    if((int)$cm["pid"]==0){
                        $list_comment1[] = $cm;
                    }else{
                        $list_comment2[$cm["pid"]][] = $cm;
                    }
                }
                if($array_userid){
                    $str_userid = implode(",", $array_userid);
                    $u_r ="$u_r,$str_userid";
                }
                
                $list_user = Business_Common_Users::getInstance()->getListById($u_r);
                foreach ($list_user as $u){
                    if($u["avatar"]==NULL){
                        $u["avatar"]="8.jpg";
                    }
                    $array_fullnames[$u["userid"]] = $u["fullname"];
                    $array_idregency[$u["userid"]] = $u["idregency"];
                    $array_idstrore[$u["userid"]] = $u["parentid"];
                    $array_avatar[$u["userid"]] = "http://app.hnammobile.com/uploads/profile/".$u["avatar"];
                }
            }
            
            
            
            
        }
        $this->view->array_avatar =$array_avatar;
        $this->view->list_comment =$list_comment;
        $this->view->list_comment1 =$list_comment1;
        $this->view->list_comment2 =$list_comment2;
        $this->view->array_idregency =$array_idregency;
        $this->view->array_idstrore =$array_idstrore;
        
        $this->view->list_assign =$list_assign;
        $this->view->detail_regency =$detail_regency;
        $this->view->array_fullnames =$array_fullnames;
        $this->view->detail =$detail;
        
        $list_status = $__option->get_status();
        $this->view->list_status = $list_status;
        
        
        $list_regency = Business_Addon_Regency::getInstance()->getList();
        foreach ($list_regency as $r){
            if($r["id"]==42){
                continue;
            }
            $list_regency2[] = $r;
            $name_regency[$r["id"]] = $r["name"];
        }
        
        $this->view->name_regency = $name_regency; 
        $this->view->list_regency = $list_regency2; 
        if($detail["status"] <=2){
            if($detail["receiver"] == $userid || $bgd==1){
                $this->_helper->viewRenderer('view-assign');
                $list_all_user = Business_Common_Users::getInstance()->get_list_ticket();
                $this->view->list_all_user = $list_all_user;
            }
        }
        $list_storeid = Business_Common_Users::getInstance()->getListByUname(FALSE);
        foreach ($list_storeid as $store){
            $storename[$store["userid"]] = $store["storename"];
        }
        $this->view->storename = $storename;
    }
    
}
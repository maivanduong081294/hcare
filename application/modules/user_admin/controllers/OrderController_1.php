<?php

/**
 * Hnam Controller
 * @author: nghidv
 */
class User_Admin_OrderController extends Zend_Controller_Action {

    private $_identity;
    private $_default_menu = "1";
    private $_plist = array(
                    "1" => "Trong kho",
                    "2" => "Đã bán",
                );
    public function init() {
        // do something
        
        $auth = Zend_Auth::getInstance();
        $identity = $auth->getIdentity();
        $this->_identity = (array) $auth->getIdentity();
        $this->view->full_name = $identity->fullname;
        if ($identity != null) {
            $username = $identity->username;
            $this->view->username = $username;
            $this->view->user_name = $username;
        }
        $userid = (int)  $this->_identity["userid"];
        if($userid ==0){
            BlockManager::setLayout('admin_login');
        }else{
            BlockManager::setLayout('appbh');
        }
    }
    public function excelAction(){
        error_reporting(0);
        ini_set('display_errors', false); 
        ini_set('display_startup_errors', false); 
        date_default_timezone_set('Europe/London');
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $id = (int)$this->_request->getParam("id");
        Business_Common_Utils::getExcelOrder($id);
        
    }

    public function pdfAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
//        include("/pdf/mpdf60/mpdf.php");
        include("/zserver/lib/mpdf60/mpdf.php");
        $id = $this->_request->getParam("id");
        $token = $this->_request->getParam("token");
        $_secKey = "HNAM_ORDER_2016";
        $_ztoken = md5($id.$_secKey);
        if($token != $_ztoken){
            die('You do not have access.');
        }
        $html = Business_Common_Utils::gethtmlOrder($id);
        //$style_data = file_get_contents("style_current.css");
        //$frontmatter_data = file_get_contents("frontmatter_current.html");

        // Create new PDF with font subsetting, 234mm wide, 297mm high
        $mpdf = new mPDF('s', array(234,297));

        // Make it DOUBLE SIDED document with 4mm bleed
        $mpdf->mirrorMargins = 1;
        $mpdf->bleedMargin = 4;

        //$mpdf->WriteHTML($style_data, 1);

        //$mpdf->WriteHTML($frontmatter_data, 2);
        
        $mpdf->WriteHTML($html, 2);
        $mpdf->SetTitle("Đặt hàng Hnammobile.com");
        $mpdf->SetAuthor("Hnammobile.com");
        $mpdf->SetCreator("Booktype 2.0 and mPDF 6.0");
        $mpdf->SetSubject("");
        $mpdf->SetKeywords("");
        $filename = "hnam-order-".date('dmY').".pdf";
        $mpdf->Output($filename,"D");
    }

    public function example2Action(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        include 'simplexlsx.class.php';
        $xlsx = new SimpleXLSX('imei.xlsx');
        echo '<h1>$xlsx->rows()</h1>';
        echo '<pre>';
        print_r( $xlsx->rows() );
        echo '</pre>';
    }
    public function saveViewsCkAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $_order = Business_Addon_Order::getInstance();
        $id = (int)$this->_request->getParam("id");
        $query ="UPDATE `hnam_order_ck` SET views = 1 where id = $id";
        if($id >0){
            $_order->excute($query);
        }
        
    }

    public function saveCkAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $_order = Business_Addon_Order::getInstance();
        $ret = array();
        $arr = array();
        $ids = $this->_request->getParam("idsk");
        $ht = $this->_request->getParam("ht");
        $completes = $this->_request->getParam("completes");
        $money = $this->_request->getParam("money");
        $note = $this->_request->getParam("note");
        $note2 = $this->_request->getParam("note2");
        $note2 = trim($note2);
        $username = $this->_identity["username"];
        $idhd = $this->_request->getParam("idhd");
        $end_datetime = $this->_request->getParam("end_datetime");
        
        for($i=0;$i<count($ids);$i++){
            $money[$i] = str_replace(",", "", $money[$i]);
            if($end_datetime[$i] !=null){
                $end_datetime[$i] = str_replace("/", "-", $end_datetime[$i]);
                $end_datetime[$i] = date('Y-m-d',  strtotime($end_datetime[$i]));
            }
            $_query[] = "UPDATE `hnam_order_ck` SET `money` = '$money[$i]', note = '$note[$i]', note2 = '$note2[$i]',end_creator ='$username',end_datetime = '$end_datetime[$i]',ht='$ht[$i]',completes='$completes[$i]',idhd='$idhd[$i]' WHERE `id` = '$ids[$i]'";
        }
        if($_query != null){
            $query = implode(";", $_query);
            $_order->excute($query);
        }
        
        $arr["id"] ="ok";
        $arr["msg"] ="ok";
        $ret[]= $arr;
        echo json_encode($ret);
    }

    public function ckAction(){
        $_order = Business_Addon_Order::getInstance();
        $_order_detail = Business_Addon_OrderDetail::getInstance();
        $_option = Business_Addon_Options::getInstance();
        $_vendor  = Business_Import_Vendor::getInstance();
        $list_vendor = $_vendor->getList();
        $p = $this->_request->getParam("p");
        $this->view->list_vendor = $list_vendor;
        $idvendor = (int)$this->_request->getParam("idvendor");
        $this->view->idvendor = $idvendor;
        
        $start_end           = $this->_request->getParam("start_end");
        if($start_end ==null){
           $start_end = date("F j, Y")." - ".date("F j, Y"); 
        }
        $this->view->start_end = $start_end;
        $start  = $_option->getStartDate($start_end);
        $end  = $_option->getEndDate($start_end);
        $this->view->start = $start;
        $this->view->end = $end;
        
        
        
        $idregency = $this->_identity["idregency"];
        $uid = $this->_identity["userid"];
        $_zwfuser = Business_Common_Users::getInstance();
        if($_option->isBGD($idregency) || $idregency ==37 || $idregency ==38){
            $uid = (int)$this->_request->getParam("userid");
        }
        
        $list = $_order->getListByDayUserId($uid,$start,$end,$idvendor,1);
        
        
        
        foreach ($list as $items){
            if($items["userid"] !=null){
                $str_userid[] = $items["userid"];
            }
            if($items["id"] !=null){
                $_orderid[] = $items["id"];
            }
            
            
        }
        
        
        
        if($p==1){
            echo "<pre>";
            var_dump($_orderid,$str_userid);
            die();
        }
        
        if($str_userid!=null){
            $strs_userid = implode(",", $str_userid);
            $fullname_userid = array();
            if($strs_userid != null){
                $l_userid = $_zwfuser->getListByUserid($strs_userid);
                foreach ($l_userid as $items){
                    $fullname_userid[$items["userid"]] = $items["fullname"];
                }
            }
            
        }
        
        $this->view->fullname_userid = $fullname_userid;
        
        
        if($_orderid !=null){
            $orderid = implode(",", $_orderid);
            $slist = $_order_detail->getListByOrderId($orderid);
            
            $_ck = Business_Addon_OrderCk::getInstance();
            $list_ck = $_ck->getListByOrderId($orderid);
            $money_ck = array();
            foreach ($list_ck as $items){
                $money_ck[$items["orderid"]] += $items["money"];
            }
            $this->view->money_ck = $money_ck;
        }
        $_pidname = array();
        $full_imei = 1;
        $f_imei = array();
        foreach ($slist as $items){
            $_pid[] = $items["pid"];
            $f_imei[$items["orderid"]][] = $items["status_imei"];

            if($items["status_imei"] ==0){
                $full_imei = 0;
            }
        }
        
        $_pid = array_unique($_pid);
        if($_pid !=null ){
            $pid = implode(",", $_pid);
            $_productitem = Business_Ws_ProductsItemWh::getInstance();
            $plist = $_productitem->getListByProductsID2($pid);
        }
        
        $name_pid = array();
        foreach ($plist as $items){
            $name_pid[$items["itemid"]] = $items["title"];
        }
        foreach ($slist as $items){
            $_pidname[$items["orderid"]][] = $name_pid[$items["pid"]];
        }
        
        foreach ($list as &$items){
            $items["status_imei"] = 1;
            foreach ($f_imei[$items["id"]] as $_item){
                if($_item ==0){
                    $items["status_imei"] =0;
                }
            }
        }
        
        $this->view->pid_name = $_pidname;
        $this->view->list = $list;
    }
    public function editCkAction(){
        $id = $this->_request->getParam("id");
        $_order = Business_Addon_Order::getInstance();
        $zwf_user = Business_Common_Users::getInstance();
        $_vendor = Business_Import_Vendor::getInstance();
        $_option = Business_Addon_Options::getInstance();
        $token = $this->_request->getParam("token");
        $_secKey = "HNAM_ORDER_2016";
        $_ztoken = md5($id.$_secKey);
        if($token != $_ztoken){
            die('You do not have access.');
        }
        $_ck = Business_Addon_OrderCk::getInstance();
        $list = $_ck->getListByOrderId($id);
        $this->view->list = $list;
        $detail = $_order->getDetail($id);
        $this->view->detail= $detail;
        $detail_user = $zwf_user->getDetail($detail["userid"]);
        $this->view->detail_user = $detail_user;
        $detail_vendor = $_vendor->getDetail($detail["supplier_id"]);
        $this->view->detail_vendor = $detail_vendor;
        $completes = $_option->getCompletes();
        $this->view->completes = $completes;
        $ht = $_option->getHTs();
        $this->view->ht = $ht;
                
    }

    public function uploadAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $_order = Business_Addon_Order::getInstance();
        $id = (int)  $this->_request->getParam("id");
        $detail = $_order->getDetail($id);
        $img = $detail["img"];
        $array_img = explode(",", $img);
//        $name_img = $detail["img"];
        $__file = $_FILES['image'];
            if(isset($__file)){
                for($i=0;$i<count($__file);$i++){
                    $errors= array();
                    $file_name = $__file['name'][$i];
                    if($file_name != null){
                        $file_size =$__file['size'][$i];
                        $file_tmp =$__file['tmp_name'][$i];
                        $file_type  =$__file['type'][$i];
                        $file_ext=strtolower(end(explode('.',$__file['name'][$i])));


                        if (strpos($file_type, "image")===false) {
                                $errors[]= "Vui lòng chọn file";
                            }
                        $expensions= array("jpeg","jpg","png","gif");

                        if(in_array($file_ext,$expensions)=== false){
                           $errors[]="File upload không đúng định dạng (jpeg,jpg,png,gif)";
                        }
                        if($file_size > 5097152){
                           $errors[]='Dung lượng vượt quá 5MB';
                        }
                        $path =  $this->getPathOrder2();
                        if (!is_dir($path)) {
                            mkdir($path, 0777, true);
                        }

                        $name_img = $id ."_".$i. "." . $file_ext; 

                        $newFile = $path . "/".$id ."_".$i. "." . $file_ext;
        //                if($detail["status"] ==2){
        //                    $errors[]= ' Bạn đã hoàn tất đơn hàng nên không thể upload';
        //                }
                        if(empty($errors)==true){
                           move_uploaded_file($file_tmp,$newFile);
                           if($id>0){
//                               if(!in_array($name_img, $array_img)){
                                   $data_img[] = $name_img;
//                               }

                            }
                        }else{
                           for($i=0;$i<count($errors);$i++){
                                echo "<script>window.parent.uploadthatbai('Upload thất bại: $errors[$i]');</script>";
                                return;
                            }

                        }
                    }
                }
                if($data_img !=null){
                    if($img==null){
                        $data["img"] = implode(",", $data_img);
                    }else{
                        $data["img"] = $img.",".implode(",", $data_img);
                    }
                    $_order->update($id, $data);
                    
                }
                echo "<script>window.parent.uploadthanhcong('Upload thành công');</script>";
             }
    }

    public function saveUploadAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $_order = Business_Addon_Order::getInstance();
        $id = (int)  $this->_request->getParam("detail_orderid2");
        $_column = (int)$this->_request->getParam("column");
        $_total = (int)$this->_request->getParam("total_imei2");
        if($_column ==0){
            $_column =1;
        }
        $col = $_column -1;
        if(isset($_FILES['file'])){
            $errors= array();
            $file_name = $_FILES['file']['name'];
            $file_size =$_FILES['file']['size'];
            $file_tmp =$_FILES['file']['tmp_name'];
            $file_type  =$_FILES['file']['type'];
            $file_ext=strtolower(end(explode('.',$_FILES['file']['name'])));
            
            $expensions= array("xls","xlsx");

            if(in_array($file_ext,$expensions)=== false){
               $errors[]="File upload không đúng định dạng (jpeg,jpg,png,gif)";
            }
            if($file_size > 5097152){
               $errors[]='Dung lượng vượt quá 5MB';
            }
//            $createdate = date('Y-m-d');
//            $path =  $this->getPathOrder2($createdate);
            $path =  $this->getPathOrderImei();
            if (!is_dir($path)) {
                mkdir($path, 0777, true);
            }
            
            $newFile = $path . "/".$id . "." . $file_ext;
//            $newFile = $path . "/".$id . "." . $file_ext;
            if(empty($errors)==true){
               move_uploaded_file($file_tmp,$newFile);
            }else{
                for($i=0;$i<count($errors);$i++){
                    echo "<script>window.parent.uploadthatbai('Upload thất bại: $errors[$i]');</script>";
                }
                
                 return;
//               print_r($errors);
            }
         }
         $now = date('Y-m-d H:i:s');
         if($file_ext == "xlsx"){
             include 'simplexlsx.class.php';
             $xlsx = new SimpleXLSX($newFile);
             $data = $xlsx->rows();
             foreach ($data as $items){
                 $imeis[] = $items[$col];
             }
         }else{
             if($file_ext == "xls"){
                 include 'excel_reader2.php';
                 $data = new Spreadsheet_Excel_Reader($newFile);
                for($i=0;$i<$_total*2;$i++){
                    $im = $data->val($i,$_column);
                    if($im !=null){
                        $imeis[] = $im;
                    }
                }
             }
         }
          if(count($imeis) < $_total){
                 echo "<script>window.parent.uploadthatbai('Upload thất bại.Số lượng imei thấp hơn số lượng quy định');</script>";
                 return;
             }
         // insert
         for($i=0;$i<$_total;$i++){
            $_query[] = "(NULL, '$id', '$imeis[$i]', '1', '$now','')";
        }
        if($_query != null){
            $query = implode(",", $_query);
            $sql = "INSERT INTO `hnam_order_detail_imei` (`id`, `detail_orderid`, `imei`, `enabled`, `datetime`, `end_datetime`) VALUES $query";
            $_order->excute($sql);
            $sdata["status_imei"] = 1;
            Business_Addon_OrderDetail::getInstance()->update($id, $sdata);
        } 
         echo "<script>window.parent.uploadthanhcong('Upload thành công');</script>";
    }
    
    public function listAction(){
        $_order = Business_Addon_Order::getInstance();
        $_order_detail = Business_Addon_OrderDetail::getInstance();
        $_option = Business_Addon_Options::getInstance();
        $_vendor  = Business_Import_Vendor::getInstance();
        $list_vendor = $_vendor->getList();
        
        $this->view->list_vendor = $list_vendor;
        $idvendor = (int)$this->_request->getParam("idvendor");
        $this->view->idvendor = $idvendor;
        $idregency = $this->_identity["idregency"];
        $uid = $this->_identity["userid"];
        $bgd=0;
        if($_option->isBGD($idregency) || $idregency ==16){
            $bgd=1;
            $uid = 0;
        }
        $this->view->bgd = $bgd;
        $start_end           = $this->_request->getParam("start_end");
        if($start_end ==null){
           $start_end = date("F j, Y")." - ".date("F j, Y"); 
        }
        $this->view->start_end = $start_end;
        $start  = $_option->getStartDate($start_end);
        $end  = $_option->getEndDate($start_end);
        $this->view->start = $start;
        $this->view->end = $end;
        $list = $_order->getListByDayUserId($uid,$start,$end,$idvendor,1);
        $_zwfuser = Business_Common_Users::getInstance();
        foreach ($list as $items){
            if($items["userid"] !=null){
              $str_userid[] = $items["userid"];  
            }
            if($items["id"] !=null){
                $_orderid[] = $items["id"];
            }
            
            
        }
        if($str_userid!=null){
            $strs_userid = implode(",", $str_userid);
            $fullname_userid = array();
            $l_userid = $_zwfuser->getListByUserid($strs_userid);
            foreach ($l_userid as $items){
                $fullname_userid[$items["userid"]] = $items["fullname"];
            }
        }
        
        $this->view->fullname_userid = $fullname_userid;
        if($_orderid != null){
            $orderid = implode(",", $_orderid);
            $slist = $_order_detail->getListByOrderId($orderid);
            $_pidname = array();
            $full_imei = 1;
            $f_imei = array();
            foreach ($slist as $items){
                $_pid[] = $items["pid"];
                $f_imei[$items["orderid"]][] = $items["status_imei"];

                if($items["status_imei"] ==0){
                    $full_imei = 0;
                }
            }

            $_pid = array_unique($_pid);
            $pid = implode(",", $_pid);
            
            $_productitem = Business_Ws_ProductsItemWh::getInstance();
            if($pid !=null){
                $plist = $_productitem->getListByProductsID2($pid);
                $name_pid = array();
                foreach ($plist as $items){
                    $name_pid[$items["itemid"]] = $items["title"];
                }
                foreach ($slist as $items){
                    $_pidname[$items["orderid"]][] = $name_pid[$items["pid"]];
                }
            }
            
        }
        

        foreach ($list as &$items){
            $items["status_imei"] = 1;
            foreach ($f_imei[$items["id"]] as $_item){
                if($_item ==0){
                    $items["status_imei"] =0;
                }
            }
        }
        
        $this->view->pid_name = $_pidname;
        $this->view->list = $list;
    }
    public function editAction() {
        $_option = Business_Addon_Options::getInstance();
        $zwf_user = Business_Common_Users::getInstance();
        $_vendor = Business_Import_Vendor::getInstance();
        $ldiscount = $_option->getTypeDiscount();
        $this->view->ldiscount = $ldiscount;
        $lvendor = $_vendor->getList();
        $this->view->lvendor = $lvendor;
        $id = (int)  $this->_request->getParam("id");
        $userid = (int)  $this->_identity["userid"];
        if($userid ==0){
            $token = $this->_request->getParam("token");
            $_secKey = "HNAM_ORDER_2016";
            $_ztoken = md5($id.$_secKey);
            if($token != $_ztoken){
                die('You do not have access.');
            }
        }else{
            $fname = $zwf_user->getDetail($userid);
            $this->view->fname = $fname["fullname"];
            $this->view->fphone = $fname["phone"];
            $this->view->femail = $fname["email"];
        }
        
        $productsid = $_option->getCateHnamNew();
        $this->view->productsid = $productsid;
    }
    public function editVendorAction(){
        $uri = $this->_request->getRequestUri();
        $this->view->uri = $uri;
        $_option = Business_Addon_Options::getInstance();
        $idregency = (int)  $this->_identity["idregency"];
        $bgd =0;
        if($_option->isBGD($idregency) || $idregency == 33 || $idregency ==34){
            $bgd =1;
        }
        if($idregency >0){
            $this->_helper->viewRenderer('edit-vendor-admin');
        }
        $this->view->bgd = $bgd;
        $id = $this->_request->getParam("id");
        $token = $this->_request->getParam("token");
        $_secKey = "HNAM_ORDER_2016";
        $_ztoken = md5($id.$_secKey);
        if($token != $_ztoken){
            die('You do not have access.');
        }
        
        $ldiscount = $_option->getTypeDiscount();
        $this->view->ldiscount = $ldiscount;
        $zwf_user = Business_Common_Users::getInstance();
        $_order = Business_Addon_Order::getInstance();
        $_order_detail = Business_Addon_OrderDetail::getInstance();
        $_newitem = Business_Ws_NewsItem::getInstance();
        $detail = $_order->getDetail($id);
        if($detail["enabled"] ==0){
            $this->_redirect('/admin');
        }
        $this->view->detail = $detail;
        $detail_user = $zwf_user->getDetail($detail["userid"]);
        $this->view->detail_user = $detail_user;
        
        $list_order = $_order_detail->getListByOrderId($detail["id"]);
        $this->view->list_order = $list_order;
        foreach ($list_order as $items){
            $_pid[] = $items["pid"];
            $_colorid[] = $items["pid_color"];
        }
        $_colorid = array_unique($_colorid);
        $colorid = implode(",", $_colorid);
        
        $_pid = array_unique($_pid);
        $pid = implode(",", $_pid);
        $_productsitem = Business_Ws_ProductsItemWh::getInstance();
        $list_products = $_productsitem->getListByProductsID($pid);
        $name_pid = array();
        foreach ($list_products as $items){
            $name_pid[$items["itemid"]] = $items["title"];
        }
        $this->view->name_pid = $name_pid;
        
        $name_color = array();
        $list_color = $_newitem->getListByItemid($colorid);
        
        foreach ($list_color as $items){
            
            $name_color[$items["itemid"]] = $items["title"];
        }
        $this->view->name_color = $name_color;   
        
        $path = $this->getPathOrder2();
        
        $arr_img = explode(",", $detail["img"]);
        foreach ($arr_img as $key=> &$__items){
            $__items = "/uploads/order/".$__items;
        }
        if($detail["img"] ==null){
            $arr_img[] = "/images/admin/no-image.jpg";
        }
        $this->view->img = $arr_img;
        $productsid = $_option->getCateHnamNew();
        $this->view->productsid = $productsid;
//        $display='';
//        if($idregency >0){
            $display ='none';
//        }
        $this->view->displays = $display;
    }

    public function delAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $id = $this->_request->getParam("id");
        $data["enabled"] = 0;
        Business_Addon_OrderDetail::getInstance()->update($id, $data);
    }
    public function udelAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $id = $this->_request->getParam("id");
        $data["enabled"] = 0;
        $data["end_del"] = $this->_identity["username"];
        Business_Addon_Order::getInstance()->update($id, $data);
    }
    public function uresAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $id = $this->_request->getParam("id");
        $data["enabled"] = 1;
        $data["end_del"] = $this->_identity["username"];
        Business_Addon_Order::getInstance()->update($id, $data);
    }

    public function restoreAction(){
            $this->_helper->Layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            $id = $this->_request->getParam("id");
            Business_Addon_Sim::getInstance()->restore($id);
    }
    public function isValid($ret, $price) {
        if ($price == 0) {
            $err['id'] = "price";
            $err['msg'] = "Lỗi!\nGiá.";
            $ret[] = $err;
        }
        return $ret;
    }

    public function saveAction() {
            $url = Globals::getBaseUrl();
            $this->_helper->Layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            $_option = Business_Addon_Options::getInstance();
            $_order = Business_Addon_Order::getInstance();
            $_order_detail = Business_Addon_OrderDetail::getInstance();
            $id = (int)$this->_request->getParam("id");
            $productsid = $this->_request->getParam("productsid");
            $pid = $this->_request->getParam("pid");
            $pid_color = $this->_request->getParam("pid_color");
            $accounting_name = $this->_request->getParam("accounting_name");
            $total = $this->_request->getParam("total");
            $_price = $this->_request->getParam("price");
            $price = str_replace(",", "", $_price);
            $supplier_id = $this->_request->getParam("supplier_id");
            $supplier_name = $this->_request->getParam("supplier_name");
            $staff_supplier = $this->_request->getParam("staff_supplier");
            $supplier_phone = $this->_request->getParam("supplier_phone");
            $supplier_email = $this->_request->getParam("supplier_email");
            $note = $this->_request->getParam("note");
            $note = trim($note);
            $day = date("y.m.d"); 
            
            $po_ncc = str_pad($supplier_id, 3, '0', STR_PAD_LEFT);
            $star = date('Y-m-d 00:00:00');
            $end = date('Y-m-d 23:59:59');
            
            $inday = $_order->getListByDay($star,$end,$supplier_id);
            $stt =str_pad(count($inday)+1, 3, '0', STR_PAD_LEFT);
            
            $startday = date('Y-m-01');
            $endday = date('Y-m-').date('t', mktime(0, 0, 0, date('m'), 1, date('Y')));
            
            $inmonth = $_order->getListByDay($startday,$endday,$supplier_id);
            $stt_in_month =str_pad(count($inmonth)+1, 3, '0', STR_PAD_LEFT);
//            PO-01-16.03.30.01.05
            $po = "PO-$po_ncc-$day.$stt.$stt_in_month";
            $_discount = $this->_request->getParam("discount");
            $type_discount = $this->_request->getParam("type_discount");
            
            $ret = array();
            if($supplier_id ==0){
                $err['id'] = "supplier_id";
                $err['msg'] = "Thông báo ! \nVui lòng lựa chọn nhà cung cấp";
                $ret[] = $err; 
            }
            
            if ($supplier_email == null) {
                $err['id'] = "supplier_email";
                $err['msg'] = "Lỗi ! \nVui lòng nhập địa chỉ email sales nhà cung cấp";
                $ret[] = $err;
            }
            if ($supplier_email != null && !$_option->isValidEmail($supplier_email)) {
                $err['id'] = "supplier_email";
                $err['msg'] = "Lỗi ! \nVui lòng nhập địa chỉ email hợp lệ";
                $ret[] = $err;
            }
            for($i=0;$i<count($productsid);$i++){
                if($productsid[$i] ==0){
                    $err['id'] = "productsid".$i;
                    $err['msg'] = "Thông báo ! \nVui lòng chọn loại";
                    $ret[] = $err; 
                }
                if($pid[$i] ==0){
                    $err['id'] = "pid".$i;
                    $err['msg'] = "Thông báo ! \nVui lòng chọn tên sản phẩm";
                    $ret[] = $err; 
                }
                if($pid_color[$i] ==0){
                    $err['id'] = "pid_color".$i;
                    $err['msg'] = "Thông báo ! \nVui lòng chọn màu sắc của sản phẩm";
                    $ret[] = $err; 
                }
                if($accounting_name[$i] ==Null){
                    $err['id'] = "accounting_name".$i;
                    $err['msg'] = "Thông báo ! \nVui lòng chọn tên kế toán của sản phẩm.\nNếu không có tên kế toán vui lòng liên hệ kế toán";
                    $ret[] = $err; 
                }
                if($total[$i] ==0){
                    $err['id'] = "total".$i;
                    $err['msg'] = "Thông báo ! \nVui lòng nhập số lượng của sản phẩm";
                    $ret[] = $err; 
                }
//                if($price[$i] ==0){
//                    $err['id'] = "price".$i;
//                    $err['msg'] = "Thông báo ! \nVui lòng nhập đơn giá của sản phẩm";
//                    $ret[] = $err; 
//                }
            }
            
            
            
//            $_ret = $_option->checkPhone($supplier_phone,"supplier_phone");
//            $ret = array_merge($ret,$_ret);
            
            if($note ==null){
                $err['id'] = "note";
                $err['msg'] = "Thông báo ! \nVui lòng nhập nội dụng chiết khấu vào đây";
                $ret[] = $err; 
            }else{
                if(strlen($note)<20){
                   $err['id'] = "note";
                    $err['msg'] = "Thông báo ! \nGhi chú phải lớn hơn 20 ký tự";
                    $ret[] = $err; 
                }
            }
            
            if (count($ret) > 0) {
                echo json_encode($ret);
            } else {
                $now = date('Y-m-d H:i:s');
                $fullnames = $this->_identity["fullname"];
                $phones = $this->_identity["phone"];
                if($id==0){
                    // insert
                    $data["staff_supplier"] = $staff_supplier;
                    $data["note"] = $note;
                    $data["datetime"] = $now;
                    $data["creator"] = $this->_identity["username"];
                    $data["supplier_id"] = $supplier_id;
                    $data["supplier_name"] = $supplier_name;
                    $data["supplier_phone"] = $supplier_phone;
                    $data["supplier_email"] = $supplier_email;
                    $data["userid"] = $this->_identity["userid"];
                    $data["status"] = 1;
                    $data["enabled"] = 1;
                    $data["po"] = $po;
                    $orderid = $_order->insert($data);
                    $_secKey = "HNAM_ORDER_2016";
                    $token = md5($orderid.$_secKey);
                    //insert detail
                    for($i=0;$i<count($productsid);$i++){
                        if($type_discount[$i] == 1 || $type_discount[$i] ==3){
                            $discount[$i] = str_replace(",", "", $_discount[$i]);
                        }else{
                            $discount[$i] = str_replace(",", ".", $_discount[$i]);
                        }
                        $_query[]= "(NULL, '$orderid', '$productsid[$i]', '$pid[$i]', '$pid_color[$i]', '$accounting_name[$i]', '$now', '1', '$total[$i]', '$price[$i]', '$discount[$i]', '$type_discount[$i]')";
                    }
                    $query = implode(",", $_query);
                    $sql = "INSERT INTO `hnam_order_detail` (`id`, `orderid`, `productsid`, `pid`, `pid_color`, `accounting_name`, `datetime`, `enabled`, `total`, `price`, `discount`, `type_discount`) VALUES $query";
                    $_order->excute($sql);
                    
                    // send mail
                        $mail_config = "smtp.mailgun.org;587;postmaster@hnammobile.vn;vannghi@123@098";
                        $from = "no-reply@hnammobile.vn";
                        $displayname = $supplier_name;
                        $replyto = $from;
                        $subject = "Đặt hàng từ nhà cung cấp ".$supplier_name." - ".$po;
                        $body_html = 'Dear anh/chị '.$staff_supplier.'<br/>
                        Hnammobile gửi đơn đặt hàng, anh/chị vui lòng kiểm tra và cập nhật đơn giá theo liên kết bên dưới.<br/>
                        <a target="_blank" href='.$url.'admin/user/order/edit-vendor?id='.$orderid.'&token='.$token.'>Xem đơn hàng</a> <br/>
                        
                        Mọi thắc mắc vui lòng liên hệ:<br/>

                        Họ tên: '.$fullnames.'<br/>
                        Số điện thoại: '.$phones.'';
                        
                        $to = $supplier_email;
                        if($to == null){
                            $to = "duyhuy@hnammobile.com";
                        }
                        $cc = "";
                        $bcc = array("nghi.dang@hnammobile.com","quynh.nguyen@hnammobile.com","duyhuy@hnammobile.com");
                        Business_Common_Utils::sendEmailV4($from, $displayname, $replyto, $to, $subject, $body_html, $file_attached="",$mail_config,$cc,$bcc);
                    
                    
                }else{
                    //update
                }
                
                
                $err['id'] = "ok";
                $err['msg'] = "ok";
                $ret[] = $err;
                echo json_encode($ret);
            }
    }
    public function saveImeiAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $_order = Business_Addon_Order::getInstance();
        $detail_orderid = (int)$this->_request->getParam("detail_orderid");
        $imei = $this->_request->getParam("imei");
        $listimei = $this->_request->getParam("listimei");
        $total_imei = (int)$this->_request->getParam("total_imei");
        if ($listimei != null) {
            $limei = explode("\r\n", $listimei);
        }
        for($i=0;$i<count($limei);$i++){
            if($limei[$i] == null){
                continue;
            }else{
                $slist[] = trim($limei[$i]);
            }
        }
        $ids = $this->_request->getParam("ids");
        $ret = array();
        $arr = array();
        if(count($ids)==0){
            if(count($slist) ==0){
                $arr["id"] = "listimei";
                $arr["msg"] = "Thông báo!.\nVui lòng nhập imei";
                $ret[] = $arr;
            }
            if(count($slist) < $total_imei){
                $arr["id"] = "listimei";
                $arr["msg"] = "Thông báo!.\nVui lòng nhập đủ số lượng imei";
                $ret[] = $arr;
            }
        }
        
        
        if(count($ret)>0){
            echo json_encode($ret);
            return;
        }else{
            $now = date('Y-m-d H:i:s');
            if(count($ids)==0){
                for($i=0;$i<count($slist);$i++){
                    $_query[] = "(NULL, '$detail_orderid', '$slist[$i]', '1', '$now','')";
                }
                if($_query != null){
                    $query = implode(",", $_query);
                    $sql = "INSERT INTO `hnam_order_detail_imei` (`id`, `detail_orderid`, `imei`, `enabled`, `datetime`, `end_datetime`) VALUES $query";
                    $_order->excute($sql);
                    
                    $sdata["status_imei"] = 1;
                    Business_Addon_OrderDetail::getInstance()->update($detail_orderid, $sdata);
                    
                } 
            }else{
                //update
                for($i=0;$i<$total_imei;$i++){
                    $_query2[] = "update `hnam_order_detail_imei` set imei = '$imei[$i]',end_datetime = '$now' where id = $ids[$i]";
                }
                if($_query2 != null){
                    $sql2 = implode(";", $_query2);
                    $_order->excute($sql2);
                }
            }
            
            $arr["id"] = "ok";
            $arr["msg"] = "ok";
            $ret[] = $arr;
            echo json_encode($ret);
        }
    }

    public function saveVendorAction() {
            $url = Globals::getBaseUrl();
            $this->_helper->Layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            $_option = Business_Addon_Options::getInstance();
            $_order = Business_Addon_Order::getInstance();
            
            $id = (int)$this->_request->getParam("id");
            $idorder = $this->_request->getParam("idorder");
            $totals = $this->_request->getParam("totals");
            $_prices = $this->_request->getParam("prices");
            $prices = str_replace(",", "", $_prices);
            $supplier_name = $this->_request->getParam("supplier_name");
            $staff_supplier = $this->_request->getParam("staff_supplier");
            $supplier_phone = $this->_request->getParam("supplier_phone");
            $supplier_email = $this->_request->getParam("supplier_email");
            $note = $this->_request->getParam("note");
            $link = $this->_request->getParam("link");
            // insert
            $productsid = $this->_request->getParam("productsid");
            $pid = $this->_request->getParam("pid");
            $pid_color = $this->_request->getParam("pid_color");
            $accounting_name = $this->_request->getParam("accounting_name");
            $total = $this->_request->getParam("total");
            $_price = $this->_request->getParam("price");
            $price = str_replace(",", "", $_price);
           
            $_discount = $this->_request->getParam("discount");
            $type_discount = $this->_request->getParam("type_discount");
            
            $ret = array();
            $detail =$_order->getDetail($id);
            
//            if($detail["status"] ==2){
//                $err['id'] = "productsid0";
//                $err['msg'] = "Thông báo ! \nBạn đã hoàn tất đơn đặt hàng này";
//                $ret[] = $err;  
//            }
            for($i=0;$i<count($productsid);$i++){
                if($productsid[$i] ==0){
                    $err['id'] = "productsid".$i;
                    $err['msg'] = "Thông báo ! \nVui lòng chọn loại";
                    $ret[] = $err; 
                }
                if($pid[$i] ==0){
                    $err['id'] = "pid".$i;
                    $err['msg'] = "Thông báo ! \nVui lòng chọn tên sản phẩm";
                    $ret[] = $err; 
                }
                if($pid_color[$i] ==0){
                    $err['id'] = "pid_color".$i;
                    $err['msg'] = "Thông báo ! \nVui lòng chọn màu sắc của sản phẩm";
                    $ret[] = $err; 
                }
                if($accounting_name[$i] ==Null){
                    $err['id'] = "accounting_name".$i;
                    $err['msg'] = "Thông báo ! \nVui lòng chọn tên kế toán của sản phẩm.\nNếu không có tên kế toán vui lòng liên hệ kế toán";
                    $ret[] = $err; 
                }
                
            }
            for($i=0;$i<count($type_discount);$i++){
                if($type_discount[$i] == 1 || $type_discount[$i] ==3){
                    $discount[$i] = str_replace(",", "", $_discount[$i]);
                }else{
                    $discount[$i] = str_replace(",", ".", $_discount[$i]);
                }
                if($type_discount[$i]==2 && (int)$discount[$i] >100){
                    $err['id'] = "type_discount".$i;
                    $err['msg'] = "Thông báo ! \nĐơn vị phần trăm chiết khấu lớn hơn 100";
                    $ret[] = $err;
                }
            }
            for($i=0;$i<count($idorder);$i++){
                if($totals[$i] ==0){
                    $err['id'] = "totals".$i;
                    $err['msg'] = "Thông báo ! \nVui lòng nhập số lượng của sản phẩm";
                    $ret[] = $err; 
                }
                if($prices[$i] ==0){
                    $err['id'] = "prices".$i;
                    $err['msg'] = "Thông báo ! \nVui lòng nhập đơn giá của sản phẩm";
                    $ret[] = $err; 
                }
                
            }
           
//            $_ret = $_option->checkPhone($supplier_phone,"supplier_phone");
//            $ret = array_merge($ret,$_ret);
            if($staff_supplier == null){
                $err['id'] = "staff_supplier";
                $err['msg'] = "Thông báo ! \nVui lòng nhập họ tên của bạn";
                $ret[] = $err;
            }
            if($supplier_email == null){
                $err['id'] = "supplier_email";
                $err['msg'] = "Thông báo ! \nVui lòng nhập địa chỉ email";
                $ret[] = $err;
            }
            if (!$_option->isValidEmail($supplier_email)) {
                $err['id'] = "supplier_email";
                $err['msg'] = "Thông báo ! \nVui lòng nhập địa chỉ email hợp lệ";
                $ret[] = $err;
            }
            if($note ==null){
                $err['id'] = "note";
                $err['msg'] = "Thông báo ! \nVui lòng nhập ghi chú vào đây";
                $ret[] = $err; 
            }else{
                if(strlen($note)<20){
                   $err['id'] = "note";
                    $err['msg'] = "Thông báo ! \nGhi chú phải lớn hơn 20 ký tự";
                    $ret[] = $err; 
                }
            }
            
            if (count($ret) > 0) {
                echo json_encode($ret);
                return;
            } else {
                $now = date('Y-m-d H:i:s');
                if($id==0){
                    // insert
                }else{
                    // insert thêm
                    for($i=0;$i<count($productsid);$i++){
                        $_querys[]= "(NULL, '$id', '$productsid[$i]', '$pid[$i]', '$pid_color[$i]', '$accounting_name[$i]', '$now', '1', '$total[$i]', '$price[$i]', '$discount[$i]', '$type_discount[$i]')";
                    }
                    $querys = implode(",", $_querys);
                    if($querys !=null){
                        $sql = "INSERT INTO `hnam_order_detail` (`id`, `orderid`, `productsid`, `pid`, `pid_color`, `accounting_name`, `datetime`, `enabled`, `total`, `price`, `discount`, `type_discount`) VALUES $querys";
                        $_order->excute($sql);
                    }
                    
                    
                    
                    
                    //update
                    $data["supplier_phone"] = $supplier_phone;
                    $data["supplier_email"] = $supplier_email;
                    $data["staff_supplier"] = $staff_supplier;
                    $data["note"] = $note;
                    $data["end_creator"] = $this->_identity["username"];
                    if($data["end_creator"] == null){
                        $data["end_creator"] = $supplier_name;
                    }
                    $data["end_datetime"] = date('Y-m-d H:i:s');
                    $_order->update($id, $data);
                    
                    //update order detail
                    $_detail_order = Business_Addon_OrderDetail::getInstance();
                    
                    $slist = $_detail_order->getListByOrderId($detail["id"]);
                    foreach ($slist as $items){
                        $products_id[] = $items["pid"];
                    }
                    $products_id = array_unique($products_id);
                    $_sproducts = implode(",", $products_id);
                    
                    if($_sproducts!=null){
                        $plist = Business_Ws_ProductsItem::getInstance()->getListByProductsID2($_sproducts);
                        $name_product  = array();
                        foreach ($plist as $items){
                            $name_product[$items["itemid"]] = $items["title"];
                        }
                    }
                    
                    
                    $sendmail = 0;
                    if($note != $detail["note"]){
                        $sendmail = 1;
                        $nd[] = "<td>Cập nhật nội dung chiết khấu</td>   <td>Cập nhật nội dung chiết khấu</td>  <td>$note</td>";
                    }
                    
                    
                    
                    foreach ($slist as $key=>$items){
                        if($idorder[$key] == $items["id"]){
                            if($prices[$key] != $items["price"]){
                               $sendmail = 1;
                               $nd[] = "<td>".$name_product[$items["pid"]]."</td>  <td>Cập nhật đơn giá</td>   <td> ".number_format($items["price"])."->".  number_format($prices[$key])."</td>";
                            }
                            if($totals[$key] != $items["total"]){
                               $sendmail = 1;
                               $nd[] = "<td>".$name_product[$items["pid"]]."</td>   <td>Cập nhật số lượng </td>   <td>".number_format($items["total"])."->".  number_format($totals[$key])." </td>";
                            }
                            if($discount[$key] != $items["discount"]){
                               $sendmail = 1;
                               $nd[] = "<td>".$name_product[$items["pid"]]."</td>   <td> Cập nhật chiết khấu</td>  <td>".number_format($items["discount"])."->".  number_format($discount[$key])."</td>";
                            }
                            if($type_discount[$key] != $items["type_discount"]){
                               $sendmail = 1;
                               $nd[] = "<td>".$name_product[$items["pid"]]."</td>   <td>Cập nhật loại chiết khấu </td>   <td> ".number_format($items["type_discount"])."->".  number_format($type_discount[$key])." </td>";
                            }
                        }
                    }
                    for($i=0;$i<count($idorder);$i++){
                        if($type_discount[$i] == 1 || $type_discount[$i] ==3){
                            $discount[$i] = str_replace(",", "", $_discount[$i]);
                        }else{
                            $discount[$i] = str_replace(",", ".", $_discount[$i]);
                        }
                        $discount[$i] = (int)$discount[$i];
                        $totals[$i] = (int)$totals[$i];
                        $prices[$i] = (int)$prices[$i];
                        $_query[] = "UPDATE `hnam_order_detail` SET `total` = '$totals[$i]', price = '$prices[$i]', discount = '$discount[$i]',type_discount = '$type_discount[$i]' WHERE `id` = '$idorder[$i]'";
                    }
                    
                    if($_query!=null){
                        $query = implode(";", $_query);
                        $_order->excute($query);
                    }
                    
                    
                    $detail_user = Business_Common_Users::getInstance()->getDetail($detail["userid"]);
                    if($sendmail ==1){
                        // send mail
                        $mail_config = "smtp.mailgun.org;587;postmaster@hnammobile.vn;vannghi@123@098";
                        $from = "no-reply@hnammobile.vn";
                        $displayname = $detail["supplier_name"];
                        $replyto = $from;
                        $subject = "Đặt hàng từ nhà cung cấp ".$detail["supplier_name"]." - ".$detail["po"];
//                        $body_html = '<table border="1" style="border-collapse: collapse"><tr> <td>STT</td> <td>Sản phẩm</td> <td>Mô tả</td> <td>Nội dung</td> </tr>';
//                        $nb=0;
//                        foreach ($nd as  $items){
//                            $nb++;
//                            $body_html  .= "<tr style='line-height:20px'><td>$nb</td>$items </tr>";
//                        }
//                        $body_html .= "</table>";
                        $to = $detail_user['email'];
                        if($to == null){
                            $to = "duyhuy@hnammobile.com";
                        }
                        
                        $body_html .= "<hr/>Đơn đặt hàng có thay đổi, vui lòng kiểm tra lại <a href='$url.$link' target='_blank' >Xem đơn hàng</a>";
                        $cc = "";
                        $bcc = array("nghi.dang@hnammobile.com","quynh.nguyen@hnammobile.com","duyhuy@hnammobile.com");
                        Business_Common_Utils::sendEmailV4($from, $displayname, $replyto, $to, $subject, $body_html, $file_attached="",$mail_config,$cc,$bcc);
                    }
                    
                }
                
                
                $err['id'] = "ok";
                $err['msg'] = "ok";
                $ret[] = $err;
                echo json_encode($ret);
            }
    }
    public  function getPathOrder($createdate) {
        $lastChar = strrchr(BASE_PATH, "/");     
        if ($lastChar !== "/") {
            $basePath = BASE_PATH . "/";
        }
        $basePath = $basePath . "uploads/order";
        $ts = strtotime($createdate);
        $year = date("Y", $ts);
        $month = date("m",$ts);
        $day = date("d",$ts);
       
        return sprintf("%s/%s/%s/%s", $basePath, $year, $month, $day);
    }
    public  function getPathOrder2($createdate) {
        $lastChar = strrchr(BASE_PATH, "/");     
        if ($lastChar !== "/") {
            $basePath = BASE_PATH . "/";
        }
        $basePath = $basePath . "uploads/order";
        return sprintf("%s", $basePath);
    }
    public  function getPathOrderImei() {
        $lastChar = strrchr(BASE_PATH, "/");     
        if ($lastChar !== "/") {
            $basePath = BASE_PATH . "/";
        }
        $basePath = $basePath . "uploads/order/imei";
        return sprintf("%s", $basePath);
    }
    public  function getPathOrderExcel() {
        $lastChar = strrchr(BASE_PATH, "\\");     
        if ($lastChar !== "\\") {
            $basePath = BASE_PATH . "\\";
        }
        $basePath = $basePath . "uploads\\order\\excel\\";
        return sprintf("%s", $basePath);
    }
    public function completeAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $_order = Business_Addon_Order::getInstance();
        $id = (int)  $this->_request->getParam("id");
        $detail = $_order->getDetail($id);
        $_vendor = Business_Import_Vendor::getInstance();
        $detail_vendor = $_vendor->getDetail($detail["supplier_id"]);
        $status_imei = $this->_request->getParam("status_imei");
        $supplier_id = $this->_request->getParam("supplier_id");
        $note = $this->_request->getParam("note");
        $username = $this->_identity["username"];
        $userid = $this->_identity["userid"];
        $note = trim($note);
        if ($note != null) {
            $_note = explode("\r\n", $note);
        }
        $full_imei = 1;
        if(count($status_imei) > 0){
            for($i = 0;$i<count($status_imei);$i++){
                if($status_imei[$i] ==0){
                    $full_imei =0;
                }
            }
        }
        $ret = array();
        $arr = array();
//        if($detail["img"] ==null){
//            $arr["id"] = "image";
//            $arr["msg"] = "Thông báo.\nBạn chưa thể hoàn tất do chưa upload hóa đơn";
//            $ret[] = $arr;
//        }
//        if($full_imei ==0){
//            $arr["id"] = "status_imei";
//            $arr["msg"] = "Thông báo.\nBạn chưa thể hoàn tất do chưa nhập đủ số lượng imei";
//            $ret[] = $arr;
//        }
        $now = date('Y-m-d H:i:s');
        if(count($ret)>0){
            echo json_encode($ret);
            return;
        }else{
            $data["status"] = 2;
            $data["note"] = $note;
            $data["f_imei"] = 1;
            if($id>0){
                $_order->update($id, $data);
            }
            //INSERT INTO `hnam_order_ck` (`id`, `orderid`, `datetime`, `end_datetime`, `creator`, `enabled`, `money`, `end_creator`, `userid`, `supplier_id`) VALUES ('1', '2', '2016-04-04 00:00:00', '2016-04-27 00:00:00', 'hnam_quynhn', '0', '10', NULL, '123', '8')
            $date = date('Y-m-d H:i:s');
            
            foreach ($_note as $items){
                $_query[] = "(NULL, '$id', '$now', '$date', '$username', '0', '0', NULL, '$userid', '$supplier_id','$items')";
            }
            $querys = implode(",", $_query);
            if($querys !=null){
                $sql = "INSERT INTO `hnam_order_ck` (`id`, `orderid`, `datetime`, `end_datetime`, `creator`, `enabled`, `money`, `end_creator`, `userid`, `supplier_id`, `note`) VALUES $querys";
                $_order->excute($sql);
            }
            // send mail
            $_secKey = "HNAM_ORDER_2016";
            $token = md5($id.$_secKey);
            $url  = Globals::getBaseUrl();
            $mail_config = "smtp.mailgun.org;587;postmaster@hnammobile.vn;vannghi@123@098";
            $from = "no-reply@hnammobile.vn";
            $displayname = $detail_vendor["name"];
            $replyto = $from;
            $subject = "Đặt hàng từ nhà cung cấp ".$detail_vendor["name"]." - ".$detail["po"];
            $body_html = 'Dear anh/chị '.$detail["staff_supplier"].'<br/>
                
            Phòng kinh doanh vừa hoàn tất đơn hàng với nhà cung cấp '.$detail_vendor["name"].' mã đơn hàng '.$detail["po"].'.
            
            Anh/chị vui lòng xem nội dung đặt hàng chi tiết tại:.<br/>
            Định dạng PDF: <a target="_blank" href='.$url.'admin/user/order/pdf?id='.$id.'&token='.$token.'>Tại đây </a> <br/>
            Định dạng web: <a target="_blank" href='.$url.'admin/user/order/edit-vendor?id='.$id.'&token='.$token.'>Tại đây </a> <br/>
            ';

            $to = $detail["supplier_email"];
            $cc = "";
            $bcc = array("nghi.dang@hnammobile.com","quynh.nguyen@hnammobile.com","duyhuy@hnammobile.com","dongquan@hnammobile.com","tuvan@hnammobile.com","kimngoc.hnam@gmail.com","doandung@hnammobile.com");
            if($detail["supplier_id"] ==43){
                $bcc = array("doandung@hnammobile.com","dongquan@hnammobile.com");
            }
            
            Business_Common_Utils::sendEmailV4($from, $displayname, $replyto, $to, $subject, $body_html, $file_attached="",$mail_config,$cc,$bcc);
            $arr["id"] = "ok";
            $arr["msg"] = "ok";
            $ret[] = $arr;
            echo json_encode($ret);
        }
        
    }

}

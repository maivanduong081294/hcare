<?php

/**
 * User Admin Home Controller
 * @author: nghidv
 */
class User_Admin_PromotionController extends Zend_Controller_Action {
private $_identity;
    public function init() {
        // do something
        BlockManager::setLayout('appbh');
        $auth = Zend_Auth::getInstance();
        $identity = $auth->getIdentity();
        $this->_identity = (array) $auth->getIdentity();
        $this->view->full_name = $identity->fullname;
        $this->view->user_name = $identity->username;
        
        if ($identity != null) {
            //set cookie
            $username = $identity->username;
            $this->view->username = $username;
        }
    }

    private $_plist = array(
        "3" => "Điện thoại",
        "5" => "Máy tính bảng",
        "4" => "Phụ kiện",
    );
    private $_default_menu = "3";
    private $_enabled_promotion = array(
        "1" => "Kích hoạt",
        "0" => "Không kích hoạt",
    );
    private $_default_enabled = "1";

    public function isValid($ret, $price, $return_price) {
        if ($price == "") {
            $err['id'] = "price";
            $err['msg'] = "Lỗi!\nGiá.";
            $ret[] = $err;
        }
        if ($return_price == "") {
            $err['id'] = "return_price";
            $err['msg'] = "Lỗi!\nGiá.";
            $ret[] = $err;
        }
        return $ret;
    }
    public function ajctkmAction(){
        $this->_helper->Layout()->disableLayout();
        $_promotion_fast = Business_Addon_PromotionFast::getInstance();
        $ctkm = $this->_request->getParam("ctkm");
        $list_fast = $_promotion_fast->get_list_by_ctkm($ctkm);
        $this->view->list_fast = $list_fast;
    }

    public function iframeAddKmAction(){
        $this->_helper->Layout()->disableLayout();
        $_promotion_fast = Business_Addon_PromotionFast::getInstance();
        $_option = Business_Addon_Options::getInstance();
        $list_select = $_option->getSelectOne();
        $this->view->list_select = $list_select;
        
        $list_fast = $_promotion_fast->get_list_by_ctkm(-1);
        $this->view->list_fast = $list_fast;
        
        
        $_ctkm = Business_Addon_Ctkm::getInstance();
        $list_ctkm = $_ctkm->getList();
        $this->view->list_ctkm = $list_ctkm;
        
        
        $_cated_promotion = Business_Addon_CatedPromotion::getInstance();
        $list = $_cated_promotion->getList();
        $name_type_km=array();
        foreach ($list as $items){
            $name_type_km[$items["value"]] = $items["name"];
        }
        $this->view->name_type_km = $name_type_km;
        $this->view->items = $list;
        $_zwfuser = Business_Common_Users::getInstance();
        $list_vote = $_zwfuser->getListByUname(FALSE);
        $this->view->list_vote = $list_vote;
        //end cated_promotion
        $id = (int) $this->_request->getParam("id");
        $this->view->id = $id;
        $slist = Business_Ws_ProductsItem::getInstance()->getProducts();
        $this->view->slist = $slist;
        
        $start_end = $this->_request->getParam("start_end");
        if(!empty($start_ends)){
            $date_from = $_option->getStartDateHIS($start_end);
            $date_to = $_option->getEndDateHIS($start_end);
        }
        $this->view->start_end = $start_end;
        
        
    }

    public function listKmAction(){
        $_cated_promotion = Business_Addon_CatedPromotion::getInstance();
        $sslist = $_cated_promotion->getList();
        $name_type_km=array();
        foreach ($sslist as $items){
            $name_type_km[$items["value"]] = $items["name"];
        }
        $this->view->name_type_km = $name_type_km;
        
        $_promotion_fast = Business_Addon_PromotionFast::getInstance();
        $list_promotion_fast = $_promotion_fast->get_list();
        $this->view->list_fast = $list_promotion_fast;
        $enabled2 = $this->_request->getParam("enabled2");
        if($enabled2 ==NULL){
            $enabled2  =1;
        }
        $this->view->enabled2= $enabled2;
        $name_promotion_fast = array();
        $price = array();
        $return_price = array();
        $types = array();
        $date_from = array();
        $date_to = array();
        $product_itemidtitle = array();
        foreach ($list_promotion_fast as $items){
            $name_promotion_fast[$items["itemid"]] = $items["title"];
            $price[$items["itemid"]] = $items["price"];
            $return_price[$items["itemid"]] = $items["return_price"];
            $types[$items["itemid"]] = $items["type"];
            $product_itemidtitle[$items["itemid"]] = $items["itemid_title"];
            $date_from[$items["itemid"]] = $items["date_from"];
            $date_to[$items["itemid"]] = $items["date_to"];
        }
        $this->view->date_from = $date_from;
        $this->view->date_to = $date_to;
        $this->view->name_promotion_fast = $name_promotion_fast;
        $this->view->price = $price;
        $this->view->return_price = $return_price;
        $this->view->types = $types;
        $this->view->product_itemidtitle = $product_itemidtitle;
        $_promotion = Business_Addon_PromotionFastProduct::getInstance();
        $_products = Business_Ws_ProductsItem::getInstance();
        $keywork = $this->_request->getParam("keywork");
        $keywork = str_replace("'", "", $keywork);
        $this->view->keywork = $keywork;
        
        $q_itemid_title = (int)$this->_request->getParam("q_itemid_title");
        $this->view->q_itemid_title = $q_itemid_title;
        
        if ($keywork != null) {
            $detail = $_products->getDetail3((int) $keywork);
            $this->view->detail = $detail;
        }
        $enabled = $this->_request->getParam("enabled");
        if ($enabled == '' || is_null($enabled)) {
            $enabled = $this->_default_enabled;
        }
        $list_kich_hoat = Business_Addon_Options::getInstance()->get_kich_hoat();
        $this->view->list_kich_hoat = $list_kich_hoat;
        $this->view->enabled = $enabled;
        $this->view->enabled_promotion = $this->_enabled_promotion;
//        $plist = $_promotion->getAllList($keywork, $enabled);
//        $total = count($plist);
        $total = 500;
        $records = 50;
        Business_Ws_Common_Paging::setRPP($records);
        $page = $this->_request->getParam('page', '');
        if ($page == '') {
            $page = $this->_session->page;
        }
        ((int) $page == 0 ? $page = 1 : true);
        if ($page > ($total / $records) + 1) {
            $page = 1;
        }

        $this->_session->page = $page;
        $offset = ($page - 1) * $records;
        $this->view->no = $offset + 1;
        $link_to_paging = "/admin/user/promotion/list-km?keywork=$keywork&enabled=$enabled&";
        $paging_template = Business_Ws_Common_Paging::doPaging($page, $total, $link_to_paging, $page_range = 5, 'tg');
        $this->view->paging_template = $paging_template;
        if($q_itemid_title ==1){
            if((int) $keywork >0){
                $__slist_promotion_fast = Business_Addon_PromotionFast::getInstance()->get_list_by_itemid_title($keywork);
                foreach ($__slist_promotion_fast as $val){
                    $___strID[] = $val["itemid"];
                }
            }
            
            if($___strID != NULL){
                $______strIDfast = implode(",", $___strID);
                $list = $_promotion->get_list_by_idfast($______strIDfast);
            }
            
        }else{
            $list = $_promotion->getList($keywork, $offset, $records, $enabled);
        }
        
        foreach ($list as $__items) {
            if($__items["product_ids"] != NULL){
                $product_ids[] = $__items["product_ids"];
            }
        }
       
        if($product_ids!= null){
            $product_ids = array_unique($product_ids);
            $strID = implode(",", $product_ids);
            $slist = Business_Ws_ProductsItem::getInstance()->getListByProductsID2($strID);
        }
        $name_product = array();
        foreach ($slist as $items){
            $name_product[$items["itemid"]] = $items["title"];
        }
        $this->view->name_product = $name_product;
        $this->view->items = $list;
        $__ctkm = Business_Addon_Ctkm::getInstance();
        $name_ctkm = array();
        $list_ctkm = $__ctkm->getList();
        foreach ($list_ctkm as $items){
            $name_ctkm[$items["id"]] = $items["name"];
        }
        $this->view->name_ctkm = $name_ctkm;
    }
    public function saveTitleAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $id = $this->_request->getParam("id");
        $title = $this->_request->getParam("title");
        $data["title"] = $title;
        Business_Addon_PromotionFast::getInstance()->update($id, $data);
    }
    public function delFastProductAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $id = $this->_request->getParam("id");
        $r = (int)$this->_request->getParam("r");
        $data["enabled"] = $r;
        Business_Addon_PromotionFastProduct::getInstance()->update($id, $data);
    }
    public function delFastAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $id = $this->_request->getParam("id");
        $r = (int)$this->_request->getParam("eb");
        $data["enabled"] = $r;
        if($r ==0){
            $query ="update addon_promotion_fast_product set enabled =0 where idfast = $id";
            Business_Addon_PromotionFast::getInstance()->excute($query);
        }
        Business_Addon_PromotionFast::getInstance()->update($id, $data);
    }
    public function getPromotionByIdAction(){
        $this->_helper->Layout()->disableLayout();
        $_promotion = Business_Addon_Promotion::getInstance();
        $product_id = $this->_request->getParam("product_id");
        $__product_id = explode("--", $product_id);
        $id = $__product_id[2];
        $list = $_promotion->getListByProductIds2($id);
        $this->view->list = $list;
        $_cated_promotion = Business_Addon_CatedPromotion::getInstance();
        $slist = $_cated_promotion->getList();
        $name_type_km=array();
        foreach ($slist as $items){
            $name_type_km[$items["value"]] = $items["name"];
        }
        $this->view->name_type_km = $name_type_km;
    }

    public function isKmPromotion($data_frm){
        $ret = array();
        $arr = array();
        if((int)$data_frm["one_or_more"]==0){
            if($data_frm["idfast"] ==NULL){
                $arr["id"] ="idfast";
                $arr["msg"] ="Thông báo.!\nVui lòng chọn khuyến mãi áp dụng";
                $ret[] = $arr;
            }
            if($data_frm["product_ids"] ==null){
                $arr["id"] ="productid";
                $arr["msg"] ="Thông báo.!\nVui lòng chọn sản phẩm áp dụng";
                $ret[] = $arr;
            }
        }else{
            if($data_frm["idfasts"] ==NULL){
                $arr["id"] ="idfasts";
                $arr["msg"] ="Thông báo.!\nVui lòng chọn khuyến mãi áp dụng";
                $ret[] = $arr;
            }
            if($data_frm["product_idss"] ==null){
                $arr["id"] ="product_idss";
                $arr["msg"] ="Thông báo.!\nVui lòng chọn sản phẩm áp dụng";
                $ret[] = $arr;
            }
        }
        
        return $ret;
    }

    public function savesAction(){
        $_option = Business_Addon_Options::getInstance();
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $_promotion = Business_Addon_PromotionFastProduct::getInstance();
        $id = (int) ($this->_request->getParam("id"));
        $data_frm = $this->_request->getParams("data_frm");
        if($data_frm["add_or_copy"] ==0){
            $id =0;
        }
        $ret = $this->isKmPromotion($data_frm);
        if(count($ret)>0){
            echo json_encode($ret);
            return;
        }else{
            $colorid = $data_frm["colorid"];
            $now = date('Y-m-d H:i:s');
            $creator = $this->_identity["username"];
            $start_ends = $this->_request->getParam("start_end");
            $ictkm = (int)$this->_request->getParam("ictkm");
            if(!empty($start_ends)){
                $date_from = $_option->getStartDateHIS($start_ends);
                $date_to = $_option->getEndDateHIS($start_ends);
            }
            $vote_notus2 =0;
            if((int)$data_frm["one_or_more"] ==0){
                $__products_id = explode("--", $data_frm["product_ids"]);
                
                $count_idfast = $data_frm["idfast"];
                $strFastId = implode(",", $count_idfast);
                
                $count_vote_notus2 = $data_frm["vote_notus2"];
                $vote_notus2 = implode(",", $count_vote_notus2);
                if($vote_notus2 ==NULL){
                    $vote_notus2 =0;
                }
                
                
                $check_product_fast = $_promotion->getListProductFast($__products_id[2], $strFastId);
                // INSERT INTO `addon_promotion_fast_product` (`itemid`, `idfast`, `product_ids`, `colorid`, `enabled`, `datetime`, `creator`, `edit_datetime`, `edit_creator`, `product_name`)
                //  VALUES (NULL, '1', '5380', '821', '1', '2016-04-26 00:00:00', 'hnam_quynhn', NULL, NULL, 'san phẩm')
                for($i=0;$i<count($count_idfast);$i++){
                    $check=0;
//                    foreach ($check_product_fast as $items){
//                       if($items["product_ids"] == $__products_id[2] && $items["idfast"] == $count_idfast[$i]){
//                            $check=1;
//                        } 
//                    }
                    if($check ==0){
                        $_query[] = "(NULL, '$count_idfast[$i]', '$__products_id[2]', '$colorid', '1', '$now', '$creator', '', '', '$__products_id[0]','$date_from','$date_to','$ictkm','$vote_notus2')";
                    }
                }
//                echo "<pre>";
//                var_dump($_query);
//                die();
                if($_query != null){
                    $query = implode(",", $_query);
                    $sql = "INSERT INTO `addon_promotion_fast_product` (`itemid`, `idfast`, `product_ids`, `colorid`, `enabled`, `datetime`, `creator`, `edit_datetime`, `edit_creator`, `product_name`, `date_from2`, `date_to2`, `ictkm`, `vote_notus2`)  VALUES $query";
                    $_promotion->excute($sql);
                }
            }else{
                
                $idfast = $data_frm["idfasts"];
                $strProductId = trim($data_frm["product_idss"]);
                $__products_id = implode(",", $strProductId);
                $count_vote_notus2 = $data_frm["vote_notus2s"];
                $vote_notus2 = implode(",", $count_vote_notus2);
                if($vote_notus2 ==NULL){
                    $vote_notus2 =0;
                }
                
                $check_product_fast = $_promotion->getListProductFast($strProductId, $idfast);
                for($i=0;$i<count($__products_id);$i++){
                    $check=0;
                    foreach ($check_product_fast as $items){
                       if($items["product_ids"] == $__products_id[$i] && $items["idfast"] == $idfast){
                            $check=1;
                        } 
                    }
                    if($check ==0){
                        $_query[] = "(NULL, '$idfast', '$__products_id[$i]', '$colorid', '1', '$now', '$creator', '', '', '','$date_from','$date_to','$ictkm','$vote_notus2')";
                    }
                }
                if($_query != null){
                    $query = implode(",", $_query);
                    $sql = "INSERT INTO `addon_promotion_fast_product` (`itemid`, `idfast`, `product_ids`, `colorid`, `enabled`, `datetime`, `creator`, `edit_datetime`, `edit_creator`, `product_name`, `date_from2`, `date_to2`, `ictkm`, `vote_notus2`)  VALUES $query";
                    $_promotion->excute($sql);
                }
            }
            $arr["id"] = "ok";
            $arr["msg"] ="ok";
            $ret[]=$arr;
            echo json_encode($ret);
        }
    }

    public function getCtkmAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $__ctkm = Business_Addon_Ctkm::getInstance();
        $list = $__ctkm->getList();
        echo json_encode($list);
    }
    public function listCtkmAction(){
        $__ctkm = Business_Addon_Ctkm::getInstance();
        $list = $__ctkm->getListAll();
        $this->view->list = $list;
    }
    public function delCtkmAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $id = $this->_request->getParam("id");
        $__ctkm = Business_Addon_Ctkm::getInstance();
        $data["enabled"] = 0;
        $data["datetime_end"] = date('Y-m-d H:i:s');
        $__ctkm->update($id, $data);
        
    }
    public function isKmFast($data_frm){
        $data_frm["price"] = str_replace(",", "", $data_frm ["price"]);
        $data_frm["return_price"] = str_replace(",", "", $data_frm ["return_price"]);
        
        $title_km = $data_frm["title"];
        if ($data_frm["title"] == null) {
            $err['id'] = "title_km";
            $err['msg'] = "Tên khuyến mãi không được để trống";
            $ret[] = $err;
        }
        if ($data_frm["type"] == null) {
            $err['id'] = "type";
            $err['msg'] = "Lựa chọn loại khuyến mãi";
            $ret[] = $err;
        }
        
        if ((int)$data_frm["price"] < 0) {
            $err['id'] = "price";
            $err['msg'] = "Giá không được nhỏ hơn 0";
            $ret[] = $err;
        }
        if ((int)$data_frm["return_price"] < 0) {
            $err['id'] = "return_price";
            $err['msg'] = "Giá hoàn tiền không được nhỏ hơn 0";
            $ret[] = $err;
        }
        if ($data_frm["type"] == 4 || $data_frm["type"]==0) {
            if ($data_frm["itemid_title"] == null) {
                $err['id'] = "itemid_title";
                $err['msg'] = "Mã sản phẩm '$title_km' với giá ưu đãi không được để trống";
                $ret[] = $err;
            }
        }
        return $ret;
    }

    public function savePromotionFastAction() {
        if ($this->_request->isPost()) {
            $this->_helper->Layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            $_option = Business_Addon_Options::getInstance();
            $_promotion_fast = Business_Addon_PromotionFast::getInstance();
            $id = (int) ($this->_request->getParam("id"));
            $data_frm = $this->_request->getParams("data_frm");
            $ret = array();
            $ret = $this->isKmFast($data_frm);
            if (count($ret) > 0) {
                echo json_encode($ret);
                return;
            } else {
                $data = $this->getData($data_frm);
                if((int)$data_frm["type"]==4 || (int)$data_frm["type"]==0){
                    $itemid_title = explode("--", $data_frm["itemid_title"]);
                    $data["itemid_title"] = $itemid_title[2];
                    $data["product_itemidtitle"] = $data_frm["itemid_title"];
                }
                
                if($id==0){
                    $check_detail = $_promotion_fast->checkDetailByTitle($data['title'],$data['price']);
                    $datetime = date('Y-m-d H:i:s');
                    $enabled = 1;
                    $data["datetime"] = $datetime;
                    $data["enabled"] = $enabled;
                    if($check_detail ==NULL){
                        $_promotion_fast->insert($data);
                    }
                }else{
                    $_promotion_fast->update($id,$data);
                }
            }
            $err['id'] = "ok";
            $err['msg'] = "ok";
            $ret[] = $err;
            echo json_encode($ret);
        }
    }
    public function addFastAction() {
        $_promotion_fast = Business_Addon_PromotionFast::getInstance();
        $list_fast = $_promotion_fast->getList();
        $this->view->list_fast = $list_fast;
        $_ctkm = Business_Addon_Ctkm::getInstance();
        $list_ctkm = $_ctkm->getList();
        $this->view->list_ctkm = $list_ctkm;
        $_cated_promotion = Business_Addon_CatedPromotion::getInstance();
        $list = $_cated_promotion->getList();
        $this->view->items = $list;
        $_zwfuser = Business_Common_Users::getInstance();
        $list_vote = $_zwfuser->getListByUname(FALSE);
        $this->view->list_vote = $list_vote;
        //end cated_promotion
        $id = (int) $this->_request->getParam("id");
        $this->view->id = $id;
        $display ='none';
        if ($id > 0) {
            $_promotion = Business_Addon_PromotionFast::getInstance();
            $detail_promotion = $_promotion->getDetail($id);
            $this->view->detail_promotion = $detail_promotion;
            $arr_vote               = explode(",", $detail_promotion["vote_notus"]);
            $this->view->vote_us    = $arr_vote;
            if($detail_promotion['type'] ==0 || $detail_promotion["type"]==4){
                $display ='block';
            }
        }
        $this->view->display = $display;
        $list_color = Business_Ws_NewsItem::getInstance()->getListByCateId(722);
        $this->view->list_color = $list_color;
        
        
        $slist = Business_Ws_ProductsItem::getInstance()->getProducts();
        $this->view->slist = $slist;
    }
    public function saveFastAction(){
        die('no access');
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $_promotion_fast = Business_Addon_PromotionFast::getInstance();
        $_promotion = Business_Addon_Promotion::getInstance();
        $data_frm = $this->_request->getParams("data_frm");
        $ret = array();
        $arr = array();
        if($data_frm["product_ids_fast"] == NULL){
            $arr["id"] = "product_ids_fast";
            $arr["msg"] = "Thông báo.!\nVui lòng nhập mã sản phẩm";
            $ret[] = $arr;
        }
        if((int)$data_frm["idfast"] == 0){
            $arr["id"] = "idfast";
            $arr["msg"] = "Thông báo.!\nVui lòng chọn khuyến mãi thêm nhanh";
            $ret[] = $arr;
        }
        if(count($ret)>0){
            echo json_encode($ret);
            return;
        }else{
            $_productsItem = Business_Ws_ProductsItem::getInstance();
            $list_products = $_productsItem->getListByProductsID2(trim($data_frm["product_ids_fast"]));
            $name_product = array();
            $bonus_company_line = array();
            $bonus_mobile_line = array();
            foreach ($list_products as $items){
                $name_product[$items["itemid"]] = $items["title"];
                $bonus_company_line[$items["itemid"]] = $items["bonus_company_full"];
                $bonus_mobile_line[$items["itemid"]] = $items["bonus_mobile"];
            }
            $data = $_promotion_fast->getDetail($data_frm["idfast"]);
            $title = $data["title"];
            $price = $data["price"];
            $return_price = $data["return_price"];
            $type = $data["type"];
            $now = date('Y-m-d H:i:s');
            $itemid_title = $data["itemid_title"];
            $product_itemidtitle = $data["product_itemidtitle"];
            $date_from = $data["date_from"];
            $date_to = $data["date_to"];
            $kmhang = $data["kmhang"];
            $colorid = $data["colorid"];
            $vote_notus = $data["vote_notus"];
            $ctkm = 0;
            $product_fast = explode(",", trim($data_frm["product_ids_fast"]));
            
            $checkList = $_promotion->getListByType($data_frm["product_ids_fast"],$type,$itemid_title);
            //INSERT INTO `addon_promotion` (`itemid`, `title`, `price`, `return_price`, `link_web`, `type`, `product_ids`, `enabled`, `datetime`, `itemid_title`, `product_name`, `product_itemidtitle`, `time_delete`, `time_restore`, `date_from`, `date_to`, `kmhang`, `colorid`, `vote_notus`, `ctkm`) VALUES
            // (NULL, 'KHuyến mãi mới thêm', '0', '0', '0', '4', '5555', '1', '2016-04-22 00:00:00', '2', 'product_name', 'product_itemidtitle', NULL, NULL, '2016-04-22 00:00:00', '2016-04-29 00:00:00', '0', NULL, '0', '0')
            for($i=0;$i<count($product_fast);$i++){
                $product_fast[$i] = trim($product_fast[$i]);
                $check=0;
                $name_products = $name_product[$product_fast[$i]];
                foreach ($checkList as $items){
                   if($items["product_ids"] ==$product_fast[$i]){
                        $check=1;
                    } 
                }
                if($check==0){
                   $_query[]="(NULL, '$title', '$price', '$return_price', '0', '$type', '$product_fast[$i]', '1', '$now', '$itemid_title', '$name_products', '$product_itemidtitle', '', '', '$date_from', '$date_to', '$kmhang', '$colorid', '$vote_notus', '$ctkm')"; 
                   //update bonuch text trên web
                   if($data_frm["bonus_company_full"] !=NULL){
                       $bonus_company_full = $bonus_company_line[$product_fast[$i]]."".$data_frm["bonus_company_full"];
                   }else{
                       $bonus_company_full = $bonus_company_line[$product_fast[$i]];
                   }
                   if($data_frm["bonus_mobile"] !=NULL){
                       $bonus_mobile = $bonus_mobile_line[$product_fast[$i]].",".$data_frm["bonus_mobile"];
                   }else{
                       $bonus_mobile = $bonus_mobile_line[$product_fast[$i]];
                   }
                   $_query_web[] = "UPDATE ws_productsitem set bonus_company_full = '$bonus_company_full',bonus_mobile='$bonus_mobile' where itemid = '$product_fast[$i]'";
                }
            }
            if($_query != null){
                $query = implode(",", $_query);
                $sql = "INSERT INTO `addon_promotion` (`itemid`, `title`, `price`, `return_price`, `link_web`, `type`, `product_ids`, `enabled`, `datetime`, `itemid_title`, `product_name`, `product_itemidtitle`, `time_delete`, `time_restore`, `date_from`, `date_to`, `kmhang`, `colorid`, `vote_notus`, `ctkm`) VALUES $query";
                $_promotion->excute($sql);
            }
            if($_query_web !=null){
                $query_web = implode(";", $_query_web);
                $_promotion->excute($query_web);
            }
            $arr["id"]="ok";
            $arr["msg"]="ok";
            $ret[]=$arr;
            echo json_encode($ret);
        }
        
    }

    public function saveCtkmAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $__ctkm = Business_Addon_Ctkm::getInstance();
        $name_ctkm = $this->_request->getParam("name_ctkm");
        $id = (int)$this->_request->getParam("id_ctkm");
        $ret = array();
        $arr = array();
        if($name_ctkm ==null){
            $arr["id"] = "name_ctkm";
            $arr["msg"] = "Vui lòng nhập tên chương trình khuyến mãi";
            $ret[]=$arr;
        }
        if(count($ret)>0){
            echo json_encode($ret);
            return;
        }else{
            $data["datetime"] = date('Y-m-d H:i:s');
            $data["enabled"] = 1;
            $data["creator"] = $this->_identity["username"];
            $data["name"] = $name_ctkm;
            if($id==0){
                $__ctkm->insert($data);
            }
            $arr["id"]="ok";
            $arr["msg"]="ok";
            $ret[]=$arr;
            echo json_encode($ret);
        }
    }

    public function deleteAction() {
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $id = $this->_request->getParam("id");
//            var_dump($id);exit();
        Business_Addon_Promotion::getInstance()->delete($id);
    }

    public function delete2Action() {
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $id = $this->_request->getParam("id");
//            var_dump($id);exit();
        Business_Addon_Promotion::getInstance()->delete2($id);
    }

    public function restoreAction() {
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $id = $this->_request->getParam("id");
        Business_Addon_Promotion::getInstance()->restore($id);
    }
    public function getData($data_frm){
        $_option = Business_Addon_Options::getInstance();
        $data_frm["price"] = str_replace(",", "", $data_frm ["price"]);
        $data_frm["return_price"] = str_replace(",", "", $data_frm ["return_price"]);
        $start_ends = $this->_request->getParam("start_ends");
        if(!empty($start_ends)){
            $date_from = $_option->getStartDateHIS($start_ends);
            $date_to = $_option->getEndDateHIS($start_ends);
        }
        $list = Business_Common_Users::getInstance()->getListByUname(FALSE);
        $arr='';
        foreach ($list as $plist){
            $userid = $plist["userid"];
            $vote_name = $this->_request->getParam("vote_check_$userid");
            if($vote_name != null){
                $arr[] = "$vote_name";
            }
        }
        $vote_id = implode(",", $arr);
        $vote_check_all = (int)$this->_request->getParam("vote_check_all");
        if($vote_check_all ==1){
            $vote_id =0;
        }
        $data["title"] = $data_frm["title"];
        $data["kmhang"] = $data_frm["kmhang"];
        $data["price"] = (int)$data_frm["price"];
        $data["return_price"] = (int)$data_frm["return_price"];
        $data["link_web"] = '';
        $data["type"] = $data_frm["type"];
        $data["date_from"] = $date_from;
        $data["date_to"] = $date_to;
        $data["vote_notus"] = $vote_id;
        $data["itemid_title"] = 0;
        $data["product_itemidtitle"] = '';
        $data["ctkm"] = 0;
        return $data;
    }

    public function sendMail($name,$__price_products,$pricekm){
        $mail_config = "smtp.gmail.com;587;saleonline@hnammobile.com;saleonline552015";
        $from = "saleonline@hnammobile.com";     
        $displayname = "Hnammobile";
        $replyto = $from;
        $subject = "Khuyến mãi đã vượt quá 30% giá trị máy ". $name;
        $body_html  = '1. Tên máy   :  '. $name.'<br/>';
        $body_html .= '2. Giá máy        :  '.number_format($__price_products).'<br/>';
        $body_html .= '3. Tổng tiền khuyến mãi (hoàn tiền) :  '.number_format($pricekm).'<br/>';
        $to = "bangiamdoc@hnammobile.com";
//        $to = "nguyenquynh1105@gmail.com";
        $cc = array("nghi.dang@hnammobile.com","quynh.nguyen@hnammobile.com");
        $result = Business_Common_Utils::sendEmailV3($from, $displayname, $replyto, $to, $subject, $body_html, $file_attached="",$mail_config,$cc);
        return $result;
    }
    public function uploadAction(){
        if ($this->_request->isPost()){
            $_option = Business_Addon_Options::getInstance();
            $path = $_option->getPath();
            $expensions = array("xls","xlsx");
            $check_upload = $_option->upload($path, $expensions);
            $ret = array();
            if($check_upload[0]["msg"] !="ok"){
                foreach ($check_upload as $item){
                  $ret[] = $item;  
                }
            }else{
                $files_path = $check_upload[0]["files_path"];
                $file_ext = $check_upload[0]["file_ext"];
            }
            if(count($ret) >0){
                for($i=0;$i<count($ret);$i++){
                    $msg = $ret[$i]['msg'];
                    $ids = $ret[$i]['id'];
                    echo "<script>window.parent.notification('$msg','$ids');</script>";
                    return;
                }
            }else{ 
                if($_column ==0){
                    $_column =1;
                }
                $col = $_column -1;
                if($file_ext == "xls"){
                   $col =  $_column;
                }
                $postion_title =$col;
                $postion_products_id =$col+1;
                $postion_price = $col+2;
                $postion_date_from = $col+3;
                $postion_date_to = $col+4;
                $postion_vote = $col+5;
                ini_set('display_errors', '1');
                if($file_ext == "xlsx"){
                    include BASE_PATH.'/simplexlsx.class.php';
                    $xlsx = new SimpleXLSX($files_path);
                    $data = $xlsx->rows();
                    foreach ($data as $key=> $items){
                        if($key==0){
                            continue;
                        }
                        if($items[$postion_title] != NULL){
                            $slist["title"] = $items[$postion_title];
                            $slist["products_id"] = $items[$postion_products_id];
                            $slist["price"] = $items[$postion_price];
                            $slist["date_from"] = $items[$postion_date_from];
                            $slist["date_to"] = $items[$postion_date_to];
                            $slist["vote_notus"] = $items[$postion_vote];
                            $sdata[] = $slist;
                        }
                    }
                }else{
                    if($file_ext == "xls"){
                        include BASE_PATH.'/excel_reader2.php';
                        $data = new Spreadsheet_Excel_Reader($files_path);
                       for($i=0;$i<500;$i++){
                           if($i==0){
                               continue;
                           }
                           if($data->val($i,$postion_title) != NULL){
                                $slist["title"] = $data->val($i,$postion_title);
                                $slist["products_id"] = $data->val($i,$postion_products_id);
                                $slist["price"] = $data->val($i,$postion_price);
                                $slist["date_from"] = $data->val($i,$postion_date_from);
                                $slist["date_to"] = $data->val($i,$postion_date_to);
                                $slist["vote_notus"] = $data->val($i,$postion_vote);
                                $sdata[] = $slist; 
                           }
                       }
                    }
                }
                $datetime = date("Y-m-d H:i:s");
                $creator = $this->_identity["username"];
                foreach($sdata as $val){
                    $__products_id[] = $val["products_id"];
                }
                $strID  = implode(",", $__products_id);
                $list_product = Business_Ws_ProductsItem::getInstance()->getListByProductsID2($strID);
                $name_products = array();
                foreach ($list_product as $item){
                    $name_products[$item["itemid"]] = $item["title"];
                }
                
                foreach($sdata as $val){
                    $idctkm = (int)$val["title"];
                }
                $check_ctkm=0;
                if($idctkm==0){
                    $err["msg"]="Vui lòng nhập id chương trình khuyến mãi";
                    $err["id"]="files_path";
                    $ret[] = $err;
                    $check_ctkm=1;
                }else{
                    $detail = Business_Addon_Ctkm::getInstance()->getDetail($idctkm);
                    if($detail==NULL){
                        $err["msg"]="Chương trình khuyến mãi không có thực. Vui lòng kiểm tra lại";
                        $err["id"]="files_path";
                        $ret[] = $err;
                        $check_ctkm=1;
                    }
                }
                if($check_ctkm==1){
                    for($i=0;$i<count($ret);$i++){
                        $msg = $ret[$i]['msg'];
                        $ids = $ret[$i]['id'];
                        echo "<script>window.parent.notification('$msg','$ids');</script>";
                        return;
                    }
                    die();
                }
                
                foreach($sdata as $val){
                    $val["price"] = str_replace(",", "", $val["price"]);
                    $idctkm = (int)$val["title"];
                    $_data2["title"] = "Voucher giảm giá ". $val["title"];
                    $_data2["price"]= $val["price"];
                    $_data2["return_price"]= $val["price"];
                    $_data2["datetime"] = $datetime;
                    $_data2["enabled"]= 1;
                    $_data2["type"]= 6;
                    $_data2["ctkm"]= $idctkm;
                    
                    $_data2["vote_notus"]= $val["vote_notus"];
                    $idfast = Business_Addon_PromotionFast::getInstance()->insert($_data2);
                    
                    //INSERT INTO `addon_promotion_fast_product` (`itemid`, `idfast`, `product_ids`, `colorid`, `enabled`, `datetime`, `creator`, `edit_datetime`, `edit_creator`, `product_name`) VALUES (NULL, '12', '5380', '0', '1', '2016-06-06 00:00:00', 'hnam_quynhn', NULL, NULL, 'iphone 6s');
                    $_data3["idfast"] = $idfast;
                    $_data3["product_ids"] = $val["products_id"];
                    $_data3["colorid"] = 0;
                    $_data3["enabled"] = 1;
                    $_data3["datetime"] = $datetime;
                    $_data3["creator"] = $creator;
                    $_data3["product_name"] = $name_products[$val["products_id"]];
                    $_data3["date_from2"]= date('Y-m-d',  strtotime($val["date_from"]));
                    $_data3["date_to2"]= date('Y-m-d',  strtotime($val["date_to"]))." 23:59:59";
                    $_data3["ictkm"]= $idctkm;
                    Business_Addon_PromotionFastProduct::getInstance()->insert($_data3);
                }
                echo "<script>window.parent.completes('Upload thành công');</script>";
                
            }
        }
    }
    
}
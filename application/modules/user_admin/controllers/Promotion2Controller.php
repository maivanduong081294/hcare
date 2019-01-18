<?php

/**
 * User Admin Home Controller
 * @author: nghidv
 */
class User_Admin_Promotion2Controller extends Zend_Controller_Action {
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
    public function addAction(){
        $_promotion_fast = Business_Addon_PromotionFast::getInstance();
        $_option = Business_Addon_Options::getInstance();
        $list_select = $_option->getSelectOne();
        $this->view->list_select = $list_select;
        
        $_ctkm = Business_Addon_Ctkm::getInstance();
        $strID = "265,266,267,268,269,270,271,272,273,274,275";
        $array_km_san = explode(",", $strID);
        $this->view->array_km_san = $array_km_san;
        $list_ctkm2 = $_ctkm->get_list_by_id($strID);
        $this->view->list_ctkm2 = $list_ctkm2;
        
        
        $_zwfuser = Business_Common_Users::getInstance();
        $list_vote = $_zwfuser->getListByUname(FALSE);
        $this->view->list_vote = $list_vote;
        //end cated_promotion
        $id = (int) $this->_request->getParam("id");
        $this->view->id = $id;
        
        $list_promotion_fast = $_promotion_fast->get_list(6);
        $this->view->list_fast = $list_promotion_fast;
        
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
        
        
        
        $_products = Business_Ws_ProductsItem::getInstance();
        $_promotion = Business_Addon_PromotionFastProduct::getInstance();
        if($id >0){
            $detail = $_products->getDetail3($id);
            $list_km = $_promotion->getList($id, 0, 100, 1);
            
            foreach ($list_km as $__items) {
                if($__items["product_ids"] != NULL){
                    $product_ids[] = $__items["product_ids"];
                }
                $array_idfast[] = $__items["idfast"];
            }
            if($array_idfast){
                $str_idfast = implode(",", $array_idfast);
                $list_tmp2 = $_promotion_fast->get_list_by_id($str_idfast);
                foreach ($list_tmp2 as $tmp2){
                    $name_promotion_fast[$tmp2["itemid"]] = $tmp2["title"];
                    $types[$tmp2["itemid"]] = $tmp2["type"];
                    $price[$tmp2["itemid"]] = $tmp2["price"];
                    $return_price[$tmp2["itemid"]] = $tmp2["return_price"];
                    $product_itemidtitle[$tmp2["itemid"]] = $tmp2["itemid_title"];
                    $date_from[$tmp2["itemid"]] = $tmp2["date_from"];
                    $date_to[$tmp2["itemid"]] = $tmp2["date_to"];
                    
                    
                }
            }
            if($product_ids!= null){
                $product_ids = array_unique($product_ids);
                $strID2 = implode(",", $product_ids);
                $slist2 = Business_Ws_ProductsItem::getInstance()->getListByProductsID2($strID2);
            }
            $name_product = array();
            foreach ($slist2 as $items){
                $name_product[$items["itemid"]] = $items["title"];
            }
            $this->view->name_product = $name_product;
            $this->view->types = $types;
            $this->view->price = $price;
            $this->view->return_price = $return_price;
            $this->view->date_from = $date_from;
            $this->view->date_to = $date_to;
            $this->view->product_itemidtitle = $product_itemidtitle;
        }
        $this->view->name_promotion_fast = $name_promotion_fast;
        $this->view->detail = $detail;
        $this->view->list_km = $list_km;
        $this->view->items = $list_km;
        
        $slist = Business_Ws_ProductsItem::getInstance()->getProducts(0,$id);
        $this->view->slist = $slist;
        
        $start_end = $this->_request->getParam("start_end");
        if(!empty($start_ends)){
            $date_from = $_option->getStartDateHIS($start_end);
            $date_to = $_option->getEndDateHIS($start_end);
        }
        $this->view->start_end = $start_end;
        
        $__ctkm = Business_Addon_Ctkm::getInstance();
        $name_ctkm = array();
        $list_ctkm = $__ctkm->getList();
        foreach ($list_ctkm as $items){
            $name_ctkm[$items["id"]] = $items["name"];
        }
        $this->view->name_ctkm = $name_ctkm;
    }
}
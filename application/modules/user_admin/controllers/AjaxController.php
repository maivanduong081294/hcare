<?php

/**
 * Hnam Controller
 * @author: nghidv
 */
class User_Admin_AjaxController extends Zend_Controller_Action {

    private $_identity;
    public function init() {
        // do something
        BlockManager::setLayout('appbh');
        $auth = Zend_Auth::getInstance();
        $identity = $auth->getIdentity();
        $this->_identity = (array) $auth->getIdentity();
        $this->view->full_name = $identity->fullname;
        if ($identity != null) {
            $username = $identity->username;
            $this->view->username = $username;
            $this->view->user_name = $username;
        }
    }
    public function getMenuAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $_option = Business_Addon_Options::getInstance();
        $menuid = $this->_request->getParam("productsid");
        $list = $_option->getMenuById($menuid);
        $ret                        = array();
        $arr                        = array();
//        $_tmp = array();
//        if($menuid ==3){
//            $_tmp[0]["itemid"] = 999997;
//            $_tmp[0]["title"] = "Apple";
//        }
//        if($menuid ==5){
//            $_tmp[0]["itemid"] = 999998;
//            $_tmp[0]["title"] = "Apple";
//        }
//        if($_tmp !=null){
//            $list = array_merge($_tmp, $list);
//        }
        foreach ($list as $items){
            $arr["itemid"]          = $items["itemid"];
            $arr["title"]           = $items["title"];
            $ret[]                  = $arr;
        }
        echo json_encode($ret);
    }
    public function getMenu2Action(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $_option = Business_Addon_Options::getInstance();
        $menuid = $this->_request->getParam("productsid");
        $list = $_option->getMenuById2($menuid);
        $ret                        = array();
        $arr                        = array();
        $_tmp = array();
        if($menuid ==3){
            $_tmp[0]["itemid"] = 999997;
            $_tmp[0]["title"] = "Apple";
        }
        if($menuid ==5){
            $_tmp[0]["itemid"] = 999998;
            $_tmp[0]["title"] = "Apple";
        }
        if($_tmp !=null){
            $list = array_merge($_tmp, $list);
        }
        foreach ($list as $items){
            if(strpos(strtolower($items["title"]), "iphone")){
                continue;
            }
            $arr["itemid"]          = $items["itemid"];
            $arr["title"]           = $items["title"];
            $ret[]                  = $arr;
        }
        echo json_encode($ret);
    }
    public function getItemidAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $_option = Business_Addon_Options::getInstance();
        $cated_id = $this->_request->getParam("cated_id");
        $__bs = Business_Ws_ProductsItem::getInstance();
        if($cated_id==999997){ //điện thoại
            $list = $__bs->get_list_by_cated_apple(3);
        }else{
            if($cated_id==999998){ // máy tính bảng
                $list = $__bs->get_list_by_cated_apple(5);
            }else{
                $list = $__bs->get_list_by_cated_2016($cated_id);
                if(!$list)
                {
                    $_products = Business_Ws_MenuItem::getInstance();
                    $sub =  $_products->getSubmenu($cated_id);
                    foreach ($sub as $key => $value) {
                       $listcateid[] = $value['itemid'];
                    }
                    if(count($listcateid)>1)
                       $list = $__bs->get_list_by_cated_2016(implode(',',$listcateid));
                }

            }
        }
        
        $ret                        = array();
        $arr                        = array();
        foreach ($list as $items){
            $arr["itemid"]          = $items["itemid"];
            $arr["title"]           = $items["title"];
            $ret[]                  = $arr;
        }
        echo json_encode($ret);
    }
    public function getProductsAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $_products_item = Business_Ws_ProductsItem::getInstance();
        $productsid = $this->_request->getParam("productsid");
        $list = $_products_item->getList2($productsid);
        echo json_encode($list);
    }
    public function getProductsWhAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $_products_item = Business_Ws_ProductsItemWh::getInstance();
        $productsid = $this->_request->getParam("productsid");
        $list = $_products_item->getList2($productsid);
        echo json_encode($list);
    }
    public function getDetailOrderImeiAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $_order_imei = Business_Addon_OrderDetailImei::getInstance();
        $detail_orderid = $this->_request->getParam("detail_orderid");
        $list = $_order_imei->getListByDetailOrderId($detail_orderid);
        if($list ==null){
            $l["imei"] ="nok";
            $list[] = $l;
        }
        echo json_encode($list);
    }
    public function getColorByProductIdsAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $__product_ids = $this->_request->getParam("product_id");
        $__product_id = explode("--", $__product_ids);
        $product_id =  $__product_id[2];
        $_product_color             = Business_Addon_ProductsColor::getInstance();
        $list = $_product_color->get_list_by_id($product_id);
        
        $name_color = array();
        $list_color = Business_Ws_NewsItem::getInstance()->getListByCateId(722);
        foreach ($list_color as $val){
            $name_color[$val["itemid"]] = $val["title"];
        }
        
        foreach ($list as &$items){
            $items["name_color"] = $name_color[$items["colorid"]];
        }
        echo json_encode($list);
    }
    public function getColorByProductIdAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $product_id = $this->_request->getParam("product_id");
        $_product_color             = Business_Addon_ProductsColor::getInstance();
        $list = $_product_color->get_list_by_id($product_id);
        $name_color = array();
        $list_color = Business_Ws_NewsItem::getInstance()->getListByCateId(722);
        foreach ($list_color as $items2){
            $name_color[$items2["itemid"]] = $items2["title"];
        }
        
        foreach ($list as &$items){
            $items["name_color"] = $name_color[$items["colorid"]];
        }
        echo json_encode($list);
    }
    public function getTitleKtByColoridAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $product_id = $this->_request->getParam("product_id");
        $colorid = $this->_request->getParam("colorid");
        $_product_color             = Business_Addon_AddonProductTitleKT::getInstance();
        $list = $_product_color->getListByPidByColorid($product_id,$colorid);
        echo json_encode($list);
    }


}

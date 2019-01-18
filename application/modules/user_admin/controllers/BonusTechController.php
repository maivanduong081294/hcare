<?php

/**
 * User Admin Home Controller
 * @author: nghidv
 */
class User_Admin_BonusTechController extends Zend_Controller_Action {

    public function init() {
        // do something
        BlockManager::setLayout('appbh');
        $auth = Zend_Auth::getInstance(); 
        $identity = $auth->getIdentity();
        $this->view->full_name = $identity->fullname;
        if ($identity != null) {
            $username = $identity->username;
            $this->view->username = $username;
        }
    }
public function indexAction(){
    $keywork = $this->_request->getParam("keywork");
    $this->view->keywork = $keywork;
    $list = Business_Addon_BonusTech::getInstance()->getList($keywork);
    $this->view->items = $list;
    $productsid = "4,10";
    $list_item = Business_Ws_ProductsItem::getInstance()->get_list($productsid);
    $this->view->list_item = $list_item;
}
public function update7ngayAction(){
    $this->_helper->Layout()->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);
    $products_id = (int)  $this->_request->getParam("products_id");
    $_price = $this->_request->getParam("price");
    $price = str_replace(",", "", $_price);
    $cur_date = date('Y-m-d H:i:s');
    $_date30 = Business_Addon_Options::getInstance()->getPrevDay2(7, $cur_date);
    if($products_id >0){
        $query = "update users_products set bonus_tech =$price where products_id = $products_id and create_date >= '$_date30' ";
        Business_Addon_UsersProducts::getInstance()->excute($query);
    }
}

public function deleteAction(){
            $this->_helper->Layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            $id = $this->_request->getParam("id");
//            var_dump($id);exit();
            $data["enabled"]=0;
        Business_Addon_BonusTech::getInstance()->update($id,$data);
        
    }
    public function restoreAction(){
            $this->_helper->Layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            $id = $this->_request->getParam("id");
            Business_Addon_BonusTech::getInstance()->restore($id);
    }
   
    public function saveAction(){
        if ($this->_request->isPost()) {
            $this->_helper->Layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            $_bonus_tech = Business_Addon_BonusTech::getInstance();
            // param san pham
            $products_id = (int)$this->_request->getParam("products_id");
            $_bonus_price = $this->_request->getParam("bonus_price");
            $bonus_price = str_replace(",", "", $_bonus_price);
            $bonus_type = $this->_request->getParam("bonus_type");
            $bonus_date = date('Y-m-d H:i:s');
            
            $ret = array();
            if($products_id ==0){
                $err['id'] = "products_id";
                $err['msg'] = "Mã sản phẩm không được bỏ trống";
                $ret[] = $err;
            }else{
                $lbonus_tech = $_bonus_tech->getListByPId($products_id);
                if($lbonus_tech!= NULL){
                    $err['id'] = "products_id";
                    $err['msg'] = "Sản phẩm này đã được thêm. Vui lòng kiểm tra lại";
                    $ret[] = $err;
                }
            }
            if ((int)$bonus_price == 0) {
                $err['id'] = "bonus_price";
                $err['msg'] = "Giá giảm không được bỏ trống";
                $ret[] = $err;
            }
            if (count($ret) > 0){
                echo json_encode($ret);
                
            }else{
                $data["products_id"] = $products_id;
                $detail_item = Business_Ws_ProductsItem::getInstance()->get_detail($products_id);
                $data["bonus_name"] = $detail_item["title"];
                $data["bonus_price"] = $bonus_price;
                $data["bonus_date"] = $bonus_date;
                $data["bonus_type"] = $bonus_type;
                $_bonus_tech->insert($data);
                $err['id'] = "ok";
                $err['msg'] = "ok";
                $ret[] = $err;
                echo json_encode($ret);
            }
            
            
        }
    }
    
    public function save2Action(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $_bonus_tech = Business_Addon_BonusTech::getInstance();
        $id = $this->_request->getParam("id");
        $_bonus_price = $this->_request->getParam("bonus_price");
        $bonus_price = str_replace(",", "", $_bonus_price);
        $data["bonus_price"] = $bonus_price;
        $_bonus_tech->update($id, $data);
    }

}
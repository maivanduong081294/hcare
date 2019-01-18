<?php
/**
 * User Admin Banner Controller
 * @author: tanduc_nguyen
 */
class User_Admin_AddaccfullController extends Zend_Controller_Action
{
    
    public function init()
    {        
        BlockManager::setLayout('user_admin_layout');
    }
    
    public function addAccAjaxAction() {
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $pitemid = $this->_request->getParam("pitemid");
        $accitemid = $this->_request->getParam("accitemid");
        
        $pdetail = Business_Ws_ProductsItem::getInstance()->getDetail($pitemid);        
        $accid = $pdetail["accidfull"];
        $accids = split(",", $accid);
        $accids[$accitemid] = $accitemid;
        
        $accid_str = implode(",", $accids);
        $accid_str = trim($accid_str, ",");
        $pdetail["accidfull"] = $accid_str;
        $accid_str = trim($accid_str, ",");
        Business_Ws_ProductsItem::getInstance()->update($pitemid, $pdetail["productsid"], $pdetail["cateid"], $pdetail);                    
    }
    
    public function removeAccAjaxAction() {
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $pitemid = $this->_request->getParam("pitemid");
        $accitemid = $this->_request->getParam("accitemid");
        
        $pdetail = Business_Ws_ProductsItem::getInstance()->getDetail($pitemid);        
        $accid = $pdetail["accidfull"];
        $accids = split(",", $accid);
        
        $remain_accids = array();
        
        foreach($accids as $_accid) {
            if ($_accid == $accitemid) continue;
            $remain_accids[] = $_accid;
        }
        
        $accid_str = implode(",", $remain_accids);
        $pdetail["accidfull"] = $accid_str;
        $accid_str = trim($accid_str, ",");
        Business_Ws_ProductsItem::getInstance()->update($pitemid, $pdetail["productsid"], $pdetail["cateid"], $pdetail);                    
    }
    
    public function searchAccAction() {
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        $keyword = $this->_request->getParam("key");
        $list = Business_Ws_ProductsItem::getInstance()->getListByFilterByCateidAndKeywordWithPaging(0, $keyword, $offset=0, $records=10);
        $ret = "<ul id='acc-block'>";
        foreach($list as &$item) {                
            $item = Business_Helpers_Products::updateAccessoryDetail($item);
            $ret .= "<li>";
            $ret .= "<p><img src='".$item["thumb_home"]."' /><br />".$item["title"]."</p>";
            $ret .= "<p>".$item["price"]."</p>";
            $ret .= "<p><a href='#' onclick='additem(\"".$item["itemid"]."\"); return false;' style='border:1px solid #CCCCCC; padding:5px; background-color:green; color:#ffffff'>Thêm</a></p>";
            $ret .= "</li>";
        }
        $ret .= "</ul>";
        echo $ret;
    }
    
    public function addAccAction() {
        $itemid = (int) $this->_request->getParam("itemid");
        $product = Business_Ws_ProductsItem::getInstance()->getDetail($itemid);
        $this->view->product = $product;
        $accids = $product["accidfull"];
        $accids = trim($accids, ",");
        if ($accids==null) {
            $accids = array();
        }
        if ($accids != null) {
            $accList = Business_Ws_ProductsItem::getInstance()->getListByProductsID($accids);
            foreach($accList as &$acc) {
                $acc = Business_Helpers_Products::updateAccessoryDetail($acc);
            }
            $this->view->accList = $accList;
        }                
    }
}
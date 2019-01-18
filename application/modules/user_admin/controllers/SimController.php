<?php

/**
 * Hnam Controller
 * @author: nghidv
 */
class User_Admin_SimController extends Zend_Controller_Action {

    private $_identity;
    private $_default_menu = "1";
    private $_plist = array(
                    "1" => "Trong kho",
                    "2" => "Đã bán",
                );
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
    public function thongkeSimAction(){
        $_addon_sim             = Business_Addon_AddonSim::getInstance();
        $_zfw_users             = Business_Common_Users::getInstance();
        $list_vote              = $_zfw_users->getListByUname(FALSE);
        $this->view->list_vote  = $list_vote;
        
        $_option = Business_Addon_Options::getInstance();
        $d=date('Y-m-d');
        $k=$_option->pre_date($d, 2);
        $nows = date('F j, Y',  strtotime($k));
        $start_end           = $this->_request->getParam("start_end");
        if($start_end ==null){
           $start_end = $nows." - ".date("F j, Y"); 
        }
        $this->view->start_end = $start_end;
        $start  = $_option->getStartDate($start_end);
        $end  = $_option->getEndDate($start_end);
        $this->view->start = $start;
        $this->view->end = $end;
        
        $storeid = $this->_identity["parentid"];
        $this->view->storeid = $storeid;
        $idregency = $this->_identity["idregency"];
        $bgd = 0;
        if($_option->isBGD($idregency)){
            $bgd=1;
        }
        $this->view->bgd = $bgd;
        $this->view->idregency = $idregency;
        
        $list_seriasim          = $_addon_sim->countSimByOption($start,$end,'seriasim');
        
        $list_hopdong           = $_addon_sim->countSimByOption($start,$end,'hopdong');
        
        $list_cmndNext          = $_addon_sim->countSimByOption($start,$end,'cmnd_next');
        $list_cmndPre          = $_addon_sim->countSimByOption($start,$end,'cmnd_pre');
        $list_complete          = $_addon_sim->countSimByOption($start,$end,'complete');
        $list_active          = $_addon_sim->countSimByOption($start,$end,'active');
        $total_sim_actived     = $_addon_sim->count_sim_actived($start,$end);
        $this->view->items      = $list_seriasim;
        $this->view->items_hd   = $list_hopdong;
        $this->view->items_cmndNext     = $list_cmndNext;
        $this->view->items_cmndPre      = $list_cmndPre;
        $this->view->items_complete      = $list_complete;
        $this->view->total_sim_actived    = $total_sim_actived;
        $ret_seriasim           = array();
        foreach ($total_sim_actived as $items){
            $vote_id            = $items["vote_id"];
            $ret_total_sim_actived[$vote_id]   = $items["dem"];
        }
        $this->view->count_total_sim_actived = $ret_total_sim_actived;
        foreach ($list_seriasim as $items){
            $vote_id            = $items["vote_id"];
            $enabled            = $items["seria_sim"];
            $ret_seriasim[$vote_id][$enabled]        = $items["dem"];
        }
        $this->view->count_simActive        = $ret_seriasim;
        
        $ret_hopdong           = array();
        foreach ($list_hopdong as $items){
            $vote_id            = $items["vote_id"];
            $enabled            = $items["hopdong"];
            $ret_hopdong[$vote_id][$enabled]        = $items["dem"];
        }
        $this->view->count_hd        = $ret_hopdong;
        
        
        $ret_cmndNext           = array();
        foreach ($list_cmndNext as $items){
            $vote_id            = $items["vote_id"];
            $enabled            = $items["cmnd_next"];
            $ret_cmndNext[$vote_id][$enabled]        = $items["dem"];
        }
        $this->view->count_cmndNext        = $ret_cmndNext;
        
        $ret_cmndPre           = array();
        foreach ($list_cmndPre as $items){
            $vote_id            = $items["vote_id"];
            $enabled            = $items["cmnd_pre"];
            $ret_cmndPre[$vote_id][$enabled]        = $items["dem"];
        }
        $this->view->count_cmndPre        = $ret_cmndPre;
        
        $ret_complete           = array();
        foreach ($list_complete as $items){
            $vote_id            = $items["vote_id"];
            $enabled            = $items["complete"];
            $ret_complete[$vote_id][$enabled]        = $items["dem"];
        }
        $this->view->count_complete        = $ret_complete;
        
        $ret_active           = array();
        foreach ($list_active as $items){
            $vote_id            = $items["vote_id"];
            $enabled            = $items["enabled"];
            $ret_active[$vote_id][$enabled]        = $items["dem"];
        }
        $this->view->count_active        = $ret_active;
//        foreach ($list_vote as &$items){
//            $vote_id            = $items["userid"];
//            $items["simseri"]   = $_addon_sim->countSimByOption($vote_id, $created_day, $end_day,'serisim');
//            var_dump($items["simseri"]);exit();
//        }
//        echo '<pre>';var_dump($list);exit();
        
        $idregency = $this->_identity["idregency"];
//        if($idregency ==11){
//            $this->_helper->viewRenderer('thongke-sim-store');
//        }
        $this->view->storeid = $this->_identity["parentid"];
        
    }
    public function thongkeSim2Action(){
        $_addon_sim             = Business_Addon_AddonSim::getInstance();
        $_zfw_users             = Business_Common_Users::getInstance();
        $list_vote              = $_zfw_users->getListByUname(FALSE);
        $this->view->list_vote  = $list_vote;
        $days_created_end       = $this->_request->getParam("day_created_end");
        if ($days_created_end   == null) {
            $days_created_end   = date('Y/m/01') . ' - ' . date('Y/m/31');
        }
        $this->view->days_created_end = $days_created_end;
        $created_date           = substr($days_created_end, 0, 10);
        $created_day            = str_replace("/", "-", $created_date) . ' 00:00:00';
        $end_date               = substr($days_created_end, 13, 10);
        $months                 = substr($days_created_end, 18, 2);
        $end_day                = str_replace("/", "-", $end_date) . ' 23:59:59';
        $list                   = $_addon_sim->countSim($created_day,$end_day);
        $ret                    = array();
        foreach ($list as $items){
            $vote_id            = $items["vote_id"];
            $enabled            = $items["enabled"];
            $ret[$vote_id][$enabled]        = $items["dem"];
        }
        $this->view->count_simActive        = $ret;
//        echo '<pre>';var_dump($ret,$list_vote,$list);exit();
    }

    private function isValidMoney($ret,$vote_id,$money,$des){
        if($vote_id == 0){
            $err['id'] = "vote_id";
            $err['msg'] = "Lỗi!\nVui lòng lựa chọn cửa hàng!";
            $ret[] = $err;
        }
        if($money == null){
            $err['id'] = "money";
            $err['msg'] = "Lỗi!\nVui lòng nhập số tiền!";
            $ret[] = $err;
        }
        return $ret;
    }
    public function delAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $id = $this->_request->getParam("id");
        $_sim_money             = Business_Addon_MoneySim::getInstance();
        $data["enabled"] =0;
        $data["creator_end"] =  $this->_identity["username"];
        $data["update_date"] =  date('Y-m-d H:i:s');
        $_sim_money->update($id, $data);
    }

    public function listMoneyAction(){
        $_sim_money             = Business_Addon_MoneySim::getInstance();
        $_zfw_users             = Business_Common_Users::getInstance();
        $_addon_sim             = Business_Addon_AddonSim::getInstance();
        $list_vote              = $_zfw_users->getListByUname(FALSE);
        $vote_id                = (int)$this->_request->getParam("storeid");
        $list                   = $_sim_money->getList($vote_id);
        $this->view->vote_id    = $vote_id;
        $storename = array();
        foreach ($list_vote as $items){
            $storename[$items["userid"]] = $items["storename"];
        }
        foreach ($list as &$_items){
            $_items["storename"] = $storename[$_items["vote_id"]];
        }
        $this->view->items      = $list;
        $this->view->items2     = $list_vote;
        
        $ltotal_sim = $_addon_sim->groupSimByVote();
        $total_vote = array();
        $total =0;
        foreach ($ltotal_sim as $items){
            $total_vote[$items["vote_id"]] = $items["total"];
            $total += $items["total"];
        }
        $this->view->total_vote = $total_vote;
        $this->view->total = $total;
    }

    public function saveMoneyAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $_sim_money     = Business_Addon_MoneySim::getInstance();
        $id             = (int)$this->_request->getParam("id");
        $vote_id        = $this->_request->getParam("vote_id");
        $_money         = $this->_request->getParam("money");
        $money          = str_replace(",", "", $_money);
        $des            = $this->_request->getParam("des");
        $data           = array();
        $ret            = array();
        if ($id == 0) {
            $ret        = $this->isValidMoney($ret,$vote_id, $money, $des);
        }
        if(count($ret)>0){
            echo json_encode($ret);
        }else{
            if($id == 0){
                $data["vote_id"]    = $vote_id;
                $data["money"]      = $money;
                $data["des"]        = $des;
                $data["enabled"]    = 1;
                $data["createdate"] = date('Y-m-d H:i:s');
                $data["creator"] = $this->_identity["username"];
                $_sim_money->insert($data);
            }
        
            $err['id'] = "ok";
            $err['msg'] = "ok";
            $ret[] = $err;
            echo json_encode($ret);
        }
    }

    private function getSimMenu() {
        $depth = 1;
        $parentid = 0;
        $menuname = 'menu_sim';
        $leftMenuProducts = Business_Helpers_Common::getMenuLev($depth, $parentid, $menuname);
        if (count($leftMenuProducts) > 0) {
            $i = 0;
            foreach ($leftMenuProducts as &$item) {
                $item["title_encode"] = htmlspecialchars($item["title"]);
                $cateid = $item['itemid'];
                $title = Business_Common_Utils::adaptTitleLinkURLSEO($item['title']);
                if ($_cateid == $cateid)
                    $active = 'active';
                else
                    $active = '';
                switch ($cateid) {
                    default:
                        $item['link'] = SEOPlugin::getSimLink($cateid, $title);
                        break;
                }

                if ($i++ == count($leftMenuProducts) - 1)
                    $item['class'] = 'class="' . $active . ' last"';
                else
                    $item['class'] = 'class=' . $active;
            }
        }

        return $leftMenuProducts;
    }
    public function listAction() {
        $menu_sim = $this->getSimMenu();
        $this->view->items = $menu_sim;
//        echo '<pre>';var_dump($menu_sim);exit();
       $check_sim = $this->_request->getParam('check_sim','');
       $title = $this->_request->getParam('title',"");
       $cateid = $this->_request->getParam('cateid',"");
       $this->view->cateid = $cateid;
       $this->view->title = $title;
//            var_dump($menuname);exit();
        if ($check_sim == '' || is_null($check_sim)){
            $check_sim = $this->_default_menu;
        }
        $this->view->check_sim = $check_sim;
        $this->view->plist = $this->_plist;
        
        
        $sim = Business_Addon_Sim::getInstance();
        $listSim = $sim->countList($cateid,$check_sim,$title);
        $total = (int) $listSim["count(*)"];
        $records = 20;
        Business_Ws_Common_Paging::setRPP($records);
        $page = $this->_request->getParam('page', '');
        if ($page == '') {
            $page = $this->_session->page;
        }
        ((int) $page == 0 ? $page = 1 : true);
        if ($page > ($total / $records) + 1)
        {
           $page = 1; 
        }
        $this->_session->page = $page;
        $offset = ($page - 1) * $records;
        $this->view->no = $offset + 1;
        $link_to_paging = "/admin/user/sim/list?cateid=$cateid&";
        $paging_template = Business_Ws_Common_Paging::doPaging($page, $total, $link_to_paging, $page_range = 5, 'tg');
        $this->view->paging_template = $paging_template;
        $plist = $sim->getList($cateid,$offset, $records,$check_sim,$title);
        $this->view->items2 = $plist;
        
        
    }
    

    public function editAction() {
        
    }

    public function deleteAction(){
            $this->_helper->Layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            $id = $this->_request->getParam("id");
//            var_dump($id);exit();
            Business_Addon_Sim::getInstance()->_delete($id);
        
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
            $this->_helper->Layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            $flag = $this->_request->getParam("flag");
            $id = (int)$this->_request->getParam("itemid");
            $price = $this->_request->getParam("price",0);
            $price_tm = str_replace(",", "", $price);
            $cateid = $this->_request->getParam("cateid");
            $ret = array();
            $data = array();
            if ($id == 0) {
                $ret = $this->isValid($ret, $price_tm);
            }
            if (count($ret) > 0) {
                echo json_encode($ret);
            } else {
                if ($id > 0) {
                    if($flag == "price"){
                        $data["price"]=$price_tm;
                    }
                    if($flag == "cateid"){
                        $data["cateid"] = $cateid;
                    }
                    Business_Addon_Sim::getInstance()->update($id,$data);
                }else {
//                    Business_Addon_Sim::getInstance()->insert($data);
                }
                $err['id'] = "ok";
                $err['msg'] = "ok";
                $ret[] = $err;
                echo json_encode($ret);
            }
    }

}

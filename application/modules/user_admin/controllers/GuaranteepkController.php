<?php

/**
 * User Admin Home Controller
 * @author: nghidv
 */
class User_Admin_GuaranteepkController extends Zend_Controller_Action {

    private $_identity;
    private $skey="BHVT2016HNAM";
    private $skey2="BHVT2016";
    public function init() {
        // do something
        BlockManager::setLayout('appbh');
        $auth = Zend_Auth::getInstance(); 
        $this->_identity = (array) $auth->getIdentity();
    }
    public function reportAction(){
        die();
        $__bs = Business_Addon_GuaranteePk::getInstance();
        $_option = Business_Addon_Options::getInstance();
        $d=date('Y-m-d');
        $k=$_option->pre_date($d, 6);
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
        $q= $this->_request->getParam("q");
        $this->view->q= $q;
        $list = $__bs->get_list_by_date($q,$start,$end);
        foreach ($list as $val){
            $__strid[] = $val["id"];
        }
        if($__strid != NULL){
            $strid = implode(",", $__strid);
            $slist = Business_Addon_TransferGuarantee::getInstance()->get_list_by_idguarantee2($strid);
            foreach ($slist as $val2){
                $money_dathu[$val2["idguarantee"]] = $val2["money"];
            }
        }
        $this->view->list = $list;
        $this->view->slist = $slist;
        $this->view->money_dathu = $money_dathu;
        $list_status_guarantee = $_option->getBH();
        $this->view->list_status_guarantee = $list_status_guarantee;
        $list_ok = $_option->get_ok();
        $this->view->list_ok = $list_ok;
    }

    public function printBaoHiemAction() {
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $bill_id = $this->_request->getParam('bill_id');
        $imei_may = $this->_request->getParam('imes_may');
        if($bill_id && $imei_may) {
            $where = array(
                "up.id_addon_user like '%$bill_id%'",
                "abh.imei_may like '%$imei_may%'",
            );
            $_users_products = Business_Addon_UsersProducts::getInstance();
            $list = $_users_products->getBaoHiemWhere($where);
            if($list) {
                $item = $list[0];
                $list_store = Business_Common_Users::getInstance()->getListByUname(FALSE);
                $storename = '';
                foreach ($list_store as $store){
                    if($store["userid"] == $item['vote_id']){
                        $storename = $store["storename"];
                        break;
                    }
                }
                $data = explode(',',$item['group_data']);
                $data = explode('|',$data[count($data) - 1]);
                $free = $data[3];
                $phieu = $data[1]?$data[1]:'';
                $status = $free==1?'Đổi bù '.number_format($item['bh_price'],0,',','.').'đ':($free==2?'Thay miễn phí 30 ngày':'Bảo hành');
                $customer = Business_Addon_Customer::getInstance()->get_list_by_search2($item['phone_addon']);
                $customer = $customer[0];
                $imei_may = $item["imei_may"]?explode(',',$item["imei_may"]):array();
                ob_start();
                ?>
                <center>
                    <table style="width: 531px;font-family: Arial;border-collapse: collapse;border-spacing: 0;">
                        <tr>
                            <td style="font-size: 15px;padding: 5px;border: 1px solid #ccc;width: 50%;">Thông tin Khách hàng</td>
                            <td style="font-size: 15px;padding: 5px;border: 1px solid #ccc;width: 50%;">Thông tin sản phẩm</td>
                        </tr>
                        <tr>
                            <td style="font-size: 15px;padding: 5px;border: 1px solid #ccc;width: 50%;">Họ tên: <?php echo $item['fullname_addon']; ?></td>
                            <td style="font-size: 15px;padding: 5px;border: 1px solid #ccc;width: 50%;">Model: <?php echo $item['products_name']; ?></td>
                        </tr>
                        <tr>
                            <td style="font-size: 15px;padding: 5px;border: 1px solid #ccc;width: 50%;">Địa chỉ: <?php echo $customer['address']; ?></td>
                            <td style="font-size: 15px;padding: 5px;border: 1px solid #ccc;width: 50%;">Imei/seri KCL: <?php echo $item['imes']; ?></td>
                        </tr>
                        <tr>
                            <td style="font-size: 15px;padding: 5px;border: 1px solid #ccc;width: 50%;">Điện thoại: <?php echo $item['phone_addon']; ?></td>
                            <td style="font-size: 15px;padding: 5px;border: 1px solid #ccc;width: 50%;">Imei/seri máy được dán: <?php echo $imei_may[count($imei_may)-1]; ?></td>
                        </tr>
                        <tr>
                            <td style="font-size: 15px;padding: 5px;border: 1px solid #ccc;width: 50%;"></td>
                            <td style="font-size: 15px;padding: 5px;border: 1px solid #ccc;width: 50%;">Tình trạng: <?php echo $status; ?></td>
                        </tr>
                        <tr>
                            <td style="font-size: 15px;padding: 5px;border: 1px solid #ccc;width: 50%;"></td>
                            <td style="font-size: 15px;padding: 5px;border: 1px solid #ccc;width: 50%;">Ngày đổi: <?php echo date('d/m/Y', strtotime($item['changed_date'])) ?></td>
                        </tr>
                        <tr>
                            <td style="font-size: 15px;padding: 5px;border: 1px solid #ccc;width: 50%;"></td>
                            <td style="font-size: 15px;padding: 5px;border: 1px solid #ccc;width: 50%;">Số phiếu: <?php echo $phieu; ?></td>
                        </tr>
                    </table>
                </center>
                <?php
                $html = ob_get_contents();
                ob_end_clean();
            }
            $json = ['stt'=>1,'html'=>$html];
        }
        else {
            $json = ['stt'=>0];
        }
        echo json_encode($json);
    }

    public function printBaoHanhAction() {
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $bill_id = $this->_request->getParam('bill_id');
        $imei_may = $this->_request->getParam('imes_may');
        if($bill_id && $imei_may) {
            $where = array(
                "up.id_addon_user like '%$bill_id%'",
                "abh.imei_may like '%$imei_may%'",
            );
            $_users_products = Business_Addon_UsersProducts::getInstance();
            $list = $_users_products->getBaoHiemWhere($where);
            if($list) {
                $item = $list[0];
                $_general = Business_Addon_General::getInstance();
                $service = $_general->getProductsByVT("'{$item['dt_ma_vt']}'");
                $price = $service[0]['price'];
                $list_store = Business_Common_Users::getInstance()->getListByUname(FALSE);
                $storename = '';
                foreach ($list_store as $store){
                    if($store["userid"] == $item['vote_id']){
                        $storename = $store["storename"];
                        break;
                    }
                }
                $date=date_create($item['create_date']);
                date_add($date, date_interval_create_from_date_string("1 months"));
                $expired = date_format($date,"d/m/Y");

                $date=date_create($item['create_date']);
                date_add($date, date_interval_create_from_date_string("6 days"));
                $freeday = date_format($date,"d/m/Y");

                $date=date_create($item['create_date']);
                date_add($date, date_interval_create_from_date_string("7 days"));
                $nofreeday = date_format($date,"d/m/Y");

                $start = date('d/m/Y',strtotime($item['create_date']));

                $imei_may = $item["imei_may"]?explode(',',$item["imei_may"]):array();
                ob_start();
                ?>
                <center>
                    <table style="width: 650px;font-family: Arial;border-collapse: collapse;border-spacing: 0;border: 2px solid #333;">
                        <tr>
                            <td colspan="2" style="text-align: center;font-size: 20px; padding: 5px 5px 0;">
                                <b>QUY ĐỊNH BẢO HÀNH/ĐỔI BÙ DÁN CƯỜNG LỰC</b></td>
                        </tr>
                        <tr>
                            <td colspan="2" style="font-size: 15px; padding: 0 5px;">Cửa hàng: <b><?php echo $storename;?></b></td>
                        </tr>
                        <tr>
                            <td colspan="2" style="font-size: 15px; padding: 0 5px;">Khách hàng: <b><?php echo $item['fullname_addon']; ?></b></td>
                        </tr>
                        <tr>
                            <td style="font-size: 15px; padding: 0 5px;">Imei dán cường lực: <b><?php echo $item['imes']; ?></b></td>
                            <td style="font-size: 15px; padding: 0 5px;">Imei máy: <b><?php echo $imei_may[count($imei_may)-1]; ?></b></td>
                        </tr>
                        <tr>
                            <td colspan="2" style="font-size: 15px; padding: 0 5px;">Bảo hành lỗi nhà sản xuất (bong bóng, bụi, keo, hở viền): <b><?php echo $start; ?></b> đến <b><?php echo $expired; ?></b></td>
                        </tr>
                        <tr>
                            <td colspan="2" style="font-size: 15px; padding: 0 5px;">Bảo hành bể/nứt/mẻ: <b><?php echo $start; ?></b> đến <b><?php echo $freeday; ?></b></td>
                        </tr>
                        <tr>
                            <td colspan="2" style="font-size: 15px; padding: 0 5px;">Phí đổi bù <b><?php echo number_format($price,0,',','.').'đ' ?>/lần</b>: <b><?php echo $nofreeday; ?></b> đến <b><?php echo $expired; ?></b></td>
                        </tr>
                        <tr>
                            <td colspan="2" style="font-size: 15px; padding: 0 5px 5px;">
                                <div style="font-size: 15px;">Quy định:</div>
                                <div style="font-size: 15px;">+ Phí đổi bù được áp dụng khi dán bị mẻ, nứt, bể sau 7 ngày hoặc dán cường lực không phát sinh hư hỏng mà  KH có nhu cầu đổi mới nhưng vẫn nằm trong giới hạn 30 ngày.</div>
                                <div style="font-size: 15px;">+ Dán cường lực bảo hành/đổi bù được tính trong vòng 30 ngày kể từ ngày mua in trên phiếu.</div>
                                <div style="font-size: 15px;">+  Sản phẩm được bảo hành/đổi bù mới 100%, cùng loại, cùng màu sắc với sản phẩm ban đầu.</div>
                                <div style="font-size: 15px;">+ Dán cường lực được bảo hành/đổi bù phải còn được dán trên máy có imei đăng ký lúc mua.</div>
                                <div style="font-size: 15px;">+ KH dán tại chỗ và Hnam thu hồi lại dán cũ.</div>
                                <div style="font-size: 15px;">+ KH cung cấp PGH/PBH/ thông tin mua hàng lúc ban đầu cho nhân viên kiểm tra.</div>
                                <div style="font-size: 15px;">+ Không áp dụng hoàn tiền trong mọi trường hợp.</div>
                            </td>
                        </tr>
                    </table>
                </center>
                <?php
                $html = ob_get_contents();
                ob_end_clean();
            }
            $json = ['stt'=>1,'html'=>$html];
        }
        else {
            $json = ['stt'=>0];
        }
        echo json_encode($json);
    }

    public function uploadBaoHiemAction() {
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $__option = Business_Addon_Options::getInstance();
        $_users_products = Business_Addon_UsersProducts::getInstance();
        $_general = Business_Addon_General::getInstance();
        if($this->_request->isPost()){
            $itemid = $this->_request->getParam('itemid');
            $bill_id = $this->_request->getParam('bill_id');
            $imes_md = $this->_request->getParam('imes_md');
            $imei_may = trim($this->_request->getParam('imes_may'));
            $note = str_replace(array(',','|'),array(';','/'),$this->_request->getParam('note'));
            $cbillid = $this->_request->getParam('cbillid');
            $ma_vt = $this->_request->getParam('ma_vt');
            $anhgoc2 = $this->_request->getParam('anhgoc2');
            $free = $this->_request->getParam('free');
            $cache = GlobalCache::getCacheInstance();
            $key = $this->_request->getParam('keycache');
            $cache->setCache($key, false, 3600);
            $mb_id = $this->_identity["userid"];
            $vote_id = $this->_identity["parentid"];
            $extensions = array("jpeg", "jpg", "png", "gif");
            $bill = $_users_products->getBaoHiemByBill($bill_id,$imes_md);
            $count = $bill ? $bill[0]['count'] : 0;
            $image_name = "image-$imei_may-$count";
            $path = BASE_PATH . "/uploads/bao-hiem/$bill_id";
            $__file = $_FILES['image'];
            $check_upload = $__option->upload($path, $extensions, $__file, $image_name);
            if ($check_upload[0]["msg"] != "ok") {
                foreach ($check_upload as $ch) {
                    $ret[] = $ch;
                }
            } else {
                if($cbillid) {
                    $bill_check = $_general->getDataDB('users_products','autoid',"is_actived = 1 and id_addon_user = $cbillid and imes = '$ma_vt'");
                    if (!$bill_check) {
                        $err["id"] = "cbillid";
                        $err["msg"] = "Phiếu thu không phù hợp";
                        $ret = $err;
                        echo json_encode($ret);
                        die();
                    }
                }
                $file_upload = $check_upload[0]["name_files"];
                $data = [
                    'bill_id' => $bill_id,
                    'imei_md' => $imes_md,
                    'imei_may' => $imei_may,
                    'itemid' => $itemid,
                    'note' => $note,
                    'bill_change' => $cbillid,
                    'anhgoc2' => $anhgoc2,
                    'ma_vt' => $ma_vt,
                    'free' => $free,
                    'id_users' => $mb_id,
                    'vote_id' => $vote_id,
                    'created' => date('Y-m-d H:i:s'),
                    'image' => $file_upload,
                ];
                $bh_id = $_users_products->insertBaoHiem($data);
                $ret = array('stt' => 1, 'msg' => 'Lưu thành công');
            }
            if ($file_upload == NULL) {
                $err["id"] = "file_upload";
                $err["msg"] = "Vui lòng upload file hình.";
                $ret = $err;
            }
        }
        echo json_encode($ret);
    }

    public function updatePricePbhAction() {
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $id = $this->_request->getParam('id');
        $price = $this->_request->getParam('price');
        if($id and $price) {
            $data = array(
                'price' => $price,
            );
            $_general = Business_Addon_General::getInstance();
            $id = $_general->updateDB('addon_baohiem_products',$data,"id = $id");
        }
        $this->_redirect('/admin/user/guaranteepk/products-bao-hiem');
    }

    public function removePbhAction() {
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $id = $this->_request->getParam('id');
        if($id) {
            $data = array(
                'status' => 0,
            );
            $_general = Business_Addon_General::getInstance();
            $id = $_general->updateDB('addon_baohiem_products',$data,"id = $id");
        }
        $this->_redirect('/admin/user/guaranteepk/products-bao-hiem');
    }

    public function addProductsBaoHiemAction() {
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $itemid = $this->_request->getParam('itemid');
        $ma_vt = $this->_request->getParam('ma_vt');
        if($itemid and $ma_vt) {
            $data = array(
                'itemid' => $itemid,
                'ma_vt' => $ma_vt,
            );
            $_general = Business_Addon_General::getInstance();
            $id = $_general->insertDB('addon_baohiem_products',$data);
        }
        $this->_redirect('/admin/user/guaranteepk/products-bao-hiem');
    }

    public function productsBaoHiemAction() {
        $_general = Business_Addon_General::getInstance();
        $list = $_general->getProductsGuaranteePK();
        $this->view->list = $list;
        $idregency = $this->_identity["idregency"];
        $userid = $this->_identity["userid"];
        $add=0;
        if(in_array($userid,array(8))){
            $add=1;
        }
        $this->view->add = $add;
    }

    public function checkBaoHiemAction() {
        //ini_set('display_errors', '1');
        $_option = Business_Addon_Options::getInstance();
        $_users_products = Business_Addon_UsersProducts::getInstance();
        $_general = Business_Addon_General::getInstance();

        $list_price = $_general->getProductsGuaranteePK();
        $newListMaVT = array();
        foreach ($list_price as $pitem) {
            $newListMaVT[$pitem['itemid']] = $pitem['ma_vt'];
        }
        $this->view->mavtList = $newListMaVT;

        $listVT = array_unique(array_column($list_price,'ma_vt'));
        $listVT = "'".implode("','",$listVT)."'";
        $listPrice = $_general->getProductsByVT($listVT);
        $newPriceList = array();
        foreach ($listPrice as $pitem) {
            $newPriceList[$pitem['code']] = $pitem['price'];
        }
        $this->view->priceList = $newPriceList;
        require_once 'Mobile_Detect.php';
        $detect = new Mobile_Detect;
        if ( $detect->isMobile() ) {
            $this->_helper->viewRenderer('check-bao-hiem-mobile');
        }
        $vote_id = $this->_identity["parentid"];
        $idregency = $this->_identity["idregency"];
        $this->view->idregency = $idregency;
        $this->view->user_id = $this->_identity["userid"];
        $bgd = 0;
        if($_option->isBGD($idregency) || in_array($this->_identity["userid"],array('398','436'))){
            $bgd=1;
            $vote_id = (int)  $this->_request->getParam("storeid");
        }
        $bgd=1;
        $vote_id = (int)  $this->_request->getParam("storeid");
        $this->view->bgd = $bgd;

        $list_store = Business_Common_Users::getInstance()->getListByUname(FALSE);
        foreach ($list_store as $store){
            $storename[$store["userid"]] = $store["storename"];
        }
        $this->view->list_store = $list_store;
        $this->view->storename = $storename;


        $where = array();
        $having = '';

        $start_end           = $this->_request->getParam("start_end");
        if($start_end ==null){
            $start_end = date("F j, Y")." - ".date("F j, Y");
        }
        $this->view->start_end = $start_end;
        $start  = $_option->getStartDate($start_end);
        $end  = $_option->getEndDate($start_end);
        $this->view->start = $start;
        $this->view->end = $end;

        $where[] = "up.create_date >= '$start' and up.create_date < '$end'";

        $type = $_users_products->getBaoHiemType(false);
        if($vote_id) {
            $this->view->storeid = $vote_id;
            $where[] = "up.vote_id = ".$vote_id;
        }
        if($this->_request->getParam('bill_imei')) {
            $this->view->bill_imei = $this->_request->getParam('bill_imei');
            $where[] = "(up.id_addon_user = '".$this->_request->getParam('bill_imei'). "' or up.imes = '".$this->_request->getParam('bill_imei'). "' or abh.imei_may = '".$this->_request->getParam('bill_imei'). "' or up.phone_addon like '%".$this->_request->getParam('bill_imei'). "%' or up.fullname_addon like '%".$this->_request->getParam('bill_imei')."%')";
        }
        $cache = GlobalCache::getCacheInstance();
        $key = 'CBH'.implode(',',$where);
        $list = $cache->getCache($key);
        $list = false;
        if(!$list) {
            if (!empty($where)) {
                if($vote_id) {
                    $where[] = "up.vote_id = $vote_id";
                }
                $list = $_users_products->getBaoHiemWhere($where,$having);
            } else {
                $list = $_users_products->getBaoHiem($vote_id);
            }
            $cache->setCache($key,$list,3600);
        }

        $bh_users = implode(',',array_filter(array_column($list,'bhid_users')));
        $bh_users = implode(',',array_unique((explode(',',$bh_users))));
        $newUsers = array();
        if($bh_users) {
            $listUsers = Business_Common_Users::getInstance()->getListByUserid($bh_users);
            foreach ($listUsers as $user) {
                $newUsers[$user['userid']] = $user['fullname'];
            }
            $this->view->users = $newUsers;
        }
        $this->view->key = $key;
        $this->view->list = $list;
    }

    public function reportBaoHiemAction() {
        $_option = Business_Addon_Options::getInstance();
        $_users_products = Business_Addon_UsersProducts::getInstance();
        $_general = Business_Addon_General::getInstance();
        require_once 'Mobile_Detect.php';
        $detect = new Mobile_Detect;
        if ( $detect->isMobile() ) {
            $this->view->mobile = 1;
        }
        $start_end = $this->_request->getParam("start_end");
        $vote_id = $this->_identity["parentid"];
        $idregency = $this->_identity["idregency"];
        $bgd = 0;
        if($_option->isBGD($idregency) || in_array($this->_identity["userid"],array('398','436'))){
            $bgd=1;
            $vote_id = (int)  $this->_request->getParam("storeid");
        }
        $this->view->bgd = $bgd;

        if($start_end ==null){
            $start_end = date("F j, Y")." - ".date("F j, Y");
        }
        $this->view->start_end = $start_end;
        $start  = $_option->getStartDate($start_end);
        $end  = $_option->getEndDate($start_end);
        $this->view->start = $start;
        $this->view->end = $end;

        $list_store = Business_Common_Users::getInstance()->getListByUname(FALSE);
        foreach ($list_store as $store){
            $store_int = (int) $store["storename"];
            if($store_int) {
                $stores[$store['userid']] = $store["abbreviation"];
            }
            $storename[$store["userid"]] = $store["storename"];
        }

        $stores['total'] = 'Tổng';
        $this->view->list_store = $stores;
        $this->view->storename = $storename;
        $this->view->list_stores = $list_store;

        $type = $_users_products->getBaoHiemType(false);
        $newType = array();

        $where = "created >= '$start' and created <= '$end' and note != '' and ma_vt != ''";
        if($vote_id) {
            if ( $detect->isMobile() ) {
                $this->_helper->viewRenderer('report-bao-hiem-chi-nhanh-mobile');
            }
            else {
                $this->_helper->viewRenderer('report-bao-hiem-chi-nhanh');
            }
            $this->view->storeid = $vote_id;
            $where = array();
            $where[] = "((abh.created >= '$start' and abh.created < '$end' and abh.note != '' and abh.ma_vt != '' and abh.vote_id = $vote_id) or abh.image like '%0.%' or abh.anhgoc2 = 1)";
            $list = $_users_products->getBaoHiemWhere($where, 'count > 1');

            $list_price = $_general->getProductsGuaranteePK();
            $newListMaVT = array();
            foreach ($list_price as $pitem) {
                $newListMaVT[$pitem['itemid']] = $pitem['ma_vt'];
            }
            $this->view->mavtList = $newListMaVT;

            $listVT = array_unique(array_column($list_price,'ma_vt'));
            $listVT = "'".implode("','",$listVT)."'";
            $listPrice = $_general->getProductsByVT($listVT);
            $newPriceList = array();
            foreach ($listPrice as $pitem) {
                $newPriceList[$pitem['code']] = $pitem['price'];
            }
            $this->view->priceList = $newPriceList;

            $this->view->list = $list;
        }
        else {
            //$list = $_users_products->getBaoHiemWhere($where, 'count > 0');
            $list = $_general->getDataDB('addon_baohiem', 'free,ma_vt,vote_id',$where);
            $newsList = array();
            $newType = array();
            foreach ($list as $item) {
                if($item['ma_vt']) {
                    if (!in_array($item['ma_vt'], $newType)) {
                        $newType[$item['ma_vt']] = $item['ma_vt'];
                    }
                    if (in_array($item['vote_id'], array_keys($stores))) {
                        //Add SL Ma_VT theo CN
                        if (!isset($newsList[$item['ma_vt']][$item['vote_id']])) {
                            $newsList[$item['ma_vt']][$item['vote_id']] = 0;
                        }
                        $newsList[$item['ma_vt']][$item['vote_id']]++;
                        //Add tổng Ma_VT
                        if (!isset($newsList[$item['ma_vt']]['total'])) {
                            $newsList[$item['ma_vt']]['total'] = 0;
                        }
                        $newsList[$item['ma_vt']]['total']++;
                        //Add tổng CN
                        if (!isset($newsList['total'][$item['vote_id']])) {
                            $newsList['total'][$item['vote_id']] = 0;
                        }
                        $newsList['total'][$item['vote_id']]++;

                        if (!isset($newsList['total']['total'])) {
                            $newsList['total']['total'] = 0;
                        }
                        //Add tổng DV
                        $newsList['total']['total']++;
                    }
                }
            }
            if(!empty($newsList)) {
                $newType['total'] = "Tổng";
            }
            $this->view->type = $newType;
            $this->view->list = $newsList;
        }
    }

    public function addbhscAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $__bs = Business_Addon_GuaranteeBhsc::getInstance();
        $id_guaranteeid = (int)  $this->_request->getParam("id_guaranteeid");
        $__money_dvsc = $this->_request->getParam("money_dvsc");
        $money_dvsc = str_replace(",", "", $__money_dvsc);
        $status = (int)  $this->_request->getParam("status");
        $ids = (int)  $this->_request->getParam("ids");
        $note = $this->_request->getParam("note");
        if($id_guaranteeid ==0){
            die('no');
        }else{
            $detail = $__bs->getDetail($ids);
            if($status>0){
               $data["status"] = $status; 
            }
            $data["money_dvsc"] = $money_dvsc;
            $chagre = $money_dvsc*15/100;
            if($chagre>0 && $chagre<100000){
               $chagre=100000; 
            }
            $status_guarantee = $this->_request->getParam("status_guarantee");
            $ncc_ok = $this->_request->getParam("ncc_ok");
            $flag = $this->_request->getParam("flag");
            $money = $money_dvsc;
            if($status_guarantee ==1){
                if($ncc_ok==2 && $flag==2){
                    $money = $money_dvsc + $chagre;
                }
            }else{
                $money = $money_dvsc + $chagre;
            }
            
            $data["money"] = $money;
            $data["id_guaranteeid"] = $id_guaranteeid;
            $data["enabled"] = 1;
            $data["ncc_ok"] = $ncc_ok;
            $data["note"] = $note;
            if($ids ==0){
                //insert
                $data["enabled"] = 1;
                $data["userid"] = $this->_identity["userid"];
                $data["datetime"] = date('Y-m-d H:i:s');
                $__bs->insert($data);
            }else{
                if($detail["status"]!=2){
                    $data["userid_update"] = $this->_identity["userid"];
                    $data["datetime_update"] = date('Y-m-d H:i:s');
                    $__bs->update($ids,$data);
                }
                
                //update
            }
        }
        
    }
    public function statusbhscAction(){
        die();
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $status = (int)  $this->_request->getParam("status");
        $id = (int)  $this->_request->getParam("id");
        $__bhsc = Business_Addon_GuaranteeBhsc::getInstance();
        $detail = $__bhsc->getDetail($id);
        if($detail != NULL){
            if($detail["status"] !=2){
                $data["status"] =$status;
                $__bhsc->update($id, $data);
            }
        }
        header("location:".$_SERVER['HTTP_REFERER']);
        exit();
    }

    public function editAction(){
        die();
        $idregency = (int)$this->_identity["idregency"];
        $kq = $this->_request->getParam("kq");
        $this->view->kq = $kq;
        $token = $this->_request->getParam("token");
        
        $ids = (int)  $this->_request->getParam("ids");
        $ztoken = md5($this->skey2.$ids);
        
        $this->view->ids = $ids;
        $this->view->skey2 = $this->skey2;
        $__bs = Business_Addon_GuaranteePk::getInstance();
        $_option = Business_Addon_Options::getInstance();
        $list_ok = $_option->get_ok();
        $bgd=0;
        if($_option->isBGD($idregency) || $this->isBHVT($idregency)){
           $bgd=1; 
        }
        $this->view->bgd = $bgd;
        $list_bh = $_option->getBH();
        $list_status_bhsc = Business_Addon_Options::getInstance()->get_status_bhsc();
//        if($this->_request->isPost()){
            if($kq != NULL){
                $list = $__bs->get_list_search_id_imei($kq);
                
//                if($__strId != NULL){
//                    $strid = implode(",", $__strId);
//                    $list_bhsc = Business_Addon_GuaranteeBhsc::getInstance()->get_list_by_guaranteeid($strid);
//                    foreach ($list_bhsc as $bh){
//                        $datetime[$bh["id_guaranteeid"]] = $bh["datetime"];
//                        $money_dvsc[$bh["id_guaranteeid"]] = $bh["money_dvsc"];
//                        $money[$bh["id_guaranteeid"]] = $bh["money"];
//                        $status[$bh["id_guaranteeid"]] = $bh["status"];
//                    }
//                }
            }
//        }
            
         if($token != NULL){
            if($token != $ztoken){
               die('no access'); 
            }
            $detail2 = Business_Addon_GuaranteeBhsc::getInstance()->getDetail($ids);
            $list = $__bs->get_list_by_id($detail2["id_guaranteeid"]);
        }
        foreach ($list as $val){
            $sstatus_guarantee2[$val["id"]] = $list_bh[$val["status_guarantee"]];
        }
        $d=date('Y-m-d');
        $k=$_option->pre_date($d, 6);
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
        $__status =  $this->_request->getParam("status");
        $this->view->sstatus = $__status;
        $ncc_ok =  $this->_request->getParam("ncc_ok");
        $this->view->ncc_ok = $ncc_ok;
        $__userid = $this->_identity["userid"];
        if($bgd ==1){
            $__userid = (int)$this->_request->getParam("uid");
        }
        $this->view->uid = $__userid;
        $slist = Business_Addon_GuaranteeBhsc::getInstance()->get_list($start,$end,$__status,$ncc_ok,$__userid); 
        foreach ($slist as $bh){
            $datetime[$bh["id"]] = $bh["datetime"];
            $money_dvsc[$bh["id"]] = $bh["money_dvsc"];
            $money[$bh["id"]] = $bh["money"];
            $status[$bh["id"]] = $bh["status"];
            $sstatus[$bh["id"]] = $list_status_bhsc[$bh["status"]];
            $sncc_ok[$bh["id"]] = $bh["ncc_ok"];
            $snote[$bh["id"]] = $bh["note"];
            $__strId[] = $bh["id_guaranteeid"];
        }
        if($__strId != NULL){
            $strid = implode(",", $__strId);
            $list2 = $__bs->get_list_by_id($strid);
            foreach ($list2 as $val){
                $sitem_name[$val["id"]] = $val["item_name"];
                $simei[$val["id"]] = $val["imei"];
                $sstatus_guarantee[$val["id"]] = $list_bh[$val["status_guarantee"]];
                $sflag[$val["id"]] = $val["flag"];
            }
        }
        $this->view->list = $list;
        $this->view->slist = $slist;
        $this->view->sitem_name = $sitem_name;
        $this->view->simei = $simei;
        $this->view->datetime = $datetime;
        $this->view->money_dvsc = $money_dvsc;
        $this->view->money = $money;
        $this->view->status = $status;
        $this->view->sstatus = $sstatus;
        $this->view->sncc_ok = $sncc_ok;
        $this->view->snote = $snote;
        $this->view->list_status_bhsc= $list_status_bhsc;
        $this->view->list_ok = $list_ok;
        $this->view->sstatus_guarantee = $sstatus_guarantee;
        $this->view->sstatus_guarantee2 = $sstatus_guarantee2;
        $this->view->list_bh = $list_bh;
        $this->view->sflag = $sflag;
        $this->view->get_list_bhsc_orther = $_option->get_list_bhsc_orther();
    }
    

    public function searchWarrantyAction(){
        $kq = $this->_request->getParam("kq");
        $this->view->kq = $kq;
        $__bs = Business_Addon_UsersProducts::getInstance();
        $__option = Business_Addon_Options::getInstance();
        $ngaybaohanh  =0;
        $kq = trim($kq);
        if($kq != NULL){
            $sdetail = Business_Addon_GuaranteePk::getInstance()->get_list_detail_imei($kq);
            if($sdetail != NULL){
                $ngaybaohanh = strtotime($sdetail["datetime"]);
            }
            $list = $__bs->search($kq);
            foreach ($list as &$val){
                if(strtotime($val["create_date"]) <$ngaybaohanh){
                    continue;
                }
                if($val["productsid"]==4){
                    $w =6;
                }else{
                    $w =12;
                }
                
                if((int)$val["warranty"]>0){
                    $w = $val["warranty"];
                }
                $sdate = strtotime($val["create_date"]);
                $date1 = $__option->add($val["create_date"], $w);
                
                $second_date = strtotime('now');
                $nday = strtotime($date1);
                
                $dif = abs();
                
                
                $val["day_end"] =0;
                $first_date = strtotime($date1);
                $datediff = abs($first_date - $second_date);
                $datediff2 = abs($first_date-$sdate);
                
                
                $date_used = abs(floor($datediff2 / (60*60*24)) - floor($datediff / (60*60*24)));
                
                $val["day_end"] = floor($datediff2 / (60*60*24)) - $date_used;
                
                
            }
            $this->view->list = $list;
            $dealine = 14;
            $this->view->dealine = $dealine;
        }
    }

    public function required(){
        $ret = array();
        $arr = array();
        $data_frm = $this->_request->getParams('data_frm');
        if($data_frm["imei"] ==null){
            $arr["id"] = "imei";
            $arr["msg"] = "IMEI không được để trống";
            $ret[] = $arr;
        }
//        if($data_frm["seri"] ==null){
//            $arr["id"] = "seri";
//            $arr["msg"] = "SERI không được để trống";
//            $ret[] = $arr;
//        }
        
        if($data_frm["itemid"] ==null){
            $arr["id"] = "itemid";
            $arr["msg"] = "Sản phẩm không được để trống";
            $ret[] = $arr;
        }
        if((int)$data_frm["flag"] ==0){
            $arr["id"] = "flag";
            $arr["msg"] = "Vui lòng chọn loại công ty hoặc hnam";
            $ret[] = $arr;
        }
        if((int)$data_frm["pid_color"] ==0){
            $arr["id"] = "pid_color";
            $arr["msg"] = "Màu không được để trống";
            $ret[] = $arr;
        }
        if((int)$data_frm["status_guarantee"] ==0){
            $arr["id"] = "status_guarantee";
            $arr["msg"] = "Tình trạng bảo hành của sản phẩm không được để trống";
            $ret[] = $arr;
        }
//        if($data_frm["producers"] ==null){
//            $arr["id"] = "producers";
//            $arr["msg"] = "Hãng bảo hành không được để trống";
//            $ret[] = $arr;
//        }
        $purchase_date = str_replace("/", "-", $data_frm["purchase_date"]) ;
        $__purchase_date       =   date('Y/m/d',  strtotime($purchase_date));
        
        $dealine = str_replace("/", "-", $data_frm["dealine"]) ;
        $__dealine       =   date('Y/m/d',  strtotime($dealine));
        $_idregency = $this->_identity["idregency"];
        $__option = Business_Addon_Options::getInstance();
        if($_idregency !=48){
            if($__purchase_date =='1970/01/01'){
                $arr["id"] = "purchase_date";
                $arr["msg"] = "Vui lòng nhập ngày mua sản phẩm";
                $ret[] = $arr;
            }
            if($__dealine =='1970/01/01'){
                $arr["id"] = "dealine";
                $arr["msg"] = "Vui lòng nhập ngày hẹn";
                $ret[] = $arr;
            }
        }
        $voucher = trim($data_frm["voucher"]);
        $ret2 = $this->check_voucher($voucher);
        if($ret2 != NULL){
           $ret = array_merge($ret,$ret2); 
        }
        return $ret;
    }
    public function checkVoucherAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $voucher = $this->_request->getParam("voucher");
        $voucher = trim($voucher);
        $ret = $this->check_voucher($voucher);
        if($voucher ==NULL){
            echo "";
            die();
        }
        if(count($ret) >0){
            for($i=0;$i<count($ret);$i++){
                $msg = $ret[$i]['msg'];
                $ids = $ret[$i]['id'];
                $msg .="<input name='code_value' id='code_value' type='hidden' value='0' />";
                echo $msg;
                die();
            }
        }else{
            $_wsvoucher = Business_Addon_Voucher::getInstance();
            $detail_voucher = $_wsvoucher->getDetailByCode($voucher);
            $code_value = 0;
            if($detail_voucher["type_ctkm"]==1){ //Hvip
                $code_value =10;
            }
            if($detail_voucher["type_ctkm"]==2){ //Hmember
               $code_value =5; 
            }
            $html ='Giảm '.number_format($code_value).'%';
            $html .=" <br/><input name='code_value' id='code_value' type='hidden' value='$code_value' />";
            echo  $html;
        }
    }

    public function check_voucher($voucher){
        $ret = array();
        $err = array();
        if($voucher != NULL){
            $_wsvoucher = Business_Addon_Voucher::getInstance();
            $detail_voucher = $_wsvoucher->getDetailByCode($voucher);
            $type_ctkm=0;
            if($detail_voucher["type_ctkm"]==1 || $detail_voucher["type_ctkm"]==2 ){
                $type_ctkm=1;
            }
            if($type_ctkm==0){ 
                $err['id'] = "voucher";
                $err['msg'] = "Mã voucher này không áp dụng cho bảo hành sửa chữa";
                $ret[] = $err;
            }
            if ($detail_voucher == null){
                $err['id'] = "voucher";
                $err['msg'] = "Mã voucher này không có thực";
                $ret[] = $err;
            }else{
                if ($detail_voucher["used"] == 1) {
                    $err['id'] = "voucher";
                    $err['msg'] = "Voucher này đã được sử dụng";
                    $ret[] = $err;
                } else {
                    if ((int) strtotime($detail_voucher["code_expired"]) > 0) {
                        if (strtotime($detail_voucher["code_expired"]) < $day_now) {
                            $err['id'] = "voucher";
                            $err['msg'] = "Voucher này đã hết hạn sử dụng";
                            $ret[] = $err;
                        }
                    }
                    if ($detail_voucher["number_used"] == 0) {
                        $err['id'] = "voucher";
                        $err['msg'] = "Voucher này đã hết số lần sử dụng";
                        $ret[] = $err;
                    }
                }
            }
        }
        return $ret;
    }
    public function update_voucher($voucher){
        // cập nhật sử dụng voucher =0
        $voucher = trim($voucher);
        $_wsvoucher = Business_Addon_Voucher::getInstance();
        $detail_voucher = $_wsvoucher->getDetailByCode($voucher);
        $number_used = $detail_voucher["number_used"] - 1;
        
        if ($number_used < 0) {
            $number_used = 0;
        }
                        
        if ($number_used == 0) {
            $data_voucher["used"] = 1;
        } else {
            $data_voucher["used"] = 0;
        }
        $data_voucher["number_used"] = $number_used;

        $data_voucher["code_store"] = $this->_identity["userid"];
        $data_voucher["code_updated"] = date('Y-m-d H:i:s');
        $_wsvoucher->update($voucher, $data_voucher);
    }

    public function iframeAdd1Action(){
        $this->_helper->Layout()->disableLayout();
    }
    public function getImeiBbmhAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $imei = $this->_request->getParam("imei");
        $detail = Business_Addon_UsersProducts::getInstance()->get_detail_by_imei($imei);
        if($detail["cated_id"]==890 || $detail["cated_id"]==867 ){
            $products_id = $detail["products_id"];
            echo $detail["products_name"]."[".number_format($detail["products_price"])."]";
            echo "<input type='hidden' name='idbbmh' value='$products_id' >";
        }else{
            echo "Không có";
        }
    }
    public function getimeibyflagAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $imei = $this->_request->getParam("imei");
        $detail = Business_Addon_UsersProducts::getInstance()->get_detail_by_imei2($imei);
        echo json_encode($detail);
    }
    public function okCustomerAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $id = (int)  $this->_request->getParam("id");
        $ok = (int)  $this->_request->getParam("ok");
        $data["ok"] = $ok;
        $_guarantee = Business_Addon_GuaranteePk::getInstance();
        $_guarantee->update($id, $data);
    }
    public function bhangAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $id = (int)  $this->_request->getParam("id");
        $data["bhang"] = 1;
        $_guarantee = Business_Addon_GuaranteePk::getInstance();
        $_guarantee->update($id, $data);
    }
    public function delEnabledAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $id = (int)  $this->_request->getParam("id");
        $data["enabled"] =0;
        $token = $this->_request->getParam("token");
        $ztoken = md5($this->skey.$id);
        if($token != $ztoken){
            die("No access");
        }
        $_guarantee = Business_Addon_GuaranteePk::getInstance();
        $_guarantee->update($id, $data);
    }
    public function delbystoreAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $id = (int)  $this->_request->getParam("id");
        $data["enabled"] =0;
        $data["note_del"] ='Chi nhánh tự hủy '.date('d/m/Y H:i:s');
        $token = $this->_request->getParam("token");
        $ztoken = md5($this->skey.$id);
        if($token != $ztoken){
            die("No access");
        }
        $_guarantee = Business_Addon_GuaranteePk::getInstance();
        if($id >0){
            $detail = $_guarantee->getDetail($id);
            if($detail["isdel"]==0){
                $_guarantee->update($id, $data);
            }
        }
        
    }
    public function xnAction(){
        die();
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $id = (int)  $this->_request->getParam("id");
        $data["status"] =3;
        $data["creator_end"] = $this->_identity["username"];
        $token = $this->_request->getParam("token");
        $ztoken = md5($this->skey.$id);
        
        if($token != $ztoken){
            die("No access");
        }
        $_guarantee = Business_Addon_TransferGuarantee::getInstance();
        $_guarantee->update($id, $data);
    }
    public function chuyenAction(){
        die();
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $id = (int)  $this->_request->getParam("id");
        $data["status"] =4;
        $data["creator_end"] = $this->_identity["username"];
        $token = $this->_request->getParam("token");
        $ztoken = md5($this->skey.$id."4");
        
        if($token != $ztoken){
            die("No access");
        }
        $_guarantee = Business_Addon_TransferGuarantee::getInstance();
        $_guarantee->update($id, $data);
    }
    public function xn2Action(){
        die();
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $id = (int)  $this->_request->getParam("id");
        $data["status"] =5;
        $data["creator_end"] = $this->_identity["username"];
        $token = $this->_request->getParam("token");
        $ztoken = md5($this->skey.$id);
        
        if($token != $ztoken){
            die("No access");
        }
        $_guarantee = Business_Addon_TransferGuarantee::getInstance();
        $_guarantee->update($id, $data);
    }

    public function searchAction(){
        $this->_helper->Layout()->disableLayout();
    }
    public function searchsAction(){
        $this->_helper->Layout()->disableLayout();
        $_city                          = Business_Common_City::getInstance();
        $list_city                      = $_city->getListCity();
        $this->view->list_city          = $list_city;
        $_district                      = Business_Common_District::getInstance();
        
        $id_city                        = 1;
        if($id_city !=0){
            $list_district              = $_district->getListDistrict2($id_city);
        }
        $this->view->list_district      = $list_district;
        $phone = $this->_request->getParam("phone");
        $this->view->phone = $phone;
        $list = Business_Addon_Customer::getInstance()->search($phone);
        if($list == NULL){
            $this->_helper->viewRenderer('iframe-add1');
        }
        $this->view->list = $list;
    }
    public function imeiVendorsAction(){
        $imei = $this->_request->getParam("imei");
        $detail = Business_Addon_HistoryImeiVendors::getInstance()->get_detail_by_imei($imei);
        $this->view->detail = $detail;
        $this->view->imei_old = $imei;
        $_warranty_unit = Business_Addon_WarrantyUnit::getInstance();
        $sproducers = $_warranty_unit->getList();
        $this->view->sproducers = $sproducers;
        
        if($this->_request->isPost()){
            $data_frm = $this->_request->getParams("data_frm");
            $id = (int) $this->_request->getParam("id");
            $ret = array();
            $err = array();
            if((int)$data_frm["idvendors"] == 0){
                $err["id"] ="idvendors";
                $err["msg"] ="Vui lòng nhập nhà cung cấp";
                $ret[] = $err;
            }
            if($data_frm["imei_old"] == NULL){
                $err["id"] ="imei_old";
                $err["msg"] ="Vui lòng nhập imei cũ";
                $ret[] = $err;
            }
            if($data_frm["imei_new"] == NULL){
                $err["id"] ="imei_new";
                $err["msg"] ="Vui lòng nhập imei mới";
                $ret[] = $err;
            }
            if($data_frm["note"] == NULL){
                $err["id"] ="note";
                $err["msg"] ="Vui lòng nhập ghi chú vào đây";
                $ret[] = $err;
            }
            if(strlen($data_frm["note"]) < 10){
                $err["id"] ="note";
                $err["msg"] ="Ghi chú quá ngắn. Vui lòng nhập trên 10 ký tự";
                $ret[] = $err;
            }
            if(count($ret) >0){
                for($i=0;$i<count($ret);$i++){
                    $msg = $ret[$i]['msg'];
                    $ids = $ret[$i]['id'];
                    echo "<script>window.parent.show_msg('$msg','$ids');</script>";
                    die();
                }
            }else{
                $data["imei_new"] = $data_frm["imei_new"];
                $data["imei_old"] = $data_frm["imei_old"];
                $data["idvendors"] = $data_frm["idvendors"];
                $data["note"] = $data_frm["note"];
                $data["enabled"] = 1;
                $data["datetime"] = date('Y-m-d H:i:s');
                $data["creator"] = $this->_identity["username"];
                if((int)$this->_identity["userid"]>0){
                    if($id ==0){
                        Business_Addon_HistoryImeiVendors::getInstance()->insert($data);
                    }else{
                        Business_Addon_HistoryImeiVendors::getInstance()->update($id,$data);
                    }
                }
                $url = '/admin/user/guarantee/list';
                echo "<script>window.parent.show_success('LƯU THÀNH CÔNG','','$url');</script>";
                die();
            }
        }
        
    }
    
    public function saveCustomerAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $_option = Business_Addon_Options::getInstance();
        $_customer = Business_Addon_Customer::getInstance();
        $data_frm = $this->_request->getParams("data_frm");
        $ret = array();
        $arr = array();
        
        $ret = $_option->checkPhone($data_frm["phone"],"phone");
        if($data_frm["fullname"] ==  NULL){
            $arr["id"] = "fullname";
            $arr["msg"] = "Vui lòng nhập họ tên";
            $ret[] = $arr;
        }
        if(strlen($data_frm["phone"])>9){
            $detail = $_customer->getDetailByPhone($data_frm["phone"]);
        }
        if(count($ret) >0){
            for($i=0;$i<count($ret);$i++){
                $msg = $ret[$i]['msg'];
                $ids = $ret[$i]['id'];
                echo "<script>window.parent.show_msg('$msg','$ids');</script>";
                return;
            }
        }else{
            $sdata["fullname"] = $data_frm["fullname"];
            $sdata["phone"] = $data_frm["phone"];
            $sdata["email"] = $data_frm["email"];
            $sdata["address"] = $data_frm["address"];
            if(strtotime($data_frm["birthday"]) > 0){
               $sdata["birthday"] = $data_frm["birthday"]; 
            }
            $sdata["district"] = $data_frm["district"];
            $sdata["city"] = $data_frm["city"];
            $sdata["datetime"] = date('Y-m-d H:i:s');
            $sdata["enabled"] = 1;
            if($detail != NULL){
                $idcustomer = $detail["id"];
            }else{
                $idcustomer = $_customer->insert($sdata);
            }
            echo "<script>window.parent.completes('LƯU THÀNH CÔNG',$idcustomer);</script>";
            return;
        }
        
    }

    public function iframeAddAction(){
        $this->_helper->Layout()->disableLayout();
        $_option = Business_Addon_Options::getInstance();
        $_zwf_user = Business_Common_Users::getInstance();
        $__customer = Business_Addon_Customer::getInstance();
        
        $__storeid = (int)  $this->_identity["parentid"];
//        $listmb = $_zwf_user->get_list_by_kh($__storeid,12);
//        foreach ($listmb as &$vl){
//            $vl["fullname"] = "Kỹ thuật ".$vl["fullname"];
//        }
        
        $receiver = array();
        $sreceiver = Business_Common_Users::getInstance()->getListUser("",0,1,26);
        foreach ($sreceiver as &$_items){
            $_items["fullname"] ="Luân chuyển ".$_items["fullname"];
            $receiver[$_items["userid"]] = $_items["fullname"];
        }
        
        if($listmb != NULL){
            $sreceiver = array_merge($sreceiver,$listmb);
        }
        $this->view->sreceiver = $sreceiver;
        $dt="4";
        $slist = Business_Ws_ProductsItem::getInstance()->getProducts2($dt);
        $this->view->slist = $slist;
        
        $_warranty_unit = Business_Addon_WarrantyUnit::getInstance();
        $sproducers = $_warranty_unit->getList();
        $this->view->sproducers = $sproducers;
        
        $_idregency = $this->_identity["idregency"];
        if($this->isStore($_idregency)){
            $this->_helper->viewRenderer('iframe-add2');
        }
        $bgd =0;
        if($_option->isBGD($_idregency) || $_idregency ==48){
            $bgd = 1;
        }
        $this->view->bgd = $bgd;
        $list_store = $_zwf_user->getListByUname(FALSE);
        $this->view->list_store = $list_store;
        $idcustomer = $this->_request->getParam("idcustomer");
        $this->view->idcustomer = $idcustomer;
        $detail_customer = $__customer->getDetail($idcustomer);
        $this->view->detail_customer = $detail_customer;
       
    }
    public function getWarrantyAction(){
        $_warranty_unit = Business_Addon_WarrantyUnit::getInstance();
        $sproducers = $_warranty_unit->getList();
        $this->view->sproducers = $sproducers;
    }
    public function addWarrantyAction(){
        
    }

    public function saveWarrantyAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $_warranty_unit = Business_Addon_WarrantyUnit::getInstance();
        $data_frm = $this->_request->getParams('data_frm');
        $name_warranty = $this->_request->getParam("name_warranty");
        $ret = array();
        $arr = array();
        if($name_warranty == null){
            $arr["id"] ="name_warranty";
            $arr["msg"] ="Vui lòng nhập tên đơn vị bảo hành mới";
            $ret[] = $arr;
        }
        if(count($ret) >0){
            echo json_encode($ret);
            return;
        }else{
            $data["name"] = $name_warranty;
            $data["address"] = $data_frm["address"];
            $data["phone"] = $data_frm["phone"];
            $data["datetime"] = date('Y-m-d H:i:s');
            $data["creator"] = $this->_identity["username"];
            $data["enabled"] = 1;
            if((int)$this->_identity["userid"]>0){
                $_warranty_unit->insert($data);
            }
            $sproducers = $_warranty_unit->getList();
            $arr["id"]="ok";
            $arr["msg"]="ok";
            $arr["data"]=$sproducers;
            $ret[]=$arr;
            echo json_encode($ret);
        }
    }
    public function data_guarantee($data_frm){
        $_name  = $data_frm["itemid"];
        $name = explode("--", $_name);
        $data['id_box']       =   $data_frm["id_box"];
        $data['imei']       =   $data_frm["imei"];
        $data['seri']       =   $data_frm["seri"];
        if($data_frm["itemid"] != NULL){
            $data['itemid']     =   $name[2];
            $data['item_name']  =   $name[0]; 
             
        }
        $data['flag']  =   (int)$data_frm["flag"];
        $purchase_date = str_replace("/", "-", $data_frm["purchase_date"]) ;
        $data['purchase_date']       =   date('Y/m/d',  strtotime($purchase_date));
        
        $data['phone']       =   $data_frm["phone"];
        $data['note2']       =   $data_frm["note2"];
        $data["note"]       =   $data_frm["note"];
        $data["pid_color"]       =   (int)$data_frm["pid_color"];
        $data["damages"]       =   $data_frm["damages"];
        $data["status_receive"]       =   $data_frm["status_receive"];
        $data["acc_attach"]       =   $data_frm["acc_attach"];
        $data["receiver_id"]=   $data_frm["receiver_id"];
        if((int) $data_frm["receiver_id"] >0){
            $data["isdel"] = 1;
        }
        $data["receiver"]   =   $data_frm["receiver"];
        $data["money"]     = str_replace(",", "", $data_frm["money"]);
        $data["money_dvsc"]     = str_replace(",", "", $data_frm["money_dvsc"]);
        $data["money_hnam"]     = str_replace(",", "", $data_frm["money_hnam"]);
        return $data;
    }
    public function history_date($type,$id_guarantee){
        
        $date = date('Y-m-d H:i:s');
        $username = $this->_identity["username"];
        $data["id_guarantee"] = $id_guarantee;
        $data["datetime"] = $date;
        $data["creator"] = $username;
        $data["type"] = $type;
        Business_Addon_GuaranteeHistoryPk::getInstance()->insert($data);
    }

    public function  addAction(){
        if((int)  $this->_identity["userid"] ==0){
            echo "<script>$( document ).ready(function() {
            alert('Thời gian đăng nhập hết hiệu lực, vui lòng đăng nhập lại');
        });</script>";
            die('Thoi gian dang nhap het hieu luc.Vui long dang nhap lai');
        }
        $_option = Business_Addon_Options::getInstance();
        $sreceiver = Business_Common_Users::getInstance()->getListUser("",0,1);
        $this->view->sreceiver = $sreceiver;
        $slist = Business_Ws_ProductsItem::getInstance()->getProducts();
        $this->view->slist = $slist;
        $idregency = $this->_identity["idregency"];
        if($this->_request->isPost()){
            $data_frm = $this->_request->getParams('data_frm');
            $_name  = $data_frm["itemid"];
            $name = explode("--", $_name);
            $ret = $this->required();
            $data= $this->data_guarantee($data_frm);
            $price_tmp = str_replace(",", "", $data_frm["price_tmp"]);
            $data["price_tmp"] = (int)$price_tmp;
            
            $money_voucher = str_replace(",", "", $data_frm["money_voucher"]);
            $data["money_voucher"] = (int)$money_voucher;
            
            $advance_payment = str_replace(",", "", $data_frm["advance_payment"]);
            $data["advance_payment"] = (int)$advance_payment;
            if((int)$advance_payment >0){
                $data["advance_payment_date"] = date('Y-m-d H:i:s');
            }
            $voucher = trim($data_frm["voucher"]);
            $data["voucher"] = trim($data_frm["voucher"]);
            
            
            $data["ncc_ok"] = (int)$data_frm["ncc_ok"];
            $data['idcustomer'] =   $data_frm["idcustomer"];
            $data['status_guarantee']       =   $data_frm["status_guarantee"];
            $dealine = str_replace("/", "-", $data_frm["dealine"]) ;
            $data['dealine']       =   date('Y/m/d',  strtotime($dealine));
            $data["confirm"]    = 1;
            $data["status_where"]     = 1; // chi nhánh
            if($idregency ==48 || $_option->isBGD($idregency)){
                $data["status_where"]     = 3; // Kho bảo hành
            }
            if($data["receiver_id"] >0){
                $data["confirm"]    = 0;
                $data["status_where"]     = 2; // nhân viên
                $data["isdel"] = 1;
                
            }
            $data["confirm_name"]    = $this->_identity["username"];
            $data["status"]     = 1; 
            
            $data["complete"]     = 0; // chưa sửa
            $data["storeid"]     = $this->_identity["parentid"];
            $data["datetime"]   = date('Y-m-d H:i:s');
            $data["creator"]    = $this->_identity["username"];
            
            $data["storeid_in"]     = (int)$data_frm["storeid_in"];
            
            $data["enabled"]    = 1; 
            
            
            if(count($ret) >0){
                for($i=0;$i<count($ret);$i++){
                    $msg = $ret[$i]['msg'];
                    $ids = $ret[$i]['id'];
                    echo "<script>window.parent.show_msg('$msg','$ids');</script>";
                    return;
                }
            }else{
                if((int)$this->_identity["userid"]>0){
                    $last_id = Business_Addon_GuaranteePk::getInstance()->insert($data);
                    if($data["receiver_id"] >0){
                        $this->history_date($type=1,$last_id);// type = 1 ngày giao cho nhân viên
                    }
                }
                if($voucher != NULL){
                    $this->update_voucher($voucher);
                }
                $__token = md5($this->skey . $last_id);
                $__msg ='Lưu thành công';
                echo "<script>window.parent.completes('$__msg',$last_id,'$__token');</script>";
                return;
            }
        }
    }
    public function printAction(){
        $this->_helper->Layout()->disableLayout();
        $_option = Business_Addon_Options::getInstance();
        $id = $this->_request->getParam("id");
        $token = $this->_request->getParam("token");
        $ztoken = md5($this->skey . $id);
        if ($token != $ztoken) {
            die('No access');
        }
        $_guarantee = Business_Addon_GuaranteePk::getInstance();
        $detail = $_guarantee->getDetail($id);
        
        $_zwf_user = Business_Common_Users::getInstance();
        $list_store = $_zwf_user->getListByUname(FALSE);
        $storename = array();
        $address = array();
        foreach ($list_store as $items){
            $storename[$items["userid"]] = $items["storename"];
            $storename3[$items["userid"]] = $items["abbreviation"];
            $address[$items["userid"]] = $items["address"];
            $phone_store[$items["userid"]] = $items["phone"];
            
        }
        $this->view->address = $address[$this->_identity["parentid"]];
        $detail_mb = $_zwf_user->getDetail((int)$detail["receiver_id"]);
        $mb_name = $detail_mb["fullname"];
        $this->view->mb_name = $mb_name;
        $this->view->storename = $storename;
        $this->view->storename3 = $storename3;
        $this->view->storename2 = $storename[$this->_identity["parentid"]];
        $this->view->phone_store = $phone_store[$this->_identity["parentid"]];
        if((int)$this->_identity["parentid"] ==0){
            $this->view->storename2 = '';
            $this->view->address = '148 Nguyễn Cư Trinh,<br/>P. Nguyễn Cư Trinh, Q.1, TP.HCM';
        }
        $__customer = Business_Addon_Customer::getInstance();
        if($detail["idcustomer"] != NULL){
            $detail_customer = $__customer->getDetail((int)$detail["idcustomer"]);
        }
        $this->view->detail_customer = $detail_customer;
        
        $list_color = Business_Ws_NewsItem::getInstance()->getListByCateId(722);
        foreach ($list_color as $items) {
            $color_name[$items["itemid"]] = $items["title"];
        }
        $this->view->color_name = $color_name;
        
        $detail_user = Business_Common_Users::getInstance()->getDetailByUserName($detail["creator"]);
        $detail["fullname"] = $detail_user["fullname"];
        $this->view->detail = $detail;
    }
    public function detailAction(){
        $_option = Business_Addon_Options::getInstance();
        $__customer = Business_Addon_Customer::getInstance();
        $_zwf_user = Business_Common_Users::getInstance();
        $id = $this->_request->getParam("id");
        $token = $this->_request->getParam("token");
        $this->view->token =$token;
        $ztoken = md5($this->skey . $id);
        if ($token != $ztoken) {
            $this->_redirect('/admin/home');
        }
        $_guarantee = Business_Addon_GuaranteePk::getInstance();
        $detail = $_guarantee->getDetail($id);
        $this->view->detail = $detail;
        $__userid = (int)  $this->_identity["userid"];
        
        
        $idcustomer = $detail["idcustomer"];
        $detail_customer = $__customer->getDetail($idcustomer);
        $this->view->detail_customer = $detail_customer;
        
        $idregency = $this->_identity["idregency"];
        if($this->isStore($idregency)){
            $this->_helper->viewRenderer('detail-store');
            $__storeid = (int)  $this->_identity["parentid"];
//            $listmb = $_zwf_user->get_list_by_kh($__storeid,12);
//            foreach ($listmb as &$vl){
//                $vl["fullname"] = "Kỹ thuật ".$vl["fullname"];
//            }
        }
        if($_option->isBGD($idregency) || $this->isBHVT($idregency)){
            $this->_helper->viewRenderer('detail-admin');
        }
        if($idregency ==26){
//            if($detail["complete"] ==0 && $detail["confirm"]==0){
//                $this->_redirect('/admin/user/guarantee/list');
//            }
        }
        if($idregency ==26 && $detail["receiver_id2"] !=  $__userid){
            header("location:".$_SERVER['HTTP_REFERER']);
            exit();
        }
        if($idregency ==49){
            $this->_helper->viewRenderer('detail-view');
        }
        $list_store = $_zwf_user->getListByUname(FALSE);
        $this->view->list_store = $list_store;
        $storename = array();
        foreach ($list_store as $items){
            $storename[$items["userid"]] = $items["storename"];
        }
        $detail_mb = $_zwf_user->getDetail($detail["receiver_id"]);
        $mb_name = $detail_mb["fullname"];
        $this->view->mb_name = $mb_name;
        $this->view->storename = $storename;
        $list_status_guarantee = $_option->getBH();
        $this->view->status_guarantee = $list_status_guarantee[$detail["status_guarantee"]];
        
        $slist = Business_Addon_GuaranteeWarrantyPk::getInstance()->getListByGuarantee($id);
        
        $s_warranty1 = array();
        $s_warranty2 = array();
        foreach ($slist as $_item){
            if($_item["status"]==0){
                $s_warranty1[$_item["id_guarantee"]][] = $_item;
            }else{
               $s_warranty2[$_item["id_guarantee"]][] = $_item; 
            }
            $s_warranty[$_item["id_guarantee"]][] = $_item; 
        }
        $this->view->warranty = $s_warranty[$id];
        $this->view->warranty1 = $s_warranty1[$id];
        $this->view->warranty2 = $s_warranty2[$id][0];
        
        $detail_warranty = $s_warranty2[$id][0];
        $this->view->detail_warranty = $detail_warranty;
        
        
        $_warranty_unit = Business_Addon_WarrantyUnit::getInstance();
        $sproducers = $_warranty_unit->getList();
        $name_producers = array();
        foreach ($sproducers as $items){
            $name_producers[$items["id"]] = $items["name"]."-".$items["address"]."-".$items["phone"];
        }
        $this->view->sproducers = $sproducers;
        $this->view->name_producers = $name_producers;
        
        
        
        $sreceiver = Business_Common_Users::getInstance()->getListUser("",0,1,26);
        
        $receiver = array();
        foreach ($sreceiver as &$_items){
            $_items["fullname"] ="Luân chuyển ".$_items["fullname"];
        }
        if($listmb != NULL){
            $sreceiver = array_merge($sreceiver,$listmb);
        }
        foreach ($sreceiver as &$_items2){
            $receiver[$_items2["userid"]] = $_items2["fullname"];
        }
        $this->view->sreceiver = $sreceiver;
        $this->view->receiver = $receiver;
        
        $lcomplete = $_option->getCompleteKBH();
        $this->view->lcomplete = $lcomplete;
        $complete_color = array();
        $status_complete = array();
        foreach ($lcomplete as $val){
            $complete_color[$val["id"]] = $val["color"];
            $status_complete[$val["id"]] = $val["name"];
        }
        $this->view->complete_color = $complete_color;
        $this->view->status_complete = $status_complete;
        
        $show =0;
        if($detail_warranty ==null){
           $show =1; 
        }else{
            if($detail_warranty["status"] == 0){
                $show =1;
            }
        }
        $this->view->show = $show;
        $dt="4";
        $slist_products = Business_Ws_ProductsItem::getInstance()->getProducts2($dt);
        $this->view->slist_products = $slist_products;
        
        $list_regency = Business_Addon_Regency::getInstance()->getList();
        $this->view->list_regency = $list_regency;
        
        $list_department = Business_Addon_Department::getInstance()->getList();
        $department = array();
        foreach ($list_department as $item){
            $department[$item["id"]] = $item["name"];
        }
        $this->view->department = $department;
        $this->view->list_department = $list_department;
        
        
        $___color = Business_Addon_ProductsColor::getInstance();
        $color_name = array();
        $list_color = $___color->getListById($detail["itemid"]);
        $this->view->list_color = $list_color;
//        echo "<pre>";
//        var_dump($list_color,$detail["pid_color"]);
//        die();
        $scolor = Business_Ws_NewsItem::getInstance()->getListByCateId(722);
        foreach ($scolor as $items) {
            $color_name[$items["itemid"]] = $items["title"];
        }
        $this->view->color_name = $color_name;
        
    }
    public function listmoneyAction(){
        $this->view->skey = $this->skey;
        $list_collectingmoney = Business_Addon_Options::getInstance()->get_status_collectingmoney();
        $this->view->list_collectingmoney = $list_collectingmoney;
        
        $sreceiver = Business_Common_Users::getInstance()->getListUser("",0,1,26);
        $this->view->sreceiver = $sreceiver;
        $receiver = array();
        foreach ($sreceiver as $_items){
            $receiver[$_items["userid"]] = $_items["fullname"];
        }
        $this->view->receiver = $receiver;
        
        $_option = Business_Addon_Options::getInstance();
        $__bs = Business_Addon_TransferGuarantee::getInstance();
        $idregency = $this->_identity["idregency"];
        $storeid = (int)$this->_request->getParam("storeid");
        $receiverid = (int)$this->_request->getParam("receiverid");
        if($idregency ==26   || $idregency==12){ // nhân viên bảo hành
            $this->_helper->viewRenderer('listmoney2');
            $receiverid = $this->_identity["userid"];
            $list = $__bs->get_list_by_storeid_receiverid($storeid,$receiverid);
        }
        if($this->isStore($idregency)){ // cửa hàng
            $storeid = $this->_identity["parentid"];
            $list = $__bs->get_list_by_storeid_receiverid($storeid);
        }
        if($_option->isBGD($idregency) || $idregency ==48){ // KBH
            $this->_helper->viewRenderer('listmoney3');
            $list = $__bs->get_list_by_storeid_receiverid($storeid,$receiverid);
        }
        $this->view->list = $list;
        $list_status_money = $_option->get_status_money();
        $this->view->list_status_money = $list_status_money;
    }

    public function collectingmoneyAction(){
        $__bs = Business_Addon_GuaranteePk::getInstance();
        $list_collectingmoney = Business_Addon_Options::getInstance()->get_status_collectingmoney();
        $this->view->list_collectingmoney = $list_collectingmoney;
        
        $sreceiver = Business_Common_Users::getInstance()->getListUser("",0,1,26);
        $this->view->sreceiver = $sreceiver;
        $receiver = array();
        foreach ($sreceiver as $_items){
            $receiver[$_items["userid"]] = $_items["fullname"];
        }
        $this->view->receiver = $receiver;
        
        $idguarantee = (int)$this->_request->getParam("id");
        $this->view->idguarantee = $idguarantee;
        $token = $this->_request->getParam("token");
        $ztoken = md5($this->skey.$idguarantee);
        if($token != $ztoken){
            die('No access');
        }
        
        if($idguarantee >0){
            
            $detail = $__bs->getDetail($idguarantee);
            
            if($detail ==NULL){
                $this->_redirect('/admin');
            }
            $this->view->detail= $detail;
           
            $check_layout = Business_Addon_TransferGuarantee::getInstance()->get_detail_by_idguarantee($idguarantee);
            
            if($check_layout != NULL){
                $this->_helper->viewRenderer('collectingmoney2');
            }
            $this->view->check_layout = $check_layout;
            if($this->_request->isPost()){
                $data_frm = $this->_request->getParams('data_frm');
                $ret = array();
                $err = array();
                if((int)$data_frm["receiverid"]==0){
                    $err["id"] = "receiverid";
                    $err["msg"] = "Vui lòng chọn nhân viên";
                    $ret[] = $err;
                }
                if((int)$data_frm["money"]==0){
                    $err["id"] = "money";
                    $err["msg"] = "Vui lòng nhập số tiền";
                    $ret[] = $err;
                }
                if(count($ret) >0){
                    for($i=0;$i<count($ret);$i++){
                        $msg = $ret[$i]['msg'];
                        $ids = $ret[$i]['id'];
                        echo "<script>window.parent.show_msg('$msg','$ids');</script>";
                        die();
                    }
                }else{
                    
                    $data["money"] = str_replace(",", "", $data_frm["money"]);
                    $data["imei"] = $detail["imei"];
                    $data["receiverid"] = $data_frm["receiverid"];
                    $data["storeid"] = $this->_identity["parentid"];
                    $data["enabled"]= 1;
                    $data["status"]= 2;
                    $data["creator"] = $this->_identity["username"];
                    $data["datetime"] = date('Y-m-d H:i:s');
                    $data["idguarantee"] = $idguarantee;
                    if((int)$this->_identity["userid"]>0){
                        $last_id = Business_Addon_TransferGuarantee::getInstance()->insert($data);
                    }
                    $__msg ='Lưu thành công';
                    $url ='/admin/user/guarantee/list';
                    echo "<script>window.parent.show_success('$__msg',$last_id,'');</script>";
                    die();
                }
            }
        }
    }

    public function listAction(){
        if($_REQUEST['d']==10)
        {
            error_reporting(E_ALL);
            ini_set('display_errors', 1);

        }
        $_zwf_user = Business_Common_Users::getInstance();
        $list_store = $_zwf_user->getListByUname(FALSE);
        $__uid = (int)  $this->_identity["userid"];
        $detail_user = $_zwf_user->getDetail($__uid);
        $this->view->detail_user = $detail_user;
        $storename = array();
        foreach ($list_store as $__st){
            $storename[$__st["userid"]] = $__st["abbreviation"];
            
            $__str_storeid[] = $__st["userid"];
        }
        $str_storeid = implode(",", $__str_storeid);
        
        $this->view->storename = $storename;
        $this->view->list_store = $list_store;
        $_option = Business_Addon_Options::getInstance();
        $list_status_guarantee = $_option->getBH();
        
        $this->view->list_ok = $_option->get_ok();
        $ok = $this->_request->getParam("ok");
        if($ok ==NULL){
            $ok =-1;
        }
        $this->view->ok = $ok;
        $bhang = (int)$this->_request->getParam("bhang");
        $this->view->bhang = $bhang;
        
        $list_transfers = $_option->get_transfers();
        $this->view->list_transfers = $list_transfers;
        $transfers = $this->_request->getParam("transfers");
        $this->view->transfers = $transfers;
        $status_guarantee = $this->_request->getParam("status_guarantee");
        $this->view->status_guarantee = $status_guarantee;
        
        $complete = $this->_request->getParam("complete");
        if($complete == NULL){
            $complete = -1;
        }
        $this->view->complete = $complete;
        
        $this->view->list_status_guarantee = $list_status_guarantee;
        $lcomplete = $_option->getCompleteKBH();
        $this->view->lcomplete = $lcomplete;
        $complete_color = array();
        foreach ($lcomplete as $val){
            $complete_color[$val["id"]] = $val["color"];
        }
        $this->view->complete_color = $complete_color;
        
        $list_confirm = $_option->getConfirm();
        $name_confirm = array();
        $icon = array();
        foreach ($list_confirm as $cf){
            $name_confirm[$cf["id"]] = $cf["name"];
            $icon[$cf["id"]] = $cf["icon"];
            $icon2[$cf["id"]] = $cf["icon2"];
        }
        $this->view->icon = $icon;
        $this->view->icon2 = $icon2;
        $this->view->name_confirm = $name_confirm;
        $this->view->list_confirm = $list_confirm;
        
        $_guarantee = Business_Addon_GuaranteePk::getInstance();
        
        $d=date('Y-m-d');
        $k=$_option->pre_date($d, 1);
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
        
        $userid = $this->_identity["userid"];
        $this->view->uid = $userid;
        $creator = $this->_identity["username"];
        $this->view->creator = $creator;
        $storeid = $this->_identity["parentid"];
        $idregency = $this->_identity["idregency"];
        $status_where = 2;
        $bgd =0;
        
        if($_option->isBGD($idregency) || $this->isBHVT($idregency)){
            $status_where = 3;
            $userid = (int) $this->_request->getParam("receiver_id");
            $storeid = $this->_request->getParam("storeid");
            if($storeid ==NULL){
                $storeid =0;
            }
            if($storeid ==-1){
                $storeid =$str_storeid;
            }
            $bgd =1;
            $this->_helper->viewRenderer('list-admin');
        }
        
        $this->view->storeid = $storeid;
        $this->view->bgd = $bgd;
        
        if($this->isStore($idregency)){
            $status_where = 1;
            $userid = (int) $this->_request->getParam("receiver_id");
            $this->_helper->viewRenderer('list-store');
        }
        if($idregency ==12){
            $userid = (int) $this->_request->getParam("receiver_id");
        }
        
        $this->view->receiver_id = $userid;
        $storeid_in = (int)$this->_request->getParam('storeid_in');
        $this->view->storeid_in = $storeid_in;
        $this->view->status_where = $status_where;
        $enabled =1;
        if($_option->isBGD($idregency) || $this->isBHVT($idregency)){
           $enabled =''; 
        }
        $sync = (int)$this->_request->getParam("sync");
        $out = (int)$this->_request->getParam("out");
        $isxuat = (int)$this->_request->getParam("isxuat");
        if($isxuat ==1){
           $out =1; 
        }
        $this->view->isxuat = $isxuat;
        if($out ==1){
            $sdata = $_guarantee->get_list_by_date_out($start,$end,$storeid,$sync);
        }else{
            $sdata = $_guarantee->getListByUserId($userid,$start,$end,$storeid,$complete,$status_guarantee,$transfers,$enabled,$storeid_in,$ok,$bhang,$sync);
        }
        
        $this->view->date_outs = $__date_out;
        $data_out = array();
        $data_in = array();
        
        foreach ($sdata as $item){
            if($item["creator"] ==NULL){
                continue;
            }
            if($bgd==1){
                if($storeid ==0 && $item["storeid"] !=0){
                    continue;
                }
                
            }
            
            $id_guarantee[] = $item["id"];
            if($item["confirm"] ==1){
                foreach ($lcomplete as $val){
                    if($item["complete"] == $val["id"]){
                        $total[$item["complete"]]++;
                        $_totals++;
                    }
                }
            }else{
                $total_cxn[0]++;
            }
            if((int)$item["iddepartment"] > 0){
                $total_out++;
                $data_out[] = $item;
            }else{
                $data_in[] = $item;
            }
        }
       
        
        $this->view->total = $total;
        $this->view->total_cxn = $total_cxn;
        $this->view->total_out = $total_out;
        $this->view->total_inventory = $_totals + (int)$total_cxn[0] - $total_out;
        
        $str_guaranteeID = implode(",", $id_guarantee);
        if($str_guaranteeID != NULL){
            $slist = Business_Addon_GuaranteeWarrantyPk::getInstance()->getListByGuarantee($str_guaranteeID);
        }
        
        $s_warranty = array();
        foreach ($slist as $_item){
            $s_warranty[$_item["id_guarantee"]][] = $_item;
            $s_warranty_time[$_item["id_guarantee"]] = $_item["datetime"];
            $s_warranty_dealine[$_item["id_guarantee"]] = $_item["dealine"];
        }
        if($out ==0){
            $list = $data_in;
        }else{
            $list = $data_out;
        }
        
        foreach ($list as &$item){
            $item["s_warranty"] = $s_warranty[$item["id"]];
        }
        $this->view->list = $list;
        $this->view->skey = $this->skey;
        $this->view->s_warranty_time = $s_warranty_time;
        $this->view->s_warranty_dealine = $s_warranty_dealine;
        
        $_warranty_unit = Business_Addon_WarrantyUnit::getInstance();
        $sproducers = $_warranty_unit->getList();
        $name_producers = array();
        
        foreach ($sproducers as $items){
            $name_producers[$items["id"]] = $items["name"];
            $receipt[$items["id"]] = $items["receipt"];
        }
        $this->view->name_producers = $name_producers;
        
        $lstatus = $_option->getStatusKBH();
        $status_color = array();
        foreach ($lstatus as $val){
            $status_color[$val["id"]] = $val["color"];
        }
        $this->view->lstatus = $lstatus;
        $this->view->status_color = $status_color;
        if($storeid >0){
            $listmb = Business_Common_Users::getInstance()->get_list_by_kh($storeid,12);
            foreach ($listmb as &$vl){
                $vl["fullname"] = "Kt ".$vl["fullname"];
            }
        }
        
        $receiver = array();
        $sreceiver = Business_Common_Users::getInstance()->getListUser("",0,1,26);
        foreach ($sreceiver as &$_items){
            $_items["fullname"] ="lc ".$_items["fullname"];
        }
        
        if($listmb != NULL){
            $sreceiver = array_merge($sreceiver,$listmb);
        }
        
//        $sreceiver = Business_Common_Users::getInstance()->getListUser("",0,1,26);
        $this->view->sreceiver = $sreceiver;
//        $receiver = array();
        foreach ($sreceiver as $_items2){
            $receiver[$_items2["userid"]] = $_items2["fullname"];
        }
        $this->view->receiver = $receiver;
        
        
        $list_bhang = $_option->get_list_bhang();
        $this->view->list_bhang = $list_bhang;
        
        $this->view->idregency = $idregency;
        $this->view->username = $this->_identity["username"];
        $this->view->out = $out;
        
        $tstatus = (int)  $this->_request->getParam("tstatus");
        if($tstatus ==1){
            $this->_helper->viewRenderer('list-admin-1');
        }
        if($tstatus ==2){
            
            $this->_helper->viewRenderer('list-admin-2');
        }
        if($tstatus ==3){
            $this->_helper->viewRenderer('list-admin-3');
        }
        
        $this->view->tstatus = $tstatus;
    }
    public function statisticsAction(){
        $_zwf_user = Business_Common_Users::getInstance();
        $list_store = $_zwf_user->getListByUname(FALSE);
        $storename = array();
        foreach ($list_store as $__st){
            $storename[$__st["userid"]] = $__st["abbreviation"];
            
            $__str_storeid[] = $__st["userid"];
        }
        $str_storeid = implode(",", $__str_storeid);
        
        $this->view->storename = $storename;
        $this->view->list_store = $list_store;
        $_option = Business_Addon_Options::getInstance();
        $list_status_guarantee = $_option->getBH();
        
        $this->view->list_ok = $_option->get_ok();
        
        $bhang = (int)$this->_request->getParam("bhang");
        $this->view->bhang = $bhang;
        
        $list_transfers = $_option->get_transfers();
        $this->view->list_transfers = $list_transfers;
        $transfers = $this->_request->getParam("transfers");
        $this->view->transfers = $transfers;
        $status_guarantee = $this->_request->getParam("status_guarantee");
        $this->view->status_guarantee = $status_guarantee;
        
        $flag = (int)$this->_request->getParam("flag");
        $this->view->flag = $flag;
        $price_tmp = $this->_request->getParam("price_tmp");
        if($price_tmp==NULL){
            $price_tmp = -1;
        }
        $price_thu = $this->_request->getParam("price_thu");
        if($price_thu==NULL){
            $price_thu = -1;
        }
        $this->view->price_tmp = $price_tmp;
        $this->view->price_thu = $price_thu;
        $complete = $this->_request->getParam("complete");
        if($complete == NULL){
            $complete = -1;
        }
        $this->view->complete = $complete;
        
        $this->view->list_status_guarantee = $list_status_guarantee;
        $lcomplete = $_option->getCompleteKBH();
        $this->view->lcomplete = $lcomplete;
        $complete_color = array();
        foreach ($lcomplete as $val){
            $complete_color[$val["id"]] = $val["color"];
        }
        $this->view->complete_color = $complete_color;
        
        $list_confirm = $_option->getConfirm();
        $name_confirm = array();
        $icon = array();
        foreach ($list_confirm as $cf){
            $name_confirm[$cf["id"]] = $cf["name"];
            $icon[$cf["id"]] = $cf["icon"];
            $icon2[$cf["id"]] = $cf["icon2"];
        }
        $this->view->icon = $icon;
        $this->view->icon2 = $icon2;
        $this->view->name_confirm = $name_confirm;
        $this->view->list_confirm = $list_confirm;
        
        $_guarantee = Business_Addon_GuaranteePk::getInstance();
        
        $d=date('Y-m-d');
        $k=$_option->pre_date($d, 6);
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
        
        $userid = $this->_identity["userid"];
        $storeid = $this->_identity["parentid"];
        $idregency = $this->_identity["idregency"];
        $bgd =0;
        if($_option->isBGD($idregency) || $this->isBHVT($idregency)){
            $userid = (int) $this->_request->getParam("receiver_id");
            $storeid = $this->_request->getParam("storeid");
            if($storeid ==NULL){
                $storeid =0;
            }
            if($storeid ==-1){
                $storeid =$str_storeid;
            }
            $bgd =1;
        }
        
        $this->view->storeid = $storeid;
        $this->view->bgd = $bgd;
        
        $this->view->receiver_id = $userid;
        $storeid_in = (int)$this->_request->getParam('storeid_in');
        $this->view->storeid_in = $storeid_in;
        $enabled =1;
        if($_option->isBGD($idregency) || $this->isBHVT($idregency)){
           $enabled =''; 
        }
        $isxuat = (int)$this->_request->getParam("isxuat");
        $this->view->isxuat = $isxuat;
        if($isxuat ==1){
            $pdata = Business_Addon_GuaranteeHistory::getInstance()->get_list_by_type($start,$end,$__istype=4);
            $__date_out = array();
            foreach ($pdata as $__item){
                $__id_guarantee[] = $__item["id_guarantee"];
                $__date_out[$__item["id_guarantee"]] = $__item["datetime"];
            }
            if($__id_guarantee != NULL){
                $str_id_guarantee = implode(",", $__id_guarantee);
                $sdata = $_guarantee->get_list_by_id($str_id_guarantee);
            }
        }else{
            $sdata = $_guarantee->get_list_statistics($start,$end,$storeid,$flag,$price_tmp,$transfers,$enabled,$storeid_in,$ok=1,$bhang);
        }
        $this->view->date_outs = $__date_out;
        $data_out = array();
        $data_in = array();
        
        foreach ($sdata as $item){
            if($item["creator"] ==NULL){
                continue;
            }
            if($bgd==1){
                if($storeid ==0 && $item["storeid"] !=0){
                    continue;
                }
                
            }
            
            $id_guarantee[] = $item["id"];
            if($item["confirm"] ==1){
                foreach ($lcomplete as $val){
                    if($item["complete"] == $val["id"]){
                        $total[$item["complete"]]++;
                        $_totals++;
                    }
                }
            }else{
                $total_cxn[0]++;
            }
            if((int)$item["iddepartment"] > 0){
                $total_out++;
                $data_out[] = $item;
            }else{
                $data_in[] = $item;
            }
        }
       
        
        $this->view->total = $total;
        $this->view->total_cxn = $total_cxn;
        $this->view->total_out = $total_out;
        $this->view->total_inventory = $_totals + (int)$total_cxn[0] - $total_out;
        
        $str_guaranteeID = implode(",", $id_guarantee);
        $money_thu = array();
        if($str_guaranteeID != NULL){
            $slist = Business_Addon_GuaranteeWarrantyPk::getInstance()->getListByGuarantee($str_guaranteeID);
            
            $__guarantee_transfer = Business_Addon_TransferGuarantee::getInstance();
            $list_transfer = $__guarantee_transfer->get_list_by_idguarantee($str_guaranteeID);
            foreach ($list_transfer as $vl){
                $money_thu[$vl["idguarantee"]] = $vl["money"];
            }
        }
        $this->view->money_thu = $money_thu;
        
        $s_warranty = array();
        foreach ($slist as $_item){
            $s_warranty[$_item["id_guarantee"]][] = $_item;
        }
        if($isxuat ==0){
            $list = $data_in;
        }else{
            $list = $data_out;
        }
        
        foreach ($list as &$item){
            $item["s_warranty"] = $s_warranty[$item["id"]];
        }
        $this->view->list = $list;
        $this->view->skey = $this->skey;
        
        $_warranty_unit = Business_Addon_WarrantyUnit::getInstance();
        $sproducers = $_warranty_unit->getList();
        $name_producers = array();
        
        foreach ($sproducers as $items){
            $name_producers[$items["id"]] = $items["name"];
            $receipt[$items["id"]] = $items["receipt"];
        }
        $this->view->name_producers = $name_producers;
        
        $lstatus = $_option->getStatusKBH();
        $status_color = array();
        foreach ($lstatus as $val){
            $status_color[$val["id"]] = $val["color"];
        }
        $this->view->lstatus = $lstatus;
        $this->view->status_color = $status_color;
        
        
        $sreceiver = Business_Common_Users::getInstance()->getListUser("",0,1,26);
        $this->view->sreceiver = $sreceiver;
        $receiver = array();
        foreach ($sreceiver as $_items){
            $receiver[$_items["userid"]] = $_items["fullname"];
        }
        $this->view->receiver = $receiver;
        
        
        $list_bhang = $_option->get_list_bhang();
        $this->view->list_bhang = $list_bhang;
        
        $this->view->idregency = $idregency;
        $this->view->username = $this->_identity["username"];
        
        
        
    }
    
    public function delAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $id = (int)  $this->_request->getParam("id");
        $data["status"] =0;
        $_guarantee = Business_Addon_GuaranteePk::getInstance();
        $_guarantee->update_warranty($id, $data);
    }
    public function del2Action(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $id = (int)  $this->_request->getParam("id");
        $_guarantee = Business_Addon_GuaranteePk::getInstance();
        $idregency = $this->_identity["idregency"];
        if($id >0){
            $data["isdel"] = 1;
            $_guarantee->update($id, $data);
        }
        
    }
    public function activeAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $id = (int)  $this->_request->getParam("id");
        $_guarantee = Business_Addon_GuaranteePk::getInstance();
        $idregency = $this->_identity["idregency"];
        $detail = $_guarantee->getDetail($id);
        $token = $this->_request->getParam("token");
        $status_where = (int)  $this->_request->getParam("status_where");
        $ztoken = md5($this->skey.$id.$status_where);
        if($token != $ztoken){
            die("No access");
        }
        
        switch ($status_where){
            case 1: // chi nhánh
            {
                if((int)$detail["complete"] >1){
                    $data["confirm"] =1;
                }
                if((int)$detail["receiver_id3"] >0){
                    $data["confirm"] =1;
                }
                break;
            }
            case 2: // luân chuyển
            {
                if((int)$detail["complete"] ==0){
                    $data["complete"] =1;
                }
                if((int)$detail["complete"] < 2){
                    $data["confirm"] =1;
                }
                break;
            }
            case 3: // KBH
            {
                $data["confirm"] =1;
                $data["date_confirm"] =date('Y-m-d H:i:s');
                
                break;
            }
        }
//        // Gửi mail
//        $__zwf_user = Business_Common_Users::getInstance();
//        $_option = Business_Addon_Options::getInstance();
//        $detail_user = $__zwf_user->getDetailByUserName($detail["creator"]);
//        $to = $detail_user["email"];
//        if($to == NULL){
//            $to ="quynh.nguyen@hnammobile.com";
//        }
//        $subject ="Đã xác nhận";
//        $body_html ="Bạn đã xác nhận. Vui lòng xem lại link <a href='http://app.hnammobile.com/admin/user/guarantee/list'>Tại đây</a>";
//        $displayname = $this->_identity["fullname"];
//        $cc="";
//        $bcc=array("nghi.dang@hnammobile.com");
////                $bcc = array("nghi.dang@hnammobile.com","quynh.nguyen@hnammobile.com","duyhuy@hnammobile.com");
//        $_option->sendMail($to, $subject, $displayname, $body_html, $cc, $bcc);
            
        
        $data["status_where"]     = $status_where;
        $_guarantee->update($id, $data);
    }
    public function transfersAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $_guarantee = Business_Addon_GuaranteePk::getInstance();
        $token = $this->_request->getParam("token");
        $id = (int)  $this->_request->getParam("id");
        $transfers = (int)  $this->_request->getParam("transfers");
        $ztoken = md5($this->skey.$transfers);
        if($token != $ztoken){
            die("No access");
        }
        $idregency = $this->_identity["idregency"];
        if($this->isStore($idregency)){
           die("No access"); 
        }
        $data["transfers"]     = $transfers;
        
        $_guarantee->update($id, $data);
    }
    public function historyAction(){
        $this->_helper->Layout()->disableLayout();
        $id_guarantee = (int) $this->_request->getParam("id_guarantee");
        $list = Business_Addon_GuaranteeHistory::getInstance()->get_list_by_idguaranree($id_guarantee);
        $this->view->list = $list;
        $istype = Business_Addon_Options::getInstance()->status_history_guarantee();
        $this->view->istype = $istype;
    }

    public function updatesAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $_guarantee = Business_Addon_GuaranteePk::getInstance();
        $data_frm = $this->_request->getParams('data_frm');
        $_option = Business_Addon_Options::getInstance();
        $idregency = $this->_identity["idregency"];
        $ztoken = md5($this->skey . $data_frm["id"]);
        $detail = $_guarantee->getDetail($data_frm["id"]);
        
        $ret = array();
        $arr = array();
        if ($data_frm["token"] != $ztoken) {
            $err["id"] ="data_frm";
            $err["msg"] = "Bạn không có quyền truy cập";
            $ret[] = $err;
        }
        if($idregency ==26 || $idregency==12){
            if($detail["complete"] >1){
                $err["id"] ="data_frm";
                $err["msg"] = "Bạn đã hoàn tất nên không thể chỉnh sửa";
                $ret[] = $err;
            }
        }
        if(count($ret) >0){
            for($i=0;$i<count($ret);$i++){
                $msg = $ret[$i]['msg'];
                $ids = $ret[$i]['id'];
                echo "<script>window.parent.show_msg('$msg','$ids');</script>";
                return;
            }
        }else{
            $check_history =0;
            if($this->isStore($idregency)){ // quản lý
                $_data = $this->get_data_store($data_frm,$detail);
                $data = $_data["data"][0];
                $check_history = (int)$_data["check_history"][0];
            }
            if($idregency ==26 || $idregency==12){ // nhân viên
                $_data = $this->get_data($data_frm,$detail);
                $data = $_data["data"][0];
                $check_history = (int)$_data["check_history"][0];
            }
            if($_option->isBGD($idregency) || $this->isBHVT($idregency)){ // trưởng KBH
                $_data1 = $this->get_data($data_frm,$detail);
                $_guarantee->update($data_frm["id"], $_data1["data"][0]); 
                $_data = $this->get_data_admin($data_frm,$detail);
                
                $data = $_data["data"][0];
                $check_history = (int)$_data["check_history"][0];
            }
            
            $_guarantee->update($data_frm["id"], $data); 
            if($check_history ==1){
                if((int)$this->_identity["userid"]>0){
                    $_guarantee->insert_history($detail); 
                }
            }
            
            if($_option->isBGD($idregency) || $this->isBHVT($idregency)){
                $_sdata = $this->data_guarantee($data_frm);
                Business_Addon_GuaranteePk::getInstance()->update($data_frm["id"],$_sdata);
            }
            echo "<script>window.parent.completes('LƯU THÀNH CÔNG');</script>";
            return;
        }
    }
    public function get_data_admin($data_frm,$detail){
        $idregency = $this->_identity["idregency"];
        $_option = Business_Addon_Options::getInstance();
        $__zwf_user = Business_Common_Users::getInstance();
        $data["receiver_id3"]=   (int)$data_frm["receiver_id3"];
        if((int)$detail["receiver_id3"]==0){
            if((int)$data_frm["receiver_id3"] >0){
                $data["status_where"] = 1;
                $data["confirm"] = 0;
            }
        }else{
            if((int)$data_frm["receiver_id3"] != 0 && (int)$detail["receiver_id3"] != (int)$data_frm["receiver_id3"]){
                $data["status_where"] = 1;
                $data["confirm"] = 0;
            }
        }
        
        
        
        if((int)$detail["receiver_id3"]==0){
            if((int)$data_frm["receiver_id2"] >0){
                $data["receiver_id2"]=   $data_frm["receiver_id2"];
                
//                $data["confirm"] = 0;
//                $data["status_where"] = 2;
                $data["isdel"] = 1;
                $this->history_date($__type=1, $id_guarantee=$detail["id"]); // Ngày giao cho nhân viên
                
//                $detail_user = $__zwf_user->getDetail($data_frm["receiver_id3"]);
//                $to = $detail_user["email"];
//                if($to == NULL){
//                    $to ="quynh.nguyen@hnammobile.com";
//                }
//                $subject ="Đồng ý xác nhận là đã nhận sản phẩm";
//                $body_html ="Vui lòng nhấn xác nhận vào link <a href='http://app.hnammobile.com/admin/user/guarantee/list'>Tại đây</a>";
//                $displayname = $this->_identity["fullname"];
//                $cc="";
//                $bcc=array("nghi.dang@hnammobile.com");
////                $bcc = array("nghi.dang@hnammobile.com","quynh.nguyen@hnammobile.com","duyhuy@hnammobile.com");
//                $_option->sendMail($to, $subject, $displayname, $body_html, $cc, $bcc);
            }
        }else{
            if((int)$data_frm["receiver_id3"] != 0 && (int)$detail["receiver_id3"] != (int)$data_frm["receiver_id3"]){
                $data["receiver_id2"]=   $data_frm["receiver_id2"];
                $data["confirm"] = 0;
                $data["status_where"] = 1;
                $data["complete"] = 1;
                
//                $detail_user = $__zwf_user->getDetail($data_frm["receiver_id3"]);
//                $to = $detail_user["email"];
//                if($to == NULL){
//                    $to ="quynh.nguyen@hnammobile.com";
//                }
//                $subject ="Đồng ý xác nhận là đã nhận sản phẩm";
//                $body_html ="Vui lòng nhấn xác nhận vào link <a href='http://app.hnammobile.com/admin/user/guarantee/list'>Tại đây</a>";
//                $displayname = $this->_identity["fullname"];
//                $cc="";
//                $bcc=array("nghi.dang@hnammobile.com");;
////                $bcc = array("nghi.dang@hnammobile.com","quynh.nguyen@hnammobile.com","duyhuy@hnammobile.com");
//                $_option->sendMail($to, $subject, $displayname, $body_html, $cc, $bcc);
            }
        }
        $data["ncc_ok"] = (int)$data_frm["ncc_ok"];
        $data["storeid_in"] = $data_frm["storeid_in"];
        $ck=0;
        if($this->isStore($idregency)){
            if($detail["money"]==0){
                $data["money"] = str_replace(",", "", $data_frm["money"]);
            }
            if($detail["money_dvsc"]==0){
                $data["money_dvsc"] = str_replace(",", "", $data_frm["money_dvsc"]);
            }
            if($detail["money_hnam"]==0){
                $data["money_hnam"] = str_replace(",", "", $data_frm["money_hnam"]);
            }
        }else{
            $data["money"] = str_replace(",", "", $data_frm["money"]);
            $data["money_dvsc"] = str_replace(",", "", $data_frm["money_dvsc"]);
            $data["money_hnam"] = str_replace(",", "", $data_frm["money_hnam"]);
        }
        $price_tmp = str_replace(",", "", $data_frm["price_tmp"]);
        $data["price_tmp"] = (int)$price_tmp;
        $advance_payment = str_replace(",", "", $data_frm["advance_payment"]);
        $data["advance_payment"] = (int)$advance_payment;
        if((int)$detail["advance_payment"] ==0 && (int)$advance_payment >0){
            $data["advance_payment_date"] = date('Y-m-d H:i:s');
        }
        
        
        
        $check_history = 0;
        if($detail["money"] != (int)$data_frm["money"]){
            $check_history =1;
        }
        if($detail["money_dvsc"] != (int)$data_frm["money_dvsc"]){
            $check_history =1;
        }
        if($detail["money_hnam"] != (int)$data_frm["money_hnam"]){
            $check_history =1;
        }
        if($detail["note"] != $data_frm["note"]){
            $check_history =1;
        }
        $data["note"] = $data_frm["note"];
        $data["editor"] = $this->_identity["username"];
        $data["datetime_editor"] = date('Y-m-d H:i:s');
        
        $money_voucher = str_replace(",", "", $data_frm["money_voucher"]);
        $data["money_voucher"] = (int)$money_voucher;
        $voucher = trim($data_frm["data_voucher"]);
        $data["voucher"] = $data_frm["data_voucher"];
        if($voucher != NULL){
            $this->update_voucher($voucher);
        }
        
        if($detail["confirm"] ==1 && $detail["complete"] >1){
            if((int)$data_frm["iddepartment"]>0){
                $data["iddepartment"] = $data_frm["iddepartment"];
            }
            if((int)$data_frm["storeid_out"]>0){
                if((int)$data_frm["iddepartment"] ==10){ // phòng ban chi nhánh
                  $data["storeid_out"] = $data_frm["storeid_out"];  
                }
            }
            if((int)$data_frm["iddepartment"]>0 || (int)$data_frm["storeid_out"]>0){
                $this->history_date($__type=4, $id_guarantee=$detail["id"]); // Xuất
                $data["date_out"] = date('Y-m-d H:i:s');
            }
            
        }
       
        $sdata["data"][] = $data;
        $sdata["check_history"] = $check_history;
        return $sdata;
    }
    public function get_data_store($data_frm,$detail){
        $idregency = $this->_identity["idregency"];
        $_option = Business_Addon_Options::getInstance();
        $__zwf_user = Business_Common_Users::getInstance();
        if((int)$detail["receiver_id"]==0){
            if((int)$data_frm["receiver_id"] >0){
                $data["receiver_id"]=   $data_frm["receiver_id"];
                $data["receiver"]   =   $data_frm["receiver"];
                $data["confirm"] = 0;
                $data["status_where"] = 2;
                $data["isdel"] =1;
                $this->history_date($__type=1, $id_guarantee=$detail["id"]); // Ngày giao cho nhân viên
                
//                $detail_user = $__zwf_user->getDetail($data_frm["receiver_id"]);
//                $to = $detail_user["email"];
//                if($to == NULL){
//                    $to ="quynh.nguyen@hnammobile.com";
//                }
//                $subject ="Đồng ý xác nhận là đã nhận sản phẩm";
//                $body_html ="Vui lòng nhấn xác nhận vào link <a href='http://app.hnammobile.com/admin/user/guarantee/list'>Tại đây</a>";
//                $displayname = $this->_identity["fullname"];
//                $cc="";
//                $bcc=array("nghi.dang@hnammobile.com");;
////                $bcc = array("nghi.dang@hnammobile.com","quynh.nguyen@hnammobile.com","duyhuy@hnammobile.com");
//                $_option->sendMail($to, $subject, $displayname, $body_html, $cc, $bcc);
            }
        }
        $ck=0;
        if($this->isStore($idregency)){
            if($detail["money"]==0){
                $data["money"] = str_replace(",", "", $data_frm["money"]);
            }
            if($detail["money_dvsc"]==0){
                $data["money_dvsc"] = str_replace(",", "", $data_frm["money_dvsc"]);
            }
            if($detail["money_hnam"]==0){
                $data["money_hnam"] = str_replace(",", "", $data_frm["money_hnam"]);
            }
        }else{
            $data["money"] = str_replace(",", "", $data_frm["money"]);
            $data["money_dvsc"] = str_replace(",", "", $data_frm["money_dvsc"]);
            $data["money_hnam"] = str_replace(",", "", $data_frm["money_hnam"]);
        }
        
        
        $check_history = 0;
        if($detail["money"] != (int)$data_frm["money"]){
            $check_history =1;
        }
        if($detail["money_dvsc"] != (int)$data_frm["money_dvsc"]){
            $check_history =1;
        }
        if($detail["money_hnam"] != (int)$data_frm["money_hnam"]){
            $check_history =1;
        }
        if($detail["note"] != $data_frm["note"]){
            $check_history =1;
        }
        $data["note"] = $data_frm["note"];
        $data["editor"] = $this->_identity["username"];
        $data["datetime_editor"] = date('Y-m-d H:i:s');
        
        
        if($detail["complete"] >1){
            if((int)$data_frm["iddepartment"]>0){
                $data["iddepartment"] = $data_frm["iddepartment"];
            }
            if((int)$data_frm["storeid_out"]>0){
                if((int)$data_frm["iddepartment"] ==10){ // phòng ban chi nhánh
                  $data["storeid_out"] = $data_frm["storeid_out"];  
                }
            }
            if((int)$data_frm["iddepartment"]>0 || (int)$data_frm["storeid_out"]>0){
                $this->history_date($__type=4, $id_guarantee=$detail["id"]); // Xuất
                $data["date_out"] = date('Y-m-d H:i:s');
            }
        }
        $sdata["data"][] = $data;
        $sdata["check_history"][] = $check_history;
        return $sdata;
    }
    public function get_data($data_frm,$detail){
        $check_history = 0;
        $__zwf_user = Business_Common_Users::getInstance();
        $_option = Business_Addon_Options::getInstance();
        $_guarantee = Business_Addon_GuaranteePk::getInstance();
        $detail_warranty = $_guarantee->getDetailByWarranty($data_frm["id"]);
        $data_warranty["note"] = $data_frm["note_warranty"];
        $data_warranty["receipt"] = $data_frm["receipt"];
//        $data_warranty["dealine"] = date('Y-m-d',  strtotime($data_frm["dealine"]));
        
        $dealine = str_replace("/", "-", $data_frm["dealine"]) ;
        $__dealine       =   date('Y-m-d',  strtotime($dealine));
        
        $data_warranty["dealine"] = $__dealine;
        if($data_frm["producers"] >0){
            $data_warranty["id_guarantee"] = $data_frm["id"];
            $data_warranty["id_warranty"] = $data_frm["producers"];
            $data_warranty["creator"] = $this->_identity["username"];
            $data_warranty["status"] = 1;
            $data_warranty["enabled"] = 1;
            $data_warranty["complete"] = $data_frm["complete"];
            $data_warranty["datetime"] = date('Y-m-d H:i:s');
            $this->history_date($__type=2, $id_guarantee=$detail["id"]); // Nhân viên lên hãng
            if((int)$this->_identity["userid"]>0){
                $_guarantee->insert_warranty($data_warranty);
            }
        }else{
            if(strtotime($__dealine)>0){
                $data_warranty["complete"] = $data_frm["complete"];
                $data_warranty["end_creator"] = $this->_identity["username"];
                $data_warranty["end_datetime"] = date('Y-m-d H:i:s');
                $this->history_date($__type=2, $id_guarantee=$detail["id"]); // Nhân viên lên hãng
                Business_Addon_GuaranteeWarrantyPk::getInstance()->update($detail_warranty["id"], $data_warranty);
            }
        }

    if($detail["complete"] != $data_frm["complete"]){
           $check_history =1;
        }
        $data["complete"] = $data_frm["complete"];
        $data["confirm_name"]    = $this->_identity["username"];
        $data["confirm"]    = 1;
        if($data_frm["complete"] > 1){
           if((int)$detail["complete"] <2){
                $data["confirm"]    = 0; 
                $data["status_where"] = 3; // trả về kho bảo hành xác nhận
                $this->history_date($__type=3, $id_guarantee=$detail["id"]);
                // Gửi mail
//                 $detail_user = $__zwf_user->getDetailByUserName($detail["creator"]);
//                 $to = $detail_user["email"];
//                 if($to == NULL){
//                     $to ="quynh.nguyen@hnammobile.com";
//                 }
//                 $subject ="Đồng ý xác nhận hoàn tất quá trình sửa chửa và giao lại sản phẩm";
//                 $body_html ="Vui lòng nhấn xác nhận vào link <a href='http://app.hnammobile.com/admin/user/guarantee/list'>Tại đây</a>";
//                 $displayname = $this->_identity["fullname"];
//                 $cc="";
//                 $bcc=array("nghi.dang@hnammobile.com");;
//     //                $bcc = array("nghi.dang@hnammobile.com","quynh.nguyen@hnammobile.com","duyhuy@hnammobile.com");
//
//                 $_option->sendMail($to, $subject, $displayname, $body_html, $cc, $bcc);
            }
        }
        if($detail["note"] != $data_frm["note"]){
            $check_history =1;
        }
        if($data_frm["note"] != NULL){
            $data["note"] = $data_frm["note"];
        }
        
        $data["editor"] = $this->_identity["username"];
        $data["datetime_editor"] = date('Y-m-d H:i:s');
        $sdata["data"][] = $data;
        $sdata["check_history"] = $check_history;
        return $sdata;
    }
    public function isBHVT($idregency){
        if($idregency ==48){
            return true;
        }
        $username = $this->_identity["username"];
        if($username =="hnam_quynhn"){
            return true;
        }
        if($username =="hnam_vinhpt"){
            return true;
        }
        return FALSE;
    }
    public function isStore($idregency){
        if($idregency ==11 || $idregency ==14){
            return true;
        }
        return FALSE;
    }

}
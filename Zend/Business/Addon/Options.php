<?php

class Business_Addon_Options extends Business_Abstract {
    private $_identity;
    private static $_instance = null;
    private $_secKey   = "Hnammobile@2015";
            function __construct() {
             $auth = Zend_Auth::getInstance(); 
        $identity = (array)$auth->getIdentity();
        $this->_identity = $identity;   
        
    }
    

    //public static function
    /**
     * get instance of Business_Addon_Options
     *
     * @return Business_Addon_Options
     */
    public static function getInstance() {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Get DB Connection
     *
     * @return Zend_Db_Adapter_Abstract
     */
    
    private function getDbConnection() {
        $db = Globals::getDbConnection('maindb');
        return $db;
    }
    public function renderExcel($header,$content){
        
    }
    public function flush_cache($q=1,$currentUrl){
//            $cache = GlobalCache::getCacheInstance();
//            $cache->flushAll();
        }
        
    //nghidv - reset toàn bộ voucher, trả góp, cà thẻ, chuyển khoản, công nợ của phiếu bán hàng
    public function resetBill($users_product) {
		$fields = array('money_voucher','id_voucher','money_installment','cated_prepaid_installment','contract','money_installment_lech','id_installment','prepaid','free_prepaid','namecard','cated_card','money_transfer','transfer');
		foreach($fields as $col) {
			if (isset($users_product[$col])) {
				$users_product[$col]=0;
			}
		}
		return $users_product;
	}
	
	public function get_makh_suachua($ten_kh,$ma_vt, $ma_bp) {
		$_ma_kh = '';
		if (stripos($ma_vt,"sc.")!==false || strpos($ma_vt,"DV.CTYSUACHUAXXX")!==false) {
			//nhóm linh kiện sửa chữa			
			$_ma_kh = $this->tenkh_to_makh($ten_kh);
			if ($_ma_kh=='') {
				$_ma_kh = 'KL.SCA.'.$ma_bp;
			}
			
		}
		return $_ma_kh;
	}
	
	public function tenkh_to_makh($ten_kh) {
		
		//$arr = array('THN','QVT','VVT','AHY');
		if (stripos($ten_kh,"qvt")!==false || stripos($ten_kh,"vvt")!==false) {
			return 'CT.VTX.XXXXX';
		}
		if (stripos($ten_kh,"thn")!==false) {			
			return 'KL.SCA.ATHIN';
		}
		if (stripos($ten_kh,"ahy")!==false) {
			return 'KL.SCA.BAHUY';
		}
		
		return '';		
	}
	
    public function randomCode($char = '', $lengCode = 0, $numCode = 0)
    {
        $result = '';
        $size = strlen($char);
        for ($i = 0; $i < $numCode; $i ++) {
            $code = '';
            for ($j = 0; $j < $lengCode; $j ++) {
                $code .= $char[rand(0, $size - 1)];
            }
            $result .= $code . ' ';
        }
        $results = substr($result, 0, - 1);
        return $results;
    }
    public function check_imei_vt($imei){
        if(strlen($imei)>16){
            $err["id"] ="imei";
            $err["msg"] ="Imei tối da chỉ có 16 ký tự. Vui lòng kiểm tra lại";
            $ret[] = $err;
        } else {
            $ret = array();
            $__bs = Business_Addon_OrderDetailVt::getInstance(); 
            $detail = $__bs->get_detail_by_imei($imei);
            if($detail==NULL){
                $err["id"] ="imei";
                $err["msg"] ="Imei này không có trong hệ thống. Vui lòng kiểm tra lại";
                $ret[] = $err;
            }else{
                if($detail["status_import"]==0){
                    $err["id"] ="imei";
                    $err["msg"] ="Imei này chưa nhập kho. Vui lòng kiểm tra lại";
                    $ret[] = $err;
                }else{
                    $detail_item = Business_Addon_ProductsItem::getInstance()->getDetail($detail["itemid"]);
                    $title = $detail_item["title"];
                    if((int)$detail_item["price"]<=0){
                        $err["id"] ="imei";
                        $err["msg"] ="Sản phẩm $title có giá =0 nên không thể xuất bán. Vui lòng kiểm tra lại";
                        $ret[] = $err;
                    }else{
                        if($detail_item["ma_vt"] == Null){
                            $err["id"] ="imei";
                            $err["msg"] ="Sản phẩm $title không có mã vật tư nên không thể xuất bán. Vui lòng kiểm tra lại";
                            $ret[] = $err;
                        }
                    } 
                    
                    $detail_hnam = Business_Addon_ProductsColor::getInstance()->get_detail_by_id_vt($detail["itemid"]);
                    if($detail_hnam==NULL){
                        $err["id"] ="imei";
                        $err["msg"] ="Sản phẩm này chưa được mapping với hệ thống Hnam. Vui lòng liên hệ IT";
                        $ret[] = $err;
                    }else{
                        if($detail_hnam["code"]==NULL){
                            $err["id"] ="imei";
                            $err["msg"] ="Sản phẩm này bên hệ thống Hnam chưa có mã vật tư. Vui lòng liên hệ IT";
                            $ret[] = $err;
                        }
                    }
                    $storeid = (int)$this->_identity["parentid"];
                   
                    $list_store = Business_Addon_MappingStoreVt::getInstance()->get_list();
                    foreach ($list_store as $store){
                        $name_store[$store["storeid"]] = $store["ma_bp"]; //id_fast_bp
                    }
                    if($detail["storeid"] != $storeid){
                        $store1 = $name_store[$detail["storeid"]];
                        $err["id"] ="imei";
                        $err["msg"] ="Sản phẩm đang ở chi nhánh $store1 nên không thể xuất bán. Vui lòng kiểm tra lại";
                        $ret[] = $err;
                    }
                    if((int)$detail["status_move"] >0){
                        $store2 = $name_store[$detail["status_move"]];
                        $err["id"] ="imei";
                        $err["msg"] ="Sản phẩm đang luân chuyển chi nhánh $store2 nên không thể xuất bán. Vui lòng kiểm tra lại";
                        $ret[] = $err;
                    }
                }
            }
        }
        return $ret;
    }
    public function sync_imei($imei,$token){
        $skey = "SYNCIMEIFAST";
        $ztoken = md5($skey.$imei);
        $idregency = $this->_identity["idregency"];
        if(!$this->isBGD($idregency)){
           if($token != $ztoken){
                $err['id']      = "imei";
                $err['msg']     = "Đã có lỗi xảy ra. Vui lòng thử lại sau";
                $ret[]          = $err;
                return $ret;
                die();
            } 
        }
        $__config = Globals::getConfig('fastsync')->db->conn;
        $config = explode(";", $__config);
        $host = $config[0];
        $user = $config[1];
        $pass = $config[2];
                
        $conn = mssql_connect($host, $user, $pass);
		$_query = "HNAM_NB_A.dbo.zc_genStockToDTG_imeis '$imei';";
        $proc = mssql_init($_query, $conn);
		
        $proc_result = mssql_execute($proc);
        mssql_free_statement($proc);
        unset($proc);
        
        $err['id']      = "ok";
        $err['msg']     = "ok";
        $ret[]          = $err;
        return $ret;
    }

    public function delete_bill_api($id_addon_user,$itype,$token){
        $result ='';
        if($itype ==0){
            $itype =1;
        }
        $skey = "DELETEBILLFAST";
        $ztoken = md5($skey.$itype.$id_addon_user);
        $idregency = $this->_identity["idregency"];
/*         if(!$this->isBGD($idregency)){
           if($token != $ztoken){
                die('no access');
            } 
        } */
        
        if($id_addon_user >0){
            if($itype ==1){
                    $detail = Business_Addon_UsersProducts::getInstance()->get_detail_by_idaddonuser($id_addon_user);
                    $date = date('Ymd',  strtotime($detail["create_date"]));
                    
//                    $list_bill = Business_Addon_UsersProducts::getInstance()->getListByBillIdActived($id_addon_user,1);
//                    foreach ($list_bill as $_data){
//                        if($_data["vote_id"]==167){
//                            $itemid = $_data["products_id"];
//                            $phone = $_data["phone_addon"];
//                            $link_accesstrade = "https://www.hnammobile.com/api/update-accesstrade?itemid=$itemid&phone=$phone&billid=$id_addon_user&status=2";
//                            Business_Common_Utils::getContentByCurl($link_accesstrade);
//                        }
//                    }
                    
            }else{
                $detail = Business_Addon_Purchase::getInstance()->get_detail($id_addon_user);
                $date = date('Ymd',  strtotime($detail["datetime"]));
                $__bs_vt = Business_Addon_OrderDetailVt::getInstance();
                if((int)$detail["order_detail_vt"]>0){
                    $query ="update quynhn_order_detail set isale=0 where id = ".$detail["order_detail_vt"];
                    $__bs_vt->excute($query);
                }
                if((int)$detail["billid"]>0){
                    $billid = $detail["billid"];
                    $datetime_end = date('Y-m-d H:i:s');
                    //$userid_end = $this->_identity["userid"];
                    $userid_end=1; //hardcode ID tool
                    $__query ="update quynhn_bill set enabled=0,userid_end=$userid_end,datetime_end='$datetime_end' where id IN ($billid) ";
                    $__bs_vt->excute($__query);

                    $__query2 ="update quynhn_sell set enabled=0,userid_end=$userid_end,datetime_end='$datetime_end' where billid IN ($billid) ";
                    $__bs_vt->excute($__query2);
                    
                    $__query3 ="update quynhn_pos set enabled=0 where billid IN ($billid) "; 
                    $__bs_vt->excute($__query3);
                }
                
            }
            if($detail){
                // trả nợ
                if($itype ==1){
                    $list_promotion = Business_Addon_AddonPromotion::getInstance()->get_list_by_billid_trano($id_addon_user);
                }
                
                $__config = Globals::getConfig('fast')->db->conn;
                $config = explode(";", $__config);
                $host = $config[0];
                $user = $config[1];
                $pass = $config[2];
                
                $conn = mssql_connect($host, $user, $pass);
                //mssql_select_db($table, $conn);

                // Call a simple query
                //$result = mssql_query('SELECT TOP 100 * FROM tonkhoimei', $conn);

                // Release the result resource
                //mssql_free_result($result);

                // Then execute the procedure
                //xoa bill xuat ban hang
                
                $proc = mssql_init("HNAM_NB_A.dbo.zc_DeleteTranfer $itype, '$id_addon_user', '$date'", $conn);
                $proc_result = mssql_execute($proc);
                // Etc...
                mssql_free_statement($proc);
                unset($proc);
                // bill trả nợ
                if($itype ==1){
                    if($list_promotion){
                        foreach ($list_promotion as $pr){
                            if((int)$pr["bill_no"]>0){
                                $id_addon_user_no = $pr["bill_no"];
                                $query_update_bill_no ="update users_products set is_actived =-1 where id_addon_user = ".$id_addon_user_no;
                                Business_Addon_UsersProducts::getInstance()->excute($query_update_bill_no);
                                
                                Business_Addon_FASThdbanhang::getInstance()->delete_dbtg($id_addon_user_no);
                                $date_no = date('Ymd',  strtotime($pr["update_ispay"]));
                                $proc2 = mssql_init("HNAM_NB_A.dbo.zc_DeleteTranfer 1, '$id_addon_user_no', '$date_no'", $conn);
                                echo $id_addon_user_no,
                                $proc_result2 = mssql_execute($proc2);
                                // Etc...
                                mssql_free_statement($proc2);
                                unset($proc2);
                                
                                
                            }
                        }
                        
                    }
                }
                $result = 'delete complete'.$id_addon_user.'-'.date('d/m/Y H:i:s');
                
                
                if($itype ==1){
                    Business_Addon_FASThdbanhang::getInstance()->delete_dbtg($id_addon_user);
                }else{
                    Business_Addon_FASThdmuahang::getInstance()->delete_dbtg($id_addon_user);
                }
                
                
            }
        }else{
            $result = 'delete fail'.$id_addon_user.'-'.date('d/m/Y H:i:s');
        }
        return $result;
    }



    public function delete_bill($id_addon_user,$itype,$token){
        $result ='';
        if($itype ==0){
            $itype =1;
        }
        $skey = "DELETEBILLFAST";
        $ztoken = md5($skey.$itype.$id_addon_user);
        $idregency = $this->_identity["idregency"];
        if(!$this->isBGD($idregency)){
           if($token != $ztoken){
                die('no access');
            } 
        }
        
        if($id_addon_user >0){
            if($itype ==1){
                    $detail = Business_Addon_UsersProducts::getInstance()->get_detail_by_idaddonuser($id_addon_user);
                    $date = date('Ymd',  strtotime($detail["create_date"]));
                    
//                    $list_bill = Business_Addon_UsersProducts::getInstance()->getListByBillIdActived($id_addon_user,1);
//                    foreach ($list_bill as $_data){
//                        if($_data["vote_id"]==167){
//                            $itemid = $_data["products_id"];
//                            $phone = $_data["phone_addon"];
//                            $link_accesstrade = "https://www.hnammobile.com/api/update-accesstrade?itemid=$itemid&phone=$phone&billid=$id_addon_user&status=2";
//                            Business_Common_Utils::getContentByCurl($link_accesstrade);
//                        }
//                    }
                    
            }else{
                $detail = Business_Addon_Purchase::getInstance()->get_detail($id_addon_user);
                $date = date('Ymd',  strtotime($detail["datetime"]));
                $__bs_vt = Business_Addon_OrderDetailVt::getInstance();
                if((int)$detail["order_detail_vt"]>0){
                    $query ="update quynhn_order_detail set isale=0 where id = ".$detail["order_detail_vt"];
                    $__bs_vt->excute($query);
                }
                if((int)$detail["billid"]>0){
                    $billid = $detail["billid"];
                    $datetime_end = date('Y-m-d H:i:s');
                    $userid_end = $this->_identity["userid"];
                    $__query ="update quynhn_bill set enabled=0,userid_end=$userid_end,datetime_end='$datetime_end' where id IN ($billid) ";
                    $__bs_vt->excute($__query);

                    $__query2 ="update quynhn_sell set enabled=0,userid_end=$userid_end,datetime_end='$datetime_end' where billid IN ($billid) ";
                    $__bs_vt->excute($__query2);
                    
                    $__query3 ="update quynhn_pos set enabled=0 where billid IN ($billid) "; 
                    $__bs_vt->excute($__query3);
                }
                
            }
            if($detail){
                // trả nợ
                if($itype ==1){
                    $list_promotion = Business_Addon_AddonPromotion::getInstance()->get_list_by_billid_trano($id_addon_user);
                }
                
                $__config = Globals::getConfig('fast')->db->conn;
                $config = explode(";", $__config);
                $host = $config[0];
                $user = $config[1];
                $pass = $config[2];
                
                $conn = mssql_connect($host, $user, $pass);
                //mssql_select_db($table, $conn);

                // Call a simple query
                //$result = mssql_query('SELECT TOP 100 * FROM tonkhoimei', $conn);

                // Release the result resource
                //mssql_free_result($result);

                // Then execute the procedure
                //xoa bill xuat ban hang
                
                $proc = mssql_init("HNAM_NB_A.dbo.zc_DeleteTranfer $itype, '$id_addon_user', '$date'", $conn);
                $proc_result = mssql_execute($proc);
                // Etc...
                mssql_free_statement($proc);
                unset($proc);
                // bill trả nợ
                if($itype ==1){
                    if($list_promotion){
                        foreach ($list_promotion as $pr){
                            if((int)$pr["bill_no"]>0){
                                $id_addon_user_no = $pr["bill_no"];
                                $query_update_bill_no ="update users_products set is_actived =-1 where id_addon_user = ".$id_addon_user_no;
                                Business_Addon_UsersProducts::getInstance()->excute($query_update_bill_no);
                                
                                Business_Addon_FASThdbanhang::getInstance()->delete_dbtg($id_addon_user_no);
                                $date_no = date('Ymd',  strtotime($pr["update_ispay"]));
                                $proc2 = mssql_init("HNAM_NB_A.dbo.zc_DeleteTranfer 1, '$id_addon_user_no', '$date_no'", $conn);
                                echo $id_addon_user_no,
                                $proc_result2 = mssql_execute($proc2);
                                // Etc...
                                mssql_free_statement($proc2);
                                unset($proc2);
                                
                                
                            }
                        }
                        
                    }
                }
                $result = 'delete complete'.$id_addon_user.'-'.date('d/m/Y H:i:s');
                
                
                if($itype ==1){
                    Business_Addon_FASThdbanhang::getInstance()->delete_dbtg($id_addon_user);
                }else{
                    Business_Addon_FASThdmuahang::getInstance()->delete_dbtg($id_addon_user);
                }
                
                
            }
        }else{
            $result = 'delete fail'.$id_addon_user.'-'.date('d/m/Y H:i:s');
        }
        return $result;
    }

    public function storeid_vivo($storeid){
        if((int)$storeid == 0){ 
            $storeid = (int)$this->_identity["parentid"];
        }
        
        if($storeid == 443){
            return TRUE;
        }
        return FALSE;
    }
    public function storeid_orther($storeid){
        if((int)$storeid == 0){ 
            $storeid = (int)$this->_identity["parentid"];
        }
        if($storeid != 443 && $storeid != 167){
            return TRUE;
        }
        return FALSE;
    }

    public function replace_space($str){
        $str2 = preg_replace("/[^A-Za-z0-9.-]/", "", $str);
        $str3 = str_replace('%E2%80%8E', '',$str2);
        $str4 = preg_replace("/\s+/", "", $str3);
        $str5 = trim($str4);
        $str5 = str_replace(" ",'',$str5);
        return $str5;
    }
    public function copy_addon_user($data){
        $sdata = array();
        $sdata = $data;
        $sdata["id"] =NULL;
        $lastid = Business_Addon_Users::getInstance()->insert($sdata);
        return (int)$lastid;
    }

    public function get_status_warehouse($id = null) { 
        $array = array(
            "0" => "C.OLDX",
            "1" => "C.NEWX",
            "2" => "C.REPR",
            "3" => "K.NEWX",
            "4" => "K.REPR",
            "5" => "K.OLDX",
        ); 
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function get_type_dv($id = null) {
        $array = array(
            "0" => "Dịch vụ IOS",
            "1" => "Dịch vụ Bao bể màn hình",
            "2" => "Dịch vụ Bảo hành mở rộng 24 tháng Hnam",
            "3" => "Dịch vụ Android",
            "4" => "Bảo hành máy cũ",
            "5" => "Bảo hành VIP",
        ); 
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function get_type_bh($id = null) {
        $array = array(
            "1" => "Bảo hành khác",
            "2" => "Máy đi quốc tế",
            "3" => "Bao bể màn hình",
            "4" => "Bảo hành mở rộng",
//            "5" => "Bảo hành VIP",
        ); 
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function get_sex($id = null) {
        $array = array(
            "1" => "Nam",
            "2" => "Nữ",
            "3" => "Khác",
        ); 
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function get_isTypeViettel($id = null) {
        $array = array(
            "ID" => "Chứng minh thư",
            "PASS" => "Hộ chiếu",
            "POP" => "Sổ hộ khẩu",
            "IDC" => "Thẻ căn cước",
            "BUS" => "Giấy phép kinh doanh",
            "DL" => "Bằng lái",
            "TIN" => "Mã số thuế",
        ); 
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function get_status_chuong_trinh($id = null) {
        $array = array(
            "1" => "Đang diển ra",
            "2" => "Sắp kết thúc",
            "3" => "Đã kết thúc gần đây",
        ); 
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function list_type_customer($id = null) {
        $array = array(
            "VIE" => "VIE Tư nhân trong nước",
            "PTE" => "PTE DN_Doanh nghiệp",
        ); 
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function list_ma_goi_cuoc($id = null) {
        $array = array(
            "1" => "TOM11",
            "2" => "LTEASY",
        ); 
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function list_storeid_off($id = null) {
        $array = array(
            "21" => "301 Võ văn tần",
            "443" => "Vivo city",
            "539" => "778 CMT8 PK",
            "622" => "40 Hoàng văn Thụ",
        ); 
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function get_type_program($id = null) {
        $array = array(
            "1" => "Online",
            "2" => "Offine",
            "3" => "Trao giải",
        ); 
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function list_regType($id = null) {
        $array = array(
            "1" => "DM_HNAM",
            "2" => "DCOM_HNAM",
        ); 
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function list_group_customer($id = null) {
        $array = array(
            "1" => "Cá nhân trong nước",
            "2" => "Doanh nghiệp",
            "3" => "Nước ngoài",
        ); 
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function list_sex($id = null) {
        $array = array(
            "F" => "Nữ",
            "M" => "Nam",
        ); 
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function list_phone_preorder_s8($id = null) {
        $array = array(
            "1" => "Nữ",
            "2" => "Nam",
            ""
        ); 
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function list_status_no($id = null) {
        $array = array(
            "1" => "Chưa trả nợ",
            "2" => "Đã trả nợ (Còn thiếu)",
            "3" => "Đã trả nợ (Đủ)",
        ); 
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function list_products_online($id = null) {
        $array = array(
            "1" => "8157",
            "2" => "9031",
            "3" => "11251",
            "4" => "11418",
            "5" => "11417",
        ); 
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function array_pass_thu($id = null) {
        $array = array(
            "1" => "8225",
            "2" => "8224",
            "3" => "11352",
            "4" => "11055",
        ); 
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function list_type_hdmh($id = null) {
        $array = array(
            "2" => "Mua hàng kí gửi",
            "3" => "Mua máy cũ",
        ); 
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function list_imei_bao_hanh_0day($id = null) {
        $array = array(
            "1" => "357917061024182",
            "2" => "357917060729641",
            "3" => "357917060879719",
            "4" => "357917060878166",
            "5" => "357917061023713",
            "6" => "357917060882796",
            "7" => "357917060889197",
            "8" => "357917060889221", 
            "9" => "357917060731548",
            "10" => "357917060675927",
            "11" => "357917060879081",
            "12" => "357917060731001",
            "13" => "357917060882598",
            "14" => "357917060878489",
            "15" => "357917060889189",
            "16" => "357917061722331",
            "17" => "357917060888739",
            "18" => "357917061021998",
            "19" => "357917060729187",
            "20" => "357917060880584",
            "21" => "357917060888967",
            "22" => "357917060676834",
            "23" => "357917060730615",
            "24" => "357917060728445",
            "25" => "357917060729567",
            "26" => "357917061029033",
            "27" => "357917060694191",
            "28" => "357917061029397",
            "29" => "357917060694100",
            "30" => "357917060685827",
            "31" => "357917060693672",
            "32" => "357917060694399",
            "33" => "357917060689605",
            "34" => "357917060690397",
            "35" => "357917060690314",
            "36" => "357917060729229",
            "37" => "357917061029264",
            "38" => "357917060684598",
            "39" => "357917060694050",
            "40" => "357917061029025",
            "41" => "357917061029348",
            "42" => "357917060693359",
            "43" => "357917061029272",
            "44" => "357917060691171",
            "45" => "357917061028555",
            "46" => "357917060692781",
            "47" => "357917060694340",
            "48" => "357917060691346",
            "49" => "357917061028233",
            "50" => "357917061029280",
            "51" => "357917060689662",
            "52" => "357917060693243",
            "53" => "357917060729716",
        ); 
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function list_imei_ss($id = null) {
        $array = array(
            "1" => "354737080927105",
            "2" => "354737081346834",
            "3" => "354737081168949",
            "4" => "354737080925455",
            "5" => "354737081159948",
            "6" => "354737081229790",
            "7" => "354737081229808",
            "8" => "354737080924987", 
            "9" => "354737080930034",
            "10" => "357224074435012",
            "11" => "354737080928442",
            "12" => "354737081168352",
            "13" => "354737080927709",
            "14" => "354737081156324",
            "15" => "354737081158817",
            "16" => "354737081167487",
            "17" => "354737080923120",
        ); 
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function get_data($query){
        $getData = array();        
        $getData["q"] = $query;
        $getData["t"] = 1;
        return $getData;
    }
    public function cancel($_strID,$data_frm){
        $_users_products        = Business_Addon_UsersProducts::getInstance();
        $list = $_users_products->getListByBillId($_strID);
        foreach ($list as $items){
            if($items["addon_info"] != null){
                $id_addon_info[] = (int)$items["addon_info"];
            }
            if($items["id_voucher"] !=null){

                if($items["cated_voucher"] ==1){
                    $list_voucher_old[]=$items["id_voucher"];
                }
                if($items["cated_voucher"] ==2){
                    $list_voucher_money[] = $items["id_voucher"];
                }

            }
            if($items["cated_id"] ==53){
                $__strid[] = $items["products_id"];
            }
            
            if(strlen($items["simso"]) >8){
                $this->update_addon_sim($items["simso"]);
                $this->update_addon_sim_seri($items["imes"]);
            }
            
        }
        if($__strid){
            $strid = implode(",", $__strid);
            $list_p = Business_Ws_ProductsItem::getInstance()->getListByProductsID2($strid);
            foreach ($list_p as $val){
                $___ids = $val["itemid"];
                $quanlity =0;
                $quanlity = $val["quanlity"]+1;
                $__squery[] = "update ws_productsitem set quanlity = $quanlity,enabled=1,onstock=1 where itemid = $___ids ";
            }
            if($__squery){
                $squery = implode(";", $__squery);
                $_users_products->excute($squery);
            }
        }
        $description = $data_frm["description"];
        if($description ==NULL){
            $description ='tool xóa';
        }
        $time_delete = date('Y-m-d H:i:s');
        $id_voucher = "";
        
        $userid = $this->_identity["userid"];
        $query = "update users_products set description ='$description',is_actived=0,"
                . "time_delete='$time_delete',userid_delete_bill='$userid' "
                . " where id_addon_user IN ($_strID)";
        
        $abc = $_GET["abc"];
        if($abc==2){
           echo "<pre>";
            var_dump($query);
            die(); 
        }
        
        $ik= $_users_products->excute($query);
            foreach ($list as $_data){
                if($_data["vote_id"]==167){
                    $itemid = $_data["products_id"];
                    $phone = $_data["phone_addon"];
                    $id_addon_user = $_data["id_addon_user"];
                    $link_accesstrade = "https://www.hnammobile.com/api/update-accesstrade?itemid=$itemid&phone=$phone&billid=$id_addon_user&status=2";
                    $link_best_big_sale = "https://www.hnammobile.com/api/update-best-big-sale?itemid=$itemid&phone=$phone&billid=$id_addon_user&status=2";
                    Business_Common_Utils::getContentByCurl($link_accesstrade);
                    Business_Common_Utils::getContentByCurl($link_best_big_sale);
                }
            }
        
        // đơn hàng trên web
        if($id_addon_info != NULL){
            $strINFO = implode(",", $id_addon_info);
            $query2 =" update addon_quick_cart set sell =0,enabled=0 where id IN ($strINFO)";
            $_users_products->excute($query2);
        }
        // back voucher

        if($list_voucher_old != NULL){
            $voucher_old = implode(",", $list_voucher_old);
            $query3 =" update addon_usedphone_customer set datetime_update ='$time_delete',active=1,storeid_update='' where id IN ($voucher_old)";
            $_users_products->excute($query3);
        }
        if($list_voucher_money != NULL){
            foreach ($list_voucher_money as $val){
                $query4[] = "update ws_vouchers set used =0,number_used=1 where code_name = '$val'";
            }
            $_query4 = implode(";", $query4);
            Business_Addon_Voucher::getInstance()->excute($_query4);
        }
    }
    public function update_addon_sim($sim_number){
        $_seri_sim          = Business_Addon_Simseri::getInstance();
        $query1 = "update addon_sim set enabled=2 where sim_number = '$sim_number'";
        $query2 = "update ws_sim set enabled=1 where title = '$sim_number'";
        $_seri_sim->excute($query1);
        $_seri_sim->excute($query2);
    }
    public function update_addon_sim_seri($seri_sim){
        $_seri_sim          = Business_Addon_Simseri::getInstance();
        $query2 = "update addon_sim_seri set status=0 where seri = '$seri_sim'";
        $_seri_sim->excute($query2);
    }
    public function is_apple($cateid){
        $arr_cateid =array(585,455,759,760,622,764,586,765,587,853,466,879,895);
        if(in_array($cateid, $arr_cateid)){
            return true;
        }else{
            return FALSE;
        }
    }
    public function mobile_app(){
        $detect = new Business_Helpers_Mobiledetect();
        $deviceType = ($detect->isMobile() ? ($detect->isTablet() ? 'tablet' : 'phone') : 'computer');
        if ($deviceType == "phone" || $deviceType == "tablet") {
            return TRUE;
        }else{
            return FALSE;
        }
    }
    public function mobile_app2(){
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'Mac') !== false){
            return TRUE;
        }else{
            return FALSE;
        }
    }
    public function set_thu_hoi_mcu($storeid,$from,$to){
        $__bs = Business_Addon_Usedphonehistory::getInstance();
        $_users_products = Business_Addon_UsersProducts::getInstance();
        $type = "4,7"; // cũ,demo,99,vt99
        $data = $_users_products->get_list_by_type($type,$storeid,$from,$to);
        foreach ($data as $d){
            $__price2[$d["products_id"]] = $d["products_price"];
            if((int)$d["products_id"] >0){
                $__strid[] = $d["products_id"];
            }
        }
        if($__strid != NULL){
            $strid = implode(",", $__strid);
            $list = $__bs->getListByItemID($strid);
        }
        $__price = array();
        foreach ($list as $val){
            $__price[$val["itemid"]] = $val["price"];
        }
        foreach ($data as $item){
            if((int)$__price[$item["products_id"]] >0 &&  (int)$__price2[$item["products_id"]] < (int)$__price[$item["products_id"]]){
                $__autoid[]=$item["autoid"];
            }
        }
        if($__autoid != NULL){
            $autoid = implode(",", $__autoid);
            $query = "update users_products set status_thuhoi=1 where autoid IN ($autoid)";
            $_users_products->excute($query);
        }
    }
    public function accept_pos(){
        $userid = $this->_identity["userid"];
        $idregency = $this->_identity["idregency"];
        if($this->isBGD($idregency)){
            return TRUE;
        }
        $arr_userid =array(22,605);
        if(in_array($userid, $arr_userid)){
            return true;
        }else{
            return FALSE;
        }
    }
    public function list_xuat_xu($id = null) {
        $array = array(
            "1" => "ZP- Hong kong, Macau",
            "2" => "ZA- Singapore",
            "3" => "KH-Korea",
            "4" => "Eu- CHÂU ÂU",
            "5" => "MY-Malaysia",
            "6" => "LL/A - Mỹ",
            "7" => "SL, CN - Slovakia",
            "8" => "VN - Việt Nam",
            "9" => "B/A - Anh",
            "10" => "FD/A , SE - Thụy Sĩ",
            "11" => "TA/A - TAIWAN",
            "12" => "X/A – Úc, New Zealand",
            "13" => "J/A - NHẬT",
            "14" => "PM/A - BA LAN",
            "15" => "RO, RM/A - RUMANI",
            "16" => "GB,GR,GH - Hy Lạp",
            "17" => "DN, ZD/A - Đức, HÀ LAN",
            "18" => "NF – PHÁP, BỈ",
            "19" => "FB – PHÁP, LUXEMBOURG",         
            "20" =>  "LP,PL : BA LAN",                    
             "21" => "ET/A, T, IP QL - Ý",
            "22" => "QN/A - THỤY ĐIỂN",
            "23" => "TH - Thái Lan",
            "24" => "PA- INDONESIA", 
            "26" => "LZ – Paraguay, Chile, Uruguay",
            "27" => "C, VC/A - CANADA",
            "28" => "AB, AE- Ả RẬP ,UAE, QATAR",
            "29" => "AH, HN- ẤN ĐỘ",
            "30" => "BZ- BRAZIL",
            "31" => "MG, CM, HC- HUNGARY",
            "32" => "CH/A - China",
            "33" => "CS,CN- SLOVAKIA, CỘNG HÒA SEC",
            "34" => "CZ- CỘNG HÒA SEC",
            "35" => "E- MEXICO",
            "36" => "EE- ESTONIA",
            "37" => "HB- ISRAEL",
            "38" => "KN- ĐAN MẠCH, NA UY",
            "39" => "KS- PHẦN LAN , THỤY ĐIỂN",
            "40" => "LA – GUATEMALA, HONDURAS,COLOMBIA, PERU, EL SALVADOR, ECUADOR",
            "41" => "LE - ARGENTINA",
            "42" => "LT- LITHUANIA",
            "43" => "LV-LATVIA",
            "44" => "PO – BỒ ĐÀO NHA",
            "45" => "PP- PHILIPPIN",
            "46" => "RR- NGA, MOLDOVA",
            "47" => "SO- NAM PHI",
            "48" => "TU- THỔ NHĨ KÌ",
            "49" => "Y – TÂY BAN NHA",
            "50" => "RS- NGA ",
            "51" => "CL - Paraguay",
            "52" => "ZA - HongKong",
            );

        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function list_mavt_dv($id = null) {
        $array = array(
            "905" => "DV.BHMR24HNAMXXX",
            "890" => "DV.BHMANHINHXXXX",
            "865" => "DV.CAIDATIOSXXXX",
            "901" => "DV.BAOHANHMAYCUX",
            "877" => "DV.SUACHUAXXXXXX",
            "4" => "DV.PHICATHEXXXXX",
            "929" => "DV.CAIDATANDROID",
            "1012" => "DV.BHMRVIPXXXXXX",
            "1028" => "DV.INCHUPHUKIENX",
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function list_isdel($id = null) {
        $array = array(
            "1" => "Đang chờ xử lý",
            "2" => "Đồng ý",
            "3" => "Không đồng ý",
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function list_is_sync($id = null) {
        $array = array(
            "1" => "Chưa đồng bộ",
            "2" => "Đã đồng bộ",
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    
    public function list_mavt_dv_pass($id = null) {
        $array = array(
            "1" => "DV.BHMR24HNAMXXX",
            "2" => "DV.BHMANHINHXXXX",
            "3" => "DV.CAIDATIOSXXXX",
            "4" => "DV.BAOHANHMAYCUX",
            "5" => "DV.SUACHUAXXXXXX",
            "6" => "DV.PHICATHEXXXXX",
            "7" => "DV.SIM3G.TT10",
            "8" => "DV.SIM3G.TT11",
            "9" => "DV.SIM3G.F500",
            "10" => "DV.SIM3G.DATA", 
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function check_imei($detail,$barcode){
        
        $__storeid = (int)$this->_identity["parentid"];
        $dv ="DV.";
        $pk ="PK.DA";
        $km ="KM.";
        $km2 ="PK.MC.";
        $ret = array();
        $err = array();
        if($detail ==NULL){
            $detail_product = Business_Addon_MappingProduct::getInstance()->get_detail_by_imei($barcode);
            if($detail_product == NULL){ 
                $err["id"] ="barcode"; 
                $err["msg"] ="Lỗi chưa có tồn kho vui lòng kiểm tra lại kho. Nếu có tồn kho FAST vui lòng liên hệ Phòng IT"; 
                $ret[] = $err; 
            }else{
                if($detail_product["follow_imei"]==1){
                    $err["id"] ="barcode"; 
                    $err["msg"] ="IMEI này không hợp lệ (chưa tạo bên kế toán). Nếu imei này không theo imei thì liên hệ IT. Ngược lại thì liên hệ kế toán. Vui lòng kiểm tra lại"; 
                    $ret[] = $err;
                }
            }
            
            
            
//           if( (strpos($barcode, $dv) === FALSE) && (strpos($barcode, $pk) === FALSE ) && (strpos($barcode, $km) === FALSE ) && (strpos($barcode, $km2) === FALSE )){
//                $err["id"] ="barcode"; 
//                $err["msg"] ="IMEI này không hợp lệ (chưa tạo bên kế toán). Vui lòng kiểm tra lại"; 
//                $ret[] = $err;
//            }else{
//                $detail_product = Business_Addon_MappingProduct::getInstance()->get_detail_by_imei($barcode);
//                if($detail_product == NULL){ 
//                    $err["id"] ="barcode"; 
//                    $err["msg"] ="IMEI này chưa được tạo tại hệ thống HNAM.Vui lòng kiểm tra lại hoặc liên hệ Phòng IT"; 
//                    $ret[] = $err; 
//                }
//            }
            
            
            
            
        }else{
            
            if((int)$detail["sl_ton"] <1){
                $err["id"] ="barcode"; 
                $err["msg"] ="IMEI này có số lượng tồn kho 0 nên không thể xuất kho. Vui lòng kiểm tra lại"; 
                $ret[] = $err;
            }else{
                $ma_kho = $detail["ma_kho"];
                if ($barcode==null) {
					$barcode = $detail["ma_lo"];
				}
//                $detail_product = Business_Addon_MappingProduct::getInstance()->get_detail_by_id_material($ma_vt);
                $detail_product = Business_Addon_MappingProduct::getInstance()->get_detail_by_imei($barcode);
                if($detail_product==NULL){
                    $err["id"] ="barcode"; 
                    $err["msg"] ="(2) IMEI này chưa được tạo tại hệ thống HNAM.Vui lòng kiểm tra lại hoặc liên hệ Phòng IT"; 
                    $ret[] = $err; 
                }
                if(!$this->accept_pos()){
                    $detail_store = Business_Addon_MappingStore::getInstance()->get_detail_by_id_fast_warehouse($ma_kho);
                    if($__storeid != $detail_store["id_store"]){
                        $err["id"] ="barcode"; 
                        $err["msg"] ="IMEI này không thuộc hệ thống của bạn.Vui lòng kiểm tra lại"; 
                        $ret[] = $err; 
                    } 
                }
            }
        }
        return $ret;
    }
    public function doPost($url, $getData=null, $auth=null) {
        try {
            
            $agents[] = 'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/52.0.2743.116 Safari/537.36';
            $agents[] = 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; SV1)';
            $agents[] = 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; SV1)';
            $agents[] = 'Mozilla/4.0 (compatible; MSIE 9.0; Windows NT 5.1; SV1)';
            $agents[] = 'Mozilla/4.0 (compatible; MSIE 10.0; Windows NT 5.1; SV1)';
            $agents[] = 'Mozilla/4.0 (compatible; MSIE 11.0; Windows NT 5.1; SV1)';
  
            shuffle($agents);
            $agent=$agents[0];
            
            $curlHandle = curl_init(); // init curl
            if ($getData!=null) {
                $url = $url . "?" . http_build_query($getData);
            }
            curl_setopt($curlHandle, CURLOPT_URL, $url); // set the url to fetch
            curl_setopt($curlHandle, CURLOPT_HEADER, 0);
            curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curlHandle, CURLOPT_BINARYTRANSFER, 1);
            curl_setopt($curlHandle, CURLOPT_ENCODING , "");
            curl_setopt($curlHandle, CURLOPT_TIMEOUT, 300);
            curl_setopt($curlHandle, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($curlHandle, CURLOPT_USERAGENT, $agent);
            curl_setopt($curlHandle, CURLOPT_POST, 1);        
            
            if ($auth!=null) {                
                curl_setopt($curlHandle, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
                curl_setopt($curlHandle, CURLOPT_USERPWD, $auth);
            }
            $content = curl_exec($curlHandle);
            curl_close($curlHandle);
        } catch (Exception $ex) {            
            return "Error!!!!";
        }

        return $content;
    }
    public function list_imei_obi($id = null) {
        $array = array(
            "1" => "911477150065835",
            "2" => "911477150200234",
            "3" => "911477150173019",
            "4" => "911477150061131",
            "5" => "911477150136297",
            "6" => "911477150071320",
            "7" => "911477150059598",
            "8" => "911477150065355",
            "9" => "911477150066551",
            "10" => "911477150082574",
            "11" => "911477150049813",
            "12" => "911477150140554",
            "13" => "911477150051215",
            "14" => "911477150040770",
            "15" => "911477150141537",
            "16" => "911477150048351",
            "17" => "911477150109138",
            "18" => "911477150141636",
            "19" => "911477150119095",
            "20" => "911477150051017",
            "21" => "911477150197976",
            "22" => "911477150073235",
            "23" => "911477150049334",
            "24" => "911477150068334",
            "25" => "911477150135612",
            "26" => "911477150114237",
            "27" => "911477150037453",
            "28" => "911477150142931",
            "29" => "911477150174959",
            "30" => "911477150062774",
            "31" => "911477150192779",
            "32" => "911477150048336",
            "33" => "911477150177010",
            "34" => "911477150073771",
            "35" => "911477150065975",
            "36" => "911477150030854",
            "37" => "911477150137956",
            "38" => "911477150140398",
            "39" => "911477150123493",
            "40" => "911477150050217",
            "41" => "911477150067831",
            "42" => "911477150106175",
            "43" => "911477150106431",
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function list_imei_iphone10409_8th($id = null) { //10409
        $array = array(
            "1" => "355733073183987",
            "2" => "353333078038914",
            "3" => "355736071227904",
            "4" => "355733073121128",
            "5" => "353333077704110",
            "6" => "355736072984347",
            "7" => "355735072337084",
            "8" => "355732071909880",
            "9" => "355730071175056",
            "10" => "355737072818352",
            "11" => "353330077132490",
            "12" => "353331076875337",
            "13" => "355735073044846",
            "14" => "355732072831083",
            "15" => "353333077758959",
            "16" => "355733071773631",
            "17" => "355732072428112",
            "18" => "355729070508119",
            "19" => "353333078011663",
            "20" => "353295074295758",
            "21" => "355734071707090",
            "22" => "355736072399926",
            "23" => "355732073125220",
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function list_imei_iphone10410_11th($id = null) { //10410
        $array = array(
            "1" => "355734072878916",
            "2" => "355732071784531",
            "3" => "355734070819847",
            "4" => "355731072394258",
            "5" => "353335075303787",
            "6" => "355733073177567",
            "7" => "353337075463413",
            "8" => "355732071933005",
            "9" => "355733071682691",
            "10" => "355729073157450",
            "11" => "353334078415045",
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function list_imei_iphone10408_7th($id = null) { //10408
        $array = array(
            "1" => "355735072413760",
            "2" => "355732071323926",
            "3" => "353331076982117",
            "4" => "355732071828981",
            "5" => "353328076515962",
            "6" => "355730071122652",
            "7" => "353331076659780",
            "8" => "355730071474459",
            "9" => "353335076976532",
            "10" => "355731071169685",
            "11" => "355732071778566",
            "12" => "355734070759910",
            "13" => "353328076938487",
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function list_imei_iphone9540_7th($id = null) { //9540
        $array = array(
            "1" => "353257078661724",
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    
    public function list_imei_iphone10406_6th($id = null) { //10406
        $array = array(
            "1" => "355729070901033",
            "2" => "353336076126078",
            "3" => "353334076038070",
            "4" => "355730070872729",
            "5" => "355730071341047",
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function list_imei_iphone10411_7th($id = null) { //10411
        $array = array(
            "1" => "355737071388811",
            "2" => "353330076120405",
            "3" => "355728070623845",
            "4" => "353333076532595",
            "5" => "353337076081263",
            "6" => "355733071848409",
            "7" => "355732071484942",
            "8" => "355735071248456",
            "9" => "355728071340969",
            "10" => "355729070741033",
            "11" => "353333076984101",
            "12" => "353333076947587",
            "13" => "355733071388281",
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function list_imei_iphone10407_6th($id = null) { //10407
        $array = array(
            "1" => "355730070994895",
            "2" => "355732071197502",
            "3" => "355737070616063",
            "4" => "355729070662098",
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function list_imei_iphone10412_8th($id = null) { //10412
        $array = array(
            "1" => "353333076680709",
            "2" => "355731072071237",
            "3" => "355737071365645",
            "4" => "355734071756311",
            "5" => "355732072922767",
            "6" => "353333076852324",
            "7" => "353333076695319",
            "8" => "355737071069700",
            "9" => "355736071509046",
            "10" => "355728071049982",
            "11" => "355733071847104",
            "12" => "355734071995588",
            "13" => "355735072857230",
            "14" => "355733072405670",
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function list_imei_iphone10413_11th($id = null) { //10413
        $array = array(
            "1" => "355733071388026",
            "2" => "353337073542176",
            "3" => "353335076996878",
            "4" => "353334076168828",
            "5" => "353333077761425",
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    
    
    public function list_imei_iphone10477($id = null) { //10477
        $array = array(
            "1" => "352083070272950",
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function list_imei_iphone10478($id = null) { //10478
        $array = array(
            "1" => "355676072205030",
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function list_imei_iphone10479($id = null) { //10479
        $array = array(
            "1" => "352086077048059",
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function list_imei_iphone10480($id = null) { //10480
        $array = array(
            "1" => "358361068197171",
            "2" => "358368068074813",
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function list_imei_iphone10481($id = null) { //10481
        $array = array(
            "1" => "359260060276075",
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function list_imei_iphone10482($id = null) { //10482
        $array = array(
            "1" => "355691073639293",
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function list_imei_iphone10483($id = null) { //10483
        $array = array(
            "1" => "353285072016405",
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function list_imei_iphone10484($id = null) { //10484
        $array = array(
            "1" => "353335075429467",
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function list_imei_iphone10485($id = null) { //10485
        $array = array(
            "1" => "355733072070300",
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function list_imei_iphone10486($id = null) { //10486
        $array = array(
            "1" => "353332073540312",
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    
    public function get_prev_date($cur_date,$countday){
        $time_on_day = $countday * 24 * 60 * 60;
        $cur_time = strtotime($cur_date);
        $previous_time = $cur_time - $time_on_day;
        $previous_date = date("Y-m-d", $previous_time);
        return $previous_date;
    }
    public function is_phone($phone,$id){
        if($id ==null){
           $id = 'phone'; 
        }
        $phone_tmp = substr($phone, 0,2);
        if ($phone == null) {
            $err['id'] = "$id";
            $err['msg'] = "Vui lòng nhập số điện thoại .";
            $ret[] = $err;
        }
        
        elseif(strlen($phone) < 10 || strlen($phone) >11){
            $err['id'] = "$id";
            $err['msg'] = "Số điện thoại này không tồn tại.Vui lòng kiểm tra lại .";
            $ret[] = $err;
        }
        elseif($phone_tmp != "01" && $phone_tmp != "09" && $phone_tmp != "08" && $phone_tmp != "03" && $phone_tmp != "05" && $phone_tmp != "06" && $phone_tmp != "07"){
            $err['id'] = "$id";
            $err['msg'] = "Số điện thoại này không tồn tại.Vui lòng kiểm tra lại .";
            $ret[] = $err;
        }
        elseif(($phone_tmp == "01" && strlen($phone) != 11) || $phone_tmp == "09" && strlen($phone) != 10){
            $err['id'] = "$id";
            $err['msg'] = "Số điện thoại này không tồn tại.Vui lòng kiểm tra lại .";
            $ret[] = $err;
        }
        return $ret;
    }
    public function is_email_format($email,$id){
        $ret = array();
        if($id ==null){
           $id = 'email'; 
        }
        if($email != NULL && !$this->isValidEmail($email)){
           $err["id"] = "$id";
            $err["msg"] = "Vui lòng nhập đúng định dạng email.";
            $ret[] = $err; 
        }
        return $ret;
    }
    public function islogin(){
        $ok =0;
        if((int)$this->_identity["userid"]==0){
            $ok=1;
            echo "<script>window.parent.show_nologin();</script>";
            return;
        }
        return $ok;
    }
    public function get_ck($id){
        $array = array(
            "1" => "Chiết khấu đơn hàng", 
            "2" => "Chiết khấu ngoài đơn hàng",
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function get_kich_hoat($id){
        $array = array(
            "0" => "Chưa kích hoạt", 
            "1" => "Đã kích hoạt", 
            
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function get_list_audio($id){
        $array = array(
            "1" => "Subscribe", 
            "2" => "Follow", 
            "3" => "Content", 
            
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function get_list_bhang($id){
        $array = array(
            "1" => "Đã báo", 
            "2" => "Chưa báo", 
            
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function get_hd($id){
        $array = array(
            "1" => "1. Không có HĐ", 
            "2" => "2. Chưa có HĐ", 
            "3" => "3. Đã có HĐ", 
            
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    
    public function status_history_guarantee($id = null) {
        $array = array(
            "1" => "Ngày giao máy cho nhân viên",
            "2" => "Nhân viên lên hãng ngày nào",
            "3" => "Nhân viên chuyển lại cho kho bảo hành, chi nhánh ngày nào",
            "4" => "Ngày xuất sản phẩm",
            
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function status_imei($id = null) { 
        $array = array(
            "1" => "Bán từ HPOS",
            "2" => "Đổi IMEI",
            "3" => "Thu máy cũ",
            "4" => "Thu máy trên HPOS",
            "5" => "Đơn hàng từ NCC",
            "6" => "Đơn hàng bị hủy",
            "7" => "Đơn hàng bị bán lại",
            "8" => "IMEI A được đổi bảo hành bởi IMEI B",
            "9" => "IMEI B đổi bảo hành cho IMEI A",
            "10" => "Đã trả nợ",
            
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }

    /**
     * Enter description here...
     *
     * @return Maro_Cache
     */
    
    public function sendMail($to,$subject,$displayname,$body_html,$cc,$bcc){
        $mail_config = "smtp.mailgun.org;587;postmaster@hnammobile.vn;vannghi@123@098";
        $from = "no-reply@hnammobile.vn";
        $replyto = "";
        $result = Business_Common_Utils::sendEmailV4($from, $displayname, $replyto, $to, $subject, $body_html, $file_attached="",$mail_config,$cc,$bcc);
        return $result;
    }
    public  function getPath($dir1="",$dir2="") {
        $lastChar = strrchr(BASE_PATH, "/");     
        if ($lastChar !== "/") {
            $basePath = BASE_PATH . "/";
        }
        if($dir1==NULL){
            $dir1 ='general';
        }
        $basePath = $basePath . "uploads/$dir1";
        if($dir2 != NULL){
           $basePath .="/$dir2"; 
        }
        return sprintf("%s", $basePath);
    }
    public function upload($path,$expensions,$__file="",$name=""){
        if($__file == NULL){
          $__file = $_FILES['file_upload'];  
        }
        $ret = array();
        if(isset($__file)){
            $arr = array();
            $file_name = $__file['name'];
            if($file_name != null){
                $file_size = $__file['size'];
                $file_tmp =$__file['tmp_name'];
                $file_type  =$__file['type'];
                $file_ext=strtolower(end(explode('.',$__file['name'])));
                $arr["name_files"] =0;
                $__expensions = implode(",", $expensions);
                if(in_array($file_ext,$expensions)=== false){
                    $arr["id"] = "file_upload";
                    $arr["msg"] = "File upload không đúng định dạng ($__expensions)";
                    $ret[] = $arr;
                }
                if($file_size > 5097152){
                    $arr["id"] = "file_upload";
                    $arr["msg"] = "Dung lượng vượt quá 5MB";
                    $ret[] = $arr;
                }
                if (!is_dir($path)) {
                    mkdir($path, 0777, true);
                }
                $name_f = $this->vn_str_filter($file_name);
                $__dates = date('YmdHis');
                $name_f = $__dates."-".Business_Common_Utils::adaptTitleLinkURLSEO2($name_f);
                if($name)
                    $name_f = $name.'.'.$file_ext;
                $newFile = $path . "/".$name_f;
                if(count($ret)==0){
                    $arr["files_path"] = $newFile;
                    $arr["file_ext"] = $file_ext;
                    $arr["name_files"] = $name_f;
                    $arr["id"] = "ok";
                    $arr["msg"] = "ok";
                    $ret[] = $arr;
                    move_uploaded_file($file_tmp,$newFile);
                }
            }
         }
         return $ret;
    }
    public function upload_muti($path,$expensions,$__file=""){
        if($__file == NULL){
          $__file = $_FILES['file_upload'];  
        }
        $ret = array();
        if(isset($__file)){
                foreach ($__file['name'] as $key=> $val){
                    $arr[$key] = array();
                    $file_name = $val;
                    if($file_name != null){
                        $file_tmp =$__file['tmp_name'][$key];
                        $file_size = $__file['size'][$key];
                        $expensions = array("jpeg","jpg","png","gif");
                        $file_ext=strtolower(end(explode('.',$val)));
                        $__expensions = implode(",", $expensions);
                        if(in_array($file_ext,$expensions)=== false){
                            $arr[$key]["id"] = "file_upload";
                            $arr[$key]["msg"] = "File upload không đúng định dạng2 ($__expensions)";
                            $rets[] = $arr[$key];
                        }

                        $imgDetail = getimagesize($file_tmp);
                        if (strpos($imgDetail["mime"], "image")===false) {
                            $arr[$key]["id"] = "file_upload";
                            $arr[$key]["msg"] = "File upload không đúng định dạng1 ($__expensions)";
                            $rets[] = $arr[$key];
                        }

                        if($file_size > 5097152){
                            $arr[$key]["id"] = "file_upload";
                            $arr[$key]["msg"] = "Dung lượng vượt quá 5MB";
                            $rets[] = $arr[$key];
                        }
                        
                        if (!is_dir($path)) {
                            mkdir($path, 0777, true);
                        }
                        $name_f = $this->vn_str_filter($file_name);

                        $__dates = date('YmdHis').$key;
                        $name_f = $__dates."-".str_replace(" ", "-", $name_f);
                        $newFile = $path . "/".$name_f;
                        if(count($ret)==0){
                            $arr["files_path"] = $newFile;
                            $arr["file_ext"] = $file_ext;
                            $arr["name_files"] = $name_f;
                            $arr["id"] = "ok";
                            $arr["msg"] = "ok";
                            $ret[] = $arr;
                           move_uploaded_file($file_tmp,$newFile);
                        }
                
                    }
                }
         }
         return $ret;
    }
    public function upload2($__file="",$path,$name){
        if($__file == NULL){
          $__file = $_FILES['file_upload'];  
        }
        if(isset($__file)){
            $ret = array();
            $arr = array();
            $file_name = $__file['name'];
            if($file_name != null){
                
                $file_size = $__file['size'];
                $file_tmp =$__file['tmp_name'];
                $file_type  =$__file['type'];
                
                $data = getimagesize($file_tmp);
                $w = $data[0];
                $h = $data[1];
                $width = $w;
                $height = $h;
                if($w>1200){
                    $width = 1200;
                    $height = $h * 1200 / $w;
                }
                if($height>1600){
                    $width = $width*1600/$height;
                    $height = 1600;
                }
                
                $file_ext=strtolower(end(explode('.',$__file['name'])));
                $arr["name_files"] =0;
                if($expensions==NULL){
                    $expensions = array("png","jpg");
                }
                $__expensions = implode(",", $expensions);
                if(in_array($file_ext,$expensions)=== false){
                    $arr["id"] = "file_upload";
                    $arr["msg"] = "File upload không đúng định dạng ($__expensions)";
                    $ret[] = $arr;
                }
                if($file_size > 25097152){
                    $arr["id"] = "file_upload";
                    $arr["msg"] = "Dung lượng vượt quá 24MB";
                    $ret[] = $arr;
                }
                if (!is_dir($path)) {
                    mkdir($path, 0777, true);
                }
                
                $newFile = $path . "/".$name;
                if(count($ret)==0){
                    $arr["files_path"] = $newFile;
                    $arr["file_ext"] = $file_ext;
                    $arr["id"] = "ok";
                    $arr["msg"] = "ok";
                    $ret[] = $arr;
                    Business_Helpers_Image::getInstance()->resizeImage2($file_tmp, "$newFile", $width, $height); 
                }
            }
         }
         return $ret;
    }
    
    
    public function LIST_PRINT($id = null) {
        $array = array(
            "0" => "Xem thử",
            "1" => "CA I",
            "2" => "CA II",
            "3" => "CA III",
            "4" => "Tất cả trong ngày",
            "5" => "Tổng tiền trên bill",
            
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function list_ispay($id = null) {
        $array = array(
            "1" => "Chưa trả nợ",
            "2" => "Đã trả nợ",
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function CTY_HNAM_PKIEN_NEW_OLD($id = null) {
        $array = array(
            "1" => "HÀNG CÔNG TY(MỚI)",
            "2" => "HÀNG CÔNG TY(CŨ)",
            "3" => "HÀNG HNAM (MỚI)",
            "4" => "HÀNG HNAM (CŨ)",
            "5" => "PHỤ KIỆN",
            
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function CTY_HNAM_PKIEN($id = null) {
        $array = array(
            "1" => "HÀNG CÔNG TY",
            "2" => "HÀNG HNAM",
            "3" => "PHỤ KIỆN",
            "4" => "DỊCH VỤ", 
            
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    
    public static function getQT($id=null) {
        $quatang = array(
            "0" => array(
                "name" => "Thẻ nhớ 16Gb Class 10",
                "price" => "99000",
            ),
            "1" => array(
                "name" => "Lắp sau cao cấp",
                "price" => "39000",
            ),
            "2" => array(
                "name" => "Dán màn hình cường lực chống bể",
                "price" => "29000",
            ),
            "3" => array(
                "name" => "Tai nghe Genius M220 (có micro)",
                "price" => "139000",
            ),
            "4" => array(
                "name" => "Pin dự phòng Tuxedo 5.000 mAh",
                "price" => "159000",
            ),
            "5" => array(
                "name" => "Combo 5 phụ kiện trên",
                "price" => "419000",
            ),
            "6" => array(
                "name" => "Combo 3 phụ kiện trên",
                "price" => "359000",
                "link" => ""
            ),
           );

        if ($id===null) {
             return $quatang;
        }
        return $quatang[$id];
       }
       public static function getQTInfocus($id=null) {
        $quatang = array( //infocus
		"0" => array(
		    "name" => "Giá đỡ Hoco",
		    "price" => "68000",
		    "link" => "#"
		),
		"1" => array(
		    "name" => "Tai nghe Bluetooth Roman X3S",
		    "price" => "219000",
		    "link" => "#"
		),
		"2" => array(
		    "name" => "Thẻ nhớ 16Gb Class 10",
		    "price" => "89000",
		    "link" => "#"
		),
		"3" => array(
		    "name" => "Pin dự phòng Tuxedo 5.000 mAh",
		    "price" => "143000",
		    "link" => "#"
		),
		"4" => array(
		    "name" => "Dán màn hình cường lực chống bể",
		    "price" => "49000",
		    "link" => "#"
		),
		"5" => array(
		    "name" => "Loa nghe nhạc bluetooth Soundlink",
		    "price" => "219000",
		    "link" => "#"
		),
		"6" => array(
		    "name" => "Nguyên combo gồm các phụ kiện trên",
		    "price" => "650000",
		    "link" => ""
		),
            );

        if ($id===null) {
             return $quatang;
        }
        return $quatang[$id];
       }
    public function getDHOnline($id = null) {
        $array = array(
            "1" => "Đơn hàng online",
            "2" => "Đơn hàng đóng gói",
            "3" => "Đơn hàng Pre-Order",
            
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function getStatusGHN_QUICKCART($id = null) {
        $array = array(
            array(
                "id"=>"0","name"=>"Mua nhanh.","icon"=>"<small class='label pull-left bg-primary rgh'>"
            ),
            array(
                "id"=>"1","name"=>"Lỗi khi Cancel hoặc thao tác lỗi.","icon"=>"<small class='label pull-left bg-red rgh'>"
            ),
            array(
                "id"=>"2","name"=>"Lỗi khi hoàn thành, khi giao dịch lỗi khi là vấn đề thanh toán","icon"=>"<small class='label pull-left bg-red rgh'>"
            ),
            array(
                "id"=>"3","name"=>"Hoàn thành việc thanh toán (đã trả tiền).","icon"=>"<small class='label pull-left bg-green rgh'>"
            ),
            
            
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function getCatedType($id = null) {
        $array = array(
            "1" => "Giảm tiền",
            "2" => "Giảm %"
            
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function getStatusKBH($id){
        $array = array(
            array("id"=>1,"name"=>"Đang ở KBH","color"=>""),
            array("id"=>2,"name"=>"Đang ở Giao Nhận","color"=>"#367fa9"),
            array("id"=>3,"name"=>"Đang ở Nhà Phân Phối","color"=>"#DD4B39"),
            array("id"=>4,"name"=>"Đang làm mới","color"=>"#00A65A"),
            array("id"=>5,"name"=>"Đang chờ hóa đơn","color"=>"#F39C12"),
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function getCompleteKBH($id){
        $array = array(
            array("id"=>0,"name"=>"Chưa sửa","color"=>"#808080"),
            array("id"=>1,"name"=>"Đang sửa","color"=>"#8e8951"),
            array("id"=>2,"name"=>"Hoàn tất - Sửa được","color"=>"#095f23"),
            array("id"=>3,"name"=>"Hoàn tất -  Không sửa được","color"=>"#ec6c71"),
            array("id"=>4,"name"=>"Hoàn tất - Máy bình thường","color"=>"#87d881"),
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function get_status_collectingmoney($id){
        $array = array(
            "1"=>"Ở cửa hàng",
            "2"=>"Cửa hàng chuyển cho nhân viên giao nhận - Đang chờ xác nhận",
            "3"=>"Ở nhân viên giao nhận",
            "4"=>"Nhân viên giao nhận chuyển cho KBH - Đang chờ xác nhận",
            "5"=>"Ở KBH",
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function getBH($id){
        $array = array(
            "1"=>"Còn bảo hành",
            "2"=>"Hết bảo hành",
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function get_transfers($id){
        $array = array(
            "1"=>"Chờ",
            "2"=>"Đã chuyển",
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function get_ok($id){
        $array = array(
            "1"=>"Yes",
            "2"=>"No",
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function get_status_bhsc($id){
        $array = array(
            "1"=>"Chưa xử lý",
            "2"=>"Đã xử lý",
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function get_status($id){
        $array = array(
            "1"=>"Chưa xử lý",
            "2"=>"Đang xử lý",
            "3"=>"Đã xử lý",
            "4"=>"Từ chối",
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function get_list_bhsc_orther($userid){
        $array = array(
            "565"=>"Trung tâm bảo hành HNAM",
            "564"=>"Trung tâm bảo hành Samsung",
        );
        if ($userid === null) {
            return $array;
        }
        return $array[$userid];
    }
    public function getConfirm($id){
        $array = array(
            array("id"=>0,"name"=>"Chưa xác nhận","color"=>"red","icon"=>"<i style='color:red;size:50px' class='fa fa-minus-circle fa-3 btn' aria-hidden='true'></i>"),
            array("id"=>1,"name"=>"Đã xác nhận","color"=>"#090","icon"=>"<i style='color:#090;size:50px' class='fa fa-check-circle fa-3 btn' aria-hidden='true'></i>","icon2"=>"<i style='color:#4a90e2;size:50px' class='fa fa-check-circle fa-3 btn' aria-hidden='true'></i>"),
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function isDV($id){
        $array = array(
            "1" => "Bảo hành mở rộng", 
            "2" => "AAA", 
            "3" => "Combo", 
            "4" => "IOS", 
            "5" => "ZingVip", 
            "6" => "Gói bảo hiểm màn hình", 
            "8" => "Sim mobile", 
            "11" => "Android", 
            "1012" => "Bảo hành VIP 1 đổi 1", 
            "1013" => "Gói cước viettel", 
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function getStartDateHIS($str_end_date){
        $day = explode("-", $str_end_date);
        $result = date('Y-m-d H:i:s',  strtotime($day[0]));
        return $result;
    }
    public function getEndDateHIS($str_end_date){
        $day = explode("-", $str_end_date);
        $result = date('Y-m-d H:i:s',  strtotime($day[1]));
        return $result;
    }
    public function getStartDate($str_end_date){
        $day = explode("-", $str_end_date);
        $result = date('Y-m-d',  strtotime($day[0]))." 00:00:00";
        return $result;
    }
    public function getEndDate($str_end_date){
        $day = explode("-", $str_end_date);
        $result = date('Y-m-d',  strtotime($day[1]))." 23:59:59";
        return $result;
    }

 public function getSelectOne($id){
        $array = array(
            "0" => "Một", 
            "1" => "Nhiều", 
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
 public function getTypeUser($id){
        $array = array(
            "0" => "Nhóm", 
            "1" => "Người dùng", 
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
 public function getCTKM($id){
        $array = array(
            "0" => "Mặc định", 
            "1" => "Khuyến mãi giờ vàng", 
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }


    public function getTypeDiscount($id){
        $array = array(
            "1" => "Tiền(chưa VAT)", 
            "2" => "Phần trăm(chưa VAT)", 
            "3" => "Tiền(có VAT)", 
            "4" => "Phần trăm(có VAT)", 
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function getHTs($id){
        $array = array(
            "1" => "Hoàn tất", 
            "2" => "Chưa hoàn tất", 
            
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function getCompletes($id){
        $array = array(
            "1" => "Thành công", 
            "2" => "Thất bại", 
            
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function getCatedHnam($id){
        $array = array(
            "3" => "Điện thoại", 
            "4" => "Phụ kiện", 
            "5" => "Máy tính bảng", 
            "6" => "Laptop", 
            "8" => "Đồng hồ thông minh", 
            "10" => "Dịch vụ", 
            "53" => "Kho máy cũ", 
            "9" => "Nước hoa", 
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function getCatedBaohanhHnam($id){
        $array = array(
            "3" => "Điện thoại", 
            "4" => "Phụ kiện", 
            "5" => "Máy tính bảng", 
            "6" => "Laptop", 
            "8" => "Đồng hồ thông minh", 
            "53" => "Kho máy cũ", 
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function get_tk_voucher($id){
        $array = array(
            "3" => "5212111", 
            "4" => "5212112", 
            "5" => "5212113", 
            "6" => "5212114", 
            "9" => "5212115", 
            "8" => "5212119", 
            "10" => "5212119", 
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function get_tk_gift($id){
        $array = array(
            "3" => "5212121", 
            "4" => "5212122", 
            "5" => "5212123", 
            "6" => "5212124", 
            "9" => "5212125", 
            "8" => "5212129", 
            "10" => "5212129", 
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function get_phantram_ck_lotte($id){
        $array = array(
            "3" => "3", 
            "4" => "6", 
            "5" => "3", 
            "6" => "0", 
            "9" => "0", 
            "8" => "3", 
            "10" => "3", 
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function get_tk_ck_lotte_adayroi($id){
        $array = array(
            "3" => "521111", 
            "4" => "521112", 
            "5" => "521113", 
            "6" => "521114", 
            "9" => "521115", 
            "8" => "521119", 
            "10" => "521119", 
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function get_phantram_ck_adayroi($id){
        $array = array(
            "3" => "2", 
            "4" => "12", 
            "5" => "2", 
            "6" => "0", 
            "9" => "0", 
            "8" => "2", 
            "10" => "2", 
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function get_phantram_ck_tiki($id){
        $array = array(
            "3" => "2", 
            "4" => "12", 
            "5" => "2", 
            "6" => "0", 
            "9" => "0", 
            "8" => "2", 
            "10" => "2", 
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function get_status_money($id){
        $array = array(
            "1" => "Đang ở chi nhánh", 
            "2" => "Chi nhánh chuyển cho nhân viên (chưa xác nhận)", 
            "3" => "Đang ở nhân viên(đã xác nhận)", 
            "4" => "Nhân viên chuyển cho KBH(chưa xác nhận)", 
            "5" => "Đang ở KBH(đã xác nhận)", 
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    private function getCacheInstance() {
        $cache = GlobalCache::getCacheInstance('nt');
        return $cache;
    }
    public function getToken($vote_id,$id_addon_user){
        $string = '';
        $string = md5($this->_secKey.$vote_id.$id_addon_user);
        return $string;
    }
    public function getMenuById($menuid){
        $_products = Business_Helpers_Products::getInstance();
        switch ($menuid) {
            case 3:
                $menu = $_products->getProductMenu();
                break;
            case 4:
                //$menu = $_products->getAccMenu2();
                $menu = $_products->getAccMenu3();
                break;
            case 5:
                $menu = $this->getTabletMenu();
                break;
            case 6:
                $menu = $this->getLaptopMenu();
                break;
            case 8:
                $menu = $this->getWatchMenu();
                break;
            case 10:
                $menu = $this->getServiceMenu();
                break;
            case 9:
                $menu = $this->get_nuochoa();
                break;
            case 53:
                $menu = $this->get_khomaycu();
                break;
//            ...
            default:
                $menu = $_products->getProductMenu();
        }
        
        return $menu;
    }
    public function getMenuById2($menuid){
        $_products = Business_Ws_MenuItem::getInstance();
        switch ($menuid) {
            case 3:
                $menu='menu_products';
                break;
            case 4:
                $menu='menu_acc';
                break;
            case 5:
                $menu='menu_tablet';
                break;
            case 6:
                $menu='menu_laptop';
                break;
            case 8:
                $menu='menu_watch';
                break;
            case 10:
                $menu='menu_service_sale';
                break;
            case 9:
                $list = $this->get_nuochoa();
                break;
            case 53:
                $list = $this->get_khomaycu();
                break;
            default:
                $menu='menu_products';
        }
        if($menuid !=9 && $menuid!=53){
            $list = $_products->getListByName2($menu); 
        }
        return $list;
    }
    function get_khomaycu(){
        $arr["itemid"]          = 53;
        $arr["title"]           = "Kho máy cũ";
        $ret[] = $arr;
        return $ret;
    }
    function get_name_cated_ispecl($id,$name){
        $arr["itemid"]          = $id;
        $arr["title"]           = $name;
        $ret[] = $arr;
        return $ret;
    }

    public function get_nuochoa() {
        /* get menu tablet */
        $depth = 1;
        $parentid = 0;
        $menuname = 'menu_perfume';
        $leftMenuLaptop = Business_Helpers_Common::getMenuLev($depth, $parentid, $menuname);
        if (count($leftMenuLaptop) > 0) {
            $i = 0;

            foreach ($leftMenuLaptop as &$item) {
                $item["title_encode"] = htmlspecialchars($item["title"]);
                $cateid = $item['itemid'];
                if ($cateid == 191)
                    $pos = $_pos;
                $_pos++;


                $title = Business_Common_Utils::adaptTitleLinkURLSEO($item['title']);
                if ($_cateid == $cateid)
                    $active = 'active'; else
                    $active = '';
            }
        }
        return $leftMenuLaptop;
    }
    public function getServiceMenu() {
        /* get menu tablet */
        $depth = 1;
        $parentid = 0;
        $menuname = 'menu_service_sale';
        $leftMenuLaptop = Business_Helpers_Common::getMenuLev($depth, $parentid, $menuname);
        if (count($leftMenuLaptop) > 0) {
            $i = 0;

            foreach ($leftMenuLaptop as &$item) {
                $item["title_encode"] = htmlspecialchars($item["title"]);
                $cateid = $item['itemid'];
                if ($cateid == 191)
                    $pos = $_pos;
                $_pos++;


                $title = Business_Common_Utils::adaptTitleLinkURLSEO($item['title']);
                if ($_cateid == $cateid)
                    $active = 'active'; else
                    $active = '';
            }
        }
        return $leftMenuLaptop;
    }
    public function getformality($id){
        $array = array(
            "1" => "Tiền mặt", 
            "2" => "Ngân hàng", 
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public  function isValidEmail($email) {
        if (preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $email)) {
            return true;
        }
        return false;
    }
    public function checkPhone($phone,$ids=""){
        if($ids ==null){
           $ids = $phone; 
        }
        if ($phone == null || empty($phone)) {
            $err['id'] = "$ids";
            $err['msg'] = "Vui lòng nhập số điện thoại .";
            $ret[] = $err;
        }
        
        if(strlen($phone) < 10 || strlen($phone) >11){
            $err['id'] = "$ids";
            $err['msg'] = "Số điện thoại này không tồn tại.Vui lòng kiểm tra lại .";
            $ret[] = $err;
        }
        $phone_tmp = substr($phone, 0,2);
        if($phone_tmp != "01" && $phone_tmp != "09" && $phone_tmp != "08"){
            $err['id'] = "$ids";
            $err['msg'] = "Số điện thoại này không tồn tại.Vui lòng kiểm tra lại .";
            $ret[] = $err;
        }
        if(($phone_tmp == "01" && strlen($phone) != 11) || $phone_tmp == "09" && strlen($phone) != 10){
            $err['id'] = "$ids";
            $err['msg'] = "Số điện thoại này không tồn tại.Vui lòng kiểm tra lại .";
            $ret[] = $err;
        }
        return $ret;
    }
    public function getNameCTTarget($id){
        $array = array(
            "1" => "Nhóm 1 (9 nhân viên)", 
            "2" => "Nhóm 2 (11 nhân viên)", 
            "3" => "Nhóm 3 (12 đến 13 nhân viên)", 
            "4" => "CH Thường Nhóm 1 (8 nhân viên)- update 14/02/2017", 
            "5" => "CH Thường Nhóm 2 (11 nhân viên)- update 14/02/2017", 
            "6" => "CH Thường Nhóm 3 (12 nhân viên)- update 14/02/2017", 
            "7" => "CH S.O  - update 14/02/2017", 
            "8" => "CH SES  - update 14/02/2017",
            "9" => "Nhóm 1 - DOANH SỐ MÁY CTY + HNAM (NEW) T7/2018", 
            "10" => "Nhóm 2 - DOANH SỐ MÁY CTY + HNAM (NEW) T7/2018", 
            "11" => "Nhóm 3 - DOANH SỐ MÁY CTY + HNAM (NEW) T7/2018", 
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function getCTTarget($id){
        $array = array(
            "1" => array(
                "1"=>array("name"=>"Qũy cửa hàng","tytrong"=>"10","idregency"=>0),
                "2"=>array("name"=>"Cửa hàng trưởng","tytrong"=>"9","idregency"=>11),
                "3"=>array("name"=>"Cửa hàng phó/Trưởng kỹ thuật","tytrong"=>"16.2","idregency"=>14),
                "4"=>array("name"=>"Nhân viên bán hàng","tytrong"=>"45","idregency"=>10),
                "5"=>array("name"=>"Nhân viên kỹ thuật","tytrong"=>"19.8","idregency"=>12),
            ), 
            "2" => array(
                "1"=>array("name"=>"Qũy cửa hàng","tytrong"=>"10","idregency"=>0),
                "2"=>array("name"=>"Cửa hàng trưởng","tytrong"=>"9","idregency"=>11),
                "3"=>array("name"=>"Cửa hàng phó/Trưởng kỹ thuật","tytrong"=>"24.3","idregency"=>14),
                "4"=>array("name"=>"Nhân viên bán hàng","tytrong"=>"40.5","idregency"=>10),
                "5"=>array("name"=>"Nhân viên kỹ thuật","tytrong"=>"16.2","idregency"=>12),
            ), 
            "3" => array(
                "1"=>array("name"=>"Qũy cửa hàng","tytrong"=>"10","idregency"=>0),
                "2"=>array("name"=>"Cửa hàng trưởng","tytrong"=>"9","idregency"=>11),
                "3"=>array("name"=>"Cửa hàng phó/Trưởng kỹ thuật","tytrong"=>"24.3","idregency"=>14),
                "4"=>array("name"=>"Nhân viên bán hàng","tytrong"=>"36","idregency"=>10),
                "5"=>array("name"=>"Nhân viên kỹ thuật","tytrong"=>"20.7","idregency"=>12),
            ),  
            "4" => array(
                "1"=>array("name"=>"Qũy cửa hàng","tytrong"=>"10","idregency"=>0),
                "2"=>array("name"=>"Cửa hàng trưởng","tytrong"=>"11.7","idregency"=>11),
                "3"=>array("name"=>"Cửa hàng phó","tytrong"=>"10.8","idregency"=>14),
                "4"=>array("name"=>"Nhân viên bán hàng","tytrong"=>"45","idregency"=>10),
                "5"=>array("name"=>"Nhân viên kỹ thuật","tytrong"=>"22.5","idregency"=>12),
            ),  
            "5" => array(
                "1"=>array("name"=>"Qũy cửa hàng","tytrong"=>"10","idregency"=>0),
                "2"=>array("name"=>"Cửa hàng trưởng","tytrong"=>"10.8","idregency"=>11),
                "3"=>array("name"=>"Cửa hàng phó","tytrong"=>"19.8","idregency"=>14),
                "4"=>array("name"=>"Nhân viên bán hàng","tytrong"=>"40.5","idregency"=>10),
                "5"=>array("name"=>"Nhân viên kỹ thuật","tytrong"=>"18.9","idregency"=>12),
            ),  
            "6" => array(
                "1"=>array("name"=>"Qũy cửa hàng","tytrong"=>"10","idregency"=>0),
                "2"=>array("name"=>"Cửa hàng trưởng","tytrong"=>"9","idregency"=>11),
                "3"=>array("name"=>"Cửa hàng phó","tytrong"=>"24.3","idregency"=>14),
                "4"=>array("name"=>"Nhân viên bán hàng","tytrong"=>"36","idregency"=>10),
                "5"=>array("name"=>"Nhân viên kỹ thuật","tytrong"=>"20.7","idregency"=>12),
            ),  
            "7" => array(
                "1"=>array("name"=>"Qũy cửa hàng","tytrong"=>"10","idregency"=>0),
                "2"=>array("name"=>"Cửa hàng trưởng","tytrong"=>"27","idregency"=>11),
                "3"=>array("name"=>"Cửa hàng phó","tytrong"=>"0","idregency"=>14),
                "4"=>array("name"=>"Nhân viên bán hàng","tytrong"=>"63","idregency"=>10),
                "5"=>array("name"=>"Nhân viên kỹ thuật","tytrong"=>"0","idregency"=>12),
            ),  
            "8" => array(
                "1"=>array("name"=>"Qũy cửa hàng","tytrong"=>"0","idregency"=>0),
                "2"=>array("name"=>"Cửa hàng trưởng","tytrong"=>"30","idregency"=>11),
                "3"=>array("name"=>"Cửa hàng phó","tytrong"=>"20","idregency"=>14),
                "4"=>array("name"=>"Nhân viên bán hàng","tytrong"=>"50","idregency"=>10),
                "5"=>array("name"=>"Nhân viên kỹ thuật","tytrong"=>"0","idregency"=>12),
            ), 
            "9" => array(
                "1"=>array("name"=>"Qũy cửa hàng","tytrong"=>"10","idregency"=>0),
                "2"=>array("name"=>"Cửa hàng trưởng","tytrong"=>"17","idregency"=>11),
                "3"=>array("name"=>"Cửa hàng phó","tytrong"=>"13","idregency"=>14),
                "4"=>array("name"=>"Nhân viên bán hàng","tytrong"=>"30","idregency"=>10),
                "5"=>array("name"=>"Nhân viên kỹ thuật","tytrong"=>"30","idregency"=>12),
            ), 
			"10" => array(
                "1"=>array("name"=>"Qũy cửa hàng","tytrong"=>"10","idregency"=>0),
                "2"=>array("name"=>"Cửa hàng trưởng","tytrong"=>"13","idregency"=>11),
                "3"=>array("name"=>"Cửa hàng phó","tytrong"=>"22","idregency"=>14),
                "4"=>array("name"=>"Nhân viên bán hàng","tytrong"=>"22","idregency"=>10),
                "5"=>array("name"=>"Nhân viên kỹ thuật","tytrong"=>"23","idregency"=>12),
            ),
			"11" => array(
                "1"=>array("name"=>"Qũy cửa hàng","tytrong"=>"10","idregency"=>0),
                "2"=>array("name"=>"Cửa hàng trưởng","tytrong"=>"12","idregency"=>11),
                "3"=>array("name"=>"Cửa hàng phó","tytrong"=>"28","idregency"=>14),
                "4"=>array("name"=>"Nhân viên bán hàng","tytrong"=>"25","idregency"=>10),
                "5"=>array("name"=>"Nhân viên kỹ thuật","tytrong"=>"25","idregency"=>12),
            ),
             
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function getCTTargetPk($id){
        $array = array(
            "4" => array(
                "1"=>array("name"=>"Qũy cửa hàng","tytrong"=>"0","idregency"=>0),
                "2"=>array("name"=>"Cửa hàng trưởng","tytrong"=>"15","idregency"=>11),
                "3"=>array("name"=>"Cửa hàng phó/Trưởng kỹ thuật","tytrong"=>"10","idregency"=>14),
                "4"=>array("name"=>"Nhân viên bán hàng","tytrong"=>"75","idregency"=>10),
                "5"=>array("name"=>"Nhân viên kỹ thuật","tytrong"=>"0","idregency"=>12),
            ), 
            "5" => array(
                "1"=>array("name"=>"Qũy cửa hàng","tytrong"=>"0","idregency"=>0),
                "2"=>array("name"=>"Cửa hàng trưởng","tytrong"=>"15","idregency"=>11),
                "3"=>array("name"=>"Cửa hàng phó/Trưởng kỹ thuật","tytrong"=>"20","idregency"=>14),
                "4"=>array("name"=>"Nhân viên bán hàng","tytrong"=>"65","idregency"=>10),
                "5"=>array("name"=>"Nhân viên kỹ thuật","tytrong"=>"0","idregency"=>12),
            ), 
            "6" => array(
                "1"=>array("name"=>"Qũy cửa hàng","tytrong"=>"0","idregency"=>0),
                "2"=>array("name"=>"Cửa hàng trưởng","tytrong"=>"15","idregency"=>11),
                "3"=>array("name"=>"Cửa hàng phó/Trưởng kỹ thuật","tytrong"=>"30","idregency"=>14),
                "4"=>array("name"=>"Nhân viên bán hàng","tytrong"=>"55","idregency"=>10),
                "5"=>array("name"=>"Nhân viên kỹ thuật","tytrong"=>"0","idregency"=>12),
            ),  
            "7" => array(
                "1"=>array("name"=>"Qũy cửa hàng","tytrong"=>"10","idregency"=>0),
                "2"=>array("name"=>"Cửa hàng trưởng","tytrong"=>"27","idregency"=>11),
                "3"=>array("name"=>"Cửa hàng phó","tytrong"=>"0","idregency"=>14),
                "4"=>array("name"=>"Nhân viên bán hàng","tytrong"=>"63","idregency"=>10),
                "5"=>array("name"=>"Nhân viên kỹ thuật","tytrong"=>"0","idregency"=>12),
            ),  
            "8" => array(
                "1"=>array("name"=>"Qũy cửa hàng","tytrong"=>"0","idregency"=>0),
                "2"=>array("name"=>"Cửa hàng trưởng","tytrong"=>"30","idregency"=>11),
                "3"=>array("name"=>"Cửa hàng phó","tytrong"=>"20","idregency"=>14),
                "4"=>array("name"=>"Nhân viên bán hàng","tytrong"=>"50","idregency"=>10),
                "5"=>array("name"=>"Nhân viên kỹ thuật","tytrong"=>"0","idregency"=>12),
            ),
            "9" => array(
                "1"=>array("name"=>"Qũy cửa hàng","tytrong"=>"0","idregency"=>0),
                "2"=>array("name"=>"Cửa hàng trưởng","tytrong"=>"18","idregency"=>11),
                "3"=>array("name"=>"Cửa hàng phó","tytrong"=>"12","idregency"=>14),
                "4"=>array("name"=>"Nhân viên bán hàng","tytrong"=>"35","idregency"=>10),
                "5"=>array("name"=>"Nhân viên kỹ thuật","tytrong"=>"35","idregency"=>12),
            ),
			"10" => array(
                "1"=>array("name"=>"Qũy cửa hàng","tytrong"=>"0","idregency"=>0),
                "2"=>array("name"=>"Cửa hàng trưởng","tytrong"=>"17","idregency"=>11),
                "3"=>array("name"=>"Cửa hàng phó","tytrong"=>"23","idregency"=>14),
                "4"=>array("name"=>"Nhân viên bán hàng","tytrong"=>"30","idregency"=>10),
                "5"=>array("name"=>"Nhân viên kỹ thuật","tytrong"=>"30","idregency"=>12),
            ),
			"11" => array(
                "1"=>array("name"=>"Qũy cửa hàng","tytrong"=>"0","idregency"=>0),
                "2"=>array("name"=>"Cửa hàng trưởng","tytrong"=>"17","idregency"=>11),
                "3"=>array("name"=>"Cửa hàng phó","tytrong"=>"33","idregency"=>14),
                "4"=>array("name"=>"Nhân viên bán hàng","tytrong"=>"25","idregency"=>10),
                "5"=>array("name"=>"Nhân viên kỹ thuật","tytrong"=>"25","idregency"=>12),
            ),
            
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    
    
    public function isBHVT($idregency){
        if($idregency ==48){
            return true;
        }
        return FALSE;
    }
    public function isBGD($idregency){
        if($idregency ==39 || $idregency ==40 || $idregency ==41){
            return true;
        }
        return FALSE;
    }
    public function getAllDaysInAMonth($year, $month, $day = 'Monday', $daysError = 3) {
    $dateString = 'first '.$day.' of '.$year.'-'.$month;

    if (!strtotime($dateString)) {
        throw new \Exception('"'.$dateString.'" is not a valid strtotime');
    }

    $startDay = new \DateTime($dateString);

//    if ($startDay->format('j') > $daysError) {
//        $startDay->modify('- 7 days');
//    }
    
    
  
    $days = array();

    while ($startDay->format('Y-m') <= $year.'-'.str_pad($month, 2, 0, STR_PAD_LEFT)) {
        $days[] = clone($startDay);
        $startDay->modify('+ 7 days');
    }

    return $days;
}

        public function getListProductID($id){
        $array = array(
            "0" => array(
                "id"=>"6020",
                "name"=>"Xiaomi"
            ), 
            "1" => array(
                "id"=>"7446",
                "name"=>"Infocus"
            ), 
            "2" => array(
                "id"=>"4281",
                "name"=>"Iphone 5s"
            ),  
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function getLocation($id){
        $array = array(
            "1" => "Công ty", 
            "2" => "Ngoài công ty", 
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function getCateHnamNew($id){
        $array = array(
            "3" => "Điện thoại", 
            "4" => "Phụ kiện", 
            "5" => "Máy tính bảng", 
            "6" => "Laptop", 
            "8" => "Đồng hồ thông minh", 
            "9" => "Nước hoa", 
            "10" => "Dịch vụ", 
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function getVT99($id){
        $array = array(
            "1" => "7789", 
            "2" => "5746", 
            "3" => "9478", 
            "4" => "6128", 
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function getDemo($id){
        $array = array(
            "1" => "7433", 
            "2" => "7718", 
            "3" => "8047", 
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function getCty99($id){
        $array = array(
            "1" => "8112", 
            "2" => "7268", 
            "3" => "7229", 
            "4" => "8006", 
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    
    
        public function getMenu($id){
        $array = array(
            "17" => "Đặt cọc",
            "12" => "Tra cứu",
            "16" => "Đồng bộ Fast", 
            "1" => "Đơn hàng online", 
            "2" => "Bán hàng", 
            "3" => "Thống kê",  
            "18" => "Báo cáo", 
            "4" => "Doanh số", 
            "5" => "Quản lý chi nhánh", 
            "6" => "Nhân viên,Target,Thưởng", 
            "7" => "Khác", 
//            "8" => "Kho", 
            "0" => "Tài liệu,Hd,Tìm kiếm", 
            "9" => "Tài chính kế toán", 
//            "10" => "Đặt hàng", 
            "11" => "Dịch vụ Bảo hành",
//            "13" => "Bán hàng V2",
//            "14" => "Bảo hành phụ kiện", 
            "15" => "Chat,Comment", 
            "20" => "Sim",
            "127" => "Ẩn",
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    
    public function isValid($data,$id,$msg,$status="") {
        $ret = array();
            if ($data == $status){
                $err['id'] = "$id";
                $err['msg'] = "$msg";
                $ret[] = $err;
            }
        
        return $ret;
    }
    
    public function syncAll($a)
        {
            $cache = GlobalCache::getCacheInstance($a);
            $cache->flushAll();
        }
    public function sync($key){
        $cache = GlobalCache::getCacheInstance('event');
        $cache->deleteCache($key);
        return true;
    }
    public function redirect($param=""){
        if($param != null){
            if(strpos($this->_identity->username, $param)){
                $this->redirect('/admin/home');
            }
        }
        return array();
    }
    public function getDay($daynow,$month){
        $endmonth = $this->add($daynow, $month);
        $time = strtotime($endmonth);
        $one = 24*60*60;
        $day = $time-$one;
        $_day = date("Y/m/d", $day);
        return $_day;
    }
    
    
    
    public function getDayC($days_created_end){
        $created_date               = substr($days_created_end, 0, 10);
        $created_day                = str_replace("/", "-", $created_date) . ' 00:00:00';
        return $created_day;
    }
    public function getDayE($days_created_end){
        $end_date = substr($days_created_end, 13, 10);
        $end_day = str_replace("/", "-", $end_date) . ' 23:59:59';
        return $end_day;
    }
    
    public function getModules($id){
        $array = array(
            "1" => "addon_admin", 
            "2" => "admin", 
            "3" => "hnam", 
            "4" => "user_admin", 
            "5" => "import", 
            "6" => "website_admin" 
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    
    public function getCateTargetHnamNew($id){
        $array = array(
            "1" => "Điện thoại khác", 
            "2" => "Điện thoại Apple", 
            "3" => "Phụ kiện", 
            "4" => "MTB khác", 
            "5" => "MTB Apple", 
            "6" => "Like new và SR", 
            "9" => "Demo", 
            "10" => "99%", 
            "11" => "VT 99%", 
            "7" => "Laptop", 
            "8" => "Đồng hồ thông minh", 
            "12" => "Nước hoa", 
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function getDemo99VTLN($id){
        $array = array(
            "4" => "Like new và SR", 
            "5" => "Demo", 
            "6" => "CTY 99%", 
            "7" => "VT 99%", 
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function getIsType($id){
        $array = array(
            "3" => "Mới", 
            "4" => "Cũ", 
            "5" => "Demo", 
            "6" => "CTY 99%", 
            "7" => "VT 99%", 
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    
    public function getGroupHnam($id){
        $array = array(
            "1" => "Apple", 
            "0" => "Khác"
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function getChiNhanh($id){
        $array = array(
            "12" => "Chi Nhánh 89", 
            "13" => "Chi Nhánh 148", 
            "15" => "Chi Nhánh 370", 
            "16" => "Chi Nhánh 654", 
            "17" => "Chi Nhánh 774", 
            "18" => "Chi Nhánh 67", 
            "19" => "Chi Nhánh 778", 
            "20" => "Chi Nhánh 191", 
            "21" => "Chi Nhánh 301", 
            "23" => "Chi Nhánh 492", 
            "24" => "Chi Nhánh 294", 
            "26" => "Chi Nhánh 253", 
            "28" => "Chi Nhánh 206", 
            "203" => "Chi Nhánh 1047", 
            "167" => "Chi Nhánh Saleonline", 
            
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function getApple($id){
        $array = array(
            "1" => "264", 
            "2" => "585", 
            "3" => "455", 
            "4" => "759", 
            "5" => "760",
            "6" => "622",
            "7" => "764", 
            "8" => "586", 
            "9" => "765", 
            "10" => "587", 
            "11" => "853", 
            "12" => "466", 
            "13" => "879", 
            "14" => "895", 
        );
        if ($id === null){
            return $array;
        }
        return $array[$id];
    }

    public function getPhoneApple($id){
        $array = array(
            "1" => "264", 
            "2" => "585", 
            "3" => "455", 
            "4" => "759", 
            "5" => "760",
            "6" => "853",
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function get_event_2510($id){
        $array = array(
            "9464" => "174300",
            "7877" => "258300",
            "10146" => "238000",
            "5531" => "279300",
            "5532" => "546000",
            "7225" => "343000",
            "11801" => "280000",
            "11776" => "315000",
            "11777" => "315000",
            "9705" => "343000",
            "11778" => "343000",
            "7878" => "385000",
            "11021" => "413000",
            "10210" => "441000",
            "10193" => "469000",
            "4813" => "525000",
            "5938" => "623000",
            "5937" => "903000",
            "9935" => "903000",
            "9936" => "973000",
            "5199" => "139300",
            "5484" => "174300",
            "8866" => "161000",
            "7913" => "174300",
            "7914" => "174300",
            "7916" => "174300",
            "7917" => "174300",
            "6771" => "258300",
            "5540" => "343000",
            "5493" => "384300",
            "7806" => "406000",
            "7925" => "413000",
            "7805" => "413000",
            "5494" => "553000",
            "7803" => "630000",
            "6120" => "623000",
            "7804" => "840000",
            "6118" => "693000",
            "7890" => "658000",
            "7926" => "903000",
            "7543" => "1400000",
            "10778" => "2030000",
            "8939" => "245000",
            "7822" => "413000",
            "7876" => "413000",
            "8865" => "525000",
            "10777" => "791000",
            "11263" => "1253000", 
            "9939" => "840000",
            "6348" => "693000",
            "6115" => "105000",
            "7800" => "84000",
            "10240" => "84000",
            "10241" => "77000",
            "10242" => "77000",
            "6375" => "119000",
            "8290" => "210000",
            "9965" => "140000",
            "10148" => "161000",
            "10455" => "140000",
            "8292" => "231000",
            "7958" => "182000",
            "9109" => "182000",
            "8851" => "196000",
            "9297" => "196000",
            "6242" => "315000",
            "6848" => "245000",
            "9043" => "315000",
            "8332" => "280000",
            "9978" => "315000",
            "8168" => "336000",
          );

        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function getTypeGHN($id){
        $array = array(
            "1" => "GHN", 
            "2" => "AĐR", 
//            "3" => "GHN-Tiết kiệm", 
            "4" => "VT", 
            "5" => "Shopee", 
            "6" => "Lotte", 
            "7" => "Accesstrade", 
            "8" => "Zalo", 
            "9" => "TIKI", 
            "10" => "Ahamove", 
            "41" => "GHN-VT", 
            "42" => "Nhân viên nội bộ", 
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function getmakh($id,$date){
        switch ($id) {
            case 1:
                $result="CT.GHN.07295";
                break;
            case 1:
                $result="CT.GHN.07295";
                break;
            case 8:
                $result="CT.GHN.07295";
                break;
            case 9:
                $result="CT.TKI.32909";
                break;
            default:
                $dnow = date("mY",  strtotime($date));
                $result ="KL.BL.".$dnow;
                break;
        }
        return $result;
    }
    public function getidghn($id){
        $array = array(
            "265" => "AĐR", 
            "266" => "Shopee", 
            "267" => "Lotte", 
            "269" => "Accesstrade", 
            "268" => "Zalo", 
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function getTabletApple($id){
        $array = array(
            "1" => "764", 
            "2" => "586", 
            "3" => "765", 
            "4" => "587", 
            "5" => "622"
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function getHnammobile($id){
        $array = array(
            "1" => "Điện thoại", 
            "2" => "Máy tính bảng",
            "3" => "Phụ kiện",
            "4" => "Laptop",
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function getNameDV($id){
        $array = array(
            "4" => "Gói cài đặt Ios", 
//            "5" => "Gói ZingVip",
            "11" => "Gói App game Android",
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function getPriceDV($id){
        $array = array(
            "4" => array(
                "1" => "50000", 
                "2" => "100000",
            ),
            "5" => array(
                "1" => "30000", 
                "3" => "80000",
                "6" => "150000", 
                "12" => "270000",
            ), 
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }

    

    public function getZingVip($id){
        $array = array(
            "1" => "30000", 
            "3" => "80000",
            "6" => "150000", 
            "12" => "270000",
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function getIOS($id){
        $array = array(
            "1" => "50000", 
            "2" => "100000",
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function getCategoryHnam($id){
        $array = array(
            "1" => "Điện thoại", 
            "2" => "Máy tính bảng",
            "3" => "Phụ kiện",
            "4" => "Sản phẩm khác",
            "5" => "Kho máy cũ",
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function getCateAAA($id){
        $array = array(
            "1" => "Điện thoại", 
            "2" => "Máy tính bảng",
            "3" => "Apple Watch",
            "4" => "Macbook",
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function getPhoneAAA($id){
        $array = array(
            "99.000" => "0 - 1.000.000", 
            "129.000" => "1.000.001 - 2.000.000", 
            "149.000" => "2.000.001 - 3.000.000", 
            "179.000" => "3.000.001 - 4.000.000", 
            "199.000" => "4.000.001 - 5.000.000", 
            "269.000" => "5.000.001 - 7.000.000", 
            "389.000" => "7.000.001 - 9.000.000", 
            "449.000" => "9.000.001 - 11.000.000", 
            "489.000" => "11.000.001 - 13.000.000", 
            "539.000" => "13.000.001 - 15.000.000", 
            "619.000" => "15.000.001 - 17.000.000", 
            "719.000" => "17.000.001 - 19.000.000", 
            "789.000" => "19.000.001 - 21.000.000", 
            "869.000" => "21.000.001 - 23.000.000", 
            "999.000" => "23.000.001 - 25.000.000", 
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function getTabletAAA($id){
        $array = array(
            "290.000" => "2.000.000 - 6.000.000", 
            "390.000" => "6.000.001 - 8.000.000", 
            "490.000" => "12.5.00.001 - 12.5.00.000", 
            "590.000" => "8.000.001 - 15.000.000", 
            "690.000" => "15.000.001 - 20.000.000", 
            "890.000" => "20.000.001 - 25.000.000", 
            "990.000" => "25.000.001 - 30.000.000", 
            "1.019.000" => "30.000.001 - 35.000.000", 
            
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function getAppleWatchAAA($id){
        $array = array(
            "890.000" => "5.000.000 - 15.000.000", 
            "1.090.000" => "15.000.001 - 20.000.000", 
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function getMacbookAAA($id){
        $array = array(
            "690.000" => "10.000.000 - 15.000.000", 
            "790.000" => "15.000.001 - 20.000.000", 
            "990.000" => "20.000.001 - 25.000.000", 
            "1.190.000" => "25.000.001 - 30.000.000", 
            "1.390.000" => "30.000.001 - 35.000.000", 
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function getCombo($id){
        $array = array(
            "1" => "6 Miếng dán màn hình", 
            
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    
    public function getWarranty($id){
        $array = array(
            "1" => "Gói 199.000", 
            "2" => "Gói 299.000",
            "3" => "Gói 449.000",
            "4" => "Gói 499.000",
            "5" => "Gói 549.000",
            "6" => "Gói 599.000",
            "7" => "Gói cài đặt game/app ios (50.000)",
            "8" => "Gói cài đặt game/app ios (100.000)",
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function getValWarranty($id){
        $array = array(
            "1" => "199000", 
            "2" => "299000",
            "3" => "449000",
            "4" => "499000",
            "5" => "549000",
            "6" => "599000",
            "7" => "50000",
            "8" => "100000",
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    
    public function getChargeCardBank($id){
        $array = array(
            "1" => "ACB (Ngân hàng TMCP Á Châu)", 
            "2" => "TP Bank (Tiên Phong)",
            "3" => "DongA Bank, DAF (Đông Á)",
            "4" => "SeABank (Đông Nam Á)",
            "5" => "ABBank (An Bình)",
            "6" => "BacABank (Bắc Á)",
            "7" => "VCCB (Bản Việt)",
            "8" => "Maritime Bank, MSB (Hàng Hải Việt Nam)",
            "9" => "Techcombank (Kỹ Thương Việt Nam)",
            "10" => "KienLongBank (Kiên Long)",
            "11" => "Nam A Bank (Nam Á)",
            "12" => "National Citizen Bank, NCB (Quốc Dân)",
            "13" => "VP Bank (Việt Nam Thịnh Vượng)",
            "14" => "HDBank (Phát triển TP HCM)",
            "15" => "Orient Commercial Bank, OCB (Phương Đông)",
            "16" => "Military Bank, MBB (Quận đội)",
            "17" => "PVcom Bank (Đại chúng)",
            "18" => "VIBBank, VIB (Quốc tế)",
            "19" => "SCB (Sài Gòn)",
            "20" => "Saigonbank, SGB (Sài Gòn Công Thương)",
            "21" => "SHBank, SHB (Sài Gòn-Hà Nội)",
            "22" => "Sacombank, STB (Sài Gòn Thương Tín)",
            "23" => "VietABank, VAB (Việt Á)",
            "24" => "BaoVietBank, BVB (Bảo Việt)",
            "25" => "VietBank (Việt Nam Thương Tín)",
            "26" => "Petrolimex Group Bank, PG Bank",
            "27" => "Eximbank, EIB (Xuất Nhập Khẩu Việt Nam)",
            "28" => "LienVietPostBank, LPB (Bưu Điện Liên Việt)",
            "29" => "Vietcombank,VCB (Ngoại thương Việt Nam)",
            "30" => "Vietinbank, CTG (Công Thương Việt Nam)",
            "31" => "BIDV, BID (Đầu tư và Phát triển Việt Nam)",
            "32" => "Agribank",
            "33" => "HSBC bank",
            "34" => "ANZ",
            "35" => "Citibank (Ngân hàng Citibank Việt Nam)",			
            "36" => "Shinhan Vietnam Bank Limited - SHBVN",
            "37" => "Ngân hàng Ưu tiên - Standard Chartered Bank",
            "38" => "Thẻ quốc tế - The Nature Conservancy",
            "39" => "Halifax bank",
            "40" => "MB PRIVATE",
            "41" => "YUANTA  BANK",
            "42" => "XÂY DỰNG VN VNCB",
            "43" => "Ngân hàng AIB",
            "44" => "PayPass",
            "45" => "RBC",
            "46" => "VRB",
            "47" => "JACCS",
            "48" => "BANKIA",
            "49" => "VIET CAPITAL",
            "50" => "Ngân hàng quân đội (MB)",
            "51" => "BANGKOK BANK",
            "52" => "MERITRUST",
            "53" => "societe generale",
            "54" => "TM TNHH MTV ĐẠI DƯƠNG (OCEAN BANK)",
            "55" => "LLOYDS BANK",
            "56" => "ngân hàng BIP",
            "57" => "Signature Bank",
            "58" => "DEUTSCHE BANK",
            "59" => "KASIKORNBANK",
            "60" => "CIMB BANK",
            "61" => "NGÂN HÀNG DBS",
            "62" => "NGÂN HÀNG IVB",
            "63" => "NGÂN HÀNG Nissan Banks",
            "64" => "ngân hàng Handelsbanken",
            "65" => "ngânngân hàng swedbank",
            "66" => "Ngân hàng Nordea Netbank",
            "67" => "Ngân hàng BPI",
            "68" => "Ngân hàng BNZ",
            "69" => "Ngân hàng ING",
            "70" => "Ngân hàng SKYPASS",
            "71" => "Ngân hàng EARTH BANK",
            "72" => "Ngân hàng CHARLES SCHWAB BANK",
            "73" => "Ngân hàng SAMSUNG",
            "74" => "Ngân hàng CHASE",
            "75" => "Ngân hàng BARCLAYCARD",
            "76" => "THE BANK OF TOKYO - MITSUBISHI UFJ",
            "77" => "Ngân hàng Nodrea",
            "78" => "Ngân hàng Rapid Rewards",
            "79" => "ngân hàng QANTAS",
            "80" => "ngân hàng payoneer",
            "81" => "ngân hàng EQUITY",
            "82" => "ngân hàng KB Bank",
            "83" => "commonwealth bank",
            "84" => "Premata Bank",
            "85" => "SHIDAX Bank",
            "86" => "ARQUIA BANCA",
            "87" => "CAIXABANK",
            "88" => "Home Credit",
            "89" => "FE Credit",
        ); 
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    
    public function getCatedHnammobile($id){
        $array = array(
            "1" => "Công ty", 
            "2" => "Hnam",
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function getCateVoucher($id){
        $array = array(
            "1" => "Máy cũ", 
            "2" => "Giảm tiền",
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function getTiento($id){
        $array = array(
            "0" => "", 
            "1" => "",//admin
            "2" => "",//tech
            "3" => "",//operator
            "4" => "mb_",//nhân viên bán hàng
            "5" => "qlkt_",//quản lý kỹ thuật
            "6" => "vote_",//quản lý cửa hàng
            "7" => "mbk_",//nhân viên kỹ thuật
            "8" => "bs_",//nhân viên kinh doanh
            "9" => "services_",// dịch vụ
            "10" => "", //hnamobile
            "11" => "acc_", // phụ kiện
            "12" => "support_",// nhân viên kho
            "13" => "mw_",// nhân viên kho
            "14" => "mkt_",// markerting
            "15" => "hr_",// Nhận sự
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
public function getActiveVoteID($slist, $request) {
        $ret = null;
        foreach($slist as $item) {
            $voteid = $item["userid"];
            if($request["voteid_$voteid"]==1) {
                $ret[$voteid] = $voteid;
            }
        }
        
        return $ret;
    }

    public function checkIp($storeid){
        $array = array(
            "12" => "210.245.33.223", // 89 Trần Quang Khải
//            "12" => "14.161.5.99", // 89 Trần Quang Khải
            "13" => "118.69.191.250", // 148 Nguyễn Cư Trinh
//            "13" => "14.161.40.124", // 148 Nguyễn Cư Trinh
            "14" => "127.0.0.1",
            "15" => "210.245.33.236", // 370 Lê Văn Sỹ
//            "15" => "14.161.30.149", // 370 Lê Văn Sỹ
            "16" => "118.69.72.114", //654 lê hồng phong
//            "16" => "14.161.45.48", //654 lê hồng phong
//            "17" => "14.161.27.164", //774 Nguyễn Trãi
            "17" => "210.245.33.190", //774 Nguyễn Trãi
            "18" => "210.245.33.215", // 67 Trần Quang Khải
//            "18" => "210.245.33.215", // 67 Trần Quang Khải
            "19" => "210.245.33.230", // 776 CMT8
//            "19" => "14.161.33.138", // 776 CMT8
            "20" => "210.245.33.198", // 191 Khánh hội
//            "20" => "14.161.15.98", // 191 Khánh hội
            "21" => "14.161.23.24", //301 võ văn tần
            "23" => "14.161.47.104", // 492 Ngô Gia Tự
            "24" => "210.245.33.216", // 294 Bạch đằng
//            "24" => "14.161.39.53", // 294 Bạch đằng
            "26" => "210.245.33.231", // 253 Quang Trung
//            "26" => "113.161.93.31", // 253 Quang Trung
            "28" => "14.161.2.41", // 206 Hoàng văn thụ
            "167" => "1.52.237.100,115.75.6.241,210.245.34.64", // 38/15 Nguyễn Giản Thanh
            "203" => "210.245.34.202", // 1047 Hồng Bàng
            "253" => "1.52.237.100,115.75.6.241,210.245.34.64", // 38/15 Nguyễn Giản Thanh
            "322" => "118.69.128.139", // 112 Võ Văn Ngân
            "539" => "210.245.33.230", // 778 Cách mạng tháng 8
            "443" => "210.245.32.14", // Vivo city
        );
        if ($storeid === null) {
            return $array;
        }
        return $array[$storeid];
    }
    public function checkBillVote($vote_id,$bill){
    }

    public function vn_str_filter($str){

       $unicode = array(

           'a'=>'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ',

           'd'=>'đ',

           'e'=>'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',

           'i'=>'í|ì|ỉ|ĩ|ị',

           'o'=>'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',

           'u'=>'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',

           'y'=>'ý|ỳ|ỷ|ỹ|ỵ',

           'A'=>'Á|À|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ẳ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',

           'D'=>'Đ',

           'E'=>'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',

           'I'=>'Í|Ì|Ỉ|Ĩ|Ị',

           'O'=>'Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',

           'U'=>'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',

           'Y'=>'Ý|Ỳ|Ỷ|Ỹ|Ỵ',

       );

      foreach($unicode as $nonUnicode=>$uni){

           $str = preg_replace("/($uni)/i", $nonUnicode, $str);

      }

       return $str;

   }


    public function insertSim($id_addon_user,$sim_number,$fullname,$vote_id){
        $_sim                       = Business_Addon_Sim::getInstance();
        $_addon_sim                 = Business_Addon_AddonSim::getInstance();
        $data_sim                   = array();
        foreach ($sim_number as $key => &$sim_items){
            if($sim_items != null || $sim_items != ""){
                $data_sim["id_addon_user"]      = $id_addon_user;
                $data_sim["sim_number"]         = $sim_items;
                $data_sim["createdate"]         = date('Y-m-d H:i:s');
                $data_sim["enabled"]            = 0;
                $data_sim["fullname"]           = $fullname;
                $data_sim["vote_id"]            = $vote_id; 
                $check_sim                      = $_sim->getDetailByCate($sim_items,$cateid = 719);
                if($check_sim != null){
                    $_addon_sim->insert($data_sim);
                    $_sim->_update($sim_items);
                }else{
                    $detail_sim                 = $_sim->getSimNotUsed($cateid=719);
                    $data_sim["sim_number"]     = $detail_sim["title"];
                    $_addon_sim->insert($data_sim);
                    $_sim->_update($detail_sim["title"]);
                }
            }

        }
//        return $data_sim;
    }
    
    public function getstatusGhn($id = null) {
        $array = array(
            "0" => "Chờ lấy hàng",
            "1" => "Đang lấy hàng",
            "2" => "Lưu kho",
            "3" => "Đang giao hàng",
            "4" => "Đã giao",
            "5" => "Đơn hàng đã được chuyển sang chờ thanh toán",
            "6" => "Kết thúc",
            "7" => "Đơn hàng được trả lại",
            "8" => "Hủy đơn hàng",
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function getGroupUser($id = null) {
        $array = array(
            "1" => "Đơn hàng online",
            "2" => "Bán hàng",
            "3" => "Thống kê",
            "6" => "Doanh số",
            "4" => "Nhân viên,Target,Thưởng",
            "5" => "Khác",
            "0" => "Tài liệu,Hướng dẫn,Tìm kiếm",
            "7" => "Quản lý chi nhánh"
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function getCatedCard($id = null) {
        $array = array(
//            "1" => "Máy POS Cty (Argibank)",
//            "2" => "Máy POS HNAM (Argibank)",
            "3" => "Máy POS Cty (Sacombank)",
            "4" => "Máy POS HNAM (Sacombank)",
            "5" => "Máy POS Cty (Vietcombank)",
            "6" => "Máy POS HNAM (Vietcombank)",
            "7" => "Máy POS PAYOO",
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function get_charge_card($id = null) {
        $array = array(
//            "1" => "0",
//            "2" => "0",
            "3" => "1131111",
            "4" => "1131121",
            "5" => "1131112",
            "6" => "1131122",
            "7" => "1131215",
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function getCatedPomotionOption($id = null) {
        $array = array(
            "1" => "Giảm %",
            "2" => "Giảm tiền"
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function getTimeSacombank($id) {
        $array = array(
            "6" => "06 Tháng",
            "12" => "12 Tháng"
        );
        if ($id===null) {
            return $array;
        }

        return $array[$id];                        
    }
    
    public function getApply($id = null) {
        $array = array(
            "0" => "Tất cả sản phẩm",
            "1" => "1 sản phẩm"
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function getTraGop($id = null) {
        $array = array( 
            "1" => "ACS",
            "2" => "FECredit",
            "3" => "HomeCredit",
            "5" => "HDBank", 
            "12" => "Payoo",
            "13" => "MIRAE ASSET",
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id]; 
    }
    public function getTraGop2($id = null) {
        $array = array(
            "1" => "ACS",
            "2" => "FECredit",
            "3" => "HomeCredit",
            "4" => "Sacombank",
            "5" => "HDBank",
            "6" => "VP Bank",
            "7" => "Nam Á Bank",
            "8" => "ANZ",
            "9" => "ShinhanBank",
            "10" => "TpBank",
            "11" => "SCB",
            "12" => "Payoo",
            "13" => "MIRAE ASSET",
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function get_tra_gop_ketoan($id = null) {
        $array = array(
            "1" => "1131213",
            "2" => "1131212",
            "3" => "1131211",
            "4" => "0",
            "5" => "1131214",
            "6" => "0",
            "12" => "1131215",//payoo
             "13" => "1131216",//MIRAE ASSET
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function getBank($id = null) {
        $array = array(
            "1" => "ACB (STK:72339559)",
            "2" => "Sacombank 1 (STK:060119776964)",
            "3" => "Vietcombank 1 (STK:0421000480996)",
            "4" => "Agribank (STK:1902201052803)",
            "5" => "HDBank",
            "6" => "Vietcombank 2 (STK:0071001312119)",
            "7" => "Sacombank 1 (STK:060032591984)",
//            "8" => "MBbank - (STK:0421000480996)",
            "9" => "Voucher V.I.P",
            "10" => "Công nợ",
            "11" => "NH QUÂN ĐỘI(MBBANK)-CN2 (STK:1031100788008)",
            "12" => "TPBank"
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function get_bank_chuyen_khoan($id = null) {
        $array = array(
            "1" => "112112",
            "2" => "112117",
            "3" => "112118",
            "4" => "112113",
            "5" => "0",
            "6" => "112121",
            "7" => "112122",
//            "8" => "112122",
            "9" => "0",
            "10" => "0",
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function ct_thumaycu($id = null) {
        $array = array(
            "1" => "CLOVER",
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function getHnamVT($id = null) {
        $array = array(
            "1" => "Cty",
            "2" => "Hnam",
            "3" => "BHMR",
            "4" => "Clover",
            "5" => "Khác",
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function get_type_ctkm_voucher($id = null) {
        $array = array(
            "0" => "Khác",
            "1" => "HVIP",
            "2" => "HMEMBER",
            "5" => "COMBO",
            "4" => "Voucher giảm giá (Mua sản phẩm với giá)",
            "6" => "Voucher A áp dụng voucher cho sản phẩm B",
            "7" => "Voucher tặng sim 3g",
            "3" => "QUÀ TẶNG",
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    
    public function getPromotionName($type){
        $array = array(
            "0" => "Tặng kèm",
            "1" => "Vocher khi mua thêm Điện thoại",
            "2" => "Vocher khi mua thêm Máy tính bảng",
            "3" => "Vocher khi mua thêm Phụ kiện",
            "4" => "Ưu đãi khi mua thêm sản phẩm với giá",
            "5" => "Giảm 10% tất cả các phụ kiện",
            "6" => "Vocher giảm tiền",
        );
        if ($type==null) return $array;
        return $array[$type];
    }
//    public function getSecurityName($username) {
//        $array = array(
//            "vote_89" => "Phan VĂn Tiến",
//            "vote_148" => "Hồng Văn Xuân",
//            "vote_654" => "Lê Ngọc Hiếu",
//            "vote_774" => "Võ Văn Thiện",
//            "vote_370" => "Nguyễn Xuân Hải",
//            "vote_43" => "Châu Cẩm Minh",
//            "vote_67" => "Châu Cẩm Minh",
//            "vote_776" => "Võ Văn Bé",
//            "vote_778" => "Võ Văn Bé",
//            "vote_191" => "Lê Đình Thanh",
//            "vote_301" => "Chú Thiện ",
//            "vote_294" => "Trần Tuấn Kiệt",
//            "vote_206" => "Lê Phứơc Mưng",
//            "vote_253" => "Nguyễn Anh Tuấn",
//            "vote_all" => "BV ADMIN"
//        );
//        return $array[$username];
//    }
    public function getPriceProductsByItemid($itemid){
        $price = 0;
        $detail = Business_Ws_ProductsItem::getInstance()->getDetail($itemid);
        $price = $detail["original_price"];
        if($price == 0){
            $price = $detail["price"];
        }
        return $price;
    }
    public function getOldPrice($itemid){
        $price = 0;
        $detail = Business_Ws_ProductsItem::getInstance()->getDetail($itemid);
        $price = $detail["oldprice"];
        return $price;
    }

    public function getTabletMenu() {
        /* get menu tablet */
        $depth = 1;
        $parentid = 0;
        $menuname = 'menu_tablet';
        $leftMenuTablet = Business_Helpers_Common::getMenuLev($depth, $parentid, $menuname);
        if (count($leftMenuTablet) > 0) {
            $i = 0;

            foreach ($leftMenuTablet as &$item) {
                $item["title_encode"] = htmlspecialchars($item["title"]);
                $cateid = $item['itemid'];
                if ($cateid == 191)
                    $pos = $_pos;
                $_pos++;


                $title = Business_Common_Utils::adaptTitleLinkURLSEO($item['title']);
                if ($_cateid == $cateid)
                    $active = 'active'; else
                    $active = '';
                switch ($cateid) {
                    default:
                        $item['link'] = SEOPlugin::getTabletLink($cateid, $title);
                        break;
                    case 190:
                        $item['link'] = Globals::getBaseUrl() . "loai-may-tinh-bang/apple-ipad-chinh-hang.html";
                        break;
                }

                if ($i++ == count($leftMenuTablet) - 1)
                    $item['class'] = 'class="' . $active . ' last"';
                else
                    $item['class'] = 'class=' . $active;
            }
        }
        return $leftMenuTablet;
    }
    public function getLaptopMenu() {
        /* get menu tablet */
        $depth = 1;
        $parentid = 0;
        $menuname = 'menu_laptop';
        $leftMenuLaptop = Business_Helpers_Common::getMenuLev($depth, $parentid, $menuname);
        if (count($leftMenuLaptop) > 0) {
            $i = 0;

            foreach ($leftMenuLaptop as &$item) {
                $item["title_encode"] = htmlspecialchars($item["title"]);
                $cateid = $item['itemid'];
                if ($cateid == 191)
                    $pos = $_pos;
                $_pos++;


                $title = Business_Common_Utils::adaptTitleLinkURLSEO($item['title']);
                if ($_cateid == $cateid)
                    $active = 'active'; else
                    $active = '';
            }
        }
        return $leftMenuLaptop;
    }
    public function getWatchMenu() {
        /* get menu tablet */
        $depth = 1;
        $parentid = 0;
        $menuname = 'menu_watch';
        $leftMenuLaptop = Business_Helpers_Common::getMenuLev($depth, $parentid, $menuname);
        if (count($leftMenuLaptop) > 0) {
            $i = 0;

            foreach ($leftMenuLaptop as &$item) {
                $item["title_encode"] = htmlspecialchars($item["title"]);
                $cateid = $item['itemid'];
                if ($cateid == 191)
                    $pos = $_pos;
                $_pos++;


                $title = Business_Common_Utils::adaptTitleLinkURLSEO($item['title']);
                if ($_cateid == $cateid)
                    $active = 'active'; else
                    $active = '';
            }
        }
        return $leftMenuLaptop;
    }
    public function getPrevDay2($countday,$cur_date){
        $time_on_day = $countday * 24 * 60 * 60;
        $cur_time = strtotime($cur_date);
        $previous_time = $cur_time - $time_on_day;
        $previous_date = date("Y-m-d", $previous_time);
        return $previous_date;
    }
    public function getNextDay2($countday,$cur_date){
        $time_on_day = $countday * 24 * 60 * 60;
        $cur_time = strtotime($cur_date);
        $next_time = $cur_time + $time_on_day;
        $next_date = date("Y-m-d", $next_time);
        return $next_date;
    }
    
    public function getPrevDay($cur_date){
        $time_on_day = 24 * 60 * 60;
        $cur_time = strtotime($cur_date);
        $previous_time = $cur_time - $time_on_day;
        $previous_date = date("Y-m-d", $previous_time);
        return $previous_date;
    }
    public function getNextDay($cur_date){
        $time_on_day = 24 * 60 * 60;
        $cur_time = strtotime($cur_date);
        $next_time = $cur_time + $time_on_day;
        $next_date = date("Y-m-d", $next_time);
        return $next_date;
    }
    public function add($date_str, $months)
    {
        
        $date = new DateTime($date_str);
        
        $start_day = $date->format('j');
        
        $date->modify("+{$months} month");
        $end_day = $date->format('j');
        if ($start_day != $end_day)
            $date->modify('last day of last month');

        return $date->format('Y-m-d');
    }
    public function pre_date($date_str, $months)
    {
        
        $date = new DateTime($date_str);
        
        $start_day = $date->format('j');
        
        $date->modify("-{$months} month");
        $end_day = $date->format('j');
        if ($start_day != $end_day)
            $date->modify('last day of last month');

        return $date->format('Y-m-d');
    }

    public function get3Months($cur_date){
        $time_on_day = 92*24 * 60 * 60;
        $cur_time = strtotime($cur_date);
        $next_time = $cur_time + $time_on_day;
        $next_date = date("Y-m-d", $next_time);
        return $next_date;
    }
    public function get1Months($cur_date){
        $time_on_day = 30*24 * 60 * 60;
        $cur_time = strtotime($cur_date);
        $next_time = $cur_time + $time_on_day;
        $next_date = date("Y-m-d", $next_time);
        return $next_date;
    }
    public function get_pre_by_date($cur_date,$pre){
        $time_on_day = $pre*24 * 60 * 60;
        $cur_time = strtotime($cur_date);
        $next_time = $cur_time - $time_on_day;
        $next_date = date("Y-m-d", $next_time);
        return $next_date;
    }
    public function getMonths($countmonth,$cur_date){
        
        $time_on_day = $countmonth * 30 * 24 * 60 * 60;
        $cur_time = strtotime($cur_date);
        $next_time = $cur_time + $time_on_day;
        $next_date = date("Y-m-d", $next_time);
        return $next_date;
    }
    public function get12Months($cur_date){
        $time_on_day = 365*24 * 60 * 60;
        $cur_time = strtotime($cur_date);
        $next_time = $cur_time + $time_on_day;
        $next_date = date("Y-m-d", $next_time);
        return $next_date;
    }

    public function getDayByMonth($thang){
        switch ($thang){
            case 1:
            case 3:
            case 5:
            case 7:
            case 8:
            case 10:
            case 12:
            {
                $thang = 31;
                break;
             }
            case 4:
            case 6:
            case 9:
            case 11:
            {
                $thang = 30;
                break;
            }
            case 2: 
            {
                if(date('Y') % 400 == 0 || (date('Y') % 4 == 0 && date('Y') % 4 !=0))
                    $thang = 29;
                else {
                    $thang =28;
                }
                    break;
            }
         default :
             break;
        }
        return $thang;
    }
    public function getDayByMonthYear($thang,$year){
        switch ($thang){
            case 1:
            case 3:
            case 5:
            case 7:
            case 8:
            case 10:
            case 12:
            {
                $thang = 31;
                break;
             }
            case 4:
            case 6:
            case 9:
            case 11:
            {
                $thang = 30;
                break;
            }
            case 2: 
            {
                if($year % 400 == 0 || $year % 4 == 0 && $year % 4 !=0)
                    $thang = 29;
                else {
                    $thang =28;
                }
                    break;
            }
         default :
             break;
        }
        return $thang;
    }
    public function VndText($amount)
    {
             if($amount <=0)
            {
                return $textnumber="Tiền phải là số nguyên dương lớn hơn số 0";
            }
            $Text=array("không", "một", "hai", "ba", "bốn", "năm", "sáu", "bảy", "tám", "chín");
            $TextLuythua =array("","nghìn", "triệu", "tỷ", "ngàn tỷ", "triệu tỷ", "tỷ tỷ");
            $textnumber = "";
            $length = strlen($amount);

            for ($i = 0; $i < $length; $i++)
            $unread[$i] = 0;

            for ($i = 0; $i < $length; $i++)
            {               
                $so = substr($amount, $length - $i -1 , 1);                

                if ( ($so == 0) && ($i % 3 == 0) && ($unread[$i] == 0)){
                    for ($j = $i+1 ; $j < $length ; $j ++)
                    {
                        $so1 = substr($amount,$length - $j -1, 1);
                        if ($so1 != 0)
                            break;
                    }                       

                    if (intval(($j - $i )/3) > 0){
                        for ($k = $i ; $k <intval(($j-$i)/3)*3 + $i; $k++)
                            $unread[$k] =1;
                    }
                }
            }

            for ($i = 0; $i < $length; $i++)
            {        
                $so = substr($amount,$length - $i -1, 1);       
                if ($unread[$i] ==1)
                continue;

                if ( ($i% 3 == 0) && ($i > 0))
                $textnumber = $TextLuythua[$i/3] ." ". $textnumber;     

                if ($i % 3 == 2 )
                $textnumber = 'trăm ' . $textnumber;

                if ($i % 3 == 1)
                $textnumber = 'mươi ' . $textnumber;


                $textnumber = $Text[$so] ." ". $textnumber;
            }

            //Phai de cac ham replace theo dung thu tu nhu the nay
            $textnumber = str_replace("không mươi", "lẻ", $textnumber);
            $textnumber = str_replace("lẻ không", "", $textnumber);
            $textnumber = str_replace("mươi không", "mươi", $textnumber);
            $textnumber = str_replace("một mươi", "mười", $textnumber);
            $textnumber = str_replace("mươi năm", "mươi lăm", $textnumber);
            $textnumber = str_replace("mươi một", "mươi mốt", $textnumber);
            $textnumber = str_replace("mười năm", "mười lăm", $textnumber);

            return ucfirst($textnumber." đồng");
    }
    public function VndText2($amount) {
        if ($amount <= 0) {
            return $textnumber = "Tiền phải là số nguyên dương lớn hơn số 0";
        }
        $Text = array("không", "một", "hai", "ba", "bốn", "năm", "sáu", "bảy", "tám", "chín");
        $TextLuythua = array("", "nghìn", "triệu", "tỷ", "ngàn tỷ", "triệu tỷ", "tỷ tỷ");
        $textnumber = "";
        $length = strlen($amount);

        for ($i = 0; $i < $length; $i++)
            $unread[$i] = 0;

        for ($i = 0; $i < $length; $i++) {
            $so = substr($amount, $length - $i - 1, 1);

            if (($so == 0) && ($i % 3 == 0) && ($unread[$i] == 0)) {
                for ($j = $i + 1; $j < $length; $j ++) {
                    $so1 = substr($amount, $length - $j - 1, 1);
                    if ($so1 != 0)
                        break;
                }

                if (intval(($j - $i ) / 3) > 0) {
                    for ($k = $i; $k < intval(($j - $i) / 3) * 3 + $i; $k++)
                        $unread[$k] = 1;
                }
            }
        }

        for ($i = 0; $i < $length; $i++) {
            $so = substr($amount, $length - $i - 1, 1);
            if ($unread[$i] == 1)
                continue;

            if (($i % 3 == 0) && ($i > 0))
                $textnumber = $TextLuythua[$i / 3] . " " . $textnumber;

            if ($i % 3 == 2)
                $textnumber = 'trăm ' . $textnumber;

            if ($i % 3 == 1)
                $textnumber = 'mươi ' . $textnumber;


            $textnumber = $Text[$so] . " " . $textnumber;
        }

        //Phai de cac ham replace theo dung thu tu nhu the nay
        $textnumber = str_replace("không mươi", "lẻ", $textnumber);
        $textnumber = str_replace("lẻ không", "", $textnumber);
        $textnumber = str_replace("mươi không", "mươi", $textnumber);
        $textnumber = str_replace("một mươi", "mười", $textnumber);
        $textnumber = str_replace("mươi năm", "mươi lăm", $textnumber);
        $textnumber = str_replace("mươi một", "mươi mốt", $textnumber);
        $textnumber = str_replace("mười năm", "mười lăm", $textnumber);

        return ucfirst($textnumber . " đồng");
    }
    public function getStoreName($userid){
        $storename  = '';
        $detail     = Business_Common_Users::getInstance()->getDetailById($userid);
        $storename  = $detail["storename"];
        return $storename;
    }
    public function getVoteId($userid){
        $vote_id  = '';
        $detail     = Business_Addon_VoteRotatory::getInstance()->getDetailByVoteId($userid);
        $vote_id  = $detail["vote_id"];
        return $vote_id;
    }
    public function getFullname($userid){
        $fullname = '';
        $detail = Business_Common_Users::getInstance()->getDetailById($userid);
        $fullname = $detail["fullname"];
        return $fullname;
    }
    public function getSortStorename($userid){
        $fullname = '';
        $detail = Business_Common_Users::getInstance()->getDetailById($userid);
        $fullname = $detail["abbreviation"];
        return $fullname;
    }
    public function getVoteName($userid){
        $username = '';
        $detail = Business_Common_Users::getInstance()->getDetailById($userid);
        $username = $detail["username"];
        return $username;
    }
    public function getSecurityName($userid){
         $security_name = '';
        $detail = Business_Common_Users::getInstance()->getDetailById($userid);
        $security_name = $detail["security_name"];
        if($detail ==NULL){
            $security_name = 'Bảo vệ Hnammobile';
        }
        return $security_name;
    }
//    public function getStoreName($username) {
//        $array = array(
//            "vote_148" => "148 Nguyễn Cư Trinh",
//            "vote_191" => "191 Khánh Hội",
//            "vote_206" => "206 Hoàng Văn Thụ",
//            "vote_253" => "253 Quang Trung",
//            "vote_294" => "294A Bạch Đằng",
//            "vote_301" => "301 Võ Văn Tần",
//            "vote_370" => "370A Lê Văn Sỹ",
//            "vote_654" => "654 Lê Hồng Phong",
//            "vote_67" => "67 Trần Quang Khải",
//            "vote_774" => "774 Nguyễn Trãi",
//            "vote_778" => "776 CMT8",
//            "vote_89" => "89 Trần Quang Khải",
//            "vote_all" => "Quản lý các cửa hàng"
//        );
//        if ($username==null) return $array;
//        return $array[$username];
//    }
    private function getSecurityNameExtra($username) {
        $array = array(
            "vote_89" => array("Phan VĂn Tiến"),
            "vote_148" => array("Hồng Văn Xuân"),
            "vote_654" => array("Lê Ngọc Hiếu"),
            "vote_774" => array("Võ Văn Thiện"),
            "vote_370" => array("Nguyễn Xuân Hải"),
            "vote_43"  => array("Châu Cẩm Minh"),
            "vote_67"  => array("Châu Cẩm Minh"),
            "vote_776" => array("Võ Văn Bé"),
            "vote_778" => array("Võ Văn Bé"),
            "vote_191" => array("Lê Đình Thanh"),
            "vote_301" => array("Chú Thiện "),
            "vote_294" => array("Trần Tuấn Kiệt"),
            "vote_206" => array("Lê Phứơc Mưng"),
            "vote_253" => array("Nguyễn Anh Tuấn","Phùng Văn Hiếu"),
            "vote_all" => array("BV ADMIN","BV ADMIN 2"),
        );
        return $array[$username];
    }
    public function getBonusByVote($username) {
         $array = array(
            "vote_89" => "2000",
            "vote_148" => "2500",
            "vote_654" => "2000",
            "vote_774" => "1500",
            "vote_370" => "2000",
            "vote_67" => "2250",
            "vote_778" => "2500",
            "vote_191" => "2250",
            "vote_301" => "2250",
            "vote_294" => "2250",
            "vote_206" => "2500",
            "vote_253" => "2500",
            "vote_all" => "0"
        );
        return $array[$username];
    }
    public function getBonusByVote2($userid) {
        $bonus = 0;
        $months = date('m');
        $pDetail = Business_Addon_BounsHnam::getInstance()->getDetailByUserIdByDay($userid,$months);
        if($pDetail !=null){
            $bonus = $pDetail[0]["bouns_hnam"];
        }
        else{
            $bonus = 2250;
        }
        return $bonus;
    }
    public function getSecList($id = null) {
        $array = array(
            "2" => "Tốt",
            "1" => "Tạm được",
            "0" => "Không hài lòng"
        );
        if ($id === null) {
            return $array;
        }
        return $array[$id];
    }
    public function getHnamFrom($id) {
        $array = array(
            "0" => "Báo/internet",
            "1" => "SMS/Email",
            "2" => "Tờ rơi",
            "3" => "Bạn bè",
            "4" => "Khác"
        );
        if ($id===null) {
            return $array;
        }

        return $array[$id];                        
    }
    public function getDayCreate($created_date){
        $months = date('m');
            $count_days = $this->getDayByMonth($months);
            if($created_date == ' 00:00:00'){
                    $created_date = date('Y/m/01').' 00:00:00';
                }
        return $created_date;
    }
    public function getDayEndByMonths(){
        $end_date = '';
        $months = date('m');
        $count_days = $this->getDayByMonth($months);
        $end_date = date('Y/m/'.$count_days);
        return $end_date;
    }
    public function getDayEndByMonths2($months){
        $end_date = '';
        $count_days = $this->getDayByMonth($months);
        $end_date = date('Y-m-'.$count_days);
        return $end_date;
    }
    public function getDayEnd($end_date){
        $months = date('m');
            $count_days = $this->getDayByMonth($months);
            if($end_date == ' 23:59:59'){
                $end_date = date('Y/m/'.$count_days).' 23:59:59';
            }
        return $end_date;
    }
    public function getDayCreateView($created_date){
        $months = date('m');
            $count_days = $this->getDayByMonth($months);
            if($created_date == ''){
                    $created_date = date('Y/m/01');
                }
        return $created_date;
    }
    public function getDayEndView($end_date){
        $months = date('m');
            $count_days = $this->getDayByMonth($months);
            if($end_date == ''){
                $end_date = date('Y/m/'.$count_days);
            }
        return $end_date;
    }
    public function getDayCreated($months,$years){
        if($months < 10){
            $months = '0'.$months;
        }
        return date($years.'/'.$months.'/01');
    }
    public function getDayEndd($months,$years){
        if($months < 10){
            $months = '0'.$months;
        }
        $count_days = $this->getDayByMonth($months);
        return date($years.'/'.$months.'/'.$count_days);
    }
    public function getDayCreated2($months,$years){
        return date($years.'/'.$months.'/01');
    }
    public function getDayEndd2($months,$years){
        $count_days = $this->getDayByMonth($months);
        return date($years.'/'.$months.'/'.$count_days);
    }
    //cty_sales
    
    //cty_sales
    public function getCountProduct($userid,$months,$years){
        $list = Business_Common_CtySales::getInstance()->countProductsSalesById($userid, $months,$years);
                foreach ($list as $items){
                    $result = $items["sum(sum_numbers)"];
                }
        return $result;
    }
    
}

?>

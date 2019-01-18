<?php

/**
 * User Admin Banner Controller
 * @author: nghidv
 */
class User_Admin_Usedphone2Controller extends Zend_Controller_Action {
    private $_identity;
    
    private $_type = array(
        0 => array(//apple sieu HOT
            1 => 40, //thoi gian bao hanh: 0thang
            2 => 35, //thoi gian bao hanh: 3-6th
            3 => 30, //thoi gian bao hanh: 6-9th
            4 => 30, //thoi gian bao hanh: 9-12th
            5 => 25, //Su dung duoi 16-30 thang
            6 => 20 //Su dug duoi 1-15 ngay
        ),
        1 => array(//apple
            1 => 42, //thoi gian bao hanh: 0thang
            2 => 38, //thoi gian bao hanh: 3-6th
            3 => 33, //thoi gian bao hanh: 6-9th
            4 => 28, //thoi gian bao hanh: 9-12th
            5 => 28, //may moi Su dung duoi 16-30 thang
            6 => 25, //may moi Su dug duoi 1-15 ngay
            7 => 5, //may cu duoi 15 ngay
            8 => 10, //may cu - su dung sau 15 ngay
            9 => 15, //het bao hanh
        ),
        2 => array(//nhanh, moi ra mắt
            1 => 50, //thoi gian bao hanh: 0thang
            2 => 45, //thoi gian bao hanh: 3-6th
            3 => 40, //thoi gian bao hanh: 6-9th
            4 => 30, //thoi gian bao hanh: 9-12th
            5 => 20, //may moi - Su dung duoi 16-30 thang
            6 => 15, //may moi - Su dug duoi 1-15 ngay
        ),
        3 => array(//binh thuong
            1 => 55, //thoi gian bao hanh: 0thang
            2 => 47, //thoi gian bao hanh: 3-6th
            3 => 40, //thoi gian bao hanh: 6-9th
            4 => 35, //thoi gian bao hanh: 9-12th
            5 => 20, //may moi - Su dung duoi 16-30 thang
            6 => 15, //may moi - Su dung duoi 1-15 ngay
        ),
        4 => array(//cham
            1 => 60, //thoi gian bao hanh: 0thang
            2 => 50, //thoi gian bao hanh: 3-6th
            3 => 45, //thoi gian bao hanh: 6-9th
            4 => 37, //thoi gian bao hanh: 9-12th
            5 => 20, //Su dung duoi 16-30 thang
            6 => 15, //Su dug duoi 1-15 ngay
        ),
        5 => array(//apple sieu HOT
            1 => 40, //thoi gian bao hanh: 0thang
            2 => 35, //thoi gian bao hanh: 3-6th
            3 => 30, //thoi gian bao hanh: 6-9th
            4 => 30, //thoi gian bao hanh: 9-12th
            5 => 25, //Su dung duoi 16-30 thang
            6 => 20 //Su dug duoi 1-15 ngay
        ),
		6 => array(//apple - không giá web
            1 => 35, //thoi gian bao hanh: 0thang
            2 => 31, //thoi gian bao hanh: 3-6th
            3 => 28, //thoi gian bao hanh: 6-9th
            4 => 23, //thoi gian bao hanh: 9-12th
            5 => 35, //may moi Su dung duoi 16-30 thang
            6 => 35, //may moi Su dug duoi 1-15 ngay
        ),
    );
	
	private $_type_used = array(
       
        1 => array(//apple
            1 => 15, //thoi gian bao hanh: 0thang
            2 => 15, //thoi gian bao hanh: 3-6th
            3 => 33, //thoi gian bao hanh: 6-9th
            4 => 28, //thoi gian bao hanh: 9-12th
            5 => 10, //Su dung duoi 16-30 thang
            6 => 5, //Su dug duoi 1-15 ngay            
        ),
        2 => array(//nhanh, moi ra mắt
            1 => 50, //thoi gian bao hanh: 0thang
            2 => 45, //thoi gian bao hanh: 3-6th
            3 => 40, //thoi gian bao hanh: 6-9th
            4 => 30, //thoi gian bao hanh: 9-12th
            5 => 20, //may moi - Su dung duoi 16-30 thang
            6 => 15, //may moi - Su dug duoi 1-15 ngay
        ),
        3 => array(//binh thuong
            1 => 60, //thoi gian bao hanh: 0thang
            2 => 50, //thoi gian bao hanh: 3-6th
            3 => 45, //thoi gian bao hanh: 6-9th
            4 => 40, //thoi gian bao hanh: 9-12th
            5 => 20, //may moi - Su dung duoi 16-30 thang
            6 => 15, //may moi - Su dung duoi 1-15 ngay
        ),
        4 => array(//cham
            1 => 75, //thoi gian bao hanh: 0thang
            2 => 65, //thoi gian bao hanh: 3-6th
            3 => 60, //thoi gian bao hanh: 6-9th
            4 => 55, //thoi gian bao hanh: 9-12th
            5 => 20, //Su dung duoi 16-30 thang
            6 => 15, //Su dug duoi 1-15 ngay
        ),
        
    );
	
    //Vỏ
    private $_canmop = array(
        1 => 0,
        2 => 10,
        3 => 25,
        4 => 35,
        //5 => 18,//backup
    );
	
	private $_canmop_apple_ko_gia = array(
        1 => 0,
        2 => 8,
        3 => 16,
        4 => 21,
        //5 => 18,//backup
    );
	
	 private $_canmop_used = array(
        1 => 18,
        2 => 27,
        3 => 35,
        4 => 40,
        //5 => 18,//backup
    );
	
	//vỏ android
	private $_canmop_android = array(
        1 => 0,
        2 => 7,
        3 => 18,
        4 => 28,
        //5 => 18,//backup
    );
	
	//vỏ android
	private $_canmop_android_used = array(
        1 => 0,
        2 => 5,
        3 => 15,
        4 => 20,
        //5 => 18,//backup
    );
	
    //man hình
    private $_tray = array(
        1 => 0,
        2 => 3, //tray nhe
        3 => 5, //tray nang
        4 => 10, //tray qua nang
    );
	
	private $_tray_apple_ko_gia = array(
        1 => 0,
        2 => 3, //tray nhe
        3 => 5, //tray nang
        4 => 15, //tray qua nang
    );
	
	private $_tray_used = array(
        1 => 0,
        2 => 3, //tray nhe
        3 => 5, //tray nang
        4 => 20 //tray qua nang
    );
	
	//man hình android
    private $_tray_android = array(
        1 => 0,
        2 => 5, //tray nhe
        3 => 15, //tray nang
        4 => 100 //tray qua nang
    );
	
	private $_tray_android_used = array(
        1 => 0,
        2 => 5, //tray nhe
        3 => 15, //tray nang
        4 => 100 //tray qua nang
    );
	
    private $_repair = array(
        1 => 0, //máy còn bảo hành, chưa sửa chữa
        2 => 0, //máy đã qua sửa chữa bởi bảo hành
        3 => 0 //máy đã sữa chưa dịch vụ bên ngoài
    );

    public function init() {
        BlockManager::setLayout('appbh');
        $auth = Zend_Auth::getInstance();
        $this->_identity = (array) $auth->getIdentity();
    }
    
    public function saveUploadAction() {
		$this->_helper->viewRenderer->setNoRender();
		$this->view->layout()->disableLayout();
		$upload_name = "upload_pic_";
		$err=0;
		$key = strip_tags($this->_request->getParam("upload_key"));
		//precheck 
		for($i=1; $i<=5; $i++) {
			$img_name = $upload_name . $i;
			if($_FILES[$img_name] != null && $_FILES[$img_name]['size'] > 0) {
				$tmp_name = $_FILES[$img_name]['tmp_name'];
				$imgDetail = getimagesize($tmp_name);
				if (strpos($imgDetail["mime"], "image")===false) {
					$res["msg"][] = "Vui lòng nhập đúng định dạng file ảnh vị trí $i.";
					$err=1;
					break;
				}				
			}
		}
		if ($err!=1) {
			for($i=1; $i<=5; $i++) {
				$img_name = $upload_name . $i;
				if($_FILES[$img_name] != null && $_FILES[$img_name]['size'] > 0) {
					$tmp_name = $_FILES[$img_name]['tmp_name'];
					$imgDetail = getimagesize($tmp_name);
					
					if (strpos($imgDetail["mime"], "image")===false) {
						$res["msg"][] = "Lỗi file, vui lòng tải đúng file ảnh vị trí $i.	";
					} else {
						
						$_upload_filename = explode("/", $imgDetail['mime']);
						$filename = md5(date("dmyHis").$tmp_name).".".$_upload_filename[1];
						
						$des_upload = STC_PATH."uploads/used/" . $filename;
						$ret = move_uploaded_file($tmp_name, "$des_upload");
						
						$data = array();
						$data["id_upload"] = $key;
						$data["filename"] = $filename;
						$data["user_id"] = $this->_identity["userid"];
						$data["vote_id"] = $this->_identity["parentid"];				
						$data["datetime"] = date("Y-m-d H:i:s");	 
						$_lid = Business_Addon_Usedphonehistory::getInstance()->insertUpload($data);
						$res["msg"][] = "Tải file lên thành công ảnh vị trí $i!!! " . $_lid;
						$res["img"][] = "<script>window.parent.$('#frmBG').show();window.parent.$('#upload_pic_block_$i').html(\"<a href='/uploads/used/$filename' target='_blank'><img src='/uploads/used/$filename' style='max-width:90%;max-height:100px;' /></a>\");</script>";
					}
					//$ret = move_uploaded_file($tmp_name, "$uploads_dir$name");
				}
			}
		}
		//var_dump($res);
		$ret_msg = implode("\\n", $res["msg"]);
		echo '<script>window.parent.alert("'.$ret_msg.'");</script>';
		echo implode($res["img"]);
	}
	
	
	public function getImeiDetailAction() {
		$this->_helper->viewRenderer->setNoRender();
		$this->view->layout()->disableLayout();
		
		$imei = strip_tags($this->_request->getParam("imei"));
		$pid = intval($this->_request->getParam("pid"));
		$auid = (int)$this->_request->getParam("auid");
                $imeiData = Business_Addon_UsersProducts::getInstance()->getLastSoldImei($imei, $pid);
		if($auid>0){
                    $imeiData = Business_Addon_UsersProducts::getInstance()->get_detail($auid);
                }
		
		//var_dump($imei, $imeiData);die();
		//máy cũ hay mới, apple hay không
		
		$isUsed=0;
		$isApple=0;
		if ($imeiData==null) {
			$isNull=1;
		} else {
			$isNull=0;
			if ($imeiData["cated_id"]==53) {
				$isUsed=1;
			}
			if (stripos(($imeiData["products_name"]), ("apple"))!==false) {
				$isApple=1;;
			}
		}

//cal used date of imei
		$createDate = $imeiData["create_date"];
 		$timeCreateDate = strtotime($imeiData["create_date"]);
		$timeCurrent = time();		
		$usedDay = intval(($timeCurrent - $timeCreateDate)/60/60/24);
		$warranty = $imeiData["warranty"];
		if ($warranty==0) {
			if ($imeiData["cated_id"]==53) {
				$totalDay = 3*30;
				$imeiData["warranty"] = 3;
			} else {
				$totalDay = 12*30;
				$imeiData["warranty"] = 12;
			}
		} else {
			$totalDay = $warranty*30;
		}
		
		$remainDay = $totalDay - $usedDay;
		if ($usedDay<15) {			
			$active=6;
		} else if ($usedDay<30) {
			$active=5;
		} else {
			if ($remainDay<=0) {
				$active=1;		
			} else if ($remainDay < 180) {
				$active=2;
			} else if ($remainDay < 270) {
				$active=3;
			} else {
				$active=4;
			}
			
		}
		
		//get price 
		$__itemid=$imeiData["products_id"];
		$__colorid=$imeiData["colorid"];
		
		$_url = "https://www.hnammobile.com/products/get-price-cost?itemid=$__itemid&colorid=$__colorid";
		try {
			$_productDetail = json_decode(file_get_contents($_url));
			$productPrice = intval(str_replace(".","",$_productDetail->price));
		
		}catch (Exception $e) {
			//$productPrice = $imeiData["products_price"];	
			$productPrice=0;
		}
		
		
		$data=array();
		$data["isUsed"]=$isUsed;
		$data["isApple"]=$isApple;
		$data["isNull"]=$isNull;		
		$data["price"]=	$productPrice;
		$data["name"]=$imeiData["products_name"];		
		$data["itemid"]=$imeiData["products_id"];		
		$data["colorid"]=$imeiData["colorid"];		
		$data["imei"]=$imei;	
		$data["usedDay"]=$usedDay;	
		$data["createDate"]=$imeiData["create_date"];
		$data["active"]=$active;
		$data["warranty"]=$imeiData["warranty"];
		$data["remain"]=$remainDay;
		
		echo json_encode($data);
		
	}
    
    public function testAction() {
        
        $usedid = 35;
        $used = Business_Addon_Usedphone::getInstance()->getDetail($usedid);
        $usedinfo = Business_Addon_Usedphoneinfo::getInstance()->getDetailByUsedID($usedid);
        
        echo $this->renderUsedDescription($used, $usedinfo);
    }
    
    public function updateInfo2Action() {
        //valid user
        $auth = Zend_Auth::getInstance();
        $identity = (array) $auth->getIdentity();
        $uname = $identity["username"];
            
        if (!($uname == 'tech' || $uname == 'tech2' || $uname == 'vote_all' || $uname == 'hnmobile')) {
            die("invalid access!!");
        }
        
        if ($this->_request->isPost()) {
            $this->_helper->viewRenderer->setNoRender();
            $this->view->layout()->disableLayout();
        
            $itemid = (int)$this->_request->getParam("itemid");
            $usedinfo = (int)$this->_request->getParam("usedid");
            $price = (int)$this->_request->getParam("newprice");
            
            //insert to history sell out
            $data=array();
            $data["addon_usedphone_info_id"] = $usedinfo;
            $data["itemid"] = $itemid;
            $data["price"] = $price;
            $data["datetime"] = date("Y-m-d H:i:s");
            
            Business_Addon_Usedphonehistory::getInstance()->insert($data);
            
            
            //update price
            $pdetail = Business_Ws_ProductsItem::getInstance()->getDetail($itemid);
            if ($pdetail["price"]>0) {
                $pdetail["price"] = $price;
            }
            if ($pdetail["original_price"]>0) {
                $pdetail["original_price"] = $price;
            }
            
            $productsid = $pdetail["productsid"];
            $cateid = $pdetail["cateid"];
                
            Business_Ws_ProductsItem::getInstance()->update($itemid, $productsid, $cateid, $pdetail);
            
            
            //result
            $data = array();
            $data["err"] = 0;
            $data["msg"] = "ok";
            echo json_encode($data);
            
        } else {
        
            $usedinfo = (int)$this->_request->getParam("id");
            $usedHistory = Business_Addon_Usedphonehistory::getInstance()->getListByUsedID($usedinfo);        

            if (count($usedHistory)>0) {
                $_item = $usedHistory[0];
                $itemid = $_item["itemid"];
                $pdetail = Business_Ws_ProductsItem::getInstance()->getDetail($itemid);

                $this->view->data = $pdetail;
                $this->view->usedid = $usedinfo;
                $this->view->history = $usedHistory;
                $this->view->hasItem = 1;
            } else {
                $this->view->hasItem = 0;
            }
        }
    }
    
    public function updateInfoAction() {
        
        //valid user
        $auth = Zend_Auth::getInstance();
        $identity = (array) $auth->getIdentity();
        $uname = $identity["username"];
            
        if (!($uname == 'tech' || $uname == 'tech2' || $uname == 'vote_all'  || $uname == 'hnmobile')) {
            die("invalid access!!");
        }
            
        if ($this->_request->isPost()){
            $this->_helper->viewRenderer->setNoRender();
            $this->view->layout()->disableLayout();
        
            $itemid = (int)$this->_request->getParam("itemid");
            $usedid = (int)$this->_request->getParam("usedid");

            //copy new product
            $lastid = $this->copyUsedAction($itemid);
                
            //update info
            $title = $this->_request->getParam("title");
            $price = $this->_request->getParam("price");
            $original_price = $this->_request->getParam("original_price");
            $warranty = $this->_request->getParam("warranty");
            $fullcontent = $this->_request->getParam("fullcontent");
            
            $pdetail = Business_Ws_ProductsItem::getInstance()->getDetail($lastid);            
            $pdetail["onstock"] = 1;
            $pdetail["quanlity"] = 1;
            $pdetail["title"] = $title;
            $pdetail["price"] = $price;
            $pdetail["newest"] = 0;
            $pdetail["super"] = 0;
            $pdetail["bestseller"] = 0;
            $pdetail["ishot"] = 0;
            $pdetail["highlight"] = 0;
            $pdetail["cheap"] = 0;
            $pdetail["highend"] = 0;
            $pdetail["smartphone"] = 0;
            $pdetail["newcome"] = 0;
            $pdetail["original_price"] = $original_price;
            $pdetail["warranty"] = $warranty;
            $pdetail["fullcontent"] = $fullcontent;
            $pdetail["bonus_company_full"] = '';
            $pdetail["bonus_mobile"] = '';
            
            $productsid = $pdetail["productsid"];
            $cateid = $pdetail["cateid"];
                
            Business_Ws_ProductsItem::getInstance()->update($lastid, $productsid, $cateid, $pdetail);
            
            //update to checked
            $usedinfo = Business_Addon_Usedphoneinfo::getInstance()->getDetailByUsedID($usedid);
            $usedinfo["checked"] = 1;
            $usedinfo["itemid"] = $lastid;
            Business_Addon_Usedphoneinfo::getInstance()->update($usedinfo["id"], $usedinfo);
            
            //update price to history list
            $hdata = array();
            $hdata["addon_usedphone_info_id"] = $usedinfo["id"];
            $hdata["itemid"] = $lastid;
            $hdata["price"] = ($original_price > 0 ? $original_price : $price);
            $hdata["datetime"] = date("Y-m-d H:i:s");
            
            $_history = Business_Addon_Usedphonehistory::getInstance();
            $_history->insert($hdata);
            
//            $this->addhdmh($usedid, $lastid); // thêm vào phần mua hàng
            
            //result
            $data = array();
            $data["err"] = 0;
            $data["msg"] = "ok";
            echo json_encode($data);

        } else {
            $usedid = (int)$this->_request->getParam("id");
            $used = Business_Addon_Usedphone::getInstance()->getDetail($usedid);
            $usedinfo = Business_Addon_Usedphoneinfo::getInstance()->getDetailByUsedID($usedid);
            $des =  $this->renderUsedDescription($used, $usedinfo);
            $this->view->des = $des;
            $this->view->usedid = $usedid;
            $this->view->checked = $usedinfo["checked"];
            
            $itemid = $used["itemid_tmp"];
            if ($itemid > 0){
                $this->view->hasItemid = $itemid;
                $pdetail = Business_Ws_ProductsItem::getInstance()->getDetail($itemid);
                
                if ($pdetail["original_price"]>0) {
                    $pdetail["original_price"] = $usedinfo["sellout_price"];
                } else if ($pdetail["price"]>0) {
                    $pdetail["price"] = $usedinfo["sellout_price"];
                }
                $this->view->data = $pdetail;
            } else {
                $this->view->hasItemid = 0;
            }
        }
    }
    
    public function customerAction() {
        
        $auth = Zend_Auth::getInstance();
        $identity = (array) $auth->getIdentity();
        $storename = $this->getStoreName($identity["username"]);
        $this->view->storename = $storename;
        $this->view->sid = $identity["username"];
        
        $this->view->usedphone_id = (int)$this->_request->getParam("id");
        $udetail = Business_Addon_Usedphone::getInstance()->getDetail($this->view->usedphone_id);
        $this->view->udetail = $udetail;
        $this->view->imei = $udetail["imei"];
        $this->view->price = $udetail["price"];
        $itemid = $udetail["itemid_tmp"];
        
        $this->view->price_voucher = self::getVoucherPrice($this->view->price, $udetail["type"], $udetail["type_voucher"], $itemid);
    }
    

    public function saveCustomerAction() {
        $this->_helper->viewRenderer->setNoRender();
        $this->view->layout()->disableLayout();
        
        $auth = Zend_Auth::getInstance();        
        $identity = (array) $auth->getIdentity();
            
        $fullname = $this->_request->getParam("fullname","");
        $email = $this->_request->getParam("email","");
        $address = $this->_request->getParam("address","");
        $phone = $this->_request->getParam("phone","");
        
            
        $usedphone_id = (int)$this->_request->getParam("usedphone_id");
        $udetail = Business_Addon_Usedphone::getInstance()->getDetail($usedphone_id);
        $itemid_tmp = $udetail["itemid_tmp"];
        $price = $udetail["price"];
        $imei = $udetail["imei"];
        $storeid = $identity["username"];
        $cur_datetime = date("Y-m-d H:i:s");

        $data = array();
        $data["storeid"] = $storeid;
        $data["addon_usedphone_id"] = $usedphone_id;
        $data["imei"] = $imei;
        $data["fullname"] = $fullname;
        $data["address"] = $address;
        $data["phone"] = $phone;
        $data["email"] = $email;
        $data["price"] = (int) self::getVoucherPrice($price, $udetail["type"], $udetail["type_voucher"], $itemid_tmp);
        $data["datetime"] = $cur_datetime;
        $data["datetime_expired"] = self::getExpiredTime($cur_datetime);
        $data["active"] = 1;       
        
        //fix price = 0
		//get last sold imei info
		$imeiInfo = Business_Addon_UsersProducts::getInstance()->getLastSoldImei($imei,$itemid_tmp);
                if($imeiInfo){
                    $check_price = $imeiInfo["products_price"] - $udetail["price"];
                    if ($check_price<100000) {	
                            $data["price"] = 0;
                    }
		}
                $detail_itemd =  Business_Ws_ProductsItem::getInstance()->get_detail($itemid_tmp);
                if($detail_itemd["productsid"]==4 || $detail_itemd["productsid"]==10){
                    $data["price"] = 0;
                }
                if($udetail["ct"] != 3){
                    $data["price"] = 0;
                }
		
        $ret = Business_Addon_Usedphonecus::getInstance()->insert($data);
        echo $ret;
    }
    
    public function listAction() {
        $keyword = "";
        $this->view->isAdmin = 0;
        $auth = Zend_Auth::getInstance();
        $identity = (array) $auth->getIdentity();
        $sid = $identity["username"];
        
        $full = (int) $this->_request->getParam("full",0);
        $this->view->keyword = $keyword = $this->_request->getParam("keyword");
        $from = $this->_request->getParam("from");
        $to = $this->_request->getParam("to");
        $cur_sid = $this->_request->getParam("vote_id");
        if ($cur_sid != null) {
            $sid = $cur_sid;
        } else {
            $sid = $identity["username"];
        }
//        echo "<pre>";
//        var_dump($cur_sid);
//        die();
            
        if ($from != null) {
            
            $froms = explode("/", $from);            
            $froms = array_reverse($froms);
            $from = implode("-",$froms);            
        }
        if ($to != null) {
            
            $tos = explode("/", $to);            
            $tos = array_reverse($tos);
            $to = implode("-",$tos);            
        }
        
        if ($sid == "vote_all" && $full==0) {
            $this->view->isAdmin = 1;
            $this->_helper->viewRenderer('list-admin');
            //update from & to date
		if ($to == "") {
			$to = date("Y-m-d");		
		}
            
            $toTime = strtotime($to);
		if ($from == ""){
			$fromTime = $toTime - (7 * 24 * 60 * 60);
            		$from = date("Y-m-d", $fromTime);		
		}	
            
        }
        $this->view->from = $from;
        $this->view->to = $to;
            
        if ($keyword != null) {
            $list = Business_Addon_Usedphonecus::getInstance()->getList("vote_all",$keyword, "", $from, $to);
        } else {
            $list = Business_Addon_Usedphonecus::getInstance()->getList($sid,$keyword, "", $from, $to);
        }
            
        foreach($list as &$item) {
            
            $used_id = $item["addon_usedphone_id"];
            $_detail = Business_Addon_Usedphone::getInstance()->getDetail($used_id);
            $_detail["storename"] = Business_Addon_Options::getInstance()->getStoreName($item["storeid"]);
            $item["detail"] = $_detail;
                
            $infos = Business_Addon_Usedphoneinfo::getInstance()->getDetailByUsedID($_detail["id"]);
                
            $_user = Business_Common_Users::getInstance()->getUserByUid($infos["storeid"]);
            $infos["storename"] = $this->getStoreName($_user["username"]);
            if ($infos["datetime_selling"]!=null) {
                $infos["datetime_selling_text"] = date("d/m/Y H:i:s", strtotime($infos["datetime_selling"]));
            }
            $item["hasinfo"] = true;
            //check info
            if ($infos["info"]==null || $infos["warranty"]==null) {
                $item["hasinfo"] = false;
            }
            
            $sellout_text = $this->getSelloutText($infos["sellout"]);
            $_infos = "<p><b>Tình trạng</b>: ".$infos["info"]."</p>";
            $_infos .= "<p><b>Bảo hành</b>: ".$infos["warranty"]."</p>";
            $_infos .= "<p><b>Phụ kiện</b>: ".$infos["accessory"]."</p>";
            $_infos .= "<p><b>Bán ra</b>: ".$sellout_text."</p>";
            
            $item["infos"] = $_infos;
            $item["detail2"] = $infos;
             
            $item["vouchers6"] = $_detail["vouchers6"];
            
            if (APP_ENV != "development") {
                $item["link_sendmail"] = "http://www.hnammobile.com/admin/user/usedphone2/sendmail2?id=".$used_cus_id;
            } else {
                $item["link_sendmail"] = "http://dev.hnamv4.test/admin/user/usedphone2/sendmail-dev?id=".$used_cus_id;
            }
        }
        $this->view->list = $list;
        
        //get store list
        $slist = Business_Addon_Options::getInstance()->getStoreName2($sid);
        $this->view->slist = $slist;
        
        $this->view->sid = $sid;
            
    }
    
    
    
    public function reportAction() {
        $keyword = "";
        $this->view->isAdmin = 0;
        $auth = Zend_Auth::getInstance();
        $identity = (array) $auth->getIdentity();
        $sid = $identity["username"];
        
        if ($sid == "vote_all") {
            $this->view->isAdmin = 1;
        } else {
            Business_Common_Utils::redirect("/admin/user/usedphone2/list");
        }
        $this->view->keyword = $keyword = $this->_request->getParam("keyword");
        $from = $this->_request->getParam("from");
        $to = $this->_request->getParam("to");
        $cur_sid = $this->_request->getParam("vote_id");
        if ($cur_sid != null) {
            $sid = $cur_sid;
        } else {
            $sid = $identity["username"];
        }
//        echo "<pre>";
//        var_dump($cur_sid);
//        die();
            
        if ($from != null) {
            $this->view->from = $from;
            $froms = explode("/", $from);            
            $froms = array_reverse($froms);
            $from = implode("-",$froms);            
        }
        if ($to != null) {
            $this->view->to = $to;
            $tos = explode("/", $to);            
            $tos = array_reverse($tos);
            $to = implode("-",$tos);            
        }
        if ($keyword != null) {
            $list = Business_Addon_Usedphonecus::getInstance()->getReport("vote_all",$keyword, "", $from, $to, "id desc");
            $list2 = Business_Addon_Usedphonecus::getInstance()->getReportUsed("vote_all",$keyword, "", $from, $to, "id desc");
        } else {
            $list = Business_Addon_Usedphonecus::getInstance()->getReport($sid,$keyword, "", $from, $to, "id desc");
            $list2 = Business_Addon_Usedphonecus::getInstance()->getReportUsed($sid,$keyword, "", $from, $to, "id desc");
        }
        
        echo "<pre>";
        var_dump($list, $list2);
        die();
            
        
        foreach($list as &$item) {
            $used_id = $item["addon_usedphone_id"];
            $_detail = Business_Addon_Usedphone::getInstance()->getDetail($used_id);
            $_detail["storename"] = Business_Addon_Options::getInstance()->getStoreName($item["storeid"]);
            $item["detail"] = $_detail;
        }
        $this->view->list = $list;
        //get store list
        $slist = Business_Addon_Options::getInstance()->getStoreName2($sid);
        $this->view->slist = $slist;
        
        
        $this->view->sid = $sid;
            
    }
    
    public function list2Action() {
        $keyword = "";
        $this->view->isAdmin = 0;
        $auth = Zend_Auth::getInstance();
        $identity = (array) $auth->getIdentity();
        $sid = $identity["username"];
        
        if ($sid == "vote_all") {
            $this->view->isAdmin = 1;
        } else {
            Business_Common_Utils::redirect("/admin/user/usedphone2/list");
        }
        $this->view->keyword = $keyword = $this->_request->getParam("keyword");
        $from = $this->_request->getParam("from");
        $to = $this->_request->getParam("to");
        $cur_sid = $this->_request->getParam("vote_id");
        if ($cur_sid != null) {
            $sid = $cur_sid;
        } else {
            $sid = $identity["username"];
        }
//        echo "<pre>";
//        var_dump($cur_sid);
//        die();
            
        if ($from != null) {
            $this->view->from = $from;
            $froms = explode("/", $from);            
            $froms = array_reverse($froms);
            $from = implode("-",$froms);            
        }
        if ($to != null) {
            $this->view->to = $to;
            $tos = explode("/", $to);            
            $tos = array_reverse($tos);
            $to = implode("-",$tos);            
        }
            
        if ($keyword != null) {
            $list = Business_Addon_Usedphonecus::getInstance()->getList("vote_all",$keyword, "", $from, $to, "id desc");
        } else {
            $list = Business_Addon_Usedphonecus::getInstance()->getList($sid,$keyword, "", $from, $to, "id desc");
        }
        
        
        foreach($list as &$item) {
            $used_id = $item["addon_usedphone_id"];
            $_detail = Business_Addon_Usedphone::getInstance()->getDetail($used_id);
            $_detail["storename"] = Business_Addon_Options::getInstance()->getStoreName($item["storeid"]);
            $item["detail"] = $_detail;
                
            
            $infos = Business_Addon_Usedphoneinfo::getInstance()->getDetailByUsedID($used_id);
                
            $sellout_text = $this->getSelloutText($infos["sellout"]);
//            $sellout_text = ".";
            $_infos = "<p><b>Tình trạng</b>: ".$infos["info"]."</p>";
            $_infos .= "<p><b>Bảo hành</b>: ".$infos["warranty"]."</p>";
            $_infos .= "<p><b>Phụ kiện</b>: ".$infos["accessory"]."</p>";
            $_infos .= "<p><b>Bán ra</b>: ".$sellout_text."</p>";
            
            $item["detail2"] = $infos;
            $item["infos"] = $_infos;
            $item["info"] = $infos;
        }
        $this->view->list = $list;
        //get store list
        $slist = Business_Addon_Options::getInstance()->getStoreName2($sid);
        $this->view->slist = $slist;
        
        $this->view->sid = $sid;
            
    }
    
    private function getSelloutText($selloutid=2) {
        $arr = array(
            0 => "Like new",
            1 => "Mới 100%",
            2 => "SR",
        );
        return $arr[$selloutid];
    }
    public function deleteAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $id = $this->_request->getParam("id");
        $active = $this->_request->getParam("active");
        
        $auth = Zend_Auth::getInstance();
        $identity = (array) $auth->getIdentity();
        $sid = $identity["username"];
        
        Business_Addon_Usedphonecus::getInstance()->delete($id,$active,$sid);
    }
    public function updateItemidAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $id = $this->_request->getParam("id");
        $itemid = $this->_request->getParam("itemid");
        $data = array(
            "itemid" => $itemid,
            "datetime_update" => date("Y-m-d H:i:s")
        );
            
        Business_Addon_Usedphoneinfo::getInstance()->updateByUsedID($id, $data);
    }
    
    public function updateAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $id = $this->_request->getParam("id");
        $price = $this->_request->getParam("price");
        $price = str_replace(",", "", $price);
        $data = array(
            "sellout_price" => $price,
            "datetime_update" => date("Y-m-d H:i:s")
        );
            
        Business_Addon_Usedphoneinfo::getInstance()->updateByUsedID($id, $data);
        
        //get phone info
        $pdetail = Business_Addon_Usedphone::getInstance()->getDetail($id);
        $phone_name = $pdetail["name"];
        $store_name = $this->getStoreName($pdetail["storeid"]);
        
        //get detail phone info
        $pdetail2 = Business_Addon_Usedphoneinfo::getInstance()->getDetailByUsedID($id);
        $info = $pdetail2["info"];
        $warranty = $pdetail2["warranty"];
        $accessory = $pdetail2["accessory"];
        //send email to updater
        
       
    }
    
    public function restoreAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $id = $this->_request->getParam("id");
        Business_Addon_Usedphonecus::getInstance()->restore($id);
    }
    public function insertExpenditure($addon_usedphone_id){
        $_expenditure   = Business_Addon_Expenditure::getInstance();
        $detail = Business_Addon_Usedphone::getInstance()->getDetail($addon_usedphone_id);
        $__option = Business_Addon_Options::getInstance();
        $data["hnamvt"]         = $detail["hnamvt"];
        $data["title"]          = $detail["name"];
        $data["money"]          = $detail["price"];
        $data["fullcontent"]    = $detail["note"];
        $data["vote_id"]        = $detail["storeid"];
        $data["enabled"]        = 1;
        $data["created_date"]   = date('Y-m-d H:i:s');
        $data["userphoneid"]   = $addon_usedphone_id;
        $_expenditure->insert($data);
        
        $auid =  (int)$detail["auid"];
        if($auid >0){
            $date1 = $__option->get1Months($detail["datetime"]);
            if(strtotime($date1) > strtotime('now')){
                $sdata["status2"]=1;
                Business_Addon_UsersProducts::getInstance()->update2($auid, $sdata);
            }
        }
    }

    public function printAction() {
        $used_cus_id = (int) $this->_request->getParam("id");
        $sendmail = (int) $this->_request->getParam("sendmail",0);
        $this->view->sendmail = $sendmail;
        
        $detail = Business_Addon_Usedphonecus::getInstance()->getDetail($used_cus_id);
        $usedid = $detail["addon_usedphone_id"];
        $used_info = Business_Addon_Usedphoneinfo::getInstance()->getDetailByUsedID($usedid);
        
        //nhóm thu khác bình thường không in voucher
        $used = Business_Addon_Usedphone::getInstance()->getDetail($used_cus_id);
		if ($used["ct"]>0) {
			$this->_helper->viewRenderer->setNoRender(true);
		}
		
		
        if ($used_info["info"] == null || $used_info["warranty"]==null) {
            echo "<script>alert('Vui lòng cập nhật thông máy để in voucher!!!');window.location='/admin/user/usedphone2/list'</script>";
            return;
        }
//        echo "<pre>";
//        var_dump($detail);
//        die();
//        Business_Addon_Usedphoneinfo::getInstance()->getDetail($id);
        $detail["datetime_expired"] = date("d/m/Y", strtotime($detail["datetime_expired"]));
        
        $this->view->detail = $detail;
       
            
//        if ($sendmail == 1) {  
            if (APP_ENV != "development") {
                $this->view->sendmail_url = "http://app.hnammobile.com/admin/user/usedphone2/sendmail?id=".$used_cus_id;
                
                    
            } else {
                $this->view->sendmail_url = "http://dev.app.hnammobile.com/admin/user/usedphone2/sendmail-dev?id=".$used_cus_id;
            }
//        }
//        $this->insertExpenditure($usedid);// phát sinh chi phí
//        $this->addhdmh($usedid); // thêm vào phần mua hàng
        
    }
    
    public function addhdmhAction(){
        $id = (int)  $this->_request->getParam("usesid");
        $itemid = (int)  $this->_request->getParam("lastid");
        
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        if($id >0){
            $this->addhdmh($id,$itemid);
            echo "susscess";
            die();
        }
    }
    public function addhdmh($id,$itemid){
        $__option = Business_Addon_Options::getInstance();
        if((int)$id >0){
            $detail_product = Business_Ws_ProductsItem::getInstance()->get_detail($itemid);
            $flag=0;
            if($detail_product["original_price"] >0){
               $flag=1; 
            }
            if($detail_product["productsid"] ==4){
               $flag=1; 
            }
            $data["isync"] = 1;
            $service =0;
            $data["service"] = 0;
            if($detail_product["productsid"]==10){
                $data["service"] = 1;
                $service =1; 
                $data["isync"] = 2;
            }
            $data["productsid"] = $detail_product["productsid"];
            
            $detail = Business_Addon_Usedphone::getInstance()->getDetail($id);
            
            $auid =  (int)$detail["auid"];
            if($auid >0){
                $date1 = $__option->get1Months($detail["datetime"]);
                if(strtotime($date1) > strtotime('now')){
                    $sdata["status2"]=1;
                    Business_Addon_UsersProducts::getInstance()->update2($auid, $sdata);
                }
            }
            
            $data["itemid"] = $itemid;
            $data["ct"] = $detail["ct"];
            $data["colorid"] = $detail["colorid"];
            $data["imei"] = trim($detail["imei"]);
            $data["storeid"] = $detail["storeid2"];
            $data["userid"] = $detail["userid"];
            $data["price"] = (int)$detail["price"];
            $data["datetime"] = date('Y-m-d H:i:s');
            $data["enabled"] = 1;
            
            $data["type"] = 3;
            $data["block"] = 1;
            $data["flag"] = $flag;
            $data["note"] = $detail["note"];
            $data["status_warehouse"] = $detail["status_warehouse"];
            
            
            $imei = trim($detail["imei"]);
            $colorid =$detail["colorid"];
            $__color = Business_Addon_ProductsColor::getInstance();
            $__option = Business_Addon_Options::getInstance();
            
            $array_pass_thu = $__option->array_pass_thu();
            if(in_array($itemid, $array_pass_thu)){
                $sdetail = $__color::getInstance()->get_detail_by_id_color2($itemid,$colorid);
            }else{
                $sdetail = $__color::getInstance()->get_detail_by_id_color($itemid,$colorid);
            }
            
            $ma_vt = $sdetail["code"];
            $data["ma_vt"] = $ma_vt;
            
            Business_Addon_Purchase::getInstance()->insert($data);
            $__storeid = (int)  $this->_identity["parentid"];
            $detail_store = Business_Addon_MappingStore::getInstance()->get_detail_by_storeid($__storeid);
            if($flag ==0){
                $k = ".K.OLDX";
            }else{
                $k = ".C.OLDX";
            }
            $__ma_kho = $detail_store["id_fast_bp"].$k;
            $ma_kho = trim($__ma_kho);
            $_query2= "INSERT INTO `hnam_live`.`app_mapping_product` (`id`, `id_product`, `id_color`, `id_material`,`id_warehouse`, `imei`, `type`) VALUES (NULL, '$itemid', '$colorid', '$ma_vt', '$ma_kho', '$imei',0)";
            if($service ==0 ){
                if($_query2 != null){
                    Business_Addon_UsersProducts::getInstance()->excute($_query2);
                }
            }
        }
    }

        public function prints6Action() {
        $used_cus_id = (int) $this->_request->getParam("id");
        
        $detail = Business_Addon_Usedphonecus::getInstance()->getDetail($used_cus_id);
        $detail["datetime_expired"] = date("d/m/Y", strtotime($detail["datetime_expired"]));
        
        $cusid = $detail["addon_usedphone_id"];
        $detail2 = Business_Addon_Usedphone::getInstance()->getDetail($cusid);
        if ($detail2["vouchers6"]==1) {
            $change_name = "Samsung Galaxy S6";
			$detail2["vprice"] = 1500000;
        }
        if ($detail2["vouchers6"]==2) {
            $change_name = "Samsung Galaxy S6 Edge";
			$detail2["vprice"] = 1700000;
        }
        $this->view->change_name = $change_name;
        $this->view->detail2 = $detail2;
        $this->view->detail = $detail;
    }
    
    
    
    public function sendmailAction() {
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $auth = Zend_Auth::getInstance();
        $identity = (array) $auth->getIdentity();
        //============= goi email list BHSC
        $parrent_id = $identity["parentid"];
        $bhsc = Business_Common_Variables::variable_get('mail_bhsc', '');
        $json = Zend_Json::decode($bhsc);
        
        if(count($json )>0)
        {
            $dataEmail = $json[$parrent_id];
            $tempEmail = explode(',',$dataEmail);
            //$sendCC[]='"thang.le@hnammobile.com"';	
            if(count($tempEmail)>1)
            {
            foreach ($tempEmail as $key => $value) {
                if($value !='')
                $sendCC[]= '"'.$value.'"';	
            }
            }else{
                if($dataEmail !='')
                   $sendCC[]= '"'.$dataEmail.'"';
            }

        }


        $used_cus_id = (int) $this->_request->getParam("id");
        $used_cus = Business_Addon_Usedphonecus::getInstance()->getDetail($used_cus_id);
        
        if ($used_cus != null) {
            $used_id = $used_cus["addon_usedphone_id"];
            
            
            $used = Business_Addon_Usedphone::getInstance()->getDetail($used_id);
		
            
            
			$upload_key = $used["upload_key"];
			$_header_img = '<p style="line-height:200%"><b>HÌNH ẢNH KÈM THEO</b></p><p style="line-height:120%"><b>Nhấn vào liên kết bên dưới để xem hình ảnh chi tiết</b></p>';
			if ($upload_key!="") {
				$list_upload_img = Business_Addon_Usedphonehistory::getInstance()->getListUpload($upload_key);
				
				foreach($list_upload_img as $lu) {
					$_tmp_img  = Globals::getBaseUrl() . "/uploads/used/" . $lu["filename"];
					$imgs[] = "<a  href='$_tmp_img' target='_blank'><img src='$_tmp_img' style='height:200px;' /></a>";
				}			
			}
			$imgs_upload = $_header_img.implode("", $imgs);
			
			
            $list_ct_thumay = Business_Addon_CTthumay::getInstance()->get_list_by_id();
            foreach ($list_ct_thumay as $th){
                $array_name_ct_thumay[$th["id"]] = $th["name"];
            }
            $check_sale=' (ngoài hệ thống)';
            if($used["check_sale"]==1){
                $check_sale=' (trong hệ thống)';
            }
            $name_ct_thumay = $array_name_ct_thumay[$used["ct"]].$check_sale;
            
			//get techname
			$techID = intval($used["userid_check"]);
			$uDetail = Business_Common_Users::getInstance()->getDetail($techID);
            $techFullname = $uDetail["fullname"];
            
            
            //fix send mail
            if ($used["type_voucher"]==1) {
                return;
            }
            
            $used_info = Business_Addon_Usedphoneinfo::getInstance()->getDetailByUsedID($used_id);

            $phone_name = $used["name"];
            $IMEI = $used["imei"];
            $product_cate_name = $this->getProductCateName($used_info["product_cate"]);
            $color = $used_info["color"];
            $list_store = Business_Common_Users::getInstance()->getListByUname(FALSE);
            foreach ($list_store as $___store){
                $___storename[$___store["userid"]] = $___store["storename"];
                $___address[$___store["userid"]] = $___store["address"];
                $___phone[$___store["userid"]] = $___store["phone"];
            }
            $store_name = $___storename[$used["storeid2"]] ."  ".$___address[$used["storeid2"]]."  ".$___phone[$used["storeid2"]];
            $price = number_format($used["price"])."đ";
            $voucher = number_format($used_cus["price"])."đ";
            $info = $used_info["info"];
            $warranty = $used_info["warranty"];
            $accessory = $used_info["accessory"];
            $sellout_text = $this->getSelloutText($used_info["sellout"]);
            if ((int) $used_info["sellout_price"] > 0) {
                $sellout_price_text = number_format($used_info["sellout_price"])."đ";
            }
            $datetime = date("d/m/Y H:i:s",strtotime($used["datetime"]));
            $url = Globals::getBaseUrl() . "admin/user/usedphone2/update-info?id=" . $used_id;
            
			//get customer info
			$cus_name = $used_cus["fullname"];
			$cus_phone = $used_cus["phone"];
			$cus_address = $used_cus["address"];
			
			$url_history_imei = 'http://app.hnammobile.com/statistical/history-imei?f_tabless_length=25&imei='.$IMEI;
			
$msg = <<<HTMLCONTENT
<p style="line-height:200%;color:#ff6600; font-size:14px; font-weight:bold">
        Hnammobile - Hệ thống bán lẻ điện thoại di động chính hãng
        </p>
        <p><b>Tên chương trình: </b> $name_ct_thumay</p>
<p style="line-height:200%">
	<b>THÔNG TIN SẢN PHẨM</b>
</p>
<p style="line-height:120%"><b>Ngày thu máy:</b> $datetime</p>
<p style="line-height:120%"><b>Nhân viên kỹ thuật kiểm tra máy:</b> $techFullname</p>
<p style="line-height:120%"><b>Tên máy:</b> $phone_name</p>
<p style="line-height:120%"><b>IMEI:</b> $IMEI</p>
<p style="line-height:120%"><b>Loại:</b> $product_cate_name</p>
<p style="line-height:120%"><b>Màu:</b> $color</p>
<p style="line-height:120%"><b>Giá thu vào:</b> $price</p>
<p style="line-height:120%"><b>Voucher phát hành:</b> $voucher</p>
<p style="line-height:120%"><b>Chi nhánh:</b> $store_name</p>
<p style="line-height:120%"><b>Tình trạng:</b> $info</p>
<p style="line-height:120%"><b>Bảo hành:</b> $warranty</p>
<p style="line-height:120%"><b>Phụ kiện:</b> $accessory</p>
<p style="line-height:120%"><b>Đề xuất bán ra:</b> $sellout_text</p>
<p style="line-height:120%"><b>Giá đề xuất bán ra:</b> $sellout_price_text</p>
<p style="line-height:200%"><b>Link đăng sản phẩm:</b> $url</p>
    
<p style="line-height:200%">
	<b>THÔNG TIN KHÁCH HÀNG</b>
</p>
<p style="line-height:120%"><b>Họ tên:</b> $cus_name</p>
<p style="line-height:120%"><b>Điện thoại:</b> $cus_phone</p>
<p style="line-height:120%"><b>Địa chỉ:</b> $cus_address</p>

<p style="line-height:200%">
	<b>THÔNG TIN LỊCH SỬ MÁY</b>
</p>
<p style="line-height:120%"><b>Nhấn vào liên kết bên dưới để xem lịch sử máy</b></p>
<p style="line-height:120%">$url_history_imei</p>

$imgs_upload


HTMLCONTENT;
    
	    $from = "khomaycu@hnammobile.com";
	    $displayname = "Hnammobile";
	    $replyto = $from;
	    
//	    $to = "nghi.dang@hnammobile.com";
//            $cc = array("dangvannghi37@gmail.com");

            $sid = $identity["username"];
        
//            $storeemail = Business_Helpers_Used::getStoreEmail($sid);
            if((int)$used["userid"] >0 ){
                $detail_user = Business_Common_Users::getInstance()->getDetail($used["userid"]);
            }
            $storeemail = trim($detail_user["email"]);


//            $storeemail = $___email[$used["storeid2"]];	
            //$to = "nghi.dang@hnammobile.com";		
            $detail_item = Business_Ws_ProductsItem::getInstance()->get_detail($used["itemid_tmp"]);
            $to = "duyhuy@hnammobile.com";
            		
            if($storeemail != NULL){
                $cc = array("kinhdoanh@hnammobile.com","nghi.dang@hnammobile.com","trongnhan@hnammobile.com","$storeemail","minhthinh@hnammobile.com");
            }else{
                $cc = array("kinhdoanh@hnammobile.com","nghi.dang@hnammobile.com","trongnhan@hnammobile.com","minhthinh@hnammobile.com");
            }
            if($detail_item["productsid"]==4){
                $cc = array("anhtuan@hnammobile.com","kinhdoanh.phukien@hnammobile.com","nghi.dang@hnammobile.com","trongnhan@hnammobile.com");
            }
           // $bcc="thang.le@hnammobile.com";				
            $subject = "Thông tin cập nhật máy cũ - " . $phone_name . " - " . $store_name;
            $body_html = $msg;  
            
            if(count($sendCC)>0)
                $cc= array_merge($cc, $sendCC);

            if($_REQUEST['d']==10)
            {
                var_dump($cc);
                exit;
            }
            if ($used_info["sendmail"]!=1) {
                                
                $result = Business_Common_Utils::sendEmailV4($from, $displayname, $replyto, $to, $subject, $body_html, $file_attached,"used", $cc,$bcc);
                if ($result=="") {
                    //copy product
//                    $newid = $this->copyUsedAction($pids);
//                    $used_info["itemid"] = $newid;
                    $used_info["sendmail"] = 1;
                    Business_Addon_Usedphoneinfo::getInstance()->update($used_info["id"], $used_info);
                    echo "done";
                }
                $this->addhdmh($used_id, $used["itemid_tmp"]);
                
                
                //không gửi tin nhắn cho imei nếu voucher = 0
                if ($used_cus["price"]>0) {	
                    $datetime_expired = date("d/m/Y", strtotime($used_cus["datetime_expired"]));
                    $sms_content = "Ma Voucher $used_cus_id. Tri gia 100.000d ap dung cho hoa don tren 500.000d.Han dung den $datetime_expired.LH 19002012";
                    $sms_token = md5($cus_phone."HNAMMAYCU");
                    $sms_url = "https://www.hnammobile.com/sms/may-cu?phone=$cus_phone&token=$sms_token&content=" . $sms_content;
                    Business_Common_Utils::getContentByCurl($sms_url);
                }
                
            }
            echo $result;
        }        
    }
    
    public function copyProductAction() {
        $itemid = $this->_request->getParam("itemid");
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        $this->copyUsedAction($itemid);
    }
    
    public function sendmail2Action() {
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        $used_cus_id = (int) $this->_request->getParam("id");
        $used_cus = Business_Addon_Usedphonecus::getInstance()->getDetail($used_cus_id);
        
        if ($used_cus != null) {
            $used_id = $used_cus["addon_usedphone_id"];
            $used = Business_Addon_Usedphone::getInstance()->getDetail($used_id);
            $list_ct_thumay = Business_Addon_CTthumay::getInstance()->get_list_by_id();
            foreach ($list_ct_thumay as $th){
                $array_name_ct_thumay[$th["id"]] = $th["name"];
            }
            $name_ct_thumay = $array_name_ct_thumay[$used["ct"]];
            
            $used_info = Business_Addon_Usedphoneinfo::getInstance()->getDetailByUsedID($used_id);
//            echo "<pre>";
//            var_dump($used, $used_cus, $used_info);
//            die();
//                
            $phone_name = $used["name"];
            $IMEI = $used["imei"];
            $product_cate_name = $this->getProductCateName($used_info["product_cate"]);
            $color = $used_info["color"];
//            $store_name = $this->getFullStorename($used["storeid"]);
            $list_store = Business_Common_Users::getInstance()->getListByUname(FALSE);
            foreach ($list_store as $store){
                $___storename[$store["userid"]] = $store["storename"]; 
                $___email[$store["userid"]] = $store["email"]; 
            }
            $store_name = $___storename[$used["storeid2"]];
            
            
            $price = number_format($used["price"])."đ";
            $voucher = number_format($used_cus["price"])."đ";
            $info = $used_info["info"];
            $warranty = $used_info["warranty"];
            $accessory = $used_info["accessory"];
            $sellout_text = $this->getSelloutText($used_info["sellout"]);
            if ((int) $used_info["sellout_price"] > 0) {
                $sellout_price_text = number_format($used_info["sellout_price"])."đ";
            }
            $datetime = date("d/m/Y H:i:s",strtotime($used["datetime"]));
            $income_text = $this->getSelloutText($used["type"]);
            $url = Globals::getBaseUrl() . "admin/user/usedphone2/update-info?id=" . $used_id;
$msg = <<<HTMLCONTENT
<p style="line-height:200%;color:#ff6600; font-size:14px; font-weight:bold">
        Hnammobile - Hệ thống bán lẻ điện thoại di động chính hãng
        </p>
<p style="line-height:120%">
<b>Thông tin cập nhật máy cũ</b>
</p>
<p><b>Tên chương trình: </b> $name_ct_thumay</p>
<p style="line-height:120%"><b>Ngày thu máy:</b> $datetime</p>
<p style="line-height:120%"><b>Tên máy:</b> $phone_name</p>
<p style="line-height:120%"><b>IMEI:</b> $IMEI</p>
<p style="line-height:120%"><b>Loại:</b> $product_cate_name</p>
<p style="line-height:120%"><b>Màu:</b> $color</p>
<p style="line-height:120%"><b>Giá thu vào:</b> $price</p>
<p style="line-height:120%"><b>Voucher phát hành:</b> $voucher</p>
<p style="line-height:120%"><b>Chi nhánh:</b> $store_name</p>
<p style="line-height:120%"><b>Tình trạng:</b> $info</p>
<p style="line-height:120%"><b>Bảo hành:</b> $warranty</p>
<p style="line-height:120%"><b>Phụ kiện:</b> $accessory</p>
<p style="line-height:120%"><b>Đề xuất bán ra:</b> $sellout_text</p>
<p style="line-height:120%"><b>Giá đề xuất bán ra:</b> $sellout_price_text</p>
<p style="line-height:200%"><b>Link đăng sản phẩm:</b> $url</p>
HTMLCONTENT;
	    $from = "khomaycu@hnammobile.com";
	    $displayname = "Hnammobile";
	    $replyto = $from;
	    
            $_sid = $used["storeid"];
        
//            $storeemail = Business_Helpers_Used::getStoreEmail($_sid);
            if((int)$used["userid"] >0){
                $detail_user = Business_Common_Users::getInstance()->getDetail($used["userid"]);
            }
            $storeemail = $detail_user["email"];
			//var_dump($storeemail, $msg);die();
			$detail_item = Business_Ws_ProductsItem::getInstance()->get_detail($used["itemid_tmp"]);
            if($detail_item["productsid"]==4){
                $to = "kinhdoanh.phukien@hnammobile.com";
            }else{
                $to = "duyhuy@hnammobile.com";
            }
            		
            if($storeemail != NULL){
                $cc = array("kinhdoanh@hnammobile.com","nghi.dang@hnammobile.com","trongnhan@hnammobile.com","$storeemail","minhthinh@hnammobile.com");
            }else{
                $cc = array("kinhdoanh@hnammobile.com","nghi.dang@hnammobile.com","trongnhan@hnammobile.com","minhthinh@hnammobile.com");
            }
            if($detail_item["productsid"]==4){
                $cc = array("nghi.dang@hnammobile.com","trongnhan@hnammobile.com");
            }			
            //$to = "nghi.dang@hnammobile.com";
            //$cc = array("dangvannghi37@gmail.com");
            $subject = "Thông tin cập nhật máy cũ - " . $phone_name . " - " . $store_name;
            $body_html = $msg;

            $result = Business_Common_Utils::sendEmailV4($from, $displayname, $replyto, $to, $subject, $body_html, $file_attached,"used", $cc);
            if ($result=="") {
                $used_info2 = Business_Addon_Usedphoneinfo::getInstance()->getDetailByUsedID($used_id);
                $used_info2["sendmail"] = 1;
                Business_Addon_Usedphoneinfo::getInstance()->update($used_info2["id"], $used_info2);
                echo "done";
            }
            echo $result;
        }
        
    }
    
    private function renderUsedDescription($used, $usedinfo) {
        if ($used == null || $usedinfo == null) return "";
//        $store_name = $this->getFullStorename($used["storeid"]);
        
        $list_store = Business_Common_Users::getInstance()->getListByUname(FALSE);
        foreach ($list_store as $store){
            $___storename[$store["userid"]] = $store["storename"]; 
            $___address[$store["userid"]] = $store["address"];
            $___phone[$store["userid"]] = $store["phone"];
        }
        $store_name = $___address[$used["storeid2"]]." <br/> Số điện thoại:".$___phone[$used["storeid2"]];
        $info = $usedinfo["info"];
        $warranty = $usedinfo["warranty"];
        $accessory = $usedinfo["accessory"];
        $color = $usedinfo["color"];
        
        $content = '
            <p><font size="4" color="#000099"><em><strong>Li&ecirc;n hệ 
            __STORE__
                để xem sản phẩm. </strong></em></font></p>
            <p>
                <font size="2"><strong>Màu:</strong> </font>
                __COLOR__
            </p>
            <p>
                <font size="2"><strong>Tình trạng:</strong> </font>
                __INFO__
            </p>
            <p>
                <font size="2"><strong>Bảo hành:</strong> </font>
                __WAR__
            </p>
            <p>
                <font size="2"><strong>Phụ kiện kèm theo:</strong> </font>
                __ACC__
            </p>
        ';
        
            
        $content = str_replace("__COLOR__", $color, $content);
        $content = str_replace("__STORE__", $store_name, $content);
        $content = str_replace("__INFO__", $info, $content);
        $content = str_replace("__WAR__", $warranty, $content);
        $content = str_replace("__ACC__", $accessory, $content);
            
        return $content;
        
    }
    
    public function sendmailDevAction() {
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        $used_cus_id = (int) $this->_request->getParam("id");
        $used_cus = Business_Addon_Usedphonecus::getInstance()->getDetail($used_cus_id);
        
        if ($used_cus != null) {
            $used_id = $used_cus["addon_usedphone_id"];
            $used = Business_Addon_Usedphone::getInstance()->getDetail($used_id);
            $list_ct_thumay = Business_Addon_CTthumay::getInstance()->get_list_by_id();
            foreach ($list_ct_thumay as $th){
                $array_name_ct_thumay[$th["id"]] = $th["name"];
            }
            $name_ct_thumay = $array_name_ct_thumay[$used["ct"]];
            
			//get techname
			$techID = intval($used["userid_check"]);
			$uDetail = Business_Common_Users::getInstance()->getDetail($techID);
            $techFullname = $uDetail["fullname"];
						
            $used_info = Business_Addon_Usedphoneinfo::getInstance()->getDetailByUsedID($used_id);
//            echo "<pre>";
//            var_dump($used, $used_cus, $used_info);
//            die();
//                
            $phone_name = $used["name"];
            $IMEI = $used["imei"];
            $product_cate_name = $this->getProductCateName($used_info["product_cate"]);
            $color = $used_info["color"];
//            $store_name = $this->getFullStorename($used["storeid"]);
            $list_store = Business_Common_Users::getInstance()->getListByUname(FALSE);
            foreach ($list_store as $store){
                $___storename[$store["userid"]] = $store["storename"]; 
            }
            $store_name = $___storename[$used["storeid2"]];
            
            
            
            $price = number_format($used["price"])."đ";
            $voucher = number_format($used_cus["price"])."đ";
            $info = $used_info["info"];
            $warranty = $used_info["warranty"];
            $accessory = $used_info["accessory"];
            $sellout_text = $this->getSelloutText($used_info["sellout"]);
            if ((int) $used_info["sellout_price"] > 0) {
                $sellout_price_text = number_format($used_info["sellout_price"])."đ";
            }
            $datetime = date("d/m/Y H:i:s",strtotime($used["datetime"]));
//            $income_text = $this->getSelloutText($used["type"]);
            $url = Globals::getBaseUrl() . "admin/user/usedphone2/update-info?id=" . $used_id;
$msg = <<<HTMLCONTENT
<p style="line-height:200%;color:#ff6600; font-size:14px; font-weight:bold">
        Hnammobile - Hệ thống bán lẻ điện thoại di động chính hãng
        </p>
<p style="line-height:120%">
<b>Thông tin cập nhật máy cũ</b>
</p>
<p><b>Tên chương trình: </b> $name_ct_thumay</p>
<p style="line-height:120%"><b>Ngày thu máy:</b> $datetime</p>
<p style="line-height:120%"><b>Nhân viên kỹ thuật kiểm tra máy:</b> $techFullname</p>
<p style="line-height:120%"><b>Tên máy:</b> $phone_name</p>
<p style="line-height:120%"><b>IMEI:</b> $IMEI</p>
<p style="line-height:120%"><b>Loại:</b> $product_cate_name</p>
<p style="line-height:120%"><b>Màu:</b> $color</p>
<p style="line-height:120%"><b>Giá thu vào:</b> $price</p>
<p style="line-height:120%"><b>Voucher phát hành:</b> $voucher</p>
<p style="line-height:120%"><b>Chi nhánh:</b> $store_name</p>
<p style="line-height:120%"><b>Tình trạng:</b> $info</p>
<p style="line-height:120%"><b>Bảo hành:</b> $warranty</p>
<p style="line-height:120%"><b>Phụ kiện:</b> $accessory</p>
<p style="line-height:120%"><b>Đề xuất bán ra:</b> $sellout_text</p>
<p style="line-height:120%"><b>Giá đề xuất bán ra:</b> $sellout_price_text</p>
<p style="line-height:200%"><b>Link đăng sản phẩm:</b> $url</p>
HTMLCONTENT;
	    $from = "khomaycu@hnammobile.com";
	    $displayname = "Hnammobile";
	    $replyto = $from;
	    
            $_sid = $used["storeid"];
            
            $storeemail = Business_Helpers_Used::getStoreEmail($_sid);
                
			//var_dump($storeemail, $msg);die();
//            $to = "duyhuy@hnammobile.com";			
//            $cc = array("xuanan@hnammobile.com","kinhdoanh@hnammobile.com","nghi.dang@hnammobile.com","","$storeemail");			
            $to = "nghi.dang@hnammobile.com";
            $cc = array("dangvannghi37@gmail.com");
            $subject = "Thông tin cập nhật máy cũ - " . $phone_name . " - " . $store_name;
            $body_html = $msg;

            if ($used_info["sendmail"]!=1) {
                $result = Business_Common_Utils::sendEmailV4($from, $displayname, $replyto, $to, $subject, $body_html, $file_attached,"used", $cc);
                if ($result=="") {
                    $used_info2 = Business_Addon_Usedphoneinfo::getInstance()->getDetailByUsedID($used_id);
                    $used_info2["sendmail"] = 1;
                    Business_Addon_Usedphoneinfo::getInstance()->update($used_info2["id"], $used_info2);
                    echo "done";
                }                
            }
            echo $result;
        }
        
    }
    
    public function infoAction() {
        $used_cus_id = (int) $this->_request->getParam("id");
        $this->view->used_cus_id = $used_cus_id;
        $detail = Business_Addon_Usedphonecus::getInstance()->getDetail($used_cus_id);
        $detail2 = Business_Addon_Usedphone::getInstance()->getDetail($detail["addon_usedphone_id"]);
        
        $this->view->detail2 = $detail2;
        $this->view->usedid = $detail["addon_usedphone_id"];
        
        if ($this->_request->isPost()) {
		//ini_set('display_errors', '1');
            $buy = $this->_request->getParam("buy", -1);
            $used_cus_id = $this->_request->getParam("used_cus_id");
            $product_cate = $this->_request->getParam("product_cate");
            $color = $this->_request->getParam("color");
            $info = $this->_request->getParam("info");
            $warranty = $this->_request->getParam("warranty");
            $accessory = $this->_request->getParam("accessory");
            $sellout = $this->_request->getParam("sellout");
            $sellout_price = $this->_request->getParam("sellout_price","");
            if ($sellout_price != "") {
                $sellout_price = str_replace(",", "", $sellout_price);
            }
            $uids = $this->_request->getParam("usedid");
            
            $data = array();
            $data["addon_usedphone_id"] = $uids;
            $data["product_cate"] = $product_cate;
            $data["color"] = $color;
            $data["info"] = $info;
            $data["warranty"] = $warranty;
            $data["accessory"] = $accessory;
            $data["sellout"] = $sellout;
            $data["sellout_price"] = $sellout_price;
            $data["buy"] = $buy;
            $data["datetime"] = date("Y-m-d H:i:s");
                
            $_detail = Business_Addon_Usedphoneinfo::getInstance()->getDetailByUsedID($uids);
            if ($_detail == null) {
                Business_Addon_Usedphoneinfo::getInstance()->insert($data);
            } else {
                unset($data["datetime"]);
                Business_Addon_Usedphoneinfo::getInstance()->update($_detail["id"], $data);
            }
            $this->view->ok = "1";
            $this->view->usedid2 = $used_cus_id;
        }
    }
    
    private static function getProductCateName($id) {
        if ($id == 1) {
            return "Công ty";
        } 
        return "Xách tay";
    }
    private static function getExpiredTime($datetime) {
        $range = 3 * 30 * 24 * 60 * 60;
        $time = strtotime($datetime) + $range;
        
        return date("Y-m-d 00:00:00", $time);
    }
    
    private static function getVoucherPrice($price, $type, $type_voucher=0, $itemid) {
        $pdetail = Business_Ws_ProductsItem::getInstance()->getDetail($itemid);
		
		if ($pdetail["productsid"]==4) {
			return 0; //hardcode voucher 0 for accessory
		}
		
		//hardcode voucher 100.000đ for all
		return 100000;
        
        if ($type==5 || $type_voucher==1 || $type_voucher==10) {
            //5 ==> like new khong phat hanh voucher
            //1==>thong tin tam cho chuong trinh samsung 6 khong phat hanh voucher
            //10 ==> khong phat hanh voucher
            return 0;
        }
        $price2 = $price * 10 / 100;
        if ($price2 > 1000000) {
            $price2 = 1000000;
            return $price2/2;
        }        
        $remain = $price2 % 100000;
        $price2 = ($price2 - $remain)/2;
        return $price2;
    }

    public function historyAction(){
//        $this->_helper->viewRenderer->setNoRender();
        $this->view->layout()->disableLayout();
        $__option = Business_Addon_Options::getInstance();
        $imei = $this->_request->getParam("imei", 0);
        $list = Business_Addon_Usedphone::getInstance()->getListByIMEI($imei);
        foreach($list as  &$item){
            $item["price"] = number_format($item["price"]);
            $item["storename"] = $this->getStoreName($item["storeid"]);
        }
        if($imei != NULL){
            $detail = Business_Addon_UsersProducts::getInstance()->getDetailByImes($imei);
        }
        if($detail != NULL){
            $date1 = $__option->getNextDay2(30, $detail["create_date"]);
            $detail["date1"] = $date1;
        }
        $this->view->detail = $detail;
        $this->view->listh = $list;
        $auid = (int)$this->_request->getParam("auid");
        $this->view->auid = $auid;
    }
    
    public function saveAction() {
        $this->_helper->viewRenderer->setNoRender();
        $this->view->layout()->disableLayout();

        $price_tmp = $this->_request->getParam("price", "");
        
        $name = $this->_request->getParam("name", "");
        
        $type = $this->_request->getParam("type", 0);
        $vouchers6 = $this->_request->getParam("vouchers6", 0);
        $type_voucher = $this->_request->getParam("type_voucher", 10);
        $hasvoucher = $this->_request->getParam($hasvoucher, 1);
        $storeid = $this->_request->getParam("storeid", 0);
        $pricekh = $this->_request->getParam("pricekh", 0);
        $colorid = $this->_request->getParam("colorid", 0);
        
        $imei = $this->_request->getParam("imei", 0);
        if($imei){
            $detail_ispay = Business_Addon_AddonPromotion::getInstance()->get_detail_by_imei_synced($imei);
            if($detail_ispay){
                $pricekh = $detail_ispay["return_price"];
            }
        }
            
        $itemid = $this->_request->getParam("itemid", 0);
        $__pid = (int)$this->_request->getParam("pid");
        $note = $this->_request->getParam("note");
        $hnamvt = (int)$this->_request->getParam("hnamvt");
        $auid = (int)  $this->_request->getParam("auid");
        $userid_check = (int)  $this->_request->getParam("tech");
        $ct = (int)  $this->_request->getParam("ct");
		
        $__option = Business_Addon_Options::getInstance();
		$data = array();	
        $ret = array();
        if($imei != NULL){
            $detail = Business_Addon_UsersProducts::getInstance()->get_detail_by_imes_not_dv($imei);
            $____slist2 = Business_Addon_FASTTONKHOIMEI::getInstance()->get_list_by_malo3($imei);
            if($____slist2 != NULL){
                $err["id"] = "imei";
                $err["msg"] = "IMEI này đã có trong kho của hệ thống HNAMOBILE. Vui lòng liên hệ IT kiểm tra.";
                $ret[] = $err;
            }
            
        }
        $data["check_sale"] =0;
        if($detail != NULL){
            $date1 = $__option->getNextDay2(30, $detail["create_date"]);
            $__date1 = strtotime($date1);
            $data["check_sale"] =1;
        }
        $this->view->detail = $detail;
        $now = strtotime('now');
        if((int)$colorid ==0){
                $err["id"] = "colorid";
                $err["msg"] = "Vui lòng chọn màu của sản phẩm.";
                $ret[] = $err;
            }
        
        if($auid==0){
            if($__date1 >$now){
                $err['id']      = "imei";
                $err['msg']     = "IMEI này trong 30 ngày đổi trả. vui lòng vào phần mềm bán hàng để thu....";                
                $ret[]          = $err;
            }
        }else{
            $ssdetail = Business_Addon_UsersProducts::getInstance()->getDetail($auid);
            $ppprice = $ssdetail["products_price"] - $ssdetail["money_voucher"];
            $pricekh = str_replace(",", "", $pricekh);
            if($pricekh == $ppprice){ // giá thu
                $data["active_target"] =0;
            }
            if($ssdetail==NULL){
                $err['id']      = "imei";
                $err['msg']     = "BILL đã bị thu nên không thể thu thêm lần nửa. Vui lòng kiểm tra lại.";
                $ret[]          = $err;
            }
        }
        if((int)$ct ==0){
            $err["id"] = "ct";
            $err["msg"] = "Vui lòng chọn chương trình.";
            $ret[] = $err;
        }
        $__pname = explode("--", $name);
        $itemid = $__pname[2];
        if((int) $itemid >0 && (int)$colorid >0){
//            $detail_product = Business_Addon_MappingProduct::getInstance()->get_detail_by_product_color($itemid,$colorid);
            $array_pass_thu = $__option->array_pass_thu();
            if(in_array($itemid, $array_pass_thu)){
                $detail_product = Business_Addon_ProductsColor::getInstance()->get_detail_by_id_color2($itemid,$colorid);
            }else{
                $detail_product = Business_Addon_ProductsColor::getInstance()->get_detail_by_id_color($itemid,$colorid);
            }
            if($detail_product["code"] ==NULL || trim($detail_product["code"]) ==""){
                $err['id']      = "name";
                $err['msg']     = "Sản phẩm này chưa có mã vật tư trong hệ thống hnammobile, vui lòng liên hệ IT để tạo mã vật tư.";
                $ret[]          = $err;
            }
            $___detail_item = Business_Ws_ProductsItem::getInstance()->getDetail($itemid);
        }
        if($__pid >0){
            $___detail_item = Business_Ws_ProductsItem::getInstance()->getDetail($__pid);
        }
        $ma_kho = (int)$this->_request->getParam("ma_kho");
        if($___detail_item["productsid"] !=4){
            $ma_kho=0;
        }
        if($___detail_item["productsid"] !=10){
            
            if($___detail_item["productsid"]==4 && $ma_kho ==0){
                $err['id']      = "ma_kho";
                $err['msg']     = "Phụ kiện vui lòng chọn kho khác C.OLDX";
                $ret[]          = $err;
            }
            $detail_mapping = Business_Addon_MappingProduct::getInstance()->get_detail_by_imei($imei);
            if($detail_mapping["follow_imei"]==1){
                $__tonkhoimei = Business_Addon_FASTTONKHOIMEI::getInstance();
                $detail_tonkho = $__tonkhoimei->get_detail_by_malo($imei,0); 
                if($detail_tonkho !=NULL){
                    $err['id']      = "name";
                    $err['msg']     = "Sản phẩm này đang tồn kho nên không thể mua sản phẩm này vào tiếp.";
                    $ret[]          = $err;
                }
            }
        }else{ // dịch vụ
            if($__date1 < $now){ // chặn 30 ngày thu dịch vụ
                $err['id']      = "imei";
                $err['msg']     = "Các gói dịch vụ quá 30 ngày không được thu lại. Vui lòng kiểm tra lại";                
                $ret[]          = $err;
            }else{ // thỏa 30 dịch vụ
                // Chặn thu máy trước khi thu dịch vụ
                if($detail != NULL){
                    if($detail["status2"]==0){
                        $err['id']      = "imei";
                        $err['msg']     = "Vui lòng thu máy trước khi thu gói dịch vụ";                
                        $ret[]          = $err;
                    }
                }
                
            } 
        }
        if ($storeid == null || $imei == null || $pricekh == 0) {
            $err['id']      = "storeid";
            $err['msg']     = "Vui lòng kiểm tra lại giá, số IMEI";
            $ret[]          = $err;
        }
        $price_tmp2 = str_replace(",", "", $price_tmp);
        if($ct==6){ //Thu máy nguyên seal, khách lẻ 
            $price_15_cent = $price_tmp2 - $price_tmp2*15/100;
            if($pricekh >$price_15_cent){
                $err['id']      = "imei";
                $err['msg']     = "Giá thu vào không được vượt quá 15%";                
                $ret[]          = $err;
            }
        }
        if($ct==7){ //Thu máy nguyên seal, đại lý (lái)
            $price_5_cent = $price_tmp2 - $price_tmp2*5/100;
            if($pricekh >$price_5_cent){
                $err['id']      = "imei";
                $err['msg']     = "Giá thu vào không được vượt quá 5%";                
                $ret[]          = $err;
            }
        }
        if(count($ret) >0){
            echo json_encode($ret);
        }else{
            $pricekh = str_replace(",", "", $pricekh);
            
            $data["storeid"] = $storeid;
            $data["productsid"] = $___detail_item["productsid"];
            $data["ct"] = $ct;
            $data["storeid2"] = $this->_identity["parentid"];
            $data["userid"] = $this->_identity["userid"];
            $data["name"] = $__pname[0];			
            $data["price"] = (int) $pricekh;
            $data["colorid"] = (int) $colorid;
            $data["type"] = $type;
            $data["imei"] = $imei;
            $data["vouchers6"] = $vouchers6;
            $data["type_voucher"] = $type_voucher;
            $data["datetime"] = date("Y-m-d H:i:s");
            $data["itemid_tmp"] = $itemid;
            $data["note"] = $note;
            $data["hnamvt"] = $hnamvt;
            $data["auid"] = $auid;
            $data["userid_check"] = $userid_check;
            $data["status_warehouse"] = $ma_kho;
			$data["upload_key"] = $this->_request->getParam("upload_key");
			
            $lastid = Business_Addon_Usedphone::getInstance()->insert($data);
            $err['id']      = "ok";
            $err['lastid']      = $lastid;
            $err['msg']     = "ok";
            $ret[]          = $err;
//            $url = 'http://app.hnammobile.com/admin/user/usedphone2/customer?id='.$lastid;
//            header("Location: $url");
					
			//remove old mapping
			Business_Addon_MappingProduct::getInstance()->deleteByImei($imei);
			
            echo json_encode($ret);
        }
    }

    public function getPriceAction() {
        $this->_helper->viewRenderer->setNoRender();
        $this->view->layout()->disableLayout();
        
        $name = $this->_request->getParam("name");
                
        $names = explode("--", $name);
        $itemid = (int) $names[1];
        $pdetail = Business_Ws_ProductsItem::getInstance()->getDetail($itemid); 
        $oprice = $pdetail["original_price"];
        $price = $pdetail["price"];
        $arr = array();
        $arr["price"]= $oprice>0? $oprice : $price;        
        $arr["itemid"]= $itemid;
        $arr["name"]= $names[0];
        
        echo json_encode($arr);
    }
    public function getPrice2Action() {
        $this->_helper->viewRenderer->setNoRender();
        $this->view->layout()->disableLayout();
        
        $name = $this->_request->getParam("name");
                
        $names = explode("--", $name);
        $itemid = (int) $names[2];
        $pdetail = Business_Ws_ProductsItem::getInstance()->getDetail($itemid); 
        $oprice = $pdetail["original_price"];
        $price = $pdetail["price"];
        $arr = array();
        $arr["price"]= $oprice>0? $oprice : $price;        
        $arr["itemid"]= $itemid;
        $arr["name"]= $names[0];
        
        echo json_encode($arr);
    }
    
    public function indexAction() {
        $this->view->upload_key =  md5(date("YmdHis"));
        $list_status_warehouse = Business_Addon_Options::getInstance()->get_status_warehouse();
        $this->view->list_status_warehouse = $list_status_warehouse;
        $auth = Zend_Auth::getInstance();
        $identity = (array) $auth->getIdentity();
        $storename = $this->getStoreName($identity["username"]);
        $auid = (int)$this->_request->getParam("auid");
		
		//get tech list
		$techList = $this->getTechList();
		$this->view->techList = $techList;
		
        $pid = (int)$this->_request->getParam("pid");
        $this->view->pid = $pid;
        $__d = date('Ymd');
        if($auid >0){
            $token = $this->_request->getParam("token");
            $skeys ="ABCKMC";
            $ztoken = md5($skeys.$__d.$auid);
            if($token != $ztoken){
                die('No access');
            }
            $ssdetail = Business_Addon_UsersProducts::getInstance()->getDetail($auid);
            if($ssdetail==NULL){
                die('Bill da thu. nen khong the thu them lan nua. Vui long kiem tra lai');
            }
            
            $detail_price_by_color = Business_Addon_ProductsColor::getInstance()->get_detail_by_id_color($ssdetail["products_id"], $ssdetail["colorid"]);
            $this->view->price_new = $detail_price_by_color["price"];
        }
        $this->view->ssdetail = $ssdetail;
       
        $name_color = array();
        $list_color = Business_Ws_NewsItem::getInstance()->getListByCateId(722);
        foreach ($list_color as $val){
            $name_color[$val["itemid"]] = $val["title"];
        }
        
        $this->view->name_color = $name_color;
        $this->view->token = $token;
        $this->view->auid = $auid;
        $this->view->storename = $storename;
        $this->view->sid = $identity["username"];
        $_option = Business_Addon_Options::getInstance();
        
        $list_ct_thumaycu = Business_Addon_CTthumay::getInstance()->get_list_by_id();
        $this->view->list_ct_thumaycu= $list_ct_thumaycu;
        
        $this->view->list_hnamVt= $_option->getHnamVT();
        if ($this->_request->isPost()) {
            $this->view->layout()->disableLayout();
            $this->view->ispost = true;
            
            $name = $this->_request->getParam("name", "");
            //process product
            $names = explode("--", $name);
            $itemid = (int)$names[2];
            if ($itemid ==0) {
				$itemid = $pid = (int)$this->_request->getParam("itemid");
            }
			$pdetail = Business_Ws_ProductsItem::getInstance()->getDetail($itemid); 
			
            $imei = $this->_request->getParam("imei", "");
            $pricekh ='';
            $note_hoantien='';
            if($imei){
                $detail_ispay = Business_Addon_AddonPromotion::getInstance()->get_detail_by_imei_synced($imei);
                if($detail_ispay){
                    $pricekh = number_format($detail_ispay["return_price"]);
                    $note_hoantien='Sản phẩm này khuyến mãi nên quản lý thu đúng giá hoàn tiền bên dưới là '.$pricekh;
                }
            }
            $this->view->pricekh = $pricekh;
            $this->view->note_hoantien = $note_hoantien;
            $price = $this->_request->getParam("price", "");
            $price = str_replace(",", "", $price);
            $type = $this->_request->getParam("type", 0);
            $warranty = $this->_request->getParam("warranty", 0);
            $can = $this->_request->getParam("can", 0);
            $rot = $this->_request->getParam("rot", 0);
            $repair = $this->_request->getParam("repair", 0);
            $out = $this->_request->getParam("out", 0);
			
			
            
			
			$isApple = $this->_request->getParam("isApple", 0);
			$isUsed = $this->_request->getParam("isUsed", 0);
			
            $cable = $this->_request->getParam("cable", 0);
            $cable = str_replace(",", "", $cable);
            $charge = $this->_request->getParam("charge", 0);
            $charge = str_replace(",", "", $charge);
            $headphone = $this->_request->getParam("headphone", 0);
            $headphone = str_replace(",", "", $headphone);
            $note = $this->_request->getParam("note");
            $vouchers6 = $this->_request->getParam("vouchers6", 0);            
            $phonetype = $this->_request->getParam("phonetype", 0);            
			
            

            $this->view->name = $name;
            $this->view->imei = $imei;
            $this->view->note = $note;
            $this->view->price = $price;
            $this->view->type = $type;
            $this->view->warranty = $warranty;
            $this->view->can = $can;
            $this->view->rot = $rot;
            $this->view->repair = $repair;
            
            if ($isUsed==1) {
				$price_type = $this->_type_used[$type][$warranty];				
			} else {
				$price_type = $this->_type[$type][$warranty];
			}
			
			if ($isApple==1 || $type==1) {
				if ($isUsed==1) {
					$price_can = $this->_canmop_used[$can];
					$price_rot = $this->_tray_used[$rot];			
				} else {
					//apple=1; used=0
					//máy mới nhưng chọn giá bán cũ, tính rule mới
					//công thức máy apple, hàng mới, không có giá mới, chọn giá cũ														
					//var_dump($isApple,$isUsed,$itemid,$names ,$pdetail);die();
					if ($pdetail["cateid"]==53) {
						//echo "Công thức tính giá - không giá web";
						//harcode nhóm 6 là apple không giá 
						$price_type = $this->_type[6][$warranty];
					
						//nhóm máy cũ
						$price_can = $this->_canmop_apple_ko_gia[$can];
						$price_rot = $this->_tray_apple_ko_gia[$rot];
					} else {
						//công thức máy apple, hàng mới, chọn giá mới
						$price_can = $this->_canmop[$can];
						$price_rot = $this->_tray[$rot];
					}
				}
				
				
			} else {
				if ($isUsed==1) {
					$price_can = $this->_canmop_android_used[$can];
					$price_rot = $this->_tray_android_used[$rot];			
				} else {
					$price_can = $this->_canmop_android[$can];
					$price_rot = $this->_tray_android[$rot];
				}
				
			}
            $price_repair = $this->_repair[$repair];

            //fix for apple sieu hot
            /*if ($type==0) {
                //apple sieu hot, can rot -5%
                $price_can -= 5;
                if ($price_can <0) {    
                    $price_can=0;
                }
            }*/
            
			//may ngoai he thong giam 5%
			if ($out==1) {
				//kiem tra phải hàng hnam và loại cty hay ko
				if ($phonetype==1 && $names[1]=="(cty)") {
					//máy ngoài + chọn loại hnam + sp cty
					$price = $price - ($price*15/100);					
				} else {
					$price = $price - ($price*5/100);
				}
				
			}			
			
            $price = $price - ($price_type * $price / 100);
            $price = $price - ($price_can * $price / 100);
            $price = $price - ($price_rot * $price / 100);
            $price = $price - ($price_repair * $price / 100);

            //tinh gia phu kien
            $price = $price - $cable;
            $price = $price - $charge;
            $price = $price - $headphone;
            
            $min_price = $price - ($price * 10 / 100);
            $max_price = $price + ($price * 5 / 100);
            $cur_price = $price;
            
            //fix for apple sieu hot
            if ($type==0) {
                $min_price = $min_price + (int)($min_price*10/100);
                $max_price = $max_price + (int)($max_price*10/100);
                $cur_price = $cur_price + (int)($cur_price*10/100);
            }
            //end fix for apple sieu hot            
            
            //update price for voucher s6
            if ($vouchers6!=0) {
                $_p_range = $this->getVoucherS6Price($min_price);
                $min_price = $min_price - $_p_range;
                
                $_p_range = $this->getVoucherS6Price($max_price);
                $max_price = $max_price - $_p_range;
                
                $_p_range = $this->getVoucherS6Price($cur_price);
                $cur_price = $cur_price - $_p_range;
            }
            //end update price for voucher s6
            
            $this->view->cur_price = number_format($cur_price);
            $this->view->min_price = number_format($min_price);
            $this->view->max_price = number_format($max_price);
            $this->view->finalprice = number_format($price);
            
            //update gia ban du kiến
            if ($price <=3000000) {
                //muc ban ra 15-20%
                $min_price_sell = $min_price + (int)($min_price*20/100);
                $max_price_sell = $max_price + (int)($max_price*20/100);
                $cur_price_sell = $cur_price + (int)($cur_price*20/100);                
            } else if ($price <=7000000) {
                $min_price_sell = $min_price + (int)($min_price*15/100);
                $max_price_sell = $max_price + (int)($max_price*15/100);
                $cur_price_sell = $cur_price + (int)($cur_price*15/100); 
            } else {
                $min_price_sell = $min_price + (int)($min_price*7/100);
                $max_price_sell = $max_price + (int)($max_price*7/100);
                $cur_price_sell = $cur_price + (int)($cur_price*7/100); 
            }
            $this->view->cur_price_sell = number_format($cur_price_sell);
            $this->view->min_price_sell = number_format($min_price_sell);
            $this->view->max_price_sell = number_format($max_price_sell);
            
        } else {
            //get all products name
//            $pname = Business_Ws_ProductsItem::getInstance()->getProductsNameWithID();
//            $this->view->pnames = $pname;
$__price = 1;
$__onstock = 1;

            $slist = Business_Ws_ProductsItem::getInstance()->getProducts2("","",$__onstock,$__price);
            if($auid>0 || $pid >0){
                foreach ($slist as $val){
                    $__products_id = explode("--", $val); 
                    if($__products_id[2] == $pid){
                        $slist2[]=$val;
                    }
                }
                $slist = $slist2;
            }
            
            $this->view->slist = $slist;
        }
    }
    
    private function getVoucherS6Price($price) {
        
        if ($price < 1000000) {
            return (int) $price*25/100;
        }
        
        if ($price < 2000000) {
            return (int) $price*20/100;
        }
        
        if ($price < 3000000) {
            (int) $price*15/100;
        }
        
        return 500000;
    }

    private function getStoreName($username) {
        $array = array(
            "vote_89" => "89 Trần Quang Khải",
            "vote_148" => "148 Nguyễn Cư Trinh",
            "vote_654" => "654 Lê Hồng Phong",
            "vote_774" => "774 Nguyễn Trãi",
            "vote_370" => "370A Lê Văn Sỹ",
            "vote_43" => "67 Trần Quan Khải",
            "vote_67" => "67 Trần Quan Khải",
            "vote_778" => "778 CMT8",
            "vote_191" => "191 Khánh Hội",
            "vote_301" => "301 Võ Văn Tần",
            "vote_294" => "294A Bạch Đằng",
            "vote_all" => "ADMIN"
        );
        return $array[$username];
    }
    
    private function getFullStorename($username) {
	
        $listStore = Business_Helpers_Store::getInstance()->getList();        
		if ($username == "vote_778") {
			$username = "vote_776";
		}
        foreach($listStore as $store) {
            $title = $store["title"];
            $usernames = explode("_", $username);
            if (strpos($title, $usernames[1])!==false) {
                return $title;
            }
        }
        return "admin";
    }
    
    
    private function isAdmin() {
        $identity = (array) $auth->getIdentity();
        $sid = $identity["username"];
        
        if ($sid == "vote_all") {
            return 1;
        }
        return 0;
    }
    
    private function copyUsedAction($pids) {

        $cache = GlobalCache::getCacheInstance('ws');
        $cache->flushAll();

        $error = 0;
        $msg = "";
        $data = null;
        if ($pids == null || $pids == "") {
            $msg = "Vui lòng chọn sản phẩm để copy!";
            $error = 1;
            
            return 0;
        } else {
            $_products = Business_Ws_ProductsItem::getInstance();
            $pids = explode(",", $pids);

            foreach ($pids as $pid) {
                $detail = $_products->getDetail($pid);
                if (isset($detail['itemid']))
                    unset($detail['itemid']);

                //copy product
                $detail['title'] = $detail['title'] . " cũ";
                $detail['cateid']  = 53; //hardcode to cateid 53: kho may cũ
                $detail['quanlity']  = 1; 
                $lastid = $_products->insert($detail['productsid'], $detail['cateid'], $detail);

                //copyimage
                $thumb = json_decode($detail['thumb']);
                $home = $thumb->thumb1;
                $large = json_decode($thumb->thumb2);

                $path_home = BASE_PATH_V3 . Globals::getConfig("product_path_home") . "/" . $home;
                $path_thumb = BASE_PATH_V3 . Globals::getConfig("product_path_thumbnails") . "/" . $large[0];
                $path_detail = BASE_PATH_V3 . Globals::getConfig("product_path_details");

                $title = Business_Common_Utils::adaptTitleLinkURLSEO($detail['title']);
                $ext = Business_Common_Images::get_image_extension($home);

                $new_home = "";
                if (is_file($path_home)) {
                    $new_home = BASE_PATH_V3 . Globals::getConfig("product_path_home") . "/" . $title . rand(0, 100) . "." . $ext;
                    copy($path_home, $new_home);
                }
                $new_thumb = "";
                if (is_file($path_thumb)) {
                    $ext = Business_Common_Images::get_image_extension($home);
                    $new_thumb = BASE_PATH_V3 . Globals::getConfig("product_path_thumbnails") . "/" . $title . rand(0, 100) . "." . $ext;
                    copy($path_thumb, $new_thumb);
                }

                $new_details[0] = $new_thumb;
                if (count($large) > 1) {
                    for ($i = 1; $i < count($large); $i++) {
                        if (isset($large[$i]) && $large[$i] != "") {
                            $_path_detail = $path_detail . "/" . $large[$i];
                            if (is_file($_path_detail)) {
                                $ext = Business_Common_Images::get_image_extension($large[$i]);
                                $new_details[$i] = BASE_PATH_V3 . Globals::getConfig("product_path_details") . "/" . $title . rand(0, 100) . "." . $ext;
                                copy($_path_detail, $new_details[$i]);
                            }
                        } else {
                            $new_details[$i] = "";
                        }
                    }
                }
                
                //copy 360 & fullbox
                $path_360 = BASE_PATH_V3 . "/v4/360/";
                $path_360_src = $path_360 . $pid;
                $path_360_des = $path_360 . $lastid;
                if (is_dir($path_360_src)) {
                    mkdir($path_360_des);
                    for($k=1; $k<=36; $k++) {
                        $_s = $path_360_src . "/" . $k . ".jpg";
                        $_d = $path_360_des . "/" . $k . ".jpg";
                        copy($_s, $_d);
                    }
                }
                
                //copy fullbox
                $path_fullbox = BASE_PATH_V3 . "/v4/fullbox/";
                $path_fullbox_src = $path_fullbox . $pid . ".jpg";
                $path_fullbox_des = $path_fullbox . $lastid . ".jpg";
                if (is_file($path_fullbox_src)) {
                    copy($path_fullbox_src, $path_fullbox_des);
                }

                //copy spec
                $this->dupSpec($pid, $lastid);

                //copy flash
                $flash_path = BASE_PATH_V3 . "/uploads/flash/" . $pid . ".swf";
                $flash_path_des = BASE_PATH_V3 . "/uploads/flash/" . $lastid . ".swf";
                if (is_file($flash_path)) {
                    copy($flash_path, $flash_path_des);
                }
            }
            $error = 0;
            $msg = "Thành công";
            return $lastid;
        }
    }

    public function dupspecAction() {
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $from_id = $this->_request->getParam('from_id');
        $to_id = $this->_request->getParam('to_id');

        $this->dupSpec($from_id, $to_id);
        echo "Done.";
    }

    private function dupSpec($from_pid, $to_pid) {
        //copy spec detail
        $_addon = Business_Addon_Featuresdata::getInstance();
        $alist = $_addon->getListByPid($from_pid);
        $_addon->deleteByPid($to_pid);

        if (count($alist) > 0) {
            foreach ($alist as $item) {
                $item['pid'] = $to_pid;
                $_addon->insert($item['fid'], $to_pid, $item['parentid'], $item);
            }
        }
    }
	
	private function getTechList() {
		
		$idregency=12; //hardcode tech group
		$is_actived=1; //get actived list
		$identity = $this->_identity;
		$parentid = $identity["parentid"];
		if ($parentid == 0) {
			$parentid  = $identity["userid"];
		}
		//$parentid=18;//hardcode 654 for dev
		
		//get list tech staff by parentid
		
		$users = Business_Common_Users::getInstance()->getListUser($keyword="",$vote_id=$parentid,$is_actived,$idregency);
		return $users;
	}
        
        
        public function delAction(){
            $this->_helper->Layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            $id = (int)  $this->_request->getParam("id");
            $__bs = Business_Addon_CTthumay::getInstance();
            $detail = $__bs->getDetail($id);
            if($detail != NULL){
                $data["creator_end"] =  $this->_identity["username"];
                $data["datetime_end"] =  date('Y-m-d H:i:s');
                $data["enabled"] =0;
                $__bs->update($id, $data);
            }
            header("location:".$_SERVER['HTTP_REFERER']);
            exit();
        }
        public function addCtthumayAction(){
            $__bs = Business_Addon_CTthumay::getInstance();
            $list = $__bs->get_list_by_id();
            $this->view->list = $list;
            if ($this->_request->isPost()) {
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
            if(count($ret) >0){
                for($i=0;$i<count($ret);$i++){
                    $msg = $ret[$i]['msg'];
                    $ids = $ret[$i]['id'];
                    echo "<script>window.parent.show_msg('$msg','$ids');</script>";
                    die();
                }
            }else{
                $data["datetime"] = date('Y-m-d H:i:s');
                $data["enabled"] = 1;
                $data["creator"] = $this->_identity["username"];
                $data["name"] = $data_frm["name"];
                $__bs->insert($data);
                $__msg ='Lưu thành công';
                $url = "/admin/user/usedphone2/add-ctthumay";
                echo "<script>window.parent.show_success('$__msg','','$url');</script>";
                die();
            }
        }
            
        }
}
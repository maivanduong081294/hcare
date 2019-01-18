<?php
/**  
* User Admin Home Controller
* @author: nghidv
*/ 
class User_Admin_ConfigController extends Zend_Controller_Action
{
	public function init()
	{
		// do something
            BlockManager::setLayout('appbh');
	}
	
	public function globalAction()
	{
		
	}

        public function emailAction(){
            if ($this->_request->isPost()){
                $mailServer = $this->_request->getParam('mailServer');
                $mailServerPort = $this->_request->getParam('mailServerPort');
                $defaultMailBoxUserName = $this->_request->getParam('defaultMailBoxUserName');
                $defaultMailBoxPassword = $this->_request->getParam('defaultMailBoxPassword');
                $defaultMailBox = $this->_request->getParam('defaultMailBox');
                $yahoo = $this->_request->getParam('yahoo');
                $skype = $this->_request->getParam('skype');
                $phone = $this->_request->getParam('phone');
                $fax = $this->_request->getParam('fax');
                $warning_email = $this->_request->getParam('warning_email');

                $mailconfig[] = $mailServer;
                $mailconfig[] = $mailServerPort;
                $mailconfig[] = $defaultMailBoxUserName;
                $mailconfig[] = $defaultMailBoxPassword;

                $mailconfig = implode(";", $mailconfig);

                Business_Common_Variables::variable_set('mail_config', $mailconfig);
                Business_Common_Variables::variable_set('contact_email', $defaultMailBox);
                Business_Common_Variables::variable_set('online_yahoo', $yahoo);
                Business_Common_Variables::variable_set('online_skype', $skype);
                Business_Common_Variables::variable_set('online_phone', $phone);
                Business_Common_Variables::variable_set('online_fax', $fax);                
                Business_Common_Variables::variable_set('warning_email', $warning_email);

                echo "<script>alert('Completed')</script>";
            }
            $config = 'mail.rohto.com.vn;25;acnes@rohto.com.vn;acnes@123';
            $mail_config = Business_Common_Variables::variable_get('mail_config', $config);
            $contact_email = Business_Common_Variables::variable_get('contact_email', 'ninhkhuong_online@yahoo.com');

            $mail_config = explode(";", $mail_config);

            $this->view->mail_config = $mail_config;
            $this->view->contact_email = $contact_email;

            $onlineyahoo = Business_Common_Variables::variable_get('online_yahoo', 'trucntt.ninhkhuong,ninhkhuong_online');
            $this->view->online_yahoo = $onlineyahoo;

            $onlinesky = Business_Common_Variables::variable_get('online_skype', 'truc-nk,hangdtt.ninhkhuong');
            $this->view->online_skype = $onlinesky;

            $online_phone = Business_Common_Variables::variable_get('online_phone', '0835073535');
            $this->view->online_phone = $online_phone;

            $online_fax = Business_Common_Variables::variable_get('online_fax', '0838279681');
            $this->view->online_fax = $online_fax;

            $warning_email = Business_Common_Variables::variable_get('warning_email', 'trucntt@ninhkhuong.vn');
            $this->view->warning_email = $warning_email;

        }


        public function listEmailAction(){

         //get list bhsc

         $bhsc = Business_Common_Variables::variable_get('mail_bhsc', '');
         $json = Zend_Json::decode($bhsc);
         $this->view->list = $json;

       if ($this->_request->isPost() or 1==1 ){
            $type = $this->_request->getParam('type');
            if($type=="add")
            {
                $storeid = (int)$this->_request->getParam('storeid');
                $email = $this->_request->getParam('email');
                $json[$storeid] =$email  ;    
                $json = Zend_Json::encode($json);  
                Business_Common_Variables::variable_set('mail_bhsc', $json);   
                exit;
            }
            if($type=="edit")
            {
                foreach ($json as $key => $value) {
                    $storeid = (int)$this->_request->getParam('storeid_'.$key);
                    $email = $this->_request->getParam('email_'.$key);
                    if( $storeid != $key)
                      unset($json[$key]);
                        $json[$storeid] =$email;    
                }
                $json = Zend_Json::encode($json);  
                Business_Common_Variables::variable_set('mail_bhsc', $json);   
                exit;
            }



         }

        }

        public function listUrlAction(){
            $accbylink = Business_Addon_AccessByLink::getInstance();
            $_regency = Business_Addon_Regency::getInstance();
            $_option = Business_Addon_Options::getInstance();
            $kw = (int)$this->_request->getParam("kw");
            $this->view->kw = $kw;
            $list_regency = $_regency->getList();
            $this->view->list_regency = $list_regency;
            
            
            $list =$accbylink->getList($kw);
            foreach ($list as &$items){
                $items["name_pos"] = $_option->getMenu($items["postion"]);
            }
//            var_dump($list);exit();
            $this->view->list = $list;
        }
        public function deleteUrlAction(){
            $id = $this->_request->getParam("id");
            $accbylink = Business_Addon_AccessByLink::getInstance();
            $data["status"] = 0;
            $accbylink->update($id, $data);
            Business_Addon_Options::getInstance()->syncAll('event');
        }

        public function setUrlAction(){
            $_regency = Business_Addon_Regency::getInstance();
            $_option = Business_Addon_Options::getInstance();
            $accbylink = Business_Addon_AccessByLink::getInstance();
            $list_modules = $_option->getModules();
            $this->view->list_modules = $list_modules;
            $id = (int)$this->_request->getParam("id");
            if($id > 0){
                $detail = $accbylink->getDetailById($id);
                $people = $detail["people"];
                $_people = explode(",", $people);
                $array_userid = explode(",", $detail["userid"]);
            }
            $this->view->array_userid = $array_userid;
            $this->view->people = $_people;
            $this->view->detail = $detail;
            $list_regency = $_regency->getList();
            $name_regency = array();
            foreach ($list_regency as $items){
                $name_regency[$items["id"]] = $items["name"];
            }
            $this->view->name_regency = $name_regency;
            $this->view->list_regency = $list_regency;
            $this->view->mid = count($list_regency)/2;
            
            $list_postion = $_option->getMenu();
            $this->view->list_postion = $list_postion;
            $type = $_option->getTypeUser();
            $this->view->type = $type;
            
            $_zwf_user = Business_Common_Users::getInstance();
            $list_user = $_zwf_user->getListUser("",0,1);
            if(count($array_userid) >0){
                foreach ($list_user as $v){
                    if(in_array($v["userid"], $array_userid)){
                        $sdata[]=$v;
                    }
                }
            }
            foreach ($list_user as $v){
                    if(!in_array($v["userid"], $array_userid)){
                        $sdata[]=$v;
                    }
                }
            
            $this->view->list_user = $sdata;
        }
        public function saveUrlAction(){
            $this->_helper->Layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            $_option = Business_Addon_Options::getInstance();
            $_accbylink = Business_Addon_AccessByLink::getInstance();
            $id = (int)  $this->_request->getParam("id");
            $data_frm = $this->_request->getParams("data_frm");
            
            $regency = implode($data_frm["regency"], ",");
            $userid = implode(",",$data_frm["userid"]);
            if($regency ==NULL){
                $regency =0;
            }
            if($userid ==NULL){
                $userid =0;
            }
            $data = array();
            $data["name"] = $data_frm["name"];
            $data["modules"] = $data_frm["moduless"];
            $data["controller"] = $data_frm["controllers"];
            $data["action"] = $data_frm["actions"];
            $data["people"] = $regency;
            $data["userid"] = $userid;
            $data["status"] = 1;
            $data["postion"] = $data_frm["postion"];
            $_link ="";
            if($data_frm["moduless"] == "user_admin"){
                $_link ="/admin/user";
            }
            if($data_frm["moduless"] =="import"){
                $_link ="/import";
            }
            $link = $_link."/".$data_frm["controllers"]."/".$data_frm["actions"];
            $data["link"] = $link;
            $ret = array();
            if ($id == 0) {
                $ret = $this->isValid($ret, $data);
            }
            if (count($ret) > 0) {
                echo json_encode($ret);
                return;
            } else {
                if($id ==0){
                    $_accbylink->insert($data);
                } else {
                    $_accbylink->update($id,$data);
                }
                $err['id'] = "ok";
                $err['msg'] = "ok";
                $ret[] = $err;
                $_option->syncAll('event');
                echo json_encode($ret);
            }
            
        }
        
        public function isValid($ret, $data){
            $_accbylink = Business_Addon_AccessByLink::getInstance();
            $arr = array();
            if($data["name"]==NULL){
                $arr["id"] = "name";
                $arr["msg"] = "Vui lòng nhập tiêu đề";
                $ret[] = $arr;
            }
            if($data["modules"]==NULL){
                $arr["id"] = "modules";
                $arr["msg"] = "Vui lòng nhập modules";
                $ret[] = $arr;
            }
            if($data["controller"]==NULL){
                $arr["id"] = "controller";
                $arr["msg"] = "Vui lòng nhập controller";
                $ret[] = $arr;
            }
            if($data["action"]==NULL){
                $arr["id"] = "action";
                $arr["msg"] = "Vui lòng nhập action";
                $ret[] = $arr;
            }
            if($data["people"] == 0 && $data["userid"] == 0){
                $arr["id"] = "regency_10";
                $arr["msg"] = "Vui lòng nhập phân quyền";
                $ret[] = $arr;
            }
            
            if($data["action"] !=NULL && $data["controller"] != NULL && $data["modules"] != NULL){
                $detail = $_accbylink->getDetail($data["modules"], $data["controller"], $data["action"]);
                if($detail !=null){
                    $arr["id"] = "action";
                    $arr["msg"] = "Link này đã được tạo.Vui lòng chỉnh sửa hoặc phân quyền thêm";
                    $ret[] = $arr;
                }
            }
            return $ret;
        }



        public function listEditBillAction(){

            //get list bhsc
          $user_product = Business_Addon_UsersProducts::getInstance();
          $user = Business_Addon_Users::getInstance();
          $billid = (int)  $this->_request->getParam("billid");
          $getDetailByID =  $user->getDetailByID($billid);

          $fullname =  $this->_request->getParam("fullname");
          $phone =  $this->_request->getParam("phone");
          $address =  $this->_request->getParam("address");

          $this->view->getDetailByID = $getDetailByID;
          if ($this->_request->isPost() and $fullname !='' ){
            $billid =  $this->_request->getParam("billid");

            error_reporting(E_ALL);
            ini_set('display_errors', 1);

            $data = array();
            $data['fullname'] = $fullname;
            $data['phone'] = $phone;
            $data['address'] = $address;
            $user->update( $billid, $data );


            $data1 = array();
            $data1['fullname_addon'] = $fullname;
            $data1['phone_addon'] = $phone;
            $data1['id_addon_user'] = $billid;  
     
            $user_product->updateBill($data1);
            }
   
         }







}
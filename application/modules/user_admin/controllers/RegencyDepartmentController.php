<?php
/**  
* User Admin Home Controller
* @author: nghidv
*/ 
class User_Admin_RegencyDepartmentController extends Zend_Controller_Action
{
    private $_identity;
	public function init()
	{
		// do something
            BlockManager::setLayout('appbh');
            $auth = Zend_Auth::getInstance(); 
            $_identity = (array)$auth->getIdentity();
            $this->_identity = $_identity;
	}
	
	public function globalAction()
	{
		
	}
        public function getStoreidAction(){
            $this->_helper->Layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            $_zwfuser = Business_Common_Users::getInstance();
            $list = $_zwfuser->getListByUname(FALSE);
            $ret                        = array();
            $arr                        = array();
            
            foreach ($list as &$items){
                $arr["userid"]     = $items["userid"];
                $arr["storename"]  = $items["storename"];
                $ret[]                 = $arr;
            }
            echo json_encode($ret);
        }

        public function getRegencyAction(){
            $this->_helper->Layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            $_regency = Business_Addon_Regency::getInstance();
            $name_regency = array();
            $list_regency = $_regency->getList();
            foreach ($list_regency as $items){
                $name_regency[$items["id"]] = $items["name"];
            }
            $_regency_department = Business_Addon_RegencyDepartment::getInstance();
            $id_department = (int)$this->_request->getParam("id_department");
            $list = $_regency_department->getListByDepartment($id_department);
            $ret                        = array();
            $arr                        = array();
            $_regencyid                 = array();
            foreach ($list as $_items){
                $_regencyid[] = $_items["id_regency"];
            }
            $_rid = implode($_regencyid, ",");
            foreach ($list as &$items){
                $arr["regency_all"]     = $_rid;
                $arr["id_regency"]     = $items["id_regency"];
                $arr["name"]           = $name_regency[$items["id_regency"]];
                $ret[]                 = $arr;
            }
            echo json_encode($ret);
        }
        public function deleteAction(){
             $this->_helper->Layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            $_regency_department = Business_Addon_RegencyDepartment::getInstance();
            $id = (int)  $this->_request->getParam("id");
            $data["enabled"] = 0;
            $_regency_department->update($id, $data);
        }

        public function listAction(){
            $_department = Business_Addon_Department::getInstance();
            $_regency_department = Business_Addon_RegencyDepartment::getInstance();
            $_regency = Business_Addon_Regency::getInstance();
            $name_regency = array();
            $name_department = array();
            
            
            $list_regency = $_regency->getList();
            foreach ($list_regency as $items){
                $name_regency[$items["id"]] = $items["name"];
            }
            $department = (int)$this->_request->getParam("department");
            $regency = (int)$this->_request->getParam("regency");
            
            $list_department = $_department->getList();
            foreach ($list_department as $items){
                $name_department[$items["id"]] = $items["name"];
            }
            
            $this->view->list_department = $list_department;
            $this->view->list_regency = $list_regency;
            
            $this->view->department = $department;
            $this->view->regency = $regency;
            $list = $_regency_department->getList($department,$regency);
            
            $this->view->name_regency = $name_regency;
            $this->view->name_department = $name_department;
            $this->view->list = $list;
//            echo '<pre>';
//                        var_dump($list);exit();
        }

        public function editAction(){
            $_regency = Business_Addon_Regency::getInstance();
            $_department = Business_Addon_Department::getInstance();
            $_regency_department = Business_Addon_RegencyDepartment::getInstance();
            $list_regency = $_regency->getList();
            $this->view->list_regency = $list_regency;
            
            $list_department = $_department->getList();
            $this->view->list_department = $list_department;
            
            $id = (int)$this->_request->getParam("id");
            if($id >0){
                $detail = $_regency_department->getDetailById($id);
            }
            $this->view->detail = $detail;
        }
        public function saveAction(){
            $this->_helper->Layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            $_regency_department = Business_Addon_RegencyDepartment::getInstance();
            $id = (int)  $this->_request->getParam("id");
            $id_regency = $this->_request->getParam("regency");
            $id_department = $this->_request->getParam("department");
            
            $data = array();
            $data["id_regency"] = $id_regency;
            $data["id_department"] = $id_department;
            
            $data["enabled"] = 1;
           
            $ret = array();
            if ($id == 0) {
                $ret = $this->isValid($ret, $data);
            }
            if (count($ret) > 0) {
                echo json_encode($ret);
            } else {
                if($id ==0){
                    $data["creator"] = $this->_identity["username"];
                    $data["datetime"] = date('Y-m-d H:i:s');
                    $_regency_department->insert($data);
                } else {
                    $data["creator_end"] = $this->_identity["username"];
                    $data["end_datetime"] = date('Y-m-d H:i:s');
                    $_regency_department->update($id,$data);
                }
                $err['id'] = "ok";
                $err['msg'] = "ok";
                $ret[] = $err;
//                $_option->syncAll('event');
                echo json_encode($ret);
            }
            
        }
        public function isValid($ret, $data){
            $_option = Business_Addon_Options::getInstance();
            $_regency_department = Business_Addon_RegencyDepartment::getInstance();
            $ret = $_option->isValid($data["id_regency"], "regency", "Thông báo.!\nVui lòng chọn chức vụ.\nXin cảm ơn!.",0);
            $ret1 = $_option->isValid($data["id_department"], "department", "Thông báo.!\nVui lòng chọn phòng ban.\nXin cảm ơn!.",0);
            $ret = array_merge($ret,$ret1);
            if($data["id_regency"] !=0){
                $detail = $_regency_department->getDetail($data["id_regency"]);
                if($detail != null){
                    $ret2 = $_option->isValid(0, "department", "Thông báo.!\nChức vụ này đã thuộc phòng ban khác.Vui lòng kiểm tra lại.\nXin cảm ơn!.",0);
                    $ret = array_merge($ret,$ret2);
                }
            }
            
            return $ret;
        }

}
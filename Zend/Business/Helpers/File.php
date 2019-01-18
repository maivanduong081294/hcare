<?php
class Business_Helpers_File {

    private static $_instance = null;
    public $_session = null;
    public $_userinfo = null;

    // module news to store

    function __construct() {
        $this->_session = new Zend_Session_Namespace('session_app');
        $this->_userinfo = $this->_session->userinfo;
    }

    /**
     * get instance of Business_Helpers_File
     *
     * @return Business_Helpers_File
     */
    public static function getInstance() {

        if (self::$_instance == null) {
            self::$_instance = new Business_Helpers_File();
        }
        return self::$_instance;
    }
    
    public function data2File2($data, $ext="txt", $path="tmp/") {
        $filename = "";
        if ($data != null) {
            $rand = Business_Common_Utils::generateRandomNumber();
            $filename = BASE_PATH . "/" . $path . date("dmYHis") . "-" . $rand . "." . $ext;
            $file_put_contents = file_put_contents($filename, $data);
            if ($file_put_contents)
                return $filename;
            return null;
        } else {
            return null;
        }
    }

    public function data2File($data) {
        $filename = "";
        if ($data != null) {
            $rand = Business_Common_Utils::generateRandomNumber();
            $filename = CONTACT_FILE_PATH . "/" . date("dmYHis") . "-" . $rand . ".txt";
            $file_put_contents = file_put_contents($filename, $data);
            if ($file_put_contents)
                return $filename;
            return null;
        } else {
            return null;
        }
    }
    
    public function uploadContact($file) {
        $allow_type_csv = array("application/vnd.ms-excel","application/csv");
        $allow_type_txt = array("text/csv","text/plain","text/x-csv","text/comma-separated-values");
        $allow_upload = "none";
        if ($file == null)
            return -1;        
        if (in_array($file['type'], $allow_type_csv)) {
            $allow_upload = "csv";
        } else if (in_array($file['type'], $allow_type_txt)) {
            $allow_upload = "txt";
        }
        
        if ($allow_upload != "none" ) {
            //backup user contact     
            $_userinfo = $this->_userinfo;
            $listname = $_userinfo['userid'] . "-" . date("dmYhis") . ".txt";
            $ret = move_uploaded_file($file['tmp_name'], CONTACT_FILE_PATH . "/" . $listname);            
        
//            $this->processList(CONTACT_FILE_PATH . "/" . $listname);
            $_path = CONTACT_FILE_PATH . "/" . $listname;
            $_path_tmp = CONTACT_FILE_PATH . "/" . $listname . ".tmp." . $allow_upload;
	    
            exec("sort -u $_path > $_path_tmp");
            exec("rm $_path");            
            
//            exec("sort -u $_path > $_path_tmp");
//            $_path_tmp_2 = CONTACT_FILE_PATH . "/" . $listname . ".tmp." . $allow_upload . "2";
            //remove unicode bị lỗi thông tin họ tên
            //exec("iconv -c -f utf-8 -t ascii $_path_tmp_2 > $_path_tmp");
//            exec("rm $_path");
//            exec("rm $_path_tmp_2");
            
            
            return $_path_tmp;
        } else {
            return -1;
        }
    }

    public function processList($contactid, $path) {
        try {
            $delimiter = null;
            
            $handle = @fopen($path, "r");
//            $_userinfo = $this->_userinfo;
            if ($handle == null) return;
            $totalProcess=0;           
            $ret = array();
            $count=1;
            $hasData = false;

            while (!feof($handle)) {
                $line = fgets($handle);
                $line = $this->fixStr($line);
                
//                if ($line != null && $totalProcess <= 10) {
                if ($line != null) {
                    if ($delimiter === null)
                        $delimiter = $this->getDelimiter($path);
                    
                    $data = explode($delimiter, $line);
                    if (!$this->isRowHasFullData($data)) continue;
                    $hasData = true;
                    //if($data != null && count($data)>0) {
                        $emailOffset = $this->getEmailOffset($data);
                        $email = $data[$emailOffset];                        
                        if ($email != null && !empty($email)){
                            $arr['email'] = strtolower(trim($email));
                            $arr['contactid'] = $contactid;
                            $arr['status'] = 0; //not check
                            $arr['createdate'] = date("Y-m-d H:i:s");
                            $arr['updatedate'] = date("Y-m-d H:i:s");                             
                            for($i=0; $i<count($data); $i++) {
                                $arr["field".($i+1)] = trim($data[$i]);
                            }                        
                            $ret[] = $arr;
                            if ($count++==10) {
                                Business_Es_ContactDetail::getInstance()->insertMulti($ret);
//                                Business_Es_ContactDetailStorage::getInstance()->insertMulti($ret);
//                                Business_Es_ContactDetail::getInstance()->insertModifyDupEntry($ret);
//                                Business_Es_ContactDetailStorage::getInstance()->insertModifyDupEntry($ret);
                                $ret = array();
                                break;
                            }
                        }
                    }
                }
                $totalProcess++;
            if (count($ret)>0) {
//                Business_Es_ContactDetail::getInstance()->insertModifyDupEntry($ret);
//                Business_Es_ContactDetailStorage::getInstance()->insertModifyDupEntry($ret);
                Business_Es_ContactDetail::getInstance()->insertMulti($ret);
//                Business_Es_ContactDetailStorage::getInstance()->insertMulti($ret);
            }
             
        //update total contact 
//        $data = Business_Es_Contact::getInstance()->getDetail($contactid);
//        $data['total'] += (int) Business_Es_ContactDetail::getInstance()->getTotal($contactid);
//        Business_Es_Contact::getInstance()->update($contactid, $data);
            
            //call mass upload
            //$esurl = Zend_Registry::get("configuration")->esurl;
            //$url = $esurl . "/contact/send-upload?contactid=$contactid&filename=".urlencode($path);
            //Business_Common_Utils::getContentByCurl($url);
            if (APP_ENV != "development"){
                $obj =new easymail_TESContactData();
                $obj->contactid = $contactid;
                $obj->path = $path;
                Business_ThriftClient_ContactImporter::getInstance()->ow_import($obj);
            }
            fclose($handle);            
            
            return $hasData;
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
            return false;
        }        
    }        
    
    private function getDelimiter($path) {
        if (strpos($path, "csv"))
            return ",";
        else
            return "\t";
    }
    
    private function fixStr($str) {
        //$str = strtolower($str);
        $str = str_replace("\"", "", $str);
        $str = str_replace("'", "", $str);
        return $str;
    }
            
    private function getEmailOffset($arrData) {
        for($i=0; $i<count($arrData); $i++) {
            $data = trim($arrData[$i]);
            $isValidEmail = Business_Common_Utils::isValidEmail($data);
            if ($isValidEmail) {
                return $i;
            }
        }
        return -1;
    }
    
    private function isRowHasFullData($arrData) {
        $hasFullData = true;
        for($i=0; $i<count($arrData); $i++) {
            $data = $arrData[$i];
            if ($data==null || empty($data)) {
                $hasFullData = false;
                break;
            }
        }
        return $hasFullData;
    }
}

?>

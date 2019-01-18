<?php

class Business_Helpers_Es {

    private static $_instance = null;
    public $_session = null;
    public $_userinfo = null;

    // module news to store

    function __construct() {
        $this->_session = new Zend_Session_Namespace('session_app');
        $this->_userinfo = $this->_session->userinfo;
    }

    /**
     * get instance of Business_Helpers_Es
     *
     * @return Business_Helpers_Es
     */
    public static function getInstance() {

        if (self::$_instance == null) {
            self::$_instance = new Business_Helpers_Es();
        }
        return self::$_instance;
    }

    public function isListOwner($contactid) {
        try {
            $contact = Business_Es_Contact::getInstance()->getDetail($contactid);
            $userid = (int) $this->_userinfo['userid'];
            if ((int) $contact['owner_userid'] == $userid)
                return true;
            return false;
        } catch (Exception $exc) {
//            echo $exc->getTraceAsString();
            return false;
        }
    }

    public function isTemplateOwner($templateid) {
        try {
            $template = Business_Es_Templates::getInstance()->getDetail($templateid);
            $userid = (int) $this->_userinfo['userid'];
            if ((int) $template['usedby'] == $userid)
                return true;
            return false;
        } catch (Exception $exc) {
//            echo $exc->getTraceAsString();
            return false;
        }
    }

    public function getDefaultFooter($companyname, $address, $phone) {
//        if ($companyname != null)
//            $info[] = "<b>".$companyname."</b>";
//        if ($address != null)
//            $info[] = $address;
//        if ($phone != null)
//            $info[] = $phone;
//        $company_info = implode(" &#8901; ", $info);
        $HTMLCONTENT = <<<HTMLCONTENT
        <table><tr height=50><td>&nbsp;</td></tr></table>
        <table width="100%" cellspacing="0" cellpadding="0">            
            <tr height="22">
                <td>
                <p style="font-size:12px; color:#999;padding:2px 0; margin:0">Bạn không muốn nhận email? Bấm vào <a href="{{unsubscriber-link}}">unsubscribe</a></p>                
                </td>
            </tr>
            <tr>
                <td>
                    <p style="font-size:12px; color:#999;padding:2px 0; margin:0">Email được gửi đến {{EMAIL}} lúc {{DATETIME}}</p>
                    <p style="font-size:12px; color:#999;padding:2px 0; margin:0">Công ty: $companyname</p>
                    <p style="font-size:12px; color:#999;padding:2px 0; margin:0">Địa chỉ: $address</p>                                    
                </td>
            </tr>
            <tr height="22">
                <td>
                    <p style="font-size:12px; color:#999;padding:2px 0; margin:0">Powered by <a href="http://www.easymail.vn">EasyMail</a> - Email Marketing thật dễ!</p>
                </td>
            </tr>
        </table>
HTMLCONTENT;
        return $HTMLCONTENT;
    }

    public function filterContent($html) {
        $html = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $html);
        $html = preg_replace('#<style(.*?)>(.*?)</style>#is', '', $html);
        return $html;
    }

    public function isContactRunning($contactid) {
        $detail = Business_Es_TemplatesRunning::getInstance()->getDetailByContactid($contactid);
        if ($detail != null)
            return true;
        return false;
    }

    public function getCampaignStatus($id, $time) {

        switch ($id) {
            case 0:
                return "Nháp";
                break;
            case 1:
                return "Đang xử lý";
                break;
            case 2:
                return "Đang xử lý";
                break;
            case 3:
                return "Đã gửi";
                break;
            case 5:
                $date = date("H:i-d/m/Y", strtotime($time));
                return "Gửi vào lúc<br />" . $date;
                break;
            default:
                return "Đã gửi";
                break;
        }
    }

    public function setcookie($cookiename, $value) {
        setcookie($cookiename, $value, time() + 3600, '/', Globals::getDomain());
    }

    public function getEmailVerifiedStatus($intStatus) {
        switch ($intStatus) {
            case 1:
                return "<b class='green'>Đã xác thực</b>";
                break;
            case 0:
            default:
                return "<b class='red'>Chưa xác thực</b>";
                break;
        }
    }

    public function genUserToken($userid) {
        return md5($userid . "ZXCASDQWE!@#");
    }

    public function getColInfo($contactid) {
        $_contact = Business_Es_ContactDetailInfo::getInstance();
        $info = $_contact->getList($contactid);
//        $_info[0]["field_name"] = "email";
//        $_info[0]["displayname"] = "email";        
//        $info = array_merge($_info, $info);

        //GlobalsEs::getColumnName($colname);
        foreach ($info as &$item) {
            if ($item["field_name"] != "email")
                $item["fulldisplay"] = ucfirst (GlobalsEs::getColumnName($item["displaynameid"]));
            else {
                $item["fulldisplay"] = "email";
            }            
        }
        return $info;
    }
/*
 * @return
 * 1: confirm by me
 * 2: confirm by another
 * 3: not confirm
 */
    public function getSenderConfirmLev($emailsender, $uid) {
        $_verify = Business_Es_VerifyEmail::getInstance();
        $detail = $_verify->getDetail($emailsender);
        if ($detail["status"] == 1 && $detail["owner_id"]==$uid) {
            return 1;
        } else if ($detail["status"] == 1) {
            return 2;
        } else {
            return 3;
        }
        
    }
/*
 * @return
 * empty:ok
 * not empty:Fail
 * verified: Xác thực bởi tài khoản khác
 */
    public function verifySender($emailsender) {
        $userinfo = $this->_userinfo;                
        $uid = (int) $userinfo["userid"];
        $_eshelper = Business_Helpers_Es::getInstance();
        $_verifyemail = Business_Es_VerifyEmail::getInstance();        
        
        if ($emailsender == null) {
            return "email is empty";
        } else {
            //send verify email to user
            $esURL = Globals::getConfig("esurl");

            $token = md5($emailsender . "easymail@123");
//            58fb24a2852563bec84a06d30fe39b8b
            $path = "$esURL/verifysender?email=$emailsender&token=$token";
            $content = file_get_contents($path);

            if ($content == "") {
                //get email
                $result = $_verifyemail->getDetail($emailsender);
                if (count($result) == 0) {
                    //complete, save email to DB
                    $data = array();
                    $data["email"] = $emailsender;
                    $data["owner_id"] = $uid;
                    $data["status"] = 0; //default not active
                    $data["createddate"] = date("Y-m-d H:i:s");
                    $_verifyemail->insert($data);
                }
                return $content; //send ok
            } else {
                return $content;//send fail
            }
        }
    }
    
/*
 * @return
 * empty:ok
 * not empty:Fail
 * verified: Xác thực bởi tài khoản khác
 */
    public function verifySenderV2($email) {
        $userinfo = $this->_userinfo;                
        $uid = (int) $userinfo["userid"];
        $_eshelper = Business_Helpers_Es::getInstance();
        $_verifyemail = Business_Es_VerifyEmail::getInstance();        
        
        if ($email == null) {
            return "email is empty";
        } else {
            //check email
            $result = $_verifyemail->getDetail($email);
            if (count($result) > 0) {
                if((int)$result["status"] == 1 && $result["owner_id"] != $uid) {
                    //verified
                    $content = "verified";
                    return $content;
                }
            }
            
            //send verify email to user
//            $from = "hotro@easymail.vn";
            $from = "info@ezm-system.com";
            $displayname = "Easymail";
//            $replyto = "hotro@easymail.vn";
            $to = $email;
            $subject = "Xác thực địa chỉ email tạo chiến dịch";
            $templatename = "verify_sender";
            $verify_template = Business_Common_Utils::getEmailTemplate($templatename);

//            $smtp = Zend_Registry::get("configuration")->es_smtp;

            $link_confirm = "http://ui.easymail.vn/verifyemail/confirm?account=" . $email . "&token=" . md5($email . "zz");
            $verify_template = str_replace("{{link_confirm_verify}}", $link_confirm, $verify_template);
            $verify_template = str_replace("{{username}}", $email, $verify_template);
//            $content = Business_Common_Utils::sendEmail($from, $displayname, $replyto, $to, $subject, $verify_template, null, $smtp);
            $content = Business_Common_Utils::sendMG($subject, $displayname, $from, $to, $verify_template);

            if ($content == "") {
                //get email
//                $result = $_verifyemail->getDetail($email);
                if (count($result) == 0) {
                    //complete, save email to DB
                    $data = array();
                    $data["email"] = $email;
                    $data["owner_id"] = $uid;
                    $data["status"] = 0; //default not active
                    $data["createddate"] = date("Y-m-d H:i:s");
                    $_verifyemail->insert($data);
                }
                return $content; //send ok
            } else {
                return $content;//send fail
            }
        }
    }
    
    public function updateStatus() {
//        $this->_helper->Layout()->disableLayout();
//        $this->_helper->viewRenderer->setNoRender(true);

        $_verifyemail = Business_Es_VerifyEmail::getInstance();
        $identity = Business_Auth_GlobalAuth::getIdentity();
        $uid = (int) $identity["uid"];

        if ($uid == 0)
            header('Location: /home');

        //get all email not verify in DB
        $_emailListNotVerified = array();
        $emailListNotVerified = $_verifyemail->getListNotVerified();
        
        foreach ($emailListNotVerified as $_email) {
            $_emailListNotVerified[] = $_email["email"];
        }
        if (count($_emailListNotVerified) > 0) {
            //get verified email list from amazon
            $esURL = Globals::getConfig("esurl");

            $token = md5($uid . "easymail@123");
            $path = "$esURL/getallsender?uid=$uid&token=$token";
            $content = file_get_contents($path);
            $emailList = json_decode($content);

            foreach ($emailList as $email) {
                //check email from amazon on DB
                if (in_array($email, $_emailListNotVerified)) {
                    $data = array();
                    $data["status"] = 1;
                    $_verifyemail->update($email, $data);
                }
            }
            //update list cache
            $_verifyemail->updateCache($uid);
        }
    }
    
    public function isAllowCreateCampaign($userid, $totalContact) {
        $user = Business_Ws_UserModule::getInstance()->getUserByUserid($userid);
        $remainCredits = $user["total_credits"] - $user["used_credits"];
        if ($remainCredits > $totalContact) {
            return true;
        }
        return false;
    }
    
    public function getEmailType($email) {
        if ($email == null || empty($email)) return 0;
        $email = strtolower($email);
        if (strpos($email, "@gmail") !== false) return 1;
        if (strpos($email, "@yahoo") !== false) return 2;
        return 3;
    }
    
    public function getEmailsFromBounceString($strBounce) {
        // this regex handles more email address formats like a+b@google.com.sg, and the i makes it case insensitive
        $pattern = '/[a-z0-9_\-\+]+@[a-z0-9\-]+\.([a-z]{2,3})(?:\.[a-z]{2})?/i';

        // preg_match_all returns an associative array
        preg_match_all($pattern, $strBounce, $matches);

        // the data you want is in $matches[0], dump it with var_export() to see it
        if (count($matches)>0) {
            $arr = $matches[0];
            return $arr;
        }
        return null;
    }
}
?>
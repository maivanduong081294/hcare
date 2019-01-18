<?php

//require_once BASE_PATH . '/sms_gapit/nusoap/nusoap.php';

class Business_External_SMS extends Business_Abstract {

    public static function sendMT($des, $data_msg, $billid, $serviceID = 1) {
        ini_set('display_errors', '1');
        //serviceID
        //1 --> gui tin nhan ra cua hang
        //2 --> gui tin nhan giao hang quan GHN
        
        $_config = Globals::getConfig("sms");
        $url = $_config->url;
            
        $clientGapit = new nusoap_client($url, true);

        $errGapit = $clientGapit->getError();
        if ($errGapit) {
            die("Can not connect");
        }
        $dest = $des;
        $name = $_config->brandname;
        $msgBody = $data_msg;
        $contentType = $_config->contenttype;
        $serviceID = $serviceID;
        $cpID = $_config->cpid;
        $username = $_config->username;
        $password = $_config->password;
        $operator = $_config->operator;;

        $paramsGapit = array(
            'dest' => $dest,
            'name' => $name,
            'msgBody' => $msgBody,
            'contentType' => $contentType,
            'serviceID' => $serviceID,
            'cpID' => $cpID,
            'username' => $username,
            'password' => $password
        );
		
		$totalSent = Business_Addon_SMSHistory::getInstance()->getTotalSendByDate($des, date("Y-m-d"));
        
        if ($totalSent>=5) {
            $resultSent = 99999;
        } else {
            $_resultSent = $clientGapit->call($operator, $paramsGapit);		
            $resultSent = $_resultSent["SendMTResult"];
        }
        
		//$_resultSent = $clientGapit->call($operator, $paramsGapit);		
		//$resultSent = $_resultSent["SendMTResult"];
		
        //$resultSent = 200;    
        $msg = "";
        switch ($resultSent) {
//            case 200: $msg = "OK (successfully received by GAPIT platform)";
            case 200: $msg = "OK (successfully received by GAPIT platform)";
                break;
			case 99999: $msg = "LIMIT (Max 5 times)";
                break;
            case 401: $msg = "FORBIDDEN (invalid source ip address)";
                break;
            case 406: $msg = "FORBIDDEN (invalid cpid)";
                break;
            case 403: $msg = "FORBIDDEN (bad username or password)";
                break;
            case 400: $msg = "BAD REQUEST (invalid format or parameters)";
                break;
            case -999: $msg = "FAIL (fail received by GAPIT)";
                break;
            case 505: $msg = "BAD REQUEST (Invalid brand name)";
                break;
            default: $msg = "Unknow Error";
                break;
        }
        
        //write log
        $data = array();
        $data["to"] = $des;
        $data["content"] = $data_msg;
        $data["result_msg"] = $msg;
        $data["result_code"] = $resultSent;
        $data["serviceid"] = $serviceID;
        $data["billid"] = (int) $billid;
        $data["datetime"] = date("Y-m-d H:i:s");
        Business_Addon_SMSHistory::getInstance()->insert($data);
        
        return $resultSent;
    }

    public static function sendAPI($des, $data_msg, $billid, $serviceID = 1) {
        $url = 'https://www.hnammobile.com/api/sms';
        $params = [
            'phone'=>$des,
            'message'=>$data_msg,
            'billID'=>$billid,
            'serviceID'=>$serviceID,
            'token'=>Business_External_SMS::getToken($billid),
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        $result = curl_exec($ch);
        if(curl_errno($ch) !== 0) {
            error_log('cURL error when connecting to ' . $url . ': ' . curl_error($ch));
        }
        curl_close($ch);
        return $result;
    }

    public static function getToken($billid) {
        $_config = Globals::getConfig("sms");
        return md5((int)$billid.$_config->secret);
    }

}

?>
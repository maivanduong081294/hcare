<?php

class Business_External_Ghn extends Business_Abstract {

    const METHODE_GET = 'GET';
    const METHODE_POST = 'POST';

    protected $validMethods = array(
        self::METHODE_GET,
        self::METHODE_POST,
    );
    protected $apiUrl;
    protected $cURL;
    protected $clientID;
    protected $apiKey;
    protected $apiSecretKey;
    protected $password;

    public function __construct($apiUrl, $clientID, $password, $apiKey, $apiSecretKey) {
        $this->apiUrl = rtrim($apiUrl, '/') . '/';
        $this->clientID = $clientID;
        $this->password = $password;
        $this->apiKey = $apiKey;
        $this->apiSecretKey = $apiSecretKey;

        $this->cURL = curl_init();
        curl_setopt($this->cURL, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->cURL, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($this->cURL, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($this->cURL, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8'));
    }

    public function call($url, $method = self::METHODE_GET, $data = array(), $params = array()) {
        if (!in_array($method, $this->validMethods)) {
            throw new Exception('Invalid HTTP-Methods: ' . $method);
        }

        $queryString = '';

        if (!empty($params)) {
            $queryString = http_build_query($params);
        }

        $url = rtrim($url, '?') . '?';
        $url = $this->apiUrl . $url . $queryString;

        $jsonData = json_encode($data);

        curl_setopt($this->cURL, CURLOPT_URL, $url);
        curl_setopt($this->cURL, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($this->cURL, CURLOPT_POSTFIELDS, $jsonData);

        $result = curl_exec($this->cURL);
        $httpCode = curl_getinfo($this->cURL, CURLINFO_HTTP_CODE);

        return $this->response($result, $httpCode);
    }

    protected function response($result, $httpCode) {
        //var_export($result); // Print Log
        if (null === $decodedResult = json_decode($result, true)) {
            echo "Could not decode json. " . print_r($result, true);
            return;
        }

        if ($decodedResult['ErrorMessage'] != null) {
            $this->errorMessager = $decodedResult['ErrorMessage'];
        }
        return $decodedResult;
    }

    private function get($url, $params = array()) {
        return $this->call($url, self::METHODE_GET, array(), $params);
    }

    private function post($url, $data = array(), $params = array()) {
        return $this->call($url, self::METHODE_POST, $data, $params);
    }

    public function SignIn() {
        $result = $this->post('SignIn', array("ApiKey" => $this->apiKey, "ApiSecretKey" => $this->apiSecretKey, "ClientID" => $this->clientID, "Password" => $this->password));

        if (empty($result['ErrorMessage'])) {
            return $result['SessionToken'];
        }
        return null;
    }

    public function SignOut() {
        return $this->post('SignOut', array("ApiKey" => $this->apiKey, "ApiSecretKey" => $this->apiSecretKey, "SessionToken" => $this->sessionClient));
    }

    public function CreateShippingOrder($orderRequest) {
        $header = array("ApiKey" => $this->apiKey, "ApiSecretKey" => $this->apiSecretKey, "ClientID" => $this->clientID, "Password" => $this->password);
        $request = array_merge($header, $orderRequest);

        return $this->post('CreateShippingOrder', $request);
    }

    public function GetClientHubs($getPickHubRequest) {
        $header = array("ApiKey" => $this->apiKey, "ApiSecretKey" => $this->apiSecretKey, "ClientID" => $this->clientID, "Password" => $this->password);
        $request = array_merge($header, $getPickHubRequest);

        return $this->post('GetPickHubs', $request);
    }

    public function CancelOrder($cancelOrderRequest) {
        $header = array("ApiKey" => $this->apiKey, "ApiSecretKey" => $this->apiSecretKey, "ClientID" => $this->clientID, "Password" => $this->password);
        $request = array_merge($header, $cancelOrderRequest);

        return $this->post('CancelOrder', $request);
    }

    public function GetDistrictProvinceData($districtProvinceDataRequest) {
        $header = array("ApiKey" => $this->apiKey, "ApiSecretKey" => $this->apiSecretKey, "ClientID" => $this->clientID, "Password" => $this->password);
        $request = array_merge($header, $districtProvinceDataRequest);

        return $this->post('GetDistrictProvinceData', $request);
    }

    public function CalculateServiceFee($calculateServiceFeeRequest) {
        $header = array("ApiKey" => $this->apiKey, "ApiSecretKey" => $this->apiSecretKey, "ClientID" => $this->clientID, "Password" => $this->password);
        $request = array_merge($header, $calculateServiceFeeRequest);

        return $this->post('CalculateServiceFee', $request);
    }
    
    public function GetOrderInfo($orderInfoServiceFeeRequest) {
        $header = array("ApiKey" => $this->apiKey, "ApiSecretKey" => $this->apiSecretKey, "ClientID" => $this->clientID, "Password" => $this->password);
        $request = array_merge($header, $orderInfoServiceFeeRequest);

        return $this->post('GetOrderInfo', $request);
    }
    
    public function GetServiceList($infoServiceRequest) {
        $header = array("ApiKey" => $this->apiKey, "ApiSecretKey" => $this->apiSecretKey, "ClientID" => $this->clientID, "Password" => $this->password);
        $request = array_merge($header, $infoServiceRequest);

        return $this->post('GetServiceList', $request);
    }

}
?>
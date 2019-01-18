<?php

class Business_Addon_Telegram extends Business_Abstract
{
    private $_tablename = 'addon_telegram_history';
    private static $_token = null;
    private static $_url = null;
    private static $_instance = null;

    function __construct(){
        $this->_token = "778895657:AAGH6-Uklp7_px_lDxoQk7DhJuiwZv5bLiQ";
        $this->_url = "https://api.telegram.org/bot{$this->_token}/";
    }

    public static function getInstance() {
        if(self::$_instance == null) {
            self::$_instance = new Business_Addon_Telegram();
        }
        return self::$_instance;
    }

    function getDbConnection() {
        $db = Globals::getDbConnection('maindb', false);
        return $db;
    }

    public function insert($data) {
        $db = $this->getDbConnection();
        $result = $db->insert($this->_tablename,$data);
        if ($result > 0) {
            $lastid= $db->lastInsertId($this->_tablename);
        }
        return $lastid;
    }

    public function sendMessage($id, $message) {
        $params = array(
            'chat_id' => $id,
            'text' => $message,
            'parse_mode' => 'HTML',
        );
        $url = $this->_url.'sendMessage';
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
}
<?php

class Business_Addon_FASThdbanhangck extends Business_Abstract {

    private $_tablename = 'hdbanhang_ck';
    private static $_instance = null;
    private $id = 0;
    private $id0 = 0;
    private $ma_vt;
    private $ma_lo;
    private $ma_ck;
    private $ten_ck;
    private $ck;
    private $loai_ck;
    private $_url = "http://sync.hnammobile.com/api/mssql/execute/query";
    private $_authString = "hnam:#hn@m16";
    function getId() {
        return $this->id;
    }

    function getId0() {
        return $this->id0;
    }

    function getMa_vt() {
        return $this->ma_vt;
    }

    function getMa_lo() {
        return $this->ma_lo;
    }

    function getMa_ck() {
        return $this->ma_ck;
    }

    function getTen_ck() {
        return $this->ten_ck;
    }

    function getCk() {
        return $this->ck;
    }

    function getLoai_ck() {
        return $this->loai_ck;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setId0($id0) {
        $this->id0 = $id0;
    }

    function setMa_vt($ma_vt) {
        $this->ma_vt = $ma_vt;
    }

    function setMa_lo($ma_lo) {
        $this->ma_lo = $ma_lo;
    }

    function setMa_ck($ma_ck) {
        $this->ma_ck = $ma_ck;
    }

    function setTen_ck($ten_ck) {
        $this->ten_ck = $ten_ck;
    }

    function setCk($ck) {
        $this->ck = $ck;
    }

    function setLoai_ck($loai_ck) {
        $this->loai_ck = $loai_ck;
    }
    function __construct() {
        
    }

    //public static function
    /**
     * get instance of Business_Addon_FASThdbanhangck
     *
     * @return Business_Addon_FASThdbanhangck
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
     * @return Maro_Db_Interface
     */
    private function getDbConnection() {
        $db = Globals::getDbInstance('windows');
        return $db;
    }

    /**
     * Enter description here...
     *
     * @return Maro_Cache
     */
    private function getCacheInstance() {
        $cache = GlobalCache::getCacheInstance('event');
        return $cache;
    }

    public function select($query) {
        $db = $this->getDbConnection();
        try {
            $result = $db->select($query);
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
        }
        return $result;
    }
    public function excute($query) {
        
        $db = $this->getDbConnection();
        try {
            $result = $db->excute($query);
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
        }
        return $result;
    }
    public function insert($data) {
        $db = $this->getDbConnection();
        $id = $this->getId();
        $id0 = $this->getId0();
        $ma_vt = $this->getMa_vt();
        $ma_lo = $this->getMa_lo();
        $ma_ck = $this->getMa_ck();
        $ten_ck = $this->getTen_ck();
        $ck = $this->getCk();
        $loai_ck = $this->getLoai_ck();
        $query = "INSERT INTO $this->_tablename ([id],[id0],[ma_vt],[ma_lo],[ma_ck],[ten_ck],[ck],[loai_ck])
                 VALUES ('$id','$id0','$ma_vt','$ma_lo',N'$ma_ck',N'$ten_ck','$ck','$loai_ck')";
        try {
            $__option = Business_Addon_Options::getInstance();
            $getData = $__option->get_data($query);
            $ret = $__option->doPost($this->_url,$getData,  $this->_authString);
            if($ret["error"]==-1){
                $result=0;
            }else{
                $result =1;
            }
        } catch (Exception $exc) {
            echo 'Them khong thanh cong. Da co loi xay ra';
            die();
            echo $exc->getTraceAsString();
        }
        return $result;
    }
    public function delete($id){
        $query ="DELETE FROM $this->_tablename WHERE id=$id";
        try {
            if((int)$id >0){
                $__option = Business_Addon_Options::getInstance();
                $getData = $__option->get_data($query);
                $ret = $__option->doPost($this->_url,$getData,  $this->_authString);
                if($ret["error"]==-1){
                    $result=0;
                }else{
                    $result =1;
                }
            }
        } catch (Exception $exc) {
            echo 'Them khong thanh cong. Da co loi xay ra';
            die();
            echo $exc->getTraceAsString();
        }
        return $result;
    }
    public function delete_autoid($id){
        $query ="DELETE FROM $this->_tablename WHERE id0=$id";
        try {
            if((int)$id >0){
                $__option = Business_Addon_Options::getInstance();
                $getData = $__option->get_data($query);
                $ret = $__option->doPost($this->_url,$getData,  $this->_authString);
                if($ret["error"]==-1){
                    $result=0;
                }else{
                    $result =1;
                }
            }
        } catch (Exception $exc) {
            echo 'Them khong thanh cong. Da co loi xay ra';
            die();
            echo $exc->getTraceAsString();
        }
        return $result;
    }

}

?>

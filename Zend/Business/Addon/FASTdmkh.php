<?php

class Business_Addon_FASTdmkh extends Business_Abstract {

    private $_tablename = 'hdbanhang_ck';
    private static $_instance = null;
    private $ma_kh;
    private $ten_kh;
    private $ten_kh2;
    private $dia_chi;
    private $ma_so_thue;
    private $doi_tac;
    private $ma_nvbh;
    private $tk;
    private $ma_tt;
    private $kh_yn =1;
    private $cc_yn = 0;
    private $nv_yn = 0;
    private $nh_kh1;
    private $nh_kh2;
    private $nh_kh3;
    private $dien_thoai;
    private $fax;
    private $e_mail;
    private $tk_nh;
    private $ngan_hang;
    private $tinh_thanh;
    private $ghi_chu;
    private $status = 1;
    private $_url = "http://sync.hnammobile.com/api/mssql/execute/query";
    private $_authString = "hnam:#hn@m16";
    function getMa_kh() {
        return $this->ma_kh;
    }

    function getTen_kh() {
        return $this->ten_kh;
    }

    function getTen_kh2() {
        return $this->ten_kh2;
    }

    function getDia_chi() {
        return $this->dia_chi;
    }

    function getMa_so_thue() {
        return $this->ma_so_thue;
    }

    function getDoi_tac() {
        return $this->doi_tac;
    }

    function getMa_nvbh() {
        return $this->ma_nvbh;
    }

    function getTk() {
        return $this->tk;
    }

    function getMa_tt() {
        return $this->ma_tt;
    }

    function getKh_yn() {
        return $this->kh_yn;
    }

    function getCc_yn() {
        return $this->cc_yn;
    }

    function getNv_yn() {
        return $this->nv_yn;
    }

    function getNh_kh1() {
        return $this->nh_kh1;
    }

    function getNh_kh2() {
        return $this->nh_kh2;
    }

    function getNh_kh3() {
        return $this->nh_kh3;
    }

    function getDien_thoai() {
        return $this->dien_thoai;
    }

    function getFax() {
        return $this->fax;
    }

    function getE_mail() {
        return $this->e_mail;
    }

    function getTk_nh() {
        return $this->tk_nh;
    }

    function getNgan_hang() {
        return $this->ngan_hang;
    }

    function getTinh_thanh() {
        return $this->tinh_thanh;
    }

    function getGhi_chu() {
        return $this->ghi_chu;
    }

    function getStatus() {
        return $this->status;
    }

    function setMa_kh($ma_kh) {
        $this->ma_kh = $ma_kh;
    }

    function setTen_kh($ten_kh) {
        $this->ten_kh = $ten_kh;
    }

    function setTen_kh2($ten_kh2) {
        $this->ten_kh2 = $ten_kh2;
    }

    function setDia_chi($dia_chi) {
        $this->dia_chi = $dia_chi;
    }

    function setMa_so_thue($ma_so_thue) {
        $this->ma_so_thue = $ma_so_thue;
    }

    function setDoi_tac($doi_tac) {
        $this->doi_tac = $doi_tac;
    }

    function setMa_nvbh($ma_nvbh) {
        $this->ma_nvbh = $ma_nvbh;
    }

    function setTk($tk) {
        $this->tk = $tk;
    }

    function setMa_tt($ma_tt) {
        $this->ma_tt = $ma_tt;
    }

    function setKh_yn($kh_yn) {
        $this->kh_yn = $kh_yn;
    }

    function setCc_yn($cc_yn) {
        $this->cc_yn = $cc_yn;
    }

    function setNv_yn($nv_yn) {
        $this->nv_yn = $nv_yn;
    }

    function setNh_kh1($nh_kh1) {
        $this->nh_kh1 = $nh_kh1;
    }

    function setNh_kh2($nh_kh2) {
        $this->nh_kh2 = $nh_kh2;
    }

    function setNh_kh3($nh_kh3) {
        $this->nh_kh3 = $nh_kh3;
    }

    function setDien_thoai($dien_thoai) {
        $this->dien_thoai = $dien_thoai;
    }

    function setFax($fax) {
        $this->fax = $fax;
    }

    function setE_mail($e_mail) {
        $this->e_mail = $e_mail;
    }

    function setTk_nh($tk_nh) {
        $this->tk_nh = $tk_nh;
    }

    function setNgan_hang($ngan_hang) {
        $this->ngan_hang = $ngan_hang;
    }

    function setTinh_thanh($tinh_thanh) {
        $this->tinh_thanh = $tinh_thanh;
    }

    function setGhi_chu($ghi_chu) {
        $this->ghi_chu = $ghi_chu;
    }

    function setStatus($status) {
        $this->status = $status;
    }
    function __construct() {
        
    }

    //public static function
    /**
     * get instance of Business_Addon_FASTdmkh
     *
     * @return Business_Addon_FASTdmkh
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
        $ma_kh = $this->getMa_kh();
        $ten_kh = $this->getTen_kh();
        $ten_kh2 = $this->getTen_kh2();
        $dia_chi = $this->getDia_chi();
        $ma_so_thue = $this->getMa_so_thue();
        $doi_tac = $this->getDoi_tac();
        $ma_nvbh = $this->getMa_nvbh();
        $tk = $this->getTk();
        $ma_tt = $this->getMa_tt();
        $kh_yn = $this->getKh_yn();
        $cc_yn = $this->getCc_yn();
        $nv_yn = $this->getNv_yn();
        $nh_kh1 = $this->getNh_kh1();
        $nh_kh2 = $this->getNh_kh2();
        $nh_kh3 = $this->getNh_kh3();
        $dien_thoai = $this->getDien_thoai();
        $fax = $this->getFax();
        $e_mail = $this->getE_mail();
        $tk_nh = $this->getTk_nh();
        $ngan_hang = $this->getNgan_hang();
        $tinh_thanh = $this->getTinh_thanh();
        $ghi_chu = $this->getGhi_chu();
        $status = $this->getStatus();
        $query = "INSERT INTO [FAST_HNAM_DBTG].[dbo].[dmkh]
           ([ma_kh]
           ,[ten_kh]
           ,[ten_kh2]
           ,[dia_chi]
           ,[ma_so_thue]
           ,[doi_tac]
           ,[ma_nvbh]
           ,[tk]
           ,[ma_tt]
           ,[kh_yn]
           ,[cc_yn]
           ,[nv_yn]
           ,[nh_kh1]
           ,[nh_kh2]
           ,[nh_kh3]
           ,[dien_thoai]
           ,[fax]
           ,[e_mail]
           ,[tk_nh]
           ,[ngan_hang]
           ,[tinh_thanh]
           ,[ghi_chu]
           ,[status])
     VALUES
           ('$ma_kh'
           ,N'$ten_kh'
           ,N'$ten_kh2'
           ,N'$dia_chi'
           ,'$ma_so_thue'
           ,'$doi_tac'
           ,'$ma_nvbh'
           ,'$tk'
           ,'$ma_tt'
           ,$kh_yn
           ,$cc_yn
           ,$nv_yn
           ,'$nh_kh1'
           ,'$nh_kh2'
           ,'$nh_kh3'
           ,'$dien_thoai'
           ,'$fax'
           ,'$e_mail'
           ,'$tk_nh'
           ,'$ngan_hang'
           ,'$tinh_thanh'
           ,'$ghi_chu'
           ,'$status')";
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

}

?>

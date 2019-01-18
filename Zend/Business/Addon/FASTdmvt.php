<?php

class Business_Addon_FASTdmvt extends Business_Abstract {

    private $_tablename = 'hdbanhang_ck';
    private static $_instance = null;
    private $ma_vt = 'NULL';
    private $ten_vt = 'NULL';
    private $ten_vt2 = 'NULL';
    private $dvt = 'NULL';
    private $nhieu_dvt = 'NULL';
    private $vt_ton_kho = 'NULL';
    private $lo_yn = 'NULL';
    private $kk_yn = 'NULL';
    private $gia_ton = 'NULL';
    private $loai_vt = 'NULL';
    private $nh_vt1 = 'NULL';
    private $nh_vt2 = 'NULL';
    private $nh_vt3 = 'NULL';
    private $nh_vt9 = 'NULL';
    private $ma_kho = 'NULL';
    private $ma_vi_tri = 'NULL';
    private $ma_thue = 'NULL';
    private $ma_thue_nk = 'NULL';
    private $tk_vt = 'NULL';
    private $sua_tk_vt = 'NULL';
    private $tk_gv = 'NULL';
    private $tk_dt = 'NULL';
    private $tk_tl = 'NULL';
    private $tk_ck = 'NULL';
    private $tk_cpbh = 'NULL';
    private $kieu_lo = 'NULL';
    private $cach_xuat = 'NULL';
    private $so_ngay_sp = 'NULL';
    private $so_ngay_bh = 'NULL';
    private $tao_lo = 'NULL';
    private $status = 'NULL';

    function getMa_vt() {
        return $this->ma_vt;
    }

    function getTen_vt() {
        return $this->ten_vt;
    }

    function getTen_vt2() {
        return $this->ten_vt2;
    }

    function getDvt() {
        return $this->dvt;
    }

    function getNhieu_dvt() {
        return $this->nhieu_dvt;
    }

    function getVt_ton_kho() {
        return $this->vt_ton_kho;
    }

    function getLo_yn() {
        return $this->lo_yn;
    }

    function getKk_yn() {
        return $this->kk_yn;
    }

    function getGia_ton() {
        return $this->gia_ton;
    }

    function getLoai_vt() {
        return $this->loai_vt;
    }

    function getNh_vt1() {
        return $this->nh_vt1;
    }

    function getNh_vt2() {
        return $this->nh_vt2;
    }

    function getNh_vt3() {
        return $this->nh_vt3;
    }

    function getNh_vt9() {
        return $this->nh_vt9;
    }

    function getMa_kho() {
        return $this->ma_kho;
    }

    function getMa_vi_tri() {
        return $this->ma_vi_tri;
    }

    function getMa_thue() {
        return $this->ma_thue;
    }

    function getMa_thue_nk() {
        return $this->ma_thue_nk;
    }

    function getTk_vt() {
        return $this->tk_vt;
    }

    function getSua_tk_vt() {
        return $this->sua_tk_vt;
    }

    function getTk_gv() {
        return $this->tk_gv;
    }

    function getTk_dt() {
        return $this->tk_dt;
    }

    function getTk_tl() {
        return $this->tk_tl;
    }

    function getTk_ck() {
        return $this->tk_ck;
    }

    function getTk_cpbh() {
        return $this->tk_cpbh;
    }

    function getKieu_lo() {
        return $this->kieu_lo;
    }

    function getCach_xuat() {
        return $this->cach_xuat;
    }

    function getSo_ngay_sp() {
        return $this->so_ngay_sp;
    }

    function getSo_ngay_bh() {
        return $this->so_ngay_bh;
    }

    function getTao_lo() {
        return $this->tao_lo;
    }

    function getStatus() {
        return $this->status;
    }

    function setMa_vt($ma_vt) {
        $this->ma_vt = $ma_vt;
    }

    function setTen_vt($ten_vt) {
        $this->ten_vt = $ten_vt;
    }

    function setTen_vt2($ten_vt2) {
        $this->ten_vt2 = $ten_vt2;
    }

    function setDvt($dvt) {
        $this->dvt = $dvt;
    }

    function setNhieu_dvt($nhieu_dvt) {
        $this->nhieu_dvt = $nhieu_dvt;
    }

    function setVt_ton_kho($vt_ton_kho) {
        $this->vt_ton_kho = $vt_ton_kho;
    }

    function setLo_yn($lo_yn) {
        $this->lo_yn = $lo_yn;
    }

    function setKk_yn($kk_yn) {
        $this->kk_yn = $kk_yn;
    }

    function setGia_ton($gia_ton) {
        $this->gia_ton = $gia_ton;
    }

    function setLoai_vt($loai_vt) {
        $this->loai_vt = $loai_vt;
    }

    function setNh_vt1($nh_vt1) {
        $this->nh_vt1 = $nh_vt1;
    }

    function setNh_vt2($nh_vt2) {
        $this->nh_vt2 = $nh_vt2;
    }

    function setNh_vt3($nh_vt3) {
        $this->nh_vt3 = $nh_vt3;
    }

    function setNh_vt9($nh_vt9) {
        $this->nh_vt9 = $nh_vt9;
    }

    function setMa_kho($ma_kho) {
        $this->ma_kho = $ma_kho;
    }

    function setMa_vi_tri($ma_vi_tri) {
        $this->ma_vi_tri = $ma_vi_tri;
    }

    function setMa_thue($ma_thue) {
        $this->ma_thue = $ma_thue;
    }

    function setMa_thue_nk($ma_thue_nk) {
        $this->ma_thue_nk = $ma_thue_nk;
    }

    function setTk_vt($tk_vt) {
        $this->tk_vt = $tk_vt;
    }

    function setSua_tk_vt($sua_tk_vt) {
        $this->sua_tk_vt = $sua_tk_vt;
    }

    function setTk_gv($tk_gv) {
        $this->tk_gv = $tk_gv;
    }

    function setTk_dt($tk_dt) {
        $this->tk_dt = $tk_dt;
    }

    function setTk_tl($tk_tl) {
        $this->tk_tl = $tk_tl;
    }

    function setTk_ck($tk_ck) {
        $this->tk_ck = $tk_ck;
    }

    function setTk_cpbh($tk_cpbh) {
        $this->tk_cpbh = $tk_cpbh;
    }

    function setKieu_lo($kieu_lo) {
        $this->kieu_lo = $kieu_lo;
    }

    function setCach_xuat($cach_xuat) {
        $this->cach_xuat = $cach_xuat;
    }

    function setSo_ngay_sp($so_ngay_sp) {
        $this->so_ngay_sp = $so_ngay_sp;
    }

    function setSo_ngay_bh($so_ngay_bh) {
        $this->so_ngay_bh = $so_ngay_bh;
    }

    function setTao_lo($tao_lo) {
        $this->tao_lo = $tao_lo;
    }

    function setStatus($status) {
        $this->status = $status;
    }
    function __construct() {
        
    }

    //public static function
    /**
     * get instance of Business_Addon_FASTdmvt
     *
     * @return Business_Addon_FASTdmvt
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
        $ma_vt = $this->getMa_vt();
        $ten_vt  = $this->getTen_vt();
        $ten_vt2  = $this->getTen_vt2();
        $dvt = $this->getDvt();
        $nhieu_dvt = $this->getNhieu_dvt();
        $vt_ton_kho = $this->getVt_ton_kho();
        $loai_vt = $this->getLoai_vt();
        $nh_vt1 = $this->getNh_vt1();
        $nh_vt2 = $this->getNh_vt2();
        $nh_vt3 = $this->getNh_vt3();
        $nh_vt9 = $this->getNh_vt9();
        $lo_yn = $this->getLo_yn();
        $ma_kho = $this->getMa_kho();
        $ma_vi_tri = $this->getMa_vi_tri();
        $ma_thue = $this->getMa_thue();
        $ma_thue_nk = $this->getMa_thue_nk();
        $kk_yn = $this->getKk_yn();
        $tk_vt = $this->getTk_vt();
        $sua_tk_vt = $this->getSua_tk_vt();
        $tk_gv = $this->getTk_gv();
        $tk_dt = $this->getTk_dt();
        $tk_tl = $this->getTk_tl();
        $tk_ck = $this->getTk_ck();
        $tk_cpbh = $this->getTk_cpbh();
        $kieu_lo = $this->getKieu_lo();
        $cach_xuat = $this->getCach_xuat();
        $so_ngay_sp = $this->getSo_ngay_sp();
        $so_ngay_bh = $this->getSo_ngay_bh();
        $tao_lo = $this->getTao_lo();
        $status = $this->getStatus();
        $query = "INSERT INTO [FAST_HNAM_DBTG].[dbo].[dmvt]
           ([ma_vt]
           ,[ten_vt]
           ,[ten_vt2]
           ,[dvt]
           ,[nhieu_dvt]
           ,[vt_ton_kho]
           ,[loai_vt]
           ,[nh_vt1]
           ,[nh_vt2]
           ,[nh_vt3]
           ,[nh_vt9]
           ,[lo_yn]
           ,[ma_kho]
           ,[ma_vi_tri]
           ,[ma_thue]
           ,[ma_thue_nk]
           ,[kk_yn]
           ,[tk_vt]
           ,[sua_tk_vt]
           ,[tk_gv]
           ,[tk_dt]
           ,[tk_tl]
           ,[tk_ck]
           ,[tk_cpbh]
           ,[kieu_lo]
           ,[cach_xuat]
           ,[so_ngay_sp]
           ,[so_ngay_bh]
           ,[tao_lo]
           ,[status])
     VALUES
           ($ma_vt
           ,$ten_vt
           ,$ten_vt2
           ,$dvt
           ,$nhieu_dvt
           ,$vt_ton_kho
           ,$loai_vt
           ,$nh_vt1
           ,$nh_vt2
           ,$nh_vt3
           ,$nh_vt9
           ,$lo_yn
           ,$ma_kho
           ,$ma_vi_tri
           ,$ma_thue
           ,$ma_thue_nk
           ,$kk_yn
           ,$tk_vt
           ,$sua_tk_vt
           ,$tk_gv
           ,$tk_dt
           ,$tk_tl
           ,$tk_ck
           ,$tk_cpbh
           ,$kieu_lo
           ,$cach_xuat
           ,$so_ngay_sp
           ,$so_ngay_bh
           ,$tao_lo
           ,$status)";
        try {
            $result = $db->excute($query);
            if($result ==FALSE){
                echo 'Them khong thanh cong. Da co loi xay ra';
                die();
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

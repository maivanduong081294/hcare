<?php

class Business_Addon_FASThdbanhang extends Business_Abstract {

    private $_tablename = '[FAST_HNAM_DBTG].[dbo].[hdbanhang]';
    private static $_instance = null;
    private $id=0;
    private $ma_kh ;
    private $ong_ba ;
    private $tk ;
    private $ma_tt ;
    private $dien_giai ;
    private $so_bil ;
    private $so_ct ;
    private $so_seri ;
    private $ngay_lct ;
    private $ngay_ct ;
    private $ma_nt ;
    private $ty_gia=0 ;
    private $td_kh ;
    
    private $id0=0 ;
    private $ma_vt ;
    private $dvt ;
    private $km_yn=0 ;
    private $ma_kho ;
    private $ma_vi_tri ;
    private $ma_lo ;
    private $so_luong=0 ;
    private $gia_bill=0;
    private $gia2=0 ;
    private $tien2=0 ;
    private $tien_mat=0 ;
    private $chuyen_khoan=0 ;
    private $tra_gop=0 ;
    private $ca_the=0 ;
    private $ck=0 ;
    private $ma_bp ;
    private $gc_chuyen_khoan ;
    private $gc_tra_gop ;
    private $gc_ca_the ;
    private $ma_thue ;
    private $_url = "http://sync.hnammobile.com/api/mssql/execute/query";
    private $_authString = "hnam:#hn@m16";
    
    private $xstatus ;
    private $tk_chuyen_khoan;
    private $tk_tra_gop ;
    private $tk_ca_the ;
    private $tien_vc=0 ;
    private $tk_vc ;
    private $tien_gift=0 ;
    private $tk_gift ;
//     
//    function getStatus() {
//        return $this->status;
//    }
//
//    function setStatus($status) {
//        if($status != NULL){
//            $this->status = $status;
//        }
//    }
    function getTk_chuyen_khoan() {
        return $this->tk_chuyen_khoan;
    }

    function getTk_tra_gop() {
        return $this->tk_tra_gop;
    }

    function getTk_ca_the() {
        return $this->tk_ca_the;
    }

    function setTk_chuyen_khoan($tk_chuyen_khoan) {
        $this->tk_chuyen_khoan = $tk_chuyen_khoan;
    }

    function setTk_tra_gop($tk_tra_gop) {
        $this->tk_tra_gop = $tk_tra_gop;
    }

    function setTk_ca_the($tk_ca_the) {
        $this->tk_ca_the = $tk_ca_the;
    }

        function getId() {
        return $this->id;
    }

    function getMa_kh() {
        return $this->ma_kh;
    }

    function getOng_ba() {
        return $this->ong_ba;
    }

    function getTk() {
        return $this->tk;
    }

    function getMa_tt() {
        return $this->ma_tt;
    }

    function getDien_giai() {
        return $this->dien_giai;
    }

    function getSo_bil() {
        return $this->so_bil;
    }

    function getSo_ct() {
        return $this->so_ct;
    }

    function getSo_seri() {
        return $this->so_seri;
    }

    function getNgay_lct() {
        return $this->ngay_lct;
    }

    function getNgay_ct() {
        return $this->ngay_ct;
    }

    function getMa_nt() {
        return $this->ma_nt;
    }

    function getTy_gia() {
        return (int)$this->ty_gia;
    }

    function getTd_kh() {
        return $this->td_kh;
    }

    function getId0() {
        return (int)$this->id0;
    }

    function getMa_vt() {
        return $this->ma_vt;
    }

    function getDvt() {
        return $this->dvt;
    }

    function getKm_yn() {
        return $this->km_yn;
    }

    function getMa_kho() {
        return $this->ma_kho;
    }

    function getMa_vi_tri() {
        return $this->ma_vi_tri;
    }

    function getMa_lo() {
        return $this->ma_lo;
    }

    function getSo_luong() {
        return (int)$this->so_luong;
    }

    function getGia2() {
        return (int)$this->gia2;
    }

    function getTien2() {
        return (int)$this->tien2;
    }

    function getTien_mat() {
        return (int)$this->tien_mat;
    }

    function getChuyen_khoan() {
        return (int)$this->chuyen_khoan;
    }

    function getTra_gop() {
        return (int)$this->tra_gop;
    }

    function getCa_the() {
        return (int)$this->ca_the;
    }

    function getCk() {
        return (int)$this->ck;
    }

    function getMa_bp() {
        return $this->ma_bp;
    }

    function getGc_chuyen_khoan() {
        return $this->gc_chuyen_khoan;
    }

    function getGc_tra_gop() {
        return $this->gc_tra_gop;
    }

    function getGc_ca_the() {
        return $this->gc_ca_the;
    }

    function getMa_thue() {
        return $this->ma_thue;
    }

    function getXstatus() {
        return $this->xstatus;
    }
    function getGia_bill() {
        return $this->gia_bill;
    }
    function setId($id) {
        $this->id = $id;
    }

    function setMa_kh($ma_kh) {
        $this->ma_kh = $ma_kh;
    }

    function setOng_ba($ong_ba) {
        $this->ong_ba = $ong_ba;
    }

    function setTk($tk) {
        $this->tk = $tk;
    }

    function setMa_tt($ma_tt) {
        $this->ma_tt = $ma_tt;
    }

    function setDien_giai($dien_giai) {
        $this->dien_giai = $dien_giai;
    }

    function setSo_bil($so_bil) {
        $this->so_bil = $so_bil;
    }

    function setSo_ct($so_ct) {
        $this->so_ct = $so_ct;
    }

    function setSo_seri($so_seri) {
        $this->so_seri = $so_seri;
    }

    function setNgay_lct($ngay_lct) {
        $this->ngay_lct = $ngay_lct;
    }

    function setNgay_ct($ngay_ct) {
        $this->ngay_ct = $ngay_ct;
    }

    function setMa_nt($ma_nt) {
        $this->ma_nt = $ma_nt;
    }

    function setTy_gia($ty_gia) {
        $this->ty_gia = $ty_gia;
    }

    function setTd_kh($td_kh) {
        $this->td_kh = $td_kh;
    }

    function setId0($id0) {
        $this->id0 = $id0;
    }

    function setMa_vt($ma_vt) {
        $this->ma_vt = $ma_vt;
    }

    function setDvt($dvt) {
        $this->dvt = $dvt;
    }

    function setKm_yn($km_yn) {
        $this->km_yn = $km_yn;
    }

    function setMa_kho($ma_kho) {
        $this->ma_kho = $ma_kho;
    }

    function setMa_vi_tri($ma_vi_tri) {
        $this->ma_vi_tri = $ma_vi_tri;
    }

    function setMa_lo($ma_lo) {
        $this->ma_lo = $ma_lo;
    }

    function setSo_luong($so_luong) {
        $this->so_luong = $so_luong;
    }

    function setGia_bill($gia_bill) {
        $this->gia_bill = $gia_bill;
    }

    function setGia2($gia2) {
        $this->gia2 = $gia2;
    }

    function setTien2($tien2) {
        $this->tien2 = $tien2;
    }

    function setTien_mat($tien_mat) {
        $this->tien_mat = $tien_mat;
    }

    function setChuyen_khoan($chuyen_khoan) {
        $this->chuyen_khoan = $chuyen_khoan;
    }

    function setTra_gop($tra_gop) {
        $this->tra_gop = $tra_gop;
    }

    function setCa_the($ca_the) {
        $this->ca_the = $ca_the;
    }

    function setCk($ck) {
        $this->ck = $ck;
    }

    function setMa_bp($ma_bp) {
        $this->ma_bp = $ma_bp;
    }

    function setGc_chuyen_khoan($gc_chuyen_khoan) {
        $this->gc_chuyen_khoan = $gc_chuyen_khoan;
    }

    function setGc_tra_gop($gc_tra_gop) {
        $this->gc_tra_gop = $gc_tra_gop;
    }

    function setGc_ca_the($gc_ca_the) {
        $this->gc_ca_the = $gc_ca_the;
    }

    function setMa_thue($ma_thue) {
        $this->ma_thue = $ma_thue;
    }

    function setXstatus($xstatus) {
        $this->xstatus = $xstatus;
    
    }
    function getTien_vc() {
        return $this->tien_vc;
    }

    function getTk_vc() {
        return $this->tk_vc;
    }

    function getTien_gift() {
        return $this->tien_gift;
    }

    function getTk_gift() {
        return $this->tk_gift;
    }

    function setTien_vc($tien_vc) {
        $this->tien_vc = $tien_vc;
    }

    function setTk_vc($tk_vc) {
        $this->tk_vc = $tk_vc;
    }

    function setTien_gift($tien_gift) {
        $this->tien_gift = $tien_gift;
    }

    function setTk_gift($tk_gift) {
        $this->tk_gift = $tk_gift;
    }

            
    function __construct() {
        
    }

    //public static function
    /**
     * get instance of Business_Addon_FASThdbanhang
     *
     * @return Business_Addon_FASThdbanhang
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
        $ma_kh = $this->getMa_kh();
        $ong_ba = $this->getOng_ba();
        $tk = $this->getTk();
        $ma_tt = $this->getMa_tt();
        $dien_giai = $this->getDien_giai();
        $so_bil = $this->getSo_bil();
        $so_ct = $this->getSo_ct();
        $so_seri = $this->getSo_seri();
        $ngay_lct = $this->getNgay_lct();
        $ngay_ct = $this->getNgay_ct();
        $ma_nt = $this->getMa_nt();
        $ty_gia = $this->getTy_gia();
        $td_kh = $this->getTd_kh();
        
        $id0 = $this->getId0();
        $ma_vt = $this->getMa_vt();
        $dvt = $this->getDvt();
        $km_yn = $this->getKm_yn();
        $ma_kho = $this->getMa_kho();
        $ma_vi_tri = $this->getMa_vi_tri();
        $ma_lo = $this->getMa_lo();
        $so_luong = $this->getSo_luong();
        $gia2 = $this->getGia2();
        $tien2 = $this->getTien2();
        $tien_mat = $this->getTien_mat();
        $chuyen_khoan = $this->getChuyen_khoan();
        
        $tra_gop = $this->getTra_gop();
        $ca_the = $this->getCa_the();
        $ck = $this->getCk();
        $ma_bp = $this->getMa_bp();
        $gc_chuyen_khoan = $this->getGc_chuyen_khoan();
        $gc_tra_gop = $this->getGc_tra_gop();
        $gc_ca_the = $this->getGc_ca_the();
        $ma_thue = $this->getMa_thue();
        $xstatus = $this->getXstatus();
        $tk_chuyen_khoan = $this->getTk_chuyen_khoan();
        $tk_tra_gop = $this->getTk_tra_gop();
        $tk_ca_the = $this->getTk_ca_the();
        $tien_vc = $this->getTien_vc();
        $tk_vc = $this->getTk_vc();
        $tien_gif = $this->getTien_gift();
        $tk_gif = $this->getTk_gift();
        $query="INSERT INTO [FAST_HNAM_DBTG].[dbo].[hdbanhang]
           ([id]
           ,[ma_kh]
           ,[ong_ba]
           ,[tk]
           ,[ma_tt]
           ,[dien_giai]
           ,[so_bil]
           ,[so_ct]
           ,[so_seri]
           ,[ngay_lct]
           ,[ngay_ct]
           ,[ma_nt]
           ,[ty_gia]
           ,[td_kh]
           ,[id0]
           ,[ma_vt]
           ,[dvt]
           ,[km_yn]
           ,[ma_kho]
           ,[ma_vi_tri]
           ,[ma_lo]
           ,[so_luong]
           ,[gia2]
           ,[tien2]
           ,[tien_mat]
           ,[chuyen_khoan]
           ,[tra_gop]
           ,[ca_the]
           ,[ck]
           ,[ma_bp]
           ,[gc_chuyen_khoan]
           ,[gc_tra_gop]
           ,[gc_ca_the]
           ,[ma_thue]
           ,[xstatus]
           ,[tk_chuyen_khoan]
           ,[tk_tra_gop]
            ,[tk_ca_the]
            ,[tien_vc]
            ,[tk_vc]
            ,[tien_gift]
            ,[tk_gift])
     VALUES
           ($id
           ,'$ma_kh'
           ,N'$ong_ba'
           ,'$tk'
           ,'$ma_tt'
           ,N'$dien_giai'
           ,'$so_bil'
           ,'$so_ct'
           ,'$so_seri'
           ,'$ngay_lct'
           ,'$ngay_ct'
           ,'$ma_nt'
           ,$ty_gia
           ,'$td_kh'
           ,$id0
           ,'$ma_vt'
           ,'$dvt'
           ,$km_yn
           ,'$ma_kho'
           ,'$ma_vi_tri'
           ,'$ma_lo'
           ,$so_luong
           ,$gia2
           ,$tien2
           ,$tien_mat
           ,$chuyen_khoan
           ,$tra_gop
           ,$ca_the
           ,$ck
           ,'$ma_bp'
           ,N'$gc_chuyen_khoan'
           ,N'$gc_tra_gop'
           ,N'$gc_ca_the'
           ,'$ma_thue'
           ,'$xstatus'
           ,'$tk_chuyen_khoan'
           ,'$tk_tra_gop'
           ,'$tk_ca_the',$tien_vc,N'$tk_vc',$tien_gif,N'$tk_gif')";
//        if($id ==474918){
//            echo "<pre>";
//            var_dump($query);
//            die();
//        }
        $result =0;
        try {
            $__option = Business_Addon_Options::getInstance();
            $getData = $__option->get_data($query);
            $ret = $__option->doPost($this->_url,$getData,  $this->_authString);
            if($ret["error"]==-1){
                $result=0;
            }else{
                $result=1;
            }
//            $result = (int)$db->excute($query);
        } catch (Exception $exc) {
            echo 'Them khong thanh cong. Da co loi xay ra';
            die();
            echo $exc->getTraceAsString();
        }
        return $result;
    }
    public function get_group_list_by_id($strID="",$start,$end){
        $db = $this->getDbConnection();
        $query = "select id,count(*) as total,sum(tien_mat) as s_tien_mat,sum(chuyen_khoan) as s_chuyen_khoan,sum(tra_gop) as s_tra_gop,sum(ca_the) as s_ca_the from $this->_tablename where id IN ($strID)";
        if($start != NULL){
            $query .=" and ngay_lct > '$start' ";
        }
        if($end != NULL){
            $query .=" and ngay_lct <= '$end' ";
        }
        $query .=" group by id ";
        $result = $db->select2($query);
        return $result;
    }
    public function get_list_by_id($strID=""){
        $db = $this->getDbConnection();
        $query = "select * from $this->_tablename where id IN ($strID)";
        $result = $db->select($query);
        return $result;
    }
    public function get_list_by_id2($strID=""){
        $db = $this->getDbConnection();
        $query = "select * from $this->_tablename where id IN ($strID)";
        $result = $db->select2($query);
        return $result;
    }
    public function count_hnam_fast($start,$end){
        $cache = $this->getCacheInstance();
        $key ="count_hnam_fast".$this->_tablename.$start.$end;
        $result = $cache->getCache($key);
        if($result ===FALSE){
            $db = $this->getDbConnection();
            $query = "select count(*) as total,id from $this->_tablename  where  1=1 ";
            if($start != NULL){
                $query .=" and ngay_lct > '$start' ";
            }
            if($end != NULL){
                $query .=" and ngay_lct <= '$end' ";
            }
            $query .=" group by id ";
            $result = $db->select2($query);
            if(!is_null($result) && is_array($result)){
                $cache->setCache($key, $result,300);
            }
        }
        return $result;
    }
    public function get_list_by_imei($str_imei=""){
        $db = $this->getDbConnection();
        $query = "select * from $this->_tablename where ma_lo ='$str_imei' and xstatus=0 ";
        $result = $db->select($query);
        return $result;
    }
    public function get_list_by_imei2($str_imei="",$date=""){
        $db = $this->getDbConnection();
        $query = "select * from $this->_tablename where ma_lo ='$str_imei'";
        if($date != NULL){
            $query .=" and DATE(ngay_lct) = '$date'";
        }
        $query .=" order by id desc";
        $result = $db->select($query);
        return $result;
    }
    public function get_list_by_date_nocache($start="",$end="",$strID=""){
        $cache = $this->getCacheInstance();
        $key ="get_list_by_date_nocache".$this->_tablename.$start.$end.$strID;
        $result = $cache->getCache($key);
        $result = FALSE;
        if($result ===FALSE){
            $db = $this->getDbConnection();
            $query = "select * from $this->_tablename where 1=1 ";
            if($start != NULL){
                $query .=" and ngay_lct > '$start' ";
            }
            if($end != NULL){
                $query .=" and ngay_lct <= '$end' ";
            }
            if($strID != NULL){
                $query .=" and id IN ($strID) ";
            }
            $result = $db->select2($query);
            if(!is_null($result) && is_array($result)){
                $cache->setCache($key, $result,120);
            }
        }
        
        return $result;
    }
    public function get_list_by_date($start="",$end="",$strID=""){
        $cache = $this->getCacheInstance();
        $key ="get_list_by_date".$this->_tablename.$start.$end.$strID;
        $result = $cache->getCache($key);
        if($result ===FALSE){
            $db = $this->getDbConnection();
            $query = "select * from $this->_tablename where 1=1 ";
            if($start != NULL){
                $query .=" and ngay_lct > '$start' ";
            }
            if($end != NULL){
                $query .=" and ngay_lct <= '$end' ";
            }
            if($strID != NULL){
                $query .=" and id IN ($strID) ";
            }
            $result = $db->select2($query);
            if(!is_null($result) && is_array($result)){
                $cache->setCache($key, $result,120);
            }
        }
        
        return $result;
    }
    public function get_list_by_date2($start="",$end="",$strID=""){
        $cache = $this->getCacheInstance();
        $key ="get_list_by_date2".$this->_tablename.$start.$end.$strID;
        $result = $cache->getCache($key);
        if($result ===FALSE){
            $db = $this->getDbConnection();
            $query = "select DISTINCT id from $this->_tablename where 1=1 ";
            if($start != NULL){
                $query .=" and ngay_lct > '$start' ";
            }
            if($end != NULL){
                $query .=" and ngay_lct <= '$end' ";
            }
            if($strID != NULL){
                $query .=" and id IN ($strID) ";
            }
            $query .=" group by id ";
            $result = $db->select2($query);
            if(!is_null($result) && is_array($result)){
                $cache->setCache($key, $result,120);
            }
        }
        return $result;
    }
    public function get_detail_by_date($start="",$end="",$strID=""){
        $cache = $this->getCacheInstance();
        $key ="get_detail_by_date".$this->_tablename.$start.$end.$strID;
        $result = $cache->getCache($key);
        $result = FALSE;
        if($result ===FALSE){
            $db = $this->getDbConnection();
            $query = "select DISTINCT id from $this->_tablename where 1=1 ";
            if($start != NULL){
                $query .=" and ngay_lct > '$start' ";
            }
            if($end != NULL){
                $query .=" and ngay_lct <= '$end' ";
            }
            if($strID != NULL){
                $query .=" and id IN ($strID) ";
            }
            $query .=" group by id ";
            $result = $db->select2($query);
            $result = $result[0];
            if(!is_null($result) && is_array($result)){
                $cache->setCache($key, $result,120);
            }
        }
        return $result;
    }
    public function delete_dbtg($id){
        $query ="DELETE FROM [FAST_HNAM_DBTG].[dbo].[hdbanhang] WHERE id IN ($id)";
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

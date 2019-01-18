<?php

class Business_Addon_FASThdmuahang extends Business_Abstract {

    private $_tablename = '[FAST_HNAM_DBTG].[dbo].[hdmuahang]';
    private static $_instance = null;
    private $id = '';
    private $ma_kh = '';
    private $ong_ba = '';
    private $tk = '';
    private $ma_tt = '';
    private $dien_giai = '';
    private $so_bil = '';
    private $loai_mua = '';
    private $so_ct = '';
    private $ngay_lct = '';
    private $ngay_ct = '';
    private $ma_nt = '';
    private $ty_gia = 1;
    private $td_kh = '';
    private $status = '';
    
    private $id0 = '';
    private $ma_vt = '';
    private $dvt = '';
    private $ma_kho = '';
    private $ma_vi_tri = '';
    private $ma_lo = '';
    private $so_luong = '';
    private $gia0 = '';
    private $tien0 = '';
    private $ma_bp = '';
    private $xstatus = 1;
    
    private $_url = "http://sync.hnammobile.com/api/mssql/execute/query";
    private $_authString = "hnam:#hn@m16";
    
    function getId() {
        return $this->id;
    }
    function getMa_kh() {
        if($this->ma_kh !='NULL'){
            $this->ma_kh="'$this->ma_kh'";
        }
        return $this->ma_kh;
    }
    function getOng_ba() {
        if($this->ong_ba !='NULL'){
            $this->ong_ba="'$this->ong_ba'";
        }
        return $this->ong_ba;
    }
    function getTk() {
        if($this->tk !='NULL'){
            $this->tk="'$this->tk'";
        }
        return $this->tk;
    }

    function getMa_tt() {
        if($this->ma_tt !='NULL'){
            $this->ma_tt="'$this->ma_tt'";
        }
        return $this->ma_tt;
    }

    function getDien_giai() {
        if($this->dien_giai !='NULL'){
            $this->dien_giai="'$this->dien_giai'";
        }
        return $this->dien_giai;
    }

    function getSo_bil() {
        if($this->so_bil !='NULL'){
            $this->so_bil="'$this->so_bil'";
        }
        return $this->so_bil;
    }

    function getLoai_mua() {
        if($this->loai_mua !='NULL'){
            $this->loai_mua="'$this->loai_mua'";
        }
        return $this->loai_mua;
    }

    function getSo_ct() {
        if($this->so_ct !='NULL'){
            $this->so_ct="'$this->so_ct'";
        }
        return $this->so_ct;
    }

    function getNgay_lct() {
        if($this->ngay_lct !='NULL'){
            $this->ngay_lct="'$this->ngay_lct'";
        }
        return $this->ngay_lct;
    }

    function getNgay_ct() {
        if($this->ngay_ct !='NULL'){
            $this->ngay_ct="'$this->ngay_ct'";
        }
        return $this->ngay_ct;
    }

    function getMa_nt() {
        if($this->ma_nt !='NULL'){
            $this->ma_nt="'$this->ma_nt'";
        }
        return $this->ma_nt;
    }

    function getTy_gia() {
        return $this->ty_gia;
    }

    function getTd_kh() {
        if($this->td_kh !='NULL'){
            $this->td_kh="'$this->td_kh'";
        }
        return $this->td_kh;
    }

    function getStatus() {
        if($this->status !='NULL'){
            $this->status="'$this->status'";
        }
        return $this->status;
    }

    function getId0() {
        return $this->id0;
    }

    function getMa_vt() {
        if($this->ma_vt !='NULL'){
            $this->ma_vt="'$this->ma_vt'";
        }
        return $this->ma_vt;
    }

    function getDvt() {
        if($this->dvt !='NULL'){
            $this->dvt="'$this->dvt'";
        }
        return $this->dvt;
    }

    function getMa_kho() {
        if($this->ma_kho !='NULL'){
            $this->ma_kho="'$this->ma_kho'";
        }
        return $this->ma_kho;
    }

    function getMa_vi_tri() {
        if($this->ma_vi_tri !='NULL'){
            $this->ma_vi_tri="'$this->ma_vi_tri'";
        }
        return $this->ma_vi_tri;
    }

    function getMa_lo() {
        if($this->ma_lo !='NULL'){
            $this->ma_lo="'$this->ma_lo'";
        }
        return $this->ma_lo;
    }

    function getSo_luong() {
        return $this->so_luong;
    }

    function getGia0() {
        return $this->gia0;
    }

    function getTien0() {
        return $this->tien0;
    }

    function getMa_bp() {
        if($this->ma_bp !='NULL'){
            $this->ma_bp="'$this->ma_bp'";
        }
        return $this->ma_bp;
    }

    function getXstatus() {
        if($this->xstatus !='NULL'){
            $this->xstatus="'$this->xstatus'";
        }
        return $this->xstatus;
    }

    function setId($id) {
        if($id != NULL){
            $this->id = $id;
        }
    }

    function setMa_kh($ma_kh) {
        if($ma_kh != NULL){
            $this->ma_kh = $ma_kh;
        }
        
    }

    function setOng_ba($ong_ba) {
        if($ong_ba != NULL){
            $this->ong_ba = $ong_ba;
        }
    }

    function setTk($tk) {
        if($tk != NULL){
            $this->tk = $tk;
        }
    }

    function setMa_tt($ma_tt) {
        if($ma_tt != NULL){
            $this->ma_tt = $ma_tt;
        }
    }

    function setDien_giai($dien_giai) {
        if($dien_giai != NULL){
            $this->dien_giai = $dien_giai;
        }
    }

    function setSo_bil($so_bil) {
        if($so_bil != NULL){
            $this->so_bil = $so_bil;
        }
    }

    function setLoai_mua($loai_mua) {
        if($loai_mua != NULL){
            $this->loai_mua = $loai_mua;
        }
    }

    function setSo_ct($so_ct) {
        if($so_ct != NULL){
            $this->so_ct = $so_ct;
        }
    }

    function setNgay_lct($ngay_lct) {
        if($ngay_lct != NULL){
            $this->ngay_lct = $ngay_lct;
        }
    }

    function setNgay_ct($ngay_ct) {
        if($ngay_ct != NULL){
            $this->ngay_ct = $ngay_ct;
        }
    }

    function setMa_nt($ma_nt) {
        if($ma_nt != NULL){
           $this->ma_nt = $ma_nt; 
        }
    }

    function setTy_gia($ty_gia) {
        if($ty_gia != NULL){
            $this->ty_gia = $ty_gia;
        }
    }

    function setTd_kh($td_kh) {
        if($td_kh != NULL){
            $this->td_kh = $td_kh;
        }
        
    }

    function setStatus($status) {
        if($status != NULL){
            $this->status = $status;
        }
    }

    function setId0($id0) {
        if($id0 != NULL){
            $this->id0 = $id0;
        }
        
    }
    function setMa_vt($ma_vt) {
        if($ma_vt != NULL){
            $this->ma_vt = $ma_vt;
        }
    }
    function setDvt($dvt) {
        if($dvt != NULL){
            $this->dvt = $dvt;
        }
    }
    function setMa_kho($ma_kho) {
        if($ma_kho != NULL){
            $this->ma_kho = $ma_kho;
        }
        
    }

    function setMa_vi_tri($ma_vi_tri) {
        if($ma_vi_tri != NULL){
            $this->ma_vi_tri = $ma_vi_tri;
        }
    }

    function setMa_lo($ma_lo) {
        if($ma_lo != NULL){
            $this->ma_lo = $ma_lo;
        }
    }

    function setSo_luong($so_luong) {
        if($so_luong != NULL){
            $this->so_luong = $so_luong;
        }
    }

    function setGia0($gia0) {
        if($gia0 != NULL){
            $this->gia0 = $gia0;
        }
    }

    function setTien0($tien0) {
        if($tien0 != NULL){
            $this->tien0 = $tien0;
        }
    }

    function setMa_bp($ma_bp) {
        if($ma_bp != NULL){
            $this->ma_bp = $ma_bp;
        }
    }

    function setXstatus($xstatus) {
        if($xstatus != NULL){
            $this->xstatus = $xstatus;
        }
    }
    function __construct() {
        
    }

    //public static function
    /**
     * get instance of Business_Addon_FASThdmuahang
     *
     * @return Business_Addon_FASThdmuahang
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
    public function get_list_by_id2($strID=""){
        $db = $this->getDbConnection();
        $query = "select * from $this->_tablename where id IN ($strID)";
        $result = $db->select2($query);
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
    public function insert() {
        $db = $this->getDbConnection();
        $id = $this->getId();
        $ma_kh = $this->getMa_kh();
        $ong_ba = $this->getOng_ba();
        $tk = $this->getTk();
        $ma_tt = $this->getMa_tt();
        $dien_giai = $this->getDien_giai();
        $so_bil = $this->getSo_bil();
        $loai_mua = $this->getLoai_mua();
        $so_ct = $this->getSo_ct();
        $ngay_lct = $this->getNgay_lct();
        $ngay_ct = $this->getNgay_ct();
        $ma_nt = $this->getMa_nt();
        $ty_gia = $this->getTy_gia();
        $td_kh = $this->getTd_kh();
        $status = $this->getStatus();
        $id0 = $this->getId0();
        $ma_vt = $this->getMa_vt();
        $dvt = $this->getDvt();
        $ma_kho = $this->getMa_kho();
        $ma_vi_tri = $this->getMa_vi_tri();
        $ma_lo = $this->getMa_lo();
        $so_luong = $this->getSo_luong();
        $gia0= $this->getGia0();
        $tien0 = $this->getTien0();
        $ma_bp = $this->getMa_bp();
        $xstatus = $this->getXstatus();
        
        $query = "INSERT INTO [FAST_HNAM_DBTG].[dbo].[hdmuahang]
           ([id]
           ,[ma_kh]
           ,[ong_ba]
           ,[tk]
           ,[ma_tt]
           ,[dien_giai]
           ,[so_bil]
           ,[loai_mua]
           ,[so_ct]
           ,[ngay_lct]
           ,[ngay_ct]
           ,[ma_nt]
           ,[ty_gia]
           ,[td_kh]
           ,[status]
           ,[id0]
           ,[ma_vt]
           ,[dvt]
           ,[ma_kho]
           ,[ma_vi_tri]
           ,[ma_lo]
           ,[so_luong]
           ,[gia0]
           ,[tien0]
           ,[ma_bp]
           ,[xstatus])
     VALUES
           ($id
           ,$ma_kh
           ,N$ong_ba
           ,$tk
           ,$ma_tt
           ,N$dien_giai
           ,$so_bil
           ,$loai_mua
           ,$so_ct
           ,$ngay_lct
           ,$ngay_ct
           ,$ma_nt
           ,$ty_gia
           ,$td_kh
           ,2
           ,$id0
           ,$ma_vt
           ,$dvt
           ,$ma_kho
           ,$ma_vi_tri
           ,$ma_lo
           ,$so_luong
           ,$gia0
           ,$tien0
           ,$ma_bp
           ,0)";
        try {
            $__option = Business_Addon_Options::getInstance();
            $getData = $__option->get_data($query);
            $ret = $__option->doPost($this->_url,$getData,  $this->_authString);
            if($ret["error"]==-1){
                $result=0;
            }else{
                $result =1;
            }
//            $result = (int)$db->excute($query);
            if($result ==0){
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
    public function get_list_by_loai_mua($loai_mua,$month,$year){
        $db = $this->getDbConnection();
        $query = "SELECT count(*) as total FROM $this->_tablename where loai_mua =$loai_mua and month(ngay_lct)=$month and year(ngay_lct)=$year";
        $result = $db->select($query);
        return $result;
    }
    public function get_list_by_id($strID){
        $cache = $this->getCacheInstance();
        $key ="get_list_by_id".$this->_tablename.$strID;
        $result = $cache->getCache($key);
        if($result ===FALSE){
            $db = $this->getDbConnection();
            $query = "SELECT * FROM $this->_tablename where id IN ($strID)";
            $result = $db->select2($query);
            if(!is_null($result) && is_array($result)){
                $cache->setCache($key, $result,120);
            }
        }
        return $result;
    }
    public function delete_dbtg($id){
        $query ="DELETE FROM [FAST_HNAM_DBTG].[dbo].[hdmuahang] WHERE id IN ($id)";
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
    
    public function update_dbtg_vt($id, $price, $note=""){
		        
        try {
            if((int)$id >0 && (int) $price > 0){	
				$price = $price * 1000;
				//update DBTG
					$query ="UPDATE [FAST_HNAM_DBTG].[dbo].[hdmuahang] SET gia0=$price, tien0=$price, xstatus=0  WHERE id IN ($id)";			
					
					$__option = Business_Addon_Options::getInstance();				
					$getData = $__option->get_data($query);
					$ret = $__option->doPost($this->_url,$getData,  $this->_authString);
					
					//end udpate DBTG	
					
				//delete hdmh from Fast
					$detail = Business_Addon_Purchase::getInstance()->get_detail($id);
					
					$date = date('Ymd',  strtotime($detail["datetime"]));
				
					$__config = Globals::getConfig('fast')->db->conn;
					$config = explode(";", $__config);
					$host = $config[0];
					$user = $config[1];
					$pass = $config[2];
					
					$conn = mssql_connect($host, $user, $pass);               
					
					$proc = mssql_init("HNAM_NB_A.dbo.zc_DeleteTranfer 2, '$id', '$date'", $conn);
					
					$proc_result = mssql_execute($proc);                
					mssql_free_statement($proc);
					unset($proc);
				//end delete hdmh from Fast
				
				//update hoa don mua hang hpos
				$query ="UPDATE `hnam_live`.`hnam_purchase` SET price=$price, note= CONCAT(note, '$note')  WHERE id IN ($id)";				
				Business_Addon_Addon::getInstance()->execute($query);
				
				
                if($ret["error"]==-1){
                    $result=0;
                }else{
                    $result =1;
                }
            }
        } catch (Exception $exc) {
            return $exc->getTraceAsString();       
            //echo $exc->getTraceAsString();
			die();
        }
		return $result;
    }

}

?>

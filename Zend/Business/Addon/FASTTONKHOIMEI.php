<?php

class Business_Addon_FASTTONKHOIMEI extends Business_Abstract {

    private $_tablename = '[FAST_HNAM_DBTG].[dbo].[tonkhoimei]';
    private static $_instance = null;
    private $ma_lo = 'NULL';
    private $ma_kho = 'NULL';
    private $ma_vi_tri = 'NULL';
    private $ma_bp = 'NULL';
    private $sl_ton = 'NULL';
    private $ma_vt = 'NULL';
    function getMa_vt() {
        return $this->ma_vt;
    }

    function setMa_vt($ma_vt) {
        $this->ma_vt = $ma_vt;
    }

    function getMa_lo() {
        return $this->ma_lo;
    }
    function getMa_kho() {
        return $this->ma_kho;
    }

    function getMa_vi_tri() {
        return $this->ma_vi_tri;
    }

    function getMa_bp() {
        return $this->ma_bp;
    }

    function getSl_ton() {
        return $this->sl_ton;
    }

    function setMa_lo($ma_lo) {
        $this->ma_lo = $ma_lo;
    }

    function setMa_kho($ma_kho) {
        $this->ma_kho = $ma_kho;
    }

    function setMa_vi_tri($ma_vi_tri) {
        $this->ma_vi_tri = $ma_vi_tri;
    }

    function setMa_bp($ma_bp) {
        $this->ma_bp = $ma_bp;
    }

    function setSl_ton($sl_ton) {
        $this->sl_ton = $sl_ton;
    }
    function __construct() {
        
    }

    //public static function
    /**
     * get instance of Business_Addon_FASTTONKHOIMEI
     *
     * @return Business_Addon_FASTTONKHOIMEI
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
//        $db = Globals::getDbInstance('windows'); 
        $db = Globals::getDbInstance('linux'); 
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
    public function get_detail_by_malo($ma_lo,$ma_bp="",$not_gift=0,$check_ton=1){
        
        $db = $this->getDbConnection();
		if ($check_ton==1) {
			$query = "select * from $this->_tablename where ma_lo = '$ma_lo' and sl_ton >0";
		} else {
			$query = "select * from $this->_tablename where ma_lo = '$ma_lo'";
		}
        if($ma_bp != NULL){
            $query .=" and ma_bp = '$ma_bp'";
        }
        if((int)$not_gift >0){
            $query .="  and ma_kho NOT LIKE '%C.GIFT%'";
        }
        $result = $db->select($query);
		
        return $result;
    }
    public function get_list_by_mavt($ma_vt,$ma_bp=""){
        $cache = $this->getCacheInstance();
        $key ="get_list_by_mavt".$this->_tablename.$ma_vt.$ma_bp;
        $result = $cache->getCache($key);
        if($result ===FALSE){
            $db = $this->getDbConnection();
            $query = "select ma_bp,sum(sl_ton) as total from $this->_tablename where ma_vt IN ($ma_vt) and sl_ton >=0";
            if($ma_bp != NULL){
                $query .=" and ma_bp = '$ma_bp'";
            }
            $query .=" group by ma_bp";
            $result = $db->select2($query);
            if(!is_null($result) && is_array($result)){
                $cache->setCache($key, $result,120);
            }
        }
        
        return $result;
    }
    public function get_list_by_mavt_by_ma_kho($ma_vt,$ma_kho=""){
        $cache = $this->getCacheInstance();
        $key ="get_list_by_mavt_by_ma_kho".$this->_tablename.$ma_vt.$ma_kho;
        $result = $cache->getCache($key);
        $result = FALSE;
        if($result ===FALSE){
            $db = $this->getDbConnection();
            $query = "select ma_lo,ma_kho,ma_bp,sl_ton from $this->_tablename where ma_vt IN ($ma_vt) and sl_ton >0";
            if($ma_kho != NULL){
                $query .=" and ma_kho IN ($ma_kho)";
            }
            
            $result = $db->select2($query);
            if(!is_null($result) && is_array($result)){
                $cache->setCache($key, $result,120);
            }
        }
        
        return $result;
    }
    public function get_list_by_mavt2($ma_vt,$ma_bp=""){
        $cache = $this->getCacheInstance();
        $key ="get_list_by_mavt2b".$this->_tablename.$ma_vt.$ma_bp;
        $result = $cache->getCache($key);
        if($result ===FALSE){
            $db = $this->getDbConnection();
            $query = "select ma_kho,ma_bp,sum(sl_ton) as total from $this->_tablename where ma_vt IN ($ma_vt) and sl_ton >=0";
            if($ma_bp != NULL){
                $query .=" and ma_bp = '$ma_bp'";
            }
            $query .=" group by ma_bp,ma_kho order by ma_bp desc";
            $result = $db->select2($query);
            if(!is_null($result) && is_array($result)){
                $cache->setCache($key, $result,120);
            }
        }
        
        return $result;
    }
    public function get_list_by_mavt2_web($ma_vt,$ma_bp=""){
        $cache = $this->getCacheInstance();
        $key ="get_list_by_mavt2_web".$this->_tablename.$ma_vt.$ma_bp;
        $result = $cache->getCache($key);
        if($result ===FALSE){
            $db = $this->getDbConnection();
            $query = "select ma_kho,ma_bp,sum(sl_ton) as total from $this->_tablename where ma_vt IN ($ma_vt) and sl_ton >=0";
            if($ma_bp != NULL){
                $query .=" and ma_bp = '$ma_bp'";
            }
            $query .=" group by ma_bp,ma_kho order by ma_bp desc";
            $result = $db->select2($query);
            if(!is_null($result) && is_array($result)){
                $cache->setCache($key, $result,1800);
            }
        }
        
        return $result;
    }
    public function get_list_by_mavt3($ma_vt,$ma_bp=""){
        $cache = $this->getCacheInstance();
        $key ="get_list_by_mavt3".$this->_tablename.$ma_vt.$ma_bp;
        $result = $cache->getCache($key);
        $result = FALSE;
        if($result ===FALSE){
            $db = $this->getDbConnection();
            $query = "select ma_vt,ma_kho,ma_bp,sum(sl_ton) as total from $this->_tablename where ma_vt IN ($ma_vt) and sl_ton >=0 ";
            if($ma_bp != NULL){
                $query .=" and ma_bp = '$ma_bp'";
            }
            $query .=" group by ma_bp,ma_kho,ma_vt";
//            echo "<pre>";
//            var_dump($query);
            $result = $db->select2($query);
            if(!is_null($result) && is_array($result)){
                $cache->setCache($key, $result,120);
            }
        }
        
        return $result;
    }
    public function get_list_by_mavt4($ma_vt,$ma_bp=""){
        $cache = $this->getCacheInstance();
        $key ="get_list_by_mavt4".$this->_tablename.$ma_vt.$ma_bp;
        $result = $cache->getCache($key);
        $result = FALSE;
        if($result ===FALSE){
            $db = $this->getDbConnection();
            $query = "select ma_vt,ma_kho,ma_bp,sum(sl_ton) as total from $this->_tablename where ma_vt IN ($ma_vt) and sl_ton >0 ";
            if($ma_bp != NULL){
                $query .=" and ma_bp = '$ma_bp'";
            }
            $query .=" group by ma_bp,ma_kho,ma_vt";
//            echo "<pre>";
//            var_dump($query);
            $result = $db->select2($query);
            if(!is_null($result) && is_array($result)){
                $cache->setCache($key, $result,120);
            }
        }
        
        return $result;
    }
    public function get_list_by_mavt5($ma_vt,$ma_bp=""){
        $cache = $this->getCacheInstance();
        $key ="get_list_by_mavt4".$this->_tablename.$ma_vt.$ma_bp;
        $result = $cache->getCache($key);
        $result = FALSE;
        if($result ===FALSE){
            $db = $this->getDbConnection();
            $query = "select ma_lo,ma_vt,ma_kho,ma_bp,ngay_nhap from $this->_tablename where ma_vt IN ($ma_vt) and sl_ton >0 ";
            if($ma_bp != NULL){
                $query .=" and ma_bp = '$ma_bp'";
            }
//            echo "<pre>";
//            var_dump($query);
            $result = $db->select2($query);
            if(!is_null($result) && is_array($result)){
                $cache->setCache($key, $result,120);
            }
        }
        
        return $result;
    }
    public function get_list_by_malo22($ma_lo,$ma_bp=""){
        $cache = $this->getCacheInstance();
        $key ="get_list_by_malo22".$this->_tablename.$ma_lo.$ma_bp;
        $result = $cache->getCache($key);
        if($result ===FALSE){
            $db = $this->getDbConnection();
            $query = "select * from $this->_tablename where ma_lo IN ($ma_lo) and sl_ton >=0";
            if($ma_bp != NULL){
                $query .=" and ma_bp = '$ma_bp'";
            }
            $result = $db->select2($query);
            if(!is_null($result) && is_array($result)){
                $cache->setCache($key, $result,120);
            }
        }
        
        return $result;
    }
    public function get_detail_by_malo2($ma_lo,$ma_bp=""){
        $db = $this->getDbConnection();
        $query = "select * from $this->_tablename where ma_lo = '$ma_lo'";
        if($ma_bp != NULL){
            $query .=" and ma_bp = '$ma_bp'";
        }
        $result = $db->select($query);
        return $result;
    }
    public function get_list_by_malo($ma_lo,$ma_bp=""){
        $cache = $this->getCacheInstance();
        $key ="get_list_by_malo".$this->_tablename.$ma_lo.$ma_bp;
        $result = $cache->getCache($key);
        if($result ===FALSE){
            $db = $this->getDbConnection();
            $query = "select * from $this->_tablename where ma_lo = '$ma_lo' and sl_ton <=1";
            if($ma_bp != NULL){
                $query .=" and ma_bp = '$ma_bp'";
            }
            $result = $db->select2($query);
            if(!is_null($result) && is_array($result)){
                $cache->setCache($key, $result,120);
            }
        }
        return $result;
    }
    public function get_list_by_malo2($ma_lo,$ma_bp=""){
        $cache = $this->getCacheInstance();
        $key ="get_list_by_malo2".$this->_tablename.$ma_lo.$ma_bp;
        $result = $cache->getCache($key);
        if($result ===FALSE){
            $db = $this->getDbConnection();
            $query = "select * from $this->_tablename where ma_lo = '$ma_lo' and sl_ton >0";
            if($ma_bp != NULL){
                $query .=" and ma_bp = '$ma_bp'";
            }
            $result = $db->select2($query);
            if(!is_null($result) && is_array($result)){
                $cache->setCache($key, $result,120);
            }
        }
        
        return $result;
    }
    public function get_list_by_malo3($ma_lo,$ma_bp=""){
        $cache = $this->getCacheInstance();
        $key ="get_list_by_malo3".$this->_tablename.$ma_lo.$ma_bp;
        $result = $cache->getCache($key);
        if($result ===FALSE){
            $db = $this->getDbConnection();
            $query = "select * from $this->_tablename where ma_lo IN ($ma_lo) and sl_ton >0";
            if($ma_bp != NULL){
                $query .=" and ma_bp = '$ma_bp'";
            }
            $result = $db->select2($query);
            if(!is_null($result) && is_array($result)){
                $cache->setCache($key, $result,120);
            }
        }
        
        return $result;
    }
    
    public function get_detail_by_malo_ma_vt($ma_vt){
        $db = $this->getDbConnection();
        $query = "select * from $this->_tablename where ma_lo ='$ma_vt' and ma_vt = '$ma_vt' and sl_ton >0";
        $result = $db->select($query);
        return $result[0];
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
    public function insert() {
        $db = $this->getDbConnection();
        $ma_lo = $this->getMa_lo();
        $ma_kho = $this->getMa_kho();
        $ma_vi_tri = $this->getMa_vi_tri();
        $ma_bp = $this->getMa_bp();
        $sl_ton = $this->getSl_ton();
        $query = "INSERT INTO [FAST_HNAM_DBTG].[dbo].[tonkhoimei]([ma_lo],[ma_kho],[ma_vi_tri],[ma_bp],[sl_ton]) VALUES ($ma_lo,$ma_kho,$ma_vi_tri,$ma_bp,$sl_ton)";
        $err = 0;
        try {
            $result = $db->excute($query);
            if($result ==FALSE){
                $err = 1;
                echo 'Them khong thanh cong. Da co loi xay ra';
                die();
            }
        } catch (Exception $exc) {
            $err = 1;
            echo 'Them khong thanh cong. Da co loi xay ra';
            die();
            echo $exc->getTraceAsString();
        }
        if($err ==0){
            // sync thành công, cập nhật trạng thái đã đồng bộ
        }
        return $result;
    }
	
	public function getList($limit=0){   
		$db = $this->getDbConnection();
		$limit = intval($limit);
		
		if ($limit==0) {
			$query ="SELECT * FROM $this->_tablename WHERE sl_ton > 0;";
		} else {
			$query ="SELECT TOP $limit * FROM $this->_tablename WHERE sl_ton > 0;";
		}

        
        try {			
            $result = $db->select2($query);            
        } catch (Exception $exc) {
            echo $exc->getTraceAsString(); 
			die();			
        }
        return $result;
    }

}

?>

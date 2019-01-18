<?php

class Business_Addon_FASTdmkho extends Business_Abstract {

    private $_tablename = 'hdbanhang_ck';
    private static $_instance = null;
    private $ma_dvcs = 'NULL';
    private $ma_kho = 'NULL';
    private $ten_kho = 'NULL';
    private $ten_kho2 = 'NULL';
    private $user_ql = 'NULL';
    private $ma_bp = 'NULL';
    private $status = 'NULL';
    function getStatus() {
        return $this->status;
    }

    function setStatus($status) {
        $this->status = $status;
    }

        function getMa_dvcs() {
        return $this->ma_dvcs;
    }

    function getMa_kho() {
        return $this->ma_kho;
    }

    function getTen_kho() {
        return $this->ten_kho;
    }

    function getTen_kho2() {
        return $this->ten_kho2;
    }

    function getUser_ql() {
        return $this->user_ql;
    }

    function getMa_bp() {
        return $this->ma_bp;
    }

    function setMa_dvcs($ma_dvcs) {
        $this->ma_dvcs = $ma_dvcs;
    }

    function setMa_kho($ma_kho) {
        $this->ma_kho = $ma_kho;
    }

    function setTen_kho($ten_kho) {
        $this->ten_kho = $ten_kho;
    }

    function setTen_kho2($ten_kho2) {
        $this->ten_kho2 = $ten_kho2;
    }

    function setUser_ql($user_ql) {
        $this->user_ql = $user_ql;
    }

    function setMa_bp($ma_bp) {
        $this->ma_bp = $ma_bp;
    }
    function __construct() {
        
    }

    //public static function
    /**
     * get instance of Business_Addon_FASTdmkho
     *
     * @return Business_Addon_FASTdmkho
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
            if($result ==FALSE){
                echo 'Them khong thanh cong. Da co loi xay ra';
                die();
            }
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
        }
        return $result;
    }
    public function insert($data) {
        $db = $this->getDbConnection();
        $ma_dvcs = $this->getMa_dvcs();
        $ma_kho = $this->getMa_kho();
        $ten_kho = $this->getTen_kho();
        $ten_kho2 = $this->getTen_kho2();
        $ma_bp = $this->getMa_bp();
        $user_ql = $this->getUser_ql();
        $status = $this->getStatus();
        $query = "INSERT INTO [FAST_HNAM_DBTG].[dbo].[dmkho]
           ([ma_dvcs]
           ,[ma_kho]
           ,[ten_kho]
           ,[ten_kho2]
           ,[ma_bp]
           ,[user_ql]
           ,[status])
     VALUES
           ($ma_dvcs
           ,$ma_kho
           ,$ten_kho
           ,$ten_kho2
           ,$ma_bp
           ,$user_ql
           ,$status)";
        try {
            $result = $db->excute($query);
        } catch (Exception $exc) {
            echo 'Them khong thanh cong. Da co loi xay ra';
            die();
            echo $exc->getTraceAsString();
        }
        return $result;
    }

}

?>

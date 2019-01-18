<?php

class Business_Addon_Billupdate extends Business_Abstract {

    private $_tablename = 'bill_update';
    private static $_instance = null;

    function __construct() {
        
    }

    //public static function
    /**
     * get instance of Business_Addon_Billupdate
     *
     * @return Business_Addon_Billupdate
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
     * @return Zend_Db_Adapter_Abstract
     */
    private function getDbConnection() {
        $db = Globals::getDbConnection('maindb');
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
     public function getDetailByPhone($phone){
        $db = $this->getDbConnection();
        $query = "select * from $this->_tablename where enabled = 1 and phone='$phone'";
        $result = $db->fetchAll($query);
        return $result[0];
    }
    public function get_list_by_req($isdel,$storeid){
            $db = $this->getDbConnection();
            $query = "select * from $this->_tablename where isdel > 0";
            if((int)$isdel >0){
                $query .=" and isdel = $isdel ";
                if($isdel ==1 || $isdel==3){
                    $query .=" and enabled = 1 ";
                }
            }
            if((int)$storeid >0){
                $query .=" and storeid IN ($storeid) ";
            }
            $query .=" group by id";
            $query .=" order by time_isdel desc";
            $result = $db->fetchAll($query);
            return $result;
        }
    public function get_detail_by_date_req($isdel,$datetime=""){
            $db = $this->getDbConnection(); 
            $query = "select * from $this->_tablename where isdel = $isdel ";
            if($datetime != NULL){
                $query .=" and DATE(time_isdel)='$datetime' ";
            }
            $result = $db->fetchAll($query);
            return $result[0];
        }
     public function search($kw){
        $db = $this->getDbConnection();
        $query = "select * from $this->_tablename where enabled = 1 and phone like '%$kw%' limit 200";
        $result = $db->fetchAll($query);
        return $result;
    }
    public function get_list_by_date($isync,$from="",$to="",$storeid=0,$type=0){
        $id = (int)$id;
        $db = $this->getDbConnection();
        $query = "select * from $this->_tablename where enabled = 1";
        if((int)$isync >0){
            $query .=" and isync = $isync";
        }
        if($from !=NULL){
            $query .=" and datetime >= '$from'";
        }
        if($to !=NULL){
            $query .=" and datetime <= '$to'";
        }
        if($storeid >0){
            $query .=" and storeid = $storeid";
        }
        if((int)$type >0){
            $query .=" and type = $type";
        }
        $query .=" order by id desc";
        $result = $db->fetchAll($query);
        return $result;
    }
    public function getDetail($id){
        $id = (int)$id;
        $db = $this->getDbConnection();
        $query = "select * from $this->_tablename where enabled = 1 and id = $id";
        $result = $db->fetchAll($query);
        return $result[0];
    }
    public function get_list_by_imei($strIMEI,$datetime=""){
        $db = $this->getDbConnection();
        $query = "select * from $this->_tablename where enabled = 1 and imei IN ($strIMEI)";
        if($datetime != NULL){
            $query .=" and DATE(datetime) ='$datetime' ";
        }
        $result = $db->fetchAll($query);
        return $result;
    }
    public function get_detail_by_imei2($strIMEI,$storeid=0){
        $db = $this->getDbConnection();
        $query = "select * from $this->_tablename where enabled = 1 and imei IN ($strIMEI) ";
        if((int)$storeid >0){
            $query .=" and storeid = $storeid";
        }
        $query .=" order by id desc";
        $result = $db->fetchAll($query);
        return $result[0];
    }
    public function get_list($enabled=0,$storeid=0,$limit=0){
        $db = $this->getDbConnection();
        $query = "select * from $this->_tablename where 1=1";
        if((int)$enabled >0){
            $query.=" and enabled = $enabled";
        }
        if((int)$storeid >0){
            $query.=" and storeid = $storeid";
        }
        if((int)$limit >0){
            $query.=" LIMIT $limit";
        }
        $result = $db->fetchAll($query);
        return $result;
    }
    public function get_top1($_date30=""){
        $db = $this->getDbConnection();
        $query = "select * from $this->_tablename where isync=1 and enabled =1 and block=1 and service = 0 ";
        if($_date30 != NULL){
            $query .=" and datetime >='$_date30'";
        }
        $query .=" order by id asc limit 0,1";
        $result = $db->fetchAll($query);
        return $result[0];
    }
    public function get_detail_by_sync($id){
        $db = $this->getDbConnection();
        $query = "select * from $this->_tablename where isync=1 and enabled =1 and id = $id and block=1 and service = 0 ";
        $result = $db->fetchAll($query);
        return $result[0];
    }
    public function get_detail($id){
        $db = $this->getDbConnection();
        $query = "select * from $this->_tablename where id = $id ";
        $result = $db->fetchAll($query);
        return $result[0];
    }

    public function insert($data) {
        $db = $this->getDbConnection();
        $result = $db->insert($this->_tablename, $data);
        if ($result > 0) {
            $lastid= $db->lastInsertId($this->_tablename); // tra ve id khi them vao
        }
        return $lastid;
    }
    
    public function excute($query){
        $db     =  $this->getDbConnection();
        $result = $db->query($query);
        return $result;
    }
    public function update($id,$data) {
        $db= $this->getDbConnection();
        $query = "id = ".$id;
        $result = $db->update($this->_tablename, $data,$query);
        return $result;
    }

}

?>

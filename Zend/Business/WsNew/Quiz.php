<?php

class Business_WsNew_Quiz extends Business_Abstract
{
    const EXPIRED = 3000; //secs
    private $_tablename = 'quiz_value';
    const KEY_LIST = 'quiz_value.list';

    private static $_instance = null;

    function __construct()
    {}

    /**
     * get instance of Business_WsNew_Quiz
     *
     * @return Business_WsNew_Quiz
     */
    public static function getInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new Business_WsNew_Quiz();
        }
        return self::$_instance;
    }

    private function getKeyList()
    {
        return sprintf(self::KEY_LIST);
    }

    private function getKeyDetail($id)
    {
        return sprintf(self::KEY_DETAIL, $id);
    }

    /**
     * get Zend_Db connection
     *
     * @return Zend_Db_Adapter_Abstract
     */
    function getDbConnection()
    {
        $db = Globals::getDbConnection('gamesdb', false);
        return $db;
    }

    private function getCacheInstance()
    {
        $cache = GlobalCache::getCacheInstance('ws');
        return $cache;
    }

        public function getConfig()
    {
            $db = $this->getDbConnection();
            $query = "select * from quiz_config";   
            
            $result = $db->fetchAll($query);       
            return $result;
    }
    
            public function checkcaudung($id)
    {
            $db = $this->getDbConnection();  
            $query = "select * from " . $this->_tablename . " where id=$id and featured=1 ";           
            $result = $db->fetchRow($query);       

            return $result;
    }
    
            public function getListTraLoi($parentID)
    {
            $db = $this->getDbConnection();           
            $query = "select * from " . $this->_tablename . " where  active=1 and parrent_id=$parentID  order by RAND()  ";           
            $result = $db->fetchAll($query);        

           return $result;
    }
    
            public function getListDacBietCapCao($litmit)
    {
            $db = $this->getDbConnection();           
            $query = "select * from " . $this->_tablename . " where featured=2 and active=1 and parrent_id=0 order by RAND() limit $litmit ";           
            $result = $db->fetchAll($query);        

           return $result;
    }
    
    
        public function getListDacBietCapThuong($litmit)
    {
            $db = $this->getDbConnection();           
            $query = "select * from " . $this->_tablename . " where featured=1 and active=1 and parrent_id=0 order by RAND() limit $litmit ";           
            $result = $db->fetchAll($query);        

           return $result;
    }
    
            public function getListThuong($litmit)
    {
            $db = $this->getDbConnection();           
            $query = "select * from " . $this->_tablename . " where featured=0 and active=1 and parrent_id=0 order by RAND() limit $litmit ";           
            $result = $db->fetchAll($query);        

           return $result;
    }
    
    
            public function getListUserThongKe($userid='',$check='',$date='')
    {       
                
             if($date!=''){
                 $temp=  explode('-', $date);
                $andDate=" and YEAR(datetime) = $temp[0] and MONTH(datetime) = $temp[1] and DAYOFMONTH(datetime)=$temp[2] ";
             }
            if($check!='')
            {
            $year = date('Y');
            $month = date('m');
            $day= date('d');
            $andCheck= "  and YEAR(datetime)=$year  and    MONTH(datetime)=$month and    DAYOFMONTH(datetime)=$day    ";
            }
            
            if($userid!='')
                $andUserID= "  and userid=$userid ";
            $db = $this->getDbConnection();     
            $cache = $this->getCacheInstance();
            $key ='Quiz-list-user-thong-ke-'.$date;
            $result = $cache->getCache($key);
           // $result=FALSE;
            if ($result === FALSE) {
            $query = "select userid,datetime from quiz_user where 1=1 $andUserID $andCheck $andDate group by userid ";           
            $result = $db->fetchAll($query);     
             $cache->setCache($key, $result, self::EXPIRED);
        }
            return $result;
    }
    
    
    
        public function getListUser($userid='',$check='',$date='')
    {       
             if($date!=''){
                 $temp=  explode('-', $date);
                $andDate=" and YEAR(datetime) = $temp[0] and MONTH(datetime) = $temp[1] and DAYOFMONTH(datetime)=$temp[2] ";
             }
            if($check!='')
            {
            $year = date('Y');
            $month = date('m');
            $day= date('d');
            $andCheck= "  and YEAR(datetime)=$year  and    MONTH(datetime)=$month and    DAYOFMONTH(datetime)=$day    ";
            }
            
            if($userid!='')
                $andUserID= "  and userid=$userid ";
            $db = $this->getDbConnection();           
            $query = "select * from quiz_user where 1=1 $andUserID $andCheck $andDate ";           
            $result = $db->fetchAll($query);       
            return $result;
    }
            public function getListAnswer($userqid)
    {
            $db = $this->getDbConnection();           
            $query = "select * from quiz_answer where userqid=$userqid";           
            $result = $db->fetchAll($query);       
            return $result;
    }
    
    public function getList($itemid,$featured='',$active='')
    {


        if($featured!='' and $featured >-1)
            $andfeatured = " and featured=$featured";

        if($active !=-1){
               if($active !='')
                $andactive = " and active=$active";
            else
               $andactive = " and active=1"; 
        }
            $db = $this->getDbConnection();           
            $query = "select * from " . $this->_tablename . " where parrent_id =$itemid $andfeatured $andactive";           
            $result = $db->fetchAll($query);        
  
        return $result;
    }
    
        public function insertCauhoi( $data) {
        $db = $this->getDbConnection();
        $result = $db->insert($this->_tablename, $data);
        return $db->lastInsertId();
    }
    
    
      public function insertCautraloi( $data) {
        $db = $this->getDbConnection();
        $result = $db->insert('quiz_answer' , $data);
        return $db->lastInsertId();
    }
    
          public function insertUser( $data) {
        $db = $this->getDbConnection();
        $result = $db->insert('quiz_user' , $data);
        return $db->lastInsertId();
    }
    
         public function updateUser($id,$data) {
        $db = $this->getDbConnection();
        $where = array();
        $where[] = "userqid='" . parent::adaptSQL($id) . "'";
        $result = $db->update('quiz_user' , $data, $where);
        return $result;
    }
        public function updateData($id,$data) {
        $db = $this->getDbConnection();
        $where = array();
        $where[] = "id='" . parent::adaptSQL($id) . "'";
        $result = $db->update($this->_tablename, $data, $where);
        return $result;
    }
    
    
     public function updateConfig($id,$data) {
        $db = $this->getDbConnection();
        $where = array();
        $where[] = "id='" . parent::adaptSQL($id) . "'";
        $result = $db->update('quiz_config' , $data, $where);
        return $result;
    }
    
    
   
}

?>
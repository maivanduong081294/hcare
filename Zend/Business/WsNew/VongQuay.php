<?php

class Business_WsNew_VongQuay extends Business_Abstract
{
    const EXPIRED = 3000; //secs
    private $_tablename = 'ws_vong_quay';
    private $_tablename1 = 'ws_vong_quay_quatang';
    private $_tablename2 = 'ws_vong_quay_set_trung_thuong';
    private $_tablename3 = 'ws_vong_quay_trung_thuong';

    const KEY_LIST_ALL = 'push.list.all.%s';
    const KEY_DETAIL = 'push.detail.%s';

    private static $_instance = null;

    function __construct()
    {}

    /**
     * get instance of Business_WsNew_VongQuay
     *
     * @return Business_WsNew_VongQuay
     */
    public static function getInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new Business_WsNew_VongQuay();
        }
        return self::$_instance;
    }

    private function getKeyList($filter)
    {
        return sprintf(self::KEY_LIST_ALL, $filter);
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
        $db = Globals::getDbConnection('maindb', false);
        return $db;
    }

    function getDbConnectionHnamcode()
    {
        $db = Globals::getDbConnection('codedb', false);
        return $db;
    }


    private function getCacheInstance()
    {
        $cache = GlobalCache::getCacheInstance('ws');
        return $cache;
    }


  
    public function check($endpoint)
    {

            $json = json_decode($endpoint);
            $endpoint = $json->endpoint;
            $db = $this->getDbConnection();
            $query = "select * from " . $this->_tablename . " where endpoint like '%$endpoint%'  ";
            $result = $db->fetchRow($query); 
           return $result;
    }  

  
    public function search($keyword, $limit ,$offset)
    {

            $json = json_decode($endpoint);
            $endpoint = $json->endpoint;
            $db = $this->getDbConnection();
            $query = "SELECT p.idpush FROM `ws_push_detail` as p INNER JOIN `ws_push`  as c on p.idpush = c.id where  c.enabled=1 and  p.link like '%$keyword%' and c.created> DATE_SUB(NOW(), INTERVAL 30 day) GROUP BY p.idpush limit $limit OFFSET $offset";
            $result = $db->fetchAll($query); 
           return $result;
    }  


    public function countListHistoryPush($date,$sync='')
    {
            $temp = explode('-',$date);
            $year= $temp[0];
            $month= $temp[1];
            $day= $temp[2];

            if($sync==2)
            {
                $andSync = " and active=1";
            }
            else
            {
                if(is_numeric($sync))
                $andSync = " and sync=$sync";
            }


            $db = $this->getDbConnection();
            $query = "select count(id) as count from `ws_push_log` where YEAR(datetime)=$year and MONTH(datetime)= $month and DAY(datetime)= $day $andSync";
            $result = $db->fetchRow($query);
            return $result['count'];
    }


    public function countListVongQuay($id , $enabled)
    {

                if(is_numeric($id))
                $andID = " and id in($id)   ";
            else
                $andID = " and  title like '%$id%'   ";

            if($enabled!='')
                $enabled = " and enabled = $enabled  ";

            $db = $this->getDbConnection();
            $query = "select count(id) as count from " . $this->_tablename  . " where 1=1 $andID  $enabled    ";
            $result = $db->fetchRow($query);
            return $result['count'];
    }
    
    public function countTrungThuong($idqt,$cateid)
    {
            $db = $this->getDbConnection();
            $query = "select count(id) as count from " . $this->_tablename3  . " where  idqt = $idqt and cateid= $cateid    ";
            $result = $db->fetchRow($query);
            return $result['count'];
    }

    public function checkBillidTrungthuong($billid,$id)
    {
            $db = $this->getDbConnection();
            $query = "select id from " . $this->_tablename3  . " where  billid = $billid  and cateid=$id ";
            $result = $db->fetchRow($query);
            return $result;
    }


    public function getlistVongQuay($id ,$limit='',$to='',$enabled)
    {
            if(is_numeric($id))
               $andID = " and id in($id)   ";
            else
                $andID = " and  title like '%$id%'   ";

            if($enabled!='')
                $enabled = " and enabled = $enabled  ";
            $db = $this->getDbConnection();

            $andLimit = " limit $limit OFFSET $to";
            $query = "select *,(select count(id) from `ws_vong_quay_quatang` where cateid = p.id ) as tongqua from " . $this->_tablename . " as p where 1=1 $andID  $enabled  order by id desc  $andLimit ";
            $result = $db->fetchAll($query);
           return $result;
    }
    public function getlistVongQuayQuatang($id ,$limit='',$to='')
    {
            $db = $this->getDbConnection();
            if($limit !='')
            $andLimit = " limit $limit OFFSET $to";
            $query = "select * from " . $this->_tablename1 . " where  cateid =$id    order by id asc  $andLimit ";
            $result = $db->fetchAll($query);
           return $result;
    }

    public function getlistTrungThuong($cateid)
    {
            $db = $this->getDbConnection();
            $query = "select * from " . $this->_tablename3 . " where  cateid =$cateid    order by id desc   ";
            $result = $db->fetchAll($query);
           return $result;
    }

    public function getlistSetTrungThuong1($id ,$limit='',$to='')
    {
            $db = $this->getDbConnection();
            if($limit !='')
            $andLimit = " limit $limit OFFSET $to";
            $query = "select p.*,c.title from " . $this->_tablename2 . " as p inner join ".$this->_tablename1 ." as c on p.idqt = c.id  where  p.cateid =$id    order by p.id desc  $andLimit ";
            $result = $db->fetchAll($query);
           return $result;
    }
    

    public function getlistTrungThuongVongQuayQuatang($id)
    {
            $db = $this->getDbConnection();
            $query = "select * from " . $this->_tablename1 . " where  cateid =$id    order by id asc   ";
            $result = $db->fetchAll($query);
           return $result;
    }


    public function getlistPlayVongQuayQuatang($id)
    {
            $db = $this->getDbConnection();
            $query = "select * from " . $this->_tablename1 . " where  cateid =$id and enabled=1   order by id asc   ";
            $result = $db->fetchAll($query);
           return $result;
    }
    public function getlistSetTrungthuong($id)
    {
            $db = $this->getDbConnection();
            $query = "select * from " . $this->_tablename2 . " where  idqt =$id   order by id desc  ";
            $result = $db->fetchAll($query);
           return $result;
    }
    public function getlistPlayVongQuayQuatangKM($id)
    {     
            $db = $this->getDbConnection();
            $query = "select * from " . $this->_tablename1 . " where  cateid =$id and enabled=1 and type=1  order by id asc   ";
            $result = $db->fetchAll($query);
           return $result;
    }

    public function getlistPlayVongQuayQuatangKMCon($id)
    {     
            $db = $this->getDbConnection();
            $query = "select * from " . $this->_tablename1 . " as p where  p.cateid =$id and p.enabled=1 and p.type=1  and p.soluong > ( select count(id) as count from ws_vong_quay_trung_thuong where cateid =$id  and idqt = p.id ) ";
            $result = $db->fetchAll($query);
           return $result;
    }

    public function getUserById($id)
    {     
            $db = $this->getDbConnection();
            $query = "select * from  `zfw_users`   where  userid =$id ";
            $result = $db->fetchRow($query);
           return $result;
    }

    public function getlistWinnerQuatang($id)
    {
            //=============== check trong time and so luong da trung < so luong set trung | check so luong con va so luong trung
            $db = $this->getDbConnection();
            $query = "select * from " . $this->_tablename2 . " as p where  p.cateid =$id and p.enabled=1 and p.time_begin < NOW() and p.time_end > NOW() and p.soluong > ( select count(id) as count from ws_vong_quay_trung_thuong where idset = p.id  and cateid =$id and idqt = p.idqt and p.time_begin < created and p.time_end > created) and p.idqt = ( select id  from ws_vong_quay_quatang where id = p.idqt and soluong >  ( select count(id) as count from ws_vong_quay_trung_thuong where  cateid =$id and idqt = p.idqt ) )  ";
            $result = $db->fetchAll($query);
           return $result;
    }

    public function getdetailVongQuay($id='')
    {        
            $db = $this->getDbConnection();
                if($id!='')
                $andID= "where id= $id";
            $query = "select * from " . $this->_tablename . " $andID  ";
            $result = $db->fetchRow($query);
           return $result;
    }

    public function getListBill($billid='')
    {        
            $db = $this->getDbConnection();
            $query = "select * from users_products where id_addon_user= $billid and is_actived=1  and status2=0 ";
            $result = $db->fetchAll($query);
           return $result;
    }

    public function getdetailVongQuayQuatang($id='')
    {        
            $db = $this->getDbConnection();
                if($id!='')
                $andID= "where id= $id";
            $query = "select * from " . $this->_tablename1 . " $andID  ";
            $result = $db->fetchRow($query);
           return $result;
    }
    public function getdetailSetTrungthuong($id='')
    {        
            $db = $this->getDbConnection();
                if($id!='')
                $andID= "where id= $id";
            $query = "select * from " . $this->_tablename2 . " $andID  ";
            $result = $db->fetchRow($query);
           return $result;
    }



    public function update($id,$data) {
        $where = array();
        $where[] = "id='" . parent::adaptSQL($id) . "'";
        $db = $this->getDbConnection();
        $result = $db->update($this->_tablename, $data, $where);
        return $result;
    }

    public function updateQT($id,$data) {
        $where = array();
        $where[] = "id='" . parent::adaptSQL($id) . "'";
        $db = $this->getDbConnection();
        $result = $db->update($this->_tablename1, $data, $where);
        return $result;
    }

    public function updateSetTrungthuong($id,$data) {
        $where = array();
        $where[] = "id='" . parent::adaptSQL($id) . "'";
        $db = $this->getDbConnection();
        $result = $db->update($this->_tablename2, $data, $where);
        return $result;
    }

    public function updateLog($hash) {
        $db = $this->getDbConnection();
        $query = "UPDATE ".$this->_tablename3." SET active=1  WHERE  active=0 and hash='$hash' ORDER BY id asc LIMIT 1 ";
        $result = $db->query( $query );
        return $result;
    }

    public function insertQT($data) {
        $db = $this->getDbConnection(); 
        $result = $db->insert($this->_tablename1, $data);
        return $db->lastInsertId();
    }

    
    public function insertSetTrungthuong($data) {
        $db = $this->getDbConnection(); 
        $result = $db->insert($this->_tablename2, $data);
        return $db->lastInsertId();
    }

   public function insert($data) {
        $db = $this->getDbConnection(); 
        $result = $db->insert($this->_tablename, $data);
        return $db->lastInsertId();
    }

    public function insertVoucher($data) {    
            $db = $this->getDbConnectionHnamcode(); 
            $result = $db->insert('ws_vouchers_add', $data);
            return $db->lastInsertId();
        }   


    public function insertTrungThuong($data) {
        $db = $this->getDbConnection(); 
        $result = $db->insert($this->_tablename3, $data);
        return $db->lastInsertId();
    }
        


}

?>
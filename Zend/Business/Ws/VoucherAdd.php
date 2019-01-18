<?php

class Business_Ws_VoucherAdd extends Business_Abstract
{

    private $_tablename_tmp = 'ws_vouchers_add';
    private $_tablename = array(
        'ws_vouchers_add' => 'ws_vouchers_add',
        'ws_vouchers' => 'ws_vouchers'
    )
    ;

    private static $_instance = null;

    const KEY_DETAILS = 'voucher.%s';

    function __construct()
    {}

    /**
     * get instance of Business_Ws_VoucherAdd
     *
     * @return Business_Ws_VoucherAdd
     */
    public static function getInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new Business_Ws_VoucherAdd();
        }
        return self::$_instance;
    }

    /**
     * get Zend_Db connection
     *
     * @return Zend_Db_Adapter_Abstract
     */
    function getDbConnection()
    {
        $db = Globals::getDbConnection('codedb', false);
        return $db;
    }

    /**
     * Enter description here...
     *
     * @return Maro_Cache
     */
    private function getCacheInstance()
    {
        $cache = GlobalCache::getCacheInstance('menu');
        return $cache;
    }

    private function getKeyDetail($id)
    {
        return sprintf(self::KEY_DETAILS, $id);
    }
    public function get_detail_by_id($id){
        $db = $this->getDbConnection();
        $query = "select * from $this->_tablename_tmp where id = $id";
        $result = $db->fetchAll($query);
        return $result[0];
    }
    public function get_list_by_id($id){
        $db = $this->getDbConnection();
        $query = "select * from $this->_tablename_tmp where id IN ($id)";
        $result = $db->fetchAll($query);
        return $result;
    }

    public function validateVoucher($vcode, $prefix)
    {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename[$prefix] . " WHERE code_name='$vcode' and used = 0";
        $result = $db->fetchAll($query);
        if (! is_null($result) && is_array($result) && count($result) == 1) {
            $result = $result[0];
        } else {
            return false;
        }
        if ($result == null)
            return null;
        return $result;
    }

    public function randomCode($char = '', $lengCode = 0, $numCode = 0)
    {
        $result = '';
        $size = strlen($char);
        for ($i = 0; $i < $numCode; $i ++) {
            $code = '';
            for ($j = 0; $j < $lengCode; $j ++) {
                $code .= $char[rand(0, $size - 1)];
            }
            $result .= $code . ' ';
        }
        $results = substr($result, 0, - 1);
        return $results;
    }

    public function insertCode($arrayCode = NULL)
    {
        $db = $this->getDbConnection();
        if (isset($arrayCode) && $arrayCode != '') {
            $listCode = explode(' ', $arrayCode);
            $list_sql = '';
            $sum_code = '';
            foreach ($listCode as $key => $val) {
                $list_sql .= 'INSERT INTO ws_voucher (code) VALUES ("' . $val . '");';
                if (($key + 1) % 100 == 0) {
                    $sum_code .= $list_sql . ',';
                    $list_sql = '';
                }
            }
            $sum_code = substr($sum_code, 0, - 1);
            $arrySumCode = explode(',', $sum_code);
            foreach ($arrySumCode as $key => $val) {
                $db->query("" . $val . "");
            }
        }
    }

    public function update($id, $data)
    {
        $db = $this->getDbConnection();
        $where = array();
        $where[] = "id='" . parent::adaptSQL($id) . "'";
        $result = $db->update($this->_tablename_tmp, $data, $where);
        return $result;
    }

    public function updateCodeUsed($code_name, $data)
    {
        $db = $this->getDbConnection();
        $where = array();
        $where[] = "code_name='" . parent::adaptSQL($code_name) . "'";
        $result = $db->update($this->_tablename, $data, $where);
        return $result;
    }

    public function insert($data, $prefix)
    {
        $db = $this->getDbConnection();
        $result = $db->insert($this->_tablename[$prefix], $data);
        return $result;
    }

    public function getVouchers($id = 0, $prefix)
    {
        $db = $this->getDbConnection();
        if ($id != 0) {
            $sql = "SELECT * FROM " . $this->_tablename[$prefix] . " WHERE id=$id ";
            $result = $db->fetchRow($sql);
        } else {
            $sql = "SELECT * FROM " . $this->_tablename[$prefix] . " ";
            $result = $db->fetchAll($sql);
        }
        return $result;
    }
    public function getList($enabled="",$orderby=0,$sectionid=0,$limit = ''){
        $db = $this->getDbConnection();
        $sql = "SELECT * FROM $this->_tablename_tmp where 1=1";
        if($enabled != null){
            $sql .=" and enabled = $enabled";
        }
        if((int)$sectionid >  0){
            $sql .=" and sectionid = $sectionid";
        }
        if($orderby >0){
            $sql .=" order by id asc";
        }else{
           $sql .=" order by id desc"; 
        }
        if($limit) {
            $sql .= "limit $limit";
        }
        $result = $db->fetchAll($sql);
        return $result;
    }

    public function getVoucherList($sectionid=0,$limit = 10){
        $db = $this->getDbConnection();
        $sql = "SELECT * FROM {$this->_tablename['ws_vouchers']} where 1=1";
        if((int)$sectionid >  0){
            $sql .=" and sectionid = $sectionid";
        }
        $sql .=" order by code_id desc";
        if($limit) {
            $sql .= " limit $limit";
        }
        $result = $db->fetchAll($sql);
        return $result;
    }

    public function get_list($type_ctkm=0,$enabled=1){
        $db = $this->getDbConnection();
        $sql = "SELECT * FROM $this->_tablename_tmp where 1=1";
        if($type_ctkm >  -1){
            $sql .=" and type_ctkm = $type_ctkm";
        }
        if((int)$enabled <=1){
            $sql .=" and enabled = 1";
        }
        
        $sql .=" order by id desc"; 
        $result = $db->fetchAll($sql);
        return $result;
    }
    public function getList2($enabled="",$orderby=0,$type_ctkm=""){
        $db = $this->getDbConnection();
        $sql = "SELECT * FROM $this->_tablename_tmp where 1=1";
        if($enabled != null){
            $sql .=" and enabled = $enabled";
        }
        if($type_ctkm !=  NULL){
            $sql .=" and type_ctkm = $type_ctkm";
        }
        if($orderby >0){
            $sql .=" order by id asc";
        }else{
           $sql .=" order by id desc"; 
        }
        $result = $db->fetchAll($sql);
        return $result;
    }
}
?>
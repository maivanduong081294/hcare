<?php

class Business_Addon_Complain extends Business_Abstract
{
    private $_tablename = 'addon_complain';
    private $_sub_tablename = 'addon_complain_message';
    private $_timecache = 120;
    private static $_instance = null;

    function __construct(){

    }

    public static function getInstance() {
        if(self::$_instance == null) {
            self::$_instance = new Business_Addon_Complain();
        }
        return self::$_instance;
    }

    function getDbConnection() {
        $db = Globals::getDbConnection('maindb', false);
        return $db;
    }

    public function countTimeByVote($vote_id,$date='') {
        $db = $this->getDbConnection();
        $where = '';
        $where.= " and vote_id=$vote_id";
        $date = $date?$date:date('Y-m-d');
        $query = "select count(*) as count from $this->_tablename where date(created) = '{$date}' $where order by id desc";
        $_result = $db->fetchAll($query);
        $result = $_result[0];
        return $result;
    }

    public function getList($where='') {
        $db = $this->getDbConnection();
        $query = "select * from $this->_tablename where (1) $where order by id desc";
        $result = $db->fetchAll($query);
        return $result;
    }

    public function getDetail($id,$vote_id=0) {
        $db = $this->getDbConnection();
        $where = '';
        if($vote_id) {
            $where = ' and vote_id=$vote_id';
        }
        $query = "select * from $this->_tablename where id = $id order by id desc";
        $_result = $db->fetchAll($query);
        $result = $_result[0];
        return $result;
    }

    public function insert($data) {
        $db = $this->getDbConnection();
        $result = $db->insert($this->_tablename,$data);
        if ($result > 0) {
            $lastid= $db->lastInsertId($this->_tablename);
        }
        return $lastid;
    }

    public function delete($id) {
        $db = $this->getDbConnection();
        $result = $db->delete($this->_tablename,"id = $id");
        return $result;
    }

    public function update($data,$query) {
        $db= $this->getDbConnection();
        $result = $db->update($this->_tablename, $data, $query);
        return $result;
    }

    public function getSubDetail($where='',$order = 'id desc') {
        $db = $this->getDbConnection();
        $query = "select * from $this->_sub_tablename where (1) $where order by $order";
        $_result = $db->fetchAll($query);
        if($_result) {
            return $_result;
        }
        return 0;
    }

    public function getSubDetailID($where='') {
        $_result = $this->getSubDetail($where);
        if($_result) {
            $result = $_result[0]['id'];
            return $result;
        }
        return 0;
    }

    public function subinsert($data) {
        $db = $this->getDbConnection();
        $result = $db->insert($this->_sub_tablename,$data);
        if ($result > 0) {
            $lastid= $db->lastInsertId($this->_sub_tablename);
        }
        return $lastid;
    }

    public function subupdate($data,$query) {
        $db= $this->getDbConnection();
        $result = $db->update($this->_sub_tablename, $data, $query);
        return $result;
    }

    public function getType() {
        $arrs = array(719,304);
        $usernames = array('hnam_hanhtd','hnam_trungtq','hnam_bachpn','hnam_tuanpha','hnam_thulnt','hnam_vangnv','hnam_hieppth','hnam_vanglv');
        $types = [
            '1' => [
                'telegram_group' => '-1001163274384',
                'name' => 'K/n Bảo hành máy',
                'id' => array_merge(array(338,767,957,369,935),$arrs),
                'username' => array(),
            ],
            '2' => [
                'telegram_group' => '-1001318285050',
                'name' => 'K/n Bảo hành phụ kiện',
                'id' => array_merge(array(436,398,956),$arrs),
                'username' => array(),
            ],
            '3' => [
                'telegram_group' => '-1001232068439',
                'name' => 'K/n Khác',
                'id' => array_merge(array(851,358),$arrs),
                'username' => array(),
            ],
            '4' => [
                'telegram_group' => '-1001433747175',
                'name' => 'Kỹ thuật',
                'id' => array(),
                'username' => array_merge(array('hnam_tinhvv','hnam_thinhtm','hnam_vinhtp'),$usernames),
            ],
            '5' => [
                'telegram_group' => '-1001436165318',
                'name' => 'Bán hàng',
                'id' => array(),
                'username' => array_merge(array('hnam_hoangtpk','hnam_ngocnlb','hnam_toantq','hnam_chienvt','hnam_chienhv'),$usernames),
            ],
            '6' => [
                'telegram_group' => '-1001332300807',
                'name' => 'Cửa hàng trưởng/ phó',
                'id' => array(),
                'username' => array_merge(array('hnam_hoangtpk','hnam_ngocnlb','hnam_toantq','hnam_chienvt','hnam_chienhv','hnam_quanlhd','hnam_vannnt'),$usernames),
            ],
            '7' => [
                'telegram_group' => '-1001484611963',
                'name' => 'Kho',
                'id' => array(),
                'username' => array(),
            ],
        ];
        return $types;
    }
}
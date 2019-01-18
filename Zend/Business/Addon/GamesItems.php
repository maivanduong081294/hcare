<?php

class Business_Addon_GamesItems extends Business_Abstract {

    private $_tablename = 'items';
    private static $_instance = null;

    function __construct() {
        
    }

    //public static function
    /**
     * get instance of Business_Addon_GamesItems
     *
     * @return Business_Addon_GamesItems
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
        $db = Globals::getDbConnection('gamesdb');
        return $db;
    }

    public function getList($campaignid) {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " WHERE campaignid = ?";
        $data = array($campaignid);
        $result = $db->fetchAll($query, $data);
        return $result;
    }
    
    public function getRewardName($input) {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM rewards WHERE id = ?";
        $data = array($input["rewardid"]);
        $result = $db->fetchAll($query, $data);
        return $result[0]["name"];
    }
    
    
    public function getDetail($rid) {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " WHERE id = ?";
        $data = array($rid);
        $result = $db->fetchAll($query, $data);
        return $result[0];
    }
    
    /*
     * get reward for draw
     */
    public function getRewardSpecial($campaignid, $date, $itemid) {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " WHERE campaignid = ?  AND (itembuy = ? OR itembuy = '' OR itembuy IS NULL) AND DATE(setdate) = ? AND used = 0 ORDER BY RAND() LIMIT 0,1";        
        $data = array($campaignid, $itemid, $date);        
        $result = $db->fetchAll($query, $data);
        return $result[0];
    }
    
    /*
     * get special reward list
     */
    public function getRewardSpecialList($campaignid) {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " WHERE campaignid = ? AND public=0";
        $data = array($campaignid);        
        $result = $db->fetchAll($query, $data);
        return $result;
    }
    
    public function getSummmaryRewards($campaignid) {
        $db = $this->getDbConnection();
        $query = "SELECT rewardid, count(id) as total, itembuy FROM " . $this->_tablename . " WHERE campaignid = ? GROUP BY rewardid ORDER BY rewardid ASC";       
        $data = array($campaignid);        
        $result = $db->fetchAll($query, $data);
        return $result;
    }
    
    public function getSummmaryUsedRewards($campaignid, $fromDate, $endDate, $store=null) {
        $db = $this->getDbConnection();
        if ($store==null || empty($store))
            $query = "SELECT rewardid, count(id) as total, itembuy FROM " . $this->_tablename . " WHERE campaignid = ? AND used = 1 AND date(winner_date) >= ? AND date(winner_date) <= ? GROUP BY rewardid ORDER BY rewardid ASC";
        else
            $query = "SELECT item.rewardid, count(item.id) as total, item.itembuy FROM " . $this->_tablename . " as item INNER JOIN winner as win WHERE item.winner_code = win.code AND win.store LIKE '$store%' AND item.campaignid = ? AND item.used = 1 AND date(item.winner_date) >= ? AND date(item.winner_date) <= ? GROUP BY item.rewardid ORDER BY item.rewardid ASC";
//        echo "<pre>";
//        var_dump($query, $campaignid, $fromDate, $endDate);
//        die();
                
        $data = array($campaignid, $fromDate, $endDate);        
        $result = $db->fetchAll($query, $data);
        return $result;
    }
    
    public function getReward($campaignid) {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " WHERE campaignid = ? AND public=1 AND used = 0 ORDER BY RAND() LIMIT 0,1";
        $data = array($campaignid);
        $result = $db->fetchAll($query, $data);
        return $result[0];
    }
    
    public function getWinnerByIMEI($imei) {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM winner WHERE code = ? LIMIT 0,1";
        $data = array($imei);
        $result = $db->fetchAll($query, $data);
        return $result[0];
    }
    
    public function insert($data) {
        $db = $this->getDbConnection();
        $result = $db->insert($this->_tablename, $data);
        if ($result > 0) {
            $last_id = $db->lastInsertId();
            return $last_id;
        }
        return 0;
    }
    public function insertWinner($data) {
        $db = $this->getDbConnection();
        $result = $db->insert("winner", $data);
        if ($result > 0) {
            $last_id = $db->lastInsertId();
            return $last_id;
        }
        return 0;
    }

    public function update($id, $data) {
        $where = array();
        $where[] = "id='" . parent::adaptSQL($id) . "'";
        $db = $this->getDbConnection();
        $result = $db->update($this->_tablename, $data, $where);
        return $result;
    }        
    
    public function updateWinner($id, $data) {
        $where = array();
        $where[] = "code='" . parent::adaptSQL($id) . "'";
        $db = $this->getDbConnection();
        $result = $db->update("winner", $data, $where);
        return $result;
    }        

    public function delete($id) {

        $where = array();
        $where[] = "id='" . parent::adaptSQL($id) . "'";
        $db = $this->getDbConnection();
        $result = $db->delete($this->_tablename, $where);
        return $result;
    }
    
    public function getListWinner($campaignid) {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM  winner WHERE campaignid = ? ORDER BY createddate DESC";
        $data = array($campaignid);
        $result = $db->fetchAll($query, $data);
        return $result;
    }
    
    public function getListWinnerByStoreAddress($campaignid, $add) {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM  winner WHERE campaignid = ? AND store LIKE '$add%' ORDER BY createddate DESC";
        $data = array($campaignid);
        $result = $db->fetchAll($query, $data);
        return $result;
    }
            

}

?>

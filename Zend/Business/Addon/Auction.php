<?php

class Business_Addon_Auction extends Business_Abstract {

    private $_tablename = 'members';
    private static $_instance = null;

    function __construct() {
        
    }

    //public static function
    /**
     * get instance of Business_Addon_Auction
     *
     * @return Business_Addon_Auction
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
        $db = Globals::getDbConnection("auction");
        return $db;
    }
    
    public function getAuctionDetail($auctionID)
    {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . "auction_items" . " WHERE id = ?";
        $data = array($auctionID);
        $result = $db->fetchAll($query, $data);
        return $result[0];
    }
    
    public function getDetail($rid) {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " WHERE id = ?";
        $data = array($rid);
        $result = $db->fetchAll($query, $data);
        return $result[0];
    }
    
    public function getDetailByIMEI($imei) {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM members WHERE imei = ? LIMIT 0,1";
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

    public function update($id, $data) {
        $where = array();
        $where[] = "id='" . parent::adaptSQL($id) . "'";
        $db = $this->getDbConnection();
        $result = $db->update($this->_tablename, $data, $where);
        return $result;
    }   
    
    public function updateAuction($id, $data) {
        $where = array();
        $where[] = "id='" . parent::adaptSQL($id) . "'";
        $db = $this->getDbConnection();
        $result = $db->update("auction_items", $data, $where);
        return $result;
    }  
    

    public function delete($id) {

        $where = array();
        $where[] = "id='" . parent::adaptSQL($id) . "'";
        $db = $this->getDbConnection();
        $result = $db->delete($this->_tablename, $where);
        return $result;
    }

    public function getListMembers($auctionID) {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM members WHERE auctionid = ? ORDER BY auction_price1, auction_price2, auction_price3 ASC";
        $data = array($auctionID);
        $result = $db->fetchAll($query, $data);
        return $result;
    }
    public function getListWinner($auctionID, $price) {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM members WHERE auctionid = ? AND (auction_price1 = ? OR auction_price2 = ? OR auction_price3 = ?) ORDER BY auction_price1, auction_price2, auction_price3 ASC";
        $data = array($auctionID, $price, $price, $price);
        $result = $db->fetchAll($query, $data);
        return $result;
    }
    public function getTop10($auctionID) {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM members WHERE auctionid = ? ORDER BY auction_price1, auction_price2, auction_price3 ASC LIMIT 0, 10";
        $data = array($auctionID);
        $result = $db->fetchAll($query, $data);
        return $result;
    }
    
    public function getMemberDetailFromTopMembersByPrice($price) {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM top_members WHERE price = ?";
        $data = array($price);
        $result = $db->fetchAll($query, $data);
        return $result[0];
    }
    
    public function insertTopMember($topMemberData) {
        $db = $this->getDbConnection();
        $result = $db->insert("top_members", $topMemberData);
        if ($result > 0) {
            $last_id = $db->lastInsertId();
            return $last_id;
        }
        return 0;
    }
    public function deleteTopMemberByPrice($price) {

        $where = array();
        $where[] = "price=" . parent::adaptSQL($price) . "";
        $db = $this->getDbConnection();
        $result = $db->delete("top_members", $where);
        return $result;
    }
    
    public function getTopMembers($auctionid) {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM top_members WHERE auctionid = ? ORDER BY price ASC LIMIT 0, 10";
        $data = array($auctionid);
        $result = $db->fetchAll($query, $data);
        return $result;
    }
    
    public function getTop10ByIMEI($imeis) {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM members WHERE imei IN ($imeis)";
        $data = array();
        $result = $db->fetchAll($query, $data);
        return $result;
    }
    
}

?>

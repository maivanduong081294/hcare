<?php

class Business_Addon_Tonkhoimei2 extends Business_Abstract {

    private $_tablename = 'fast_tonkhoimei';

    private static $_instance = null;

    function __construct() {
        
    }

	
	/**
     * get instance of Business_Addon_Tonkhoimei2
     *
     * @return Business_Addon_Tonkhoimei2
     */
    public static function getInstance() {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
	
    /**
     * get Zend_Db connection
     *
     * @return Zend_Db_Adapter_Abstract
     */
    function getDbConnection() {
        $db = Globals::getDbConnection('hnam_app', false);
        return $db;
    }

    private function getCacheInstance() {
        $cache = GlobalCache::getCacheInstance('ws');
        return $cache;
    }
	
	public function getListByMaVT() {
		$db = $this->getDbConnection();
        $query = 'SELECT ma_vt,ma_kho,sum(sl_ton) as sl_ton FROM '.$this->_tablename.' WHERE (ma_kho NOT LIKE ("KBH%")) AND  (ma_kho LIKE ("%C.NEWX") OR ma_kho LIKE ("%C.OLDX") OR ma_kho LIKE ("%K.NEWX") OR ma_kho LIKE ("%K.OLDX")) group by ma_vt, ma_kho';
        $data = array();
        $result = $db->fetchAll($query, $data);
        return $result;
	}
        public function get_list_by_ma_vt($ma_vt){
            $cache = $this->getCacheInstance();
            $key = "get_list_by_ma_vt".$this->_tablename.$ma_vt;
            $result = $cache->getCache($key);
            if ($result === FALSE) {
                $db = $this->getDbConnection();
                $query = "SELECT count(*) as total FROM $this->_tablename where ma_vt like '$ma_vt%' and ma_kho NOT LIKE '%DEMO%' and ma_kho NOT LIKE '%GIFT%' and sl_ton>0";
                $data = array();
                $result = $db->fetchAll($query, $data);
                $result = $result[0];
                if(!is_null($result) && is_array($result)){
                    $cache->setCache($key, $result,900);
                }
            }
            return $result;
        }
        public function get_list_by_group_orther(){
            $cache = $this->getCacheInstance();
            $key = "get_list_by_group_orther".$this->_tablename;
            $result = $cache->getCache($key);
            if ($result === FALSE) {
                $db = $this->getDbConnection();
                $query = "SELECT * FROM `fast_tonkhoimei` where ma_kho NOT LIKE '%DEMO%' and ma_kho NOT LIKE '%GIFT%' and ma_kho NOT LIKE '%KBH%' and (ma_vt LIKE 'SW.%' or ma_vt LIKE 'LT.%' or ma_vt LIKE 'TB.%') and sl_ton>0";
                $data = array();
                $result = $db->fetchAll($query, $data);
                $result = $result[0];
                if(!is_null($result) && is_array($result)){
                    $cache->setCache($key, $result,900);
                }
            }
            return $result;
        }

                public function getSummaryByItemid() {
		$db = $this->getDbConnection();
        $query = 'SELECT itemid,code as ma_vt FROM `hnam_live`.`addon_product_color` where code != "" group by itemid,code';
        $data = array();
        $result = $db->fetchAll($query, $data);
        return $result;
	} 
    
	public function get_list_by_imei($ma_lo,$srt_type) {
        $cache = $this->getCacheInstance();
        $date = date('YmdHi');
        $key = "get_list_by_imei".$this->_tablename.$date.$ma_lo.$srt_type;
        $result = $cache->getCache($key);
        $result = FALSE;
        if ($result === FALSE) {
            $db = $this->getDbConnection();
            $query = "SELECT * FROM $this->_tablename where itemid IS NULL and ma_lo NOT LIKE '%.%' and ma_vt like '$srt_type%' and ma_lo NOT IN ($ma_lo) ";
            
    //        $query .= " LIMIT $records OFFSET $offset ";
            $result = $db->fetchAll($query);
            if(!is_null($result) && is_array($result)){
                $cache->setCache($key, $result,60);
            }
        }
        return $result;
	}

	public function get_list_by_imeiv2($srt_type) {
        $cache = $this->getCacheInstance();
        $date = date('YmdHi');
        $key = "get_list_by_imeiv2".$this->_tablename.$date.$srt_type;
        $result = $cache->getCache($key);
        $result = FALSE;
        if ($result === FALSE) {
            $db = $this->getDbConnection();
            //$query = "SELECT k.* FROM $this->_tablename as k LEFT JOIN hnam_live.app_mapping_product as p ON p.id_product = k.itemid where k.itemid IS NULL and k.ma_lo NOT LIKE '%.%' and ma_vt like '$srt_type%' AND (ma_kho LIKE ('%NEWX') OR ma_kho LIKE ('%OLDX'))";
            $query = "SELECT * FROM $this->_tablename WHERE `ma_vt` LIKE '$srt_type%' AND `itemid` is null AND ( ma_kho LIKE ('%NEWX') OR ma_kho LIKE ('%OLDX') ) LIMIT 500";
    //        $query .= " LIMIT $records OFFSET $offset ";
            $result = $db->fetchAll($query);
            if(!is_null($result) && is_array($result)){
                $cache->setCache($key, $result,60);
            }
        }
        return $result;
	}
	public function getListByItemid() {
		$db = $this->getDbConnection();
        $query = 'SELECT itemid,code as ma_vt FROM `hnam_live`.`addon_product_color` where code != "" group by itemid,code';
        $data = array();
        $result = $db->fetchAll($query, $data);
        return $result;
	} 
	
	public function getReportByItemid2($strID) {
		$db = $this->getDbConnection();
        $query = "SELECT * FROM $this->_tablename where itemid IN ($strID)";
        
        $data = array(); 
        $result = $db->fetchAll($query, $data);
        return $result;
	}
	public function getReportByItemid() {
		$db = $this->getDbConnection();
        $query = 'SELECT itemid, count(sl_ton) AS sl_ton FROM '.$this->_tablename.' GROUP BY itemid';
        $data = array(); 
        $result = $db->fetchAll($query, $data);
        return $result;
	}

    public function insert($data) {
        $db = $this->getDbConnection();       
        $result = $db->insert($this->_tablename, $data);
        
        return $db->lastInsertId();
    }
		
	public function truncate() {
		$db = $this->getDbConnection();    
		$sql = "TRUNCATE TABLE `$this->_tablename`;";
		$db->query($sql);
		try {
			$sql = "ALTER TABLE `$this->_tablename` MODIFY id INT(11) PRIMARY KEY AUTO_INCREMENT;";
			$db->query($sql);
			$sql = "ALTER TABLE `$this->_tablename` ADD INDEX( `ma_lo`, `ma_vt`);";
			$db->query($sql);
		} catch (Exception $e) {
			
		}
	}
	public function execute($sql) {
		$db = $this->getDbConnection();    
		if ($sql=="") {
			return;
		}
		try {
			$db->query($sql);
		} catch (Exception $e) {
			return $e->getMessage();
		}				
	}
	
	public function getTotalImeiFromMain($imei) {
		$db = $this->getDbConnection();
		
        $query = 'SELECT count(id) as total FROM `fast_tonkhoimei`';
		if ($imei!="") {
			$query = 'SELECT count(id) as total FROM `fast_tonkhoimei` where ma_lo="'.$imei.'"';
		}
        $data = array();
        $result = $db->fetchAll($query, $data);
        return intval($result[0]["total"]);
	} 


}

?>
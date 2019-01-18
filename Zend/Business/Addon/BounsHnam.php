<?php

class Business_Addon_BounsHnam extends Business_Abstract {

    private $_tablename = 'bouns_hnam';
    private static $_instance = null;

    function __construct() {
        
    }

    //public static function
    /**
     * get instance of Business_Addon_BounsHnam
     *
     * @return Business_Addon_BounsHnam
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
        $cache = GlobalCache::getCacheInstance('ws');
        return $cache;
    }
    
    
    public function getDetail($id) {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " WHERE id=?";
        $data = array($id);
        $result = $db->fetchAll($query, $data);
        return $result;
    }
    public function getDetailByUserIdByDay($id,$months) {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " WHERE userid= $id and months = $months";
        $result = $db->fetchAll($query);
        return $result;
    }
    public function getListByProductIds($itemid)
	{
//            exit('231');
            $db = $this->getDbConnection();
            $query = "SELECT * FROM  $this->_tablename  where product_ids = $itemid";
//    			var_dump($query);exit();
            $result = $db->fetchAll($query);
            
//            var_dump(count($result));exit();
            return $result;		
	}
    public function getList()
	{
//            exit('231');
            $db = $this->getDbConnection();
            $query = "SELECT * FROM  $this->_tablename ";
//    			var_dump($query);exit();
            $result = $db->fetchAll($query);
            
//            var_dump(count($result));exit();
            return $result;		
	}
        public function getListByVoteIdDate($vote_id,$months_created,$years_created){
            $cache = $this->getCacheInstance();
            $key = 'getListByVoteIdDate'.  $this->_tablename.$vote_id.$months_created.$years_created;
            $result = $cache->getCache($key);
//            $cache->deleteCache($key);//exit();
            if($result === false){
                $db = $this->getDbConnection();
                $query = "SELECT * FROM  $this->_tablename where 1=1";
                if($vote_id !=null){
                    $query .=" and userid = $vote_id  ";
                }
                if($months_created != 0){
                    $query .= " and months = $months_created ";
                }
                if($years_created != 0){
                    $query .= " and years = $years_created ";
                }
                $query.="  order by id desc limit 100";
                $result = $db->fetchAll($query);
                $cache->setCache($key, $result);
            }
            return $result;
        }
        
        
        
        public function getBounsVote($vote_id,$months_created,$years_created)
	{
		$list = $this->getBounsByVote($vote_id,$months_created,$years_created);
//                var_dump($list);exit();
		if($list != null && is_array($list) && count($list) > 0)
		{
			for($i=0;$i<count($list);$i++)
			{
				if($list[$i]['userid'] == $vote_id)
				{
					return $list[$i];
				}
			}
		}
		return null;
	}
	public function getBounsByVote($vote_id,$months_created,$years_created)
	{

            $db = $this->getDbConnection();
            $query = "SELECT * FROM  $this->_tablename  where  userid = $vote_id and months = $months_created and years = $years_created";
//    			var_dump($query);exit();
            $result = $db->fetchAll($query);

            return $result;		
	}
        
    public function insert($data) {
        $db = $this->getDbConnection();
        $result = $db->insert($this->_tablename, $data);
        $this->_deleteAllCache();
        return $result;
    }
    public function update($id,$data) {
        $db= $this->getDbConnection();
        $query = "id = ".$id;
        $result = $db->update($this->_tablename, $data,$query);
        $this->_deleteAllCache();
//            var_dump($result);exit();
        return $result;
    }

    public function delete($id) {
        //get current menu
            $db= $this->getDbConnection();
            $query = "itemid = ".$id;
            $data = array(
                "enabled" => "0"
            );
            $result = $db->update($this->_tablename, $data,$query);
//            var_dump($result);exit();
            return $result;
    }
    public function deleteProportionById($id){
            $db = $this->getDbConnection();
            $result = $db->delete($this->_tablename, "id = $id");
            $this->_deleteAllCache();
            return $result;
        }
    public function restore($id) {
        //get current menu
            $db= $this->getDbConnection();
            $query = "itemid = ".$id;
            $data = array(
                "enabled" => "1"
            );
            $result = $db->update($this->_tablename, $data,$query);
//            var_dump($result);exit();
            return $result;
    }
    
    private function _deleteAllCache()
    {
            $cache = $this->getCacheInstance('ws');
            $cache->flushAll();
    }
    private function _deleteCacheByKey($key)
    {
        
        $key_name = "getlist".$key;
            $cache = $this->getCacheInstance('ws');
            $data = $cache->getCache($key_list);
//            $cache->flushAll();
            $cache->deleteCache($key_name);
    }

}

?>

<?php

class Business_Addon_VoteRotatory extends Business_Abstract
{
	private $_tablename = 'vote_rotatory';
	private static $_instance = null; 
	
	function __construct()
	{
		
	}
	
	/**
	 * get instance of Business_Addon_VoteRotatory
	 *
	 * @return Business_Addon_VoteRotatory
	 */
	public static function getInstance()
	{
		if(self::$_instance == null)
		{
			self::$_instance = new Business_Addon_VoteRotatory();
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
		$db    	= Globals::getDbConnection('maindb', false);
		return $db;	
	}
               
        public function getDetail($id) {
            $db = $this->getDbConnection();
            $query = "select * FROM " . $this->_tablename . " where id='$id' order by created_date desc";
            $result = $db->fetchAll($query);
            return $result[0];
        }
        public function getDetailByVoteId($userid) {
            $db = $this->getDbConnection();
            $query = "select * FROM " . $this->_tablename . " where userid='$userid' order by created_date desc";
//            echo "<pre>";
//            var_dump($query);
//            exit();
            $result = $db->fetchAll($query);
            return $result[0];
        }
       
	public function insert($data)
	{
		$db = $this->getDbConnection();
		$result = $db->insert($this->_tablename,$data);
                if ($result > 0) {
                    $lastid= $db->lastInsertId($this->_tablename); // tra ve id khi them vao
                }
                return $lastid;
	}
        public function update($itemid, $data)
	{
            $db = $this->getDbConnection();
            $where = array();
            $where[] = "id = '" . parent::adaptSQL($itemid) . "'";                
            try
            {			
                $result = $db->update($this->_tablename, $data, $where);
            }
            catch(Exception $e)
            {
                    return 0;
            }
            return $result;
	}
}

?>
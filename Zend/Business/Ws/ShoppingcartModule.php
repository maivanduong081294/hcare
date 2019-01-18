<?php

class Business_Ws_ShoppingcartModule extends Business_Abstract
{
	private $_tablename = 'ws_shoppingcartmodule';

	const KEY_LIST = 'ws_shoppingcartmodule.list';			//key of list
	const KEY_LIST_HAS_UID = 'ws_shoppingcartmodule.list.hasuid';			//key of list
	const KEY_LIST_HAS_NO_UID = 'ws_shoppingcartmodule.list.hasnouid';			//key of list
	const KEY_DETAIL = 'ws_shoppingcartmodule.detail.%s';		//key of detail.id

	private static $_instance = null;

	function __construct()
	{

	}

	//public static function
	/**
	 * get instance of Business_Ws_ShoppingcartModule
	 *
	 * @return Business_Ws_ShoppingcartModule
	 */
	public static function getInstance()
	{
		if(self::$_instance == null)
		{
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	private function getKeyList()
	{
		return sprintf(self::KEY_LIST);
	}

        private function getKeyListHasUID()
	{
		return sprintf(self::KEY_LIST_HAS_UID);
	}

        private function getKeyListHasNoUID()
	{
		return sprintf(self::KEY_LIST_HAS_NO_UID);
	}

        private  function getKeyDetail($id)
	{
		return sprintf(self::KEY_DETAIL,$id);
	}

	public static function mi_buildUrl($lang,$delta,$itemid)
	{

		$_menuitem = Business_Ws_MenuItem::getInstance();
		$item = $_menuitem->getDetailById($itemid);
		$url = '';
		$langid = $lang;

		if($item != null)
		{
			$title = $item['title'];
			$title = Business_Common_Utils::adaptTitleLinkURL($title);
			$_module = self::getInstance();
			$download = $_module->getDetail($delta);

			$menuitemstart = $download['menuitemstart'];
			if($menuitemstart == "" || $menuitemstart == null) $menuitemstart = 0;
			else
			{
				$menuitemstart = json_decode($menuitemstart,true);
				if(array_key_exists($langid,$menuitemstart)) $menuitemstart = $menuitemstart[$langid];
				else $menuitemstart = 0;
			}

			$_lang = Business_Ws_Languages::getInstance();
			$mylang = $_lang->getDetail($lang);
			$lang = $mylang['lang'];


			//format : /:lang/:menuitem/gallery/show/:galleryid/:gallery_name

			$url = Globals::getBaseUrl() . $lang . '/' . $itemid . '/shoppingcart/show/' . $delta . '/' . $title;
		}
		return $url;
	}

	public static function mi_getDetail($lang,$delta)
	{
		$_downloadmodule = self::getInstance();
		$downloadmodule = $_downloadmodule->getDetail($delta);
		return $downloadmodule;
	}

	public static function mi_getAltTitle($lang,$delta)
	{
		$_downloadmodule = self::getInstance();
		$downloadmodule = $_downloadmodule->getDetail($delta);
		return $downloadmodule['desc'];
	}

	public static function mi_getList($lang,$itemid = null)
	{
		$return = array();

		$_downloadmodule = self::getInstance();

		$list = $_downloadmodule->getList();

		if($list != null && count($list) > 0)
		{
			for($i=0;$i<count($list);$i++) $return[$list[$i]['shoppingcartid']] = $list[$i]['name'];
		}

		return $return;
	}

	/**
	 * Get DB Connection
	 *
	 * @return Zend_Db_Adapter_Abstract
	 */
	private function getDbConnection()
	{
		$db    	= Globals::getDbConnection('maindb');
		return $db;
	}

	/**
	 * Enter description here...
	 *
	 * @return Maro_Cache
	 */
	private function getCacheInstance()
	{
		$cache = GlobalCache::getCacheInstance('ws');
		return $cache;
	}

	public function buildDetailUrl($lang,$menuitem,$item)
	{
		//:lang/:menuitem/news/detail/:newsid/:newsitemid/title
		$_lang = Business_Ws_Languages::getInstance();
		$mylang = $_lang->getDetail($lang);
		$lang = $mylang['lang'];

		$delta = $item['cateid'];
		$title = $item['title'];
		$title = Business_Common_Utils::adaptTitleLinkURL($title);
		$url = Globals::getBaseUrl() . $lang . '/' . $menuitem . '/shoppingcart/detail/' . $delta . '/' . $item['itemid'] . '/' . $title;
		return $url;
	}

	public function getList()
	{

		$cache = $this->getCacheInstance();
		$key = $this->getKeyList();
		$result = $cache->getCache($key);
		if($result === FALSE)
		{
			$db = $this->getDbConnection();
			$query = "SELECT * FROM " . $this->_tablename . " ORDER BY created DESC";
			$result = $db->fetchAll($query);
			if(!is_null($result) && is_array($result))
			{
				$cache->setCache($key, $result);
			}
		}
		return $result;
	}

        public function getListWithPaging($status='all', $offset=0, $records=20, $payment = 'all', $key = '', $search_type = 'orderid', $source = '')
	{
            if ($source == 'web') $source = '';
            $db = $this->getDbConnection();
            if ($status == 'all'){
                $_status = '1=1';
            }else{
                $_status = 'status = '.$status;
            }
            //$key = json_encode($key);
            //$key = str_replace("\"", "", $key);
            $phonename = null;
            switch($search_type) {
                case 'orderid':
                        $key = 'AND orderid like "%'.$key.'%" ';
                    break;
                case 'name':
                        $key = 'AND LOWER(orderinfo) like "%'.$key.'%" ';
                    break;
                case 'phone':
                        $key = 'AND orderinfo like "%'.$key.'%" ';
                    break;                
                case 'phonename': 
                   
                        $_products = Business_Ws_ProductsItem::getInstance();
                     
                        $listID = $_products->getListByName($key);
            
                        $key = implode(",", $listID);
                        
                        $phonename = $key;
                    break;
                default:
                        $key = '';
                    break;
            }
            
            if ($key != '' && $source != '')
                $key .= 'AND source = "' . $source . '"';
//            else
//                $key = '';
            //echo "<pre>";
            //var_dump($key);
            //die();
            if ($payment == 'all'){
                
                if ($phonename == null)
                    $query = "SELECT * FROM " . $this->_tablename . " WHERE $_status $key ORDER BY created DESC LIMIT ";
                else
                    $query = "SELECT t1.* FROM " . $this->_tablename . " t1, ws_shoppingcartitem t2 WHERE $_status AND t1.shoppingcartid = t2.shoppingcartid AND t2.productsid IN (".$phonename.") ORDER BY created DESC LIMIT ";
                $query .= " $offset, $records ";
            }else{
                
                if ($phonename == null)
                    $query = "SELECT * FROM " . $this->_tablename . " WHERE $_status $key AND payment = $payment ORDER BY created DESC LIMIT ";
                else
                    $query = "SELECT t1.* FROM " . $this->_tablename . " t1, ws_shoppingcartitem t2 WHERE $_status AND payment = $payment AND t1.shoppingcartid = t2.shoppingcartid AND t2.productsid IN ('".$phonename."') ORDER BY created DESC LIMIT ";
                
                $query .= " $offset, $records ";
            }

            $result = $db->fetchAll($query);
            if(!is_null($result) && is_array($result))
            {
                    return $result;
            }
	}

        public function getListHasUID($status='all', $offset=0, $records=0, $payment = 'all', $key = '')
	{
            
            $db = $this->getDbConnection();
            if ($status == 'all'){
                $_status = 'AND 1=1';
            }else{
                $_status = 'AND status = '.$status;
            }
            if ($key != '')
                $key = ' AND orderid like "%'.$key.'%" ';
            if ($payment == 'all'){
                if ($offset == 0 && $records == 0){
                    $query = "SELECT * FROM " . $this->_tablename . " WHERE userid > 0 $_status $key ORDER BY created DESC ";
                }else{
                    $query .= "SELECT * FROM " . $this->_tablename . " WHERE userid > 0 $_status $key ORDER BY created DESC LIMIT ";
                    $query .= " $offset, $records ";
                }
            }else{
                if ($offset == 0 && $records == 0){
                    $query = "SELECT * FROM " . $this->_tablename . " WHERE userid > 0 $_status $key AND payment = $payment ORDER BY created DESC ";
                }else{
                    $query .= "SELECT * FROM " . $this->_tablename . " WHERE userid > 0 $_status $key AND payment = $payment ORDER BY created DESC LIMIT ";
                    $query .= " $offset, $records ";
                }
            }


            $result = $db->fetchAll($query);
            return $result;
	}

        public function getListNoUID($status='all', $offset=0, $records=0, $payment = 'all', $key = '')
	{
            
            $db = $this->getDbConnection();
            if ($status == 'all'){
                $_status = 'AND 1=1';
            }else{
                $_status = 'AND status = '.$status;
            }
            if ($key != '')
                $key = ' AND orderid like "%'.$key.'%" ';
            if ($payment == 'all'){
                if ($offset == 0 && $records == 0)
                    $query = "SELECT * FROM " . $this->_tablename . " WHERE userid = 0  $_status $key ORDER BY created DESC ";
                else{
                    $query .= "SELECT * FROM " . $this->_tablename . " WHERE userid = 0 $_status $key ORDER BY created DESC  LIMIT ";
                    $query .= " $offset, $records ";
                }
            }else{
                if ($offset == 0 && $records == 0)
                    $query = "SELECT * FROM " . $this->_tablename . " WHERE userid = 0 $_status AND payment = $payment $key ORDER BY created DESC ";
                else{
                    $query .= "SELECT * FROM " . $this->_tablename . " WHERE userid = 0 $_status AND payment = $payment $key ORDER BY created DESC  LIMIT ";
                    $query .= " $offset, $records ";
                }
            }

            $result = $db->fetchAll($query);
            return $result;
	}

	public function getDetail($id)
	{
		$cache = $this->getCacheInstance();
		$key = $this->getKeyDetail($id);
		$result = $cache->getCache($key);
		if($result === FALSE)
		{
			$db = $this->getDbConnection();
			$query = "SELECT * FROM " . $this->_tablename . " WHERE shoppingcartid=?";
			$data = array($id);
			$result = $db->fetchAll($query,$data);
			if($result != null && is_array($result))
			{
				$result = $result[0];
				$cache->setCache($key, $result);
			}
		}
		return $result;
	}

	public function insert($data)
	{            
            
            $db = $this->getDbConnection();
            $result = $db->insert($this->_tablename, $data);
            if($result > 0)
            {
                    $this->_deleteAllCache();
            }
            return $db->lastInsertId();
	}

	public function update($id, $data)
	{
		$where = array();
		$where[] = "shoppingcartid='" . parent::adaptSQL($id) . "'";
		$db = $this->getDbConnection();
		$result = $db->update($this->_tablename, $data,$where);
		if($result > 0)
		{
			$this->_deleteAllCache($id);
		}
	}

	public function delete($id)
	{
		//get current menu
		$current = $this->getDetail($id);

		$where = array();
		$where[] = "shoppingcartid='" . parent::adaptSQL($id) . "'";
		$db = $this->getDbConnection();
		$result = $db->delete($this->_tablename, $where);
		if($result > 0)
		{
			$this->_deleteAllCache($id);
		}
	}

	///private functions /////

	private function _deleteAllCache($id = null)
	{
		$cache = $this->getCacheInstance();
                $cache->flushAll();
		$key = $this->getKeyList();
		$cache->deleteCache($key);
                $key = $this->getKeyListHasUID();
		$cache->deleteCache($key);
                $key = $this->getKeyListHasNoUID();
		$cache->deleteCache($key);
		if($id != null)
		{
			$key = $this->getKeyDetail($id);
			$cache->deleteCache($key);
		}
                $cache->flushAll();
	}

        public function getListByUid($uid){
            $db = $this->getDbConnection();
            $query = "SELECT * FROM " . $this->_tablename . " WHERE userid=?";
            $data = array($uid);
            $result = $db->fetchAll($query,$data);
            if(!is_null($result) && is_array($result) && count($result) == 1)
            {
                    $result = $result;
            }
            return $result;
        }

        public function getTotalHasUID($status = 'all', $payment = 'all', $key = ''){
           
            $db = $this->getDbConnection();
            if ($status == 'all'){
                $_status = 'AND 1=1';
            }else{
                $_status = 'AND status = '.$status;
            }
            if ($key != '')
                $key = ' AND orderid like "%'.$key.'%" ';
            else
                $key = '';
            if ($payment == 'all'){
                $query = "SELECT count(*) as total FROM " . $this->_tablename . " WHERE userid>0 $_status $key";
            }else{
                $query = "SELECT count(*) as total FROM " . $this->_tablename . " WHERE userid>0 $_status AND payment = $payment $key ";
            }
//            pre($query);
            $result = $db->fetchAll($query);
            if(!is_null($result) && is_array($result) && count($result) == 1)
            {
                    $result = $result[0]['total'];
            }
            return (int)$result;
        }

        public function getTotalHasNoUID($status=0, $payment = 'all', $key = ''){
            
            $db = $this->getDbConnection();
            
            if ($status == 'all'){
                $_status = 'AND 1=1';
            }else{
                $_status = 'AND status = '.$status;
            }
            
            if ($key != '')
                $key = ' AND orderid like "%'.$key.'%" ';
            if ($payment == 'all')
                $query = "SELECT count(*) as total FROM " . $this->_tablename . " WHERE userid=0 $_status $key";
            else
                $query = "SELECT count(*) as total FROM " . $this->_tablename . " WHERE userid=0 $_status AND payment = $payment $key";
            $result = $db->fetchAll($query);
            if(!is_null($result) && is_array($result) && count($result) == 1)
            {
                    $result = $result[0]['total'];
            }
            return (int)$result;
        }

        public function getTotal($status, $payment = 'all', $key = '', $source=''){
            $db = $this->getDbConnection();
	    
            
            if ($source == 'web') $source = '';
            
	    if ($status == 'all'){
                $_status = '1=1';
            }else{
                $_status = 'status = '.$status;
            }

            if ($key != '')
                $key = ' AND orderid like "%'.$key.'%" ';
            else
                $key = '';
            
            if ($source != '') {
                $key = ' AND source = "'.$source.'"';
            }
            if ($payment == 'all')
                $query = "SELECT count(*) as total FROM " . $this->_tablename . " WHERE $_status $key";
            else
                $query = "SELECT count(*) as total FROM " . $this->_tablename . " WHERE $_status $key AND payment = $payment";

            $result = $db->fetchAll($query);
            if(!is_null($result) && is_array($result) && count($result) == 1)
            {
                    $result = $result[0]['total'];
            }
            return (int)$result;
        }
        

}
?>
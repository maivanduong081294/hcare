<?php

class Business_Ws_DownloadItem extends Business_Abstract
{
	private $_tablename = 'ws_downloaditem';
	
	const KEY_LIST = 'downloaditem.list.%s.%s';			//key of list by faqid,cateid	
	const KEY_COUNT = 'downloaditem.count.%s.%s';			//key of count by faqid,cateid	
	const KEY_DETAIL = 'downloaditem.detail.%s';	//key of detail.id
		
	private static $_instance = null; 
	
	function __construct()
	{			
	}
	
	//public static function
	/**
	 * get instance of Business_Ws_DownloadItem
	 *
	 * @return Business_Ws_DownloadItem
	 */
	public static function getInstance()
	{
		if(self::$_instance == null)
		{
			self::$_instance = new self();
		}
		return self::$_instance;
	}	
		
	private function getKeyList($downloadid,$cateid)
	{
		return sprintf(self::KEY_LIST,$downloadid,$cateid);
	}
	
	private function getKeyCount($downloadid,$cateid)
	{
		return sprintf(self::KEY_COUNT,$downloadid,$cateid);
	}
	
	private function getKeyDetail($id)
	{
		return sprintf(self::KEY_DETAIL,$id);
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
	
	public function getDownloadItemLink($item)
	{
		$downlink = Globals::getConfig('root_url') . "/uploads/download/" . $item['filename'];
		return $downlink;
	}
	
	public function getFileIcon($filename)
	{
		$pos = strripos($filename,'.');
		if($pos === false)
		{
			return Globals::getConfig('root_url') . "/file-icons/file.gif";
		}
		
		$ext = substr($filename,$pos+1);
		switch ($ext)
		{
			case "7z":
				return "<img border=0 src='" . Globals::getConfig('root_url') . "/file-icons/7z.png" . "'>";
				break;
			case "ai":
				return "<img border=0 src='" . Globals::getConfig('root_url') . "/file-icons/ai.png" . "'>";
				break;
			case "bin":
			case "exe":
				return "<img border=0 src='" . Globals::getConfig('root_url') . "/file-icons/bin.png" . "'>";
				break;
			case "bz2":
				return "<img border=0 src='" . Globals::getConfig('root_url') . "/file-icons/bz2.png" . "'>";
				break;
			case "c":
				return "<img border=0 src='" . Globals::getConfig('root_url') . "/file-icons/c.png" . "'>";
				break;
			case "cfc":
				return "<img border=0 src='" . Globals::getConfig('root_url') . "/file-icons/cfc.png" . "'>";
				break;
			case "cfm":
				return "<img border=0 src='" . Globals::getConfig('root_url') . "/file-icons/cfm.png" . "'>";
				break;
			case "chm":
				return "<img border=0 src='" . Globals::getConfig('root_url') . "/file-icons/cfm.png" . "'>";
				break;
			case "class":
				return "<img border=0 src='" . Globals::getConfig('root_url') . "/file-icons/class.png" . "'>";
				break;
			case "conf":
				return "<img border=0 src='" . Globals::getConfig('root_url') . "/file-icons/conf.png" . "'>";
				break;
			case "cpp":
				return "<img border=0 src='" . Globals::getConfig('root_url') . "/file-icons/cpp.png" . "'>";
				break;
			case "cs":
				return "<img border=0 src='" . Globals::getConfig('root_url') . "/file-icons/cs.png" . "'>";
				break;
			case "css":
				return "<img border=0 src='" . Globals::getConfig('root_url') . "/file-icons/css.png" . "'>";
				break;
			case "csv":
				return "<img border=0 src='" . Globals::getConfig('root_url') . "/file-icons/csv.png" . "'>";
				break;
			case "deb":
				return "<img border=0 src='" . Globals::getConfig('root_url') . "/file-icons/deb.png" . "'>";
				break;
			case "divx":
				return "<img border=0 src='" . Globals::getConfig('root_url') . "/file-icons/divx.png" . "'>";
				break;
			case "doc":
			case "docx":
				return "<img border=0 src='" . Globals::getConfig('root_url') . "/file-icons/doc.png" . "'>";
				break;
			case "dot":
				return "<img border=0 src='" . Globals::getConfig('root_url') . "/file-icons/dot.png" . "'>";
				break;
			case "eml":
				return "<img border=0 src='" . Globals::getConfig('root_url') . "/file-icons/eml.png" . "'>";
				break;
			case "gif":
				return "<img border=0 src='" . Globals::getConfig('root_url') . "/file-icons/gif.png" . "'>";
				break;
			case "gz":
				return "<img border=0 src='" . Globals::getConfig('root_url') . "/file-icons/gz.png" . "'>";
				break;
			case "htm":
				return "<img border=0 src='" . Globals::getConfig('root_url') . "/file-icons/htm.png" . "'>";
				break;
			case "html":
				return "<img border=0 src='" . Globals::getConfig('root_url') . "/file-icons/html.png" . "'>";
				break;
			case "iso":
				return "<img border=0 src='" . Globals::getConfig('root_url') . "/file-icons/iso.png" . "'>";
				break;
			case "cs":
				return "<img border=0 src='" . Globals::getConfig('root_url') . "/file-icons/cs.png" . "'>";
				break;
			case "jar":
				return "<img border=0 src='" . Globals::getConfig('root_url') . "/file-icons/jar.png" . "'>";
				break;
			case "jpeg":
				return "<img border=0 src='" . Globals::getConfig('root_url') . "/file-icons/jpeg.png" . "'>";
				break;
			case "jpg":
				return "<img border=0 src='" . Globals::getConfig('root_url') . "/file-icons/jpg.png" . "'>";
				break;
			case "mov":
				return "<img border=0 src='" . Globals::getConfig('root_url') . "/file-icons/mov.png" . "'>";
				break;
			case "mp3":
				return "<img border=0 src='" . Globals::getConfig('root_url') . "/file-icons/mp3.png" . "'>";
				break;
			case "mpg":
				return "<img border=0 src='" . Globals::getConfig('root_url') . "/file-icons/mpg.png" . "'>";
				break;
			case "pdf":
				return "<img border=0 src='" . Globals::getConfig('root_url') . "/file-icons/pdf.png" . "'>";
				break;
			case "png":
				return "<img border=0 src='" . Globals::getConfig('root_url') . "/file-icons/png.png" . "'>";
				break;
			case "ppt":
				return "<img border=0 src='" . Globals::getConfig('root_url') . "/file-icons/ppt.png" . "'>";
				break;
			case "ps":
				return "<img border=0 src='" . Globals::getConfig('root_url') . "/file-icons/ps.png" . "'>";
				break;
			case "rpm":
				return "<img border=0 src='" . Globals::getConfig('root_url') . "/file-icons/rpm.png" . "'>";
				break;
			case "rar":
				return "<img border=0 src='" . Globals::getConfig('root_url') . "/file-icons/rar.png" . "'>";
				break;
			case "swf":
			case "flv":
			case "fla":
				return "<img border=0 src='" . Globals::getConfig('root_url') . "/file-icons/swf.png" . "'>";
				break;
			case "tar":
				return "<img border=0 src='" . Globals::getConfig('root_url') . "/file-icons/tar.png" . "'>";
				break;
			case "tgz":
				return "<img border=0 src='" . Globals::getConfig('root_url') . "/file-icons/tgz.png" . "'>";
				break;
			case "txt":
				return "<img border=0 src='" . Globals::getConfig('root_url') . "/file-icons/txt.png" . "'>";
				break;
			case "wav":
				return "<img border=0 src='" . Globals::getConfig('root_url') . "/file-icons/wav.png" . "'>";
				break;
			case "wmv":
				return "<img border=0 src='" . Globals::getConfig('root_url') . "/file-icons/wmv.png" . "'>";
				break;
			case "xls":
				return "<img border=0 src='" . Globals::getConfig('root_url') . "/file-icons/xls.png" . "'>";
				break;
			case "zip":
				return "<img border=0 src='" . Globals::getConfig('root_url') . "/file-icons/zip.png" . "'>";
				break;			
			default :
				return "<img border=0 src='" . Globals::getConfig('root_url') . "/file-icons/file.png" . "'>";
				break;
		}
	}
	
	public function getList($downloadid = 0, $cateid = 0)
	{
		$cache = $this->getCacheInstance();
		$key = $this->getKeyList($downloadid,$cateid);
		$result = $cache->getCache($key);
		if($result === FALSE)
		{
			$db = $this->getDbConnection();
			$query = "SELECT * FROM " . $this->_tablename . " WHERE downloadid=? AND cateid=? ORDER BY myorder,posteddate";
			$data = array($downloadid,$cateid);
			$result = $db->fetchAll($query,$data);			
			if(!is_null($result) && is_array($result))
			{
				$cache->setCache($key, $result);
			}
		}
		return $result;
	}
	
	public function getCountByCate($downloadid=0,$cateid=0)
	{
		$cache = $this->getCacheInstance();
		$key = $this->getKeyCount($downloadid,$cateid);
		$result = $cache->getCache($key);
		if($result === FALSE)
		{
			$db = $this->getDbConnection();
			$query = "SELECT count(*) AS mysum FROM " . $this->_tablename . " WHERE downloadid=? AND cateid=?";
			$data = array($downloadid,$cateid);
			$result = $db->fetchAll($query,$data);
			if(!is_null($result) && is_array($result) && count($result) == 1)
			{
				$result = $result[0]['mysum'];				
			}
			else
			{
				$result = 0;				
			}
			$cache->setCache($key,$result);			
		}
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
			$query = "SELECT * FROM " .$this->_tablename . " WHERE itemid='" . parent::adaptSQL($id) . "'";
			$result = $db->fetchAll($query);
			if($result != null && is_array($result))
			{
				$result = $result[0];
				$cache->setCache($key, $result);				
			}
		}
		return $result;
	}	
	
	public function insert($downloadid, $cateid, $data)
	{
		$data['downloadid'] = $downloadid;
		$data['cateid'] = $cateid;		
		$db = $this->getDbConnection();
		$result = $db->insert($this->_tablename, $data);
		if($result > 0)
		{
			$this->_deleteAllCache($downloadid,$cateid);			
		}
	}
	
	public function moveCate($id,$downloadid,$cateid,$cateid_new)
	{
		$data = array(
			'cateid' => $cateid_new
		);
		$result = $this->_update($id,$data);
		if($result > 0)
		{
			$this->_deleteAllCache($downloadid,$cateid);
			$this->_deleteAllCache($downloadid,$cateid_new,$id);		
		}
	}
	
	public function update($id, $downloadid, $cateid, $data)
	{
		if(array_key_exists('downloadid',$data)) unset($data['downloadid']);
		if(array_key_exists('cateid',$data)) unset($data['cateid']);
		$where = array();
		$where[] = "itemid='" . parent::adaptSQL($id) . "'";
		$db = $this->getDbConnection();
		$result = $db->update($this->_tablename, $data,$where);
		if($result > 0)
		{
			$this->_deleteAllCache($downloadid,$cateid,$id);			
		}
		
	}
	
	public function delete($id,$downloadid,$cateid)
	{
		//get current menu
		$current = $this->getDetail($id);
				
		$where = array();
		$where[] = "itemid='" . parent::adaptSQL($id) . "'";
		$db = $this->getDbConnection();
		$result = $db->delete($this->_tablename, $where);
		if($result > 0)
		{
			$this->_deleteAllCache($downloadid,$cateid,$id);			
		}
	}
	
	///private functions /////

	private function _update($id, $data)
	{
	
		$where = array();
		$where[] = "itemid='" . parent::adaptSQL($id) . "'";
		$db = $this->getDbConnection();
		$result = $db->update($this->_tablename, $data,$where);
		return $result;
	}
	
		
	private function _deleteAllCache($downloadid,$cateid,$id = null)
	{
		$cache = $this->getCacheInstance();
		$key = $this->getKeyList($downloadid,$cateid);
		$cache->deleteCache($key);
		$key = $this->getKeyCount($downloadid,$cateid);
		$cache->deleteCache($key);		
		if($id != null)
		{
			$key = $this->getKeyDetail($id);
			$cache->deleteCache($key);
		}		
	}
	
}
?>
<?php

class Business_Import_Request extends Business_Abstract
{
	private $_tablename = 'import_request';
	private static $_instance = null; 
	const EXPIRED = 3000; //secs
	const KEY_DETAIL = 'number.detail.%s'; //key of detail.id
	function __construct()
	{
		
	} 
	
	/**
	 * get instance of Business_Addon_Number
	 *
	 * @return Business_Addon_Number
	 */
	public static function getInstance()
	{
		if(self::$_instance == null)
		{
			self::$_instance = new Business_Import_Request();
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
		$db = Globals::getDbConnection('hnam_app', false);
		return $db;	
	}
	private function getCacheInstance() {
        $cache = GlobalCache::getCacheInstance('ws');
        return $cache;
    }
		
	public function getList()
	{
        $db = $this->getDbConnection();
        $query = "select * from " . $this->_tablename . " where 1 order by id desc";
        $result = $db->fetchAll($query);
        return $result;
	}
	private function getKeyDetail($id) {
        return sprintf(self::KEY_DETAIL, $id);
    }
	public function getDetail($id, $field = "*")
	{
            $id = intval($id);
            $db = $this->getDbConnection();
            $query = " SELECT ".$field." FROM " . $this->_tablename ." WHERE id = ?";
            $data = array($id);
            $result = $db->fetchAll($query,$data);
            if($result != null && is_array($result))
            {
                    return $result[0];
            }
            else return null;
	}
	public function getWhere($field_where,$value_where, $field = "*")
	{
            $db = $this->getDbConnection();
            $query = " SELECT ".$field." FROM " . $this->_tablename ." WHERE ".$field_where." = ?";
            $data = array($value_where);
            $result = $db->fetchAll($query,$data);
            if($result != null && is_array($result))
            {
                return $result;
            }
            else return null;
	}
	public function getListByInventory($field_where,$value_where, $field = "*")
	{
            $db = $this->getDbConnection();
            $query = " SELECT ".$field." FROM " . $this->_tablename ." WHERE ".$field_where." = ? order by id desc ";
            $data = array($value_where);
            $result = $db->fetchAll($query,$data);
            if($result != null && is_array($result))
            {
                return $result;
            }
            else return null;
	}
	public function getWhereRow($field_where,$value_where, $field = "*")
	{
            $db = $this->getDbConnection();
            $query = " SELECT ".$field." FROM " . $this->_tablename ." WHERE ".$field_where." = ? order by id desc";
            $data = array($value_where);
            $result = $db->fetchAll($query,$data);
            if($result != null && is_array($result))
            {
                return $result[0];
            }
            else return null;
	}
	public function update($id, $data)
	{
		
		$db = $this->getDbConnection();
		$where = array();	
		$where[] = "id = '" . parent::adaptSQL($id) . "'";
		try
		{			
			$result = $db->update($this->_tablename, $data, $where);
			
			return $result; 
		}
		catch(Exception $e)
		{
			return 0;
		}
	}
	
	public function insert($data)
	{
		$db = $this->getDbConnection();
		$result = $db->insert($this->_tablename,$data);
        return $result;
	}
	
	public function delete($id)
	{	
		$db = $this->getDbConnection();
		$where = array();
		$where[] = "id='" . parent::adaptSQL($id) . "'";
		$result = $db->delete($this->_tablename,$where);
                return $result;
	}
	public function getListOlder($id_num = 0, $limit=8)
	{
        $cache = $this->getCacheInstance();
        $key = $this->getKeyDetail('number.list.all'.$this->_tablename.$id_num.$limit);
        $cache->deleteCache($key);
        $result = $cache->getCache($key);
        if($result === FALSE)
        {
        	echo 'nnn';
            if ($id_num == 0)
                $query = "SELECT * FROM " . $this->_tablename . " WHERE id=? ORDER BY id desc";
            else
                $query = "SELECT * FROM " . $this->_tablename . " WHERE id=? ORDER BY id desc LIMIT 0,$limit";
            //echo $query;die;
            $db = $this->getDbConnection();
            $data = array($id_num);
            $result = $db->fetchAll($query,$data);
            $result = $result[0];
            $ret = $cache->setCache($key, $result, self::EXPIRED);
            //var_dump($result);die;

        }
        return $result;
	}
	public function send_mail($email_from = '',$name_from = '',$email_to = '',$name_to = '',$subject = '')
	{
		$settings = array('ssl'=>'ssl',
                        'port'=>465,
                        'auth' => 'login',
                        'username' => 'lmhieu1608@gmail.com',
                        'password' => 'huynhhieu1608');
        $transport = new Zend_Mail_Transport_Smtp('smtp.gmail.com', $settings);
        $email_from = $email_from;
        $name_from = $name_from;
        $email_to = $email_to;
        $name_to = $name_to;
        

        $mail = new Zend_Mail();
        $mail->setReplyTo($email_from, $name_from);
        $mail->setFrom($email_from, $name_from);
        $mail->addTo($email_to, $name_to);
        $mail->setSubject($subject);
        $mail->setBodyText("Email body");
        echo $email_from;die;
        $mail->send($transport);
        
	}
	public function sendEmailV3($from, $displayname,$to,$replyto, $subject, $body_html, $file_attached = null, $mail_config) {

        $mail_config = "smtp.gmail.com;587;saleonline@hnammobile.com;saleonline552015";

        if ($replyto == "")
            $replyto = $from;

        $arr_config = explode(';', $mail_config);

        //$host = $arr_config[0] . ':' . $arr_config[1];

        $host = $arr_config[0];
        $port = $arr_config[1];

        $username = $arr_config[2];
        $password = $arr_config[3];
        try {
            if ($port == 25) {
                $config = array(
                    //				'ssl' => 'tls',
                    'auth' => 'login',
                    'username' => $username,
                    'password' => $password,
                    'port' => $port
                );
            } else {
                $config = array(
                    'ssl' => 'tls',
                    'auth' => 'login',
                    'username' => $username,
                    'password' => $password,
                    'port' => $port
                );
            }
            //var_dump($config);die;
            $transport = new Zend_Mail_Transport_Smtp($host, $config);

            $mail = new Zend_Mail('utf-8');
            $mail->setType(Zend_Mime::MULTIPART_RELATED);
            $mail->setHeaderEncoding(Zend_Mime::ENCODING_BASE64);

            $mail->setReplyTo($replyto);
//			$mail->setBodyText(strip_tags($body_html));

            $mail->setFrom($from, $displayname);
            $mail->addTo($to);
            $mail->setSubject($subject);
            $mail->setBodyHtml($body_html);

            //$mail->setBodyHtml($body_html);
            $mail->send($transport);
//		    pre($result);
        } catch (Exception $ex) {
            return $ex->getMessage();
        }

        return "";
    }
}

?>
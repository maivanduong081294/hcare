<?php

class GlobalPermision {

    public static function getPermModules() {
        $config = Globals::getConfiguration();
        if (!isset($config->permission_modules)) {
            return array();
        }

        $list = $config->permission_modules->list;
        $arr = explode(',', $list);
        return $arr;
    }

    public static function getPermList($modulename) {

        $config = Globals::getConfiguration();

        if (!isset($config->permission_modules->$modulename)) {
            return null;
        } else {
            return $config->permission_modules->$modulename->toArray();
        }
    }

}

class GlobalsDB {

    private static $_db = null;
    public static $arrDB = array();

    public static function closeAllDbConnection() {
        if (is_array(self::$arrDB) && count(self::$arrDB) > 0) {
            foreach (self::$arrDB as $key => $value) {
                if ($value != null)
                    $value->closeConnection();
                //unset(self::$arrDB[$key]);	       				       			
            }
        }
    }

    public static function getDbConnection($dbName, $state = false) {
        if (self::$_db != null && $state) {
            return self::$_db;
        }

        if (isset(self::$arrDB[$dbName]) && self::$arrDB[$dbName] != null) {
            self::$_db = self::$arrDB[$dbName];
            return self::$arrDB[$dbName];
        }

        $config = Zend_Registry::get('configuration');

        $_db = Zend_Db::factory($config->$dbName);


        $debug = Globals::isDebug();

        if ($debug == true) {
            $_db->getProfiler()->setEnabled(true);
        }


        //$_db->query('SET NAMES UTF8');
        self::$arrDB[$dbName] = $_db;
        self::$_db = $_db;
        return $_db;
    }

}

class Globals {

    /**
     * cache object
     * db object
     * @var object
     */
    private static $_cache = null;
    private static $_dbName = null;
    private static $_db = null;

    public static function isMobile() {
        $useragent = $_SERVER['HTTP_USER_AGENT'];
        if (preg_match('/android.+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i', $useragent) || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|e\-|e\/|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(di|rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|xda(\-|2|g)|yas\-|your|zeto|zte\-/i', substr($useragent, 0, 4)))
            return true;
        return false;
    }
    
    static public function getConfiguration() {
        return Zend_Registry::get('configuration');
    }

    /*
     * method for partition db by userid
     */

    static public function setDbByUserId($userId, $dbName = '', $maxUserId = '') {
        if ($dbName != '' && $maxUserId != '') {
            $part = (int) ($userId / $maxUserId);
            $dbName = empty($part) ? $dbName : $dbName . $part;
            return $dbName;
        } else {
            $globalConfig = Zend_Registry::get('configuration');
            $maxUserId = $globalConfig->profile->dbmaxrecord;
            return self::setDbByUserId($userId, 'profile', $maxUserId);
        }
    }

    /*
     * method for partition table by userid
     */

    static public function setTblByUserId($userId, $tblName, $maxUserId) {
        $part = (int) ($userId / $maxUserId);
        $tblName = empty($part) ? $tblName : $tblName . $part;
        return $tblName;
    }

    /*
     * method for set db name
     */

    static public function setDbName($dbName) {
        self::$_dbName = $dbName;
        return self::$_dbName;
    }

    static public function getConfig($name = '') {
        if ($name == '')
            return null;
        $globalConfig = Zend_Registry::get('configuration');
        if (isset($globalConfig->$name)) {
            return $globalConfig->$name;
        }
        else
            return null;
    }

    /*
     * method for get db name
     */

    static public function getDbName() {
        return self::$_dbName;
    }

    /*
     * method for get db cache
     * var $state : for check if db name is changed 
     * $state = false = changed
     */
    public static function getDbInstance($instance = 'windows') {
        $db = Maro_Db_Connect::factory($instance);
        return $db;
    }
    static public function getDbConnection($dbName, $state = false) {
        return GlobalsDB::getDbConnection($dbName, $state);
    }

    static public function filterAlphabet($var) {
        if (substr(ucfirst($var['displayname']), 0, 1) == 'C') {
            return $var;
        }
    }

    static function getStaticUrl() {
        $configuration = Zend_Registry::get('configuration');
        if (isset($configuration->staticurl)) {
            return $configuration->staticurl;
        }
        else
            return "/";
    }

    static public function getBaseUrl() {
        $configuration = Zend_Registry::get('configuration');
        if (isset($configuration->baseurl)) {
            return $configuration->baseurl;
        }
        else
            return "/";
    }

    static public function getVersion() {
        //https://hnamnew.com//min-jquery
        //https://hnamnew.com//min-sheets
        if (APP_ENV == 'production') {
            $version =4832;  
        }else
            $version =strtotime('now');  
        return $version;
    }

    static public function variable_get($name, $default_value = null) {
        return Business_Common_Variables::variable_get($name, $default_value);
    }

    static public function variable_set($name, $value) {
        
    }

    static public function adaptData($value) {
        return str_replace('"', '&quot;', $value);
    }

    static public function readaptData($value) {
        return str_replace('&quot;', '"', $value);
    }

    /**
     * utf8 to ascii
     */
    static function utf8ToAscii($str) {
        $chars = array
            (
            'a' => array('áº¤', 'áº¦', 'áº¨', 'áºª', 'áº¬', 'áº®', 'áº°', 'áº²', 'áº´', 'áº¶', 'Ã�', 'Ã€', 'áº¢', 'Ãƒ', 'áº ', 'Ã‚', 'Ä‚'),
            'e' => array('áº¾', 'á»€', 'á»‚', 'á»„', 'á»†', 'Ãª', 'Ã‰', 'Ãˆ', 'áºº', 'áº¼', 'áº¸', 'ÃŠ'),
            'i' => array('Ã�', 'ÃŒ', 'á»ˆ', ' Ä¨', 'á»Š'),
            'o' => array('á»�', 'á»’', 'á»”', 'á»�', 'á»˜', 'á»š', 'á»œ', 'á»ž', 'á» ', 'á»¢', 'Ã“', 'Ã’', 'á»Ž', 'Ã•', 'á»Œ', 'Ã”', 'Æ '),
            'u' => array('á»¨', 'á»ª', 'á»®', 'á»®', 'á»°', 'Ã™', 'á»¦', 'Å¨', 'á»¤', 'Æ¯'),
            'y' => array('Ã�', 'á»²', 'á»¶ ', 'á»¸', 'á»´'),
            'd' => array('Ä�'),
        );

        foreach ($chars as $key => $arr) {
            foreach ($arr as $val) {
                $str = str_replace($val, $key, $str);
                return $str;
            }
        }
    }

    static function getCurrentDateTime($extra_time = 0) {
        if ($extra_time == 0)
            return date("Y-m-d H:i:s");
        else {

            $current_time = time() + $extra_time;
            return date("Y-m-d H:i:s", $current_time);
        }
    }

    /**
     * convert array result resource to array 
     */
    static function resultToArray($result) {
        $arrTmp = array();
        foreach ($result as $key => $value) {
            $arrTmp[$value['id']] = $value['name'];
        }
        return $arrTmp;
    }

    /**
     * method: doPaging
     * paging data
     * @return paginator object
     */
    static function doPagingEmail($arrData, $page, $view, $rpp, $totalpage) {
        // get paging configuration

        $paginator = Zend_Paginator::factory($arrData);
        $paginator->setCurrentPageNumber($page)
                ->setItemCountPerPage($rpp)
                ->setPageRange(5);
        $viewRenderer = Zend_Controller_Action_HelperBroker
                ::getStaticHelper('viewRenderer');
        // setup view helpers
        //$view->setScriptPath(APPLICATION_PATH . '/modules/admin/views/scripts/');
        $viewRenderer->setView($view);
        $viewRenderer->view->paginator = $paginator;
        $viewRenderer->view->currentPage = $page;
        $viewRenderer->view->scrollingStyle = $arrPaging['scrollingStyle'];
        Zend_View_Helper_PaginationControl::setDefaultViewPartial(
                'common/' . $arrPaging['paginationControl'] . '_pagination_control.phtml');
    }

    /**
     * method: doPaging
     * paging data
     * @return paginator object
     */
    static function doPaging($arrData, $page, $view) {
        // get paging configuration
        $config = Zend_Registry::get('configuration');
        $arrPaging = $config->pagination->toArray();
        $paginator = Zend_Paginator::factory($arrData);
        $paginator->setCurrentPageNumber($page)
                ->setItemCountPerPage($arrPaging['itemPerPage'])
                ->setPageRange($arrPaging['pageRange']);
        $viewRenderer = Zend_Controller_Action_HelperBroker
                ::getStaticHelper('viewRenderer');
        // setup view helpers
        //$view->setScriptPath(APPLICATION_PATH . '/modules/admin/views/scripts/');
        $viewRenderer->setView($view);
        $viewRenderer->view->paginator = $paginator;
        $viewRenderer->view->currentPage = $page;
        $viewRenderer->view->scrollingStyle = $arrPaging['scrollingStyle'];
        Zend_View_Helper_PaginationControl::setDefaultViewPartial(
                'common/' . $arrPaging['paginationControl'] . '_pagination_control.phtml');
    }

    static function isDebug() {
        if (isset($_REQUEST["debug"])) {
            $debug = $_REQUEST["debug"];
            if ($debug == "true")
                return true;
            else
                return false;
        }
        else {
            return false;
        }
    }

    public static function dumpLog($content) {

        //if(Zend_Registry::isRegistered('logger') && APP_ENV == "production")
        if (Zend_Registry::isRegistered('logger')) {
            $logger = Zend_Registry::get('logger');

            $logger->log($content, Zend_Log::EMERG);
        } else {
            
        }
    }

}

/**
 * GlobalCache class
 * Description: cache object global for caching db, deleleting cache with key, subkey
 */
class GlobalCache {

    private static $_main_array_name = "_main_array_";
    private static $_local_cache = array();
    private static $_enable = null;

    private static function checkEnableCache() {
        if (is_null(self::$_enable)) {
            $configuration = Zend_Registry::get("configuration");
            if (isset($configuration->caching->enable)) {
                self::$_enable = $configuration->caching->enable;
            } else {
                self::$_enable = false;
            }
        }
        return self::$_enable;
    }

    /**
     * get globalcache
     *
     * @param string $instance
     * @return Maro_Cache_Interface
     */
    public static function getCacheInstance($instance = 'default') {
        $cache = Maro_Cache_MemGlobalCache::getGlobalCache($instance);
        return $cache;
    }
    

    public static function x_flushLocalCache($instance = 'default') {
        Maro_Cache_MemGlobalCache::flushLocalCache($instance);
    }

    public static function flushLocalCache($instance = 'default') {
        if (!self::checkEnableCache())
            return;
        GlobalCache::x_flushLocalCache($instance);
    }

    public static function x_getMultiCache($keys, $instance = 'default') {
        return Maro_Cache_MemGlobalCache::getMultiCache($keys, $instance);
    }

    public static function getMultiCache($keys, $instance = 'default') {
        if (!self::checkEnableCache())
            return array();
        return GlobalCache::x_getMultiCache($keys, $instance);
    }

    //new function for cache multi-instance
    public static function x_getCache($key, $instance = 'default') {
        return Maro_Cache_MemGlobalCache::getCache($key, $instance);
    }

    public static function getCache($key, $instance = 'default') {
        if (!self::checkEnableCache())
            return FALSE;
        return GlobalCache::x_getCache($key, $instance);
    }

    //new function for cache multi-instance
    public static function x_deleteCache($key, $instance = 'default') {
        Maro_Cache_MemGlobalCache::deleteCache($key, $instance);
    }

    public static function deleteCache($key, $instance = 'default') {
        if (!self::checkEnableCache())
            return array();
        GlobalCache::x_deleteCache($key, $instance);
    }

    public static function x_setCache($key, $value, $instance = 'default', $expireTime = 0, $compress = 0) {
        Maro_Cache_MemGlobalCache::setCache($key, $value, $instance, $expireTime, $compress);
    }

    public static function setCache($key, $value, $instance = 'default', $expireTime = 0, $compress = 0) {
        if (!self::checkEnableCache())
            return;
        if (is_int($instance)) {
            $expireTime = $instance;
            $_instance = '';
            GlobalCache::x_setCache($key, $value, $_instance, $expireTime, $compress);
        } else {
            GlobalCache::x_setCache($key, $value, $instance, $expireTime, $compress);
        }
    }

    

}

?>
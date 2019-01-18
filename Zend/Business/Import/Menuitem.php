<?php

class Business_Import_MenuItem extends Business_Abstract {

    private $_tablename = 'ws_menuitem';

    const KEY_LIST = 'menuitem.list.%s.%s';     //key of list by menuname and language
    const KEY_LIST_FILTER = 'menuitem.list.filter.%s.%s.%s.%s';     //key of list by menuname and language
    const KEY_LIST_PARENT = 'menuitem.list-parent.%s.%s';  //key of list parent by menuname and language	
    const KEY_DETAILS = 'menuitem.detail.%s';    //key detail by menuitemid 
    const KEY_LIST_PID = 'menuitem.list.pid.%s';    //key detail by menuitemid
    const EXPIRED = 60; //seconds

    private static $id_filter = 0;
    private static $depth_filter = 1;
    private static $_instance = null;

    function __construct() {
        
    }

    /**
     * get instance of Business_Import_MenuItem
     *
     * @return Business_Import_MenuItem
     */
    public static function getInstance() {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    private function getKeyList($menuname, $lang) {
        return sprintf(self::KEY_LIST, $menuname, $lang);
    }

    private function getKeyListFilter($menuname, $lang, $id, $depth) {
        return sprintf(self::KEY_LIST_FILTER, $menuname, $lang, $id, $depth);
    }

    private function getKeyParentList($menuname, $lang) {
        return sprintf(self::KEY_LIST_PARENT, $menuname, $lang);
    }

    private function getKeyDetail($id) {
        return sprintf(self::KEY_DETAILS, $id);
    }

    private function getKeyListByPid($id) {
        return sprintf(self::KEY_LIST_PID, $id);
    }

    /**
     * Get DB Connection
     *
     * @return Zend_Db_Adapter_Abstract
     */
    private function getDbConnection() {
        $db = Globals::getDbConnection('hnam_wh');
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
    public function getListByItemId($strItemid)
	{
            $db = $this->getDbConnection();
            $query = " SELECT * FROM " . $this->_tablename ." WHERE itemid IN ($strItemid)";
            $result = $db->fetchAll($query);
            if($result != null && is_array($result))
            {
                    return $result;
            }
            else return null;
	}
    public function getMenuById($itemid){
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " WHERE itemid=?";
//        var_dump($query);exit();
        $data = array($itemid);
        $result = $db->fetchAll($query, $data);
        return $result[0];
    }

    public function getDetailByMenuname($menuname = '') {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " WHERE menuname=?";
        $data = array($menuname);
        $result = $db->fetchAll($query, $data);
        return $result[0];
    }

    public function getDetailByTitle($title = '') {
        $cache = $this->getCacheInstance();
        $key = md5($title);
        $result = $cache->getCache($key);
        if ($result === FALSE) {
            $db = $this->getDbConnection();
            $query = "SELECT * FROM " . $this->_tablename . "   WHERE title=?";
            $data = array($title);
            $result = $db->fetchAll($query, $data);
            if (!is_null($result) && is_array($result)) {
                $cache->setCache($key, $result[0]);
                $result = $result[0];
            }
        }

        return $result;
    }

    public function getListByName($menuname, $lang = 1, $ordering = null) {
        $cache = $this->getCacheInstance();

        $key = $this->getKeyList($menuname, $lang);
        $result = $cache->getCache($key . md5($ordering));
//        $result =FALSE;
        if ($result === FALSE) {
            $db = $this->getDbConnection();
            if ($ordering == null) {
                $query = "SELECT * FROM " . $this->_tablename . " WHERE menuname=? AND lang=? ORDER BY depth asc, myorder asc, itemid";
            } else {
                $query = "SELECT * FROM " . $this->_tablename . " WHERE menuname=? AND lang=? ORDER BY $ordering";
            }

            $data = array($menuname, $lang);
            $result = $db->fetchAll($query, $data);
            if (!is_null($result) && is_array($result))
                $cache->setCache($key . md5($ordering), $result);
        }

        return $result;
    }
    
    public function getListFilter($menuname, $id = 0, $lang = '', $depth = null, $ordering = null) {
        $cache = $this->getCacheInstance();
        $_key = $this->getKeyListFilter($menuname, $lang, $id, $depth);
//        $cache->deleteCache($key);exit();
        $result = $cache->getCache($_key . md5($ordering));
//        $result=FALSE;
        if ($result === FALSE) {
            if ($ordering == null) {
                $list = $this->getListByName($menuname, $lang);
            } else {
                $list = $this->getListByName($menuname, $lang, $ordering);
            }
            
            
            self::$id_filter = $id;
            if ($depth == null) {
                $list = array_filter($list, "Business_Import_MenuItem::filterDaisyString");
            } elseif ($id == 0) {
                self::$depth_filter = $depth;
//                $list = array_filter($list, "Business_Import_MenuItem::filterDepth");
            } else {
                self::$depth_filter = $depth;
                $list = array_filter($list, "Business_Import_MenuItem::filterDaisyStringAndDepth");
            }
            
//            if($menuname =='menu_acc'){
//                echo "<pre>";
//            var_dump($list);
//            exit();
//            }
            $return = array();
            if (is_array($list) && count($list) > 0) {
                foreach ($list as $key => $value) {
                    $return[] = $value;
                }
            }
            $result = $return;
                        


            $cache->setCache($_key . md5($ordering), $result, self::EXPIRED);
        }
        return $result;
    }

    public function getParentMenu($menuname, $lang = '') {
        $result = $this->getListFilter($menuname, 0, $lang, 1);
        //return $result;
        $cache = $this->getCacheInstance();
        $key = $this->getKeyParentList($menuname, $lang);
        $result = $cache->getCache($key);
        if ($result === FALSE) {
            $db = $this->getDbConnection();
            $query = "SELECT * FROM " . $this->_tablename . " WHERE pid=0 AND menuname=? AND lang=? ORDER BY depth asc, myorder asc, itemid";
            $data = array($menuname, $lang);
            $result = $db->fetchAll($query, $data);
            if (!is_null($result) && is_array($result)) {
                $cache->setCache($key, $result);
            }
        }
        return $result;
    }

    public function moveParentMenu($id, $menuname, $pid_new, $lang = '') {
        $depth = 0;
        $daisystring = "";
        $parent = null;

        if (intval($pid_new) > 0) {
            //load new parent
            $parent = $this->getDetail($pid_new, $menuname, $lang);
            if ($parent != null) {
                //check truong hop pid_new la con cua id
                $find = " " . $id . " ";
                $pos = strpos($parent['daisystring'], $find);
                if ($pos !== false) {
                    return -1;   // ko the chuyen parent mo la con cua menu hien tai
                }
                $depth = intval($parent['depth']);
                $daisystring = $parent['daisystring'];
            }
        }

        //update depth and daisystring to current menu
        $depth++;
        if ($pid_new != 0)
            $daisystring .= ", " . $id . " ";
        else
            $daisystring = " " . $id . " ";

        $data = array(
            "pid" => $pid_new,
            "depth" => $depth,
            "daisystring" => $daisystring
        );

        $this->updateData($id, $menuname, $lang, $data);

        //update haschild to new parent
        if ($parent != null) {
            $data = array(
                "haschild" => "1"
            );
            $this->updateData($pid_new, $menuname, $lang, $data);
        }

        //update depth, daisystring for all sub menuitem of current menuitem
        $submenuitem = $this->getListFilter($menuname, $id, $lang);
        if ($submenuitem != null && is_array($submenuitem) && count($submenuitem) > 0) {
            for ($i = 0; $i < count($submenuitem); $i++) {
                $myid = $submenuitem[$i]['itemid'];
                if ($myid == $id)
                    continue; //skip current menuitem
                $mypid = $submenuitem[$i]['pid'];
                $parent = $this->getDetail($mypid, $menuname, $lang);
                if ($parent != null) {
                    $depth = intval($parent['depth']);
                    $daisystring = $parent['daisystring'];
                    $depth++;
                    $daisystring .= ", " . $myid . " ";
                } else {
                    $depth = 1;
                    $daisystring = " " . $myid . " ";
                }

                $data = array(
                    "depth" => $depth,
                    "daisystring" => $daisystring
                );
                $this->updateData($myid, $menuname, $lang, $data);
            }
        }

        //update haschild to old parent
        $parent_old = $this->getDetail($pid, $menuname, $lang);
        if ($parent_old == null) {
            $result = $this->getListFilter($menuname, $pid, $lang);
            if ($result == null || (is_array($result) && count($result) > 0)) {
                $data = array(
                    "haschild" => "0"
                );
                $this->updateData($pid, $menuname, $lang);
            }
        }

        //clear all cache with menuname and lang
        $this->_deleteAllCache($menuname, $lang);
    }

    //delete all menuitems by menuname and lang
    public function deleteByMenuName($menuname, $lang = '') {
        $menuitemlist = $this->getListByName($menuname, $lang);
        if ($menuitemlist != null && is_array($menuitemlist) && count($menuitemlist) > 0) {
            $db = $this->getDbConnection();
            $where = array();
            $where[] = "menuname='" . parent::adaptSQL($menuname) . "'";
            $where[] = "lang='" . parent::adaptSQL($lang) . "'";
            $result = $db->delete($this->_tablename, $where);
            if ($result > 0) {
                for ($i = 0; $i < count($menuitemlist); $i++) {
                    $id = $menuitemlist[$i]['itemid'];
                    $this->_deleteDetailCache($id);
                }
                $this->_deleteAllCache($menuname, $lang);
            }
        }
    }

    public function getDetailByDeltaAndCateID($cateid, $delta) {
        $cache = $this->getCacheInstance();
        $key = 'menu.detail.cate.delta.' . $cateid . $delta;
        $result = $cache->getCache($key);
        if ($result === FALSE) {
            $db = $this->getDbConnection();
            $query = "SELECT * FROM " . $this->_tablename . " WHERE itemid=? AND delta =?";

            $data = array($cateid, $delta);

            $result = $db->fetchAll($query, $data);

            if (!is_null($result) && is_array($result) && count($result) == 1) {
                $result = $result[0];
                $cache->setCache($key, $result);
            }
        }
        return $result;
    }

    public function getDetailById($id) {
        return $this->getDetail($id, '', '');
    }

    public function getDetail($id, $menuname = '', $lang = '') {
        if (intval($id) == 0)
            return null;
        $cache = $this->getCacheInstance();
        $key = $this->getKeyDetail($id);
        $result = $cache->getCache($key);

        if ($result === FALSE) {
            $db = $this->getDbConnection();
            $query = "SELECT * FROM " . $this->_tablename . " WHERE itemid=?";
            $data = array();
            $data[] = $id;

            $result = $db->fetchAll($query, $data);
            if (!is_null($result) && is_array($result) && count($result) == 1) {
                $result = $result[0];
            }
            else
                $result = false;
            $cache->setCache($key, $result);
        }
        if ($result == null)
            return null;
        return $result;
    }

    public function getListByPid($pid) {
        if (intval($pid) == 0)
            return null;
        $cache = $this->getCacheInstance();
        $key = $this->getKeyListByPid($pid);
        $result = $cache->getCache($key);

        if ($result === FALSE) {
            $db = $this->getDbConnection();
            $query = "SELECT * FROM " . $this->_tablename . " WHERE pid=?";
            $data = array();
            $data[] = $pid;

            $result = $db->fetchAll($query, $data);
            if (!is_null($result) && is_array($result)) {
                $cache->setCache($key, $result, self::EXPIRED);
            }
            else
                $result = false;
        }
        if ($result == null)
            return null;
        return $result;
    }

    public function updateMenuItem($id, $menuname, $data, $lang = '') {
        if (array_key_exists('menuname', $data))
            unset($data['menuname']);
        if (array_key_exists('itemid', $data))
            unset($data['itemid']);
        if (array_key_exists('pid', $data))
            unset($data['pid']);
        if (array_key_exists('haschild', $data))
            unset($data['haschild']);
        if (array_key_exists('depth', $data))
            unset($data['depth']);
        if (array_key_exists('daisystring', $data))
            unset($data['daisystring']);

        $result = $this->updateData($id, $menuname, $lang, $data);
        return $result;
    }

    public function deleteMenuItem($id, $menuname, $lang = '') {
        //lay pid cua menuitem hien tai
        $current_item = $this->getDetail($id, $menuname, $lang);
        if ($current_item == null)
            return false;

        $pid = $current_item["pid"];
        $_depth = 0;
        $_daisystring = "";

        if ($pid != 0) {
            //lay depth va daisystring cua parent
            $parent = $this->getDetail($pid, $menuname, $lang);
            if ($parent == null)
                return false;
            $_depth = $parent["depth"];
            $_daisystring = $parent["daisystring"];
        }

        //update depth, daisystring for all sub menu item of current item
        $sub_items = $this->getListFilter($menuname, $id, $lang);
        if ($sub_items != null && is_array($sub_items) && count($sub_items) > 0) {
            for ($i = 0; $i < count($sub_items); $i++) {
                $myid = $sub_items[$i]["itemid"];
                if ($myid == $id)
                    continue; //skip current item
                $mypid = $sub_items[$i]["pid"];
                if ($mypid == $id) {  //lien ke voi current item
                    $depth = $_depth++;
                    if ($_daisystring == "") {
                        $daisystring = " " . $myid . " ";
                    } else {
                        $daisystring = ", " . $myid . " ";
                    }
                    $data = array(
                        "pid" => $pid,
                        "depth" => $depth,
                        "daisystring" => $daisystring
                    );
                    $this->updateData($myid, $menuname, $lang, $data);
                } else {
                    $myparent = $this->getDetail($mypid, $menuname, $lang);
                    if ($myparent != null) {
                        $depth = intval($myparent["depth"]);
                        $daisystring = $myparent["daisystring"];
                        $depth++;
                        $daisystring .= ", " . $myid . " ";
                    } else {
                        $depth = 1;
                        $daisystring = " " . $myid . " ";
                    }

                    $data = array(
                        "depth" => $depth,
                        "daisystring" => $daisystring
                    );
                    $this->updateData($myid, $menuname, $lang, $data);
                }
            }
        }

        $this->_deleteMenuItem($id, $menuname, $lang);
        return true;
    }

    public function insertMenuItem($menuname, $lang = '', $pid = 0, $title, $shortcontent, $myorder = 0, $hide = 0, $expanded = 0, $submenu = '', $submenu_menustart = '', $submenu_lang = '', $module = '', $delta = '', $newwindow = 0, $meta_data, $warrantyid) {

        if ($pid == 0) {
            //add new menuitem
            $data = array(
                "menuname" => $menuname,
                "lang" => $lang,
                "title" => $title,
                "shortcontent" => $shortcontent,
                "hide" => $hide,
                "myorder" => $myorder,
                "expanded" => $expanded,
                "submenu" => $submenu,
                "submenu_menustart" => $submenu_menustart,
                "submenu_lang" => $submenu_lang,
                "depth" => 1,
                "daisystring" => "",
                "metadata" => $meta_data,
                "warrantyid" => $warrantyid
            );

            $db = $this->getDbConnection();
            $result = $db->insert($this->_tablename, $data);

            if ($result > 0) {
                $lastid = $db->lastInsertId($this->_tablename);
                $daisystring = " " . $lastid . " ";
                $data = array(
                    "daisystring" => $daisystring
                );
                $where = array();
                $where[] = "itemid='" . parent::adaptSQL($lastid) . "'";
                $where[] = "menuname='" . parent::adaptSQL($menuname) . "'";
                $where[] = "lang='" . parent::adaptSQL($lang) . "'";
                $result = $db->update($this->_tablename, $data, $where);
                if ($result > 0) {
                    $this->_deleteAllCache($menuname, $lang, $lastid);
                }
                return $lastid;
            }
        } else {
            $db = $this->getDbConnection();
            $result = $this->getDetail($pid, $menuname, $lang);
            if ($result != null) {
                $depth = intval($result['depth']);
                $depth++;
                $daisystring = $result['daisystring'];

                $data = array(
                    "menuname" => $menuname,
                    "lang" => $lang,
                    "pid" => $pid,
                    "title" => $title,
                    "hide" => $hide,
                    "myorder" => $myorder,
                    "depth" => $depth,
                    "submenu" => $submenu,
                    "submenu_menustart" => $submenu_menustart,
                    "submenu_lang" => $submenu_lang,
                    "daisystring" => ""
                );

                $result = $db->insert($this->_tablename, $data);
                $lastid = $db->lastInsertId($this->_tablename);
                $daisystring .= ", " . $lastid . " ";
                $data = array(
                    "daisystring" => $daisystring
                );

                $this->updateData($lastid, $menuname, $lang, $data);

                //update haschild of pid
                $data = array(
                    "haschild" => "1"
                );
                $this->updateData($pid, $menuname, $lang, $data);
                $this->_deleteAllCache($menuname, $lang);
                return $lastid;
            }
        }
    }

    ///private functions /////
    //delete one menuitem
    private function _deleteMenuItem($id, $menuname, $lang = '') {
        $db = $this->getDbConnection();
        $where = array();
        $where[] = "itemid='" . parent::adaptSQL($id) . "'";
        $where[] = "menuname='" . parent::adaptSQL($menuname) . "'";
        $where[] = "lang='" . parent::adaptSQL($lang) . "'";
        $result = $db->delete($this->_tablename, $where);

        if ($result > 0) {
            $this->_deleteAllCache($menuname, $lang, $id);
        }
    }

    private function updateData($id, $menuname, $lang, $data) {
        if (array_key_exists('menuname', $data))
            unset($data['menuname']);
        if (array_key_exists('itemid', $data))
            unset($data['itemid']);
        if (array_key_exists('pid', $data))
            unset($data['pid']);
        if (array_key_exists('lang', $data))
            unset($data['lang']);

        $db = $this->getDbConnection();

        $where = array();
        $where[] = "itemid='" . parent::adaptSQL($id) . "'";
        $where[] = "menuname='" . parent::adaptSQL($menuname) . "'";
        $where[] = "lang='" . parent::adaptSQL($lang) . "'";

        $result = $db->update($this->_tablename, $data, $where);
        if ($result > 0) {
            //xoa cache
            $this->_deleteAllCache($menuname, $lang, $id);
        }
        return $result;
    }

    private function _deleteDetailCache($id) {
        $cache = $this->getCacheInstance();
        $key = $this->getKeyDetail($id);
        $cache->deleteCache($key);
    }

    private function _deleteAllCache($menuname, $lang, $id = null) {
        $cache = $this->getCacheInstance();

        //xoa cache KEY_LIST
        $key = $this->getKeyList($menuname, $lang);
        $cache->deleteCache($key);

        //xoa cache KEY_LIST_PARENT
        $key = $this->getKeyParentList($menuname, $lang);
        $cache->deleteCache($key);

        //xoa cache detail
        if ($id != null && intval($id) > 0) {
            $key = $this->getKeyDetail($id);
            $cache->deleteCache($key);
        }
    }

    static function filterDepth($var) {
        if (intval($var["depth"]) == self::$depth_filter)
            return true;
        else
            return false;
    }

    static function filterDaisyString($var) {
        if (self::$id_filter == 0)
            return true;
        $find = " " . self::$id_filter . " ";
        $pos = strpos($var["daisystring"], $find);
        if ($pos !== false)
            return true;
        else
            return false;
    }

    static function filterDaisyStringAndDepth($var) {
        $find = " " . self::$id_filter . " ";
        $pos = strpos($var["daisystring"], $find);
        if ($pos !== false) {
            if (intval($var["depth"]) == self::$depth_filter)
                return true;
            else
                return false;
        }
        else
            return false;
    }

    public function getMenuItem($menuname1, $menuname2) {

        $db = $this->getDbConnection();
        //$query = "SELECT * FROM " . $this->_tablename . " WHERE menuname=".$menuname1." OR menuname=".$menuname2;
        $query = "select * from $this->_tablename where menuname like '%$menuname1%' OR  menuname like '%$menuname2%' ";
        $result = $db->fetchAll($query);
        return $result;
    }

    public function getMenuItemOption($menuname) {
        $db = $this->getDbConnection();
        $query = "select * from $this->_tablename where menuname like '%$menuname%'";
        $result = $db->fetchAll($query);
        return $result;
    }

}

?>
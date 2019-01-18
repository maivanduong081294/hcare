<?php

class Business_WsNew_NewsItem extends Business_Abstract
{

    private $_tablename = 'ws_newsitem';

    const EXPIRED                 = 3000; // secs
    const KEY_ALL_LIST            = 'newsitem.list.%s.%s.%s.%s';
    const KEY_ALL_LIST_LAST       = 'newsitem.list.last.%s.%s.%s.%s';
    const KEY_ALL_LIST_PAGINATOR  = 'newsitem.list.%s.%s.%s.%s';
    const KEY_LIST_CHANNEL_CATEID = 'newsitem.list.channel.cateid.%s.%s.%s.%s';
    const KEY_LIST_STAR_LIST      = 'newsitem.star.list.%s.%s';
    const KEY_LIST_COUNT          = 'newsitem.count.list.%s';
    const KEY_DETAIL              = 'newsitem.detail.%s';
    const KEY_DETAIL_BY_LIST_ID   = 'newsitem.detail.listid.%s';
    const KEY_DETAIL_TITLE        = 'newsitem.detail.%s.%s';
    // key of list by newsid,cateid
    private static $_instance = null;

    public function __construct()
    {}

    // public static function
    /**
     * get instance of Business_WsNew_NewsItem
     *
     * @return Business_WsNew_NewsItem
     */
    public static function getInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    private function getKeyCountList($count)
    {
        return sprintf(self::KEY_LIST_COUNT, $count);
    }
    private function getKeyAllListLast($limit, $enable,$hit,$cateid)
    {
        return sprintf(self::KEY_ALL_LIST_LAST,$limit, $enable,$hit,$cateid);
    }
    private function getKeyAllList($cateids, $newsid, $limit, $enable)
    {
        return sprintf(self::KEY_ALL_LIST, $cateids, $newsid, $limit, $enable);
    }
    private function getKeyAllListPaginator($cateids, $newsid, $limit, $to)
    {
        return sprintf(self::KEY_ALL_LIST_PAGINATOR, $cateids, $newsid, $limit, $to);
    }
    private function getKeyListChannelCateid($cateid, $newsid, $limit,$to)
    {
        return sprintf(self::KEY_LIST_CHANNEL_CATEID, $cateid, $newsid, $limit,$to);
    }
    private function getKeyListStarList($cateid, $newsid)
    {
        return sprintf(self::KEY_LIST_STAR_LIST, $cateid, $newsid);
    }

    private function getKeyDetailByListId($Listid)
    {
        return sprintf(self::KEY_DETAIL_BY_LIST_ID, $Listid);
    }

    private function getKeyDetail($id)
    {
        return sprintf(self::KEY_DETAIL, $id);
    }
    private function getKeyDetailByTitle($title, $limit)
    {
        return sprintf(self::KEY_DETAIL_TITLE, $title, $limit);
    }
    /**
     * Get DB Connection
     *
     * @return Zend_Db_Adapter_Abstract
     */
    private function getDbConnection()
    {
        $db = Globals::getDbConnection('maindb');
        return $db;
    }

    /**
     * Enter description here...
     *
     * @return Maro_Cache
     */
    private function getCacheInstance()
    {
        $cache = GlobalCache::getCacheInstance('pro');
        return $cache;
    }

        public function getListChannelByTitle($title,$listCateid, $limit = null)
    {
        // get channel newsitem
        if ($limit != null) {
            $limit = " LIMIT  $limit ";
        }

        if ($listCateid != null) {
            $and =  " and p.title like '%$title%'  and p.cateid in(" . $listCateid['cateid'] . ") and p.newsid=" . $listCateid['delta'];
        }

        $cache  = $this->getCacheInstance();
        $key    = 'video-detail-title-'.md5($title.$listCateid['cateid'].$listCateid['delta'].$limit);
        $result = $cache->getCache($key);
        //  $result=false;
        if ($result === false) {
            $db     = $this->getDbConnection();
            $query  = "SELECT p.title_seo,p.hits,p.posteddate,p.itemid,p.thumb,p.title,p.newimage_path,p.posteddate,p.link,p.shortcontent,p.fullcontent,c.title as title_parent FROM " . $this->_tablename . " as p  INNER JOIN ws_menuitem as c on p.cateid =c.itemid  where 1=1 $and  and enabled=1 ORDER BY  p.myorder asc, p.posteddate desc $limit";
            $result = $db->fetchAll($query);
            if (!is_null($result) && is_array($result)) {
                $cache->setCache($key, $result, self::EXPIRED);
            }
        }
        return $result;

    }



    public function getListChannelByCateid($listCateid, $limit = null,$to = 0)
    {
        // get channel newsitem
        if ($limit != null) {
             $limit = " limit $limit OFFSET $to";
        }

        if ($listCateid != null) {
            $and = "  and cateid in(" . $listCateid['cateid'] . ") and newsid=" . $listCateid['delta'];
        }

        $cache  = $this->getCacheInstance();
        $key    = $this->getKeyListChannelCateid($listCateid['cateid'], $listCateid['delta'], $limit,$to);
        $result = $cache->getCache($key);
        //  $result=false;
        if ($result === false) {
            $db     = $this->getDbConnection();
            $query  = "SELECT p.title_seo,p.hits,p.posteddate,p.itemid,p.thumb,p.title,p.newimage_path,p.posteddate,p.link,p.shortcontent,p.fullcontent,c.title as title_parent,p.social_img FROM " . $this->_tablename . " as p  INNER JOIN ws_menuitem as c on p.cateid =c.itemid  where 1=1 $and  and enabled=1 ORDER BY  p.myorder asc, p.posteddate desc $limit";
            $result = $db->fetchAll($query);
            if (!is_null($result) && is_array($result)) {
                $cache->setCache($key, $result, self::EXPIRED);
            }
        }
        return $result;

    }

    public function starList($listCateid)
    {

        $cache  = $this->getCacheInstance();
        $key    = $this->getKeyListStarList($listCateid['cateid'], $listCateid['delta']);
        $result = $cache->getCache($key);
        // $result=false;
        if ($result === false) {
            $db     = $this->getDbConnection();
            $query  = "SELECT p.title_seo,p.itemid,p.title,p.link,p.thumb,c.title as title_parent FROM " . $this->_tablename . " as p INNER JOIN ws_menuitem as c on p.cateid =c.itemid  where p.cateid in (" . $listCateid['cateid'] . ") and p.newsid =" . $listCateid['delta'] . "   ORDER BY p.myorder asc, p.posteddate desc ";
            $result = $db->fetchAll($query);
            if (!is_null($result) && is_array($result)) {
                $cache->setCache($key, $result, self::EXPIRED);
            }
        }
        return $result;
    }

    public function countList($listCateid)
    {
        $cache = $this->getCacheInstance();
        if ($listCateid != null) {
            $and = " and cateid in(" . $listCateid['cateid'] . ") and newsid=" . $listCateid['delta'];
        }

        $key    = $this->getKeyCountList($listCateid['cateid'], $listCateid['delta']);
        $result = $cache->getCache($key);
        if ($result === false) {
            $db     = $this->getDbConnection();
            $query  = "SELECT  count(cateid) as count  FROM " . $this->_tablename . " as p INNER JOIN ws_menuitem as c on p.cateid =c.itemid where 1=1 $and and enabled=1 ORDER BY p.myorder asc, p.posteddate desc ";
            $result = $db->fetchRow($query);
            if (!is_null($result) && is_array($result)) {
                $cache->setCache($key, $result, self::EXPIRED);
            }
        }
        return $result["count"];
    }

    public function getAllListPaginator($listCateid, $limit, $to)
    {
        $cache = $this->getCacheInstance();
        $limit = " limit $limit OFFSET $to";
        if ($listCateid != null) {
            $and = " and cateid in(" . $listCateid['cateid'] . ") and newsid=" . $listCateid['delta'];
        }

        $key    = $this->getKeyAllListPaginator($listCateid['cateid'], $listCateid['delta'], $limit, $to);
        $result = $cache->getCache($key);
        if ($result === false) {
            $db     = $this->getDbConnection();
            $query  = "SELECT p.title_seo,p.itemid,p.shortcontent,p.cateid,p.title,p.link,p.thumb,p.newimage_path,p.fullcontent,c.title as title_parent ,p.social_img,p.hits FROM " . $this->_tablename . " as p INNER JOIN ws_menuitem as c on p.cateid =c.itemid where 1=1 $and and enabled=1 ORDER BY p.posteddate desc $limit ";
            $result = $db->fetchAll($query);
            if (!is_null($result) && is_array($result)) {
                $cache->setCache($key, $result, self::EXPIRED);
            }
        }
        return $result;
    }

    public function getAllListPaginatorFAQSEO($listCateid, $limit, $to, $search = '', $tag = '')
    {
        $cache = $this->getCacheInstance();
        $limit = " limit $limit OFFSET $to";
        if ($listCateid != null) {
            $and = " and p.cateid in(" . $listCateid['cateid'] . ") and p.newsid=" . $listCateid['delta'];
        }

        if ($search != '') {
            $andsearch = "and p.title like  '%$search%'";
        }

        if ($tag != '') {
            $andtag = "and p.shortcontent like  '%$tag%'";
        }

        $key    = "serch-" . $listCateid['cateid'] . "-" . $listCateid['delta'] . "-" . $limit . "-" . $to . "-" . $search . "-" . $tag;
        $result = $cache->getCache($key);
        if ($result === false) {
            $db     = $this->getDbConnection();
            $query  = "SELECT p.title_seo,p.itemid,p.cateid,p.title,p.link,p.thumb,p.newimage_path,p.fullcontent FROM " . $this->_tablename . " as p  where 1=1 $and and p.enabled=1  $andsearch $andtag ORDER BY p.myorder asc, p.posteddate desc $limit ";
            $result = $db->fetchAll($query);
            if (!is_null($result) && is_array($result)) {
                $cache->setCache($key, $result, self::EXPIRED);
            }
        }
        return $result;
    }

    public function getAllListtagFAQSEO($listCateid)
    {
        $cache = $this->getCacheInstance();
        if ($listCateid != null) {
            $and = " and cateid in(" . $listCateid['cateid'] . ") and newsid=" . $listCateid['delta'];
        }

        $key    = "tag-" . $listCateid['cateid'] . "-" . $listCateid['delta'] . "-" . $limit . "-" . $to . "-" . $search;
        $result = $cache->getCache($key);
        if ($result === false) {
            $db     = $this->getDbConnection();
            $query  = "SELECT distinct shortcontent FROM " . $this->_tablename . " where 1=1 $and and enabled=1 and itemid=16066  ";
            $result = $db->fetchRow($query);
            if (!is_null($result) && is_array($result)) {
                $cache->setCache($key, $result, self::EXPIRED);
            }
        }
        return $result;
    }


    public function getAllListLast1($limit = null,$to = 0, $enable = 1,$hit=0,$cateid='',$key='')
    {
        $cache = $this->getCacheInstance();
        if ($limit != null) {
             $limit = " limit $limit OFFSET $to";
        }

        if ($hit==1) {
            $orderby = " ORDER BY p.posteddate desc, p.hits desc  ";
        } else {
            $orderby = " ORDER BY  p.posteddate desc ";
        }

       if ($cateid != null) {
            $andCateid = " and p.cateid in ($cateid) ";
        }
        if($key!='')
            $andKey= " and p.title LIKE \"%$key%\" ";

        $key    = $this->getKeyAllListLast($limit, $enable,$hit,md5($cateid.$key));
        $result = $cache->getCache($key);
        //$result=FALSE;
        if ($result === false) {
            $db     = $this->getDbConnection();
            $query  = "SELECT p.title_seo,p.hits,p.posteddate,p.social_img,p.shortcontent,p.itemid,p.cateid,p.title,p.link,p.thumb,p.newimage_path,p.fullcontent,c.title as title_parent FROM " . $this->_tablename . " as p INNER JOIN ws_menuitem as c on p.cateid =c.itemid where 1=1 $and and enabled=$enable  $andCateid $andKey  $orderby  $limit ";
            $result = $db->fetchAll($query);
            if (!is_null($result) && is_array($result)) {
                $cache->setCache($key, $result, self::EXPIRED);
            }
        }
        return $result;
    }


    public function getAllListLast($limit = null,$to = 0, $enable = 1,$hit=0,$cateid='',$key='')
    {
        $cache = $this->getCacheInstance();
        if ($limit != null) {
             $limit = " limit $limit OFFSET $to";
        }

        if ($hit==1) {
            $orderby = " ORDER BY p.posteddate desc, p.hits desc  ";
        } else {
            $orderby = " ORDER BY  p.posteddate desc ";
        }

       if ($cateid != null) {
            $andCateid = " and p.cateid in ($cateid) ";
        }
        if($key!='')
            $andKey= " and p.title LIKE \"%$key%\" ";

        $key    = $this->getKeyAllListLast($limit, $enable,$hit,md5($cateid.$key));
        $result = $cache->getCache($key);
        //$result=FALSE;
        if ($result === false) {
            $db     = $this->getDbConnection();
            $query  = "SELECT p.title_seo,p.hits,p.posteddate,p.social_img,p.shortcontent,p.itemid,p.cateid,p.title,p.link,p.thumb,p.newimage_path,p.fullcontent,c.title as title_parent FROM " . $this->_tablename . " as p INNER JOIN ws_menuitem as c on p.cateid =c.itemid where 1=1 $and and enabled=$enable  $andCateid $andKey  $orderby  $limit ";
            $result = $db->fetchAll($query);
            if (!is_null($result) && is_array($result)) {
                $cache->setCache($key, $result, self::EXPIRED);
            }
        }
        return $result;
    }




    public function getListApiLast($limit = null,$to = 0, $enable = 1,$hit=0,$cateid='')
    {
        $cache = $this->getCacheInstance();
        if ($limit != null) {
             $limit = " limit $limit OFFSET $to";
        }

        if ($hit==1) {
            $orderby = " ORDER BY p.posteddate desc, p.hits desc  ";
        } else {
            $orderby = " ORDER BY  p.posteddate desc ";
        }

       if ($cateid != null) {
            $andCateid = " and p.cateid in ($cateid) ";
        }


        $key    = $this->getKeyAllListLast($limit, $enable,$hit,md5($cateid));
        $result = $cache->getCache($key);
        //$result=FALSE;
        if ($result === false) {
            $db     = $this->getDbConnection();
            $query  = "SELECT p.title_seo,p.hits,p.posteddate,p.social_img,p.shortcontent,p.itemid,p.cateid,p.title,p.link,p.thumb,p.newimage_path,p.fullcontent,c.title as title_parent FROM " . $this->_tablename . " as p INNER JOIN ws_menuitem as c on p.cateid =c.itemid where 1=1 $and and enabled=$enable  $andCateid  $orderby  $limit ";
            $result = $db->fetchAll($query);
            if (!is_null($result) && is_array($result)) {
                $cache->setCache($key, $result, self::EXPIRED);
            }
        }
        return $result;
    }



    public function getAllListAPI($limit = null,$to = 0, $enable = 1,$keyword='' )
    {
        $cache = $this->getCacheInstance();
        if ($limit != null) {
          $limit = "  limit $limit OFFSET $to";
        }
        if($keyword !='')
        {
            $andKey = "  and title like '%$keyword%'";
        }
            $db     = $this->getDbConnection();
            $query  = "SELECT fullcontent FROM " . $this->_tablename . "  where enabled=$enable  $andKey  order by itemid desc $limit  ";
            $result = $db->fetchAll($query);
            return $result;
    }



    public function getAllList($listCateid, $limit = null, $enable = 1, $orderby = null)
    {
        $cache = $this->getCacheInstance();
        if ($limit != null) {
            $limit = " limit $limit";
        }

        if ($orderby != null) {
            $orderby = "  ORDER BY $orderby ";
        } else {
            $orderby = " ORDER BY p.myorder asc, p.posteddate desc ";
        }

        if ($listCateid != null) {
            $and = " and cateid in(" . $listCateid['cateid'] . ") and newsid=" . $listCateid['delta'];
        }

        $key    = $this->getKeyAllList($listCateid['cateid'], $listCateid['delta'], $limit, $enable);
        $result = $cache->getCache($key);
        //$result=FALSE;
        if ($result === false) {
            $db     = $this->getDbConnection();
            $query  = "SELECT p.title_seo,p.hits,p.posteddate,p.social_img,p.shortcontent,p.itemid,p.cateid,p.title,p.link,p.thumb,p.newimage_path,p.fullcontent,c.title as title_parent FROM " . $this->_tablename . " as p INNER JOIN ws_menuitem as c on p.cateid =c.itemid where 1=1 $and and enabled=$enable $orderby  $limit ";
            $result = $db->fetchAll($query);
            if (!is_null($result) && is_array($result)) {
                $cache->setCache($key, $result, self::EXPIRED);
            }
        }
        return $result;
    }

    public function getListbyTitleDetail($title, $limit = 0)
    {
        $title = strtolower($title);
        if ($limit > 0) {
            $andLimit = " LIMIT $limit ";
        } else {
            $andLimit = '';
        }

        if ($title == null) {
            return null;
        }

        $cache  = $this->getCacheInstance();
        $key    = $this->getKeyDetailByTitle($title, $limit);
        $result = $cache->getCache($key);
        //  $result=false;
        if ($result === false) {
            $db     = $this->getDbConnection();
            $query  = "SELECT *  FROM " . $this->_tablename . " WHERE title LIKE \"%$title%\" ORDER BY itemid DESC $andLimit ";
            $result = $db->fetchAll($query);
            if ($result != null && is_array($result)) {

            } else {
                $result = $this->helpSearchNew($title,$limit, '');
            }
        }
        $cache->setCache($key, $result, self::EXPIRED);
        return $result;
    }

    public function getListbyTitleDetailDequy($title, $limit = 0)
    {
        $title = strtolower($title);
        if ($limit > 0) {
            $andLimit = " LIMIT $limit ";
        } else {
            $andLimit = '';
        }

        if ($title == null) {
            return null;
        }

        $cache  = $this->getCacheInstance();
        $key    = $this->getKeyDetailByTitle($title, $limit);
        $result = $cache->getCache($key);
        //  $result=false;
        if ($result === false) {
            $db     = $this->getDbConnection();
            $query  = "SELECT *  FROM " . $this->_tablename . " WHERE title LIKE \"%$title%\" ORDER BY itemid DESC $andLimit ";
            $result = $db->fetchAll($query);
          }
        $cache->setCache($key, $result, self::EXPIRED);
        return $result;
    }



    public function helpSearchNew($search, $limit, $delta = null)
    {
        $list1 = function ($key, $delta = null, $limit) {
            $news        = Business_WsNew_NewsItem::getInstance();
            return $list = $news->getListbyTitleDetailDequy($key, $limit);
        };
     
        $list = $this->fixSearch($search, $list1, 0, $limit);

        return $list;

    }

    
    public function fixSearch($key, $list, $i,  $limit)
    {
       
        $list1 = $list($key, $limit);
         if (count($list1) == 0) {
            $i     = $i + 1;
            $right = substr($key, 0, -$i);
            $list1 = $list($right,  $limit);
            if (count($list1) == 0) {
                $list1 = $this->fixSearch($key, $list, $i,  $limit);
            }

        }
        return $list1;
    }

    public function getCountListbyTitle($title, $limit = '')
    {
        if ($limit != '') {
            $limit = "LIMIT $limit";
        }

        if (strrpos($title, 'tra gop') === false) {
            $title = strtolower(Business_Common_Utils::removeTiengViet($title));
            $title = str_replace(array("gb", "GB"), "", $title);
            if (strpos($title, 'sony') !== false) {
                $length = 3;
            } else {
                $length = 2;
            }

            if (strpos($title, 'apple') !== false) {
                $title = str_replace("apple", "", $title);
            }

            $title = Business_Helpers_Common::shortText($title, $length);
        }

        if ($title == null) {
            return null;
        }

        $cache  = $this->getCacheInstance();
        $key    = "search-count-new-" . md5($title);
        $result = $cache->getCache($key);
        if ($result === false) {
            $db = $this->getDbConnection();
            if (strrpos($title, 'tra gop') !== false) {
                $query = "SELECT count(*) as count FROM " . $this->_tablename . " WHERE itemid=6206";
            } else {
                $query = "SELECT count(*) as count  FROM " . $this->_tablename . " WHERE title LIKE \"%$title%\" ORDER BY itemid DESC $limit";
            }

            $result = $db->fetchRow($query);
            if ($result != null && is_array($result)) {
                $cache->setCache($key, $result, self::EXPIRED);
            }
        }
        return $result['count'];
    }


 public function CountListbyTitle($title)
    {


        if (strrpos($title, 'tra gop') === false) {
            $title = strtolower(Business_Common_Utils::removeTiengViet($title));
            $title = str_replace(array("gb", "GB"), "", $title);
            if (strpos($title, 'sony') !== false) {
                $length = 3;
            } else {
                $length = 2;
            }

            if (strpos($title, 'apple') !== false) {
                $title = str_replace("apple", "", $title);
            }

            $title = Business_Helpers_Common::shortText($title, $length);
        }

        if ($title == null) {
            return null;
        }

        $cache  = $this->getCacheInstance();
        $key    = 'count-chuyen-tin-tuc-'.md5($title);
        $result = $cache->getCache($key);
        if ($result === false) {
            $db = $this->getDbConnection();
            $query = "SELECT count(*) as count  FROM " . $this->_tablename . " WHERE title LIKE \"%$title%\" ORDER BY itemid DESC ";           

            $result = $db->fetchRow($query);
            if ($result != null && is_array($result)) {
                $cache->setCache($key, $result, self::EXPIRED);
            }
        }

        return $result;
    }


        public function getListbyTitleAjax($title, $limit = '',$itemid=0)
    {
        if ($limit != '') {
            if (is_array($limit)) {
                $limit = " LIMIT " . $limit[0] . " OFFSET " . $limit[1];
            } else {
                $limit = " LIMIT 0, $limit ";
            }

        }

        if($itemid>0)
            $andItemid= " and itemid < $itemid ";


        if (strrpos($title, 'tra gop') === false) {
            $title = strtolower(Business_Common_Utils::removeTiengViet($title));
            $title = str_replace(array("gb", "GB"), "", $title);
            if (strpos($title, 'sony') !== false) {
                $length = 3;
            } else {
                $length = 2;
            }

            if (strpos($title, 'apple') !== false) {
                $title = str_replace("apple", "", $title);
            }

            $title = Business_Helpers_Common::shortText($title, $length);
        }

        if ($title == null) {
            return null;
        }

        $cache  = $this->getCacheInstance();
        $key    = "tin-tuc-lien-quan".md5($title.$limit.$itemid);
        $result = $cache->getCache($key);
        if ($result === false) {
            $db = $this->getDbConnection();
            if (strrpos($title, 'tra gop') !== false) {
                $query = "SELECT *  FROM " . $this->_tablename . " WHERE itemid=6206";
            } else {
                $query = "SELECT *  FROM " . $this->_tablename . " WHERE title LIKE \"%$title%\"   $andItemid and cateid !=630 ORDER BY itemid DESC $limit";
            }

            $result = $db->fetchAll($query);
            if ($result != null && is_array($result)) {
                $cache->setCache($key, $result, self::EXPIRED);
            }
        }

        return $result;
    }



    public function getListbyTitleCount($title, $limit = '')
    { 
        if ($limit != '') {
            if (is_array($limit)) {
                $limit = " LIMIT " . $limit[0] . " OFFSET " . $limit[1];
            } else {
                $limit = " LIMIT 0, $limit ";
            }

        }

        if ($title == null) {
            return null;
        }

        $cache  = $this->getCacheInstance();
        $key    = $this->getKeyDetailByTitle($title.'count', $limit);
        $result = $cache->getCache($key);
        if ($result === false) {
            $db = $this->getDbConnection();
            if (strrpos($title, 'tra gop') !== false) {
                $query = "SELECT count(*) as count  FROM " . $this->_tablename . " WHERE itemid=6206";
            } else {
                $query = "SELECT count(*) as count   FROM " . $this->_tablename . " WHERE title LIKE \"%$title%\" ORDER BY itemid DESC $limit";
            }

            $result = $db->fetchAll($query);
            if ($result != null && is_array($result)) {
                $cache->setCache($key, $result, self::EXPIRED);
            }
        }

        return $result;
    }


    public function getListbyTitle($title, $limit = '')
    {
        if ($limit != '') {
            if (is_array($limit)) {
                $limit = " LIMIT " . $limit[0] . " OFFSET " . $limit[1];
            } else {
                $limit = " LIMIT 0, $limit ";
            }

        }

        if (strrpos($title, 'tra gop') === false) {
            $title = strtolower(Business_Common_Utils::removeTiengViet($title));
            $title = str_replace(array("gb", "GB"), "", $title);
            if (strpos($title, 'sony') !== false) {
                $length = 3;
            } else {
                $length = 2;
            }

            if (strpos($title, 'apple') !== false) {
                $title = str_replace("apple", "", $title);
            }

            $title = Business_Helpers_Common::shortText($title, $length);
        }

        if ($title == null) {
            return null;
        }

        $cache  = $this->getCacheInstance();
        $key    = $this->getKeyDetailByTitle($title, $limit);
        $result = $cache->getCache($key);
        if ($result === false) {
            $db = $this->getDbConnection();
            if (strrpos($title, 'tra gop') !== false) {
                $query = "SELECT *  FROM " . $this->_tablename . " WHERE itemid=6206";
            } else {
                $query = "SELECT *  FROM " . $this->_tablename . " WHERE title LIKE \"%$title%\" ORDER BY itemid DESC $limit";
            }

            $result = $db->fetchAll($query);
            if ($result != null && is_array($result)) {
                $cache->setCache($key, $result, self::EXPIRED);
            }
        }

        return $result;
    }
    public function getDetailByListId($ListId)
    {
        $cache  = $this->getCacheInstance();
        $key    = $this->getKeyDetailByListId(md5($ListId));
        $result = $cache->getCache($key);
        if ($result === false) {
            $db     = $this->getDbConnection();
            $query  = "SELECT * FROM " . $this->_tablename . " WHERE itemid in ($ListId)";
            $result = $db->fetchAll($query);
            if ($result != null && is_array($result)) {
                $cache->setCache($key, $result, self::EXPIRED);
            }
        }
        return $result;
    }
    public function getDetailByCateid($id, $cateid)
    {
        if ( $cateid == '' or (int) $id == 0) {
            return null;
        }

        $cache  = $this->getCacheInstance();
        $key    = "list-cateid-" . $cateid . '-' . $id;
        $result = $cache->getCache($key);
        if ($result === false) {
            $db     = $this->getDbConnection();
            $query  = "SELECT * FROM " . $this->_tablename . " WHERE   cateid in (" . $cateid . ") and itemid<$id and enabled=1 order by updatedate desc  limit 6 ";
            $result = $db->fetchAll($query);
            if ($result != null && is_array($result)) {
                $cache->setCache($key, $result, self::EXPIRED);
            }
        }
        return $result;
    }
    public function getDetail($id)
    {
        if ((int) $id == 0) {
            return null;
        }

        $cache  = $this->getCacheInstance();
        $key    = $this->getKeyDetail($id);
        $result = $cache->getCache($key);
        if ($result === false) {
            $db     = $this->getDbConnection();
            $query  = "SELECT * FROM " . $this->_tablename . " WHERE itemid='" . parent::adaptSQL($id) . "'";
            $result = $db->fetchRow($query);
            $cache->setCache($key, $result, self::EXPIRED);
            
        }

        return $result;
    }
    public function update($id, $newsid, $cateid, $data)
    {
        $data    = Business_HelpersNew_Common::fixItempropTags($data);
        $where   = array();
        $where[] = "itemid='" . parent::adaptSQL($id) . "'";
        $db      = $this->getDbConnection();
        $result  = $db->update($this->_tablename, $data, $where);
        if ($result > 0) {
            $cache = $this->getCacheInstance();
            $cache->flushAll();
            $this->_deleteAllCache($newsid, $cateid, $id);
        }
        return $result;
    }

    private function _deleteAllCache($newsid, $cateid, $id = null, $title = '')
    {
        $cache = $this->getCacheInstance();
        if ($id != null) {
            $key = $this->getKeyDetail($id);
            $cache->deleteCache($key);
        }

    }

    public function insert($data)
    {

        $db     = $this->getDbConnection();
        $result = $db->insert($this->_tablename, $data);
        return $db->lastInsertId();
    }



    

}

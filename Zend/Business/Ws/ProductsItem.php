<?php

class Business_Ws_ProductsItem extends Business_Abstract {

    private $_tablename = 'ws_productsitem';

    const EXPIRED = 3000; //secs
    const KEY_LIST = 'productsitem.list.%s.%s'; //key of list by productsid,cateid
    const KEY_LIST_HOT = 'productsitem.listhot.%s.%s'; //key of list hot by productsid,cateid
    const KEY_LIST_ALL = 'productsitem.list.%s'; //key of list all by productsid,cateid
    const KEY_LIST_FILTER = 'productsitem.list.filter.%s.%s.%s.%s.%s';
    const KEY_LIST_ALL_HOT = 'productsitem.list.host.%s'; //key of list all by productsid
    const KEY_LIST_TOP = 'productsitem.list.top.%s.%s'; //key of list all by productsid
    const KEY_LIST_HIGHLIGHT = 'productsitem.list.highlight.%s.%s'; //key of list all by productsid
    const KEY_LIST_NEWEST = 'productsitem.list.newest.%s.%s'; //key of list all by productsid
    const KEY_LIST_ALL_PAGING = 'productsitem.list.all.%s.%s.%s.%s'; //key of list all by productsid
    const KEY_LIST_ALL_PAGING_MULTI = 'productsitem.list.all.multi.%s'; //key of list all by productsid
    const KEY_COUNT = 'productsitem.count.%s.%s'; //key of count by productsid,cateid
    const KEY_COUNT_FILTER = 'productsitem.count.filter.%s.%s.%s'; //key of count by productsid,cateid
    const KEY_DETAIL = 'productsitem.detail.%s'; //key of detail.id
    //for paging
    CONST PAGING_NUM_RECORDS = 100;
    CONST PAGING_MAX_PAGE = 10;

    private $_paging = null;

    const KEY_LIST_PAGING = 'productsitem.list.paging.%s.%s'; //key of list by productsid, cateid with paging

    private static $_instance = null;

    function __construct() {
        
    }

    //public static function
    /**
     * get instance of Business_Ws_ProductsItem
     *
     * @return Business_Ws_ProductsItem
     */
    public static function getInstance() {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    private function getKeyList($productsid, $cateid) {
        return sprintf(self::KEY_LIST, $productsid, $cateid);
    }

    private function getKeyListHot($productsid, $cateid) {
        return sprintf(self::KEY_LIST_HOT, $productsid, $cateid);
    }

    private function getKeyCount($productsid, $cateid) {
        return sprintf(self::KEY_COUNT, $productsid, $cateid);
    }

    private function getKeyListAll($productsid) {
        return sprintf(self::KEY_LIST_ALL, $productsid);
    }

    private function getKeyListHotAll($productsid, $cateid) {
        return sprintf(self::KEY_LIST_ALL_HOT, $productsid, $cateid);
    }

    private function getKeyDetail($id) {
        return sprintf(self::KEY_DETAIL, $id);
    }

    private function getKeyListPaging($productsid, $cateid) {
        return sprintf(self::KEY_LIST_PAGING, $productsid, $cateid);
    }

    private function getKeyListFilter($productsid, $cateid, $offset, $records, $filter) {
        return sprintf(self::KEY_LIST_FILTER, $productsid, $cateid, $offset, $records, $filter);
    }

    private function getKeyCountListFilter($productsid, $cateid, $filter) {
        return sprintf(self::KEY_COUNT_FILTER, $productsid, $cateid, $filter);
    }

    private function getKeyListTop($productsid, $cateid) {
        return sprintf(self::KEY_LIST_TOP, $productsid, $cateid);
    }

    private function getKeyListNewest($productsid, $cateid) {
        return sprintf(self::KEY_LIST_NEWEST, $productsid, $cateid);
    }

    private function getKeyListHighlight($productsid, $cateid) {
        return sprintf(self::KEY_LIST_HIGHLIGHT, $productsid, $cateid);
    }

    private function getKeyListAllMulti($keys) {
        return sprintf(self::KEY_LIST_ALL_PAGING_MULTI, $keys);
    }

    private function getKeyListAllPaging($productsid, $cateid, $offset, $records) {
        return sprintf(self::KEY_LIST_ALL_PAGING, $productsid, $cateid, $offset, $records);
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
        $cache = GlobalCache::getCacheInstance('pro');
        return $cache;
    }
	
	public function getStockByID($id="") {
		$db = $this->getDbConnection();
		if ($id!="") {
			$query = "SELECT itemid,ma_kho FROM `ws_productsitem` WHERE enabled=1 AND onstock IN ($id) AND ma_kho>0";
		} else {
			$query = "SELECT itemid,ma_kho FROM `ws_productsitem` WHERE enabled=1 AND onstock=1 AND ma_kho>0";
		}
        $result = $db->fetchAll($query);
        return $result;
	}
	
    public function getProductsNameWithID() {
        $cache = $this->getCacheInstance();
        $key = "products.name.id";
        $result = $cache->getCache($key);
//$result=false;
        if ($result === FALSE) {
            $db = $this->getDbConnection();
            $query = "SELECT title, itemid FROM ws_productsitem WHERE title != '' AND productsid IN (3,4,5) ORDER BY title ASC";
            $result = $db->fetchAll($query);

            if (!is_null($result) && is_array($result) && count($result) > 0) {
                foreach ($result as $item) {
                    $ret[] = "\"" . $item["title"] . " --" . $item["itemid"] ."\"";
                }
                $ret = implode(",", $ret);
                $result = $ret;
                $cache->setCache($key, $result);
            }
        }
        return $result;
    }
    public function get_list($productsid=0){
            $cache = $this->getCacheInstance();
            $key = "get_lists".  $this->_tablename.$productsid;
            $result = $cache->getCache($key);
            if($result === FALSE){
                $db= $this->getDbConnection();
                $query = "select title, itemid,price,original_price,productsid,productscode,cateid,onstock from $this->_tablename where title != '' and (price > 0 or original_price > 0 ) and onstock = 1";
                if((int)$productsid >0){
                    $query .=" and productsid IN ($productsid)";
                }
                
                $query ." order by itemid ASC ";
                $result = $db->fetchAll($query);
                if(!is_null($result) && is_array($result))
                {
                    $cache->setCache($key, $result);
                }
            }
            return $result;
        }
    public function get_list2($productsid=0,$flag){
            $cache = $this->getCacheInstance();
            $key = "get_list2".  $this->_tablename.$productsid.$flag;
            $result = $cache->getCache($key);
            if($result === FALSE){
                $db= $this->getDbConnection();
                $query = "select title, itemid,price,original_price,productsid,productscode,cateid from $this->_tablename where title != '' and (price > 0 or original_price > 0 ) and onstock = 1";
                if((int)$productsid >0){
                    $query .=" and productsid IN ($productsid)";
                }
                if((int)$flag ==0){
                    $query .=" and original_price >0";
                }else{
                    $query .=" and price >0";
                }
                
                $query ." order by itemid ASC ";
                $result = $db->fetchAll($query);
                if(!is_null($result) && is_array($result))
                {
                    $cache->setCache($key, $result);
                }
            }
            return $result;
        }
    public function get_list_by_cateid($cateid=""){
            $cache = $this->getCacheInstance();
            $key = "s2get_list_by_cateids".  $this->_tablename.$cateid;
            $result = $cache->getCache($key);
//            $result = FALSE;
            if($result === FALSE){
                $db= $this->getDbConnection();
                $query = "select bonus_mobile,title, itemid,price,original_price,productsid,productscode,cateid from $this->_tablename where title != '' and onstock = 1 and enabled=1 ";
                if($cateid !=NULL){ 
                    $query .=" and cateid = $cateid";
                }    
                
                
                $query ." order by itemid ASC ";
                $result = $db->fetchAll($query);
                if(!is_null($result) && is_array($result))
                {
                    $cache->setCache($key, $result,3600);
                }
            }
            return $result;
        }
    
    public function get_list_phukien_and_cty($productsid=0,$cateid=0, $stock=1){
        $cache = $this->getCacheInstance();
        $key = "s2get_list_phukien_and_ctys".  $this->_tablename.$productsid.$cateid;
        $result = $cache->getCache($key);
            //$result = FALSE;
        if($result === FALSE){
            $db= $this->getDbConnection();
            //$query = "select bonus_mobile,title, itemid,price,original_price,productsid,productscode,cateid from $this->_tablename where title != '' and onstock IN ($stock) and enabled=1 ";
            $query = "select * from $this->_tablename where onstock IN ($stock) and enabled=1 ";
            $query .=" and productsid = $productsid ";
            
            if((int)$productsid !=4 && $productsid !=9){ 
                //$query .=" and original_price >0";
            }  
            if((int)$cateid >0){ 
                $query .=" and cateid IN ($cateid)";
            }   
            //$query ." order by itemid ASC ";
            $result = $db->fetchAll($query);
            if(!is_null($result) && is_array($result))
            {
                $cache->setCache($key, $result,3600);
            }
        }
        return $result;
    }

    public function get_list_phukien_and_ctyv2($productsid=0,$cateid=0, $stock=1,$type=0){
        $cache = $this->getCacheInstance();
        $key = "s2get_list_phukien_and_ctys".  $this->_tablename.$productsid.$cateid.$type;
        $result = $cache->getCache($key);
        //$result = FALSE;
        if($result === FALSE){
            $db= $this->getDbConnection();
            if($type == 1) {
                $join = ' INNER JOIN addon_product_color as pc ON pc.itemid = pi.itemid';
                $whereType = ' and pc.itemid_vt != 0';
            }
            elseif($type == 2) {
                $join = ' INNER JOIN addon_product_color as pc ON pc.itemid = pi.itemid';
                $whereType = ' and pc.itemid_vt = 0';
            }
            else {
                $join = '';
                $whereType = '';
            }
            //$query = "select bonus_mobile,title, itemid,price,original_price,productsid,productscode,cateid from $this->_tablename where title != '' and onstock IN ($stock) and enabled=1 ";
            $query = "select pi.* from $this->_tablename as pi $join where pi.onstock IN ($stock) and pi.enabled=1 ";
            $query .=" and pi.productsid = $productsid ".$whereType;

            if((int)$productsid !=4 && $productsid !=9){
                //$query .=" and original_price >0";
            }
            if((int)$cateid >0){
                $query .=" and pi.cateid IN ($cateid)";
            }
            //$query ." order by itemid ASC ";
            $result = $db->fetchAll($query);
            if(!is_null($result) && is_array($result))
            {
                $cache->setCache($key, $result,3600);
            }
        }
        return $result;
    }
    public function get_list3($productsid=0,$flag){
            $cache = $this->getCacheInstance();
            $key = "get_list3".  $this->_tablename.$productsid.$flag;
            $result = $cache->getCache($key);
            if($result === FALSE){
                $db= $this->getDbConnection();
                $query = "select title, itemid,price,original_price,productsid,productscode,cateid from $this->_tablename where title != '' and (price > 0 or original_price > 0 ) and onstock = 1 and enabled=1";
                if((int)$productsid >0){
                    $query .=" and productsid IN ($productsid)";
                }
                if((int)$flag ==1){
                    $query .=" and original_price >0";
                }
                if((int)$flag ==2){
                    $query .=" and original_price >0";
                }
                
                $query ." order by itemid ASC ";
                $result = $db->fetchAll($query);
                if(!is_null($result) && is_array($result))
                {
                    $cache->setCache($key, $result);
                }
            }
            return $result;
        }
    public function getProducts($productsid="",$itemid=0) {
        $cache = $this->getCacheInstance();
        $key = "getProducts" . $this->_tablename.$productsid.$itemid;
        $result = $cache->getCache($key);
//        $result = false;
        if ($result === FALSE) {
            $db = $this->getDbConnection();
            $query = "SELECT title, itemid,price,original_price,productsid,productscode,cateid FROM ws_productsitem WHERE title != '' and (price > 0 or original_price > 0 ) and onstock = 1 ";
            if((int)$itemid >0){
               $query .=" and itemid IN ($itemid)"; 
            }
            $query .=" ORDER BY title ASC"; 
            $result = $db->fetchAll($query);
            if (!is_null($result) && is_array($result) && count($result) > 0) {
                foreach ($result as $item) {
//                    $item = trim(str_replace("", "%", $item));
                    $subfix = "";
                    if ($item["productsid"] == 4) {
                        $subfix = "--(" . $item["productscode"] . ")";
                    } else {
                        $subfix = "--(cty)";
                        if ($item["price"] > 0) {
                            $subfix = "--(hnam)";
                        }
                    }
                    $itemid = "";
                    $itemid = "--" . $item["itemid"];
                    $productsid = "--" . $item["productsid"];
                    $cate_id = "--" . $item["cateid"];

                    $ret[] = trim($item["title"] . $subfix . $itemid . $productsid . $cate_id);
                }
                $result = $ret;
                $cache->setCache($key, $result, 60 * 5);
            }
        }
        return $result;
    }
    public function getProducts2($productsid="",$posteddate="",$onstock="",$price="") {
        $cache = $this->getCacheInstance();
        $key = "getProducts2" . $this->_tablename.$productsid.$posteddate.$onstock.$price;
        $result = $cache->getCache($key);
//        $result = false;
        if ($result === FALSE) {
            $db = $this->getDbConnection();
            $query = "SELECT title, itemid,price,original_price,productsid,productscode,cateid FROM ws_productsitem WHERE title != '' AND productsid < 14  ";
            if($productsid != NULL){
                $query .=" and productsid IN ($productsid)";
            }
            if($onstock != NULL){
                $query .=" and onstock = $onstock";
            }
            if($price != NULL){
                $query .=" and (price > 0 or original_price > 0 )";
            }
            if($posteddate != NULL){
                $query .=" and posteddate >= '$posteddate'";
            }
            $query .=" ORDER BY title ASC";
            $result = $db->fetchAll($query);
            if (!is_null($result) && is_array($result) && count($result) > 0) {
                foreach ($result as $item) {
//                    $item = trim(str_replace("", "%", $item));
                    $subfix = "";
                    if ($item["productsid"] == 4) {
                        $subfix = "--(" . $item["productscode"] . ")";
                    } else {
                        $subfix = "--(cty)";
                        if ($item["price"] > 0) {
                            $subfix = "--(hnam)";
                        }
                    }
                    $itemid = "";
                    $itemid = "--" . $item["itemid"];
                    $productsid = "--" . $item["productsid"];
                    $cate_id = "--" . $item["cateid"];

                    $ret[] = trim($item["title"] . $subfix . $itemid . $productsid . $cate_id);
                }
                $result = $ret;
                $cache->setCache($key, $result, 60 * 5);
            }
        }
        return $result;
    }
    
    public function getListByCateId2($cateid){
        $tableName = "ws_newsitem";
            $cache = $this->getCacheInstance();
		$key = 'getListByCateId2'.$tableName.$cateid;
		$result = $cache->getCache($key);
		if($result === FALSE)
		{
                    $db = $this->getDbConnection();
                    $query = "SELECT * FROM " . $tableName . " WHERE cateid = $cateid";
                    $result = $db->fetchAll($query);
                    if(!is_null($result) && is_array($result))
                    {
                        $cache->setCache($key, $result,60*5);
                            
                    }
                }
                return $result;
        }
        
public function getRandomAccDetail(){
        $db = $this->getDbConnection();
        $query = "SELECT *, rand() as random FROM $this->_tablename where onstock=1 and productsid = 4 order by random asc LIMIT 0, 1";
        $result = $db->fetchAll($query);
        $_result =$result[0];
        return $_result;
    }
    public function getListproductsitem() {
        $cache = $this->getCacheInstance();
        $key = 'getListproductsitem' . $this->_tablename;
        $result = $cache->getCache($key);
//        $cache->deleteCache($key);exit();
        if ($result === false) {
            $db = $this->getDbConnection();
            $query = "select (shortcontent),itemid from $this->_tablename where productsid IN (3,5)";
//            var_dump($query);exit();
            $result = $db->fetchAll($query);
            $cache->setCache($key, $result);
        }
        return $result;
    }

    public function getNameByItemid($itemid) {
        $cache = $this->getCacheInstance();
        $key = 'getNameByItemid' . $id;
        $result = $cache->getCache($key);
        if ($result === false) {
            $db = $this->getDbConnection();
            $query = "select * from $this->_tablename where itemid = $itemid";
            $result = $db->fetchAll($query);
            if (!is_null($result) && is_array($result)) {
                $cache->setCache($key, $result);
            }
        }

        return $result;
    }

    public function get_NameByItemid($itemid) {
        $list = $this->getNameByItemid($itemid);
        foreach ($list as &$items) {
            $title = $items["title"];
        }
        return $title;
    }

    public function getDetailAllTags($id) {
        $db = $this->getDbConnection();
            $query = "SELECT title, itemid,price,original_price,productsid,productscode,cateid FROM ws_productsitem WHERE title != ''  and itemid = $id ORDER BY title ASC";
            $result = $db->fetchAll($query);
            if (!is_null($result) && is_array($result) && count($result) > 0) {
                foreach ($result as $item) {

//                    $item = trim(str_replace("", "%", $item));
                    $subfix = "";
                    if ($item["productsid"] == 4) {
                        $subfix = "--(" . $item["productscode"] . ")";
                    } else {
                        $subfix = "--(cty)";
                        if ($item["price"] > 0) {
                            $subfix = "--(hnam)";
                        }
                    }
                    $itemid = "";
                    $itemid = "--" . $item["itemid"];
                    $productsid = "--" . $item["productsid"];
                    $cate_id = "--" . $item["cateid"];

                    $ret[] = $item["title"] . $subfix . $itemid . $productsid . $cate_id;
                }
//                var_dump($ret);exit();
                $ret = implode(",", $ret);
                $result = $ret;
            }
        return $result;
    }
    public function searchFull($hsx, $gia, $hdh, $mh, $tn) {
        $cached = $this->getCacheInstance();
        $key = $hsx . $gia . $hdh . $mh . $tn;
        $result = $cached->getCache($key);
//        $cached->deleteCache($key);//exit();
//        var_dump($result);exit();
//        and shortcontent like '%$mh%'
//        $fid='';

        $query2 = "SELECT * FROM hn_features_data f, $this->_tablename w WHERE f.pid=w.itemid ";
        $giasanphamduoi10trieu = "and ((price <= $gia and price > 0) or (original_price <= $gia and original_price > 0) ) ";
        $giasanphamtren10trieu = "and ((price > $gia and price > 0) or (original_price > $gia and original_price > 0) ) ";
        $hedieuhanh = "and shortcontent like '%$hdh%' ";
        $tinhnangchongbui = "and shortcontent like '%$tn%' ";
        $tinhnangcamera = "and f.fid='$fid' ";
        $odergia = "ORDER BY original_price DESC ";
        $hangsanxuat = "and cateid = '$hsx' ";
        $tinhnang2sim = "and dualsim > 0 ";
        $limit = "LIMIT 100";
        if ($result === FALSE) {
//            exit('dsadsa');
            $db = $this->getDbConnection();
            $query = "SELECT * FROM $this->_tablename where 1=1 and productsid !='4' ";
            if ($gia > 0 && $gia < 10000001) {
                $query.="$giasanphamduoi10trieu";
            }
            if ($gia > 10000000) {
                $query.="$giasanphamtren10trieu";
            }
            if ($hsx !== "0") {
                $query.="$hangsanxuat";
            }
            if ($hdh !== "0") {
                $query.="$hedieuhanh";
            }
            if ($tn !== "0") {
                if ($tn === '2 Sim') {
                    $query.="$tinhnang2sim";
                } else {
                    if ($tn == "chống bụi") {
                        $query.="$tinhnangchongbui";
                    } else {
                        if ($tn === '1.3') {
                            $fid = '63';
                            $query2 = $query;
                        } else {
                            $fid = '60';
                            $query2 = $query;
                        }

                        $query.=" and f.fid='$fid' ";
                    }
                }
            }
            $query.="$odergia $limit";
//            var_dump($query);exit();
            $result = $db->fetchAll($query);
            $cached->setCache($key, $result);
        }
//        var_dump($result);exit();
        return $result;
    }

    public function resetNewest() {
        $db = $this->getDbConnection();
        $query = "UPDATE " . $this->_tablename . " set newest = 0 where newest=1 AND productsid=3";
        $result = $db->query($query);

        $query = "SELECT itemid FROM " . $this->_tablename . " WHERE productsid=3 AND original_price > 0 ORDER BY posteddate DESC LIMIT 0,15";
        $result = $db->fetchAll($query, $data);

        foreach ($result as $item) {
            $itemids[] = $item["itemid"];
        }
        $query = "UPDATE " . $this->_tablename . " set newest = 1 where productsid=3 AND itemid IN (" . implode(",", $itemids) . ")";
        $result = $db->query($query);
        if ($result)
            return true;
        return false;
    }

    //Nghidv added 3:26pm 15-5-2011
    public function getListByProIdPagingAdmin($productsid = 0, $cateid = 0, $offset = 0, $records = 20, $filter = '', $enable = '', $onstock = '', $key = '', $sort = '', $price_start = 0, $price_end = 0, $_where = array()) {
        $keys = md5($productsid . $cateid);
        if ($cateid == 191)
            $productsid = 5;
//            if ($filter == 'newest')  $filter = "newest desc, ";
//            if ($filter == 'highlight')  $filter = "highlight desc, ";
        $_select = '';
        ($filter != '') ? $_filter[] = " $filter desc " : $filter = '';
        switch ($sort) {
            case 'a-z':
                $_filter[] = ' title asc ';
//                $_filter[] = ' myorder asc ';
                break;
            case 'z-a':
                $_filter[] = ' title desc ';
//                $_filter[] = ' myorder asc ';
                break;
            case 'da-z':
//                $_filter[] = ' myorder asc ';
                $_filter[] = ' posteddate asc ';

                break;
            case 'dz-a':
//                $_filter[] = ' myorder asc ';
                $_filter[] = ' posteddate desc ';
                break;
            case 'pa-z':

//                        $_filter[] = ' price asc ';
                $_select = ', (price * 10000000 + original_price * 10000000) as max';
                $_filter[] = ' max asc ';
                $_filter[] = ' original_price asc ';
//                $_filter[] = ' myorder asc ';
                break;
            case 'pz-a':

//                        $_filter[] = ' price desc ';
                $_select = ', (price * 10000000 + original_price * 10000000) as max';
                $_filter[] = ' max desc ';
                $_filter[] = ' original_price desc ';
//                $_filter[] = ' myorder asc ';
                break;
            default:
                $_filter[] = ' posteddate desc ';
                break;
        }

        ($enable != '') ? $where[] = " enabled = $enable " : $enable = '';
        ($onstock != '') ? $where[] = " onstock = $onstock " : $onstock = '';
        ($key != '') ? $where[] = " (title like '%$key%' OR title like '$key' OR productscode LIKE '%$key%' OR productscode = '$key') " : $key = '';

        if ($key != '') {
            $_select .= ', MATCH(title) AGAINST ("' . $key_match . '") as score';
            $_filter = array();
            $_filter[] = "original_price DESC";
            $_filter[] = "price  DESC";
            $_filter[] = "score ASC";
        }
        //$_filter[] = ' myorder asc ';

        $_filter = implode(" , ", $_filter);


//            if ($price_start != '' && $price_start != null)
//                $where2[] = "(price >= $price_start OR original_price >= $price_start)";
//            if ($price_end != '' && $price_end != null)
//                $where2[] = "(price <= $price_end OR original_price <= $price_end)";
        ((int) $price_start == 0 ? $price_start = 0 : true);
        ((int) $price_end == 0 ? $price_end = 1000000000 : true);
        $where2[] = "( (price >= $price_start AND price <= $price_end) OR (original_price >= $price_start AND original_price <= $price_end) )";

        if ($where2 != null && $where2 != '')
            $where2 = implode(" AND ", $where2);

        if ($onstock != "")
            $where2 .= " AND onstock = " . $onstock . " ";

        $cache = $this->getCacheInstance();
        $keys = $keys . preg_replace("/[ ,]/", "", $cateid . $productid . $offset . $records . $sort . $_filter . $enable . $onstock . $key . $price_end . $price_start . md5(json_encode($_where)));

        $key = $this->getKeyListAllMulti("admin" . $keys);
        $result = $cache->getCache($key);

        $result = FALSE;
        if ($result === FALSE) {
            $db = $this->getDbConnection();

            if ($cateid == 0) {
                if ($productsid != null)
                    $where[] = 'productsid IN (' . $productsid . ')';

                $where = implode(" AND ", $where);

                if ($where2 != '' && $where2 != null)
                    $where .= " AND " . $where2;

                if (count($_where) == 0 || empty($_where)) {
                    $query = "SELECT * $_select FROM " . $this->_tablename . " WHERE  $where ORDER BY $_filter LIMIT ";
                } else {
                    $_where = implode(" AND ", $_where) . " AND ";
                    $query = "SELECT * $_select FROM " . $this->_tablename . " JOIN hn_features_data as fd WHERE  $_where $where ORDER BY $_filter LIMIT ";
                }

                //$data = array($productsid);
                $data = array();
            } else {
                if ($productsid != null)
                    $where[] = 'productsid IN (' . $productsid . ')';
                $where[] = 'cateid IN (' . $cateid . ')';
                $where = implode(" AND ", $where);
                if ($where2 != '' && $where2 != null)
                    $where .= " AND " . $where2;
                if (count($_where) == 0) {
                    $query = "SELECT * $_select FROM " . $this->_tablename . " WHERE $where ORDER BY $_filter LIMIT ";
                } else {
                    $_where = implode(" AND ", $_where) . " AND ";
                    $query = "SELECT DISTINCT(itemid), * $_select FROM " . $this->_tablename . " JOIN hn_features_data as fd  WHERE $_where $where ORDER BY $_filter LIMIT ";
                }
                //$data = array($productsid);
                $data = array();
            }
            if ($offset == 0 && $records == 0) {
                $offset = 0;
                $records = 100;
            }
            $query .= " $offset, $records ";
//echo "<pre>";
//var_dump($query);
//die();
            $result = $db->fetchAll($query, $data);
            $cache->setCache($key, $result);
        }

        return $result;
    }

    public function getCountByProIdPagingAdmin($productsid = 0, $cateid = 0, $filter = '', $enable = '', $onstock = '', $key = '', $price_start = '', $price_end = '', $_where = array()) {

        ($filter != '') ? $filter = " $filter desc, " : $filter = '';
        ($enable != '') ? $where[] = " enabled = $enable " : $enable = '';
        ($onstock != '') ? $where[] = " onstock = $onstock " : $onstock = '';
//        ($key != '') ? $where[] = " title like '%$key%'" : $key = '';
//        ($key != '') ? $where[] = " (title like '%$key%' OR title like '$key') " : $key = '';
        ($key != '') ? $where[] = " (title like '%$key%' OR title like '$key') " : $key = '';
//            if ($price_start != '' && $price_start != null)
//                $where2[] = "(price >= $price_start OR original_price >= $price_start)";
//            if ($price_end != '' && $price_end != null)
//                $where2[] = "(price <= $price_end OR original_price <= $price_end)";

        /*
         * if price not set, default = 0
         * if price_end not set, default max very very large value 100,000,000
         */
        ((int) $price_start == 0 ? $price_start = 0 : true);
        ((int) $price_end == 0 ? $price_end = 1000000000 : true);
        $where2[] = "( (price >= $price_start AND price <= $price_end) OR (original_price >= $price_start AND original_price <= $price_end) )";

        if (count($where2) > 0)
            $where2 = implode(" AND ", $where2);

        $key = 'admin.count.products.cateid.list.count.all.' . $cateid . $productsid . $filter . $enable . $onstock . $key . $price_end . md5(json_encode($_where));

        $cache = $this->getCacheInstance();
        $result = $cache->getCache($key);
        //$result = false;
        if ($result === FALSE) {
            $db = $this->getDbConnection();
            if ($cateid == 0) {
                if ($productsid != null)
                    $where[] = 'productsid IN (' . $productsid . ')';
                $where = implode(" AND ", $where);
                if ($where2 != '' && $where2 != null)
                    $where .= " AND " . $where2;
                if (count($_where) == 0) {
                    $query = "SELECT count(*) as total FROM " . $this->_tablename . " WHERE  $where ORDER BY $filter myorder asc ";
                } else {

                    $_where = implode(' AND ', $_where) . ' AND ';
                    $query = "SELECT count(DISTINCT(itemid)) as total FROM " . $this->_tablename . " JOIN hn_features_data fd WHERE  $_where $where ORDER BY $filter myorder asc ";
                }
                $data = array($productsid);
            } else {
                if ($productsid == null)
                    $where[] = 'productsid IN (?)';
                $where[] = 'cateid IN (' . $cateid . ')';
                $where = implode(" AND ", $where);
                if ($where2 != '' && $where2 != null)
                    $where .= " AND " . $where2;
                if (count($_where) == 0) {
                    $query = "SELECT count(*) as total FROM " . $this->_tablename . " WHERE $where ORDER BY $filter myorder asc ";
                } else {

                    $_where = implode(' AND ', $_where) . ' AND ';

                    $query = "SELECT count(DISTINCT(itemid)) as total FROM " . $this->_tablename . " JOIN hn_features_data fd WHERE $_where $where ORDER BY $filter myorder asc ";
                }
                $data = array($productsid);
            }
//            echo "<pre>";
//            var_dump($query);
//            die();
            $result = $db->fetchAll($query, $data);
            $cache->setCache($key, $result[0]['total'], self::EXPIRED);
            $result = $result[0]['total'];
        }
        return $result;
    }

    public function getHotSale() {
        $cache = $this->getCacheInstance();
        $key = "hotsale.list";
        $result = $cache->getCache($key);

        if ($result === FALSE) {
            $db = $this->getDbConnection();
            $query = "SELECT * FROM " . $this->_tablename . " WHERE enabled = 1 AND onstock = 1 AND hotsale = 1 ORDER BY posteddate DESC";
            $data = array($productsid);
            $result = $db->fetchAll($query, $data);
            if (count($result) > 0)
                $cache->setCache($key, $result);
        }
        return $result;
    }

    public function getHotSaleByPID($pids) {
        $cache = $this->getCacheInstance();
        $key = "hotsale.pid.list." . $pids;
        $result = $cache->getCache($key);
        if ($result === FALSE) {
            $db = $this->getDbConnection();
            $query = "SELECT * FROM " . $this->_tablename . " WHERE enabled = 1 AND onstock = 1 AND itemid IN ($pids) ORDER BY posteddate DESC";
            $data = array($productsid);
            $result = $db->fetchAll($query, $data);
            if (count($result) > 0)
                $cache->setCache($key, $result);
        }
        return $result;
    }

    public function getHotSaleByPID2($pids) {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " WHERE  itemid IN ($pids) ORDER BY posteddate DESC";
        $result = $db->fetchAll($query);
        return $result;
    }
    
    public function getHotSaleByPID3($pids) {
        $db = $this->getDbConnection();
        $query = "SELECT itemid,cateid FROM " . $this->_tablename . " WHERE  itemid IN ($pids) ORDER BY posteddate DESC";
        $result = $db->fetchAll($query);
        return $result;
    }

    public function getHotSaleAcc() {
        $cache = $this->getCacheInstance();
        $key = "hotsale.acc.list";
        $result = $cache->getCache($key);

        if ($result === FALSE) {
            $db = $this->getDbConnection();
            $query = "SELECT * FROM " . $this->_tablename . " WHERE enabled = 1 AND onstock = 1 AND discountid > 0 ORDER BY price DESC";
            $data = array($productsid);
            $result = $db->fetchAll($query, $data);
            if (count($result) > 0)
                $cache->setCache($key, $result);
        }
        return $result;
    }

    public function getHotSaleAccByCate($id) {
        $cache = $this->getCacheInstance();
        $key = "hotsale.acc.list." . $id;
        $result = $cache->getCache($key);

        if ($result === FALSE) {
            $db = $this->getDbConnection();
            $query = "SELECT * FROM " . $this->_tablename . " WHERE enabled = 1 AND onstock = 1 AND discountid = $id ORDER BY price DESC";
            $data = array($productsid);
            $result = $db->fetchAll($query, $data);
            if (count($result) > 0)
                $cache->setCache($key, $result);
        }
        return $result;
    }

    public function getProductByItemID($itemids, $offset = 0, $records = 60) {

        $cache = $this->getCacheInstance();
        $key = "list.itemid.%s.%s.%s";
        $key = sprintf($itemids, $offset, $records);
        $result = $cache->getCache($key);
        if ($result === FALSE) {
            $db = $this->getDbConnection();
            $query = "SELECT * FROM " . $this->_tablename . " WHERE itemid IN ($itemids) LIMIT $offset, $records";
            $data = array();

            $result = $db->fetchAll($query, $data);

            if (count($result) > 0)
                $cache->setCache($key, $result);
        }
        return $result;
    }

    public function getProductByPrice($itemid, $productsid, $cateid, $price, $oprice, $offset = 0, $records = 28, $rprice = 1000000) {

        $cache = $this->getCacheInstance();
        $key = "p.price.list.%s.%s.%s.%s.%s";
        $key = sprintf($key, $itemid, $productsid, $cateid, $price, $oprice . $rprice);
        $result = $cache->getCache($key);

        //$result = false;
        if ($result === FALSE) {
            $db = $this->getDbConnection();
            $upper = $price + $rprice;
            $lower = $price - $rprice;
            $oupper = $oprice + $rprice;
            $olower = $oprice - $rprice;

            $p1 = "( price >= $lower AND price <= $upper)";
            $p2 = "(original_price>= $olower AND original_price <= $oupper)";

            if ($price > 0)
                $_p[] = $p1;
            if ($oprice > 0)
                $_p[] = $p2;


            $_price = implode(" OR ", $_p);
            if ($_price == null || empty($_price))
                $_price = "1";
            $query = "SELECT * FROM " . $this->_tablename . " WHERE onstock = 1 AND productsid = ? AND ($_price) LIMIT $offset, $records";

            $data = array($productsid);

            $result = $db->fetchAll($query, $data);

            if (count($result) > 0)
                $cache->setCache($key, $result);
        }
        return $result;
    }

    public function updateOrdering($itemid, $order, $productsid, $cateid) {
        $db = $this->getDbConnection();
        /* $query = 'update ' . $this->_tablename . ' set myorder = myorder + 1 where myorder >= ? AND productsid = ? AND cateid = ?';
          $data = array($order, $productsid, $cateid);
          $db->query($query, $data); */

        $query = 'update ' . $this->_tablename . ' set myorder = ' . (int) $order . ' WHERE itemid = ?';
        $data = array($itemid);
        $db->query($query, $data);
    }

    public function getListGroupCateid() {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM  $this->_tablename where  enabled = 1 and (price > 0 or original_price > 0 )  GROUP BY cateid";
//            var_dump($query);exit();
        $result = $db->fetchAll($query);
        return $result;
    }
    public function get_list_by_cheap() {
        $db = $this->getDbConnection();
        $query = "SELECT itemid,title FROM $this->_tablename WHERE `cheap` NOT IN (1) and productsid =3 and enabled=1 and onstock = 1";
//            var_dump($query);exit();
        $result = $db->fetchAll($query);
        return $result;
    }
    public function get_list_by_cheap_android() {
        $db = $this->getDbConnection();
        $query = "SELECT itemid,title FROM $this->_tablename WHERE `cheap` NOT IN (1) and productsid =3 and is_apple=0";
//            var_dump($query);exit();
        $result = $db->fetchAll($query);
        return $result;
    }

    public function getListByName($pname) {
        $cache = $this->getCacheInstance();
        $key = "products.list.name";
        $result = $cache->getCache($key);

        if ($result === FALSE) {
            $db = $this->getDbConnection();
            $query = "SELECT itemid FROM ws_productsitem WHERE title LIKE '%" . $pname . "%'";

            $result = $db->fetchCol($query);

            if (!is_null($result) && is_array($result) && count($result) > 0) {
                $cache->setCache($key, $result);
            }
        }
        return $result;
    }

    public function getProductsName() {
        $cache = $this->getCacheInstance();
        $key = "products.name";
        $result = $cache->getCache($key);
//        $result = false;

        if ($result === FALSE) {
            $db = $this->getDbConnection();
            $query = "SELECT title, itemid FROM ws_productsitem WHERE title != '' AND productsid IN (3,5) AND enabled = 1 ORDER BY title ASC";
            $result = $db->fetchAll($query);

            if (!is_null($result) && is_array($result) && count($result) > 0) {
                foreach ($result as $item) {
                    if ($item["itemid"] == 53)
                        continue;

//                    $item = trim(str_replace("", "%", $item));
                    $ret[] = "\"" . $item["title"] . "\"";
                }
                $ret = implode(",", $ret);
                $result = $ret;
                $cache->setCache($key, $result);
            }
        }
        return $result;
    }

    public function getProductsNameByCateid($id) {
//    exit('dsads');
        $cache = $this->getCacheInstance();
        $key = "products.name.by.cateid" . $id;
        $result = $cache->getCache($key);
//        $cache->deleteCache($key);//exit();
//        $result = false;
//        var_dump($key);exit();
        if ($result === FALSE) {
            $db = $this->getDbConnection();
            $query = "SELECT title, itemid,price,original_price,productsid,productscode FROM ws_productsitem WHERE title != '' AND productsid = $id and (price > 0 or original_price > 0 ) and onstock = 1 ORDER BY title ASC";
//            var_dump($query);exit();
            $result = $db->fetchAll($query);

            if (!is_null($result) && is_array($result) && count($result) > 0) {
                foreach ($result as $item) {

//                    $item = trim(str_replace("", "%", $item));
                    $subfix = "";
                    if ($item["productsid"] == 4) {
                        $subfix = "--(" . $item["productscode"] . ")";
                    } else {
                        $subfix = "--(cty)";
                        if ($item["price"] > 0) {
                            $subfix = "--(hnam)";
                        }
                    }

                    $ret[] = "\"" . $item["title"] . $subfix . "\"";
                }
                $ret = implode(",", $ret);
                $result = $ret;
                $cache->setCache($key, $result, 60 * 5);
            }
        }
        return $result;
    }
    
    public function getProductsNameByCateid2() {
//    exit('dsads');
        $cache = $this->getCacheInstance();
        $key = "products.name.by.cateid2" . $this->_tablename;
        $result = $cache->getCache($key);
//        $cache->deleteCache($key);//exit();
//        $result = false;
//        var_dump($key);exit();
        if ($result === FALSE) {
            $db = $this->getDbConnection();
            $query = "SELECT title, itemid,price,original_price,productsid,productscode,cateid FROM ws_productsitem WHERE title != '' and (price > 0 or original_price > 0 ) and onstock = 1 ORDER BY title ASC";
//            var_dump($query);exit();
            $result = $db->fetchAll($query);

            if (!is_null($result) && is_array($result) && count($result) > 0) {
                foreach ($result as $item) {
//                    $item = trim(str_replace("", "%", $item));
                    $subfix = "";
                    if ($item["productsid"] == 4) {
                        $subfix = "--(" . $item["productscode"] . ")";
                    } else {
                        $subfix = "--(cty)";
                        if ($item["price"] > 0) {
                            $subfix = "--(hnam)";
                        }
                    }
                    $itemid = "";
                    $itemid = "--" . $item["itemid"];
                    $productsid = "--" . $item["productsid"];
                    $cate_id = "--" . $item["cateid"];

                    $ret[] = "\"" . $item["title"] . $subfix . $itemid . $productsid . $cate_id."\"";
                }
                $ret = implode(",", $ret);
                $result = $ret;
                $cache->setCache($key, $result, 60 * 5);
            }
        }
        return $result;
    }
    public function getAllTagsByPhone2() {
        $cache = $this->getCacheInstance();
        $key = "getAllTagsByPhone" . $this->_tablename;
        $result = $cache->getCache($key);
        if ($result === FALSE) {
            $db = $this->getDbConnection();
            $query = "SELECT id, fullname,phone,address FROM addon_users WHERE phone != '' ORDER BY phone ASC";
            $result = $db->fetchAll($query);
            if (!is_null($result) && is_array($result) && count($result) > 0) {
                foreach ($result as $item) {
                    $itemid = "";
                    $itemid = "--" . $item["id"];
                    $fullname = "--" . $item["fullname"];
                    $address = "--" . $item["address"];
                    $ret[] = "\"" . $item["phone"] . $itemid . $fullname . $address."\"";
                }
                $ret = implode(",", $ret);
                $result = $ret;
                $cache->setCache($key, $result, 60 * 5);
            }
        }
        return $result;
    }

    public function getListAllPaging($productsid, $cateid, $offset, $records, $viewType = '') {
        $cache = $this->getCacheInstance();
        $key = $this->getKeyListAllPaging($productsid, $cateid, $offset, $records);
        $result = $cache->getCache($key);
        if ($result === FALSE) {
            $db = $this->getDbConnection();
            $where = array('productsid=?', 'cateid=?');

            switch ($viewType) {
                case 'pDesc':
                    $orderby = 'price DESC';
                    break;
                case 'nDesc':
                    $orderby = 'title DESC';
                    break;
                case 'pAsc':
                    $orderby = 'price ASC';
                    break;
                default:
                    $orderby = 'title ASC';
                    break;
            }

            $query = "SELECT * FROM " . $this->_tablename . " WHERE " . implode(" AND ", $where);
            $query .= " ORDER BY $orderby";
            $query .= " LIMIT $offset, $records";
            $data = array($productsid, $cateid);
            $result = $db->fetchAll($query, $data);

            if (!is_null($result) && is_array($result) && count($result) > 0) {
                $cache->setCache($key, $result, self::EXPIRED);
            }
        }
        return $result;
    }

    public function getListHighlight($productsid, $cateid, $quanlity = 30) {
        $cache = $this->getCacheInstance();
        $key = $this->getKeyListHighlight($productsid, $cateid);
        $result = $cache->getCache($key);
        if ($result === FALSE) {
            $db = $this->getDbConnection();
            $where = array('highlight=1', 'productsid=?', 'cateid=?');
            $query = "SELECT * FROM " . $this->_tablename . " WHERE " . implode(" AND ", $where);
            $query .= " ORDER BY updatedate DESC ";
            $query .= " LIMIT 0, $quanlity";
            $data = array($productsid, $cateid);
            $result = $db->fetchAll($query, $data);

            if (!is_null($result) && is_array($result) && count($result) > 0) {
                $cache->setCache($key, $result, self::EXPIRED);
            }
        }
        return $result;
    }

    public function getListNewest($productsid, $cateid, $quanlity = 30) {
        $cache = $this->getCacheInstance();
        $key = $this->getKeyListNewest($productsid, $cateid);
        $result = $cache->getCache($key);
        if ($result === FALSE) {
            $db = $this->getDbConnection();
            $where = array('newest=1', 'productsid=?', 'cateid=?');
            $query = "SELECT * FROM " . $this->_tablename . " WHERE " . implode(" AND ", $where);
            $query .= " ORDER BY updatedate DESC ";
            $query .= " LIMIT 0, $quanlity";
            $data = array($productsid, $cateid);
            $result = $db->fetchAll($query, $data);

            if (!is_null($result) && is_array($result) && count($result) > 0) {
                $cache->setCache($key, $result, self::EXPIRED);
            }
        }
        return $result;
    }

    public function getListTopHits($productsid, $cateid, $quanlity = 30) {
        $cache = $this->getCacheInstance();
        $key = $this->getKeyListTop($productsid, $cateid);
        $result = $cache->getCache($key);
        if ($result === FALSE) {
            $db = $this->getDbConnection();
            $where = array('productsid=?', 'cateid=?');
            $query = "SELECT * FROM " . $this->_tablename . " WHERE " . implode(" AND ", $where);
            $query .= " ORDER BY hits DESC ";
            $query .= " LIMIT 0, $quanlity";
            $data = array($productsid, $cateid);
            $result = $db->fetchAll($query, $data);

            if (!is_null($result) && is_array($result) && count($result) > 0) {
                $cache->setCache($key, $result, self::EXPIRED);
            }
        }
        return $result;
    }

    public function getTotalListFilter($productsid, $cateid, $filter) {
        $cache = $this->getCacheInstance();
        $key = $this->getKeyCountListFilter($productsid, $cateid, $filter);
        $result = $cache->getCache($key);
        if ($result === FALSE) {

            $where = array('productsid=?', 'cateid=?');
            if ($filter != '') {
                $where[] = $filter . "=?";
                $data = array($productsid, $cateid, $filter);
            } else {
                $data = array($productsid, $cateid);
            }

            $db = $this->getDbConnection();
            $query = "SELECT count(*) FROM " . $this->_tablename . " WHERE " . implode(" AND ", $where);
            $result = $db->fetchAll($query, $data);
            if (!is_null($result) && is_array($result) && count($result) > 0) {
                $cache->setCache($key, (int) $result[0]['total'], self::EXPIRED);
            }
        }
        return (int) $result;
    }

    public function getListFilter($productsid, $cateid, $offset, $records, $filter) {
        $cache = $this->getCacheInstance();
        $key = $this->getKeyListFilter($productsid, $cateid, $offset, $records, $filter);
        $result = $cache->getCache($key);
        if ($result === FALSE) {

            $where = array('productsid=?', 'cateid=?');
            if ($filter != '') {
                $where[] = $filter . "=?";
                $data = array($productsid, $cateid, $filter);
            } else {
                $data = array($productsid, $cateid);
            }

            $db = $this->getDbConnection();
            $query = "SELECT * FROM " . $this->_tablename . " WHERE " . implode(" AND ", $where);
            $query .= " ORDER BY myorder asc ";
            $query .= " LIMIT $offset, $records";
            $result = $db->fetchAll($query, $data);
            if (!is_null($result) && is_array($result) && count($result) > 0) {
                $cache->setCache($key, $result, self::EXPIRED);
            }
        }

        return $result;
    }

    //Nghidv added 3:26pm 15-5-2011
    public function getDetailByMyorder($myorder) {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " WHERE myorder='" . parent::adaptSQL($myorder) . "'";
        $result = $db->fetchAll($query);
        if ($result != null && is_array($result)) {
            $result = $result[0];
            return $result;
        }
    }

    //Nghidv added 3:26pm 15-5-2011
    public function getMaxMyOrder($productsid = 0, $cateid = 0) {

        $db = $this->getDbConnection();
        if ($productsid > 0 && $cateid > 0) {
            $query = "SELECT max(myorder) AS mysum FROM " . $this->_tablename . " WHERE productsid=? AND cateid=?";
            $data = array($productsid, $cateid);
        } elseif ($productsid > 0 && $cateid == 0) {
            $query = "SELECT max(myorder) AS mysum FROM " . $this->_tablename . " WHERE productsid=?";
            $data = array($productsid);
        }

        $result = $db->fetchAll($query, $data);
        if (!is_null($result) && is_array($result) && count($result) == 1) {
            $result = $result[0]['mysum'];
        } else {
            $result = 0;
        }

        return $result;
    }

    //Nghidv added 3:26pm 15-5-2011
    public function getListByProIdPagingOld($productsid = 0, $cateid = 0, $offset = 0, $records = 20, $filter = '', $enable = '', $onstock = '', $key = '', $sort = '', $price_start = 0, $price_end = 0, $_where = array()) {

        if ($cateid == 191)
            $productsid = 5;
//            if ($filter == 'newest')  $filter = "newest desc, ";
//            if ($filter == 'highlight')  $filter = "highlight desc, ";
        $_select = '';
        ($filter != '') ? $_filter[] = " $filter desc " : $filter = '';
        switch ($sort) {
            case 'a-z':
                $_filter[] = ' title asc ';
//                $_filter[] = ' myorder asc ';
                break;
            case 'z-a':
                $_filter[] = ' title desc ';
//                $_filter[] = ' myorder asc ';
                break;
            case 'da-z':
//                $_filter[] = ' myorder asc ';
                $_filter[] = ' posteddate asc ';

                break;
            case 'dz-a':
//                $_filter[] = ' myorder asc ';
                $_filter[] = ' posteddate desc ';
                break;
            case 'pa-z':

//                        $_filter[] = ' price asc ';
                $_select = ', (price * 10000000 + original_price * 10000000) as max';
                $_filter[] = ' max asc ';
                $_filter[] = ' original_price asc ';
//                $_filter[] = ' myorder asc ';
                break;
            case 'pz-a':

//                        $_filter[] = ' price desc ';
                $_select = ', (price * 10000000 + original_price * 10000000) as max';
                $_filter[] = ' max desc ';
                $_filter[] = ' original_price desc ';
//                $_filter[] = ' myorder asc ';
                break;
            default:
                $_filter[] = ' myorder ASC ';
                break;
        }

        ($enable != '') ? $where[] = " enabled = $enable " : $enable = '';
        ($onstock != '') ? $where[] = " onstock = $onstock " : $onstock = '';
        ($key != '') ? $where[] = " (title like '%$key%' OR title like '$key') " : $key = '';

        //$_filter[] = ' myorder asc ';

        $_filter = implode(" , ", $_filter);


//            if ($price_start != '' && $price_start != null)
//                $where2[] = "(price >= $price_start OR original_price >= $price_start)";
//            if ($price_end != '' && $price_end != null)
//                $where2[] = "(price <= $price_end OR original_price <= $price_end)";
        ((int) $price_start == 0 ? $price_start = 0 : true);
        ((int) $price_end == 0 ? $price_end = 1000000000 : true);
        $where2[] = "( (price >= $price_start AND price <= $price_end) OR (original_price >= $price_start AND original_price <= $price_end) )";

        if ($where2 != null && $where2 != '')
            $where2 = implode(" AND ", $where2);

        if ($onstock != "")
            $where2 .= " AND onstock = " . $onstock . " ";
        else {
            $where2 .= " AND onstock >0";
        }

        $cache = $this->getCacheInstance();
        $keys = preg_replace("/[ ,]/", "", $cateid . $productid . $offset . $records . $sort . $_filter . $enable . $onstock . $key . $price_end . $price_start . md5(json_encode($_where)));
        $key = $this->getKeyListAllMulti($keys);
        $result = $cache->getCache($key);

        $result = FALSE;
        if ($result === FALSE) {
            $db = $this->getDbConnection();

            if ($cateid == 0) {
                if ($productsid != null)
                    $where[] = 'productsid IN (' . $productsid . ')';

                $where = implode(" AND ", $where);

                if ($where2 != '' && $where2 != null)
                    $where .= " AND " . $where2;

                if (count($_where) == 0 || empty($_where)) {
                    $query = "SELECT * $_select FROM " . $this->_tablename . " WHERE  $where ORDER BY $_filter LIMIT ";
                } else {
                    $_where = implode(" AND ", $_where) . " AND ";
                    $query = "SELECT * $_select FROM " . $this->_tablename . " JOIN hn_features_data as fd WHERE  $_where $where ORDER BY $_filter LIMIT ";
                }

                //$data = array($productsid);
                $data = array();
            } else {
                if ($productsid != null)
                    $where[] = 'productsid IN (' . $productsid . ')';
                $where[] = 'cateid IN (' . $cateid . ')';
                $where = implode(" AND ", $where);
                if ($where2 != '' && $where2 != null)
                    $where .= " AND " . $where2;
                if (count($_where) == 0) {
                    $query = "SELECT * $_select FROM " . $this->_tablename . " WHERE $where ORDER BY $_filter LIMIT ";
                } else {
                    $_where = implode(" AND ", $_where) . " AND ";
                    $query = "SELECT DISTINCT(itemid), * $_select FROM " . $this->_tablename . " JOIN hn_features_data as fd  WHERE $_where $where ORDER BY $_filter LIMIT ";
                }
                //$data = array($productsid);
                $data = array();
            }

            $query .= " $offset, $records ";
//echo "<pre>";
//var_dump($query);
//die();
            $result = $db->fetchAll($query, $data);
            $cache->setCache($key, $result);
        }

        return $result;
    }

    public function getListByProIdPaging($productsid = 0, $cateid = 0, $offset = 0, $records = 20, $filter = '', $enable = '', $onstock = '', $key = '', $sort = '', $price_start = 0, $price_end = 0, $_where = array()) {
        $keys = md5($productsid . $cateid);
        if ($cateid == 191)
            $productsid = 5;
//            if ($filter == 'newest')  $filter = "newest desc, ";
//            if ($filter == 'highlight')  $filter = "highlight desc, ";
        $_select = '';
        ($filter != '') ? $_filter[] = " $filter desc " : $filter = '';
        switch ($sort) {
            case 'a-z':
                $_filter[] = ' title asc ';
//                $_filter[] = ' myorder asc ';
                break;
            case 'z-a':
                $_filter[] = ' title desc ';
//                $_filter[] = ' myorder asc ';
                break;
            case 'da-z':
//                $_filter[] = ' myorder asc ';
                $_filter[] = ' posteddate asc ';

                break;
            case 'dz-a':
//                $_filter[] = ' myorder asc ';
                $_filter[] = ' posteddate desc ';
                break;
            case 'pa-z':

//                        $_filter[] = ' price asc ';
                $_select = ', (price * 10000000 + original_price * 10000000) as max';
                //$_filter[] = ' myorder asc ';
                $_filter[] = ' max asc ';
                $_filter[] = ' original_price asc ';
                break;
            case 'pz-a':

//                        $_filter[] = ' price desc ';
                $_select = ', (price * 10000000 + original_price * 10000000) as max';
                //$_filter[] = ' myorder asc ';
                $_filter[] = ' max desc ';
                $_filter[] = ' original_price desc ';
                break;
            default:
                $_filter[] = ' myorder ASC ';
                break;
        }

        ($enable != '') ? $where[] = " enabled = $enable " : $enable = '';
        ($onstock != '') ? $where[] = " onstock = $onstock " : $onstock = '';
        preg_match("/([\d]+)/", $key, $matches);
        $match = $matches[0];
        $key_original = str_replace(" ", "%", $key);
        $key_match = str_replace($match, " $match", $key);
        $key = str_replace($match, "%$match", $key);
        $key = str_replace(" ", "%", $key);
        ($key != '') ? $where[] = " (title like '%$key%' OR title like '$key' OR title like '%$key_original%') " : $key = '';
        if ($key != '') {
            $_select .= ', MATCH(title) AGAINST ("' . $key_match . '") as score';
            $_filter = array();
            $_filter[] = "original_price DESC";
            $_filter[] = "price DESC";
            $_filter[] = "score ASC";
        }
        //$_filter[] = ' myorder asc ';

        $_filter = implode(" , ", $_filter);


//            if ($price_start != '' && $price_start != null)
//                $where2[] = "(price >= $price_start OR original_price >= $price_start)";
//            if ($price_end != '' && $price_end != null)
//                $where2[] = "(price <= $price_end OR original_price <= $price_end)";
        ((int) $price_start == 0 ? $price_start = 0 : true);
        ((int) $price_end == 0 ? $price_end = 1000000000 : true);
        $where2[] = "( (price >= $price_start AND price <= $price_end) OR (original_price >= $price_start AND original_price <= $price_end) )";

        if ($where2 != null && $where2 != '')
            $where2 = implode(" AND ", $where2);

        if ($onstock != "")
            $where2 .= " AND onstock = " . $onstock . " ";

        $cache = $this->getCacheInstance();
        $keys = $keys . preg_replace("/[ ,]/", "", $cateid . $productid . $offset . $records . $sort . $_filter . $enable . $onstock . $key . $price_end . $price_start . md5(json_encode($_where)));

        $key = $this->getKeyListAllMulti($keys);
        $result = $cache->getCache($key);

//        $result = FALSE;
        if ($result === FALSE) {
            $db = $this->getDbConnection();

            if ($cateid == 0) {
                if ($productsid != null)
                    $where[] = 'productsid IN (' . $productsid . ')';

                $where = implode(" AND ", $where);

                if ($where2 != '' && $where2 != null)
                    $where .= " AND " . $where2;

                if (count($_where) == 0 || empty($_where)) {
                    $query = "SELECT * $_select FROM " . $this->_tablename . " WHERE  $where ORDER BY $_filter LIMIT ";
                } else {
                    $_where = implode(" AND ", $_where) . " AND ";
                    $query = "SELECT * $_select FROM " . $this->_tablename . " JOIN hn_features_data as fd WHERE  $_where $where ORDER BY $_filter LIMIT ";
                }

                //$data = array($productsid);
                $data = array();
            } else {
                if ($productsid != null)
                    $where[] = 'productsid IN (' . $productsid . ')';
                $where[] = 'cateid IN (' . $cateid . ')';
                $where = implode(" AND ", $where);
                if ($where2 != '' && $where2 != null)
                    $where .= " AND " . $where2;
                if (count($_where) == 0) {
                    $query = "SELECT * $_select FROM " . $this->_tablename . " WHERE $where ORDER BY $_filter LIMIT ";
                } else {
                    $_where = implode(" AND ", $_where) . " AND ";
                    $query = "SELECT DISTINCT(itemid), * $_select FROM " . $this->_tablename . " JOIN hn_features_data as fd  WHERE $_where $where ORDER BY $_filter LIMIT ";
                }
                //$data = array($productsid);
                $data = array();
            }

            $query .= " $offset, $records ";
//echo "<pre>";
//var_dump($query);
//die();
            $result = $db->fetchAll($query, $data);
//            echo "<pre>";
//            var_dump($result);
//            die();
//                
            $cache->setCache($key, $result);
        }

        return $result;
    }

    public function getListByProIdPagingUsed($productsid = 0, $cateid = 0, $offset = 0, $records = 20, $filter = '', $enable = '', $onstock = '', $key = '', $sort = '', $price_start = 0, $price_end = 0, $_where = array()) {
        $keys = md5($productsid . $cateid);
        if ($cateid == 191)
            $productsid = 5;
//            if ($filter == 'newest')  $filter = "newest desc, ";
//            if ($filter == 'highlight')  $filter = "highlight desc, ";
        $_select = '';
        ($filter != '') ? $_filter[] = " $filter desc " : $filter = '';
        switch ($sort) {
            case 'a-z':
                $_filter[] = ' title asc ';
//                $_filter[] = ' myorder asc ';
                break;
            case 'z-a':
                $_filter[] = ' title desc ';
//                $_filter[] = ' myorder asc ';
                break;
            case 'da-z':
//                $_filter[] = ' myorder asc ';
                $_filter[] = ' posteddate asc ';

                break;
            case 'dz-a':
//                $_filter[] = ' myorder asc ';
                $_filter[] = ' posteddate desc ';
                break;
            case 'pa-z':

//                        $_filter[] = ' price asc ';
                $_select = ', (price * 10000000 + original_price * 10000000) as max';
                //$_filter[] = ' myorder asc ';
                $_filter[] = ' max asc ';
                $_filter[] = ' original_price asc ';
                break;
            case 'pz-a':

//                        $_filter[] = ' price desc ';
                $_select = ', (price * 10000000 + original_price * 10000000) as max';
                //$_filter[] = ' myorder asc ';
                $_filter[] = ' max desc ';
                $_filter[] = ' original_price desc ';
                break;
            default:
                $_filter[] = ' myorder ASC ';
                break;
        }

        ($enable != '') ? $where[] = " enabled = $enable " : $enable = '';
        ($onstock != '') ? $where[] = " onstock = $onstock " : $onstock = '';
        preg_match("/([\d]+)/", $key, $matches);
        $match = $matches[0];
        $key = str_replace("  ", " ", $key);
        $key_original = str_replace(" ", "%", $key);
        $key_match = str_replace($match, " $match", $key);
        $key = str_replace($match, "%$match", $key);
        $key = str_replace(" ", "%", $key);
        ($key != '') ? $where[] = " (title like '%$key%' OR title like '$key' OR title like '%$key_original%') " : $key = '';
        if ($key != '') {
            $_select .= ', MATCH(title) AGAINST ("' . $key_match . '") as score';
            $_filter = array();
            $_filter[] = "original_price DESC";
            $_filter[] = "price DESC";
            $_filter[] = "score ASC";
        }
        //$_filter[] = ' myorder asc ';

        $_filter = implode(" , ", $_filter);


//            if ($price_start != '' && $price_start != null)
//                $where2[] = "(price >= $price_start OR original_price >= $price_start)";
//            if ($price_end != '' && $price_end != null)
//                $where2[] = "(price <= $price_end OR original_price <= $price_end)";
        ((int) $price_start == 0 ? $price_start = 0 : true);
        ((int) $price_end == 0 ? $price_end = 1000000000 : true);
        $where2[] = "( (price >= $price_start AND price <= $price_end) OR (original_price >= $price_start AND original_price <= $price_end) )";
//        $where2[] = "cateid != 53";

        if ($where2 != null && $where2 != '')
            $where2 = implode(" AND ", $where2);

        if ($onstock != "")
            $where2 .= " AND onstock = " . $onstock . " ";

        $cache = $this->getCacheInstance();
        $keys = $keys . preg_replace("/[ ,]/", "", $cateid . $productid . $offset . $records . $sort . $_filter . $enable . $onstock . $key . $price_end . $price_start . md5(json_encode($_where)));

        $key = $this->getKeyListAllMulti($keys);
        $result = $cache->getCache($key);

        $result = FALSE;
        if ($result === FALSE) {
            $db = $this->getDbConnection();

            if ($cateid == 0) {
                if ($productsid != null)
                    $where[] = 'productsid IN (' . $productsid . ')';

                $where = implode(" AND ", $where);

                if ($where2 != '' && $where2 != null)
                    $where .= " AND " . $where2;

                if (count($_where) == 0 || empty($_where)) {
                    $query = "SELECT * $_select FROM " . $this->_tablename . " WHERE  $where ORDER BY $_filter LIMIT ";
                } else {
                    $_where = implode(" AND ", $_where) . " AND ";
                    $query = "SELECT * $_select FROM " . $this->_tablename . " JOIN hn_features_data as fd WHERE  $_where $where ORDER BY $_filter LIMIT ";
                }

                //$data = array($productsid);
                $data = array();
            } else {
                if ($productsid != null)
                    $where[] = 'productsid IN (' . $productsid . ')';
                $where[] = 'cateid IN (' . $cateid . ')';
                $where = implode(" AND ", $where);
                if ($where2 != '' && $where2 != null)
                    $where .= " AND " . $where2;
                if (count($_where) == 0) {
                    $query = "SELECT * $_select FROM " . $this->_tablename . " WHERE $where ORDER BY $_filter LIMIT ";
                } else {
                    $_where = implode(" AND ", $_where) . " AND ";
                    $query = "SELECT DISTINCT(itemid), * $_select FROM " . $this->_tablename . " JOIN hn_features_data as fd  WHERE $_where $where ORDER BY $_filter LIMIT ";
                }
                //$data = array($productsid);
                $data = array();
            }

            $query .= " $offset, $records ";
//echo "<pre>";
//var_dump($query);
//die();
            $result = $db->fetchAll($query, $data);
            $cache->setCache($key, $result);
        }

        return $result;
    }

    public function getListByProIdPaging2($productsid = 0, $cateid = 0, $offset = 0, $records = 20, $filter = '', $enable = '', $onstock = '', $key = '', $sort = '', $price_start = 0, $price_end = 0, $_where = array()) {
        $keys = md5($productsid . $cateid);
        if ($cateid == 191)
            $productsid = 5;
//            if ($filter == 'newest')  $filter = "newest desc, ";
//            if ($filter == 'highlight')  $filter = "highlight desc, ";
        $_select = '';
        ($filter != '') ? $_filter[] = " $filter desc " : $filter = '';
        switch ($sort) {
            case 'a-z':
                $_filter[] = ' title asc ';
//                $_filter[] = ' myorder asc ';
                break;
            case 'z-a':
                $_filter[] = ' title desc ';
//                $_filter[] = ' myorder asc ';
                break;
            case 'da-z':
//                $_filter[] = ' myorder asc ';
                $_filter[] = ' posteddate asc ';

                break;
            case 'dz-a':
//                $_filter[] = ' myorder asc ';
                $_filter[] = ' posteddate desc ';
                break;
            case 'pa-z':

//                        $_filter[] = ' price asc ';
                $_select = ', (price * 10000000 + original_price * 10000000) as max';
                //$_filter[] = ' myorder asc ';
                $_filter[] = ' max asc ';
                $_filter[] = ' original_price asc ';
                break;
            case 'pz-a':

//                        $_filter[] = ' price desc ';
                $_select = ', (price * 10000000 + original_price * 10000000) as max';
                //$_filter[] = ' myorder asc ';
                $_filter[] = ' max desc ';
                $_filter[] = ' original_price desc ';
                break;
            default:
                $_filter[] = ' myorder ASC ';
                break;
        }

        ($enable != '') ? $where[] = " enabled = $enable " : $enable = '';
        ($onstock != '') ? $where[] = " onstock = $onstock " : $onstock = '';
        preg_match("/([\d]+)/", $key, $matches);
        $match = $matches[0];
        $key_original = str_replace(" ", "%", $key);
        $key_match = str_replace($match, " $match", $key);
        $key = str_replace($match, "%$match", $key);
        $key = str_replace(" ", "%", $key);
        ($key != '') ? $where[] = " (title like '%$key%' OR title like '$key' OR title like '%$key_original%') " : $key = '';
        if ($key != '') {
            $_select .= ', MATCH(title) AGAINST ("' . $key_match . '") as score';
            $_filter = array();
            $_filter[] = "original_price DESC";
            $_filter[] = "price DESC";
            $_filter[] = "score ASC";
        }
        //$_filter[] = ' myorder asc ';

        $_filter = implode(" , ", $_filter);


//            if ($price_start != '' && $price_start != null)
//                $where2[] = "(price >= $price_start OR original_price >= $price_start)";
//            if ($price_end != '' && $price_end != null)
//                $where2[] = "(price <= $price_end OR original_price <= $price_end)";
        ((int) $price_start == 0 ? $price_start = 0 : true);
        ((int) $price_end == 0 ? $price_end = 1000000000 : true);
        $where2[] = "( (price >= $price_start AND price <= $price_end) OR (original_price >= $price_start AND original_price <= $price_end) )";

        if ($where2 != null && $where2 != '')
            $where2 = implode(" AND ", $where2);

        if ($onstock != "") {
            $where2 .= " AND onstock = " . $onstock . " ";
        } else {
            $where2 .= " AND onstock != 0 ";
        }

        $cache = $this->getCacheInstance();
        $keys = $keys . preg_replace("/[ ,]/", "", $cateid . $productid . $offset . $records . $sort . $_filter . $enable . $onstock . $key . $price_end . $price_start . md5(json_encode($_where)));

        $key = $this->getKeyListAllMulti($keys);
        $result = $cache->getCache($key);

//        $result = FALSE;
        if ($result === FALSE) {
            $db = $this->getDbConnection();

            if ($cateid == 0) {
                if ($productsid != null)
                    $where[] = 'productsid IN (' . $productsid . ')';

                $where = implode(" AND ", $where);

                if ($where2 != '' && $where2 != null)
                    $where .= " AND " . $where2;

                if (count($_where) == 0 || empty($_where)) {
                    $query = "SELECT * $_select FROM " . $this->_tablename . " WHERE  $where ORDER BY $_filter LIMIT ";
                } else {
                    $_where = implode(" AND ", $_where) . " AND ";
                    $query = "SELECT * $_select FROM " . $this->_tablename . " JOIN hn_features_data as fd WHERE  $_where $where ORDER BY $_filter LIMIT ";
                }

                //$data = array($productsid);
                $data = array();
            } else {
                if ($productsid != null)
                    $where[] = 'productsid IN (' . $productsid . ')';
                $where[] = 'cateid IN (' . $cateid . ')';
                $where = implode(" AND ", $where);
                if ($where2 != '' && $where2 != null)
                    $where .= " AND " . $where2;
                if (count($_where) == 0) {
                    $query = "SELECT * $_select FROM " . $this->_tablename . " WHERE $where ORDER BY $_filter LIMIT ";
                } else {
                    $_where = implode(" AND ", $_where) . " AND ";
                    $query = "SELECT DISTINCT(itemid), * $_select FROM " . $this->_tablename . " JOIN hn_features_data as fd  WHERE $_where $where ORDER BY $_filter LIMIT ";
                }
                //$data = array($productsid);
                $data = array();
            }

            $query .= " $offset, $records ";
//echo "<pre>";
//var_dump($query);
//die();
            $result = $db->fetchAll($query, $data);
            $cache->setCache($key, $result);
        }

        return $result;
    }

    public function getCountByProIdPaging($productsid = 0, $cateid = 0, $filter = '', $enable = '', $onstock = '', $key = '', $price_start = '', $price_end = '', $_where = array()) {

        ($filter != '') ? $filter = " $filter desc, " : $filter = '';
        ($enable != '') ? $where[] = " enabled = $enable " : $enable = '';
        ($onstock != '') ? $where[] = " onstock = $onstock " : $onstock = '';
//        ($key != '') ? $where[] = " title like '%$key%'" : $key = '';
        ($key != '') ? $where[] = " (title like '%$key%' OR title like '$key') " : $key = '';

//            if ($price_start != '' && $price_start != null)
//                $where2[] = "(price >= $price_start OR original_price >= $price_start)";
//            if ($price_end != '' && $price_end != null)
//                $where2[] = "(price <= $price_end OR original_price <= $price_end)";

        /*
         * if price not set, default = 0
         * if price_end not set, default max very very large value 100,000,000
         */
        ((int) $price_start == 0 ? $price_start = 0 : true);
        ((int) $price_end == 0 ? $price_end = 1000000000 : true);
        $where2[] = "( (price >= $price_start AND price <= $price_end) OR (original_price >= $price_start AND original_price <= $price_end) )";

        if (count($where2) > 0)
            $where2 = implode(" AND ", $where2);

        $key = 'count.products.cateid.list.count.all.' . $cateid . $productsid . $filter . $enable . $onstock . $key . $price_end . md5(json_encode($_where));

        $cache = $this->getCacheInstance();
        $result = $cache->getCache($key);
//        $result = false;
        if ($result === FALSE) {
            $db = $this->getDbConnection();
            if ($cateid == 0) {
                if ($productsid != null)
                    $where[] = 'productsid IN (' . $productsid . ')';
                $where = implode(" AND ", $where);
                if ($where2 != '' && $where2 != null)
                    $where .= " AND " . $where2;
                if (count($_where) == 0) {
                    $query = "SELECT count(*) as total FROM " . $this->_tablename . " WHERE  $where ORDER BY $filter myorder asc ";
                } else {

                    $_where = implode(' AND ', $_where) . ' AND ';
                    $query = "SELECT count(DISTINCT(itemid)) as total FROM " . $this->_tablename . " JOIN hn_features_data fd WHERE  $_where $where ORDER BY $filter myorder asc ";
                }
                $data = array($productsid);
            } else {
                if ($productsid == null)
                    $where[] = 'productsid IN (?)';
                $where[] = 'cateid IN (' . $cateid . ')';
                $where = implode(" AND ", $where);
                if ($where2 != '' && $where2 != null)
                    $where .= " AND " . $where2;
                if (count($_where) == 0) {
                    $query = "SELECT count(*) as total FROM " . $this->_tablename . " WHERE $where ORDER BY $filter myorder asc ";
                } else {

                    $_where = implode(' AND ', $_where) . ' AND ';

                    $query = "SELECT count(DISTINCT(itemid)) as total FROM " . $this->_tablename . " JOIN hn_features_data fd WHERE $_where $where ORDER BY $filter myorder asc ";
                }
                $data = array($productsid);
            }
//            echo "<pre>";
//            var_dump($query);
//            die();
            $result = $db->fetchAll($query, $data);
            $cache->setCache($key, $result[0]['total'], self::EXPIRED);
            $result = $result[0]['total'];
        }
        return $result;
    }

    public function getCountByProIdPaging2($productsid = 0, $cateid = 0, $filter = '', $enable = '', $onstock = '', $key = '', $price_start = '', $price_end = '', $_where = array()) {

        ($filter != '') ? $filter = " $filter desc, " : $filter;
        ($enable != '') ? $where[] = " enabled = $enable " : $enable;
        if ($onstock == null) {
            $where[] = " onstock != 0 ";
        } else {
            $where[] = " onstock = $onstock ";
        }
//        $where[] = " onstock != 0 ";
//        ($key != '') ? $where[] = " title like '%$key%'" : $key = '';
        ($key != '') ? $where[] = " (title like '%$key%' OR title like '$key') " : $key = '';

//            if ($price_start != '' && $price_start != null)
//                $where2[] = "(price >= $price_start OR original_price >= $price_start)";
//            if ($price_end != '' && $price_end != null)
//                $where2[] = "(price <= $price_end OR original_price <= $price_end)";

        /*
         * if price not set, default = 0
         * if price_end not set, default max very very large value 100,000,000
         */
        ((int) $price_start == 0 ? $price_start = 0 : true);
        ((int) $price_end == 0 ? $price_end = 1000000000 : true);
        $where2[] = "( (price >= $price_start AND price <= $price_end) OR (original_price >= $price_start AND original_price <= $price_end) )";

        if (count($where2) > 0)
            $where2 = implode(" AND ", $where2);

        $keys = 'count.products.cateid.list.count.all.' . $cateid . $productsid . $filter . $enable . $onstock . $price_end . md5(json_encode($_where));

        $cache = $this->getCacheInstance();
        $result = $cache->getCache($keys);
//        $result = false;

        if ($result === FALSE) {
            $db = $this->getDbConnection();
            if ($cateid == 0) {
                if ($productsid != null)
                    $where[] = 'productsid IN (' . $productsid . ')';
                $where = implode(" AND ", $where);
                if ($where2 != '' && $where2 != null)
                    $where .= " AND " . $where2;
                if (count($_where) == 0) {
                    $query = "SELECT count(*) as total FROM " . $this->_tablename . " WHERE  $where ORDER BY $filter myorder asc ";
                } else {

                    $_where = implode(' AND ', $_where) . ' AND ';
                    $query = "SELECT count(DISTINCT(itemid)) as total FROM " . $this->_tablename . " JOIN hn_features_data fd WHERE  $_where $where ORDER BY $filter myorder asc ";
                }
                $data = array($productsid);
            } else {
                if ($productsid == null)
                    $where[] = 'productsid IN (?)';
                $where[] = 'cateid IN (' . $cateid . ')';
                $where = implode(" AND ", $where);
                if ($where2 != '' && $where2 != null)
                    $where .= " AND " . $where2;
                if (count($_where) == 0) {
                    $query = "SELECT count(*) as total FROM " . $this->_tablename . " WHERE $where ORDER BY $filter myorder asc ";
                } else {

                    $_where = implode(' AND ', $_where) . ' AND ';

                    $query = "SELECT count(DISTINCT(itemid)) as total FROM " . $this->_tablename . " JOIN hn_features_data fd WHERE $_where $where ORDER BY $filter myorder asc ";
                }
                $data = array($productsid);
            }
            $result = $db->fetchAll($query, $data);
            $cache->setCache($key, $result[0]['total'], self::EXPIRED);
            $result = $result[0]['total'];
        }
        return $result;
    }

    public function countListPagingCateidAndKeywords($cateid, $keyword = '') {
        if ($cateid == 0)
            $cateid = "11,12";
        elseif ($cateid == 1)
            $cateid = "12";
        else
            $cateid = "11";

        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " WHERE productsid IN ($cateid) AND title LIKE '%$keyword%' ORDER BY itemid desc";
        $result = $db->fetchAll($query);
        if (!is_null($result) && is_array($result)) {
            $num = count($result);
            return $num;
        }
    }

    public function getListByFilterByCateidAndKeywordWithPaging($cateid = 0, $keyword, $offset = '', $records = 20, $filter = '', $pids = 4) {
        $cache = $this->getCacheInstance();
        $key = $this->getKeyListFilter($cateid, $keyword, $offset, $records, $filter, $pids);
        $result = $cache->getCache($key);
        if ($result === FALSE) {
            if ($filter == 1) {
                $orderby = "price ASC";
            } elseif ($filter == 2) {
                $orderby = "price DESC";
            } elseif ($filter == 3) {
                $orderby = "title ASC";
            } elseif ($filter == 4) {
                $orderby = "title DESC";
            } elseif ($filter == 5) {
                $orderby = "discount DESC";
            } elseif ($filter == "" || !isset($filter)) {
                $orderby = "posteddate DESC, ishot DESC";
            }
            $where[] = "1=1";
            if ($cateid > 0)
                $where[] = "cateid IN ($cateid)";
            if ($keyword != '')
                $where[] = "title LIKE '%$keyword%'";
            $where[] = "productsid IN ($pids)";

            $where = implode(" AND ", $where);

            $db = $this->getDbConnection();
            $query = "SELECT * FROM " . $this->_tablename . " WHERE  $where  ORDER BY $orderby ";

            if ($offset >= 0 && $records > 0)
                $query .= " LIMIT $offset, $records";
            $result = $db->fetchAll($query);
//                if(!is_null($result) && is_array($result))
//                {
            $cache->setCache($key, $result, self::EXPIRED);
//                }
        }
        return $result;
    }

    public function getListWithPagingByProductidCateid($productid, $cateid, $offset, $records = 20, $filter) {

        if ($filter == 1) {
            $orderby = "price ASC";
        } elseif ($filter == 2) {
            $orderby = "price DESC";
        } elseif ($filter == 3) {
            $orderby = "title ASC";
        } elseif ($filter == 4) {
            $orderby = "title DESC";
        } elseif ($filter == 5) {
            $orderby = "discount DESC";
        } elseif ($filter == "" || !isset($filter)) {
            $orderby = "title ASC";
        }
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " WHERE productsid=$productid AND cateid=$cateid ORDER BY $orderby LIMIT ";

        $query .= " $offset, $records";
        $result = $db->fetchAll($query);

        if (!is_null($result) && is_array($result)) {
            return $result;
        }
    }

    public function getList($productsid = 0, $cateid = 0) {
        $cache = $this->getCacheInstance();
        $key = $this->getKeyList($productsid, $cateid);
        $result = $cache->getCache($key);
        if ($result === FALSE) {
            $db = $this->getDbConnection();
            $query = "SELECT * FROM " . $this->_tablename . " WHERE productsid=? AND cateid=? ORDER BY myorder asc";
            $data = array($productsid, $cateid);
            $result = $db->fetchAll($query, $data);
            if (!is_null($result) && is_array($result)) {
                $cache->setCache($key, $result);
            }
        }
        return $result;
    }
    public function getList2($productsid = 0) {
        $cache = $this->getCacheInstance();
        $key = "getList2p".$this->_tablename.$productsid;
        $result = $cache->getCache($key);
//        $result = FALSE;
        if ($result === FALSE) {
            $db = $this->getDbConnection();
            $query = "SELECT itemid,title FROM " . $this->_tablename . " WHERE productsid=$productsid  and original_price > 0 and enabled = 1 and onstock !=0 and cateid !=53 ORDER BY myorder asc";
            $result = $db->fetchAll($query);
            if (!is_null($result) && is_array($result)) {
                $cache->setCache($key, $result);
            }
        }
        return $result;
    }

    public function getListByCate($cateids) {
        $cache = $this->getCacheInstance();
        $key = $this->getKeyList($cateids);
        $result = $cache->getCache($key);
        if ($result === FALSE) {
            $db = $this->getDbConnection();
            $query = "SELECT itemid,productsid,cateid,title FROM " . $this->_tablename . " WHERE cateid IN ($cateids) AND enabled = 1 AND onstock = 1 ";
            $data = array();
            $result = $db->fetchAll($query, $data);
            if (!is_null($result) && is_array($result)) {
                $cache->setCache($key, $result);
            }
        }
        return $result;
    }
    public function getListByCatedId($cateids) {
        $cache = $this->getCacheInstance();
        $key = "getListByCatedId".$this->_tablename.$cateids;
        $result = $cache->getCache($key);
//        $result = FALSE;
        if ($result === FALSE) {
            $db = $this->getDbConnection();
            $query = "SELECT * FROM " . $this->_tablename . " WHERE cateid IN ($cateids)  ORDER BY posteddate DESC";
//            var_dump($query);exit();
            $data = array();
            $result = $db->fetchAll($query, $data);
            if (!is_null($result) && is_array($result)) {
                $cache->setCache($key, $result);
            }
        }
        return $result;
    }
    public function get_list_by_cated_2016($cateids) {
        $cache = $this->getCacheInstance();
        $key = $this->get_key_by_cate($cateids);
        $result = $cache->getCache($key);
        if ($result === FALSE) {
            $db = $this->getDbConnection();
            $query = "SELECT itemid,title,cateid FROM " . $this->_tablename . " WHERE cateid IN ($cateids) and  onstock!=0 ORDER BY posteddate DESC";
//            var_dump($query);exit();
            $data = array();
            $result = $db->fetchAll($query, $data);
            if (!is_null($result) && is_array($result)) {
                $cache->setCache($key, $result,3600);
            }
        }
        return $result;
    }
    public function get_key_by_cate_apple($productsid){
        $key = "get_list_by_cated_iphone".$this->_tablename.$productsid;
        return $key;
    }
    public function delete_key_by_cate_apple($productsid){
        $cache = $this->getCacheInstance();
        $key = $this->get_key_by_cate_apple($productsid);
        $cache->deleteCache($key);
    }
    public function get_key_by_cate($cateids){
        $key = "get_list_by_cated_2017".$this->_tablename.$cateids;
        return $key;
    }
    public function delete_key_by_cate($cateids){
        $cache = $this->getCacheInstance();
        $key = $this->get_key_by_cate($cateids);
        $cache->deleteCache($key);
    }
    public function get_list_by_cated_apple($productsid) { 
        $cache = $this->getCacheInstance();
        $key = $this->get_key_by_cate_apple($productsid);
        $result = $cache->getCache($key);
        if ($result === FALSE) {
            $db = $this->getDbConnection();
            $query = "SELECT itemid,title,cateid FROM " . $this->_tablename . " WHERE productsid = $productsid and title like '%iphone%' and  onstock!=0 ORDER BY posteddate DESC";
            $data = array();
            $result = $db->fetchAll($query, $data);
            if (!is_null($result) && is_array($result)) {
                $cache->setCache($key, $result,3600);
            }
        }
        return $result;
    }

    public function getListByCate2($cateids) {

        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . "  where itemid= $cateids ORDER BY price DESC";
        $data = array();
//            var_dump($query);exit();
        $result = $db->fetchAll($query, $data);

        return $result;
    }

    public function getListWithPaging($productsid, $cateid = 0, $offset, $records = 20) {
        $paging = $this->getPagingBusiness();
        $keyprefix = $this->getKeyListPaging($productsid, $cateid);
        $params = array(
            'productsid' => $productsid,
            'cateid' => $cateid
        );
        $result = $paging->getData($offset, $records, $keyprefix, $params);
        return $result;
    }

    //------------------------end------------------------------------
    public function getListAll($productsid = 0, $limit = 100) {
        $cache = $this->getCacheInstance();
        $key = $this->getKeyListAll($productsid . $limit);
        $result = $cache->getCache($key);
//        $result=false;
        if ($result === FALSE) {
            $db = $this->getDbConnection();
            $query = "SELECT * FROM " . $this->_tablename . " WHERE productsid IN (?) ORDER BY itemid desc LIMIT 0, $limit";
            $data = array($productsid);
            $result = $db->fetchAll($query, $data);
            if (!is_null($result) && is_array($result)) {
                $cache->setCache($key, $result);
            }
        }
        return $result;
    }
    
    public function getListAllForReportSale($productsid = 0, $limit = 100) {
        $cache = $this->getCacheInstance();
        $key = $this->getKeyListAll($productsid . $limit . "2");
        $result = $cache->getCache($key);
//        $result=false;
        if ($result === FALSE) {
            $db = $this->getDbConnection();
            $query = "SELECT itemid,price,original_price,title,onstock,enabled,productsid FROM " . $this->_tablename . " WHERE productsid IN (?) ORDER BY itemid desc LIMIT 0, $limit";
            $data = array($productsid);
            $result = $db->fetchAll($query, $data);
            if (!is_null($result) && is_array($result)) {
                $cache->setCache($key, $result);
            }
        }
        return $result;
    }

    public function getListAllOrder($productsid = 0, $offset = 0, $records = 40, $orderby = "updatedate desc", $keys = null) {
        $cache = $this->getCacheInstance();
        $key = $this->getKeyListAll($productsid . $orderby . $offset . $records . $keys);
        $result = $cache->getCache($key);
//        $result = false;
        if ($result === FALSE) {
            $db = $this->getDbConnection();
            if ($keys != null) {
                $query = "SELECT *,MATCH (title) AGAINST ('$keys') as p FROM " . $this->_tablename . " WHERE onstock!=0 AND  MATCH (title) AGAINST ('$keys') ORDER BY p DESC LIMIT $offset, $records";
            } else {
                $query = "SELECT * FROM " . $this->_tablename . " WHERE onstock!=0 AND productsid=? ORDER BY $orderby LIMIT $offset, $records";
            }

            $data = array($productsid);
            $result = $db->fetchAll($query, $data);
            if (!is_null($result) && is_array($result)) {
                $cache->setCache($key, $result);
            }
        }
        return $result;
    }

    //-----------------------------------------phan  nay chua coi-------------------------
    public function _getRealData($productsid, $cateid, $page) {
        $offset = 0;
        $records = self::PAGING_NUM_RECORDS;

        if ($page == 0)
            $offset = 0;
        else
            $offset = $page * $records;

        $db = $this->getDbConnection();

        $query = "SELECT * FROM " . $this->_tablename . " WHERE  ishot <> 1 and productsid=? AND cateid=? ORDER BY myorder asc,itemid desc LIMIT ";
        $query .= " $offset , $records ";
        $data = array($productsid, $cateid);
        $result = $db->fetchAll($query, $data);

        return $result;
    }

    //----------------------------------------end chua coi--------------------------
    public function getHotList($productsid, $cateid) {
        $cache = $this->getCacheInstance();
        $key = $this->getKeyListHotAll($productsid, $cateid);
        $result = $cache->getCache($key);
        if ($result === FALSE) {
            $db = $this->getDbConnection();
            $query = "SELECT * FROM " . $this->_tablename . " WHERE productsid=? and cateid=? and ishot=1 ORDER BY myorder,itemid desc";
            $data = array($productsid, $cateid);
            $result = $db->fetchAll($query, $data);
            if (!is_null($result) && is_array($result)) {
                $cache->setCache($key, $result);
            }
        }
        return $result;
    }

    public function getCountByCate($productsid = 0, $cateid = 0) {
        $cache = $this->getCacheInstance();
        $key = $this->getKeyCount($productsid, $cateid);
        $result = $cache->getCache($key);
        $result = false;
        if ($result === FALSE) {
            $db = $this->getDbConnection();
            if ($cateid > 0) {
                $query = "SELECT count(*) AS mysum FROM " . $this->_tablename . " WHERE productsid=? AND cateid=?";
                $data = array($productsid, $cateid);
            } else {
                $query = "SELECT count(*) AS mysum FROM " . $this->_tablename . " WHERE productsid=?";
                $data = array($productsid);
            }
            $result = $db->fetchAll($query, $data);
            if (!is_null($result) && is_array($result) && count($result) == 1) {
                $result = $result[0]['mysum'];
            } else {
                $result = 0;
            }
            $cache->setCache($key, $result);
        }
        return $result;
    }

    public function getCountBySubCate($productsid = 0, $cateid) {
        $cache = $this->getCacheInstance();
        $key = $this->getKeyCount($productsid, $cateid);
        $result = $cache->getCache($key);
        if ($result === FALSE) {
            $db = $this->getDbConnection();
            if ($cateid != "") {
                $query = "SELECT count(*) AS mysum FROM " . $this->_tablename . " WHERE productsid=? AND cateid IN ($cateid)";
                $data = array($productsid);
            }
            $result = $db->fetchAll($query, $data);
            if (!is_null($result) && is_array($result) && count($result) == 1) {
                $result = $result[0]['mysum'];
            } else {
                $result = 0;
            }
            $cache->setCache($key, $result);
        }
        return $result;
    }

    public function getMultiDetail($pids) {
        if (count($pids) == 0)
            return null;

        $ret = array();
        foreach ($pids as $pid) {
            $ret[] = $this->getDetail($pid);
        }
        return $ret;
    }

    public function getDetail($id) {
        $cache = $this->getCacheInstance();
        $key = "getDetail".$this->_tablename.$id;
        $result = $cache->getCache($key);
        $result = FALSE;
        if ($result === FALSE) {
            $db = $this->getDbConnection();
            $query = "SELECT * FROM " . $this->_tablename . " WHERE itemid='" . parent::adaptSQL($id) . "'";
            $result = $db->fetchAll($query);
            $result = $result[0];
            $cache->setCache($key, $result);
        }
        return $result;
    }
    public function get_detail($id) {
        $cache = $this->getCacheInstance();
        $key = "get_details".$this->_tablename.$id;
        $result = $cache->getCache($key);
        $result = FALSE;
        if ($result === FALSE) {
            $db = $this->getDbConnection();
            $query = "SELECT itemid,price,original_price,title,enabled,cateid,productsid,onstock FROM " . $this->_tablename . " WHERE itemid='" . parent::adaptSQL($id) . "'";
            $result = $db->fetchAll($query);
            $result = $result[0];
            $cache->setCache($key, $result);
        }
        return $result;
    }
    public function get_detail_by_itemid($id) {
        $cache = $this->getCacheInstance();
        $key = "get_detail_by_itemid".$this->_tablename.$id;
        $result = $cache->getCache($key);
        $result = FALSE;
        if ($result === FALSE) {
            $db = $this->getDbConnection();
            $query = "SELECT bonus_mobile,title,itemid FROM " . $this->_tablename . " WHERE itemid='" . parent::adaptSQL($id) . "'";
            $result = $db->fetchAll($query);
            $result = $result[0];
            $cache->setCache($key, $result);
        }
        return $result;
    }

    public function getDetail2($id) {
        $cache = $this->getCacheInstance();
        $key = "getDetail2.$this->_tablename" . $id;
        $result = $cache->getCache($key);
        $result = FALSE;
        if ($result === FALSE) {
            $db = $this->getDbConnection();
            $query = "SELECT * FROM $this->_tablename  WHERE itemid= $id";
            $result = $db->fetchAll($query);
            $cache->setCache($key, $result);
        }
        return $result;
    }

    public function getDetail3($id) {
        $cache = $this->getCacheInstance();
        $key = "getDetail3.$this->_tablename" . $id;
        $result = $cache->getCache($key);
        $result = FALSE;
        if ($result === FALSE) {
            $db = $this->getDbConnection();
            $query = "SELECT * FROM $this->_tablename  WHERE itemid= $id";
            $result = $db->fetchAll($query);
            $cache->setCache($key, $result);
        }
        return $result[0];
    }

    public function _update_bonus_company($id,$bonus_mobile, $bonus_company_full, $price_cty, $price_hnam) {
        $db = $this->getDbConnection();
        $query = "itemid = " . $id;
        $data = array(
            "bonus_mobile" =>$bonus_mobile,
            "bonus_company_full" => $bonus_company_full,
            "price" => $price_hnam,
            "original_price" => $price_cty
        );
        $result = $db->update($this->_tablename, $data, $query);
        return $result;
    }
    public function _update_bonus_mobile($id,$bonus_mobile, $bonus_company_full) {
        $db = $this->getDbConnection();
        $query = "itemid = " . $id;
        $data = array(
            "bonus_mobile" =>$bonus_mobile,
            "bonus_company_full" => $bonus_company_full
        );
        $result = $db->update($this->_tablename, $data, $query);
        return $result;
    }

    public function getDetailByName($name) {
        $cache = $this->getCacheInstance();
        $key = "getDetailByName.$this->_tablename" . $name;
        $result = $cache->getCache($key);
        if ($result === FALSE) {
            $_select .= ', MATCH(title) AGAINST ("' . $name . '") as score';
            $db = $this->getDbConnection();
//            $query = "SELECT * $_select FROM " . $this->_tablename . " WHERE title LIKE '%" . parent::adaptSQL($name) . "%'";
            $query = "SELECT * $_select FROM " . $this->_tablename . " WHERE cateid != 53 ORDER BY score DESC";
            $result = $db->fetchAll($query);
            $result = $result[0];
            $cache->setCache($key, $result);
        }
        return $result;
    }

    public function getDetailByName2($name) {
        $cache = $this->getCacheInstance();
        $key = "getDetailByName2.$this->_tablename" . $name;
        $result = $cache->getCache($key);
        $result = false;
        if ($result === FALSE) {
//            $_select .= "";
            $db = $this->getDbConnection();
//            $query = "SELECT * $_select FROM " . $this->_tablename . " WHERE title LIKE '%" . parent::adaptSQL($name) . "%'";
            $query = "SELECT * FROM  $this->_tablename WHERE title = '$name'";
//            echo "<pre>";
//            var_dump($query);exit();
//            die();
            $result = $db->fetchAll($query);
            $result = $result[0];
            $cache->setCache($key, $result);
        }

        return $result;
    }

    public function getDetailByName3($name, $product_cated) {
        $cache = $this->getCacheInstance();
        $key = "getDetailByName3.$this->_tablename" . $name . $product_cated;
        $result = $cache->getCache(md5($name));
        $result = false;


        if ($result === FALSE) {
//            $_select .= "";
            $db = $this->getDbConnection();
//            $query = "SELECT * $_select FROM " . $this->_tablename . " WHERE title LIKE '%" . parent::adaptSQL($name) . "%'";
            $query = "SELECT * FROM  $this->_tablename WHERE title = '$name'";
            if ($product_cated == '(cty)') {
                $query .= " and original_price >0";
            }
            if ($product_cated == '(hnam)') {
                $query .= " and price >0";
            }
//            echo "<pre>";
//            var_dump($query);exit();
//            die();
            $result = $db->fetchAll($query);
            $result = $result[0];
            $cache->setCache($key, $result);
        }

        return $result;
    }

    public function getListByName2($name) {
        $cache = $this->getCacheInstance();
        $key = "getListByName2.$this->_tablename" . $name;
        $result = $cache->getCache(md5("list" . $name));
        $result = false;
        if ($result === FALSE) {
//            $_select .= "";
            $db = $this->getDbConnection();
//            $query = "SELECT * $_select FROM " . $this->_tablename . " WHERE title LIKE '%" . parent::adaptSQL($name) . "%'";
            $query = "SELECT * FROM  $this->_tablename WHERE title = '$name' and onstock = 1";
//            echo "<pre>";
//            var_dump($query);exit();
//            die();
            $result = $db->fetchAll($query);
            $cache->setCache($key, $result);
        }

        return $result;
    }
    
    public function getProductsNameWithID2($getAcc=true) {
        $cache = $this->getCacheInstance();        
        $key = "products.name2.id.$getAcc";
        
        $result = $cache->getCache($key);
        $result=false;
        if ($result === FALSE) {
            $db = $this->getDbConnection();
            if ($getAcc==true) {
                $query = "SELECT title, itemid, productsid, price, original_price FROM ws_productsitem WHERE title != '' AND productsid IN (3,4,5) ORDER BY title ASC";
            } else {
                $query = "SELECT title, itemid, productsid, price, original_price FROM ws_productsitem WHERE title != '' AND productsid IN (3,5) ORDER BY title ASC";
            }
            
            $result = $db->fetchAll($query);

            if (!is_null($result) && is_array($result) && count($result) > 0) {
                foreach ($result as $item) {
                    if ($item["productsid"] == 4) {
                        $subfix = "(" . $item["productscode"] . ")";
                    } else {
                        $subfix = "(cty)";
                        if ($item["price"] > 0) {
                            $subfix = "(hnam)";
                        }
                    }
                    $ret[] = "\"" . $item["title"] . " --" . $item["itemid"] . "--" . $subfix ."\"";
                }
                $ret = implode(",", $ret);
                $result = $ret;
                $cache->setCache($key, $result);
            }
        }
        return $result;
    }

    public function insert($productsid, $cateid, $data) {
//        exit('dsadssdsadsa');
        $data['productsid'] = $productsid;
        $data['cateid'] = $cateid;
        $db = $this->getDbConnection();
//        var_dump($db);exit();
//        exit('123');
        $result = $db->insert($this->_tablename, $data);
//        var_dump($result);exit();
        if ($result > 0) {
//            exit('dsadas');
            $this->_deleteAllCache($productsid, $cateid);
            return $db->lastInsertId();
        }
    }

    //----------------------------------chua hieu---------------------------
    public function moveCate($id, $productsid, $cateid, $cateid_new) {
        $data = array(
            'cateid' => $cateid_new
        );
        $result = $this->_update($id, $data);
        if ($result > 0) {
            $this->_deleteAllCache($productsid, $cateid);
            $this->_deleteAllCache($productsid, $cateid_new, $id);
        }
    }

    //------------------------------------end--------------------------------
    public function update($id, $productsid, $cateid, $data) {
//		if(array_key_exists('productsid',$data))
//                        unset($data['productsid']);
//		if(array_key_exists('cateid',$data))
//                        unset($data['cateid']);
        $where = array();
        $where[] = "itemid='" . parent::adaptSQL($id) . "'";
        $db = $this->getDbConnection();
        $result = $db->update($this->_tablename, $data, $where);
        if ($result > 0) {
            $this->_deleteAllCache($productsid, $cateid, $id);
        }
        return $result;
    }

    public function delete($id, $productsid, $cateid) {
        //get current menu
        $current = $this->getDetail($id);

        $where = array();
        $where[] = "itemid='" . parent::adaptSQL($id) . "'";
        $db = $this->getDbConnection();
        $result = $db->delete($this->_tablename, $where);
        if ($result > 0) {
            $this->_deleteAllCache($productsid, $cateid, $id);
        }
    }
    public function updatefull($id, $data) {
        
        $where = array();
        $where[] = "itemid='" . parent::adaptSQL($id) . "'";
        $db = $this->getDbConnection();
        $result = $db->update($this->_tablename, $data, $where);
        return $result;
    }

    ///private functions /////

    private function _update($id, $data) {

        $where = array();
        $where[] = "itemid='" . parent::adaptSQL($id) . "'";
        $db = $this->getDbConnection();
        $result = $db->update($this->_tablename, $data, $where);
        return $result;
    }

    public function p_update($id, $quanlity) {
        $db = $this->getDbConnection();
        $query = "itemid = " . $id;
        if ($quanlity > 1) {
            $data = array(
                "quanlity" => $quanlity - 1
            );
        } else {
            $data = array(
                "enabled" => "0",
                "onstock" => "0"
            );
        }

        $result = $db->update($this->_tablename, $data, $query);
        return $result;
    }

    private function _deleteAllCache($productsid, $cateid, $id = null) {
        $cache = $this->getCacheInstance();
        $key = $this->getKeyList($productsid, $cateid);
        $cache->deleteCache($key);
        $key = $this->getKeyCount($productsid, $cateid);
        $cache->deleteCache($key);
        $key = $this->getKeyListAll($productsid);
        $cache->deleteCache($key);
        $key = $this->getKeyListHotAll($productsid, $cateid);
        $cache->deleteCache($key);
        if ($id != null) {
            $key = $this->getKeyDetail($id);
            $cache->deleteCache($key);
        }

        // clean paging cache
        $key = $this->getKeyListPaging($productsid, $cateid);
        $paging = $this->getPagingBusiness();
        $paging->clearCachePaging($key);

        //$cache->flushAll();
    }

    /**
     * Enter description here...
     *
     * @return Maro_Paging_Interface
     */
    private function getPagingBusiness() {
        if ($this->_paging != null)
            return $this->_paging;

        $cache = $this->getCacheInstance();

        $this->_paging = new Maro_Paging_Common($cache, array($this, "_getRealData"), self::PAGING_MAX_PAGE, self::PAGING_NUM_RECORDS);

        return $this->_paging;
    }

    public function getTop3ProductByProductsid($productsid = 0) {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " WHERE productsid=$productsid ORDER BY itemid desc LIMIT 0,3";
        $result = $db->fetchAll($query);
        return $result;
    }

    public function getListProductDiscount($productsid = 0) {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " WHERE productsid=$productsid AND discount > 0 ORDER BY itemid desc LIMIT 0,3";
        $result = $db->fetchAll($query);
        return $result;
    }

    public function getListProductTypical($productsid = 0) {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " WHERE productsid=$productsid AND highlight=1 ORDER BY itemid desc LIMIT 0,4";
        $result = $db->fetchAll($query);
        return $result;
    }

    public function getListProductOfProductidOfCateid($productsid, $listcateid) {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " WHERE productsid=$productsid AND cateid IN ('$listcateid') ORDER BY itemid desc LIMIT 0,5";
        $result = $db->fetchAll($query);
        return $result;
    }

    public function getListProductCungLoai($productsid, $cateid, $itemid) {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " WHERE productsid=$productsid AND cateid=$cateid AND itemid !=$itemid ORDER BY itemid desc LIMIT 0,4";
        $result = $db->fetchAll($query);
        return $result;
    }

    public function getListProductOfCateOfTitle($cateid, $keyword_title) {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " WHERE cateid=$cateid AND cateid=$cateid AND title LIKE '%$keyword_title%' ORDER BY itemid desc";
        $result = $db->fetchAll($query);
        return $result;
    }

    public function getListProductBanChay($productsid, $listcateid) {
        $listcateid = explode(",", $listcateid);
        foreach ($listcateid as &$item) {
            $item = "'" . $item . "'";
        }
        $listcateid = implode(',', $listcateid);

        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " WHERE productsid=? AND cateid IN ($listcateid) AND bestseller=1 ORDER BY itemid desc LIMIT 0,4";
        $data = array($productsid);
        $result = $db->fetchAll($query, $data);
        return $result;
    }

    public function getListProductTieuBieu($productsid, $listcateid) {

        $listcateid = explode(",", $listcateid);
        foreach ($listcateid as &$item) {
            $item = "'" . $item . "'";
        }
        $listcateid = implode(',', $listcateid);

        $db = $this->getDbConnection();

        $query = "SELECT * FROM " . $this->_tablename . " WHERE productsid= ? AND cateid IN ($listcateid) AND highlight=1 ORDER BY itemid desc LIMIT 0,4";
        $data = array($productsid);
        $result = $db->fetchAll($query, $data);
        return $result;
    }

    public function getListByType($typename = '', $limit = 8, $productsid = 0) {

        $cache = $this->getCacheInstance();
        $key = 'ws.products.items.list.type.' . $typename . $limit . $productsid;
        $result = $cache->getCache($key);

        if ($result === FALSE) {

            if ($typename != '')
//                $_typename = " ORDER BY myorder DESC, productsid DESC, $typename DESC, posteddate DESC"; 
                $_typename = " ORDER BY myorder DESC";
            else
//                $_typename = ' ORDER BY myorder DESC ';
                $_typename = ' ORDER BY myorder DESC ';

            $db = $this->getDbConnection();
            if ($productsid == 0)
                $query = "SELECT * FROM " . $this->_tablename . " WHERE 1 = 1 AND enabled=1 AND $typename = 1 $_typename LIMIT 0, $limit";
            else {
                $query = "SELECT * FROM " . $this->_tablename . " WHERE 1 =1 AND productsid IN(?) AND enabled=1 AND $typename = 1  $_typename , myorder ASC LIMIT 0, $limit";
            }

            $data = array($productsid);
            $result = $db->fetchAll($query, $data);
            $cache->setCache($key, $result);
        }
        return $result;
    }

    public function getListByTypeForAcc($typename = '', $limit = 8, $productsid = 0) {

        $cache = $this->getCacheInstance();
        $key = 'ws.products.items.list.type.acc.' . $typename . $limit . $productsid;
        $result = $cache->getCache($key);
        if ($result === FALSE) {

            if ($typename != '')
//                $_typename = " ORDER BY myorder DESC, productsid DESC, $typename DESC, posteddate DESC"; 
                $_typename = " ORDER BY posteddate DESC";
            else
//                $_typename = ' ORDER BY myorder DESC ';
                $_typename = ' ORDER BY posteddate DESC ';

            $db = $this->getDbConnection();
            if ($productsid == 0)
                $query = "SELECT * FROM " . $this->_tablename . " WHERE newest = 1 AND enabled=1 AND $typename = 1 $_typename LIMIT 0, $limit";
            else {
                $query = "SELECT * FROM " . $this->_tablename . " WHERE newest = 1 AND productsid IN(?) AND enabled=1 AND $typename = 1  $_typename , myorder ASC LIMIT 0, $limit";
            }

            $data = array($productsid);
            $result = $db->fetchAll($query, $data);
            $cache->setCache($key, $result);
        }
        return $result;
    }
    public function get_list_bhvip($is_bhmc = 1) {
        $db = $this->getDbConnection();
        $query = "SELECT itemid FROM " . $this->_tablename . " WHERE is_bhmc=$is_bhmc ";
        $result = $db->fetchAll($query);
        return $result;
    }
    public function getListByProductsID($strID, $limit = 20) {
        $cache = $this->getCacheInstance();
        $strID = trim($strID, ",");
        $key = 'p.idlist.' . $strID;
        $result = $cache->getCache($key);
        $result = FALSE;
        if ($result === FALSE) {
            $db = $this->getDbConnection();
            $query = "SELECT * FROM " . $this->_tablename . " WHERE itemid IN($strID) ORDER BY posteddate DESC $_typename LIMIT 0, $limit";
            $data = array();
            $result = $db->fetchAll($query, $data);
            $cache->setCache($key, $result);
        }
        return $result;
    }
    public function getListByProductsID2($strID) {
        $cache = $this->getCacheInstance();
        $strID = trim($strID, ",");
        $key = 'getListByProductsID2s'.$this->_tablename . $strID;
        $result = $cache->getCache($key);
        if ($result === FALSE) {
            $db = $this->getDbConnection();
            $query = "SELECT is_bhmc,bonus_mobile,cheap,bonus_company_full,bonus_mobile,itemid,title,price,original_price,ismoney,isservice,iskm,is_apple,istype,is_special,quanlity,cateid,productsid,productscode,warranty,btest FROM " . $this->_tablename . " WHERE itemid IN($strID) ORDER BY posteddate DESC ";
            $data = array();
            $result = $db->fetchAll($query, $data);
            $cache->setCache($key, $result,3600*24);
        }
        return $result;
    }
    public function get_list_by_itemid_productsid_cateid($strID,$productsid=0,$cateid=0) {
        $cache = $this->getCacheInstance();
        $strID = trim($strID, ",");
        $key = 'get_list_by_itemid_productsid_cateid'.$this->_tablename . $strID; 
        $result = $cache->getCache($key);
        $result = FALSE;
        if ($result === FALSE) {
            $db = $this->getDbConnection();
            $query = "SELECT is_bhmc,bonus_mobile,cheap,bonus_company_full,bonus_mobile,itemid,title,price,original_price,ismoney,isservice,iskm,is_apple,istype,is_special,quanlity,cateid,productsid,productscode,warranty,btest FROM " . $this->_tablename . " WHERE itemid IN($strID)  ";
            if((int)$productsid>0){
                $query .=" and productsid = $productsid";
            }
            if((int)$cateid>0){
                $query .=" and cateid = $cateid";
            }
            $query .=" ORDER BY posteddate DESC";
            $data = array();
            $result = $db->fetchAll($query, $data);
            $cache->setCache($key, $result,300);
        }
        return $result;
    }
    public function get_list_notsale($strID,$productsid=0,$cateid=0) {
        $cache = $this->getCacheInstance();
        $strID = trim($strID, ",");
        $key = 'get_list_notsale'.$this->_tablename . $strID.$productsid.$cateid;
        $result = $cache->getCache($key);
        $result = FALSE;
        if ($result === FALSE) {
            $db = $this->getDbConnection();
            $query = "SELECT bonus_mobile,cheap,bonus_company_full,bonus_mobile,itemid,title,price,original_price,ismoney,isservice,iskm,is_apple,istype,is_special,quanlity,cateid,productsid,productscode,warranty,btest FROM " . $this->_tablename . " WHERE title != '' and onstock = 1 AND enabled = 1 and itemid NOT IN($strID)  ";
            if((int)$productsid>0){
                $query .=" and productsid = $productsid";
            }
            if((int)$cateid == 0){
                $query .=" and cateid != 53";
            }else{
                $query .=" and cateid = $cateid";
            }
            
            $data = array();
            $result = $db->fetchAll($query, $data);
            $cache->setCache($key, $result,300);
        }
        return $result;
    }
    public function get_list_notsale2($productsid=0,$cateid=0) {
        $cache = $this->getCacheInstance();
        $key = 'get_list_notsale2'.$this->_tablename . $productsid.$cateid;
        $result = $cache->getCache($key);
        $result = FALSE;
        if ($result === FALSE) {
            $db = $this->getDbConnection();
            $query = "SELECT bonus_mobile,cheap,bonus_company_full,bonus_mobile,itemid,title,price,original_price,ismoney,isservice,iskm,is_apple,istype,is_special,quanlity,cateid,productsid,productscode,warranty,btest FROM " . $this->_tablename . " WHERE title != '' and onstock = 1 AND enabled = 1   ";
            
            if((int)$productsid>0){
                $query .=" and productsid = $productsid";
            }
            if((int)$cateid == 0){
                $query .=" and cateid != 53";
            }else{
                $query .=" and cateid = $cateid";
            }
            
            
            $data = array();
            $result = $db->fetchAll($query, $data);
            $cache->setCache($key, $result,300);
        }
        return $result;
    }
    
    public function getListByProductsIDLite($strID, $limit = 20) {
        $cache = $this->getCacheInstance();
        $strID = trim($strID, ",");
        $key = 'p.idlist.l.' . $strID;
        $result = $cache->getCache($key);
        if ($result === FALSE) {
            $db = $this->getDbConnection();
            $query = "SELECT itemid, title FROM " . $this->_tablename . " WHERE itemid IN($strID) ORDER BY posteddate DESC $_typename LIMIT 0, $limit";
            $data = array();
            $result = $db->fetchAll($query, $data);
            $cache->setCache($key, $result);
        }
        return $result;
    }
    public function getListByProductsIDLite2($strID, $limit = 20,$productsid="",$flag="") {
        $cache = $this->getCacheInstance();
        $strID = trim($strID, ",");
        $key = 'p.idlist.l.' . $strID;
        $result = $cache->getCache($key);
        $result = FALSE;
        if ($result === FALSE) {
            $db = $this->getDbConnection();
            $query = "SELECT itemid, title FROM " . $this->_tablename . " WHERE itemid IN($strID) ";
            if($productsid != null){
                
                $query .=" and productsid = $productsid";
            }
            if($flag != null){
                if($flag == 1){
                    $query .=" and original_price > 0";
                }else{
                    $query .=" and price > 0";
                }
                
            }
            $query .=" ORDER BY posteddate DESC $_typename LIMIT 0, $limit";
            
            $data = array();
            $result = $db->fetchAll($query, $data);
//            var_dump($query);exit();
            $cache->setCache($key, $result);
        }
        return $result;
    }

}

?>
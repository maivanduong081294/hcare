<?php
class Business_Helpers_Store
{
    private static $_instance = null;
    // module news to store
    
    function __construct()
    {
    }

    /**
     * get instance of Business_Helpers_Store
     *
     * @return Business_Helpers_Store
     */
    public static function getInstance()
    {
            if(self::$_instance == null)
            {
                    self::$_instance = new Business_Helpers_Store();
            }
            return self::$_instance;
    }
    
    public function getList(){
        $menuname = 'menu_shop';
        Business_Helpers_Common::getMenuDetail($menuname, $delta, $cateid);
        $_news = Business_Ws_NewsItem::getInstance();
        $list = $_news->getList($newsid=$delta, $cateid);
        foreach($list as &$store) {
            //shorten address
            $_title = $store["title"];
            $_titles = explode("-", $_title);
            $phone = $_titles[1];
            $adds = explode(",", $_titles[0]);
            $short_add = $adds[0] . ", " . $adds[2];
            $store["short2"] = $short_add . "<br />" . $phone;

            $titles = explode(" ", $store["title"]);
            $store["add"] = $titles[0];
        }
        return $list;
    }

    public static function getStoreDetail($name) {
        $list = array(
            "vote_1047"=>"203"
            ,"vote_191"=>"20"
            ,"vote_206"=>"28"
            ,"vote_253"=>"26"
            ,"vote_294"=>"24"
            ,"vote_301"=>"21"
            ,"vote_370"=>"15"
            ,"vote_492"=>"23"
            ,"vote_654"=>"16"
            ,"vote_774"=>"17"
    //	    ,"vote_778"=>"19"
            ,"vote_48"=>"783"
            ,"vote_776"=>"19"
            ,"vote_67"=>"18"
            ,"vote_148"=>"13"
            ,"vote_89"=>"12"
            ,"vote_112"=>"322"
            ,"vote_40"=>"622"
        );
        return $list[$name];
    }
    
    public function getListStock($stock){
        $menuname = 'menu_shop';
        Business_Helpers_Common::getMenuDetail($menuname, $delta, $cateid);
        $_news = Business_Ws_NewsItem::getInstance();
        $list = $_news->getList($newsid=$delta, $cateid);
        $stock = json_decode($stock);        

        //only check company products
        foreach($list as &$item){
            
            //shorten address
            $_title = $item["title"];
            $_titles = explode("-", $_title);
            $phone = $_titles[1];
            $adds = explode(",", $_titles[0]);
            $short_add = $adds[0] . ", " . $adds[2];
            $item["short2"] = $short_add . "<br />" . $phone;

            $_key = "i".$item['itemid'];
            
            if (strpos($stock->congty->$_key, "++0")!== FALSE){ //in instock
                $item['hasproducts'] = 0;
            }else{
                $item['hasproducts'] = 1;
            }
            
            $add = explode(" ",$item['title']);
            switch($add[0]){
                case '43':
                    $item['short'] = '<span class="bold blue">43 TQK</span>';
                    break;
                case '89':
                    $item['short'] = '<span class="bold blue">89 TQK</span>';
                    break;
                case '148':
                    $item['short'] = '<span class="bold blue">148 NCT</span>';
                    break;
                case '370A':
                    $item['short'] = '<span class="bold blue">370A LVS</span>';
                    break;
                case '774':
                    $item['short'] = '<span class="bold blue">774 NT</span>';
                    break;
                case '654':
                    $item['short'] = '<span class="bold blue">654 LHP</span>';
                    break;
                case '778':
                    $item['short'] = '<span class="bold blue">778 CMT8</span>';
                    break;
                case '191':
                    $item['short'] = '<span class="bold blue">191 K.Hội</span>';
                    break;
                case '294':
                    $item['short'] = '<span class="bold blue">294 B.Đằng</span>';
                    break;
                case '235':
                    $item['short'] = '<span class="bold blue">235 Q.Trung</span>';
                    break;
            }
            
        }
        return $list;
        
    }
    
    
    public function buildList($pDetail, $list){
        
        $_store = $pDetail['store'].'';
        $_products = Business_Ws_ProductsItem::getInstance();
        
        if ($_store != '' && $_store != 'null'){
            
            $_store = json_decode($_store);
            
            $_total_store = count($list);
            
            if (count((array)$_store->hnam)<$_total_store){
                foreach($list as $sItem){
                    $sItemId = $sItem['itemid'];
//                    $_storeHnam = $_store['hnam'];                    
//                    $_storeCongty = $_store['congty'];                                        
                    $_id = "i".$sItemId;
                    if (!isset($_store->hnam->$_id)){
                        $_temp = explode(" ",$sItem['title']);
                        $shortTitle = $_temp[0];
                        $_store->hnam->$_id = "$shortTitle++1";
                        $_store->congty->$_id = "$shortTitle++1";
                    }
                }
            }
            
            $_store = json_encode($_store);
            
            $pDetail['store'] = $_store;
//            $_products->update($pDetail['itemid'], $pDetail['productsid'], $pDetail['cateid'], $pDetail);

            
        }else{
            
            //insert store to list
            foreach($list as $item){
                $_temp = explode(" ",$item['title']) ;
                $shortTitle = $_temp[0];
                $array['hnam']["i".$item['itemid']] = $shortTitle.'++1';
                $array['congty']["i".$item['itemid']] = $shortTitle.'++1';
            }
            
            $array = json_encode($array);
            $pDetail['store'] = $array;
            
//            $_products->update($pDetail['itemid'], $pDetail['productsid'], $pDetail['cateid'], $pDetail);
            $_store = $array;
        }
        
        return $_store;
    }
    
 }
?>

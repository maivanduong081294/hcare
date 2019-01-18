<?php
class Business_Helpers_ProductsDetail
{
    private static $_instance = null;
    // module news to store
    
    function __construct()
    {

    }

    /**
     * get instance of Business_Helpers_ProductsDetail
     *
     * @return Business_Helpers_ProductsDetail
     */
    public static function getInstance()
    {
            if(self::$_instance == null)
            {
                    self::$_instance = new Business_Helpers_ProductsDetail();
            }
            return self::$_instance;
    }

    public function getColorDetailByID($colorid){
        $_news = Business_Ws_NewsItem::getInstance();
        $detail = $_news->getDetail($colorid);
        if (count($detail)>0)
            return $detail;
        return null;
    }
    
    public static function getMenuname($cateid){
        $_menu = Business_Ws_MenuItem::getInstance();

        $menu = $_menu->getDetailById($cateid);
        return $menu['menuname'];
    }

    public function getColorCodeById($colorid){
        $_news = Business_Ws_NewsItem::getInstance();

        $detail = $_news->getDetail($colorid);
        $colorcode = $detail['title'];
        return $colorcode;
    }

    public function getProductsCode($itemid, $colorid){
        $_news = Business_Ws_NewsItem::getInstance();
        
        $detail = $_news->getDetail($colorid);
        $colorname = $detail['title'];
        $_products = Business_Ws_ProductsItem::getInstance();
        $pdetail = $_products->getDetail($itemid);
        $pcode = $pdetail['productscode'];
        
        return $pcode.$colorname;

    }

    public function getColorBySizeAndItemid($itemid, $sizeid){
        $_productsdetail = Business_Addon_ProductsDetail::getInstance();
        $_news = Business_Ws_NewsItem::getInstance();
        $plist = $_productsdetail->getListColorBySizeAndItemid($itemid, $sizeid);

        $result = array();
        if(count($plist)>0){
            foreach($plist as $item){
                $colorid = $item['colorid'];
                $colordetail = $_news->getDetail($colorid);
                $result[$colorid] = array("name"=>$colordetail['title'],"thumb"=>json_decode($colordetail['thumb'])->thumb1);
            }
        }

        return $result;
    }


    public function getColorListByPitemid($itemid){
        $_productsdetail = Business_Addon_ProductsDetail::getInstance();
        $_news = Business_Ws_NewsItem::getInstance();

        $plist = $_productsdetail->getListColorByItemid($itemid);

        $result = array();
        if(count($plist)>0){
            foreach($plist as $item){
                $colorid = $item['colorid'];
                $colordetail = $_news->getDetail($colorid);
                $result[$colorid] = array("name"=>$colordetail['title'],"thumb"=>json_decode($colordetail['thumb'])->thumb1);
            }
        }

        return $result;
    }

    public function getSizeListByPitemid($itemid){
        $_productsdetail = Business_Addon_ProductsDetail::getInstance();
        $_news = Business_Ws_NewsItem::getInstance();

        $plist = $_productsdetail->getListByItemid($itemid);

        $result = array();
        $pos = 0;
        if(count($plist)>0){
            foreach($plist as $item){
                $sizeid = $item['sizeid'];
                $sizedetail = $_news->getDetail($sizeid);
                if ($pos++ == 0)
                    $result[$sizeid] = array("name"=>$sizedetail['title'],"active"=>"selected='selected'","price"=>number_format($item['price']),"weight"=>$item['weight']);
                else
                    $result[$sizeid] = array("name"=>$sizedetail['title'],"active"=>"","price"=>number_format($item['price']),"weight"=>$item['weight']);

            }
        }

        return $result;
    }

    public static function getCatename($cateid){
        $_menuitem = Business_Ws_MenuItem::getInstance();
        $detail = $_menuitem->getDetailById($cateid);
        if (count($detail)>0)
            return $detail['title'];
        else
            return "danh-muc";
    }

 }
?>

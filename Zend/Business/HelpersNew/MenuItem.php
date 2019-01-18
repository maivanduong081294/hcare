<?php

class Business_HelpersNew_MenuItem
{
    private static $_instance = null;
    
    function __construct()
    {}

    /**
     * get instance of Business_HelpersNew_MenuItem
     *
     * @return Business_HelpersNew_MenuItem
     */
    public static function getInstance()
    {
        if (self::$_instance == null) {
          self::$_instance = new Business_HelpersNew_MenuItem();
        }
        return self::$_instance;
    }

      public static function getMenuDetail($listMenuItem)
    {   
        $data = array();
        $check=true;
        if (count($listMenuItem) > 0)
        {
            foreach ($listMenuItem as $key => $item) {
                if(!is_array($item))
                {
                    $check=false; // chi co 1 mang
                    break;
                }
                if ($key == 0) {
                    $data['cateid'] = $item['itemid'];
                    $data['delta'] = $item['delta'];
                }else
                $data['cateid'] = $item['itemid'] . ',' . $data['cateid'];
            }
        }
        
        if($check==false)
        {
            $data['cateid'] = $listMenuItem['itemid'];
            $data['delta'] = $listMenuItem['delta'];
        }
        return $data;
    }
}

?>

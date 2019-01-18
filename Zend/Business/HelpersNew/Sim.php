<?php

class Business_HelpersNew_Sim
{

    private static $_instance = null;

    public function __construct()
    {}

    /**
     * get instance of Business_HelpersNew_Sim
     *
     * @return Business_HelpersNew_Sim
     */
    public static function getInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new Business_HelpersNew_Sim();
        }
        return self::$_instance;
    }


    public function heperSim($sim)
    {
        foreach ($sim as $key => $val) {
        if($val['network']==1)
       {
            if(strlen($val["title"])<11) 
              $sim[$key]['price']=  '69.000 đ'; 
            else 
              $sim[$key]['price']=  '49.000 đ' ;  

            $sim[$key]['loai']=  'Mobifone' ;  

       }else
       {
        

            if(strlen($val["title"])<11) 
             {      
                  if(strpos(substr($val["title"], 0,2), '08')!==false)  
                  $sim[$key]['price']=  '99.000 đ' ;  
                  if(strpos(substr($val["title"], 0,2), '09')!==false)  
                  $sim[$key]['price']=  '199.000 đ' ;  
             }
            else 
            {

                 $sim[$key]['price']=  '69.000 đ'; 

            }


            $sim[$key]['loai']=  'Viettel' ;  
       }
    

        }

        return $sim;
    }

  




}

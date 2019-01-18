<?php

class Business_HelpersNew_Products
{

    private static $_instance = null;

    public function __construct()
    {}

    /**
     * get instance of Business_HelpersNew_Products
     *
     * @return Business_HelpersNew_Products
     */
    public static function getInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new Business_HelpersNew_Products();
        }
        return self::$_instance;
    }

    public static function dequyPid($pid)
    {
        $_menu           = Business_WsNew_MenuItem::getInstance();
        $_productsHelper = Business_HelpersNew_Products::getInstance();
        $menuPidItem     = $_menu->getDetail($pid);
        $pid             = $_productsHelper->buildLinkbreadcrumbListCate($menuPidItem);
        return $pid;
    }

    public static function dequysubitem($pid)
    {
        $_menu             = Business_WsNew_MenuItem::getInstance();
        $_productsHelper   = Business_HelpersNew_Products::getInstance();
        $menuItem['delta'] = 4;
        $menuSubItem       = $_menu->getSubMenu($pid);
        $subItem           = array();
        foreach ($menuSubItem as $key => $val) {

            if (strpos($val['title'], 'khác') !== false) {
                $titleKhac = $val['title'];
                if($val['title_sub']!='')
                $titleKhac = $val['title_sub'];
                $linkKhac  = SEOPlugin::getAccesoriesLink($val['itemid'], Business_Common_Utils::adaptTitleLinkURLSEO($titleKhac));
            } else {
                $subItem[$key]['title'] = $val['title'];
                if($val['title_sub']!='')
                $subItem[$key]['title']  = $val['title_sub'];
                $subItem[$key]['link']  = SEOPlugin::getAccesoriesLink($val['itemid'], Business_Common_Utils::adaptTitleLinkURLSEO($subItem[$key]['title']));
            }
        }
        if ($titleKhac != '') {
            $key                    = count($subItem) + 1;
            $subItem[$key]['title'] = $titleKhac;
            $subItem[$key]['link']  = $linkKhac;
        }
        return $subItem;
    }

    public static function getIdSubProperties($list, $val)
    {
        foreach ($list as $key => $data) {
            if (SEOPlugin::removeTiengViet($data['name']) == $val) {
                return '"' . $data['id'] . '"';
            }

        }
    }

    public static function getNameSubProperties($list, $val)
    {
        foreach ($list as $key => $data) {
            if (SEOPlugin::removeTiengViet($data['name']) == $val) {
                return $data['name'] ;
            }

        }
    }



    public static function calculateInstallmentPrice($price, $percent)
    {
        if ($price <= 0) {
            return 0;
        }

        return ($percent * $price) / 100;
    }


 public static function getOnstockSub($flag=0)
    {

        if($flag==1)
        {
            return  array(
                1 => 'Tin đồn',
                2 => 'Đặt hàng',
                0 => 'Đặt hàng',
            );

        }else
        {
            return  array(
                1 => 'Tin đồn',
                2 => 'Sắp ra mắt',
                0 => 'Đặt hàng',
            );            
        }

    }


   public static function getOnstock($flag=0)
    {
        if($flag==1)
        {
        return  array(
            0 => 'Hết hàng(thời gian dài có lại)', // thời gian dài có lại
            1 => 'Có hàng',
            2 => 'Sắp có hàng',
            3 => 'Tạm hết hàng (vài ngày sau có lại)', // vài ngày sau có lại
            4 => 'Tạm hết hàng (vài tháng sau có lại)', // 1,2 tháng sau có lại
            5 => 'Ngừng kinh doanh',
        );
        }
        else if($flag==2){
            return  array(
                ''=>'Tất cả',
                0 => 'Hết hàng(thời gian dài có lại)', // thời gian dài có lại
                1 => 'Có hàng',
                //2 => 'Sắp có hàng',
                3 => 'Tạm hết hàng (vài ngày sau có lại)', // vài ngày sau có lại
                4 => 'Tạm hết hàng (vài tháng sau có lại)', // 1,2 tháng sau có lại
                5 => 'Ngừng kinh doanh',
                6 => 'Tin đồn',
                7 => 'Sắp ra mắt',
                8 => 'Sắp có hàng',                
            );
        }else
        {
        return  array(
            0 => 'Hết hàng', // thời gian dài có lại
            1 => 'Có hàng',
            2 => 'Sắp có hàng',
            3 => 'Tạm hết hàng', // vài ngày sau có lại
            4 => 'Tạm hết hàng', // 1,2 tháng sau có lại
            5 => 'Ngừng kinh doanh',
        );
        }
    }
    public static function propetiesHelp($idGroup,&$propeties)
    {

                        // pin sac du phong 293 | loại 312 313 314 | Tính năng 359 360 361 456 815
                        $click= array(312,313,314,359,360,361,456,815);
                        foreach ($click as $value) {
                                $dataClick[$value]=293;
                        }

                         // check properties tương ứng
                        foreach ($propeties as $key => $value) {
                               if($dataClick[$value['id']]==$idGroup)
                                $kq[$value['id']]=$value;
                            }  

                        // cáp sạc 294 | loại 315 316 317 318 319 320 | Tính năng 362 363 364 365
                        $click= array(315,319,320,362,363);
                        foreach ($click as $value) {
                            $dataClick[$value]=294;
                        }
                        $click= array(316,317,318,426,364,365);
                        foreach ($click as $value) {
                            $dataClick[$value]=917;
                        }
                       // check properties tương ứng
                        foreach ($propeties as $key => $value) {
                               if($dataClick[$value['id']]==$idGroup)
                                $kq[$value['id']]=$value;
                            }  

                        // tai nghe 295 | loại 321 322 | Tính năng 366 367 368
                       $click= array(321,322,366, 367 ,368);
                        foreach ($click as $value) {
                                $dataClick[$value]=295;
                        }
                       // check properties tương ứng
                        foreach ($propeties as $key => $value) {
                               if($dataClick[$value['id']]==$idGroup)
                                $kq[$value['id']]=$value;
                            }  

                        // the nho va usb 296 | loại 323 324 325 444 | Tính năng 369 370 371 372 373
                       $click= array(323,324,325, 369 ,370 ,371, 372 ,373 , 444);
                        foreach ($click as $value) {
                                $dataClick[$value]=296;
                        }

                        // check properties tương ứng
                        foreach ($propeties as $key => $value) {
                               if($dataClick[$value['id']]==$idGroup)
                                $kq[$value['id']]=$value;
                            }  


                        // pk laptop 297 | loại 326 327 328 | Tính năng 
                       $click= array(326,327,328);
                        foreach ($click as $value) {
                                $dataClick[$value]=297;
                        }

                      // check properties tương ứng
                        foreach ($propeties as $key => $value) {
                               if($dataClick[$value['id']]==$idGroup)
                                $kq[$value['id']]=$value;
                            }  

                        // loa di dong 298 | loại 329 330 331 | Tính năng 
                       $click= array(329,330,331);
                        foreach ($click as $value) {
                                $dataClick[$value]=298;
                        }

                        // check properties tương ứng
                        foreach ($propeties as $key => $value) {
                               if($dataClick[$value['id']]==$idGroup)
                                $kq[$value['id']]=$value;
                            }  


                        // dong ho thong minh 299 | loại 332 333 334 | Tính năng 
                       $click= array(332,333,334);
                        foreach ($click as $value) {
                                $dataClick[$value]=299;
                        }

                     // check properties tương ứng
                        foreach ($propeties as $key => $value) {
                               if($dataClick[$value['id']]==$idGroup)
                                $kq[$value['id']]=$value;
                            }  

                        // dan cuogn luc dan man hinh 300 | loại 335 336  453 454 | Tính năng 374 375  437 438
                       $click= array(335,336,374, 375, 437,438,453, 454,839 );
                        foreach ($click as $value) {
                                $dataClick[$value]=300;
                        }

                        // check properties tương ứng
                        foreach ($propeties as $key => $value) {
                               if($dataClick[$value['id']]==$idGroup)
                                $kq[$value['id']]=$value;
                            }  

                        // op lung dien thoai 301| loại 337 338 339 340  | Tính năng  377 378 379 380 445 446 447 457 458 465
                       $click= array(337,338,339, 340,377, 378 ,379 ,380, 445, 446, 447 ,457 ,458,465);
                        foreach ($click as $value) {
                                $dataClick[$value]=301;
                        }

                        // check properties tương ứng
                        foreach ($propeties as $key => $value) {
                               if($dataClick[$value['id']]==$idGroup)
                                $kq[$value['id']]=$value;
                            }  


                        // bao da may tinh bang 302| loại  342 381 382 | Tính năng  439 440 441 442
                       $click= array(342,381 ,382,439 ,440 ,441 ,442);
                        foreach ($click as $value) {
                                $dataClick[$value]=302;
                        }   

                        // check properties tương ứng
                        foreach ($propeties as $key => $value) {
                               if($dataClick[$value['id']]==$idGroup)
                                $kq[$value['id']]=$value;
                            }  


                        // do choi doc la 303| loại 343 344 345 346 347 348  | Tính năng  
                       $click= array(343, 344, 345, 346, 347 ,348 );
                        foreach ($click as $value) {
                                $dataClick[$value]=303;
                        }               

                        // check properties tương ứng
                        foreach ($propeties as $key => $value) {
                               if($dataClick[$value['id']]==$idGroup)
                                $kq[$value['id']]=$value;
                            }  


                        // phu kien apple 304| loại 349 350 351 352 353 354 355 356 357 358 | Tính năng  
                       $click= array(349, 350, 351, 352, 353, 354, 355, 356, 357 ,358 );
                        foreach ($click as $value) {
                                $dataClick[$value]=304;
                        }      
                        
                        // Flash sale 459| loại | Tính năng  
                       $click= array( );
                        foreach ($click as $value) {
                                $dataClick[$value]=459;
                        }            

                        // check properties tương ứng
                        foreach ($propeties as $key => $value) {
                               if($dataClick[$value['id']]==$idGroup)
                                $kq[$value['id']]=$value;
                            }  

                         $propeties = $kq;


    }
    public static function productsHelp(&$products, $video,$giamgia)
    {
        foreach ($products as $key => $val) {

			//them trường toàn diện, bao gồm nguồn, màn hình...
			//áp dụng cho: 
			//1. Điện thoại mới + hnam + #apple
			//2. Điện thoại cũ: tất cả
			$wtext = 'toàn diện, nguồn, pin, màn hình';
            $wtext1 = 'chính hãng';
            $wtext2 = ', theo chuẩn Apple';
			if ($val['cateid'] == 53) {
				//điện thoại cũ
				$products[$key]['warranty'] = $val['warranty'] . " " . $wtext;
			} else {
				//điện thoại mới, hàng hnam và khác nhóm apple
				if ($val['is_apple']==0 && $val['price']>0 && $val['productsid']==3) {
					$products[$key]['warranty'] = $val['warranty'] . " " . $wtext;
				}
                //điện thoại mới, hàng cty và khác nhóm apple và wacth samsung 
                if ( ($val['is_apple']==0 && $val['original_price']>0 && $val['productsid']==3) or $val['cateid']==786 ) {
                    $products[$key]['warranty'] = $val['warranty'] . " " . $wtext1;
                }
                // laptop macbook và wacth apple 
                if ( ($val['is_apple']==1 && $val['price']>0 && $val['productsid']==6) or $val['cateid']==785 ) {
                    $products[$key]['warranty'] = $val['warranty'] . " " . $wtext2;
                }


			}
			
            // bo note : * Sản Phẩm chỉ bán tại chi nhánh 778 CMT8 Q.Tân Bình 
            if(strpos( $products[$key]["note"] , '778 CMT8')!==false)
                $products[$key]["note"]='';
            

			if($_REQUEST['d']=='chuong-trinh')
			{
				if($products[$key]["itemid"]==8157)
				{
					$products[$key]["original_price"]=$val['original_price']=6999000;
				}
			}
			
            // nước hoa giảm giá 10%  19/6 - 19/7
/*            if($products[$key]["cateid"]==952 and $giamgia==1 and 1==2)
            {
              $products[$key]['oldprice'] =$val['oldprice']=$products[$key]['price'];
              $products[$key]['price'] =$val['price']=$products[$key]['price'] - ( $products[$key]['price'] * 0.1);
            }
            // giảm giá tri an 30%
            if($giamgia=='trian' and $products[$key]["productsid"] ==4)
            {
              $products[$key]['oldprice'] =$val['oldprice'];
              $products[$key]['price'] =$val['price']=$products[$key]['oldprice'] - ( $products[$key]['oldprice'] * 0.3);
            }else
            {
                if($products[$key]['price']==$products[$key]['oldprice'] )
                    $products[$key]['oldprice'] =$val['oldprice']=0;
            }*/

			if ($products[$key]["productsid"] == 4)
              $url   = Globals::getCDNUrl(3);
            else
              $url   = Globals::getCDNUrl(2);
            $products[$key]['image']        = $url . "/v5/images/noimage.jpg";
            if($val['oldprice'] == $val['price']   or  $val['oldprice'] == $val['original_price']  ){
                $val['oldprice'] = 0;
            }
            $products[$key]['giathitruong'] = $val['oldprice'];

            if ($products[$key]["productsid"] == 3) {
                $products[$key]["loaisanpham"] = 'Điện thoại';
            }

            if ($products[$key]["productsid"] == 4) {
                $products[$key]["loaisanpham"] = 'Phụ kiện';
            }

            if ($products[$key]["productsid"] == 5) {
                $products[$key]["loaisanpham"] = 'Máy tính bảng';
            }

            if ($products[$key]["productsid"] == 6) {
                $products[$key]["loaisanpham"] = 'Laptop';
            }

            if ($products[$key]["productsid"] == 8) {
                $products[$key]["loaisanpham"] = 'Đồng hồ thông minh';
            }

            if ($products[$key]["productsid"] == 9) {
                $products[$key]["loaisanpham"] = 'Nước hoa';
            }

            $_menu                     = Business_WsNew_MenuItem::getInstance();
            $menu                      = $_menu->getDetail($products[$key]["cateid"]);
            $menuPid ="";
            if( $menu['pid'] >0)
            $menuPid = $_menu->getDetail($menu['pid']);
            //var_dump( $menuPid ); exit;
            $products[$key]["danhmuc"] = $menuPid['title'] ." ".$menu['title'];

            // gói hỏa tốc 60 phút apple cty + apple hname + hnam sony black
            $products[$key]["hoatoc"] = false;

            if ($video == 1) {
                $class = 'numberprice';
            }

            if ($video == 2) {
                $class = 'numberprice';
            }
            // hàng apple được giao hỏa tốc 60'

            if ($products[$key]["productsid"] == 3 or $products[$key]["productsid"] == 5) // nếu là dt / máy tính bảng  hnam va cty
            {
                if (strpos(strtolower($products[$key]["title"]), 'apple') !== false) {
                    $products[$key]["hoatoc"] = true;
                }
            }

        
         // set hnam 
        if (in_array($products[$key]["itemid"],array(14357,14362,14356, 14363))) 
        {
            $products[$key]["ishnam"] = true;
        }


        // ====================== hàng Hnam
        if ($val['price'] > 0 or $val['price'] > $val['original_price'] or $products[$key]["ishnam"]) {
            $products[$key]['prefixPrice']  ='HNAM';
            //============= giảm giá đồng lọt 15% cho phụ 
			/*
          $loaitru=array(10439,10990,10989,11497,11556,11496,10983,10986,11495,10984,10988,10979,10982,10985,10976,10987,10981,10978,10975,10977,10980,11031);  
          if ($products[$key]["productsid"] == 4  and !in_array($products[$key]['itemid'], $loaitru)) // nếu là phụ kiện
            {
                $giam15=  $val['price']*0.15;
                //======= nếu old price >0
                if( $val['oldprice']>0)
                {
                     $val['price']=  $val['price']-$giam15;

                }
                else
                {
                    $val['oldprice']=$val['price'];
                    $val['price']=  $val['price']-$giam15;

                }				
           }
*/

            $__price                       = $val['price'];
            if (in_array($products[$key]["cateid"],array(977))) // iphong 8 , iphone x
				//$giadukien= " Dự kiến: ";
				$giadukien= "";
            else
            $giadukien ="";
            $products[$key]['cost']        = self::numberformat("<font color=#ff5400>$giadukien HNAM <font class='$class'>", $val['price'], 'đ') . "</font></font>";

            $products[$key]['cost_detail'] = self::numberformat("<font color=#ff5400>$giadukien HNAM <font class='$class'>", $val['price'], 'đ') . "</font></font>";
            $products[$key]['cost1']       = $val['price'];
            $products[$key]['cost2']       = self::numberformat("HNAM ", $val['price'], 'đ');
            $products[$key]['cost3']       = self::numberformat("", $val['price'], 'đ');
            $products[$key]['cost4']       = self::numberformat("", $val['price'], '');
            $products[$key]['costfb']       =  $val['price']." VND";
            $products[$key]["ishnam"]      = true;
            $products[$key]["loaihang"]    = '<font color=#ff5400>Hàng Hnam</font>';
            $products[$key]["doitra"]      = '15ngay';

            if ($products[$key]["productsid"] == 4) // nếu là phụ kiện
            {
                $products[$key]["doitra"] = '30ngay';
                if (strpos(strtolower($products[$key]["title"]), 'apple') !== false) {
                    if (self::strpos_array($products[$key]["title"], array(
                        'Pencil',
                        'TV',
                        'Mouse',
                        'Airpods',
                    )) !== false and !in_array($val["itemid"], array(11756))) {
                        $products[$key]["doitra"] = ''; // Apple Pencil, Apple TV, Apple Mouse
                        $products[$key]["hoatoc"] = true;
                    }
                } else {
                  
                }
                // phụ kiện còn lại
            }
            if ($products[$key]["productsid"] == 6) // nếu là laptop
            {
                if (strpos(strtolower($products[$key]["title"]), 'macbook') !== false) {
                    $products[$key]["doitra"] = ''; // Macbook
                    $products[$key]["hoatoc"] = true;
                }
            }
            if ($products[$key]["productsid"] == 8) // nếu là đồng hồ thông minh
            {
                $products[$key]["doitra"] = '15ngay';
            }
            if ($products[$key]["productsid"] == 9) // nếu là nước hoa
            {           
                 $products[$key]["hoatoc"] = true;             
            }
            // ===== tính phí trả góp
            $val["installment"]  = self::calculateInstallmentPrice($val["price"], 10);
            $val["installment2"] = self::calculateInstallmentPrice($val["price"], 20);
            $val["installment3"] = self::calculateInstallmentPrice($val["price"], 30);
            $val["installment4"] = self::calculateInstallmentPrice($val["price"], 40);
            $val["installment5"] = self::calculateInstallmentPrice($val["price"], 50);
            $val["installment6"] = self::calculateInstallmentPrice($val["price"], 60);
            $val["installment7"] = self::calculateInstallmentPrice($val["price"], 70);
        } // ====================== hàng Cty
        else {

            $products[$key]['prefixPrice']  ='CTY';
            $__price                = $val['original_price'];
            $products[$key]['cost'] = self::numberformat("<font color=#3289ff>CTY <font class='numberprice'>", $val['original_price'], 'đ') . "</font></font>";
            if ($val["cateid"] == 53) // máy cũ
            {
                $products[$key]['cost_detail'] = self::numberformat("<font color=#3289ff>CTY <font class='numberprice'>", $val['original_price'], 'đ') . "</font></font>";
            } else {
				if ($val['itemid']==12281) {
					//fix note 8 giá text 
					$products[$key]['cost_detail'] = self::numberformat("<font color=#3289ff>CTY <font class='numberprice'>", $val['original_price'], 'đ') . "</font></font><font style='color: #5d5a5a;font-size: small;'> ( Đã có V.A.T )</font>";					
				} else {	
					$products[$key]['cost_detail'] = self::numberformat("<font color=#3289ff>CTY <font class='numberprice'>", $val['original_price'], 'đ') . "</font></font><font style='color: #5d5a5a;font-size: small;'> ( Đã có V.A.T )</font>";
				}            }

            $products[$key]['cost1']    = $val['original_price'];
            $products[$key]['cost2']    = self::numberformat("CTY ", $val['original_price'], 'đ');
            $products[$key]['cost3']    = self::numberformat("", $val['original_price'], 'đ');
            $products[$key]['cost4']    = self::numberformat("", $val['original_price'], '');
            $products[$key]['costfb']       =  $val['original_price']." VND";
            $products[$key]["ishnam"]   = false;
            $products[$key]["loaihang"] = '<font color="#3289ff">Hàng Công Ty</font>';
            $products[$key]["doitra"]   = '30ngay';

            // get gói hỏa tốc
            if ($products[$key]["cateid"] != 53) {
                if (strpos(strtolower($products[$key]["title"]), 'apple') !== false or strpos(strtolower($products[$key]["title"]), 'macbook') !== false) {
                    $products[$key]["hoatoc"] = true;
                }

                if (in_array($products[$key]["cateid"], array(185, 45))) {
                    $products[$key]["hoatoc"] = true;
                }

            }

            if ($products[$key]["productsid"] == 4) // nếu là phụ kiện
            {
                $products[$key]["doitra"] = '30ngay';
                if (strpos($products[$key]["title"], 'Apple') !== false) {
                    if (self::strpos_array($products[$key]["title"], array(
                        'Pencil',
                        'TV',
                        'Mouse',
                        'Airpods',
                    )) !== false) {
                        $products[$key]["doitra"] = ''; // Apple Pencil, Apple TV, Apple Mouse
                        $products[$key]["hoatoc"] = true;
                    }
                } else {
               
                }
                // phụ kiện còn lại
            }
            if ($products[$key]["productsid"] == 6) // nếu là laptop
            {
                if (strpos(strtolower($products[$key]["title"]), 'macbook') !== false) {
                    $products[$key]["doitra"] = ''; // Macbook
                }
            }
            if ($products[$key]["productsid"] == 8) // nếu là đồng hồ thông minh
            {
                $products[$key]["doitra"] = '15ngay';
            }

            // === tính phí trả gópp
            $val["installment"]  = self::calculateInstallmentPrice($val["original_price"], 10);
            $val["installment2"] = self::calculateInstallmentPrice($val["original_price"], 20);
            $val["installment3"] = self::calculateInstallmentPrice($val["original_price"], 30);
            $val["installment4"] = self::calculateInstallmentPrice($val["original_price"], 40);
            $val["installment5"] = self::calculateInstallmentPrice($val["original_price"], 50);
            $val["installment6"] = self::calculateInstallmentPrice($val["original_price"], 60);
            $val["installment7"] = self::calculateInstallmentPrice($val["original_price"], 70);
        }
        // ====================== giá thị trường
        $products[$key]['oldprice'] = self::numberformat('', $val['oldprice'], 'đ');
        // ====================== % down price
        if ($val["oldprice"] > 0) {
            $remain                    = $val["oldprice"] - $__price;
            $__percent                 = $remain * 100 / $val["oldprice"];
            $products[$key]['percent'] = (int) $__percent;
        }
        // ====================== giảm thêm or đã giảm
        if ($val['pricebeforediscount'] > 0) {
            $products[$key]['pricedown'] = self::numberformat(" Giảm thêm ", $val['pricebeforediscount'], 'đ');
        } else {
            if ($val['pricebeforediscount2'] > 0) {
                $products[$key]['pricedown'] = self::numberformat(" Đã giảm ", $val['pricebeforediscount2'], 'đ');
            }

        }
        // ====================== dual sim if ($val['dualsim'] > 0)
        // ====================== title Seo
        $titleSEO               = Business_Common_Utils::adaptTitleLinkURLSEO($val['title_seo']);
        $products[$key]['link'] = self::getLink($val, $titleSEO);
        // ====================== Image
        $thumb  = json_decode($val['thumb']);
        $thumb2 = json_decode($thumb->thumb2);
        // =====================get list thumb slide show
        $products[$key]["img_home"] = $url . "uploads/img_home/" . $val['img_home'];
        foreach ($thumb2 as $t) {
            if ($t != null) {
                if ($val['productsid'] == 4 || $val['productsid'] == 8 || $val['productsid'] == 9) {
                    $products[$key]["thumb_detail"][] = $url . "uploads/accesories/details/" . $t;
                } else {
                    $products[$key]["thumb_detail"][] = $url . "uploads/products/details/" . $t;
                }
            }
        }
        // ======================
        if ($thumb2[0] != '') {
            if ($val['productsid'] == 4 or $val['productsid'] == 8 || $val['productsid'] == 9) {
                $products[$key]['image_goc']     = $url . "uploads/accesories/details/" . $thumb2[0];
                $products[$key]['image']     = $url . "rsuploads/accesories/details/home/" . $thumb2[0];
                $products[$key]['imageHome'] = $url . "rsuploads/accesories/details/home/" . $thumb2[0];
            } else {
                $products[$key]['image_goc']     = $url . "uploads/products/thumbnails/" . $thumb2[0];
                $products[$key]['image']     = $url . "rsuploads/products/thumbnails/home/" . $thumb2[0];
                $products[$key]['imageHome'] = $url . "rsuploads/products/details/" . $thumb2[1];
            }
        }
        // ====================== KM
        if ($val["bonus_company_full"] != "") {
            $products[$key]['km'] = $val["bonus_company_full"];
        } else {
            $products[$key]['km'] = $val["bonus_hnam"];
        }
        $products[$key]['km'] = preg_replace("/<img[^>]+\>/i", "&bull;", $products[$key]['km'] ); 
        // -------------------update link fullbox
        $products[$key]["fullboxpic_img"] = Globals::getStaticUrl()."v4/fullbox/" . $val["itemid"] . ".jpg";
        // -------------------update onstock
        $tempOnstock = $products[$key]['onstock'];
        if( $products[$key]['enabled'] and $products[$key]['enabled'] === 0)
              $products[$key]['onstock'] = 0;
        switch ($tempOnstock) {
            case 0: // Hết hàng thời gian dài có lại
                $products[$key]["status"]     = 'Hết hàng';
                $products[$key]["buy_online"] = false;
                break;
            case 1:  // Có hàng
                $products[$key]["status"]     = 'Đang có hàng';
                $products[$key]["buy_online"] = true;
                break;
            case 2:   // sắp có hàng
                if ($__price > 0) {
                    $products[$key]["status"]     = 'Đặt hàng ngay';
                    $products[$key]["buy_online"] = true;
                } else {
                    $products[$key]["status"]     = 'Sắp ra mắt';
                    $products[$key]["buy_online"] = false;
                }

                break;
            case 3: // tạm hết hàng vài ngày sau có lại
                $products[$key]["status"]     = 'Tạm hết hàng';
                $products[$key]["buy_online"] = false;
                  break;
            case 4:  // tạm hết hàng 1 , 2 tháng sau có lại
                $products[$key]["status"]     = 'Tạm hết hàng';
                $products[$key]["buy_online"] = false;
                  break;
            case 5:  // ngừng kinh doanh
            $products[$key]["status"]     = 'Ngừng kinh doanh';
            $products[$key]["buy_online"] = false;
                break;
        }

        if ($val["cateid"] == 53) // đặt hàng ngay máy cũ
        {
            $products[$key]["ishnam"] = true;
            $products[$key]["hoatoc"] = false;
        }
        // ==================================== link tra gop
        $products[$key]["payment_link"] = SEOPlugin::getProductDetailLink($val["itemid"], $titleSEO) . "/mua-tra-gop";
        // ==========================trung tam bao hanh chinh hang
        $products[$key]["wlink"] = self::getWarrantyLinkV2($val["cateid"]);

        // ======================= trả trước 0 đồng
        if (in_array($val["itemid"], array(
            7839,
        ))) {
            // tra truoc 0đ
            $products[$key]["installment_text"] = "0đ";
        } else {
            $products[$key]["installment"]       = $val["installment"];
            $products[$key]["installment_text"]  = number_format($val["installment"]) . "đ";
            $products[$key]["installment_text2"] = number_format($val["installment2"]) . "đ";
            $products[$key]["installment_text3"] = number_format($val["installment3"]) . "đ";
            $products[$key]["installment_text4"] = number_format($val["installment4"]) . "đ";
            $products[$key]["installment_text5"] = number_format($val["installment5"]) . "đ";
            $products[$key]["installment_text6"] = number_format($val["installment6"]) . "đ";
            $products[$key]["installment_text7"] = number_format($val["installment7"]) . "đ";
        }
        // ========================================================
        if ($video == 1) {
            $products[$key]["video"] = self::helpVideo($val["video"], $val["title"]);
            $videos= explode(',', $val["video"]);
            $products[$key]["idvideo"] = self::helpVideo1($videos[0]);
        }

        // ======================================================== get text box
        $_option     = Business_WsNew_Option::getInstance();
        $arrayItemid = array(
            $val["itemid"],
        );
        $arrayOption = array(
            'txt_box',
            'txt_color',
            'txt_background',
            'txt_border',
            'txt_radius',
        );
        $optionList   = $_option->getOption($arrayOption, $arrayItemid);
        $optionDetail = '';
        foreach ($optionList as $value) {
            $optionDetail[$value['option']] = $value['value'];
        }
        $products[$key]["txt_box"]        = $optionDetail['txt_box'];
        $products[$key]["txt_color"]      = $optionDetail['txt_color'];
        $products[$key]["txt_background"] = $optionDetail['txt_background'];
        $products[$key]["txt_border"]     = $optionDetail['txt_border'];
        $products[$key]["txt_radius"]     = $optionDetail['txt_radius'];

        // tạm hết hàng
        if (strpos($products[$key]["txt_box"], "hết hàng") !== false) {
            $products[$key]["note_cohang"] = 3;
        }

        // bảo hành mở rộng
        $products[$key]["cost_bhmr"] = self::costBHMR($products[$key]["cost1"], $val['productsid']);

        if ($val['cheap'] != 1) // máy cùi bắp ko bao bể màn hình
        {        
            $products[$key]["cost_baobemanhinh"] = self::costBBMH1($products[$key]["cost1"], $val['productsid'], $val['cateid'],$val['title']);
        }

        if ($val['cateid'] == 53 and $val['is_bhmc'] ==1) // bảo hành vip Điện thoại cũ (Apple, Samsung, Nokia, Asus, Oppo) hàng vt, thu mua ngoài, trôi bảo hành/ Không áp dụng cho Macbook, Smart Watch, Tablet
        {        
           $products[$key]["cost_baohanhvip"] = self::costBHVIP($products[$key]["cost1"]);

        }



        //================ get link may cu
        if ($products[$key]["productsid"] == 3 and $val['search_text']) // nếu là dt
        {
            $_products   = Business_WsNew_ProductsItem::getInstance();
            $search_text = $val['search_text'];
            $result      = $_products->getDetailOldByTitle($search_text);
            if ($result) {
                $title     = $result['title'];
                $titleOlod = $result['title_seo'];
                $titleSEO  = Business_Common_Utils::adaptTitleLinkURLSEO($titleOlod);
                $link      = SEOPlugin::getProductDetailLink($result["itemid"], $titleSEO);
                if ($result['price'] > 0 or $result['price'] > $result['original_price']) {
                    $price = self::numberformat("<font>", $result['price'], 'đ') . '</font>';

                } else {
                    $price = self::numberformat("<font>", $result['original_price'], 'đ') . '</font>';
                }
                if ($val['cateid'] != 53) {
                    $products[$key]["maycu"] = "<a  style='font-size: small;' title='$titleOlod' target='_blank' href='$link?utm_source=internal&utm_campaign=old&utm_medium=cpc'>" . "<font color=black>Xem thêm máy cũ : </font> <font color=#288ad6>$title</font>" . "<p><font color=black>Giá từ : </font> $price</p></a> ";
                    if ($products[$key]["itemid"] == 6802) {
                        $link                    = 'https://www.hnammobile.com/dien-thoai/blackberry-passport-silver-32gb--quoc-te-.10467.html';
                        $title                   = 'BlackBerry Passport Silver 32Gb (Quốc tế)';
                        $products[$key]["maycu"] = "<a  style='font-size: small;' title='$title' target='_blank' href='$link?utm_source=internal&utm_campaign=old&utm_medium=cpc'>" . "<font color=black>Phiên bản quốc tế nhập khẩu : </font> <font color=#288ad6>$title</font>" . "<p><font color=black>Giá từ : </font> $price</p></a>  " . $products[$key]["maycu"];
                    }
                }
            }
        }

        //===========neu la may cu

        if ($val['cateid'] == 53) {
            if (strpos(strtolower($products[$key]["title"]), 'macbook') === false) {
                $products[$key]["doitra"] = '15ngay';
            } else {
                $products[$key]["doitra"] = '';
            }

        // bảo hành máy cũ 
        $products[$key]["cost_bhmc"] = self::costBHMC($products[$key]["cost1"], $val['productsid']);

        }


        if (in_array($products[$key]["itemid"],array(12661))) // gia dự kiến
        {
            if($val['original_price'] == 0 and $val['price'] == 0 )
            {
                $products[$key]['cost']        = "<font color=#3289ff>Giá dự kiến: 13.990.000 đ</font></font>";
                $products[$key]['cost_detail'] = "<font color=#3289ff>Giá dự kiến: 13.990.000 đ</font></font>"; 
            }
        }

  
        
        

        // gia du kien
        if (in_array($products[$key]["itemid"],array(12656))) // gia dự kiến
        {
            if($val['original_price'] == 0 and $val['price'] == 0 )
            {
                $products[$key]['cost']        = "<font color=#3289ff>Giá dự kiến: 7.XXX.XXX đ</font></font>";
                $products[$key]['cost_detail'] = "<font color=#3289ff>Giá dự kiến: 7.XXX.XXX đ</font></font>"; 
            }
        }
        // gia du kien
        if (in_array($products[$key]["itemid"],array(12496,12497))) // gia dự kiến
        {
             if($val['original_price'] == 0 and $val['price'] == 0 )
            {
                $products[$key]['cost']        = "<font color=#ff5400>Giá: vui lòng liên hệ 1900.2012</font></font>";
                $products[$key]['cost_detail'] = "<font color=#ff5400>Giá: vui lòng liên hệ 1900.2012</font></font>"; 
            }
        }
		// gia du kien
        if (in_array($products[$key]["itemid"],array(12457))) // gia dự kiến
        {
            if($val['original_price'] == 0 and $val['price'] == 0 )
            {
                $products[$key]['cost']        = "<font color=#3289ff>Giá dự kiến: 12.XXX.XXXđ</font></font>";
                $products[$key]['cost_detail'] = "<font color=#3289ff>Giá dự kiến: 12.XXX.XXXđ</font></font>"; 
            }
        }
		// gia du kien
        if (in_array($products[$key]["itemid"],array(12457))) // gia dự kiến
        {
            if($val['original_price'] == 0 and $val['price'] == 0 )
            {
                $products[$key]['cost']        = "<font color=#3289ff>Giá dự kiến: 12.XXX.XXXđ</font></font>";
                $products[$key]['cost_detail'] = "<font color=#3289ff>Giá dự kiến: 12.XXX.XXXđ</font></font>"; 
            }
        }
		// gia du kien
        if (in_array($products[$key]["itemid"],array(13007))) // gia dự kiến
        {
            if($val['original_price'] == 0 and $val['price'] == 0 )
            {
                $products[$key]['cost']        = "<font color=#3289ff>Giá dự kiến: 4.XXX.XXXđ</font></font>";
                $products[$key]['cost_detail'] = "<font color=#3289ff>Giá dự kiến: 4.XXX.XXXđ</font></font>"; 
            }
        }



    //$themkm = array(14512,14461,14434,14390,14379,14375,14363,14362,14356,14355,14347,14300,14276,14274,14273,14272,14258,14237,14212,14202,14165,14164,14163,14162,14151,14124,14061,14059,14032,14020,13996,13976,13974,13958,13938,13853,13795,13782,13766,13734,13733,13718,13714,13703,13683,13682,13129,13125,13105,13039,13038,13007,12759,12774,12753,12739,12738,12733,12617,12587,12534,12497,12496,12495,12494,12490,12459,12433,12428,9031,11380,12249,11720,9148,11593,11251,12296,12208,11728,11454,10278,10238,10132,11431,12281,12093,11929,8494,11908,9995,12394,11435,8157,12035,12036,8079,10874,11797,7610,7407,11261,11062,11290,11260,11014,9116,12145,9577,12159,9712,12310,11920,11309,11133,12351,14344,14339,14336,14028,13713,13042,12770,12743,12581,11459,11458,11460,11461,11665,11932,11933,12341,12342);


    if (in_array($products[$key]["productsid"],array(3,5)) and $products[$key]["cateid"] != 53 )
    {
    //    $products[$key]['km'] .= "* Giao hàng online, tặng <a href=\"https://www.hnammobile.com/phu-kien/gay-chup-hinh-tu-suong-mono-pod.6257.html\" target=\"_blank\">gậy tự sướng Mono Pod</a> từ ngày 16/05 - 16/06/2018 hoặc đến khi hết quà"; 
    $products[$key]['km'] .= "* Giao hàng online, tặng <a href=\"https://www.hnammobile.com/phu-kien/pin-du-phong-tich-hop-loa-power-jam-3-in-1-.9464.html\" target=\"_blank\">loa Power Jam 3 in 1</a>"; 
     
    }
        //=== hardcode static
        $products[$key]["unbox"]  =  str_replace('cdn01','stcv4', $val["unbox"] );
        
 
    }
}

function productsHelpThongSoKyThuat($itemid,$productsid)
{
    if($productsid!=6)
    return self::getFeaturesHomePhoneBangGIa($itemid); //=== list short
    else
    return self::getFeaturesHomeLaptop($itemid); //=== list short    
}

function productsHelpBangGia(&$products, $thongso)
{
    foreach ($products as $key => $val) {
        $url                            = 'http://www.hnammobile.com/';
        $products[$key]['image']        = $url . "/v5/images/noimage.jpg";
        $products[$key]['giathitruong'] = $val['oldprice'];

        if ($products[$key]["productsid"] == 3) {
            $products[$key]["loaisanpham"] = 'Điện thoại';
        }

        if ($products[$key]["productsid"] == 4) {
            $products[$key]["loaisanpham"] = 'Phụ kiện';
        }

        if ($products[$key]["productsid"] == 5) {
            $products[$key]["loaisanpham"] = 'Máy tính bảng';
        }

        if ($products[$key]["productsid"] == 6) {
            $products[$key]["loaisanpham"] = 'Laptop';
        }

        if ($products[$key]["productsid"] == 8) {
            $products[$key]["loaisanpham"] = 'Đồng hồ thông minh';
        }
        
        if ($products[$key]["productsid"] == 9) {
            $products[$key]["loaisanpham"] = 'Nước hoa';
        }

        $_menu = Business_WsNew_MenuItem::getInstance();
        $menu  = $_menu->getDetail($products[$key]["cateid"]);

        $products[$key]["danhmuc"] = $menu['title'];

        if ($menu['pid'] > 0) {
            $menu                         = $_menu->getDetail($menu['pid']);
            $products[$key]["danhmuccha"] = $menu['title'];

        }

        // get thong so ky thuat
        if ($thongso == 1) {
            $products[$key]["thongso"] = self::getFeaturesHomePhone($products[$key]['itemid']);
        }
        //=== list short

        //get color
        $_pcolor                       = Business_Addon_Productcolor::getInstance();
        $products[$key]["curentColor"] = $_pcolor->getListByItemid($products[$key]['itemid'], 1);

        // ====================== hàng Hnam
        if ($val['price'] > 0 or $val['price'] > $val['original_price']) {
            $__price                       = $val['price'];
            $products[$key]['cost']        = self::numberformat("<font color=#ff5400>HNAM ", $val['price'], 'đ') . "</font>";
            $products[$key]['cost_detail'] = self::numberformat("<font color=#ff5400>HNAM ", $val['price'], 'đ') . "</font>";
            $products[$key]['cost1']       = $val['price'];
            $products[$key]['cost2']       = self::numberformat("HNAM ", $val['price'], 'đ');
            $products[$key]['cost3']       = self::numberformat("", $val['price'], 'đ');
            $products[$key]["ishnam"]      = true;
            $products[$key]["loaihang"]    = '<font color=#ff5400>Hàng Hnam</font>';
            $products[$key]["doitra"]      = '15ngay';
            if ($products[$key]["productsid"] == 4) // nếu là phụ kiện
            {

            }
            if ($products[$key]["productsid"] == 6) // nếu là laptop
            {

            }
            if ($products[$key]["productsid"] == 8) // nếu là đồng hồ thông minh
            {
                $products[$key]["doitra"] = '15ngay';
            }

        } // ====================== hàng Cty
        else {
            $__price                       = $val['original_price'];
            $products[$key]['cost']        = self::numberformat("<font color=#3289ff>CTY ", $val['original_price'], 'đ') . "</font>";
            $products[$key]['cost_detail'] = self::numberformat("<font color=#3289ff>CTY ", $val['original_price'], 'đ') . "</font><font style='color: #5d5a5a;font-size: small;'> ( Đã có V.A.T )</font>";
            $products[$key]['cost1']       = $val['original_price'];
            $products[$key]['cost2']       = self::numberformat("CTY ", $val['original_price'], 'đ');
            $products[$key]['cost3']       = self::numberformat("", $val['original_price'], 'đ');
            $products[$key]["ishnam"]      = false;
            $products[$key]["loaihang"]    = '<font color="#3289ff">Hàng Công Ty</font>';
            $products[$key]["doitra"]      = '30ngay';
            if ($products[$key]["productsid"] == 4) // nếu là phụ kiện
            {

            }
            if ($products[$key]["productsid"] == 6) // nếu là laptop
            {

            }
            if ($products[$key]["productsid"] == 8) // nếu là đồng hồ thông minh
            {
                $products[$key]["doitra"] = '15ngay';
            }

        }
        // ====================== giá thị trường
        $products[$key]['oldprice'] = self::numberformat('', $val['oldprice'], 'đ');
        // ====================== % down price
        if ($val["oldprice"] > 0) {
            $remain                    = $val["oldprice"] - $__price;
            $__percent                 = $remain * 100 / $val["oldprice"];
            $products[$key]['percent'] = (int) $__percent;
        }
        // ====================== giảm thêm or đã giảm
        if ($val['pricebeforediscount'] > 0) {
            $products[$key]['pricedown'] = self::numberformat(" Giảm thêm ", $val['pricebeforediscount'], 'đ');
        } else {
            if ($val['pricebeforediscount2'] > 0) {
                $products[$key]['pricedown'] = self::numberformat(" Đã giảm ", $val['pricebeforediscount2'], 'đ');
            }

        }
        // ====================== dual sim if ($val['dualsim'] > 0)
        // ====================== title Seo
        $titleSEO               = Business_Common_Utils::adaptTitleLinkURLSEO($val['title_seo']);
        $products[$key]['link'] = self::getLink($val, $titleSEO);
        // ======================
        // ====================== KM
        if ($val["bonus_company_full"] != "") {
            $products[$key]['km'] = $val["bonus_company_full"];
        } else {
            $products[$key]['km'] = $val["bonus_hnam"];
        }

        if ($val["cateid"] == 53) // đặt hàng ngay máy cũ
        {
            $products[$key]["ishnam"] = true;
        }

    }
}


function costBHVIP($cost)
{

    $data = self::getBHVIP();

    foreach ($data as $key => $value) {

        if ($value['from'] <= $cost and $cost <= $value['to']) {
            return self::numberformat('', $value['result'], 'đ');
        }

    }
}


function costBBMH1($cost, $productsid, $cateid,$title)
{
    // 879 iphone 7 895 iphone 7 plush
    if ($cateid == 879 or $cateid == 895 or strpos(strtolower($title),strtolower('iPhone 7')) !== false) {
        return self::numberformat('', 179000, 'đ');
    }
    // 900 iphone 8/8 plus
    if ($cateid == 900 or strpos(strtolower($title),strtolower('iPhone 8')) !== false) {
        return self::numberformat('', 179000, 'đ');
    }
    // 977 iphone X
      if ($cateid == 977 or strpos(strtolower($title),strtolower('iPhone X')) !== false) {
        return self::numberformat('', 179000, 'đ');
    }  


    // nếu là ipad
    if ($productsid == 5) {
        // trừ ipad mini và ipad pro
        $array = array(
            466,
            586,
        );
        if (in_array($cateid, $array)) {
            return self::numberformat('', 179000, 'đ');
        }
        // ipad mini 587
        $array = array(
            587,765,
        );
        if (in_array($cateid, $array)) {
            return self::numberformat('', 179000, 'đ');
        }

        // ipad mini 764
        $array = array(
            764,
        );
        if (in_array($cateid, $array) and strpos($title, '9.7')) {
            return self::numberformat('', 179000, 'đ');
        }
        if (in_array($cateid, $array) and strpos($title, '10.5')) {
            return self::numberformat('', 179000, 'đ');
        }

    }

    // danh sách BBMH 
    $arrayBBMH =  array(895,879,760,759,455,585,264,42,674,354,185,490,563,784,44,53  );    
    if(!in_array($cateid, $arrayBBMH) and $productsid==3)
    {
        return '';
    }

    if($cateid ==53)
    {
        $arrayBBMH = array('iPhone','Samsung','Xiaomi','Oppo','Sony','Asus','Wiko','Vivo','Lg');
        $flag= false;
        foreach ($arrayBBMH as $key => $value) {
           if(strpos(strtolower($title),strtolower($value)) !== false)
           {
                    $flag= true;
                    break;
           }
        }
        if(!$flag or strpos(strtolower($title),strtolower('tab')) !== false  )
          return '';
    }

    if($productsid ==3)
      return self::numberformat('', 179000, 'đ');
}


function costBBMH($cost, $productsid, $cateid,$title)
{
    // 879 iphone 7 895 iphone 7 plush
    if ($cateid == 879 or $cateid == 895 or strpos(strtolower($title),strtolower('iPhone 7')) !== false) {
        return self::numberformat('', 299000, 'đ');
    }
    // 900 iphone 8/8 plus
    if ($cateid == 900 or strpos(strtolower($title),strtolower('iPhone 8')) !== false) {
        return self::numberformat('', 319000, 'đ');
    }
    // 977 iphone X
      if ($cateid == 977 or strpos(strtolower($title),strtolower('iPhone X')) !== false) {
        return self::numberformat('', 499000, 'đ');
    }  


    // nếu là ipad
    if ($productsid == 5) {
        // trừ ipad mini và ipad pro
        $array = array(
            466,
            586,
        );
        if (in_array($cateid, $array)) {
            return self::numberformat('', 499000, 'đ');
        }
        // ipad mini 587
        $array = array(
            587,765,
        );
        if (in_array($cateid, $array)) {
            return self::numberformat('', 399000, 'đ');
        }

        // ipad mini 764
        $array = array(
            764,
        );
        if (in_array($cateid, $array) and strpos($title, '9.7')) {
            return self::numberformat('', 399000, 'đ');
        }
        if (in_array($cateid, $array) and strpos($title, '10.5')) {
            return self::numberformat('', 499000, 'đ');
        }

    }

    // danh sách BBMH 
    $arrayBBMH =  array(895,879,760,759,455,585,264,42,674,354,185,490,563,784,44,53  );    
    if(!in_array($cateid, $arrayBBMH) and $productsid==3)
    {
        return '';
    }

    if($cateid ==53)
    {
        $arrayBBMH = array('iPhone','Samsung','Xiaomi','Oppo','Sony','Asus','Wiko','Vivo','Lg');
        $flag= false;
        foreach ($arrayBBMH as $key => $value) {
           if(strpos(strtolower($title),strtolower($value)) !== false)
           {
                    $flag= true;
                    break;
           }
        }
        if(!$flag or strpos(strtolower($title),strtolower('tab')) !== false  )
          return '';
    }

    $data = self::getBBMH();
    foreach ($data[$productsid] as $key => $value) {

        if ($value['from'] <= $cost and $cost <= $value['to']) {
            return self::numberformat('', $value['result'], 'đ');
        }

    }
}




function costBHMC($cost, $productsid)
{
    $data = self::getBHMC();
    $cost = (int) $cost;
    foreach ($data as $key => $value) {
        if ($value['from'] <= $cost and $cost <= $value['to']) {
            return self::numberformat('', $value['result'], 'đ');
        }

    }
}



function costBHMR($cost, $productsid)
{
    return self::numberformat('', 99000, 'đ');
    $data = self::getBHMR();
    foreach ($data[$productsid] as $key => $value) {

        if ($value['from'] <= $cost and $cost <= $value['to']) {
            return self::numberformat('', $value['result'], 'đ');
        }

    }
}


function getBHVIP()
{
    $array = array();
    // ---------- giá dành cho điện thoại
    $data           = array();
    $data['from']   = 0;
    $data['to']     = 3000000;
    $data['result'] = 149000;
    $array[]     = $data;

    $data           = array();
    $data['from']   = 3000001;
    $data['to']     = 5000000;
    $data['result'] = 249000;
    $array[]   = $data;

    $data           = array();
    $data['from']   = 5000001;
    $data['to']     = 7000000;
    $data['result'] = 329000;
    $array[]     = $data;

    $data           = array();
    $data['from']   = 7000001;
    $data['to']     = 9000000;
    $data['result'] = 439000;
    $array[]   = $data;

    $data           = array();
    $data['from']   = 9000001;
    $data['to']     = 11000000;
    $data['result'] = 549000;
    $array[]  = $data;

    $data           = array();
    $data['from']   = 11000001;
    $data['to']     = 13000000;
    $data['result'] = 659000;
    $array[]   = $data;

    $data           = array();
    $data['from']   = 13000001;
    $data['to']     = 15000000;
    $data['result'] = 769000;
    $array[]   = $data;

    $data           = array();
    $data['from']   = 15000001;
    $data['to']     = 18000000;
    $data['result'] = 899000;
    $array[]    = $data;

    $data           = array();
    $data['from']   = 18000001;
    $data['to']     = 21000000;
    $data['result'] = 979000;
    $array[]   = $data;

    $data           = array();
    $data['from']   = 21000001;
    $data['to']     = 25000000;
    $data['result'] = 1089000;
    $array[]   = $data;


    $data           = array();
    $data['from']   = 25000001;
    $data['to']     = 35000000;
    $data['result'] = 1289000;
    $array[]  = $data;

    // -------------------

    return $array;
}


function getBBMH()
{
    $array = array();
    // ---------- giá dành cho điện thoại
    $data           = array();
    $data['from']   = 0;
    $data['to']     = 6999000;
    $data['result'] = 149000;
    $array[3][]     = $data;

    $data           = array();
    $data['from']   = 7000000;
    $data['to']     = 12000000;
    $data['result'] = 199000;
    $array[3][]     = $data;

    $data           = array();
    $data['from']   = 12000000;
    $data['to']     = 120000000;
    $data['result'] = 249000;
    $array[3][]     = $data;

    // -------------------

    return $array;
}

function getBHMR()
{
    $array = array();
    // ---------- giá dành cho điện thoại
    $data           = array();
    $data['from']   = 0;
    $data['to']     = 999000;
    $data['result'] = 99000;
    $array[3][]     = $data;

    $data           = array();
    $data['from']   = 1000000;
    $data['to']     = 1999000;
    $data['result'] = 129000;
    $array[3][]     = $data;

    $data           = array();
    $data['from']   = 2000000;
    $data['to']     = 2999000;
    $data['result'] = 149000;
    $array[3][]     = $data;

    $data           = array();
    $data['from']   = 3000000;
    $data['to']     = 3999000;
    $data['result'] = 179000;
    $array[3][]     = $data;

    $data           = array();
    $data['from']   = 4000000;
    $data['to']     = 4999000;
    $data['result'] = 199000;
    $array[3][]     = $data;

    $data           = array();
    $data['from']   = 5000000;
    $data['to']     = 6999000;
    $data['result'] = 269000;
    $array[3][]     = $data;

    $data           = array();
    $data['from']   = 7000000;
    $data['to']     = 8999000;
    $data['result'] = 389000;
    $array[3][]     = $data;

    $data           = array();
    $data['from']   = 9000000;
    $data['to']     = 10999000;
    $data['result'] = 449000;
    $array[3][]     = $data;

    $data           = array();
    $data['from']   = 11000000;
    $data['to']     = 12999000;
    $data['result'] = 489000;
    $array[3][]     = $data;

    $data           = array();
    $data['from']   = 13000000;
    $data['to']     = 14999000;
    $data['result'] = 539000;
    $array[3][]     = $data;

    $data           = array();
    $data['from']   = 15000000;
    $data['to']     = 16999000;
    $data['result'] = 619000;
    $array[3][]     = $data;

    $data           = array();
    $data['from']   = 17000000;
    $data['to']     = 18999000;
    $data['result'] = 719000;
    $array[3][]     = $data;

    $data           = array();
    $data['from']   = 19000000;
    $data['to']     = 20999000;
    $data['result'] = 789000;
    $array[3][]     = $data;

    $data           = array();
    $data['from']   = 21000000;
    $data['to']     = 22999000;
    $data['result'] = 869000;
    $array[3][]     = $data;

    $data           = array();
    $data['from']   = 23000000;
    $data['to']     = 24999000;
    $data['result'] = 999000;
    $array[3][]     = $data;

    $data           = array();
    $data['from']   = 25000000;
    $data['to']     = 54999000;
    $data['result'] = 1199000;
    $array[3][]     = $data;



    // -------------------

    // ------ giá dành cho máy tính bảng

    $data           = array();
    $data['from']   = 2000000;
    $data['to']     = 5999000;
    $data['result'] = 290000;
    $array[5][]     = $data;

    $data           = array();
    $data['from']   = 6000000;
    $data['to']     = 7999000;
    $data['result'] = 390000;
    $array[5][]     = $data;

    $data           = array();
    $data['from']   = 8000000;
    $data['to']     = 12499000;
    $data['result'] = 490000;
    $array[5][]     = $data;

    $data           = array();
    $data['from']   = 12500000;
    $data['to']     = 14999000;
    $data['result'] = 590000;
    $array[5][]     = $data;

    $data           = array();
    $data['from']   = 15000000;
    $data['to']     = 19999000;
    $data['result'] = 690000;
    $array[5][]     = $data;

    $data           = array();
    $data['from']   = 20000000;
    $data['to']     = 24999000;
    $data['result'] = 890000;
    $array[5][]     = $data;

    $data           = array();
    $data['from']   = 25000000;
    $data['to']     = 29999000;
    $data['result'] = 990000;
    $array[5][]     = $data;

    $data           = array();
    $data['from']   = 30000000;
    $data['to']     = 34999000;
    $data['result'] = 1019000;
    $array[5][]     = $data;

    return $array;
}


function getBHMC()
{
    $array = array();

    $data           = array();
    $data['from']   = 0;
    $data['to']     = 3000000;
    $data['result'] = 99000;
    $array[0]     = $data;

    $data           = array();
    $data['from']   = 3000000;
    $data['to']     = 6999000;
    $data['result'] = 149000;
    $array[1]     = $data;


    $data           = array();
    $data['from']   = 7000000;
    $data['to']     = 9999000;
    $data['result'] = 219000;
    $array[2]    = $data;


    $data           = array();
    $data['from']   = 10000000;
    $data['to']     = 14999000;
    $data['result'] = 319000;
    $array[3]    = $data;

    $data           = array();
    $data['from']   = 15000000;
    $data['to']     = 150000000;
    $data['result'] = 399000;
    $array[4]    = $data;

    return $array;
}
function strpos_array($haystack, $needles)
{
    if (is_array($needles)) {
        foreach ($needles as $str) {
            if (is_array($str)) {
                $pos = strpos_array($haystack, $str);
            } else {
                $pos = strpos($haystack, $str);
            }
            if ($pos !== false) {
                break;
            }
        }
        return $pos;

    } else {
        return strpos($haystack, $needles);
    }

}

function helpVideo($video, $title)
{
    $_menuItem       = Business_WsNew_MenuItem::getInstance();
    $news            = Business_WsNew_NewsItem::getInstance();
    $_MenuItemHelper = Business_HelpersNew_MenuItem::getInstance();
    $_NewsHelper     = Business_HelpersNew_News::getInstance();

    if ($video != '') {
        $titleKenh  = "Kênh review";
        $menuDetail = $_menuItem->getDetailByTitle($titleKenh);
        $listCateid = $_MenuItemHelper::getMenuDetail($menuDetail);
        if (SEOPlugin::isMobile()) {
            $total = 3;
        } else {
            $total = 5;
        }

        $list = $news->getListChannelByTitle($title,$listCateid, $total);
        if (strpos($video, 'iframe') !== false) {
            if (SEOPlugin::isMobile()) {
                $data = $data . '<div><div style="height:67px;width:120px;float:left;text-align:center;font-style: italic;font-size: smaller;">' . self::getTextBetweenTags($video, "iframe") . "</div><div class='mediades''>" . $title . '</div></div><div style="clear:both;margin-bottom: 10px;""></div>';
            } else {
                $data = $data . '<div style="height: 213px;width:33%;padding:10px;float:left;text-align:center;font-style: italic;font-size: smaller;">' . self::getTextBetweenTags($video, "iframe") . $title . '</div>';
            }

            foreach ($list as $key => $value) {
                if (SEOPlugin::isMobile()) {
                    $data = $data . '<div><div style="height:67px;width:120px;float:left;text-align:center;font-style: italic;font-size: smaller;">' . self::getTextBetweenTags($value['fullcontent'], "iframe") . "</div><div class='mediades''>" . $value['title'] . '</div></div><div style="clear:both;margin-bottom: 10px;""></div>';
                } else {
                    $data = $data . '<div style="height: 213px;width:33%;padding:10px;float:left;text-align:center;font-style: italic;font-size: smaller;">' . self::getTextBetweenTags($value['fullcontent'], "iframe") . $value['title'] . '</div>';
                }

            }

            return $data;
        } else {
            if (SEOPlugin::isMobile()) {
                $data = '<iframe width="120px" height="67px" allowfullscreen  src="https://www.youtube.com/embed/' . $video . '" frameborder="0" allowfullscreen></iframe>';
                $data = '<div><div style="height:67px;width:120px;float:left;text-align:center;font-style: italic;font-size: smaller;">' . $data . "</div><div class='mediades''>" . $title . '</div></div><div style="clear:both;margin-bottom: 10px;""></div>';
            } else {
                $data = '<iframe width="100%" height="auto" allowfullscreen  src="https://www.youtube.com/embed/' . $video . '" frameborder="0" allowfullscreen></iframe>';
                $data = '<div style="height: 213px;width:33%;padding:10px;float:left;text-align:center;font-style: italic;font-size: smaller;">' . $data . $title . '</div>';
            }
            foreach ($list as $key => $value) {
                if (SEOPlugin::isMobile()) {
                    $data = $data . '<div><div style="height:67px;width:120px;float:left;text-align:center;font-style: italic;font-size: smaller;">' . self::getTextBetweenTags($value['fullcontent'], "iframe") . "</div><div class='mediades''>" . $value['title'] . '</div></div><div style="clear:both;margin-bottom: 10px;""></div>';
                } else {
                    $data = $data . '<div style="height: 213px;width:33%;padding:10px;float:left;text-align:center;font-style: italic;font-size: smaller;">' . self::getTextBetweenTags($value['fullcontent'], "iframe") . $value['title'] . '</div>';
                }

            }
            return $data;
        }
    } else {

        $title      = "Kênh review";
        $menuDetail = $_menuItem->getDetailByTitle($title);
        $listCateid = $_MenuItemHelper::getMenuDetail($menuDetail);
        if (SEOPlugin::isMobile()) {
            $total = 4;
        } else {
            $total = 6;
        }

        $list = $news->getListChannelByTitle($title,$listCateid, $total);
        foreach ($list as $key => $value) {
            if (SEOPlugin::isMobile()) {
                $data = $data . '<div><div style="height:67px;width:120px;float:left;text-align:center;font-style: italic;font-size: smaller;">' . self::getTextBetweenTags($value['fullcontent'], "iframe") . "</div><div class='mediades''>" . $value['title'] . '</div></div><div style="clear:both;margin-bottom: 10px;""></div>';
            } else {
                $data = $data . '<div style="height: 213px;width:33%;padding:10px;float:left;text-align:center;font-style: italic;font-size: smaller;">' . self::getTextBetweenTags($value['fullcontent'], "iframe") . $value['title'] . '</div>';
            }

        }

        return $data;
    }
}
function helpVideo1($video, $title)
{
        if (strpos($video, 'iframe') !== false) {
        
            $temp = explode('embed/', $video);
            $temp1=explode('"',  $temp[1] );
            return   $temp1[0];
                     
        } else {
            if(strlen($video)>100)
                return '';
        }
        return $video;
}

function getTextBetweenTags($string, $tagname)
{
    $pattern = "/<$tagname ?.*>(.*)<\/$tagname>/";
    preg_match($pattern, $string, $matches);
    $result = preg_replace('/width="([^"]*)"/', 'width="100%" allowfullscreen', $matches[0]);
    $result = preg_replace('/height="([^"]*)"/', 'height="auto"', $result);
    if (SEOPlugin::isMobile()) {
        $result = preg_replace('/width="([^"]*)"/', 'width="120px" allowfullscreen', $matches[0]);
        $result = preg_replace('/height="([^"]*)"/', 'height="67"', $result);
    }
    return $result;

    if (SEOPlugin::isMobile()) {
        return str_replace('67px', '120px', $matches[0]);
    } else {
        $video = str_replace('315"', 'auto" allowfullscreen', $matches[0]);
        return str_replace('560', '100%', $video);
    }
}

function getWarrantyLinkV2($cateid)
{
    if ($cateid == 0) {
        return "";
    }
    $menu    = Business_Ws_MenuItem::getInstance()->getDetail($cateid);
    $wid     = $menu["warrantyid"];
    $wdetail = Business_Ws_NewsItem::getInstance()->getDetail($wid);
    if ($wdetail != null) {
        $brand = trim(strtolower(strip_tags($wdetail["shortcontent"])));
        $brand = str_replace("&nbsp;", "", $brand);
        $link  = SEOPlugin::getWarrantyStoreLink($wid, $brand);
    } else {
        $link = "";
    }
    return $link;
}

function sortByCateid($list)
{
    $data = array();

    foreach ($list as $item) {
        $data[$item['cateid']][] = $item;
    }
    return $data;
}

function listColorByItemid($list)
{
    $data = array();
    foreach ($list as $val) {
        $data[$val['itemid']][] = $val;
    }

    return $data;
}

function checkThumb($thumb)
{
    if ($thumb == 'null' or $thumb == null or $thumb == ''  or $thumb == '[]') {
        return true;
    }
    return false;
}

function ListDataByPrice(&$list, $pAsc = true)
{
    if ($pAsc) {  // asc

        for ($i = 0; $i < count($list) - 1; $i++) {
            for ($j = $i + 1; $j < count($list); $j++) {
                if ($list[$j]['cost1'] < $list[$i]['cost1'] or $list[$i]['cost1'] == 0) {
                    $temp     = $list[$i];
                    $list[$i] = $list[$j];
                    $list[$j] = $temp;
                }
            }
        }
    } else { // des
        for ($i = 0; $i < count($list) - 1; $i++) {
            for ($j = $i + 1; $j < count($list); $j++) {
                if ($list[$j]['cost1'] > $list[$i]['cost1'] or $list[$i]['cost1'] == 0) {
                    $temp     = $list[$i];
                    $list[$i] = $list[$j];
                    $list[$j] = $temp;
                }
            }
        }
    }

    return $list;
}


function ListDataByPriceHnamCty($list)
{ 
    $data = array();

        foreach ($list as $product) {

            if(strpos($product['cost'], 'HNAM')!==FALSE or in_array($product['itemid'], array(11431,11432,11433,11434)))
            {   
                $data['hnam'][] = $product;
            }
            else 
            { 
                $data['cty'][] = $product;
            } 
        }


    return $data;
}


function ListDataBySearch($list, $search)
{
    if (!is_array($search)) {
        $arr[] = $search;
    } else {
        $arr = $search;
    }

    $data = array();
    foreach ($arr as $value) {
        foreach ($list as $product) {
            if (strpos(strtolower(trim($product["title"])), strtolower($value)) !== false) {
                $data[] = $product;
            }
        }
    }

    return $data;
}

function ListTopByID($list, $array)
{
    $data = array();
    $temp = array();

    foreach ($list as $product) {
        if (in_array($product['itemid'], $array)) {
            $dataTop[$product['itemid']] = $product;
        } else {
            $temp[] = $product;
        }

    }
    foreach ($array as $key => $value) {
        if ($dataTop[$value] == '') {
            continue;
        }

        $data[] = $dataTop[$value];
    }
    $list = array_merge($data, $temp);
    return $list;
}

function ListDataRemoveID($list, $listIDremove,$type=0)
{
    $data = array();

    if($type==0)
    {

        foreach ($list as $product) {
            $has = false;
            if (in_array($product["itemid"], $listIDremove)) {
                $has = true;
                continue;
            }

            if ($has == false) {
                $data[] = $product;
            }
        }

    }
    else
    {

            foreach ($list as $product) {
                $has = false;
                if (!in_array($product["itemid"], $listIDremove)) {
                    $has = true;
                    continue;
                }

                if ($has == false) {
                    $data[] = $product;
                }
            }

    }





    return $data;
}

function ListDataRemoveSearch($list, $search)
{
    $data = array();

    foreach ($list as $product) {
        $has = false;
        foreach ($search as $_p) {
            if (strpos(strtolower(trim($product["title"])), strtolower(trim($_p))) !== false) {
                $has = true;
                break;
            }
        }
        if ($has == false) {
            $data[] = $product;
        }
    }

    return $data;
}

function numberformat($first, $price, $prefix)
{
    if ($price > 0) {
        return "$first " . number_format($price, 0, '', '.') . "$prefix";
    } else {
        return '';
    }

}

function getLink($product, $titleSEO)
{
    if (self::isPhone($product)) {
        $link = SEOPlugin::getProductDetailLink($product['itemid'], $titleSEO);
    } elseif (self::isTablet($product)) {
        $link = SEOPlugin::getTabletDetailLink($product['itemid'], $titleSEO);
    } elseif (self::isLaptop($product)) {
        $link = SEOPlugin::getLaptopDetailLink($product['itemid'], $titleSEO);
    } elseif (self::isSmartWatch($product)) {
        $link = SEOPlugin::getSmartWatchDetailLink($product['itemid'], $titleSEO);
    } elseif (self::isRepair($product)) {
        $link = SEOPlugin::getRepairDetailLink($product['itemid'], $titleSEO);
    }  else {
        $link = SEOPlugin::getAccesoriesDetailLink($product['itemid'], $titleSEO);
    }
    return $link;
}

function isRepair($item)
{
    if ($item["productsid"] == 14) // dev | 8:live
    {
        return true;
    }

    return false;
}


function isSmartWatch($item)
{
    if ($item["productsid"] == 8) // dev | 8:live
    {
        return true;
    }

    return false;
}

function isLaptop($item)
{
    if ($item["productsid"] == 6) {
        return true;
    }

    return false;
}

function isTablet($item)
{
    if ($item["productsid"] == 5) {
        return true;
    }

    return false;
}

function isCallable($product)
{
    if (isset($product['callable'])) {
        if ($product['callable'] == 1) {
            return true;
        }

    }
    return false;
}

function isPhone($item)
{
    if ($item["productsid"] == 3) {
        return true;
    }

    return false;
}

function isAcc($item)
{
    if ($item["productsid"] != 5 && $item["productsid"] != 3) {
        return true;
    }

    return false;
}

function buildLinkbreadcrumbListCate($menuItem)
{
    $data         = array();
    $data['name'] = $menuItem['title'];
    if($menuItem['title_sub']!='')
    $data['name'] = $menuItem['title_sub'];
    $title        = Business_Common_Utils::adaptTitleLinkURLSEO($menuItem['title']);
    $data['link'] = SEOPlugin::getAccesoriesLink($menuItem['itemid'], $title);

    return $data;
}

function buildLinkbreadcrumbSub($menuItem)
{
    $data = array();
    if ($menuItem['title_sub'] != '') {
        $data['name'] = $menuItem['title_sub'];
    } else {
        $data['name'] = $menuItem['title'];
    }

    $title  = Business_Common_Utils::adaptTitleLinkURLSEO($menuItem['title']);
    $cateid = $menuItem['itemid'];
    switch ($menuItem['menuname']) {
        case 'menu_products':
            $data['link'] = SEOPlugin::getProductLink($cateid, $title);
            break;
        case 'menu_tablet':
            $data['link'] = SEOPlugin::getTabletLink($cateid, $title);
            break;
        case 'menu_acc':
            $data['link'] = SEOPlugin::getAccesoriesLink($cateid, $title);
            break;
        case 'menu_watch':
            $data['link'] = SEOPlugin::getSmartWatchLink($cateid, $title);
            break;
        case 'menu_laptop':
            $data['name'] = null;
            // $data['link']= SEOPlugin:: getLaptopAppleLink();
            break;
        case 'menu_perfume':
            $data['link'] = SEOPlugin::getAccesoriesLink($cateid, $title);
            break;
        default:
            break;
    }

    return $data;
}

function buildLinkbreadcrumb($menuItem)
{
    $data = array();
    switch ($menuItem['menuname']) {
        case 'menu_products':
            $data['name'] = 'Điện thoại';
            $data['link'] = SEOPlugin::getDienthoaiLink();
            break;
        case 'menu_tablet':
            $data['name'] = 'Máy tính bảng';
            $data['link'] = SEOPlugin::getMaytinhbangLink();
            break;
        case 'menu_acc':
            $data['name'] = 'Phụ kiện';
            $data['link'] = SEOPlugin::getAccessoriesLink();
            break;
        case 'menu_watch':
            $data['name'] = 'Đồng hồ thông minh';
            $data['link'] = SEOPlugin::getDonghothongminhLink();
            break;
        case 'menu_laptop':
            $data['name'] = 'Laptop';
            $data['link'] = SEOPlugin::getLaptopAppleLink();
            break;
        case 'menu_perfume':
            $data['name'] = 'Phụ kiện';
            $data['link'] = SEOPlugin::getAccessoriesLink();
            $data['fullcontent'] = $menuItem['fullcontent'];     
            break;
        default:
            break;
    }

    return $data;
}

function updateProductFeaturesDetail($features, $pDetail)
{
    $isPhone = self::isPhone($pDetail);

    $isCallable   = self::isCallable($pDetail);
    $dataCallable = null;

    if ($isCallable) {
        // update font callable
        $dataCallable[0]          = $features[1];
        $dataCallable[0]["name"]  = "Điện thoại,SMS";
        $dataCallable[0]["value"] = "Có";
        // push callable info to features
        array_splice($features, 1, 0, $dataCallable);
    } else
    if (!$isPhone) {
        $dataCallable[0]          = $features[1];
        $dataCallable[0]["name"]  = "Điện thoại,SMS";
        $dataCallable[0]["value"] = "Không";
        // push callable info to features
        array_splice($features, 1, 0, $dataCallable);
    }
    $isApple = self::isPhone($pDetail);
    foreach ($features as &$fItem) {
        if (trim($fItem['value']) != '' && trim($fItem['value']) != '_' && strlen($fItem['value']) > 3) {
            $fItem["length"] = strlen($fItem["value"]);
            if ($fItem["length"] > 50) {
                // has 2 line
                $fItem["class"] = " dline";
            } else {
                $fItem["class"] = "";
            }
            $_lv = strtolower($fItem['value']);
        }
    }

    return $features;
}

function getThongSoKyThuat($pid)
{
    $_features = Business_Addon_Features::getInstance();
    $_fList    = $_features->getListByPid($pid);

    return $_fList;
}

function getFeaturesDetailLaptop($pid)
{
    $_features = Business_Addon_Featureslaptop::getInstance();
    $_fList    = $_features->getListByPid($pid);
    return $_fList;
}

function getFeaturesHomePhone4($pid)
{
    $_features = Business_Addon_Features::getInstance();
    $_fList    = $_features->getListByPid($pid);

    $display = array(
        9, // kich co
        45, // cpu
        60, // camera chính
        56, // pin chuẩn
        65, // sim
        64,
    ) // chipset
    ;
    $ret = array();
    foreach ($_fList as $item) {
        if (in_array($item["fid"], $display)) {
            $ret[] = $item;
        }
    }
    return $ret;
}

function getFeaturesHomePhoneBangGIa($pid)
{
    $_features = Business_Addon_Features::getInstance();
    $_fList    = $_features->getListByPid($pid);

    $display = array(
        9,//kich co
            45, //cpu
            18, //bộ nhớ trong
            44, //hđh
            60, //camera chính
            63, //camera phụ
            20, //khe cắm thẻ nhớ
            56, //pin chuẩn
            50, //mau sắc
            64 ,//chipset
             65, // sim
    ) 
    ;
    $ret = array();
    foreach ($_fList as $item) {
        if (in_array($item["fid"], $display)) {
            $ret[] = $item;
        }
    }
    return $ret;
}

function getFeaturesHomePhone($pid)
{
    $_features = Business_Addon_Features::getInstance();
    $_fList    = $_features->getListByPid($pid);

    $display = array(
        9, // kich co
        45, // cpu
        44, // hđh
        60, // camera chính
        63, // camera phụ
        56, // pin chuẩn
        65, // sim
        64,
    ) // chipset
    ;
    $ret = array();
    foreach ($_fList as $item) {
        if (in_array($item["fid"], $display)) {
            $ret[] = $item;
        }
    }
    return $ret;
}

function getFeaturesHomeLaptop($pid)
{
    $_features = Business_Addon_Featureslaptop::getInstance();
    $_fList    = $_features->getListByPid($pid);

    $display = array(
        3, // Bluetooth
        6, // bộ nhớ trong
        7, // hệ điều hành
        8, // Chipset
        9, // CPU
        10, // card đồ họa
        11, // RAM
        13,
    ) // Kích thước
    ;
    $ret = array();
    foreach ($_fList as $item) {
        if (in_array($item["fid"], $display)) {
            $ret[] = $item;
        }
    }
    return $ret;
}

function getFeaturesDetailLaptopGiua($pid)
{
    $_features = Business_Addon_Featureslaptop::getInstance();
    $_fList    = $_features->getListByPid($pid);

    $display = array(
        2, // wifi
        3, // Bluetooth
        4, // cổng kết nối
        6, // bộ nhớ trong
        7, // hệ điều hành
        8, // Chipset
        9, // CPU
        10, // card đồ họa
        11, // RAM
        13,
    ) // Kích thước
    ;
    $ret = array();
    foreach ($_fList as $item) {
        if (in_array($item["fid"], $display)) {
            $ret[] = $item;
        }
    }
    return $ret;
}


function getFeaturesDetailLaptopGiua1($pid)
{
    $_features = Business_Addon_Featureslaptop::getInstance();
    $_fList    = $_features->getListByPid($pid);

    $display = array(
        13,// Kích thước
    ) 
    ;
    $ret = array();
    foreach ($_fList as $item) {
        if (in_array($item["fid"], $display)) {
            $ret[] = $item;
        }
    }
    return $ret;
}


function getFeaturesDetailFeed($pid)
{
    $_features = Business_Addon_Features::getInstance();
    $_fList    = $_features->getListByPid($pid);

    $display = array(
        9, // kich co
        45, // cpu
        44, // hđh
        60, // camera chính
        ) // chipset
    ;
    $ret = array();
    foreach ($_fList as $item) {
        if (in_array($item["fid"], $display)) {
            $ret[] = $item;
        }
    }
    return $ret;
}

function getFeaturesDetailSony($pid)
{
    $_features = Business_Addon_Features::getInstance();
    $_fList    = $_features->getListByPid($pid);

    $display = array(
        60, // camera chính
        45, // cpu
        18, // bộ nhớ trong
        60, // camera chính
        ) // chipset
    ;
    $ret = array();
    foreach ($_fList as $item) {
        if (in_array($item["fid"], $display)) {
            $ret[] = $item;
        }
    }
    return $ret;
}


function getFeaturesDetailViewGiua($pid)
{
    $_features = Business_Addon_Features::getInstance();
    $_fList    = $_features->getListByPid($pid);

    $display = array(
        65, // sim
        9, // kich co
        45, // cpu
        18, // bộ nhớ trong
        44, // hđh
        60, // camera chính
        63, // camera phụ
        20, // khe cắm thẻ nhớ
        56, // pin chuẩn
        50, // mau sắc
        64,
    ) // chipset
    ;
    $ret = array();
    foreach ($_fList as $item) {
        if (in_array($item["fid"], $display)) {
            $ret[] = $item;
        }
    }
    return $ret;
}

function getFeaturesDetailViewGiua1($pid)
{
    $_features = Business_Addon_Features::getInstance();
    $_fList    = $_features->getListByPid($pid);

    $display = array(
        9,
    ) // kich co
    ;
    $ret = array();
    foreach ($_fList as $item) {
        if (in_array($item["fid"], $display)) {
            $ret[] = $item;
        }
    }
    return $ret;
}

/*
 * return false: Không có KM
 * return >0: có KM, gia trị trả về là tổng tiền còn lại sau khi trừ KM
 */
function getProductPromotionPrice($itemid)
{
    $_promotion    = Business_Addon_Promotion::getInstance();
    $_productsitem = Business_Ws_ProductsItem::getInstance();
    $detail        = $_productsitem->getDetail($itemid);
    $price         = $detail["price"];
    if ($price == 0) {
        $price = $detail["original_price"];
    }

    $list   = $_promotion->getListByPid2($itemid);
    $sprice = 0;
    foreach ($list as $items) {
        // check date from date to
        if ($items['date_from'] != '' and $items['date_to'] != '') {
            $now = date("Y-m-d H:i:s");
            if ($now > $items['date_from'] and $now < $items['date_to']) {
                if ($items["type"] == 0 || $items["type"] == 6) {
                    $sprice += $items["return_price"];
                }
            }
        } else {
            if ($items["type"] == 0 || $items["type"] == 6) {
                $sprice += $items["return_price"];
            }
        }
    }
    if ($sprice == 0) {
        return false;
    }
    return $price - $sprice;
}


function getProductPromotionPrice1($itemid)
{
    $_promotion    = Business_Addon_Promotion::getInstance();
    $_productsitem = Business_Ws_ProductsItem::getInstance();
    $detail        = $_productsitem->getDetail($itemid);
    $price         = $detail["price"];
    if ($price == 0) {
        $price = $detail["original_price"];
    }

    $list   = $_promotion->getListByPid3($itemid);
    $sprice = 0;
    foreach ($list as $items) {
        // check date from date to
        if ($items['date_from2'] != '' and $items['date_end2'] != '') {
            $now = date("Y-m-d H:i:s");
            if ($now > $items['date_from2'] and $now < $items['date_end2']) {
                if ($items["type"] == 0 || $items["type"] == 6) {
                    $sprice += $items["return_price"];
                }
            }
        } else {
            if ($items["type"] == 0 || $items["type"] == 6) {
                $sprice += $items["return_price"];
            }
        }
    }
    if ($sprice == 0) {
        return false;
    }
    return $price - $sprice;
}



}

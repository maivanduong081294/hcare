<?php

class Business_Common_Utils {

    static function  render_excel($header,$content,$filenames) {
        
//        include(EXCELLIBRARY_PATH."PHPExcel.php");
        include("PHPExcel.php");
        
        $objPHPExcel = new PHPExcel();
        $start_data=1;
//        foreach($content as $key=>$value) {
//            $col =0;
//            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value);
//            $col++;
//            $row++;
//        }
        $objPHPExcel->getProperties()->setCreator("Hnammobile")
            ->setLastModifiedBy("Hnammobile")
            ->setTitle("Hnammobile-Order")
            ->setSubject("Hnammobile-Order")
            ->setDescription("Hnammobile-Order")
            ->setKeywords("Hnammobile-Order")
            ->setCategory("Hnammobile-Order");
        $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')
                                                  ->setSize(10);
        $objPHPExcel->getActiveSheet()->setCellValue('B'.$start_data, 'Tên')
                                      ->setCellValue('C'.$start_data, 'Ngày chi')
                                      ->setCellValue('D'.$start_data, 'Nội dung')
                                      ->setCellValue('E'.$start_data, 'Số tiền')
                                      ->setCellValue('F'.$start_data, 'Phân loại')
                                      ->setCellValue('H'.$start_data, 'Phòng ban')
                                      ->setCellValue('I'.$start_data, 'Chi nhánh')
                                      ->setCellValue('J'.$start_data, 'Mã hóa đơn')
                                      ->setCellValue('K'.$start_data, 'Ngày ra hóa đơn');
        $objPHPExcel->getActiveSheet()->getStyle("A$start_data:K$start_data")->getFont()->setBold(true);
        
        
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $path_save = Business_Common_Utils::getPathBillExcel();
        if($filenames ==NULL){
            $filenames = date('YmdHis');
        }
        $objPHPExcel->getActiveSheet()->setTitle($filenames);
        
        $__filename = $path_save.$filenames.".xlsx";
        
        $__f = str_replace(__FILE__, $__filename, __FILE__);
        
        $objWriter->save($__f);
        
        //save file
        ob_start();
        $contents = file_get_contents($__f);
        header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
        header("Content-type:   application/x-msexcel; charset=utf-8");
        header("Content-Disposition: attachment; filename=$filenames.xlsx"); 
        ob_end_clean();
        echo $contents;
    }
    static function  getExcelBIllDelegate($list,$ac,$month,$year) {
        $_option = Business_Addon_Options::getInstance();
        $lformality = $_option->getformality();
        $_cost = Business_Addon_Cost::getInstance();
        $_cost_detail = Business_Addon_CostDetail::getInstance();
        $_zwfuser = Business_Common_Users::getInstance();
        $lstore = $_zwfuser->getListByUname(FALSE);
        $_department = Business_Addon_Department::getInstance();
        $__regency = Business_Addon_Regency::getInstance();
        $ldepartment = $_department->getList();
        $lregency = $__regency->getList();
        $name_regency = array();
        foreach ($lregency as $items){
            $name_regency[$items["id"]] = $items["name"];
        }
        $name_department = array();
        foreach ($ldepartment as $items){
            $name_department[$items["id"]] = $items["name"];
        }
        $arr_storename = array();
        foreach ($lstore as $items){
            $arr_storename[$items["userid"]] = $items["storename"];
        }
        
        $lcost = $_cost->getList();
        $name_cost = array();
        foreach ($lcost as $items){
            $name_cost[$items["id"]] = $items["name"];
        }
        
        $__lcost_detail = $_cost_detail->getList();
        $name_cost_detail = array();
        foreach ($__lcost_detail as $items){
            $name_cost_detail[$items["id"]] = $items["name"];
        }
        
        
        $filename ='';
        $tiento ='';
        if($ac ==1){
            $filename ='PHIẾU CHI -';
            $tiento ='PC';
        }
        if($ac ==2){
            $filename ='PHIẾU THU -';
             $tiento ='PT';
        }
        if($ac ==3){
            $filename ='ỦY NHIỆM CHI -';
             $tiento ='UNC';
        }
        $filenames = $tiento.$month.$year;
        include(EXCELLIBRARY_PATH."PHPExcel.php");
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("Hnammobile")
            ->setLastModifiedBy("Hnammobile")
            ->setTitle("Hnammobile-Order")
            ->setSubject("Hnammobile-Order")
            ->setDescription("Hnammobile-Order")
            ->setKeywords("Hnammobile-Order")
            ->setCategory("Hnammobile-Order");
        $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')
                                                  ->setSize(10);
        $start =1;
        $start_mer =$start+4;
        $start_data = $start+1;
        $objPHPExcel->getActiveSheet()->setCellValue('A'.($start), $filename.' CÔNG TY TNHH TM DV DI ĐỘNG SAO VIỆT');
        $objPHPExcel->getActiveSheet()->getStyle('A'.($start))->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("A$start:D$start")->getFont()->setSize(15);
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells("A$start:D$start");
        $width = 20;
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth("$width");
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth("$width");
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth("$width");
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth("$width");
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth("$width");
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth("$width");
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth("$width");
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth("$width");
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth("$width");
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth("$width");
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth("$width");
        if($ac ==1 || $ac ==2){
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$start_data, 'Số '.$tiento);
            $objPHPExcel->getActiveSheet()->setCellValue('G'.$start_data, 'Các chi phí');
        }else{
            $objPHPExcel->getActiveSheet()->setCellValue('G'.$start_data, 'Hình thức');
        }
        
        $objPHPExcel->getActiveSheet()->setCellValue('B'.$start_data, 'Tên')
                                      ->setCellValue('C'.$start_data, 'Ngày chi')
                                      ->setCellValue('D'.$start_data, 'Nội dung')
                                      ->setCellValue('E'.$start_data, 'Số tiền')
                                      ->setCellValue('F'.$start_data, 'Phân loại')
                                      ->setCellValue('H'.$start_data, 'Phòng ban')
                                      ->setCellValue('I'.$start_data, 'Chi nhánh')
                                      ->setCellValue('J'.$start_data, 'Mã hóa đơn')
                                      ->setCellValue('K'.$start_data, 'Ngày ra hóa đơn');
        $objPHPExcel->getActiveSheet()->getStyle("A$start_data:K$start_data")->getFont()->setBold(true);
        
        $total =0;
        foreach ($list as $items){
            $total +=$items["money"];
            $storename ='';
            if($items["dayout"] != "0000-00-00"){
                if($items["dayout"] != null){
                    $items["dayout"] = date('d/m/Y',  strtotime($items["dayout"]));
                }
            }else{
                $items["dayout"] = '';
            }
            if($items["departmentid"] ==10 && $items["storeid"] >0){ 
                $storename =  $arr_storename[$items["storeid"]];
                } else if($items["storeid"] ==0) {

                    $storename =  "Tất cả"; 
               } else {
                    $storename =  $name_regency[$items["storeid"]];
                }
            $start_data++;
             if($ac ==1 || $ac ==2){
                 $objPHPExcel->getActiveSheet()->setCellValue('A'.$start_data, "Số: $tiento".$items["number_pc"]);
                 $objPHPExcel->getActiveSheet()->setCellValue('G'.$start_data, $name_cost_detail[$items["cost_detail"]]);
             }else{
                 $objPHPExcel->getActiveSheet()->setCellValue('G'.$start_data, $lformality[$items["formality"]]);
             }
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$start_data, $items["name"])
                                      ->setCellValue('C'.$start_data, date('d/m/Y',  strtotime($items["bill_datetime"])))
                                      ->setCellValue('D'.$start_data, $items["content"])
                                      ->setCellValue('E'.$start_data, $items["money"])
                                      ->setCellValue('F'.$start_data, $name_cost[$items["costid"]])
                                      ->setCellValue('H'.$start_data, $name_department[$items["departmentid"]])
                                      ->setCellValue('I'.$start_data, $storename)
                                      ->setCellValue('J'.$start_data, $items["bills_id"])
                                      ->setCellValue('K'.$start_data, $items["dayout"]);
            $objPHPExcel->getActiveSheet()->getStyle('E'.($start_data).':E'.($start_data))->getNumberFormat()->setFormatCode('#,##0');
        }
        $objPHPExcel->getActiveSheet()->setCellValue('D'.($start_data+1),'Tổng:')
                                      ->setCellValue('E'.($start_data+1), $total);
        $objPHPExcel->getActiveSheet()->getStyle('E'.($start_data+1).':E'.($start_data+1))->getNumberFormat()->setFormatCode('#,##0');
        $objPHPExcel->getActiveSheet()->getStyle('D'.($start_data+1).':E'.($start_data+1))->getFont()->setBold(true);
        
      $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        
        $path_save = Business_Common_Utils::getPathBillExcel();
        
        $objPHPExcel->getActiveSheet()->setTitle($filenames);
        
        $__filename = $path_save.$filenames.".xlsx";
        
        $objWriter->save(str_replace(__FILE__, $__filename, __FILE__));
        //save file
        
        $path = $__filename;
        $contents = file_get_contents($__filename);
        header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
        header("Content-type:   application/x-msexcel; charset=utf-8");
        header("Content-Disposition: attachment; filename=$filenames.xlsx"); 
        ob_end_clean();
        echo $contents;
        die();
    }
    
    static function  getExcelOrder($id) {
        
        $zwf_user = Business_Common_Users::getInstance();
        $_order = Business_Addon_Order::getInstance();
        $_order_detail = Business_Addon_OrderDetail::getInstance();
        $_newitem = Business_Ws_NewsItem::getInstance();
        $_vendor = Business_Import_Vendor::getInstance();
        $detail = $_order->getDetail($id);
        if($detail["enabled"] ==0){
            $this->_redirect('/admin');
        }
        $detail_vendor = $_vendor->getDetail($detail["supplier_id"]);
        $detail_user = $zwf_user->getDetail($detail["userid"]);
        $list_order = $_order_detail->getListByOrderId($detail["id"]);
        foreach ($list_order as $items){
            $_pid[] = $items["pid"];
            $_colorid[] = $items["pid_color"];
        }
        $_colorid = array_unique($_colorid);
        $colorid = implode(",", $_colorid);
        
        $_pid = array_unique($_pid);
        $pid = implode(",", $_pid);
        $_productsitem = Business_Ws_ProductsItem::getInstance();
        $list_products = $_productsitem->getListByProductsID($pid);
        $name_pid = array();
        foreach ($list_products as $items){
            $name_pid[$items["itemid"]] = $items["title"];
        }
        
        $name_color = array();
        $list_color = $_newitem->getListByItemid($colorid);
        
        foreach ($list_color as $items){
            $name_color[$items["itemid"]] = $items["title"];
        }
        $total =0;
        $ck =0;
        foreach ($list_order as $items){
            $ck_detail =0;
            $total1 +=$items["total"];
            $total2 +=$items["price"];
            $total3 +=$items["price"]*$items["total"];
            
            
        }
        
        include(EXCELLIBRARY_PATH."PHPExcel.php");
        
        // Create new PHPExcel object
//        echo date('H:i:s') , " Create new PHPExcel object" , EOL;
        $objPHPExcel = new PHPExcel();
        // Set document properties
//        echo date('H:i:s') , " Set document properties" , EOL;
        $objPHPExcel->getProperties()->setCreator("Hnammobile")
            ->setLastModifiedBy("Hnammobile")
            ->setTitle("Hnammobile-Order")
            ->setSubject("Hnammobile-Order")
            ->setDescription("Hnammobile-Order")
            ->setKeywords("Hnammobile-Order")
            ->setCategory("Hnammobile-Order");

        // Set default font
//        echo date('H:i:s') , " Set default font" , EOL;
        $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')
                                                  ->setSize(10);
        // Add some data, resembling some different data types
//        echo date('H:i:s') , " Add some data" , EOL;
        
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth("50");
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth("15");
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth("15");
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth("15");
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth("15");
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth("15");
        $objPHPExcel->getActiveSheet()->setCellValue('A2', 'CÔNG TY TNHH TM DV DI ĐỘNG SAO VIỆT')
                                      ->setCellValue('A3', '148 Nguyễn Cư Trinh, P. Nguyễn Cư Trinh, Q.1, TP.HCM')
                                      ->setCellValue('A4', 'MST: 0309538058')
                                      ->setCellValue('A5', 'Điện thoại: 1900 2012')
                                      ->setCellValue('A8', 'Nhân viên kinh doanh : '.$detail_user["fullname"])
                                      ->setCellValue('A9', 'Số điện thoại : 0'.$detail_user["phone"])
                                      ->setCellValue('A10', 'Email : '.$detail_user["email"]);

        $objPHPExcel->getActiveSheet()->setCellValue('D2', $detail_vendor["namekt"])
                                      ->setCellValue('D3', $detail_vendor["address"])
                                      ->setCellValue('D4', $detail_vendor["mst"])
                                      ->setCellValue('D5', $detail_vendor["phone"])
                                      ->setCellValue('D8', 'Nhân viên kinh doanh : '.$detail["staff_supplier"])
                                      ->setCellValue('D9', 'Số điện thoại : '.$detail["supplier_phone"])
                                      ->setCellValue('D10', 'Email : '.$detail["supplier_email"]);
        
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells("D2:F2");
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells("D3:F3");
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells("D4:F4");
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells("D5:F5");
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells("D6:F6");
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells("D7:F7");
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells("D8:F8");
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells("D9:F9");
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells("D10:F10");
        
        
        $objPHPExcel->getActiveSheet()->setCellValue('A13', 'Sản phẩm')
                                      ->setCellValue('B13', 'Màu sắc')
                                      ->setCellValue('C13', 'Số lượng')
                                      ->setCellValue('D13', 'Đơn giá(EU)')
                                      ->setCellValue('E13', 'Chiết khấu')
                                      ->setCellValue('F13', 'Thành tiền');
        $starts_title_dh =12;
        $objPHPExcel->getActiveSheet()->setCellValue('A'.$starts_title_dh, 'Chi tiết đơn hàng');
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells("A$starts_title_dh:B$starts_title_dh");
        $objPHPExcel->getActiveSheet()->getStyle("A$starts_title_dh:A$starts_title_dh")->getFont()->setSize(15);
        $objPHPExcel->getActiveSheet()->getStyle("A$starts_title_dh:A$starts_title_dh")->getFont()->setBold(true);
        $start = 13;
        
        foreach ($list_order as $items){
            $start++;
            $cks =0;
            $ck_detail =0;
            if($items["type_discount"] ==1 || $items["type_discount"] ==3){
                $cks = $items["discount"];
                $ck += $items["discount"]* $items["total"];
               $ck_detail = $items["discount"]* $items["total"];
            }else{
                $cks = ($items["discount"]/100)* $items["price"];
                $ck += ($items["discount"]/100)* $items["price"]*$items["total"];
                $ck_detail = ($items["discount"]/100)* $items["price"]*$items["total"];
            }
            $kk+=$cks;
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$start, $items["accounting_name"])
                                      ->setCellValue('B'.$start, $name_color[$items["pid_color"]])
                                      ->setCellValue('C'.$start, ($items["total"]))
                                      ->setCellValue('D'.$start, ($items["price"]))
                                      ->setCellValue('E'.$start, $cks)
                                      ->setCellValue('F'.$start, $items["price"]*$items["total"] -$ck_detail);
            $objPHPExcel->getActiveSheet()->getStyle('C'.($start).':F'.($start))->getNumberFormat()->setFormatCode('#,##0');
        }
        $objPHPExcel->getActiveSheet()->setCellValue('B'.($start+1), 'Total:')
                                      ->setCellValue('C'.($start+1), $total1)
                                      ->setCellValue('D'.($start+1), $total2)
                                      ->setCellValue('E'.($start+1), $kk)
                                      ->setCellValue('F'.($start+1), $total3-$ck);
        $objPHPExcel->getActiveSheet()->getStyle('C'.($start+1).':F'.($start+1))->getNumberFormat()->setFormatCode('#,##0');
        $note = explode("\r\n", $detail["note"]);
        $objPHPExcel->getActiveSheet()->setCellValue('A'.($start+3), 'Ghi chú:');
        $objPHPExcel->getActiveSheet()->getStyle('A'.($start+3))->getFont()->setBold(true);
        $number_note = $start +3;
        foreach ($note as $items){
            $number_note++;
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$number_note, $items);
        }
        
        
        
//        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A13:B14');
        $objPHPExcel->getActiveSheet()->getStyle("A2:A6")->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("D2:D6")->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("A13:F13")->getFont()->setBold(true);
        // Rename worksheet
//        echo date('H:i:s') , " Rename worksheet" , EOL;
        
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);
        // Save Excel 2007 file
//        echo date('H:i:s') , " Write to Excel2007 format" , EOL;
//        $callStartTime = microtime(true);
        
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        
        $path_save = Business_Common_Utils::getPathOrderExcel();
        $filename = "hnam-order-".date('dmY').".xlsx";
        
        $objPHPExcel->getActiveSheet()->setTitle($filename);
        
        $objWriter->save(str_replace(__FILE__, $path_save.$filename, __FILE__));
        //save file
        
        $path = $path_save.$filename;
        $contents = file_get_contents($path);
        header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
        header("Content-type:   application/x-msexcel; charset=utf-8");
        header("Content-Disposition: attachment; filename=$filename"); 
        ob_end_clean();
        echo $contents;
        die();
    }
    static  function getPathOrderExcel() {
        $lastChar = strrchr(BASE_PATH, "\\");     
        if ($lastChar !== "\\") {
            $basePath = BASE_PATH . "\\";
        }
        $basePath = $basePath . "uploads\\order\\excel\\";
        return sprintf("%s", $basePath);
    }
    static  function getPathBillExcel() {
        $lastChar = strrchr(BASE_PATH, "\\");     
        if ($lastChar !== "\\") {
            $basePath = BASE_PATH . "\\";
        }
        $basePath = $basePath . "uploads\\billdelegate\\excel\\";
        return sprintf("%s", $basePath);
    }
    static function  gethtmlOrder($id) {
        $_option = Business_Addon_Options::getInstance();
        $zwf_user = Business_Common_Users::getInstance();
        $_order = Business_Addon_Order::getInstance();
        $_order_detail = Business_Addon_OrderDetail::getInstance();
        $_newitem = Business_Ws_NewsItem::getInstance();
        $_vendor = Business_Import_Vendor::getInstance();
        $detail = $_order->getDetail($id);
        if($detail["enabled"] ==0){
            $this->_redirect('/admin');
        }
        $detail_vendor = $_vendor->getDetail($detail["supplier_id"]);
        $detail_user = $zwf_user->getDetail($detail["userid"]);
        $list_order = $_order_detail->getListByOrderId($detail["id"]);
        foreach ($list_order as $items){
            $_pid[] = $items["pid"];
            $_colorid[] = $items["pid_color"];
        }
        $_colorid = array_unique($_colorid);
        $colorid = implode(",", $_colorid);
        
        $_pid = array_unique($_pid);
        $pid = implode(",", $_pid);
        $_productsitem = Business_Ws_ProductsItem::getInstance();
        $list_products = $_productsitem->getListByProductsID($pid);
        $name_pid = array();
        foreach ($list_products as $items){
            $name_pid[$items["itemid"]] = $items["title"];
        }
        
        $name_color = array();
        $list_color = $_newitem->getListByItemid($colorid);
        
        foreach ($list_color as $items){
            $name_color[$items["itemid"]] = $items["title"];
        }
        $total =0;
        $ck =0;
        foreach ($list_order as $items){
            $ck_detail =0;
            $total1 +=$items["total"];
            $total2 +=$items["price"];
            $total3 +=$items["price"]*$items["total"];
            
            
        }
        $content="";
        $content ='<table  width="100%" style="border-collapse: collapse;">
                    <tr  valign="top">
                        <th align="left" valign="top" width="60%" style="font-size:11px;float:left">CÔNG TY TNHH TM DV DI ĐỘNG SAO VIỆT<br/>
                        148 Nguyễn Cư Trinh, P. Nguyễn Cư Trinh, Q.1, TP.HCM <br/>
						MST: 0309538058<br />
                        Điện thoại: 1900 2012
                        </th>
                        
                        <th  valign="top" align="left" width="40%" style="font-size:11px;float:left">
                        '.$detail_vendor["namekt"].' <br/>
                        '.$detail_vendor["address"].' <br/>
                        MST : '.$detail_vendor["mst"].' <br/>
                        Điện Thoại : '.$detail_vendor["phone"].' <br/>
                            
                            
                           
                            </th>
                    </tr>
                    <tr> 
                        <td width="60%" style="font-size:11px;float:left" width="40%"> Nhân viên kinh doanh : '.$detail_user["fullname"].' <br/>
                            Số điện thoại : 0'.$detail_user["phone"].' <br/>
                            Email : '.$detail_user["email"].'
                        </td>
                        <td width="40%" align="left" valign="top" style="font-size:11px;float:left"> 
                            Nhân viên kinh doanh : '.$detail["staff_supplier"].' <br/>
                            Số điện thoại : '.$detail["supplier_phone"].' <br/>
                            Email : '.$detail["supplier_email"].'
                        </td>
                    </tr>
                </table>';
        $content .="<br/>";
        $ck =0;
        foreach ($list_order as $items){
            $cks =0;
            $ck_detail =0;
            if($items["type_discount"] ==1 || $items["type_discount"] ==3){
                $cks = $items["discount"];
                $ck += $items["discount"]* $items["total"];
               $ck_detail = $items["discount"]* $items["total"];
            }else{
                $cks = ($items["discount"]/100)* $items["price"];
                $ck += ($items["discount"]/100)* $items["price"]*$items["total"];
                $ck_detail = ($items["discount"]/100)* $items["price"]*$items["total"];
            }
            $kk+=$cks;
            $lcontent.='<tr>'
            . '<td>'.$items["accounting_name"].'</td>'
            . '<td>'.$name_color[$items["pid_color"]].'</td>'
            . '<td style="text-align: right">'.number_format($items["total"]).'</td>'
            . '<td style="text-align: right">'.number_format($items["price"]).'</td>'
            . '<td style="text-align: right">'.number_format($cks).'</td>'
            . '<td style="text-align: right">'.number_format($items["price"]*$items["total"] -$ck_detail).'</td>'
            . '</tr>';
        }
        $content .='<table width="100%" border="1" style="border-collapse: collapse;">
                    <tr>
                        <th width="40%">Sản phẩm</th>
                        <th width="12%">Màu sắc</th>
                        <th width="10%">Số lượng</th>
                        <th width="12%">Đơn giá(EU)</th>
                        <th width="12%">Chiết khấu</th>
                        <th width="14%">Thành tiền</th>
                    </tr>
                '.$lcontent.'
                    <tr>
                    <td style="text-align: right" colspan="2">Total:</td>
                        <td style="text-align: right">'.number_format($total1).'</td>
                        <td style="text-align: right" >'.number_format($total2).'</td>
                        <td style="text-align: right" >'.number_format($kk).'</td>
                        <td style="font-weight:bold;float:right;text-align: right" >'.number_format($total3-$ck).'</td>
                    </tr>
                </table>';
       //update detail note
		$detail["note"] = str_replace("\r\n", "<br />", $detail["note"]);
        $content .='<p><b>Ghi chú:</b>'.'</p>'.$detail["note"]; 
        return $content;
        
    }
    static function  gethtmlOrder_BK($id) {
        $_option = Business_Addon_Options::getInstance();
        $zwf_user = Business_Common_Users::getInstance();
        $_order = Business_Addon_Order::getInstance();
        $_order_detail = Business_Addon_OrderDetail::getInstance();
        $_newitem = Business_Ws_NewsItem::getInstance();
        $_vendor = Business_Import_Vendor::getInstance();
        $detail = $_order->getDetail($id);
        if($detail["enabled"] ==0){
            $this->_redirect('/admin');
        }
        $detail_vendor = $_vendor->getDetail($detail["supplier_id"]);
        $detail_user = $zwf_user->getDetail($detail["userid"]);
        $list_order = $_order_detail->getListByOrderId($detail["id"]);
        foreach ($list_order as $items){
            $_pid[] = $items["pid"];
            $_colorid[] = $items["pid_color"];
        }
        $_colorid = array_unique($_colorid);
        $colorid = implode(",", $_colorid);
        
        $_pid = array_unique($_pid);
        $pid = implode(",", $_pid);
        $_productsitem = Business_Ws_ProductsItem::getInstance();
        $list_products = $_productsitem->getListByProductsID($pid);
        $name_pid = array();
        foreach ($list_products as $items){
            $name_pid[$items["itemid"]] = $items["title"];
        }
        
        $name_color = array();
        $list_color = $_newitem->getListByItemid($colorid);
        
        foreach ($list_color as $items){
            $name_color[$items["itemid"]] = $items["title"];
        }
        $total =0;
        $ck =0;
        foreach ($list_order as $items){
            $ck_detail =0;
            $total1 +=$items["total"];
            $total2 +=$items["price"];
            $total3 +=$items["price"]*$items["total"];
            
            
        }
        $content="";
        $content ='<table  width="100%" style="border-collapse: collapse;">
                    <tr  valign="top">
                        <th align="left" valign="top" width="60%" style="font-size:11px;float:left">CÔNG TY TNHH TM DV DI ĐỘNG SAO VIỆT<br/>
                        148 Nguyễn Cư Trinh, P. Nguyễn Cư Trinh, Q.1, TP.HCM <br/>
						MST: 0309538058<br />
                        Điện thoại: 1900 2012
                        </th>
                        
                        <th  valign="top" align="left" width="40%" style="font-size:11px;float:left">
                        '.$detail_vendor["namekt"].' <br/>
                        '.$detail_vendor["address"].' <br/>
                        MST : '.$detail_vendor["mst"].' <br/>
                        Điện Thoại : '.$detail_vendor["phone"].' <br/>
                            
                            
                           
                            </th>
                    </tr>
                    <tr> 
                        <td width="60%" style="font-size:11px;float:left" width="40%"> Nhân viên kinh doanh : '.$detail_user["fullname"].' <br/>
                            Số điện thoại : 0'.$detail_user["phone"].' <br/>
                            Email : '.$detail_user["email"].'
                        </td>
                        <td width="40%" align="left" valign="top" style="font-size:11px;float:left"> 
                            Nhân viên kinh doanh : '.$detail["staff_supplier"].' <br/>
                            Số điện thoại : '.$detail["supplier_phone"].' <br/>
                            Email : '.$detail["supplier_email"].'
                        </td>
                    </tr>
                </table>';
        $content .="<br/>";
        $ck =0;
        foreach ($list_order as $items){
            $cks =0;
            $ck_detail =0;
            if($items["type_discount"] ==1){
                $cks = $items["discount"];
                $ck += $items["discount"]* $items["total"];
               $ck_detail = $items["discount"]* $items["total"];
            }else{
                $cks = ($items["discount"]/100)* $items["price"];
                $ck += ($items["discount"]/100)* $items["price"]*$items["total"];
                $ck_detail = ($items["discount"]/100)* $items["price"]*$items["total"];
            }
            $kk+=$cks;
            $lcontent.='<tr>'
            . '<td>'.$items["accounting_name"].'</td>'
            . '<td>'.$name_color[$items["pid_color"]].'</td>'
            . '<td style="text-align: right">'.number_format($items["total"]).'</td>'
            . '<td style="text-align: right">'.number_format($items["price"]).'</td>'
            . '<td style="text-align: right">'.number_format($items["price"]*$items["total"]).'</td>'
            . '</tr>';
            
            $lck.='<tr>'
            . '<td>'.$items["accounting_name"].'</td>'
            . '<td>'.$name_color[$items["pid_color"]].'</td>'
            . '<td style="text-align: right">'.number_format($items["total"]).'</td>'
            . '<td style="text-align: right">'.number_format($cks).'</td>'
            . '<td style="text-align: right">'.number_format($ck_detail).'</td>'
            . '</tr>';
            
            
        }
        $content .='<table width="100%" border="1" style="border-collapse: collapse;">
                    <tr>
                        <th width="40%">Sản phẩm</th>
                        <th width="15%">Màu sắc</th>
                        <th width="15%">Số lượng</th>
                        <th width="15%">Đơn giá</th>
                        <th width="15%">Thành tiền</th>
                    </tr>
                '.$lcontent.'
                    <tr>
                    <td style="text-align: right" colspan="2">Total:</td>
                        <td style="text-align: right">'.number_format($total1).'</td>
                        <td style="text-align: right" >'.number_format($total2).'</td>
                        <td style="text-align: right" >'.number_format($total3).'</td>
                    </tr>
                </table>'
                ;
        
        if ($ck>0) {
			$content .="<br/><p>Chiết khấu trực tiếp</p>";
			$content .='<table width="100%" border="1" style="border-collapse: collapse;">
						<tr>
							<th width="40%">Sản phẩm</th>
							<th width="15%">Màu sắc</th>
							<th width="15%">Số lượng</th>
							<th width="15%">Chiết khấu/Sản phẩm</th>
							<th width="15%">Thành tiền</th>
						</tr>
					'.$lck.'
						<tr>
							<td style="text-align: right" colspan="2">Total:</td>
							<td style="text-align: right">'.number_format($total1).'</td>
							<td style="text-align: right" >'.number_format($kk).'</td>
							<td style="text-align: right">'.number_format($ck).'</td>
						</tr>
						
					</table>'
					;
		}	
        $total4 = $total3-$ck;
        $content .="<br/><table width='100%' border='1' style='border-collapse: collapse;'><tr><td style='text-align: right'width='85%'>Số tiền thanh toán:</td><td><span style='font-size:16px;font-weight:bold;float:right'> ".number_format($total4)."</span></td></tr></table> ";
       //update detail note
		$detail["note"] = str_replace("\r\n", "<br />", $detail["note"]);
        $content .='<p><b>Ghi chú:</b>'.'</p>'.$detail["note"]; 
        return $content;
        
    }
    static function redirect($url) {
        header("HTTP/1.1 301 Moved Permanently");
        header('Location: ' . $url);
        exit;
    }
    
    static function fixEmptyTag($str) {
        $str = str_replace("span></span", "span>&nbsp;</span", $str);
        $str = str_replace("\"></span", "\">&nbsp;</span", $str);
        return $str;
    }
    
    static function getCurrentIP() {
        if (isset($_SERVER)) {
            if (isset($_SERVER["HTTP_X_FORWARDED_FOR"]))
                return $_SERVER["HTTP_X_FORWARDED_FOR"];

            if (isset($_SERVER["HTTP_CLIENT_IP"]))
                return $_SERVER["HTTP_CLIENT_IP"];

            return $_SERVER["REMOTE_ADDR"];
        }

        if (getenv('HTTP_X_FORWARDED_FOR'))
            return getenv('HTTP_X_FORWARDED_FOR');

        if (getenv('HTTP_CLIENT_IP'))
            return getenv('HTTP_CLIENT_IP');

        return getenv('REMOTE_ADDR');
    }

    static function secondsToDate($seconds) {
        if ($seconds == 0)
            return "(<b class='red'>0</b> ngày)";
        $one_day = 24 * 60 * 60;
        $day = (int) ($seconds / $one_day);
        $hours = (int) ( ($seconds % $one_day) / 3600);
        return "(<b class='red'>" . $day . "</b> ngày <b class='red'>" . $hours . "</b>h)";
    }

    static function parseInput($string, $length = 0) {
        $string = htmlspecialchars($string);
        $string = str_replace('\'', '&#39;', $string);
        $string = str_replace('"', '&quot;', $string);
        if ($length == 0)
            return $string;
        $aString = explode(" ", $string);
        return array_slice($aString, 0, $length);
    }

    static function getCurrentURL() {
        $host = $_SERVER['HTTP_HOST'];
        $uri = $_SERVER['REQUEST_URI'];
        return 'http://' . $host . $uri;
    }

    static function prepareDay() {
        $return = "";
        for ($i = 1; $i <= 31; $i++) {
            if ($i < 10) {
                $return .= "<option value='0" . $i . "'>0" . $i . "</option>";
            } else {
                $return .= "<option value='" . $i . "'>" . $i . "</option>";
            }
        }
        return $return;
    }

    static function prepareMonth() {
        $return = "";
        for ($i = 1; $i <= 12; $i++) {
            if ($i < 10) {
                $return .= "<option value='0" . $i . "'>0" . $i . "</option>";
            } else {
                $return .= "<option value='" . $i . "'>" . $i . "</option>";
            }
        }
        return $return;
    }

    static function prepareYear($range = 100) {
        $year = intval(date('Y'));

        $start = $year - $range;

        $return = "";
        for ($i = $start; $i < $year; $i++) {
            if ($i < 10) {
                $return .= "<option value='0" . $i . "'>0" . $i . "</option>";
            } else {
                $return .= "<option value='" . $i . "'>" . $i . "</option>";
            }
        }
        return $return;
    }

    static function adaptTitleLinkURLSEO($title) {
        $title = str_replace("  ", " ", $title);
        $special = array(" ", "/", "\\", "?", "&", ",", "\"", "”", "“", "'", "%", "(", ")", ".", "!", "®");
        $title = str_replace($special, "-", $title);
        $title = self::removeTiengViet($title);
        $title = strtolower($title);
        if (is_numeric($title[strlen($title) - 1]))
            $title = $title . "-";
        return $title;
    }
    static function adaptTitleLinkURLSEO2($title) {
        $title = str_replace("  ", " ", $title);
        $special = array(" ", "/", "\\", "?", "&", ",", "\"", "”", "“", "'", "%", "(", ")", "!", "®");
        $title = str_replace($special, "-", $title);
        $title = self::removeTiengViet($title);
        $title = strtolower($title);
        if (is_numeric($title[strlen($title) - 1]))
            $title = $title . "-";
        return $title;
    }
    static function adaptDateToString($datetime) {  
        $ptime = strtotime($datetime);
        $etime = time() - $ptime;

        if ($etime < 1)
        {
            return '0 giây';
        }

        $a = array( 365 * 24 * 60 * 60  =>  'year',
                    30 * 24 * 60 * 60  =>  'month',
                        24 * 60 * 60  =>  'day',
                            60 * 60  =>  'hour',
                                    60  =>  'minute',
                                    1  =>  'second'
                    );
        $a_plural = array( 'year'   => 'năm',
                        'month'  => 'tháng',
                        'day'    => 'ngày',
                        'hour'   => 'giờ',
                        'minute' => 'phút',
                        'second' => 'giây'
                    );
        
        foreach ($a as $secs => $str)
        {
            $d = $etime / $secs;
            if ($d >= 1)
            {
                $r = round($d);
                return $r . ' ' . ($r > 1 ? $a_plural[$str] : $a_plural[$str]) . ' trước';
            }
        }
    }
    
    
    static function adaptTextSEO($title) {
        $title = str_replace("  ", " ", $title);
        $special = array(" ", "/", "\\", "?", "&", ",", "\"", "”", "“", "'", "%", "(", ")", ".", "!", "®");
        $title = str_replace($special, "-", $title);
        $title = self::removeTiengViet($title);
        $title = strtolower($title);
        if (is_numeric($title[strlen($title) - 1]))
            $title = $title . "-";
        return $title;
    }

    static function adaptTitleLinkURL($title) {
        $title = str_replace("-", "", $title);
        $title = str_replace("  ", " ", $title);
        $special = array(" ", "/", "\\", "?", "&", ",", "\"", "”", "“", "'", "(", ")", ".");
        $title = str_replace($special, "-", $title);
        $title = self::removeTiengViet($title);
        $title = strtolower($title) . '.html';
        return $title;
    }

    static function removeTiengViet($content) {
        $trans = array('à' => 'a', 'á' => 'a', 'ả' => 'a', 'ã' => 'a', 'ạ' => 'a', 'â' => 'a', 'ấ' => 'a', 'ầ' => 'a', 'ẫ' => 'a', 'ẩ' => 'a', 'ậ' => 'a', 'ú' => 'u', 'ù' => 'u', 'ủ' => 'u', 'ũ' => 'u', 'ụ' => 'u', 'à' => 'a', 'á' => 'a', 'ô' => 'o', 'ố' => 'o', 'ồ' => 'o', 'ổ' => 'o', 'ỗ' => 'o', 'ộ' => 'o', 'ó' => 'o', 'ò' => 'o', 'ỏ' => 'o', 'õ' => 'o', 'ọ' => 'o', 'ê' => 'e', 'ế' => 'e', 'ề' => 'e', 'ể' => 'e', 'ễ' => 'e', 'ệ' => 'e', 'í' => 'i', 'ì' => 'i', 'ỉ' => 'i', 'ĩ' => 'i', 'ị' => 'i', 'ơ' => 'o', 'ớ' => 'o', 'ý' => 'y', 'ỳ' => 'y', 'ỷ' => 'y', 'ỹ' => 'y', 'ỵ' => 'y', 'ờ' => 'o', 'ở' => 'o', 'ỡ' => 'o', 'ợ' => 'o', 'ư' => 'u', 'ừ' => 'u', 'ứ' => 'u', 'ử' => 'u', 'ữ' => 'u', 'ự' => 'u', 'đ' => 'd', 'À' => 'A', 'Á' => 'A', 'Ả' => 'A', 'Ã' => 'A', 'Ạ' => 'A', 'Â' => 'A', 'Ấ' => 'A', 'À' => 'A', 'Ẫ' => 'A', 'Ẩ' => 'A', 'Ậ' => 'A', 'Ú' => 'U', 'Ù' => 'U', 'Ủ' => 'U', 'Ũ' => 'U', 'Ụ' => 'U', 'Ô' => 'O', 'Ố' => 'O', 'Ồ' => 'O', 'Ổ' => 'O', 'Ỗ' => 'O', 'Ộ' => 'O',
            'Ê' => 'E', 'Ế' => 'E', 'Ề' => 'E', 'Ể' => 'E', 'Ễ' => 'E', 'Ệ' => 'E', 'Í' => 'I', 'Ì' => 'I', 'Ỉ' => 'I', 'Ĩ' => 'I', 'Ị' => 'I', 'Ơ' => 'O', 'Ớ' => 'O', 'Ờ' => 'O', 'Ở' => 'O', 'Ỡ' => 'O', 'Ợ' => 'O', 'Ư' => 'U', 'Ừ' => 'U', 'Ứ' => 'U', 'Ử' => 'U', 'Ữ' => 'U', 'Ự' => 'U', 'Đ' => 'D', 'Ý' => 'Y', 'Ỳ' => 'Y', 'Ỷ' => 'Y', 'Ỹ' => 'Y', 'Ỵ' => 'Y', 'ẻ' => 'e', 'ẽ' => 'e', 'ẹ' => 'e', 'ẵ' => 'a', 'ẳ' => 'a',
            'á' => 'a', 'à' => 'a', 'ả' => 'a', 'ã' => 'a', 'ạ' => 'a', 'ă' => 'a', 'ắ' => 'a', 'ằ' => 'a', 'ẳ' => 'a', 'ẵ' => 'a', 'ặ' => 'a', 'â' => 'a', 'ấ' => 'a', 'ầ' => 'a', 'ẩ' => 'a', 'ẫ' => 'a', 'ậ' => 'a', 'ú' => 'u', 'ù' => 'u', 'ủ' => 'u', 'ũ' => 'u', 'ụ' => 'u', 'ư' => 'u', 'ứ' => 'u', 'ừ' => 'u', 'ử' => 'u', 'ữ' => 'u', 'ự' => 'u', 'í' => 'i', 'ì' => 'i', 'ỉ' => 'i', 'ĩ' => 'i', 'ị' => 'i', 'ó' => 'o', 'ò' => 'o', 'ỏ' => 'o', 'õ' => 'o', 'ọ' => 'o', 'ô' => 'o', 'ố' => 'o', 'ồ' => 'ô', 'ổ' => 'o', 'ỗ' => 'o', 'ộ' => 'o', 'ơ' => 'o', 'ớ' => 'o', 'ờ' => 'o', 'ở' => 'o', 'ỡ' => 'o', 'ợ' => 'o', 'đ' => 'd', 'Đ' => 'D', 'ý' => 'y', 'ỳ' => 'y', 'ỷ' => 'y', 'ỹ' => 'y', 'ỵ' => 'y', 'Á' => 'A', 'À' => 'A', 'Ả' => 'A', 'Ã' => 'A', 'Ạ' => 'A', 'Ă' => 'A', 'Ắ' => 'A', 'Ẳ' => 'A', 'Ẵ' => 'A', 'Ặ' => 'A', 'Â' => 'A', 'Ấ' => 'A', 'Ẩ' => 'A', 'Ẫ' => 'A', 'Ậ' => 'A', 'É' => 'E', 'È' => 'E', 'Ẻ' => 'E', 'Ẽ' => 'E', 'Ẹ' => 'E', 'Ế' => 'E', 'Ề' => 'E', 'Ể' => 'E', 'Ễ' => 'E', 'Ệ' => 'E', 'Ú' => 'U', 'Ù' => 'U', 'Ủ' => 'U', 'Ũ' => 'U', 'Ụ' => 'U', 'Ư' => 'U', 'Ứ' => 'U', 'Ừ' => 'U', 'Ử' => 'U', 'Ữ' => 'U', 'Ự' => 'U', 'Í' => 'I', 'Ì' => 'I', 'Ỉ' => 'I', 'Ĩ' => 'I', 'Ị' => 'I', 'Ó' => 'O', 'Ò' => 'O', 'Ỏ' => 'O', 'Õ' => 'O', 'Ọ' => 'O', 'Ô' => 'O', 'Ố' => 'O', 'Ổ' => 'O', 'Ỗ' => 'O', 'Ộ' => 'O', 'Ơ' => 'O', 'Ớ' => 'O', 'Ờ' => 'O', 'Ở' => 'O', 'Ỡ' => 'O', 'Ợ' => 'O', 'Ý' => 'Y', 'Ỳ' => 'Y', 'Ỷ' => 'Y', 'Ỹ' => 'Y', 'Ỵ' => 'Y', 'ặ' => 'a', 'é' => 'e', 'ắ' => 'a', 'ế' => 'e', 'è' => 'e', 'ằ' => 'a', 'É' => 'E', '–' => '')
        ;
        $content = strtr($content, $trans); // chuoi da duoc bo dau
        return $content;
    }

    static function sendRequestByCurl($url, $param = array()) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
        $output = curl_exec($ch);
        curl_close($ch);
        unset($ch);
        if ($output === false) {
            return "";
        }
        return $output;
    }
    static function sendEmailKMC($from, $displayname, $replyto, $to, $subject, $body_html, $file_attached = null, $mail_config, $cc) {

        if ($mail_config == "used") {
            $mail_config = "smtp.gmail.com;587;khomaycu@hnammobile.com;bobo@abc@098";
        } else {
            $mail_config = "smtp.gmail.com;587;saleonline@hnammobile.com;saleonline552015";
        }
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

            $transport = new Zend_Mail_Transport_Smtp($host, $config);

            $mail = new Zend_Mail('utf-8');
            $mail->setType(Zend_Mime::MULTIPART_RELATED);
            $mail->setHeaderEncoding(Zend_Mime::ENCODING_BASE64);
            $mail->setReplyTo($replyto);
//			$mail->setBodyText(strip_tags($body_html));

            $mail->setFrom($from, $displayname);
            $mail->addTo($to);
            if ($cc != null) {
                $mail->addCc($cc);
            }
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
    static function sendEmailV3($from, $displayname, $replyto, $to, $subject, $body_html, $file_attached = null, $mail_config="",$cc=null) {
        if($mail_config !=null){
            $mail_config = "smtp.gmail.com;587;saleonline@hnammobile.com;saleonline552015";
        }
        

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
            $transport = new Zend_Mail_Transport_Smtp($host, $config);

            $mail = new Zend_Mail('utf-8');
            $mail->setType(Zend_Mime::MULTIPART_RELATED);
            $mail->setHeaderEncoding(Zend_Mime::ENCODING_BASE64);

            $mail->setReplyTo($replyto);
//			$mail->setBodyText(strip_tags($body_html));

            $mail->setFrom($from, $displayname);
            $mail->addTo($to);
            
            if ($cc != null) {
                $mail->addCc($cc);
            }
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
    static function sendEmailV4($from, $displayname, $replyto, $to, $subject, $body_html, $file_attached = null, $mail_config="",$cc=null,$bcc=null) {
        if($mail_config !=null){
            //$mail_config = "smtp.mailgun.org;587;postmaster@hnammobile.vn;vannghi@123@098"; 
            $mail_config = "smtp.mailgun.org;587;postmaster@notify.hnammobile.vn;7d6f5fb7d27818ea37ce85a75f40f576"; 
        }
        

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
            $transport = new Zend_Mail_Transport_Smtp($host, $config);

            $mail = new Zend_Mail('utf-8');
            $mail->setType(Zend_Mime::MULTIPART_RELATED);
            $mail->setHeaderEncoding(Zend_Mime::ENCODING_BASE64);

            $mail->setReplyTo($replyto);
//			$mail->setBodyText(strip_tags($body_html));

            $mail->setFrom($from, $displayname);
            $mail->addTo($to);
            
            if ($cc != null) {
                $mail->addCc($cc);
            }
            if ($bcc != null) {
                $mail->addBcc($bcc);
            }
            $mail->setSubject($subject);
            $mail->setBodyHtml($body_html);

            //$mail->setBodyHtml($body_html);
            if(APP_ENV != 'development'){
                $mail->send($transport);
            }
//		    pre($result);
        } catch (Exception $ex) {
            return $ex->getMessage();
        }

        return "";
    }

    static function sendEmail($from, $displayname, $replyto, $to, $subject, $body_html, $file_attached = null, $mail_config) {
        //nghidv amazon
        if ($mail_config == null)
            $mail_config = "email-smtp.us-east-1.amazonaws.com;587;AKIAJDCE4FESLO52ENHA;ApQc+MZCEyw0crfoN9hOgLOalbq6hn9RnvutGwIKU+Xd";


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

            $transport = new Zend_Mail_Transport_Smtp($host, $config);

            $mail = new Zend_Mail('utf-8');
            $mail->setType(Zend_Mime::MULTIPART_RELATED);
            //$mail->addHeader("List-Unsubscribe: <mailto:hotro@easymail.vn>");
            //$mail->addHeader("Return-Path: <mailto:hotro@easymail.vn>");
            $mail->setHeaderEncoding(Zend_Mime::ENCODING_BASE64);

            $mail->setReplyTo($replyto);
            //			$mail->setBodyText(strip_tags($body_html));

            $mail->setFrom($from, $displayname);
            $mail->addTo($to);
            $mail->setSubject($subject);
            $mail->setBodyHtml($body_html);

            //$mail->setBodyHtml($body_html);
            $mail->send($transport);
            //		pre($result);
        } catch (Exception $ex) {
            return $ex->getMessage();
        }

        return "";
    }

    static function uppercase($name) {
        $result = strtoupper($name);
        return $result;
    }

    static function curPageURL() {
        $pageURL = 'http';
        if (isset($_SERVER["HTTPS"]) and $_SERVER["HTTPS"] == "on") {
            $pageURL .= "s";
        }
        $pageURL .= "://";
        if ($_SERVER["SERVER_PORT"] != "80") {
            $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
        } else {
            $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
        }
        return $pageURL;
    }

    static function generateRandomWord($length = 6) {
        $list = 'ABCDEFGHIJKLMNOPQRST123456789';

        $rndWord = "";
        for ($i = 0; $i < $length; $i++) {
            $index = rand(0, strlen($list) - 1);
            $rndWord .= $list{$index};
        }
        return $rndWord;
    }

    static function generateRandomNumber($length = 6) {
        $list = '0123456789';

        $rndWord = "";
        for ($i = 0; $i < $length; $i++) {
            $index = rand(0, strlen($list) - 1);
            $rndWord .= $list{$index};
        }
        return $rndWord;
    }

    public static function getContentByCurl($url) {
        try {
            
            $agents[] = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)';
            $agents[] = 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; SV1)';
            $agents[] = 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; SV1)';
            $agents[] = 'Mozilla/4.0 (compatible; MSIE 9.0; Windows NT 5.1; SV1)';
            $agents[] = 'Mozilla/4.0 (compatible; MSIE 10.0; Windows NT 5.1; SV1)';
            $agents[] = 'Mozilla/4.0 (compatible; MSIE 11.0; Windows NT 5.1; SV1)';
  
            shuffle($agents);
            $agent=$agents[0];
            
            $curlHandle = curl_init(); // init curl
            
            curl_setopt($curlHandle, CURLOPT_URL, $url); // set the url to fetch   
            curl_setopt($curlHandle,CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curlHandle, CURLOPT_HEADER, 0);
            curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curlHandle, CURLOPT_BINARYTRANSFER, 1);
            curl_setopt($curlHandle, CURLOPT_ENCODING , "");
            curl_setopt($curlHandle, CURLOPT_TIMEOUT, 300);
            curl_setopt($curlHandle, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($curlHandle, CURLOPT_USERAGENT, $agent);
            $content = curl_exec($curlHandle);
            curl_close($curlHandle);
        } catch (Exception $ex) {
            return "";
        }

        return $content;
    }
    public static function getContentByCurl2($url) {
        $options = array(
            CURLOPT_RETURNTRANSFER => true,     // return web page
            CURLOPT_HEADER         => false,    // don't return headers
            CURLOPT_FOLLOWLOCATION => true,     // follow redirects
            CURLOPT_ENCODING       => "",       // handle all encodings
            CURLOPT_USERAGENT      => "spider", // who am i
            CURLOPT_AUTOREFERER    => true,     // set referer on redirect
            CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
            CURLOPT_TIMEOUT        => 120,      // timeout on response
            CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
            CURLOPT_SSL_VERIFYPEER => false     // Disabled SSL Cert checks
        );

        $ch      = curl_init( $url );
        curl_setopt_array( $ch, $options );
        $content = curl_exec( $ch );
        $err     = curl_errno( $ch );
        $errmsg  = curl_error( $ch );
        $header  = curl_getinfo( $ch );
        curl_close( $ch );

        $header['errno']   = $err;
        $header['errmsg']  = $errmsg;
        $header['content'] = $content;
        return $content;
    }

    public static function getFileContent($path) {
        if ($path != null) {
            try {
                $ret = file_get_contents($path);
                return $ret;
            } catch (Exception $exc) {
                echo $exc->getTraceAsString();
            }
        }
        return null;
    }

    public static function getEmailTemplate($templatename) {
        if ($templatename != null) {
            $path = APPLICATION_PATH . "/templates/" . $templatename . ".html";
            return self::getFileContent($path);
        }
    }

    public static function waterMark($image_source) {
        $waterMarkImage = BASE_PATH . Globals::getConfig('watermark');

        if (is_file($image_source)) {
            exec("composite -dissolve 50 -gravity NorthEast $waterMarkImage $image_source $image_source", $result = array());
        }
    }

    static function convertDateTime($date, $time = false) {
        if ($time)
            $_time = "H:i:s"; else
            $_time = '';
        $date = date('d-m-Y ' . $_time, strtotime($date));
        return $date;
    }

    static function shortPrice($price) {
        $limit = 1000000;
        if ($price == 0)
            return 0;
        if ($price < $limit)
            return ($price / 1000) . " ngàn";
        return ($price / 1000000) . " triệu";
    }

    static function shorten($text, $length = 50) {
        if ($text == null)
            return "";
        $text = explode(" ", $text);
        return implode(" ", array_slice($text, 0, $length)) . "...";
    }

    static function isValidEmail($email) {
        if (preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $email)) {
            return true;
        }
        return false;
    }

    static function getDateBefore($mySQLDate, $day) {
        return date('Y-m-d', strtotime($date . ' -' . $day . ' day'));
    }

    static function sendMG($subject, $displayName, $fromEmail, $toEmail, $html) {
        $domain = "ezm-system.com";
        $url = "https://api.mailgun.net/v2/$domain/messages";
        $username = 'api';
        $password = 'key-9jfoeqxft0pnd7iuafd2exghyaz2okc2';
// create a new cURL resource
        $myRequest = curl_init($url);
        $data["from"] = "$displayName <$fromEmail>";
        $data["to"] = $toEmail;
        $data["subject"] = $subject;
        $data["html"] = $html;
        foreach ($data as $k => $v) {
            $arr[] = $k . "=" . urlencode($v);
        }
        $datas = implode("&", $arr);
// do a POST request, using application/x-www-form-urlencoded type
        curl_setopt($myRequest, CURLOPT_POST, TRUE);
// credentials
        curl_setopt($myRequest, CURLOPT_USERPWD, "$username:$password");
// returns the response instead of displaying it
        curl_setopt($myRequest, CURLOPT_RETURNTRANSFER, 1);

//merge data
        curl_setopt($myRequest, CURLOPT_POSTFIELDS, $datas);

// do request, the response text is available in $response
        $response = curl_exec($myRequest);
    }
    
    static function  getExcelOrder_BK($id) {
        $zwf_user = Business_Common_Users::getInstance();
        $_order = Business_Addon_Order::getInstance();
        $_order_detail = Business_Addon_OrderDetail::getInstance();
        $_newitem = Business_Ws_NewsItem::getInstance();
        $_vendor = Business_Import_Vendor::getInstance();
        $detail = $_order->getDetail($id);
        if($detail["enabled"] ==0){
            $this->_redirect('/admin');
        }
        $detail_vendor = $_vendor->getDetail($detail["supplier_id"]);
        $detail_user = $zwf_user->getDetail($detail["userid"]);
        $list_order = $_order_detail->getListByOrderId($detail["id"]);
        foreach ($list_order as $items){
            $_pid[] = $items["pid"];
            $_colorid[] = $items["pid_color"];
        }
        $_colorid = array_unique($_colorid);
        $colorid = implode(",", $_colorid);
        
        $_pid = array_unique($_pid);
        $pid = implode(",", $_pid);
        $_productsitem = Business_Ws_ProductsItem::getInstance();
        $list_products = $_productsitem->getListByProductsID($pid);
        $name_pid = array();
        foreach ($list_products as $items){
            $name_pid[$items["itemid"]] = $items["title"];
        }
        
        $name_color = array();
        $list_color = $_newitem->getListByItemid($colorid);
        
        foreach ($list_color as $items){
            $name_color[$items["itemid"]] = $items["title"];
        }
        $total =0;
        $ck =0;
        foreach ($list_order as $items){
            $ck_detail =0;
            $total1 +=$items["total"];
            $total2 +=$items["price"];
            $total3 +=$items["price"]*$items["total"];
            
            
        }
        
        include(EXCELLIBRARY_PATH."PHPExcel.php");
        // Create new PHPExcel object
//        echo date('H:i:s') , " Create new PHPExcel object" , EOL;
        $objPHPExcel = new PHPExcel();
        // Set document properties
//        echo date('H:i:s') , " Set document properties" , EOL;
        $objPHPExcel->getProperties()->setCreator("Hnammobile")
            ->setLastModifiedBy("Hnammobile")
            ->setTitle("Hnammobile-Order")
            ->setSubject("Hnammobile-Order")
            ->setDescription("Hnammobile-Order")
            ->setKeywords("Hnammobile-Order")
            ->setCategory("Hnammobile-Order");

        // Set default font
//        echo date('H:i:s') , " Set default font" , EOL;
        $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')
                                                  ->setSize(10);
        // Add some data, resembling some different data types
//        echo date('H:i:s') , " Add some data" , EOL;
        $st=2;
        $ncc = 13;
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth("50");
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth("15");
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth("15");
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth("15");
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth("15");
        $objPHPExcel->getActiveSheet()->setCellValue('A2', 'CÔNG TY TNHH TM DV DI ĐỘNG SAO VIỆT')
                                      ->setCellValue('A3', '148 Nguyễn Cư Trinh, P. Nguyễn Cư Trinh, Q.1, TP.HCM')
                                      ->setCellValue('A4', 'MST: 0309538058')
                                      ->setCellValue('A5', 'Điện thoại: 1900 2012')
                                      ->setCellValue('A8', 'Nhân viên kinh doanh : '.$detail_user["fullname"])
                                      ->setCellValue('A9', 'Số điện thoại : 0'.$detail_user["phone"])
                                      ->setCellValue('A10', 'Email : '.$detail_user["email"]);

        $objPHPExcel->getActiveSheet()->setCellValue('D2', $detail_vendor["namekt"])
                                      ->setCellValue('D3', $detail_vendor["address"])
                                      ->setCellValue('D4', $detail_vendor["mst"])
                                      ->setCellValue('D5', $detail_vendor["phone"])
                                      ->setCellValue('D8', 'Nhân viên kinh doanh : '.$detail["staff_supplier"])
                                      ->setCellValue('D9', 'Số điện thoại : '.$detail["supplier_phone"])
                                      ->setCellValue('D10', 'Email : '.$detail["supplier_email"]);
        
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells("D2:F2");
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells("D3:F3");
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells("D4:F4");
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells("D5:F5");
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells("D6:F6");
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells("D7:F7");
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells("D8:F8");
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells("D9:F9");
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells("D10:F10");
        
        
        $objPHPExcel->getActiveSheet()->setCellValue('A13', 'Sản phẩm')
                                      ->setCellValue('B13', 'Màu sắc')
                                      ->setCellValue('C13', 'Số lượng')
                                      ->setCellValue('D13', 'Đơn giá')
                                      ->setCellValue('E13', 'Thành tiền');
        $starts_title_dh =12;
        $objPHPExcel->getActiveSheet()->setCellValue('A'.$starts_title_dh, 'Chi tiết đơn hàng');
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells("A$starts_title_dh:B$starts_title_dh");
        $objPHPExcel->getActiveSheet()->getStyle("A$starts_title_dh:A$starts_title_dh")->getFont()->setSize(15);
        $objPHPExcel->getActiveSheet()->getStyle("A$starts_title_dh:A$starts_title_dh")->getFont()->setBold(true);
        $start = 13;
        foreach ($list_order as $items){
            $start++;
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$start, $items["accounting_name"])
                                      ->setCellValue('B'.$start, $name_color[$items["pid_color"]])
                                      ->setCellValue('C'.$start, number_format($items["total"]))
                                      ->setCellValue('D'.$start, number_format($items["price"]))
                                      ->setCellValue('E'.$start, number_format($items["price"]*$items["total"]));
        }
        $objPHPExcel->getActiveSheet()->setCellValue('B'.($start+1), 'Total:')
                                      ->setCellValue('C'.($start+1), number_format($total1))
                                      ->setCellValue('D'.($start+1), number_format($total2))
                                      ->setCellValue('E'.($start+1), number_format($total3));
        $starts_title = $start+2;
        $objPHPExcel->getActiveSheet()->setCellValue('A'.$starts_title, 'Chiết khấu trực tiếp');
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells("A$starts_title:B$starts_title");
        $objPHPExcel->getActiveSheet()->getStyle("A$starts_title:A$starts_title")->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("A$starts_title:A$starts_title")->getFont()->setSize(15);
        $start2 = $start+3;
        $objPHPExcel->getActiveSheet()->setCellValue('A'.$start2, 'Sản phẩm')
                                      ->setCellValue('B'.$start2, 'Màu sắc')
                                      ->setCellValue('C'.$start2, 'Số lượng')
                                      ->setCellValue('D'.$start2, 'Chiết khấu sản phẩm')
                                      ->setCellValue('E'.$start2, 'Thành tiền');
        $objPHPExcel->getActiveSheet()->getStyle("A$start2:E$start2")->getFont()->setBold(true);
        foreach ($list_order as $items){
            $start2++;
            $cks =0;
            $ck_detail =0;
            if($items["type_discount"] ==1){
                $cks = $items["discount"];
                $ck += $items["discount"]* $items["total"];
               $ck_detail = $items["discount"]* $items["total"];
            }else{
                $cks = ($items["discount"]/100)* $items["price"];
                $ck += ($items["discount"]/100)* $items["price"]*$items["total"];
                $ck_detail = ($items["discount"]/100)* $items["price"]*$items["total"];
            }
            $kk+=$cks;
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$start2, $items["accounting_name"])
                                      ->setCellValue('B'.$start2, $name_color[$items["pid_color"]])
                                      ->setCellValue('C'.$start2, number_format($items["total"]))
                                      ->setCellValue('D'.$start2, number_format($cks))
                                      ->setCellValue('E'.$start2, number_format($ck_detail));
        }
        $objPHPExcel->getActiveSheet()->setCellValue('B'.($start2+1), 'Total:')
                                      ->setCellValue('C'.($start2+1), number_format($total1))
                                      ->setCellValue('D'.($start2+1), number_format($kk))
                                      ->setCellValue('E'.($start2+1), number_format($ck));
        
        $note = explode("\r\n", $detail["note"]);
        $objPHPExcel->getActiveSheet()->setCellValue('A'.($start2+3), 'Ghi chú:');
        $objPHPExcel->getActiveSheet()->getStyle('A'.($start2+3))->getFont()->setBold(true);
        $number_note = $start2 +3;
        foreach ($note as $items){
            $number_note++;
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$number_note, $items);
        }
        
        
        
//        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A13:B14');
        $objPHPExcel->getActiveSheet()->getStyle("A2:A6")->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("D2:D6")->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("A13:E13")->getFont()->setBold(true);
        // Rename worksheet
//        echo date('H:i:s') , " Rename worksheet" , EOL;
        
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);
        // Save Excel 2007 file
//        echo date('H:i:s') , " Write to Excel2007 format" , EOL;
//        $callStartTime = microtime(true);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $path_save = $this->getPathOrderExcel();
        $filename = "hnam-order-".date('dmY').".xlsx";
        $objPHPExcel->getActiveSheet()->setTitle($filename);
        $objWriter->save(str_replace(__FILE__, $path_save.$filename, __FILE__));
        //save file
        $path = $path_save.$filename;
        $contents = file_get_contents($path);
        header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
        header("Content-type:   application/x-msexcel; charset=utf-8");
        header("Content-Disposition: attachment; filename=$filename"); 
        ob_end_clean();
        echo $contents;
        die();
    }

}

?>
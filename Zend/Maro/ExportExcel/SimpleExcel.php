<?php

//	$header = array("fullname:200","address:300","phone:100","email:100","created:150","date:150");
//      Business_Common_Utils::array2XmlExcelHeader(); //open new excel file
//	Business_Common_Utils::array2XmlExcelSheetHeader("Acnes User Posted", $header); //new sheet name "acnes user posted"
//	Business_Common_Utils::array2XmlExcelDataToSheet($users, $header); // push data to current sheet
//	Business_Common_Utils::array2XmlExcelSheetFooter(); // close current sheet. New sheet if needed
//	Business_Common_Utils::array2XmlExcelFooter(); // close excel file
class Maro_ExportExcel_SimpleExcel{
	
	public function export($filename, $title, $fields, $data) {
		self::array2XmlExcelHeader($filename);
		self::array2XmlExcelSheetHeader($filename , $title);
		self::array2XmlExcelDataToSheet($data, $fields);
		self::array2XmlExcelSheetFooter();
		self::array2XmlExcelFooter();	
	}

	static function array2XmlExcelHeader($reportname = "report.xls"){
            header('Content-Disposition: attachment; filename="'.$reportname.'"');
            echo '<?xml version="1.0"?>
                    <?mso-application progid="Excel.Sheet"?>
<ss:Workbook xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">
    <ss:Styles>
        <ss:Style ss:ID="1">
            <ss:Font ss:Bold="1"/>
        </ss:Style>

    </ss:Styles>';
        }

        static function array2XmlExcelSheetHeader($sheet = "Sheet1", $header = array()){
            
            if (count($header) == 0){
                //header('Content-Type: text/plain');

                echo '';
            }else{ // save data

                echo '
    <ss:Worksheet ss:Name="'.$sheet.'">
        <ss:Table>';

                for($i=0; $i<count($header); $i++){
                    $item = $header[$i];
                    $item = explode(":", $item);
                    $length = (int)$item[1];
                    if ($length>0)
                        echo '<ss:Column ss:Width="'.$length.'"/>';
                    else
                        echo '<ss:Column ss:Width="100"/>';
                }
                echo '<ss:Row ss:StyleID="1">';
                for($i=0; $i<count($header); $i++){
                    $item = $header[$i];
                    $item = explode(":", $item);
                    $field = $item[0];
                    echo '
                        <ss:Cell>
                            <ss:Data ss:Type="String">'.$field.'</ss:Data>
                        </ss:Cell>
                    ';
                }
                echo '</ss:Row>';
            }
        }

        static function array2XmlExcelDataToSheet($content, $header = array()){
                if (count($content)>0)
                foreach($content as $line){
                    
                    echo '<ss:Row>';
                    for($i=0; $i<count($header); $i++){
                        $item = $header[$i];
                        $item = explode(":", $item);
                        $field = $item[0];
                        echo '
                <ss:Cell>
                    <ss:Data ss:Type="String">'.$line[$field].'</ss:Data>
                </ss:Cell>
            ';
                    }
                    echo '</ss:Row>';
                }
                unset($content);
        }
        
        static function array2XmlExcelSheetFooter(){
            echo '</ss:Table>
            </ss:Worksheet>';
        }

        static function array2XmlExcelFooter(){
            echo '</ss:Workbook>';
        }
}


?>

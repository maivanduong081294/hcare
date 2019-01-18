<?php

//hnam define
define('BASE_PATH', realpath(dirname(__FILE__)));//public folder
define('BASE_PATH_V3', realpath(dirname(__FILE__)));//public folder
define('STATIC_BASE_PATH', BASE_PATH);//public folder
define('ROOT_PATH', realpath(dirname(__FILE__).'/../'));//base folder

if(isset($_SERVER["APP_ENV"]))
{
	define('APP_ENV',$_SERVER["APP_ENV"]);
}
else
{
	define('APP_ENV','development');
}

define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application/'));
define('ZENDLIB_PATH', ROOT_PATH.'/Zend/');
define('MODULES_PATH', ROOT_PATH.'/application/modules/');

if($_REQUEST['d'] ==10)
define('ZENDLIBRARY_PATH', 'C:/xampp/htdocs/hnamappbh_production/Zend_lib');
else
define('ZENDLIBRARY_PATH', '/zserver/lib/Zend_lib');

define('BUSINESS_PATH', ROOT_PATH.'/Business/');
define('EXCELLIBRARY_PATH', '/zserver/lib/excel/');
set_include_path(EXCELLIBRARY_PATH . PATH_SEPARATOR . get_include_path());
set_include_path(MODULES_PATH . PATH_SEPARATOR . get_include_path());
set_include_path(ZENDLIB_PATH . PATH_SEPARATOR . get_include_path());
set_include_path(ZENDLIBRARY_PATH . PATH_SEPARATOR . get_include_path());
set_include_path(BUSINESS_PATH . PATH_SEPARATOR . get_include_path());


ini_set('display_errors', '1');

try 
{
    require APPLICATION_PATH . '/bootstrap.php';  
}
catch (Exception $exception) 
{
    echo '<html><body><center>'
   . 'An exception occured while bootstrapping the application.';
	echo '<br /><br />' . $exception->getMessage() . '<br />'
	   . '<div align="left">Stack Trace:' 
	   . '<pre>' . $exception->getTraceAsString() . '</pre></div>';
    echo '</center></body></html>';
    exit(1);
}

//==================MAIN PROCESS==================

	
//timer
$start = time();
$loai_kho = array(
    '1' => 'C.NEWX',
    '2' => 'K.NEWX',
);


//==========get maping store `quynhn_mapping_store` 
$listMappingStore = Business_Addon_MappingStoreVt::getInstance()->get_list_Console();
foreach ($listMappingStore as $key => $value) {
    $ma_bo[$value['storeid']] = $value['ma_bp'];
}
$ma_bo[0] = 0;
// ============ get ds sp order detail `quynhn_order_detail` 
$listOderDetail = Business_Addon_OrderDetailVt::getInstance()->getList();
foreach ($listOderDetail as $key => $value) {
      $listItemid[] = $value['itemid']; 
    
}

//get list ma kho tam `quynhn_productsitem` 
$listMakhoTam = Business_Addon_OrderDetailVt::getInstance()->getListMakhoTam(implode(',',$listItemid));
foreach ($listMakhoTam as $key => $value) {
        $ma_kho_tam[$value['itemid']] = "K.NEWX";
        if($value['ma_kho_map'] > 0)
          $ma_kho_tam[$value['itemid']] = $loai_kho[$value['ma_kho_map']];
}
// get list id web form itemid_vt
$listIdweb = Business_Addon_ProductsColor::getInstance()->get_list_by_id_vt_console(implode(',',$listItemid));
foreach ($listIdweb as $key => $value) {
	$ma_id_web[$value['itemid_vt']] = $value['itemid'];
	$ma_vt[$value['itemid_vt']] = $value['code'];
}


//start process
$run=0;
foreach ($listOderDetail as $key => $value) {

    if ( strtoupper($value['imei']) =="") continue;

	$data = array();	    
	$data["ma_lo"] = strtoupper($value['imei']);
	$data["ma_kho"] =   $ma_bo[$value['storeid']].".".$ma_kho_tam[$value['itemid']] ;
	$data["ma_bp"] =  $ma_bo[$value['storeid']];
	$data["ma_vt"] = $ma_vt[$value['itemid']];
    $data["sl_ton"] = 1;
    $data["itemid"] = $ma_id_web[$value['itemid']];
    //var_dump( $listMa_bp[strtoupper($value['imei'])]);  
   // var_dump( $value);
   // var_dump( $data);
    //build sql insert foreach item
	 $sqls[] = buildSQLString($data);
	
	if ($run++ >= 300) {
		$_sql = implode("", $sqls);		
		Business_Addon_Tonkhoimei::getInstance()->execute($_sql);
		unset($sqls);
		unset($_sql);
		$sqls = array();
		$run=0;
    }
    

}
echo count($listOderDetail);
//var_dump( $listMa_bp);
//process remain items not in loop
$_sql = implode("", $sqls);
//var_dump($_sql);die();

Business_Addon_Tonkhoimei::getInstance()->execute($_sql);



function buildSQLString($data) {
	$pattern = "INSERT INTO `hnam_app`.`fast_tonkhoimei_tmp_vt` (`id`, `ma_lo`, `ma_kho`, `ma_bp`, `ma_vt`, `sl_ton`,`itemid`) VALUES (NULL, '__MA_LO__', '__MA_KHO__', '__MA_BP__', '__MA_VT__', '__SL_TON__','__ITEMID__');";
	$ret = str_replace("__MA_LO__", $data["ma_lo"], $pattern);
	$ret = str_replace("__MA_KHO__", $data["ma_kho"], $ret);
	$ret = str_replace("__MA_BP__", $data["ma_bp"], $ret);
	$ret = str_replace("__MA_VT__", $data["ma_vt"], $ret);
    $ret = str_replace("__SL_TON__", $data["sl_ton"], $ret);	
    $ret = str_replace("__ITEMID__", $data["itemid"], $ret);	
	return $ret;
}

switchTable();
reCheck();


function switchTable(){
	
	//rename 
	$sqlDrop = "DROP TABLE `hnam_app`.`fast_tonkhoimei_vt`";
	Business_Addon_Tonkhoimei::getInstance()->execute($sqlDrop);
	sleep(2);
	
	//rename 
	$sqlRename = "RENAME TABLE `hnam_app`.`fast_tonkhoimei_tmp_vt` TO `hnam_app`.`fast_tonkhoimei_vt`";
	Business_Addon_Tonkhoimei::getInstance()->execute($sqlRename);
		
	//create new empty table
	sleep(2);
	$sqlCreate = "CREATE TABLE `hnam_app`.`fast_tonkhoimei_tmp_vt` as SELECT * FROM `hnam_app`.`fast_tonkhoimei_vt` LIMIT 0";
    Business_Addon_Tonkhoimei::getInstance()->execute($sqlCreate);
    
    sleep(2);
	$sqlCreate = "ALTER TABLE `hnam_app`.`fast_tonkhoimei_tmp_vt`  CHANGE id id INT(11) AUTO_INCREMENT PRIMARY KEY; ";
	Business_Addon_Tonkhoimei::getInstance()->execute($sqlCreate);
}

function reCheck() {
	$totalImei = Business_Addon_Tonkhoimei::getInstance()->getTotalImeiFromMain();
	//double check main table fast_tonkhoimei not empty
	if ($totalImei==0) {
		$url = "https://www.hnammobile.com/smsbg/imei?phone=0908717169&token=ed0bac79cdc9c30da2cd3d3913e0be61&content=Loi%20dong%20bo";
		file_get_contents($url);
	}
}

$end = time();
$total_time = $end - $start;
echo "DONE\tSync data\t" . $total_time . " seconds\r\n";

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

if($_REQUEST['d'] ==10 )
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
if($_REQUEST['ma_vt'] !='' and $_REQUEST['ma_vt'] !='all')
{
	$andMaVt = " where ma_vt = '".$_REQUEST['ma_vt']."'";
	echo $_sql = "INSERT INTO `hnam_app`.`fast_tonkhoimei`(`id`, `ma_lo`, `ma_kho`, `ma_bp`, `ma_vt`, `sl_ton`,`itemid`) SELECT '',`ma_lo`, `ma_kho`, `ma_bp`, `ma_vt`, `sl_ton`,`itemid` FROM `hnam_app`.`fast_tonkhoimei_vt` $andMaVt   ";
}else if($_REQUEST['ma_vt']=='all')
{
	echo $_sql = "INSERT INTO `hnam_app`.`fast_tonkhoimei`(`id`, `ma_lo`, `ma_kho`, `ma_bp`, `ma_vt`, `sl_ton`,`itemid`) SELECT '',`ma_lo`, `ma_kho`, `ma_bp`, `ma_vt`, `sl_ton`,`itemid` FROM `hnam_app`.`fast_tonkhoimei_vt`    ";
}

$start = time();

if($_REQUEST['ma_vt'] !='')
Business_Addon_Tonkhoimei::getInstance()->execute($_sql);

reCheck();
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

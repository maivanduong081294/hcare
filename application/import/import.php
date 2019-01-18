<?php
/**  
* User Admin Import Controller
* @author: nghidv
*/ 

date_default_timezone_set('Asia/Krasnoyarsk');
error_reporting(E_ALL ^ E_NOTICE);
/************************* phan ko chinh ********************************/
//doc duong dan file hien tai
define('BASE_PATH', realpath(dirname(__FILE__).'/../../www/'));//public folder
define('BASE_PATH2', realpath(dirname(__FILE__).'/../'));//public folder
define('ROOT_PATH', realpath(dirname(__FILE__).'/../../'));//base folder

require_once 'Globals.php';
/******************** ket thuc phan ko chinh ****************************/

/******** option tuy chon ********/
//xem set can dung cac lib dung chung hay ko 
define('ZENDLIB_PATH', ROOT_PATH.'/Zend/');
set_include_path(ZENDLIB_PATH . PATH_SEPARATOR . get_include_path());
/***** end option tuy chon ********/

// autoloader - Set up autoloading.
require_once "Zend/Loader.php";
Zend_Loader::registerAutoload();

//doc file config
if(isset($_SERVER["APP_ENV"]))
{
	define('APP_ENV',$_SERVER["APP_ENV"]);
}
else
{
	define('APP_ENV','development');
}

$config_path = BASE_PATH2 . '/config/' . APP_ENV . '.global.ini';
//pre($config_path);
$config = new Zend_Config_Ini($config_path,APP_ENV);
Zend_Registry::set('configuration', $config);

if($config == null)
{
	echo "Can not load config";
	exit();
}
function pre($var){
    var_dump($var);die();
}

if (isset($_SERVER['argv'][1]))
	$parentid = $_SERVER['argv'][1];
if ((int)$parentid<0)
    die('please input cate');

$import = new import();
$import->action();
define('BASE_PATH', realpath(dirname(__FILE__).'/../../'));//public folder
class import
{		
    public function action(){
        $dir = BASE_PATH . "/uploads/news/";
//        $itemid = $this->_request->getParam('itemid',0);
            for($i=2701; $i<7000; $i++){
                $itemid = $i;
                $_news = Business_Ws_NewsItem::getInstance();
                $detail = $_news->getDetail($itemid);

                if (count($detail)>0){
                    //show picture
                    $thumb = json_decode($detail['thumb']);
                    if (!$this->isNewsImageFile($thumb->thumb1)){//not has thumb image for news
                        //get first image in news content
                        $link_image = $this->grepImages($detail['fullcontent']);
                        $title = Business_Common_Utils::adaptTitleLinkURLSEO($detail['title']);
                        if ($link_image != ''){//has found first image in news content
                            
                            
                            $img = $this->saveImage($link_image, $title);
                            
                            $ret = $this->renderImages($img);

                            //update thumbnail
        //                        {"thumb1":"6199.jpg","thumb2":"6199_large.jpg"}
                            $thumb = '{"thumb1":"'.$ret['small'].'","thumb2":"'.$ret['large'].'"}';
                            $detail['thumb'] = $thumb;
                            $_news->update($itemid, $detail['newsid'], $detail['cateid'], $detail);
                            echo $itemid . " - Done\n";           
                        }
                    }
                }
                echo $itemid . " - has Imges\n";
                
            }
    }    
    
    
    private function renderImages($img){
            $dir = BASE_PATH . "/uploads/news/";
            
            $sourceImg = $dir . $img;
            
            $ext = Business_Common_Images::get_image_extension($sourceImg);
            
            $name = Business_Common_Images::get_image_filename($img);
            
            $bgSmall = $dir . "white_100x80.png";
            $bgLarge = $dir . "white_300x215.png";
            
            $cropImage = $dir . $name."_crop.".$ext;
            $largeImage = $dir . $name."--.".$ext;
            $smallImage = $dir . $name."-.".$ext;
            
            exec("convert $sourceImg -crop +30+30 $cropImage");
            exec("convert -scale 100 $cropImage $smallImage");
            exec("convert -scale 300 $cropImage $largeImage");
            exec("composite -dissolve 50 -gravity Center $smallImage $bgSmall $smallImage");
            exec("composite -dissolve 50 -gravity Center $largeImage $bgLarge $largeImage");
            
            unlink($cropImage);
            unlink($sourceImg);
            
            return array(
                'small'=>$name."-.".$ext,
                'large'=>$name."--.".$ext
                );
            
        }
        
        private function saveImage($link, $title){
            $dir = BASE_PATH . "/uploads/news/";
            
            if (is_file($dir.$title.".jpg")){
                return $title.".jpg";
            }
                        
            $content = Business_Common_Utils::getContentByCurl($link);
            
            if (!is_dir($dir)) mkdir($dir, 0777);
            
            if ($content != ''){
                file_put_contents($dir.$title.".jpg", $content);
                return $title.".jpg";
            }
        }
        
        private function grepImages($content){            
            
            $pattern = array('|src="([^\"^{^}]+)"|',"|src='([^\'^{^}]+)'|");

            preg_match('/src="([^\"^{^}]+)"/', $content, $matches);
            if (count($matches)>0)
                return $matches[1];
            return '';
        }
        
        private function isNewsImageFile($itemid){
            $path = BASE_PATH . "uploads/news/" . $itemid .  ".jpg";
            return is_file($path);
        }
        
        
        
	public function _action(){	   
            $_feature = Business_Addon_Features::getInstance();
            $_fd = Business_Addon_Featuresdata::getInstance();
            $_products = Business_Ws_ProductsItem::getInstance();
            
            
            $pList = $_products->getListAll($productsid=3);
            
            foreach($pList as $p){
                $pid = $p['itemid'];
                $fDetail = $_feature->getListByPid($p['itemid']);

                //match 3G[2], GPRS[22]
                preg_match('/(3G|3g)(.+)/', $fDetail[1]['value'], $matches);
                if (count($matches)>0){
                    $fDetail[2]['value'] = trim($matches[0]);
                    $fDetail[22]['value'] = trim($matches[0]);
                }


                $all = $fDetail[43]['value'];

    //            3G[20]
                preg_match('/(3G|3g)(.+)/', $all, $matches);                
                if (count($matches)>0)
                    $fDetail[20]['value'] = trim($matches[2]);

    //            EDGE[20]
                preg_match('/(EDGE|edge|Edge)(.+)/', $all, $matches);                
                if (count($matches)>0)
                    $fDetail[21]['value'] = trim($matches[2]);

    //            WLAN[23]
                preg_match('/(WLAN|wlan|wifi)(.+)/', $all, $matches); 
                if (count($matches)>0)
                    $fDetail[23]['value'] = trim($matches[2]);

    //            Bluetooth[24]
                preg_match('/(Bluetooth|bluetooth|tooth)(.+)/', $all, $matches); 
                if (count($matches)>0)
                    $fDetail[24]['value'] = trim($matches[2]);

    //            USB[26]
                preg_match('/(USB|usb)(.+)/', $all, $matches); 
                if (count($matches)>0)
                    $fDetail[26]['value'] = trim($matches[2]);

    //            camera chinh [28]
                preg_match('/(Máy ảnh số|Máy ảnh|máy ảnh|Máy ảnh)(.+)/', $all, $matches); 
                if (count($matches)>0)
                    $fDetail[28]['value'] = trim($matches[2]);

    //            quay phim[30]
                preg_match('/(video|Video)(.+)/', $all, $matches); 
                if (count($matches)>0)
                    $fDetail[30]['value'] = trim(trim($matches[2]));

    //            may anh phu[31]
                preg_match('/(Máy ảnh thứ 2|Máy ảnh phụ|máy ảnh thứ 2|máy ảnh phụ)(.+)/', $all, $matches); 
                if (count($matches)>0)
                    $fDetail[31]['value'] = trim(trim($matches[2]));

    //            HĐH[33]
                preg_match('/(Hệ điều hành|hệ điều hành|operator|Operator)(.+)/', $all, $matches); 
                if (count($matches)>0)
                    $fDetail[33]['value'] = trim($matches[2]);

    //            Bộ xử lý[34]
                preg_match('/(Vi xử lý|Bộ vi xử lý|Xử lý|xử lý)(.+)/', $all, $matches); 
                if (count($matches)>0)
                    $fDetail[34]['value'] = trim($matches[2]);

    //            Trình duyệt[36]
                preg_match('/(Trình duyệt|trình duyệt)(.+)/', $all, $matches); 
                if (count($matches)>0)
                    $fDetail[36]['value'] = trim($matches[2]);

    //            Radio[37]
                preg_match('/(Nghe đài|nghe đài|Đài|đài)(.+)/', $all, $matches); 
                if (count($matches)>0)
                    $fDetail[37]['value'] = trim($matches[2]);

    //            Định vị|GPS[41]
                preg_match('/(GPS|gps|Định vị|định vị)(.+)/', $all, $matches); 
                if (count($matches)>0)
                    $fDetail[41]['value'] = trim($matches[0]);

    //            java[42]
                preg_match('/(java|JAVA|Java)(.+)/', $all, $matches); 
                if (count($matches)>0)
                    $fDetail[42]['value'] = trim($matches[0]);


                foreach($fDetail as $i){

                    if ($i['value']!=''){

                        $phone_id = $pid;
                        $parent_id = $i['parentid'];
                        $f_id = $i['fid'];
                        $detail = $_fd->getDetail($phone_id, $parent_id, $f_id);                                                                

                        if (count($detail)>0){
                            $detail['value'] = $i['value'];
                            $_fd->update($f_id, $phone_id, $parent_id, $detail);   
                        }else{

                            $detail['fid'] = $i['fid'];
                            $detail['pid'] = $pid;
                            $detail['value'] = $i['value'];
                            $detail['parentid'] = $i['parentid'];
    //                        if ($i['fid']==34) pre($detail);
                            $_fd->insert($f_id, $phone_id, $parent_id, $detail);
                        }                    
                    }

                }
                echo (++$k).'Completed '.$pid."\r\n";
                unset($fDetail, $detail, $p);
             
            }
        }

        private function getUploadFullPath(){
            $config = Zend_Registry::get('configuration');
//            $upload = "/var/hosting/ninhkhuong/www/".$config->upload_url;
            $upload = "/media/c/working/ninhkhuong/www_duc/www/".$config->upload_url;
            if (!is_dir($upload))
                mkdir($upload, 0775);
            if (!is_dir($upload."/products/"))
                mkdir($upload."/products/", 0775);
            return str_replace("//", "/", $upload."/products/");
        }
        
        private function getFile($file){            
            return file_get_contents($file);            
        }

}
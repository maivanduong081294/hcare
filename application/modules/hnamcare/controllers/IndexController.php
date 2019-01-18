<?php
class Hnamcare_IndexController extends Zend_Controller_Action
{
    private $html = 0;
    public function init()
    { 
        BlockManager::setLayout('no-sidebar');
    }

    public function indexAction()
    {
        $this->view->classBody = 'home';
        $seo = array(
            'seo_title' => 'HCare - Trung tâm bảo hành sửa chữa',
            'seo_description' => 'HCare - Trung tâm bảo hành sửa chữa uy tín, chất lượng tại TPHCM',
            'seo_keywords' => '',
        );
        SEOPlugin::setTitle($seo["seo_title"]);
        SEOPlugin::setKeywords($seo["seo_keywords"]);
        SEOPlugin::setDescriptions($seo["seo_description"]);
    }

    public function uploadImageAction() {
        $this->_helper->Layout()->disableLayout();
        if(isset($_FILES['file']) and $_FILES['file']) {
            $file = $_FILES['file'];
            $method = $this->_request->getParam('type');
            $width = $this->_request->getParam('width');
            $height = $this->_request->getParam('height');
            $this->view->result = $this->upload_image($file,$method,$width,$height);
        }
    }

    private function upload_image($file,$method='',$xwidth='',$xheight='') {
        $type = $file['type'];
        if($type == 'image/png' || $type == 'image/jpeg') {
            $image_info = getimagesize($file["tmp_name"]);
            $width = $image_info[0];
            $height = $image_info[1];
            $target_dir = "hcare/images/";
            $filename = $file["name"];
            $pos = strrpos($filename,'.');
            $ext = substr($filename,$pos+1);
            $filename = substr($filename,0,$pos);
            require_once("tinypng/autoload.php");
            require_once("tinypng/tinify/tinify/lib/Tinify/Exception.php");
            require_once("tinypng/tinify/tinify/lib/Tinify/ResultMeta.php");
            require_once("tinypng/tinify/tinify/lib/Tinify/Result.php");
            require_once("tinypng/tinify/tinify/lib/Tinify/Source.php");
            require_once("tinypng/tinify/tinify/lib/Tinify/Client.php");
            require_once("tinypng/tinify/tinify/lib/Tinify.php");
            \Tinify\setKey("DcqqGc27NAfgpcCEPKBnGBhWaykUOsRw");
            $source = \Tinify\fromFile($file["tmp_name"]);
            try{
                $target_file = $target_dir . str_replace(' ','-',strtolower($filename)).'.'.$ext;
                if(!$method) {
                    $source->toFile($target_file);
                }
                else {
                    $data = array(
                        'method' => $method,
                    );
                    $target_file = $target_dir . str_replace(' ','-',strtolower($filename));
                    if($xwidth) {
                        $data['width'] = (int) $xwidth;
                        $target_file.= '-w'.$xwidth;
                    }
                    if($xheight) {
                        $data['height'] = (int) $xheight;
                        $target_file.= '-h'.$xheight;
                    }
                    $target_file.= '.'.$ext;
                    $resized = $source->resize($data);
                    $resized->toFile($target_file);
                }
                return $target_file;
            }
            catch(Exception $e) {
                return 'Caught exception: '.$e->getMessage()."\n";
            }
        }
    }
}
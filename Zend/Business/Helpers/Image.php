<?php

class Business_Helpers_Image {

    private static $_instance = null;

    // module news to store

    function __construct() {
        
    }

    /**
     * get instance of Business_Helpers_Image
     *
     * @return Business_Helpers_Image
     */
    public static function getInstance() {
        if (self::$_instance == null) {
            self::$_instance = new Business_Helpers_Image();
        }
        return self::$_instance;
    }

    static function isURLImageURLExisted($url) {
        $cache = GlobalCache::getCacheInstance('ws');
        $key = md5($url);
        $result = $cache->getCache($key);
        if ($result===FALSE) {
            $size = getimagesize($url);
            if($size !== false){    
                $cache->setCache($key, "ok");
                return true;
            }else{
                $cache->setCache($key, "fail");
                return false;
            }
        }
        if ($result=="ok") return true;
        return false;
    }
    
    ///////////////
    static function resizeImage($src, $dst, $width, $height, $crop = 0) {
        if (!list($w, $h) = getimagesize($src))
            return "Unsupported picture type!";

        $type = self::getExtByMime($src);
        switch ($type) {
            case 'bmp': $img = imagecreatefromwbmp($src);
                break;
            case 'gif': $img = imagecreatefromgif($src);
                break;
            case 'jpg': $img = imagecreatefromjpeg($src);
                break;
            case 'png': $img = imagecreatefrompng($src);
                break;
            default : $img = imagecreatefromjpeg($src);
        }
            

        // resize
        if ($crop) {
//            if ($w < $width or $h < $height) {
//                
//            }
            $ratio = max($width / $w, $height / $h);
            $h = $height / $ratio;
            $x = ($w - $width / $ratio) / 2;
            $w = $width / $ratio;
        }
        else {
//            if ($w < $width and $h < $height) {
//                
//            }
            $ratio = min($width / $w, $height / $h);
            $width = $w * $ratio;
            $height = $h * $ratio;
            $x = 0;
        }

        $new = imagecreatetruecolor($width, $height);

        // preserve transparency
        if ($type == "gif" or $type == "png") {
            imagecolortransparent($new, imagecolorallocatealpha($new, 0, 0, 0, 127));
            imagealphablending($new, false);
            imagesavealpha($new, true);
        }

        imagecopyresampled($new, $img, 0, 0, $x, 0, $width, $height, $w, $h);

        switch ($type) {
            case 'bmp': imagewbmp($new, $dst);
                break;
            case 'gif': imagegif($new, $dst);
                break;
            case 'jpg': imagejpeg($new, $dst, 95);
                break;
            case 'png': imagepng($new, $dst);
                break;
        }
        return true;
    }
    static function resizeImage2($src, $dst, $width, $height) {
        if (!list($w, $h) = getimagesize($src))
            return "Unsupported picture type!";

        $type = self::getExtByMime($src);
        switch ($type) {
            case 'bmp': $img = imagecreatefromwbmp($src);
                break;
            case 'gif': $img = imagecreatefromgif($src);
                break;
            case 'jpg': $img = imagecreatefromjpeg($src);
                break;
            case 'png': $img = imagecreatefrompng($src);
                break;
            default : $img = imagecreatefromjpeg($src);
        }
            

        $x=0;
        $new = imagecreatetruecolor($width, $height);
        
        // preserve transparency
        if ($type == "gif" or $type == "png") {
            imagecolortransparent($new, imagecolorallocatealpha($new, 0, 0, 0, 127));
            imagealphablending($new, false);
            imagesavealpha($new, true);
        }

        imagecopyresampled($new, $img, 0, 0, $x, 0, $width, $height, $w, $h);
        //quynh
        switch ($type) {
            case 'bmp': imagewbmp($new, $dst);
                break;
            case 'gif': imagegif($new, $dst);
                break;
            case 'jpg': imagejpeg($new, $dst, 95);
                break;
            case 'png': imagepng($new, $dst);
                break;
        }
        return true;
    }
    static function waterMark($stamp, $source, $des) {
        // Load the stamp and the photo to apply the watermark to
        $stamp = imagecreatefrompng($stamp);
        $type = self::getExtByMime($source);
        switch ($type) {
            case 'bmp': $im = imagecreatefromwbmp($source);
                break;
            case 'gif': $im = imagecreatefromgif($source);
                break;
            case 'jpg': $im = imagecreatefromjpeg($source);
                break;
            case 'png': $im = imagecreatefrompng($source);
                break;
            default : return "Unsupported picture type!";
        }

//        $im = imagecreatefromjpeg($source);

        // Set the margins for the stamp and get the height/width of the stamp image
        list($w, $h) = getimagesize($source);
        $sw = 200;
        $sh = 48;
//        list($sw, $sh) = getimagesize($stamp);
        $x = ($w / 2) - ($sw / 2);
        $y = ($h / 2) - ($sh / 2);
        
            
        // Copy the stamp image onto our photo using the margin offsets and the photo 
        // width to calculate positioning of the stamp. 
//        imagecopy($im, $stamp, imagesx($im) - $sx - $marge_right, imagesy($im) - $sy - $marge_bottom, 0, 0, imagesx($stamp), imagesy($stamp));
        imagecopy($im, $stamp, $x, $y, 0, 0, imagesx($stamp), imagesy($stamp));
        switch ($type) {
            case 'bmp': imagewbmp($im, $des);
                break;
            case 'gif': imagegif($im, $des);
                break;
            case 'jpg': imagejpeg($im, $des, 95);
                break;
            case 'png': imagepng($im, $des);
                break;            
        }
        imagedestroy($im);
    }
    
    public static function getExtByMime($path) {
        $ret = getimagesize($path);
        $mime = $ret["mime"];
        switch ($mime) {
            case 'image/gif':
                return "gif";
                break;
            case 'image/jpeg':
                return "jpg";
                break;
            case 'image/png':
                return "png";
                break;
            case 'image/bmp':
                return "bmp";
                break;
        }
        return "jpg";
    }
            

    ///////
}

?>

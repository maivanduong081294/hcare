<?php

class Business_WsHelper_Utils
{
	
	
	public static function createThumbNail($filename_src, $path_dest, $filename_dest, $width = 0, $height = 0, $thumb_prefix = 'thumb_')	//return filename thumb only
	{
		
		if($width == 0 && $height == 0)
		{
			$path_parts = pathinfo($filename_dest);			
			$extension = $path_parts['extension'];
			$filename = $filename_dest;
			
			switch (strtolower($extension))
			{
				case "gif":
					$src_img = imagecreatefromgif($filename_src);
					break;
				case "png":
					$src_img = imagecreatefrompng($filename_src);
					break;
				case "jpg":
				case "jpeg":
				default:
					$src_img = imagecreatefromjpeg($filename_src);
					break;
			}
			
			if($src_img === FALSE) return "";
			
			$old_x=imagesx($src_img);
			$old_y=imagesy($src_img);
						
			$thumb_w = $old_x;
			$thumb_h = $old_y;
			
						
			$dst_img=ImageCreateTrueColor($thumb_w,$thumb_h);			
			imagecopyresampled($dst_img,$src_img,0,0,0,0,$thumb_w,$thumb_h,$old_x,$old_y);
			
			

			if(!is_dir($path_dest))
			{
				mkdir($path_dest);				
			}
			
			if (preg_match("/png/",$extension))
			{
				imagepng($dst_img,$path_dest . $filename); 
			} else {				
				imagejpeg($dst_img,$path_dest . $filename); 
			}
			imagedestroy($dst_img); 
			imagedestroy($src_img);
			return $filename;			
		}
		else
		{
			$path_parts = pathinfo($filename_dest);			
			$extension = $path_parts['extension'];
			$filename = $thumb_prefix . $filename_dest;
			
			switch (strtolower($extension))
			{
				case "gif":
					$src_img = imagecreatefromgif($filename_src);
					break;
				case "png":
					$src_img = imagecreatefrompng($filename_src);
					break;
				case "jpg":
				case "jpeg":
				default:
					$src_img = imagecreatefromjpeg($filename_src);
					break;
			}
			
			if($src_img === FALSE) return "";
      	
	      	$old_x=imagesx($src_img);
			$old_y=imagesy($src_img);
			
			$new_w = $width;
			$new_h = $height;
			
			if ($old_x > $old_y) {
				$thumb_w=$new_w;
				$thumb_h=$old_y*($new_h/$old_x);
			}
			if ($old_x < $old_y) {
				$thumb_w=$old_x*($new_w/$old_y);
				$thumb_h=$new_h;
			}
			if ($old_x == $old_y) {
				$thumb_w=$new_w;
				$thumb_h=$new_h;
			}
		
			
			$dst_img=ImageCreateTrueColor($thumb_w,$thumb_h);
			imagecopyresampled($dst_img,$src_img,0,0,0,0,$thumb_w,$thumb_h,$old_x,$old_y);
			
						
			if(!is_dir($path_dest))
			{
				mkdir($path_dest);				
			}
			
			if (preg_match("/png/",$extension))
			{
				imagepng($dst_img,$path_dest . $filename); 
			} else {
				imagejpeg($dst_img,$path_dest . $filename); 
			}
			imagedestroy($dst_img); 
			imagedestroy($src_img); 
			
			return $filename;
		}     	
		
	}

}

?>
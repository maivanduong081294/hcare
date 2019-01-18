<?php

class Business_Common_Uploads
{
        /**
    	 * get instance of Business_Common_Uploads
	 * @param config =
         *
	 */

         private $_allow = array('jpg','gif','png');
         

	function __construct($config = array('type'=>'file'))
	{
            if ($config == 'file'){
                
            }
	}	
	
	/**
	 * get instance of Business_Common_Uploads
	 * @param config = array()
         * @param
	 * @return Business_Common_Uploads
	 */
//	public static function getInstance()
//	{
//		if(self::$_instance == null)
//		{
//			self::$_instance = new Business_Common_Uploads();
//		}
//		return self::$_instance;
//	}
        static function uploads($path){
            $upload = new Zend_File_Transfer_Adapter_Http();
            $files = $upload->getFileInfo();
            // neu co file up len
            if ($files['image']['name']!="") {
                // set thu muc upload
                $upload->setDestination($path);        
                // upfile duoi 4MB va kiem tra file co phai la image
                $upload->addValidator('Filessize', false, 40000)->addValidator('IsImage', false);
                $name = $files['avatar']['name'];
                $name = str_replace('.', '-' . time() . '.', $name);
                $upload->addFilter('Rename', array(
                    'target' => $path . $name
                ));
                
                if (!$upload->isValid()) {
                    $errors[]='File không hợp lệ';
                }
                
                if(empty($errors)==true){
                    $upload->receive();
//                   move_uploaded_file($file_tmp,$newFile);
//                   if($id>0){
//                        $data["img"] = $name_img;
//                        $_order->update($id, $data);
//                    }
                }else{
                   for($i=0;$i<count($errors);$i++){
                        echo "<script>window.parent.uploadthatbai('Upload thất bại: $errors[$i]');</script>";
                        return;
                    }
                    
                }
                echo "<script>window.parent.uploadthanhcong('Upload thành công');</script>";
                
                
                
            }
        }
        

}

?>
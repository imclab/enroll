<?php

	//Full path including new filename
	$uploadfile = '../img/courses/' . rand() . basename($_FILES['image']['name']);
	$path_parts = pathinfo($uploadfile);
	$jpegversion=$path_parts['dirname'] . '/' . $path_parts['filename'] . '.jpg';

	//$originalImage=imagecreatefromjpeg(basename($_FILES['image']['tmp_name']));
	if(move_uploaded_file($_FILES['image']['tmp_name'], $uploadfile)){
		/** Resize the image to have a width of 200px **/
		$resizedImage=imagecreatetruecolor(200, 200);
		//Get current dimensions and file type
		list($width, $height,$type) = getimagesize($uploadfile);
		switch ($type){
			case 1:   //   gif -> jpg
		    	$originalImage=imagecreatefromgif($uploadfile);
		    	break;
		    case 2:   //   jpeg -> jpg
		    	$originalImage=imagecreatefromjpeg($uploadfile);
		    	break;
		   	case 3:  //   png -> jpg
		    	$originalImage=imagecreatefrompng($uploadfile);
		    	break;
		}
		if (imagecopyresampled($resizedImage, $originalImage, 0, 0, 0, 0, 200, 200, $width, $height)) {
			imagejpeg($resizedImage, $jpegversion);
			if(strcmp($path_parts['extension'], 'jpg') != 0){
				unlink($uploadfile);  //  clean up image storage
			}
			imagedestroy($resizedImage);
			imagedestroy($originalImage);
			echo $path_parts['filename'] . '.jpg'; // successful, returns filename
		} 
		else {
			echo 'error.jpg';
		}
	}
	else{
		echo 'error.png';
	}
?>
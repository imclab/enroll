<?php
  require_once '../admin/settings.php';
  //Configure and Connect to the Databse
  $con = mysql_connect($host,$db_username,$db_password);
  if (!$con) {
   	die('Could not connect: ' . mysql_error());
  }
  mysql_select_db($db, $con);
  $image=NULL;
  //CODE TO GET IMAGE, PERFORM RESIZE, SET VARIABLE WITH IMAGE NAME
  //Full path including new filename
  $uploadfile = '../img/courses/' . rand(999,99999) . $_FILES['image']['name'];
  $path_parts = pathinfo($uploadfile);
  $jpegversion=$path_parts['dirname'] . '/' . $path_parts['filename'] . '.jpg';
  if(is_uploaded_file($_FILES['image']['tmp_name'])){
    if(move_uploaded_file($_FILES['image']['tmp_name'], $uploadfile)){
      /** Resize the image to have a width of 200px **/
      $resizedImage=imagecreatetruecolor(200, 200);
      //Get current dimensions and file type
      list($width, $height,$type)=getimagesize($uploadfile);
      if($width==0 || $height==0){
        mysql_close();
        header('Location: repository_col.php?status=0');
      }
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
          default:
            header('Location: repository_col.php');
          if(!$originalImage){
            mysql_close();
            header('Location: repository_col.php?status=0');
          }
      }
      if (imagecopyresampled($resizedImage, $originalImage, 0, 0, 0, 0, 200, 200, $width, $height)) {
        if(imagejpeg($resizedImage, $jpegversion)){
          if(strcmp($path_parts['extension'], 'jpg') != 0){
            unlink($uploadfile);  //  clean up image storage
          }
          imagedestroy($resizedImage);
          imagedestroy($originalImage);
          $image=$path_parts['filename'] . '.jpg';
        }
        else{
          mysql_close();
          header('Location: repository_col.php?status=0');
        }
      } 
      else {
        mysql_close();
        header('Location: repository_col.php?status=0');
      }
    }
    else{
      mysql_close();
      header('Location: repository_col.php?status=0');
    }
  }
  else{
    $image=$_POST['originalimage'];
  }
  //CODE TO HANDLE EVERYTHING BUT IMAGE
  $name=trim($_POST['name']);
  $description=trim($_POST['description']);
  $teacher=$_POST['teacher'];
  $preferred_room=trim($_POST['preferred_room']);
  $preferred_class_size=$_POST['preferred_class_size'];
  $preferred_lunch_block=$_POST['preferred_lunch_block'];
  $freshmen=$_POST['freshmen'];
  if($freshmen==null)
    $freshmen=0;
  $sophomores=$_POST['sophomores'];
  if($sophomores==null)
    $sophomores=0;
  $juniors=$_POST['juniors'];
  if($juniors==null)
    $juniors=0;
  $seniors=$_POST['seniors'];
  if($seniors==null)
    $seniors=0;  	
  $mysql_id=null;
  $delete=null;
  if($_POST['existing']){
    $mysql_id=$_POST['mysql_id'];
    $delete=$_POST['delete'];
    if(strcmp($delete,'n') == 0){
      //Insert Data into mysql
      mysql_query("UPDATE colloquiums SET name='$name',description='$description',
          image='$image',teacher_id=$teacher,preferred_room='$preferred_room',
          preferred_class_size=$preferred_class_size,preferred_lunch_block='$preferred_lunch_block',
          freshmen='$freshmen',sophomores='$sophomores',juniors='$juniors',seniors='$seniors'
          WHERE id='$mysql_id'");
      mysql_close($con);
      header('Location: repository_col.php?status=1');
    }
    else{
      mysql_query("DELETE FROM colloquiums WHERE id=$mysql_id LIMIT 1");
      mysql_close($con);
      header('Location: repository_col.php?status=1');
    } 
  }
  else{
    mysql_query("INSERT INTO colloquiums(
            name,description,image,teacher_id,preferred_room,preferred_class_size,
            preferred_lunch_block,freshmen,sophomores,juniors,seniors) 
            VALUES('$name','$description','$image',$teacher,'$preferred_room',
                $preferred_class_size,'$preferred_lunch_block',$freshmen,$sophomores,
                $juniors,$seniors)");
    mysql_close($con);
    header('Location: repository_col.php?status=1');
  }
?>

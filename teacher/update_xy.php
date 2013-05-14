<?php
  require_once '../admin/db.php';
	//Configure and Connect to the Databse
	 $con = mysql_connect($host,$db_username,$db_password);
   if (!$con) {
 		die('Could not connect: ' . mysql_error());
 	}
 	mysql_select_db($db, $con);
 	//Pull data from manage_courses.php front-end page
 	$mysql_id = trim($_POST['mysql_id']);
 	$name=trim($_POST['name']);
 	$description=trim($_POST['description']);
 	$image=$_POST['uploadedImg'];
 	$category=$_POST['category'];
 	$teacher=$_POST['teacher'];
 	$preferred_room=trim($_POST['preferred_room']);
 	$preferred_class_size=$_POST['preferred_class_size'];
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
  $delete=$_POST['delete'];
  $result = "";	
  if(strcmp($delete,"n") == 0){
    //Insert Data into mysql
    $query=mysql_query("UPDATE xy SET name='$name',description='$description',
    image='$image',category=$category,teacher_id=$teacher,preferred_room='$preferred_room',
    preferred_class_size=$preferred_class_size,
    freshmen=$freshmen,sophomores=$sophomores,juniors=$juniors,seniors=$seniors
    WHERE id=$mysql_id");
    if($query)
      echo "Course updated!";
    else
      echo "Update failed!"; 
  }
  else{
    $query=mysql_query("DELETE FROM xy WHERE id='$mysql_id' LIMIT 1");
    if($query)
      echo "Course deleted!";
    else
      echo "Delete failed!";
  } 

	mysql_close($con);
?>

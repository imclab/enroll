<?php
	require_once '../admin/db.php';
	//Configure and Connect to the Databse
	 $con = mysql_connect($host,$db_username,$db_password);
 	if (!$con) {
 		die('Could not connect: ' . mysql_error());
 	}
 	mysql_select_db($db, $con);
 	//Pull data from manage_courses.php front-end page
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
 	//Insert Data into mysql
	$query=mysql_query("INSERT INTO xy(
			name,description,image,category,teacher_id,preferred_room,preferred_class_size,
			freshmen,sophomores,juniors,seniors) 
			VALUES('$name','$description','$image',$category,'$teacher','$preferred_room',
			$preferred_class_size,$freshmen,$sophomores,$juniors,$seniors)");
	mysql_close($con);
  	if($query){
		echo "Course added!";
   	}
	else{
		echo "An error occurred!$image";
	}
?>

<?php
	require_once '../admin/db.php';
	//Configure and Connect to the Databse
	$con = mysql_connect($host,$db_username,$db_password);
 	if (!$con) {
 		die('Could not connect: ' . mysql_error());
 	}
 	mysql_select_db($db, $con);
 	$xy_id=trim($_POST['xy_id']);
 	$date_id=trim($_POST['date_id']);
 	$notes=trim($_POST['notes']);
 	$teacher=trim($_POST['teacher']);
 	$blockpreference=trim($_POST['blockpreference']);
	$existing=$_POST['existing'];
 	if($existing){
 		//Update MySQL Entry
 		$query=mysql_query("UPDATE xy_assignments SET xy_id=$xy_id,notes='$notes',
 				preferred_block='$blockpreference' WHERE date_id=$date_id");
 	}
 	else{
		//Insert Data into MySQL
		$query=mysql_query("INSERT INTO xy_assignments(xy_id,date_id,notes,teacher,preferred_block) 
							VALUES($xy_id,$date_id,'$notes','$teacher','$blockpreference')");
 	}
 	mysql_close($con);
  	if($query){
		echo "Successfully Updated!";
   	}
	else{ 
		echo $_POST['existing']; 
	}
?>
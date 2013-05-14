<?php
	require_once '../admin/db.php';
	//Configure and Connect to the Databse
	$con = mysql_connect($host,$db_username,$db_password);
 	if (!$con) {
 		die('Could not connect: ' . mysql_error());
 	}
 	mysql_select_db($db, $con);
 	$semester=trim($_POST['semester']);
 	$duration=trim($_POST['duration']);
 	$notes=trim($_POST['notes']);
 	$c_id=trim($_POST['c_id']);
 	$teacher=trim($_POST['teacher']);
 	$existing=$_POST['existing'];
 	if(strcmp($existing, "true") == 0){
 		$assnID=$_POST['assnID'];
 		//Update MySQL Entry
 		$query=mysql_query("UPDATE c_assignments SET duration='$duration',semester='$semester',
 				c_id='$c_id',notes='$notes' WHERE id='$assnID'");
 		if(strcmp($duration, "y") == 0){
 			$delquery=mysql_query("DELETE FROM c_assignments WHERE semester='2' AND teacher='$teacher' LIMIT 1");
 		}
 	}
 	else{
 		//Insert Data into MySQL
		$query=mysql_query("INSERT INTO c_assignments(duration,semester,c_id,notes,teacher) 
							VALUES('$duration','$semester','$c_id','$notes','$teacher')");
 	}
  	if($query){
		echo "Successfully Updated!";
   	}
	else{ 
		echo "An error occurred!"; 
	}
	mysql_close($con);
?>
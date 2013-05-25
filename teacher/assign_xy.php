<?php
	require_once '../admin/settings.php';
	//Configure and Connect to the Databse
	$con = mysql_connect($host,$db_username,$db_password);
 	if (!$con) {
 		die('Could not connect: ' . mysql_error());
 	}
 	mysql_select_db($db, $con);
 	$xy_id=trim($_POST['xy_id']);
 	$date_id=trim($_POST['date_id']);
 	$notes=trim($_POST['notes']);
 	$teacher=trim($_POST['teacher_id']);
 	$blockpreference=trim($_POST['blockpreference']);
 	$existing=NULL;
	//Check to see if assignment already exists
	$existing_assignment_result=mysql_query("SELECT id FROM xy_assignments WHERE date_id=$date_id AND teacher_id=$teacher");
	$existing_assignment_row=mysql_fetch_assoc($existing_assignment_result);
	if(empty($existing_assignment_row['id']))
		$existing=false;
	else
		$existing=true;

 	if($existing){
 		//Update MySQL Entry
 		$query=mysql_query("UPDATE xy_assignments SET xy_id=$xy_id,notes='$notes',
 				preferred_block='$blockpreference' WHERE date_id=$date_id");
	  	if($query){
	  		mysql_close($con);
			echo "Successfully Updated!";
	   	}
		else{ 
			mysql_close($con);
			echo "Failed!"; 
		}
 	}
 	else{
		//Insert Data into MySQL
		$query=mysql_query("INSERT INTO xy_assignments(xy_id,date_id,notes,teacher_id,preferred_block) 
							VALUES($xy_id,$date_id,'$notes',$teacher,'$blockpreference')");
	  	if($query){
	  		mysql_close($con);
			echo "XY Assigned!";
	   	}
		else{ 
			mysql_close($con);
			echo "Failed!";
		}
 	}
 	mysql_close($con);
?>
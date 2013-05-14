<?php

	require_once 'admin/db.php';
	//Configure and Connect to the Databse
	$con = mysql_connect($host,$db_username,$db_password);
 	if (!$con) {
 		die('Could not connect: ' . mysql_error());
 	}
 	mysql_select_db($db, $con);

 	$type=trim($_POST['type']);
 	$courseid=trim($_POST['courseid']);
 	$class_size=trim($_POST['class_size']);
 	$username=trim($_POST['username']);

 	//Get the user's id
 	$userid_result = mysql_query("SELECT id from users WHERE username=\"$username\"") or die(mysql_error());
 	$userid_array = mysql_fetch_array($userid_result);
 	$userid = $userid_array['id'];

 	if(strcmp($type, "colloquium") == 0){
 		//Get the current number of registrations for the course
 		$numberRegistrations_result = mysql_query("SELECT COUNT(*) AS count FROM `c_enrollments` WHERE c_assignments_id=\"$courseid\"") or die(mysql_error());
 	}
 	else if(strcmp($type, "xy") == 0){
 		//Get the current number of registrations for the course
 		$numberRegistrations_result = mysql_query("SELECT COUNT(*) AS count FROM `xy_enrollments` WHERE xy_assignments_id=\"$courseid\"") or die(mysql_error());
 	}
 	
 	//Spots left
 	$spots_left = $class_size - $numberRegistrations_result;

 	if($spots_left < 1){
 		echo "Course is full! Unable to enroll.";
 	}
 	else if($spots_left > 0 && strcmp($type, "colloquium") == 0){
 		$register_result=mysql_query("INSERT INTO c_enrollments(c_assignments_id,users_id) VALUES('$courseid','$userid')") or die(mysql_error());
 		if($register_result){
 			$numberRegistrations_result = mysql_query("SELECT COUNT(*) AS count FROM `c_enrollments` WHERE c_assignments_id=$courseid") or die(mysql_error());
 			$spots_left = $class_size - $numberRegistrations_result;
 			if($spots_left < 0){
 				$delete_result=mysql_query("DELETE FROM c_enrollments WHERE c_assignments_id='$courseid' AND users_id='$userid' LIMIT 1");
 				echo "Course is full! Unable to enroll.";
 			}
 			else{
 				echo "Registration Successful!";
 			}
 		}
 		else{
 			echo "Error! Please try again.";
 		}
 	}
 	else if($spots_left > 0 && strcmp($type, "xy") == 0){
 		$register_result=mysql_query("INSERT INTO xy_enrollments(xy_assignments_id,users_id) VALUES('$courseid','$userid')") or die(mysql_error());
 		if($register_result){
 			$numberRegistrations_result = mysql_query("SELECT COUNT(*) AS count FROM `xy_enrollments` WHERE xy_assignments_id=$courseid") or die(mysql_error());
 			$spots_left = $class_size - $numberRegistrations_result;
 			if($spots_left < 0){
 				$delete_result=mysql_query("DELETE FROM xy_enrollments WHERE xy_assignments_id='$courseid' AND users_id='$userid' LIMIT 1");
 				echo "Course is full! Unable to enroll.";
 			}
 			else{
 				echo "Registration Successful!";
 			}
 		}
 		else{
 			echo "Error! Please try again.";
 		}
 	}
 	else{
 		echo "Error! Please try again.";
 	}
	mysql_close($con);
?>

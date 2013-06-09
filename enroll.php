<?php
	require_once 'admin/settings.php';
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
 	$spots_left=$class_size - $numberRegistrations_result;
 	if($spots_left < 1){
 		if(strcmp($type, "colloquium") == 0){
	 		mysql_close($con);
	 		header('Location: colloquium.php?status=3');
	 	}
	 	else if(strcmp($type, "xy") == 0){
	 		mysql_close($con);
	 		header('Location: xy.php?status=3');
	 	}
 	}
 	//If there are spots left and it is a colloquium
 	else if($spots_left > 0 && strcmp($type, "colloquium") == 0){
 		if(mysql_query("INSERT INTO c_enrollments(c_assignments_id,users_id) VALUES('$courseid','$userid')")){
 			$numberRegistrations_result = mysql_query("SELECT COUNT(*) AS count FROM `c_enrollments` WHERE c_assignments_id=$courseid") or die(mysql_error());
 			$spots_left=$class_size - $numberRegistrations_result;
 			//Course over limit, remove student
 			if($spots_left < 0){
 				$delete_result=mysql_query("DELETE FROM c_enrollments WHERE c_assignments_id=$courseid AND users_id=$userid LIMIT 1");
 				mysql_close($con);
 				header('Location: colloquium.php?status=3');
 			}
 			//Student successfully registered in a colloquium
 			else{
 				//Add to activity log
 				mysql_query("INSERT INTO c_activity(date,primary_user_id,c_assignments_id,activity) 
 							 VALUES(NOW(),$userid,$courseid,'enroll')");
 				mysql_close($con);
 				header('Location: colloquium.php?status=1');
 			}
 		}
 		else{
 			mysql_close($con);
 			header('Location: colloquium.php?status=0');
 		}
 	}
 	//If there are spots left and it is a XY
 	else if($spots_left > 0 && strcmp($type, "xy") == 0){
 		if(mysql_query("INSERT INTO xy_enrollments(xy_assignments_id,users_id) VALUES('$courseid','$userid')")){
 			$numberRegistrations_result = mysql_query("SELECT COUNT(*) AS count FROM `xy_enrollments` WHERE xy_assignments_id=$courseid") or die(mysql_error());
 			$spots_left = $class_size - $numberRegistrations_result;
 			//Course over limit, remove student
 			if($spots_left < 0){
 				$delete_result=mysql_query("DELETE FROM xy_enrollments WHERE xy_assignments_id='$courseid' AND users_id='$userid' LIMIT 1");
 				mysql_close($con);
 				header('Location: xy.php?status=3');
 			}
 			//Student successfully registered in a XY
 			else{
 				//Add to activity log
 				mysql_query("INSERT INTO xy_activity(date,primary_user_id,xy_assignments_id,activity) 
 							 VALUES(NOW(),$userid,$courseid,'enroll')");
 				mysql_close($con);
 				header('Location: xy.php?status=1');
 			}
 		}
 		else{
 			mysql_close($con);
 			header('Location: xy.php?status=0');
 		}
 	}
 	else{
 		mysql_close($con);
 		header('Location: xy.php?status=0');
 	}
	mysql_close($con);
?>

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
 	$username=trim($_POST['username']);
 	//Get the user's id
 	$userid_result = mysql_query("SELECT id from users WHERE username=\"$username\"") or die(mysql_error());
 	$userid_array = mysql_fetch_array($userid_result);
 	$userid = $userid_array['id'];

 	if(strcmp($type, "colloquium") == 0){
 		$remove_result=mysql_query("DELETE FROM c_enrollments WHERE c_assignments_id='$courseid' AND users_id='$userid' LIMIT 1");
 		//Add to activity log
 		mysql_query("INSERT INTO c_activity(date,primary_user_id,c_assignments_id,activity) 
 					 VALUES(NOW(),$userid,$courseid,'unenroll')");
 	}
 	else if(strcmp($type, "xy") == 0){
 		$remove_result=mysql_query("DELETE FROM xy_enrollments WHERE xy_assignments_id='$courseid' AND users_id='$userid' LIMIT 1");
 		//Add to activity log
 		mysql_query("INSERT INTO xy_activity(date,primary_user_id,xy_assignments_id,activity) 
 					 VALUES(NOW(),$userid,$courseid,'unenroll')");
 	}
	if($remove_result){
		echo "Course removed!";
 	}
 	else{
 		echo "Error! Please try again.";
 	}
	mysql_close($con);
?>

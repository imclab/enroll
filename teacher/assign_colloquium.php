<?php
	require_once '../admin/db.php';
	//Configure and Connect to the Databse
	$con = mysql_connect($host,$db_username,$db_password);
 	if (!$con) {
 		die('Could not connect: ' . mysql_error());
 	}
 	mysql_select_db($db, $con);
 	$semester=$_POST['semester'];
 	$duration=trim($_POST['duration']);
 	$previous_duration=trim($_POST['previous_duration']);
 	$notes=trim($_POST['notes']);
 	$c_id=trim($_POST['c_id']);
 	$teacher=trim($_POST['teacher']);
 	$existing=$_POST['existing'];
 	$result;
 	if($existing){
 		//Update MySQL Entry
 		if(mysql_query("UPDATE c_assignments SET duration='$duration',
 				c_id='$c_id',notes='$notes' WHERE semester=$semester AND teacher_id=$teacher")){
 			if(strcmp($duration, "y") == 0){
 				if(mysql_query("DELETE FROM c_assignments WHERE semester='2' AND teacher_id='$teacher' LIMIT 1")){
 					$result="Course Updated!";
 				}
 				else{
 					$result="An error occurred!";
 				}
 			}
 			else if(strcmp($previous_duration, "y") == 0 && strcmp($duration, "s") == 0){
 				$result="Course Updated!";
 			}
 			else{
 				$result="Successfully Updated!";
 			}
 		}
 		else{
 			$result="An error occurred!";
 		}
 	}
 	else{
 		//Insert Data into MySQL
		if(mysql_query("INSERT INTO c_assignments(duration,semester,c_id,notes,teacher_id) 
							VALUES('$duration','$semester','$c_id','$notes','$teacher')")){
			$result="Successfully Updated!";
		}

 	}
	echo $result;
	mysql_close($con);
?>
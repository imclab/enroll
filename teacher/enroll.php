<?php
  require_once '../admin/settings.php';
  //Configure and Connect to the Databse
  $con = mysql_connect($host,$db_username,$db_password);
  if (!$con) {
   	die('Could not connect: ' . mysql_error());
  }
  mysql_select_db($db, $con);
  $studentid=$_POST['id'];
  $teacherid=$_POST['teacher_id'];
  $status=0;
  if(strcmp($_POST['type'],"colloquium")==0){
    $semester=$_POST['semester'];
    $col_id=$_POST['col_id'];
    //Check to see if user is already enrolled
    $check_enrollment_result=mysql_query(
        "SELECT c_enrollments.id,c_assignments.semester
         FROM c_enrollments
         INNER JOIN `c_assignments` on c_enrollments.c_assignments_id=c_assignments.id 
         WHERE users_id=$studentid") or die(mysql_error());
    if (mysql_num_rows($check_enrollment_result) == 0){
      //Enroll user
      mysql_query("INSERT INTO c_enrollments(c_assignments_id,users_id) VALUES($col_id,$studentid)");
      //Add to activity log
      mysql_query("INSERT INTO c_activity(date,primary_user_id,secondary_user_id,c_assignments_id,activity) 
             VALUES(NOW(),$teacherid,$studentid,$col_id,'enroll')");
      mysql_close($con);
      $status=1;
    }
    else{
      $status=3;
    }
    header("Location: preenroll_col.php?semester=$semester&status=$status");
  }
  elseif(strcmp($_POST['type'],"xy")==0){
    $xy_id=$_POST['xy_id'];
    //Check to see if user is already enrolled
    $check_enrollment_result=mysql_query(
        "SELECT xy_enrollments.id,xy_assignments.date_id
         FROM xy_enrollments
         INNER JOIN `xy_assignments` on xy_enrollments.xy_assignments_id=xy_assignments.id 
         WHERE users_id=$studentid") or die(mysql_error());
    if (mysql_num_rows($check_enrollment_result) == 0){
      //Enroll user
      mysql_query("INSERT INTO xy_enrollments(xy_assignments_id,users_id) VALUES($xy_id,$studentid)");
      //Add to activity log
      mysql_query("INSERT INTO xy_activity(date,primary_user_id,secondary_user_id,xy_assignments_id,activity) 
             VALUES(NOW(),$teacherid,$studentid,$xy_id,'enroll')");
      mysql_close($con);
      $status=1;
    }
    else{
      $status=3;
    }
    header("Location: preenroll_xy.php?#$xy_id");
  }
?>

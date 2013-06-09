<?php
  require_once '../admin/settings.php';
  //Configure and Connect to the Databse
  $con = mysql_connect($host,$db_username,$db_password);
  if (!$con) {
   	die('Could not connect: ' . mysql_error());
  }
  mysql_select_db($db, $con);
  $id=$_POST['id'];
  if(strcmp($_POST['type'],"colloquium")==0){
      $studentid=$_POST['student_id'];
      $teacherid=$_POST['teacher_id'];
      $colid=$_POST['col_id'];
      $semester=$_POST['semester'];
      //Add to activity log
      mysql_query("INSERT INTO c_activity(date,primary_user_id,secondary_user_id,c_assignments_id,activity) 
             VALUES(NOW(),$teacherid,$studentid,$colid,'unenroll')") or die(mysql_error());
      mysql_query("DELETE FROM c_enrollments WHERE id=$id LIMIT 1");
      mysql_close($con);
      header("Location: enroll_col.php?semester=$semester#$id");
  }
  elseif(strcmp($_POST['type'],"xy")==0){
      mysql_query("DELETE FROM xy_enrollments WHERE id=$id LIMIT 1");
      mysql_close($con);
      header("Location: enroll_xy.php#$id");
  }
?>

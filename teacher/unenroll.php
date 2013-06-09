<?php
  require_once '../admin/settings.php';
  //Configure and Connect to the Databse
  $con = mysql_connect($host,$db_username,$db_password);
  if (!$con) {
   	die('Could not connect: ' . mysql_error());
  }
  mysql_select_db($db, $con);
  $id=$_POST['id'];
  $studentid=$_POST['student_id'];
  $teacherid=$_POST['teacher_id'];
  if(strcmp($_POST['type'],"colloquium")==0){
      $semester=$_POST['semester'];
      $col_id=$_POST['col_id'];
      mysql_query("DELETE FROM c_enrollments WHERE id=$id LIMIT 1");
      //Add to activity log
      mysql_query("INSERT INTO c_activity(date,primary_user_id,secondary_user_id,c_assignments_id,activity) 
             VALUES(NOW(),$teacherid,$studentid,$col_id,'unenroll')");
      mysql_close($con);
      header("Location: preenroll_col.php?semester=$semester");
  }
  elseif(strcmp($_POST['type'],"xy")==0){
      $xy_id=$_POST['xy_id'];
      mysql_query("DELETE FROM xy_enrollments WHERE id=$id LIMIT 1");
      mysql_query("INSERT INTO xy_activity(date,primary_user_id,secondary_user_id,xy_assignments_id,activity) 
             VALUES(NOW(),$teacherid,$studentid,$xy_id,'unenroll')");
      mysql_close($con);
      header("Location: preenroll_xy.php#$id");
  }
?>

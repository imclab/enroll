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
      $semester=$_POST['semester'];
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

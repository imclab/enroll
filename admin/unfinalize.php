<?php
  require_once 'settings.php';
  //Configure and Connect to the Databse
  $con = mysql_connect($host,$db_username,$db_password);
  if (!$con) {
   	die('Could not connect: ' . mysql_error());
  }
  mysql_select_db($db, $con);
  $id=$_POST['id'];
  if(strcmp($_POST['type'],"colloquium")==0){
    $semester=$_POST['semester'];
    if(!$_POST['keepstudents']){
      mysql_query("DELETE FROM c_enrollments WHERE c_assignments_id=$id");
    }
    //Update Data in MySQL
    mysql_query("UPDATE c_assignments SET final=0 WHERE id=$id");
    mysql_close($con);
    header("Location: approvals_col.php?semester=$semester");
  }
  else if(strcmp($_POST['type'],"xy")==0){
    if(!$_POST['keepstudents']){
      mysql_query("DELETE FROM xy_enrollments WHERE xy_assignments_id=$id");
    }
    //Update Data in MySQL
    mysql_query("UPDATE xy_assignments SET final=0 WHERE id=$id");
    mysql_close($con);
    header("Location: approvals_xy.php?#$id");
  }
?>

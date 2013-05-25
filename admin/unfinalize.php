<?php
  require_once '../admin/db.php';
  //Configure and Connect to the Databse
  $con = mysql_connect($host,$db_username,$db_password);
  if (!$con) {
   	die('Could not connect: ' . mysql_error());
  }
  mysql_select_db($db, $con);
  $id=$_POST['id'];
  if(strcmp($_POST['type'],"colloquium")==0){
    $semester=$_POST['semester'];
    //Update Data in MySQL
    mysql_query("UPDATE c_assignments SET final=0 WHERE id=$id");
    mysql_close($con);
    header("Location: approvals_col.php?semester=$semester");
  }
  else if(strcmp($_POST['type'],"xy")==0){
    //Update Data in MySQL
    mysql_query("UPDATE xy_assignments SET final=0 WHERE id=$id");
    mysql_close($con);
    header("Location: approvals_xy.php?#$id");
  }
?>

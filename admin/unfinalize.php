<?php
  require_once '../admin/db.php';
  //Configure and Connect to the Databse
  $con = mysql_connect($host,$db_username,$db_password);
  if (!$con) {
   	die('Could not connect: ' . mysql_error());
  }
  mysql_select_db($db, $con);
  $id=$_POST['id'];
  $semester=$_POST['semester'];
  if(strcmp($_POST['type'],"colloquium")==0){
    //Update Data in MySQL
    mysql_query("UPDATE c_assignments SET final=0 WHERE id=$id");
    mysql_close($con);
    header("Location: approvals_col.php?semester=$semester");
  }
?>

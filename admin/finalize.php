<?php
  require_once '../admin/db.php';
  //Configure and Connect to the Databse
  $con = mysql_connect($host,$db_username,$db_password);
  if (!$con) {
   	die('Could not connect: ' . mysql_error());
  }
  mysql_select_db($db, $con);
  if(strcmp($_POST['type'],"colloquium")==0){
    $lunch_block=$_POST['lunch_block'];
  }
  $room=trim($_POST['room']);
  $class_size=$_POST['class_size'];
  $id=$_POST['id'];
  $semester=$_POST['semester'];
  if(strcmp($_POST['type'],"colloquium")==0){
    //Update Data in MySQL
    mysql_query("UPDATE c_assignments SET room='$room',
        class_size=$class_size,lunch_block='$lunch_block',final=1
        WHERE id=$id");
    mysql_close($con);
    header("Location: approvals_col.php?semester=$semester");
  }
?>

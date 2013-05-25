<?php
  require_once 'settings.php';
  //Configure and Connect to the Databse
  $con = mysql_connect($host,$db_username,$db_password);
  if (!$con) {
   	die('Could not connect: ' . mysql_error());
  }
  mysql_select_db($db, $con);
  $room=trim($_POST['room']);
  $class_size=$_POST['class_size'];
  $id=$_POST['id'];
  if(strcmp($_POST['type'],"colloquium")==0){
    $semester=$_POST['semester'];
    $lunch_block=$_POST['lunch_block'];
    //Update Data in MySQL
    mysql_query("UPDATE c_assignments SET room='$room',
        class_size=$class_size,lunch_block='$lunch_block',final=1
        WHERE id=$id");
    mysql_close($con);
    header("Location: approvals_col.php?semester=$semester");
  }
  else if(strcmp($_POST['type'],"xy")==0){
    $block=$_POST['block'];
    //Update Data in MySQL
    mysql_query("UPDATE xy_assignments SET room='$room',
        class_size=$class_size,block='$block',final=1
        WHERE id=$id");
    mysql_close($con);
    header("Location: approvals_xy.php?#$id");
  }
?>

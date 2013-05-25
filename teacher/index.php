<?php
session_start();
//Credentials aren't legit or user isn't an admin, kick back to login screen
if (!isset($_SESSION['username']) || 
  	$_SESSION['login']!=true ) {
    $_SESSION['from_teacher']=true;
    header("Location: ../login.html");
}
else if(isset($_SESSION['username']) && $_SESSION['teacher']){
	header("Location: agenda.php");
}
else if(isset($_SESSION['username']) && $_SESSION['student']){
	header("Location: ../xy.php");
}

?>
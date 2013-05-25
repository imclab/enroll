<?php
	session_start();
	require_once 'settings.php';
	//Configure and Connect to the Databse
	$con = mysql_connect($host,$db_username,$db_password);
 	if (!$con) {
 		die('Could not connect: ' . mysql_error());
 	}
	mysql_select_db($db, $con);
	$ghostuser=$_POST['username'];
	$get_userrole_result=mysql_query("SELECT role FROM users WHERE username=\"$ghostuser\"") or die(mysql_error());
	$get_userrole_array=mysql_fetch_array($get_userrole_result);
	$ghostrole=$get_userrole_array['role'];
	$_SESSION['ghostuser']=$ghostuser;
	$_SESSION['ghostrole']=$ghostrole;
	if(strcmp($_SESSION['ghostuser'],$_SESSION['username']) == 0){
		$_SESSION['ghostuser']=null;
		$_SESSION['ghostrole']=null;
	}
	mysql_close();
	echo $ghostrole;
?>
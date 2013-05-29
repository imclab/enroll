<?php

	session_start();
	if (!(isset($_SESSION['login']) && $_SESSION['login'] != ''))
		header ("Location: xy.php");
	else if(strcmp($_SESSION['ghostrole'],"admin")==0)
		header ("Location: admin/index.php");
	else if(strcmp($_SESSION['ghostrole'],"teacher")==0)
		header ("Location: teacher/agenda.php");
	else if($_SESSION['admin'])
		header ("Location: admin/index.php");
	else if($_SESSION['teacher'])
		header ("Location: teacher/agenda.php");
	else
		header ("Location: xy.php");
?>

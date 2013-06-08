<?php
	session_start();
	include_once 'admin/settings.php';
	$username=$_POST["username"];
	$pwd=$_POST["password"];
	//either username or password are not set
	if (!isset($username) && !isset($pwd)) {
		header ("Location: login.html");
	}
	//username and password are not null
	else {
		$ldap_connection=ldap_connect($ldap_server) or die("Error while connecting to LDAP server.");
		if (is_resource($ldap_connection)) {
			//Try binding to LDAP server with credentials
			if ($bind = ldap_bind($ldap_connection,$username.$ldap_user_suffix,$pwd)) {
				$filter = "(samAccountName=" . $username . ")";
				$attrs = array("givenname","sn");
				//grab the group membership info
				$result = ldap_search($ldap_connection, $ldap_search_ou, $filter, $attrs);
				$entries = ldap_get_entries($ldap_connection, $result);
				if ($entries['count'] > 0){
					$_SESSION['login']=true;
					$_SESSION['username']=$username;
					$_SESSION['ghostrole']=null;
					//finds role
					//Connects to MySQL and Selects Database
					$con = mysql_connect($host,$db_username,$db_password);
					if (!$con)
					  die('Could not connect: ' . mysql_error());
					//Select DB
					mysql_select_db($db, $con);
					$get_user_role_result=mysql_query(
					  "SELECT role,secondary_role,ghost_user FROM users WHERE username='$username' LIMIT 1") or die(mysql_error());
					$get_user_role_array=mysql_fetch_array($get_user_role_result);
					$_SESSION['secondary_role']=$get_user_role_array['secondary_role'];
					$_SESSION['firstname']=$entries[0]["givenname"][0];
					$_SESSION['lastname']=$entries[0]["sn"][0];
					$_SESSION['ghostuser']=$get_user_role_array['ghost_user'];
					if(isset($get_user_role_array['ghost_user'])){
						$ghost_username=$get_user_role_array['ghost_user'];
						$get_ghost_user_details=mysql_query(
					  		"SELECT * FROM users WHERE username='$ghost_username' LIMIT 1") or die(mysql_error());
						while($row=mysql_fetch_array($get_ghost_user_details)){
							$_SESSION['secondary_role']=$row['secondary_role'];
							$_SESSION['firstname']=$row['firstname'];
							$_SESSION['lastname']=$row['lastname'];
							$_SESSION['username']=$row['username'];
						}
					}
					//If role is administator, set session variable
					if(strcmp($get_user_role_array['role'], 'admin')==0 || 
					   strcmp($get_user_role_array['secondary_role'], 'admin')==0)
						$_SESSION['admin']=true;
					else
						$_SESSION['admin']=false;
					//If role is teacher, set session variable
					if(strcmp($get_user_role_array['role'], 'teacher') == 0 ||
						strcmp($get_user_role_array['secondary_role'], 'teacher') == 0)
						$_SESSION['teacher']=true;
					else
						$_SESSION['teacher']=false;
					//If role is student, set session variable
					if(strcmp($get_user_role_array['role'], 'student') == 0){
						$_SESSION['student']=true;
					}
					else{
						$_SESSION['student']=false;
					}
					if ($_SESSION['admin'])
						header ("Location: admin/index.php");
					else if ($_SESSION['teacher'])
						header ("Location: teacher/agenda.php");
					else if ($_SESSION['student'])
						header ("Location: xy.php");
					else
						header ("Location: xy.php");
				}
				//credentials are probably invalid
				else {
					header("Location: login.html");
				}
			}
			//LDAP connection failed
			else {
				header("Location: login.html");
			}
		}
	}
?>
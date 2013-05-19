<?php
session_start();
$username=$_POST["username"];
$pwd=$_POST["password"];
//either username or password are not set
if (!isset($username) && !isset($pwd)) {
	header ("Location: login.html");
}
//username and password are not null
else {


	$ldap_connection=ldap_connect("ldap://il-idc02.instr.cps.k12.il.us/") or die("Error while connecting to LDAP server.");
	if (is_resource($ldap_connection)) {

		//Try binding to LDAP server with credentials
		if ($bind = ldap_bind($ldap_connection,$username."@instr.cps.k12.il.us",$pwd)) {
			$filter = "(samAccountName=" . $username . ")";
			$attrs = array("memberOf","givenname","sn");
			//grab the group membership info
			$result = ldap_search($ldap_connection, "OU=Users,OU=nscollege-1740,OU=school-high,DC=instr,DC=cps,DC=k12,DC=il,DC=us", $filter, $attrs);
			$entries = ldap_get_entries($ldap_connection, $result);

			if ($entries['count'] > 0){
				$_SESSION['login']=true;
				$_SESSION['username']=$username;

				//finds role based on AD group
				$role_array_key = array_search('GG-1740-Registration-', $entries[0]["memberof"]);
				$role = substr($entries[0]["memberof"][$role_array_key], 3, strpos($entries[0]["memberof"][$role_array_key], ',') - 3);
				$_SESSION['firstname']=$entries[0]["givenname"][0];
				$_SESSION['lastname']=$entries[0]["sn"][0];
				

				//If role is administator, set session variable
				if(strcmp($role, "GG-1740-Registration-Administrator") == 0)
					$_SESSION['admin']=true;
				else
					$_SESSION['admin']=false;

				//If role is teacher, set session variable
				if(strcmp($role, "GG-1740-Registration-Teacher") == 0)
					$_SESSION['teacher']=true;
				else
					$_SESSION['teacher']=false;

				//If role is student, set session variable
				if(strcmp($role, "GG-1740-Registration-Student") == 0){
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
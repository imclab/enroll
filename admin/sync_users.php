<?php
	session_start();
	if ($_SESSION['admin']) {
		if($_SERVER['REQUEST_METHOD'] == "POST"){
			$usersUpdated=0;
			$usersAdded=0;
			$usersDeleted=0;
			$numErrors=0;
			//Load Settings File
			include_once 'settings.php';
			//Connects to MySQL and Selects Database
			$con = mysql_connect($host,$db_username,$db_password);
			if (!$con)
			  die('Could not connect: ' . mysql_error());
			//Select DB
			mysql_select_db($db, $con);
			//Get graduation years from Settings DB
			$get_settings_result=mysql_query(
			  "SELECT * FROM settings LIMIT 1") or die(mysql_error());
			$get_settings_array=mysql_fetch_array($get_settings_result);
			$freshman=$get_settings_array['freshman'];
			$sophomore=$get_settings_array['sophomore'];
			$junior=$get_settings_array['junior'];
			$senior=$get_settings_array['senior'];
			//Get user credentials
			$post_username=$_POST['username'];
			$post_password=$_POST['password'];
			//Connect to LDAP server
			$ldap_connection=ldap_connect($ldap_server) or die("Error while connecting to LDAP server.");
			//If Connection is Successful
			if (is_resource($ldap_connection)) {
				//Try binding to LDAP server with credentials
				if ($bind=ldap_bind($ldap_connection,$post_username.$ldap_user_suffix,$post_password)) {
					$ldap_usernames=array();  
					$filter = "(name=*)";
					$attrs = array("givenname","sn","samaccountname","department","description");
					//grab the group membership info
					$result = ldap_search($ldap_connection, $ldap_search_ou, $filter, $attrs);
					$entries = ldap_get_entries($ldap_connection, $result);
					//Traverse through LDAP Search Results
					 for ($i=0; $i<$entries["count"]; $i++)
				    {
						$firstname=$entries[$i]["givenname"][0];
					    $lastname=$entries[$i]["sn"][0];
					    $user=strtolower($entries[$i]["samaccountname"][0]);
					    $ldap_usernames[]=strtolower($entries[$i]["samaccountname"][0]);
					    $department=$entries[$i]["department"][0];
					    $description=$entries[$i]["description"][0];
					    $student=FALSE;
					    $teacher=FALSE;
					    //If user is a student
					    if(strpos($department,$freshman)!==FALSE ||
					    	strpos($department,$sophomore)!==FALSE || 
					    	strpos($department,$junior)!==FALSE || 
					    	strpos($department,$senior)!==FALSE)
					    {
					    	$student=TRUE;
					    }
					    elseif(strpos($description,"Teacher")!==FALSE){
					    	$teacher=TRUE;
					    }
					    //If any LDAP fields are empty
					    if(strcmp($firstname,'')!=0 ||
					    	strcmp($lastname,'')!=0 ||
					    	strcmp($user,'')!=0 ||
					    	strcmp($department,'')!=0 ||
					    	strcmp($description,'')!=0)
					    {
					    	$get_username_result=mysql_query(
					    		"SELECT id,username FROM users WHERE username='$user' LIMIT 1") or die(mysql_error());
					    	$get_users_array=mysql_fetch_array($get_username_result);
					    	$userid=$get_users_array['id'];
					    	//If user doesn't exist add user
					    	if (mysql_num_rows($get_username_result)==0){
					    		//If user is a student, insert new row
								if($student)
								{
									if(mysql_query("INSERT INTO users(
									      lastname,firstname,username,role,graduation_year) 
									      VALUES('$lastname','$firstname','$user','student','$department')"))
									{
										$usersAdded++;
									}
									else{
										$numErrors++;
									}
								}
								//If user is a teacher, insert new row
								elseif($teacher){
					    			if(mysql_query("INSERT INTO users(
					    			      lastname,firstname,username,role) 
					    			      VALUES('$lastname','$firstname','$user','teacher')"))
					    			{
					    				$usersAdded++;
					    			}
					    			else{
					    				$numErrors++;
					    			}
					    		}
					    	}
					    	//User exists, update entries
					    	else{
					    		//If user is a student, update record
		    					if($student)
		    					{
		    						if(mysql_query(
		    								"UPDATE users 
		    								 SET lastname=\"$lastname\",firstname=\"$firstname\",
												 username='$user',role='student',graduation_year='$department'
		    						 		 WHERE id=$userid LIMIT 1") or die(mysql_error()))
		    						{
		    							$usersUpdated++;
		    						}
		    						else{
		    							$numErrors++;
		    						}
		    					}
		    					elseif($teacher){
		    						if(mysql_query(
		    								"UPDATE users 
		    								 SET lastname=\"$lastname\",firstname=\"$firstname\",
												 username='$user',role='teacher'
		    						 		 WHERE id=$userid LIMIT 1") or die(mysql_error()))
		    						{
		    							$usersUpdated++;
		    						}
		    						else{
		    							$numErrors++;
		    						}
		    		    		}
					    	}
						}
				    }//End of For loop
				    //Get full user list from local db
				    $get_users_result=mysql_query("SELECT id,username FROM users") or die(mysql_error());
				    //Traverse through local db
				    while ($row=mysql_fetch_array($get_users_result)) {
				    	$local_db_username=$row['username'];
				    	$local_db_userid=$row['id'];
				    	//Local DB user is not in LDAP anymore
				    	if(!in_array(strtolower($local_db_username), $ldap_usernames)){
				    		if(mysql_query("DELETE FROM users WHERE id=$local_db_userid LIMIT 1")){
				    			$usersDeleted++;
				    		}
				    		else{
				    			$numErrors++;
				    		}
				    	}
				    }
				    echo "$usersAdded users added, $usersDeleted users deleted, and $usersUpdated users updated with $numErrors errors.";
				}
				else{
					echo "Counld not bind to LDAP server.";
				}
			}
			else{
				echo "Connection to LDAP server failed.";
			}
			mysql_close();
		}
	}
?>
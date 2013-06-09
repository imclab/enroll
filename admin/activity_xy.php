<?php
  session_start();
  //Credentials aren't legit or user isn't an admin, kick back to login screen
  if (!isset($_SESSION['username']) || 
    $_SESSION['login']!=true || 
    $_SESSION['admin']!=true) {
      header("Location: ../login.html");
  }
  //Code to connect to database
  include_once 'settings.php';
  //Connects to MySQL and Selects Database
  $con = mysql_connect($host,$db_username,$db_password);
  if (!$con)
    die('Could not connect: ' . mysql_error());
  //Select DB
  mysql_select_db($db, $con);
  //Ghost usernames
  $ghost_usernames=array();
  $get_ghost_usernames=mysql_query(
      "SELECT username,role 
       FROM users 
       WHERE role='teacher' OR role='student'") or die(mysql_error());
  while($row=mysql_fetch_array($get_ghost_usernames)){
    $ghost_usernames[]="\"" . $row['username'] . "\"";
  }
  //Get Settings
  $get_settings_result=mysql_query(
    "SELECT * FROM settings LIMIT 1") or die(mysql_error());
  $get_settings_array=mysql_fetch_array($get_settings_result);
  //Get latest activity, limit to 50 rows
  $get_activity_result=mysql_query(
    "SELECT xy_activity.date,users.firstname AS primary_firstname,users.lastname AS primary_lastname,
            xy_activity.secondary_user_id,xy.name AS xy_name,xy_activity.activity
      FROM `xy_activity` 
      INNER JOIN `users` on xy_activity.primary_user_id=users.id 
      INNER JOIN `xy_assignments` on xy_activity.xy_assignments_id=xy_assignments.id 
      INNER JOIN `xy` on xy_assignments.xy_id=xy.id 
      ORDER BY date DESC LIMIT 50") or die(mysql_error());
?>
<!DOCTYPE html>
<html lang='en'>
  <head>
    <meta charset='utf-8'>
    <title>Enroll: <?php echo $school_name; ?></title>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <meta name='description' content="Flexible Scheduling for Today's Classroom">
    <meta name='author' content='Marcos Alcozer'>
    <meta name='keywords' content='Education, Scheduling'>
    <!-- CSS -->
    <style>
      body {
        padding-top: 60px; /* 60px to make the container go all the way to the bottom of the topbar */
      }
    </style>
    <link href="../css/bootstrap.css" rel="stylesheet">
    <link href="../css/bootstrap-responsive.css" rel="stylesheet">
    <link href="../css/admin.css" rel="stylesheet">
    <!-- JQUERY -->
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <!-- BOOTSTRAP -->
    <script src="../js/bootstrap.min.js"></script>
    <!-- AJAX UPLOAD BY BRYAN GENTRY -->
    <!-- http://bryangentry.us/ajax-upload-with-javascript-and-php-upload-an-image-and-display-a-preview/ -->
    <script src="../js/ajaxupload.js"></script>
    <!-- FORM VALIDATION USING JQUERY -->
    <!-- http://alittlecode.com/jquery-form-validation-with-styles-from-twitter-bootstrap/ -->
    <!-- <script src="../js/jquery.validate.min.js"></script> -->
    <!-- <script src="../js/validate.js"></script> -->
    <!-- INHOUSE JAVASCRIPT -->
    <script src="../js/admin.js"></script>
    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="../js/html5shiv.js"></script>
    <![endif]-->
  </head>
  <body>
    <?php include_once("analyticstracking.php") ?>
    <div class="navbar navbar-inverse navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="brand appname" href="#">Enroll<img src='../img/beta-icon.png' style="vertical-align:text-top;"/></a>
          <div class="nav-collapse collapse">
           <ul class="nav">
             <li><a href="index.php">Dashboard</a></li>
              <li class="dropdown">
               <a href="#" class="dropdown-toggle" data-toggle="dropdown">Approvals <b class="caret"></b></a>
               <ul class="dropdown-menu">
                 <li><a href='approvals_xy.php'>XY</a></li>
                   <li class="dropdown-submenu">
                       <a tabindex="-1" href="#">Colloquium</a>
                       <ul class="dropdown-menu">
                          <li><a href='approvals_col.php?semester=1'>Semester 1</a></li>
                          <li><a href='approvals_col.php?semester=2'>Semester 2</a></li>
                       </ul>
                   </li>
               </ul>
              </li>
              <li class="dropdown">
               <a href="#" class="dropdown-toggle" data-toggle="dropdown">Enrollments <b class="caret"></b></a>
               <ul class="dropdown-menu">
                 <li><a href='enroll_xy.php'>XY</a></li>
                   <li class="dropdown-submenu">
                       <a tabindex="-1" href="#">Colloquium</a>
                       <ul class="dropdown-menu">
                          <li><a href='enroll_col.php?semester=1'>Semester 1</a></li>
                          <li><a href='enroll_col.php?semester=2'>Semester 2</a></li>
                       </ul>
                   </li>
               </ul>
              </li>
              <li class="dropdown active">
               <a href="#" class="dropdown-toggle" data-toggle="dropdown">Activity <b class="caret"></b></a>
               <ul class="dropdown-menu">
                 <li><a href='activity_xy.php'>XY</a></li>
                 <li><a href='activity_col.php'>Colloquium</a></li>
               </ul>
              </li>
               <?php if($_SESSION['teacher']){
                echo "<li><a href='../teacher/agenda.php'>My Courses</a></li>";
               }?>
               <li><a href="options.php">Settings</a></li>
           </ul>
            <ul class="nav pull-right">
                <li>
                <form id="ghostuserform" class="navbar-form pull-right">
                  <input class="span2 search-query" name="username" type="text" 
                         data-provide="typeahead" autocomplete="off" placeholder="Login as..."
                         data-source='[<?php echo implode(',',$ghost_usernames); ?>]' />
                </form>
              </li>
              <?php 
                if(!isset($_SESSION['username']))
                  echo "<li><a href='../login.html'>Login</a></li>";
                else
                  echo "<li><a href='../logout.php'>Logout</a></li>";
              ?>
            </ul>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>
    <header id="overview">
      <div class="container">
        <h1>Activity</h1>
      </div>
    </header>
    <div class='container'>
      <table class="table table-hover table-striped">
        <thead>
          <tr>
            <th>Date</th>
            <th>Primary User</th>
            <th>Activity</th>
            <th>Secondary User</th>
            <th>Course</th>
          </tr>
        </thead>
        <tbody>
          <?php
            while($row=mysql_fetch_array($get_activity_result)){
              $secondary_user_firstname="";
              $secondary_user_lastname="";
              if(isset($row['secondary_user_id'])){
                $secondary_user_id=$row['secondary_user_id'];
                if($secondary_user_array=mysql_fetch_array(mysql_query("SELECT firstname, lastname FROM users WHERE id=$secondary_user_id LIMIT 1"))){
                  $secondary_user_firstname=$secondary_user_array['firstname'];
                  $secondary_user_lastname=$secondary_user_array['lastname'];
                }
              }
              echo "<tr>";
                echo "<td>" . $row['date'] . "</td>";
                echo "<td>" . $row['primary_firstname'] . " " . $row['primary_lastname'] . "</td>";
                echo "<td>" . $row['activity'] . "</td>";
                echo "<td>" . $secondary_user_firstname . " " . $secondary_user_lastname . "</td>";
                echo "<td>" . $row['xy_name'] . "</td>";
              echo "</tr>";
            }
            mysql_close();
          ?>
        </tbody>
      </table>
    </div> <!-- /container -->
  </body>
</html>
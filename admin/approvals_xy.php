<?php
  session_start();
  //Credentials aren't legit or user isn't an admin, kick back to login screen
  if (!isset($_SESSION['username']) || 
    $_SESSION['login']!=true || 
    $_SESSION['admin']!=true) {
      header("Location: ../login.html");
  }
  //Code to connect to database
  include_once 'db.php';
  //Connects to MySQL and Selects Database
  $con = mysql_connect($host,$db_username,$db_password);
  if (!$con)
    die('Could not connect: ' . mysql_error());
  //Select DB
  mysql_select_db($db, $con);
  //Get XY dates for Approvals menu
  $dates_result=mysql_query("SELECT * FROM dates WHERE schedule='a'") or die(mysql_error());
  /**
  $dates_row= mysql_fetch_array($dates_result);
  $next_date=$dates_row['date'];
  $next_date_id=$dates_row['id'];
  $next_date_semester=$dates_row['semester'];
  $next_date_schedule=$dates_row['schedule'];
  //if next date schedule is a then get the xy assignments for that date
  if(strcmp($next_date_schedule, "a")){
    $xy_assignments_result=mysql_query(
        "SELECT users.lastname, users.firstname, xy_assignments.id, xy_assignments.final, xy.name, 
                xy_assignments.block, xy_assignments.class_size, xy_assignments.room, xy_assignments.preferred_block, 
                xy.preferred_class_size, xy.preferred_room 
        FROM `users` 
        INNER JOIN `xy_assignments` on xy_assignments.teacher_id = users.id 
        INNER JOIN `xy` on xy_assignments.xy_id = xy.id 
        WHERE xy_assignments.date_id=$next_date_id") or die(mysql_error());
  }
  **/
  mysql_close();
?>
<!DOCTYPE html>
<html lang='en'>
  <head>
    <meta charset='utf-8'>
    <title>Enroll: Northside Prep</title>
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
          <a class="brand appname" href="#">Enroll</a>
          <div class="nav-collapse collapse">
           <ul class="nav">
             <li><a href="index.php">Dashboard</a></li>
             <li class="dropdown active">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">Approvals <b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li class="dropdown-submenu">
                 <a tabindex="-1" href="#">XY</a>
                 <ul class="dropdown-menu">
                   <?php
                     while($row=mysql_fetch_array($dates_result)){
                         $id=$row['id'];
                         $date=$row['date'];
                         echo "<li><a href='approvals_xy.php?id=$id&date=$date'>" . date('F jS, Y', strtotime($date)) . "</a></li>";
                     }
                   ?>
                 </ul>
               </li>
               <li class="dropdown-submenu">
                   <a tabindex="-1" href="#">Colloquium</a>
                   <ul class="dropdown-menu">
                      <li><a href='approvals_col.php?semester=1'>Semester 1</a></li>
                      <li><a href='approvals_col.php?semester=2'>Semester 2</a></li>
                   </ul>
               </li>
              </ul>
            </li>
           </ul>
            <ul class="nav pull-right">
                <li>
                <form id="ghostuserform" class="navbar-form pull-right">
                  <input class="span2 search-query" name="username" type="text" placeholder="Login as..." />
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
    <div class='container'>
      <h1>
        <?php 
          $selected_date=$_GET['date'];
          echo date('l F jS, Y', strtotime($selected_date)); 
        ?>
      </h1>
      <hr />
      <div id='main' role='main'>
        <div>

        </div>
      </div>
    </div> <!-- /container -->
  </body>
</html>
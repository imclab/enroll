<?php
  session_start();
  //Credentials aren't legit or user isn't an admin, kick back to login screen
  if (!isset($_SESSION['username']) || 
    $_SESSION['login']!=true || 
    $_SESSION['student']) {
      $_SESSION['from_teacher']=true;
      header("Location: ../login.html");
  }
  $master_username=$_SESSION['username'];
  $ghostuser=$_SESSION['ghostuser'];
  if(!is_null($ghostuser))
    $username=$_SESSION['ghostuser'];
  else
    $username=$_SESSION['username'];
  //Code to connect to database
  include_once '../admin/settings.php';
  //Connects to MySQL and Selects Database
  $con = mysql_connect($host,$db_username,$db_password);
  if (!$con)
    die('Could not connect: ' . mysql_error());
  //Select DB
  mysql_select_db($db, $con);
  //Internal user id
  $userid=NULL;
  $get_userid_result=mysql_query("SELECT id FROM users WHERE username=\"$username\"") or die(mysql_error());
  $get_userid_array=mysql_fetch_array($get_userid_result);
  $userid=$get_userid_array['id'];
  //Get next date XY is offered
  $next_date=null;
  $next_date_id=null;
  $next_xy_result=mysql_query(
      "SELECT *
       FROM dates 
       WHERE date >= " .  date('Y-m-d') . " AND schedule='a' ORDER BY date LIMIT 1") or die(mysql_error());
  $next_xy_row= mysql_fetch_array($next_xy_result);
  $next_date=$next_xy_row['date'];
  $next_date_id=$next_xy_row['id'];
  //Get all XY assignments for teacher
  $dates_result=mysql_query(
      "SELECT xy_assignments.id, xy.name, xy_assignments.date_id, dates.date 
       FROM xy_assignments 
       INNER JOIN `xy` on xy_assignments.xy_id=xy.id 
       INNER JOIN `dates` on xy_assignments.date_id=dates.id  
       WHERE xy_assignments.teacher_id=$userid AND xy_assignments.final=1")
      or die(mysql_error());
  //Get Student List
  $students_result=mysql_query("SELECT id,firstname,lastname FROM users WHERE role='student'") or die(mysql_error());
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
    <script src="../js/teacher.js"></script>

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="../js/html5shiv.js"></script>
    <![endif]-->

  </head>

  <body>
    <?php include_once("../admin/analyticstracking.php") ?>
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
              <li><a href="agenda.php">Agenda</a></li>
              <li class="dropdown active">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">X/Y <b class="caret"></b></a>
                <ul class="dropdown-menu">
                  <li><a href="assigned_xy.php">Assign Course</a></li>
                  <li><a href="repository_xy.php">Course Repository</a></li>
                  <li><a href="preenroll_xy.php">Enroll Students</a></li>
                </ul>
              </li>
              <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Colloquium <b class="caret"></b></a>
                <ul class="dropdown-menu">
                  <li><a href="assigned_col.php">Assign Course</a></li>
                  <li><a href="repository_col.php">Course Repository</a></li>
                  <li class="dropdown-submenu">
                      <a tabindex="-1" href="#">Enroll Students</a>
                      <ul class="dropdown-menu">
                        <li><a href="preenroll_col.php?semester=1">Semester 1</a></li>
                        <li><a href="preenroll_col.php?semester=2">Semester 2</a></li>
                      </ul>
                  </li>
                </ul>
              </li>
            </ul>
            <ul class="nav pull-right">
              <?php if($_SESSION['admin']){
               echo "<li><a href='../admin'>Admin Panel</a></li>";
              }
              if(!is_null($ghostuser)){ ?>
              <li><a href="javascript:void(0)" onclick='ghost_user("<?php echo $master_username; ?>","admin");'><?php echo $master_username; ?></a></li>
              <?php 
                }
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
        <?php
          //If status returned is 0
          if($_GET['status']==3){
        ?>
        <div class="alert alert-error fade in">
          <button type="button" class="close" data-dismiss="alert">&times;</button>
          Sorry, that student is already enrolled elsewhere.
        </div>
        <?php } ?>
        <div class="row">
          <div class="span3 bs-docs-sidebar hidden-phone hidden-tablet">
            <ul class="nav nav-list bs-docs-sidenav">
              <?php
                //Iterate through the dates and create side navigation menu
                while($row=mysql_fetch_array($dates_result)){
                  echo "<li";
                  if(strcmp($row['date'], $next_date)==0)
                    echo " class='active' ";
                  echo "><a href='#" . $row['id'] . "'><i class='icon-chevron-right'></i>" . date('D F jS, Y', strtotime($row['date'])) . "</a></li>";
                }
              ?>
            </ul>
        </div>
        <div class="span9">
          <?php
            if (mysql_num_rows($dates_result) == 0){
              echo "Looks like the programmer has not finalized your XY selection just yet. <p>You may not enroll a student until it has been finalized, check back soon.</p>";
            }
            //Iterate through the dates and create a section for each date that will house enrollment rosters
            mysql_data_seek($dates_result,0);
            while($date=mysql_fetch_array($dates_result)){
              $xyassid=$date['id'];
              //Get Current Roster for Course
              $xy_roster_result=mysql_query(
                  "SELECT xy_enrollments.id,users.firstname,users.lastname 
                   FROM xy_enrollments 
                   INNER JOIN `users` on xy_enrollments.users_id=users.id
                   WHERE xy_assignments_id=$xyassid") or die(mysql_error());
          ?>
            <div class='page-header'>
              <h1><?php echo $date['name']; ?></h1>
            </div>
            <section id="<?php echo $xyassid; ?>">
            <div class="row">
                  <!-- Current Roster for Course -->
                  <div class='span4'>
                    <h3>Current Roster</h3>
                    <table class="table table-striped table-hover table-condensed">
                      <thead>
                        <tr>
                          <th>Last Name</th>
                          <th>First Name</th>
                          <th></th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                          if (mysql_num_rows($xy_roster_result) == 0){
                            echo "<tr>";
                            echo "<td></td>";
                            echo "<td></td>";
                            echo "<td></td>";
                            echo "</form>";
                            echo "</tr>";
                          }
                          else{
                            while($row=mysql_fetch_array($xy_roster_result)){
                              echo "<tr>";
                              echo "<form action='unenroll.php' method='post'>";
                              echo "<input name='id' type='hidden' value='" . $row['id'] . "' />";
                              echo "<input name='type' type='hidden' value='xy' />";
                              echo "<td>" . $row['lastname'] . "</td>";
                              echo "<td>" . $row['firstname'] . "</td>";
                              echo "<td><button class='btn btn-medium btn-warning' type='submit'>Remove</button></td>";
                              echo "</form>";
                              echo "</tr>";
                            }
                          }
                        ?>
                      </tbody>
                    </table>
                  </div>
                  <div class="span4">
                    <h3>Available Students</h3>
                    <table class="table table-striped table-hover table-condensed">
                      <thead>
                        <tr>
                          <th>Last Name</th>
                          <th>First Name</th>
                          <th></th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                          if (mysql_num_rows($students_result) == 0){
                            echo "<tr>";
                            echo "<td></td>";
                            echo "<td></td>";
                            echo "<td></td>";
                            echo "</form>";
                            echo "</tr>";
                          }
                          else{
                            while($row=mysql_fetch_array($students_result)){
                              echo "<tr>";
                              echo "<form action='enroll.php' method='post'>";
                              echo "<input name='id' type='hidden' value=" . $row['id'] . " />";
                              echo "<input name='type' type='hidden' value='xy' />";
                              echo "<input name='xy_id' type='hidden' value='" . $xyassid . "' />";
                              echo "<td>" . $row['lastname'] . "</td>";
                              echo "<td>" . $row['firstname'] . "</td>";
                              echo "<td><button class='btn btn-medium btn-primary' type='submit'>Enroll</button></td>";
                              echo "</form>";
                              echo "</tr>";
                            }
                          }
                        ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </section>
          <?php
            }
            mysql_close();
          ?>
        </div>   
    </div>
  </body>
</html>
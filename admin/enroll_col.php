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
  $numStudents=0;
  $numTeachers=0;
  $ghost_usernames=array();
  $student_usernames=array();
  $get_ghost_usernames=mysql_query(
      "SELECT username,role 
       FROM users 
       WHERE role='teacher' OR role='student'") or die(mysql_error());
  while($row=mysql_fetch_array($get_ghost_usernames)){
    $ghost_usernames[]="\"" . $row['username'] . "\"";
    if(strcmp($row['role'], 'student')==0){
      $numStudents++;
      $student_usernames[]="\"" . $row['username'] . "\"";
    }
    elseif(strcmp($row['role'], 'teacher')==0)
      $numTeachers++;
  }
  //Get Settings
  $get_settings_result=mysql_query(
    "SELECT * FROM settings LIMIT 1") or die(mysql_error());
  //Get selected semester from URL
  $selected_semester=$_GET['semester'];
  //Get Colloquium assignment for selected Semester
  $col_assignment_result=mysql_query(
        "SELECT c_assignments.id, colloquiums.name 
         FROM c_assignments 
         INNER JOIN `colloquiums` on c_assignments.c_id=colloquiums.id  
         WHERE semester=$selected_semester AND c_assignments.final=1") or die(mysql_error());
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
              <li class="dropdown active">
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
        <h1>Semester <?php echo $selected_semester; ?></h1>
      </div>
    </header>
    <div class='container'>
      <div class="row">
        <div class="span2 bs-docs-sidebar hidden-phone hidden-tablet">
          <ul class="nav nav-list bs-docs-sidenav">
            <?php
              while($row=mysql_fetch_array($col_assignment_result)){
                echo "<li><a href='#" . $row['id'] . "'><i class='icon-chevron-right'></i>" . $row['name'] . "</a></li>";
              }
            ?>
          </ul>
      </div>
      <div class="span9 offset1">
        <?php
          mysql_data_seek($col_assignment_result,0);
          while($row=mysql_fetch_array($col_assignment_result)){
            $id=$row['id'];
            //Get Current Roster for Course
            $roster_result=mysql_query(
                "SELECT c_enrollments.id,users.firstname,users.lastname 
                 FROM c_enrollments 
                 INNER JOIN `users` on c_enrollments.users_id=users.id
                 WHERE c_assignments_id=$id") or die(mysql_error());
        ?>
            <section id='<?php echo $row['id']; ?>'>
              <div class='page-header'>
                <h2><?php echo $row['name']; ?></h2>
              </div>
                <div class="row">
                  <div class='span4'>
                    <form action='enroll.php' method='post'>
                      <input name='id' type='hidden' value="<?php echo $id; ?>" />
                      <input name='type' type='hidden' value='colloquium' />
                      <input name='semester' type='hidden' value="<?php echo $selected_semester; ?>" />
                      <span class="input-append">
                        <input class="input-medium" name="username" type="text" 
                               data-provide="typeahead" autocomplete="off" placeholder="Username..."
                               data-source='[<?php echo implode(',',$student_usernames); ?>]' />
                        <span class="add-on">
                          <span class="icon-search"></span>
                        </span>
                      </span>
                      <button class='btn btn-medium btn-primary' type='submit'>Enroll</button>
                    </form>
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
                          if (mysql_num_rows($roster_result) == 0){
                            echo "<tr>";
                            echo "<td>No Students Currently Enrolled</td>";
                            echo "<td></td>";
                            echo "<td></td>";
                            echo "</tr>";
                          }
                          else{
                            while($roster=mysql_fetch_array($roster_result)){
                              echo "<tr>";
                              echo "<form action='unenroll.php' method='post'>";
                              echo "<input name='id' type='hidden' value='" . $roster['id'] . "' />";
                              echo "<input name='type' type='hidden' value='colloquium' />";
                              echo "<input name='semester' type='hidden' value=" . $selected_semester . " />";
                              echo "<td>" . $roster['lastname'] . "</td>";
                              echo "<td>" . $roster['firstname'] . "</td>";
                              echo "<td><button class='btn btn-medium btn-warning' type='submit'>Remove</button></td>";
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
    </div> <!-- /container -->
  </body>
</html>
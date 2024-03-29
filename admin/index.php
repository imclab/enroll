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
  $get_ghost_usernames=mysql_query(
      "SELECT username,role 
       FROM users 
       WHERE role='teacher' OR role='student'") or die(mysql_error());
  while($row=mysql_fetch_array($get_ghost_usernames)){
    $ghost_usernames[]="\"" . $row['username'] . "\"";
    if(strcmp($row['role'], 'student')==0)
      $numStudents++;
    elseif(strcmp($row['role'], 'teacher')==0)
      $numTeachers++;
  }
  //Get settings
  //Get Settings
  $get_settings_result=mysql_query(
    "SELECT * FROM settings LIMIT 1") or die(mysql_error());
  $get_settings_array=mysql_fetch_array($get_settings_result);
  $num_col1=0;
  $num_col1_final=0;
  $num_col2=0;
  $num_col2_final=0;
  //Get number of colloquia assigned for that date
  $c_assignments_result=mysql_query(
      "SELECT id,final,duration,semester 
       FROM `c_assignments`") or die(mysql_error());
  while($row = mysql_fetch_array($c_assignments_result)){
    if($row['semester']==1){
      $num_col1++;
      if($row['final']==1)
        $num_col1_final++;
    }
    elseif($row['semester']==2){
      $num_col2++;
      if($row['final']==1)
        $num_col2_final++;
    }
  }
  //Semester 1 Colloquium Data
  $percentage_col1_assigned=$num_col1/$numTeachers*100;
  $progress_col1_assigned="progress-danger";
  if($percentage_col1_assigned>30 && $percentage_col1_assigned<70)
    $progress_col1_assigned="progress-warning";
  elseif($percentage_col1_assigned>=70)
    $progress_col1_assigned="progress-success";
  //Semester 2 Colloquium Data
  $percentage_col2_assigned=$num_col2/$numTeachers*100;
  $progress_col2_assigned="progress-danger";
  if($percentage_col2_assigned>30 && $percentage_col2_assigned<70)
    $progress_col2_assigned="progress-warning";
  elseif($percentage_col2_assigned>=70)
    $progress_col2_assigned="progress-success";
  //Number of Semester 1 Colloquiums you have finalized:
  $percentage_col1_finalized=$num_col1_final/$num_col1*100;
  $progress_col1_finalized="progress-danger";
  if($percentage_col1_finalized>30 && $percentage_col1_finalized<70)
    $progress_col1_finalized="progress-warning";
  elseif($percentage_col1_finalized>=70)
    $progress_col1_finalized="progress-success";
  //Number of Semester 2 Colloquiums you have finalized:
  $percentage_col2_finalized=$num_col2_final/$num_col2*100;
  $progress_col2_finalized="progress-danger";
  if($percentage_col2_finalized>30 && $percentage_col2_finalized<70)
    $progress_col2_finalized="progress-warning";
  elseif($percentage_col2_finalized>=70)
    $progress_col2_finalized="progress-success";
  //Get next date
  $next_date_result=mysql_query("SELECT id,date FROM course_schedule WHERE date > " .  date('Y-m-d') . "  LIMIT 1") or die(mysql_error());
  $next_date_row= mysql_fetch_array($next_date_result);
  $next_date=$next_date_row['date'];
  $next_date_id=$next_date_row['id'];
  if($next_date < $get_settings_array['quarter_3_start'])
    $next_date_semester=1;
  else
    $next_date_semester=1;
  //Get number of xy courses assigned for that date
  $xy_assignments_result=mysql_query("SELECT id,final FROM `xy_assignments` WHERE date_id=$next_date_id") or die(mysql_error());
  $xy_assignments_count=0;
  $xy_assignments_not_approved=0;
  while($row = mysql_fetch_array($xy_assignments_result)){
    $xy_assignments_count++;
    if($row['final']==0)
      $xy_assignments_not_approved++;
  }
  mysql_close();
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
             <li class="active"><a href="index.php">Dashboard</a></li>
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
              <li class="dropdown">
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
        <h1>Dashboard</h1>
      </div>
    </header>
    <div class='container'>
      <div class="span9">
        Teachers who have assigned a Colloquium to Semester 1:
        <span class="tooltip" data-toggle="tooltip" data-placement="left" 
              title="<?php echo $num_col1 . ' teachers'; ?>">Tooltip</span>
        <div class="progress progress-striped active <?php echo $progress_col1_assigned; ?>">
          <div class="bar" style="width: <?php echo $percentage_col1_assigned; ?>%;"></div>
        </div>
        Teachers who have assigned a Colloquium to Semester 2:
        <span class="tooltip" data-toggle="tooltip" data-placement="left" 
              title="<?php echo $num_col2 . ' teachers'; ?>">Tooltip</span>
        <div class="progress progress-striped active <?php echo $progress_col2_assigned; ?>">
          <div class="bar" style="width: <?php echo $percentage_col2_assigned; ?>%;"></div>
        </div>
        Number of Semester 1 Colloquiums you have finalized:
        <span class="tooltip" data-toggle="tooltip" data-placement="left" 
              title="<?php echo $num_col1_final . ' courses'; ?>">Tooltip</span>
        <div class="progress progress-striped active <?php echo $progress_col1_finalized; ?>">
          <div class="bar" style="width: <?php echo $percentage_col1_finalized; ?>%;"></div>
        </div>
        Number of Semester 2 Colloquiums you have finalized:
        <span class="tooltip" data-toggle="tooltip" data-placement="left" 
              title="<?php echo $num_col2_final . ' courses'; ?>">Tooltip</span>
        <div class="progress progress-striped active <?php echo $progress_col2_finalized; ?>">
          <div class="bar" style="width: <?php echo $percentage_col2_finalized; ?>%;"></div>
        </div>
      </div>
    </div> <!-- /container -->
  </body>
</html>
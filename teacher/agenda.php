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

  include_once '../admin/settings.php';
  //Connects to MySQL and Selects Database
  $con = mysql_connect($host,$db_username,$db_password);
  if (!$con)
    die('Could not connect: ' . mysql_error());
  //Select DB
  mysql_select_db($db, $con);

  //Get next date for XY Courses
  $next_xy_result=mysql_query("SELECT id,date FROM dates WHERE date > " .  date('Y-m-d') . " AND schedule=\"a\" ORDER BY date LIMIT 1") or die(mysql_error());
  $next_xy_row= mysql_fetch_array($next_xy_result);
  $next_xy=$next_xy_row['date'];

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
    <link href="//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.2/css/bootstrap-combined.min.css" rel="stylesheet">
   <!-- <link href="../css/bootstrap.css" rel="stylesheet">
    <link href="../css/bootstrap-responsive.css" rel="stylesheet"> -->
    <link href="../css/admin.css" rel="stylesheet">

    <!-- JQUERY -->
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <!-- BOOTSTRAP -->
    <!-- <script src="../js/bootstrap.min.js"></script> -->
    <script src="//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.2/js/bootstrap.min.js"></script>
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
              <li class="active"><a href="agenda.php">Agenda</a></li>
              <li class="dropdown">
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
              <?php if($_SESSION['admin'] && $_SESSION['teacher']){
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
    <div class="container">
        <h1 class='hidden-phone'>
          Agenda for <?php echo date('l F jS, Y', strtotime($next_xy)); ?>
        </h1>
        <h3 class='visible-phone'>
          Agenda for <?php echo date('l F jS, Y', strtotime($next_xy)); ?>
        </h3>
        <hr />
        <div id="main" role="main">
        </div>
    </div> <!-- /container -->
  </body>
</html>
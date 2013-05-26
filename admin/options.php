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
  //Get teacher's usernames
  $teacher_usernames=array();
  $get_teacher_usernames=mysql_query(
    "SELECT username FROM users WHERE role='teacher'") or die(mysql_error());
  while($row=mysql_fetch_array($get_teacher_usernames)){
    $teacher_usernames[]="\"" . $row['username'] . "\"";
  }
  //Handle form updates
  $status=null;
  if($_SERVER['REQUEST_METHOD'] == "POST"){
    if(isset($_POST['freshman'])){
      $post_freshman=$_POST['freshman'];
      $post_sophomore=$_POST['sophomore'];
      $post_junior=$_POST['junior'];
      $post_senior=$_POST['senior'];
      if(mysql_query("UPDATE settings 
                         SET freshman='$post_freshman',sophomore='$post_sophomore',
                         junior='$post_junior',senior='$post_senior'
                         WHERE id=1 LIMIT 1") or die(mysql_error()))
      {
        $status=1;
      }
      else{
        $status=0;
      }
    }
  }
  //Get Settings
  $get_settings_result=mysql_query(
    "SELECT * FROM settings LIMIT 1") or die(mysql_error());
  $get_settings_array=mysql_fetch_array($get_settings_result);
  $freshman=$get_settings_array['freshman'];
  $sophomore=$get_settings_array['sophomore'];
  $junior=$get_settings_array['junior'];
  $senior=$get_settings_array['senior'];
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
          <a class="brand appname" href="#">Enroll</a>
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
               <li><a href="options.php">Settings</a></li>
             </li>
           </ul>
            <ul class="nav pull-right">
                <li>
                <form id="ghostuserform" class="navbar-form pull-right">
                  <input class="span2 search-query" name="username" type="text" 
                         data-provide="typeahead" autocomplete="off" placeholder="Login as..."
                         data-source='[<?php echo implode(',',$teacher_usernames); ?>]' />
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
        <?php if(!$status && !is_null($status)) { ?>
        <div id="failed" class="alert alert-error">
          <button type="button" class="close" data-dismiss="alert">&times;</button>
          Sorry, changes did not save.
        </div>
        <?php }else if($status && !is_null($status)) { ?>
        <div id="success" class="alert alert-success">
          <button type="button" class="close" data-dismiss="alert">&times;</button>
          Changes saved successfully.
        </div>
        <?php } ?>
        <div class="row">
          <div class="span3 bs-docs-sidebar hidden-phone hidden-tablet">
            <ul class="nav nav-list bs-docs-sidenav">
              <li><a href='#graduation'><i class='icon-chevron-right'></i>Graduation Years</a></li>
            </ul>
        </div>
        <div class="span8 offset1">
          <section id="graduation">
            <div class='page-header'>
              <h1>Graduation Years</h1>
              <form class="form-horizontal" action="#" method="post">
                <div class="control-group">
                  <label class="control-label" for="inputFreshman">Freshman</label>
                  <div class="controls">
                    <input type="number" name="freshman" id="inputFreshman" value=<?php echo $freshman; ?> required />
                  </div>
                </div>
                <div class="control-group">
                  <label class="control-label" for="inputSophomore">Sophomore</label>
                  <div class="controls">
                    <input type="number" name="sophomore" id="inputSophomore" value=<?php echo $sophomore; ?> required />
                  </div>
                </div>
                <div class="control-group">
                  <label class="control-label" for="inputJunior">Junior</label>
                  <div class="controls">
                    <input type="number" name="junior" id="inputJunior" value=<?php echo $junior; ?> required />
                  </div>
                </div>
                <div class="control-group">
                  <label class="control-label" for="inputSenior">Senior</label>
                  <div class="controls">
                    <input type="number" name="senior" id="inputSenior" value=<?php echo $senior; ?> required />
                  </div>
                </div>
                <div class="control-group">
                  <div class="controls">
                    <button type="submit" class="btn btn-primary">Update</button>
                  </div>
                </div>
              </form>
            </div>
          </section>
        </div>    
      </div>
    </div>
  </body>
</html>
<?php
session_start();

//Credentials aren't legit or user isn't an admin, kick back to login screen
if (!isset($_SESSION['username']) || 
  $_SESSION['login']!=true || 
  $_SESSION['teacher']!=true) {
    $_SESSION['from_teacher']=true;
    header("Location: ../login.html");
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Enroll: Northside Prep</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Flexible Scheduling for Today's Classroom">
    <meta name="author" content="Marcos Alcozer">
    <meta name="keywords" content="Education, Scheduling">

    <!-- Le styles -->
    <link href="../css/bootstrap.css" rel="stylesheet">
    <style>
      body {
        padding-top: 60px; /* 60px to make the container go all the way to the bottom of the topbar */
      }
    </style>
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
    <script src="../js/jquery.validate.min.js"></script>
    <!-- <script src="../js/validate.js"></script> -->
    <!-- INHOUSE JAVASCRIPT -->
    <script src="../js/admin.js"></script>

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
          <a class="brand appname" href="#">Enroll</a>
          <div class="nav-collapse collapse">
            <ul class="nav">
              <li class="active"><a href="agenda.php">Agenda</a></li>
              <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">X/Y <b class="caret"></b></a>
                <ul class="dropdown-menu">
                  <li><a href="xy.php#assign">Assign</a></li>
                  <li><a href="xy.php#manage">Manage</a></li>
                </ul>
              </li>
              <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Colloquium <b class="caret"></b></a>
                <ul class="dropdown-menu">
                  <li><a id="assignLink" href="colloquium.php#assign">Assign</a></li>
                  <li><a id="manageLink" href="colloquium.php#manage">Manage</a></li>
                </ul>
              </li>
            </ul>
            <ul class="nav pull-right">
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
    <div class="container">
    </div> <!-- /container -->
  </body>
</html>
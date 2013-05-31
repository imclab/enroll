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
    "SELECT username FROM users WHERE role='teacher' OR role='student'") or die(mysql_error());
  while($row=mysql_fetch_array($get_ghost_usernames)){
    $ghost_usernames[]="\"" . $row['username'] . "\"";
  }
  //Handle form updates
  $status=null;
  if($_SERVER['REQUEST_METHOD'] == "POST"){
    if(isset($_POST['gradyears'])){
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
    elseif(isset($_POST['sem1times'])){
      $post_freshman=$_POST['freshman'];
      $post_sophomore=$_POST['sophomore'];
      $post_junior=$_POST['junior'];
      $post_senior=$_POST['senior'];
      $post_end=$_POST['end'];
      if(mysql_query("UPDATE settings 
                         SET col1_freshman_start='$post_freshman',col1_sophomore_start='$post_sophomore',
                         col1_junior_start='$post_junior',col1_senior_start='$post_senior',col1_end='$post_end'
                         WHERE id=1 LIMIT 1") or die(mysql_error()))
      {
        $status=1;
      }
      else{
        $status=0;
      }
    }
    elseif(isset($_POST['sem2times'])){
      $post_freshman=$_POST['freshman'];
      $post_sophomore=$_POST['sophomore'];
      $post_junior=$_POST['junior'];
      $post_senior=$_POST['senior'];
      $post_end=$_POST['end'];
      if(mysql_query("UPDATE settings 
                         SET col2_freshman_start='$post_freshman',col2_sophomore_start='$post_sophomore',
                         col2_junior_start='$post_junior',col2_senior_start='$post_senior',col2_end='$post_end'
                         WHERE id=1 LIMIT 1") or die(mysql_error()))
      {
        $status=1;
      }
      else{
        $status=0;
      }
    }
    elseif(isset($_POST['xytimes'])){
      $xy_num_days_open=$_POST['xy_num_days_open'];
      $xy_time_open=$_POST['xy_time_open'];
      $xy_num_days_close=$_POST['xy_num_days_close'];
      $xy_time_close=$_POST['xy_time_close'];
      if(mysql_query("UPDATE settings 
                         SET xy_num_days_open='$xy_num_days_open',xy_time_open='$xy_time_open',
                         xy_num_days_close='$xy_num_days_close',xy_time_close='$xy_time_close'
                         WHERE id=1 LIMIT 1") or die(mysql_error()))
      {
        $status=1;
      }
      else{
        $status=0;
      }
    }
    elseif(isset($_POST['classrooms'])){
      $classrooms=$_POST['classrooms'];
      if(mysql_query("UPDATE settings 
                         SET rooms='$classrooms' WHERE id=1 LIMIT 1") or die(mysql_error()))
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
    <link href="../css/bootstrap-datetimepicker.min.css" rel="stylesheet">
    <link href="../css/admin.css" rel="stylesheet">
    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="../js/html5shiv.js"></script>
    <![endif]-->
  </head>

  <body data-spy="scroll" data-target=".bs-docs-sidebar">
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
               <?php if($_SESSION['teacher']){
                echo "<li><a href='../teacher/agenda.php'>My Courses</a></li>";
               }?>
               <li class="active"><a href="options.php">Settings</a></li>
             </li>
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
      <?php if(!$status && !is_null($status)) { ?>
      <div id="failed" class="alert alert-error text-center">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        Sorry, changes did not save.
      </div>
      <?php }else if($status && !is_null($status)) { ?>
      <div id="success" class="alert alert-info text-center">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        Changes saved successfully.
      </div>
      <?php } ?>
      <div id="status" class="alert alert-info text-center" style="display: none;">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <span id="status_text"></span>
      </div>
    </div>
      <div class='container'>
        <div class="row">
          <div class="span2 bs-docs-sidebar hidden-phone hidden-tablet">
            <ul class="nav nav-list bs-docs-sidenav">
              <li><a href='#colloquiumstartend'><i class='icon-chevron-right'></i>Colloquium Start/End Times</a></li>
              <li><a href='#xystartend'><i class='icon-chevron-right'></i>XY Start/End Times</a></li>
              <li><a href='#classrooms'><i class='icon-chevron-right'></i>Available Classrooms</a></li>
              <li><a href='#graduation'><i class='icon-chevron-right'></i>Graduation Years</a></li>
              <li><a href='#sync'><i class='icon-chevron-right'></i>Sync Users</a></li>
            </ul>
        </div>
        <div class="span9 offset1">
          <section id="colloquiumstartend">
          <div class='page-header'>
            <h1>Colloquium Start/End Times</h1>
          </div>
          <div class="row">
            <div class="span4">
              <h3>Semester 1</h3>
              <form class="form" action="#" method="post">
                <div class="control-group">
                  <label class="control-label" for="inputCol1Freshman">Freshman Start Time</label>
                 <div class="input-append date controls datetimepicker">
                    <input data-format="yyyy-MM-dd hh:mm:ss" type="text" 
                           name="freshman" id="inputCol1Freshman" value="<?php echo $get_settings_array['col1_freshman_start']; ?>" required />
                    <span class="add-on">
                      <i data-time-icon="icon-time" data-date-icon="icon-calendar">
                      </i>
                    </span>
                  </div>
                </div>
                <div class="control-group">
                  <label class="control-label" for="inputCol1Sophomore">Sophomore Start Time</label>
                  <div class="input-append date controls datetimepicker">
                    <input data-format="yyyy-MM-dd hh:mm:ss" type="text" 
                           name="sophomore" id="inputCol1Sophomore" value="<?php echo $get_settings_array['col1_sophomore_start']; ?>" required />
                    <span class="add-on">
                      <i data-time-icon="icon-time" data-date-icon="icon-calendar">
                      </i>
                    </span>
                  </div>
                </div>
                <div class="control-group">
                  <label class="control-label" for="inputCol1Junior">Junior Start Time</label>
                  <div class="input-append date controls datetimepicker">
                    <input data-format="yyyy-MM-dd hh:mm:ss" type="text" 
                           name="junior" id="inputCol1Junior" value="<?php echo $get_settings_array['col1_junior_start']; ?>" required />
                    <span class="add-on">
                      <i data-time-icon="icon-time" data-date-icon="icon-calendar">
                      </i>
                    </span>
                  </div>
                </div>
                <div class="control-group">
                  <label class="control-label" for="inputCol1Senior">Senior Start Time</label>
                  <div class="input-append date controls datetimepicker">
                    <input data-format="yyyy-MM-dd hh:mm:ss" type="text" 
                           name="senior" id="inputCol1Senior" value="<?php echo $get_settings_array['col1_senior_start']; ?>" required />
                    <span class="add-on">
                      <i data-time-icon="icon-time" data-date-icon="icon-calendar">
                      </i>
                    </span>
                  </div>
                </div>
                <div class="control-group">
                  <label class="control-label" for="inputCol1EndTime">End Time</label>
                  <div class="input-append date controls datetimepicker">
                    <input data-format="yyyy-MM-dd hh:mm:ss" type="text" 
                           name="end" id="inputCol1EndTime" value="<?php echo $get_settings_array['col1_end']; ?>" required />
                    <span class="add-on">
                      <i data-time-icon="icon-time" data-date-icon="icon-calendar">
                      </i>
                    </span>
                  </div>
                </div>
                <div class="control-group">
                  <div class="controls">
                    <button type="submit" class="btn btn-primary" name="sem1times">Update</button>
                  </div>
                </div>
              </form>
            </div>
            <div class="span5">
              <h3>Semester 2</h3>
              <form class="form" action="#" method="post">
                <div class="control-group">
                  <label class="control-label" for="inputCol2Freshman">Freshman Start Time</label>
                 <div class="input-append date controls datetimepicker">
                    <input data-format="yyyy-MM-dd hh:mm:ss" type="text" 
                           name="freshman" id="inputCol2Freshman" value="<?php echo $get_settings_array['col2_freshman_start']; ?>" required />
                    <span class="add-on">
                      <i data-time-icon="icon-time" data-date-icon="icon-calendar">
                      </i>
                    </span>
                  </div>
                </div>
                <div class="control-group">
                  <label class="control-label" for="inputCol2Sophomore">Sophomore Start Time</label>
                  <div class="input-append date controls datetimepicker">
                    <input data-format="yyyy-MM-dd hh:mm:ss" type="text" 
                           name="sophomore" id="inputCol2Sophomore" value="<?php echo $get_settings_array['col2_sophomore_start']; ?>" required />
                    <span class="add-on">
                      <i data-time-icon="icon-time" data-date-icon="icon-calendar">
                      </i>
                    </span>
                  </div>
                </div>
                <div class="control-group">
                  <label class="control-label" for="inputCol2Junior">Junior Start Time</label>
                  <div class="input-append date controls datetimepicker">
                    <input data-format="yyyy-MM-dd hh:mm:ss" type="text" 
                           name="junior" id="inputCol2Junior" value="<?php echo $get_settings_array['col2_junior_start']; ?>" required />
                    <span class="add-on">
                      <i data-time-icon="icon-time" data-date-icon="icon-calendar">
                      </i>
                    </span>
                  </div>
                </div>
                <div class="control-group">
                  <label class="control-label" for="inputCol2Senior">Senior Start Time</label>
                  <div class="input-append date controls datetimepicker">
                    <input data-format="yyyy-MM-dd hh:mm:ss" type="text" 
                           name="senior" id="inputCol2Senior" value="<?php echo $get_settings_array['col2_senior_start']; ?>" required />
                    <span class="add-on">
                      <i data-time-icon="icon-time" data-date-icon="icon-calendar">
                      </i>
                    </span>
                  </div>
                </div>
                <div class="control-group">
                  <label class="control-label" for="inputCol2EndTime">End Time</label>
                  <div class="input-append date controls datetimepicker">
                    <input data-format="yyyy-MM-dd hh:mm:ss" type="text" 
                           name="end" id="inputCol2EndTime" value="<?php echo $get_settings_array['col2_end']; ?>" required />
                    <span class="add-on">
                      <i data-time-icon="icon-time" data-date-icon="icon-calendar">
                      </i>
                    </span>
                  </div>
                </div>
                <div class="control-group">
                  <div class="controls">
                    <button type="submit" class="btn btn-primary" name="sem2times">Update</button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </section>
        <section id="xystartend">
          <div class='page-header'>
            <h1>XY Start/End Times</h1>
          </div>
          <form class="form" action="#" method="post">
            XY registration will open 
            <input type="number" name="xy_num_days_open" class="input-mini" value=<?php echo $get_settings_array['xy_num_days_open']; ?> required /> 
            days prior at 
            <span class="input-append timepicker">
              <input data-format="hh:mm:ss" type="text" class="input-mini"
                     name="xy_time_open" value="<?php echo $get_settings_array['xy_time_open']; ?>" required />
              <span class="add-on">
                <i data-time-icon="icon-time" data-date-icon="icon-calendar">
                </i>
              </span> 
            </span>
            and will close 
            <input type="number" name="xy_num_days_close" class="input-mini" value=<?php echo $get_settings_array['xy_num_days_close']; ?> required /> 
            days prior at 
            <span class="input-append timepicker">
              <input data-format="hh:mm:ss" type="text" class="input-mini"
                     name="xy_time_close" value="<?php echo $get_settings_array['xy_time_close']; ?>" required />
              <span class="add-on">
                <i data-time-icon="icon-time" data-date-icon="icon-calendar">
                </i>
              </span> 
            </span>
            <div>
              <button type="submit" class="btn btn-primary" name="xytimes">Update</button>
            </div>
          </form>
        </section>
        <section id="classrooms">
          <div class='page-header'>
            <h1>Available Classrooms</h1>
          </div>
          <form class="form" action="#" method="post">
            <div class="control-group">
              <label class="control-label" for="classroms">List of classrooms:</label>
              <div class="controls">
                <textarea class="input-xxlarge" name="classrooms" id="classrooms" rows="5" required><?php echo $get_settings_array['rooms']; ?></textarea>
              </div>
            </div>
            <div class="control-group">
              <div class="controls">
                <button name="classrooms_submit" type="submit" class="btn btn-primary">Update</button>
              </div>
            </div>
          </form>
       </section>
          <section id="graduation">
            <div class='page-header'>
              <h1>Graduation Years</h1>
            </div>
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
                    <button name="gradyears" type="submit" class="btn btn-primary">Update</button>
                  </div>
                </div>
              </form>
          </section>
          <section id="sync">
            <div class='page-header'>
              <h1>Sync Users</h1>
            </div>
            <form class="form-horizontal" id="sync_users_form" method="post">
              <div class="control-group">
                <label class="control-label" for="inputUsername">Username</label>
                <div class="controls">
                  <input type="text" name="username" id="inputUsername" value=<?php echo $_SESSION['username']; ?> required />
                </div>
              </div>
              <div class="control-group">
                <label class="control-label" for="inputPassword">Password</label>
                <div class="controls">
                  <input type="password" name="password" id="inputPassword" required />
                </div>
              </div>
              <div class="control-group">
                <div class="controls">
                  <input type="submit" id="syncButton" class="btn btn-primary" value="Sync" />
                </div>
              </div>
              <div class="control-group">
                <div class="controls">
                  <div id="please_wait" style="display: none;">
                    <em>Working on it, please wait...</em>
                  </div>
              </div>
            </form>
          </section>
        </div>    
      </div>
    </div>
    <!-- JQUERY -->
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <!-- BOOTSTRAP -->
    <script src="../js/bootstrap.min.js"></script>
    <!-- AJAX UPLOAD BY BRYAN GENTRY -->
    <!-- http://bryangentry.us/ajax-upload-with-javascript-and-php-upload-an-image-and-display-a-preview/ -->
    <script src="../js/ajaxupload.js"></script>
    <!-- BOOTSTRAP DATETIME PICKER -->
    <!-- http://tarruda.github.io/bootstrap-datetimepicker/ -->
    <script src="../js/bootstrap-datetimepicker.min.js"></script>
    <!-- FORM VALIDATION USING JQUERY -->
    <!-- http://alittlecode.com/jquery-form-validation-with-styles-from-twitter-bootstrap/ -->
    <!-- <script src="../js/jquery.validate.min.js"></script> -->
    <!-- <script src="../js/validate.js"></script> -->
    <!-- INHOUSE JAVASCRIPT -->
    <script src="../js/admin.js"></script>
    <script type="text/javascript">
      $(function() {
        $('.datetimepicker').datetimepicker({
          language: 'pt-BR'
        });
        $('.timepicker').datetimepicker({
          pickDate: false
        });
      });
    </script>
  </body>
</html>
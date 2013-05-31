<?php
  session_start();

  //Credentials aren't legit or user isn't an admin, kick back to login screen
  if (!isset($_SESSION['username']) || 
    $_SESSION['login']!=true || 
    $_SESSION['student']) {
      $_SESSION['from_teacher']=true;
      header('Location: ../login.html');
  }
  //Code to connect to database
  //Grab all of teacher's colloquiums and store in array
  include_once '../admin/settings.php';
  //Connects to MySQL and Selects Database
  $con = mysql_connect($host,$db_username,$db_password);
  if (!$con)
    die('Could not connect: ' . mysql_error());
  mysql_select_db($db, $con);
  $master_username=$_SESSION['username'];
  $ghostuser=$_SESSION['ghostuser'];
  if(!is_null($ghostuser))
    $username=$_SESSION['ghostuser'];
  else
    $username=$_SESSION['username'];
  //Internal user id
  $userid=NULL;
  $get_userid_result=mysql_query("SELECT id FROM users WHERE username=\"$username\"") or die(mysql_error());
  $get_userid_array=mysql_fetch_array($get_userid_result);
  $userid=$get_userid_array['id'];
  //Grab all of the teacher's colloquiums
  $get_colloquium_repository_result=mysql_query('SELECT colloquiums.id,colloquiums.name, colloquiums.description, colloquiums.image, colloquiums.preferred_room, 
                   colloquiums.preferred_class_size, colloquiums.preferred_lunch_block, colloquiums.freshmen,
                   colloquiums.sophomores, colloquiums.juniors, colloquiums.seniors, users.id AS userid 
            FROM colloquiums INNER JOIN `users` on colloquiums.teacher_id = users.id WHERE users.username="' . $username . '"')
            or die (mysql_error());
  //Grab all of the teachers assigned colloquiums
  $get_colloquium_assignments_result=mysql_query(
              'SELECT c_assignments.duration, c_assignments.semester, c_assignments.c_id, c_assignments.notes, c_assignments.final, users.id AS userid
               FROM c_assignments INNER JOIN `users` on c_assignments.teacher_id = users.id WHERE users.username="' . $username . '"')
                or die (mysql_error());
  //Teacher has no assigned colloquiums
  $sem1Col=false;
  $sem2Col=false;
  //Attributes of the teacher's semester 1 colloquium
  $sem1ColName=null;
  $sem1ColDuration=null;
  $sem1ColNotes=null;
  $sem1ColFinal=null;
  //Attributes of the teacher's semester 1 colloquium
  $sem2ColName=null;
  $sem2ColDuration=null;
  $sem2ColNotes=null;
  $sem2ColFinal=null;
  //Traverse through existing colloquium assignments
  mysql_data_seek($get_colloquium_assignments_result,0);
  while($colRow = mysql_fetch_array($get_colloquium_assignments_result)){
      //Attributes of individual colloquium assignment
      $c_id = $colRow['c_id'];
      $duration = $colRow['duration'];
      $semester = $colRow['semester'];
      //If colloquium assignment is for a full year colloquium
      if(strcmp($duration, 'y') == 0){
        //We have a Semester 1 and 2 assignment!
        $sem1Col = true;
        $sem2Col = true;
        //Find out the colloquium's name
        //Assign it to name variables for both Semesters
        mysql_data_seek($get_colloquium_repository_result,0);
        while($row = mysql_fetch_array($get_colloquium_repository_result)){
          if( $c_id == $row['id'] ){
            $sem1ColName = $row['name'];
            $sem2ColName = $row['name'];
          }
        }
        //Only need to set fields for Semester 1
        //Semester 2 fields besides name will be disabled
        $sem1ColDuration = $colRow['duration'];
        $sem1ColNotes = $colRow['notes'];
        $sem1ColFinal = $colRow['final'];
      }
      //else if assignment is 1st Semester
      else if(strcmp($duration, 'y') != 0 && $semester == 1){
        //We have a Semester 1 assignment!
        $sem1Col = true;
        //Find out the colloquium's name
        //Assign it to name variable for Semester 1
        mysql_data_seek($get_colloquium_repository_result,0);
        while($row = mysql_fetch_array($get_colloquium_repository_result)){
          if( $c_id == $row['id'] ){
            $sem1ColName = $row['name'];
          }
        }
        //Set fields for Semester 1
        $sem1ColDuration = $colRow['duration'];
        $sem1ColNotes = $colRow['notes'];
        $sem1ColFinal = $colRow['final'];
      }
      //if assignment is 2nd Semester
      else if(strcmp($duration, "y") != 0 && $semester == 2){
        //We have a Semester 2 assignment!
        $sem2Col = true;
        //Find out the colloquium's name
        //Assign it to name variable for Semester 2
        mysql_data_seek($get_colloquium_repository_result,0);
        while($row = mysql_fetch_array($get_colloquium_repository_result)){
          if( $c_id == $row['id'] ){
            $sem2ColName = $row['name'];
          }
        }
        //Set fields for Semester 2
        $sem2ColDuration = $colRow['duration'];
        $sem2ColNotes = $colRow['notes'];
        $sem2ColFinal = $colRow['final'];
      }
  }
    mysql_close($con);
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

    <!-- Le styles -->
    <style>
      body {
        padding-top: 60px; /* 60px to make the container go all the way to the bottom of the topbar */
      }
    </style>
    <link href='../css/bootstrap.css' rel='stylesheet'>
    <link href='../css/bootstrap-responsive.css' rel='stylesheet'>
    <link href='../css/admin.css' rel='stylesheet'>

    <!-- JQUERY -->
    <script src='http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js'></script>
    <!-- BOOTSTRAP -->
    <script src='../js/bootstrap.min.js'></script>
    <!-- AJAX UPLOAD BY BRYAN GENTRY -->
    <!-- http://bryangentry.us/ajax-upload-with-javascript-and-php-upload-an-image-and-display-a-preview/ -->
    <script src='../js/ajaxupload.js'></script>
    <!-- FORM VALIDATION USING JQUERY -->
    <!-- http://alittlecode.com/jquery-form-validation-with-styles-from-twitter-bootstrap/ -->
    <script src='../js/jquery.validate.min.js'></script>
    <!-- <script src='../js/validate.js'></script> -->
    <!-- INHOUSE JAVASCRIPT -->
    <script src='../js/teacher.js'></script>

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="../js/html5shiv.js"></script>
    <![endif]-->
  </head>
  <body>
    <?php include_once('../admin/analyticstracking.php') ?>
    <!-- TOP MENU -->
    <div class='navbar navbar-inverse navbar-fixed-top'>
      <div class='navbar-inner'>
        <div class='container'>
          <button type='button' class='btn btn-navbar' data-toggle='collapse' data-target='.nav-collapse'>
            <span class='icon-bar'></span>
            <span class='icon-bar'></span>
            <span class='icon-bar'></span>
          </button>
          <a class='brand appname' href='#'>Enroll<img src='../img/beta-icon.png' style="vertical-align:text-top;"/></a>
          <div class='nav-collapse collapse'>
            <ul class="nav">
              <li><a href="agenda.php">Agenda</a></li>
              <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">X/Y <b class="caret"></b></a>
                <ul class="dropdown-menu">
                  <li><a href="assigned_xy.php">Assign Course</a></li>
                  <li><a href="repository_xy.php">Course Repository</a></li>
                  <li><a href="preenroll_xy.php">Enroll Students</a></li>
                </ul>
              </li>
              <li class="dropdown active">
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
    <div class='container'>
      <h2 id='assignLabel'>Assign Course</h2>
      <hr />
      <div id='assign'>
        <!-- Semester 1 Choice -->
        <div class='controls controls-row'>
          <div class='span5'>
            <h4>Semester 1:</h4>
            <!-- Form Name sem1Selection -->
            <form id='sem1Selection'>
              <input name='semester' type='hidden' value=1 />
              <input name='teacher' type='hidden' value='<?php echo $userid; ?>' />
              <input name='previous_duration' type='hidden' value='<?php echo $duration; ?>' />
              <input name='existing' type='hidden' value=<?php if($sem1Col){ echo 1; }else{ echo 0; } ?> />
            <div class='control-group'>
              <label class='control-label'>Colloquium</label>
              <div class='controls'>
                  <select name='c_id' id='sem1Colloquium' <?php if($sem1ColFinal==1){ echo " disabled "; } ?> >
                    <option value=''></option>
                  <?php
                    //Traverse through teacher's colloquium repository
                    mysql_data_seek($get_colloquium_repository_result,0);
                    while($row = mysql_fetch_array($get_colloquium_repository_result)){
                      echo '<option';
                      if(strcmp($row['name'], $sem1ColName) == 0){ echo " selected"; }
                        echo ' value=' . $row['id'] . '>' . $row['name'] . '</option>';
                    }
                  ?>
                </select>
              </div>
            </div>
            <div class='control-group'>
              <label class='control-label'>Duration: </label>
              <div class='controls'>
                <select name='duration' id='sem1Duration' <?php if($sem1ColFinal==1){ echo " disabled "; } ?> >
                  <option selected value=''></option>
                  <?php 
                    if($sem1Col && strcmp($duration, "s") == 0)
                    {
                      echo '<option selected value="s">Semester</option>';
                      echo '<option value="y">Year</option>';
                    }
                    else if($sem1Col && strcmp($duration, "y") == 0){
                      echo '<option value="s">Semester</option>';
                      echo '<option selected value="y">Year</option>';
                    }
                    else{
                      echo '<option value="s">Semester</option>';
                      echo '<option value="y">Year</option>';
                    }
                  ?>
                </select>
              </div>
            </div>
                <div class='control-group'>
                  <label class='control-label'>Notes for programmer: </label>
                  <div class='controls'>
                    <textarea name='notes' rows='5' id='sem1Notes'<?php if($sem1ColFinal==1){ echo " disabled "; } ?> ><?php if($sem1Col){ echo $sem1ColNotes; } ?></textarea>
                  </div>
                </div>
                <div class='control-group'>
                  <div class='controls'>
                    <?php
                      if(!$sem1ColFinal){
                    ?>
                      <div><button class='btn' type='submit' onClick='assign_colloquium(1)' >Update</button></div>
                      <?php if($sem1Col){ ?>
                        <div><em class='text-info'>Pending Approval</em></div>
                      <?php } ?>
                      <div id='sem1Status'></div>
                    <?php
                      }
                      else{
                    ?>
                      <div><em class='text-success'>Finalized, no changes possible.</em></div>
                    <?php } ?>
                  </div>
                </div>
              </form>
          </div>
          <div class='span5'>
            <?php 
              if(strcmp($sem1ColDuration, "y") == 0){echo "<div hidden>"; }
            ?>
                  <!-- Semester 2 Choice -->
                    <h4>Semester 2:</h4>
                    <form id='sem2Selection'>
                      <input name='semester' type='hidden' value=2 />
                      <input name='teacher' type='hidden' value='<?php echo $userid ?>' />
                      <input name='duration' type='hidden' value='s' />
                      <input name='previous_duration' type='hidden' value='s' />
                      <input name='existing' type='hidden' value=<?php if($sem2Col){ echo 1; }else{ echo 0; } ?> />
                    <div class='control-group'>
                      <label class='control-label'>Colloquium</label>
                      <div class='controls'>
                          <select name='c_id' id='sem2Colloquium' <?php if($sem2ColFinal==1){ echo " disabled "; } ?> >
                            <option value=''></option>
                          <?php
                            //Traverse through teacher's colloquium repository
                            mysql_data_seek($get_colloquium_repository_result,0);
                            while($row = mysql_fetch_array($get_colloquium_repository_result)){
                              echo '<option';
                              if(strcmp($row['name'], $sem2ColName) == 0){ echo " selected"; }
                                echo ' value=' . $row['id'] . '>' . $row['name'] . '</option>';
                            }
                          ?>
                        </select>
                      </div>
                    </div>
                    <div class='control-group'>
                      <label class='control-label'>Notes for programmer: </label>
                      <div class='controls'>
                        <textarea name='notes' rows='5' id='sem2Notes' <?php if($sem2ColFinal==1){ echo " disabled "; } ?> ><?php if($sem2Col){ echo $sem2ColNotes; } ?></textarea>
                      </div>
                    </div>
                    <div class='control-group'>
                      <div class='controls'>
                        <?php
                          if($sem2ColFinal==0){
                        ?>
                          <div><button class='btn' type='submit' onClick='assign_colloquium(2)' >Update</button></div>
                          <?php if($sem2Col){ ?>
                            <div><em class='text-info'>Pending Approval</em></div>
                          <?php } ?>
                          <div id='sem2Status'></div>
                        <?php
                          }
                          else{
                        ?>
                          <div><em class='text-success'>Finalized, no changes possible.</em></div>
                        <?php } ?>
                      </div>
                    </div>
                  </form>
                <?php 
                  if(strcmp($sem1ColDuration, "y") == 0){echo "</div>"; }
                ?>
          </div>
        </div></div>
    </div> <!-- /container -->
  </body>
</html>
<?php
  session_start();
  //Credentials aren't legit or user isn't an admin, kick back to login screen
  if (!isset($_SESSION['username']) || 
    $_SESSION['login']!=true || 
    $_SESSION['admin']!=true) {
      header("Location: ../login.html");
  }
  include_once 'settings.php';
  //Connects to MySQL and Selects Database
  $con = mysql_connect($host,$db_username,$db_password);
  if (!$con)
    die('Could not connect: ' . mysql_error());
  //Select DB
  mysql_select_db($db, $con);
  //Get selected semester from URL
  $selected_semester=$_GET['semester'];
  //Get colloquium assignments for selected date
  $col_assignments_result=mysql_query(
      "SELECT users.lastname, users.firstname, c_assignments.id, c_assignments.final, colloquiums.name, 
              c_assignments.class_size, c_assignments.room, colloquiums.preferred_lunch_block, c_assignments.lunch_block, c_assignments.duration,
              colloquiums.preferred_class_size, colloquiums.preferred_room, c_assignments.notes
      FROM `users` 
      INNER JOIN `c_assignments` on c_assignments.teacher_id=users.id 
      INNER JOIN `colloquiums` on c_assignments.c_id=colloquiums.id 
      WHERE c_assignments.semester=$selected_semester") or die(mysql_error());
  //Calculate total seats offered
  $col_seats=0;
  $col_seats_result=mysql_query("SELECT class_size FROM c_assignments WHERE semester=$selected_semester AND final=1") or die(mysql_error());
  while ($row=mysql_fetch_array($col_seats_result)) {
    $col_seats+=$row['class_size'];
  }
  //Ghost usernames
  $ghost_usernames=array();
  $get_ghost_usernames=mysql_query(
    "SELECT username FROM users WHERE role='teacher' OR role='student'") or die(mysql_error());
  while($row=mysql_fetch_array($get_ghost_usernames)){
    $ghost_usernames[]="\"" . $row['username'] . "\"";
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
             <li><a href="index.php">Dashboard</a></li>
             <li class="dropdown active">
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
    <div class='container'>
      <h1>
        <?php 
          echo "Semester " . $selected_semester;
        ?>
      </h1>
      <hr />
      <div id='main' role='main'>
        <p class="lead pull-right">
          <?php echo $col_seats; ?> seats assigned.
        </p>
          <table class="table table-striped">
            <thead>
                <tr>
                  <th>Name</th>
                  <th>Colloquium</th>
                  <th>Duration</th>
                  <th>Preferred Class Size</th>
                  <th>Class Size</th>
                  <th>Preferred Room</th>
                  <th>Room</th>
                  <th>Preferred Lunch Block</th>
                  <th>Lunch Block</th>
                  <th>Notes</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                <?php
                  while ($row=mysql_fetch_array($col_assignments_result)) {
                    $seats_assigned=0;
                    ?>
                    <?php
                    echo "<tr>";
                    if(!$row['final']){
                      echo "<form action='finalize.php' method='post'>";
                    }
                    else{
                      echo "<form id='unfinalize" . $row['id'] . "' action='unfinalize.php' method='post'>";
                    }
                    echo "<input name='id' type='hidden' value='" . $row['id'] . "' />";
                    echo "<input name='type' type='hidden' value='colloquium' />";
                    echo "<input name='semester' type='hidden' value=$selected_semester />";
                    echo "<input id='keepstudents" . $row['id'] . "' name='keepstudents' type='hidden' value=1 />";
                    ?>
                    <?php
                    echo "<td>" . $row['lastname'] . ", " . $row['firstname'] . "</td>";
                    echo "<td>" . $row['name'] . "</td>";
                    echo "<td>";
                        if(strcmp($row['duration'],'s')==0)
                          echo "Semester";
                        else
                          echo "Year";
                    echo "</td>";
                    echo "<td>" . $row['preferred_class_size'] . "</td>";
                    if(!$row['final']){
                      if($row['class_size']!=0)
                        echo "<td><input class='input-mini' name='class_size' type='number' maxlength='4' value='" . $row['class_size'] . "' required /></td>";
                      else
                        echo "<td><input class='input-mini' name='class_size' type='number' maxlength='4' value='" . $row['preferred_class_size'] . "' required /></td>";
                    }
                    else{
                      echo "<td>" . $row['class_size'] . "</td>";
                    }
                    echo "<td>" . $row['preferred_room'] . "</td>";
                    if(!$row['final']){
                      if(!is_null($row['room']))
                        echo "<td><input class='input-mini' name='room' type='text' value='" . $row['room'] . "' required /></td>";
                      else
                        echo "<td><input class='input-mini' name='room' type='text' value='" . $row['preferred_room'] . "' required /></td>";
                    }
                    else{
                      echo "<td>" . $row['room'] . "</td>";
                    }
                    echo "<td>" . $row['preferred_lunch_block'] . "</td>";
                    if(!$row['final']){
                      if(!is_null($row['lunch_block'])){ ?>
                        <td>
                          <select class='input-mini' name='lunch_block' required>
                            <option value=''></option>
                            <option <?php if(strcmp($row['lunch_block'],'A')==0) echo ' selected '; ?> value='A'>A</option>
                            <option <?php if(strcmp($row['lunch_block'],'B')==0) echo ' selected '; ?> value='B'>B</option>
                            <option <?php if(strcmp($row['lunch_block'],'C')==0) echo ' selected '; ?> value='C'>C</option>
                            <option <?php if(strcmp($row['lunch_block'],'D')==0) echo ' selected '; ?> value='D'>D</option>
                          </select>
                        </td>
                      <?php } else{ ?>
                      <td>
                        <select class='input-mini' name='lunch_block' required>
                          <option value=''></option>
                          <option <?php if(strcmp($row['preferred_lunch_block'],'A')==0) echo ' selected '; ?> value='A'>A</option>
                          <option <?php if(strcmp($row['preferred_lunch_block'],'B')==0) echo ' selected '; ?> value='B'>B</option>
                          <option <?php if(strcmp($row['preferred_lunch_block'],'C')==0) echo ' selected '; ?> value='C'>C</option>
                          <option <?php if(strcmp($row['preferred_lunch_block'],'D')==0) echo ' selected '; ?> value='D'>D</option>
                        </select>
                      </td>
                    <?php
                    }}
                    else{
                      echo "<td>" . $row['lunch_block'] . "</td>";
                    }
                    echo "<td>";
                      if(strcmp($row['notes'],"")!=0){ ?>
                       <a href="#" class="tooltip" title="<?php echo $row['notes']; ?>" >Note</a>Note
                    <?php }
                    echo "</td>";
                    if(!$row['final']){
                      echo "<td><button class='btn btn-medium btn-warning' type='submit'>Finalize</button></td>";
                    }
                    else{
                      ?>
                      <!-- Modal -->
                      <div id="<?php echo $row['id']; ?>" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                        <div class="modal-header">
                          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                          <h3 id="myModalLabel">Unfinalize Course</h3>
                        </div>
                        <div class="modal-body">
                          <p>If there are already students enrolled in this course, what should happen to the current enrollments?</p>

                        </div>
                        <div class="modal-footer">
                          <button id="<?php echo $row['id']; ?>" class="btn btn-warning unenroll_students" type='submit'>Unenroll Students</button>
                          <button class="btn btn-primary" type='submit'>Keep Enrollments</button>
                        </div>
                      </div>
                      <?php
                      echo "<td><button class='btn btn-medium btn-success unfinalize_button' data-toggle='modal' data-target='#" . $row['id'] . "'>Unfinalize</button></td>";
                      //echo "<td><button class='btn btn-medium btn-success' type='submit'>Unfinalize</button></td>";
                    }
                    echo "</form>";
                    echo "</tr>";
                  }
                ?>
              </tbody>
          </table>
      </div>
    </div> <!-- /container -->
  </body>
</html>
<?php
session_start();

//Credentials aren't legit or user isn't an admin, kick back to login screen
if (!isset($_SESSION['username']) || 
  $_SESSION['login']!=true || 
  $_SESSION['teacher']!=true) {
    $_SESSION['from_teacher']=true;
    header("Location: ../login.html");
}
//Code to connect to database
//Grab all of teacher's colloquiums and store in array
include_once '../admin/db.php';
//Connects to MySQL and Selects Database
$con = mysql_connect($host,$db_username,$db_password);
if (!$con)
  die('Could not connect: ' . mysql_error());
mysql_select_db($db, $con);
$teacher = $_SESSION['username'];
$userid;
//Grab all of the teacher's colloquiums
$query = 'SELECT colloquiums.name, colloquiums.description, colloquiums.image, colloquiums.preferred_room, 
                 colloquiums.preferred_class_size, colloquiums.preferred_lunch_block, colloquiums.freshmen,
                 colloquiums.sophomores, colloquiums.juniors, colloquiums.seniors, users.id AS userid 
          FROM colloquiums INNER JOIN `users` on colloquiums.teacher_id = users.id WHERE users.username="' . $teacher . '"';
//Grab all of the teachers assigned colloquiums
$colQuery = 'SELECT c_assignments.duration, c_assignments.semester, c_assignments.c_id, c_assignments.notes, users.id AS userid
             FROM c_assignments INNER JOIN `users` on c_assignments.teacher_id = users.id WHERE users.username="' . $teacher . '"';
//Result of teacher's colloquiums
$result = mysql_query($query) or die(mysql_error());
//Result of the teacher's colloquium assignments
$colResult = mysql_query($colQuery) or die(mysql_error());
//Teacher has no assigned colloquiums
$sem1Col = false;
$sem2Col = false;
//Attributes of the teacher's semester 1 colloquium
$sem1ColName = null;
$sem1ColDuration = null;
$sem1ColNotes = null;
$sem1ColAssnID = null;
//Attributes of the teacher's semester 1 colloquium
$sem2ColName = null;
$sem2ColDuration = null;
$sem2ColNotes = null;
$sem2ColAssnID = null;
//Traverse through existing colloquium assignments
mysql_data_seek($colResult,0);
while($colRow = mysql_fetch_array($colResult)){
    //Attributes of individual colloquium assignment
    $c_id = $colRow['c_id'];
    $duration = $colRow['duration'];
    $semester = $colRow['semester'];
    $userid = $colRow['userid'];
    //If colloquium assignment is for a full year colloquium
    if(strcmp($duration, "y") == 0){
      //We have a Semester 1 and 2 assignment!
      $sem1Col = true;
      $sem2Col = true;
      //Find out the colloquium's name
      //Assign it to name variables for both Semesters
      mysql_data_seek($result,0);
      while($row = mysql_fetch_array($result)){
        if( $c_id == $row['id'] ){
          $sem1ColName = $row['name'];
          $sem2ColName = $row['name'];
        }
      }
      //Only need to set fields for Semester 1
      //Semester 2 fields besides name will be disabled
      $sem1ColDuration = $colRow['duration'];
      $sem1ColNotes = $colRow['notes'];
      $sem1ColAssnID = $colRow['id'];
    }
    //else if assignment is 1st Semester
    else if(strcmp($duration, "y") != 0 && $semester == 1){
      //We have a Semester 1 assignment!
      $sem1Col = true;
      //Find out the colloquium's name
      //Assign it to name variable for Semester 1
      mysql_data_seek($result,0);
      while($row = mysql_fetch_array($result)){
        if( $c_id == $row['id'] ){
          $sem1ColName = $row['name'];
        }
      }
      //Set fields for Semester 1
      $sem1ColDuration = $colRow['duration'];
      $sem1ColNotes = $colRow['notes'];
      $sem1ColAssnID = $colRow['id'];
    }
    //if assignment is 2nd Semester
    else if(strcmp($duration, "y") != 0 && $semester == 2){
      //We have a Semester 2 assignment!
      $sem2Col = true;
      //Find out the colloquium's name
      //Assign it to name variable for Semester 2
      mysql_data_seek($result,0);
      while($row = mysql_fetch_array($result)){
        if( $c_id == $row['id'] ){
          $sem2ColName = $row['name'];
        }
      }
      //Set fields for Semester 2
      $sem2ColDuration = $colRow['duration'];
      $sem2ColNotes = $colRow['notes'];
      $sem2ColAssnID = $colRow['id'];
    }
}
  mysql_close($con);
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
              <li><a href="agenda.php">Agenda</a></li>
              <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">X/Y <b class="caret"></b></a>
                <ul class="dropdown-menu">
                  <li><a href="xy.php#assign">Assign</a></li>
                  <li><a href="xy.php#manage">Manage</a></li>
                </ul>
              </li>
              <li class="dropdown active">
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
      <h2 id="assignLabel">Assign</h2>
      <hr />
      <div id="assign">
        <!-- Semester 1 Choice -->
        <div class="controls controls-row">
          <div class="span5">
            <h4>Semester 1:</h4>
            <!-- Form Name sem1Selection -->
            <?php 
              //If user has previously selected a Semester 1 colloquium
              if($sem1Col){
                echo '<form id="sem1ExistingSelection">';
              }
              else{
                echo '<form id="sem1Selection">';
              }
            ?>
            <div class="control-group">
              <label class="control-label">Colloquium</label>
              <div class="controls">
                <?php 
                  if($sem1Col){ 
                    echo '<select name="c_id" id="sem1Colloquium" disabled>';
                  }else{ 
                     echo '<select name="c_id" id="sem1Colloquium">'; 
                  }
                ?>
                  <?php
                    //If there isn't an existing colloquium choice for Semester 1
                    //first choice is blank
                    if(!$sem1Col){
                      echo '<option value=""></option>';
                    }
                    //Traverse through teacher's colloquium repository
                    mysql_data_seek($result,0);
                    while($row = mysql_fetch_array($result)){
                      $courseName = $row['name'];
                      $c_id = $row['id'];
                      $teacher = $row['teacher'];
                      //If current colloquium is selected colloquium the default to this one
                      if(strcmp($courseName, $sem1ColName) == 0){
                        echo "<option selected value=\"$c_id\">$courseName</option>";
                      }
                      //Add colloquium name to list, not selected
                      else{
                        echo "<option value=\"$c_id\">$courseName</option>";
                      }
                    }
                  ?>
                </select>
              </div>
            </div>
                <!-- Additional options once a course has been selected -->
                <!--
                  <?php 
                     //If user has previously selected a Semester 1 colloquium
                    if($sem1Col){
                      echo '<div id="sem1ExistingOptions">';
                    }
                    else{
                      echo '<div id="sem1Options">';
                    }
                  ?>
                -->
                <!-- Hidden variables to be used for form processing -->
                <input name="semester" type="hidden" value="1" />
                <input name="teacher" type="hidden" value='<?php echo $_SESSION["username"] ?>' />
                <input name="existing" type="hidden" value='<?php if($sem1Col){ echo "true"; }else{ echo "false"; } ?>' />
                <input name="assnID" type="hidden" value='<?php echo "$sem1ColAssnID" ?>' />
                <div class="control-group">
                  <label class="control-label">Duration: </label>
                  <div class="controls">
                    <?php 
                      if($sem1Col){ 
                        echo '<select name="duration" id="sem1Duration" disabled>';
                      }else{ 
                         echo '<select name="duration" id="sem1Duration">'; 
                      }
                    ?>
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
                <div class="control-group">
                  <label class="control-label">Notes for programmer: </label>
                  <div class="controls">
                    <?php 
                      if($sem1Col){ 
                        echo '<textarea name="notes" rows="5" id="sem1Notes" disabled>' . $sem1ColNotes . '</textarea>';
                      }else{ 
                         echo '<textarea name="notes" rows="5" id="sem1Notes">' . $sem1ColNotes . '</textarea>'; 
                      }
                    ?>
                  </div>
                </div>
                <div class="control-group">
                  <div class="controls">
                    <?php   
                      //If user has previously selected a Semester 1 colloquium
                      if(!$sem1Col){
                    ?>
                        <button class="btn" type="button" id="assignSem1ColButton">Assign</button>
                    <?php
                      }
                      else{
                    ?>
                        <button class="btn" type="button" id='editSem1ColAssnButton'>Edit</button>
                        <button class="btn" type="button" id='updateSem1ColAssnButton' style="display: none;">Update</button>
                    <?php
                      }
                    ?>
                    <div id="sem1Status"></div>
                  </div>
                </div>
              </form>
          </div>
          <div class="span5">
            <?php 
              if(strcmp($sem1ColDuration, "y") == 0){echo "<div hidden>"; }
            ?>
                  <!-- Semester 2 Choice -->
                    <h4>Semester 2:</h4>
                    <!-- Form Name sem2Selection -->
                    <?php 
                      //If user has previously selected a Semester 1 colloquium
                      if($sem2Col){
                        echo '<form id="sem2ExistingSelection">';
                      }
                      else{
                        echo '<form id="sem2Selection">';
                      }
                    ?>
                    <div class="control-group">
                      <label class="control-label">Colloquium</label>
                      <div class="controls">
                        <?php 
                            if($sem2Col){ 
                              echo '<select name="c_id" id="sem2Colloquium" disabled>';
                            }else{ 
                              echo '<select name="c_id" id="sem2Colloquium">'; 
                            }
                        ?>
                          <?php
                            //If there isn't an existing colloquium choice for Semester 2
                            //first choice is blank
                            if(!$sem2Col){
                              echo '<option value=""></option>';
                            }
                            //Traverse through teacher's colloquium repository
                            mysql_data_seek($result,0);
                            while($row = mysql_fetch_array($result)){
                              $courseName = $row['name'];
                              $c_id = $row['id'];
                              $teacher = $row['teacher'];
                              //If current colloquium is selected colloquium the default to this one
                              if(strcmp($courseName, $sem2ColName) == 0){
                                echo "<option selected value=\"$c_id\">$courseName</option>";
                              }
                              //Add colloquium name to list, not selected
                              else{
                                echo "<option value=\"$c_id\">$courseName</option>";
                              }
                            }
                          ?>
                        </select>
                      </div>
                    </div>
                    <!-- Additional options once a course has been selected -->
                    <?php 
                      //If user has previously selected a Semester 2 colloquium
                      if($sem2Col){
                        echo '<div id="sem2ExistingOptions">';
                      }
                      else{
                        echo '<div id="sem2Options">';
                      }
                    ?>
                    <!-- Hidden variables to be used for form processing -->
                    <input name="semester" type="hidden" value="2" />
                    <input name="duration" id="sem1Duration" type="hidden" value="s" />
                    <input name="teacher" type="hidden" value='<?php echo $_SESSION["username"] ?>' />
                    <input name="existing" type="hidden" value='<?php if($sem2Col){ echo "true"; }else{ echo "false"; } ?>' />
                    <input name="assnID" type="hidden" value='<?php echo "$sem2ColAssnID" ?>' /> 
                    <div class="control-group">
                      <label class="control-label">Notes for programmer: </label>
                      <div class="controls">
                        <?php 
                          if($sem2Col){ 
                            echo '<textarea name="notes" rows="5" id="sem2Notes" disabled>' . $sem2ColNotes . '</textarea>';
                          }else{ 
                             echo '<textarea name="notes" rows="5" id="sem2Notes">' . $sem2ColNotes . '</textarea>'; 
                          }
                        ?>
                      </div>
                    </div>
                    <div class="control-group">
                      <div class="controls">
                        <?php 
                          //If user has previously selected a Semester 2 colloquium
                          if($sem2Col){
                        ?>
                            <button class="btn" type="button" id='editSem2ColAssnButton'>Edit</button>
                            <button class="btn" type="button" id='updateSem2ColAssnButton' style="display: none;">Update</button>
                        <?php
                          }
                          else{
                            echo '<button type="button" class="btn" id="assignSem2ColButton">Assign</button>';
                          }
                        ?>
                        <div id="sem2Status"></div>
                      </div>
                    </div>
                  </form>          
                <?php 
                  if(strcmp($sem1ColDuration, "y") == 0){echo "</div>"; }
                ?>
          </div>
        </div></div></div>
 

      <!-- MANAGEMENT MODULE -->

      <div id="manage">
        <h2>Manage</h2>
        <hr />
        <div class="accordion" id="accordion2">
          <div class="accordion-group">
            <div class="accordion-heading">
              <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#addColloquium">
                <i class="icon-folder-close"></i> Add Colloquium...
              </a>
            </div>
            <div id="addColloquium" class="accordion-body collapse">
              <div class="accordion-inner">
                <!-- Add Colloquium Form -->
                <form class="form-horizontal" id="addColloquiumForm" enctype="multipart/form-data">
                  <!-- Colloquium Name -->
                  <div class="control-group">
                    <label class="control-label">Name</label>
                    <div class="controls">
                      <?php
                        echo "<input name=\"teacher\" type=\"hidden\" value=\"" . $_SESSION['username'] . "\" />";
                      ?>
                      <input type="text" class="span5" name="name">
                    </div>
                  </div>
                  <div class="control-group">
                    <label class="control-label">Description</label>
                    <div class="controls">
                      <textarea name="description" class="span5" rows="10"></textarea>
                    </div>
                  </div>
                  <div class="control-group">
                    <label class="control-label">Image</label>
                    <div class="controls">
                      <img id="preview" src="http://www.placehold.it/200x150/EFEFEF/AAAAAA&text=no+image" width="200px" height="200px" />
                      <p><i class="icon-resize-small"></i>Currently all images are resized to 200 x 200</p>
                      <button id="imageUpload" class="btn btn-small" type="button">Upload Image</button>
                      <input name="uploadedImg" id="uploadedImg" type="hidden" value="" />
                    </div>
                  </div>
                  <div class="control-group">
                    <label class="control-label">Preferred Room</label>
                    <div class="controls">
                      <input name="preferred_room" type="text" maxlength="4" />
                    </div>
                  </div>
                  <div class="control-group">
                    <label class="control-label">Preferred Class Size</label>
                    <div class="controls">
                      <select name="preferred_class_size">
                        <?php
                          for($i=0; $i<=31; $i++)
                            echo "<option value='" . $i . "'>" . $i . "</option>";
                        ?>
                      </select>
                    </div>
                  </div>
                  <div class="control-group">
                    <label class="control-label">Preferred Lunch Block</label>
                    <div class="controls">
                      <select name="preferred_lunch_block">
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                        <option value="D">D</option>
                      </select>
                    </div>
                  </div>
                  <div class="control-group">
                    <label class="control-label">Course open to </label>
                    <div class="controls">
                      <label class="checkbox inline">
                        <input type="checkbox" name="freshmen" value="1" checked>Freshmen
                      </label>
                      <label class="checkbox inline">
                        <input type="checkbox" name="sophomores" value="1" checked>Sophomores
                      </label>
                      <label class="checkbox inline">
                        <input type="checkbox" name="juniors" value="1" checked>Juniors
                      </label>
                      <label class="checkbox inline">
                        <input type="checkbox" name="seniors" value="1" checked>Seniors
                      </label>
                    </div>
                  </div>
                  <div class="control-group">
                    <div class="controls">
                      <button type="submit" class="btn">Add Course</button>
                      <div id="status"></div>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
          <?php 
            $numCourse = 1;
            mysql_data_seek($result,0);
            //Generates rows for colloquiums
            while($row = mysql_fetch_array($result)){
              $mysql_id = $row['id'];
              $courseName = $row['name'];
              $description = $row['description'];
              $image = $row['image'];
              $preferred_room = $row['preferred_room'];
              $preferred_class_size = $row['preferred_class_size'];
              $preferred_lunch_block = $row['preferred_lunch_block'];
              $freshmen = $row['freshmen'];
              $sophomores = $row['sophomores'];
              $juniors = $row['juniors'];
              $seniors = $row['seniors'];  
          ?>
          <!-- INSIDE THE WHILE LOOP -->
          <div class="accordion-group">
            <div class="accordion-heading">
              <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#<?php echo $numCourse; ?>">
                <i class="icon-folder-close"></i> <?php echo $courseName; ?>
              </a>
            </div>
            <div id="<?php echo $numCourse; ?>" class="accordion-body collapse">
              <div class="accordion-inner">
                <!-- Add Colloquium Form -->
                <form class="form-horizontal" id="updateColloquium<?php echo $numCourse; ?>" enctype="multipart/form-data">
                  <!-- Colloquium Name -->
                  <div class="control-group">
                    <label class="control-label">Name</label>
                    <div class="controls">
                      <?php
                        echo "<input name=\"teacher\" type=\"hidden\" value=\"" . $_SESSION['username'] . "\" />";
                        echo "<input name=\"form_id\" type=\"hidden\" value=\"" . $courseName . "\"  />";
                        echo "<input name=\"mysql_id\" type=\"hidden\" value=\"" . $mysql_id . "\"  />";
                      ?>
                      <input id='name<?php echo $numCourse; ?>' name='name' class="span5" type='text'value='<?php echo $courseName; ?>' disabled /> 
                    </div>
                  </div>
                  <div class="control-group">
                    <label class="control-label">Description</label>
                    <div class="controls">
                      <textarea id='description<?php echo $numCourse; ?>' name='description' class="span5" rows='10' disabled><?php echo $description; ?></textarea>
                    </div>
                  </div>
                  <div class="control-group">
                    <label class="control-label">Image</label>
                    <div class="controls">
                      <img id="preview" src="../img/courses/<?php echo $image; ?>" width="200px" height="200px" />
                      <p><i class="icon-resize-small"></i>Currently all images are resized to 200 x 200</p>
                      <div id="changeButton<?php echo $numCourse; ?>" style="display: none;">
                        <button id="imageUpload" class="btn btn-small" type="button">Change Image</button>
                      </div>
                      <input name="uploadedImg" id="uploadedImg" type="hidden" value="<?php echo $image; ?>" />
                    </div>
                  </div>
                  <div class="control-group">
                    <label class="control-label">Preferred Room</label>
                    <div class="controls">
                      <input id='preferred_room<?php echo $numCourse; ?>' name='preferred_room' 
                           type='text' maxlength='4' value='<?php echo $preferred_room; ?>' disabled />
                    </div>
                  </div>
                  <div class="control-group">
                    <label class="control-label">Preferred Class Size</label>
                    <div class="controls">
                      <select id='preferred_class_size<?php echo $numCourse; ?>' 
                            name='preferred_class_size' disabled>
                        <?php
                        for($i=0; $i<=31; $i++)
                        if($i == $preferred_class_size)
                          echo "<option selected value='" . $i . "'>" . $i . "</option>";
                        else
                          echo "<option value='" . $i . "'>" . $i . "</option>";
                        ?>
                      </select>
                    </div>
                  </div>
                  <div class="control-group">
                    <label class="control-label">Preferred Lunch Block</label>
                    <div class="controls">
                      <select id='preferred_lunch_block<?php echo $numCourse; ?>' 
                            name='preferred_lunch_block' disabled>
                        <?php 
                        if (strcmp($preferred_lunch_block, "A") == 0)
                          echo '<option selected value="A">A</option>';
                        else
                          echo '<option value="A">A</option>';
                          
                        if (strcmp($preferred_lunch_block, "B") == 0)
                          echo '<option selected value="B">B</option>';
                        else
                          echo '<option value="B">B</option>';
                          
                        if (strcmp($preferred_lunch_block, "C") == 0)
                          echo '<option selected value="C">C</option>';
                        else
                          echo '<option value="C">C</option>';
                          
                        if (strcmp($preferred_lunch_block, "D") == 0)
                          echo '<option selected value="D">D</option>';
                        else
                          echo '<option value="D">D</option>';
                        ?>
                      </select> 
                    </div>
                  </div>
                  <div class="control-group">
                    <label class="control-label">Course open to </label>
                    <div class="controls">
                      <label class="checkbox inline">
                        <?php 
                          if ($freshmen == 1)
                            echo '<input id=freshmen' . $numCourse . ' type="checkbox" name="freshmen" value="1" checked disabled>Freshmen';
                          else
                            echo '<input id=freshmen' . $numCourse . ' type="checkbox" name="freshmen" value="1" disabled >Freshmen';
                        ?>  
                      </label>
                      <label class="checkbox inline">
                        <?php 
                          if ($sophomores == 1)
                            echo '<input id=sophomores' . $numCourse . ' type="checkbox" name="sophomores" value="1" checked disabled>Sophomores';
                          else
                            echo '<input id=sophomores' . $numCourse . ' type="checkbox" name="sophomores" value="1" disabled>Sophomores';
                        ?>  
                      </label>
                      <label class="checkbox inline">
                        <?php 
                          if ($juniors == 1)
                            echo '<input id=juniors' . $numCourse . ' type="checkbox" name="juniors" value="1" checked disabled>Juniors';
                          else
                            echo '<input id=juniors' . $numCourse . ' type="checkbox" name="juniors" value="1" disabled>Juniors';
                        ?>  
                      </label>
                      <label class="checkbox inline">
                        <?php 
                          if ($seniors == 1)
                            echo '<input id=seniors' . $numCourse . ' type="checkbox" name="seniors" value="1" checked disabled>Seniors';
                          else
                            echo '<input id=seniors' . $numCourse . ' type="checkbox" name="seniors" value="1" disabled>Seniors';
                        ?>
                      </label>
                    </div>
                  </div>
                  <div class="control-group">
                    <label class="control-label">Delete?</label>
                    <div class="controls">
                      <select id='delete<?php echo $numCourse; ?>' name='delete' disabled>
                          <option selected value='n'>No</option>
                          <option value='y'>Yes</option>
                      </select>
                    </div>
                  </div>
                  <div class="control-group">
                    <div class="controls">
                      <div id="editColloquiumButton<?php echo $numCourse; ?>">
                        <button class="btn" type="button" onClick='edit_colloquium("<?php echo $numCourse ?>")'>Edit</button>
                      </div>
                      <div id="updateColloquiumButton<?php echo $numCourse; ?>" style="display: none;">
                        <button class="btn" type="button" onClick='update_colloquium("<?php echo $numCourse ?>")'>Update</button>
                      </div>
                      <div id="status<?php echo $numCourse ?>"></div>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
          <?php
              $numCourse++;
            }
          ?>
        </div>
      </div>
    </div> <!-- /container -->
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>
    <script src="../js/admin.js"></script>
    <script src="../js/ajaxupload.js"></script>
  </body>
</html>
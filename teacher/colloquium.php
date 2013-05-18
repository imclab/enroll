<?php
  session_start();

  //Credentials aren't legit or user isn't an admin, kick back to login screen
  if (!isset($_SESSION['username']) || 
    $_SESSION['login']!=true || 
    $_SESSION['teacher']!=true) {
      $_SESSION['from_teacher']=true;
      header('Location: ../login.html');
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
  $userid=NULL;
  //Grab all of the teacher's colloquiums
  $get_colloquium_repository_result=mysql_query('SELECT colloquiums.name, colloquiums.description, colloquiums.image, colloquiums.preferred_room, 
                   colloquiums.preferred_class_size, colloquiums.preferred_lunch_block, colloquiums.freshmen,
                   colloquiums.sophomores, colloquiums.juniors, colloquiums.seniors, users.id AS userid 
            FROM colloquiums INNER JOIN `users` on colloquiums.teacher_id = users.id WHERE users.username="' . $teacher . '"')
            or die (mysql_error());
  //Grab all of the teachers assigned colloquiums
  $get_colloquium_assignments_result=mysql_query('SELECT c_assignments.duration, c_assignments.semester, c_assignments.c_id, c_assignments.notes, users.id AS userid
               FROM c_assignments INNER JOIN `users` on c_assignments.teacher_id = users.id WHERE users.username="' . $teacher . '"')
                or die (mysql_error());
  //Teacher has no assigned colloquiums
  $sem1Col=false;
  $sem2Col=false;
  //Attributes of the teacher's semester 1 colloquium
  $sem1ColName=null;
  $sem1ColDuration=null;
  $sem1ColNotes=null;
  //Attributes of the teacher's semester 1 colloquium
  $sem2ColName=null;
  $sem2ColDuration=null;
  $sem2ColNotes=null;
  //Traverse through existing colloquium assignments
  mysql_data_seek($get_colloquium_assignments_result,0);
  while($colRow = mysql_fetch_array($get_colloquium_assignments_result)){
      //Attributes of individual colloquium assignment
      $c_id = $colRow['c_id'];
      $duration = $colRow['duration'];
      $semester = $colRow['semester'];
      if(is_null($userid))
        $userid = $colRow['userid'];
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
      }
  }
    mysql_close($con);
?>
<!DOCTYPE html>
<html lang='en'>
  <head>
    <meta charset='utf-8'>
    <title>Enroll: Northside Prep</title>
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
    <!-- <script src='http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js'></script> -->
    <script src='../js/jquery.min.js'></script>
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
    <script src='../js/admin.js'></script>

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
          <a class='brand appname' href='#'>Enroll</a>
          <div class='nav-collapse collapse'>
            <ul class='nav'>
              <li><a href='agenda.php'>Agenda</a></li>
              <li class='dropdown'>
                <a href='#' class='dropdown-toggle' data-toggle='dropdown'>X/Y <b class='caret'></b></a>
                <ul class='dropdown-menu'>
                  <li><a href='xy.php#assign'>Assign</a></li>
                  <li><a href='xy.php#manage'>Manage</a></li>
                </ul>
              </li>
              <li class='dropdown active'>
                <a href='#' class='dropdown-toggle' data-toggle='dropdown'>Colloquium <b class='caret'></b></a>
                <ul class='dropdown-menu'>
                  <li><a id='assignLink' href='colloquium.php#assign'>Assign</a></li>
                  <li><a id='manageLink' href='colloquium.php#manage'>Manage</a></li>
                </ul>
              </li>
            </ul>
            <ul class='nav pull-right'>
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
      <h2 id='assignLabel'>Assign</h2>
      <hr />
      <div id='assign'>
        <!-- Semester 1 Choice -->
        <div class='controls controls-row'>
          <div class='span5'>
            <h4>Semester 1:</h4>
            <!-- Form Name sem1Selection -->
            <form id='sem1Selection'>
              <input name='semester' type='hidden' value='1' />
              <input name='teacher' type='hidden' value='<?php echo $userid ?>' />
              <input name='existing' type='hidden' value='<?php if($sem1Col){ echo "true"; }else{ echo "false"; } ?>' />
            <div class='control-group'>
              <label class='control-label'>Colloquium</label>
              <div class='controls'>
                  <select name='c_id' id='sem1Colloquium' <?php if($sem1Col){ echo 'disabled'; } ?> >
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
                <select name='duration' id='sem1Duration' <?php if($sem1Col){ echo 'disabled'; } ?> >
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
                    <textarea name='notes' rows='5' id='sem1Notes' <?php if($sem1Col){ echo 'disabled'; } ?> ><?php if($sem1Col){ echo $sem1ColNotes; } ?></textarea>
                  </div>
                </div>
                <div class='control-group'>
                  <div class='controls'>
                    <button class='btn' type='submit' onClick='assign_colloquium("1")' >Update</button>
                    <div id='sem1Status'></div>
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
                      <input name='semester' type='hidden' value='2' />
                      <input name='teacher' type='hidden' value='<?php echo $userid ?>' />
                      <input name='existing' type='hidden' value='<?php if($sem2Col){ echo "true"; }else{ echo "false"; } ?>' />
                    <div class='control-group'>
                      <label class='control-label'>Colloquium</label>
                      <div class='controls'>
                          <select name='c_id' id='sem2Colloquium' <?php if($sem2Col){ echo 'disabled'; } ?> >
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
                      <label class='control-label'>Duration: </label>
                      <div class='controls'>
                        <select name='duration' id='sem2Duration' <?php if($sem2Col){ echo 'disabled'; } ?> >
                          <option selected value=''></option>
                          <?php 
                            if($sem2Col && strcmp($duration, "s") == 0)
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
                            <textarea name='notes' rows='5' id='sem2Notes' <?php if($sem2Col){ echo 'disabled'; } ?> ><?php if($sem2Col){ echo $sem2ColNotes; } ?></textarea>
                          </div>
                        </div>
                        <div class='control-group'>
                          <div class='controls'>
                            <button class='btn' type='submit' onClick='assign_colloquium("2")' >Update</button>
                            <div id='sem2Status'></div>
                          </div>
                        </div>
                      </form>
                <?php 
                  if(strcmp($sem1ColDuration, "y") == 0){echo "</div>"; }
                ?>
          </div>
        </div></div>
 

      <!-- MANAGEMENT MODULE -->

      <div id='manage'>
        <h2>Manage</h2>
        <hr />
        <!-- CODE TO ADD A NEW COLLOQUIUM COURSE -->
        <div class='accordion' id='accordion2'>
          <div class='accordion-group'>
            <div class='accordion-heading'>
              <a class='accordion-toggle' data-toggle='collapse' data-parent='#accordion2' href='#addColloquium'>
                <i class='icon-folder-close'></i> Add Colloquium...
              </a>
            </div>
            <div id='addColloquium' class='accordion-body collapse'>
              <div class='accordion-inner'>
                <!-- Add Colloquium Form -->
                <form class='form-horizontal' id='addColloquiumForm' enctype='multipart/form-data'>
                  <input name='teacher' type='hidden' value='<?php echo $userid; ?>' />
                  <!-- Colloquium Name -->
                  <div class='control-group'>
                    <label class='control-label'>Name</label>
                    <div class='controls'>
                      <input type='text' class='span5' name='name' required />
                    </div>
                  </div>
                  <div class='control-group'>
                    <label class='control-label'>Description</label>
                    <div class='controls'>
                      <textarea name='description' class='span5' rows='10' required></textarea>
                    </div>
                  </div>
                  <div class='control-group'>
                    <label class='control-label'>Image</label>
                    <div class='controls'>
                      <img id='preview' src='http://www.placehold.it/200x150/EFEFEF/AAAAAA&text=no+image' width='200px' height='200px' />
                      <p><i class='icon-resize-small'></i>Currently all images are resized to 200 x 200</p>
                      <button id='imageUpload' class='btn btn-small' type='button'>Upload Image</button>
                      <input name='uploadedImg' id='uploadedImg' type='hidden' value='' />
                    </div>
                  </div>
                  <div class='control-group'>
                    <label class='control-label'>Preferred Room</label>
                    <div class='controls'>
                      <input name='preferred_room' type='text' maxlength='4' required />
                    </div>
                  </div>
                  <div class='control-group'>
                    <label class='control-label'>Preferred Class Size</label>
                    <div class='controls'>
                      <input name='preferred_class_size' type='number' min='10' maxlength='4' required />
                    </div>
                  </div>
                  <div class='control-group'>
                    <label class='control-label'>Preferred Lunch Block</label>
                    <div class='controls'>
                      <select name='preferred_lunch_block' required>
                        <option value=''></option>
                        <option value='A'>A</option>
                        <option value='B'>B</option>
                        <option value='C'>C</option>
                        <option value='D'>D</option>
                      </select>
                    </div>
                  </div>
                  <div class='control-group'>
                    <label class='control-label'>Course open to </label>
                    <div class='controls'>
                      <label class='checkbox inline'>
                        <input type='checkbox' name='freshmen' value='1' checked>Freshmen
                      </label>
                      <label class='checkbox inline'>
                        <input type='checkbox' name='sophomores' value='1' checked>Sophomores
                      </label>
                      <label class='checkbox inline'>
                        <input type='checkbox' name='juniors' value='1' checked>Juniors
                      </label>
                      <label class='checkbox inline'>
                        <input type='checkbox' name='seniors' value='1' checked>Seniors
                      </label>
                    </div>
                  </div>
                  <div class='control-group'>
                    <div class='controls'>
                      <button type='submit' class='btn'>Add Course</button>
                      <div id='status'></div>
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
                <form class="form-horizontal" id="updateColloquiumForm<?php echo $numCourse; ?>" enctype="multipart/form-data">
                  <input name='teacher' type='hidden' value='<?php echo $userid; ?>' />
                  <input name='form_id' type='hidden' value='<?php echo $courseName; ?>' />
                  <input name='mysql_id' type='hidden' value='<?php echo $mysql_id; ?>' />
                  <!-- Colloquium Name -->
                  <div class="control-group">
                    <label class="control-label">Name</label>
                    <div class="controls">  
                      <input id='name<?php echo $numCourse; ?>' name='name' class="span5" type='text' value='<?php echo $courseName; ?>' disabled required /> 
                    </div>
                  </div>
                  <div class="control-group">
                    <label class="control-label">Description</label>
                    <div class="controls">
                      <textarea id='description<?php echo $numCourse; ?>' name='description' class="span5" rows='10' disabled required><?php echo $description; ?></textarea>
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
                           type='text' maxlength='4' value='<?php echo $preferred_room; ?>' disabled required />
                    </div>
                  </div>
                  <div class='control-group'>
                    <label class='control-label'>Preferred Class Size</label>
                    <div class='controls'>
                      <input id='preferred_class_size<?php echo $numCourse; ?>' name='preferred_class_size' type='number' min='10' maxlength='4' disabled required />
                    </div>
                  </div>
                  <div class='control-group'>
                    <label class='control-label'>Preferred Lunch Block</label>
                    <div class='controls'>
                      <select id='preferred_lunch_block<?php echo $numCourse; ?>' 
                            name='preferred_lunch_block' disabled required>
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
                  <div class='control-group'>
                    <label class='control-label'>Course open to </label>
                    <div class='controls'>
                      <label class='checkbox inline'>
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
                  <div class='control-group'>
                    <label class='control-label'>Delete?</label>
                    <div class='controls'>
                      <select id='delete<?php echo $numCourse; ?>' name='delete' disabled>
                          <option selected value='n'>No</option>
                          <option value='y'>Yes</option>
                      </select>
                    </div>
                  </div>
                  <div class='control-group'>
                    <div class='controls'>
                      <div id='editColloquiumButton<?php echo $numCourse; ?>'>
                        <button class='btn' type='button' onClick='edit_colloquium("<?php echo $numCourse ?>")'>Edit</button>
                      </div>
                      <div id="updateColloquiumButton<?php echo $numCourse; ?>" style="display: none;">
                        <button class="btn" type="button" onClick='update_colloquium("<?php echo $numCourse ?>")'>Update</button>
                      </div>
                      <div id='status<?php echo $numCourse ?>'></div>
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
  </body>
</html>
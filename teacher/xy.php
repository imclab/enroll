<?php
  session_start();
  //Not logged in or doesn't have the teacher role
  if (!isset($_SESSION['username']) || 
    $_SESSION['login']!=true || 
    $_SESSION['student']) 
  {
      header("Location: ../login.html");
  }
  //Code to connect to database
  include_once '../admin/db.php';
  //Connects to MySQL and Selects Database
  $con=mysql_connect($host,$db_username,$db_password);
  if (!$con)
    die('Could not connect: ' . mysql_error());
  //Select DB
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
  //Grab all dates xy is offered 
  $get_dates_result=mysql_query("SELECT id, date FROM dates WHERE schedule='a'") or die(mysql_error());
  //Get all xy assignments
  $get_xy_assignments_result=mysql_query(
              "SELECT dates.date, dates.id AS dateid, xy.name, xy_assignments.notes, 
                      xy_assignments.preferred_block,xy_assignments.final 
              FROM `dates` 
              LEFT JOIN `xy_assignments` on xy_assignments.date_id = dates.id 
              LEFT JOIN `xy` on xy_assignments.xy_id = xy.id 
              WHERE dates.schedule ='a' AND xy_assignments.teacher_id=$userid") or die(mysql_error());
  //Array of all dates with assignments if they exist
  $xy_assignments_dates=array();
  //Reset to first element in array
  mysql_data_seek($xyResult,0);
  //Fill array with arrays that consist of existing xy assignments
  while($row=mysql_fetch_array($get_xy_assignments_result)){
    $xy_assignments_dates[]=$row;
  }
  //Grab all of the teacher's xy repository
  $get_xy_repository_result=mysql_query("SELECT * FROM xy WHERE teacher_id=$userid") or die(mysql_error());
  //Close MySQL Connection, all necessary queries have been run
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
    <!-- CSS -->
    <style>
      body {
        padding-top: 60px; /** Clear the top bar **/
      }
    </style>
    <link href="../css/bootstrap.css" rel="stylesheet">
    <link href="../css/bootstrap-responsive.css" rel="stylesheet">
    <link href="../css/admin.css" rel="stylesheet">
    
    <!-- JQUERY -->
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <!-- BOOTSTRAP -->
    <script src="../js/bootstrap.min.js"></script>
    <!-- FORM VALIDATION USING JQUERY -->
    <!-- http://alittlecode.com/jquery-form-validation-with-styles-from-twitter-bootstrap/ -->
    <script src="../js/jquery.validate.min.js"></script>
    <!-- AJAX UPLOAD BY BRYAN GENTRY -->
    <!-- http://bryangentry.us/ajax-upload-with-javascript-and-php-upload-an-image-and-display-a-preview/ -->
    <script src="../js/ajaxupload.js"></script>
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
    <!-- TOP MENU -->
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
              <li class="dropdown active">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">X/Y <b class="caret"></b></a>
                <ul class="dropdown-menu">
                  <li><a href="xy.php#assign">Assign Course</a></li>
                  <li><a href="xy.php#manage">Course Repository</a></li>
                  <li><a href="preenroll_xy.php">Pre-Enroll Students</a></li>
                </ul>
              </li>
              <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Colloquium <b class="caret"></b></a>
                <ul class="dropdown-menu">
                  <li><a href="colloquium.php#assign">Assign Course</a></li>
                  <li><a href="colloquium.php#manage">Course Repository</a></li>
                  <li><a href="preenroll_col.php">Pre-Enroll Students</a></li>
                </ul>
              </li>
            </ul>
            <ul class="nav pull-right">
              <?php if(!is_null($ghostuser)){ ?>
              <li><a href="javascript:void(0)" onclick='ghost_user("<?php echo $master_username; ?>","admin");'><?php echo $master_username; ?></a></li>
              <?php 
                }
                if(!isset($_SESSION['username']))
                  echo "<li><a href='../login.html'>Login</a></li>";
                else
                  echo "<li><a href='../logout.php'>Logout</a></li>";
              ?>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <div class="container">
      <!-- ASSIGNMENT MODULE -->
      <h2 id="assignLabel">Assign Course</h2>
      <hr />
      <div id="assign">
        <!-- SCROLLING MONTHS ALONG TOP -->
        <i id="previous" class="icon-chevron-left"></i>
        <div id="month"></div>
        <i id="next" class="icon-chevron-right"></i>
        <hr />
        <div class="controls controls-row">
          <?php 
            //Array filled with names of the month
            $months = array('January','February','March','April','May','June','July','August','September','October','November','December');
            //This for loop creates a hidden div for each month with the respective xy dates
            foreach ($months as $month) {
              echo "<div id='$month' class='selectedMonth' hidden>";
              //Make a form for every date within the selected month
              mysql_data_seek($get_dates_result,0);
              while($selected_date=mysql_fetch_array($get_dates_result)){
                $date = $selected_date['date']; //ie. YYYY-MM-DD
                $xyAssigned=false;
                $xyName=NULL;
                $xyNotes=NULL;
                $xyPreferredBlock=NULL;
                $xyDateID=$selected_date['id'];
                $xyMonth = date('F', strtotime($date)); //ie. January
                //If the month that is selected matches the month of the variable in the array
                if(strcmp($month, $xyMonth) == 0){
                  //Check to see if there is an xy assigned to this date
                  //Traverse xy assignments result
                  mysql_data_seek($get_xy_assignments_result,0);
                  while($xy_assignment=mysql_fetch_array($get_xy_assignments_result)){
                    if(strcmp($date,$xy_assignment['date']) == 0){
                      //XY is assigned, set other variables
                      $xyAssigned=true;
                      $xyName=$xy_assignment['name'];
                      $xyNotes=$xy_assignment['notes'];
                      $xyPreferredBlock=$xy_assignment['preferred_block'];
                      $xyDateID=$xy_assignment['dateid'];
                      $xyFinal=$xy_assignment['final'];
                      break;
                    }
                  }
          ?>
                  <!-- Still in the for loop for a date that matches the current month we are traversing -->
                  <div class="span5">
                    <form class='form-horizontal' id='selection<?php echo $xyDateID; ?>' enctype='multipart/form-data'>
                      <input name="teacher_id" type="hidden" value="<?php echo $userid; ?>" />
                      <input name="existing" type="hidden" value='<?php if($xyAssigned){ echo "true"; }else{ echo "false"; } ?>' />
                      <input name="date_id" type="hidden" value="<?php echo $xyDateID; ?>" />
                      <div class="control-group">
                        <!-- Displays date as Wednesday the 28th -->
                        <label class="control-label"><strong><?php echo date('l \t\h\e jS', strtotime($date)); ?></strong></label>
                        <div class="controls"></div>
                      </div>
                      <div class="control-group">
                        <label class="control-label">Course Name:</label>
                        <!-- CHOOSE WHICH XY IS ASSIGNED TO THIS DATE -->
                        <div class="controls">
                          <?php
                            $courseName;
                            //If there is already an XY assigned to this date
                            echo "<select name='xy_id' id='name$xyDateID' class='selectedXYDate' required";
                            if($xyFinal==1){ echo " disabled "; }
                            echo ">";
                            echo "<option value=''></option>";
                            //Traverse through teacher's XY repository
                            mysql_data_seek($get_xy_repository_result,0);
                            while($row = mysql_fetch_array($get_xy_repository_result)){
                              echo "<option value=" . $row['id'];
                              //Add selected attribute if current course is the one already assigned
                              if(strcmp($xyName, $row['name']) == 0)
                                echo " selected ";
                              echo ">" . $row['name'] . "</option>";
                            }
                          ?>
                          </select>
                        </div>
                      </div>
                      <div class="control-group">
                        <label class="control-label">Block Preference:</label>
                        <div class="controls">
                          <select name='blockpreference' id='preferred_block<?php echo $xyDateID; ?>' required  <?php if($xyFinal==1){ echo " disabled "; } ?> >
                            <option value=''></option>
                          <?php
                            if(strcmp($xyPreferredBlock, "x") == 0)
                              echo '<option selected value="x">X</option>';
                            else
                              echo '<option value="x">X</option>';
                            if(strcmp($xyPreferredBlock, "y") == 0)
                              echo '<option selected value="y">Y</option>';
                            else
                              echo '<option value="y">Y</option>';
                            if(strcmp($xyPreferredBlock, "xy") == 0)
                              echo '<option selected value="xy">Span both X and Y</option>';
                            else
                              echo '<option value="xy">Span both X and Y</option>';
                          ?>
                          </select>
                        </div>
                      </div>
                      <div class="control-group">
                        <label class="control-label">Notes for programmer:</label>
                        <div class="controls">
                          <?php
                              echo "<textarea name='notes' id='notes$xyDateID' rows='5'";
                              if($xyFinal==1){ echo " disabled "; }
                              echo ">";
                              if($xyAssigned){ echo $xyNotes; }
                              echo "</textarea>";
                          ?>
                        </div>
                      </div>
                      <div class="control-group">
                        <div class="controls">
                          <?php
                            if($xyFinal==0){
                          ?>
                            <div><button class='btn' type='submit' onClick='assign_xy("<?php echo $xyDateID; ?>")' >Update</button></div>
                            <?php if($xyAssigned){ ?>
                              <div><em class='text-info'>Pending Approval</em></div>
                            <?php } ?>
                            <div id='status<?php echo $xyDateID; ?>'></div>
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
                <?php
                  }
                  }
                  echo "</div>";
                  }
                ?>
            </div>
      </div>
      <!-- MANAGEMENT MODULE -->
      <div id='manage'>
        <h2>Course Repository</h2>
        <hr />
        <div class='accordion' id='accordion2'>
          <!-- ACCORDIAN TO ADD A NEW XY COURSE -->
          <div class='accordion-group'>
            <div class='accordion-heading'>
              <a class='accordion-toggle' data-toggle='collapse' data-parent='#accordion2' href='#addXY'>
                <i class='icon-folder-close'></i> Add XY...
              </a>
            </div>
            <div id='addXY' class='accordion-body collapse'>
              <div class='accordion-inner'>
                <!-- Add XY Form -->
                <form class='form-horizontal' action="update_xy.php" method="post" enctype='multipart/form-data'>
                  <input name='teacher' type='hidden' value='<?php echo $userid; ?>' />
                  <input name='existing' type='hidden' value=0 />
                  <!-- XY Name -->
                  <div class='control-group'>
                    <label class='control-label'>Course Name</label>
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
                    <label class='control-label'>Image:</label>
                    <div class="controls">
                      <input type="file" name="image" id="image" />
                      <p><i class="icon-resize-small"></i>Currently all images are resized to 200 x 200</p>
                    </div>
                  </div>
                  <div class='control-group'>
                    <label class='control-label'>Category</label>
                    <div class='controls'>
                      <select name='category' required>
                        <option value=''></option>
                        <option value='1'>Academic Enhancement</option>
                        <option value='2'>Enrichment</option>
                        <option value='3'>Health &amp; Wellness</option>
                      </select>
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
            $numCourse = 11;
            mysql_data_seek($get_xy_repository_result,0);
            //Generates rows for colloquiums
            while($row = mysql_fetch_array($get_xy_repository_result)){
              $mysql_id = $row['id'];
              $courseName = $row['name'];
              $description = $row['description'];
              $image = $row['image'];
              $category = $row['category'];
              $preferred_room = $row['preferred_room'];
              $preferred_class_size = $row['preferred_class_size'];
              $freshmen = $row['freshmen'];
              $sophomores = $row['sophomores'];
              $juniors = $row['juniors'];
              $seniors = $row['seniors'];  
          ?>
          <!-- INSIDE THE WHILE LOOP -->
          <div class='accordion-group'>
            <div class='accordion-heading'>
              <a class='accordion-toggle' data-toggle='collapse' data-parent='#accordion2' href='#<?php echo $numCourse; ?>'>
                <i class='icon-folder-close'></i> <?php echo $courseName; ?>
              </a>
            </div>
            <div id='<?php echo $numCourse; ?>' class='accordion-body collapse'>
              <div class='accordion-inner'>
                <form class='form-horizontal' action="update_xy.php" method="post" enctype='multipart/form-data'>
                  <input name='teacher' type='hidden' value='<?php echo $userid; ?>' />
                  <input name='mysql_id' type='hidden' value='<?php echo $mysql_id; ?>'  />
                  <input name='originalimage' type='hidden' value='<?php echo $image; ?>' />
                  <input name='existing' type='hidden' value=1 />
                  <!-- XY Name -->
                  <div class='control-group'>
                    <label class='control-label'>Course Name</label>
                    <div class='controls'>
                      <input id='name<?php echo $numCourse; ?>' name='name' class='span5' type='text' value='<?php echo $courseName; ?>' disabled required /> 
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
                      <img id="currentImage" src="../img/courses/<?php echo $image; ?>" width="200px" height="200px" />
                      <div id="changeButton<?php echo $numCourse; ?>" style="display: none;">
                        <input type="file" name="image" id="image" />
                      </div>
                      <p><i class="icon-resize-small"></i>Currently all images are resized to 200 x 200</p>
                    </div>
                  </div>
                  <div class="control-group">
                    <label class="control-label">Category</label>
                    <div class="controls">
                      <select id='category<?php echo $numCourse; ?>' name='category' disabled required>
                        <?php 
                          if ($category == 1) {
                            echo "<option selected value='1'>Academic Enhancement</option>";
                            echo "<option value='2'>Enrichment</option>";
                            echo "<option value='3'>Health &amp; Wellness</option>";
                          }
                          else if ($category == 2){
                            echo "<option value='1'>Academic Enhancement</option>";
                            echo "<option selected value='2'>Enrichment</option>";
                            echo "<option value='3'>Health &amp; Wellness</option>";
                          }
                          else{
                            echo "<option value='1'>Academic Enhancement</option>";
                            echo "<option value='2'>Enrichment</option>";
                            echo "<option selected value='3'>Health &amp; Wellness</option>";
                          }
                        ?>
                      </select>
                    </div>
                  </div>
                  <div class="control-group">
                    <label class="control-label">Preferred Room</label>
                    <div class="controls">
                      <input id='preferred_room<?php echo $numCourse; ?>' name='preferred_room' 
                           type='text' maxlength='4' value='<?php echo $preferred_room; ?>' disabled required />
                    </div>
                  </div>
                  <div class="control-group">
                    <label class="control-label">Preferred Class Size</label>
                    <div class="controls">
                      <input id='preferred_class_size<?php echo $numCourse; ?>' name='preferred_class_size' 
                           type='number' min="10" maxlength='4' value='<?php echo $preferred_class_size; ?>' disabled required />
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
                      <div id="editXYButton<?php echo $numCourse; ?>">
                        <button class="btn" type="button" onClick='edit_XY("<?php echo $numCourse ?>")'>Edit</button>
                      </div>
                      <div id="updateXYButton<?php echo $numCourse; ?>" style="display: none;">
                        <button class="btn" type="submit" >Update</button>
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
    </div>
  </body>
</html>
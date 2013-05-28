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
  include_once '../admin/settings.php';
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
  //Grab all of the teacher's xy repository
  $get_xy_repository_result=mysql_query("SELECT * FROM xy WHERE teacher_id=$userid") or die(mysql_error());
  //Close MySQL Connection, all necessary queries have been run
  mysql_close($con);
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Enroll: <?php echo $school_name; ?></title>
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
          <a class="brand appname" href="#">Enroll<img src='../img/beta-icon.png' style="vertical-align:text-top;"/></a>
          <div class="nav-collapse collapse">
            <ul class="nav">
              <li><a href="agenda.php">Agenda</a></li>
              <li class="dropdown active">
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
      <?php if($_GET['status']==0 && !is_null($_GET['status'])) { ?>
      <div id="failed" class="alert alert-error text-center">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        Sorry, changes did not save.
      </div>
      <?php }else if($_GET['status']==1 && !is_null($_GET['status'])) { ?>
      <div id="success" class="alert alert-info text-center">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        Changes saved successfully.
      </div>
      <?php } ?>
    </div>
    <div class="container">
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
                      <input type="file" name="image" id="image" required />
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
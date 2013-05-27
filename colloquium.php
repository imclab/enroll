<!DOCTYPE html>
<?php
  session_start();
  //Credentials aren't legit or user isn't an admin, kick back to login screen
  if (!isset($_SESSION['username'])){
    $loggedin=false;
    $username=NULL;
  }
  else{
    $loggedin=true;
    $master_username=$_SESSION['username'];
    $ghostuser=$_SESSION['ghostuser'];
    if(!is_null($ghostuser))
      $username=$_SESSION['ghostuser'];
    else
      $username=$_SESSION['username'];
  }
  //Code to connect to database
  include_once 'admin/settings.php';
  //Connects to MySQL and Selects Database
  $con = mysql_connect($host,$db_username,$db_password);
  if (!$con)
    die('Could not connect: ' . mysql_error());
  //Select DB
  mysql_select_db($db, $con);
  //Get class levels
  $get_settings_result=mysql_query(
    "SELECT freshman,sophomore,junior,senior,col1_freshman_start,col1_sophomore_start,
            col1_junior_start,col1_senior_start,col1_end,col2_freshman_start,col2_sophomore_start,
            col2_junior_start,col2_senior_start,col2_end 
     FROM settings 
     LIMIT 1") or die(mysql_error());
  $get_settings_array=mysql_fetch_array($get_settings_result);
  $freshman=$get_settings_array['freshman'];
  $sophomore=$get_settings_array['sophomore'];
  $junior=$get_settings_array['junior'];
  $senior=$get_settings_array['senior'];
  //Get user's class level
  $class_level=null;
  $col1_start=null;
  $col1_end=$get_settings_array['col1_end'];
  $col2_start=null;
  $col2_end=$get_settings_array['col2_end'];
  $get_graduation_year_result=mysql_query(
      "SELECT graduation_year FROM users WHERE username='$username' LIMIT 1") or die(mysql_error());
  $get_graduation_year_array=mysql_fetch_array($get_graduation_year_result);
  $graduation_year=$get_graduation_year_array['graduation_year'];
  if($graduation_year==$freshman){
    $class_level="freshman";
    $col1_start=$get_settings_array['col1_freshman_start'];
    $col2_start=$get_settings_array['col2_freshman_start'];
  }
  elseif($graduation_year==$sophomore){
    $class_level="sophomore";
    $col1_start=$get_settings_array['col1_sophomore_start'];
    $col2_start=$get_settings_array['col2_sophomore_start'];
  }
  elseif($graduation_year==$junior){
    $class_level="junior";
    $col1_start=$get_settings_array['col1_junior_start'];
    $col2_start=$get_settings_array['col2_junior_start'];
  }
  elseif($graduation_year==$senior){
    $class_level="senior";
    $col1_start=$get_settings_array['col1_senior_start'];
    $col2_start=$get_settings_array['col2_senior_start'];
  }
  //Check to see if registration window is currently open
  $col1_register=false;
  $col2_register=false;
  if(time() >= strtotime($col1_start) && time() < strtotime($col1_end))
    $col1_register=true;
  if(time() >= strtotime($col2_start) && time() < strtotime($col2_end))
    $col2_register=true;
  //Get next date for colloquium courses
  $next_col_result=mysql_query("SELECT id,date FROM dates WHERE date >= " .  date('Y-m-d') . " ORDER BY date LIMIT 1") or die(mysql_error());
  $next_col_row= mysql_fetch_array($next_col_result);
  $next_col=$next_col_row['date'];
  $next_col_id=$next_col_row['id'];
  //Grab all of the teacher's colloquium options
  $query = "SELECT users.lastname, users.firstname, c_assignments.id AS cassnid, c_assignments.duration, 
                   c_assignments.semester, colloquiums.name, colloquiums.description, colloquiums.image, 
                   colloquiums.freshmen, colloquiums.sophomores, colloquiums.juniors, colloquiums.seniors, c_assignments.class_size 
            FROM `users` 
            INNER JOIN `c_assignments` on c_assignments.teacher_id = users.id
            INNER JOIN `colloquiums` on c_assignments.c_id = colloquiums.id 
            WHERE c_assignments.final=1";
  //Result of above query
  $result = mysql_query($query) or die(mysql_error());
  $chosen_col1_name=NULL;
  $chosen_col1_image=NULL;
  $chosen_col1_id=NULL;
  $chosen_col1_duration=NULL;
  $chosen_col1_room=NULL;
  $chosen_col2_name=NULL;
  $chosen_col2_image=NULL;
  $chosen_col2_id=NULL;
  $chosen_col2_room=NULL;
  //If user is logged in
  if(isset($_SESSION['username'])){
    $chosen_col_result=mysql_query("SELECT users.username, users.graduation_year, colloquiums.name, colloquiums.image, 
                                           c_assignments.duration, c_assignments.semester, c_assignments.id,c_assignments.room 
                                   FROM `users` 
                                   INNER JOIN `c_enrollments` on users_id = users.id 
                                   INNER JOIN `c_assignments` on c_assignments.id = c_enrollments.c_assignments_id 
                                   INNER JOIN `colloquiums` on c_assignments.c_id = colloquiums.id 
                                   WHERE users.username=\"$username\"") or die(mysql_error());
    while($chosen_col_row = mysql_fetch_array($chosen_col_result)){
      if(strcmp($chosen_col_row['semester'],"1") == 0){
        $chosen_col1_name=$chosen_col_row['name'];
        $chosen_col1_image=$chosen_col_row['image'];
        $chosen_col1_id=$chosen_col_row['id'];
        $chosen_col1_duration=$chosen_col_row['duration'];
        $chosen_col1_room=$chosen_col_row['room'];
      }
      else if(strcmp($chosen_col_row['semester'],"2") == 0){
        $chosen_col2_name=$chosen_col_row['name'];
        $chosen_col2_image=$chosen_col_row['image'];
        $chosen_col2_id=$chosen_col_row['id'];
        $chosen_col2_room=$chosen_col_row['room'];
      }
    }
  }
  
?>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Enroll: <?php echo $school_name; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Flexible Scheduling for Today's Classroom">
    <meta name="author" content="Marcos Alcozer">
    <meta name="keywords" content="Education, Scheduling">

    <!-- Le styles -->
    <style>
      body {
        padding-top: 60px; /* 60px to make the container go all the way to the bottom of the topbar */
      }
    </style>
    <link href="css/wookmark.css" rel="stylesheet">
    <link href="css/bootstrap.css" rel="stylesheet">
    <link href="css/bootstrap-responsive.css" rel="stylesheet">
    <link href="css/admin.css" rel="stylesheet">
    
    
    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="../js/html5shiv.js"></script>
    <![endif]-->
  </head>
  <body>
    <?php include_once("admin/analyticstracking.php") ?>
    <div class="navbar navbar-inverse navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="brand appname" href="#">Enroll<img src='img/beta-icon.png' style="vertical-align:text-top;"/></a>
          <div class="nav-collapse collapse">
            <ul class="nav">
              <li><a href="xy.php">XY</a></li>
              <li class="active"><a href="colloquium.php">Colloquium</a></li>
            </ul>
            <ul class="nav pull-right">
              <?php if(!is_null($ghostuser)){ ?>
                <li><a href="javascript:void(0)" onclick='ghost_user("<?php echo $master_username; ?>","admin");'><?php echo $master_username; ?></a></li>
              <?php 
                }
                if(!$loggedin)
                  echo "<li><a href='login.html'>Login</a></li>";
                else
                  echo "<li><a href='logout.php'>Logout</a></li>";
              ?>
            </ul>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>
    <div class="container">
        <h1>Colloquium</h1>
        <hr />
      <div id="main" role="main">
        <!-- SHOW AGENDA IS USER IS LOGGED IN AND HAS ALREADY CHOSEN A COLLOQUIUM COURSE -->
        <?php if(isset($_SESSION['username']) && (!is_null($chosen_col1_name) || !is_null($chosen_col2_name))) { ?>
          <div id="agenda" class="container" style="height:275px;">
            <h2>Agenda</h2>
            <ul id="tiles">
              <?php if(isset($chosen_col1_name)) { ?>
                <div id="tile<?php echo $chosen_col1_id; ?>">
                  <form id='remove<?php echo $chosen_col1_id; ?>' >
                    <input name='type' type='hidden' value='colloquium' />
                    <input name='courseid' type='hidden' value='<?php echo $chosen_col1_id; ?>' />
                    <input name='username' type='hidden' value='<?php echo $_SESSION["username"]; ?>' />
                  </form>
                  <li>
                    <?php
                      if($_SESSION['student'] && $col1_register){
                    ?> 
                    <i id="<?php echo $chosen_col1_id; ?>" class="icon-remove-sign remove_assignment"></i>
                    <?php } ?>
                    <img class="img-rounded" src="img/courses/<?php echo $chosen_col1_image; ?>" width="200"  />
                    <p><?php echo $chosen_col1_name; ?></p>
                    <p>1st Semester</p>
                    <p>Room <?php echo $chosen_col1_room; ?></p>
                  </li>
                </div>
              <?php }
              if(isset($chosen_col2_name)) { ?>
                <div id="tile<?php echo $chosen_col2_id; ?>">
                  <form id='remove<?php echo $chosen_col2_id; ?>' >
                    <input name='type' type='hidden' value='colloquium' />
                    <input name='courseid' type='hidden' value='<?php echo $chosen_col2_id; ?>' />
                    <input name='username' type='hidden' value='<?php echo $_SESSION["username"]; ?>' />
                  </form>
                  <li>
                    <?php
                      if($_SESSION['student'] && $col2_register){
                    ?> 
                    <i id="<?php echo $chosen_col2_id; ?>" class="icon-remove-sign remove_assignment"></i>
                    <?php } ?>
                    <img class="img-rounded" src="img/courses/<?php echo $chosen_col2_image; ?>" width="200"  />
                    <p><?php echo $chosen_col2_name; ?></p>
                    <p>2nd Semester</p>
                    <p>Room <?php echo $chosen_col2_room; ?></p>
                  </li>
                </div>
              <?php } ?>
            </ul>
          </div>
        </div>
        <div class="container">
        <?php }
          if(isset($_SESSION['username']) && 
            (isset($chosen_col1_name) || isset($chosen_col2_name)) )
            echo "<h2>Choices</h2><hr />";
        ?>
        <div id="choices" class="container">

        <!-- FILTER BETWEEN SEMESTER 1 AND 2 COURSES-->

          <?php if(!isset($chosen_col1_name) && !isset($chosen_col2_name)){ ?>
          <ul id="filters" >
              <div><li data-filter="1"><h3>Semester 1</h3></li></div>
              <div><li data-filter="2"><h3>Semester 2</h3></li></div>
          </ul>
        <?php } ?>
        <ul id="tiles">
          <?php
            //Iterate through all of colloquium options
            while($row = mysql_fetch_array($result)){
              if(($row['freshmen'] && strcmp($class_level,'freshman')==0) ||
                 ($row['sophomore'] && strcmp($class_level,'sophomore')==0) ||
                 ($row['junior'] && strcmp($class_level,'junior')==0) ||
                 ($row['senior'] && strcmp($class_level,'senior')==0 ||
                  !$loggedin))
              {
                $cassnid = $row['cassnid'];
                $image = $row['image'];
                $name = $row['name'];
                $description = $row['description'];
                $lastname = $row['lastname'];
                $firstname = $row['firstname'];
                $duration = $row['duration'];
                $semester = $row['semester'];
                $class_size = $row['class_size'];
                $spots_left_result=mysql_query("SELECT COUNT(*) AS count FROM `c_enrollments` WHERE c_assignments_id=$cassnid") or die(mysql_error());
                $spots_left_array=mysql_fetch_array($spots_left_result);
                $spots_left=$class_size - $spots_left_array['count'];
                if($spots_left > 0 && 
                  ((is_null($chosen_col1_name) && strcmp($semester, "1") == 0) || 
                   (is_null($chosen_col2_name) && strcmp($semester, "2") == 0) ) )
                {

          ?>
                  <li class="<?php echo $semester; ?> card" value="<?php echo $cassnid; ?>"  >
                    <form id='enroll<?php echo $cassnid; ?>' >
                      <input name='type' type='hidden' value='colloquium' />
                      <input name='courseid' type='hidden' value='<?php echo $cassnid; ?>' />
                      <input name='username' type='hidden' value='<?php echo $_SESSION["username"]; ?>' />
                      <input name='class_size' type='hidden' value='<?php echo $class_size; ?>' />
                    </form>
                    <img class="img-rounded" src="img/courses/<?php echo $image; ?>" width="200"  />
                    <p><?php echo $name; ?></p>
                    <p><?php echo $firstname . " " . $lastname; ?></p>
                    <p>Semester <?php echo $semester; ?></p>
                    <?php 
                      if(isset($_SESSION['username'])) {
                        echo "<p>$spots_left Spots Left</p>";
                      } ?>
                    <p onClick="expand_description('<?php echo $cassnid; ?>')">
                      <?php echo substr($description, 0, 200); ?><span id="ellipses<?php echo $cassnid; ?>">...</span><span id="description<?php echo $cassnid; ?>" style="display: none;"><?php echo substr($description, 200); ?></span>
                    </p>
                    <div id='status<?php echo $cassnid; ?>'></div>
                    <?php
                      if($_SESSION['student'] && 
                         (($semester==1 && $col1_register) ||
                          ($semester==2 && $col2_register))) 
                        echo "<p><button class='btn' type='button' id='enrollbutton" . $cassnid . "' onClick='enroll(\"$cassnid\")' >Enroll</button></p>";
                    ?>  
                  </li>
          <?php
                }
              }
            }
            mysql_close();
          ?>
        </ul>
      </div>
    </div>
    </div>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/jquery.wookmark.min.js"></script>
    <script src="js/student.js"></script>
    <!-- Once the images are loaded, initalize the Wookmark plug-in. -->
    <script type="text/javascript">
        $(document).ready(new function() {
          // This filter is later used as the selector for which grid items to show.
          var filter = '', handler;

          // Prepare layout options.
          var options = {
            align: 'left',
            autoResize: true, // This will auto-update the layout when the browser window is resized.
            container: $('#main'), // Optional, used for some extra CSS styling
            offset: 15, // Optional, the distance between grid items
            itemWidth: 210 // Optional, the width of a grid item
          };

          // This function filters the grid when a change is made.
          var refresh = function() {
            // This hides all grid items ("inactive" is a CSS class that sets opacity to 0).
            $('#tiles li').addClass('inactive');

            // Create a new layout selector with our filter.
            handler = $(filter);

            // This shows the items we want visible.
            handler.removeClass("inactive");

            // This updates the layout.
            handler.wookmark(options);
          }

          /**
           * This function checks all filter options to see which ones are active.
           * If they have changed, it also calls a refresh (see above).
           */
          var updateFilters = function() {
            var oldFilter = filter,
                filters = [];

            // Collect filter list.
            var items = $('#filters li'),
                i = 0, length = items.length, item;

            for(; i < length; i++) {
              item = items.eq(i);
              if(item.hasClass('active')) {
                filters.push('#tiles li.' + item.attr('data-filter'));
              }
            }

            // If no filters active, set default to show all.
            if (filters.length == 0) {
              filters.push('#tiles li');
            }

            // Finalize our filter selector for jQuery.
            filter = filters.join(', ');

            // If the filter has changed, update the layout.
            if(oldFilter != filter) {
              refresh();
            }
          };

          /**
           * When a filter is clicked, toggle it's active state and refresh.
           */
          var onClickFilter = function(event) {
            $('#filters li').removeClass('active');
            $(event.currentTarget).toggleClass('active');
            updateFilters();
          }

          // Capture filter click events.
          $('#filters li').click(onClickFilter);

          // Do initial update (shows all items).
          updateFilters();
        });
      </script>
  </body>
</html>  
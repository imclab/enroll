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
  include_once 'admin/settings.php';
  //Connects to MySQL and Selects Database
  $con = mysql_connect($host,$db_username,$db_password);
  if (!$con)
    die('Could not connect: ' . mysql_error());
  //Select DB
  mysql_select_db($db, $con);
  //Get class levels
  $get_settings_result=mysql_query(
    "SELECT freshman,sophomore,junior,senior FROM settings LIMIT 1") or die(mysql_error());
  $get_settings_array=mysql_fetch_array($get_settings_result);
  $freshman=$get_settings_array['freshman'];
  $sophomore=$get_settings_array['sophomore'];
  $junior=$get_settings_array['junior'];
  $senior=$get_settings_array['senior'];
  //Get user's class level
  $class_level=null;
  $get_graduation_year_result=mysql_query(
      "SELECT graduation_year FROM users WHERE username='$username' LIMIT 1") or die(mysql_error());
  $get_graduation_year_array=mysql_fetch_array($get_graduation_year_result);
  $graduation_year=$get_graduation_year_array['graduation_year'];
  if($graduation_year==$freshman)
    $class_level="freshman";
  elseif($graduation_year==$sophomore)
    $class_level="sophomore";
  elseif($graduation_year==$junior)
    $class_level="junior";
  elseif($graduation_year==$senior)
    $class_level="senior";
  //Get next date for XY Courses
  $next_xy_result=mysql_query("SELECT id,date 
                               FROM dates 
                               WHERE date >= " .  date('Y-m-d') . " AND schedule=\"a\" 
                               ORDER BY date LIMIT 1") or die(mysql_error());
  $next_xy_row= mysql_fetch_array($next_xy_result);
  $next_xy=$next_xy_row['date'];
  $next_xy_id=$next_xy_row['id'];
  //Grab all of the teacher's xy options
  $query = "SELECT users.lastname, users.firstname, xy_assignments.id AS xyassnid, 
                   xy.name, xy.description, xy.image, xy_assignments.block, xy_assignments.class_size,
                   xy.freshmen,xy.sophomores,xy.juniors,xy.seniors 
            FROM `users` 
            INNER JOIN `xy_assignments` on xy_assignments.teacher_id = users.id
            INNER JOIN `xy` on xy_assignments.xy_id = xy.id 
            WHERE xy_assignments.final=1 AND xy_assignments.date_id=$next_xy_id";
  //Result of above query
  $result = mysql_query($query) or die(mysql_error());
  $chosen_xy_name=NULL;
  $chosen_xy_image=NULL;
  $chosen_xy_id=NULL;
  $chosen_xy_room=NULL;
  $chosen_x_name=NULL;
  $chosen_x_image=NULL;
  $chosen_x_id=NULL;
  $chosen_x_room=NULL;
  $chosen_y_name=NULL;
  $chosen_y_image=NULL;
  $chosen_y_id=NULL;
  $chosen_y_room=NULL;
  //If user is logged in
  if(isset($_SESSION['username'])){
    $chosen_xy_result=mysql_query("SELECT users.username, xy.name, xy.image, xy_assignments.block, xy_assignments.id, xy_assignments.room 
                                   FROM `users` 
                                   INNER JOIN `xy_enrollments` on users_id = users.id 
                                   INNER JOIN `xy_assignments` on xy_assignments.id = xy_enrollments.xy_assignments_id 
                                   INNER JOIN `xy` on xy_assignments.xy_id = xy.id 
                                   WHERE users.username=\"$username\"") or die(mysql_error());
    while($chosen_xy_row = mysql_fetch_array($chosen_xy_result)){
      if(strcmp($chosen_xy_row['block'],"xy") == 0){
        $chosen_xy_name=$chosen_xy_row['name'];
        $chosen_xy_image=$chosen_xy_row['image'];
        $chosen_xy_id=$chosen_xy_row['id'];
        $chosen_xy_room=$chosen_xy_row['room'];
      }
      elseif(strcmp($chosen_xy_row['block'],"x") == 0){
        $chosen_x_name=$chosen_xy_row['name'];
        $chosen_x_image=$chosen_xy_row['image'];
        $chosen_x_id=$chosen_xy_row['id'];
        $chosen_x_room=$chosen_xy_row['room'];
      }
      else{
        $chosen_y_name=$chosen_xy_row['name'];
        $chosen_y_image=$chosen_xy_row['image'];
        $chosen_y_id=$chosen_xy_row['id'];
        $chosen_y_room=$chosen_xy_row['room'];
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
          <a class="brand appname" href="#">Enroll</a>
          <div class="nav-collapse collapse">
            <ul class="nav">
              <li class="active"><a href="xy.php">XY</a></li>
              <li><a href="colloquium.php">Colloquium</a></li>
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
      <h1>XY for <?php echo date('l F jS, Y', strtotime($next_xy)); ?></h1>
      <hr />
      <!-- SHOW AGENDA IS USER IS LOGGED IN AND HAS ALREADY CHOSEN A X OR Y COURSE -->
      <?php if(isset($_SESSION['username']) && 
              (!is_null($chosen_xy_name) || 
               !is_null($chosen_x_name) || 
               !is_null($chosen_y_name))) 
            { ?>
                <div class="row" style="min-height: 450px;">
                <h2>Agenda</h2>
                <ul id="tiles">
                  <!-- IF USER HAS ALREADY CHOSEN AN XY -->
                  <?php 
                    if(isset($chosen_xy_name)) 
                    { ?>
                      <div id="tile<?php echo $chosen_xy_id; ?>">
                        <form id='remove<?php echo $chosen_xy_id; ?>' >
                          <input name='type' type='hidden' value='xy' />
                          <input name='courseid' type='hidden' value='<?php echo $chosen_xy_id; ?>' />
                          <input name='username' type='hidden' value='<?php echo $_SESSION["username"]; ?>' />
                        </form>
                        <li>
                          <i id="<?php echo $chosen_xy_id; ?>" class="icon-remove-sign remove_assignment"></i>
                          <img class="img-rounded" src="img/courses/<?php echo $chosen_xy_image; ?>" width="200"  />
                          <p><?php echo $chosen_xy_name; ?></p>
                          <p>Room <?php echo $chosen_xy_room; ?></p>
                          <p>Spans both X and Y blocks.</p>
                        </li>
                      </div>
              <?php }
                    //IF USER HAS ALREADY CHOSEN AN X
                    if(isset($chosen_x_name)) 
                    { ?>
                      <div id="tile<?php echo $chosen_x_id; ?>">
                        <form id='remove<?php echo $chosen_x_id; ?>' >
                          <input name='type' type='hidden' value='xy' />
                          <input name='courseid' type='hidden' value='<?php echo $chosen_x_id; ?>' />
                          <input name='username' type='hidden' value='<?php echo $_SESSION["username"]; ?>' />
                        </form>
                        <li>
                          <i id="<?php echo $chosen_x_id; ?>" class="icon-remove-sign remove_assignment"></i>
                          <img class="img-rounded" src="img/courses/<?php echo $chosen_x_image; ?>" width="200"  />
                          <p><?php echo $chosen_x_name; ?></p>
                          <p>Room <?php echo $chosen_x_room; ?></p>
                          <p>X Block</p>
                        </li>
                      </div>
              <?php }
                    //IF USER HAS ALREADY CHOSEN AN X
                    if(isset($chosen_y_name)) 
                    { ?>
                      <div id="tile<?php echo $chosen_y_id; ?>">
                        <form id='remove<?php echo $chosen_y_id; ?>' >
                          <input name='type' type='hidden' value='xy' />
                          <input name='courseid' type='hidden' value='<?php echo $chosen_y_id; ?>' />
                          <input name='username' type='hidden' value='<?php echo $_SESSION["username"]; ?>' />
                        </form>
                        <li>
                          <i id="<?php echo $chosen_y_id; ?>" class="icon-remove-sign remove_assignment"></i>
                          <img class="img-rounded" src="img/courses/<?php echo $chosen_y_image; ?>" width="200"  />
                          <p><?php echo $chosen_y_name; ?></p>
                          <p>Room <?php echo $chosen_y_room; ?></p>
                          <p>Y Block</p>
                        </li>
                      </div>
              <?php } ?>
                </ul>
            </div>
      <?php } ?>
    <div id='choices' class='row'>
    <?php
      if((is_null($chosen_xy_name) && is_null($chosen_x_name) && is_null($chosen_y_name)) ||
         (is_null($chosen_xy_name) && is_null($chosen_y_name)) || 
         (is_null($chosen_xy_name) && is_null($chosen_x_name)))
      {
          echo "<h2>Choices</h2><hr />";
    ?>
          <!-- FILTER BETWEEN X AND Y COURSES-->
          <?php if(!isset($chosen_x_name) && 
                   !isset($chosen_y_name))
                { ?>
                  <ul id="filters" >
                      <div><li data-filter="x"><h2>X Period</h2></li></div>
                      <div><li data-filter="y"><h2>Y Period</h2></li></div>
                  </ul>
          <?php } ?>
          <ul id="tiles">
            <?php
              while($row = mysql_fetch_array($result)){
                if(($row['freshmen'] && strcmp($class_level,'freshman')==0) ||
                   ($row['sophomore'] && strcmp($class_level,'sophomore')==0) ||
                   ($row['junior'] && strcmp($class_level,'junior')==0) ||
                   ($row['senior'] && strcmp($class_level,'senior')==0 ||
                    !$loggedin))
                {
                  $xyassnid = $row['xyassnid'];
                  $image = $row['image'];
                  $name = $row['name'];
                  $description = $row['description'];
                  $lastname = $row['lastname'];
                  $firstname = $row['firstname'];
                  $block = $row['block'];
                  $class_size = $row['class_size'];
                  $spots_left_result=mysql_query("SELECT COUNT(*) AS count FROM `xy_enrollments` WHERE xy_assignments_id=$xyassnid") or die(mysql_error());
                  $spots_left_array=mysql_fetch_array($spots_left_result);
                  $spots_left=$class_size - $spots_left_array['count'];
                  if($spots_left > 0 && 
                    ((is_null($chosen_x_name) && strcmp($block, "x") == 0 && is_null($chosen_xy_name)) || 
                     (is_null($chosen_y_name) && strcmp($block, "y") == 0 && is_null($chosen_xy_name)) ||
                     (is_null($chosen_y_name) && is_null($chosen_x_name) && strcmp($block, "xy") != 0)) )
                  {
              ?>
                    <li class="<?php if(strcmp($block, 'xy')==0){echo 'x y';}else{echo $block;} ?> card" value="<?php echo $xyassnid; ?>"  >
                      <form id='enroll<?php echo $xyassnid; ?>' >
                        <input name='type' type='hidden' value='xy' />
                        <input name='courseid' type='hidden' value='<?php echo $xyassnid; ?>' />
                        <input name='username' type='hidden' value='<?php echo $_SESSION["username"]; ?>' />
                        <input name='class_size' type='hidden' value='<?php echo $class_size; ?>' />
                      </form>
                      <img class="img-rounded" src="img/courses/<?php echo $image; ?>" width="200"  />
                      <p><?php echo $name; ?></p>
                      <p><?php echo $firstname . " " . $lastname; ?></p>
                      <?php
                        if(strcmp($block, 'xy')==0){
                          echo "<p>Spans both X and  Y blocks.</p>";
                        }
                        else{
                          echo "<p>" . strtoupper($block) . " Block</p>";
                        }
                      ?>
                      <?php 
                        if(isset($_SESSION['username'])) {
                          echo "<p>$spots_left Spots Left</p>";
                        } ?>
                      <p onClick="expand_description('<?php echo $xyassnid; ?>')">
                        <?php echo substr($description, 0, 200); ?><span id="ellipses<?php echo $xyassnid; ?>">...</span><span id="description<?php echo $xyassnid; ?>" style="display: none;"><?php echo substr($description, 200); ?></span>
                      </p>
                      <div id='status<?php echo $xyassnid; ?>'></div>
                      <?php
                        if($_SESSION['student']) 
                          echo "<p><button class='btn' type='button' id='enrollbutton" . $xyassnid . "' onClick='enroll(\"$xyassnid\")' >Enroll</button></p>";
                      ?>  
                    </li>
            <?php
                  }
                }
              }
              mysql_close();
            ?>
          </ul>
<?php } ?>
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
            container: $('#choices'), // Optional, used for some extra CSS styling
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
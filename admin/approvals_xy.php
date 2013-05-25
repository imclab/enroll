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
  $next_date=null;
  $next_date_id=null;
  $next_xy_result=mysql_query(
      "SELECT *
       FROM dates 
       WHERE date >= " .  date('Y-m-d') . " AND schedule='a' ORDER BY date LIMIT 1") or die(mysql_error());
  $next_xy_row= mysql_fetch_array($next_xy_result);
  $next_date=$next_xy_row['date'];
  $next_date_id=$next_xy_row['id'];
  $dates_result=mysql_query("SELECT * FROM dates WHERE schedule='a'") or die(mysql_error());
  $xy_assignments_result=mysql_query(
    "SELECT users.lastname, users.firstname, xy_assignments.date_id, xy_assignments.id, 
            xy_assignments.final, xy.name, xy_assignments.class_size, xy_assignments.room, 
            xy_assignments.preferred_block, xy_assignments.block, xy.preferred_class_size, xy.preferred_room, xy_assignments.notes 
     FROM `users` 
     INNER JOIN `xy_assignments` on xy_assignments.teacher_id=users.id 
     INNER JOIN `xy` on xy_assignments.xy_id=xy.id") or die(mysql_error());
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
          <a class="brand appname" href="#">Enroll</a>
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
            </li>
           </ul>
            <ul class="nav pull-right">
                <li>
                <form id="ghostuserform" class="navbar-form pull-right">
                  <input class="span2 search-query" name="username" type="text" placeholder="Login as..." />
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
      <div class="row">
        <div class="span3 bs-docs-sidebar">
          <ul class="nav nav-list bs-docs-sidenav">
            <?php
              //Iterate through the dates and create side navigation menu
              while($row=mysql_fetch_array($dates_result)){
                echo "<li";
                if(strcmp($row['date'], $next_date)==0)
                  echo " class='active' ";
                echo "><a href='#" . $row['id'] . "'><i class='icon-chevron-right'></i>" . date('D F jS, Y', strtotime($row['date'])) . "</a></li>";
              }
            ?>
          </ul>
      </div>
      <div class="span9">
        <?php
          //Iterate through the dates and create a section for each date that will house current assigned courses
          mysql_data_seek($dates_result,0);
          while($date=mysql_fetch_array($dates_result)){
            $seats_assigned=0;
        ?>
            <section id="<?php echo $date['id']; ?>">
              <div class='page-header'>
                <h1><?php echo date('F jS, Y', strtotime($date['date'])); ?></h1>
              </div>
              <table class="table table-striped">
                <thead>
                    <tr>
                      <th>Name</th>
                      <th>XY</th>
                      <th>Preferred Class Size</th>
                      <th>Class Size</th>
                      <th>Preferred Room</th>
                      <th>Room</th>
                      <th>Preferred Block</th>
                      <th>Block</th>
                      <th>Notes</th>
                      <th></th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                      //Iterate through xy assignments
                      mysql_data_seek($xy_assignments_result,0);
                      while ($row=mysql_fetch_array($xy_assignments_result)) {
                        if($date['id']==$row['date_id']){
                          if($row['final'])
                            $seats_assigned+=$row['class_size'];
                          echo "<tr>";
                          if(!$row['final']){
                            echo "<form action='finalize.php' method='post'>";
                          }
                          else{
                            echo "<form action='unfinalize.php' method='post'>";
                          }
                          echo "<input name='id' type='hidden' value='" . $row['id'] . "' />";
                          echo "<input name='type' type='hidden' value='xy' />";
                          echo "<td>" . $row['lastname'] . ", " . $row['firstname'] . "</td>";
                          echo "<td>" . $row['name'] . "</td>";
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
                          echo "<td>" . strtoupper($row['preferred_block']) . "</td>";
                          if(!$row['final']){
                            if(!is_null($row['block'])){ ?>
                              <td>
                                <select class='input-mini' name='block' required>
                                  <option value=''></option>
                                  <option <?php if(strcmp($row['block'],'x')==0) echo ' selected '; ?> value='x'>X</option>
                                  <option <?php if(strcmp($row['block'],'y')==0) echo ' selected '; ?> value='y'>Y</option>
                                  <option <?php if(strcmp($row['block'],'xy')==0) echo ' selected '; ?> value='xy'>XY</option>
                                </select>
                              </td>
                            <?php } else{ ?>
                            <td>
                              <select class='input-mini' name='block' required>
                                <option value=''></option>
                                <option <?php if(strcmp($row['preferred_block'],'x')==0) echo ' selected '; ?> value='x'>X</option>
                                <option <?php if(strcmp($row['preferred_block'],'y')==0) echo ' selected '; ?> value='y'>Y</option>
                                <option <?php if(strcmp($row['preferred_block'],'xy')==0) echo ' selected '; ?> value='xy'>XY</option>
                              </select>
                            </td>
                          <?php
                          }}
                          else{
                            echo "<td>" . strtoupper($row['block']) . "</td>";
                          }
                          echo "<td>";
                            if(strcmp($row['notes'],"")!=0)
                              echo "<a href='#'' title='" . $row['notes'] . "'>Note</a>";
                          echo "</td>";
                          if(!$row['final']){
                            echo "<td><button class='btn btn-medium btn-warning' type='submit'>Finalize</button></td>";
                          }
                          else{
                            echo "<td><button class='btn btn-medium btn-success' type='submit'>Unfinalize</button></td>";
                          }
                          echo "</form>";
                          echo "</tr>";
                        }
                      }
                    ?>
                    <p class="lead pull-right">
                      <?php echo $seats_assigned; ?> seats assigned.
                    </p>
                  </tbody>
              </table>
            </section>
        <?php
          }
        ?>
      </div>    
    </div>
  </div>
  </body>
</html>
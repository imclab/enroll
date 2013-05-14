<?php
session_start(); // initialize session
session_destroy(); // destroy session
setcookie("PHPSESSID","",time()-3600,"/"); // delete session cookie
header("Location: xy.php");
?>
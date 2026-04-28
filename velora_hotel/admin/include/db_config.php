<?php
$hname = "localhost";
$uname = "root";
$pass  = "";
$db    = "vlwebsite";
$con   = mysqli_connect($hname,$uname,$pass,$db);
if(!$con) die("Connection failed: ".mysqli_connect_error());
mysqli_set_charset($con,"utf8");
?>

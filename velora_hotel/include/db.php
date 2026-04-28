<?php
$con = mysqli_connect("localhost","root","","vlwebsite");
if(!$con) die("DB Error: ".mysqli_connect_error());
mysqli_set_charset($con,"utf8");
?>

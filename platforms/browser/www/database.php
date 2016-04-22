<?php
$con = mysqli_connect("localhost", "dadanina", "Parfemi2014"); 
if (!$con) {
    die('Could not connect: ' . mysqli_error());
}

mysqli_select_db($con,"dadanina");

?>
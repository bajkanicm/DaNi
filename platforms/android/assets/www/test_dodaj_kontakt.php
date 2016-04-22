<?php
include 'database.php';

$action = $_POST['action'];

if($action == 'addregid'){
	$regid = $_POST['regid'];
	$query = "DELETE FROM test_push WHERE regid like '".$regid."'";
	mysqli_query($con,$query) or die ($query); 
	$query = "INSERT INTO test_push (regid) VALUES ('".$regid."')";
	mysqli_query($con,$query) or die ($query); 
}elseif ($action = 'newcontact'){

$displayName	= $_POST['displayName'];
$nickname		= $_POST['nickname'];
$name			= $_POST['name'];
$phone1			= $_POST['phone1'];
$phone2			= $_POST['phone2'];
$birthday		= $_POST['birthday'];
$organizationName = $_POST['organizationName'];
$organizationTitle = $_POST['organizationTitle'];

$query = "INSERT INTO test_tabela (displayName,nickname,name,phone1,phone2,birthday,organizationName,organizationTitle) VALUES ('".$displayName."','".$nickname."','".$name."','".$phone1."','".$phone2."','".$birthday."','".$organizationName."','".$organizationTitle."')";
mysqli_query($con,$query) or die ($query); 
}
?>
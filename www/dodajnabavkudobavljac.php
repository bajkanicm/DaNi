<?php

include 'database.php';

		
$dobavljac	= $_POST['dobavljac'];


$datum			= date('Y-m-d H:i:s');


$query = "INSERT INTO nabavka (datum,iddobavljaca) VALUES ('".$datum."',".$dobavljac.")";
mysqli_query($con,$query) or die ($query); 
$idnabavke = mysqli_insert_id($con);
echo($idnabavke);



?>
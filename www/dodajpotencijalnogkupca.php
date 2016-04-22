<?php

include 'database.php';




if($_POST['id']>0){
	
 $id			= $_POST['id'];
 $opis			= $_POST['opis'];
 $telefon		= $_POST['telefon'];


 $query = "INSERT INTO korisnik_potencijalni (idkorisnika,opis,telefon) VALUES ('".$id."','".$opis."','".$telefon."')";
 mysqli_query($con,$query) or die ($query); 
}




?>
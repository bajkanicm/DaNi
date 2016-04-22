<?php

include 'database.php';



if($_POST['id']>0){
 $id			= $_POST['id'];
 $naziv			= $_POST['naziv'];
 $nalageru		= $_POST['nalageru'];


 $ime = mysql_escape_string($ime);
 $query = "UPDATE parfem SET naziv='".$naziv."',nalageru=".$nalageru." WHERE id=".$id;
  mysqli_query($con,$query) 
			or die ($query); 
}
elseif($_POST['naziv'])
{
 $naziv			= $_POST['naziv'];
 $nalageru		= $_POST['nalageru'];
 $vrsta			= $_POST['vrsta'];
 $tip			= $_POST['tip'];
 $query = "INSERT INTO parfem (naziv,nalageru,idvrsta,idtipa) VALUES ('".$naziv."',".$nalageru.",'".$vrsta."',".$tip.")";
 mysqli_query($con,$query)
			or die ($query); 
			//or die ($query);

}



?>
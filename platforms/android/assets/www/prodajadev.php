<?php

include 'databasedev.php';



if($_POST['idkorisnika']>0 && $_POST['idparfema']>0){

 $idkorisnika			= $_POST['idkorisnika'];
 $idparfema				= $_POST['idparfema'];
 $cena					= $_POST['cena'];
 $uplata				= $_POST['uplata'];
 $dug					= $_POST['dug'];
 $datum					= date('Y-m-d H:i:s');

 if($_POST['sledecauplata']=='') {$sledecauplata='1900-01-01 00:00:00';}  
 else {
	 $sledecauplata = $_POST['sledecauplata'];
	 $sledecauplata = str_replace('.', '-', $sledecauplata);
	 $sledecauplata =  date('Y-m-d', strtotime($sledecauplata));
	}
 $isporuceno			= $_POST['isporuceno'];

 $result = mysqli_query($con,"SELECT id  FROM porudzbina WHERE isporuceno IN (2,3) AND idkorisnika=".$idkorisnika ." AND idparfema=".$idparfema."  LIMIT 1 ");

 while($row = mysqli_fetch_array($result))
 {
	  $idporudzbine = $row['id'];
 }
 if($idporudzbine>0){
	 $query = "DELETE FROM porudzbina WHERE id=".$idporudzbine;
	 mysqli_query($con,$query) or die($query);
 }

 $query = "INSERT INTO porudzbina (idkorisnika,idparfema,datum,cena,isporuceno) VALUES ('".$idkorisnika."','".$idparfema."','".$datum."','".$cena."','".$isporuceno."')";
 mysqli_query($con,$query) or die($query);

 $idporudzbine = mysqli_insert_id($con);
 $query = "INSERT INTO korisnik_uplate (idkorisnika,datum,uplata,sledecauplata) VALUES ('".$idkorisnika."','".$datum."','".$uplata."','".$sledecauplata."')";
 mysqli_query($con,$query) or die($query);
 

 if($isporuceno==1){	
		$query = "UPDATE parfem SET nalageru=nalageru-1 WHERE id=".$idparfema;
		mysqli_query($con,$query) or die($query);
		
		$result = mysqli_query($con,"SELECT id  FROM porudzbina WHERE isporuceno=2 AND  idparfema=".$idparfema." ORDER BY datum DESC  LIMIT 1 ");

		while($row = mysqli_fetch_array($result))
		{
			  $idporudzbine = $row['id'];

			  $query = "UPDATE porudzbina SET isporuceno=3 WHERE id=".$idporudzbine;
		      mysqli_query($con,$query) or die($query);
		}

 }
			



 $data = array();
 $data[] = $idparfema;
 $data[] = $idkorisnika;
 echo json_encode($data);

 //header("Location: index.html");
 //exit;

}



?>
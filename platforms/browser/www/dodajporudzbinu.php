<?php

include 'database.php';



if($_POST['idkorisnika']>0 && $_POST['idparfema']>0){

 $idkorisnika			= $_POST['idkorisnika'];
 $idparfema				= $_POST['idparfema'];
 $cena					= $_POST['cena'];
 $datumporudzbine		= $_POST['datumporudzbine'];
 $datum					= date('Y-m-d H:i:s');

 $datumporudzbine = str_replace('.', '-', $datumporudzbine);
 $datumporudzbine =  date('Y-m-d', strtotime($datumporudzbine));
 $isporuceno			= 3; //hardcoded


 $query = "INSERT INTO porudzbina (idkorisnika,idparfema,datum,cena,isporuceno,datumporudzbine) VALUES ('".$idkorisnika."','".$idparfema."','".$datum."','".$cena."',".$isporuceno.",'".$datumporudzbine."')";

mysqli_query($con,$query) or die($query);


 $data = array();
 $data[] = $idparfema;
 $data[] = $idkorisnika;
 echo json_encode($data);

}



?>
<?php

include 'database.php';

$kolicina	= $_POST['kolicina'];
$cena		= $_POST['cena'];
$parfem		= $_POST['parfemi'];		

$kolicinaArray	= explode(",", $kolicina);
$cenaArray		= explode(",", $cena);
$parfemArray	= explode(",",$parfem);
$datum			= date('Y-m-d H:i:s');

$ukupnozauplatiti = 0;
 
$idnabavke = $_POST['nabavka'];
$brojParfema = count($parfemArray);
$p = 0;
while ($p<$brojParfema){
	
	if($kolicinaArray[$p] >0 or $cenaArray[$p] >0){
		$ukupnozauplatiti = $ukupnozauplatiti + $kolicinaArray[$p] * $cenaArray[$p];
		$query = "INSERT INTO nabavka_parfemi (idnabavke,idparfema,kolicina,cena) VALUES ('".$idnabavke."','".$parfemArray[$p]."','".$kolicinaArray[$p]."','".$cenaArray[$p]."')";
		mysqli_query($con,$query) or die ($query); 
		$query="UPDATE parfem P SET nalageru = nalageru + ".$kolicinaArray[$p]." WHERE P.id =".$parfemArray[$p];
		mysqli_query($con,$query) or die ($query);		
	}
	$p = $p + 1;
}
$query="UPDATE nabavka SET ukupnavrednost = ukupnavrednost + ".$ukupnozauplatiti." WHERE id =".$idnabavke;
mysqli_query($con,$query) or die ($query);	



?>
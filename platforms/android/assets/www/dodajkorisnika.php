<?php
include 'database.php';

if($_POST['id']>0){
 $id			= $_POST['id'];
 $ime			= $_POST['ime'];
 $mobilni		= $_POST['mobilni'];
 $fiksni		= $_POST['fiksni'];
 //$datumrodjenja	= $_POST['datumrodjenja'];
 $adresa		= $_POST['adresa'];
 $grad			= $_POST['grad'];
 $email			= $_POST['email'];
 //$pol			= $_POST['pol'];
 $pol = 'izbacili';
 $nadimak		= $_POST['nadimak'];
 $firma			= $_POST['firma'];
 $jmbg			= $_POST['jmbg'];
 $preporuka		= $_POST['preporuka'];
 $opis			= $_POST['opis'];
 $problematican	= $_POST['problematican'];
 $pocetnostanje = $_POST['pocetnostanje'];


 
 $query = "UPDATE korisnik SET ime='".$ime."',adresa='".$adresa."',grad='".$grad."',mobilni='".$mobilni."',fiksni='".$fiksni."',email='".$email."',pol='".$pol."',nadimak='".$nadimak."',firma='".$firma."',jmbg='".$jmbg."',preporuka='".$preporuka."',opis='".$opis."',problematican='".$problematican."',pocetnostanje=".$pocetnostanje." WHERE id=".$id;
  mysqli_query($con,$query); 
			//or die ($query); 
echo($id);
}
elseif($_POST['ime'])
{
 $ime			= $_POST['ime'];
 $mobilni		= $_POST['mobilni'];
 $fiksni		= $_POST['fiksni'];
 //$datumrodjenja	= $_POST['datumrodjenja'];
 $adresa		= $_POST['adresa'];
 $grad			= $_POST['grad'];
 $email			= $_POST['email'];
 //$pol			= $_POST['pol'];
 $pol = 'izbacili';
 $nadimak		= $_POST['nadimak'];
 $firma			= $_POST['firma'];
 $jmbg			= $_POST['jmbg'];
 $preporuka		= $_POST['preporuka'];
 $opis			= $_POST['opis'];
 $problematican	= $_POST['problematican'];
 $pocetnostanje = $_POST['pocetnostanje'];

 $query = "INSERT INTO korisnik (ime,adresa,grad,mobilni,fiksni,email,pol,nadimak,firma,jmbg,preporuka,opis,problematican,pocetnostanje) VALUES ('".$ime."','".$adresa."','".$grad."','".$mobilni."','".$fiksni."','".$email."','".$pol."','".$nadimak."','".$firma."','".$jmbg."','".$preporuka."','".$opis."','".$problematican."',".$pocetnostanje.")";
mysqli_query($con,$query);
$idkorisnika = mysqli_insert_id($con);
echo($idkorisnika);
			//or die ("5"); 
			//or die ($query);

}

mysqli_close($con);

?>
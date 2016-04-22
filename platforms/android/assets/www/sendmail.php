<?php
include 'database.php';
$to = "zorankoce@gmail.com,dadaparfem@gmail.com";
$subject = "DaNi Obavestenje";


$message = "Korisnici koji bi trebalo danas da plate (ili kasne sa uplatama):<br>----------------------------<br>";


/*$sql="SELECT K.id,ime, nadimak, firma, mobilni,DATE_FORMAT(sledecauplata,'%d-%m-%Y') AS sledecauplata,
((SELECT CASE WHEN SUM(cena)>0 THEN SUM(cena) ELSE 0 END AS cena FROM porudzbina WHERE porudzbina.idkorisnika= KU.idkorisnika) +
(SELECT pocetnostanje FROM korisnik WHERE korisnik.id = KU.idkorisnika) - 
(SELECT SUM(uplata) FROM korisnik_uplate WHERE korisnik_uplate.idkorisnika= KU.idkorisnika)) AS dug 
FROM korisnik_uplate KU
LEFT JOIN korisnik K ON K.id=KU.idkorisnika
WHERE (sledecauplata < CURDATE() OR sledecauplata = CURDATE()) AND sledecauplata>'1970-01-01 00:00:00'
GROUP BY id
ORDER BY sledecauplata";*//*izmena 15/05/2014*/
$sql="SELECT K.id,ime, nadimak, firma, mobilni,DATE_FORMAT(maxsledecauplata,'%d-%m-%Y') AS sledecauplata,
((SELECT CASE WHEN SUM(cena)>0 THEN SUM(cena) ELSE 0 END AS cena FROM porudzbina WHERE porudzbina.idkorisnika= KU.idkorisnika) +
(SELECT pocetnostanje FROM korisnik WHERE korisnik.id = KU.idkorisnika) - 
(SELECT SUM(uplata) FROM korisnik_uplate WHERE korisnik_uplate.idkorisnika= KU.idkorisnika)) AS dug 
FROM korisnik_uplate KU
LEFT JOIN korisnik K ON K.id=KU.idkorisnika
LEFT JOIN 
(SELECT id,sledecauplata AS maxsledecauplata, idkorisnika FROM korisnik_uplate KUPL WHERE 
id=(SELECT MAX(id) FROM korisnik_uplate WHERE korisnik_uplate.idkorisnika=KUPL.idkorisnika) GROUP BY idkorisnika) MSU
ON MSU.idkorisnika = KU.idkorisnika
WHERE  (MSU.maxsledecauplata < CURDATE() OR MSU.maxsledecauplata = CURDATE()) AND MSU.maxsledecauplata>'1970-01-01 00:00:00'
GROUP BY ID
ORDER BY MSU.maxsledecauplata";

$result = mysqli_query($con,$sql);

$data = array();
while($row = mysqli_fetch_array($result))
{
	
	$message .= "Ime: <b>" . $row['ime'] . "</b><br>";
	$message .= "Firma: <b>" . $row['nadimak'] . "</b><br>";
	$message .= "Nadimak: <b>" . $row['firma'] . "</b><br>";
	$message .= "Mobilni: <b>" . $row['mobilni'] . "</b><br>";
	$message .= "Datum uplate: <b>" . $row['sledecauplata'] . "</b><br>";
	$message .= "Dug: <b>" . $row['dug'] . "EUR</b><br>----------------------------<br>";
}
$from = "dadaandnina@bonuscashfree.com";
// Always set content-type when sending HTML email
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";

// More headers
$headers .= 'From: dadaandnina@bonuscashfree.com' . "\r\n";


mail($to,$subject,$message,$headers);
echo "Mail Sent.";
?>
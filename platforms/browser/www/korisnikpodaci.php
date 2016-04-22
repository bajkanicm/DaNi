<?php
include 'database.php';

if(isset($_GET['idkorisnikazabrisanje']) && !empty($_GET['idkorisnikazabrisanje'])){
	$sql = "UPDATE korisnik SET aktivan = 0 WHERE id= ".$_GET['idkorisnikazabrisanje'];
	$result = mysqli_query($con,$sql);
	header("Location: index.html");
	exit;
}

if(isset($_POST['action']) && !empty($_POST['action'])) {
    $action = $_POST['action'];
	
	
	
    switch($action) {
        case 'get_user_details' : 
			$id = $_POST['id'];
			$sql="SELECT * FROM korisnik WHERE id =".$id;

			$result = mysqli_query($con,$sql);

			while($row = mysqli_fetch_array($result))
			  {
			  echo(json_encode($row));
			  }
			break;
        case 'get_shopping_details' : 
			$idkorisnika = $_POST['idkorisnika'];
			$sql="SELECT 
					porudzbina.id,
					porudzbina.idkorisnika,
					naziv,
					datum as pravidatum,
					DATE_FORMAT(datum,'%d-%m-%Y') AS datum,
					CASE isporuceno
					WHEN 0 THEN 'NIJE ISPORUCENO'
					WHEN 2 THEN 'REZERVISANO'
					WHEN 3 THEN 'PORUDZBINA'
					WHEN 1 THEN 'PRODATO' END AS isporuceno,
					isporuceno as status,
					DATE_FORMAT(datumporudzbine,'%d-%m-%Y') AS datumporudzbine,
					cena,
					idparfema, 
					idtipa,
					nalageru
					FROM porudzbina
					LEFT JOIN parfem ON porudzbina.idparfema = parfem.id 
					WHERE idkorisnika=".$idkorisnika." AND isporuceno <4 ORDER BY pravidatum DESC";

			$result = mysqli_query($con,$sql);
			$data = array();

			while($row = mysqli_fetch_array($result))
			  {
				$data[] = $row;
			  }
			echo json_encode($data);
			break;
		case 'get_transaction_details' :
			$idkorisnika = $_POST['idkorisnika'];
			$sql="SELECT DATE_FORMAT(korisnik_uplate.datum,'%d-%m-%Y') AS datum,
					korisnik_uplate.uplata, korisnik_uplate.napomena
					FROM korisnik_uplate
					WHERE idkorisnika=".$idkorisnika." AND uplata>0 ORDER BY id DESC";
			
			$result = mysqli_query($con,$sql);
			$data = array();

			while($row = mysqli_fetch_array($result))
			  {
				$data[] = $row;
			  }
			echo json_encode($data);
			break;
		case 'get_total' :
			$idkorisnika = $_POST['idkorisnika'];
			$sql="SELECT 
					CASE 
					WHEN 
					(
					(SELECT pocetnostanje FROM korisnik WHERE korisnik.id=K.id)+
					(SELECT CASE WHEN SUM(cena)>0 THEN SUM(cena) ELSE 0 END FROM porudzbina WHERE porudzbina.idkorisnika= K.id AND porudzbina.isporuceno=1) - 
					(SELECT CASE WHEN SUM(uplata)>0 THEN SUM(uplata) ELSE 0 END FROM korisnik_uplate WHERE korisnik_uplate.idkorisnika= K.id)
					) IS NOT NULL
					THEN ((SELECT pocetnostanje FROM korisnik WHERE korisnik.id=K.id)+
					(SELECT CASE WHEN SUM(cena)>0 THEN SUM(cena) ELSE 0 END FROM porudzbina WHERE porudzbina.idkorisnika= K.id AND porudzbina.isporuceno=1) - 
					(SELECT CASE WHEN SUM(uplata)>0 THEN SUM(uplata) ELSE 0 END FROM korisnik_uplate WHERE korisnik_uplate.idkorisnika= K.id))
					ELSE (SELECT pocetnostanje FROM korisnik WHERE korisnik.id=K.id) 
					END
					AS total,
					CASE 
					WHEN (SELECT SUM(uplata) FROM korisnik_uplate WHERE korisnik_uplate.idkorisnika= K.id) > 0
					THEN (SELECT SUM(uplata) FROM korisnik_uplate WHERE korisnik_uplate.idkorisnika= K.id)
					ELSE 0 
					END
					AS uplata,
					(SELECT COUNT(*) FROM porudzbina WHERE porudzbina.idkorisnika= K.id AND isporuceno=1) AS brojparfema,
					(SELECT COUNT(*) FROM porudzbina WHERE porudzbina.idkorisnika= K.id AND isporuceno=2) AS rezervisano,
					(SELECT COUNT(*) FROM porudzbina WHERE porudzbina.idkorisnika= K.id AND isporuceno=3) AS naruceno,		
					(SELECT CASE WHEN sledecauplata>'2013-01-01' THEN DATE_FORMAT(sledecauplata,'%d-%m-%Y') ELSE 'Nema' END FROM korisnik_uplate WHERE korisnik_uplate.idkorisnika= K.id AND korisnik_uplate.id = (SELECT MAX(id) FROM korisnik_uplate WHERE korisnik_uplate.idkorisnika= K.id)) AS sledecauplata
					FROM korisnik K
					LEFT JOIN korisnik_uplate KU ON KU.idkorisnika = K.id
					WHERE K.id=".$idkorisnika." GROUP BY total";
			
			$result = mysqli_query($con,$sql);
			$data = array();

			while($row = mysqli_fetch_array($result))
			  {
				$data[] = $row;
			  }
			if(empty($data)){
				$sql="SELECT 
					CASE 
					WHEN ((SELECT pocetnostanje FROM korisnik WHERE korisnik.id=P.id) - 
					(SELECT SUM(uplata) FROM korisnik_uplate WHERE korisnik_uplate.idkorisnika= P.id)) > 0
					THEN ((SELECT pocetnostanje FROM korisnik WHERE korisnik.id=P.id) - 
					(SELECT SUM(uplata) FROM korisnik_uplate WHERE korisnik_uplate.idkorisnika= P.id))
					ELSE (SELECT pocetnostanje FROM korisnik WHERE korisnik.id=P.id) 
					END
					AS total,
					CASE 
					WHEN (SELECT SUM(uplata) FROM korisnik_uplate WHERE korisnik_uplate.idkorisnika= P.id) > 0
					THEN (SELECT SUM(uplata) FROM korisnik_uplate WHERE korisnik_uplate.idkorisnika= P.id)
					ELSE 0 
					END
					AS uplata,
					0 as brojparfema,
					0 as rezervisano,
					0 as naruceno,
					DATE_FORMAT((SELECT sledecauplata FROM korisnik_uplate WHERE korisnik_uplate.idkorisnika= P.id AND korisnik_uplate.id = (SELECT MAX(id) FROM korisnik_uplate WHERE korisnik_uplate.idkorisnika= P.id)),'%d-%m-%Y') AS sledecauplata
					FROM korisnik P
					LEFT JOIN korisnik_uplate KU ON KU.idkorisnika = P.id
					WHERE P.id=".$idkorisnika." GROUP BY total";
					$result = mysqli_query($con,$sql);
					$data = array();

					while($row = mysqli_fetch_array($result))
					  {
						$data[] = $row;
					  }
			}
			echo json_encode($data);
			break;
		case 'get_period_payments' :
			$idporudzbine = $_POST['idporudzbine'];
			$sql="SELECT 
					CASE 
					WHEN rata1>'1900-01-01' THEN DATE_FORMAT(rata1,'%d-%m-%Y')
					ELSE '' END as rata1,
					CASE 
					WHEN rata2>'1900-01-01' THEN DATE_FORMAT(rata2,'%d-%m-%Y')
					ELSE '' END as rata2,
					CASE 
					WHEN rata3>'1900-01-01' THEN DATE_FORMAT(rata3,'%d-%m-%Y')
					ELSE '' END as rata3,
					CASE 
					WHEN rata4>'1900-01-01' THEN DATE_FORMAT(rata4,'%d-%m-%Y')
					ELSE '' END as rata4,
					CASE 
					WHEN rata5>'1900-01-01' THEN DATE_FORMAT(rata5,'%d-%m-%Y')
					ELSE '' END as rata5,
					naziv
					FROM porudzbina
					LEFT JOIN parfem ON porudzbina.idparfema = parfem.id
					WHERE porudzbina.id=".$idporudzbine;
			
			$result = mysqli_query($con,$sql);
			$data = array();

			while($row = mysqli_fetch_array($result))
			  {
				$data[] = $row;
			  }
			echo json_encode($data);
			break;
		case 'add_transaction' :
			$idkorisnika = $_POST['idkorisnika'];
			$uplata = $_POST['novauplata'];
			$datum	= date('Y-m-d H:i:s');
			
			$sledecauplata = $_POST['sledecauplata'];
			$sledecauplata = str_replace('.', '-', $sledecauplata);
			$sledecauplata =  date('Y-m-d', strtotime($sledecauplata));


			$query = "INSERT INTO korisnik_uplate (idkorisnika,datum,uplata,sledecauplata) VALUES ('".$idkorisnika."','".$datum."','".$uplata."','".$sledecauplata."')";
			$result = mysqli_query($con,$query) or die($query);
					//or die ("5");
			$data = array();
			$data[] = $idkorisnika;
			echo json_encode($data);
			break;
		case 'get_all_users_charge' :
			
			/*$sql="SELECT K.id, K.ime, (((SELECT CASE WHEN SUM(cena) IS NULL THEN 0 AS SUM(cena) END FROM porudzbina WHERE idkorisnika = K.id and isporuceno=1) + (SELECT pocetnostanje FROM korisnik WHERE id=K.id) 
					- (SELECT CASE WHEN SUM(uplata) IS NULL THEN 0 ELSE SUM(uplata) END FROM korisnik_uplate WHERE idkorisnika = K.id))) as dugnovi,
					CASE WHEN (((SELECT SUM(cena) FROM porudzbina WHERE idkorisnika = K.id and isporuceno=1) + (SELECT pocetnostanje FROM korisnik WHERE id=K.id) 
					- (SELECT SUM(uplata) FROM korisnik_uplate WHERE idkorisnika = K.id))) IS NULL THEN 0
					ELSE (((SELECT SUM(cena) FROM porudzbina WHERE idkorisnika = K.id and isporuceno=1) + (SELECT pocetnostanje FROM korisnik WHERE id=K.id) 
					- (SELECT SUM(uplata) FROM korisnik_uplate WHERE idkorisnika = K.id)))
					END AS dug 
					FROM korisnik K
					WHERE K.problematican=1
					ORDER BY dug DESC
					";*/
			$sql = "SELECT K.id, K.ime, 
					(SELECT CASE WHEN SUM(cena) IS NULL THEN 0 ELSE SUM(cena) END FROM porudzbina WHERE idkorisnika = K.id and isporuceno=1) + 
					(SELECT pocetnostanje FROM korisnik WHERE id=K.id) -
					(SELECT CASE WHEN SUM(uplata) IS NULL THEN 0 ELSE SUM(uplata) END FROM korisnik_uplate WHERE idkorisnika = K.id)
					as dug
					FROM korisnik K
					WHERE K.problematican=1
					ORDER BY dug DESC";
			
			$result = mysqli_query($con,$sql);
			$data = array();

			while($row = mysqli_fetch_array($result))
			  {
				$data[] = $row;
			  }
			echo json_encode($data);
			break;
		case 'get_total_black_list' :
			
			$sql = "SELECT SUM((viewkorisnikstanje.pocetnostanje+viewkorisnikstanje.porudzbinastanje-viewkorisnikstanje.uplatastanje))
					AS total
					FROM viewkorisnikstanje
					LEFT JOIN korisnik ON viewkorisnikstanje.id=korisnik.id
					WHERE korisnik.problematican=1";
			
			$result = mysqli_query($con,$sql);
			$data = array();

			while($row = mysqli_fetch_array($result))
			  {
				$data[] = $row;
			  }
			echo json_encode($data);
			break;
        // ...etc...
    }
}

mysqli_close($con);

?>
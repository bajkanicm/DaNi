<?php
include 'database.php';

//$sql="SELECT * FROM korisnik";
if(isset($_GET['action'])  && !empty($_GET['action'])) {
    $action = $_GET['action'];
	$search = $_GET['search'];
	if($action == 'get_all_possible_users'){
		$sql = "SELECT ime,korisnik_potencijalni.opis, telefon FROM korisnik_potencijalni
				LEFT JOIN korisnik ON korisnik_potencijalni.idkorisnika = korisnik.id";


				$result = mysqli_query($con,$sql);

				$data = array();


				while($row = mysqli_fetch_array($result))
				  {
				  $data[] = $row;
				  }
				echo json_encode($data);
	}elseif($action == 'get_all_active_users'){
		$sql = "SELECT K.id,K.ime,K.firma,K.nadimak,K.problematican,
				(VKS.pocetnostanje+VKS.porudzbinastanje-VKS.uplatastanje) AS dug 
				FROM porudzbina P
				LEFT JOIN korisnik K ON P.idkorisnika=K.id
				LEFT JOIN viewkorisnikstanje VKS ON VKS.id = K.id
				WHERE P.isporuceno IN (1,2,3) AND K.aktivan=1 AND  (K.ime like '%".$search."%' OR K.firma like '%".$search."%' OR K.nadimak like '%".$search."%')
				GROUP BY P.idkorisnika
				ORDER BY K.ime";
				
				$result = mysqli_query($con,$sql);

				$data = array();


				while($row = mysqli_fetch_array($result))
				  {
				  $data[] = $row;
				  }
				echo json_encode($data);
	}
	elseif($action == 'get_all_red_users'){
		$sql = "SELECT KU.idkorisnika, K.ime, K.firma, K.nadimak, 
				DATE_FORMAT(MAX(sledecauplata),'%d-%m-%Y')  AS uplata,
				DATEDIFF(CURDATE(),MAX(sledecauplata)) AS kasni,
				((SELECT pocetnostanje FROM korisnik WHERE id=K.id) +
				(SELECT CASE WHEN SUM(cena)>0 THEN SUM(cena) ELSE 0 END FROM porudzbina WHERE idkorisnika = K.id AND isporuceno=1) 
				- (SELECT CASE WHEN SUM(uplata)>0 THEN SUM(uplata) ELSE 0 END FROM korisnik_uplate WHERE idkorisnika = K.id)) AS dug 
				FROM korisnik_uplate KU
				LEFT JOIN korisnik K ON K.id = KU.idkorisnika
				WHERE KU.id IN (SELECT MAX(id) FROM korisnik_uplate WHERE idkorisnika = KU.idkorisnika)
				AND KU.sledecauplata<=CURDATE() AND KU.sledecauplata>'2013-01-01'
				GROUP BY KU.idkorisnika
				ORDER BY uplata ASC";


				$result = mysqli_query($con,$sql) or die($sql);

				$data = array();


				while($row = mysqli_fetch_array($result))
				  {
				  $data[] = $row;
				  }
				echo json_encode($data);
	}
	elseif($action == 'get_all_order_parfume_users'){
		$sql = "SELECT 
			idkorisnika,ime,nadimak,firma,nalageru,datum,
			DATE_FORMAT(datumporudzbine,'%d-%m-%Y') AS datumporudzbine,
			naziv
			FROM porudzbina
			LEFT JOIN korisnik ON korisnik.id = porudzbina.idkorisnika
			LEFT JOIN parfem ON parfem.id = porudzbina.idparfema
			WHERE isporuceno =3
			ORDER BY ime";


				$result = mysqli_query($con,$sql) or die($sql);

				$data = array();


				while($row = mysqli_fetch_array($result))
				  {
				  $data[] = $row;
				  }
				echo json_encode($data);
	}
	else{
		/*$sql = "SELECT id,ime,firma,nadimak,
				(SELECT COUNT(*) FROM porudzbina WHERE idkorisnika=korisnik.id AND isporuceno=0) AS porudzbina,
				(SELECT COUNT(*) FROM porudzbina WHERE idkorisnika=korisnik.id AND isporuceno=2) AS rezervisano,
				(SELECT COUNT(*) FROM porudzbina WHERE idkorisnika=korisnik.id AND isporuceno=1) AS isporuceno
				FROM korisnik
				ORDER BY porudzbina DESC";*/
		
		
		$sql = "SELECT K.id,K.ime,K.firma,K.nadimak,K.problematican,
				(VKS.pocetnostanje+VKS.porudzbinastanje-VKS.uplatastanje) AS dug 
				FROM korisnik K
				LEFT JOIN viewkorisnikstanje VKS ON VKS.id = K.id
				WHERE  K.aktivan=1 AND (K.ime like '%".$search."%' OR K.firma like '%".$search."%' OR K.nadimak like '%".$search."%')
				ORDER BY K.ime";
		//var_dump($sql);

				$result = mysqli_query($con,$sql);

				$data = array();


				while($row = mysqli_fetch_array($result))
				  {
				  $data[] = $row;
				  }
				echo json_encode($data);
	}
}






mysqli_close($con);

?>
<?php
include 'database.php';


if(isset($_POST['action']) && !empty($_POST['action'])) {
    $action = $_POST['action'];
    switch($action) {
        case 'get_parfume_details' :
			$id = $_POST['id'];
			$sql="SELECT * FROM parfem WHERE id =".$id;

			$result = mysqli_query($con,$sql);

			while($row = mysqli_fetch_array($result))
			  {
			  echo(json_encode($row));
			  }
			break;
		case 'get_price_history' :
			$id = $_POST['id'];
			$sql="SELECT DATE_FORMAT(datum,'%d-%m-%Y') AS datum,datum AS datumsort, cena, naziv 
					FROM nabavka_parfemi 
					LEFT JOIN nabavka ON nabavka.id = nabavka_parfemi.idnabavke 
					LEFT JOIN dobavljac ON dobavljac.id = nabavka.iddobavljaca
					WHERE idparfema=".$id."
					ORDER BY datumsort DESC";

			$result = mysqli_query($con,$sql);

			$data = array();
			while($row = mysqli_fetch_array($result))
			  {
			  $data[] = $row;
			  }
			   echo(json_encode($data));
			break;
		 case 'get_parfume_type' :
			
			$sql="SELECT * FROM parfem_vrsta ";

			$result = mysqli_query($con,$sql);

			$data = array();
			while($row = mysqli_fetch_array($result))
			  {
			  $data[] = $row;
			  }
			   echo(json_encode($data));
			break;
		case 'get_parfume_category' :
			
			$sql="SELECT * FROM parfem_tip ";

			$result = mysqli_query($con,$sql);

			$data = array();
			while($row = mysqli_fetch_array($result))
			  {
			  $data[] = $row;
			  }
			   echo(json_encode($data));
			break;
		 case 'get_company' :
			
			$sql="SELECT * FROM dobavljac ";

			$result = mysqli_query($con,$sql);

			$data = array();
			while($row = mysqli_fetch_array($result))
			  {
			  $data[] = $row;
			  }
			   echo(json_encode($data));
			break;
		case 'get_parfume_sum' :
			$sql="SELECT SUM(nalageru) as ukupno_parfema FROM parfem";

			$result = mysqli_query($con,$sql);
			$data = array();
			while($row = mysqli_fetch_array($result))
			  {
			  $data[] = $row;
			  }
			   echo(json_encode($data));
			break;
		case 'get_parfume_sold' :
			$oddatuma = $_POST['oddatuma'];
		    $oddatuma = str_replace('.', '-', $oddatuma);
			$oddatuma = $oddatuma . ' 00:00:00';
			$oddatuma =  date('Y-m-d H:i:s', strtotime($oddatuma));
			$dodatuma = $_POST['dodatuma'];
			$dodatuma = str_replace('.', '-', $dodatuma);
			$dodatuma = $dodatuma . ' 23:59:59';
			$dodatuma =  date('Y-m-d H:i:s', strtotime($dodatuma));

			$sql="SELECT COUNT(idparfema) as brojparfema,'".$oddatuma."' as oddatuma,'".$dodatuma."' as dodatuma FROM porudzbina WHERE isporuceno=1 AND datum BETWEEN '".$oddatuma."' AND '".$dodatuma."'";
			//die($sql);
			
			$result = mysqli_query($con,$sql) or die($sql);
			$data = array();
			while($row = mysqli_fetch_array($result))
			  {
			  $data[] = $row;
			  }
			   echo(json_encode($data));
			break;
        case 'get_all_parfumes' :
			$sql="SELECT id, naziv, nalageru, 
					(SELECT COUNT(*) FROM porudzbina WHERE idparfema = parfem.id AND isporuceno=0) AS porudzbina,
					(SELECT COUNT(*) FROM porudzbina WHERE idparfema = parfem.id AND isporuceno=1) AS isporuceno
					FROM parfem
					ORDER BY porudzbina DESC";

			$result = mysqli_query($con,$sql);
			$data = array();
			while($row = mysqli_fetch_array($result))
			  {
			  $data[] = $row;
			  }
			   echo(json_encode($data));
			break;
		 case 'get_all_parfumes_per_type_nabavka' :
			 $id = $_POST['id'];

			$sql="SELECT id, naziv, nalageru, 
					(SELECT COUNT(*) FROM porudzbina WHERE idparfema = parfem.id AND isporuceno=0) AS porudzbina
					FROM parfem
					WHERE idvrsta = ".$id."
					ORDER BY porudzbina DESC";

			$result = mysqli_query($con,$sql);
			$data = array();
			while($row = mysqli_fetch_array($result))
			  {
			  $data[] = $row;
			  }
			   echo(json_encode($data));
			break;
		case 'get_all_parfumes_per_order' :
			/*$sql="SELECT idparfema, DATE_FORMAT(datum,'%d-%m-%Y') AS datum,
				CASE isporuceno
				WHEN 0 THEN 'Nije isporuceno'
				ELSE 'Rezervisano'
				END  AS status_porudzbine,
				parfem.naziv,
				korisnik.ime,korisnik.firma,korisnik.nadimak
				FROM porudzbina
				LEFT JOIN parfem ON porudzbina.idparfema = parfem.id
				LEFT JOIN korisnik ON porudzbina.idkorisnika = korisnik.id
				WHERE porudzbina.isporuceno IN (0,2) ORDER BY DATUM ASC";*/
			$sql = "SELECT P.id, P.idparfema, DATE_FORMAT(P.datum,'%d-%m-%Y') AS datum,
				DATE_FORMAT(P.datumporudzbine,'%d-%m-%Y') AS datumporudzbine,
				PR.naziv,
				K.ime,K.firma,K.nadimak,PR.idtipa,
				(PR.nalageru - (SELECT COUNT(*) FROM porudzbina WHERE isporuceno=2 AND idparfema = P.idparfema))
				AS slobodno
				FROM porudzbina P
				LEFT JOIN parfem PR ON P.idparfema = PR.id
				LEFT JOIN korisnik K ON P.idkorisnika = K.id
				WHERE P.isporuceno=3 ORDER BY naziv ASC";

			$result = mysqli_query($con,$sql);
			$data = array();
			while($row = mysqli_fetch_array($result))
			  {
			  $data[] = $row;
			  }
			   echo(json_encode($data));
			break;
		case 'get_all_parfumes_reserved' :
			$sql = "SELECT P.id, P.idparfema,P.idkorisnika, DATE_FORMAT(P.datum,'%d-%m-%Y') AS datum,
				DATE_FORMAT(P.datumporudzbine,'%d-%m-%Y') AS datumporudzbine,
				PR.naziv,
				K.ime,K.firma,K.nadimak,
				PR.nalageru
				AS slobodno
				FROM porudzbina P
				LEFT JOIN parfem PR ON P.idparfema = PR.id
				LEFT JOIN korisnik K ON P.idkorisnika = K.id
				WHERE P.isporuceno=2 ORDER BY naziv ASC";

			$result = mysqli_query($con,$sql);
			$data = array();
			while($row = mysqli_fetch_array($result))
			  {
			  $data[] = $row;
			  }
			   echo(json_encode($data));
			break;
		 case 'get_all_parfumes_per_type' :
			$id = $_POST['id'];
		    $tip = $_POST['tip'];
			$sql="SELECT id, naziv, nalageru, 
					(SELECT COUNT(*) FROM porudzbina WHERE idparfema = parfem.id AND isporuceno=1) AS isporuceno,
					(SELECT COUNT(*) FROM porudzbina WHERE idparfema = parfem.id AND isporuceno=2) AS rezervisano,
					(SELECT COUNT(*) FROM porudzbina WHERE idparfema = parfem.id AND isporuceno=3) AS porudzbina,
					(nalageru - (SELECT COUNT(*) FROM porudzbina WHERE idparfema = parfem.id AND isporuceno=2)) AS slobodno
					FROM parfem
					WHERE idvrsta = ".$id." AND idtipa=".$tip." 
					 ORDER BY nalageru DESC, porudzbina DESC";
			//die($sql);
			$result = mysqli_query($con,$sql);
			$data = array();
			while($row = mysqli_fetch_array($result))
			  {
			  $data[] = $row;
			  }
			   echo(json_encode($data));
			break;
		case 'get_all_parfumes_active' :
			
			$sql="SELECT id, naziv, nalageru, 
				(SELECT COUNT(*) FROM porudzbina WHERE idparfema = parfem.id AND isporuceno=1) AS isporuceno,
				(SELECT COUNT(*) FROM porudzbina WHERE idparfema = parfem.id AND isporuceno=2) AS rezervisano,
				(SELECT COUNT(*) FROM porudzbina WHERE idparfema = parfem.id AND isporuceno=3) AS porudzbina,
				(nalageru - (SELECT COUNT(*) FROM porudzbina WHERE idparfema = parfem.id AND isporuceno=2)) AS slobodno
				FROM parfem
				WHERE nalageru>0 OR id IN (SELECT idparfema FROM porudzbina WHERE isporuceno IN (2,3))
				ORDER BY  porudzbina DESC,rezervisano DESC, nalageru DESC";
			//die($sql);
			$result = mysqli_query($con,$sql);
			$data = array();
			while($row = mysqli_fetch_array($result))
			  {
			  $data[] = $row;
			  }
			   echo(json_encode($data));
			break;
		case 'get_all_parfumes_order' :
			$id = $_POST['id'];
			$sql="SELECT * FROM nabavka_parfemi
					LEFT JOIN parfem ON nabavka_parfemi.idparfema = parfem.id
					WHERE idnabavke= ".$id;

			$result = mysqli_query($con,$sql);
			$data = array();
			while($row = mysqli_fetch_array($result))
			  {
			  $data[] = $row;
			  }
			   echo(json_encode($data));
			break;
		case 'get_all_parfumes_sold' :
			$oddatuma = $_POST['oddatuma'];
		    $oddatuma = str_replace('.', '-', $oddatuma);
			$oddatuma = $oddatuma . ' 00:00:00';
			$oddatuma =  date('Y-m-d H:i:s', strtotime($oddatuma));
			$dodatuma = $_POST['dodatuma'];
			$dodatuma = str_replace('.', '-', $dodatuma);
			$dodatuma = $dodatuma . ' 23:59:59';
			$dodatuma =  date('Y-m-d H:i:s', strtotime($dodatuma));
		
			$sql="SELECT naziv,ime,nadimak,firma,datum as datumsort,DATE_FORMAT(datum,'%d-%m-%Y') AS datum,idtipa FROM porudzbina
					LEFT JOIN parfem ON parfem.id = porudzbina.idparfema 
					LEFT JOIN korisnik ON korisnik.id = porudzbina.idkorisnika
					WHERE isporuceno=1 AND datum BETWEEN '".$oddatuma."' AND '".$dodatuma."'
					ORDER BY datumsort DESC";
			//die($sql);	
			$result = mysqli_query($con,$sql) or die($sql);;
			$data = array();
			while($row = mysqli_fetch_array($result))
			  {
			  $data[] = $row;
			  }
			   echo(json_encode($data));
			break;
		case 'get_all_parfumes_lager' :
			$sql="SELECT naziv,nalageru FROM parfem where nalageru>0 ORDER BY naziv ASC";
			//die($sql);	
			$result = mysqli_query($con,$sql) or die($sql);;
			$data = array();
			while($row = mysqli_fetch_array($result))
			  {
			  $data[] = $row;
			  }
			   echo(json_encode($data));
			break;
		 case 'get_all_acquisitions' :
			$sql="SELECT 
					N.id,
					DATE_FORMAT(datum,'%d-%m-%Y') AS datum,
					DATEDIFF(CURDATE(),datum) AS predana,
					COALESCE((SELECT SUM(kolicina) FROM nabavka_parfemi WHERE idnabavke = N.id),0) AS brojparfema,
					COALESCE((SELECT SUM(kolicina*cena) FROM nabavka_parfemi WHERE idnabavke = N.id),0) AS total,
					D.naziv
					FROM nabavka N
					LEFT JOIN dobavljac D ON N.iddobavljaca = D.id
					ORDER BY N.id DESC
					";

			$result = mysqli_query($con,$sql);
			$data = array();
			while($row = mysqli_fetch_array($result))
			  {
			  $data[] = $row;
			  }
			   echo(json_encode($data));
			break;
		case 'get_total_credit' :
			/*$sql="SELECT SUM(ukupnavrednost) AS ukupno,N.iddobavljaca,D.naziv,
				(SELECT COALESCE(SUM(uplata),0)
				FROM nabavka_uplata 
				LEFT JOIN dobavljac ON dobavljac.id = nabavka_uplata.iddobavljaca
				WHERE dobavljac.id = N.iddobavljaca
				)  AS uplata,
				(SUM(ukupnavrednost) - (SELECT COALESCE(SUM(uplata),0)
				FROM nabavka_uplata 
				LEFT JOIN dobavljac ON dobavljac.id = nabavka_uplata.iddobavljaca
				WHERE dobavljac.id = N.iddobavljaca
				)) AS dug
				FROM nabavka N
				LEFT JOIN dobavljac D ON D.id = N.iddobavljaca
				GROUP BY N.iddobavljaca";*/

				$sql = "SELECT ukupnavrednost AS ukupno,VDS.iddobavljaca,naziv,ukupnauplata AS uplata,
ukupnavrednost-ukupnauplata AS dug FROM viewdobavljacstanje VDS
LEFT JOIN viewdobavljacuplate VDU ON VDS.iddobavljaca = VDU.iddobavljaca";

			$result = mysqli_query($con,$sql);
			$data = array();
			while($row = mysqli_fetch_array($result))
			  {
			  $data[] = $row;
			  }
			   echo(json_encode($data));
			break;
		 case 'get_all_acquisitions_parfumes' :
			 $id = $_POST['id'];
			$sql="SELECT idparfema,naziv,cena,SUM(kolicina) as kolicina
					FROM nabavka_parfemi
					LEFT JOIN parfem ON parfem.id=nabavka_parfemi.idparfema
					WHERE idnabavke= ".$id. " GROUP BY idparfema";

			$result = mysqli_query($con,$sql);
			$data = array();
			while($row = mysqli_fetch_array($result))
			  {
			  $data[] = $row;
			  }
			   echo(json_encode($data));
			break;
		case 'get_all_acquisitions_parfumes_total' :
			$id = $_POST['id'];
			$sql="SELECT COALESCE(SUM(kolicina),0)  as kolicina,COALESCE(SUM(kolicina*cena),0) AS total
					FROM nabavka_parfemi
					LEFT JOIN parfem ON parfem.id=nabavka_parfemi.idparfema
					WHERE idnabavke= ".$id;

			$result = mysqli_query($con,$sql);
			$data = array();
			while($row = mysqli_fetch_array($result))
			  {
			  $data[] = $row;
			  }
			   echo(json_encode($data));
			break;
		case 'get_all_transactions_order' :
			 $id = $_POST['id'];
			$sql="SELECT datum as datumsort, DATE_FORMAT(datum,'%d-%m-%Y') AS datum,uplata
					FROM nabavka_uplata
					WHERE iddobavljaca= ".$id." ORDER by datumsort DESC";

			$result = mysqli_query($con,$sql);
			$data = array();
			while($row = mysqli_fetch_array($result))
			  {
			  $data[] = $row;
			  }
			   echo(json_encode($data));
			break;
		case 'realised' :
			$id = $_POST['id'];
			$kolicina	= $_POST['kolicina'];
			$parfem		= $_POST['parfemi'];

			$kolicinaArray	= explode(",", $kolicina);
			$parfemArray	= explode(",",$parfem);

			$sql="UPDATE nabavka SET primljenaroba=1 WHERE id=".$id;
			$result = mysqli_query($con,$sql);

			$brojParfema = count($parfemArray);
			$p = 0;
			while ($p<$brojParfema){
				
				if($kolicinaArray[$p] >0){
					$sql="UPDATE parfem P SET nalageru = nalageru + ".$kolicinaArray[$p]." WHERE P.id =".$parfemArray[$p];
					mysqli_query($con,$sql) or die ($sql);
					$sql = "UPDATE nabavka_parfemi SET kolicina = ".$kolicinaArray[$p]." WHERE idparfema =".$parfemArray[$p]." AND idnabavke=".$id;
					mysqli_query($con,$sql) or die ($sql); 
					
				}
				$p = $p + 1;
			}
			
			
			break;
		case 'reserve' :
			$id = $_POST['id'];
			$sql="UPDATE porudzbina SET isporuceno=2 WHERE id=".$id;
			$result = mysqli_query($con,$sql);
			echo($_POST['idkorisnika']);
			break;
		case 'back_parfume' :
			$id = $_POST['id'];
			$idparfema = $_POST['idparfema'];
			$iznos = $_POST['iznos'];
			$iznos = -1 * $iznos;
			$sql="UPDATE parfem SET nalageru=nalageru + 1 WHERE id=".$idparfema;
			$result = mysqli_query($con,$sql) or die($sql);
			$sql="UPDATE porudzbina SET isporuceno=4 WHERE id=".$id;
			$result = mysqli_query($con,$sql) or die($sql);
			$sql="INSERT INTO korisnik_uplate(idkorisnika, uplata, datum, napomena) VALUES (".$_POST['idkorisnika'].",".$iznos.",CURDATE(),'VRACENE PARE KORISNIKU!')";
			$result = mysqli_query($con,$sql) or die($sql);
			echo($_POST['idkorisnika']);
			break;
		case 'realisedaction' :
			$id = $_POST['id'];
			$sql="UPDATE porudzbina SET isporuceno=1 WHERE id=".$id;
			$result = mysqli_query($con,$sql) or die($sql);
			$sql="UPDATE parfem P SET nalageru = nalageru - 1 WHERE P.id IN (SELECT idparfema FROM porudzbina WHERE id=".$id.")";
			$result = mysqli_query($con,$sql) or die($sql);
			echo($_POST['idkorisnika']);
			break;
		case 'cancelorder' :
			$id = $_POST['id'];
			$sql="DELETE FROM porudzbina WHERE  id=".$id;
			$result = mysqli_query($con,$sql) or die($sql);
			echo($_POST['idkorisnika']);
			break;
		case 'deleteparfume' :
			$id = $_POST['id'];
			$idparfema = $_POST['idparfema'];
			$sql="UPDATE parfem SET nalageru = (nalageru  - (SELECT kolicina FROM nabavka_parfemi WHERE  idnabavke=".$id." AND idparfema=".$idparfema.")) WHERE id=".$idparfema;
			$result = mysqli_query($con,$sql) or die($sql);
			$sql="DELETE FROM nabavka_parfemi WHERE  idnabavke=".$id." AND idparfema=".$idparfema;
			$result = mysqli_query($con,$sql) or die($sql);
			
			echo($_POST['id']);
			break;
		case 'get_current_total' :
			$sql="SELECT 
					COALESCE((SELECT SUM(uplata) FROM nabavka_uplata),0)  AS nabavka,
					COALESCE((SELECT SUM(vrednost) FROM troskovi),0) AS troskovi,
					COALESCE((SELECT SUM(uplata) FROM korisnik_uplate),0) AS uplata,
					COALESCE((SELECT SUM(pocetnostanje+porudzbinastanje-uplatastanje) FROM viewkorisnikstanje),0) AS dug,
					DATE_FORMAT(CURDATE(),'%d-%m-%Y') AS datum,
					COALESCE((SELECT SUM(nalageru*cena) FROM parfem LEFT JOIN viewposlednjecene ON viewposlednjecene.idparfema=parfem.id WHERE nalageru>0),0) AS iznoslagera,
					COALESCE((SELECT SUM(porudzbina.cena-viewposlednjecene.cena) FROM porudzbina LEFT JOIN viewposlednjecene ON viewposlednjecene.idparfema=porudzbina.idparfema WHERE isporuceno=1),0)
					AS ostvarenprofit
					";

			$result = mysqli_query($con,$sql);
			$data = array();
			while($row = mysqli_fetch_array($result))
			  {
			  $data[] = $row;
			  }
			   echo(json_encode($data));
			break;
		case 'get_all_user_order_parfume' :
			$id = $_POST['idparfema'];
			$sql="SELECT ime, DATE_FORMAT(CASE WHEN datumporudzbine < '2000-01-01' THEN datum ELSE datumporudzbine END,'%d-%m-%Y') AS datum FROM porudzbina 
					LEFT JOIN korisnik ON korisnik.id = porudzbina.idkorisnika
					WHERE isporuceno=3 AND idparfema = ".$id;

			$result = mysqli_query($con,$sql) or die($sql);
			$data = array();
			while($row = mysqli_fetch_array($result))
			  {
			  $data[] = $row;
			  }
			echo(json_encode($data));
			break;
		case 'add_transaction_order' :
			$id = $_POST['id'];
			$uplata = $_POST['uplata'];
			$sql="INSERT INTO nabavka_uplata(uplata,datum,iddobavljaca) VALUES (".$uplata.",CURDATE(),".$id.")";

			$result = mysqli_query($con,$sql) or die($sql);
			
			break;
        // ...etc...
    }
}

mysqli_close($con);

?>
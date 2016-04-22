<?php
include 'database.php';


if(isset($_POST['action']) && !empty($_POST['action'])) {
    $action = $_POST['action'];
    switch($action) {
		case 'add_new_expense' :
			$vrsta = $_POST['vrsta'];
			$vrednost = $_POST['vrednost'];
			 $datum = $_POST['datum'];
			 $datum = str_replace('.', '-', $datum);
			 $datum =  date('Y-m-d', strtotime($datum));
			 $opis = $_POST['opis'];
			
			$sql="INSERT INTO troskovi (idvrste, vrednost, datum, opis) VALUES ( ".$vrsta.",".$vrednost.",'".$datum."','".$opis."')";
			
			$result = mysqli_query($con,$sql) or die($sql);

			
			break;
        case 'get_expense_type' :
			$sql="SELECT * FROM troskovi_vrsta ";

			$result = mysqli_query($con,$sql);

			$data = array();
			while($row = mysqli_fetch_array($result))
			  {
			  $data[] = $row;
			  }
			   echo(json_encode($data));
			break;
		 case 'get_specific_expense' :
			$oddatuma = $_POST['oddatuma'];
			$oddatuma = str_replace('.', '-', $oddatuma);
			$oddatuma =  date('Y-m-d', strtotime($oddatuma));
			$dodatuma = $_POST['dodatuma'];
			$dodatuma = str_replace('.', '-', $dodatuma);
			$dodatuma =  date('Y-m-d', strtotime($dodatuma));
			$id = $_POST['id'];
			$sql="SELECT DATE_FORMAT(datum,'%d-%m-%Y') as datumTroska, vrednost, id FROM troskovi WHERE idvrste=".$id." AND datum BETWEEN '".$oddatuma."' AND '".$dodatuma."' ORDER BY datum";

			$result = mysqli_query($con,$sql);

			$data = array();
			while($row = mysqli_fetch_array($result))
			  {
			  $data[] = $row;
			  }
			   echo(json_encode($data));
			break;
		case 'delete_specific_expense' :
			
			$obrisati = $_POST['obrisati'];
			$sql="DELETE FROM troskovi WHERE id=".$obrisati;

			$result = mysqli_query($con,$sql);

			
			break;
		case 'get_specific_expense_total' :
			$oddatuma = $_POST['oddatuma'];
			$oddatuma = str_replace('.', '-', $oddatuma);
			$oddatuma =  date('Y-m-d', strtotime($oddatuma));
			$dodatuma = $_POST['dodatuma'];
			$dodatuma = str_replace('.', '-', $dodatuma);
			$dodatuma =  date('Y-m-d', strtotime($dodatuma));
			$id = $_POST['id'];
			$sql="SELECT COALESCE(SUM(vrednost),0) as total FROM troskovi WHERE idvrste=".$id." AND datum BETWEEN '".$oddatuma."' AND '".$dodatuma."'";

			$result = mysqli_query($con,$sql);

			$data = array();
			while($row = mysqli_fetch_array($result))
			  {
			  $data[] = $row;
			  }
			   echo(json_encode($data));
			break;
		case 'get_daily_payments' :
			$datum = $_POST['datum'];
			$sql = "SELECT ime, uplata, napomena FROM korisnik_uplate
					LEFT JOIN korisnik ON korisnik_uplate.idkorisnika = korisnik.id
					WHERE DATE_FORMAT(datum,'%d-%m-%Y') ='".$datum."' AND uplata>0";
			
			
			$result = mysqli_query($con,$sql) or die($sql);

			$data = array();
			while($row = mysqli_fetch_array($result))
			  {
			  $data[] = $row;
			  }
			   echo(json_encode($data));
			break;
		case 'get_daily_expenses' :
			$datum = $_POST['datum'];
			$sql = "SELECT vrednost, opis, naziv FROM troskovi LEFT JOIN troskovi_vrsta ON troskovi_vrsta.id=troskovi.idvrste
					WHERE DATE_FORMAT(datum,'%d-%m-%Y') ='".$datum."' AND vrednost>0";
			
			
			$result = mysqli_query($con,$sql) or die($sql);

			$data = array();
			while($row = mysqli_fetch_array($result))
			  {
			  $data[] = $row;
			  }
			   echo(json_encode($data));
			break;
		case 'get_daily_details' :
			$sql="SELECT 
					(SELECT SUM(vrednost) FROM troskovi WHERE datum = T.datum) AS troskovi_ukupno,
					(SELECT CASE WHEN SUM(uplata)>0 THEN SUM(uplata) ELSE 0 END AS uplata FROM korisnik_uplate WHERE DATE_FORMAT(datum,'%d-%m-%Y') = DATE_FORMAT(T.datum,'%d-%m-%Y')) AS prihodi,
					DATE_FORMAT(datum,'%d-%m-%Y') AS datum
					FROM troskovi T
					GROUP BY datum
					ORDER BY T.datum DESC
					";

			$result = mysqli_query($con,$sql);

			$data = array();
			while($row = mysqli_fetch_array($result))
			  {
			  $data[] = $row;
			  }
			   echo(json_encode($data));
			break;
			
		
        // ...etc...
    }
}

mysqli_close($con);

?>
<?php
include 'database.php';

if ($_FILES[csv][size] > 0) { 

    //get the csv file 
    $file = $_FILES[csv][tmp_name]; 
    $handle = fopen($file,"r"); 
     
    //loop through the csv file and insert into database 
    do { 
        if ($data[0]) { 
            $query ="INSERT INTO korisnik (ime, adresa, grad, mobilni, fiksni, email, pol) VALUES 
                ( 
                    '".addslashes($data[0])."', 
                    '".addslashes($data[1])."',
					'".addslashes($data[2])."',
					'".addslashes($data[3])."',
					'".addslashes($data[4])."',
					'".addslashes($data[5])."',
                    '".addslashes($data[6])."' 
                ) 
            ";
			//die($query);
			 mysqli_query($con,$query);
        } 
    } while ($data = fgetcsv($handle,1000,",","'")); 
    // 

    //redirect 
   header('Location: listakupaca.html'); die; 

}

if ($_FILES[csvvrsta][size] > 0) { 

    //get the csv file 
    $file = $_FILES[csvvrsta][tmp_name]; 
    $handle = fopen($file,"r"); 
     
    //loop through the csv file and insert into database 
    do { 
        if ($data[0]) { 
            $query ="INSERT INTO parfem_vrsta (vrsta) VALUES 
                ( 
                    '".addslashes($data[0])."'
                ) 
            ";
			//die($query);
			 mysqli_query($con,$query);
        } 
    } while ($data = fgetcsv($handle,1000,",","'")); 
    // 

    //redirect 
   header('Location: listavrstaparfema.html'); die; 

}

if ($_FILES[csvlager][size] > 0) { 

    //get the csv file 
    $file = $_FILES[csvlager][tmp_name]; 
    $handle = fopen($file,"r"); 
     
    //loop through the csv file and insert into database 
    do { 
        if ($data[0]) { 
            $query ="INSERT INTO nabavka_parfemi (idnabavke,idparfema,kolicina,cena) VALUES 
                ( 28,
                    '".addslashes($data[0])."',
					'".addslashes($data[2])."',
					'".addslashes($data[3])."'
                ) 
            ";
			//die($query);
			 mysqli_query($con,$query);
        } 
    } while ($data = fgetcsv($handle,1000,",","'")); 
    // 

    //redirect 
   header('Location: listavrstaparfema.html'); die; 

}

if ($_FILES[csvparfemi][size] > 0) { 

    //get the csv file 
    $file = $_FILES[csvparfemi][tmp_name]; 
    $handle = fopen($file,"r"); 
     
    //loop through the csv file and insert into database 
    do { 
        if ($data[0]) { 
            $query ="INSERT INTO parfem (idtipa, idvrsta, nalageru, naziv) VALUES 
                ( 
                    '".addslashes($data[0])."', 
                    '".addslashes($data[1])."',
					'".addslashes($data[2])."',
					'".addslashes($data[3])."' 
                ) 
            ";
			//die($query);
			 mysqli_query($con,$query);
        } 
    } while ($data = fgetcsv($handle,1000,",","'")); 
    // 

    //redirect 
   header('Location: listavrstaparfema.html'); die; 

}


mysqli_close($con);

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head> 
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" /> 
<title>Import a CSV File with PHP & MySQL</title> 
</head> 

<body> 

<?php if (!empty($_GET[success])) { echo "<b>Your file has been imported.</b><br><br>"; } //generic success notice ?> 




<form action="" method="post" enctype="multipart/form-data" name="form1" id="form1"> 
  Izaberi fajl za listu korisnika: <br /> 
  <input name="csv" type="file" id="csv" /> <br>
   Izaberi fajl za listu vrsta parfema: <br /> 
  <input name="csvvrsta" type="file" id="csvvrsta" />
  <br>
  Izaberi fajl za listu parfema: <br /> 
  <input name="csvparfemi" type="file" id="csvparfemi" />
  <br>
  Izaberi fajl za korekciju lagera: <br /> 
  <input name="csvlager" type="file" id="csvlager" />
  <br>
  <input type="submit" name="Submit" value="Ubaci" /> 
</form> 

</body> 
</html> 
<?php
include 'database.php';

//$sql="SELECT * FROM parfem";

$sql = "SELECT id, naziv, nalageru, 
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
echo json_encode($data);


mysqli_close($con);

?>
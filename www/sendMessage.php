<?php
$yourKey = 'AIzaSyD8v2_8YcGaX3lWKMM3VEjiPviuje-rSlo';
$yourId = '1009226900402';

define("GOOGLE_API_KEY", "AIzaSyD0a3tAESJbTRF7HbwUPP5CuMEyG24d8Iw");
define("GOOGLE_GCM_URL", "https://android.googleapis.com/gcm/send");
function sendMessageToPhone($deviceToken, $yourId, $messageText, $yourKey)  
{  
	
    $headers = array('Authorization:key=' . $yourKey);  
    $data = array(
		'sender_id' => $yourId, 
        'registration_id' => $deviceToken,   
        'data.message' => $messageText);
	
  
    $ch = curl_init();  
  
    curl_setopt($ch, CURLOPT_URL, "https://android.googleapis.com/gcm/send");  
    if ($headers)  
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);  
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
    curl_setopt($ch, CURLOPT_POST, true);  
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);  
  
    $response = curl_exec($ch);  
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);  
    if (curl_errno($ch)) {  
        //request failed  		
        return false;//probably you want to return false  
    }  
    if ($httpCode != 200) {  
        //request failed  
        return false;//probably you want to return false  
    }  
    curl_close($ch);
	echo($response);
    return $response;  
}

function send_gcm_notify($reg_id, $message) {

    $fields = array(
        'registration_ids'  => array( $reg_id ),
        'data'              => array( "message" => $message ),
    );

    $headers = array(
        'Authorization: key=' . GOOGLE_API_KEY,
        'Content-Type: application/json'
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, GOOGLE_GCM_URL);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
	

    $result = curl_exec($ch);
	
    if ($result === FALSE) {
        die('Problem occurred: ' . curl_error($ch));
    }
	
    curl_close($ch);
    echo $result;
 }



/*sendMessageToPhone('APA91bE6Yx5X12SD1scTQP3ZhqRIaj_AXVLm_jQWvHPe-lEIqKri2SlUUNwTxqQ4fU2PSfV82255PwPww6s78g8Mulp05gOgLtjnjLUoA51sQFwY09XZxg5jYXfc_zieeIrhUEprYdCqH1xFFG7d83ItWvCWzI1dbw',
$yourId,'Test Push Notification',$yourKey);*/

include 'database.php';

$result = mysqli_query($con,"SELECT * FROM test_push");

while($row = mysqli_fetch_array($result))
 {
	  $msg = 'Software Developer: Zoran Kocevski!';
	  $reg_id = $row['regid'];
	  send_gcm_notify($reg_id, $msg);
  
 }
 

mysqli_close($con);



//$reg_id = "APA91bFKP4007aXxoRoGs_UWI_4SotNdaR2tiUqu8kjHhS0fAJLBmmAARnzRJHGyDlzmzPlSXD9JVBlVtSM4QZWm6yRY_8iDuq7DHuME3glP1V15s1x0cnft4eQhcpXlr2fku9KYajbtO-QduDGq6F-46hRng2ovKg";
//send_gcm_notify($reg_id, $msg);





?>
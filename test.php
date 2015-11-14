<?php
include "include/config.php";
include "include/function.php";
$phone = "1347-613-1428,1347-613-1428";
$cname = "Luis";
$mmessage = "testing the Wap Push";
echo $mmessage."<br/>";
$result = sendSMS($phone,$mmessage);
if($result)
	echo "SUCCESS: Information Saved and Text Message Sent";
else
	echo $result;
/*$message = "from Luis, something to test";
$to="3476131428";
$formatted_number = $to."@txt.att.net";
$headers  = 'MIME-Version: 1.0'."\r\n";
$headers .='Content-type: text/html; charset=iso-8859-1' . "\r\n";
$headers .="From: FamilyEnergy Recuiter System<no-reply@yourfamilyenergy.com>\r\n"."X-Mailer: PHP/".phpversion();
if($result = mail("$formatted_number", "SMS", "$message",$headers))
	echo "worked";
else
	echo "not";
	*/
include "include/unconfig.php";
?>
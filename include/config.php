<?Php
/*$vmhost = 'p3nldg50mysql89.secureserver.net';
$vmuser = 'familymap';
$vmpass = 'Me15141514';
$vmdb = "familymap";*/
$vmhost = 'familyenergymark.db.9963422.hostedresource.com';
$vmuser = 'familyenergymark';
$vmpass = 'Family1514!';
$vmdb = "familyenergymark";
$vmconn = mysql_connect($vmhost, $vmuser, $vmpass);
$vmconndb = mysql_select_db($vmdb,$vmconn);
/*if(!$vmconn || !$vmconndb)
{
	$vmresult = array("bad","I'm sorry, we were unanble to conect you with out system, we apologize for any incovinence we cause.<br/><br/>Please come back later at a later time until our technician fix this issue.");
	$_SESSION["vmstatus"]=$vmresult;
	header("location:status.php");
	exit;
}*/
if(!$vmconn || !$vmconndb)
{
	$vmresult = array("bad","I'm sorry, we were unanble to conect you with out system, we apologize for any incovinence we cause.<br/><br/>Please come back later at a later time until our technician fix this issue.");
	$_SESSION["vmstatus"]=$vmresult;
	header("location:status.php");
	exit;
}
?>
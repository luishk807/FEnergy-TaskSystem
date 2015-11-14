<?Php
session_start();
unset($_SESSION["taskuser"]);
$_SESSION["loginresult"]="Logout Successfull";
header('location:index.php');
exit;
?>
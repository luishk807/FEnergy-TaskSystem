<?php
session_start();
include "include/config.php";
include "include/function.php";
if(empty($_SERVER['HTTP_REFERER']))
{
	$_SESSION["loginresult"]="Illegal entry detected";
	header("location:index.php");
	exit;
}
$uname = trim($_REQUEST["uname"]);
$uname = strtolower($uname);
$upass = trim($_REQUEST["upass"]);
$query = "select * from task_users where email = '".clean($uname)."' and password ='".md5(clean($upass))."'";
if($result = mysql_query($query))
{
	if(($num_rows = mysql_num_rows($result))>0)
	{
		$row = mysql_fetch_assoc($result);
		if($row["status"]=="2" || $row["status"]=="3")
		{
			$_SESSION["loginresult"]="Your Account is currently either blocked or cancelled";
			unset($_SESSION["taskuser"]);
			header("location:index.php");
			exit;
		}
		$user = array("id"=>$row["id"], "name"=>stripslashes($row["name"]),"username"=>stripslashes($row["username"]),"password"=>stripslashes($row["password"]),"email"=>stripslashes($row["email"]),'title'=>$row["title"],"status"=>$row["status"],"type"=>$row["type"],"phone"=>stripslashes($row["phone"]));
		adminstatus($row["status"]);
		$querys = "update task_users set last_checkin=DATE_ADD(NOW(), INTERVAL 2 HOUR) where id='".$row["id"]."'";
		@mysql_query($querys);
		$_SESSION["taskuser"]=$user;
		header("location:http://www.familyenergyportal.com/cms/home.php");
		exit;
	}
	else
	{
		$_SESSION["loginresult"]="Invalid Email And Password";
		header("location:http://www.familyenergyportal.com/cms");
		exit;
	}
}
else
{
	$_SESSION["loginresult"]="System is unable to check your email and password, please try again later";
	unset($_SESSION["taskuser"]);
	header("location:http://www.familyenergyportal.com/cms");;
	exit;
}
include "include/unconfig.php";
?>
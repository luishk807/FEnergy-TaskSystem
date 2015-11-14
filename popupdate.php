<?php
session_start();
include "include/config.php";
include "include/function.php";
if(isset($_SESSION["taskuser"]))
{
	$check1 = false;
	$taskview = $_SESSION["taskuser"];
	$queryview= "select * from task_update where view_update='no' and userid='".$taskview["id"]."'";
	if($resultview = mysql_query($queryview))
	{
		if(($num_rows = mysql_num_rows($resultview))>0)
			$check1=true;
	}
	if($check1)
	{
		if($popview !="close")
			echo "<div id='newicon' style='position:absolute; top:160px;left:320px'><img src='images/new.png' border='0' alt='New Message' /></div>";
	}
}
include "include/unconfig.php";
?>
<?php
session_start();
include "include/config.php";
include "include/function.php";
adminlogin();
$host = getHost();
$user = $_SESSION["taskuser"];
$list_emailx=trim(base64_decode($_REQUEST["le"])); //list of emails in "|" format
$id=base64_decode($_REQUEST["id"]); //the id to be sent with the target path
$task=base64_decode($_REQUEST["task"]); //the task
$ft=base64_decode($_REQUEST["ft"]); //the username from
$info = $_SESSION["info"];
$title = $info["title"];
$report = $info["report"];
if(empty($list_emailx) || empty($task) || empty($ft))
{
	$_SESSION["taskresult"]="ERROR:Illegal Entry";
	header("location:home.php");
	exit;
}
if(!pView($user["type"]))
	$height = "style='height:500px'";
else
	$height = "";
$list_email = explode(" | ",$list_emailx);
if(sizeof($list_email)<1)
{
	$_SESSION["taskresult"]="ERROR:Missing Entries For Access";
	header("location:home.php");
	exit;
}
$newlist="";
for($i=0;$i<sizeof($list_email);$i++)
{
	if(empty($newlist))
		$newlist="'".$list_email[$i]."'";
	else
		$newlist .=",'".$list_email[$i]."'";
}
function sendMain($value)
{
	header('location:'.$value);
	exit;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link rel="icon" type="image/png" href="images/favicon.ico">
<![if IE]>
<link rel="stylesheet" type="text/css" href="css/styleie.css" />
<![endif]>
<link rel="stylesheet" type="text/css" href="css/style.css" />
<!—[if lt IE 7]>
  <link rel="stylesheet" type="text/css" href="ie6.css" />
  <link rel="stylesheet" href="css/ie7.css">  
<![endif]—>
<script type="text/javascript" language="javascript" src="js/script.js"></script>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Welcome To Family Energy Task Manager System</title>
</head>
<body onload="preload('new.png,fonts/scrible.tff')">
<div id="main_cont">
	<?php
		include "include/header.php";
	?>
    <div id="body_middle">
        <div id="body_content_home">
           <div style="text-align:center">
           <div id="messageresult">
           	 <?php
                    if(isset($_SESSION["taskresult"]))
                    {
                        echo $_SESSION["taskresult"]."<br/>";
                    }
                 ?>
           </div>
        	Hello <u><b><?php echo $user["username"]; ?></b></u>, System is sending updates to the following users, please wait.
            <br/><br/>
           <!-- <a href=''><img src="images/hchat.jpg" alt="chat" border="0" /></a>-->
           <div style="height:700px; overflow:auto">
           <?Php
		   	   $prevlink="";
			   $query ="select * from task_users where id in (".$newlist.")";
			   if($result = mysql_query($query))
			   {
				   if(($num_rows = mysql_query($query))>0)
				   {
					   $count=0;
					   while($rows = mysql_fetch_array($result))
					   {
						   if($task=="create")
						   {
							    $prevlink = "createtask.php";
							    $title = "New Group Task Created From  ".$ft;
								$message = "Hello,<br/><br/>";
								$message .="This is to let know that a new group task has been created by ".$ft.".<br/>";
								$message .="To view the new task please login to Family Energy Task System by clicking the link below.<br/><br/><br/>";
								$message .="<a href='".$host."xfor.php?i=".base64_encode($rows["id"])."&t=".base64_encode('ind')."' target='_blank'>Login Here</a><br/><br/>Attn,<br/><br/>Family Energy Team<br/>";
						   }
						   else if($task=="sreport")
						   {
							    $prevlink ="taskin.php?id=".base64_encode($id);
							    $title = "Family Energy Task Manager: Report Update ".stripslashes($info["title"])."!";
								$message = "Hello,<br/><br/>";
								$message .="A new report has been updated for task: <b>".$info["title"]."</b> by ".$ft."!<br/><br/>";
								$message .="<fieldset><legend>Message:</legend>".nl2br($info["report"])."</fieldset><br/><br/>";
								$message .="To view the update please login to Family Energy Task System just click the link below and the given username and password.<br/>";
								$message .="<a href='".$host."xfor.php?i=".base64_encode($rows["id"])."&t=".base64_encode('ind')."' target='_blank'>Login Here</a><br/><br/>Attn,<br/><br/>Family Energy Team<br/>";
						   }
						    else if($task=="sreports")
						   {
							    $prevlink ="taskin_report.php?id=".base64_encode($id);
							    $title = "Family Energy Task Manager: Report Update ".stripslashes($info["title"])."!";
								$message = "Hello,<br/><br/>";
								$message .="A new report has been updated for task: <b>".$info["title"]."</b> by ".$ft."!<br/><br/>";
								$message .="<fieldset><legend>Message:</legend>".nl2br($info["report"])."</fieldset><br/><br/>";
								$message .="To view the update please login to Family Energy Task System just click the link below and the given username and password.<br/>";
								$message .="<a href='".$host."xfor.php?i=".base64_encode($rows["id"])."&t=".base64_encode('ind')."' target='_blank'>Login Here</a><br/><br/>Attn,<br/><br/>Family Energy Team<br/>";
						   }
						    else
						   {
							    $prevlink ="view.php";
							    $title = "Family Energy Task Manager: Report Update ".stripslashes($info["title"])."!";
								$message = "Hello,<br/><br/>";
								$message .="Task: <b>".$info["title"]."</b> has been recently updated by ".$ft."!<br/><br/>";
								$message .="To view the update please login to Family Energy Task System just click the link below and the given username and password.<br/>";
								$message .="<a href='".$host."xfor.php?i=".base64_encode($rows["id"])."&t=".base64_encode('ind')."' target='_blank'>Login Here</a><br/><br/>Attn,<br/><br/>Family Energy Team<br/>";
						   }
						 	echo "Sending: ".stripslashes($rows["email"])."..";
						   if($results=sendEmail(stripslashes($rows["email"]),$title,$message))
						   {
								echo "..Sent!<br/>";
								$count++;
								if($count >6)
								{
									$count=0;
									echo "Overheating...wait...<br/>";
									sleep(10);
								}
						   }
						   else
								echo ".. Failed!<br/>";
							$count++;
							flush();
					   }
					  sendMain($prevlink);
				   }
			   }
		   ?>
           </div>
            <br/><br/>
           </div>
        </div>
    </div>
    <div id="body_footer"></div>
	<div class="clearfooter"></div>
</div>
<?php
include "include/footer.php";
?>
</body>
</html>
<?php
include "include/unconfig.php";
?>
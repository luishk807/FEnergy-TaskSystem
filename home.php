<?php
session_start();
include "include/config.php";
include "include/function.php";
adminlogin();
$user = $_SESSION["taskuser"];
if($user["type"] !='1' && $user["type"] !='2')
	$height = "style='height:500px'";
else
	$height = "";
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
<body onload="preload('new.png,fonts/scrible.tff'),getlink()">
<div id="main_cont">
	<?php
		include "include/header.php";
	?>
    <div id="body_middle">
        <div id="body_content_home" <?php echo $height; ?>>
           <div style="text-align:center">
           <div id="messageresult">
           	 <?php
                    if(isset($_SESSION["taskresult"]))
                    {
                        echo $_SESSION["taskresult"]."<br/>";
                        unset($_SESSION["taskresult"]);
                    }
                 ?>
           </div>
        	Hello <u><b><?php echo $user["username"]; ?></b></u>, what would you like to do today?
            <br/><br/>
            <a href='createtask.php'><img src="images/hassign.jpg" border="0" alt="assign task" /></a>
            <br/><br/>
            <a href='view.php'><img src="images/hview.jpg"  border="0" alt="view task" /></a>
            <br/><br/>
            <?php
			if($user["type"]=='1' || $user["type"]=='2')
			{
				?>
            <a href='create.php'><img src="images/hcreate.jpg" border="0" alt="create users" /></a>
            <br/><br/>
            <a href='viewuser.php'><img src="images/husers.jpg" alt="edit users" border="0" /></a>
            <br/><br/>
            <?php
			}
			?>
           <!-- <a href=''><img src="images/hchat.jpg" alt="chat" border="0" /></a>-->
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
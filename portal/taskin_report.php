<?php
session_start();
include "include/config.php";
include "include/function.php";
adminlogin();
$user = $_SESSION["taskuser"];
$popview="close";
$id = base64_decode($_REQUEST["id"]);
if(empty($id))
{
	$_SESSION["taskresult"]="ERROR: Invalid Entry, choose a valid task";
	header("location:view.php");
	exit;
}
else
{
	$query = "select * from task where id='".$id."'";
	if($result = mysql_query($query))
	{
		if(($num_rows= mysql_num_rows($result))>0)
		{
			$taskinfo = mysql_fetch_assoc($result);
			$usersender=true;
			//check if user is valid to view this task
			if(!pView($user["type"]))
			{
				if($taskinfo["group_task"] !="yes")
				{
					if($taskinfo["sender"] != $user["id"] && $taskinfo["receiver"] != $user["id"])
					{
						$_SESSION["taskresult"]="ERROR: Invalid Entry";
						header("location:home.php");
						exit;
					}
				}
				else
				{
					$foundx=false;
					$queryx = "select * from task_group where task='".$taskinfo["id"]."' and userid='".$user["id"]."'";
					if($resultx = mysql_query($queryx))
					{
						if(($num_rowxs = mysql_num_rows($resultx))>0)
							$foundx=true;
					}
					if(!$foundx)
					{
						$_SESSION["taskresult"]="ERROR: Invalid Entry";
						header("location:home.php");
						exit;
					}
				}
			}
			if($taskinfo["group_task"] !="yes")
			{
				if($user["id"]==$taskinfo["sender"])
					$queryviewup = "update task_report set view_sender='yes' where task='".$id."'";
				else
				{
					$queryviewup = "update task_report set view_receiver='yes' where task='".$id."'";
					$usersender=false;
				}
				if($resultviewup=mysql_query($queryviewup))
				{
					if($usersender)
						$queryviewup = "select * from task_report where view_sender='no' and task='".$id."'";
					else
						$queryviewup = "select * from task_report where view_receiver='no' and task='".$id."'";
					if($resultviewup = mysql_query($queryviewup))
					{
						if(($num_rowsviewup = mysql_num_rows($resultviewup))>0)
						{
							$foudviewup=true;
							break;
						}
					}
					if(!$foundviewup)
					{
						if($usersender)
							$queryviewup = "update task set view_sender='yes' where id='".$id."'";
						else
							$queryviewup = "update task set view_receiver='yes' where id='".$id."'";
						@mysql_query($queryviewup);
					}
				}
			}
			else
			{
				if($user["id"]==$taskinfo["sender"])
					$queryviewup = "update task_report set view_sender='yes' where task='".$id."'";
				else
				{
					$queryx = "update task_group set view_update='yes' where task='".$id."' and userid='".$user["id"]."'";
					if($resultx = mysql_query($queryx))
					{
						$queryy="select * from task_group where view_update='no' and task='".$id."'";
						if($resulty = mysql_query($queryy))
						{
							if(($num_rowsy = mysql_num_rows($resulty))>0)
								$queryviewup = "update task_report set view_receiver='no' where task='".$id."'";
							else
								$queryviewup = "update task_report set view_receiver='yes' where task='".$id."'";
						}
					}
					$usersender=false;
				}
				if($resultviewup=mysql_query($queryviewup))
				{
					if($usersender)
						$queryviewup = "select * from task_report where view_sender='no' and task='".$id."'";
					else
						$queryviewup = "select * from task_group where view_update='no' and task='".$id."'";
					if($resultviewup = mysql_query($queryviewup))
					{
						if(($num_rowsviewup = mysql_num_rows($resultviewup))>0)
							$foudviewup=true;
					}
					if(!$foundviewup)
					{
						if($usersender)
							$queryviewup = "update task set view_sender='yes' where id='".$id."'";
						else
							$queryviewup = "update task set view_receiver='yes' where id='".$id."'";
						@mysql_query($queryviewup);
					}
				}				
			}
		}
		else
		{
			$_SESSION["taskresult"]="ERROR: Invalid Entry, choose a valid task";
			header("location:view.php");
			exit;
		}
	}
	else
	{
		$_SESSION["taskresult"]="ERROR: Invalid Entry, choose a valid task";
		header("location:view.php");
		exit;
	}
	$querycount = "select count(*) as counter  from task_report where task='".$id."' order by date desc";
	if($resultc= mysql_query($querycount))
	{
		$row = mysql_fetch_assoc($resultc);
		if($row["counter"] >50)
			$height = "style='height:500px'";
		else
			$height = "";
	}
	else
		$height = "";
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<script type="text/javascript" language="javascript">
function showpop_reportin()
{
	var ids = document.getElementById("id").value;
	showdivpop_reportin(ids);
}
var intervalID_reportin = window.setInterval(showpop_reportin, 10000);
</script>
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
        <div id="body_content" <?Php echo $height; ?>>
           <div id="task_in">
           <div id="message" name="message" class="white" style="text-align:center">
        		 <?php
                    if(isset($_SESSION["taskresult"]))
                    {
                        echo $_SESSION["taskresult"];
                        unset($_SESSION["taskresult"]);
                    }
                 ?>
      		</div><br/>
          <fieldset>
            	<legend>
           <span class='report_title'>Task</span>: <span class='report_title'><?php echo stripslashes($taskinfo["title"]); ?></span>&nbsp;&nbsp;<a href='taskin.php?id=<?php echo $_REQUEST["id"]; ?>' class='task_edit_link'>Previous Page?</a>
           		</legend>
          <span class='report_handreport'><?php echo htmlentities($taskinfo["task"]); ?></span>
         </fieldset>
           <br/><br/>
          	<br/>
            <span class='report_title'>Reports</span><br/><br/>
            <div>
            <div id="whole_report_in">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <?php
			$queryr = "select *  from task_report where task='".$id."' order by date desc";
			if($resultr = mysql_query($queryr))
			{
				if(($num_rowsr = mysql_num_rows($resultr))>0)
				{
					while($rowsr = mysql_fetch_array($resultr))
					{
						echo "<tr><td width='32%' class='report_date' valign='top'>".fixdate_comp($rowsr["date"])."</td> <td width='68%' valign='top'> From: ".getUserName($rowsr["reporter"])."<br/><span class='report_report'>".stripslashes(nl2br($rowsr["report"]))."</span></td></tr>";
						echo "<tr><td colspan='2' align='left' valign='top'><img src='images/horzlineb.jpg' border='0' /></td></tr>";
					}
				}
				else
					echo "<tr class='rowstylenob'><td colspan='2' align='center'>No Report Found</td></tr>";		
			}
			else
				echo "<tr class='rowstylenob'><td colspan='2' align='center'>No Report Found</td></tr>";
				?>
            </table>
            </div>
            </div>
            <br/>
             <?php
			if($taskinfo["status"] !="2")
			{
				?>
            <span class='report_title'>Your Report</span><br/>
            <?Php
			}
			else
			{
			?>
             <span class='report_title'>This Task is Terminated, No New Report is Allowed</span><br/>
            <?Php
			}
			?>
            <form action="savetask.php" method="post" onsubmit="return checkFieldc();">
            <input type="hidden" id="id" name="id" value="<?Php echo $_REQUEST["id"]; ?>" />
            <input type="hidden" id="taski" name="taski" value="<?php echo base64_encode("savereportr"); ?>" />
            <?php
			if($taskinfo["status"]!="2")
			{
				?>
            <textarea id="ureport" name="ureport" cols="70" rows="10"></textarea>
            <?php
			}
			?>
            <br/>
            <br/>
            <div id="message2" name="message2" class="white" style="text-align:left">
        &nbsp;
      		</div>
      <br/>
            <a href="taskin.php?id=<?php echo $_REQUEST["id"]; ?>"><img src="images/btncancel.png" border="0" alt="Cancel" /></a>
            <?php
			if($taskinfo["status"]!="2")
			{
				?>
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
             <input type="image"  src="images/btnreport.png" onmouseover="javascript:this.src='images/btnreport.png';" onmouseout="javascript:this.src='images/btnreport.png';">
             <?php
			}
			?>
             </form>
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
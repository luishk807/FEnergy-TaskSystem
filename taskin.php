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
			if(!pView($user["type"]))//validate the user to enter and view this page
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
			$queryo = "update task_update set view_update ='yes',status=NULL,created=NULL,report=NULL where task='".$id."' and userid='".$user["id"]."'";
			@mysql_query($queryo);
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
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<script type="text/javascript" language="javascript">
function showpop_report()
{
	var ids = document.getElementById("id").value;
	showdivpop_report(ids);
}
var intervalID_report = window.setInterval(showpop_report, 10000);
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
        <div id="body_content">
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
           <span class='report_title'>Task</span>: <span class='report_title'><?php echo stripslashes($taskinfo["title"]); ?></span>&nbsp;&nbsp;
		   	<a href='setting_task.php?id=<?php echo $_REQUEST["id"]; ?>' class='task_edit_link'>Edit Task?</a>
                       <?Php
		   $list_name="";
		   $list_name_comp="";
		   if($taskinfo["group_task"]=="yes")
		   {
				$queryx = "select * from task_group where task='".$taskinfo["id"]."'";
				if($resultx = mysql_query($queryx))
				{
					if(($num_rowsx = mysql_num_rows($resultx))>0)
					{
					 	$count=0;
						while($rowx = mysql_fetch_array($resultx))
						{
							if($count==0)
							{
								if(pViewb($user["type"]))
								 	$list_name = getName($rowx["userid"]);
								else
							   		$list_name = getUserName($rowx["userid"]);
						    }
							else
							{
							   if(pViewb($user["type"]))
								 $list_name .= ", ".getName($rowx["userid"]);
								else
							     $list_name .= ", ".getUserName($rowx["userid"]);
							}
							$count++;
						 }
					}
					else
					   $list_name ="N/A";
				 }
				 else
				  	$list_name ="N/A";
				$list_name_comp = "<br/><br/><fieldset><legend><span class='report_subtitle'>To Group</span></legend><span class='report_hand'>&nbsp;".$list_name."</span></fieldset>";
			}
			else
			{
				if(pViewb($user["type"]))
					$list_name = getName($taskinfo["receiver"]);
				else
					$list_name = getUserName($taskinfo["receiver"]);
				$list_name_comp = "<span class='report_subtitle'>To</span>:<span class='report_hand'>&nbsp;".$list_name."</span>";
			}
		   ?>
           <br/><br/>
           <span class='report_subtitle'>From</span>:<span class='report_hand'>&nbsp;<?php echo getName($taskinfo["sender"]); ?></span>&nbsp;&nbsp;&nbsp;<?Php echo $list_name_comp; ?>
           <br/><br/>
          <span class='report_subtitle'> Status</span>: 
          <span class='report_hand'>
		  <?php 
		  		if($taskinfo["status"]=="2")
		  			echo getTaskStatus($taskinfo["status"])." On: ".fixdateb($taskinfo["date_status"]); 
				else
					echo getTaskStatus($taskinfo["status"]);
			?>
          </span>
           <br/><br/>
          <?php
		  if($taskinfo["status"] !="2")
		  {
			  ?>
          <span class='report_subtitle'>Expected Completition Date</span>: 
          <span class='report_hand'>
		  <?php 
				echo $taskinfo["datec"];
			?>
          </span>
           <br/><br/>
          <?php
		  }
		  ?>
          <span class='report_handreport'><?php echo htmlentities($taskinfo["task"]); ?></span>
           <br/><br/>
           <img src="images/horzlineb.jpg" border="0" />
          	<br/>
            <span class='report_title'>Reports</span><br/>
            <div style="height:200px; overflow:auto" id="report_view">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <?php
			$queryr = "select *  from task_report where task='".$id."' order by date desc limit 4";
			if($resultr = mysql_query($queryr))
			{
				if(($num_rowsr = mysql_num_rows($resultr))>0)
				{
					while($rowsr = mysql_fetch_array($resultr))
					{
						echo "<tr><td width='32%' class='report_date'>".fixdate_comp($rowsr["date"])."</td> <td width='68%' class='report_report'>- ".rLongTextb(stripslashes($rowsr["report"]))."</td></tr>";
					}
					echo "<tr><td colspan='2' align='center' class='report_view'><a href='taskin_report.php?id=".$_REQUEST["id"]."' class='report_view_link'>...View Complete Report</a></td></tr>";
				}
				else
					echo "<tr class='rowstylenob'><td colspan='2' align='center'>No Report Found</td></tr>";		
			}
			else
				echo "<tr class='rowstylenob'><td colspan='2' align='center'>No Report Found</td></tr>";
				?>
            </table>
            </div>
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
            <input type="hidden" id="taski" name="taski" value="<?php echo base64_encode("savereport"); ?>" />
            <?php
			if($taskinfo["status"] !="2")
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
            <a href="view.php"><img src="images/btncancel.png" border="0" alt="Cancel" /></a>
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      <a href="Javascript:deletetask('tasks','<?php echo "taski=".base64_encode('delete')."&id=".$_REQUEST["id"]; ?>')" onmouseover="document.delete.src='images/btndelete.png'" onmouseout="document.delete.src='images/btndelete.png'"><img src="images/btndelete.png"  border="0" alt="Delete This User" name="delete" /></a>
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      <?php
			if($taskinfo["status"] !="2")
			{
				?>
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
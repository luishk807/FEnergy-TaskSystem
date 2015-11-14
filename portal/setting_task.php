<?php
session_start();
include "include/config.php";
include "include/function.php";
$user = $_SESSION["taskuser"];
adminlogin();
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
			$taskinfo = mysql_fetch_assoc($result);
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
	$groupnames=array();
	$queryx= "select * from task_group where task='".$id."'";
	if($resultx = mysql_query($queryx))
	{
		if(($num_rowsx= mysql_num_rows($resultx))>0)
		{
			while($rowx = mysql_fetch_assoc($resultx))
			{
				$groupnames[]=$rowx["userid"];
			}
		}
	}
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
<link rel="stylesheet" type="text/css" href="calendar_asset/css/ng_all.css">
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
    <div id="body_middle" >
        <div id="body_content" style="padding-left:50px">
           <div id="messageresultb">
           <?php
                 if(isset($_SESSION["taskresult"]))
                  {
                     echo $_SESSION["taskresult"]."<br/>";
                     unset($_SESSION["taskresult"]);
                  }
           ?></div>
           <div style="width:800px;">
           	   <span class='task_title'>Edit Task</span>: <span class='task_title'></span><?Php echo $taskinfo["title"]; ?></span>
               <br/>
               <div id="message" name="message" class="white">
                    &nbsp;
                  </div>
                  <form method="post" action="savetask.php" onsubmit="return checkFielde()">
                  <input type="hidden" id="taski" name="taski" value="<?php echo base64_encode("save"); ?>" />
                  <input type="hidden" id="id" name="id" value="<?Php echo $_REQUEST["id"]; ?>" />
                 From Agent:<br/>
                 	<?php
						$query = "select * from task_users order by id";
						if($result = mysql_query($query))
						{
							if(($num_rows = mysql_num_rows($result))>0)
							{
								echo "<select id='agentfrom' name='agentfrom'>";
								echo "<option value='na'>Select An Agent</option>";
								while($rows = mysql_fetch_array($result))
								{
									if($taskinfo["sender"]==$rows["id"])
										echo "<option value='".base64_encode($rows["id"])."' selected='selected'>".stripslashes($rows["name"])."</option>";
									else
										echo "<option value='".base64_encode($rows["id"])."'>".stripslashes($rows["name"])."</option>";
								}
								echo "</select>";
							}
							else
								echo "<span style='font-size:13pt'>No Agent Avaliable -</span> <a href='create.php' class='link_color'>Create One?</a>";
						}
						else
							echo "<span style='font-size:13pt'>No Agent Avaliable -</span> <a href='create.php' class='link_color'>Create One?</a>";
					?>
                 <br/><br/>
                  <?php
				 $checked ="";
				 $checkedyesno ="no";
				 $agentdisplayind= "";
				 $agentdisplaygp= "";
				 if($taskinfo["group_task"]=="yes")
				 {
					$checkedyesno ="yes";
					$agentdisplayind= "style='display:none'";
					$agentdisplaygp= "";
				 }
				 else
				 {
					 $agentdisplayind= "";
					 $checked = "checked='checked'";
					 $agentdisplaygp= "style='display:none'";
				 }
				 ?>
                 Individual Task? <input type="checkbox" id="groupselect_check" name="groupselect_check" <?php echo $checked; ?> onclick="changeGroup()"/>&nbsp;Yes
                 <br/>
                 <br/>
                 <div id="indvagent" name="indvagent" <?php echo $agentdisplayind; ?>>
              	 To Agent:<br/>
                 	<?php
						$query = "select * from task_users order by id";
						if($result = mysql_query($query))
						{
							if(($num_rows = mysql_num_rows($result))>0)
							{
								$agentselect = "yes";
								echo "<select id='agent' name='agent'>";
								echo "<option value='na'>Select An Agent</option>";
								while($rows = mysql_fetch_array($result))
								{
									if($taskinfo["receiver"]==$rows["id"])
										echo "<option value='".base64_encode($rows["id"])."' selected='selected'>".stripslashes($rows["name"])."</option>";
									else
										echo "<option value='".base64_encode($rows["id"])."'>".stripslashes($rows["name"])."</option>";
								}
								echo "</select>";
							}
							else
							{
								echo "<span style='font-size:13pt'>No Agent Avaliable -</span> <a href='create.php' class='link_color'>Create One?</a>";
								$agentselect = "no";
							}
						}
						else
						{
							echo "<span style='font-size:13pt'>No Agent Avaliable -</span> <a href='create.php' class='link_color'>Create One?</a>";
							$agentselect = "no";
						}
					?>
                  </div>
                 <div id="groupagent" name="groupagent" <?php echo $agentdisplaygp; ?>>
                 <fieldset>
                 	<legend>To Agents:</legend>
                    <div style="height:150px; overflow:auto; font-size:15pt;">
                    <?php
						$query = "select * from task_users order by id";
						if($result = mysql_query($query))
						{
							if(($num_rows = mysql_num_rows($result))>0)
							{
								$agentselect = "yes";
								$foundx = false;
								while($rows = mysql_fetch_array($result))
								{
									if($taskinfo["group_task"]=="yes")
									{
										$foundx = false;
										if(sizeof($groupnames)>0)
										{
											for($x=0;$x<sizeof($groupnames);$x++)
											{
												if($groupnames[$x]==$rows["id"])
												{
													$foundx=true;
													break;
												}
											}
										}
										if($foundx)
											echo "<input type='checkbox' id='agentg[]' name='agentg[]' checked='checked' value='".base64_encode($rows["id"])."' />&nbsp;".stripslashes($rows["username"])."<br/>";
										else
											echo "<input type='checkbox' id='agentg[]' name='agentg[]' value='".base64_encode($rows["id"])."' />&nbsp;".stripslashes($rows["username"])."<br/>";
									}
									else
										echo "<input type='checkbox' id='agentg[]' name='agentg[]' value='".base64_encode($rows["id"])."' />&nbsp;".stripslashes($rows["username"])."<br/>";
								}
							}
							else
							{
								echo "<span style='font-size:13pt'>No Agent Avaliable -</span> <a href='create.php' class='link_color'>Create One?</a>";
								$agentselect="no";
							}
						}
						else
						{
							echo "<span style='font-size:13pt'>No Agent Avaliable -</span> <a href='create.php' class='link_color'>Create One?</a>";
							$agentselect="no";
						}
					?>
                    </div>
                 </fieldset>
                 </div>
                 <input type="hidden" id="groupselect" name="groupselect" value="<?php echo $checkedyesno; ?>" />
                  <input type="hidden" id="agentselect" name="agentselect" value="<?php echo $agentselect; ?>" />
                 <br/><br/>
                  Title: <br/><input type="text" id="title" name="title" size="40" value="<?php echo stripslashes($taskinfo["title"]); ?>"  />
                  <br/><br/>
                  Status<br/>
                  <?php
						$query = "select * from task_status order by id";
						if($result = mysql_query($query))
						{
							if(($num_rows = mysql_num_rows($result))>0)
							{
								echo "<select id='taskstatus' name='taskstatus'>";
								echo "<option value='na'>Select A Status</option>";
								while($rows = mysql_fetch_array($result))
								{
									if($taskinfo["status"]==$rows["id"])
										echo "<option value='".base64_encode($rows["id"])."' selected='selected'>".stripslashes($rows["name"])."</option>";
									else
										echo "<option value='".base64_encode($rows["id"])."'>".stripslashes($rows["name"])."</option>";
								}
								echo "</select>";
							}
						}
							?>
                  <br/><br/>
                  Date For Completion &nbsp; <b><?php echo $taskinfo["datec"]; ?></b>&nbsp;<input type="checkbox" id="changecdate" name="changecdate" onclick="changecdatediv()" />&nbsp;<span class='changedate_style'>Change Date?</span>:<br/>
                  <input type="hidden" id="changecdates" name="changecdates" value="no" />
                 <div id="allowcdate" name="allowcdate" style="display:none;">
                  <input type="text" id="cdate" name="cdate"/>
                                    <script type="text/javascript">
						var ng_config = {
							assests_dir: 'calendar_asset/'	// the path to the assets directory
						}
					</script>
					<script type="text/javascript" src="js/calendar_js/ng_all.js"></script>
					<script type="text/javascript" src="js/calendar_js/calendar.js"></script>
					<script type="text/javascript">
					var my_cal;
					ng.ready(function(){
							// creating the calendar
							my_cal = new ng.Calendar({
								input: 'cdate',	// the input field id
								start_date: 'year - 1',	// the start date (default is today)
								display_date: new Date()	// the display date (default is start_date)
							});
							
						});
					</script>
                  </div>
                  <br/><br/>
                  Task: <br/><textarea id="task" name="task" cols="50" rows="10"><?php echo stripslashes(nl2br($taskinfo["task"])); ?></textarea><br/><br/>
                  <div id="message2" name="message2" class="white" style="text-align:left">&nbsp;</div><br/>
                  
                   <div style="text-align:left">
                   <a href="taskin.php?id=<?php echo $_REQUEST["id"]; ?>" onmouseover="document.cancel.src='images/btncancel.png'" onmouseout="document.cancel.src='images/btncancel.png'"><img src="images/btncancel.png"  border="0" alt="Cancel and return to View Page" name="cancel" /></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                   <input type="image"  src="images/btnsave.png" onmouseover="javascript:this.src='images/btnsave.png';" onmouseout="javascript:this.src='images/btnsave.png';"></div>
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
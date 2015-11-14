<?php
session_start();
include "include/config.php";
include "include/function.php";
$user = $_SESSION["taskuser"];
adminlogin();
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
                     echo $_SESSION["taskresult"]."<br/><br/>";
                     unset($_SESSION["taskresult"]);
                  }
           ?></div>
           <div  id="task1">
           	   <span class='task_title'>New Task</span>
               <br/>
               <div id="message" name="message" class="white">
                    &nbsp;
                  </div>
                  <form method="post" action="savetask.php" onsubmit="return checkFieldb()">
                  <input type="hidden" id="taski" name="taski" value="<?php echo base64_encode("create"); ?>" />
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
									if($user["id"]==$rows["id"])
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
                 Individual Task? <input type="checkbox" id="groupselect_check" name="groupselect_check" checked="checked" onclick="changeGroup()"/>&nbsp;Yes
                 <br/>
                 <br/>
                 <div id="indvagent" name="indvagent">
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
									echo "<option value='".base64_encode($rows["id"])."'>".stripslashes($rows["name"])."</option>";
								}
								echo "</select>";
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
                 <div id="groupagent" name="groupagent" style="display:none">
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
								while($rows = mysql_fetch_array($result))
								{
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
                 	<input type="hidden" id="groupselect" name="groupselect" value="no" />
                    <input type="hidden" id="agentselect" name="agentselect" value="<?php echo $agentselect; ?>" />
                 <br/><br/>
                  Title: <br/><input type="text" id="title" name="title" size="40" /><br/><br/>
                  Date For Completion:<br/>
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
                  <br/><br/>
                  Task: <br/><textarea id="task" name="task" cols="50" rows="10"></textarea><br/><br/>
                  <div id="message2" name="message2" class="white" style="text-align:center">&nbsp;</div><br/>
                  
                   <div style="text-align:right"><input type="image"  src="images/btncreate.png" onmouseover="javascript:this.src='images/btncreate.png';" onmouseout="javascript:this.src='images/btncreate.png';"></div>
                   </form>
           </div>
           <div id="task2"><img src="images/vertiline.jpg"/></div>
           <div id="task3">
           		<span class='task_title'>Task Pending</span>
                <br/>
                <br/>
                <div id="task_pending">
                <?php
				if(pView($user["type"]))
					$query = "select * from task where status='1' order by date desc";
				else
				{
					$query = "select * from task where status='1' and (sender='".$user["id"]."' or receiver='".$user["id"]."') order by date desc";
					if(verify_group_valid_task())
						$groupcheck ="yes";
				}
				$countg=0;
				$count=1;
				$found=false;
				if($groupcheck=="yes")
				{
					$queryx = "select * from task where group_task='yes' and status ='1' order by datec desc";
					if($resultx = mysql_query($queryx))
					{
						if(($num_rowsx = mysql_num_rows($resultx))>0)
						{
							while($rowgroup = mysql_fetch_array($resultx))
							{
								if(verify_group($rowgroup["id"],$user["id"]))
								{
									$exp = explode(" ",$rowgroup["date_status"]);
									$date = $exp[0];
									echo $count.".<a href='taskin.php?id=".base64_encode($rowgroup["id"])."' class='link_colorb'>Group Task -".$date."</a><br/>";
									$count++;
									$countg++;
									$found=true;
								}
							}
						}
					}
				}
				if($result = mysql_query($query))
				{
					if(($num_rows = mysql_num_rows($result))>0)
					{
						while(($rows = mysql_fetch_array($result)) && $countg < 3)
						{
							$exp = explode(" ",$rows["date"]);
							$date = $exp[0];
							if($rows["group_task"]=="yes")
							{
								if(!pView($user["type"]))
								{
									$queryx = "select * from task_group where userid='".$user["id"]."' and task='".$rows["id"]."'";
									if($resultx = mysql_query($queryx))
									{
										if(($numrowsx=mysql_num_rows($resultx))>0)
										{
											echo $count.".<a href='taskin.php?id=".base64_encode($rows["id"])."' class='link_colorb'>Group Task -".$date."</a><br/>";
											$found=true;
											$countg++;
										}
									}
								}
								else
								{
									echo $count.".<a href='taskin.php?id=".base64_encode($rows["id"])."' class='link_colorb'>Group Task -".$date."</a><br/>";
									$found=true;
									$countg++;
								}
							}
							else
							{
								echo $count.".<a href='taskin.php?id=".base64_encode($rows["id"])."' class='link_colorb'>".rLongText(getName($rows["receiver"]))." - ".$date."</a><br/>";
								$found=true;
								$countg++;
							}
							$count++;
						}
					}
				}
				if(!$found)
					echo "<span class='task_cp'>No Pending Task Found</span>";
				else
					echo "<br/><span class='task_cp'><a href='view.php?v=".base64_encode('pending')."' class='link_colorb'>View All Pending</a></span>";
				?>
                </div>
                <img src="images/horzline.jpg" border="0" /><br/>
                <span class='task_title'>Task Completed</span>
                <br/>
                <br/>
                <div id="task_completed">
                  <?php
				if(pView($user["type"]))
					$query = "select * from task where status='2'  order by date desc";
				else
				{
					$query = "select * from task where status='2' and (sender='".$user["id"]."' or receiver='".$user["id"]."') order by date desc";
					if(verify_group_valid_task())
						$groupcheck ="yes";
				}
				$countg=0;
				$count=1;
				$found=false;
				if($groupcheck=="yes")
				{
					$queryx = "select * from task where group_task='yes' and status ='2' order by datec desc";
					if($resultx = mysql_query($queryx))
					{
						if(($num_rowsx = mysql_num_rows($resultx))>0)
						{
							while($rowgroup = mysql_fetch_array($resultx))
							{
								if(verify_group($rowgroup["id"],$user["id"]))
								{
									$exp = explode(" ",$rowgroup["date_status"]);
									$date = $exp[0];
									echo $count.".<a href='taskin.php?id=".base64_encode($rowgroup["id"])."' class='link_colorb'>Group Task -".$date."</a><br/>";
									$countg++;
									$count++;
									$found=true;
								}
							}
						}
					}
				}
				if($result = mysql_query($query))
				{
					if(($num_rows = mysql_num_rows($result))>0)
					{
						while(($rows = mysql_fetch_array($result)) && $countg < 3)
						{
							$exp = explode(" ",$rows["date"]);
							$date = $exp[0];
							if($rows["group_task"]=="yes")
							{
								if(!pView($user["type"]))
								{
									$queryx = "select * from task_group where userid='".$user["id"]."' and task='".$rows["id"]."'";
									if($resultx = mysql_query($queryx))
									{
										if(($numrowsx=mysql_num_rows($resultx))>0)
										{
											echo $count.".<a href='taskin.php?id=".base64_encode($rows["id"])."' class='link_colorb'>Group Task -".$date."</a><br/>";
											$countg++;
											$found=true;
										}
									}
								}
								else
								{
									echo $count.".<a href='taskin.php?id=".base64_encode($rows["id"])."' class='link_colorb'>Group Task -".$date."</a><br/>";
									$countg++;
									$found=true;
								}
							}
							else
							{
								echo $count.".<a href='taskin.php?id=".base64_encode($rows["id"])."' class='link_colorb'>".rLongText(getName($rows["receiver"]))." - ".$date."</a><br/>";
								$countg++;
								$found=true;
							}
							$count++;
						}
					}
				}
				if(!$found)
					echo "<span class='task_cp'>No Completed Task Found</span>";
				else
					echo "<br/><span class='task_cp'><a href='view.php?v=".base64_encode('completed')."' class='link_colorb'>View All Completed</a></span>";
				?>
                </div>
           </div>
           <div class="cleardiv"></div>
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
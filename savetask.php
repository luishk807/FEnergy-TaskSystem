<?php
session_start();
include "include/config.php";
include "include/function.php";
adminlogin();
$task = base64_decode($_REQUEST["taski"]);
if($task=="create")
{
	$host= getHost();
	if(isset($_SESSION["taskuser"]))
	{
		$user = $_SESSION["taskuser"];
		$query = "select * from task_users where id='".$user["id"]."'";
		if($result = mysql_query($query))
		{
			if(($num_rows = mysql_num_rows($result))>0)
				$checkuser = mysql_fetch_assoc($result);
			else
			{
				$_SESSION["loginresult"]="ERROR: Invalid Entry";
				header('location:index.php');
				exit;
			}
		}
		else
		{
			$_SESSION["loginresult"]="ERROR: Invalid Entry";
			header('location:index.php');
			exit;
		}
	}
	else
	{
		header("location:status.php");
		exit;
	}
	$sender = base64_decode($_REQUEST["agentfrom"]);
	$agent = base64_decode($_REQUEST["agent"]);
	$title = ucwords(strtolower($_REQUEST["title"]));
	$date = fixdate($_REQUEST["cdate"]);
	//$emailnot = $_REQUEST["emailnot"];
	$group = $_REQUEST["groupselect"];
	if($group=="yes")
	{
		$groups = $_REQUEST["agentg"];
		if(sizeof($groups) < 2)
		{
			$_SESSION["taskresult"]="ERROR: You must select more than one user to create a group task";
			header("location:createtask.php");
			exit;
		}
	}
	$task = $_REQUEST["task"];
	//$query = "insert ignore into task (sender,receiver, task, title, datec,date,view_sender)values('".$sender."','".$agent."','".clean($task)."','".clean($title)."','".$date."',NOW(),'yes')";
	if($group=="yes")
		$query = "insert ignore into task (sender,task, title, datec,date,group_task)values('".$sender."','".clean($task)."','".clean($title)."','".$date."',DATE_ADD(NOW(), INTERVAL 2 HOUR),'yes')";
	else
		$query = "insert ignore into task (sender,receiver, task, title, datec,date)values('".$sender."','".$agent."','".clean($task)."','".clean($title)."','".$date."',DATE_ADD(NOW(), INTERVAL 2 HOUR))";
	if($result = mysql_query($query))
	{
		$taskid = mysql_insert_id();
		$_SESSION["taskresult"]="SUCCESS: Task Created";
		$from = false;
		$to = false;
		$query = "select * from task_users where id='".$sender."'";
		if($result = mysql_query($query))
		{
			if(($num_rows = mysql_num_rows($result))>0)
			{
				$agentfrom = mysql_fetch_assoc($result);
				$from=true;
			}
		}
		if($group=="yes")
		{
			$queryv= "insert ignore into task_update(task,userid,view_update)values('".$taskid."','".$user["id"]."','yes')";
			@mysql_query($queryv);
			$groups = $_REQUEST["agentg"];
			for($i=0;$i<sizeof($groups);$i++)
			{
				$queryv= "insert ignore into task_group(task,userid,date)values('".$taskid."','".base64_decode($groups[$i])."',DATE_ADD(NOW(), INTERVAL 2 HOUR))";
				@mysql_query($queryv);
				if(base64_decode($groups[$i]) != $user["id"])
				{
					$queryv= "insert ignore into task_update(task,userid,created)values('".$taskid."','".base64_decode($groups[$i])."','yes')";
					@mysql_query($queryv);
				}
			}
			if(sizeof($groups)>0)
			{
				$agents_list ="";
				for($v=0;$v<sizeof($groups);$v++)
				{
					if($v==0)
						$agents_list = "'".base64_decode($groups[$v])."'";
					else
						$agents_list .= ",'".base64_decode($groups[$v])."'";
				}
				$list_email="";
				$list_names="";
				$list_phone="";
				$list_id="";
				$queryx = "select * from task_users where id in (".$agents_list.")";
				if($resultx = mysql_query($queryx))
				{
					if(($num_rowsx = mysql_num_rows($resultx))>0)
					{
						$to=true;
						$count=1;
						while($agentto = mysql_fetch_array($resultx))
						{
							if(!empty($list_email))
								$list_email .=",".$agentto["email"];
							else
								$list_email =$agentto["email"];
							if(!empty($list_names))
								$list_names .="<br/>".$count.". ".$agentto["username"];
							else
								$list_names =$count.". ".$agentto["username"];
							if(!empty($list_id))
								$list_id .=" | ".$agentto["id"];
							else
								$list_id =$agentto["id"];
							if(!empty($agentto["phone"]))
							{
								if(!empty($list_phone))
									$list_phone .=","."1".stripslashes($agentto["phone"]);
								else
									$list_phone ="1".stripslashes($agentto["phone"]);
							}
							$count++;
						}
					}
					else
						$to=false;
				}
				else
					$to=false;
				if($to==true && $from==true)
				{
					if(!empty($list_phone))
					{
						$mmessage = "Family Energy Task Manager: New Task From ".$agentfrom["username"]."! Comp Date: $date, please login to www.familyenergyportal.com to view your task";
						if($result = sendSMS($list_phone,$mmessage))
							$_SESSION["taskresult"]="SUCCESS: Task Created and Text Messages Sent";
					}
					header("location:updater.php?ft=".base64_encode($agentfrom["username"])."&task=".base64_encode("create")."&le=".base64_encode($list_id));
					exit;
				}
			}
		}
		else
		{
			$queryv= "insert ignore into task_update(task,userid,view_update,created)values('".$taskid."','".$user["id"]."','yes',NULL),('".$taskid."','".$agent."','no','yes')";
			@mysql_query($queryv);
			$query = "select * from task_users where id='".$agent."'";
			if($result = mysql_query($query))
			{
				if(($num_rows = mysql_num_rows($result))>0)
				{
					$agentto = mysql_fetch_assoc($result);
					$to=true;
				}
			}
			if($to==true && $from==true)
			{
				$title = "New Task Created From  ".$agentfrom["username"];
				$message = "Hello ".$agentto["name"].",<br/><br/>";
				$message .="This is to let know that a new task has been created for you from ".$agentfrom["username"].".<br/><br/>";
				$message .="To view the new task please login to Family Energy Task System by clicking the link below.<br/>";
				$message .="<a href='".$host."xfor.php?i=".base64_encode($agentfrom["id"])."&t=".base64_encode('ind')."' target='_blank'>Login Here</a><br/><br/>Attn,<br/><br/>Family Energy Team<br/>";
				if($resultemail = sendEmail($agentto["email"],$title,$message))
					$_SESSION["taskresult"]="SUCCESS: Task Created and Email Sent";
				if(!empty($agentto["phone"]))
				{
					$mmessage = "Family Energy Task Manager: New Task From ".$agentfrom["username"]."! Comp Date: $date, please login to  www.familyenergyportal.com to view your task";
					if($result = sendSMS($agentto["phone"],$mmessage))
						$_SESSION["taskresult"]="SUCCESS: Task Created and Email and Text Message Sent";
				}
			}
		}
	}
	else
		$_SESSION["taskresult"]="ERROR: Task Can't Be Created";
	header("location:createtask.php");
	exit;
}
else if($task=="save")
{
	if(isset($_SESSION["taskuser"]))
	{
		$user = $_SESSION["taskuser"];
		$query = "select * from task_users where id='".$user["id"]."'";
		if($result = mysql_query($query))
		{
			if(($num_rows = mysql_num_rows($result))>0)
				$checkuser = mysql_fetch_assoc($result);
			else
			{
				$_SESSION["loginresult"]="ERROR: Invalid Entry";
				header('location:index.php');
				exit;
			}
		}
		else
		{
			$_SESSION["loginresult"]="ERROR: Invalid Entry";
			header('location:index.php');
			exit;
		}
	}
	else
	{
		header("location:status.php");
		exit;
	}
	$sender = base64_decode($_REQUEST["agentfrom"]);
	$agent = base64_decode($_REQUEST["agent"]);
	$title = ucwords(strtolower($_REQUEST["title"]));
	$status = base64_decode($_REQUEST["taskstatus"]);
	$id = base64_decode($_REQUEST["id"]);
	$list_emails="";
	$list_names="";
	$cuser = array();
	$host = getHost();
	if(empty($id))
	{
		$_SESSION["taskresult"]="ERROR: Invalid Entry";
		header('location:view.php');
		exit;
	}
	else
	{
		//get task information
		$queryo = "select * from task where id='".$id."'";
		if($resulto = mysql_query($queryo))
		{
			if(($numrows = mysql_num_rows($result))>0)
				$infox = mysql_fetch_assoc($resulto);
		}
		else
		{
			$_SESSION["taskresult"]="ERROR: Invalid Task Entry";
			header('location:view.php');
			exit;
		}
	}
	$group = $_REQUEST["groupselect"];
	if($group=="yes")
	{
		$groups = $_REQUEST["agentg"];
		if(sizeof($groups) < 2)
		{
			$_SESSION["taskresult"]="ERROR: You must select more than one user to assign a group task";
			header("location:setting_task.php?id=".base64_encode($id));
			exit;
		}
		else
		{
			//get current user to verify if email is needed
			$queryo = "select * from task_group where task='".$id."'";
			if($resulto = mysql_query($queryo))
			{
				if(($numrowso = mysql_num_rows($resulto))>0)
				{
					while($rowo= mysql_fetch_array($resulto))
					{
						$cuser[]=$rowo["userid"];
					}
				}
			}
			$groupquery = "group_task='yes',receiver=NULL ";
			$queryx = "delete from task_update where task='".$id."'";
			if($resultx = mysql_query($queryx))
			{
				$queryx="insert into task_update(task,userid,view_update,status)values";
				for($x=0;$x<sizeof($groups);$x++)
				{
					$xx=$x+1;
					if($x==0)
					{
						if(base64_decode($groups[$x])==$user["id"])
							$queryx .="('".$id."','".base64_decode($groups[$x])."','yes',NULL)";
						else
							$queryx .="('".$id."','".base64_decode($groups[$x])."','no','yes')";
					}
					else
					{
						if(base64_decode($groups[$x])==$user["id"])
						$queryx .=",('".$id."','".base64_decode($groups[$x])."','yes',NULL)";
						else
						$queryx .=",('".$id."','".base64_decode($groups[$x])."','no','yes')";
					}

				}
				@mysql_query($queryx);
			}
			$queryx = "delete from task_group where task='".$id."'";
			if($resultx = mysql_query($queryx))
			{
				$queryx="insert into task_group(task,userid,date)values";
				for($x=0;$x<sizeof($groups);$x++)
				{
					$xx=$x+1;
					if($x==0)
					{
						$queryx .="('".$id."','".base64_decode($groups[$x])."',DATE_ADD(NOW(), INTERVAL 2 HOUR))";
						$list_names = $xx.". ".getUserName(base64_decode($groups[$x]));
					}
					else
					{
						$queryx .=",('".$id."','".base64_decode($groups[$x])."',DATE_ADD(NOW(), INTERVAL 2 HOUR))";
						$list_names = "<br/>".$xx.". ".getUserName(base64_decode($groups[$x]));
					}
				}
				@mysql_query($queryx);
			}
		}
	}
	else
	{
		$queryx = "delete from task_group where task='".$id."'";
		@mysql_query($queryx);
		$queryx = "delete from task_update where task='".$id."'";
		@mysql_query($queryx);
		$queryx="insert into task_update(task,userid,view_update,status)values('".$id."','".$user["id"]."','yes',NULL),('".$id."','".$agent."','no','yes')";
		@mysql_query($queryx);
		$groupquery = "receiver='".$agent."', group_task='no' ";
	}
	$statuschange ="";
	$cdatequery="";
	$task = $_REQUEST["task"];
	$changecdate = $_REQUEST["changecdates"];
	if($status != "na" && !empty($status))
		$statuschange = ",status='$status',date_status=DATE_ADD(NOW(), INTERVAL 2 HOUR) ";
	if($changecdate =="yes")
	{
		$date = fixdate($_REQUEST["cdate"]);
		$cdatequery = ",datec='".$date."' ";
	}
	$query = "update task set sender='".$sender."', $groupquery, task='".clean($task)."', title='".clean($title)."' $cdatequery  $statuschange where id='".$id."'";
	if($result = mysql_query($query))
		$_SESSION["taskresult"]="SUCCESS: Task Saved";
	else
		$_SESSION["taskresult"]="ERROR: Task Can't Be Saved";
	//extract new info
	$queryi = "select * from task where id='".$id."'";
	if($resulti = mysql_query($queryi))
	{
		if(($num_rowsi = mysql_num_rows($resulti))>0)
			$taskinfo = mysql_fetch_assoc($resulti);
	}
	//preparting the send email;
	if($group=="yes")
	{
		$agents_list ="";
		$list_phone="";
		$groups = $_REQUEST["agentg"];
		$count=0;
		for($v=0;$v<sizeof($groups);$v++)
		{
			if($v==0)
				$agents_list = "'".base64_decode($groups[$v])."'";
			else
				$agents_list .= ",'".base64_decode($groups[$v])."'";
		}
		$queryx = "select * from task_users where id in (".$agents_list.")";
		if($resultx = mysql_query($queryx))
		{
			if(($num_rowsx = mysql_num_rows($resultx))>0)
			{
				while($agentto = mysql_fetch_array($resultx))
				{
					if(!empty($list_id))
						$list_id .=" | ".$agentto["id"];
					else
						$list_id =$agentto["id"];
					if(!empty($agentto["phone"]))
					{
						if(!empty($list_phone))
							$list_phone .=","."1".stripslashes($agentto["phone"]);
						else
							$list_phone ="1".stripslashes($agentto["phone"]);
					}
					$count++;
				}
			}
		}
		if($taskinfo["sender"] !=$info["sender"]  || stripslashes($infox["task"]) != $task  || $title != stripslashes($infox["title"]) || $status !=$infox["status"])
		{
			$mmessage = "Family Energy Task Manager Update: ".stripslashes($infox["title"])." From ".getUserName($infox["sender"])."!, please login to  www.familyenergyportal.com to view your task";
			if($result = sendSMS($list_phone,$mmessage))
				$_SESSION["taskresult"]="SUCCESS: Task Saved And Text Message Sent";
				
		}
		if(sizeof($groups) != sizeof($cuser) && $count>0)
		{
			$mmessage = "Family Energy Task Manager Group Change Update: ".stripslashes($infox["title"])." !, please login to  www.familyenergyportal.com to view your task";
			if($result = sendSMS($list_phone,$mmessage))
				$_SESSION["taskresult"]="SUCCESS: Task Saved And Text Message Sent";
		}
		header("location:updater.php?ft=".base64_encode(getUserName($taskinfo["sender"]))."&task=".base64_encode("save")."&le=".base64_encode($list_id));
		exit;
	}
	else
	{
		$textsend=false;
		$torec=false;
		$queryx = "select * from task_users where id='".$taskinfo["receiver"]."'";
		if($resultx = mysql_query($queryx))
		{
			if(($numrowsx = mysql_num_rows($resultx))>0)
				$recx = mysql_fetch_assoc($resultx);
		}
		$queryx = "select * from task_users where id='".$taskinfo["sender"]."'";
		if($resultx = mysql_query($queryx))
		{
			if(($numrowsx = mysql_num_rows($resultx))>0)
				$sendx = mysql_fetch_assoc($resultx);
		}
		if($infox["receiver"] != $taskinfo["receiver"] && !empty($recx["phone"]))
		{
			$mmessage = "Family Energy Task Manager Task Reassign Change: ".stripslashes($infox["title"])." !, Comp Date: ".$taskinfo["datec"]." please login to  www.familyenergyportal.com to view your task";
			$messagea="New task has been re-assigned for you from ".$sendx["username"].".<br/><br/>";
			if($result = sendSMS(stripslashes($recx["phone"]),$mmessage))
			{
				$textsend=true;
				$_SESSION["taskresult"]="SUCCESS: Task Saved And Text Message Sent";
			}
		}
		if($infox["sender"] != $taskinfo["sender"] && !empty($sendx["phone"]))
		{
			$torec=true;
			$mmessage = "Family Energy Task Manager Task Update: ".stripslashes($infox["title"])." is Yours!, please login to  www.familyenergyportal.com to view your task";
			$messageb="<li>Task ".stripslashes($taskinfo["title"])." belongs to ".stripslashes($sendx["username"])."</li>";
			if($result = sendSMS(stripslashes($sendx["phone"]),$mmessage))
			{
				$textsend=true;
				$_SESSION["taskresult"]="SUCCESS: Task Saved And Text Message Sent";
			}
		}
		if(stripslashes($infox["task"]) != $task  || $title != stripslashes($infox["title"]) || $status !=$infox["status"])
		{
			$messagec="<li>New and important changes for this task has been made</li>";
			$mmessage = "Family Energy Task Manager Update: ".stripslashes($infox["title"])." From ".getUserName($infox["sender"])."!, please login to  www.familyenergyportal.com to view your task";
			if($result = sendSMS(stripslashes($recx["phone"]),$mmessage))
			{
				$textsend=true;
				$_SESSION["taskresult"]="SUCCESS: Task Saved And Text Message Sent";
			}
		}
		if(!empty($recx["email"]))
		{
			if($torec)
				$emailto=stripslashes($recx["email"]).",".stripslashes($sendx["email"]);
			else
				$emailto=stripslashes($recx["email"]);
			$title = "Family Energy Task: ".stripslashes($taskinfo["title"])." Update! Created From  ".$agentfrom["username"];
			$message = "Hello ".stripslashes($recx["name"]).",<br/><br/>";
			$message .="The following changes are as follows:<br/><br/><ul>";
			$message .="$messagea";
			$message .="$messageb";
			$message .="$messagec";
			$message .="</ul>To view the new task please login to Family Energy Task System by clicking the link below.<br/>";
			$message .="<a href='".$host."xfor.php?i=".base64_encode($recx["id"])."&t=".base64_encode('ind')."' target='_blank'>Login Here</a><br/><br/>Attn,<br/><br/>Family Energy Team<br/>";
			if($resultemail = sendEmail($emailto,$title,$message))
			{ 
				if($textsend)
					$_SESSION["taskresult"]="SUCCESS: Task Created and Email And Text Message Sent";
				else
					$_SESSION["taskresult"]="SUCCESS: Task Created and Email Sent";
			}
		}
		
	}
	header("location:setting_task.php?id=".base64_encode($id));
	exit;
}
else if($task=="savereport")
{
	$ip = getIP();
	$host =getHost();
	$id = base64_decode($_REQUEST["id"]);
	$report = trim($_REQUEST["ureport"]);
	$user = $_SESSION["taskuser"];
	$query = "select * from task where id='".$id."'";
	$usersender = true;
	if($result = mysql_query($query))
	{
		if(($num_rows = mysql_num_rows($result))>0)
		{
			$checksender = mysql_fetch_assoc($result);
			if($checksender["sender"]==$user["id"])
				$usersender = true;
			else
				$usersender = false;
		}
		else
			$usersender = false;
	}
	else
		$usersender = false;
	$query = "insert ignore into task_report(task,reporter,report,date,ip)values('".$id."','".$user["id"]."','".clean($report)."',DATE_ADD(NOW(), INTERVAL 2 HOUR) ,'".$ip."')";
	if($result = mysql_query($query))
	{
		$queryo = "update task_update set view_update='no',report='yes' where task='".$id."'";
		@mysql_query($queryo);
		$queryo = "update task_update set view_update='yes',report=NULL where task='".$id."' and userid='".$user["id"]."'";
		@mysql_query($queryo);
		$_SESSION["taskresult"]="SUCCESS: REPORT SAVED!";
		if($checksender["group_task"] !="yes")
		{
			if($useru["sender"] ==$user["id"])
				$queryemail = "select * from task_users where id='".$checksender["receiver"]."'";
			else
				$queryemail = "select * from task_users where id='".$checksender["sender"]."'";
			if($resultemail = mysql_query($queryemail))
			{
				if(($num_rowsemail = mysql_num_rows($resultemail))>0)
				{
					$emailinfo = mysql_fetch_assoc($resultemail);
					$email = $emailinfo["email"];
					$title = "Family Energy Task Manager: Report Update ".stripslashes($checksender["title"])."!";
					$message = "Hello ".$emailinfo["name"].",<br/><br/>";
					$message .="A new report has been updated for task: <b>".$checksender["title"]."</b> by ".$checksender["username"]."!<br/><br/>";
					$message .="<fieldset><legend>Message:</legend>".nl2br($report)."</fieldset><br/><br/>";
					$message .="To view the update please login to Family Energy Task System just click the link below and the given username and password.<br/>";
					$message .="<a href='".$host."xfor.php?i=".base64_encode($emailinfo["id"])."&t=".base64_encode('ind')."' target='_blank'>Login Here</a><br/><br/>Attn,<br/><br/>Family Energy Team<br/>";
					if($resultemail = sendEmail($email,$title,$message))
						$_SESSION["taskresult"]="SUCCESS: Report Saved and Email Sent";
					if(!empty($emailinfo["phone"]))
					{
						$mmessage = "Family Energy Task Manager: New Message For Task ".stripslashes($checksender["title"])."! please login to www.familyenergyportal.com to view this update";
						if($result = sendSMS($emailinfo["phone"],$mmessage))
							$_SESSION["taskresult"]="SUCCESS: Report Saved  and Email and Text Message Sent";
					}
				}
			}
		}
		else
		{
			$list_emails="";
			$list_id="";
			$list_phone="";
			//get all receivers's email
			$queryx = "select * from task_group where task='".$id."' and userid !='".$user["id"]."'";
			if($resultx = mysql_query($queryx))
			{
				if(($numrowsx = mysql_num_rows($resultx))>0)
				{
					//get all emails
					$county=0;
					while($rowx = mysql_fetch_array($resultx))
					{
						$queryy = "select * from task_users where id='".$rowx["userid"]."'";
						if($resulty = mysql_query($queryy))
						{
							$rowy = mysql_fetch_assoc($resulty);
							if($county==0)
							{
								$list_emails = stripslashes($rowy["email"]);
								$list_id = $rowy["id"];
								if($numrowsx>1)
									$list_phone="1".stripslashes($rowy["phone"]);
								else
									$list_phone=stripslashes($rowy["phone"]);
							}
							else
							{
								$list_emails .= ",".stripslashes($rowy["email"]);
								$list_id .= " | ".$rowy["id"];
								$list_phone .=","."1".stripslashes($rowy["phone"]);
							}
						}
						$county++;
					}
				}
			}
			if(!empty($list_phone))
			{
				$mmessage = "Family Energy Task Manager: New Message For Task ".stripslashes($checksender["title"])."! login to www.familyenergyportal.com to view your update";
				if($result = sendSMS($list_phone,$mmessage))
					$_SESSION["taskresult"]="SUCCESS: Report Saved and Email and Text Message Sent";
			}
			if(!empty($list_emails))
			{
				$info = array("title"=>$checksender["title"],"report"=>$report);
				$_SESSION["info"]=$info;
				header("location:updater.php?ft=".base64_encode($user["username"])."&task=".base64_encode("sreport")."&le=".base64_encode($list_id)."&id=".base64_encode($id));
				exit;
			}
		}
	}
	else
		$_SESSION["taskresult"]="ERROR: Unable To Save Report!";
		
	header("location:taskin.php?id=".base64_encode($id));
	exit;
}
else if($task=="savereportr")
{
	$ip = getIP();
	$id = base64_decode($_REQUEST["id"]);
	$report = trim($_REQUEST["ureport"]);
	$user = $_SESSION["taskuser"];
	$query = "select * from task where id='".$id."'";
	$usersender = true;
	if($result = mysql_query($query))
	{
		if(($num_rows = mysql_num_rows($result))>0)
		{
			$checksender = mysql_fetch_assoc($result);
			if($checksender["sender"]==$user["id"])
				$usersender = true;
			else
				$usersender = false;
		}
		else
			$usersender = false;
	}
	else
		$usersender = false;
	$query = "insert ignore into task_report(task,reporter,report,date,ip)values('".$id."','".$user["id"]."','".clean($report)."',DATE_ADD(NOW(), INTERVAL 2 HOUR) ,'".$ip."')";
	if($result = mysql_query($query))
	{
		//update updates
		$queryo = "update task_update set view_update='no',report='yes' where task='".$id."'";
		@mysql_query($queryo);
		$queryo = "update task_update set view_update='yes',report=NULL where task='".$id."' and userid='".$user["id"]."'";
		@mysql_query($queryo);
		$_SESSION["taskresult"]="SUCCESS: REPORT SAVED!";
		if($checksender["group_task"] !="yes")
		{
			if($useru["sender"] ==$user["id"])
				$queryemail = "select * from task_users where id='".$checksender["receiver"]."'";
			else
				$queryemail = "select * from task_users where id='".$checksender["sender"]."'";
			if($resultemail = mysql_query($queryemail))
			{
				if(($num_rowsemail = mysql_num_rows($resultemail))>0)
				{
					$emailinfo = mysql_fetch_assoc($resultemail);
					$email = $emailinfo["email"];
					$title = "Family Energy Task Manager: Report Update ".stripslashes($checksender["title"])."!";
					$message = "Hello ".$emailinfo["name"].",<br/><br/>";
					$message .="A new report has been updated for task: <b>".$checksender["title"]."</b> by ".$checksender["username"]."!<br/><br/>";
					$message .="<fieldset><legend>Message:</legend>".nl2br($report)."</fieldset><br/><br/>";
					$message .="To view the update please login to Family Energy Task System just click the link below and the given username and password.<br/>";
					$message .="<a href='".$host."xfor.php?i=".base64_encode($emailinfo["id"])."&t=".base64_encode('ind')."' target='_blank'>Login Here</a><br/><br/>Attn,<br/><br/>Family Energy Team<br/>";
					if($resultemail = sendEmail($email,$title,$message))
						$_SESSION["taskresult"]="SUCCESS: Report Saved and Email Sent";
					if(!empty($emailinfo["phone"]))
					{
						$mmessage = "Family Energy Task Manager: New Message For Task ".stripslashes($checksender["title"])."! please login to www.familyenergyportal.com to view your update";
						if($result = sendSMS($emailinfo["phone"],$mmessage))
							$_SESSION["taskresult"]="SUCCESS: Report Saved and Email and Text Message Sent";
					}
				}
			}
		}
		else
		{
			$list_emails="";
			$list_id="";
			$list_phone="";
			//get all receivers's email
			$queryx = "select * from task_group where task='".$id."' and userid !='".$user["id"]."'";
			if($resultx = mysql_query($queryx))
			{
				if(($numrowsx = mysql_num_rows($resultx))>0)
				{
					//get all emails
					$county=0;
					while($rowx = mysql_fetch_array($resultx))
					{
						$queryy = "select * from task_users where id='".$rowx["userid"]."'";
						if($resulty = mysql_query($queryy))
						{
							$rowy = mysql_fetch_assoc($resulty);
							if($county==0)
							{
								$list_emails = stripslashes($rowy["email"]);
								$list_id=$rowy["id"];
								if($numrowsx>1)
									$list_phone = "1".stripslashes($rowy["phone"]);
								else
									$list_phone = stripslashes($rowy["phone"]);
							}
							else
							{
								$list_emails .= ",".stripslashes($rowy["email"]);
								$list_id .=" | ".$rowy["id"];
								$list_phone .= ","."1".stripslashes($rowy["phone"]);
							}
						}
						$county++;
					}
				}
			}
			if(!empty($list_phone))
			{
				$mmessage = "Family Energy Task Manager: New Message For Task ".stripslashes($checksender["title"])."! please login to www.familyenergyportal.com to view your update";
				if($result = sendSMS($list_phone,$mmessage))
				$_SESSION["taskresult"]="SUCCESS: Report Saved and Email and Text Message Sent";
			}
			if(!empty($list_emails))
			{
				$info = array("title"=>$checksender["title"],"report"=>$report);
				$_SESSION["info"]=$info;
				header("location:updater.php?ft=".base64_encode($user["username"])."&task=".base64_encode("sreportr")."&le=".base64_encode($list_id)."&id=".base64_encode($id));
				exit;
			}
		}
	}
	else
		$_SESSION["taskresult"]="ERROR: Unable To Save Report!";
	header("location:taskin_report.php?id=".base64_encode($id));
	exit;
}
else if($task=="delete")
{
	if(isset($_SESSION["taskuser"]))
	{
		$user = $_SESSION["taskuser"];
		$query = "select * from task_users where id='".$user["id"]."'";
		if($result = mysql_query($query))
		{
			if(($num_rows = mysql_num_rows($result))>0)
				$checkuser = mysql_fetch_assoc($result);
			else
			{
				$_SESSION["loginresult"]="ERROR: Invalid Entry";
				header('location:index.php');
				exit;
			}
		}
		else
		{
			$_SESSION["loginresult"]="ERROR: Invalid Entry";
			header('location:index.php');
			exit;
		}
	}
	else
	{
		header("location:status.php");
		exit;
	}
	$id = base64_decode($_REQUEST["id"]);
	$query = "delete from task where id='$id'";
	if($result = mysql_query($query))
	{
		$_SESSION["taskresult"]="SUCCESS: Task Deleted";
		$queryi = "delete from task_report where task='".$id."'";
		if($resulti = mysql_query($queryi))
			$_SESSION["taskresult"]="SUCCESS: Task And Reports Deleted";
		else
			$_SESSION["taskresult"]="ERROR: Task Deleted but Unable To Delete Reports";
		$queryi = "delete from task_group where task='".$id."'";
		if($resulti = mysql_query($queryi))
			$_SESSION["taskresult"]="SUCCESS: Task And Reports Deleted";
		else
			$_SESSION["taskresult"]="ERROR: Task Deleted but Unable To Delete Reports";
		$queryi = "delete from task_update where task='".$id."'";
		if($resulti = mysql_query($queryi))
			$_SESSION["taskresult"]="SUCCESS: Task And Reports Deleted";
		else
			$_SESSION["taskresult"]="ERROR: Task Deleted but Unable To Delete Reports";
	}
	else
		$_SESSION["taskresult"]="ERROR: Unable To Delete Task, Please try again later";
	header('location:view.php');
	exit;
}
else
{
	$_SESSION["taskresult"]="Error: Invalid Entry";
	header("location:home.php");
	exit;
}
include "include/unconfig.php";
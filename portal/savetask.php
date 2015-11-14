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
		$query = "insert ignore into task (sender,task, title, datec,date,view_sender,group_task)values('".$sender."','".clean($task)."','".clean($title)."','".$date."',DATE_ADD(NOW(), INTERVAL 2 HOUR),'yes','yes')";
	else
		$query = "insert ignore into task (sender,receiver, task, title, datec,date,view_sender)values('".$sender."','".$agent."','".clean($task)."','".clean($title)."','".$date."',DATE_ADD(NOW(), INTERVAL 2 HOUR),'yes')";
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
			$groups = $_REQUEST["agentg"];
			for($i=0;$i<sizeof($groups);$i++)
			{
				$queryv= "insert ignore into task_group(task,userid,date)values('".$taskid."','".base64_decode($groups[$i])."',DATE_ADD(NOW(), INTERVAL 2 HOUR))";
				@mysql_query($queryv);
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
					header("location:updater.php?ft=".base64_encode($agentfrom["username"])."&task=".base64_encode("create")."&le=".base64_encode($list_id));
					exit;
					/*$title = "New Group Task Created From  ".$agentfrom["username"];
					$message = "Hello,<br/><br/>";
					$message .="This is to let know that a new group task has been created by ".$agentfrom["username"]." for the following groups:.<br/>";
					$message .=$list_names."<br/><br/>";
					$message .="To view the new task please login to Family Energy Task System by clicking the link below.<br/>";
					$message .="<a href='$host' target='_blank'>Login Here</a><br/><br/>Attn,<br/><br/>Family Energy Team<br/>";
					if($resultemail = sendEmail($list_email,$title,$message))
						$_SESSION["taskresult"]="SUCCESS: Task Created and All Emails Sent";*/
				}
			}
		}
		else
		{
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
				if($resultemail = sendEmail($agentfrom["email"],$title,$message))
					$_SESSION["taskresult"]="SUCCESS: Task Created and Email Sent";
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
	if(empty($id))
	{
		$_SESSION["taskresult"]="ERROR: Invalid Entry";
		header('location:view.php');
		exit;
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
			$groupquery = "group_task='yes',receiver=NULL ";
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
		$groupquery = "receiver='".$agent."', group_task='no' ";
	}
	$queryi = "select * from task where id='".$id."'";
	if($resulti = mysql_query($queryi))
	{
		if(($num_rowsi = mysql_num_rows($resulti))>0)
			$taskinfo = mysql_fetch_assoc($resulti);
		else
		{
			$_SESSION["taskresult"]="ERROR: Invalid Entry";
			header('location:view.php');
			exit;
		}
	}
	else
	{
		$_SESSION["taskresult"]="ERROR: Invalid Entry";
		header('location:view.php');
		exit;
	}
	if($status != $taskinfo["status"])
		$statuschange = ",status='$status',date_status=DATE_ADD(NOW(), INTERVAL 2 HOUR) ";
	else
		$statuschange ="";
	$task = $_REQUEST["task"];
	$changecdate = $_REQUEST["changecdates"];
	if($changecdate =="yes")
	{
		$date = fixdate($_REQUEST["cdate"]);
		$query = "update task set sender='".$sender."', $groupquery, task='".clean($task)."', title='".clean($title)."',datec='".$date."' $statuschange where id='".$id."'";
	}
	else
		$query = "update task set sender='".$sender."', $groupquery, task='".clean($task)."', title='".clean($title)."' $statuschange where id='".$id."'";
	if($result = mysql_query($query))
		$_SESSION["taskresult"]="SUCCESS: Task Saved";
	else
		$_SESSION["taskresult"]="ERROR: Task Can't Be Saved";
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
	if($usersender)
	{
		$query = "insert ignore into task_report(task,reporter,report,date,ip,view_sender)values('".$id."','".$user["id"]."','".clean($report)."',DATE_ADD(NOW(), INTERVAL 2 HOUR) ,'".$ip."','yes')";
		$querys = "update task set view_sender='yes', view_receiver='no' where id='".$id."'";
	}
	else
	{
		$query = "insert ignore into task_report(task,reporter,report,date,ip,view_receiver)values('".$id."','".$user["id"]."','".clean($report)."',DATE_ADD(NOW(), INTERVAL 2 HOUR) ,'".$ip."','yes')";
		$querys = "update task set view_receiver='yes', view_sender='no' where id='".$id."'";
	}
	if($result = mysql_query($query))
	{
		@mysql_query($querys);
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
				}
			}
		}
		else
		{
			$queryx = "update task_group set view_update = 'yes' where task='".$id."' and userid='".$user["id"]."'";
			@mysql_query($queryx);
			$queryx = "update task_group set view_update = 'no' where task='".$id."' and userid !='".$user["id"]."'";
			@mysql_query($queryx);
			$list_emails="";
			$list_id="";
			if($checksender["sender"] ==$user["id"])
			{
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
								}
								else
								{
									$list_emails .= ",".stripslashes($rowy["email"]);
									$list_id .= " | ".$rowy["id"];
								}
							}
							$county++;
						}
					}
				}
			}
			else
			{
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
								}
								else
								{
									$list_emails .= ",".stripslashes($rowy["email"]);
									$list_id .= " | ".$rowy["id"];
								}
							}
							$county++;
						}
					}
				}
				$queryx = "select * from task_users where id='".$checksender["sender"]."'";
				if($resultx = mysql_query($queryx))
				{
					if(($numrowsx = mysql_num_rows($resultx))>0)
					{
						$rowx = mysql_fetch_assoc($resultx);
						if(empty($list_emails))
							$list_emails = stripslashes($rowx["email"]);
						else
							$list_emails .= ",".stripslashes($rowx["email"]);
						if(empty($list_id))
							$list_id = stripslashes($rowx["id"]);
						else
							$list_id .= " | ".stripslashes($rowx["id"]);
					}
				}
			}
			if(!empty($list_emails))
			{
				$info = array("title"=>$checksender["title"],"report"=>$report);
				$_SESSION["info"]=$info;
				header("location:updater.php?ft=".base64_encode($user["username"])."&task=".base64_encode("sreport")."&le=".base64_encode($list_id)."&id=".base64_encode($id));
				exit;
				/*$email = $list_emails;
				$title = "Family Energy Task Manager: Report Update ".stripslashes($checksender["title"])."!";
				$message = "Hello,<br/><br/>";
				$message .="A new report has been updated for task: <b>".$checksender["title"]."</b> by ".$user["username"]."!<br/><br/>";
				$message .="<fieldset><legend>Message:</legend>".nl2br($report)."</fieldset><br/><br/>";
				$message .="To view the update please login to Family Energy Task System just click the link below and the given username and password.<br/>";
				$message .="<a href='$host' target='_blank'>Login Here</a><br/><br/>Attn,<br/><br/>Family Energy Team<br/>";
				if($resultemail = sendEmail($list_emails,$title,$message))
					$_SESSION["taskresult"]="SUCCESS: Report Saved and Email Sent";*/
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
	if($usersender)
	{
		$query = "insert ignore into task_report(task,reporter,report,date,ip,view_sender)values('".$id."','".$user["id"]."','".clean($report)."',DATE_ADD(NOW(), INTERVAL 2 HOUR) ,'".$ip."','yes')";
		$querys = "update task set view_sender='yes', view_receiver='no' where id='".$id."'";
	}
	else
	{
		$query = "insert ignore into task_report(task,reporter,report,date,ip,view_receiver)values('".$id."','".$user["id"]."','".clean($report)."',DATE_ADD(NOW(), INTERVAL 2 HOUR) ,'".$ip."','yes')";
		$querys = "update task set view_receiver='yes',view_sender='no' where id='".$id."'";
	}
	if($result = mysql_query($query))
	{
		@mysql_query($querys);
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
				}
			}
		}
		else
		{
			$queryx = "update task_group set view_update = 'yes' where task='".$id."' and userid ='".$user["id"]."'";
			@mysql_query($queryx);
			$queryx = "update task_group set view_update = 'no' where task='".$id."' and userid !='".$user["id"]."'";
			@mysql_query($queryx);
			$list_emails="";
			$list_id="";
			if($checksender["sender"] ==$user["id"])
			{
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
								}
								else
								{
									$list_emails .= ",".stripslashes($rowy["email"]);
									$list_id .=" | ".$rowy["id"];
								}
							}
							$county++;
						}
					}
				}
			}
			else
			{
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
								}
								else
								{
									$list_emails .= ",".stripslashes($rowy["email"]);
									$list_id .=" | ".$rowy["id"];
								}
							}
							$county++;
						}
					}
				}
				$queryx = "select * from task_users where id='".$checksender["sender"]."'";
				if($resultx = mysql_query($queryx))
				{
					if(($numrowsx = mysql_num_rows($resultx))>0)
					{
						$rowx = mysql_fetch_assoc($resultx);
						if(empty($list_emails))
							$list_emails = stripslashes($rowx["email"]);
						else
							$list_emails .= ",".stripslashes($rowx["email"]);
						if(empty($list_id))
							$list_id = $rowx["id"];
						else
							$list_id .= " | ".$rowx["id"];
					}
				}
			}
			if(!empty($list_emails))
			{
				$info = array("title"=>$checksender["title"],"report"=>$report);
				$_SESSION["info"]=$info;
				header("location:updater.php?ft=".base64_encode($user["username"])."&task=".base64_encode("sreportr")."&le=".base64_encode($list_id)."&id=".base64_encode($id));
				exit;
				/*$email = $list_emails;
				$title = "Family Energy Task Manager: Report Update ".stripslashes($checksender["title"])."!";
				$message = "Hello,<br/><br/>";
				$message .="A new report has been updated for task: <b>".$checksender["title"]."</b> by ".$user["username"]."!<br/><br/>";
				$message .="<fieldset><legend>Message:</legend>".nl2br($report)."</fieldset><br/><br/>";
				$message .="To view the update please login to Family Energy Task System just click the link below and the given username and password.<br/>";
				$message .="<a href='$host' target='_blank'>Login Here</a><br/><br/>Attn,<br/><br/>Family Energy Team<br/>";
				if($resultemail = sendEmail($email,$title,$message))
					$_SESSION["taskresult"]="SUCCESS: Report Saved and Email Sent";*/
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
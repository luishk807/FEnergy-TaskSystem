<?php
session_start();
include "include/config.php";
include "include/function.php";
adminlogin();
if(empty($_SERVER['HTTP_REFERER']))
{
	header("location:status.php");
	exit;
}
$task = $_REQUEST["task"];
if($task=="save")
{
	$user = $_SESSION["taskuser"];
	$uname = trim($_REQUEST["uname"]);
	$upass = trim($_REQUEST["newpass"]);
	$changepass = $_REQUEST["changepass"];
	$newpass = trim($_REQUEST["newpass"]);
	$name = trim(ucwords(strtolower($_REQUEST["realname"])));
	$email =trim(strtolower($_REQUEST["uemail"]));
	if($email != $user["email"])
	{
		$query = "select * from task_users where email='".clean($email)."'";
		if($result = mysql_query($query))
		{
			if(($num_rows = mysql_num_rows($result))>0)
			{
				$_SESSION["taskresult"]="ERROR: email already in use";
				header('location:setting.php');
				exit;
			}
		}
	}
	$title = trim(ucwords(strtolower($_REQUEST["utitle"])));
	if($changepass=="yes")
		$query = "update task_users set username='".clean($uname)."',password='".md5(clean($newpass))."',name='".clean($name)."',title='".clean($title)."',email='".clean($email)."' where id='".$user["id"]."'";
	else
		$query = "update task_users set username='".clean($uname)."',name='".clean($name)."',title='".clean($title)."',email='".clean($email)."' where id='".$user["id"]."'";
	if($result = mysql_query($query))
		$_SESSION["taskresult"]="SUCCESS: Changes Saved";
	else
		$_SESSION["taskresult"]="ERROR: Unable To Save Changes";
	$query = "select * from task_users where id='".$user["id"]."'";
	if($result = mysql_query($query))
	{
		if(($num_rows = mysql_num_rows($result))>0)
		{
			$row = mysql_fetch_assoc($result);
			$user = array("id"=>$row["id"], "name"=>stripslashes($row["name"]),"username"=>stripslashes($row["username"]),"password"=>stripslashes($row["password"]),"email"=>stripslashes($row["email"]),'title'=>$row["title"],"status"=>$row["status"],"type"=>$row["type"]);
			adminstatus($row["status"]);
			$_SESSION["taskuser"]=$user;
			header("location:setting.php");
			exit;
		}
		else
		{
			$_SESSION["taskstatus"]=array("bad","Invalid Username And Password","You need to login to access this page<br/><br/>Please Click <a href='index.php'>Here</a> To Login");
			unset($_SESSION["taskuser"]);
			header("location:status.php");
			exit;
		}
	}
	else
	{
		$_SESSION["loginresult"]="System is unable to check your username and password, please try again later";
		unset($_SESSION["fmap_user"]);
		header("location:index.php");
		exit;
	}
}
else if($task=="create")
{
	$host = getHost();
	if(isset($_SESSION["taskuser"]))
	{
		$user = $_SESSION["taskuser"];
		$query = "select * from task_users where id='".$user["id"]."'";
		if($result = mysql_query($query))
		{
			if(($num_rows = mysql_num_rows($result))>0)
			{
				$checkuser = mysql_fetch_assoc($result);
				if($checkuser["type"] != "1" && $checkuser["type"] !='2')
				{
					$_SESSION["taskresult"]="ERROR: You don't have sufficient access to create users";
					header("location:status.php");
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
	$user = $_SESSION["taskuser"];
	$uname = trim($_REQUEST["uname"]);
	$upass = trim($_REQUEST["newpass"]);
	$name = trim(ucwords(strtolower($_REQUEST["realname"])));
	$email =trim(strtolower($_REQUEST["uemail"]));
	$status =$_REQUEST["ustatus"];
	$type = $_REQUEST["utype"];
	$title = trim(ucwords(strtolower($_REQUEST["utitle"])));
	$query = "select * from task_users where email='".clean($email)."'";
	if($result = mysql_query($query))
	{
		if(($num_rows = mysql_num_rows($result))>0)
		{
			$_SESSION["taskresult"]="ERROR: Email already exist, please another email";
			header("location:create.php");
			exit;
		}
	}
	$query = "insert ignore into task_users(username,password,name,title,email,status,type,date)values('".clean($uname)."','".md5(clean($upass))."','".clean($name)."','".clean($title)."','".clean($email)."','".$status."','".$type."',NOW())";
	if($result = mysql_query($query))
	{
		$_SESSION["taskresult"]="SUCCESS: User Created";
		$title = "$name, Your Account is Created!";
		$message = "Hello ".$name.",<br/><br/>";
		$message .="This is to let know that your account for the Family Energy Task Manager System has been created for you from and you can start using it.<br/><br/>";
		$message .="Your Login Information is as follow:<br/>Email: <b>".$email."</b><br/>Password: <b>".$upass."</b><br/><br/>";
		$message .="To login to Family Energy Task System just click the link below and the given email and password.<br/>";
		$message .="<a href='$host' target='_blank'>Login Here</a><br/><br/>You can always change this information by login in the website and change your settings.<br/><br/>Attn,<br/><br/>Family Energy Team<br/>";
		if($resultemail = sendEmail($email,$title,$message))
			$_SESSION["taskresult"]="SUCCESS: User Created and Email Sent";
	}
	else
		$_SESSION["taskresult"]="ERROR: Unable To Create User";
	header('location:viewuser.php');
	exit;	
}
else if($task=="savem")
{
	$host = getHost();
	if(isset($_SESSION["taskuser"]))
	{
		$user = $_SESSION["taskuser"];
		$query = "select * from task_users where id='".$user["id"]."'";
		if($result = mysql_query($query))
		{
			if(($num_rows = mysql_num_rows($result))>0)
			{
				$checkuser = mysql_fetch_assoc($result);
				if($checkuser["type"] != "1" && $checkuser["type"] !="2")
				{
					$_SESSION["taskresult"]="ERROR: You don't have sufficient access to modify users";
					header("location:status.php");
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
	$userid = base64_decode($_REQUEST["id"]);
	$email =trim(strtolower($_REQUEST["uemail"]));
	$query = "select * from task_users where id='".$userid."'";
	$changeemail=false;
	if($result = mysql_query($query))
	{
		if(($num_rows = mysql_num_rows($result))>0)
		{
			$checkusername = mysql_fetch_assoc($result);
			if($email != stripslashes($checkusername["email"]))
			{
				$query = "select * from task_users where email='".clean($email)."' and id !='".$userid."'";
				if($result = mysql_query($query))
				{
					$changeemail = true;
					if(($num_rows = mysql_num_rows($result))>0)
					{
						$_SESSION["taskresult"]="ERROR: Email already in use";
						header('location:settingm.php?id='.base64_encode($userid));
						exit;
					}
				}
			}
		}
		else
		{
			$_SESSION["taskresult"]="ERROR: invalid email";
			header("location:settingm.php?id=".base64_encode($userid));
			exit;
		}
	}
	else
	{
		$_SESSION["taskresult"]="ERROR: invalid email";
		header("location:settingm.php?id=".base64_encode($userid));
		exit;
	}
	$upass = trim($_REQUEST["newpass"]);
	$changepass = $_REQUEST["changepass"];
	$newpass = trim($_REQUEST["newpass"]);
	$name = trim(ucwords(strtolower($_REQUEST["realname"])));
	$uname = trim($_REQUEST["uname"]);
	$status =$_REQUEST["ustatus"];
	$type = $_REQUEST["utype"];
	$title = trim(ucwords(strtolower($_REQUEST["utitle"])));
	if($changepass=="yes")
		$query = "update task_users set username='".clean($uname)."',password='".md5(clean($newpass))."',name='".clean($name)."',title='".clean($title)."',email='".clean($email)."',status='".$status."',type='".$type."' where id='".$userid."'";
	else
		$query = "update task_users set username='".clean($uname)."',name='".clean($name)."',title='".clean($title)."',email='".clean($email)."',status='".$status."',type='".$type."' where id='".$userid."'";
	if($result = mysql_query($query))
	{
		$_SESSION["taskresult"]="SUCCESS: Changes For Admin $uname Saved";
		if($status =="2")
		{
		$title = "$name, Your Account is currently blocked!";
		$message = "Hello ".$name.",<br/><br/>";
		$message .="This is to let know that your account for the Family Energy Task Manager System has been recently updated and is currently blocked or cancelled<br/><br/>";
		$message .="Only Administrator or high staff personal can grant you access to the Family Energy Task Manager System.  You will be notified if your account becomes avaliable.<br/>";
		$message .="<br/><br/>Attn,<br/><br/>Family Energy Team<br/>";			
		}
		else
		{
		$title = "$name, Your Account is updated!";
		$message = "Hello ".$name.",<br/><br/>";
		$message .="This is to let know that your account for the Family Energy Task Manager System has been recently updated!<br/><br/>";
		if($changepass=="yes")
			$message .="Your New Login Information is as follow:<br/>Username: <b>$uname</b><br/>Password: <b>$upass</b><br/><br/>";
		else if($changeemail ==false)
			$message .="Your New Login Information is as follow:<br/>Email: <b>$email</b><br/>Password: Same as Before<br/><br/>";
		$message .="To login to Family Energy Task System just click the link below and the given username and password.<br/>";
		$message .="<a href='$host' target='_blank'>Login Here</a><br/><br/>Attn,<br/><br/>Family Energy Team<br/>";
		}
		if($resultemail = sendEmail($email,$title,$message))
			$_SESSION["taskresult"]="SUCCESS: User Changes is Saved and Email Sent";
	}
	else
		$_SESSION["taskresult"]="ERROR: Unable To Save Changes For Admin $uname";
	
	header('location:settingm.php?id='.$_REQUEST["id"]);
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
			{
				$checkuser = mysql_fetch_assoc($result);
				if($checkuser["type"] != "1")
				{
					$_SESSION["taskresult"]="ERROR: You don't have sufficient access to delete users";
					header("location:status.php");
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
	$query = "delete from task_users where id='$id'";
	if($result = mysql_query($query))
		$_SESSION["taskresult"]="SUCCESS: User Deleted";
	else
		$_SESSION["taskresult"]="ERROR: Unable To Delete User, Please try again later";
	header('location:viewuser.php');
	exit;
}
else
{
	header('location:status.php');
	exit;
}
include "include/unconfig.php";
?>
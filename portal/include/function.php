<?php
define("MAPS_HOST", "maps.google.com");
define("KEY", "ABQIAAAAUQoOcLjWVW04XTfLi1SbghRHDJMFrGd7U-5vIm6DVyt_Kv6o_BSNRkm6Jc5CUWvgHIeR0Q2uNVQ4Fw");
 $showbutton = true;
 function pView($type)
{
	if($type !='1' && $type !='2')
		return false;
	return true;
}
function pViewb($type)
{
	if($type =='1' || $type =='2')
		return true;
	return false;
}
function getIP()
{
	 return $_SERVER['REMOTE_ADDR'];
}
function getHost()
{
	return "http://www.familyenergyportal.com/";
}
function getGEO($address){
	// Initialize delay in geocode speed
	$delay = 0;
	$base_url = "http://" . MAPS_HOST . "/maps/geo?output=xml" . "&key=" . KEY;

	// Iterate through the rows, geocoding each address
  $geocode_pending = true;

  while ($geocode_pending) {
    $request_url = $base_url . "&q=" . urlencode($address);
   $xml = simplexml_load_file($request_url) or die("url not loading");

    $status = $xml->Response->Status->code;
    if (strcmp($status, "200") == 0) {
      // Successful geocode
      $geocode_pending = false;
      $coordinates = $xml->Response->Placemark->Point->coordinates;
      $coordinatesSplit = explode(",", $coordinates);
      // Format: Longitude, Latitude, Altitude
      $lat = $coordinatesSplit[1];
      $lng = $coordinatesSplit[0];
	  $values = array('lat'=>$lat,'lng'=>$lng);
 	  return $values;

    } else if (strcmp($status, "620") == 0) {
      // sent geocodes too fast
      $delay += 100000;
    } 
	else 
	{
      // failure to geocode
	  $geocode_pending = false;
	 	$values = array('lat'=>"",'lng'=>"");
  		return $values;
    }
    usleep($delay);
  }
}
function clean($str) 
{
	$str = trim($str);
	if(get_magic_quotes_gpc()) 
	{
		$str = stripslashes($str);
	}
	return mysql_real_escape_string($str);
}
function adminstatus($value)
{
	if($value !="1")
	{
		$_SESSION["loginresult"]="Your Account is currently Blocked";
		header("location:http://www.familyenergyportal.com/");
		exit;
	}
}
function adminlogin()
{
	if(!isset($_SESSION["taskuser"]))
	{
		//$_SESSION["loginresult"]="Illegal Access";
		unset($_SESSION["taskuser"]);
		$_SESSION["loginresult"]="Please Login To Continue";
		header("location:index.php");
		exit;
	}
	else
	{
		$user=$_SESSION["taskuser"];
		$query = "select * from task_users where id='".$user["id"]."'";
		if($result = mysql_query($query))
		{
			if(($num_rows =mysql_num_rows($result))>0)
			{
				$checkuser = mysql_fetch_assoc($result);
				if($checkuser["status"] !="1")
				{
					$_SESSION["loginresult"]="Your Account is blocked or Cancelled";
					unset($_SESSION["taskuser"]);
					//header("location:index.php");
					header("location:http://www.familyenergyportal.com/");
					exit;
				}
			}
			else
			{
				$_SESSION["loginresult"]="ERROR: Invalid Entry";
				unset($_SESSION["taskuser"]);
				header("location:http://www.familyenergyportal.com/");
				exit;
			}
		}
		else
		{
			$_SESSION["loginresult"]="ERROR: Invalid Entry";
			unset($_SESSION["taskuser"]);
			header("location:http://www.familyenergyportal.com/");;
			exit;
		}
		/*if($user["status"] != "1")
		{
			$_SESSION["loginresult"]="Your Account is blocked or Cancelled";
			unset($_SESSION["taskuser"]);
			header("location:index.php");
			exit;
		}*/
	}
}
function convertUS($number)
{
	return number_format($number, 2, '.', ',');
}
function fixdate($str)
{
	if(!empty($str))
	{
		$exp = explode("-",$str);
		if(sizeof($exp)>2)
		{
			$y = $exp[0];
			$m = $exp[1];
			$d = $exp[2];
			if($m<10)
				$m = "0".$m;
			if($d<10)
				$d = "0".$d;
			$newdate = $y."-".$m."-".$d;
			return $newdate;
		}
	}
	return "";
}
function fixdateb($str)
{
	if(!empty($str))
	{
		$exp = explode(" ",$str);
		if(sizeof($exp)>1)
		{
			return $exp[0];
		}
	}
	return "";
}
function fixdate_comp($value)
{
	$date="";
	$ampm="am";
	if(!empty($value))
	{
		$exp = explode(" ",$value);
		if(sizeof($exp)>1)
		{
			$date = $exp[0];
			$exptime = explode(":",$exp[1]);
			if($exptime[0]>11)
			{
				$h = $exptime[0] - 12;
				$ampm = "pm";
			}
			else
				$h = $exptime[0];
			$date .= " ".$h.":".$exptime[1]." ".$ampm;
			return $date;
		}
		else
			$date ="";
	}
	else
		$date="";
	return $date;
}
function getStatus($value)
{
	$query = "select * from task_users_status where id='$value'";
	if($result = mysql_query($query))
	{
		if(($num_rows = mysql_num_rows($result))>0)
		{
			$row = mysql_fetch_assoc($result);
			return stripslashes($row["name"]);
		}
		else
			return "N/A";
	}
	else
		return "N/A";
}
function getTaskStatus($value)
{
	$query = "select * from task_status where id='$value'";
	if($result = mysql_query($query))
	{
		if(($num_rows = mysql_num_rows($result))>0)
		{
			$row = mysql_fetch_assoc($result);
			return stripslashes($row["name"]);
		}
		else
			return "N/A";
	}
	else
		return "N/A";
}
function getCoords($value)
{
	$coordss = $value;
	if(!empty($coordss))
	{
		$coorda=explode(",",$coordss);
		$cod = array("lat"=>$coorda[1],"lng"=>$coorda[0]);
	}
	else
		$cod=array("lat"=>"","lng"=>"");
	return $cod;
}
function getName($id)
{
	$namea="";
	$query = "select * from task_users where id='".$id."'";
	if($result = mysql_query($query))
	{
		if(($num_rows = mysql_num_rows($result))>0)
		{
			$rows = mysql_fetch_assoc($result);
			$namea= stripslashes($rows["name"]);
		}
		else
			$namea="N/A";
	}
	else
		$namea = "N/A";
	return $namea;
}
function getUserName($id)
{
	$namea="";
	$query = "select * from task_users where id='".$id."'";
	if($result = mysql_query($query))
	{
		if(($num_rows = mysql_num_rows($result))>0)
		{
			$rows = mysql_fetch_assoc($result);
			$namea= stripslashes($rows["username"]);
		}
		else
			$namea="N/A";
	}
	else
		$namea = "N/A";
	return $namea;
}
function rLongText($str)
{
	if(!empty($str))
	{
		if(strlen($str)>30)
		{
			$str = substr($str,0,30);
			$str = $str."....";
		}
		else
			return $str;
	}
	return $str;
}
function rLongTextb($str)
{
	if(!empty($str))
	{
		if(strlen($str)>30)
		{
			$str = substr($str,0,50);
			$str = $str."....";
		}
		else
			return $str;
	}
	return $str;
}
function rLongTextc($str)
{
	if(!empty($str))
	{
		if(strlen($str)>6)
		{
			$str = substr($str,0,6);
			$str = $str."...";
		}
		else
			return $str;
	}
	return $str;
}
function sendEmail($email_to,$title,$messages)
{
	$host = getHost();
	if(empty($email_to))
		return false;
	$message = "<table width='100%' border='0' cellspacing='0' cellpadding='0'><tr><td align='center'><table width='800' border='0' cellspacing='0' cellpadding='0'><tr><td><img src='$host/images/email1.jpg' width='800' height='210' alt='email_t' style='display:block;'/></td></tr><tr><td background='$host/images/email2.jpg'><br/><br/>
<table width='100%' border='0' cellspacing='0' cellpadding='0'><tr><td width='7%'>&nbsp;</td><td width='84%' align='left' valign='top'>";
	$message .=$messages;
	$message .="</td><td width='9%'>&nbsp;</td></tr></table><br/></td></tr><tr><td><img src='$host/images/email3.jpg' width='800' height='143' style='display:block;'/></td></tr></table></td></tr></table>";
	$headers  = 'MIME-Version: 1.0'."\r\n";
	$headers .='Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .="From: FamilyEnergy Task Manager<no-reply@yourfamilyenergy.com>\r\n"."X-Mailer: PHP/".phpversion();
	if($result = mail($email_to,$title, $message,$headers))
		return true;
	else
		return false;
}
function verify_group($task,$id)
{
	$queryq = "select * from task_group where userid='".$id."' and task='".$task."'";
	if($resultq = mysql_query($queryq))
	{
		if(($num_rowsq = mysql_num_rows($resultq))>0)
			return true;
		else
			return false;
	}
	return false;
}
function verify_group_valid($task,$id)
{
	$user = $_SESSION["taskuser"];
	$queryq = "select * from task_group where userid='".$id."' and view_update='no' and task='".$task."'";
	if($resultq = mysql_query($queryq))
	{
		if(($num_rowsq = mysql_num_rows($resultq))>0)
			return true;
		else
			return false;
	}
	return false;
}
function verify_group_valid_task()
{
	$user = $_SESSION["taskuser"];
	$queryq = "select * from task_group where userid='".$user["id"]."'";
	if($resultq = mysql_query($queryq))
	{
		if(($num_rowsq = mysql_num_rows($resultq))>0)
			return true;
		else
			return false;
	}
	return false;
}
?>
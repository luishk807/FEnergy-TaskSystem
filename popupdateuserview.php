<?php
session_start();
include "include/config.php";
include "include/function.php";
$user= $_SESSION["taskuser"];
?>
<?php
	  $query = "select * from task_users where id != '".$user["id"]."' and id !=1 order by date desc";
		if($result = mysql_query($query))
		{
			if(($num_rows = mysql_num_rows($result))>15)
				$height ="style='font-size:15pt;'";
			else
				$height="style='height:500px;font-size:15pt;'";
		}
		else
			$height="style='height:500px;font-size:15pt;'";
	  ?>
      <div <?Php echo $height; ?>>
      <?php
	  	$taskview = base64_decode($_REQUEST["taskview"]);
		if($taskview=="signin")
		{
			$query = "select id,status,name,last_checkin as dateget,username from task_users where id != '".$user["id"]."' order by last_checkin desc";
			$titlecol = "Last Check-in";
		}
		else if($taskview=="sortname")
		{
							 $query = "select id,status,name,date as dateget,username from task_users where id != '".$user["id"]."' order by name";
                            $titlecol = "Created";
		}
		else
		{
			$query = "select id,status,name,date as dateget,username from task_users where id != '".$user["id"]."' order by date desc";
			$titlecol = "Created";
		}
	  ?>
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr style="background-color:#014681; color:#FFF">
    <td width="7%">&nbsp;</td>
    <td width="28%" align="center" valign="middle">Username</td>
    <td width="27%" align="center" valign="middle">Name</td>
    <td width="21%" align="center" valign="middle">Status</td>
    <td width="17%" align="center" valign="middle"><?php echo $titlecol; ?></td>
  </tr>
  <?php
  	//$query = "select * from task_users where id != '".$user["id"]."' order by date desc";
	if($result = mysql_query($query))
	{
		if(($num_rows = mysql_num_rows($result))>0)
		{
			$count = 1;
			$total = 0;
			while($rows = mysql_fetch_array($result))
			{
				$total = $count %2;
				if($total !=0)
					$style = "style='background-color:#e6f882'";
				else
					$style="";
				if($taskview=="signin")
				{
					$date = fixdate_comp($rows["dateget"]);
					if(empty($date))
						$date = "N/A";
				}
				else
					$date = fixdateb($rows["dateget"]);
				echo "<tr class='rowstyle' $style><td height='27' align='center' valign='middle'>$count</td><td height='27' align='center' valign='middle'><a class='adminlink' href='settingm.php?id=".base64_encode($rows["id"])."'>".stripslashes($rows["username"])."</a></td><td height='27' align='center' valign='middle'>".stripslashes($rows["name"])."</td><td height='27' align='center' valign='middle'>".getStatus($rows["status"])."</td><td height='27' align='center' valign='middle'>".$date."</td></tr>";
				$count++;
			}
		}
		else
			echo "<tr class='rowstyleno'><td colspan='5' align='center' valign='middle'>No User Created</td></tr>";
	}
	else
		echo "<tr class='rowstyleno'><td colspan='5' align='center' valign='middle'>No User Created</td></tr>";
  ?>
        </table>
        </div>
<?php
include "include/config.php";
?>
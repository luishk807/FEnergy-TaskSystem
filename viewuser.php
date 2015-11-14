<?php
session_start();
include "include/config.php";
include "include/function.php";
$user = $_SESSION["taskuser"];
adminlogin();
if($user["type"] !='2' && $user["type"] !='1')
{
	$_SESSION["taskresult"]="ERROR: Invalid Access";
	header("location:home.php");
	exit;
}
$user=$_SESSION["taskuser"];
unset($_SESSION["prevlink"]);
$popuserview="open";
$showmainbutton = true;
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
<script type="text/javascript" language="javascript">
function showpopvun()
{
	var selectuser = document.getElementById("selectview").value;
	changeuserview(selectuser);
}
//var intervalIDvun = window.setInterval(showpopvun, 10000);
</script>
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
           <div style="text-align:center">
               <form action="" method="post">
               <div style="text-align:center; font-size:15pt;">Hello <b><u><?php echo $user["username"]; ?></u></b>, Select user you wish to see.
                <select id="selectview" name="selectview" onchange="changeuserview(this.value)">
                    <option value="<?php echo base64_encode('datecreated'); ?>" selected="selected">Select A View</option>
                    <option value="newuser">Create New User</option>
                    <option value="<?php echo base64_encode('signin'); ?>">Sort By Sign-in Date</option>
                    <option value="<?php echo base64_encode('datecreated'); ?>">Sort By Date Created</option>
                    <option value="<?php echo base64_encode('sortname'); ?>">Sort By Name</option>
                </select> 
               </div>
               </form>
                <br/>
                <div style="text-align:center">
                <?php
                 if(isset($_SESSION["taskresult"]))
                 {
                     echo $_SESSION["taskresult"];
                     unset($_SESSION["taskresult"]);
                 }
                ?>
               </div>
              <div style="width:850px; padding-left:50px;" id="usercont" name="usercont">
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
              </div>
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
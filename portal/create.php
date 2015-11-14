<?php
session_start();
include "include/config.php";
include "include/function.php";
$user = $_SESSION["taskuser"];
adminlogin();
if(!pView($user["type"]))
{
	$_SESSION["taskresult"]="ERROR: Invalid Access";
	header("location:home.php");
	exit;
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
           <div style="text-align:center">
           <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <form action="save.php" method="post" onsubmit="return checkFieldf()">
            <input type="hidden" id="task" name="task" value="create"/>
    	    <tr>
    	      <td colspan="2" align="center" valign="middle"><div id="message" name="message" class="white" style="text-align:center">
        &nbsp;
        <?php
                    if(isset($_SESSION["taskresult"]))
                    {
                        echo $_SESSION["taskresult"];
                        unset($_SESSION["taskresult"]);
                    }
                 ?>
      </div> </td>
   	        </tr>
    	    <tr>
    	      <td width="27%" height="37" align="right" valign="middle">Username:</td>
    	      <td width="73%" align="left" valign="middle">&nbsp;&nbsp;<input type="text" id="uname" name="uname" size="60" value="" /></td>
  	      </tr>
<tr>
                    <td width="27%" height="36" align="right" valign="middle">New Password: </td>
                    <td width="73%" align="left" valign="middle">&nbsp;&nbsp;<input type="password" id="newpass" name="newpass" size="60" value="" /></td>
                  </tr>
                  <tr>
                    <td height="36" align="right" valign="middle">Re-Type Password:</td>
                    <td align="left" valign="middle">&nbsp;&nbsp;<input type="password" id="renewpass" name="renewpass" size="60" /></td>
                  </tr>
    	    <tr>
    	      <td height="37" align="right" valign="middle">Name:</td>
    	      <td align="left" valign="middle">&nbsp;&nbsp;<input type="text" id="realname" name="realname" size="60" value="" /></td>
  	      </tr>
    	    <tr>
    	      <td height="37" align="right" valign="middle">Email:</td>
    	      <td align="left" valign="middle">&nbsp;&nbsp;<input type="text" id="uemail" name="uemail" size="60" value="" /></td>
  	      </tr>
    	    <tr>
    	      <td height="37" align="right" valign="middle">Title:</td>
    	      <td align="left" valign="middle">&nbsp;&nbsp;<input type="text" id="utitle" name="utitle" size="60" value=""/></td>
  	      </tr>
    	    <tr>
    	      <td height="27" align="right" valign="middle">Status:</td>
    	      <td align="left" valign="middle">&nbsp;&nbsp;  <select id="ustatus" name="ustatus">
                <?php
					$query = "select * from task_users_status order by id";
					if($result = mysql_query($query))
					{
						if(($num_rows = mysql_num_rows($result))>0)
						{
							while($rows = mysql_fetch_array($result))
							{
								echo "<option value='".$rows["id"]."'>".$rows["name"]."</option>";
							}
						}
					}
				?>
              </select>
              </td>
  	      </tr>
    	    <tr>
    	      <td height="37" align="right" valign="middle">Type:</td>
    	      <td align="left" valign="middle">
              	&nbsp;&nbsp;
                <select id="utype" name="utype">
                <?php
					$query = "select * from task_admin_type order by id";
					if($result = mysql_query($query))
					{
						if(($num_rows = mysql_num_rows($result))>0)
						{
							while($rows = mysql_fetch_array($result))
							{
								echo "<option value='".$rows["id"]."'>".$rows["name"]."</option>";
							}
						}
					}
				?>
              </select>
              </td>
  	      </tr>
    	    <tr>
    	      <td height="47" colspan="2" align="left" valign="middle">
              <div id="message2" name="message2" class="white" style="text-align:center">
        &nbsp;
      </div>
              </td>
   	        </tr>
    	    <tr>
    	      <td colspan="2" align="center" valign="middle">
      <a href="home.php"><img src="images/btncancel.png" border="0" alt="Cancel" /></a>
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
              <input type="image"  src="images/btncreate.png" onmouseover="javascript:this.src='images/btncreate.png';" onmouseout="javascript:this.src='images/btncreate.png';">
              </td>
  	      </tr>
    	    <tr>
    	      <td colspan="2" align="left" valign="middle">&nbsp;</td>
  	      </tr>
          </form>
        </table>
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
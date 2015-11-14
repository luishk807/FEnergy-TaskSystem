         <?php
		 session_start();
		 include "include/config.php";
		 include "include/function.php";
		 $v = base64_decode($_REQUEST["v"]);
		 ?>
            <?php
	  $imgnew = "<img src='images/newgif.gif' border='0' alt='new'/>";
$user=$_SESSION["taskuser"];
unset($_SESSION["prevlink"]);
$showmainbutton = true;
$total=0;
$viewtype=true;
$viewtitle ="";
if($v=="all" || empty($v))
{
	if(!pView($user["type"]))
	{
		$query = "select count(*) as counter from task where receiver='".$user["id"]."' order by date desc";
	}
	else
		$query = "select count(*) as counter from task  order by date desc";
	
}
else if($v=="pending")
{
	if(!pView($user["type"]))
	{
		$query = "select count(*) as counter from task where status='1' and receiver='".$user["id"]."'  order by datec desc";
		$querys = "select *  from task where status='1' and receiver='".$user["id"]."'  and group_task='no'  order by datec desc";
		$queryx = "select * from task where group_task='yes' and status='1' order by datec desc";
		if(verify_group_valid_task())
			$groupcheck="yes";
	}
	else
	{
		$query = "select count(*) as counter from task where status='1'  order by date desc";
		$querys = "select *  from task where status='1'  order by date desc";
	}
	$viewtitle = "Pending";
	$viewtype=false;
	
}
else if($v=="completed")
{
	if(!pView($user["type"]))
	{
		$query = "select count(*) as counter from task where status='2' and receiver='".$user["id"]."' order by date_status desc";
		$querys = "select *  from task where status='2' and receiver='".$user["id"]."'  and group_task='no'  order by date_status desc";
		$queryx = "select * from task where group_task='yes' and status='2' order by date_status desc";
		if(verify_group_valid_task())
			$groupcheck="yes";
	}
	else
	{
		$query = "select count(*) as counter from task where status='2'  order by date_status desc";
		$querys = "select *  from task where status='2' order by date_status desc";
	}
	$viewtitle = "Completed";
	$viewtype=false;
}
else if($v=="misc")
{
	if(!pView($user["type"]))
	{
		$query = "select count(*) as counter from task where status !='1' and status !='2' and receiver='".$user["id"]."' order by date desc";
		$querys = "select *  from task where status !='1' and status !='2' and receiver='".$user["id"]."' and group_task='no' order by date_status desc";
		$queryx = "select * from task where group_task='yes' and status !='1' and status !='2' order by date_status desc";
		if(verify_group_valid_task())
			$groupcheck="yes";
	}
	else
	{
		$query = "select count(*) as counter from task where status !='1' and status !='2' order by date desc";
		$querys = "select *  from task where status !='1' and status !='2' order by date_status desc";
	}
	$viewtitle = "Miscelaneous";
	$viewtype=false;
}
else
{
	if(pView($user["type"]))
	{
		$query = "select count(*) as counter from task where receiver='".$user["id"]."' order by date desc";
	}
	else
	{
		$query = "select count(*) as counter from task  order by date desc";
	}
}
if($result = mysql_query($query))
{
	$row = mysql_fetch_assoc($result);
	if($row["counter"]>12)
		$height ="style='font-size:15pt;'";
	else
		$height="style='height:500px; font-size:15pt;'";
}
else
	$height="style='height:500px; font-size:15pt;'";
	  ?>
      <div  <?Php echo $height; ?>>
        <?php
	  if($viewtype==true)
	  {
		  ?>
      <div style="width:850px; padding-left:50px;">
      <span class="task_view_title">Task Completed</span><br/>
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr style="background-color:#014681; color:#FFF">
            <td width="7%">&nbsp;</td>
            <td width="22%" align="center" valign="middle">Username</td>
            <td width="36%" align="center" valign="middle">Task</td>
            <td width="19%" align="center" valign="middle">Status</td>
            <td width="16%" align="center" valign="middle">Completed</td>
          </tr>
          <?php
		  	$groupcheck = "no";
		    if(pView($user["type"]))
				$query = "select * from task where status='2' order by group_task desc, date_status desc";
			else
			{
				$query = "select * from task where status='2' and receiver='".$user["id"]."' and group_task='no' order by date_status desc";
				if(verify_group_valid_task())
					$groupcheck ="yes";
			}
			if($groupcheck=="yes")
			{
				$queryx = "select * from task where group_task='yes' and status='2' order by date_status desc";
				if($resultx = mysql_query($queryx))
				{
					if(($num_rowsx = mysql_num_rows($resultx))>0)
					{
						echo "<tr><td colspan='5' align='center' valign='middle' height='25'> >>>>>>>> Group Assignment <<<<<< </td></tr>";
						while($rowgroup = mysql_fetch_array($resultx))
						{
							$count = 1;
                    		$total = 0;
							if(verify_group($rowgroup["id"],$user["id"]))
							{
								$total = $count %2;
								if($total !=0)
									$style = "style='background-color:#e6f882'";
								else
									$style="";
								$exp = explode(" ",$rowgroup["date_status"]);
								$date = $exp[0];
								if(verify_group_valid($rowgroup["id"],$user["id"]))
									$imagenew = $imgnew;
								else
									$imagenew ="";
								$username_t="Groups Task";
								echo "<tr class='rowstyle' $style><td height='27' align='center' valign='middle'>$count</td><td height='27' align='center' valign='middle'>".$username_t."</td><td height='27' align='center' valign='middle'><a href='taskin.php?id=".base64_encode($rowgroup["id"])."' class='link_colorb' title='View Task Information' >".stripslashes($rowgroup["title"])."</a>&nbsp;".$imagenew."</td><td height='27' align='center' valign='middle'>".getTaskStatus($rowgroup["status"])."</td><td height='27' align='center' valign='middle'>".$date."</td></tr>";
								$count++;
							}
						}
						echo "<tr><td colspan='5' align='center' valign='middle'>&nbsp;</td></tr>";
						echo "<tr><td colspan='5' align='center' valign='middle' height='25'> >>>>>>>> Individual Assignment <<<<<< </td></tr>";
					}
				}
			}
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
                        $exp = explode(" ",$rows["date"]);
                        $date = $exp[0];
						if(verify_group_valid($rows["id"],$user["id"]))
							$imagenew = $imgnew;
						else
							$imagenew ="";
						/*$queryss = "select * from task_update where userid='".$user["id"]."' and view_update='no' and task='".$rows["id"]."'";
						if($resultqs = mysql_query($queryss))
						{
							if(($num_rowsqs = mysql_num_rows($resultqs))>0)
								$imagenew = $imgnew;
							else
								$imagenew = "false";
						}*/
						if(empty($rows["receiver"]) && $rows["group_task"]=="yes")
							$username_t="Groups Task";
						else
						{
							$checkadmin = pView($user["type"]);
							if($checkadmin)
								$username_t = "<a class='link_colorb' title='View User Information' href='settingm.php?id=".base64_encode($rows["receiver"])."'>".getName($rows["receiver"])."</a>";
							else
								$username_t ="<a class='link_colorb' title='View User Information' href='settingm.php?id=".base64_encode($rows["receiver"])."'>".getUserName($rows["receiver"])."</a>";
						}
                        echo "<tr class='rowstyle' $style><td height='27' align='center' valign='middle'>$count</td><td height='27' align='center' valign='middle'>".$username_t."</td><td height='27' align='center' valign='middle'><a href='taskin.php?id=".base64_encode($rows["id"])."' class='link_colorb' title='View Task Information' >".stripslashes($rows["title"])."</a>&nbsp;".$imagenew."</td><td height='27' align='center' valign='middle'>".getTaskStatus($rows["status"])."</td><td height='27' align='center' valign='middle'>".$date."</td></tr>";
                        $count++;
                    }
                }
                else
                    echo "<tr class='rowstyleno'><td colspan='5' align='center' valign='middle'>No Task Completed</td></tr>";
            }
            else
                echo "<tr class='rowstyleno'><td colspan='5' align='center' valign='middle'>No Task Completed</td></tr>";
          ?>
        </table>
      </div>
      <br/><br/>
      <div style="width:850px; padding-left:50px;">
      <span class="task_view_title">Task Pending</span><br/>
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr style="background-color:#014681; color:#FFF">
            <td width="7%">&nbsp;</td>
            <td width="22%" align="center" valign="middle">Username</td>
            <td width="36%" align="center" valign="middle">Task</td>
            <td width="19%" align="center" valign="middle">Status</td>
            <td width="16%" align="center" valign="middle">Created</td>
          </tr>
          <?php
			if(pView($user["type"]))
				$query = "select * from task where status='1' order by group_task desc, date desc";
			else
			{
            	$query = "select * from task where status='1' and receiver='".$user["id"]."' order by datec desc";
				if(verify_group_valid_task())
					$groupcheck ="yes";
			}
			if($groupcheck=="yes")
			{
				$queryx = "select * from task where group_task='yes' and status='1' order by datec desc";
				if($resultx = mysql_query($queryx))
				{
					if(($num_rowsx = mysql_num_rows($resultx))>0)
					{
						echo "<tr><td colspan='5' align='center' valign='middle' height='25'> >>>>>>>> Group Assignment <<<<<< </td></tr>";
						while($rowgroup = mysql_fetch_array($resultx))
						{
							$count = 1;
                    		$total = 0;
							if(verify_group($rowgroup["id"],$user["id"]))
							{
								$total = $count %2;
								if($total !=0)
									$style = "style='background-color:#e6f882'";
								else
									$style="";
								$exp = explode(" ",$rowgroup["date"]);
								$date = $exp[0];
								if(verify_group_valid($rowgroup["id"],$user["id"]))
									$imagenew = $imgnew;
								else
									$imagenew ="";
								$username_t="Groups Task";
								echo "<tr class='rowstyle' $style><td height='27' align='center' valign='middle'>$count</td><td height='27' align='center' valign='middle'>".$username_t."</td><td height='27' align='center' valign='middle'><a href='taskin.php?id=".base64_encode($rowgroup["id"])."' class='link_colorb' title='View Task Information' >".stripslashes($rowgroup["title"])."</a>&nbsp;".$imagenew."</td><td height='27' align='center' valign='middle'>".getTaskStatus($rowgroup["status"])."</td><td height='27' align='center' valign='middle'>".$date."</td></tr>";
								$count++;
							}
						}
						echo "<tr><td colspan='5' align='center' valign='middle'>&nbsp;</td></tr>";
						echo "<tr><td colspan='5' align='center' valign='middle' height='25'> >>>>>>>> Individual Assignment <<<<<< </td></tr>";
					}
				}
			}
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
                        $exp = explode(" ",$rows["date"]);
                        $date = $exp[0];
						if(verify_group_valid($rows["id"],$user["id"]))
							$imagenew = $imgnew;
						else
							$imagenew ="";
						if(empty($rows["receiver"]) && $rows["group_task"]=="yes")
							$username_t="Groups Task";
						else
						{
							$checkadmin = pView($user["type"]);
							if($checkadmin)
								$username_t = "<a class='link_colorb' title='View User Information' href='settingm.php?id=".base64_encode($rows["receiver"])."' >".getName($rows["receiver"])."</a>";
							else
								$username_t = "<a class='link_colorb' title='View User Information' href='settingm.php?id=".base64_encode($rows["receiver"])."' >".getUserName($rows["receiver"])."</a>";
						}
                        echo "<tr class='rowstyle' $style><td height='27' align='center' valign='middle'>$count</td><td height='27' align='center' valign='middle'>".$username_t."</td><td height='27' align='center' valign='middle'><a href='taskin.php?id=".base64_encode($rows["id"])."' class='link_colorb' title='View Task Information'>".stripslashes($rows["title"])."</a>&nbsp;$imagenew</td><td height='27' align='center' valign='middle'>".getTaskStatus($rows["status"])."</td><td height='27' align='center' valign='middle'>".$date."</td></tr>";
                        $count++;
                    }
                }
                else
                    echo "<tr class='rowstyleno'><td colspan='5' align='center' valign='middle'>No Task Pending Found</td></tr>";
            }
            else
                echo "<tr class='rowstyleno'><td colspan='5' align='center' valign='middle'>No Task Pending Found</td></tr>";
          ?>
        </table>
      </div>
      <br/><br/>
      <div style="width:850px; padding-left:50px;">
      <span class="task_view_title">Task Miscellaneous</span><br/>
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr style="background-color:#014681; color:#FFF">
            <td width="7%">&nbsp;</td>
            <td width="22%" align="center" valign="middle">Username</td>
            <td width="36%" align="center" valign="middle">Task</td>
            <td width="19%" align="center" valign="middle">Status</td>
            <td width="16%" align="center" valign="middle">Created</td>
          </tr>
          <?php
			if(pView($user["type"]))
				$query = "select * from task where status !='1' and status !='2' order by group_task desc, date desc";
			else
			{
				$query = "select * from task where status !='1' and status !='2' and receiver='".$user["id"]."' order by date_status desc";
				if(verify_group_valid_task())
					$groupcheck ="yes";
			}
			if($groupcheck=="yes")
			{
				$queryx = "select * from task where group_task='yes' and status !='1' and status !='2' order by datec desc";
				if($resultx = mysql_query($queryx))
				{
					if(($num_rowsx = mysql_num_rows($resultx))>0)
					{
						echo "<tr><td colspan='5' align='center' valign='middle' height='25'> >>>>>>>> Group Assignment <<<<<< </td></tr>";
						while($rowgroup = mysql_fetch_array($resultx))
						{
							$count = 1;
                    		$total = 0;
							if(verify_group($rowgroup["id"],$user["id"]))
							{
								$total = $count %2;
								if($total !=0)
									$style = "style='background-color:#e6f882'";
								else
									$style="";
								$exp = explode(" ",$rowgroup["date_status"]);
								$date = $exp[0];
								if(verify_group_valid($rowgroup["id"],$user["id"]))
									$imagenew = $imgnew;
								else
									$imagenew ="";
								$username_t="Groups Task";
								echo "<tr class='rowstyle' $style><td height='27' align='center' valign='middle'>$count</td><td height='27' align='center' valign='middle'>".$username_t."</td><td height='27' align='center' valign='middle'><a href='taskin.php?id=".base64_encode($rowgroup["id"])."' class='link_colorb' title='View Task Information' >".stripslashes($rowgroup["title"])."</a>&nbsp;".$imagenew."</td><td height='27' align='center' valign='middle'>".getTaskStatus($rowgroup["status"])."</td><td height='27' align='center' valign='middle'>".$date."</td></tr>";
								$count++;
							}
						}
						echo "<tr><td colspan='5' align='center' valign='middle'>&nbsp;</td></tr>";
						echo "<tr><td colspan='5' align='center' valign='middle' height='25'> >>>>>>>> Individual Assignment <<<<<< </td></tr>";
					}
				}
			}
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
                        $exp = explode(" ",$rows["date"]);
                        $date = $exp[0];
						if(verify_group_valid($rows["id"],$user["id"]))
							$imagenew = $imgnew;
						else
							$imagenew ="";
						if(empty($rows["receiver"]) && $rows["group_task"]=="yes")
							$username_t="Groups Task";
						else
						{
							$checkadmin = pView($user["type"]);
							if($checkadmin)
								$username_t = "<a class='link_colorb' title='View User Information' href='settingm.php?id=".base64_encode($rows["receiver"])."'>".getName($rows["receiver"])."</a>";
							else
								$username_t = "<a class='link_colorb' title='View User Information' href='settingm.php?id=".base64_encode($rows["receiver"])."'>".getUserName($rows["receiver"])."</a>";
						}
                        echo "<tr class='rowstyle' $style><td height='27' align='center' valign='middle'>$count</td><td height='27' align='center' valign='middle'>".$username_t."</td><td height='27' align='center' valign='middle'><a href='taskin.php?id=".base64_encode($rows["id"])."' class='link_colorb' title='View Task Information'>".stripslashes($rows["title"])."</a>&nbsp;$imagenew</td><td height='27' align='center' valign='middle'>".getTaskStatus($rows["status"])."</td><td height='27' align='center' valign='middle'>".$date."</td></tr>";
                        $count++;
                    }
                }
                else
                    echo "<tr class='rowstyleno'><td colspan='5' align='center' valign='middle'>No Task Miscelaneaus Found</td></tr>";
            }
            else
                echo "<tr class='rowstyleno'><td colspan='5' align='center' valign='middle'>No Task Miscelaneaus Found</td></tr>";
          ?>
        </table>
      </div>
      <?php
	  }
	  else
	  {
	  ?>
      <br/><br/>
      <div style="width:850px; padding-left:50px;">
      <span class="task_view_title">Task <?php echo $viewtitle; ?></span><br/>
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr style="background-color:#014681; color:#FFF">
            <td width="7%">&nbsp;</td>
            <td width="22%" align="center" valign="middle">Username</td>
            <td width="36%" align="center" valign="middle">Task</td>
            <td width="19%" align="center" valign="middle">Status</td>
            <td width="16%" align="center" valign="middle">Created</td>
          </tr>
          <?php
  			if($groupcheck=="yes")
			{
				if($resultx = mysql_query($queryx))
				{
					if(($num_rowsx = mysql_num_rows($resultx))>0)
					{
						echo "<tr><td colspan='5' align='center' valign='middle' height='25'> >>>>>>>> Group Assignment <<<<<< </td></tr>";
						while($rowgroup = mysql_fetch_array($resultx))
						{
							$count = 1;
                    		$total = 0;
							if(verify_group($rowgroup["id"],$user["id"]))
							{
								$total = $count %2;
								if($total !=0)
									$style = "style='background-color:#e6f882'";
								else
									$style="";
								$exp = explode(" ",$rowgroup["date_status"]);
								$date = $exp[0];
								if(verify_group_valid($rowgroup["id"],$user["id"]))
									$imagenew = $imgnew;
								else
									$imagenew ="";
								$username_t="Groups Task";
								echo "<tr class='rowstyle' $style><td height='27' align='center' valign='middle'>$count</td><td height='27' align='center' valign='middle'>".$username_t."</td><td height='27' align='center' valign='middle'><a href='taskin.php?id=".base64_encode($rowgroup["id"])."' class='link_colorb' title='View Task Information' >".stripslashes($rowgroup["title"])."</a>&nbsp;".$imagenew."</td><td height='27' align='center' valign='middle'>".getTaskStatus($rowgroup["status"])."</td><td height='27' align='center' valign='middle'>".$date."</td></tr>";
								$count++;
							}
						}
						echo "<tr><td colspan='5' align='center' valign='middle'>&nbsp;</td></tr>";
						echo "<tr><td colspan='5' align='center' valign='middle' height='25'> >>>>>>>> Individual Assignment <<<<<< </td></tr>";
					}
				}
			}
            if($result = mysql_query($querys))
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
                        $exp = explode(" ",$rows["date"]);
                        $date = $exp[0];
						if(verify_group_valid($rows["sender"],$user["id"]))
							$imagenew = $imgnew;
						else
							$imagenew ="";
						if(empty($rows["receiver"]) && $rows["group_task"]=="yes")
							$username_t="Groups Task";
						else
						{
							$checkadmin = pView($user["type"]);
							if($checkadmin)
								$username_t = "<a class='link_colorb' href='settingm.php?id=".base64_encode($rows["id"])."'>".getName($rows["receiver"])."</a>";
							else
								$username_t = "<a class='link_colorb' href='settingm.php?id=".base64_encode($rows["id"])."'>".getUserName($rows["receiver"])."</a>";
						}
                        echo "<tr class='rowstyle' $style><td height='27' align='center' valign='middle'>$count</td><td height='27' align='center' valign='middle'>".$username_t."</td><td height='27' align='center' valign='middle'><a href='taskin.php?id=".base64_encode($rows["id"])."' class='link_colorb'>".stripslashes($rows["title"])."</a>&nbsp;$imagenew</td><td height='27' align='center' valign='middle'>".getTaskStatus($rows["status"])."</td><td height='27' align='center' valign='middle'>".$date."</td></tr>";
                        $count++;
                    }
                }
                else
                    echo "<tr class='rowstyleno'><td colspan='5' align='center' valign='middle'>No Task Miscelaneaus Found</td></tr>";
            }
            else
                echo "<tr class='rowstyleno'><td colspan='5' align='center' valign='middle'>No Task Miscelaneaus Found</td></tr>";
          ?>
        </table>
      </div>
      <?php
	  }
	  ?>
      </div>
               <?php
		 include "include/config.php";
		 ?>
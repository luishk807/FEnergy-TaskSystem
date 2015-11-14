<?php
session_start();
include "include/config.php";
include "include/function.php";
$id =base64_decode($_REQUEST["id"]);
?>
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <?php
			$queryr = "select *  from task_report where task='".$id."' order by date desc";
			if($resultr = mysql_query($queryr))
			{
				if(($num_rowsr = mysql_num_rows($resultr))>0)
				{
					while($rowsr = mysql_fetch_array($resultr))
					{
						echo "<tr><td width='32%' class='report_date' valign='top'>".fixdate_comp($rowsr["date"])."</td> <td width='68%' valign='top'> From: ".getUserName($rowsr["reporter"])."<br/><span class='report_report'>".stripslashes(nl2br($rowsr["report"]))."</span></td></tr>";
						echo "<tr><td colspan='2' align='left' valign='top'><img src='images/horzlineb.jpg' border='0' /></td></tr>";
					}
				}
				else
					echo "<tr class='rowstylenob'><td colspan='2' align='center'>No Report Found</td></tr>";		
			}
			else
				echo "<tr class='rowstylenob'><td colspan='2' align='center'>No Report Found</td></tr>";
				?>
            </table>
<?php
include "include/unconfig.php";
?>
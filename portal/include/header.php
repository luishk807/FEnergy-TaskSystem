<script type="text/javascript" language="javascript">
function showpop()
{
	showdivpop();
}
<?php
if($popview !="close")
{
	?>
var intervalID = window.setInterval(showpop, 10000);
<?php
}
?>
</script>   
    <div id="body_header">
    	<?php
		echo "<span id='popdiv'>";
		if(isset($_SESSION["taskuser"]))//show pop
		{
			$check1 = false;
			$taskview = $_SESSION["taskuser"];
			$queryview= "select * from task where (receiver='".$taskview["id"]."' and view_receiver='no') or (sender='".$taskview["id"]."' and view_sender='no')";
			if($resultview = mysql_query($queryview))
			{
				if(($num_rows = mysql_num_rows($resultview))>0)
				{
					while($taskiview= mysql_fetch_array($resultview))
					{
						$queryviewb = "select * from task_report where (receiver='".$taskview["id"]."' and view_receiver='no') or (sender='".$taskview["id"]."' and view_sender='no')";
						$check1=true;
						break;
					}
				}
				else
					$checki=false;
			}
			else
				$checki=false;
			if($check1)
			{
					if($popview !="close")
						echo "<div id='newicon' style='position:absolute; top:160px;left:320px'><img src='images/new.png' border='0' alt='New Message' /></div>";
			}
		}
		echo "</span>";
			?>
    	<div id="header_greet"><div id="header_greet_in">Welcome &nbsp;<a title='<?php echo $user["username"]; ?>' href='setting.php' class="username_style"><?php echo  rLongTextc($user["username"]); ?></a><br/><a href='logout.php' class="header_logout">LogOut?</a></div></div>
        <div id="header_menu">
        	<div id="header_menu_in">
            <?php
			$user = $_SESSION["taskuser"];
			if($user["type"] =="1" || $user["type"] =="2")
			{
			?>
        	<a href='home.php' class="header_menu">Home</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href='createtask.php' class="header_menu">Assign Task</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a class="header_menu" href='view.php'>View Tasks</a>
           &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href='viewuser.php' class="header_menu">View Users</a>
            <?php
			}
			else
			{
			?>
            <a href='home.php' class="header_menu">Home</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href='createtask.php' class="header_menu">Assign Task</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a class="header_menu" href='view.php'>View Tasks</a>
            <!--&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a class="header_menu" href=''>Chats</a>-->
            <?Php
			}
			?>
            </div>
        </div>
        <div class="cleardiv"></div>
    </div>
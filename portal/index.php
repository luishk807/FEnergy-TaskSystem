<?php
session_start();
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
<body onload="getlink()">
<div id="main_cont">
	<div id="login_cont_panel">
    	<div id="login_cont">
        	<div id="form">
                    <form action="login.php" method="post" onsubmit="return checkField()">
                    <div id="questions_in">
                        <input type="text" size="50" id="uname" name="uname" />
                        <div id="form_spacer"></div>
                          <input type="password" size="50" id="upass" name="upass"/>
               		</div>
                    <div id="loginmessage">
                    	<div id="loginmessage_in">
                    	<div id="message2" name="message2" class="white_home">
                              <?php
								if(isset($_SESSION["loginresult"]))
								{
									echo $_SESSION["loginresult"];
									unset($_SESSION["loginresult"]);
								}
							 ?>
                        </div>
                        </div>
                     </div>
                      <div id="home_button">
                        <input type="image"  src="images/s2.png" onmouseover="javascript:this.src='images/s2.png';" onmouseout="javascript:this.src='images/s2.png';">
                     </div>
                     <div class="cleardiv"></div>
               </form>
              </div>
        </div>
    </div>
	<div class="clearfooter"></div>
</div>
<?php
include "include/footer.php";
?>
</body>
</html>
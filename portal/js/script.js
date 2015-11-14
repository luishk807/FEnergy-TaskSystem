// JavaScript Document
var xmlHttpReq = null;
function getHttpPost() 
{
	try{			
		xmlHttpReq=new XMLHttpRequest();// Firefox, Opera 8.0+, Safari
	}catch (e)
	{		
		try{			
			xmlHttpReq=new ActiveXObject("Msxml2.XMLHTTP"); // Internet Explorer
		}catch (e)
		{		    
			try{				
				xmlHttpReq=new ActiveXObject("Microsoft.XMLHTTP");	
			}catch (e)
			{				
				alert("No AJAX!?");				
				return false;			
			}		
		}	
	}
}
function showdivpop()
{
	getHttpPost();
    xmlHttpReq.onreadystatechange = function() 
	{
   		if(xmlHttpReq.readyState == 4) 
		{
           document.getElementById('popdiv').innerHTML=xmlHttpReq.responseText;
        }
    }
	var url = "popupdate.php";
	xmlHttpReq.open('POST',url, true);
	//xmlHttp.setRequestHeader("If-Modified-Since", "Sat, 1 Jan 2000 00:00:00 GMT");
    xmlHttpReq.send(null);
}
function showdivpop_view(value)
{
	getHttpPost();
    xmlHttpReq.onreadystatechange = function() 
	{
   		if(xmlHttpReq.readyState == 4) 
		{
           document.getElementById('wholeview').innerHTML=xmlHttpReq.responseText;
        }
    }
	var url = "popupdateview.php?v="+value;
	xmlHttpReq.open('POST',url, true);
	//xmlHttp.setRequestHeader("If-Modified-Since", "Sat, 1 Jan 2000 00:00:00 GMT");
    xmlHttpReq.send(null);
}
function showdivpop_report(value)
{
	getHttpPost();
    xmlHttpReq.onreadystatechange = function() 
	{
   		if(xmlHttpReq.readyState == 4) 
		{
           document.getElementById('report_view').innerHTML=xmlHttpReq.responseText;
        }
    }
	var url = "popupdatereport.php?id="+value;
	xmlHttpReq.open('POST',url, true);
	//xmlHttp.setRequestHeader("If-Modified-Since", "Sat, 1 Jan 2000 00:00:00 GMT");
    xmlHttpReq.send(null);
}
function showdivpop_reportin(value)
{
	getHttpPost();
    xmlHttpReq.onreadystatechange = function() 
	{
   		if(xmlHttpReq.readyState == 4) 
		{
           document.getElementById('whole_report_in').innerHTML=xmlHttpReq.responseText;
        }
    }
	var url = "popupdatereportin.php?id="+value;
	xmlHttpReq.open('POST',url, true);
	//xmlHttp.setRequestHeader("If-Modified-Since", "Sat, 1 Jan 2000 00:00:00 GMT");
    xmlHttpReq.send(null);
}
function changeuserview(value)
{
	if(value=="newuser")
		window.location.href='create.php';
	else
		changeuserviews(value);
}
function changeuserviews(value)
{
	getHttpPost();
    xmlHttpReq.onreadystatechange = function() 
	{
   		if(xmlHttpReq.readyState == 4) 
		{
           document.getElementById('usercont').innerHTML=xmlHttpReq.responseText;
        }
    }
	var url = "popupdateuserview.php?taskview="+value;
	xmlHttpReq.open('POST',url, true);
	//xmlHttp.setRequestHeader("If-Modified-Since", "Sat, 1 Jan 2000 00:00:00 GMT");
    xmlHttpReq.send(null);
}
function errorcheck(task,vars,message)
{
	var color = "#cee838";
	//var color = "";
	var variable = document.getElementById(vars).value;
	if(task=="text")
	{
		if(variable.length==0 || isNaN(variable)==false)
		{
			document.getElementById(vars).style.background=color;
			document.getElementById("message2").innerHTML=message;
			return false;
		}
		else
		{
			document.getElementById(vars).style.background="";
			document.getElementById("message2").innerHTML="";
		}
	}
	if(task=="select")
	{
		if(variable=="0")
		{
			document.getElementById("message2").innerHTML=message;
			return false;
		}
		else
		{
			document.getElementById("message2").innerHTML="";
		}
	}
	else if(task=="number")
	{
		if(variable.length==0 || isNaN(variable)==true)
		{
			document.getElementById(vars).style.background=color;
			document.getElementById("message2").innerHTML=message;
			return false;
		}
		else
		{
			document.getElementById(vars).style.background="";
			document.getElementById("message2").innerHTML="";
		}	
	}
	else if(task=="normal")
	{
		if(variable.length==0)
		{
			document.getElementById(vars).style.background=color;
			document.getElementById("message2").innerHTML=message;
			return false;
		}
		else
		{
			document.getElementById(vars).style.background="";
			document.getElementById("message2").innerHTML="";
		}
	}
	else if(task=="email")
	{
		var email = document.getElementById(vars).value;
		if(email.length !=0)
		{
			if(variable.length==0 || ((variable.indexOf(".")<2) && (variable.indexOf("@")<=0)))
			{
				document.getElementById(vars).style.background=color;
				document.getElementById("message2").innerHTML=message;
				return false;
			}
			else
			{
				document.getElementById(vars).style.background="";
				document.getElementById("message2").innerHTML="";
			}
		}
		else
		{
			document.getElementById(vars).style.background="";
			document.getElementById("message2").innerHTML="";
		}
	}
	else if(task=="emailf")
	{
		var email = document.getElementById(vars).value;
		if(variable.length==0 || ((variable.indexOf(".")<2) && (variable.indexOf("@")<=0)))
		{
			document.getElementById(vars).style.background=color;
			document.getElementById("message2").innerHTML=message;
			return false;
		}
		else
		{
			document.getElementById(vars).style.background="";
			document.getElementById("message2").innerHTML="";
		}
	}
	else if(task=="selects")
	{
		if(document.getElementById(vars).selectedIndex==0)
		{
			document.getElementById("message2").innerHTML=message;
			return false;
		}
		else
		{
			document.getElementById("message2").innerHTML="";
		}
	}
	else if(task=="checksa")
	{
		if(document.getElementById(vars).checked==false)
		{
			document.getElementById("message2").innerHTML=message;
			return false;
		}
		else
		{
			document.getElementById("message2").innerHTML="";
		}
	}
	return true;
}
function preload(images) {
    if (document.images) {
        var i = 0;
        var imageArray = new Array();
        imageArray = images.split(',');
        var imageObj = new Image();
        for(i=0; i<=imageArray.length-1; i++) {
            //document.write('<img src="' + imageArray[i] + '" />');// Write to page (uncomment to check images)
            imageObj.src=images[i];
        }
    }
}
function clearmailform()
{
	document.getElementById("maddtype").selectedIndex=0;
	document.getElementById("maddapt").value="";
	document.getElementById("maddnum").value="";
	document.getElementById("maddsuf").value="";
	document.getElementById("maddst").value="";
	document.getElementById("maddsttype").selectedIndex=0;			   
	document.getElementById("maddstdir").selectedIndex=0;
	document.getElementById("maddcity").value="";
	document.getElementById("maddzip").value="";
	document.getElementById("madddwell").value="";
	document.getElementById("maddocup").value="";
}
function checkField()
{
	//form from the login form
	if(!errorcheck("normal","uname","Please enter username"))
		return false;
	if(!errorcheck("normal","upass","Please enter password"))
		return false;
	return true;
}
function checkFieldc()
{
	//form from the insert a new report form
	if(!errorcheck("normal","ureport","Please Write a Report To Continue"))
		return false;
	return true;
}
function checkFieldb()
{
	//form from the create task form
	var groupselect = document.getElementById("groupselect").value
	var agentselect = document.getElementById("agentselect").value;
	if(groupselect =="no")
	{
		if(agentselect=="no")
		{
			document.getElementById("message2").innerHTML="You Need An Agent To Continue";
			return false;
		}
		else
		{
			if(!errorcheck("selects","agent","Please Choose An Agent"))
				return false;
		}
	}
	if(!errorcheck("normal","title","Please provide a valid subject or title for this task"))
		return false;
	if(!errorcheck("normal","cdate","Please provide a valid date for task completition"))
		return false;
	if(!errorcheck("normal","task","Please provide information about the task"))
		return false;
	return true;
}
function checkFielde()
{
	//form from the edit task form
	if(!errorcheck("selects","agentfrom","Please Choose Task Owner"))
		return false;
	var groupselect = document.getElementById("groupselect").value;
	var agentselect = document.getElementById("agentselect").value;
	if(groupselect =="no")
	{
		if(agentselect=="no")
		{
			document.getElementById("message2").innerHTML="You Need An Agent To Continue";
			return false;
		}
		else
		{
			if(!errorcheck("selects","agent","Please Choose An Agent"))
				return false;
		}
	}
	if(!errorcheck("normal","title","Please provide a valid subject or title for this task"))
		return false;
	if(!errorcheck("selects","taskstatus","Please Choose A Task Status"))
		return false;
	var checkcdate = document.getElementById("changecdates").value;
	if(checkcdate=="yes")
	{
		if(!errorcheck("normal","cdate","Please provide a valid date for task completition"))
		return false;
	}
	if(!errorcheck("normal","task","Please provide information about the task"))
		return false;
	return true;
}
function checkFieldf()
{
	//form from the create user form
	if(!errorcheck("emailf","uemail","Please provide a valid email"))
		return false;
	if(!errorcheck("normal","uname","Please Write A Username"))
		return false;
	var newpass = document.getElementById("newpass").value;
	var renewpass = document.getElementById("renewpass").value;
	if(newpass != renewpass)
	{
		document.getElementById("renewpass").style.background="#cee838";
		document.getElementById("message2").innerHTML="Both Password Must Match";
		return false;
	}
	else
	{
		document.getElementById("renewpass").style.background="";
		document.getElementById("message2").innerHTML="";
	}
	if(!errorcheck("normal","realname","Please provide a valid name"))
		return false;
	if(!errorcheck("normal","utitle","Please provide a title"))
		return false;
		var utype = document.getElementById("utype").value;
	if(utype=="1")
	{
		var confirmx = window.confirm("WARNING!!: YOU ARE ABOUT TO CREATE A SUPER ADMIN. DOING THIS WILL ALLOW EXCLUSIVE ACCESS THAN REGULAR USERS. ARE YOU SURE YOU WANT TO PROCEED?!\r\n\r\nClick Yes To Proceed Or Cancel To Cancel The Process.");
		if(confirmx==false)
			return false;
	}
	return true;
}
function checkFieldd()
{
	//form from the create user form
	if(!errorcheck("emailf","uemail","Please provide a valid email"))
		return false;
	if(!errorcheck("normal","uname","Please Write A Username"))
		return false;
	var changepass = document.getElementById("changepass").value;
	if(changepass=="yes")
	{
		var newpass = document.getElementById("newpass").value;
		var renewpass = document.getElementById("renewpass").value;
		if(newpass != renewpass)
		{
			document.getElementById("renewpass").style.background="#cee838";
			document.getElementById("message2").innerHTML="Both Password Must Match";
			return false;
		}
		else
		{
			document.getElementById("renewpass").style.background="";
			document.getElementById("message2").innerHTML="";
		}
	}
	if(!errorcheck("normal","realname","Please provide a valid name"))
		return false;
	if(!errorcheck("normal","utitle","Please provide a title"))
		return false;
		var utype = document.getElementById("utype").value;
	if(utype=="1")
	{
		var confirmx = window.confirm("WARNING!!: YOU ARE ABOUT TO CREATE A SUPER ADMIN. DOING THIS WILL ALLOW EXCLUSIVE ACCESS THAN REGULAR USERS. ARE YOU SURE YOU WANT TO PROCEED?!\r\n\r\nClick Yes To Proceed Or Cancel To Cancel The Process.");
		if(confirmx==false)
			return false;
	}
	return true;
}
function checkFieldg()
{
	//form from the create user form
	if(!errorcheck("normal","uname","Please Write A Username"))
		return false;
	var changepass = document.getElementById("changepass").value;
	if(changepass=="yes")
	{
		var newpass = document.getElementById("newpass").value;
		var renewpass = document.getElementById("renewpass").value;
		if(newpass != renewpass)
		{
			document.getElementById("renewpass").style.background="#cee838";
			document.getElementById("message2").innerHTML="Both Password Must Match";
			return false;
		}
		else
		{
			document.getElementById("renewpass").style.background="";
			document.getElementById("message2").innerHTML="";
		}
	}
	if(!errorcheck("normal","realname","Please provide a valid name"))
		return false;
	if(!errorcheck("emailf","uemail","Please provide a valid email"))
		return false;
	if(!errorcheck("normal","utitle","Please provide a title"))
		return false;
	return true;
}
function load() 
{
  var map;
  var geoXml;

  if (GBrowserIsCompatible()) 
  {
    map = new GMap2(document.getElementById('map_canvas'));
    map.addControl(new GSmallMapControl());
    map.addControl(new GMapTypeControl());
    geoXml = new GGeoXml('http://www.yourfamilyenergy.com/testsite/testexcel/genkml_v8.php');
    map.addOverlay(geoXml);
    map.setCenter(new GLatLng(47.613976,-122.345467), 13);
  }
}
function changeview(value)
{
	if(value.length >1)
	{
		window.location.href="view.php?v="+value;
	}
}
function changecdatediv()
{
	var checkin = document.getElementById("changecdate").checked;
	if(checkin==true)
	{
		document.getElementById("allowcdate").style.display="block";
		document.getElementById("changecdates").value="yes";
	}
	else
	{
		document.getElementById("allowcdate").style.display="none";
		document.getElementById("changecdates").value="no";
	}
}
function showdaterange(value)
{
	if(value=="yes")
	{
		document.getElementById("datedrangediv").style.display="block";
		document.getElementById("daterange").value="yes";
	}
	else
	{
		document.getElementById("date1").value="";
		document.getElementById("date2").value="";
		document.getElementById("daterange").value="no";
		document.getElementById("datedrangediv").style.display="none";
	}
}
function allowpassword()
{
	var checking = document.getElementById("checkchange").checked;
	if(checking==true)
	{
		document.getElementById("allowpassworddiv").style.display="block";
		document.getElementById("changepass").value="yes";
	}
	else
	{
		document.getElementById("changepass").value="no";
		document.getElementById("newpass").value="";
		document.getElementById("renewpass").value="";
		document.getElementById("allowpassworddiv").style.display="none";
	}
}
function deletetask(tasks,value)
{
	if(tasks=="users")
	{
		var confirmx = window.confirm("WARNING!!: YOU ARE ABOUT TO DELETE THIS USER. ARE YOU SURE YOU WANT TO PROCEED?!\r\n\r\nClick Yes To Proceed Or Cancel To Cancel The Process.");
		if(confirmx==true)
		window.location.href='save.php?task=delete&id='+value;	
	}
	else if(tasks =="tasks")
	{
		var confirmx = window.confirm("WARNING!!: YOU ARE ABOUT TO DELETE THIS TASK AND ITS REPORTS. ARE YOU SURE YOU WANT TO PROCEED?!\r\n\r\nClick Yes To Proceed Or Cancel To Cancel The Process.");
		if(confirmx==true)
		window.location.href="savetask.php?"+value;	
	}
}
function changeGroup()
{
	var check=document.getElementById("groupselect_check").checked;
	if(check==true)
	{
		document.getElementById("indvagent").style.display="block";
		document.getElementById("groupagent").style.display="none";
		document.getElementById("groupselect").value="no";
	}
	else
	{
		document.getElementById("groupselect").value="yes";
		document.getElementById("groupagent").style.display="block";
		document.getElementById("indvagent").style.display="none";
	}
}
function getlink()
{
 var url = document.location.href;
 if(url=="http://www.familyenergymap.com/portal/index.php" || url=="http://www.familyenergymap.com/portal/" || url=="http://familyenergymap.com/portal/" || url=="http://familyenergymap.com/portal/index.php")
 {
	 window.location.href="http://www.familyenergyportal.com";
 }
 else if(url=="http://www.familyenergymap.com/portal/home.php" || url=="http://familyenergymap.com/portal/home.php")
 {
	 window.location.href="http://www.familyenergyportal.com/home.php";
 }
}
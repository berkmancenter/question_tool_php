<?php

/*The following code was written by Dan Cohen; it is a revision of the original question tool copyrighted in 2005 by David Russcol. This code is available under the terms of the GNU Public License; see file LICENSE or email djcohen@fas.harvard.edu for more details.*/

/*This file generates the two-column list of questions by opening the database and creating an xml document-object in memory.*/

require_once('functions.php');							//requires a script containing a lot of useful functions for the question tool
require_once('navbar.php');
connect_to_db();										//defined in functions.php
cookie_check();										//checks for table name cookie; redirects to chooser page is none exists. See functions.php

if ($_POST['pword']){											//creates a global variable for the table name
	$db_pw = mysql_query("SELECT password FROM admins WHERE tablename = '$tablename'", $db);        //gets password from db
	if($db_pw){
		$db_pw = mysql_fetch_object($db_pw);
		$db_pw = $db_pw->password;
	}
	mysql_close($db);
	if ($_POST['pword'] == $db_pw){							//if the submitted password matches
		setcookie('admin_pw', $_POST['pword']);					//set the appropriate cookie
		redirect();								//and redirect
	}else{
		show_form("Incorrect password. Please make sure you are accessing your own instance and try again.");
	}
}else{
	show_form('');
}

function show_form($message){							//prints the form for question entry
  global $tablename;
  print '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<META HTTP-EQUIV="PRAGMA" CONTENT="NO-CACHE" />
<meta content="text/html; charset=iso-8859-1" />
<title>Live Question Tool Admin Login Area</title>
<link rel="stylesheet" type="text/css" href="liststyle.css" />
</head>

<body>
<div id="banner">Question Tool Admin Login Area</div><br/><br/>';

navbar();											//prints the navbar; defined in navbar.php

print '   
<div id="wrapper">
<form action= "login.php" method="post">	
<center>							
<div class="center">
<div class="columnhead">Admin login</div><br/>
' . "<center><b>$message</b></center><br/>" . '
<table width="100%">
<tr><td><b>Enter the password for access to the admin interface of ' . $tablename . ':</b></td>
<td><input type="password" maxlength="10" name="pword"></td></tr>
</table>
<p align="center"><input type="submit" name="login" /></p>
<p align="center"><a href="list.php">Return to the list</a></p></div>';

print'</body></html>';
}
?>

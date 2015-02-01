<?php

/*The following code was written by Dan Cohen; it is a revision of the original question tool copyrighted in 2005 by David Russcol. This code is available under the terms of the GNU Public License; see file LICENSE or email djcohen@fas.harvard.edu for more details.*/

/*If a question has been submitted, this file converts it and other relevant information into data that is saved as an xml document. If not, a form for proposing a question is displayed.*/

require_once('functions.php');						//requires a script containing a lot of useful functions for the question tool
require_once('navbar.php');
cookie_check();										//checks for table name cookie; redirects to chooser page is none exists. See functions.ph
$question = validate($_POST['comment']);			//validate() is defined in functions.php

if (($_POST['submit_check']) && ($question) && (strlen($question)<255)){//checks for submission non-empty question, and length
	connect_to_db();								//defined in functions.php
	process_form($question);
	redirect();										//redirects to list page. Defined in functions.php
}else{
	show_form($question);
}  

function process_form($question){  					//defines several variables, then writes them in the xml log
	global $db, $tablename;						//gets global variables (see functions.php)
	if (!$poster = validate($_POST['poster'])){			//validates poster name using a function from function.php
		$poster = '';}
	if (!evalidate($email = $_POST['email'])){			//validate email address
		$email = '';}
	$ip = getIP();							//get ip address (see... you guessed it... functions.php)
	$timeorder = mktime();							//get date for ordering; # of seconds since Jan 1, 1970
	$votes = 0;										//votes start off at 0 	
	
	mysql_query("INSERT INTO questions (tablename, text, poster, email, ip, timeorder, lasttouch, state, votes) VALUES ('$tablename', '$question', '$poster', '$email', '$ip', '$timeorder', '$timeorder', 'normal', '$votes')", $db);
	
	mysql_close($db);
}

function show_form($question){							//prints the form for question entry
  print '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta content="text/html; charset=iso-8859-1" />
<title>Live Question Tool Proposal Form</title>
<link rel="stylesheet" type="text/css" href="liststyle.css" />
</head>
<body>
<div id="banner">Question Tool Proposal Form</div><br /><br />';

navbar();											//prints the navbar; defined in navbar.php

print '
<div id="wrapper">  
<form action= "propose.php" method="post">
<center>							
<div class="center">
<div class="columnhead">Post a question</div><br/>					
<table width="100%">
<tr><td><b>Please post your question. There is a limit of 255 characters.</b></td>
<td><textarea name="comment" rows = "4" cols="40">' . $question . '</textarea></td></tr>
<td><b>Your name (optional): </b></td><td><input type="text" name="poster" maxlength="30"></td></tr>
<td><b>Your email address (optional): </b></td><td><input type="text" name="email" maxlength = "50"></td></tr>
</table>
<p align="center"><input type="submit" name="submit">
<input type="hidden" name="submit_check" value="1"></p>
<p align="center"><a href="list.php">Return to the list</a></p>

</center>
</form>';
  if (strlen($question) > 255){							//print a message if the character limit was exceeded
    print "<center><br /><br /><b>Please observe the character limit.</b></center>";
print'</body></html>';
	}
}
?>

<?php

/*The following code was written by Dan Cohen; it is a revision of the original question tool copyrighted in 2005 by David Russcol. This code is available under the terms of the GNU Public License; see file LICENSE or email djcohen@fas.harvard.edu for more details.*/

/*This file allows answers for each question to be added to the database.*/

require_once("functions.php");						//requires a script containing a lot of useful functions for the question tool
require_once("navbar.php");
cookie_check();										//checks for table name cookie; redirects to chooser page is none exists. See functions.php
$answer = validate($_POST['comment']);

connect_to_db();									//see functions.php
if (($_POST['submit_check']) && ($answer) && (strlen($answer)<255)){	//checks for submission non-empty question, and length
	process_form($answer);
	mysql_close($db);
	redirect();										//redirects to the main list
}else{
	show_form($answer);								//shows the form with the proposed (and rejected) answer
	mysql_close($db);}

function process_form($answer){  					//defines several variables, then writes them in the xml log
	global $db, $tablename;							//these global variables were created by connect_to_db() and cookie_check(), respectively 
	$ip = getIP();					//get ip address
	$qid = $_GET['qid'];							//get question id
	if (!$poster = validate($_POST['poster'])){		//if the poster has not left a valid name
		$poster = 'Anonymous';}						//call her anonymous
	if (!evalidate($email = $_POST['email'])){		//if no valid email address (see functions.php) is provided, 
		$email = '';}								//give no mailto: link
	
	mysql_query("INSERT INTO answers (text, poster, email, ip, tablename, qid) VALUES ('$answer', '$poster', '$email', '$ip', '$tablename', '$qid')", $db);		//insert answer into database
	
	$new_time = mktime();
	
	mysql_query("UPDATE questions SET lasttouch = \"$new_time\" WHERE qid = \"$qid\"", $db); 		 
	
}

function show_form($answer){						//prints the form for question entry
  $qid = $_GET['qid'];
  global $db;
  $object = mysql_query("SELECT text FROM questions WHERE qid = '$qid'", $db);
  $question = mysql_fetch_object($object);			//get the text of the question to which the user is attempting to reply
  $question = $question->text;
  
  print <<<_HTML_
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta content="text/html; charset=iso-8859-1" />
<title>Live Question Tool Answer Form</title>
<link rel="stylesheet" type="text/css" href="liststyle.css" />
</head>
<body>
<div id="banner">Question Tool Answer Form</div><br/><br/>
_HTML_;

navbar();											//prints the navbar; defined in navbar.php

  print <<<_HTML_
<div id="wrapper">  
<form action= "{$_SERVER[PHP_SELF]}?qid=$qid" method="post">								
<center>
<div class="center">
<div class="columnhead">Post an answer</div><br/>
				
<table width="100%">
<tr><td><b>The question was...</b><br/></td>
<td>$question<br/><br/></td></tr>

<tr><td><b>Please post your answer. There is a limit of 255 characters.</b></td>
<td><textarea name="comment" rows = "4" cols="40">$answer</textarea></td></tr>
<td><b>Your name (optional): </b></td><td><input type="text" name="poster" maxlength="30"></td></tr>
<td><b>Your email address (optional): </b></td><td><input type="text" name="email" maxlength = "50"></td></tr>
</table>
<p align="center"><input type="submit" name="submit">
<input type="hidden" name="submit_check" value="1" /></p>
<p align="center"><a href="list.php">Return to the list</a></p>
</div>
</center>
</form>
</div>
_HTML_;
  if (strlen($answer) > 255){							//print a message if the character limit was exceeded
    print "<br /><br /><center><b>Please observe the character limit.</b></center>";}
print'</body></html>';
  
}
?>

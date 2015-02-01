<?php

/*The following code was written by Dan Cohen; it is a revision of the original question tool copyrighted in 2005 by David Russcol. This code is available under the terms of the GNU Public License; see file LICENSE or email djcohen@fas.harvard.edu for more details.*/

/*This file allows votes for each question to be added to the database. Only one vote for each question is allowed for each ip address.*/

require_once('functions.php');						//requires a script containing a lot of useful functions for the question tool
require_once('navbar.php');

connect_to_db();							//see functions.php
cookie_check();								//detects table (see functions.php)
if (admin_check()){							//if user is logged in as an admin
	$question = validate($_POST['comment']);
	if (($_POST['submit_check']) && ($question) && (strlen($question)<255)){	//checks for submission non-empty question, and length
		process_form($question);
		mysql_close($db);
		redirect();										//redirects to the main list
	}else{
		show_form($question);								//shows the form with the proposed (and rejected) answer
		mysql_close($db);}						//disable the question (or hide it)
}else{
	mysql_close($db);
	no_access();							//if not logged in as admin, deny access
}

	//redirects to the list page. Defined in functions.php
	
function process_form($question){  					//defines several variables, then writes them in the xml log
	global $db, $tablename;							//these global variables were created by connect_to_db() and cookie_check(), respectively 
	$qid = $_GET['qid'];							//get question id
	
	$new_time = mktime();
	
	mysql_query("UPDATE questions SET lasttouch = \"$new_time\", text = \"$question\" WHERE qid = \"$qid\"", $db);
	}		

function show_form($question){
	global $db;
	$qid = $_GET['qid'];							//get question id from url
	$object = mysql_query("SELECT text FROM questions WHERE qid = '$qid'", $db);
	$old_question = mysql_fetch_object($object);			//get the text of the question to which the user is attempting to reply
	$old_question = $old_question->text;
	
	print <<<_HTML_
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta content="text/html; charset=iso-8859-1" />
<title>Live Question Tool Modification Form</title>
<link rel="stylesheet" type="text/css" href="liststyle.css" />
</head>

<body>
<div id="banner">Question Tool Modification Form</div><br/><br/>
_HTML_;

navbar();											//prints the navbar; defined in navbar.php

	print <<<_HTML_
<div id="wrapper">  
<form action= "{$_SERVER[PHP_SELF]}?qid=$qid" method="post">								
<center>
<div class="center">
<div class="columnhead">Modify a question</div><br/>
				
<table width="100%">

<tr><td><b>Please modify the question. There is a limit of 255 characters.</b></td>
<td><textarea name="comment" rows = "4" cols="40">$old_question</textarea></td></tr>
</table>
<p align="center"><input type="submit" name="submit">
<input type="hidden" name="submit_check" value="1" /></p>
<p align="center"><a href="list.php">Return to the list</a></p>
</div>
</center>
</form>
</div>
_HTML_;
  if (strlen($question) > 255){							//print a message if the character limit was exceeded
    print "<br /><br /><center><b>Please observe the character limit.</b></center>";}
print'</body></html>';
  }
	

?>

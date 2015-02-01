<?php
	// focus:php:
	// Present one question, all its answers, and an answer form.
	// No scrolling away or switching columns.

	require_once('functions.php');

	connect_to_db();
	cookie_check();											//checks for table name cookie; redirects to chooser page is none exists. See functions.php

	// these should be full from the 2 prior function calls:
	global $db;
	global $tablename;
?>
<html>

<head>
	<title>Question Tool: Focus</title>
	<link rel="stylesheet" type="text/css" href="liststyle.css"/>
</head>

<body>

<?	$query = "SELECT description FROM admins WHERE tablename='$tablename'";
	$result = mysql_query($query);
	$descr_array = mysql_fetch_assoc($result);
?>
	<div align="center"><?=$descr_array['description']?></div>

<?
	if($_GET['qid'] == 0)
	{
		// No question selected; bounce.
?>

No question selected.  How did you get here??

<?
	}


	else
	{

		if($_POST['submit_check'] == 1) // there's an answer submitted, process it 		'
		{
			$answer = validate($_POST['comment']);
			process_form($answer);
		}

		// Regardless of whether we submitted an answer, re-disp. the question:

		show_question_info($_GET['qid']);

		show_answer_form($_GET['qid']);
	}

// Local Functions:

function show_question_info($qid) // these args are in mktime() format
{

	// recognize the global vars
	global $tablename;

	// get the question list

	$theQuery = "SELECT qid, text, poster, email, tablename, timeorder, lasttouch, state, votes FROM questions " .
		"WHERE qid=$qid ";

	$question_list = mysql_query($theQuery);
	if ($question_list)
	{												//if the question object exists,
		while ($question_array = mysql_fetch_assoc($question_list)) //create an array containing questions
		{
			print "\n<div class='question c3'><div class='votes vc3'>" . $question_array['votes'] . " votes</div> <b>" .
				$question_array['poster'] . ":</b> " .
				$question_array['text'] . " (" .
				date('Y M j H:i', $question_array['timeorder']) . ")<br/>\n";

			$answer_list = mysql_query("SELECT qid, text, poster, email FROM answers WHERE qid=$qid" .
				" ORDER BY aid ASC");
			while($answer = mysql_fetch_assoc($answer_list))
			{
				print "<b>" . $answer['poster'] . ":</b> " . $answer['text'] . "<br/>\n";
			}
			print "\n\n</div>&nbsp;\n";
		}
	}

}

function show_answer_form($qid){						//prints the form for question entry
  global $db;

  print <<<_HTML_
<!-- div id="wrapper" -->
<form action= "{$_SERVER[PHP_SELF]}?qid=$qid" method="post">
<center>
<!-- div class="center" -->
<div class="columnhead">Post an answer</div><br/>

<table width="100%">

<tr><td colspan='2'><b>Please post your answer. There is a limit of 255 characters.</b><br>
<textarea name="comment" rows = "4" cols="50"></textarea></td></tr>
<td align='left'><b>Your name (optional): </b></td><td align='right'><input type="text" name="poster" maxlength="30" value="
_HTML_;

	print $_COOKIE['poster'];

  print <<<_HTML_
"></td></tr>
<td align='left'><b>Your email address (optional): </b></td><td align='right'><input type="text" name="email" maxlength = "50" value="
_HTML_;

	print $_COOKIE['email'];

  print <<<_HTML_
"></td></tr>
</table>
<p align="center"><input type="submit" name="submit">
<input type="hidden" name="submit_check" value="1" /></p>
<!-- /div -->
</center>
</form>
<!-- /div -->
_HTML_;
/*
  if (strlen($answer) > 255){							//print a message if the character limit was exceeded
    print "<br /><br /><center><b>Please observe the character limit.</b></center>";
print'</body></html>';
  }
*/
}

function process_form($answer){  					//defines several variables, then writes them in the xml log
	global $db, $tablename;							//these global variables were created by connect_to_db() and cookie_check(), respectively
	$ip = getIP();					//get ip address
	$qid = $_GET['qid'];							//get question id
	if (!$poster = validate($_POST['poster'])){		//if the poster has not left a valid name
		$quartet = explode('.', $ip);				//call her anonymous, but append a portion of the IP as "quasi-ID"
		$poster = 'Anon-' . $quartet[3];
	} else  {
		// only set the "poster" cookie if we're 					'
		// NOT anonymous:
	}
	if (!evalidate($email = $_POST['email'])){		//if no valid email address (see functions.php) is provided,
		$email = '';}								//give no mailto: link

	mysql_query("INSERT INTO answers (text, poster, email, ip, tablename, qid) VALUES ('$answer', '$poster', '$email', '$ip', '$tablename', '$qid')", $db);		//insert answer into database

	$new_time = mktime();

	mysql_query("UPDATE questions SET lasttouch = \"$new_time\" WHERE qid = \"$qid\"", $db);

}


?>

</body>
</html>


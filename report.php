<?php
	// report:php:
	// Display all questions for a given instance within a given date range.
	//  (date range forthcoming)

	require_once('functions.php');

	connect_to_db();
	//$db = get_db_var();	//get these as local vars because it's a hassle w/ everything being a global.    '

	cookie_check();											//checks for table name cookie; redirects to chooser page is none exists. See functions.php
	//$tablename = get_tablename_var();

	// these should be full now
	global $db;
	global $tablename;
?>
<html>

<head>
	<title>Question Tool: Archive</title>
	<link rel="stylesheet" type="text/css" href="liststyle.css"/>
</head>

<body>
<div id="banner">Question Archive: <?=$tablename?></div> <!-- Later, make dynamic from the DB/functions.php -->

<?	$query = "SELECT description FROM admins WHERE tablename='$tablename'";
	$result = mysql_query($query);
	$descr_array = mysql_fetch_assoc($result);
?>
	<div align="center"><?=$descr_array['description']?></div>

<a href="list.php">[ Return to live Question Tool ]</a><p>

<?
	if($_POST['submit'] != 1)
	{
		// put up the date range form
?>
Enter a date range:
<form method="POST" action='<?=$_SERVER['PHP_SELF']?>' name="dateForm">

<input type="hidden" value="1" name="submit">

<b>Start date</b> Year:<input type="text" name="b_year" value="2007" size="5">
	Month:<input type="text" name="b_mo" value="01" size="3">
	Day:<input type="text" name="b_day" value="02" size="3">

<p>
<b>End date</b> Year:<input type="text" name="e_year" value="2007" size="5">
	Month:<input type="text" name="e_mo" value="01" size="3">
	Day:<input type="text" name="e_day" value="22" size="3">

<input type="submit" value="View Archive">
</form>

<?
	}

	else  // process the supplied date and show the questions
	{
		$begin_archive = mktime(0, 0, 0, $_POST['b_mo'], $_POST['b_day'], $_POST['b_year']);
		$end_archive = mktime(23, 59, 59, $_POST['e_mo'], $_POST['e_day'], $_POST['e_year']);

		show_report_info($begin_archive, $end_archive);
	}

// Functions:

function show_report_info($begin_archive, $end_archive) // these args are in mktime() format
{

	// recognize the global vars
	global $tablename;

	// get the question list

	$theQuery = "SELECT qid, text, poster, email, tablename, timeorder, lasttouch, state, votes FROM questions " .
		"WHERE tablename = '$tablename' AND timeorder > $begin_archive AND timeorder < $end_archive " .
		"ORDER BY timeorder ASC";

	$question_list = mysql_query($theQuery);
	if ($question_list)
	{												//if the question object exists,
		while ($question_array = mysql_fetch_assoc($question_list)) //create an array containing questions
		{
			print "\n<div class='question c3'><div class='votes vc3'>" . $question_array['votes'] . " votes</div> <b>" .
				$question_array['poster'] . ":</b> " .
				$question_array['text'] . " (" .
				date('Y M j H:i', $question_array['timeorder']) . ")<br/>\n";

			$answer_list = mysql_query("SELECT qid, text, poster, email FROM answers WHERE qid=" . $question_array['qid']);
			while($answer = mysql_fetch_assoc($answer_list))
			{
				print "<b>" . $answer['poster'] . ":</b> " . $answer['text'] . "<br/>\n";
			}
			print "\n\n</div>&nbsp;\n";
		}
	}

}

?>

<br clear="all"><a href="list.php">[ Return to live Question Tool ]</a>
</body>
</html>


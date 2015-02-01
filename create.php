<?php

/*The following code was written by Dan Cohen; it is a revision of the original question tool copyrighted in 2005 by David Russcol. This code is available under the terms of the GNU Public License; see file LICENSE or email djcohen@fas.harvard.edu for more details.*/

/*The following script generates an instance of the question tool by creating a database and some tables*/

require_once('functions.php');							//requires a script containing a lot of useful functions for the question tool
require_once('navbar.php');

$tablename = ($_POST['tablename']);						//obtain desired tablename,
$pword1 = $_POST['pword1'];								//password,
$pword2 = $_POST['pword2'];
$refresh_time = $_POST['refresh_time'];					//refresh time,
$threshold = $_POST['threshold'];						//number of 'active' questions,
$description = validate($_POST['description']);			//description,
$new_length = $_POST['new_length'];						//length of time questions remain in red,
$stale_length = $_POST['stale_length'];					//and length of time before which questions appear in grey

if ($tablename){										//if a tablename has been submitted,
	if (tvalidate($tablename)){							//validate it (see functions.php for the pvalidate function)
		if ((pvalidate($pword1)) && ($pword1==$pword2)){	//check that passwords match and validate them
			if (strlen($description) < 255){				//check that the description is under 255 characters
				connect_to_db();
				if (create_new($tablename, $pword1, $refresh_time, $threshold, $description, $new_length, $stale_length)){		//if everything's good and no one has picked this tablename already, create it
					setcookie('tablename', $tablename);					//and set the appropriate table cookie
					setcookie('admin_pw', $_POST['pword1']);			//and admin cookie
					redirect();											//then redirect to list page (see functions.php)
				}else{
					show_form('That name has already been taken. Please choose another.', $description);
				}
			}else{
				show_form('Please observe the instance description character limit.', $description);
			}
		}else{
			show_form('Please enter two valid, matching passwords.', $description);
		}
	}else{
		show_form('Please enter a valid tablename', $description);
	}
}else{
	show_form('','');
}

function show_form($message, $description){							//prints the form for creating an instance
  print '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta content="text/html; charset=iso-8859-1" />
<title>Live Question Tool Creation Area</title>
<link rel="stylesheet" type="text/css" href="liststyle.css" />
</head>

<body>
<div id="banner">Question Tool Creation Area</div><br/><br/>';

navbar();											//prints the navbar; defined in navbar.php

print '
<div id="wrapper">
<form action= "create.php" method="post">	
<center>							
<div class="center">
<div class="columnhead">Create a new instance</div><br/>
' . "<center><b>$message</b></center><br/>" . '
<table width="100%">
<tr><td><b>Name your project (4-30 characters, numbers and letters only):</b></td>
<td><input type= "text" name="tablename" maxlength="30"/></td></tr>
<tr><td>&nbsp;</td><td>&nbsp;</td></tr>
<tr><td><b>Enter a password for admin access to your instance of the question tool (4-10 characters, numbers and letters only):</b></td>
<td><input type="password" maxlength="10" name="pword1"></td></tr>
<tr><td><b>Enter your password again to confirm it.</b></td>
<td><input type="password" maxlength="10" name="pword2"/></td></tr>
<tr><td>&nbsp;</td><td>&nbsp;</td></tr>
<tr><td><b>How often should the question list auto-refresh?</b></td>
<td>Every <select name="refresh_time" size="1">
<option value="15">15 seconds</option>
<option value="30">30 seconds</option>
<option value="45">45 seconds</option>
<option value="60">1 minute</option>
</select></td></tr>
<tr><td><b>Number of "featured" questions:</b></td>
<td><select name="threshold" size="1">
<option value="2">2</option>
<option value="4">4</option>
<option value="6">6</option>
<option value="8">8</option>
</select></td></tr>
<tr><td><b>New questions appear in red text for</b></td>
<td><select name="new_length" size="1">
<option value="30">30 seconds</option>
<option value="60">1 minute</option>
<option value="300">5 minutes</option>
<option value="3600">1 hour</option>
<option value="86400">1 day</option>
<option value="604800">1 week</option>
</select></td></tr>
<tr><td><b>Old questions appear in grey text after</b></td>
<td><select name="stale_length" size="1">
<option value="900">15 minutes</option>
<option value="1800">30 minutes</option>
<option value="3600">1 hour</option>
<option value="86400">1 day</option>
<option value="604800">1 week</option>
<option value="2592000">1 month</option>
<option value="31557600">1 year</option>
</select></td></tr>
<tr><td>&nbsp;</td><td>&nbsp;</td></tr>
<tr><td><i>(Optional)</i> <b>Write a description of this instance to appear at the top of the list page. Please keep it under 255 characters.</b></td>
<td><textarea name="description" rows = "4" cols="30">' . $description . '</textarea></td></tr>
<tr><td>&nbsp;</td><td>&nbsp;</td></tr>
</table>
<p align="center"><input type="submit" name="create" /></p>
</div>';

print'</body></html>';
}

function create_new($tablename, $password, $refresh_time, $threshold, $description, $new_length, $stale_length){
	global $db;
	
	//the following queries all set up tables in the pre-existing database if they do not exist yet.

	mysql_query("CREATE TABLE IF NOT EXISTS questions(
	qid INTEGER PRIMARY KEY AUTO_INCREMENT,	
	tablename VARCHAR(30), 
	text VARCHAR(255),
	poster VARCHAR(30),
	email VARCHAR(50),
	ip VARCHAR(15),  
	timeorder INT,
	lasttouch INT,
	state VARCHAR (15),
	votes INT)", $db);
	

	mysql_query("CREATE TABLE IF NOT EXISTS voters(
	qid INTEGER,
	tablename VARCHAR(30),
	ip VARCHAR(16), 
	primary key (qid, ip))", $db);
	
	mysql_query("CREATE TABLE IF NOT EXISTS answers(
	aid INTEGER PRIMARY KEY AUTO_INCREMENT,
	qid INTEGER,
	tablename VARCHAR(30),
	poster VARCHAR(30),
	email VARCHAR(50),
	text VARCHAR(255), 
	ip VARCHAR(15))", $db);
	
	mysql_query("CREATE TABLE IF NOT EXISTS admins(
	tablename VARCHAR(30),
	refresh_time INTEGER,
	threshold INTEGER,
	new_length INTEGER,
	stale_length INTEGER,
	time_created TIMESTAMP,
	description VARCHAR(255),
	password VARCHAR(20))", $db);

	//this checks to see if the user is attempting to create a question tool instance with a name that's already been taken. Think AIM screen names.
	$resource = mysql_query("SELECT DISTINCT tablename FROM questions", $db);
	while ($row = mysql_fetch_assoc($resource)){
		$already_used = $row['tablename'];
		if ($row['tablename'] == $tablename){
			mysql_close($db);
			return 0;
		}
	}
	
	//if the name is indeed unique, a "welcome" post is used to start off the list and establish the table name in the list of questions
	mysql_query("INSERT INTO admins (tablename, refresh_time, threshold, new_length, stale_length, description, password) VALUES ('$tablename', '$refresh_time', '$threshold', '$new_length', '$stale_length', '$description', '$password')", $db);
	
	$timeorder = mktime();			//get a timestamp
	
	mysql_query("INSERT INTO questions (tablename, text, poster, timeorder, lasttouch, state, votes) VALUES ('$tablename', 'Welcome to the live question tool. Feel free to post questions. Vote by clicking on the votes box.', 'the system', '$timeorder', '$timeorder', 'normal', '$votes')", $db);

	mysql_close($db);
	return 1;
}

?>

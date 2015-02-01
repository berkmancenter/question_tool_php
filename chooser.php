<?php

/*The following code was written by Dan Cohen; it is a revision of the original question tool copyrighted in 2005 by David Russcol. This code is available under the terms of the GNU Public License; see file LICENSE or email djcohen@fas.harvard.edu for more details.*/

/*This script uses a form to ask the user which instance of the question tool he would like to see. After the form is submitted, a cookie with the appropriate table name is set and the user is redirected to the list page.*/


require_once('functions.php');						//requires a script containing a lot of useful functions for the question tool
require_once('navbar.php');
connect_to_db();									//defined in functions.php

$tablename = $_COOKIE['tablename'];					//creates a global variable for the table name
//if (!$tablename){
//	$table_resource = mysql_query("SELECT DISTINCT tablename, time_created FROM admins", $db);
//	if($table_resource){
//		$first_table = mysql_fetch_assoc($table_resource);
//		$top_time = $first_table[time_created];
//		$newest_table = $first_table[tablename];
//		while ($tables = mysql_fetch_assoc($table_resource)){
//			if ($tables[time_created] > $top_time){
//				$top_time = $tables[time_created];
//				$newest_table = $tables[tablename];
//			}
//		}
//	setcookie('tablename', $newest_table);
//	redirect();
//	}
//}

if ($_POST['tablename']){							//if the user has selected some tablename,
	setcookie('tablename', $_POST['tablename']);	//give her the corresponding cookie
	redirect();										//redirects to list page. Defined in functions.php
}else{
	show_form();}

function show_form(){								//prints the form for question entry;
	global $db;
	print '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta content="text/html; charset=iso-8859-1" />
<META HTTP-EQUIV="PRAGMA" CONTENT="NO-CACHE" />
<title>Live Question Tool Chooser</title>
<link rel="stylesheet" type="text/css" href="liststyle.css" />
</head>
<body>
<div id="banner">Question Tool Instance Chooser</div><br/><br/>';

navbar();											//prints the navbar; defined in navbar.php
print '
<div id="wrapper" style="text-align: center;">
<center>					
<div class="center">
<div class="columnhead">Choose an instance</div><br/>					
<form action= "chooser.php" method="post">	
<table width="100%">								
<tr><td><b>Choose which instance of the question tool you would like to see:</b></td>
<td><select name="tablename" size="1">';
$resource = mysql_query("SELECT DISTINCT tablename FROM questions", $db);	//obtains all distinct table names
while ($row = mysql_fetch_assoc($resource)){		//creates an array for each row of the database resource obtained earlier
	print "<option value = \"{$row['tablename']}\">{$row['tablename']}</option>";	//prints each tablename in a select menu
	}
print '</select></td></tr></table>
<p align="center"><input type="submit" name="submit" /></p>
<input type="hidden" name="submit_check" value="1" />
</form>
<div class="columnhead">OR</div><br/>
<p style="text-align:center;"><a href="create.php">Create a new instance</a></p>	
<p align="center"><a href="list.php">Return to the list</a></p>
</div>
</center>
</div>
</body></html>';
}

?>

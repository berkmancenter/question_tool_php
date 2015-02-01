<?php

/*The following code was written by Dan Cohen; it is a revision of the original question tool copyrighted in 2005 by David Russcol. This code is available under the terms of the GNU Public License; see file LICENSE or email djcohen@fas.harvard.edu for more details.*/

/*This deletes all the records associated with some instance from the database.*/

require_once('functions.php');						//requires a script containing a lot of useful functions for the question tool
connect_to_db();							//see functions.php
cookie_check();								//detect correct table (see functions.php)
if (admin_check()){							//if user is logged in as an admin
	if ($_POST['delete']){						//if user hit 'delete'
		delete_instance();					//delete the instance
		mysql_close($db);					//close the database
		finished_message('deleted');				//print the 'deleted' message
	}elseif($_POST['save']){					//if user hit 'save' (note: not yet added)
		save_list($tablename);					//save the table (function to be written)
		mysql_close($db);					//and close the database
		finished_message('saved');				//print the 'saved' message
	}else{
		show_form();}	
}else{
	mysql_close($db);						//if incorrect user, deny access
	no_access();
}





function delete_instance(){
	global $tablename, $db;
	mysql_query("DELETE FROM admins WHERE tablename = '$tablename'", $db);
	mysql_query("DELETE FROM answers WHERE tablename = '$tablename'", $db);
	mysql_query("DELETE FROM questions WHERE tablename = '$tablename'", $db);
	mysql_query("DELETE FROM voters WHERE tablename = '$tablename'", $db);
	return;
	}
	
function save_list(){
}

function show_form(){
global $tablename;
print <<<_HTML_
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta content="text/html; charset=iso-8859-1" />
<title>Live Question Tool Instance Delete</title>
<link rel="stylesheet" type="text/css" href="liststyle.css" />
<div id="banner">Question Tool Instance Delete</div><br/><br/>
</head>

<body>
   
<div id="wrapper">						
<center>
<div class="center">
<div class="columnhead">Delete this instance</div><br/>
<form method="post" action="close.php">
<table>
<tr><td><b>Clicking the button at right will delete this instance ($tablename) from the database. You will not be able to get your data back, so make a record of it first.</b></td>
<td><input type="submit" name="delete" value="Delete" /></td></tr></table>
</form>
<br/>

<p style="text-align: center;"><a href="list.php">Return to the list</a></p>
</div>
</center>
</body></html>
_HTML_;
}
function finished_message($message){
print <<<_HTML_
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta content="text/html; charset=iso-8859-1" />
<meta http-equiv="PRAGMA" content="NO-CACHE" />
<title>Live Question Tool Instance Close</title>
<link rel="stylesheet" type="text/css" href="liststyle.css" />
<div id="banner">Question Tool Instance Close</div><br/><br/>
</head>

<body>
   
<div id="wrapper" style="text-align: center;">
<center>					
<div class="center" style="text-align: center;">
<div class="columnhead">Your instance has been $message.</div><br/>

<p style="text-align: center;"><a href="chooser.php">Look at a different instance</a> :: 
<a href="create.php">Create a new instance</a></p>
</div>
</center>
</div>
</body></html>
_HTML_;
}

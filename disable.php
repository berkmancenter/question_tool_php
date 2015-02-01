<?php

/*The following code was written by Dan Cohen; it is a revision of the original question tool copyrighted in 2005 by David Russcol. This code is available under the terms of the GNU Public License; see file LICENSE or email djcohen@fas.harvard.edu for more details.*/

/*This file allows votes for each question to be added to the database. Only one vote for each question is allowed for each ip address.*/

require_once('functions.php');						//requires a script containing a lot of useful functions for the question tool
connect_to_db();							//see functions.php
cookie_check();								//detects table (see functions.php)
if (admin_check()){							//if user is logged in as an admin
	disable_question();						//disable the question (or hide it)
	mysql_close($db);						
	redirect();							//redirect to list page (see functions.php)
}else{
	mysql_close($db);
	no_access();							//if not logged in as admin, deny access
}

	//redirects to the list page. Defined in functions.php
	

function disable_question(){
	global $db;
	$qid = $_GET['qid'];							//get question id from url
	$query_disable = "UPDATE questions SET state = 'disabled' WHERE qid = \"$qid\""; 		 
	mysql_query("$query_disable", $db);
}

?>

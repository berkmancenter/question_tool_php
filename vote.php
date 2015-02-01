<?php

/*The following code was written by Dan Cohen; it is a revision of the original question tool copyrighted in 2005 by David Russcol. This code is available under the terms of the GNU Public License; see file LICENSE or email djcohen@fas.harvard.edu for more details.*/

/*This file allows votes for each question to be added to the database. Only one vote for each question is allowed for each ip address.*/

require_once('functions.php');						//requires a script containing a lot of useful functions for the question tool
cookie_check();								//get tablename cookie (see functions.php)
connect_to_db();							//see functions.php
process_vote();	
mysql_close($db);
redirect();								//redirects to the list page. Defined in functions.php

function process_vote(){
	global $db, $tablename;
	$qid = $_GET['qid'];						//get question id from url
	$ip = getIP();							//see functions.php
	$resource = mysql_query("SELECT * FROM voters WHERE qid = '$qid' AND ip = '$ip'", $db);	//accesses voters table
	if(!mysql_fetch_assoc($resource)){							//if ip hasn't already voted
	  	$new_time = mktime();
		//update when question was 'last touched'
		$query_questions = "UPDATE questions SET lasttouch = \"$new_time\", votes = votes + 1 WHERE qid = \"$qid\""; 		 
		//update voters table
		$query_voters = "INSERT INTO voters (qid, ip, tablename) VALUES ('$qid', '$ip', '$tablename')";
		mysql_query("$query_questions", $db);
		mysql_query("$query_voters", $db);
	}
}
	
?>

<?php

/*The following code was written by Dan Cohen; it is a revision of the original question tool copyrighted in 2005 by David Russcol. This code is available under the terms of the GNU Public License; see file LICENSE or email djcohen@fas.harvard.edu for more details.*/

/*This file allows votes for each question to be added to the database. Only one vote for each question is allowed for each ip address.*/

require_once('functions.php');						//requires a script containing a lot of useful functions for the question tool
connect_to_db();							//see functions.php
cookie_check();								//gets tablename cookie; see functions.php
if (admin_check()){							//if user is logged in
	unhide();							//unhide questions as requested
	mysql_close($db);
	redirect();
}else{
	mysql_close($db);
	no_access();							//sorry, no access page loads (see functions.php)
}

function unhide(){
	global $db, $tablename;
		$query = "UPDATE questions SET state='normal' WHERE tablename = \"$tablename\""; 		 
		mysql_query("$query", $db);
	//}
}
	
?>

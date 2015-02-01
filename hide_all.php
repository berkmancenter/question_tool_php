<?php

/*The following code was written by Dan Cohen; it is a revision of the original question tool copyrighted in 2005 by David Russcol. This code is available under the terms of the GNU Public License; see file LICENSE or email djcohen@fas.harvard.edu for more details.*/

require_once('functions.php');						//requires a script containing a lot of useful functions for the question tool
connect_to_db();							//see functions.php
cookie_check();								//gets tablename cookie; see functions.php
if (admin_check()){							//if user is logged in
	do_hide();							//hide questions as requested
	mysql_close($db);
	redirect();
}else{
	mysql_close($db);
	no_access();							//sorry, no access page loads (see functions.php)
}

function do_hide(){
	global $db, $tablename;
		$query = "UPDATE questions SET state='disabled' WHERE tablename = \"$tablename\"";
		mysql_query("$query", $db);
	//}
}

?>

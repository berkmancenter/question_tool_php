<?php

/*The following code was written by Dan Cohen; it is a revision of the original question tool copyrighted in 2005 by David Russcol. This code is available under the terms of the GNU Public License; see file LICENSE or email djcohen@fas.harvard.edu for more details.*/

/*This file generates the two-column list of questions by opening the database and creating an xml document-object in memory.*/

require_once('functions.php');							//requires a script containing a lot of useful functions for the question tool
cookie_check();										//checks for table name cookie; redirects to chooser page is none exists. See functions.php

if (admin_check){
	setcookie('admin_pw', '');						//gets rid of admin cookie
	}
	
redirect();

?>

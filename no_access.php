<?php

/*The following code was written by Dan Cohen; it is a revision of the original question tool copyrighted in 2005 by David Russcol. This code is available under the terms of the GNU Public License; see file LICENSE or email djcohen@fas.harvard.edu for more details.*/

/*The following script generates an instance of the question tool by creating a database and some tables*/

require_once('functions.php');							//requires a script containing a lot of useful functions for the question tool
require_once('navbar.php');
cookie_check();									//checks for tablename cookie (see functions.php)

print '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta content="text/html; charset=iso-8859-1" />
<title>Live Question Tool Error</title>
<link rel="stylesheet" type="text/css" href="liststyle.css" />
</head>

<body>
<div id="banner">Question Tool Error</div><br/><br/>
';
navbar();											//prints the navbar; defined in navbar.php

print '
<div id="wrapper">	
<center>							
<div class="center">
<div class="columnhead">Sorry, you do not have access to this page or link.</div><br/>
<center><b>You must be logged in to '. $tablename .' as an administrator to view this material.</b><br/><br/>
<a href="login.php">Login to Admin Interface</a> :: <a href="list.php">Return to the list</a>
</center><br/>
</div>';

print'</body></html>';

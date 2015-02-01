<?php

require_once("navbar.php");
print <<<_HTML_
	<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
	"http://www.w3.org/TR/html4/loose.dtd">
	<html>
	<head>
	<meta content="text/html; charset=iso-8859-1" />
	<title>Live Question Tool Credits</title>
	<link rel="stylesheet" type="text/css" href="liststyle.css" />
	</head>
	<body>
	<div id="banner">Question Tool Credits</div><br/><br/>
_HTML_;

navbar();											//prints the navbar; defined in navbar.php

print <<<_HTML_
	<div id="wrapper">							
	<center>
	<div class="center">
	<br/>
	<p>The question tool was conceived by Professor Jonathan Zittrain and first coded by David Russcol. This version, published in August of 2006, was coded by Dan Cohen. Additional thanks and huge props to Team Nesson and the folks at the Berkman Center, and especially to Denise Grey and Ken Martin and everyone at Harvard Law School ITS.
	</p>


	<p>This software is available under the GNU Public License.<br/><br/>
	<a href="list.php">Return to the list</a></p>
	</div>
	</center>
	</body></html>
_HTML_;

?>
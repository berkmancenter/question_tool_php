<?php

/*The following code was written by Dan Cohen; it is a revision of the original question tool copyrighted in 2005 by David Russcol. This code is available under the terms of the GNU Public License; see file LICENSE or email djcohen@fas.harvard.edu for more details.*/

/*This script contains the content used in the banner of the question tool.*/

function banner(){
	print <<<_HTML_
	<div id="nav">
	<ul>
	<li><a href="create.php">Create Instance</a></li>
	<li><a href="credits.html">QTool Credits</a></li>
	</ul>
	</div>
_HTML_;
	}
	
?>
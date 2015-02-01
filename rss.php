<?php

/*The following code was written by Dan Cohen; it is a revision of the original question tool copyrighted in 2005 by David Russcol. This code is available under the terms of the GNU Public License; see file LICENSE or email djcohen@fas.harvard.edu for more details.*/

/*This file generates rss-approriate xml from the database.*/

require_once('functions.php');							//requires a script containing a lot of useful functions for the question tool
connect_to_db();										//defined in functions.php
$tablename = $_GET['tablename'];						//checks for table name cookie; redirects to chooser page is none exists. See functions.php
$arrays = create_doc();
$doc = $arrays[0];
$entries = $arrays[1];									//create an xml document. Defined in functions.php
$arrays = get_list_info();
$question_arrays = $arrays[0];
$answer_arrays = $arrays[1];
$threshold = $arrays[2];
if ($question_arrays){
	$stats = get_statistics();
	$stdev = $stats[0];
	$average = $stats[1];
	$doc = create_xml($question_arrays, $answer_arrays, $stdev, $average, $doc, $entries);}
mysql_close($db);										//close the database
output_xml($doc, 'rss_xslt.xml');						//output the xml using the appropriate stylesheet. Defined in functions.php

?>
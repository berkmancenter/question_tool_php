<?php

/*The following code was written by Dan Cohen; it is a revision of the original question tool copyrighted in 2005 by David Russcol. This code is available under the terms of the GNU Public License; see file LICENSE or email djcohen@fas.harvard.edu for more details.*/

/*This script contains several functions that are used multiple times in the question tool.*/

/* line 11, 14 and 265 need to be edited to appropriate site values */

function connect_to_db(){
	global $db;														//creates a global variable for the database link
	$db = mysql_connect('db.hostname.or.ip','db.user','db.user.pw');				//enter host name, user name, and password here (also in create.php)
	if (!$db) {
		die('Could not connect: ' . mysql_error());}
	mysql_select_db("db.name", $db);}								//selects the questions database

function get_db_var(){
	global $db;

	return $db;
}

function validate($input){
	$output = stripslashes(strip_tags(trim($input), '<a>'));  				//remove html and unwanted formatting from the question
	$output = str_replace('&', 'and', $output);						//sanitize input with '&'
	$output = str_replace('$', "\$", $output);						//...and $
	$output = str_replace("'", "\'", $output);						// ...and \
	return $output;
}												//...and fix single quotes

function evalidate($email){     									//validates email addresses; returns 1 if good and 0 if bad
	if(!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $email)){
		return 0;
	}else{
		return 1;
	}
}

function pvalidate($password){										//validates passwords. Accepts 4-10 char. alphanumerics
	if(!ereg("^[a-zA-Z0-9]{4,10}$",$password)){
		return 0;
	}else{
		return 1;
	}
}

function tvalidate($password){										//validates tablenames. Accepts 4-30 char. alphanumerics
	if(!ereg("^[a-zA-Z0-9]{4,30}$",$password)){
		return 0;
	}else{
		return 1;
	}
}

// for the de-tagged text stored in questions & answers, wrap bare
// URLs with links for output.

function add_links($input)
{
	$pattern = '/(http\:\/\/\S+)/i';

	$num_matches = preg_match($pattern, $input);

	if($num_matches){
    	$linked = preg_replace($pattern, '<a href="$1">$1</a>', $input);
	}
	else {
		$linked = $input; // no matches, just return the bare output
	}

	return $linked;

}

function redirect(){												//redirect to list page
	$host  = $_SERVER['HTTP_HOST'];
	$uri  = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
	$list = 'list.php';
//	if($tablename){
//		header("Location: http://$host$uri/$list/$tablename");
//	} else {
		header("Location: http://$host$uri/$list");
//	}
	print "<h2>Thank you for submitting a question.</h2><br /><br />";		//just in case the redirect didn't work...
	print '<a href="list.php">Return to the list</a>';}

function no_access(){												//redirects to the "Sorry, you can't see this..." page
	$host  = $_SERVER['HTTP_HOST'];
	$uri  = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
	$no_access = 'no_access.php';
	header("Location: http://$host$uri/$no_access");
	print "<h2>Sorry, you do not have access to this page.</h2><br /><br />";		//just in case the redirect didn't work...
	print '<a href="list.php">Return to the list</a>';}

function getIP() {									//gets the user's ip address
  if (getenv("HTTP_CLIENT_IP")){
   $ip = getenv("HTTP_CLIENT_IP");
  }elseif(getenv("HTTP_X_FORWARDED_FOR")){
    $ip = getenv("HTTP_X_FORWARDED_FOR");
  }elseif(getenv("REMOTE_ADDR")){
    $ip = getenv("REMOTE_ADDR");
  }else{ $ip = "UNKNOWN";
  }
  return $ip;
}

function cookie_check(){
	global $tablename;												//creates a global variable for the table name
	if (!$tablename) $tablename = $_COOKIE['tablename'];			//checks for a cookie
	if (!$tablename){												//if there is none...
		$host  = $_SERVER['HTTP_HOST'];								//redirects to the chooser page (which will give the user one)
		$uri  = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
		$chooser = 'chooser.php';
		header("Location: http://$host$uri/$chooser");}}

function url_check(){
	global $tablename;
// first check the URL
	$url=strip_tags($_SERVER['REQUEST_URI']);
	$url_array=explode("/",$url);
	if(!preg_match("/\.php$/i",$url_array[2])){
		$tablename=$url_array[2]; // 'cause we only want the first dir of the uri 
		setcookie('tablename', $tablename);
	}
// then check the cookie 
        if(!$tablename) $tablename = $_COOKIE['tablename'];                    //checks for a cookie
	if(!$tablename){
                $host  = $_SERVER['HTTP_HOST'];                                                         //redirects to the chooser page (which will give the user one)
//                $uri  = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
                $chooser = 'chooser.php';
                header("Location: http://$host/$url_array[1]/$chooser");
	}	
}

function get_tablename_var()
{
	global $tablename;

	return $tablename;
}

function admin_check(){												//checks user's admin password cookie against that in the  database
	global $tablename, $db;											//creates a global variable for the table name
	$db_pw = mysql_query("SELECT password FROM admins WHERE tablename = '$tablename'", $db);
	if($db_pw){
		$db_pw = mysql_fetch_object($db_pw);
		$db_pw = $db_pw->password;									//fetches the password from the database...
	}

	$admin_pw = $_COOKIE['admin_pw'];								//checks for a cookie

	if ($admin_pw == $db_pw){										//checks for a match...
		$match = 1;
	}

	if ((!$admin_pw)||(!$match)){									//returns 1 if a password has been provided and matches that
		return 0;													//in the database
	}else{
		return 1;
	}
}

function create_doc(){										//this functions renders xml from the database
	global $db, $tablename;
	$doc = new DOMDocument('1.0', 'ISO-8859-1');			//create the document-object for the xml
	$doc->formatOutput = TRUE;								//turn on automatic line-feeds and indents
	$entries = $doc->createElement('entries');				//create the root element
	$entries = $doc->appendChild($entries);					//and add it to the tree

	$globals = $doc->createElement('globals');					//create the root element
	$entries->appendChild($globals);							//and add it to the tree

	$data = mysql_query("SELECT refresh_time, description FROM admins WHERE tablename = '$tablename'", $db);
	if($data){ 											//finds the refresh time that the prof. specified in the database
		$data = mysql_fetch_object($data);
		$refresh_time = intval($data->refresh_time);
		$description = $data->description;
	}


	$admin = $doc->createElement('admin', admin_check());		//creats <admin>1</admin> if the user's logged in; else <admin>0</admin>
	$refresh = $doc->createElement('refresh_time', $refresh_time);	//creates a refresh time element
	$desc = $doc->createElement('description', $description);

	$globals->appendChild($admin);								//adds the aforementioned elements to the doc...
	$globals->appendChild($refresh);
	$globals->appendChild($desc);

	return array($doc, $entries);								//and returns them for more additions
}

function get_list_info(){ 						//gets info from the database for the list page
	global $db, $tablename;
	$question_list = mysql_query("SELECT qid, text, poster, email, tablename, timeorder, lasttouch, state, votes FROM questions WHERE tablename = '$tablename' AND state != 'disabled' ORDER BY votes DESC, timeorder DESC", $db);

	$data = mysql_query("SELECT threshold, new_length, stale_length FROM admins WHERE tablename = '$tablename'", $db);
	if($data){
		$data = mysql_fetch_object($data);
		$threshold = intval($data->threshold);
		$new_length= intval($data->new_length);
		$stale_length= intval($data->stale_length);
		}

	$i = 0;
	if ($question_list){												//if the question object exists,
		while ($question_array = mysql_fetch_assoc($question_list)){	//create an array containing questions
		$question_arrays[]=$question_array;}
		$popular_questions = array();					//now create another array for active questions
		while (($i < $threshold) && (count($question_arrays)>0)){	//while we're under the 'active' threshold...
			$popular_questions[] = array_shift($question_arrays);	//add to the popular questions array
			$i++;}
		if ($question_arrays){						//if there's anything left, order by time submitted
			foreach ($question_arrays as $key => $row){
				$timeorder[$key] = $row['timeorder'];
			}
		}

		if ($i > $threshold){
			array_multisort($timeorder, SORT_DESC, $question_arrays);
		}

		if (($question_arrays)||($popular_questions)){			//now merge the sorted arrays back together
			$question_arrays = array_merge($popular_questions, $question_arrays);
		}
		//get the answer list
		$answer_list = mysql_query("SELECT qid, text, poster, email FROM answers WHERE tablename = '$tablename' ORDER BY aid ASC", $db);

		if ($answer_list){												//if the answer object exists,
			while ($answer_array = mysql_fetch_assoc($answer_list)){	//create an array containing answers
				$answer_arrays[]=$answer_array;
			}
		}
	}
	return array($question_arrays, $answer_arrays, $threshold, $new_length, $stale_length);
}

function get_statistics(){							//gets avg. and std. dev. of votes per question
	global $db, $tablename;

	if($stdev = mysql_query("SELECT STD(votes) FROM questions WHERE tablename = '$tablename'", $db)){
		$stdev = mysql_fetch_array($stdev);
		$stdev = $stdev[0]+.001;					//so we don't divide by zero
	}

	if($average = mysql_query("SELECT AVG(votes) FROM questions WHERE tablename = '$tablename'", $db)){
		$average = mysql_fetch_array($average);
		$average = $average[0];
	}

	return array($stdev, $average);
}


function create_xml($question_arrays, $answer_arrays, $stdev, $average, $doc, $entries, $threshold=0, $new_length=0, $stale_length=99999999){
	$i=0;
	foreach ($question_arrays as $fields) {					//for each question
		$question = $doc->createElement('question');		//create a question element
		$entries->appendChild($question);					//below the root
		if ($i < $threshold){
			$question->setAttribute("column", "popular");	//if the question is in the first half, give that question the "popular" attribute
		}else{												//if not, give the question the "recent" attribute
			$question->setAttribute("column", "recent");
		}
		$i++;

		$host  = 'web.hostname';
		$uri  = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');

		$votelink = "http://$host$uri/" . 'vote.php?qid=' . $fields['qid'];
		$answerlink = "http://$host$uri/" . 'answer.php?qid=' . $fields['qid'];
		$disablelink = "http://$host$uri/" . 'disable.php?qid=' . $fields['qid'];
		$modifylink = "http://$host$uri/" . 'modify.php?qid=' . $fields['qid'];

		$question->setAttribute('votelink', $votelink);			//sets the question attribute votelink
		$question->setAttribute('answerlink', $answerlink);		//sets the question attribute answerlink
		$question->setAttribute('index', $i%2);					//a display index
		if (admin_check()){
			$question->setAttribute('disablelink', $disablelink);
			$question->setAttribute('modifylink', $modifylink);
		}

		foreach($fields as $key => $value){					//for each value in the question
			if ($key == "timeorder"){					//if the key is timeorder
				$item = $doc->createElement($key, $value);		//reformat for display and add to xml
				$question->appendChild($item);
				$item = $doc->createElement('f_time', date("g:i a T, j M", intval($value)));
				$question->appendChild($item);
				if (mktime() - $value < $new_length){
					$item = $doc->createElement('age_marker', 'new');
					$question->appendChild($item);
				}
			}elseif($key == "lasttouch"){					//if the key is lasttouch
				if (mktime() - $value > $stale_length){			//use to determine staleness
					$item = $doc->createElement('age_marker', 'stale');
					$question->appendChild($item);
				}
			}
			/*elseif($key == "text")
			{
				$item = $doc->createElement($key, add_links($value));
				$question->appendChild($item);
			}*/
			else{
			$item = $doc->createElement($key, $value);		//create an appropriately-titled element
			$question->appendChild($item);			//and add it to the tree
			}
		}

		$votes = $fields['votes'];						//append votes child
		$shade = $doc->createElement('shade', 'c' . round(3+(($votes - $average)/$stdev)));
		$question->appendChild($shade);

		if ($answer_arrays){								//for each answer
			foreach ($answer_arrays as $a_fields){
				if ($a_fields['qid'] == $fields['qid']){	//if it's question id matches that of the question currently open
					$answer = $doc->createElement('answer');	//add more elements to describe the answer
					$question->appendChild($answer);
					$item = $doc->createElement('text', $a_fields['text']);
					$answer->appendChild($item);
					$item = $doc->createElement('poster', $a_fields['poster']);
					$answer->appendChild($item);
					$item = $doc->createElement('email', $a_fields['email']);
					$answer->appendChild($item);
				}
			}
		}
	}
	return $doc;
}

function output_xml($doc, $stylesheet){
	//$doc->save('php://output');						//remove the comments here to see the xml unformatted by the stylesheet
	$xp = new XsltProcessor();
	$xsl = new DomDocument;
	$xsl->load($stylesheet);
	$xp->importStylesheet($xsl);							// import the XSL stylesheet into the XSLT process
	$result = $xp->transformToURI($doc, 'php://output');
}

?>

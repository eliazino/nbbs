<?php
function redef($arg, $query, $h, $un){
	$retval;
	if ($arg == "dbcon"){
		$retval = mysqli_select_db($h,$query);
	}else if($arg == "query" or $arg == "q"){
		$retval = mysqli_query($h,$query) or die (mysqli_error($h));
	}else if($arg == "fetch" or $arg == "f"){
		$retval = mysqli_fetch_array($query);
	}else if($arg == "mCount" or $arg == "m"){
		$retval = mysqli_num_rows($query);
	}else if($arg == "mcon"){
		$retval = mysqli_connect($query,$h,$un);
	}else if($arg == "ar"){
		$retval = mysqli_affected_rows($h);
	}
	return $retval;
}
?>
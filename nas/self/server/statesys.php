<?php
require_once('defines.php');
require_once('config.php');
require_once('functions.php');
if($_POST['m']){
	$m = mysqli_real_escape_string($jr,$_POST['m']);
	$d = redef("query","select*from localgovernment where stateID = '$m'",$jr,0);
	$txt = '';
	if(redef("mCount",$d,$jr,0) < 1){
		$txt = $txt."<option>Not available</option>";
	}
	else{
		while ($bull= redef("fetch",$d,$jr,0)){
			$txt = $txt."<option value='".$bull['id']."'>".$bull['label']."</option>";
		}
	}
	$txt = $txt."";
	echo $txt;
}
?>
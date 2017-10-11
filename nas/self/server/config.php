<?php
require_once("defines.php");
error_reporting(E_ERROR | E_PARSE);
//We start sessions

/******************************************************
------------------Required Configuration---------------
Please edit the following variables so the members area
can work correctly.
******************************************************/

//We log to the DataBase
$jr = redef("mcon",'localhost', 'yandgafn', 'u8zHv7FNaSG5');
redef("dbcon","yandgafn_s8",$jr,0) or die("Could not select database");

//Email
/*$info = 'info@kunsana.com';
$help = 'help@kunsana.com';
$marketing = 'marketing@kunsana.com';*/

$base = "http://www.studentaccommod8.com/n/new/";
$baseRef = "<base href='http://www.studentaccommod8.com/n/new/' />";
?>
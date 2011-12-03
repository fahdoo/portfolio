<?php

ob_start();
include ('../helpers/connect.php');

$id = $_REQUEST['id'];
$kid = $_REQUEST['kid'];
$sid = $_REQUEST['sid'];
$uid = $_REQUEST['uid'];
$myuserid = $_REQUEST['myuserid'];
$pid=$_REQUEST['pid'];
$table = "tbl_match";

/*
Manage PID users!
*/

/*$rs = mysql_query("SELECT matchid FROM $table WHERE kid = $kid AND sid = $sid AND uid = $myuserid");
$count=mysql_num_rows($rs);
if($count==0){
	mysql_query("INSERT INTO $table (uid,kid,sid,score) VALUES('$myuserid','$kid','$sid','1') ") or die(mysql_error());
	echo "new";  
}else{
	while($row = mysql_fetch_array($rs)) {
		$matchid= $row['matchid'];
	}
	mysql_query("UPDATE $table SET score = score + 1 WHERE matchid = $matchid") or die(mysql_error()); 
	echo "updated: ".$matchid;
}
echo " userid: ".$myuserid;*/

echo $id." ".$kid." ".$sid." ".$uid." ".$myuserid." ".$pid;


ob_end_flush();
?>
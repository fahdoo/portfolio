<?php

if(!empty($_REQUEST['pid']) && !empty($_REQUEST['callback']) && !empty($_REQUEST['widget']) && $pid == 'soeie')
{
	ob_start();
	include('../helpers/connect.php');
	include_once('../helpers/id.php');
	
	$table = "tbl_match";
	$pid = $_REQUEST['pid'];
	$callback= $_REQUEST['callback'];
	$userid = 0;
	$creator = $pid;
	$link = mysql_real_escape_string(stripslashes($_REQUEST['link']));
	// Define
	$widget= mysql_real_escape_string(stripslashes($_REQUEST['widget']));

	
	//$wid = get_id($widget, 'widget',  'tbl_widgets');
	$sql = "SELECT id FROM tbl_widgets WHERE widget='$widget' AND creator='$pid'";
	$result=mysql_query($sql); 
	$count=mysql_num_rows($result);	
	// If the term does not exist in the table, add it
	if($count==0){
		mysql_query("INSERT INTO tbl_widgets (widget, link, creator) VALUES('$widget', '$link', '$creator') ") or die(mysql_error());  

		$result=mysql_query($sql); 
		$selfid = get_id($widget, 'keyword', 'tbl_keywords');
		
		// find out  the id of the term
		while($row = mysql_fetch_array($result)) {
			$wid = $row['id'];
		}
		// Self Keyword Process
		$sql_self="SELECT matchid FROM $table WHERE wid='$wid' AND kid='$selfid'";
		$result_self=mysql_query($sql_self)or die(mysql_error());
		$count_self=mysql_num_rows($result_self);
		if ($count_self == 0){		
			mysql_query("INSERT INTO $table (kid,wid, uid) VALUES ('$selfid', '$wid', '$userid')") or die(mysql_error()); 
		}
		
		$creatorid = get_id($pid, 'keyword', 'tbl_keywords');
		// Creator Keyword Process
		$sql_creator="SELECT matchid FROM $table WHERE wid='$wid' AND kid='$creatorid'";
		$result_creator=mysql_query($sql_creator)or die(mysql_error());
		$count_creator=mysql_num_rows($result_creator);
		if ($count_creator == 0){		
			mysql_query("INSERT INTO $table (kid,wid, uid) VALUES ('$creatorid', '$wid', '$userid')") or die(mysql_error()); 
		}
	}else{
		// find out  the id of the term
		while($row = mysql_fetch_array($result)) {
			$wid = $row['id'];
		}
	}


		
	
	// KEYWORD PROCESS
	if(!empty($_REQUEST['keyword'])){
		$keyword= mysql_real_escape_string(stripslashes($_REQUEST['keyword']));
		$kid = get_id($keyword, 'keyword' ,'tbl_keywords');
		
		// Keyword
		$sql_joint="SELECT matchid FROM $table WHERE wid='$wid' AND kid='$kid'";
		$result_joint=mysql_query($sql_joint)or die(mysql_error());	
		$count_joint=mysql_num_rows($result_joint);
		if ($count_joint == 0){		
			mysql_query("INSERT INTO $table (kid, wid, uid) VALUES ('$kid', '$wid', '$userid')") or die(mysql_error()); 

		}
	}
	
	/*if(!empty($link))
	{
		mysql_query("UPDATE tbl_widgets SET link = '$link' WHERE widget = '$widget' AND creator = '$creator'") or die(mysql_error());  
	}*/
	echo '{"wid":'.$wid.'}';
	// echo $callback.'({"WidgetID":'.json_encode($wid).'});';
	ob_end_flush();
}else{
	echo $_REQUEST['widget'] + $_REQUEST['keyword']+$_REQUEST['link']+$_REQUEST['pid'];
}

?>

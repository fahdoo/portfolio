
<?php
ob_start();
include ('../helpers/connect.php');
include_once('../helpers/id.php');

$callback=$_GET['callback'];
$pid=$_GET['pid'];
$output=$_GET['output'];
$query=$_GET['query'];

$userid = 0;
if(!empty($query)){
	$queryid = get_id($query, 'keyword', 'tbl_keywords');
	mysql_query("UPDATE tbl_keywords SET num_query = num_query + 1 WHERE id = $queryid") or die(mysql_error()); 
}
if($pid == "1" && $output == "json"){
	$arr = array();
	
	if($userid!=0){
		$i_weight = 2;
		$g_weight = 1;
	}else{
		$i_weight = 1;
		$g_weight = 1;
	}
	$rs_global= mysql_query("SELECT score AS global FROM tbl_widgets, tbl_keywords, tbl_match WHERE (tbl_widgets.id = tbl_match.wid AND tbl_keywords.id = tbl_match.kid) AND keyword LIKE '%$query%' AND uid!=$userid");
	while($row = mysql_fetch_array($rs_global)) {
		$global= $row['global'];
	}
			
	$function = "$g_weight*$global+$i_weight*score";
	$function_echo = "score";
	
	$filter=false;
	if(strncmp("!", $query,1)==0 && strlen($query) != 1){
		$popular = stristr("!popular",$query);
		$new = stristr("!new",$query);
		$active = stristr("!active",$query);
		if($popular!=false){
			$rs= mysql_query("SELECT matchid, widget, keyword, link, $function_echo AS superscore, h, w, tbl_keywords.id AS kid, tbl_widgets.id AS wid, uid FROM tbl_widgets, tbl_keywords, tbl_match
	WHERE (tbl_widgets.id = tbl_match.wid AND tbl_keywords.id = tbl_match.kid)
	GROUP BY widget ORDER BY superscore DESC LIMIT 10");
			$filter = true;
		}elseif($new!=false){
			$rs= mysql_query("SELECT matchid, widget, keyword, link, $function_echo AS superscore, h, w, tbl_keywords.id AS kid, tbl_widgets.id AS wid, uid FROM tbl_widgets, tbl_keywords, tbl_match
	WHERE (tbl_widgets.id = tbl_match.wid AND tbl_keywords.id = tbl_match.kid)
	GROUP BY widget ORDER BY created DESC LIMIT 10");
			$filter = true;
		}elseif($active!=false){
			$rs= mysql_query("SELECT matchid, widget, keyword, link, $function_echo AS superscore, h, w, tbl_keywords.id AS kid, tbl_widgets.id AS wid, uid FROM tbl_widgets, tbl_keywords, tbl_match
	WHERE (tbl_widgets.id = tbl_match.wid AND tbl_keywords.id = tbl_match.kid)
	GROUP BY widget ORDER BY updatetime DESC LIMIT 10");
			$filter = true;
		}
	
	}
	if(!$filter){
		$rs= mysql_query("SELECT matchid, widget, keyword, link, $function AS superscore, h, w, tbl_keywords.id AS kid, tbl_widgets.id AS wid, uid FROM tbl_widgets, tbl_keywords, tbl_match
		WHERE (tbl_widgets.id = tbl_match.wid AND tbl_keywords.id = tbl_match.kid) AND keyword LIKE '%$query%'
		GROUP BY widget ORDER BY superscore DESC LIMIT 10");
	}
	
	
	while($obj = mysql_fetch_object($rs)) {
			$arr[] = $obj;
	}
	
	//echo json_encode($arr);
	echo $callback.'({"Widgets":'.json_encode($arr).'});';
}


 
ob_end_flush();
?>

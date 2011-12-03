<?php
session_start();
if(!session_is_registered(username)){}
?>

<?php
ob_start();
include ('connect.php');

$query=$_GET['query'];


$arr = array();

if($userid!=0){
	$i_weight = 2;
	$g_weight = 1;
}else{
	$i_weight = 1;
	$g_weight = 1;
}
$rs_global= mysql_query("SELECT score AS global FROM tbl_services, tbl_keywords, tbl_match WHERE (tbl_services.id = tbl_match.sid AND tbl_keywords.id = tbl_match.kid) AND keyword LIKE '%$query%' AND uid!=$userid");
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
		$rs= mysql_query("SELECT matchid, service, keyword, link, $function_echo AS superscore, h, w, tbl_keywords.id AS kid, tbl_services.id AS sid, uid FROM tbl_services, tbl_keywords, tbl_match
WHERE (tbl_services.id = tbl_match.sid AND tbl_keywords.id = tbl_match.kid)
GROUP BY service ORDER BY superscore DESC LIMIT 10");
		$filter = true;
	}elseif($new!=false){
		$rs= mysql_query("SELECT matchid, service, keyword, link, $function_echo AS superscore, h, w, tbl_keywords.id AS kid, tbl_services.id AS sid, uid FROM tbl_services, tbl_keywords, tbl_match
WHERE (tbl_services.id = tbl_match.sid AND tbl_keywords.id = tbl_match.kid)
GROUP BY service ORDER BY created DESC LIMIT 10");
		$filter = true;
	}elseif($active!=false){
		$rs= mysql_query("SELECT matchid, service, keyword, link, $function_echo AS superscore, h, w, tbl_keywords.id AS kid, tbl_services.id AS sid, uid FROM tbl_services, tbl_keywords, tbl_match
WHERE (tbl_services.id = tbl_match.sid AND tbl_keywords.id = tbl_match.kid)
GROUP BY service ORDER BY updatetime DESC LIMIT 10");
		$filter = true;
	}

}
if(!$filter){
	$rs= mysql_query("SELECT matchid, service, keyword, link, $function AS superscore, h, w, tbl_keywords.id AS kid, tbl_services.id AS sid, uid FROM tbl_services, tbl_keywords, tbl_match
	WHERE (tbl_services.id = tbl_match.sid AND tbl_keywords.id = tbl_match.kid) AND keyword LIKE '%$query%'
	GROUP BY service ORDER BY superscore DESC LIMIT 10");
}


while($obj = mysql_fetch_object($rs)) {
		$arr[] = $obj;
}


echo '{"Services":'.json_encode($arr).'}';

 
ob_end_flush();
?>

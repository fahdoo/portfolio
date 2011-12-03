<body>
<?php
ob_start();

$link=stripslashes($_REQUEST['link']);
$linkid=stripslashes($_REQUEST['linkid']);

if(!empty($linkid)){
	include ('../helpers/connect.php');

	$rs= mysql_query("SELECT link FROM tbl_widgets WHERE id = $linkid");
	$count=mysql_num_rows($rs);
	if($count!=0){
		while($row = mysql_fetch_assoc($rs)) {
				$link= $row['link']; 
		}

	}else{
		echo $linkid."<div>Could not find widget :(</div>";
	}
	
	
	 
}

echo $link;
ob_end_flush();

?>
</body>


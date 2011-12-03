<?php 
$thought = $data['thought'];
$title = $thought['Content']['websiteTitle'][0];
$favicon = $thought['Content']['favicon'][0];
if(isset($thought['Content']['blurb'][0]))
	$blurb = $thought['Content']['blurb'][0];
else
	$blurb = '';
$url = $thought['Content']['url'][0];
?>
<a href="<?php echo $url;?>" target="_blank"><img class="favicon" src="<?php echo $favicon; ?>" height="16px" width="16px"/></a>&nbsp;<b><?php echo $title;?></b>&nbsp;<?php echo $blurb; ?>
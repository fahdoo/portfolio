<?php 
$title = $content['websiteTitle'][0];
$favicon = $content['favicon'][0];
?>
<img src="<?php echo $favicon; ?>" height="16px" width="16px"/>&nbsp;<span style="font-weight:bold; color: #555;"><?php echo $title; ?></span>
<?php if(isset($content['blurb'][0])):?>
	<span style=""><?php echo strip_tags($content['blurb'][0]);?></span>
<?php endif?>
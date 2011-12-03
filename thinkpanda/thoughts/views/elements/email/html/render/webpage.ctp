<a href="<?php echo $data['Content'][$version]['url'][0]; ?>" websiteTitle="<?php echo $data['Content'][$version]['url'][0]; ?>">
<?php 
	if(empty($data['Content'][$version]['websiteTitle'][0]) || $data['Content'][$version]['websiteTitle'][0] == "Untitled")
		echo $data['Content'][$version]['url'][0];
	else
		echo $data['Content'][$version]['websiteTitle'][0]; 
?>
</a>
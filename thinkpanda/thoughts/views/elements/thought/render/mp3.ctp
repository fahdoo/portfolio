<!--mp3 audio, embed it -->
<EMBED src="<?php echo $url; ?>" autostart=false>

<?php 
echo $this->element('/thought/render/webpage',
	array(
		'container'	=> $container,
		'title'		=> $title,
		'url'		=> $url
	)
);
?>
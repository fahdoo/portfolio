<?php 
echo $this->element('/thought/render/webpage',
	array(
		'container'	=> $container,
		'title'		=> $title,
		'url'		=> $url
	)
);
?>
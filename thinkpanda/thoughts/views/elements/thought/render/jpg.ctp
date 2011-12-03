<?php 
echo $this->element('/thought/render/image',
	array(
		'container'	=> $container,
		'title'		=> $title,
		'url'		=> $url,
		'type'		=> $type
	)
);
?>
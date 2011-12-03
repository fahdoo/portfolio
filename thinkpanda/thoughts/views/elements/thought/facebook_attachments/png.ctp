<?php 
//debug($thought['Content']);
echo $this->element('/thought/facebook_attachments/image',
	array(
		'thought'	=> $thought,
		'project' 	=> $project,
		'plugin' 	=> 'thoughts'
	)
);
?>